<?php
	define('ROOT_URL','http://localhost');
	
	define('THIS_PAGE',ROOT_URL . $_SERVER['REQUEST_URI']);
	
	function openDB($db = "upscale")
	{
		$a = new mysqli("localhost","root","root");
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
