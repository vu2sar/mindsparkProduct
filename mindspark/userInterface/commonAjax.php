<?php
include("check1.php");
include_once("constants.php");
include("functions/functions.php");
include("classes/clsUser.php");
include_once("classes/clsTopicProgress.php");
$mode = isset($_POST["mode"])?$_POST["mode"]:"";
if($mode != '' && $mode != 'filterAQADByDate' && $mode != 'ReloadAQADAfterUserAnswer'){
	if(!isset($_SESSION['userID']) && isset($_POST))
	{
		header("Location:error.php");
		exit();
	}	
}

$userID = $_SESSION['userID'];
 
switch ($mode)
{
    case "checkPendingTimedTest":
    	$timedTestArray		= array();
		$rctFlag=checkForRCT($userID);		
		if(!$rctFlag)
		{			
			$sq	=	"SELECT pendingTopicTimedTest FROM adepts_timedTestStatus WHERE userID=$userID";
			$rs	=	mysql_query($sq);
			$rw	=	mysql_fetch_array($rs);
			if($rw[0]!="")
			{
				$ttArray	=	explode(",",$rw[0]);
				foreach($ttArray as $ttDetails)
				{
					$allTimedTest	=	explode("~",$ttDetails);
					$sqAttempt		=	"SELECT max(attemptedDate) FROM adepts_timedTestDetails WHERE userID=$userID AND timedTestCode='".$allTimedTest[1]."'";
					$result			=	mysql_query($sqAttempt);
					$ttflag = 1;
					if($line=mysql_fetch_array($result))
					{
						$lastAttemptedDate	=	$line[0];
						$daysLoggedIn	=	getNoOfDaysLoggedIn($userID,$lastAttemptedDate);
						if($daysLoggedIn > 1)
							$ttflag = 1;
						else
							$ttflag = 0;
					}
					if($ttflag==1)
					{
						$timedTestArray[$allTimedTest[0]]	=	$allTimedTest[1];
					}
				}
			}
			mysql_close();
		}
		echo json_encode($timedTestArray);
		break;
		
	case "downLoadCount":
		$summuryID	=	$_POST['summuryID'];
		$sq	=	"UPDATE adepts_summarySheets SET downloadCount=downloadCount+1 WHERE summuryID=$summuryID";
		mysql_query($sq);
		mysql_close();
		
		break;
		
	case "examCorner":
	
		
		if($_POST["userType"]=="teacherAsStudent")
		{
			$_SESSION["userType"]=="teacherAsStudent";
			$_SESSION["childClass"] = (isset($_POST['childClass']) && $_POST['childClass'] != "")?$_POST['childClass']:"";
			
			mysql_query("UPDATE adepts_userDetails SET childClass=".$_SESSION["childClass"]." WHERE userID=".$_SESSION["userID"]);
			
			mysql_query("UPDATE educatio_educat.common_user_details SET class=".$_SESSION["childClass"]." WHERE MS_userID=".$_SESSION["userID"]);
			$commentBy	=	'Teacher';
			header("Location: examCorner.php");

			
		}
		break;
			
	case "shareParentEmailIds":
	
		include("ses.php");
		
		if($_POST["emailids"])
		{
			$emailids	=	$_POST["emailids"];
			$userID = $_SESSION['userID'];
			$randString	=	RandomString();
			$randString = md5($randString.$userID);
			
			$fist_name = ucwords(trim($_SESSION['childName']));
			$temp_arr = explode(" ", $fist_name);
			$fist_name = trim($temp_arr[0]);
			
			$awsAccessKey = AWS_ACCESS_KEY;
			$awsSecretKey = AWS_SECRET_KEY;
			
			$con = new SimpleEmailService($awsAccessKey,$awsSecretKey);
			
			$m = new SimpleEmailServiceMessage();
			
			$link	=	"https://www.mindspark.in/mindspark/userInterface/mindsparkMailVarification.php?verify=".$randString;
			
			// prepare mail content
			$mail_content = "<html>";
			$mail_content .= "<body style='font-family: Arial, sans-serif; font-size: 13px;'>";
			$mail_content .= "Dear Parent,<br /><br />";
			$mail_content .= "Welcome to Mindspark!<br /><br />";
			$mail_content .= ucwords(trim($_SESSION['childName']))." has shared your email ID with us. ".$fist_name." is an active user of Mindspark.<br /><br />";
			$mail_content .= "The new Mindspark Parent Connect allows parents to see what their children are doing in Mindspark. The Parent Connect is easily accessible to those parents whose email IDs have been registered with Mindspark.<br /><br />";
			$mail_content .= "Registered parent email IDs also make it easier for teachers to communicate directly with parents, through the \"Mail Parents\" option on the Mindspark Teacher Interface.<br /><br />";
			$mail_content .= "[Mindspark treats all personal information including email IDs as privileged information. It will be used solely for mailing Mindspark related communications.]<br /><br />";
			$mail_content .= "<b>Please click <a href='".$link."'>here</a> to verify your e-mail ID.</b><br /><br />";
			$mail_content .= "Regards,<br />Team Mindspark";
			$mail_content .= "</body></html>";
			
			$to = $emailids;
			$subject = "Mindspark email verification";
			
			$m->addTo($to);
			$m->setFrom("mindspark@ei-india.com");
			$m->addReplyTo("mindspark@ei-india.com");
			$m->setSubject($subject);
			$m->setMessageFromString("", $mail_content);
			$response = $con->sendEmail($m);
			
			if($response === false)
				echo "0";
			else {
				$update_entry = "UPDATE adepts_parentEmailNotification SET added_on = NOW(), email = '".mysql_real_escape_string($emailids)."', 
					verification_code = '".mysql_real_escape_string($randString)."', verified = 0, verified_on = '0000-00-00 00:00:00' 
					WHERE userID = ".mysql_real_escape_string($userID);
				$exec_entry = mysql_query($update_entry);
				
				echo "1";
			}
			
			usleep(50000);
			
		}
		break;
		
	case "remindParentEmailIds":
		
		$userID = $_SESSION['userID'];
		
		$update_entry = "UPDATE adepts_parentEmailNotification SET reminder_count = 1 WHERE userID = ".mysql_real_escape_string($userID);
		$exec_entry = mysql_query($update_entry);
		
		echo "1";
		break;
		
	case "summerFeedback":
		$userID = $_SESSION['userID'];
		$feedBack	=	$_POST['feedBack'];
		$query = "INSERT INTO adepts_summerProgramFeedback SET userID=$userID,feedback='".$feedBack."'";
		$result = mysql_query($query) or die("Error : ".mysql_error());
		break;
	case "filterAQADByDate":
		include_once  'classes/clsUser.php';
		$classTobeFilter = $_POST['classTobeFilter'];
		$dateTobeFilter = $_POST['dateTobeFilter'];
		$classSectionArray = $_POST['classSectionArray'];
		$classSectionArray = str_replace('\"', '"', $classSectionArray);
		$isTeacher = $_POST['isTeacher'];
		$schoolCode = $_POST['schoolCode'];
		$userResponse = $_POST['userResponse'];
		mysql_select_db("educatio_educat");
		require_once 'eiaqad.cls.php';
		if($allClasses != NULL){
			$allClasses = explode("-",$allClasses);
		}
		echo generateAQADtemplate ($dateTobeFilter,$classTobeFilter,$userResponse,0,$isTeacher,"$classSectionArray",$schoolCode);
		break;
	case "ReloadAQADAfterUserAnswer" :
		include_once  'classes/clsUser.php';
		include_once  'eiaqad.cls.php';
		$startdateToday = date("Y-m-d");
		$childClass = $_POST["class"];
		$userAnswer = $_POST["student_answer"];
		$userID = $_POST["studentID"];
		mysql_select_db("educatio_educat");
		echo generateAQADtemplate ( $startdateToday, $childClass, $userAnswer, $userID ,0);
		break;
	case "getQuestionBySDL":
		include_once'functions/orig2htm.php';
		include_once 'classes/clsQuestion.php';
		include_once'functions/functionsForDynamicQues.php';
		$clusterCode = $_POST["clusterCode"];
		$subDifficultyLevel = $_POST["subDifficultyLevel"];
		$ttcode = $_POST["ttcode"];
		$misconceptoinString = $_POST['misconceptoinString'];
		$misconceptoinString = base64_decode($misconceptoinString);
		$classDetail = $_POST["classDetail"];
		$flow = $_POST["flow"];
		$question     = new Question("",1,$subDifficultyLevel,$clusterCode);
		$question->generateQuestion();
		$questionType = $question->quesType;
		if($misconceptoinString != ''){
			echo '<img class="question-flag" alt="'.$misconceptoinString.'" src="assets/redflag.ico" style="float:right;height:20px;" title="'.$misconceptoinString.'">';
		}
		?>
		<table width='100%' border=0 cellspacing=0 style="float:left;">
	            <tr>
	                <td align='left'><?echo str_replace("\over", "/", $question->getQuestion()); ?><br/></td>
	            </tr>
		<?php
	    if($questionType=='MCQ-4' || $questionType=='MCQ-3' || $questionType=='MCQ-2')    {
		?>
	            <tr  bgcolor="">
	                <td>
	                <table width="100%" border="0" cellspacing="2" cellpadding="3">

	    <?php     if($questionType=='MCQ-4' || $questionType=='MCQ-2')    {    ?>
	                <tr valign="top">
	                      <td width="5%"  class="orangeBorder" nowrap align="center" ><b>A</b></td>
	                    <td width="45%" class="orangeBorder"><?php echo str_replace("\over", "/", $question->getOptionA());?></td>
	                    <td width="5%"  class="orangeBorder" nowrap align="center" ><b>B</b></td>
	                    <td width="45%" class="orangeBorder"><?php echo str_replace("\over", "/", $question->getOptionB());?></td>
	                </tr>
	    <?php    }    ?>
	    <?php    if($questionType=='MCQ-4')    {    ?>
	                <tr valign="top">
	                    <td width="5%"  class="orangeBorder" align="center"><b>C</b></td>
	                    <td width="45%" class="orangeBorder"><?php echo str_replace("\over", "/", $question->getOptionC());?></td>
	                    <td width="5%"  class="orangeBorder" align="center"><b>D</b></td>
	                    <td width="45%" class="orangeBorder"><?php echo str_replace("\over", "/", $question->getOptionD());?></td>
	                </tr>
	    <?php    }    ?>
	    <?php    if($questionType=='MCQ-3')    {    ?>
	                <tr valign="top">
	                    <td width="3%"  class="orangeBorder" nowrap align="center"><b>A</b></td>
	                      <td width="30%" class="orangeBorder"><?php echo str_replace("\over", "/", $question->getOptionA());?></td>
	                      <td width="3%"  class="orangeBorder" nowrap align="center"><b>B</b></td>
	                      <td width="30%" class="orangeBorder"><?php echo str_replace("\over", "/", $question->getOptionB());?></td>
	                      <td width="3%"  class="orangeBorder" nowrap align="center"><b>C</b></td>
	                      <td width="30%" class="orangeBorder"><?php echo str_replace("\over", "/", $question->getOptionC());?></td>

	                </tr>
	            <?php    }    ?>
	                  </table>
	                </td>
	            </tr>

			<?php }    ?>
	        </table>
	        <div style="float: left; width: 100%; margin-top: 40px;"><a href="sampleQuestions.php?ttCode=<?=$ttcode;?>&learningunit=<?=$clusterCode;?>&cls=<?=$classDetail?>&flow=<?=$flow?>" target="_blank" >More >></a></div>
		<?php 
		exit;		
		break;
		
	case "createDdContent":
	
		$practiseModuleId = $_POST['practiseModuleId'];
		$status = $_POST['status'];
		$testId = $_POST['testId'];
		$firstLoginToday=isUserFirstTimeLoggedInToday($userID);
		$q="SELECT * FROM practiseModulesTestStatus WHERE lastModified>CURDATE() AND userID=$userID";
		$r=mysql_query($q) or die(mysql_error().$q);
		if ($firstLoginToday && mysql_num_rows($r)>0) 
			$firstLoginToday=0;


		unset($_SESSION['dailyDrillArray']);
		$dailyDrillForDayCompleted=array();
		$insertRecord = 0;
		if (isset($_POST['forDailyDrill'])){
			$selectRecordSQL = "SELECT id, status FROM practiseModulesTestStatus where lastModified>CURDATE() AND status='completed' AND userID=".$_SESSION['userID']." ORDER BY id DESC";
			$selectRecordQuery = mysql_query($selectRecordSQL) or die (mysql_error().$selectRecordSQL);
			if (mysql_num_rows($selectRecordQuery)>0){
				$dailyDrillForDayCompleted['dailyDrillForDayCompleted']=1;
				print_r(json_encode($dailyDrillForDayCompleted));exit;
			}
			$selectRecordSQL = "SELECT id, status FROM practiseModulesTestStatus where practiseModuleId='$practiseModuleId' AND userID=".$_SESSION['userID']." ORDER BY id DESC";
			$selectRecordQuery = mysql_query($selectRecordSQL) or die (mysql_error().$selectRecordSQL);
			$status="";
			if (mysql_num_rows($selectRecordQuery)>0){
				$selectRecordResult = mysql_fetch_row($selectRecordQuery);
				$status=$selectRecordResult[1];
				$testId=$selectRecordResult[0];
			}
		}
		if($status == ""){
			$insertRecord = 1;
			$attemptNo = 1;
		}else if($status == "in-progress"){
			$attemptNo = 1;
			
			$practiseModuleTestStatusId = $testId;
			$selectRecordSQL = "SELECT remainingTime,score,attemptNo,currentLevel FROM practiseModulesTestStatus where id = $testId AND practiseModuleId='$practiseModuleId'";
			$selectRecordQuery = mysql_query($selectRecordSQL) or die (mysql_error().$selectRecordSQL);
			$selectRecordResult = mysql_fetch_row($selectRecordQuery);
			$remainingTime = $selectRecordResult[0];
			$score = $selectRecordResult[1];
			$attemptNo = $selectRecordResult[2];
			$currentLevel = $selectRecordResult[3];
			
			$currentScore = $score;
			
		}else if($status == "completed"){
			// get last attempt No.
			$attemptNoSQL = "SELECT MAX(f.attemptNo) FROM practiseModulesTestStatus f where f.practiseModuleId = '$practiseModuleId' AND userID = $userID";
			$attemptNoQuery = mysql_query($attemptNoSQL) or die(mysql_error().$attemptNoSQL);
			$attemptNoResult = mysql_fetch_row($attemptNoQuery);
			$attemptNo = $attemptNoResult[0] + 1;
			$insertRecord = 1;
		}
		
		if($insertRecord == 1){
			// Insert New Record
			$insertSQL = "INSERT INTO practiseModulesTestStatus (userID,status,remainingTime,lastAttemptQue,practiseModuleId,score,attemptNo, currentLevel) VALUES ($userID, 'in-progress',300,0,'$practiseModuleId',0,$attemptNo,1)";
			mysql_query($insertSQL) or die(mysql_error().$insertSQL);
			$practiseModuleTestStatusId = mysql_insert_id();
			
			
			$currentLevel = 1;
			$currentScore = 0;
			// remainingTime not in use for now until we implement Daily Drill.
			$remainingTime = 300;
		}
		if (isset($_POST['forDailyDrill'])){
			if($firstLoginToday){
				$remainingTime=300;
				$query="UPDATE practiseModulesTestStatus SET remainingTime=300 WHERE id=$practiseModuleTestStatusId";
				mysql_query($query) or die(mysql_error().$query);
			}
			else if ($remainingTime<30) {
				$dailyDrillForDayCompleted['dailyDrillForDayCompleted']=1;
				print_r(json_encode($dailyDrillForDayCompleted));exit;
			}
		}
		$_SESSION['dailyDrillArray']['remainingTime'] = $remainingTime;
		$_SESSION['dailyDrillArray']['isInternalRequest'] = isset($_SESSION['msAsStudent'])?$_SESSION['msAsStudent']:0;
		$_SESSION['dailyDrillArray']['practiseModuleTestStatusId'] = $practiseModuleTestStatusId;
		// add all values to session.
		
		//$DDsql = "SELECT numberAtLevel1,level1_qCodes,numberAtLevel2,level2_qCodes,numberAtLevel3,level3_qCodes,timedTestCode, linkedToCluster as clusterCode FROM practiseModules WHERE practiseModuleId = '$practiseModuleId'";
		$DDsql = "SELECT description, linkedToCluster, numberOfLevels, dailyDrill FROM practiseModuleDetails WHERE practiseModuleId = '$practiseModuleId'";
		$resultSet = mysql_query($DDsql) or die(mysql_error().$DDsql);
		while($rw = mysql_fetch_array($resultSet,MYSQL_ASSOC)){
			$_SESSION['dailyDrillArray']['isDailyDrill']=$rw['dailyDrill'];
			$_SESSION['dailyDrillArray']['clusterCode'] = $rw['linkedToCluster'];
			$_SESSION['dailyDrillArray']['practiseModuleId'] = $practiseModuleId;
			$_SESSION['dailyDrillArray']['description'] = $rw['description'];
			$_SESSION['dailyDrillArray']['numberOfLevels'] = $rw['numberOfLevels'];
		}
		$DDsql = "SELECT SUM(IF(qCodes!='',1,0)) qLevels, SUM(IF(timedTest!='',1,0)) tLevels FROM practiseModuleLevels WHERE practiseModuleId = '$practiseModuleId'";
		$resultSet = mysql_query($DDsql) or die(mysql_error().$DDsql);
		while($rw = mysql_fetch_array($resultSet,MYSQL_ASSOC)){
			$attemptPer=($attemptNo>6)?0:(6-$attemptNo)*2/10;
			$_SESSION['dailyDrillArray']['canSparkies']	= round(($rw['tLevels']*5+5)*$attemptPer);
		}
		$query="UPDATE practiseModulesTestStatus SET lastModified=NOW() WHERE id=$practiseModuleTestStatusId";
		mysql_query($query) or die(mysql_error().$query);
		$_SESSION['dailyDrillArray']['currentLevel'] = $currentLevel;
		$_SESSION['dailyDrillArray']['currentScore'] = $currentScore;
		
		$_SESSION['dailyDrillArray']['attemptNo'] = $attemptNo;
		print_r(json_encode($_SESSION['dailyDrillArray']));
		// check user attainted questions code ends
		
		break;  
		
	case "setAqadSession":
		$url = AQAD_URL."aqadAjax.php";
		$sq = "SELECT password FROM adepts_userDetails WHERE userID=".$userID;
		$rs = mysql_query($sq);
		$rw = mysql_fetch_array($rs);
		$response = cURL($url,1,null,"username=".$_SESSION['username']."&password=".$rw[0]."&func=checkLogin&origin=mindspark");
		preg_match('/^Set-Cookie: (.*?);/m', $response, $m);
		if(!isset($m[1]) || $m[1] == "") die("Could not initiate session.");
		$msSessionCookie = explode("=",$m[1]);
		$msSessionCookie = $msSessionCookie[1];
		$_SESSION['msSessionCookie'] = $msSessionCookie;
		//$strCookie = 'PHPSESSID=' . $_SESSION['msSessionCookie'] . '; path=/';
		echo $_SESSION['msSessionCookie'];
		break;

	case "checkForPractiseTopicPage":
		$practiceModuleId = $_POST['practiseModuleId'];					
		isPractiseTimeTopicPage($practiceModuleId);		
		break;			
		
}

function isPractiseTimeTopicPage($pmID)
{
		$userID = $_SESSION['userID'];
		global $dailyDrillAvailableToday;
		$practiseModuleId = $pmID;
		$status = "";
		$testId = "";				

		unset($_SESSION['dailyDrillArray']);
		
		$selectRecordSQL = "SELECT id, status FROM practiseModulesTestStatus where practiseModuleId='$practiseModuleId' AND userID=".$_SESSION['userID']." ORDER BY id DESC";
		$selectRecordQuery = mysql_query($selectRecordSQL) or die (mysql_error().$selectRecordSQL);
		$status="";
		if (mysql_num_rows($selectRecordQuery)>0){
			$selectRecordResult = mysql_fetch_row($selectRecordQuery);
			$status=$selectRecordResult[1];
			$testId=$selectRecordResult[0];
		}	
		if($status == ""){
			$insertRecord = 1;
			$attemptNo = 1;
		}else if($status == "in-progress"){
			$attemptNo = 1;
			$practiseModuleTestStatusId = $testId;
			$selectRecordSQL = "SELECT remainingTime,score,attemptNo,currentLevel FROM practiseModulesTestStatus where id = $testId AND practiseModuleId='$practiseModuleId'";
			$selectRecordQuery = mysql_query($selectRecordSQL) or die (mysql_error().$selectRecordSQL);
			$selectRecordResult = mysql_fetch_row($selectRecordQuery);
			$remainingTime = $selectRecordResult[0];
			$score = $selectRecordResult[1];
			$attemptNo = $selectRecordResult[2];
			$currentLevel = $selectRecordResult[3];
			$currentScore = $score;
			
		}else if($status == "completed"){
			// get last attempt No.
			$attemptNoSQL = "SELECT MAX(f.attemptNo) FROM practiseModulesTestStatus f where f.practiseModuleId = '$practiseModuleId' AND userID = $userID";
			$attemptNoQuery = mysql_query($attemptNoSQL) or die(mysql_error().$attemptNoSQL);
			$attemptNoResult = mysql_fetch_row($attemptNoQuery);
			$attemptNo = $attemptNoResult[0] + 1;
			$insertRecord = 1;
		}
	
		if($insertRecord == 1){
			// Insert New Record
			$insertSQL = "INSERT INTO practiseModulesTestStatus (userID,status,remainingTime,lastAttemptQue,practiseModuleId,score,attemptNo, currentLevel) VALUES ($userID, 'in-progress',300,0,'$practiseModuleId',0,$attemptNo,1)";
			mysql_query($insertSQL) or die(mysql_error().$insertSQL);
			$practiseModuleTestStatusId = mysql_insert_id();
			$currentLevel = 1;
			$currentScore = 0;
			// remainingTime not in use for now until we implement Daily Drill.
			$remainingTime = 300;
		}
		
			

		$_SESSION['dailyDrillArray']['remainingTime'] = $remainingTime;
		$_SESSION['dailyDrillArray']['isInternalRequest'] = isset($_SESSION['msAsStudent'])?$_SESSION['msAsStudent']:0;
		$_SESSION['dailyDrillArray']['practiseModuleTestStatusId'] = $practiseModuleTestStatusId;
		// add all values to session.
		
		$DDsql = "SELECT description, linkedToCluster, numberOfLevels, dailyDrill FROM practiseModuleDetails WHERE practiseModuleId = '$practiseModuleId'";
		$resultSet = mysql_query($DDsql) or die(mysql_error().$DDsql);
		while($rw = mysql_fetch_array($resultSet,MYSQL_ASSOC)){
			$_SESSION['dailyDrillArray']['isDailyDrill']=$rw['dailyDrill'];
			$_SESSION['dailyDrillArray']['clusterCode'] = $rw['linkedToCluster'];
			$_SESSION['dailyDrillArray']['practiseModuleId'] = $practiseModuleId;
			$_SESSION['dailyDrillArray']['description'] = $rw['description'];
			$_SESSION['dailyDrillArray']['numberOfLevels'] = $rw['numberOfLevels'];
		}
		$DDsql = "SELECT SUM(IF(qCodes!='',1,0)) qLevels, SUM(IF(timedTest!='',1,0)) tLevels FROM practiseModuleLevels WHERE practiseModuleId = '$practiseModuleId'";
		$resultSet = mysql_query($DDsql) or die(mysql_error().$DDsql);
		while($rw = mysql_fetch_array($resultSet,MYSQL_ASSOC)){
			$attemptPer=($attemptNo>6)?0:(6-$attemptNo)*2/10;
			$_SESSION['dailyDrillArray']['canSparkies']	= round(($rw['tLevels']*5+5)*$attemptPer);
		}
		$query="UPDATE practiseModulesTestStatus SET lastModified=NOW() WHERE id=$practiseModuleTestStatusId";
		mysql_query($query) or die(mysql_error().$query);
		$_SESSION['dailyDrillArray']['currentLevel'] = $currentLevel;
		$_SESSION['dailyDrillArray']['currentScore'] = $currentScore;
		
		$_SESSION['dailyDrillArray']['attemptNo'] = $attemptNo;
		
		$dailyDrillAvailableToday=array('pmArray'=>$_SESSION['dailyDrillArray']);
		$practiseModuleArray['pmArray'] = json_encode($dailyDrillAvailableToday['pmArray']);		
		echo json_encode($practiseModuleArray);
		exit;
	}
function RandomString()
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randstring = '';
    for ($i = 0; $i < 10; $i++) {
        $randstring .= $characters[rand(0, strlen($characters))];
    }
	$randstring .= date("Ymdhis");
    return $randstring;
}
function calculateAccuracy($Class,$schoolCode,$currentDateToFilter,$currenSection){
	$sql = "SELECT  COUNT(ud.userID) AS totalStudents,COUNT(aqad.id) AS attendedStudents, SUM(aqad.score) AS correctAnsStudents FROM educatio_adepts.adepts_userDetails ud LEFT JOIN educatio_educat.aqad_responses aqad ON ud.userID = aqad.studentId AND DATE(aqad.entered_date)= '$currentDateToFilter' WHERE ud.category = 'STUDENT' AND ud.childClass = $Class AND ud.schoolCode=$schoolCode AND ud.enabled=1 AND ud.endDate>=curDate()";
	if($currenSection != ''){
		$currenSection = str_replace("$", "','", $currenSection);
		$sql .= " AND ud.childSection IN ('$currenSection')";
	}
// 	echo $sql;
	$query = mysql_query($sql);
	$result = mysql_fetch_assoc($query);
	if($result['correctAnsStudents'] != NULL){
		$accuracy = round($result['correctAnsStudents']/$result['attendedStudents']*100);
	}
	else{
		$accuracy = 0;
	}
	return "<div style='width: 650px; margin-left:10px;'><span style=' font-weight: bold;'>".$result['attendedStudents']." Out of ".$result['totalStudents']." students attempted this question with an average accuracy of ".$accuracy ." %.</span></div>";
}

function cURL($url,$header,$cookie,$p) {
	if(!$header)
		session_write_close();
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, $header);
	curl_setopt($ch, CURLOPT_NOBODY, $header);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

	if($p){
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $p);
	};

	$result = curl_exec($ch);
	curl_close($ch);

	if($result){
		return $result;

	}else{

	};
};
?>