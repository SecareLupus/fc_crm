<?php
// GET
// sort = 'num' - by member number
// showmembers.php

   include('funcs.inc');
   include('member.inc');
   include('header.php');
   require('customisation.inc');
   global $CUS_Company_Email;
   
   if($_POST['submit'] == 'Send')
   {
	   extract($_POST);
	   
	   //$emailsubject = cleanString($emailsubject);
	   //$emailbody = cleanString($emailbody);
	   
		$cxn = open_stream();
		
		if($contactType == 'Agent')
		{
			$sql = "SELECT email FROM Agents WHERE AID=$contactAgent";
		}
		elseif($contactType == 'Contact')
		{
			$sql = "SELECT email FROM Customers WHERE CID=$contactInd";
		}
		
		//echo "DEBUG: SQL='$sql'<br>";
		$result = query($cxn, $sql);
		
		if ($row = mysqli_fetch_assoc($result))
		{
			//echo "DEBUG: email fetched!<br>";
			$emailheader = "from: $CUS_Company_Email
					$CUS_Company_Email
					X-Mailer: PHP/" . phpversion();
					
			if(mail($row['email'], $emailsubject, $emailbody, $emailheader))
			{
				echo "Email sent successfully.<br>";
			}
			else echo "Failed to send email.<hr>";
		}
		else
		{
			echo "Failed to fetch email.<hr>";
		}
		
		
   }
echo "<script type='text/javascript'>
	 function initForm(){
		document.forms['email']['contactAgent'].disabled=false;
		document.forms['email']['contactInd'].disabled=true;
	 }
	 
	 function selectRadio(n){ 
	 if(n==0){
		document.forms['email']['contactAgent'].disabled=false;
		document.forms['email']['contactInd'].disabled=true;
		document.forms['email']['contactType[0]'].checked=true;
		document.forms['email']['contactType[1]'].checked=false;
	 }
	 if(n==1){
		document.forms['email']['contactAgent'].disabled=true;
		document.forms['email']['contactInd'].disabled=false;
		document.forms['email']['contactType[0]'].checked=false;
		document.forms['email']['contactType[1]'].checked=true;
	 }
	 } 
	 </script>";

  echo "<h2>Send Email</h2><hr>
		<form name='email' method='post'>
		<table>";
  echo "<tr><td width=150>Recipient Type:</td><td>
        Agent <input type='radio' name='contactType' value='Agent' onclick='selectRadio(0)' checked>
        Contact <input type='radio' name='contactType' value='Contact' onclick='selectRadio(1)'>
        </td></tr>";
  echo "<tr><td>Recipient:</td><td>";
		selectAgent('contactAgent', (($_GET['eType'] == 'A' && !empty($_GET['recipID'])) ? $_GET['recipID'] : -1));
        selectCustomer('contactInd', (($_GET['eType'] == 'C' && !empty($_GET['recipID'])) ? $_GET['recipID'] : -1));
  echo "</td></tr><br>";
  echo "<tr><td>Subject:</td><td width=500><input type='text' name='emailsubject' size=75 maxlength=150></td></tr>";
  echo "<script type='text/javascript'>initForm();</script></td></tr>";
  echo "<tr><td valign='top'>Email:</td><td><textarea cols=60 rows=20 name='emailbody'></textarea></td></tr>
		<tr><td>&nbsp;</td><td align='right'><input type='submit' name='submit' value='Send'></td></tr></table>
        </form>";
        
	if($_GET['eType'] == 'A')
	{
		echo "<script type='text/javascript'>
			  document.forms['email']['contactAgent'].disabled=false;
			  document.forms['email']['contactInd'].disabled=true;
			  document.forms['email']['contactType[0]'].checked=true;
			  document.forms['email']['contactType[1]'].checked=false;
			  </script>";
	}
	elseif($_GET['eType'] == 'C')
	{
		echo "<script type='text/javascript'>
			  document.forms['email']['contactAgent'].disabled=true;
			  document.forms['email']['contactInd'].disabled=false;
			  document.forms['email']['contactType[0]'].checked=false;
			  document.forms['email']['contactType[1]'].checked=true;
			  </script>";
	}
        
	include ('footer.php');
?>
