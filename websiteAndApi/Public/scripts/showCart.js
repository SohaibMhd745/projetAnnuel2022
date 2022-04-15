import {
    FATAL_EXCEPTION, MYSQL_EXCEPTION, INVALID_PARAMETER, MISSING_PARAMETER, PARAMETER_WRONG_LENGTH,
    USER_NOT_FOUND, INCORRECT_USER_CREDENTIALS, INVALID_AUTH_TOKEN
} from './const.js';

function changeDisplay(display) {
    const signedCart = document.getElementById("cart-signed");
    const unsignedCart = document.getElementById("cart-unsigned");
    if (display === "cart") {
        signedCart.style.display = "block";
        unsignedCart.style.display = "none";
    } else if (display === "empty") {
        unsignedCart.style.display = "block";
        signedCart.style.display = "none";
    } else console.log("Paramètre invalide");
}

function showCart() {
    const token = localStorage.getItem("token");
    if (token == null) {
        changeDisplay("empty");
    } else {
        changeDisplay("cart");
        const token = localStorage.getItem("token");
        const serializedInput = JSON.stringify({ "token": token });
        try {
            var xhttp = new XMLHttpRequest();
            xhttp.open("POST", "/order/getorder", false);
            xhttp.setRequestHeader("Content-Type", "application/json");
            xhttp.onreadystatechange = function () {
                if (this.readyState === 4) {
                    const response = this.responseText;
                    const parsedResponse = JSON.parse(response);
                    if (parsedResponse.success === true) {
                        if(parsedResponse.table.length > 0) {
                            let i;
                            for (i = 0; i < parsedResponse.table.length; i++) {
                                const quantity = document.getElementById("cart-quantity" + i);
                                const name = document.getElementById("cart-name" + i);
                                const price = document.getElementById("cart-price" + i);
                                const id = document.getElementById("cart-id" + i);
                                quantity.innerHTML = parsedResponse.table[i].quantity + 'x';
                                name.innerHTML = parsedResponse.table[i].name;
                                price.innerHTML = parsedResponse.table[i].individualprice + '€';
                                id.innerHTML = 'ID: ' + parsedResponse.table[i].id;
                            }
                        } else {
                            changeDisplay("empty");
                        }
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
                            default: console.log("Uknown error."); break;
                        }
                    }
                }
            };
            xhttp.send(serializedInput);
        } catch (error) {
            console.error(error);
        }
    }
}

showCart();