<?php
	class Hours
	{
		public $rid;
		public $day;
		public $open;
		public $close;
		public $isClosed;
		
		public function __construct($rid = NULL,$day = NULL,$open = NULL,$close = NULL,$isClosed = NULL)
		{
			$this->rid = $rid;
			$this->day = $day;
			$this->open = $open;
			$this->close = $close;
			$this->isClosed = $isClosed;	
		}		
	}
?>