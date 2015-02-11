<?php

include_once('Invoice.inc');
include_once('PnL.inc');
require('customisation.inc');

class Report
{
	private $data = array();
	
	public function __construct($startDate, $endDate)
	{
		$cxn = open_stream();
		$sql = "SELECT InvoiceID FROM Invoices WHERE (dateInvoiced >= '$startDate') AND (dateInvoiced <= '$endDate')";
		$result = query($cxn, $sql);
		while($row = mysqli_fetch_assoc($result))
		{
			array_push($this->data, new Invoice($row['InvoiceID']));
		}
	}
	
	public function getCombinedPartsBilled()
	{
		$tmpTotal = 0;
		
		foreach($this->data as $tmpInvoice)
		{
			$tmpTotal += $tmpInvoice->getPartsCost();
		}
		
		return $tmpTotal;
	}
	
	public function getCombinedTaxBilled()
	{
		global $CUS_Company_TaxRate;
		
		$tmpTotal = 0;
		foreach($this->data as $tmpInvoice)
		{
			$tmpTotal += $tmpInvoice->getPartsTax($CUS_Company_TaxRate);
		}
		
		return $tmpTotal;
	}
	
	public function getCombinedLabourBilled()
	{
		$tmpTotal = 0;
		foreach($this->data as $tmpInvoice)
		{
			$tmpTotal += $tmpInvoice->getLabourCost();
		}
		
		return $tmpTotal;
	}

    public function getCombinedLabourUsed()
	{
		$tmpLabour = array();
		foreach($this->data as $tmpInvoice)
		{
			$tmpLabourArray = $tmpInvoice->getLabourArray();
			foreach($tmpLabourArray as $labour)
			{
				$name = $labour->getName();
				$tmpLabour[$name] = 1 + $tmpLabour[$name];
			}
		}
		return $tmpLabour;
	}

	public function getCombinedPartsUsed()
	{
		$tmpParts = array();
		foreach($this->data as $tmpInvoice)
		{
			$tmpPartsArray = $tmpInvoice->getPartsArray();
			foreach($tmpPartsArray as $part)
			{
				$name = $part->getName();
				$tmpParts[$name] = 1 + $tmpParts[$name];
			}
		}
		return $tmpParts;
	}
	
	/*
	public function getCombinedTotalBilled()
	{
		$tmpTotal = 0;
		foreach($this->data as $tmpInvoice)
		{
			$tmpTotal += $tmpInvoice->getTotalCost();
		}
		
		return money($tmpTotal);
	}*/
}

?>
