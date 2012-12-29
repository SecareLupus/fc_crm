<?php
// GET
// sort = 'num' - by member number
// showmembers.php

   include('funcs.inc');
   include('member.inc');
   include('header.php');
   require('customisation.inc');
   global $CUS_Company_Email;
   global $CUS_Employee_Title;
   
   $cxn = open_stream();
   
   if($_POST['submit'] == 'Save')
   {
	   $newName = cleanstring($_POST['emailname']);
	   $newSub = cleanString($_POST['emailsubject']);
	   
	   $newBody = cleanString($_POST['emailbody']);
	   
	   $sql = "INSERT INTO emailTemplates(name, subject, body) VALUES('$newName', '$newSub', '$newBody')";
	   if ($result = query($cxn, $sql))
	   {
		   echo "New Template Saved<br>";
	   }
	   else
	   {
		   echo "ERROR: Could not save new template.<br>";
	   }
   }
   
   $editName = "";
   $editSub = "";
   $editBody = "";
   if($_POST['submit'] == 'Edit')
   {
	   $tempID = $_POST['selectedtemplate'];
	   $sql = "SELECT * FROM emailTemplates WHERE templateID=$tempID";
	   $result = query($cxn, $sql);
	   if ($row = mysqli_fetch_assoc($result))
	   {
		   $editName = $row['name'];
		   $editSub = $row['subject'];
		   $editBody = $row['body'];
		   $_POST['submit'] = 'Remove';
	   }
   }
   
   if ($_POST['submit'] == 'Remove')
   {
	   $tempID = $_POST['selectedtemplate'];
	   $sql = "DELETE FROM emailTemplates WHERE templateID=$tempID";
	   if($result != query($cxn, $sql))
	   {
		   "ERROR: Row failed to be deleted.<br>";
	   }
   }

  echo "<h2>Email Template</h2><hr>
		<form name='email' method='post'>
		<table>";
  echo "<tr><td>Existing Templates:</td><td width=150><select name='selectedtemplate'>";
  $sql = "SELECT * FROM emailTemplates ORDER BY name DESC";
  $result = query($cxn, $sql);
  while ($row = mysqli_fetch_assoc($result))
  {
	  extract($row);
	  echo "<option value='$templateID'>$name</option>";
  }
  echo "</select></td></tr>";
  echo "<tr><td><input type='submit' name='submit' value='Edit'><input type='submit' name='submit' value='Remove'></td></tr>";
  echo "<tr><td><br></td></tr>";
  echo "<tr><td>Template Name:</td><td width=500><input type='text' name='emailname' size=50 value='$editName'></td></tr>";
  echo "<tr><td>Subject:</td><td width=500><input type='text' name='emailsubject' size=75 maxlength=150 value='$editSub'></td></tr>";
  echo "<tr><td valign='top'>Email:</td><td><textarea cols=60 rows=20 name='emailbody'>$editBody</textarea></td></tr>
		<tr><td>&nbsp;</td><td align='right'><input type='submit' name='submit' value='Save'></td></tr></table>
        </form>";
        
	include ('footer.php');
?>
