<?php
// Contact.inc
// PHP Class for accessing and modifying Contacts stored in the MySQL database.

//include('funcs.inc');
include_once('Task.inc');
include_once('Business.inc');
include_once('Validate.inc');

class Contact
{
	private $id = -1;
	private $fname = "";
	private $lname = "";
	private $phonenum = "";
	private $email = "";
	private $howfound = "";
	
	public function __construct($pullid)
	{
		$this->id = $pullid;
		$contactRow = self::fetchContact($this->id, "fname, lname, phonenum, email, howfound");
		
		$this->fname = $contactRow['fname'];
		$this->lname = $contactRow['lname'];
		$this->phonenum = $contactRow['phonenum'];
		$this->email = $contactRow['email'];
		$this->howfound = $contactRow['howfound'];
	}
	
	public function getName($order=1)
	{
		$tmp = "";
		switch($order)
		{
			case 1:
				$tmp .= "$this->fname $this->lname";
				break;
			case 2:
				$tmp .= "$this->lname, $this->fname";
				break;
			case 3:
				$tmp .= "$this->fname";
				break;
			case 4:
				$tmpfname = substr($this->fname, 0, 1);
				$tmp .= "$this->lname, $tmpfname";
				break;
	   }
	   return $tmp;
	}
	
	public function getPhoneNum()
	{
		return $this->phonenum;
	}
	
	public function getEmail()
	{
		return $this->email;
	}
	
	public function getHowFound()
	{
	   return $this->howfound;
	}
	
	public function getLink($order=1)
	{
		$tmpid = $this->id;
		return "<a href='showcontact.php?CID=" . $this->id . "'>" . $this->getName($order) . "</a>";
	}
	
	public function getBusinesses()
	{
		$cxn = open_stream();
		$tmpBusinesses = array();
		
		$sql = "SELECT BID FROM Businesses WHERE ContactCID=$this->id";
		$result = query($cxn, $sql);
		
		while($row = mysqli_fetch_assoc($result))
		{
			//Add new Business to Array
			$tmpBusinesses[] = new Business($row['BID']);
		}
		return $tmpBusinesses;
	}
	
	public function getTasks($status='All', $invert=false)
	{
		$cxn = open_stream();
		$tmpTasks = array();
		
		$sql = "";
		if ($status = 'All')
		{
			$sql = "SELECT TID FROM Tasks WHERE customerID=$this->id AND customerType='Individual'";
		}
		else
		{
			if (!invert)
			{
				$sql = "SELECT TID FROM Tasks WHERE customerID=$this->id AND customerType='Individual' AND status='$status'";
			}
			else
			{
				$sql = "SELECT TID FROM Tasks WHERE customerID=$this->id AND customerType='Individual' AND status!='$status'";
			}
		}
		
		$result = query($cxn, $sql);
		
		while($row = mysqli_fetch_assoc($result))
		{
			$tmpTasks[] = new Task($row['TID']);
		}
		return $tmpTasks;
	}
	
	public function setFName($newName)
	{
		//Is this a real name?
		if (updateContact($id, 'fname', $newName))
		{
			$fname = $newName;
			return true;
		}
		return false;
	}
	
	public function setLName($newName)
	{
		//Is this a real name?
		if (updateContact($id, 'lname', $newName))
		{
			$lname = $newName;
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
		if (updateContact($id, 'phonenum', $newNumber))
		{
			$phonenum = $newNumber;
			return true;
		}
		return false;
	}
	
	public function setEmail($newAddress)
	{
		if (!isLegalEmail($newAddress))
		{
			return false;
		}
		if (updateContact($id, 'email', $newAddress))
		{
			$email = $newAddress;
			return true;
		}
		return false;
	}
	
	private static function fetchContact($ContactCID, $rows = '*')
	{
		$cxn = open_stream();
		$sql = "SELECT $rows FROM Customers WHERE CID=$ContactCID";
		
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
	
	private static function updateContact($contactCID, $var, $value)
	{
		$cxn = open_stream();
		$sql = "";
		if(is_array($var) && is_array($value))
		{
			$sql .= "UPDATE Customers SET ";
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
			$sql .=  " WHERE CID=$contactCID";
		}
		elseif(!is_array($var) && !is_array($value))
		{
			$sql = "UPDATE Customers SET $var='$value' WHERE CID=$contactCID";
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