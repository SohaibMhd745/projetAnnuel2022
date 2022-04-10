"use strict";

import { sendCatalogRequest } from "./sendCatalogRequest.js";

const sortButton = document.getElementById("sort-button");

// On submit
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