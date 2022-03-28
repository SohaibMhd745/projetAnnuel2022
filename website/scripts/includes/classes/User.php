<?php

class User{
    private $id;
	private $email;
	private $firstName;
	private $lastName;
	private $inscription;
	private $birth;
	private $phone;
	private $id_partner;

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

        $q = "SELECT id, firstname, lastname, inscription, birth, phone, id_partner FROM akm_users WHERE email = ? AND password = ?";
        $res = $link->query($q, [$email, hash('sha512', $password)]);

        if($res === false) throw new Exception("Invalid user email/password", INCORRECT_USER_CREDENTIALS);
        else if($res === MYSQL_EXCEPTION) throw new Exception("Error while trying to access database", MYSQL_EXCEPTION);
        else{
            $this->email = $email;
            $this->id = $res["id"];

            $this->firstName = $res["firstname"];
            $this->lastName = $res["lastname"];
            $this->inscription = $res["inscription"];
            $this->birth = $res["birth"];
            $this->phone = $res["phone"];
            $this->id_partner = $res["id_partner"];
        }
	}

    /**
     * Construct user class from user id
     * @param int $id : user id
     * @param DbLink $link : database link object
     * @throws Exception : INVALID_PARAMETER | INCORRECT_USER_CREDENTIALS | MYSQL_EXCEPTION
     */
    public function constructFromId(int $id, DbLink $link){
        $q = "SELECT email, firstname, lastname, inscription, birth, phone, id_partner FROM akm_users WHERE id = ?";
        $res = $link->query($q, [$id]);

        if($res === false) throw new Exception("User does not exist", USER_NOT_FOUND);
        else if($res === MYSQL_EXCEPTION) throw new Exception("Error while trying to access database", MYSQL_EXCEPTION);
        else{
            $this->id = $id;
            $this->email = $res["email"];

            $this->firstName = $res["firstname"];
            $this->lastName = $res["lastname"];
            $this->inscription = $res["inscription"];
            $this->birth = $res["birth"];
            $this->phone = $res["phone"];
            $this->id_partner = $res["id_partner"];
        }
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
    private function checkMailValidity(string $email) : bool{
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
    private function checkPassValidity(string $pass) : bool{
        if (empty($pass)) return false;
        if (strlen($pass) == 0) return false;
        if(strlen($pass)>30 || strlen($pass)<8) return false;

        return true;
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