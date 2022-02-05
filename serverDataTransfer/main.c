///Default Libs
#include <stdio.h>
#include <stdlib.h>
#include <string.h>

///Outside Libs
#include <yaml.h>
#include <curl/curl.h>
#include <xlsxwriter.h>

///General use definitions
#include "macros.h"

/**
 * @usage Returns the selected mode, checks the existence of the file and breaks the execution
 * in case of an anomaly
 * @param argc command line args amount
 * @param argv command line args
 * @return Execution mode: SEND_MODE or RECEIVE_MODE, breaks the program if needed
 *
 * @TODO: Potentially make it return "NULL_MODE" instead of breaking the execution and letting main handle the exceptions could be interesting
 */
int parseArgs(int argc, char **argv){
    if (argc > 3){//There is technically always 1 argument: the program's name
        printf("Error: Too many arguments.\nUse -h for help");
        exit(-1);
    }
    if (argc < 3){
        printf("Error: Not enough arguments.\nUse -h for help");
        exit(-1);
    }

    if (argv[1][0] == '-'){//If the argument starts with - (parameter)
        switch (argv[1][1]) {//Switch depending on the parameter
            case 'h'://help
                printf("h\t\t:\tHelp"
                "\ns [\t:\tStart the program in sender mode"
                "\nr\t:\tStart the program in receiver mode"
                "\n");
                exit(0);
                break;
            case 's':
                return SEND_MODE;
            case 'r':
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


/**
 *
 * Parse command line parameter args and start the program in one of two modes depending
 *
 */

int main(int argc, char **argv) {
    int mode = parseArgs(argc, argv);

    switch (mode) {
        case SEND_MODE:
            printf("Starting report...");
            break;

        case RECEIVE_MODE:
            printf("Initializing sockets...");
            break;

        default:
            break;
    }
    return 0;
}