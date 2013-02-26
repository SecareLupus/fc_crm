<?php
// Address.inc
// PHP Class for encapsulating Addresses for storage in the MySQL database.

class Address
{
	private $street = "";
	private $city = "";
	private $state = "";
	private $zip = "";
	
	public function __construct($str, $cit, $sta, $zip)
	{
		$this->street = $str;
		$this->city = $cit;
		$this->state = $sta;
		$this->zip = $zip;
	}
	
	public function getAddress()
	{
		$arr = array("street"=>($this->street), "city"=>($this->city), "state"=>($this->state), "zip"=>($this->zip));
		return $arr;
	}
	
	public function getStreetAddress()
	{
		return $this->street;
	}
	
	public function getCity()
	{
		return $this->city;
	}
	
	public function getState()
	{
		return $this->state;
	}
	
	public function getZip()
	{
		return $this->zip;
	}
	
	public function setStreetAddress($setVar)
	{
		$this->street = $setVar;
	}
	
	public function setCity($setVar)
	{
		$this->street = $setVar;
	}
	
	public function setState($setVar)
	{
		$this->street = $setVar;
	}
	
	public function setZip($setVar)
	{
		$this->street = $setVar;
	}
}

?>