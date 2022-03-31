<?php

class Login
{
    /**
     *
     * Utility functions
     *
     */

    /**
     * Checks validity of email string
     * @param string $email : email string input
     * @return bool : validity
     */
    protected function checkMailValidity(string $email) : bool{
        if (empty($email)) return false;
        if (strlen($email) == 0) return false;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
        if(strlen($email)>50 || strlen($email)<5) return false;

        return true;
    }

    /**
     * Checks validity of password string
     * @param string $pass : password string input
     * @return bool : validity
     */
    protected function checkPassValidity(string $pass) : bool{
        if (empty($pass)) return false;
        if (strlen($pass) == 0) return false;

        return true;
    }
}