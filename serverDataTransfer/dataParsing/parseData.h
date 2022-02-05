#ifndef SERVERDATATRANSFER_PARSEDATA_H
#define SERVERDATATRANSFER_PARSEDATA_H

typedef struct loggedOrder{
    char* article;
    int change;
    int timestamp;
    //TODO: Unix smallest unit: seconds, milliseconds precision cannot be stored in an int, might need change later on

    struct loggedOrder * next;
    ///linked list
}loggedOrder;

#endif //SERVERDATATRANSFER_PARSEDATA_H
