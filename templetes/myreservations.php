<?php
	$router = Router::Load();
	$page = $router->params;
	$title = "Upscale™ - My Reservations";
	include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainHeader.php");
?>
	<link href="<?php echo ROOT_URL;?>/css/fullcalendar/fullcalendar.css" rel="stylesheet">
	<link href="<?php echo ROOT_URL;?>/css/fullcalendar/fullcalendar.print.css" rel="stylesheet" media="print">
	<script src="<?php echo ROOT_URL;?>/js/lib/jquery.min.js"></script>
    <script src="<?php echo ROOT_URL;?>/js/lib/jquery-ui.custom.min.js"></script>
    <script src="<?php echo ROOT_URL;?>/js/lib/fullcalendar.min.js"></script>
    <script>	
		$(document).ready(function() {
			$('#calendar').fullCalendar({
				header: {
					left: 'prev,next today',
					center: 'title',
					right: 'month,agendaWeek,agendaDay'
				},
				editable: false,
				clickable: true,
				eventClick: function(calEvent) {
					if (calEvent.url) {
						window.open(calEvent.url);
						return false;
					}
				},
				events: [
					<?php 
						$res = Reservations::GetAllUserReservations($_SESSION['id']);
						
						if ($res)
						{
							$i = 0;
							foreach($res as $r)
							{
								if ($i++ != 0)
									echo ",\n";
								$rest = Restaurant::GetRestaurant($r->restaurantID);
								
								$dateStart = strtotime("$r->date $r->startTime");
								$dateEnd = strtotime("$r->date $r->startTime") + (ceil($rest->reservationLength) * 60);
								
								$start = date("Y,",$dateStart) . (date("m",$dateStart) - 1) . date(",d,G,i",$dateStart);
								$end = date("Y,",$dateEnd) . (date("m",$dateEnd) - 1) . date(",d,G,i",$dateEnd);
								echo "{
									   title: '$rest->name', 
								   	   start: new Date($start), 
									   end: new Date($end),
									   allDay: false,
									   url: '".ROOT_URL."/reservations/edit/$r->id'
									  }\n";		
							}
						}
				    ?>
				]
			});
			
		});
	

    </script>
        <div id='mainContentsLogin'>
        	<div id='calendar'>
            </div>
       		
        </div>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainFooter.php"); ?>