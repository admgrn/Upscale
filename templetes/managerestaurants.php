<?php
	$router = Router::Load();
	$page = $router->params;
	$session = new Session;
	
	if (isset($_POST['delete']))
	{
		Restaurant::DeleteRestaurant($_POST['delete'],$_SESSION['id']);
	}
	
	$restaurants = Restaurant::GetRestaurantList($_SESSION['id']);

	$title = "Upscale™ - Manage Restaurants";
	
	include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainHeader.php");
	
?>  
        <div id='mainContentsLogin'>
        	<div class='center'>
				<?php
                    if ($restaurants)
                    {
                    	if (array_key_exists("active",$restaurants))
						{
							$i = 1;
							
							echo "<table>";
							echo "\t<tr><th>#</th><th>name</th><th>address</th><th>table count</th><th>actions</th></tr>";
							
							foreach($restaurants["active"] as $r)
							{
								echo "\t<tr><td>$i</td><td>$r->name</td><td>$r->address</td><td>$r->tableCount</td><td><a href='".ROOT_URL."/editrestaurant/$r->id' title='edit info'>edit info</a><a href='".ROOT_URL."/edittables/$r->id' title='edit tables'>edit tables</a><a href='".ROOT_URL."/reservations/$r->id' title='reservations'>reservations</a><form action='".THIS_PAGE."' method='post'><input type='hidden' name='delete' value='$r->id' /><input type='submit' value='delete' onclick=\"if(confirm('Are you sure you want to delete this restaurant? This cannot be undone.')) return true; else return false;\" /></form></td></tr>";
								++$i;
							}
							
							echo "</table>";
						}
						
						if (array_key_exists("nonactive",$restaurants))
						{
							
						}
                        
                    }
                    else
                    {
                        echo "<h3>You are not managing any restaurants</h3>";	
                    }
                ?>
            </div>
        </div>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainFooter.php"); ?>