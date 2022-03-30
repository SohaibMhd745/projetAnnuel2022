<?php

    include("include_scripts.php");

    try{
        $dblink = new DbLink(HOST, CHARSET, DB, USER, PASS);
    }catch(Exception $e){
        header("Location: /account.php?errorMsg=database&errorType=2");
        exit();
    }
    $user = new User();
    try{
        $user->constructFromEmailAndPassword($_POST['email'], $_POST['password'], $dblink);
    }catch (Exception $e){
        switch($e->getCode()){
            case INVALID_PARAMETER : redirectFormError("invalid_parameter", "account.php", "Un des paramètres entrés de correspond pas aux contraintes", 2); break;
            case INCORRECT_USER_CREDENTIALS : redirectFormError("incorrect_credentials", "account.php", "Vérifiez votre mot de passe", 2); break;
            case MYSQL_EXCEPTION : redirectFormError("database", "account.php", "Impossible d'accéder à la base de donnée, veuillez réessayer plus tard", 2); break;
            default : redirectFormError("fatal", "account.php", "Veuillez réessayer plus tard", 2); break;
        }
    }

    try {
        connectUser($user, $dblink);
    } catch (Exception $e) {
        redirectFormError("fatal", "account.php", "Veuillez réessayer plus tard", 2);
    }

?>