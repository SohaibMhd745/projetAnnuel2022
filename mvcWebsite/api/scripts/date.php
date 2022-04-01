<?php


/**
 * Gets the date of n years ago
 * @param int $yearsAgo : input number of years from today's date
 * @return string : date of n years ago YYYY-mm-dd
 */
function getYearsAgo(int $yearsAgo){
    $dayMonth = date("m-d");
    $year = date("Y")-$yearsAgo;
    $string = $year . '-' . $dayMonth;
    return $string;
}