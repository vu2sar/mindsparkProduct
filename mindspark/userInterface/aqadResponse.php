<?php
//include_once("../../constants.php");
//studentID, qcode, papercode,student_answer,product, score,entered_date (FIELDS IN DATABASE)(Fields To be send)
include("check1.php");
$aqadDate = $_POST["aqadDate"];
$date = date('Y-m-d H:i:s'); //Current date
$score="'".$_REQUEST['score']."'";
$childClass = $_SESSION["childClass"];
$arr = array(array('studentID' => $_REQUEST['studentID'], 'qcode' => $_REQUEST['qcode'], 'entered_date' => $date , 'student_answer' => $_REQUEST['student_answer'] ,'product' => 'MS' ,'papercode' => $_REQUEST['paperCode'] ,'score' =>$score ,'childClass' =>$childClass));
$data = json_encode($arr);
$ch = curl_init();
// 127.0.0.1:8080 for 192.168.0.61
if($_SERVER["HTTP_HOST"] == "localhost" || $_SERVER["HTTP_HOST"] == "127.0.0.1:8080"){
	$crlURL = "http://192.168.0.7/mailers/question_a_day/getaqad_response.php";
}
else{
	$crlURL = "http://www.educationalinitiatives.com/mailers/question_a_day/getaqad_response.php";
}
$curlConfig = array(
    CURLOPT_URL => $crlURL, //Url where data should be posted
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_NOBODY => false,
    CURLOPT_POSTFIELDS => $data, //Post array
);
 
curl_setopt_array($ch, $curlConfig);
$result = curl_exec($ch);
curl_close($ch);
echo $result;
$explaination = $_REQUEST['explaination'];
if(trim($explaination)!='')
{
	require_once 'functions/functions.php';
	$date = date_parse($aqadDate);
	if ($date["error_count"] == 0 && checkdate($date["month"], $date["day"], $date["year"]))
	{
		$sq = "INSERT INTO adepts_errorLogs SET bugType='aqadDate',bugText='$aqadDate',userID='".$_REQUEST['studentID']."'";
		$rs = mysql_query($sq);
	}
	else
	{
		$aqadDate = date("Y-m-d");
	}
	addAQADExplaination($aqadDate, $_REQUEST['class'], $_REQUEST['studentID'], $explaination);
}
?>