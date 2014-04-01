<?php	
	define('ROOT_URL','http://localhost');
	
	define('THIS_PAGE',ROOT_URL . $_SERVER['REQUEST_URI']);
	
	define('TALLY_COORD',"30.4328071,-84.2879266");
	
	function openDB($db = "upscale")
	{
		if(!@include_once($_SERVER['DOCUMENT_ROOT'] . "/../info/login.php"))
		{		
			$host = "localhost"; 
			$username = "root"; 
			$password = "root";
		}
		
		$a = new mysqli($host,$username,$password);
		if (!$a->select_db($db))
		{
			include_once($_SERVER['DOCUMENT_ROOT'] . "/database/create.php");
			
			$a->multi_query($query);
			echo $a->error;
			$a->select_db($db);
			
		}
		
		return $a;
	}
?>
