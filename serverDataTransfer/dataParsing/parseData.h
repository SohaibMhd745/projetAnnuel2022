#ifndef SERVERDATATRANSFER_PARSEDATA_H
#define SERVERDATATRANSFER_PARSEDATA_H

#include <mysql.h>
#include <yaml.h>

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
    unsigned int timestamp;//TODO: Unix smallest unit: seconds, milliseconds precision cannot be stored in an int, might need change later on
    char dateTime[20];
    int change;
    char article[11];

    struct loggedOrder * next;
    ///linked list
}loggedOrder;

typedef struct loggedData{
    unsigned int timestamp;
    unsigned int previousStamp;
    int result; ///1 --> success or 0 --> failure, used when logging the communication at the end of execution

    int listLength;

    int serverId;

    loggedOrder * firstLog;
}loggedData;

void printYamlError(yaml_emitter_t* emitter, yaml_event_t* event);

int outputYaml(loggedData* data, char* output);

unsigned int getLastStamp();

int parseCredentials(char* path, database* db);

void freeList(loggedData* data);

void printList(loggedOrder * firstNode);

void reccFreeList(loggedOrder * node);

int generateReport(char* credentials, loggedData* data, char* reportBuffer);

int generateList(database* db, loggedData* data);

int parseYaml(char* yaml, loggedData* outputData);

void printParserError(yaml_parser_t* parser, yaml_event_t* event);

#endif //SERVERDATATRANSFER_PARSEDATA_H