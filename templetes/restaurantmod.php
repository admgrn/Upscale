<?php
	$router = Router::Load();
	$page = $router->params;
	$session = new Session;
	$errors = Errors::Create("rest");
	
	if(isset($_POST['newOrUpdate']) && $_POST['newOrUpdate'] == "new")
	{
		$status = Restaurant::CreateRestaurant($_SESSION['id'],$_POST['name'],$_POST['address'],$_POST['phoneNumber'],$_POST['maxTime'],
									  $_POST['minTime'],$_POST['length'],$_POST['long'],$_POST['lat']);
		if ($status) 
		{
			header("Location: " . ROOT_URL . "/managerestaurants");
			die();
		}
	}
	elseif(isset($_POST['newOrUpdate']))
	{
		$status = Restaurant::UpdateRestaurant($_POST['newOrUpdate'],$_SESSION['id'],$_POST['name'],$_POST['address'],
											   $_POST['phoneNumber'],$_POST['maxTime'],$_POST['minTime'],$_POST['length'],
											   $_POST['long'],$_POST['lat']);
											   
		$page = Restaurant::GetRestaurant($_SESSION['id'],$page->id);
	}
	
	if ($page == "new" || (isset($_POST['newOrUpdate']) && !$status))
	{
		$form = new FormValues;
	}
	else
	{
		$values['name'] = $page->name;
		$values['address'] = $page->address;
		$values['phoneNumber'] = $page->phoneNumber;
		$values['maxTime'] = $page->maxNotice;
		$values['minTime'] = $page->minNotice;
		$values['length'] = $page->reservationLength;
		$values['long'] = $page->longitude;
		$values['lat'] = $page->latitude;
		$form = new FormValues($values);	
	}
	
	
	$days = array("monday","tuesday","wednesday","thursday","friday","saturday","sunday");
	
	
	if ($page == "new")
		$title = "Upscale™ - New Restaurant";
	else
		$title = "Upscale™ - Edit Restaurant";
	
	include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainHeader.php");
	
?>  
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA_K5EZSK6N2XxNbuaM8SJ8CEwAXFLCfkk&sensor=true"></script>
        <script type="text/javascript">
          function initialize() {
			geocoder = new google.maps.Geocoder();
            var mapOptions = {
			<?php if ($page == "new" && $_POST['lat'] == 0 && $_POST['long'] == 0) { ?>
              center: new google.maps.LatLng(30.4328071,-84.2879266),
              zoom: 11
		     <?php } elseif($_POST['lat'] != 0 && $_POST['long'] != 0) {?>
			  center: new google.maps.LatLng(<?php echo "{$_POST['lat']},{$_POST['long']}";?>),
			  zoom: 15			 
			 <?php } else {?>
			  center: new google.maps.LatLng(<?php echo "$page->latitude,$page->longitude";?>),
			  zoom: 15
			 <?php } ?>
            };

            map = new google.maps.Map(document.getElementById("map-canvas"),
                mapOptions);
			<?php if ($page != "new" && !isset($_POST['lat']) && !isset($_POST['long'])) { ?>
			marker = new google.maps.Marker({
				  position: new google.maps.LatLng(<?php echo "$page->latitude,$page->longitude";?>),
				  map: map
			  });
			<?php } elseif($_POST['lat'] != 0 && $_POST['long'] != 0) {?>	
			 marker = new google.maps.Marker({
				  position: new google.maps.LatLng(<?php echo "{$_POST['lat']},{$_POST['long']}";?>),
				  map: map
			  });
			<?php } ?>	
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
						marker.setMap(null);
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
        <div id='mainContentsLogin'>
        	<div class='layerEdit'>
                <div id='restBox' class='infoBox'>
                    <?php 
                        if ($page == "new")
                            echo "<h3>New Restaurant</h3>";
                        else
                            echo "<h3>Edit Restaurant</h3>";
                            
                            if ($status) 
                            {
                                echo "<ul class='loginErrorListBlack'>";
                                echo "\t<li>Information Successfully Updated</li>";
                                echo "</ul>";
                            }
                            
                            if ($errors->GetState()) echo "<ul class='loginErrorListBlack'>";
                            $errors->GetError("general","\t<li>An Error Occured, please try again</li>");
                            $errors->GetError("addressDuplicate","\t<li>The address is linked to another restaurant</li>");
                            $errors->GetError("nameNull","\t<li>Name cannot be empty</li>");
                            $errors->GetError("addressNull","\t<li>Address cannot be empty</li>");
                            $errors->GetError("phoneNumberFormat","\t<li>Invalid Phone Format</li>");
                            $errors->GetError("phoneNumberNull","\t<li>Phone Number cannot be empty</li>");
                            $errors->GetError("maxResNull","\t<li>Maximum reservation time cannot be empty</li>");
                            $errors->GetError("maxResFormat","\t<li>Maximum reservation invalid format</li>");
                            $errors->GetError("minResFormat","\t<li>Minimum reservation invalid format</li>");
                            $errors->GetError("minResNull","\t<li>Minimum reservation time cannot be empty</li>");
                            $errors->GetError("resTimeNull","\t<li>Reservation time cannot be empty</li>");
                            $errors->GetError("resTimeFormat","\t<li>Reservation length invalid format</li>");
                            if ($errors->GetState()) echo "</ul>";
                    ?>
                    <form action='<?php echo THIS_PAGE;?>' method='post' class='center'>
                        <input type='text' name='name' placeholder='name' class='inputField<?php $errors->GetError("nameNull"," borderError");?>' <?php echo $form->GetValue('name');?> />
                        <input type='text' name='address' placeholder='address' class='inputField<?php $errors->GetError(array("addressNull","addressDuplicate")," borderError");?>' onblur='GetLocation($(this).val())' <?php echo $form->GetValue('address');?> />
                        <input type='text' name='phoneNumber' placeholder='phone number' class='inputField<?php $errors->GetError(array("phoneNumberNull","phoneNumberFormat")," borderError");?>' <?php echo $form->GetValue('phoneNumber');?> />
                        <input type='text' name='maxTime' placeholder='max reservation notice (in hours)' class='inputField<?php $errors->GetError(array("maxResNull","maxResFormat")," borderError");?>' <?php echo $form->GetValue('maxTime');?> />
                        <input type='text' name='minTime' placeholder='min reservation notice (in hours)' class='inputField<?php $errors->GetError(array("minResNull","minResFormat")," borderError");?>' <?php echo $form->GetValue('minTime');?>/>
                        <input type='text' name='length' placeholder='reservation length (in minutes)' class='inputField<?php $errors->GetError(array("resTimeNull","resTimeFormat")," borderError");?>' <?php echo $form->GetValue('length');?>/>
                        <input type='hidden' name='long' id='long' <?php echo $form->GetValue('long');?> />
                        <input type='hidden' name='lat' id='lat' <?php echo $form->GetValue('lat');?> />
                        <?php
                            if ($page == "new")
                                echo "<input type='hidden' name='newOrUpdate' value='new' />";
                            else
                                echo "<input type='hidden' name='newOrUpdate' value='$page->id' />";
                        ?> 
						<a href='<?php echo ROOT_URL;?>/managerestaurants' title='< back' style='display:inline-block;' class='mainButton'>< back</a><input type='submit' value='save' class='mainButton' />            
                </div>
                <div id='map-canvas' class='restEditMap'></div>
            </div>
            <div class='layerEdit'>
            	<div class='infoBox mainScheduleBox'>
                	<h3>Schedule</h3>
                    <table style='margin:0px auto'>
                     <?php foreach($days as $index => $day) { ?>
                        <tr>
                            <td><?php echo $day;?></td>
                            <td><input type='time' placeholder='open' name='open<?php echo $index;?>' /></td>
                            <td><input type='time' placeholder='close' name='close<?php echo $index;?>' /></td>
                            <td><label><input type='checkbox' name='allday<?php echo $index;?>' />open all day</label></td>
                            <td><label><input type='checkbox' name='closed<?php echo $index;?>' />closed</label></td>
                        </tr>
                     <?php } ?>
                     </table>
                     <div class='center' style='margin:40px 0px 0px 0px'><a href='<?php echo ROOT_URL;?>/managerestaurants' title='< back' style='display:inline-block;' class='mainButton'>< back</a><input type='submit' value='save' class='mainButton' /></div>
                 </div>
             </div>
          </form>
        </div>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainFooter.php"); ?>