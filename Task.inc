<?php
// Task.inc
// PHP Class for accessing and modifying Tasks stored in the MySQL database.

//include('funcs.inc');
include_once('Contact.inc');
include_once('Business.inc');
include_once('Employee.inc');
include_once('Validate.inc');

class Task
{	
	private $id = -1;
	private $category = "";
	private $summary = "";
	private $description = "";
	private $needsUpdate = array();
	
	public function __construct($pullid)
	{
		$this->id = $pullid;
		$contactRow = self::fetchTask($this->id, "phone, problem, notes");
		
		$this->category = $contactRow['phone'];
		$this->summary = $contactRow['problem'];
		$this->description = $contactRow['notes'];
	}
	
	public function getID()
	{
		return $this->id;
	}
	
	public function getCategory()
	{
		return $this->category;
	}
	
	public function getSummary()
	{
		return $this->summary;
	}
	
	public function getDescription($length=30)
	{
		if ($length > 0) return (substr($this->description, 0, $length) . "...");
		return $this->description;
	}
	
	public function getLink($text="Summary")
	{
		$tmpid = $this->id;
		$tmpText = "";
		if($text == "Category") $tmpText .= $this->getCategory();
		if($text == "Summary") $tmpText .= $this->getSummary();
		if($text == "Description") $tmpText .= $this->getDescription(15);
		if($tmpText == "") $tmpText .= $text;
		return "<a href='showtask.php?TID=" . $this->id . "'>" . $tmpText . "</a>";
	}
	
	public function getCustomer()
	{
		$cxn = open_stream();
		$sql = "SELECT customerID, customerType FROM Tasks WHERE TID=$this->id";
		$result = query($cxn, $sql);
		
		if($row = mysqli_fetch_assoc($result))
		{
			if ($row['customerType'] == "Individual")
			{
				return (new Contact($row['customerID']));
			}
			return (new Business($row['customerID']));
		}
		return null;
	}
	
	public function getCustomerType()
	{
		$cxn = open_stream();
		$sql = "SELECT customerType FROM Tasks WHERE TID=$this->id";
		$result = query($cxn, $sql);
		
		if($row = mysqli_fetch_assoc($result))
		{
			return $row['customerType'];
		}
		return null;
	}
	
	public function getAssignedEmployee()
	{
		$cxn = open_stream();
		$sql = "SELECT assignedTo FROM Tasks WHERE TID=$this->id";
		$result = query($cxn, $sql);
		
		if($row = mysqli_fetch_assoc($result))
		{
			return (new Employee($row['assignedTo']));
		}
		return null;
	}
	
	public function getStatus()
	{
		$cxn = open_stream();
		$sql = "SELECT status FROM Tasks WHERE TID=$this->id";
		$result = query($cxn, $sql);
		
		if($row = mysqli_fetch_assoc($result))
		{
			return $row['status'];
		}
		return null;
	}
	
	public function getInvoice()
	{
		$cxn = open_stream();
		$sql = "SELECT * FROM Invoices WHERE taskID=$this->id";
		$result = query($cxn, $sql);
		
		if($row = mysqli_fetch_assoc($result))
		{
			return $row['InvoiceID'];
		}
		return -1;
	}
	
	public function getDateDue()
	{
		$tmpdate = self::fetchTask($this->id, "dueDate");
		return $tmpdate['dueDate'];
	}
	
	public function getDateCreated()
	{
		$tmpdate = self::fetchTask($this->id, "createdOn");
		return $tmpdate['createdOn'];
	}
	
	public function setCategory($newVal, $push=true)
	{
		$this->needsUpdate['phone'] = $newVal;
		
		return $this->pushCheck($push);
	}
	
	public function setSummary($newVal, $push=true)
	{
		$this->needsUpdate['problem'] = $newVal;
		
		return $this->pushCheck($push);
	}
	
	public function setCustomer($newCust, $push=true)
	{
		if($newCust instanceof Contact)
		{
			$this->needsUpdate['customerType'] = "Individual";
		}
		elseif($newCust instanceof Business)
		{
			$this->needsUpdate['customerType'] = "Business";
		}
		else
		{
			return false;
		}
		
		$this->needsUpdate['customerID'] = $newCust->getID();	
		
		return $this->pushCheck($push);
	}
	
	public function setAssignedEmployee($newEmpl, $push=true)
	{
		$newID = -1;
		if($newEmpl instanceof Employee)
		{
			$newID = $newEmpl->getID();
		}
		elseif(is_int($newEmpl))
		{
			$newID = $newEmpl;
		}
		else
		{
			return false;
		}
		
		$this->needsUpdate['assignedTo'] = $newID;	
		
		return $this->pushCheck($push);
	}
	
	public function setDescription($newVal, $push=true)
	{
		$this->needsUpdate['notes'] = $newVal;
		
		return $this->pushCheck($push);
	}
	
	public function setDateDue($newDate, $push=true)
	{
		$this->needsUpdate['dueDate'] = $newDate;
		
		return $this->pushCheck($push);
	}
	
	public function setStatus($newVal, $push=true)
	{
		if (in_array($newVal, self::getStatusOptions()))
		{
			$this->needsUpdate['status'] = $newVal;
			
			return $this->pushCheck($push);
		}
		return false;
	}
	
	public function pushCheck($push)
	{
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
			return new Task($this->id);
		}
		return null;
	}
	
	private function rawUpdate($var, $value)
	{
		if (self::updateTask($this->id, $var, $value)) return true;
		return false;
	}
	
	public static function createTask()
	{
		$cxn = open_stream();
		$sql = "INSERT INTO Tasks (createdOn) VALUES (NOW())";
		
		if($result = query($cxn, $sql))
		{
			$sql = "SELECT TID FROM Tasks ORDER BY TID DESC LIMIT 1";
			$result = query($cxn, $sql);
			if ($row = mysqli_fetch_assoc($result))
			{
				return new Task($row['TID']);
			}
		}
		return null;
	}
	
	private static function fetchTask($taskTID, $rows = '*')
	{
		$cxn = open_stream();
		$sql = "SELECT $rows FROM Tasks WHERE TID=$taskTID";
		
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
	
	private static function updateTask($taskTID, $var, $value)
	{
		$cxn = open_stream();
		$sql = "";
		if(is_array($var) && is_array($value))
		{
			$sql .= "UPDATE Tasks SET ";
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
			$sql .=  " WHERE TID=$taskTID";
		}
		elseif(!is_array($var) && !is_array($value))
		{
			$sql = "UPDATE Tasks SET $var='$value' WHERE TID=$taskTID";
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
	
	public static function getStatusOptions()
	{
		//This is a stub that should collect this array programatically
		// from the database rather than being hardcoded in like this.
		$stubArray = array();
		$stubArray[] = "Opened";
		$stubArray[] = "Waiting on Parts";
		$stubArray[] = "Waiting on Customer";
		$stubArray[] = "Waiting on Labor";
		$stubArray[] = "In Progress";
		$stubArray[] = "Completed";
		return $stubArray;
	}
}

?>
