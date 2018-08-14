<?php
	include_once('dashboardFunctions.php');
	error_reporting(E_ALL & ~E_DEPRECATED);
	
	$retArr = array();
	$schoolCode = $_POST['schoolCode'];
	$class = $_POST['class'];
	$section = isset($_POST['section'])?$_POST['section']:'';
	$topicArr = explode(',', $_POST['topic']);
	$mode = (isset($_POST['mode']))? $_POST['mode']:0;
	if($mode == 1) {
		$startDate = $_POST['startDate'];
		$endDate = $_POST['endDate'];
		$retArr = getTopicReport2($schoolCode, $class, $section, $startDate, $endDate);	
	} else {
		$retArr = getTopicReport($schoolCode, $class, $section, $topicArr, $mode);		
	}

	echo json_encode($retArr);
?>