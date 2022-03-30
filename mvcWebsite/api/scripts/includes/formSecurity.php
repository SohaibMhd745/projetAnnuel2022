<?php

/**
 * Redirect to specified page with error when a form is involved
 * @param string $inputName : name of invalid input
 * @param string $page : page to redirect to
 * @param string $errorMsg : error message to be shown
 */
function redirectFormError(string $errorType, string $page, string $errorMsg, $errorSlot){
    header("Location: /". $page ."?errorMsg=" . $errorMsg . "&errorSlot=". $errorSlot ."&errorType=" . $errorType);
    exit();
}

/**
 * Validate form inputs
 * @param string $inputName : name of input to be validated
 * @param string $page : page of form
 * @param int $minLength : (optional) minimum length of input
 * @param int $maxLength : (optional) maximum length of input
 */
function validateForm(string $inputName, string $page, int $minLength, int $maxLength, int $errorSlot){

    // general validations
    if(!isset($_POST[$inputName])||empty($_POST[$inputName])) redirectFormError($inputName, $page, "Ce champ est nécessaire",$errorSlot);

    $inputName = sanitizeStringQuotes($inputName);
    setcookie($inputName, $_POST[$inputName], time() + 3600 );

    if(strlen($_POST[$inputName]) < $minLength || strlen($_POST[$inputName]) > $maxLength) redirectFormError($inputName, $page, "Taille incorrecte",$errorSlot);

    switch($inputName){
        case "lasname":
        case "firstname":
        case "name":
        case "partnername":
            $inputWithoutSpaces = str_replace(' ', '', $_POST[$inputName]);
            if(!(ctype_alpha($inputWithoutSpaces)))redirectFormError($inputName, $page, "Valeur incorrecte",$errorSlot);
            break;

        case "birthdate":
            $reg = '/[0-9]{4}-[0-9]{2}-[0-9]{2}/';
            if(preg_match($reg, $_POST[$inputName])){
                if($_POST[$inputName] < DEFAULT_DATE_MIN || $_POST[$inputName] > getYearsAgo(18))
                    redirectFormError($inputName, $page, "La valeur n'est pas comprise dans l'intervalle imposé",$errorSlot);
            } else redirectFormError($inputName, $page, "Valeur incorrecte",1);
            break;

        case "phone":
            if (!is_numeric($_POST[$inputName])) redirectFormError($inputName, $page, "Veuillez entrer un numéro de téléphone existant",$errorSlot);
            break;

        case "revenue":
            if (!is_numeric($_POST[$inputName])) redirectFormError($inputName, $page, "Veuillez entrer un chiffre d'affaire",$errorSlot);
            break;

        case "email":
            if (!filter_var($_POST[$inputName], FILTER_VALIDATE_EMAIL))redirectFormError($inputName, $page, "Veuillez entrer une addresse valide",$errorSlot);
            break;

        case "website":
            if (!filter_var($_POST[$inputName], FILTER_VALIDATE_URL))redirectFormError($inputName, $page, "Veuillez entrer un lien valide",$errorSlot);
            break;

        default:
            break;

    }
}