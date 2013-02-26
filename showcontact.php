<?php
// GET
// sort = 'num' - by member number
// showmembers.php

   include('funcs.inc');
   include('member.inc');
   include('header.php');
<<<<<<< HEAD
=======
   include_once('Contact.inc');
>>>>>>> invoices
   $cxn = open_stream();
   
   echo "<h2>Customer Info</h2>
         <hr>";

<<<<<<< HEAD
	$sql = "SELECT CID from Customers where CID='0'";
	if($_GET['CID'])
	{
		extract($_GET);
		$sqlCustomerInfo = "SELECT * FROM Customers WHERE CID='$CID'";
		$sqlCompanies = "SELECT BID, name, phonenum FROM Businesses WHERE contactCID='$CID'";
		$sqlTasks = "SELECT TID, phone, problem, status, notes FROM Tasks
					WHERE (customerID='$CID' AND customerType='Individual')";
=======
	$CID = $_GET['CID'];
	$thisContact = (object) null;
	if($_GET['CID'])
	{
		$thisContact = new Contact($CID);
>>>>>>> invoices
	}
	else
	{
		die("No customer chosen.<hr>");
	}

<<<<<<< HEAD
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
=======
	echo "<table>";
	echo "<tr><td width=300>Contact Name:</td><td width=250>" . $thisContact->getName(1) .  "</td></tr>";
	echo "<tr><td>Phone Number:</td><td>" . $thisContact->getPhoneNum() . "</td></tr>";
	echo "<tr><td>Email Address:</td><td>" . $thisContact->getEmail() . "</td></tr>";
	echo "<tr><td>How Found:</td><td>" . $thisContact->getHowFound() . "</td></tr>";
	echo "</table>";
  
	echo "";
    echo "<table><td width=100><a href='editcontact.php?CID=$CID'>Edit Customer</a></td><td width=100><a href='sendemail.php?eType=C&recipID=$CID'>Email Contact</a></td><td width=100><a href='addtask.php?cType=I&custID=$CID'>Create Task</a></td></table>";
	echo "<hr>";

	$businesses = $thisContact->getBusinesses();
	if (count($businesses) > 0)
	{
		echo "<h2>Companies</h2>";
		echo "<table>";
		echo "	<tr>
					<td width=250>Company Name</td>
					<td>Phone Number</td>
				</tr>";
	}
	while (count($businesses) > 0)
	{
		echo "<tr><td>";
		$curr = array_shift($businesses);
		echo $curr->getLink();
		echo "</td>
			<td>" . $curr->getPhoneNum() . "</td></tr>";
	}
	if (!empty($curr))
	{
		echo "</table>";
		echo "<hr>";
	}
	
	$curr = null;
	$tasks = $thisContact->getTasks();
	if (count($tasks) > 0)
	{
		echo "<h2>Tasks</h2>";
		echo "<table>";
		echo "	<tr>
>>>>>>> invoices
					<td width=250>Phone</td>
					<td width=250>Problem</td>
					<td width=250>Status</td>
					<td width=500>Notes</td>
				</tr>";
<<<<<<< HEAD
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
=======
	}
	while (count($tasks) > 0)
	{
		echo "<tr><td>";
		$curr = array_shift($tasks);
		echo "<tr>
			<td>" . $curr->getCategory() . "</td>
			<td>" . $curr->getLink();
		echo "</td>
			<td>" . $curr->getStatus() . "</td>
			<td>" . $curr->getDescription() . "</td></tr>";
	}
	if (!empty($curr))
	{
		echo "</table>";
		echo "<hr>";
	}
>>>>>>> invoices

	if($_POST['submit'] == 'Add Note')
	{
		extract($_POST);
		addNote($newNote, 'CID', $_GET['CID']); 
	}
<<<<<<< HEAD
	displayNotes('CID', $_GET['CID']);
	
	include ('footer.php');
?>
=======
	
	displayNotes('CID', $CID);
	
	include ('footer.php');
?>
>>>>>>> invoices
