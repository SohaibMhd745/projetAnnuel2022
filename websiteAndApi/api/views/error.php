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

        <?php switch($errorCode) {
            case 400: $errorMessage = "Requête HTML invalide."; break;
            case 401: $errorMessage = "Authentification invalide."; break;
            case 404: $errorMessage = "La page que vous recherchez n'existe pas."; break;
            case 500: $errorMessage = "Une erreur interne liée au serveur a eu lieu."; break;
            default: header("Location: /error/404"); exit(); break;
        }?>

        <main id="main-unsigned">
            <div class="separator-l"></div>

            <div class="container">
                <div class="row">
                    <p class="title">Erreur <?php echo $errorCode; ?></p>
                    <div class="separator-s"></div>
                    <img src="/assets/img/error.svg" alt="Espace Compte" height="256px" /><br><br><br><br><br><br>
                    <p style="text-align: center; margin-top: 48px;"><?php echo $errorMessage; ?></p>
                </div>
            </div>

            <div class="separator-l"></div>
        </main>

        <footer>
            <?php include("includes/footer.php"); ?>
        </footer>
    </body>
</html>