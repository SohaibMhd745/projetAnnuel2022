<?php

/**
 * Starts session and connects the user, checks if the user has a company or not
 * @param $user
 * @return void
 * @throws Exception : those of constructFrom id/mail+password (MYSQL_EXCEPTION) + COMPANY_NOT_FOUND
 */
function connectUser($user, $link){
    session_start();

    if ($user->getIdPartner() === -1) $_SESSION['user'] = $user;
    else{
        $partner = new Partner();
        $partner->constructFromId($user->getId(), $link);
        $_SESSION['user'] = $partner;
    }

    header("Location: / ");
    exit();
}