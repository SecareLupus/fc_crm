<?php
// GET
// sort = 'num' - by member number
// showmembers.php

	include('funcs.inc');
	include('member.inc');
	include_once('Invoice.inc');
	include_once('PnL.inc');
	include('header.php');
	$cxn = open_stream();

	echo "<h2>Edit Invoice</h2>
		 <hr>";

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
	
	if($_POST['submit'] == 'Pay Invoice')
	{
		if ($_POST['confirmPayInvoice'])
		{
			$thisInvoice->setPaid();
		}
		else
		{
			echo "Check Confirm Box to Confirm Payment<br>";
		}
	}

	if ($thisInvoice->isPaid())
	{
	  echo "Paid invoices may not be edited further.<br>";
	  echo "<a href='showinvoice.php?InvID=$invID'>View Invoice</a><br>";
	  include ('footer.php');
	  die();
	}

	$actionArray = $thisInvoice->getActionsTaken();
	$labourArray = $thisInvoice->getLabourArray();
	$partsArray = $thisInvoice->getPartsArray();

	//actionsTaken Post Handling
	if($_POST['submit'] == 'Add Action')
	{
	  $actionArray[] = $_POST['addAction'];
	  $thisInvoice->setActionsTaken($actionArray);
	}
	elseif($_POST['submit'] == 'Edit Action' && isset($_POST['currActions']))
	{
		$editAction = $_POST['currActions'];
		$_POST['submit'] = 'Delete Action';
	}
	if($_POST['submit'] == 'Delete Action' && isset($_POST['currActions']))
	{
		for ($i = 0; $i < count($actionArray); $i++)
		{
			if ($actionArray[$i] == $_POST['currActions'])
			{
				unset($actionArray[$i]);
				break;
			}
		}
		$actionArray = array_values($actionArray);
		$thisInvoice->setActionsTaken($actionArray);
	}

	//Labour Post Handling
	if($_POST['submit'] == 'Add Labour')
	{
		$labourArray[] = new Labour($_POST['addLabour'], $_POST['addLabourHours'], $_POST['addLabourRate']);
		$thisInvoice->setLabour($labourArray);
	}
	elseif($_POST['submit'] == 'Edit Labour' && isset($_POST['currLabour']))
	{
		$editLabour = InvoiceLister::csv_to_list($_POST['currLabour']);
		$_POST['submit'] = 'Delete Labour';
	}
	if($_POST['submit'] == 'Delete Labour' && isset($_POST['currLabour']))
	{
	  $currLabour = Labour::labourFromString($_POST['currLabour']);
	  for ($i = 0; $i < count($labourArray); $i++)
		{
			if ($labourArray[$i]->getName() == $currLabour[0]->getName())
			{
				unset($labourArray[$i]);
				break;
			}
		}
		$labourArray = array_values($labourArray);
		$thisInvoice->setLabour($labourArray);
	}

	//Parts Post Handling
	if($_POST['submit'] == 'Add Part')
	{
	  $partsArray[] = new Parts($_POST['addPart'], $_POST['addPartQty'], $_POST['addPartCost'], $_POST['addPartTaxed']);	  
	  $thisInvoice->setParts($partsArray);
	}
	elseif($_POST['submit'] == 'Edit Part' && isset($_POST['currParts']))
	{
		$editParts = InvoiceLister::csv_to_list($_POST['currParts']);
		$_POST['submit'] = 'Delete Part';
	}
	if($_POST['submit'] == 'Delete Part' && isset($_POST['currParts']))
	{
	  $currParts = Parts::partsFromString($_POST['currParts']);
	  for ($i = 0; $i < count($partsArray); $i++)
		{
			if ($partsArray[$i]->getName() == $currParts[0]->getName())
			{
				unset($partsArray[$i]);
				break;
			}
		}
		$partsArray = array_values($partsArray);
		$thisInvoice->setParts($partsArray);
	}

echo "<table>";
	echo "<tr height=150 valign='top'>
			<td width=400>
				<b>Actions Taken:</b><br>";
				echo "<form method='post'><table>
				<tr><td width=100% align='left'>
					Action:<input type='text' name='addAction' value='$editAction' width='75%'>
					<input type='submit' name='submit' value='Add Action' width='25%'>
				</td></tr>
				<tr><td>
					<select name='currActions' size=5 width='100%'>";
					for ($i = 0; $i < count($actionArray); $i++)
					{
					  echo "<option value='" . $actionArray[$i] . "' width='100%'>" .
							$actionArray[$i] . "</option>";
					}
					if(count($actionArray) == 0)
					{
						echo "<option value='' width='100%'>" .
						"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .
						"No Actions Listed" .
						"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .
						"</option>";
					}
					echo "</select>
				</td></tr>
				<tr><td align='left'>
					<input type='submit' name='submit' value='Edit Action' width='50%'>
					<input type='submit' name='submit' value='Delete Action' width='50%'>
				</td></tr>
				</table></form>";
			echo "</td>
			<td width=400>
				<b>Labor:</b><br>";
				echo "<form method='post'><table>
				<tr><td width=100% align='left'>
					Desc:<input type='text' name='addLabour' value='" . $editLabour[0] . "' width='50%'><br>
					Hours:<input type='text' name='addLabourHours' value='" . $editLabour[1] . "' width='15%'><br>
					Rate:<input type='text' name='addLabourRate' value='" . $editLabour[2] . "' width='15%'>
					<input type='submit' name='submit' value='Add Labour' width='20%'>
				</td></tr>
				<tr><td>
					<select name='currLabour' size=5 width='100%'>";
					for ($i = 0; $i < count($labourArray); $i++)
					{
						echo "<option value='\"" . $labourArray[$i]->getName() . "\",\"" .
						$labourArray[$i]->getHours() . "\",\"" . $labourArray[$i]->getRate() . "\"'>" . 
						$labourArray[$i]->getName() . ", " . $labourArray[$i]->getHours() . 
						" hours at " . money($labourArray[$i]->getRate()) . " per Hour" .
						"</option>";
					}
					if(count($labourArray) == 0)
					{
						echo "<option value='' width='100%'>" .
						"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .
						"No Labor Listed" .
						"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .
						"</option>";
					}
					echo "</select>
				</td></tr>
				<tr><td align='left'>
					<input type='submit' name='submit' value='Edit Labour' width='50%'>
					<input type='submit' name='submit' value='Delete Labour' width='50%'>
				</td></tr>
				</table></form>";
				
	echo     "</td>
		</tr>";
	echo "<tr height=150 valign='top'>
			<td width=400>
				<b>$CUS_Expenses_Title:</b><br>";
				$checkbox = "<input type='checkbox' name='addPartTaxed' width='15%'";
				if(isset($editParts) && $editParts[3] != "")
				{
					$checkbox .= " checked";
				}
				$checkbox .= ">";
				echo "<form method='post'><table>
				<tr><td width=100% align='left'>
					Part:<input type='text' name='addPart' value='" . $editParts[0] . "' width='50%'><br>
					Qty:<input type='text' name='addPartQty' value='" . $editParts[1] . "' width='15%'><br>
					Unit Price:<input type='text' name='addPartCost' value='" . $editParts[2] . "' width='15%'><br>
					Taxed:$checkbox
					<input type='submit' name='submit' value='Add Part' width='20%'>
				</td></tr>
				<tr><td>
					<select name='currParts' size=5 width='100%'>";
				for ($i = 0; $i < count($partsArray); $i++)
				{
				  
				  $taxString = "";
				  
				  if($partsArray[$i]->isTaxed())
				  {
					  $taxString = "Taxed";
				  }
				  else
				  {
					  $taxString = "Not Taxed";
				  }
				  $valuearray = array($partsArray[$i]->getName(), $partsArray[$i]->getQty(),
						$partsArray[$i]->getUnitCost(), $partsArray[$i]->isTaxed());
				  $valuecsv = InvoiceLister::list_to_csv($valuearray);
				  echo "<option value='$valuecsv'>" . $partsArray[$i]->getName() .
					", QTY:" . $partsArray[$i]->getQty() .
					", " . money($partsArray[$i]->getUnitCost()) .
					"ea, $taxString" .
					"</option>";
				}
					if(count($partsArray) == 0)
					{
						echo "<option value='' width='100%'>" .
						"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .
						"No Parts Listed" .
						"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .
						"</option>";
					}
					echo "</select>
				</td></tr>
				<tr><td align='left'>
					<input type='submit' name='submit' value='Edit Part' width='50%'>
					<input type='submit' name='submit' value='Delete Part' width='50%'>
				</td></tr>
				</table></form>";
	echo	   "</td>
			<td width=400>
				<b>Total:</b><br>";

				echo "$CUS_Expenses_Title Total:  " . money($thisInvoice->getPartsCost()) . "<br>";
				echo "Labor Total: " . money($thisInvoice->getLabourCost()) . "<br>";
				echo "Subtotal: " . money($thisInvoice->getLabourCost() + $thisInvoice->getPartsCost()) . "<br>";
				echo "Tax:  " . money($thisInvoice->getPartsTax($CUS_Company_TaxRate)) . "<br>";
				echo "Grand Total: " .
					money($thisInvoice->getLabourCost() + $thisInvoice->getPartsCost() +
					$thisInvoice->getPartsTax($CUS_Company_TaxRate)) .
				"<br><br>";
				echo "<form method='post'><table>" .
				"<tr><td>" .
					"<input type='checkbox' name='confirmPayInvoice'>Confirm Payment</input>" .
				"</td><td>" .
					"<input type='submit' name='submit' value='Pay Invoice'>" .
				"</td></tr>" .
				"</table></form>";

	echo 	   "</td>" .
		"</tr>" .
		"</table><br><br>";
		
	echo "<a href='showinvoice.php?InvID=$invID' target='_blank'>View Invoice</a>";
	
	include ('footer.php');
?>