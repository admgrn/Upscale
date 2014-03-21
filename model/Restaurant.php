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
					
					return TRUE;
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