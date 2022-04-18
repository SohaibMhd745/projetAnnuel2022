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

    /**
     * @httpmethod GET
     * @param string $token user auth token
     * @param string $confirmCode order confirmation code
     * @return void
     */
    public static function completeOrder(string $token, string $confirmCode){
        include __DIR__.'/../models/User.php';
        include __DIR__.'/../models/Order.php';

        try{
            $orderCode = Order::getOrderConfirm($token);
            if ($orderCode !== $confirmCode){
                //TODO Redirect to 401 error page
                die();
            }

            $price = Order::getOrderTotal(Order::getCurrentOrder($token));

            $user = new User();
            $user->constructFromToken($token);
            $user->updatePoints($user->getPoints()+self::euroToPoints($price));

            Order::finalizeOrder($confirmCode);
        }catch (Exception $e){
            switch ($e->getCode()){
                //TODO: error pages
                case INVALID_AUTH_TOKEN:
                    //TODO Redirect to 401 error page
                    break;
                default:
                    //TODO Redirect to error 500 page
                    break;
            }
            die();
        }
        header("Location: /");
        exit();
    }

    /**
     * @httpmethod GET
     * @param string $token
     * @return void
     */
    public static function orderCheckout(string $token){
        include __DIR__."/../models/User.php";
        include __DIR__.'/../models/Order.php';

        try{
            $oId = Order::getCurrentOrder($token);
            $cart = Order::getStripeInfo($oId);
            $orderCode = Order::getOrderConfirm($token);
        }catch (Exception $e) {
            switch ($e->getCode()) {
                case INVALID_AUTH_TOKEN:
                    //TODO page 401
                    break;
                default:
                    //TODO page 500
                    break;
            }
            die();
        }

        if ($cart === []) {
            //TODO rediriger sur le cart
            die();
        }

        if ($orderCode === "") {
            //TODO page 401
            die();
        }

        \Stripe\Stripe::setApiKey(STRIPE_KEY);

        try{
            header('Content-Type: application/json');
            $checkout_session = \Stripe\Checkout\Session::create([
                'line_items' => $cart,
                'mode' => 'payment',
                'success_url' => DOMAIN_NAME . 'checkout/completeorder/'.$token.'/'.$orderCode,

                //TODO: redirection page cart
                'cancel_url' => DOMAIN_NAME . 'cancel'
            ]);
        }catch (Exception $e){
            echo formatResponse(500, ["Content-Type" => "application/json"],
                ["success" => false, "errorMessage" => "Database error", "errorCode" => OUTSIDE_API_EXCEPTION, "step" => "Stripe connection"]);
            die();
        }


        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);

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

    // 1 point = 0.2€
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