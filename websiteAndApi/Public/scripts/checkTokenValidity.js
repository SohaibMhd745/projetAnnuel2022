import {INVALID_AUTH_TOKEN} from "./const.js";

function checkTokenValidity(callback){
    if(localStorage.getItem("token") == ""||localStorage.getItem("token") == null) callback(false);
    else try {
        var xhttp = new XMLHttpRequest();
        xhttp.open("POST", "/login/getdata", false);
        xhttp.setRequestHeader("Content-Type", "application/json");
        xhttp.onreadystatechange = function () {
            if (this.readyState === 4) {
                const response = this.responseText;
                const parsedResponse = JSON.parse(response);
                if (parsedResponse.success === true) callback(true);
                else if (parsedResponse.errorCode === INVALID_AUTH_TOKEN) {
                    localStorage.removeItem("token");
                    callback(false);
                }
            }
        };
        xhttp.send(JSON.stringify({"token" : localStorage.getItem("token")} ));
    } catch (error) {
        console.error(error);
        callback(false);
    }
}

export{ checkTokenValidity }