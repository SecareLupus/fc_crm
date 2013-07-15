<?php

   include('funcs.inc');
   include_once('Warranty.inc');
   include_once('Contact.inc');
   
	if($_GET['WID'])
	{
		$planID = $_GET['WID'];
	}
	
	$madlibs = array(	"a small monthly fee",
						"a greatly reduced service fee",
						"may be required to hold membership for a minimum number of terms",
						"may be entitled to additional bonus features")
	if($plan = new WarrantyPlan($planID))
	{
		$madlibs = array(	money($plan->getPremium())." per ".
								$plan->getDuration(),
							money($plan->getDeductible())." service fee",
							"required to hold membership for at least ".
								$plan->getMinTerm()." ".
								$plan->getDuration()."(s)",
							$plan->getBonuses()
						)
	}
	
	echo "<table valign='top'>
		<tr>
		<td rowspan=5 height=150 width=500><img height=150 width=150 src='./data/logo.png' ALT='$CUS_Company_Name Logo' /></td>
		<td width=300 align='left'>$CUS_Company_Name</td></tr>
		<tr>
		<td align='left'>$CUS_Company_AddressL1</td></tr>
		<tr>
		<td align='left'>$CUS_Company_AddressL2</td></tr>
		<tr>
		<td align='left'>$CUS_Company_Phone</td></tr>
		</table>";
	echo "<h2>Membership Agreement</h2>";
	echo "<p>
			This page details an agreement between Mobile Rapid Response Unit
			and the purchaser of this agreement, hereafter referred to as 'The Member'.
			This agreement does not constitute a legally binding contract, and may
			not be used litigiously by any party. This agreement is made in the
			hope that it will be useful to The Member, but WITHOUT ANY WARRANTY;
			without even the implied warranty of WORKMANLIKE QUALITY or FITNESS
			FOR A PARTICULAR PURPOSE.
		 </p>";
	echo "<p>
			The Member agrees to 
		 </p>";
	echo "<p></p>";
	echo "<p></p>";
?>
