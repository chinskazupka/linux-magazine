<?php

/**
* Classes needed :
*/
require_once('view/View.php');
require_once('control/ClientController.php');
/* Interface of the class creating a DB connection (since the Class was already instanciated in index.php) */
require_once('model/ClientStorage.php');


/* Only the Router has access to URLs 
* Other classes need to retrieve it from him using getXXXURL() type methods*/

class Router
{
	/** The prefix for URLs rooted by this instance. */
    protected $baseURL;

    /** The prefix for URLs of resources directly accessible via get requests. */
    protected $webBaseURL;

    /** The current view. */
    private $view;

    public function __construct ($baseURL, $webBaseURL) {
        $this->baseURL = $baseURL;
        $this->webBaseURL = $webBaseURL;
        //echo $baseURL;
        //echo "<br>";
        //echo $webBaseURL;  
    }
	
	function main(ClientStorage $clientdb){
		session_start();
		/* Taking the feedback from previous SESSION and 
		* if it is not empty, I put it in $feedback. 
		* If it is empty, I return a null */
		$feedback = isset($_SESSION['feedback']) ? $_SESSION['feedback'] : '';

		/* Deleting the session */ 
		$_SESSION['feedback'] = '';

		/* Creating a new View and tranfering into it (with $this) 
		* all the information from Router and the  $feedback (empty or not)*/
		$view = new View($this, $feedback);
		$ctl = new ClientController($view, $clientdb);


		/* Testing parameters from the URL : */

		/* Testing the contents of id attribute */
		$id = key_exists('id',$_GET) ? $_GET['id'] : null;

		/* Testing the contents of action attribute */
		$action = key_exists('action',$_GET) ? $_GET['action'] : null;


		$url = getenv('PATH_INFO');
		$urlParts = explode('/', $url);
		// First element is always empty, so I skip it
        array_shift($urlParts);

        // Retrieving first real element
        $page = array_shift($urlParts);


        // Tranferring control
        switch ($page) {
        	case '':
        		$view->makeHomePage();
        		break;
        	case 'list':
        	// Expecting URL of the form "list/x"
                if (!empty($urlParts[0])) {
                    $id = array_shift($urlParts);
                    $ctl->showInformation($id);
                    break;
                    } else {
           				$ctl->showList();                	
                    }
        		break;
        	case 'action':
        		if (! empty($urlParts[0])) {
        			$doAction = array_shift($urlParts);
        			switch ($doAction) {
						case 'add':
							$ctl-> newClient();
							break;
						case 'save':
							$ctl->saveNewClient($_POST);
							break;
						case 'edit':
							// Expecting URL of the form "edit/x"
                			if (!empty($urlParts[0])) {
                   			$id = array_shift($urlParts);
                   			$ctl->editClient($id); 
                    		break;
                    		} else {
           						$view->makeUnknownActionPage();         	
                    		}
                    	case 'saveedit':
							// Expecting URL of the form "saveedit/x"
                			if (!empty($urlParts[0])) {
                   			$id = array_shift($urlParts);
                   			$ctl->saveEditClient($id, $_POST); 
                    		break;
                    		} else {
           						$view->makeUnknownActionPage();         	
                    		}
						case 'delete' :
						    // Expecting URL of the form "delete/x"
                			if (!empty($urlParts[0])) {
                   			$id = array_shift($urlParts);
                    		$ctl->askClientDeletion($id);
                    		break;
                    		} else {
           						$view->makeUnknownActionPage();             	
                    		}
							break;
						case 'confirm':
							// Expecting URL of the form "confirm/x"
                			if (!empty($urlParts[0])) {
                   			$id = array_shift($urlParts);
                   			$ctl->deleteClient($id); 
                    		break;
                    		} else {
           						$view->makeUnknownActionPage();         	
                    		}
							break;	
						default:
							$view->makeUnknownActionPage();
							break;
					}// end switch $doAction
        		} // end if $doAction
        		break; // end case 'action'
        	default:
        		$view->makeUnknownActionPage();
        		break;
        }// end switch $page


	/* Showing the prepared View */
	$view->render();

	} // end method main()


	/*
	* REDIRECTING user to the URL given in argument (using redirect 303).
	* Method used after using POST on the URLs given by Router 
	* (so HTML escape is needed).
	*/
	public function POSTredirect($url)
	{
		header("Location:".htmlspecialchars_decode($url), true, 303);
		/* 
		* ATTENTION! The script has to be stopped, as the redirection is valid only once,
		* When client receives the HTTP message.
		*/
		die;
	}



	/* Those methods are used in links, when the page is changed.
	* Data retrieved from them is processed by Router */

	/* Returns homepage URL */
	public function getHomeURL()
	{
		return $this->baseURL;
	}

	/* Returns client list URL */
	public function getClientListURL()
	{
		return $this->baseURL."/list";
	}

	/* Returns URL with $id of a client */
	public function getClientURL($id) // retrieves $id of a clietn from URL
	{
			return $this->baseURL."/list/".$id;
	}

	/* Returns URL of the page creating a new client */
	public function getClientCreationURL()
	{
		return $this->baseURL . "/action/add";
	}

	/* Returns URL of the page editing a client */
	public function getClientEditURL($id)
	{
		return $this->baseURL . "/action/edit/" . $id;
	}

	/* Returns URL of the page saving an edited client */
	public function getClientSaveEditURL($id)
	{
		return $this->baseURL . "/action/saveedit/" . $id;
	}

	/* Returns URL of the page saving a new client */
	public function getClientSaveURL()
	{
		return $this->baseURL . "/action/save";
	}

	/* Returns URL of the page deleting a client */
	public function getClientDeletionURL($id)
	{
		return $this->baseURL . "/action/confirm/" . $id;
	}

	/* Returns URL of the page confirming a client deletion */
	public function getClientAskDeletionURL($id)
	{
		return $this->baseURL . "/action/delete/" . $id;
	}

    /* Method returning any path address */
    /* In this app it returns the CSS file address */
    public function getURL ($path) {
        return $this->webBaseURL ."/". $path;
    }

} // end class Router

?>