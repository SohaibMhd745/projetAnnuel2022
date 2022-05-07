#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>

#include <yaml.h>
#include <mysql.h>
#include <curl/curl.h>

#include "../dataParsing/parseData.h"
#include "../macros.h"
#include "../output/output.h"
#include "commsSafety.h"


/**
 * @usage takes a filepath and returns the size of that file
 * @param path -- Path of the file
 * @return int: size -- size of the file
 */
int getFilesize(char* path){
    FILE *file = fopen(path, "rb");
    if(file == NULL){
        fclose(file);
        return READ_FAILURE;
    }
    fseek(file, 0, SEEK_END);
    int size = ftell(file);
    fclose(file);

    return size;
}

/**
 * @usage generates timestamp
 * @return int: timestamp
 */
long generateTimestamp(){
    return (long)time(NULL);
}

/**
 * @usage returns last timestamp from specified server to check for duplicate transmissions
 * @param id -- store/server id id
 * @return NO_HISTORY if no prior transmission, last communication timestamp otherwise
 */
long getLastCommunication(int serverId){
    FILE* history;
    int stamp = NO_HISTORY;
    int number = -1;

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
    char val2[2000];
    char line [10000];

    while(stamp==NO_HISTORY && fgets(line,10000, history)!= NULL){
        strcpy(val, "");
        memset(val2, 0, 2000);
        number = -1;
        int breakPoint = -1;
        int breakPoint2 = -1;

        int j = 0;

        while(line[j+1]!='\0' && breakPoint != j) {
            j++;
            if (line[j] == '|') breakPoint = j;
        }

        if(line[0] != '#' && line[0] != '\n' && breakPoint!=0){

            strncpy(val, line, breakPoint);
            *(val+breakPoint) = '\0';

            while(line[j+1]!='\0' && breakPoint2 != j) {
                j++;
                if (line[j] == '|') breakPoint2 = j;
            }

            if(breakPoint != breakPoint2 && breakPoint2 != -1){
                strncpy(val2, line+breakPoint+1, breakPoint2-breakPoint-1);
                *(val2+breakPoint2-breakPoint) = '\0';

                number = atoi(val2);

                if (number == serverId) stamp = atoi(val);

            }

        }
    }

    fclose(history);
    return stamp;
}

/**
 * @usage checks parsed data structure from received YAML report, prunes incomplete information or stops process if the missing data is serious
 * @param data -- parsed data structure to verify
 * @param serverId -- id of the server
 * @return
 */
int checkData(loggedData * data, int serverId){
    int status = READ_OK;

    if (data->listLength == -1) {
        outputError("Missing critical 'count' value from received reporting file");
        return MISSING_CRITICAL_DATA;
    }
    if (data->timestamp == 0){
        outputError("Missing critical 'timestamp' value from received reporting file");
        return MISSING_CRITICAL_DATA;
    }

    data->previousStamp = getLastCommunication(serverId);

    if (data->previousStamp > data->timestamp){
        outputError("Critical 'timestamp' value from received reporting file is incorrect");
        return DATA_CRITICAL_ERROR;
    }

    if (data->firstLog != NULL){
        loggedOrder * node = data->firstLog;
        loggedOrder * previousNode = NULL;
        int error = 0;
        do{
            error = 0;
            if(node->timestamp < data->previousStamp||node->timestamp > data->timestamp||node->timestamp == 0){
                outputError("Value 'timestamp' incorrect. Data node Pruned.");
                error = DATA_ERROR;
                status = DATA_ERROR;
            }else if (strcmp(node->article, "")==0|| strlen(node->article)<10){
                outputError("Value 'article' incorrect. Data node Pruned.");
                error = DATA_ERROR;
                status = DATA_ERROR;
            }else if (strcmp(node->dateTime, "")==0|| strlen(node->dateTime)<19){
                outputError("Value 'datetime' incorrect. Data node Pruned.");
                error = DATA_ERROR;
                status = DATA_ERROR;
            }else if(node->change == 0){
                outputError("Value 'change' incorrect. Data node Pruned.");
                error = DATA_ERROR;
                status = DATA_ERROR;
            }

            if (error == DATA_ERROR){
                if (previousNode == NULL){
                    data->firstLog = node->next;
                    free(node);
                    node = data->firstLog;
                }else{
                    previousNode->next = node->next;
                    free(node);
                    node = previousNode->next;
                }
            }else {
                previousNode = node;
                node = node->next;
            }

        }while (node != NULL);
    }

    return status;
}

static size_t read_callback(char *ptr, size_t size, size_t nmemb, void *stream){
    size_t retcode = fread(ptr, size, nmemb, stream);
    return retcode;
}

/**
 * @usage send report to target
 * @param yaml -- formatted yaml string
 * @param target -- target URL
 * @return CURL_SUCCESS | CURL_FAILURE
 */
int sendReport(char* yaml, char* target, char* srvNb){
    CURL *curlHandler;
    CURLcode res;
    int reportSize;
    FILE* reportFile;
    struct curl_slist *headerList = NULL;
    char* renameTo = malloc(50*sizeof (char));

    reportFile = fopen("buffer.yaml", "wb+");
    if (reportFile == NULL) {
        outputError("Program does not have writing perms");
        exit(-1);
    }
    fprintf(reportFile, "%s", yaml);

    reportSize = getFilesize("buffer.yaml");
    curl_global_init(CURL_GLOBAL_ALL);

    int attempt=0;
    curlHandler = curl_easy_init();

    if(curlHandler) {
        /* build a list of commands to pass to libcurl */
        sprintf(renameTo, "RNTO %s.yaml", srvNb);

        headerList = curl_slist_append(headerList, "RNFR buffer.yaml");
        headerList = curl_slist_append(headerList, renameTo);

        /* we want to use our own read function */
        curl_easy_setopt(curlHandler, CURLOPT_READFUNCTION, read_callback);

        /* enable uploading */
        curl_easy_setopt(curlHandler, CURLOPT_UPLOAD, 1L);

        /* specify target */
        curl_easy_setopt(curlHandler, CURLOPT_URL, target);

        /* pass in that last of FTP commands to run after the transfer */
        curl_easy_setopt(curlHandler, CURLOPT_POSTQUOTE, headerList);

        /* now specify which file to upload */
        curl_easy_setopt(curlHandler, CURLOPT_READDATA, reportFile);

        /* Set the size of the file to upload */
        curl_easy_setopt(curlHandler, CURLOPT_INFILESIZE_LARGE,
                         (curl_off_t)reportSize);
        do{
            /* Now run off and do what you have been told! */
            res = curl_easy_perform(curlHandler);
            /* Check for errors */
            if(res != CURLE_OK){
                outputError(curl_easy_strerror(res));
                attempt++;
            }
        } while (attempt>0&&attempt<3);

        /* clean up the FTP commands list */
        curl_slist_free_all(headerList);
        /* always cleanup */
        curl_easy_cleanup(curlHandler);
    }
    fclose(reportFile);
    curl_global_cleanup();
    free(renameTo);

    if(attempt==3||!curlHandler) return CURL_FAILURE;
    else return CURL_SUCCESS;
}

/**
 * @usage Copies source to dest if source is numeric.
 * @param source
 * @param dest
 * @return READ_FAILURE if non numeric | READ_OK if numeric
 */
int isNumeric(char * source, int * dest){
    for (int i = 0; i < strlen(source); ++i) {
        if(source[i] < '0' || source[i] > '9')
             return READ_FAILURE;
    }
    *dest = atoi(source);
    return READ_OK;
}

/**
 * @usage Copies source to dest if source is numeric.
 * @param source
 * @param dest
 * @return READ_FAILURE if non numeric | READ_OK if numeric
 */
long isLongNumeric(char * source, long * dest){
    for (int i = 0; i < strlen(source); ++i) {
        if(source[i] < '0' || source[i] > '9'){
            printf("%d", source[i]);
            return READ_FAILURE;
        }
    }
    *dest = atol(source);
    return READ_OK;
}