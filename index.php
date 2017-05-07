<?php

/*
 * Setting all paths to be relative to SRC folder
 */
set_include_path("./src");

/* Classes needed: */
require_once("Router.php");
/* Class creating connection to the database - NOT the interface */
require_once("model/ClientStorageMySQL.php");
/* Data necessary to connect to the database */
require_once("model/mysql_config.php");


/* Creating database connection in $pdo object */
$pdo = new PDO(DB, USER, PASS);
/* Adding custon options to the connection */
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

/* DATABASE!!! Index.php is creating connection instance and sending it to the Model */
$clientdb = new ClientStorageMySQL($pdo);


/* 
* Index.php is the entrance point for the user. 
* All is needed here is to create an instance of Router and starting it's main() method.
*/
$scname=getenv("SCRIPT_NAME");
$router = new Router($scname,dirname($scname));
//print_r('<p>' . dirname($scname));
/* Sendng an instance of model to main() method of the Router, it will be redirected to the Controller which is being created there */
$router->main($clientdb);

?>