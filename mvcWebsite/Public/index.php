<?php

include __DIR__."/../api/scripts/includes/const.php";
include __DIR__."/../api/scripts/includes/preparePassword.php";
include __DIR__."/../api/scripts/includes/sanitizeStringQuotes.php";
include __DIR__."/../api/scripts/includes/date.php";
include __DIR__."/../api/scripts/includes/formatResponse.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);


$route = $_REQUEST["route"] ?? "";

$method = $_SERVER["REQUEST_METHOD"];

/**
 *
 * get --> obtenir l'info, requête classique en web
 * post --> créer l'info
 * put --> remplacer l'info
 * delete --> suprimmer l'info
 * patch --> modifier l'info
 */

if ($route !== ""){
    $controller = explode('/', $route)[0];
    $action = explode('/', $route)[1] ?? "index";

    //Switch based on controller
    switch ($controller){
        case "account":
            include __DIR__ . "/../api/controllers/Account.php";
            Account::view();
            break;
        case "login":
            switch ($method){
                case "POST":
                    switch ($action){
                        case "signup":
                            include __DIR__ . "/../api/controllers/Login.php";
                            Login::signup();
                            break;
                        case "registercompany":
                            include __DIR__ . "/../api/controllers/Login.php";
                            Login::registercompany();
                            break;
                        default:
                            echo formatResponse(400, ["Content-Type" => "application/json"],
                                ["success" => false, "errorMessage" => "", "errorCode" => WRONG_ACTION]);
                            die();
                    }
                    break;
                case "GET":
                    include __DIR__ . "/../api/controllers/Login.php";
                    Login::signin();
                    break;
                default:
                    echo formatResponse(400, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "", "errorCode" => WRONG_METHOD]);
                    die();
            }

            break;

        case "shop":
            include __DIR__ . "/../api/controllers/Shop.php";
            Shop::view();
            break;

        default:
            //TODO: Page 404
            include __DIR__ . "/../api/controllers/Home.php";
            Home::view();
            break;
    }
}else{
    include __DIR__ . "/../api/controllers/Home.php";
    Home::view();
}
