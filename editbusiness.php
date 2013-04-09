<?php
// GET
// sort = 'num' - by member number
// showmembers.php

   include('funcs.inc');
   include('member.inc');
   include('header.php');
   include_once('Business.inc');
   include_once('Contact.inc');
   include_once('Address.inc');
   
   if($_POST['submit'] == 'Update')
	{
		$tmpBID = $_GET['BID'];
		$thisBusiness = new Business($tmpBID);
		$name = cleanString($_POST['name']);
		$taxexempt = extractNums($_POST['taxexempt']);
		$street = cleanString($_POST['street']);
		$city = cleanString($_POST['city']);
		$state = cleanString($_POST['state']);
		$zip = extractNums($_POST['zip']);
		$thisAddress = new Address($_POST['street'], $_POST['city'], $_POST['state'], $_POST['zip']);
		$phonenum = buildPhoneNum($_POST['phonenum']);
		
		$thisBusiness->setName($name, false);
		$thisBusiness->setContactCID($_POST['newContact']);
		$thisBusiness->setTaxExempt($taxexempt, false);
		$thisBusiness->setAddress($thisAddress, false);
		$thisBusiness->setPhone($phonenum, false);
		$thisBusiness = $thisBusiness->pushUpdate();
		if(is_object($thisBusiness))
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

	$BID = $_GET['BID'];
	$thisBusiness = (object) null;
	if($_GET['BID'])
	{
		$thisBusiness = new Business($BID);
	}
	else
	{
		die("No business chosen.<hr>");
	}

	echo "<form method='post'><table>";
	echo "<tr><td width=250>Company Name:</td><td width=500><input type='text' name='name' value='".$thisBusiness->getName()."'/></td></tr>";
	$busCont = $thisBusiness->getContact();
	echo "<tr><td>Company Contact:</td><td>";
	selectCustomer('newContact', $busCont->getID());
	echo "</td></tr>";
	echo "<tr><td>Phone Number:</td><td><input type='text' name='phonenum' value='".$thisBusiness->getPhoneNum()."'/></td></tr>";
	$busAddress = $thisBusiness->getAddress();
	echo "<tr><td>Street Address:</td><td><input type='text' name='street' value='".$busAddress->getStreetAddress()."'/></td></tr>";
	echo "<tr><td>City:</td><td><input type='text' name='city' value='".$busAddress->getCity()."'/></td></tr>";
	echo "<tr><td>State:</td><td><input type='text' name='state' value='".$busAddress->getState()."'/></td></tr>";
	echo "<tr><td>Zip Code:</td><td><input type='text' name='zip' value='".$busAddress->getZip()."'/></td></tr>";
	echo "<tr><td>Tax Exempt Number:</td><td><input type='text' name='taxexempt' value='".$thisBusiness->getTaxExempt()."'/></td></tr>";
	echo "</table><input type='submit' name='submit' value='Update'>
		</form>";
	$BID = $_GET['BID'];
	echo $thisBusiness->getLink("Business Overview");
	echo "<hr>";
   
	if($_POST['submit'] == 'Add Note')
	{
		extract($_POST);
		addNote($newNote, 'BID', $_GET['BID']); 
	}
	displayNotes('BID', $_GET['BID']);
	
	include ('footer.php');
?>
