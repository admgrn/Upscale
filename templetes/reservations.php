<?php
	$router = Router::Load();
	$page = $router->params;
	$title = "Upscale™ - Reservations";
	$form = new FormValues;
	
	//
		$r = Restaurant::GetAllRestaurants();
		
		if (isset($_POST['time']))
		{
			Reservations::MakeReservation($r[0],$_SESSION['id'],$_POST['date'],$_POST['time'],$_POST['people']);
		}
	
	//
	include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainHeader.php");
?>  
        <div id='mainContentsLogin'>
        
        <form action='<?php echo THIS_PAGE;?>' method='post'>
        	<input type='text' name='date' id='date' <?php echo $form->GetValue('date');?> />
        	<?php $form->TimeSelection("time"); ?>
            <input type='text' name='people' <?php echo $form->GetValue('people');?> />
            <input type='submit' />
        </form>
                    <script>
						$(function() {
						$( "#date" ).datepicker();
						});
					</script>

        </div>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainFooter.php"); ?>