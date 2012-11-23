<?php
// GET
// sort = 'num' - by member number
// showmembers.php

   include('funcs.inc');
   include('member.inc');
   //include('header.php');
   $cxn = open_stream();
   
   $sql = "SELECT TID from Tasks where TID='0'";
	if($_GET['TID'])
	{
		extract($_GET);
		$sql = "SELECT * FROM Tasks WHERE TID='$TID'";
	}
	else
	{
		die("No task chosen.<hr>");
	}

   $result = query($cxn, $sql);
   if($row = mysqli_fetch_assoc($result))
   {
      extract($row);
   
   echo "<table>
   <tr>
		<td rowspan=3 height=100 width=100><img width=100 src='./data/logo.png' ALT='MRRU Logo' /></td>
		<td align='center'>Mobile Rapid Response Unit</td></tr>
   <tr>
		<td align='center'>Case File</td></tr>
   <tr><td>
		<table>
		<td width=100 align='left'>File #" . $_GET['TID'] . "</td>
		<td>Lead Agent: ";
			printAgent($_SESSION['ID'], 1);
echo 	"</td>
		</table>
   </td></tr>
   </table>
         <hr>";
         
		//createdOn status dueDate notes
      echo "<table>";
      echo "<tr><td width=250>Phone Model:</td><td width=500>$phone</td></tr>";
      echo "<tr><td>Problem Summary:</td><td>$problem</td></tr>";
      echo "<tr><td>Assigned Agent:</td><td>";
			printAgent($assignedTo, 1);
      echo "</td></tr>";
      if ($customerType == 'Business')
      {
		  echo "<tr><td>Hiring Company:</td><td>
				<table><td width=200>";
		  printBusiness($customerID);
		  echo "</td><td>";
		  $business = fetchBusiness($customerID, 'phonenum, contactCID');
		  echo $business['phonenum'];
		  echo "</td></table>
				</td></tr>";
		  echo "<tr><td>Company Contact:</td><td>
				<table><td width=200>";
		  printBusinessContact($customerID, 2);
		  echo "</td><td>";
		  $businessContact = fetchContact($business['contactCID'], 'phonenum');
		  echo $businessContact['phonenum'];
		  echo "</td></table></td></tr>";
	  }
	  elseif ($customerType == 'Individual')
	  {
		  echo "<tr><td>Customer:</td><td>
				<table><td width=200>";
		  printCustomer($customerID, 2);
		  echo "</td><td>";
		  $contactInfo = fetchContact($customerID, 'phonenum');
		  echo $contactInfo['phonenum'];
		  echo "</td></table>";
		  echo "</td></tr>";
	  }
	  else
	  {
		  echo "<tr><td>Customer:</td><td>ERROR: Customer Type Incorrect</td></tr>";
	  }
      echo "<tr><td>Created On:</td><td>$createdOn</td></tr>";
      echo "<tr><td>Status:</td><td>$status</td></tr>";
      echo "<tr><td>Due Date:</td><td>$dueDate</td></tr>";
      echo "<tr><td>Description:</td><td>$notes</td></tr>";
      echo "</table>";
      $TID = $_GET['TID'];
      //echo "<a href='edittask.php?TID=$TID'>Edit Task</a>";
   }
   /*
	if($_POST['submit'] == 'Add Note')
	{
		extract($_POST);
		addNote($newNote, 'TID', $_GET['TID']); 
	}
	* */
	if(!$_GET['CustomerCopy'])
	{
		displayNotes('TID', $_GET['TID'], false);
	}
	else echo "<hr>";
	//include ('footer.php');
?>
