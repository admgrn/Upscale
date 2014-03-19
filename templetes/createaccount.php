<?php
	$router = Router::Load();
	$page = $router->params;
	
	if (isset($_POST['createUse']))
	{
		$session = new Session;
		$status = FALSE;
		
		if ($_POST['createUse'] == 'user')
			$status = $session->CreateUser($_POST['loginName'],$_POST['loginUsername'],$_POST['loginPassword'],$_POST['loginEmail'],$_POST['loginPhoneNumber']);
		else
		{
			$status = $session->CreateManager($_POST['loginName'],$_POST['loginUsername'],$_POST['loginPassword'],$_POST['loginEmail']);
		}
			
		if ($status) header('Location: ' . ROOT_URL);
	}
	
	$errors = Errors::Create("create");
	$form = new FormValues;
	$title = "Upscale™ -"; if ($page == "manager") $title .= " Manager"; $title .= " Create Account";
	
	include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/loginHeader.php");
	
?>  
        <div id='mainContentsLogin'>
       		<?php
				if ($page == "user")
					echo "<h2 class='textCenter'>Create User Account</h2>";
				else
					echo "<h2 class='textCenter'>Create Manager Account</h2>";
			?>  
            <div id='createBox'>
            	<?php 
					if ($errors->GetState()) echo "<ul class='loginErrorList'>";
					$errors->GetError("general","\t<li>An Error Occured, please try again</li>");
					$errors->GetError("username","\t<li>Username Already Exists</li>");
					$errors->GetError("nameNull","\t<li>Name cannot be empty</li>");
					$errors->GetError("userNameNull","\t<li>Username cannot be empty</li>");
					$errors->GetError("passwordNull","\t<li>Password cannot be empty</li>");
					$errors->GetError("passwordLen","\t<li>New Password must be at least 6 characters</li>");
					$errors->GetError("emailNull","\t<li>Email cannot be empty</li>");
					$errors->GetError("email","\t<li>Email Already Exists</li>");
					$errors->GetError("emailFormat","\t<li>Invalid Email address</li>");
					$errors->GetError("phoneFormat","\t<li>Invalid Phone Format</li>");
					$errors->GetError("phoneNumberNull","\t<li>Phone Number cannot be empty</li>");
					if ($errors->GetState()) echo "</ul>";
				?>
				
                <form id='loginForm' method='post' action='<?php echo THIS_PAGE;?>'>
                	<input type='text' placeholder='name' class='inputField<?php $errors->GetError("nameNull"," borderError");?>' name='loginName' <?php echo $form->GetValue('loginName');?> />
					<input type='text' placeholder='username' class='inputField<?php $errors->GetError("userNameNull"," borderError");?>' name='loginUsername' <?php echo $form->GetValue('loginUsername');?> />
                    <input type='password' placeholder='password' class='inputField<?php $errors->GetError(array("passwordLen","passwordNull")," borderError");?>' name='loginPassword' <?php echo $form->GetValue('loginPassword');?> />
                    <input type='text' placeholder='email' class='inputField<?php $errors->GetError(array("emailNull","emailFormat")," borderError");?>' name='loginEmail' <?php echo $form->GetValue('loginEmail');?> />
                    <?php
						if ($page == "user")
						{
							echo " <input type='text' placeholder='phone number' class='inputField";
							$errors->GetError(array("phoneFormat","phoneNumberNull")," borderError");
							echo "' name='loginPhoneNumber' " . $form->GetValue('loginPhoneNumber') . " />";
							echo "<a href='" . ROOT_URL . "' class='mainButton' value='back' style='white-space:nowrap'>< back</a>"; 
							echo "<input type='hidden' name='createUse' value='user' />";
						}
						else
						{
							echo "<a href='" . ROOT_URL . "/managers' class='mainButton' value='back' style='white-space:nowrap'>< back</a>"; 
							echo "<input type='hidden' name='createUse' value='manager' />";
						}
					?>
                    <input type='submit' class='mainButton' value='create account' />
                 </form>
            </div>
       		<div class='infoBox about'>
            	<h1>Create an UPSCALE™ Account</h1>
            	<p>It's Easy! Just plug in your information and begin searching for reservations.</p>
            </div>
        </div>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/loginFooter.php"); ?>