<?php

class User{
    protected int $id = -1;
    protected string $email;
    protected string $firstName;
    protected string $lastName;
    protected string $inscription;
    protected string $birth;
    protected string $phone;
    protected int $id_partner;
    protected int $points;

    /**
     * Construct user class from email and password
     * @param string $email : input email to attempt connection
     * @param string $password : input password to attempt connection
     * @throws Exception : INVALID_PARAMETER | INCORRECT_USER_CREDENTIALS | MYSQL_EXCEPTION
     */
    public function constructFromEmailAndPassword(string $email, string $password){
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $q = "SELECT id, firstname, lastname, inscription, birthdate, phone, id_partner, points FROM akm_users WHERE email = ? AND password = ?";
        $res = $link->query($q, [$email,  preparePassword($password)]);

        if($res === false) throw new Exception("Invalid user email/password", INCORRECT_USER_CREDENTIALS);
        else if($res === MYSQL_EXCEPTION) throw new Exception("Error while trying to access database", MYSQL_EXCEPTION);
        else{
            $this->email = $email;
            $this->id = $res["id"];
            $this->assignValues($res);
        }
	}

    /**
     * Construct user class from user id
     * @param int $id : user id
     * @throws Exception : INVALID_PARAMETER | INCORRECT_USER_CREDENTIALS | MYSQL_EXCEPTION
     */
    public function constructFromId(int $id){
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $q = "SELECT email, firstname, lastname, inscription, birthdate, phone, id_partner, points FROM akm_users WHERE id = ?";
        $res = $link->query($q, [$id]);

        if($res === false) throw new Exception("User does not exist", USER_NOT_FOUND);
        else if($res === MYSQL_EXCEPTION) throw new Exception("Error while trying to access database", MYSQL_EXCEPTION);
        else{
            $this->id = $id;
            $this->email = $res["email"];
            $this->assignValues($res);
        }
    }

    /**
     * Construct user class from user id
     * @param string $token : user connection token
     * @throws Exception : INVALID_AUTH_TOKEN | MYSQL_EXCEPTION
     */
    public function constructFromToken(string $token){
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $token = sanitizeStringQuotes($token);

        $q = "SELECT id, email, firstname, lastname, inscription, birthdate, phone, id_partner, token, points FROM akm_users WHERE token = :token AND NOW() < token_end";
        $res = $link->query($q, ["token" => $token]);

        if($res === false) throw new Exception("Auth token invalid", INVALID_AUTH_TOKEN);
        else if($res === MYSQL_EXCEPTION) throw new Exception("Error while trying to access database", MYSQL_EXCEPTION);
        else{
            $this->id = $res["id"];
            $this->email = $res["email"];
            $this->assignValues($res);
        }
    }

    /**
     * Simplifies both construct from
     * @param $res : result from SQL query
     * @return void
     */
    protected function assignValues(array $res){
        $this->firstName = $res["firstname"];
        $this->lastName = $res["lastname"];
        $this->inscription = $res["inscription"];
        $this->birth = $res["birthdate"];
        $this->phone = $res["phone"];
        $this->points = $res["points"];

        ///Causes crashes if not set to -1 and we check later on, we have to check now or it won't work later
        if ($res["id_partner"] === null) $this->id_partner = -1;
        else $this->id_partner = $res["id_partner"];
    }

    /**
     * @param string $lastname
     * @param string $firstname
     * @param string $birthdate
     * @param string $phone
     * @param string $email
     * @param string $password
     * @throws Exception :
     * - MYSQL_EXCEPTION (database failure)
     * - EMAIL_USED (Email already in use)
     */
    public static function create(string $lastname, string $firstname, string $birthdate, string $phone, string $email, string $password){
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        if($link->query('SELECT id FROM akm_users WHERE email = :email', ['email' => $email]) !== false)
            throw new Exception("User already exists", EMAIL_USED);

        try {
            $status = $link->insert(
                'INSERT INTO akm_users (lastname, firstname, birthdate, phone, email, password, inscription, points)
                   VALUES (:lastname, :firstname, :birthdate, :phone, :email, :password, :inscription, 0)',
                [
                    'lastname' => $lastname,
                    'firstname' => $firstname,
                    'birthdate' => $birthdate,
                    'phone' => $phone,
                    'email' => $email,
                    'password' => preparePassword($password),
                    'inscription' => getYearsAgo(0)
                ]);
        } catch (Exception $e){
            throw new Exception("Critical Database Failure", MYSQL_EXCEPTION);
        }
        if($status !== true)
            throw new Exception("Critical Database Failure", MYSQL_EXCEPTION);
    }

    /**
     *
     * Update (setter) Functions
     *
     */

    /**
     * Updates user token and token_end
     * @return string : -1 if user not set, new token if token refreshed successfully
     * @throws Exception - MYSQL_EXCEPTION if error while trying to access database
     */
    public function updateToken():string{
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $new = generateRandomString(16);

        $end = date('Y-m-d H:i:s', strtotime(TOKEN_VALIDITY));

        if ($this->id != -1){
            $status = $link->insert("UPDATE akm_users SET token = :newtoken, token_end = :newend WHERE id = :uid", [
                'uid' => $this->id,
                'newtoken' => $new,
                "newend" => $end
            ]);

            if (!$status) throw new Exception("Error while trying to access database", MYSQL_EXCEPTION);

            return $new;
        }

        return -1;
    }

    /**
     * Updates ID partner
     * @throws :
     * - COMPANY_NOT_FOUND if the company does not exist (wrong use of the function),
     * - MYSQL_EXCEPTION if fatal sql error
     */
    public function updateIdPartner(){
        $link = new DbLink(HOST, CHARSET, DB, USER, PASS);

        $res = $link->query("SELECT id as pid FROM akm_partners WHERE id_user = ?", [$this->id]);

        if ($res === false) throw new Exception("Company does not exist", COMPANY_NOT_FOUND);
        if ($res === MYSQL_EXCEPTION) throw new Exception("Error while trying to access database", MYSQL_EXCEPTION);

        $q = "UPDATE akm_users SET id_partner = :pid WHERE id = :uid";
        $success = $link->insert($q, ["pid"=>$res["pid"], "uid"=>$this->id]);

        if ($success === false) throw new Exception("Company does not exist", COMPANY_NOT_FOUND);
        if ($success === MYSQL_EXCEPTION) throw new Exception("Error while trying to access database", MYSQL_EXCEPTION);

    }

    /**
     *
     * Getter functions
     *
     */

    /**
     * @return string : email adress
     */
    public function getEmail() : string
    {
        return $this->email;
    }

    /**
     * @return string : datetime of birth
     */
    public function getBirth() : string
    {
        return $this->birth;
    }

    /**
     * @return int : id of parent company (if exists)
     */
    public function getIdPartner() : int
    {
        return $this->id_partner;
    }

    /**
     * @return int
     */
    public function getPoints(): int
    {
        return $this->points;
    }

    /**
     * @return string : datatime of isncription
     */
    public function getInscription() : string
    {
        return $this->inscription;
    }

    /**
     * @return string : first name
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string : phone number (french format)
     */
    public function getPhone() : string
    {
        return $this->phone;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

}