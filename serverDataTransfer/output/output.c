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

 timestamp|store n° (only on receiver)|result(success/failure)|datetime(YYYY/MM/DD h:m:s)|#of transactions sent|database id of first transaction|database id of last transaction

 @important: must write from the start of the file when adding to the history
 **/

int logCommunication(loggedData* data){
    ///TODO: Déterminer le n° à partir de l'ip -- Ranger dans un .txt ?
    ///TODO: Copier output error pour avoir les envois les + récents en premier
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