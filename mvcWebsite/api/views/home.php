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
            <div class="page-title-div">
                <h1 class="page-title">Le site est en <span style="color: #FE4A49;">construction</span>.<br>
                Il sera <span style="color: #009FB7;">bientôt prêt</span> à vous accueillir.</h1><br><br>
                <p>Fièrement propulsé par</p><img src="/assets/logo/logo_right.png" width="220px">
            </div>
        </main>

        <footer>
            <?php include("includes/footer.php"); ?>
        </footer>
    </body>
</html>