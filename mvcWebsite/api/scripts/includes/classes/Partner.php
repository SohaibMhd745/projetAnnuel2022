<?php

class Partner extends User {
    private $partnerName;
    private $partnerInscription;
    private $revenue;
    private $website;
    private $id_sponsor;


    //@Override user constructFromEmailAndPassword
    public function constructFromEmailAndPassword(string $email, string $password, DbLink $link){
        parent::constructFromEmailAndPassword($email, $password, $link);
        $this->constructCompany($link);
    }

    //@Override user constructFromEmailAndPassword
    public function constructFromId(int $id, DbLink $link){
        parent::constructFromId($id, $link);
        $this->constructCompany($link);
    }

    //@Override continuation of both overrides,
    // method to create is the exact same (based on id_partner)
    private function constructCompany($link){
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