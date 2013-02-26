<?php
// GET
// sort = 'num' - by member number
// showmembers.php

   include('funcs.inc');
   include('member.inc');
   include('header.php');
   $cxn = open_stream();
   
   if($_GET['TID'])
   {
	   $sql = "SELECT * FROM Invoices WHERE taskID=" . $_GET['TID'];
	   
	   $result = query($cxn, $sql);
	   if($row = mysqli_fetch_assoc($result))
	   {
		   extract($row);
		   echo "It appears that there is already an invoice for this " .
				"task. Please click the link below to go to the Edit Invoice Page.";
		   echo "<br><br><a href='editinvoice.php?InvID=$InvID'>Edit this task's invoice.</a><br><br>";
		   include ('footer.php');
		   die();
	   }
	   //taskID	actionsTaken	parts	labour	reccs	dateInvoiced	paid
	   $sql = "INSERT INTO Invoices (taskID, actionsTaken, parts, labour, reccs, dateInvoiced, paid) " .
				"Values (" . $_GET['TID'] . ", \"\", \"\", \"\", \"\", CURDATE(), 0)";
		
		/*
	   if (!$_GET['status'] == 'Completed')
	   {
		   echo "It appears that the task you're trying to invoice is not yet " .
				"completed. Please complete the task before creating your invoice.<br><br>";
		   echo "<a href='edittask.php?TID=$taskID'>Edit Task</a><br><br>";
		   include ('footer.php');
		   die();
	   }
	   */
	   
	   if ($result = query($cxn, $sql))
	   {
		   echo "Invoice created, click below to edit the new invoice.<br>";
		   
		   $sql = "SELECT InvoiceID FROM Invoices WHERE taskID=" . $_GET['TID'];
		   $result = query($cxn, $sql);
		   if($row = mysqli_fetch_assoc($result))
		   {
			   extract($row);
				echo "<a href='editinvoice.php?InvID=$InvoiceID'>Edit New Invoice</a><br>";
		   }
		   else
		   {
			   echo "An error prevented us from linking you to the new invoice.<br>";
		   }
	   }
	   else
	   {
		   echo "Unknown Error prevented invoice creation. Please bear with us, we're alpha.<br>";
	   }
	}
	   include ('footer.php');
?>
