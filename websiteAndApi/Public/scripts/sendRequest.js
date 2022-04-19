import {
    FATAL_EXCEPTION, MYSQL_EXCEPTION, INVALID_PARAMETER, MISSING_PARAMETER, PARAMETER_WRONG_LENGTH,
    USER_NOT_FOUND, INCORRECT_USER_CREDENTIALS, INVALID_AUTH_TOKEN
} from './const.js';

function sendRequest(method, url, serializedInput) {
    try {
        var xhttp = new XMLHttpRequest();
        xhttp.open(method, url, false);
        xhttp.setRequestHeader("Content-Type", "application/json");
        xhttp.onreadystatechange = function () {
            if (this.readyState === 4) {
                const response = this.responseText;
                const parsedResponse = JSON.parse(response);
                if (parsedResponse.success === true) {
                    return true;
                } else {
                    switch (parsedResponse.errorCode) {
                        case FATAL_EXCEPTION: console.log("Erreur fatale. Veuillez réessayer."); break;
                        case MYSQL_EXCEPTION: console.log("Erreur base de données. Veuillez réessayer."); break;
                        case INVALID_PARAMETER: console.log("Paramètre invalide."); break;
                        case MISSING_PARAMETER: console.log("Paramètre manquant."); break;
                        case PARAMETER_WRONG_LENGTH: console.log("Paramètre de longueur invalide."); break;
                        case USER_NOT_FOUND: console.log("Utilisateur inexistant."); break;
                        case INCORRECT_USER_CREDENTIALS: console.log("Identifiants invalides."); break;
                        case INVALID_AUTH_TOKEN: console.log("Token invalide."); break;
                        default: console.log("Unknown error."); break;
                    }
                    return false;
                }
            }
        };
        xhttp.send(serializedInput);
    } catch (error) {
        console.error(error);
    }
}

export { sendRequest }