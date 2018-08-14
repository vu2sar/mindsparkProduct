<?php
class kst_util 
{
var $userName;

//obtain userAttempted data from DB
function getUserAttemptData($userID,$ttattemptID){

	$arr_attemptData = array();
	if(isset($_SESSION['isPostTest']) && $_SESSION['isPostTest'] == 1){
		$attemptID = getPostAttemptID($userID, $ttattemptID);
	} else if($_POST['quesCategory'] == 'kstPostTest') {
		$attemptID = NULL;
	} else {
		$attemptID = getAttemptID($userID, $ttattemptID);
	}
	if(count($attemptID) == 0){
		return 0;
	} else {
	$query = "SELECT group_concat(concat('X',qCode,'=',if(R=2,0,R))) as cols FROM educatio_adepts.kst_diagnosticQuestionAttempt WHERE userID=$userID and typeAsked='Normal' and attemptID=$attemptID";
	$result = mysql_query( $query) or die( "Invalid query".$query);
    if( mysql_num_rows( $result)) {
       $line = mysql_fetch_array( $result, MYSQL_ASSOC);
	}
	$whereClause = $line['cols'];
	if(count($whereClause) == 0){
		return 0;
	} else {
	$askedQuestions = explode(" and ",$whereClause);
	$askedQuestions = explode(",",$askedQuestions[0]);
	foreach($askedQuestions as $key=>$value){
		$temp = explode("=",$value);
		$arr_attemptData[$temp[0]] = (int)$temp[1];
	}
	return json_encode($arr_attemptData);
	}
}
}

//Call the Python API here.
function getNextQuestionFromAPI($userID,$ttcode,$attempt_data,$mode='',$qcode='',$response='')
{
	$quesLoadTime = -1;
	$quesLoadStartTime = microtime(true);
	 if($mode =='firstQuestion'){
		$prev_question = 0;
		$answer_response = 0;
		$attempt_flag = 0;
	 } else {
		$prev_question = "X".$qcode;
		$answer_response = $response;
		$attempt_flag = 1;
	 }
	
	$ttcode = $_SESSION['schoolCode']."_".$_SESSION['childClass']."_".$_SESSION['childSection']."_".$ttcode;
	$request = array('userID' => $userID, 'prev_question' => $prev_question, 'answer_response' => $answer_response, 'attempt_flag'=>$attempt_flag, 'attemptData' => $attempt_data,'ttcode' => $ttcode);
	$url = 'http://127.0.0.1/';
	// Send using curl3
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url); // URL to post
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); // return into a variable
	curl_setopt( $ch, CURLOPT_HTTPHEADER, 'Content-Type:application/x-www-form-urlencoded' ); // headers from above
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_PORT, 5000);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$request);
	$result = json_decode(curl_exec( $ch )); // runs the post
	//perform tasks on $result
	curl_close($ch);
	$quesLoadEndTime = microtime(true);
	$quesLoadTime = $quesLoadEndTime - $quesLoadStartTime;  // returns in seconds
	return array('questionCode' => $result , 'quesLoadTime' => $quesLoadTime);
}
}
?>
