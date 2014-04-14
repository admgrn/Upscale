<?php
	$router = Router::Load();
	$page = $router->params[0];
	
	if (isset($_POST['delete']))
	{
		Reservations::DeleteIDWithManID($_POST['delete'],$_SESSION['id']);	
	}
	
	if ($router->params[1] == "future")
	{
		$title = "Upscale™ - Reservations - ".$page->name;
		$reservations = $page->GetReservations($_SESSION['id']);
	}
	else
	{
		$title = "Upscale™ - Past Reservations - ".$page->name;
		$reservations = $page->GetReservations($_SESSION['id'],$past = TRUE);
	}
	
	include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainHeader.php");
?>  
        <div id='mainContentsLogin'>
        	<div class='infoBox'>
        		<div class='resTopLeft'>
                
                    <h1>Reservation Manager - <?php echo $page->name; ($router->params[1] == "future") ? print " - Future": print " - Past";?></h1>
                   
                    <div class='reservationSelector'>
                        <h2>
                            <a href='<?php echo ROOT_URL."/reservations/past?id=".$page->id;?>' title='past reservations'>past</a> | 
                            <a href='<?php echo ROOT_URL."/reservations?id=".$page->id;?>' title='future reservations'>future</a>
                        </h2>
                    </div>
                </div>
                <div class='resTopRight'>
                	<?php if ($router->params[1] == "future") { ?>
                		<a href='#' title='+ add reservation' class='mainButton'>+ add reservation</a>
                    <?php } ?>						
                </div>
                <div class='layerEdit'>
                	<?php 
						if(!$reservations)
						{
							echo "<h3 class='center'>There are no reservations here</h3>";	
						}
						else
						{
							if ($router->params[1] == "future")
							{
								$change = function ($i) {return ++$i;};
								$start = $i = 1;
							}
							else
							{
								$change = function ($i) {return --$i;};
								$start = $i = count($reservations);
							}
							
							$start = $start & 1;
						
							$header = "\t<tr><th>#</th><th>time</th><th>name</th><th>phone #</th><th>email</th><th>table count</th><th>tables</th><th>actions</th></tr>";
							echo "<table class='tableList'>";
							echo $header;
							foreach($reservations as $r)
							{
								if (($i & 1) == $start) 
									$class = "class='rowHighlight'";
								else
									$class = "";
								
								$u = Users::GetUserFromID($r->userID);
								$time = date("g:i A l, F j, Y",strtotime("$r->date $r->startTime"));
								
								$tables = $r->GetTableList();
								
								$b = 0;
								
								foreach($tables as $t)
								{
									if ($b++ == 0)
									{
										$tList = $t->name;
									}
									else
									{
										$tList .= ", $t->name";	
									}
								}
								
								echo "\t<tr $class><td>$i</td><td>$time</td><td>$u->name</td><td>$u->phoneNumber</td><td>$u->email</td><td>$r->tableCount</td><td>$tList</td><td><form action='".THIS_PAGE."' method='post' style='display:inline'><input type='submit' value='delete' onclick=\"if(confirm('Are you sure you want to delete this reservation? This cannot be undone.')) return true; else return false;\" class='mainButtonTable' /><input type='hidden' name='delete' value='$r->id' /></form></td></tr>\n";
								$i = $change($i);
							}
							echo "</table>";
						}
					?>
                </div>
        	</div>      		
        </div>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainFooter.php"); ?>