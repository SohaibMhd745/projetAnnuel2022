<?php

class User{
    protected $id;
    protected $email;
    protected $firstName;
    protected $lastName;
    protected $inscription;
    protected $birth;
    protected $phone;
    protected $id_partner;

    public function __construct()
    {
    }

    /**
     * Construct user class from email and password
     * @param string $email : input email to attempt connection
     * @param string $password : input password to attempt connection
     * @param DbLink $link : database link object
     * @throws Exception : INVALID_PARAMETER | INCORRECT_USER_CREDENTIALS | MYSQL_EXCEPTION
     */
    public function constructFromEmailAndPassword(string $email, string $password, DbLink $link){
        if (!$this->checkMailValidity($email)) throw new Exception("Invalid Email Provided",INVALID_PARAMETER);
        if (!$this->checkPassValidity($password)) throw new Exception("Invalid Password Provided",INVALID_PARAMETER);

        $q = "SELECT id, firstname, lastname, inscription, birthdate, phone, id_partner FROM akm_users WHERE email = ? AND password = ?";
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
     * @param DbLink $link : database link object
     * @throws Exception : INVALID_PARAMETER | INCORRECT_USER_CREDENTIALS | MYSQL_EXCEPTION
     */
    public function constructFromId(int $id, DbLink $link){
        $q = "SELECT email, firstname, lastname, inscription, birthdate, phone, id_partner FROM akm_users WHERE id = ?";
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
     * Simplifies both construct from
     * @param $res : result from SQL query
     * @return void
     */
    protected function assignValues($res){
        $this->firstName = $res["firstname"];
        $this->lastName = $res["lastname"];
        $this->inscription = $res["inscription"];
        $this->birth = $res["birthdate"];
        $this->phone = $res["phone"];

        ///Causes crashes if not set to -1 and we check later on, we have to check now or it won't work later
        if ($res["id_partner"] === null) $this->id_partner = -1;
        else $this->id_partner = $res["id_partner"];
    }

    /**
     *
     * Utility functions
     *
     */

    /**
     * Checks validity of email string
     * @param string $email : email string input
     * @return bool : validity
     */
    protected function checkMailValidity(string $email) : bool{
        if (empty($email)) return false;
        if (strlen($email) == 0) return false;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
        if(strlen($email)>50 || strlen($email)<5) return false;

        return true;
    }

    /**
     * Checks validity of password string
     * @param string $pass : password string input
     * @return bool : validity
     */
    protected function checkPassValidity(string $pass) : bool{
        if (empty($pass)) return false;
        if (strlen($pass) == 0) return false;

        return true;
    }
    /**
     *
     * Setter Functions
     *
     */

    /**
     * Updates ID partner
     * @param DbLink $dbLink : database link object
     * @return int :
     * - NO_EXCEPTION if update happens, COMPANY_NOT_FOUND if the company does not exist (wrong use of the function),
     * - MYSQL_EXCEPTION if fatal sql error
     */
    public function updateIdPartner(DbLink $dbLink) : int{
        $q = "UPDATE akm_users SET id_partner = (SELECT id FROM akm_partners WHERE id_user = ?) WHERE id = ?";
        $success = $dbLink->insert($q, [$this->id, $this->id]);
        if (!$success){
            if ($success === false) return COMPANY_NOT_FOUND;
            if($success === MYSQL_EXCEPTION) return MYSQL_EXCEPTION;
        }
        return NO_EXCEPTION;
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
     * @return string : datatime of isncription
     */
    public function getInscription() : string
    {
        return $this->inscription;
    }

    /**
     * @return mixed
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