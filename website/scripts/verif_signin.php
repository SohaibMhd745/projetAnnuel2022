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

    session_start();
    $_SESSION['user'] = $user;
    header("Location: / ");
    exit();

?>