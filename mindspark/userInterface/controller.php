<?php
set_time_limit(0);
include("check1.php");
include("classes/kst_util.php");
include("functions/next_cluster.php");
include_once("functions/next_question.php");
include("classes/clsUser.php");
include("classes/clsSession.php");
include("functions/topic_revision_functions.php");
include("classes/clsQuestion.php");
include("classes/clsdaTest.php");
include("classes/clsNCERTQuestion.php");
include("classes/clsResearchQuestion.php");
include("classes/clsSbaQuestion.php");
include("classes/clsDiagnosticTestQuestion.php");
include_once("functions/orig2htm.php");
include("constants.php");
include_once("classes/clsTeacherTopic.php");
include_once("classes/clsProgressCalculation.php");
include_once("classes/clsRewardSystem.php");
include_once("classes/clsWorksheetQuestion.php");
include("functions/functions.php");
include("functions/comprehensiveModuleFunction.php");
include_once("functions/functionsForDynamicQues.php");
include_once("functions/sbaTestFunctions.php");
include_once("functions/examCornerFunction.php");
include_once("errorLog.php");

//for offline
$flagForOffline = false;
if($_SESSION['isOffline'] === true && ($_SESSION['offlineStatus']==1 || $_SESSION['offlineStatus']==2))
{
	$flagForOffline = true;
}
if($flagForOffline)
	include("../teacherInterface/logDeleteQuery.php");
error_reporting(E_ERROR | E_CORE_WARNING);


if(!isset($_SESSION['userID']) && isset($_POST))
{
	header("Location:error.php");
	exit();
}
else if(!isset($_SESSION['userID']) && !isset($_POST))
{
	header("Location:logout.php");
	exit;
}

$mode = isset($_REQUEST["mode"])?$_REQUEST["mode"]:"";
$userID = $_SESSION['userID'];
//echo $mode;die;
switch ($mode)
{
    case "createSession":
        $startTime = date("Y-m-d H:i:s");
        $objSession = new session($userID);

        $sessionID = $objSession->createSession(TBL_SESSION_STATUS, $startTime);
        $_SESSION["sessionID"] = $sessionID;
        $_SESSION["sessionStartTime"] = $startTime;
        $_SESSION["wildcardSession"] = 1;
		$_SESSION["rewardSystem"] = 0;
		$_SESSION["videoSystem"] = 0;
		if($_SESSION['schoolCode']==123456789)
			$_SESSION["videoSystem"] = 1;
		if($_SESSION['childClass']>3)
			$_SESSION["rewardSystem"] = 1;
		$_SESSION['progressBarFlag'] = 1;

        $path = "browser_detect.php?image1=$image1&image2=$image2";
		$windowName=$_SESSION['windowName'];
        echo '<html><body>';
        echo '<form name="frmBrowser" id="frmBrowser" method="POST" action="' . $path . '">';
        echo '<input type="hidden" name="sessionID" id="sessionID" value="' . $sessionID . '">';
        echo "</form><script>sessionStorage.setItem('windowName','$windowName');   document.frmBrowser.submit();</script></body></html>";
		break;
	case "saveBrowser":
		$sessionID = $_SESSION["sessionID"];
		$objSession = new session($userID, $sessionID);
		if (isset($_REQUEST['browser'])) {
            $objSession->updateBrowserDetails($_REQUEST['browser']);
			$flashContent = 1;
			//Duplicate login check
			if(!isset($_REQUEST['forceSave']))
			{
				if (isset($_REQUEST['hasFlash']) && $_REQUEST['hasFlash'] == "false")
					$flashContent = 0;
				$_SESSION['flashContent'] = $flashContent;
				$duplicateSessionID = $objSession->checkDuplicateLogin($_SESSION['timePerDay'],$_REQUEST['browser']);
				if ($duplicateSessionID != -1) {
					$path = "removeOtherSession.php?image1=$image1&image2=$image2";
					$sessionID = $duplicateSessionID;
				}
				else
				{
					$path = "controller.php?mode=login";
				}
				echo '<html><body>';
				echo '<form name="frmBrowser" id="frmBrowser" method="POST" action="' . $path . '">';
				echo '<input type="hidden" name="sessionID" id="sessionID" value="' . $sessionID . '">';
				echo '</form><script>document.frmBrowser.submit();</script></body></html>';
				break;
			}
			else
			{
				if(isset($_REQUEST['blockMindspark']))
				{
					$_SESSION['blockMindspark']	=	1;
				}
			}
		}
		break;
    case "login":
        //$userID    = $_SESSION["userID"];
        $sessionID = $_SESSION["sessionID"];
        $schoolCode = $_SESSION["schoolCode"];
        $objUser = new User($userID);
        $objSession = new session($userID, $sessionID);
		
			if($_SESSION['rewardSystem']==1)
			{
				$objReward	=	new Sparkies($userID);
				$_SESSION["arrayPrompts"]	=	$objReward->checkForSparkieAlertsAtStart();
				$_SESSION["rewardTheme"] = $objReward->rewardTheme;
			}

	    $nextPage = "home.php";
	    $qcode = "";
	    //School Admin -All category user is made for the dev/ei team to access the teacher interface of any school
	    //For this get the school code for which the data needs to be viewed.
	    if($objUser->category=="School Admin" && $objUser->subcategory=="All")
			{
				//begin: added by Jayanth for new teacherInterface
				//$interfacePreference = getTeacherInterfacePreference($userID);
				//if(strcasecmp($interfacePreference, "OLD") == 0) {
				//  $nextPage = "../teacherInterface_old/getSchoolDetails.php";
				//} else {
				  $nextPage = "../teacherInterface/getSchoolDetails.php";
				//}
			}
			elseif(strcasecmp($objUser->category,"Teacher")==0 || $objUser->category=="School Admin" || $objUser->category=="Home Center Admin")
			{

				//$interfacePreference = getTeacherInterfacePreference($userID);
				//if(strcasecmp($interfacePreference, "OLD") == 0) {
				//  $nextPage = "../teacherInterface_old/home.php";
				//} else {
				  $nextPage = "../teacherInterface/home.php";
				//}
				$flag = 0;
				if (SUBJECTNO == 2)
					$flag = $objUser->checkForFeedback();
	    		if ($flag != 0) {
					//if(strcasecmp($interfacePreference, "OLD") == 0) {
					//    $nextPage = "../teacherInterface_old/feedBackForm.php";
					//} else {
					    $nextPage = "../teacherInterface/feedBackForm.php";
					//}
				}
				//end: added by Jayanth for new teacherInterface

      			if(SUBJECTNO==2 && $objUser->currentOrdertype == "pilot")
				{
					if($objUser->checkForFeedback()!=0)
					{
						$nextPage = "../feedbackform.php";
					}
				}
			}        //Check if birth date not set for the user, if so redirect to the birth date page
        	//Ignore the check for class 1 & 2
        	else if (((strcasecmp($objUser->category, "STUDENT") == 0 && $objUser->childClass > 2) || strcasecmp($objUser->category, "STUDENT") != 0) && ($objUser->childDob == "" || $objUser->childDob == "00-00-0000")) { 
            	$nextPage = "getBirthDate.php";
        	} else {
            if (!isset($_REQUEST['bypass'])) { //If any comments have been responded by EI and yet to be seen by the user, direct him to the comment response screen
                $comment_query = "SELECT count(srno) FROM adepts_userComments WHERE userID=$userID AND status='Closed' AND viewed=0";
                $comment_result = mysql_query($comment_query) or die("<br>Error in comment query - " . mysql_error());
                $comment_data = mysql_fetch_array($comment_result);
                if ($comment_data[0] > 0)
                    $nextPage = "viewComments.php";
            }
            /*if (strcasecmp($objUser->category, "STUDENT") == 0 && strcasecmp($objUser->subcategory, "School") == 0 && ($objUser->childClass == 1 || $objUser->childClass == 2) && $nextPage != "viewComments.php") {
                $daysLoggedIn = $objUser->getNoOfDaysLoggedIn();
                if ($daysLoggedIn <= 7 && !isset($_POST['activityFinished']) && $_SESSION['flashContent']==1) {
                    $nextPage = "mouseactivity.php";
                }
            }*/ //commented till mouse activity converted into html5
            $flag = 0;
            if (SUBJECTNO == 2)
                $flag = $objUser->checkForFeedback();
            if ($flag != 0) {
                $nextPage = "feedBackForm.php";
            }

            $_SESSION['timeSpentToday'] = getTimeSpent($userID);
            $limitExceeded = hasExceededTodaysLimit($userID);
            if ($limitExceeded != 0) {
				$_SESSION["limitExceeded"] = $limitExceeded;
                $objSession->setEndTime($limitExceeded);
                //code for log off
                $nextPage = "endSessionReport.php?mode=$limitExceeded";
            }
            //Monthly revision session currently only applicable for Maths
            else if (SUBJECTNO == 2 && strcasecmp($objUser->category, "STUDENT") == 0 && strcasecmp($objUser->subcategory, "School") == 0) {
				
				//for SBA test
				$sbaTestMode	=	checkSbaAllowed($objUser->schoolCode,$objUser->childClass,$objUser->childSection,$objUser->userID);
				if($sbaTestMode=="")
				{
					$daysLeftInSbaStr	=	getDaysLeftInSbaTest($objUser->schoolCode,$objUser->childClass,$objUser->childSection,$objUser->userID);
					$daysLeftInSbaArr	=	explode("~",$daysLeftInSbaStr);
					$daysLeftInSba	=	$daysLeftInSbaArr[0];
					if($daysLeftInSba != -100)
					{
						if($daysLeftInSba <= 0)
						{
							$sbaTestMode	=	"sbaTest";
						}
					}
				}
				if($sbaTestMode!="")
				{
					echo "<html><body>";
					if($sbaTestMode=="sbaTestStarted")
					{
						echo '<form id="frmHidForm" action="controller.php" method="post">';
						echo '<input type="hidden" name="testMode" id="testMode" value="testPending">';
					}
					else
					{
						$arrayTestDetails	=	getTestDetails($_SESSION['sbaTestID']);
						echo '<form id="frmHidForm" action="sbaInstruction.php" method="post">';
						echo '<input type="hidden" name="totalTime" value="'.$arrayTestDetails[0].'">';
						echo '<input type="hidden" name="totalQues" value="'.$arrayTestDetails[1].'">';
					}
					echo '<input type="hidden" name="mode" id="mode" value="sbaTest">';
					echo '</form>';
					echo "<script>document.getElementById('frmHidForm').submit();</script>";
					echo "</body></html>";
					mysql_close();
					exit;
				}
				//for SBA test----ends here
				$revisionSessionID = checkPendingRevisionSession($userID, $objUser->schoolCode, $objUser->childClass, $objUser->childSection);
                if ($revisionSessionID != "") {
                    $dateArray = getRevisionSessionStartDate($objUser->schoolCode, $objUser->childClass, $objUser->childSection, $revisionSessionID);
                    $dateArray = explode("~", $dateArray);
                    $startDate = $dateArray[0];
                    $endDate = $dateArray[1];
                    populateRevisionClusters($userID, $revisionSessionID, $startDate, $endDate);

                    $tmpStr = getRevisionSessionQuestionsAttempted($userID, $revisionSessionID);
                    $tmpStr = explode("~", $tmpStr);
                    $qno = $tmpStr[0];
                    $prevTT = $tmpStr[1];
                    $prevCluster = $tmpStr[2];
                    $qcodeDetails = getNextQuestionForRevisionSession($userID, $revisionSessionID, $prevTT, $prevCluster);
                    $qcode = $qcodeDetails[0];
                    if ($qcode != "") {
                        echo "<html><body>";
                        echo '<form id="frmHidForm" action="revisionSessionInstructions.php" method="post">';

                        echo '<input type="hidden" name="qcode" id="qcode" value="' . $qcode . '">';
                        echo '<input type="hidden" name="qno" id="qno" value="' . $qno . '">';
                        echo '<input type="hidden" name="sdl" id="sdl" value="' . $qcodeDetails[3] . '">';
                        echo '<input type="hidden" name="clusterCode" id="clusterCode" value="' . $qcodeDetails[1] . '">';
                        echo '<input type="hidden" name="ttCode" id="ttCode" value="' . $qcodeDetails[2] . '">';
                        echo '<input type="hidden" name="revisionSessionID" id="revisionSessionID" value="' . $revisionSessionID . '">';
                        echo '</form>';
                        echo "<script>document.getElementById('frmHidForm').submit();</script>";
                        echo "</body></html>";
						mysql_close();
                        exit;
                    }
                    else
                        updateRevisionSessionStatus($userID, $revisionSessionID);
                }
            }
            $qcode = loadDetails($userID, $objUser->childClass);    //Most probably not needed, confirm and remove
        }


        echo '<html>';
        echo '<body>';
        echo 'Loading...';
        echo '<form id="frmHidForm" action="' . $nextPage . '" method="post">';
        echo '<input type="hidden" name="mode" value="login">';
        echo '<input type="hidden" name="qcode" id="qcode" value="' . $qcode . '">';
        echo '</form>';
        echo "<script>
					document.getElementById('frmHidForm').submit();
				  </script>";
        echo '</body>';
        echo '</html>';
        break;

	case "getpractisetest":
					$items = explode(",",$_POST['QCode']);
					$qcode	=	$items[array_rand($items)];
					$practiceid	=	$_POST['practiceid'];
					$showAnswer  = 1;
					$_SESSION['startTime']                    = date("Y-m-d H:i:s");
                    $_SESSION['endTime']                      = "";
                    $_SESSION['pageStartTime']                = 0;

					$sql = "INSERT INTO practiceTestStatus (userID, practiceid, status, lastmodified) VALUES ('".$_SESSION['userID']."', $practiceid , 1, '".date("Y-m-d H:i:s")."')";
						mysql_query($sql) or die(mysql_error().$sql);
						$practiseid = mysql_insert_id();
						$_SESSION["practiseid"] = $practiseid;
						$_SESSION["practiseqcodes"] = $items;
					  echo '<html>';
                    echo '<body>';
                    echo 'Loading...';
					echo '<form id="frmHidForm" action="question.php" method="post">';
						echo '<input type="hidden" name="qcode" id="qcode" value="'.$qcode.'">';
  						echo '<input type="hidden" name="qno" id="qno" value="1">';
  						echo '<input type="hidden" name="quesCategory" id="quesCategory" value="practiseTest">';
	                   echo '<input type="hidden" name="showAnswer" id="showAnswer" value="'.$showAnswer.'">';
	                    echo "<script>
		                           document.getElementById('frmHidForm').submit();
		                      </script>";
							   echo '</body>';
                    echo '</html>';
						break;
		case "endpractisetestsession":
					$action	=	$_POST['action'];
					if($action != 'continue')
					{
						$sql = "update practiceTestStatus set status = 2 where practiceattemptID = ".$_SESSION['practiseid']." and userID = $userID";
						mysql_query($sql) or die(mysql_error().$sql);
					}
					break;
		case "endWorksheet":
					$qno	=	$_POST['qno'];
					$time	=	$_POST['time'];
					$worksheetAttemptID = $_SESSION['worksheetAttemptID'];
					$checkSq = "SELECT COUNT(id), SUM(IF(RW=1,1,0)) FROM worksheet_attempt_detail WHERE userID = $userID AND ws_srno = '$worksheetAttemptID'";
					$result	=	mysql_query($checkSq);
					$line = mysql_fetch_array($result);
					$accuracy = $line[0]==0?0:round($line[1]*100/$line[0],2);
					$sql = "update worksheet_attempt_status set last_attempted_que = '$qno' , status = 'completed' , accuracy = '$accuracy'  where userID = $userID and srno = '$worksheetAttemptID' ";				
					mysql_query($sql) or die(mysql_error().$sql);
					$_SESSION["noOfJumps"] =    $_SESSION["noOfJumps"] + 10;
					$_SESSION['sparkie']['worksheetCompletion'] += 10;
					$query = "UPDATE ".TBL_SESSION_STATUS." SET noOfJumps = (noOfJumps + 10) WHERE sessionID=".$_SESSION['sessionID'];				
					mysql_query($query);
					break;
		case "endDaTest":
						$qno	=	$_POST['qno'];
						$time	=	$_POST['time'];
						$status	=	$_POST['status']; 
						$response	=	$_POST['response'];
						$daPaperCode = $_SESSION['daPaperCode'];
						$checkSq = "SELECT A FROM da_questionAttemptDetails WHERE userID = $userID AND qno = $qno and paperCode = '$daPaperCode' ";
						$result	=	mysql_query($checkSq);
						$resultCount = mysql_num_rows($result);
						$daPaperCode = $_SESSION['daPaperCode'];
						if($resultCount > 0)
						{
							if(empty($response))
							{
								$response = 'No Answer';
								$updateSql = "update da_questionAttemptDetails set A = '$response', R = 0 where userID = $userID and qno = $qno and paperCode = '$daPaperCode'";
							}
							else
								$updateSql = "update da_questionAttemptDetails set A = '$response' where userID = $userID and qno = $qno and paperCode = '$daPaperCode'";

							mysql_query($updateSql) or die(mysql_error().$updateSql);
						}
						$sql = "update da_questionTestStatus set status = $status , lastAttemptQue = if(lastAttemptQue >= $qno,lastAttemptQue,$qno)  where userID = $userID and paperCode = '$daPaperCode' ";
						mysql_query($sql) or die(mysql_error().$sql);
                        if($status=="3")    {    //send super test report to parent on completion of the test.
                               include("daTestMailer.php");
                               mailSuperTestReport($userID, $daPaperCode);
                        }
					break;
		case "saveDaFlag":
					$qno	=	$_POST['qno'];
					$qcode	=	$_POST['qcode'];
					$ans	=	$_POST['ans'];
					$sessionID = $_SESSION["sessionID"];
					$responseResult = $_POST['result'];
					$isflag  = $_POST['isflag'];
					//$daPaperCode =	$_POST['daPaperCode'];
					$daPaperCode = $_SESSION['daPaperCode'];
					nextDATestQuestion($userID,$sessionID,$subjectNo,$qcode,$ans,$qno,0,$responseResult,$dynamic=0,$dynamicParams="",$eeresponse="NO_EE",$daPaperCode);
					$checkSq = "SELECT * FROM da_questionAttemptDetails WHERE userID = $userID AND qno = $qno AND qcode =  $qcode and paperCode = '$daPaperCode'";
					$result	=	mysql_query($checkSq);
					$resultCount = mysql_num_rows($result);

					if($resultCount == 0)
					{
						$sq	= "INSERT INTO da_questionAttemptDetails (userID, attemptdate, qcode, qno, A, S, R, sessionID,paperCode, lastmodified,isFlag) VALUES ($userID,'".date("Y-m-d")."',$qcode,$qno,'$ans',5,1,$sessionID,'$daPaperCode','".date("Y-m-d H:i:s")."',1)";
					}
					else
					{
						$sq	= "UPDATE da_questionAttemptDetails SET isFlag = $isflag WHERE userID = $userID AND qno = $qno AND qcode =  $qcode and paperCode = '$daPaperCode' ";
					}
					mysql_query($sq) or die(mysql_error().$sq);
					break;

	case "saveDaLastAnswer":
					$qno	=	$_POST['qno'];
					$qcode	=	$_POST['qcode'];
					$ans	=	$_POST['ans'];
					$sessionID = $_SESSION["sessionID"];
					$responseResult = $_POST['result'];
					//$daPaperCode =	$_POST['daPaperCode'];
					$daPaperCode = $_SESSION['daPaperCode'];
					$subjectNo = 2;
					nextDATestQuestion($userID,$sessionID,$subjectNo,$qcode,$ans,$qno,0,$responseResult,$dynamic=0,$dynamicParams="",$eeresponse="NO_EE",$daPaperCode);
					break;
    case "nextAction":
                    $_SESSION['startTime']                    = date("Y-m-d H:i:s");
                    $_SESSION['endTime']                      = "";
                    $_SESSION['pageStartTime']                = 0;
                    echo '<html>';
                    echo '<body>';
					echo 'Loading...';
					
                    $objUser = new User($userID);
                    $qcode = loadDetails($userID, $objUser->childClass);
					$ttAttemptID  = $_SESSION['teacherTopicAttemptID'];
                	$userID = $_SESSION['userID'];
                	$sessionID = $_SESSION['sessionID'];
                    if($qcode==-1)
                    {
                        setSessionVariables();
                    }

                    $sessionID = $_SESSION["sessionID"];
                    $pendingTimedTest = isset($_POST['pendingTimeTest'])?$_POST['pendingTimeTest']:"";
					//---pending timed test of a topic on clicking a topic
					$pendingTopicTimedTest = isset($_POST['pendingTopicTimedTest'])?$_POST['pendingTopicTimedTest']:"";
					if(isset($_POST["timedTestCode"]) && $_POST["timedTestCode"]!="")
						$_SESSION['timedTest']	=	$_POST["timedTestCode"];

                    //Check if post test of any other topic is pending for completion, if so give that first. This will be set from the home page/topic selection page
					if(SUBJECTNO==2 && ($pendingTimedTest=="yes" || $pendingTopicTimedTest=="yes"))
	                {
	                	$quesCategory = "normal";
	                    echo '<form id="frmHidForm" action="timedTest.php" method="post">';
	                    echo '<input type="hidden" name="qcode" id="qcode" value="'.$qcode.'">';
	                    echo '<input type="hidden" name="qno" id="qno" value="1">';
	                    echo '<input type="hidden" name="quesCategory" id="quesCategory" value="'.$quesCategory.'">';
	                    echo '<input type="hidden" name="showAnswer" id="showAnswer" value="'.$showAnswer.'">';
	                    echo '</form>';
	                    echo "<script>
	                      		document.getElementById('frmHidForm').submit();
	                          </script>";
						$_SESSION["wildcardSession"]=2;
						mysql_close();
	                    exit;
	                }
					else
					{
						$_SESSION["wildcardSession"]=1;
					}
	                if($_SESSION['remedialMode']==1)
                    {
                    	echo '<form id="frmHidForm" action="remedialItem.php" method="post">';
                    	echo '<input type="hidden" name="qcode" value="'.$qcode.'">';
	                    echo '</form>';
	                    echo "<script>
	                              document.getElementById('frmHidForm').submit();
	                          </script>";
						mysql_close();
	                    exit;
                    }
                    if($_SESSION['game'] == true)
                    {
                        echo '<form id="frmHidForm" action="controller.php" method="post">';
                    	echo '<input type="hidden" name="mode" value="game">';
	                    echo '</form>';
	                    echo "<script>
	                              document.getElementById('frmHidForm').submit();
	                          </script>";
							  mysql_close();
	                    exit;
                    }
	                if($qcode == -8)
                    {
						$_SESSION['current_cluster'] = get_current_cluster($teacherTopicCode,$userID);
						$featureType = $_SESSION['kstdiagnosticTest'] = checkForKstDiagnosticTest($teacherTopicCode,$schoolCode,$childClass);
                    	if(preg_match('(with pre-requisite)', strtolower($featureType['featureType'])) === 1){
							redirectToKstFlow();
						} else {
							redirectToFlow();
						}
						if($_SESSION['comprehensiveModule']!="" || $_SESSION['subModule']!="")
							break;
                    	$_SESSION['fromTTSelection']=1;
	                    echo '<form id="frmHidForm" action="classLevelCompletion.php" method="post">';
	                    echo '</form>';
	                    echo "<script>
	                              document.getElementById('frmHidForm').submit();
	                          </script>";
							  mysql_close();
	                    exit;
                    }
                    $showAnswer  = 1;
                    if($qcode!=-1)
                    {
						$_SESSION['current_cluster'] = get_current_cluster($teacherTopicCode,$userID);
						$featureType = $_SESSION['kstdiagnosticTest'] = checkForKstDiagnosticTest($teacherTopicCode,$schoolCode,$childClass);
						if(preg_match('(with pre-requisite)', strtolower($featureType['featureType'])) === 1){
							redirectToKstFlow();
						} else {
							redirectToFlow();
						}
						if($_SESSION['comprehensiveModule']!="" || $_SESSION['subModule']!="")
							break;
						if(count($_SESSION['kstdiagnosticTest'])!=0){
							$kst_util_obj = new kst_util();
							$_SESSION['attemptData'] = $kst_util_obj -> getUserAttemptData($userID,$ttAttemptID);
							//check if additional test is going on or normal.
							/* SETTING attemptData into SESSION variable or 0 if not present from DB */
							$qcode = 0;
							$quesCategory = 'kstdiagnosticTest';
							$nextPage = "question.php";
						} else {
							$quesCategory = "normal";
						}
                        $commonInstructionSDL = $_SESSION['commonInstruction'];
					    if($commonInstructionSDL!="")
					    {
					    	$currentSDL	= $_SESSION["currentSDL"];
						    $CISdlArray = explode(",",$commonInstructionSDL);
						    if(in_array($currentSDL,$CISdlArray))
						    {
								if($_SESSION['groupInstructionType'] == 'groupInstruction')
									$nextPage = "group_instruction.php";
								elseif($_SESSION['groupInstructionType'] == 'gamesMaster')
								{
									$clusterCode = $_SESSION['clusterCode'];
									$linkedToSDL = $currentSDL;
									$sql = "SELECT gameID FROM adepts_gamesMaster WHERE linkedToCluster='$clusterCode' and linkedToSDL=$linkedToSDL AND live='Live' AND type='introduction' AND ver NOT IN ('as2','as3')";
									$result = mysql_query($sql) or die($sql);

									if($line = mysql_fetch_array($result))
									$_SESSION['gameID'] = $line['gameID'];
									echo '<form id="enrichmentModuleForm" action="enrichmentModule.php" method="post">';
									echo '<input type="hidden" name="gameMode" id="gameMode" value="groupInstruction">';
									echo '</form>';
									echo "<script>
												document.getElementById('enrichmentModuleForm').submit();
										  </script>";
									 exit;
								}
						    }
					    }
					    else
					    {
                        	$nextPage = "question.php";
						}
						if($_SESSION["videoSystem"] == 1)
						{
							$introVidsSDL = $_SESSION['introVids'];
							if($introVidsSDL!="")
						    {
						    	$currentSDL	= $_SESSION["currentSDL"];
							    $CISdlArray = explode(",",$introVidsSDL);
							    if(in_array($currentSDL,$CISdlArray))
							    {
									if(videoViewedPreviously($_SESSION["clusterCode"],$_SESSION["currentSDL"]) == 0)
									{
										$nextPage = "introductionVideos.php";
									}
							    }
						    }
						    else
						    {
	                        	$nextPage = "question.php";
						    }
						}
						/* wildcard question elligibility */
						if(checkForWildcard($userID,$sessionID))
						{
							if($_SESSION["wildcardSession"]==1)
							{
								$_SESSION['wildcardAtStart'] = array();
								$_SESSION['wildcardAtStart']	=	wildCardQuesStart($schoolCode,$childClass,$userID);
								$_SESSION['wildcardAtRand']	=	wildCardQuesRand($schoolCode,$childClass,$userID,$teacherTopicCode,$_SESSION['wildcardAtStart']);
								$_SESSION["wildcardSession"]	=	0;
							}
							else if($_SESSION["wildcardSession"]==2)
							{
								$_SESSION['wildcardAtStart']	=	"";
								$_SESSION['wildcardAtRand']	=	wildCardQuesRand($schoolCode,$childClass,$userID,$teacherTopicCode,0);
								$_SESSION["wildcardSession"]	=	0;
							}
						}
						else
						{
							$_SESSION['wildcardAtRand']		=	"";
							$_SESSION['wildcardAtStart']	=	"";
						}
						if($_SESSION['wildcardAtStart']!="" && $quesCategory=="normal" && $qcode>0 && $nextPage=="question.php")
							$quesCategory = "wildcard";
						echo '<form id="frmHidForm" action="'.$nextPage.'" method="post">';
						echo '<input type="hidden" name="qcode" id="qcode" value="'.$qcode.'">';
  						echo '<input type="hidden" name="qno" id="qno" value="1">';
  						echo '<input type="hidden" name="quesCategory" id="quesCategory" value="'.$quesCategory.'">';
	                    echo '<input type="hidden" name="showAnswer" id="showAnswer" value="'.$showAnswer.'">';
	                    echo "<script>
		                           document.getElementById('frmHidForm').submit();
		                      </script>";
							  mysql_close();
                        exit;
                    }
                    else
                    {
                        echo '<form id="frmHidForm" action="dashboard.php" method="post">';
                        echo '</form>';
                        echo "<script>
                                document.getElementById('frmHidForm').submit();
                              </script>";
							  mysql_close();
                        exit;
                    }
                    echo '</body>';
                    echo '</html>';
                    break;
			case "firstQuestion":
						if($_SESSION['progressBarFlag'] && $quesCategory!="diagnosticTest") {
							$_SESSION['clusterStatusPrompt'] = 0;
						}
						if(isset($_POST['quesCategory']) && $_POST['quesCategory']=="practiseModule")
						{
							$quesCategory = $_POST['quesCategory'];
							$practiseModuleTestStatusId = $_POST['practiseModuleTestStatusId'];
							$dailyDrillSession = $_SESSION['dailyDrillArray'];
							$attemptNo = $_SESSION['dailyDrillArray']['attemptNo'];
							$practiseModuleId = $_SESSION['dailyDrillArray']['practiseModuleId'];
							$currentLevel = $_SESSION['dailyDrillArray']['currentLevel'];
							$currentScore = $_SESSION['dailyDrillArray']['currentScore'];
							$isInternalRequest = $_SESSION['dailyDrillArray']['isInternalRequest'];
							$sessionID=$_SESSION['sessionID'];
							$nextPractiseModQuestion=nextPractiseModuleQuestion("","","","","","",$sessionID,0,0,$userID,$currentLevel,$attemptNo,$practiseModuleId,$practiseModuleTestStatusId,"");
							$nextPractiseElement=explode("~",$nextPractiseModQuestion);
							$quesno=(isset($nextPractiseElement[3])?$nextPractiseElement[3]+1:1);
							$_SESSION['dailyDrillArray']['currentLevel']=$nextPractiseElement[2];
							$response = createResponse($nextPractiseElement[0],$quesno,$quesCategory,"1");
							echo $response;
							break;
						}
						if(isset($_POST['quesCategory']) && $_POST['quesCategory']=="worksheet")
						{
							$userid = $_SESSION['userID'];
							$quesCategory = $_POST['quesCategory'];
							$worksheetID = $_POST['worksheetID'];
							$worksheetAttemptID = $_POST['worksheetAttemptID'];
							$quesno = $_POST['qno'];
							$paperQueCount = $_POST['paperQueCount'];

							$schoolCode= $_SESSION['schoolCode'];
							$class = $_SESSION['childClass'];
							$section = $_SESSION['childSection'];
							if($_SESSION["userType"]!="msAsStudent" && $_SESSION["userType"]!="teacherAsStudent")
								$_SESSION["userType"] = (isset($_POST['userType']) && $_POST['userType'] != "")?$_POST['userType']:"";

							$worksheetDetail = getWorksheetDetail($worksheetID, $worksheetAttemptID, $class, $section, $schoolCode);
							//$worksheetName = $worksheetDetail['wsm_name'];
							$worksheetQList = $worksheetDetail['qList'];
							$worksheetQCount = $worksheetDetail['qcount'];
							$worksheetThumbnail = $worksheetDetail['thumbnail'];
							$worksheetDuration = $worksheetDetail['duration'];
							$worksheetEndDateTime = $worksheetDetail['end_datetime'];
							$worksheetAllQs = $worksheetDetail['allQs'];
							$worksheetCompletedQs = $worksheetDetail['completedQs'];
							$_SESSION['worksheetAttemptID'] = $worksheetAttemptID;
							$tmpQcodeLists = explode(",",$worksheetAllQs);
							$_SESSION["tmpQcodeLists"] = $tmpQcodeLists;
							$_SESSION["DAquetype"] = "true";
							$wsAnsweredArray = getWorksheetAnsweredArray();
							$_SESSION['wsAnsweredArray'] = $wsAnsweredArray;
								$qcode	=	$tmpQcodeLists[$quesno-1];
								$quesno	=	$quesno;
							$sql = "SELECT b.wsm_id, if(b.end_datetime<NOW() OR a.status='completed',1,0) timeUP 
								FROM worksheet_master b JOIN worksheet_attempt_status a ON a.wsm_id=b.wsm_id and a.userID=$userid and srno = '$worksheetAttemptID'";
							$rs	=	mysql_query($sql) or die(mysql_error().$sql);
							$line = mysql_fetch_array($rs);
							if ($line[1]==1) {$qcode=-12;$quesno=1;}
							$response = createResponse($qcode,$quesno,$quesCategory);
							echo $response;
							break;
						}
						if(isset($_POST['quesCategory']) && $_POST['quesCategory']=="daTest")
						{
							$userid = $_SESSION['userID'];
							$quesCategory = $_POST['quesCategory'];
							$paperQueCount = $_POST['paperQueCount'];
							$daTestCode = $_POST['daTestCode'];
							$checkAttempt = "select * from da_questionTestStatus where userID = $userid and paperCode = '$daTestCode' ";
							$rSet = mysql_query($checkAttempt);
							if(mysql_num_rows($rSet) == 0)
							{
								$sql = "INSERT INTO da_questionTestStatus (userID, status, spendTime, lastAttemptQue,paperCode, lastmodified) VALUES ($userid, 1,'1800',1,'$daTestCode', now())";
								mysql_query($sql) or die(mysql_error().$sql);
							}
							else{
								$rowset = mysql_fetch_array($rSet);
								$attemptDaqno = $rowset['lastAttemptQue'];
								$attemptDaFlag = 'true';
							}
							$DAsql = "SELECT * FROM educatio_educat.da_paperDetails where papercode = '$daTestCode' and version = 1";
							$resultSet = mysql_query($DAsql) or die(mysql_error().$DAsql);
							$rw = mysql_fetch_array($resultSet);
							$qcodeLists = $rw['qcode_list'];
							$tmpQcodeLists = explode(",",$qcodeLists);
							$_SESSION["tmpQcodeLists"] = $tmpQcodeLists;
							$_SESSION["DAquetype"] = "true";
							if($_POST['linkedquestion'] == 1)
							{
								$quesno	=	$_POST['qno'];
								$qcode	=	$_POST['qcode'];
							}
							elseif($attemptDaFlag == 'true')
							{
								$qcode	=	$tmpQcodeLists[$attemptDaqno-1];
								$quesno	=	$attemptDaqno;
							}
							else
							{
								$qcode	=	$tmpQcodeLists[0];
								$quesno	=	1;
							}
							$response = createResponse($qcode,$quesno,$quesCategory);
							echo $response;
							break;
						}
					/* DA TEST  */
					/* START SBA TEST */
					if(isset($_POST['quesCategory']) && $_POST['quesCategory']=="sba" && $_SESSION['sbaTestID']!="")
					{
						$qcode	=	$_POST['qcode'];
						$quesno	=	$_POST['qno'];
						$response = createResponse($qcode,$quesno,$quesCategory);
						echo $response;
    					break;
					}
                    /* START DIAGNOSTIC TEST */
					if(isset($_POST['quesCategory']) && $_POST['quesCategory']=="diagnosticTest" && $_SESSION['comprehensiveModule']!="")
					{
						diagnosticTestQuestions(1,$_SESSION['teacherTopicAttemptID']);
						$qcode = getDiagnosticQcode($_SESSION['diagnosticTest']);
						$response = createResponse($qcode,"1","diagnosticTest");
						echo $response;
    					break;
					}
					/* START KST DIAGNOSTIC TEST */
					if(isset($_POST['quesCategory']) && $_POST['quesCategory']=="kstdiagnosticTest")
					{
						
						$kst_util_obj = new kst_util();
						$mode='firstQuestion';
						//check if additional is going on or normal accordingly call api
						//echo $_SESSION['teacherTopicAttemptID'];die;
						$isPredicted = check_predictedQuestions_kst($_SESSION['userID'], $_SESSION['teacherTopicAttemptID']);
						if($isPredicted == 1 ){
							//this means there is predicted questions and the student has completed the api now we have to ask additional
							$_SESSION['isPredicted'] = 1;
						} else {
							$_SESSION['isPredicted'] = 0;
						}
						if(isset($_SESSION['isPredicted']) && $_SESSION['isPredicted'] == 0 ){
							$qInfo = $kst_util_obj -> getNextQuestionFromAPI($_SESSION['userID'],$_SESSION['teacherTopicCode'],$_SESSION['attemptData'],$mode);	//api here
							if (strpos($qInfo['questionCode'], 'done') !== false){
								//store the result of predicted questions in database
								//this will be in string. should convert to proper format
								$predicted_qcodeArray = $qInfo['questionCode'];
								$predicted_arr = predictedSet($predicted_qcodeArray);
								//conversion to proper format
								array_shift($predicted_arr);
								storePredictedSet($predicted_arr,$_SESSION['sessionID'],$_SESSION['userID'],$_SESSION['teacherTopicAttemptID']);
								//Store predicted list in DB
								$qInfo = getAdditionalQuestionsForKst($userID,$_SESSION['teacherTopicAttemptID'],$_SESSION['kstdiagnosticTest']['addtnlQues'],$_SESSION['schoolCode']);
							} else {
								$learningCode = $qInfo['questionCode'];
								$query = "SELECT qcode FROM educatio_adepts.learning_objective_qcode_cluster_mapping_Fractions WHERE learning_objective_qcode='$learningCode'";
								$rs	=	mysql_query($query) or die(mysql_error().$query);
								$row = mysql_fetch_array($rs, MYSQL_ASSOC);
								$qInfo['questionCode']	=	$row['qcode'];
							}
						} else {
							//call function to obtain additional questions and return that
							$qInfo = getAdditionalQuestionsForKst($userID,$_SESSION['teacherTopicAttemptID'],$_SESSION['kstdiagnosticTest']['addtnlQues'],$_SESSION['schoolCode']);
						}
						if($qInfo['questionCode'] == 'done'){
							$response	=	array();
							$response["tmpMode"]	=	"kstdiagnosticTestComplete";
							$response["testType"]	=	$_SESSION['kstdiagnosticTest']['featureType'];
							$response["alertText"]	=	$qInfo['alertText'];
							$_SESSION["qno"] = 1;
							echo json_encode($response);
						} else {
							$response = createResponse($qInfo['questionCode'],$_SESSION["qno"],"kstdiagnosticTest");
							echo $response;
						}
						break;
					}
					if(isset($_POST['quesCategory']) && $_POST['quesCategory']=="practiseTest")
					{
						$qcode	=	$_POST['qcode'];
						$response = createResponse($qcode,"1","practiseTest");
						echo $response;
    					break;
					}
					/* CREATE EI LOGIN ENTRY */
					if(isset($_SESSION["userType"]) && $_SESSION["userType"]=="msAsStudent")
					{
						$sql = "INSERT IGNORE INTO adepts_eiLoginData (userID, ttAttemptID, childClass) VALUES ('".$_SESSION['userID']."', '".$_SESSION['teacherTopicAttemptID']."', '".$_SESSION['childClass']."')";
						mysql_query($sql);
						$_SESSION['topicOwner']	=	getTopicQwner($_SESSION['teacherTopicCode'],$_SESSION["username"]);
					}
					/* FOR comprehensive module CLUSTER ATTEMPT*/
					if($_SESSION['comprehensiveCluster']==1)
					{
						$response = createResponse($_SESSION["qcode"],1,"normal");
						echo $response;
    					break;
					}
					if($_SESSION['subModuleCluster']==1)
					{
						$response = createResponse($_SESSION["qcode"],1,"normal");
						echo $response;
    					break;
					}
					/* FOR EXAM CORNER CLUSTER ATTEMPT*/
					if(isset($_SESSION['examCornerCluster']) && $_SESSION['examCornerCluster']==1)
					{
						if(isset($_POST['impQuesMode']) && $_POST['impQuesMode']=="1")
						{
							$_SESSION["allQuestionsArray"]	=	$_SESSION["importantQuestions"];
							getFirstImportantQuetion();
						}
						$response = createResponse($_SESSION["qcode"],1,"normal");
						echo $response;
    					break;
					}
					/* SET UPDATE FLAG - PROGRESS */
					$sql = "UPDATE ".TBL_CURRENT_STATUS." SET progressUpdate=1 WHERE userID='".$userID."' AND teacherTopicCode='".$_SESSION['teacherTopicCode']."'";
					$result = mysql_query($sql) or die($sql);
					//if($_SESSION["currentClusterType"] == "practice" || $_SESSION["teacherTopicCode"] == "NCERT")
					if($_SESSION["teacherTopicCode"] == "NCERT")
					{
						$qcode = implode("##",$_SESSION["allQuestionsArray"][round($_SESSION["currentSDL"],0)]);
					}
					else
					{
    					$qcode = $_SESSION["qcode"];
					}
    				if(isset($_POST['quesCategory']) && $_POST['quesCategory']!="")
    					$quesCategory = $_POST['quesCategory'];
    				else
						$quesCategory = 'normal';
					if($quesCategory=="topicRevision")	//For topic revision, start from qno 1 always
						$quesno = 1;
					elseif($quesCategory=="exercise" || $quesCategory=="NCERT")
						$quesno = $_REQUEST["qno"];
					else
						$quesno = $_SESSION["qno"];
					//Construct the response to be sent back to the question page
					$dctPendingStage = isPendingDCT($_SESSION['teacherTopicAttemptID']);
					if($dctPendingStage != false)
					{
						$qcode = -11;
						$tmpMode = $dctPendingStage;
						$quesCategory = "DCT";
                    	$response = createResponse($qcode,$quesno,$quesCategory,"0",$tmpMode);
					}
					else
					{
						$researchModulePending = isPendingResearchModule($_SESSION['teacherTopicAttemptID']);
						if($researchModulePending)
						{
							$qcode = -13;
							$tmpMode = "researchModule";
							$quesCategory = "researchModule";
							$response = createResponse($qcode,$quesno,$quesCategory,"0",$tmpMode);
						}
						else
						{
							if($quesCategory == "wildcard")
							{
								$qcode = $_SESSION['wildcardAtStart']['qcode'];
								$tmpMode = $_SESSION['wildcardAtStart']['type'];
								$displayText = $_SESSION['wildcardAtStart']['displayText'];
								$response = createResponse($qcode,$quesno,$quesCategory,"1",$tmpMode,0, $displayText);
								unset($_SESSION['wildcardAtStart']);
							}
							else
							{
								//check for pending comprehensive module flow
								if($_SESSION['comprehensiveModule']!="")
								{
									$comprehensiveModuleDetails	=	checkPendingComprehensiveModule($_SESSION['teacherTopicAttemptID']);									
									$comprehensiveModuleDetailsArr	=	explode("$",$comprehensiveModuleDetails);
									$_SESSION['comprehensiveModule']	=	$comprehensiveModuleDetailsArr[0];
									if($comprehensiveModuleDetailsArr[2]=="remedial")
									{
										$tmpMode = "remedial";
										$qcode	=	$comprehensiveModuleDetailsArr[1];
									}
									else if(strtolower($comprehensiveModuleDetailsArr[2])=="timedtest")
									{
										$tmpMode = "timedtest";
										$_SESSION["quesCategory"] = "comprehensive";
										$_SESSION['timedTest']	=	$comprehensiveModuleDetailsArr[1];
									}
									else if($comprehensiveModuleDetailsArr[2]=="activity")
									{
										$_SESSION['gameID']	=	$comprehensiveModuleDetailsArr[1];
										$qcode	=	-11;
										$tmpMode     = $_SESSION['gameID'];
									}
									else if(strtolower($comprehensiveModuleDetailsArr[2])=="diagnostic")
									{
										$_SESSION['diagnosticTest']	=	$comprehensiveModuleDetailsArr[1];
										$tmpMode = "diagnosticTest";
									}
									else if(strtolower($comprehensiveModuleDetailsArr[2])=="cluster")
									{
										$_SESSION['clusterCode']	=	$comprehensiveModuleDetailsArr[1];
										$_SESSION['comprehensiveCluster'] = 1;
										$qcode	=	getComphrensiveQcode($_SESSION['clusterCode']);
										$tmpMode     = "question";
										$curQuesType = "normal";
									}
									$response = createResponse($qcode,$quesno,$curQuesType,$showAnswer,$tmpMode);
									echo $response;
									break;
								} else if($_SESSION['subModule']!="")
								{
									$subModuleDetails	=	checkPendingSubModuleForKst($_SESSION['teacherTopicAttemptID']);									
									$subModuleDetailsArr	=	explode("$",$subModuleDetails);
									$_SESSION['subModule']	=	$subModuleDetailsArr[0];
									if($subModuleDetailsArr[1]=="remedial")
									{
										$tmpMode = "remedial";
										$qcode	=	$subModuleDetailsArr[0];
									}
									else if(strtolower($subModuleDetailsArr[1])=="timedtest")
									{
										$tmpMode = "timedtest";
										$_SESSION["quesCategory"] = "subModuleKst";
										$_SESSION['timedTest']	=	$subModuleDetailsArr[0];
									}
									else if($subModuleDetailsArr[1]=="activity")
									{
										$_SESSION['gameID']	=	$subModuleDetailsArr[0];
										$qcode	=	-11;
										$tmpMode     = $_SESSION['gameID'];
									}
									else if(strtolower($subModuleDetailsArr[1])=="cluster")
									{
										$_SESSION['clusterCode']	=	$subModuleDetailsArr[0];
										$_SESSION['subModuleCluster'] = 1;
										$qcode	=	getKstQcode($_SESSION['clusterCode']);
										$tmpMode     = "question";
										$curQuesType = "normal";
									}
									$response = createResponse($qcode,$quesno,$curQuesType,$showAnswer,$tmpMode);
									echo $response;
									break;
								}
								$choiceScreenFlagDT = isset($_SESSION['choiceScreenFlagDT']) && $_SESSION['choiceScreenFlagDT']!='' ?$_SESSION['choiceScreenFlagDT'] : 0;			
								$response = createResponse($qcode,$quesno,$quesCategory,"","",$choiceScreenFlagDT);
								unset($_SESSION['choiceScreenFlagDT']);
							}
						}
					}

                    echo $response;
    				break;

	case "ttSelection":
				    echo '<html>';
                    echo '<body>';
                    echo "Loading...";
                    $_SESSION['startTime'] = date("Y-m-d H:i:s");        //this will be used to calculate the load time for the next ques
                    $_SESSION['endTime']   = 0;
                    $_SESSION['pageStartTime'] = 0;
					$childClass = $_SESSION['childClass'];
                    $sessionID  = $_SESSION['sessionID'];
                    $teacherTopicCode = $_POST['ttCode'];
					$userID	=	$_SESSION['userID'];
					$schoolCode	=	$_SESSION['schoolCode'];
					$category = $_SESSION["admin"];
					$ttAttemptID = $_SESSION['teacherTopicAttemptID'];
					
					$_SESSION["quesAttemptedInAttempt"] = "";	// this variable is used for displaying sparkie prompt on topic repeat attempts
					/* unset if session is set */
					if($_SESSION['freeTrialTopics']==1)
					{
						$freeTrilaTopicArray = getTopicsForFreeTrial($childClass);
						if(!in_array($teacherTopicCode,$freeTrilaTopicArray))
						{
							echo "<script>alert('For attempting other Topics - Purchase Mindspark.');window.location.href='dashboard.php'</script>";
							echo '</body>';
							echo '</html>';
							break;
						}
					}

					if(isset($_SESSION['examCornerCluster']) && $_SESSION['examCornerCluster']==1)
					{
						unset($_SESSION['bucketAttemptID'],$_SESSION['bucketClusterCode'],$_SESSION['examCornerCluster'],$_SESSION['bucketTopicName']);
					}

					/* wildcard question elligibility */
					if(checkForWildcard($userID,$sessionID))
					{
						if($_SESSION["wildcardSession"]==1)
						{
							$_SESSION['wildcardAtStart'] = array();
							$_SESSION['wildcardAtStart']	=	wildCardQuesStart($schoolCode,$childClass,$userID);
							$_SESSION['wildcardAtRand']	=	wildCardQuesRand($schoolCode,$childClass,$userID,$teacherTopicCode,$_SESSION['wildcardAtStart']);
							$_SESSION["wildcardSession"]	=	0;
						}
						else if($_SESSION["wildcardSession"]==2)
						{
							$_SESSION['wildcardAtStart']	=	"";
							$_SESSION['wildcardAtRand']	=	wildCardQuesRand($schoolCode,$childClass,$userID,$teacherTopicCode,0);
							$_SESSION["wildcardSession"]	=	0;
						}
					}
					else
					{
						$_SESSION['wildcardAtRand']		=	"";
						$_SESSION['wildcardAtStart']	=	"";
					}
					/* wildcard timedtest */
					if($_SESSION['wildcardAtStart']!="" && $_SESSION['wildcardAtStart']["type"]=="timedtest")
					{
						$_SESSION["quesCategory"] = $quesCategory = "wildcard";
						$showAnswer	=	1;
						$_SESSION['timedTest']	=	$_SESSION['wildcardAtStart']["qcode"];
						echo '<form id="frmHidForm" action="timedTest.php" method="post">';
						echo '<input type="hidden" name="qcode" id="qcode" value="">';
						echo '<input type="hidden" name="qno" id="qno" value="1">';
						echo '<input type="hidden" name="quesCategory" id="quesCategory" value="'.$quesCategory.'">';
						echo '<input type="hidden" name="showAnswer" id="showAnswer" value="'.$showAnswer.'">';
						echo '</form>';
						echo "<script>
									document.getElementById('frmHidForm').submit();
							  </script>";
						$_SESSION['wildcardAtStart']="";
						$_SESSION["wildcardSession"]=0;
						mysql_close();
						exit;
					}
					/* wildcard timedtest */
					/* ends here */

					if(!isset($_POST['start']))
					{
						if($_SESSION["userType"]!="msAsStudent" && $_SESSION["userType"]!="teacherAsStudent")
							$_SESSION["userType"] = (isset($_POST['userType']) && $_POST['userType'] != "")?$_POST['userType']:"";
						$_SESSION["msAttemptMode"] = (isset($_POST['msAttemptMode']) && $_POST['msAttemptMode'] != "")?$_POST['msAttemptMode']:"";
						$_SESSION["forceNew"] = (isset($_POST['forceNew']) && $_POST['forceNew'] != "")?$_POST['forceNew']:"no";
						$_SESSION["forceFlow"] = (isset($_POST['forceFlow']) && $_POST['forceFlow'] != "")?$_POST['forceFlow']:"";
						$_SESSION["customClusterCode"] = (isset($_POST['customClusterCode']) && $_POST['customClusterCode'] != "")?$_POST['customClusterCode']:"";
						if($_SESSION["userType"]=="teacherAsStudent" || $_SESSION["userType"]=="msAsStudent")
							$_SESSION["theme"] = 0;

						if($_SESSION["userType"]=="teacherAsStudent" && ($_SESSION['admin']=="STUDENT" || $_SESSION['admin']=="GUEST"))
						{
							header("location:newTab.php");
						}

						if($_SESSION["userType"]=="teacherAsStudent" || (isset($_SESSION["userTypeS"]) && $_SESSION["userTypeS"]=="teacherAsStudent"))
						{
							$_SESSION["childClass"] = (isset($_POST['childClass']) && $_POST['childClass'] != "")?$_POST['childClass']:(isset($_SESSION["childClass"])?$_SESSION["childClass"]:"");
							$query="UPDATE educatio_educat.common_user_details SET class=".$_SESSION["childClass"]." WHERE MS_userID=".$_SESSION["userID"];
                            mysql_query($query);// or die($query. 'error in setting class for DMS'.mysql_error());
							$theme = 0;
							if($_SESSION["childClass"]>=1 && $_SESSION["childClass"]<=3)
								$theme = 1;
							else if($_SESSION["childClass"]>=4 && $_SESSION["childClass"]<=7)
								$theme = 2;
							else
								$theme = 3;
							$_SESSION["theme"] = $theme;
						}
						if($_SESSION["forceNew"] != "no")
							deleteCurrentStatus($userID,$teacherTopicCode,$flagForOffline);
					}
					if($category=='TEACHER' || $category=='School Admin')
					{
						if($_SESSION["userType"]!="msAsStudent" && $_SESSION["userType"]!="teacherAsStudent")
							header("location:newTab.php");
					}

                    if(isset($_POST['higherLevel']) && $_POST['higherLevel']==2)	//i.e. if the user choses to restart the same topic from the class level completion screen.
					{
						updateTeacherTopicStatus($_SESSION['teacherTopicAttemptID'],-4);
	                    deleteCurrentStatus($userID,$teacherTopicCode,$flagForOffline);
					}

                    if($_SESSION['teacherTopicCode']!=$teacherTopicCode)
                    {
                    	$_SESSION['teacherTopicCode'] = $teacherTopicCode;
                    	setInactive($_SESSION['teacherTopicAttemptID'], $userID);
                    }

					if(teacherTopicIncomplete($teacherTopicCode,$userID) && $_SESSION["forceNew"] == "no")
					{
						getFlowForTT($userID, $teacherTopicCode);
						activateTeacherTopic($teacherTopicCode, $userID);
						$quesno = $_SESSION['qno'];
						$qcode = loadDetails($userID, $childClass);
						//check kst here
						if($_POST['quesCategory'] == 'kstPostTest'){ //This checks for first time for post test.
							$quesCategory = "kstdiagnosticTest";
							$_SESSION['kstdiagnosticTestType'] = 'Assessment';
							$_SESSION['isPostTest'] = 1;
						} else {
							$kstdiagnosticTest = checkForKstDiagnosticTest($teacherTopicCode,$schoolCode,$childClass);
							if(count($kstdiagnosticTest) != 0 && $kstdiagnosticTest['isActive'] == 1){
								$kstTestType = getKstTestStatus($userID,$_SESSION['teacherTopicAttemptID'],$teacherTopicCode); //checks for pre-test status
								if($kstTestType == 0){
									$quesCategory = "kstdiagnosticTest";
									$_SESSION['kstdiagnosticTest'] = $kstdiagnosticTest;
									unset($_SESSION['isPostTest']);
									unset($_SESSION['kstdiagnosticTestType']);
								}
								else if($kstTestType==1){
									$checkPostTest = checkPostStatus($userID,$_SESSION['teacherTopicAttemptID'],$teacherTopicCode); //checks for post-test
									if($checkPostTest == 0){
										$quesCategory = "kstdiagnosticTest";
										$_SESSION['kstdiagnosticTest'] = $kstdiagnosticTest;
										$_SESSION['kstdiagnosticTestType'] = 'Assessment';
										$_SESSION['isPostTest'] = 1;
									} else {
										unset($_SESSION['isPostTest']);
										unset($_SESSION['kstdiagnosticTestType']);
										//Now we check if we have to give any submodules.
										$_SESSION['current_cluster'] = get_current_cluster($teacherTopicCode,$userID);
										//Goes inside this if the activity is game or timedTest.
										if( (isset($_POST['isActivity']) && $_POST['isActivity'] == 1) || (isset($_POST['isTimedTest']) && $_POST['isTimedTest'] == 1) ) {
											$sqTimeTaken	=	"SELECT flowAttemptID FROM educatio_adepts.kst_userFlowDetails WHERE srno=".$_SESSION['subModule_srno']." AND status=0 ORDER BY flowAttemptID LIMIT 1";
											$rsTimeTaken	=	mysql_query($sqTimeTaken);
											$rwTimeTaken	=	mysql_fetch_array($rsTimeTaken);
											if(isset($_POST['Tduration'])){
												$timeTaken = $_POST['Tduration'];
												$sqFinish	=	"UPDATE educatio_adepts.kst_userFlowDetails SET status = 1, timeTaken=$timeTaken  WHERE flowAttemptID=".$rwTimeTaken['flowAttemptID'];	
											} else {
												$sqFinish	=	"UPDATE educatio_adepts.kst_userFlowDetails SET status = 1  WHERE flowAttemptID=".$rwTimeTaken['flowAttemptID'];
											}
											$rsFinish	=	mysql_query($sqFinish);
											$sqSetCurrentActivityDetail	=	"UPDATE educatio_adepts.kst_ModuleAttempt SET currentActivityDetail='' WHERE srno=".$_SESSION['subModule_srno'];
											$rsSetCurrentActivityDetail	=	mysql_query($sqSetCurrentActivityDetail);
											$_SESSION["currentSDL"] = "";
											getNextKstSubModuleInflow();
											redirectToKstFlow();
										}
										$_SESSION['current_cluster'] = get_current_cluster($teacherTopicCode,$userID);
										$featureType = $_SESSION['kstdiagnosticTest'] = checkForKstDiagnosticTest($teacherTopicCode,$schoolCode,$childClass);
										if(preg_match('(with pre-requisite)', strtolower($featureType['featureType'])) === 1){
											redirectToKstFlow();
										}
									}
								}
							}
						}
					}
					else {
						$choiceSreenAvailable=checkIfChoiceScreenApplicable($teacherTopicCode,$userID,$childClass);
						if ($choiceSreenAvailable!=0 && !(isset($_POST['higherLevel']) && $_POST['higherLevel']==2)){
							//check for comprhensive module flow
							redirectToFlow();
							if($_SESSION['comprehensiveModule']!="")
								break;
							setTeacherTopic($teacherTopicCode);
							$_SESSION['teacherTopicAttemptID']=$choiceSreenAvailable;
							$_SESSION['teacherTopicCode']=$teacherTopicCode;
							$clusterQuery=mysql_query("SELECT clusterCode, clusterAttemptID FROM adepts_teacherTopicClusterStatus WHERE userID=$userID AND ttAttemptID=$choiceSreenAvailable ORDER BY clusterAttemptID DESC LIMIT 1");
							$clusterRes=mysql_fetch_assoc($clusterQuery);
							$_SESSION['clusterCode']=$clusterRes['clusterCode'];
							$_SESSION['fromTTSelection']=1;
							echo '<form id="classLevelCompletion" action="classLevelCompletion.php" method="post">';
							echo '</form>';
							echo "<script>
										document.getElementById('classLevelCompletion').submit();
								  </script>";
							 exit;
						}
						else
						{
							$_SESSION['timedTest']                      = "";
							$_SESSION['game']							= false;
							$_SESSION['quesCorrectInALevelOfTopic'] = 0;
							setTeacherTopic($teacherTopicCode); //sets a session variable as teacherTopicName
							$_SESSION["remedialStack"] = "";
							$_SESSION['remedialMode'] = 0;
							getTopicProgressDetails($userID, $teacherTopicCode); //sets progress in test so far from DB in a session variable
							//Change in progress topic for this user if any to inactive
							$query = "UPDATE ".TBL_CURRENT_STATUS." SET status=0 WHERE userID=$userID";
							mysql_query($query);
							if(isset($_SESSION['forceFlow']) && $_SESSION['forceFlow'] != "")
							{
								$flow = $_SESSION['forceFlow'];
								unset($_SESSION["forceFlow"]);
							}
							else
								$flow  = getFlowForTT($userID, $teacherTopicCode);
							$_SESSION['flow'] = $flow;
							//mysql_query("START TRANSACTION"); //startting transaction
							$ttAttemptNo = getTTAttemptNO($userID,$teacherTopicCode);
							$query = "INSERT INTO ".TBL_TOPIC_STATUS." SET userID=$userID, teacherTopicCode='$teacherTopicCode'"; //adepts_teacherTopicStatus 
							if($ttAttemptNo>0)
								$query .= ", ttAttemptNo=$ttAttemptNo";
							if($flow!="")
								$query .= ", flow='$flow'";
							mysql_query($query) or die("2".mysql_error());
							$ttAttemptID = mysql_insert_id();
							$_SESSION['topicAttemptStatus'] = 'new';
							$_SESSION['teacherTopicAttemptID'] = $ttAttemptID;
							//$_SESSION['topicAttemptNo'] = getTopicAttemptNo($ttAttemptID);
							$_SESSION['topicAttemptNo'] = $ttAttemptNo;
							$clusterCode = nextCluster($userID,$teacherTopicCode,$ttAttemptID, $sessionID);
							$clusterAttemptID = $_SESSION['clusterAttemptID'];
							$tmpResult = nextQuestion($userID,$clusterAttemptID,$clusterCode,$sessionID, SUBJECTNO);
							$tmpResult = explode("~",$tmpResult);
							$qcode = $tmpResult[0];
							$showAnswer = $tmpResult[1];
	                        $_SESSION['clusterProgress'] = $tmpResult[2];
							if(!isset($_SESSION["qno"]))
							{
								$quesno=1;
								$_SESSION["qno"] =   $quesno;
							}
							else
								$quesno = $_SESSION["qno"];
							$objTT       = new teacherTopic($teacherTopicCode,$childClass,$flow);
							$higherLevel = isAtaHigherLevelInTT($objTT, $clusterCode,$childClass);
							$_SESSION['higherLevel'] = $higherLevel;

							$tempTT = $teacherTopicCode;
							if($objTT->customTopic==1)		//if custom topic load CQs of parent TT
								$tempTT = $objTT->parentTTCode;
							$_SESSION['topicProgressDetails'] = $objTT->getProgressDetailsAtSDL();
							$_SESSION['challengeQuestionsArray'] = loadChallengeQuestionsNewLogic($tempTT, $userID, $ttAttemptID);
							$_SESSION['allClustersInTT'] = loadAllClusterInTopic($tempTT);
							/*if(count($_SESSION['challengeQuestionsArray'])==0)
							{*/
								$_SESSION['challengeQuestionsOtherArray'] = loadChallengeQuestionsOtherTopic($teacherTopicCode, $userID);
							//}
							$ttCQArray = array("TT082");
							$_SESSION['challengeQuestionsProblemSolvingArray'] = loadChallengeQuestionsProblemSolving($ttCQArray, $childClass);
							//loadChallengeQuestions($tempTT, $userID);
							// New topic progress bar code start
							//Filling clas specific clusters for TT
							fillTopicProgressBar($teacherTopicCode,$childClass,$flow,$userID);
							// New topic progress bar code end
							// check kst here also
							if($_POST['quesCategory'] == 'kstPostTest'){ //This checks for first time for post test.
								$quesCategory = "kstdiagnosticTest";
								$_SESSION['kstdiagnosticTestType'] = 'Assessment';
								$_SESSION['isPostTest'] = 1;
							} else {
								$kstdiagnosticTest = checkForKstDiagnosticTest($teacherTopicCode,$schoolCode,$childClass);
								if(count($kstdiagnosticTest) != 0 && $kstdiagnosticTest['isActive'] == 1){
									$kstTestType = getKstTestStatus($userID,$_SESSION['teacherTopicAttemptID'],$teacherTopicCode); //checks for pre-test status
									if($kstTestType == 0){
										$quesCategory = "kstdiagnosticTest";
										$_SESSION['kstdiagnosticTest'] = $kstdiagnosticTest;
										unset($_SESSION['isPostTest']);
										unset($_SESSION['kstdiagnosticTestType']);
									}
									else if($kstTestType==1){
										$checkPostTest = checkPostStatus($userID,$_SESSION['teacherTopicAttemptID'],$teacherTopicCode); //checks for post-test
										if($checkPostTest == 0){
											$quesCategory = "kstdiagnosticTest";
											$_SESSION['kstdiagnosticTest'] = $kstdiagnosticTest;
											$_SESSION['kstdiagnosticTestType'] = 'Assessment';
											$_SESSION['isPostTest'] = 1;
										} else {
											unset($_SESSION['isPostTest']);
											unset($_SESSION['kstdiagnosticTestType']);
											//Now we check if we have to give any submodules.
											$_SESSION['current_cluster'] = get_current_cluster($teacherTopicCode,$userID);
											//Goes inside this if the activity is game or timedTest.
											if( (isset($_POST['isActivity']) && $_POST['isActivity'] == 1) || (isset($_POST['isTimedTest']) && $_POST['isTimedTest'] == 1) ) {
												$sqTimeTaken	=	"SELECT flowAttemptID FROM educatio_adepts.kst_userFlowDetails WHERE srno=".$_SESSION['subModule_srno']." AND status=0 ORDER BY flowAttemptID LIMIT 1";
												$rsTimeTaken	=	mysql_query($sqTimeTaken);
												$rwTimeTaken	=	mysql_fetch_array($rsTimeTaken);
												if(isset($_POST['Tduration'])){
													$timeTaken = $_POST['Tduration'];
													$sqFinish	=	"UPDATE educatio_adepts.kst_userFlowDetails SET status = 1, timeTaken=$timeTaken  WHERE flowAttemptID=".$rwTimeTaken['flowAttemptID'];	
												} else {
													$sqFinish	=	"UPDATE educatio_adepts.kst_userFlowDetails SET status = 1  WHERE flowAttemptID=".$rwTimeTaken['flowAttemptID'];
												}
												$rsFinish	=	mysql_query($sqFinish);
												$sqSetCurrentActivityDetail	=	"UPDATE educatio_adepts.kst_ModuleAttempt SET currentActivityDetail='' WHERE srno=".$_SESSION['subModule_srno'];
												$rsSetCurrentActivityDetail	=	mysql_query($sqSetCurrentActivityDetail);
												$_SESSION["currentSDL"] = "";
												getNextKstSubModuleInflow();
												redirectToKstFlow();
											}
											$_SESSION['current_cluster'] = get_current_cluster($teacherTopicCode,$userID);
											$featureType = $_SESSION['kstdiagnosticTest'] = checkForKstDiagnosticTest($teacherTopicCode,$schoolCode,$childClass);
											if(preg_match('(with pre-requisite)', strtolower($featureType['featureType'])) === 1){
												redirectToKstFlow();
											}
										}
									}
								}
							}
						}
					}
					if($_SESSION['remedialMode']==1)
					{
						$quesCategory = "remedial";
						$nextPage     = "remedialItem.php";
					}
					elseif($_SESSION['game']==true && $childClass>=4)
					{
						$nextPage = "controller.php";
					}
					elseif($quesCategory == 'kstdiagnosticTest'){
						$kst_util_obj = new kst_util();
						$ttAttemptID = $_SESSION['teacherTopicAttemptID'];
						$_SESSION['attemptData'] = $kst_util_obj -> getUserAttemptData($userID,$ttAttemptID);
						//check if additional test is going on or normal.
						/* SETTING attemptData into SESSION variable or 0 if not present from DB */
						//check if this condition is also getting executed once finishing with additional questions.
						$attemptedDate = date("Y-m-d");
						if($_POST['quesCategory'] == 'kstPostTest'){
							$_SESSION['isPostTest'] = 1;
							$query = "INSERT INTO educatio_adepts.kst_diagnosticTestAttempts (userID, testType, ttAttemptID, status, attemptedDate)
									SELECT * FROM (SELECT $userID, 'Post Test', $ttAttemptID, 0, '$attemptedDate') AS tmp
									WHERE NOT EXISTS (SELECT userID FROM educatio_adepts.kst_diagnosticTestAttempts WHERE userID = $userID and ttAttemptID = $ttAttemptID and testType='Post Test') LIMIT 1";
						} else {
							$query = "INSERT INTO educatio_adepts.kst_diagnosticTestAttempts (userID, testType, ttAttemptID, status, attemptedDate)
									SELECT * FROM (SELECT $userID, 'Pretest', $ttAttemptID, 0, '$attemptedDate') AS tmp
									WHERE NOT EXISTS (SELECT userID FROM educatio_adepts.kst_diagnosticTestAttempts WHERE userID = $userID and ttAttemptID = $ttAttemptID and testType='Pretest') LIMIT 1";
						}
						mysql_query($query);
						$qcode = 0;
						$nextPage = "question.php";
					}
					else
					{
						$quesCategory = "normal";
						$showAnswer = 1;
						$nextPage = "question.php";
						$_SESSION['fromTTSelection']=1;
						$commonInstructionSDL = $_SESSION['commonInstruction'];
						if($commonInstructionSDL!="")
						{
							$currentSDL	= $_SESSION["currentSDL"];
							$CISdlArray = explode(",",$commonInstructionSDL);
							if(in_array($currentSDL,$CISdlArray))
							{
								if($_SESSION['groupInstructionType'] == 'groupInstruction')
									$nextPage = "group_instruction.php";
								elseif($_SESSION['groupInstructionType'] == 'gamesMaster')
								{
									$clusterCode = $_SESSION['clusterCode'];
									$linkedToSDL = $currentSDL;
									$sql = "SELECT gameID FROM adepts_gamesMaster WHERE linkedToCluster='$clusterCode' and linkedToSDL=$linkedToSDL AND live='Live' AND type='introduction' AND ver NOT IN ('as2','as3')";
									$result = mysql_query($sql) or die($sql);
									if($line = mysql_fetch_array($result))
									$_SESSION['gameID'] = $line['gameID'];
									echo '<form id="enrichmentModuleForm" action="enrichmentModule.php" method="post">';
									echo '<input type="hidden" name="gameMode" id="gameMode" value="groupInstruction">';
									echo '</form>';
									echo "<script>
												document.getElementById('enrichmentModuleForm').submit();
										  </script>";
									 exit;
								}
							}
						}

						if($_SESSION["videoSystem"] == 1)
						{
							$introVidsSDL = $_SESSION['introVids'];
							if($introVidsSDL!="")
						    {
						    	$currentSDL	= $_SESSION["currentSDL"];
							    $CISdlArray = explode(",",$introVidsSDL);
							    if(in_array($currentSDL,$CISdlArray))
							    {
									if(videoViewedPreviously($_SESSION["clusterCode"],$_SESSION["currentSDL"]) == 0)
							    	$nextPage = "introductionVideos.php";
							    }
						    }
						}
					}
					//check for comprhensive module flow
					redirectToFlow();
					if($_SESSION['comprehensiveModule']!="" || $_SESSION['subModule']!="" )
						break;
					if($_SESSION['wildcardAtStart']!="" && $quesCategory=="normal" && $qcode>0 && $nextPage=="question.php" && $comprehensiveModuleDetailsArr[2]!="activity" && $_SESSION['game']!= true)
							$quesCategory = "wildcard";
					echo '<form id="frmHidForm" action="'.$nextPage.'" method="post">';
                    echo '<input type="hidden" name="qcode" id="qcode" value="'.$qcode.'">';
                    echo '<input type="hidden" name="qno" id="qno" value="'.$quesno.'">';
                    echo '<input type="hidden" name="quesCategory" id="quesCategory" value="'.$quesCategory.'">';
                    echo '<input type="hidden" name="showAnswer" id="showAnswer" value="'.$showAnswer.'">';
					if($comprehensiveModuleDetailsArr[2]=="activity")
						echo '<input type="hidden" name="mode" id="mode" value="comprehensive">';
                    else if ($_SESSION['game']==true)
                        echo '<input type="hidden" name="mode" id="mode" value="game">';
                    echo '</form>';
                    echo "<script>
                             document.getElementById('frmHidForm').submit();
                          </script>";
                    echo '</body>';
                    echo '</html>';
                    break;
	case "submitAnswer":
					$startTime = $_SESSION['startTime'];             //Take the start time for the ques for which response is received
                    $_SESSION['startTime'] = date("Y-m-d H:i:s");   //Reinitialize the start time to store it for the next ques
                    $_SESSION['endTime']   = 0;
                    unset($_SESSION['fromTTSelection']);
					$response 			= str_replace("ร—", "&#215;", $_POST['userResponse']);
					$response 			= str_replace("รท", "&#247;", $response);
					$encoding			= 'UTF-8';
                    $response           = htmlentities($response, ENT_COMPAT, $encoding);//echo $response; exit;
					$eeresponse         = $_POST['eeResponse'];	//equation editor
                    $sessionID          = $_SESSION['sessionID'];
                    $clusterAttemptID   = $_SESSION["clusterAttemptID"];
                    $clusterCode        = $_SESSION["clusterCode"];
					$qcode              = $_POST['qcode']; //qcode
                    $correct_answer     = $_POST['correctAnswer'];
                    $quesType           = $_POST['quesType'];
                    $currentSDL         = $_SESSION["currentSDL"];
                    $secs               = $_POST['secsTaken'];
                    $endTime            = $_POST['endTime'];
                    $quesno             = $_POST['qno'];
                    $responseResult     = $_POST['result']; //0 or 1
                    $responseExtraParameters = $_POST['extraParameters'];
                    $pageLoadTime       = $_POST['pageloadtime'];
                    $quesCategory       = $_POST['quesCategory'];
                    $getDirectQuestion       = $_POST['getDirectQuestion'];
                    $dynamic            = $_POST['dynamicQues'];
                    $dynamicParams      = $_POST['dynamicParams'];
                    $noOfTrialsTaken    = isset($_POST['noOfTrialsTaken'])?$_POST['noOfTrialsTaken']:0;
					$noOfTrialsAllowed	= isset($_POST['noOfTrialsAllowed'])?$_POST['noOfTrialsAllowed']:0;
					$childClass = $_SESSION['childClass'];
					$teacherTopicAttemptID = $_SESSION['teacherTopicAttemptID'];
					$teacherTopicCode = $_SESSION['teacherTopicCode'];
					$dup = removeSession(TBL_SESSION_STATUS, $sessionID);              //Duplicate login
					if($dup=="endsession")
					{
						echo "Duplicate";
						break;
					}
					if($noOfTrialsAllowed < $noOfTrialsTaken)
					$noOfTrialsTaken	=	$noOfTrialsAllowed;
					$hintAvailable		= $_POST['hintAvailable'];
					$hintUsed			= $_POST['hintUsed'];
					$tmpMode			= $_POST['tmpMode'];
					$userAllAnswers		= $_POST['userAllAnswers'];
					$timeTakenHints		= $_POST['timeTakenHints'];
					$isHintUsefull	    = isset($_POST['isHintUsefull'])?$_POST['isHintUsefull']:0;
					$toughType	   		= isset($_POST['toughType'])?$_POST['toughType']:"";
					$toughResult 		= $_POST['toughResult'];
                    $timeTakenToughQues = $_POST['timeTakenToughQues'];
					$promptNumber		= $_POST['promptNo'];
					if($secs=="undefined") $secs=0;
					if($_SESSION['sbaTestID']!="")
					{
						$totalQuestion	=	$_POST["totalQuestion"];
						$totalTime		=	$_POST["totalTime"];
						$timeLeft		=	$_POST["timeLeft"];
						$reviewQuestion	=	$_POST["reviewQuestions"];
						nextSbaQuestion($userID,$sessionID,SUBJECTNO,$qcode,$response, $quesno, $secs, $responseResult, $dynamic, $dynamicParams, $eeresponse, $totalQuestion,$totalTime,$timeLeft);
						$qcode		=	$_SESSION["qcode"];
						$quesno		=	$_SESSION["qno"];
						$quesCategory	=	"sba";
						if($qcode=="" || $reviewQuestion=="1")
							$response = "";
						else
							$response = createResponse($qcode,$quesno,$quesCategory);
						echo $response;
    					break;
					}
                    else if($quesCategory=="diagnosticTest")
					{
						$qcode   = nextDiagnosticQuestion($userID,$sessionID,SUBJECTNO,$qcode,$response, $quesno, $secs, $responseResult, $dynamic, $dynamicParams, $eeresponse,$teacherTopicAttemptID,$teacherTopicCode);
						if(count($_SESSION["diagnosticTestQuestions"])==0) //complete the test
						{
							$response	=	array();
							$response["tmpMode"]	=	"diagnosticTestComplete";
							$diagnosticTestResult	=	explode("~",$qcode);
							$response["dataResponse"]	=	$diagnosticTestResult[0];
							$choiceScreenFlag = $diagnosticTestResult[1].'~'.$diagnosticTestResult[2];
							$query  = "SELECT qcode FROM ".TBL_CURRENT_STATUS." WHERE userID=$userID AND ttAttemptID=$teacherTopicAttemptID";
							$result = mysql_query($query);
							if($line   = mysql_fetch_array($result))
							{
								$qcode = $line[0];
								$_SESSION['qcode'] = $qcode;
							}
							$response["lastQcode"]	=	$qcode;
							$response["testType"]	=	$diagnosticTestResult[3];
							$response["alertText"]	=	$diagnosticTestResult[4]!=''?$diagnosticTestResult[4]:'';
							if($response["dataResponse"]=="Saved")
								$_SESSION['choiceScreenFlagDT']	= $choiceScreenFlag;
							$_SESSION["qno"] = 1;
							echo json_encode($response);
						}
						else
						{
							$quesno++;
							$_SESSION["qno"] = $quesno;
							$curQuesType = "diagnosticTest";
							$response = createResponse($qcode,$quesno,$curQuesType,"1");
							echo $response;
						}
						break;
					}
					else if($quesCategory=="kstdiagnosticTest")
					{
						$quesno		=	$_SESSION["qno"];
						$isPredicted = check_predictedQuestions_kst($_SESSION['userID'], $_SESSION['teacherTopicAttemptID']);
						if($isPredicted == 1 ){
							//this means there is predicted questions and the student has completed the api now we have to ask additional
							$_SESSION['isPredicted'] = 1;
						} else {
							$_SESSION['isPredicted'] = 0;
						}
						$kst_util_obj = new kst_util();
						$qInfo   = nextKstDiagnosticQuestion($userID,$sessionID,SUBJECTNO,$qcode,$response, $quesno, $secs, $responseResult, $dynamic, $dynamicParams, $eeresponse,$teacherTopicAttemptID,$teacherTopicCode,$kst_util_obj);
						if($qInfo['questionCode'] == 'done') //complete the test
						{
							$response	=	array();
							$response["tmpMode"]	=	"kstdiagnosticTestComplete";
							$response["lastQcode"]	=	$qInfo['questionCode'];
							$response["testType"]	=	$_SESSION['kstdiagnosticTest']['featureType'];
							$response["alertText"]	=	$qInfo['alertText'];
							$_SESSION["qno"] = 1;
							echo json_encode($response);
						} 
						else
						{
							$quesno++;
							$_SESSION["qno"] = $quesno;
							$curQuesType = "kstdiagnosticTest";
							$response = createResponse($qInfo['questionCode'],$quesno,$curQuesType,"1");
							echo $response;
						}
						break;
					}
					else if($quesCategory=="daTest")
					{
						if(isset($_SESSION["practiseid"]))
							$practiseid = $_SESSION["practiseid"];
						else
							$practiseid = '';

						$daPaperCode =	$_POST['daPaperCode'];
						
						$qcode   = nextDATestQuestion($userID,$sessionID,SUBJECTNO,$qcode,$response, $quesno, $secs, $responseResult, $dynamic, $dynamicParams, $eeresponse, $daPaperCode );
						
						
							$quesno++;
							$_SESSION["qno"] = $quesno;
							$curQuesType = "daTest";
							$response = createResponse($qcode,$quesno,$curQuesType,"1");
							echo $response;
						
						break;
					}
					else if($quesCategory=="worksheet")
					{
						$worksheetID =	$_POST['worksheetID'];
						$worksheetAttemptID =	$_POST['worksheetAttemptID'];
						$submitWorksheet =	$_POST['submitWorksheet'];											
						$qcodeL   = nextWorksheetQuestion($userID,$sessionID,SUBJECTNO,$qcode,$response, $quesno, $secs, $responseResult, $worksheetAttemptID,$getDirectQuestion);
						if (!$submitWorksheet){
							$qcodeL=explode("~", $qcodeL);
							$qcode=$qcodeL[1];
							$quesno=$qcodeL[0]*1+1;
						}
						$_SESSION["qno"] = $quesno;
						$curQuesType = "worksheet";
						$response = createResponse($qcode,$quesno,$curQuesType,"1");
						echo $response;
						break;
					}
					else if($quesCategory=="practiseTest")
					{
						if(isset($_SESSION["practiseid"]))
							$practiseid = $_SESSION["practiseid"];
						else
							$practiseid = '';
						
						$qcode   = nextpractiseQuestion($userID,$sessionID,SUBJECTNO,$qcode,$response, $quesno, $secs, $responseResult, $dynamic, $dynamicParams, $eeresponse, $practiseid );
						
						
							$quesno++;
							$_SESSION["qno"] = $quesno;
							$curQuesType = "practiseTest";
							$response = createResponse($qcode,$quesno,$curQuesType,"1");
							echo $response;
						
						break;
					}
					else if($quesCategory=="practiseModule"){

						$currentScore = $_POST['iTargetSpeed'];
						$timedTestAttemptId=isset($_POST['timedTestAttemptId'])?$_POST['timedTestAttemptId']:"";
						$timedTestCode=isset($_POST['timedTestCode'])?$_POST['timedTestCode']:"";
						$qcode=isset($_POST['timedTestCode'])?"":$_POST['qcode'];

						$dailyDrillSession = $_SESSION['dailyDrillArray'];
						$isInternalRequest = $_SESSION['dailyDrillArray']['isInternalRequest'];
						$questionLevel = $_SESSION['dailyDrillArray']['currentLevel'];
						$attemptNo = $_SESSION['dailyDrillArray']['attemptNo'];
						$practiseModuleId = $_SESSION['dailyDrillArray']['practiseModuleId'];
						$practiseModuleTestStatusId = $_SESSION['dailyDrillArray']['practiseModuleTestStatusId'];

						$nextPractiseModQuestion=nextPractiseModuleQuestion($qcode,$timedTestCode,$quesno,$response,$responseResult,$secs,$sessionID,$dynamic,$dynamicParams,$userID,$questionLevel,$attemptNo,$practiseModuleId,$practiseModuleTestStatusId,$timedTestAttemptId,$responseExtraParameters);
						$nextPractiseElement=explode("~",$nextPractiseModQuestion);

						//$qcode.'~question~'.$currentLevel.'~'.$quesno;
						//$timedTestCode.'~timedTest~'.$currentLevel;
						//-98 error
						//-99 completed
						$quesno=(isset($nextPractiseElement[3])?$nextPractiseElement[3]+1:1);
						$_SESSION['dailyDrillArray']['currentLevel']=$nextPractiseElement[2];
						//$_SESSION["qno"] = $quesno;
						$qcode=$nextPractiseElement[0];
						
							//Check if exceeded todays limit on time spent
							$limitExceeded = hasExceededTodaysLimit($userID);
							if($limitExceeded!=0)
							{
								$_SESSION["limitExceeded"] = $limitExceeded;
								$qcode = $limitExceeded;                //set the response to -5 which will be handled in the question page
							}

						$response = createResponse($qcode,$quesno,$quesCategory,"1");

						echo $response;
						break;
						
					}
					else if($_SESSION['comprehensiveCluster']==1)
					{
						$tmpResult   = nextComprehensiveQuetion($userID,$sessionID,SUBJECTNO,$qcode,$response, $quesno, $secs, $responseResult, $dynamic, $dynamicParams, $eeresponse);
						$tmpResult   = explode("~",$tmpResult);
						$qcode       = $tmpResult[0];
						$showAnswer  = $tmpResult[1];
						$quesno++;
						$_SESSION["qno"] = $quesno;
						$tmpMode     = "question";
						$curQuesType = "normal";
						if($qcode==0)
						{
							$_SESSION['comprehensiveCluster']=0;
							getNextComprehensiveInflow();
							$comprehensiveModuleDetails	=	checkPendingComprehensiveModule($teacherTopicAttemptID);
							if($comprehensiveModuleDetails != "")
							{
								$comprehensiveModuleDetailsArr	=	explode("$",$comprehensiveModuleDetails);
								$_SESSION['comprehensiveModule']	=	$comprehensiveModuleDetailsArr[0];
								if($comprehensiveModuleDetailsArr[2]=="remedial")
								{
									$tmpMode = "remedial";
									$qcode	=	$comprehensiveModuleDetailsArr[1];
								}
								else if(strtolower($comprehensiveModuleDetailsArr[2])=="timedtest")
								{
									$tmpMode = "timedtest";
									$_SESSION["quesCategory"] = "comprehensive";
									$_SESSION['timedTest']	=	$comprehensiveModuleDetailsArr[1];
								}
								else if($comprehensiveModuleDetailsArr[2]=="activity")
								{
									$_SESSION['gameID']	=	$comprehensiveModuleDetailsArr[1];
									$qcode	=	-11;
									$tmpMode     = $_SESSION['gameID'];

								}
								else if(strtolower($comprehensiveModuleDetailsArr[2])=="diagnostic")
								{
									$_SESSION['diagnosticTest']	=	$comprehensiveModuleDetailsArr[1];
									$tmpMode = "diagnosticTest";
								}
								else if(strtolower($comprehensiveModuleDetailsArr[2])=="cluster")
								{
									$_SESSION['clusterCode']	=	$comprehensiveModuleDetailsArr[1];
									$_SESSION['comprehensiveCluster'] = 1;
									$qcode	=	getComphrensiveQcode($_SESSION['clusterCode']);
									$tmpMode     = "question";
									$curQuesType = "normal";
								}
							}
						}
						//if all comprehensive submodules are completed than move to next cluster
						if($qcode== 0)
						{
							$query  = "SELECT qcode,clusterCode,clusterAttemptID FROM ".TBL_CURRENT_STATUS." WHERE userID=$userID AND ttAttemptID=$teacherTopicAttemptID ";
							$result = mysql_query($query);
							if($line   = mysql_fetch_array($result))
							{
								$qcode = $line[0];
								if($qcode != -8)
								{
									$_SESSION['clusterCode'] = $line[1];
									$_SESSION['clusterAttemptID'] = $line[2];
									$clusterAttemptID = $_SESSION['clusterAttemptID'];
									$clusterCode = $_SESSION['clusterCode'];
									loadArrays($clusterCode,$clusterAttemptID, $userID, $teacherTopicAttemptID, $_SESSION['flashContent']);							
									$tmpResult = nextQuestion($userID,$clusterAttemptID,$clusterCode,$sessionID, SUBJECTNO);
									$tmpResult = explode("~",$tmpResult);
									$qcode = $tmpResult[0];
									$_SESSION['clusterProgress'] = $tmpResult[2];
								}
								$quesno=1;
								$_SESSION["qno"] =1;
							}
						}
							$choiceScreenFlagDT = isset($_SESSION['choiceScreenFlagDT']) && $_SESSION['choiceScreenFlagDT']!='' ?$_SESSION['choiceScreenFlagDT'] : 0;
							$response = createResponse($qcode,$quesno,$curQuesType,$showAnswer,$tmpMode,$choiceScreenFlagDT);
							unset($_SESSION['choiceScreenFlagDT']);
							echo $response;
							break;
					}
					else if($_SESSION['subModuleCluster']==1){
						$tmpResult   = nextKstModuleQuestion($userID,$sessionID,SUBJECTNO,$qcode,$response, $quesno, $secs, $responseResult, $dynamic, $dynamicParams, $eeresponse);
						$tmpResult   = explode("~",$tmpResult);
						$qcode       = $tmpResult[0];
						$showAnswer  = $tmpResult[1];
						$quesno++;
						$_SESSION["qno"] = $quesno;
						$tmpMode     = "question";
						$curQuesType = "normal";

						if($qcode==0)
						{
							$_SESSION['subModuleCluster']=0;
							getNextKstSubModuleInflow();
							$subModuleDetails	=	checkPendingSubModuleForKst($teacherTopicAttemptID);
							if($subModuleDetails != "")
							{
								//0-activityCode 1-activityType
								$subModuleDetailsArr	=	explode("$",$subModuleDetails);
								$_SESSION['subModule']	=	$subModuleDetailsArr[0];
								if($subModuleDetailsArr[1]=="remedial")
								{
									$tmpMode = "remedial";
									$qcode	=	$subModuleDetailsArr[0];
								}
								else if(strtolower($subModuleDetailsArr[1])=="timedtest")
								{
									$tmpMode = "timedtest";
									$_SESSION["quesCategory"] = "comprehensive";
									$_SESSION['timedTest']	=	$subModuleDetailsArr[0];
								}
								else if($subModuleDetailsArr[1]=="activity")
								{
									$_SESSION['gameID']	=	$subModuleDetailsArr[0];
									$qcode	=	-11;
									$tmpMode     = $_SESSION['gameID'];

								}
								else if(strtolower($subModuleDetailsArr[1])=="cluster")
								{
									$_SESSION['clusterCode']	=	$subModuleDetailsArr[0];
									$_SESSION['subModuleCluster'] = 1;
									$qcode	=	getKstQcode($_SESSION['clusterCode']);
									$tmpMode     = "question";
									$curQuesType = "normal";
								}
							}  else { 						//if all comprehensive submodules are completed than move to next cluster
								$query  = "SELECT qcode,clusterCode,clusterAttemptID FROM ".TBL_CURRENT_STATUS." WHERE userID=$userID AND ttAttemptID=$teacherTopicAttemptID ";							
								$result = mysql_query($query);
								if($line   = mysql_fetch_array($result))
								{
									$qcode = $line[0];
									if($qcode != -8)
									{
										$_SESSION['clusterCode'] = $line[1];
										$_SESSION['clusterAttemptID'] = $line[2];
										$clusterAttemptID = $_SESSION['clusterAttemptID'];
										$clusterCode = $_SESSION['clusterCode'];
										loadArrays($clusterCode,$clusterAttemptID, $userID, $teacherTopicAttemptID, $_SESSION['flashContent']);
										$tmpResult = nextQuestion($userID,$clusterAttemptID,$clusterCode,$sessionID, SUBJECTNO);
										$tmpResult = explode("~",$tmpResult);
										$qcode = $tmpResult[0];
										$_SESSION['clusterProgress'] = $tmpResult[2];
									}
									$quesno=1;
									$_SESSION["qno"] =1;
								}
							} 
						}

						$choiceScreenFlagDT = isset($_SESSION['choiceScreenFlagDT']) && $_SESSION['choiceScreenFlagDT']!='' ?$_SESSION['choiceScreenFlagDT'] : 0;
						$response = createResponse($qcode,$quesno,$curQuesType,$showAnswer,$tmpMode,$choiceScreenFlagDT);
						unset($_SESSION['choiceScreenFlagDT']);
						echo $response;
						break; 
					}
					else if(isset($_SESSION['examCornerCluster']) && $_SESSION['examCornerCluster']==1)
					{
						$tmpResult   = nextBucketQuetion($userID,$sessionID,SUBJECTNO,$qcode,$response, $quesno, $secs, $responseResult, $dynamic, $dynamicParams, $eeresponse);
						$tmpResult   = explode("~",$tmpResult);
						$qcode       = $tmpResult[0];
						$showAnswer  = $tmpResult[1];
						$quesno++;
						$_SESSION["qno"] = $quesno;
						$tmpMode     = "question";
						$curQuesType = "normal";
						if($qcode==-16)
						{
							if(isset($_POST["impQuesMode"]) && $_POST["impQuesMode"]==0)
								finishBucketCluster();
						}
						$response = createResponse($qcode,$quesno,$curQuesType,$showAnswer,$tmpMode);
						echo $response;
						break;
					}
					else if($quesCategory=="NCERT")
					{
						$attemptID = $_SESSION["ncertAttemptID"];
						$exerciseCode = $clusterCode;
						$tmpResult = nextNCERTquestion($userID,$exerciseCode,$qcode,$attemptID,$quesno,$sessionID,$currentSDL,$responseResult,$response,$secs,$eeresponse);
						if($tmpResult != -1)
						{
							$tmpResult = explode("~",$tmpResult);
							$qcode = $tmpResult[0];
							$showAnswer = $tmpResult[1];
							$curQuesType = "NCERT";
							$tmpMode = "NCERT";
							//updateNCERTExcercise($attemptID);
						}
						else
						{
							$qcode = -14;
							$showAnswer = 0;
							$curQuesType = "NCERT REPORT";
							$tmpMode = $attemptID;
							//updateNCERTExcercise($attemptID,true);
						}
						$sdlArray = array_keys($_SESSION["allQuestionsArray"]);
						$completeExerciseFlag = (count($_SESSION["completedGroups"]) == count($sdlArray))?true:false;
						/*$sql = "SELECT COUNT(srno) FROM adepts_ncertQuesAttempt WHERE ncertAttemptID=$attemptID AND R=-1";
						$result = mysql_query($sql);
						$row = mysql_fetch_array($result);
						$completeExerciseFlag = ($row[0] == 0)?true:false;
						updateNCERTExcercise($attemptID,$completeExerciseFlag);*/
						updateNCERTExcercise($attemptID,$completeExerciseFlag);
						$quesno++;
					}
					else if($quesCategory=="topicRevision")
					{
						$teacherTopicCode  = $_SESSION['teacherTopicCode'];
						$_SESSION['totalCorrect'] += $responseResult;
						$_SESSION['totalRevisionQuesAttempt']++;
						saveTopicRevisionQuesResponse($userID,$sessionID, $teacherTopicCode,$quesno, $qcode,$response,$secs,$responseResult, $dynamic, $dynamicParams);
						$quesno++;
						$prevCluster = $clusterCode;
						$clusterArray = array_keys($_SESSION['quesAttempted']);
						$nextQuesDetails = findNextQuesForTopicRevision($clusterArray, $prevCluster, $quesno);
						if($nextQuesDetails!="")
						{
							$nextQuesDetails = explode("-",$nextQuesDetails);
							$_SESSION["clusterCode"] = $nextQuesDetails[0];
							$qcode       = $nextQuesDetails[1];
							if($qcode=="")
								$qcode = -9;
						}
						else
						{
							$qcode = -9;
						}
						if($qcode == -9)
						{
							$topicRevisionSparkies = calculateRevisionSparkies($teacherTopicCode,$sessionID,$_SESSION['topicRevisionAttemptNo'],$_SESSION['totalCorrect'],$_SESSION['totalRevisionQuesAttempt']);
							if($topicRevisionSparkies > 0){
								addSparkies($topicRevisionSparkies, $sessionID);
								$_SESSION['sparkie']['topicRevision'] += $topicRevisionSparkies;
							}
						}
						$tmpMode     = "question";
						$curQuesType = "topicRevision";
						$showAnswer  = 1;
					}
					elseif($quesCategory!="exercise")
					{
	                    $showAnswer  = $_POST['showAnswer'];         //for challenge question
	                    $prevCluster = $clusterCode;
						$prevSDL     = $currentSDL;
						$tmpResult   = nextQuestion($userID,$clusterAttemptID,$clusterCode,$sessionID,SUBJECTNO,$qcode,$currentSDL,$response, $quesno, $secs, $responseResult, $startTime, $endTime, $pageLoadTime, $quesCategory,$dynamic,$dynamicParams, $showAnswer, $noOfTrialsTaken, $eeresponse,$hintAvailable, $hintUsed ,$tmpMode,$userAllAnswers,$timeTakenHints,$isHintUsefull,$toughType,$toughResult,$promptNumber,$timeTakenToughQues, $responseExtraParameters);
						$tmpResult   = explode("~",$tmpResult);
						$qcode       = $tmpResult[0];
						$showAnswer  = $tmpResult[1];
                        $_SESSION['clusterProgress']  = $tmpResult[2];
                        $choiceFlag = isset($tmpResult[3])?$tmpResult[3].'~'.$clusterCode.'~'.$clusterAttemptID:0;
						if(($_SESSION['prevQcode'] == $_SESSION["qcode"]) && $quesCategory!="challenge" && $quesCategory!="topicRevision"   && $quesCategory!="wildcard"  && $_SESSION["isQuesDynamicArray"][$_SESSION["qcode"]]!=1 && $quesCategory!="bonusCQ")
						{
							$duplicate_question_data_log = "Session prevQcode: ".$_SESSION['prevQcode'].", ";
							$duplicate_question_data_log .= "Session qcode: ".$_SESSION['qcode'].", ";
							$duplicate_question_data_log .= "Var prevSDL: ".$prevSDL.", ";
							$duplicate_question_data_log .= "Var prevCluster: ".$prevCluster.", ";
							$duplicate_question_data_log .= "Session currentSDL: ".$_SESSION["currentSDL"].", ";
							$duplicate_question_data_log .= "Session clusterCode: ".$_SESSION["clusterCode"].", ";
							$duplicate_question_data_log .= "Session allQuestionsArray: ".json_encode($_SESSION["allQuestionsArray"]).", ";
							$duplicate_question_data_log .= "Session questionsNeverAttemptedArray[SDL]: ".json_encode($_SESSION["questionsNeverAttemptedArray"]).", ";
							$duplicate_question_data_log .= "Session questionsAttemptedInCurrentClusterAttemptArray: ". json_encode($_SESSION["questionsAttemptedInCurrentClusterAttemptArray"]).", ";
                            $duplicate_question_data_log .= "quesCategory: ".$quesCategory.", ";
							$duplicate_question_data_log .= "nextQuestion return values: ".implode("~",$tmpResult);

							if($qcode == -3 || $qcode == -8) {
								$log_error_data = "INSERT INTO adepts_errorLogs SET bugType='duplicateQuestion', 
									bugText='".mysql_real_escape_string($duplicate_question_data_log)."', qcode='".$_SESSION['qcode']."', 
									userID='".mysql_real_escape_string($_SESSION['userID'])."', 
									sessionID='".mysql_real_escape_string($_SESSION['sessionID'])."', 
									schoolCode='".mysql_real_escape_string($_SESSION['schoolCode'])."'";
								$exec_log_error_data = mysql_query($log_error_data);
							} else {
								echo "DUPLICATE_QUESTION - ".$duplicate_question_data_log;
								break;
							}
						}
						if($showAnswer=="")  //This condition will apply in case end of topic or class level completion, in that case this will be blank and so the xml output will be incorrect, hence setting it to zero.
							$showAnswer  = 0;
						if($quesCategory=='bonusCQ' || (count($_SESSION["bonusCQArray"])>0  && count($_SESSION["bonusCQArray"])<3))	{
							$choiceFlag=0;
						}
						else if($qcode==-2 || $qcode == -3)        {	//End of  topic
							//echo 'end of topic reached: '.$_SESSION['subModule'];die;
							/* UPDATE PROGRESS UNSET UPDATE FLAG - PROGRESS*/
							if(trim($_SESSION['topicAttemptNo'])=="1" && $_SESSION['rewardSystem']==1)
							{
								$objRewards	=	new Sparkies($userID);
								$objRewards->giveTopicReward($_SESSION['teacherTopicCode']);
								addSparkies(15, $sessionID);

								$queryTeacherTopic="SELECT perCorrect,teacherTopicCode from ".TBL_TOPIC_STATUS." WHERE ttAttemptID=".$_SESSION["teacherTopicAttemptID"];
								$queryRes=mysql_query($queryTeacherTopic) or die(mysql_error());
								$line=mysql_fetch_array($queryRes);
								$query2 = "INSERT INTO  adepts_userFeeds (userID, studentIcon, childName, childClass, schoolCode, actID, actDesc, actIcon, score, timeTaken, srno, ftype) VALUES ($userID, '', '".$_SESSION['childName']."', ".$_SESSION['childClass'].", '".$_SESSION['schoolCode']."', '".$line[1]."', '".$_SESSION['teacherTopicName']."', '', '".$line[0]."', 0, ".$_SESSION["teacherTopicAttemptID"]." , 'topic')";
								mysql_query($query2);
                         	}
							if($_SESSION["updateProgress"] )
							{
								$progressUpdateObj = new topicProgressCalculation($_SESSION['teacherTopicCode'],$_SESSION['childClass'],$_SESSION['flow'],$_SESSION["teacherTopicAttemptID"],SUBJECTNO);
								$progressUpdateObj->updateProgress();
								$_SESSION["updateProgress"] = false;
							}
							/* CODE ENDS */
	                        updateTeacherTopicStatus($_SESSION['teacherTopicAttemptID'],$qcode);
	                        deleteCurrentStatus($userID,$_SESSION['teacherTopicCode'],$flagForOffline);
	                        $choiceFlag='2~'.$_SESSION['teacherTopicCode'].'~'.$_SESSION["teacherTopicAttemptID"];
	                         if($_SESSION['topicAttemptNo'] == 1 || $_SESSION["userType"]=="msAsStudent" || $_SESSION["userType"]=="teacherAsStudent")
						    {
						    	checkForComprehensiveModule($teacherTopicAttemptID, $userID, $sessionID,'Assessment');
						    	if($_SESSION['diagnosticTest'] !="" && $_SESSION['comprehensiveModule']!="")
								{
									$tmpMode	= "Assessment";
									$response = createResponse($qcode,$quesno,$curQuesType,$showAnswer,$tmpMode);
									echo $response;
			    					break;
								}
						    }
	                    }
	                    elseif ($qcode==-8)	//if class level completed, set the qcode to -8 so that in case the user closes the browser, on next login gets the same message for this TT
	                    {
							//echo 'end of topic reached: '.$_SESSION['topicAttemptNo'].$_SESSION["userType"];die;
							/* UPDATE PROGRESS UNSET UPDATE FLAG - PROGRESS*/
							if(trim($_SESSION['topicAttemptNo'])=="1" && $_SESSION['rewardSystem']==1)
							{
								$objRewards	=	new Sparkies($userID);
								$objRewards->giveTopicReward($_SESSION['teacherTopicCode']);
								addSparkies(15, $sessionID);

								$queryTeacherTopic="SELECT perCorrect,teacherTopicCode from ".TBL_TOPIC_STATUS." WHERE ttAttemptID=".$_SESSION["teacherTopicAttemptID"];
								$queryRes=mysql_query($queryTeacherTopic) or die(mysql_error());
								$line=mysql_fetch_array($queryRes);
								$query2 = "INSERT INTO  adepts_userFeeds (userID, studentIcon, childName, childClass, schoolCode, actID, actDesc, actIcon, score, timeTaken, srno, ftype) VALUES ($userID, '', '".$_SESSION['childName']."', ".$_SESSION['childClass'].", '".$_SESSION['schoolCode']."', '".$line[1]."', '".$_SESSION['teacherTopicName']."', '', '".$line[0]."', 0, ".$_SESSION["teacherTopicAttemptID"]." , 'topic')";
								mysql_query($query2);
							}
							if($_SESSION["updateProgress"])
							{
								$progressUpdateObj = new topicProgressCalculation($_SESSION['teacherTopicCode'],$_SESSION['childClass'],$_SESSION['flow'],$_SESSION["teacherTopicAttemptID"],SUBJECTNO);
								$progressUpdateObj->updateProgress();
								$_SESSION["updateProgress"] = false;
							}
							/* CODE ENDS */
							$query = "UPDATE ".TBL_CURRENT_STATUS." SET qcode=$qcode WHERE userID=$userID AND teacherTopicCode='".$_SESSION['teacherTopicCode']."'";
						    mysql_query($query);
						    $choiceFlag='2~'.$_SESSION['teacherTopicCode'].'~'.$_SESSION["teacherTopicAttemptID"];
						    if($_SESSION['topicAttemptNo'] == 1 || $_SESSION["userType"]=="msAsStudent" || $_SESSION["userType"]=="teacherAsStudent")
						    {
								if(preg_match('(post test)', strtolower($_SESSION['kstdiagnosticTest']['featureType'])) === 1){
									$tmpMode	= "KstAssessment";
									$curQuesType = "kstdiagnosticTest";
									$_SESSION['kstdiagnosticTestType'] == 'Assessment';
									$response = createResponse($qcode,$quesno,$curQuesType,$showAnswer,$tmpMode);
									echo $response;
									break;
								} else {
									checkForComprehensiveModule($teacherTopicAttemptID, $userID, $sessionID,'Assessment');
									if($_SESSION['diagnosticTest'] !="" && $_SESSION['comprehensiveModule']!="")
									{
										$tmpMode	= "Assessment";
										$response = createResponse($qcode,$quesno,$curQuesType,$showAnswer,$tmpMode);
										echo $response;
										break;
									}
								}
						    }
	                    }
						else if($qcode==-11) // DCT will be Applicable here...
						{
							$dctType = $tmpResult[2].'~'.$tmpResult[3]; // Expects return values (DCT~1, DCT~2, false,$gameID~$gameCode)
						}

						$curQuesType = $_SESSION['questionType'];
						if($qcode==-11)
							$curQuesType = "DCT";
						else if ($qcode==-13)
							$curQuesType = "researchModule";
	                    $_SESSION['pageStartTime'] = 0;
	                    if(count($_SESSION["bonusCQArray"])>0)
						{
							if(count($_SESSION["bonusCQArray"])==3)
							{
								$_SESSION["qno"]++;
								$_SESSION['quesAttemptedInTopic']++;
								$_SESSION['afterBonusCQqcode'] = $qcode;
								if ($choiceFlag!=0) $_SESSION['choiceScreenAfterBonusCQ']=$choiceFlag;
							}
							$bonusCQArray = $_SESSION["bonusCQArray"];
							$randKey = array_rand($bonusCQArray);
							$qcode = $bonusCQArray[$randKey];
							unset($bonusCQArray[$randKey]);
							$_SESSION["bonusCQArray"] = $bonusCQArray;
							echo $response = createResponse($qcode,$quesno,"bonusCQ",1,"");
							exit();
						}
	                    else if($curQuesType!="challenge" && $curQuesType!="DCT" && $curQuesType!="researchModule" && $_SESSION['comprehensiveModule']!="")
	                    {
	                       	$tmpMode = "diagnosticTest";
	                    }
	                    else if($curQuesType!="challenge" && $curQuesType!="DCT" && $curQuesType!="researchModule" && $_SESSION['timedTest']!="")
	                    {
	                       	$tmpMode = "timedtest";
	                       	if ($choiceFlag!=0) $_SESSION['choiceScreenAfterTimedTest']=$choiceFlag;
	                       	else $_SESSION['choiceScreenAfterTimedTest']=0;
	                    }
	                    elseif($curQuesType=="remedial")
	                    {
	                    	$tmpMode = "remedial";
	                    }
	                    elseif($curQuesType=="DCT")
						{
							$tmpMode = $dctType;
							$showAnswer = 0;
	                    }
	                    elseif ($curQuesType=="researchModule")
	                    {
	                    	$tmpMode = "researchModule";
	                    }
						elseif($_SESSION['questionType']=="wildcard")
						{
							$tmpMode = $_SESSION['wildcardAtRand']['type'];
							$_SESSION['wildcardAtRand']	=	"";
							$_SESSION["wildcardSession"]	=	0;
						}
	                    else
	                    {
	                      	$tmpMode = "question";
	                       	$childClass  = $_SESSION['childClass'];
	                       	$gamePlayed  = $_SESSION['gamePlayed'];

	                       	if(SUBJECTNO==2)	//Game logic only for Maths
	                       	{
								if($_SESSION['game'] == true && $gamePlayed != 2)
									$tmpMode = "game";
                        	}

                        	$commonInstructionSDL = $_SESSION['commonInstruction'];
						    $clusterCode 		= $_SESSION["clusterCode"];
						    $currentSDL			= $_SESSION["currentSDL"];
						    if(($prevCluster!=$clusterCode || $prevSDL!=$currentSDL) && $commonInstructionSDL!="" && $tmpMode!="game" && $curQuesType!="challenge")
						    {
							    $CISdlArray = explode(",",$commonInstructionSDL);
							    if(in_array($currentSDL,$CISdlArray))
								{
    								unset($CISdlArray[array_search($currentSDL,$CISdlArray)]);
									$_SESSION['commonInstruction'] = implode(",",$CISdlArray);
									$linkedToSDL = $currentSDL;
									$sql = "SELECT gameID FROM adepts_gamesMaster WHERE linkedToCluster='$clusterCode' and linkedToSDL=$linkedToSDL AND live='Live' AND type='introduction' AND ver NOT IN ('as2','as3')";
									
									$result = mysql_query($sql) or die($sql);
									
									if($line = mysql_fetch_array($result))
									$_SESSION['gameID'] = $line['gameID'];
									if($_SESSION['groupInstructionType'] == 'groupInstruction')
										$tmpMode = "commoninstruction - groupInstruction";
									elseif($_SESSION['groupInstructionType'] == 'gamesMaster')
										$tmpMode = "commoninstruction - enrichmentModule";
								}
						    }
							
							
							if($_SESSION["videoSystem"] == 1)
							{
								$introVidsSDL = $_SESSION['introVids'];
							    $clusterCode 		= $_SESSION["clusterCode"];
							    $currentSDL			= $_SESSION["currentSDL"];
								
							    if(($prevCluster!=$clusterCode || $prevSDL!=$currentSDL) && $introVidsSDL!="" && $tmpMode!="game")
							    {
									
								    $CISdlArray = explode(",",$introVidsSDL);
								    if(in_array($currentSDL,$CISdlArray))
									{
										if(videoViewedPreviously($_SESSION["clusterCode"],$_SESSION["currentSDL"]) == 0)
										$tmpMode = "introVids";
									}
									    
							    }
							}
							
                        }
                        if(($quesCategory!="wildcard" || $quesno>1) && $curQuesType!="challenge" && $curQuesType!="remedial" && $curQuesType!="DCT" && $curQuesType!="researchModule")// in case of challenge ques, remedial & DCT ques no not applicable
	                      	$quesno++;
                        $_SESSION["qno"] = $quesno;
					}
					else
					{
						$tmpResult = nextExerciseQuestion($userID,$clusterCode,$quesno,$sessionID,SUBJECTNO,$qcode,$response,$secs,$responseResult);
				        $tmpResult = explode("~",$tmpResult);
	                    $qcode = $tmpResult[0];

						if($qcode!="-10")
						{
							$quesno++;
							$tmpMode     = "question";
							$curQuesType = "exercise";
							$showAnswer  = 1;
						}
					}

                    //Check if exceeded todays limit on time spent
					if($quesCategory!="daTest")
					{
						$limitExceeded = hasExceededTodaysLimit($userID);
						if($limitExceeded!=0)
						{
							$_SESSION["limitExceeded"] = $limitExceeded;
							$qcode = $limitExceeded;                //set the response to -5 which will be handled in the question page
						}
					}

					if($_SESSION['clusterCompleted'] == 1)  {
						$_SESSION['clusterCompleted'] = 0;
						$testType = $_SESSION['kstdiagnosticTest']['featureType'];
						if(preg_match('(with pre-requisite)', strtolower($testType)) === 1){
							//check whether it has misconception code or not
								$_SESSION['current_cluster'] = get_current_cluster($teacherTopicCode,$userID);
								$attemptID	=	getAttemptID($userID, $teacherTopicAttemptID);
								$_SESSION['clusterAndLearningObjective'] = getClusterAndMisconception($userID,$attemptID);
							if(isset($_SESSION['clusterAndLearningObjective']) && $_SESSION['clusterAndLearningObjective'] !="" ){
									setComprehensiveFlowForKst($_SESSION['clusterAndLearningObjective'],$userID,$testType,$teacherTopicCode,$teacherTopicAttemptID,$sessionID);
									$_SESSION['clusterCompleted'] = 0;
									$response	=	array();
									$response["tmpMode"]	=	"kstSubModule";
									echo json_encode($response);
									break;
								}
						}
					}
                    //Construct the response to be sent back to the question page
                    $response = createResponse($qcode,$quesno,$curQuesType,$showAnswer,$tmpMode,$choiceFlag);
					if($response=="")
					{
						sendDataCheckMail($qcode,"5");
					}
                    echo $response;
                    break;
    case "topicRevision":
    				$userID = $_POST['userID'];
    				$cls    = $_POST['cls'];
    				$teacherTopicCode = $_POST['ttCode'];
    				setTeacherTopic($teacherTopicCode);
    				$quesno = 1;
    				$_SESSION['teacherTopicCode'] = $teacherTopicCode;
    				$clusterArray = getClusters($teacherTopicCode,$cls, $userID);
					$_SESSION['totalCorrect'] = 0;
					$_SESSION['totalRevisionQuesAttempt'] = 0;
					$_SESSION['topicRevisionAttemptNo'] = getTopicRevisionAttemptNo($teacherTopicCode,$userID,$_SESSION["sessionID"]);
    				$_SESSION['quesAttempted'] = initializeQuesArrayForTopicRevision($clusterArray);
    				$prevCluster = "";
    				$nextQuesDetails = findNextQuesForTopicRevision($clusterArray, $prevCluster, $quesno);
    				$nextQuesDetails = explode("-",$nextQuesDetails);

    				$qcode = $nextQuesDetails[1];
    				$_SESSION['qcode'] = $qcode;
    				$_SESSION["clusterCode"] = $nextQuesDetails[0];
    				$quesCategory = "topicRevision";
					echo '<html>';
                    echo '<body>';
    				echo '<form id="frmHidForm" action="question.php" method="post">';
                    echo '<input type="hidden" name="qcode" id="qcode" value="'.$qcode.'">';
                    echo '<input type="hidden" name="qno" id="qno" value="'.$quesno.'">';
                    echo '<input type="hidden" name="quesCategory" id="quesCategory" value="'.$quesCategory.'">';
                    echo '<input type="hidden" name="showAnswer" id="showAnswer" value="1">';
                    echo '</form>';
                    echo "<script>
                             document.getElementById('frmHidForm').submit();
                          </script>";
                    echo '</body>';
                    echo '</html>';
					break;

	case "saveremedialItemStatus":
					//echo 'inside remedialItemStatus';die;
    				$sessionID        = $_SESSION['sessionID'];
    				$ttAttemptID      = $_SESSION['teacherTopicAttemptID'];
    				$teacherTopicCode = $_SESSION['teacherTopicCode'];
    				$clusterAttemptID = $_SESSION["clusterAttemptID"];
                    $clusterCode      = $_SESSION["clusterCode"];
					$remedialItemCode = $_POST['remedialItemCode'];
					$result           = $_POST['result'];
					$timeTaken        = $_POST['totalTimeTaken']==""?0:$_POST['totalTimeTaken'];
					$fromSDL		  = $_POST['fromSDL'];
					$learningGoalStatus = $_POST['learningGoalStatus'];
					$noOfPromptsUsed  = $_POST['noOfPrompts'];
					$activityFormat	=	$_POST['activityFormat'];
					//Check if the remedial is given at an SDL level or due to the failure on the cluster.
					$query  = "SELECT result FROM ".TBL_CLUSTER_STATUS." WHERE clusterAttemptID=$clusterAttemptID";					
					$cluster_result = mysql_query($query) or die("Error in fetching the data!");
					$line   = mysql_fetch_array($cluster_result);
					$remedialmode   = $line['result']==""?"Sdl":"Cluster";		
					$isComprehensive =0;
					if($activityFormat=="new")
					{
						$remedialAttemptID	=	$_POST["remedialAttemptID"];
						$levelsAttempted	=	$_POST["levelsAttempted"];
						$levelWkiseStatus	=	$_POST["levelWiseStatus"];
						$levelWiseScore		=	$_POST["levelWiseScore"];
						$levelWiseTimeTaken	=	$_POST["levelWiseTimeTaken"];
						$extraParameters	=	$_POST["extraParameters"];
						$levelsAttemptedArr		=	explode("|",$levelsAttempted);
						$levelWiseStatusArr		=	explode("|",$levelWiseStatus);
						$levelWiseScoreArr		=	explode("|",$levelWiseScore);
						$levelWiseTimeTakenArr	=	explode("|",$levelWiseTimeTaken);
						$extraParametersArr		=	explode("|",$extraParameters);
						$timeTaken	=	array_sum($levelWiseTimeTakenArr);
						for($n=0;$n<count($levelsAttemptedArr);$n++)
						{
							$level	=	str_replace("L","",$levelsAttemptedArr[$n]);
							$sq	=	"UPDATE adepts_activityLevelDetails
									 SET score=".$levelWiseScoreArr[$n].", timeTaken='".$levelWiseTimeTakenArr[$n]."', status=".$levelWiseStatusArr[$n].",
									  extraParams='".$extraParametersArr[$n]."'
									 WHERE srno=".$remedialAttemptID." AND level=".$level." AND type='remedial'";
							$rs	=	mysql_query($sq);
						}
						$query = "UPDATE adepts_remedialItemAttempts SET result=$result, timeTaken='$timeTaken' WHERE remedialAttemptID=$remedialAttemptID";
					}
					else
					{
						if($remedialmode=="Sdl")
						{
							$query = "INSERT INTO adepts_remedialItemAttempts (userID, sessionID, remedialItemCode, clusterAttemptID, result, timeTaken, fromSDL)
									   VALUES ($userID, $sessionID, '$remedialItemCode', $clusterAttemptID, $result, $timeTaken, $fromSDL) ";
						}
						else
						{
							$query = "INSERT INTO adepts_remedialItemAttempts (userID, sessionID, remedialItemCode, clusterAttemptID, result, timeTaken)
									   VALUES ($userID, $sessionID, '$remedialItemCode', $clusterAttemptID, $result, $timeTaken) ";
						}
					}
					$remedialItemAttemptID = "";
					mysql_query($query);
					if($activityFormat=="old")
						$remedialItemAttemptID = mysql_insert_id();
					if($remedialItemAttemptID!="0" && $remedialItemAttemptID!="")
					{
						$query = "INSERT INTO adepts_learningGoalDetails(remedialAttemptID,learningGoal,status,noOfPrompts,userID) VALUES ";
						$learningGoalArray = explode(",",$learningGoalStatus);
						$promptsArray = explode(",", $noOfPromptsUsed);
						for($j=0; $j<count($learningGoalArray);$j++)
						{
							$noOfPrompts = $promptsArray[$j]==""?0:$promptsArray[$j];

							$learningGoal = $j+1;
							$query .= " ($remedialItemAttemptID, $learningGoal, '".$learningGoalArray[$j]."',$noOfPrompts,".$_SESSION["userID"]."),";
						}
						$query = substr($query,0,-1);
						mysql_query($query);
					}
					if($_SESSION["comprehensiveModule"]!="")
					{
						$isComprehensive=1;
						if($remedialItemAttemptID!="")
							$activityAttemptID	=	$remedialItemAttemptID;
						else
							$activityAttemptID	=	$remedialAttemptID;
						$sq	=	"UPDATE adepts_userComprehensiveFlow SET activityAttemptID=$activityAttemptID ,timeTaken='$timeTaken', status=1 WHERE flowAttemptID=".$_SESSION["currentFlowID"];
						if($rs=mysql_query($sq))
						{
						}
						else
						{
							$sqError = "INSERT INTO adepts_errorLogs SET bugType='remedial-comprehensive',bugText='".json_encode($sq)."',qcode='".$_SESSION["comprehensiveModule"]."',userID=".$_SESSION['userID'].",sessionID=".$_SESSION['sessionID'].",schoolCode=".$_SESSION['schoolCode'];
							mysql_query($sqError);
						}
						getNextComprehensiveInflow();
					}
					if($_SESSION["subModule"]!="")
					{
						$isComprehensive=1;
						if($remedialItemAttemptID!="")
							$activityAttemptID	=	$remedialItemAttemptID;
						else
							$activityAttemptID	=	$remedialAttemptID;
						$sq	=	"UPDATE educatio_adepts.kst_userFlowDetails SET activityAttemptID=$activityAttemptID ,timeTaken='$timeTaken', status=1 WHERE flowAttemptID=".$_SESSION["currentFlowID"];
						if($rs=mysql_query($sq))
						{
						}
						else
						{
							$sqError = "INSERT INTO adepts_errorLogs SET bugType='remedial-kstSubModule',bugText='".json_encode($sq)."',qcode='".$_SESSION["comprehensiveModule"]."',userID=".$_SESSION['userID'].",sessionID=".$_SESSION['sessionID'].",schoolCode=".$_SESSION['schoolCode'];
							mysql_query($sqError);
						}
						$sqSetCurrentActivityDetail	=	"UPDATE educatio_adepts.kst_ModuleAttempt SET currentActivityDetail='' WHERE srno=".$_SESSION['subModule_srno'];
						mysql_query($sqSetCurrentActivityDetail);
						getNextKstSubModuleInflow();
						redirectToKstFlow();
					}
					$nextPage = "question.php";
					$qcode = -1;
					$mode  = $remedialItemCode = "";
					//check comprehensive modeule next in flow
					redirectToFlow();
					if($_SESSION['comprehensiveModule']!="" || $_SESSION['subModule']!="")
						break;
					if(!$isComprehensive)
					{
						if($remedialmode=="Sdl")
						{
							//If remedial item complete, take the student to the next higher SDL
							if($result==1)
							{
								$currentSDL = findHigherSDL($fromSDL);
								if ($currentSDL != -1)
							        $qcode = findQuestion($currentSDL);
							}
							else	//Check if 50% sdls covered, if yes and score<60%, treat as cluster failure else take him to next higher sdl
							{
								$perSDLsCovered = getPerOfSDLsCovered($fromSDL);
		                	    $currentSDL     = findHigherSDL($fromSDL);
		                	    if($currentSDL != -1)
		                	    {
		                		    if($perSDLsCovered>=50)
		                		    {
		                			    $totalScore = calcTotalScore($clusterAttemptID);
		                			    if($totalScore>=60)
		                			        $qcode = findQuestion($currentSDL);
		                		    }
		                		    else
		                		        $qcode = findQuestion($currentSDL);
		                	    }
							}

							if($qcode==-1)
							{
								$clusterAttemptNo  = $_SESSION['clusterAttemptNo'];
								$_SESSION['clusterCompleted'] = 1;
								$returnArray = determineNextCluster($userID, $clusterAttemptID, $clusterCode, $ttAttemptID, $teacherTopicCode, $sessionID, $clusterAttemptNo);
								if($returnArray[0]!="remedial")
									$clusterCode = $returnArray[1];
								else
								{
									$qcode = $returnArray[1];
									$nextPage = "remedialItem.php";
								}
							}
						}
						else //check if any remedial pending to be given at the end of the cluster.
						{
							$qcode = getRemedialItemAtTheEndOfTheCluster($ttAttemptID, $clusterAttemptID);
							if($qcode!="")
							{
								$nextPage = "remedialItem.php";
								$remedialItemCode = $qcode;
							}
							else
							{
								$clusterCode = nextCluster($userID,$teacherTopicCode,$ttAttemptID,$sessionID,$clusterCode,"FAILURE", $clusterAttemptID);
								$qcode = -1;
							}
						}

						if($_SESSION['clusterCompleted'] == 1)  {
								$_SESSION['current_cluster'] = get_current_cluster($teacherTopicCode,$userID);
								$attemptID	=	getAttemptID($userID, $teacherTopicAttemptID);
									$_SESSION['clusterAndLearningObjective'] = getClusterAndMisconception($userID,$attemptID);
									if(isset($_SESSION['clusterAndLearningObjective']) && $_SESSION['clusterAndLearningObjective'] !="" ){
										setComprehensiveFlowForKst($_SESSION['clusterAndLearningObjective'],$userID,$testType,$teacherTopicCode,$teacherTopicAttemptID,$sessionID);
										$_SESSION['clusterCompleted'] = 0;
										redirectToKstFlow();
									}
									$_SESSION['clusterCompleted'] = 0;
						}

						if ($qcode == -1 && ($_SESSION['comprehensiveModule']=="" || $_SESSION['subModule']==""))
						{
							if ($clusterCode == -1) {
								$mode     = -3;
								$nextPage = "endSessionReport.php";                    //End of Topic, Topic Success;
							}
							elseif ($clusterCode == -2) {	//Class level completed in the TT
								$mode     = -8;
								$nextPage = "classLevelCompletion.php";
							}
							else
							{
								$currentSDL = findMinSDL();
								//echo "Cur SDL-$currentSDL";
								$qcode = findQuestion($currentSDL);
							}
						}
						if($mode=="" )
						{
							updateSession($userID, $clusterCode,$currentSDL,$qcode, $teacherTopicCode, $remedialItemCode);
							manipulateArrays($currentSDL,$qcode, "normal", "");
						}
					}
					$quesno = $_SESSION["qno"];

					//check comprehensive modeule next in flow------ends here
					echo '<html>';
					echo '<body>';
					echo '<form id="frmHidForm" action="'.$nextPage.'" method="post">';
					echo '<input type="hidden" name="qcode" id="qcode" value="'.$qcode.'">';
					echo '<input type="hidden" name="qno" id="qno" value="'.$quesno.'">';
					if(strtolower($comprehensiveModuleDetailsArr[2])=="timedtest")
						echo '<input type="hidden" name="quesCategory" id="quesCategory" value="'.$quesCategory.'">';
					else
						echo '<input type="hidden" name="quesCategory" id="quesCategory" value="normal">';
					echo '<input type="hidden" name="showAnswer" id="showAnswer" value="1">';
					echo '<input type="hidden" name="mode" id="mode" value="'.$mode.'">';
					echo '</form>';
					echo "<script>
							 document.getElementById('frmHidForm').submit();
						  </script>";
					echo '</body>';
					echo '</html>';
    case "endsession":
					/* UPDATE PROGRESS UNSET UPDATE FLAG - PROGRESS*/
					if($_SESSION['topicRevisionAttemptNo']>0)
						updateRevisionQues($_SESSION['teacherTopicCode'],$_SESSION['sessionID'],$_SESSION['topicRevisionAttemptNo'],$_SESSION['totalCorrect'],$_SESSION['totalRevisionQuesAttempt']);
					if($_SESSION["updateProgress"] )
					{
						$progressUpdateObj = new topicProgressCalculation($_SESSION['teacherTopicCode'],$_SESSION['childClass'],$_SESSION['flow'],$_SESSION["teacherTopicAttemptID"],SUBJECTNO);
						$progressUpdateObj->updateProgress();
						$_SESSION["updateProgress"] = false;
					}
                    $sessionID = $_SESSION['sessionID'];
                    $code = $_REQUEST['code'];                    
                    setEndTime($sessionID, $code);
                    break;
    case "topicSwitch":
					if($_SESSION["updateProgress"])
					{
						/* UPDATE PROGRESS UNSET UPDATE FLAG - PROGRESS*/
						$progressUpdateObj = new topicProgressCalculation($_SESSION['teacherTopicCode'],$_SESSION['childClass'],$_SESSION['flow'],$_SESSION["teacherTopicAttemptID"],SUBJECTNO);
						$progressUpdateObj->updateProgress();
						$_SESSION["updateProgress"] = false;
					}					
					if(isset($_REQUEST["from"]) && $_REQUEST["from"]=="endSession")
						mysql_query("UPDATE ".TBL_SESSION_STATUS." SET endTime=NULL,endType=(case when ISNULL(endType) then '11' else concat_ws(',',endType,'11') end) WHERE sessionID=".$_SESSION['sessionID']);
					else
						mysql_query("UPDATE ".TBL_SESSION_STATUS." SET endTime=NULL,endType=(case when ISNULL(endType) then '10' else concat_ws(',',endType,'10') end) WHERE sessionID=".$_SESSION['sessionID']);
					
					//$summerTopicCode	=	array('TT11027','TT11062','TT11097','TT11081','TT11080','TT11082','TT11073','TT11074','TT11076','TT11075','TT11067'); //for summer program
					if($_SESSION['teacherTopicCode'] == "NCERT")
					{
						$nextPage = "homeworkSelection.php";
					}
					else if(isset($_REQUEST["from"]) && $_REQUEST["from"]=="endSession")
					{
						$nextPage = "home.php";
					}
					/*else if(in_array($_SESSION['teacherTopicCode'],$summerTopicCode))  //for summer program
					{
						$nextPage = "dashboard.php?programMode=summerProgram";
                    	setInactive($_SESSION['teacherTopicAttemptID'], $userID);
					}*/
					else
					{
						$nextPage = "dashboard.php";
                    	setInactive($_SESSION['teacherTopicAttemptID'], $userID);
					}
                    echo '<html>';
                    echo '<body>';
                    echo '<form id="frmHidForm" action="'.$nextPage.'" method="post">';
                    echo '</form>';
                    echo "<script>
                              document.getElementById('frmHidForm').submit();
                          </script>";
                    echo '</body>';
                    echo '</html>';

                    break;
    case "topicQuit":
    				$ttAttemptID = $_SESSION['teacherTopicAttemptID'];
					$teacherTopicCode = $_SESSION['teacherTopicCode'];
    				updateTeacherTopicStatus($ttAttemptID,-5);
	                deleteCurrentStatus($userID,$teacherTopicCode,$flagForOffline);
                    echo '<html>';
                    echo '<body>';
                    echo '<form id="frmHidForm" action="dashboard.php" method="post">';
                    echo '</form>';
                    echo "<script>
                              document.getElementById('frmHidForm').submit();
                          </script>";
                    echo '</body>';
                    echo '</html>';

                    break;
    case "comment":    					
            	    $assignTo = '';
            	    $dynamicParams = isset($_POST['dynamicParams']) ? $_POST['dynamicParams'] : '';
            	    $user	=	explode("_",$_SESSION["username"]);
					$username	=	$user[0];
            	    $query = '';
            	    $previousQuestion = isset($_POST['previousQuestion'])?$_POST['previousQuestion']:0;

            	    if(($_SESSION['childClass']==10 || $_SESSION['childClass']==9) && ($_SESSION['schoolCode']==1752 || $_SESSION['schoolCode']==572))
            	    {
	            	    $q = "select topicCode from adepts_clusterMaster a, adepts_subTopicMaster b,adepts_questions c where a.subTopicCode=b.subTopicCode and a.clusterCode=c.clusterCode and a.clusterCode='".$_SESSION['clusterCode']."' and qcode=".$_POST['qcode'];
	            	    $r = mysql_query($q) or die(mysql_error());
	            	    $l = mysql_fetch_array($r);

	            	    if($l['topicCode']=="EXE")
	            	    {
	            	    	$query = "INSERT INTO adepts_exerciseUserComments (userID,sessionID,questionNo,qcode,comment,queryType,status,category,assignTo,lastModified) VALUES
									(".$userID.",".$_SESSION['sessionID'].",".$_POST['quesNo'].",".$_POST['qcode'].",'".mysql_escape_string(stripslashes($_POST['comment']))."','Doubt','Open','".$_POST['selCategory']."','".$assignTo."',now())";
	            	    }
            	    }

        	    	if($query=='')
        	    	{
        	    		$userComments   =   str_replace("~", "-", $_POST['comment']);
						$userComments	=	mysql_escape_string(stripslashes($userComments));
						include_once("classes/clsbucketCommentsV2.php");
						$obj = new commentCategorization();
						$systemCategory	=	$obj->mark($userComments);
						if($_SESSION["userType"]=="msAsStudent" || $_SESSION["userType"]=="teacherAsStudent")
							$commentSource='MS as student';
						else
							$commentSource='Student Comments';

						if($_SESSION["userType"]=="teacherAsStudent")
							$commentBy	=	'Teacher';
						else if($_SESSION["userType"]=="msAsStudent")
							$commentBy	=	'Internal';
						else
							$commentBy	=	'Student';

						/*if($_POST['selCategory']=="Idea for a Question")
						{
							$status = "In-Progress";
							$assignTo = ",assignTo='athul.john'";
						}
						else
						{*/
							$assignTo = "";
							$status=($systemCategory['bucket']=='Junk' && $systemCategory['isJunk2']=='Junk')?'Ignored':'Open';
							$resolvedBy=($systemCategory['bucket']=='Junk' && $systemCategory['isJunk2']=='Junk')?", resolvedby = 'system' ":'';
							$finalComment = $resolvedBy==''?'':", finalcomment = 'Thank you for sending your comment. <br><br>Regards, <br>Mindspark Team'";
						/*}*/
						

						$sq	=	"INSERT INTO adepts_userComments SET userID=$userID, sessionID=".$_SESSION['sessionID'].", questionNo=".$_POST['quesNo'].",
								 qcode=".$_POST['qcode'].", queryType='".$_POST['selCategory']."', status='$status', category='".$systemCategory['bucket']."',
								 systemCategory='".$systemCategory['bucket']."', type='".$_POST['type']."', previousQuestionDetails='".$_POST['previousQuestionDetails']."', previousQuestion='".$previousQuestion."', comment='".$userComments."',
								 commentReceivedate=now(), commentBy='$commentBy', notRelatedToQuestion='".$_POST['notRelatedToQuestion']."',dynamicParameters='".$dynamicParams."', commentSource='$commentSource' $resolvedBy $finalComment $assignTo";
						mysql_query($sq) or die(mysql_error());
						$commentID	=	mysql_insert_id();
						$query	=	"INSERT INTO adepts_userCommentDetails SET srno=$commentID, comment='".$userComments."',
									 commentDate=now(), commenter='$username',flag=1,userID=".$_SESSION["userID"];
						if($_SESSION["schoolCode"]!="") 
							$query	.= ", schoolCode=".$_SESSION["schoolCode"];

						if ($resolvedby!=""){
            	    		mysql_query($query) or die(mysql_error());
							$query	=	"INSERT INTO adepts_userCommentDetails SET srno=$commentID ". str_replace("finalcomment", "comment", $finalComment).",
									 commentDate=now(), commenter='system',flag=2,userID=0";
							if($_SESSION["schoolCode"]!="")
								$query	.= ", schoolCode=".$_SESSION["schoolCode"];
						}
        	    	}

            	    mysql_query($query) or die(mysql_error());

                    break;

	case "tagThisQcode":
					$user	=	explode("_",$_SESSION["username"]);
					$dynamicParams = isset($_POST['dynamicParams']) ? $_POST['dynamicParams'] : '';
					$username	=	$user[0];
					$type	=	$_POST['type'];
					$sessionID             = $_SESSION['sessionID'];
					$assignTO	=	isset($_POST['assignTO'])?$_POST['assignTO']:"";
					if($assignTO=="")
						$assignTO	=	$_SESSION['topicOwner'];
					if($type=="activity")
						$sqQues	=	"UPDATE adepts_gamesMaster SET needToModify=1 WHERE gameID=$qcode";
					else if($type=="remedial")
						$sqQues	=	"UPDATE adepts_remedialItemMaster SET needToModify=1 WHERE remedialItemCode='$qcode'";
					else
						$sqQues	=	"UPDATE adepts_questions SET needToModify=1 WHERE qcode=$qcode";
					$rsQues	=	mysql_query($sqQues);

					$sqCm	=	"INSERT INTO adepts_userComments SET userID=$userID, qcode='".$qcode."', status='In-Progress', type='".$type."',
								 commentReceivedate=now(), sessionID='".$sessionID."', commentBy='Internal', assignTo='".$assignTO."', commentSource='MS as student', category='Other', dynamicParameters='".$dynamicParams."'";
					mysql_query($sqCm) or die(mysql_error());
					$commentID	=	mysql_insert_id();
					$msg = str_replace("~", "-", $msg);
					$msg = mysql_real_escape_string($msg);
					$query	=	"INSERT INTO adepts_userCommentDetails SET srno=$commentID, comment='".$msg."',
								 commentDate=now(), commenter='$username', flag=1,userID=".$_SESSION["userID"];
					if($_SESSION["schoolCode"]!="")
						$query	.= ", schoolCode=".$_SESSION["schoolCode"];
					mysql_query($query) or die(mysql_error());
					break;

	case "timedtest":
                    $sessionID             = $_SESSION['sessionID'];
                    $timedTestCode         = $_POST['timedTestCode'];
					//if(isset($_SESSION['dailyDrillArray']['isInternalRequest']) && $_SESSION['dailyDrillArray']['isInternalRequest'] == 1){
						
					//}else{
						 //Temp check since changes made in js file - so because of caching it can be based on older approach
						if(checkTimedTestBug($sessionID,$timedTestCode))
						{
							echo "dummyresponse";
							mysql_close();
							exit;
						}
						if($timedTestCode=="")
						{
							$timedTestCode  = $_SESSION['timedTest'];
							if($timedTestCode=="")
							{
								$timedTest_query  = "SELECT currentTimedTest FROM adepts_timedTestStatus WHERE userID=$userID";
								$timedTest_result = mysql_query($timedTest_query);
								$timedTest_line   = mysql_fetch_array($timedTest_result);
								$timedTestCode = $timedTest_line[0];
							}
						}
						$quesCorrect           = $_POST['quesCorrect'];
						$timeTaken             = $_POST['timeTaken'];
						$perCorrect            = $_POST['perCorrect'];
						$noOfQuesAttempted     = $_POST['noOfQuesAttempted'];
						$noOfSparkies          = $_POST['noOfSparkies'];
						$timedTestVersion	   = $_POST['timedTestVersion'];
						$quesCategory		   = $_POST['quesCategory'];
						$attemptNo			   = $_POST['attemptNo'];	
						$autoSubmitUsed        = $_POST['autoSubmitUsed'];
						$timedTestDesc         = $_POST['timedTestDesc'];
						$timedTestAttemptID = saveTimedTestDetails($userID, $timedTestCode, $sessionID, $timeTaken, $quesCorrect, $perCorrect, $noOfQuesAttempted, $timedTestVersion,$autoSubmitUsed, $quesCategory,$attemptNo,$timedTestDesc);
						$_SESSION['timedTest'] = "";
						$_SESSION["quesCategory"] = "";
						if($noOfSparkies>0 && $noOfSparkies<6){
							addSparkies($noOfSparkies,$sessionID);
							$_SESSION['sparkie']['timedTest']= $_SESSION['sparkie']['timedTest'] + $noOfSparkies;

						}
						$limitExceeded = hasExceededTodaysLimit($userID);
						if($limitExceeded!=0)
						{
							$_SESSION["limitExceeded"] = $limitExceeded;
							setEndTime($sessionID, $limitExceeded);
						}
						
						echo $limitExceeded."~".$timedTestAttemptID;
						mysql_close();
					//}
                   
                    exit;
                    break;
					
	case "saveTimedTestQuestions":
					$user	=	explode("_",$_SESSION["username"]);
					$username	=	$user[0];
                    $timedTestAttemptID = $_POST['timedTestAttemptID'];
					$fromComments = $_POST['fromComments'];
                    if($timedTestAttemptID!="" && $fromComments==0)
                    {
                        $noOfQuesInTimedTest = $_POST['noOfQuesInTimedTest'];
						$timedTestCode	=	$_POST['timedTestCode'];
                        $query = "INSERT INTO adepts_timedTestQuesAttempt (timedTestID,timedTestCode, userID, qno, question, userResponse, correctAnswer, result, S) VALUES ";
                        for($cnt=1; $cnt<=$noOfQuesInTimedTest; $cnt++)
                        {
                            $quesText   = $_POST['quesText'.$cnt];
                            $userResp   = $_POST['userResp'.$cnt];
                            $correctAns   = $_POST['correctAns'.$cnt];
                            $quesResult = $_POST['result'.$cnt];
                            $timeOfQ = $_POST['timeOfQ'.$cnt];
                            $query .= " ($timedTestAttemptID, '$timedTestCode', $userID, $cnt, '$quesText', '$userResp','$correctAns','$quesResult','$timeOfQ'),";
                        }
                        $query = substr($query,0,-1);
                        mysql_query($query) or die($query);
                    }
					if($fromComments==1)
					{
						include_once("classes/clsbucketCommentsV2.php");
						$obj = new commentCategorization();
						$systemCategory	=	$obj->mark($_POST['comment']);
						$msg = stripslashes($_POST['comment']);
						$msg = str_replace("~", "-", $msg);
						
						$status=($systemCategory['bucket']=='Junk' && $systemCategory['isJunk2']=='Junk')?'Ignored':'Open';
						$resolvedBy=($systemCategory['bucket']=='Junk' && $systemCategory['isJunk2']=='Junk')?"system":'';
						$finalComment = $resolvedBy==''?'':'Thank you for sending your comment. <br><br>Regards, <br>Mindspark Team';

						$query = "INSERT INTO adepts_userComments (userID,sessionID,questionNo,qcode,comment,queryType,category,systemCategory,status,commentReceivedate, type, commentBy, commentSource ".($resolvedBy!=""?",resolvedby":"")."".($finalComment!=""?",finalcomment":"")." ) VALUES
									(".$userID.",".$_SESSION['sessionID'].",".$timedTestAttemptID.",'".$_POST['timedTestCode']."','".mysql_escape_string($msg)."','Doubt','".$systemCategory['bucket']."','".$systemCategory['bucket']."','$status',now(),'timedtest','Student','Timedtest'".($resolvedBy!=""?",'$resolvedBy'":"").($finalComment!=""?",'$finalComment'":"").")";
						mysql_query($query) or die(mysql_error());
						$commentID = mysql_insert_id();
						$query	=	"INSERT INTO adepts_userCommentDetails SET srno=$commentID, comment='".mysql_escape_string($msg)."',
								 commentDate=now(), commenter='$username', flag=1,userID=".$_SESSION["userID"];
						if($_SESSION["schoolCode"]!="")
							$query	.= ", schoolCode=".$_SESSION["schoolCode"];
						mysql_query($query) or die(mysql_error());

						if ($resolvedby!=""){
							$query	=	"INSERT INTO adepts_userCommentDetails SET srno=$commentID , comment='". $finalComment."',
									 commentDate=now(), commenter='system',flag=2,userID=0";
							if($_SESSION["schoolCode"]!="")
								$query	.= ", schoolCode=".$_SESSION["schoolCode"];
            	    		mysql_query($query) or die(mysql_error());
						}
					}

                  break;

	case "saveTimedTestResearchQuestions":
                    $timedTestAttemptID = $_POST['timedTestAttemptID'];
                    if($timedTestAttemptID!="")
                    {
						$timedTestCode	=	$_POST['timedTestCode'];
                        $noOfQuesInTimedTest = $_POST['noOfQuesInTimedTest'];
						$quesCategory	=	$_POST['checkQuesCategory'];
                        $query = "INSERT INTO adepts_timedTestQuesAttempt (timedTestID, timedTestCode, userID, qno, question, userResponse, correctAnswer, result, mode, S) VALUES ";
                        for($cnt=1; $cnt<=$noOfQuesInTimedTest; $cnt++)
                        {
                            $quesText   = $_POST['quesText'.$cnt];
                            $userResp   = $_POST['userResp'.$cnt];
                            $correctAns   = $_POST['correctAns'.$cnt];
                            $quesResult = $_POST['result'.$cnt];
                            $timeOfQ = $_POST['timeOfQ'.$cnt];
                            $query .= " ($timedTestAttemptID, '$timedTestCode',  $userID, $cnt, '$quesText', '$userResp','$correctAns','$quesResult','$quesCategory','$timeOfQ'),";
                        }
                        $query = substr($query,0,-1);
                        mysql_query($query);
                    }
                    break;

    case "game":
              		$sessionID             = $_SESSION['sessionID'];
                    $ttAttemptID 		   = $_SESSION["teacherTopicAttemptID"];
                    $_SESSION['gamePlayed']	= 1;
                    if(isset($_POST['gameID']) && $_POST['gameID']!="ques")	//this would be set when the user is given a choice of games and he has selected one of them.
                    {
						$query  = "UPDATE adepts_gameStatus SET currentGame='".$_POST['gameID']."', level='1' WHERE userID=$userID";
						mysql_query($query);
                    }

                    $html  = '<html>';
	                $html .= '<body>';
					$query  = "SELECT gameID FROM ".TBL_CURRENT_STATUS." WHERE userID=$userID AND ttAttemptID=$ttAttemptID";
					$result = mysql_query($query);
					$line   = mysql_fetch_array($result);
					$gameID = "";
					if($line["gameID"]!="")
					{
						$gameID = $line["gameID"];
						$phpFile = "enrichmentModule.php";
					}
					else
					{
						$phpFile = "question.php";
					}
					$html .= '<form id="frmHidForm" action="'.$phpFile.'" method="post">';
					if($gameID!="")
					{
						$html .= '<input type="hidden" name="gameID" id="gameID" value="'.$gameID.'">';
						$html .= '<input type="hidden" name="gameMode" id="gameMode" value="afterCluster">';
					}
					else
					{
						$html .= '<input type="hidden" name="qcode" id="qcode" value="'.$qcode.'">';
  						$html .= '<input type="hidden" name="qno" id="qno" value="1">';
  						$html .= '<input type="hidden" name="quesCategory" id="quesCategory" value="normal">';
	                    $html .= '<input type="hidden" name="showAnswer" id="showAnswer" value="'.$showAnswer.'">';
					}

	                $html .= '</form>';
                    $html .= "<script>
                      	          document.getElementById('frmHidForm').submit();
                              </script>";
                    $html .= '</body>';
                    $html .= '</html>';
                    echo $html;
					mysql_close();
                    exit;
                    break;
					
    case "saveGameDetails":
					$sessionID             = $_SESSION['sessionID'];
					$ttAttemptID 		   = $_SESSION["teacherTopicAttemptID"];
					$gameID				   = $_POST['gameID'];
					$level                 = $_POST['level'];
					$totalScore            = $_POST['totalScore'];
					$timeTaken			   = $_POST['timeTaken'];
					$status = saveGameDetails($userID, $gameID, $sessionID, $timeTaken,$totalScore,$level);
					$limitExceeded = hasExceededTodaysLimit($userID);
					if($limitExceeded!=0)
                    {
						$_SESSION["limitExceeded"] = $limitExceeded;
                        setEndTime($sessionID, $limitExceeded);
                    }
                    if($limitExceeded==0)
                      	echo $status;
                    else
                       	echo $limitExceeded;
					mysql_close();	
                    exit;
                    break;
					
    case "timeTakenForExpln":
					$qid = $_SESSION['qid'];
					$timeTaken  = $_POST['timeTaken'];
					$question_type  = $_POST['question_type'];
					$qcode  = $_POST['qcode'];
					$quesCategory  = $_POST['quesCategory'];
					$sparkieExplaination	=	$_POST['sparkieExplaination'];
					// $daComment = $_POST["daComment"];
					// $displayAnsRating = $_POST["displayAnsRating"];
					$userID = $_SESSION['userID'];
					$sessionID = $_SESSION['sessionID'];
					
					/*For Dispaly answer rating*/
					/*if($displayAnsRating!="")
					{
						saveDisplayAnswerRating($qcode,$userID,$sessionID,$displayAnsRating,$daComment);
					}*/
					/*For wild card questions*/
					if($_POST['wildCardText'] && $question_type == 'wildcard')
					{
						saveWildcardUserResponsesText(0,$qcode);
					}
					/*if($sparkieExplaination==1 && $_SESSION['rewardSystem']==1 && $quesCategory!="topicRevision" && $quesCategory!="practiseModule")
					{
						addSparkies(1, $_SESSION['sessionID']);
						$_SESSION['sparkie']['sparkieExplaination']++;
					}*/

					if($qid!="")
					{
						$query = "UPDATE ".TBL_QUES_ATTEMPT_CLASS." SET timeTakenForExpln=$timeTaken WHERE srno=$qid";
						mysql_query($query);
					}
					else if(isset($_SESSION["cqsrno"]) && $_SESSION["cqsrno"] != "" && $markedWrong == "yes" && $question_type=="challenge" && $systemCategory!="Junk")
					{
						unset($_SESSION["cqsrno"]);
					}
					break;
	
    case "classLevelCompletion":
    				$higherLevel = $_POST['higherLevel'];
    				$fromQuesPage = isset($_POST['fromQuesPage'])?1:0;
    				$fromChoiceScreen = isset($_POST['fromChoiceScreen'])?1:0;
    				$sessionID   = $_SESSION['sessionID'];
					$ttAttemptID = $_SESSION["teacherTopicAttemptID"];
					$teacherTopicCode = $_SESSION['teacherTopicCode'];
					if(!$higherLevel)	//i.e. if the user choses not to go to a higher level.
					{
						if (!$fromQuesPage)
						{
							updateTeacherTopicStatus($ttAttemptID,-4); //set the result (i.e. advanced topic completion to Not attempted - NA)
	                    	deleteCurrentStatus($userID,$teacherTopicCode,$flagForOffline); //remove the entry from the current status, so that next time he starts from the first cluster
	                    	//Redirect to the topic selection screen
	                	}
	                    echo '<html>';
	                    echo '<body>';
	                    echo '<form id="frmHidForm" action="dashboard.php" method="post">';
	                    echo '</form>';
	                    echo "<script>
	                              document.getElementById('frmHidForm').submit();
	                          </script>";
	                    echo '</body>';
	                    echo '</html>';
					}
					else
					{
						$_SESSION['higherLevel'] = 1;
						$clusterCode      = $_SESSION["clusterCode"];
						$childClass       = $_SESSION['childClass'];
						$_SESSION['quesCorrectInALevelOfTopic'] = 0;
    					//$flow             = isset($_SESSION['flow'])?$_SESSION['flow']:"";
    					$query = "SELECT flow FROM ".TBL_TOPIC_STATUS." WHERE ttAttemptID=$ttAttemptID";
						$flow_result = mysql_query($query);
						$flow_line = mysql_fetch_array($flow_result);
						$flow = $flow_line[0];
    					$objTT            = new teacherTopic($teacherTopicCode,$childClass,$flow);
    					$nextCluster      = $objTT->findNextNormalCluster($clusterCode);

						$attemptType = "N";
						insertClusterData($userID, $ttAttemptID, $nextCluster, $attemptType, $sessionID,$teacherTopicCode);
						$currentSDL = findMinSDL();
			            $qcode = findQuestion($currentSDL);
			            $questionType = "normal";
			            updateSession($userID, $nextCluster,$currentSDL,$qcode, $teacherTopicCode,"");
			            manipulateArrays($currentSDL,$qcode, $questionType, "");
						unset($_SESSION['choiceScreenAfterBonusCQ']);unset($_SESSION['choiceScreenAfterTimedTest']);
			            $quesno = $_SESSION["qno"];
			            echo '<html>';
	                    echo '<body>';
	                    echo '<form id="frmHidForm" action="question.php" method="post">';
	                    echo '<input type="hidden" name="qcode" id="qcode" value="'.$qcode.'">';
	                    echo '<input type="hidden" name="qno" id="qno" value="'.$quesno.'">';
	                    echo '<input type="hidden" name="quesCategory" id="quesCategory" value="normal">';
	                    echo '<input type="hidden" name="showAnswer" id="showAnswer" value="1">';
	                    echo '</form>';
	                    echo "<script>
	                             document.getElementById('frmHidForm').submit();
	                          </script>";
	                    echo '</body>';
	                    echo '</html>';

					}
					break;

    case "revisionSession":
					$sessionID         = $_SESSION['sessionID'];
					$revisionSessionID = $_POST['revisionSessionID'];
					$qno               = $_POST['qno'];
					$qcode             = $_POST['qcode'];
					$clusterCode       = $_POST['clusterCode'];
					$ttCode            = $_POST['ttCode'];
					$sdl               = $_POST['sdl'];
					$result            = $_POST['result'];
					$response          = $_POST['userResponse'];
					$timeTaken         = $_POST['secsTaken'];
					$dynamic           = $_POST['dynamicQues'];
					$dynamicParams     = $_POST['dynamicParams'];
					$ttCode            = $_POST['ttCode'];
						//print_r($_POST);
                    $dup = removeSession(TBL_SESSION_STATUS, $sessionID);              //set the response to -5 which will be handled in the question page
					if($dup=="endsession")
					{
						echo '<html><body>';
						echo '<form id="frmHidForm" action="error.php" method="post"></form>';
						echo "<script>document.getElementById('frmHidForm').submit();</script>";
						echo '</body></html>';
						exit;
					}
					saveRevisionQuesDetails($revisionSessionID,$userID,$qno,$qcode, $clusterCode, $ttCode, $sessionID, $result, $response, $timeTaken, $dynamic,$dynamicParams, $_SESSION['childClass']);
					$quesAttemptedArray = $_SESSION['revisionSessionClusterArray'];

					if($quesAttemptedArray[$ttCode][strtoupper($clusterCode)][$sdl][0]!="")
						$quesAttemptedArray[$ttCode][strtoupper($clusterCode)][$sdl][0] .= ",".$qcode;
					else
						$quesAttemptedArray[$ttCode][strtoupper($clusterCode)][$sdl][0]   = $qcode;
					$_SESSION['revisionSessionClusterArray'] = $quesAttemptedArray;
					//Max 30 ques to be given in a revision session
					$revisionSessionComplete = 0;
					if($qno==30)
						$revisionSessionComplete = 1;
					else
					{
						$timeRemaining = isRevisionSessionTimeRemaining();
						if($timeRemaining)
						{
							$qcodeDetails = getNextQuestionForRevisionSession($userID,$revisionSessionID, $ttCode,$clusterCode);
							$qcode = $qcodeDetails[0];
							if($qcode=="")
								$revisionSessionComplete = 1;
						}
						else
							$revisionSessionComplete = 1;
					}
					echo '<html>';
					echo '<body>';
					if($revisionSessionComplete)
					{
						updateRevisionSessionStatus($userID, $revisionSessionID);
						echo '<form id="frmHidForm" action="revisionSessionReport.php" method="post">';
						echo '<input type="hidden" name="revisionSessionID" id="revisionSessionID" value="'.$revisionSessionID.'">';
						echo '</form>';
						echo "<script>
                        		document.getElementById('frmHidForm').submit();
                              </script>";
					}
					else
					{
						$qno = $qno + 1;

						echo '<form id="frmHidForm" action="revisionSessionQuestion.php" method="post">';
						echo '<input type="hidden" name="qcode" id="qcode" value="'.$qcode.'">';
						echo '<input type="hidden" name="qno" id="qno" value="'.$qno.'">';
						echo '<input type="hidden" name="sdl" id="sdl" value="'.$qcodeDetails[3].'">';
						echo '<input type="hidden" name="clusterCode" id="clusterCode" value="'.$qcodeDetails[1].'">';
						echo '<input type="hidden" name="ttCode" id="ttCode" value="'.$qcodeDetails[2].'">';
						echo '<input type="hidden" name="revisionSessionID" id="revisionSessionID" value="'.$revisionSessionID.'">';
						echo '</form>';

						echo "<script>
	                           	document.getElementById('frmHidForm').submit();
	                          </script>";

					}
					echo '</body>';
					echo '</html>';
					break;

	case "worksheet":
			        echo '<html>';
			        echo '<body>';
			        echo "Loading...";

					$worksheetID = $_POST['wsm_id'];
					$_SESSION['clusterCode'] = $worksheetID;
					$_SESSION['worksheetID'] = $worksheetID;
					$_SESSION['teacherTopicCode'] = "WS";
					$_SESSION['teacherTopicName'] = "Worksheets";
					$userID = $_SESSION['userID'];
					$sessionID = $_SESSION["sessionID"];
			       	$_SESSION['timedTest']  = "";
			    	$_SESSION['game']		= false;
			    	// $_SESSION["noOfJumps"] = 0;
			    	$_SESSION["remedialStack"] = "";
			       	$_SESSION['timedTest']  = "";
					
					$attemptDetailArr = setWorksheetDetails($worksheetID);

					$worksheetAttemptID = $attemptDetailArr["worksheetAttemptID"];
					$timeLeft = $attemptDetailArr["spend_time"];
					$questionNo = $attemptDetailArr["questionNo"];
					$worksheetStatus = $attemptDetailArr["status"];
					$schoolCode= $_SESSION['schoolCode'];
					$class = $_SESSION['childClass'];
					$section = $_SESSION['childSection'];
					if($_SESSION["userType"]!="msAsStudent" && $_SESSION["userType"]!="teacherAsStudent")
						$_SESSION["userType"] = (isset($_POST['userType']) && $_POST['userType'] != "")?$_POST['userType']:"";

					$worksheetDetail = getWorksheetDetail($worksheetID, $worksheetAttemptID, $class, $section, $schoolCode);
					
					$worksheetName = $worksheetDetail['name'];
					$worksheetQList = $worksheetDetail['qList'];
					$worksheetQCount = $worksheetDetail['qcount'];
					$worksheetThumbnail = $worksheetDetail['thumbnail'];
					$worksheetDuration = $worksheetDetail['duration'];
					$worksheetEndDateTime = $worksheetDetail['end_datetime'];
					$worksheetAllQs = $worksheetDetail['allQs'];
					$worksheetCompletedQs = $worksheetDetail['completedQs'];
					$_SESSION['worksheetAttemptID'] = $worksheetAttemptID;

					$quesCategory = "worksheet";
			        if ($worksheetDetail['wsActive']==1 && $attemptDetailArr['completedWorksheet']==0)
			            $nextPage = "question.php";
			        else 
			        	$nextPage = "worksheetSelection.php";
					if($_SESSION["userType"]=="msAsStudent")
						$_SESSION["theme"] = 0;
					echo '<form id="frmHidForm" action="'.$nextPage.'" method="post">';
			        echo '<input type="hidden" name="qcode" id="qcode" value="">';
			        echo '<input type="hidden" name="worksheetID" id="worksheetID" value="'.$worksheetID.'">';
			        echo '<input type="hidden" name="worksheetAttemptID" id="worksheetAttemptID" value="'.$worksheetAttemptID.'">';
			        echo '<input type="hidden" name="qno" id="qno" value="'.$questionNo.'">';
			        echo '<input type="hidden" name="quesCategory" id="quesCategory" value="'.$quesCategory.'">';
			        echo '<input type="hidden" name="worksheetName" id="worksheetName" value="'.$worksheetName.'">';
			        echo '<input type="hidden" name="timeLeft" id="timeLeft" value="'.$timeLeft.'">';
			        echo '<input type="hidden" name="totalQs" id="totalQs" value="'.$worksheetQCount.'">';
			        echo '<input type="hidden" name="wsActive" id="wsActive" value="'.$worksheetDetail['wsActive'].'">';
					
			        echo '</form>';
			        echo "<script>
								document.getElementById('frmHidForm').submit();
			              </script>";
			        echo '</body>';
			        echo '</html>';
			        break;

	case "ncert":
			        echo '<html>';
			        echo '<body>';
			        echo "Loading...";

					$clusterCode = $_POST['clusterCode'];
					$_SESSION['clusterCode'] = $exerciseCode;
					$_SESSION['teacherTopicCode'] = "NCERT";
					$_SESSION['teacherTopicName'] = "NCERT Exercise";
					$userID = $_SESSION['userID'];
					$sessionID = $_SESSION["sessionID"];
			       	$_SESSION['timedTest']  = "";
			    	$_SESSION['game']		= false;
			    	$_SESSION["noOfJumps"] = 0;
			    	$_SESSION["remedialStack"] = "";
			       	$_SESSION['timedTest']  = "";
					
					$attemptDetailArr = setNCERTHomework($exerciseCode);
					$attemptID = $attemptDetailArr["ncertAttemptID"];
					$attemptNo = $attemptDetailArr["ncertAttemptNo"];
					$addQuesAttempts = $attemptDetailArr["addQuesAttempts"];
					$qcode = $attemptDetailArr["qcode"];
					$questionNo = $attemptDetailArr["questionNo"];
					$schoolCode= $_SESSION['schoolCode'];
					$class = $_SESSION['childClass'];
					$section = $_POST['section'];
					if($_SESSION["userType"]!="msAsStudent" && $_SESSION["userType"]!="teacherAsStudent")
						$_SESSION["userType"] = (isset($_POST['userType']) && $_POST['userType'] != "")?$_POST['userType']:"";
					$exerciseDetail = getExerciseDetail($exerciseCode, $attemptID, $class, $section, $schoolCode);
					
					$exerciseName = $exerciseDetail[0];
					$totalGroups = $exerciseDetail[1];
					$dueDate = $exerciseDetail[2];
					$completedGroups = $exerciseDetail[3];
					$_SESSION['ncertAttemptID'] = $attemptID;

					loadArrays($exerciseCode,"", $userID, "", $_SESSION['flashContent'], true);

					if($attemptNo == 1 && $addQuesAttempts)
					{
						insertQuesAttempts($userID, $attemptID, $exerciseCode, $sessionID);
					}
					$tmpResult = nextNCERTquestion($userID,$exerciseCode,$qcode,$attemptID,$questionNo,$sessionID);
					$tmpResult = explode("~",$tmpResult);
					$qcode = $tmpResult[0];
					$showAnswer = $tmpResult[1];

					$quesCategory = "NCERT";
			        $nextPage = "question.php";
					if($_SESSION["userType"]=="msAsStudent")
						$_SESSION["theme"] = 0;
					echo '<form id="frmHidForm" action="'.$nextPage.'" method="post">';
			        echo '<input type="hidden" name="qcode" id="qcode" value="'.$qcode.'">';
			        echo '<input type="hidden" name="qno" id="qno" value="'.$questionNo.'">';
			        echo '<input type="hidden" name="quesCategory" id="quesCategory" value="'.$quesCategory.'">';
			        echo '<input type="hidden" name="exerciseName" id="exerciseName" value="'.$exerciseName.'">';
			        echo '<input type="hidden" name="showAnswer" id="showAnswer" value="'.$showAnswer.'">';
			        echo '<input type="hidden" name="totalGroups" id="totalGroups" value="'.$totalGroups.'">';
			        echo '<input type="hidden" name="completedGroups" id="completedGroups" value="'.$completedGroups.'">';
			        echo '<input type="hidden" name="dueDate" id="dueDate" value="'.$dueDate.'">';
					
			        echo '</form>';
			        echo "<script>
								document.getElementById('frmHidForm').submit();
			              </script>";
			        echo '</body>';
			        echo '</html>';
			        break;

	case "fetchNCERTQuestion":

					$quesCategory = "NCERT";
					$userID = $_SESSION["userID"];
					$qcode = $_POST["qcode"];
					$qcodes = explode("##",$qcode);
					$qno = $_POST["qno"];
					$qno = explode("##",$qno);
					$R = '-1';
					$S = 0;
					$exerciseCode = $_POST["clusterCode"];
					$result = $_POST["result"];
					$sessionID = $_SESSION["sessionID"];
					$ncertattemptID = $_SESSION["ncertAttemptID"];
					$userResponse = $_POST["userResponse"];
					$eeResponse = $_POST["eeResponse"];
					
					if(trim($userResponse) != -1)
					{
						$counter = 0;
						$userResponse = explode("##",$userResponse);
						$eeResponse = explode("##",$eeResponse);
						foreach($qcodes as $singleQcode)
						{
							$A = $userResponse[$counter];
							saveNCERTques($userID,$questionNo,$singleQcode,$A,$S,$R,$ncertattemptID,$exerciseCode,$sessionID,$eeResponse[$counter]);
							$counter++;
						}
					}
					$groupNo = isset($_POST["result"])?$_POST["result"]:1;
					$_SESSION["currentSDL"] = $groupNo;
					$qcode = implode("##",$_SESSION["allQuestionsArray"][$groupNo]);
					$response = createResponse($qcode,$quesno,$quesCategory);
					echo $response;

					break;

	case "saveNcertTeacherComment":
					$srno = $_POST["srno"];
					$comment = $_POST["comment"];
					$comment = "<strong>".$_SESSION["username"]." (".date('d-m-Y')."):</strong>&nbsp;".$comment;

					$sql = "UPDATE adepts_ncertQuesAttempt SET teacherComments = CONCAT_WS('<br>',teacherComments,'$comment') WHERE srno=$srno";
					$result = mysql_query($sql);
					if($result)
						echo "true";
					else
						echo "false";
					break;

	case "saveNcertStudentComment":
					$qcode = $_POST["qcode"];
					$qcode = explode("##",$qcode);
					$qcode = $qcode[0];
					$comment = $_POST["comment"];

					$sql = "SELECT groupID FROM adepts_ncertQuestions WHERE qcode=$qcode";
					$result = mysql_query($sql);
					$row = mysql_fetch_array($result);
					$groupID = $row[0];
					$sql = "INSERT INTO adepts_ncertQuesComment (comment, userID, groupID, exerciseCode) VALUES ('$comment', '".$_SESSION["userID"]."', '$groupID','".$_SESSION['clusterCode']."')";
					$result = mysql_query($sql);
					if($result)
						echo "true";
					else
						echo "false";
					break;

	case "exercise":
			        echo '<html>';
			        echo '<body>';
			        echo "Loading...";

			        $_SESSION['startTime'] = date("Y-m-d H:i:s");        //this will be used to calculate the load time for the next ques
			        $_SESSION['endTime']   = 0;
			        $_SESSION['pageStartTime'] = 0;
			        $childClass = $_SESSION['childClass'];
			        $sessionID  = $_SESSION['sessionID'];

					$_SESSION["clusterCode"] = $_POST["clusterCode"];
			        $clusterCode = $_POST["clusterCode"];

			       	$_SESSION['higherLevel'] = '';
			       	setExerciseName($clusterCode);

			       	$_SESSION['timedTest']  = "";
			    	$_SESSION['game']		= false;
			    	//$_SESSION["qno"]       = 1;
			    	$_SESSION["noOfJumps"] = 0;
			    	$_SESSION["remedialStack"] = "";

			        $quesno=($_POST["qno"] + 1);

			        $tmpResult = nextExerciseQuestion($userID,$clusterCode,0,$sessionID,SUBJECTNO);
			        $tmpResult = explode("~",$tmpResult);
                    $qcode = $tmpResult[0];
                    $showAnswer = $tmpResult[1];

			        $_SESSION['qcode'] = $qcode;
			        //$_SESSION["qno"] = $quesno;

			        $quesCategory = "exercise";
			        $nextPage = "question.php";

					echo '<form id="frmHidForm" action="'.$nextPage.'" method="post">';
			        echo '<input type="hidden" name="qcode" id="qcode" value="'.$qcode.'">';
			        echo '<input type="hidden" name="qno" id="qno" value="'.$quesno.'">';
			        echo '<input type="hidden" name="quesCategory" id="quesCategory" value="'.$quesCategory.'">';
			        echo '<input type="hidden" name="showAnswer" id="showAnswer" value="'.$showAnswer.'">';
			        echo '</form>';
			        echo "<script>
			                 document.getElementById('frmHidForm').submit();
			              </script>";
			        echo '</body>';
			        echo '</html>';
			        break;

		case "saveEmot":
					$boolValue = false;
					$qcode = $_POST["qcode"];
					$emotID = $_POST["emotID"];
					$sessionID = $_SESSION["sessionID"];
					$sql = "INSERT INTO adepts_emotToolbarTagging (type,userID,fieldID,response,sessionID) VALUES ('q','$userID','$qcode','$emotID',$sessionID)";
					if(mysql_query($sql))
						$boolValue = true;
					if($boolValue)
						echo "success";
					break;
         case "skipFeedback":
					$gameID = $_POST['gameID'];
					$dateToday = date('Y-m-d h:i:s');
					$feedBackSet = 28;
					$dataSet = json_decode(stripslashes($_POST["data"]),true);
					$category = $_SESSION['admin'];
					foreach($dataSet as $qid=>$response)
					{
						$sql = "INSERT INTO adepts_feedbackresponse (userID,qid,response,feedbackset,feedbackdate,type,category) VALUES ($userID,$qid,'$response','$feedBackSet','$dateToday','$gameID','$category')";
						$result = mysql_query($sql);
					}
					if($sql);
						echo "success";
					break;
		case "gameFeedBack":
					$gameID = $_POST['gameID'];
					$dateToday = date('Y-m-d h:i:s');
					$feedBackSet = ($_POST['formType']=="type1")?20:21;
					if($_POST['formType']=="type3")
					$feedBackSet = 33;
					$category = $_SESSION['admin'];
					$dataSet = json_decode(stripslashes($_POST["data"]),true);
					foreach($dataSet as $qid=>$response)
					{						
						$sql = "INSERT INTO adepts_feedbackresponse (userID,qid,response,feedbackset,feedbackdate,type,category) VALUES ($userID,$qid,'$response','$feedBackSet','$dateToday','$gameID', '$category')";
						$result = mysql_query($sql);
					}
					if($sql);
						echo "success";
					break;

		case "changeInterface":
				setTeacherScreen($userID,$interfaceFlag);
				if($interfaceFlag==1)
				$nextPage = "../teacherInterface/home.php";
				header("location:$nextPage");
				break;

		case "back_refresh":
				mysql_query("UPDATE ".TBL_SESSION_STATUS." SET endTime='".date("Y-m-d H:i:s")."', endType=concat_ws(',',endType,'6'), logout_flag=1 WHERE sessionID=".$_SESSION['sessionID']);
				break;

		case "comprehensiveAfterActivity":
				//check for comprhensive module flow
				//echo 'coming from timedTest';die;
				redirectToFlow();
				break;

		case "sbaTest":
			$objUser = new User($userID);
			
			$testMode	=	isset($_POST["testMode"])?$_POST["testMode"]:"";
			$sbaDetailArray	=	getSbaTestID($userID,$objUser->schoolCode,$objUser->childClass,$objUser->childSection,$testMode);
			$sbaTestID		=	$sbaDetailArray["sbaID"];
			$totalQues		=	$sbaDetailArray["totalQues"];
			$maxTime		=	$sbaDetailArray["maxTime"];
			$qcodeStr		=	$sbaDetailArray["qcodes"];
			
			$_SESSION['sbaTestID']	=	$sbaTestID;
			if(checkForPendingTest($userID,$sbaTestID)=="notStarted")
			{
				$qcodeStr	=	insertSbaDetails($userID,$sbaTestID,$qcodeStr,$totalQues,$maxTime,$_SESSION["sessionID"]);
			}
			$_SESSION["qcodeStr"]	=	getQcodeDetails($userID,$sbaTestID);
			$timeTaken	=	getSbaQcode($userID,$sbaTestID);
			$qcode	=	$_SESSION["qcode"];
			$qno	=	$_SESSION["qno"];
			$timeLeft	=	($maxTime*60) - $timeTaken;
			echo '<form id="frmHidForm" action="sbaQuestion.php" method="post">';
			echo '<input type="hidden" name="qno" value="'.$qno.'">';
			echo '<input type="hidden" name="timeLeft" value="'.$timeLeft.'">';
			echo '<input type="hidden" name="qcode" value="'.$qcode.'">';
			echo '<input type="hidden" name="totalTime" value="'.$maxTime.'">';
			echo '<input type="hidden" name="totalQuestion" value="'.$totalQues.'">';
			echo "<script>
					   document.getElementById('frmHidForm').submit();
				  </script>";
			break;
		
		case "sbaTestAnswer":
			$userID	=	$_SESSION["userID"];
			$qcode	=	$_POST["qcode"];
			$qno	=	$_POST["qno"];
			echo $userResponse	=	getUserAnswer($userID,$qcode,$qno);
			break;
			
		case "finishSbaTest":
			if($_SESSION['sbaTestID']!="")
				$perCorrect	=	finishSbaTest($_SESSION['sbaTestID'],$_SESSION['userID']);
			$_SESSION['sbaTestID'] = "";
			$_SESSION["qno"] = 1;
			$_SESSION["qcode"] = "";
			echo $perCorrect;
			break;
			
		case "timeTakenSbaTest":
			$totalTime	=	$_POST["totalTime"];
			$timeLeft	=	$_POST["timeLeft"];
			saveTimeTaken($_SESSION['sbaTestID'],$_SESSION['userID'],$totalTime,$timeLeft);
			break;
			
		case "startExamCornerCluster" :
			$clusterCode	=	$_POST["clusterCode"];
			$ttCode	=	$_POST["ttCode"];
			$clusterType	=	$_POST["clusterType"];
			$qcode	=	setExamCornerQcode($clusterCode,$clusterType,$ttCode);
			$date	=	date("Y-m-d");
			$_SESSION['bucketClusterCode']	=	$clusterCode;
			$_SESSION['bucketTopicName']	=	getTeacherTopicDesc($ttCode);
			$sq	=	"INSERT INTO adepts_examcornerClusterStatus
					 SET userID=$userID, clusterCode='$clusterCode', teacherTopicCode='$ttCode', attemptType='$clusterType', attemptDate='$date'";
			$rs	=	mysql_query($sq);
			$_SESSION['bucketAttemptID']	=	mysql_insert_id();
			echo '<html>';
			echo '<body>';
			echo '<form id="frmHidForm" action="question.php" method="post">';
				echo '<input type="hidden" name="qcode" id="qcode" value="'.$qcode.'">';
				echo '<input type="hidden" name="qno" id="qno" value="1">';
				if ($_POST['fromChoiceScreen']) echo '<input type="hidden" name="iycAsChoice" id="iycAsChoice" value="1">';
				echo '<input type="hidden" name="quesCategory" id="quesCategory" value="normal">';
				echo '<input type="hidden" name="showAnswer" id="showAnswer" value="1">';
			echo '</form>';
			echo "<script>
					 document.getElementById('frmHidForm').submit();
				  </script>";
			echo '</body>';
			echo '</html>';
			break;
			
		case 'createWindowName' : 
			if(!$_SESSION["windowName"])
				echo $_SESSION["windowName"]	=	date("Ymd").$_SESSION["userID"].rand(5, 15);
			else
				$_SESSION["windowName"] = "invalidAccess";
			break;
			
		case "saveVideoComments" :
			
			$videoComment = $_GET['comment'];
			$videoID = $_GET['videoID'];
			$userID = $_SESSION['userID'];
			
			$sql="update adepts_userVideoLog set comments='$videoComment' where videoID=$videoID and userID=$userID";
			mysql_query($sql) or die(mysql_error());
		
		break;
		
		case "videoViewCount";
		
		    $query  = "UPDATE adepts_msVideos set clickCnt=clickCnt+1 where videoID=".$_GET['videoID'];
            $result = mysql_query($query) or die(mysql_error());
		
		break;
		case "startDiagnostic":
		
			echo '<html>';
			echo '<body>';
			echo "Loading...";
			redirectToFlow();
		break;

		case "endSessionType" :

			if(isset($_POST["endType"]))
				$endType = $_POST["endType"];
			else
				$endType = 12;
		    $query  = "UPDATE ".TBL_SESSION_STATUS." SET endTime='".date("Y-m-d H:i:s")."', endType=(case when ISNULL(endType) then '$endType' else concat_ws(',',endType,'$endType') end), logout_flag=1 WHERE sessionID=".$_SESSION['sessionID'];
		    $result = mysql_query($query) or die(mysql_error());
			if(isset($_POST["endType"]) && $_POST["endType"]==4)
				session_destroy();
		break;

		case "voiceOverLog" :

		$userId= $_SESSION['userID'];
		$sessionID   = $_SESSION['sessionID'];
		$qcode = $_POST['qcode'];
		
		$sql = "INSERT into adepts_voiceOverLog(userID,sessionID,qcode) values($userID,$sessionID,$qcode)";
		$result = mysql_query($sql) or die(mysql_error());
		
		break;
		
		case "updateDaTimeSpent" :
			$time = $_POST["timeSpent"];
			$qno = $_POST["qno"];
			$daPaperCode = $_SESSION['daPaperCode'];
			$sql = "update da_questionTestStatus set spendTime = $time, lastAttemptQue = if(lastAttemptQue >= $qno,lastAttemptQue,$qno) where userID = $userID and paperCode = '$daPaperCode' ";
			mysql_query($sql) or die(mysql_error().$sql);

		break;
		case "updateWSTimeSpent" :
			$time = $_POST["timeSpent"];
			$qno = $_POST["qno"];
			$worksheetAttemptID = $_SESSION['worksheetAttemptID'];
			$sql = "update worksheet_attempt_status set spend_time = $time, last_attempted_que = $qno where userID = $userID and srno = '$worksheetAttemptID' ";
			mysql_query($sql) or die(mysql_error().$sql);

		break;
		
		case "saveCQUserResponse" :
			saveWildcardUserResponsesText(1,$_POST['qcode']);
			break;
			
		case "otherTopicCQ"	:
			if($userResponse=="yes")
				$_SESSION['challengeQuestionsOtherFlag'] = 2;
			else if($userResponse=="no")
				$_SESSION['challengeQuestionsOtherFlag'] = 3;
			break;
		case "updateDdTimeSpent" :
				$time = $_POST["timeSpent"];
				$practiseModuleId = $_SESSION['dailyDrillArray']['practiseModuleId'];
				$practiseModuleTestStatusId = $_SESSION['dailyDrillArray']['practiseModuleTestStatusId'];
				$sql = "update practiseModulesTestStatus set remainingTime = $time where userID = $userID and practiseModuleId = '$practiseModuleId' and id = $practiseModuleTestStatusId ";
				mysql_query($sql) or die(mysql_error().$sql);
			break;
		/*case "completePractiseModule" :
				$timedTestStatus = $_POST['timedTestStatus'];
				$clusterCode = $_SESSION['dailyDrillArray']['ddClusterCode'];
				$practiseModuleId = $_SESSION['dailyDrillArray']['practiseModuleId'];
				$attemptNo = $_SESSION['dailyDrillArray']['attemptNo'];
				$timedTestAttemptId = $_POST['timedTestAttemptId'];
				$isInternalRequest = $_SESSION['dailyDrillArray']['isInternalRequest'];
				if (isset($timedTestAttemptId)){
					if($timedTestStatus  == "pass"){
						$scoreToDisplay = 110;
						$updateQ = " status = 'completed', score =$scoreToDisplay ";
						$timedTestScore = 10;
						
					}else{
						$scoreToDisplay = 100;
						$updateQ = " score =$scoreToDisplay ";
						$timedTestScore = 0;
						
					}
				}else{
					$scoreToDisplay = 100;
					$updateQ = " score =$scoreToDisplay ";
					$timedTestScore = 0;
				}
				$responseResult = '';
				//if(!$isInternalRequest){
					$updateQuery = "UPDATE practiseModulesTestStatus SET $updateQ WHERE userID = $userID AND practiseModuleId = '$practiseModuleId' AND attemptNo = $attemptNo";
					mysql_query($updateQuery) or die(mysql_error().$updateQuery);	
					
					$selectQ = "SELECT R,questionLevel FROM practiseModulesQuestionAttemptDetails  WHERE userID = $userID AND practiseModuleId = '$practiseModuleId' AND attemptNo = $attemptNo";
					$selectS = mysql_query($selectQ) or die(mysql_error().$selectQ);
				//}
				
			break;*/
		case "fetchChoiceScreenOnTopicCompletion"  :
				$ttCode = $_POST['teacherTopicCode'];
				$ttAttemptID = $_POST['ttAttemptID'];
				$_SESSION['teacherTopicAttemptID']=$ttAttemptID;
				$childClass=$_SESSION['childClass'];
				$type=2;
				if (isset($_SESSION['fromTTSelection'])){
					$type=3;unset($_SESSION['fromTTSelection']);
				}
				$query = "SELECT flow FROM ".TBL_TOPIC_STATUS." WHERE ttAttemptID=$ttAttemptID";
				$flow_result = mysql_query($query);
				$flow_line = mysql_fetch_array($flow_result);
				$flow = $flow_line[0];
				$objTT            = new teacherTopic($ttCode,$childClass,$flow);
				$nextCluster      = $objTT->hashigherClassInFlow($childClass);
				if ($nextCluster==-1)
					echo createResponse(-3,"","normal","1","",$type."~".$ttCode);
				else 
					echo createResponse(-8,"","normal","1","",$type."~".$ttCode);
				break;
			case "chooseFromChoiceScreen"  :
				if (isset($_POST['clicked']) && isset($_POST['choiceID'])) {
					$clickedChoice=$_POST['clicked'];
					$clickedParams=isset($_POST['params'])?$_POST['params']:'';
					$choiceID=$_POST['choiceID'];
					mysql_query("UPDATE adepts_userChoices SET choiceSelected='$clickedChoice', choiceParameters='$clickedParams' WHERE choiceID=$choiceID");
				}
				break;
}

mysql_close();

function isPendingDCT($ttAttemptID)
{
    $pendingDCT = false;
	$sql = "SELECT current, status FROM adepts_dctDetails WHERE ttAttemptID = '$ttAttemptID'";
	//echo $sql;
	$result = mysql_query($sql);
	if(mysql_num_rows($result) != 0)
	{
		$row = mysql_fetch_assoc($result);
		$pendingDCT = $row['current'];

		if($pendingDCT == '')
			$pendingDCT = false;
	}

	return $pendingDCT;
}

function isPendingResearchModule($ttAttemptID)
{
	$pendingRM = 0;
	$query = "SELECT count(*) FROM adepts_researchModuleAttemptDetails WHERE ttAttemptID = $ttAttemptID AND status='In-progress'";
	$result = mysql_query($query);
	if($line = mysql_fetch_array($result))
	{
		if($line[0]>0)
			$pendingRM = 1;
	}
	return $pendingRM;
}
function getHTMLForGame($gameID,$level,$phpFile,$param)
{
	$html = "";
	$html .= '<input type="hidden" name="gameID" id="gameID" value="'.$gameID.'">';
	$html .= '<input type="hidden" name="level" id="level" value="'.$level.'">';
	if(strcasecmp($phpFile,"Balloon.php")==0)
	{
		$html .= '<input type="hidden" name="sumvalue" id="sumvalue" value="'.$param.'">';
	}
	elseif (strcasecmp($phpFile,"sparkie.php")==0 || strcasecmp($phpFile,"sparkie_3digit.php")==0)
	{
		$html .= '<input type="hidden" name="operation" id="operation" value="'.$param.'">';
	}
	return $html;
}

function setEndTime($sessionID, $from)
{
	switch ($from)
	{

		case "1" :
			$endtype = "2"; //user has pressed end session button (Clean Exit)
			break;

		//Weekly limit no longer exits
		case "-5" :
			$endtype = "3";//quota of day is over (Daily Quota Over)
			break;

		case "-7" :
			$endtype = "1";//session time limit of 30 minutes is over (Session Time Over)
			break;
		case "-6" :
			$endtype = "3";//quota of day is over (Daily Quota Over)
			break;

		case "6" :
			$endtype = "4";//session time out due to inactivity (Session inactivity)
			break;
			
		case "-9" :
			$endtype = "2";//Topic practice 15 questions completed..
			break;

		default :
			$endtype = $from;//default case (Unknown reason)
	}
	$query = "UPDATE ".TBL_SESSION_STATUS." SET endTime='".date("Y-m-d H:i:s")."', endType=concat_ws(',',endType,'".$endtype."') WHERE sessionID=".$sessionID;echo
	mysql_query($query);

	if(SUBJECTNO==2)
	{
		$query = "SELECT z.sessionID sessID, z.lastModified lastMod, count(distinct a.srno) totalq, count(distinct b.srno) totalcq, count(distinct c.timedTestID) totTmTst, count(distinct d.gameID) totgms,  count(distinct e.srno) totmonrev, count(distinct f.srno) tottoprev
		FROM (((((adepts_sessionStatus z LEFT JOIN ".TBL_QUES_ATTEMPT_CLASS." a on z.sessionID=a.sessionID)
		LEFT JOIN  adepts_ttChallengeQuesAttempt b on z.sessionID=b.sessionID)
		LEFT JOIN adepts_timedTestDetails c on z.sessionID=c.sessionID)
		LEFT JOIN adepts_userGameDetails d on z.sessionID=d.sessionID)
		LEFT JOIN adepts_revisionSessionDetails e on z.sessionID=e.sessionID)
		LEFT JOIN (SELECT srno, sessionID FROM adepts_topicRevisionDetails WHERE sessionID=".$sessionID." UNION SELECT id as srno, sessionID FROM practiseModulesQuestionAttemptDetails  WHERE sessionID=".$sessionID.") f on z.sessionID=f.sessionID
		WHERE z.sessionID=".$sessionID." GROUP BY z.sessionID";

		$result = mysql_query($query) or die(mysql_error());

		$row = mysql_num_rows($result);

		if($row > 0)
		{
			$line = mysql_fetch_array($result);

			$update_query = "UPDATE adepts_sessionStatus SET ";
			$update_query .= "totalQ=".$line['totalq'].",";
			$update_query .= "totalCQ=".$line['totalcq'].",";
			$update_query .= "totalTmTst=".$line['totTmTst'].",";
			$update_query .= "totalGms=".$line['totgms'].",";
			$update_query .= "totalMonRevQ=".$line['totmonrev'].",";
			$update_query .= "totalTopRevQ=".$line['tottoprev'].",";
			$update_query .= "lastModified='".$line['lastMod']."' WHERE sessionID=".$line['sessID'];

			mysql_query($update_query) or die($update_query."<br>".mysql_error());
		}
	}
	elseif (SUBJECTNO==3)
	{
		$query = "SELECT z.sessionID sessID, z.lastModified lastMod, count(distinct a.srno) totalq, count(distinct f.srno) tottoprev
		FROM (adepts_sessionStatus_sc z LEFT JOIN adepts_teacherTopicQuesAttempt_sc a on z.sessionID=a.sessionID)
		  	LEFT JOIN adepts_topicRevisionDetails_sc f on z.sessionID=f.sessionID
		WHERE z.sessionID=".$sessionID." GROUP BY z.sessionID";

		$result = mysql_query($query) or die(mysql_error());

		$row = mysql_num_rows($result);

		if($row > 0)
		{
			$line = mysql_fetch_array($result);

			$update_query = "UPDATE adepts_sessionStatus_sc SET ";
			$update_query .= "totalQ=".$line['totalq'].",";
			$update_query .= "totalTopRevQ=".$line['tottoprev'].",";
			$update_query .= "lastModified='".$line['lastMod']."' WHERE sessionID=".$line['sessID'];
			mysql_query($update_query) or die($update_query."<br>".mysql_error());
		}
	}
}

function deleteCurrentStatus($userID,$ttCode,$flagForOffline)
{
	$query = "DELETE FROM ".TBL_CURRENT_STATUS." WHERE userID=".$userID." AND teacherTopicCode='".$ttCode."'";
	if($flagForOffline)
		logDeleteQuery($query,TBL_CURRENT_STATUS, $_SESSION["schoolCode"], array('userID'=>$userID,'teacherTopicCode'=>'$ttCode'), '', "userID=$userID AND teacherTopicCode='$ttCode'");
	mysql_query($query);
}

function updateTeacherTopicStatus($ttAttemptID, $result)
{
	if($result==-2)
	    $status = "FAILURE";
	elseif ($result==-4)
		$status = "NA";
	elseif ($result==-5)
		$status = "Aborted";
	else
	    $status = "SUCCESS";
	
	$query = "SELECT classLevelResult, result FROM ".TBL_TOPIC_STATUS."  WHERE ttAttemptID=".$ttAttemptID;
	$result = mysql_query($query);
	$line = mysql_fetch_array($result);
	if ($line[0]!="SUCCESS" && $status!="SUCCESS")
		return;
	if ($line[1]=="SUCCESS" || $line[1]=="Aborted")
		return;

	$query = "UPDATE ".TBL_TOPIC_STATUS." SET result='".$status."'";
	if($status=="SUCCESS"){
        $query .= ",progress=100";
	}
	$query .= " WHERE ttAttemptID=".$ttAttemptID;
	mysql_query($query);
}

function setTeacherTopic($teacherTopicCode)
{
	$userID=$_SESSION['userID'];
	$user=new User($userID);
	if(!(strcasecmp($user->category,"STUDENT")==0 && (strcasecmp($user->subcategory,"SCHOOL")==0 || strcasecmp($user->subcategory,"Home Center")==0)) && $user->childClass<=3) {
		$query = "SELECT IF(c.newTTDesc='' OR ISNULL(c.newTTDesc),a.teacherTopicDesc,c.newTTDesc) as 'teacherTopicDesc'
					FROM   adepts_teacherTopicMaster a LEFT JOIN adepts_teacherTopicFlow_classwise c ON a.teacherTopicCode=c.teacherTopicCode
					WHERE a.teacherTopicCode='$teacherTopicCode'";
	}
	else 
		$query = "SELECT teacherTopicDesc FROM adepts_teacherTopicMaster WHERE teacherTopicCode='".$teacherTopicCode."'";
	$result = mysql_query($query);
	$line = mysql_fetch_array($result);
	$_SESSION['teacherTopicName'] = $line[0];
}

function setExerciseName($clusterCode)
{
	$query = "SELECT cluster FROM adepts_clusterMaster WHERE clusterCode='".$clusterCode."'";
	$result = mysql_query($query);
	$line = mysql_fetch_array($result);
	$_SESSION['teacherTopicName'] = $line[0];
}

//function to check if the daily/weekly time limit is done with, if so log him out.
function hasExceededTodaysLimit($userID,$quesno="")
{
	$schoolCode         = $_SESSION['schoolCode'];
	$timeAllowedPerDay  = $_SESSION['timePerDay'];
	$subcategory        = $_SESSION['subcategory'];
	$category           = $_SESSION['admin'];
	//just a second check, not needed as such;
	if($timeAllowedPerDay=="" && (strcasecmp($category,"STUDENT")==0))
	    $timeAllowedPerDay = 40;
	if($timeAllowedPerDay=="")
	    return 0;
	else
	{
		$timeAllowedPerDay = $timeAllowedPerDay * 60;    //convert to secs (daily limit)
		//$maxTimeAllowed    = $timeAllowedPerDay * 2;	// max 2 sessions of 30 mins (timeAllowedPerDay mins of userDetails) allowed
		$maxTimeAllowed = 5400;//90 * 60;    //Changed to max 1-1/2 hr per day for all users on 4th Oct.
		$sessionStartTime  = $_SESSION["sessionStartTime"];

		$now               = date("Y-m-d H:i:s");
		$sessionTime       = convertToTime($now) - convertToTime($sessionStartTime);
		$timeSpentToday    = $_SESSION['timeSpentToday'] + $sessionTime;
		//$timeSpentThisWeek = $_SESSION['timeSpentInTheWeek'] + $sessionTime;

		//-5 => Weekly limit over, -6 => daily limit over -7 => session limit over

		if(strcasecmp($subcategory,"School")!=0)
		{
			if($timeSpentToday > $maxTimeAllowed)
			    return -6; //Daily quota Over
			// else if($sessionTime > $timeAllowedPerDay)
			//     return -7; //session time limit over 				 removed for retail mantis ID - 13178
			else
			    return 0;
		}
		else	//For school users, check if the session is from the school i.e. between 7 to 4 Mon-Fri
		{
			$ts    = mktime(substr($sessionStartTime,11,2),substr($sessionStartTime,14,2), substr($sessionStartTime,17,2), substr($sessionStartTime,5,2), substr($sessionStartTime,8,2), substr($sessionStartTime,0,4));
			$start = mktime(07,00);
			$end   = mktime(16,00);
			$timeStarted = mktime(substr($sessionStartTime,11,2),substr($sessionStartTime,14,2), substr($sessionStartTime,17,2));
			$startDay = 1;
			$endDay   = 5;
			if($schoolCode==1752 || $schoolCode==37421 || $schoolCode==359413)	//For SNK consider Mon-Sat as working days.
			    $endDay = 6;
			if($schoolCode==1752)	//For SNK, consider end time as 5:45 - based on mail from SNK dated 4th Oct,2012 related to PBL
				$end = mktime(17,45);

			if(date("w",$ts)>=$startDay && date("w",$ts)<=$endDay && $timeStarted >= $start && $timeStarted < $end)
			{
				if($sessionTime > $timeAllowedPerDay)
				    return -7;
				else
				    return 0;
			}
			else
			{
				if($timeSpentToday > $maxTimeAllowed)
				    return -6;
				else if($sessionTime > $timeAllowedPerDay)
				    return -7;
				else
				    return 0;
			}
		}
	}
}

//get the amount of time spent by the user in the day/week
function getTimeSpent($userID, $fromDay="", $tillDay="")
{
	$fromDay = $fromDay==""?date("Ymd"):str_replace("-","",$fromDay);
	$tillDay = $tillDay==""?date("Ymd"):str_replace("-","",$tillDay);
	$query = 'SELECT sessionID, startTime, endTime FROM '.TBL_SESSION_STATUS.'
              WHERE  userID='.$userID.' AND startTime_int>=  '.$fromDay.' AND startTime_int <='.$tillDay;
	//echo $query."<br/>";

	$time_result = mysql_query($query);
	$timeSpent = 0;
	while ($time_line = mysql_fetch_array($time_result))
	{
		$startTime = convertToTime($time_line[1]);
		if($time_line[2]!="")        {
			$endTime = convertToTime($time_line[2]);
		}
		else
		{
			$query = "SELECT max(lastModified) FROM ".TBL_QUES_ATTEMPT_CLASS." WHERE sessionID=".$time_line[0]." AND userID=".$userID;
			$r     = mysql_query($query);
			$l     = mysql_fetch_array($r);
			if($l[0]=="")
			    continue;
			else
			    $endTime = convertToTime($l[0]);
		}
		$timeSpent = $timeSpent + ($endTime - $startTime);        //in secs
	}
	return $timeSpent;
}

function convertToTime($date)
{
	$hr   = substr($date,11,2);
    $mm   = substr($date,14,2);
    $ss   = substr($date,17,2);
    $day  = substr($date,8,2);
    $mnth = substr($date,5,2);
    $yr   = substr($date,0,4);
    $time = mktime($hr,$mm,$ss,$mnth,$day,$yr);
    return $time;
}

function loadDetails($userID, $childClass)
{
	$query  = "SELECT qcode, clusterCode, clusterAttemptID, teacherTopicCode, ttAttemptID, currentSDL, remedialStack, progressUpdate, remedialMode, gameID
                            FROM ".TBL_CURRENT_STATUS." WHERE userID=$userID AND status=1 ORDER BY srno DESC";
	$result = mysql_query($query) or die("1".mysql_error());
	if($user_row = mysql_fetch_array($result))
	{
		/*if(!isActive($userID,$user_row['teacherTopicCode']))
		{
			setInactive($user_row['ttAttemptID'], $userID);
			return -1;
		}*/
		
		// Added for temporary period for testing of duplicate qcode at starting of topic/cluster
		if(empty($user_row["currentSDL"]) && !empty($user_row["qcode"])) {
			// when current SDL is empty and qcode is there then fetch SDL of qcode
			$get_sdl_detail = "SELECT subdifficultylevel FROM adepts_questions WHERE qcode = '".mysql_real_escape_string($user_row["qcode"])."'";
			$exec_sdl_detail = mysql_query($get_sdl_detail);
			if(mysql_num_rows($exec_sdl_detail)) {
				$row_sdl_detail = mysql_fetch_array($exec_sdl_detail);
				$user_row["currentSDL"] = $row_sdl_detail['subdifficultylevel'];
			}
		}

		if(!isset($_SESSION['qno']))
			$_SESSION["qno"]                        = 1;
        $_SESSION['prevQcode']    = $_SESSION["qcode"];
		$_SESSION["qcode"]                          = $user_row["qcode"];
		$_SESSION["clusterCode"]                	= $user_row["clusterCode"];
		//$_SESSION["currentClusterType"] 			= findClusterType($user_row["clusterCode"]); // Added By Manish Dariyani For Practice Cluster
		$_SESSION["clusterAttemptID"]        		= $user_row["clusterAttemptID"];
		$_SESSION["teacherTopicCode"]        		= $user_row["teacherTopicCode"];
		$_SESSION["teacherTopicAttemptID"]          = $user_row["ttAttemptID"];
		$_SESSION["correctInARow"]                	= 0;                //No jumps for 3 correct in a row across the sessions
		$_SESSION["currentSDL"]                     = $user_row["currentSDL"];
		$_SESSION["remedialStack"]                	= $user_row["remedialStack"];
		if(!isset($_SESSION["noOfJumps"]))
		    $_SESSION["noOfJumps"]                  = 0;
		$_SESSION['quesCorrectInALevelOfTopic'] 	= 0;
		$_SESSION['questionType']                	= "normal";
		$_SESSION['challengeQuestionsArray']    	= "";
		$_SESSION['challengeQuestionsCorrect']  	= "";
		if($user_row['gameID']!="")
		    $_SESSION['game']	 					= true;
		else
		    $_SESSION['game']	 					= false;
		if(!isset($_SESSION['gamePlayed']))
			$_SESSION['gamePlayed']	 				= 0;
		$_SESSION['qid']         				    = "";
		$_SESSION['prevQcode']         				= "";
		$_SESSION['commonInstruction']              = "";
		$_SESSION['introVids']						= "";
		$_SESSION['competitiveExamCQ']              = 0;
		$_SESSION['afterBonusCQqcode']              = 0;
		$remedialMode = $user_row["remedialMode"]==1?1:0;
		$_SESSION['remedialMode']                   = $remedialMode;
		$_SESSION["bonusCQArray"] = array();
		$_SESSION['totalCorrect'] = 0;
		$_SESSION['totalRevisionQuesAttempt'] = 0;
		$_SESSION['topicRevisionAttemptNo'] = "";
		setTeacherTopic($user_row["teacherTopicCode"]);

		$qcode = $user_row["qcode"];
		$clusterCode = $user_row["clusterCode"];
		//Set the flow of TT
		$flow_query  = "SELECT flow, ttAttemptNo, noOfQuesAttempted, perCorrect, progress FROM ".TBL_TOPIC_STATUS." WHERE ttAttemptID=".$user_row["ttAttemptID"];
		$flow_result = mysql_query($flow_query);
		$flow_line   = mysql_fetch_array($flow_result);
		$_SESSION['flow'] = $flow_line['flow'];
		$_SESSION['topicAttemptNo'] = $flow_line['ttAttemptNo'];
		$noOfQuesAttempted = $flow_line['noOfQuesAttempted'];
		$perCorrect = $flow_line['perCorrect'];

		if($user_row["progressUpdate"]==1)
		{
			$progressUpdateObj = new topicProgressCalculation($user_row['teacherTopicCode'],$childClass,$flow_line['flow'],$user_row["ttAttemptID"],SUBJECTNO);
			$progressUpdateObj->updateProgress();
		}
		getTopicProgressDetails($userID,$user_row['teacherTopicCode']);
		if($qcode!=-2 && $qcode!=-3)
		{
			$objTT       = new teacherTopic($user_row['teacherTopicCode'],$childClass,$flow_line['flow']);
			$_SESSION['topicProgressDetails'] = $objTT->getProgressDetailsAtSDL();
			
			fillTopicProgressBar($user_row['teacherTopicCode'],$childClass,$flow_line['flow'],$userID);
			
            $_SESSION['topicWiseProgressStatus']=array();
			$_SESSION['topicWiseProgressStatus'] = findCLustersAttendedInTopic($_SESSION['teacherTopicAttemptID']);

			loadArrays($clusterCode, $user_row["clusterAttemptID"], $userID, $user_row["ttAttemptID"], $_SESSION['flashContent']);
			$tempTT = $user_row['teacherTopicCode'];
            if($objTT->customTopic==1)		//if custom topic load CQs of parent TT
               	$tempTT = $objTT->parentTTCode;
			
			$_SESSION['challengeQuestionsArray'] = loadChallengeQuestionsNewLogic($tempTT, $userID,$user_row["ttAttemptID"]);
			$_SESSION['allClustersInTT'] = loadAllClusterInTopic($tempTT);
			/*if(count($_SESSION['challengeQuestionsArray'])==0)
			{*/
				$_SESSION['challengeQuestionsOtherArray'] = loadChallengeQuestionsOtherTopic($teacherTopicCode, $userID);
			//}
			$ttCQArray = array("TT082");
			$_SESSION['challengeQuestionsProblemSolvingArray'] = loadChallengeQuestionsProblemSolving($ttCQArray, $childClass);	
			//loadChallengeQuestions($tempTT, $userID);
			if(!$remedialMode)
			    manipulateArrays($user_row["currentSDL"],$qcode,"normal");

			$higherLevel = isAtaHigherLevelInTT($objTT, $user_row['clusterCode'], $childClass);
			$_SESSION['higherLevel'] = $higherLevel;
			$lowerLevel = 0;
			if(in_array($user_row['clusterCode'],$objTT->getLowerLevelClusters()))
				$lowerLevel = 1;
			$_SESSION['lowerLevel'] = $lowerLevel;
		}

		return $qcode;
	}
	else
	    return -1;
}

function isHigherClassOptionAvailable($ttCode,$userID, $ttAttemptID,$childClass)
{
	$higherClassAvailable=0;
	$objTT  = new teacherTopic($ttCode,$childClass,getFlowForTT($userID, $ttCode));
	if ($objTT->hashigherClassInFlow($childClass)==-1) return $higherClassAvailable;

	if ($ttAttemptID!=""){
		$higherClassQuery="SELECT classLevelResult, result FROM ".TBL_TOPIC_STATUS." WHERE userID=$userID AND classLevelResult='SUCCESS' AND (ISNULL(result) OR result NOT IN ('Aborted', 'SUCCESS')) AND ttAttemptID=".$ttAttemptID;
		$result = mysql_query($higherClassQuery);
		if (mysql_num_rows($result)>0) $higherClassAvailable=1;
	}
	return $higherClassAvailable;
}
function isAtaHigherLevelInTT($objTT, $clusterCode, $childClass)
{
	$higherLevel = 0;
	$levelArray  = $objTT->getClusterLevel($clusterCode);
	if($levelArray[0]>$objTT->startingLevel)
		$higherLevel = 1;
	return $higherLevel;
}

function setInactive($ttAttemptID, $userID)
{
	if($ttAttemptID!="")
	{
		$query  = "UPDATE ".TBL_CURRENT_STATUS." a, ".TBL_TOPIC_STATUS." b SET a.status=0
	               WHERE  a.userID=b.userID AND a.teacherTopicCode=b.teacherTopicCode AND b.ttAttemptID=$ttAttemptID AND a.userID=$userID";
	    //echo $query;
	    $result = mysql_query($query) or die("It seems your session has expired. Kindly login again!!");
	}
}

function teacherTopicIncomplete($teacherTopicCode, $userID)
{
	//Check if this topic was left in between previously
	if(isset($_SESSION["userType"]) && ($_SESSION["userType"]=="msAsStudent" || $_SESSION["userType"]=="teacherAsStudent"))
		$query  = "SELECT count(srno) FROM ".TBL_CURRENT_STATUS." a, adepts_eiLoginData b WHERE a.ttAttemptID=b.ttAttemptID AND teacherTopicCode='$teacherTopicCode' AND b.userID=$userID AND b.childClass=".$_SESSION["childClass"]; //Class filter for EI users, they can use MS as multiple class..
	else
		$query  = "SELECT count(srno) FROM ".TBL_CURRENT_STATUS." WHERE teacherTopicCode='$teacherTopicCode' AND userID=$userID";
	$result = mysql_query($query);
	$line   = mysql_fetch_array($result);
	if($line[0]>0)
	    return true;
	else
	    return false;
}

function checkIfChoiceScreenApplicable($teacherTopicCode, $userID,$childClass)
{
	if (!isChoiceScreenSchool($childClass)) return 0;
	$query = "SELECT ttAttemptID, progress FROM ".TBL_TOPIC_STATUS." WHERE userID=$userID AND teacherTopicCode='$teacherTopicCode' ORDER BY ttAttemptID DESC LIMIT 1";
	$result = mysql_query($query);
	if(mysql_num_rows($result) > 0)
	{
		$line   = mysql_fetch_array($result);
		if ($line[1]!=100) return 0;
		$ttAttemptID=$line[0];
		return $ttAttemptID;
	}
	return 0;
}
function activateTeacherTopic($teacherTopicCode, $userID)
{
	if(isset($_SESSION["userType"]) && ($_SESSION["userType"]=="msAsStudent" || $_SESSION["userType"]=="teacherAsStudent"))
		$query  = "UPDATE ".TBL_CURRENT_STATUS." a, adepts_eiLoginData b SET status=1 WHERE a.ttAttemptID=b.ttAttemptID AND b.userID=$userID AND teacherTopicCode='$teacherTopicCode' AND b.childClass=".$_SESSION["childClass"];
	else
		$query  = "UPDATE ".TBL_CURRENT_STATUS." SET status=1 WHERE userID=$userID AND teacherTopicCode='$teacherTopicCode'";
	mysql_query($query) or die("6".mysql_error());

	$query  = "UPDATE ".TBL_CURRENT_STATUS." SET status=0 WHERE userID=$userID AND teacherTopicCode<>'$teacherTopicCode'";
	mysql_query($query) or die("Error in deactivating topic".mysql_error());
}

function getNoOfQuesAttempted($userID, $teacherTopicCode)
{
	$query  = "SELECT count(srno) FROM ".TBL_QUES_ATTEMPT_CLASS." a, ".TBL_CLUSTER_STATUS." b, ".TBL_TOPIC_STATUS." c
               WHERE  a.userID=$userID AND a.userID=b.userID AND b.userID=c.userID AND
                      a.clusterAttemptID=b.clusterAttemptID  AND b.ttAttemptID=c.ttAttemptID AND
                      c.teacherTopicCode='$teacherTopicCode'";
	$result = mysql_query($query);
	$line   = mysql_fetch_array($result);
	$noOfQues = $line[0];
	if($line[0]=="")
	    $noOfQues = 0;
	return $noOfQues;
}

function sendMail($comment,$problemid,$userID, $quesNo, $qcode, $sessionID, $isMSAsStudent, $teacherTopicCode)
{
	$user = new User($userID);
	$from     = $user->childEmail;
	if($isMSAsStudent)
	{
		$sql = "SELECT owner1, owner2 FROM adepts_topicMaster a, adepts_teacherTopicMaster b WHERE a.topicCode=b.mappedToTopic AND b.teacherTopicCode='$teacherTopicCode'";
		$result = mysql_query($sql);
		if(mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_assoc($result);
			if($row["owner1"] != "")
				$row["owner1"] .= "@ei-india.com";
			if($row["owner2"] != "")
				$row["owner2"] .= "@ei-india.com";
			$to = trim(implode($row),",");
		}
	}
	else
	{
		$to = "mindspark@ei-india.com";
	}
	$headers  = "From:<$from>\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	$comment .= "<br/><br/><div align='center'>Question: http://www.educationalinitiatives.com/mindspark/viewquestion.php?sessionstr=".$sessionID."&qno=".$quesNo."&qcode=".$qcode."</div>";
	$comment .= "<br/><br/><hr/>";
	//$comment .= $user->getUserDetails($sessionID);
	if(mail($to,$problemid,$comment,$headers))
	    echo "mail sent";
	else
	    echo "mail not sent";
}

function saveTimedTestDetails($userID, $timedTestCode, $sessionID, $timeTaken, $quesCorrect, $perCorrect, $noOfQuesAttempted, $timedTestVersion, $autoSubmitUsed=0, $quesCategory="",$attemptNo="",$timedTestDesc="")
{
	$arrPos = 0;
	$query = "INSERT INTO adepts_timedTestDetails(userID,timedTestCode,attemptedDate,sessionID,quesCorrect,timeTaken,perCorrect, noOfQuesAttempted, timedTestVersion,autoSubmitUsed) VALUES
              ($userID, '$timedTestCode', '".date("Y-m-d")."', $sessionID, $quesCorrect, $timeTaken, $perCorrect, $noOfQuesAttempted, $timedTestVersion,$autoSubmitUsed )";
	mysql_query($query);
	$timedTestID = mysql_insert_id();
	$query2 = "INSERT INTO  adepts_userFeeds (userID, studentIcon, childName, childClass, schoolCode, actID, actDesc, actIcon, score, timeTaken, srno, ftype) VALUES ($userID, '', '".$_SESSION['childName']."', ".$_SESSION['childClass'].", '".$_SESSION['schoolCode']."', '$timedTestCode', '$timedTestDesc', '', $perCorrect, $timeTaken, $timedTestID , 'timedTest')";
	mysql_query($query2);    
    
	if($_SESSION["comprehensiveModule"]!="")
	{
		$sq	=	"UPDATE adepts_userComprehensiveFlow SET activityAttemptID=$timedTestID ,timeTaken='$timeTaken', status=1 WHERE flowAttemptID=".$_SESSION["currentFlowID"];
		$rs	=	mysql_query($sq);
		getNextComprehensiveInflow();
	}
	if($_SESSION["subModule"]!="")
	{
		$sq	=	"UPDATE kst_userFlowDetails SET activityAttemptID=$timedTestID ,timeTaken='$timeTaken', status=1 WHERE flowAttemptID=".$_SESSION["currentFlowID"];
		$rs	=	mysql_query($sq);
		getNextKstSubModuleInflow();
	}
	$comprehensiveModuleFlag = $_SESSION["comprehensiveModule"]!=''? 1 : 0;
	if($_SESSION["quesCategory"]!="wildcard")
		updateTimedTestStatus($userID, $timedTestCode, $perCorrect,$attemptNo,$comprehensiveModuleFlag);
		
	foreach($_SESSION['classSpecificClustersForTT'] as $key=>$val)
	{
		if($val[0]===$timedTestCode)
		$arrPos = $key;
	}
	
	$cleared = "NA";
	if($perCorrect>=75)
	    $cleared = "passed";
	elseif($perCorrect<75)
	    $cleared = "failed";
	
	$_SESSION['classSpecificClustersForTT'][$arrPos][3] = $cleared;
	return $timedTestID;
}

function getNoOfGameAttemptsToday($userID, $gameID)
{
	$attempts = 0;
	$query = "SELECT count(srno) FROM adepts_userGameDetails WHERE gameID=$gameID AND userID=$userID AND attemptedDate='".date("Y-m-d")."'";
	$result = mysql_query($query);
	if($line = mysql_fetch_array($result))
 	    $attempts = $line[0];
	return $attempts;
}

function saveGameDetails($userID, $gameID, $sessionID, $timeTaken, $score, $level)
{
	$query = "INSERT INTO adepts_userGameDetails(userID,gameID,attemptedDate,sessionID,gameLevel,score,timeTaken) VALUES
                  ($userID, $gameID, '".date("Y-m-d")."',$sessionID, $level, $score, $timeTaken)";
	mysql_query($query) or die(mysql_error());
	$status = updateGameStatus($userID, $gameID, $level, $score);
	return $status;
}

function updateGameStatus($userID, $gameID, $level, $score)
{
	$query  = "SELECT pendingGame, clearedGame FROM adepts_gameStatus WHERE userID=$userID";
	$result = mysql_query($query);
	$line   = mysql_fetch_array($result);

	$noOfAttempts = getNoOfGameAttemptsToday($userID, $gameID);
	$pendingGame = $clearedGame = "";
	if($level==3 || $gameID==11 || $gameID==12 || $gameID==17 || $noOfAttempts==3)
	{
		$gameCleared = hasCleared($gameID, $userID);
		if($gameCleared)
		{

			if($line['clearedGame']=='')
			    $clearedGame = $gameID;
			else
			{
				$clearedGameArray = explode(",",$line['clearedGame']);
				if(!in_array($gameID,$clearedGameArray))
				    $clearedGame = $line['clearedGame'].",".$gameID;
				else
				    $clearedGame = $line['clearedGame'];
			}
			$pendingGame = $line['pendingGame'];
		}
		else
		{
			if($line['pendingGame']=='')
			    $pendingGame = $gameID;
			else
			{
				$pendingGameArray = explode(",",$line['pendingGame']);
				if(!in_array($gameID,$pendingGameArray))
				    $pendingGame = $line['pendingGame'].",".$gameID;
				else
				    $pendingGame = $line['pendingGame'];
			}
			$clearedGame = $line['clearedGame'];
		}
		$level = "";
		$gameID = "";
		$query  = "UPDATE adepts_gameStatus SET currentGame='$gameID', level='$level', pendingGame='$pendingGame', clearedGame='$clearedGame'";
		$query .= " WHERE userID=$userID";
		mysql_query($query);
		$_SESSION['game'] = false;
		return -10;
	}
	else
	{
		$nextLevelScore = 120;
		if($gameID==13)
		    $nextLevelScore = 200;
		if($score>=$nextLevelScore)
		{
			$level =	$level + 1;
			$query  = "UPDATE adepts_gameStatus SET currentGame='$gameID', level='$level' WHERE userID=$userID";
			mysql_query($query);
		}
		return 0;
	}
	return 0;

}

function hasCleared($gameID, $userID)
{
	//check if the game is cleared: i.e. scores >=80% 5 times (this number can vary from game to game) on a game at the highest level, for place value game, there is only one level
	$cleared = 0;
	$nextLevelScore = 120;	//80% score for the all games except bubble game
	if($gameID==13)
	    $nextLevelScore = 200; //For bubble game it is 200 for clearing a level
	$query = "SELECT count(srno) FROM adepts_userGameDetails WHERE userID=$userID AND gameID=$gameID AND score>=$nextLevelScore";
	if($gameID!=11 && $gameID!=12 && $gameID!=17)	//For place value and fraction games there are no levels
	{
		$query .= " AND gameLevel=3 ";
	}
	$result = mysql_query($query);
	if($line = mysql_fetch_array($result))
	{
		$noOfTimesCleared = $line[0];
		if($noOfTimesCleared>=5)
			$cleared = 1;
	}
	return $cleared;
}
function hasPlayedOnce($gameID, $userID,$ttAttemptID)
{
	//check if the game is cleared: i.e. scores >=80% 5 times (this number can vary from game to game) on a game at the highest level, for place value game, there is only one level
	$cleared = 0;
	$nextLevelScore = 120;	//80% score for the all games except bubble game
	if($gameID==13)
	    $nextLevelScore = 200; //For bubble game it is 200 for clearing a level
	$query = "SELECT count(srno) FROM adepts_userGameDetails WHERE userID=$userID AND gameID=$gameID AND score>=$nextLevelScore";
	if($gameID!=11 && $gameID!=12 && $gameID!=17)	//For place value and fraction games there are no levels
	{
		$query .= " AND gameLevel=3 ";
	}
	$result = mysql_query($query);
	if($line = mysql_fetch_array($result))
	{
		$noOfTimesCleared = $line[0];
		if($noOfTimesCleared>=5)
			$cleared = 1;
	}
	return $cleared;
}

function updateTimedTestStatus($userID, $timedTestCode, $perCorrect, $attemptNo="",$comprehensiveModuleFlag=0)
{
	$pendingTimedTest = "";
	$query  = "SELECT pendingTimedTest,pendingTopicTimedTest FROM adepts_timedTestStatus WHERE userID=$userID";
	$result = mysql_query($query);
	$line   = mysql_fetch_array($result);
	$pendingTimedTest	=	$line['pendingTimedTest'];
	$pendingTopicTimedTest	=	$line['pendingTopicTimedTest'];
	
	if($attemptNo<3 && $perCorrect < 75)
	{
		if($pendingTimedTest=='')
			$pendingTimedTest = $timedTestCode;
		else
			$pendingTimedTest = $pendingTimedTest.",".$timedTestCode;
	}
	else if($attemptNo>=3)
	{
		$ttCodePending	=	getTopicTimedTest($timedTestCode,$userID);
		if($pendingTopicTimedTest=='' && $perCorrect < 75 && $ttCodePending!="")
			$pendingTopicTimedTest = $ttCodePending."~".$timedTestCode;
		else if($ttCodePending!="")
		{
			$pendingTopicTimedTest	=	str_replace($ttCodePending."~".$timedTestCode.",","",$pendingTopicTimedTest);
			$pendingTopicTimedTest	=	str_replace(",".$ttCodePending."~".$timedTestCode,"",$pendingTopicTimedTest);
			$pendingTopicTimedTest	=	str_replace($ttCodePending."~".$timedTestCode,"",$pendingTopicTimedTest);
			if($perCorrect < 75)
				$pendingTopicTimedTest	=	$pendingTopicTimedTest.",".$ttCodePending."~".$timedTestCode;
		}
	}

	$query  = "UPDATE adepts_timedTestStatus SET currentTimedTest='' ";
	if($pendingTimedTest!="" && $comprehensiveModuleFlag==0)
 	    $query .= " , pendingTimedTest='$pendingTimedTest' ";
	if($pendingTopicTimedTest!="" && $comprehensiveModuleFlag==0)
 	    $query .= " , pendingTopicTimedTest='$pendingTopicTimedTest' ";
	$query .= "WHERE userID=$userID";
	mysql_query($query);
}

function checkPendingGame($userID, $sessionID)
{
	$game = "";
	$query  = "SELECT currentGame,level, pendingGame FROM adepts_gameStatus WHERE userID=$userID";
	$result = mysql_query($query);
	if($line   = mysql_fetch_array($result))
	{
		$currentGame = $line['currentGame'];
		$pendingGame = $line['pendingGame'];
		if($currentGame != "")
		{

			$query = "SELECT count(sessionID) FROM ".TBL_SESSION_STATUS." WHERE userID=$userID AND sessionID<>$sessionID AND cast(startTime as DATE)='".date("Y-m-d")."'";
			$r     = mysql_query($query);
			$l     = mysql_fetch_array($r);
			if($l[0]>0) //not the first session in the day i.e. left the game in the previous session,so start from the game
			    $game = $currentGame;
			else {   // if logging for the first time in the day, give the game after 20 mins
				if($pendingGame!="")
				    $pendingGame .= ",".$currentGame;
				else
				    $pendingGame = $currentGame;
				$query = "UPDATE adepts_gameStatus SET pendingGame='$pendingGame', currentGame='', level='' WHERE  userID=$userID";
				mysql_query($query);
			}
		}
	}
	return $game;
}

function checkGameInThePool($userID)
{
	$game = "";
	$query  = "SELECT pendingGame, clearedGame FROM adepts_gameStatus WHERE userID=$userID";
	$result = mysql_query($query);
	if($line   = mysql_fetch_array($result))
	{
		$pendingGame = $line['pendingGame'];
		$clearedGame = $line['clearedGame'];
		if($pendingGame != "")
		{
			$gameArray = explode(",",$pendingGame);
			$game = trim($gameArray[0]);
			$newpending = "";
			for($i=1; $i<count($gameArray); $i++)
			    $newpending .= $gameArray[$i].",";
			$newpending = substr($newpending,0,-1);
			$query = "UPDATE adepts_gameStatus SET currentGame='$game', level=1, pendingGame='$newpending' WHERE userID=$userID";
			mysql_query($query);
		}
		elseif($clearedGame != "")
		{
			$game = "choice";
		}
	}
	return $game;
}

function setSessionVariables()
{
	$_SESSION["qno"]                                            = 1;
	$_SESSION["qcode"]                                          = "";
	$_SESSION["clusterCode"]                                	= "";
	$_SESSION["clusterAttemptID"]                        		= "";
	$_SESSION["teacherTopicCode"]                               = "";
	$_SESSION["teacherTopicAttemptID"]                          = "";
	$_SESSION["correctInARow"]                                	= 0;
	$_SESSION["currentSDL"]                                     = "";
	$_SESSION["remedialStack"]                                	= "";
	$_SESSION["allQuestionsArray"]                        		= "";
	$_SESSION["questionsNeverAttemptedArray"]					= "";
	$_SESSION["questionsAttemptedInCurrentClusterAttemptArray"] = "";
	$_SESSION["noOfJumps"]                                      = 0;
	$_SESSION['challengeQuestionsArray']    					= "";
	$_SESSION['challengeQuestionsOtherArray']    				= "";
	$_SESSION["allCQAttempted"]									= "";
	$_SESSION['challengeQuestionsProblemSolvingArray'] 			= "";
	$_SESSION['challengeQuestionsOtherFlag']    				= 0;
	$_SESSION['challengeQuestionsCorrect']  					= "";
	$_SESSION['quesCorrectInALevelOfTopic'] 					= 0;
	$_SESSION['questionType']                                	= "";
	$_SESSION["updateProgress"]									= false;
	if(!isset($_SESSION['timedTest']))
	   $_SESSION['timedTest']                                   = "";
	if(!isset($_SESSION['comprehensiveModule']))
	   $_SESSION['comprehensiveModule']                         = "";
	$_SESSION['game']		 									= false;
	$_SESSION['qid']         									= "";
	$_SESSION['prevQcode']         								= "";
	$_SESSION['gamePlayed']	 									= 0;
	$_SESSION['higherLevel']                                    = 0;
	$_SESSION['commonInstruction']                              = "";
	$_SESSION['introVids']										= "";
	$_SESSION['flow']			                                = "";
	$_SESSION['topicAttemptNo']			                        = "";
	//$_SESSION['prePostTestFlag']                                = 0;
	$_SESSION['competitiveExamCQ']                              = 0;
	$_SESSION['progressInTopic']                                = 0;
	$_SESSION['quesAttemptedInTopic']                           = 0;
	$_SESSION['quesCorrectInTopic']                             = 0;
	$_SESSION['topicProgressDetails']                           = "";
	$_SESSION['lowerLevel']                                     = 0;
    //$_SESSION['prePostTestFlag']                           = 0;
	//$_SESSION['prePostTestTopic']			     = "";
	$_SESSION['sbaTestID']			     = "";
	$_SESSION['sparkie'] = array('normal'=>0,'wildcard'=>0,'CQ'=>0,'sparkieExplaination'=>0,'topicCompletion'=>0,'timedTest'=>0,'game'=>0,'worksheetCompletion'=>0);
	
	$_SESSION['allClustersInTT']								= "";
    $_SESSION['classSpecificClustersForTT']						= "";
	$_SESSION['classSpecificClustersNameForTT']					= "";
}

function isActive($userID,$teacherTopicCode)
{
	$active = true;
	$query  = "SELECT schoolCode, childClass, childSection, category, subcategory FROM adepts_userDetails WHERE userID=".$userID;
	$result = mysql_query($query) or die(mysql_error());

	$line   = mysql_fetch_array($result);
	$schoolCode    = $line['schoolCode'];
	$childClass    = $line['childClass'];
	$childSection  = $line['childSection'];
	$category 	   = $line['category'];
	$subcategory   = $line['subcategory'];
	if(strcasecmp($category,"STUDENT")==0 && strcasecmp($subcategory,"SCHOOL")==0)
	{
		$query = "SELECT count(*)  FROM adepts_teacherTopicActivation  WHERE  teacherTopicCode='$teacherTopicCode' AND deactivationDate='0000-00-00' AND schoolCode='$schoolCode'";

		if($childClass != "")
		    $query .= " AND class=$childClass";
		if($childSection != "")
		    $query .= " AND section ='$childSection'";

		$result = mysql_query($query);
		$line = mysql_fetch_array($result);
		if($line[0]==0)
		{
		    $query  = "SELECT count(srno) FROM adepts_studentTopicActivation WHERE userID=$userID AND teacherTopicCode='$teacherTopicCode' AND deactivationDate='0000-00-00'";
		    $result = mysql_query($query);
		    $line   = mysql_fetch_array($result);
		    if($line[0]==0)
		        $active = false;
		}
	}
	return $active;

}

function checkPendingRevisionSession($userID, $schoolCode, $class, $section)
{
	$revisionSessionID = "";
	//Check if any revision session active for the class/section.
	$query = "SELECT revisionSessionID, datediff(curdate(), activationDate) as noofdays FROM adepts_revisionSessionMaster
					  WHERE  isActive=1 AND activationDate<=curdate() AND schoolCode=$schoolCode AND class=$class";
	if($section!="")
	    $query .= " AND section='$section'";
	//echo $query."<br/>";exit;
	$result = mysql_query($query) or die(mysql_error());
	if($line = mysql_fetch_array($result))
	{
		$days = $line['noofdays'];
		if($days<31)	//by default, consider the revision session as inactive after a month
		{
			$revisionSessionID = $line['revisionSessionID'];
			$query = "SELECT status FROM adepts_revisionSessionStatus WHERE userID=".$userID." AND revisionSessionID=$revisionSessionID";
			//echo $query."<br/>";exit;
			$result = mysql_query($query) or die(mysql_error());
			if($line=mysql_fetch_array($result))
			{
				//check if revision session not completed
				if(strcasecmp($line['status'],"completed")==0)
				{
					$revisionSessionID = "";
				}
				else
				{
					$timeSpent = getTimeSpentOnRevisionSession($userID,$revisionSessionID); //Get the time spent till now on this revision session.
				}
			}
			else
			{
				//make an entry in the revision session status table for this user-revision session combination
				$query = "INSERT INTO adepts_revisionSessionStatus (userID, revisionSessionID, noOfQuestions, status, lastModified) VALUES
									($userID, $revisionSessionID,0, 'incomplete','".date("Y-m-d H:i:s")."')";
				$result = mysql_query($query) or die(mysql_error());
				$timeSpent = 0;	//Initialize the time spent to zero
			}
			//Set the time in the session.
			$_SESSION['timeSpentOnRevisionSession'] = $timeSpent;
		}
	}
	return $revisionSessionID;
}

function isRevisionSessionTimeRemaining()
{
	$revisionSessionTime = 30 * 60;        // 30 mins - convert to secs
	$sessionStartTime  = $_SESSION["sessionStartTime"];
	$now               = date("Y-m-d H:i:s");
	$sessionTime       = convertToTime($now) - convertToTime($sessionStartTime);
	$timeSpentOnRevisionSession   = $_SESSION['timeSpentOnRevisionSession'] + $sessionTime;
	//echo $timeSpentOnRevisionSession;
	if($timeSpentOnRevisionSession>=$revisionSessionTime)
	    return 0;
	else
	    return 1;
}

function getTimeSpentOnRevisionSession($userID, $revisionSessionID)
{
	$query = "SELECT DISTINCT a.sessionID, startTime, endTime
			  FROM   ".TBL_SESSION_STATUS." a, adepts_revisionSessionDetails b
		      WHERE  b.userID=".$userID." AND a.userID=b.userID AND a.sessionID=b.sessionID AND revisionSessionID=$revisionSessionID";
	//echo $query."<br/>";
	$time_result = mysql_query($query) or die(mysql_error());
	$timeSpent = 0;
	while ($time_line = mysql_fetch_array($time_result))
	{
		$startTime = convertToTime($time_line[1]);
		if($time_line[2]!="")        {
			$endTime = convertToTime($time_line[2]);
		}
		else
		{
			$query = "SELECT max(lastModified) FROM adepts_revisionSessionDetails WHERE sessionID=".$time_line[0]." AND userID=".$userID;
			$r     = mysql_query($query);
			$l     = mysql_fetch_array($r);
			if($l[0]=="")
			    continue;
			else
			    $endTime = convertToTime($l[0]);
		}
		$timeSpent = $timeSpent + ($endTime - $startTime);        //in secs
	}
	return $timeSpent;
}

function getRevisionSessionStartDate($schoolCode, $class, $section, $currentRevisionSessionID)
{
	$startDate = "";

	$query  = "SELECT activationDate FROM adepts_revisionSessionMaster WHERE revisionSessionID=$currentRevisionSessionID";
	$result = mysql_query($query) or die(mysql_error());
	$line   = mysql_fetch_array($result);
	$curRevisionSessionStartDate = $line[0];

	//Get the last revision session id, if any.
	$query  = "SELECT revisionSessionID, date_add(activationDate, interval 1 day) as startDate, datediff('$curRevisionSessionStartDate',activationDate) as days
			   FROM   adepts_revisionSessionMaster
			   WHERE  schoolCode=$schoolCode AND class = $class AND revisionSessionID<>$currentRevisionSessionID";
	if($section!="")
	    $query .= " AND section='$section'";
	$query .= " AND YEAR(activationDate)<>".substr($curRevisionSessionStartDate,0,4)." AND MONTH(activationDate)<>".substr($curRevisionSessionStartDate,5,2); //This condn is for the case where a teacher activates and deactivates the session in same month by chance.
	$query .= " ORDER BY activationDate DESC limit 1";
	//echo "<br/>".$query."<br/>";
	$result = mysql_query($query) or die(mysql_error());

	if($line = mysql_fetch_array($result))
	{
		//Check if the difference between cur and last revision date is more than a month(30 days), if so take 1 month prior as the start date
		$daysDiff = $line['days'];
		if($daysDiff>30)
		{
			$startDate = date("Y-m-d",mktime(0,0,0,substr($curRevisionSessionStartDate,5,2)-1, substr($curRevisionSessionStartDate,8,2)+1, substr($curRevisionSessionStartDate,0,4)));
		}
		else
		    $startDate = $line['startDate']; //if any previous revision session found, get the end date of the last revision session i.e. the max date.
	}
	else //For the first revision test, consider all topics covered from the 1st of the month
	    $startDate = substr($curRevisionSessionStartDate,0,4)."-".substr($curRevisionSessionStartDate,5,2)."-01";
	//$startDate = date("Y")."-".date("m")."-01";
	return $startDate."~".$curRevisionSessionStartDate;
}

function populateRevisionClusters($userID, $revisionSessionID, $startDate, $endDate)
{
	$ttArray = array();
	$revisionSessionClusterArray = array();	//2D array, with the keys as TT code and clustercode.
	$clustersAttemptedArray = array();

	$teachertopiclist = "";
		$childClass = 	$_SESSION['childClass'];  
		$childSection = 	$_SESSION['childSection'];
		$schoolCode =	$_SESSION['schoolCode']; 

       
		 $query = "SELECT teacherTopicCode
		  FROM   adepts_revisionSessionMaster
		  WHERE  schoolCode=$schoolCode and isActive=1 and class=$childClass  ";

		if($childSection)
			$query .=	  " and section='$childSection' ";
			  
		$query .=	  " order by lastmodified desc limit 1";

        $result = mysql_query($query) or die(mysql_error());
        while($line = mysql_fetch_array($result))
		        $teachertopiclist = "'".str_replace(",","','",$line[0])."'";
		$startdate = explode('/',date('d/m/Y', strtotime('-60 days')));
		$startdate = $startdate[2].'-'.$startdate[1].'-'.$startdate[0]; 

	$query = "SELECT teacherTopicCode, group_concat(distinct b.clusterCode ORDER BY b.lastModified)
			  FROM   ".TBL_TOPIC_STATUS." a, ".TBL_CLUSTER_STATUS." b, adepts_clusterMaster c
			  WHERE  a.userID=$userID AND a.ttAttemptID=b.ttAttemptID and a.teacherTopicCode in ($teachertopiclist) AND b.clusterCode=c.clusterCode AND a.userID=b.userID AND b.result='SUCCESS' AND (isnull(c.clusterType) OR c.clusterType='learning') AND
			  		 (b.lastModified BETWEEN '$startdate' AND '$endDate 23:59:59')
			  GROUP BY teacherTopicCode";
	$result = mysql_query($query) or die(mysql_error().$query);
	while ($line=mysql_fetch_array($result))
	{
		array_push($ttArray,$line[0]);
		$clusterArray = explode(",",$line[1]);
		for($i=0; $i<count($clusterArray); $i++)
		{
			if(!in_array($clusterArray[$i],$clustersAttemptedArray))	//This check is for the case where a same cluster has been cleared in two different Teacher topic.
			{
				$sdlArray = getGreatestOneThirdSDL($clusterArray[$i]);
				foreach ($sdlArray as $sdl=>$ques)
				{
					$revisionSessionClusterArray[$line[0]][strtoupper($clusterArray[$i])][$sdl][0] = "";	//ques attempted in the revision session for the TT/cluster/SDL - initialize it to blank
					$revisionSessionClusterArray[$line[0]][strtoupper($clusterArray[$i])][$sdl][1] = $ques;	//all ques for this SDL
				}
				array_push($clustersAttemptedArray,$clusterArray[$i]);
			}
		}
	}
	$quecount =	count($sdlArray) * 3;
	if($quecount > 30)
	{
		$quecount = 30;
	}
	$_SESSION['questioncount'] = $quecount;
	$_SESSION['revisionSessionTTArray'] 	 = $ttArray;
	$_SESSION['revisionSessionClusterArray'] = $revisionSessionClusterArray;
}

function getRevisionSessionQuestionsAttempted($userID, $revisionSessionID)
{
	$qno = 0;
	$prevTT = $prevCluster = "";
	$revisionSessionClusterArray = $_SESSION['revisionSessionClusterArray'] ;
	//Populate the questions attempted previously by the user in this revision session, if any. (this will happen if the user closed the revision session in between)
	$query  = "SELECT teacherTopicCode, a.clusterCode,  a.qcode, b.subdifficultylevel, a.questionNo
			   FROM   adepts_revisionSessionDetails a,
			   		  adepts_questions b
			   WHERE  userID=$userID AND revisionSessionID=$revisionSessionID AND
			   		  a.qcode=b.qcode
			   ORDER BY a.questionNo";
	$result = mysql_query($query) or die(mysql_error());
	while ($line = mysql_fetch_array($result))
	{

		$qno = $line['questionNo'];
		$prevTT = $line[0];
		$prevCluster = $line[1];
		if($revisionSessionClusterArray[$line[0]][strtoupper($line[1])][$line[3]][0]!="")
		    $revisionSessionClusterArray[$line[0]][strtoupper($line[1])][$line[3]][0] .= ",".$line[2];
		else
		    $revisionSessionClusterArray[$line[0]][strtoupper($line[1])][$line[3]][0] = $line[2];

	}
	$qno++;

	$_SESSION['revisionSessionClusterArray'] = $revisionSessionClusterArray;
	return $qno."~".$prevTT."~".$prevCluster;
}

function getGreatestOneThirdSDL($clusterCode)
{
    $context            = isset($_SESSION['country'])?$_SESSION['country']:"India";
	$query  = "SELECT 	subdifficultylevel, group_concat(qcode) as qcode
			   FROM   	adepts_questions
			   WHERE  	clusterCode='$clusterCode' AND status='3' AND context in ('Global','$context') AND question NOT LIKE '%fracbox%'
			   GROUP BY subdifficultylevel ORDER BY subdifficultylevel";
	$result = mysql_query($query) or die(mysql_error());
	$sdlArray       = array();
	$questionsArray = array();
	while ($line = mysql_fetch_array($result))
	{
		array_push($sdlArray,$line[0]);
		$questionsArray[$line[0]] = $line[1];
	}
	$factor = count($sdlArray)/3;
	$highestSDLs = array();

	for($i=intval($factor); $i<count($sdlArray); $i++)		//Consider only the greatest 1/3rd of the SDLs.
	{
		$highestSDLs[$sdlArray[$i]] = $questionsArray[$sdlArray[$i]];

	}
	return $highestSDLs;
}

function getNextQuestionForRevisionSession($userID, $revisionSessionID, $prevTT, $prevCluster)
{
	$qcode = $curTT = $curCluster = "";
	$qcodeDetails = array();

	$revisionSessionClusterArray = $_SESSION['revisionSessionClusterArray'];
	$ttArray = $_SESSION['revisionSessionTTArray'];

	if($prevTT=="")	//first question in the revision session. start from the first TT
	{
		$ttCode = $ttArray[0];
		$clusterArray = array_keys($revisionSessionClusterArray[$ttCode]);
		$clusterCode  = $clusterArray[rand(0, count($clusterArray)-1)];
		$sdlArray     = array_keys($revisionSessionClusterArray[$ttCode][$clusterCode]);	//All sdls can be considered for the first case
		for($i=0; $i<count($sdlArray); $i++)
		{
			$questionsArray[$sdlArray[$i]][0] = $revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$i]][0];
			$questionsArray[$sdlArray[$i]][1] = $revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$i]][1];
		}
		$tmpResponse  = getQuestionOfCluster($userID, $clusterCode, $ttCode,$sdlArray, $questionsArray);
		$tmpResponse  = explode("~",$tmpResponse);
		$qcode        = $tmpResponse[1];
		$curCluster   = $clusterCode;
		$curTT        = $ttCode;
		$curSDL       = $tmpResponse[0];
	}
	else
	{
		for($ttCounter=0; $prevTT!=$ttArray[$ttCounter] && $ttCounter<count($ttArray); $ttCounter++);
		$ttCounter = $ttCounter+1;  //Pick question from TTs attempted in a cyclic order
		if($ttCounter>=count($ttArray))
			$ttCounter=0;

		$ttsVisited = 0;
		while ($ttsVisited<count($ttArray) && $qcode=="")
		{
			$clusterArray = array();
			$ttCode = $ttArray[$ttCounter];

			//echo $ttCode."<br/>";
			$clustersClearedInTT = array_keys($revisionSessionClusterArray[$ttCode]);

			for($i=0; $i<count($clustersClearedInTT); $i++)
			{
				$clusterCode = $clustersClearedInTT[$i];
				$sdlArray    = array_keys($revisionSessionClusterArray[$ttCode][$clusterCode]);
				$clusterGiven = 0;
				for($j=0; $j<count($sdlArray) && !$clusterGiven; $j++)
				    if($revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$j]][0]!="")
				        $clusterGiven = 1;
				if(!$clusterGiven)
				    array_push($clusterArray,$clusterCode);
			}

			if(count($clusterArray)>0)	//if there are any clusters from which question not given yet
			{
				$clusterCode  = $clusterArray[rand(0, count($clusterArray)-1)];
				$sdlArray = array_keys($revisionSessionClusterArray[$ttCode][$clusterCode]);
				$sdlToBeGiven = array();

				for($j=0; $j<count($sdlArray); $j++)
				{
					if($revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$j]][0]=="")
					{
						array_push($sdlToBeGiven,$sdlArray[$j]);
						$questionsArray[$sdlArray[$j]][0] = $revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$j]][0];
						$questionsArray[$sdlArray[$j]][1] = $revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$j]][1];
					}
				}
				$tmpResponse = getQuestionOfCluster($userID, $clusterCode, $ttCode, $sdlToBeGiven,$questionsArray);
				$tmpResponse = explode("~",$tmpResponse);
				$qcode = $tmpResponse[1];
				$curSDL = $tmpResponse[0];
				$curCluster = $clusterCode;
				$curTT = $ttCode;
			}
			else	//if all clusters in a tt are exhausted, check for the next tt
			{
				$ttsVisited++;
				//echo $ttsVisited;
				$ttCounter = $ttCounter + 1;
				if($ttCounter==count($ttArray))
				{
					$ttCounter=0;
				}
			}
		}

		if($qcode=="")	//if a question from all clusters of all tts are given
		{
			$ttsVisited = 0;
			while ($ttsVisited<count($ttArray) && $qcode=="") {
				$ttCode 		 = $ttArray[$ttCounter];
				$clustersVisited = 0;
				$clusterArray    = array_keys($revisionSessionClusterArray[$ttCode]);	//check for all clusters
				$totalClusters   = count($clusterArray);
				$clustersAlreadySeen = array();

				//till a question can be given - the terminating condition will be if all questions of highest 1/3rd sdls of all clusters attempted are given or time limit is exhausted.
				while ($qcode=="" && $clustersVisited < $totalClusters)
				{
					$clustersToBeSeen = array_diff($clusterArray,$clustersAlreadySeen);
					$tmpKeys          = array_keys($clustersToBeSeen);
					$randomNo         = mt_rand(0,count($tmpKeys)-1);
					$clusterCode      = $clustersToBeSeen[$tmpKeys[$randomNo]];
					$sdlArray         = array_keys($revisionSessionClusterArray[$ttCode][$clusterCode]);
					$sdlToBeGiven     = array();
					$questionsArray   = array();


					//print_r($sdlArray);

					for($j=0; $j<count($sdlArray); $j++)	//Check for sdls from which no question is given yet
					{
						$quesAttempted = $revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$j]][0];
						if($quesAttempted=="")
						{
							array_push($sdlToBeGiven,$sdlArray[$j]);
							$questionsArray[$sdlArray[$j]][0] = $revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$j]][0];
							$questionsArray[$sdlArray[$j]][1] = $revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$j]][1];
						}
					}

					if(count($sdlToBeGiven)>0)
					{
						$tmpResponse = getQuestionOfCluster($userID,$clusterCode,$ttCode, $sdlToBeGiven, $questionsArray);
						$tmpResponse = explode("~",$tmpResponse);
						$qcode       = $tmpResponse[1];
						$curSDL      = $tmpResponse[0];
						$curCluster  = $clusterCode;
						$curTT       = $ttCode;
					}
					array_push($clustersAlreadySeen,$clusterCode);
					$clustersVisited++;

				}
				$ttsVisited++;
				$ttCounter = $ttCounter + 1;
				if($ttCounter==count($ttArray))
				    $ttCounter=0;
			}
		}

		if($qcode=="")	//if a question from all sdls of all clusters of all tts are given
		{
			$ttsVisited = 0;
			while ($ttsVisited<count($ttArray) && $qcode=="") {
				$ttCode 		 = $ttArray[$ttCounter];
				$clustersVisited = 0;
				$clusterArray    = array_keys($revisionSessionClusterArray[$ttCode]);	//check for all clusters
				$totalClusters   = count($clusterArray);
				$clustersAlreadySeen = array();
				//till a question can be given - the terminating condition will be if all questions of highest 1/3rd sdls of all clusters attempted are given or time limit is exhausted.
				while ($qcode=="" && $clustersVisited < $totalClusters)
				{
					$clustersToBeSeen = array_diff($clusterArray,$clustersAlreadySeen);
					$tmpKeys          = array_keys($clustersToBeSeen);
					$randomNo         = mt_rand(0,count($tmpKeys)-1);
					$clusterCode      = $clustersToBeSeen[$tmpKeys[$randomNo]];
					$sdlArray         = array_keys($revisionSessionClusterArray[$ttCode][$clusterCode]);
					$sdlToBeGiven     = array();
					$questionsArray   = array();

					
					$sdls = array();
					foreach ($sdlArray as $dataarray)
					{
						array_push($sdls,$dataarray);
					}	


					for($j=0; $j<count($sdls); $j++)
					{
						$totalQues     = explode(",",$revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$j]][1]);
						$quesAttempted = explode(",",$revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$j]][0]);

						if(count($totalQues)>count($quesAttempted))
						{
							array_push($sdlToBeGiven,$sdlArray[$j]);
							$questionsArray[$sdlArray[$j]][0] = $revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$j]][0];
							$questionsArray[$sdlArray[$j]][1] = $revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$j]][1];
						}
					}

					if(count($sdlToBeGiven)>0)
					{
						$tmpResponse = getQuestionOfCluster($userID,$clusterCode,$ttCode, $sdlToBeGiven, $questionsArray);
						$tmpResponse = explode("~",$tmpResponse);
						$qcode       = $tmpResponse[1];
						$curSDL      = $tmpResponse[0];
						$curCluster  = $clusterCode;
						$curTT       = $ttCode;
					}
					array_push($clustersAlreadySeen,$clusterCode);
					$clustersVisited++;

				}
				$ttsVisited++;
				$ttCounter = $ttCounter + 1;
				if($ttCounter==count($ttArray))
				    $ttCounter=0;
			}
		}
	}

	$qcodeDetails[0] = $qcode;
	$qcodeDetails[1] = $curCluster;
	$qcodeDetails[2] = $curTT;
	$qcodeDetails[3] = $curSDL;
	return $qcodeDetails;
}

function getQuestionOfCluster($userID, $clusterCode, $ttCode, $sdlArray, $questionsArray)
{
	$questionsAlreadyAttemptedArray = array();
	if(count($sdlArray)>1)
	{
	    $ttAttemptID_query = "SELECT ttAttemptID FROM ".TBL_TOPIC_STATUS." WHERE userID=$userID AND teacherTopicCode='$ttCode'";
	    $ttAttemptID_result = mysql_query($ttAttemptID_query);
	    $ttAttemptIDStr = "";
	    while ($ttAttemptID_line = mysql_fetch_array($ttAttemptID_result))
	        $ttAttemptIDStr .= $ttAttemptID_line[0].",";
	    $ttAttemptIDStr = substr($ttAttemptIDStr,0,-1);
	    if($ttAttemptIDStr!="")
	    {
    		$query = "SELECT   subdifficultylevel, count(srno) as noOfQuesAttempted
    				  FROM 	   adepts_questions a, ".TBL_QUES_ATTEMPT_CLASS." b,".TBL_CLUSTER_STATUS." c
    				  WHERE    a.qcode=b.qcode AND a.clusterCode=c.clusterCode AND b.clusterAttemptID=c.clusterAttemptID AND
    				           c.userID=$userID AND a.clusterCode='$clusterCode' AND  subdifficultylevel in (".implode(",",$sdlArray).") AND
    				  		   ttAttemptID in ($ttAttemptIDStr)
    				  GROUP BY subdifficultylevel ORDER BY noOfQuesAttempted DESC";
    		//echo $query."<br/>";
    		$result  = mysql_query($query) or die(mysql_error());
			
			$maxsdl = array();
			while($line = mysql_fetch_array($result))
			{
				array_push($maxsdl,$line['subdifficultylevel']);
			}
			$topsdl = max($maxsdl); 
    		
    		if(!empty($topsdl))
    		    $sdl  = $topsdl;
    		else
    		    $sdl = $sdlArray[0];
	    }
	    else
    	    $sdl = $sdlArray[0];
	}
	else
	{
		$sdl = $sdlArray[0];
	}

	$qcodelist = $questionsArray[$sdl][1];
	if($qcodelist)
	{
	$MSqcode = "";
	$query = "select qcode from  ".TBL_QUES_ATTEMPT_CLASS." where qcode in ($qcodelist) and userID=$userID AND clusterCode='$clusterCode' and R = 0";
	$result  = mysql_query($query) or die(mysql_error());

	if(mysql_num_rows($result) == 0)
	{
		$query = "select qcode from  ".TBL_QUES_ATTEMPT_CLASS." where qcode in ($qcodelist) and userID=$userID AND clusterCode='$clusterCode' and R = 1";
		$result  = mysql_query($query) or die(mysql_error());
	}
	while($line = mysql_fetch_array($result))
	{
		$MSqcode .= $line['qcode'].',';
	}
	$MSattemptedqcode = rtrim($MSqcode, ',');
	}else
	{
		$MSattemptedqcode = "";
	}

	//$allQuesArray = explode(",",$questionsArray[$sdl][1]);
	$allQuesArray = explode(",",$MSattemptedqcode);
	if($questionsArray[$sdl][0]!="")
	    $questionsAlreadyAttemptedArray = explode(",",$questionsArray[$sdl][0]);
	$quesToBeGiven = array_diff($allQuesArray,$questionsAlreadyAttemptedArray);

	if(count($quesToBeGiven) == 0)
	{
		$allQuesArray = explode(",",$questionsArray[$sdl][1]);
		if($questionsArray[$sdl][0]!="")
		$questionsAlreadyAttemptedArray = explode(",",$questionsArray[$sdl][0]);
		$quesToBeGiven = array_diff($allQuesArray,$questionsAlreadyAttemptedArray);
	}

	if(count($quesToBeGiven)>0)
	{
		$tmpKeys      = array_keys($quesToBeGiven);
		$no 		  = rand(0, count($tmpKeys)-1);
		$qcode 		  = $quesToBeGiven[$tmpKeys[$no]];
	}
	return $sdl."~".$qcode;
}

function saveRevisionQuesDetails($revisionSessionID, $userID,$qno,$qcode, $clusterCode, $ttCode, $sessionID, $result, $response, $timeTaken, $dynamic, $dynamicParams, $class)
{
	if($timeTaken=="undefined") $timeTaken="NULL";
	$query = "INSERT INTO adepts_revisionSessionDetails (revisionSessionID, userID, questionNo, qcode, A, S, R, clusterCode, teacherTopicCode, sessionID, lastModified)
	           VALUES ($revisionSessionID, $userID, $qno, $qcode, '$response',$timeTaken, $result, '$clusterCode', '$ttCode', $sessionID,'".date("Y-m-d H:i:s")."')";
	mysql_query($query);
	$errno = mysql_errno();
	if($errno==0)
	{
		$quesAttempt_srno = mysql_insert_id();
		if($dynamic)
		{
			$query = "INSERT INTO adepts_dynamicParameters (userID, qcode, quesAttempt_srno, parameters, mode, class, lastModified) VALUES
		            				($userID, $qcode, ".$quesAttempt_srno.", '$dynamicParams', 'revision', '".$_SESSION['childClass']."', '".date("Y-m-d H:i:s")."')";
			mysql_query($query);
		}
		$query =  "UPDATE adepts_revisionSessionStatus SET noOfQuestions=noOfQuestions+1 WHERE revisionSessionID=$revisionSessionID AND userID=$userID";
		//echo $query;
		mysql_query($query) or die($query.mysql_error());
	}
}

function updateRevisionSessionStatus($userID, $revisionSessionID)
{
	$query  = "SELECT sum(R),count(srno) FROM adepts_revisionSessionDetails WHERE revisionSessionID=$revisionSessionID AND userID=$userID";
	$result = mysql_query($query);
	$line   = mysql_fetch_array($result);
	$correct   = $line[0];
	$totalQues = $line[1];
	$perCorrect = 0;
	if($totalQues>0)
		$perCorrect = round($correct*100/$totalQues,2);

	$noOfSparkies = 0;
	if($totalQues>=20)
	{
		if($perCorrect>=60 && $perCorrect<=74)
			$noOfSparkies = 4;
		elseif($perCorrect>=75 && $perCorrect<=89)
			$noOfSparkies = 6;
		elseif($perCorrect>=90)
			$noOfSparkies = 10;
	}
	$query = "UPDATE adepts_revisionSessionStatus SET status='completed', perCorrect=$perCorrect, noOfSparkies=$noOfSparkies WHERE revisionSessionID=$revisionSessionID AND userID=$userID";
	mysql_query($query) or die(mysql_error());

	$query = "UPDATE ".TBL_SESSION_STATUS." SET noOfJumps=$noOfSparkies WHERE userID=$userID AND sessionID=".$_SESSION['sessionID'];
	mysql_query($query);
}
function getLinkedUnattemptedPracticeContent($clusterCodes, $type, $userID){
	$practiseElement="";
	foreach ($clusterCodes as $a => $clusterCode) {
		$sq	=	"SELECT a.practiseModuleId, description FROM practiseModules a WHERE linkedToCluster='$clusterCode' AND status='Approved' AND type='$type' AND a.practiseModuleId NOT IN (SELECT b.practiseModuleId FROM practiseModulesTestStatus b WHERE b.userID=$userID) LIMIT 1";
		$rs	=	mysql_query($sq);
		while($rw=mysql_fetch_array($rs))
		{
			$practiseElement=array('practiseModuleId'=>$rw[0], 'description'=>$rw[1]);
		}
	}
	return $practiseElement;
}
function getSourcesForCEQ()
{
	$arraySorces	=	array();
	$sq	=	"SELECT DISTINCT source FROM adepts_competitiveExamMaster WHERE status=3 AND level<=".$_SESSION["childClass"];
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$arraySorces[$rw[0]]	=	$rw[0];
	}
	ksort($arraySorces);
	return $arraySorces;
}
function getCEQForTopic($userID,$ttCode){
	$pendingChallengeQuery=mysql_query("SELECT challengeNo, status, totalQues FROM adepts_competitiveExamStatus WHERE userID=$userID AND forTTcode='$ttCode'");
	if (mysql_num_rows($pendingChallengeQuery)>0){
		$pendingChallenge=mysql_fetch_assoc($pendingChallengeQuery);
		if ($pendingChallenege['status']=='Pending') return array('totQues'=>$pendingChallenge['totalQues'], 'challengeNo'=>$pendingChallenge['challengeNo'],'forTTcode'=>"$ttCode");
		else return array('totQues'=>0);
	}

	$topics=getTopicStrFromTTCode($ttCode);
	$sq	=	"SELECT topicCode,count(*),GROUP_CONCAT(qcode) FROM adepts_competitiveExamMaster 
				 WHERE 1=1 AND level <= ".$_SESSION["childClass"]." AND topicCode IN ($topics) AND status=3 GROUP BY topicCode";
	$rs	=	mysql_query($sq);
	$totalQues=0;
	while($rw=mysql_fetch_array($rs))
	{
		$qcodeArray[]	=	explode(",",$rw[2]);
		$topicStr	.=	$rw[0].",";
		$totalQues	+=	$rw[1];
	}
	if($totalQues>9)
	{
		$i=0;$j=0;$k=rand(0,count($qcodeArray)-1);
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
		$totalQues=count($qcodeNewArray);
	}
	$qcodeStr	=	implode("|",$qcodeNewArray);
	$topicStr	=	substr($topicStr,0,-1);
	$sourcesArr =	getSourcesForCEQ();
	return array('topics' => $topicStr, 'qcodes' => $qcodeStr, 'sources' => implode("|~|", $sourcesArr), 'totQues'=>$totalQues,'forTTcode'=>"$ttCode");
}
function getTopicStrFromTTCode($ttCode)
{
	$ttType = mysql_query("SELECT customTopic, parentTeacherTopicCode from adepts_teacherTopicMaster where teacherTopicCode='$ttCode'");
	$data = mysql_fetch_assoc($ttType);
	$ttCode = ($data['customTopic']==1)?$data['parentTeacherTopicCode']:$ttCode;
	
	$mapped_topic = mysql_query("SELECT mappedToTopic from adepts_teacherTopicMaster where teacherTopicCode='$ttCode'");
	$data = mysql_fetch_assoc($mapped_topic);
	$topics = explode(',', $data['mappedToTopic']);
	$topics = "'".implode("','", $topics)."'";
	return $topics;
}
function getUnattemptedEnrichmentForChoice($userID,$ttCode,$class){
	$topics=getTopicStrFromTTCode($ttCode);
	$query="SELECT gameID, gameDesc as gameName from adepts_gamesMaster where type='enrichment' AND live=1 AND topicCode IN ($topics) AND ( class='' OR ISNULL(class) OR find_in_set('$class',class)>0)";
	$result=mysql_query($query);
	if(mysql_num_rows($result)>0) {
		while($enrichment = mysql_fetch_assoc($result)) {
			if (!checkIfAttempted($enrichment['gameID'])){
				return $enrichment;
			}
		}
	}
	return '';
}
function checkForPractiseModule($userID,$cluster){
	$practiseModule='';
	$query="SELECT a.practiseModuleId, a.description FROM practiseModuleDetails a WHERE linkedToCluster = '$cluster' and a.dailyDrill=0 and a.status='Approved'";
	$result=mysql_query($query);
	if (mysql_num_rows($result)>0){
		$practiseModule=mysql_fetch_assoc($result);
	}
	return $practiseModule;
}
function isIncompletePractiseModule($userID,$practiseModule){
	$query="SELECT f.practiseModuleId FROM practiseModulesTestStatus f WHERE f.userID=$userID AND f.attemptNo=1 AND f.`status`='completed' AND f.practiseModuleId='$practiseModule'";
	$result=mysql_query($query);
	if (mysql_num_rows($result)>0){
		return false;
	}
	return true;
}
function getIncompletePractiseModule($userID,$clusterArray){
	$clusterArray=array_keys($clusterArray);
	$clusters="'".implode("','", $clusterArray)."'";
	$practiseModule='';
	$query="SELECT a.practiseModuleId, a.description FROM practiseModuleDetails a WHERE linkedToCluster IN ($clusters) and a.dailyDrill=0 and a.status='Approved' AND a.practiseModuleId NOT IN (SELECT f.practiseModuleId FROM practiseModulesTestStatus f WHERE f.userID=$userID AND f.attemptNo=1 AND f.`status`='completed')";
	$result=mysql_query($query);
	if (mysql_num_rows($result)>0){
		$practiseModule=mysql_fetch_assoc($result);
	}
	return $practiseModule;
}
function getChoiceScreenData($userID,$qcode,$choiceScreenFlag,$childClass){
	$choiceSetting=explode("~", $choiceScreenFlag);
	$choiceScreenData=array("choiceType"=>"","choices"=>array(),"choiceReject"=>"");			
	$noChoice=false;
	switch ($choiceSetting[0]) {
		case '1':
			// cluster Completion
			// give unattempted game mapped to cluster

			if ($childClass<=3) break;

			$clusterCode=$choiceSetting[1];
			$ttAttemptID=isset($_SESSION['teacherTopicAttemptID'])?$_SESSION['teacherTopicAttemptID']:getLastTTAttempt($userID, $ttCode);

			$notInGame='';
			$game = checkForGame($clusterCode, $ttAttemptID, "", "", $childClass,1);			
			if ($game=="") $noChoice=true;
			while ($game!=""){
				if (!checkIfAttempted($game['gameID'])){
					$gArray=array('gameID'=>$game['gameID'],"helpText"=>"Practice more with the game - ".$game['gameName'],'link'=>'enrichmentModule.php', 'fromCS'=>'cluster');
					if (isset($choiceScreenData['choices']['Game']))
						$choiceScreenData['choices']['Game2']=$gArray;
					else $choiceScreenData['choices']['Game']=$gArray;
				}
				$notInGame.=' AND gameID!='.$game['gameID'];
				$game = checkForGame($clusterCode, $ttAttemptID, "", "", $childClass,1,$notInGame);
			}

			if ($childClass>7){
				//non-DailyDrill Practise Modules
				$practiseModuleChoice=checkForPractiseModule($userID,$clusterCode);
				
				if ($practiseModuleChoice!=""){
					if ($noChoice==true) $noChoice=false;
					if (isIncompletePractiseModule($userID,$practiseModuleChoice['practiseModuleId'])){
						$practiseModuleChoice['helpText']="Enjoyed? Solve 15 more problems on ".$practiseModuleChoice['description'];
						$practiseModuleChoice['link']="practisePage.php";
						$practiseModuleChoice['fromCS']="cluster";
						$choiceScreenData['choices']['Solve']=$practiseModuleChoice;
					}
				}
			}

			$choiceScreenData['choiceType']='Cluster Completion';
			$choiceScreenData['choiceReject']='close';
			$choiceScreenData['choiceReject']=array("helpText"=>"NO, TAKE ME BACK TO QUESTIONS");			
			break;
		case '2':
		case '3':
			// topic Completion
			// check Accuracy

			$maxChoices=($childClass>3?4:3);

			$choiceScreenData['choiceType']='Topic Completion';
			$ttCode=$choiceSetting[1];
			$topicAccuracy=getLastTTAttemptAccuracy($userID, $ttCode);
			$arrayClusterList	=	getClusterForBucketing($userID,$ttCode);
			$notInGame='';
			$ttAttemptID=isset($_SESSION['teacherTopicAttemptID'])?$_SESSION['teacherTopicAttemptID']:getLastTTAttempt($userID, $ttCode);
			//get Unattempted Game from cluster with lowest accuracy
			asort($arrayClusterList); 
			foreach ($arrayClusterList as $clusterCode => $perf) {
			 	$game = checkForGame($clusterCode, "", "", "", $childClass,1);//echo checkIfAttempted($game['gameID']);
			 	while ($game!=""){
			 		if (!checkIfAttempted($game['gameID'])){
			 			$gameHelpText=$childClass>3?"Practice more with the game - ".$game['gameName']:"Play a game";
			 			$gArray=array('gameID'=>$game['gameID'],"helpText"=>$gameHelpText,'link'=>'enrichmentModule.php', 'fromCS'=>'topic');
			 			$selectGameForChoice=$gArray;
			 			break;
			 		}
			 		$notInGame.=' AND gameID!='.$game['gameID'];
			 		$game = checkForGame($clusterCode, "", "", "", $childClass,1,$notInGame);
			 	}
			 	if (isset($selectGameForChoice)) break;
			}
			//Enrichment
			$enrichmentForChoice=getUnattemptedEnrichmentForChoice($userID,$ttCode,$childClass);
			//OR get Unattempted Game from cluster with lowest accuracy != game
			if ($enrichmentForChoice=="" && isset($selectGameForChoice)){
				foreach ($arrayClusterList as $clustercode => $perf) {
				 	$game = checkForGame($clusterCode, "", "", "", $childClass,1);
				 	if ($game!="" && !checkIfAttempted($game['gameID']) && $game['gameID']!=$selectGameForChoice['gameID']){
				 		$enrichmentHelpText=$childClass>3?"Explore the beauty of Maths with ".$game['gameName']:"Play a game";
				 		$enrichmentForChoice=array('gameID'=>$game['gameID'], 'helpText'=>$enrichmentHelpText,'link'=>"enrichmentModule.php",'fromCS'=>"topic");
				 		break;
				 	}
				}
			}
			if ($enrichmentForChoice!="" && !isset($enrichmentForChoice['helpText'])){
				$enrichmentForChoice['helpText']=$childClass>3?"Explore the beauty of Maths with ".$enrichmentForChoice['gameName']:"Play a game";
				$enrichmentForChoice['link']="enrichmentModule.php";
				$enrichmentForChoice['fromCS']="topic";
			}
			if ($childClass>7){
				//non-DailyDrill Practise Modules
				$practiseModuleChoice=getIncompletePractiseModule($userID,$arrayClusterList);
				if ($practiseModuleChoice!=""){
					$practiseModuleChoice['helpText']="Enjoyed? Solve 15 more problems on ".$practiseModuleChoice['description'];
					$practiseModuleChoice['link']="practisePage.php";
					$practiseModuleChoice['fromCS']="topic";
				}
			}

			//Higher Class
			if ($qcode==-8 && isHigherClassOptionAvailable($ttCode,$userID,$ttAttemptID,$childClass))
			{
				$higherClassHelpText=$childClass>3?"Learn more advanced (higher level) concepts.":"Go to Higher Level";
				$higherClassForChoice=array('ttCode'=>$ttCode,'higherLevel'=>1,'mode'=>"classLevelCompletion","helpText"=>$higherClassHelpText,'link'=>'controller.php');
			}
			else 
				$higherClassForChoice="";

			//Repeat Topic
			$repeatHelpText=$childClass>3?"Repeat the entire topic once again.":"Revise this topic";
			$repeatTopicForChoice=array('ttCode'=>$ttCode,'higherLevel'=>2,'mode'=>"ttSelection","helpText"=>$repeatHelpText,'link'=>'controller.php');

			if($topicAccuracy<70) {	// <70% choices, max 2 when arrived from question page, else max 4
				$numChoices=($choiceSetting[0]==2 && !(isset($_SESSION['higherLevel']) && $_SESSION['higherLevel']==1))?2:$maxChoices;
				
				if ($childClass>7){
					//IYC
					$bronzeBucket	=	array();
					$arrayClusterBucketing	=	bucketLogicCalculation($userID,$ttCode,$arrayClusterList);
					$bronzeBucket	=	$arrayClusterBucketing[0];
					$iycForChoice="";
					if (count($bronzeBucket)>0){
						$iycForChoice=array('ttCode'=>$ttCode,"helpText"=>"Get better at some concepts.",'link'=>'improveConcepts.php');
						$choiceScreenData['choices']['IYC']=$iycForChoice;
					}
				}

				//topicPractice
				if ($childClass<=3 && count($choiceScreenData['choices'])<$numChoices){
					$choiceScreenData['choices']['topicPractice']=array('ttCode'=>$ttCode,'userID'=>$userID,'childClass'=>$childClass,'mode'=>"topicRevision","helpText"=>"Practice",'link'=>'controller.php');
				}

				//unattempted Game
				if ($selectGameForChoice!="" && $childClass>3)
					$choiceScreenData['choices']['Game']=$selectGameForChoice;
				//StepByStep
				if ($practiseModuleChoice!="" && count($choiceScreenData['choices'])<$numChoices)
					$choiceScreenData['choices']['Solve']=$practiseModuleChoice;
				//repeatTopic
				if ($repeatTopicForChoice!="" && count($choiceScreenData['choices'])<$numChoices)
					$choiceScreenData['choices']['repeatTopic']=$repeatTopicForChoice;
				//unattempted Enrichment
				if ($enrichmentForChoice!="" && count($choiceScreenData['choices'])<$numChoices && $childClass>3)
					$choiceScreenData['choices']['Enrichment']=$enrichmentForChoice;
				if (($enrichmentForChoice!="" || $selectGameForChoice!="") && count($choiceScreenData['choices'])<$numChoices && $childClass<=3)
					$choiceScreenData['choices']['Game']=$selectGameForChoice!=""?$selectGameForChoice:$enrichmentForChoice;

				//higherClass
				if ($higherClassForChoice!="" && count($choiceScreenData['choices'])<$numChoices)
					$choiceScreenData['choices']['HigherClass']=$higherClassForChoice;
			}
			else {	// >70% choices, max 4

				//unattempted Game
				if ($selectGameForChoice!="" && $childClass>3)
					$choiceScreenData['choices']['Game']=$selectGameForChoice;
				//unattempted Enrichment
				if ($enrichmentForChoice!="" && $childClass>3)
					$choiceScreenData['choices']['Enrichment']=$enrichmentForChoice;
				if (($enrichmentForChoice!="" || $selectGameForChoice!="") && count($choiceScreenData['choices'])<$maxChoices && $childClass<=3)
					$choiceScreenData['choices']['Game']=$selectGameForChoice!=""?$selectGameForChoice:$enrichmentForChoice;

				if ($childClass>7){
					//StepByStep
					if ($practiseModuleChoice!="" && count($choiceScreenData['choices'])<$maxChoices)
						$choiceScreenData['choices']['Solve']=$practiseModuleChoice;

					//CEQ
					$ceqForChoice=getCEQForTopic($userID,$ttCode);
					if ($ceqForChoice['totQues']>9){
						$ceqForChoice['helpText']="Take the challenge of solving questions from competitive exams!";
						$ceqForChoice['link']='competitiveExam.php';
						$gArray=$ceqForChoice;
						$choiceScreenData['choices']['CEQ']=$gArray;
					}
				}
				//higherClass
				if ($higherClassForChoice!="" && count($choiceScreenData['choices'])<$maxChoices)
					$choiceScreenData['choices']['HigherClass']=$higherClassForChoice;
				//topicPractice
				if ($childClass<=3 && count($choiceScreenData['choices'])<$maxChoices){
					$choiceScreenData['choices']['topicPractice']=array('ttCode'=>$ttCode,'userID'=>$userID,'childClass'=>$childClass,'mode'=>"topicRevision","helpText"=>"Practice",'link'=>'controller.php');
				}
				//repeatTopic
				if ($repeatTopicForChoice!="" && count($choiceScreenData['choices'])<$maxChoices)
					$choiceScreenData['choices']['repeatTopic']=$repeatTopicForChoice;
			}
			
			//Close Choice Screen
			$rejectHelpText=$childClass>3?"NO, TAKE ME TO THE DASHBOARD":"Dashboard";
			$choiceScreenData['choiceReject']=array('ttCode'=>$ttCode,'higherLevel'=>0,'mode'=>"classLevelCompletion","helpText"=>$rejectHelpText,'link'=>'controller.php');
			//if ($choiceSetting[0]==2){
				$choiceScreenData['choiceReject']['fromQuesPage']=1;
			//}
			break;
		
		default:
			# code...
			break;
	}
	$choices=array_keys($choiceScreenData['choices']);

	$query="INSERT INTO adepts_userChoices (userID,sessionID,ttAttemptID,choiceAfter,choiceType,numberOfChoices,choices,topicAccuracy, choiceGivenAt) 
			VALUES ($userID, ".$_SESSION['sessionID'].",$ttAttemptID,'$choiceSetting[1]',$choiceSetting[0],".count($choices).",'".mysql_escape_string(json_encode($choices))."',".(isset($topicAccuracy)?$topicAccuracy:0).", NOW())";
	if (!$noChoice){
		$result = mysql_query($query);
		$choiceScreenData['choiceID'] = mysql_insert_id();
	}
	if ($childClass<=3) $choiceScreenData['choiceTheme'] = "New";
	return $choiceScreenData;
}

function createResponse($qcode,$quesno,$quesCategory,$showAnswer="1",$tmpMode="",$choiceScreenFlag=0,$displayText="")
{	
	$_SESSION['pageStartTime'] = date("Y-m-d H:i:s");
	$childClass  = $_SESSION['childClass'];
	$sessionID   = $_SESSION['sessionID'];
	
	// Add default values to response (return default response when qcode is negative)
	$response = array(
		"qcode"=>$qcode, "totalSDLs"=>"", "tmpMode"=>$tmpMode, "quesCategory"=>$quesCategory, "showAnswer"=>$showAnswer,
		"correctAnswer"=>"", "noOfBlanks"=>0, "quesType"=>"", "clusterCode"=>"", "hasExpln"=>0, "Q1"=>"", "Q2"=>"",
		"optionA"=>"", "optionB"=>"", "optionC"=>"", "optionD"=>"", "eeIcon"=>"", "dispAns"=>"", "footer"=>"", "sparkie"=>"",
		"pnlCQ"=>"", "pnlWC"=>"", "voiceover"=>"", "hint"=>"", "dropdownAns"=>"", "dynamicQues"=>"", "dynamicParams"=>"",
		"preloadDisplayAnswerImage"=>"", "problemid"=>"", "quesVoiceOver"=>"", "ansVoiceOver"=>"", "noOfTrials"=>1,
		"dispAnsA"=>"", "dispAnsB"=>"", "dispAnsC"=>"", "dispAnsD"=>"", "hintAvailable"=>0, "quesAttemptedInTopic"=>"",
		"quesCorrectInTopic"=>"", "progressInTopic"=>"", "lowerLevel"=>"", "condition"=>"", "action"=>"", "noOfCondition"=>"", 
		"isTough"=>"", "sdlList"=>"", "clusterProgress"=>"", "SDLAttempts"=>"", "clusterAttemptsArr"=>"", 
		"clusterNameAttemptsArr"=>"", "topicProgressStatus"=>"", "clusterStatusPrompts"=>"", "clusterName"=>"",
		"higherLevel"=>"", "badgeType"=>"", "getWindowName"=>"", "daUserAnswer"=>"", "displayText"=>""
	);
	$response["badgeType"]	=	"";
	
	if($quesCategory == 'wildcard')
		$response['displayText'] = $displayText;

	if ($quesCategory=='practiseModule'){
		if (isset($_SESSION['dailyDrillArray']['currentScore']))
			$response['currentScore']=$_SESSION['dailyDrillArray']['currentScore'];
		if (isset($_SESSION['dailyDrillArray']['currentLevel']))
			$response['currentLevel']=$_SESSION['dailyDrillArray']['currentLevel'];
		if (isset($_SESSION['dailyDrillArray']['levelsAttempted']))
			$response['levelsAttempted']=$_SESSION['dailyDrillArray']['levelsAttempted'];
		if (isset($_SESSION['dailyDrillArray']['completionSparkie']))
			$response['completionSparkie']=$_SESSION['dailyDrillArray']['completionSparkie'];
		if (isset($_SESSION['dailyDrillArray']['accuracy']))
			$response['accuracy']=$_SESSION['dailyDrillArray']['accuracy'];
		if (isset($_SESSION['dailyDrillArray']['avgTimePerQues']))
			$response['avgTimePerQues']=$_SESSION['dailyDrillArray']['avgTimePerQues'];
		if (isset($_SESSION['dailyDrillArray']['benchmark50']))
			$response['benchmark50']=$_SESSION['dailyDrillArray']['benchmark50'];
		if (isset($_SESSION['dailyDrillArray']['benchmark75']))
			$response['benchmark75']=$_SESSION['dailyDrillArray']['benchmark75'];
		$response["attemptNo"] = $_SESSION['dailyDrillArray']['attemptNo'];
	}
	if ($quesCategory=='worksheet'){
		$response["wsAnsweredArray"] = $_SESSION['wsAnsweredArray'];
	}
	if($_SESSION["rewardSystem"]==1 && count($_SESSION["arrayPrompts"])>1 && $_SESSION['noOfJumps']>=$_SESSION["arrayPrompts"]["minValue"])
	{
		$objReward	=	new Sparkies($_SESSION["userID"]);
		$response["badgeType"]	=	$objReward->checkForSparkieAlertsCurrent();
	}
	if(isset($_SESSION["windowName"]))
		$response["getWindowName"]	=	$_SESSION["windowName"];
	$choiceScreenData="";
	if (($choiceScreenFlag!=0 || ($choiceScreenFlag==0 && ((isset($_SESSION['choiceScreenAfterTimedTest']) && $_SESSION['choiceScreenAfterTimedTest']!=0) || (isset($_SESSION['choiceScreenAfterBonusCQ']) && $quesCategory!='bonusCQ' &&  $_SESSION['choiceScreenAfterBonusCQ']!=0)))) && isChoiceScreenSchool($childClass)/* && !(isset($_SESSION['higherLevel']) && $_SESSION['higherLevel']==1)*/){
		if ($choiceScreenFlag==0 && (isset($_SESSION['choiceScreenAfterTimedTest']) || (isset($_SESSION['choiceScreenAfterBonusCQ']) && $quesCategory!='bonusCQ')))
		{
			if (isset($_SESSION['choiceScreenAfterTimedTest'])){
				$choiceScreenFlag=$_SESSION['choiceScreenAfterTimedTest'];
				unset($_SESSION['choiceScreenAfterTimedTest']);
			}
			else if (isset($_SESSION['choiceScreenAfterBonusCQ']) && $quesCategory!='bonusCQ'){
				$choiceScreenFlag=$_SESSION['choiceScreenAfterBonusCQ'];
				unset($_SESSION['choiceScreenAfterBonusCQ']);
			}
		}
		else if ($choiceScreenFlag!=0 && $quesCategory!='bonusCQ'){
			unset($_SESSION['choiceScreenAfterBonusCQ']);
		}
		$choiceScreenData=getChoiceScreenData($_SESSION["userID"],$qcode,$choiceScreenFlag,$childClass);

		$response['choiceScreen']=$choiceScreenData;
	}
	if($qcode < 0 || (isset($response["nextLevelType"]) && $response["nextLevelType"]=='timedTest')){
		return json_encode($response);
	}
	// default values end here

	if(isset($_SESSION['examCornerCluster']) && $_SESSION['examCornerCluster']==1 && $_SESSION['iycAsChoice']){
		$response['iycAsChoice']=1;
	}
	if ($quesCategory=='practiseModule'){
		$response["nextLevelType"]	=	$_SESSION['dailyDrillArray']['nextLevelType'];
	}
	if($quesCategory == "NCERT")
	{
		$qcodeArray = explode("##",$qcode);
		$response["qcode"] = $qcode;
		$response["tmpMode"] = "NCERT";
		$response["quesCategory"] = "NCERT";
		$response["showAnswer"]	=	0;
		foreach($qcodeArray as $singleqCode)
		{
			$question = new ncertQuestion($singleqCode);
			if($question->isDynamic())
				$question->generateQuestion();
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
			$Q1[] = $question->subQuestionNo;
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
		//if($quesCategory == "NCERT")
		$Q1 = implode("##",$Q1);
		$Q2 = implode("##",$Q2);
		$Q4 = implode("##",$Q4);
		$eeIcon = implode("##",$eeIcon);//Equation Editor Icon Flag
		$ansForDisplay = implode("##",$ansForDisplay);
		$sql = "SELECT groupText, groupNo, groupColumn FROM adepts_groupInstruction WHERE groupID = $question->groupID";
		$result = mysql_query($sql) or die("B".mysql_error().$sql." ------- qcode: ".$qcode);
		$row = mysql_fetch_assoc($result);
		$Q5 = $row["groupText"]; // Group Title will go here...
		$group_title = $row["groupText"];
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
			if($row[2]=="" && $row[3]=="")
				$Q7 .= "[eeresponse]"."##";
			else
				$Q7 .= "[eeresponse]".$row[2]."@$*@".$row[3]."##";
			$Q9 .= $row[1]."##";
			$Q8 += $row[1];
		}
		$Q7 = substr($Q7,0,-2);
		$Q9 = substr($Q9,0,-2);
		$Q8 = ($Q8<0)?0:1;
		if($correctAnswer == "") $correctAnswer = "";
        $response["noOfBlanks"]  = $noOfBlanks;
		$response["correctAnswer"]	=	$correctAnswer;
		$response["quesType"]	= $quesType;
		$response["clusterCode"] = $question->exerciseCode;
		$response["Q1"] = $Q1;
		$response["Q2"]	= $Q2;
		if($Q4 == "") $Q4 = "";
		$response["Q4"]	= $Q4;
		if($eeIcon!="" )
			$response["eeIcon"]	= $eeIcon;//Equation Editor Icon Flag

		$response["dispAns"]	= $Q5; // Group Title will go here...

		$category = $_SESSION['admin'];
		if(isset($_SESSION["userType"]) && $_SESSION["userType"] == "msAsStudent")
		{
			$footer = '';
			$exerciseName = "";
			$chapterName = "";
			$get_exercise_name = "SELECT chapterName, description FROM adepts_ncertExerciseMaster WHERE 
				exerciseCode = '".$question->exerciseCode."'";
			$exec_exercise_name = mysql_query($get_exercise_name);
			if(mysql_num_rows($exec_exercise_name) > 0) {
				$row_exercise_name = mysql_fetch_array($exec_exercise_name);
				$exerciseName = $row_exercise_name['description'];
				$chapterName = $row_exercise_name['chapterName'];
			}
			
			$footer = "<span class=\"ttInfo\" style=\"width: 250px; word-wrap:break-word;\">";
			$footer .= "<a style='text-decoration:underline' href='javascript:void(0)' onclick=\"$('#msAsStudentInfo').html('')\">Hide</a><br>";
			$footer .= "<strong>Chapter: </strong>".$chapterName."<br>";
			$footer .= "<strong>Exercise Code: </strong>".$question->exerciseCode."<br>";
			$footer .= "<strong>Exercise Name: </strong>".$exerciseName."<br>";
			$footer .= "<strong>Group No: </strong>".$groupNo." <a href=\"http://www.educationalinitiatives.com/mindspark/addEditPCQuestions.php?type=NCERT&mode=editGroup&ID=".$question->groupID."\" target=\"_blank\">[Edit]</a><br>";
			$footer .= "<strong>Qcode: </strong>".$qcode;
			$footer .= "</span>";
			$response["footer"]	=	$footer;
		}

		$sparkie = "";
       	$tmpCount = $_SESSION['noOfJumps'];
        if($childClass<8)
        {
           $sparkie = $tmpCount;
        }
        else
        {
			if($_SESSION['rewardSystem']==0)
        		$rewardPoints = $tmpCount * 10;
			else
				$rewardPoints = $tmpCount;
        	if($rewardPoints>0)
        	{
        		$sparkie .= "<span class='reward_points'>Reward Points: ".$rewardPoints."</span>";
        	}
        }

        if($sparkie!='')
        	$response["sparkie"]	= $sparkie;

		$response["voiceover"]	= $ansForDisplay;
		$response["hint"]	=	$hint; // Group Column will go here...
		$encryptedDropDownAns = implode("##",$encryptedDropDownAns);
		if(trim($encryptedDropDownAns) == "")
			$encryptedDropDownAns = "";
		$response["dropdownAns"]	=	$encryptedDropDownAns;
		$response["problemid"]	= "Session ID:".$sessionID." Question No:".$quesno;
		//Added by chirag for custom display answers on May, 21, 2012 ---start here
		if(isset($groupNo) && $groupNo != "")
			$response["dispAnsA"]	= $groupNo;

		if($Q7 == "") $Q7 = "";
		$response["dispAnsB"]	= $Q7;
		$response["dispAnsC"]	= $Q8;
		$response["dispAnsD"]	= $Q9;
		$response["hintAvailable"]	= "0";
		if($_SESSION['quesAttemptedInTopic'])
			$response["quesAttemptedInTopic"]	= $_SESSION['quesAttemptedInTopic'];
		if($_SESSION['quesCorrectInTopic'])
			$response["quesCorrectInTopic"]	= $_SESSION['quesCorrectInTopic'];
		if($_SESSION['progressInTopic'])
			$response["progressInTopic"]	= $_SESSION['progressInTopic'];
		if($_SESSION['lowerLevel'])
			$response["lowerLevel"]	= $_SESSION['lowerLevel'];
        $response["clusterProgress"] = $_SESSION['clusterProgress'];
	}
	else
	{
		if($quesCategory=="sba")
			$question = new sbaQuestion($qcode);
        else if($quesCategory=="diagnosticTest")
			$question = new diagnosticTestQuestion($qcode);
		else if($tmpMode=="research")
			$question = new researchQuestion($qcode);
		else if($quesCategory=="daTest")
			$question = new daTest($qcode);
		else if($quesCategory=="worksheet")
			$question = new WorksheetQuestion($qcode);
		else if ($quesCategory=="kstdiagnosticTest"){
			$question = new Question($qcode);
		}
		else 
			$question = new Question($qcode);

		if($question->isDynamic())
			$question->generateQuestion();
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
		if($childClass<3 && $tmpMode!="research" && $question->isDynamic()!=1)
		{
			$quesVoiceOver = VOICEOVER_FOLDER_AUTOMATED."/".substr($question->clusterCode,0,3)."/".$qcode.".mp3";
		}
		
		if($question->ansVoiceOver!="" && $childClass<3 && $_SESSION['flashContent']==1)
		{
			$ansVoiceOver = VOICEOVER_FOLDER."/".substr($question->clusterCode,0,3)."/".$question->ansVoiceOver;
		}
		$hasExpln = $question->hasExplanation();

		$displayAns = $question->getDisplayAnswer();
		if(!$_SESSION['flashContent'])
		{
			if(stristr($displayAns,".swf"))
			{
				$displayAns = $question->getCorrectAnswerForDisplay();
				$hasExpln = 0;
			}
		}
		$display_answer  = encrypt($displayAns);
		//Added by chirag for custom display answers on May, 21, 2012 ---start here
		if($quesCategory!="wildcard" && $quesCategory!="diagnosticTest")
		{
			$display_answerA = encrypt($question->getDisplayAnswerA());
			$display_answerB = encrypt($question->getDisplayAnswerB());
			$display_answerC = encrypt($question->getDisplayAnswerC());
			$display_answerD = encrypt($question->getDisplayAnswerD());
			$pnlWC	=	"";
		}
		else
		{
			$display_answerA = "";
			$display_answerB = "";
			$display_answerC = "";
			$display_answerD = "";
			$showAnswer	=	1;
		}
		//----End
			
			$response["qcode"]	= $qcode;
            if($_SESSION['progressBarFlag'] && $quesCategory!="diagnosticTest")
			{
                if($tmpMode!="remedial")
                    $_SESSION['sdlAttemptResult'][$_SESSION["currentSDL"]] = 5;
				//Pending Integration
				if(array_key_exists("",$_SESSION['sdlAttemptResult']))
				{
					$response["totalSDLs"] = count($_SESSION['sdlAttemptResult']) - 1;
					unset($_SESSION['sdlAttemptResult'][""]);
				}	
				else
					$response["totalSDLs"] = count($_SESSION['sdlAttemptResult']);
				$response["SDLAttempts"] = $_SESSION['sdlAttemptResult'];
                $response["clusterAttemptsArr"] = $_SESSION['classSpecificClustersForTT'];
                $response["clusterNameAttemptsArr"] = $_SESSION['classSpecificClustersNameForTT'];
				$response['topicProgressStatus'] = $_SESSION['topicWiseProgressStatus']; 
                $response['clusterStatusPrompts'] = $_SESSION['clusterStatusPrompt'];         
                $response['clusterName'] = getClusterDetails($_SESSION['clusterCode'],"",1);
				$response['higherLevel'] = $_SESSION['higherLevel'];    
         
			}

			if($quesCategory=='challenge')
				$tmpMode = $_SESSION['challengeQuestionsOtherFlag'];
			if($tmpMode!='')
				$response["tmpMode"]	= $tmpMode;

			if($quesCategory!='')
				$response["quesCategory"]	= $quesCategory;

			if($showAnswer!='')
				$response["showAnswer"]	= $showAnswer;
			if($encryptedCorrectAns!='')
				$response["correctAnswer"]	= $encryptedCorrectAns;
			$response["noOfBlanks"] = $noOfBlanks;
			if($question->quesType!='')
				$response["quesType"]	=	$question->quesType;

			if($question->clusterCode!='')
				$response["clusterCode"]	=	$question->clusterCode;

			$response["hasExpln"]	= $hasExpln;

			if($quesCategory!="bonusCQ" && $quesCategory!="challenge" && $quesCategory!="wildcard" && strcasecmp($quesCategory,"EoLCQ")!=0)
			{
				if($quesno!='')
					$response["Q1"]	=	$quesno;
			}
			
			$questionStr = $question->getQuestion();

			if($quesCategory == "daTest")
			{
				$daAnswerStr = $question->getAnswer();
				if($daAnswerStr!="")
					$response["daUserAnswer"] = $daAnswerStr;

				$DaTimeRemaining = intval($question->getDaTimeRemaining()/60);
				if($DaTimeRemaining<30 && ($question->getDaTimeRemaining() % 60)>0)
					$DaTimeRemaining++;
				$response["DaTimeRemaining"] = $DaTimeRemaining;

			}
			if($quesCategory == "worksheet")
			{
				$wsAnswerStr = $question->getAnswer();
				if($wsAnswerStr!="")
					$response["wsUserAnswer"] = $wsAnswerStr;

				$WSTimeRemaining = getWorksheetTimeRemaining();
				$response["WSTimeRemaining"] = $WSTimeRemaining;
				// $response["wsAnsweredArray"] = getWorksheetAnsweredArray();
			}

			if($questionStr!='')
				$response["Q2"]	= $questionStr;
			
            $optionA = $optionB = $optionC = $optionD = "";
			if($question->quesType=='MCQ-4' || $question->quesType=='MCQ-3' || $question->quesType=='MCQ-2')
			{
				if($quesCategory=="diagnosticTest")
				{
					$randomOptions = randomizeOptions($question);
					$optionA = $randomOptions[0];
					$optionB = $randomOptions[1];
					if($question->quesType=='MCQ-4' || $question->quesType=='MCQ-3')
					{
						$optionC = $randomOptions[2];
					}
					if($question->quesType=='MCQ-4')
					{
						$optionD = $randomOptions[3];
					}
				}
				else
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
			}
		   $response["optionA"] = $optionA;
		   $response["optionB"] = $optionB;
           $response["optionC"] = $optionC;
           $response["optionD"] = $optionD;
	       if($question->eeIcon!="")
				$response["eeIcon"]	= $question->eeIcon;//Equation Editor Icon Flag

		   if($display_answer!='')
				$response["dispAns"]	=	$display_answer;

		   $category = $_SESSION['admin'];
		   $footer='';

			if(isset($_SESSION["userType"]) && $_SESSION["userType"] == "msAsStudent" && !isset($_SESSION["userTypeS"]) && $_SESSION["userTypeS"] != "teacherAsStudent")
			{	
				$totalSDL = count(array_keys($_SESSION["allQuestionsArray"]));
				$currentSDL = array_search($question->subDifficultyLevel,array_keys($_SESSION["allQuestionsArray"])) + 1;
				$clusterLevel =  $clusterDetails["level"];
				$clusterName =  $clusterDetails["cluster"];
				$remedialCluster = $clusterDetails["remedialCluster"];
				$footer .= "<span class=\"ttInfo\"><strong>Drag me!</strong><br/><a style='text-decoration:underline' id='tagtoModify".$qcode."' href='javascript:void(0)' onclick=\"setTryingToUnload();showTagBox('tagMsgBox', '', '".$qcode."');\">Need to modify</a><br><a style='text-decoration:underline' href='javascript:void(0)' onclick=\"setTryingToUnload();$('#msAsStudentInfo').html('')\">Hide</a><br><strong>Qcode: </strong><a href=\"http://www.educationalinitiatives.com/mindspark/add_edit_question_viewmode.php?qcode=".$qcode."\" target=\"_blank\">".$qcode."</a><br><strong>Cluster Code: </strong>".$question->clusterCode."<br><strong>SDL</strong> $currentSDL of $totalSDL<span><br><strong>Remedial Unit: </strong>".$remedialCluster."<br><strong>".$_SESSION["flow"]." Level: </strong>".$clusterLevel."<br><strong>Cluster: </strong>".$clusterName."</span></span>";
			}
			$footer .= "<br><div id='img_error'></div>";

		   $response["footer"]	= $footer;

		   $sparkie = "";
		   $tmpCount = $_SESSION['noOfJumps'];
			if($childClass<8)
			{
				$sparkie = $tmpCount;
			}
			else
			{
				$rewardPoints = $tmpCount;
				if($rewardPoints>0)
				{
					$sparkie .= "<span class='reward_points'>Reward Points: ".$rewardPoints."</span>";
				}
			}

			if($sparkie!='')
				$response["sparkie"]	=	$sparkie;
			else
				$response["sparkie"]	=	"";

			if(trim($quesVoiceOver)!="" && voiceFileNotExists($quesVoiceOver)==0)
				$voiceover = 1;
			else
				$voiceover = 0;

			if($voiceover!='')
				$response["voiceover"]	=	$voiceover;
    
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
			}

			if($encryptedDropDownAns!='')
				$response["dropdownAns"]	=	$encryptedDropDownAns;

			if($question->isDynamic()!='')
				$response["dynamicQues"]	=	$question->isDynamic();

			if($question->dynamicParams!='')
				$response["dynamicParams"]	= $question->dynamicParams;

			$preloadStr = $question->displayAnswer." ".$question->questionStem." ".$question->optionA." ".$question->optionB." ".$question->optionC." ".$question->optionD;

			$preloadStr = checkImage($preloadStr);        //Check and preload images if any in the display answer.

			if($preloadStr!='')
				$response["preloadDisplayAnswerImage"]	= $preloadStr;

			$problemid = "Session ID:".$sessionID." Question No:".$quesno;

			$response["problemid"]	=	$problemid;

			if($quesVoiceOver!='' && voiceFileNotExists($quesVoiceOver)==0)
				$response["quesVoiceOver"]	= $quesVoiceOver;

			if($ansVoiceOver!='')
				$response["ansVoiceOver"]	= $ansVoiceOver;

			if($question->noOfTrials!="")
				$response["noOfTrials"]	=	$question->noOfTrials;

			//Added by chirag for custom display answers on May, 21, 2012 ---start here
			if($display_answerA!='')
				$response["dispAnsA"]	= $display_answerA;

			if($display_answerB!='')
				$response["dispAnsB"]	=	$display_answerB;

			if($display_answerC!='')
				$response["dispAnsC"]	=	$display_answerC;

			if($display_answerD!='')
				$response["dispAnsD"]	=	$display_answerD;

			if($quesCategory!="wildcard" && $quesCategory!="diagnosticTest" && $quesCategory!="daTest" && $quesCategory!="worksheet")
				$response["hintAvailable"]	= $question->hintAvailable;

			if($_SESSION['quesAttemptedInTopic'])
				$response["quesAttemptedInTopic"]	= $_SESSION['quesAttemptedInTopic'];
				
			if($_SESSION['quesCorrectInTopic'])
				$response["quesCorrectInTopic"]	= $_SESSION['quesCorrectInTopic'];
				
			if($_SESSION['progressInTopic'])
				$response["progressInTopic"]	= $_SESSION['progressInTopic'];

			$response["lowerLevel"] = $_SESSION['lowerLevel'];
			if($condition!="")
				$response["condition"]	=	$condition;

			if($action!="")
				$response["action"]	=	$action;
			if($noOfCondition!="")
				$response["noOfCondition"]	=$noOfCondition;

			$response["isTough"]	= "NOTTOUGH";
			
			if($childClass > 3 && $quesCategory=="normal" && $response["noOfTrials"]==1 && $tmpMode!="remedial") 
			{
				$query="select accuracy,noOfAttempts from adepts_questionPerformance where qcode=$qcode and class=$childClass order by majorVersion desc limit 1";
	
				$result = mysql_query($query);
				if($row = mysql_fetch_row($result))
				{
					if($row[0] <= 50 && $row[0]!=null && $row[1] >=100 ) 
					{
						$response["isTough"]	= "TOUGH"; 
					} 
				}
			}
			
		$response["sdlList"] = $_SESSION["currentSDL"];
		//------End
	}
	
	// for prompt on repletion of topic (show on very first attempt of topic)
	if($quesCategory == "normal" && !empty($_SESSION['teacherTopicAttemptID']) && $_SESSION["quesAttemptedInAttempt"] != $_SESSION['teacherTopicAttemptID']) {
		// if topic attempt number is not there, get it
		if(empty($_SESSION['topicAttemptNo'])) {
			$response['topicRepeatAttempt'] = getTopicAttemptNo($_SESSION['teacherTopicAttemptID']);
			$_SESSION['topicAttemptNo'] = $response['topicRepeatAttempt'];
		} else {
			$response['topicRepeatAttempt'] = $_SESSION['topicAttemptNo'];
		}
		
		// check no. of question attempted in current attempt
		if($response['topicRepeatAttempt'] > 2) {
			$getCurrentAttemptQues = "SELECT noOfQuesAttempted FROM ".TBL_TOPIC_STATUS." WHERE ttAttemptID=".$_SESSION['teacherTopicAttemptID'];
			$execCurrentAttemptQues = mysql_query($getCurrentAttemptQues);
			if(mysql_num_rows($execCurrentAttemptQues) > 0) {
				$rowCurrentAttemptQues = mysql_fetch_array($execCurrentAttemptQues);
				if(!empty($rowCurrentAttemptQues['noOfQuesAttempted']))
					unset($response['topicRepeatAttempt']);
			}
		}
		$_SESSION["quesAttemptedInAttempt"] = $_SESSION['teacherTopicAttemptID'];	// set this variable to execute above code only once

	}
	if($quesCategory=="diagnosticTest")
			$response['dignosticTestTotalQuestion'] = $_SESSION["diagnosticTestTotalQuestions"];
	if($_SESSION['comprehensiveCluster'])
		$response['comprehensiveCluster'] = $_SESSION["comprehensiveCluster"];
	if($_SESSION["comprehensiveModuleCompleted"])	
	{
		$response['comprehensiveModuleCompleted'] = $_SESSION["comprehensiveModuleCompleted"];
		unset($_SESSION["comprehensiveModuleCompleted"]);
	}
	
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

function removeSession($table, $sessionID)
{
	$query = "SELECT logout_flag FROM $table WHERE sessionID=".$sessionID;
	$result = mysql_query($query) or die(mysql_error());
	$line = mysql_fetch_array($result);
	if($line['logout_flag']==1)
	{
		return "endsession";
	}
	return "";
}

function getFlowForTT($userID, $teacherTopicCode)
{
	$flow = "MS";
	$objUser = new User($userID);
	//For school user pick up the flow that the teacher has chosen
	if(strcasecmp($objUser->category,"STUDENT")==0 && (strcasecmp($objUser->subcategory,"School")==0 || strcasecmp($objUser->subcategory,"Home Center")==0))
	{
		$query = "SELECT flow FROM adepts_teacherTopicActivation
		          WHERE  schoolCode=".$objUser->schoolCode." AND
		                 class=".$objUser->childClass." AND
		                 teacherTopicCode='$teacherTopicCode'";
		if($objUser->childSection!="")
			$query .= " AND section='".$objUser->childSection."'";
		$query .= " ORDER BY srno DESC LIMIT 1";
		$result = mysql_query($query) or die("Error in fetching the flow for the topic!");
		if($line=mysql_fetch_array($result))
		    $flow   = $line['flow'];
		else
		{
		    $query  = "SELECT flow FROM adepts_studentTopicActivation WHERE userID=$userID AND teacherTopicCode='$teacherTopicCode'";
		    $result = mysql_query($query);
		    if($line=mysql_fetch_array($result))
				$flow   = $line['flow'];
			else
			{
				echo '<html><body>';
				echo '<script>alert("This topic is not activated for you.");window.location.href="error.php";</script></body></html>';
				break;
			}
		}
	}
	else
	{
		//for retail and other users (Admin or guest), default flow will be MS.
		/*During the transition to the new system, check if the user has attempted the topic in old flow, if so give him the old flow.
		This condition can be removed later when all non-school users have shifted to MS flow*/
		$query  = "SELECT count(ttAttemptID) , flow FROM ".TBL_TOPIC_STATUS." WHERE userID=$userID AND teacherTopicCode='$teacherTopicCode'  ORDER BY ttAttemptID DESC";
		$result = mysql_query($query) or die("Error in fetching previous flow!");
		$line   = mysql_fetch_array($result);
		if($line[0]==0)	//i.e. starting the topic for the first time
            $flow = "MS";
        else 
        	$flow = $line[1];
	}
	return $flow;
}

function getTopicAttemptNo($ttAttemptID)
{
    $ttAttemptNo = 1;
    $query  = "SELECT ttAttemptNo FROM ".TBL_TOPIC_STATUS." WHERE ttAttemptID=$ttAttemptID";
    $result = mysql_query($query);
    if($line   = mysql_fetch_array($result))
        $ttAttemptNo = $line[0];
    return $ttAttemptNo;
}

function checkForFirstTimeLogin($userID)
{
	$query  = "SELECT COUNT(*) as totalLogin FROM ".TBL_SESSION_STATUS." WHERE userID=$userID";
	$result = mysql_query($query);
	$line   = mysql_fetch_assoc($result);
	if($line['totalLogin'] > 1)
		return false;
	else
		return true;
}

function updateNCERTExcercise($ncertAttemptID,$complete=false)
{
	$sql = "SELECT SUM(R), COUNT(A) FROM adepts_ncertQuesAttempt WHERE ncertAttemptID='$ncertAttemptID' AND R!=-1 AND R!=3";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	$corrects = $row[0];
	$total = $row[1];
	$per = round(($corrects/$total)*100,2);

	if($complete)
		$status =  "result='SUCCESS', submitDate='".date('Y-m-d')."', ";
	else
		$status = "";
	$sql = "UPDATE adepts_ncertHomeworkStatus SET $status noOfQuesAttempted='$total', perCorrect='$per' WHERE ncertAttemptID='$ncertAttemptID'";
	mysql_query($sql) or die(mysql_error());
}

function getExerciseDetail($exerciseCode, $ncertAttemptID, $childClass, $childSection, $schoolCode,$userType)
{
	$sql = "SELECT chapterNo, chapterName, exerciseNo, GROUP_CONCAT(DISTINCT(d.groupNo) ORDER BY d.groupNo) as groups, deactivationDate FROM adepts_ncertExerciseMaster a, adepts_ncertQuestions b, adepts_ncertHomeworkActivation c, adepts_groupInstruction d WHERE b.groupID=d.groupID AND c.class='$childClass' ";
	
	if($childSection!="")
	$sql.=" AND c.section='$childSection' ";
	
	if($schoolCode!="")
	$sql.=" And c.schoolCode='$schoolCode' ";
	
	$sql.=" AND a.exerciseCode=b.exerciseCode "; 
	
	if($schoolCode!="")
	$sql.=" AND b.exerciseCode=c.exerciseCode ";
	
	$sql.=" AND a.exerciseCode='$exerciseCode' AND b.status=3";
	

	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	$chapterNo = $row['chapterNo'];
	$chapterName = $row['chapterName'];
	$exerciseNo = $row['exerciseNo'];
	$groups = $row['groups'];
	if($schoolCode!="")
	$deactivationDate = $row['deactivationDate'];
	else
	$deactivationDate = "";
	$exerciseDetail[0] = "$chapterName - Exercise $chapterNo.$exerciseNo";
	$exerciseDetail[1] = $groups;
	$exerciseDetail[2] = $deactivationDate;

	$completedGroups = array();
	$sql = "SELECT SUM(IF(R=-1,1,0)), groupNo FROM adepts_ncertQuesAttempt a, adepts_ncertQuestions b, adepts_groupInstruction c WHERE a.qcode=b.qcode AND b.groupID=c.groupID AND c.groupNo IN ($groups) AND ncertAttemptID=$ncertAttemptID GROUP BY c.groupID";
	$result = mysql_query($sql);
	while($row = mysql_fetch_array($result))
	{
		if($row[0] == 0)
			array_push($completedGroups,$row[1]);
	}
	$_SESSION["completedGroups"] = $completedGroups;
	$exerciseDetail[3] = implode(",",$completedGroups);
	return($exerciseDetail);
}
function setNCERTHomework($exerciseCode)
{
	$statusArray = array();
	$sql = "SELECT a.ncertAttemptID, ncertAttemptNo, qcode, questionNo FROM adepts_ncertHomeworkStatus a LEFT OUTER JOIN adepts_ncertQuesAttempt b ON a.ncertAttemptID=b.ncertAttemptID WHERE a.userID='".$_SESSION['userID']."' AND a.exerciseCode='$exerciseCode' AND (result!='SUCCESS' OR isNULL(result)) AND R=-1 ORDER BY a.ncertAttemptID,srno";
	$result = mysql_query($sql);
	if(mysql_num_rows($result) != 0)
	{
		$row = mysql_fetch_array($result);
		$statusArray['ncertAttemptID'] = $row[0];
		$statusArray['ncertAttemptNo'] = $row[1];
		$statusArray['qcode'] = $row[2];
		$statusArray['questionNo'] = $row[3];
		$statusArray['addQuesAttempts'] = false;
	}
	else
	{
		$statusArray['ncertAttemptNo'] = 1;
		$sql = "SELECT MAX(ncertAttemptNo) FROM adepts_ncertHomeworkStatus WHERE userID='".$_SESSION['userID']."' AND exerciseCode='$exerciseCode' AND result='SUCCESS'";
		$result = mysql_query($sql);
		if(mysql_num_rows($result) != 0)
		{
			$row = mysql_fetch_array($result);
			$statusArray['ncertAttemptNo'] = $row[0] + 1;
		}
		$sql = "INSERT INTO adepts_ncertHomeworkStatus (userID, exerciseCode, ncertAttemptNo) VALUES ('".$_SESSION['userID']."', '$exerciseCode', '".$statusArray['ncertAttemptNo']."')";
		$result = mysql_query($sql);
		$statusArray['ncertAttemptID'] = mysql_insert_id();
		$statusArray['qcode'] = "";
		$statusArray['questionNo'] = "";
		$statusArray['addQuesAttempts'] = true;
	}
	if($statusArray['questionNo'] == "")
		$statusArray['questionNo'] = 1;
	else
		$statusArray['questionNo']++;
	return($statusArray);
}
function setWorksheetDetails($worksheetID)
{
	$statusArray = array();
	$sql = "SELECT a.srno, a.wsm_id, a.spend_time, a.last_attempted_que, a.status
			FROM worksheet_attempt_status a 
			WHERE a.userID='".$_SESSION['userID']."' AND a.wsm_id='$worksheetID' AND (a.status!='completed' OR isNULL(a.status)) ORDER BY a.srno";
	$result = mysql_query($sql) or die($sql);
	if(mysql_num_rows($result) != 0)
	{
		$row = mysql_fetch_array($result);
		$statusArray['worksheetAttemptID'] = $row[0];
		$statusArray['worksheetID'] = $row[1];
		$statusArray['spend_time'] = $row[2];
		$statusArray['questionNo'] = $row[3];
		$statusArray['status'] = $row[4];
		$statusArray['completedWorksheet']= 0;
	}
	else
	{
		$sql = "SELECT a.srno, a.wsm_id, a.spend_time, a.last_attempted_que, a.status
			FROM worksheet_attempt_status a 
			WHERE a.userID='".$_SESSION['userID']."' AND a.wsm_id='$worksheetID' ";
		$result = mysql_query($sql) or die($sql);
		if(mysql_num_rows($result) != 0)
		{	
			$row = mysql_fetch_array($result);
			$statusArray['worksheetAttemptID'] = $row[0];
			$statusArray['worksheetID'] = $row[1];
			$statusArray['spend_time'] = $row[2];
			$statusArray['questionNo'] = $row[3];
			$statusArray['status'] = $row[4];
			$statusArray['completedWorksheet']= 1;		
		}
		else
		{
			$sql="SELECT duration from worksheet_master WHERE wsm_id='$worksheetID'";
			$rsql = mysql_query($sql) ;
			$line = mysql_fetch_array($rsql);

			$sql = "INSERT INTO worksheet_attempt_status (userID, status, ".(($line[0]!=0)?"spend_time,":"")." last_attempted_que,wsm_id) VALUES ('".$_SESSION['userID']."', 'pending',".(($line[0]!=0)?(($line[0]*60).','):'')."1,'$worksheetID')";
			$result = mysql_query($sql);
			$statusArray['worksheetAttemptID'] = mysql_insert_id();
			$statusArray['worksheetID'] = $worksheetID;
			$statusArray['questionNo'] = "";
			$statusArray['spend_time'] = ($line[0]!=0)?$line[0]*60:"";
			$statusArray['status'] = 'pending';
			$statusArray['completedWorksheet']= 0;

			$sql = "SELECT wsd_id FROM worksheet_detail WHERE wsm_id='$worksheetID' ORDER BY qno";
			$rsql = mysql_query($sql);
			while ($lsql=mysql_fetch_array($rsql)) {
				$sql = "INSERT INTO worksheet_attempt_detail(userID, attemptDate, wsd_id, answer, RW, ws_srno, wsm_id, sessionID) VALUES ('".$_SESSION['userID']."', '".date("Y-m-d")."', '".$lsql[0]."', '', '-1', '".$statusArray['worksheetAttemptID']."', '$worksheetID', '".$_SESSION['sessionID']."')";
				mysql_query($sql) or die(mysql_error());
			}
		}
		
	}
	if($statusArray['questionNo'] == "")
		$statusArray['questionNo'] = 1;
	return($statusArray);
}
function getWorksheetDetail($worksheetID, $worksheetAttemptID, $childClass, $childSection, $schoolCode)
{
	$sql = "SELECT a.wsm_id, wsm_name, GROUP_CONCAT(CONCAT(wsd_id,'~',qcode,'~',source,'~',ifnull(qno,''))) qinfo, COUNT(wsd_id) qcount, thumbnail, duration, end_datetime, IF(end_datetime>NOW(),1,0) wsActive FROM worksheet_master a, worksheet_detail b WHERE a.wsm_id=b.wsm_id AND a.class='$childClass' ";
	if($childSection != "")
		$sql.=" AND FIND_IN_SET('$childSection',a.section_list) ";
	if($schoolCode!="")
		$sql.=" And a.schoolCode='$schoolCode' ";
	$sql.=" AND a.wsm_id='$worksheetID' and assign_flag=1 ";
	$sql.=" GROUP BY a.wsm_id";
	
	$result = mysql_query($sql) or die(mysql_error().$sql);
	$row = mysql_fetch_array($result);
	$worksheetDetail = array();
	$worksheetDetail['id'] = $row['wsm_id'];
	$worksheetDetail['name'] = stripcslashes($row['wsm_name']);
	$worksheetDetail['qList'] = explode(",", $row['qinfo']);
	$worksheetDetail['qcount'] = $row['qcount'];
	$worksheetDetail['thumbnail'] = $row['thumbnail'];
	$worksheetDetail['duration'] = $row['duration'];
	$worksheetDetail['end_datetime'] = $row['end_datetime'];
	$worksheetDetail['wsActive'] = $row['wsActive'];

	$completedQs = array();
	$allQs = array();
	$sql = "SELECT wsd_id, RW FROM worksheet_attempt_detail a WHERE a.ws_srno=$worksheetAttemptID";
	$result = mysql_query($sql) or die(mysql_error().$sql);
	while ($row = mysql_fetch_array($result))
	{
		array_push($allQs, $row[0]);
		if ($row[1]!=-1)
			array_push($completedQs, $row[0]);
	}
	$_SESSION["completedQs"] = $completedQs;
	$_SESSION["allQs"] = $allQs;
	$worksheetDetail['completedQs'] = implode(",", $completedQs);
	$worksheetDetail['allQs'] = implode(",", $allQs);
	return($worksheetDetail);
}
function getWorksheetTimeRemaining(){
	$userID = $_SESSION['userID'];
	$worksheetAttemptID = $_SESSION['worksheetAttemptID'];
	$WSquery = "SELECT spend_time from worksheet_attempt_status WHERE srno = '$worksheetAttemptID' ";
	$WSresult = mysql_query($WSquery);
	$WSline   = mysql_fetch_array($WSresult);
	if(count($WSline) > 0)
		$WSTimeRemaining = $WSline['spend_time'];
	return $WSTimeRemaining;
}
function getWorksheetAnsweredArray(){
	$userID = $_SESSION['userID'];
	$worksheetAttemptID = $_SESSION['worksheetAttemptID'];
	$WSquery = "SELECT wsd_id,RW from worksheet_attempt_detail WHERE ws_srno = '$worksheetAttemptID' ORDER by id";
	$WSresult = mysql_query($WSquery);
	$wsArray = array();
	while($WSline   = mysql_fetch_array($WSresult)){
		array_push($wsArray,$WSline[1]==-1?0:1);
	}
	return $wsArray;
}
function insertQuesAttempts($userID, $ncertAttemptID, $exerciseCode, $sessionID)
{
	foreach($_SESSION["allQuestionsArray"] as $groupNo=>$qcodeArr)
	{
		foreach($qcodeArr as $qcode)
		{
			$sql = "INSERT INTO adepts_ncertQuesAttempt(userID, attemptedDate, qcode, A, R, ncertAttemptID, exerciseCode, sessionID) VALUES ($userID, '".date("Y-m-d")."', $qcode, '', '-1', $ncertAttemptID, '$exerciseCode', $sessionID)";
			mysql_query($sql);
		}
	}
}

function setTeacherScreen($userID,$interfaceFlag)
{
	$sq	=	"UPDATE adepts_teacherInterfaceScreen SET interfaceFlag=$interfaceFlag WHERE userID=$userID";
	$rs	=	mysql_query($sq);
}
function getClusterDetails($clusterCode,$flow,$mode=0)
{
	if($mode==1)
	{
		$clusterName = "";
		$sql = "SELECT cluster FROM adepts_clusterMaster WHERE clusterCode='$clusterCode'";
		$result = mysql_query($sql);
		$line = mysql_fetch_array($result);
		return $line[0];
	}	
	else
	{
		$clusterDetails = array();
		$sql = "SELECT cluster, remedialCluster, ".$flow."_level FROM adepts_clusterMaster WHERE clusterCode='$clusterCode'";
		$result = mysql_query($sql);
		if($row = mysql_fetch_array($result))
		{
			$clusterDetails["cluster"] = $row[0];
			$clusterDetails["remedialCluster"] = $row[1];
			$clusterDetails["level"] = $row[2];
		}
		return($clusterDetails);
	}
}

function wildCardQuesStart($schoolCode,$cls,$userID)
{
	$sqData	=	"SELECT code,type,displayText,stopingCriterion,stopingCondition,deactivationDate FROM adepts_researchQuesActivation 
				 WHERE appearance='Start' 
				 AND (schoolCode='All' OR find_in_set('$schoolCode',schoolCode)>0) 
				 AND (class='All' OR find_in_set('$cls',class)>0) ORDER BY RAND()";
	$rsData	=	mysql_query($sqData);
	while($rwData=mysql_fetch_array($rsData))
	{
		if($rwData[1]=="timedtest" && $rwData["deactivationDate"]>date("Y-m-d"))
		{
			$sqSearch	=	"SELECT timedTestCode FROM adepts_timedTestQuesAttempt WHERE userID=$userID AND timedTestCode='".$rwData[0]."' AND mode='wildcard'";
			$rsSearch	=	mysql_query($sqSearch);
			if(mysql_num_rows($rsSearch)==0)
			{
				$arrayQues["qcode"]	=	$rwData[0];
				$arrayQues["type"]	=	$rwData[1];
				$arrayQues["displayText"] =	$rwData[2];
				return $arrayQues;
				break;
			}
		}
		else if($rwData[1]!="timedtest")
		{
			$sqAttempts = "SELECT COUNT(qcode) as cnt FROM adepts_researchQuesAttempt WHERE qcode=".$rwData[0];
			$rsAttempts = mysql_query($sqAttempts);
			$rsArr = mysql_fetch_array($rsAttempts);
			$rwCount = $rsArr['cnt'];
			if($rwData["stopingCondition"]=="or")
			{
				if($rwCount>=$rwData["stopingCriterion"] || $rwData["deactivationDate"]<=date("Y-m-d"))
					continue;
			}
			else if($rwData["stopingCondition"]=="and")
			{
				if($rwCount>=$rwData["stopingCriterion"] && $rwData["deactivationDate"]<=date("Y-m-d"))
					continue;
			}
			
			$sqSearch	=	"SELECT qcode FROM adepts_researchQuesAttempt WHERE userID=$userID AND qcode=".$rwData[0]." AND questionType='".$rwData[1]."'";
			$rsSearch	=	mysql_query($sqSearch);
			if(mysql_num_rows($rsSearch)==0)
			{
				$arrayQues["qcode"]	=	$rwData[0];
				$arrayQues["type"]	=	$rwData[1];
				$arrayQues["displayText"] =	$rwData[2];
				return $arrayQues;
				break;
			}
		}
	}
	$_SESSION['lowerLevel'] = 0;	//Initialize it to 0, will get updated later.
}

function checkForWildcard($userID,$sessionID)
{
	$sqSession = "SELECT COUNT(sessionID) FROM adepts_sessionStatus WHERE userID=$userID AND startTime_int=".date("Ymd");
	$rsSession = mysql_query($sqSession);
	$rwSession = mysql_fetch_array($rsSession);
	
	if($rwSession[0]>1)
	{
		return false;
	}
	else
	{
		$currentDate	=	date("Y-m-d");
		$sqQues	=	"SELECT qcode FROM adepts_researchQuesAttempt WHERE userID=$userID AND attemptedDate='$currentDate'";
		$rsQues	=	mysql_query($sqQues);
		if($rwQues=mysql_fetch_array($rsQues))
		{
			return false;
		}
		else
		{
			$sqTimedTest	=	"SELECT timedTestCode FROM adepts_timedTestQuesAttempt WHERE userID=$userID AND mode='wildcard' AND lastModified >= '$currentDate'";
			$rsTimedTest	=	mysql_query($sqTimedTest);
			if($rwTimedTest=mysql_fetch_array($rsTimedTest))
			{
				return false;
			}
			else
			{
				return true;
			}
		}
	}
}

function wildCardQuesRand($schoolCode,$cls,$userID,$ttCode)
{
	$arrayQues	=	array();
	$sqData	=	"SELECT code,type,displayText,stopingCriterion,stopingCondition,deactivationDate FROM adepts_researchQuesActivation WHERE appearance='Randomly' AND (schoolCode='All' OR find_in_set('$schoolCode',schoolCode)>0) AND (class='All' OR find_in_set('$cls',class)>0) ORDER BY RAND()";
	$rsData	=	mysql_query($sqData);
	while($rwData=mysql_fetch_array($rsData))
	{
		if($rwData[1]=="timedtest" && $rwData["deactivationDate"]>date("Y-m-d"))
		{
			$sqSearch	=	"SELECT timedTestCode FROM adepts_timedTestQuesAttempt WHERE userID=$userID AND timedTestCode='".$rwData[0]."' AND mode='wildcard'";
			$rsSearch	=	mysql_query($sqSearch);
			if(mysql_num_rows($rsSearch)==0)
			{
				$arrayQues["qcode"]	=	$rwData[0];
				$arrayQues["type"]	=	$rwData[1];
				$arrayQues["displayText"] =	$rwData[2];
				return $arrayQues;
				break;
			}
		}
		else if($rwData[1]!="timedtest")
		{
			$sqAttempts = "SELECT COUNT(qcode) as cnt FROM adepts_researchQuesAttempt WHERE qcode=".$rwData[0];
			$rsAttempts = mysql_query($sqAttempts);
			$rsArr = mysql_fetch_array($rsAttempts);
			$rwCount = $rsArr['cnt'];
			if($rwData["stopingCondition"]=="or")
			{
				if($rwCount>=$rwData["stopingCriterion"] || $rwData["deactivationDate"]<=date("Y-m-d"))
					continue;
			}
			else if($rwData["stopingCondition"]=="and")
			{
				if($rwCount>=$rwData["stopingCriterion"] && $rwData["deactivationDate"]<=date("Y-m-d"))
					continue;
			}

			$sqSearch	=	"SELECT qcode FROM adepts_researchQuesAttempt WHERE userID=$userID AND qcode=".$rwData[0]." AND questionType='".$rwData[1]."'";
			$rsSearch	=	mysql_query($sqSearch);
			if(mysql_num_rows($rsSearch)==0)
			{
				$arrayQues["qcode"]	=	$rwData[0];
				$arrayQues["type"]	=	$rwData[1];
				$arrayQues["displayText"] =	$rwData[2];
				return $arrayQues;
				break;
			}
		}
	}
	
	if(count($arrayQues)==0 && count($wildcardAtStart)==0)
	{
		if($cls>1)
			$classLevel	=	$cls-1;
		$sqQcodeWrong	=	"SELECT A.qcode FROM ".TBL_QUES_ATTEMPT_CLASS." A, adepts_clusterMaster B 
							 WHERE A.userID=$userID AND A.clusterCode=B.clusterCode AND teacherTopicCode<>'$ttCode' AND A.R=0 AND FIND_IN_SET($classLevel,ms_level) > 0 
							 AND FIND_IN_SET($cls,ms_level) = 0 AND clusterType NOT IN ('pre','post','practice') ORDER BY RAND() LIMIT 1";
		$rsQcodeWrong	=	mysql_query($sqQcodeWrong);
		if($rwQcodeWrong=mysql_fetch_array($rsQcodeWrong))
		{
			$arrayQues["qcode"]	=	$rwQcodeWrong[0];
			$arrayQues["type"]	=	"normal";
		}
		else
		{
			$sqQcodeWrong	=	"SELECT A.qcode FROM ".TBL_QUES_ATTEMPT_CLASS." A, adepts_clusterMaster B 
								 WHERE A.userID=$userID AND A.clusterCode=B.clusterCode AND teacherTopicCode<>'$ttCode' AND A.R=0 AND 
								 FIND_IN_SET($cls,ms_level) > 0 AND clusterType NOT IN ('pre','post','practice') ORDER BY RAND() LIMIT 1";
			$rsQcodeWrong	=	mysql_query($sqQcodeWrong);
			if($rwQcodeWrong=mysql_fetch_array($rsQcodeWrong))
			{
				$arrayQues["qcode"]	=	$rwQcodeWrong[0];
				$arrayQues["type"]	=	"normal";
			}
			else
			{
				$sqQcodeCorrect	=	"SELECT qcode FROM ".TBL_QUES_ATTEMPT_CLASS." A, adepts_clusterMaster B WHERE userID=$userID AND A.clusterCode=B.clusterCode 
									 AND clusterType NOT IN ('pre','post','practice') ORDER BY RAND() LIMIT 1";
				$rsQcodeCorrect	=	mysql_query($sqQcodeCorrect);
				if($rwQcodeCorrect=mysql_fetch_array($rsQcodeCorrect))
				{
					$arrayQues["qcode"]	=	$rwQcodeCorrect[0];
					$arrayQues["type"]	=	"normal";
				}
			}
		}
		if(count($arrayQues)>0)
		{
			$sqSearch	=	"SELECT qcode FROM adepts_researchQuesAttempt WHERE userID=$userID AND qcode=".$arrayQues["qcode"]." AND questionType='normal'";
			$rsSearch	=	mysql_query($sqSearch);
			if(mysql_num_rows($rsSearch)>0)
				$arrayQues = array();
		}
		return $arrayQues;
	}
}

function saveWildcardQues($userID, $questionNo, $qcode, $response,$seconds,$responseResult, $clusterAttemptID, $clusterCode, $sessionID,$startTime, $endTime, $pageLoadTime, $quesType, $ttAttemptID, $teacherTopicCode, $dynamic,$dynamicParams, $showAnswer, $subjectno, $noOfTrialsTaken, $eeresponse, $hintAvailable, $hintUsed)
{
	$sq	=	"INSERT INTO adepts_researchQuesAttempt (userID,attemptedDate,questionNo,questionType,qcode,A,R,sessionID)
	 		 VALUES ($userID,'".date("Y-m-d")."','$questionNo','$questionType',$qcode,'$response',$responseResult,$sessionID)";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
}
function getLastTTAttemptAccuracy($userID, $ttCode)
{
	$query = "SELECT progress, noOfQuesAttempted, perCorrect FROM adepts_teacherTopicStatus WHERE userID=$userID AND teacherTopicCode='$ttCode' ORDER BY ttAttemptID DESC LIMIT 1";
	$result = mysql_query($query);
	if (mysql_num_rows($result)>0){
		$line = mysql_fetch_array($result);
		return $line['perCorrect'];
	}
	else return 0;
}
function getLastTTAttempt($userID, $ttCode)
{
	$query = "SELECT ttAttemptID FROM adepts_teacherTopicStatus WHERE userID=$userID AND teacherTopicCode='$ttCode' ORDER BY ttAttemptID DESC LIMIT 1";
	$result = mysql_query($query);
	if (mysql_num_rows($result)>0){
		$line = mysql_fetch_array($result);
		return $line['ttAttemptID'];
	}
	else return "";
}
function getTopicProgressDetails($userID, $ttCode)
{
	$quesAttempted = $quesCorrect = $progress = 0;
	$query = "SELECT progress, noOfQuesAttempted, perCorrect FROM adepts_teacherTopicStatus WHERE userID=$userID AND teacherTopicCode='$ttCode'";
	$result = mysql_query($query);
	while($line = mysql_fetch_array($result))
	{
		$quesAttempted += $line['noOfQuesAttempted'];
		$quesCorrect += round($line['noOfQuesAttempted']*$line['perCorrect']/100);
		if($progress<$line['progress'])
			$progress = $line['progress'];
	}
	$_SESSION['progressInTopic'] = round($progress);
	$_SESSION['quesAttemptedInTopic'] = $quesAttempted;
	$_SESSION['quesCorrectInTopic'] = $quesCorrect;
	$_SESSION['lowerLevel'] = 0;	//Initialize it to 0, will get updated later.
}

/*function getMsgSignature($qcode,$quesType,$correctAnswer,$dropdownAns,$clusterCode,$hintAvailable,$noOfTrials,$quesCategory)
{
	$str = $qcode."-".$quesType."-".$clusterCode."-".$hintAvailable."-".$noOfTrials."-".$quesCategory;//$dropdownAns."-".$correctAnswer
	return hash_hmac('md5', $str, 'mindspark_2013');
}

function sendDataTamperingMail($userID, $sessionID, $signature, $newSignature)
{
	$query = "insert into adepts_signature(userID, sessionID, oldSignature, newSignature) VALUES ($userID, $sessionID, '".mysql_escape_string($signature)."','".mysql_escape_string($newSignature)."')";
	mysql_query($query);
}*/

function checkTimedTestBug($sessionID,$timedTestCode)
{
	$sq	=	"SELECT lastModified FROM adepts_timedTestDetails WHERE sessionID=$sessionID AND timedTestCode='$timedTestCode' ORDER BY timedTestID DESC LIMIT 1";
	$rs	=	mysql_query($sq);
	if($rw=mysql_fetch_array($rs))
	{
		if((strtotime(date("Y-m-d H:i:s")) - strtotime($rw[0])) < 120)
			return true;
		else
			return false;
	}
}

function getTopicQwner($teacherTopicCode,$userName)
{
	$userNameArr	=	explode("_",$userName);
	$sq	=	"SELECT owner1,owner2 FROM adepts_topicMaster A, adepts_teacherTopicMaster B WHERE teacherTopicCode='$teacherTopicCode' AND find_in_set(topicCode,mappedtoTopic)>0";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	if($rw[0]!="" && $userNameArr!=$rw[0])
		return $rw[0];
	else
		return $rw[1];
}


function avgTime($qcode,$childClass)
{
	$query="select averageTimeTakenToAns from adepts_questionPerformance where qcode=$qcode and class=$childClass order by majorVersion desc limit 1";
	$result = mysql_query($query);
	if($row = mysql_fetch_row($result))
	{
	 if($row[0] == "")
		return 60;
	 else
		return $row[0];
	}
}

function getTopicTimedTest($timedTestCode,$userID)
{
	$sq	=	"SELECT teacherTopicCode FROM adepts_timedTestMaster A, adepts_teacherTopicClusterStatus B, adepts_teacherTopicStatus C
			 WHERE B.userID=$userID AND timedTestCode='$timedTestCode' AND A.linkedToCluster=B.clusterCode AND B.ttAttemptID=C.ttAttemptID 
			 ORDER BY clusterAttemptID DESC LIMIT 1";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	return $rw[0];
}

function videoViewedPreviously($clusterCode,$currentSDL)
{
	$query = "SELECT videoID,videoFile  FROM adepts_msVideos WHERE mappingType='cluster' and mappingID='$clusterCode' AND linkedToSDL=$currentSDL";
	$result = mysql_query($query) or die(mysql_error());
	$line = mysql_fetch_array($result);
	$videoID = $line['videoID'];
	$userID  = $_SESSION['userID'];
	
	$query="select count(*),noOfViews from adepts_userVideoLog where userID=$userID and videoID=$videoID";
	$result = mysql_query($query) or die(mysql_error());
	$line = mysql_fetch_array($result);
	$count = $line[0];
	$videoViews = $line[1];

	 if($count==0)
	 {
	 	return 0;
	 }
	 else
	 {
		if($videoViews>1)
		return 1;
		else 
		return 0; 
	 }

}

function rewardSystemFeature($schoolCode,$childClass)
{
	$flag	=	0;
	if($childClass>3) { 
		$sq	=	"SELECT flag FROM adepts_rewardSystemPilot WHERE schoolCode=$schoolCode";
		$rs	=	mysql_query($sq);
		if($rw=mysql_fetch_assoc($rs))
			$flag	=	$rw["flag"];
	}
	return $flag;
}
function findCLustersAttendedInTopic($attemptID)
{
	$arr = array();
	$sql = "Select clusterCode,clusterAttemptNo,result,attemptType from adepts_teacherTopicClusterStatus where ttAttemptID=$attemptID";
	$result = mysql_query($sql);
	while($line = mysql_fetch_array($result))
	{
		($line[0]=="")?array_push($arr,""):array_push($arr,$line[0]);
		($line[1]=="")?array_push($arr,""):array_push($arr,$line[1]);
		($line[2]=="")?array_push($arr,""):array_push($arr,$line[2]);
	}
	return $arr;
}
 	function hasAttemptedGame($userID,$gameID){
        $cleared=0;
        $query = "SELECT count(*) FROM adepts_userGameDetails WHERE userID=$userID AND gameID=$gameID AND completed<>0";
	    $result = mysql_query($query);
	    if($line = mysql_fetch_array($result))
	    {
		    $noOfTimesCleared = $line[0];
		    if($noOfTimesCleared>=1)
			    $cleared = 1;
	    }
	    return $cleared;
    }
    function hasClearedTimedTest($timedTestCode,$clusterCode,$userID){
        $cleared=0;
        $query = "SELECT a.perCorrect as percent from adepts_timedTestDetails a, adepts_timedTestMaster b where a.timedTestCode=b.timedTestCode and a.timedTestCode='".$timedTestCode."' and b.linkedToCluster='".$clusterCode."' and a.userid=".$userID." order by timedTestID desc limit 1";
        $result = mysql_query($query);
	    while($line = mysql_fetch_array($result))
	    {
    	    $timedTestPer = $line[0];
		    if($timedTestPer>=75)
			    $cleared = 1;
            elseif($timedTestPer<75)
                $cleared = 2;
	    }
        return $cleared;
    }
	
	function fillTopicProgressBar($teacherTopicCode,$childClass,$flow,$userID)
	{
			$objTT       = new teacherTopic($teacherTopicCode,$childClass,$flow);
			$noClustersOfClass = 0;
			$tmparrayy = array();
			$tmparrayyName =  array();
			
			$classSpecificClustersForTT = array();
			$classSpecificClustersForTT = $objTT->clusterFlowArray;
            $meantForClasses=$objTT->meantForClasses;
			$totalSDLsInTopic = getTotalSDLsInTopic($classSpecificClustersForTT,$childClass);
           
            foreach($classSpecificClustersForTT as $key=>$value)
			{
				$classes = explode(',',$classSpecificClustersForTT[$key][1]);
				foreach($classes as $keys=>$values)
				{
					if($classes[$keys] == $childClass) {
						$totalSDLsInCluster = getTotalSDLsInCluster($classSpecificClustersForTT[$key][0]);
						$clusterSize = $totalSDLsInCluster/$totalSDLsInTopic;
						/*array_push($classSpecificClustersForTT[$key],$totalSDLsInCluster." - ".$totalSDLsInTopic);*/
						array_push($classSpecificClustersForTT[$key],$clusterSize);

					    array_push($tmparrayy,$classSpecificClustersForTT[$key]);
					    $noClustersOfClass = 1; 
					    $choiceScreenFlagProgress=0;
					    if (isChoiceScreenSchool($childClass) && $childClass>3) $choiceScreenFlagProgress=1;

                        $game = checkForGame($classSpecificClustersForTT[$key][0], "", "", "", $childClass,$choiceScreenFlagProgress);
                        $timedTest = checkForTimedTest("", "", $classSpecificClustersForTT[$key][0], "", $childClass,1);
                        if(($game!=-1&&$game!="") && gettype($game)!='array'){
                            $gameArr= array();
                            array_push($gameArr,$game['gameID']);
                            array_push($gameArr,$game['gameName']);
                            array_push($gameArr,'activity');
                            if(hasAttemptedGame($userID,$game['gameID'])){
                               array_push($gameArr,'attempted');
                            }
                            else{
                                array_push($gameArr,'NA');
                            }
                            array_push($tmparrayy,$gameArr);
                        }
                        foreach($timedTest as $code=>$description)
						{
                            $timedTestArr= array();
                            array_push($timedTestArr,$code);
                            array_push($timedTestArr,$timedTest[$code]);
                            array_push($timedTestArr,'timedTest');
                            if(hasClearedTimedTest($code,$classSpecificClustersForTT[$key][0],$userID)==1){
                                array_push($timedTestArr,'passed');
                            }
                            elseif(hasClearedTimedTest($code,$classSpecificClustersForTT[$key][0],$userID)==2){
                                array_push($timedTestArr,'failed');
                            }
                            elseif(hasClearedTimedTest($code,$classSpecificClustersForTT[$key][0],$userID)==0){
                                array_push($timedTestArr,'NA');
                            }
                            array_push($tmparrayy,$timedTestArr);
                        }
                    }
                    //array_push($tmparrayyName[$classSpecificClustersForTT[$key][0]],getClusterDetails($classSpecificClustersForTT[$key][0],"",1));
                    $tmparrayyName[$classSpecificClustersForTT[$key][0]]=getClusterDetails($classSpecificClustersForTT[$key][0],"",1);
				}
							
			}
      		if($noClustersOfClass==0)
			{
                if($childClass > $meantForClasses[count($meantForClasses)-1]){
                    $allowClass=$meantForClasses[count($meantForClasses)-1];
                }
                elseif($childClass <= $meantForClasses[0]){
                    $allowClass=$meantForClasses[0];
                }
				elseif($childClass<=$meantForClasses[count($meantForClasses)-1] && $childClass>=$meantForClasses[0])
				{
					$allowClass=$meantForClasses[0];
				}
				
				$totalSDLsInEmptyTopic = getTotalSDLsInTopic($classSpecificClustersForTT,$allowClass);

                foreach($classSpecificClustersForTT as $key=>$value)
				{
                    $classes = explode(',',$classSpecificClustersForTT[$key][1]);
					foreach($classes as $keys=>$values)
					{
						if($classes[$keys] == $allowClass) {
							$totalSDLsInCluster = getTotalSDLsInCluster($classSpecificClustersForTT[$key][0]);
							$clusterSize = $totalSDLsInCluster/$totalSDLsInEmptyTopic;
							array_push($classSpecificClustersForTT[$key],$clusterSize);

							array_push($tmparrayy,$classSpecificClustersForTT[$key]);

							$choiceScreenFlagProgress=0;
							if (isChoiceScreenSchool($childClass) && $childClass>3) $choiceScreenFlagProgress=1;

                            $game = checkForGame($classSpecificClustersForTT[$key][0], "", "", "", $childClass,$choiceScreenFlagProgress);
                            $timedTest = checkForTimedTest("", "", $classSpecificClustersForTT[$key][0], "", $childClass,1);
                            if(($game!=-1&&$game!="") && gettype($game)!='array'){
                                $gameArr= array();
                                array_push($gameArr,$game['gameID']);
                                array_push($gameArr,$game['gameName']);
                                array_push($gameArr,'activity');
                                if(hasAttemptedGame($userID,$game['gameID'])){
                                    array_push($gameArr,'attempted');
                                }
                                else{
                                    array_push($gameArr,'NA');
                                }
                                array_push($tmparrayy,$gameArr);
                            }
                            foreach($timedTest as $code=>$description){
                                $timedTestArr= array();
                                array_push($timedTestArr,$code);
                                array_push($timedTestArr,$timedTest[$code]);
                                array_push($timedTestArr,'timedTest');
                                if(hasClearedTimedTest($code,$classSpecificClustersForTT[$key][0],$userID)==1){
                                    array_push($timedTestArr,'passed');
                                }
                                elseif(hasClearedTimedTest($code,$classSpecificClustersForTT[$key][0],$userID)==2){
                                    array_push($timedTestArr,'failed');
                                }
                                elseif(hasClearedTimedTest($code,$classSpecificClustersForTT[$key][0],$userID)==0){
                                    array_push($timedTestArr,'NA');
                                }
                                array_push($tmparrayy,$timedTestArr);
                            }
						}
                        $tmparrayyName[$classSpecificClustersForTT[$key][0]]=getClusterDetails($classSpecificClustersForTT[$key][0],"",1);
					}
				}
			}
			$_SESSION['classSpecificClustersForTT'] = $tmparrayy;
			$_SESSION['classSpecificClustersNameForTT'] = $tmparrayyName;
	}
	
function getTotalSDLsInTopic($classSpecificClustersForTT,$childClass)
{
	$context = isset($_SESSION['country'])?$_SESSION['country']:"India";
	$flashContent = $_SESSION['flashContent'];

		foreach($classSpecificClustersForTT as $keys => $values )
		{
				if (strpos($classSpecificClustersForTT[$keys][1], $childClass) !== false)
				{
					$clusterCodes = $classSpecificClustersForTT[$keys][0];
					$query1 = "SELECT  distinct   subdifficultylevel
		                      FROM       adepts_questions
		                      WHERE      clusterCode='$clusterCodes' AND status='3' AND context in ('Global','$context')";
		            if(!$flashContent)
		                $query .= " AND (question NOT LIKE '%[%.swf%]%')";
		            $query.= " ORDER BY subdifficultylevel";
					$result1 = mysql_query($query1) or die(mysql_error());
					$totalRows += mysql_num_rows($result1);
				}
		}
	
	
	return $totalRows;
}

function getTotalSDLsInCluster($clusterCode)
{
	$context = isset($_SESSION['country'])?$_SESSION['country']:"India";
	$flashContent = $_SESSION['flashContent'];
	$totalRows = 0;
	$query = "SELECT  distinct   subdifficultylevel
                      FROM       adepts_questions
                      WHERE      clusterCode='$clusterCode' AND status='3' AND context in ('Global','$context')";
            if(!$flashContent)
                $query .= " AND (question NOT LIKE '%[%.swf%]%')";
            $query.= " ORDER BY subdifficultylevel";
	$result = mysql_query($query) or die(mysql_error());
	$rowsInCluster = mysql_num_rows($result);
	return $rowsInCluster;
	
}

function randomizeOptions($objQuesition)
{
	$tempArray = array();
	$question_type = $objQuesition->quesType;
	$tempArray[] = array("A",$objQuesition->getOptionA());
	$tempArray[] = array("B",$objQuesition->getOptionB());
	if($question_type=='MCQ-3' || $question_type=='MCQ-4')
	{
		$tempArray[] = array("C",$objQuesition->getOptionC());
	}
	if($question_type=='MCQ-4')
	{
		$tempArray[] = array("D",$objQuesition->getOptionD());
	}
	shuffle($tempArray);
	return($tempArray);
}


function isClusterVoiceOverPilot($clusterCode)
{
	$pilotTopics = array('TT002','TT005','TT010','TT015','TT073','TT097','TT104','TT105','TT106');
	
	$sql = "select teacherTopicCode from adepts_teacherTopicClusterMaster where clusterCode='".$clusterCode."'";
	$result = mysql_query($sql) or die(mysql_error());
	while($row = mysql_fetch_array($result))
	{
		if(in_array($row[0], $pilotTopics))
			return 1;
	}
	
	return 0;
}

function voiceFileNotExists($quesVoiceOver)
{
	$retcode = 0;
	$ch = curl_init($quesVoiceOver);
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_exec($ch);
	$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	// $retcode >= 400 -> not found, $retcode = 200, found.
	curl_close($ch);

	if($retcode==200)
	return 0;
	else
	return 1;
}

function saveWildcardUserResponsesText($saveCQUserResponse = NULL,$qcode = NULL){
	$userID = $_SESSION['userID'];
	$sessionID	= $_SESSION["sessionID"];
	if($saveCQUserResponse == 1){
		$wildCardText = trim($_POST['cqUserResponse']);
		$commentCategory = 'challenge';
	}
	else{
		$wildCardText = trim($_POST['wildCardText']);
		$commentCategory = 'wildcard';
	}
	$query="INSERT INTO adepts_wildCardComments(userID,sessionID,qcode,comments,commentCategory) VALUES('$userID','$sessionID','$qcode','$wildCardText','$commentCategory')";
	mysql_query($query) or die($query);
	
}

//added by Jayanth for new teacherInterface
function getTeacherInterfacePreference($userID) {
    $query = "SELECT interfacePreference, isFirstLogin FROM teacherInterfacePreferences WHERE userID = $userID";
    $result = mysql_query($query) or die(mysql_error());
    if(mysql_num_rows($result) > 0) {
        $line   = mysql_fetch_assoc($result);
        $interfacePreference = $line['interfacePreference'];        
    } else {
        $interfacePreference = "NEW";
    }
    return $interfacePreference;    
}

function getTTAttemptNO($userID,$teacherTopicCode)
{
	$sq = "SELECT COUNT(ttAttemptID) FROM ".TBL_TOPIC_STATUS." WHERE userID=$userID AND teacherTopicCode='$teacherTopicCode'";
	$rs = mysql_query($sq);
	$rw = mysql_fetch_array($rs);
	return $rw[0] + 1;
}
/*function saveDisplayAnswerRating($qid,$userID,$sessionID,$displayAnsRating,$daComment)
{
	$daComment = trim($daComment);
	$systemCategory = "";
	if($daComment!="")
	{
		include_once("classes/clsbucketCommentsV1.php");
		$obj = new commentCategorization();
		$systemCategory	=	$obj->mark($daComment);
	}
	$displayAnsRating = $displayAnsRating + 14;
	$sq = "INSERT INTO adepts_emotToolbarTagging SET type='da',userID=$userID, fieldID=$qid, sessionID=$sessionID, response='$displayAnsRating', comments='$daComment', commentCategory='$systemCategory'";
	$rs = mysql_query($sq);
}*/



?>
