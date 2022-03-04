<?php session_start(); ?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <!-- Informations -->
        <meta charset="utf-8" />
        <meta name="description" content="Loyalty Card, le service de fidélité numéro 1 en France." />
        <meta name="author" content="AKM Gestion" />
        <title>Espace compte - Loyalty Card</title>
        <!-- Icône du site -->
        <link rel="icon" type="image/x-icon" href="assets/logo.ico" />
        <!-- CSS -->
        <link href="css/bootstrap.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
    </head>
    <body>
        <header>
            <?php
            function includeFileWithVariables($fileName, $variables) {
                extract($variables);
                include($fileName);
            }
            includeFileWithVariables("includes/header.php", array(
                'script_name'=> $_SERVER['SCRIPT_NAME']
            ));
            ?>
        </header>

        <main>
            <div class="global-separator"></div>

            <div class="container account-container">
                <div class="row">
                    <p class="page-title">Espace Compte</p>
                    <div class="global-separator"></div>
                    
                    <div class="col account-col">
                        <p class="account-title">Ouvrir un compte</p>
                        <button class="account-button account-btnclient" onclick="location.href='signup.php?type=1'">Compte client</button>
                        <button class="account-button account-btnentreprise" onclick="location.href='signup.php?type=2'">Compte entreprise</button>
                    </div>
                    
                    <div class="col-1 account-col">
                        <div class="global-vl"></div>
                    </div>

                    <div class="col account-col">
                        <p class="account-title">Se connecter</p>
                        <form class="account-form" action="verif_signin.php" method="post">
                            <input class="account-input" name="id" type="text" placeholder="Nom d'utilisateur / e-mail">
                            <input class="account-input" name="password" type="password" placeholder="Mot de passe">
                            <input class="account-button account-btnform" type="submit" value="Valider">
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