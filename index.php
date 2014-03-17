<?php
	ob_start();			// Prevents any contents from being outputed before http headers
	
	// Include site information 
	include_once($_SERVER['DOCUMENT_ROOT'] . '/server/root.php');

	// Autoloads classes
	function __autoload($class_name) 
	{
	    include($_SERVER['DOCUMENT_ROOT'] . '/model/' . $class_name . '.php');
	}

	// Creates a new session
	$session = new Session();
	
	// Instantiate router object
	$router = new Router($_SERVER['REQUEST_URI']);
	
	// Check if user is trying to login	
	if (isset($_POST['loginUse']))
	{
		$status = $_POST['loginUse'] == "manager" ? $session->NewManagerSession($_POST['loginUsername'],$_POST['loginPassword']) : 
										  $session->NewUserSession($_POST['loginUsername'],$_POST['loginPassword']);
		  
		// Redirect to main page if manager login
		if ($status && $_SERVER['REQUEST_URI'] == strtolower("/managers"))
		{
			header('Location: ' . ROOT_URL);
			die();
		}
	}
		
	
	// Check if user is logged in
	if (isset($_SESSION['login'])) 
		$router->SetUse($_SESSION['login']);			// Set router to either manager or user mode
	else
		$router->SetUse(NULL);							// User is not logged in
	
	// Include templete page
	include_once($router->File());
	
	ob_end_flush();										// Output page
?>