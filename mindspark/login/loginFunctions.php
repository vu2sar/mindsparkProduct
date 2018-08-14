<?php

include("../userInterface/constants.php");
if(SERVER_TYPE=='LIVE')
{
	if (!class_exists('db')) {
		require_once('../parentInterface/classes/class.db.php');
	}
	
	include_once("../userInterface/functions/sbaTestFunctions.php");
}
if (!class_exists('session'))
    include("../userInterface/classes/clsSession.php");
include("../userInterface/functions/next_cluster.php");
include_once("../userInterface/functions/next_question.php");
include("../userInterface/functions/functions.php");
require_once("../userInterface/classes/clsRewardSystem.php");
include_once("../userInterface/classes/clsTeacherTopic.php");
include_once("../userInterface/classes/clsProgressCalculation.php");
require_once "../parentInterface/common.php";

function validationProcess($objUser, $browserName, $browserVersion, $image1, $image2, $browser)
{
    if (strcasecmp($objUser->category,"STUDENT")==0 && strcasecmp($objUser->subcategory,"Individual")==0)
			{	
				$objUser->updateStartDate();
			}
//			$subjectArray = $objUser->getSubjectDetails();
			$_SESSION['buddy']		 = $objUser->buddyID;
			$_SESSION['admin']       = $objUser->category;
			$_SESSION['subcategory'] = $objUser->subcategory;
			$_SESSION['userID']      = $objUser->userID;
			$_SESSION['username']	 = $objUser->username;
			$_SESSION['childClass']  = $objUser->childClass;
			$_SESSION['childSection']  = $objUser->childSection;
			$_SESSION['timePerDay']  = $objUser->timeAllowedPerDay;
			$_SESSION['childName']   = $objUser->childName;
			$_SESSION['schoolCode']  = $objUser->schoolCode;
			$_SESSION['theme']  = $objUser->theme;
			$context= $objUser->country=="US"?"US":"India";			
			$_SESSION['context']  = $context;
						
			$_SESSION['isOffline'] = $objUser->isOffline; //for offline
			if($_SESSION['isOffline'])
			{				
				$_SESSION['offlineStatus'] = $objUser->offlineStatus;
				if($_SESSION['offlineStatus']==5)
				{
					echo '<script>window.location="index.php?login=4";</script>';
					exit();
				}
			}
            $query  = "SELECT userID FROM adepts_userClassUpgradeError WHERE userID=".$objUser->userID;
            $userid_result 	= mysql_query($query) or die(mysql_error());
		    if(mysql_num_rows($userid_result)>0){
		        //echo "<strong>Sorry for the inconvenience! You won't be able to login temporary due to class up-gradation in progress. Please try again later.<br>(If you are not able to login after 24 hours, please contact your teacher or customer support)</strong>";
                echo '<script>window.location="index.php?login=2";</script>';
		    }
            else{
                createSession();
                    $browserCategory = checkBrowser($browserName, $browserVersion);
                    if ($browserCategory != 'green') {
						
                        $path = "../userInterface/browser_detect.php?image1=$image1&image2=$image2";
                        echo '<html><body>';
                        echo '<form name="frmBrowser" id="frmBrowser" method="POST" action="' . $path . '">';
                        echo '<input type="hidden" name="sessionID" id="sessionID" value="' . $_SESSION["sessionID"] . '">';
						$windowName=$_SESSION['windowName'];
                        echo "</form><script>if(window.sessionStorage){sessionStorage.setItem('windowName','$windowName');console.log('sessionStorage supports')}else{window.name='$windowName';console.log('sessionStorage does not support');} document.frmBrowser.submit();</script></body></html>";
                        exit;
                    }
                    saveBrowserInfo($browser);
                    login();//Redirect internally from the function
            }
}

function parentLogin($parentUserID, $changePassword=true) {
    $querySession = "SELECT * FROM adepts_parentSessionStatus WHERE parentUserID=$parentUserID ";
    $result = mysql_query($querySession) or die(mysql_error());
    $sessionCount = mysql_num_rows($result);
    $query = "SELECT * FROM parentUserDetails WHERE parentUserID=$parentUserID ";
    $result = mysql_query($query) or die(mysql_error());
    if (mysql_num_rows($result) > 0) {
        $line = mysql_fetch_array($result);
        set_parent_json_array($line);
        $_SESSION['isOpenID'] = false;
        $_SESSION['name'] = $line['firstName'] . ' ' . $line['lastName'];
        $_SESSION['firstName'] = $line['firstName'];
        $_SESSION['lastName'] = $line['lastName'];
        $_SESSION['openIDEmail'] = $line['username'];
        $_SESSION['openIDMethod'] = 'MS';
        $_SESSION['openIDProvider'] = 'Mindspark';
        $sql = "SELECT pc.*,(IF(ISNULL(f.userID),0,(CASE status WHEN 'Active' THEN 1 ELSE 0 END))) AS freeTrial FROM parentChildMapping pc left join freeTrialDetail f on f.userID=pc.childUserID WHERE parentUserID=$parentUserID ";
        $result = mysql_query($sql) or die(mysql_error());
        $childrenMapped = array();
        while ($row = mysql_fetch_assoc($result)) {
            $childrenMapped[] = $row;
        }
        $ids = array();
        
        if (count($childrenMapped) > 0) {
            setParentSesssionValues($childrenMapped);
            $ids = array_map(function($item) {
                return $item['childUserID'];
            }, $childrenMapped);
            $nextPage = PARENT_CONNECT_PATH;
        } else {
            $students = implode(',', $ids);
            $startTime = date("Y-m-d H:i:s");
            $provider = 'Mindspark';
            $IPaddress = get_ip_address();
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
        }
        if($sessionCount==0 && $changePassword)
            $nextPage = '../parentInterface/changePasswordOuter.php?showMessage';
        else if(count($childrenMapped) == 0)
           $nextPage = PARENT_CONNECT_PATH;
        $students = implode(',', $ids);
        $startTime = date("Y-m-d H:i:s");
        $provider = 'Mindspark';
        $IPaddress = get_ip_address();
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $openID = $_SESSION['openIDEmail'];
        $sql = "INSERT into adepts_parentSessionStatus SET openID='$openID',parentUserID=$parentUserID, provider='$provider', IPaddress='$IPaddress', userAgent='$userAgent',students='$students', startTime='$startTime'";
        $result = mysql_query($sql) or die(mysql_error());
        $_SESSION['sessionID'] = mysql_insert_id();
        header("Location:$nextPage");
            exit;
    }    
}

function createSession() {
    $startTime = date("Y-m-d H:i:s");
    $objSession = new session($_SESSION['userID']);

    $sessionID = $objSession->createSession(TBL_SESSION_STATUS, $startTime);
    $_SESSION["sessionID"] = $sessionID;
    $_SESSION["sessionStartTime"] = $startTime;
    $_SESSION["wildcardSession"] = 1;
    $_SESSION["rewardSystem"] = 0;
    $_SESSION["videoSystem"] = 0;
    if ($_SESSION['schoolCode'] == 123456789)
        $_SESSION["videoSystem"] = 1;
    if ($_SESSION['childClass'] > 3)
        $_SESSION["rewardSystem"] = 1;
    $_SESSION['progressBarFlag'] = 1;

    $path = "browser_detect.php?image1=$image1&image2=$image2";
}

function saveBrowserInfo($browser, $version) {
    $sessionID = $_SESSION["sessionID"];
    $objSession = new session($_SESSION['userID'], $sessionID);
    $objSession->updateBrowserDetails($browser);
    $flashContent = 1;

    //Duplicate login check
    if (!isset($_REQUEST['forceSave'])) {
//            if (isset($_REQUEST['hasFlash']) && $_REQUEST['hasFlash'] == "false")
//                $flashContent = 0;
        $_SESSION['flashContent'] = $flashContent;
        $duplicateSessionID = $objSession->checkDuplicateLogin($_SESSION['timePerDay'], $browser);
        if ($duplicateSessionID != -1) {
            $path = "../userInterface/removeOtherSession.php?image1=$image1&image2=$image2";
            $sessionID = $duplicateSessionID;
            echo '<html><body>';
            echo '<form name="frmBrowser" id="frmBrowser" method="POST" action="' . $path . '">';
            echo '<input type="hidden" name="sessionID" id="sessionID" value="' . $sessionID . '">';
            $windowName=$_SESSION['windowName'];
            echo "</form><script>if(window.sessionStorage){sessionStorage.setItem('windowName','$windowName');console.log('sessionStorage supports')}else{window.name='$windowName';console.log('sessionStorage does not support');} document.frmBrowser.submit();</script></body></html>";
            exit;
        }
    } else {
        if (isset($_REQUEST['blockMindspark'])) {
            $_SESSION['blockMindspark'] = 1;
        }
    }
}

function checkBrowser($browserName, $browserVersion) {
    $browserCategory = '';
    global $cookies;
    global $localStorage;//$localStorage=!1;
    if (!isset($cookies)) $cookies=true;
    if (!isset($localStorage)) $localStorage=true;
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $isIPad = (!stripos($userAgent, 'ipad') ? false : true);
    $isAndroid = (!stripos($userAgent, 'android') ? false : true);
    $arrVersion = explode('.', $browserVersion);
    $majorVersion = $arrVersion[0];
    $browserSupport = array();
    $query = "SELECT browser,category FROM adepts_browserSupport";
    $result = mysql_query($query) or die(mysql_error());
    while ($line = mysql_fetch_array($result)) {
        $browserSupport[$line[0]] = $line[1];
    }
    if (!$browserSupport[$browserName . ' ' . $majorVersion]) {
        if (($browserName == "Internet Explorer" && $majorVersion > 10) || ($browserName == "Mozilla Firefox" && $majorVersion > 26) || ($browserName == "Google Chrome" && $majorVersion > 26)) {
            $browserCategory = 'green';
        }
    } else {
        $browserCategory = $browserSupport[$browserName . ' ' . $majorVersion];
    }
    if ($isAndroid || $isIPad) {
        if ($browserName == "Safari") {
            $browserCategory = "green";
        }
    }
	if (strpos($userAgent, 'Mac OS X ') !== false)
	{
		$browserCategory = "white";
	}
    if (!($cookies && $localStorage)) $browserCategory='red';
    $_SESSION['browserColor'] = $browserCategory;
    return $browserCategory;
}

function login($bypassComment = False) {
    $sessionID = $_SESSION["sessionID"];
    $schoolCode = $_SESSION["schoolCode"];
    $userID = $_SESSION['userID'];
    $objUser = new User($userID);
    $objSession = new session($userID, $sessionID);
    if ($_SESSION['rewardSystem'] == 1) {
        $objReward = new Sparkies($userID);
        $_SESSION["arrayPrompts"] = $objReward->checkForSparkieAlertsAtStart();
        $_SESSION["rewardTheme"] = $objReward->rewardTheme;
    }  
    $nextPage = "../userInterface/home.php";
    $qcode = "";
    //School Admin -All category user is made for the dev/ei team to access the teacher interface of any school
    //For this get the school code for which the data needs to be viewed.
    if ($objUser->category == "School Admin" && $objUser->subcategory == "All") {
        $newInterfaceFlag = $objUser->isSetNewInterface();
        if ($newInterfaceFlag == 2)
            $nextPage = "../teacherInterface/redirect.php?admin=1";
        else if ($newInterfaceFlag)
            $nextPage = "../teacherInterface/getSchoolDetails.php";
        else
            $nextPage = "../getSchoolDetails.php";
    }
    elseif (strcasecmp($objUser->category, "Teacher") == 0 || $objUser->category == "School Admin" || $objUser->category == "Home Center Admin") {
        $nextPage = "../ti_home.php";
        $newInterfaceFlag = $objUser->isSetNewInterface();
        if ($newInterfaceFlag == 2)
            $nextPage = "../teacherInterface/redirect.php";
        else if ($newInterfaceFlag == 1 || $newInterfaceFlag == 3)
            $nextPage = "../teacherInterface/home.php";

        $flag = 0;
        if (SUBJECTNO == 2)
            $flag = $objUser->checkForFeedback();
        $feedbackFlag = explode("~", $flag);

        if ($feedbackFlag[0] != 0) 
        {
            echo "<form name='hiddenForm' method='post' id='hiddenForm' action='../teacherInterface/feedBackForm.php'>";
            echo "<input type='hidden' name='setNo' id='setNo' value='".$feedbackFlag[1]."'/>";
            echo "</form>";
			echo "<script>";
            echo "document.hiddenForm.submit();";
            echo "</script>";
        }

        if (SUBJECTNO == 2 && $objUser->currentOrdertype == "pilot") {
            if ($objUser->checkForFeedback() != 0) {
                $nextPage = "../feedbackform.php";
            }
        }
    }
	
	        //Check if birth date not set for the user, if so redirect to the birth date page
    //Ignore the check for class 1 & 2
	if (strcasecmp($objUser->category, "STUDENT") == 0 || strcasecmp($objUser->category, "School admin") == 0 || strcasecmp($objUser->category, "TEACHER") == 0)
	{
		checkForOfflineURL($schoolCode,$objUser);
	}
	
    if ((strcasecmp($objUser->category, "STUDENT") == 0 && $objUser->childClass > 2) && ($objUser->childDob == "" || $objUser->childDob == "00-00-0000")) {
        $nextPage = "../userInterface/getBirthDate.php";
    }else if (strcasecmp($objUser->category, "STUDENT") == 0) {
        if (!$bypassComment) { //If any comments have been responded by EI and yet to be seen by the user, direct him to the comment response screen
            $comment_query = "SELECT count(srno) FROM adepts_userComments WHERE userID=$userID AND status='Closed' AND viewed=0";
            $comment_result = mysql_query($comment_query) or die("<br>Error in comment query - " . mysql_error());
            $comment_data = mysql_fetch_array($comment_result);
            if ($comment_data[0] > 0)
                $nextPage = "../userInterface/viewComments.php";
        }
        /*if (strcasecmp($objUser->category, "STUDENT") == 0 && strcasecmp($objUser->subcategory, "School") == 0 && ($objUser->childClass == 1 || $objUser->childClass == 2) && $nextPage != "viewComments.php") {
            $daysLoggedIn = $objUser->getNoOfDaysLoggedIn();
            if ($daysLoggedIn <= 7 && !isset($_POST['activityFinished']) && $_SESSION['flashContent'] == 1) {
                $nextPage = "../userInterface/mouseactivity.php";
            }
        }*/ //commented till mouse activity converted into html5
        $flag = 0;
        if (SUBJECTNO == 2)
            $flag = $objUser->checkForFeedback();
        //$flag=1;
        $feedbackFlag = explode("~", $flag);   

        if ($feedbackFlag[0] != 0) 
        {
            echo "<form name='hiddenForm' method='post' id='hiddenForm' action='../userInterface/feedBackForm.php'>";
            echo "<input type='hidden' name='setNo' id='setNo' value='".$feedbackFlag[1]."'/>";
            echo "</form>";
            echo "<script>";
            echo "document.hiddenForm.submit();";
            echo "</script>";
        }

        $_SESSION['timeSpentToday'] = getTimeSpent($userID);
        $limitExceeded = hasExceededTodaysLimit($userID);
        if ($limitExceeded != 0) {
			$_SESSION["limitExceeded"] = $limitExceeded;
            $objSession->setEndTime($limitExceeded);
            //code for log off
            $nextPage = "../userInterface/endSessionReport.php?mode=$limitExceeded";
        }
        //Monthly revision session currently only applicable for Maths
        else if (SUBJECTNO == 2 && strcasecmp($objUser->category, "STUDENT") == 0 && strcasecmp($objUser->subcategory, "School") == 0) {
			if(SERVER_TYPE=='LIVE')
			{
				//for SBA test
				$sbaTestMode = checkSbaAllowed($objUser->schoolCode, $objUser->childClass, $objUser->childSection, $objUser->userID);
				if ($sbaTestMode == "") {
					$daysLeftInSbaStr = getDaysLeftInSbaTest($objUser->schoolCode, $objUser->childClass, $objUser->childSection, $objUser->userID);
					$daysLeftInSbaArr = explode("~", $daysLeftInSbaStr);
					$daysLeftInSba = $daysLeftInSbaArr[0];
					if ($daysLeftInSba != -100) {
						if ($daysLeftInSba <= 0) {
							$sbaTestMode = "sbaTest";
						}
					}
				}
				if ($sbaTestMode != "") {
					echo "<html><body>";
					if ($sbaTestMode == "sbaTestStarted") {
						sbaTest('testPending');
						mysql_close();
					} else {
						$arrayTestDetails = getTestDetails($_SESSION['sbaTestID']);
						echo '<form id="frmHidForm" action="../userInterface/sbaInstruction.php" method="post">';
						echo '<input type="hidden" name="totalTime" value="' . $arrayTestDetails[0] . '">';
						echo '<input type="hidden" name="totalQues" value="' . $arrayTestDetails[1] . '">';
						echo '<input type="hidden" name="mode" id="mode" value="sbaTest">';
						echo '</form>';
						$windowName=$_SESSION['windowName'];
						echo "</form><script>if(window.sessionStorage){sessionStorage.setItem('windowName','$windowName');console.log('sessionStorage supports')}else{window.name='$windowName';console.log('sessionStorage does not support');} document.getElementById('frmHidForm').submit();</script></body></html>";
	
						
					   
						mysql_close();
						exit;
					}
				}
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
                //print_r($qcodeDetails);exit;
                $qcode = $qcodeDetails[0];
                if ($qcode != "") {
                    echo "<html><body>";
                    echo '<form id="frmHidForm" action="../userInterface/revisionSessionInstructions.php" method="post">';

                    echo '<input type="hidden" name="qcode" id="qcode" value="' . $qcode . '">';
                    echo '<input type="hidden" name="qno" id="qno" value="' . $qno . '">';
                    echo '<input type="hidden" name="sdl" id="sdl" value="' . $qcodeDetails[3] . '">';
                    echo '<input type="hidden" name="clusterCode" id="clusterCode" value="' . $qcodeDetails[1] . '">';
                    echo '<input type="hidden" name="ttCode" id="ttCode" value="' . $qcodeDetails[2] . '">';
                    echo '<input type="hidden" name="revisionSessionID" id="revisionSessionID" value="' . $revisionSessionID . '">';
                    echo '</form>';
                   $windowName=$_SESSION['windowName'];
            		echo "</form><script>if(window.sessionStorage){sessionStorage.setItem('windowName','$windowName');console.log('sessionStorage supports')}else{window.name='$windowName';console.log('sessionStorage does not support');}document.getElementById('frmHidForm').submit();</script></body></html>";
                    mysql_close();
                    exit;
                } else
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
    echo '<input type="hidden" name="sessionID" id="sessionID" value="' . $sessionID . '">';
    
   $windowName=$_SESSION['windowName'];
   echo "</form><script>if(window.sessionStorage){sessionStorage.setItem('windowName','$windowName');console.log('sessionStorage supports')}else{window.name='$windowName';console.log('sessionStorage does not support');} document.getElementById('frmHidForm').submit();</script></body></html>";
}

function sbaTest($testMode) {
    $userID = $_SESSION['userID'];
    $objUser = new User($userID);

    $testMode = isset($testMode) ? $testMode : "";
    $sbaDetailArray = getSbaTestID($userID, $objUser->schoolCode, $objUser->childClass, $objUser->childSection, $testMode);
    $sbaTestID = $sbaDetailArray["sbaID"];
    $totalQues = $sbaDetailArray["totalQues"];
    $maxTime = $sbaDetailArray["maxTime"];
    $qcodeStr = $sbaDetailArray["qcodes"];

    $_SESSION['sbaTestID'] = $sbaTestID;
    if (checkForPendingTest($userID, $sbaTestID) == "notStarted") {
        $qcodeStr = insertSbaDetails($userID, $sbaTestID, $qcodeStr, $totalQues, $maxTime, $_SESSION["sessionID"]);
    }
    $_SESSION["qcodeStr"] = getQcodeDetails($userID, $sbaTestID);
    $timeTaken = getSbaQcode($userID, $sbaTestID);
    $qcode = $_SESSION["qcode"];
    $qno = $_SESSION["qno"];
    $timeLeft = ($maxTime * 60) - $timeTaken;
    echo '<form id="frmHidForm" action="../userInterface/sbaQuestion.php" method="post">';
    echo '<input type="hidden" name="qno" value="' . $qno . '">';
    echo '<input type="hidden" name="timeLeft" value="' . $timeLeft . '">';
    echo '<input type="hidden" name="qcode" value="' . $qcode . '">';
    echo '<input type="hidden" name="totalTime" value="' . $maxTime . '">';
    echo '<input type="hidden" name="totalQuestion" value="' . $totalQues . '">';
   $windowName=$_SESSION['windowName'];
            		echo "</form><script>if(window.sessionStorage){sessionStorage.setItem('windowName','$windowName');console.log('sessionStorage supports')}else{window.name='$windowName';console.log('sessionStorage does not support');} document.getElementById('frmHidForm').submit();</script>";
    exit;
}

function getTimeSpent($userID, $fromDay = "", $tillDay = "") {
    $fromDay = $fromDay == "" ? date("Ymd") : str_replace("-", "", $fromDay);
    $tillDay = $tillDay == "" ? date("Ymd") : str_replace("-", "", $tillDay);
    $query = "SELECT sum(TIMESTAMPDIFF(SECOND, startTime,endTime)) FROM adepts_sessionStatus b
WHERE  b.userID=$userID AND startTime_int >= $fromDay and startTime_int <=$tillDay and endTime is not null;";
    $time_result = mysql_query($query) or reportError($query . mysql_error());
    $line = mysql_fetch_array($time_result);
    $timeSpent = $line[0];
    return $timeSpent;
}

//function to check if the daily/weekly time limit is done with, if so log him out.
function hasExceededTodaysLimit($userID, $quesno = "") {
    $schoolCode = $_SESSION['schoolCode'];
    $timeAllowedPerDay = $_SESSION['timePerDay'];
    $subcategory = $_SESSION['subcategory'];
    $category = $_SESSION['admin'];
    //just a second check, not needed as such;
    if ($timeAllowedPerDay == "" && (strcasecmp($category, "STUDENT") == 0))
        $timeAllowedPerDay = 30;
    if ($timeAllowedPerDay == "")
        return 0;
    else {
        $timeAllowedPerDay = $timeAllowedPerDay * 60;    //convert to secs (daily limit)
        //$maxTimeAllowed    = $timeAllowedPerDay * 2;	// max 2 sessions of 30 mins (timeAllowedPerDay mins of userDetails) allowed
        $maxTimeAllowed = 5400; //90 * 60;    //Changed to max 1-1/2 hr per day for all users on 4th Oct.
        $sessionStartTime = $_SESSION["sessionStartTime"];

        $now = date("Y-m-d H:i:s");
        $sessionTime = convertToTime($now) - convertToTime($sessionStartTime);
        $timeSpentToday = $_SESSION['timeSpentToday'] + $sessionTime;
        //$timeSpentThisWeek = $_SESSION['timeSpentInTheWeek'] + $sessionTime;
        //-5 => Weekly limit over, -6 => daily limit over -7 => session limit over

        if (strcasecmp($subcategory, "School") != 0) {
            if ($timeSpentToday > $maxTimeAllowed)
                return -6; //Daily quota Over
            //else if ($sessionTime > $timeAllowedPerDay)
                //return -7; //session time limit over removed for retail mantis ID - 13178
            else
                return 0;
        }
        else { //For school users, check if the session is from the school i.e. between 7 to 4 Mon-Fri
            $ts = mktime(substr($sessionStartTime, 11, 2), substr($sessionStartTime, 14, 2), substr($sessionStartTime, 17, 2), substr($sessionStartTime, 5, 2), substr($sessionStartTime, 8, 2), substr($sessionStartTime, 0, 4));
            $start = mktime(07, 00);
            $end = mktime(16, 00);
            $timeStarted = mktime(substr($sessionStartTime, 11, 2), substr($sessionStartTime, 14, 2), substr($sessionStartTime, 17, 2));
            $startDay = 1;
            $endDay = 5;
            if ($schoolCode == 1752 || $schoolCode == 37421 || $schoolCode == 359413) //For SNK consider Mon-Sat as working days.
                $endDay = 6;
            if ($schoolCode == 1752) //For SNK, consider end time as 5:45 - based on mail from SNK dated 4th Oct,2012 related to PBL
                $end = mktime(17, 45);

            if (date("w", $ts) >= $startDay && date("w", $ts) <= $endDay && $timeStarted >= $start && $timeStarted < $end) {
                if ($sessionTime > $timeAllowedPerDay)
                    return -7;
                else
                    return 0;
            }
            else {
                if ($timeSpentToday > $maxTimeAllowed)
                    return -6;
                else if ($sessionTime > $timeAllowedPerDay)
                    return -7;
                else
                    return 0;
            }
        }
    }
}

function convertToTime($date) {
    $hr = substr($date, 11, 2);
    $mm = substr($date, 14, 2);
    $ss = substr($date, 17, 2);
    $day = substr($date, 8, 2);
    $mnth = substr($date, 5, 2);
    $yr = substr($date, 0, 4);
    $time = mktime($hr, $mm, $ss, $mnth, $day, $yr);
    return $time;
}

function checkPendingRevisionSession($userID, $schoolCode, $class, $section) {
    $revisionSessionID = "";
    //Check if any revision session active for the class/section.
    $query = "SELECT revisionSessionID, datediff(curdate(), activationDate) as noofdays FROM adepts_revisionSessionMaster
					  WHERE  isActive=1 AND activationDate<=curdate() AND schoolCode=$schoolCode AND class=$class";
    if ($section != "")
        $query .= " AND section='$section'";
    //echo $query."<br/>";exit;
    $result = mysql_query($query) or die(mysql_error());
    if ($line = mysql_fetch_array($result)) {
        $days = $line['noofdays'];
        if ($days < 31) { //by default, consider the revision session as inactive after a month
            $revisionSessionID = $line['revisionSessionID'];
            $query = "SELECT status FROM adepts_revisionSessionStatus WHERE userID=" . $userID . " AND revisionSessionID=$revisionSessionID";
            //echo $query."<br/>";exit;
            $result = mysql_query($query) or die(mysql_error());
            if ($line = mysql_fetch_array($result)) {
                //check if revision session not completed
                if (strcasecmp($line['status'], "completed") == 0) {
                    $revisionSessionID = "";
                } else {
                    $timeSpent = getTimeSpentOnRevisionSession($userID, $revisionSessionID); //Get the time spent till now on this revision session.
                }
            } else {
                //make an entry in the revision session status table for this user-revision session combination
                $query = "INSERT INTO adepts_revisionSessionStatus (userID, revisionSessionID, noOfQuestions, status, lastModified) VALUES
									($userID, $revisionSessionID,0, 'incomplete','" . date("Y-m-d H:i:s") . "')";
                $result = mysql_query($query) or die(mysql_error());
                $timeSpent = 0; //Initialize the time spent to zero
            }
            //Set the time in the session.
            $_SESSION['timeSpentOnRevisionSession'] = $timeSpent;
        }
    }
    return $revisionSessionID;
}

function getRevisionSessionStartDate($schoolCode, $class, $section, $currentRevisionSessionID) {
    $startDate = "";

    $query = "SELECT activationDate FROM adepts_revisionSessionMaster WHERE revisionSessionID=$currentRevisionSessionID";
    $result = mysql_query($query) or die(mysql_error());
    $line = mysql_fetch_array($result);
    $curRevisionSessionStartDate = $line[0];

    //Get the last revision session id, if any.
    $query = "SELECT revisionSessionID, date_add(activationDate, interval 1 day) as startDate, datediff('$curRevisionSessionStartDate',activationDate) as days
			   FROM   adepts_revisionSessionMaster
			   WHERE  schoolCode=$schoolCode AND class = $class AND revisionSessionID<>$currentRevisionSessionID";
    if ($section != "")
        $query .= " AND section='$section'";
    $query .= " AND YEAR(activationDate)<>" . substr($curRevisionSessionStartDate, 0, 4) . " AND MONTH(activationDate)<>" . substr($curRevisionSessionStartDate, 5, 2); //This condn is for the case where a teacher activates and deactivates the session in same month by chance.
    $query .= " ORDER BY activationDate DESC limit 1";
    //echo "<br/>".$query."<br/>";
    $result = mysql_query($query) or die(mysql_error());

    if ($line = mysql_fetch_array($result)) {
        //Check if the difference between cur and last revision date is more than a month(30 days), if so take 1 month prior as the start date
        $daysDiff = $line['days'];
        if ($daysDiff > 30) {
            $startDate = date("Y-m-d", mktime(0, 0, 0, substr($curRevisionSessionStartDate, 5, 2) - 1, substr($curRevisionSessionStartDate, 8, 2) + 1, substr($curRevisionSessionStartDate, 0, 4)));
        } else
            $startDate = $line['startDate']; //if any previous revision session found, get the end date of the last revision session i.e. the max date.
    } else //For the first revision test, consider all topics covered from the 1st of the month
        $startDate = substr($curRevisionSessionStartDate, 0, 4) . "-" . substr($curRevisionSessionStartDate, 5, 2) . "-01";
    //$startDate = date("Y")."-".date("m")."-01";
    return $startDate . "~" . $curRevisionSessionStartDate;
}

function getTimeSpentOnRevisionSession($userID, $revisionSessionID) {
    $query = "SELECT DISTINCT a.sessionID, startTime, endTime
			  FROM   " . TBL_SESSION_STATUS . " a, adepts_revisionSessionDetails b
		      WHERE  b.userID=" . $userID . " AND a.userID=b.userID AND a.sessionID=b.sessionID AND revisionSessionID=$revisionSessionID";
    //echo $query."<br/>";
    $time_result = mysql_query($query) or die(mysql_error());
    $timeSpent = 0;
    while ($time_line = mysql_fetch_array($time_result)) {
        $startTime = convertToTime($time_line[1]);
        if ($time_line[2] != "") {
            $endTime = convertToTime($time_line[2]);
        } else {
            $query = "SELECT max(lastModified) FROM adepts_revisionSessionDetails WHERE sessionID=" . $time_line[0] . " AND userID=" . $userID;
            $r = mysql_query($query);
            $l = mysql_fetch_array($r);
            if ($l[0] == "")
                continue;
            else
                $endTime = convertToTime($l[0]);
        }
        $timeSpent = $timeSpent + ($endTime - $startTime);        //in secs
    }
    return $timeSpent;
}

function populateRevisionClusters($userID, $revisionSessionID, $startDate, $endDate) {
    $ttArray = array();
    $revisionSessionClusterArray = array(); //2D array, with the keys as TT code and clustercode.
    $clustersAttemptedArray = array();

		$teachertopiclist = "";
		$childClass = 	$_SESSION['childClass'];  
		$childSection = 	$_SESSION['childSection'];
		$schoolCode =	$_SESSION['schoolCode']; 
		$quecount = "";

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
			  FROM   " . TBL_TOPIC_STATUS . " a, " . TBL_CLUSTER_STATUS . " b, adepts_clusterMaster c
			  WHERE  a.userID=$userID AND a.ttAttemptID=b.ttAttemptID and a.teacherTopicCode in ($teachertopiclist) AND b.clusterCode=c.clusterCode AND a.userID=b.userID AND b.result='SUCCESS' AND (isnull(c.clusterType) OR c.clusterType='learning') AND
			  		 (b.lastModified BETWEEN '$startdate' AND '$endDate 23:59:59')
			  GROUP BY teacherTopicCode";


    $result = mysql_query($query) or die(mysql_error() . $query);
    while ($line = mysql_fetch_array($result)) {
        array_push($ttArray, $line[0]);
        $clusterArray = explode(",", $line[1]);
        for ($i = 0; $i < count($clusterArray); $i++) {
            if (!in_array($clusterArray[$i], $clustersAttemptedArray)) { //This check is for the case where a same cluster has been cleared in two different Teacher topic.
                $sdlArray = getGreatestOneThirdSDL($clusterArray[$i]);
                foreach ($sdlArray as $sdl => $ques) {
                    $revisionSessionClusterArray[$line[0]][strtoupper($clusterArray[$i])][$sdl][0] = ""; //ques attempted in the revision session for the TT/cluster/SDL - initialize it to blank
                    $revisionSessionClusterArray[$line[0]][strtoupper($clusterArray[$i])][$sdl][1] = $ques; //all ques for this SDL
                }
                array_push($clustersAttemptedArray, $clusterArray[$i]);
            }
        }
    }

	$strcount = '';
	foreach ($revisionSessionClusterArray as $key=>$value) {
		foreach ($value as $iKey => $iValue) {
			foreach ($iValue as $i => $val) {
				$strcount .= $val[1].",";
			}
		}	
	}   

	$quecount =	substr_count($strcount, ',');
	if($quecount > 30)
	{
		$quecount = 30;
	}

	$_SESSION['questioncount'] = $quecount;
    $_SESSION['revisionSessionTTArray'] = $ttArray;
    $_SESSION['revisionSessionClusterArray'] = $revisionSessionClusterArray;
}

function getRevisionSessionQuestionsAttempted($userID, $revisionSessionID) {
    $qno = 0;
    $prevTT = $prevCluster = "";
    $revisionSessionClusterArray = $_SESSION['revisionSessionClusterArray'];
    //Populate the questions attempted previously by the user in this revision session, if any. (this will happen if the user closed the revision session in between)
    $query = "SELECT teacherTopicCode, a.clusterCode,  a.qcode, b.subdifficultylevel, a.questionNo
			   FROM   adepts_revisionSessionDetails a,
			   		  adepts_questions b
			   WHERE  userID=$userID AND revisionSessionID=$revisionSessionID AND
			   		  a.qcode=b.qcode
			   ORDER BY a.questionNo";
    $result = mysql_query($query) or die(mysql_error());
    while ($line = mysql_fetch_array($result)) {

        $qno = $line['questionNo'];
        $prevTT = $line[0];
        $prevCluster = $line[1];
        if ($revisionSessionClusterArray[$line[0]][strtoupper($line[1])][$line[3]][0] != "")
            $revisionSessionClusterArray[$line[0]][strtoupper($line[1])][$line[3]][0] .= "," . $line[2];
        else
            $revisionSessionClusterArray[$line[0]][strtoupper($line[1])][$line[3]][0] = $line[2];
    }
    $qno++;

    $_SESSION['revisionSessionClusterArray'] = $revisionSessionClusterArray;
    return $qno . "~" . $prevTT . "~" . $prevCluster;
}

function getNextQuestionForRevisionSession($userID, $revisionSessionID, $prevTT, $prevCluster) {
    $qcode = $curTT = $curCluster = "";
    $qcodeDetails = array();

    $revisionSessionClusterArray = $_SESSION['revisionSessionClusterArray'];
    $ttArray = $_SESSION['revisionSessionTTArray'];

    if ($prevTT == "") { //first question in the revision session. start from the first TT
        $ttCode = $ttArray[0];
        $clusterArray = array_keys($revisionSessionClusterArray[$ttCode]);
        $clusterCode = $clusterArray[rand(0, count($clusterArray) - 1)];
        $sdlArray = array_keys($revisionSessionClusterArray[$ttCode][$clusterCode]); //All sdls can be considered for the first case
        for ($i = 0; $i < count($sdlArray); $i++) {
            $questionsArray[$sdlArray[$i]][0] = $revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$i]][0];
            $questionsArray[$sdlArray[$i]][1] = $revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$i]][1];
        }
        $tmpResponse = getQuestionOfCluster($userID, $clusterCode, $ttCode, $sdlArray, $questionsArray);
        $tmpResponse = explode("~", $tmpResponse);
        $qcode = $tmpResponse[1];
        $curCluster = $clusterCode;
        $curTT = $ttCode;
        $curSDL = $tmpResponse[0];
    } else {
        for ($ttCounter = 0; $prevTT != $ttArray[$ttCounter] && $ttCounter < count($ttArray); $ttCounter++)
            ;
        $ttCounter = $ttCounter + 1;  //Pick question from TTs attempted in a cyclic order
        if ($ttCounter >= count($ttArray))
            $ttCounter = 0;

        $ttsVisited = 0;
        while ($ttsVisited < count($ttArray) && $qcode == "") {
            $clusterArray = array();
            $ttCode = $ttArray[$ttCounter];

            //echo $ttCode."<br/>";
            $clustersClearedInTT = array_keys($revisionSessionClusterArray[$ttCode]);

            for ($i = 0; $i < count($clustersClearedInTT); $i++) {
                $clusterCode = $clustersClearedInTT[$i];
                $sdlArray = array_keys($revisionSessionClusterArray[$ttCode][$clusterCode]);
                $clusterGiven = 0;
                for ($j = 0; $j < count($sdlArray) && !$clusterGiven; $j++)
                    if ($revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$j]][0] != "")
                        $clusterGiven = 1;
                if (!$clusterGiven)
                    array_push($clusterArray, $clusterCode);
            }

            if (count($clusterArray) > 0) { //if there are any clusters from which question not given yet
                $clusterCode = $clusterArray[rand(0, count($clusterArray) - 1)];
                $sdlArray = array_keys($revisionSessionClusterArray[$ttCode][$clusterCode]);
                $sdlToBeGiven = array();

                for ($j = 0; $j < count($sdlArray); $j++) {
                    if ($revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$j]][0] == "") {
                        array_push($sdlToBeGiven, $sdlArray[$j]);
                        $questionsArray[$sdlArray[$j]][0] = $revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$j]][0];
                        $questionsArray[$sdlArray[$j]][1] = $revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$j]][1];
                    }
                }


                $tmpResponse = getQuestionOfCluster($userID, $clusterCode, $ttCode, $sdlToBeGiven, $questionsArray);
                $tmpResponse = explode("~", $tmpResponse);
                $qcode = $tmpResponse[1];
                $curSDL = $tmpResponse[0];
                $curCluster = $clusterCode;
                $curTT = $ttCode;
            } else { //if all clusters in a tt are exhausted, check for the next tt
                $ttsVisited++;
                //echo $ttsVisited;
                $ttCounter = $ttCounter + 1;
                if ($ttCounter == count($ttArray)) {
                    $ttCounter = 0;
                }
            }
        }

        if ($qcode == "") { //if a question from all clusters of all tts are given
            $ttsVisited = 0;
            while ($ttsVisited < count($ttArray) && $qcode == "") {
                $ttCode = $ttArray[$ttCounter];
                $clustersVisited = 0;
                $clusterArray = array_keys($revisionSessionClusterArray[$ttCode]); //check for all clusters
                $totalClusters = count($clusterArray);
                $clustersAlreadySeen = array();

                //till a question can be given - the terminating condition will be if all questions of highest 1/3rd sdls of all clusters attempted are given or time limit is exhausted.
                while ($qcode == "" && $clustersVisited < $totalClusters) {
                    $clustersToBeSeen = array_diff($clusterArray, $clustersAlreadySeen);
                    $tmpKeys = array_keys($clustersToBeSeen);
                    $randomNo = mt_rand(0, count($tmpKeys) - 1);
                    $clusterCode = $clustersToBeSeen[$tmpKeys[$randomNo]];
                    $sdlArray = array_keys($revisionSessionClusterArray[$ttCode][$clusterCode]);
                    $sdlToBeGiven = array();
                    $questionsArray = array();

                    for ($j = 0; $j < count($sdlArray); $j++) { //Check for sdls from which no question is given yet
                        $quesAttempted = $revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$j]][0];
                        if ($quesAttempted == "") {
                            array_push($sdlToBeGiven, $sdlArray[$j]);
                            $questionsArray[$sdlArray[$j]][0] = $revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$j]][0];
                            $questionsArray[$sdlArray[$j]][1] = $revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$j]][1];
                        }
                    }

                    if (count($sdlToBeGiven) > 0) {
                        $tmpResponse = getQuestionOfCluster($userID, $clusterCode, $ttCode, $sdlToBeGiven, $questionsArray);
                        $tmpResponse = explode("~", $tmpResponse);
                        $qcode = $tmpResponse[1];
                        $curSDL = $tmpResponse[0];
                        $curCluster = $clusterCode;
                        $curTT = $ttCode;
                    }
                    array_push($clustersAlreadySeen, $clusterCode);
                    $clustersVisited++;
                }
                $ttsVisited++;
                $ttCounter = $ttCounter + 1;
                if ($ttCounter == count($ttArray))
                    $ttCounter = 0;
            }
        }

        if ($qcode == "") { //if a question from all sdls of all clusters of all tts are given
            $ttsVisited = 0;
            while ($ttsVisited < count($ttArray) && $qcode == "") {
                $ttCode = $ttArray[$ttCounter];
                $clustersVisited = 0;
                $clusterArray = array_keys($revisionSessionClusterArray[$ttCode]); //check for all clusters
                $totalClusters = count($clusterArray);
                $clustersAlreadySeen = array();
                //till a question can be given - the terminating condition will be if all questions of highest 1/3rd sdls of all clusters attempted are given or time limit is exhausted.
                while ($qcode == "" && $clustersVisited < $totalClusters) {
                    $clustersToBeSeen = array_diff($clusterArray, $clustersAlreadySeen);
                    $tmpKeys = array_keys($clustersToBeSeen);
                    $randomNo = mt_rand(0, count($tmpKeys) - 1);
                    $clusterCode = $clustersToBeSeen[$tmpKeys[$randomNo]];
                    $sdlArray = array_keys($revisionSessionClusterArray[$ttCode][$clusterCode]);
                    $sdlToBeGiven = array();
                    $questionsArray = array();

                    for ($j = 0; $j < count($sdlArray); $j++) {
                        $totalQues = explode(",", $revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$j]][1]);
                        $quesAttempted = explode(",", $revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$j]][0]);

                        if (count($totalQues) > count($quesAttempted)) {
                            array_push($sdlToBeGiven, $sdlArray[$j]);
                            $questionsArray[$sdlArray[$j]][0] = $revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$j]][0];
                            $questionsArray[$sdlArray[$j]][1] = $revisionSessionClusterArray[$ttCode][$clusterCode][$sdlArray[$j]][1];
                        }
                    }

                    if (count($sdlToBeGiven) > 0) {
                        $tmpResponse = getQuestionOfCluster($userID, $clusterCode, $ttCode, $sdlToBeGiven, $questionsArray);
                        $tmpResponse = explode("~", $tmpResponse);
                        $qcode = $tmpResponse[1];
                        $curSDL = $tmpResponse[0];
                        $curCluster = $clusterCode;
                        $curTT = $ttCode;
                    }
                    array_push($clustersAlreadySeen, $clusterCode);
                    $clustersVisited++;
                }
                $ttsVisited++;
                $ttCounter = $ttCounter + 1;
                if ($ttCounter == count($ttArray))
                    $ttCounter = 0;
            }
        }
    }


    $qcodeDetails[0] = $qcode;
    $qcodeDetails[1] = $curCluster;
    $qcodeDetails[2] = $curTT;
    $qcodeDetails[3] = $curSDL;
    return $qcodeDetails;
}

function getQuestionOfCluster($userID, $clusterCode, $ttCode, $sdlArray, $questionsArray) {
    $questionsAlreadyAttemptedArray = array();
    if (count($sdlArray) > 1) {
        $ttAttemptID_query = "SELECT ttAttemptID FROM " . TBL_TOPIC_STATUS . " WHERE userID=$userID AND teacherTopicCode='$ttCode'";
        $ttAttemptID_result = mysql_query($ttAttemptID_query);
        $ttAttemptIDStr = "";
        while ($ttAttemptID_line = mysql_fetch_array($ttAttemptID_result))
            $ttAttemptIDStr .= $ttAttemptID_line[0] . ",";
        $ttAttemptIDStr = substr($ttAttemptIDStr, 0, -1);
        if ($ttAttemptIDStr != "") {
            $query = "SELECT   subdifficultylevel, count(srno) as noOfQuesAttempted
    				  FROM 	   adepts_questions a, " . TBL_QUES_ATTEMPT_CLASS . " b," . TBL_CLUSTER_STATUS . " c
    				  WHERE    a.qcode=b.qcode AND a.clusterCode=c.clusterCode AND b.clusterAttemptID=c.clusterAttemptID AND
    				           c.userID=$userID AND a.clusterCode='$clusterCode' AND  subdifficultylevel in (" . implode(",", $sdlArray) . ") AND
    				  		   ttAttemptID in ($ttAttemptIDStr)
    				  GROUP BY subdifficultylevel ORDER BY noOfQuesAttempted DESC";
            //echo $query."<br/>";
            $result = mysql_query($query) or die(mysql_error());

            if ($line = mysql_fetch_array($result))
                $sdl = $line[0];
            else
                $sdl = $sdlArray[0];
        } else
            $sdl = $sdlArray[0];
    }
    else {
        $sdl = $sdlArray[0];
    }

    $allQuesArray = explode(",", $questionsArray[$sdl][1]);
    if ($questionsArray[$sdl][0] != "")
        $questionsAlreadyAttemptedArray = explode(",", $questionsArray[$sdl][0]);
    $quesToBeGiven = array_diff($allQuesArray, $questionsAlreadyAttemptedArray);

    if (count($quesToBeGiven) > 0) {
        $tmpKeys = array_keys($quesToBeGiven);
        $no = rand(0, count($tmpKeys) - 1);
        $qcode = $quesToBeGiven[$tmpKeys[$no]];
    }

    return $sdl . "~" . $qcode;
}

function updateRevisionSessionStatus($userID, $revisionSessionID) {
    $query = "SELECT sum(R),count(srno) FROM adepts_revisionSessionDetails WHERE revisionSessionID=$revisionSessionID AND userID=$userID";
    $result = mysql_query($query);
    $line = mysql_fetch_array($result);
    $correct = $line[0];
    $totalQues = $line[1];
    $perCorrect = 0;
    if ($totalQues > 0)
        $perCorrect = round($correct * 100 / $totalQues, 2);

    $noOfSparkies = 0;
    if ($totalQues >= 20) {
        if ($perCorrect >= 60 && $perCorrect <= 74)
            $noOfSparkies = 4;
        elseif ($perCorrect >= 75 && $perCorrect <= 89)
            $noOfSparkies = 6;
        elseif ($perCorrect >= 90)
            $noOfSparkies = 10;
    }
    $query = "UPDATE adepts_revisionSessionStatus SET status='completed', perCorrect=$perCorrect, noOfSparkies=$noOfSparkies WHERE revisionSessionID=$revisionSessionID AND userID=$userID";
    mysql_query($query) or die(mysql_error());

    $query = "UPDATE " . TBL_SESSION_STATUS . " SET noOfJumps=$noOfSparkies WHERE userID=$userID AND sessionID=" . $_SESSION['sessionID'];
    mysql_query($query);
}

function loadDetails($userID, $childClass) {
    $query = "SELECT qcode, clusterCode, clusterAttemptID, teacherTopicCode, ttAttemptID, currentSDL, remedialStack, progressUpdate, remedialMode, gameID
                            FROM " . TBL_CURRENT_STATUS . " WHERE userID=$userID AND status=1 ORDER BY srno DESC";
    $result = mysql_query($query) or die("1" . mysql_error());
    if ($user_row = mysql_fetch_array($result)) {
        /* if(!isActive($userID,$user_row['teacherTopicCode']))
          {
          setInactive($user_row['ttAttemptID'], $userID);
          return -1;
          } */
        if (!isset($_SESSION['qno']))
            $_SESSION["qno"] = 1;
        $_SESSION["qcode"] = $user_row["qcode"];
        $_SESSION["clusterCode"] = $user_row["clusterCode"];
        //$_SESSION["currentClusterType"] 			= findClusterType($user_row["clusterCode"]); // Added By Manish Dariyani For Practice Cluster
        $_SESSION["clusterAttemptID"] = $user_row["clusterAttemptID"];
        $_SESSION["teacherTopicCode"] = $user_row["teacherTopicCode"];
        $_SESSION["teacherTopicAttemptID"] = $user_row["ttAttemptID"];
        $_SESSION["correctInARow"] = 0;                //No jumps for 3 correct in a row across the sessions
        $_SESSION["currentSDL"] = $user_row["currentSDL"];
        $_SESSION["remedialStack"] = $user_row["remedialStack"];
        if (!isset($_SESSION["noOfJumps"]))
            $_SESSION["noOfJumps"] = 0;
        $_SESSION['quesCorrectInALevelOfTopic'] = 0;
        $_SESSION['questionType'] = "normal";
        $_SESSION['challengeQuestionsArray'] = "";
        $_SESSION['challengeQuestionsCorrect'] = "";
        if ($user_row['gameID'] != "")
            $_SESSION['game'] = true;
        else
            $_SESSION['game'] = false;
        if (!isset($_SESSION['gamePlayed']))
            $_SESSION['gamePlayed'] = 0;
        $_SESSION['qid'] = "";
        $_SESSION['prevQcode'] = "";
        $_SESSION['commonInstruction'] = "";
        $_SESSION['introVids'] = "";
        $_SESSION['competitiveExamCQ'] = 0;
        $remedialMode = $user_row["remedialMode"] == 1 ? 1 : 0;
        $_SESSION['remedialMode'] = $remedialMode;
        setTeacherTopic($user_row["teacherTopicCode"]);

        $qcode = $user_row["qcode"];
        $clusterCode = $user_row["clusterCode"];
        //Set the flow of TT
        $flow_query = "SELECT flow, ttAttemptNo, noOfQuesAttempted, perCorrect, progress FROM " . TBL_TOPIC_STATUS . " WHERE ttAttemptID=" . $user_row["ttAttemptID"];
        $flow_result = mysql_query($flow_query);
        $flow_line = mysql_fetch_array($flow_result);
        $_SESSION['flow'] = $flow_line['flow'];
        $_SESSION['topicAttemptNo'] = $flow_line['ttAttemptNo'];
        $noOfQuesAttempted = $flow_line['noOfQuesAttempted'];
        $perCorrect = $flow_line['perCorrect'];

        if ($user_row["progressUpdate"] == 1) {
            $progressUpdateObj = new topicProgressCalculation($user_row['teacherTopicCode'], $childClass, $flow_line['flow'], $user_row["ttAttemptID"], SUBJECTNO);
            $progressUpdateObj->updateProgress();
        }
        getTopicProgressDetails($userID, $user_row['teacherTopicCode']);
        if ($qcode != -2 && $qcode != -3) {
            $objTT = new teacherTopic($user_row['teacherTopicCode'], $childClass, $flow_line['flow']);
            $_SESSION['topicProgressDetails'] = $objTT->getProgressDetailsAtSDL();
            fillTopicProgressBar($user_row['teacherTopicCode'], $childClass, $flow_line['flow'], $userID);

            $_SESSION['topicWiseProgressStatus'] = array();
            $_SESSION['topicWiseProgressStatus'] = findCLustersAttendedInTopic($_SESSION['teacherTopicAttemptID']);

            loadArrays($clusterCode, $user_row["clusterAttemptID"], $userID, $user_row["ttAttemptID"], $_SESSION['flashContent']);
            $tempTT = $user_row['teacherTopicCode'];
            if ($objTT->customTopic == 1)  //if custom topic load CQs of parent TT
                $tempTT = $objTT->parentTTCode;
            loadChallengeQuestions($tempTT, $userID);
            if (!$remedialMode)
                manipulateArrays($user_row["currentSDL"], $qcode, "normal");

            $higherLevel = isAtaHigherLevelInTT($objTT, $user_row['clusterCode'], $childClass);
            $_SESSION['higherLevel'] = $higherLevel;
            $lowerLevel = 0;
            if (in_array($user_row['clusterCode'], $objTT->getLowerLevelClusters()))
                $lowerLevel = 1;
            $_SESSION['lowerLevel'] = $lowerLevel;
        }

        return $qcode;
    } else
        return -1;
}

function setTeacherTopic($teacherTopicCode) {
    $query = "SELECT teacherTopicDesc FROM adepts_teacherTopicMaster WHERE teacherTopicCode='" . $teacherTopicCode . "'";
    $result = mysql_query($query);
    $line = mysql_fetch_array($result);
    $_SESSION['teacherTopicName'] = $line[0];
}

function getTopicProgressDetails($userID, $ttCode) {
    $quesAttempted = $quesCorrect = $progress = 0;
    $query = "SELECT progress, noOfQuesAttempted, perCorrect FROM adepts_teacherTopicStatus WHERE userID=$userID AND teacherTopicCode='$ttCode'";
    $result = mysql_query($query);
    while ($line = mysql_fetch_array($result)) {
        $quesAttempted += $line['noOfQuesAttempted'];
        $quesCorrect += round($line['noOfQuesAttempted'] * $line['perCorrect'] / 100);
        if ($progress < $line['progress'])
            $progress = $line['progress'];
    }
    $_SESSION['progressInTopic'] = round($progress);
    $_SESSION['quesAttemptedInTopic'] = $quesAttempted;
    $_SESSION['quesCorrectInTopic'] = $quesCorrect;
    $_SESSION['lowerLevel'] = 0; //Initialize it to 0, will get updated later.
}

function fillTopicProgressBar($teacherTopicCode, $childClass, $flow, $userID) {
    $objTT = new teacherTopic($teacherTopicCode, $childClass, $flow);

    $noClustersOfClass = 0;
    $tmparrayy = array();
    $tmparrayyName = array();

    $classSpecificClustersForTT = array();
    $classSpecificClustersForTT = $objTT->clusterFlowArray;
    $meantForClasses = $objTT->meantForClasses;
    $srno = 0;
    foreach ($classSpecificClustersForTT as $key => $value) {
//        echo $srno++;
        $classes = explode(',', $classSpecificClustersForTT[$key][1]);
        foreach ($classes as $keys => $values) {
            if ($classes[$keys] == $childClass) {
                array_push($tmparrayy, $classSpecificClustersForTT[$key]);
                $noClustersOfClass = 1;
                $game = checkForGame($classSpecificClustersForTT[$key][0], "", "", "", $childClass, 1);
                $timedTest = checkForTimedTest("", "", $classSpecificClustersForTT[$key][0], "", $childClass, 1);

                if ($game != -1 && $game != "") {
                    $gameArr = array();
                    array_push($gameArr, $game['gameID']);
                    array_push($gameArr, $game['gameName']);
                    array_push($gameArr, 'activity');
                    if (hasAttemptedGame($userID, $game['gameID'])) {

                        array_push($gameArr, 'attempted');
                    } else {
                        array_push($gameArr, 'NA');
                    }
                    array_push($tmparrayy, $gameArr);
                }

                if ($timedTest != -1 && $timedTest != "") {
                    $timedTestArr = array();
                    array_push($timedTestArr, $timedTest['timedTestCode']);
                    array_push($timedTestArr, $timedTest['timedTestName']);
                    array_push($timedTestArr, 'timedTest');
                    if (hasClearedTimedTest($timedTest['timedTestCode'], $classSpecificClustersForTT[$key][0], $userID) == 1) {
                        array_push($timedTestArr, 'passed');
                    } elseif (hasClearedTimedTest($timedTest['timedTestCode'], $classSpecificClustersForTT[$key][0], $userID) == 2) {
                        array_push($timedTestArr, 'failed');
                    } elseif (hasClearedTimedTest($timedTest['timedTestCode'], $classSpecificClustersForTT[$key][0], $userID) == 0) {
                        array_push($timedTestArr, 'NA');
                    }
                    array_push($tmparrayy, $timedTestArr);
                }
            }

            //array_push($tmparrayyName[$classSpecificClustersForTT[$key][0]],getClusterDetails($classSpecificClustersForTT[$key][0],"",1));
            $tmparrayyName[$classSpecificClustersForTT[$key][0]] = getClusterDetails($classSpecificClustersForTT[$key][0], "", 1);
        }
    }

    if ($noClustersOfClass == 0) {
        if ($childClass > $meantForClasses[count($meantForClasses) - 1]) {
            $allowClass = $meantForClasses[count($meantForClasses) - 1];
        } elseif ($childClass <= $meantForClasses[0]) {
            $allowClass = $meantForClasses[0];
        }
        foreach ($classSpecificClustersForTT as $key => $value) {
            $classes = explode(',', $classSpecificClustersForTT[$key][1]);

            foreach ($classes as $keys => $values) {
                if ($classes[$keys] == $allowClass) {
                    array_push($tmparrayy, $classSpecificClustersForTT[$key]);
                    $game = checkForGame($classSpecificClustersForTT[$key][0], "", "", "", $childClass, 1);
                    $timedTest = checkForTimedTest("", "", $classSpecificClustersForTT[$key][0], "", $childClass, 1);
                    if ($game != -1 && $game != "") {
                        $gameArr = array();
                        array_push($gameArr, $game['gameID']);
                        array_push($gameArr, $game['gameName']);
                        array_push($gameArr, 'activity');
                        if (hasAttemptedGame($userID, $game['gameID'])) {
                            array_push($gameArr, 'attempted');
                        } else {
                            array_push($gameArr, 'NA');
                        }
                        array_push($tmparrayy, $gameArr);
                    }
                    if ($timedTest != -1 && $timedTest != "") {
                        $timedTestArr = array();
                        array_push($timedTestArr, $timedTest['timedTestCode']);
                        array_push($timedTestArr, $timedTest['timedTestName']);
                        array_push($timedTestArr, 'timedTest');
                        if (hasClearedTimedTest($timedTest['timedTestCode'], $classSpecificClustersForTT[$key][0], $userID) == 1) {
                            array_push($timedTestArr, 'passed');
                        } elseif (hasClearedTimedTest($timedTest['timedTestCode'], $classSpecificClustersForTT[$key][0], $userID) == 2) {
                            array_push($timedTestArr, 'failed');
                        } elseif (hasClearedTimedTest($timedTest['timedTestCode'], $classSpecificClustersForTT[$key][0], $userID) == 0) {
                            array_push($timedTestArr, 'NA');
                        }
                        array_push($tmparrayy, $timedTestArr);
                    }
                }

                $tmparrayyName[$classSpecificClustersForTT[$key][0]] = getClusterDetails($classSpecificClustersForTT[$key][0], "", 1);
            }
        }
    }
    $_SESSION['classSpecificClustersForTT'] = $tmparrayy;
    $_SESSION['classSpecificClustersNameForTT'] = $tmparrayyName;
}

function getClusterDetails($clusterCode, $flow, $mode = 0) {
    if ($mode == 1) {
        $clusterName = "";
        $sql = "SELECT cluster FROM adepts_clusterMaster WHERE clusterCode='$clusterCode'";
        $result = mysql_query($sql);
        $line = mysql_fetch_array($result);
        return $line[0];
    } else {
        $clusterDetails = array();
        $sql = "SELECT cluster, remedialCluster, " . $flow . "_level FROM adepts_clusterMaster WHERE clusterCode='$clusterCode'";
        $result = mysql_query($sql);
        if ($row = mysql_fetch_array($result)) {
            $clusterDetails["cluster"] = $row[0];
            $clusterDetails["remedialCluster"] = $row[1];
            $clusterDetails["level"] = $row[2];
        }
        return($clusterDetails);
    }
}

function findCLustersAttendedInTopic($attemptID) {
    $arr = array();
    $sql = "Select clusterCode,clusterAttemptNo,result,attemptType from adepts_teacherTopicClusterStatus where ttAttemptID=$attemptID";
    $result = mysql_query($sql);
    while ($line = mysql_fetch_array($result)) {
        ($line[0] == "") ? array_push($arr, "") : array_push($arr, $line[0]);
        ($line[1] == "") ? array_push($arr, "") : array_push($arr, $line[1]);
        ($line[2] == "") ? array_push($arr, "") : array_push($arr, $line[2]);
    }
    return $arr;
}

function isAtaHigherLevelInTT($objTT, $clusterCode, $childClass) {
    $higherLevel = 0;
    $levelArray = $objTT->getClusterLevel($clusterCode);
    if ($levelArray[0] > $objTT->startingLevel)
        $higherLevel = 1;
    return $higherLevel;
}

function getGreatestOneThirdSDL($clusterCode) {
    $context = isset($_SESSION['country']) ? $_SESSION['country'] : "India";
    $query = "SELECT 	subdifficultylevel, group_concat(qcode) as qcode
			   FROM   	adepts_questions
			   WHERE  	clusterCode='$clusterCode' AND status='3' AND context in ('Global','$context') AND question NOT LIKE '%fracbox%'
			   GROUP BY subdifficultylevel ORDER BY subdifficultylevel";
    $result = mysql_query($query) or die(mysql_error());
    $sdlArray = array();
    $questionsArray = array();
    while ($line = mysql_fetch_array($result)) {
        array_push($sdlArray, $line[0]);
        $questionsArray[$line[0]] = $line[1];
    }
    $factor = 0;
    $highestSDLs = array();

    for ($i = intval($factor); $i < count($sdlArray); $i++) {  //Consider only the greatest 1/3rd of the SDLs.
        $highestSDLs[$sdlArray[$i]] = $questionsArray[$sdlArray[$i]];
    }
	
    return $highestSDLs;
}

function hasAttemptedGame($userID, $gameID) {
    $cleared = 0;
    $query = "SELECT count(*) FROM adepts_userGameDetails WHERE userID=$userID AND gameID=$gameID AND completed<>0";
    $result = mysql_query($query);
    if ($line = mysql_fetch_array($result)) {
        $noOfTimesCleared = $line[0];
        if ($noOfTimesCleared >= 1)
            $cleared = 1;
    }
    return $cleared;
}

function hasClearedTimedTest($timedTestCode, $clusterCode, $userID) {
    $cleared = 0;
    $query = "SELECT a.perCorrect as percent from adepts_timedTestDetails a, adepts_timedTestMaster b where a.timedTestCode=b.timedTestCode and a.timedTestCode='" . $timedTestCode . "' and b.linkedToCluster='" . $clusterCode . "' and a.userid=" . $userID . " order by timedTestID desc limit 1";
    $result = mysql_query($query);
    if ($line = mysql_fetch_array($result)) {
        $timedTestPer = $line[0];
        if ($timedTestPer >= 75)
            $cleared = 1;
        elseif ($timedTestPer < 75)
            $cleared = 2;
    }
    return $cleared;
}
 /*function set_parent_json_array($data)
{
            $data_accesstoken = file_get_contents(BASE_URL."mindspark_product/parent/api/auth/create_access_token/".$data['parentUserID']);
            $data_accesstoken = json_decode($data_accesstoken);
            $json_array = array();
            $tmp = array();
            $tmp['parentUserID'] = $data['parentUserID'];
            $tmp['firstName'] = $data['firstName'];
            $tmp['lastName'] = $data['lastName'];
            $tmp['username'] = $data['username'];
            $tmp['access_token'] = $data_accesstoken->accesstoken;
            $json_array['data'] = $tmp;
            $str = json_encode($json_array);
            $_SESSION['data_set_parent'] = $str;
     
}*/

function setParentSesssionValues($childrenMapped) {
    $childID = array();
    $i = 0;
    foreach ($childrenMapped as $child1) {
        $childID[$i] = $child1['childUserID'];
        $child[$i] = new User( $childID[$i]);
        $childName[$i] = $child[$i]->childName;
        $childFreeTrial[$i] = $child1['freeTrial'];
        if ($i == 0) {
            $childSelected = $child[$i];
            $_SESSION['childID'] = $childSelected->userID;
            $_SESSION['childIDUsed'] = $childSelected->userID;
            $_SESSION['childNameUsed'] = $childSelected->childName;
            $_SESSION['childClassUsed'] = $childSelected->childClass;
            $_SESSION['childSubcategory'] = $childSelected->subcategory;
            $_SESSION['packageExpiryDate'] = $childSelected->endDate;
        }
        $i++;
    }
    $_SESSION['arrChildID'] = $childID;
    $_SESSION['arrChildName'] = $childName;
    $_SESSION['arrChildFreeTrial'] = $childFreeTrial;
}

// function to send mail from forgot password page
function mailPassword($emailID, $password, $username, $childName, $ccList="")
{
	$subject = "Mindspark - Login Details";
	$message = "Dear ".$childName.",<br /><br />";
	$message .= "Your password has been reset, following are your login details:<br /><br />";
	$message .= "Username: ".$username."<br />";
	$message .= "Password: ".$password."<br /><br />";
	$message .= "(Password was changed from: ".$_SERVER['REMOTE_ADDR'].")<br /><br />";
	$message .= "Regards<br />The Mindspark Team";
	
	$headers  = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
	$headers .= "From: Mindspark <mindspark@ei-india.com>" . "\r\n";
	$headers .= "X-Mailer: PHP/" . phpversion();
	$send_mail = mail($emailID, $subject, $message, $headers);
	if($send_mail) {
		insert(9,$emailID,"$ccList","","mindspark@ei-india.com","",1);
	} else {
		insert(9,$emailID,"$ccList","","mindspark@ei-india.com","",0);
	}
}

function mailToTeacherUnlockAccount($teacher_email, $student_name, $username, $locked_date) {
	$subject = "Mindspark - Account Locked";
	$message = "Dear Teacher,<br /><br />";
	$message .= "The following student's Mindspark account is locked. This has resulted after 5 failed attempts in retrieving the picture password in the log-in process.<br /><br />";
	$message .= "<table border='1' cellpadding='5'>";
	$message .= "<tr><td><b>Student Name</b></td><td><b>User ID</b></td><td><b>Account Locked Date</b></td><td><b>Action</b></td></tr>";
	$message .= "<tr>";
	$message .= "<td>".$student_name."</td>";
	$message .= "<td>".$username."</td>";
	$message .= "<td>".date("d M Y h:i A", strtotime($locked_date))."</td>";
	$message .= "<td>1) Allow Access<br />2) No Action</td>";
	$message .= "</tr>";
	$message .= "</table>";
	$message .= "You are requested to select the \"Allow Access\" option in the My Students sections to enable the student to log-in to Mindspark.<br /><br />";
	$message .= "Thank you for your support.<br /><br />";
	$message .= "Regards<br />The Mindspark Team";
	
	$headers  = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
	$headers .= "From: Mindspark <mindspark@ei-india.com>" . "\r\n";
	$headers .= "Bcc: kamlesh.chetnani@ei-india.com, venkateshwarlu.maguloori@ei-india.com, anita.kamath@ei-india.com" . "\r\n";
	//$headers .= "Bcc: kamlesh.chetnani@ei-india.com" . "\r\n";
	$headers .= "X-Mailer: PHP/" . phpversion();
	$send_mail = mail($teacher_email, $subject, $message, $headers);
}

function mailToTeacherResetPassword($teacher_email, $student_name, $username, $requested_date) {
	$subject = "Mindspark - Reset Password Request";
	$message = "Dear Teacher,<br /><br />";
	$message .= "The following student has requested to reset his/her password.<br /><br />";
	$message .= "<table border='1' cellpadding='5'>";
	$message .= "<tr><td><b>Student Name</b></td><td><b>User ID</b></td><td><b>Requested Date</b></td><td><b>Action</b></td></tr>";
	$message .= "<tr>";
	$message .= "<td>".$student_name."</td>";
	$message .= "<td>".$username."</td>";
	$message .= "<td>".date("d M Y h:i A", strtotime($requested_date))."</td>";
	$message .= "<td>1) Reset Password<br />2) No Action</td>";
	$message .= "</tr>";
	$message .= "</table>";
	$message .= "You are requested to select the \"Reset Password\" option in the My Students sections to enable the student to log-in to Mindspark.<br /><br />";
	$message .= "Thank you for your support.<br /><br />";
	$message .= "Regards<br />The Mindspark Team";
	
	$headers  = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
	$headers .= "From: Mindspark <mindspark@ei-india.com>" . "\r\n";
	$headers .= "Bcc: kamlesh.chetnani@ei-india.com, venkateshwarlu.maguloori@ei-india.com, anita.kamath@ei-india.com" . "\r\n";
	//$headers .= "Bcc: kamlesh.chetnani@ei-india.com" . "\r\n";
	$headers .= "X-Mailer: PHP/" . phpversion();
	$send_mail = mail($teacher_email, $subject, $message, $headers);
}

function checkForOfflineURL($schoolCode,$objUser)
{
	if(strpos($_SERVER['HTTP_HOST'],"mindspark.in") !== false || strpos($_SERVER['HTTP_HOST'],"122.248.236.40") !== false || strpos($_SERVER['HTTP_HOST'],"localhost") !== false || strpos($_SERVER['HTTP_HOST'],"192.168.0.94") !== false)
	{
		$sq	=	"SELECT * FROM adepts_offlineSchools WHERE schoolCode=$schoolCode";
		$rs	=	mysql_query($sq);
		if(mysql_num_rows($rs)>0)
		{
			if($_SESSION['offlineURL']=="1")
			{
				if(strcasecmp($objUser->category, "STUDENT") == 0)
				{
					echo '<script src="../userInterface/libs/jquery.js"></script>
					<script type="text/javascript" src="../userInterface/libs/jquery-ui-1.8.16.custom.min.js"></script>
					<script src="../userInterface/libs/jquery.colorbox-min.js" type="text/javascript"></script>
					<link href="../userInterface/css/login/login.css?ver=11" rel="stylesheet" type="text/css">
					<link rel="stylesheet" type="text/css" href="../userInterface/css/colorbox.css">
					<script type="text/javascript">
					function redierectUser()
					{
						$.fn.colorbox({"href":"#moveToOfflineMessage","inline":true,"open":true,"escKey":false,"overlayClose": false, "height":310, "width":510});
						$("#cboxClose").remove();
					}
					function redirectTooffline()
					{
						window.location.href = "http://mindsparkserver/mindspark/login/";
						$("#moveToOfflineMessage").remove();
					}
					';
					echo '</script><div style="display:none">
							<div id="moveToOfflineMessage" style="padding:15px;font-family:Conv_HelveticaLTStd-LightCond;">
								<div>
									<h2 align="center">Welcome to Mindspark!</h2>
									<p><b>Hold on a second... You seem to be logging in from school, but into the Mindspark internet server. You need to log in to the Mindspark School Server, by clicking <a href="javascript:void(0)" onclick="redirectTooffline()">here</a>..</b></p>
								</div>
								<br>
								<div onclick="redirectTooffline()" style="display:block; border-style:solid; border-color:#bbb #888 #666 #aaa; border-width:3px 4px 4px 3px; width:9em; height:2em; background:#ccc; color:#333; line-height:2; text-align:center; text-decoration:none; font-weight:900; cursor:pointer; margin-left:150px">Continue</div>
							</div>
						</div>
		<script>redierectUser();</script></body></html>';
					
					exit;
				}
				else
				{
					unset($_SESSION['offlineURL']);
					$_SESSION["offlineActive"] = 1;
				}
			}
		}
	}
}

?>