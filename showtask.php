<?php
// GET
// sort = 'num' - by member number
// showmembers.php

   include('funcs.inc');
   include('member.inc');
   include('header.php');
   require('customisation.inc');
   global $CUS_Category_Name;
   global $CUS_Assigned_Employee;
   global $CUS_Task_Summary;
   $cxn = open_stream();
   
   echo "<h2>Task Info</h2>
         <hr>";

	$sql = "SELECT TID from Tasks where TID='0'";
	if($_GET['TID'])
	{
		extract($_GET);
		$sqlCompanyInfo = "SELECT * FROM Tasks WHERE TID='$TID'";
	}
	else
	{
		die("No task chosen.<hr>");
	}

   $result = query($cxn, $sqlCompanyInfo);
   if($row = mysqli_fetch_assoc($result))
   {
      extract($row);
		//createdOn status dueDate notes
      echo "<table>";
      echo "<tr><td width=250>$CUS_Category_Name:</td><td width=500>$phone</td></tr>";
      echo "<tr><td>$CUS_Task_Summary:</td><td>$problem</td></tr>";
      echo "<tr><td>$CUS_Assigned_Employee:</td><td>";
			printAgent($assignedTo, 1);
      echo "</td></tr>";
      if ($customerType == 'Business')
      {
		  echo "<tr><td>Hiring Company:</td><td>";
		  printBusinessLink($customerID);
		  echo "</td></tr>";
		  echo "<tr><td>Company Contact:</td><td>";
		  printBusinessContactLink($customerID, 2);
		  echo "</td></tr>";
	  }
	  elseif ($customerType == 'Individual')
	  {
		  echo "<tr><td>Customer:</td><td>";
		  printCustomerLink($customerID, 2);
		  echo "</td></tr>";
	  }
	  else
	  {
		  echo "<tr><td>Customer:</td><td>ERROR: Customer Type Incorrect</td></tr>";
	  }
      echo "<tr><td>Created On:</td><td>$createdOn</td></tr>";
      echo "<tr><td>Status:</td><td>$status</td></tr>";
      echo "<tr><td>Due Date:</td><td>$dueDate</td></tr>";
      echo "<tr><td>Description:</td><td>$notes</td></tr>";
      echo "</table>";
      $TID = $_GET['TID'];
      echo "<table><td width=100><a href='edittask.php?TID=$TID'>Edit Task</a></td><td width=100><a href='printtask.php?TID=$TID' target='_blank'>Print Task</a></td><td>";
      $sql = "SELECT * FROM Invoices WHERE taskID=$TID";
      $result = query($cxn, $sql);
      if ($row = mysqli_fetch_assoc($result))
      {
		  extract($row);
		  if ($paid)
		  {
			  echo "<a href='showinvoice.php?InvID=$InvoiceID'>View Invoice</a>";
		  }
		  else
		  {
			  echo "<a href='editinvoice.php?InvID=$InvoiceID'>Edit Invoice</a>";
		  }
	  }
	  else
	  {
		  echo "<a href='addinvoice.php?TID=$TID&status=$status'>Create Invoice</a>";
	  }
      
      
      echo "</td></table>";
   }
   
	if($_POST['submit'] == 'Add Note')
	{
		extract($_POST);
		addNote($newNote, 'TID', $_GET['TID']); 
	}
	displayNotes('TID', $_GET['TID']);
	
	include ('footer.php');
?>
