<?php
session_start();

include('config.inc'); // include specific info for specific database

   if(!$_SESSION['loggedin'])
   {
      if(login($_POST['username'], $_POST['password']))
      {
         $_SESSION['loggedin'] = TRUE;
         $_SESSION['lastActive'] = mktime();
      }
      else
      {
         displayLogin();
         include('footer.php');
         die();
      }
   }

$ADMINEMAIL = "info@mrru.us";

$IMAGEROOT = "http://www.worldsapartgames.org/productimages/";

// megaStrip
// strips slashes from GET, POST, and COOKIE
function megaStrip()
{
   $_POST = ((function_exists("get_magic_quotes_gpc") &&  
   get_magic_quotes_gpc()) || (ini_get('magic_quotes_sybase') &&  
   (strtolower(ini_get('magic_quotes_sybase'))!="off")))
                                    ? stripslashes_deep($_POST)
                                    : $_POST;
}
function stripslashes_deep($value)
{
        $value = is_array($value) ?
                array_map('stripslashes_deep', $value) :
                stripslashes($value);
        return $value;
}

// checkAlphaNum
// takes string and returns TRUE if it only has alphanumeric characters
function checkAlphaNum($string)
{
   return (eregi("^[a-z0-9]*$", $string));
}

// checkAlphaNumSpace
// takes string and returns TRUE if it only has alphanumeric characters and spaces
function checkAlphaNumSpace($string)
{
   return (eregi("^[a-z0-9 ]*$", $string));
}


// checkDateNum
// takes a string and returns TRUE if it is a date in the form YYYY-MM-DD
function checkDateNum($string)
{
   return (ereg("^[0-9]{1,4}\-[0-9]{1,2}\-[0-9]{1,2}$", $string));
}

function check_email_address($email) {
 // First, we check that there's one @ symbol, and that the lengths are right
 if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
 // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
 return false;
 }

 // Split it into sections to make life easier
 $email_array = explode("@", $email);
 $local_array = explode(".", $email_array[0]);
 for ($i = 0; $i < sizeof($local_array); $i++) {
 if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
 return false;
 }
 }
 if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
 $domain_array = explode(".", $email_array[1]);
 if (sizeof($domain_array) < 2) {
 return false; // Not enough parts to domain
 }
 for ($i = 0; $i < sizeof($domain_array); $i++) {
 if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
 return false;
 }
 }
 }
 return true;
 }

// checkName
// takes a string and returns TRUE if it is consistent with the characters in a name
// WARNING!!! LEAVES APOSTROPHIES IN STRING
function checkName($name)
{
   return (ereg("^([A-Za-z0-9/\,\.\'\& ]|-)*$", $name));
}

// dateDiff
// args: dformat = delimter
//	 endDate, beginDate = dates to compare in form YY-MM-DD
// returns: difference in days
function dateDiff($dformat, $beginDate, $endDate)
{
$date_parts1=explode($dformat, $beginDate);
$date_parts2=explode($dformat, $endDate);
$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
return $end_date - $start_date;
}

// dateSame
// args: each var can be a date object or a properly formed date string
// returns true if they are the same, comparing only year, month day, FALSE if different
function dateSame($date1, $date2)
{
   if(isstring($date1))
   {
      $date1 = date_create($date1);
   }
   if(isstring($date2))
   {
      $date2 = date_create($date2);
   }
   
   $a1 = date_parse($date1);
   $a2 = date_parse($date2);
   
   return( ($a1['year'] == $a2['year'])
        && ($a1['month'] == $a2['month'])
        && ($a1['day'] == $a2['day']));
}

// dateSplit
// args: datestr - a string of a date YYYY-MM-DD
// returns: array of the string 0-Y, 1-M, 2-D
function dateSplit($datestr)
{
   $newdate[0] = substr($datestr, 0, 4);
   $newdate[1] = substr($datestr, 5, 2);
   $newdate[2] = substr($datestr, 8, 2);
}

// dateString
// returns a string of the current date modified by the string as appropriate for date_modify
function dateString($mod)
{
   $date = date_create();
   $date->modify($mod);
   return date_format($date, "Y-m-d");
}

// dateStringVar
// returns Y-m-d of the given string
function dateStringVar($when)
{
   $date = date_create($when);
   return $date->format("Y-m-d");
}

// dayToNum
// returns an integer between 1 and 7 inclusively,
// corresponding to the days of the week,
// from Sunday to Saturday, respectively.
function dayToNum($day)
{
   switch($day) {
	case "Sunday":
		return 1;
		break;
	case "Monday":
		return 2;
		break;
	case "Tuesday":
		return 3;
		break;
	case "Wednesday":
		return 4;
		break;
	case "Thursday":
		return 5;
		break;
	case "Friday":
		return 6;
		break;
	case "Saturday":
		return 7;
		break;
	}
}

// displayRefs
// args: start - starting date
//       ID - user ID
// displays referral rewards
function displayRefs($start, $ID)
{
   $cxn = open_stream();
   $sql = "SELECT * FROM storeAccount WHERE memberID='$ID' AND notes LIKE 'REF%' AND whenAcct >= $start";
   $result = query($cxn, $sql);
   
   $refs = FALSE;
   while($row = mysqli_fetch_assoc($result))
   {
      $refs = TRUE;
      extract($row);
      $refID = substr($notes, 4, 10);
      $refID = intval($refID);
      $refBucks[$refID] += $amount;
      $lastPurchase[$refID] = $whenAcct;
   }
   
   if($refs == FALSE)
   {
      echo "<table border cellpadding=3><tr><td>No Referral bonuses since $start</td></tr></table>";
      return (FALSE);
   }
   else
   {
      foreach($refBucks as $key => $value)
      {
         $name[$key] = printMemberString($key,1);
      }
      ksort($name);
   
      // display stuff
      echo "Since $start<br>
      <table border cellpadding=3><tr><td>Referred Customer</td><td>Reward</td><td>Last Purchase</td></tr>\n";
      foreach($name as $key => $value)
      {
         echo "<tr><td>$value</td><td>" . money($refBucks[$key]) . "</td><td>{$lastPurchase[$refID]}</td></tr>\n";
      }
      echo "</table>\n";
      $sum = array_sum($refBucks);
      echo "Total Referral Rewards: " . money($sum) . "<br>";
   }
   return (TRUE);
}

// displayError
// arguments: string containing error message
function displayError($message)
{
   $WEBMASTER = "webmaster@pvgaming.org";

   echo"<font color=RED>$message<br>
        Please contact the webmaster about this problem at <a href='mailto:$WEBMASTER'>$WEBMASTER</a></font><p>";
}

// displayErrorDie
// arguments: string containing error message then includes footer and dies
function displayErrorDie($message)
{
   $WEBMASTER = "webmaster@pvgaming.org";

   echo"<font color=RED>$message<br>
        Please contact the webmaster about this problem at <a href='mailto:$WEBMASTER'>$WEBMASTER</a></font><p>";
   include('footer.php');
   die();
}


function displayLogin()
{
   echo"<h1>Login</h1>
        <hr>
        <form action='" . $_SERVER['SCRIPT_NAME'] . "' method='post'>
        Username: <input type='text' name='username'><p>
        Password: <input type='password' name='password'><p>
        <input type='submit' name='submit' value='Login'>
        </form><a href='forgotpassword.php'>Forgot your password?</a><p>";
}

// displayFCMessage
// displays $message as a message from Friend Computer
function displayFCMessage($message)
{
   echo "<table border cellpadding=3><tr><td><font size=+3>Message From Friend Computer</font><p>
         $message</td></tr></table><p>\n";
}

// Returns the key at the end of the array
function endKey($array){
 end($array);
 return key($array);
}

// extractNums
// takes a string as an argument and returns only the numbers
function extractNums($input)
{
   $nums = '';

   for($i=0;$i<strlen($input);$i++)
   {
      $s = substr($input, $i, 1);
      if(ereg("^[0-9]$", $s))
      {
         $nums .= $s;
      }
   }
   return ($nums);
}

// formPhoneNumber
// takes an integer and turns it into a properly formated american phone number
function formPhoneNumber($raw)
{
   $raw = strval($raw);
	
   if(strlen($raw) == 11)
   {
      return ('(' . substr($raw, 1, 3) . ')' . substr($raw, 4, 3) . '-' . substr($raw, 7, 4));
   }
   if(strlen($raw) == 10)
   {
      return ('(' . substr($raw, 0, 3) . ')' . substr($raw, 3, 3) . '-' . substr($raw, 6, 4));
   }
   if(strlen($raw) == 7)
   {
      return (substr($raw, 0, 3) . '-' . substr($raw, 3, 4));
   }

   else return ($raw);
}

function phonenumFromString($inputstring)
{
	return formPhoneNumber(intval(extractNums(cleanString($inputstring))));
}

// greaterThanZero
// returns num if it is greater than 0, and 0 otherwise.
function greaterThanZero($num)
{
   return(($num > 0) ? $num : 0);
}

// noRefresh
// Adds the hidden form element which will be used to update SESSION in noRefreshCheck
function noRefresh()
{
   $next = $_SESSION['page'] + 1;
   
   echo "\n<input type='hidden' name='page' value='$next'>\n";
}

// noRefeshCheck
// checks to see if page is the same as the hidden value, in which case it returns false
// otherwise, sets SESSION page to POST page and returns true
function noRefreshCheck()
{
   // if it is not set, then we are not checking it
   if(!isset($_POST['page']))
   {
      $_SESSION['page'] == 0;
      return TRUE;
   }
   
   // if they are the same, it indicates a refresh
   if($_POST['page'] == $_SESSION['page']) return FALSE;
   else
   {
      $_SESSION['page'] = $_POST['page'];
      return TRUE;
   }
}

// lastDayOfMonth
// takes month as number and returns date of last day of month. Ignores leapyears
function lastDayOfMonth($month)
{
   switch($month)
   {
      case 1:
      case 3:
      case 5:
      case 7:
      case 8:
      case 10:
      case 12: return(31);
      case 4:
      case 6:
      case 9:
      case 11 : return(30);
      case 2 : return(28);
      default : return (FALSE);
   }
}

function login($username, $password)
{
   if(strlen($username) == 0)
   {
      return FALSE;
   }
   else
   {
	   list($fname, $lname) = explode(".", $username);
   }

   $stream = open_stream();
   if(strlen($password) == 0) // if there is no password, cannot log in - no accounts w/o passwords
   {
      echo "Login Failed: No Password<br>";
      return FALSE;
   }

   $pwdhash = hash('sha256',$password);
   $query = "SELECT * FROM Agents WHERE fname='$fname' AND lname='$lname' AND password='$pwdhash'";

   if($result = query($stream, $query))
   {
      if(($row = mysqli_fetch_assoc($result)) && ($row['status'] != -1))
      {
         $_SESSION['ID'] = $row['AID'];
         mysqli_free_result($result);
         mysqli_close($stream);
         unset($_POST['username']); // to make sure it does not trip up safePost
         unset($_POST['password']);
         echo "Login successful";
         return TRUE;
      }
      else
      {
         echo "Login Failed.<br>";
         echo "Username:  $fname  $lname <br>";
         echo "Password: " . $pwdhash . "<br>";
         mysqli_free_result($result);
         mysqli_close($stream);
         return FALSE;
      }
   }
}

// logout
// logs user out completely and displays login info
function logout()
{
   foreach($_SESSION as $key => $value)
   {
      unset($_SESSION[$key]);
   }

   echo "<h1>Login</h1>
        <hr>
        <form action='index.php' method='post'>
        Username: <input type='text' name='username'><p>
        Password: <input type='password' name='password'><p>
        <input type='submit' name='submit' value='Login'>
        </form><p>";
   include ('footer.php');
   exit();
}

// logoutTimeout
// logs user out but preserves POST data as hiddens
function logoutTimeout()
{
   foreach($_SESSION as $key => $value)
   {
      unset($_SESSION[$key]);
   }

   echo "<h1>Login</h1>
        <hr>
        <form action='" . $_SERVER["REQUEST_URI"] . "' method='post'>";
   if(is_array($_POST)) foreach($_POST as $key => $value)
   {
      if(is_array($value)) foreach($value as $k2 => $v2)
      {
         echo "<input type='hidden' name='$key" . "[$k2]' value='$v2'>\n";
      }
      else
         echo "<input type='hidden' name='$key' value='$value'>\n";
   }
        
   echo "Username: <input type='text' name='username'><p>
        Password: <input type='password' name='password'><p>
        <input type='submit' name='submit' value='Login'>
        </form><p>";
   include ('footer.php');
   exit();
}

// money
// returns a float as money in the form $00.00
function money($float)
{
   return "\$" . sprintf("%01.2f", $float);
}

// moneyND
// returns a float as money in the form 00.00
function moneyND($float)
{
   return sprintf("%01.2f", $float);
}


// printMember
// args: member number,
//       order - 1 for fname lname, 2 for lname, fname, 3 for fname only, 4, lname, F
// displays the name
function printAgent($num, $order)
{
   $cxn = open_stream();
   $sql = "SELECT fname, lname FROM Agents WHERE AID='$num'";
   $result = query($cxn, $sql);
   if(!$row = mysqli_fetch_assoc($result)) return FALSE;
   extract($row);
   switch($order)
   {
      case 1: echo "$fname $lname";
              break;
      case 2: echo "$lname, $fname ($num)";
              break;
      case 3: echo "$fname";
              break;
      case 4: $fname = substr($fname, 0, 1);
              echo "$lname, $fname ($num)";
              break;
   }
   return TRUE;
}

// printMemberString
// args: member number,
//       order - 1 for fname lname, 2 for lname, fname, 3 for fname only, 4, lname, F
// returns the name as a string
function printAgentString($num, $order)
{
   $cxn = open_stream();
   $sql = "SELECT fname, lname FROM Agents WHERE AID='$num'";
   $result = query($cxn, $sql);
   if(!$row = mysqli_fetch_assoc($result)) return FALSE;
   extract($row);
   switch($order)
   {
      case 1: return "$fname $lname ($num)";
              break;
      case 2: return "$lname, $fname ($num)";
              break;
      case 3: return "$fname";
              break;
      case 4: $fname = substr($fname, 0, 1);
              return "$lname, $fname ($num)";
              break;
      case 5: return "$fname $lname";
              break;
   }
   return "Invalid Display Type Requested";
}

// printMember
// args: member number,
// displays the name
function printBusiness($num)
{
   $cxn = open_stream();
   $sql = "SELECT name, ContactCID FROM Businesses WHERE BID='$num'";
   $result = query($cxn, $sql);
   if(!$row = mysqli_fetch_assoc($result)) return FALSE;
   extract($row);
   echo "$name";
   return TRUE;
}

// printMember
// args: member number,
// displays the name
function printBusinessLink($num)
{
   $cxn = open_stream();
   $sql = "SELECT name, ContactCID FROM Businesses WHERE BID='$num'";
   $result = query($cxn, $sql);
   if(!$row = mysqli_fetch_assoc($result)) return FALSE;
   extract($row);
   echo "<a href='showbusiness.php?BID=$num'>$name</a>";
   return TRUE;
}

// printMemberString
// args: member number,
//       order - 1 for fname lname, 2 for lname, fname, 3 for fname only, 4, lname, F
// returns the name as a string
function printBusinessString($num)
{
   $cxn = open_stream();
   $sql = "SELECT name, ContactCID FROM Businesses WHERE BID='$num'";
   $result = query($cxn, $sql);
   if(!$row = mysqli_fetch_assoc($result)) return FALSE;
   extract($row);
   return "$name";
}

// printMember
// args: member number,
// displays the name
function printBusinessContact($num, $order)
{
   $cxn = open_stream();
   $sql = "SELECT contactCID FROM Businesses WHERE BID='$num'";
   $result = query($cxn, $sql);
   if(!$row = mysqli_fetch_assoc($result)) return FALSE;
   extract($row);
   printCustomer($contactCID, $order);
   return TRUE;
}

// printMember
// args: member number,
// displays the name
function printBusinessContactLink($num, $order)
{
   $cxn = open_stream();
   $sql = "SELECT contactCID FROM Businesses WHERE BID='$num'";
   $result = query($cxn, $sql);
   if(!$row = mysqli_fetch_assoc($result)) return FALSE;
   extract($row);
   printCustomerLink($contactCID, $order);
   return TRUE;
}

// printMemberString
// args: member number,
//       order - 1 for fname lname, 2 for lname, fname, 3 for fname only, 4, lname, F
// returns the name as a string
function printBusinessContactString($num, $order)
{
   $cxn = open_stream();
   $sql = "SELECT contactCID FROM Businesses WHERE BID='$num'";
   $result = query($cxn, $sql);
   if(!$row = mysqli_fetch_assoc($result)) return FALSE;
   extract($row);
   return printCustomerString($contactCID, $order);
}

// printMember
// args: member number,
//       order - 1 for fname lname, 2 for lname, fname, 3 for fname only, 4, lname, F
// displays the name
function printCustomer($num, $order)
{
   $cxn = open_stream();
   $sql = "SELECT fname, lname FROM Customers WHERE CID='$num'";
   $result = query($cxn, $sql);
   if(!$row = mysqli_fetch_assoc($result)) return FALSE;
   extract($row);
   switch($order)
   {
      case 1: echo "$fname $lname";
              break;
      case 2: echo "$lname, $fname";
              break;
      case 3: echo "$fname";
              break;
      case 4: $fname = substr($fname, 0, 1);
              echo "$lname, $fname";
              break;
   }
   return TRUE;
}

// printMember
// args: member number,
//       order - 1 for fname lname, 2 for lname, fname, 3 for fname only, 4, lname, F
// displays the name
function printCustomerLink($num, $order)
{
   $cxn = open_stream();
   $sql = "SELECT fname, lname FROM Customers WHERE CID='$num'";
   $result = query($cxn, $sql);
   if(!$row = mysqli_fetch_assoc($result)) return FALSE;
   extract($row);
   echo "<a href='showcontact.php?CID=$num'>";
   switch($order)
   {
      case 1: echo "$fname $lname";
              break;
      case 2: echo "$lname, $fname";
              break;
      case 3: echo "$fname";
              break;
      case 4: $fname = substr($fname, 0, 1);
              echo "$lname, $fname";
              break;
   }
   echo "</a>";
   return TRUE;
}

// printMemberString
// args: member number,
//       order - 1 for fname lname, 2 for lname, fname, 3 for fname only, 4, lname, F
// returns the name as a string
function printCustomerString($num, $order)
{
   $cxn = open_stream();
   $sql = "SELECT fname, lname FROM Customers WHERE CID='$num'";
   $result = query($cxn, $sql);
   if(!$row = mysqli_fetch_assoc($result)) return FALSE;
   extract($row);
   switch($order)
   {
      case 1: return "$fname $lname";
              break;
      case 2: return "$lname, $fname";
              break;
      case 3: return "$fname";
              break;
      case 4: $fname = substr($fname, 0, 1);
              return "$lname, $fname";
              break;
      case 5: return "$fname $lname";
              break;
   }
   return "Invalid Display Type Requested";
}

//Leaves a link to the Task with TID $num,
//$style determines what the text of the link says.
//$style - 1, <PHONE> - <PROBLEM>
//		   2, <PROBLEM>
//		   3, <PHONE> - <PROBLEM> - <STATUS>
//	TID	customerID	customerType	createdOn	startedOn	dueDate	completedOn	phone	problem	status	notes
function printTaskLink($num, $style)
{
   $cxn = open_stream();
   $sql = "SELECT phone, problem, status FROM Tasks WHERE TID='$num'";
   $result = query($cxn, $sql);
   if(!$row = mysqli_fetch_assoc($result)) return FALSE;
   extract($row);
   echo "<a href='showtask.php?TID=$num'>";
   switch($style)
   {
      case 1: echo "$phone - $problem";
              break;
      case 2: echo "$problem";
              break;
      case 3: echo "$phone - $problem - $status";
              break;
   }
   echo "</a>";
   return TRUE;
}

// query
// args: cxn: a SQL connection, sql: an SQL query
// returns the result
function query($cxn, $sql)
{
   if(!$result = mysqli_query($cxn, $sql))
   {
      displayError("Query Error!<br>Query: $sql<br>SQL Error: " . mysqli_error($cxn));
      return (FALSE);
   }
   else return ($result);
}

// queryAssoc
// like query but returns the first associated array for the result
function queryAssoc($cxn, $sql)
{
   if($result = query($cxn, $sql))
   {
      $row = mysqli_fetch_assoc($result);
      return($row);
   }
   else
   {
      return FALSE;
   }
}

// queryOnce
// like query but returns the scalar value of the row[0]
function queryOnce($cxn, $sql)
{
   if($result = query($cxn, $sql))
   {
      $row = mysqli_fetch_row($result);
      return($row[0]);
   }
   else
   {
      return FALSE;
   }
}
   

// queryB
// args: a bound statement shmoogly
// returns the bound statement
function queryB($stmt)
{
   if(!$stmt->execute())
   {
      displayError("Query Error!<br>Query: $sql<br>SQL Error: " . $stmt->error);
      return (FALSE);
   }
   else
   {
      $stmt->store_result();
      return ($stmt);
   }
}

// reportBug
// emails a bug to the High Programer
// args - $page: the app which this was sent from
function reportBug($page)
{
   $bug = $_POST['bug'];
   $post = var_export($_POST, TRUE);
   $session = var_export($_SESSION, TRUE);
   $body = "A Bug Report was sent from $page.
            Date: " . date('l dS \of F Y h:i:s A') . "
            POST data: $post

            SESSION data: $session\n";

   if(mail('info@mrru.us', "FC/$page Bug Report", $body))
   {
      echo "<table border><tr><td><b>Message From Friend Computer</b><br>
            The following bug report has been sent to the High Programmers<br>
            $bug</td></tr></table>\n";
   }
   else
   {
      displayError("Error: Unable to send bug report. Pretty ironic, isn't it?");
   }
}

// safeGet
// args - $approved - an array of strings of approved GET keys
// unsets all unapproved GET entries
function safeGet($approved)
{
   $errors = FALSE;

   foreach($_GET as $key => $value)
   {
      if(!in_array($key, $approved))
      {
         unset($_GET[$key]);
         echo ($errors) ? "<font color=BLUE>Invalid POST entry: '$key'" : ", '$key'";
         $errors = TRUE;
      }
   }

   if($errors) 
   {
      echo "</font><br>\n";
      displayErrorDie("Invalid GET entries.");
   }
}


// safePost
// args - $approved - an array of strings of approved POST keys
// unsets all unapproved POST entries
function safePost($approved)
{
   $errors = FALSE;
   
   foreach($_POST as $key => $value)
   {
      if(!in_array($key, $approved))
      {
         unset($_POST[$key]);
         echo (!$errors) ? "<font color=BLUE>Invalid POST entry: '$key'" : ", '$key'";
         $errors = TRUE;         
      }
   }
   
   if($errors)
   {
      echo "</font><br>\n";
      displayErrorDie("Invalid POST entries.");
   }
}

// selectInputDate
// arguments: *Name are the names for the form data. startYear and endYear are the first and last years available
function selectInputDate($monName, $dayName, $yearName, $startYear, $endYear, $defMon, $defDay, $defYear)
{
$month = array('', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

   echo"<select name='$monName'>\n";
   foreach ($month as $num => $monName)
   {
      if($defMon == $num)
      {
         echo "<option value=$num selected>$monName</option>\n";
      }
      else
      {
         echo "<option value=$num>$monName</option>\n";
      }
   }
   echo"</select>\n
        <select name='$dayName'>\n
        <option value=0></option>\n";

  for ($i=1;$i<=31;$i++)
  {
     if($defDay == $i)
     {
        echo "<option value=$i selected>$i</option>\n";
     }
     else
     {
        echo "<option value=$i>$i</option>\n";
     }
  }

     echo "</select>
           <select name='$yearName'>\n
           <option value=0></option>\n";
  for ($i=$startYear; $i<=$endYear; $i++)
  {
     if($defYear == $i)
     {
        echo "<option value=$i selected>$i</option>\n";
     }
     else
     {
        echo "<option value=$i>$i</option>\n";
     }
  }
  echo "</select>\n";
}

// selectInputDayOfWeek
// produces a select for days of the week with 1 for Monday, 7 for Sunday.
function selectInputDayOfWeek($name, $default)
{
   $days = array (1=>'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
   echo "<select name='$name'>
         <option value='0'></option>\n";

   for($i = 1; $i <= 7; $i++)
   {
      if($i == $default)
      {
         echo "<option value='$i' selected>" . $days[$i] . "</option>\n";
      }
      else
      {
         echo "<option value='$i'>" . $days[$i] . "</option>\n";
      }
   }
   echo "</select>\n";
}
      


// selectInputTime
// displays a "select" for time
function selectInputTime($hourName, $minuteName, $defHour, $defMinute)
{
   echo "<select name='$hourName'>\n";
   for($i = 0; $i < 24; $i++)
   {
      if($defHour == $i)
      {
         echo "<option value=$i selected>$i</option>\n";
      }
      else
      {
         echo "<option value=$i>$i</option>\n";
      }
   }
   echo "</select> : <select name='$minuteName'>\n";
   for($i = 0; $i < 60; $i += 5)
   {
      if($defMinute == $i)
      {
         echo "<option value=$i selected>";
         printf("%02d", $i);
         echo "</option>\n";
      }
      else
      {
         echo "<option value=$i>";
         printf("%02d", $i);
         echo "</option>\n";
      }
   }
   echo "</select>";
}

// selectMember
// args: name - name for form
//       default - default to select
function selectAgent($selname, $default)
{
   $sql = "SELECT AID, fname, lname FROM Agents ORDER BY lname, fname, AID";
   $cxn = open_stream();
   $result = query($cxn, $sql);

   echo "<select name='$selname'>";
   echo "<option value='0'></option>\n";
   while ($row = mysqli_fetch_assoc($result))
   {
      extract($row);
      $fname = substr($fname, 0, 1);
      if($AID == $default)
      {
         echo "<option value='$AID' selected>$lname, $fname (#$AID)</option>\n";
      }
      else echo "<option value='$AID'>$lname, $fname (#$AID)</option>\n";
   }
   echo "</select>";

   mysqli_free_result($result);
   mysqli_close($cxn);
}

function selectBusiness($selname, $default)
{
   $sql = "SELECT BID, name, contactCID FROM Businesses ORDER BY name, contactCID, BID";
   $cxn = open_stream();
   $result = query($cxn, $sql);

   echo "<select name='$selname'>";
   echo "<option value='0'></option>\n";
   while ($row = mysqli_fetch_assoc($result))
   {
      extract($row);
      if($BID == $default)
      {
         echo "<option value='$BID' selected>$name (Contact: " . printCustomerString($contactCID, 2) . ")</option>\n";
      }
      else echo "<option value='$BID'>$name (Contact: " . printCustomerString($contactCID, 2) . ")</option>\n";
   }
   echo "</select>";

   mysqli_free_result($result);
   mysqli_close($cxn);
}

function selectCustomer($selname, $default)
{
   $sql = "SELECT CID, fname, lname FROM Customers ORDER BY lname, fname, CID";
   $cxn = open_stream();
   $result = query($cxn, $sql);

   echo "<select name='$selname'>";
   echo "<option value='0'></option>\n";
   while ($row = mysqli_fetch_assoc($result))
   {
      extract($row);
      if($CID == $default)
      {
         echo "<option value='$CID' selected>$lname, $fname</option>\n";
      }
      else echo "<option value='$CID'>$lname, $fname</option>\n";
   }
   echo "</select>";

   mysqli_free_result($result);
   mysqli_close($cxn);
}

function selectStatus($selName, $default)
{
	echo "<select name='$selName'>";
	
	if (!defined("CACHEENUM"))
	{
		$cxn = open_stream();
		$sql = "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS
			WHERE TABLE_NAME = 'Tasks' AND COLUMN_NAME = 'status'";
		$result = query($cxn, $sql);
		$row = mysqli_fetch_assoc($result);
		
		$colType = $row['COLUMN_TYPE'];
		define("CACHEENUM", str_replace("'", "", substr($colType, 5, (strlen($colType)-6))));
	}
	
	$cachedString = constant("CACHEENUM");
	$enumList = explode(",", $cachedString);

	foreach($enumList as $value)
	{
		if($value == $default)
		{
			echo "<option value='$value' selected>$value</option>";
		}
		else 
		{
			echo "<option value='$value'>$value</option>";
		}
	}

	echo "</select>";
}

function selectDate($selName, $dueDate)
{
	  echo "<select name='$selName'>
			<option value='$dueDate' selected>";
	  echo date("M-d-Y", mktime(0,0,0,
		intval(substr($dueDate, 5, 2)), intval(substr($dueDate, 8, 2)), intval(substr($dueDate, 0, 5))));
	  echo "</option>";
	  for ($i=0; $i<=7; $i++)
	  {
		  echo "<option value='";
		  echo date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")+$i, date("Y")));
		  echo "'>";
		  switch($i)
		  {
			  case 0:
				echo "Today (";
				echo date("M-d-Y", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
				echo ")";
				break;
			  case 1:
				echo "Tomorrow (";
				echo date("M-d-Y", mktime(0, 0, 0, date("m"), date("d")+1, date("Y")));
				echo ")";
				break;
			  case 7:
				echo "Next Week (";
				echo date("M-d-Y", mktime(0, 0, 0, date("m"), date("d")+7, date("Y")));
				echo ")";
				
				break;
			  default:
				echo "$i Days (";
				echo date("M-d-Y", mktime(0, 0, 0, date("m"), date("d")+$i, date("Y")));
				echo ")";
				break;
		  }
		  echo "</option>";
	  }
	  echo "</select>";
}

function addNote($newNote, $objtyp, $objID)
{
	$newNote = cleanString($newNote);
	$sql = "INSERT INTO Notes (timestamp, objecttype, objectID, note)
			VALUES (NOW(), '$objtyp', $objID, '$newNote')";
	$cxn = open_stream();
	if($result = query($cxn, $sql))
	{
		echo "Note added to the database.<hr>";
	}
	else echo "Error adding note to database.<hr>";
}

function displayNotes($objtyp, $objID, $addNoteBox = true)
{
	$sql = "SELECT timestamp, note FROM Notes WHERE (objecttype='$objtyp' AND objectID='$objID') ORDER BY timestamp DESC";
	
	$cxn = open_stream();
	$result = query($cxn, $sql);
	
	echo "<h2>Notes:</h2>";
	if ($addNoteBox)
	{
		echo "<form method='post'>";
		echo "<textarea rows='3' cols='50' name='newNote'></textarea>
			 <input type='submit' name='submit' value='Add Note'>
			 </form>";
	}
	while($row = mysqli_fetch_assoc($result))
	{
		extract($row);
		echo "$timestamp<br>
			  $note<hr>";
	}
}

function cleanString($dirtystring)
{
	$dirtystring = strip_tags($dirtystring);
	$dirtystring = str_replace("'", "&apos;", $dirtystring);
	return $dirtystring;
}

function fetchContact($ContactCID, $rows = '*')
{
	$sql = "SELECT $rows FROM Customers WHERE CID=$ContactCID";
	
	$cxn = open_stream();
	$result = query($cxn, $sql);
	
	if($row = mysqli_fetch_assoc($result))
	{
		return $row;
	}
}

function fetchBusiness($ContactBID, $rows = '*')
{
	$sql = "SELECT $rows FROM Businesses WHERE BID=$ContactBID";
	
	$cxn = open_stream();
	$result = query($cxn, $sql);
	
	if($row = mysqli_fetch_assoc($result))
	{
		return $row;
	}
}


?>
