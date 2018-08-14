<?php
error_reporting(E_ALL & ~E_DEPRECATED);

include_once('dashboardFunctions.php');

$schoolCode = $_POST['schoolCode'];
$class = $_POST['class'];
$section = isset($_POST['section'])?$_POST['section']:'';
$dateRange = $_POST['dateRange'];

$dateRangeArr = explode('~', $dateRange);
$startDate = $dateRangeArr[0];
$endDate = $dateRangeArr[1];

$retArr = getImpactSummaryDetails($schoolCode, $class, $section, $startDate, $endDate);
echo json_encode($retArr);

?>