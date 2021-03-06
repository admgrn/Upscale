<?php
	$router = Router::Load();
	$page = $router->params;
	$errors = Errors::Create("edit");

	if (isset($_POST['updateUse']))
	{
		$session = new Session;
		$status = ($page == "user" ? $session->UpdateUser($_SESSION['id'],$_POST['name'],$_POST['email'],$_POST['phoneNumber'],$_POST['oldPassword'],$_POST['newPassword']) :
						  		     $session->UpdateManager($_SESSION['id'],$_POST['name'],$_POST['email'],$_POST['oldPassword'],$_POST['newPassword']));
									 
		if (!$status)
			$form  = new FormValues;
		else
			$form = new FormValues($_SESSION);
				
	}
	else
	{
		$session = new Session;
		$form  = new FormValues($_SESSION);
	}
	
	
	$title = "Upscale™ -"; if ($page == "manager") $title .= " Manager"; $title .= " Edit Account";
	include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainHeader.php");
?>  
        <div id='mainContentsLogin'>
       		<div id='editBox' class='infoBox'>
           		<h1>Edit Account</h1>
            	<?php 
					if ($status) 
					{
						echo "<ul class='loginErrorListBlack'>";
						echo "\t<li>Information Successfully Updated</li>";
						echo "</ul>";
					}
					
					if ($errors->GetState()) echo "<ul class='loginErrorListBlack'>";
					$errors->GetError("general","\t<li>An Error Occured, please try again</li>");
					$errors->GetError("username","\t<li>Username Already Exists</li>");
					$errors->GetError("emailDuplicate","\t<li>Email Linked to another account</li>");
					$errors->GetError("emailFormat","\t<li>Invalid Email address</li>");
					$errors->GetError("nameNull","\t<li>Name cannot be empty</li>");
					$errors->GetError("userNameNull","\t<li>Username cannot be empty</li>");
					$errors->GetError("emailNull","\t<li>Email cannot be empty</li>");
					$errors->GetError("phoneNumberNull","\t<li>Phone Number cannot be empty</li>");
					$errors->GetError("phoneFormat","\t<li>Invalid Phone Format</li>");
					$errors->GetError("incorrectPassword","\t<li>Old Password was incorrect</li>");
					$errors->GetError("passwordLen","\t<li>New Password must be at least 6 characters</li>");
					$errors->GetError("passwordNull","\t<li>Password cannot be empty</li>");
					if ($errors->GetState()) echo "</ul>";
				?>
				
                <form id='loginForm' method='post' action='<?php echo THIS_PAGE;?>'>
                	<input type='text' placeholder='name' class='inputField<?php $errors->GetError("nameNull"," borderError");?>' name='name' <?php echo $form->GetValue('name');?> />
					<input type='text' placeholder='username' class='inputField<?php $errors->GetError("userNameNull"," borderError");?>' <?php echo $form->FormatValue($_SESSION['username']);?> disabled='disabled' />
                    <input type='text' placeholder='email' class='inputField<?php $errors->GetError(array("emailDuplicate","emailNull","emailFormat")," borderError");?>' name='email' <?php echo $form->GetValue('email');?> />
                    <?php
						if ($page == "user")
						{
							echo " <input type='text' placeholder='phone number' class='inputField";
							$errors->GetError(array("phoneFormat","phoneNumberNull")," borderError");
							echo "' name='phoneNumber' " . $form->GetValue('phoneNumber') . " />";
							echo "<input type='hidden' name='updateUse' value='user' />";
						}
						else
						{
							echo "<input type='hidden' name='updateUse' value='manager' />";
						}
					?>
                    <input type='password' placeholder='old password' class='inputField<?php $errors->GetError(array("incorrectPassword","oldPasswordNull")," borderError");?>' name='oldPassword' />
                    <input type='password' placeholder='new password' class='inputField<?php $errors->GetError(array("incorrectPassword","passwordLen")," borderError");?>' name='newPassword' />
                    <input type='submit' class='mainButton' value='update' />
                 </form>
            </div>
        </div>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainFooter.php"); ?>