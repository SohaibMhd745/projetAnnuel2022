<?php

class Account
{
    public static function view()
    {
        include __DIR__ . "/../views/account.php";
        echo '<script type="text/javascript" src="../api/scripts/account.js"></script>';
    }
}