<?php
	$router = Router::Load();
	$page = $router->params;
	$session = new Session;
	
	if (isset($_POST['delete']))
	{
		$page->DeleteTable($_POST['delete']);
	}

	if (isset($_POST['addName']))
	{
		$page->AddTable($_POST['addName'],$_POST['addCapacity'],@$_POST['addCombine'],$_POST['addDescription'],@$_POST['addReserve']);
		
	}
	
	$tables = $page->GetTableList();

	$title = "Upscale™ - Manage Tables";
	
	include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainHeader.php");
	
?>  
        <div id='mainContentsLogin'>
        	<a href='<?php echo ROOT_URL;?>/managerestaurants' style='top:10px;position:relative' class='mainButton' title='back'>< back</a>
        	<div class='center'>
            <h3 class='tableTitle'>Tables</h3><table class='tableList'";
				<?php
		 
					$i = 1;
				
					$header = "\t<tr><th>#</th><th>name</th><th>description</th><th>capacity</th><th>can combine</th><th>can reserve online</th><th>actions</th></tr>";
					echo $header;
					foreach($tables as $t)
					{
						if (($i & 1) == 1) 
							$class = "class='rowHighlight'";
						else
							$class = "";
							
						$combine = $t->canCombine == 1 ? "yes" : "no";
						$reserve = $t->reserveOnline == 1 ? "yes" : "no";
						
						echo "\t<tr $class><td>$i</td><td>$t->name</td><td>$t->description</td><td>$t->capacity</td><td>$combine</td><td>$reserve</td><td><form action='".THIS_PAGE."' method='post'><input type='submit' value='delete' onclick=\"if(confirm('Are you sure you want to delete this table? This cannot be undone.')) return true; else return false;\" class='mainButtonTable' /><input type='hidden' name='delete' value='$t->id' /></form></td></tr>";
						++$i;
					}
				?>
                	<form action='<?php echo THIS_PAGE;?>' method='post'>
					<tr>
                    	<?php if ($i > 1) echo $header;?>
                    	<td></td>
                    	<td><input type='text' name='addName' placeholder='name' class='inputFieldSmall' /></td>
                        <td><input type='text' name='addDescription' placeholder='description' class='inputFieldSmall' /></td>
                        <td><input type='text' name='addCapacity' placeholder='capacity' class='inputFieldSmall' /></td>
                        <td><input type='checkbox' name='addCombine' value='1' class='inputFieldSmall' /></td>
                        <td><input type='checkbox' name='addReserve' value='1' class='inputFieldSmall' /></td>
                        <td><input type='submit' class='mainButtonTable' value='add' /></td>
                    </form>
				</table>

            </div>
        </div>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainFooter.php"); ?>