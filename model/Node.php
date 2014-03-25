<?php
	class Node
	{
		public $date;
		public $time;
		public $type;
		public $closed;	
		
		public function __construct($time = NULL,$date = NULL,$type = NULL,$closed = FALSE)
		{
			$this->date = $date;
			$this->time = $time;
			$this->type = $type;
			$this->closed = $closed;
		}
				
	}
?>