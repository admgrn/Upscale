<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title><?php echo $title;?></title>
<link href="<?php echo ROOT_URL;?>/css/mainStyles.css" rel="stylesheet" type="text/css" />
<script src='<?php echo ROOT_URL;?>/js/jquery-2.1.0.js'></script>
</head>

<body>
    <div id='mainWrapper'>
        <div id='headerWrapper'>
        	<div id='headerMainContents'>
            	<div id='headerMainLeft'>
            		<img src='<?php echo ROOT_URL;?>/img/logo.png' alt='UPSCALE™' />
                </div>
                <div id='headerMainRight'>
                	<div id='headerMainRightTop'>
                    	<?php echo "<a href='".ROOT_URL."' title='home'>{$_SESSION['name']}</a>";?> | <a href='<?php echo ROOT_URL;?>/account' title='edit account'>edit account</a> | <a href='javascript:void(0)' title='logout' id='logout'>logout</a>
                        <form action='<?php echo ROOT_URL?>' method='post' id='logoutForm'>
                        	<input type='hidden' name='logout' value='true' />
                        </form>
                        <script type='text/javascript'>
							$('#logout').click(function() {
								$('#logoutForm').submit()
							});
						</script>
                    </div>
                    <div id='headerMainRightBottom'>
                    	<a href='<?php echo ROOT_URL;?>' class='mainButtonWhite' title='home'>home</a><?php 
							if ($page == "user")
							{
								
								echo "<a href='". ROOT_URL . "/find' class='mainButtonWhite' title='find reservations'>find reservations</a>";
								echo "<a href='". ROOT_URL . "/myreservations' class='mainButtonWhite' title='my reservations'>my reservations</a>";
								echo "<a href='". ROOT_URL . "/map' class='mainButtonWhite' title='restaurant map'>restaurant map</a>";
								echo "<a href='". ROOT_URL . "/restaurants' class='mainButtonWhite' title='restaurants'>restaurants</a>";
								echo "<a href='". ROOT_URL . "/search' class='mainButtonWhite' title='search'>search</a>";
							}
							else
							{
								echo "<a href='". ROOT_URL . "/managerestaurants' class='mainButtonWhite' title='manage restaurants'>manage restaurants</a>";
								echo "<a href='" . ROOT_URL . "/reservations' class='mainButtonWhite' title='manage reservations'>manage reservations</a>";
								echo "<a href='" . ROOT_URL . "/compare' class='mainButtonWhite' title='compare'>compare</a>";
							}	
						?>
                    </div>
                </div>
            </div>
        </div>     