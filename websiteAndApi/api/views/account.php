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
            <div class="separator-l"></div>

            <div class="container">
                <div class="row">
                    <p class="title">Accès Espace Compte</p>
                    <div class="separator-s"></div>
                    <img src="/assets/img/ill/account.svg" alt="Espace Compte" height="256px" />

                    <div class="separator-m"></div>
                    
                    <div class="col col-centered">
                        <p class="subtitle">Ouvrir un compte</p>
                        <div id="signup-form" class="form">
                            <input class="input input-red" name="lastname" type="text" placeholder="Nom" minlength="2" maxlength="30" required>
                            <input class="input input-red" name="firstname" type="text" placeholder="Prénom" minlength="2" maxlength="30" required>
                            <label class="label">Date de naissance</label>
                            <input class="input input-red" name="birthdate" type="date" min="<?php echo DEFAULT_DATE_MIN; ?>" max="<?php echo getYearsAgo(18); ?>" required>
                            <input class="input input-red" name="phone" type="text" placeholder="Téléphone" minlength="10" maxlength="10" required>
                            <input class="input input-red" name="email" type="email" placeholder="E-mail" maxlength="30" required>
                            <input class="input input-red" name="password" type="password" placeholder="Mot de passe" minlength="8" maxlength="30" required>
                            <button class="button button-red" type="submit">Valider</button>
                            <p class="error" id="signup-error"></p>
                        </div>
                    </div>
                    
                    <div class="col-1 col-centered">
                        <div class="vl"></div>
                    </div>
                    
                    <div class="col col-centered">
                        <p class="subtitle">Se connecter</p>
                        <div id="signin-form" class="form">
                            <input class="input input-red" id="signin-email" type="email" placeholder="E-mail" maxlength="30" required>
                            <input class="input input-red" id="signin-password" type="password" placeholder="Mot de passe" minlength="8" maxlength="30" required>
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

            <div class="container container">
                <div class="row">
                    <p class="title">Votre Espace Compte</p>
                    <img src="/assets/img/ill/account.svg" alt="Espace Compte" height="256px" />
                    <div class="separator-m"></div>
                    
                    <div class="col col-centered">
                        <!-- TODO: Infos du compte (modifiables dans le form) PRIORITE BASSE -->
                        <div></div>
                    </div>
                    
                    <div class="col-1 col-centered">
                        <div class="vl"></div>
                    </div>
                    
                    <div class="col col-centered">
                        <!-- TODO: Devenir partenaire (form) PRIORITE ELEVEE, supprimer le compte PRIORITE BASSE -->
                        <p class="subtitle">Devenir partenaire</p>
                        <div id="upgrade-form" class="form">
                            <input class="input input-gold" id="upgrade-partnername" type="text" placeholder="Nom d'entreprise" minlength="2" maxlength="30" required>
                            <input class="input input-gold" id="upgrade-revenue" type="text" placeholder="Chiffre d'affaires" minlength="1" maxlength="20" required>
                            <input class="input input-gold" id="upgrade-website" type="text" placeholder="Site Internet" minlength="3" maxlength="30" required>
                            <input class="input input-gold" id="upgrade-sponsorcode" type="text" placeholder="Code de parrainage (optionnel)" minlength="10" maxlength="10">
                            <button class="button button-gold" id="upgrade-submit" type="submit">Valider</button>
                            <p class="error" id="upgrade-error"></p>
                        </div>
                    </div>
                </div>

                <div class="separator-xl"></div>

                <div class="row">
                    <div class="col col-centered"></div>
                    
                    <div class="col-1 col-centered">
                        <div class="vl"></div>
                    </div>
                    
                    <div class="col col-centered">
                        <div class="form">
                            <p class="subtitle">Déconnexion</p>
                            <button class="button button-red" id="signout" type="submit">Déconnexion</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="separator-l"></div>
        </main>

        <footer>
            <?php include("includes/footer.php"); ?>
        </footer>
    <script type="module" src="/scripts/account.js"></script>
    </body>
</html>