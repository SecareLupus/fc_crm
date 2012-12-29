<?php
// GET
// sort = 'num' - by member number
// showmembers.php

   include('funcs.inc');
   include('member.inc');
   include('header.php');
   $cxn = open_stream();
   
   echo "<h2>Customer Info</h2>
         <hr>";

	$sql = "SELECT CID from Customers where CID='0'";
	if($_GET['CID'])
	{
		extract($_GET);
		$sqlCustomerInfo = "SELECT * FROM Customers WHERE CID='$CID'";
		$sqlCompanies = "SELECT BID, name, phonenum FROM Businesses WHERE contactCID='$CID'";
		$sqlTasks = "SELECT TID, phone, problem, status, notes FROM Tasks
					WHERE (customerID='$CID' AND customerType='Individual')";
	}
	else
	{
		die("No customer chosen.<hr>");
	}

   $result = query($cxn, $sqlCustomerInfo);
   if($row = mysqli_fetch_assoc($result))
   {
      extract($row);

      echo "<table>";
      echo "<tr><td width=300>Contact Name:</td><td width=250>" . printCustomerString($CID, 1) .  "</td></tr>";
      echo "<tr><td>Phone Number:</td><td>$phonenum</td></tr>";
      echo "<tr><td>Email Address:</td><td>$email</td></tr>";
      echo "<tr><td>How Found:</td><td>$howfound</td></tr>";
      echo "</table>";
      $CID = $_GET['CID'];
      echo "";
      echo "<table><td width=100><a href='editcontact.php?CID=$CID'>Edit Customer</a></td><td width=100><a href='sendemail.php?eType=C&recipID=$CID'>Email Contact</a></td><td width=100><a href='addtask.php?cType=I&custID=$CID'>Create Task</a></td></table>";
   }
   echo "<hr>";
   
   $countrows = 0;
   $result = query($cxn, $sqlCompanies);
   while($row = mysqli_fetch_assoc($result))
   {
      extract($row);
      $countrows = $countrows + 1;
      if ($countrows == 1)
      {
		  echo "<h2>Companies</h2>";
		  echo "<table>
				<tr>
					<td width=250>Company Name</td>
					<td>Phone Number</td>
				</tr>";
	  }
      echo "<tr><td>";
	  printBusinessLink($BID);
      echo "</td>
			<td>$phonenum</td></tr>";
	}
   if ($countrows > 0)
   {
		echo "</table>";
		echo "<hr>";
   }
   
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
   if($countrows > 0) echo "</table>";

	if($_POST['submit'] == 'Add Note')
	{
		extract($_POST);
		addNote($newNote, 'CID', $_GET['CID']); 
	}
	displayNotes('CID', $_GET['CID']);
	
	include ('footer.php');
?>
