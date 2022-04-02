<?php

/**
 * Redirect to account page with error
 * @param string $inputString : input string to sanitize
 * @return string : sanitized string
 */
function sanitizeStringQuotes(string $inputString):string{
    str_replace('"', '', $inputString);
    str_replace("'", "", $inputString);
    return $inputString;
}