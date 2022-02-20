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

int generateReport(char* credentials){
    database db;

    if(parseCredentials(credentials, &db) == READ_FAILURE) return READ_FAILURE;

    db.connection = mysql_init(NULL);

    if (!mysql_real_connect(db.connection, db.server, db.user, db.password, db.database, 0, NULL, 0)) {
        fprintf(stderr, "%s\n", mysql_error(db.connection));
        return READ_FAILURE;
    }

    printf("\nDatabase Connection successful\n");

    mysql_free_result(db.res);
    mysql_close(db.connection);

    return READ_OK;
}