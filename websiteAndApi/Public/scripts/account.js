"use strict";

import { checkTokenValidity } from "./checkTokenValidity.js";
import { getUserData } from "./getUserData.js";
import { signinUser, signupUser, signoutUser, updateProfile, upgradeToPartner, addPrestation } from "./formActions.js";

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
function addScript(url) {
    var script = document.createElement('script');
    script.type = 'application/javascript';
    script.src = url;
    document.head.appendChild(script);
}
addScript('https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js');

const invoice = document.getElementById("user-invoice");
invoice.style.display = "none";

const downloadUserPDF = document.getElementById("download-user-pdf");
downloadUserPDF.addEventListener("click", async (event) => {
    event.preventDefault();
    /*window.open("/blank.php", '_blank').focus();
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
    invoice.style.display = "none";*/

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
                            p3.innerHTML = parsedResponse.table[i].individualprice + '€';
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
});