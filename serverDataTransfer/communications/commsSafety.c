#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>

#include <yaml.h>

#include "../macros.h"

#include "commsSafety.h"

int generateTimestamp(){
    return (int)time(NULL);
}