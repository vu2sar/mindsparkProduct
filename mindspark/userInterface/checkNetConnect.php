<?php
include("check1.php");
include("constants.php");
if(!isset($_SESSION['userID']) && isset($_POST))
{
	echo false;
	exit();
}

if(isset($_POST["mode"]) && $_POST["mode"]=="check")
	echo "OK";
else if(isset($_POST["mode"]) && $_POST["mode"]=="checkPrevQues")
{
	$quesno             = $_POST['qno'];
	$sessionID          = $_SESSION['sessionID'];
	$userID				= $_SESSION['userID'];
	$qcode				= $_POST['qcode'];
	$sq	=	"SELECT srno FROM ".TBL_QUES_ATTEMPT_CLASS."
			 WHERE sessionID=$sessionID AND userID=$userID AND qcode=$qcode AND questionNo=$quesno";
	$rs	=	mysql_query($sq);
	if($rw=mysql_fetch_array($rs))
		echo "copy";
	else
		echo "fresh";
}
?>