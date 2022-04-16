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
    await new Promise(resolve => setTimeout(resolve, 3000));
    invoice.style.display = "none";
});