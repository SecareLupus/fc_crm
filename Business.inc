<?php
// Business.inc
// PHP Class for accessing and modifying Businesses stored in the MySQL database.

//include('funcs.inc');
include_once('Address.inc');
include_once('Validate.inc');

class Business
{
	private $id = -1;
	private $name = "";
	private $taxExempt = "";
	private $phone = "";
	private $address = null;
	private $needsUpdate = array();
	
	public function __construct($pullid)
	{
		$this->id = $pullid;
		$contactRow = self::fetchBusiness($this->id, "name, taxexempt, street, city, state, zip, phonenum");
		
		$this->name = $contactRow['name'];
		$this->taxExempt = $contactRow['taxexempt'];
		$this->phone = $contactRow['phonenum'];
		$this->address = new Address($contactRow['street'], $contactRow['city'], $contactRow['state'], $contactRow['zip']);
	}
	
	public function getID()
	{
		return $this->id;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getTaxExempt()
	{
		return $this->taxExempt;
	}
	
	public function getPhoneNum()
	{
		return $this->phone;
	}
	
	public function getAddress()
	{
	   return $this->address;
	}
	
	public function getLink($tmpName="")
	{
		$tmpid = $this->id;
		if ($tmpName == "") $tmpName = $this->getName();
		return "<a href='showbusiness.php?BID=$tmpid'>$tmpName</a>";
	}
	
	public function getContact()
	{
		$cxn = open_stream();
		
		$sql = "SELECT contactCID FROM Businesses WHERE BID=$this->id";
		$result = query($cxn, $sql);
		
		if($row = mysqli_fetch_assoc($result))
		{
			//Add new Business to Array
			return new Contact($row['contactCID']);
		}
		return null;
	}
	
	public function getTasks($status='All', $invert=false)
	{
		$cxn = open_stream();
		$tmpTasks = array();
		
		$sql = "";
		if ($status = 'All')
		{
			$sql = "SELECT TID FROM Tasks WHERE customerID=$this->id AND customerType='Business'";
		}
		else
		{
			if (!invert)
			{
				$sql = "SELECT TID FROM Tasks WHERE customerID=$this->id AND customerType='Business' AND status='$status'";
			}
			else
			{
				$sql = "SELECT TID FROM Tasks WHERE customerID=$this->id AND customerType='Business' AND status!='$status'";
			}
		}
		
		$result = query($cxn, $sql);
		
		while($row = mysqli_fetch_assoc($result))
		{
			$tmpTasks[] = new Task($row['TID']);
		}
		return $tmpTasks;
	}
	
	public function setName($newName, $push=true)
	{
		//Is this a real name?
		$this->needsUpdate['name'] = $newName;
		if ($push)
		{
			if ($this->pushUpdate())
			{
				return true;
			}
		}
		else
		{
			return true;
		}
		return false;
	}
	
	public function setContactCID($newContact, $push=true)
	{
		$this->needsUpdate['contactCID'] = $newContact;
		if ($push)
		{
			if ($this->pushUpdate())
			{
				return true;
			}
		}
		else
		{
			return true;
		}
		return false;
	}
	
	public function setTaxExempt($newNum, $push=true)
	{
		//Is this a legal tax exemption number?
		$this->needsUpdate['taxExempt'] = $newNum;
		if ($push)
		{
			if ($this->pushUpdate())
			{
				return true;
			}
		}
		else
		{
			return true;
		}
		return false;
	}
	
	public function setPhone($newNumber, $push=true)
	{
		//Strip out everything that isn't a number.
		//If there are more or less than 10 digits, break.
		//Else, format as follows: "(ABC) DEF-GHIJ"
		if ($newNumber == null)
		{
			return false;
		}
		$this->needsUpdate['phonenum'] = $newNumber;
		if ($push)
		{
			if ($this->pushUpdate())
			{
				return true;
			}
		}
		else
		{
			return true;
		}
		return false;
	}
	
	public function setAddress($newAddress, $push=true)
	{
		$tmpAddress = $this->address;
		if ($tmpAddress->getStreetAddress() != $newAddress->getStreetAddress())
		{
			$this->needsUpdate['street'] = $newAddress->getStreetAddress();
		}
		if ($tmpAddress->getCity() != $newAddress->getCity())
		{
			$this->needsUpdate['city'] = $newAddress->getCity();
		}
		if ($tmpAddress->getState() != $newAddress->getState())
		{
			$this->needsUpdate['state'] = $newAddress->getState();
		}
		if ($tmpAddress->getZip() != $newAddress->getZip())
		{
			$this->needsUpdate['zip'] = $newAddress->getZip();
		}
		
		if ($push)
		{
			if ($this->pushUpdate())
			{
				return true;
			}
		}
		else
		{
			return true;
		}
		return false;
	}
	
	public function pushUpdate()
	{
		if($this->rawUpdate(array_keys($this->needsUpdate), $this->needsUpdate))
		{
			return new Business($this->id);
		}
		return null;
	}
	
	private function rawUpdate($var, $value)
	{
		if (self::updateBusiness($this->id, $var, $value)) return true;
		return false;
	}
	
	public static function createBusiness()
	{
		$cxn = open_stream();
		$sql = "INSERT INTO Businesses (createdOn) VALUES (NOW())";
		
		if($result = query($cxn, $sql))
		{
			$sql = "SELECT BID FROM Businesses ORDER BY BID DESC LIMIT 1";
			$result = query($cxn, $sql);
			if ($row = mysqli_fetch_assoc($result))
			{
				return new Business($row['BID']);
			}
		}
		return null;
	}
	
	
	private static function fetchBusiness($BID, $rows = '*')
	{
		$cxn = open_stream();
		$sql = "SELECT $rows FROM Businesses WHERE BID=$BID";
		
		$result = query($cxn, $sql);
		
		if($row = mysqli_fetch_assoc($result))
		{
			return $row;
		}
		else
		{
			return null;
		}
	}
	
	private static function updateBusiness($BID, $var, $value)
	{
		$cxn = open_stream();
		$sql = "";
		if(is_array($var) && is_array($value))
		{
			$sql .= "UPDATE Businesses SET ";
			$first = true;
			while(count($var) > 0)
			{
				if($first)
				{
					$first = false;
				}
				else
				{
					$sql .= ", ";
				}
				$sql .= array_shift($var) . "='" . array_shift($value) . "'";
			}
			$sql .=  " WHERE BID=$BID";
		}
		elseif(!is_array($var) && !is_array($value))
		{
			$sql = "UPDATE Businesses SET $var='$value' WHERE BID=$BID";
		}
		else
		{
			return false;
		}
		
		if ($result = query($cxn, $sql))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

?>
