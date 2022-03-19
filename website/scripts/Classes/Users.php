<?php

class User{
    private $id;
	private $email;
	private $name;
	private $surname;
	private $inscription;
	private $birth;
	private $phone;
	private $id_partner;

    function __construct(string $email, string $password, DbLink $link){
        if (!$this->checkMailValidity($email)) throw new Exception("Invalid Email Provided");
        if (!$this->checkPassValidity($password)) throw new Exception("Invalid Password Provided");

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
        if(strlen($email)>250) return false;

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
        if(strlen($pass)>250) return false;

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
     * @return string : name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string : phone number (french format)
     */
    public function getPhone() : string
    {
        return $this->phone;
    }

    /**
     * @return string : surname
     */
    public function getSurname() : string
    {
        return $this->surname;
    }

}
