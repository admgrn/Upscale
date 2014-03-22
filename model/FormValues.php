<?php

class FormValues
{
	private $values;

	public function __construct($type = "post")
	{
		$this->values = ($type == "post") ? $_POST : $type; 
	}

	public function GetValue($value)
	{
		if (isset($this->values[$value]))
		{
			return $this->FormatValue($this->values[$value]);
		}
	}
	
	public function SendItem($item,$send)
	{
		if (isset($this->values[$item]))
		{
			return $send;	
		}
	}
	
	public function FormatValue($value)
	{
		$value = htmlentities($value,ENT_QUOTES);
			
		if ($value != "")
			return "value='$value'";
		else 
			return "";
	}
}
