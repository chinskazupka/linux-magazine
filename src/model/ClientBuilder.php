<?php
/* Classes needed : */
require_once('model/Client.php');

/**
* Reprezentuje instancję klasy Client podczas manipulacji 
* (tworzenia lub edycji).
* Waliduje również dane przesłane z formularza ClientForm.
*/

class ClientBuilder
{

	/* ENCAPSULATING FORM'S FIELD NAMES */

	/* Fieldnames are aviable only in this calss, so to change3 them in HTML
	* it is enought to change them here */

	/* Client's first name field name */
	const FIRSTNAME_REF='firstName';
	/* Client's second name field name */
	const SECONDNAME_REF='secondName';
	/* Client's email field name */
	const EMAIL_REF='email';
	/* Client's university status field name */
	const STATUS_REF='status';
	/* Client's telephone field name */
	const TELEPHONE_REF='telephone';



		/* ATTRIBUTS */

	/* Form data (array), needed to create a Client class instance */
	protected $data;
	/* Error table */
	private $error;
	
	/* Creates a new instance according to an array given */
	function __construct(array $data)
	{
		/* Weryfikacja istnienia pól */
		if (!key_exists(self::FIRSTNAME_REF,$data)) {
			$data[self::FIRSTNAME_REF] = '';
		}

		if (!key_exists(self::SECONDNAME_REF,$data)) {
			$data[self::SECONDNAME_REF] = '';
		}

		if (!key_exists(self::EMAIL_REF,$data)) {
			$data[self::EMAIL_REF] = '';
		}

		if (!key_exists(self::STATUS_REF,$data)) {
			$data[self::STATUS_REF] = '';
		}

		if (!key_exists(self::TELEPHONE_REF,$data)) {
			$data[self::TELEPHONE_REF] = '';
		}
		$this->data=$data;
		/* At the moment of creation of ClientBuilder instance, this string is null. */
		$this->error=null;
	}// end of __construct()



		/* GETTERS */ 

	/* Returna an array of $data */
	public function getData()
	{
		return $this->data;
	}


    public function fillWithInstance($client) {
        $this->data = array();
        $this->data[self::FIRSTNAME_REF] = $client->getFirstName();
        $this->data[self::SECONDNAME_REF] = $client->getSecondName();
        $this->data[self::EMAIL_REF] = $client->getEmail();
        $this->data[self::STATUS_REF] = $client->getStatus();
        $this->data[self::TELEPHONE_REF] = $client->getTelephone();
        return $this->data;
    }

	/* Returns errors string or null if the data was correct
	* or the data was not validated yet */
	public function getError()
	{
		return $this->error;
	}



		/* METHODS */

	/* Creating client - fills in the empty $data array with data from the sent client creation form */
	public function createClient(){
		return new Client($this->data[self::FIRSTNAME_REF], $this->data[self::SECONDNAME_REF], $this->data[self::EMAIL_REF], $this->data[self::STATUS_REF], $this->data[self::TELEPHONE_REF]);
	}

	/* Editing client - fills in the current $data array with data from the edit client creation form */
	public function editClient(){
		return new Client($this->data[self::FIRSTNAME_REF], $this->data[self::SECONDNAME_REF], $this->data[self::EMAIL_REF], $this->data[self::STATUS_REF], $this->data[self::TELEPHONE_REF]);
	}

	/* Validating data from the form */
	public function isValid()
	{
		//* Declaring an empty string */
		$this->error = '';

		/* Validating first name */
		if ($this->data[self::FIRSTNAME_REF] === '') {
			$this->error .= "Nom est requis !<br/>";
		} else {
    			$firstName = strip_tags(trim($this->data[self::FIRSTNAME_REF]));
     				 if (strlen($firstName) > 75 || strlen($firstName) < 2){
     				 	$this->error .= 'Longueur inacceptable. Le nom doit contenir entre 2 et 75 lettres !<br/>';
     				} else {
          					if (!preg_match("/^([a-zA-Z'àâéèêôùûçÀÂÉÈÔÙÛÇ[:blank:]-]{1,75})$/",$firstName)) {
           		 			$this->error .= "Seulement les lettres, espaces, apostrophes et tirets sont autorisés en nom !<br/>";
         		 			}
        			}
  		} // enf validation $firstName

  		/* Validating second name */
  		if ($this->data[self::SECONDNAME_REF] === '') {
			$this->error .= "Prénom est requis !<br/>";
		} else {
    			$secondName = strip_tags(trim($this->data[self::FIRSTNAME_REF]));
     				 if (strlen($secondName) > 75 || strlen($secondName) < 2){
     				 	$this->error .= 'Longueur inacceptable. Le prénom doit contenir entre 2 et 75 lettres !<br/>';
     				} else {
          					if (!preg_match("/^([a-zA-Z'àâéèêôùûçÀÂÉÈÔÙÛÇ[:blank:]-]{1,75})$/",$secondName)) {
           		 			$this->error .= "Seulement les lettres, espaces, apostrophes et tirets sont autorisés en prénom !<br/>";
         		 			}
        			}
  		} // end validation $secondName

  		/* Validating e-mail */
		if ($this->data[self::EMAIL_REF] === ''){
    	$this->error .= "Email est obligatoire !<br/>";
    	}
  		else{
   			 $email = strip_tags(trim($this->data[self::EMAIL_REF]));
   			 if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      		$this->error .= "Adresse e-mail incorrecte !<br/>";
    		}
  		} // end validation $email

  		/* Validating university status */
  		if ($this->data[self::STATUS_REF] === '') {
		$this->error .= "Status est requis !<br/>";
		} else {
    			$status = strip_tags(trim($this->data[self::STATUS_REF]));
     				 if (strlen($status) > 75 || strlen($status) < 2){
     				 	$this->error .= 'Longueur inacceptable. Le status doit contenir entre 2 et 75 lettres !<br/>';
     				} else {
          					if (!preg_match("/^([a-zA-Z'àâéèêôùûçÀÂÉÈÔÙÛÇ[:blank:]-]{1,75})$/",$status)){
           		 			$this->error .= 'Seulement les lettres, espaces, apostrophes et tirets sont autorisés en status universitaire !<br/>';
         		 			}
        			}
  		} // end validation $status

  		/* Validating telephone number */
		if ($this->data[self::TELEPHONE_REF] !== ''){
    		$telephone = strip_tags(trim($this->data[self::TELEPHONE_REF]));
        	if (!preg_match("/^[0-9\-\+() ]*$/",$telephone)) {
            	$this->error .= "Seulement les chiffres et -, + ou () sont autorisés en numéro de téléphone !<br/>";
          	}
  		}// end validation $telephone

		return $this->error === ''; // Returns boolean 

	}// end isValid()

}// end class ClientBuilder


?>