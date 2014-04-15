<?php
	$router = Router::Load();
	$page = $router->params;
	$title = "Upscale™ - Compare Restaurants";
	
	$restaurants = Restaurant::GetTopRestaurants();
	
	include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainHeader.php");
?>  
        <div id='mainContentsLogin'>
       		<div class='infoBox'>
            	<h1>Compare Restaurants - Top 20</h1>
                <?php 
					if (!$restaurants)
					{
						"<h3>There are no restaurants to compare</h3>";	
					}
					else
					{
				?>
				<table class='tableList'>
                	<tr><th>Rank</th><th>Name</th><th>Reservation Count</th></tr>
                    <?php
						$i = 1;
						foreach($restaurants as $r)
						{
							$count = $r[1];
							$r = $r[0];
							
							if (($i & 1) == 1) 
								$class = "class='rowHighlight'";
							else
								$class = "";
							
							echo "\t<tr $class style='height:30px'><td>$i</td><td>$r->name</td><td>$count</td>\n";
							++$i;
						}
					?>                
                </table>
                
                
                <?php } ?>
            </div>
        </div>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainFooter.php"); ?>