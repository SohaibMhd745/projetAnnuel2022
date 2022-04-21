<?php
//TODO: ENUM
//TODO: report caught error function


//Partnership policy
const SPONSOR_REWARD_POLICY = [500, 615, 1000];
const SUBSEQUENT_SPONSORS = 1500;
const SUBSCRIPTION_POLICY = [
    ["upper_limit"=>200000, "percentage"=>0.0],
    ["upper_limit"=>800000,"percentage"=>0.8],
    ["upper_limit"=>1500000,"percentage"=>0.6],
    ["upper_limit"=>3000000,"percentage"=>0.4],
    ["upper_limit"=>-1,"percentage"=>0.3],
];

//Order
const INVALID_ORDER = -102;

const ALPHABETICAL_ORDER = 10;
const CHRONOLOGICAL_ORDER = 11;

const NO_EXCEPTION = 1;
const OUTSIDE_API_EXCEPTION = -1000;

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
const INVALID_PARTNER_TOKEN = -14;

//Partner Related Exceptions
const COMPANY_NOT_FOUND = -15;
const INVALID_CODE = -16;

//Order related exceptions
const ORDER_ALREADY_EXISTS = -30;
const ALREADY_IN_CART = -31;
const NOT_IN_CART = -32;
const ORDER_EMPTY = -33;
const INVALID_CONFIRM_CODE = -34;
const NOT_ENOUGH_POINTS = -35;

//Handle set field values
const DEFAULT_DATE_MIN = "1900-01-01";
const TOKEN_VALIDITY = "+1 day";

const WRONG_METHOD = -100;
const WRONG_ACTION = -101;