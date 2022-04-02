<!DOCTYPE html>
<html lang="fr">
    <head>
        <?php include("includes/headInfo.php"); ?>
    </head>
    <body>
        <header>
            <?php
            $script_name = "/account.php";
            include("includes/header.php");
            ?>
        </header>
            <main>
                <div class="global-separator"></div>

                <div class="container account-container">
                    <div class="row">
                        <p class="page-title">Espace Compte</p>
                        <img src="/assets/img/ill/account.svg" alt="Espace Compte" height="256px" />
                        <div class="global-separator"></div>
                        
                        <div class="col account-col">
                            <p class="account-title">Ouvrir un compte</p>
                            <!-- TODO: MVC -->
                            <form id="signup-form" class="account-form">
                                <input class="account-input" name="lastname" type="text" placeholder="Nom" minlength="2" maxlength="30" required>
                                <input class="account-input" name="firstname" type="text" placeholder="Prénom" minlength="2" maxlength="30" required>
                                <label class="account-label">Date de naissance</label>
                                <input class="account-input" name="birthdate" type="date" min="<?php echo DEFAULT_DATE_MIN; ?>" max="<?php echo getYearsAgo(18); ?>" required>
                                <input class="account-input" name="phone" type="text" placeholder="Téléphone" minlength="10" maxlength="10" required>
                                <input class="account-input" name="email" type="email" placeholder="E-mail" maxlength="30" required>
                                <input class="account-input" name="password" type="password" placeholder="Mot de passe" minlength="8" maxlength="30" required>
                                <input class="account-button account-btnform" type="submit" value="Valider">
                                <!-- TODO: Div D'erreur -->
                            </form>
                        </div>
                        
                        <div class="col-1 account-col">
                            <div class="global-vl"></div>
                        </div>
                        
                        <?php $loginString = isset($_COOKIE['email']) ? " value=\"" . $_COOKIE['email'] . "\"" : ""; ?>
                        <div class="col account-col">
                            <p class="account-title">Se connecter</p>
                            <form id="signin-form" class="account-form">
                                <input class="account-input" id="signin-email" name="email" type="email" placeholder="E-mail"<?php echo $loginString ?> maxlength="30" required>
                                <input class="account-input" id="signin-password" name="password" type="password" placeholder="Mot de passe" minlength="8" maxlength="30" required>
                                <input class="account-button account-btnform" id="signin-submit" type="submit" value="Valider">
                                <!-- TODO: Div D'erreur -->
                            </form>
                        </div>
                    </div>
                </div>

                <div class="global-separator"></div>
            </main>

        <footer>
            <?php include("includes/footer.php"); ?>
        </footer>
    </body>
</html>