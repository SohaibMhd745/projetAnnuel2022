import {
    FATAL_EXCEPTION,
    MYSQL_EXCEPTION,
    INVALID_PARAMETER,
    MISSING_PARAMETER,
    PARAMETER_WRONG_LENGTH,
    USER_NOT_FOUND,
    INCORRECT_USER_CREDENTIALS,
    INVALID_AUTH_TOKEN
} from './const.js';

//import { displayPrestations } from './displayPrestations.js';

function sendCatalogRequest(mode, reverse) {
    const serializedInput = JSON.stringify({ "mode": mode, "reverse": reverse });

    try {
        var xhttp = new XMLHttpRequest();
        xhttp.open("POST", "/catalog/orderedsearch", false);
        xhttp.setRequestHeader("Content-Type", "application/json");
        xhttp.onreadystatechange = function () {
            if (this.readyState === 4) {
                const response = this.responseText;
                const parsedResponse = JSON.parse(response);
                console.log(parsedResponse);
                if (parsedResponse.success === true) {
                    let i;
                    for(i=1; i<=8; i++) {
                        const name = document.getElementById("shop-item-name" + i);
                        const description = document.getElementById("shop-item-description" + i);
                        const price = document.getElementById("shop-item-price" + i);
                
                        name.innerHTML = parsedResponse.table[i].name;
                        description.innerHTML = parsedResponse.table[i].description;
                        price.innerHTML = parsedResponse.table[i].price;
                    }
                } else {
                    const sortError = document.getElementById("shop-error");
                    switch (parsedResponse.errorCode) {
                        case FATAL_EXCEPTION: sortError.innerHTML = "Erreur fatale. Veuillez réessayer."; break;
                        case MYSQL_EXCEPTION: sortError.innerHTML = "Erreur base de données. Veuillez réessayer."; break;
                        case INVALID_PARAMETER: sortError.innerHTML = "Paramètre invalide."; break;
                        case MISSING_PARAMETER: sortError.innerHTML = "Paramètre manquant."; break;
                        case PARAMETER_WRONG_LENGTH: sortError.innerHTML = "Paramètre de longueur invalide."; break;
                        case USER_NOT_FOUND: sortError.innerHTML = "Utilisateur inexistant."; break;
                        case INCORRECT_USER_CREDENTIALS: sortError.innerHTML = "Identifiants invalides."; break;
                        case INVALID_AUTH_TOKEN: sortError.innerHTML = "Token invalide."; break;
                        default: sortError.innerHTML = "Erreur inconnue."; break;
                    }
                }
            }
        };
        xhttp.send(serializedInput);
    } catch (error) {
        console.error(error);
    }
}

export {
    sendCatalogRequest
}