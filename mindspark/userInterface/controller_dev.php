<?php
session_start();

include("../slave_connectivity.php");
//include("dbconf.php");
include("classes/clsQuestion_dev.php");
include("classes/clsNCERTQuestion.php");
//include("classes/clsTeacherQuestion.php");
include("classes/clsResearchQuestion.php");
include_once("functions/orig2htm.php");
include("constants.php");
include("functions/functions.php");
include_once("functions/functionsForDynamicQues.php");

error_reporting(E_ERROR);

$mode = isset($_REQUEST["mode"])?$_REQUEST["mode"]:"";

switch ($mode)
{
    case "firstQuestion":
					$qcode		=	$_POST['qcode'];
					$quesno		=	$_POST['qno'];
					$tmpMode	=	$_POST['tmpMode'];
					$commentSrNo =  $_POST['commentSrNo'];
					$_SESSION['theme']	=	$_POST['theme'];

					$quesCategory	=	"normal";
					if($_SESSION["teacherQuestion"]==1)
					$quesCategory ="teacherQuestion";// new category
			    	//echo $quesCategory."TeacherQues".$_SESSION["teacherQuestion"]; 
					//exit;
					$response = createResponse($qcode,$quesno,$quesCategory,1,$tmpMode,$commentSrNo);
					echo $response;
					break;

    case "submitAnswer":
					echo "-10";
                    break;
}

function createResponse($qcode,$quesno,$quesCategory,$showAnswer="1",$tmpMode="",$commentSrNo="")
{
	$_SESSION['pageStartTime'] = date("Y-m-d H:i:s");
	//$clusterDetails = getClusterDetails($_SESSION['clusterCode'],$_SESSION['flow']);
	$response	=	array();
	// Added By Manish Dariyani For Practice Cluster..
	//Start..
	if(($_SESSION["currentClusterType"] == "practice" || $quesCategory == "NCERT") && $quesCategory!="prepostTestQues")
	{
		$qcodeArray = explode("##",$qcode);
		$response["qcode"] = $qcode;
		if($_SESSION["currentClusterType"] == "practice")
		{
			$response["tmpMode"] = "practice";
			$response["quesCategory"] = "practice";
		}
		else
		{
			$response["tmpMode"] = "NCERT";
			$response["quesCategory"] = "NCERT";
		}
		$response["showAnswer"]	=	0;
		foreach($qcodeArray as $singleqCode)
		{
			if($quesCategory == "NCERT")
				$question = new ncertQuestion($singleqCode);
			else
				$question = new Question($singleqCode);
			if($question->isDynamic())
			{
				if($commentSrNo != "")
				{
					$query  = "SELECT dynamicParameters FROM adepts_userComments WHERE srno=".$commentSrNo;
					$params_result = mysql_query($query);
					$params_line   = mysql_fetch_array($params_result);
					if($params_line[0] != "")
					{
						$question->generateQuestion("answer",$params_line[0]);
						$question->dynamicParams = $params_line[0];					
					}
					else
						$question->generateQuestion();
				}
				else
					$question->generateQuestion();
			}
            if($question->quesType=="Blank" || $question->quesType=="D" && $question->correctAnswer!="")
               $noOfBlanks[] = substr_count($question->correctAnswer,"|") +  1;
            else
                $noOfBlanks[] = 0;
			$correctAnswer[] = encrypt($question->correctAnswer);
			$quesType[] = $question->quesType;
                            $dynamic[]	=	$question->isDynamic();
		         $dynamicParams[]	= $question->dynamicParams;
			$eeIcon[] = $question->eeIcon;//Equation Editor Icon Flag
			$Q2[] = $question->getQuestion();
			if($quesCategory == "NCERT")
				$Q1[] = $question->subQuestionNo;
			//$ansForDisplay[] = encrypt($question->getCorrectAnswerForDisplay());
			$ansForDisplay[] = encrypt($question->getDisplayAnswer()); // New vesrsion includes Display Answer
			$encryptedDropDownAns[] = encrypt($question->dropDownAns);
			$option = '';
			if($question->quesType=='MCQ-4' || $question->quesType=='MCQ-3' || $question->quesType=='MCQ-2')
			{
			  $option .= '<table width="80%" border="0" cellspacing="2" cellpadding="3" class="fontHigherClass">';
			  if($question->quesType=='MCQ-4' || $question->quesType=='MCQ-2')
			  {
				  $option .=  '<tr>
					  <td width="5%" align="center">&nbsp;</td>
					  <td width="5%" nowrap><b>A</b><input type="radio" name="ansRadio" id="ansRadioA" value="A"></td>
					  <td width="43%" align="left" onclick="setOpt(\'A\')">'.$question->getOptionA().'</td>
					  <td width="5%" nowrap><b>B</b><input type="radio" name="ansRadio" id="ansRadioB" value="B"></td>
					  <td width="42%" align="left" onclick="setOpt(\'B\')">'.$question->getOptionB().'</td>
				  </tr>';
			  }
			  if($question->quesType=='MCQ-4')
			  {
				  $option .=  '<tr><td colspan=5 height=10px></td><tr>
					  <td width="5%" align="center" valign="top">&nbsp;</td>
					  <td width="5%" nowrap><b>C</b><input type="radio" name="ansRadio" id="ansRadioC" value="C"></td>
					  <td width="43%" onclick="setOpt(\'C\')">'.$question->getOptionC().'</td>
					  <td width="5%" nowrap><b>D</b><input type="radio" name="ansRadio" id="ansRadioD" value="D"></td>
					  <td width="42%" onclick="setOpt(\'D\')">'.$question->getOptionD().'</td>
				  </tr>';
			  }
			  if($question->quesType=='MCQ-3')
			  {
				  $option .= '<tr>
					  <td width="5%" align="center">&nbsp;</td>
					  <td width="5%" nowrap><b>A</b><input type="radio" name="ansRadio" id="ansRadioA" value="A"></td>
					  <td width="28%" onclick="setOpt(\'A\')">'.$question->getOptionA().'</td>
					  <td width="5%" nowrap><b>B</b><input type="radio" name="ansRadio" id="ansRadioB" value="B"></td>
					  <td width="26%" onclick="setOpt(\'B\')">'.$question->getOptionB().'</td>
					  <td width="5%" nowrap><b>C</b><input type="radio" name="ansRadio" id="ansRadioC" value="C"></td>
					  <td width="26%" onclick="setOpt(\'C\')">'.$question->getOptionC().'</td>
				  </tr>';
			  }
			  $option .=  '</table>';
		   	}
		   	else
				$option = '';
			$Q4[] = $option;
		}
		$correctAnswer = implode("##",$correctAnswer);
                   $noOfBlanks = implode("##",$noOfBlanks);
		$quesType = implode("##",$quesType);
                   $response["dynamicQues"]	=  implode("##",$dynamic);
		$response["dynamicParams"]	=  implode("##",$dynamicParams);
		if($quesCategory == "NCERT")
			$Q1 = implode("##",$Q1);
		$Q2 = implode("##",$Q2);
		$Q4 = implode("##",$Q4);
		$eeIcon = implode("##",$eeIcon);//Equation Editor Icon Flag
		$ansForDisplay = implode("##",$ansForDisplay);
		if($_SESSION["currentClusterType"] == "practice")
		{
			$sql = "SELECT groupText, groupColumn FROM adepts_groupInstruction, adepts_questions WHERE qcode=$qcodeArray[0] AND adepts_groupInstruction.groupID = adepts_questions.groupID";
			$result = mysql_query($sql) or die("A".$sql.mysql_error());
			$row = mysql_fetch_assoc($result);
			$Q5 = $row["groupText"]; // Group Title will go here...
			$hint = $row["groupColumn"]; // Group Column will go here...
			$Q7 = "";
			$Q8 = "";
			$Q9 = "";
		}
		else
		{
			$sql = "SELECT groupText, groupNo, groupColumn FROM adepts_groupInstruction WHERE groupID = $question->groupID";
			$result = mysql_query($sql) or die("B".mysql_error().$sql);
			$row = mysql_fetch_assoc($result);
			$Q5 = $row["groupText"]; // Group Title will go here...
			if(trim($Q5) == "")
				$Q5 = "";
			$Q5 = orig_to_html($Q5,"images");
			$hint = $row["groupColumn"]; // Group Column will go here...
			$hint = 1; // Group Column will go here...
			$groupNo = $row["groupNo"]; // Group no to be displayed on screen for homework module..

			$sql = "SELECT A, R, eeresponse,eeResponseImg FROM adepts_ncertQuesAttempt WHERE qcode IN (".implode(",",$qcodeArray).") AND ncertAttemptID=".$_SESSION['ncertAttemptID']." ORDER BY srno";
			$result = mysql_query($sql);
			while($row = mysql_fetch_array($result))
			{
				$Q7 .= $row[0];
				$Q7 .= "[eeresponse]".$row[2]."@$*@".$row[3]."##";
				$Q9 .= $row[1]."##";
				$Q8 += $row[1];
			}
			$Q7 = substr($Q7,0,-2);
			$Q9 = substr($Q9,0,-2);
			$Q8 = ($Q8<0)?0:1;
			/*$Q7 = str_replace(",","##",$row[0]);
			$Q8 = ($row[1]<0)?0:1;
			$Q9 = str_replace(",","##",$row[2]);*/

		}
		if($correctAnswer == "") $correctAnswer = "";
        $response["noOfBlanks"]  = $noOfBlanks;
		$response["correctAnswer"]	=	$correctAnswer;
		$response["quesType"]	= $quesType;
		if($quesCategory == "NCERT")
			$response["clusterCode"] = $question->exerciseCode;
		else
			$response["clusterCode"]	= $question->clusterCode;
		$response["hasExpln"]	= 0;
		if($quesCategory == "NCERT")
			$response["Q1"] = $Q1;
		else if($quesno!='')
			$response["Q1"]	=	$quesno;
		else
			$response["Q1"]	=	"";
		$response["Q2"]	= $Q2;
		if($Q4 == "") $Q4 = "";
		$response["Q4"]	= $Q4;
		if($eeIcon!="")
			$response["eeIcon"]	= $eeIcon;//Equation Editor Icon Flag
		else
			$response["eeIcon"]	= "";//Equation Editor Icon Flag
		//$response .= "<Q5>".htmlentities($Q5)."</Q5>"; // Group Title will go here...
		$response["dispAns"]	= $Q5; // Group Title will go here...

		$category = $_SESSION['admin'];
		$footer = '';
		if($category=='ADMIN')
		{
			//$footer = "[<b>Cluster: </b>".$question->clusterCode." <b>Qcode: </b>".$qcode." <b>SDL</b>:".$question->subDifficultyLevel." <b>Session ID: </b>".$sessionID."]";
		}
		elseif (strcasecmp($category,'STUDENT')==0 || strcasecmp($category,'GUEST')==0)
		{
			//$footer = "[<b>Session ID: </b>".$sessionID." <b>Question No: </b>".$quesno."]";
		}
		else
			$footer = '';

		if(isset($_SESSION["userType"]) && $_SESSION["userType"] == "msAsStudent")
		{
			$totalSDL = count(array_keys($_SESSION["allQuestionsArray"]));
			$currentSDL = array_search($question->subDifficultyLevel,array_keys($_SESSION["allQuestionsArray"])) + 1;
			$clusterLevel =  $clusterDetails["level"];
			$clusterName =  $clusterDetails["cluster"];
			$remedialCluster = $clusterDetails["remedialCluster"];
			$footer .= "<span class=\"ttInfo\"><a style='text-decoration:underline' id='tagtoModify".$qcode."' href='javascript:void(0)' onclick=\"showTagBox('tagMsgBox', '', '".$qcode."');\">Need to modify</a><br><a style='text-decoration:underline' href='javascript:void(0)' onclick=\"$('#msAsStudentInfo').html('')\">Hide</a><br><strong>Qcode: </strong><a href=\"http://www.educationalinitiatives.com/mindspark/add_edit_question_viewmode.php?qcode=".$singleqCode."&list=".implode(",",$qcodeArray)."\" target=\"_blank\">".$qcode."</a><br><strong>Cluster Code: </strong>".$question->clusterCode."<br><strong>SDL</strong> $currentSDL of $totalSDL<span><br><strong>Remedial Unit: </strong>".$remedialCluster."<br><strong>".$_SESSION["flow"]." Level: </strong>".$clusterLevel."<br><strong>Cluster: </strong>".$clusterName."</span></span>";
		}
		//$footer .= "<br><div id='img_error'></div>";

		$response["footer"]	=	$footer;

		$sparkie = "";
       	$tmpCount = $_SESSION['noOfJumps'];
        if($childClass<8)
        {
           $sparkie = $tmpCount;
        	/*if($tmpCount>=10)
			{
        		$sparkiecount = intval($tmpCount/10);

        		for($j=0; $j<$sparkiecount; $j++)
        		$sparkie .=  "<img src='images/Sparkie.png' height='50'>";
        		$tmpCount = $tmpCount%10;
        	}
        	for($j=0; $j<$tmpCount; $j++)
			{
        		$sparkie .= "<img src='images/Sparkie.png' height='30'>";
        	}*/
        }
        else
        {
        	$rewardPoints = $tmpCount * 10;
        	if($rewardPoints>0)
        	{
        		$sparkie .= "<span class='reward_points'>Reward Points: ".$rewardPoints."</span>";
        	}
        }

        if($sparkie!='')
        	$response["sparkie"]	= $sparkie;
        else
        	$response["sparkie"] = "";

		if($_SESSION["currentClusterType"] == "practice")
			$response["pnlCQ"]	= correctQuestionsInPC($_SESSION['clusterAttemptID']);
		else
			$response["pnlCQ"] = "";
		$response["pnlWC"]	=	"";
		$response["voiceover"]	= $ansForDisplay;
		$response["hint"]	=	$hint; // Group Column will go here...
		$encryptedDropDownAns = implode("##",$encryptedDropDownAns);
		if(trim($encryptedDropDownAns) == "")
			$encryptedDropDownAns = "";
		$response["dropdownAns"]	=	$encryptedDropDownAns;
		$response["preloadDisplayAnswerImage"]	= "";
		$response["problemid"]	= "Session ID:".$sessionID." Question No:".$quesno;
		$response["topicChangeMsg"]	=	"Are you sure you want to switch to another topic?";
		$response["quesVoiceOver"]	= "";
		$response["ansVoiceOver"]	= "";
		$response["noOfTrials"]	= 1;
//Added by chirag for custom display answers on May, 21, 2012 ---start here
		if(isset($groupNo) && $groupNo != "")
			$response["dispAnsA"]	= $groupNo;
		else
			$response["dispAnsA"]	= "";

		if($Q7 == "") $Q7 = "";
		$response["dispAnsB"]	= $Q7;
		$response["dispAnsC"]	= $Q8;
		$response["dispAnsD"]	= $Q9;
		$response["hintAvailable"]	= "0";
		if($_SESSION['quesAttemptedInTopic'])
			$response["quesAttemptedInTopic"]	= $_SESSION['quesAttemptedInTopic'];
		else
			$response["quesAttemptedInTopic"]	= "";
			
		if($_SESSION['quesCorrectInTopic'])
			$response["quesCorrectInTopic"]	= $_SESSION['quesCorrectInTopic'];
		else
			$response["quesCorrectInTopic"]	= "";
			
		if($_SESSION['progressInTopic'])
			$response["progressInTopic"]	= $_SESSION['progressInTopic'];
		else
			$response["progressInTopic"]	= "";
		
		if($_SESSION['lowerLevel'])
			$response["lowerLevel"]	= $_SESSION['lowerLevel'];
		else
			$response["lowerLevel"]	= "";

		$signature = getMsgSignature($response["qcode"], $response["quesType"],$response["correctAnswer"],$response["dropdownAns"],$response["clusterCode"],$response["hintAvailable"],$response["noOfTrials"],$response["quesCategory"]);
		$response["signature"] = $signature;
		$response["condition"]	=	"";
		$response["action"]	=	"";
		$response["noOfCondition"]	="";
//------End
	}
	else
	{
	//End Here....
		if($quesCategory=="sba")
			$question = new sbaQuestion($qcode);
		else if($tmpMode=="research")
			$question = new researchQuestion($qcode);
		else if($quesCategory=="teacherQuestion")
		{
			$question = new teacherQuestion($qcode);
		}
		else
		{

			$question = new Question($qcode);
			
			if(isset($_SESSION['html5version']) && $_SESSION['html5version']==1)
			{
				$question->getAllSwfInQcode();
				$question->changeSwfWithHtml();
			}
		}
		if($question->isDynamic())
		{
			if($commentSrNo != "")
			{
				$query  = "SELECT dynamicParameters FROM adepts_userComments WHERE srno=".$commentSrNo;
				$params_result = mysql_query($query);
				$params_line   = mysql_fetch_array($params_result);
				if($params_line[0] != "")
				{
					$question->generateQuestion("answer",$params_line[0]);
					$question->dynamicParams = $params_line[0];					
				}
				else
					$question->generateQuestion();
			}
			else
				$question->generateQuestion();
		}
		$noOfBlanks = 0;
		if($question->quesType=="Blank" || $question->quesType=="D" && $question->correctAnswer!="")
			$noOfBlanks = substr_count($question->correctAnswer,"|") +  1;
		$encryptedCorrectAns  = encrypt($question->correctAnswer);
		$encryptedDropDownAns = encrypt($question->dropDownAns);
		//added for conditional alerts
		
		$condition = $question->condition;
		$action = $question->action;
		$noOfCondition = $question->conditionAvailable;
		//ends here
		$quesCategory  = isset($quesCategory)?$quesCategory:"normal";
		$quesCategory  = trim($quesCategory);

		$quesVoiceOver = $ansVoiceOver = "";
		
		if($question->quesVoiceOver!="" && $_SESSION['theme']==1)
		{
			if($tmpMode=='research')
			{
				$quesVoiceOver = VOICEOVER_FOLDER."/".'researchQues'."/".$question->quesVoiceOver;
			}
			else
			{
				$quesVoiceOver = (checkIfUnlive($qcode) ? VOICEOVER_FOLDER_AUTOMATED : VOICEOVER_FOLDER)."/".substr($question->clusterCode,0,3)."/".$question->quesVoiceOver;
			}
		}
		if($question->ansVoiceOver!="" && $_SESSION['theme']==1)
		{
			$ansVoiceOver = VOICEOVER_FOLDER."/".substr($question->clusterCode,0,3)."/".$question->ansVoiceOver;
		}
		$hasExpln = $question->hasExplanation();

		$displayAns = $question->getDisplayAnswer();
		if(stristr($displayAns,".swf"))
		{
			$displayAns = $question->getCorrectAnswerForDisplay();
			$hasExpln = 0;
		}
		$display_answer  = encrypt($displayAns);
//Added by chirag for custom display answers on May, 21, 2012 ---start here
		if($quesCategory!="wildcard" && $quesCategory!="teacherQuestion" && $tmpMode!="research")
		{
			$display_answerA = encrypt($question->getDisplayAnswerA());
			$display_answerB = encrypt($question->getDisplayAnswerB());
			$display_answerC = encrypt($question->getDisplayAnswerC());
			$display_answerD = encrypt($question->getDisplayAnswerD());
			$pnlWC	=	"";
		}
		else
		{
			if($_SESSION['theme']==3)
			{
				$pnlWC	=	"<img src='images/wildcard.png' id='wildcardImg' alt='Here is an opportunity to earn 10 reward points by answering a wild card question!!! A wild card question is a question that may not be related to the topic that you are currently doing. It is asked to test your alertness and ability to answer a question from other topics. You will get 10 reward points for answering a wild card question correctly. Remember that wild card questions will not affect your performance in the regular topics.' align='center'>";
			}
			else
			{
				$pnlWC	=	"<img src='images/wildcard.png' id='wildcardImg' alt='Wild card Question' title='Here is an opportunity to earn a full sparkie by answering a wild card question!!! A wild card question is a question that may not be related to the topic that you are currently doing. It is asked to test your alertness and ability to answer a question from other topics. You will get a sparkie for answering a wild card question correctly. Remember that wild card questions will not affect your performance in the regular topics.' align='center'>";
			}
			$display_answerA = "";
			$display_answerB = "";
			$display_answerC = "";
			$display_answerD = "";
			$showAnswer	=	1;
		}
//----End
		//sleep(10);

		//Construct the response to be sent back to the question page
		$response = array();
			$response["qcode"]	= $qcode;

			if($tmpMode!='')
				$response["tmpMode"]	= $tmpMode;
			else
				$response["tmpMode"]	= "";

			if($quesCategory!='')
				$response["quesCategory"]	= $quesCategory;
			else
				$response["quesCategory"]	= "";

			if($showAnswer!='')
				$response["showAnswer"]	= $showAnswer;
			else
				$response["showAnswer"]	= "";

			if($encryptedCorrectAns!='')
				$response["correctAnswer"]	= $encryptedCorrectAns;
			else
				$response["correctAnswer"]	= "";
                            $response["noOfBlanks"] = $noOfBlanks;
			if($question->quesType!='')
				$response["quesType"]	=	$question->quesType;
			else
				$response["quesType"]	=	"";

			if($question->clusterCode!='')
				$response["clusterCode"]	=	$question->clusterCode;
			else
				$response["clusterCode"]	= "";

			$response["hasExpln"]	= $hasExpln;

			if($quesCategory!="challenge" && $quesCategory!="wildcard" && strcasecmp($quesCategory,"EoLCQ")!=0 && $quesCategory!="prepostTestQues")
			{
				if($quesno!='')
					$response["Q1"]	=	$quesno;
				else
					$response["Q1"]	=	"";
			}
			/*else if($quesCategory=="challenge")
				$response .= "<Q1>C.Q.</Q1>";*/
			else
				$response["Q1"]	= "";

			$questionStr = $question->getQuestion();

			if($questionStr!='')
				$response["Q2"]	= $questionStr;
			else
				$response["Q2"]	= "";
                            $optionA = $optionB = $optionC = $optionD = "";
			if($question->quesType=='MCQ-4' || $question->quesType=='MCQ-3' || $question->quesType=='MCQ-2')
			{
				$optionA = $question->getOptionA();
				$optionB = $question->getOptionb();
				if($question->quesType=='MCQ-4' || $question->quesType=='MCQ-3')
				{
					$optionC = $question->getOptionC();
				}
				if($question->quesType=='MCQ-4')
				{
					$optionD = $question->getOptionD();
				}
			}
		   $response["optionA"] = $optionA;
		   $response["optionB"] = $optionB;
                      $response["optionC"] = $optionC;
                      $response["optionD"] = $optionD;
	             if($question->eeIcon=="")
				$response["eeIcon"]	= $question->eeIcon;//Equation Editor Icon Flag
			else
				$response["eeIcon"]	= "";//Equation Editor Icon Flag 

		   if($display_answer!='')
				$response["dispAns"]	=	$display_answer;
		   else
				$response["dispAns"]	= "";

		   $category = $_SESSION['admin'];
		   $footer='';
		   if($category=='ADMIN')
		   {
				//$footer = "[<b>Cluster: </b>".$question->clusterCode." <b>Qcode: </b>".$qcode." <b>SDL</b>:".$question->subDifficultyLevel." <b>Session ID: </b>".$sessionID."]";
		   }
		   elseif ($category=='STUDENT' || $category=='GUEST')
		   {
				//$footer = "[<b>Session ID: </b>".$sessionID." <b>Question No: </b>".$quesno."]";
		   }
		   else
				$footer = '';
			if(isset($_SESSION["userType"]) && $_SESSION["userType"] == "msAsStudent")
			{
				$totalSDL = count(array_keys($_SESSION["allQuestionsArray"]));
				$currentSDL = array_search($question->subDifficultyLevel,array_keys($_SESSION["allQuestionsArray"])) + 1;
				$clusterLevel =  $clusterDetails["level"];
				$clusterName =  $clusterDetails["cluster"];
				$remedialCluster = $clusterDetails["remedialCluster"];
				$footer .= "<span class=\"ttInfo\"><a style='text-decoration:underline' id='tagtoModify".$qcode."' href='javascript:void(0)' onclick=\"showTagBox('tagMsgBox', '', '".$qcode."');\">Need to modify</a><br><a style='text-decoration:underline' href='javascript:void(0)' onclick=\"$('#msAsStudentInfo').html('')\">Hide</a><br><strong>Qcode: </strong><a href=\"http://www.educationalinitiatives.com/mindspark/add_edit_question_viewmode.php?qcode=".$qcode."\" target=\"_blank\">".$qcode."</a><br><strong>Cluster Code: </strong>".$question->clusterCode."<br><strong>SDL</strong> $currentSDL of $totalSDL<span><br><strong>Remedial Unit: </strong>".$remedialCluster."<br><strong>".$_SESSION["flow"]." Level: </strong>".$clusterLevel."<br><strong>Cluster: </strong>".$clusterName."</span></span>";
			}
		   $footer .= "<br><div id='img_error'></div>";

		   $response["footer"]	= $footer;

		   $sparkie = "";
		   $tmpCount = $_SESSION['noOfJumps'];
			if($childClass<8)
			{
				/*if($tmpCount>=10)        {
					$sparkiecount = intval($tmpCount/10);

					for($j=0; $j<$sparkiecount; $j++)
					$sparkie .=  "<img src='images/Sparkie.png' height='50'>";
					$tmpCount = $tmpCount%10;
				}
				for($j=0; $j<$tmpCount; $j++)        {
					$sparkie .= "<img src='images/Sparkie.png' height='30'>";
				}*/
                                    $sparkie = $tmpCount;
			}
			else
			{
				$rewardPoints = $tmpCount * 10;
				if($rewardPoints>0)
				{
					$sparkie .= "<span class='reward_points'>Reward Points: ".$rewardPoints."</span>";
				}
			}

			if($sparkie!='')
				$response["sparkie"]	=	$sparkie;
			else
				$response["sparkie"]	=	"";

		   if(strcasecmp($quesCategory,"challenge")==0 || strcasecmp($quesCategory,"EoLCQ")==0)
		   {
		   		if($_SESSION['competitiveExamCQ']==1)	//implies CQ from Competitive exam bank - show from where it has been picked up
		   		{
					$pnlCQ = '<div class="CQ"><div class="CQLeft"><img src="images/exam.jpg" width="70" height="60"/></div><div class="CQRight">Challenge Question from<br/><strong>'.$question->comments.'</strong></div></div>';
				}
				else
					$pnlCQ =  "<img src='images/chanllenge.png' alt='Challenge Question' align=center>";
		   }
		   else
				$pnlCQ = "";

			if($pnlCQ!='')
				$response["pnlCQ"]	=	$pnlCQ;
			else
				$response["pnlCQ"]	=	"";

			if($pnlWC!='')
				$response["pnlWC"]	=	$pnlWC;
			else
				$response["pnlWC"]	=	"";

			if(trim($quesVoiceOver)!="" && $childClass<3)
				$voiceover = 1;
			else
				$voiceover = 0;

			$response['theme'] = $_SESSION['theme'];
			if($voiceover!='')
				$response["voiceover"]	=	$voiceover;
			else
				$response["voiceover"]	=	"";

			if($question->hint1 != "") {
				$hint	.=	$question->getHint1().'||';
				if($question->hint2 != "")
					$hint	.=	$question->getHint2().'||';
				if($question->hint3 != "")
					$hint	.=	$question->getHint3().'||';
				if($question->hint4 != "")
					$hint	.=	$question->getHint4();
			}
			else
				$hint = '';

			if($hint!='')
			{
				if($quesCategory=='challenge' && $showAnswer==1)
					$response["hint"]	=	$hint;
				else if($quesCategory!='challenge')
					$response["hint"]	=	$hint;
				else
					$response["hint"]	=	"";
			}
			else
				$response["hint"]	=	"";

			if($encryptedDropDownAns!='')
				$response["dropdownAns"]	=	$encryptedDropDownAns;
			else
				$response["dropdownAns"]	=	"";

			if($question->isDynamic()!='')
				$response["dynamicQues"]	=	$question->isDynamic();
			else
				$response["dynamicQues"]	=	"";

			//$question->dynamicParams = "";
			if($question->dynamicParams!='')
				$response["dynamicParams"]	= $question->dynamicParams;
			else
				$response["dynamicParams"]	= "";

			$preloadStr = $question->displayAnswer." ".$question->questionStem." ".$question->optionA." ".$question->optionB." ".$question->optionC." ".$question->optionD;

			$preloadStr = checkImage($preloadStr);        //Check and preload images if any in the display answer.

			if($preloadStr!='')
				$response["preloadDisplayAnswerImage"]	= $preloadStr;
			else
				$response["preloadDisplayAnswerImage"]	= "";

			$problemid = "Session ID:".$sessionID." Question No:".$quesno;

			$response["problemid"]	=	$problemid;

			if($quesCategory=="challenge")
				$response["topicChangeMsg"]	=	"If you change the topic now, you will lose the chance to answer this question.<br/>";
			else
				$response["topicChangeMsg"]	= "Are you sure you want to switch to another topic?";

			if($quesVoiceOver!='')
				$response["quesVoiceOver"]	= $quesVoiceOver;
			else
				$response["quesVoiceOver"]	= "";

			if($ansVoiceOver!='')
				$response["ansVoiceOver"]	= $ansVoiceOver;
			else
				$response["ansVoiceOver"]	= "";

			if($question->noOfTrials!="")
				$response["noOfTrials"]	=	$question->noOfTrials;
			else
				$response["noOfTrials"]	=	"";

			//Added by chirag for custom display answers on May, 21, 2012 ---start here
			if($display_answerA!='')
				$response["dispAnsA"]	= $display_answerA;
			else
				$response["dispAnsA"]	= "";

			if($display_answerB!='')
				$response["dispAnsB"]	=	$display_answerB;
			else
				$response["dispAnsB"]	= "";

			if($display_answerC!='')
				$response["dispAnsC"]	=	$display_answerC;
			else
				$response["dispAnsC"]	= "";

			if($display_answerD!='')
				$response["dispAnsD"]	=	$display_answerD;
			else
				$response["dispAnsD"]	=	"";
			if($quesCategory!="wildcard")
				$response["hintAvailable"]	= $question->hintAvailable;
			else
				$response["hintAvailable"]	= "0";
			if($_SESSION['quesAttemptedInTopic'])
				$response["quesAttemptedInTopic"]	= $_SESSION['quesAttemptedInTopic'];
			else
				$response["quesAttemptedInTopic"]	= "";
				
			if($_SESSION['quesCorrectInTopic'])
				$response["quesCorrectInTopic"]	= $_SESSION['quesCorrectInTopic'];
			else
				$response["quesCorrectInTopic"]	= "";
				
			if($_SESSION['progressInTopic'])
				$response["progressInTopic"]	= $_SESSION['progressInTopic'];
			else
				$response["progressInTopic"]	= "";
			if($_SESSION['lowerLevel'])
				$response["lowerLevel"]	= $_SESSION['lowerLevel'];
			else
				$response["lowerLevel"]	= "";
			if($condition!="")
				$response["condition"]	=	$condition;
			else
				$response["condition"]	=	"";
			if($action!="")
				$response["action"]	=	$action;
			else
				$response["action"]	=	"";
			if($noOfCondition!="")
				$response["noOfCondition"]	=$noOfCondition;
			else
				$response["noOfCondition"]	="";
			
			
			$signature = getMsgSignature($response["qcode"], $response["quesType"],$response["correctAnswer"],$response["dropdownAns"],$response["clusterCode"],$response["hintAvailable"],$response["noOfTrials"],$response["quesCategory"]);
			$response["signature"] = $signature;
			//------End
		}
		if($_SESSION['validToken'])
			$response["validToken"]	= $_SESSION['validToken'];
		else
			$response["validToken"]	= "";
		//$response	=	array();
		
		return json_encode($response);
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
} //end function

function checkImage($str) {

    $baseurl = IMAGES_FOLDER;
	$preloadStr = '';

    $pattern = array();
    $pattern[0] = "/\[([a-z0-9_ -\.]*[\.][a-z]*)\]/i";
    $pattern[1] = "/\[([a-z0-9_ -\s]*[\.][a-z]*),([0-9]*)\]/i";
    $pattern[2] = "/\[([a-z0-9_ -\s]*[\.][a-z]*),([0-9]*),([0-9]*)\]/i";

    for($j=0; $j<count($pattern); $j++)
    {
        $matches = array();
        preg_match_all($pattern[$j], $str, $matches, PREG_SET_ORDER);
        $cnt_matches = count($matches);
        if($cnt_matches>0) {
            for($i=0 ; $i<$cnt_matches; $i++)
            {
            	// ignore html files from preloading images stack
            	if(end(explode(".", $matches[$i][1])) != "html") {
	                $url = $baseurl;
	                $imgName = $matches[$i][1];
	                if($imgName[3]=="_")
	                {
	                    $folder = substr($imgName,0,3);
	                    //if(is_dir("images/".$folder))
	                    $url = $baseurl."/$folder";
	                }
	            	$imgName = $matches[$i][1];
	                $preloadStr .= $url."/".$imgName.",";
	            }
            }
        }
    }
    return  $preloadStr;
}

function checkIfUnlive($qcode)
{
	$sq	=	"SELECT qcode FROM adepts_quesUnlive WHERE qcode='$qcode'";
	$rs	=	mysql_query($sq) or die("$sq".mysql_error());
	return mysql_num_rows($rs);
}

function getMsgSignature($qcode,$quesType,$correctAnswer,$dropdownAns,$clusterCode,$hintAvailable,$noOfTrials,$quesCategory)
{
	$str = $qcode."-".$quesType."-".$clusterCode."-".$hintAvailable."-".$noOfTrials."-".$quesCategory;//$dropdownAns."-".$correctAnswer
	return hash_hmac('md5', $str, 'mindspark_2013');
}
?>