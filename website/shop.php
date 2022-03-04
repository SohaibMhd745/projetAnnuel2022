<?php session_start(); ?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <!-- Informations -->
        <meta charset="utf-8" />
        <meta name="description" content="Loyalty Card, le service de fidélité numéro 1 en France." />
        <meta name="author" content="AKM Gestion" />
        <title>Boutique - Loyalty Card</title>
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
        </main>

        <footer>
            <?php include("includes/footer.php"); ?>
        </footer>
    </body>
</html>