<?php
include("check1.php");
include_once("constants.php");

if(isset($_GET['oldSessionID']))
{
	$elapsed_time = time() - $_GET['oldStartTime'];
	if($elapsed_time<2700)
	{
		$sq = "UPDATE ".TBL_SESSION_STATUS." SET endType=concat_ws(',',endType,'13'),endTime=NOW(),logout_flag=1 WHERE sessionID=".$_GET['oldSessionID'];
		mysql_query($sq);
	}
}
/*
session_destroy();
if(isset($_SERVER['HTTP_COOKIE']))
{
	$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
	foreach ($cookies as $cookie)
	{
		$parts = explode('=', $cookie);
		$name = trim($parts[0]);
		setcookie($name, '', time() - 1000);
		setcookie($name, '', time() - 1000, '/');
	}
}*/

?>

<!DOCTYPE HTML>
<html>
<title>Mindspark</title>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9">
<link href="css/login/login.css" rel="stylesheet" type="text/css">
<style>
.errorMsg {
	font-family: 'Conv_HelveticaLTStd-Roman';
	font-size:18px;
	margin-top:120px;
}
</style>

<script>

try {
	sessionStorage.setItem('sessionID','<?=$_SESSION["sessionID"]?>');
}
catch(err) {
	
}

</script>
</head>
<body onLoad="qs()" >
<div id="header">
	<div id="eiColors">
    	<div id="orange"></div>
        <div id="yellow"></div>
        <div id="blue"></div>
        <div id="green"></div>
    </div>
</div>
<div id="head" style="">
	<div id="logo" ></div>
</div>
<div class="errorMsg">
<table width="80%" align="center" border="0">
<tr>
<td align="right"><img src="assets/login/warning.png"></td>
<td nowrap><font color="red"><b>Note</b></font> : Opening Mindspark in multiple tabs is <font color="red"><b>NOT</b></font> allowed.</td>
</tr>
<tr>
<td nowrap colspan="2">&nbsp;</td>
</tr>
<tr>
<td nowrap colspan="2">&nbsp;</td>
</tr>
<tr>
<td nowrap colspan="2">Click <font color="red"><a href="logout.php?mode=13">here</a></font> to logout of all other Mindspark sessions and login again.<br></td>
</tr>
</table>	
<?php include("footer.php"); ?>