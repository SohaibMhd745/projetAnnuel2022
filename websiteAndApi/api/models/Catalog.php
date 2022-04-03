<?php

class Catalog
{
    private int $id;
    private string $description;
    private float $price;
    private int $id_partner;

    /**
     * Returns all articles of the company with the specified id
     * @param int $id id of the partner
     * @return array array of found articles
     * @throws Exception:
     * - COMPANY_NOT_FOUND if provided id is incorrect
     * - MYSQL_EXCEPTION in case of database errors
     */
    public static function getAllArticles(int $id):array{
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $q = "SELECT id, name, description, price FROM akm_prestation WHERE id_partner = :id";
        $res = $link->queryAll($q, ["id"=>$id]);

        if($res === false) throw new Exception("Invalid partner id", COMPANY_NOT_FOUND);
        else if($res === MYSQL_EXCEPTION) throw new Exception("Error while trying to access database", MYSQL_EXCEPTION);
        else return $res;
    }


    /**
     * Returns all articles with names similar to input
     * @param string $term search term
     * @return array results of the search
     * @throws Exception MYSQL_EXCEPTION in case of database failure
     */
    public static function searchArticles(string $term):array{
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $q = "SELECT id, name, description, price FROM akm_prestation WHERE name LIKE '%".sanitizeStringQuotes($term)."%'";
        $res = $link->queryAll($q, []);

        if($res === false) return [];
        else if($res === MYSQL_EXCEPTION) throw new Exception("Error while trying to access database", MYSQL_EXCEPTION);
        else return $res;
    }

    /**
     * Builds into structure
     * @param string $token auth token of the user in charge of the partner
     * @throws Exception:
     * - INVALID_AUTH_TOKEN if token is invalid
     * - MYSQL_EXCEPTION in case of database errors
     */
    public function buildArticle(string $token, int $id){
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $q = "SELECT id, description, description, price, id_partner FROM akm_prestation WHERE id_partner = (SELECT id_partner FROM akm_users WHERE token = :token) AND id = :id";
        $res = $link->query($q, ["token"=>$token, "id" => $id]);

        if($res === false) throw new Exception("Invalid auth token", INVALID_AUTH_TOKEN);
        else if($res === MYSQL_EXCEPTION) throw new Exception("Error while trying to access database", MYSQL_EXCEPTION);
        else{
            $this->id = $id;
            $this->id_partner = $res["id_partner"];
            $this->price = $res["price"];
            $this->description = $res["descriptino"];
        }
    }

    /**
     * @param string $token
     * @param string $name
     * @param float $price
     * @param string $description
     * @throws Exception:
     * - INVALID_AUTH_TOKEN if provided token is invalid
     * - MYSQL_EXCEPTION in case of critical database failure
     * - COMPANY_NOT_FOUND if the user is not in control of a database
     */
    public static function addArticle(string $token, string $name, float $price, string $description){
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        include __DIR__.'/User.php';

        $user = new User();
        $user->constructFromToken($token);

        if ($user->getIdPartner()===-1) throw new Exception("User does not manage a company", COMPANY_NOT_FOUND);

        $q = "INSERT INTO akm_prestation (name, description, price, id_partner) VALUES(:name, :description, :price, :id_partner)";

        $status = $link->insert($q, ["name"=>$name,"description" => $description, "price" => $price, "id_partner" => $user->getIdPartner()]);

        if ($status !== true) throw new Exception("Critical Database Failure", MYSQL_EXCEPTION);
    }

    /**
     *
     * Getters
     *
     */

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getIdPartner(): int
    {
        return $this->id_partner;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }
}