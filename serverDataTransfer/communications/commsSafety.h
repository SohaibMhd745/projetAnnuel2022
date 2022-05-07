#ifndef SERVERDATATRANSFER_COMMSSAFETY_H
#define SERVERDATATRANSFER_COMMSSAFETY_H

#include "../dataParsing/parseData.h"

int getFilesize(char* path);

int generateTimestamp();

int getLastCommunication(int serverId);

int checkData(loggedData * data, int serverId);

int sendReport(char* yaml, char* target, char* srvNb);

#endif //SERVERDATATRANSFER_COMMSSAFETY_H
