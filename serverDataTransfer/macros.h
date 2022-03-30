#ifndef SERVERDATATRANSFER_MACROS_H
#define SERVERDATATRANSFER_MACROS_H

#define READ_OK 1
#define READ_FAILURE -1
#define READ_OVERSIZE -2
#define DATABASE_FAILURE -3
#define YAML_FAILURE -4
#define DUPLICATE_ERROR -5
#define MISSING_CRITICAL_DATA -6
#define DATA_CRITICAL_ERROR -7
#define DATA_ERROR -8

#define CURL_FAILURE -100
#define CURL_SUCCESS 100

#define YAML_EOF 0

#define YAML_START 1
#define YAML_STREAM 2
#define YAML_DOCUMENT 3
#define YAML_MAP 4

#define YAML_VALUE_TIMESTAMP 10
#define YAML_VALUE_COUNT 11

#define YAML_LIST 20
#define YAML_LIST_OBJECT 21
#define YAML_LIST_VALUES 22

#define YAML_LIST_VALUE_ARTICLE 30
#define YAML_LIST_VALUE_CHANGE 31
#define YAML_LIST_VALUE_TIMESTAMP 32
#define YAML_LIST_VALUE_DATETIME 33

#define MAX_BUFFER 65000
#define MAX_EVENT 1000
//TODO: Calculate buffer size/try to estimate it from the number of transactions

#define NO_HISTORY 0

#define SEND_MODE 1
#define RECEIVE_MODE 2

#endif //SERVERDATATRANSFER_MACROS_H