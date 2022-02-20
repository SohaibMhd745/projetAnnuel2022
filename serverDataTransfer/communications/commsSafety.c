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