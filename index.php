<?php
// index.php
// This is the main page for all users.
// Everyone sees:
// Nothing right now... Trying to retain the two column format for future use, though.

   $title = "Fri3nd Compvter Master P@ge";
   include('funcs.inc');
   include('friendcomputer.inc');
   include('member.inc');
   include('header.php');
   $cxn = open_stream();

   $SID = $_SESSION['ID'];
   
   // check for GET data for a logout
   if($_GET['logout'] == 1)
   {
      foreach($_SESSION as $key => $value)
      {
         unset($_SESSION[$key]);
      }

      die ("<h1>Login</h1>
           <hr>
           <form action='index.php' method='post'>
           Username: <input type='text' name='username'><p>
           Password: <input type='password' name='password'><p>
           <input type='submit' name='submit' value='Login'>
           </form><p>");

   }
   
   if($_POST['submit'] == 'Update')
   {
	  $maxRow = $_POST['maxRow'];
	  echo "<br>Updating Open Tasks";
      for ($i = 0; $i < $maxRow; $i++)
      {
		  $statusname = "status_" . $i;
		  $IDname = "rowID_" . $i;
		  $sql = "UPDATE Tasks SET status='" . $_POST[$statusname] . "' WHERE TID=" . $_POST[$IDname];
		  $result = query($cxn, $sql);
	  }
   }
   
   // get info to display current time
   date_default_timezone_set('America/New_York');
   
   $date = date("l, F jS, o g:i A");
   $time = localtime();

   fcMessage($message);


	   echo "<h2>Open Tasks</h2>";
	
		$sql = "SELECT * FROM Tasks WHERE (assignedTo='$SID' AND status!='Completed') ORDER BY dueDate";
	
	   $result = query($cxn, $sql);
	   echo "<form method='post'>";
	   echo "<table><tr>";
	   echo "<th width=300>Phone Model</th>";
	   echo "<th width=300>Problem</th>";
	   echo "<th width=300>Customer</th>";
	   echo "<th width=300>Status</th>";
	   echo "<th width=150>Due Date</th>";
	   echo "</tr>";
	   
	   
	   $rowcount = 0;
	   while($row = mysqli_fetch_assoc($result))
	   {
		  extract($row);
		  
		  echo "<tr><td>$phone</td><td>";
		  printTaskLink($TID, 2);
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
			  echo "ERROR: Incorrect Customer Type";
		  }
		  
		  $selectName = "status_" . $rowcount;
		  echo "</td><td><input type='hidden' name='rowID_$rowcount' value=$TID>";
		  selectStatus($selectName, $status);
		  echo "</td><td>$dueDate</td>";
		  echo "</tr>";
		  $rowcount++;
	   }
	   echo "</table>";
	   echo "<input type='hidden' name='maxRow' value=$rowcount><input type='submit' name='submit' value='Update'>";
	   echo "</form>";
	   


   $version="0.1alpha";
   include ('footer.php');
?>
