<?php
    session_start();

    /**
     * Gets the date of n years ago
     * @param int $yearsAgo : input number of years from today's date
     * @return string : date of n years ago YYYY-mm-dd
     */
    function getYearsAgo(int $yearsAgo){
        $dayMonth = date("m-d");
        $year = date("Y")-$yearsAgo;
        $string = $year . '-' . $dayMonth;
        return $string;
    }
?>

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
                        <form class="account-form" action="verif_signup.php" method="post">
                            <input class="account-input" name="lastname" type="text" placeholder="Nom" required>
                            <input class="account-input" name="firstname" type="text" placeholder="Prénom" required>
                            <label class="account-label">Date de naissance</label>
                            <input class="account-input" name="birthdate" type="date" min="1900-01-01" max="<?php echo getYearsAgo(18); ?>" required>
                            <input class="account-input" name="phone" type="number" placeholder="Téléphone" required minlength="10" maxlength="10">
                            <input class="account-input" name="email" type="email" placeholder="E-mail" required>
                            <input class="account-input" name="password" type="password" placeholder="Mot de passe" required>
                            <input class="account-button account-btnform" type="submit" value="Valider">
                        </form>
                    </div>
                    
                    <div class="col-1 account-col">
                        <div class="global-vl"></div>
                    </div>
                    
                    <?php $loginString = isset($_SESSION['email']) ? " value=\"" . $_SESSION['email'] . "\"" : ""; ?>
                    <div class="col account-col">
                        <p class="account-title">Se connecter</p>
                        <form class="account-form" action="verif_signin.php" method="post">
                            <input class="account-input" name="id" type="text" placeholder="Nom d'utilisateur / e-mail"<?php echo $loginString ?> required>
                            <input class="account-input" name="password" type="password" placeholder="Mot de passe" required>
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