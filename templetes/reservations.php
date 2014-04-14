<?php
	$router = Router::Load();
	$info = $router->params[1];
	$page = $router->params[0];
	$form = new FormValues;
	
	$reservation = FALSE;
	
	if (!$info)
	{
		$reserve = FALSE;
		
		if(isset($_POST['timeAll']))
		{
			$reserve = Reservations::FindAllReservations($_SESSION['id'],$_POST['dateAll'],$_POST['timeAll'],$_POST['peopleAll']);
		}
		
		if(isset($_POST['restChoose']))
		{
			$info = Restaurant::GetRestaurant($_POST['restChoose'],$active = TRUE);
			
			$reservation = Reservations::MakeReservation($info,$_SESSION['id'],$_POST['dateB'],$_POST['timeB'],$_POST['peopleB']);
			
			if ($reservation)
				$tableList = $reservation->GetTableList();
			
		}
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
	
	if (!$info)
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
                        <td class='firstColDetails'><a href='<?php echo ROOT_URL."/restaurants?id=".$info->id;?>' title='<?php echo $info->name;?>' class='boldLink'><?php echo $info->name;?></a></td>
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
                       	<?php 
							$error = Errors::Create("resCreate");
							
							if ($error->GetState()) echo "<ul class='loginErrorListBlack'>";
							$error->GetError("generalError","\t<li>There was an error. Please try again.</li>\n");
							$error->GetError("boundError","\t<li>The reservation is not during the restaurants open hours.</li>\n");
							$error->GetError("noTables","\t<li>There are tables no available tables for that time.</li>\n");
							$error->GetError("resAlready","\t<li>You already have a reservation during that time.</li>\n");
							$error->GetError("dateSet","\t<li>Please enter a valid date.</li>\n");
							$error->GetError("timeSet","\t<li>Please enter a valid time.</li>\n");
							$error->GetError("peopleSet","\t<li>Please enter the number of people.</li>\n");
							$error->GetError("futureCheck","\t<li>Please enter a time and date that is in the future.</li>\n");
							if ($error->GetState()) echo "</ul>";
						?>
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
                 	<div class='rightMakeRes'>
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
		<?php }else { ?>
       		<div id='layerEdit'>
            	<div class='infoBox'>
                	<div class='<?php if ($reserve) echo "leftMakeRes"; else echo "centerMakeRes";?>' style='text-align:center'>
                        <h3>Search All Restaurants</h3>
                        <?php 
						
							$error = Errors::Create("resCreate");
							
							if ($error->GetState()) echo "<ul class='loginErrorListBlack'>";
							$error->GetError("generalError","\t<li>There was an error. Please try again.</li>\n");
							$error->GetError("nonefoundError","\t<li>There were no reservations for that time.</li>\n");
							$error->GetError("dateSet","\t<li>Please enter a valid date.</li>\n");
							$error->GetError("timeSet","\t<li>Please enter a valid time.</li>\n");
							$error->GetError("peopleSet","\t<li>Please enter the number of people.</li>\n");
							$error->GetError("futureCheck","\t<li>Please enter a time and date that is in the future.</li>\n");
							if ($error->GetState()) echo "</ul>";
						?>
                        <form action='<?php echo THIS_PAGE;?>' method='post'>
                            <input type='text' name='dateAll' id='date1' <?php echo $form->GetValue('dateAll');?> placeholder='date' class='inputField' />
                            <input type='text' name='peopleAll' <?php echo $form->GetValue('peopleAll');?> placeholder='number in party' class='inputField' />
                            <div class='spacing'><?php $form->TimeSelection("timeAll"); ?></div>
                            <input type='submit' value='Search' class='mainButton' />
                        </form>
                        <script>
                            $(function() {
                            $( "#date1" ).datepicker();
                            });
                        </script>
                	</div>
                      <?php if ($reserve) { ?>
                        <div class='rightMakeRes'>
                            <h3 class='res'>Found Reservations</h3>
							<h4 class='sub'><?php echo date("l, F j, Y - g:i A",strtotime($_POST['dateAll'] . " " . $_POST['timeAll']));?></h4>
                            <form action='<?php echo THIS_PAGE;?>' method='post'>
                            <table class='sub'>
                            <?php 
								$i = 0;
                                foreach($reserve as $r)
                                {
									if ($i++ == 0) 
										$p = "checked='checked' ";
									else
										$p = "";
										
                                    echo "<tr>
									        <td><input type='radio' name='restChoose' value='$r->id' $p/></td>
									        <td><a href='".ROOT_URL."/restaurants?id=$r->id' target='_blank' title='$r->name' class='boldLink'>$r->name</a><td></td>
										  </tr>";
                                }
                              ?>
                              <input type='hidden' name='dateB' <?php echo $form->GetValue('dateAll');?> />
                              <input type='hidden' name='timeB' <?php echo $form->GetValue('timeAll');?> />
                              <input type='hidden' name='peopleB' <?php echo $form->GetValue('peopleAll');?> />
                            </table>
                            <input type='submit' value='Make Reservation' class='mainButton' />
                            </form>
                        </div>
                        <?php } ?>
                    </div>
                  </div>
                 </div>
        <?php } ?>
        </div>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainFooter.php"); ?>