#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#include <yaml.h>
#include <curl/curl.h>
#include <xlsxwriter.h>
#include <mysql.h>

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
 *
 * TODO: Making the function return "NULL_MODE" instead of breaking the execution and letting main handle the exceptions could be better
 */
int parseArgs(int argc, char **argv){
    FILE* useTest;

    if (argc > 3){//There is technically always 1 argument: the program's name
        printf("Error: Too many arguments.\nUse -h for help");
        exit(-1);
    }
    if (argc < 3 && argv[1][1]!='h'){
        printf("Error: Not enough arguments.\nUse -h for help");
        exit(-1);
    }

    if (argv[1][0] == '-'){//If the argument starts with - (parameter)
        switch (argv[1][1]) {//Switch depending on the parameter
            case 'h'://help
                printf("h\t\t\t\t:\tHelp"
                "\ns [Info]\t\t:\tStart the program in sender mode"
                "\nr [Central Excel Sheet]\t\t:\tStart the program in receiver mode"
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
                return SEND_MODE;
            case 'r'://Receive
                useTest = fopen(argv[2],"rb");
                if (useTest == NULL){
                    fclose(useTest);
                    printf("\nCould not open excel sheet\n");
                    exit(-1);
                }
                fclose(useTest);
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
            fprintf(stdout, "\n\n%s", report);

            ///Debug Block
            /*
            loggedData rdata;
            parseYaml(report, &rdata);
            printList(rdata.firstLog);
            printf("%d", checkData(&rdata, 1));
            printList(rdata.firstLog);
            freeList(&rdata);
            */
            ///Debug Block

            freeList(&data);
            break;

        case RECEIVE_MODE:
            printf("Initializing sockets...");
            break;

        default:
            break;
    }
    return 0;
}