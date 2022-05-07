#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>


#include <yaml.h>
#include <curl/curl.h>
#include <xlsxwriter.h>
#include <mysql.h>

#include "../macros.h"

#include "../dataParsing/parseData.h"
#include "../communications/commsSafety.h"

#include "output.h"

/**
Communication History:
 @file: ./history

 @format:

 timestamp|store n° (only on receiver)|result(success/failure)|datetime(YYYY/MM/DD h:m:s)|#of transactions sent

 @important: must write from the start of the file when adding to the history
 **/

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

                ///timestamp|store n° (only on receiver)|result(success/failure)|
                /// datetime(YYYY/MM/DD h:m:s)|#of transactions sent
                printf("%ld", data->timestamp);
                if (mode == SEND_MODE) sprintf(buffer, "%ld|%s|%s|%d\n", data->timestamp, resBuffer, timeBuffer, data->listLength);
                else sprintf(buffer, "%ld|%d|%s|%s|%d\n", data->timestamp, data->serverId,resBuffer, timeBuffer, data->listLength);

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