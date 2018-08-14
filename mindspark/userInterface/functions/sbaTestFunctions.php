<?php

function getSbaTestID($userID,$schoolCode,$childClass,$childSection,$testMode="")
{
	$sbaDetailArray	=	array();
	$sq	=	"SELECT sbaTestID,qcodes,totalQues,maxTime FROM adepts_sbaBluePrint
			 WHERE schoolCode=$schoolCode AND childClass=$childClass AND childSection='$childSection' AND testType='post'";
	if($testMode=="")
		$sq	.=	" AND status='Pending'";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	if(mysql_num_rows($rs)==0)
	{
		return "";
	}
	else
	{
		$sbaDetailArray["sbaID"]		=	$rw[0];
		$sbaDetailArray["qcodes"]		=	$rw[1];
		$sbaDetailArray["totalQues"]	=	$rw[2];
		$sbaDetailArray["maxTime"]		=	$rw[3];
		return $sbaDetailArray;
	}
}

function checkForPendingTest($userID,$sbaID)
{
	$sq	=	"SELECT status FROM adepts_userwiseSbaStatus where sbaID=$sbaID AND userID=$userID";
	$rs	=	mysql_query($sq);
	if(mysql_num_rows($rs)==0)
	{
		return "notStarted";
	}
}

function insertSbaDetails($userID,$sbaTestID,$qcodeStr,$totalQues,$maxTime,$sessionID)
{
	$sq	=	"INSERT INTO adepts_userwiseSbaStatus SET userID=$userID, sbaTestID=$sbaTestID, startTime=NOW(), totalQues=$totalQues, maxTime=$maxTime";
	$rs	=	mysql_query($sq);
	$qcodeStrArr	=	explode(",",$qcodeStr);
	shuffle($qcodeStrArr);
	$i=0;
	foreach($qcodeStrArr as $qcode)
	{
		$i++;
		$sqQues	=	"INSERT INTO adepts_userwiseSbaQuesAttempt SET userID=$userID, sbaTestID=$sbaTestID, qno=$i, qcode=$qcode, sessionID=$sessionID";
		$rsQues	=	mysql_query($sqQues);
	}
}

function getSbaQcode($userID,$sbaTestID)
{
	$sq	=	"SELECT qno,qcode,timeTaken FROM adepts_userwiseSbaQuesAttempt A , adepts_userwiseSbaStatus B
			 WHERE A.userID=$userID AND A.sbaTestID=$sbaTestID AND A.userID=B.userID AND A.sbaTestID=B.sbaTestID AND A.qno=B.currentQno";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	$_SESSION["qno"]	=	$rw[0];
	$_SESSION["qcode"]	=	$rw[1];
	return $rw[2];
}

function checkSbaAllowed($schoolCode,$childClass,$childSection,$userID)
{
	$sq	=	"SELECT A.sbaTestID,A.status FROM adepts_userwiseSbaStatus A, adepts_sbaBluePrint B WHERE A.sbaTestID=B.sbaTestID AND userID=$userID AND testType='post'";
	$rs	=	mysql_query($sq);
	if(mysql_num_rows($rs)>0)
	{
		$rw	=	mysql_fetch_array($rs);
		if($rw[1]=='Pending')
		{
			return "sbaTestStarted";
		}
		else
			return "";
	}
	else
	{
		$sq	=	"SELECT sbaTestID FROM adepts_sbaBluePrint
				 WHERE schoolCode=$schoolCode AND childClass=$childClass AND childSection='$childSection' AND status='Pending' AND testType='post'";
		$rs	=	mysql_query($sq);
		if(mysql_num_rows($rs)>0)
		{
			$rw	=	mysql_fetch_array($rs);
			$_SESSION['sbaTestID'] = $rw[0];
			return "sbaTest";
		}
		else
		{
			return "";
		}
	}
}

function getDaysLeftInSbaTest($schoolCode,$childClass,$childSection,$userID)
{
	$sq	=	"SELECT testDate,sbaTestID FROM adepts_sbaBluePrint
			 WHERE schoolCode=$schoolCode AND childClass=$childClass AND childSection='$childSection' AND status='Not Started'";
	$rs	=	mysql_query($sq);
	if(mysql_num_rows($rs)==0)
	{
		return -100;
	}
	else
	{
		$rw	=	mysql_fetch_array($rs);
		$testDate	=	$rw[0];
		$_SESSION['sbaTestID'] = $rw[1];
		$curDate	=	date("Y-m-d");
		$timeLeft	=	floor((strtotime($testDate) - strtotime($curDate))/3600/24);
		if($timeLeft<=0)
		{
			$sq	=	"UPDATE adepts_sbaBluePrint SET status='Pending'
					 WHERE schoolCode=$schoolCode AND childClass=$childClass AND childSection='$childSection' AND status='Not Started'";
			$rs	=	mysql_query($sq);
		}
		$testDate	=	date('F j, Y', strtotime('-1 day', strtotime($testDate)));
		//$testDate	=	date("F j, Y",$testDate);
		return $timeLeft."~".$testDate;
	}
}

function nextSbaQuestion($userID,$sessionID,$subjectNo,$qcode,$response, $quesno, $secs, $responseResult,$dynamic,$dynamicParams,$eeresponse,$totalQuestion,$totalTime,$timeLeft)
{
	$childClass  = $_SESSION['childClass'];
	$sbaTestID	=	$_SESSION['sbaTestID'];
	if(trim($response)!="")
	{
		$sq	=	"UPDATE adepts_userwiseSbaQuesAttempt SET A='$response',R=$responseResult,sessionID=$sessionID
				 WHERE userID=$userID AND sbaTestID=$sbaTestID AND qno=$quesno AND qcode=$qcode";
		$rs	=	mysql_query($sq);
		$quesAttempt_srno = mysql_insert_id();
		if($eeresponse != "NO_EE") // When question is having equation editor.
		{
			$query = "INSERT INTO adepts_equationEditorResponse (srno, childClass, userID, sessionID, qcode, question_type, eeResponse) VALUES(".$quesAttempt_srno.", ".$childClass.",".$userID.",".$sessionID.",'".$qcode."','sba', '".$eeresponse."')";
			mysql_query($query) or die($query);
		}
		if($dynamic)
		{
			$query	=	"INSERT INTO adepts_dynamicParameters (userID, qcode, quesAttempt_srno, parameters, mode, class, lastModified) VALUES
						 ($userID, $qcode, ".$quesAttempt_srno.", '$dynamicParams', 'sba','".$childClass."', '".date("Y-m-d H:i:s")."')";
			mysql_query($query);
		}
	}
	$timeTaken	=	($totalTime*60) - $timeLeft;
	if($totalQuestion > $quesno)
	{
		$quesno++;
		$sq	=	"UPDATE adepts_userwiseSbaStatus SET timeTaken=$timeTaken,currentQno=$quesno WHERE userID=$userID AND sbaTestID=$sbaTestID";
		mysql_query($sq);
		$timeLeft	=	getSbaQcode($userID,$sbaTestID);
	}
	else
	{
		$_SESSION["qno"]	=	"";
		$_SESSION["qcode"]	=	"";
	}
}

function getQcodeDetails($userID,$sbaTestID)
{
	$arrQcodes	=	array();
	$sq	=	"SELECT qcode,A,R FROM adepts_userwiseSbaQuesAttempt WHERE userID=$userID AND sbaTestID=$sbaTestID";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		if($rw[1]=="" || $rw[2]==2)
			$a=0;
		else
			$a=1;
		$arrQcodes[$rw[0]]	=	$a;
	}
	return $arrQcodes;
}

function finishSbaTest($sbaTestID,$userID)
{
	$sq	=	"UPDATE adepts_userwiseSbaQuesAttempt SET R=0 WHERE sbaTestID=$sbaTestID AND userID=$userID AND R=2";
	$rs	=	mysql_query($sq);
	
	$sq	=	"SELECT SUM(R),COUNT(qno) FROM adepts_userwiseSbaQuesAttempt WHERE sbaTestID=$sbaTestID AND userID=$userID";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	$score = $rw[0];
	$total	=	$rw[1];
	
	$sq	=	"UPDATE adepts_userwiseSbaStatus SET score=$score,status='completed',endTime=NOW()
			 WHERE userID=$userID AND sbaTestID=$sbaTestID";
	mysql_query($sq);
	$perCorrect	=	round(($score/$total)*100);
	return $perCorrect;
}

function getUserAnswer($userID,$qcode,$qno)
{
	$sq	=	"SELECT A FROM adepts_userwiseSbaQuesAttempt WHERE userID=$userID AND qcode=$qcode AND qno=$qno";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	return $rw[0];
}

function saveTimeTaken($sbaTestID,$userID,$totalTime,$timeLeft)
{
	$timeTaken	=	($totalTime * 60) - $timeLeft;
	$sq	=	"UPDATE adepts_userwiseSbaStatus SET timeTaken=$timeTaken WHERE userID=$userID AND sbaTestID=$sbaTestID";
	mysql_query($sq);
}

function getTestDetails($sbaTestID)
{
	$arrayTestDetails	=	array();
	if($sbaTestID!="")
	{
		$sq	=	"SELECT maxTime,totalQues FROM adepts_sbaBluePrint WHERE sbaTestID=$sbaTestID";
		$rs	=	mysql_query($sq);
		$rw	=	mysql_fetch_array($rs);
		$arrayTestDetails[]	=	$rw[0];
		$arrayTestDetails[]	=	$rw[1];
	}
	return $arrayTestDetails;
}

function getSbaAttemptDetails($userID,$sbaTestID)
{
	$sbaAttemptDetails	=	array();
	$sq	=	"SELECT totalQues, maxTime, currentQno, score, timeTaken FROM adepts_userwiseSbaStatus WHERE userID=$userID AND sbaTestID=$sbaTestID AND reportViewed=0";
	$rs	=	mysql_query($sq);
	if($rw=mysql_fetch_array($rs))
	{
		$sbaAttemptDetails["totalQues"]		=	$rw["totalQues"];
		$sbaAttemptDetails["maxTime"]		=	$rw["maxTime"];
		$sbaAttemptDetails["currentQno"]	=	$rw["currentQno"];
		$sbaAttemptDetails["score"]			=	$rw["score"];
		$sbaAttemptDetails["timeTaken"]		=	$rw["timeTaken"];
		$sq	=	"UPDATE adepts_userwiseSbaStatus SET reportViewed=1 WHERE userID=$userID AND sbaTestID=$sbaTestID AND reportViewed=0";
		mysql_query($sq);
		return $sbaAttemptDetails;	
	}
	else
	{
		return "";
	}
}

function getSbaQuesDetails($userID,$sbaTestID)
{
	$ans = array();
	$totalAttempted=0;
	$sq	=	"SELECT srno, qno, qcode, A, R FROM adepts_userwiseSbaQuesAttempt WHERE userID=$userID AND sbaTestID=$sbaTestID";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$tmp = array();
		array_push( $tmp, $rw[ 'srno' ]);
		array_push( $tmp, $rw[ 'qno' ]);
		array_push( $tmp, $rw[ 'qcode' ]);
		array_push( $tmp, str_replace("|",", ",$rw[ 'A' ]));
		$question = new sbaQuestion($rw['qcode']);
		if($question->isDynamic())
		{
			$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE class=".$_SESSION['childClass']." AND quesAttempt_srno= ".$row[ 'srno' ];
			$dynamic_result = mysql_query($query);
			$dynamic_line   = mysql_fetch_array($dynamic_result);
			$question->generateQuestion("answer",$dynamic_line[0]);
		}
		array_push( $tmp, $question->getCorrectAnswerForDisplay());
		array_push( $tmp, $rw[ 'R' ]);
		if($rw['A']!="")
			$totalAttempted++;
		array_push( $ans, $tmp);
	}
	array_push( $ans, $totalAttempted);
	return $ans;
}

function checkForSbaReport($sessionID,$userID)
{
	$sq	=	"SELECT sbaTestID,DATE_FORMAT(endTime,'%Y%m%d') FROM adepts_userwiseSbaStatus WHERE userID=$userID AND reportViewed=0 AND status='Completed'";
	$rs	=	mysql_query($sq);
	if($rw=mysql_fetch_array($rs))
	{
		$sbaID	=	$rw[0];
		$sbaDate	=	$rw[1];
		$sq	=	"SELECT sessionID FROM adepts_sessionStatus WHERE sessionID=$sessionID AND startTime_int > ".$sbaDate."";
		$rs	=	mysql_query($sq);
		if(mysql_num_rows($rs))
		{
			return $sbaID;
		}
	}
	else
	{
		return "";
	}
}

?>