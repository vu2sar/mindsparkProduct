<?php
error_reporting(E_ALL & ~E_DEPRECATED);
$test = 0;

if($test == 1) {
	$schoolCode = '2387554';
	$class = '6';
	$section = 'C';
	$startDate = '2015-02-01';
	$endDate = '2015-02-10';
	// $retArr = getOverallUsageSummary($schoolCode, $class, $section, $startDate, $endDate);
	// $retJson = json_encode($retArr);

	echo "{\"usageSummaryGraphDetails\":{\"zero\":1,\"low\":14,\"average\":10,\"good\":2,\"great\":0,\"classAvg\":\"average\"},\"lowUsageNames\":[\"Sumvith Kiran\",\"Sahithi Chaluvadi\",\"Anushka Sullad\",\"Haritha Kollipara\",\"Sabyasachi Samanta\",\"Keerthana Sunil\",\"Abhay Rao\",\"Neeraj P\",\"Pradhyumna Kadambi\",\"Aryan Kamani\",\"Sreevidhya Alvandi\",\"Trisha Santosh\",\"Arnav Yayavaram\",\"Malavika Nair\"],\"lowAccuracyNames\":[\"Someone\"],\"allTopicsCompletedNames\":[\"Jagadish Ramachandra\",\"Shrinidhi Suneeth\"],\"numerousAttemptsFailureNames\":[\"Someone\"]}";
	exit;
} //TODO delete this block

include_once('dashboardFunctions.php');

$schoolCode = $_POST['schoolCode'];
$class = $_POST['class'];
$section = isset($_POST['section'])?$_POST['section']:'';
$dateRange = $_POST['dateRange'];

$dateRangeArr = explode('~', $dateRange);
$startDate = $dateRangeArr[0];
$endDate = $dateRangeArr[1];

$retArr = getOverallUsageSummary($schoolCode, $class, $section, $startDate, $endDate);
echo json_encode($retArr);

?>