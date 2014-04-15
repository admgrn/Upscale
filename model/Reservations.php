<?php
	class Reservations
	{
		public $id;
		public $restaurantID;
		public $userID;
		public $date;
		public $startTime;
		public $numberOfPeople;	
		public $tableCount;
		public $name;
		
		public function __construct($id = NULL,$restaurantID = NULL,$userID = NULL,$date = NULL,$startTime = NULL,
									  $numberOfPeople = NULL,$tableCount = NULL,$name = NULL)
		{
			$this->id = $id;
			$this->restaurantID = $restaurantID;
			$this->userID = $userID;
			$this->date = $date;
			$this->startTime = $startTime;
			$this->numberOfPeople = $numberOfPeople;
			$this->tableCount = $tableCount;
			$this->name = $name;
		}
		
		public static function MakeReservation(Restaurant $r,$uid,&$date,&$time,&$numberOfPeople)
		{			
			if (!self::Validate($date,$time,$numberOfPeople))
				return FALSE;
				
			$foundTables = self::SearchReservation($r,$uid,$date,$time,$numberOfPeople);
			
			if (!$foundTables)
			{
				return FALSE;	
			}
			
			$mysqli = openDB();
			
			$mysqli->begin_transaction();
			
			$stmt = $mysqli->prepare("INSERT INTO reservations (restaurant_id,user_id,date,start_time,number_of_people) 
												  VALUES(?,?,?,?,?)");
			
			$stmt->bind_param("iissi",$r->id,$uid,$date,$time,$numberOfPeople);
			
			if(!$stmt->execute())
			{
				Errors::Create("resCreate")->SetError("generalError");
				$mysqli->rollback();
				return FALSE;
			}
			
			
			$insertID = $stmt->insert_id;
			
			$stmt->close();
			
			$stmt = $mysqli->prepare("INSERT INTO tables_in_reservation (reservation_id,table_id) VALUES(?,?)");
			
			$stmt->bind_param("ii",$insertID,$tid);
			
			foreach($foundTables as $found)
			{
				$tid = $found->id;
				
				if(!$stmt->execute())
				{
					Errors::Create("resCreate")->SetError("generalError");
					$mysqli->rollback();
					return FALSE;					
				}
			}
		
			$mysqli->commit();
			
			// Return Reservation Object	
			return self::GetReservationFromReservationID($insertID);
			
		}
		
		public static function ManagerMakeReservation(Restaurant $r,$mid,&$name,&$date,&$time,&$numberOfPeople)
		{	
			$status = TRUE;		
			
			if (!self::Validate($date,$time,$numberOfPeople))
				$status = FALSE;
				
			$name = trim($name);
			
			if ($name == "")
			{
				Errors::Create("resCreate")->SetError("nameSet");
				return FALSE;	
			}
			
			if ($status == FALSE)
				return FALSE;
				
			if ($r->managerID != $mid)
			{
				Errors::Create("resCreate")->SetError("generalError");
				return FALSE;
			}
				
			if(!self::GetBounds($r,$date,$time))	
			{
				Errors::Create("resCreate")->SetError("boundError");
				return FALSE;	
			}
			
			$foundTables = self::GetTables($r,$date,$time,$numberOfPeople);
		
			
			if (!$foundTables)
			{
				return FALSE;	
			}
			
			$mysqli = openDB();
			
			$mysqli->begin_transaction();
			
			$stmt = $mysqli->prepare("INSERT INTO reservations (restaurant_id,user_id,date,start_time,number_of_people,name) 
												  VALUES(?,NULL,?,?,?,?)");
			
			$stmt->bind_param("issis",$r->id,$date,$time,$numberOfPeople,$name);
			
			if(!$stmt->execute())
			{
				Errors::Create("resCreate")->SetError("generalError");
				$mysqli->rollback();
				return FALSE;
			}
			
			
			$insertID = $stmt->insert_id;
			
			$stmt->close();
			
			$stmt = $mysqli->prepare("INSERT INTO tables_in_reservation (reservation_id,table_id) VALUES(?,?)");
			
			$stmt->bind_param("ii",$insertID,$tid);
			
			foreach($foundTables as $found)
			{
				$tid = $found->id;
				
				if(!$stmt->execute())
				{
					Errors::Create("resCreate")->SetError("generalError");
					$mysqli->rollback();
					return FALSE;					
				}
			}
		
			$mysqli->commit();
			
			// Return Reservation Object	
			return self::GetReservationFromReservationID($insertID);
			
		}
		
		public static function GetAllUserReservations($id)
		{
			$mysqli = openDB();
			
			$stmt = $mysqli->prepare("SELECT r.id,r.restaurant_id,r.user_id,r.date,r.start_time,r.number_of_people,
									  COUNT(tr.reservation_id) AS table_count
									  FROM reservations r LEFT JOIN tables_in_reservation tr ON 
										r.id = tr.reservation_id WHERE r.user_id=? GROUP BY r.id");
										
			$stmt->bind_param("i",$id);
			$stmt->bind_result($id,$rid,$uid,$date,$startTime,$numberOfPeople,$tableCount);
			
			$stmt->execute();
			
			$list = array();
			
			while($stmt->fetch())
			{
				$reservation = new Reservations($id,$rid,$uid,$date,$startTime,$numberOfPeople,$tableCount);
				array_push($list,$reservation);
			}
			
			if ($list == array())
			{
				return FALSE;
			}
			else
			{
				return $list;	
			}		
		}
		
		public static function GetAllReservationsMIDRID($mid, $rid,$past = FALSE)
		{
			$mysqli = openDB();
			
			$time = time();
			
			$r = Restaurant::GetRestaurantWithMID($rid, $mid, $active = TRUE);
			
			if (!$r)
			{
				return FALSE;
			}
			
			$t = ceil($r->reservationLength * 60);
			
			if ( $past )
			{
				$op = "<";
				$ord = "DESC";
			}
			else
			{
				$op = ">=";
				$ord = "ASC";
			}
			
			$stmt = $mysqli->prepare("SELECT r.id,r.restaurant_id,r.user_id,r.date,r.start_time,r.number_of_people,
									  COUNT(tr.reservation_id) AS table_count,r.name
									  FROM reservations r LEFT JOIN tables_in_reservation tr ON 
										r.id = tr.reservation_id
									  JOIN restaurants res ON r.restaurant_id = res.id WHERE res.manager_id=? AND res.id=?
									  AND UNIX_TIMESTAMP(TIMESTAMP(date,start_time)) $op $time - ?									  
									  GROUP BY r.id ORDER BY r.date $ord,r.start_time $ord");
										

			$stmt->bind_param("iii",$mid,$rid,$t);
			$stmt->bind_result($id,$rid,$uid,$date,$startTime,$numberOfPeople,$tableCount,$name);
			
			$stmt->execute();
			
			$list = array();
			
			while($stmt->fetch())
			{
				$reservation = new Reservations($id,$rid,$uid,$date,$startTime,$numberOfPeople,$tableCount,$name);
				array_push($list,$reservation);
			}
			
			if ($list == array())
			{
				return FALSE;
			}
			else
			{
				return $list;	
			}		
		}
		
		public static function GetReservationFromReservationID($id)
		{
			$mysqli = openDB();
			
			$stmt = $mysqli->prepare("SELECT r.id,r.restaurant_id,r.user_id,r.date,r.start_time,r.number_of_people,
									  COUNT(tr.reservation_id) AS table_count
									  FROM reservations r LEFT JOIN tables_in_reservation tr ON 
										r.id = tr.reservation_id WHERE r.id=? GROUP BY r.id");
			$stmt->bind_param("i",$id);
			
			$stmt->bind_result($resid,$rid,$uid,$date,$time,$nop,$tableCount);
			
			if ($stmt->execute() && $stmt->store_result() && $stmt->fetch())
			{
				return new Reservations($resid,$rid,$uid,$date,$time,$nop,$tableCount);	
			}
			else
			{
				return FALSE;	
			}
		
		}
		
		public static function GetReservationFromRIDAndUID($rid,$uid)
		{
			$mysqli = openDB();
			
			$stmt = $mysqli->prepare("SELECT r.id,r.restaurant_id,r.user_id,r.date,r.start_time,r.number_of_people,
									  COUNT(tr.reservation_id) AS table_count
									  FROM reservations r LEFT JOIN tables_in_reservation tr ON 
										r.id = tr.reservation_id WHERE r.id=? AND r.user_id=? GROUP BY r.id");
			$stmt->bind_param("ii",$rid,$uid);
			
			$stmt->bind_result($resid,$id,$userid,$date,$time,$nop,$tableCount);
			
			if ($stmt->execute() && $stmt->store_result() && $stmt->fetch())
			{
				return new Reservations($resid,$id,$userid,$date,$time,$nop,$tableCount);	
			}
			else
			{
				return FALSE;	
			}
		
		}
		
		public static function FindAllReservations($uid,&$date,&$time,&$numberOfPeople)
		{
			if (!self::Validate($date,$time,$numberOfPeople))
				return FALSE;
				
			$restaurants = Restaurant::GetAllRestaurants();
			
			$rList = array();
			
			foreach($restaurants as $r)
			{
				if(self::SearchReservation($r,$uid,$date,$time,$numberOfPeople))
				{
					array_push($rList,$r);
				}
			}
			if ($rList == array())
			{
				Errors::Create("resCreate")->SetError("nonefoundError");
				return FALSE;
			}	
			else
			{
				return $rList;	
			}
		}
		
		public function GetTableList()
		{
			$list = array();
			
			if ($this->tableCount > 0)
			{
				$mysqli = openDB();
				
				$stmt = $mysqli->prepare("SELECT t.id,t.name,t.restaurant_id,t.capacity,t.can_combine,t.description,t.reserve_online FROM 
											tables t JOIN tables_in_reservation tr ON t.id = tr.table_id 
													 JOIN reservations r ON tr.reservation_id = r.id WHERE r.id=?");
				$stmt->bind_param("i",$this->id);
				
				$stmt->bind_result($id,$name,$rid,$capacity,$canCombine,$description,$reserveOnline);
				
				$stmt->execute();
				$stmt->store_result();
				
				$i = 0;
				
				while($stmt->fetch())
				{
					$list[$i++] = new Table($id,$name,$rid,$capacity,$canCombine,$description,$reserveOnline);
				}
			}
				
			return $list;				
		}
		
		public static function SearchReservation(Restaurant $r,$uid,$date,$time,$numberOfPeople)
		{	
			if(!self::GetBounds($r,$date,$time))	
			{
				Errors::Create("resCreate")->SetError("boundError");
				return FALSE;	
			}
			
			// Check if user has reservation in that time frame
			date_default_timezone_set('America/New_York');
			$reservationLengthSec = $r->reservationLength * 60;
			$reservationTimeUnix = strtotime($date . " " . $time);	
			
			$mysqli = openDB();
			
			$stmt = $mysqli->prepare("SELECT id FROM reservations WHERE user_id=? AND restaurant_id=? AND
							 (((UNIX_TIMESTAMP(TIMESTAMP(date,start_time)) + ?) >= ?) AND ((? + ?) >=
														UNIX_TIMESTAMP(TIMESTAMP(date,start_time))))");
														
			$stmt->bind_param("iiiiii",$uid,$r->id,$reservationLengthSec,$reservationTimeUnix,$reservationTimeUnix,
										$reservationLengthSec);
			
			if ($stmt->execute() && $stmt->fetch())
			{
				Errors::Create("resCreate")->SetError("resAlready");
				return FALSE;
			}
			
			return self::GetTables($r,$date,$time,$numberOfPeople);
	
		}
		
		public static function DeleteReservationForUser($rid,$uid)
		{
			$mysqli = openDB();
			
			$stmt = $mysqli->prepare("DELETE FROM reservations WHERE id=? AND user_id=?");
			
			$stmt->bind_param("ii",$rid,$uid);
			
			if($stmt->execute())
			{
				return TRUE;
			}
			else
			{
				Errors::Create("ResUserDelete")->SetError("general");
				return FALSE;
			}			
		}
		
		public static function DeleteIDWithManID($rid,$mid)
		{
			$mysqli = openDB();
			
			$stmt = $mysqli->prepare("DELETE r FROM reservations r JOIN restaurants res ON r.restaurant_id = res.id
									  WHERE r.id=? AND res.manager_id=?");
									  			
			$stmt->bind_param("ii",$rid,$mid);
			
			if($stmt->execute())
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}			
		}
		
		public static function GetTables(Restaurant $r,$date,$time,$numberOfPeople)
		{
			date_default_timezone_set('America/New_York');
			$reservationLengthSec = $r->reservationLength * 60;
			$reservationTimeUnix = strtotime($date . " " . $time);	
						
			$query = "SELECT ta.id,ta.name,ta.capacity,ta.can_combine,ta.description,ta.reserve_online,ta.restaurant_id 
						FROM tables ta WHERE ta.id 
							NOT IN (SELECT t.id FROM reservations r 	
								JOIN tables_in_reservation tr ON r.id = tr.reservation_id 
								JOIN tables t ON tr.table_id = t.id 
				 			   WHERE reserve_online=1 
				  				AND (((UNIX_TIMESTAMP(TIMESTAMP(r.date,r.start_time)) + ?) >= ?) AND ((? + ?) >=
									UNIX_TIMESTAMP(TIMESTAMP(r.date,r.start_time))))) 
					AND ta.restaurant_id=?
				 	 ORDER BY ta.capacity DESC";
				 	
			$mysqli = openDB();
			
			$stmt = $mysqli->prepare($query);
			
			$stmt->bind_param("iiiii",$reservationLengthSec,$reservationTimeUnix,$reservationTimeUnix,$reservationLengthSec,$r->id);
			
			$stmt->bind_result($id,$name,$capacity,$canCombine,$description,$reserveOnline,$rid);
			
			$stmt->execute();
			
			$i = 0; $tList = array();

			while($stmt->fetch())
			{
				$tList[$i++] = new Table($id,$name,$rid,$capacity,$canCombine,$description,$reserveOnline);
			}
			
		  
			
			$theNum = $numberOfPeople;
			$maxDifference = 3;
			
			$found = array();
			
			while($numberOfPeople < $theNum + $maxDifference)
			{
				foreach($tList as $l)
				{
					if($l->capacity == $numberOfPeople)
					{
						array_push($found,$l);
						break;
					}
				}	
				
				if ($found == array())
				{
					self::Find_Combinations($tList,$numberOfPeople,$found);
					
					if($found != array())
					{
						break;
					}
					else
					{
						++$numberOfPeople;
					}
				}
				else
				{
					break;
				}
			}
			
			if($found == array())
			{
				
				Errors::Create("resCreate")->SetError("noTables");
				return FALSE;	
			}
			else
			{
				return $found;
			}
		}
		
		public static function GetBounds(Restaurant $r,$date,$time)
		{
			// Check if time is in window
			date_default_timezone_set('America/New_York');
			$reservationTime = floor(strtotime("$date $time") / 60);				// Time Stamp of reservation
			$currTime = floor(time() / 60);											// Current Time Stamp Number of minutes
			$maxNoticeTime = $reservationTime - ceil($r->maxNotice * 60);			// Time Stamp of earliest time for resrvation
			$minNoticeTime = $reservationTime - ceil($r->minNotice * 60);			// Time Stamp of lastest time for reservation
			$dayOfWeek = date("N",strtotime($date)) - 1;							// Index of day of week 
			$endDate = date("Y-m-d",strtotime(ceil($r->reservationLength)." minutes",strtotime($date)));
			$endTime = date("G:i:s",strtotime(ceil($r->reservationLength)." minutes",strtotime($time)));
	
			
			if ($currTime < $maxNoticeTime || $currTime > $minNoticeTime || $reservationTime < $currTime)
			{
				// Reservation time is out of bounds
				return FALSE;	
			}
			
			// Check if time is in bounds of hours
			$hour = $r->GetMainScheduleList();
			
			$day[0] = $hour[($dayOfWeek + 5) % 7];
			$day[1] = $hour[($dayOfWeek + 6) % 7];
			$day[2] = $hour[$dayOfWeek];
			$day[3] = $hour[($dayOfWeek + 1) % 7];
			$day[4] = $hour[($dayOfWeek + 2) % 7];
			
			$j = 0;
			
			// Assemble special schedule into array
			for($i = -2; $i < 3; ++$i,$temp = FALSE,++$j)
			{
				$newDate = date('Y-m-d', strtotime("$i day", strtotime($date)));
				
				if ($temp = $r->GetSpecialSchedule($newDate))
				{
					$day[$j] = $temp;
				}
				else
				{
					$day[$j]->day = $newDate;	
				}
			}

			$nList = array();
			
			// Build list
			$i = 0;
			$j = 0;
			foreach ($day as $d)
			{
				if (!$d->isClosed)
				{
					if ($d->open != NULL)
					{
						$nList[$i++] = new Node($d->open,$d->day,'o');
					}
					
					if ($d->close != NULL && $d->open != NULL)
					{
						if ($d->open > $d->close)
							$nList[$i++] = new Node($d->close,date('Y-m-d', strtotime("1 day", strtotime($d->day))),'c');
						else
							$nList[$i++] = new Node($d->close,$d->day,'c');
					}
					elseif($d->close != NULL && $d->open == NULL)
					{
							$nList[$i++] = new Node($d->close,$d->day,'c');	
					}
				}
				else
				{
					$nList[$i++] = new Node($d->close,$d->day,'-',TRUE);		
				}
				++$j;
			}
			
			$nCount = count($nList);
			$location = NULL;
			
			for($i = 0; $i < $nCount; ++$i)
			{
				
				$dateTime = strtotime("{$nList[$i]->date} {$nList[$i]->time}");
				$endDateTime = strtotime("$endDate $endTime");
				$startDateTime = strtotime("$date $time");
				
				if ( ($nList[$i]->type =='o' && $dateTime <= $startDateTime) ||
					 ($nList[$i]->type == 'c' && $dateTime >= $endDateTime) )
				{
					if (array_key_exists($i+1,$nList) && ($nList[$i]->type =='o'))
					{	
						$dateTime = strtotime($nList[$i+1]->date . " " . $nList[$i+1]->time);
						
						if ($nList[$i+1]->type =='c' && $dateTime >= $endDateTime)
						{
							$location = $i;
							break;
						}
						else
						{
							$location = NULL;	
						}
					}
					elseif (array_key_exists($i-1,$nList) && ($nList[$i]->type =='c'))
					{
						$dateTime = strtotime("{$nList[$i-1]->date} {$nList[$i-1]->time}");
						
						if ($nList[$i-1]->type =='o' && $dateTime <= $startDateTime)
						{
							$location = $i;
							break;
						}
						else
						{
							$location = NULL;	
						}
					}
					else					
					{
						$location  = $i;
					}
				} 	
			}
			
			if ($location === NULL && $nCount != 0)
			{
				// Out of bounds in time
				return FALSE;
			}	
			
			return TRUE;		
		}
		
		private static function Find_Combinations($numbers,$target,&$item = array(),$partial = array())
		{
			if (count($item) > 0)
				return;
				
			$s = 0;
			$f = 0;
			if ($partial != array())
			{
				foreach($partial as $p)
				{
					if ($p->canCombine == 1)
					{
						if ($f != 0)
						{
							switch($p->capacity)
							{
								case 0: case 1: case 2: case 3:
									$s += $p->capacity;
									break;
								default:
									$s = $s + $p->capacity - 2;
									break;
							}
							++$f;
						}
						else
						{
							$s += $p->capacity;
							++$f;
						}
					
					}
				}
			}
			
			if ($s == $target)
			{
				foreach($partial as $p)
				{
					if ($p->canCombine == 1)
						array_push($item,$p);
				}
			}
			if ($s >= $target)
				return;
				
			$c = count($numbers);
			
			for($i = 0; $i < $c; ++$i)
			{
				$n = $numbers[$i];
				$remaining = array_slice($numbers,$i+1); 
				array_push($partial,$n);
				self::Find_Combinations($remaining,$target,$item,$partial);
			}	
		}
		
		private static function Validate(&$date,&$time,&$people)
		{
			date_default_timezone_set('America/New_York');
			$status = TRUE;
			$error = Errors::Create("resCreate");
			
			$date = trim($date);
			$time = trim($time);
			$people = $people;
			
			if ($date == "")
			{
				$status = FALSE;
				$error->SetError("dateSet");
			}
			
			if ($time == "")
			{
				$status = FALSE;
				$error->SetError("timeSet");
			}
			
			if ($people == "")
			{
				$status = FALSE;
				$error->SetError("peopleSet");
			}
			
			if (time() > strtotime("$date $time"))
			{
				$status = FALSE;
				$error->SetError("futureCheck");
			}
			
			return $status;		
		}
	}
?>