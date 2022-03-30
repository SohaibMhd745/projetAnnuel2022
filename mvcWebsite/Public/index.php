<?php

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
    $params = explode('/', $route);
    $controller = $params[0];

    $action = $params[1] ?? "index";

    //Switch based on controller
    switch ($controller){
        case "account":
            include __DIR__ . "/../api/controllers/Account.php";
            Account::view();
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
