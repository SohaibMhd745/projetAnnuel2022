#ifndef SERVERDATATRANSFER_COMMSSAFETY_H
#define SERVERDATATRANSFER_COMMSSAFETY_H

#include "../dataParsing/parseData.h"

int getFilesize(char* path);

long generateTimestamp();

long getLastCommunication(int serverId);

int checkData(loggedData * data, int serverId);

int sendReport(char* yaml, char* target, char* srvNb);

int isNumeric(char * source, int * dest);

long isLongNumeric(char * source, long * dest);

#endif //SERVERDATATRANSFER_COMMSSAFETY_H
