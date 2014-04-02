<?php
	$router = Router::Load();
	$reservation = $router->params[1];
	$page = $router->params[0];
	
	$restaurant = Restaurant::GetRestaurant($reservation->restaurantID,$active = TRUE);
	$tableList = $reservation->GetTableList();
	
	$deleted = FALSE;
	$errors = Errors::Create("ResUserDelete");
	
	if (isset($_POST['cancel']))
	{
		$deleted = Reservations::DeleteReservationForUser($reservation->id,$_SESSION['id']);	
	}
	
	$title = "Upscale™ - Edit Reservation";
	
	include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainHeader.php");
?>
		<div id='mainContentsLogin'>
        	<div id='infoBox'>
            <?php if (!$deleted) { ?>
            	<div class='center'>
            		<h3>Reservation Details</h3>
                    <?php
                      if ($errors->GetState()) echo "<ul class='loginErrorListBlack'>";
                            $errors->GetError("general","\t<li>The reservation could not be cancelled, please try again</li>");
                      if ($errors->GetState()) echo "</ul>";
					 ?>
                </div>
                <table class='details'>
                    <tr>
                    	<td class='firstColDetails'>Restaurant: </td>
                        <td class='firstColDetails'><a href='<?php echo ROOT_URL."/restaurants/".$restaurant->id;?>' title='<?php echo $restaurant->name;?>' class='boldLink'><?php echo $restaurant->name;?></a></td>
                     <tr>
                        <td class='firstColDetails'>Address: </td><td><?php echo $restaurant->address;?></td>
                     </tr>
                     <tr>
                        <td class='firstColDetails'>Date: </td><td><?php echo date("l, F j, Y",strtotime($reservation->date));?></td>
                     </tr>
                     <tr>
                        <td class='firstColDetails'>Time: </td><td><?php echo date("g:i A",strtotime($reservation->startTime));?></td>
                     </tr>
                      <tr>
                        <td class='firstColDetails'>Number of People: </td><td><?php echo $reservation->numberOfPeople;?></td>
                     </tr>
                     <tr>
                        <td class='firstColDetails'>Table(s):</td>
                        <td>
						<?php
							if ($reservation->tableCount <= 0)
							{
								echo "No tables";	
							}
							elseif($reservation->tableCount == 1)
							{
								echo $tableList[0]->name;
							}
							else
							{
								echo $tableList[0]->name;
								$i = 0;
								foreach($tableList as $t)
								{
									if ($i++ == 0)
										continue;
									
									echo ", ".$t->name;											
								}
							}
						?>
                        </td>
                    </tr>                   
                </table>
                <div class='center'>
                	<form action='<?php echo THIS_PAGE;?>' method='post'>
                    	<input type='hidden' value='<?php echo $reservation->id;?>' name='cancel' />
                    	<input type='submit' value='Cancel this Reservation' title='cancel this reservation' class='mainButton' onclick="if(confirm('Are you sure you want to cancel this reservation? This cannot be undone.')) return true; else return false;" />
              		</form>
                </div>
            </div    
		><?php }else{ ?>  
        	<div class='center'>
          		<h3>Your Reservation has been Successfully Cancelled!</h3>
                <a href='<?php echo ROOT_URL;?>/reservations' class='mainButton' title='back' style='position:relative;top:20px'>make a new reservation</a>
            </div>
        <?php } ?>   
        </div>
     </div>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainFooter.php"); ?>