<?php
	class Errors
	{
		private $state = FALSE;				// Default there are no errors
		private $errors = array();
		
		private function __construct()
		{
		}
		
		static function Create($index)
		{
			static $items = array();
			if (array_key_exists($index,$items))
			{
				return $items[$index];
			}
			else
			{
				$items[$index] = new Errors();
				return $items[$index];	
			}	
		}
		
		// Returns true or false if errors exist
		public function GetState()
		{
			return $this->state;
		}
		
		// Returns true of false if value exists and echos string if passed
		public function GetError($value,$string = NULL)
		{
			if(array_key_exists($value,$this->errors))
			{
				if ($string != NULL) echo $string;
				return TRUE;
			}
			
			return FALSE;
		}
		
		public function SetError($value)
		{
			$this->state = TRUE;
			$this->errors[$value] = TRUE;
		}
		
		public function RemoveError($value)
		{
			unset($this->errors[$value]);
			if (count($this->errors) <= 0) $this->state = FALSE;
		}
	}
?>