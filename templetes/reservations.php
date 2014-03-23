<?php
	$router = Router::Load();
	$page = $router->params;
	$title = "Upscale™ - Reservations";
	$form = new FormValues;
	
	//
		$r = Restaurant::GetAllRestaurants();
		echo $r[0]->name;
		if (isset($_POST['time']))
		{
			Reservations::SearchReservation($r[0],$_POST['date'],$_POST['time'],4);
		}
	
	//
	include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainHeader.php");
?>  
        <div id='mainContentsLogin'>
        
        <form action='<?php echo THIS_PAGE;?>' method='post'>
        	<input type='date' name='date' />
        	<?php $form->TimeSelection("time"); ?>
            <input type='submit' />
        </form>

        </div>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainFooter.php"); ?>