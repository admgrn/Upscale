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
		
		return "";
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
	
	public function TimeSelection($name)
	{
		$get = $this->GetValue($name);
			
		echo "<select name='$name' />\n";
		
		if ($get == "")
			echo "\t<option value='00:00:00'>Select Time</option>\n";
			

		for($i = 0; $i < 24;++$i)
		{
			if ($i > 11)
				$aop = "pm";
			else
				$aop = "am";
			
			for($j = 0; $j < 60;$j+=15)
			{
				$im = (($i + 11) % 12) + 1;
				$ip = str_pad($i,2,"0",STR_PAD_LEFT);
				$jp = str_pad($j,2,"0",STR_PAD_LEFT);
				$string = "$ip:$jp:00";
				
				if ($get == "value='$string'")
					$checked = " selected='selected'";
				else
					$checked = "";
				echo "\t<option value='$string'$checked>$im:$jp $aop</option>\n";		
			}
		}
		echo "</select>";
	}
}
