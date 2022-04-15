<!DOCTYPE html>
<html lang="fr">
    <head>
        <?php include("includes/headInfo.php"); ?>
        <script>
            function changeQuantity(cartItem, action) {
                const token = localStorage.getItem("token");
                const id = document.getElementById("cart-id" + cartItem).innerHTML;
                const serializedInput = JSON.stringify({ "token": token, "id_prestation": id, "change": action });
                try {
                    var xhttp = new XMLHttpRequest();
                    xhttp.open("POST", "/order/change", false);
                    xhttp.setRequestHeader("Content-Type", "application/json");
                    xhttp.onreadystatechange = function () {
                        if (this.readyState === 4) {
                            const response = this.responseText;
                            console.log(response);
                            const parsedResponse = JSON.parse(response);
                            if (parsedResponse.success === true) {
                                const quantity = document.getElementById("cart-quantity" + cartItem);
                                let quantityValue;
                                quantityValue = parseInt(quantity.innerHTML.replace('x', ''));
                                if(action == 1) quantityValue += 1; else quantityValue -= 1;
                                console.log(quantityValue);
                                quantity.innerHTML = quantityValue + 'x';
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
            $script_name = "/shop.php";
            include("includes/header.php");
            ?>
        </header>

        <main>
            <div class="separator-l"></div>

            <div class="container cart-container">
                <div class="row">
                    <p class="title">Panier</p>
                    <div class="separator-s"></div>
                    <img src="/assets/img/ill/cart.svg" alt="Catalogue des prestations" height="256px" />
                </div>

                <div class="separator-m"></div>

                    <div id="cart-signed">
                    <?php for($i=0; $i<8; $i++) {
                        echo '
                        <div class="row cart-row">
                            <div class="col-3">
                                <p class="cart-quantity" id="cart-quantity' . $i . '">0x</p>
                            </div>
                            <div class="col-6">
                                <p class="shop-item-name" id="cart-name' . $i . '">Nom de la prestation</p>
                                <div class="shop-item-numbers">
                                    <p id="cart-price' . $i . '">0</p>
                                    <p id="cart-id' . $i . '">0</p>
                                </div>
                            </div>
                            <div class="col-3">
                                <button class="button-red" onclick="changeQuantity(' . $i . ', 1);">+</button>
                                <button class="button-red" onclick="changeQuantity(' . $i . ', -1);">-</button>
                            </div>
                        </div>';}
                    ?>
                </div>
                <div id="cart-unsigned">
                    <div class="row">
                        <p class="shop-item-name">Panier vide.</p>
                    </div>
                </div>
            </div>

            <div class="separator-l"></div>
        </main>

        <footer>
            <?php include("includes/footer.php"); ?>
        </footer>
        <script type="module" src="/scripts/showCart.js"></script>
    </body>
</html>