<?php
// Parses the url and returns the file that must be included
class Router
{
	static $obj;
	public $params;
	private $url;
	private $use;
	private $notFound = FALSE;

	public function __construct($url)
	{
		$this->url = $url;
		self::$obj = $this;
	}

	// Singleton Access
	static function Load($url = NULL)
	{
		if (self::$obj == NULL)
			return new Session($url);
		else
			return self::$obj;
	}

	// Sets use as manager, user or not logged in
	public function SetUse($use)
	{
		if ( $use == "Manager" || $use == "User" )
			$this->use = $use;
		else 
			$this->use = NULL;
	}

	private function SendHeader()
	{
		if ($this->notFound)
			header("HTTP/1.0 404 Not Found");
	}

	// Add pages here
	public function File()
	{
		$URLArray = $this->GetURLParts();

		if ($this->use == NULL)
		{
			switch ($URLArray[1])
			{
				case "managers":
					$this->params = "manager";
					$file = "/templetes/login.php";	
					break;
				case "createaccount":
					if (isset($URLArray[2]) && $URLArray[2] == "manager" )
						$this->params = "manager";
					else
						$this->params = "user";
					$file = "/templetes/createAccount.php";
					break;
				default:
					$this->params = "user";
					$file = "/templetes/login.php";	
					break;
			}			
		}
		elseif ($this->use == "Manager")
		{
			switch($URLArray[1])
			{
				case "":
					$this->params = "manager";
					$file = "/templetes/homepage.php";
					break;
				case "account":
					$this->params = "manager";
					$file = "/templetes/accountedit.php";
					break;
				case "managerestaurants":
					$this->params = "manager";
					$file = "/templetes/managerestaurants.php";
					break;
				default:
					$this->notFound = TRUE;
					$file = "/templetes/404.php";
			}
		}
		else
		{
			switch($URLArray[1])
			{
				case "":
					$this->params = "user";
					$file = "/templetes/homepage.php";
					break;
				case "account":
					$this->params = "user";
					$file = "/templetes/accountedit.php";
					break;
				default:
					$this->notFound = TRUE;
					$file = "/templetes/404.php";
			}
		}
		
		// Optionally send out headers (i.e. for 404 page)
		$this->SendHeader();
			
		return $_SERVER['DOCUMENT_ROOT'] . $file;
	}

	private function GetURLParts()
	{
		return explode('/',strtolower($this->url));
	}
}
