<!DOCTYPE html>
<html lang="fr">
    <head>
        <?php include("includes/headInfo.php"); ?> <!-- TODO: Passer le script_name pour afficher le bon <title> -->
    </head>
    <body>
        <header>
            <?php
            $script_name = "/account.php";
            include("includes/header.php");
            ?>
        </header>
            <main id="main-unsigned">
                <div class="global-separator"></div>

                <div class="container account-container">
                    <div class="row">
                        <p class="page-title">Accès Espace Compte</p>
                        <img src="/assets/img/ill/account.svg" alt="Espace Compte" height="256px" />
                        <div class="global-separator"></div>
                        
                        <div class="col account-col">
                            <p class="account-title">Ouvrir un compte</p>
                            <div id="signup-form" class="account-form">
                                <input class="account-input account-input-red" name="lastname" type="text" placeholder="Nom" minlength="2" maxlength="30" required>
                                <input class="account-input account-input-red" name="firstname" type="text" placeholder="Prénom" minlength="2" maxlength="30" required>
                                <label class="account-label">Date de naissance</label>
                                <input class="account-input account-input-red" name="birthdate" type="date" min="<?php echo DEFAULT_DATE_MIN; ?>" max="<?php echo getYearsAgo(18); ?>" required>
                                <input class="account-input account-input-red" name="phone" type="text" placeholder="Téléphone" minlength="10" maxlength="10" required>
                                <input class="account-input account-input-red" name="email" type="email" placeholder="E-mail" maxlength="30" required>
                                <input class="account-input account-input-red" name="password" type="password" placeholder="Mot de passe" minlength="8" maxlength="30" required>
                                <button class="account-btn account-btn-red" type="submit">Valider</button>
                                <p class="global-error" id="signup-error"></p>
                            </div>
                        </div>
                        
                        <div class="col-1 account-col">
                            <div class="global-vl"></div>
                        </div>
                        
                        <div class="col account-col">
                            <p class="account-title">Se connecter</p>
                            <div id="signin-form" class="account-form">
                                <input class="account-input account-input-red" id="signin-email" type="email" placeholder="E-mail" maxlength="30" required>
                                <input class="account-input account-input-red" id="signin-password" type="password" placeholder="Mot de passe" minlength="8" maxlength="30" required>
                                <button class="account-btn account-btn-red" id="signin-submit" type="submit">Valider</button>
                                <p class="global-error" id="signin-error"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="global-separator"></div>
            </main>

            <main id="main-signed">
                <div class="global-separator"></div>

                <div class="container account-container">
                    <div class="row">
                        <p class="page-title">Votre Espace Compte</p>
                        <img src="/assets/img/ill/account.svg" alt="Espace Compte" height="256px" />
                        <div class="global-separator"></div>
                        
                        <div class="col account-col">
                            <!-- TODO: Infos du compte (modifiables dans le form) PRIORITE BASSE -->
                            <div></div>
                        </div>
                        
                        <div class="col-1 account-col">
                            <div class="global-vl"></div>
                        </div>
                        
                        <div class="col account-col">
                            <!-- TODO: Devenir partenaire (form) PRIORITE ELEVEE, supprimer le compte PRIORITE BASSE -->
                            <p class="account-title">Devenir partenaire</p>
                            <div id="upgrade-form" class="account-form">
                                <input class="account-input account-input-gold" id="upgrade-partnername" type="text" placeholder="Nom d'entreprise" minlength="2" maxlength="30" required>
                                <input class="account-input account-input-gold" id="upgrade-revenue" type="text" placeholder="Chiffre d'affaires" minlength="1" maxlength="20" required>
                                <input class="account-input account-input-gold" id="upgrade-website" type="text" placeholder="Site Internet" minlength="3" maxlength="30" required>
                                <input class="account-input account-input-gold" id="upgrade-sponsorcode" type="text" placeholder="Code de parrainage (optionnel)" minlength="10" maxlength="10">
                                <button class="account-btn account-btn-gold" id="upgrade-submit" type="submit">Valider</button>
                                <p class="global-error" id="upgrade-error"></p>
                            </div>
                        </div>
                    </div>

                    <div class="global-separator-xl"></div>

                    <div class="row">
                        <div class="col account-col"></div>
                        
                        <div class="col-1 account-col">
                            <div class="global-vl"></div>
                        </div>
                        
                        <div class="col account-col">
                            <div class="account-form">
                                <p class="account-title">Déconnexion</p>
                                <button class="account-btn account-btn-red" id="signout" type="submit">Déconnexion</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="global-separator"></div>
            </main>

        <footer>
            <?php include("includes/footer.php"); ?>
        </footer>
    <script type="module" src="/scripts/account.js"></script>
    </body>
</html>