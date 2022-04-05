//General Exceptions
const FATAL_EXCEPTION = 0;
const MYSQL_EXCEPTION = -1;
const INVALID_PARAMETER = -2;
const MISSING_PARAMETER = -3;
const PARAMETER_WRONG_LENGTH = -4;

//User Related Exceptions
const USER_NOT_FOUND = -10;
const INCORRECT_USER_CREDENTIALS = -11;
//const EMAIL_USED = -12;
const INVALID_AUTH_TOKEN = -13;

//Partner Related Exceptions
const COMPANY_NOT_FOUND = -15;

export {
    FATAL_EXCEPTION,
    MYSQL_EXCEPTION,
    INVALID_PARAMETER,
    MISSING_PARAMETER,
    PARAMETER_WRONG_LENGTH,
    USER_NOT_FOUND,
    INCORRECT_USER_CREDENTIALS,
    INVALID_AUTH_TOKEN,
    COMPANY_NOT_FOUND
}