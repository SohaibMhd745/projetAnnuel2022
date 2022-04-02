<?php

include __DIR__ . "/../api/scripts/const.php";
include __DIR__."/../api/database/CREDENTIALS.php";
include __DIR__."/../api/database/DbLink.php";
include __DIR__ . "/../api/scripts/preparePassword.php";
include __DIR__ . "/../api/scripts/sanitizeStringQuotes.php";
include __DIR__ . "/../api/scripts/date.php";
include __DIR__ . "/../api/scripts/formatResponse.php";
include __DIR__ . "/../api/scripts/generateRandomString.php";
include __DIR__ . "/../api/scripts/reportErrors.php";

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
        case "session":
            include __DIR__."/../api/controllers/Session.php";
            Session::createSession($action);
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
                        case "signin":
                            include __DIR__ . "/../api/controllers/Login.php";
                            Login::signin();
                            break;
                        case "getdata":
                            include __DIR__ . "/../api/controllers/Login.php";
                            Login::getdata();
                            break;
                        case "getallcompanies":
                            include __DIR__ . "/../api/controllers/Login.php";
                            Login::getAllCompanies();
                            break;
                        default:
                            echo formatResponse(400, ["Content-Type" => "application/json"],
                                ["success" => false, "errorMessage" => "", "errorCode" => WRONG_ACTION]);
                            die();
                    }
                    break;
                default:
                    echo formatResponse(400, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "", "errorCode" => WRONG_METHOD]);
                    die();
            }

            break;

        case "catalog":
            switch ($method){
                case "POST":
                    switch ($action){
                        case "addarticle":
                            include __DIR__."/../api/controllers/CatalogController.php";
                            CatalogController::addArticle();
                            break;
                        case "getallarticles":
                            include __DIR__."/../api/controllers/CatalogController.php";
                            CatalogController::getAllArticles();
                            break;
                        default:
                            echo formatResponse(400, ["Content-Type" => "application/json"],
                                ["success" => false, "errorMessage" => "", "errorCode" => WRONG_ACTION]);
                            die();
                    }
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
