<?php

//Order
const INVALID_ORDER = -102;

const ALPHABETICAL_ORDER = 10;
const CHRONOLOGICAL_ORDER = 11;


const NO_EXCEPTION = 1;

//General Exceptions
const FATAL_EXCEPTION = 0;
const MYSQL_EXCEPTION = -1;
const INVALID_PARAMETER = -2;
const MISSING_PARAMETER = -3;
const PARAMETER_WRONG_LENGTH = -4;

//User Related Exceptions
const USER_NOT_FOUND = -10;
const INCORRECT_USER_CREDENTIALS = -11;
const EMAIL_USED = -12;
const INVALID_AUTH_TOKEN = -13;

//Partner Related Exceptions
const COMPANY_NOT_FOUND = -15;
const INVALID_CODE = -16;

//Handle set field values
const DEFAULT_DATE_MIN = "1900-01-01";
const TOKEN_VALIDITY = "+1 day";

const WRONG_METHOD = -100;
const WRONG_ACTION = -101;