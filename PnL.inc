<?php
// PnL.inc
// PHP Class for accessing and modifying Parts and Labour units for use in Invoicing.

class Parts
{
	private $name = "Uninitialized Part";
	private $unitCost = 0.00;
	private $qty = 0;
	private $tax = true;
	
	public function __construct($newname="Initialized Part", $quant=1, $uCost=1.00, $taxed=true)
	{
		$this->name = $newname;
		$this->unitCost = $uCost;
		$this->qty = $quant;
		$this->tax = $taxed;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getUnitCost()
	{
		return $this->unitCost;
	}
	
	public function getQty()
	{
		return $this->qty;
	}
	
	public function getTotalCost()
	{
		return ($this->unitCost * $this->qty);
	}
	
	public function isTaxed()
	{
		return $this->tax;
	}
	
	public function getString()
	{
		$tmpStr = '"';
		$tmpStr .= $this->name . '","';
		$tmpStr .= $this->qty . '","';
		$tmpStr .= $this->unitCost . '","';
		if($this->tax)
		{
			$tmpStr .= 'true"';
		}
		else
		{
			$tmpStr .= 'false"';
		}
		
		return $tmpStr;
	}
	
	public function getCost()
	{
		return ($this->unitCost * $this->qty);
	}
	
	public static function partsFromString($string)
	{
		//Turn a single string into an array of strings.
		$arrToPop = InvoiceLister::csv_to_list($string);
		$partsArray = array();
		//Take array of strings and turn them into an array of parts.
		while(count($arrToPop) > 0)
		{
			$newName = array_shift($arrToPop);
			$newQty = array_shift($arrToPop);
			$newUCost = array_shift($arrToPop);
			if (array_shift($arrToPop) == "true")
			{
				$newTax = true;
			}
			else
			{
				$newTax = false;
			}
			$partsArray[] = new Parts($newName, $newQty, $newUCost, $newTax);
		}
		
		return $partsArray;
	}
	
	public static function partsFromOldString($string)
	{
		//Turn a single string into an array of strings.
		$arrToPop = InvoiceLister::csv_to_list($string);
		$partsArray = array();
		//Take array of strings and turn them into an array of parts.
		while(count($arrToPop) > 0)
		{
			$newName = array_shift($arrToPop);
			$newQty = array_shift($arrToPop);
			$newUCost = array_shift($arrToPop);
			$partsArray[] = new Parts($newName, $newQty, $newUCost, true);
		}
		
		return $partsArray;
	}
	
	public static function stringFromParts($partsArr)
	{
		$tmpStr = '';
		$isFirst = true;
		while(count($partsArr) > 0)
		{
			if($isFirst)
			{
				$isFirst = false;
			}
			else
			{
				$tmpStr .= ',';
			}
			$currPart = array_shift($partsArr);
			$tmpStr .= $currPart->getString();
		}
		return $tmpStr;
	}
}

class Labour
{
	private $name = 'Uninitialized Labor';
	private $rate = 0.0;
	private $hours = 0.0;
	
	public function __construct($newName='Initialized Labor', $newHours=1, $newRate=1.00)
	{
		$this->name = $newName;
		$this->hours = $newHours;
		$this->rate = $newRate;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getRate()
	{
		return $this->rate;
	}
	
	public function getHours()
	{
		return $this->hours;
	}
	
	public function getString()
	{
		$tmpStr = '"';
		$tmpStr .= $this->name . '","';
		$tmpStr .= $this->hours . '","';
		$tmpStr .= $this->rate . '"';
		return $tmpStr;
	}
	
	public function getCost()
	{
		return ($this->rate * $this->hours);
	}

	public static function labourFromString($string)
	{
		//Turn a single string into an array of strings.
		$arrToPop = InvoiceLister::csv_to_list($string);
		$labourArray = array();
		//Take array of strings and turn them into an array of parts.
		while(count($arrToPop) > 0)
		{
			$labourArray[] = new Labour(array_shift($arrToPop), array_shift($arrToPop), array_shift($arrToPop));
		}
		
		return $labourArray;
	}
	
	public static function stringFromLabour($labourArr)
	{
		$tmpStr = '';
		$isFirst = true;
		while(count($labourArr) > 0)
		{
			if($isFirst)
			{
				$isFirst = false;
			}
			else
			{
				$tmpStr .= ',';
			}
			$currLabour = array_shift($labourArr);
			$tmpStr .= $currLabour->getString();
		}
		
		return $tmpStr;
	}
}

class InvoiceLister
{
	private function __construct(){}
	
	public static function csv_to_list($csvString)
	{
		if(strlen($csvString) == 0)
		{
			return array();
		}
		
		if (substr($csvString, 0, 1) == '"')
		{
			$csvString = substr($csvString, 1);
		}
		if (substr($csvString, -1, 1) == '"')
		{
			$csvString = substr($csvString, 0, -1);
		}
		return explode('","', $csvString);
	}

	public static function list_to_csv($csvArray)
	{
		$tmpstring = "";
		for ($iter = 0; $iter < count($csvArray); $iter++)
		{
			if ($iter > 0) $tmpstring .= ',';
			$tmpstring .= '"';
			$tmpstring .= $csvArray[$iter];
			$tmpstring .= '"';
		}
		return $tmpstring;
	}
}

?>
