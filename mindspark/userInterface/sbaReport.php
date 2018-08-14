<?php
include("check1.php");
include("constants.php");
include("classes/clsUser.php");
include ("functions/functionsForDynamicQues.php");
include ("functions/functions.php");
include("classes/clsSbaQuestion.php");
include("functions/orig2htm.php");
include_once("functions/sbaTestFunctions.php");
if(!isset($_SESSION['userID']))
{
	header("Location:error.php");
	exit;
}
if(!isset($_REQUEST['sbaTestID']) || $_REQUEST['sbaTestID']=="")
{
	header("Location:home.php");
	exit;
}
$userID = $_SESSION['userID'];
//error_reporting(E_ERROR);
$user = new User($userID);
$Name = explode(" ", $_SESSION['childName']);
$Name = $Name[0];
$objUser = new User($userID);
$childName 	   = $user->childName;
$schoolCode    = $user->schoolCode;
$childClass    = $user->childClass;
$childSection  = $user->childSection;
$category 	   = $user->category;
$subcategory   = $user->subcategory;
$endDate 	   = $user->endDate;

$keys = array_keys($_REQUEST);
foreach($keys as $key)
{
	${$key} = $_REQUEST[$key];
}
$sbaAttemptDetails	=	getSbaAttemptDetails($userID,$sbaTestID);
if($sbaAttemptDetails=="")
{
	header("Location:home.php");
	exit;
}
else
{
	$resArr	=	getSbaQuesDetails($userID,$sbaTestID);
	$totalAttempted	=	array_pop($resArr);
}
?>

<?php include("header.php"); ?>

<title>Test Report</title>

<?php
	if($theme==1) { ?>
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/endSesssionReport/lowerClass.css" rel="stylesheet" type="text/css">
<?php } else if($theme==2) { ?>
    <link rel="stylesheet" href="css/commonMidClass.css" />
    <link rel="stylesheet" href="css/endSesssionReport/midClass.css" />
<?php } else { ?>
    <link rel="stylesheet" href="css/commonHigherClass.css" />
    <link rel="stylesheet" href="css/endSesssionReport/higherClass.css" />
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
			$('#endSessionDataDivMain').css({"height":a+"px"});
			$('#sideBar').css({"height":a+"px"});
			$('#main_bar').css({"height":a+"px"});
			$('#menubar').css({"height":a+"px"});
		<?php } ?>
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
</style>
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
								<!--	<li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li> -->
									<li><a href='changePassword.php'><span data-i18n="homePage.changePassword"></span></a></li>
                                    <li><a href='whatsNew.php'><span data-i18n="common.whatsNew"></span></a></li>
									<li><a href='javascript:void(0)'><span data-i18n="common.help"></span></a></li>
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
    	<div id="info_bar" class="forLowerOnly hidden">
        	<div id="blankWhiteSpace"></div>
             <div id="home">
                <div id="homeIcon" class="linkPointer"<?php if($deactiveLinks!=1) { ?> onClick="getHome() <?php } ?>"></div>
                 <div id="dashboardHeading" class="forLowerOnly"> - <span class="textUppercase" >ASSESMENT REPORT</span></div>
                <div class="clear"></div>
            </div>
        </div>
		<div id="info_bar" class="forHigherOnly hidden">
			<div id="topic">
				<div id="home">
                    <div id="homeIcon"<?php if($deactiveLinks!=1) { ?> onClick="getHome() <?php } ?>"></div>
                    <div id="homeText" class="forHigherOnly"><span<?php if($deactiveLinks!=1) { ?> onClick="getHome() <?php } ?>" class="textUppercase linkPointer" data-i18n="dashboardPage.home"></span> > <font color="#606062"> <span class="textUppercase">ASSESMENT REPORT</span></font></div>
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
            <div class="clear"></div>
		</div>
        <div id="info_bar" class="forHighestOnly">
				<a href="dashboard.php" style="text-decoration:none;color:inherit"><div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase" data-i18n="dashboardPage.dashboard"></span></div>
                </div></a>
				<div class="arrow-right"></div>
				<div id="endSessionHeading">ASSESMENT REPORT</div>
				<div id="challengeQues">
					TIME
                    <span class="correct_bar">TOTAL : <?=$sbaAttemptDetails["maxTime"]?> min &nbsp;&nbsp; TAKEN : <?=convertSecs($sbaAttemptDetails["timeTaken"])?></span>
                </div>
                <div id="totalQuestionCorrect">
                    QUESTIONS
                    <span class="correct_bar">CORRECT : <?=$sbaAttemptDetails["score"]?> &nbsp;&nbsp; ATTEMPTED : <?=$sbaAttemptDetails["totalQues"]?></span>
                </div>
				<div class="clear"></div>
			</div>
		</div>
        <div id="endSessionDataDivMain">
		<div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
			</div>
			</div>
        <?php

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
                    <span id="totalQuestionText" data-i18n="endSessionReportPage.quesAnswered">Total Questions</span>:
                    <span id="totalQuestionDigit"><?=$sbaAttemptDetails["totalQues"]?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span id="challengeQuesAttemptedText" data-i18n="endSessionReportPage.attempted"></span> :
                    <span id="totalQuestionDigit"><?=$totalAttempted?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span id="challengeQuesCorrectText" data-i18n="endSessionReportPage.correct"></span> :
                    <span id="totalQuestionCorrectDigit"><?=$sbaAttemptDetails["score"]?></span>
                </div>
                <div id="totalQuestionCorrect">
                    <span id="timeTakenQuesText">Total time</span>:
                    <span id="timeTakenQuesDigit"><?=$sbaAttemptDetails["maxTime"]?></span>
                    <span data-i18n="endSessionReportPage.minutes"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span id="timeTakenQuesText">Time taken</span>:
                    <span id="timeTakenQuesDigit"><?=convertSecs($sbaAttemptDetails["timeTaken"])?></span>
                    <span data-i18n="endSessionReportPage.minutes"></span>
                </div>
                <!--<div id="totalQuestionCorrect">
                    <span id="totalQuestionCorrectText" data-i18n="endSessionReportPage.quesAnsweredCorrectly"></span>:
                    <span id="totalQuestionCorrectDigit"><?=$score?></span>
                </div>-->

                <div class="clear"></div>
            </div>
            <div id="detail_bar" class="forLowerOnly hidden">
            	<div id="totalQuestion">
                    <span id="totalQuestionText" data-i18n="endSessionReportPage.quesAnswered">Total Questions</span>:
                    <span id="totalQuestionDigit"><?=$sbaAttemptDetails["totalQues"]?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span id="challengeQuesAttemptedText" data-i18n="endSessionReportPage.attempted"></span> :
                    <span id="totalQuestionDigit"><?=$totalAttempted?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span id="challengeQuesCorrectText" data-i18n="endSessionReportPage.correct"></span> :
                    <span id="totalQuestionCorrectDigit"><?=$sbaAttemptDetails["score"]?></span>
                </div>
                <div id="totalQuestionCorrect">
                    <span id="totalQuestionText">Total time</span>:
                    <span id="totalQuestionDigit"><?=$sbaAttemptDetails["maxTime"]?></span>
                    <span data-i18n="endSessionReportPage.minutes"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span id="challengeQuesAttemptedText">Time taken</span>:
                    <span id="totalQuestionCorrectDigit"><?=convertSecs($sbaAttemptDetails["timeTaken"])?></span>
                    <span data-i18n="endSessionReportPage.minutes"></span>
                </div>
                
                <div class="clear"></div>
	        </div>

            <div id="dataTableDiv">
                <table width="<?php if($theme==3)  echo "100%"; else echo "90%"; ?>" border="0" class="endSessionTbl" align="center">
                    <tr class="trHead">
                        <td>S. No</td>
                        <td>My Answer</td>
                        <td >Correct Answer</td>
                        <td>Result</td>
                    </tr>
                    <tr class="forLowerOnly"><td colspan="4" class="yellowBackground"></td></tr>
                    <tr class="forLowerOnly"><td colspan="4" class="forLowerOnly"></td></tr>
		<?php //echo "<pre>"; print_r($resArr); echo "</pre>";

			foreach( $resArr as $val) {
				 ?>
            <tr>
                <td ><?=$val[1]?></td>
                <td ><?=$val[3]?></td>
                <td ><?=$val[4]?></td>
                <td align="center"><div class="<?php if($val[5]==0) echo 'wrongAnsIcon'; else echo 'correctAnsIcon';?>"></div></td>
            </tr>
			<?php } ?>
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
        <input type="hidden" name="qcode" id="hidqcode">
        <input type="hidden" name="srno" id="hidsrno">
        <input type="hidden" name="qno" id="hidqno">
        <input type="hidden" name="mode" id="mode" value="normal">
    </form>

<?php include("footer.php"); ?>

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

?>