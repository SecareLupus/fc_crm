<?php
	include('funcs.inc');
	include('member.inc');
	include('header.php');
	include_once('Contact.inc');
	include_once('Warranty.inc');
	
	$workingIMEI = -1;
	if($_POST['submit'] == 'Save')
	{
		$workingplan = new WarrantyPlan($_POST['WID']);
		if ($enrollee->setPlan($_POST['enrollPlan']))
		{
			$name = new Contact($_POST['enrollContact']);
			echo $name->getName() . " has been successfully enrolled as a member!<hr>";
		}
	}
	if($_GET['WID'])
	{
		$workingPlan = $_GET['WID'];
	}
	if($_POST['ReUp'])
	{
		$workingIMEI = $_POST['hiddenIMEI'];
		$workingSub = new WarrantySub($workingIMEI);
		$workingSub->addTerms(1);
	}
	
	
	echo "<h2>Membership Plans</h2><hr>";
	echo "<h3>Inquire</h3>";
	echo "<form method='get'>";
	WarrantySub::selectWarrantyOwner("INQ", $workingIMEI);
	echo "<br><input type='submit' value='Lookup'>";
	echo "</form>";
	echo "<hr>";
	
	if ($workingIMEI == -1)
	{
		echo "<h3>Enroll</h3>";
		echo "<form method='post'>";
		echo "<table>";
		echo "<tr><td width=250>Owner:</td>";
		echo "<td width=500>";
		selectCustomer("enrollContact", -1);
		echo "</td></tr>";
		echo "<tr><td>Plan:</td>";
		echo "<td>";
		WarrantyPlan::selectWarrantyPlan("enrollPlan", -1);
		echo "</td></tr>";
		echo "<tr><td>IMEI/MEID:</td>";
		echo "<td><input type='text' name='enrollIMEI'></td></tr>";
		echo "</table>";
		echo "<input type='submit' name='submit' value='Enroll'>";
		echo "</form>";
	}
	else
	{
		$currSub = new WarrantySub($workingIMEI);
		$currPlan = $currSub->getPlan();
		echo "<h3>Plan Details</h3>";
		echo "<table>";
		echo "<tr><td width=250>Owner:</td>";
		echo "<td width=500>". $currSub->getOwner()->getName() ."</td></tr>";
		echo "<tr><td>IMEI/MEID:</td>";
		echo "<td>". $currSub->getID() ."</td></tr>";
		echo "<tr><td>Membership Dues:</td>";
		echo "<td>". money($currPlan->getPremium()) ."</td></tr>";
		echo "<tr><td>Service Fee:</td>";
		echo "<td>". money($currPlan->getDeductible()) ."</td></tr>";
		echo "<tr><td>Status:</td>";
		$statusText = ($currSub->isActive() ? "Active" : "Inactive");
		echo "<td>$statusText</td></tr>";		
		echo "<tr><td>Due:</td>";
		echo "<td>".$currSub->getDue()."</td></tr>";
		echo "</table>";
		$buttonText = ($currSub->isActive() ? "Add Additional Term" : "Pay to Current");
		echo "<form method='post'><input type='hidden' name='hiddenIMEI' value='$workingIMEI'>";
		echo "<input type='submit' name='ReUp' value='$buttonText'></form>";
		echo "<table><tr>";
		echo "<td width=150><a href='printMembershipDetails.php?SID=$workingIMEI'>Print Summary</a></td>";
		echo "<td width=150>Print Agreement</td>";
		echo "</tr></table>";
	}
	echo "<hr>";
	echo "<table><tr>";
	echo "<td width=250>View Past-Due Members</td>";
	echo "<td width=250>Edit Membership Plans</td>";
	echo "</tr></table>";
?>
