<?php

use Stripe\StripeClient;

class CheckoutController
{

    /**
     * @httpmethod POST
     * @return void
     */
    public static function partnerCheckout(){
        include __DIR__."/../models/User.php";
        include __DIR__."/../models/Partner.php";
        include __DIR__."Login.php";

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

        $user = Login::attemptConnection($json->token);

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
     *
     * User order checkout
     *
     */

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
                header('Location: /error/401');
                exit();
            }

            $price = Order::getOrderTotal(Order::getCurrentOrder($token));

            $user = new User();
            $user->constructFromToken($token);
            $user->updatePoints($user->getPoints()+self::euroToPoints($price));

            Order::finalizeOrder($confirmCode);
        }catch (Exception $e){
            switch ($e->getCode()){
                case INVALID_AUTH_TOKEN:
                    header('Location: /error/401');
                    break;
                default:
                    header('Location: /error/500');
                    break;
            }
            exit();
        }
        //include __DIR__.'/../scripts/push.php';
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
                    header('Location: /error/401');
                    break;
                default:
                    header('Location: /error/500');
                    break;
            }
            exit();
        }

        if ($cart === []) {
            header('Location: /cart');
            exit();
        }

        if ($orderCode === "") {
            header('Location: /error/401');
            exit();
        }

        \Stripe\Stripe::setApiKey(STRIPE_KEY);

        try{
            header('Content-Type: application/json');
            $checkout_session = \Stripe\Checkout\Session::create([
                'line_items' => $cart,
                'mode' => 'payment',
                'success_url' => DOMAIN_NAME . 'checkout/completeorder/'.$token.'/'.$orderCode,
                'cancel_url' => DOMAIN_NAME . '/cart'
            ]);
        }catch (Exception $e){
            echo formatResponse(500, ["Content-Type" => "application/json"],
                ["success" => false, "errorMessage" => "Api error", "errorCode" => OUTSIDE_API_EXCEPTION, "step" => "Stripe connection"]);
            die();
        }


        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);
    }

    /**
     * @httpmethod POST
     * @return void
     */
    public static function pointsCheckout(){
        include __DIR__."/../models/User.php";
        include __DIR__."/../models/Order.php";
        include __DIR__."/Login.php";

        $json = json_decode(file_get_contents("php://input"));

        if (!isset($json->token) || empty($json->token))
            reportMissingParam("token");

        $user = Login::attemptConnection($json->token);

        try{
            $oId = Order::getCurrentOrder($json->token);
            $total = Order::getOrderTotal($oId);
            $confirmCode = Order::getOrderConfirm($json->token);
        }catch (Exception $e) {
            switch ($e->getCode()) {
                case MYSQL_EXCEPTION:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION]);
                    break;
                default:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Unexpected error", "errorCode" => FATAL_EXCEPTION]);
                    break;
            }
            die();
        }

        if ($total === 0.0){
            echo formatResponse(401, ["Content-Type" => "application/json"],
                ["success" => false, "errorMessage" => "Cart is empty", "errorCode" => ORDER_EMPTY]);
            die();
        }

        $pointsNeeded = self::euroToPoints($total);

        if($user->getPoints() < $pointsNeeded){
            echo formatResponse(401, ["Content-Type" => "application/json"],
                ["success" => false, "errorMessage" => "Not enough points", "errorCode" => NOT_ENOUGH_POINTS]);
            die();
        }

        try{
            $user->updatePoints($user->getPoints()-$pointsNeeded);
            Order::finalizeOrder($confirmCode);
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case MYSQL_EXCEPTION:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION]);
                    break;
                default:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Unexpected error", "errorCode" => FATAL_EXCEPTION]);
                    break;
            }
            die();
        }

        echo formatResponse(200, ["Content-Type" => "application/json"], ["success" => true]);
    }

    /**
     *
     * Partner Subscription checkout
     *
     */

    /**
     * Returns status for the payment of the partner annual subscription
     * @httpmethod POST
     * - false: still has to be paid
     * - true: has been paid
     */
    public static function getPartnerSubscriptionStatus(){
        include __DIR__."/../models/User.php";
        include __DIR__."/../models/Partner.php";
        include __DIR__."/Login.php";

        $json = json_decode(file_get_contents("php://input"));

        if (!isset($json->token) || empty($json->token))
            reportMissingParam("token");

        $partner = self::createPartner($json->token);

        $status = $partner->returnSubscriptionStatus();
        if(!$status) $amount = self::getSubscriptionPrice($partner->getRevenue());
        else $amount = 0;

        echo formatResponse(200, ["Content-Type" => "application/json"],
            ["success" => true, "status"=>$status, "amount"=>$amount]);
    }

    /**
     * Changes revenue stored in the database
     * @httpmethod POST
     * @return void
     */
    public static function declareRevenue(){
        include __DIR__."/../models/User.php";
        include __DIR__."/../models/Partner.php";
        include __DIR__."/Login.php";

        $json = json_decode(file_get_contents("php://input"));

        if (!isset($json->token) || empty($json->token))
            reportMissingParam("token");

        if (!isset($json->revenue) || empty($json->revenue))
            reportMissingParam("revenue");

        $partner = self::createPartner($json->token);

        if(!is_numeric($json->revenue)) {
            echo formatResponse(400, ["Content-Type" => "application/json"],
                ["success" => false, "errorMessage" => "Invalid value", "errorCode" => INVALID_PARAMETER, "step" => "Partner Authentication"]);
            die();
        }
        try{
            $stripe = new StripeClient(STRIPE_KEY);

            $stripe->prices->update(
                $partner->getIdStripe(),
                ["active"=>false]
            );//We can't delete a price with the API, so we archive it instead

            $id_stripe = $stripe->prices->create([
                'unit_amount' => (int) self::getSubscriptionPrice($json->revenue)*100,
                'currency' => 'eur',
                'product' => SUBSCRIPTION_PRODUCT_ID
            ])->id;

            $partner->updateRevenue((int)$json->revenue, $id_stripe);
        }catch (Exception $e){
            switch ($e->getCode()) {
                case MYSQL_EXCEPTION:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION, "step" => "Revenue update"]);
                    break;
                default:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION, "step" => "Revenue update"]);
                    break;
            }
            die();
        }

        echo formatResponse(200, ["Content-Type" => "application/json"],
            ["success" => true]);
    }

    /**
     * @httpmethod GET
     * @param string $token user auth token
     * @param string $confirmCode subscription confirmation code
     * @return void
     */
    public static function completeSubscriptionPayment(string $token, string $confirmCode){
        include __DIR__.'/../models/User.php';
        include __DIR__.'/../models/Order.php';
        include __DIR__.'/../models/Partner.php';
        include __DIR__.'/Login.php';

        try{
            $partner = self::createPartner($token);
            $orderCode = $partner->getSubscriptionCode();
            if ($orderCode===''||$orderCode !== $confirmCode){
                header('Location: /error/401');
                exit();
            }
            $partner->updateSubscriptionPaymentDate();
            $partner->resetSubscriptionCode();
        }catch (Exception $e){
            switch ($e->getCode()){
                case INVALID_AUTH_TOKEN:
                    header('Location: /error/401');
                    break;
                default:
                    header('Location: /error/500');
                    break;
            }
            exit();
        }
        header("Location: /");
    }

    /**
     * @httpmethod GET
     * @param string $token
     * @return void
     */
    public static function subscriptionPayment(string $token){
        include __DIR__."/../models/User.php";
        include __DIR__.'/../models/Order.php';
        include __DIR__.'/../models/Partner.php';
        include __DIR__.'/Login.php';

        try{
            $partner = self::createPartner($token);
            $code = $partner->updateSubscriptionCode();
        }catch (Exception $e) {
            switch ($e->getCode()) {
                case INVALID_AUTH_TOKEN:
                    header('Location: /error/401');
                    break;
                default:
                    header('Location: /error/500');
                    break;
            }
            exit();
        }

        if ($partner->returnSubscriptionStatus()){
            header('Location: /error/401');
            exit();
        }

        if(self::getSubscriptionPrice($partner->getRevenue()) < 0.0){
            header('Location: /error/401');
            exit();
        }

        \Stripe\Stripe::setApiKey(STRIPE_KEY);

        try{
            header('Content-Type: application/json');
            $checkout_session = \Stripe\Checkout\Session::create([
                'line_items' => [[
                    "price"=>$partner->getIdStripe(),
                    "quantity"=>1
                ]],
                'mode' => 'payment',
                'success_url' => DOMAIN_NAME . 'checkout/completesubscriptionpayment/'.$token.'/'.$code,
                'cancel_url' => DOMAIN_NAME . '/account'
            ]);
        }catch (Exception $e){
            echo formatResponse(500, ["Content-Type" => "application/json"],
                ["success" => false, "errorMessage" => "Api error", "errorCode" => OUTSIDE_API_EXCEPTION, "step" => "Stripe connection"]);
            die();
        }


        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);

    }

    /**
     *
     * Utility functions
     *
     */

    /**
     * makes the appropriate checks and creates a partner object
     * @param string $token user token
     * @return Partner Corresponsding user
     */
    private static function createPartner(string $token):Partner{
        $user = Login::attemptConnection($token);
        if ($user->getIdPartner() === -1){
            echo formatResponse(401, ["Content-Type" => "application/json"],
                ["success" => false, "errorMessage" => "Account is not linked to a partner company", "errorCode" => COMPANY_NOT_FOUND, "step" => "Partner Authentication"]);
            die();
        }

        return Login::attemptPartnerConnection($token);
    }

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
        return round($price/0.2);
    }

    /**
     * Converts raw price to points when buying from the store
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
     * Converts revenue to subscription price
     * @param int $revenue
     * @return float subscription price
     */
    public static function getSubscriptionPrice(int $revenue):float{
        $i = 0;
        while (SUBSCRIPTION_POLICY[$i]["upper_limit"]!==-1){
            if($revenue < SUBSCRIPTION_POLICY[$i]["upper_limit"])
                return round((SUBSCRIPTION_POLICY[$i]["percentage"]*(float)$revenue)/100,2);
            else $i++;
        }
        return round((SUBSCRIPTION_POLICY[$i]["percentage"]*(float)$revenue)/100,2);
    }
}