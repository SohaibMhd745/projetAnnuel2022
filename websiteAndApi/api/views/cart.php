<!DOCTYPE html>
<html lang="fr">
    <head>
        <?php include("includes/headInfo.php"); ?>
        <script>
            function changeQuantity(cartItem, action) {
                const token = localStorage.getItem("token");
                const id = document.getElementById("cart-id" + cartItem).innerHTML;
                const idValue = id.replace('ID: ', '');
                const serializedInput = JSON.stringify({ "token": token, "id_prestation": idValue, "change": action });
                try {
                    var xhttp = new XMLHttpRequest();
                    xhttp.open("POST", "/order/change", false);
                    xhttp.setRequestHeader("Content-Type", "application/json");
                    xhttp.onreadystatechange = function () {
                        if (this.readyState === 4) {
                            const response = this.responseText;
                            const parsedResponse = JSON.parse(response);
                            if (parsedResponse.success === true) {
                                const quantity = document.getElementById("cart-quantity" + cartItem);
                                let quantityValue;
                                quantityValue = parseInt(quantity.innerHTML.replace('x', ''));
                                if(action == 1) quantityValue += 1; else quantityValue -= 1;
                                quantity.innerHTML = quantityValue + 'x';
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
        </script>
    </head>
    <body>
        <header>
            <?php
            $page_name = "cart";
            include("includes/header.php");
            ?>
        </header>

        <main>
            <div class="separator-l"></div>

            <div class="container cart-container">
                <div class="row">
                    <p class="title">Panier</p>
                    <div class="separator-s"></div>
                    <img src="/assets/img/cart.svg" alt="Catalogue des prestations" height="256px" />
                </div>

                <div class="separator-m"></div>

                <div id="cart-full"></div>

                <div id="cart-empty">
                    <div class="row"><p class="shop-item-name">Panier vide / non-connecté.</p></div>
                </div>
            </div>

            <div class="separator-l"></div>
        </main>

        <footer>
            <?php include("includes/footer.php"); ?>
        </footer>
        <script type="module" src="/scripts/cart.js"></script>
    </body>
</html>