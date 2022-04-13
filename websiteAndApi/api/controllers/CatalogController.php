<?php

class CatalogController
{

    /**
     * Adds article to the database
     * @httpmethod post
     * @return void
     */
    public static function addArticle(){
        include __DIR__."/../models/Catalog.php";

        $json = json_decode(file_get_contents("php://input"));

        if(!isset($json->name)||empty($json->name))
            reportMissingParam("name");
        if(!isset($json->description)||empty($json->description))
            reportMissingParam("description");
        if(!isset($json->price)||empty($json->price))
            reportMissingParam("price");

        if(!isset($json->token)||empty($json->token))
            reportMissingParam("token");


        $token = $json->token;

        $params = [
            "name" => $json->name,
            "description" => $json->description,
            "price" => $json->price,
        ];

        self::checkParams($params);

        try {
            Catalog::addArticle($token, $params["name"], $params["price"], $params["description"]);
        }catch (Exception $e){
            switch ($e->getCode()){
                case INVALID_AUTH_TOKEN:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Invalid Token", "errorCode" => INVALID_AUTH_TOKEN, "step" => "Article registration"]);
                    break;
                case MYSQL_EXCEPTION:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION, "step" => "Article registration"]);
                    break;
                case COMPANY_NOT_FOUND:
                    echo formatResponse(400, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "User is not linked to company", "errorCode" => COMPANY_NOT_FOUND, "step" => "Article registration"]);
                    break;
                default:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION, "step" => "Article registration"]);
                    break;
            }
            die();
        }

        echo formatResponse(200, ["Content-Type" => "application/json"],
            ["success" => true]);
    }

    /**
     * Searc article based on search term
     * @return void
     */
    public static function searchArticles(){
        include __DIR__."/../models/Catalog.php";

        $json = json_decode(file_get_contents("php://input"));

        if(!isset($json->search)||empty($json->search))
            reportMissingParam("search");

        if(!isset($json->number)||empty($json->number)) $number = null;
        else $number = $json->number;

        if(!isset($json->page)||empty($json->page)) $page = null;
        else $page = $json->page;

        try {

            if($page === null || $number === null) $info = Catalog::searchAllArticles($json->search);
            else $info = Catalog::searchArticles($json->search, $number, $page);
        }catch (Exception $e){
            switch ($e->getCode()){
                case MYSQL_EXCEPTION:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Database Error", "errorCode" => MYSQL_EXCEPTION]);
                    break;
                default:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION]);
                    break;
            }
            die();
        }

        echo formatResponse(200, ["Content-Type" => "application/json"],
            ["success" => true, "articles" => $info]);
    }

    /**
     * returns all articles of a specific partner
     * @httpmethod get
     * @return void
     */
    public static function getArticles(){
        include __DIR__."/../models/Catalog.php";

        $json = json_decode(file_get_contents("php://input"));

        if(!isset($json->id_partner)||empty($json->id_partner))
            reportMissingParam("id_partner");

        if(!isset($json->number)||empty($json->number)) $number = null;
        else $number = $json->number;

        if(!isset($json->page)||empty($json->page)) $page = null;
        else $page = $json->page;

        try {
            if($page === null || $number === null) $info = Catalog::getAllArticles($json->id_partner);
            else $info = Catalog::getArticles($json->id_partner, $number, $page);
        }catch (Exception $e){
            switch ($e->getCode()){
                case COMPANY_NOT_FOUND:
                    echo formatResponse(400, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Company does not exist", "errorCode" => COMPANY_NOT_FOUND]);
                    break;

                case MYSQL_EXCEPTION:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Database Error", "errorCode" => MYSQL_EXCEPTION]);
                    break;

                default:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION]);
                    break;
            }
            die();
        }

        echo formatResponse(200, ["Content-Type" => "application/json"],
            ["success" => true, "table" => $info]);

    }

    /**
     * returns n articles based on parameter:
     * optional param: partner id
     * main param: "mode": "alpha" VS "chrono"
     * optional param: reverse true|false
     * reverse mode
     * @httpmethod get
     * @return void
     */
    public static function orderedSearch(){
        include __DIR__."/../models/Catalog.php";

        $json = json_decode(file_get_contents("php://input"));

        $id = intval($json->id_partner) ?? -1;

        if(!isset($json->mode)||empty($json->mode))
            reportMissingParam("mode");

        if(!isset($json->number)||empty($json->number)) $number = null;
        else $number = $json->number;

        if(!isset($json->page)||empty($json->page)) $page = null;
        else $page = $json->page;

        $reverse = $json->reverse ?? false;

        switch ($json->mode){
            case "alpha": $mode = ALPHABETICAL_ORDER; break;
            case "chrono": $mode = CHRONOLOGICAL_ORDER; break;
            default :
                echo formatResponse(400, ["Content-Type" => "application/json"],
                    ["success" => false, "errorMessage" => "Not an ordering mode", "errorCode" => INVALID_ORDER]);
                die();
        }

        try {
            if($page === null || $number === null) $info = Catalog::orderedSearchAll($id, $mode, $reverse);
            else $info = Catalog::orderedSearch($id, $mode, $reverse, $number, $page);
        }catch (Exception $e){
            switch ($e->getCode()){
                case INVALID_ORDER:
                    echo formatResponse(400, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Not an ordering mode", "errorCode" => INVALID_ORDER]);
                    break;

                case MYSQL_EXCEPTION:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Database Error", "errorCode" => MYSQL_EXCEPTION]);
                    break;

                default:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION]);
                    break;
            }
            die();
        }

        echo formatResponse(200, ["Content-Type" => "application/json"],
            ["success" => true, "table" => $info]);

    }

    /**
     *
     * Utility Functions
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

        if($paramName == "price") {
            $inputCleaned = str_replace('.', '', $param);
            $inputCleaned = str_replace(',', '', $inputCleaned);
            $inputCleaned = str_replace(' ', '', $inputCleaned);
            if (!is_numeric($inputCleaned))
                throw new Exception("Parameter incorrect", INVALID_PARAMETER);
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
                case "name":
                    $min = 2;
                    $max = 50;
                    break;
                case "description":
                    $max = 2000;
                    break;
                case "price":
                    $min = 1;
                    $max = 10;
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
}