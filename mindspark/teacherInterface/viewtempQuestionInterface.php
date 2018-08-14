<?php
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	//error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);

	//@include("check.php");
	session_start();
	
	$qcode	=	$_GET["qcode"];
	//$type	=	$_GET["type"];
	$theme	=	$_GET["theme"];
	$teacherQuestion=$_GET["teacherQuestion"];
	$_SESSION["userType"]="msAsStudent";
	$modified=0;
	$html5version=0;
	$tmpMode="";
	if(isset($_GET["modified"]))
		$modified=$_GET["modified"];
	if(isset($_GET["research"]) && $_GET["research"]==1)
		$tmpMode="research";
	if(isset($_GET["html5version"]) && $_GET["html5version"]==1)
		$html5version=1;
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>New Interface</title>
</head>

<body>
<form id="frmHidForm" action="../userInterface/question_dev.php" method="post">
	<input type="hidden" name="viewerMode" id="viewerMode" value="viewQuestionAsMs">
	<input type="hidden" name="mode" id="mode" value="firstQuestion">
	<input type="hidden" name="qcode" id="qcode" value="<?=$qcode?>">
	<input type="hidden" name="qno" id="qno" value="1">
	<input type="hidden" name="theme" id="theme" value="<?=$theme?>">
	<input type="hidden" name="quesCategory" id="quesCategory" value="normal">
	<input type="hidden" name="modified" id="modified" value="<?=$modified?>">
	<input type="hidden" name="tmpMode" id="tmpMode" value="<?=$tmpMode?>">
	<input type="hidden" name="html5version" id="html5version" value="<?=$html5version?>">
	<input type="hidden" name="showAnswer" id="showAnswer" value="0">
	<input type="hidden" name="teacherQuestion" id="teacherQuestion" value="<?=$teacherQuestion?>">
</form>
<script>
	window.name="";
	document.getElementById('frmHidForm').submit();
</script>

</body>
</html>