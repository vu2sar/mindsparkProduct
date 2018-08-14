<?php

include "connect.php";
$keys = array_keys($_REQUEST);
	foreach ($keys as $key)
	{
		${$key} = $_REQUEST[$key];
	}
	
	
	$query1 = "INSERT INTO PS_studentResp (data, timeTaken, qID, imagefile) VALUES ('$stData', $timeTaken, '$qpage', '$imID')";
	$result1 = mysql_query($query1) or die (mysql_error());
?>