<?php
// GET
// sort = 'num' - by member number
// showmembers.php

   include('funcs.inc');
   include('member.inc');
   include('header.php');
   $cxn = open_stream();
   
   if($_POST['submit'] == 'Update')
	{
		extract($_POST);
		$tmpBID = $_GET['BID'];
		$name = cleanString($name);
		$taxexempt = extractNums($taxexempt);
		$street = cleanString($street);
		$city = cleanString($city);
		$state = cleanString($state);
		$zip = extractNums($zip);
		$phonenum = phonenumFromString($phonenum);
		$sql = "UPDATE Businesses SET name='$name', taxexempt='$taxexempt', contactCID='$newContact', street='$street', city='$city', state='$state', zip='$zip', phonenum='$phonenum' WHERE BID=$tmpBID";
		if($result = query($cxn, $sql))
		{
			echo "Entry Updated. Go ahead, check it below!<br>";
		}
		else
		{
			echo "ERROR! Entry not Updated.<br>";
		}
	}
   
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

      echo "<form method='post'><table>";
      echo "<tr><td width=250>Company Name:</td><td width=500><input type='text' name='name' value='$name'/></td></tr>";
      echo "<tr><td>Company Contact:</td><td>";
      selectCustomer('newContact', $contactCID);
      echo "</td></tr>";
      echo "<tr><td>Phone Number:</td><td><input type='text' name='phonenum' value='$phonenum'/></td></tr>";
      echo "<tr><td>Street Address:</td><td><input type='text' name='street' value='$street'/></td></tr>";
      echo "<tr><td>City:</td><td><input type='text' name='city' value='$city'/></td></tr>";
      echo "<tr><td>State:</td><td><input type='text' name='state' value='$state'/></td></tr>";
      echo "<tr><td>Zip Code:</td><td><input type='text' name='zip' value='$zip'/></td></tr>";
      echo "<tr><td>Tax Exempt Number:</td><td><input type='text' name='taxexempt' value='$taxexempt'/></td></tr>";
      echo "</table><input type='submit' name='submit' value='Update'>
			</form>";
	  $BID = $_GET['BID'];
      echo "<a href='showbusiness.php?BID=$BID'>Business Overview</a>";
   }
   echo "<hr>";
   
	if($_POST['submit'] == 'Add Note')
	{
		extract($_POST);
		addNote($newNote, 'BID', $_GET['BID']); 
	}
	displayNotes('BID', $_GET['BID']);
	
	include ('footer.php');
?>
