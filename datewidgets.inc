<?php

require('customisation.inc');

function selDateWidget($namePrefix, $startYear=-1, $endYear=-1, $defMonth=-1, $defDay=-1, $defYear=-1)
{
	if ($startYear == -1)
	{
		global $CUS_Company_Founded;
		$startYear = $CUS_Company_Founded;
	}
	if ($endYear == -1)
	{
		$endYear = intval(date("Y"));
	}

	$months = array('', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	
	echo "Month: ";
	echo	"<select name='".$namePrefix."_Month'><br>";
	foreach ($months as $num => $monName)
	{
		if($defMonth == $num)
		{
			echo "<option value=$num selected>$monName</option>";
		}
		else
		{
			echo "<option value=$num>$monName</option>";
		}
	}
	echo "</select>";
	
	echo " Day: ";
	echo"<select name='".$namePrefix."_Day'><br>";
	echo "<option value=0></option>";
	for ($day=1;$day<= 31;$day++)
	{
		if($defDay == $day)
		{
			echo "<option value=$day selected>$day</option>";
		}
		else
		{
			echo "<option value=$day>$day</option>";
		}
	}
	echo "</select>";
	
	echo " Year: ";
	echo"<select name='".$namePrefix."_Year'><br>";
	echo "<option value=0></option>";
	for ($year=$startYear;$year<=$endYear;$year++)
	{
		
		if($defYear == $year)
		{
			echo "<option value=$year selected>$year</option>";
		}
		else
		{
			echo "<option value=$year>$year</option>";
		}
	}
	echo "</select>";
}
?>
