<?php

include("include_scripts.php");

session_start();

//TODO: Page name TBD, empty as placeholder
//TODO: Error Slot TBD, 0 as placeholder

validateForm('partnername', "",2, 30,0);
validateForm('revenue', "",0, 20,0);
validateForm('website', "", 5,50,0);

//TODO: Determine how to do this
/*
validateForm('sponsorshipCode', "", ?,?,0);
*/

$name = $_POST['partnername'];
$revenue = $_POST['revenue'];
$website = $_POST['website'];

//TODO:
$id_sponsor = null;

$inscription = getYearsAgo(0);

try{
    $dblink = new DbLink(HOST, CHARSET, DB, USER, PASS);
}catch(Exception $e){
    redirectFormError("Database", "", "Erreur fatale de connection à la base de donnée.",0);
}

if($dblink->insert(
        'INSERT INTO akm_partners (name, inscription, revenue, website, id_sponsor, id_user)
        VALUES (:partnername, :inscription, :revenue, :website, :id_sponsor, :id_user)',
        [
            'partnername' => $name,
            'inscription' => $inscription,
            'revenue' => $revenue,
            'website' => $website,
            'id_sponsor' => $id_sponsor,
            'id_user' => $_SESSION['user']->getId(),
        ]
    ) !== false){
    $status = $_SESSION['user']->updateIdPartner($dblink);

    if ($status!==NO_EXCEPTION){
        //TODO:
        redirectFormError("database","", "Le partenaire n'a pas été enregistré correctement", 0);
    }

    $uAcc = new Partner();
    try {
        $uAcc->constructFromId($_SESSION['user']->getId(), $dblink);
    } catch (Exception $e) {
        //TODO:
        redirectFormError("database", "", "Impossible d'accéder à la base de donnée, veuillez réessayer plus tard", 0);
    }

    try {
        connectUser($uAcc, $dblink);
    } catch (Exception $e) {
        //TODO:
        redirectFormError("fatal", "", "Veuillez réessayer plus tard", 0);
    }

} else {
    //TODO:
    redirectFormError("database", "", "Impossible d'accéder à la base de donnée, veuillez réessayer plus tard", 0);
}