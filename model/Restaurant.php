<?php

	class Restaurant
	{
		public $id;
		public $name;
		public $address;
		public $phoneNumber;
		public $longitude;
		public $latitude;
		public $maxNotice;
		public $minNotice;
		public $reservationLength;
		public $tableCount;
		public $managerID;
		public $status;
		
		public function __construct($id = NULL,$name = NULL,$address = NULL,$phoneNumber = NULL,$longitude = NULL,
							   	    $latitude = NULL,$maxNotice = NULL,$minNotice = NULL,$reservationLength = NULL,
									$tableCount = NULL,$managerID = NULL,$status = NULL)
		{
			$this->id = $id;
			$this->name = $name;
			$this->address = $address;
			$this->phoneNumber = $phoneNumber;
			$this->longitude = $longitude;
			$this->latitude = $latitude;
			$this->maxNotice = $maxNotice;
			$this->minNotice = $minNotice;
			$this->reservationLength = $reservationLength;
			$this->tableCount = $tableCount;
			$this->managerID = $managerID;	
			$this->status = $status;			
		}
		
		function __clone()
		{
	
		}
		
		public static function GetRestaurantList($id)
		{
			$mysqli = openDB();
			
			
			$restaurants = array('active' => array(),'nonactive'=>array());
			
			$query = "SELECT r.id,r.name,r.address,r.phone_number,r.longitude,r.latitude,r.max_notice,
			r.min_notice,r.reservation_time,COUNT(t.id) AS table_count,r.manager_id,r.status 
					  FROM restaurants r LEFT JOIN tables t ON 
					  	r.id = t.restaurant_id WHERE r.manager_id=? GROUP BY r.id ORDER BY r.id";
			if ($stmt = $mysqli->prepare($query))
			{
				$stmt->bind_param("i",$id);
				$stmt->bind_result($id_f,$name,$address,$phoneNumber,$longitude,$latitude,$maxNotice,
								   $minNotice,$reservationLength,$tableCount,$managerID,$status);
								
				if ($stmt->execute() && $stmt->store_result() && $stmt->num_rows)
				{		   
					while($stmt->fetch())
					{
						$r = new Restaurant($id_f,$name,$address,$phoneNumber,$longitude,$latitude,$maxNotice,
								   $minNotice,$reservationLength,$tableCount,$managerID,$status);
								   
						if ($r->status)
							array_push($restaurants['active'],$r);
						else
							array_push($restaurants['nonactive'],$r);	
										
					}
					
					return $restaurants;
				}
			}
				// No restaurants found
				return FALSE;
		}
		
		public static function DeleteRestaurant($rid,$uid)
		{
			$mysqli = openDB();
			
			$stmt = $mysqli->prepare("DELETE FROM restaurants WHERE manager_id=? AND id=?");
			$stmt->bind_param("ii",$uid,$rid);
			
			$stmt->execute();	
			echo $stmt->error;		
		}
	}
?>