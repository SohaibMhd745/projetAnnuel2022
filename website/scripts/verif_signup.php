<?php

    include("include_scripts.php");

    /**
     * Redirect to account page with error
     * @param string $inputName : name of invalid input
     * @param string $errorType : error message to be shown
     */
    function redirectError(string $inputName, string $errorMsg){
        header("Location: /account.php?errorMsg=" . $errorMsg . "&errorType=1&inputName=" . $inputName);
        exit();
    }

    /**
     * Redirect to account page with error
     * @param string $inputString : input string to sanitize
     */
    function sanitizeStringQuotes(string $inputString){
        str_replace('"', '', $inputString);
        str_replace("'", "", $inputString);
        return $inputString;
    }

    /**
     * Gets the date of n years ago
     * @param int $yearsAgo : input number of years from today's date
     * @return string : date of n years ago YYYY-MM-DD
     */
    function getYearsAgo(int $yearsAgo){
        $dayMonth = date("m-d");
        $year = date("Y")-$yearsAgo;
        $string = $year . '-' . $dayMonth;
        return $string;
    }

    /**
     * Validate form inputs
     * @param string $inputName : name of input to be validated
     * @param int $minLength : (optional) minimum length of input
     * @param int $maxLength : (optional) maximum length of input
     */
    function validateForm(string $inputName, int $minLength = 0, int $maxLength = 30){

        // general validations

        if(!isset($_POST[$inputName]) || empty($_POST[$inputName])){
            redirectError($inputName, 'champ requis');
        }
        
        $_POST[$inputName] = sanitizeStringQuotes($_POST[$inputName]);
        setcookie($inputName, $_POST[$inputName], time() + 3600 );

        if(strlen((string)($_POST[$inputName])) < $minLength || strlen((string)($_POST[$inputName])) > $maxLength){
            redirectError($inputName, 'doit faire entre ' . $minLength . ' et ' . $maxLength . ' caractères');
        }

        // specific validations

        if($inputName == 'lastname' || $inputName == 'firstname'){
            $inputWithoutSpaces = str_replace(' ', '', $_POST[$inputName]);
            if(!(ctype_alpha($inputWithoutSpaces))){
                redirectError($inputName, 'doit contenir des lettres uniquement');
            }
        }

        elseif(($inputName == 'birthdate')){
            $regex = '/[0-9]{4}-[0-9]{2}-[0-9]{2}/';
            if(preg_match($regex, $_POST[$inputName])){
                $minDate = '1900-01-01';
                $maxDate = getYearsAgo(18);
                if($_POST[$inputName] < $minDate || $_POST[$inputName] > $maxDate){
                    redirectError($inputName, 'n\'est pas une date valide');
                }
            } else {
                redirectError($inputName, 'n\'est pas une date');
            }
        }

        elseif($inputName == 'phone' && !(is_numeric($_POST[$inputName]))){
            redirectError($inputName, 'doit contenir des chiffres uniquement');
        }

        elseif($inputName == 'email' && !filter_var($_POST[$inputName], FILTER_VALIDATE_EMAIL)){
            redirectError($inputName, 'format invalide');
        }
    }

    validateForm('lastname', 2, 30);
    validateForm('firstname', 2, 30);
    validateForm('birthdate');
    validateForm('phone', 10, 10);
    validateForm('email', 5, 30);
    validateForm('password', 8, 30);

    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $birthdate = $_POST['birthdate'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $inscription = getYearsAgo(0);

    try{
        $dblink = new DbLink("localhost", "utf8", "lc_website", "root", "root");
    }catch(Exception $e){
        header("Location: /account.php?errorMsg=database&errorType=1");
        exit();
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
        'password' => preparePassword($password),
        'inscription' => $inscription
    ]
    ) === true){
        $dblink->connectUser($email);
    } else {
        header("Location: /account.php?errorMsg=database&errorType=1");
        exit();
    }
?>