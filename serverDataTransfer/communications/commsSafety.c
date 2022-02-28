#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>

#include <yaml.h>
#include <mysql.h>

#include "../macros.h"

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
int generateTimestamp(){
    return (int)time(NULL);
}

/**
 * @usage returns last timestamp from specified server to check for duplicate transmissions
 * @param id -- store/server id id
 * @return NO_HISTORY if no prior transmission, last communication timestamp otherwise
 */
int getLastCommunication(int serverId){
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

                printf("%s %s\n", val, val2);

                number = atoi(val2);

                if (number == serverId) stamp = atoi(val);

            }

        }
    }

    fclose(history);
    return stamp;
}