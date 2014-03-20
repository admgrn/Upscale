<?php
	$router = Router::Load();
	$page = $router->params;
	$session = new Session;
	$restaurants = Restaurant::GetRestaurantList($_SESSION['id']);

	$title = "Upscale™ - Manage Restaurants";
	
	include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainHeader.php");
	
?>  
        <div id='mainContentsLogin'>
     
        </div>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainFooter.php"); ?>