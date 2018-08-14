<?php
error_reporting(E_ALL & ~E_DEPRECATED);


include("../userInterface/classes/clsStudentWiseTopicsUsage.php");
include_once('functions/schoolwiseUsageAjax.php');

define("CONNECT", 0);

if(CONNECT == 0) {
	include("../slave_connectivity.php");
	error_reporting(E_ERROR & ~E_DEPRECATED) ;
	mysql_select_db("educatio_adepts") or die (mysql_errno());	
}
if(CONNECT == 1) {
	$link = mysql_connect("192.168.0.15","ms_analysis","sl@vedb@e!")  or die (mysql_errno()."-".mysql_error()."Could not connect to localhost");
	mysql_select_db ("educatio_adepts")  or die ("Could not select database".mysql_error());
	putenv('TZ=IST-5:30');
	$studentID=126443;
	$startDate='2015-02-02';
	$endDate='2015-06-06';
	$class='3';
	$section='A';
	//$ret=getTimeSpentHomeAndSchool($studentID, $class, $section, $startDate, $endDate);
   // echo $ret;
    //exit;
}



$studentID = $_POST['studentID'];
$class = isset($_POST['childclass'])?$_POST['childclass']:'';
$section = isset($_POST['section'])?$_POST['section']:'';
$dateRange = $_POST['dateRange'];

$dateRangeArr = explode('~', $dateRange);
$startDate = $dateRangeArr[0];
$endDate = $dateRangeArr[1];
$mode=$_POST['mode'];
$resultSummary=array();


switch($mode)
{

case 'timeSpentHomeSchool':
		$retArr = getTimeSpentHomeAndSchool($studentID, $class, $section, $startDate, $endDate);
		echo $retArr;

break;
case 'timeSpentAcrossTopicsAjax':
		$retArr = getTimeSpentAcrossTopics($studentID, $class,$startDate, $endDate);
		echo $retArr;
break;
case 'timeSpentActivitiesQuestAjax':
		$retArr = getTimeSpentActivitiesAndQuestions($studentID, $class,$startDate, $endDate);
		echo $retArr;

break;
case 'timeSpentBarChartAjax':
		$resultSummary = timeSpentperDayBarChart($studentID,$startDate, $endDate);
		
		$tag=$resultSummary['tag'];
		$mainArr=$resultSummary['tableData'];
		echo json_encode($resultSummary['tableData']).'~'.$tag;

break;
case 'topicProgressCategoriesAjax':
		$retArr = getTopicProgressAcrossCategories($studentID, $class,$startDate, $endDate);
		echo $retArr;
break;

case 'getnoofquesthomeschoolAjax':
		$retArr = getNoofQuesHomeSchool($studentID,$startDate, $endDate);
		echo $retArr;

break;
case 'getnoofquestAcrossTopicsAjax':
		$retArr = getNoofQuestAcrossTopics($studentID,$class,$startDate, $endDate);
		echo $retArr;
break;
case 'AccuracyForTopicsAjax':
		$retArr = getAccuracyForStudentCategories($studentID,$class,$startDate, $endDate);
		echo $retArr;
break;

case 'getTopicProgressSummaryDetailsAjax':
		$retArr = getTopicProgressSummaryDetailsChart($studentID, $class, $section, $startDate, $endDate);
		echo $retArr;
break;
}

?>