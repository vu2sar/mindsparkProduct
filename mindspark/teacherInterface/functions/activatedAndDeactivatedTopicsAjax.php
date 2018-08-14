<?php
	include_once('functions.php');
	include_once('dashboardFunctions.php');
	error_reporting(E_ALL & ~E_DEPRECATED);
	$retArr = array();
	$schoolCode = $_POST['schoolCode'];
	$class = $_POST['class'];
	$category = isset($_POST['category'])?$_POST['category']:"";
	$section = isset($_POST['section'])?$_POST['section']:'';
	$retArr = getActivatedTopicsForSelectedClassAndSection($schoolCode, $class, $section, $category);
	echo json_encode($retArr);
?>