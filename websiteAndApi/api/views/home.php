<?php session_start(); ?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <?php include("includes/headInfo.php"); ?>
    </head>
    <body>
        <header>
            <?php
            $page_name = "home";
            include("includes/header.php");
            ?>
        </header>

        <main>
            <div class="home-title-div">
                <h1 class="title">Le site est en <span style="color: #FE4A49;">construction</span>.<br>
                Il sera <span style="color: #009FB7;">bientôt prêt</span> à vous accueillir.</h1><br><br>
                <p>Fièrement propulsé par</p><img src="/assets/logo/logo_right.png" width="220px">
            </div>
        </main>

        <footer>
            <?php include("includes/footer.php"); ?>
        </footer>
    </body>
</html>