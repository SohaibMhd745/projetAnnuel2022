#ifndef SERVERDATATRANSFER_OUTPUT_H
#define SERVERDATATRANSFER_OUTPUT_H

void outputError(char* error);

void logCommunication(loggedData* data, int mode);

void outputData(loggedOrder *firstNode, char * path);

#endif //SERVERDATATRANSFER_OUTPUT_H
