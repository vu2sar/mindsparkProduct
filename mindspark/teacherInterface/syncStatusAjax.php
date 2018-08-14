<?php
set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
@include("../userInterface/check1.php");
	$schoolCode = $_POST['schoolCode'];
	$mode = $_POST['mode'];
	
	
	if($mode=="offlineSyncStatus")
	{

	// Check if the sync file exists
	$t=time();
	$syncLog = array();
	$currentTime = date("Y-m-d H:i:s",$t);
	$syncFile = "../sync/.sync";
	if (file_exists($syncFile)) 
		$syncOngoing = "In Progress";
	else
		$syncOngoing = "Stopped";
		
	// Gett last synced Date
	$sql = "Select lastSyncDate from adepts_sync_status where schoolCode=$schoolCode order by lastModified desc limit 1";
	$result = mysql_query($sql);
	if($line = mysql_fetch_array($result))
	$lastSyncedDate = $line['lastSyncDate'];
	
	//get next sync
	$sql = "Select syncTimeMorning,syncTimeEvening from adepts_offlineSchools where schoolCode=$schoolCode";
	$result = mysql_query($sql);
	if($line = mysql_fetch_array($result))
	{
		$schoolSyncTimeMorning = $line['syncTimeMorning'];
		$schoolSyncTimeEvening = $line['syncTimeEvening'];
	}
	
	if($schoolSyncTimeMorning - $lastSyncedDate < 0)
	$nextSync = $schoolSyncTimeEvening;
	else
	$nextSync = $schoolSyncTimeMorning;
	
	//Get last Synced Date for local
	$sql = "Select lastSyncDate from adepts_sync_status where schoolCode=$schoolCode and type='local' order by lastModified desc limit 1";
	$result = mysql_query($sql);
	if($line = mysql_fetch_array($result))
	$lastSyncedDateLocal = $line['lastSyncDate'];
	
	//Get last Synced Date for live
	$sql = "Select lastSyncDate from adepts_sync_status where schoolCode=$schoolCode and type='live' order by lastModified desc limit 1";
	$result = mysql_query($sql);
	if($line = mysql_fetch_array($result))
	$lastSyncedDateLive = $line['lastSyncDate'];
	
	echo  $syncOngoing.'~'.$lastSyncedDate.'~'.$nextSync.'~'.date("d-m-Y H:i:s",strtotime($lastSyncedDateLocal)).'~'.date("d-m-Y H:i:s",strtotime($lastSyncedDateLive));
	}
	
	
	else if($mode="syncLog")
	{
	?>
	
	<table class="hor-zebra" style="display:table">	
	<thead>
	<tr>
	<th> Sync From </th>
	<th> Sync Date </th>
	</tr>
	</thead>
	<tbody id="logTableBody">
	
	<?php
		//Get Sync Log
		$sql = "Select type,lastSyncDate from adepts_sync_status where schoolCode=$schoolCode order by lastModified desc limit 10";
		$result = mysql_query($sql);
		$i=0;
		while($line = mysql_fetch_array($result))
		{
			if($i%2==0)
			echo "<tr class='odd'>";
			else
			echo "<tr class='even'>";
			echo "<td>" . ucfirst($line['type']) . "</td>";
			echo "<td>" . $line['lastSyncDate'] . "</td>";
			echo "</tr>";
			
			$i++;
		}
	
	}
	?>
	
	 </tbody>
		</table>