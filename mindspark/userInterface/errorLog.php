<?php


include_once("check1.php");

//$mailTo = "bhushan.kothari@ei-india.com,chirag.vijay@ei-india.com";
//$mailTo = "chirag.vijay@ei-india.com";
//$subject = "Result 127 Script";
//echo $msg = "Query: ".implode(" , ",$_POST);

//mail($mailTo,$subject,$msg);
if(isset($_POST["accessToken"]) && $_POST["accessToken"]!="")
{
	$accessToken	=	$_POST["accessToken"];
	mysql_query("DELETE FROM parentApiAccesstoken WHERE accessToken='$accessToken'");
	exit();
}

if(isset($_POST["typeErrorLog"]) && $_POST["typeErrorLog"]!="")
{
	$typeErrorLog	=	$_POST["typeErrorLog"];
	$msgErrorLog	=	implode(" , ",$_POST);
	$errorURL		=   $_POST["errorURL"];
	$errorline		=   $_POST["errorline"];
	$qcode		=   $_POST["qcode"];
	sendDataCheckMail($msgErrorLog,$typeErrorLog,$errorURL,$errorline,$qcode);
}

function sendDataCheckMail($msg,$type,$errorURL,$errorline,$qcode)
{
	if($type=="1")
	{
		$type	=	"error127ScriptProcessAns";
	}
	else if($type=="2")
	{
		$type	=	"error127NextQues";
	}
	else if($type=="3")
	{
		$type	=	"error127EventListenerAfter";
	}
	else if($type=="4")
	{
		$type	=	"error127EventListenerBefore";
	}
	else if($type=="5")
	{
		$type	=	"loadingControllerSubmitAns";
	}
	else if($type=="6")
	{
		$type	=	"loadingControllerResponse";
	}
	else if($type=="7")
	{
		$type	=	"jsonParsingError";
	}
	else if($type=="8")
	{
		$type	=	"getNextQuesUndefined";
	}
	else if($type=="9")
	{
		$type	=	"submitAnswer";
	}
	else if($type=="10")
	{
		$type	=	"jsError";
	}
	
	if(isset($_POST["sessionID"]))
	{
		$sessionID	=	$_POST["sessionID"];
		$msg	.=	"Current Session ID - ".$_SESSION['sessionID'];
	}
	else 
		$sessionID	=	$_SESSION['sessionID'];
	$sq	=	'INSERT INTO adepts_errorLogs SET bugType="'.$type.'",bugText="'.$msg.'",url="'.$errorURL.'",line="'.$errorline.'",qcode="'.$qcode.'",userID="'.$_SESSION['userID'].'",sessionID='.$sessionID.',schoolCode="'.$_SESSION['schoolCode'].'"';
	$rs	=	mysql_query($sq) or die(mysql_error());
	mysql_close();
}
?>