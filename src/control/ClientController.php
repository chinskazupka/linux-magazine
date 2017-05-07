<?php

/* Classes needed: */
require_once('view/View.php');
require_once('model/Client.php');
/* Calling directly to interface */
require_once('model/ClientStorage.php');
require_once('model/ClientBuilder.php');


/*
* Controller handles the requests and fills in the View
*/
class ClientController{

	/* The View instance used to show HTML */
	protected $view;
	/* Database, storing client's info */
	protected $clientdb;



	/* CONSTRUCTOR */
	
	public function __construct(View $view, ClientStorage $clientdb){
		$this->view=$view;
		/* Creating an instance of ClientStorage - model's interface */
		$this->clientdb=$clientdb;
	}



	/* CLIENT VIEWING */

	/* Asks View to create single client page */
	/* $id id transmitted by URL */
	public function showInformation($id){
		$client = $this->clientdb->read($id);
		if ($client!== null) {
			$this->view->makeClientPage($id, $client);
		}else{
			$this->view->makeUnknownClientPage();
		}
	}

	/* Asks View to create a client list */
	public function showList(){
		$this->view->makeListPage($this->clientdb->readAll());
	}



	/* CLIENT CREATION */


	/* Asking View to prepare a client creation page */
	/* Using the data from SESSION, if they exist */
	public function newClient()
	{
		if (isset($_SESSION['currentNewClient'])) {
			$builder = $_SESSION['currentNewClient'];
		} else {
			$builder = new ClientBuilder(array());
		}
		$this->view->makeClientCreationPage($builder);
	}

	/* Saving a new client */
	public function saveNewClient(array $data){
			/* Creating an instance of ClientBuilder() model
			* with data given by Router */
			$builder = new ClientBuilder($data);
			/* VALIDATION - Creates a client is filled properly, 
			resends the form with filled data otherwise */
			if ($builder->isValid()) {
			/* Creating an instance if valid */
			$client = $builder->createClient();
			/* Saving in database: */
			$id = $this->clientdb->create($client);
			/* Deleting the session with form data */
			unset($_SESSION['currentNewClient']);
			/* Creating the newly created client page */
			// $this->view->makeClientPage($id, $client);
			/* REDIRECTING to newly created client's page*/
			$this->view->displayClientCreationSuccess($id);
			} else {
				/* Saving firm data in session and redirecting to the form */
				$_SESSION['currentNewClient'] = $builder;
				$this->view->displayClientCreationFailure();
			}
	}// end of saveNewClient()



	/* CLIENT EDITION */


	public function editClient($id)
	{
		/* Chceking, if client exists */
		$client=$this->clientdb->read($id);

		if ($client === null) {
			$this->view->makeUnknownClientPage();
		} else { 
				if (isset($_SESSION['editedClient'])) {
				$builder = $_SESSION['editedClient'];
				} else {
						$builder = new ClientBuilder(array());
				}
			$this->view->makeClientEditPage($id, $client, $builder);
		}
	}

	/* Editing a client */
	public function saveEditClient($id, array $data){
			/* Creating an instance of ClientBuilder() model
			* with data given by Router */
			$builder = new ClientBuilder($data);
			/* VALIDATION - Creates a client is filled properly, 
			resends the form with filled data otherwise */
			if ($builder->isValid()) {
			/* Creating an instance if valid */
			$client = $builder->editClient();
			/* Saving in database: */
			$edit = $this->clientdb->edit($id, $client);
			/* Deleting the session with form data */
			unset($_SESSION['editedClient']);
			/* REDIRECTING to edited client's page*/
			$this->view->displayClientEditSuccess($id);
			} else {
				/* Saving firm data in session and redirecting to the form */
				$_SESSION['editedClient'] = $builder;
				$this->view->displayClientEditFailure($id);
			}
	}// end of saveNewClient()



	/* CLIENT DELETION */

	/* Preparing the View to make a page asking for deletion confirmation */
	public function askClientDeletion($id)
	{
		/* Chceking if client exists in the first place */
		$client=$this->clientdb->read($id);
		if ($client === null) {
			$this->view->makeUnknownClientPage();
		} else {
			$this->view->makeClientDeletionPage($id, $client);
		}
	}

	/* Deleting the client */
	public function deleteClient($id)
	{
		$ok = $this->clientdb->delete($id);
		if($ok){
			$this->view->displayClientDeletionSuccess();
		} else {
			/* If the client does not exist: */
			$this->view->displayClientDeletionFailure($id);
		}
	}

}//end class Controller

?>