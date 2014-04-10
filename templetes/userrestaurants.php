<?php
	$router = Router::Load();
	$page = $router->params[0];
	$info = $router->params[1];
	
	if ($info == FALSE)
	{
		// Get restaurant list
		$restaurants = Restaurant::GetAllRestaurants();
	}
	
	if ($info == FALSE)
		$title = "Upscale™ - Restaurants";
	else
		$title = "Upscale - " . $info->name;
		
	include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainHeader.php");
?>  
        <div id='mainContentsLogin'>
    		<?php if (!$info) { ?>
       		<div class='infoBox'>
            	<h1>Restaurants</h1>
                <?php 
					if (!$restaurants)
					{
						echo "<h4 class='center'>There are no restaurants to display</h4>";
						
					}
					else
					{
						echo "<table class='tableList'>\n";
						echo "\t<tr><th>#</th><th>name</th><th>address</th><th>table count</th><th>actions</th></tr>\n";
						
						$i = 1;
						
						foreach($restaurants as $r)
						{
							if (($i & 1) == 1) 
								$class = "class='rowHighlight'";
							else
								$class = "";
								
							echo "\t<tr $class><td>$i</td><td>$r->name</td><td>$r->address</td><td>$r->tableCount</td><td style='width:305px;'><a href='".ROOT_URL."/restaurants/$r->id' title='view profile' class='mainButtonTable'>view profile</a><a href='".ROOT_URL."/reservations/$r->id' title='make reservation' class='mainButtonTable'>make reservation</a></td></tr>";
							++$i;
						}	
						
						echo "</table>";					
					}
		
				?>
            </div>
           	<?php } else { ?>
			<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA_K5EZSK6N2XxNbuaM8SJ8CEwAXFLCfkk&sensor=true"></script>
            <script type="text/javascript">
              marker = null;
              function initialize() {
                geocoder = new google.maps.Geocoder();
                var mapOptions = {            
                  center: new google.maps.LatLng(<?php echo "$info->latitude,$info->longitude";?>),
                  zoom: 15
                };
    
                map = new google.maps.Map(document.getElementById("map-canvas"),
                    mapOptions);
                marker = new google.maps.Marker({
                      position: new google.maps.LatLng(<?php echo "$info->latitude,$info->longitude";?>),
                      map: map
                  });
              }
              google.maps.event.addDomListener(window, 'load', initialize);
              
              function GetLocation(address)
              {
                if (address.trim() != "")
                {
                    var address = address;
                    geocoder.geocode( { 'address': address}, function(results, status) {
                         if (status == google.maps.GeocoderStatus.OK) {
                            map.setCenter(results[0].geometry.location);
                            map.setZoom(15);
                            if (marker != null) marker.setMap(null);
                            marker = new google.maps.Marker({
                                map: map,
                                position: results[0].geometry.location,
                            });
                            $('#long').val(results[0].geometry.location.lng());
                            $('#lat').val(results[0].geometry.location.lat());
                            
                         } else {
                            alert('Could not find that address');
                         }
                    });	
                }
            }
            
            </script>
            <div class='infoBox'>
            	<h1><?php echo $info->name;?></h1>
                <div class='leftRest center'>
                	<a href='<?php echo ROOT_URL . "/reservations/$info->id";?>' title='make reservation' class='mainButton'>make reservation</a>
                    <div class='itemBox'>
                    	<h1>info</h1>
                        <table class='restdetails'>
                            <tr><td class='firstColDetails'>Address:</td><td><?php echo $info->address;?></td></tr>
                            <tr><td class='firstColDetails'>Phone Number:</td><td><?php echo $info->phoneNumber;?></td></tr>
                         </table>
                     </div>
                     <div class='itemBox'>
                     	<h1>schedule</h1>
                        <table class='restdetails'>
                        <?php
							$days = array("Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday");
						
							$schedule = $info->GetMainScheduleList();

							$i = 0;
							
							foreach($schedule as $s)
							{
								echo "<tr><td class='firstColDetails'>".$days[$i++]."</td><td>";
								
								if ($s->isClosed)
								{
									echo "Closed";	
								}
								elseif($s->open != NULL && $s->close != NULL)
								{
									echo date("g:i A",strtotime($s->open))." - ".date("g:i A",strtotime($s->close));
								}
								elseif($s->open == NULL && $s->close == NULL)
								{
									echo "24 hours";
								}
								elseif($s->open == NULL && $s->close != NULL)
								{
									echo "12:00 AM - ".date("g:i A",strtotime($s->close));
								}
								else
								{
									echo date("g:i A",strtotime($s->open))." - 12:01 AM";
								}
								
								echo "</td></tr>\n";
							}
						?>
                        </table>
                     </div>
                </div>
                <div id='map-canvas' style='width:360px;height:300px; float:left; margin:18px 0px 0px 25px'></div>
             </div>
            <?php } ?>
        </div>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainFooter.php"); ?>