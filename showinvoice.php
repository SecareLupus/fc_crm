<?php
// GET
// sort = 'num' - by member number
// showmembers.php

   include('funcs.inc');
   include('member.inc');
   include('invoice.inc');
   require('customisation.inc');
   global $CUS_Company_Name;
   global $CUS_Company_AddressL1;
   global $CUS_Company_AddressL2;
   global $CUS_Company_Phone;
   global $CUS_Company_TaxRate;
   global $CUS_Expenses_Title;
   $cxn = open_stream();
   $invoiceshowemail = true;
   
   $sql = "SELECT InvoiceID from Invoices where InvoiceID='0'";
	if($_GET['InvID'])
	{
		extract($_GET);
		$sql = "SELECT * FROM Invoices WHERE InvoiceID='$InvID'";
	}
	else
	{
		die("No Invoice chosen.<hr>");
	}

   $result = query($cxn, $sql);
   if($row = mysqli_fetch_assoc($result))
   {
      extract($row);
      $sql = "SELECT * FROM Tasks WHERE TID='$taskID'";
      $result = query($cxn, $sql);
      $row = mysqli_fetch_assoc($result);
      extract($row);
      
      $actionsArray = csv_to_list($actionsTaken);
      $labourArray = csv_to_list($labour);
      $labourCost = getListTotal($labourArray);
      $partsArray = csv_to_list($parts);
      $partsCost = getListTotal($partsArray);
   
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
		  if ($customerType == 'Business')
		  {
			  printBusiness($customerID);
		  }
		  elseif ($customerType == 'Individual')
		  {
			  printCustomer($customerID, 1);
		  }
		  
		  echo "</h2>";
		  echo "<table border>";
		  echo "<tr height=150 valign='top'>
					<td width=400>
						<b>Issues:</b><br>
						$phone - $problem
					</td>
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
			  if ($i % 3 == 0)
			  {
				  echo "<tr><td width=150>";
				  echo $partsArray[$i];
				  echo "</td>";
			  }
			  elseif ($i % 3 == 1)
			  {
				  echo "<td width=100>";
				  echo $partsArray[$i];
				  echo " units @</td>";
			  }
			  else
			  {
				  echo "<td>";
				  echo money($partsArray[$i]);
				  echo " each</td></tr>";
			  }
		  }
		  echo "</table>";
						
		  echo	   "</td>
					<td width=400>
						<b>Labor:</b>";
		  echo "<table>";
		  for ($i = 0; $i < count($labourArray); $i++)
		  {
			  if ($i % 3 == 0)
			  {
				  echo "<tr><td width=150>";
				  echo $labourArray[$i];
				  echo "</td>";
			  }
			  elseif ($i % 3 == 1)
			  {
				  echo "<td width=100>";
				  echo $labourArray[$i];
				  echo " hours @</td>";
			  }
			  else
			  {
				  echo "<td>";
				  echo money($labourArray[$i]);
				  echo " per hour</td></tr>";
			  }
		  }
		  echo "</table>";

		  echo 	   "</td>
				</tr>
				</table><br><br>";
		  echo "<table>
				<tr><td width=300>Total Time:</td><td width=300>";
		  $hours = getListTotal($labourArray, 2);
		  echo $hours . "hr";
		  echo "</td></tr>
				<tr><td>Effective Rate:</td><td>";
		  echo money($labourCost / $hours);
		  echo " per hour</td></tr>
				<tr><td>$CUS_Expenses_Title:</td><td>";
		  echo money($partsCost);
		  echo "</td></tr>
				<tr><td>Tax:</td><td>";
		  $tax = 0;
		  if ($customerType == 'Individual')
		  {
			  $tax = $partsCost * $CUS_Company_TaxRate;
		  }
		  echo money($tax);
		  echo "</td></tr>
				<tr><td><b>Total:</b></td><td><b>";
		  $grandTotal = $partsCost + $labourCost + $tax;
		  echo money($grandTotal);
		  echo "</b></td></tr>
				</table><br><br>";
		  echo "Work performed by ______________________________________<br><br>";
		  
		  echo "<table>
				<tr><td width=300>Name:</td><td width=300>";
				printAgent($assignedTo, 1);
		  if ($customerType == 'Individual')
		  {
			echo "</td></tr>
				<tr><td>For:</td><td>";
				printCustomer($customerID, 1);
		  }
		  else
		  {
			echo "</td></tr>
				<tr><td>For:</td><td>";
				printBusinessContact($customerID, 1);
		  }
		  echo "</td></tr>
				<tr><td>Date:</td><td>$dateInvoiced</td></tr>
				</table>";
	}
?>
