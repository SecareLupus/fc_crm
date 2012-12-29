<?php
// GET
// sort = 'num' - by member number
// showmembers.php

   include('funcs.inc');
   include('member.inc');
   include('header.php');
   $cxn = open_stream();
   
   echo "<h2>Company Info</h2>
         <hr>";

	$sql = "SELECT BID from Businesses where BID='0'";
	if($_GET['BID'])
	{
		extract($_GET);
		$sqlCompanyInfo = "SELECT * FROM Businesses WHERE BID='$BID'";
		$sqlTasks = "SELECT TID, phone, problem, status, notes FROM Tasks
					WHERE (customerID='$BID' AND customerType='Business')";
	}
	else
	{
		die("No business chosen.<hr>");
	}

   $result = query($cxn, $sqlCompanyInfo);
   if($row = mysqli_fetch_assoc($result))
   {
      extract($row);

      echo "<table>";
      echo "<tr><td width=300>Company Name:</td><td width=250>$name</td></tr>";
      echo "<tr><td>Company Contact:</td><td>";
      printCustomerLink($contactCID,2);
      echo "</td></tr>";
      echo "<tr><td>Phone Number:</td><td>$phonenum</td></tr>";
      echo "<tr><td>Street Address:</td><td>$street</td></tr>";
      echo "<tr><td>City:</td><td>$city</td></tr>";
      echo "<tr><td>State:</td><td>$state</td></tr>";
      echo "<tr><td>Zip Code:</td><td>$zip</td></tr>";
      echo "<tr><td>Tax Exempt Number:</td><td>$taxexempt</td></tr>";
      echo "</table>";
      $BID = $_GET['BID'];
      echo "<table><td width=100><a href='editbusiness.php?BID=$BID'>Edit Business</a></td><td width=100><a href='sendemail.php?eType=C&recipID=$contactCID'>Email Contact</a></td><td width=100><a href='addtask.php?cType=B&custID=$BID'>Create Task</a></td></table>";
   }
   echo "<hr>";
   $countrows = 0;
   $result = query($cxn, $sqlTasks);
   while($row = mysqli_fetch_assoc($result))
   {
      extract($row);
      $countrows = $countrows + 1;
      if ($countrows == 1)
      {
		  echo "<h2>Tasks</h2>";
		  echo "<table>
				<tr>
					<td width=250>Phone</td>
					<td width=250>Problem</td>
					<td width=250>Status</td>
					<td width=500>Notes</td>
				</tr>";
	  }
      echo "<tr>
			<td>$phone</td>
			<td>";
			printTaskLink($TID, 2);
	  echo "</td>
			<td>$status</td>
			<td>$notes</td></tr>";
   }
   if ($countrows > 0) echo "</table>";
   
	if($_POST['submit'] == 'Add Note')
	{
		extract($_POST);
		addNote($newNote, 'BID', $_GET['BID']); 
	}
	displayNotes('BID', $_GET['BID']);
	
	include ('footer.php');
?>
