<?php

    include("include_scripts.php");

    validateForm('lastname', "account.php",2, 30);
    validateForm('firstname', "account.php",2, 30);
    validateForm('birthdate', "account.php", 9,11);
    validateForm('phone', "account.php",10, 10);
    validateForm('email', "account.php",5, 30);
    validateForm('password', "account.php",8, 30);

    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $birthdate = $_POST['birthdate'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = preparePassword($_POST['password']);
    $inscription = getYearsAgo(0);

    try{
        $dblink = new DbLink(HOST, CHARSET, DB, USER, PASS);
    }catch(Exception $e){
        redirectIFormError("Database", "account.php", "Erreur fatale de connection à la base de donnée.");
    }

    if($dblink->query('SELECT id FROM akm_users WHERE email = :email', ['email' => $email]) !== false){
        redirectError('email', 'compte déjà existant');
    }
    
    if($dblink->query(
        'INSERT INTO akm_users (lastname, firstname, birthdate, phone, email, password, inscription)
        VALUES (:lastname, :firstname, :birthdate, :phone, :email, :password, :inscription)',
    [
        'lastname' => $lastname,
        'firstname' => $firstname,
        'birthdate' => $birthdate,
        'phone' => $phone,
        'email' => $email,
        'password' => $password,
        'inscription' => $inscription
    ]
    ) === true){
        $uAcc = new User();
        try {
            $uAcc->constructFromEmailAndPassword($email, $password, $dblink);
        } catch (Exception $e) {
            header("Location: /account.php?errorMsg=database&errorType=1");
            exit();
        }

        session_start();
        $_SESSION['user'] = $uAcc;
        header("Location: / ");
        exit();

    } else {
        header("Location: /account.php?errorMsg=database&errorType=1");
        exit();
    }
?>