#ifndef SERVERDATATRANSFER_PARSEDATA_H
#define SERVERDATATRANSFER_PARSEDATA_H

#include <mysql.h>

typedef struct database{
    MYSQL* connection;
    MYSQL_RES* res;
    MYSQL_ROW row;

    char query[500];

    char server[50];
    char user[50];
    char password[50];
    char database[50];

}database;

typedef struct loggedOrder{
    unsigned int id;
    long timestamp;
    char dateTime[20];
    int change;
    char article[11];

    struct loggedOrder * next;
    ///linked list
}loggedOrder;

typedef struct loggedData{
    long timestamp;
    long previousStamp;
    int result; ///1 --> success or 0 --> failure, used when logging the communication at the end of execution

    int listLength;

    int serverId;

    loggedOrder * firstLog;
}loggedData;

int outputYaml(loggedData* data, char* output);

long getLastStamp();

int parseCredentials(char* path, database* db);

void freeList(loggedData* data);

void printList(loggedOrder * firstNode);

void reccFreeList(loggedOrder * node);

int generateReport(char* credentials, loggedData* data, char* reportBuffer);

int generateList(database* db, loggedData* data);

int parseYaml(char* yaml, loggedData* outputData);

int readReceived(char* dirPath);

#endif //SERVERDATATRANSFER_PARSEDATA_H