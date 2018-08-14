<?php
error_reporting(E_ALL & ~E_DEPRECATED);
$test = 0;
if($test == 1) {
	// echo "{\"ttProgress\":[{\"ttCode\":\"TT21279\",\"ttDesc\":\"Data representation - Custom 1\",\"startProgress\":22.22,\"endProgress\":35.81,\"currentProgress\":36.58},{\"ttCode\":\"TT037\",\"ttDesc\":\"Area and perimeter\",\"startProgress\":48.15,\"endProgress\":60.09,\"currentProgress\":60.09}],\"totalHigherLevelReached\":0}";
		echo "{\"ttProgress\":[{\"ttCode\":\"TT21279\",\"ttDesc\":\"Data representation - Custom 1\",\"startProgress\":22.22,\"endProgress\":35.81,\"currentProgress\":36.58},{\"ttCode\":\"TT037\",\"ttDesc\":\"Area and perimeter\",\"startProgress\":48.15,\"endProgress\":60.09,\"currentProgress\":60.09},{\"ttCode\":\"TT022\",\"ttDesc\":\"Angles\",\"startProgress\":29.82,\"endProgress\":36.77,\"currentProgress\":36.77}],\"totalHigherLevelReached\":0}";
	exit;
}

include_once('dashboardFunctions.php');
//TODO move to ajaxRequest.php
$schoolCode = $_POST['schoolCode'];
$class = $_POST['class'];
$section = isset($_POST['section'])?$_POST['section']:'';
$dateRange = $_POST['dateRange'];

$dateRangeArr = explode('~', $dateRange);
$startDate = $dateRangeArr[0];
$endDate = $dateRangeArr[1];

$retArr = getTopicProgressSummaryDetails($schoolCode, $class, $section, $startDate, $endDate);
echo json_encode($retArr);

?>