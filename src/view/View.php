<?php

/**
* Classes needed :
*/
require_once('Router.php');
require_once('model/Client.php');
require_once('model/ClientBuilder.php');

class View
{

			/*** ATTRIBUTS ***/

	/* Instance of Router, used by View to construct URLs */
	protected $router;
	/* Webpage title, filled with Views's methods like makeXXXPage() */
	protected $title;
	/* Webpage content, filled with Views's methods like makeXXXPage() */
	protected $content;
	/* Main menu, aviable on all the pages */
	protected $menu;
	/* Information goven to the user after completing an action */
	protected $feedback;
	/* Link to the CSS file */
	protected $styleSheetURL;



			/***  ANTI-INJECTION ATTACK METHOD ***/

			
	/**
	 * Une fonction pour échapper les caractères spéciaux de HTML.
	 * Encapsule celle de PHP, trop lourde à utiliser car
	 * nécessite trop d'options.
	 */

	public static function htmlesc($str) 
	{
		return htmlspecialchars($str,
			ENT_QUOTES  /* on échappe guillemets _et_ apostrophes */
			| ENT_SUBSTITUTE  /* les séquences UTF-8 invalides sont
			                   * remplacées par le caractère �
			                   * (au lieu de renvoyer la chaîne vide…) */
			| ENT_HTML5,  /* on utilise les entités HTML5 (en particulier &apos;) */
			'UTF-8'  /* encodage de la chaîne passée en entrée */
		);
	}
			/***  END ANTI-INJECTION ATTACK METHOD ***/



			/*** CONSTRUCTOR ***/


/* Here the Router given to View (during it's creation in index.php) is constructed */
	public function __construct(Router $router, $feedback)
	{
		/* Instantiating Router*/
		$this->router = $router;
		/* Constructing menu */
		$this->menu = array(
			$this->router->getHomeURL() => 'Accueil',
			$this->router->getClientListURL() => 'Liste de clients',
			$this->router->getClientCreationURL() =>'Ajout d\'un client',
			);
		/* Constructing feedback for the user */
		$this->feedback = $feedback;
		/* Constructing CSS file path */
		$this->styleSheetURL = $router->getURL("style.css");
	}//end __construct()



			/*** METHODS ***/

	/* Generating home page */
	public function makeHomePage()
	{
		$this->title = "Bienvenue à LM_Emprunts";
		$this->content = '<p> Système de réservation du Linux Magazine de l\'Université de Caen.</p>';
	}

	/* Generating client list page */
	public function makeListPage(array $clientsTab)
	{
		$print = "";
		$print .= "<p>Clients enregistrés dans la base de données : </p>";
		$print .= "<ul>";
		foreach ($clientsTab as $id => $a) {
			/* $clientsTab is an array of objects, it's impossible to use simply echo */
			$print .= '<li><a href="'.$this->router->getClientURL($id).'">'.self::htmlesc($a->getFirstName()).' '.self::htmlesc($a->getSecondName()).'</li>';
		}
		$print .= "</ul>";

		$this->title = "Liste de clients";
		$this->content = $print;
	}

	/* Generating single client page */
	public function makeClientPage($id, Client $client)
	{
		$this->title = "Données de ".self::htmlesc($client->getFirstName()).' '.self::htmlesc($client->getSecondName());
		$this->content = '<table>'.
						'<tr><th>Données client</th></tr>
						<tr><td>Nom : </td><td>'.self::htmlesc($client->getFirstName()).'</td></tr>
						<tr><td>Prénom : </td><td>'.self::htmlesc($client->getSecondName()).'</td></tr>
						<tr><td>Prénom : </td><td>'.self::htmlesc($client->getEmail()).'</td></tr>
						<tr><td>Status universitaire : </td><td>'.self::htmlesc($client->getStatus()).'</td></tr>
						<tr><td>Téléphone : </td><td>'.self::htmlesc($client->getTelephone()).'</td></tr>
						</table>
						<a href="'.$this->router->getClientAskDeletionURL($id).'">Supprimer ce client</a>
						<a href="'.$this->router->getClientEditURL($id).'">Éditer ce client</a>';
	}


	
		/* CLIENT CREATION */


	/* Generating page with a webform for creating a new client */ 
	/* Data given to this method at the beggining is an instance of ClientBuilder with empty attributs. */

	/* If user returns here because previous data was invalid, changed data is imported from Controller's saveNewClient($_POST) method;*/
	public function makeClientCreationPage(ClientBuilder $builder)
	{
		/* Preparing webform content*/
		$data = $builder->getData();
		/* Reference to encapsulated field names, aviable only in ClientBuilder model class */
		$nameRef = $builder::FIRSTNAME_REF;
		$surnameRef = $builder::SECONDNAME_REF;
		$emailRef = $builder::EMAIL_REF;
		$statusRef = $builder::STATUS_REF;
		$telephoneRef = $builder::TELEPHONE_REF;
		/* HTML form */
		$s = '';
		$s .= '<form action ="'.$this->router->getClientSaveURL().'" method="POST">';
		$s .= '<p><label for="nameInput">Prénom : </label><input type="text" name="'.$nameRef.'" id="nameInput" value="'.self::htmlesc($data[$nameRef]).'" /></p>';
		$s .= '<p><label for="surnameInput">Nom : </label><input type="text" name="'.$surnameRef.'" id="surnameInput" value="'.self::htmlesc($data[$surnameRef]).'" /></p>';
		$s .= '<p><label for="emailInput">E-mail : </label><input type="text" name="'.$emailRef.'" id="emailInput" value="'.self::htmlesc($data[$emailRef]).'" /></p>';
		$s .= '<p><label for="statusInput">Status universitaire : </label><input type="text" name="'.$statusRef.'" id="statusInput" value="'.self::htmlesc($data[$statusRef]).'" /></p>';
		$s .= '<p><label for="telephoneInput">Téléphone : </label><input type="text" name="'.$telephoneRef.'" id="telephoneInput" value="'.self::htmlesc($data[$telephoneRef]).'" /></p>';
		$s .= '<p><button type="submit">Ajouter</button></p>';
		$s .= '</form>';
		/* Error table */
			$error = $builder->getError();
			if ($error !== '' && $error !== null) {
				$s .= '<div class="error">'.$error.'</div>';
			}
		$this->title = "Ajouter un client";
		$this->content = $s;
	}

	/* REDIRECTION after successful client creation */
	public function displayClientCreationSuccess($id)
	{
		$_SESSION['feedback'] = "Dodawanie do bazy zakończone sukcesem!";
		$this->router->POSTredirect($this->router->getClientURL($id));
	}

	/* REDIRECTION after failed client creation */
	public function displayClientCreationFailure()
	{
		$_SESSION['feedback'] = 'Formulaire invalide';
		$this->router->POSTredirect($this->router->getClientCreationURL());
	}



		/* CLIENT EDIT */


	public function makeClientEditPage($id, $client, ClientBuilder $builder)
	{	
		/* Generating page with a webform for editing a client */ 
		/* Data given to this method at the beggining comes from the given instance of a Client corresponding to the given $id. */

		/* If user returns here because previous data was invalid, changed data is imported from SESSION */

		/* Preparing webform content */
		if (key_exists('editedClient', $_SESSION)) {
			$data = $builder->getData();
		} else {
			$data = $builder->fillWithInstance($client);
		}
		/* Reference to encapsulated field names, aviable only in ClientBuilder model class */
		$nameRef = $builder::FIRSTNAME_REF;
		$surnameRef = $builder::SECONDNAME_REF;
		$emailRef = $builder::EMAIL_REF;
		$statusRef = $builder::STATUS_REF;
		$telephoneRef = $builder::TELEPHONE_REF;
		/* HTML form */
		$s = '';
		$s .= '<form action ="'.$this->router->getClientSaveEditURL($id).'" method="POST">';
		$s .= '<p><type="hidden" name="id" id="id" value="'.$id.'" /></p>';
		$s .= '<p><label for="nameInput">Prénom : </label><input type="text" name="'.$nameRef.'" id="nameInput" value="'.self::htmlesc($data[$nameRef]).'" /></p>';
		$s .= '<p><label for="surnameInput">Nom : </label><input type="text" name="'.$surnameRef.'" id="surnameInput" value="'.self::htmlesc($data[$surnameRef]).'" /></p>';
		$s .= '<p><label for="emailInput">E-mail : </label><input type="text" name="'.$emailRef.'" id="emailInput" value="'.self::htmlesc($data[$emailRef]).'" /></p>';
		$s .= '<p><label for="statusInput">Status universitaire : </label><input type="text" name="'.$statusRef.'" id="statusInput" value="'.self::htmlesc($data[$statusRef]).'" /></p>';
		$s .= '<p><label for="telephoneInput">Téléphone : </label><input type="text" name="'.$telephoneRef.'" id="telephoneInput" value="'.self::htmlesc($data[$telephoneRef]).'" /></p>';
		$s .= '<p><button type="submit">Modifier</button></p>';
		$s .= '</form>';
		$s .= '<a href="'.$this->router->getClientURL($id).'">Annuler et retourner</a>';
		/* Errors table */
			$error = $builder->getError();
			if ($error !== '' && $error !== null) {
				$s .= '<div class="error">'.$error.'</div>';
			}
		$this->title = "Modifier un client";
		$this->content = $s;
	}// end makeClientEditPage()

	/* REDIRECTION after successful edit */
	public function displayClientEditSuccess($id)
	{
		$_SESSION['feedback'] = "L'édition a réussi !";
		$this->router->POSTredirect($this->router->getClientURL($id));
	}

	/* REDIRECTION after failed edit */
	public function displayClientEditFailure($id)
	{
		$_SESSION['feedback'] = 'Formulaire en edition est invalide !';
		$this->router->POSTredirect($this->router->getClientEditURL($id));
	}



		/* CLIENT DELETION */


	/* Generating deletion confirmation page */
	public function makeClientDeletionPage($id, $client)
	{
		$s = '';
		$s .= '<form action="'.$this->router->getClientDeletionURL($id).'" method="POST">'."\n";
		$s .= '<p>Voulez-vous vraiment supprimer le client « '
			.self::htmlesc($client->getFirstName()).' '.self::htmlesc($client->getSecondName()).' » ?</p>'."\n";
		$s .= '<button type="submit">Confirmer la suppression</button>'."\n";
		$s .= '<a href="'.$this->router->getClientURL($id).'">Annuler et retourner</a>';
		$s .= '</form>'."\n";
		$this->title = 'Suppression d\'un client :';
		$this->content = $s;
	}// end makeClientDeletionPage()

	/* Filling in the View with info about successful deletion */
	public function makeClientDeletedPage()
	{
		$this->title = 'Suppression effectuée!';
		$this->content = "Le client a été supprimé.";
	}

	/* REDIRECTION after successful deleteion */
	public function displayClientDeletionSuccess($id)
	{
		$_SESSION['feedback'] = "Le client a été supprimé.";
		$this->router->POSTredirect($this->router->getClientListURL());
	}

	/* REDIRECTION after failed deleteion */
	public function displayClientDeletionFailure()
	{
		$_SESSION['feedback'] = 'Impossible de supprimer le client!';
		$this->router->POSTredirect($this->router->getClientURL($id));
	}



		/* ERRORS */


	/* Generating unknow client error page */
	public function makeUnknownClientPage()
	{
		$this->title = "Client inconnu !";
		$this->content = '<p> Ce client n\'est pas enregistré dans notre base de données ! </p>';
	}

	/* Generating unknow action error page */
	public function makeUnknownActionPage()
	{
		$this->title = "Erreur ! ";
		$this->content = '<p> Cette action ne peut pas être réalisée ! </p>';
	}



		/* DEBUG */


	/* Filling in the View with chosen varaible to debug */
	public function makeDebugPage($variable)
	{
		$this->title='Debug';
		$this->content = '<pre>'.var_export($variable,true).'<pre>';
	}



		/* HTML template */


	/* Generating HTML with generated earlier in PHP content */
	public function render(){
		?>
		<!DOCTYPE html>
		<html>
			<head>
				<title><?php echo $this->title; ?></title>
				<link rel="stylesheet" type="text/css" href="<?php echo $this->styleSheetURL; ?>">
			</head>
			<body>
			<nav class="menu">
				<ul>
					<?php foreach ($this->menu as $url => $text) { ?>
						 	<li><a href="<?php echo $url; ?>"><?php echo $text; ?></a></li>
					<?php } ?>
				</ul>
			</nav>
			<h1><?php echo $this->title; ?></h1>
			<?php
			/* Showing feedback only, if it exists */
			if($this->feedback !==''){
					echo '<div class="feedback">'.$this->feedback.'</div>';
				}
			echo $this->content; 
			?>
			</body>
		</html>
		<?php
	} //end render()

} //end class View

?>