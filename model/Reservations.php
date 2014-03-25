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
			echo "<pre>";
			print_r($day);
			echo "</pre>";

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
						if ($d->close < $day[($j + 1) % 7]->open)	
							$nList[$i++] = new Node($d->close,date('Y-m-d', strtotime("1 day", strtotime($d->day))),'c');
						else
							$nList[$i++] = new Node($d->close,$d->day,'c');	
					}
				}
				else
				{
					$nList[$i++] = new Node($d->close,$d->day,'-',TRUE);		
				}
				++$j;
			}
			
			echo "<pre>";
			print_r($nList);
			echo "</pre>";
			
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
				echo "out of bounds";
				return FALSE;
			}
				
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