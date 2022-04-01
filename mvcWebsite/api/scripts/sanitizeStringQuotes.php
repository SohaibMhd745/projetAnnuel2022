<?php

/**
 * Redirect to account page with error
 * @param string $inputString : input string to sanitize
 */
function sanitizeStringQuotes(string $inputString){
    str_replace('"', '', $inputString);
    str_replace("'", "", $inputString);
    return $inputString;
}