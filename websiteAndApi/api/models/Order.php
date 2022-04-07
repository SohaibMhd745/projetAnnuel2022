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

        include __DIR__.'/User.php';

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
     * ORDER_ALREADY_EXISTS
     */
    public static function getCurrentOrder(string $token):int{
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        include __DIR__.'/User.php';

        $user = new User();
        $user->constructFromToken($token);
        //Can throw MYSQL AUTH TOKEN exceptions

        $select = "SELECT id FROM akm_order WHERE id_user = :id AND ordered = false";
        $status = $link->query($select, ["id"=>$user->getId()]);

        if($status === false) return -1;
        else return $status["id"];
    }
}