#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>

#include <yaml.h>

#include "../macros.h"

#include "commsSafety.h"

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

int generateTimestamp(){
    return (int)time(NULL);
}