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

require '../lib/vendor/autoload.php';

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
    $param1 = explode('/', $route)[2] ?? "none";
    $param2 = explode('/', $route)[3] ?? "none";
    
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
                        case "generatecode":
                            include __DIR__ . "/../api/controllers/Login.php";
                            Login::generateSponsorCode();
                            break;
                        case "signin":
                            include __DIR__ . "/../api/controllers/Login.php";
                            Login::signin();
                            break;
                        case "getdata":
                            include __DIR__ . "/../api/controllers/Login.php";
                            Login::getdata();
                            break;
                        case "getpartners":
                            include __DIR__ . "/../api/controllers/Login.php";
                            Login::getCompanies();
                            break;
                        case "generateapitoken":
                            include __DIR__ . "/../api/controllers/Login.php";
                            Login::generateApiToken();
                            break;
                        default:
                            echo formatResponse(400, ["Content-Type" => "application/json"],
                                ["success" => false, "errorMessage" => "This function does not exist", "errorCode" => WRONG_ACTION]);
                            die();
                    }
                    break;
                default:
                    echo formatResponse(400, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "This controller does not support this method", "errorCode" => WRONG_METHOD]);
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
                        case "search":
                            include __DIR__."/../api/controllers/CatalogController.php";
                            CatalogController::searchArticles();
                            break;
                        case "orderedsearch":
                            include __DIR__."/../api/controllers/CatalogController.php";
                            CatalogController::orderedSearch();
                            break;
                        case "getarticles":
                            include __DIR__."/../api/controllers/CatalogController.php";
                            CatalogController::getArticles();
                            break;
                        default:
                            echo formatResponse(400, ["Content-Type" => "application/json"],
                                ["success" => false, "errorMessage" => "This function does not exist", "errorCode" => WRONG_ACTION]);
                            die();
                    }
                    break;
                default:
                    echo formatResponse(400, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "This controller does not support this method", "errorCode" => WRONG_METHOD]);
                    die();
            }
            break;

        case "checkout":
            switch ($method){
                case "POST":
                    switch ($action){
                        case "partner":
                            include __DIR__."/../api/controllers/CheckoutController.php";
                            CheckoutController::partnerCheckout();
                            break;
                        case "pointscheckout":
                            include __DIR__."/../api/controllers/CheckoutController.php";
                            CheckoutController::pointsCheckout();
                            break;
                        case "partnersubscriptionstatus":
                            include __DIR__."/../api/controllers/CheckoutController.php";
                            CheckoutController::getPartnerSubscriptionStatus();
                            break;
                        case "declarerevenue":
                            include __DIR__."/../api/controllers/CheckoutController.php";
                            CheckoutController::declareRevenue();
                            break;
                        default:
                            echo formatResponse(400, ["Content-Type" => "application/json"],
                                ["success" => false, "errorMessage" => "This function does not exist", "errorCode" => WRONG_ACTION]);
                            die();
                    }
                    break;
                case "GET":
                    switch($action){
                        case "validateorder":
                            include __DIR__."/../api/controllers/CheckoutController.php";
                            CheckoutController::orderCheckout($param1);
                            break;
                        case "completeorder":
                            include __DIR__."/../api/controllers/CheckoutController.php";
                            CheckoutController::completeOrder($param1, $param2);
                            break;
                            case "subscriptionpayment":
                            include __DIR__."/../api/controllers/CheckoutController.php";
                            CheckoutController::subscriptionPayment($param1);
                            break;
                        case "completesubscriptionpayment":
                            include __DIR__."/../api/controllers/CheckoutController.php";
                            CheckoutController::completeSubscriptionPayment($param1, $param2);
                            break;
                        default:
                            echo formatResponse(400, ["Content-Type" => "application/json"],
                                ["success" => false, "errorMessage" => "This function does not exist", "errorCode" => WRONG_ACTION]);
                            die();
                    }
                    break;
                default:
                    echo formatResponse(400, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "This controller does not support this method", "errorCode" => WRONG_METHOD]);
                    die();
            }
            break;

        case "shop":
            include __DIR__ . "/../api/controllers/Shop.php";
            Shop::view();
            break;

        case "cart":
            include __DIR__ . "/../api/controllers/Cart.php";
            Cart::view();
            break;

        case "order":
            switch ($method){
                case "POST":
                    switch ($action){
                        case "getorder":
                            include __DIR__."/../api/controllers/OrderController.php";
                            OrderController::getOrder();
                            break;
                        case "change":
                            include __DIR__."/../api/controllers/OrderController.php";
                            OrderController::setNewCartAmount();
                            break;
                        default:
                            echo formatResponse(400, ["Content-Type" => "application/json"],
                                ["success" => false, "errorMessage" => "This function does not exist", "errorCode" => WRONG_ACTION]);
                            die();
                    }
                    break;
                default:
                    echo formatResponse(400, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "This controller does not support this method", "errorCode" => WRONG_METHOD]);
                    die();
            }
            break;

        case "cancel":
            include __DIR__ . "/../api/controllers/Cart.php";
            Cart::view();
            break;

        case "error":
            include __DIR__ . "/../api/controllers/ErrorPage.php";
            ErrorPage::view($action);
            break;

        default:
            include __DIR__ . "/../api/controllers/ErrorPage.php";
            ErrorPage::view(404);
            break;
    }
} else {
    include __DIR__ . "/../api/controllers/Home.php";
    Home::view();
}