<HTML>
<HEAD>
<?php
   echo "<TITLE>$title</TITLE>\n";

   echo $topinfo;
   
   echo "Hello, ";
   printAgent($_SESSION['ID'], 3);

   echo "<p><b><a href='index.php'>Master Page</a></b><br>";

   echo "<hr>";
   
   $now = date ("Y-m-d");
   $date = date_create();
   $date->modify("+1 day");
   $tomorrow = date_format($date, "Y-m-d");
   $date->modify("+6 day");
   $nextweek = date_format($date, "Y-m-d");

   echo "<b>Create Records</b><br>
         <a href='addcontact.php'>Add Contact</a><br>
         <a href='addbusiness.php'>Add Business</a><br>
         <a href='addtask.php'>Create Task</a><hr>";

   echo "<b>Edit Records</b><br>
		<a href='listcontact.php'>Contact List</a><br>
		<a href='listbusiness.php'>Business Contacts</a><br>
		<a href='listtask.php'>Task List</a><br>";
   
   echo "<hr>";
   
   echo "<b>My Account</b><br>
		<a href='accountsettings.php'>Account Info</a><br>";
		
   echo "<hr>";

   echo "<b><a href='index.php?logout=1'>Logout</a></b>";

   echo "</TD><TD width=800 valign=top><FONT SIZE=+2>$title</FONT>";
?>

