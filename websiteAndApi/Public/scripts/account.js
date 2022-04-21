"use strict";

import {
    FATAL_EXCEPTION, MYSQL_EXCEPTION, INVALID_PARAMETER, MISSING_PARAMETER, PARAMETER_WRONG_LENGTH,
    USER_NOT_FOUND, INCORRECT_USER_CREDENTIALS, INVALID_AUTH_TOKEN
} from './const.js';
import { signinUser, signupUser, signoutUser, updateProfile, upgradeToPartner, addPrestation } from "./formActions.js";
import { checkTokenValidity } from "./checkTokenValidity.js";
import { getUserData } from "./getUserData.js";

// Unsigned & signed <main>
const mainUnsigned = document.getElementById("main-unsigned");
const mainSigned = document.getElementById("main-signed");

// Shows page depending on whether the user is signed or unsigned
checkTokenValidity(function (tokenValid) {

    // UNSIGNED
    if (!tokenValid) {
        mainSigned.style.display = "none";

        // Sign-in form
        const signinButton = document.getElementById("signin-submit");
        signinButton.addEventListener("click", async (event) => {
            event.preventDefault();
            signinUser();
        });

        // Sign-up form
        const signupButton = document.getElementById("signup-submit");
        signupButton.addEventListener("click", async (event) => {
            event.preventDefault();
            signupUser();
        });
    }

    // SIGNED
    else {
        mainUnsigned.style.display = "none";

        // Profile form
        getUserData("fill"); // Fill profile form with user data
        const profileButton = document.getElementById("profile-submit");
        profileButton.addEventListener("click", async (event) => {
            event.preventDefault();
            updateProfile();
        });

        // Upgrade form
        getUserData("partner"); // Check if user is a partner
        if (localStorage.getItem("isPartner") == "true") {
            const upgradeSection = document.getElementById("upgrade-section");
            upgradeSection.style.display = 'none';
        } else {
            const partnerSection = document.getElementById("partner-section");
            partnerSection.style.display = 'none';
            const upgradeButton = document.getElementById("upgrade-submit");
            upgradeButton.addEventListener("click", async (event) => {
                event.preventDefault();
                upgradeToPartner();
            });
        } localStorage.removeItem("isPartner");

        // Signout button
        const signoutButton = document.getElementById("signout");
        signoutButton.addEventListener("click", async (event) => {
            event.preventDefault();
            signoutUser();
        });

        // Add prestation form
        const addprestaButton = document.getElementById("addpresta-submit");
        addprestaButton.addEventListener("click", async (event) => {
            event.preventDefault();
            addPrestation();
        });

        /* BARCODE */
        const token = localStorage.getItem("token");
        const serializedInput = JSON.stringify({ "token": token });
        try {
            var xhttp = new XMLHttpRequest();
            xhttp.open("POST", "/login/getdata", false);
            xhttp.setRequestHeader("Content-Type", "application/json");
            xhttp.onreadystatechange = function () {
                if (this.readyState === 4) {
                    const response = this.responseText;
                    const parsedResponse = JSON.parse(response);
                    if (parsedResponse.success === true) {
                        const barcode = document.querySelector(".barcode");
                        barcode.setAttribute("jsbarcode-value", '867905' + parsedResponse.user.barcode);
                        JsBarcode(".barcode").init();
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
                    }
                }
            };
            xhttp.send(serializedInput);
        } catch (error) {
            console.error(error);
        }
    }
});

// Subviews
const subviewAccount = document.getElementById("subview-account");
const subviewAddPresta = document.getElementById("subview-addpresta");
subviewAddPresta.style.display = "none";

const partnerActionButton2 = document.getElementById("partner-2");
partnerActionButton2.addEventListener("click", async (event) => {
    event.preventDefault();
    subviewAccount.style.display = "none";
    subviewAddPresta.style.display = "block";
});

/* PDF GENERATION */
const invoice = document.getElementById("user-invoice");
invoice.style.display = "none";
const downloadUserPDF = document.getElementById("download-user-pdf");
downloadUserPDF.addEventListener("click", async (event) => {
    event.preventDefault();
    window.open("/blank.php", '_blank').focus();
    invoice.style.display = "block";
    getUserData("pdf");
    const opt = {
        margin: 0.8,
        filename: 'profile_card.pdf',
        image: { type: 'png' },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'p' }
    };
    html2pdf().set(opt).from(invoice).save();
    await new Promise(resolve => setTimeout(resolve, 2000));
    invoice.style.display = "none";
});