<?php
	include_once('dashboardFunctions.php');
	error_reporting(E_ALL & ~E_DEPRECATED);
	
	$retArr = array();
	$schoolCode = $_POST['schoolCode'];
	$class = $_POST['class'];
	$section = isset($_POST['section'])?$_POST['section']:'';
	$topic = $_POST['topic'];
	$startDate = isset($_POST['startDate'])?$_POST['startDate']:"";
	$endDate = isset($_POST['endDate'])?$_POST['endDate']:"";
	$retArr = getDailyPracticeReport($schoolCode, $class, $section, $topic, $startDate, $endDate);
	echo json_encode(array_values($retArr));
?>