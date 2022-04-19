import { sendRequest } from './sendRequest';

function sendCatalogRequest(mode, reverse, partner = "-1") {
    const serializedInput = JSON.stringify({ "mode": mode, "reverse": reverse, "id_partner": partner });

    if (sendRequest("POST", "/catalog/orderedsearch", serializedInput) == true) {
        let i;
        for (i = 0; i < 8; i++) {
            if (i < parsedResponse.table.length) {
                const name = document.getElementById("shop-item-name" + i);
                const description = document.getElementById("shop-item-description" + i);
                const price = document.getElementById("shop-item-price" + i);
                const id = document.getElementById("shop-item-id" + i);
                name.innerHTML = parsedResponse.table[i].name;
                description.innerHTML = parsedResponse.table[i].description;
                price.innerHTML = parsedResponse.table[i].price + '€';
                id.innerHTML = parsedResponse.table[i].id;
            } else {
                const divToHide = document.getElementById("shop-item" + i);
                divToHide.style.display = "none";
            }
        }
    }
}

function setupFilterByPartner() {
    const serializedInput = JSON.stringify({});
    if (sendRequest("POST", "/login/getpartners", serializedInput) == true) {
        let i;
        for (i = 0; i < parsedResponse.table.length; i++) {
            const partnerName = parsedResponse.table[i].name;
            const partnerID = parsedResponse.table[i].id;

            const option = document.createElement("option");
            option.setAttribute("value", partnerID);
            option.innerHTML = partnerName;

            const filterSelect = document.getElementById("filter-select");
            filterSelect.appendChild(option);
        }
    }
}

export { sendCatalogRequest, setupFilterByPartner }