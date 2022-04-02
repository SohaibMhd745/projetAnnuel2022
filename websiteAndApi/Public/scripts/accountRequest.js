//"use strict";

const form = document.getElementById("signin-form");
const emailInput = document.getElementById("signin-email");
const passwordInput = document.getElementById("signin-password");

form.addEventListener("submit", async (event) => {
    event.preventDefault();

    const email = emailInput.value;
    const password = passwordInput.value;
    const serializedInput = JSON.stringify({ "email": email, "password": password });

    console.log(serializedInput);

    try {
        var xhttp = new XMLHttpRequest();
        xhttp.open("POST", "/login/signin", true);
        xhttp.setRequestHeader("Content-Type", "application/json");
        xhttp.onreadystatechange = function () {
            if (this.readyState === 4) {
                const response = this.responseText;
                parsedResponse = JSON.parse(response);
                if(parsedResponse.success === true){
                    // parsedResponse.token à mettre dans le local storage OU dans le header (pour le passer au php)
                } else {
                    switch(parsedResponse.errorCode){
                        // constantes à traduire en js depuis const.php dans un nouveau script
                        // les erreurs de reportErrors.php à mettre en case:
                        // + les erreurs de Login.php lignes 118-126
                        // todo account.php 33 & 48
                        // localhost/login/getdata pour verif si l'user est co
                    }
                }
            }
        };
        xhttp.send(serializedInput);
    } catch (error) {
        console.error(error);
    }
})