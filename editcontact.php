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
		$tmpCID = $_GET['CID'];
		$firstname = cleanString($firstname);
		$lastname = cleanString($lastname);
		if (!check_email_address($emailadd)) die("Invalid Email Address");
		$howfound = cleanString($howfound);
		$phonenum = phonenumFromString($phonenum);
		$sql = "UPDATE Customers SET fname='$firstname', lname='$lastname', email='$emailadd', phonenum='$phonenum', howfound='$howfound' WHERE CID=$tmpCID";
		if($result = query($cxn, $sql))
		{
			echo "Entry Updated. Go ahead, check it below!<br>";
		}
		else
		{
			echo "ERROR! Entry not Updated.<br>";
		}
	}
   
   echo "<h2>Contact Info</h2>
         <hr>";

	$sql = "SELECT CID from Customers where CID='0'";
	if($_GET['CID'])
	{
		extract($_GET);
		$sql = "SELECT * FROM Customers WHERE CID='$CID'";
	}
	else
	{
		die("No customer chosen.<hr>");
	}

   $result = query($cxn, $sql);
   if($row = mysqli_fetch_assoc($result))
   {
      extract($row);
		//howfound	createdOn
      echo "<form method='post'><table>";
      echo "<tr><td width=250>Contact Name:</td><td width=500>
			<input type='text' name='firstname' size=25 maxlength=50 value='$fname' /><br><input type='text' name='lastname' size=25 maxlength=50 value='$lname' /></td></tr>";
      echo "<tr><td>Phone Number:</td><td><input type='text' name='phonenum' size=25 maxlength=20 value='$phonenum' /></td></tr>";
      echo "<tr><td>Email Address:</td><td><input type='text' name='emailadd' size=25 maxlength=150 value='$email' /></td></tr>";
      echo "<tr><td>How Found:</td><td><textarea rows='3' cols='25' name='howfound'>$howfound</textarea></td></tr>";
      echo "</table><input type='submit' name='submit' value='Update'></form>";
      $CID = $_GET['CID'];
      echo "<a href='showcontact.php?CID=$CID'>Contact Overview</a>";
   }
   echo "<hr>";

	if($_POST['submit'] == 'Add Note')
	{
		extract($_POST);
		addNote($newNote, 'CID', $_GET['CID']); 
	}
	displayNotes('CID', $_GET['CID']);
	
	include ('footer.php');
?>
