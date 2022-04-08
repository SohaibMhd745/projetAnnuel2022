<?php

class Order
{

    /**
     * Adds and activate a new order for specified user
     * @param string $token
     * @return void
     * @throws Exception
     * MYSQL_EXCEPTION
     * INVALID_AUTH_TOKEN
     * ORDER_ALREADY_EXISTS
     */
    public static function createOrder(string $token){
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $user = new User();
        $user->constructFromToken($token);
        //Can throw MYSQL AUTH TOKEN exceptions

        $select = "SELECT id FROM akm_order WHERE id_user = :id AND ordered = false";
        $status = $link->query($select, ["id"=>$user->getId()]);

        if($status !== false) throw new Exception("User already has an active order", ORDER_ALREADY_EXISTS);

        $q = "INSERT INTO akm_order(id_user, order_time, cost, ordered) VALUES(:id, :now, 0, false)";

        $status = $link->insert($q, ["id"=>$user->getId(),"now" => getYearsAgo(0)]);

        if ($status !== true) throw new Exception("Critical Database Failure", MYSQL_EXCEPTION);

    }

    /**
     * returns id for user's current active order or -1 if none is active
     * @param string $token
     * @return int
     * @throws Exception
     * MYSQL_EXCEPTION
     * INVALID_AUTH_TOKEN
     */
    public static function getCurrentOrder(string $token):int{
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $user = new User();
        $user->constructFromToken($token);
        //Can throw MYSQL & AUTH TOKEN exceptions

        $select = "SELECT id FROM akm_order WHERE id_user = :id AND ordered = false";
        $status = $link->query($select, ["id"=>$user->getId()]);

        if($status === false) return -1;
        else return $status["id"];
    }

    /**
     * Returns the amount of a certain prestation in cart with specific id
     * @param int $pId prestation id
     * @param int $oId order id
     * @return int -1 if link does not exist, the amount otherwise
     * @throws Exception MYSQL_EXCEPTION
     */
    public static function getAmount(int $pId, int $oId):int{
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $q = "SELECT amount FROM akm_cart WHERE id_order = :oid AND id_prestation = :pid";

        $res = $link->query($q, ["oid"=>$oId, "pid"=>$pId]);

        if ($res === false) return -1;
        else if($res === MYSQL_EXCEPTION) throw new Exception("Database Error", MYSQL_EXCEPTION);
        else return $res["amount"];
    }

    /**
     *
     * @param int $pId Prestation id
     * @param int $oId order id
     * @param int $new new value after update
     * @return void
     * @throws Exception
     * MYSQL_EXCEPTION
     * NOT_IN_CART
     */
    public static function setAmount(int $pId, int $oId, int $new){
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $q = "UPDATE akm_cart SET amount = :new WHERE id_order = :oid AND id_prestation = :pid";

        $status = $link->insert($q, ["oid"=>$oId, "pid"=>$pId, "new"=>$new]);

        if ($status === MYSQL_EXCEPTION) throw new Exception("Database Error", MYSQL_EXCEPTION);
        if ($status === false) throw new Exception("Item not in cart", NOT_IN_CART);
    }

    /**
     * Adds relation between order and prestation
     * @param int $oId order id
     * @param int $pId prestation id
     * @param int $amount initial amount
     * @return void
     * @throws Exception
     * MYSQL_EXCEPTION
     * ALREADY_IN_CART
     */
    public static function addToCart(int $oId, int $pId, int $amount){
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $q = "INSERT INTO akm_cart(id_order, id_prestation, amount) VALUES (:oid, :pid, :amount)";

        try {
            $link->insert($q, ["oid" => $oId, "pid" => $pId, "amount"=>$amount]);
        }catch (Exception $e){
            if($e->getCode() === MYSQL_EXCEPTION) throw new Exception("Database Error", MYSQL_EXCEPTION);
            else throw new Exception("Prestation already in cart", ALREADY_IN_CART);
        }
    }

    /**
     * Returns detailed cart information
     * @param int $oId order id
     * @return array|mixed Array of prestation info with cart info
     * @throws Exception
     * MYSQL_EXCEPTION
     */
    public static function getCartInfo(int $oId){
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $q = "SELECT ak_p.id as id, ak_p.name as name, ak_p.price as individualprice,
            ak_c.amount as quantity, (ak_c.amount*ak_p.price) as subtotal 
            FROM akm_prestation as ak_p LEFT JOIN akm_cart as ak_c ON ak_p.id = ak_c.id_prestation
            WHERE ak_c.id_order = :oid";

        $res = $link->queryAll($q, ["oid" => $oId]);

        if ($res === false) return [];
        else if($res === MYSQL_EXCEPTION) throw new Exception("Database Error", MYSQL_EXCEPTION);
        else return $res;
    }

    /**
     * Remove item from cart
     * @param int $oId order id
     * @param int $pId prestation id
     * @return void
     * @throws Exception
     * MYSQL_EXCETPION
     * NOT_IN_CART
     */
    public static function removeFromCart(int $oId, int $pId){
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $q = "DELETE FROM akm_cart WHERE id_order = :oid AND id_prestation = :pid";

        $status = $link->insert($q, ["oid"=>$oId, "pid"=>$pId]);

        if ($status === MYSQL_EXCEPTION) throw new Exception("Database Error", MYSQL_EXCEPTION);
        if ($status !== true) throw new Exception("Item not in cart", NOT_IN_CART);
    }
}