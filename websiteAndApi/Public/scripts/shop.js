"use strict";

import { sendCatalogRequest, setupFilterByPartner } from "./catalogRequests.js";
import { addToCart } from "./addToCart.js";

sendCatalogRequest("chrono", true); // Default sort
setupFilterByPartner(); // Setup "filter by partner" feature

document.querySelectorAll('.shop-btn').forEach(button => {
    button.addEventListener('click', event => {
        event.preventDefault();

        const buttonID = button.id.charAt(button.id.length - 1);
        addToCart(buttonID);
    })
})

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

const filterButton = document.getElementById("filter-button");
filterButton.addEventListener("click", async (event) => {
    event.preventDefault();

    const filterSelect = document.getElementById("filter-select");
    const partner = filterSelect.value;
    sendCatalogRequest("chrono", false, partner);
});

document.getElementsByTagName("BODY")[0].style.display = "block";