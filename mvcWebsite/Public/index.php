<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);


$route = $_REQUEST["view"] ?? "";

$method = $_SERVER["REQUEST_METHOD"];

//Switch Route

include __DIR__ . "/../api/controllers/home.php";
Home::all();

