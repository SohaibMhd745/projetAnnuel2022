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

/**
 * Returns array of date_min and date_max of current year
 * @return array
 * date_min - midnight of january the first of the current year
 * date_max - 23:59:59 of december the 31st of the current year
 */
function getCivilYear(): array{
    $year = date('Y');
    $date_min = date("y-m-d H:i:s", strtotime("1 january ".$year." 00:00:00"));
    $date_max = date("y-m-d H:i:s", strtotime("31 december ".$year." 23:59:59"));
    return  ["date_min" => $date_min, "date_max" => $date_max];
}