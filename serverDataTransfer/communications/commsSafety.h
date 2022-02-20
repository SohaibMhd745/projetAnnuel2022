#ifndef SERVERDATATRANSFER_COMMSSAFETY_H
#define SERVERDATATRANSFER_COMMSSAFETY_H

#include <mysql.h>

typedef struct database{
    MYSQL* connection;
    MYSQL_RES* res;
    MYSQL_ROW row;

    char* query;

    char* server;
    char* user;
    char* password;
    char* database;

}database;

int getFilesize(char* path);

int generateTimestamp();

int parseCredentials(char* path, database* db);

#endif //SERVERDATATRANSFER_COMMSSAFETY_H
