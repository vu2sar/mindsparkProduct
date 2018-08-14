<?php

include("check1.php");
include("constants.php");
include("classes/clsUser.php");
include ("functions/functionsForDynamicQues.php");
include ("functions/functions.php");
include("classes/clsQuestion.php");
include("functions/orig2htm.php");

if(!isset($_SESSION['userID']))
	{
		echo "Your session has expired.<br/> Kindly login again to continue.";
		exit;
	}
	$_SESSION['revisionSessionTTArray']	=	array();
	unset($_SESSION['revisionSessionTTArray']);
	$userID = $_SESSION['userID'];

	if(isset($_POST['sessionID']))
		$sessionID = $_POST['sessionID'];
	else
		$sessionID	= $_SESSION['sessionID'];

	$today = date("Y-m-d");

	$keys = array_keys($_REQUEST);
	foreach($keys as $key)
	{
		${$key} = $_REQUEST[$key] ;
	}
	$user = new User($userID);
	$childName  = $user->childName;
	$Name = explode(" ", $childName);
	$Name = $Name[0];
	$childClass = $user->childClass;
	if($_SESSION['timePerDay']=="")
		$timePerDay = 40;
	else
		$timePerDay = 30;
?>

<?php include("header.php"); ?>

<title>End Session Report</title>

<?php
	if($theme==1) { ?>
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/revisionSessionReport/lowerClass.css" rel="stylesheet" type="text/css">
<?php } else if($theme==2) { ?>
    <link rel="stylesheet" href="css/commonMidClass.css" />
    <link rel="stylesheet" href="css/revisionSessionReport/midClass.css" />
<?php } else { ?>
    <link rel="stylesheet" href="css/commonHigherClass.css" />
    <link rel="stylesheet" href="css/revisionSessionReport/higherClass.css" />
<?php } ?>
<link rel="stylesheet" type="text/css" href="css/colorbox.css">
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
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
		document.getElementById('hidqcode').value = qcode;
		document.getElementById('hidqno').value = qno;
		document.getElementById('hidsrno').value = srno;
		document.getElementById('frmReport').submit();
	}
	function init()
	{
		document.cookie = 'SHTS=;';
		document.cookie = 'SHTSP=;';
		document.cookie = 'SHTParams=;';
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
	window.location.href	=	"home.php";
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
function renew(userID){
    window.open("http://mindspark.in/registration.php?userID="+userID,"_newtab");
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
<body class="translation" onLoad="load();init();" onResize="load()" >
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
							<!--		<li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li> -->
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

	$query  = "SELECT noOfQuestions, perCorrect, noOfSparkies FROM adepts_revisionSessionStatus WHERE revisionSessionID=$revisionSessionID AND userID=$userID";
	$result = mysql_query($query);
	$line   = mysql_fetch_array($result);
	$questionsAttempted = $line[0];
	$percentageCorrect  = $line[1];
	$noOfSparkies       = $line[2];
	$msg = "You have completed the revision session!<br/>";
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
            <div id="session">
                <span data-i18n="common.sessionID"></span>: <font color="#39a9e0"><?=$sessionID?></font>
            </div>
            <div id="duration">
                <span>No. of questions attempted</span>: <font color="#39a9e0"><?=$questionsAttempted?></font>
            </div>
			<div id="duration">
                <span>Percentage correct</span>: <font color="#39a9e0"><?=$percentageCorrect?>%</font>
            </div>
            <div id="sparkieInfo">
                <span><?php if($childClass<8) { ?>
	     		Sparkies in this session:
			     	<?php } else  { ?>
			     		Reward Points in this session
			     	<?php } ?></span><font color="#39a9e0">
			     	<?php
			     		if($childClass<8)
			     			echo $noOfSparkies;
			     		else
			     			echo "<span style='font-size:1.2em;'>".$noOfSparkies*10 ."</span>";
			     	?></font>
            </div>
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
                <span>No. of questions attempted</span>: <font id="sessionColor"><?=$questionsAttempted?></font>
            </div>
			<div id="duration">
                <span>Percentage correct</span>: <font id="sessionColor"><?=$percentageCorrect?>%</font>
            </div>
            <div id="sparkieInfo">
                <span><?php if($childClass<8) { ?>
	     		Sparkies in this session:
			     	<?php } else  { ?>
			     		Reward Points in this session :
			     	<?php } ?></span><font id="sessionColor">
			     	<?php
			     		if($childClass<8)
			     			echo $noOfSparkies;
			     		else
			     			echo "<span style='font-size:1.2em;'>".$noOfSparkies*10 ."</span>";
			     	?></font>
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

		if($mode=="refresh") {
			echo "<div align='center'>";
			echo "It seems you have pressed refresh.<br/>Kindly <a href='logout.php'>login</a> again!";
			echo "</div>";
		}
		else if ($mode== -7 )	{
			echo "<div align='center'><br/>";
			echo "You have completed your ".$timePerDay." minute session! We hope you enjoyed it.<br/>You can either re-login later or come back tomorrow.";
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
            <div id="detail_bar" class="forLowerOnly hidden">
            	 <div id="session">
                    <span data-i18n="common.sessionID" class="fontWeight"></span> : <font color="#39a9e0"><?=$sessionID?></font>
                </div>
                <div id="duration">
                <span>No. of questions attempted</span>: <font color="#39a9e0"><?=$questionsAttempted?></font>
            </div>
			<div id="duration">
                <span>Percentage correct</span>: <font color="#39a9e0"><?=$percentageCorrect?>%</font>
            </div>
            <div id="sparkieInfo">
                <span><?php if($childClass<8) { ?>
	     		Sparkies in this session:
			     	<?php } else  { ?>
			     		Reward Points in this session
			     	<?php } ?></span><font color="#39a9e0">
			     	<?php
			     		if($childClass<8)
			     			echo $noOfSparkies;
			     		else
			     			echo "<span style='font-size:1.2em;'>".$noOfSparkies*10 ."</span>";
			     	?></font>
            </div>
                <!--<div id="totalQuestionCorrect">
                	<span id="totalQuestionCorrectText" data-i18n="endSessionReportPage.quesAnsweredCorrectly"></span>:
                	<span id="totalQuestionCorrectDigit"><?=$score?></span>
				</div>-->
                <div class="clear"></div>
                <a href="sessionWiseReport.php?mode=<?=$mode?>" class="removeDecoration">
                <div id="prevReportImote">
                    <div id="prevReportText" data-i18n="endSessionReportPage.prevReport"></div>
                </div></a>
	        </div>

            <div id="dataTableDiv">
                <table width="100%" border="0" class="endSessionTbl" align="center">
                    <tr class="trHead">
                        <th>Q No</th>
						<th>Selected Ans</th>
						<th>Correct Ans</th>
						<th>Result</th>
                    </tr>
                    <tr class="forLowerOnly"><td colspan="4" class="yellowBackground"></td></tr>
                    <tr class="forLowerOnly"><td colspan="4" class="forLowerOnly"></td></tr>
		<?php

	$totalTime  = 0;
	$totalScore = 0;

	$query= "SELECT srno, questionNo, qcode, A, S, R
			 FROM   adepts_revisionSessionDetails
			 WHERE  userID=".$userID." AND revisionSessionID=$revisionSessionID
			 ORDER BY questionNo";
	$result=mysql_query($query) or die(mysql_error());
	while($line=mysql_fetch_array($result))
	{


		$totalScore += $line['R'];
		$totalTime  += $line['S'];

		$qcode = $line['qcode'];
		//fetch correct ans for each question
		$question = new Question($qcode);
		if($question->isDynamic())
		{
			$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE class=$childClass AND mode='revision' AND quesAttempt_srno= ".$line['srno'];
			$dynamic_result = mysql_query($query);
			$dynamic_line   = mysql_fetch_array($dynamic_result);
			$question->generateQuestion("answer",$dynamic_line[0]);
		}
		$correctAns = $question->getCorrectAnswerForDisplay()
?>
		<tr>
			<td align='center'><a onclick='showQues(<?=$qcode?>,<?=$line['questionNo']?>,<?=$line['srno']?>)' href='#' style="text-decoration:underline;color:blue;"><?=$line['questionNo']?></a></td>
			<td align="center"><?=$line['A']==""?"N.A.":$line['A']?></td>
			<td align="center"><?=$correctAns==""?"N.A.":$correctAns?></td>
			<?php if($line['R']==1) {?>
			<td align="center"><img src="assets/right.gif" alt="Right" width="20px"></td>
			<?php } else { ?>
			<td align='center'><img src='assets/wrong.gif' alt='Wrong' width='15px'></td>
			<?php } ?>
		</tr>
<?php
	}
?>
            </table>
            </div>
        </div>
	</div>

    <form id="frmReport" action="quesWiseReport.php" method="POST">
        <input type="hidden" name="userID" id="userID" value="<?=$userID?>">
        <input type="hidden" name="qcode" id="hidqcode">
        <input type="hidden" name="srno" id="hidsrno">
        <input type="hidden" name="qno" id="hidqno">
        <input type="hidden" name="bucketAttemptID" id="bucketAttemptID" value="<?=isset($_SESSION['bucketAttemptID'])?$_SESSION['bucketAttemptID']:""?>">
        <input type="hidden" name="mode" id="mode" value="revision">
    </form>
	

<?php 
$parentFileNameStr = explode("/",$_SERVER['HTTP_REFERER']);
$parentFileNameStr1 = array_pop($parentFileNameStr);
$parentFileNameStr2 = explode("?",$parentFileNameStr1);
$parentFileNameStr = $parentFileNameStr2[0];
//echo $parentFileNameStr;

if($parentFileNameStr=="controller.php")
{
	mysql_query("UPDATE ".TBL_SESSION_STATUS." SET endType=concat_ws(',',endType,'S3(".$totalQuestions.")') WHERE sessionID=".$sessionID) or die(mysql_error());
}

include("footer.php"); ?>

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
?>