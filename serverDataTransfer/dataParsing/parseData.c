#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>

#include <yaml.h>
#include <mysql.h>

#include "../macros.h"

#include "parseData.h"

#include "../communications/commsSafety.h"

///json format example:
/**
 {
  "site": 3
  "timestamp":"1644589270",

  "transactions":[
    {
      "article":"char* : code article",
      "change":"int: quantité (neg/pos) de la transaction",
      "timestamp":"int, timestamp unix en secondes"
  	},
		{
      "article":"08A85",
      "change":-2,
      "timestamp":1644586270
		}
  ]
}

 **/

///yaml format example:
/**
 ---
site: 3
timestamp: 1644589270
transactions:
- article: 'char* : code article'
  change: 'int: quantité (neg/pos) de la transaction'
  timestamp: int, timestamp unix en secondes
- article: '08A85'
  change: -2
  timestamp: 1644586270

**/

int generateReport(char* reportPath){
    int stamp = generateTimestamp();
    int fileSize = getFilesize(reportPath);

    if (fileSize == READ_FAILURE) return READ_FAILURE;

    FILE *reportFile = fopen(reportPath, "r");
    yaml_parser_t parser;

    if(!yaml_parser_initialize(&parser)){
        fputs("Failed to initialize parser!\n", stderr);
        return READ_FAILURE;
    }
    if(reportFile == NULL) {
        fputs("Failed to open file!\n", stderr);
        return READ_FAILURE;
    }



    return READ_OK;
}