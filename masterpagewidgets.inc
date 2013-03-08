<?php
//masterpagewidgets.inc
//PHP Library used to hold the widgets used on the Master Page of FC_CRM

include_once('Employee.inc');
include_once('Invoice.inc');
include_once('Task.inc');
require('customisation.inc');

function mpwOpenTasks($SEmployee)
{
	global $CUS_Category_Name;
	global $CUS_Task_Short;
	global $CUS_Customer_Title;
	
	echo "<h2>Open Tasks</h2>";

	$openTasks = $SEmployee->getTasks('Completed', true);
	if(count($openTasks) == 0)
	{
		echo "<h4 align='center'>No Tasks are currently assigned to you.</h4>";
		echo "<br>";
		return;
	}

	echo "<form method='post'>";
	echo "<table><tr>";
	echo "<th width=300>".$CUS_Category_Name."</th>";
	echo "<th width=300>".$CUS_Task_Short."</th>";
	echo "<th width=300>".$CUS_Customer_Title."</th>";
	echo "<th width=300>Status</th>";
	echo "<th width=150>Due Date</th>";
	echo "</tr>";


	$rowcount = 0;
	while($row = array_shift($openTasks))
	{
		echo "<tr><td>".$row->getCategory()."</td>";
		echo "<td>".$row->getLink()."</td><td>";
		echo $row->getCustomer()->getLink();
	  
		$selectName = "status_" . $rowcount;
		echo "</td><td><input type='hidden' name='statusID_$rowcount' value=".$row->getID().">";
		selectStatus($selectName, $row->getStatus());
		echo "</td><td>".$row->getDateDue()."</td>";
		echo "</tr>";
		$rowcount++;
	}
	echo "</table>";
	echo "<input type='hidden' name='maxStatus' value=$rowcount><input type='submit' name='submit' value='Update'>";
	echo "</form>";
}

function mpwUnpaidInvoices($SEmployee)
{
	global $CUS_Task_Short;
	global $CUS_Customer_Title;
	global $CUS_Company_TaxRate;
	
	echo "<h2>Unpaid Invoices</h2>";

	$openTasks = $SEmployee->getTasks();
	$unpaidInvoices = array();
	foreach($openTasks as $task)
	{
		$invID = $task->getInvoice();
		if($invID >= 0)
		{
			$invoice = new Invoice($invID);
			if(!($invoice->isPaid()))
			{
				$unpaidInvoices[] = $invoice;
			}
		}
	}
	if(count($unpaidInvoices) == 0)
	{
		echo "<h4 align='center'>None of your Invoices are currently awaiting payment.</h4>";
		echo "<br>";
		return;
	}

	echo "<form method='post'>";
	echo "<table><tr>";
	echo "<th width=150>Invoice #</th>";
	echo "<th width=300>".$CUS_Customer_Title."</th>";
	echo "<th width=300>".$CUS_Task_Short."</th>";
	echo "<th width=150>Total</th>";
	echo "<th width=100>Pay</th>";
	echo "</tr>";


	$rowcount = 0;
	while($row = array_shift($unpaidInvoices))
	{
		echo "<tr>";
		echo "<td align='center'><a href='editinvoice.php?InvID=".$row->getID()."'>".$row->getID()."</a></td>";
		
		$customer = $row->getCustomer();
		echo "<td>".$customer->getLink()."</td>";
		echo "<td>".$row->getTask()->getLink()."</td>";
		echo "<td align='center'>".money($row->getCost($CUS_Company_TaxRate))."</td>";
		
		$checkName = "checkpaid_" . $rowcount;
		echo "<td align='center'><input type='hidden' name='checkID_$rowcount' value=".$row->getID().">";
		echo "<input type='checkbox' name='$checkName'></td></tr>";
		$rowcount++;
	}
	echo "</table>";
	echo "<input type='hidden' name='maxInvoice' value=$rowcount><input type='submit' name='submit' value='Pay'>";
	echo "</form>";
}


?>
