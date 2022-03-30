<?php

include "scripts/include_scripts.php";

try{
    $dblink = new DbLink(HOST, CHARSET, DB, USER, PASS);
}catch(Exception $e){
    echo("Pas de PDO");
    exit();
}

$test = new User();
try {
    $test->constructFromId(14, $dblink);
} catch (Exception $e) {
    echo($e->getMessage());
    exit();
}

echo $test->getIdPartner();
echo $this->getEmail();

?>