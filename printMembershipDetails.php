<?php

   include('funcs.inc');
   include_once('Warranty.inc');
   include_once('Contact.inc');
   
	if($_GET['SID'])
	{
		$workingIMEI = $_GET['SID'];
	}
	else
	{
		die("No membership plan chosen.<hr>");
	}

   if($currSub = new WarrantySub($workingIMEI))
   {
   
   echo "<table>
   <tr>
		<td rowspan=3 height=100 width=100><img width=100 src='./data/logo.png' ALT='$CUS_Company_Name Logo' /></td>
		<td align='center'>$CUS_Company_Name</td></tr>
   <tr>
		<td align='center'>Member File</td></tr>
   <tr><td>
		<table>
		<td width=100 align='left'>Member #" . $currSub->getOwner()->getID() . "</td>
		<td>Lead $CUS_Employee_Title: ";
			printAgent($_SESSION['ID'], 1);
echo 	"</td>
		</table>
   </td></tr>
   </table>
         <hr>";
         
		$currPlan = $currSub->getPlan();
		echo "<h3>Plan Details</h3>";
		echo "<table>";
		echo "<tr><td width=250>Owner:</td>";
		echo "<td width=500>". $currSub->getOwner()->getName() ."</td></tr>";
		echo "<tr><td>IMEI/MEID:</td>";
		echo "<td>". $currSub->getID() ."</td></tr>";
		echo "<tr><td>Membership Dues:</td>";
		echo "<td>". money($currPlan->getPremium()) ." per ".$currPlan->getDuration()."</td></tr>";
		echo "<tr><td>Service Fee:</td>";
		echo "<td>". money($currPlan->getDeductible()) ." per incident</td></tr>";
		echo "<tr><td>Status:</td>";
		$statusText = ($currSub->isActive() ? "Active" : "Inactive");
		echo "<td>$statusText</td></tr>";		
		echo "<tr><td>Due:</td>";
		echo "<td>".$currSub->getDue()."</td></tr>";
		echo "</table>";
   }
?>
