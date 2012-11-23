<?php
// GET
// sort = 'num' - by member number
// showmembers.php

   include('funcs.inc');
   include('member.inc');
   include('header.php');
   require('customisation.inc');
   global $CUS_Category_Name;
   global $CUS_Assigned_Employee;
   global $CUS_Task_Summary;
   $cxn = open_stream();
   
   if($_POST['submit'] == 'Update')
	{
		extract($_POST);
		$tmpTID = $_GET['TID'];
		$phone = cleanString($phone);
		$problem = cleanString($problem);
		$notes = cleanString($notes);
		if($custType == 'Individual')
		{
			$sql = "UPDATE Tasks SET customerID='$customerInd', customerType='Individual', assignedTo='$assignedAgent', dueDate='$dueDate', phone='$phone', problem='$problem', status='$status', notes='$notes' WHERE TID=$tmpTID";
		}
		elseif($custType == 'Business')
		{
			$sql = "UPDATE Tasks SET customerID='$customerBus', customerType='Business', assignedTo='$assignedAgent', dueDate='$dueDate', phone='$phone', problem='$problem', status='$status', notes='$notes' WHERE TID=$tmpTID";
		}
		else
		{
			die("ERROR! Incorrect Customer Type Selected");
		}
		
		if($result = query($cxn, $sql))
		{
			echo "Entry Updated. Go ahead, check it below!<br>";
		}
		else
		{
			echo "ERROR! Entry not Updated.<br>";
		}
	}
	
	echo "<script type='text/javascript'>
			 function initForm(activeCust){
				 if(activeCust=='Individual'){
					document.forms['eTask']['customerInd'].disabled=false;
					document.forms['eTask']['customerBus'].disabled=true;
				 }
				 else{
					document.forms['eTask']['customerInd'].disabled=true;
					document.forms['eTask']['customerBus'].disabled=false;
				 }
			 }
			 
			 function selectRadio(n){ 
			 if(n==1){
				document.forms['eTask']['customerInd'].disabled=false;
				document.forms['eTask']['customerBus'].disabled=true;
			 }
			 if(n==2){
				document.forms['eTask']['customerInd'].disabled=true;
				document.forms['eTask']['customerBus'].disabled=false;
			 }
			 } 
			 </script>";
			 
	echo "<h2>Task Info</h2><hr>";

	if($_GET['TID'])
	{
		extract($_GET);
		$sqlCompanyInfo = "SELECT * FROM Tasks WHERE TID='$TID'";
	}
	else
	{
		die("No task chosen.<hr>");
	}

   $result = query($cxn, $sqlCompanyInfo);
   if($row = mysqli_fetch_assoc($result))
   {
      extract($row);

      echo "<form name='eTask' method='post'>";
      echo "<table>";
      echo "<tr><td width=250>$CUS_Category_Name:</td><td width=500><input type='text' name='phone' value='$phone' /></td></tr>";
      echo "<tr><td>$CUS_Task_Summary:</td><td><input type='text' name='problem' value='$problem' /></td></tr>";
      echo "<tr><td>$CUS_Assigned_Employee:</td><td>";
		selectAgent('assignedAgent', $assignedTo);
	  echo "</td></tr>";
      echo "<tr><td>Customer Type:</td><td>";
      if ($customerType == 'Individual')
      {
	  echo "Individual <input type='radio' name='custType' value='Individual' onclick='selectRadio(1)' checked />
			Business <input type='radio' name='custType' value='Business' onclick='selectRadio(2)' /></td></tr><br>
			<tr><td>Customer:</td><td>";
			selectCustomer('customerInd', $customerID);
			selectBusiness('customerBus', -1);
	  }
	  elseif ($customerType == 'Business')
	  {
	  echo "Individual <input type='radio' name='custType' value='Individual' onclick='selectRadio(1)' />
			Business <input type='radio' name='custType' value='Business' onclick='selectRadio(2)' checked /></td></tr><br>
			<tr><td>Customer:</td><td>";
			selectCustomer('customerInd', -1);
			selectBusiness('customerBus', $customerID);
	  }
	  else
	  {
		  die("ERROR! Invalid Customer Type!");
	  }
      echo "</td></tr><br>";
      echo "<tr><td>Status:</td><td>";
      selectStatus('status', $status);
      echo "</td></tr>";
      echo "<tr><td>Due Date:</td>";
      echo "<td>";
      selectDate('dueDate', $dueDate);
      echo "</td></tr>";
      echo "<tr><td>Description:</td><td><textarea rows='3' cols='25' name='notes'>$notes</textarea></td></tr>";
      echo "</table><input type='submit' name='submit' value='Update'></form>";
	  echo "<script type='text/javascript'>initForm('$customerType');</script>";
	  $TID = $_GET['TID'];
      echo "<a href='showtask.php?TID=$TID'>Task Overview</a>";
   }
   
	if($_POST['submit'] == 'Add Note')
	{
		extract($_POST);
		addNote($newNote, 'TID', $_GET['TID']); 
	}
	displayNotes('TID', $_GET['TID']);
	
	include ('footer.php');
?>
