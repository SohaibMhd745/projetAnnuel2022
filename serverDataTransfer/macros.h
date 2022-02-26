#ifndef SERVERDATATRANSFER_MACROS_H
#define SERVERDATATRANSFER_MACROS_H

#define READ_OK 1
#define READ_FAILURE -1
#define READ_OVERSIZE -2
#define DATABASE_FAILURE -3
#define YAML_FAILURE -4

#define MAX_BUFFER 65000
#define MAX_EVENT 1000
//TODO: Calculate buffer size/try to estimate it from the number of transactions

#define NO_HISTORY 0

#define SEND_MODE 1
#define RECEIVE_MODE 2

#endif //SERVERDATATRANSFER_MACROS_H