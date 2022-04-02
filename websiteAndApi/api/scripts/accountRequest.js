//"use strict";

const form = document.getElementById("signin-form");
const emailInput = document.getElementById("signin-email");
const passwordInput = document.getElementById("signin-password");
console.log("feur");

form.addEventListener("submit", async (event) => {
    event.preventDefault();

    const email = emailInput.value;
    const password = passwordInput.value;
    const serializedInput = JSON.stringify({ email, password });

    try {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4) {
                const response = this.responseText;
                console.log(response);
            }
        };
        xhttp.open("GET", "/login/signin", false);
        xhttp.send(serializedInput);
    } catch (error) {
        console.error(error);
    }
})