#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>

#include <yaml.h>
#include <mysql.h>

#include "../macros.h"

#include "parseData.h"

#include "../communications/commsSafety.h"

///json format example:
/**
 {
  "site": 3
  "timestamp":"1644589270",

  "transactions":[
    {
      "article":"char* : code article",
      "change":"int: quantité (neg/pos) de la transaction",
      "timestamp":"int, timestamp unix en secondes"
  	},
		{
      "article":"08A85",
      "change":-2,
      "timestamp":1644586270
		}
  ]
}

 **/

///yaml format example:
/**
 ---
site: 3
timestamp: 1644589270
transactions:
- article: 'char* : code article'
  change: 'int: quantité (neg/pos) de la transaction'
  timestamp: int, timestamp unix en secondes
  dateTime: 'string de date, sql fais la conversion pour nous'
- article: 'AABBCCDDEE'
  change: -2
  timestamp: 1645436734
  dateTime: '2022-02-21 01:45:34'

**/
///TODO: shorten article code to char 5 or 4 instead of 10, which is overkill
/**
Communication History:
 @file: ./logs/history

 @format:

 timestamp|result(success/failure)|datetime(YYYY/MM/DD h:m:s)|#of transactions sent|database id of first transaction|database id of last transaction

 @important: must write from the start of the file when adding to the history
 **/


/**
 * @usage deletes the first ellement of the chained list from data structure and uses recFreeList to free the rest
 * @param data -- data strcuture
 */
void freeList(loggedData* data){
     reccFreeList(data->firstLog);
     data->firstLog = NULL;
}

/**
 * @usage recursively free chained list
 * @param node -- first node from loggedData
 */
void reccFreeList(loggedOrder * node){
    if (node->next != NULL) reccFreeList(node->next);
    node->next = NULL;
    free(node);
}

/**
 * @usage debug function: print chained list contents
 * @param node -- first node from loggedData
 */
void printList(loggedOrder * firstNode){
    loggedOrder * currentNode = firstNode;
    while(currentNode != NULL){
        fprintf(stdout, "\nId: %d| Stamp: %d| Date: %s| Delta: %d| Code: %s\n",
                currentNode->id, currentNode->timestamp, currentNode->dateTime, currentNode->change, currentNode->article);
        currentNode = currentNode->next;
    }
}

/**
 * @usage Generates chained list from database
 * @param db -- database handler structure
 * @param data -- data handler structure, will be filed
 * @return DATABASE_FAILURE or READ_OK
 */
int generateList(database* db, loggedData* data){
    db->connection = mysql_init(NULL);
    ///attempt connection
    if (!mysql_real_connect(db->connection, db->server, db->user, db->password, db->database, 0, NULL, 0)) {
        fprintf(stderr, "\n%s\n", mysql_error(db->connection));
        return DATABASE_FAILURE;
    }

    ///select transactions history since the previous report
    sprintf(db->query,
            "SELECT transactions.id, UNIX_TIMESTAMP(transactions.stamp), transactions.stamp,transactions.quantity, stock.code"
            " FROM transactions INNER JOIN stock on transactions.item = stock.id WHERE transactions.stamp > %d", data->previousStamp);
    if (mysql_query(db->connection, db->query)) {
        fprintf(stderr, "%s\n", mysql_error(db->connection));
        exit(1);
    }
    db->res = mysql_use_result(db->connection);

    int i = 0;
    loggedOrder * currentLog;
    while ((db->row = mysql_fetch_row(db->res)) != NULL){
        if(i == 0){
            data->firstLog = malloc(sizeof (loggedOrder));

            data->firstLog->id = atoi(db->row[0]);
            data->firstLog->timestamp = atoi(db->row[1]);
            strcpy(data->firstLog->dateTime, db->row[2]);
            data->firstLog->change = atoi(db->row[3]);
            strcpy(data->firstLog->article, db->row[4]);

            currentLog = data->firstLog;
        }else{
            currentLog->next = malloc(sizeof (loggedOrder));
            currentLog = currentLog->next;

            currentLog->id = atoi(db->row[0]);
            currentLog->timestamp = atoi(db->row[1]);
            strcpy(currentLog->dateTime, db->row[2]);
            currentLog->change = atoi(db->row[3]);
            strcpy(currentLog->article, db->row[4]);
        }
        i++;
    }
    currentLog->next = NULL;
    data->listLength = i;


    ///Free results
    mysql_free_result(db->res);
    mysql_close(db->connection);

    return READ_OK;
}


int generateReport(char* credentials, loggedData* data){
    data->previousStamp = getLastStamp();
    database db;
    int credentialsStatus = parseCredentials(credentials, &db);

    ///Return error status if credential read failed
    switch (credentialsStatus) {
        case READ_FAILURE:
            return READ_FAILURE;
        case READ_OVERSIZE:
            return READ_OVERSIZE;
        default:
            break;
    }

    int dbStatus = generateList(&db, data);
    if (dbStatus == DATABASE_FAILURE) return DATABASE_FAILURE;

    /**
     * Generate YAML
     */

    freeList(data);
    ///Notify that the creation was a success
    return READ_OK;
}

/**
 * @usage Parse credentials file given as send mode parameter
 * @param path -- path of the file
 * @param db -- target db structure to receive credentials
 * @return READ_OK in case process goes correctly, READ_FAILURE in case of parsing failure
 */
int parseCredentials(char* path, database* db){
    FILE* credentialFile = fopen(path, "rb");
    if(credentialFile == NULL) return READ_FAILURE;

    char line[2000];
    char value[2000];
    char name[2000];

    while(fgets(line,2000, credentialFile)!= NULL){
        strcpy(name, "");
        strcpy(value, "");

        int breakPoint = 0;

        int j = 0;

        while(line[j]!='\0') {
            if (line[j] == ':') breakPoint = j;
            j++;
        }

        if(line[0] != '#' && breakPoint!=0){

            strncpy(name, line, breakPoint);
            *(name+breakPoint) = '\0';

            strcpy(value, line + breakPoint + 1);

            if (strlen(value)>50) return READ_OVERSIZE;
            if (value[strlen(value)-1] == '\n') value[strlen(value)-1] = '\0';

            if (strcmp(name, "server")==0) strcpy(db->server, value);
            if (strcmp(name, "user")==0) strcpy(db->user, value);
            if (strcmp(name, "password")==0) strcpy(db->password, value);
            if (strcmp(name, "database")==0) strcpy(db->database, value);
        }
    }

    fclose(credentialFile);

    int cheksum =
            (strcmp(db->server, "")==0?1:0)+
            (strcmp(db->user, "")==0?1:0)+
            (strcmp(db->password, "")==0?1:0)+
            (strcmp(db->database, "")==0?1:0)
    ;
    if (cheksum!=0) return READ_FAILURE;
    else return READ_OK;

}

/**
 * @return returns last known timestamp from communications history log
 */
int getLastStamp(){
    FILE* history;
    int stamp = NO_HISTORY;

    history = fopen("history", "rb");
    if (history == NULL){
        history = fopen("history", "w");
        if (history == NULL){
            printf("\nERROR: program doesn't have writing permissions.");
            exit(-1);
        }
        fclose(history);
        return NO_HISTORY;
    }

    if (getFilesize("history") == 0) return NO_HISTORY;
    else if (getFilesize("history") == READ_FAILURE){
        printf("\nERROR: program doesn't have reading permissions.");
        exit(-1);
    }

    char val[2000];
    char line [10000];

    while(stamp==NO_HISTORY && fgets(line,10000, history)!= NULL){
        strcpy(val, "");

        int breakPoint = 0;

        int j = 0;

        while(line[j]!='\0') {
            if (line[j] == '|') breakPoint = j;
            j++;
        }

        if(line[0] != '#' && breakPoint!=0){

            strncpy(val, line, breakPoint);
            *(val+breakPoint) = '\0';

            stamp = atoi(val);
        }
    }

    fclose(history);
    return stamp;
}