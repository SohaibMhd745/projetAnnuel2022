#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>

#include "../macros.h"

#include "../dataParsing/parseData.h"
#include "../communications/commsSafety.h"

#include "output.h"

/**
Communication History:
 @file: ./history

 @format:

 send:
 timestamp|result(success/failure)|datetime(YYYY/MM/DD h:m:s)|#of transactions sent

 receive:
 timestamp|server n°|datetime(YYYY/MM/DD h:m:s)|#of transactions received

 @important: must write from the start of the file when adding to the history
 **/


void outputData(loggedOrder *firstNode, char * path){
    if (strlen(path) > 5 && !strcmp(path + strlen(path) - 4, ".csv")) {
        FILE * outputFile = fopen(path, "r");
        if (outputFile == NULL){
            outputFile = fopen(path, "ab+");
            if (outputFile == NULL){
                printf("\nError: could not write to output file");
                outputError("Could not write to output file");
                exit(-1);
            } else fprintf(outputFile,"Code Article, Changement, Date Et Heure, Timestamp\n");
        }else{
            fclose(outputFile);
            if (getFilesize(path) <= 0){
                outputFile = fopen(path, "ab+");
                fprintf(outputFile,"Code Article, Changement, Date Et Heure, Timestamp\n");
            }else outputFile = fopen(path, "ab+");
        }



        loggedOrder * currentNode = firstNode;
        while (currentNode!=NULL){
            fprintf(outputFile,"%s, %d, %s, %ld\n", currentNode->article, currentNode->change, currentNode->dateTime, currentNode->timestamp);

            currentNode = currentNode->next;
        }
    }else{
        printf("\nError: Output file is of the wrong format.");
        outputError("Output file is of the wrong format.");
        exit(-1);
    }
}

void logCommunication(loggedData* data, int mode){
    FILE* logFile = fopen("history","ab+");

    if(logFile != NULL) {
        int currentSize = getFilesize("history");

        if (currentSize != READ_FAILURE) {

            char fileBuffer[currentSize+1];
            fileBuffer[currentSize] = '\0';

            if (currentSize > 0) fread(fileBuffer, 1, currentSize, logFile);


            fclose(logFile);

            logFile = fopen("history", "wb+");
            if (logFile != NULL) {
                char buffer[200];
                char timeBuffer[30];
                char resBuffer[20];

                memset(buffer, 0,200);
                {
                    time_t now;
                    struct tm ts;
                    time(&now);
                    ts = *localtime(&now);
                    strftime(timeBuffer, 500, "%Y/%m/%d %H:%M:%S", &ts);
                }
                fputs(buffer, logFile);

                if (data->result == CURL_SUCCESS) strcpy(resBuffer, "Success");
                else strcpy(resBuffer, "Failure");

                if (mode == SEND_MODE) sprintf(buffer, "%ld|%s|%s|%d\n", data->timestamp, resBuffer, timeBuffer, data->listLength);
                else sprintf(buffer, "%ld|%d|%s|%d\n", data->timestamp, data->serverId, timeBuffer, data->listLength);

                fputs(buffer, logFile);
                if(currentSize > 0) fputs(fileBuffer, logFile);
            }
        }
        fclose(logFile);
    }
}

/**
 * @usage outputs log errors into error file
 * @param error -- string to log
 */
void outputError(char* error){
    FILE* errFile = fopen("errors","ab+");

    if(errFile != NULL) {
        int currentSize = getFilesize("errors");

        if (currentSize != READ_FAILURE) {

            char fileBuffer[currentSize+1];
            fileBuffer[currentSize] = '\0';

            if (currentSize > 0) fread(fileBuffer, 1, currentSize, errFile);


            fclose(errFile);

            errFile = fopen("errors", "wb+");
            if (errFile != NULL) {


                char buffer[500];

                {
                    time_t now;
                    struct tm ts;
                    time(&now);
                    ts = *localtime(&now);

                    strftime(buffer, 500, "%Y/%m/%d %H:%M:%S", &ts);
                }

                strcat(buffer, "|");
                strcat(buffer, error);
                strcat(buffer, "\n");

                fputs(buffer, errFile);
                if(currentSize > 0) fputs(fileBuffer, errFile);
            }
        }
        fclose(errFile);
    }
}