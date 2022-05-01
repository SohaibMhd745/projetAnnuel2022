package com.akm.back;

public enum AkmException {
    //General Exceptions
    NO_EXCEPTION(1, "OK"),
    FATAL_EXCEPTION(0, "Erreur Inconnue"),
    MYSQL_EXCEPTION(-1, "Erreur de base de donnée"),
    OUTSIDE_API_EXCEPTION(-1000, "Erreur externe d'API"),
    BAD_REQUEST(-1001, "Format de requête incorrect"),

    //Parameter related Exceptions
    INVALID_PARAMETER(-2, "Paramètre invalide"),
    MISSING_PARAMETER(-3, "Paramètre manquant"),
    PARAMETER_WRONG_LENGTH(-4, "Paramètre de taille incorrecte"),

    //User related Exceptions
    USER_NOT_FOUND(-10, "Utilisateur inconnu"),
    INCORRECT_USER_CREDENTIALS(-11, "échc de la connexion"),
    EMAIL_USED(-12, "Email déjà utilisé"),
    INVALID_AUTH_TOKEN(-13, "Session expirée"),

    //Partner related Exceptions
    COMPANY_NOT_FOUND(-15, "Ce Partenaire n'existe pas"),
    INVALID_CODE(-16, "Code sponsor invalide");

    public final int code;
    public final String errorLabel;

    AkmException(int code, String errorLabel) {
        this.code = code;
        this.errorLabel = errorLabel;
    }

    /**
     * Returns exception matching provided code
     * @param code int
     * @return AkmException
     */
    public static AkmException getExceptionFromCode(int code){
        for (AkmException exception: values()) {
            if (exception.code == code) return exception;
        }
        return NO_EXCEPTION;
    }
}
