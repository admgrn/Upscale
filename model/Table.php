<?php

	class Table
	{
		public $id;
		public $name;
		public $rid;
		public $capacity;
		public $canCombine;
		public $description;
		public $reserveOnline;
		
		public function __construct($id = NULL,$name = NULL,$rid = NULL,$capacity = NULL,$canCombine = NULL,$description = NULL,
									$reserveOnline = NULL)
		{
			$this->id = $id;
			$this->name = $name;
			$this->rid = $rid;
			$this->capacity = $capacity;
			$this->canCombine = $canCombine;
			$this->description = $description;
			$this->reserveOnline = $reserveOnline;
		}
		
	}
?>