<?php
// GET
// sort = 'num' - by member number
// showmembers.php

   include('funcs.inc');
   include('member.inc');
   include('header.php');
   
   if($_POST['submit'] == 'submit')
   {
	   extract($_POST);
	   
	   $fname = strip_tags($fname);
	   $lname = strip_tags($lname);
	   if (!check_email_address($email)) die("Invalid Email Address");
	   $phone = phonenumFromString($phone);
	   $howfound = cleanString($howfound);
	   
	   if (checkName($fname) && checkName($lname) && check_email_address($email))
	   {
		$cxn = open_stream();
		
		$sql = "INSERT INTO Customers (fname, lname, email, phonenum, howfound, createdOn)
				VALUES ('$fname', '$lname', '$email', '$phone', '$howfound', NOW())";   
		if($result = query($cxn, $sql))
		{
			$sql = "SELECT CID from Customers ORDER BY CID DESC LIMIT 1";
			$result = query($cxn, $sql);
			echo "$lname, $fname added to the database.<br>";
			if ($row = mysqli_fetch_assoc($result))
			{
				extract($row);
				echo "<a href='showcontact.php?CID=$CID'>Click Here to View Customer</a><hr>";
			}
			else
			{
				echo "No link available<hr>";
			}
		}
		else echo "Error adding customer to database.<hr>";
	   }
	   else echo "Error with illegal input.<hr>";
   }

	echo "<h2>Add Contact</h2><hr>
		<form method='post'>
		<table>
		<tr><td width=250>First Name:</td><td width=500><input type='text' name='fname' size=25 maxlength=50></td></tr>
		<tr><td>Last Name:</td><td><input type='text' name='lname' size=25 maxlength=50></td></tr>
        <tr><td>Phone Number:</td><td><input type='text' name='phone' size=25 maxlength=20></td></tr>
        <tr><td>Email Address:</td><td><input type='text' name='email' size=25 maxlength=150></td></tr>
        <tr><td>How Did You Find Us?</td><td><textarea rows='3' cols='25' name='howfound'></textarea></td></tr>
        </table>
        <input type='submit' name='submit' value='submit'>
        </form>";
        
        include ('footer.php');
?>
