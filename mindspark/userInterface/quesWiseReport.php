<?php

set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);

include("check1.php");
include("classes/clsQuestion.php");
include("constants.php");
@include_once("functions/orig2htm.php");
include("classes/clsUser.php");
include ("functions/functionsForDynamicQues.php");
include("classes/clsDiagnosticTestQuestion.php");
include("classes/clsResearchQuestion.php");
if(!isset($_SESSION['userID']) || !isset($_REQUEST['qcode']))
{
	header("Location:logout.php");
	exit;
}
$userID = $_SESSION['userID'];
$Name = explode(" ", $_SESSION['childName']);
$Name = $Name[0];
$objUser = new User($userID);
$schoolCode    = $objUser->schoolCode;
$childClass    = $objUser->childClass;
$childName    = $objUser->childName;
$childSection  = $objUser->childSection;

$qcode= $_REQUEST['qcode'];
$sessionID= $_REQUEST['sessionIDNew'];
$reportDate= $_REQUEST['reportDate'];
if($reportDate=="")
	$reportDate = date('Y-m-d');
$modeCheck= $_REQUEST['modeCheck'];
$srno = $_REQUEST['srno'];
$qno  = $_REQUEST['qno'];
$mode = $_REQUEST['mode'];
if(strpos($qno,"D")>-1)
{
	$mode = 'diagnosticTest';
}
$bucketAttemptID	=	$_REQUEST['bucketAttemptID'];	
$qcodeArray = array();
//Check if qcode is part of PracticeCluster
$sql = "SELECT groupID, clusterType FROM adepts_questions a, adepts_clusterMaster b WHERE a.clusterCode=b.clusterCode AND a.qcode='$qcode'";
$result = mysql_query($sql);
$row = mysql_fetch_assoc($result);
$clusterType = $row['clusterType'];

if($clusterType == "practice")
{
	$groupID = $row['groupID'];
	$sql = "SELECT qcode FROM adepts_questions WHERE groupID='$groupID'";
	$result = mysql_query($sql);
	while($row = mysql_fetch_assoc($result))
	{
		$qcodeArray[] = $row['qcode'];
	}
	$sql = "SELECT groupText, groupColumn FROM adepts_groupInstruction WHERE groupID='$groupID'";
	$result = mysql_query($sql);
	$row = mysql_fetch_assoc($result);
	$groupText = $row['groupText'];
	$groupColumn = $row['groupColumn'];
}
else
{
	array_push($qcodeArray,$qcode);
}
sort($qcodeArray);
//print_r($_REQUEST);
?>

<?php include("header.php"); ?>

<title>Question Wise Report</title>
<?php if($theme==1) { ?>
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/quesWiseReport/lowerClass.css" rel="stylesheet" type="text/css">
<?php } else if($theme==2){ ?>
    <link rel="stylesheet" href="css/quesWiseReport/midClass.css?ver=1" />
	<link rel="stylesheet" href="css/commonMidClass.css" />
<?php } else { ?>
	<link rel="stylesheet" href="css/commonHigherClass.css" />
    <link rel="stylesheet" href="css/quesWiseReport/higherClass.css?ver=1" />
<?php } ?>
<script type="text/javascript" src="/mindspark/js/load.js"></script>
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/mindspark/userInterface/libs/combined.js"></script>
<!--<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="libs/closeDetection.js"></script>-->
<script>
var langType = '<?=$language;?>';
function load() {
<?php if($theme==1) { ?>	
	var a= window.innerHeight - (47 + 70 + 50 + 10);
	$('#topicInfoContainer').css("height",a+"px");
<?php } else if($theme==2){ ?>
	/*var a= window.innerHeight - (80 + 17 + 140 + 30 );
	$('#topicInfoContainer').css("height",a+"px");*/
<?php } else if($theme==3) { ?>
			var a= window.innerHeight - (170);
			var b= window.innerHeight - (610);
			$('#topicInfoContainer').css({"height":a+"px"});
			$('#sideBar').css({"height":a+"px"});
			$('#main_bar').css({"height":a+"px"});
			$('#menubar').css({"height":a+"px"});
		<?php } ?>
		if(androidVersionCheck==1){
			$('#topicInfoContainer').css("min-height","450px");
			$('#topicInfoContainer').css("height","auto");
			$('#main_bar').css("height",$('#topicInfoContainer').css("height"));
			$('#menu_bar').css("height",$('#topicInfoContainer').css("height"));
			$('#sideBar').css("height",$('#topicInfoContainer').css("height"));
		}
}

function goBack()
{
	setTryingToUnload();
	if($("#modeCheck").val()==2){
		document.getElementById("frmReport1").submit();
	}else{
		history.go(-1);
	}
}

function getHome()
{
	setTryingToUnload();
	window.location.href	=	"home.php";
}
function logoff()
{
	setTryingToUnload();
	window.location="logout.php";
}
var click=0;
function openMainBar(){
	
	if(click==0) {
		if(window.innerWidth>1024){
			$("#main_bar").animate({'width':'245px'},600);
			$("#plus").animate({'margin-left':'227px'},600);
		}
		else {
			$("#main_bar").animate({'width':'200px'},600);
			$("#plus").animate({'margin-left':'182px'},600);
		}
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

function loadConstrFrame(cfr)
{
		var cfrw=cfr.contentWindow;
		// cfrw.drawcode.setDrawnShapes(cfr.getAttribute("data-response"));
		cfrw.postMessage(JSON.stringify({
			subject: 'trail',
			content: {
				type: 'display',
				trail: cfr.getAttribute("data-response"),
			},
		}), getWindowOrigin(cfr.src));
}
function startInteractive(frame)
{	
    try {
        var win = frame.contentWindow;
        frames.push(frame);windows.push(win);
        //win.postMessage('setUserResponse='+$(frame).attr('data-response'), '*');
    }
    catch (ex) {
        //alert('error in getting the response from interactive');
    }
}
function getWindowOrigin(url) {
	var dummy = document.createElement('a');
	dummy.href = url;
	return dummy.protocol+'//'+dummy.hostname+(dummy.port ? ':'+dummy.port : '');
}
var frames=[],windows=[];
var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
var eventer = window[eventMethod];
var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";
// Listen to message from child window
eventer(messageEvent, function (e) {
    var response1 = ""; 
    response1 = e.data;
    //e.source.postMessage('setUserResponse='+$(frame).attr('data-response'), '*');
    if ($.inArray(e.source,windows)>-1){
    	var frame=frames[$.inArray(e.source,windows)];
	   	if(response1.indexOf("loaded=1") == 0) {
			e.source.postMessage('setUserResponse='+$(frame).attr('data-response'), '*');
	   	}
	   	else if(response1.indexOf("frameHeight=") == 0) {
		  frameHeight=response1.replace('frameHeight=','');$(frame).attr('height',frameHeight);
		}
    }
}, false);
</script>
</head>

<?php
//Populate the teacher topic name $topicName = $line['topic'];
if($mode=="topicRevision")
	$query = "SELECT teacherTopicDesc topic, R,A FROM ".TBL_TOPIC_REVISION." a, adepts_teacherTopicMaster b
	          WHERE  srno= $srno AND a.teacherTopicCode=b.teacherTopicCode";
else if($mode=="exercise")
{
	$query = "SELECT cluster topic, R,A FROM adepts_exerciseQuesAttempt a, adepts_clusterMaster b
	          WHERE  srno= $srno AND a.clusterCode=b.clusterCode";
}
else if($bucketAttemptID!="")
{
	$query  = "SELECT R,A FROM adepts_bucketClusterAttempt WHERE attemptID=$bucketAttemptID AND qcode=$qcode";
}
else if(strpos($qno,"WCQ") > -1 || strpos($qno,"COMPREHENSIVE") > -1)
{
	$query  = "SELECT A,R,f.teacherTopicDesc as topic FROM adepts_researchQuesAttempt a JOIN adepts_userComprehensiveFlowDetails b ON a.srno=b.activityAttemptID  JOIN adepts_userComprehensiveFlow c ON c.flowAttemptID=b.flowAttemptID JOIN adepts_comprehensiveModuleAttempt d ON c.srno=d.srno JOIN adepts_teacherTopicStatus e ON e.ttAttemptID=d.ttAttemptID JOIN adepts_teacherTopicMaster f ON e.teacherTopicCode=f.teacherTopicCode WHERE a.srno= $srno";	
}
else if(strpos($qno,"D")>-1)
{
	$query  = "SELECT R,A FROM adepts_diagnosticQuestionAttempt WHERE srno= $srno";
}
else if(strpos($qno,"Practice")>-1)
{
	$query  = "SELECT R,A FROM practiseModulesQuestionAttemptDetails WHERE id= $srno";
}
else
{
    $query  = "SELECT teacherTopicDesc topic, R,A FROM ".TBL_QUES_ATTEMPT_CLASS." a, adepts_teacherTopicMaster b
               WHERE  srno= $srno AND a.teacherTopicCode=b.teacherTopicCode";
}
$result = mysql_query($query);
$line   = mysql_fetch_array($result);

if(strpos($qno,"WCQ") > -1)
$topicName = "Wild Card Question";
else if(strpos($qno,"Practice")>-1)
{$topicName = "Practice";$mode="practiseModule";}
else if(strpos($qno,"D") > -1) 
$topicName = "Diagnostic Test";
else
$topicName = $line['topic'];

$userResponse	=	$line['R'];
$user_ans	=	$line['A'];


if($mode=="revision"){
	$query = "SELECT A,R FROM adepts_revisionSessionDetails where srno=$srno";
	$result = mysql_query($query);
	$line   = mysql_fetch_array($result);

	$userResponse	=	$line['R'];
	$user_ans	=	$line['A'];
}
//echo $query;
//echo $userResponse;
//echo $user_ans;
?>

<body class="translation" onLoad="load();" onResize="load();">
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
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?php echo $Name ?>&nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a href='myDetailsPage.php'><span data-i18n="homePage.myDetails"></span></a></li>
								<!--	<li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li>-->
									<li><a href='changePassword.php'><span data-i18n="homePage.changePassword"></span></a></li>
                                    <li><a href='whatsNew.php'><span data-i18n="common.whatsNew"></span></a></li>
                                    <li><a href='logout.php'><span data-i18n="common.logout"></span></a></li>
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
        <div id="logout" class="linkPointer hidden" onClick="logoff();">
        	<div class="logout"></div>
        	<div class="logoutText" data-i18n="common.logout"></div>
        </div>
		<div id="whatsNew" style="visibility:hidden">
            <div class="whatsNew"></div>
            <div class="whatsNewText" data-i18n="common.whatsNew"></div>
        </div>
    </div>
	
	<div id="container">
        <div id="info_bar" class="forLowerOnly hidden">
        	<div id="blankWhiteSpace">
            </div>
             <div id="home">
                <div id="homeIcon" class="linkPointer" onClick="getHome()"></div>
                <div id="dashboardHeading" class="forLowerOnly"> - <a class="removeDecoration textUppercase" href="dashboard.php" data-i18n="dashboardPage.dashboard"></a> - <a class="removeDecoration" href="sessionWiseReport.php" data-i18n="sessionWiseReportPage.sessionWiseReport"></a> - <span class="textUppercase linkPointer" onClick="goBack()" data-i18n="endSessionReportPage.scoreCard"></span></div>
                <div class="clear"></div>
            </div>
        </div>
        
		<div id="info_bar" class="forHigherOnly hidden">
			<div id="topic">
				<div id="home">
                	<div id="homeIcon" class="linkPointer"></div>
                    <div id="homeText"><span onClick="getHome()" class="textUppercase linkPointer" data-i18n="dashboardPage.home" ></span> &#10140; &nbsp;<a onClick="goBack()" class="textUppercase linkPointer" style="color:#606062;">End-Session Report</a> &#10140; &nbsp; <font color="#606062" style="text-transform:uppercase;"> <?=$topicName?></font></div>
				</div>
				<div class="clear"></div>
			</div>
            <div id="goBackDiv" onClick="goBack()" class="textUppercase" data-i18n="quesWiseReportPage.backToReport"></div>
            <div class="clear"></div>
		</div>
        <div id="info_bar" class="forHighestOnly">
				<a href="dashboard.php" style="text-decoration:none;color:inherit"><div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase" data-i18n="dashboardPage.dashboard"></span></div>
                </div></a>
				<div class="arrow-right"></div>
				<div id="sessionHeading"><?=$topicName?></div>
		</div>
		<div id="main_bar" class="forHighestOnly">
			<div id="drawer1">
			<a href="activity.php" style="text-decoration:none;color:inherit"> 
			<div id="drawer1Icon"></div>
			ACTIVITIES
			</div></a>
			<a href="examCorner.php" style="text-decoration:none;color:inherit"><div id="drawer2"><div id="drawer2Icon"></div>EXAM CORNER
			</div></a>
			<a href="home.php"><div id="drawer3"><div id="drawer3Icon"></div>HOME
			</div></a>
			<!--<div id="drawer4"><div id="drawer4Icon"></div>EXPLORE ZONE
			</div>-->
			<div id="plus" onClick="openMainBar();">
				<div id="vertical"></div>
				<div id="horizontal"></div>
			</div>
			<div id="drawer5"><div id="drawer5Icon"></div>REWARD POINT
			</div>
			<!--<a href="viewComments.php?from=links&mode=1"><div id="drawer6"><div id="drawer6Icon"></div>NOTIFICATIONS
			</div></a>-->
		</div>
        <div id="topicInfoContainerMain">
        <div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
				<div id="sessionReport" onClick="goBack();">Back to report</div>
				<div class="empty"></div>
			</div>
			</div>
        <div id="topicInfoContainer">
<?php
$Number = 0;
foreach($qcodeArray as $singleQcode)
{
	$Number++;
	if(strpos($qno,"D") > -1)
		$question = new diagnosticTestQuestion($singleQcode);
	else if($qno == "WCQ_R")
		$question = new researchQuestion($singleQcode);
	else
		$question = new Question($singleQcode);
	$eeresponse = "";
	if($question->eeIcon == "1")
	{
		if($clusterType == "practice")
			$question_type = "practice";
		else
			$question_type = "normal";
		$eeresponse = getEEresponse($srno, $childClass, $question_type);
	}
	if($question->isDynamic())
	{
		$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE userID=$userID AND mode='$mode' AND class=$childClass AND quesAttempt_srno= $srno";
		$result = mysql_query($query);
		$line   = mysql_fetch_array($result);
		$question->generateQuestion("answer",$line[0]);
	}
		
	$correct_answer = $question->getCorrectAnswerForDisplay();
?>          
		<div id="quesTrailDiv">
            	<div id="quesNoDiv">
					<?php 
					if(strpos($qno,"COMPREHENSIVE") > -1) { ?>
						<div id="quesNo"><?=str_replace("COMPREHENSIVE", "", $qno);?></div>
					<?php }
					elseif(strpos($qno,"WCQ") === false) {?>
                	<div id="quesNo"><?=str_replace("Practice ", "", $qno);?></div>
					<?php }	else{?>
					<div id="quesNo" class='wcQuesNo'>WCQ</div>
					<?php	} ?>
                </div>
                <div id="quesTextDiv">
                	<div id="quesText"><?php
	$eeresponse = getEEresponse($srno, $childClass, 'normal');
	$longResponse = getLongUserResponse($srno, $userID, $qcode, $sessionID, $user_ans);
	if (strpos($question->questionStem,"ADA_eqs") !== false) {
		echo $question->getQuestionForDisplay($longResponse,2);
		$user_ans = "";
	}
	else echo $question->getQuestionForDisplay($eeresponse);

?></div>

					
<?php	if($question->quesType=='MCQ-4' || $question->quesType=='MCQ-3' || $question->quesType=='MCQ-2')	{	?>
                    <div id="optionDiv">
                    
                    	<div class="optionAdiv floatLeft">
                        	<div class="optA  <?php if($correct_answer=="A") echo "option"; else echo "option"?> floatLeft" data-i18n="common.optA">A</div>
                            <div class="optAText floatLeft"><?=$question->getOptionA();?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="optionBdiv floatLeft">
                        	<div class="optB <?php if($correct_answer=="B") echo "option"; else echo "option"?> floatLeft" data-i18n="common.optB">B</div>
                            <div class="optAText floatLeft"><?=$question->getOptionB();?></div>
                            <div class="clear"></div>
                        </div>
<?php	if($question->quesType=='MCQ-4') { ?>
                        <div class="clear"></div>
<?php }	if($question->quesType=='MCQ-4' || $question->quesType=='MCQ-3') { ?>
                        <div class="optionCdiv floatLeft">
                        	<div class="optC <?php if($correct_answer=="C") echo "option"; else echo "option"?> floatLeft" data-i18n="common.optC">C</div>
                            <div class="optAText floatLeft"><?=$question->getOptionC();?></div>
                            <div class="clear"></div>
                        </div>
	<?php	if($question->quesType=='MCQ-4') { ?>                        
                        <div class="optionDdiv floatLeft">
                        	<div class="optD <?php if($correct_answer=="D") echo "option"; else echo "option"?> floatLeft" data-i18n="common.optD">D</div>
                            <div class="optAText floatLeft"><?=$question->getOptionD();?></div>
                            <div class="clear"></div>
                        </div>
	<?php } } ?>
                        <div class="clear"></div>
                    </div>
<?php } ?>
                    <div id="answerDiv">
                    	<div id="userResponseText">Your Response:</div>
                        <div id="userResponse"><?=$user_ans?></div>
                        <div id="correctAnsText">Correct Answer:</div>
                        <div id="correctAns"><?=$correct_answer?></div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <div id="quesFeedback">
                <div id="scoreDiv" class="<?php if($userResponse==1) echo 'scoreDiv_corret'; else echo 'scoreDiv_wrong';?>"><div id="feedback_header_img" <?php if($userResponse==1) echo "class='ques_correct'"; else echo "class='ques_incorrect'"?>></div><span class="explainText"><?php if($userResponse==1) echo "Great Work"; else echo "Sorry, that's incorrect! "?></span></div>
                <div id="feedbackExplain"><?php echo ($clusterType=="practice")?$correct_answer:$question->getDisplayAnswer();?></div>
                <div class="clear"></div>
            </div>
        </div>
<?php } ?>        
        </div>
		<form id="frmReport1" action="endSessionReport.php" method="POST">
			<input type="hidden" name="sessionID" id="sessionID" value="<?=$sessionID?>">
			<input type="hidden" name="reportDate" id="reportDate" value="<?=$reportDate?>">
			<input type="hidden" id="modeCheck" value="<?=$modeCheck?>">
	    </form>
	</div>
    
<?php include("footer.php"); ?>

<?php
function getEEresponse($srno, $childClass, $question_type)
{
	$eeResponse = "";
	$query = "SELECT eeResponse FROM adepts_equationEditorResponse WHERE srno='$srno' AND childClass='$childClass' AND question_type='$question_type'";
	$result = mysql_query($query);
	if(mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$eeResponse = $row[0];
	}
	return $eeResponse;
}
function getLongUserResponse($srno, $userID, $qcode, $sessionID,$user_ans)
{
	$longResponse = "";
	$query = "SELECT userResponse FROM longUserResponse WHERE srno='$srno' AND userID='$userID' AND sessionID='$sessionID' AND qcode='$qcode'";
	$result = mysql_query($query);
	if(mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$longResponse = $row[0];
	}
	else {
		$longResponse = $user_ans;
	}
	/*else {
		$query = "SELECT A FROM ".TBL_QUES_ATTEMPT_CLASS." WHERE srno='$srno' AND userID='$userID' AND sessionID='$sessionID' AND qcode='$qcode'";
		$result = mysql_query($query);
		if(mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			$longResponse = $row[0];
		}
	}*/
	return $longResponse;
}
?>