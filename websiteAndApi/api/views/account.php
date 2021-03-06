<!DOCTYPE html>
<html lang="fr">
    <head>
        <?php include("includes/headInfo.php"); ?>
        <script type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    </head>
    <body>
        <header>
            <?php
            $page_name = "account";
            include("includes/header.php");
            ?>
        </header>

        <main id="main-unsigned">
            <div class="separator-l"></div>

            <div class="container">
                <div class="row">
                    <p class="title">Accès Espace Compte</p>
                    <div class="separator-s"></div>
                    <img src="/assets/img/account.svg" alt="Espace Compte" height="256px" />

                    <div class="separator-m"></div>
                    
                    <div class="col col-centered">
                        <p class="subtitle">Ouvrir un compte</p>
                        <div id="signup-form" class="form">
                            <input class="input input-red" id="signup-lastname" type="text" placeholder="Nom">
                            <input class="input input-red" id="signup-firstname" type="text" placeholder="Prénom">
                            <label class="label">Date de naissance</label>
                            <input class="input input-red" id="signup-birthdate" type="date" min="<?php echo DEFAULT_DATE_MIN; ?>" max="<?php echo getYearsAgo(18); ?>">
                            <input class="input input-red" id="signup-number" type="text" placeholder="Téléphone">
                            <input class="input input-red" id="signup-email" type="email" placeholder="E-mail">
                            <input class="input input-red" id="signup-password" type="password" placeholder="Mot de passe">
                            <button class="button button-red" id="signup-submit" type="submit">Valider</button>
                            <p class="error" id="signup-error"></p>
                        </div>
                    </div>
                    
                    <div class="col-1 col-centered">
                        <div class="vl"></div>
                    </div>
                    
                    <div class="col col-centered">
                        <p class="subtitle">Se connecter</p>
                        <div id="signin-form" class="form">
                            <input class="input input-red" id="signin-email" type="email" placeholder="E-mail">
                            <input class="input input-red" id="signin-password" type="password" placeholder="Mot de passe">
                            <button class="button button-red" id="signin-submit" type="submit">Valider</button>
                            <p class="error" id="signin-error"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="separator-l"></div>
        </main>

        <main id="main-signed">
            <div class="separator-l"></div>

            <div class="container" id="subview-account">
                <div class="row">
                    <p class="title">Votre Espace Compte</p>
                    <img src="/assets/img/account.svg" alt="Espace Compte" height="256px" />
                    <div class="separator-m"></div>
                    
                    <div class="col col-centered">
                        <p class="subtitle">Mes coordonnées</p>
                        <div id="profile-form" class="form">
                            <input class="input input-blue" id="profile-lastname" type="text" placeholder="Nom">
                            <input class="input input-blue" id="profile-firstname" type="text" placeholder="Prénom">
                            <label class="label">Date de naissance</label>
                            <input class="input input-blue" id="profile-birthdate" type="date" min="<?php echo DEFAULT_DATE_MIN; ?>" max="<?php echo getYearsAgo(18); ?>">
                            <input class="input input-blue" id="profile-number" type="text" placeholder="Téléphone">
                            <button class="button button-blue" id="profile-submit" type="submit">Modifier</button>
                            <p class="error" id="profile-error"></p>
                        </div>
                    </div>
                    
                    <div class="col-1 col-centered">
                        <div class="vl"></div>
                    </div>
                    
                    <div class="col col-centered">
                        <div id="upgrade-section" class="form">
                            <p class="subtitle">Devenir partenaire</p>
                            <input class="input input-gold" id="upgrade-name" type="text" placeholder="Nom d'entreprise" minlength="2" maxlength="30" required>
                            <input class="input input-gold" id="upgrade-revenue" type="text" placeholder="Chiffre d'affaires" minlength="1" maxlength="20" required>
                            <input class="input input-gold" id="upgrade-website" type="text" placeholder="Site Internet" minlength="3" maxlength="30" required>
                            <input class="input input-gold" id="upgrade-sponsorcode" type="text" placeholder="Code de parrainage (optionnel)" minlength="10" maxlength="10">
                            <button class="button button-gold" id="upgrade-submit" type="submit">Valider</button>
                            <p class="error" id="upgrade-error"></p>
                        </div>
                        <div id="partner-section" class="form">
                            <p class="subtitle">Espace partenaire</p>
                            <button class="button button-gold" id="partner-1" type="submit">Gérer mes prestations</button>
                            <button class="button button-gold" id="partner-2" type="submit">Ajouter une prestation</button>
                        </div>

                        <div class="separator-m"></div>

                        <p class="subtitle">Code barre</p>
                        <svg class="barcode" jsbarcode-format="EAN13"></svg>
                    </div>
                </div>

                <div class="separator-xl"></div>

                <div class="row">
                    <div class="col col-centered">
                        <div class="form">
                            <p class="subtitle">Fermer le compte</p>
                            <button class="button button-red" id="delete-account" type="submit">Fermeture</button>
                        </div>
                    </div>
                    
                    <div class="col-1 col-centered">
                        <div class="vl"></div>
                    </div>
                    
                    <div class="col col-centered">
                        <div class="form">
                            <p class="subtitle">Se déconnecter</p>
                            <button class="button button-red" id="signout" type="submit">Déconnexion</button>
                        </div>
                    </div>
                </div>

                <button class="button button-gold" id="download-user-pdf">Télécharger ma fiche au format PDF</button>
            </div>

            <div class="container" id="subview-addpresta">
                <div class="row">
                    <p class="title">Ajouter une prestation</p>
                    <img src="/assets/img/addpresta.svg" alt="Ajouter une prestation" height="256px" />
                    <div class="separator-m"></div>

                    <div class="col col-centered">
                        <div class="form">
                            <input class="input input-blue" id="addpresta-name" type="text" placeholder="Nom de la prestation">
                            <input class="input input-blue" id="addpresta-description" type="textarea" placeholder="Description de la la prestation">
                            <input class="input input-blue" id="addpresta-price" type="number" placeholder="Prix de la prestation">
                            <button class="button button-blue" id="addpresta-submit" type="submit">Ajouter</button>
                            <p class="error" id="addpresta-error"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="separator-l"></div>

            <div id="user-invoice">
                <br>
                <div style="display: flex; align-items: center; justify-content: center;">
                    <h3>AKM Gestion - Carte utilisateur</h3>
                    <img src="/assets/logo/logo_right.png" width="180px">
                </div>
                <div style="display: flex; align-items: center; justify-content: center;">
                    <h5 style="font-weight: bold; text-decoration: underline;">Fiche utilisateur</h5>
                </div>
                <p id="pdf-lastname">Nom : </p>
                <p id="pdf-firstname">Prénom : </p>
                <p id="pdf-birthdate">Date de naissance : </p>
                <p id="pdf-number">Téléphone : </p>
                <?php for($i=0; $i<20; $i++){ echo '<p class="invisible">.</p>'; } ?>
            </div>
        </main>

        <footer>
            <?php include("includes/footer.php"); ?>
        </footer>
    <script type="module" src="/scripts/account.js"></script>
    <script type="application/javascript" src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/barcodes/JsBarcode.ean-upc.min.js"></script>
    </body>
</html>