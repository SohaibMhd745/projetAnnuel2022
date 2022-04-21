import {
    FATAL_EXCEPTION, MYSQL_EXCEPTION, INVALID_PARAMETER, MISSING_PARAMETER, PARAMETER_WRONG_LENGTH,
    USER_NOT_FOUND, INCORRECT_USER_CREDENTIALS, INVALID_AUTH_TOKEN
} from './const.js';

function signinUser() {
    const emailInput = document.getElementById("signin-email");
    const passwordInput = document.getElementById("signin-password");

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
}

function signupUser() {
    const lastnameInput = document.getElementById("signup-lastname");
    const firstnameInput = document.getElementById("signup-firstname");
    const birthdateInput = document.getElementById("signup-birthdate");
    const numberInput = document.getElementById("signup-number");
    const emailInput = document.getElementById("signup-email");
    const passwordInput = document.getElementById("signup-password");

    const lastname = lastnameInput.value;
    const firstname = firstnameInput.value;
    const birthdate = birthdateInput.value;
    const number = numberInput.value;
    const email = emailInput.value;
    const password = passwordInput.value;

    const serializedInput = JSON.stringify({
        "lastname": lastname,
        "firstname": firstname,
        "birthdate": birthdate,
        "phone": number,
        "email": email,
        "password": password
    });

    try {
        var xhttp = new XMLHttpRequest();
        xhttp.open("POST", "/login/signup", false);
        xhttp.setRequestHeader("Content-Type", "application/json");
        xhttp.onreadystatechange = function () {
            if (this.readyState === 4) {
                const response = this.responseText;
                const parsedResponse = JSON.parse(response);
                if (parsedResponse.success === true) {
                    localStorage.setItem("token", parsedResponse.token);
                    window.location.replace("/");
                } else {
                    const errorSignup = document.getElementById("signup-error");
                    switch (parsedResponse.errorCode) {
                        case FATAL_EXCEPTION:
                            errorSignup.innerHTML = "Erreur fatale. Veuillez réessayer.";
                            break;
                        case MYSQL_EXCEPTION:
                            errorSignup.innerHTML = "Erreur base de données. Veuillez réessayer.";
                            break;
                        case INVALID_PARAMETER:
                            errorSignup.innerHTML = "Paramètre invalide.";
                            break;
                        case MISSING_PARAMETER:
                            errorSignup.innerHTML = "Paramètre manquant.";
                            break;
                        case PARAMETER_WRONG_LENGTH:
                            errorSignup.innerHTML = "Paramètre de longueur invalide.";
                            break;
                        case USER_NOT_FOUND:
                            errorSignup.innerHTML = "Utilisateur inexistant.";
                            break;
                        case INCORRECT_USER_CREDENTIALS:
                            errorSignup.innerHTML = "Identifiants invalides.";
                            break;
                        case INVALID_AUTH_TOKEN:
                            errorSignup.innerHTML = "Token invalide.";
                            break;
                        default:
                            errorSignup.innerHTML = "Erreur inconnue.";
                            break;
                    }
                }
            }
        };
        xhttp.send(serializedInput);
    } catch (error) {
        console.error(error);
    }
}

function signoutUser() {
    localStorage.clear();
    window.location.replace("/");
}

function updateProfile() {
    // TODO: Update profile of user in database
    window.alert("Profil mis à jour.");
}

function upgradeToPartner() {
    const nameInput = document.getElementById("upgrade-name");
    const revenueInput = document.getElementById("upgrade-revenue");
    const websiteInput = document.getElementById("upgrade-website");
    const sponsorcodeInput = document.getElementById("upgrade-sponsorcode");

    const name = nameInput.value;
    const revenue = revenueInput.value;
    const website = websiteInput.value;
    const sponsorcode = sponsorcodeInput.value;

    const token = localStorage.getItem("token");

    let serializedInput;

    if (sponsorcode !== "") {
        serializedInput = JSON.stringify({ "partnername": name, "revenue": revenue, "website": website, "token": token, "sponsorcode": sponsorcode });
    } else {
        serializedInput = JSON.stringify({ "partnername": name, "revenue": revenue, "website": website, "token": token });
    }

    try {
        var xhttp = new XMLHttpRequest();
        xhttp.open("POST", "/login/registercompany", false);
        xhttp.setRequestHeader("Content-Type", "application/json");
        xhttp.onreadystatechange = function () {
            if (this.readyState === 4) {
                const response = this.responseText;
                const parsedResponse = JSON.parse(response);
                if (parsedResponse.success === true) {
                    window.location.replace("/account");
                } else {
                    const errorUpgrade = document.getElementById("upgrade-error");
                    switch (parsedResponse.errorCode) {
                        case FATAL_EXCEPTION: errorUpgrade.innerHTML = "Erreur fatale. Veuillez réessayer."; break;
                        case MYSQL_EXCEPTION: errorUpgrade.innerHTML = "Erreur base de données. Veuillez réessayer."; break;
                        case INVALID_PARAMETER: errorUpgrade.innerHTML = "Paramètre invalide."; break;
                        case MISSING_PARAMETER: errorUpgrade.innerHTML = "Paramètre manquant."; break;
                        case PARAMETER_WRONG_LENGTH: errorUpgrade.innerHTML = "Paramètre de longueur invalide."; break;
                        case USER_NOT_FOUND: errorUpgrade.innerHTML = "Utilisateur inexistant."; break;
                        case INCORRECT_USER_CREDENTIALS: errorUpgrade.innerHTML = "Identifiants invalides."; break;
                        case INVALID_AUTH_TOKEN: errorUpgrade.innerHTML = "Token invalide."; break;
                        default: errorUpgrade.innerHTML = "Erreur inconnue."; break;
                    }
                }
            }
        };
        xhttp.send(serializedInput);
    } catch (error) {
        console.error(error);
    }
}

function addPrestation() {
    const nameInput = document.getElementById("addpresta-name");
    const descriptionInput = document.getElementById("addpresta-description");
    const priceInput = document.getElementById("addpresta-price");

    const name = nameInput.value;
    const description = descriptionInput.value;
    const price = priceInput.value;
    const token = localStorage.getItem("token");

    const serializedInput = JSON.stringify({ "name": name, "description": description, "price": price, "token": token });

    try {
        var xhttp = new XMLHttpRequest();
        xhttp.open("POST", "/catalog/addarticle", false);
        xhttp.setRequestHeader("Content-Type", "application/json");
        xhttp.onreadystatechange = function () {
            if (this.readyState === 4) {
                const response = this.responseText;
                const parsedResponse = JSON.parse(response);
                if (parsedResponse.success === true) {
                    window.location.replace("/account");
                } else {
                    const errorAddPresta = document.getElementById("addpresta-error");
                    switch (parsedResponse.errorCode) {
                        case FATAL_EXCEPTION:
                            errorAddPresta.innerHTML = "Erreur fatale. Veuillez réessayer.";
                            break;
                        case MYSQL_EXCEPTION:
                            errorAddPresta.innerHTML = "Erreur base de données. Veuillez réessayer.";
                            break;
                        case INVALID_PARAMETER:
                            errorAddPresta.innerHTML = "Paramètre invalide.";
                            break;
                        case MISSING_PARAMETER:
                            errorAddPresta.innerHTML = "Paramètre manquant.";
                            break;
                        case PARAMETER_WRONG_LENGTH:
                            errorAddPresta.innerHTML = "Paramètre de longueur invalide.";
                            break;
                        case USER_NOT_FOUND:
                            errorAddPresta.innerHTML = "Utilisateur inexistant.";
                            break;
                        case INCORRECT_USER_CREDENTIALS:
                            errorAddPresta.innerHTML = "Identifiants invalides.";
                            break;
                        case INVALID_AUTH_TOKEN:
                            errorAddPresta.innerHTML = "Token invalide.";
                            break;
                        default:
                            errorAddPresta.innerHTML = "Erreur inconnue.";
                            break;
                    }
                }
            }
        };
        xhttp.send(serializedInput);
    } catch (error) {
        console.error(error);
    }
}

export { signinUser, signupUser, signoutUser, updateProfile, upgradeToPartner, addPrestation }