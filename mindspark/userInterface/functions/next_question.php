<?php
	function nextDiagnosticQuestion($userID,$sessionID,$subjectNo,$qcode,$response,$quesno,$seconds,$responseResult,$dynamic=0,$dynamicParams,$eeresponse="NO_EE",$teacherTopicAttemptID,$teacherTopicCode)
	{
		$childClass	=	$_SESSION['childClass'];
		$attemptID	=	$_SESSION['comprehensiveModule_srno'];
		$diagnosticTestID	=	$_SESSION['diagnosticTest'];
		$groupID	=	$_SESSION["diagnosticTestQuestions"][$_SESSION['diagnosticTest']][0][1];
		// $testType	=	getTestType($_SESSION['comprehensiveModule_srno']);
		$testType = $_SESSION['diagnosticTestType'];
		$sq	=	"INSERT INTO adepts_diagnosticQuestionAttempt (userID,questionNo,qcode,A,S,R,groupID,diagnosticTestID,attemptID,sessionID)
				 VALUES 
				 ($userID,$quesno,$qcode,'$response','$seconds',$responseResult,$groupID,'$diagnosticTestID',$attemptID,$sessionID)";
		$rs	=	mysql_query($sq) or die(mysql_error().$sq);
		$quesAttempt_srno = mysql_insert_id();
		if($dynamic)
		{
			$query	=	"INSERT INTO adepts_dynamicParameters (userID, qcode, quesAttempt_srno, parameters, mode, class, lastModified) VALUES
						 ($userID, $qcode, ".$quesAttempt_srno.", '$dynamicParams', 'diagnosticTest','".$childClass."', '".date("Y-m-d H:i:s")."')";
			mysql_query($query);
		}
		if($responseResult)
			$_SESSION['correctInARow']++;
		else
			$_SESSION['correctInARow']=0;
		if($_SESSION['correctInARow']==3)
		{
			addSparkies(1, $sessionID);
			$_SESSION['sparkie']['normal'] += 1;
			$_SESSION['correctInARow']=0;
		}
		unset($_SESSION["diagnosticTestQuestions"][$_SESSION['diagnosticTest']][0]);
		if(count($_SESSION["diagnosticTestQuestions"][$_SESSION['diagnosticTest']])==0)
			return saveDignosticTest($diagnosticTestID,$attemptID,$userID,$testType,$teacherTopicAttemptID,$teacherTopicCode);
		else
			return getDiagnosticQcode($diagnosticTestID);
	}
	function nextKstDiagnosticQuestion($userID,$sessionID,$subjectNo,$qcode,$response,$quesno,$seconds,$responseResult,$dynamic=0,$dynamicParams,$eeresponse="NO_EE",$teacherTopicAttemptID,$teacherTopicCode,$kst_util_obj)
	{
		$childClass	=	$_SESSION['childClass'];
		if(isset($_SESSION['isPostTest']) && $_SESSION['isPostTest'] == 1){
			$attemptID = getPostAttemptID($userID, $teacherTopicAttemptID);
		} else {
			$attemptID = getAttemptID($userID, $teacherTopicAttemptID);
		}
		$query = "SELECT learning_objective_qcode FROM educatio_adepts.learning_objective_qcode_cluster_mapping_Fractions WHERE qcode=$qcode";
		$rs	=	mysql_query($query) or die(mysql_error().$query);
		$row = mysql_fetch_array($rs, MYSQL_ASSOC);
		$learning_objective_code	=	$row['learning_objective_qcode'];
		if(isset($_SESSION['isPredicted']) && $_SESSION['isPredicted'] == 0 ){
			$typeAsked = 'Normal';
			$predictedR = -1;
			$sq	=	"INSERT INTO educatio_adepts.kst_diagnosticQuestionAttempt (userID,questionNo,qcode,A,S,R,learning_objective_code,attemptID,predictedR,typeAsked,sessionID)
					VALUES
					($userID,$quesno,$qcode,'$response','$seconds',$responseResult,'$learning_objective_code',$attemptID,$predictedR,'$typeAsked',$sessionID)";
			$rs	=	mysql_query($sq) or die(mysql_error().$sq);

			$quesAttempt_srno = mysql_insert_id();
		} else {
			$typeAsked = 'Additional';
			$predictedR = $responseResult;
			$sq = "UPDATE educatio_adepts.kst_diagnosticQuestionAttempt SET questionNo=$quesno, typeAsked='Additional', A='$response',S='$seconds',R=$responseResult where attemptID=$attemptID and userID=$userID and qcode=$qcode  and typeAsked='Predicted'";
			$rs	=	mysql_query($sq) or die(mysql_error().$sq);
			$quesAttempt_srno = mysql_insert_id();
		}
		if($dynamic)
		{
			$query	=	"INSERT INTO adepts_dynamicParameters (userID, qcode, quesAttempt_srno, parameters, mode, class, lastModified) VALUES
						 ($userID, $qcode, ".$quesAttempt_srno.", '$dynamicParams', 'diagnosticTest','".$childClass."', '".date("Y-m-d H:i:s")."')";
			mysql_query($query);
		}
		if($responseResult)
			$_SESSION['correctInARow']++;
		else
			$_SESSION['correctInARow']=0;
		if($_SESSION['correctInARow']==3)
		{
			addSparkies(1, $sessionID);
			$_SESSION['sparkie']['normal'] += 1;
			$_SESSION['correctInARow']=0;
		}
		$mode='submitAnswer';
		if(isset($_SESSION['isPredicted']) && $_SESSION['isPredicted'] == 0 ){
			$qInfo = $kst_util_obj -> getNextQuestionFromAPI($userID,$teacherTopicCode,$_SESSION['attemptData'],$mode,$qcode,$responseResult);	//api here
			if (strpos($qInfo['questionCode'], 'done') !== false){ //searches for the word done in the predicted set
					//store the result of predicted questions in database
					//this will be in string. should convert to proper format
					$predicted_qcodeArray = $qInfo['questionCode'];
					$predicted_arr = predictedSet($predicted_qcodeArray);
					//conversion to proper format
					array_shift($predicted_arr); //remove the done element from predicted set
					storePredictedSet($predicted_arr,$_SESSION['sessionID'],$_SESSION['userID'],$_SESSION['teacherTopicAttemptID']);
					//Store predicted list in DB
					$qInfo = getAdditionalQuestionsForKst($userID,$teacherTopicAttemptID,$_SESSION['kstdiagnosticTest']['addtnlQues'],$_SESSION['schoolCode'],$teacherTopicCode,$sessionID);
				} else {
					$learningCode = $qInfo['questionCode'];
					$query = "SELECT qcode FROM educatio_adepts.learning_objective_qcode_cluster_mapping_Fractions WHERE learning_objective_qcode='$learningCode'";
					$rs	=	mysql_query($query) or die(mysql_error().$query);
					$row = mysql_fetch_array($rs, MYSQL_ASSOC);
					$qInfo['questionCode'] = $row['qcode'];
				}
			} else {
				//call function to obtain additional questions and return that
				$qInfo = getAdditionalQuestionsForKst($userID,$_SESSION['teacherTopicAttemptID'],$_SESSION['kstdiagnosticTest']['addtnlQues'],$_SESSION['schoolCode'],$teacherTopicCode,$sessionID);
			}
		return $qInfo;
	}


	function getAttemptID($userID, $teacherTopicAttemptID){
		$query = "SELECT attemptID FROM educatio_adepts.kst_diagnosticTestAttempts WHERE userID=$userID and ttAttemptID=$teacherTopicAttemptID";
		//echo $query;die;
		$rs	=	mysql_query($query) or die(mysql_error().$query);
		$line = mysql_fetch_array($rs, MYSQL_ASSOC);
		return	$line['attemptID'];
	}

	function getPostAttemptID($userID, $teacherTopicAttemptID){
		$query = "SELECT attemptID FROM educatio_adepts.kst_diagnosticTestAttempts WHERE userID=$userID and ttAttemptID=$teacherTopicAttemptID and testType='Post Test'";
		$rs	=	mysql_query($query) or die(mysql_error().$query);
		$line = mysql_fetch_array($rs, MYSQL_ASSOC);
		return	$line['attemptID'];
	}

	function storePredictedSet($predicted_arr,$sessionID,$userID,$teacherTopicAttemptID){
		if(isset($_SESSION['isPostTest']) && $_SESSION['isPostTest'] == 1){
			$attemptID = getPostAttemptID($userID, $teacherTopicAttemptID);
		} else {
			$attemptID = getAttemptID($userID, $teacherTopicAttemptID);
		}
		foreach ($predicted_arr as $key => $item){
			$learning_objective_code = $key;
			//query to fetch this from the mapping table.
			$query = "SELECT qcode FROM educatio_adepts.learning_objective_qcode_cluster_mapping_Fractions WHERE learning_objective_qcode='$learning_objective_code'";
			$rs	=	mysql_query($query) or die(mysql_error().$query);
			$row = mysql_fetch_array($rs, MYSQL_ASSOC);
			$qcode	=	$row['qcode'];
			$query = "INSERT INTO educatio_adepts.kst_diagnosticQuestionAttempt (userID, qcode, predictedR, attemptID, learning_objective_code, typeAsked, sessionID)
					  SELECT * FROM (SELECT $userID, $qcode, $item, $attemptID,'$learning_objective_code','Predicted', $sessionID) AS tmp
					  WHERE NOT EXISTS (SELECT qcode FROM educatio_adepts.kst_diagnosticQuestionAttempt WHERE qcode = $qcode and userID = $userID and attemptID = $attemptID and typeAsked='Predicted') LIMIT 1";
			mysql_query($query) or die("2".mysql_error());
		}
	}
	//This function is used to convert the predicted set returned by API into proper format. 
	function predictedSet($predicted_qcodeArray){
		$predicted_questions_final = array();
		$predicted_questions = $predicted_qcodeArray;
		$predicted_questions = str_replace('"','',$predicted_questions);
		$predicted_questions = ltrim($predicted_questions,'[');
		$predicted_questions = rtrim($predicted_questions,']');
		$predicted_questions = ltrim($predicted_questions,'{');
		$predicted_questions = rtrim($predicted_questions,'}');
		$predicted_questions = explode(",",$predicted_questions);
		foreach($predicted_questions as $data){
   			$a = explode(':',$data);
  			$predicted_questions_final[$a[0]] = $a[1];
		}
		return $predicted_questions_final;
	}
 	function getAdditionalQuestionsForKst($userID,$teacherTopicAttemptID,$additionalQues,$schoolCode,$teacherTopicCode,$sessionID){
		if(isset($_SESSION['isPostTest']) && $_SESSION['isPostTest'] == 1){
			$attemptID = getPostAttemptID($userID, $teacherTopicAttemptID);
		} else {
			$attemptID = getAttemptID($userID, $teacherTopicAttemptID);
		}
		//obtain list of predicted and additional Qcodes
		$query = "SELECT qcode,typeAsked  FROM educatio_adepts.kst_diagnosticQuestionAttempt WHERE userID=$userID and attemptID=$attemptID and typeAsked <> 'Normal'";
		$rs	=	mysql_query($query) or die(mysql_error().$query);
		while ($line = mysql_fetch_assoc($rs))
		{
			if($line['typeAsked'] == 'Predicted'){
				$arr_predicted[] = $line['qcode']; //list of predicted
			}
			if($line['typeAsked'] == 'Additional'){
				$arr_askedAdd[] = $line['qcode']; //list of asked additional questions
			}
		}
		if($additionalQues == 0 || count($arr_askedAdd) == $additionalQues || count($arr_predicted) == 0){
			//test is over. close it
			$query = "SELECT qcode,learning_objective_code,R FROM educatio_adepts.kst_diagnosticQuestionAttempt WHERE userID=$userID and attemptID=$attemptID and typeAsked IN ('Normal','Additional')";
			$rs	=	mysql_query($query) or die(mysql_error().$query);
			while ($line = mysql_fetch_assoc($rs)) 
			{
				if($line['R'] == 0 || $line['R'] == 2){
					$misconceptionCodes[] = $line['learning_objective_code'];
				}
				else {
					$correctCodes[] = $line['learning_objective_code'];
				}
			}
			$correct = count($correctCodes);
			$wrong = count($misconceptionCodes);
			$all = count($misconceptionCodes)+count($correctCodes);

			if(!array_filter($misconceptionCodes)){
				$misconceptionCodes = 0;
				$accuracy = 100;
			} else {
				$misconceptionCodes = implode(",",$misconceptionCodes);
				$correctCodes = implode(",",$correctCodes);
				$accuracy = ($correct/$all) * 100;
			}

			$query = "SELECT sum(S) as timeTaken FROM  educatio_adepts.kst_diagnosticQuestionAttempt WHERE userID=$userID and attemptID=$attemptID";
			$rs	=	mysql_query($query) or die(mysql_error().$query);
			$row = mysql_fetch_assoc($rs,MYSQL_ASSOC);
			$timeTaken = $row['timeTaken'];

			$query = "UPDATE educatio_adepts.kst_diagnosticTestAttempts SET status=1, misconceptionCodes='$misconceptionCodes', timeTaken=$timeTaken, accuracy=$accuracy where attemptID=$attemptID";
			$rs	=	mysql_query($query) or die(mysql_error().$query);
			$username = $_SESSION['childName'];
			if($_SESSION['isPostTest'] != 1){
				$testType = $_SESSION['kstdiagnosticTest']['featureType'];
					if(preg_match('(with pre-requisite)', strtolower($testType)) === 1){
					//check whether it has misconception code or not
					$misconception = $misconceptionCodes;
					if(count($misconception!=0)){
						$_SESSION['current_cluster'] = get_current_cluster($teacherTopicCode,$userID);
						$allClustersList = get_selectedAndListOfClusters($teacherTopicCode);
						$misconceptionCodesList = array_diff(explode(",",$misconception),explode(",",$allClustersList['currently_selectedClusters']));
						if(count($misconceptionCodesList)!=0){
							$_SESSION['misconceptionCodeForKst'] = $misconceptionCodesList;
							$misconceptionArr = implode(",",$misconceptionCodesList);
							$query = "update educatio_adepts.kst_diagnosticTestAttempts set finalMisconception = '$misconceptionArr' WHERE userID=$userID and attemptID = $attemptID and status = 1";
							mysql_query($query);
							$_SESSION['clusterAndLearningObjective'] = getClusterAndMisconception($userID,$attemptID);
							setComprehensiveFlowForKst($_SESSION['clusterAndLearningObjective'],$userID,$testType,$teacherTopicCode,$teacherTopicAttemptID,$sessionID);
						}
					}
				}
				unset($_SESSION['isPostTest']);
				unset($_SESSION['kstdiagnosticTestType']);
				return array('questionCode' => 'done', 'alertText'=>"You have answered $correct out of $all questions correctly, $username.	Let us start the topic! ");
			} else {
				//Post Test is done
				unset($_SESSION['isPostTest']);
				unset($_SESSION['kstdiagnosticTestType']);
				return array('questionCode' => 'done', 'alertText'=>"You have answered $correct out of $all questions correctly, $username.	We appreciate your efforts in learning the topic. Keep up the good work! ");
			}
		} else {
			$k = array_rand($arr_predicted);
			return array('questionCode' => $arr_predicted[$k]);
		}
	}
		//then check if the additional qcodes value is equal to actual value of additional qcodes if they are equal then the test is over



function nextpractiseQuestion($userID,$sessionID,$subjectNo,$qcode,$response,$quesno,$seconds,$responseResult,$dynamic=0,$dynamicParams,$eeresponse="NO_EE",$practiseid)
	{
		$childClass	=	$_SESSION['childClass'];
		$sq	=	"INSERT INTO practiceTestAttempt (practiceattemptID, userID, attemptdate, qcode, qno, A, S, R, sessionID, lastmodified) VALUES ($practiseid,$userID,'".date("d-m-Y")."',$qcode,$quesno,'$response','$seconds',$responseResult,$sessionID,'".date("Y-m-d H:i:s")."')";

		$rs	=	mysql_query($sq) or die(mysql_error().$sq);
		$quesAttempt_srno = mysql_insert_id();

		$qcodeitem = $_SESSION["practiseqcodes"];
		$qcode	=	$qcodeitem[array_rand($qcodeitem)];
		return $qcode;
	}
	
	function nextDATestQuestion($userID,$sessionID,$subjectNo,$qcode,$response,$quesno,$seconds,$responseResult,$dynamic=0,$dynamicParams,$eeresponse="NO_EE",$daPaperCode)
	{
		$daPaperCode = $_SESSION['daPaperCode'];
		$sql = "update da_questionTestStatus set lastAttemptQue = if(lastAttemptQue >= $quesno,lastAttemptQue,$quesno) where userID = $userID and paperCode = '$daPaperCode' ";
		mysql_query($sql) or die(mysql_error().$sql);
		
		$checkSq = "SELECT isFlag FROM da_questionAttemptDetails WHERE userID = $userID AND qno = $quesno AND qcode =  $qcode and paperCode = '$daPaperCode' ";
		$result	=	mysql_query($checkSq);
		$resultCount = mysql_num_rows($result);

		if($resultCount == 0)
		{
			if(empty($response))
				$response = 'No Answer';

			$sq	= "INSERT INTO da_questionAttemptDetails (userID, attemptdate, qcode, qno, A, S, R, sessionID,paperCode, lastmodified,isFlag) VALUES ($userID,now(),$qcode,$quesno,'$response','$seconds',$responseResult,$sessionID,'$daPaperCode','".date("Y-m-d H:i:s")."',0)";
		}
		else
		{
			$rsCheckSq = mysql_fetch_assoc($result);
			/*if($rsCheckSq["isFlag"]==1 && $response == '')
				$flag = 1;
			else
				$flag = 0;*/
			$sq	=	"update da_questionAttemptDetails set A = '$response', R=$responseResult, sessionID = $sessionID where userID = $userID and qno = $quesno and qcode =  $qcode";
		}

		//echo $sq; exit;
				 
		$rs	=	mysql_query($sq) or die(mysql_error().$sq);
		$quesAttempt_srno = mysql_insert_id();

		$qcodeitem = $_SESSION["tmpQcodeLists"];
		$qcode	=	$qcodeitem[$quesno];
		return $qcode;
	}
	function nextWorksheetQuestion($userID,$sessionID,$subjectNo,$qcode,$response,$quesno,$seconds,$responseResult,$worksheetAttemptID,$getDirectQuestion=0)
	{
		$wsAnsweredArray = array();
		$answerdQue = 0;
		$sq	=	"update worksheet_attempt_detail set answer = '$response', RW=$responseResult, sessionID = $sessionID, attemptDate=NOW() where userID = $userID and ws_srno = $worksheetAttemptID and wsd_id =  $qcode";
		$rs	=	mysql_query($sq) or die(mysql_error().$sq);
		$wsAnsweredArray = getWorksheetAnsweredArray();
		$_SESSION['wsAnsweredArray'] = $wsAnsweredArray;
		foreach ($wsAnsweredArray as $key => $value) {
			if($value == 1)
				$answerdQue++;
		}		
		$status =  (count($wsAnsweredArray) == $answerdQue) ? 'allAttempted' : 'pending';		
		$sql = "update worksheet_attempt_status set last_attempted_que = $quesno, status='$status'  where userID = $userID and srno = '$worksheetAttemptID' ";		
		mysql_query($sql) or die(mysql_error().$sql);

		$sql = "SELECT b.wsm_id, if(b.end_datetime<NOW() OR a.status='completed',1,0) timeUP 
			FROM worksheet_master b JOIN worksheet_attempt_status a ON a.wsm_id=b.wsm_id and a.userID=$userID and srno = '$worksheetAttemptID'";
		$rs	=	mysql_query($sql) or die(mysql_error().$sql);
		$line = mysql_fetch_array($rs);
		if ($line[1]==1) {return "0~-12";}
		
		$qcodeitem = $_SESSION["tmpQcodeLists"];
		if ($getDirectQuestion!=0) $quesno=$getDirectQuestion-1;
		$qcode	=	array_key_exists($quesno, $qcodeitem)?$quesno."~".$qcodeitem[$quesno]:"0~".$qcodeitem[0];
		
		return $qcode;
	}

	function nextBucketQuetion($userID,$sessionID,$subjectNo,$qcode,$response,$quesno,$seconds,$responseResult,$dynamic=0,$dynamicParams,$eeresponse="NO_EE")
	{
		$childClass	=	$_SESSION['childClass'];
		$attemptID	=	$_SESSION['bucketAttemptID'];
		$clusterCode	=	$_SESSION['bucketClusterCode'];
		
		$sq	=	"INSERT INTO adepts_bucketClusterAttempt (userID,questionNo,qcode,A,S,R, clusterCode,attemptID,sessionID)
				 VALUES 
				 ($userID,$quesno,$qcode,'$response','$seconds',$responseResult,'$clusterCode',$attemptID,$sessionID)";
		$rs	=	mysql_query($sq);
		$quesAttempt_srno = mysql_insert_id();
		if($eeresponse != "NO_EE") // When question is having equation editor.
		{
			$eeresponseArr	=	explode("@$*@",$eeresponse);
			$query	=	"INSERT INTO adepts_equationEditorResponse (srno, childClass, userID, sessionID, qcode, question_type, eeResponse, eeResponseImg)
						 VALUES(".$quesAttempt_srno.", ".$_SESSION['childClass'].",".$userID.",".$sessionID.",'".$qcode."','bucketcluster', '".$eeresponseArr[0]."', '".$eeresponseArr[1]."')";
			mysql_query($query) or die($query);
		}
		if($dynamic)
		{
			$query	=	"INSERT INTO adepts_dynamicParameters (userID, qcode, quesAttempt_srno, parameters, mode, class, lastModified) VALUES
						 ($userID, $qcode, ".$quesAttempt_srno.", '$dynamicParams', 'bucketcluster','".$childClass."', '".date("Y-m-d H:i:s")."')";
			mysql_query($query);
		}
		return getNextBucketClusterQuetion($qcode,$responseResult);
	}

	function nextComprehensiveQuetion($userID,$sessionID,$subjectNo,$qcode,$response,$quesno,$seconds,$responseResult,$dynamic=0,$dynamicParams,$eeresponse="NO_EE")
	{
		$childClass  = $_SESSION['childClass'];
		$sq	=	"INSERT INTO adepts_researchQuesAttempt (userID,attemptedDate,questionNo,questionType,qcode,A,S,R,sessionID)
				 VALUES ($userID,'".date("Y-m-d")."','$quesno','comprehensive',$qcode,'$response','$seconds',$responseResult,$sessionID)";
		$rs	=	mysql_query($sq);
		$quesAttempt_srno = mysql_insert_id();
		if($eeresponse != "NO_EE") // When question is having equation editor.
		{
			$eeresponseArr	=	explode("@$*@",$eeresponse);
			$query = "INSERT INTO adepts_equationEditorResponse (srno, childClass, userID, sessionID, qcode, question_type, eeResponse, eeResponseImg) VALUES(".$quesAttempt_srno.", ".$_SESSION['childClass'].",".$userID.",".$sessionID.",'".$qcode."','comprehensive', '".$eeresponseArr[0]."', '".$eeresponseArr[1]."')";
			mysql_query($query) or die($query);
		}
		if($dynamic)
		{
			$query	=	"INSERT INTO adepts_dynamicParameters (userID, qcode, quesAttempt_srno, parameters, mode, class, lastModified) VALUES
						 ($userID, $qcode, ".$quesAttempt_srno.", '$dynamicParams', 'comprehensive','".$childClass."', '".date("Y-m-d H:i:s")."')";
			mysql_query($query);
		}
		if($responseResult)
			$_SESSION['correctInARow']++;
		else
			$_SESSION['correctInARow']=0;
		if($_SESSION['correctInARow']==3)
		{
			addSparkies(1, $sessionID);
			$_SESSION['sparkie']['normal'] += 1;
			$_SESSION['correctInARow']=0;
		}
		return getNextComprehensiveClusterQuetion($qcode,$responseResult,$quesAttempt_srno);
	}

	function nextKstModuleQuestion($userID,$sessionID,$subjectNo,$qcode,$response,$quesno,$seconds,$responseResult,$dynamic=0,$dynamicParams,$eeresponse="NO_EE")
	{
		$childClass  = $_SESSION['childClass'];
		$sq	=	"INSERT INTO adepts_researchQuesAttempt (userID,attemptedDate,questionNo,questionType,qcode,A,S,R,sessionID)
				 VALUES ($userID,'".date("Y-m-d")."','$quesno','comprehensive',$qcode,'$response','$seconds',$responseResult,$sessionID)";
		$rs	=	mysql_query($sq);
		$_SESSION['timeTakenForCluster'][] = $seconds;
		$quesAttempt_srno = mysql_insert_id();
		if($eeresponse != "NO_EE") // When question is having equation editor.
		{
			$eeresponseArr	=	explode("@$*@",$eeresponse);
			$query = "INSERT INTO adepts_equationEditorResponse (srno, childClass, userID, sessionID, qcode, question_type, eeResponse, eeResponseImg) VALUES(".$quesAttempt_srno.", ".$_SESSION['childClass'].",".$userID.",".$sessionID.",'".$qcode."','comprehensive', '".$eeresponseArr[0]."', '".$eeresponseArr[1]."')";
			mysql_query($query) or die($query);
		}
		if($dynamic)
		{
			$query	=	"INSERT INTO adepts_dynamicParameters (userID, qcode, quesAttempt_srno, parameters, mode, class, lastModified) VALUES
						 ($userID, $qcode, ".$quesAttempt_srno.", '$dynamicParams', 'comprehensive','".$childClass."', '".date("Y-m-d H:i:s")."')";
			mysql_query($query);
		}
		if($responseResult)
			$_SESSION['correctInARow']++;
		else
			$_SESSION['correctInARow']=0;
		if($_SESSION['correctInARow']==3)
		{
			addSparkies(1, $sessionID);
			$_SESSION['sparkie']['normal'] += 1;
			$_SESSION['correctInARow']=0;
		}
		return getnextKstModuleQuestion($qcode,$responseResult,$quesAttempt_srno);
	}

	function nextExerciseQuestion($userID,$clusterCode,$quesno,$sessionID,$subjectno,$qcode=NULL,$response=NULL,$secs=NULL,$responseResult=NULL,$showAnswer=1)
	{
		if($quesno >0)
		{
			$query = "INSERT INTO adepts_exerciseQuesAttempt
                      (userID,questionNo,qcode,A,S,R,sessionID, clusterCode, lastModified)
                      VALUES
                      ($userID,$quesno,$qcode,'$response','$seconds',$responseResult,$sessionID,'$clusterCode','".date("Y-m-d H:i:s")."')";
            //echo $query;
            mysql_query($query) or die(mysql_error());
		}

		$query = "select max(subdifficultylevel) level from adepts_questions a, adepts_exerciseQuesAttempt b where a.qcode=b.qcode and userID=$userID and b.clusterCode='$clusterCode' and question_type!='I'";
		//echo $query;
		$result = mysql_query($query) or die(mysql_error());
		$line = mysql_fetch_array($result);
		$level = $line['level'];

		if($level=="")
			$level = 0;

		$query = "select qcode from adepts_questions where clusterCode='$clusterCode' and subdifficultylevel > $level and question_type!='I' order by subdifficultylevel limit 1";
		$result = mysql_query($query) or die(mysql_error());
		$line = mysql_fetch_array($result);
		$qcode = $line['qcode'];

		if($qcode=="")
			$qcode = -10;

		return $qcode."~".$showAnswer;
	}

	function nextNCERTquestion($userID,$exerciseCode,$qcode,$attemptID,$questionNo,$sessionID,$currentSDL=NULL,$responseResult=NULL,$response=NULL,$seconds=NULL, $eeresponse="")
	{
		$showAnswer = 1;
		if($qcode == "")
		{
			$currentSDL = findMinSDL();
			if($currentSDL == -1)
				return(-2);//No Questions in Excercise... (Might not be a case..)
			$qcode = findQuestion($currentSDL);
			$_SESSION["currentSDL"] = $currentSDL;
		}
		else
		{
			if($responseResult != NULL) // Case where answer passed by submit Answer
			{
				$qcodeArray = explode("##",$qcode);
				$responseResultArray = explode("##",$responseResult);
				$responseArray = explode("##",$response);
				$eeResponseArray = explode("##",$eeresponse);
				$i = 0;
				$questionNoArray = explode("##",$questionNo);
				$seconds = $seconds / count($qcodeArray);
				foreach($qcodeArray as $singleQcode)
				{
					$questionNo = $questionNoArray[$i];
					//Save Details...
					saveNCERTques($userID,$questionNo,$singleQcode,$responseArray[$i],$seconds,$responseResultArray[$i],$attemptID,$exerciseCode,$sessionID, $eeResponseArray[$i]);
					$i++;
				}
			}
			if($currentSDL == NULL) // Case where exercise left in between, qcodes will be passed but not the $currentSDL
			{
				$qcodeArray = explode("##",$qcode);
				$qcode = $qcodeArray[0];
				foreach($_SESSION["allQuestionsArray"] as $sdl=>$qcodeArray)
				{
					if(in_array($qcode,$qcodeArray))
					{
						$currentSDL = floor($sdl);
					}
				}
			}
			else
			{
				array_push($_SESSION["completedGroups"],$currentSDL);
				$currentSDL = findHigherSDL($currentSDL);
			}
			
			// If group is found but may be attempted before then mark it as not found
			if($currentSDL != -1 && in_array($currentSDL, $_SESSION["completedGroups"])) {
				$currentSDL = -1;
			}
			
			// If group is marked as not found then find first unattempted group
			if($currentSDL == -1) {
				foreach($_SESSION["allQuestionsArray"] as $sess_group => $sess_qcodes) {
					if(!in_array($sess_group, $_SESSION["completedGroups"])) {
						$currentSDL = floor($sess_group);
						break;
					}
				}
			}
			
			// if no group is found then mark exercise as completed
			if($currentSDL == -1)
				return(-1);//Excercise Completed...
			$qcode = findQuestion($currentSDL);
			$_SESSION["currentSDL"] = $currentSDL;
		}
		return $qcode."~".$showAnswer;
	}

    function nextQuestion($userID,$clusterAttemptID,$clusterCode,$sessionID,$subjectno,$qcode=NULL,$currentSDL=NULL,$response=NULL, $questionNo=NULL, $seconds=NULL, $responseResult=NULL,$startTime=NULL,$endTime=NULL, $pageLoadTime=0, $quesType="normal",$dynamic=0,$dynamicParams="",$showAnswer=1, $noOfTrialsTaken=0, $eeresponse="NO_EE",$hintAvailable=0, $hintUsed=0, $tmpMode="", $userAllAnswers="", $timeTakenHints="", $isHintUsefull=0,$toughType,$toughResult,$promptNumber,$timeTakenToughQues,$extraParameters='')
    {
		if($quesType!="bonusCQ")
		{
			$_SESSION['topicWiseProgressStatus']=array();
			$_SESSION['topicWiseProgressStatus'] = findCLustersAttendedInTopic($_SESSION['teacherTopicAttemptID']);
			$questionType = $remedialItemCode = "";
			$researchModuleStatus = 0;
			$teacherTopicCode  = $_SESSION['teacherTopicCode'];
			$clusterAttemptNo  = $_SESSION['clusterAttemptNo'];
			$ttAttemptNo       = $_SESSION['topicAttemptNo'];
			$sparkieAfterCorrectQues = getQuesCountForSparkie($ttAttemptNo);
			$DCTstage = "";
		}
		$_SESSION['clusterStatusPrompt'] = 0;
		$isNewCluster=0;
		$ttAttemptID       = $_SESSION['teacherTopicAttemptID'];
        if($qcode == "")	// this condition will apply for the first question in the TT
        {   
            $currentSDL = findMinSDL();
            if($currentSDL == -1)
                return -4;                        //"Some Error in Finding Minimum SDL";
            $qcode = findQuestion($currentSDL);
            $questionType = "normal";
            insertCurrentStatus($qcode);
        }
        else if ($qcode != "")
        {
           
			$_SESSION["qid"] = "";
			
			if($quesType!="challenge" && $quesType!="wildcard" && $quesType!="bonusCQ")
			{
				$_SESSION['quesAttemptedInTopic']++;
				if($responseResult==1)
					$_SESSION['quesCorrectInTopic']++;
			}
			if($response=="skipped" && ($quesType=="challenge" || $quesType=="bonusCQ"))
			{
			}
			else
			{
				saveDetails($userID, $questionNo, $qcode, $response,$seconds,$responseResult, $clusterAttemptID, $clusterCode, $sessionID,$startTime, $endTime, $pageLoadTime, $quesType, $ttAttemptID, $teacherTopicCode, $dynamic,$dynamicParams, $showAnswer, $subjectno, $noOfTrialsTaken, $eeresponse, $hintAvailable, $hintUsed,$tmpMode, $userAllAnswers,$timeTakenHints,$isHintUsefull,$toughType,$toughResult,$promptNumber,$timeTakenToughQues,$extraParameters);
			}
			// Ends Here..
			$_SESSION["updateProgress"] = true;
			if($quesType=="bonusCQ")
            {
				if(($key = array_search($qcode,$_SESSION["bonusCQArray"])) !== false) {
					unset($_SESSION["bonusCQArray"][$key]);
				}

				if($responseResult==1)    //if Bonus challenge question got correct
                {
                    $noOfSparkies = 5;
					$_SESSION['sparkie']['CQ'] += $noOfSparkies;
					addSparkies($noOfSparkies, $sessionID);
				}
				if(count($_SESSION["bonusCQArray"])==0)
				{
					$_SESSION['quesAttemptedInTopic']--;
					$_SESSION["qno"]--;
					$qcode = $_SESSION["afterBonusCQqcode"];
					$questionType = "normal";
					return $qcode; 
				}
				else 
					return;
			}
			else if($quesType=="challenge")
            {
				if(($key = array_search($qcode,$_SESSION["challengeQuestionsOtherArray"])) !== false) {
					unset($_SESSION["challengeQuestionsOtherArray"][$key]);
				}
				if(($key = array_search($qcode,$_SESSION["challengeQuestionsProblemSolvingArray"])) !== false) {
					unset($_SESSION["challengeQuestionsProblemSolvingArray"][$key]);
				}

                manipulateArrays($currentSDL,$qcode, $quesType, $responseResult);    // Add the qcode in the appropriate cq  array.
				//if the user gets it right or its the second attempt (show answer will be 1), reset the cq field.
				$CQAttemptNo = getCQAttemptNo($qcode);
                if($responseResult==1)    //if challenge question got correct
                {
                    //if got right on the first attempt - 5 sparkies, on 2nd and 3rd attempt - 2 sparkies and 4th attempt onwards 1 sparkie
                    
                    if($CQAttemptNo==2)
                        $noOfSparkies = 5;
                    elseif ($CQAttemptNo==3 || $CQAttemptNo==4)
                        $noOfSparkies = 2;
                    else
                        $noOfSparkies = 1;

					$_SESSION['sparkie']['CQ'] += $noOfSparkies;
                    addSparkies($noOfSparkies, $sessionID);
					$query = "UPDATE ".TBL_CURRENT_STATUS." SET challengeQues=NULL WHERE userID=$userID AND teacherTopicCode='".$teacherTopicCode."'";
					mysql_query($query);
                }
				else if($CQAttemptNo<=2)
				{
					$query = "UPDATE ".TBL_CURRENT_STATUS." SET challengeQues=$qcode WHERE userID=$userID AND teacherTopicCode='".$teacherTopicCode."'";
					mysql_query($query);
				}
				else
				{
					$query = "UPDATE ".TBL_CURRENT_STATUS." SET challengeQues=NULL WHERE userID=$userID AND teacherTopicCode='".$teacherTopicCode."'";
					mysql_query($query);
				}

                if($currentSDL=="")    //To ensure in case if it is blank
                    $currentSDL = -1;
                if ($currentSDL != -1)
                    $qcode  = findQuestion($currentSDL);
                $questionType = "normal";
            }
			else if($quesType=="wildcard")
            {
                if($responseResult==1)    //if wildcard question got correct
                {
					$noOfSparkies=1;
					$_SESSION['sparkie']['wildcard'] += $noOfSparkies;
                    addSparkies($noOfSparkies, $sessionID);
                }

                if($currentSDL=="")    //To ensure in case if it is blank
                    $currentSDL = -1;
                if ($currentSDL != -1)
                {
                	if(isset($_SESSION['qcode']) && $_SESSION['qcode'] > 0)
	                    $qcode  = $_SESSION['qcode'];
	                else
                    	$qcode  = findQuestion($currentSDL);
                }
                $questionType = "normal";
            }
            else if ($responseResult == 1 && $_SESSION["msAttemptMode"]!=2)
            {
                if($hintUsed == 0)
				{
                	$_SESSION['correctInARow'] += 1;
                	$_SESSION['quesCorrectInALevelOfTopic'] += 1;
				}

                if ($_SESSION['correctInARow'] == $sparkieAfterCorrectQues)  //Use SESSION Values
                {
                    addSparkies(1, $sessionID);
					$_SESSION['sparkie']['normal'] += 1;
                    $_SESSION['correctInARow'] = 0;
                }

				$currentProgress = $_SESSION['topicProgressDetails'][$clusterCode][$currentSDL];
				if($currentProgress> $_SESSION['progressInTopic'])
					$_SESSION['progressInTopic'] = round($currentProgress);
				
				array_push($_SESSION['sdlAttemptResultData'][$currentSDL], 1);

				if ($_SESSION["childClass"]>3 || $_SESSION["noOfAttemptsOnSdl"][$currentSDL]>=3) {
					$_SESSION['sdlAttemptResult'][$currentSDL] = ($_SESSION["childClass"]>3)?1:max($_SESSION['sdlAttemptResultData'][$currentSDL]);
					$currentSDL = findHigherSDL($currentSDL);
				}
				if ($currentSDL != -1){
				    $qcode = findQuestion($currentSDL);
	                if ($qcode == -1) //Implies all questions of SDL exhausted 
	                {
	            		$currentSDL = findHigherSDL($currentSDL);
	            		if ($currentSDL != -1)
	            		    $qcode = findQuestion($currentSDL);
            		}
            	}
                $questionType = "normal";
            }
            else
            {
                $_SESSION['correctInARow'] = 0;
                $_SESSION['quesCorrectInALevelOfTopic'] = 0;
				if($_SESSION["noOfAttemptsOnSdl"][$currentSDL]>=3)
					$qcode=-1;
				else{
                    $qcode=findQuestion($currentSDL);
				}
				array_push($_SESSION['sdlAttemptResultData'][$currentSDL], 0);

                $questionType = "normal";

                if ($qcode == -1) //Implies all questions of SDL exhausted 
                {
                	$_SESSION['sdlAttemptResult'][$currentSDL] = ($_SESSION["childClass"]>3)?0:max($_SESSION['sdlAttemptResultData'][$currentSDL]);
                	
            		if ($_SESSION['sdlAttemptResult'][$currentSDL]==1) {
						$currentSDL = findHigherSDL($currentSDL);
						if ($currentSDL != -1)
						    $qcode = findQuestion($currentSDL);
            		}
            		else {
						if($clusterAttemptNo==1)	//if 1st attempt on a cluster and a remedial item mapped to this sdl, give that remedial item
						{
							$remedialItemArray = $_SESSION['remedialItems'];
							foreach ($remedialItemArray as $code=>$sdls)
							{
								$sdlArray = explode(",", $sdls);
								if($sdls!="All" && in_array($currentSDL, $sdlArray))
								{
									if(isRemedialItemGivenBefore($ttAttemptID, $code)==0)
									{
										$remedialItemCode = $code;
										break;
									}
								}
							}
						}
						if($remedialItemCode=="")
						{
	                	    $perSDLsCovered = getPerOfSDLsCovered($currentSDL);
	                	    $currentSDL     = findHigherSDL($currentSDL);
	                	    if($currentSDL != -1)
	                	    {
	                		    if($perSDLsCovered>=50)
	                		    {
									if($_SESSION['progressBarFlag']==1)
									{
										$arrayClusterData	=	calcTotalScoreByPercentile($clusterAttemptID, "", 1);
										$totalScore	=	$arrayClusterData["checkChanceToClear"];
										$clearCriterion =	getClusterPassingCriterion($clusterCode);
									}
									else
									{
										$arrayClusterData	=	calcTotalScore($clusterAttemptID);
										$clearCriterion	=	60;
										$totalScore	=	$arrayClusterData["totalScore"];
									}

	                			    if($totalScore>=$clearCriterion)
	                			        $qcode = findQuestion($currentSDL);
	                		    }
	                		    else
	                		        $qcode = findQuestion($currentSDL);
	                	    }
						}
						else
						{
						    $questionType = "remedial";
						    $qcode = $remedialItemCode;
						}
					} 
                }

            }

            if ($questionType!="remedial" && ($currentSDL == -1 || $qcode == -1))
            {
				$_SESSION['clusterCompleted'] = 1;
				$isPracticeClusterCompletes = false;
				$returnArray = determineNextCluster($userID, $clusterAttemptID, $clusterCode, $ttAttemptID, $teacherTopicCode, $sessionID, $clusterAttemptNo);
				if($returnArray[0]!="remedial")
				{
					if($returnArray[0] == "DCT")
					{
						$DCTstage = '-11'.'~ ~'.$returnArray[2]; //DCT is applicable..
						setPendingDCT($returnArray[2], $ttAttemptID);
					}
					elseif ($returnArray[0]=="ResearchModule")
					{
						$researchModuleStatus = 1;
					}
					$clusterCode = $returnArray[1];
					if ($clusterCode == -1) {
			    		return -3;                  //End of Topic, Topic Success;
			    	}
			    	elseif ($clusterCode == -2) {	//Class level completed in the TT
			    		return -8;
			    	}
			    	else
	                {
	                    $currentSDL = findMinSDL();
	                    if ($currentSDL == -1)
	                        return -4;                    //Some Error in Finding Minimum SDL;
	                    $qcode=findQuestion($currentSDL);
	                    $isNewCluster=($returnArray[3]=='SUCCESS')?1:0;
	                }
				}
				else
				{
					$questionType = "remedial";
					$qcode  = $returnArray[1];
					$remedialItemCode = $returnArray[1];
				}
            }
        }
		$currentStatusQcode = $qcode;
        //UPDATE userCurrentStatus with the newly generated qcode and related information
        updateSession($userID, $clusterCode,$currentSDL,$currentStatusQcode, $teacherTopicCode, $remedialItemCode);
		if($_SESSION['wildcardAtRand']['qcode']!="")
		{
			$findWildcard = array(0,0,1,0);
			$chance = array_rand($findWildcard);
			if($chance==1)
			{
				$questionType	=	"wildcard";
				$qcode	=	$_SESSION['wildcardAtRand']['qcode'];
			}
		}
        if($responseResult==1 && $ttAttemptNo<=5 && $_SESSION["childClass"]>2)   //No CQs to be given after the 5th attempt on a topic
        {
        	$quesCorrectInALevelOfTopic = $_SESSION['quesCorrectInALevelOfTopic'];
	        if ($quesCorrectInALevelOfTopic == 5) //CQ logic
	        {
	            $challengeQuesQcode = isPendingChallengeQues($userID, $teacherTopicCode);
	            if($challengeQuesQcode!=-1) {
	                $showAnswer = 1;
					foreach($_SESSION['challengeQuestionsArray'] as $sdl => $cqDetails)
					{
						if(array_key_exists($challengeQuesQcode,$cqDetails))
						{
							$_SESSION['competitiveExamCQ'] = $cqDetails[$challengeQuesQcode][2];
							break;
						}
					}
	            }
	            else {
	                //$challengeQuesQcode = findChallengeQuestion();
					$challengeQuesQcode = findChallengeQuestionNewLogic($clusterCode);
	                $showAnswer = 0;
	            }

	            $_SESSION['quesCorrectInALevelOfTopic'] = 0;
	            if($challengeQuesQcode!=-1)
	            {
	                //$_SESSION['correctInARow'] = 0;
	                $questionType = "challenge";
	                $qcode = $challengeQuesQcode;    //if challenge question found, give the challenge question else normal question.
	            }
	        }
        }
        if($questionType=="normal")
        	manipulateArrays($currentSDL,$qcode, $questionType);

        $_SESSION['questionType'] = $questionType;       //set the mode in the session
        $perSDLsCovered = getPerOfSDLsCovered($currentSDL);

		//$qcode = 11542; cns
		//$qcode = 22841; image
		//$qcode = 21190; swf
		//$qcode = 11214;
		
		if($DCTstage != "")
			return $DCTstage;
		else if($researchModuleStatus)
			return "-13";	//-13 indicates research module
		else if($isPracticeClusterCompletes)
			return $returnValues;
		else
        	return $qcode."~".$showAnswer."~".$perSDLsCovered."~".$isNewCluster;
    }

	function determineNextCluster($userID, $clusterAttemptID, $clusterCode, $ttAttemptID, $teacherTopicCode, $sessionID, $clusterAttemptNo)
    {
    	$returnArray = array();
		$returnArray[0] = "cluster";
		$childClass  = $_SESSION['childClass'];
		//----add schoolCondition
		if($_SESSION['progressBarFlag']==1)
		{
			$arrayClusterData	=	calcTotalScoreByPercentile($clusterAttemptID);
			$totalScore	=	$arrayClusterData["totalPercentile"];
			$clearCriterion =	getClusterPassingCriterion($clusterCode);
			$arrayClusterDataQuesWise	=	calcTotalScore($clusterAttemptID);
			//ALTER TABLE  adepts_clusterMaster ADD  passingCriterion SMALLINT( 3 ) NOT NULL DEFAULT  '75' AFTER  lowerLimit ;
		}
		else
		{
			$arrayClusterData	=	calcTotalScore($clusterAttemptID);
			$clearCriterion	=	75;
			$totalScore	=	$arrayClusterData["totalScore"];
		}
		//---------
			
    	$clusterResult = "";
		$DCTStatus = "false";
    	if ($totalScore >= $clearCriterion)        //i.e. cluster successfully completed
    	{
			//$bonusSchoolCodeArray = array(365439,23246,2474876,205449,1752,348782,359413,384445,3184063,420525,650967,207093,33367,173767,764756,2387554);
			
    		$clusterResult = "SUCCESS";
			//Check if DCT is applicable
			$DCTStatus = isDCT($clusterAttemptNo,$clusterCode,$ttAttemptID);
			if($arrayClusterDataQuesWise["totalScore"]>=80 && $clusterAttemptNo==1 && $childClass>3 && $childClass<8 && $DCTStatus == "false" && $_SESSION['topicAttemptNo']<6)
			{
				$_SESSION["bonusCQArray"] = getBonusCQ($clusterCode);
			}
    		$childClass  = $_SESSION['childClass'];
			//check if game linked to the cluster.
			if((trim($DCTStatus) == "false" && $childClass<=3) || (!isChoiceScreenSchool($childClass)  || $childClass<=3))
    			$game = checkForGame($clusterCode, $ttAttemptID, $userID, $clusterAttemptID, $childClass);
    		//check if timed test linked to this cluster. if yes update in the session code and user current status
    		$timedTest = checkForTimedTest($ttAttemptID, $clusterAttemptID, $clusterCode, $userID, $childClass);
    		$_SESSION['timedTest'] = $timedTest;    		
    		
			$researchModuleStatus = checkForResearchModule($userID, $ttAttemptID, $clusterCode);
    	}
    	else
    	{
    		$clusterResult = "FAILURE";
    		if($clusterAttemptNo==1)	//if 1st attempt on a cluster and a remedial item mapped to this cluster, give that remedial item
	    	{
	    		$code = getRemedialItemAtTheEndOfTheCluster($ttAttemptID, $clusterAttemptID);
	    		if($code!="")
	    		{
					$returnArray[0] = "remedial";
	    			$returnArray[1] = $code;
	    		}
	    	}
    	}
	   	updateClusterData($clusterAttemptID,$clusterResult,$sessionID,$arrayClusterData["totalScore"],$arrayClusterData["totalQuesAttempted"],$ttAttemptID,$clusterCode);						//Update the cluster status table with the result of the current attempt
    	if($returnArray[0]!="remedial")
    		$returnArray[1] = nextCluster($userID,$teacherTopicCode,$ttAttemptID,$sessionID,$clusterCode,$clusterResult, $clusterAttemptID);
		
		if($DCTStatus != "false")
		{
			//Maintain Status here..
			$returnArray[0] = "DCT";
			$returnArray[2] = $DCTStatus;
		}
		
		if($researchModuleStatus==1)
		{
			$returnArray[0] = "ResearchModule";
			$returnArray[2] = $researchModuleStatus;
		}
		$returnArray[3]=$clusterResult;
		return $returnArray;
    }

    function getRemedialItemAtTheEndOfTheCluster($ttAttemptID, $clusterAttemptID)
    {
    	$remedialItemCode = "";
    	$remedialItemArray = $_SESSION['remedialItems'];
    	foreach ($remedialItemArray as $code=>$sdls)
    	{
    		if(isRemedialItemGivenBefore($ttAttemptID,$code)==0)
    		{
    		    $totalScore = 1;
    			if($sdls!="All") //If remedial item mapped to all sdls, ignore the condition of <40% in marked sdls.
				{
    			    $arrayClusterData	=	calcTotalScore($clusterAttemptID,$sdls);
					$totalScore	=	$arrayClusterData["totalScore"];
				}
    			if($totalScore!="" && $totalScore<=40)
    			{
    				$remedialItemCode = $code;
    				break;
    			}
    		}
    	}
    	return $remedialItemCode;
    }

    function isRemedialItemGivenBefore($ttAttemptID, $remedialItemCode)
    {
    	$remedialItemGivenBefore = 0;
		$query  = "SELECT count(remedialAttemptID) FROM adepts_remedialItemAttempts a, ".TBL_CLUSTER_STATUS." b
		           WHERE  a.clusterAttemptID=b.clusterAttemptID AND ttAttemptID=$ttAttemptID AND remedialItemCode='$remedialItemCode'";
		$result = mysql_query($query);
		$line   = mysql_fetch_array($result);
		if($line[0]>0)
			$remedialItemGivenBefore = $line[0];
		return $remedialItemGivenBefore;
    }

    function addSparkies($noOfSparkies, $sessionID)
    {
		$_SESSION["noOfJumps"] =    $_SESSION["noOfJumps"] + $noOfSparkies;
		$query = "UPDATE ".TBL_SESSION_STATUS." SET noOfJumps = noOfJumps + $noOfSparkies WHERE sessionID=".$sessionID;
		mysql_query($query);
    }

    function updateSession($userID, $clusterCode,$currentSDL,$qcode, $teacherTopicCode, $remedialItemCode)
    {
    	$_SESSION['prevQcode']    = $_SESSION["qcode"];
        $_SESSION["qcode"]        = $qcode;
        $_SESSION["currentSDL"]   = $currentSDL;
        $_SESSION["clusterCode"]  = $clusterCode;
        $remedialMode = ($remedialItemCode=="")?0:1;
		$correctInRow = (!empty($_SESSION['noOfJumps'])) ? $_SESSION['noOfJumps'] : 0;

        $query = "UPDATE ".TBL_CURRENT_STATUS." SET qcode='".$qcode."', currentSDL=".$currentSDL.", clusterCode='".$clusterCode."', clusterAttemptID=".$_SESSION['clusterAttemptID'].", correctInRow=".$correctInRow.", remedialMode=$remedialMode
		          WHERE  userID=".$userID." AND teacherTopicCode='".$teacherTopicCode."'";
        $exec_query = mysql_query($query);
		
		/* Added for temporary check for "why currentSDL is set as NULL in database" */
		if(!$exec_query) {
			$log_error_data = "INSERT INTO adepts_errorLogs SET bugType='queryFailLog', 
				bugText='".mysql_real_escape_string($query)."', qcode='".$_SESSION['qcode']."', 
				userID='".mysql_real_escape_string($_SESSION['userID'])."', 
				sessionID='".mysql_real_escape_string($_SESSION['sessionID'])."', 
				schoolCode='".mysql_real_escape_string($_SESSION['schoolCode'])."'";
			$exec_log_error_data = mysql_query($log_error_data);
		}
    }

    function insertCurrentStatus($qcode)
    {
        $query = "INSERT INTO ".TBL_CURRENT_STATUS."
                    (
                        userID,
                        sessionID,
                        qcode,
                        clusterCode,
                        clusterAttemptID,
                        teacherTopicCode,
                        ttAttemptID,
                        correctInRow,
                        remedialStack,
                        status
                    )
                    VALUES
                    (
                        '$_SESSION[userID]',
                        '$_SESSION[sessionID]',
                        '$qcode',
                        '$_SESSION[clusterCode]',
                        '$_SESSION[clusterAttemptID]',
                        '$_SESSION[teacherTopicCode]',
                        '$_SESSION[teacherTopicAttemptID]',
                        '$_SESSION[correctInARow]',
                        '$_SESSION[remedialStack]',
                        '1'
                    )";

        mysql_query($query) or die(mysql_error());
    }

    function calcTotalScore($clusterAttemptID, $sdls="")
    {
		$arrayClusterData	=	array();
		$totalScore = "";
        //$totalScore = total of R from assessent for that particular clusterAttemptID/ total number of entries from assessent for that particular clusterAttemptID * 100;
        if($sdls=="")
        {
        	$query  = "SELECT sum(R),count(srno) FROM ".TBL_QUES_ATTEMPT_CLASS." WHERE clusterAttemptID=".$clusterAttemptID;
        }
        else
        {
        	$query  = "SELECT sum(R),count(srno) FROM ".TBL_QUES_ATTEMPT_CLASS." a, adepts_questions b
        	           WHERE  a.qcode=b.qcode AND clusterAttemptID=$clusterAttemptID AND subdifficultylevel in ($sdls)";
        }
        $result = mysql_query($query);
        $line   = mysql_fetch_array($result);
        $right  = $line[0];
        $totalQuesAttempted = $line[1];
        mysql_free_result($result);
		if($totalQuesAttempted>0)
		{
			$totalScore = round(($right/$totalQuesAttempted)*100,2);
		}
		$arrayClusterData["totalScore"]			=	$totalScore;
		$arrayClusterData["totalQuesAttempted"]	=	$totalQuesAttempted;
        return $arrayClusterData;
    }
	
	function calcTotalScoreByPercentile($clusterAttemptID, $sdls="", $checkChanceToClear="")
    {
		$arrayClusterData	=	array();
		$totalScore = "";
		$right = $totalQuesAttempted = 0;
		$arraySdlWise	=	array();
        //$totalScore = total of R from assessent for that particular clusterAttemptID/ total number of entries from assessent for that particular clusterAttemptID * 100;
        if($sdls=="")
        {
        	$query  = "SELECT subdifficultylevel,sum(R),count(srno) FROM ".TBL_QUES_ATTEMPT_CLASS." a, adepts_questions b 
        	           WHERE  a.qcode=b.qcode AND clusterAttemptID=$clusterAttemptID AND R IN (0,1) GROUP BY subdifficultylevel";
        }
        else
        {
        	$query  = "SELECT subdifficultylevel,sum(R),count(srno) FROM ".TBL_QUES_ATTEMPT_CLASS." a, adepts_questions b 
        	           WHERE  a.qcode=b.qcode AND clusterAttemptID=$clusterAttemptID AND subdifficultylevel in ($sdls) GROUP BY subdifficultylevel";
        }
        $result = mysql_query($query);
        while($line=mysql_fetch_array($result))
		{
			$arraySdlWise[$line[0]]	=	$line[1]>0?1:0;
			$right  += $line[1];
			$totalQuesAttempted += $line[2];
		}
		mysql_free_result($result);
		if($totalQuesAttempted>0)
		{
			$totalScore = round(($right/$totalQuesAttempted)*100,2);
		}
		if($checkChanceToClear==1)
			$arrayClusterData["checkChanceToClear"] =round((array_sum($arraySdlWise)+(count($_SESSION["allQuestionsArray"])-count($arraySdlWise)))/count($_SESSION["allQuestionsArray"])*100);
		$arrayClusterData["totalPercentile"]	=	round(array_sum($arraySdlWise)/count($arraySdlWise)*100);
		$arrayClusterData["totalScore"]			=	$totalScore;
		$arrayClusterData["totalQuesAttempted"]	=	$totalQuesAttempted;
        return $arrayClusterData;
    }
	
	function getClusterPassingCriterion($clusterCode)
	{
		$sq	=	"SELECT passingCriterion FROM adepts_clusterMaster WHERE clusterCode='$clusterCode'";
		$rs	=	mysql_query($sq);
		$rw	=	mysql_fetch_array($rs);
		return $rw[0];
	}
	
	function findQuestion($currentSDL)
    {
		if($_SESSION['teacherTopicCode'] == "NCERT")
		{
			if(isset($_SESSION["allQuestionsArray"][$currentSDL]) && count($_SESSION["allQuestionsArray"][$currentSDL]) != 0)
				return implode("##",$_SESSION["allQuestionsArray"][$currentSDL]);
			else
				return -1;
		}
		else
		{
		// Ends Here..
			$questionsNotAttemptedInCurrentClusterAttemptArray =
			array_diff($_SESSION["allQuestionsArray"][$currentSDL],$_SESSION["questionsAttemptedInCurrentClusterAttemptArray"][$currentSDL]);
			//Give priority to the questions never attempted before in the cluster, if no such questions then look at the questions not attempted in the current attempt for the SDL, else return -1(no ques found)
			if (isset($_SESSION["questionsNeverAttemptedArray"][$currentSDL]) && count($_SESSION["questionsNeverAttemptedArray"][$currentSDL])!=0)
			{
				$qcodeKey = array_rand($_SESSION["questionsNeverAttemptedArray"][$currentSDL]);
				$qcode = $_SESSION["questionsNeverAttemptedArray"][$currentSDL][$qcodeKey];
			}
			else if (count($questionsNotAttemptedInCurrentClusterAttemptArray)!=0)
			{
				$qcodeKey = array_rand($questionsNotAttemptedInCurrentClusterAttemptArray);
				$qcode = $questionsNotAttemptedInCurrentClusterAttemptArray[$qcodeKey];
			}
			else
				$qcode = -1;
		}
        return $qcode;
    }

    function getUnAttemptedCQ($CQArray,$flag, $competitiveCQFlag=0)
    {
        $unattemptedQcode = -1;
        $CQNotAttempted = array();
        //flag 1 implies mapped to TT, 0 implies reserved pool and 2 mapped to some other TT of the parent MS topic
        foreach ($CQArray as $qcode=>$arrTemp)
        {
            if($arrTemp[1]==$flag && $arrTemp[0]==0 && $arrTemp[2]==$competitiveCQFlag)//CQ not attempted array will be the one for which attempts on a question are zero
                array_push($CQNotAttempted,$qcode);
        }
        if(count($CQNotAttempted)>0)
        {
            $qcodeKey = array_rand($CQNotAttempted);
            $unattemptedQcode    = $CQNotAttempted[$qcodeKey];
        }
        return $unattemptedQcode;
    }
	
	function findChallengeQuestionNewLogic($clusterCode) //manish.pandey
	{
		$qcode = -1;
		$qcodeArray = array();
		$challengeQuestionsArray = $_SESSION['challengeQuestionsArray'];
		$challengeQuestionsProblemSolvingArray = $_SESSION['challengeQuestionsProblemSolvingArray'];
		$allCQAttempted = $_SESSION["allCQAttempted"];
		if($challengeQuestionsArray[$clusterCode] && count($challengeQuestionsArray[$clusterCode])>0)
		{
			$qcodeArray = $challengeQuestionsArray[$clusterCode];
			$cqCluster = $clusterCode;
		}
		else
		{
			if(count($challengeQuestionsArray[$clusterCode])==0)
				unset($challengeQuestionsArray[$clusterCode]);
			$allClusterArray = $_SESSION['allClustersInTT'];
			$currentPosition = intval(array_search($clusterCode,$allClusterArray))-1;
			for($key=$currentPosition;$key>=0;$key--)
			{
				if($challengeQuestionsArray[$allClusterArray[$key]] && count($challengeQuestionsArray[$allClusterArray[$key]]) > 0)
				{
					$qcodeArray = $challengeQuestionsArray[$allClusterArray[$key]];
					$cqCluster = $allClusterArray[$key];
					break;
				}
			}
		}
		if(!isset($_SESSION['challengeQuestionsOtherFlag']))
			$_SESSION['challengeQuestionsOtherFlag'] = 0;
		if(count($qcodeArray)>0)
		{
			if($_SESSION['topicAttemptNo']>1)
				$qcode = getUnAttemptedCQNewLogic($qcodeArray,$allCQAttempted);
			else
			{		
				$randKey = array_rand($qcodeArray);
				$qcode = $qcodeArray[$randKey];
			}
			$key = array_search($qcode,$challengeQuestionsArray[$cqCluster]);
			unset($challengeQuestionsArray[$cqCluster][$key]);
			if(count($challengeQuestionsArray[$cqCluster])==0)
				unset($challengeQuestionsArray[$cqCluster]);
		}
		else if(count($challengeQuestionsArray)==0 && count($_SESSION['challengeQuestionsOtherArray'])>0 && ($_SESSION['challengeQuestionsOtherFlag']==0 || $_SESSION['challengeQuestionsOtherFlag']==2))
		{
			$challengeQuestionsOtherArray = $_SESSION['challengeQuestionsOtherArray'];
			$randKey = array_rand($challengeQuestionsOtherArray);
			$qcode = $challengeQuestionsOtherArray[$randKey];
			unset($challengeQuestionsOtherArray[$randKey]);
			$_SESSION['challengeQuestionsOtherArray'] = $challengeQuestionsOtherArray;
			if($_SESSION['challengeQuestionsOtherFlag']==0)
			{
				$_SESSION['challengeQuestionsOtherFlag'] = 1;
			}
		}
		else if(count($challengeQuestionsProblemSolvingArray)>0)
		{
			$randKey = array_rand($challengeQuestionsProblemSolvingArray);
			$qcode = $challengeQuestionsProblemSolvingArray[$randKey];
			unset($challengeQuestionsProblemSolvingArray[$randKey]);
			$_SESSION['challengeQuestionsProblemSolvingArray'] = $challengeQuestionsProblemSolvingArray;
		}
		$_SESSION['challengeQuestionsArray'] = $challengeQuestionsArray;
		return $qcode;
	}
	
	function getUnAttemptedCQNewLogic($qcodeArray,$allCQAttempted)
	{
		shuffle($qcodeArray);
		foreach($qcodeArray as $key=>$qcode)
		{
			if(!in_array($qcode,$allCQAttempted))
				break;
		}
		return $qcode;
	}
	
	function getBonusCQ($clusterCode)
	{
		$bonusCQArray = array();
		$challengeQuestionsArray = $_SESSION['challengeQuestionsArray'];
		if($challengeQuestionsArray[$clusterCode] && count($challengeQuestionsArray[$clusterCode])>=3)
		{
			foreach($challengeQuestionsArray[$clusterCode] as $key=>$qcode)
			{
				if(count($bonusCQArray)<3)
				{
					$bonusCQArray[] = $qcode;
					unset($challengeQuestionsArray[$clusterCode][$key]);
				}
			}
		}
		$_SESSION['challengeQuestionsArray'] = $challengeQuestionsArray;
		return $bonusCQArray;
	}
	
    function findChallengeQuestion()
    {
        $qcode = -1;
        //Start
        $childClass  = $_SESSION['childClass'];
        if($childClass<3)	//CQ not applicable for Class 1 & 2
            return $qcode;
		$CQLevelsArray = array_keys($_SESSION['challengeQuestionsArray']);
    	$CQLevel  = array("","","");
    	for($k=0; $k<count($CQLevelsArray); $k++)
    	{
    		if($childClass == $CQLevelsArray[$k])
    			$CQLevel[1] = $CQLevelsArray[$k];
    		elseif(($childClass - 1) == $CQLevelsArray[$k])
    			$CQLevel[0] = $CQLevelsArray[$k];
    		elseif(($childClass + 1) == $CQLevelsArray[$k])
    			$CQLevel[2] = $CQLevelsArray[$k];
    	}
		//End
		$competitiveCQGivenLast = $_SESSION['competitiveExamCQ'];
		$competitiveCQFlag = 0;

		if($childClass>=8 && !$competitiveCQGivenLast)	//for class 8 to 10, give CQ which are from competitive exams i.e. questions tagged with flag 4
		{
			//Check from unattempted competitive CQs which are mapped to the TT
			for($i=0; $i<count($CQLevel); $i++)
			{
				$level = $CQLevel[$i];
				if($level=="")
					continue;
				$allChallengeQuestions = array_keys($_SESSION['challengeQuestionsArray'][$level]);
				if(count($allChallengeQuestions)>0)
				{
	    	       	//Give first priority to challenge questions not attempted
	    			$qcode = getUnAttemptedCQ($_SESSION['challengeQuestionsArray'][$level],1, 1);
	    			if($qcode!=-1)
					{
						$competitiveCQFlag = 1;
	                    break;
					}
				}
			}
		}
		//Check from unattempted CQs which are mapped to the TT
		if($qcode==-1)
		{
			$levelArray = array();
			for($i=0; $i<count($CQLevel); $i++)
			{
				$level = $CQLevel[$i];
				if($level=="")
					continue;
				$allChallengeQuestions = array_keys($_SESSION['challengeQuestionsArray'][$level]);
				if(count($allChallengeQuestions)>0)
				{
	    	       	array_push($levelArray, $level);
	    	       	//Give first priority to challenge questions not attempted
	    			$qcode = getUnAttemptedCQ($_SESSION['challengeQuestionsArray'][$level],1);
	    			if($qcode!=-1)
	                    break;
				}
			}
		}
        //If not found, check from unattempted CQs  of reserved pool
		if($qcode==-1 && count($levelArray)>0)
		{
			if($childClass>=8  && !$competitiveCQGivenLast)	//for class 8 to 10, give CQ which are from competitive exams i.e. questions tagged with flag 4
			{
				for($i=0; $i<count($CQLevel); $i++)
	    		{
	    			$level = $CQLevel[$i];
	    			if($level=="")
	    				continue;
	    	        $qcode = getUnAttemptedCQ($_SESSION['challengeQuestionsArray'][$level],0,1);
	    	        if($qcode!=-1)
					{
						$competitiveCQFlag = 1;
						break;
					}
	    		}
			}
    		for($i=0; $i<count($CQLevel) && $qcode==-1; $i++)
    		{
    			$level = $CQLevel[$i];
    			if($level=="")
    				continue;
    	        $qcode = getUnAttemptedCQ($_SESSION['challengeQuestionsArray'][$level],0);
    	        if($qcode!=-1)
                    break;
    		}
		}
		//If not found, check from unattempted CQs of parent MS topic
		if($qcode==-1 && count($levelArray)>0)
		{
			if($childClass>=8 && !$competitiveCQGivenLast)
			{
				for($i=0; $i<count($CQLevel); $i++)
	    		{
	    			$level = $CQLevel[$i];
	    			if($level=="")
	    				continue;
	    			$allChallengeQuestions = array_keys($_SESSION['challengeQuestionsArray'][$level]);
	        	    $qcode = getUnAttemptedCQ($_SESSION['challengeQuestionsArray'][$level],2,1);
	        	    if($qcode!=-1)
					{
						$competitiveCQFlag = 1;
						break;
					}
	    		}
			}
    		for($i=0; $i<count($CQLevel) && $qcode==-1; $i++)
    		{
    			$level = $CQLevel[$i];
    			if($level=="")
    				continue;
    			$allChallengeQuestions = array_keys($_SESSION['challengeQuestionsArray'][$level]);
        	    $qcode = getUnAttemptedCQ($_SESSION['challengeQuestionsArray'][$level],2);
        	    if($qcode!=-1)
                    break;
    		}
		}

		if($qcode==-1 && count($levelArray)>0)	//i.e. could not find any unattempted CQs, then repeat the CQs randomly - first incorrectly attempted ones then from all
		{

			$key = array_rand($levelArray);
			$level = $levelArray[$key];
			$allChallengeQuestions = array_keys($_SESSION['challengeQuestionsArray'][$level]);

			$CQIncorrect      = array_diff($allChallengeQuestions,$_SESSION["challengeQuestionsCorrect"]);
			if (count($CQIncorrect)!=0)        //challenge questions attempted incorrectly
			{
				$qcodeKey = array_rand($CQIncorrect);
				$qcode    = $CQIncorrect[$qcodeKey];
			}
			else
			{
				$qcodeKey = array_rand($allChallengeQuestions);
				$qcode    = $allChallengeQuestions[$qcodeKey];
			}
		}
		if($qcode!=-1)
		{
		    incrementCQAttemptNo($qcode);
		}
		$_SESSION['competitiveExamCQ'] = $competitiveCQFlag;
        return $qcode;

    }

    function isPendingChallengeQues($userID, $teacherTopicCode)
    {
        $qcode = "";
    	//Check if the CQ is pending i.e. Since we give 2 attempts on a CQ, check if any CQ was answered wrongly in its first attempt, if so it will be stored in the CQ field
    	//On the second or correct attempt of CQ, this will get removed.
        $query  = "SELECT challengeQues FROM ".TBL_CURRENT_STATUS." WHERE userID=$userID AND teacherTopicCode='$teacherTopicCode'";
        $result = mysql_query($query);
        $line   = mysql_fetch_array($result);
        if($line[0]=='' || $line[0]==0)
            $qcode = -1;
        else
        {
            $qcode = $line[0];
            incrementCQAttemptNo($qcode);
        }
        return $qcode;
    }

    function getCQAttemptNo($qcode)
    {
        $CQAttemptNo = 1;
		$sq	=	"SELECT COUNT(*) FROM adepts_ttChallengeQuesAttempt WHERE qcode=$qcode AND userID=".$_SESSION["userID"]." AND ttAttemptID=".$_SESSION['teacherTopicAttemptID'];
		$rs	=	mysql_query($sq);
		if($rw=mysql_fetch_array($rs))
			$CQAttemptNo	=	$rw[0] + 1;
        return $CQAttemptNo;
    }

    function incrementCQAttemptNo($qcode)
    {
        //Increment the attempt no for the CQ
        $CQLevelsArray = array_keys($_SESSION['challengeQuestionsArray']);
        foreach ($CQLevelsArray as $level)
        {
            if(isset($_SESSION["challengeQuestionsArray"][$level][$qcode]))
                $_SESSION["challengeQuestionsArray"][$level][$qcode][0] = $_SESSION["challengeQuestionsArray"][$level][$qcode][0] + 1;
        }
    }
	function findNextNCERTSDL($currentSDL)
	{
		$newCurrentSDL = -1;
		$sdlArray = array_keys($_SESSION["allQuestionsArray"]);
        for($i=0; $i<count($sdlArray); $i++)  // Check next unattempted group....
        {
            if($sdlArray[$i] > $currentSDL && !in_array($sdlArray[$i],$_SESSION["completedGroups"]))
            {
                $newCurrentSDL = $sdlArray[$i];
				break;
            }
        }
		if($newCurrentSDL == -1) // Check if any previous group is unattempted....
		{
			for($i=0; $i<count($sdlArray); $i++)
			{
				if(!in_array($sdlArray[$i],$_SESSION["completedGroups"]))
				{
					$newCurrentSDL = $sdlArray[$i];
					break;
				}
			}
		}
		return($newCurrentSDL);
	}

    function findHigherSDL($currentSDL)
    {
        $sdlArray = array_keys($_SESSION["allQuestionsArray"]);
        for($i=0; $i<count($sdlArray); $i++)
        {
            if(round($sdlArray[$i],2) > round($currentSDL,2))
            {
                $currentSDL = $sdlArray[$i];
                return $currentSDL;
            }
        }
        return -1;
    }

	function getPerOfSDLsCovered($currentSDL)
	{
		$per = 0;
		$sdlArray = array_keys($_SESSION["allQuestionsArray"]);
		sort($sdlArray,SORT_NUMERIC);
		for($i=0; $i<count($sdlArray) && $sdlArray[$i]!=$currentSDL; $i++);
		$i++;
		$per = round(($i/count($sdlArray)*100),2);
		return $per;
	}

    function findMinSDL()
    {

        $currentSDL = min(array_keys($_SESSION["allQuestionsArray"]));
        if ($currentSDL== -1)
            return -1;
        return $currentSDL;
    }

    function manipulateArrays($currentSDL,$qcode, $quesType, $result="")
    {
        //Directly manipulate SESSION Arrays
        $lastQcodeArray = array($qcode);    //Array is made to manipulate the array
        $isQuesDynamicArray = $_SESSION["isQuesDynamicArray"];
        $noOfAttemptsForDynamicQuesArray = $_SESSION["noOfAttemptsForDynamicQuesArray"];
        if($quesType=="challenge")
        {
            if ($result==1 && !in_array($qcode,$_SESSION["challengeQuestionsCorrect"]))
            {
                array_push($_SESSION["challengeQuestionsCorrect"],$qcode);
            }
        }
        else
        {
			if($isQuesDynamicArray[$qcode])
			{
				$noOfAttemptsForDynamicQuesArray[$qcode]++;
				$_SESSION["noOfAttemptsForDynamicQuesArray"] = $noOfAttemptsForDynamicQuesArray;
			}
			if(!$isQuesDynamicArray[$qcode] || ($isQuesDynamicArray[$qcode] && $noOfAttemptsForDynamicQuesArray[$qcode]==3))
			{
	            if (isset($_SESSION["questionsNeverAttemptedArray"][$currentSDL]) && count($_SESSION["questionsNeverAttemptedArray"][$currentSDL])!=0)
	            {
	                $_SESSION["questionsNeverAttemptedArray"][$currentSDL] =
	                array_diff($_SESSION["questionsNeverAttemptedArray"][$currentSDL],$lastQcodeArray);

	            }
	            array_push($_SESSION["questionsAttemptedInCurrentClusterAttemptArray"][$currentSDL],$qcode);
			}
			if($_SESSION["msAttemptMode"]!=2)
				$_SESSION["noOfAttemptsOnSdl"][$currentSDL]++;
        }
    }

    function getQuesCountForSparkie($ttAttemptNo)
    {
        $sparkieAfterCorrectQues = 3;
        switch ($ttAttemptNo)
        {
            case "1" :
            case "2" : $sparkieAfterCorrectQues = 3;    //For attempts 1 & 2 on a topic, 1 sparkie to be given after 3 consecutive correct ques (Regular ques)
                       break;
            case "3" :
            case "4" :
            case "5" : $sparkieAfterCorrectQues = 4;    //For attempts 3,4 & 5 on a topic, 1 sparkie to be given after 4 consecutive correct ques (Regular ques)
                       break;
            case "6" :
            case "7" :
            case "8" :
            case "9" : $sparkieAfterCorrectQues = 5;    //For attempts 6,7,8 & 9 on a topic, 1 sparkie to be given after 5 consecutive correct ques (Regular ques)
                       break;
            case ($ttAttemptNo>=10 && $ttAttemptNo<=20)  : $sparkieAfterCorrectQues = 10;   //For attempts 10 & onwards on a topic, 1 sparkie to be given after 10 consecutive correct ques (Regular ques)
                       break;
            default  : $sparkieAfterCorrectQues = -1;
                       break;
        }
        return $sparkieAfterCorrectQues;
    }
	function saveNCERTques($userID,$questionNo,$qcode,$A,$S,$R,$ncertattemptID,$exerciseCode,$sessionID, $eeresponse)
	{
		$eeresponseArr	=	explode("@$*@",$eeresponse);
		$sql = "UPDATE adepts_ncertQuesAttempt SET questionNo='$questionNo', attemptedDate='".date("Y-m-d")."', A='$A', S=$S, R='$R', eeresponse='".$eeresponseArr[0]."', eeResponseImg='".$eeresponseArr[1]."', sessionID=$sessionID WHERE ncertAttemptID=$ncertattemptID AND qcode=$qcode";
		mysql_query($sql) or die(mysql_error());
	}
    function saveDetails($userID, $questionNo, $qcode, $response,$seconds,$responseResult, $clusterAttemptID, $clusterCode, $sessionID,$startTime,$endTime, $loadTime, $quesType, $ttAttemptID, $teacherTopicCode,$dynamic,$dynamicParams,  $showAnswer, $subjectno, $noOfTrialsTaken, $eeresponse, $hintAvailable=0, $hintUsed=0, $tmpMode="", $userAllAnswers="",$timeTakenHints="",$isHintUsefull=0,$toughType,$toughResult,$promptNumber=0,$timeTakenToughQues=0,$extraParameters)
    {
        if($quesType=="challenge" || $quesType=="bonusCQ")
        {
			if($quesType=="challenge")
			{
            	saveChallengeQuesDetails($userID, $sessionID, $qcode, $response, $seconds, $responseResult, $ttAttemptID, $questionNo, $extraParameters, $eeresponse, $noOfTrialsTaken);
			}
			else
				saveChallengeQuesDetails($userID, $sessionID, $qcode, $response, $seconds, $responseResult, $ttAttemptID, $questionNo, $extraParameters, $eeresponse, $noOfTrialsTaken, 1);
        }
		else if($quesType=="wildcard")
		{
			$childClass  = $_SESSION['childClass'];
			$sq	=	"INSERT INTO adepts_researchQuesAttempt (userID,attemptedDate,questionNo,questionType,qcode,A,S,R,sessionID)
					 VALUES ($userID,'".date("Y-m-d")."','$questionNo','$tmpMode',$qcode,'$response','$seconds',$responseResult,$sessionID)";
			$rs	=	mysql_query($sq);
            $quesAttempt_srno = mysql_insert_id();
				saveLongUserResponse($quesAttempt_srno,$qcode,$response,$userID,$sessionID,"wildcard",$extraParameters);
			if($eeresponse != "NO_EE") // When question is having equation editor.
			{
				$eeresponseArr	=	explode("@$*@",$eeresponse);
				$query = "INSERT INTO adepts_equationEditorResponse (srno, childClass, userID, sessionID, qcode, question_type, eeResponse, eeResponseImg) VALUES(".$quesAttempt_srno.", ".$_SESSION['childClass'].",".$userID.",".$sessionID.",'".$qcode."','".$quesType."', '".$eeresponseArr[0]."', '".$eeresponseArr[1]."')";
				mysql_query($query) or die($query);
			}
            if($dynamic)
            {
				$query	=	"INSERT INTO adepts_dynamicParameters (userID, qcode, quesAttempt_srno, parameters, mode, class, lastModified) VALUES
            				($userID, $qcode, ".$quesAttempt_srno.", '$dynamicParams', 'wildcard','".$childClass."', '".date("Y-m-d H:i:s")."')";
            	mysql_query($query);
            }
		}
        else
        {
        	$childClass  = $_SESSION['childClass'];
            $query = "INSERT INTO ".TBL_QUES_ATTEMPT_CLASS."
                      (userID,attemptedDate,questionNo,qcode,A,S,R,clusterAttemptID,sessionID, clusterCode, teacherTopicCode, lastModified)
                      VALUES
                      ($userID,'".date("Y-m-d")."','$questionNo',$qcode,'$response','$seconds',$responseResult,'$clusterAttemptID',$sessionID,'$clusterCode','$teacherTopicCode', '".date("Y-m-d H:i:s")."')";
			if($responseResult > 3)
			{
				sendDataCheckMail($query,"2");
			}
            mysql_query($query);
            $quesAttempt_srno = mysql_insert_id();
				saveLongUserResponse($quesAttempt_srno,$qcode,$response,$userID,$sessionID,"normal",$extraParameters);

            $_SESSION['qid'] = $quesAttempt_srno;
			if($eeresponse != "NO_EE" && trim($eeresponse,"# ") != "") // When question is having equation editor.
			{
				$eeresponseArr	=	explode("@$*@",$eeresponse);
				$query = "INSERT INTO adepts_equationEditorResponse (srno, childClass, userID, sessionID, qcode, question_type, eeResponse, eeResponseImg) VALUES(".$quesAttempt_srno.", ".$_SESSION['childClass'].",".$userID.",".$sessionID.",'".$qcode."','".$quesType."', '".$eeresponseArr[0]."', '".$eeresponseArr[1]."')";
				mysql_query($query) or die($query);
			}
            if($dynamic)
            {
            	$query = "INSERT INTO adepts_dynamicParameters (userID, qcode, quesAttempt_srno, parameters, mode, class, lastModified) VALUES
            				($userID, $qcode, ".$quesAttempt_srno.", '$dynamicParams', 'normal','".$childClass."', '".date("Y-m-d H:i:s")."')";
            	mysql_query($query);
            }
            if($noOfTrialsTaken>0)
            {	
				if($toughResult==1)
					$toughAnswer = "Right";
				else if($toughResult == 0)
					$toughAnswer = "wrong";
			
				if($toughType == "Tough Question")
					$type = "Tough" . " - " .$toughAnswer." - ".$timeTakenToughQues;
				else
					$type = "regular";
			
				$userAllAnswersArr	=	explode("$#@",$userAllAnswers);
                $query = "INSERT INTO adepts_teacherTopicQuesTrialDetails (userID, sessionID, qcode, quesAttempt_srno, trials, trialNo, userResponse, type, lastModified)
                          VALUES ";
				for($k=0;$k<$noOfTrialsTaken;$k++)
				{
					$query .= "($userID, $sessionID, $qcode, $quesAttempt_srno, $noOfTrialsTaken, ".($k+1).", '".$userAllAnswersArr[$k]."','$type','".date("Y-m-d H:i:s")."'), ";
				}
				$query	=	substr($query,0,-2);
                mysql_query($query) or die(mysql_error().$query);
            }
            if(($toughResult==0||$toughResult==1)&&$noOfTrialsTaken==0&&$toughResult!='NA'){
                if($toughResult==1)
					$toughAnswer = "Right";
				else if($toughResult == 0)
					$toughAnswer = "wrong";
                $userAllAnswersArr	=	explode("$#@",$userAllAnswers);
                $type = "Tough" . " - " .$toughAnswer." - ".$timeTakenToughQues;
                $query = "INSERT INTO adepts_teacherTopicQuesTrialDetails (userID, sessionID, qcode, quesAttempt_srno, trials, trialNo, userResponse, type, lastModified)
                          VALUES ";
					$query .= "($userID, $sessionID, $qcode, $quesAttempt_srno, $noOfTrialsTaken, 0, '".$response."','$type','".date("Y-m-d H:i:s")."'), ";
				$query	=	substr($query,0,-2);
                mysql_query($query) or die(mysql_error().$query);
            }
			if($hintAvailable>0)
			{
				$sq	=	"INSERT INTO adepts_hintUsed (srno,childClass,userID,sessionID,qcode,hintUsed,timePerHint,isHintUsefull)
						 VALUES ($quesAttempt_srno, $childClass, $userID, $sessionID, $qcode, $hintUsed, '$timeTakenHints',$isHintUsefull)";
				mysql_query($sq);
			}
        }


		if($subjectno==2)
		{
	        $query = "INSERT INTO adepts_loadTime
	                      (userID,questionNo,qcode,sessionID,startTime,pageStartTime,loadTime, endTime)
	                      VALUES
	                      ($userID,'$questionNo',$qcode,$sessionID,'".$startTime."','".$_SESSION['pageStartTime']."',$loadTime, now())";

	        mysql_query($query);
		}
		
		//Filling in details for fastAnswerAnalysis (Applicable for all questions)
		if($promptNumber!=0)
		{
			$sql="Insert into adepts_fastAnsAnalysis(sessionID,userID,questionNo,questionCode,promptNo) values('$sessionID','$userID','$questionNo','$qcode','$promptNumber')";
			mysql_query($sql);
		}
    }

    function saveChallengeQuesDetails($userID, $sessionID, $qcode, $response, $seconds, $responseResult, $ttAttemptID, $questionNo, $extraParameters, $eeresponse, $noOfTrialsTaken=1, $EoLMode=NULL)
    {
        $CQAttemptNo = getCQAttemptNo($qcode);
		$query = "INSERT INTO adepts_ttChallengeQuesAttempt(userID, sessionID, qcode, A, S, R, ttAttemptID, questionNo, attemptNo, EoLMode) VALUES
				 ($userID, $sessionID, $qcode, '$response', $seconds, $responseResult, $ttAttemptID, $questionNo, '$CQAttemptNo', '$EoLMode')";
		
        mysql_query($query) or die(mysql_error().$query);
		$_SESSION["cqsrno"] = mysql_insert_id();
			saveLongUserResponse($_SESSION["cqsrno"],$qcode,$response,$userID,$sessionID,"challenge",$extraParameters);
        if($noOfTrialsTaken>1)
        {
            $quesAttempt_srno = mysql_insert_id();
            $query = "INSERT INTO adepts_teacherTopicQuesTrialDetails (userID, sessionID, qcode, quesAttempt_srno, trials, userResponse, type, lastModified)
                      VALUES ($userID, $sessionID, $qcode, $quesAttempt_srno, $noOfTrialsTaken, '$response', 'CQ','".date("Y-m-d H:i:s")."')";
            mysql_query($query) or die(mysql_error().$query);
        }
		if($eeresponse != "NO_EE" && trim($eeresponse,"# ") != "") // When question is having equation editor.
		{
			$eeresponseArr	=	explode("@$*@",$eeresponse);
			$query = "INSERT INTO adepts_equationEditorResponse (srno, childClass, userID, sessionID, qcode, question_type, eeResponse, eeResponseImg) VALUES(".$_SESSION["cqsrno"].", ".$_SESSION['childClass'].",".$userID.",".$sessionID.",'".$qcode."','challenge', '".$eeresponseArr[0]."', '".$eeresponseArr[1]."')";

			mysql_query($query) or die(mysql_error().$query);
		}
    }

    function checkForTimedTest($ttAttemptID, $clusterAttemptID, $clusterCode, $userID, $class,$mode=0)
    {
		
        $timedTestCode = "";
        if (isDailyDrillSchool($class)) return $timedTestCode;
        $rctFlag=checkForRCT($userID);		
		if(!$rctFlag)
		{
	        //check if there is a TT linked to this cluster
	        $query  = "SELECT timedTestCode, description FROM adepts_timedTestMaster WHERE linkedToCluster='".$clusterCode."' AND status='live'";
	        if($class!="")
	            $query .= " AND noOfQues_cl$class>0 ";
	        $query .= " ORDER BY timedTestOrder";
	        $result = mysql_query($query);
			if($mode==0){
		        if($line = mysql_fetch_array($result))
		        {
		                $cl_query  = "SELECT  clusterCode, attemptType FROM ".TBL_CLUSTER_STATUS." WHERE clusterAttemptID=$clusterAttemptID";
		                $cl_result = mysql_query($cl_query);
		                $cl_line   = mysql_fetch_array($cl_result);
		                if($cl_line['attemptType']!="N")    {    //if the cluster is cleared in a normal flow, then only TT is given or if the cluster is its own remedial
		                    //check if the cluster has been cleared as a remedial of its own, if so give tt. if it is a remedial cluster not in this topic, timed test will not be given
		                    $cl_query  = "SELECT clusterAttemptID, clusterCode, attemptType FROM ".TBL_CLUSTER_STATUS." WHERE ttAttemptID=$ttAttemptID AND clusterAttemptID<>$clusterAttemptID ORDER BY clusterAttemptID DESC limit 1";
		                    $cl_r      = mysql_query($cl_query);
		                    $cl_l      = mysql_fetch_array($cl_r);
		                    if($cl_l['attemptType']!="N" || $cl_l['clusterCode']!=$cl_line['clusterCode'])
		                        return $timedTestCode;
		                }
		                else
		                {
		                    //Give the timed test only if the cluster is cleared for the first time in the topic attempt
		                    $cl_count_query  = "SELECT count(clusterAttemptID) as cnt FROM ".TBL_CLUSTER_STATUS." WHERE ttAttemptID=$ttAttemptID AND clusterCode='$clusterCode' AND result='SUCCESS'";
		                    $cl_count_result = mysql_query($cl_count_query);
		                    $cl_count_line   = mysql_fetch_array($cl_count_result);
		                    if($cl_count_line['cnt']>=1)
		                        return $timedTestCode;
		                }

		                $timedTestCode = $line['timedTestCode'];
		                $pendingTT = "";
		                while ($line=mysql_fetch_array($result))
		                    $pendingTT = $line['timedTestCode'].",";
		                $pendingTT = substr($pendingTT,0,-1);

		                $query  = "SELECT * FROM adepts_timedTestStatus WHERE userID=$userID";
		                $tt_result = mysql_query($query);
		                if($tt_line=mysql_fetch_array($tt_result))
		                {
		                    $mode = "Update";
		                    if($tt_line['pendingTimedTest']!="")
		                    {
		                	    $tmpArray = explode(",",$tt_line['pendingTimedTest']);
		                	    if(in_array($timedTestCode,$tmpArray))
		                		    return "";
		                        if($pendingTT!="")
		                            $pendingTT = $pendingTT.",".$tt_line['pendingTimedTest'];
		                        else
		                            $pendingTT = $tt_line['pendingTimedTest'];
		                    }
		                }
		                else
		                    $mode = "Insert";

		                if($mode=="Insert")
		                    $query = "INSERT INTO adepts_timedTestStatus SET currentTimedTest='$timedTestCode', pendingTimedTest='$pendingTT', userID=$userID";
		                else
		                    $query = "UPDATE adepts_timedTestStatus SET currentTimedTest='$timedTestCode', pendingTimedTest='$pendingTT' WHERE userID=$userID";
		                mysql_query($query);
		        }
			}
	        elseif($mode==1)
			{
				while($line = mysql_fetch_array($result))
				{
					/*echo $line['timedTestCode']."<br>".$line['description']."<br>";*/
					$timedTestCode[$line['timedTestCode']] = $line['description'];
				}
	        }
	    }
        return $timedTestCode;
	
    }

    function checkForResearchModule($userID, $ttAttemptID, $clusterCode)
    {
    	$researchModuleToBeGiven = 0;
		$query  = "SELECT rmCode FROM adepts_researchModuleMaster WHERE linkedToCluster='$clusterCode' AND status=1";
		$result = mysql_query($query);
		if($line = mysql_fetch_array($result))
		{
			$rmCode = $line[0];
			$query  = "SELECT count(clusterAttemptID) FROM ".TBL_CLUSTER_STATUS." WHERE ttAttemptID=$ttAttemptID AND clusterCode='$clusterCode' AND result='SUCCESS'";
			$result = mysql_query($query);
			$line = mysql_fetch_array($result);
			if($line[0]==1)	//i.e. cleared the cluster for the first time
			{
				$_SESSION['rmCode'] = $rmCode;
				$researchModuleToBeGiven = 1;
			}
		}
		return $researchModuleToBeGiven;
    }

    function checkForGame($clusterCode, $ttAttemptID, $userID, $clusterAttemptID, $class,$mode=0,$extraCondition='')
    {
    	$gameID = "";
		$query	=	"SELECT gameID, phpFile, param, gameDesc, type FROM adepts_gamesMaster
					 WHERE linkedToCluster='".$clusterCode."' AND live=1 AND type IN ('regular','optional') AND ( class='' OR ISNULL(class) OR find_in_set('$class',class)>0)";
		if($_SESSION['flashContent']==0)
			$query .= " AND ver='html5'";		
		$result = mysql_query($query.$extraCondition);
        if($line = mysql_fetch_array($result))
		{			
            if($mode==0){
                $cl_query  = "SELECT  clusterCode, attemptType FROM ".TBL_CLUSTER_STATUS." WHERE clusterAttemptID=$clusterAttemptID";                
                $cl_result = mysql_query($cl_query);
                $cl_line   = mysql_fetch_array($cl_result);
                if($cl_line['attemptType']!="N")    {    //if the cluster is cleared in a normal flow, then only game is given or if the cluster is its own remedial
                    //check if the cluster has been cleared as a remedial of its own, if so give the game.
                    $cl_query  = "SELECT clusterAttemptID, clusterCode, attemptType FROM ".TBL_CLUSTER_STATUS." WHERE ttAttemptID=$ttAttemptID AND clusterAttemptID<>$clusterAttemptID ORDER BY clusterAttemptID DESC limit 1";
                    $cl_r      = mysql_query($cl_query);
                    $cl_l      = mysql_fetch_array($cl_r);
                    if($cl_l['attemptType']!="N" || $cl_l['clusterCode']!=$cl_line['clusterCode'])
                        return $gameID;
                }    
				//Give the game only if the cluster is cleared for the first time in the topic attempt
				$cl_count_query  = "SELECT count(clusterAttemptID) as cnt FROM ".TBL_CLUSTER_STATUS." WHERE ttAttemptID=$ttAttemptID AND clusterCode='$clusterCode' AND result='SUCCESS'";				
				$cl_count_result = mysql_query($cl_count_query);
				$cl_count_line   = mysql_fetch_array($cl_count_result);
				if($cl_count_line['cnt']==0 && $line['type']=='regular') //i.e. clearing for the first time
				{
					$gameID = $line['gameID'];
					$query = "UPDATE ".TBL_CURRENT_STATUS." SET gameID='$gameID' WHERE userID=$userID AND ttAttemptID=$ttAttemptID";
					mysql_query($query);
					$_SESSION['game'] = true;
				}
            }
			elseif($mode==1){
				$ttAttemptIDCheck="";
				if ($ttAttemptID!="") $ttAttemptIDCheck="ttAttemptID=$ttAttemptID AND ";
				$cl_count_query  = "SELECT count(clusterAttemptID) as cnt FROM ".TBL_CLUSTER_STATUS." WHERE $ttAttemptIDCheck clusterCode='$clusterCode' AND result='SUCCESS'";				
				$cl_count_result = mysql_query($cl_count_query);
				$cl_count_line   = mysql_fetch_array($cl_count_result);
				if($cl_count_line['cnt']>0){
			    	$gameID = array();
                	$gameID['gameID']=$line['gameID'];
                	$gameID['gameName']=$line['gameDesc'];
				}
				
			}

		}		
        return $gameID;
    }
    function getSDLCountInCluster()
    {
	    $sdlArray = array_keys($_SESSION["allQuestionsArray"]);
	    sort($sdlArray,SORT_NUMERIC);
	    return count($sdlArray);
    }
   	
	function isDCT($clusterAttemptNo,$clusterCode, $ttAttemptID)
	{
		//DCT elements position array
		//$dctPositions = array("DEC062", "DEC018", "DEC020", "DEC023", "DEC050", "DEC016","DEC005","DEC054");
		$dctPositions = array("DEC062", "DEC018", "DEC020", "DEC023", "DEC050", "DEC016"); //DCT - 1 and DCT -2 Removed.
		//DCT flow for different groups
		$dctFlow = array(
						"L,L1" => array("DCT","23","25~DA_D","22~FPH_IZSL","24~NB_L1"),
						"L,L2" => array("DCT","23","25~DA_W","22~FPH_LS"),
						"L,L3" => array("DCT","23","25~DA_D","22~FPH_SL","24~NB_L3"),
						"S,S1" => array("DCT","23","24~NB_S1","22~FPH_LL"),
						"S,S3" => array("DCT","23","22~FPH_SSLL","24~NB_S3"),
						"AE" => array("DCT","23","25~DA","22~FPH","24"),
						"UN" => array("DCT","23","25~DA","22~FPH","24"),
						"S,UN" => array("DCT","23","22~FPH_LL","24~NB_S1","24~NB_S3"),
						"L,UN" => array("DCT","23","25~DA_D","22~FPH_SL","24~NB_L3")
						);
		$statusArray = "";
		//Check if DCT is applicable...
		if(in_array($clusterCode,$dctPositions))
		{
			$sql = "SELECT count(clusterAttemptID) as successNumbers FROM ".TBL_CLUSTER_STATUS." WHERE ttAttemptID='$ttAttemptID' AND clusterCode='$clusterCode' AND result='SUCCESS'";
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			$successNumbers = $row['successNumbers'];
			if($successNumbers == 0)
			{
				//Check if is there any row associated with ttAttemptID in DCTdetails Table...
				$sql = "SELECT status, firstDCT, secondDCT FROM adepts_dctDetails WHERE ttAttemptID = '$ttAttemptID'";
				$result = mysql_query($sql);
				if(mysql_num_rows($result) == 0)//If not then insert row and staus will be blank as its newely inserted row..
				{
					$sql = "INSERT INTO adepts_dctDetails (userID, ttAttemptID) VALUES ('".$_SESSION["userID"]."', '$ttAttemptID')";
					mysql_query($sql);
					$status = "";
					$firstDCT = "";
					$secondDCT = "";
				}
				else//GET the status otherwise..
				{
					$row = mysql_fetch_assoc($result);
					$status = $row['status'];
					$firstDCT = $row['firstDCT'];
					$secondDCT = $row['secondDCT'];
				}

				//Explode the DCT status to divide different stages of DCT in flow...
				if($status != "")
					$statusArray = explode("||",$status);

				//Get category code of child if its has been saved by hidden Numbers (i.e [L,L1], [L,L2], [S,S1] etc)
				if(isset($statusArray[1]))
				{
					$hiddenNumberStatus = explode("-",$statusArray[1]);
					$childCategoryCode = $hiddenNumberStatus[1];
				}
				if($clusterCode == "DEC062")
				{
					if(!isset($statusArray[0]))
						return "DCT~1";
					else if(isset($statusArray[1]) && $statusArray[1]!="") //This check is for the case, if user performs DCT & logout after it, then he will come here again. And to prevent DCT case again
						return "false";
					else
						return "23"; //returning Hidden Number Game code.
				}
				else if($clusterCode == "DEC018")
				{
					if((isset($statusArray[2]) && $statusArray[2]!="") || !isset($dctFlow[$childCategoryCode][2]))
						return "false";
					else
						return $dctFlow[$childCategoryCode][2]; //return Game with Game Code i.e (gameID~gameCode) format.. from array $dctFlow...

				}
				else if($clusterCode == "DEC020")
				{
					if((isset($statusArray[3]) && $statusArray[3]!="") || !isset($dctFlow[$childCategoryCode][3]))
						return "false";
					else
						return $dctFlow[$childCategoryCode][3];
				}
				else if($clusterCode == "DEC023")
				{
					if((isset($statusArray[4]) && $statusArray[4]!="") || !isset($dctFlow[$childCategoryCode][4]))
						return "false";
					else
						return $dctFlow[$childCategoryCode][4];
				}
				else if($clusterCode == "DEC050")
				{
					if((isset($statusArray[5]) && $statusArray[5]!="") || !isset($dctFlow[$childCategoryCode][5]))
						return "false";
					else
						return $dctFlow[$childCategoryCode][5];
				}
				else if($clusterCode == "DEC016")//Checking for different clusters
				{
					$temp = explode("-",$statusArray[count($statusArray)-1]); //Check if last attempt is DCT in DCT flow
					if(isset($temp[0]) && $temp[0] == "postDCT")
						return "false";
					else
						return "DCT~2"; //Returning DCT stage
				}
				/*else if($clusterCode == "DEC005")//Checking for different clusters // Removing this for Decimal on IPad TT
				{
					if($firstDCT != "") //Check if already attempted DCT in DCT flow
						return "false";
					else
						return "DCT~3"; //Returning DCT stage
				}
				else if($clusterCode == "DEC054")//Checking for different clusters // Removing this for Decimal on IPad TT
				{
					if($secondDCT != "") //Check if already attempted DCT in DCT flow
						return "false";
					else
						return "DCT~4"; //Returning DCT stage
				}*/
				else
				{
					return "false";
				}
			}
			else
			{
				return "false";
			}
		}
		else
		{
			return "false";
		}
	}

	function setPendingDCT($stage,$ttAttemptID)
	{
		$sql = "UPDATE adepts_dctDetails SET current = '$stage' WHERE ttAttemptID = '$ttAttemptID'";
	    mysql_query($sql);
	}
	
	function saveLongUserResponse($quesAttempt_srno,$qcode,$response,$userID,$sessionID,$type,$extraParameters) //for saving long responses and extraParameters
	{
		if(strlen($response)>255 || $extraParameters!=''){
			$sq = "INSERT INTO longUserResponse SET srno=$quesAttempt_srno,qcode=$qcode,userID=$userID,sessionID=$sessionID,type='$type'";
			if(strlen($response)>255)
				$sq .= ",userResponse='$response'";
			if($extraParameters!='')
				$sq .= ",extraParameters='$extraParameters'";
			mysql_query($sq);
		}
	}
	function nextPractiseModuleQuestion($qcode,$timedTestCode,$quesno,$response,$responseResult,$secs,$sessionID,$dynamic,$dynamicParams,$userID,$questionLevel,$attemptNo,$practiseModuleId,$practiseModuleTestStatusId,$timedTestAttemptId,$extraParameters=''){
		$childClass	=	$_SESSION['childClass'];
		$currentLevel=$questionLevel;
		$queryLevel = mysql_query("SELECT qCodes,timedTest,hasScore, completionCriteria FROM practiseModuleLevels WHERE practiseModuleId='$practiseModuleId' AND levelNumber=$currentLevel");
		
		$resultLevel = mysql_fetch_assoc($queryLevel);
		$completionCriteria=explode("~", $resultLevel['completionCriteria']);
			$totalRightCriteria=$completionCriteria[0];
			$totalQuesCriteria=$completionCriteria[1];
		$hasScore=$resultLevel['hasScore'];
		$qcodesAvailable=explode(",", $resultLevel['qCodes']);
		$timedTestCodeAvailable=$resultLevel['timedTest'];
		$unAttemptedQuestions=array();
		$thisScore=0;
		$levelComplete=0;
		$newQuestion=0;
		//echo $timedTestCode."||".$timedTestAttemptId."||".$qcode;exit;
		if ($timedTestCode!="" || $timedTestAttemptId!="" || $qcode!=""){
			if ($timedTestCode!="" && $timedTestAttemptId!=""){
				$insertTestDetailsSQL="INSERT INTO practiseModulesTimedTestAttempt (userID, attemptDate, sessionID, practiseModuleId, level, timedTestAttemptId, attemptNo, practiseModuleTestStatusId) VALUES ($userID, date(NOW()), $sessionID, '$practiseModuleId', $currentLevel, $timedTestAttemptId, $attemptNo, $practiseModuleTestStatusId)";
				mysql_query($insertTestDetailsSQL) or die("error 4".mysql_error().$insertTestDetailsSQL);
				mysql_query("UPDATE practiseModulesTestStatus SET lastModified=NOW() where userID = $userID AND id = $practiseModuleTestStatusId AND attemptNo = $attemptNo");
				$query=mysql_query("SELECT perCorrect FROM adepts_timedTestDetails where timedTestID=$timedTestAttemptId");
				if (mysql_num_rows($query)>0) {
					$res=mysql_fetch_array($query);
					$thisScore+=($hasScore=="")?0:($res[0]>75?$hasScore:0);
				}
				//$quesno=0;
				$levelComplete=1;
			}
			else if ($qcode!=""){
				$idDynamic  = $dynamic == 1 ?  1 :  0;
				$insertTestDetailsSQL = "INSERT INTO practiseModulesQuestionAttemptDetails (userID, attemptDate, qcode, qno, A, S, R, sessionID,practiseModuleId,isDynamic,questionLevel,attemptNo,practiseModuleTestStatusId) VALUES ($userID,date(NOW()),$qcode,$quesno,'$response','$secs',$responseResult,$sessionID,'$practiseModuleId',$idDynamic,$currentLevel,$attemptNo,$practiseModuleTestStatusId)";
				mysql_query($insertTestDetailsSQL) or die("error 4".mysql_error().$insertTestDetailsSQL);
				$quesAttempt_srno = mysql_insert_id();
					saveLongUserResponse($quesAttempt_srno,$qcode,$response,$userID,$sessionID,"practiseModule",$extraParameters);

				$thisScore+=($hasScore=="")?0:($responseResult?$hasScore:0);

				if($idDynamic){
					$query	=	"INSERT INTO adepts_dynamicParameters (userID, qcode, quesAttempt_srno, parameters, mode, class, lastModified) VALUES ($userID, $qcode, ".$quesAttempt_srno.", '$dynamicParams', 'practiseModule','".$childClass."', '".date("Y-m-d H:i:s")."')";
					mysql_query($query);	
				}

				$queryLevel = mysql_query("SELECT GROUP_CONCAT(qcode) as allQcodes, GROUP_CONCAT(IF(isDynamic=1,'',qcode)) as allStaticQcodes, COUNT(*) qcount, SUM(if(R=1,1,0)) correctCount FROM practiseModulesQuestionAttemptDetails WHERE questionLevel=$currentLevel AND practiseModuleTestStatusId=$practiseModuleTestStatusId ");
				$resultLevel = mysql_fetch_assoc($queryLevel);
				//echo '<br>query'.$queryLevel;
				if ($totalRightCriteria==0 && $totalQuesCriteria==0){
					$unAttemptedQuestions=array_diff($qcodesAvailable, explode(",",$resultLevel['allQcodes']));
					if (count($unAttemptedQuestions)==0) $levelComplete=1;
				}
				else {
					$unAttemptedQuestions=array_diff($qcodesAvailable, explode(",",$resultLevel['allStaticQcodes']));
					if ($totalRightCriteria>0){
						if ($resultLevel['correctCount']>=$totalRightCriteria) $levelComplete=1;
					}
					if ($totalQuesCriteria>0){
						if ($resultLevel['qcount']>=$totalQuesCriteria) $levelComplete=1;
					}
				}
			}
			$parametersToUpdate = "lastAttemptQue = if(lastAttemptQue >= $quesno,lastAttemptQue,$quesno), score = score+$thisScore, currentLevel=$currentLevel";
			$updateQuery = "UPDATE practiseModulesTestStatus SET $parametersToUpdate where userID = $userID AND id = $practiseModuleTestStatusId AND attemptNo = $attemptNo";
			mysql_query($updateQuery) or die("error 2".mysql_error().$updateQuery);
		}
		else {//echo '<br>'.$timedTestCodeAvailable;
			$newQuestion=1;
			if ($timedTestCodeAvailable!=""){
				$queryLevel = mysql_query("SELECT timedTestAttemptId FROM practiseModulesTimedTestAttempt WHERE level=$currentLevel AND practiseModuleTestStatusId=$practiseModuleTestStatusId");
				$resultLevel = mysql_fetch_assoc($queryLevel);
				$timedTestCode=$timedTestCodeAvailable;
				if (mysql_num_rows($resultLevel)>0) $levelComplete=1;
				//echo '<br>'.mysql_num_rows($resultLevel)."SELECT timedTestAttemptId FROM practiseModulesTimedTestAttempt WHERE practiseModuleId='$practiseModuleId' AND level=$currentLevel AND practiseModuleTestStatusId=$practiseModuleTestStatusId AND userID=$userID";
			}
			else {
				$queryLevel = mysql_query("SELECT GROUP_CONCAT(qcode) as allQcodes, GROUP_CONCAT(IF(isDynamic=1,'',qcode)) as allStaticQcodes, COUNT(*) qcount, SUM(if(R=1,1,0)) correctCount FROM practiseModulesQuestionAttemptDetails WHERE questionLevel=$currentLevel AND practiseModuleTestStatusId=$practiseModuleTestStatusId");
				$resultLevel = mysql_fetch_assoc($queryLevel);

				if ($totalRightCriteria==0 && $totalQuesCriteria==0){
					$unAttemptedQuestions=array_diff($qcodesAvailable, explode(",",$resultLevel['allQcodes']));
					if (count($unAttemptedQuestions)==0) $levelComplete=1;
				}
				else {
					$unAttemptedQuestions=array_diff($qcodesAvailable, explode(",",$resultLevel['allStaticQcodes']));
					if ($totalRightCriteria>0 || count($unAttemptedQuestions)==0){
						if ($resultLevel['correctCount']>=$totalRightCriteria) $levelComplete=1;
					}
					if ($totalQuesCriteria>0 || count($unAttemptedQuestions)==0){
						if ($resultLevel['qcount']>=$totalQuesCriteria) $levelComplete=1;
					}
				}
			}
		}
		
		$practiceModuleComplete=0;
		if ($levelComplete==1) {
			$currentLevel++;
			$queryLevel = mysql_query("SELECT qCodes,timedTest,hasScore, completionCriteria FROM practiseModuleLevels WHERE practiseModuleId='$practiseModuleId' AND levelNumber=$currentLevel");
			if (mysql_num_rows($queryLevel)==0) $practiceModuleComplete=1;
			else{
				$resultLevel = mysql_fetch_assoc($queryLevel);

				$unAttemptedQuestions=explode(",", $resultLevel['qCodes']);
				$timedTestCode=$resultLevel['timedTest'];

				$parametersToUpdate = "currentLevel=$currentLevel";
				$updateQuery = "UPDATE practiseModulesTestStatus SET $parametersToUpdate where userID = $userID AND id = $practiseModuleTestStatusId AND attemptNo = $attemptNo";
				mysql_query($updateQuery) or die("error 2".mysql_error().$updateQuery);
			}
		}
		$query=mysql_query("SELECT q.practiseModuleId, levelNumber, if (qCodes='','timedTest','question') levType, w.* FROM practiseModulesTestStatus z,practiseModuleLevels q LEFT JOIN (SELECT questionLevel as 'level', GROUP_CONCAT(qcode) 'levelContent', SUM(IF(R=1,1,0)) 'score', COUNT(*) 'onTotal', ROUND(SUM(IF(R=1,1,0))*100/COUNT(*),2) accuracy,'question' as levelType FROM practiseModulesQuestionAttemptDetails WHERE practiseModuleTestStatusId=$practiseModuleTestStatusId  GROUP BY questionLevel  UNION SELECT level as 'level', GROUP_CONCAT(timedTestAttemptId) 'levelContent', if(b.perCorrect>75,10,0) 'score', if(b.perCorrect>75,".(($attemptNo<6)?(6-$attemptNo):1).",0) 'onTotal', b.perCorrect accuracy,'timedTest' as levelType FROM practiseModulesTimedTestAttempt a JOIN adepts_timedTestDetails b ON b.timedTestID=a.timedTestAttemptId WHERE practiseModuleTestStatusId=$practiseModuleTestStatusId  GROUP BY level ORDER BY 'level') w ON q.levelNumber=w.level WHERE z.id=$practiseModuleTestStatusId AND q.practiseModuleId=z.practiseModuleId");
		$res=array();
		while ($result=mysql_fetch_assoc($query)){
			$res[]=$result;
		}
		$_SESSION['dailyDrillArray']['levelsAttempted']=json_encode($res);

		$query=mysql_query("SELECT score,lastAttemptQue FROM practiseModulesTestStatus WHERE id=$practiseModuleTestStatusId");
		$result=mysql_fetch_assoc($query);
		$_SESSION['dailyDrillArray']['currentScore']=$result['score'];
		$quesno=$result['lastAttemptQue'];
		if ($practiceModuleComplete){
			
			$query=mysql_query("SELECT SUM(IF(R=1,1,0)) 'score', COUNT(*) 'onTotal', SUM(S) FROM practiseModulesQuestionAttemptDetails WHERE practiseModuleTestStatusId=$practiseModuleTestStatusId");
			$queryArray=mysql_fetch_array($query);
			$accuracy=$queryArray[1]!=0?$queryArray[0]/$queryArray[1]:0;
			if ($accuracy<0.5) $noOfSparkies=0;
			else if ($accuracy<0.6) $noOfSparkies=1;
			else if ($accuracy<0.7) $noOfSparkies=2;
			else if ($accuracy<0.8) $noOfSparkies=3;
			else if ($accuracy<0.9) $noOfSparkies=4;
			else $noOfSparkies=5;

			$attemptSparkie=$attemptNo<6?6-$attemptNo:1;
			$noOfSparkies=round($noOfSparkies*($attemptSparkie*2)/10);
			
			$speedCalArray = array();
			$speedCalArray['correctQuesCount'] = $queryArray[0];
			$speedCalArray['totalQuesCount'] = $queryArray[1];
			$speedCalArray['timeSpent'] = $queryArray[2];

			$sparkieForSpeed = paractiseModuleSparkiesBasedOnSpeed($speedCalArray, $practiseModuleTestStatusId, $practiseModuleId);
			
			$_SESSION['dailyDrillArray']['accuracy'] = round($accuracy*100);
			$_SESSION['dailyDrillArray']['completionSparkie']['accuracy'] = $noOfSparkies;
			$_SESSION['dailyDrillArray']['completionSparkie']['speed'] = $sparkieForSpeed;
			$_SESSION['dailyDrillArray']['attemptNo'] = $attemptNo;
			
			if($attemptNo>5)
			{
				$noOfSparkies = 0;
				$sparkieForSpeed = 0;
				$_SESSION['dailyDrillArray']['completionSparkie']['accuracy'] = 0;
				$_SESSION['dailyDrillArray']['completionSparkie']['speed'] = 0;
			}

			$query=mysql_query("UPDATE practiseModulesTestStatus SET status='completed' WHERE id=$practiseModuleTestStatusId");
			addSparkies($noOfSparkies+$sparkieForSpeed,$sessionID);
			$_SESSION['sparkie']['practise']= $_SESSION['sparkie']['practise'] + $noOfSparkies + $sparkieForSpeed;
			return -99;
		}

		if ($timedTestCode!=""){
			$_SESSION['dailyDrillArray']['nextLevelType']='timedTest';
			return $timedTestCode.'~timedTest~'.$currentLevel;
		}
		else if (count($unAttemptedQuestions)>0){
			$_SESSION['dailyDrillArray']['nextLevelType']='question';
			$qcode=$unAttemptedQuestions[array_rand($unAttemptedQuestions)];
			return $qcode.'~question~'.$currentLevel.'~'.$quesno;
		}
		else {
			$practiceModuleComplete=0;$currentLevel++;
			$queryLevel = mysql_query("SELECT qCodes,timedTest,hasScore, completionCriteria FROM practiseModuleLevels WHERE practiseModuleId='$practiseModuleId' AND levelNumber=$currentLevel");
			if (mysql_num_rows($queryLevel)==0) $practiceModuleComplete=1;
			else{
				$resultLevel = mysql_fetch_assoc($queryLevel);
				$unAttemptedQuestions=explode(",", $resultLevel['qCodes']);
				$timedTestCode=$resultLevel['timedTest'];

				$parametersToUpdate = "currentLevel=$currentLevel";
				$updateQuery = "UPDATE practiseModulesTestStatus SET $parametersToUpdate where userID = $userID AND id = $practiseModuleTestStatusId AND attemptNo = $attemptNo";
				mysql_query($updateQuery) or die("error 2".mysql_error().$updateQuery);
			}
			$query=mysql_query("SELECT q.practiseModuleId, levelNumber, if (qCodes='','timedTest','question') levType, w.* FROM practiseModulesTestStatus z,practiseModuleLevels q LEFT JOIN (SELECT questionLevel as 'level', GROUP_CONCAT(qcode) 'levelContent', SUM(IF(R=1,1,0)) 'score', COUNT(*) 'onTotal', ROUND(SUM(IF(R=1,1,0))*100/COUNT(*),2) accuracy , 'question' as levelType FROM practiseModulesQuestionAttemptDetails WHERE practiseModuleTestStatusId=$practiseModuleTestStatusId  GROUP BY questionLevel  UNION SELECT level as 'level', GROUP_CONCAT(timedTestAttemptId) 'levelContent', if(b.perCorrect>75,10,0) 'score', if(b.perCorrect>75,".(($attemptNo<6)?(6-$attemptNo):1).",0) 'onTotal', b.perCorrect accuracy,'timedTest' as levelType FROM practiseModulesTimedTestAttempt a JOIN adepts_timedTestDetails b ON b.timedTestID=a.timedTestAttemptId WHERE practiseModuleTestStatusId=$practiseModuleTestStatusId  GROUP BY level ORDER BY 'level') w ON q.levelNumber=w.level WHERE z.id=$practiseModuleTestStatusId AND q.practiseModuleId=z.practiseModuleId");
			$res=array();
			while ($result=mysql_fetch_assoc($query)){
				$res[]=$result;
			}
			$_SESSION['dailyDrillArray']['levelsAttempted']=json_encode($res);

			$query=mysql_query("SELECT score,lastAttemptQue FROM practiseModulesTestStatus WHERE id=$practiseModuleTestStatusId");
			$result=mysql_fetch_assoc($query);
			$_SESSION['dailyDrillArray']['currentScore']=$result['score'];
			$quesno=$result['lastAttemptQue'];
			if ($practiceModuleComplete){
				$query=mysql_query("SELECT SUM(IF(R=1,1,0)) 'score', COUNT(*) 'onTotal', SUM(S) FROM practiseModulesQuestionAttemptDetails WHERE practiseModuleTestStatusId=$practiseModuleTestStatusId");
				$queryArray=mysql_fetch_array($query);
				$accuracy=$queryArray[1]!=0?$queryArray[0]/$queryArray[1]:0;
				if ($accuracy<0.5) $noOfSparkies=0;
				else if ($accuracy<0.6) $noOfSparkies=1;
				else if ($accuracy<0.7) $noOfSparkies=2;
				else if ($accuracy<0.8) $noOfSparkies=3;
				else if ($accuracy<0.9) $noOfSparkies=4;
				else $noOfSparkies=5;

				$attemptSparkie=$attemptNo<6?6-$attemptNo:1;
				$noOfSparkies=round($noOfSparkies*($attemptSparkie*2)/10);

				$speedCalArray = array();
				$speedCalArray['correctQuesCount'] = $queryArray[0];
				$speedCalArray['totalQuesCount'] = $queryArray[1];
				$speedCalArray['timeSpent'] = $queryArray[2];

				$sparkieForSpeed = paractiseModuleSparkiesBasedOnSpeed($speedCalArray, $practiseModuleTestStatusId, $practiseModuleId);				

				$_SESSION['dailyDrillArray']['accuracy'] = round($accuracy*100);
				$_SESSION['dailyDrillArray']['completionSparkie']['accuracy'] = $noOfSparkies;
				$_SESSION['dailyDrillArray']['completionSparkie']['speed'] = $sparkieForSpeed;
				$_SESSION['dailyDrillArray']['attemptNo'] = $attemptNo;

				$query=mysql_query("UPDATE practiseModulesTestStatus SET status='completed' WHERE id=$practiseModuleTestStatusId");
				addSparkies($noOfSparkies+$sparkieForSpeed,$sessionID);
				if(!isset($_SESSION['sparkie']['practise'])) $_SESSION['sparkie']['practise']=0;
				$_SESSION['sparkie']['practise']= $_SESSION['sparkie']['practise']+ $noOfSparkies+ $sparkieForSpeed;
				return -99;
			}

			if ($timedTestCode!=""){
				$_SESSION['dailyDrillArray']['nextLevelType']='timedTest';
				return $timedTestCode.'~timedTest~'.$currentLevel;
			}
			else if (count($unAttemptedQuestions)>0){
				$_SESSION['dailyDrillArray']['nextLevelType']='question';
				$qcode=$unAttemptedQuestions[array_rand($unAttemptedQuestions)];
				return $qcode.'~question~'.$currentLevel.'~'.$quesno;
			}
		}
	}
	function paractiseModuleSparkiesBasedOnSpeed($speedCalArray,$practiseModuleTestStatusId, $practiseModuleId)
	{
		$query = "SELECT SUM(IF(b.result=1,1,0)),COUNT(b.timedTestID),SUM(b.S) from practiseModulesTimedTestAttempt a , adepts_timedTestQuesAttempt b where a.timedTestAttemptId=b.timedTestID and a.practiseModuleTestStatusId=$practiseModuleTestStatusId";
		$result = mysql_query($query);
		$queryArray=mysql_fetch_array($result);
		
		$speedCalArray['correctQuesCount'] += $queryArray[0];
		$speedCalArray['totalQuesCount'] += $queryArray[1];
		$speedCalArray['timeSpent'] += $queryArray[2];

		$overallAccuracy = $speedCalArray['totalQuesCount'] > 0 ? $speedCalArray['correctQuesCount']/$speedCalArray['totalQuesCount'] : 0;
		$avgTimePerQues = $speedCalArray['totalQuesCount'] > 0 ? $speedCalArray['timeSpent']/$speedCalArray['totalQuesCount'] : 0;

		$_SESSION['dailyDrillArray']['avgTimePerQues'] = round($avgTimePerQues,1);

		$benchMarkQuery = mysql_query("SELECT percentile50, percentile75 from practiseModuleDetails where practiseModuleId='".$practiseModuleId."'");
		$benchMarkArray=mysql_fetch_array($benchMarkQuery);

		$_SESSION['dailyDrillArray']['benchmark50'] = $benchMarkArray[0];
		$_SESSION['dailyDrillArray']['benchmark75'] = $benchMarkArray[1];
		$sparkieForSpeed = 0;
		if($overallAccuracy >= 0.7)
		{
			if($avgTimePerQues <= $benchMarkArray[1])
				$sparkieForSpeed = 4;
			else if($avgTimePerQues <= $benchMarkArray[0])
				$sparkieForSpeed = 2;			
		}

		return $sparkieForSpeed; 
	}
?>
