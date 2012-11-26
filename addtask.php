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
   
   if($_POST['submit'] == 'submit')
   {
	   extract($_POST);
	   
	   $phonemod = cleanString($phonemod);
	   $problem = cleanString($problem);
	   $notes = cleanString($notes);
	   
		$cxn = open_stream();
		
		if($custType == 'Individual')
		{
			$sql = "INSERT INTO Tasks (customerID, customerType, createdOn, assignedTo, dueDate, phone, problem, status, notes)
					VALUES ('$customerInd', 'Individual', NOW(), '$assignedAgent', '$dueDate', '$phonemod', '$problem', '$status', '$notes')";   
		}
		elseif($custType == 'Business')
		{
			$sql = "INSERT INTO Tasks (customerID, customerType, createdOn, assignedTo, dueDate, phone, problem, status, notes)
					VALUES ('$customerBus', 'Business', NOW(), '$assignedAgent', '$dueDate', '$phonemod', '$problem', '$status', '$notes')";
		}
		if($result = query($cxn, $sql))
		{
			$sql = "SELECT TID from Tasks ORDER BY TID DESC LIMIT 1";
			$result = query($cxn, $sql);
			echo "New task added to the database.<br>";
			if ($row = mysqli_fetch_assoc($result))
			{
				extract($row);
				echo "<a href='http://mrru.us/CRM/showtask.php?TID=$TID'>Click Here to View Task</a><hr>";
			}
			else
			{
				echo "No link available<hr>";
			}
		}
		else echo "Error adding task to database.<hr>";
   }
echo "<script type='text/javascript'>
	 function initForm(){
		document.forms['aTask']['customerInd'].disabled=false;
		document.forms['aTask']['customerBus'].disabled=true;
	 }
	 
	 function selectRadio(n){ 
	 if(n==0){
		document.forms['aTask']['customerInd'].disabled=false;
		document.forms['aTask']['customerBus'].disabled=true;
		document.forms['aTask']['custType[0]'].checked=true;
		document.forms['aTask']['custType[1]'].checked=false;
	 }
	 if(n==1){
		document.forms['aTask']['customerInd'].disabled=true;
		document.forms['aTask']['customerBus'].disabled=false;
		document.forms['aTask']['custType[0]'].checked=false;
		document.forms['aTask']['custType[1]'].checked=true;
	 }
	 } 
	 </script>";

  echo "<h2>Add Task</h2><hr>
		<form name='aTask' method='post'>
		<table>
		<tr><td width=250>$CUS_Category_Name:</td><td width=500><input type='text' name='phonemod' size=25 maxlength=50></td></tr>
		<tr><td>$CUS_Task_Summary:</td><td><input type='text' name='problem' size=25 maxlength=80></td></tr>
		<tr><td>$CUS_Assigned_Employee:</td><td>";
		selectAgent('assignedAgent', $_SESSION['ID']);
  echo "</td></tr>
        <tr><td>Customer Type:</td><td>
        Individual <input type='radio' name='custType' value='Individual' onclick='selectRadio(0)' checked>
        Business <input type='radio' name='custType' value='Business' onclick='selectRadio(1)'>
        </td></tr>
        <tr><td>Customer:</td><td>";
        selectCustomer('customerInd', (($_GET['cType'] == 'I' && !empty($_GET['custID'])) ? $_GET['custID'] : -1));
        echo "<br>";
        selectBusiness('customerBus', (($_GET['cType'] == 'B' && !empty($_GET['custID'])) ? $_GET['custID'] : -1));
        echo "<script type='text/javascript'>initForm();</script></td></tr>";
   echo "<tr><td>Status:</td><td>";
   selectStatus('status', $status);
   echo "</td></tr>
		<tr><td>Due Date:</td><td>";
   selectDate('dueDate', $dueDate);
   echo "</td></tr>
		<tr><td>Description:</td><td><textarea rows='3' cols='25' name='notes'></textarea></td></tr>
		</table>
        <input type='submit' name='submit' value='submit'></form>";
        
	if($_GET['cType'] == 'I')
	{
		echo "<script type='text/javascript'>
			  document.forms['aTask']['customerInd'].disabled=false;
			  document.forms['aTask']['customerBus'].disabled=true;
			  document.forms['aTask']['custType[0]'].checked=true;
			  document.forms['aTask']['custType[1]'].checked=false;
			  </script>";
	}
	elseif($_GET['cType'] == 'B')
	{
		echo "<script type='text/javascript'>
			  document.forms['aTask']['customerInd'].disabled=true;
			  document.forms['aTask']['customerBus'].disabled=false;
			  document.forms['aTask']['custType[0]'].checked=false;
			  document.forms['aTask']['custType[1]'].checked=true;
			  </script>";
	}
        
	include ('footer.php');
?>
