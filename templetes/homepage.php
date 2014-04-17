<?php
  $router = Router::Load();
  $page = $router->params;
  $title = "Upscale™ -"; if ($page == "manager") $title .= " Manager"; $title .= " Home";
  include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainHeader.php");
  if ($page == "user")
  	$reservations = Reservations::GetAllUserReservations($_SESSION['id'],5);
  
?>
        <div id='mainContentsLogin'>
        	<div class='infoBox info'>
            	<h1>Welcome to Upscale</h1>
                <p>Here you will find some information</p>
            </div>
       		<div class='infoBox info'>
            	<h1><?php $page == "user" ? print "User" : print "Manager";?> Info</h1>
            	<table class='userDetails'>
                    <tr>
                        <td class='first'>Name</td><td><?php echo $_SESSION['name'];?></td>
                    </tr>
                    <tr>
                        <td class='first'>Username</td><td><?php echo $_SESSION['username'];?></td>
                    </tr>
                    <tr>
                        <td class='first'>email</td><td><?php echo $_SESSION['email'];?></td>
                    </tr>
                    <?php if ($page == "user") { ?>
                    <tr>
                        <td class='first'>phone number</td><td><?php echo $_SESSION['phoneNumber'];?></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
            <?php if ($page == "user") { ?>
            <div class='layerEdit'>
                <div class='infoBox'>
                	<h1>Upcoming Reservations</h1>
                    <?php 
						if (!$reservations) 
						{
							echo "<h3 class='center'>You have no upcoming reservations</h3>";	
						}
						else
						{
							echo "<table class='tableList' style='margin-top:20px;'>\n";
							echo "\t<tr><th>#</th><th>restaurant</th><th>address</th><th>time</th><th>action</th><tr>\n";
							$i = 1;
							
							foreach($reservations as $r)
							{
								if (($i & 1) == 1) 
									$class = "class='rowHighlight'";
								else
									$class = "";
								
									$time = date("g:i A l, F j, Y",strtotime("$r->date $r->startTime"));
									
									$rest = Restaurant::GetRestaurant($r->restaurantID,$active = TRUE);
									
									if ($rest)
									{
								
										echo "\t<tr $class><td>$i</td><td>$rest->name</td><td>$rest->address</td><td>$time</td><td><a href='".ROOT_URL."/reservations/edit?id=$r->id' title='view reservation' class='mainButtonTable'>view</a></td></tr>\n";
										++$i;
									}
							}
							
							echo "</table>";
						}
					?>
                    
                </div>
            </div>
            <?php } ?>
        </div>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainFooter.php"); ?>
