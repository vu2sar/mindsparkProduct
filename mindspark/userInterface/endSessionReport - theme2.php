<?php

include("check1.php");
include("constants.php");
include("classes/clsUser.php");
include ("functions/functionsForDynamicQues.php");
include ("functions/functions.php");
include("classes/clsQuestion.php");
include("functions/orig2htm.php");
include_once("functions/next_question.php");
include("classes/clsDiagnosticTestQuestion.php");	
if(!isset($_SESSION['userID']))
{
	header("Location:error.php");
	exit;
}
$userID = $_SESSION['userID'];
//error_reporting(E_ERROR);
$user = new User($userID);
$Name = explode(" ", $user->childName);
$Name = $Name[0];

$childName 	   = $user->childName;
$schoolCode    = $user->schoolCode;
$childClass    = $user->childClass;
$childSection  = $user->childSection;
$category 	   = $user->category;
$subcategory   = $user->subcategory;
$endDate 	   = $user->endDate;
$_SESSION['questionType'] = "normal";

//echo $mode;
//$_SESSION['total_questions']=$_SESSION['maxQues'];

$today = date("Y-m-d");
$ttAttemptID = $_SESSION['teacherTopicAttemptID'];

if(isset($_POST['sessionID']))
	$sessionID = $_POST['sessionID'];
else
	$sessionID	= $_SESSION['sessionID'];

if(!isset($mode))
	$mode = "";
if($mode=="login")
	$mode = $_GET["mode"];
?>

<?php include("header.php"); ?>

<title>End Session Report</title>

<?php
	if($theme==1) { ?>
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/endSesssionReport/lowerClass.css" rel="stylesheet" type="text/css">
<?php } else if($theme==2) { ?>
    <link rel="stylesheet" href="css/commonMidClass.css" />
    <link rel="stylesheet" href="css/endSesssionReport/midClass.css" />
	<link href="css/home/prompt2.css" rel="stylesheet" type="text/css">
<?php } else { ?>
    <link rel="stylesheet" href="css/commonHigherClass.css" />
    <link rel="stylesheet" href="css/endSesssionReport/higherClass.css" />
	<link href="css/home/prompt2.css" rel="stylesheet" type="text/css">
<?php } ?>
<link rel="stylesheet" type="text/css" href="css/colorbox.css">
<script src="libs/jquery.js"></script>
<script src="libs/jquery.colorbox-min.js" type="text/javascript"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script>

var langType = '<?=$language;?>';
var click=0;

function load() {

<?php if($theme==1) { ?>
	var a= window.innerHeight - (47 + 70 + 55 + 30);
	$('#endSessionDataDivMain').css("height",a+"px");
	$(".forHigherOnly").remove();
<?php } else if($theme==2) { ?>
	var a= window.innerHeight - (80 + 25 + 140 );
	$('#endSessionDataDivMain').css("height",a+"px");
	$(".forLowerOnly").remove();
<?php } else if($theme==3) { ?>
			var a= window.innerHeight - (170);
			var b= window.innerHeight - (610);
			$('#dataTableDiv').css({"height":a+"px"});
			$('#sideBar').css({"height":a+"px"});
			$('#main_bar').css({"height":a+"px"});
			$('#menubar').css({"height":a+"px"});
		<?php } ?>
<?php //if(showSurveyAlert($userID,$_SESSION['sessionID']))	{ ?>
	//openFeedback();
<?php //} ?>
if(androidVersionCheck==1){
			$('#dataTableDiv').css("height","auto");
			$('#endSessionDataDivMain').css("height","auto");
			$('#main_bar').css("height",$('#endSessionDataDivMain').css("height"));
			$('#menu_bar').css("height",$('#endSessionDataDivMain').css("height"));
			$('#sideBar').css("height",$('#endSessionDataDivMain').css("height"));
		}
}
function showQues(qcode, qno, srno)	{
	document.getElementById("hidqcode").value = qcode;
	document.getElementById("hidqno").value = qno;
	document.getElementById("hidsrno").value = srno;
	document.getElementById("frmReport").submit();
}
function showPracticeQues(qcode, qno, srno)	{
	document.getElementById("hidqcode").value = qcode;
	document.getElementById("hidqno").value = qno;
	document.getElementById("hidsrno").value = srno;
	document.getElementById("mode").value = "topicRevision";
	document.getElementById("frmReport").submit();
}
function showPrevComments()
{
	window.location = "viewComments.php?from=links&mode=1";
}
function logoff()
{
	window.location="logout.php";
}
function getHome()
{
	window.location.href	=	"controller.php?mode=topicSwitch&from=endSession";
}

function renew(userID){
	window.open("http://mindspark.in/registration.php?userID="+userID,"_newtab");
}
function openMainBar(){
	if(click==0){
		$("#main_bar").animate({'width':'245px'},600);
		$("#plus").animate({'margin-left':'227px'},600);
		$("#vertical").css("display","none");
		click=1;
	}
	else if(click==1){
		$("#main_bar").animate({'width':'26px'},600);
		$("#plus").animate({'margin-left':'7px'},600);
		$("#vertical").css("display","block");
		click=0;
	}
}
function openFeedback()
{
	$.fn.colorbox({'href':'#sendFeedback','inline':true,'open':true,'escKey':true, 'height':350, 'width':500});
}
</script>
<style>
#feedbakText{
	margin-top:50px;
	font-size:18px;
	padding-left:50px;
	padding-right:50px;
}
.buttonTemp1 {
	background-color:transparent;
	-moz-border-radius:2px;
	-webkit-border-radius:2px;
	border-radius:2px;
	border:1px solid #2f99cb;
	display:inline-block;
	color:#2f99cb;
	font-size:1.1em;
	margin-top:10px;
	margin-left:70px;
	padding:6px 24px;
	text-decoration:none;
	cursor:pointer;
}.buttonTemp1:active {
	position:relative;
	top:4px;
	cursor:pointer;
}
<?php	if(isset($_SESSION['examCornerCluster']) && $_SESSION['examCornerCluster']==1)
		{ ?>
#top_bar {
    background: none repeat scroll 0 0 #9EC956 !important;
}
#dashboard {
    background-color: #9EC956 !important;
}
#dashboardIcon {
    background: url("assets/higherClass/landingScreen/examcorner.png") repeat scroll 0 0 transparent !important;
	margin-left: 24px !important;
}
.arrow-right {
    border-left: 15px solid #9EC956 !important;
}
#nameIcon {
	border: 2px solid #9EC956 !important;
}
#cssmenu a {
	color: #9EC956 !important;
}
#infoBarLeft {
	color: #9EC956 !important;
}
<?php } ?>
</style>
</head>
<?php if($_SESSION['rewardSystem']==1 && $_SESSION['sessionID']==$sessionID) include("prompt2.php");?>
<body class="translation" onLoad="load()" onResize="load()" style="overflow-x:hidden;overflow-y:auto;" >
	<div id="top_bar">
		<div class="logo">
		</div>

        <div id="studentInfoLowerClass" class="forLowerOnly hidden">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span><?=$Name?>&nbsp;</span></a></li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.classSmall">Class</span> <span id="userClass"><?=$childClass.$childSection?></span></div>
            </div>
        </div>
		<div id="studentInfoLowerClass" class="forHighestOnly">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?=$Name?>&nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a href='myDetailsPage.php'><span data-i18n="homePage.myDetails"></span></a></li>
									<li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li>
									<li><a href='changePassword.php'><span data-i18n="homePage.changePassword"></span></a></li>
                                    <li><a href='whatsNew.php'><span data-i18n="common.whatsNew"></span></a></li>
                                    <li><a href='javascript:void(0)' onClick="logoff()"><span data-i18n="common.logout"></span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?=$childClass.$childSection?></span></div>
            </div>
        </div>
        <div id="help" style="visibility:hidden">
        	<div class="help"></div>
            <div class="helpText" data-i18n="common.help"></div>
        </div>
        <div id="logout" onClick="logoff()" class="hidden">
        	<div class="logout"></div>
        	<div class="logoutText" data-i18n="common.logout"></div>
        </div>
		<div id="whatsNew" style="visibility:hidden">
            <div class="whatsNew"></div>
            <div class="whatsNewText" data-i18n="common.whatsNew"></div>
        </div>
    </div>

	<div id="container">
<?php
	if(($mode== -2 || $mode==-3) && $theme!=3) {
		  $msg = "";
		  if($mode == -2)
				$msg = "You have been trying questions from the topic: <strong>".$_SESSION['teacherTopicName']."</strong>. Click <a href=\"dashboard.php\">here</a> to restart this topic or select another topic";
		  elseif ($mode == -3)
				$msg = "<br/>Congratulations! You have successfully completed the topic <strong>".$_SESSION['teacherTopicName']."</strong>! <br/>Click <a href=\"dashboard.php\">here</a> to restart this topic or select another topic";

		echo "<div align='center' class='msg'>$msg</div>";
	}
	if($mode!=-5 && $mode!=-6 && $mode!=-7 && $mode!="refresh") 	{ //Show this link if the daily/weekly/session limit has not been completed
		//pending for showing home button or not
			/*echo	'<a href="controller.php?mode=topicSwitch" class="home bcrumb"></a>
					<div class="separator"></div>
					<a href="endSessionReport.php" class="scorecarded bcrumb"></a>';*/
		$deactiveLinks=0;
	}
	else
	{
		$deactiveLinks=1;
	}

	$challengeQuesAttempted = 0;
	if(SUBJECTNO==2)
	{
		//$query  = "SELECT count(srno), sum(R) FROM adepts_ttChallengeQuesAttempt WHERE sessionID=$sessionID";
		$date = date('Y-m-d');
		$query  = "SELECT count(srno), sum(R) FROM adepts_ttChallengeQuesAttempt WHERE lastModified like '".$date."%'";
		$result = mysql_query($query);
		$line   = mysql_fetch_array($result);
		$challengeQuesAttempted = $line[0];
		if($line[1]=="")
			$challengeQuesCorrect   = 0;
		else
			$challengeQuesCorrect   = $line[1];
	}

	//$resArr = lastSession($userID,$sessionID);
	
	$resArr = lastdaySession($userID,date('Y-m-d'));
	//$resArr = lastdaySession($userID,$sessionID);

	//$timedTestArray = getTimedTestAttemptedInSession($userID,$sessionID);
	//$gamesArray = getGamesAttemptedInSession($userID,$sessionID);

	$timedTestArray = getTimedTestAttemptedIndaySession($userID,$date);
	$gamesArray = getGamesAttemptedInDAYSession($userID,$date);
	
	$remedialItemAttemptArray = getRemedialItemAttempts($userID,$sessionID);
	$challengeQuesArray = getChallengeQuesAttemptedInSession($userID,$sessionID);
	//print_r($resArr);
	$totalQuestions = count($resArr);
	$score = 0;
	$timesum = 0;
	$quesAttemptedArray = array();

	foreach( $resArr as $val) {
		if( $val[ 5 ] == 1)
			$score++;
		$timesum+= $val[ 6 ];
	}
	$score = $score + $totalScore;

	$timespent_this_session = 0.00;
	$timespent_query = "SELECT date_format(startTime,'%d-%b-%Y %H:%i:%s') starttime,date_format(endTime,'%d-%b-%Y %H:%i:%s') endtime,date_format(tmLastQues,'%d-%b-%Y %H:%i:%s') tmLastQues, time_format(timediff(if(endTime>ifnull(tmLastQues,0),endTime,tmLastQues),startTime),'%H:%i:%s') duration, date_format(lastModified,'%d-%b-%Y %H:%i:%s') as lastModified, noOfJumps FROM ".TBL_SESSION_STATUS." WHERE sessionID='".$sessionID."'";
	$timespent_result = mysql_query($timespent_query);
	$timespent_data = mysql_fetch_array($timespent_result);
	//$starttime_timestamp = strtotime($timespent_data['startTime']);
    $sparkiesInThisSession = $timespent_data['noOfJumps'];
	
	//$timespent_this_session = number_format((($endtime_timestamp - $starttime_timestamp)/60), 2, ".", "");
	$exploded_time_spent = explode(":", $timespent_data['duration']);
	if($exploded_time_spent[0]>0){
		$exploded_time_spent[0] = $exploded_time_spent[0]*60 + $exploded_time_spent[1];
		$exploded_time_spent[1] = $exploded_time_spent[2];
	}else{
		$exploded_time_spent[0] = $exploded_time_spent[1];
		$exploded_time_spent[1] = $exploded_time_spent[2];
	}
	if ($exploded_time_spent[1]>60)
	{
		$exploded_time_spent[0] = $exploded_time_spent[0] + 1;
		$exploded_time_spent[1] = $exploded_time_spent[1] - 60;
	}
	else if ($exploded_time_spent[1]==60 || $exploded_time_spent[1]==6)
	{
		$exploded_time_spent[0] = $exploded_time_spent[0] + 1;
		$exploded_time_spent[1] = "00";
	}
	else if ($exploded_time_spent[1]==0 || $exploded_time_spent[1]=="")
	{
		$exploded_time_spent[1] = "00";
	}

?>

    	<div id="info_bar" class="forLowerOnly hidden">
        	<div id="blankWhiteSpace"></div>
             <div id="home">
                <div id="homeIcon" class="linkPointer"<?php if($deactiveLinks!=1) { ?> onClick="getHome() <?php } ?>"></div>
                 <div id="dashboardHeading" class="forLowerOnly"> - <a class="removeDecoration textUppercase" href="<?php if($deactiveLinks!=1) { ?>dashboard.php<?php } else {?>javascript:void(0);<?php } ?>" data-i18n="dashboardPage.dashboard"></a> - <a class="removeDecoration" href="<?php if($deactiveLinks!=1) { ?>sessionWiseReport.php<?php } else {?>javascript:void(0);<?php } ?>" data-i18n="sessionWiseReportPage.sessionWiseReport"></a> - <span class="textUppercase" data-i18n="endSessionReportPage.scoreCard"></span></div>
                <div class="clear"></div>
            </div>
        </div>
		<div id="info_bar" class="forHigherOnly hidden">
			<div id="topic">
				<div id="home">
                    <div id="homeIcon"<?php if($deactiveLinks!=1) { ?> onClick="getHome() <?php } ?>"></div>
                    <div id="homeText" class="forHigherOnly"><span<?php if($deactiveLinks!=1) { ?> onClick="getHome() <?php } ?>" class="textUppercase linkPointer" data-i18n="dashboardPage.home"></span> > <font color="#606062"><a class="removeDecoration textUppercase" href="<?php if($deactiveLinks!=1) { ?>dashboard.php<?php } else {?>javascript:void(0);<?php } ?>" data-i18n="dashboardPage.dashboard"></a></font> > <font color="#606062"><a class="removeDecoration" href="<?php if($deactiveLinks!=1) { ?>sessionWiseReport.php<?php } else {?>javascript:void(0);<?php } ?>" data-i18n="sessionWiseReportPage.sessionWiseReport"></a></font> > <font color="#606062"> <span class="textUppercase" data-i18n="endSessionReportPage.scoreCard"></span></font></div>
                    <div class="clear"></div>
				</div>
                <div class="clear"></div>
			</div>

			<div id="studentInfo">
            	<div id="studentInfoUpper">
                	<div class="class"><span data-i18n="common.class">Class</span>  <?=$childClass.$childSection?></div>
                	<div class="Name"><?=$Name?></div>
                    <div class="clear"></div>
                </div>
            </div>
             <a href="<?php if($deactiveLinks!=1) { ?>sessionWiseReport.php<?php } else {?>javascript:void(0);<?php } ?>" class="removeDecoration"><div id="sessionWiseReport" class="textUppercase" data-i18n="dashboardPage.sessionWise"></div></a>
            <div class="clear"></div>
          <!--  <div id="session">
                <span data-i18n="common.sessionID"></span>: <font color="#39a9e0"><?=$sessionID?></font>
            </div>
            <div id="duration">
                <span data-i18n="endSessionReportPage.currentSessionTime"></span>: <font color="#39a9e0"><?=$exploded_time_spent[0].":".$exploded_time_spent[1]?> <span data-i18n="endSessionReportPage.minutes"></span></font>
            </div>
            <div id="sparkieInfo">
                <span data-i18n="[html]endSessionReportPage.sparkieEarned"></span>: <?=$sparkiesInThisSession?>
            </div>-->
		</div>
        <div id="info_bar" class="forHighestOnly">
        <?php	if(isset($_SESSION['examCornerCluster']) && $_SESSION['examCornerCluster']==1)
				{ ?>
				<a href="examCorner.php" style="text-decoration:none;color:inherit"><div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase">Exam Corner</span></div>
                </div></a>
         <?php } else { ?>
         		<a href="dashboard.php" style="text-decoration:none;color:inherit"><div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase" data-i18n="dashboardPage.dashboard"></span></div>
                </div></a>
         <?php } ?>
				<div class="arrow-right"></div>
				<div id="endSessionHeading">End Session Report</div>
				<div id="session">
                    <span data-i18n="common.sessionID"></span>: <font id="sessionColor"><?=$sessionID?></font>
                </div>
				<div id="duration">
	                <span data-i18n="endSessionReportPage.currentSessionTime"></span>: <font id="durationColor"><?=$exploded_time_spent[0].":".$exploded_time_spent[1]?> <span data-i18n="endSessionReportPage.minutes"></span></font>
	            </div>
                <div id="challengeQues">
					CHALLENGE QUESTIONS
                    <span class="correct_bar">CORRECT : <?=$challengeQuesCorrect?> &nbsp;&nbsp; ATTEMPTED : <?=$challengeQuesAttempted?></span>
                </div>
                <div id="totalQuestionCorrect">
                    QUESTIONS
                    <span class="correct_bar">CORRECT : <?=$score?> &nbsp;&nbsp; ATTEMPTED : <?=$totalQuestions?></span>
                </div>
				<div class="clear"></div>
			</div>
		</div>
		<div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
             <?php	if(isset($_SESSION['examCornerCluster']) && $_SESSION['examCornerCluster']==1)
				{ ?>
				<a href="improveConcepts.php">
				<div id="report">
					<span id="reportText">IMPROVE YOUR CONCEPTS</span>
					<div id="reportIcon" class="circle"><div class="arrow-s"></div></div>
				</div></a>
              <?php } else { ?>
              	<a href="sessionWiseReport.php">
				<div id="report">
					<span id="reportText">SESSION WISE REPORT</span>
					<div id="reportIcon" class="circle"><div class="arrow-s"></div></div>
				</div></a>
              <?php } ?>
				<div class="empty">
				</div>
			</div>
			</div>
        <div id="endSessionDataDivMain">
        <?php
		if(($mode== -2 || $mode==-3) && $theme==3) {
		  $msg = "";
		  if($mode == -2)
					$msg = "You have been trying questions from the topic: <strong>".$_SESSION['teacherTopicName']."</strong>. Click <a href=\"dashboard.php\" style='text-decoration:underline;color:blue;'>here</a> to restart this topic or select another topic";
			  elseif ($mode == -3)
					$msg = "<br/>Congratulations! You have successfully completed the topic <strong>".$_SESSION['teacherTopicName']."</strong>! <br/>Click <a href=\"dashboard.php\" style='text-decoration:underline;color:blue;'>here</a> to restart this topic or select another topic<br/>";

			echo "<div align='center' class='msg'>$msg</div><br/>";
		}
		if($mode=="refresh") {
			echo "<div align='center'>";
			echo "It seems you have pressed refresh.<br/>Kindly <a href='logout.php'>login</a> again!";
			echo "</div>";
		}
		else if ($mode== -7 )	{
			echo "<div align='center'><br/>";
			echo "<h2><font color='red'>You have completed your 30 minute session!</font><br/> We hope you enjoyed it.Please login again if you want to continue.</h2>";
			echo "</div>";
		}
		else if ($mode== -5 )	{
			echo "<div align='center'><br/>";
			echo "You have completed your Mindspark quota for the week! <br/>You can login again tomorrow to enjoy Mindspark!";
			echo "</div>";
			//echo "<div align=center><div align='left'><br/>We have instituted a daily and weekly time limit mainly to ensure that you do Mindspark regularly without ignoring the other subjects. This has been done in consultation with the school authorities who would like you to progress at an even pace throughout the year.</div></div>";
		}
		else if ($mode== -6 )	{
			echo "<div align='center'><br/>";
			echo "You have completed your session for the day!<br/>You can login again tomorrow to enjoy Mindspark!";
			echo "</div>";
			//echo "<div align=center><div align='left' ><br/>We have instituted a daily and weekly time limit mainly to ensure that you do Mindspark regularly without ignoring the other subjects. This has been done in consultation with the school authorities who would like you to progress at an even pace throughout the year.</div></div>";
		}
		else if ($mode== 6 )	{
			echo "<div align='center'><br/>";
			echo "Your session is timed out due to inactivity.<br>Please <a href='index.php'>login again</a> to continue.";
			echo "</div>";
		}
			if($user->subscriptionDaysRemaining!="" && $user->subscriptionDaysRemaining<10)
			{
				if ($category=="STUDENT" && strcasecmp($subcategory,"Individual")==0)
				{
					$msg = "<br>Your subscription period will end on ".$endDate;
					$msg .= ". <a href=\"javascript:renew('".$userID."')\">Click here</a> to renew.";
					echo "<div align='center' class='msg'>$msg</div>";
				}
			}
		?>
        	<div id="headingDiv" class="forHigherOnly textUppercase" data-i18n="endSessionReportPage.endSessionReport"></div>
            <div id="sessionInfo" class="forHigherOnly hidden">
                <div id="totalQuestion">
                    <span id="totalQuestionText" data-i18n="endSessionReportPage.quesAnswered"></span>
                    <span id="challengeQuesAttemptedText" data-i18n="endSessionReportPage.attempted"></span>:
                    <span id="totalQuestionDigit"><?=$totalQuestions?></span>
                    <span id="challengeQuesCorrectText" data-i18n="endSessionReportPage.correct"></span>:
                    <span id="totalQuestionCorrectDigit"><?=$score?></span>
                </div>
                <div id="challengeQues">
                    <span id="challengeQuesText" data-i18n="endSessionReportPage.challengeQues"></span>
                    <span id="challengeQuesAttemptedText" data-i18n="endSessionReportPage.attempted"></span>:
                    <span id="challengeQuesAttemptedDigit"><?=$challengeQuesAttempted?></span>
                    <span id="challengeQuesCorrectText" data-i18n="endSessionReportPage.correct"></span>:
                    <span id="challengeQuesCorrectDigit"><?=$challengeQuesCorrect?></span>
                </div>
                <div id="totalQuestionCorrect">
                    <span id="timeTakenQuesText" data-i18n="endSessionReportPage.quesAttemptTime"></span>:
                    <span id="timeTakenQuesDigit"><?=convertSecs($timesum)?></span>
                    <span data-i18n="endSessionReportPage.minutes"></span>
                </div>
                <!--<div id="totalQuestionCorrect">
                    <span id="totalQuestionCorrectText" data-i18n="endSessionReportPage.quesAnsweredCorrectly"></span>:
                    <span id="totalQuestionCorrectDigit"><?=$score?></span>
                </div>-->

                <div class="clear"></div>
            </div>
            <div id="detail_bar" class="forLowerOnly hidden">
            	 <div id="session">
                    <span data-i18n="common.sessionID" class="fontWeight"></span> : <font color="#39a9e0"><?=$sessionID?></font>
                </div>
                <div id="duration">
                    <span data-i18n="endSessionReportPage.currentSessionTime" class="fontWeight"></span> : <font color="#39a9e0"><?=$exploded_time_spent[0].":".$exploded_time_spent[1]?> <span data-i18n="endSessionReportPage.minutes"></span></font>
                </div>
                <div id="sparkieInfo">
                    <span data-i18n="[html]endSessionReportPage.sparkieEarned" class="fontWeight"></span> : <?=$sparkiesInThisSession?>
                </div>
                <div id="totalQuestionCorrect">
                    <span id="timeTakenQuesText" data-i18n="endSessionReportPage.quesAttemptTime" class="fontWeight"></span> :
                    <span id="timeTakenQuesDigit"><?=convertSecs($timesum)?></span>
                </div>
                <div class="clear"></div>
                <div id="totalQuestion">
                	<span id="totalQuestionText" data-i18n="endSessionReportPage.quesAnswered"></span> -
                    <span id="challengeQuesAttemptedText" data-i18n="endSessionReportPage.attempted"></span> :
                	<span id="totalQuestionDigit"><?=$totalQuestions?></span>
                    <span id="challengeQuesCorrectText" data-i18n="endSessionReportPage.correct"></span> :
                    <span id="totalQuestionCorrectDigit"><?=$score?></span>
				</div>
                <!--<div id="totalQuestionCorrect">
                	<span id="totalQuestionCorrectText" data-i18n="endSessionReportPage.quesAnsweredCorrectly"></span>:
                	<span id="totalQuestionCorrectDigit"><?=$score?></span>
				</div>-->
                <div class="clear"></div>
                <div id="challengeQues">
                	<span id="challengeQuesText" data-i18n="endSessionReportPage.challengeQues"></span> -
                	<span id="challengeQuesAttemptedText" data-i18n="endSessionReportPage.attempted"></span>:
                    <span id="challengeQuesAttemptedDigit"><?=$challengeQuesAttempted?></span>
                    <span id="challengeQuesCorrectText" data-i18n="endSessionReportPage.correct"></span>:
                    <span id="challengeQuesCorrectDigit"><?=$challengeQuesCorrect?></span>
				</div>
                <a href="sessionWiseReport.php?mode=<?=$mode?>" class="removeDecoration">
                <div id="prevReportImote">
                    <div id="prevReportText" data-i18n="endSessionReportPage.prevReport"></div>
                </div></a>
	        </div>

            <div id="dataTableDiv">
                <table width="100%" border="0" class="endSessionTbl" align="center">
                    <tr class="trHead">
						<td>Sr No</td>
                       <!-- <td>Ques. No</td> -->
                        <td>My Answer</td>
                        <td>Correct Answer</td>
                        <td>Result</td>
                    </tr>
                    <tr class="forLowerOnly"><td colspan="4" class="yellowBackground"></td></tr>
                    <tr class="forLowerOnly"><td colspan="4" class="forLowerOnly"></td></tr>
		<?php //echo "<pre>"; print_r($resArr); echo "</pre>";
		
			$counter = 1;
			$totalQuestions = 1;

			$return = array();
				foreach($resArr as $v) {
					$return[$v[8]][] = $v;
				}
			$result = array(); 
			  foreach ($return as $key => $value) { 
				if (is_array($value)) { 
				  $result = array_merge($result,$value); 
				} 
			  } 


			foreach($result as $val) {
				
				if(count($timedTestArray)>0 && $timedTestArray[0]["attemptedOn"] < $val[7])
				{
					//echo "<pre>"; print_r($timedTestArray); echo "</pre>";
				 ?>
					<!--timed test-->
                    <tr>
                        <td colspan="4">
                        	<div class="timdTestDispDiv">
								<div id="timedTestIcon"></div>
                            	<div class="timdTestDesc">Timed Test : <?=$timedTestArray[0]["description"]?></div>
                                <div class="timdTestInfo">
                                    <div class="timdTestLevel">Status: Complete % correct : <?=$timedTestArray[0]["perCorrect"]?>%</div>
                                    <div class="timdTestTimeTaken">Time taken : <?=convertSecs($timedTestArray[0]["timeTaken"])?> <span data-i18n="endSessionReportPage.minutes"></span></div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
					<!--timed test ends-->
            	<?php array_pop($timedTestArray); }
					
				if(count($remedialItemAttemptArray)>0 && $remedialItemAttemptArray[0]["attemptedOn"] < $val[6]) {
					$levelStr	=	activityLevelDetails($remedialItemAttemptArray[0]["srno"]);
					$levelArr	=	explode("~",$levelStr);
					if($levelArr[2]==0)
						$timeTaken	=	$remedialItemAttemptArray[0]["timeTaken"];
					else
						$timeTaken	=	$levelArr[2];
				 ?>
					<!--Activity-->
					<tr>
                        <td colspan="4">
                        	<div class="activityDispDiv">
                            	<div class="activityDesc">Game : <?=$remedialItemAttemptArray[0]["description"]?></div>
                                <div class="ativityInfo">
                                    <div class="activityLevel">Levels cleared: <?=$levelArr[1]?> out of <?=$levelArr[0]?></div>
                                    <div class="activityTimeTaken">Time taken : <?=convertSecs($timeTaken)?> <span data-i18n="endSessionReportPage.minutes"></span></div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
					<!--Activity ends-->
            <?php array_pop($remedialItemAttemptArray); }

			$sq ="select endTime from adepts_sessionStatus WHERE sessionID='".$val[8]."'";
			$endtimeresult_result = mysql_query($sq);
			$endtimeresult_result = mysql_fetch_array($endtimeresult_result);
			//echo $endtimeresult_result['endTime']."<br>";

				if($endtimeresult_result['endTime'] == '0000-00-00 00:00:00' || is_null($endtimeresult_result['endTime']))
				{
					$test = removenull($userID,$childClass,$val[8]);
				}

			 	
	$timespent_query = "SELECT date_format(startTime,'%d-%b-%Y %H:%i:%s') starttime,date_format(endTime,'%d-%b-%Y %H:%i:%s') endtime,date_format(tmLastQues,'%d-%b-%Y %H:%i:%s') tmLastQues, time_format(timediff(if(endTime>ifnull(tmLastQues,0),endTime,tmLastQues),startTime),'%H:%i:%s') duration, date_format(lastModified,'%d-%b-%Y %H:%i:%s') as lastModified, noOfJumps FROM ".TBL_SESSION_STATUS." WHERE sessionID='".$val[8]."'";

	//echo $timespent_query; exit;

	$timespent_result = mysql_query($timespent_query);
	$timespent_data = mysql_fetch_array($timespent_result);
	//$starttime_timestamp = strtotime($timespent_data['startTime']);
    $sparkiesInThisSession = $timespent_data['noOfJumps'];
	
	
	$exploded_time_use = explode(":", $timespent_data['duration']);
	if($exploded_time_use[0]>0){
		$exploded_time_use[0] = $exploded_time_use[0]*60 + $exploded_time_use[1];
		$exploded_time_use[1] = $exploded_time_use[2];
	}else{
		$exploded_time_use[0] = $exploded_time_use[1];
		$exploded_time_use[1] = $exploded_time_use[2];
	}
	if ($exploded_time_use[1]>60)
	{
		$exploded_time_use[0] = $exploded_time_use[0] + 1;
		$exploded_time_use[1] = $exploded_time_use[1] - 60;
	}
	else if ($exploded_time_use[1]==60 || $exploded_time_use[1]==6)
	{
		$exploded_time_use[0] = $exploded_time_use[0] + 1;
		$exploded_time_use[1] = "00";
	}
	else if ($exploded_time_use[1]==0 || $exploded_time_use[1]=="")
	{
		$exploded_time_use[1] = "00";
	}	
	if($exploded_time_use[0] == '')
	{
		//$exploded_time_use[0] = 13;
		//$exploded_time_use[1] = 47;
		//
	}
?>
			<?php  if($previoussessionValue != $val[8] || $previoussessionValue == '') { 
				$previoussessionValue = $val[8];
			 ?>
		
			 <tr class="myclass" style='font-size: 10px;padding: 0px;'>
			 <td colspan='4' style='background-color: #a0a0a0;height: 4px;'>
				<div   style='background-color: red;padding-bottom: 0;margin-top: 2px;'>

							Session ID: <?=$val[8]?>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							
							<span data-i18n="endSessionReportPage.currentSessionTime"></span>: <?=$exploded_time_use[0].":".$exploded_time_use[1]?> <span data-i18n="endSessionReportPage.minutes"></span>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							
							Sparkies Earned in this session: <?=$sparkiesInThisSession?>
				</div>
			 </td>
			 </tr> 
	

			 <?php     }  ?>

		<?php
		foreach ($gamesArray as $game){
		if(count($gamesArray)>0 && $gamesArray[count($gamesArray)-1]["attemptedOn"] < $val[6]) {
						$levelStr	=	activityLevelDetails($gamesArray[count($gamesArray)-1]["srno"]);
						$levelArr	=	explode("~",$levelStr);
						if($levelArr[2]==0)
							$timeTaken	=	$gamesArray[count($gamesArray)-1]["timeTaken"];
						else
							$timeTaken	=	$levelArr[2];

						//echo "<pre>";
						//print_r($gamesArray);
					//	echo $gamesArray[count($gamesArray)-1]["sessionID"];
						if($val[8] == $gamesArray[count($gamesArray)-1]["sessionID"]) {
				 ?>
					<!--Activity-->
					<tr>
                        <td colspan="5">
                        	<div class="activityDispDiv">
								<div id="gameIcon"></div>
                            	<div class="activityDesc">Game : <?=$gamesArray[count($gamesArray)-1]["description"]?></div>
                                <div class="ativityInfo">
                                    <div class="activityLevel">Levels cleared: <?=$levelArr[1]?> out of <?=$levelArr[0]?></div>
									<div class="activityTimeTaken">Time taken : <?=convertSecs($timeTaken)?> <span data-i18n="endSessionReportPage.minutes"></span></div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
					<!--Activity ends-->
            <?php array_pop($gamesArray); }}  }?>


            <tr class="higherAllign">
				<!--<td><span style="cursor:pointer;color:red;" title="SessionID : <?=$val[8]?>"><?=$counter?></span></td> -->
               	<td><a style="text-decoration:underline;color:blue;" href="javascript:showQues('<?=$val[2]?>','<?=$val[1]?>','<?=$val[0]?>')"><?=$counter?></a></td>
                <td ><?=$val[3]?></td>
                <td ><?=$val[4]?></td>
                <td align="center"><div class="<?php if($val[5]==0) echo 'wrongAnsIcon'; else echo 'correctAnsIcon';?>"></div></td>
            </tr>
			<?php $counter++; }
				if(count($quesAttemptedArray)>0) {
				 ?>
				 <tr>
                        <td colspan="4">
                        	<div class="activityDispDiv">
                            	<div class="activityDesc">Topic Revision Questions</div>
                                <br/>
                            </div>
                        </td>
                    </tr>
				 
				<?php }foreach( $quesAttemptedArray as $key=>$val) { ?>
            <tr>
                <td><a style="text-decoration:underline;color:blue;" href="javascript:showPracticeQues('<?=$val[2]?>','<?=$val[1]?>','<?=$val[0]?>')"><?=$val[1]?></a></td>
                <td width="280px"><?=$val[3]?></td>
                <td width="280px"><?=$val[6]?></td>
                <td align="center"><div class="<?php if($val[4]==0) echo 'wrongAnsIcon'; else echo 'correctAnsIcon';?>"></div></td>
            </tr>
			<?php   } 
				if(count($gamesArray)>0) { 
					$levelStr	=	activityLevelDetails($gamesArray[count($gamesArray)-1]["srno"]);
					$levelArr	=	explode("~",$levelStr);
					if($levelArr[2]==0)
						$timeTaken	=	$gamesArray[count($gamesArray)-1]["timeTaken"];
					else
						$timeTaken	=	$levelArr[2];
				 ?>
				 
                <!--Activity-->
                <tr>
                    <td colspan="4">
                        <div class="activityDispDiv">
                            <div id="gameIcon"></div>
                            <div class="activityDesc">Game : <?=$gamesArray[count($gamesArray)-1]["description"]?></div>
                            <div class="ativityInfo">
                                <div class="activityLevel">Levels cleared: <?=$levelArr[1]?> out <?=$levelArr[0]?></div>
                                <div class="activityTimeTaken">Time taken : <?=convertSecs($timeTaken)?> <span data-i18n="endSessionReportPage.minutes"></span></div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </td>
                </tr>
                <!--Activity ends-->
            <?php array_pop($gamesArray); } ?>
            </table>
            </div>
        </div>
	</div>
    <div style="display:none">
        <div id="sendFeedback">
            <div id="feedbakText">Do you want to give feedback</div><br/>
            <div class="buttonTemp1" onClick="javascript:window.location.href='surveyForm.php';">Yes</div>
            <div class="buttonTemp1" onClick="javascript:$('#cboxClose').click();">Remind me later</div>
        </div>
    </div>

    <form id="frmReport" action="quesWiseReport.php" method="POST">
        <input type="hidden" name="userID" id="userID" value="<?=$userID?>">
        <input type="hidden" name="sessionIDNew" id="sessionIDNew" value="<?=$sessionID?>">
		<input type="hidden" name="modeCheck" id="modeCheck" value="2">
        <input type="hidden" name="qcode" id="hidqcode">
        <input type="hidden" name="srno" id="hidsrno">
        <input type="hidden" name="qno" id="hidqno">
        <input type="hidden" name="bucketAttemptID" id="bucketAttemptID" value="<?=isset($_SESSION['bucketAttemptID'])?$_SESSION['bucketAttemptID']:""?>">
        <input type="hidden" name="mode" id="mode" value="normal">
    </form>
	

<?php 
$parentFileNameStr = explode("/",$_SERVER['HTTP_REFERER']);
$parentFileNameStr1 = array_pop($parentFileNameStr);
$parentFileNameStr2 = explode("?",$parentFileNameStr1);
$parentFileNameStr = $parentFileNameStr2[0];
//echo $parentFileNameStr;

if($parentFileNameStr=="question.php")
{
	if($endType==0)
		mysql_query("UPDATE ".TBL_SESSION_STATUS." SET endType=concat_ws(',',endType,'S1(".$totalQuestions.")') WHERE sessionID=".$sessionID) or die(mysql_error());
	else
		mysql_query("UPDATE ".TBL_SESSION_STATUS." SET endType=concat_ws(',',endType,'".$endType."','S1(".$totalQuestions.")') WHERE sessionID=".$sessionID) or die(mysql_error());
}

if(isset($_SESSION['examCornerCluster']) && $_SESSION['examCornerCluster']==1)
{
	unset($_SESSION['bucketAttemptID'],$_SESSION['bucketClusterCode'],$_SESSION['examCornerCluster'],$_SESSION["importantQuestions"],$_SESSION['bucketTopicName']);
}

include("footer.php");?>

<?php


function convertSecs($secs)
{
	if($secs==0)
		return "0";
	else if($secs<60)
		return "0:".str_pad($secs,2,"0",STR_PAD_LEFT);
	else
	{
		$temp = explode(".",$secs/60);
		return str_pad($temp[0],2,"0",STR_PAD_LEFT). ":". str_pad($secs%60,2,"0", STR_PAD_LEFT);
	}
}

function getChallengeQuesAttemptedInSession($userID, $sessionID)
{
	$challengeQuesArray = array();
	$cq_query = "SELECT qcode,A,S,R,ttAttemptID,questionNo
                     FROM   adepts_ttChallengeQuesAttempt
					 WHERE  userID=$userID AND sessionID=".$sessionID." ORDER BY srno";
	//echo $cq_query;
	$cq_result = mysql_query($cq_query) or die(mysql_error());
	$cqno = 0;
	while ($cq_line=mysql_fetch_array($cq_result))
	{
		$challengeQuesArray[$cqno][0] = $cq_line['qcode'];
		$challengeQuesArray[$cqno][1] = $cq_line['A'];
		$challengeQuesArray[$cqno][2] = $cq_line['S'];
		$challengeQuesArray[$cqno][3] = $cq_line['R'];
		$challengeQuesArray[$cqno][4] = $cq_line['questionNo'];
		$challengeQuesArray[$cqno][5] = $cq_line['ttAttemptID'];
		$cqno++;
	}
	return $challengeQuesArray;
}
function getClusterdesc($clusterCode)
{
	$sq	=	"SELECT cluster FROM adepts_clusterMaster WHERE clusterCode='$clusterCode'";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	return $rw[0];
}

function showSurveyAlert($userID,$sessionID)
{
	$sqF	=	"SELECT userID FROM adepts_feedbackresponse WHERE userID=$userID AND qid=24";
	$rsF	=	mysql_query($sqF);
	if(mysql_num_rows($rsF)==0)
	{
		$sq	=	"SELECT startTime FROM ".TBL_SESSION_STATUS." WHERE sessionID=".$sessionID;
		$rs	=	mysql_query($sq);
		$rw	=	mysql_fetch_array($rs);
		$startTime	=	$rw[0];
		$diff = strtotime(date("Y-m-d H:i:s")) - strtotime($startTime);
		if($diff > 1200)
			return true;
		else
			return false;
	}
	else
	{
		return false;
	}
}

function activityLevelDetails($srno)
{
	$totalLevel	=	0;
	$levelCleared	=	0;
	$timeTaken	=	0;
	$sq	=	"SELECT level,status,timeTaken FROM adepts_activityLevelDetails WHERE srno=$srno";
	$rs	=	mysql_query($sq);
	if(mysql_num_rows($rs) > 0)
	{
		while($rw=mysql_fetch_array($rs))
		{
			$timeTaken	+=	$rw[2];
			$totalLevel++;
			if($rw[1]==1)
				$levelCleared++;
		}
	}
	else
	{
		$totalLevel	=	1;
		$levelCleared	=	1;
	}
	return $totalLevel."~".$levelCleared."~".$timeTaken;
}

function removenull($userID,$childClass,$sessionid)
{
	
    $yesterday = date("Ymd", strtotime("today"));
   
        $userIDStr = "";
        $userIDStr = $userID;
        if($userIDStr!="")
        {

                $query = "SELECT z.sessionID sessID, z.lastModified lastMod, count(distinct a.srno) totalq, count(distinct g.srno) totalqd, count(distinct b.srno) totalcq, count(distinct c.timedTestID) totTmTst, count(distinct d.gameID) totgms,  count(distinct e.srno) totmonrev, count(distinct f.srno) tottoprev,
            greatest(IFNULL(max(a.lastModified),0),IFNULL(max(b.lastModified),0),IFNULL(max(c.lastModified),0),IFNULL(max(d.lastModified),0),IFNULL(max(e.lastModified),0),IFNULL(max(f.lastModified),0),IFNULL(max(g.lastModified),0)) maxLastTime
            FROM ((((((adepts_sessionStatus z LEFT JOIN adepts_teacherTopicQuesAttempt_class".$childClass." a on z.sessionID=a.sessionID)
            LEFT JOIN  adepts_ttChallengeQuesAttempt b on z.sessionID=b.sessionID)
            LEFT JOIN adepts_timedTestDetails c on z.sessionID=c.sessionID)
            LEFT JOIN adepts_userGameDetails d on z.sessionID=d.sessionID)
            LEFT JOIN adepts_revisionSessionDetails e on z.sessionID=e.sessionID)
            LEFT JOIN adepts_topicRevisionDetails f on z.sessionID=f.sessionID)
			LEFT JOIN adepts_diagnosticQuestionAttempt g on z.sessionID=g.sessionID
            WHERE z.userID = $userIDStr AND startTime_int=$yesterday group by z.sessionID order by z.sessionID";


                $result = mysql_query($query) or die(mysql_error());

                while ($line = mysql_fetch_array($result)) {
                    if($line['maxLastTime']!=0 && $line['sessID'] == $sessionid)
                    {
                        $update_query = "UPDATE adepts_sessionStatus SET ";
                        $update_query .= "totalQ=".($line['totalq']+$line['totalqd']).",";
                        $update_query .= "totalCQ=".$line['totalcq'].",";
                        $update_query .= "totalTmTst=".$line['totTmTst'].",";
                        $update_query .= "totalGms=".$line['totgms'].",";
                        $update_query .= "totalMonRevQ=".$line['totmonrev'].",";
                        $update_query .= "totalTopRevQ=".$line['tottoprev'].",";
                        $update_query .= "tmLastQues = '".$line['maxLastTime']."',";
                        $update_query .= "lastModified='".$line['lastMod']."' WHERE  sessionID=".$line['sessID'];

						//echo $update_query."<br>";
               
                        mysql_query($update_query) or die($update_query."<br>".mysql_error());
                    }
                }
        }	
}
?>