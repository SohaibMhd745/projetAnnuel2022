<?php

class Login
{
    public static function signup(){
        include __DIR__."../scripts/include_scripts.php";
        include __DIR__."../models/User.php";

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

        foreach ($params as $paramName => $param){
            switch ($paramName){
                case "lastname":
                case "firstname":
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
            }
            try {
                self::validateParam($param, $paramName, $min, $max);
            }catch (Exception $e){
                switch ($e->getCode()){

                    default:
                        echo formatResponse(500, ["Content-Type" => "application/json"],
                            ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION]);
                        break;
                }
                die();
            }
        }

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

        echo formatResponse(500, ["Content-Type" => "application/json"],
            ["success" => true, "token" => $token]);

    }



    /**
     *
     * Utility functions
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

    /**
     * Validate form inputs
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

}