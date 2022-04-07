"use strict";

import {
    FATAL_EXCEPTION,
    MYSQL_EXCEPTION,
    INVALID_PARAMETER,
    MISSING_PARAMETER,
    PARAMETER_WRONG_LENGTH,
    USER_NOT_FOUND,
    INCORRECT_USER_CREDENTIALS,
    INVALID_AUTH_TOKEN,
    COMPANY_NOT_FOUND
} from './const.js';

import { checkTokenValidity } from "./checkTokenValidity.js";

// Unsigned / signed <main>
const mainUnsigned = document.getElementById("main-unsigned");
const mainSigned = document.getElementById("main-signed");

// Shows page depending on whether the user is signed or unsigned
checkTokenValidity(function (tokenValid) {
    if (!tokenValid) {
        mainSigned.style.display = "none";

        // Login form
        const loginButton = document.getElementById("signin-submit");
        const emailInput = document.getElementById("signin-email");
        const passwordInput = document.getElementById("signin-password");

        // On submit
        loginButton.addEventListener("click", async (event) => {
            event.preventDefault();

            const email = emailInput.value;
            const password = passwordInput.value;
            const serializedInput = JSON.stringify({ "email": email, "password": password });

            try {
                var xhttp = new XMLHttpRequest();
                xhttp.open("POST", "/login/signin", false);
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
        mainUnsigned.style.display = "none";

        // Upgrade form
        const upgradeButton = document.getElementById("upgrade-submit");
        const partnernameInput = document.getElementById("upgrade-partnername");
        const revenueInput = document.getElementById("upgrade-revenue");
        const websiteInput = document.getElementById("upgrade-website");
        const sponsorcodeInput = document.getElementById("upgrade-sponsorcode");

        // On submit
        upgradeButton.addEventListener("click", async (event) => {
            event.preventDefault();

            const partnername = partnernameInput.value;
            const revenue = revenueInput.value;
            const website = websiteInput.value;
            const sponsorcode = sponsorcodeInput.value;

            const token = localStorage.getItem("token");

            let serializedInput;

            if(sponsorcode !== ""){
                serializedInput = JSON.stringify({ "partnername": partnername, "revenue": revenue, "website": website, "token":token, "sponsorcode": sponsorcode });
            } else {
                serializedInput = JSON.stringify({ "partnername": partnername, "revenue": revenue, "website": website, "token":token });
            }

            try {
                var xhttp = new XMLHttpRequest();
                xhttp.open("POST", "/login/registercompany", false);
                xhttp.setRequestHeader("Content-Type", "application/json");
                xhttp.onreadystatechange = function () {
                    console.log(this.readyState);
                    if (this.readyState === 4) {
                        const response = this.responseText;
                        const parsedResponse = JSON.parse(response);
                        if (parsedResponse.success === true) {
                            window.location.replace("/account");
                        } else {
                            const errorUpgrade = document.getElementById("upgrade-error");
                            switch (parsedResponse.errorCode) {
                                case FATAL_EXCEPTION:
                                    errorUpgrade.innerHTML = "Erreur fatale. Veuillez réessayer.";
                                    break;
                                case MYSQL_EXCEPTION:
                                    errorUpgrade.innerHTML = "Erreur base de données. Veuillez réessayer.";
                                    break;
                                case INVALID_PARAMETER:
                                    errorUpgrade.innerHTML = "Paramètre invalide.";
                                    break;
                                case MISSING_PARAMETER:
                                    errorUpgrade.innerHTML = "Paramètre manquant.";
                                    break;
                                case PARAMETER_WRONG_LENGTH:
                                    errorUpgrade.innerHTML = "Paramètre de longueur invalide.";
                                    break;
                                case COMPANY_NOT_FOUND:
                                    errorUpgrade.innerHTML = "Entreprise inexistante.";
                                    break;
                                case INVALID_AUTH_TOKEN:
                                    errorUpgrade.innerHTML = "Token invalide.";
                                    break;
                                default:
                                    errorUpgrade.innerHTML = "Erreur inconnue.";
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
    }
});


// Signout button
const signoutButton = document.getElementById("signout");
signoutButton.addEventListener("click", () => {
    localStorage.clear();
    window.location.replace("/");
});