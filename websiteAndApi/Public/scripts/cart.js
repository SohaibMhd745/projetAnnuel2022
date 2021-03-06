import {
    FATAL_EXCEPTION, MYSQL_EXCEPTION, INVALID_PARAMETER, MISSING_PARAMETER, PARAMETER_WRONG_LENGTH,
    USER_NOT_FOUND, INCORRECT_USER_CREDENTIALS, INVALID_AUTH_TOKEN
} from './const.js';

const token = localStorage.getItem("token");

const fullCart = document.getElementById("cart-full");
const emptyCart = document.getElementById("cart-empty");

if (token == null) {
    fullCart.style.display = "none";
    emptyCart.style.display = "block";
} else {
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
                    if (parsedResponse.table.length != 0) {
                        fullCart.style.display = "block";
                        emptyCart.style.display = "none";
                        let div1, div2, div3, div4, div5, p1, p2, p3, p4, btn1, btn2, i;
                        for (i = 0; i < parsedResponse.table.length; i++) {
                            div1 = document.createElement("div"); div1.classList.add("row", "cart-row");
                            div2 = document.createElement("div"); div2.classList.add("col-3");
                            p1 = document.createElement("p"); p1.classList.add("cart-quantity");
                            div3 = document.createElement("div"); div3.classList.add("col-6");
                            p2 = document.createElement("p"); p2.classList.add("shop-item-name");
                            div4 = document.createElement("div"); div4.classList.add("shop-item-numbers");
                            p3 = document.createElement("p"); p4 = document.createElement("p");
                            div5 = document.createElement("div"); div5.classList.add("col-3");
                            btn1 = document.createElement("button"); btn1.classList.add("button-red"); btn1.setAttribute("onclick", "changeQuantity(" + i + ", 1);");
                            btn2 = document.createElement("button"); btn2.classList.add("button-red"); btn2.setAttribute("onclick", "changeQuantity(" + i + ", -1);");

                            p1.innerHTML = parsedResponse.table[i].quantity + 'x'; p1.setAttribute("id", "cart-quantity" + i);
                            p2.innerHTML = parsedResponse.table[i].name;
                            p3.innerHTML = parsedResponse.table[i].individualprice + '???';
                            p4.innerHTML = 'ID: ' + parsedResponse.table[i].id; p4.setAttribute("id", "cart-id" + i);
                            btn1.innerHTML = "+"; btn2.innerHTML = "-";

                            fullCart.appendChild(div1); div1.appendChild(div2); div2.appendChild(p1); div1.appendChild(div3); div3.appendChild(p2);
                            div3.appendChild(div4); div4.appendChild(p3); div4.appendChild(p4); div1.appendChild(div5); div5.appendChild(btn1); div5.appendChild(btn2);
                        }
                        const validateCart = document.createElement("button");
                        validateCart.classList.add("button", "button-red");
                        validateCart.setAttribute("onclick", "window.location.replace('/checkout/validateorder/" + token + "')");
                        validateCart.innerHTML = "Valider la commande";
                        fullCart.appendChild(validateCart);
                    } else {
                        fullCart.style.display = "none";
                        emptyCart.style.display = "block";
                    }
                } else {
                    switch (parsedResponse.errorCode) {
                        case FATAL_EXCEPTION: console.log("Erreur fatale. Veuillez r??essayer."); break;
                        case MYSQL_EXCEPTION: console.log("Erreur base de donn??es. Veuillez r??essayer."); break;
                        case INVALID_PARAMETER: console.log("Param??tre invalide."); break;
                        case MISSING_PARAMETER: console.log("Param??tre manquant."); break;
                        case PARAMETER_WRONG_LENGTH: console.log("Param??tre de longueur invalide."); break;
                        case USER_NOT_FOUND: console.log("Utilisateur inexistant."); break;
                        case INCORRECT_USER_CREDENTIALS: console.log("Identifiants invalides."); break;
                        case INVALID_AUTH_TOKEN: console.log("Token invalide."); break;
                        default: console.log("Unknown error."); break;
                    }
                }
            }
        };
        xhttp.send(serializedInput);
    } catch (error) {
        console.error(error);
    }
}