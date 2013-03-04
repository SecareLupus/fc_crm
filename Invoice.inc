<?php
// Invoice.inc
// PHP Class for accessing and modifying Invoices, as stored in the MySQL Database.
// Each instantiation of Invoice holds all the data stored on the invoice, or has
// helper functions intended to assist in the procurement of that data.

include_once('PnL.inc');
include_once('Task.inc');
include_once('Contact.inc');
include_once('Business.inc');

class Invoice
{
	private $id = -1;
	private $actionsArr = array();
	private $partsArr = array();
	private $labourArr = array();
	private $reccs = array();
	private $dateInv = "";
	private $paid = false;
	
	public function __construct($pullid, $old=false)
	{
		$this->id = $pullid;
		$contactRow = self::fetchInvoice($this->id, "actionsTaken, parts, labour, reccs, dateInvoiced, paid");
		
		$this->actionsArr = InvoiceLister::csv_to_list($contactRow['actionsTaken']);
		if($old)
		{
			$this->partsArr = Parts::partsFromOldString($contactRow['parts']);
		}
		else
		{
			$this->partsArr = Parts::partsFromString($contactRow['parts']);
		}
		$this->labourArr = Labour::labourFromString($contactRow['labour']);
		$this->reccs = InvoiceLister::csv_to_list($contactRow['reccs']);
		$this->dateInv = $contactRow['dateInvoiced'];
		$this->paid = $contactRow['paid'];
	}
	
	public function getActionsTaken()
	{
		return $this->actionsArr;
	}
	
	public function getPartsArray()
	{
		return $this->partsArr;
	}
	
	public function getLabourArray()
	{
		return $this->labourArr;
	}
	
	public function getReccs()
	{
		return $this->reccs;
	}
	
	public function getDateInvoiced()
	{
		return $this->dateInv;
	}
	
	public function isPaid()
	{
		return $this->paid;
	}
	
	public function getTask()
	{
		$cxn = open_stream();
		$sql = "SELECT taskID FROM Invoices WHERE InvoiceID=$this->id";
		$result = query($cxn, $sql);
		
		if($row = mysqli_fetch_assoc($result))
		{
			return (new Task($row['taskID']));
		}
		return null;
	}

	public function getCustomer()
	{
		$cxn = open_stream();
		$sql = "SELECT taskID FROM Invoices WHERE InvoiceID=$this->id";
		$result = query($cxn, $sql);
		
		if($row = mysqli_fetch_assoc($result))
		{
			$taskID = $row['taskID'];
			$sql = "SELECT customerID, customerType FROM Tasks WHERE TID=$taskID";
			$result = query($cxn, $sql);
			
			if($row = mysqli_fetch_assoc($result))
			{
				if ($row['customerType'] == "Individual")
				{
					return (new Contact($row['customerID']));
				}
				return (new Business($row['customerID']));
			}
		}
		return null;
	}
	
	public function setActionsTaken($actionsArray)
	{
		if(self::updateInvoice($this->id, 'actionsTaken', InvoiceLister::list_to_csv($actionsArray)))
		{
			$this->actionsArr = $actionsArray;
			return true;
		}
		return false;
	}
	
	public function setParts($partsArray)
	{
		if(self::updateInvoice($this->id, 'parts', Parts::stringFromParts($partsArray)))
		{
			$this->partsArr = $partsArray;
			return true;
		}
		return false;
	}
	
	public function setLabour($labourArray)
	{
		if(self::updateInvoice($this->id, 'labour', Labour::stringFromLabour($labourArray)))
		{
			$this->labourArr = $labourArray;
			return true;
		}
		return false;
	}
	
	public function setReccs($reccs)
	{
		if(self::updateInvoice($this->id, 'reccs', InvoiceLister::list_to_csv($reccs)))
		{
			$this->reccs = $reccs;
			return true;
		}
		return false;
	}
	
	public function setPaid()
	{
		if(self::updateInvoice($this->id, 'paid', 'true'))
		{
			$this->paid = true;
			return true;
		}
		return false;
	}
	
	private static function fetchInvoice($invoiceID, $rows = '*')
	{
		$cxn = open_stream();
		$sql = "SELECT $rows FROM Invoices WHERE InvoiceID=$invoiceID";
		
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
	
	private static function updateInvoice($invoiceID, $var, $value)
	{
		$cxn = open_stream();
		$sql = "";
		if(is_array($var) && is_array($value))
		{
			$sql .= "UPDATE Invoices SET ";
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
			$sql .=  " WHERE InvoiceID=$invoiceID";
		}
		elseif(!is_array($var) && !is_array($value))
		{
			$sql = "UPDATE Invoices SET $var='$value' WHERE InvoiceID=$invoiceID";
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
