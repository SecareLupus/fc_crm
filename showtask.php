<?php
// GET
// sort = 'num' - by member number
// showmembers.php

   include('funcs.inc');
   include('member.inc');
   include('header.php');
   include_once('Task.inc');
   include_once('Business.inc');
   require('customisation.inc');
   global $CUS_Category_Name;
   global $CUS_Assigned_Employee;
   global $CUS_Task_Summary;
   $cxn = open_stream();
   
   echo "<h2>Task Info</h2>
         <hr>";

	$TID = $_GET['TID'];
	$thisTask = (object) null;
	if($_GET['TID'])
	{
		$thisTask = new Task($TID);
	}
	else
	{
		die("No task chosen.<hr>");
	}

  echo "<table>";
  echo "<tr><td width=250>$CUS_Category_Name:</td><td width=500>" . $thisTask->getCategory() . "</td></tr>";
  echo "<tr><td>$CUS_Task_Summary:</td><td>" . $thisTask->getSummary() . "</td></tr>";
  $tmpEmployee = $thisTask->getAssignedEmployee();
  echo "<tr><td>$CUS_Assigned_Employee:</td><td>" . $tmpEmployee->getName(1) . "</td></tr>";
  $customerType = $thisTask->getCustomerType();
  if ($customerType == 'Business')
  {
	  echo "<tr><td>Hiring Company:</td><td>";
	  $bus = $thisTask->getCustomer();
	  echo $bus->getLink();
	  echo "</td></tr>";
	  echo "<tr><td>Company Contact:</td><td>";
	  $cont = $bus->getContact();
	  echo $cont->getLink(1);
	  echo "</td></tr>";
  }
  elseif ($customerType == 'Individual')
  {
	  echo "<tr><td>Customer:</td><td>";
	  $cus = $thisTask->getCustomer();
	  echo $cus->getLink(2);
	  echo "</td></tr>";
  }
  else
  {
	  echo "<tr><td>Customer:</td><td>ERROR: Customer Type Incorrect</td></tr>";
  }
  echo "<tr><td>Created On:</td><td>" . $thisTask->getDateCreated() . "</td></tr>";
  echo "<tr><td>Status:</td><td>" . $thisTask->getStatus() . "</td></tr>";
  echo "<tr><td>Due Date:</td><td>" . $thisTask->getDateDue() . "</td></tr>";
  echo "<tr><td>Description:</td><td>" . $thisTask->getDescription(-1) . "</td></tr>";
  echo "</table>";

  echo "<table><td width=100><a href='edittask.php?TID=$TID'>Edit Task</a></td><td width=100><a href='printtask.php?TID=$TID' target='_blank'>Print Task</a></td><td>";
  
  $invID = $thisTask->getInvoice();
  if ($invID >= 0)
  {
	  if (false)
	  {
		  echo "<a href='showinvoice.php?InvID=$InvoiceID'>View Invoice</a>";
	  }
	  else
	  {
		  echo "<a href='editinvoice.php?InvID=$invID'>Edit Invoice</a>";
	  }
  }
  else
  {
	  echo "<a href='addinvoice.php?TID=$TID&status=$status'>Create Invoice</a>";
  }
  
  
	echo "</td></table>";
	echo "<hr>";
   
	if($_POST['submit'] == 'Add Note')
	{
		extract($_POST);
		addNote($newNote, 'TID', $_GET['TID']); 
	}
	displayNotes('TID', $_GET['TID']);
	
	include ('footer.php');
?>
