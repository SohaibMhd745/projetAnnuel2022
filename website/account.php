<?php
    session_start();

    include ("scripts/include_scripts.php");
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
            extract(array('script_name'=> $_SERVER['SCRIPT_NAME']));
            include("includes/header.php");
            ?>
        </header>

        <?php if(!isset($_SESSION['user'])) : ?>
            <main>
                <div class="global-separator"></div>

                <div class="container account-container">
                    <div class="row">
                        <p class="page-title">Espace Compte</p>
                        <img src="/assets/img/ill/account.svg" alt="Espace Compte" height="256px" />
                        <div class="global-separator"></div>
                        
                        <div class="col account-col">
                            <p class="account-title">Ouvrir un compte</p>
                            <form class="account-form" action="scripts/verif_signup.php" method="post">
                                <input class="account-input" name="lastname" type="text" placeholder="Nom" minlength="2" maxlength="30" required>
                                <input class="account-input" name="firstname" type="text" placeholder="Prénom" minlength="2" maxlength="30" required>
                                <label class="account-label">Date de naissance</label>
                                <input class="account-input" name="birthdate" type="date" min="1900-01-01" max="<?php echo getYearsAgo(18); ?>" required>
                                <input class="account-input" name="phone" type="text" placeholder="Téléphone" minlength="10" maxlength="10" required>
                                <input class="account-input" name="email" type="email" placeholder="E-mail" maxlength="30" required>
                                <input class="account-input" name="password" type="password" placeholder="Mot de passe" minlength="8" maxlength="30" required>
                                <input class="account-button account-btnform" type="submit" value="Valider">
                                <?php
                                    if(isset($_GET['errorMsg']) && !empty($_GET['errorMsg'])
                                        && isset($_GET['errorType']) && !empty($_GET['errorType'])
                                        && $_GET['errorSlot'] == 1) {
                                            switch($_GET['errorType']) {
                                                case 'lastname' : $inputName = 'Nom'; break;
                                                case 'firstname' : $inputName = 'Prénom'; break;
                                                case 'birthdate' : $inputName = 'Date de naissance'; break;
                                                case 'phone' : $inputName = 'Téléphone'; break;
                                                case 'email' : $inputName = 'E-mail'; break;
                                                case 'password' : $inputName = 'Mot de passe'; break;
                                                case 'database' : $inputName = 'Base de donnée'; break;
                                                default : $inputName = 'Indéfini'; break;
                                            }
                                            echo '<p class="global-error">' . $inputName . ' : ' . $_GET['errorMsg'] . '.</p>';
                                    }
                                ?>
                            </form>
                        </div>
                        
                        <div class="col-1 account-col">
                            <div class="global-vl"></div>
                        </div>
                        
                        <?php $loginString = isset($_COOKIE['email']) ? " value=\"" . $_COOKIE['email'] . "\"" : ""; ?>
                        <div class="col account-col">
                            <p class="account-title">Se connecter</p>
                            <form class="account-form" action="scripts/verif_signin.php" method="post">
                                <input class="account-input" name="email" type="email" placeholder="E-mail"<?php echo $loginString ?> maxlength="30" required>
                                <input class="account-input" name="password" type="password" placeholder="Mot de passe" minlength="8" maxlength="30" required>
                                <input class="account-button account-btnform" type="submit" value="Valider">
                                <?php
                                    if(isset($_GET['errorMsg']) && !empty($_GET['errorMsg'])
                                        && isset($_GET['errorType']) && !empty($_GET['errorType'])
                                        && $_GET['errorSlot'] == 2) {
                                            switch($_GET['errorType']) {
                                                case 'fatal' : $inputName = 'Erreur Fatale'; break;
                                                case 'invalid_param' : $inputName = 'Paramètre Invalide'; break;
                                                case 'incorrect_credentials' : $inputName = 'Connexion refusée'; break;
                                                case 'database' : $inputName = 'Base de donnée'; break;
                                                default : $inputName = 'Indéfini'; break;
                                            }
                                            echo '<p class="global-error">' . $inputName . ' : ' . $_GET['errorMsg'] . '.</p>';
                                        }
                                ?>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="global-separator"></div>
            </main>

        <?php else : ?>
            <main>
                <p>Connecté</p>
                <a href="/scripts/signout.php">Déconnexion</a>
            </main>
        <?php endif; ?>

        <footer>
            <?php include("includes/footer.php"); ?>
        </footer>
    </body>
</html>