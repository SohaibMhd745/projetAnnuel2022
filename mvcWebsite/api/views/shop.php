<?php session_start(); ?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <?php include("includes/headInfo.php"); ?>
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