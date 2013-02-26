<?php
// GET
// sort = 'num' - by member number
// showmembers.php

<<<<<<< HEAD
   include('funcs.inc');
   include('member.inc');
   include('invoice.inc');
   include('header.php');
   $cxn = open_stream();
   
   echo "<h2>Edit Invoice</h2>
         <hr>";

	$sql = "SELECT * FROM Invoices WHERE InvoiceID='0'";
	if($_GET['InvID'])
	{
		extract($_GET);
		$sql = "SELECT * FROM Invoices WHERE InvoiceID='$InvID'";
=======
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
>>>>>>> invoices
	}
	else
	{
		die("No Invoice chosen.<hr>");
	}

<<<<<<< HEAD
   $result = query($cxn, $sql);
   if($row = mysqli_fetch_assoc($result))
   {
      extract($row);
      
      if ($paid)
      {
		  echo "Paid invoices may not be edited further.<br>";
		  echo "<a href='showinvoice.php?InvID=$InvoiceID'>View Invoice</a><br>";
		  include('footer');
		  die();
	  }
      
      $actionArray = csv_to_list($actionsTaken);
      $partsArray = csv_to_list($parts);
      $labourArray = csv_to_list($labour);
      
      //actionsTaken Post Handling
      if($_POST['submit'] == 'Add Action')
	  {
		  $actionArray[] = $_POST['addAction'];
		  $actionString = list_to_csv($actionArray);
		  $sql = "UPDATE Invoices SET actionsTaken='$actionString' WHERE InvoiceID='$InvID'";
		  $result = query($cxn, $sql);
	  }
	  elseif($_POST['submit'] == 'Edit Action')
	  {
			$editAction = $_POST['currActions'];
			$_POST['submit'] = 'Delete Action';
	  }
	  if($_POST['submit'] == 'Delete Action')
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
			$actionString = list_to_csv($actionArray);
			$sql = "UPDATE Invoices SET actionsTaken='$actionString' WHERE InvoiceID='$InvID'";
			$result = query($cxn, $sql);
	  }
	  
	  //Labour Post Handling
	  if($_POST['submit'] == 'Add Labour')
	  {
		  $labourArray[] = $_POST['addLabour'];
		  $labourArray[] = $_POST['addLabourHours'];
		  $labourArray[] = $_POST['addLabourRate'];
		  $labourString = list_to_csv($labourArray);
		  $sql = "UPDATE Invoices SET labour='$labourString' WHERE InvoiceID='$InvID'";
		  $result = query($cxn, $sql);
	  }
	  elseif($_POST['submit'] == 'Edit Labour')
	  {
			$editLabour = csv_to_list($_POST['currLabour']);
			$_POST['submit'] = 'Delete Labour';
	  }
	  if($_POST['submit'] == 'Delete Labour')
	  {
		  $currLabourArray = csv_to_list($_POST['currLabour']);
		  for ($i = 0; $i < count($labourArray); $i+=3)
			{
				if ($labourArray[$i] == $currLabourArray[0])
				{
					unset($labourArray[$i]);
					unset($labourArray[$i+1]);
					unset($labourArray[$i+2]);
					break;
				}
			}
			$labourArray = array_values($labourArray);
			$labourString = list_to_csv($labourArray);
			$sql = "UPDATE Invoices SET labour='$labourString' WHERE InvoiceID='$InvID'";
			$result = query($cxn, $sql);
	  }
	  
	  //Parts Post Handling
	  if($_POST['submit'] == 'Add Part')
	  {
		  $partsArray[] = $_POST['addPart'];
		  $partsArray[] = $_POST['addPartQty'];
		  $partsArray[] = $_POST['addPartCost'];
		  $partsString = list_to_csv($partsArray);
		  $sql = "UPDATE Invoices SET parts='$partsString' WHERE InvoiceID='$InvID'";
		  $result = query($cxn, $sql);
	  }
	  elseif($_POST['submit'] == 'Edit Part')
	  {
			$editParts = csv_to_list($_POST['currParts']);
			$_POST['submit'] = 'Delete Part';
	  }
	  if($_POST['submit'] == 'Delete Part')
	  {
		  $currPartsArray = csv_to_list($_POST['currParts']);
		  for ($i = 0; $i < count($partsArray); $i+=3)
			{
				if ($partsArray[$i] == $currPartsArray[0])
				{
					unset($partsArray[$i]);
					unset($partsArray[$i+1]);
					unset($partsArray[$i+2]);
					break;
				}
			}
			$partsArray = array_values($partsArray);
			$partsString = list_to_csv($partsArray);
			$sql = "UPDATE Invoices SET parts='$partsString' WHERE InvoiceID='$InvID'";
			$result = query($cxn, $sql);
	  }
	  //actionsTaken	parts	labour	reccs
      
      echo "<table><tr><td width='400'>";
      //Actions Taken Form
      echo "<form method='post'><table>
			<tr><td width=350 align='center'>Action:<input type='text' name='addAction' value='$editAction' width='75%'><input type='submit' name='submit' value='Add Action' width='25%'></td></tr>
			<tr><td width=350 align='center'><select name='currActions' size=5 width='100%'>";
	  for ($i = 0; $i < count($actionArray); $i++)
	  {
		  echo "<option value='" . $actionArray[$i] . "' width='100%'>" . $actionArray[$i] . "</option>";
	  }
	  echo "</select></td></tr>
			<tr><td width=350 align='center'><input type='submit' name='submit' value='Edit Action' width='50%'><input type='submit' name='submit' value='Delete Action' width='50%'></td></tr></table></form>";
      
      echo "</td><td width='400'>";
      //Labour Form
      echo "<form method='post'><table>
			<tr><td width=350>Desc:<input type='text' name='addLabour' value='" . $editLabour[0] . "' width='50%'><br>Hours:<input type='text' name='addLabourHours' value='" . $editLabour[1] . "' width='15%'><br>Rate:<input type='text' name='addLabourRate' value='" . $editLabour[2] . "' width='15%'><input type='submit' name='submit' value='Add Labour' width='20%'></td></tr>
			<tr><td width=350 align='center'><select name='currLabour' size=5 width='100%'>";
	  for ($i = 0; $i < count($labourArray); $i+=3)
	  {
		  echo "<option value='\"" . $labourArray[$i] . "\",\"" . $labourArray[$i+1] . "\",\"" . $labourArray[$i+2] . "\"'>" . $labourArray[$i] . "&nbsp;-&nbsp;" . $labourArray[$i+1] . "&nbsp;@&nbsp;" . $labourArray[$i+2] . "</option>";
	  }
	  echo "</select></td></tr>
			<tr><td width=350 align='center'><input type='submit' name='submit' value='Edit Labour' width='50%'><input type='submit' name='submit' value='Delete Labour' width='50%'></td></tr>
			</table></form>";
			
	  echo "</td></tr><tr><td width='400'>";
	  //Parts Form
      echo "<form method='post'><table>
			<tr><td width=350>Part:<input type='text' name='addPart' value='" . $editParts[0] . "' width='50%'><br>Qty:<input type='text' name='addPartQty' value='" . $editParts[1] . "' width='15%'><br>Unit Price:<input type='text' name='addPartCost' value='" . $editParts[2] . "' width='15%'><input type='submit' name='submit' value='Add Part' width='20%'></td></tr>
			<tr><td width=350 align='center'><select name='currParts' size=5 width='100%'>";
	  for ($i = 0; $i < count($partsArray); $i+=3)
	  {
		  echo "<option value='\"" . $partsArray[$i] . "\",\"" . $partsArray[$i+1] . "\",\"" . $partsArray[$i+2] . "\"'>" . $partsArray[$i] . "&nbsp;-&nbsp;" . $partsArray[$i+1] . "&nbsp;@&nbsp;" . $partsArray[$i+2] . "</option>";
	  }
	  echo "</select></td></tr>
			<tr><td width=350 align='center'><input type='submit' name='submit' value='Edit Part' width='50%'><input type='submit' name='submit' value='Delete Part' width='50%'></td></tr>
			</table></form>";
			
	  echo "</td></tr></table>";
      
      $InvoiceID = $_GET['InvoiceID'];
      echo "<a href='showinvoice.php?InvID=$InvID' target='_blank'>View Invoice</a>";
   }
=======
	if ($thisInvoice->isPaid())
	{
	  echo "Paid invoices may not be edited further.<br>";
	  echo "<a href='showinvoice.php?InvID=$InvoiceID'>View Invoice</a><br>";
	  include('footer');
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
	//actionsTaken	parts	labour	reccs

	echo "<table><tr><td width='400'>";
	//Actions Taken Form
	echo "<form method='post'><table>
		<tr><td width=350 align='center'>Action:<input type='text' name='addAction' value='$editAction' width='75%'><input type='submit' name='submit' value='Add Action' width='25%'></td></tr>
		<tr><td width=350 align='center'><select name='currActions' size=5 width='100%'>";
	for ($i = 0; $i < count($actionArray); $i++)
	{
	  echo "<option value='" . $actionArray[$i] . "' width='100%'>" . $actionArray[$i] . "</option>";
	}
	echo "</select></td></tr>
		<tr><td width=350 align='center'><input type='submit' name='submit' value='Edit Action' width='50%'><input type='submit' name='submit' value='Delete Action' width='50%'></td></tr></table></form>";

	echo "</td><td width='400'>";
	//Labour Form
	echo "<form method='post'><table>
		<tr><td width=350>Desc:<input type='text' name='addLabour' value='" . $editLabour[0] . "' width='50%'><br>Hours:<input type='text' name='addLabourHours' value='" . $editLabour[1] . "' width='15%'><br>Rate:<input type='text' name='addLabourRate' value='" . $editLabour[2] . "' width='15%'><input type='submit' name='submit' value='Add Labour' width='20%'></td></tr>
		<tr><td width=350 align='center'><select name='currLabour' size=5 width='100%'>";
	for ($i = 0; $i < count($labourArray); $i++)
	{
	  echo "<option value='\"" . $labourArray[$i]->getName() . "\",\"" . $labourArray[$i]->getHours() . "\",\"" . $labourArray[$i]->getRate() . "\"'>" . $labourArray[$i]->getName() . "&nbsp;-&nbsp;" . $labourArray[$i]->getHours() . "&nbsp;@&nbsp;" . $labourArray[$i]->getRate() . "</option>";
	}
	echo "</select></td></tr>
		<tr><td width=350 align='center'><input type='submit' name='submit' value='Edit Labour' width='50%'><input type='submit' name='submit' value='Delete Labour' width='50%'></td></tr>
		</table></form>";
		
	echo "</td></tr><tr><td width='400'>";
	//Parts Form
	$checkbox = "<input type='checkbox' name='addPartTaxed' width='15%'";
	if(isset($editParts) && $editParts[3] != "")
	{
		$checkbox .= " checked";
	}
	$checkbox .= ">";
	echo "<form method='post'><table>
		<tr><td width=350>Part:<input type='text' name='addPart' value='" . $editParts[0] . "' width='50%'><br>Qty:<input type='text' name='addPartQty' value='" . $editParts[1] . "' width='15%'><br>Unit Price:<input type='text' name='addPartCost' value='" . $editParts[2] . "' width='15%'><br>Taxed:$checkbox<input type='submit' name='submit' value='Add Part' width='20%'></td></tr>
		<tr><td width=350 align='center'><select name='currParts' size=5 width='100%'>";
	for ($i = 0; $i < count($partsArray); $i++)
	{
	  echo "<option value='\"" . $partsArray[$i]->getName() . "\",\"" . $partsArray[$i]->getQty() . "\",\"" . $partsArray[$i]->getUnitCost() . "\",\"" . $partsArray[$i]->isTaxed() . "\"'>" . $partsArray[$i]->getName() . "&nbsp;-&nbsp;" . $partsArray[$i]->getQty() . "&nbsp;@&nbsp;" . $partsArray[$i]->getUnitCost() . "</option>";
	}
	echo "</select></td></tr>
		<tr><td width=350 align='center'><input type='submit' name='submit' value='Edit Part' width='50%'><input type='submit' name='submit' value='Delete Part' width='50%'></td></tr>
		</table></form>";
		
	echo "</td></tr></table>";
	
	echo "<a href='showinvoice.php?InvID=$invID' target='_blank'>View Invoice</a>";
>>>>>>> invoices
	
	include ('footer.php');
?>
