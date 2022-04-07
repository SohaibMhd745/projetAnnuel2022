<?php

//TODO: Réunir tout les codes d'erreur des catchs dans une fonction pour la lisibilité
class Login
{
    /**
     * @httpmethod post
     * @return void
     */
    public static function signup(){
        include __DIR__."/../models/User.php";

        $json = json_decode(file_get_contents("php://input"));

        if(!isset($json->lastname)||empty($json->lastname))
            reportMissingParam("lastname");
        if(!isset($json->firstname)||empty($json->firstname))
            reportMissingParam("firstname");
        if(!isset($json->birthdate)||empty($json->birthdate))
            reportMissingParam("birthdate");
        if(!isset($json->phone)||empty($json->phone))
            reportMissingParam("phone");
        if(!isset($json->email)||empty($json->email))
            reportMissingParam("email");
        if(!isset($json->password)||empty($json->password))
            reportMissingParam("password");

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
                        ["success" => false, "errorMessage" => "Email already in use", "errorCode" => EMAIL_USED, "step" => "User Creation"]);
                    break;
                case MYSQL_EXCEPTION:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION, "step" => "User Creation"]);
                    break;
                default:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION, "step" => "User Creation"]);
                    break;
            }
            die();
        }

        $user = new User();
        try {
            $user->constructFromEmailAndPassword($params["email"], $params["password"]);
        }catch (Exception $e){
            echo formatResponse(500, ["Content-Type" => "application/json"],
                ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION, "step" => "Connection"]);
            die();
        }

        try {
            $token = $user->updateToken();
        }catch (Exception $e){
            echo formatResponse(500, ["Content-Type" => "application/json"],
                ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION, "step" => "Token Refresh"]);
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
        include __DIR__."/../models/User.php";

        $json = json_decode(file_get_contents("php://input"));

        if(!isset($json->email)||empty($json->email))
            reportMissingParam("email");
        if(!isset($json->password)||empty($json->password))
            reportMissingParam("password");

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
                        ["success" => false, "errorMessage" => "Could not prove user authenticity", "errorCode" => INCORRECT_USER_CREDENTIALS, "step" => "Structure Creation"]);
                    break;
                case MYSQL_EXCEPTION:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION, "step" => "Structure Creation"]);
                    break;
                default:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION, "step" => "Structure Creation"]);
                    break;
            }
            die();
        }
        try {
            $token = $user->updateToken();
        }catch (Exception $e){
            echo formatResponse(500, ["Content-Type" => "application/json"],
                ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION, "step" => "Token Generation"]);
            die();
        }

        echo formatResponse(200, ["Content-Type" => "application/json"],
            ["success" => true, "token" => $token]);
    }

    /**
     * @httpmethod post
     * @return void
     */
    public static function registercompany()
    {
        include __DIR__ . "/../models/User.php";
        include __DIR__ . "/../models/Partner.php";

        $json = json_decode(file_get_contents("php://input"));

        if (!isset($json->partnername) || empty($json->partnername))
            reportMissingParam("partnername");
        if (!isset($json->revenue) || empty($json->revenue))
            reportMissingParam("revenue");
        if (!isset($json->website) || empty($json->website))
            reportMissingParam("website");

        if (!isset($json->token) || empty($json->token))
            reportMissingParam("token");

        $token = $json->token;

        $params = [
            "partnername" => $json->partnername,
            "revenue" => $json->revenue,
            "website" => $json->website,
        ];

        self::checkParams($params);

        $user = self::attemptConnection($token);

        if (!isset($json->sponsorcode) || empty($json->sponsorcode)) $sponsor = false;
        else {
            try {
                $sponsor = Partner::useSponsorCode($json->sponsorcode);
            } catch (Exception $e) {
                switch ($e->getCode()) {
                    case INVALID_CODE:
                        echo formatResponse(400, ["Content-Type" => "application/json"],
                            ["success" => false, "errorMessage" => "Code is not valid", "errorCode" => INVALID_CODE]);
                        break;
                    case MYSQL_EXCEPTION:
                        echo formatResponse(500, ["Content-Type" => "application/json"],
                            ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION, "step" => "Code check"]);
                        break;
                    default:
                        echo formatResponse(500, ["Content-Type" => "application/json"],
                            ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION, "step" => "Code check"]);
                        break;
                }
                die();
            }
        }

        try {
            if($sponsor === false) Partner::registerWithoutCode($user, $params["partnername"], $params["revenue"], $params["website"]);
            else Partner::registerWithCode($user, $params["partnername"], $params["revenue"], $params["website"], $sponsor);
        }catch (Exception $e){
            switch ($e->getCode()){
                case INVALID_AUTH_TOKEN:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Could not link company to user", "errorCode" => COMPANY_NOT_FOUND, "step" => "Company Registration"]);
                    break;
                case MYSQL_EXCEPTION:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION, "step" => "Company Registration"]);
                    break;
                default:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION, "step" => "Company Registration"]);
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
    public static function getCompanies(){
        include __DIR__."/../models/User.php";
        include __DIR__."/../models/Partner.php";

        $json = json_decode(file_get_contents("php://input"));

        if(!isset($json->number)||empty($json->number)) $number = null;
        else $number = $json->number;

        if(!isset($json->page)||empty($json->page)) $page = null;
        else $page = $json->page;

        try {
            if($page === null || $number === null) $info = Partner::getAllPartnerId();
            else $info = Partner::getPartnerId($number, $page);
        }catch (Exception $e){
            echo formatResponse(500, ["Content-Type" => "application/json"],
                ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION]);
            die();
        }

        echo formatResponse(200, ["Content-Type" => "application/json"],
            ["success" => true, "table" => $info]);
    }

    /**
     * @httpmethod get
     * @return void
     */
    public static function getdata(){
        include __DIR__."/../models/User.php";
        include __DIR__."/../models/Partner.php";

        $json = json_decode(file_get_contents("php://input"));

        if(!isset($json->token)||empty($json->token))
            reportMissingParam("token");

        $token = $json->token;

        $user = self::attemptConnection($token);

        if ($user->getIdPartner()!==-1){
            $partner = self::attemptPartnerConnection($token);
            echo formatResponse(200, ["Content-Type" => "application/json"], ["success" => true, "usertype" => "partner",
                "user" => [
                    "id"=>$partner->getId(),
                    "firstname"=>$partner->getFirstName(),
                    "lastname"=>$partner->getLastName(),
                    "inscription"=>$partner->getInscription(),
                    "birthdate"=>$partner->getBirth(),
                    "phone"=>$partner->getPhone(),
                    "id_partner"=>$partner->getIdPartner(),
                    "partner" => [
                        "name" => $partner->getPartnerName(),
                        "inscription" => $partner->getPartnerInscription(),
                        "revenue" => $partner->getRevenue(),
                        "website" => $partner->getWebsite(),
                        "id_sponsor" =>$partner->getSponsorId()
                    ]
                ]]);
        }else echo formatResponse(200, ["Content-Type" => "application/json"], ["success" => true, "usertype" => "user",
            "user" => [
                "id"=>$user->getId(),
                "firstname"=>$user->getFirstName(),
                "lastname"=>$user->getLastName(),
                "inscription"=>$user->getInscription(),
                "birthdate"=>$user->getBirth(),
                "phone"=>$user->getPhone(),
                "id_partner"=>$user->getIdPartner()
        ]]);
    }

    /**
     * @httpmethod get
     * @return void
     */
    public static function generateSponsorCode(){
        include __DIR__."/../models/User.php";
        include __DIR__."/../models/Partner.php";

        $json = json_decode(file_get_contents("php://input"));

        if(!isset($json->token)||empty($json->token))
            reportMissingParam("token");

        $token = $json->token;

        $user = self::attemptConnection($token);

        if ($user->getIdPartner() === -1) echo formatResponse(400, ["Content-Type" => "application/json"],
            ["success" => false, "errorMessage" => "Provided user is not ", "errorCode" => MYSQL_EXCEPTION]);

        try {
            $code = Partner::generateSponsorCode($user->getIdPartner());
        }catch (Exception $e) {
            switch ($e->getCode()) {
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
            ["success" => true, "code" => $code]);
    }

    /**
     *
     * Utility functions
     *
     */

    /**
     * Validate form inputs individually
     * @param mixed $param : input to be validated
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
                    $min = 3;
                    $max = 30;
                    break;
                default:
                    $min = 1;
                    $max = 20;
                    break;
            }
            try {
                self::validateParam($param, $paramName, $min, $max);
            } catch (Exception $e) {
                switch ($e->getCode()) {
                    case INVALID_PARAMETER:
                        reportInvalidParam($paramName);
                        break;
                    case PARAMETER_WRONG_LENGTH:
                        reportParamLength($paramName);
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
     * Attempts user connection, breaks execution in case of error, returns user otherwise
     * @param string $token token to attempt connection with
     * @return User built structure from token
     */
    private static function attemptConnection(string $token) : User{
        $user = new User();
        try {
            $user->constructFromToken($token);
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case INVALID_AUTH_TOKEN:
                    echo formatResponse(401, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Invalid auth token", "errorCode" => INVALID_AUTH_TOKEN, "step" => "User Authentication"]);
                    break;
                case MYSQL_EXCEPTION:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION, "step" => "User Authentication"]);
                    break;
                default:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION, "step" => "User Authentication"]);
                    break;
            }
            die();
        }
        return $user;
    }

    /**
     * Attempts partner connection, breaks execution in case of error, returns user otherwise
     * @param string $token token to attempt connection with
     * @return Partner built structure from token
     */
    private static function attemptPartnerConnection(string $token):Partner{
        $partner = new Partner();
        try {
            $partner->constructFromToken($token);
        }catch (Exception $e){
            switch ($e->getCode()){
                case MYSQL_EXCEPTION:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION, "step" => "User Authentication"]);
                    break;
                default:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION, "step" => "User Authentication"]);
                    break;
            }
            die();
        }
        return $partner;
    }
}