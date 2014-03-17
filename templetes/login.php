<?php
	$router = Router::Load();
	$page = $router->params;
	$form = new FormValues;
	$error = Errors::Create("login");
	$title = "Upscale™ -"; if ($page == "manager") $title .= " Manager"; $title .= " Login";
	include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/loginHeader.php");
?>  
        <div id='mainContentsLogin'>
       		<?php
				if ($page == "user")
					echo "<h2 class='textCenter'>Reservation Finder</h2>";
				else
					echo "<h2 class='textCenter'>Manager Login</h2>";
			?>  
            <div id='loginBox'>
            	<?php $error->GetError("main","<h3 class='loginError'>invalid login</h3>");?>
                <form id='loginForm' method='post' action='<?php echo THIS_PAGE;?>'>
					<input type='text' placeholder='username' class='inputField<?php $error->GetError("main"," borderError");?>' name='loginUsername' <?php echo $form->GetValue('loginUsername');?> />
                    <input type='password' placeholder='password' class='inputField<?php $error->GetError("main"," borderError");?>' name='loginPassword' <?php echo $form->GetValue('loginPassword');?> />
                    <?php
						if ($page == "user")
						{
							echo "<a href='" . ROOT_URL . "/createaccount' class='mainButton' value='create account' style='display:inline-block'>create account</a>"; 
							echo "<input type='hidden' name='loginUse' value='user' />";
						}
						else
						{
							echo "<a href='" . ROOT_URL . "/createaccount/manager' class='mainButton' value='create account' style='display:inline-block'>create account</a>"; 
							echo "<input type='hidden' name='loginUse' value='manager' />";
						}
					?>
                    <input type='submit' class='mainButton' value='login' />
                 </form>
            </div>
       		<div class='infoBox about'>
            	<h1>About UPSCALE™</h1>
            	<p>This is some information about UPSCALE™. It is some very informational information that will explain a lot about the website. It will be very interesting and informative.</p>
                <p> This is the second paragraph and it will also be very informative.</p>
            </div>
        </div>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/loginFooter.php"); ?>