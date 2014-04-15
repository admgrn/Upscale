<?php
  $router = Router::Load();
  $page = $router->params;
  $title = "Upscale™ -"; if ($page == "manager") $title .= " Manager"; $title .= " Home";
  include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainHeader.php");
?>
        <div id='mainContentsLogin'>
       		<div class='infoBox about'>
            	<h1>About UPSCALE™</h1>
            	<p>This is some information about UPSCALE™. It is some very informational information that will explain a lot about the website. It will be very interesting and informative.</p>
                <p> This is the second paragraph and it will also be very informative.</p>
            	</div>
        </div>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/templetes/assets/mainFooter.php"); ?>
