<?php session_start(); ?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <!-- Informations -->
        <meta charset="utf-8" />
        <meta name="description" content="Loyalty Card, le service de fidélité numéro 1 en France." />
        <meta name="author" content="AKM Gestion" />
        <title>Accueil - Loyalty Card</title>
        <!-- Icône du site -->
        <link rel="icon" type="image/x-icon" href="assets/logo.ico" />
        <!-- CSS -->
        <link href="css/bootstrap.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
    </head>
    <body>
        <!-- Header -->
        <?php
        function includeFileWithVariables($fileName, $variables) {
            extract($variables);
            include($fileName);
        }
        includeFileWithVariables("includes/header.php", array(
            'script_name'=> $_SERVER['SCRIPT_NAME']
        ));
        ?>

        <div class="page-title-div">
            <h1 class="page-title">Le site est en <span style="color: #FE4A49;">construction</span>.<br>
            Il sera <span style="color: #009FB7;">bientôt prêt</span> à vous accueillir.</h1><br><br>
            <p>Fièrement propulsé par</p><img src="assets/logo/logo_right.png" width="220px">
        </div>
        

        <!-- Séparateur -->
        <div class="global-separator"></div>

        <!-- Footer -->
        <?php include("includes/footer.php"); ?>
    </body>
</html>