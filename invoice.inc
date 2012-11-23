<?php

function csv_to_list($csvString)
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

function list_to_csv($csvArray)
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
function getListTotal($csvArray, $option = 1)
{//"Name","QTY","UnitPrice"
	$sum = 0;
	$tqty = 0;
	$qty = 1;
	for ($iter = 0; $iter < count($csvArray); $iter++)
	{
		if ($iter % 3 == 1)
		{
			$qty = $csvArray[$iter];
			$tqty += $qty;
		}
		elseif ($iter % 3 == 2)
		{
			$sum += ($qty * $csvArray[$iter]);
		}
	}
	switch($option)
	{
		case 2:
			return $tqty;
			break;
		default:
			return $sum;
	}
}

function getPriceOfcsv($csvString)
{
	return(getPriceOfList(csv_to_list($csvString)));
}

?>
