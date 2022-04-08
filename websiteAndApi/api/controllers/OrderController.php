<?php

class OrderController
{
    /**
     * Returns cart info
     * @httpmethod get
     * @return void
     */
    public static function getOrder(){
        include __DIR__."/../models/Order.php";
        include __DIR__."/../models/User.php";

        $json = json_decode(file_get_contents("php://input"));

        if (!isset($json->token) || empty($json->token))
            reportMissingParam("token");

        $id = self::getOrderId($json->token);

        try {
            $info = Order::getCartInfo($id);
        }catch (Exception $e){
            switch ($e->getCode()) {
                case MYSQL_EXCEPTION:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION, "step" => "Cart Info"]);
                    break;
                default:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION, "step" => "Cart Info"]);
                    break;
            }
            die();
        }

        echo formatResponse(200, ["Content-Type" => "application/json"],
            ["success" => true, "table" => $info]);
    }

    /**
     * Sets new cart amount using change parameter as delta, also handles deletion from cart and insertion into cart
     * @httpmethod update
     * @return void
     */
    public static function setNewCartAmount(){
        include __DIR__."/../models/Order.php";
        include __DIR__."/../models/User.php";

        $json = json_decode(file_get_contents("php://input"));

        if (!isset($json->token) || empty($json->token))
            reportMissingParam("token");
        if (!isset($json->id_prestation) || empty($json->id_prestation))
            reportMissingParam("id_prestation");
        if (!isset($json->change) || empty($json->change))
            reportMissingParam("change");

        $id = self::getOrderId($json->token);

        try{
            $currAmount = Order::getAmount($json->id_prestation, $id);
        }catch (Exception $e){
            switch ($e->getCode()) {
                case MYSQL_EXCEPTION:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION, "step" => "Get Current Cart Amount"]);
                    break;
                default:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION, "step" => "Get Current Cart Amount"]);
                    break;
            }
            die();
        }


        $new = $currAmount+$json->change;
        if ($currAmount === -1 && $json->change <= 0){
            echo formatResponse(500, ["Content-Type" => "application/json"],
                ["success" => false, "errorMessage" => "Item doesn't exist in cart", "errorCode" => NOT_IN_CART, "step" => "Calculating new amount"]);
            die();
        }
        else if ($new <= 0 && $currAmount !== -1) {
            try {
                Order::removeFromCart($id, $json->id_prestation);
            }catch (Exception $e){
                switch ($e->getCode()) {
                    case MYSQL_EXCEPTION:
                        echo formatResponse(500, ["Content-Type" => "application/json"],
                            ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION, "step" => "Remove from Cart"]);
                        break;
                    case NOT_IN_CART:
                        echo formatResponse(500, ["Content-Type" => "application/json"],
                            ["success" => false, "errorMessage" => "Could not assess item's presence in cart", "errorCode" => NOT_IN_CART, "step" => "Remove from Cart"]);
                        break;
                    default:
                        echo formatResponse(500, ["Content-Type" => "application/json"],
                            ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION, "step" => "Remove from Cart"]);
                        break;
                }
                die();
            }
        }else{
            try{
                if ($currAmount === -1) Order::addToCart($id, $json->id_prestation, $json->change);
                else Order::setAmount($json->id_prestation,$id,$new);
            }catch (Exception $e){
                switch ($e->getCode()) {
                    case MYSQL_EXCEPTION:
                        echo formatResponse(500, ["Content-Type" => "application/json"],
                            ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION, "step" => "Set new amount"]);
                        break;
                    case ALREADY_IN_CART:
                        echo formatResponse(500, ["Content-Type" => "application/json"],
                            ["success" => false, "errorMessage" => "Could not assess item's presence in cart", "errorCode" => ALREADY_IN_CART, "step" => "Insert in Cart"]);
                        break;
                    case NOT_IN_CART:
                        echo formatResponse(500, ["Content-Type" => "application/json"],
                            ["success" => false, "errorMessage" => "Item not in cart", "errorCode" => NOT_IN_CART, "step" => "Set new amount"]);
                        break;
                    default:
                        echo formatResponse(500, ["Content-Type" => "application/json"],
                            ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION, "step" => "Set new amount"]);
                        break;
                }
                die();
            }
        }
        echo formatResponse(200, ["Content-Type" => "application/json"],
            ["success" => true]);
    }

    /**
     *
     * Utility functions
     *
     */

    /**
     * Returns order id for current active order
     * Calls setOrder and then itself again if there are no active orders
     * @param string $token
     * @return int
     */
    private static function getOrderId(string $token):int{
        try {
            $id = Order::getCurrentOrder($token);
        }catch (Exception $e){
            switch ($e->getCode()) {
                case INVALID_AUTH_TOKEN:
                    echo formatResponse(400, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Auth token is not valid", "errorCode" => INVALID_AUTH_TOKEN]);
                    break;
                case MYSQL_EXCEPTION:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION, "step" => "Order ID Get"]);
                    break;
                default:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION, "step" => "Order ID Get"]);
                    break;
            }
            die();
        }
        if($id === -1) {
            self::setOrder($token);
            return self::getOrderId($token);
        }else return $id;
    }


    /**
     * Called if there are none active orders, creates an active order
     * @param string $token
     * @return void
     */
    private static function setOrder(string $token){
        try {
            Order::createOrder($token);
        }catch(Exception $e){
            switch ($e->getCode()) {
                case INVALID_AUTH_TOKEN:
                    echo formatResponse(400, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Auth token is not valid", "errorCode" => INVALID_AUTH_TOKEN]);
                    break;
                case ORDER_ALREADY_EXISTS:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Could not detect active order", "errorCode" => ORDER_ALREADY_EXISTS]);
                    break;
                case MYSQL_EXCEPTION:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Database error", "errorCode" => MYSQL_EXCEPTION, "step" => "Order Creation"]);
                    break;
                default:
                    echo formatResponse(500, ["Content-Type" => "application/json"],
                        ["success" => false, "errorMessage" => "Fatal error", "errorCode" => FATAL_EXCEPTION, "step" => "Order Creation"]);
                    break;
            }
            die();
        }
    }
}