<?php
include("check1.php");
include("constants.php");
include("classes/clsUser.php");
include ("functions/functionsForDynamicQues.php");
include ("functions/functions.php");
include("classes/clsdaTest.php");
include("functions/orig2htm.php");

if(!isset($_SESSION['userID']))
{
	header("Location:error.php");
	exit;
}

$userID = $_SESSION['userID'];
//error_reporting(E_ERROR);
$user = new User($userID);
$Name = explode(" ", $_SESSION['childName']);
$Name = $Name[0];
$childName 	   = $user->childName;
$schoolCode    = $user->schoolCode;
$childClass    = $user->childClass;
$childSection  = $user->childSection;
$category 	   = $user->category;
$subcategory   = $user->subcategory;
$endDate 	   = $user->endDate;

if(isset($_POST["daPaperCode"]))
	$daPaperCode = $_POST["daPaperCode"];
else if(isset($_SESSION['daPaperCode']))
	$daPaperCode = $_SESSION['daPaperCode'];
//echo $mode;
//$_SESSION['total_questions']=$_SESSION['maxQues'];

$today = date("Y-m-d");
$ttAttemptID = $_SESSION['teacherTopicAttemptID'];
$keys = array_keys($_REQUEST);
foreach($keys as $key)
{
	${$key} = $_REQUEST[$key];
}
if(isset($_POST['sessionID']))
	$sessionID = $_POST['sessionID'];
else
	$sessionID	= $_SESSION['sessionID'];

if(!isset($mode))
		$mode = "";
$teacherTopicCode = $_SESSION['teacherTopicCode'];
$query = "SELECT teacherTopicDesc FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$teacherTopicCode'";
$result = mysql_query($query);
$line = mysql_fetch_array($result);
$teacherTopicName = $line[0];
?>

<?php include("header.php"); ?>

<title>Super Test Report</title>

<?php if($theme==1) { ?>
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/topicRevisionReport/lowerClass.css" rel="stylesheet" type="text/css">
<?php } else if($theme==2){ ?>
    <link rel="stylesheet" href="css/commonMidClass.css" />
    <link rel="stylesheet" href="css/topicRevisionReport/midClass.css" />
<?php } else { ?>
    <link rel="stylesheet" href="css/commonHigherClass.css" />
    <link rel="stylesheet" href="css/topicRevisionReport/higherClass.css?version=1.0" />
<?php } ?>
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script>

var langType = '<?=$language;?>';
function load() {
<?php if($theme==1) { ?>	
	var a= window.innerHeight - (47 + 70 + 55 + 30);
	$('#endSessionDataDivMain').css("height",a+"px");
	$(".forHigherOnly").remove();
<?php } else if($theme==2){ ?>
	var a= window.innerHeight - (80 + 25 + 140 );
	$('#endSessionDataDivMain').css("height",a+"px");
	$(".forLowerOnly").remove();
<?php } else if($theme==3) { ?>
			var a= window.innerHeight - (170);
			var b= window.innerHeight - (610);
			$('#dataTableDiv').css({"height":a+"px","margin-top":"0px"});
			$('#sideBar').css({"height":a+"px"});
			$('#main_bar').css({"height":a+"px"});
			$('#menubar').css({"height":a+"px"});
		<?php } ?>
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
function showDaQues(qcode, qno, srno)	{
	document.getElementById("hidqcode").value = qcode;
	document.getElementById("hidqno").value = qno;
	document.getElementById("hidsrno").value = srno;
	document.getElementById("frmReport").submit();
}
function renew(userID){
	window.open("https://mindspark.in/registration.php?userID="+userID,"_newtab");
}
</script>

</head>
<body class="translation" onLoad="load()" onResize="load()" >
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
	
	if($mode=="refresh") {
							echo "<div align='center' style='color:#FF0000; font-size:1.3em;'>";
							echo "It seems you have pressed refresh.<br/>Kindly <a href='logout.php'>login</a> again!";
							echo "</div>";
						}
						elseif ($mode== -7 )	{
							echo "<div align='center' style='color:#FF0000; font-size:1.3em;'><br/>";
							echo "You have completed your 30 minute session! We hope you enjoyed it.<br/>You can either re-login later or come back tomorrow.";
							echo "</div>";
						}
						elseif ($mode== -5 )	{
							echo "<div align='center' style='color:#FF0000; font-size:1.3em;'><br/>";
							echo "You have completed your Mindspark quota for the week! <br/>You can login again tomorrow to enjoy Mindspark!";
							echo "</div>";
							echo "<div align=center><div align='left' style='width:92%; font-size:0.9em;'><br/>We have instituted a daily and weekly time limit mainly to ensure that you do Mindspark regularly without ignoring the other subjects. This has been done in consultation with the school authorities who would like you to progress at an even pace throughout the year.</div></div>";
						}
						elseif ($mode== -6 )	{
							echo "<div align='center' style='color:#FF0000; font-size:1.3em;'><br/>";
							echo "You have completed your session for the day!<br/>You can login again tomorrow to enjoy Mindspark!";
							echo "</div>";
							echo "<div align=center><div align='left' style='width:92%; font-size:0.9em;'><br/>We have instituted a daily and weekly time limit mainly to ensure that you do Mindspark regularly without ignoring the other subjects. This has been done in consultation with the school authorities who would like you to progress at an even pace throughout the year.</div></div>";
						}
						elseif ($mode== 6 )	{
							echo "<div align='center' style='color:#FF0000; font-size:1.3em;'><br/>";
							echo "Your session is timed out due to inactivity.<br/>Please login again to continue answering questions.";
							echo "</div>";
						}

	$quesAttemptedArray = array();

	$totalTime  = 0;
	$totalScore = 0;
	$totalQuestions =0;

	$query= "SELECT id, qno, qcode, A, R, S FROM da_questionAttemptDetails WHERE userID=".$userID." and paperCode = '".$daPaperCode."' ORDER BY qno";
	$result=mysql_query($query) or die(mysql_error().$sq);
	$srno = 0;
	$unAnswered = 0;
	while($line=mysql_fetch_array($result))
	{
		$quesAttemptedArray[$srno][0] = $line['id'];
		$quesAttemptedArray[$srno][1] = $line['qno'];
		$quesAttemptedArray[$srno][2] = $line['qcode'];
		$quesAttemptedArray[$srno][3] = $line['A'];
		$quesAttemptedArray[$srno][4] = $line['R'];
		$quesAttemptedArray[$srno][5] = $line['S'];

		$totalScore += $line["R"];
		$totalTime  += $line["S"];
		if($line['A']=="No Answer")
			$unAnswered++;

		//fetch correct ans for each question
		$question = new daTest($line['qcode']);
		$quesAttemptedArray[$srno][6] = $question->correctAnswer;
		$srno++;
		$totalQuestions++;
	}

	$sqTimeTaken = "SELECT spendTime, lastmodified FROM da_questionTestStatus WHERE userID=$userID and paperCode = '$daPaperCode' ";
	$rsTimeTaken = mysql_query($sqTimeTaken);
	$rwTimeTaken = mysql_fetch_array($rsTimeTaken);
	$timeSpent = intval((1800 - $rwTimeTaken["spendTime"])/60)." minutes";
	$testDate = date("d-m-Y",strtotime($rwTimeTaken["lastmodified"]));
	
	// function to get Best Performed area 
	
	$sqTopic = "SELECT topicName FROM da_paperCodeMaster WHERE paperCode = '$daPaperCode' ";
	$rsTopic = mysql_query($sqTopic);
	$rwTopic = mysql_fetch_array($rsTopic);
	$daTopicName = $rwTopic["topicName"];
	list($bestArea,$worstArea) = getPerformanceArea($daPaperCode,$userID);
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
                    <div id="homeIcon" onClick="getHome()"></div>
                    <div id="homeText" class="forHigherOnly"><span onClick="getHome()" class="textUppercase linkPointer" data-i18n="dashboardPage.home"></span> > <font color="#606062"> <span class="textUppercase"><a href='daTestTopic.php' class="removeDecoration">Super Test Report</a></span></font> > <font color="#606062"> <span class="textUppercase"><?=$daTopicName?></span></font></div>
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
			<!--<a class="removeDecoration" href="daTestTopic.php"><div  class="textUppercase" id="sessionWiseReport">Back to Topic List</div></a>-->
            <div class="clear"></div>
            <div id="session">
                <span data-i18n="common.sessionID"></span>: <font color="#39a9e0"><?=$sessionID?></font>
            </div>
			<div id="session">
                <span>Time spent</span>: <font color="#39a9e0"><?=$timeSpent?></font>
            </div>
			<div id="session">
                <span>Test Date</span>: <font color="#39a9e0"><?=$testDate?></font>
            </div>

		</div>
		
		<div id="info_bar" class="forHighestOnly">
				<a href="dashboard.php" style="text-decoration:none;color:inherit"><div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase" data-i18n="dashboardPage.dashboard"></span></div>
                </div></a>
				<div class="arrow-right"></div>
				<div id="endSessionHeading">End Session Report</div>
				<div id="session">
                    <span data-i18n="common.sessionID"></span>: <font id="sessionColor"><?=$sessionID?></font>
                </div>
				<div class="clear"></div>
			</div>
        <div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
				<a href="sessionWiseReport.php">
				<div id="report">
					<span id="reportText">SESSION WISE REPORT</span>
					<div id="reportIcon" class="circle"><div class="arrow-s"></div></div>
				</div></a>
				<div class="empty">
				</div>
			</div>
			</div>
        <div id="endSessionDataDivMain">
        <?php
		if($mode!=-5 && $mode!=-6 && $mode!=-7 && $mode!="refresh" && $mode!=6) 	{ //Show this link if the daily/weekly/session limit has not been completed
		//pending for showing home button or not
			/*echo	'<a href="controller.php?mode=topicSwitch" class="home bcrumb"></a>
					<div class="separator"></div>
					<a href="endSessionReport.php" class="scorecarded bcrumb"></a>';*/
		}
	
		if($mode=="refresh") {
			echo "<div align='center'>";
			echo "It seems you have pressed refresh.<br/>Kindly <a href='logout.php'>login</a> again!";
			echo "</div>";
		}
		else if ($mode== -7 )	{
			echo "<div align='center'><br/>";
			echo "You have completed your 30 minute session! We hope you enjoyed it.<br/>You can either re-login later or come back tomorrow.";
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
            <!--<div id="sessionInfo" class="forHigherOnly hidden">
                <div id="totalQuestion" style="font-size:16px">
                    <span id="totalQuestionText" data-i18n="endSessionReportPage.quesAnswered"></span>: 
                    <span id="totalQuestionDigit"><?=$totalQuestions?></span>
                </div>
				<div id="totalQuestion" style="margin-left:4%;font-size:16px">
                    <span id="totalQuestionCorrectText">Correct</span>: <?=$totalScore?></span>
                    <span id="totalQuestionCorrectDigit">
                </div>
				<div id="totalQuestion" style="margin-left:4%;font-size:16px">
                    <span id="totalQuestionCorrectText">Incorrect</span>: 
                    <span id="totalQuestionCorrectDigit"><?=$totalQuestions - ($totalScore+$unAnswered)?></span>
                </div>
				<div id="totalQuestion" style="margin-left:4%;font-size:16px">
                    <span id="totalQuestionCorrectText">Unanswered</span>: 
                    <span id="totalQuestionCorrectDigit"><?=$unAnswered?></span>
                </div>
				<div id="totalQuestion" style="margin-left:4%;font-size:16px">
                    <span id="totalQuestionText">Time Spent</span>: 
                    <span id="totalQuestionDigit"><?=$timeSpent?></span>
                </div>
                <div class="clear"></div>
            </div>-->
            <div id="detail_bar" class="forLowerOnly hidden">
            	 <div id="session">
                    <span data-i18n="common.sessionID"></span>: <font color="#39a9e0"><?=$sessionID?></font>
                </div>
                <div id="duration">
                    <span data-i18n="endSessionReportPage.currentSessionTime"></span>: <font color="#39a9e0"><?=convertSecs($totalTime)?> <span data-i18n="endSessionReportPage.minutes"></span></font>
                </div>
                <div id="sparkieInfo">
                    <span data-i18n="[html]endSessionReportPage.sparkieEarned"></span>: <?=$sparkiesInThisSession?>
                </div>
                <div class="clear"></div>
                <div id="totalQuestion">
                	<span id="totalQuestionText" data-i18n="endSessionReportPage.quesAnswered"></span>:
                	<span id="totalQuestionDigit"><?=$totalQuestions?></span>
				</div>
                <div id="totalQuestionCorrect">
                	<span id="totalQuestionCorrectText" data-i18n="endSessionReportPage.quesAnsweredCorrectly"></span>:
                	<span id="totalQuestionCorrectDigit"><?=$totalScore?></span>
				</div>
                <div class="clear"></div>
                <!--<div id="challengeQues">
                	<span id="challengeQuesText" data-i18n="endSessionReportPage.challengeQues"></span>: 
                	<span id="challengeQuesAttemptedText" data-i18n="endSessionReportPage.attempted"></span> - 
                    <span id="challengeQuesAttemptedDigit"><?=$challengeQuesAttempted?></span>
                    <span id="challengeQuesCorrectText" data-i18n="endSessionReportPage.correct"></span> - 
                    <span id="challengeQuesCorrectDigit"><?=$challengeQuesCorrect?></span>
				</div>-->
                <a href="sessionWiseReport.php?mode=<?=$mode?>">
                <div id="prevReportImote">
                    <div id="prevReportText" data-i18n="endSessionReportPage.prevReport"></div>
                </div></a>
	        </div>
            
            <div id="dataTableDiv">
			<br>
				<table width="70%" border="0" cellspacing="3" cellpadding="3" align="center" style="font-size:18px;vertical-align:top;border:1px solid #e75903">
					<tr>
						<td style="border-right:1px solid #e75903;border-bottom:1px solid #e75903"><b>Super test score: </b><?=round(($totalScore/$totalQuestions)*10,1)?>/10</td>
                        <td style="border-right:1px solid #e75903;border-bottom:1px solid #e75903"><b>Correct: </b><?=$totalScore?>/<?=$totalQuestions?></td>
                        <td style="border-right:1px solid #e75903;border-bottom:1px solid #e75903"><b>Incorrect: </b><?=$totalQuestions - ($totalScore+$unAnswered)?>/<?=$totalQuestions?></td>
                        <td style="border-bottom:1px solid #e75903;"><b>Unanswered: </b><?=$unAnswered?>/<?=$totalQuestions?></td>
                    </tr>
                    <tr>
                        
                        <td style="border-right:1px solid #e75903;vertical-align:top" colspan="2"><b>Best Performed Area: </b><br><?php if($bestArea!="") echo $bestArea; else echo "Oh no! There isn't any best area of performance."?></td>
                        <td colspan="2" style="vertical-align:top"><b>Area(s) for improvement: </b><br><?php if($worstArea!="") echo $worstArea; else echo "Hurray! We could not find any area for improvement based on this test."?></td>
                    </tr>
				</table>
				
                <table width="100%" border="0" class="endSessionTbl" align="center">
                    <tr class="trHead">
                        <td>S. No</td>
                        <td>My Answer</td>
                        <td>Correct Answer</td>
                        <td>Result</td>
                    </tr>
                    <tr class="forLowerOnly"><td colspan="4" class="yellowBackground"></td></tr>
                    <tr class="forLowerOnly"><td colspan="4" class="forLowerOnly"></td></tr>
		<?php //echo "<pre>"; print_r($resArr); echo "</pre>"; 
				
			foreach( $quesAttemptedArray as $key=>$val) { ?>
            <tr>
                <td><a style="text-decoration:underline;color:blue;" href="javascript:showDaQues('<?=$val[2]?>','<?=$val[1]?>','<?=$val[0]?>')"><?=$val[1]?></a></td>
                <td width="280px"><?=$val[3]?></td>
                <td width="280px"><?=$val[6]?></td>
                <td align="center"><div class="<?php if($val[4]==0) echo 'wrongAnsIcon'; else echo 'correctAnsIcon';?>"></div></td>
            </tr>
			<?php } ?>
                </table>
            </div>
        </div>
	</div>
    
    <form id="frmReport" action="daQuesWiseReport.php" method="POST">
        <input type="hidden" name="userID" id="userID" value="<?=$userID?>">
        <input type="hidden" name="sessionIDNew" id="sessionIDNew" value="<?=$sessionID?>">
		<input type="hidden" name="reportDate" id="reportDate" value="<?=$reportDate?>">
		<input type="hidden" name="modeCheck" id="modeCheck" value="2">
        <input type="hidden" name="qcode" id="hidqcode">
        <input type="hidden" name="srno" id="hidsrno">
        <input type="hidden" name="qno" id="hidqno">
        <input type="hidden" name="mode" id="mode" value="daTest">
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
		mysql_query("UPDATE ".TBL_SESSION_STATUS." SET endType=concat_ws(',',endType,'S2(".$totalQuestions.")') WHERE sessionID=".$sessionID);
	else
		mysql_query("UPDATE ".TBL_SESSION_STATUS." SET endType=concat_ws(',',endType,'".$endType."','S2(".$totalQuestions.")') WHERE sessionID=".$sessionID);
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

function getClusterdesc($clusterCode)
{
	$sq	=	"SELECT cluster FROM adepts_clusterMaster WHERE clusterCode='$clusterCode'";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	return $rw[0];
}

function getPerformanceArea($daPaperCode,$userID)
{
	$topicwiseperformance = array();
	$sqReportingDetails = "SELECT qcode_list, reporting_head FROM educatio_educat.da_reportingDetails WHERE paperCode='$daPaperCode'";
	$rsReportingDetails = mysql_query($sqReportingDetails);
	while($rwReportingDetails = mysql_fetch_array($rsReportingDetails))
	{
		$sqPerformance = "SELECT round((SUM(R)/COUNT(id))*100) FROM da_questionAttemptDetails
						  WHERE qcode IN (".$rwReportingDetails[0].") AND papercode='$daPaperCode' AND userID=$userID";
		$rsPerformance = mysql_query($sqPerformance);
		$rwPerformance = mysql_fetch_array($rsPerformance);
		$topicwiseperformance[$rwReportingDetails[1]] = $rwPerformance[0];
	}
	$max = 2;
	foreach($topicwiseperformance as $topic => $performance) {
		if(is_numeric($performance)){
			$StudentTotalPerfo += $performance;

			if(count($topicwiseperformance) != 1){ // For only one reporting head we dont need to show top performance
				if($performance > $max && $performance >= 70) {
					$studentbesttopic = $topic;
					$max = $performance;
				}
			}
			if($performance < 70){
				$StudentWorstTopicArr[] = array("srno"=>$i,"topicid"=>$topic,"performance"=>$performance);
			}
			$topiccount++;
		}
		$i++;
	}
	if(is_array($StudentWorstTopicArr) && count($StudentWorstTopicArr) > 0){

		foreach ($StudentWorstTopicArr as $key => $arrayrow) {
			$srno_arr[$key]  = $arrayrow['srno'];
			$performance_arr[$key] = $arrayrow['performance'];
		}

		array_multisort($performance_arr, SORT_ASC, $srno_arr, SORT_ASC, $StudentWorstTopicArr);

		$worsttopicdispcnt = 0;
		foreach ($StudentWorstTopicArr as $key => $arrayrow) {
			$worsttopicdispcnt++;
			$studentworsttopicstr .="- ".$arrayrow["topicid"]." <br/> ";
			if($worsttopicdispcnt == 2)
			break;
		}
	}
	if($studentworsttopicstr != "")
		$studentworsttopicstr =" <br/>".$studentworsttopicstr;
	if($studentbesttopic != '')
		$bestArea = " <br/>- ".$studentbesttopic;
	return array($bestArea,$studentworsttopicstr);
}

?>