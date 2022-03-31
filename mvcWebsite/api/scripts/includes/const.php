<?php

define('NO_EXCEPTION','1');

//General Exceptions
define('FATAL_EXCEPTION', '0');
define('MYSQL_EXCEPTION', '-1');
define('INVALID_PARAMETER', '-2');
define('MISSING_PARAMETER', '-3');
define('PARAMETER_WRONG_LENGTH', '-4');


//User Related Exceptions
define('USER_NOT_FOUND', '-10');
define('INCORRECT_USER_CREDENTIALS', '-11');
define('EMAIL_USED', '-12');

//Partner Related Exceptions
define('COMPANY_NOT_FOUND','-15');

//Handle set field values
define("DEFAULT_DATE_MIN", "1900-01-01");
define("TOKEN_VALIDITY", "+1 day");

