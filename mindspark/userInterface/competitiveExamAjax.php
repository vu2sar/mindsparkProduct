<?php

@include("check1.php");
include("constants.php");
include("classes/clsQuestion.php");
include("functions/orig2htm.php");
include("functions/functionsForDynamicQues.php");
if(!isset($_SESSION['userID']))
{
    echo "You are not authorised to access this page! (URL copy pasted in the browser!)";
    exit;
}
$mode	=	$_POST['mode'];

if($mode=="checkTopic")
{
	$qcodeArray	=	array();
	$qcodeNewArray	=	array();
	$sources	=	$_POST['sources'];
	$topics		=	$_POST['topics'];
	$userID		=	$_POST['userID'];
	$higherGrade =	$_POST['higherGrade'];
	$sources	=	substr($sources,0,-1);
	$topics		=	substr($topics,0,-1);
	$topics		=	str_replace(",","','",$topics);
	$preQcodes	=	getPrevQcodes($userID);
	$whereClause	=	"";
	if(strpos($sources,",") == true)
	{
		$sourcesArr	=	explode(",",$sources);
		foreach($sourcesArr as $source)
		{
			$whereClause	.=	"source LIKE ('%$source%') || ";
		}
		$whereClause	=	"(".substr($whereClause,0,-3).")";
	}
	else
	{
		$whereClause	=	"source LIKE ('%$sources%')";
	}
	
	$topicStr	=	"";
	$totalQues	=	0;
	$sq	=	"SELECT topicCode,count(*),GROUP_CONCAT(qcode) FROM adepts_competitiveExamMaster 
			 WHERE 1=1 AND $whereClause ";
	if($topics!="")
		$sq	.=	" AND topicCode NOT IN ('$topics')";
	if($preQcodes!="")
		$sq	.=	" AND qcode NOT IN ($preQcodes)";
	if($higherGrade==0)
		$sq	.=	" AND level <= ".$_SESSION["childClass"];
	$sq	.=	" AND status=3 GROUP BY topicCode";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$qcodeArray[]	=	explode(",",$rw[2]);
		$topicStr	.=	$rw[0].",";
		$totalQues	+=	$rw[1];
	}
	if($totalQues>9)
	{
		$i=0;
		$j=0;
		$k	=	rand(0,count($qcodeArray)-1);
		while($i<10)
		{
			$j++;
			$c	=	rand(0,count($qcodeArray[$k])-1);
			$qcodeNewArray[$i]	=	$qcodeArray[$k][$c];
			unset($qcodeArray[$k][$c]);
			if(count($qcodeArray[$k])==0)
			{
				unset($qcodeArray[$k]);
				$qcodeArray	=	array_values($qcodeArray);
			}
			else
			{
				$qcodeArray[$k]	=	array_values($qcodeArray[$k]);
			}
			if($k >=count($qcodeArray)-1)
				$k=0;
			else
				$k++;
			$i++;
		}
	}
	$qcodeStr	=	implode("|",$qcodeNewArray);
	$topicStr	=	substr($topicStr,0,-1);
	echo $topicStr."||".$totalQues."||".$qcodeStr;
}
else if($mode=="getQuestions")
{
	$userID	=	$_POST['userID'];
	$qcodeStr	=	$_POST['qcodeStr'];
	$forTT	=	isset($_REQUEST['forTTcode'])?$_REQUEST['forTTcode']:'';
	$qcodeArray	=	explode("|",$qcodeStr);
	$pendingChallenge	=	$_POST['pendingChallenge'];
	if($pendingChallenge==0)
	{
		$sources	=	$_POST['sources'];
		$topics	=	$_POST['topics'];
		$challengeNo	=	setNewChallenge($userID,$sources,$topics,$qcodeArray, $forTTcode);
	}
	else if($pendingChallenge==1){	
		$challengeNo	=	$_REQUEST['challengeNo'];//getOldChallenge($userID);
	}
	$i=1;
	$responseQuesDetails	=	array();
	foreach($qcodeArray as $qcode)
	{
		$question = new Question($qcode);
		if($question->isDynamic())
			$question->generateQuestion();
		$comments	=	$question->comments;
		if(strpos($comments,"(") === false)
		{
			$comments	=	"(".$comments.")";
		}
		else
		{
			$commentsTempArray	=	explode(" ",$comments);
			$year	=	$commentsTempArray[count($commentsTempArray)-1];
			$regex = '#[^()]*\((([^()]+|(?R))*)\)[^()]*#';
			$replacement = '\1';
			$matches	=	preg_replace($regex, $replacement, $comments);
			$comments	=	"(".$matches." ".$year.")";
		}
		$quesText	=	$question->getQuestion();
		if($question->quesType=="Blank")
		{
			for($n=1;$n<7;$n++)
			{
				$pattern	=	'id="b'.$n.'"';
				$replacement	=	'id="b'.$n.'_'.$i.'"';
				$quesText	=	str_replace($pattern,$replacement,$quesText);
			}
		}
		$responseQuesDetails[$i]["qcode"] = $qcode;
		$responseQuesDetails[$i]["source"] = $comments;
		$responseQuesDetails[$i]["correctAns"] = encrypt($question->correctAnswer);
		$responseQuesDetails[$i]["quesType"] = $question->quesType;
		$responseQuesDetails[$i]["quesText"] = $quesText;
		$responseQuesDetails[$i]["quesDisplayAns"] = encrypt($question->getDisplayAnswer()); // New vesrsion includes Display Answer
		$responseQuesDetails[$i]["dropDownAns"] = encrypt($question->dropDownAns);
		$responseQuesDetails[$i]["dropDownAns"] = $question->comments;
		$responseQuesDetails[$i]["options"] = '';
		if($question->quesType=='MCQ-4' || $question->quesType=='MCQ-3' || $question->quesType=='MCQ-2')
		{
		  $responseQuesDetails[$i]["options"] .= '<table width="80%" border="0" cellspacing="2" cellpadding="3" class="fontHigherClass">';
		  if($question->quesType=='MCQ-4' || $question->quesType=='MCQ-2')
		  {
			  $responseQuesDetails[$i]["options"] .=  '<tr valign="top">
				  <td width="5%" align="center">&nbsp;</td>
				  <td width="5%" nowrap><span class="optionA">A<input type="radio" class="ansRadio'.$i.'" name="ansRadio'.$i.'" id="ansRadioA_'.$i.'" value="A" style="display:none"></span></td>
				  <td width="43%" align="left" valign="middle">'.$question->getOptionA().'</td>
				  <td width="5%" nowrap><span class="optionB">B<input type="radio" class="ansRadio'.$i.'" name="ansRadio'.$i.'" id="ansRadioB_'.$i.'" value="B" style="display:none"></span></td>
				  <td width="42%" align="left" valign="middle">'.$question->getOptionB().'</td>
			  </tr>';
		  }
		  if($question->quesType=='MCQ-4')
		  {
			  $responseQuesDetails[$i]["options"] .=  '<tr><td colspan=5 height=10px></td><tr valign="top">
				  <td width="5%" align="center" valign="top">&nbsp;</td>
				  <td width="5%" nowrap><span class="optionC">C<input type="radio" class="ansRadio'.$i.'" name="ansRadio'.$i.'" id="ansRadioC_'.$i.'" value="C" style="display:none"></span></td>
				  <td width="43%" valign="middle">'.$question->getOptionC().'</td>
				  <td width="5%" nowrap><span class="optionD">D<input type="radio" class="ansRadio'.$i.'" name="ansRadio'.$i.'" id="ansRadioD_'.$i.'" value="D" style="display:none"></span></td>
				  <td width="42%" valign="middle">'.$question->getOptionD().'</td>
			  </tr>';
		  }
		  if($question->quesType=='MCQ-3')
		  {
			  $responseQuesDetails[$i]["options"] .= '<tr valign="top">
				  <td width="5%" align="center">&nbsp;</td>
				  <td width="5%" nowrap><span class="optionA">A<input type="radio" class="ansRadio'.$i.'" name="ansRadio'.$i.'" id="ansRadioA_'.$i.'" value="A" style="display:none"></span></td>
				  <td width="28%" valign="middle">'.$question->getOptionA().'</td>
				  <td width="5%" nowrap><span class="optionB">B<input type="radio" class="ansRadio'.$i.'" name="ansRadio'.$i.'" id="ansRadioB_'.$i.'" value="B" style="display:none"></span></td>
				  <td width="26%" valign="middle">'.$question->getOptionB().'</td>
				  <td width="5%" nowrap><span class="optionC">C<input type="radio" class="ansRadio'.$i.'" name="ansRadio'.$i.'" id="ansRadioC_'.$i.'" value="C" style="display:none"></span></td>
				  <td width="26%" valign="middle">'.$question->getOptionC().'</td>
			  </tr>';
		  }
		  $responseQuesDetails[$i]["options"] .=  '</table>';
		}
		else
			$responseQuesDetails[$i]["options"] = '';
		$i++;
	}
	$responseQuesDetails["challengeNo"]=$challengeNo;
	echo json_encode($responseQuesDetails);
	/*echo "<pre>";
		print_r($responseQuesDetails);
	echo "</pre>";*/
}
else if($mode=="updateQuesAttempt")
{
	$qcode=$_POST["qcode"];
	$userAnswer=$_POST["userAnswer"];
	$userID=$_POST["userID"];
	$result=$_POST["result"];
	$timeSpent	=	$_POST["timeSpent"];
	$challengeNo	=	$_POST["challengeNo"];
	$completed	=	$_POST["completed"];
	$totalScore	=	$_POST["totalScore"];
	$quesNo	=	$_POST["quesNo"];
	$sq	=	"UPDATE adepts_competitiveExamQuesAttempt SET A='$userAnswer',R=$result WHERE userID='$userID' AND qcode=$qcode";
	$rs	=	mysql_query($sq);
	if($timeSpent!=0)
	{
		$sq	=	"UPDATE adepts_competitiveExamStatus SET timeTaken=timeTaken+$timeSpent WHERE userID='$userID' AND challengeNo=$challengeNo";
		$rs	=	mysql_query($sq);
		echo "";
	}
	if($quesNo==10 && $completed==1)
	{
		$sq	=	"UPDATE adepts_competitiveExamStatus SET score=$totalScore,endTime=NOW(),status='Completed' WHERE userID='$userID' AND challengeNo=$challengeNo";
		$rs	=	mysql_query($sq);
		$challengeReport	=	getChallengeReport($userID,$challengeNo);
		echo json_encode($challengeReport);
	}
}
else if($mode=="getReportQues")
{
	$qcodes	=	"";
	$ans	=	"";
	$respo	=	"";
	$challengeNo	=	$_POST["challengeNo"];
	$userID	=	$_POST["userID"];
	$sq	=	"SELECT GROUP_CONCAT(qcode SEPARATOR '|'),GROUP_CONCAT(A SEPARATOR '*$&'),GROUP_CONCAT(R SEPARATOR '*$&') FROM adepts_competitiveExamQuesAttempt
			 WHERE challengeNo=$challengeNo AND userID=$userID";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$qcodes	.=	$rw[0].'|';
		$ans	.=	$rw[1].'*$&';
		$respo	.=	$rw[2].'*$&';
		
		$qcodes	=	substr($qcodes,0,-1);
		$ans	=	substr($ans,0,-3);
		$respo	=	substr($respo,0,-3);
	}
	echo $qcodeStr	=	$qcodes."$#$".$ans."$#$".$respo;
}

function encrypt($str_message) {
    $len_str_message=strlen($str_message);
    $Str_Encrypted_Message="";
    for ($Position = 0;$Position<$len_str_message;$Position++)        {
        $Byte_To_Be_Encrypted = substr($str_message, $Position, 1);
        $Ascii_Num_Byte_To_Encrypt = ord($Byte_To_Be_Encrypted);

               $Ascii_Num_Byte_To_Encrypt = $Ascii_Num_Byte_To_Encrypt + 5;
               $Ascii_Num_Byte_To_Encrypt = $Ascii_Num_Byte_To_Encrypt * 2;

        $Str_Encrypted_Message .= $Ascii_Num_Byte_To_Encrypt."-";
    }
    $Str_Encrypted_Message = substr($Str_Encrypted_Message,0,-1);
    return $Str_Encrypted_Message;
}

function getPrevQcodes($userID)
{
	$qcodeStr	=	"";
	$sq	=	"SELECT qcode FROM adepts_competitiveExamQuesAttempt WHERE userID=$userID AND R=1";
	$rs	=	mysql_query($sq);
	if(mysql_num_rows($rs)!=0)
	{
		while($rw=mysql_fetch_array($rs))
		{
			$qcodeStr	.=	$rw[0].",";
		}
		$qcodeStr	=	substr($qcodeStr,0,-1);
	}
	return $qcodeStr;
}

function setNewChallenge($userID,$sources,$topics,$qcodeArray, $forTTcode)
{
	$sessionID=$_SESSION["sessionID"];
	$sources	=	substr($sources,0,-1);
	$topics	=	substr($topics,0,-1);
	$sq	=	"SELECT MAX(challengeNo) FROM adepts_competitiveExamStatus WHERE userID=$userID";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	$challengeNo	=	$rw[0]+1;
	$sq	=	"INSERT INTO adepts_competitiveExamStatus SET userID=$userID,challengeNo=$challengeNo,sources='$sources',topics='$topics',startTime=NOW()
			,totalQues=10,score=0,timeTaken=0,status='Pending', forTTcode='$forTTcode'";
	$rs	=	mysql_query($sq);
	foreach($qcodeArray as $qcode)
	{
		$sq	=	"INSERT INTO adepts_competitiveExamQuesAttempt SET userID=$userID,challengeNo=$challengeNo,sessionID=$sessionID,qcode=$qcode";
		$rs	=	mysql_query($sq);
	}
	return $challengeNo;
}

function getOldChallenge($userID)
{
	$sq	=	"SELECT challengeNo FROM adepts_competitiveExamStatus WHERE userID=$userID AND status='Pending'";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	return $rw[0];
}

function getChallengeReport($userID,$challengeNo)
{
	$reportHtml	=	"";
	$sq	=	"SELECT sources,topics, DATE_FORMAT(startTime,'%D %M %Y<br>%h:%i %p'),DATE_FORMAT(endTime,'%D %M %Y<br>%h:%i %p'),score FROM adepts_competitiveExamStatus WHERE userID=$userID AND challengeNo=$challengeNo";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	$reportHtml	=	'<tr>
		<td class="tdData" nowrap="nowrap"><a class="linkDisp" href="javascript:void(0)" onclick="getReport('.$challengeNo.')">Challenge '.$challengeNo.'</a></td>
		<td class="tdData">'.$rw[0].'</td>
		<td class="tdData">'.getTopicDesc($rw[1]).'</td>
		<td class="tdData">'.$rw[2].'</td>
		<td class="tdData">'.$rw[3].'</td>
		<td align="center" class="tdData">'.$rw[4].'</td>
	</tr>';
	return $reportHtml;
}

function getTopicDesc($topicCodeStr)
{
	$topicCodeStr	=	str_replace(",","','",$topicCodeStr);
	$topicStr	=	"";
	$i=0;
	$sq	=	"SELECT topic FROM adepts_topicMaster WHERE topicCode IN ('$topicCodeStr')";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$i++;
		if($i==3)
			$topicStr	.=	"<span class='showMoreLess' id='showMore'>...<br>Show More</span><span class='hideTopic'>";
		$topicStr	.=	", ".$rw[0];
	}
	if($i>2)
		$topicStr	.=	"</span><span class='showMoreLess' id='showLess'><br>Show Less</span>";
	$topicStr	=	substr($topicStr,2,strlen($topicStr));
	return $topicStr;
}

function changeDateFormat($dateTime)
{
	$dateTimeArray	=	explode(" ",$dateTime);
	$dateArray	=	explode("-",$dateTimeArray[0]);
	return $dateArray[2]."-".$dateArray[1]."-".$dateArray[0]." ".$dateTimeArray[1];
}
?>