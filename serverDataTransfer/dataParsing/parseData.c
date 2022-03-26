#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>

#include <yaml.h>
#include <mysql.h>


#include "parseData.h"
#include "../macros.h"
#include "../output/output.h"


#include "../communications/commsSafety.h"

///yaml format example:
/**
 ---
site: 3
timestamp: 1644589270
count: 4
transactions:
- article: 'char* : code article'
  change: 'int: quantité (neg/pos) de la transaction'
  timestamp: int, timestamp unix en secondes
  dateTime: 'string de date, sql fais la conversion pour nous'
- article: 'AABBCCDDEE'
  change: -2
  timestamp: 1645436734
  dateTime: '2022-02-21 01:45:34'

**/
///TODO: shorten article code to char 5 or 4 instead of 10, which is overkill


///YAML Parsing/Outputting Event Hierarchy:
/**
>YAML_START
    >>YAML_STREAM
        >>>YAML_DOCUMENT
            >>>>>YAML_MAP
 */

/**
 * @usage Generate YAML report and stores it in output
 * @param data -- data
 * @param output -- output string
 * @return READ_OK|YAML_FAILURE|READ_OVERSIZE
 * TODO: Simplify this function's syntax using macros
 */
int outputYaml(loggedData* data, char* output){
    yaml_emitter_t emitter;
    yaml_event_t event;

    char buffer[20];
    ///Start Sequence
    {
        ///Init Emitter
        yaml_emitter_initialize(&emitter);
        unsigned long writtenSize;
        yaml_emitter_set_output_string(&emitter, output, MAX_BUFFER, &writtenSize);

        ///Set Start Stream
        yaml_stream_start_event_initialize(&event, YAML_UTF8_ENCODING);
        if (!yaml_emitter_emit(&emitter, &event)) {
            printYamlError(&emitter, &event);
            outputError("Unexpected error while generating report file");
            return YAML_FAILURE;
        }

        ///Start Document
        yaml_document_start_event_initialize(&event, NULL, NULL, NULL, 0);
        if (!yaml_emitter_emit(&emitter, &event)) {
            printYamlError(&emitter, &event);
            outputError("Unexpected error while generating report file");
            return YAML_FAILURE;
        }

        ///Start Mapping
        yaml_mapping_start_event_initialize(&event, NULL, (yaml_char_t *)YAML_MAP_TAG,
                                            1, YAML_ANY_MAPPING_STYLE);
        if (!yaml_emitter_emit(&emitter, &event)) {
            printYamlError(&emitter, &event);
            outputError("Unexpected error while generating report file");
            return YAML_FAILURE;
        }
    };

    ///Map timestamp
    {

        yaml_scalar_event_initialize(&event, NULL, (yaml_char_t *)YAML_STR_TAG,
                                     (yaml_char_t *)"timestamp", strlen("timestamp"), 1, 0, YAML_PLAIN_SCALAR_STYLE);
        if (!yaml_emitter_emit(&emitter, &event)) {
            printYamlError(&emitter, &event);
            outputError("Unexpected error while generating report file");
            return YAML_FAILURE;
        }

        if (snprintf(buffer, sizeof(buffer), "%d", data->timestamp) >= sizeof(buffer)) {
            outputError("Unexpected error while generating report file: Timestamp larger than authorized");
            return READ_OVERSIZE;
        }

        yaml_scalar_event_initialize(&event, NULL, (yaml_char_t *)YAML_INT_TAG,
                                     (yaml_char_t *)buffer, strlen(buffer), 1, 0, YAML_PLAIN_SCALAR_STYLE);
        if (!yaml_emitter_emit(&emitter, &event)){
            printYamlError(&emitter, &event);
            outputError("Unexpected error while generating report file");
            return YAML_FAILURE;
        }

    };

    ///Map transactions count
    {

        yaml_scalar_event_initialize(&event, NULL, (yaml_char_t *)YAML_STR_TAG,
                                     (yaml_char_t *)"count", strlen("count"), 1, 0, YAML_PLAIN_SCALAR_STYLE);
        if (!yaml_emitter_emit(&emitter, &event)) {
            printYamlError(&emitter, &event);
            outputError("Unexpected error while generating report file");
            return YAML_FAILURE;
        }

        if (snprintf(buffer, sizeof(buffer), "%d", data->listLength) >= sizeof(buffer)) return READ_OVERSIZE;

        yaml_scalar_event_initialize(&event, NULL, (yaml_char_t *)YAML_INT_TAG,
                                     (yaml_char_t *)buffer, strlen(buffer), 1, 0, YAML_PLAIN_SCALAR_STYLE);
        if (!yaml_emitter_emit(&emitter, &event)){
            printYamlError(&emitter, &event);
            outputError("Unexpected error while generating report file: Count larger than authorized");
            return YAML_FAILURE;
        }

    };

    ///Map transactions List
    if (data->listLength>0){
        ///Start Sequence
        yaml_scalar_event_initialize(&event, NULL, (yaml_char_t *)YAML_STR_TAG,
                                     (yaml_char_t *)"transactions", strlen("transactions"), 1, 0, YAML_PLAIN_SCALAR_STYLE);
        if (!yaml_emitter_emit(&emitter, &event)) {
            printYamlError(&emitter, &event);
            outputError("Unexpected error while generating report file");
            return YAML_FAILURE;
        }

        yaml_sequence_start_event_initialize(&event, NULL, (yaml_char_t *)YAML_SEQ_TAG,
                                             1, YAML_ANY_SEQUENCE_STYLE);
        if (!yaml_emitter_emit(&emitter, &event)) {
            printYamlError(&emitter, &event);
            outputError("Unexpected error while generating report file");
            return YAML_FAILURE;
        }

        loggedOrder* node = data->firstLog;

        while(node != NULL){
            ///Start Mapping
            yaml_mapping_start_event_initialize(&event, NULL, (yaml_char_t *)YAML_MAP_TAG,
                                                1, YAML_ANY_MAPPING_STYLE);
            if (!yaml_emitter_emit(&emitter, &event)) {
                printYamlError(&emitter, &event);
                outputError("Unexpected error while generating report file");
                return YAML_FAILURE;
            }

            ///Map article code
            {

                yaml_scalar_event_initialize(&event, NULL, (yaml_char_t *)YAML_STR_TAG,
                                             (yaml_char_t *)"article", strlen("article"), 1, 0, YAML_PLAIN_SCALAR_STYLE);
                if (!yaml_emitter_emit(&emitter, &event)) {
                    printYamlError(&emitter, &event);
                    outputError("Unexpected error while generating report file");
                    return YAML_FAILURE;
                }

                yaml_scalar_event_initialize(&event, NULL, (yaml_char_t *)YAML_STR_TAG,
                                             (yaml_char_t *)node->article, strlen(node->article), 1, 0, YAML_PLAIN_SCALAR_STYLE);
                if (!yaml_emitter_emit(&emitter, &event)) {
                    printYamlError(&emitter, &event);
                    outputError("Unexpected error while generating report file");
                    return YAML_FAILURE;
                }

            };

            ///Map article change
            {

                yaml_scalar_event_initialize(&event, NULL, (yaml_char_t *)YAML_STR_TAG,
                                             (yaml_char_t *)"change", strlen("change"), 1, 0, YAML_PLAIN_SCALAR_STYLE);
                if (!yaml_emitter_emit(&emitter, &event)) {
                    printYamlError(&emitter, &event);
                    outputError("Unexpected error while generating report file");
                    return YAML_FAILURE;
                }

                if (snprintf(buffer, sizeof(buffer), "%d", node->change) >= sizeof(buffer)) return READ_OVERSIZE;

                yaml_scalar_event_initialize(&event, NULL, (yaml_char_t *)YAML_INT_TAG,
                                             (yaml_char_t *)buffer, strlen(buffer), 1, 0, YAML_PLAIN_SCALAR_STYLE);
                if (!yaml_emitter_emit(&emitter, &event)){
                    printYamlError(&emitter, &event);
                    outputError("Unexpected error while generating report file");
                    return YAML_FAILURE;
                }

            };

            ///Map article timestamp
            {

                yaml_scalar_event_initialize(&event, NULL, (yaml_char_t *)YAML_STR_TAG,
                                             (yaml_char_t *)"timestamp", strlen("timestamp"), 1, 0, YAML_PLAIN_SCALAR_STYLE);
                if (!yaml_emitter_emit(&emitter, &event)) {
                    printYamlError(&emitter, &event);
                    outputError("Unexpected error while generating report file");
                    return YAML_FAILURE;
                }

                if (snprintf(buffer, sizeof(buffer), "%d", node->timestamp) >= sizeof(buffer)) return READ_OVERSIZE;

                yaml_scalar_event_initialize(&event, NULL, (yaml_char_t *)YAML_INT_TAG,
                                             (yaml_char_t *)buffer, strlen(buffer), 1, 0, YAML_PLAIN_SCALAR_STYLE);
                if (!yaml_emitter_emit(&emitter, &event)){
                    printYamlError(&emitter, &event);
                    outputError("Unexpected error while generating report file");
                    return YAML_FAILURE;
                }

            };

            ///Map article datetime
            {

                yaml_scalar_event_initialize(&event, NULL, (yaml_char_t *)YAML_STR_TAG,
                                             (yaml_char_t *)"datetime", strlen("datetime"), 1, 0, YAML_PLAIN_SCALAR_STYLE);
                if (!yaml_emitter_emit(&emitter, &event)) {
                    printYamlError(&emitter, &event);
                    outputError("Unexpected error while generating report file");
                    return YAML_FAILURE;
                }

                yaml_scalar_event_initialize(&event, NULL, (yaml_char_t *)YAML_STR_TAG,
                                             (yaml_char_t *)node->dateTime, strlen(node->dateTime), 1, 0, YAML_PLAIN_SCALAR_STYLE);
                if (!yaml_emitter_emit(&emitter, &event)) {
                    printYamlError(&emitter, &event);
                    outputError("Unexpected error while generating report file");
                    return YAML_FAILURE;
                }

            };

            ///End Mapping
            yaml_mapping_end_event_initialize(&event);
            if (!yaml_emitter_emit(&emitter, &event)) {
                printYamlError(&emitter, &event);
                outputError("Unexpected error while generating report file");
                return YAML_FAILURE;
            }

            ///Go to next node
            node = node->next;
        }


        ///End Sequence
        yaml_sequence_end_event_initialize(&event);
        if (!yaml_emitter_emit(&emitter, &event)) {
            printYamlError(&emitter, &event);
            outputError("Unexpected error while generating report file");
            return YAML_FAILURE;
        }
    }

    ///End Sequence
    {
        ///End mapping
        yaml_mapping_end_event_initialize(&event);
        if (!yaml_emitter_emit(&emitter, &event)) {
            printYamlError(&emitter, &event);
            outputError("Unexpected error while generating report file");
            return YAML_FAILURE;
        }

        ///Kill Document
        yaml_document_end_event_initialize(&event, 0);
        if (!yaml_emitter_emit(&emitter, &event)) {
            printYamlError(&emitter, &event);
            outputError("Unexpected error while generating report file");
            return YAML_FAILURE;
        }

        ///Kill Stream
        yaml_stream_end_event_initialize(&event);
        if (!yaml_emitter_emit(&emitter, &event)) {
            printYamlError(&emitter, &event);
            outputError("Unexpected error while generating report file");
            return YAML_FAILURE;
        }


        ///Kill Emitter
        yaml_emitter_delete(&emitter);
    };

    return READ_OK;
}

/**
 * @usage Parses yaml into loggedData chained list
 * @param yaml -- input yaml string
 * @param outputData -- output chained list data
 * @return READ_OK|READ_FAILURE|YAML_FAILURE
 */
int parseYaml(char* yaml, loggedData* outputData){
    yaml_parser_t parser;
    yaml_event_t event;
    char value[50];
    int status = YAML_START;
    loggedOrder * currentNode = NULL;

    outputData->firstLog = NULL;
    outputData->listLength = -1;
    outputData->timestamp = 0;

    ///Start Sequence
    {
        ///Init Parser
        yaml_parser_initialize(&parser);
        yaml_parser_set_input_string(&parser, yaml, strlen(yaml));
    }

    do{
        ///Parse Event
        if(!yaml_parser_parse(&parser, &event)){
            printParserError(&parser, &event);
            freeList(outputData);
            outputError("Unexpected error while reading report file");
            return YAML_FAILURE;
        }

        ///Switch according to last event status and event type
        switch (status) {
            case YAML_START:
                if(event.type== YAML_STREAM_START_EVENT)
                    status = YAML_STREAM;
                else {
                    printParserError(&parser, &event);
                    freeList(outputData);
                    outputError("Unexpected event encountered while reading report file");
                    return YAML_FAILURE;
                }
                break;
            case YAML_STREAM:
                switch (event.type) {
                    case YAML_DOCUMENT_START_EVENT:
                        status = YAML_DOCUMENT;
                        break;
                    case YAML_STREAM_END_EVENT:
                        status = YAML_EOF;
                        break;
                    default:
                        printParserError(&parser, &event);
                        freeList(outputData);
                        outputError("Unexpected event encountered while reading report file");
                        return YAML_FAILURE;
                }
                break;
            
            case YAML_DOCUMENT:
                switch (event.type) {
                    case YAML_MAPPING_START_EVENT:
                        status = YAML_MAP;
                        break;

                    case YAML_DOCUMENT_END_EVENT:
                        status = YAML_STREAM;
                        break;

                    default:
                        printParserError(&parser, &event);
                        freeList(outputData);
                        outputError("Unexpected event encountered while reading report file");
                        return YAML_FAILURE;
                }
                break;

            case YAML_MAP:
                switch (event.type) {
                    case YAML_SCALAR_EVENT:
                        if (strlen((char*)event.data.scalar.value)>50) return READ_OVERSIZE;
                        strcpy(value, (char*)event.data.scalar.value);

                        if (strcmp(value, "timestamp")==0) {
                            if(outputData->timestamp == 0) status = YAML_VALUE_TIMESTAMP;
                            else {
                                outputError("Encountered Duplicate Value 'timestamp' during YAML Parsing");
                                return DUPLICATE_ERROR;
                            }
                        }
                        else if (strcmp(value, "count")==0) {
                            if(outputData->listLength == -1) status = YAML_VALUE_COUNT;
                            else {
                                outputError("Encountered Duplicate Value 'count' during YAML Parsing");
                                return DUPLICATE_ERROR;
                            }
                        }
                        else if (strcmp(value, "transactions")==0) {
                            if (outputData->listLength>0){
                                if (outputData->firstLog == NULL) status = YAML_LIST;
                                else {
                                    outputError("Encountered Duplicate Value 'transactions' during YAML Parsing");
                                    return DUPLICATE_ERROR;
                                }
                            }
                            else status = YAML_EOF;
                        }
                        break;

                    case YAML_DOCUMENT_END_EVENT:
                        status = YAML_STREAM;
                        break;
                    default:
                        printParserError(&parser, &event);
                        freeList(outputData);
                        outputError("Unexpected event encountered while reading report file");
                        return YAML_FAILURE;
                }
                break;

            case YAML_LIST:
                switch (event.type) {
                    case YAML_SEQUENCE_START_EVENT:
                        status = YAML_LIST_OBJECT;
                        break;
                    default:
                        printParserError(&parser, &event);
                        freeList(outputData);
                        outputError("Unexpected event encountered while reading report file");
                        return YAML_FAILURE;
                }
                break;

            case YAML_LIST_OBJECT:
                switch (event.type) {
                    case YAML_MAPPING_START_EVENT:
                        status = YAML_LIST_VALUES;
                        if (currentNode == NULL){
                            outputData->firstLog = malloc(sizeof (loggedOrder));
                            currentNode = outputData->firstLog;
                        }else{
                            currentNode->next = malloc(sizeof (loggedOrder));
                            currentNode = currentNode->next;
                        }
                        currentNode->next = NULL;
                        strcpy(currentNode->dateTime, "");
                        strcpy(currentNode->article, "");
                        currentNode->timestamp = 0;
                        currentNode->change = 0;
                        currentNode->id = 0;
                        break;
                    case YAML_SEQUENCE_END_EVENT:
                        status = YAML_EOF;
                        break;
                    default:
                        printParserError(&parser, &event);
                        freeList(outputData);
                        outputError("Unexpected event encountered while reading report file");
                        return YAML_FAILURE;
                }
                break;

            case YAML_LIST_VALUES:
                switch (event.type) {
                    case YAML_SCALAR_EVENT:
                        if (strlen((char*)event.data.scalar.value)>50) {
                            outputError("Encountered scalar over authorized size during YAML parsing");
                            return READ_OVERSIZE;
                        }
                        strcpy(value, (char*)event.data.scalar.value);

                        if (strcmp(value, "article")==0) {
                            if(strlen(currentNode->article) == 0) status = YAML_LIST_VALUE_ARTICLE;
                            else {
                                outputError("Encountered Duplicate Value 'article' during YAML Parsing");
                                return DUPLICATE_ERROR;
                            }
                        }
                        else if (strcmp(value, "change")==0) {
                            if(currentNode->change == 0) status = YAML_LIST_VALUE_CHANGE;
                            else {
                                outputError("Encountered Duplicate Value 'change' during YAML Parsing");
                                return DUPLICATE_ERROR;
                            }
                        }
                        else if (strcmp(value, "timestamp")==0) {
                            if(currentNode->timestamp == 0) status = YAML_LIST_VALUE_TIMESTAMP;
                            else {
                                outputError("Encountered Duplicate Value 'timestamp' during YAML Parsing");
                                return DUPLICATE_ERROR;
                            }
                        }
                        else if (strcmp(value, "datetime")==0) {
                            if(strlen(currentNode->dateTime) == 0) status = YAML_LIST_VALUE_DATETIME;
                            else {
                                outputError("Encountered Duplicate Value 'datetime' during YAML Parsing");
                                return DUPLICATE_ERROR;
                            }
                        }
                        break;
                    case YAML_MAPPING_END_EVENT:
                        status = YAML_LIST_OBJECT;
                        break;
                    
                    default:
                        printParserError(&parser, &event);
                        freeList(outputData);
                        outputError("Unexpected event encountered while reading report file");
                        return YAML_FAILURE;
                }
                break;

            case YAML_VALUE_TIMESTAMP:
                switch (event.type) {
                    case YAML_SCALAR_EVENT:
                        status = YAML_MAP;

                        if (strlen((char*)event.data.scalar.value)>10) {
                            outputError("Encountered 'timestamp' value over authorized size during YAML parsing");
                            return READ_OVERSIZE;
                        }

                        outputData->timestamp = atoi((char*)event.data.scalar.value);

                        if (outputData->timestamp == 0) {
                            outputError("Missing Value for scalar 'timestamp' encountered during YAML parsing");
                            return READ_FAILURE;
                        }
                        break;
                    default:
                        printParserError(&parser, &event);
                        freeList(outputData);
                        outputError("Unexpected event encountered while reading report file");
                        return YAML_FAILURE;
                }
                break;

            case YAML_VALUE_COUNT:
                switch (event.type) {
                    case YAML_SCALAR_EVENT:
                        status = YAML_MAP;

                        if (strlen((char*)event.data.scalar.value)>10) {
                            outputError("Encountered 'count' value over authorized size during YAML parsing");
                            return READ_OVERSIZE;
                        }

                        outputData->listLength = atoi((char*)event.data.scalar.value);

                        if (outputData->listLength == -1) {
                            outputError("Missing Value for scalar 'count' encountered during YAML parsing");
                            return READ_FAILURE;
                        }
                        break;
                    default:
                        printParserError(&parser, &event);
                        freeList(outputData);
                        outputError("Unexpected event encountered while reading report file");
                        return YAML_FAILURE;
                }
                break;

            case YAML_LIST_VALUE_ARTICLE:
                switch (event.type) {
                    case YAML_SCALAR_EVENT:
                        status = YAML_LIST_VALUES;

                        if (strlen((char*)event.data.scalar.value)>11) {
                            outputError("Encountered 'article' value over authorized size during YAML parsing");
                            return READ_OVERSIZE;
                        }

                        strcpy(currentNode->article, (char*)event.data.scalar.value);

                        if (strcmp(currentNode->article, "") == 0) {
                            outputError("Missing Value for scalar 'article' encountered during YAML parsing");
                            return READ_FAILURE;
                        }
                        break;
                    default:
                        printParserError(&parser, &event);
                        freeList(outputData);
                        outputError("Unexpected event encountered while reading report file");
                        return YAML_FAILURE;
                }
                break;

            case YAML_LIST_VALUE_CHANGE:
                switch (event.type) {
                    case YAML_SCALAR_EVENT:
                        status = YAML_LIST_VALUES;

                        if (strlen((char*)event.data.scalar.value)>10) {
                            outputError("Encountered 'change' value over authorized size during YAML parsing");
                            return READ_OVERSIZE;
                        }

                        currentNode->change = atoi((char*)event.data.scalar.value);

                        if (currentNode->change == 0) {
                            outputError("Missing Value for scalar 'change' encountered during YAML parsing");
                            return READ_FAILURE;
                        }
                        break;
                    default:
                        printParserError(&parser, &event);
                        freeList(outputData);
                        outputError("Unexpected event encountered while reading report file");
                        return YAML_FAILURE;
                }
                break;

            case YAML_LIST_VALUE_TIMESTAMP:
                switch (event.type) {
                    case YAML_SCALAR_EVENT:
                        status = YAML_LIST_VALUES;

                        if (strlen((char*)event.data.scalar.value)>10) {
                            outputError("Encountered 'timestamp' value over authorized size during YAML parsing");
                            return READ_OVERSIZE;
                        }

                        currentNode->timestamp = atoi((char*)event.data.scalar.value);

                        if (currentNode->timestamp == 0) {
                            outputError("Missing Value for scalar 'timestamp' encountered during YAML parsing");
                            return READ_FAILURE;
                        }
                        break;
                    default:
                        printParserError(&parser, &event);
                        freeList(outputData);
                        outputError("Unexpected event encountered while reading report file");
                        return YAML_FAILURE;
                }
                break;

            case YAML_LIST_VALUE_DATETIME:
                switch (event.type) {
                    case YAML_SCALAR_EVENT:
                        status = YAML_LIST_VALUES;

                        if (strlen((char*)event.data.scalar.value)>20) {
                            outputError("Encountered 'datetime' value over authorized size during YAML parsing");
                            return READ_OVERSIZE;
                        }

                        strcpy(currentNode->dateTime, (char*)event.data.scalar.value);

                        if (strcmp(currentNode->dateTime, "") == 0) {
                            outputError("Missing Value for scalar 'datetime' encountered during YAML parsing");
                            return READ_FAILURE;
                        }
                        break;
                    default:
                        printParserError(&parser, &event);
                        freeList(outputData);
                        outputError("Unexpected event encountered while reading report file");
                        return YAML_FAILURE;
                }
                break;

            default:
                break;
        }


        yaml_event_delete(&event);


    } while (status != YAML_EOF);

    ///End Sequence
    {
        ///Kill Parser
        yaml_parser_delete(&parser);
    }

    return READ_OK;
}

/**
 * @usage prints YAML emitting errors
 * @param emitter -- event emitter
 * @param event -- event handler
 */
void printYamlError(yaml_emitter_t* emitter, yaml_event_t* event){
    fprintf(stderr, "Failed to emit event %d: %s\n", event->type, emitter->problem);
    yaml_emitter_delete(emitter);
}

/**
 * @usage prints YAML parsing errors
 * @param parser -- event parser
 * @param event -- event handler
 */
void printParserError(yaml_parser_t* parser, yaml_event_t* event){
    fprintf(stderr, "Failed to parse event %d: %s\n", event->type, parser->problem);
    yaml_parser_delete(parser);
}

/**
Communication History:
 @file: ./history

 @format:

 timestamp|store n° (only on receiver)|result(success/failure)|datetime(YYYY/MM/DD h:m:s)|#of transactions sent|database id of first transaction|database id of last transaction

 @important: must write from the start of the file when adding to the history
 **/


/**
 * @usage deletes the first ellement of the chained list from data structure and uses recFreeList to free the rest
 * @param data -- data strcuture
 */
void freeList(loggedData* data){
    if (data->firstLog != NULL) {
        reccFreeList(data->firstLog);
        data->firstLog = NULL;
    }
}

/**
 * @usage recursively free chained list
 * @param node -- first node from loggedData
 */
void reccFreeList(loggedOrder * node){
    if (node->next != NULL) reccFreeList(node->next);
    node->next = NULL;
    free(node);
}

/**
 * @usage debug function: print chained list contents
 * @param node -- first node from loggedData
 */
void printList(loggedOrder * firstNode){
    loggedOrder * currentNode = firstNode;
    while(currentNode != NULL){
        fprintf(stdout, "\nId: %d| Stamp: %d| Date: %s| Delta: %d| Code: %s\n",
                currentNode->id, currentNode->timestamp, currentNode->dateTime, currentNode->change, currentNode->article);
        currentNode = currentNode->next;
    }
}

/**
 * @usage Generates chained list from database
 * @param db -- database handler structure
 * @param data -- data handler structure, will be filed
 * @return DATABASE_FAILURE or READ_OK
 */
int generateList(database* db, loggedData* data){
    db->connection = mysql_init(NULL);
    ///attempt connection
    if (!mysql_real_connect(db->connection, db->server, db->user, db->password, db->database, 0, NULL, 0)) {
        fprintf(stderr, "\n%s\n", mysql_error(db->connection));
        outputError("Could not establish connection to database");
        return DATABASE_FAILURE;
    }

    ///select transactions history since the previous report
    sprintf(db->query,
            "SELECT transactions.id, UNIX_TIMESTAMP(transactions.stamp), transactions.stamp,transactions.quantity, stock.code"
            " FROM transactions INNER JOIN stock on transactions.item = stock.id"
            " WHERE UNIX_TIMESTAMP(transactions.stamp) > %d ",
            data->previousStamp);

    if (mysql_query(db->connection, db->query)) {
        fprintf(stderr, "%s\n", mysql_error(db->connection));
        exit(1);
    }
    db->res = mysql_use_result(db->connection);

    int i = 0;
    loggedOrder * currentLog;
    while ((db->row = mysql_fetch_row(db->res)) != NULL){
        if(i == 0){
            data->firstLog = malloc(sizeof (loggedOrder));

            data->firstLog->id = atoi(db->row[0]);
            data->firstLog->timestamp = atoi(db->row[1]);
            strcpy(data->firstLog->dateTime, db->row[2]);
            data->firstLog->change = atoi(db->row[3]);
            strcpy(data->firstLog->article, db->row[4]);

            currentLog = data->firstLog;
        }else{
            currentLog->next = malloc(sizeof (loggedOrder));
            currentLog = currentLog->next;

            currentLog->id = atoi(db->row[0]);
            currentLog->timestamp = atoi(db->row[1]);
            strcpy(currentLog->dateTime, db->row[2]);
            currentLog->change = atoi(db->row[3]);
            strcpy(currentLog->article, db->row[4]);
        }
        i++;
    }
    currentLog->next = NULL;
    data->listLength = i;


    ///Free results
    mysql_free_result(db->res);
    mysql_close(db->connection);

    return READ_OK;
}

/**
 * @usage Generate yaml report into reportBuffer
 * @param credentials -- path to credentials file
 * @param data -- data structure where data will be outputed
 * @param reportBuffer -- string that will contain YAML report
 * @return Int depending on several internal conditions and exceptions.
 * Normal run will return READ_OK
 * Exceptions:
 * READ_FAILURE
 * READ_OVERSIZE
 * DATABASE_FAILURE
 * YAML_FAILURE
 */
int generateReport(char* credentials, loggedData* data, char* reportBuffer){
    data->previousStamp = getLastStamp();
    database db;
    int credentialsStatus = parseCredentials(credentials, &db);

    ///Return error status if credential read failed
    switch (credentialsStatus) {
        case READ_FAILURE:
            return READ_FAILURE;
        case READ_OVERSIZE:
            return READ_OVERSIZE;
        default:
            break;
    }

    int dbStatus = generateList(&db, data);
    if (dbStatus == DATABASE_FAILURE) return DATABASE_FAILURE;


    int reportStatus = outputYaml(data, reportBuffer);
    switch (reportStatus) {
        case READ_OVERSIZE:
            freeList(data);
            return READ_OVERSIZE;
        case YAML_FAILURE:
            freeList(data);
            return YAML_FAILURE;
        default: break;
    }

    freeList(data);
    ///Notify that the creation was a success
    return READ_OK;
}

/**
 * @usage Parse credentials file given as send mode parameter
 * @param path -- path of the file
 * @param db -- target db structure to receive credentials
 * @return READ_OK in case process goes correctly, READ_FAILURE in case of parsing failure
 */
int parseCredentials(char* path, database* db){
    FILE* credentialFile = fopen(path, "rb");
    if(credentialFile == NULL) {
        outputError("Could not find 'credentials' file. Please check program readme");
        return READ_FAILURE;
    }

    char line[2000];
    char value[2000];
    char name[2000];

    while(fgets(line,2000, credentialFile)!= NULL){
        strcpy(name, "");
        strcpy(value, "");

        int breakPoint = 0;

        int j = 0;

        while(line[j]!='\0') {
            if (line[j] == ':') breakPoint = j;
            j++;
        }

        if(line[0] != '#' && breakPoint!=0){

            strncpy(name, line, breakPoint);
            *(name+breakPoint) = '\0';

            strcpy(value, line + breakPoint + 1);

            if (strlen(value)>50) {
                outputError("Encountered scalar over authorized size during credentials parsing");
                return READ_OVERSIZE;
            }
            if (value[strlen(value)-1] == '\n') value[strlen(value)-1] = '\0';

            if (strcmp(name, "server")==0) strcpy(db->server, value);
            if (strcmp(name, "user")==0) strcpy(db->user, value);
            if (strcmp(name, "password")==0) strcpy(db->password, value);
            if (strcmp(name, "database")==0) strcpy(db->database, value);
        }
    }

    fclose(credentialFile);

    int cheksum =
            (strcmp(db->server, "")==0?1:0)+
            (strcmp(db->user, "")==0?1:0)+
            (strcmp(db->password, "")==0?1:0)+
            (strcmp(db->database, "")==0?1:0)
    ;
    if (cheksum!=0) {
        outputError("Missing values in credentials file. Please check readme");
        return READ_FAILURE;
    }
    else return READ_OK;

}

/**
 * @return returns last known timestamp from communications history log
 */
unsigned int getLastStamp(){
    FILE* history;
    unsigned int stamp = NO_HISTORY;

    history = fopen("history", "rb");
    if (history == NULL){
        history = fopen("history", "w");
        if (history == NULL){
            printf("\nERROR: program doesn't have writing permissions.");
            exit(-1);
        }
        fclose(history);
        return NO_HISTORY;
    }

    if (getFilesize("history") == 0) return NO_HISTORY;
    else if (getFilesize("history") == READ_FAILURE){
        printf("\nERROR: program doesn't have reading permissions.");
        exit(-1);
    }

    char val[2000];
    char line [10000];

    while(stamp==NO_HISTORY && fgets(line,10000, history)!= NULL){
        strcpy(val, "");

        int breakPoint = -1;

        int j = 0;

        while(line[j+1]!='\0' && breakPoint != j) {
            j++;
            if (line[j] == '|') breakPoint = j;
        }

        if(line[0] != '#' && line[0] != '\n' && breakPoint!=0){

            strncpy(val, line, breakPoint);
            *(val+breakPoint) = '\0';

            stamp = atoi(val);
        }
    }

    fclose(history);
    return stamp;
}