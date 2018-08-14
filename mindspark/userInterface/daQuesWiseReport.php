<?php

set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);

include("check1.php");
include("constants.php");
@include_once("functions/orig2htm.php");
include("classes/clsUser.php");
include ("functions/functionsForDynamicQues.php");
include("classes/clsdaTest.php");
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

$qcodeArray[] = $qcode= $_REQUEST['qcode'];
$sessionID= $_REQUEST['sessionIDNew'];
$reportDate= $_REQUEST['reportDate'];
if($reportDate=="")
	$reportDate = date('Y-m-d');
$modeCheck= $_REQUEST['modeCheck'];
$srno = $_REQUEST['srno'];
$qno  = $_REQUEST['qno'];
$mode = $_REQUEST['mode'];
?>

<?php include("header.php"); ?>

<title>Question Wise Report</title>
<?php if($theme==1) { ?>
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/quesWiseReport/lowerClass.css" rel="stylesheet" type="text/css">
<?php } else if($theme==2){ ?>
    <link rel="stylesheet" href="css/quesWiseReport/midClass.css" />
	<link rel="stylesheet" href="css/commonMidClass.css" />
<?php } else { ?>
	<link rel="stylesheet" href="css/commonHigherClass.css" />
    <link rel="stylesheet" href="css/quesWiseReport/higherClass.css" />
<?php } ?>
<script type="text/javascript" src="/mindspark/js/load.js"></script>
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="libs/closeDetection.js"></script>
<script>
var langType = '<?=$language;?>';
function load() {
<?php if($theme==1) { ?>	
	var a= window.innerHeight - (47 + 70 + 50 + 10);
	$('#topicInfoContainer').css("height",a+"px");
<?php } else if($theme==2){ ?>
	var a= window.innerHeight - (80 + 17 + 140 + 30 );
	$('#topicInfoContainer').css("height",a+"px");
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
	history.go(-1);
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
function getWindowOrigin(url) {
	var dummy = document.createElement('a');
	dummy.href = url;
	return dummy.protocol+'//'+dummy.hostname+(dummy.port ? ':'+dummy.port : '');
}
</script>
<style>
.optAText {
	width:85% !important;
}
</style>
</head>

<?php
//Populate the teacher topic name $topicName = $line['topic'];
$daPaperCode = $_SESSION['daPaperCode'];
$query= "SELECT A, R, S FROM da_questionAttemptDetails WHERE id= $srno and paperCode = '$daPaperCode' ";
$result = mysql_query($query);
$line   = mysql_fetch_array($result);
$userResponse	=	$line['R'];
$user_ans	=	$line['A'];

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
                    <div id="homeText"><span onClick="getHome()" class="textUppercase linkPointer" data-i18n="dashboardPage.home"></span> > <font color="#606062" class="textUppercase"> Super Test Report</font></div>
				</div>
				<div class="clear"></div>
			</div>
            <div id="goBackDiv" onClick="goBack()" class="textUppercase" data-i18n="quesWiseReportPage.backToReport">BACK TO REPORT</div>
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
	$question = new daTest($singleQcode);
	$correct_answer = $question->correctAnswer;
?>          
		<div id="quesTrailDiv">
            	<div id="quesNoDiv">
					<?php if($qno!="WCQ") {?>
                	<div id="quesNo"><?=$qno?></div><br><br>
					<?php } ?>
					&nbsp;<div class="<?php if($userResponse==0) echo 'wrongAnsIcon'; else echo 'correctAnsIcon';?>"></div>
                </div>
                <div id="quesTextDiv">
                	<div id="quesText"><?php
echo $question->getQuestionForDisplay($eeresponse);?></div>

					
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
            <!--<div id="quesFeedback">
                <div id="scoreDiv" class="<?php if($userResponse==1) echo 'scoreDiv_corret'; else echo 'scoreDiv_wrong';?>"><div id="feedback_header_img" <?php if($userResponse==1) echo "class='ques_correct'"; else echo "class='ques_incorrect'"?>></div><span class="explainText"><?php if($userResponse==1) echo "Great Work"; else echo "Sorry, that's incorrect! "?></span></div>
                <div id="feedbackExplain"><?php echo ($clusterType=="practice")?$correct_answer:$question->getDisplayAnswer();?></div>
                <div class="clear"></div>
            </div>-->
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
?>