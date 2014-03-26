<?php
	$router = Router::Load();
	$info = $router->params[1];
	$page = $router->params[0];
	$form = new FormValues;
	
	$reservation = FALSE;
	
	if (!$info)
	{
		$r = Restaurant::GetAllRestaurants();	
	}
	else
	{
		if (isset($_POST['time']))
		{
			$reservation = Reservations::MakeReservation($info,$_SESSION['id'],$_POST['date'],$_POST['time'],$_POST['people']);
			
			if ($reservation)
				$tableList = $reservation->GetTableList();
		}
	}
	
	if ($info)
		$title = "Upscale™ - Search All Reservations";
	else
		$title = "Upscale™ - Reservations - $info->name";
	
	include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainHeader.php");
?>  
        <div id='mainContentsLogin'>
        <?php if ($reservation) { ?>
        	<div id='infoBox'>
            	<div class='center'>
            		<h3>Your Reservation has been saved!</h3>
                    <h4>Details</h4>
                </div>
                <table class='details'>
                    <tr>
                    	<td class='firstColDetails'>Restaurant: </td>
                        <td class='firstColDetails'><a href='<?php echo ROOT_URL."/restaurants/".$info->id;?>' title='<?php echo $info->name;?>' class='boldLink'><?php echo $info->name;?></a></td>
                     <tr>
                        <td class='firstColDetails'>Address: </td><td><?php echo $info->address;?></td>
                     </tr>
                     <tr>
                        <td class='firstColDetails'>Date: </td><td><?php echo date("l, F j, Y",strtotime($reservation->date));?></td>
                     </tr>
                     <tr>
                        <td class='firstColDetails'>Time: </td><td><?php echo date("g:i A",strtotime($reservation->startTime));?></td>
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
                	<h4>View your <a href='<?php echo ROOT_URL."/myreservations";?>' title='my reservations' class='boldLink'>reservations</a> to modify or delete this reservation</h4>
              	<a href='<?php echo THIS_PAGE;?>' class='mainButton' title='back'>< back</a>
                </div>
            
            </div>       
        <?php } elseif ($info) { ?>
        	<div id='layerEdit'>
            	<div class='infoBox'>
                	<div class='leftMakeRes' style='text-align:center'>
                        <h3>Make Reservation - <?php echo $info->name;?></h3>
                        <form action='<?php echo THIS_PAGE;?>' method='post'>
                            <input type='text' name='date' id='date' <?php echo $form->GetValue('date');?> placeholder='date' class='inputField' />
                            <input type='text' name='people' <?php echo $form->GetValue('people');?> placeholder='number in party' class='inputField' />
                            <div class='spacing'><?php $form->TimeSelection("time"); ?></div>
                            <input type='submit' value='Reserve' class='mainButton' />
                        </form>
                        <script>
                            $(function() {
                            $( "#date" ).datepicker();
                            });
                        </script>
                	</div>
                 	<div class='leftMakeRes'>
                    	<h3>Schedule</h3>
                        <table class='scheduleList'>
                        <?php
							$days = array("Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday");
						
							$schedule = $info->GetMainScheduleList();

							$i = 0;
							
							foreach($schedule as $s)
							{
								echo "<tr><td class='firstColDetails'>".$days[$i++]."</td><td>";
								
								if ($s->isClosed)
								{
									echo "Closed";	
								}
								elseif($s->open != NULL && $s->close != NULL)
								{
									echo date("g:i A",strtotime($s->open))." - ".date("g:i A",strtotime($s->close));
								}
								elseif($s->open == NULL && $s->close == NULL)
								{
									echo "24 hours";
								}
								elseif($s->open == NULL && $s->close != NULL)
								{
									echo "12:00 AM - ".date("g:i A",strtotime($s->close));
								}
								else
								{
									echo date("g:i A",strtotime($s->open))." - 12:01 AM";
								}
								
								echo "</td></tr>\n";
							}
						?>
                        </table>
                    </div>
            	</div>
            </div>
		<? } ?>
        </div>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainFooter.php"); ?>