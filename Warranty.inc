<?php
include_once('Contact.inc');

class WarrantyPlan
{
	private $id = -1;
	private $premium = -1;
	private $deductible = -1;
	private $minterm = -1;
	private $duration = "";
	private $commission = -1;
	private $bonuses = array();
	private $needsUpdate = array();
	
	public function __construct($pullid)
	{
		$this->id = $pullid;
		$planRow = self::fetchWarrantyPlan($this->id);
		
		$this->premium = $planRow['prem'];
		$this->deductible = $planRow['dedu'];
		$this->minterm = $planRow['minterm'];
		$this->duration = $planRow['duration'];
		$this->commission = $planRow['comm'];
		$tmp = trim($planRow['bonus']);
		$tmp = trim($tmp, '"');
		$this->bonuses = split('", "', $tmp);
	}
	
	public function getID()
	{
		return $this->id;
	}
	
	public function getPremium()
	{
		return $this->premium;
	}
	
	public function getDeductible()
	{
		return $this->deductible;
	}
	
	public function getMinTerm()
	{
		return $this->minterm;
	}
	
	public function getDuration()
	{
		return $this->duration;
	}
	
	public function getCommission()
	{
		return $this->commission;
	}
	
	public function getBonuses()
	{
		return $this->bonuses;
	}
	
	public function setPremium($newVal, $push=true)
	{
		$this->needsUpdate['prem'] = $newVal;
		
		return $this->pushCheck($push);
	}
	
	public function setDeductible($newVal, $push=true)
	{
		$this->needsUpdate['dedu'] = $newVal;
		
		return $this->pushCheck($push);
	}
	
	public function setMinTerm($newVal, $push=true)
	{
		$this->needsUpdate['minterm'] = $newVal;
		
		return $this->pushCheck($push);
	}
	
	public function setDuration($newVal, $push=true)
	{
		$this->needsUpdate['duration'] = $newVal;
		
		return $this->pushCheck($push);
	}
	
	public function setCommission($newVal, $push=true)
	{
		$this->needsUpdate['comm'] = $newVal;
		
		return $this->pushCheck($push);
	}
	
	public function setBonuses($newVal, $push=true)
	{
		if (!is_array($newVal))
			return false;
		
		$tmpString = '"';
		foreach($newVal as $currVar)
		{
			if(strlen($tmpString) > 1)
			{
				$tmpString .= ', "';
			}
			$tmpString .= $currVar;
			$tmpString .= '"';
		}
		
		$this->needsUpdate['bonus'] = $tmpString;
		
		return $this->pushCheck($push);
	}
	
	public function pushCheck($push)
	{
		if ($push)
		{
			if ($this->pushUpdate())
			{
				return true;
			}
		}
		else
		{
			return true;
		}
		return false;
	}
	
	public function pushUpdate()
	{
		if($this->rawUpdate(array_keys($this->needsUpdate), $this->needsUpdate))
		{
			return new WarrantyPlan($this->id);
		}
		return null;
	}
	
	private function rawUpdate($var, $value)
	{
		if (self::updateWarrantyPlan($this->id, $var, $value)) return true;
		return false;
	}
	
	public static function selectWarrantyPlan($selname, $default)
	{
		$sql = "SELECT WID, prem, dedu, duration FROM WarrantyPlans ORDER BY WID";
		$cxn = open_stream();
		$result = query($cxn, $sql);

		echo "<select name='$selname'>";
		echo "<option value='0'></option>\n";
		while ($row = mysqli_fetch_assoc($result))
		{
			extract($row);
			if($WID == $default)
			{
				echo "<option value='$WID' selected>".money($prem).". per $duration, $dedu Fee</option>\n";
			}
			else echo "<option value='$WID'>".money($prem).". per $duration, $dedu Fee</option>\n";
		}
		echo "</select>";

		mysqli_free_result($result);
		mysqli_close($cxn);
	}
	
	public static function createWarrantyPlan()
	{
		$cxn = open_stream();
		$sql = "INSERT INTO WarrantyPlans (prem) VALUES (10.0)";
		
		if($result = query($cxn, $sql))
		{
			$sql = "SELECT WID FROM WarrantyPlans ORDER BY WID DESC LIMIT 1";
			$result = query($cxn, $sql);
			if ($row = mysqli_fetch_assoc($result))
			{
				return new WarrantyPlan($row['WID']);
			}
		}
		return null;
	}
	
	private static function fetchWarrantyPlan($planWID, $rows = '*')
	{
		$cxn = open_stream();
		$sql = "SELECT $rows FROM WarrantyPlans WHERE WID='$planWID'";
		
		$result = query($cxn, $sql);
		
		if($row = mysqli_fetch_assoc($result))
		{
			return $row;
		}
		else
		{
			return null;
		}
	}
	
	private static function updateWarrantyPlan($planWID, $var, $value)
	{
		$cxn = open_stream();
		$sql = "";
		if(is_array($var) && is_array($value))
		{
			$sql .= "UPDATE WarrantyPlans SET ";
			$first = true;
			while(count($var) > 0)
			{
				if($first)
				{
					$first = false;
				}
				else
				{
					$sql .= ", ";
				}
				$sql .= array_shift($var) . "='" . array_shift($value) . "'";
			}
			$sql .=  " WHERE WID='$planWID'";
		}
		elseif(!is_array($var) && !is_array($value))
		{
			$sql = "UPDATE WarrantyPlans SET $var='$value' WHERE WID='$planWID'";
		}
		else
		{
			return false;
		}
		
		if ($result = query($cxn, $sql))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

class WarrantySub
{
	private $id = "";
	private $owner = -1;
	private $plan = -1;
	private $paidto = -1;
	private $bonus = -1;
	private $needsUpdate = array();
	
	public function __construct($pullid)
	{
		$this->id = $pullid;
		$subRow = self::fetchWarrantySub($this->id);
		
		$this->owner = $subRow['owner'];
		$this->plan = $subRow['plan'];
		$this->paidto = $subRow['paidto'];
		$this->bonus = $subRow['bonus'];
	}
	
	public function getID()
	{
		return $this->id;
	}
	
	public function getOwner()
	{
		return new Contact($this->owner);
	}
	
	public function getPlan()
	{
		return new WarrantyPlan($this->plan);
	}
	
	public function getPaidTo()
	{
		$year = substr($this->paidto, 0, 4);
		$month = substr($this->paidto, 5, 2);
		$day = substr($this->paidto, 8, 2);
		return mktime(0,0,0,$month,$day,$year);
	}
	
	public function getBonus()
	{
		return $this->bonus;
	}
	
	public function getDue()
	{
		$lastDate = $this->getPaidTo();
		$dayAfter = mktime(0,0,0,date("m", $lastDate),date("d", $lastDate)+1,date("Y", $lastDate));
		$today = mktime(0,0,0,date("m"),date("d"),date("Y"));
		if ($today <= $lastDate)
		{
			$tmpString = money($this->getPlan()->getPremium()) . " due by " . date("D, M j", $dayAfter);
			return $tmpString;
		}
		$tmpString = "Membership lapsed on " . date("D, M j Y", $lastDate);
		$tmpString .= ". To reactivate: ";
		$currDue = 0;
		while ($lastDate < $today)
		{
			$addDays = 0;
			$addMonths = 0;
			$addYears = 0;
			switch ($this->getPlan()->getDuration())
			{
				case "week":
					$addDays = 7;
					break;
				case "month":
					$addMonths = 1;
					break;
				case "year":
					$addYears = 1;
					break;
			}
			$lastDate = mktime(0,0,0,
								date("m", $lastDate)+$addMonths,
								date("d", $lastDate)+$addDays,
								date("Y", $lastDate)+$addYears);
			$currDue += $this->getPlan()->getPremium();
		}
		$tmpString .= money($currDue);
		return $tmpString;
	}
	
	public function isActive()
	{
		$lastDate = $this->getPaidTo();
		$today = mktime(0,0,0,date("m"),date("d"),date("Y"));
		if ($lastDate >= $today)
		{
			return true;
		}
		return false;
	}
	
	public function setOwner($newVal, $push=true)
	{
		$this->needsUpdate['owner'] = $newVal->getID();	
		
		return $this->pushCheck($push);
	}
	
	public function setPlan($newVal, $push=true)
	{
		$this->needsUpdate['plan'] = $newVal;
		
		return $this->pushCheck($push);
	}
	
	public function addTerms($newVal, $push=true)
	{
		//$newVal == number of terms being added.
		$tmpDate = $this->getPaidTo();
		$addDays = 0;
		$addMonths = 0;
		$addYears = 0;
		switch ($this->getPlan()->getDuration())
		{
			case "week":
				$addDays = (7 * $newVal);
				break;
			case "month":
				$addMonths = $newVal;
				break;
			case "year":
				$addYears = $newVal;
				break;
		}
				
		$newTimestamp = date("Y-m-d", mktime(0, 0, 0,
								date("m", $tmpDate)+$addMonths,
								date("d", $tmpDate)+$addDays,
								date("Y", $tmpDate)+$addYears));
		//$newDate = paidTo + ($newVal * $termLen
		$this->needsUpdate['paidto'] = $newTimestamp;
		return $this->pushCheck($push);
	}
	
	public function setBonus($newVal, $push=true)
	{
		$this->needsUpdate['bonus'] = $newVal;
		
		return $this->pushCheck($push);
	}
	
	public function pushCheck($push)
	{
		if ($push)
		{
			if ($this->pushUpdate())
			{
				return true;
			}
		}
		else
		{
			return true;
		}
		return false;
	}
	
	public function pushUpdate()
	{
		if($this->rawUpdate(array_keys($this->needsUpdate), $this->needsUpdate))
		{
			return new WarrantySub($this->id);
		}
		return null;
	}
	
	private function rawUpdate($var, $value)
	{
		if (self::updateWarrantySub($this->id, $var, $value)) return true;
		return false;
	}
	
	public static function selectWarrantyOwner($selname, $default)
	{
		$sql = "SELECT SID, owner FROM WarrantySubs ORDER BY owner";
		$cxn = open_stream();
		$result = query($cxn, $sql);

		echo "<select name='$selname'>";
		echo "<option value='0'></option>\n";
		while ($row = mysqli_fetch_assoc($result))
		{
			extract($row);
			$planowner = new Contact($owner);
			$fname = $planowner->getFName();
			$lname = $planowner->getLName();
			if($SID == $default)
			{
				echo "<option value='$SID' selected>$lname, $fname</option>\n";
			}
			else echo "<option value='$SID'>$lname, $fname</option>\n";
		}
		echo "</select>";

		mysqli_free_result($result);
		mysqli_close($cxn);
	}
	
	public static function createWarrantySub($IMEI)
	{
		$cxn = open_stream();
		$sql = "INSERT INTO WarrantySubs (SID, paidto) VALUES ('$IMEI', CURDATE())";
		
		if($result = query($cxn, $sql))
		{
			return new WarrantySub($IMEI);
		}
		
		return null;
	}
	
	private static function fetchWarrantySub($SID, $rows = '*')
	{
		$cxn = open_stream();
		$sql = "SELECT $rows FROM WarrantySubs WHERE SID='$SID'";
		
		$result = query($cxn, $sql);
		
		if($row = mysqli_fetch_assoc($result))
		{
			return $row;
		}
		else
		{
			return null;
		}
	}
	
	private static function updateWarrantySub($SID, $var, $value)
	{
		$cxn = open_stream();
		$sql = "";
		if(is_array($var) && is_array($value))
		{
			$sql .= "UPDATE WarrantySubs SET ";
			$first = true;
			while(count($var) > 0)
			{
				if($first)
				{
					$first = false;
				}
				else
				{
					$sql .= ", ";
				}
				$sql .= array_shift($var) . "='" . array_shift($value) . "'";
			}
			$sql .=  " WHERE SID='$SID'";
		}
		elseif(!is_array($var) && !is_array($value))
		{
			$sql = "UPDATE WarrantySubs SET $var='$value' WHERE SID='$SID'";
		}
		else
		{
			return false;
		}
		
		if ($result = query($cxn, $sql))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
?>
