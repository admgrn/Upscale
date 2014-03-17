<?php
	class Session
	{		
		public function __construct()
		{
			if (!isset($_SESSION))
				session_start();
		}
		
		public function EndSession()
		{
			session_unset();
			session_destroy();	
		}
		
		public function CreateUser($name, $userName,$password,$email,$phoneNumber)
		{
			return $this->CreateAccount($name,$userName,$password,$email,$phoneNumber);
		}
		
		public function CreateManager($name, $userName,$password,$email)
		{
			return $this->CreateAccount($name,$userName,$password,$email,NULL,'Manager');
		}
	
		public function NewManagerSession($userName,$password)
		{
			return $this->NewSession($userName,$password,"Manager");
		}
	
		public function NewUserSession($userName,$password)
		{
			return $this->NewSession($userName,$password,"User");
		}
	
		private function NewSession($userName,$password,$managerOrUser)
		{
			$person = People::LoadPerson($userName,$password,$managerOrUser);
			
			if ($person == NULL)
			{
				Errors::Create("login")->SetError("main");
				return FALSE;
			}
	
			$_SESSION['id'] = $person->id;
			$_SESSION['username'] = $person->username;
			if ($managerOrUser == "User") $_SESSION['phoneNumber'] = $person->phoneNumber;
			$_SESSION['name'] = $person->name;
			$_SESSION['email'] = $person->email;
	
			$_SESSION['login'] = $managerOrUser;
	
			return TRUE;
		}
		
		private function CreateAccount($name,$userName,$password,$email,$phoneNumber,$managerOrUser = 'User')
		{
			$person = People::CreatePerson($name,$userName,$password,$email,$phoneNumber,$managerOrUser);
			
			if ($person == NULL)
			{
				return FALSE;
			}
	
			$_SESSION['id'] = $person->id;
			$_SESSION['username'] = $person->username;
			if ($managerOrUser == "User") $_SESSION['phoneNumber'] = $person->phoneNumber;
			$_SESSION['name'] = $person->name;
			$_SESSION['email'] = $person->email;
	
			$_SESSION['login'] = $managerOrUser;
	
			return TRUE;
		}
				
	}
	

?>