<?php
// index.php
// This is the main page for all users.
// Everyone sees:
// Nothing right now... Trying to retain the two column format for future use, though.

   include('funcs.inc');
   include('member.inc');
   include('header.php');
   $cxn = open_stream();
   
   class CSVData{
    public $file;
    public $data;
    public $fp;
    public $caption=true;
    public function CSVData($file=''){
        if ($file!='') getData($file);
    }
    function getData($file){
        if (strpos($file, 'tp://')!==false){
            copy ($file, '/tmp/csvdata.csv');
            if ($this->fp=fopen('/tmp/csvdata.csv', 'r')!==FALSE){
                $this->readCSV();
                unlink('tmp/csvdata.csv');
            }
        } else {
            $this->fp=fopen($file, 'r');
            $this->readCSV();
        }
        fclose($this->fp);
    }
    private function readCSV(){
        if ($this->caption==true){
            if (($captions=fgetcsv($this->fp, 1000, ","))==false) return false;
        }
        $row=0;
        while (($data = fgetcsv($this->fp, 1000, ",")) !== FALSE) {
            for ($c=0; $c < count($data); $c++) {
                $this->data[$row][$c]=$data[$c];
                if ($this->caption==true){
                    $this->data[$row][$captions[$c]]=$data[$c];
                }
            }
            $row++;
        }
    }
	}
	
	function getIDFromZohoID($ZID)
    {
		$cxn = open_stream();
		$sqlstmt = "SELECT object FROM ZohoTranslate WHERE zohoID='$ZID'";
		$result = query($cxn, $sqlstmt);
		if($row = mysqli_fetch_assoc($result))
		{
			echo "Object: " . $row['object'] . "<br>";
			return $row['object'];
		}
		else
		{
			return 0;
		}
	}

  echo "<h2>Import Files</h2>
			 <hr>";
	
	if($_POST['submit'] == 'Import')
	{
		$newAgents = scandir('./import/Agents', 1);
		$newContacts = scandir('./import/Contacts', 1);
		//$newBusinesses = scandir('./import/Businesses', 1);
		$newTasks = scandir('./import/Tasks', 1);
		
		$o=new CSVData();
		$o->getData('./import/Agents/' . $newAgents[0]);
		$newAgentData=$o->data;
		
		$o=new CSVData();
		$o->getData('./import/Contacts/' . $newContacts[0]);
		$newContactData=$o->data;
		
		//$o=new CSVData();
		//$o->getData('./import/Businesses/' . $newBusinesses[0]);
		//$newBusinessData=$o->data;
		
		$o=new CSVData();
		$o->getData('./import/Tasks/' . $newTasks[0]);
		$newTaskData=$o->data;
		
		foreach ($newAgentData as $value)
		{//"User Id",Email,"First Name","Last Name"
			echo "Adding Agent<br>";
			$sql = "INSERT INTO Agents (fname, lname, password, email) VALUES ('";
			$sql .= cleanString($value['First Name']);
			$sql .= "', '"; 
			$sql .= cleanString($value['Last Name']);
			$sql .= "', '"; 
			$sql .= hash('sha256','changeme');
			$sql .= "', '"; 
			$sql .= cleanString($value['Email']);
			$sql .= "')";
			echo $sql . "<br>";
			
			if($result = query($cxn, $sql))
			{
				echo "Contact added to the database.<br>";
				$sql = "SELECT AID FROM Agents WHERE (fname='" . cleanString($value['First Name']) . "' AND
						lname='" . cleanString($value['Last Name']) . "')";
				$result = query($cxn, $sql);
				if($row = mysqli_fetch_assoc($result))
				{
					extract($row);
					$sql = "INSERT INTO ZohoTranslate (object, ZohoID, type) VALUES ('$AID', '";
					$sql .= $value['User Id'] . "', 'Agent')";
					if ($result = query($cxn, $sql))
					{
						echo "Translation Key added to database.<br>";
					}
				}
				
			}
			else echo "Error adding Contact to database.<br>";
		}
		
		foreach ($newContactData as $value)
		{//"Contact Id", "First Name", "Last Name", Email, Phone
			echo "Adding Contact<br>";
			//fname, lname, email, phonenum, howfound, createdOn
			$sql = "INSERT INTO Customers (fname, lname, email, phonenum, howfound, createdOn) VALUES ('";
			$sql .= cleanString($value['First Name']);
			$sql .= "', '"; 
			$sql .= cleanString($value['Last Name']);
			$sql .= "', '"; 
			$sql .= cleanString($value['Email']);
			$sql .= "', '"; 
			$sql .= phonenumFromString($value['Phone']);
			$sql .= "', 'Imported from Zoho', NOW())"; 
			echo $sql . "<br>";
			if($result = query($cxn, $sql))
			{
				echo "Contact added to the database.<br>";
				$sql = "SELECT CID FROM Customers WHERE (fname='" . cleanString($value['First Name']) . "' AND
						lname='" . cleanString($value['Last Name']) . "')";
				$result = query($cxn, $sql);
				if($row = mysqli_fetch_assoc($result))
				{
					extract($row);
					$sql = "INSERT INTO ZohoTranslate (object, ZohoID, type) VALUES ('$CID', '";
					$sql .= $value['Contact Id'] . "', 'Contact')";
					if ($result = query($cxn, $sql))
					{
						echo "Translation Key added to database.<br>";
					}
				}
				
			}
			else echo "Error adding Contact to database.<br>";
		}

		foreach ($newTaskData as $value)
		{//"Task Owner", "Task Owner Id"(ZID), Subject, "Due Date", "Who Name", "Contact Name"(ZID), Status, "Created Time", Description
			//	customerID	customerType createdOn assignedTo dueDate	completedOn	phone	problem	status	notes
			echo "Adding Task<br>";
			$sql = "INSERT INTO Tasks (customerID, customerType, createdOn, assignedTo, dueDate, problem, status, notes) VALUES (";
			$sql .= getIDFromZohoID($value['Contact Name']);
			$sql .= ", 'Individual', '";
			$sql .= $value['Created Time'];
			$sql .= "', ";
			$sql .= getIDFromZohoID($value['Task Owner Id']);
			$sql .= ", '";
			$sql .= $value['Due Date'];
			$sql .= "', '";
			$sql .= cleanString($value['Subject']);
			$sql .= "', '";
			if ($value['Status'] != 'Completed')
			{
				$sql .= "Opened";
			}
			else $sql .= "Completed";
			$sql .= "', '";
			$sql .= cleanString($value['Description']);
			$sql .= "')";
			echo "SQL: $sql<br>";
			if($result = query($cxn, $sql))
			{
				echo "Task added to database.<br>";
			}
			else
			{
				echo "Error adding task to database.<br>";
			}
		}
	}
		
	echo "<form method='post'><input type='submit' name='submit' value='Import'></form>";

	include ('footer.php');
?>
