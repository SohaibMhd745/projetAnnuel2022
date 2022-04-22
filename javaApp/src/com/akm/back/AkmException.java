package com.akm.back;

public enum AkmException {
    //General Exceptions
    NO_EXCEPTION(1),
    FATAL_EXCEPTION(0),
    MYSQL_EXCEPTION(-1),
    OUTSIDE_API_EXCEPTION(-1000),

    //Parameter related Exceptions
    INVALID_PARAMETER(-2),
    MISSING_PARAMETER(-3),
    PARAMETER_WRONG_LENGTH(-4),

    //User related Exceptions
    USER_NOT_FOUND(-10),
    INCORRECT_USER_CREDENTIALS(-11),
    EMAIL_USED(-12),
    INVALID_AUTH_TOKEN(-13),

    //Partner related Exceptions
    INVALID_PARTNER_TOKEN(-14),
    COMPANY_NOT_FOUND(-15),
    INVALID_CODE(-16);

    public final int code;

    AkmException(int code) {
        this.code = code;
    }

    /**
     * Returns exception matching provided code
     * @param code int
     * @return AkmException
     */
    public AkmException getExceptionFromCode(int code){
        for (AkmException exception:AkmException.values()) {
            if (exception.code == code) return exception;
        }
        return NO_EXCEPTION;
    }
}
