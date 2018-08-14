<?php

	
	set_time_limit(0);
	include("header.php");
	include("../slave_connectivity.php");

	include("../userInterface/functions/functions.php");
	include("../userInterface/functions/orig2htm.php");
	include("../userInterface/classes/clsQuestion.php");
	include("../userInterface/classes/clsTeacherTopic.php");
	include("../userInterface/classes/clsDiagnosticTestQuestion.php");
	include("../userInterface/constants.php");
	include("../userInterface/functions/functionsForDynamicQues.php");
	
	error_reporting(E_ERROR);
	if(!isset($_SESSION['userID']) || $_SESSION['userID']=="")
	{
		echo "You are not authorised to access this page!";
		exit;
	}

	$animationQues	=	0;
	$totalQues	=	0;
	$userID      = $_SESSION['userID'];
	$school_code = $_SESSION['schoolCode'];
	$category    = $_SESSION['admin'];

	$class = $cls = $childClass = isset($_REQUEST['cls'])?$_REQUEST['cls']:"";
	$topic = $ttCode  = isset($_REQUEST['ttCode'])?$_REQUEST['ttCode']:"";
	$childSection = isset($_REQUEST['childSection'])?$_REQUEST['childSection']:"";
	$cwaType = isset($_REQUEST['cwaType'])?$_REQUEST['cwaType']:"2";
	$cwaQuesDetails = $cwaQuesDetails = $cwaDTDetails = array();

	if (isset($ttCode) && $ttCode!="")
	{
		if(strcasecmp($category,"School Admin")==0 || strcasecmp($category,"Teacher")==0)
			$subcategory = "School";
		else if(strcasecmp($category,"Home Center Admin")==0)
			$subcategory = "Home Center";
		/* Fetch all the attempted user */
		$userIdArray = array();
		$attemptedUserQuery = "SELECT userID FROM adepts_userDetails
							   WHERE  schoolcode = $school_code AND category='STUDENT' AND subcategory='$subcategory' AND endDate>=curdate() AND enabled=1 AND subjects like '%".SUBJECTNO."%' AND childClass = $class";
		if (isset($childSection) && $childSection!="")
		{
			if(strpos($childSection, ",") < 0)
				$attemptedUserQuery .= " AND childSection ='$childSection'";
		}		
		$userIdResult = mysql_query($attemptedUserQuery) or die(mysql_error());
		while($userIdRow = mysql_fetch_array($userIdResult))
		{
			array_push($userIdArray, $userIdRow[0]);
		}

		$userIdStr = implode(",", $userIdArray);	
		$minUsers = ROUND(count($userIdArray)*25/100);			
		if($cwaType==1)
		{
			$k=0;
			$clusterArray = getClustersOfTopic($topic);
			$noofsdls = 0;

			/* Fetched all the SDLs of the clusters */
			foreach ($clusterArray as $val)
			{
				$clusterCode[$k] = $val;

				$query = "SELECT a.clusterCode, subdifficultylevel, ROUND((SUM(R)*100)/count(srno)) as accuracy ,group_concat(distinct q.qcode) as qcodes,GROUP_CONCAT(flag) as flag,SUM(R) as correct,count(srno) as total FROM  ".TBL_QUES_ATTEMPT."_class$class a, adepts_questions q, ".TBL_CLUSTER_STATUS." cs, ".TBL_TOPIC_STATUS." ts
						  WHERE a.clusterAttemptID = cs.clusterAttemptID AND
								cs.userID = ts.userID AND
								ts.userID IN ($userIdStr) AND
								a.qcode = q.qcode AND
								cs.ttAttemptID = ts.ttAttemptID AND
								cs.clusterCode='$clusterCode[$k]' AND
								ts.teacherTopicCode = '$topic'
						 GROUP BY subdifficultylevel,a.userID order by accuracy ASC";			
						 // echo $query;
				$sdl_result = mysql_query($query) or die("Error in SDL query - ".mysql_error());				
				while($sdl_row = mysql_fetch_array($sdl_result))
				{					
						if($sdl_row[2] < 50)
						{
							$commonWrongAns[$noofsdls]['sdl'] = $sdl_row[1];
							$commonWrongAns[$noofsdls]['clusterCode'] = $sdl_row[0];
							$commonWrongAns[$sdl_row[0]][$sdl_row[1]]['noOfStudents'] += 1; 
							$commonWrongAns[$sdl_row[0]][$sdl_row[1]]['qcodes'][] = explode(',',$sdl_row[3]);
							$commonWrongAns[$sdl_row[0]][$sdl_row[1]]['flag'][] = $sdl_row[4];
							$commonWrongAnsAccuracy[$noofsdls] = $sdl_row[2];
						}
						$commonWrongAns[$sdl_row[0]][$sdl_row[1]]['correct']+=$sdl_row[5];
						$commonWrongAns[$sdl_row[0]][$sdl_row[1]]['totalAttempts']+=$sdl_row[6];										
						$noofsdls++;													
				}
			}		
			// echo "<pre>";
			// print_r($commonWrongAnsAccuracy);
			$wrongClusters = array();
			foreach($commonWrongAnsAccuracy as $key => $SDLAccuracy)
			{				
				$clusterCode = $commonWrongAns[$key]['clusterCode'];
				$sdl = $commonWrongAns[$key]['sdl'];				
				$wrongClusters[$clusterCode][$sdl]['noOfStudents']=$commonWrongAns[$clusterCode][$sdl]['noOfStudents'];
				// remove blank values into array for flag field 
				$wrongClusters[$clusterCode][$sdl]['flag']=implode(',',array_filter($commonWrongAns[$clusterCode][$sdl]['flag']));
				// convert two dimensional array to one dimensional array and pic unique qcodes
				$wrongClusters[$clusterCode][$sdl]['qcodes']=implode(',',array_unique(array_reduce($commonWrongAns[$clusterCode][$sdl]['qcodes'], 'array_merge', array())));
				$wrongClusters[$clusterCode][$sdl]['overallAccuracy'] = $commonWrongAns[$clusterCode][$sdl]['correct']*100/$commonWrongAns[$clusterCode][$sdl]['totalAttempts'];
			}
			// echo "<pre>";
			// print_r($wrongClusters);
					
			$finalSDLClusterArray = array();     // Contain Cat1 and Cat2 clusters
			$finalSDLArray = array();            // Contain Cat1 and Cat2 SDLs
			$finalSDLQuesArray = array();		 // Contain the questions of that SDL							
			$finalSDLPerClassArray = array();				
			$finalSDLPerSchoolArray = array();
			$finalSDLPerNationalArray = array();				
			$harderSDLnum = 0;
			$harderSDLCat2num = 0;
			foreach($wrongClusters as $cluster=>$sdls)
			{	
				foreach($sdls as $sdl=>$sdlsValue)
				{
					if($sdlsValue['flag'] !='' && $sdlsValue['noOfStudents'] > $minUsers && $harderSDLnum < 10)
					{
						array_push($finalSDLClusterArray, $cluster);
						array_push($finalSDLArray,$sdl);
						array_push($finalSDLQuesArray,$sdlsValue['qcodes']);	
						array_push($finalSDLPerClassArray, $sdlsValue['overallAccuracy']);
						array_push($finalSDLPerSchoolArray, getSchoolAvg($schoolCode,$cluster,$sdl,$class));
						array_push($finalSDLPerNationalArray, getNationalAvg($cluster,$sdl,$class));						
						$harderSDLnum++;
					}
				}
				if($harderSDLnum < 10)
				{	
					if($sdlsValue['flag'] =='' && $sdlsValue['noOfStudents'] > $minUsers && $harderSDLCat2num < (10-$harderSDLnum))
					{
						array_push($finalSDLClusterArray, $cluster);
						array_push($finalSDLArray,$sdl);
						array_push($finalSDLQuesArray,$sdlsValue['qcodes']);	
						array_push($finalSDLPerClassArray, $sdlsValue['overallAccuracy']);
						array_push($finalSDLPerSchoolArray, getSchoolAvg($schoolCode,$cluster,$sdl,$class));
						array_push($finalSDLPerNationalArray, getNationalAvg($cluster,$sdl,$class));						
						$harderSDLCat2num++;
					}	
				}	
			}
			// echo "<pre>";
			// print_r($finalSDLClusterArray);
			// print_r($finalSDLArray);
			// print_r($finalSDLQuesArray);
			// print_r($finalSDLPerClassArray);
			// print_r($finalSDLPerSchoolArray);
			// print_r($finalSDLPerNationalArrayexit);			
						
			if($harderSDLnum+$harderSDLCat2num <10)
			{
				$harderDTnum=0;
				$halfStudents = ROUND(count($userIdArray)*25/100);	
				//fetching ttattemptIds of teacher topic code				
				$query = "SELECT GROUP_CONCAT(a.ttAttemptID) from adepts_teacherTopicStatus a where a.teacherTopicCode='$ttCode' and a.userID IN($userIdStr)";			
				$result = mysql_query($query);
				if($line=mysql_fetch_array($result))
				{					
					// fetching all qcodes of ttattempt id order by accuracy
					$dtQuery = "SELECT a.qcode,ROUND(SUM(a.R)*100/count(a.srno)) as accuracy,count(DISTINCT a.userID) as noOfUsers from adepts_diagnosticQuestionAttempt a JOIN adepts_diagnosticTestMaster b ON a.diagnosticTestID=b.diagnosticTestID JOIN adepts_diagnosticTestAttempts c ON a.attemptID=c.srno where b.testType='Prerequisite' and c.ttAttemptID IN($line[0]) group by a.qcode order by accuracy ASC";						
					$dtResult = mysql_query($dtQuery);						
					while($dtLine = mysql_fetch_array($dtResult))
					{														
						if($dtLine[1]<=40 && $dtLine[2]>$halfStudents && $harderDTnum < (10-($harderSDLnum+$harderSDLCat2num)))
						{		
							// fetching all users who has given wrong answer for question						
							$stdQuery="SELECT GROUP_CONCAT(DISTINCT d.childName order by d.childName ASC) from adepts_diagnosticQuestionAttempt a JOIN adepts_diagnosticTestAttempts c ON a.attemptID=c.srno JOIN adepts_userDetails d ON d.userID=a.userID where a.qcode=$dtLine[0] and R=0 and c.ttAttemptID IN($line[0]) order by d.childName ASC";								
							$stdResult = mysql_query($stdQuery);
							$stdLine = mysql_fetch_row($stdResult);

							$cwaDTDetails[$harderDTnum]["qcodeListData"] = $dtLine[0]."~".$userIdStr;
							$cwaDTDetails[$harderDTnum]["nationalAVG"] = number_format(getSchoolNationalAvgDT('',$dtLine[0],$class));
							$cwaDTDetails[$harderDTnum]["schoolAVG"] = number_format(getSchoolNationalAvgDT($school_code,$dtLine[0],$class));
							$cwaDTDetails[$harderDTnum]["classWisePerformance"] = number_format($dtLine[1]);
							$cwaDTDetails[$harderDTnum]["failedStudentList"] = $stdLine[0];
							$harderDTnum++;
						}																		
					}											
				}
			}															
				$countOfQueNo =  count($finalSDLClusterArray);
								
				/* Show all the Sdls of Cat1 and Cat2 */
				$qcodeStrForDownload = "";
				$jCnt = 1;
				foreach ($finalSDLClusterArray as $i => $value)
				{

					$currentTempCluster = $finalSDLClusterArray[$i];
					$currentTempSDL = $finalSDLArray[$i];
					$currentTempSDLQues = $finalSDLQuesArray[$i];
					$SDLQuesArray = array();
					$SDLQuesArray = explode(',',$currentTempSDLQues);
					if($currentTempSDL=="")		//For practice cluster, sdl will be blank - currently ignore such questions
						continue;
					$SDLsrno = 1;							
					$clusterAtttempt_query = "SELECT clusterAttemptID FROM ".TBL_TOPIC_STATUS." a, ".TBL_CLUSTER_STATUS." b WHERE a.ttAttemptID=b.ttAttemptID AND a.userID in ($userIdStr) AND teacherTopicCode='$topic' AND clusterCode='$currentTempCluster'";
					$clusterAttempt_result = mysql_query($clusterAtttempt_query);
					$clusterAttemptStr  = "";
					while ($clusterAttempt_line = mysql_fetch_array($clusterAttempt_result))
					   $clusterAttemptStr .= $clusterAttempt_line[0].",";
					$clusterAttemptStr = substr($clusterAttemptStr,0,-1);

					$student_name_string = "";
					$neverRightStudent = 0;
					if($clusterAttemptStr!="")
					{
						$student_name_query = "SELECT u.userID, childName, childClass, childSection, sum(R), count(srno) as cnt
											   FROM   adepts_userDetails u, ".TBL_QUES_ATTEMPT."_class$class a, adepts_questions q
											   WHERE  u.userID IN ($userIdStr) AND
													  a.userID= u.userID       AND
													  a.clusterAttemptID in ($clusterAttemptStr) AND
													  q.clusterCode='$currentTempCluster' AND
													  q.subdifficultylevel=$currentTempSDL AND
													  a.clusterCode = q.clusterCode AND
													  q.qcode = a.qcode";
						if (isset($childSection) && $childSection!="")
						{
							$student_name_query .= " AND childSection = '$childSection'";
						}
						$student_name_query .= " GROUP BY u.userID";						
						$student_name_result = mysql_query($student_name_query) or die("Error in student name query - ".mysql_error());
						$total_count = mysql_num_rows($student_name_result);

						while($student_name_data=mysql_fetch_array($student_name_result))
						{
							if ($student_name_data['sum(R)']==0 && $student_name_data['cnt'] >= 3)  //Show only the list of students who have not got it correct even once
							{								
								$student_name_string .= $student_name_data['childName'].", ";
								$neverRightStudent++;
							}
						}
						$student_name_string = substr($student_name_string, 0, -2);
					}					
					$leastPerformancequery = "SELECT sum(R)/COUNT(srno) as accr , qcode FROM ".TBL_QUES_ATTEMPT."_class$class 
												WHERE userID IN ($userIdStr) AND qcode in ($currentTempSDLQues) group by qcode order by accr";												
					$Qcode_result = mysql_query($leastPerformancequery);
					$qcodeList = mysql_fetch_row($Qcode_result);
					
					$leastPerformedQcode = $qcodeList[1];
					$cwaQuesDetails[$jCnt]["qcodeListData"] = $leastPerformedQcode."~".$school_code."~".$class."~".$childSection."~".$SDLsrno."~".$userIdStr."~".$cwaType;					
					$SDLsrno = $SDLsrno + 1;
					
					$cwaQuesDetails[$jCnt]["nationalAVG"] = number_format($finalSDLPerNationalArray[$i]);
					$cwaQuesDetails[$jCnt]["schoolAVG"] = number_format($finalSDLPerSchoolArray[$i]);
					$cwaQuesDetails[$jCnt]["classWisePerformance"] = number_format($finalSDLPerClassArray[$i]);
					$cwaQuesDetails[$jCnt]["failedStudentList"] = $student_name_string;
					$jCnt++;
				}				
			$cwaDetails = array_merge($cwaQuesDetails,$cwaDTDetails);
				echo "<pre>";
				print_r($cwaDetails);
				echo "<pre>";					
		}
		else if($cwaType==2)
		{
			$query = "SELECT GROUP_CONCAT(a.ttAttemptID) from adepts_teacherTopicStatus a where a.teacherTopicCode='$ttCode' and a.userID IN($userIdStr)";			
			$result = mysql_query($query);
			if($line=mysql_fetch_array($result))
			{
				$sq = "SELECT c.qcode,ROUND(SUM(c.R)/count(c.srno)*100) as accuracy from adepts_comprehensiveModuleAttempt a JOIN adepts_diagnosticTestAttempts b ON a.srno=b.srno JOIN adepts_diagnosticQuestionAttempt c ON c.attemptID=a.srno JOIN adepts_diagnosticTestMaster d ON d.diagnosticTestID=b.diagnosticTestID where a.ttAttemptID IN($line[0]) and d.testType ='Assessment' and a.`status`=1 GROUP BY c.qcode";					
				$rs = mysql_query($sq);
				while($ln = mysql_fetch_array($rs))
				{													
					$assessmentQArray[$ln[0]]= $ln[1];	
				}
				$arrayAccuracyDetails = assessmentQuesAccuracySummary($assessmentQArray);
				foreach ($arrayAccuracyDetails as $accuracyKey => $range) {
					$index =0;
					$cwaDetails[$accuracyKey] = array();
					foreach ($range as $key => $value) {
						$cwaDetails[$accuracyKey][$index]['qcodeListData']=$key."~".$userIdStr;
						$index++;
					}
				}
				echo "<pre>";
				print_r($cwaDetails);
				echo "<pre>";
			}
		}
	}
	else if($_REQUEST["mode"]=="getQuestion")
	{
		$quesDetails = $_REQUEST["quesDetails"];
		$quesDetailsArr = explode("~",$quesDetails);
		echo "<div>".getQuestionData($quesDetailsArr[0], $quesDetailsArr[1], $quesDetailsArr[2], $quesDetailsArr[3], $quesDetailsArr[4], $quesDetailsArr[5], $quesDetailsArr[6])."<div>";
	}
	else if($_REQUEST["mode"]=="getDiagnosticQuestion")
	{
		$quesDetails = $_REQUEST["quesDetails"];
		$quesDetailsArr = explode("~",$quesDetails);
		echo "<div>".getDiagnosticQuestionData($quesDetailsArr[0], $quesDetailsArr[1])."<div>";
	}
	

function getQuestionData($qcode, $schoolCode, $class, $section, $qsrn, $userIDStr, $cwaType)
{

	global $animationQues;
	global $totalQues;
    $mostCommonWrongAnswer = $questionStr = "";
    $question     = new Question($qcode);
    $dynamic = 0;

	if($question->isDynamic())
	{
		$dynamic = 1;
		$question->generateQuestion();
	}

    $question_type = $question->quesType;

	if((strpos($question->getQuestion(), ".html") !== false || strpos($question->getQuestion(), ".swf") !== false || strpos($question->getDisplayAnswer(), ".swf") !== false || strpos($question->getDisplayAnswer(), ".swf") !== false) && $qsrn==1)
		$animationQues++;

	if($qsrn==1)
		$totalQues++;
		
    $questionStr .= "<p>";
    $questionStr .= $question->getQuestion()."<br/>";
    $questionStr .= "</p>";
	$correctAns = $question->correctAnswer;
	
	$optionWiseAttempt = array();
	$answerwiseData = array();
	if($question_type=='MCQ-4' || $question_type=='MCQ-2' || $question_type=='MCQ-3')	
	{
		if($cwaType==1)
		{
			$sq = "SELECT count(srno) AS CNT, A as ans FROM adepts_teacherTopicQuesAttempt_class".$class." WHERE qcode=".$qcode." AND userID IN ($userIDStr) group by A";
		}
		else
		{
			$sq = "SELECT count(srno) AS CNT, A as ans FROM adepts_teacherTopicQuesAttempt_class".$class." A, adepts_userDetails B
					 WHERE A.userID=B.userID AND qcode=".$qcode." AND category='STUDENT' group by A";
		}
		$rs = mysql_query($sq);
		while($rw = mysql_fetch_array($rs))
		{
			$optionWiseAttempt[$rw[1]] = $rw[0];
		}
		$totalAttempt = array_sum($optionWiseAttempt);
		arsort($optionWiseAttempt);
		$mcra = 0; //most common wrong answer count
	
		foreach($optionWiseAttempt as $optionVal=>$optionCount)
		{
			if($correctAns==$optionVal)
			{
				$a = "<div class='optionPercentage_".$qcode."' id='".$optionVal.$qcode."_option' style='border: 2px solid;cursor:pointer;width:45px;padding-left:4px;padding-right:4px;text-align: center;font-size:14px'>"." ".round(($optionCount/$totalAttempt)*100,1)." %</div>";
			}
			elseif($mcra==0)
			{
				$mostCommonWrong = $optionVal;
				$a = "<div class='optionPercentage_".$qcode."' id='".$optionVal.$qcode."_option' style='border: 2px solid;cursor:pointer;width:45px;padding-left:4px;padding-right:4px;text-align: center;font-size:14px'>"." ".round(($optionCount/$totalAttempt)*100,1)." %</div>";
				$mcra++;
			}
			else
				$a = "<div class='optionPercentage_".$qcode."' id='".$optionVal.$qcode."_option' style='border: 2px solid;cursor:pointer;width:45px;padding-left:4px;padding-right:4px;text-align: center;font-size:14px'>"." ".round(($optionCount/$totalAttempt)*100,1)." %</div>";
	
			$answerwiseData[$optionVal] = $a;
		}
    
    	$questionStr .= "<table width='98%' border='0' cellpadding='3'>";

	    if($question_type=='MCQ-4' || $question_type=='MCQ-2')	{
	        $questionStr .= "<tr>";
	            $questionStr .= "<td id='td_1".$qcode."' width='5%'";
	            if($mostCommonWrong == 'A')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				if($correctAns== 'A')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
		
	            $questionStr .= "><strong>A</strong>. </td><td align='left' width='43%'>".$question->getOptionA()." ".$answerwiseData['A']."</td>";
	            $questionStr .= "<td id='td_2".$qcode."' width='5%'";
	             if($mostCommonWrong == 'B')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				 if($correctAns== 'B')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
	            $questionStr .= "><strong>B</strong>. </td><td align='left' width='42%'>".$question->getOptionB()." ".$answerwiseData['B']."</td>";
	        $questionStr .= "</tr>";
	    }
	    if($question_type=='MCQ-4')	{
	        $questionStr .= "<tr>";
	            $questionStr .= "<td  id='td_3".$qcode."' width='5%'";
	            if($mostCommonWrong == 'C')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				if($correctAns== 'C')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
	            $questionStr .= "><strong>C</strong>. </td><td align='left' width='43%'>".$question->getOptionC()." ".$answerwiseData['C']."</td>";
	            $questionStr .= "<td id='td_4".$qcode."' width='5%'";
	            if($mostCommonWrong == 'D')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				if($correctAns== 'D')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
	            $questionStr .= "><strong>D</strong>. </td><td align='left' width='42%'>".$question->getOptionD()." ".$answerwiseData['D']."</td>";
	        $questionStr .= "</tr>";
	    }
	    if($question_type=='MCQ-3')	{
	        $questionStr .= "<tr>";
	            $questionStr .= "<td id='td_1".$qcode."' width='5%'";
	            if($mostCommonWrong == 'A')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				if($correctAns== 'A')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
	            $questionStr .= "><strong>A</strong>. </td><td align='left' width='28%'>".$question->getOptionA()." ".$answerwiseData['A']."</td>";
	            $questionStr .= "<td id='td_2".$qcode."' width='5%'";
	            if($mostCommonWrong == 'B')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				if($correctAns== 'B')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
	            $questionStr .= "><strong>B</strong>. </td><td align='left' width='28%'>".$question->getOptionB()." ".$answerwiseData['B']."</td>";
	            $questionStr .= "<td id='td_3".$qcode."' width='5%'";
	            if($mostCommonWrong == 'C')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				if($correctAns== 'C')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
	            $questionStr .= "><strong>C</strong>. </td><td align='left' width='28%'>".$question->getOptionC()." ".$answerwiseData['C']."</td>";
	        $questionStr .= "</tr>";
	    }
	    $questionStr .= "</table>";
    }
	
	if($question_type=='MCQ-4' || $question_type=='MCQ-2' || $question_type=='MCQ-3')	
	{
		$str = 'optionPercentage_'.$qcode;
		$questionStr .= '<br>';
		//$questionStr .= "<div id ='divAnswerPercentage".$str."' style='width: 170px;padding: 2px;cursor:pointer;background-color: #cbcaca;border-radius: 19px;box-shadow:2px 4px 2px #888888;font-size:14px;text-align:center;' onclick=showAnswerPercentage('".$str."',".$qcode.")>+ Option-wise performance</div>";
	}
	
    if($question->hasExplanation())
    {
    	$questionStr .= "<br/><span class='title'>Answer</span>: ";
    	if ($question_type=="Blank")
    		$questionStr .= $question->getCorrectAnswerForDisplay()."<br/>";
    	else
    		$questionStr .= "<br/>";
   		$questionStr .= $question->getDisplayAnswer()."<br/>";
    }
    elseif ($question_type=="Blank")
		$questionStr .= "<br/><span class='title'> Answer</span>: ".$question->getCorrectAnswerForDisplay()."<br/>";
	
	$showMostCommonWrongAns = 1;
	
	if($question_type!='I' && $question_type!='MCQ-4' && $question_type!='MCQ-2' && $question_type!='MCQ-3' && $question_type!='D')
	{
		$questionStr .= "<br><div id='cwa_$qcode' class='cwa'><span class='title'><h3>Most common wrong answer:</h3> </span>Loading...<img src='assets/ajax-loader.gif' height='14' width='15'></div><input type='hidden' value='".$qcode.'#'.$dynamic.'#'.$showMostCommonWrongAns.'#'.$question_type.'#'.$question->correctAnswer.'#'.$class.'#'.$userIDStr.'#'.$cwaType."'>";
	}
    return $questionStr;
}

function getClustersOfTopic($ttCode)
{
    $clusterArray = array();
    $query  = "SELECT customTopic, customCode FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$ttCode'";
    $result = mysql_query($query);
    $line   = mysql_fetch_array($result);

    $customTopic = $line[0];
    $customCode  = $line[1];
    if(!$customTopic)
    {
        $clusterQuery = "SELECT a.clusterCode FROM   adepts_teacherTopicClusterMaster a, adepts_clusterMaster b
    					 WHERE  a.clusterCode=b.clusterCode AND teacherTopicCode='$ttCode' AND b.status='live' ORDER BY a.flowno";
    	$result       = mysql_query($clusterQuery) or die(mysql_error());
    	while ($line = mysql_fetch_array($result)) {
    		array_push($clusterArray,$line[0]);
    	}
    }
    else
    {
        $query = "SELECT clusterCodes FROM adepts_customizedTopicDetails where code=$customCode";
        $result = mysql_query($query);
        $line   = mysql_fetch_array($result);
        $clusterArray = explode(",",$line[0]);
    }
    return $clusterArray;
}

function getSchoolAvg($schoolCode,$clusterCode,$sdl,$class)
{
	$whereClause = "";
	if($schoolCode!="")
	{
		$whereClause =  "AND schoolCode=$schoolCode";
	}

	$sq = "SELECT ROUND((SUM(R)/COUNT(srno))*100,1) FROM adepts_questions A, adepts_teacherTopicQuesAttempt_class$class B, adepts_userDetails C 
			WHERE A.qcode=B.qcode AND B.userID=C.userID $whereClause AND category='STUDENT' AND A.clusterCode='$clusterCode' AND subdifficultylevel='$sdl'";			
	$rs = mysql_query($sq);		 
	$rw = mysql_fetch_array($rs);
	return $rw[0];
}

function getSchoolNationalAvgDT($schoolCode,$qcode,$class)
{
	$whereClause = "";
	if($schoolCode!="")
	{
		$whereClause =  "AND schoolCode=$schoolCode";
	}
	$sq = "SELECT ROUND((SUM(R)/COUNT(srno))*100,1) from adepts_diagnosticQuestionAttempt a JOIN adepts_userDetails b ON a.userID=b.userID where a.qcode=$qcode and b.category='STUDENT' and b.childClass=$class $whereClause";
	$rs = mysql_query($sq);		 
	$rw = mysql_fetch_array($rs);
	return $rw[0];
}

function getNationalAvg($clusterCode,$sdl,$class)
{
	$sq = "SELECT A.qcode,accuracy,B.majorVersion FROM adepts_questions A, adepts_questionPerformance B 
			WHERE A.qcode=B.qcode AND A.clusterCode='$clusterCode' AND subdifficultylevel='$sdl' AND class=$class order by B.majorVersion DESC";			
	$rs = mysql_query($sq);
	$accuracyArray = array();
	while($rw = mysql_fetch_array($rs))
	{
		if(!in_array($rw['qcode'], array_keys($accuracyArray)))
		{
			$accuracyArray[$rw['qcode']] = $rw['accuracy'];
		}
	}
	return round(array_sum($accuracyArray)/count($accuracyArray),1);
}

function getDefaultFlowForTheSchool($schoolCode){

	$defaultFlow = 'MS';

	$flow_query  = "SELECT settingValue FROM userInterfaceSettings WHERE schoolCode='$schoolCode' and settingName='curriculum' limit 1";

	$flow_result = mysql_query($flow_query);

	if($flow_line=mysql_fetch_assoc($flow_result))
	{
		$defaultFlow = $flow_line['settingValue'];
	}
	
	return $defaultFlow;

}


function assessmentQuesAccuracySummary($assessmentDetails)
{
	$arrayAccuracyDetails["array0to40"] = $arrayAccuracyDetails["array40to80"] = $arrayAccuracyDetails["arrayGt80"]= array();	
	foreach($assessmentDetails as $userID=>$otherDetails)
	{
		if($otherDetails<40)
			$arrayAccuracyDetails["array0to40"][$userID] = $otherDetails;
		else if($otherDetails>=40 && $otherDetails<80)
			$arrayAccuracyDetails["array40to80"][$userID] = $otherDetails;
		else
			$arrayAccuracyDetails["arrayGt80"][$userID] = $otherDetails;	
	}
	return $arrayAccuracyDetails;
}

function getDiagnosticQuestionData($qcode,$userIDStr)
{

	global $animationQues;
	global $totalQues;
    $mostCommonWrongAnswer = $questionStr = "";    
    $question = new diagnosticTestQuestion($qcode);
    $dynamic = 0;

	if($question->isDynamic())
	{
		$dynamic = 1;
		$question->generateQuestion();
	}

    $question_type = $question->quesType;

	if((strpos($question->getQuestion(), ".html") !== false || strpos($question->getQuestion(), ".swf") !== false || strpos($question->getDisplayAnswer(), ".swf") !== false || strpos($question->getDisplayAnswer(), ".swf") !== false) && $qsrn==1)
		$animationQues++;

	if($qsrn==1)
		$totalQues++;
		
    $questionStr .= "<p>";
    $questionStr .= $question->getQuestion()."<br/>";
    $questionStr .= "</p>";
	$correctAns = $question->correctAnswer;
	
	$optionWiseAttempt = array();
	$answerwiseData = array();
	if($question_type=='MCQ-4' || $question_type=='MCQ-2' || $question_type=='MCQ-3')	
	{		
		$sq = "SELECT count(srno) AS CNT, A as ans FROM adepts_diagnosticQuestionAttempt WHERE qcode=".$qcode." AND userID IN ($userIDStr) group by A";
			
		$rs = mysql_query($sq);
		while($rw = mysql_fetch_array($rs))
		{
			$optionWiseAttempt[$rw[1]] = $rw[0];
		}
		$totalAttempt = array_sum($optionWiseAttempt);
		arsort($optionWiseAttempt);
		$mcra = 0; //most common wrong answer count
	
		foreach($optionWiseAttempt as $optionVal=>$optionCount)
		{
			if($correctAns==$optionVal)
			{
				$a = "<div class='optionPercentage_".$qcode."' id='".$optionVal.$qcode."_option' style='border: 2px solid;cursor:pointer;width:45px;padding-left:4px;padding-right:4px;text-align: center;font-size:14px'>"." ".round(($optionCount/$totalAttempt)*100,1)." %</div>";
			}
			elseif($mcra==0)
			{
				$mostCommonWrong = $optionVal;
				$a = "<div class='optionPercentage_".$qcode."' id='".$optionVal.$qcode."_option' style='border: 2px solid;cursor:pointer;width:45px;padding-left:4px;padding-right:4px;text-align: center;font-size:14px'>"." ".round(($optionCount/$totalAttempt)*100,1)." %</div>";
				$mcra++;
			}
			else
				$a = "<div class='optionPercentage_".$qcode."' id='".$optionVal.$qcode."_option' style='border: 2px solid;cursor:pointer;width:45px;padding-left:4px;padding-right:4px;text-align: center;font-size:14px'>"." ".round(($optionCount/$totalAttempt)*100,1)." %</div>";
	
			$answerwiseData[$optionVal] = $a;
		}
    
    	$questionStr .= "<table width='98%' border='0' cellpadding='3'>";

	    if($question_type=='MCQ-4' || $question_type=='MCQ-2')	{
	        $questionStr .= "<tr>";
	            $questionStr .= "<td id='td_1".$qcode."' width='5%'";
	            if($mostCommonWrong == 'A')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				if($correctAns== 'A')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
		
	            $questionStr .= "><strong>A</strong>. </td><td align='left' width='43%'>".$question->getOptionA()." ".$answerwiseData['A']."</td>";
	            $questionStr .= "<td id='td_2".$qcode."' width='5%'";
	             if($mostCommonWrong == 'B')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				 if($correctAns== 'B')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
	            $questionStr .= "><strong>B</strong>. </td><td align='left' width='42%'>".$question->getOptionB()." ".$answerwiseData['B']."</td>";
	        $questionStr .= "</tr>";
	    }
	    if($question_type=='MCQ-4')	{
	        $questionStr .= "<tr>";
	            $questionStr .= "<td  id='td_3".$qcode."' width='5%'";
	            if($mostCommonWrong == 'C')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				if($correctAns== 'C')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
	            $questionStr .= "><strong>C</strong>. </td><td align='left' width='43%'>".$question->getOptionC()." ".$answerwiseData['C']."</td>";
	            $questionStr .= "<td id='td_4".$qcode."' width='5%'";
	            if($mostCommonWrong == 'D')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				if($correctAns== 'D')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
	            $questionStr .= "><strong>D</strong>. </td><td align='left' width='42%'>".$question->getOptionD()." ".$answerwiseData['D']."</td>";
	        $questionStr .= "</tr>";
	    }
	    if($question_type=='MCQ-3')	{
	        $questionStr .= "<tr>";
	            $questionStr .= "<td id='td_1".$qcode."' width='5%'";
	            if($mostCommonWrong == 'A')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				if($correctAns== 'A')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
	            $questionStr .= "><strong>A</strong>. </td><td align='left' width='28%'>".$question->getOptionA()." ".$answerwiseData['A']."</td>";
	            $questionStr .= "<td id='td_2".$qcode."' width='5%'";
	            if($mostCommonWrong == 'B')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				if($correctAns== 'B')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
	            $questionStr .= "><strong>B</strong>. </td><td align='left' width='28%'>".$question->getOptionB()." ".$answerwiseData['B']."</td>";
	            $questionStr .= "<td id='td_3".$qcode."' width='5%'";
	            if($mostCommonWrong == 'C')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				if($correctAns== 'C')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
	            $questionStr .= "><strong>C</strong>. </td><td align='left' width='28%'>".$question->getOptionC()." ".$answerwiseData['C']."</td>";
	        $questionStr .= "</tr>";
	    }
	    $questionStr .= "</table>";
    }
	
	if($question_type=='MCQ-4' || $question_type=='MCQ-2' || $question_type=='MCQ-3')	
	{
		$str = 'optionPercentage_'.$qcode;
		$questionStr .= '<br>';
		//$questionStr .= "<div id ='divAnswerPercentage".$str."' style='width: 170px;padding: 2px;cursor:pointer;background-color: #cbcaca;border-radius: 19px;box-shadow:2px 4px 2px #888888;font-size:14px;text-align:center;' onclick=showAnswerPercentage('".$str."',".$qcode.")>+ Option-wise performance</div>";
	}
	
    if($question->hasExplanation())
    {
    	$questionStr .= "<br/><span class='title'>Answer</span>: ";
    	if ($question_type=="Blank")
    		$questionStr .= $question->getCorrectAnswerForDisplay()."<br/>";
    	else
    		$questionStr .= "<br/>";
   		$questionStr .= $question->getDisplayAnswer()."<br/>";
    }
    elseif ($question_type=="Blank")
		$questionStr .= "<br/><span class='title'> Answer</span>: ".$question->getCorrectAnswerForDisplay()."<br/>";
	
	$showMostCommonWrongAns = 1;
	
	if($question_type!='I' && $question_type!='MCQ-4' && $question_type!='MCQ-2' && $question_type!='MCQ-3' && $question_type!='D')
	{
		$questionStr .= "<br><div id='cwa_$qcode' class='cwa'><span class='title'><h3>Most common wrong answer:</h3> </span>Loading...<img src='assets/ajax-loader.gif' height='14' width='15'></div><input type='hidden' value='".$qcode.'#'.$dynamic.'#'.$showMostCommonWrongAns.'#'.$question_type.'#'.$question->correctAnswer.'#'.$class.'#'.$userIDStr.'#'.$cwaType."'>";
	}
    return $questionStr;
}
?>