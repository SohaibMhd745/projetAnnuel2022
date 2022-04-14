<?php

class Partner extends User {
    private string $partnerName;
    private string $partnerInscription;
    private int $revenue;
    private string $website;
    private $id_sponsor;


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

    /**
     * Construct partner class from partner id
     * @param int $id : user id
     * @throws Exception : USER_NOT_FOUND | MYSQL_EXCEPTION
     */
    public function constructFromPartnerId(int $id){
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $q = "SELECT id, email, firstname, lastname, inscription, birthdate, phone, id_partner, points FROM akm_users WHERE id_partner = ?";
        $res = $link->query($q, [$id]);

        if($res === false) throw new Exception("User does not exist", USER_NOT_FOUND);
        else if($res === MYSQL_EXCEPTION) throw new Exception("Error while trying to access database", MYSQL_EXCEPTION);
        else{
            $this->id = $res["id"];
            $this->email = $res["email"];
            $this->assignValues($res);
            $this->constructCompany();
        }
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
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $q = "SELECT name, inscription, revenue, website, id_sponsor FROM akm_partners WHERE id = ?";
        $res = $link->query($q, [$this->id_partner]);

        if($res === false) throw new Exception("Partner does not exist", COMPANY_NOT_FOUND);
        else if($res === MYSQL_EXCEPTION) throw new Exception("Error while trying to access database", MYSQL_EXCEPTION);
        else{
            $this->partnerName = $res["name"];
            $this->partnerInscription = $res["inscription"];
            $this->revenue = $res["revenue"];
            $this->website = $res["website"];
            $this->id_sponsor = $res["id_sponsor"];
        }
    }

    /**
     * Returns a table of all registered companies (id, name and website)
     * @return array table of all registered companies
     * @throws Exception MYSQL_EXCEPTION
     */
    public static function getAllPartnerId():array{
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $res = $link->queryAll("SELECT id, name, website FROM akm_partners", []);

        if ($res === MYSQL_EXCEPTION) throw new Exception("Database Error", MYSQL_EXCEPTION);
        return $res;
    }

    /**
     * Returns a table of n registered companies (id, name and website)
     * @param int $n number of ids to select
     * @param int $page page of ids to select
     * @return array table of all registered companies
     * @throws Exception MYSQL_EXCEPTION
     */
    public static function getPartnerId(int $n, int $page):array{
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $q = "SELECT id, name, website FROM akm_partners LIMIT ".(($page-1)*$n).",".$n;

        $res = $link->queryAll($q, []);

        if ($res === MYSQL_EXCEPTION) throw new Exception("Database Error", MYSQL_EXCEPTION);
        return $res;
    }

    /**
     * @param User $user : User object to extend
     * @param string $name
     * @param int $revenue
     * @param string $website
     * @param int|null $id_sponsor
     * @return void
     * @throws Exception :
     * - MYSQL_EXCEPTION : Database Fatal Error
     * - COMPANY_NOT_FOUND : unauthorized use of the function
     */
    public static function registerWithCode(User $user, string $name, int $revenue, string $website, int $id_sponsor){
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

            $status = $link->insert(
                'INSERT INTO akm_partners (name, inscription, revenue, website, id_user)
                    VALUES (:partnername, :inscription, :revenue, :website, :id_user)',
                [
                    'partnername' => $name,
                    'inscription' => getYearsAgo(0),
                    'revenue' => $revenue,
                    'website' => $website,
                    'id_user' => $user->getId(),
                ]);

        if ($status === false) throw new Exception("Database error", MYSQL_EXCEPTION);

        $user->updateIdPartner();
    }

    /**
     * @param User $user : User object to extend
     * @param string $name
     * @param int $revenue
     * @param string $website
     * @param int|null $id_sponsor
     * @return void
     * @throws Exception :
     * - MYSQL_EXCEPTION : Database Fatal Error
     * - COMPANY_NOT_FOUND : unauthorized use of the function
     */
    public static function registerWithoutCode(User $user, string $name, int $revenue, string $website){
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $status = $link->insert(
        'INSERT INTO akm_partners (name, inscription, revenue, website, id_user)
                    VALUES (:partnername, :inscription, :revenue, :website, :id_user)',
        [
            'partnername' => $name,
            'inscription' => getYearsAgo(0),
            'revenue' => $revenue,
            'website' => $website,
            'id_user' => $user->getId(),
        ]);

        if ($status === false) throw new Exception("Database error", MYSQL_EXCEPTION);

        $user->updateIdPartner();
    }


    /**
     * Generates, inserts into the database and returns sponsorship code
     * @param int $id id of the company that will be the generator of the code
     * @return string
     * @throws Exception
     */
    public static function generateSponsorCode(int $id):string{
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $code = generateRandomString(10);

        $status = $link->insert("INSERT INTO akm_sponsor_code (id_sponsor, used, code) VALUES (:id, false,:code)",
            ["id" => $id, "code" => $code]);

        if (!$status) throw new Exception("Error while trying to access database", MYSQL_EXCEPTION);

        return $code;
    }


    /**
     * Checks sponsor code validity and returns sponsor id and uses it
     * @param string $code code to be used
     * @return int id of the sponsor
     * @throws Exception
     * - INVALID_CODE in case of code unusability
     * - MYSQL_EXCEPTIOn in case of database failure
     */
    public static function useSponsorCode(string $code):int{
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $res = $link->query('SELECT id_sponsor FROM akm_sponsor_code WHERE code = :code AND used = false', ['code' => $code]);
        if($res === false)
            throw new Exception("Code is not valid", INVALID_CODE);

        if (!$link->insert('UPDATE akm_sponsor_code SET used = true, date_used = NOW() WHERE code = :code', ['code'=>$code]))
            throw new Exception("Error while trying to access database", MYSQL_EXCEPTION);

        return $res['id_sponsor'];
    }

    /**
     * Count amount of partners sponsored by the provided sponsor id in the current civil year
     * @param int $pId partner id
     * @return int mount sponsored
     * @throws Exception MYSQL_EXCEPTION
     */
    public static function countSponsored(int $pId):int{
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $civilYear = getCivilYear();

        $res = $link->query("SELECT COUNT(id) as sponsored FROM akm_sponsor_code 
                                    WHERE used = true AND id_sponsor = :pid AND date_used BETWEEN :date_min AND :date_max",
                                    ['pid' => $pId, "date_min" => $civilYear["date_min"], "date_max"=>$civilYear["date_max"]]);
        if($res === false) return 0;
        if($res === -1) throw new Exception("Database error", MYSQL_EXCEPTION);

        return $res['sponsored'];
    }

    /**
     * Checks validity of API token
     * @param string $token
     * @throws Exception
     * - MYSQL_EXCEPTION
     * - INVALID_PARTNER_TOKEN
     */
    public static function checkApiToken(string $token){
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $res = $link->query('SELECT id FROM akm_partners WHERE api_token = :token', ['token' => $token]);

        if($res === -1) throw new Exception("Database error", MYSQL_EXCEPTION);
        if($res === false) throw new Exception("Invalid token", INVALID_PARTNER_TOKEN);
    }

    /**
     *
     * Setter/Update functions
     *
     */

    /**
     * Updates company api token
     * @param int $id id of the company
     * @return string : -1 if user not set, new token if token refreshed successfully
     * @throws Exception - MYSQL_EXCEPTION if error while trying to access database
     */
    public static function updateApiToken(int $id):string{
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $new = generateRandomString(30);

        if ($id != -1){
            $status = $link->insert("UPDATE akm_partners SET api_token = :newtoken WHERE id = :pid", [
                'pid' => $id,
                'newtoken' => $new
            ]);

            if (!$status) throw new Exception("Error while trying to access database", MYSQL_EXCEPTION);

            return $new;
        }

        return -1;
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