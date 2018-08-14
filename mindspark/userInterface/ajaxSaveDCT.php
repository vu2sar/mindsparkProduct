<?php

@include("check1.php");
if(isset($userID))
{
	
		$keys = array_keys($_POST);
		foreach ($keys as $key){
			${$key} = $_POST[$key];
		}
		$tempArray = explode(",",$results);
		$corrects = array_sum($tempArray);
		$dateTime = date('Y-m-d h:i:s');
		if($dctStage == 3)
		{
			$dctType = "firstDCT";
			$sql = "UPDATE adepts_dctDetails SET firstDCT='$results', firstDCTtime='$timeTaken', firstDCTdate='$dateTime' WHERE ttAttemptID = '$ttAttemptID'";
			$sqlPendingDCT = "UPDATE adepts_dctDetails SET current = '' WHERE ttAttemptID = '$ttAttemptID'";
		}
		else if($dctStage == 4)
		{
			$dctType = "secondDCT";
			$sql = "UPDATE adepts_dctDetails SET secondDCT='$results', secondDCTtime='$timeTaken', secondDCTdate='$dateTime' WHERE ttAttemptID = '$ttAttemptID'";
			$sqlPendingDCT = "UPDATE adepts_dctDetails SET current = '' WHERE ttAttemptID = '$ttAttemptID'";
		}
		else if($dctStage == 2)
		{
			$dctType = "postDCT";
			$sql = "UPDATE adepts_dctDetails SET postDCT='$results', status = CONCAT_WS('',status,'$dctType-$corrects||'), postDCTtime='$timeTaken', postDCTdate='$dateTime' WHERE ttAttemptID = '$ttAttemptID'";
			$sqlPendingDCT = "UPDATE adepts_dctDetails SET current = '' WHERE ttAttemptID = '$ttAttemptID'";
		}
		else
		{
			$dctType = "preDCT";
			$sql = "UPDATE adepts_dctDetails SET preDCT='$results', status = CONCAT_WS('',status,'$dctType-$corrects||'), preDCTtime='$timeTaken', preDCTdate='$dateTime' WHERE ttAttemptID = '$ttAttemptID'";
			$sqlPendingDCT = "UPDATE adepts_dctDetails SET current = '23' WHERE ttAttemptID = '$ttAttemptID'"; //SET next stage to Hidden Numbers..
		}
		//Save status in teacherTopicStatus
		$result = mysql_query($sql) or die(mysql_error());			
	    mysql_query($sqlPendingDCT) or die(mysql_error());
		if($result)
			echo "true";
		else
			echo "false";
}

?>