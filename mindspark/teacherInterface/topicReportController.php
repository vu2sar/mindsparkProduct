<?php 		
	@include("../userInterface/check1.php");
	include("../userInterface/constants.php");
	include("../userInterface/classes/clsUser.php");
	include_once("../userInterface/functions/orig2htm.php");
	include_once("../userInterface/classes/clsTopicProgress.php");
	include_once("../userInterface/classes/clsTeacherTopic.php");
	include_once("../userInterface/classes/clsDiagnosticTestQuestion.php");
	include_once("../userInterface/classes/clsQuestion.php");	
	include("functions/topicReportFunctions.php");
	include("../userInterface/functions/functionsForDynamicQues.php");
	include("functions/functions.php");	
	if(!isset($_SESSION['userID']))
	{		
		$logout['logout'] = 1;
		echo json_encode($logout);	
		exit;	
	}
	$class		= $_GET['cls'];
	$section 	= $_GET['section'];
	$ttCode		= $_GET['ttCode'];
	$mode 		= $_GET['mode'];		
	$schoolCode = isset($_SESSION['schoolCode'])?$_SESSION['schoolCode']:"";	
	$user   	= new User($userID);	
	$userDetails= getStudentDetails($class, $schoolCode, $section);  	
	$userIDs	= array_keys($userDetails);
	$userIDstr	= implode(",",$userIDs);		
	$q = "SELECT DISTINCT flow FROM adepts_teacherTopicActivation WHERE class =$class AND teacherTopicCode='".$ttCode."' AND schoolCode=$schoolCode";	
	if($section!="")
	{			
		$q .= " AND section in ('".str_replace(',', "','", $section)."')";
	}		
    $r = mysql_query($q);
    while($l = mysql_fetch_array($r))
    {
    	$flowArray[] = $l[0];
    }       
   	foreach($flowArray as $flow)
   	{   		
		$ttObj = new teacherTopic($ttCode,$class,$flow);
		$clusterArray[] = $ttObj->getClustersOfLevel($class);
   	}   
   	$clusters = array_unique(array_reduce($clusterArray, 'array_merge', array()));     	
	$clusterString = implode("','", $clusters);
	//get cluster detail
	$clusterDetail =  getClusterDetails($clusterString);	

	switch ($mode)
	{
		case "topicInfo" :
			$topicDetailsArray = array();
			
			$topicDetails = getTopicInfo($class,$section,$ttCode,$_SESSION['schoolCode']);
			$postfix = $topicDetails['days'] > 1 ? "s" : "";
			if($topicDetails['active'])
			{
				$newDate = date("d M Y", strtotime($topicDetails['activationDate']));				
				$message1 = 'Active Since : ';
				$message2 = $topicDetails['days'] != 0 ? $newDate.' ('.$topicDetails["days"].' day'.$postfix.' ago)' : $newDate;			
			}
			else
			{
				$newDate = date("d M Y", strtotime($topicDetails['deactivationDate']));				
				$message1 = 'Deactivated on : ';
				$message2 = $topicDetails['days'] != 0 ? $newDate.' (was active for '.$topicDetails['days'].' day'.$postfix.' )' : $newDate;
			}
			
			$topicDetailsArray['name'] = $topicDetails['name'];
			$topicDetailsArray['flow'] = $topicDetails['flow'];
			$topicDetailsArray['message1'] = $message1;
			$topicDetailsArray['message2'] = $message2;			
			$topicInfoArray['userID'] = $_SESSION['userID'];
			$topicInfoArray['sessionID'] = $_SESSION['sessionID'];
			$topicInfoArray['topicDetails'] = $topicDetailsArray;
			$topicInfoJson = json_encode($topicInfoArray);
			echo $topicInfoJson;		
			break;
		case  "timeToComplete":
			$estimatedTimeToCompleteArray = getEstimatedTimeToComplete($clusters,$class,$userIDstr);	
			$estimatedTimeToCompleteArray["heading"] = "Time to complete";
			$estimatedTimeToCompleteArray["icon"] = "time_to_complete";
			$estimatedTimeToCompleteJson = json_encode($estimatedTimeToCompleteArray);
			echo $estimatedTimeToCompleteJson;
			break;

	    case "progressReport":		    	
			$topicReport =  getTeacherTopicProgress($ttCode,$userIDstr,$class,$userDetails);			
			$topicReportArray["topicProgress"] = array(
				"progress" => $topicReport["avgProgress"],
				"heading"  => "Topic progress",
				"icon"     => "progress",
			);			
			$topicReportArray["progressSummary"]["total_students"] = count($userIDs);			
			unset($topicReport["avgProgress"]);			
			$topicReportSummary = progressSummary($topicReport);
			foreach ($topicReportSummary as $key => $value) {
				if($key == 0)
				{
					$range = "(0 - 50%)";
					$color = "#F98053";
					$strokeColor = "#F9561F";
					$type = 'Low';			
				}
				else if($key == 1)
				{
					$range = "(50 - 100%)";
					$color = "#FBD542";
					$strokeColor = "#F5B52A";
					$type = 'Average';
				}
				else if($key == 2)
				{
					$range = "completed 100%";
					$color = "#79D84B";
					$strokeColor = "#6EBA50";
					$type = 'Good';
				}
				$topicReportArray["progressSummary"]["student_info"][$key]["range"] = $range;	
				$topicReportArray["progressSummary"]["student_info"][$key]["color"] = $color; 
        		$topicReportArray["progressSummary"]["student_info"][$key]["strokeColor"] = $strokeColor;	
				$topicReportArray["progressSummary"]["student_info"][$key]["students"] = $value;
				$topicReportArray["progressSummary"]["student_info"][$key]["type"] = $type;
				$total[$key] = count($value);
				$topicReportArray["chartData"][$key]["color"] = $color;
				$topicReportArray["chartData"][$key]["data"] = $total[$key];
				$topicReportArray["chartData"][$key]["label"] = $total[$key]." - ".$type;
				$topicReportArray["chartData"][$key]["strokeColor"] = $strokeColor;	
			}					

			$topicJson = json_encode($topicReportArray);
			echo $topicJson;
	    	break;

    	case "assessmentReport":	    	
			$assessmentDetails = array();	
			$assessmentFlag = 0;								
			$customTopic = $ttObj->customTopic;
			$parentTeacherTopicCode = $ttObj->parentTTCode;
			$topicFlow = $ttObj->flow;
			$coteacherTopicFlag = checkForCoteacherTopic($ttCode,$class,$customTopic,$parentTeacherTopicCode,$topicFlow);				
			if($coteacherTopicFlag)
			{
				$assessmentFlag = checkForAssessment($schoolCode,$class,$clusterString,$clusters);					
			
				if($assessmentFlag)
				{
					$assessmentDetails = getAssessmentDetails($ttCode,$userDetails); 
					$assessmentCompleted["total_students"] = $assessmentDetails["totalStudents"];
					$assessmentCompleted["students_count"] = $assessmentDetails["completedStudents"];
					$assessmentCompleted["heading"] = "Assessment completed";
					$assessmentCompleted["icon"] = "assessment";
					$assessmentCompleted["progress"] =  round(($assessmentDetails["completedStudents"]/$assessmentDetails["totalStudents"])*100);
					foreach ($assessmentDetails['classAccuracyReport'] as $key => $value) {
						if($key == 0)
						{
							$range = "(0 - 40%)";
							$color = "#F98053";
							$strokeColor = "#F9561F";
							$type = 'Low';			
						}
						else if($key == 1)
						{
							$range = "(40 - 80%)";
							$color = "#FBD542";
							$strokeColor = "#F5B52A";
							$type = 'Average';
						}
						else if($key == 2)
						{
							$range = "(80 - 100%)";
							$color = "#79D84B";
							$strokeColor = "#6EBA50";
							$type = 'High';
						}
						else
						{
							$range = "Incompleted";
							$color = "#a0a0a0";
							$strokeColor = "#989898";
							$type = 'Incompleted';
						}						
						$assessmentDetailArray["accuracySummary"]["student_info"][$key]["range"] = $range;	
						$assessmentDetailArray["accuracySummary"]["student_info"][$key]["color"] = $color; 
		        		$assessmentDetailArray["accuracySummary"]["student_info"][$key]["strokeColor"] = $strokeColor;	
						$assessmentDetailArray["accuracySummary"]["student_info"][$key]["students"] = $value;
						$assessmentDetailArray["accuracySummary"]["student_info"][$key]["type"] = $type;
						if($range != 'Incompleted')
						{
							$total[$key] = count($value);
							$assessmentDetailArray["chartData"][$key]["color"] = $color;
							$assessmentDetailArray["chartData"][$key]["strokeColor"] = $strokeColor;					
							$assessmentDetailArray["chartData"][$key]["data"] = $total[$key];
							$assessmentDetailArray["chartData"][$key]["label"] = $total[$key]." - ".$type;	
						}
					}
					$assessmentDetailArray["avgAccuracy"] = $assessmentDetails["avgAccuracy"];			
				}	
			}				
			$assessmentReportArray['coteacherTopicFlag'] = $coteacherTopicFlag;
			$assessmentReportArray['assessmentFlag'] 	 = $assessmentFlag;
			$assessmentReportArray['assessmentDetails']  = $assessmentDetailArray;
			$assessmentReportArray['assessmentCompleted']  = $assessmentCompleted;
			$assessmentJson = json_encode($assessmentReportArray);
			echo $assessmentJson;
    		break;

    	case "learningUnitSummary" :
    		$learningUnitSummaryArray = array();    		
    		$learningUnitSummary = getLearningUnitSummary($clusterDetail,$userIDstr,$class,$ttCode);
    		$i=0;
    		foreach($learningUnitSummary as $key=>$luSummary)
    		{
    			$learningUnitSummaryArray[$i]["progress"]["value"] = $luSummary["accuracy"];    			
    			$learningUnitSummaryArray[$i]["progress"]["dash"] = $luSummary["dash"];
    			$learningUnitSummaryArray[$i]["progress"]["tooltip"] =  "Accuracy will appear when at least 50% of the class has completed the learning unit.";
    			$learningUnitSummaryArray[$i]["label"] = "Accuracy";
    			$learningUnitSummaryArray[$i]["tick"] =  $luSummary["tick"];
    			$learningUnitSummaryArray[$i]["tooltip"] = "On successful completion of Learning Unit by at least 75% of the class.";
    			$learningUnitSummaryArray[$i]["heading"] = $luSummary["cluster"];
    			$i++;
    		}
    		$learningUnitSummaryJson = json_encode($learningUnitSummaryArray);
    		echo  $learningUnitSummaryJson;
    		break;

    	case "whatsGoingOn" :
    		$positiveArray= $positiveOldArray = array();
			$negativeArray= $negativeOldArray = array();
			$positiveLimit = $negativeLimit = 0;
			$negativeLimitValue = $positiveLimitValue = 5;
			$dailyPractiseArray = getDailyPractiseDetails($clusterString);
			//find most recent ttattemptIds of users who have completed topic
			$query= "SELECT MAX(a.ttAttemptID) as ttAttemptID,a.userID from adepts_teacherTopicStatus a where a.teacherTopicCode='$ttCode' and a.userID IN ($userIDstr) and (a.classLevelResult IN('SUCCESS','FAILURE') OR a.result IN('SUCCESS','FAILURE')) GROUP BY a.userID";
			$result =  mysql_query($query);
			while($line = mysql_fetch_array($result))
			{
				$ttAttemptID[] = $line[0];		
			}
			if(!empty($ttAttemptID))
			{
				$ttAttemptIDStr = implode(',', $ttAttemptID);			
				$negativeOldArray = getFailedClusterDetails($ttAttemptIDStr,$userDetails,$clusterString,$clusterDetail,$negativeLimitValue);	
				if(!empty($negativeOldArray))
				{
					foreach($negativeOldArray as $value)
					{
						array_push($negativeArray,$value);						
					}
				}
				$negativeLimit = $negativeLimitValue - count($negativeArray) ;
				if($negativeLimit !=0)
				{
					$negativeOldArray = getLessAccuracyCluster($ttAttemptIDStr,$userDetails,$clusterString,$clusterDetail,$negativeLimit);
					if(!empty($negativeOldArray))
					{
						foreach($negativeOldArray as $value)
						{
							array_push($negativeArray,$value);						
						}
					}
					$negativeLimit = $negativeLimitValue - count($negativeArray) ;
					if($negativeLimit !=0)
					{			
						$negativeOldArray = getLessAccuracyDailyPractice($clusterString,$userIDstr,$userDetails,$dailyPractiseArray,$negativeLimit);
						if(!empty($negativeOldArray))
						{
							foreach($negativeOldArray as $value)
							{
								array_push($negativeArray,$value);						
							}	
						}
					}
				}				
				$positiveOldArray = getPassedClusterDetails($ttAttemptIDStr,$userDetails,$clusterString,$clusterDetail,$positiveLimitValue);
				if(!empty($positiveOldArray))
				{
					foreach($positiveOldArray as $value)
					{											
						array_push($positiveArray,$value);
					}
				}				
				$positiveLimit = $positiveLimitValue - count($positiveArray) ;			
				if($positiveLimit !=0)
				{
					$positiveOldArray = getDiagnostictTestDetails($clusterString,$ttCode,$userIDstr);
					if(!empty($positiveOldArray))
					{
						foreach($positiveOldArray as $value)
						{											
							array_push($positiveArray,$value);
						}
					}				
					$positiveLimit = $positiveLimitValue - count($positiveArray);				
					if($positiveLimit !=0)
					{			
						$needMinUsers = count($userIDs)/2;					
						$positiveOldArray = getMoreAccuracyDailyPractice($clusterString,$userIDstr,$dailyPractiseArray,$needMinUsers,$positiveLimit);
						if(!empty($positiveOldArray))
						{
							foreach($positiveOldArray as $value)
							{											
								array_push($positiveArray,$value);
							}
						}					
						$positiveLimit = $positiveLimitValue - count($positiveArray);					
						if($positiveLimit !=0)
						{	
							$positiveOldArray = getFirstStudent($ttCode,$userIDstr,$userDetails);
							if(!empty($positiveOldArray))
							{
								foreach($positiveOldArray as $value)
								{											
									array_push($positiveArray,$value);
								}				
							}
						}
					}
				}
			}
			
			$whatsGoingOnArray['positive'] = $positiveArray;
			$whatsGoingOnArray['need_attention'] = $negativeArray;			
			$whatsGoingOnJson = json_encode($whatsGoingOnArray);
			echo $whatsGoingOnJson;	
    		break;	

    	case "commonWrongAnswer" : 

    		$cwaDetails = getCommonWrongAnswer($clusters,$ttCode,$userIDstr,$class,$section,$userIDs);   				
			$cwaJson = json_encode($cwaDetails);
			echo $cwaJson;
    		break;
    	case "getQuestion":
    		$quesDetails = $_REQUEST["quesDetails"];
			$quesDetailsArr = explode("~",$quesDetails);    	
			echo "<div>".getQuestionData($quesDetailsArr[0], $quesDetailsArr[1], $quesDetailsArr[2], $quesDetailsArr[3], $quesDetailsArr[4], $quesDetailsArr[5],1,$quesDetailsArr[6],0)."<div>";
    		break;
    		
		case "getDiagnosticQuestion": 
			$quesDetails = $_REQUEST["quesDetails"];
			$quesDetailsArr = explode("~",$quesDetails);
			echo "<div>".getDiagnosticQuestionData($quesDetailsArr[0], $quesDetailsArr[1], $quesDetailsArr[2],0)."<div>";
			break;
    	case "questionsForDiscussion" :    		
    		$questionsForDiscussionDetails = getQuestionsForDiscussion($ttCode,$userIDstr);					
			echo json_encode($questionsForDiscussionDetails);
			break;

		case "getAccuracyOfLearningUnits":
			$startDate = $_GET['startDate'];
			$endDate = $_GET['endDate'];						
			$topicClusterArray = getClusterDetailsAttemptedWithinDateRange($userIDstr, $startDate, $endDate, $class);
			$clusterAccuracyArray['learningUnitDetails'] = getAccuracyForClusters($topicClusterArray,$class,$userIDstr);
			$clusterAccuracyArray['heading'] = 'Learning Units attempted ('.date("d-m-Y", strtotime($startDate)).' to '.date("d-m-Y", strtotime($endDate)).')';						
			$ctJson = json_encode($clusterAccuracyArray);
			echo $ctJson;
			break;

		case "inCompleteAssessment":
			$assessmentFlag = 0;
			$message = '';
			$assessmentFlag = checkForAssessment($schoolCode,$class,$clusterString,$clusters);				
			if($assessmentFlag)
			{				
				$assessmentDetails = getIncompleteAssessmentDetails($ttCode,$userDetails,$userIDstr);
				if(!empty($assessmentDetails))
				{
					foreach($assessmentDetails as $classSec => $count)
					{
						$postfix = $count>1 ? "s" : '';
						$message .= $count." student".$postfix." of ".$classSec.", ";
					}
					$message = rtrim($message,', ');
				}
			}
			echo $message;
			break;
	}
?>



