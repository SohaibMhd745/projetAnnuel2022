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
    int id;
    int timestamp;//TODO: Unix smallest unit: seconds, milliseconds precision cannot be stored in an int, might need change later on
    char dateTime[20];
    int change;
    char article[11];

    struct loggedOrder * next;
    ///linked list
}loggedOrder;

typedef struct loggedData{
    int timestamp;
    int previousStamp;
    int result; ///1 --> success or 0 --> failure, used when logging the communication at the end of execution

    int listLength;

    loggedOrder * firstLog;
}loggedData;

int generateReport(char* credentials, loggedData* data);

int getLastStamp();

int parseCredentials(char* path, database* db);

void freeList(loggedData* data);

void printList(loggedOrder * firstNode);

void reccFreeList(loggedOrder * node);

#endif //SERVERDATATRANSFER_PARSEDATA_H