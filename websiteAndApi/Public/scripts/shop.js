"use strict";

import { sendCatalogRequest } from "./sendCatalogRequest.js";
import { addToCart } from "./addToCart.js";

sendCatalogRequest("alpha", false); // Affichage par défaut

const sortButton = document.getElementById("sort-button");
sortButton.addEventListener("click", async (event) => {
    event.preventDefault();

    const sortSelect = document.getElementById("sort-select");
    const mode = sortSelect.value;

    switch (mode) {
        case "alpha-c": sendCatalogRequest("alpha", false); break;
        case "chrono-c": sendCatalogRequest("chrono", false); break;
        case "alpha-d": sendCatalogRequest("alpha", true); break;
        case "chrono-d": sendCatalogRequest("chrono", true); break;
    }
});

document.querySelectorAll('.shop-btn').forEach(button => {
    button.addEventListener('click', event => {
        event.preventDefault();

        const buttonID = button.id.charAt(button.id.length - 1);
        addToCart(buttonID);
    })
})