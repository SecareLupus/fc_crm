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
	//Contact
	
	public function __construct($pullid)
	{
		$this->id = $pullid;
		$contactRow = self::fetchBusiness($this->id, "name, taxexempt, street, city, state, zip, phonenum");
		
		$this->name = $contactRow['name'];
		$this->taxExempt = $contactRow['taxexempt'];
		$this->phone = $contactRow['phonenum'];
		$this->address = new Address($contactRow['street'], $contactRow['city'], $contactRow['state'], $contactRow['zip']);
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
	
	public function getLink()
	{
		$tmpid = $this->id;
		$tmpName = $this->getName();
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
	
	public function setName($newName)
	{
		//Is this a real name?
		if (updateBusiness($id, 'name', $newName))
		{
			$this->name = $newName;
			return true;
		}
		return false;
	}
	
	public function setTaxExempt($newNum)
	{
		//Is this a legal tax exemption number?
		if (updateBusiness($id, 'taxExempt', $newNum))
		{
			$this->taxExempt = $newNum;
			return true;
		}
		return false;
	}
	
	public function setPhone($newNumber)
	{
		//Strip out everything that isn't a number.
		//If there are more or less than 10 digits, break.
		//Else, format as follows: "(ABC) DEF-GHIJ"
		$newNumber = buildPhoneNum($newNumber);
		if ($newNumber == null)
		{
			return false;
		}
		if (updateBusiness($id, 'phonenum', $newNumber))
		{
			$this->phone = $newNumber;
			return true;
		}
		return false;
	}
	
	public function setAddress($newAddress)
	{
		$tmpAddress = $this->address;
		//This is a real address?		
		if ($tmpAddress->getStreetAddress() != $newAddress->getStreetAddress())
		{
			if (!self::updateBusiness($this->id, 'street', $newAddress->getStreetAddress())) return false;
		}
		if ($tmpAddress->getCity() != $newAddress->getCity())
		{
			if (!self::updateBusiness($this->id, 'city', $newAddress->getCity())) return false;
		}
		if ($tmpAddress->getState() != $newAddress->getState())
		{
			if (!self::updateBusiness($this->id, 'state', $newAddress->getState())) return false;
		}
		if ($tmpAddress->getZip() != $newAddress->getZip())
		{
			if (!self::updateBusiness($this->id, 'zip', $newAddress->getZip())) return false;
		}
		$this->address = $newAddress;
		return true;
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