<?php
	class Reservations
	{
		public $id;
		public $restaurantID;
		public $userID;
		public $date;
		public $startTime;
		public $numberOfPeople;		
		
		public function __constructor($id = NULL,$restaurantID = NULL,$userID = NULL,$date = NULL,$startTime = NULL,
									  $numberOfPeople = NULL)
		{
			$this->id = $id;
			$this->restaurantID = $restaurantID;
			$this->userID = $userID;
			$this->date = $date;
			$this->startTime = $startTime;
			$this->numberOfPeople = $numberOfPeople;			
		}
		
		public static function AddReservation($rid,$userID,$date,$startTime,$numberOfPeople)
		{
				
			
		}
		
		public static function SearchReservation(Restaurant $r,$date,$time,$numberOfPeople)
		{		
			// Check if time is in window
			date_default_timezone_set('America/New_York');
			$reservationTime = floor(strtotime("$date $time") / 60);				// Time Stamp of reservation
			$currTime = floor(time() / 60);											// Current Time Stamp Number of minutes
			$maxNoticeTime = $reservationTime - ceil($r->maxNotice * 60);			// Time Stamp of earliest time for resrvation
			$minNoticeTime = $reservationTime - ceil($r->minNotice * 60);			// Time Stamp of lastest time for reservation
			$dayOfWeek = date("N",strtotime($date)) - 1;							// Index of day of week 
			
			if ($currTime < $maxNoticeTime || $currTime > $minNoticeTime || $reservationTime < $currTime)
			{
				// Reservation time is out of bounds
				return FALSE;	
			}
			
			// Check if time is in bounds of hours
			$hour = false;
			
			$SPList = $r->GetSpecialScheduleList();
			
			$i = 0;
			foreach($SPList as $SP)
			{
				++$i;
				
				if($SP->day == $date)
				{
					$hour = $SP;
					break;
				}
			}	
			
			if($hour)
			{
				if (!$hour->isClosed)
				{
					// Restaurant is open
					
					if ($hour->open != NULL)
					{
						// Restaurant is not open from the previous night - check opening time against reservation request
						if ($time < $hour->open)
						{
							// Restaurant does not open in time
							return FALSE;	
						}
					}
					
					if ($hour->close == NULL)
					{
						// Restaurant does not have closing hours
						$nextDay = $SPList[$i];	
					}
					else
					{
						// Restaurant has closing hours
					}
				}
				else
				{
					// Restaurant is closed
					return FALSE;	
				}
			}
			else
			{
				// Choose from day of the week	
				$ml = $r->GetMainScheduleList();
				$d = $ml[$dayOfWeek];
				
				// Check if closed
				if(!$d->isClosed)
				{
					if ($d->open != NULL)
					{
						// Restaurant is not open from the previous night - check opening time against reservation request
						if ($time < $d->open)
						{
							// Restaurant does not open in time
							return FALSE;	
						}
					}
					
					if ($d->close == NULL)
					{
						// Restaurant does not have closing hours	
					}
					else
					{
						// Restaurant has closing hours
					}
										
				}
				else
				{
					// Restaurant is closed
					return FALSE;	
				}
						
			}
			
		
			// TODO check if time window spans more than one day
			
			// Return tables available in that time 
						
			$query = "SELECT t.id,t.name,t.capacity,t.can_combine,t.description,t.reserve_online FROM reservations r 	
					JOIN tables_in_reservation tr ON r.id = tr.reservation_id 
					JOIN tables t ON tr.table_id = t.id 
				  WHERE reserve_online=1 
				  	AND (((UNIX_TIMESTAMP(TIMESTAMP(r.date,r.start_time)) + ?) < ?) OR ((? + ?) <
						UNIX_TIMESTAMP(TIMESTAMP(r.date,r.start_time))))";
				 	
				  // ? = length in seconds, reservation_time in unix , reservation_time , length in seconds , 
			
				  
		}
	}
?>