<?php
// Employee.inc
// PHP Class for accessing and modifying Contacts stored in the MySQL database.

//include('funcs.inc');

class Employee
{
	private $id = -1;
	private $fname = "";
	private $lname = "";
	private $uname = "";
	private $email = "";
	private $phone = "";
	
	public function __construct($pullid)
	{
		$this->id = $pullid;
		$contactRow = self::fetchEmployee($this->id, "fname, lname, username, email, phone");
		
		$this->fname = $contactRow['fname'];
		$this->lname = $contactRow['lname'];
		$this->uname = $contactRow['username'];
		$this->email = $contactRow['email'];
		$this->phone = $contactRow['phone'];
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
	
	public function getUsername()
	{
		return $this->uname;
	}
	
	public function getPhoneNum()
	{
		return $this->phonenum;
	}
	
	public function getEmail()
	{
		return $this->email;
	}
	
	/* No Page to Link Employees to yet... Profiles?	
	public function getLink($order=1)
	{
		$tmpid = $this->id;
		return "<a href='showcontact.php?CID=" . $this->id . "'>" . $this->getName($order) . "</a>";
	}
	*/
	
	public function getTasks($status='All', $invert=false)
	{
		$cxn = open_stream();
		$tmpTasks = array();
		
		$sql = "";
		if ($status = 'All')
		{
			$sql = "SELECT TID FROM Tasks WHERE assignedTo=$this->id";
		}
		else
		{
			if (!invert)
			{
				$sql = "SELECT TID FROM Tasks WHERE assignedTo=$this->id AND status='$status'";
			}
			else
			{
				$sql = "SELECT TID FROM Tasks WHERE assignedTo=$this->id AND status!='$status'";
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
			$this->fname = $newName;
			return true;
		}
		return false;
	}
	
	public function setLName($newName)
	{
		//Is this a real name?
		if (updateContact($id, 'lname', $newName))
		{
			$this->lname = $newName;
			return true;
		}
		return false;
	}
	
	public function setUName($newName)
	{
		//Is this a real name?
		if (updateContact($id, 'uname', $newName))
		{
			$this->uname = $newName;
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
		if (updateContact($id, 'phone', $newNumber))
		{
			$this->phone = $newNumber;
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
			$this->email = $newAddress;
			return true;
		}
		return false;
	}
	
	private static function fetchEmployee($AID, $rows = '*')
	{
		$cxn = open_stream();
		$sql = "SELECT $rows FROM Agents WHERE AID=$AID";
		
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
	
	private static function updateEmployee($AID, $var, $value)
	{
		$cxn = open_stream();
		$sql = "";
		if(is_array($var) && is_array($value))
		{
			$sql .= "UPDATE Agents SET ";
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
			$sql .=  " WHERE AID=$AID";
		}
		elseif(!is_array($var) && !is_array($value))
		{
			$sql = "UPDATE Agents SET $var='$value' WHERE AID=$AID";
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