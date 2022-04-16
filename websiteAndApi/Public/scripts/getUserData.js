function getUserData(action) {
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
                    switch(action) {
                        case "fill":
                            document.getElementById("profile-lastname").value = parsedResponse.user.lastname;
                            document.getElementById("profile-firstname").value = parsedResponse.user.firstname;
                            document.getElementById("profile-birthdate").value = parsedResponse.user.birthdate;
                            document.getElementById("profile-number").value = parsedResponse.user.phone;
                            break;
                        case "partner":
                            const idPartner = parsedResponse.user.id_partner;
                            if(idPartner!==-1) localStorage.setItem("isPartner", true); else localStorage.setItem("isPartner", false); break;
                        case "pdf":
                            console.log("yes");
                            document.getElementById("pdf-lastname").innerHTML += parsedResponse.user.lastname;
                            document.getElementById("pdf-firstname").innerHTML += parsedResponse.user.firstname;
                            document.getElementById("pdf-birthdate").innerHTML += parsedResponse.user.birthdate;
                            document.getElementById("pdf-number").innerHTML += parsedResponse.user.phone;
                            break;
                        default: console.log("Invalid getUserData method"); break;
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
                        default: console.log("Uknown error."); break;
                    }
                }
            }
        };
        xhttp.send(serializedInput);
    } catch (error) {
        console.error(error);
    }
}

export { getUserData }