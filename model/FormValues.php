<?php

class FormValues
{
	private $values;

	public function __construct($type = "post")
	{
		$this->values = ($type == "post") ? $_POST : $_GET; 
	}

	public function GetValue($value)
	{
		if (isset($this->values[$value]))
		{
			$value = htmlentities($this->values[$value],ENT_QUOTES);
			
			if ($value != "")
				return "value='$value'";
			else 
				return "";
		}
	}
}
