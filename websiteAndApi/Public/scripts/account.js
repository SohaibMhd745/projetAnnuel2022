"use strict";

import {
    FATAL_EXCEPTION,
    MYSQL_EXCEPTION,
    INVALID_PARAMETER,
    MISSING_PARAMETER,
    PARAMETER_WRONG_LENGTH,
    USER_NOT_FOUND,
    INCORRECT_USER_CREDENTIALS,
    //EMAIL_USED,
    INVALID_AUTH_TOKEN,
    //COMPANY_NOT_FOUND
} from './const.js';

// Unsigned / signed <main>
const mainUnsigned = document.getElementById("main-unsigned");
const mainSigned = document.getElementById("main-signed");

// Shows page depending on whether the user is signed or unsigned
if (localStorage.getItem("token") == "" || localStorage.getItem("token") == null) {

    mainUnsigned.hidden = false;

    // Login form
    const loginForm = document.getElementById("signin-form");
    const emailInput = document.getElementById("signin-email");
    const passwordInput = document.getElementById("signin-password");

    // On submit
    loginForm.addEventListener("submit", async (event) => {
        event.preventDefault();

        const email = emailInput.value;
        const password = passwordInput.value;
        const serializedInput = JSON.stringify({ "email": email, "password": password });

        try {
            var xhttp = new XMLHttpRequest();
            xhttp.open("POST", "/login/signin", true);
            xhttp.setRequestHeader("Content-Type", "application/json");
            xhttp.onreadystatechange = function () {
                if (this.readyState === 4) {
                    const response = this.responseText;
                    const parsedResponse = JSON.parse(response);
                    if (parsedResponse.success === true) {
                        localStorage.setItem("token", parsedResponse.token);
                        window.location.replace("/");
                    } else {
                        const errorSignin = document.getElementById("signin-error");
                        switch (parsedResponse.errorCode) {
                            case FATAL_EXCEPTION:
                                errorSignin.innerHTML = "Erreur fatale. Veuillez réessayer.";
                                break;
                            case MYSQL_EXCEPTION:
                                errorSignin.innerHTML = "Erreur base de données. Veuillez réessayer.";
                                break;
                            case INVALID_PARAMETER:
                                errorSignin.innerHTML = "Paramètre invalide.";
                                break;
                            case MISSING_PARAMETER:
                                errorSignin.innerHTML = "Paramètre manquant.";
                                break;
                            case PARAMETER_WRONG_LENGTH:
                                errorSignin.innerHTML = "Paramètre de longueur invalide.";
                                break;
                            case USER_NOT_FOUND:
                                errorSignin.innerHTML = "Utilisateur inexistant.";
                                break;
                            case INCORRECT_USER_CREDENTIALS:
                                errorSignin.innerHTML = "Identifiants invalides.";
                                break;
                            case INVALID_AUTH_TOKEN:
                                errorSignin.innerHTML = "Token invalide.";
                                break;
                            default:
                                errorSignin.innerHTML = "Erreur inconnue.";
                                break;
                        }
                    }
                }
            };
            xhttp.send(serializedInput);
        } catch (error) {
            console.error(error);
        }
    });
} else {
    mainSigned.hidden = false;
}

// Signout button
const signoutButton = document.getElementById("signout-button");
signoutButton.addEventListener("click", () => {
    localStorage.clear();
    window.location.replace("/");
});