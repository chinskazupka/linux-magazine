<?php


/**
* Class client build an instance of a client
*/
class Client{

	/* Client first name (prenom) */
	protected $firstName;
	/* Client second name (nom) */
	protected $secondName;
	/* Client email */
	protected $email;
	/* Client status */
	protected $status;
	/* Client telephone */
	protected $telephone;


	public function __construct($firstName, $secondName, $email, $status, $telephone)
	{
		$this->firstName=$firstName;
		$this->secondName=$secondName;
		$this->email=$email;
		$this->status=$status;
		$this->telephone=$telephone;
	}


	/* GETTERS */
	public function getFirstName(){return $this->firstName;}
	public function getSecondName(){return $this->secondName;}
	public function getEmail(){return $this->email;}
	public function getStatus(){return $this->status;}
	public function getTelephone(){return $this->telephone;}

}

?>