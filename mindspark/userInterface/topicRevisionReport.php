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
if($_SESSION['timePerDay']=="")
	$timePerDay = 40;
else
	$timePerDay = 30;
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
$sparkiesInThisSession = $_SESSION['noOfJumps'];
?>

<?php include("header.php"); ?>

<title>End Session Report</title>

<?php if($theme==1) { ?>
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/topicRevisionReport/lowerClass.css" rel="stylesheet" type="text/css">
<?php } else if($theme==2){ ?>
    <link rel="stylesheet" href="css/commonMidClass.css" />
    <link rel="stylesheet" href="css/topicRevisionReport/midClass.css" />
<?php } else { ?>
    <link rel="stylesheet" href="css/commonHigherClass.css" />
    <link rel="stylesheet" href="css/topicRevisionReport/higherClass.css?ver=1" />
<?php } ?>
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
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
			$('#dataTableDiv').css({"height":a+"px"});
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
function renew(userID){
    window.open("http://mindspark.in/registration.php?userID="+userID,"_newtab");
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
							echo "You have completed your ".$timePerDay." minute session! We hope you enjoyed it.<br/>You can either re-login later or come back tomorrow.";
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

	$query= "SELECT   srno, questionNo, qcode, A, R, S
	         FROM     ".TBL_TOPIC_REVISION."
	         WHERE    userID=".$userID." AND sessionID=".$sessionID." AND teacherTopicCode='$teacherTopicCode'
	         ORDER BY lastModified,questionNo";
	$result=mysql_query($query) or die(mysql_error());
	$srno = 0;
	while($line=mysql_fetch_array($result))
	{
			$quesAttemptedArray[$srno][0] = $line['srno'];
			$quesAttemptedArray[$srno][1] = $line['questionNo'];
			$quesAttemptedArray[$srno][2] = $line['qcode'];
			$quesAttemptedArray[$srno][3] = $line['A'];
			$quesAttemptedArray[$srno][4] = $line['R'];
			$quesAttemptedArray[$srno][5] = $line['S'];

			$totalScore += $line["R"];
			$totalTime  += $line["S"];

			//fetch correct ans for each question
			$question = new Question($line['qcode']);
			if($question->isDynamic())
			{
				$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE mode='topicRevision' AND class=$childClass AND quesAttempt_srno= ".$line['srno'];
				$dynamic_result = mysql_query($query);
				$dynamic_line   = mysql_fetch_array($dynamic_result);
				$question->generateQuestion("answer",$dynamic_line[0]);
			}
			$quesAttemptedArray[$srno][6] = $question->getCorrectAnswerForDisplay();
			$srno++;
			$totalQuestions++;
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
                    <div id="homeIcon" onClick="getHome()"></div>
                    <div id="homeText" class="forHigherOnly"><span onClick="getHome()" class="textUppercase linkPointer" data-i18n="dashboardPage.home"></span> > <font color="#606062"><a class="removeDecoration" href="sessionWiseReport.php" data-i18n="sessionWiseReportPage.sessionWiseReport"></a></font> > <font color="#606062"> <span class="textUppercase" data-i18n="endSessionReportPage.scoreCard"></span></font></div>
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
             <a href="sessionWiseReport.php" class="removeDecoration"><div id="sessionWiseReport" class="textUppercase" data-i18n="dashboardPage.sessionWise"></div></a>
            <div class="clear"></div>
            <div id="session">
                <span data-i18n="common.sessionID"></span>: <font color="#39a9e0"><?=$sessionID?></font>
            </div>
            <div id="duration">
                <span data-i18n="endSessionReportPage.currentSessionTime"></span>: <font color="#39a9e0"><?=convertSecs($totalTime)?> <span data-i18n="endSessionReportPage.minutes"></span></font>
            </div>
            <div id="sparkieInfo">
                <span data-i18n="[html]endSessionReportPage.sparkieEarned"></span>: <?=$sparkiesInThisSession?>
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
                <div id="sparkieInfo">
                    <span data-i18n="[html]endSessionReportPage.sparkieEarned"></span>: <?=$sparkiesInThisSession?>
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
			echo "You have completed your 40 minute session! We hope you enjoyed it.<br/>You can either re-login later or come back tomorrow.";
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
                    <span id="totalQuestionText" data-i18n="endSessionReportPage.quesAnswered"></span>: 
                    <span id="totalQuestionDigit"><?=$totalQuestions?></span>
                </div>
                <div id="totalQuestionCorrect">
                    <span id="totalQuestionCorrectText" data-i18n="endSessionReportPage.quesAnsweredCorrectly"></span>: 
                    <span id="totalQuestionCorrectDigit"><?=$totalScore?></span>
                </div>
                <div class="clear"></div>
            </div>
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
                <td><a style="text-decoration:underline;color:blue;" href="javascript:showQues('<?=$val[2]?>','<?=$val[1]?>','<?=$val[0]?>')"><?=$val[1]?></a></td>
                <td width="280px"><?=$val[3]?></td>
                <td width="280px"><?=$val[6]?></td>
                <td align="center"><div class="<?php if($val[4]==0) echo 'wrongAnsIcon'; else echo 'correctAnsIcon';?>"></div></td>
            </tr>
			<?php } ?>
                </table>
            </div>
        </div>
	</div>
    
    <form id="frmReport" action="quesWiseReport.php" method="POST">
        <input type="hidden" name="userID" id="userID" value="<?=$userID?>">
        <input type="hidden" name="qcode" id="hidqcode">
        <input type="hidden" name="srno" id="hidsrno">
        <input type="hidden" name="qno" id="hidqno">
        <input type="hidden" name="mode" id="mode" value="topicRevision">
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

?>