<?php

	// Class to manage users
	class People
	{
		public $id;
		public $name;
		public $username;
		public $email;
		protected $password;

		public function __construct($id,$name,$username,$email,$password)
		{
			$this->id = $id;
			$this->name = $name;
			$this->username = $username;
			$this->email = $email;
			$this->password = $password;
		}

		public function ComparePassword($password)
		{
			if ( $password == $this->password )
				return TRUE;
			else
				return FALSE;
		}
		
		static function UpdatePerson($id,&$name,&$email,&$oldPassword,&$newPassword,&$phoneNumber,$managerOrUser = "User")
		{
			$errorName = "edit";
			$error = Errors::Create($errorName);
			
			if ($managerOrUser == "Manager")
			{
				$mysqli = openDB();
				
				if (trim($oldPassword) != "" && trim($newPassword) != "")
				{
					if (self::CheckCreatePassword($name,$userName,$newPassword,$email,$phoneNumber,$managerOrUser,$errorName,FALSE))
					{
						$oldPassword = self::EncryptPassword(trim($oldPassword));
						$newPassword = self::EncryptPassword(trim($newPassword));
						
						$stmt = $mysqli->prepare("SELECT * FROM managers WHERE id=? AND name=? AND email=? AND password=?");
						$stmt->bind_param("isss",$id,$name,$email,$newPassword);
						
						if ($stmt->execute() && $stmt->store_result() && $stmt->num_rows)
						{
							return TRUE;
						}
						
						$stmt->close();		
						$stmt = $mysqli->prepare("UPDATE managers SET name=?,email=?,password=? WHERE id=? AND password=?");
						$stmt->bind_param("sssis",$name,$email,$newPassword,$id,$oldPassword);	
					}
					else
					{
						return FALSE;
					}
				}
				else
				{
					if (self::CheckCreate($name,$userName,$email,$phoneNumber,$managerOrUser,$errorName,FALSE))
					{
						$stmt = $mysqli->prepare("SELECT * FROM users WHERE id=? AND name=? AND email=?");
						$stmt->bind_param("iss",$id,$name,$email);
						
						if ($stmt->execute() && $stmt->store_result() && $stmt->num_rows)
						{
							return TRUE;
						}
						
						$stmt->close();					
						$stmt = $mysqli->prepare("UPDATE users SET name=?,email=? WHERE id=?");
						$stmt->bind_param("ssi",$name,$email,$id);
					}
					else
					{
						return FALSE;
					}
				}				
				
				if($stmt->execute() && $stmt->affected_rows)
				{
					$stmt->close();
					return TRUE;
				}
				else
				{
					switch ($stmt->errno)
					{
						case 1062:
							$error->SetError("emailDuplicate");
							break;
						case 0:
							$error->SetError("incorrectPassword");
							break;
						default:
							$error->SetError("general");
					}
				}
				
				return FALSE;

			}
			else
			{
				$mysqli = openDB();
				
				if (trim($oldPassword) != "" && trim($newPassword) != "")
				{
					if (self::CheckCreatePassword($name,$userName,$newPassword,$email,$phoneNumber,$managerOrUser,$errorName,FALSE))
					{
						$oldPassword = self::EncryptPassword(trim($oldPassword));
						$newPassword = self::EncryptPassword(trim($newPassword));
						
						$stmt = $mysqli->prepare("SELECT * FROM users WHERE id=? AND name=? AND email=? AND phone_number=? AND password=?");
						$stmt->bind_param("issss",$id,$name,$email,$phoneNumber,$newPassword);
						
						if ($stmt->execute() && $stmt->store_result() && $stmt->num_rows)
						{
							return TRUE;
						}
						
						$stmt->close();		
						$stmt = $mysqli->prepare("UPDATE users SET name=?,email=?,phone_number=?,password=? WHERE id=? AND password=?");
						$stmt->bind_param("ssssis",$name,$email,$phoneNumber,$newPassword,$id,$oldPassword);	
					}
					else
					{
						return FALSE;
					}
				}
				else
				{
					if (self::CheckCreate($name,$userName,$email,$phoneNumber,$managerOrUser,$errorName,FALSE))
					{
						$stmt = $mysqli->prepare("SELECT * FROM users WHERE id=? AND name=? AND email=? AND phone_number=?");
						$stmt->bind_param("isss",$id,$name,$email,$phoneNumber);
						
						if ($stmt->execute() && $stmt->store_result() && $stmt->num_rows)
						{
							return TRUE;
						}
						
						$stmt->close();					
						$stmt = $mysqli->prepare("UPDATE users SET name=?,email=?,phone_number=? WHERE id=?");
						$stmt->bind_param("sssi",$name,$email,$phoneNumber,$id);
					}
					else
					{
						return FALSE;
					}
				}				
				
				if($stmt->execute() && $stmt->affected_rows)
				{
					$stmt->close();
					return TRUE;
				}
				else
				{
					switch ($stmt->errno)
					{
						case 1062:
							$error->SetError("emailDuplicate");
							break;
						case 0:
							$error->SetError("incorrectPassword");
							break;
						default:
							echo $stmt->errno;
							$error->SetError("general");
					}
				}
				
				return FALSE;
			}
		}

		static function LoadPerson($userName,$password,$managerOrUser = "User")
		{
			if ( $managerOrUser == "Manager")
			{
				$mysqli = openDB();
				$stmt = $mysqli->prepare("SELECT * FROM managers WHERE username =? AND password =?");
				$stmt->bind_param("ss",$userName,$password);
				$stmt->bind_result($values['id'],$values['name'],$values['username'],$values['email'],$values['password']);
				
				$password = self::EncryptPassword($password);	
			
				if($stmt->execute() && $stmt->fetch())
				{		
					$stmt->close();
					return new Managers($values['id'],$values['name'],$values['username'],$values['email'],$values['password']);
				}
			}
			else		
			{
				$mysqli = openDB();
				$stmt = $mysqli->prepare("SELECT * FROM users WHERE username =? AND password =?");
				$stmt->bind_param("ss",$userName,$password);
				$stmt->bind_result($values['id'],$values['name'],$values['username'],$values['email'],$values['password'],$values['phone_number']);
				
				$password = self::EncryptPassword($password);	
			
				if($stmt->execute() && $stmt->fetch())
				{		
					$stmt->close();
					return new Users($values['id'],$values['name'],$values['username'],$values['email'],$values['phone_number'],$values['password']);
				}
			}
			
			// Person does not exist
			return NULL;
		}
		
		static function CreatePerson($name,$userName,$password,$email,$phoneNumber,$managerOrUser)
		{
			$errorName = "create";
			$errors = Errors::Create($errorName);
			
			if (self::CheckCreatePassword($name,$userName,$password,$email,$phoneNumber,$managerOrUser,$errorName))
			{
				if ($managerOrUser == "Manager")
				{					
					$phoneNumber = trim($phoneNumber);
					
					$mysqli = openDB();
					$stmt = $mysqli->prepare("SELECT id FROM managers WHERE username=?");
					$stmt->bind_param("s",$userName);
					
					if ($stmt->execute() && $stmt->fetch())
						$errors->SetError("username");
	
					$stmt->close();
					$stmt = $mysqli->prepare("SELECT id FROM managers WHERE email=?");
					$stmt->bind_param("s",$email);
					
					if ($stmt->execute() && $stmt->fetch())
						$errors->SetError("email");
					
					if ($errors->GetState()) 
						return NULL;
					
					$stmt->close();
					$stmt = $mysqli->prepare("INSERT INTO managers(name,username,email,password) VALUES(?,?,?,?)");
					$stmt->bind_param("ssss",$name,$userName,$email,$password);
					
					$password = self::EncryptPassword($password);
					
					if ($stmt->execute())
					{				
						$stmt = $mysqli->prepare("SELECT * FROM managers WHERE username =? AND password =?");
						$stmt->bind_param("ss",$userName,$password);
						$stmt->bind_result($values['id'],$values['name'],$values['username'],$values['email'],$values['password']);
						
						
						if ($stmt->execute() && $stmt->fetch())
						{				
							$stmt->close();
							return new Managers($values['id'],$values['name'],$values['username'],$values['email'],$values['phone_number']);
						}
					}					
				}
				else		
				{					
					$phoneNumber = trim($phoneNumber);
					
					$mysqli = openDB();
					$stmt = $mysqli->prepare("SELECT id FROM users WHERE username=?");
					$stmt->bind_param("s",$userName);
					
					if ($stmt->execute() && $stmt->fetch())
						$errors->SetError("username");
	
					$stmt->close();
					$stmt = $mysqli->prepare("SELECT id FROM users WHERE email=?");
					$stmt->bind_param("s",$email);
					
					if ($stmt->execute() && $stmt->fetch())
						$errors->SetError("email");
					
					if ($errors->GetState()) 
						return NULL;
					
					$stmt->close();
					$stmt = $mysqli->prepare("INSERT INTO users(name,username,email,password,phone_number) VALUES(?,?,?,?,?)");
					$stmt->bind_param("sssss",$name,$userName,$email,$password,$phoneNumber);
					
					$password = self::EncryptPassword($password);
					
					if ($stmt->execute())
					{				
						$stmt = $mysqli->prepare("SELECT * FROM users WHERE username =? AND password =?");
						$stmt->bind_param("ss",$userName,$password);
						$stmt->bind_result($values['id'],$values['name'],$values['username'],$values['email'],$values['password'],$values['phone_number']);
												
						if ($stmt->execute() && $stmt->fetch())
						{			
							
							$stmt->close();
							return new Users($values['id'],$values['name'],$values['username'],$values['email'],$values['phone_number'],$values['password']);
						}
					}
				}
			}
			
			// Person could not be created
			$errors->SetError("General");
			return NULL;
		}
		
		private static function CheckCreatePassword(&$name,&$userName,&$password,&$email,&$phoneNumber,$managerOrUser,$error,$checkUsername = TRUE)
		{
			$errors = Errors::Create($error);
			$status = TRUE;
			
			$password = trim($password);
			
			if ($password == "")
			{
				$status = FALSE;
				$errors->SetError("passwordNull");
			}
			elseif(strlen($password) < 6)
			{
				$status = FALSE;
				$errors->SetError("passwordLen");
			}			
			
			return $status & self::CheckCreate($name,$userName,$email,$phoneNumber,$managerOrUser,$error,$checkUsername);
		}
		
		private static function CheckCreate(&$name,&$userName,&$email,&$phoneNumber,$managerOrUser,$error,$checkUsername = TRUE)
		{
			$errors = Errors::Create($error);
			$status = TRUE;
			
			$name = trim($name);
			if ($checkUsername) $userName = trim($userName);
			$email = trim($email);
			$phoneNumber = trim($phoneNumber);
			
			if ($name == "")
			{
				$status = FALSE;
				$errors->SetError("nameNull");
			}
						
			if ($checkUsername && $userName == "")
			{
				$status = FALSE;
				$errors->SetError("userNameNull");
			}
				
			if ($email == "")
			{
				$status = FALSE;
				$errors->SetError("emailNull");
			}
			elseif(!filter_var($email, FILTER_VALIDATE_EMAIL))
			{
				$status = FALSE;
				$errors->SetError("emailFormat");	
			}
			
			if ($phoneNumber == "" && $managerOrUser == "User")
			{
				$status = FALSE;
				$errors->SetError("phoneNumberNull");
			}
			elseif(!preg_match('/^((\(\d{3}\))|\d{3})[- ]?\d{3}[- ]?\d{4}$/', $phoneNumber) && $managerOrUser == "User")
			{
				$status = FALSE;
				$errors->SetError("phoneFormat");
			}
			else
			{
				$phoneNumber = preg_replace('/[^0-9]/',"",$phoneNumber);
				$phone1 = substr($phoneNumber,0,3);
				$phone2 = substr($phoneNumber,3,3);
				$phone3 = substr($phoneNumber,6,4);
				$phoneNumber = "$phone1-$phone2-$phone3";
			}
			
			return $status;
			
		}
		
		public static function GetUserFromID($id)
		{
			$mysqli = openDB();
			$stmt = $mysqli->prepare("SELECT * FROM users WHERE id=?");
			$stmt->bind_param("i",$id);	
			
			$stmt->bind_result($id,$name,$username,$email,$password,$phone_number);
		
			if($stmt->execute() && $stmt->fetch())
			{		
				$stmt->close();
				return new Users($id,$name,$username,$email,$phone_number,$password);
			}
			
		}		

		static function EncryptPassword($password)
		{
			return md5($password);
		}
	}
?>
