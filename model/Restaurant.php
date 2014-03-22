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
		
		public function GetMainScheduleList($mid)
		{
			$mysqli = openDB();
			
			$i = 0;
			$list = array();

			$stmt = $mysqli->prepare("SELECT h.day_of_week,h.restaurant_id,h.open,h.close,h.closed FROM hours h JOIN restaurants r ON 
								 	   h.restaurant_id = r.id WHERE r.manager_id=? AND h.day_of_week=? AND h.restaurant_id=?");
			$stmt->bind_param("iii",$mid,$i,$this->id);
			$stmt->bind_result($day,$id,$open,$close,$closed);
			
			while($i < 7)
			{
				$stmt->execute();
				$stmt->fetch();
				
				$h = new Hours($id,$day,$open,$close,$closed);
				$list[$i++] = $h;				
			}
			
			return $list;
		}
		
		public function SetMainSchedule($a,$openname,$closename,$closedname,$isOpen,$isClosed)
		{
			$mysqli = openDB();
			
			$i = 0;
			
			$stmt = $mysqli->prepare("REPLACE INTO hours(day_of_week,restaurant_id,open,close,closed) VALUES(?,?,?,?,?)");
			$stmt->bind_param("iissi",$i,$this->id,$open,$close,$closed);
			
			while($i < 7)
			{
				if (isset($a["$closedname$i"]))
					$closed = $a["$closedname$i"];
				else
					$closed = 0;
				
				if ($closed != 1)
				{
					$closed = 0;
					
					if (isset($a["$isOpen$i"]) && $a["$isOpen$i"] == 1)
						$open = NULL;
					else
						$open = $a["$openname$i"];
						
					if (isset($a["$isClosed$i"]) && $a["$isClosed$i"] == 1)
						$close = NULL;
					else
						$close = $a["$closename$i"];
					
				}
				else
				{
					$open = NULL;
					$close = NULL;	
				}
				
				
				if (!$stmt->execute())
				{
					break;	
				}
				
				++$i;	
			}
			
			if ($i != 7)
			{
				return FALSE;
			}
			else
			{
				return TRUE;
			}
			
		}
		
		public function AddSpecialSchedule($date,$open,$close,$closed,$hasOpen,$hasClose)
		{
			$mysqli = openDB();
			$stmt = $mysqli->prepare("INSERT INTO special_hours(date,restaurant_id,open,close,closed) VALUES(?,?,?,?,?)");
			$stmt->bind_param("sissi",$date,$this->id,$open,$close,$closed);
			
			if ($closed != 1)
				$closed = 0;
			else
				$closed = 1;
			
			if ($closed == 0)
			{
				
				if ($hasOpen == 1)
					$open = NULL;
					
				if ($hasClose == 1)
					$close = NULL;
				
			}
			else
			{
				$open = NULL;
				$close = NULL;	
			}
			
			if($stmt->execute())
			{
				return TRUE;
			}
			else
			{
				$error = Errors::Create("special");
				
				switch($stmt->errno)
				{
					case 1062:
						$error->SetError("duplicate");
						break;
					default:
						$error->SetError("general");
						break;
				}				
				
				return FALSE;
			}
		
		}
		
		public function AddTable($name,$capacity,$canCombine,$description,$reserveOnline)
		{
			$mysqli = openDB();

			$stmt = $mysqli->prepare("INSERT INTO tables(name,restaurant_id,capacity,can_combine,description,reserve_online) 
									   VALUES(?,?,?,?,?,?)");
									  
			$stmt->bind_param("siiisi",$name,$this->id,$capacity,$canCombine,$description,$reserveOnline);
			
			if (trim($name) == NULL) $name = "No Name";
			if (trim($capacity) == NULL || !is_numeric(trim($capacity))) $capacity = 2;
			if (trim($description) == NULL) $description = "No Description";
			if ($canCombine == NULL) $canCombine = 0;
			if ($reserveOnline == NULL) $reserveOnline = 0;
			
			if ($stmt->execute())
			{
				return TRUE;
			}
			else
			{
				echo $stmt->error;
				return FALSE;
			}
		}
		
		public function GetTableList()
		{
			$mysqli = openDB();
			$list = array();

			$stmt = $mysqli->prepare("SELECT id,name,restaurant_id,capacity,can_combine,description,reserve_online FROM tables WHERE
										restaurant_id=?");
			$stmt->bind_param("i",$this->id);
			$stmt->bind_result($id,$name,$rid,$capacity,$canCombine,$description,$reserveOnline);
			
			$stmt->execute();
			$stmt->store_result();
			
			$i = 0;
			
			while($stmt->fetch())
			{
				$list[$i++] = new Table($id,$name,$rid,$capacity,$canCombine,$description,$reserveOnline);
			}
			
			return $list;
		}
		
		public function GetSpecialScheduleList($mid)
		{
			$mysqli = openDB();
			
			$i = 0;
			$list = array();

			$stmt = $mysqli->prepare("SELECT h.date,h.restaurant_id,h.open,h.close,h.closed FROM special_hours h JOIN restaurants r 
									  ON h.restaurant_id = r.id WHERE r.manager_id=? AND h.restaurant_id=? ORDER BY h.date ASC");
			$stmt->bind_param("ii",$mid,$this->id);
			$stmt->bind_result($date,$id,$open,$close,$closed);
			
			$stmt->execute();
			
			$i = 0;
			
			while($stmt->fetch())
			{
				$h = new Hours($id,$date,$open,$close,$closed);
				$list[$i++] = $h;				
			}
			
			return $list;
		}
		
		public function DeleteTable($id)
		{
			$mysqli = openDB();
			
			$stmt = $mysqli->prepare("DELETE FROM tables WHERE id=?");
			$stmt->bind_param("i",$id);
			
			$stmt->execute();	
		}
		
		public function DeleteSpecialSchedule($date)
		{
			$mysqli = openDB();
			
			$stmt = $mysqli->prepare("DELETE FROM special_hours WHERE date=? AND restaurant_id=?");
			$stmt->bind_param("si",$date,$this->id);
			
			if(!$stmt->execute() || !$stmt->affected_rows)
			{
				Errors::Create("special")->SetError("general");
				return FALSE;
			}
			
			return FALSE;
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
		
		public static function GetRestaurant($uid,$rid)
		{
			$mysqli = openDB();
			$r = new Restaurant;
			
			$query = "SELECT r.id,r.name,r.address,r.phone_number,r.longitude,r.latitude,r.max_notice,
						r.min_notice,r.reservation_time,COUNT(t.id) AS table_count,r.manager_id,r.status 
					  FROM restaurants r LEFT JOIN tables t ON 
					  	r.id = t.restaurant_id WHERE r.manager_id=? AND r.id=? GROUP BY r.id";
						
			$stmt = $mysqli->prepare($query);
			
			$stmt->bind_param("ii",$uid,$rid);
			$stmt->bind_result($r->id,$r->name,$r->address,$r->phoneNumber,$r->longitude,$r->latitude,$r->maxNotice,
								   $r->minNotice,$r->reservationLength,$r->tableCount,$r->managerID,$r->status);
			
			if ($stmt->execute() && $stmt->fetch())
			{
				return $r;
					
			}
			else 
			{
				return FALSE;
			}
				
		}
		
		public static function CreateRestaurant(&$uid,&$name,&$address,&$phoneNumber,&$maxRes,&$minRes,&$resTime,&$long,&$lat)
		{
			$mysqli = openDB();
			
			if (self::CheckDetails($name,$address,$phoneNumber,$maxRes,$minRes,$resTime,$long,$lat))
			{
				$stmt = $mysqli->prepare("INSERT INTO restaurants(name,address,phone_number,max_notice,min_notice,
											reservation_time,longitude,latitude,manager_id,status) VALUES(?,?,?,?,?,?,?,?,?,0)");
				$stmt->bind_param("sssdddddi",$name,$address,$phoneNumber,$maxRes,$minRes,$resTime,$long,$lat,$uid);
				
				if ($stmt->execute())
				{
					return self::GetRestaurant($uid,$stmt->insert_id);
				}
				else
				{
					$error = Errors::Create("rest");
					
					switch($stmt->errno)
					{
					case 1062:
						$error->SetError("addressDuplicate");
						break;
					default:
						$error->SetError("general");
					}	
					return FALSE;
				}
			}
		}
		
		public static function UpdateRestaurant(&$rid,&$uid,&$name,&$address,&$phoneNumber,&$maxRes,&$minRes,&$resTime,&$long,&$lat)
		{
			$mysqli = openDB();
			
			$rid = trim($rid);
			
			if (self::CheckDetails($name,$address,$phoneNumber,$maxRes,$minRes,$resTime,$long,$lat))
			{
				$stmt = $mysqli->prepare("SELECT * FROM restaurants WHERE id=? AND manager_id=? AND name=? AND address=? AND 
										  phone_number=? AND max_notice=? AND min_notice=? AND reservation_time=? AND longitude=?
										  AND latitude=?");
				$stmt->bind_param("iisssddddd",$rid,$uid,$name,$address,$phoneNumber,$maxRes,$minRes,$resTime,$long,$lat);
				
				if ($stmt->execute() && $stmt->fetch())
				{
					// item does not need to be updated
					return TRUE;	
				}
				
				$stmt->close();
				
				$stmt = $mysqli->prepare("UPDATE restaurants SET name=?,address=?,phone_number=?,max_notice=?,min_notice=?,
				reservation_time=?,longitude=?,latitude=? WHERE id=? AND manager_id=?");
				$stmt->bind_param("sssdddddii",$name,$address,$phoneNumber,$maxRes,$minRes,$resTime,$long,$lat,$rid,$uid);
				
				if ($stmt->execute() && $stmt->store_result() && $stmt->affected_rows)
				{
					return TRUE;
				}
				else
				{
					Errors::Create("rest")->SetError("General");	
					return FALSE;
				}
			}			
		}
		
		public static function CheckDetails(&$name,&$address,&$phoneNumber,&$maxRes,&$minRes,&$resTime,&$long,&$lat)
		{
			$error = Errors::Create("rest");
			$status = TRUE;
			
			$name = trim($name);
			$address = trim($address);
			$phoneNumber = trim($phoneNumber);
			$maxRes = trim($maxRes);
			$minRes = trim($minRes);
			$resTime = trim($resTime);
			$long = trim($long);
			$lat = trim($lat);
			
			if ($name == "")
			{
				$error->SetError("nameNull");
				$status = FALSE;
			}
				
			if ($address == "")
			{
				$error->SetError("addressNull");
				$status = FALSE;
			}
			
			if ($phoneNumber == "")
			{
				$error->SetError("phoneNumberNull");
				$status = FALSE;
			}
			elseif(!preg_match('/^((\(\d{3}\))|\d{3})[- ]?\d{3}[- ]?\d{4}$/', $phoneNumber))
			{
				$status = FALSE;
				$error->SetError("phoneNumberFormat");
			}
			else
			{
				$phoneNumber = preg_replace('/[^0-9]/',"",$phoneNumber);
				$phone1 = substr($phoneNumber,0,3);
				$phone2 = substr($phoneNumber,3,3);
				$phone3 = substr($phoneNumber,6,4);
				$phoneNumber = "$phone1-$phone2-$phone3";
			}
				
			if ($maxRes == "")
			{
				$error->SetError("maxResNull");
				$status = FALSE;
			}
			elseif(!preg_match('/^[0-9]+(\.([0-9]+)?)?$/',$maxRes))
			{
				$error->SetError("maxResFormat");
				$status = FALSE;
			}
			
				
			if ($minRes == "")
			{
				$error->SetError("minResNull");
				$status = FALSE;
			}
			elseif(!preg_match('/^[0-9]+(\.([0-9]+)?)?$/',$minRes))
			{
				$error->SetError("minResFormat");
				$status = FALSE;
			}
			
			if ($resTime == "")
			{
				$error->SetError("resTimeNull");
				$status = FALSE;
			}
			elseif(!preg_match('/^[0-9]+(\.([0-9]+)?)?$/',$resTime))
			{
				$error->SetError("resTimeFormat");
				$status = FALSE;
			}
			
			if ($long == "")
			{
				$error->SetError("generalNull");
				$status = FALSE;
			}
			
			if ($lat == "")
			{
				$error->SetError("generalNull");
				$status = FALSE;
			}
			
			return $status;	
				
		}
		
		public static function DeleteRestaurant($rid,$uid)
		{
			$mysqli = openDB();
			
			$stmt = $mysqli->prepare("DELETE FROM restaurants WHERE manager_id=? AND id=?");
			$stmt->bind_param("ii",$uid,$rid);
			
			$stmt->execute();
		}
	}
?>