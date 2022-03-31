<?php

//TODO: Réunir tout les codes d'erreur des catchs dans une fonction pour la lisibilité
class Login
{
    /**
     * @httpmethod post
     * @return void
     */
    public static function signup(){
        include __DIR__."/../scripts/include_scripts.php";
        include __DIR__."/../models/User.php";

        $json = json_decode(file_get_contents("php://input"));

        if(!isset($json->lastname)||empty($json->lastname))
            self::reportMissingParam("lastname");
        if(!isset($json->firstname)||empty($json->firstname))
            self::reportMissingParam("firstname");
        if(!isset($json->birthdate)||empty($json->birthdate))
            self::reportMissingParam("birthdate");
        if(!isset($json->phone)||empty($json->phone))
            self::reportMissingParam("phone");
        if(!isset($json->email)||empty($json->email))
            self::reportMissingParam("email");
        if(!isset($json->password)||empty($json->password))
            self::reportMissingParam("password");

        $params = [
            "lastname" => $json->lastname,
            "firstname" => $json->firstname,
            "birthdate" => $json->birthdate,
            "phone" => $json->phone,
            "email" => $json->email,
            "password" => $json->password
        ];

        self::checkParams($params);

        try {
            User::create(
                $params["lastname"],
                $params["firstname"],
                $params["birthdate"],
                $params["phone"],
                $params["email"],
                $params["password"]
            );
        }catch (Exception $e){
            switch ($e->getCode()){
                case EMAIL_USED:
                    echo formatResponse(400, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Email already in use", "errorCode" => EMAIL_USED]);
                    break;
                case MYSQL_EXCEPTION:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION]);
                    break;
                default:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION]);
                    break;
            }
            die();
        }

        $user = new User();
        try {
            $user->constructFromEmailAndPassword($params["email"], $params["password"]);
        }catch (Exception $e){
            echo formatResponse(500, ["Content-Type" => "application/json"],
                ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION]);
            die();
        }

        try {
            $token = $user->updateToken();
        }catch (Exception $e){
            echo formatResponse(500, ["Content-Type" => "application/json"],
                ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION]);
            die();
        }

        echo formatResponse(200, ["Content-Type" => "application/json"],
            ["success" => true, "token" => $token]);
    }

    /**
     * @httpmethod get
     * @return void
     */
    public static function signin(){
        include __DIR__."/../scripts/include_scripts.php";
        include __DIR__."/../models/User.php";

        $json = json_decode(file_get_contents("php://input"));

        if(!isset($json->email)||empty($json->email))
            self::reportMissingParam("email");
        if(!isset($json->password)||empty($json->password))
            self::reportMissingParam("password");

        $params = [
            "email" => $json->email,
            "password" => $json->password
        ];

        self::checkParams($params);

        $user = new User();
        try {
            $user->constructFromEmailAndPassword(
                $params["email"],
                $params["password"]
            );
        }catch (Exception $e){
            switch ($e->getCode()){
                case INCORRECT_USER_CREDENTIALS:
                    echo formatResponse(401, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Could not prove user authenticity", "errorCode" => INCORRECT_USER_CREDENTIALS]);
                    break;
                case MYSQL_EXCEPTION:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION]);
                    break;
                default:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION]);
                    break;
            }
            die();
        }
        try {
            $token = $user->updateToken();
        }catch (Exception $e){
            echo formatResponse(500, ["Content-Type" => "application/json"],
                ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION]);
            die();
        }

        echo formatResponse(200, ["Content-Type" => "application/json"],
            ["success" => true, "token" => $token]);
    }

    /**
     * @httpmethod post
     * @return void
     */
    public static function registercompany(){
        include __DIR__."/../scripts/include_scripts.php";
        include __DIR__."/../models/User.php";
        include __DIR__."/../models/Partner.php";

        $json = json_decode(file_get_contents("php://input"));

        if(!isset($json->partnername)||empty($json->partnername))
            self::reportMissingParam("partnername");
        if(!isset($json->revenue)||empty($json->revenue))
            self::reportMissingParam("revenue");
        if(!isset($json->website)||empty($json->website))
            self::reportMissingParam("website");

        //TODO: header ?
        if(!isset($json->token)||empty($json->token))
            self::reportMissingParam("token");

        //TODO:
        /**
         * if(!isset($json->sponsorid)||empty($json->sponsorid))
                self::reportMissingParam("sponsorid");
         */

        $token = $json->token;

        $params = [
            "partnername" => $json->partnername,
            "revenue" => $json->revenue,
            "website" => $json->website,
            "sponsorid" => null
            //TODO:
            //"sponsorid" => $json->sponsorid
        ];

        self::checkParams($params);

        $user = new User();
        try {
            $user->constructFromToken($token);
        }catch (Exception $e){
            switch ($e->getCode()){
                case INVALID_AUTH_TOKEN:
                    echo formatResponse(401, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Invalid auth token", "errorCode" => INVALID_AUTH_TOKEN]);
                    break;
                case MYSQL_EXCEPTION:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION]);
                    break;
                default:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION]);
                    break;
            }
            die();
        }

        try {
            Partner::register($user, $params["partnername"], $params["revenue"], $params["website"], $params["sponsorid"]);
        }catch (Exception $e){
            switch ($e->getCode()){
                case INVALID_AUTH_TOKEN:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Could not link company to user", "errorCode" => COMPANY_NOT_FOUND]);
                    break;
                case MYSQL_EXCEPTION:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION]);
                    break;
                default:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION]);
                    break;
            }
            die();
        }

        echo formatResponse(200, ["Content-Type" => "application/json"],
            ["success" => true]);
    }

    /**
     * @httpmethod get
     * @return void
     */
    public static function getdata(){
        include __DIR__."/../scripts/include_scripts.php";
        include __DIR__."/../models/User.php";
        include __DIR__."/../models/Partner.php";

        $json = json_decode(file_get_contents("php://input"));

        if(!isset($json->token)||empty($json->token))
            self::reportMissingParam("token");

        $token = $json->token;

        $user = new User();
        try {
            $user->constructFromToken($token);
        } catch (Exception $e){
            switch ($e->getCode()){
                case INVALID_AUTH_TOKEN:
                    echo formatResponse(401, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Invalid auth token", "errorCode" => INVALID_AUTH_TOKEN]);
                    break;
                case MYSQL_EXCEPTION:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION]);
                    break;
                default:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION]);
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
                        echo formatResponse(500, ["Content-Type" => "application/json"],
                            ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION]);
                        break;
                    default:
                        echo formatResponse(500, ["Content-Type" => "application/json"],
                            ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION]);
                        break;
                }
                die();
            }
            echo formatResponse(200, ["Content-Type" => "application/json"], ["success" => true, "usertype" => "partner", "user" => $partner]);
        }else echo formatResponse(200, ["Content-Type" => "application/json"], ["success" => true, "usertype" => "user", "user" => $user]);
    }

    /**
     *
     * Utility functions
     *
     */

    /**
     * Validate form inputs inidivudally
     * @param string $param : input to be validated
     * @param string $paramName : name of input to be validated
     * @param int $minLength : (optional) minimum length of input
     * @param int $maxLength : (optional) maximum length of input
     * @throws:
     * - PARAMETER_WRONG_LENGTH
     * - INVALID_PARAMETER
     */
    private static function validateParam($param, string $paramName,int $minLength, int $maxLength){
        if(strlen($param) < $minLength || strlen($param) > $maxLength)
            throw new Exception("Parameter Length Incorrect", PARAMETER_WRONG_LENGTH);

        switch($paramName){
            case "lasname":
            case "firstname":
            case "name":
            case "partnername":
                $inputWithoutSpaces = str_replace(' ', '', $param);
                if(!(ctype_alpha($inputWithoutSpaces)))
                    throw new Exception("Parameter incorrect", INVALID_PARAMETER);
                break;

            case "birthdate":
                $reg = '/[0-9]{4}-[0-9]{2}-[0-9]{2}/';
                if(preg_match($reg, $param)){
                    if($param < DEFAULT_DATE_MIN || $param > getYearsAgo(18))
                        throw new Exception("Parameter incorrect", INVALID_PARAMETER);
                } else throw new Exception("Parameter incorrect", INVALID_PARAMETER);
                break;

            case "phone":
            case "revenue":
                if (!is_numeric($param))
                    throw new Exception("Parameter incorrect", INVALID_PARAMETER);
                break;

            case "email":
                if (!filter_var($param, FILTER_VALIDATE_EMAIL))
                    throw new Exception("Parameter incorrect", INVALID_PARAMETER);
                break;

            case "website":
                if (!filter_var($param, FILTER_VALIDATE_URL))
                    throw new Exception("Parameter incorrect", INVALID_PARAMETER);
                break;

            default:
                break;

        }
    }

    /**
     * Checks input params array and kills+respond if any param is invalid
     * @param array $params
     * @return void
     */
    public static function checkParams(array $params): void{
        foreach ($params as $paramName => $param) {
            $min = 0;
            $max = 0;
            switch ($paramName) {
                case "lastname":
                case "firstname":
                case "partnername":
                    $min = 2;
                    $max = 30;
                    break;
                case "birthdate":
                    $min = 9;
                    $max = 11;
                    break;
                case "phone":
                    $min = 10;
                    $max = 10;
                    break;
                case "email":
                    $min = 5;
                    $max = 30;
                    break;
                case "password":
                    $min = 8;
                    $max = 30;
                    break;
                case "revenue":
                    $min = 1;
                    $max = 20;
                case "website":
                    $min = 5;
                    $max = 50;
                    break;
            }
            try {
                self::validateParam($param, $paramName, $min, $max);
            } catch (Exception $e) {
                switch ($e->getCode()) {
                    case INVALID_PARAMETER:
                        self::reportInvalidParam($paramName);
                        break;
                    case PARAMETER_WRONG_LENGTH:
                        self::reportParamLength($paramName);
                        break;
                    default:
                        echo formatResponse(500, ["Content-Type" => "application/json"],
                            ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION]);
                        break;
                }
                die();
            }
        }
    }

    /**
     *
     * Error Report Functions
     *
     */

    /**
     * Reports on the missing parameter
     * @param string $param : param to report missing
     * @return void
     */
    private static function reportMissingParam(string $param){
        formatResponse(400, ["Content-Type" => "application/json"],
            ["success" => false, "errorMessage" => "Missing parameter '".$param."'", "errorCode" => MISSING_PARAMETER, "parameter"=>$param]);
        die();
    }

    /**
     * Reports on invalid parameters
     * @param string $param : param to report as invalid
     * @return void
     */
    private static function reportInvalidParam(string  $param){
        formatResponse(400, ["Content-Type" => "application/json"],
            ["success" => false, "errorMessage" => "Invalid parameter '".$param."'", "errorCode" => INVALID_PARAMETER, "parameter"=>$param]);
        die();
    }

    /**
     * Reports on invalid parameter length
     * @param string $param : param to report as invalid length
     * @return void
     */
    private static function reportParamLength(string  $param){
        formatResponse(400, ["Content-Type" => "application/json"],
            ["success" => false, "errorMessage" => "Parameter wrong length '".$param."'", "errorCode" => PARAMETER_WRONG_LENGTH, "parameter"=>$param]);
        die();
    }

}