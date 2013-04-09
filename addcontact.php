<?php
// GET
// sort = 'num' - by member number
// showmembers.php

   include('funcs.inc');
   include('member.inc');
   include('header.php');
   include_once('Contact.inc');
   
   if($_POST['submit'] == 'submit')
   {
	   extract($_POST);
	   
	   $fname = cleanString($fname);
	   $lname = cleanString($lname);
	   if (!check_email_address($email)) die("Invalid Email Address");
	   $phone = phonenumFromString($phone);
	   $howfound = cleanString($howfound);
	   
	   if (checkName($fname) && checkName($lname) && check_email_address($email))
	   {
		$thisContact = Contact::createContact();
		if(is_object($thisContact))
		{
			$thisContact->setFName($fname, false);
			$thisContact->setLName($lname, false);
			$thisContact->setEmail($email, false);
			$thisContact->setHowFound($howfound, false);
			$thisContact->setPhone($phone, false);
			$thisContact = $thisContact->pushUpdate();
			if(is_object($thisContact))
			{
				echo $thisContact->getLName() . ", " . $thisContact->getFName() . " added to the database.<br>";
				$CID = $thisContact->getID();
				echo "<a href='showcontact.php?CID=$CID'>Click Here to View Contact</a><hr>";
			}
			else
			{
				echo "ERROR! Contact not updated.<br>";
			}
			
		}
		else echo "Error adding contact to database.<hr>";
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
