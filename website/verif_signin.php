<?php

    include("scripts/include_scripts.php");

    try{
        $dblink = new DbLink("localhost", "utf8", "lc_website", "test2", "akmtest");
    }catch(Exception $e){
        header("Location: /account.php?error=" . $e->getMessage());
        exit();
    }
    echo "try1";
    echo $_POST['id'];
    echo $_POST['password'];

    try{
        $user = new User();
        $user->constructFromEmailAndPassword($_POST['id'], $_POST['password'], $dblink);
        echo "try2";
    }catch (Exception $e){
        switch($e->getCode()){
            case INVALID_PARAMETER : header("Location: /account.php?error=invalid_param"); exit();
                break;
            case INCORRECT_USER_CREDENTIALS : header("Location: /account.php?error=incorrect_cred"); exit();
                break;
            default : header("Location: /account.php?error=undefined"); exit();
                break;
        }
    }
    header("Location: /"); exit();

?>