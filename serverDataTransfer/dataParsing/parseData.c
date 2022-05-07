#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <dirent.h>

#include <mysql.h>


#include "parseData.h"
#include "../macros.h"
#include "../output/output.h"


#include "../communications/commsSafety.h"

///yaml format example:
/**
timestamp: 1644589270
count: 4
transactions:
- article: 'char* : code article'
  change: 'int: quantité (neg/pos) de la transaction'
  timestamp: long, timestamp unix en secondes
  dateTime: 'string de date, sql fais la conversion pour nous'
- article: 'AABBCCDDEE'
  change: -2
  timestamp: 1645436734
  dateTime: '2022-02-21 01:45:34'

**/
///TODO: shortening article code to char 5 or 4 instead of 10, which is overkill


/**
 * @usage Generate YAML report and stores it in output
 * @param data -- data
 * @param output -- output string
 * @return READ_OK|YAML_FAILURE|READ_OVERSIZE
 */
int outputYaml(loggedData* data, char* output){
    char  buffer[MAX_BUFFER];

    sprintf(buffer, "%s: %ld\n", "timestamp", data->timestamp);
    strcat(output, buffer);

    sprintf(buffer, "%s: %d\n", "count", data->listLength);
    strcat(output, buffer);

    strcat(buffer, "transactions:\n");

    loggedOrder * currentNode = data->firstLog;
    while(currentNode != NULL){
        sprintf(buffer, "- %s: %s\n", "article", currentNode->article);
        strcat(output, buffer);

        sprintf(buffer, "  %s: %d\n", "change", currentNode->change);
        strcat(output, buffer);

        sprintf(buffer, "  %s: %ld\n", "timestamp", currentNode->timestamp);
        strcat(output, buffer);

        sprintf(buffer, "  %s: %s\n", "datetime", currentNode->dateTime);
        strcat(output, buffer);

        currentNode = currentNode->next;
    }

    return READ_OK;
}

/**
 * @usage Parses yaml into loggedData chained list
 * @param path -- input yaml path
 * @param outputData -- output chained list data
 * @return READ_OK|READ_FAILURE|YAML_FAILURE
 */
int parseYaml(char* path, loggedData* outputData){
    if (getFilesize(path) == 0)
        return READ_FAILURE;

    FILE* yaml = fopen(path, "rb");
    if(yaml == NULL) {
        outputError("Program does not have reading permissions on ftp folder");
        return READ_FAILURE;
    }

    char line[2000];
    char value[2000];
    char name[2000];

    int firstNode = 1;
    loggedOrder *currentNode = NULL;
    while(fgets(line,2000, yaml)!= NULL){
        for (int i = 0; i < 2000; ++i) {
            name[i] = '\0';
            value[i] = '\0';
        }

        int breakPoint = 0;

        int j = 0;

        while(line[j]!='\0'&&breakPoint==0) {
            if (line[j] == ':') breakPoint = j;
            j++;
            if (line[j]=='\0')
                breakPoint = -1;
        }

        if (breakPoint == -1)
            return READ_FAILURE;

        //if we're reading the beginning of an object
        if ((line[0] == '-'||line[0] == ' ') && line[1] == ' '){
            if(line[0] == '-'){
                if (firstNode){
                    outputData->firstLog = malloc(sizeof (loggedOrder));
                    currentNode = outputData->firstLog;
                    currentNode->id = 0;
                    firstNode++;
                } else {
                    currentNode->next = malloc(sizeof (loggedOrder));
                    currentNode = currentNode->next;
                    currentNode->id = 0;
                }
            } else if (currentNode == NULL) return READ_FAILURE;

            strncpy(name, line+2, breakPoint-2);
            *(name+breakPoint) = '\0';

            strcpy(value, line + breakPoint + 2);
            if (value[strlen(value)-1] == '\n') value[strlen(value)-1] = '\0';
            if (value[strlen(value)-1] == '\r') value[strlen(value)-1] = '\0';

            if (strlen(value)>50) {
                outputError("Encountered scalar over authorized size during yaml parsing");
                return READ_FAILURE;
            }

            if (strcmp(name, "change")==0) {
                if(isNumeric(value, &currentNode->change) == READ_FAILURE)
                    return READ_FAILURE;
            }
            else if (strcmp(name, "timestamp")==0){
                if (isLongNumeric(value, &currentNode->timestamp) == READ_FAILURE)
                    return READ_FAILURE;
            }
            else if (strcmp(name, "article")==0) strcpy(currentNode->article, value);
            else if (strcmp(name, "datetime")==0) strcpy(currentNode->dateTime, value);
        }
        else{
            strncpy(name, line, breakPoint);
            *(name+breakPoint) = '\0';

            strcpy(value, line + breakPoint + 2);
            if (value[strlen(value)-1] == '\n') value[strlen(value)-1] = '\0';
            if (value[strlen(value)-1] == '\r') value[strlen(value)-1] = '\0';

            if (strlen(value)>50) {
                outputError("Encountered scalar over authorized size during yaml parsing");
                return READ_FAILURE;
            }

            if (strcmp(name, "count")==0) {
                if(isNumeric(value, &outputData->listLength) == READ_FAILURE)
                    return READ_FAILURE;
            }
            else if (strcmp(name, "timestamp")==0){
                if (isLongNumeric(value, &outputData->timestamp) == READ_FAILURE)
                    return READ_FAILURE;
            }
        }
    }
    if (currentNode != NULL) currentNode->next = NULL;
    return READ_OK;
}

/**
Communication History:
 @file: ./history

 @format:

 timestamp|store n° (only on receiver)|result(success/failure)|datetime(YYYY/MM/DD h:m:s)|#of transactions sent|database id of first transaction|database id of last transaction

 @important: must write from the start of the file when adding to the history
 **/


/**
 * @usage deletes the first ellement of the chained list from data structure and uses recFreeList to free the rest
 * @param data -- data strcuture
 */
void freeList(loggedData* data){
    if (data->firstLog != NULL) {
        reccFreeList(data->firstLog);
        data->firstLog = NULL;
    }
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
        fprintf(stdout, "\nId: %d| Stamp: %ld| Date: %s| Delta: %d| Code: %s\n",
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
    /*
    printf("%s\n", db->server);
    printf("%s\n", db->user);
    printf("%s\n", db->password);
    printf("%s\n", db->database);
     */
    //TODO: Debug this
    if (!mysql_real_connect(db->connection, "localhost", "akmadmin", "akmgestion123", "c_testdb", 3306, NULL, 0)) {
    //if (!mysql_real_connect(db->connection, db->server, db->user, db->password, db->database, 3306, NULL, 0)) {
            fprintf(stderr, "\n%s\n", mysql_error(db->connection));
            outputError("Could not establish connection to database");
            outputError(mysql_error(db->connection));
            return DATABASE_FAILURE;
    }

    ///select transactions history since the previous report
    sprintf(db->query, "%s %ld",
            "SELECT transactions.id, UNIX_TIMESTAMP(transactions.stamp), transactions.stamp,transactions.quantity, stock.code"
            " FROM transactions INNER JOIN stock on transactions.item = stock.id"
            " WHERE UNIX_TIMESTAMP(transactions.stamp) >"
            , data->previousStamp
            );

    mysql_query(db->connection, db->query);

    db->res = mysql_store_result(db->connection);

    if (db->res->row_count == 0){
        printf("Nothing to report");
        exit(0);
    }

    int i = 0;
    loggedOrder * currentLog;
    while ((db->row = mysql_fetch_row(db->res)) != NULL){
        if(i == 0){
            data->firstLog = malloc(sizeof (loggedOrder));

            data->firstLog->id = atoi(db->row[0]);
            data->firstLog->timestamp = atol(db->row[1]);
            strcpy(data->firstLog->dateTime, db->row[2]);
            data->firstLog->change = atoi(db->row[3]);
            strcpy(data->firstLog->article, db->row[4]);

            currentLog = data->firstLog;
        }else{
            currentLog->next = malloc(sizeof (loggedOrder));
            currentLog = currentLog->next;

            currentLog->id = atoi(db->row[0]);
            currentLog->timestamp = atol(db->row[1]);
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

/**
 * @usage Generate yaml report into reportBuffer
 * @param credentials -- path to credentials file
 * @param data -- data structure where data will be outputed
 * @param reportBuffer -- string that will contain YAML report
 * @return Int depending on several internal conditions and exceptions.
 * Normal run will return READ_OK
 * Exceptions:
 * READ_FAILURE
 * READ_OVERSIZE
 * DATABASE_FAILURE
 * YAML_FAILURE
 */
int generateReport(char* credentials, loggedData* data, char* reportBuffer){
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

    int reportStatus = outputYaml(data, reportBuffer);
    switch (reportStatus) {
        case READ_OVERSIZE:
            freeList(data);
            return READ_OVERSIZE;
        case YAML_FAILURE:
            freeList(data);
            return YAML_FAILURE;
        default: break;
    }

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
    if(credentialFile == NULL) {
        outputError("Could not find 'credentials' file. Please check program readme");
        return READ_FAILURE;
    }

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

            if (strlen(value)>50) {
                outputError("Encountered scalar over authorized size during credentials parsing");
                return READ_OVERSIZE;
            }
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
    if (cheksum!=0) {
        outputError("Missing values in credentials file. Please check readme");
        return READ_FAILURE;
    }
    else return READ_OK;

}

/**
 * @return returns last known timestamp from communications history log
 */
long getLastStamp(){
    FILE* history;
    long stamp = NO_HISTORY;

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

        int breakPoint = -1;

        int j = 0;

        while(line[j+1]!='\0' && breakPoint != j) {
            j++;
            if (line[j] == '|') breakPoint = j;
        }

        if(line[0] != '#' && line[0] != '\n' && breakPoint!=0){

            strncpy(val, line, breakPoint);
            *(val+breakPoint) = '\0';

            stamp = atol(val);
        }
    }

    fclose(history);
    return stamp;
}

int readReceived(char* dirPath) {
    DIR *d = opendir(dirPath);
    struct dirent *dir;
    char *fname;
    short ignore;
    loggedData dataMainNode;
    char *fileBuffer;
    FILE *report;
    char *fullName;

    if (d) {
        while ((dir = readdir(d)) != NULL) {
            //Only .yaml files that are shorter than 25 characters
            if (strlen(dir->d_name) > 5 && !strcmp(dir->d_name + strlen(dir->d_name) - 5, ".yaml") && strlen(dir->d_name) < 25) {
                //get filename without extension
                fname = malloc(sizeof(char) * 20);
                strcpy(fname, dir->d_name);
                fname[strlen(dir->d_name) - 5] = '\0';

                //Check if filename is numeric
                if (isNumeric(fname, &dataMainNode.serverId) == READ_OK) {
                    fullName = malloc(sizeof(char) * (strlen(dirPath) + dir->d_namlen + 10));
                    strcpy(fullName, dirPath);
                    strcat(fullName, "/");
                    strcat(fullName, dir->d_name);

                    unsigned long fSize = getFilesize(fullName);
                    if (fSize > 1000000) outputError("Report file is too large to be read.");
                    else if (fSize == 0) outputError("Report file is empty.");
                    else {
                        report = fopen(fullName, "rb");
                        if (report == NULL) {
                            printf("\nCould not open report file for server Id: %d", dataMainNode.serverId);
                            outputError("Could not open reporting file");
                        } else {
                            int status = parseYaml(fullName, &dataMainNode);
                            if (status != READ_FAILURE){
                                printf("\nFile -  %s parsed contents:\n", fullName);
                                printList(dataMainNode.firstLog);

                                checkData(&dataMainNode, dataMainNode.serverId);

                                //TODO: Output to xlsx

                                freeList(&dataMainNode);
                            }else{
                                printf("Skipping file %s\n", fullName);
                                outputError("Parsed file has an incorrect syntax.");
                            }
                            fclose(report);
                        }
                    }
                    free(fullName);
                }
                free(fname);
            }
        }
        closedir(d);
        return READ_OK;
    }
}