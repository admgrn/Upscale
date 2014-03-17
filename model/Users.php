<?php
	class Users extends People
	{
		public $phoneNumber;

		public function __construct($id,$name,$username,$email,$phoneNumber,$password)
		{
			parent::__construct($id,$name,$username,$email,$password);
			$this->phoneNumber = $phoneNumber;
		}

	}	
?>
