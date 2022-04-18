<?php

class ErrorPage
{
    public static function view($errorCode)
    {
        include __DIR__ . "/../views/error.php";
    }

}