<?php

class Session
{
    /**
     * Creates a session using the provided token and puts it in, should only be used by index
     * @param string $token : token to create the session with
     * @return void
     */
    public static function createSession(string $token){
        include __DIR__."/../models/User.php";
        include __DIR__."/../models/Partner.php";

        if(!isset($token)||empty($token)){
            header("Location: /error/500"); exit();
            die();
        }


        $user = new User();
        try {
            $user->constructFromToken($token);
        } catch (Exception $e){
            switch ($e->getCode()){
                case INVALID_AUTH_TOKEN:
                    header("Location: /error/500"); exit();
                    break;
                case MYSQL_EXCEPTION:
                    header("Location: /error/500"); exit();
                    break;
                default:
                    header("Location: /error/500"); exit();
                    break;
            }
            die();
        }
        session_start();
        $_SESSION['token'] = $token;
        /**
         * Après dans les controlleurs de page tu peux copier la fonction en dessous pour vérif si la session est valide:
         * Session::checkSession();
        **/

    }

    /**
     * Checks if session token exists
     * @return bool : True if token exists, false if token doesn't exist, redirects to connection page if token provided is expired/invalid
     */
    public static final function checkSession():bool{
        session_start();
        if(isset($_session['$token'])&&!empty($_session['$token'])){
            include __DIR__."/../models/User.php";
            include __DIR__."/../models/Partner.php";

            $user = new User();
            try {
                $user->constructFromToken($_session['$token']);
            } catch (Exception $e){
                switch ($e->getCode()){
                    case INVALID_AUTH_TOKEN:
                        session_destroy();
                        header('Location: localhost/account');
                        break;
                    case MYSQL_EXCEPTION:
                        header("Location: /error/500"); exit();
                        break;
                    default:
                        header("Location: /error/500"); exit();
                        break;
                }
                die();
            }
            return true;
        }else return false;
    }
}