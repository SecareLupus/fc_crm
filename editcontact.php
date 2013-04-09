<?php
// GET
// sort = 'num' - by member number
// showmembers.php

   include('funcs.inc');
   include('member.inc');
   include('header.php');
   include_once('Contact.inc');
   
	if($_POST['submit'] == 'Update')
	{
		extract($_POST);
		$tmpCID = $_GET['CID'];
		$thisContact = new Contact($tmpCID);
		$firstname = cleanString($firstname);
		$lastname = cleanString($lastname);
		if (!check_email_address($emailadd)) die("Invalid Email Address");
		$howfound = cleanString($howfound);
		$phonenum = phonenumFromString($phonenum);
		
		$thisContact->setFName($firstname, false);
		$thisContact->setLName($lastname, false);
		$thisContact->setEmail($emailadd, false);
		$thisContact->setHowFound($howfound, false);
		$thisContact->setPhone($phonenum, false);
		$thisContact = $thisContact->pushUpdate();
		if(is_object($thisContact))
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

	$CID = $_GET['CID'];
	$thisContact = (object) null;
	if($_GET['CID'])
	{
		$thisContact = new Contact($CID);
	}
	else
	{
		die("No contact chosen.<hr>");
	}

	//howfound	createdOn
	echo "<form method='post'><table>";
	echo "<tr><td width=250>Contact Name:</td><td width=500>
		<input type='text' name='firstname' size=25 maxlength=50 value='".$thisContact->getFName()."' /><br><input type='text' name='lastname' size=25 maxlength=50 value='".$thisContact->getLName()."' /></td></tr>";
	echo "<tr><td>Phone Number:</td><td><input type='text' name='phonenum' size=25 maxlength=20 value='".$thisContact->getPhoneNum()."' /></td></tr>";
	echo "<tr><td>Email Address:</td><td><input type='text' name='emailadd' size=25 maxlength=150 value='".$thisContact->getEmail()."' /></td></tr>";
	echo "<tr><td>How Found:</td><td><textarea rows='3' cols='25' name='howfound'>".$thisContact->getHowFound()."</textarea></td></tr>";
	echo "</table><input type='submit' name='submit' value='Update'></form>";
	$CID = $_GET['CID'];
	echo "<a href='showcontact.php?CID=$CID'>Contact Overview</a>";
	echo "<hr>";

	if($_POST['submit'] == 'Add Note')
	{
		extract($_POST);
		addNote($newNote, 'CID', $_GET['CID']); 
	}
	displayNotes('CID', $_GET['CID']);
	
	include ('footer.php');
?>
