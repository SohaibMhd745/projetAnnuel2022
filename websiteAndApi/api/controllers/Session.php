<?php

class Session
{
    public static function createSession(string $token){
        include __DIR__."/../models/User.php";
        include __DIR__."/../models/Partner.php";

        if(!isset($json->token)||empty($json->token)){
            //TODO: Include Page d'erreur avec msg
            die();
        }


        $user = new User();
        try {
            $user->constructFromToken($token);
        } catch (Exception $e){
            switch ($e->getCode()){
                case INVALID_AUTH_TOKEN:
                    //TODO: Include Page d'erreur
                    break;
                case MYSQL_EXCEPTION:
                    //TODO: Include Page d'erreur
                    break;
                default:
                    //TODO: Include Page d'erreur avec msg (erreur fatale)
                    break;
            }
            die();
        }

        if ($user->getIdPartner()!==-1){
            $partner = new Partner();
            try {
                $partner->constructFromToken($token);
            }catch (Exception $e){
                switch ($e->getCode()){
                    case MYSQL_EXCEPTION:
                        //TODO: Include Page d'erreur avec msg
                        break;
                    default:
                        //TODO: Include Page d'erreur avec msg (erreur fatale)
                        break;
                }
                die();
            }
            //TODO: session start avec user et partner (les propriétés, pas les Classes, ça aime pas)
        }else {
            //TODO: session start avec user (les propriétés, pas les Classes, ça aime pas)
        }
    }
}