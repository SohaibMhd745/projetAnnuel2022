<?php

class Partner extends User {
    private string $partnerName;
    private string $partnerInscription;
    private int $revenue;
    private string $website;
    private int $id_sponsor;


    //@Override user constructFromEmailAndPassword
    public function constructFromEmailAndPassword(string $email, string $password){
        parent::constructFromEmailAndPassword($email, $password);
        $this->constructCompany();
    }

    //@Override user constructFromEmailAndPassword
    public function constructFromId(int $id){
        parent::constructFromId($id);
        $this->constructCompany();
    }

    //@Override user constructFromToken
    public function constructFromToken(string $token)
    {
        parent::constructFromToken($token);
        $this->constructCompany();
    }

    //@Override continuation of both overrides,
    // method to create is the exact same (based on id_partner)
    private function constructCompany(){
        include __DIR__."../scripts/include_scripts.php";

        include __DIR__."../database/CREDENTIALS.php";
        include __DIR__."../database/DbLink.php";

        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $q = "SELECT name, inscription, revenue, website, id_sponsor FROM akm_partners WHERE id = ?";
        $res = $link->query($q, [$this->id_partner]);

        if($res === false) throw new Exception("Partner does not exist", COMPANY_NOT_FOUND);
        else if($res === MYSQL_EXCEPTION) throw new Exception("Error while trying to access database", MYSQL_EXCEPTION);
        else{
            $this->partnerName = $res["name"];
            $this->partnerInscription = $res["partnerInscription"];
            $this->revenue = $res["revenue"];
            $this->website = $res["website"];
            $this->id_sponsor = $res["id_sponsor"];
        }
    }

    /**
     *
     * Getters
     *
     */

    /**
     * @return mixed
     */
    public function getPartnerName()
    {
        return $this->partnerName;
    }

    /**
     * @return mixed
     */
    public function getPartnerInscription()
    {
        return $this->partnerInscription;
    }

    /**
     * @return mixed
     */
    public function getRevenue()
    {
        return $this->revenue;
    }


    /**
     * @return mixed
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @return mixed
     */
    public function getSponsorId()
    {
        return $this->id_sponsor;
    }

}