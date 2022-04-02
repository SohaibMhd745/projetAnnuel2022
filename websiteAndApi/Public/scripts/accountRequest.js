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
        xhttp.open("GET", "/login/signin", true);
        xhttp.setRequestHeader("Content-Type", "application/json");
        xhttp.onreadystatechange = function () {
            if (this.readyState === 4) {
                const response = this.responseText;
                console.log(response);
            }
        };
        xhttp.send(serializedInput);
    } catch (error) {
        console.error(error);
    }
})