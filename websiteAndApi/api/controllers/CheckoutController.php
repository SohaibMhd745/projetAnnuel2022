<?php

class CheckoutController
{

    /**
     * @httpmethod POST
     * @return void
     */
    public static function partnerCheckout(){
        include __DIR__."/../models/User.php";
        include __DIR__."/../models/Partner.php";

        $json = json_decode(file_get_contents("php://input"));

        if (!isset($json->token) || empty($json->token))
            reportMissingParam("token");
        if (!isset($json->partner_token) || empty($json->partner_token))
            reportMissingParam("partner_token");
        if (!isset($json->price) || empty($json->price))
            reportMissingParam("price");

        if(!is_numeric($json->price)){
            reportInvalidParam("price");
            die();
        }

        $user = self::attemptUserConnection($json->token);

        try {
            Partner::checkApiToken($json->partner_token);
        }catch (Exception $e) {
            switch ($e->getCode()) {
                case INVALID_PARTNER_TOKEN:
                    echo formatResponse(401, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Invalid API token", "errorCode" => INVALID_PARTNER_TOKEN, "step" => "Partner Authentication"]);
                    break;
                case MYSQL_EXCEPTION:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION, "step" => "Partner Authentication"]);
                    break;
                default:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION, "step" => "Partner Authentication"]);
                    break;
            }
            die();
        }

        $points = $user->getPoints() + self::convertToPoints($json->price);
        try {
            $user->updatePoints($points);
        }catch (Exception $e) {
            switch ($e->getCode()) {
                case MYSQL_EXCEPTION:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION, "step" => "Points update"]);
                    break;
                default:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION, "step" => "Points update"]);
                    break;
            }
            die();
        }

        echo formatResponse(200, ["Content-Type" => "application/json"],
            ["success" => true, "points"=>$points]);
    }


    /**
     * Converts raw price to points
     * @param float $price price to be converted
     * @return int price in points
     */
    private static function convertToPoints(float $price):int{
        $price_int = intval($price);
        $price_dec = $price - $price_int;

        $price_int += ($price_dec >= 0.5 ? 1:0);

        $rawBonus = 0.3*$price_int + (intval($price_int/100));

        $bonus_int = intval($rawBonus);
        $bonus_dec = $rawBonus - $bonus_int;

         return $bonus_int + ($bonus_dec >= 0.5 ? 1:0);
    }

    /**
     *
     * Utility functions
     *
     */

    /**
     * Attempts user connection, breaks execution in case of error, returns user otherwise
     * @param string $token token to attempt connection with
     * @return User built structure from token
     */
    private static function attemptUserConnection(string $token) : User{
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
}