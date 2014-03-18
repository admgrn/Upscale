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
			$error = Errors::Create("edit");
			
			if ($managerOrUser == "Manager")
			{
			
			}
			else
			{
				$mysqli = openDB();
				$stmt = $mysqli->prepare("UPDATE users SET name=?,email=?,phone_number=? WHERE id=?");
				$stmt->bind_param("sssi",$name,$email,$phoneNumber,$id);
				
				if($stmt->execute())
				{		
					$stmt->close();
					return TRUE;
				}
				else
				{
					if ($stmt->errno == 1062)
					{
						// email already registered
						$error->SetError("emailDuplicate");
					}
					else
					{
						// General error
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
			$errors = Errors::Create("create");
			
			if (self::CheckCreate($name,$userName,$password,$email,$phoneNumber,$managerOrUser))
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
		
		private static function CheckCreate(&$name,&$userName,&$password,&$email,&$phoneNumber,$managerOrUser)
		{
			$errors = Errors::Create("create");
			$status = TRUE;
			
			$name = trim($name);
			$userName = trim($userName);
			$password = trim($password);
			$email = trim($email);
			$phoneNumber = trim($phoneNumber);
			
			if ($name == "")
			{
				$status = FALSE;
				$errors->SetError("nameNull");
			}
			
			if ($userName == "")
			{
				$status = FALSE;
				$errors->SetError("userNameNull");
			}
			
			if ($password == "")
			{
				$status = FALSE;
				$errors->SetError("passwordNull");
			}
				
			if ($email == "")
			{
				$status = FALSE;
				$errors->SetError("emailNull");
			}
			
			if ($phoneNumber == "" && $managerOrUser == "User")
			{
				$status = FALSE;
				$errors->SetError("phoneNumberNull");
			}
			
			return $status;
		}

		static function EncryptPassword($password)
		{
			return md5($password);
		}
	}
?>
