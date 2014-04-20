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
                <?php if ($page == "user") { ?>
            	<p>Welcome to Upscale, a brand new easy to use reservation finder. Not sure what you want to have for dinner?</p>
                <p>Our tools make make it easy to find available resevations in the Tallahassee area.</p>
                <p>Login to begin using UPSCALE™</p>
                <?php }else{  ?>
                <p>Welcome to Upscale, a brand new easy to use reservation finder. UPSCALE™ helps restauants connect with clients through easy to use tools.</p>
                <p>Upscale allows restaurants to create a profile online which will makes your restaurant more asscessable.</p>
                <p>Login to begin using UPSCALE™</p>
                <?php } ?>                
            </div>
        </div>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/loginFooter.php"); ?>