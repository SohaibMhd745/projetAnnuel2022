import { FATAL_EXCEPTION, MYSQL_EXCEPTION, INVALID_AUTH_TOKEN, ALREADY_IN_CART, NOT_IN_CART } from './const.js';

function addToCart(productNumber) {
    const token = localStorage.getItem("token");
    const id = document.getElementById("shop-item-id" + productNumber);
    const serializedInput = JSON.stringify({ "token": token, "id_prestation": id.value, "change": 1 });

    try {
        var xhttp = new XMLHttpRequest();
        xhttp.open("POST", "/order/change", false);
        xhttp.setRequestHeader("Content-Type", "application/json");
        xhttp.onreadystatechange = function () {
            if (this.readyState === 4) {
                const response = this.responseText;
                const parsedResponse = JSON.parse(response);
                if (parsedResponse.success === true) {
                    console.log("Ajout réussi !");
                } else {
                    switch (parsedResponse.errorCode) {
                        case FATAL_EXCEPTION: console.log("Erreur fatale. Veuillez réessayer."); break;
                        case MYSQL_EXCEPTION: console.log("Erreur base de données. Veuillez réessayer."); break;
                        case INVALID_AUTH_TOKEN: console.log("Token invalide."); break;
                        case ALREADY_IN_CART: console.log("Déjà dans le panier."); break;
                        case NOT_IN_CART: console.log("Pas dans le panier."); break;
                        default: console.log("Erreur inconnue."); break;
                    }
                }
            }
        };
        xhttp.send(serializedInput);
    } catch (error) {
        console.error(error);
    }
}

export { addToCart }