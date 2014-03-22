<?php
	$router = Router::Load();
	$page = $router->params;
	$session = new Session;
	$errors = Errors::Create("rest");
	$errorsSP = Errors::Create("special");
	
	if(isset($_POST['newOrUpdate']) && $_POST['newOrUpdate'] == "new")
	{
		$status = Restaurant::CreateRestaurant($_SESSION['id'],$_POST['name'],$_POST['address'],$_POST['phoneNumber'],$_POST['maxTime'],
									  $_POST['minTime'],$_POST['length'],$_POST['long'],$_POST['lat']);
		
		if ($status) 
		{
			$status->SetMainSchedule($_POST,"open","close","closed","isopen","isclosed");
			
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
		$page->SetMainSchedule($_POST,"open","close","closed","isopen","isclosed");
	}
	else
	{
		$status = FALSE;
	}
	
	if(isset($_POST['dateSP']))
	{
		$statusSP = $page->AddSpecialSchedule($_POST['dateSP'],$_POST['openSP'],$_POST['closeSP'],@$_POST['closedSP'],@$_POST['isopenSP'],
								  @$_POST['isclosedSP']);
	}
	
	if (isset($_POST['deleteSP']))
	{
		$page->DeleteSpecialSchedule($_POST['deleteSP']);	
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
	
		$schedule = $page->GetMainScheduleList($_SESSION['id']);
	
		for($i=0;$i < 7;++$i)
		{
			if ($schedule[$i]->isClosed == 1) 
			{
				$values["closed$i"] = 1;
			}
			else
			{
				if ($schedule[$i]->open == NULL)
					$values["isopen$i"] = 1;
				else
					$values["open$i"] = $schedule[$i]->open;
					
				if ($schedule[$i]->close == NULL)
					$values["isclosed$i"] = 1;
				else
					$values["close$i"] = $schedule[$i]->close;
			}
		
		}
		
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
		  marker = null;
          function initialize() {
			geocoder = new google.maps.Geocoder();
            var mapOptions = {
			<?php if ($page == "new" && ((!isset($_POST['lat']) && !isset($_POST['long'])) || ($_POST['lat'] == 0 && $_POST['long'] == 0 ))) { ?>
              center: new google.maps.LatLng(30.4328071,-84.2879266),
              zoom: 11
		     <?php } elseif(isset($_POST['lat']) && isset($_POST['long'])) {?>
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
			<?php } elseif(isset($_POST['lat']) && isset($_POST['long']) && $_POST['lat'] != 0 && $_POST['long'] != 0) {?>	
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
                            <td><input type='time' placeholder='open' name='open<?php echo $index;?>' <?php echo $form->GetValue("open$index");?> /></td>
                            <td><input type='time' placeholder='close' name='close<?php echo $index;?>' <?php echo $form->GetValue("close$index");?> /></td>
                            <td><label><input type='checkbox' name='closed<?php echo $index;?>' value='1' <?php echo $form->SendItem("closed$index","checked='checked'");?> />closed</label></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><label>no open time <input type='checkbox' name='isopen<?php echo $index;?>' value='1' <?php echo $form->SendItem("isopen$index","checked='checked'");?> /></label></td>
                            <td><label>no close time <input type='checkbox' name='isclosed<?php echo $index;?>' value='1' <?php echo $form->SendItem("isclosed$index","checked='checked'");?>/></label></td>
                        </tr>
                     <?php } ?>
                     </table>
                     <div class='center' style='margin:40px 0px 0px 0px'><a href='<?php echo ROOT_URL;?>/managerestaurants' title='< back' style='display:inline-block;' class='mainButton'>< back</a><input type='submit' value='save' class='mainButton' /></div>
                 </div>
             </div>
          </form>
          <?php if($page != "new") {?>
          <div class='layerEdit' id='special'>
              <div class='infoBox mainScheduleBox'>
                <h3>Special Schedule</h3>
                
               		<?php 
							$list = $page->GetSpecialScheduleList($_SESSION['id']);
							
							if (@$statusSP) 
                            {
                                echo "<ul class='loginErrorListBlack'>";
                                echo "\t<li>Information Successfully Updated</li>";
                                echo "</ul>";
                            }
							
							if ($errorsSP->GetState()) echo "<ul class='loginErrorListBlack'>";
                            $errorsSP->GetError("general","\t<li>An Error Occured, please try again</li>");
                            $errorsSP->GetError("duplicate","\t<li>An item exists for that date. Please delete it and try again</li>");
                            if ($errorsSP->GetState()) echo "</ul>";
					?>
                    <script type='text/javascript'>
						function CheckDate(date)
						{
							if (date == "")
							{
								alert("Please Enter a valid date");
								return false;
							}
							
							var dt = new Date();
								
							var date = date.split("-");
							
							date = date[0] + date[1] + date[2];
							
							dt = dt.getFullYear() 
								 + ('0' + (dt.getMonth() + 1)).slice(-2)
								 + ('0' + dt.getDay()).slice(-2);
							
							if (parseInt(date) < parseInt(dt))
							{
								alert("Please enter a date that is in the future");
								return false;
							}
							else
							{
								return true;
							}
						}
					</script>
                    <table style='margin: 0px auto;' id='specialEditTable'>
                    	<tr style='text-align:left;'>
                        	<th>#</th>
                            <th>date</th>
                            <th>open</th>
                            <th>close</th>
                            <th>is open</th>
                            <th>actions</th>
                        </tr>
                       <?php
					   		$i=1;
					   		foreach ($list as $h)
							{
								if (($i & 1) == 1) 
									$class = "class='rowHighlight'";
								else
									$class = "";
									
								$date = date("l, F j, Y",strtotime($h->day));
								
								if ($h->isClosed) 
								{
									$open = "Closed";
									$close = "Closed";
									$closed = "Closed";
								}
								else
								{
									if ($h->open == NULL)
										$open = "No Open";
									else
										$open = date("g:i A",strtotime($h->open));
									
									if ($h->close == NULL)
										$close = "No Close";
									else
										$close = date("g:i A",strtotime($h->close));;
									
									$closed = "Open";
								}
								echo "\t<tr $class><td>$i</td><td>$date</td><td>$open</td><td>$close</td><td>$closed</td><td><form action='".ROOT_URL."/editrestaurant/$page->id#special' method='post'><input type='submit' value='delete' onclick=\"if(confirm('Are you sure you want to delete this table? This cannot be undone.')) return true; else return false;\" class='mainButtonTable' /><input type='hidden' name='deleteSP' value='$h->day' /></form></td></tr>\n";
								++$i;
							}
					   ?>
                       <form method='post' action='<?php echo ROOT_URL."/editrestaurant/$page->id";?>#special'>
                       <tr>
                       		<td><?php echo $i;?></td>
                            <td><input type='date' name='dateSP' id='dateSP' /></td>
                            <td><input type='time' placeholder='open' name='openSP' /></td>
                            <td><input type='time' placeholder='close' name='closeSP' /></td>
                            <td><label><input type='checkbox' name='closedSP' value='1' />closed</label></td>
                            <td><input type='submit' class='mainButtonTable' value='add' onclick="return CheckDate($('#dateSP').val());" /></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td><label>no open time <input type='checkbox' name='isopenSP' value='1' /></label></td>
                            <td><label>no close time <input type='checkbox' name='isclosedSP' value='1' /></label></td>
                        </tr>
                      </table>
                	</form>
                </div>        
              </div>      
       	  <?php }?>
        </div>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainFooter.php"); ?>