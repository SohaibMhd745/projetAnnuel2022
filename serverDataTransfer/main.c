#include <stdio.h>
#include <stdlib.h>
#include <dirent.h>

#include "macros.h"

#include "dataParsing/parseData.h"
#include "output/output.h"
#include "communications/commsSafety.h"

/**
 * @usage Returns the selected mode, checks the existence of the file and breaks the execution
 * in case of an anomaly
 * @param argc command line args amount
 * @param argv command line args
 * @return Execution mode: SEND_MODE or RECEIVE_MODE, breaks the program if needed
 */
int parseArgs(int argc, char **argv){
    FILE* useTest;

    //Program name is always the first argument
    if (argc < 4 && argv[1][1]!='h'){
        printf("Error: missing argument.\nUse -h for help");
        exit(-1);
    }

    if (argv[1][0] == '-'){//If the argument starts with - (parameter)
        switch (argv[1][1]) {//Switch depending on the parameter
            case 'h'://help
                printf("h\t\t\t\t\t\t\t\t\t:\tHelp"
                "\ns [DB Credentials Filepath] [target url] [Server Number]\t\t:\tStart the program in sender mode"
                "\nr [Output .csv file] [ftp directory root]\t\t\t\t:\tStart the program in receiver mode"
                "\n");
                exit(0);
            case 's'://Send
                useTest = fopen(argv[2],"rb");
                if (useTest == NULL){
                    fclose(useTest);
                    printf("\nCould not open reporting file\n");
                    exit(-1);
                }
                fclose(useTest);

                if (argc < 5) {
                    printf("Error: missing argument.\nUse -h for help");
                    exit(-1);
                }

                return SEND_MODE;
            case 'r'://Receive
                useTest = fopen(argv[2],"rb");
                if (useTest == NULL){
                    fclose(useTest);
                    printf("\nCould not open csv file\n");
                    outputError("Could not open csv file");
                    exit(-1);
                }
                fclose(useTest);

                DIR* dir = opendir(argv[3]);
                if (dir) closedir(dir);
                else {
                    printf("\nCould not open ftp directory");
                    outputError("Could not open ftp directory");
                    exit(-1);
                }

                return RECEIVE_MODE;
            default://Ignore incorrect parameters
                printf("Unknown parameter : %c\nUse -h for help", argv[1][1]);
                exit(-1);
        }
    }else{
        printf("Arguments formatting error\nUse -h for help");
        exit(-1);
    }
}


int main(int argc, char **argv) {
    loggedData data;
    data.timestamp = generateTimestamp();

    int mode = parseArgs(argc, argv);

    switch (mode) {
        case SEND_MODE:
            printf("Starting report...\n");
            char report[MAX_BUFFER];
            int repStatus = generateReport(argv[2], &data, report);
            switch (repStatus) {
                case READ_OVERSIZE:
                    printf("\nOne of the parameter values exceeds their expected length.");
                    exit(-1);
                case READ_FAILURE:
                    printf("\nCould not verify the integrity of all parameter values.");
                    exit(-1);
                case DATABASE_FAILURE:
                    printf("\nCould not establish connection to database.");
                    exit(-1);
                case YAML_FAILURE:
                    printf("\nError while generating file.");
                    exit(-1);
                case READ_OK:
                    printf("\nSuccessfully generated YAML report");
                    break;
                default:
                    break;
            }
            fprintf(stdout, "\n\n%s\n", report);

            fprintf(stdout, "Sending to: %s\n", argv[3]);

            data.result = sendReport(report, argv[3], argv[5]);

            logCommunication(&data, SEND_MODE);

            freeList(&data);
            break;

        case RECEIVE_MODE:
            readReceived(argv[3], argv[2]);
            printf("\n\nFinished parsing received content.\n\n\n");
            break;

        default:
            break;
    }
    return 0;
}