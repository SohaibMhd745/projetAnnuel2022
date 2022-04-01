<?php

/**
 * Generate string of random alphanumeric characters
 * (Source : https://stackoverflow.com/questions/4356289/php-random-string-generator)
 * @param int $length
 * @return string : random alphanumeric string of len $length
 */
function generateRandomString(int $length):string {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}