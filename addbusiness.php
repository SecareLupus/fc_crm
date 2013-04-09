<?php
// GET
// sort = 'num' - by member number
// showmembers.php

   include('funcs.inc');
   include('member.inc');
   include('header.php');
   include_once('Business.inc');
   include_once('Address.inc');
   
   if($_POST['submit'] == 'submit')
   {
	   extract($_POST);
	   
	   $name = cleanString($name);
	   $phone = phonenumFromString($phone);
	   $street = cleanString($street);
	   $city = cleanString($city);
	   $state = cleanString($state);
	   $zip = extractNums($zip);
	   $address = new Address($street, $city, $state, $zip);
	   $taxexempt = extractNums($taxexempt);
	   
	   if (checkName($name) && checkAlphaNum($state) && checkAlphaNum($zip))
	   {
		$thisBusiness = Business::createBusiness();
		if(is_object($thisBusiness))
		{
			$thisBusiness->setName($name, false);
			$thisBusiness->setContactCID($contactCID, false);
			$thisBusiness->setPhone($phone, false);
			$thisBusiness->setAddress($address, false);
			$thisBusiness->setTaxExempt($taxexempt, false);
			$thisBusiness = $thisBusiness->pushUpdate();
			if(is_object($thisBusiness))
			{
				echo $thisBusiness->getName() . " added to the database.<br>";
				$BID = $thisBusiness->getID();
				echo "<a href='showbusiness.php?BID=$BID'>Click Here to View Business</a><hr>";
			}
			else echo "Error updating business from default.<hr>";
		}
		else echo "Error adding business to database.<hr>";
	   }
	   else echo "Error with illegal input.<hr>";
   }

   echo "<h2>Add Business</h2><hr>
		<form method='post'>
		<table>
		<tr><td width=250>Company Name:</td><td width=500><input type='text' name='name' size=25 maxlength=50></td></tr>
        <tr><td>Company Contact:</td><td>";
        selectCustomer('contactCID', -1);
        echo "</td></tr>
        <tr><td>Phone Number:</td><td><input type='text' name='phone' size=25 maxlength=20></td></tr>
		<tr><td>Street Address:</td><td><input type='text' name='street' size=25 maxlength=150></td></tr>
        <tr><td>City:</td><td><input type='text' name='city' size=25 maxlength=50></td></tr>
        <tr><td>State:</td><td><input type='text' name='state' size=4 maxlength=2></td></tr>
        <tr><td>Zip Code:</td><td><input type='text' name='zip' size=25 maxlength=10></td></tr>
        <tr><td>Tax Exempt Number:</td><td><input type='text' name='taxexempt' size=25 maxlength=15></td></tr>
        </table>
        <input type='submit' name='submit' value='submit'></form>";
        
        include ('footer.php');
?>
