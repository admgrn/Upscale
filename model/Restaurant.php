<?php

	class Restaurant
	{
		public $id;
		public $name;
		public $address;
		public $description;
		public $longitude;
		public $latitude;
		public $maxNoticeDays;
		public $minNoticeMinutes;
		public $reservationLength;
		public $tableCount;
		public $managerID;
		public $status;
		
		public function __construct($id = NULL,$name = NULL,$address = NULL,$description = NULL,$longitude = NULL,$latitude = NULL,
								    $maxNoticeDays = NULL,$minNoticeMinutes = NULL,$reservationLength = NULL,$tableCount = NULL,
									$managerID = NULL,$status = NULL)
		{
			$this->id = $id;
			$this->name = $name;
			$this->address = $address;
			$this->description = $description;
			$this->longitude = $longitude;
			$this->latitude = $latitude;
			$this->maxNoticeDays = $maxNoticeDays;
			$this->minNoticeMinutes = $minNoticeMinutes;
			$this->reservationLength = $reservationLength;
			$this->tableCount = $tableCount;
			$this->managerID = $managerID;	
			$this->status = $status;			
		}
		
		
		public static function GetRestaurantList($id)
		{
			$mysqli = openDB();
			
			$r = new Restaurant;
			$restaurants = array();
			
			$query = "SELECT r.id,r.name,r.address,r.longitude,r.latitude,r.max_notice_days,r.min_notice_minutes,r.reservation_length,
					  r.tableCount,COUNT(t.id) AS table_count,r.managerID,r.status FROM restaurants r LEFT JOIN tables t ON 
					  	r.id = t.restaurant_id WHERE r.manager_id=? GROUP BY r.id ORDER BY r.id";
		
			if ($stmt = $mysqli->prepare($query))
			{
				$stmt->bind_param("i",$id);
				$stmt->bind_result($r->id,$r->name,$r->address,$r->description,$r->longitude,$r->latitude,$r->maxNoticeDays,
								   $r->minNoticeMinutes,$r->reservationLength,$r->tableCount,$r->managerID,$r->status);
								
				if ($stmt->execute() && $stmt->store_result())
				{
					while($stmt->fetch())
					{
						$c = clone $r;
						
						if ($c->status)
							array_push($restaurants['active'],$c);
						else
							array_push($restaurants['nonactive'],$c);					
					}
					
					return $restaurants;
				}
			}
				// No restaurants found
				return FALSE;
		}
	}
?>