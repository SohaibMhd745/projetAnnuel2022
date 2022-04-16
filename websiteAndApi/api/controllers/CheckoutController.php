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
            //Reset token instantly after use for safety reasons
            $user->updateToken();
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



    private static function stripePayment(){
        include __DIR__.'/../models/Order.php';

        if (!isset($json->token) || empty($json->token))
            reportMissingParam("token");

        try{
            $oId = Order::getCurrentOrder($json->token);
            //new function in the controlelr for product id and price id
            //$cart = Order::($oId);
        }catch (Exception $e) {
            switch ($e->getCode()) {
                case INVALID_AUTH_TOKEN:
                    echo formatResponse(401, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Invalid auth token", "errorCode" => INVALID_AUTH_TOKEN, "step" => "Get Order Info"]);
                    break;
                case MYSQL_EXCEPTION:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION, "step" => "Get Order Info"]);
                    break;
                default:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION, "step" => "Get Order Info"]);
                    break;
            }
            die();
        }
        /*
        if ($cart === []) {
            echo formatResponse(400, ["Content-Type" => "application/json"],
                ["success" => false, "errorMessage" => "Cannot proceed to payment because cart is empty", "errorCode" => ORDER_EMPTY]);
            die();
        }
        */

        /**
         *
         * [[
         * # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
         * 'price' => '{{PRICE_ID}}',
         * 'quantity' => 1,
         * ]]
         *
         */

        \Stripe\Stripe::setApiKey(STRIPE_KEY);

        //TODO: replace domain once online
        $YOUR_DOMAIN = 'http://localhost/';

        /*
        try{
            header('Content-Type: application/json');
            $checkout_session = \Stripe\Checkout\Session::create([
                'line_items' => $stripe_cart,
                'mode' => 'payment',
                //TODO: test values
                //Visa test value: 4242 4242 4242 4242 ??? ??/??
                //'payment_method' => 'pm_card_visa',
                'success_url' => $YOUR_DOMAIN . '/reussite',
                'cancel_url' => $YOUR_DOMAIN . '/echec',
            ]);
        }catch (Exception $e){
            echo formatResponse(500, ["Content-Type" => "application/json"],
                ["success" => false, "errorMessage" => "Database error", "errorCode" => OUTSIDE_API_EXCEPTION, "step" => "Stripe connection"]);
            die();
        }


        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);
        */
    }

    /**
     * Converts raw price to points when buying
     * @param float $price price to be converted
     * @return int price in points
     */
    public static function convertToPoints(float $price):int{
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

    //1 point = 0.2€
    /**
     * Converts points to price
     * @param float $points points
     * @return float price
     */
    public static function pointToEuros(float $points):float{
        return $points*0.2;
    }

    /**
     * Converts price to points
     * @param float $price price
     * @return float points
     */
    public static function euroToPoints(float $price):float{
        return $price/0.2;
    }

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