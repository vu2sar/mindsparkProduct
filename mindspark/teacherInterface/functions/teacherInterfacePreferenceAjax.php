<?php

	include("../../connectivity.php");
	error_reporting(0);
	$userID = $_POST['userID'];
	$mode = $_POST['mode'];
	if(strcasecmp($mode, "GET") == 0) {
		getInterfacePreference($userID);
	} else {
		$interfacePreference = $_POST['interfacePreference']; 
		$isFirstLogin	= $_POST['isFirstLogin'];	
		setInterfacePreference($userID, $interfacePreference, $isFirstLogin);		
	}

	function getInterfacePreference($userID) {
		$query = "SELECT interfacePreference, isFirstLogin FROM teacherInterfacePreferences WHERE userID = $userID";
		$result = mysql_query($query) or die(mysql_error());
		$line   = mysql_fetch_assoc($result);
		$interfacePreference = $line['interfacePreference'];
		$isFirstLogin = $line['isFirstLogin'];
		$retArr = array("interfacePreference" => $interfacePreference, "isFirstLogin" => $isFirstLogin);
		echo json_encode($retArr);
	}

	function setInterfacePreference($userID, $interfacePreference, $isFirstLogin) {
		$query = "INSERT INTO teacherInterfacePreferences (userID, interfacePreference, isFirstLogin) 
			VALUES ($userID, '$interfacePreference', $isFirstLogin)
			ON DUPLICATE KEY UPDATE interfacePreference='$interfacePreference', isFirstLogin=$isFirstLogin";
		if(mysql_query($query)) {
			echo json_encode(array("result" => 1));
		} else {
			echo json_encode(array("result" => mysql_error()));
		}		
	}
?>