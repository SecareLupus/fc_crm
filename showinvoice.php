<?php
// GET
// sort = 'num' - by member number
// showmembers.php

	include('funcs.inc');
	include('member.inc');
	include_once('Invoice.inc');
	require('customisation.inc');
	global $CUS_Company_Name;
	global $CUS_Company_AddressL1;
	global $CUS_Company_AddressL2;
	global $CUS_Company_Phone;
	global $CUS_Company_TaxRate;
	global $CUS_Expenses_Title;
	$cxn = open_stream();
	$invoiceshowemail = true;
   
	$invID = $_GET['InvID'];
	$thisInvoice = (object) null;
	if($_GET['InvID'])
	{
		$thisInvoice = new Invoice($invID);
	}
	else
	{
		die("No Invoice chosen.<hr>");
	}

	$thisTask = $thisInvoice->getTask();
	$actionsArray = $thisInvoice->getActionsTaken();
	$labourArray = $thisInvoice->getLabourArray();
	$partsArray = $thisInvoice->getPartsArray();
	$labourCost = $thisInvoice->getLabourCost();
	$totalHours = $thisInvoice->getLabourHours();
	$partsCost = $thisInvoice->getPartsCost();
	$taxCost = $thisInvoice->getPartsTax($CUS_Company_TaxRate);

	echo "<table valign='top'>
		<tr>
		<td rowspan=5 height=150 width=500><img height=150 width=150 src='./data/logo.png' ALT='$CUS_Company_Name Logo' /></td>
		<td width=300 align='left'>$CUS_Company_Name</td></tr>
		<tr>
		<td align='left'>$CUS_Company_AddressL1</td></tr>
		<tr>
		<td align='left'>$CUS_Company_AddressL2</td></tr>
		<tr>
		<td align='left'>$CUS_Company_Phone</td></tr>
		</table>";
	echo "<h2>";			 
	echo $thisInvoice->getCustomer()->getName();

	echo "</h2>";
	echo "<table border>";
	echo "<tr height=150 valign='top'>
			<td width=400>
				<b>Issues:</b><br>" .
					$thisTask->getCategory() . " - " . $thisTask->getSummary() .
			"</td>
			<td width=400>
				<b>Actions Taken:</b>";
		
	for ($i = 0; $i < count($actionsArray); $i++)
	{
	  echo "<br>";
	  echo $actionsArray[$i];
	}
				
	echo     "</td>
		</tr>";
	echo "<tr height=150 valign='top'>
			<td width=400>
				<b>$CUS_Expenses_Title:</b>";
	echo "<table>";
	for ($i = 0; $i < count($partsArray); $i++)
	{
		$currPart = $partsArray[$i];
		echo "<tr><td width=150>";
		echo $currPart->getName();
		echo "</td>";
		echo "<td width=100>";
		echo $currPart->getQty();
		echo " units @</td>";
		echo "<td>";
		echo money($currPart->getUnitCost());
		echo " each</td></tr>";
	}
	echo "</table>";
				
	echo	   "</td>
			<td width=400>
				<b>Labor:</b>";
	echo "<table>";

	for ($i = 0; $i < count($labourArray); $i++)
	{
		$currLabour = $labourArray[$i];
		echo "<tr><td width=150>";
		echo $currLabour->getName();
		echo "</td>";
		echo "<td width=100>";
		echo $currLabour->getHours();
		echo " hours @</td>";
		echo "<td>";
		echo money($currLabour->getRate());
		echo " per hour</td></tr>";
	}
	echo "</table>";

	echo 	   "</td>
		</tr>
		</table><br><br>";
	echo "<table>
		<tr><td width=300>Total Time:</td><td width=300>";
	echo $totalHours . "hr";
	echo "</td></tr>
		<tr><td>Effective Rate:</td><td>";
	if($totalHours == 0)
	{
		echo money(0);
	}
	else
	{
		echo money($labourCost / $totalHours);
	}
	echo " per hour</td></tr>
		<tr><td>$CUS_Expenses_Title:</td><td>";
	echo money($partsCost);
	echo "</td></tr>
		<tr><td>Tax:</td><td>";
	echo money($taxCost);
	echo "</td></tr>
		<tr><td><b>Total:</b></td><td><b>";
	$grandTotal = $partsCost + $labourCost + $taxCost;
	echo money($grandTotal);
	echo "</b></td></tr>
		</table><br><br>";
	echo "Work performed by ______________________________________<br><br>";

	echo "<table>
		<tr><td width=300>Name:</td><td width=600>";
	echo $thisTask->getAssignedEmployee()->getName();
	if($invoiceshowemail)
	{
		echo "&nbsp;&nbsp;&nbsp;";
		echo $thisTask->getAssignedEmployee()->getEmail();
	}
	
	if ($thisTask->getCustomerType() == 'Individual')
	{
	echo "</td></tr>
		<tr><td>For:</td><td>";
		$tmpContact = $thisTask->getCustomer();
		echo $tmpContact->getName(1);
		if($invoiceshowemail)
		{
			echo "&nbsp;&nbsp;&nbsp;";
			echo $tmpContact->getEmail();
		}
	}
	else
	{
	echo "</td></tr>
		<tr><td>For:</td><td>";
		$tmpContact = $thisTask->getCustomer()->getContact();
		echo $tmpContact->getName(1);
		if($invoiceshowemail)
		{
			echo "&nbsp;&nbsp;&nbsp;";
			echo $tmpContact->getEmail();
		}
	}
	echo "</td></tr>
		<tr><td>Date:</td><td>" . $thisInvoice->getDateInvoiced() . "</td></tr>
		</table>";
?>
