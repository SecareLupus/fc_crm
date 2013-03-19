<?php

include('funcs.inc');
include('header.php');
include('datewidgets.inc');
include('Report.inc');

echo "<form name='buildReport' method='post'>";
echo "<h3>Start Date</h3>";
selDateWidget("startDate");
echo "<h3>End Date</h3>";
selDateWidget("endDate");
echo "<br><input type='submit' name='submit' value='Build Report'><br>";
echo "</form>";

if ($_POST['submit'] == "Build Report")
{
	$startDate = $_POST['startDate_Year'] . "-" . $_POST['startDate_Month'] . "-" .$_POST['startDate_Day'];
	$endDate = $_POST['endDate_Year'] . "-" . $_POST['endDate_Month'] . "-" .$_POST['endDate_Day']; 
	$report = new Report($startDate, $endDate);
	$parts = $report->getCombinedPartsBilled();
	$labour = $report->getCombinedLabourBilled();
	$tax = $report->getCombinedTaxBilled();
	
	echo "Report Successfully Built.<br>";
	$startDate = date("F j, Y", mktime(0, 0, 0, $_POST['startDate_Month'], $_POST['startDate_Day'], $_POST['startDate_Year']));
	$endDate = date("F j, Y", mktime(0, 0, 0, $_POST['endDate_Month'], $_POST['endDate_Day'], $_POST['endDate_Year']));
	echo "<br>Report for period between $startDate and $endDate<br><br>";
	echo "Total Sales for this Period: " . money($parts + $labour) . "<br>";
	echo "&nbsp;&nbsp;&nbsp;Parts: " . money($parts) . "<br>";
	echo "&nbsp;&nbsp;&nbsp;Labour: " . money($labour) . "<br>";
	echo "Total Tax for this Period: " . money($tax) . "<br><br>";
	
	$usedParts = $report->getCombinedPartsUsed();
	asort($usedParts);
	$usedParts = array_reverse($usedParts);
	echo "Parts used in this period:<br>";
	foreach($usedParts as $key => $value)
	{
		echo "$value &nbsp;&nbsp;- &nbsp;&nbsp;$key<br>";
	}
	
}

?>
