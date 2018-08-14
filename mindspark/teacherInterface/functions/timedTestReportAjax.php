<?php
	include_once('dashboardFunctions.php');
	error_reporting(E_ALL & ~E_DEPRECATED);
	
	$retArr = array();
	$schoolCode = $_POST['schoolCode'];
	$class = $_POST['class'];
	$section = isset($_POST['section'])?$_POST['section']:'';
	$topic = $_POST['topic'];
	$topicDesc = $_POST['topicDesc'];

	$retArr = getTimedTestReport($schoolCode, $class, $section, $topic, $topicDesc);
	echo json_encode(array_values($retArr));
?>