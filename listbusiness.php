<?php
// GET
// sort = 'num' - by member number
// showmembers.php

   include('funcs.inc');
   include('member.inc');
   include('header.php');
   $cxn = open_stream();
   
   //GET Variables may include:
   // sort - determines which field to sort, appended d to denote ascending order
   // search - string to restrict the queryspace, performs a full textsearch of name
   
   echo "<h2>Company List</h2>
         <hr>";

	$search = " ";
	$sort = " ORDER BY BID DESC";
	
	if($_GET['sort'] == 'name')
	{
		$sort = " ORDER BY name";
	}
	
	if($_GET['search'])
	{
		$search = " WHERE name LIKE '%" . $_GET['search'] . "%'";
	}
	
	$sql = "SELECT BID, contactCID, phonenum FROM Businesses";
	$sql .= $search;
	$sql .= $sort;
	echo "<table><tr><td><form method='get'>
			<input type='text' name='search' value='" . $_GET['search'] . "'>
			<input type='hidden' name='sort' value='" . $_GET['sort'] . "'>
			<input type='submit' value='Search'>
		  </form></td></tr></table>";
   $result = query($cxn, $sql);
   echo "<table cellpadding=1 border><tr>";
   echo "<td>BID</td>";
   echo "<td>Name</td>";
   echo "<td>Contact</td>";
   echo "<td>Phone</td>";
   echo "</tr>";
   while($row = mysqli_fetch_assoc($result))
   {
      extract($row);
      echo "<tr><td>$BID</td><td>";
      printBusinessLink($BID);
      echo "</td><td>";
      printCustomerLink($contactCID, 2);
      echo "</td><td>$phonenum</td>";
      echo "</tr>";
   }
   echo "</table>";

	include ('footer.php');
?>
