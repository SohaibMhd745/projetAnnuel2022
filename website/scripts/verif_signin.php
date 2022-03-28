<?php

    include("include_scripts.php");

    try{
        $dblink = new DbLink("localhost", "utf8", "lc_website", "akmadmin", "akmgestion123");
    }catch(Exception $e){
        header("Location: /account.php?errorMsg=database&errorType=2");
        exit();
    }

    try{
        $user = new User();
        $user->constructFromEmailAndPassword($_POST['email'], $_POST['password'], $dblink);
    }catch (Exception $e){
        switch($e->getCode()){
            case INVALID_PARAMETER : header("Location: /account.php?errorMsg=invalid_param&errorType=2"); exit();
                break;
            case INCORRECT_USER_CREDENTIALS : header("Location: /account.php?errorMsg=incorrect_cred&errorType=2"); exit();
                break;
            case MYSQL_EXCEPTION : header("Location: /account.php?errorMsg=database&errorType=2"); exit();
                break;
            default : header("Location: /account.php?errorMsg=fatal&errorType=2"); exit();
                break;
        }
    }
    $dblink->connectUser($_POST['email']);

?>