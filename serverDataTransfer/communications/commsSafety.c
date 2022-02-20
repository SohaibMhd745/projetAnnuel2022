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
 * @usage Parse credentials file given as send mode parameter
 * @param path -- path of the file
 * @param db -- target db structure to receive credentials
 * @return READ_OK in case process goes correctly, READ_FAILURE in case of parsing failure
 */
int parseCredentials(char* path, database* db){
    FILE* credentialFile = fopen(path, "rb");
    if(credentialFile == NULL){
        return READ_FAILURE;
    }

    char *line = malloc(2000*sizeof (char));
    int i = 0;
    char* value;
    char* name;

    while(fgets(line,2000, credentialFile)!= NULL){
        name = malloc(2000*sizeof (char));
        value = malloc(2000*sizeof (char));
        fprintf(stdout,"\nLigne: %d\n%s", i, line);

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

            fprintf(stdout, "\n%s|%s\n", name, value);
            if (strcmp(name, "server")==0) strcpy(db->server, value);
            if (strcmp(name, "user")==0) strcpy(db->user, value);
            if (strcmp(name, "password")==0) strcpy(db->password, value);
            if (strcmp(name, "database")==0) strcpy(db->database, value);

        }
        i++;
        free(name);
        free(value);
        free(line);
        line = malloc(2000*sizeof (char));
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