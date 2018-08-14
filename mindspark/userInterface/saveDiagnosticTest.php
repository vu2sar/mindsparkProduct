<?php

@include("check1.php");
//error_reporting(E_ALL);

if(!isset($_POST["TestID"]) || !isset($_POST["Data"]))
{
	echo "Invalid request!";
	exit;
}
if($mode=="pingServer")
{
	exit();
}
include("functions/comprehensiveModuleFunction.php");
$diagnosticTestID = $_POST["TestID"];
$timeTaken = $_POST["timeTaken"];
$data = json_decode(stripslashes($_POST["Data"]),true);
$debug = (isset($_REQUEST['debug']) && $_REQUEST['debug']!="")?$_REQUEST['debug']:false;
$groupVizArr = array();
$misconceptionsCode = array();
$misconceptionsGenerated = array();
$isAECode = true;
$totalCorrect = 0;
$totalAttempt = 0;
$responseSave = array();
foreach($data as $details)
{
	if($details["result"] == 0)
		$isAECode = false;
	else
		$totalCorrect++;
	$totalAttempt++;
	$groupNo = $details["groupNo"];
	$qcode = $details["qcode"];
	if(!isset($groupVizArr[$groupNo]))
	{
		$groupVizArr[$groupNo] = array();
		$groupVizArr[$groupNo]["Corrects"] = 0;
		$groupVizArr[$groupNo]["Value"] = array();
	}
	array_push($responseSave,"(".$qcode."-".$details["result"]."-".$details["userAnswer"].")");
	$groupVizArr[$groupNo]["Corrects"] += $details["result"];
	array_push($groupVizArr[$groupNo]["Value"],$details["userAnswer"]);
}
$accuracy = round(($totalCorrect*100/$totalAttempt),2);
if($isAECode)
	array_push($misconceptionsGenerated,"AE");
foreach($groupVizArr as $groupNo=>$details)
{
	$valueArr = $details["Value"];
	if(count($valueArr) == 0)
	{
		$groupVizArr[$groupNo]["Value"] = "";
	}
	else if(count($valueArr) == 1)
	{
		$groupVizArr[$groupNo]["Value"] = $groupVizArr[$groupNo]["Value"][0];
	}
	else
	{
		$count = array_count_values($valueArr);
		arsort($count);
		$groupVizArr[$groupNo]["Value"] = "";
		foreach($count as $val=>$getResp)
		{
			if(round(($getResp/count($valueArr))*100,2) > 50)
				$groupVizArr[$groupNo]["Value"] = $val;
		}
	}
}
$sql = "SELECT misconceptionCode, conditionText FROM adepts_diagnosticMisconceptionsCond WHERE diagnosticTestID='$diagnosticTestID'";
$result = mysql_query($sql);
while($row = mysql_fetch_array($result))
{
	$evalStr = str_replace("~"," ",$row[1]);
	$evalStr = preg_replace("/Group ([0-9]+) (Value)/i",'$groupVizArr[$1]["Value"]',$evalStr);
	$evalStr = preg_replace("/Group ([0-9]+) (Corrects)/i",'$groupVizArr[$1]["Corrects"]',$evalStr);
	$evalStr = preg_replace("/Group ([0-9]+),/i",'$groupVizArr[$1]["Corrects"] + ',$evalStr);
	$evalStr = preg_replace("/== ([^\s]) /i",'== "$1"',$evalStr);
	$misconceptionsCode[$row[0]] = trim($evalStr);
}
foreach($misconceptionsCode as $code=>$condition)
{
	try
	{
		$bool = false;
		eval('$bool = ('.$condition.');');
		if($bool == true)
			array_push($misconceptionsGenerated,$code);
	}
	catch(Exception $ex)
	{
		echo $ex->getMessage();
		exit();
	}
}
if(count($misconceptionsGenerated) == 0)
	array_push($misconceptionsGenerated,"UN");
if($debug)
{
	echo "Generated misconceptions: ".implode(",",$misconceptionsGenerated)."\nTimeTaken: ".$timeTaken."\nAccuracy: ".$accuracy."\nUser Response: ".implode(",",$responseSave);
}
else
{
	$testType	=	getTestType($_SESSION['comprehensiveModule_srno']);
	$sql	=	"UPDATE adepts_diagnosticTestAttempts SET misconceptionCodes='".implode(",",$misconceptionsGenerated)."',
				 accuracy='".$accuracy."', timeTaken='".$timeTaken."', studentResponse='".implode(",",$responseSave)."', status=1, attemptedDate='".date('Y-m-d h:i:s')."' 
				 WHERE userID=".$_SESSION["userID"]." AND diagnosticTestID='$diagnosticTestID' AND testType='$testType' AND srno=".$_SESSION['comprehensiveModule_srno'];
	$result	=	mysql_query($sql);
	$_SESSION['diagnosticTest']="";
	if($testType=="pre")
		echo setComprehensiveFlow($misconceptionsGenerated,$_SESSION["userID"]);
	else if($testType=="post")
		echo completeComprehensiveFlow($misconceptionsGenerated,$_SESSION["userID"]);
}
?>