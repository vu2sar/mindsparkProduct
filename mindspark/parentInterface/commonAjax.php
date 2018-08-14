<?php
include("header.php");
$mode = isset($_REQUEST["mode"])?$_REQUEST["mode"]:"";
switch ($mode)
{
    case "endSessionTime":
		$query  = "UPDATE adepts_parentSessionStatus SET endTime='".date("Y-m-d H:i:s")."' WHERE sessionID=".$_SESSION['sessionID'];
		$result = mysql_query($query) or die(mysql_error());
	break;
}
?>