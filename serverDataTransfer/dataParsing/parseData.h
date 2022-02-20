#ifndef SERVERDATATRANSFER_PARSEDATA_H
#define SERVERDATATRANSFER_PARSEDATA_H

#include <mysql.h>

typedef struct database{
    MYSQL* connection;
    MYSQL_RES* res;
    MYSQL_ROW row;

    char query[200];

    char server[50];
    char user[50];
    char password[50];
    char database[50];

}database;

typedef struct loggedOrder{
    char* article;
    int change;
    int id;
    int timestamp;
    //TODO: Unix smallest unit: seconds, milliseconds precision cannot be stored in an int, might need change later on

    struct loggedOrder * next;
    ///linked list
}loggedOrder;

typedef struct loggedData{
    int timestamp;
    int previousStamp;
    int result;

    loggedOrder * firstLog;
}loggedData;

int generateReport(char* credentials, loggedData* data);

int getLastStamp();

int parseCredentials(char* path, database* db);

#endif //SERVERDATATRANSFER_PARSEDATA_H