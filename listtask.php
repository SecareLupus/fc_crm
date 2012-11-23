<?php
// GET
// sort = 'num' - by member number
// showmembers.php

   include('funcs.inc');
   include('member.inc');
   include('header.php');
   require('customisation.inc');
   global $CUS_Assigned_Employee;
   global $CUS_Category_Name;
   global $CUS_Task_Short;
   global $CUS_Customer_Title;
   $cxn = open_stream();
   
	   echo "<h2>Task List</h2>
			 <hr>";
	
		$search = " ";
		$sort = " ORDER BY TID DESC";
		
		if($_GET['sort'] == 'dueDate')
		{
			$sort = " ORDER BY dueDate";
		}
		elseif($_GET['sort'] == 'status')
		{
			$sort = " ORDER BY dueDate";
		}
		
		if($_GET['search'])
		{
			$search = " WHERE (phone LIKE '%" . $_GET['search'] . "%' OR problem LIKE '%" . $_GET['search'] . "%')";
		}
		
		$sql = "SELECT TID, phone, assignedTo, customerType, customerID, status FROM Tasks";
		$sql .= $search;
		$sql .= $sort;
		echo "<table><tr><td><form method='get'>
				<input type='text' name='search' value='" . $_GET['search'] . "'>
				<input type='hidden' name='sort' value='" . $_GET['sort'] . "'>
				<input type='submit' value='Search'>
			  </form></td></tr></table>";
	   $result = query($cxn, $sql);
	   echo "<table cellpadding=1 border><tr>";
	   echo "<th>TID</th>";
	   echo "<th>$CUS_Category_Name</th>";
	   echo "<th>$CUS_Task_Short</th>";
	   echo "<th>$CUS_Assigned_Employee</th>";
	   echo "<th>$CUS_Customer_Title</th>";
	   echo "<th>Ind/Bus</th>";
	   echo "<th>Status</th>";
	   echo "</tr>";
	   while($row = mysqli_fetch_assoc($result))
	   {
		  extract($row);
		  
		  echo "<tr><td>$TID</td><td>$phone</td><td>";
			  printTaskLink($TID, 2);
		  echo "</td><td>";
			  printAgent($assignedTo, 1);
		  echo "</td><td>";
		  if ($customerType == "Individual")
		  {
			  printCustomerLink($customerID, 2);
		  }
		  elseif ($customerType == "Business")
		  {
			  printBusinessLink($customerID);
		  }
		  else
		  {
			  echo "<tr>ERROR: Incorrect Customer Type<br>";
		  }
		  
		  echo "</td><td>$customerType</td><td>$status</td>";
		  echo "</tr>";
	   }
	   echo "</table>";

	include ('footer.php');
?>
