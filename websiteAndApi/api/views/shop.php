<!DOCTYPE html>
<html lang="fr">
    <head>
        <?php include("includes/headInfo.php"); ?>
    </head>
    <body style="display: none;">
        <header>
            <?php
            $page_name = "shop";
            include("includes/header.php");
            ?>
        </header>

        <main>
            <div class="separator-l"></div>

            <div class="container">
                <div class="row">
                    <p class="title">Catalogue des prestations</p>
                    <div class="separator-s"></div>
                    <img src="/assets/img/shop.svg" alt="Catalogue des prestations" height="256px" />
                </div>

                <div class="separator-m"></div>

                <div class="row">
                    <div class="shop-sort">
                        <p>Trier par :</p>
                        <select class="shop-select" id="sort-select">
                            <option value="chrono-d">Ordre chronologique (décroissant)</option>
                            <option value="chrono-c">Ordre chronologique (croissant)</option>
                            <option value="alpha-c">Ordre alphabétique (croissant)</option>
                            <option value="alpha-d">Ordre alphabétique (décroissant)</option>
                        </select>
                        <button class="button button-red" id="sort-button">OK</button>

                        <p>Filtrer par partenaire :</p>
                        <select class="shop-select" id="filter-select">
                            <option value="null">Tous</option>
                        </select>
                        <button class="button button-red" id="filter-button">OK</button>
                    </div>
                    
                    <p class="error" id="shop-error"></p>

                    <?php for($i=0; $i<4; $i++) {
                        echo '
                        <div class="col shop-item" id="shop-item' . $i . '"><div class="shop-insideitem">
                            <p class="shop-item-name shop-item-height" id="shop-item-name' . $i . '">Nom de la prestation</p>
                            <p class="shop-item-description shop-item-height" id="shop-item-description' . $i . '">Ce paragraphe sert de description à la prestation.</p>
                            <div class="shop-item-numbers">
                                <p class="shop-item-price" id="shop-item-price' . $i . '">0.0€</p>
                                <p class="shop-item-price shop-item-id" id="shop-item-id' . $i . '">-1</p>
                            </div>
                            <button class="button button-red shop-btn" id="shop-btn' . $i . '">Ajouter au panier</button>
                        </div></div>';}

                    echo '</div><div class="row">';
                    
                    for($i=4; $i<8; $i++) {
                        echo '
                        <div class="col shop-item" id="shop-item' . $i . '"><div class="shop-insideitem">
                            <p class="shop-item-name" id="shop-item-name' . $i . '">Nom de la prestation</p>
                            <p class="shop-item-description" id="shop-item-description' . $i . '">Ce paragraphe sert de description à la prestation.</p>
                            <div class="shop-item-numbers">
                                <p class="shop-item-price" id="shop-item-price' . $i . '">0.0€</p>
                                <p class="shop-item-price shop-item-id" id="shop-item-id' . $i . '">-1</p>
                            </div>
                            <button class="button button-red shop-btn" id="shop-btn' . $i . '">Ajouter au panier</button>
                        </div></div>';}

                    echo '</div>';
                    ?>
                </div>
            </div>

            <div class="separator-l"></div>
        </main>

        <footer>
            <?php include("includes/footer.php"); ?>
        </footer>
        <script type="module" src="/scripts/shop.js"></script>
    </body>
</html>