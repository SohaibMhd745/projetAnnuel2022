<?php

class User{
	private $email;
	private $name;
	private $surname;
	private $inscription;
	private $birth;
	private $phone;
	private $id_partner;

	function __construct(string $inputEmail){
		//TODO:

		//Check BDD

		//Si l'email n'existe pas:
		//Utiliser en try catch
		throw new NotFoundException();
	}


}

?>