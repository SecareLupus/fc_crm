<?php
// Task.inc
// PHP Class for accessing and modifying Tasks stored in the MySQL database.

//include('funcs.inc');
include_once('Contact.inc');
include_once('Employee.inc');
include_once('Validate.inc');

class Task
{	
	private $id = -1;
	private $category = "";
	private $summary = "";
	private $description = "";
	
	public function __construct($pullid)
	{
		$this->id = $pullid;
		$contactRow = self::fetchTask($this->id, "phone, problem, notes");
		
		$this->category = $contactRow['phone'];
		$this->summary = $contactRow['problem'];
		$this->description = $contactRow['notes'];
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
	
	public function setCategory($newVal)
	{
		//Is this safe?
		if (self::updateTask($id, 'phone', $newVal))
		{
			$category = $newVal;
			return true;
		}
		return false;
	}
	
	public function setSummary($newVal)
	{
		//Is this safe?
		if (self::updateTask($id, 'problem', $newVal))
		{
			$summary = $newVal;
			return true;
		}
		return false;
	}
	
	public function setDescription($newVal)
	{
		//Is this safe?
		if (self::updateTask($id, 'notes', $newVal))
		{
			$description = $newVal;
			return true;
		}
		return false;
	}
	
	public static function getStatusOptions()
	{
		$cxn = open_stream();
		$sql = "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS
			WHERE TABLE_NAME = 'Tasks' AND COLUMN_NAME = 'status'";
		$result = query($cxn, $sql);
		if ($row = mysqli_fetch_assoc($result))
		{
			echo $row['COLUMN_TYPE'];
			return $row['COLUMN_TYPE'];
		}
		else
		{
			return null;
		}
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
}

?>