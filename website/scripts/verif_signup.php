<?php

    include("include_scripts.php");

    validateForm('lastname', "account.php", 2, 30, 1);
    validateForm('firstname', "account.php", 2, 30, 1);
    validateForm('birthdate', "account.php", 9, 11, 1);
    validateForm('phone', "account.php", 10, 10, 1);
    validateForm('email', "account.php", 5, 30, 1);
    validateForm('password', "account.php", 8, 30, 1);

    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $birthdate = $_POST['birthdate'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $inscription = getYearsAgo(0);

    try{
        $dblink = new DbLink(HOST, CHARSET, DB, USER, PASS);
    }catch(Exception $e){
        redirectFormError("Database", "account.php", "Erreur fatale de connection à la base de donnée.",1);
    }

    if($dblink->query('SELECT id FROM akm_users WHERE email = :email', ['email' => $email]) !== false){
        redirectFormError('email', 'account.php','compte déjà existant',1);
    }

    if($dblink->insert(
        'INSERT INTO akm_users (lastname, firstname, birthdate, phone, email, password, inscription)
        VALUES (:lastname, :firstname, :birthdate, :phone, :email, :password, :inscription)',
    [
        'lastname' => $lastname,
        'firstname' => $firstname,
        'birthdate' => $birthdate,
        'phone' => $phone,
        'email' => $email,
        'password' => preparePassword($password),
        'inscription' => $inscription
    ]
    ) !== false){
        $uAcc = new User();
        try {
            $uAcc->constructFromEmailAndPassword($email, $password, $dblink);
        } catch (Exception $e) {
            redirectFormError("database", "account.php", "Impossible d'accéder à la base de donnée, veuillez réessayer plus tard", 1);
        }

        try {
            connectUser($uAcc, $dblink);
        } catch (Exception $e) {
            redirectFormError("fatal", "account.php", "Veuillez réessayer plus tard", 1);
        }

    } else {
        redirectFormError("database", "account.php", "Impossible d'accéder à la base de donnée, veuillez réessayer plus tard", 1);
        exit();
    }
?>