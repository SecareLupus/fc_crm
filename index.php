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
	include('masterpagewidgets.inc');
	include_once('Employee.inc');
	include_once('Task.inc');
	require('customisation.inc');
	global $CUS_Category_Name;
	global $CUS_Task_Short;
	global $CUS_Customer_Title;
	$cxn = open_stream();

	$SID = $_SESSION['ID'];
	$SEmployee = new Employee($SID);

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
		  $maxStatus = $_POST['maxStatus'];
		  echo "<br>Updating Open Tasks";
		  for ($i = 0; $i < $maxStatus; $i++)
		  {
			  $statusname = "status_" . $i;
			  $IDname = "statusID_" . $i;
			  $currTask = new Task($_POST[$IDname]);
			  $currTask->setStatus($_POST[$statusname]);
		  }
	}
	
	if($_POST['submit'] == 'Pay')
	{
		  $maxInvoice = $_POST['maxInvoice'];
		  echo "<br>Marking Invoices Paid";
		  for ($i = 0; $i < $maxInvoice; $i++)
		  {
			  $statusname = "checkpaid_" . $i;
			  $IDname = "checkID_" . $i;
			  $currInvoice = new Invoice($_POST[$IDname]);
			  if($_POST[$statusname])
			  {
				$currInvoice->setPaid();
			  }
		  }
	}

	// get info to display current time
	date_default_timezone_set('America/New_York');

	$date = date("l, F jS, o g:i A");
	$time = localtime();

	fcMessage($message);

	mpwOpenTasks($SEmployee);
	mpwUnpaidInvoices($SEmployee);
	
	include ('footer.php');
?>
