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
        	<a href='<?php echo ROOT_URL;?>/newrestaurant' style='top:10px;position:relative' class='mainButton' title='+ new restaurant'>+ new restaurant</a>
        	<div class='center'>
				<?php
                    if ($restaurants)
                    {
                    	if (count($restaurants['active']))
						{
							$i = 1;
								
							echo "<h3 class='tableTitle'>Active Restaurants</h3><table class='tableList'>";
							echo "\t<tr><th>#</th><th>name</th><th>address</th><th>table count</th><th>actions</th></tr>";
							foreach($restaurants["active"] as $r)
							{
								if (($i & 1) == 1) 
								$class = "class='rowHighlight'";
							else
								$class = "";
								
								echo "\t<tr $class><td>$i</td><td>$r->name</td><td>$r->address</td><td>$r->tableCount</td><td style='width:305px;'><a href='".ROOT_URL."/editrestaurant?id=$r->id' title='edit info' class='mainButtonTable'>edit info</a><a href='".ROOT_URL."/edittables?id=$r->id' title='edit tables' class='mainButtonTable'>edit tables</a><a href='".ROOT_URL."/reservations?id=$r->id' title='reservations' class='mainButtonTable'>reservations</a><form action='".THIS_PAGE."' method='post' style='display:inline'><input type='hidden' name='delete' value='$r->id' class='mainButtonTable' /><input type='submit' value='delete' onclick=\"if(confirm('Are you sure you want to delete this restaurant? This cannot be undone.')) return true; else return false;\" class='mainButtonTable' /></form></td></tr>";
								++$i;
							}
							
							echo "</table>";
						}
						
						if (count($restaurants['nonactive']))
						{
							$i = 1;
								
							echo "<h3 class='tableTitle'>Pending Restaurants</h3><table class='tableList'>";
							echo "\t<tr><th>#</th><th>name</th><th>address</th><th>table count</th><th>actions</th></tr>";
							foreach($restaurants["nonactive"] as $r)
							{
								if (($i & 1) == 1) 
								$class = "class='rowHighlight'";
							else
								$class = "";
								
								echo "\t<tr $class><td>$i</td><td>$r->name</td><td>$r->address</td><td>$r->tableCount</td><td><a href='".ROOT_URL."/editrestaurant?id=$r->id' title='edit info' class='mainButtonTable'>edit info</a><a href='".ROOT_URL."/edittables?id=$r->id' title='edit tables' class='mainButtonTable'>edit tables</a><form action='".THIS_PAGE."' method='post' style='display:inline'><input type='hidden' name='delete' value='$r->id' class='mainButtonTable' /><input type='submit' value='delete' onclick=\"if(confirm('Are you sure you want to delete this restaurant? This cannot be undone.')) return true; else return false;\" class='mainButtonTable' /></form></td></tr>";
								++$i;
							}
							
							echo "</table>";
							
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