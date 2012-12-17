<?php
// GET
// sort = 'num' - by member number
// showmembers.php

   include('funcs.inc');
   include('member.inc');
   include('header.php');
   require('customisation.inc');
   global $CUS_Employee_Title;
   
   $cxn = open_stream();
   
	if($_POST['submit'] == 'Update')
	{
		extract($_POST);
		$tmpAID = $_SESSION['ID'];
		$firstname = cleanString($firstname);
		$lastname = cleanString($lastname);
		$username = cleanString($username);
		if (!check_email_address($emailadd)) die("Invalid Email Address");
		$phonenum = phonenumFromString($phonenum);
		$sql = "UPDATE Agents SET fname='$firstname', lname='$lastname', username='$username', email='$emailadd', phone='$phonenum' WHERE AID=$tmpAID";
		if($result = query($cxn, $sql))
		{
			echo "Entry Updated. Go ahead, check it below!<br>";
		}
		else
		{
			echo "ERROR! Entry not Updated.<br>";
		}
	}
	
	if($_POST['resetPass'] == "Reset")
	{
		extract($_POST);
		$tmpAID = $_SESSION['ID'];
		if ($pass == $passconf)
		{
			$pass = hash("sha256", $pass);
			$sql = "UPDATE Agents SET password='$pass' WHERE AID=$tmpAID";
			if(query($cxn, $sql))
			{
				echo "Password successfully reset.<br>";
			}
		}
		else
		{
			echo "Error Resetting Password: Passwords do not match.<br>";
		}
	}
   
   echo "<h2>$CUS_Employee_Title Info</h2>
         <hr>";

	$AID = $_SESSION['ID'];
	$sql = "SELECT * FROM Agents WHERE AID='$AID'";
	if($_GET['AID'])
	{
		die("Until SecClearances are implemented, selecting Agents by AID is unavailable.");
	}

   $result = query($cxn, $sql);
   if($row = mysqli_fetch_assoc($result))
   {
      extract($row);
		//AID	fname	lname	username	password	email	phone
      echo "<form method='post'><table>";
      echo "<tr><td width=250>$CUS_Employee_Title Name:</td><td width=500>
			<input type='text' name='firstname' size=25 maxlength=50 value='$fname' /><br><input type='text' name='lastname' size=25 maxlength=50 value='$lname' /></td></tr>";
	  echo "<tr><td>Username:</td><td><input type='text' name='username' size=25 maxlength=50 value='$username' /></td></tr>";
      echo "<tr><td>Phone Number:</td><td><input type='text' name='phonenum' size=25 maxlength=20 value='$phone' /></td></tr>";
      echo "<tr><td>Email Address:</td><td><input type='text' name='emailadd' size=25 maxlength=150 value='$email' /></td></tr>";
      echo "</table><input type='submit' name='submit' value='Update'></form>";
      
      echo "<hr>";
      
      echo "<form method='post'><table>";
      echo "<tr><td width=250>Reset My Password (confirm):</td><td width=500><input type='password' name='pass' size=25 maxlength=50 /><br><input type='password' name='passconf' size=25 maxlength=50 /></td></tr>";
      echo "</table><input type='submit' name='resetPass' value='Reset'></form>";
   }
   echo "<hr>";
	
	include ('footer.php');
?>
