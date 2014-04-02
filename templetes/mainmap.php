<?php
	$router = Router::Load();
	$page = $router->params;
	$restaurants = Restaurant::GetAllRestaurants();
	$title = "Upscale™ - Restaurant Map";
	include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainHeader.php");
?>     
		<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
        <script>
            var map;
			var infoWindow = false;
            function initialize() {
              var mapOptions = {
                zoom: 11,
                center: new google.maps.LatLng(<?php echo TALLY_COORD;?>)
              };
              map = new google.maps.Map(document.getElementById('map-canvas'),
                  mapOptions);
				  
			  var locations = [<?php 
			  					if ($restaurants)
								{
									foreach($restaurants as $r)
									{
										echo "[\"$r->id\",\"".addslashes($r->name)."\",\"".addslashes($r->name)."\",'$r->phoneNumber',$r->longitude,$r->latitude],\n";									
									}
								}
							  ?>];
				
				for (var i = 0; i < locations.length; i++) {
					var l = locations[i];
					
					var contents = "<div style='font-weight:bold'>" + l[1] + "</div><div>" + l[2] + "</div><div>"
									+ l[3] + "</div><div><a href='<?php echo ROOT_URL."/restaurants/";?>" + l[0] + "' title='"
									+ l[1] + "'>go to restaurant</a>";
					
					var myLatLng = new google.maps.LatLng(l[5], l[4]);
					
					var marker = CreateMarker(contents,myLatLng,l);
	
				}
            }
			
			function CreateMarker(contents,latLng,l)
			{
				var marker = new google.maps.Marker({
					position: latLng,
					map: map,
					title: l[2]
				});
				
				google.maps.event.addListener(marker, 'click', function(key) {
					if (infoWindow)
						infoWindow.close();
					
					infoWindow = new google.maps.InfoWindow({
						content: contents
					});
					
					infoWindow.open(map, marker);
					infoWindow.setPosition({position: latLng});
				});
				
				return marker;
			}
            
            google.maps.event.addDomListener(window, 'load', initialize);
        </script>
        <div id='mainContentsLogin'>
        	<div class='infoBox'>
            	<h3>Map</h3>
       			<div id="map-canvas" style='width:800px; height:600px;margin:0px auto'></div>
            </div>
        </div>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainFooter.php"); ?>