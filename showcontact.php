<?php
// GET
// sort = 'num' - by member number
// showmembers.php

   include('funcs.inc');
   include('member.inc');
   include('header.php');
   include_once('Contact.inc');
   $cxn = open_stream();
   
   echo "<h2>Customer Info</h2>
         <hr>";

	$CID = $_GET['CID'];
	$thisContact = (object) null;
	if($_GET['CID'])
	{
		$thisContact = new Contact($CID);
	}
	else
	{
		die("No customer chosen.<hr>");
	}

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
					<td width=250>Phone</td>
					<td width=250>Problem</td>
					<td width=250>Status</td>
					<td width=500>Notes</td>
				</tr>";
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

	if($_POST['submit'] == 'Add Note')
	{
		extract($_POST);
		addNote($newNote, 'CID', $_GET['CID']); 
	}
	
	displayNotes('CID', $CID);
	
	include ('footer.php');
?>