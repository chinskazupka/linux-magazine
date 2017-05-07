<?php

/* Classes needed: */	
require_once("model/Client.php");

/*
* Inferfaces created to isolate requests concerning clients (Interfejs służy do izolacji od reszty aplikacji zapytań dotyczących klientów)
*/

interface ClientStorage {

/* Method returning instance of Client class with data corresponding to given $id, or null if client was not found in the database */
	public function read($id);
/* Method returning an associative array of Client class objects, with their $id as keys */
	public function readAll();
/* Method adding a new client */
	public function create(Client $a);
/* Method deleting a client from the database. Returns TRUE if the deletion succeedes, FALSE otherwise (for example, when the client did not exist) */
  	public function delete($id);

}

?>