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
				case "reservations":
					if (isset($URLArray[2]) && $URLArray[2] == "past")
					{					
						if (isset($_GET['id']) && is_numeric($_GET['id']))
						{
							
							if (!($this->params[0] = Restaurant::GetRestaurantWithMID($_GET['id'],$_SESSION['id'],$active = TRUE)))
							{
								$this->params = FALSE;
								$file = "/templetes/404.php";
								break;
							}
							else
							{
								$this->params[1] = "past";
							}
						}	
					}
					elseif (isset($_GET['id']) && is_numeric($_GET['id']))
					{	
						if (!($this->params[0] = Restaurant::GetRestaurantWithMID($_GET['id'],$_SESSION['id'],$active = TRUE)))
						{
							$this->params = FALSE;
							$file = "/templetes/404.php";
							break;
						}
						else
						{
							$this->params[1] = "future";
						}
						
					}
					else
					{	
						$this->params = FALSE;
						$file = "/templetes/404.php";
						break;
					}
					
					$file = "/templetes/reservationmanager.php";
					break;
				case "account":
					$this->params = "manager";
					$file = "/templetes/accountedit.php";
					break;
				case "managerestaurants":
					$this->params = "manager";
					$file = "/templetes/managerestaurants.php";
					break;
				case "newrestaurant":
					$this->params = "new";
					$file = "/templetes/restaurantmod.php";
					break;
				case "editrestaurant":
					if (isset($_GET['id']) && is_numeric($_GET['id']))
					{
						$this->params = Restaurant::GetRestaurantWithMID($_GET['id'],$_SESSION['id']);
						if ($this->params)
						{
							$file = "/templetes/restaurantmod.php";
							break;
						}	
					}
					$this->notFound = TRUE;
					$file = "/templetes/404.php";
					break;
				case "edittables":
					if (isset($_GET['id']) && is_numeric($_GET['id']))
					{
						$this->params = Restaurant::GetRestaurant($_GET['id']);
						if ($this->params)
						{
							$file = "/templetes/managetables.php";
							break;
						}	
					}
					$this->notFound = TRUE;
					$file = "/templetes/404.php";
					break;
				case "compare":
					$this->params = "manager";
					$file = "/templetes/compare.php";
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
				case "map":
					$this->params = "user";
					$file = "/templetes/mainmap.php";
					break;
				case "reservations":
					if (isset($URLArray[2]) && $URLArray[2] == "edit")
					{
						if (isset($_GET['id']) && is_numeric($_GET['id']))
						{
							$this->params[0] = "user";
							$this->params[1] = Reservations::GetReservationFromRIDAndUID($_GET['id'],$_SESSION['id']);
							if ($this->params[1])
							{
								$file = "/templetes/usereditreservations.php";
								break;
							}
						}
						
						$this->notFound = TRUE;
						$file = "/templetes/404.php";
						break;
					}
					else
					{
						$this->params[0] = "user";
						$this->params[1] = FALSE;	
										
						if (isset($URLArray[2]) && is_numeric($URLArray[2]))
						{
							$this->params[1] = Restaurant::GetRestaurant($URLArray[2], $active = TRUE);
						}
						$file = "/templetes/reservations.php";
					}
					break;
				case "myreservations":
					$this->params = "user";
					$file = "/templetes/myreservations.php";
					break;
				case "restaurants":
					$this->params[0] = "user";
					
					if (isset($_GET['id']) && is_numeric($_GET['id']))
					{
						$this->params[1] = Restaurant::GetRestaurant($_GET['id'], $active = TRUE);	
					}
					else
					{
						$this->params[1] = FALSE;	
					}
					
					$file = "/templetes/userrestaurants.php";
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
		$URLList = explode('#',strtolower($this->url));
		
		return explode('/',$URLList[0]);
	}
}
