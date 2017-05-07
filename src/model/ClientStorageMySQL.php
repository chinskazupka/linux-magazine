<?php

/* Classes needed : */
require_once('model/Client.php');
require_once('model/ClientStorage.php');

/**
* This class allows managing clients id the MySQL database  
**/

class ClientStorageMySQL implements ClientStorage
{
	/* Instance of PDO - the class connecting the application with the database */
	protected $pdo;

	/* Preparing statement - empty at the beginning */
	/* Statement reading the client's data */
	private $readStatement = null;
	/* Statement creating new client */
	private $createStatement = null;
	/* Statement deleting a client */
	private $deleteStatement = null;
	/* Statement editing a client */
	private $editStatement = null;


	/* Constructing an instance from an instance given by index.php */
	function __construct($pdo)
	{
		$this->pdo = $pdo;
	}


	/**
	 * Initializing an empty database */

	/* ATTENTION! charset 'utf8mb4' is in reality UTF-8 */
	public function init() {
		$query = '
				USE mvc;
                DROP TABLE IF EXISTS emp_clients;
				CREATE TABLE emp_clients (
				`id_client` int(11) NOT NULL AUTO_INCREMENT,
				`firstName` varchar(50) NOT NULL,
				`secondName` varchar(50) NOT NULL,
				`email` varchar(50) NOT NULL UNIQUE,
				`status` varchar(50) NOT NULL,
				`telephone` varchar(50),
				PRIMARY KEY (`id`))
				DEFAULT CHARSET=utf8mb4;
		';
		$this->pdo->exec($query);
	}



	/* IMPLEMENTING THE INTERFACE */


/* Method returning instance of Client class with data 
* corresponding to given $id, 
* or null if client was not found in the database */
	public function read($id){
		if ($this->readStatement === null) {
			/* Creating statement with "lacking data" : */
			$query = 'SELECT `firstName`, `secondName`, `email`, `status`, `telephone` FROM `emp_clients` WHERE `id_client` = :id;';
			/* Preparing the statement: */
			$this->readStatement = $this->pdo->prepare($query);
		}
		/* Executing the statement - filling in the voids */
		$this->readStatement->execute(array(':id' => $id));
		/* Retrieving the result */
		$arr = $this->readStatement->fetch();
		/* Checking the content - if the array is empty,
		* that means nothig was returned by query */
		if (!$arr) {
			return null;
		}
		/* If the array is not empty, a Client class object is created using the retrieved data */
		return new Client($arr['firstName'], $arr['secondName'], $arr['email'], $arr['status'], $arr['telephone']);
	}


/* Method returning an associative array of Client class objects, with their $id as keys */
	public function readAll(){
		$query = 'SELECT `id_client`, `firstName`, `secondName`, `email`, `status`, `telephone` FROM `emp_clients`;';
		$res = $this->pdo->query($query);
		/* Tworzę tablicę asocjacyjną */
		$clients = array();
		while ($arr = $res->fetch()) {
			$clients[$arr['id_client']] = new Client($arr['firstName'], $arr['secondName'], $arr['email'], $arr['status'], $arr['telephone']);
		}
		return $clients;
	}

/* Method adding a new client */
	public function create(Client $a){
		if($this->createStatement === null) {
			$query = 'INSERT INTO `emp_clients` (`firstName`, `secondName`, `email`, `status`, `telephone`) VALUES (:firstName, :secondName, :email, :status, :telephone);';
			$this->createStatement = $this->pdo->prepare($query);
		}
		$this->createStatement->execute(array(
			':firstName' => $a->getFirstName(),
			':secondName' => $a->getSecondName(),
			':email' => $a->getEmail(),
			':status' => $a->getStatus(),
			':telephone' => $a->getTelephone(),
		));
		return $this->pdo->lastInsertId();
	}

/* Method editing a client */
	public function edit($id, Client $a){
		if($this->editStatement === null) {
			$query = 'UPDATE `emp_clients` SET `firstName` = :firstName, `secondName` = :secondName, `email` = :email, `status` = :status, `telephone` = :telephone WHERE `id_client` = '.$id.'';
			$this->editStatement = $this->pdo->prepare($query);
		}
		$this->editStatement->execute(array(
			':firstName' => $a->getFirstName(),
			':secondName' => $a->getSecondName(),
			':email' => $a->getEmail(),
			':status' => $a->getStatus(),
			':telephone' => $a->getTelephone(),
		));
		//return $this->pdo->lastInsertId();
	}

/* Method deleting a client from the database. Returns TRUE if the deletion succeedes, FALSE otherwise (for example, when the client did not exist) */
  	public function delete($id){
		if($this->deleteStatement === null) {
			$query = 'DELETE FROM `emp_clients` WHERE `id_client` = :id';
			$this->deleteStatement = $this->pdo->prepare($query);
		}
		$this->deleteStatement->execute(array(':id' => $id));
		return $this->deleteStatement->rowCount() !== 0;
  	}

}// end of class ClientStorageMySQL
?>