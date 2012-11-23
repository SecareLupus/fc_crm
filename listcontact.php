<?php
// GET
// sort = 'num' - by member number
// showmembers.php

   include('funcs.inc');
   include('member.inc');
   include('header.php');
   $cxn = open_stream();
   
   echo "<h2>Customer List</h2>
         <hr>";

	$search = " ";
	$sort = " ORDER BY CID DESC";
	
	if($_GET['sort'] == 'fname')
	{
		$sort = " ORDER BY fname";
	}
	elseif($_GET['sort'] == 'lname')
	{
		$sort = " ORDER BY lname";
	}
	
	if($_GET['search'])
	{
		$search = " WHERE (fname LIKE '%" . $_GET['search'] . "%' OR lname LIKE '%" . $_GET['search'] . "%')";
	}
	
	$sql = "SELECT CID, fname, lname, email, phonenum FROM Customers";
	$sql .= $search;
	$sql .= $sort;
	echo "<table><tr><td><form method='get'>
			<input type='text' name='search' value='" . $_GET['search'] . "'>
			<input type='hidden' name='sort' value='" . $_GET['sort'] . "'>
			<input type='submit' value='Search'>
		  </form></td></tr></table>";
   $result = query($cxn, $sql);
   echo "<table cellpadding=1 border><tr>";
   echo "<td>CID</td>";
   echo "<td>Name</td>";
   echo "<td>Email</td>";
   echo "<td>Phone</td>";
   echo "</tr>";
   while($row = mysqli_fetch_assoc($result))
   {
      extract($row);
      echo "<tr><td>$CID</td><td><a href='showcontact.php?CID=$CID'>$lname, $fname</a></td><td>$email</td><td>$phonenum</td>";
      echo "</tr>";
   }
   echo "</table>";
   
   include ('footer.php');
?>
