package com.ashway.akmbarcodescanner.httphandler;

public class ResponseHandler {
    private final boolean success;
    private String usertype;
    private Object user;
    private String errorMessage;
    private String step;
    private int errorCode;

    public ResponseHandler(boolean success, String usertype, Object user) {
        this.success = success;
        this.usertype = usertype;
        this.user = user;
    }

    public ResponseHandler(boolean success, String errorMessage, int errorCode, String step) {
        this.success = success;
        this.errorMessage = errorMessage;
        this.errorCode = errorCode;
        this.step = step;
    }

    public boolean isSuccess() {
        return success;
    }

    public Object getUser() {
        return user;
    }

    public String getUsertype() {
        return usertype;
    }

    public String getErrorMessage(){
        return errorMessage;
    }

    public String getStep() {
        return step;
    }

    public AkmException getException() {
        return AkmException.getExceptionFromCode(errorCode);
    }
}
