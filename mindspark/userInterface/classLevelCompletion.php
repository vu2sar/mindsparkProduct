<?php
set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING);

@include("check1.php");
include("constants.php");
include("classes/clsTeacherTopic.php");
include("classes/clsUser.php");

if(!isset($_SESSION['userID']))
{
	header("Location: logout.php");
	exit;
}
$userID = $_SESSION['userID'];
$objUser = new User($userID);
$childName    = explode(" ",$objUser->childName);
$childName    = $childName[0];
$schoolCode    = $objUser->schoolCode;
$childClass    = $objUser->childClass;
$childSection  = $objUser->childSection;

$buddy_id = $_SESSION['buddy'];

//$flow       = isset($_SESSION['flow'])?$_SESSION['flow']:"";

$teacherTopicName = $_SESSION['teacherTopicName'];
$ttAttemptID      = $_SESSION['teacherTopicAttemptID'];
$teacherTopicCode = $_SESSION['teacherTopicCode'];
$query = "SELECT flow FROM ".TBL_TOPIC_STATUS." WHERE ttAttemptID=$ttAttemptID";
$flow_result = mysql_query($query);
$flow_line = mysql_fetch_array($flow_result);
$flow = $flow_line[0];
$fromQuesPage=isset($_POST['fromQuesPage'])?'&fromQuesPage=1':'';
$objTT       = new teacherTopic($teacherTopicCode,$childClass,$flow);
$perClustersCleared = getPerClustersCleared($ttAttemptID, $teacherTopicCode, $childClass, $objTT);
$msg = "";
if($perClustersCleared==100)
{
	$msg = "perCleared100";
}
elseif($perClustersCleared >= 50)
{
    $msg = "perCleared50";
}
else
{
	$msg = "perClearedLessThan50";
}
?>

<?php include("header.php"); ?>

<title>Mindspark</title>

<?php if($theme==1) { ?>
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/classLevelCompletion/lowerClass.css" rel="stylesheet" type="text/css">
<?php } else if($theme==2) { ?>
    <link rel="stylesheet" href="css/commonMidClass.css" />
    <link rel="stylesheet" href="css/classLevelCompletion/midClass.css" />
<?php } else if($theme==3) { ?>
    <link rel="stylesheet" href="css/commonHigherClass.css" />
    <link rel="stylesheet" href="css/classLevelCompletion/higherClass.css" />
<?php } ?>
<link href="css/question/choiceScreen.css?ver2" rel="stylesheet" type="text/css">
<script type="text/javascript"	src="libs/question/choiceScreen.js?ver=999"></script>
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>

<script>
$(document).ready(function(e) {
	if (window.location.href.indexOf("localhost") > -1) {	
	    var langType = 'en-us';
	}
	i18n.init({ lng: langType,useCookie: false }, function(t) {
		$(".translation").i18n();
	});
	$('#pnlLoading').hide();
	$('#formContainer .message').hide();
	
		var infobarHeight = document.getElementById("info_bar").offsetHeight;
		var b = window.innerHeight - infobarHeight - 80 - 17;
		$('#pnlLoading').css({ "display": "block", "height": b });
		$.post("controller.php", "teacherTopicCode=<?=$teacherTopicCode?>&ttAttemptID=<?=$ttAttemptID?>&mode=fetchChoiceScreenOnTopicCompletion<?=$fromQuesPage?>", function (data) {
		    //try{
		    	var responseArray = $.parseJSON(data);
		    	if (typeof responseArray['choiceScreen']!='undefined'){
		    		$('#pnlLoading').hide();
		    		choiceScreen=new ChoiceScreen(responseArray['choiceScreen']);
		    		if (choiceScreen && choiceScreen!="" <?php echo isset($_POST['fromQuesPage'])?'&& 1':'&& 0';?>) {
		    		    $('<div id="choiceScreenTextMesage" style="text-align: center;padding: 5px;"></div>').insertBefore('#choiceScreenText').html('Congratulations! You have successfully completed the topic.');
		    		    $('#choiceScreenDiv').css('height',($('#choiceScreenDiv').height()+45)+'px');
		    		}

		    		choiceScreen.show(function(){$('#pnlLoading').hide();},null);

		    	}
		    	else {
		    		$('#pnlLoading').hide();
		    		$('#formContainer .message').show();
		    	}
		    //}catch(err){
		    //	$('#pnlLoading').hide();
		    //}
		});
	
	$('#logout').click(function(){logoff();});
});
var choiceScreen;
var langType = '<?=$language;?>';

function load(){
	 init();
<?php if($theme==1) { ?>
	var a= window.innerHeight - (180);
	$('#classLevelCompletionDivMain').css("height",a+"px");
<?php } else if($theme==2){ ?>
	var a= window.innerHeight - (270);
	$('#classLevelCompletionDivMain').css("height",a+"px");
<?php } else if($theme==3) { ?>
			var a= window.innerHeight - (170);
			var b= window.innerHeight - (610);
			$('#classLevelCompletionDivMain').css({"height":a+"px"});
			$('#sideBar').css({"height":a+"px"});
			$('#main_bar').css({"height":a+"px"});
			$('#menubar').css({"height":a+"px"});
<?php } ?>
}

function logoff()
{
	window.location="logout.php";
}
function getHome()
{
	window.location.href	=	"home.php";
}
function init()
{
	/*if(parseInt(document.getElementById("pnlTeacherTopicSelection").offsetHeight)<300)
		document.getElementById("pnlTeacherTopicSelection").style.height = "300px";*/
	setTimeout("logoff()", 600000);	//log off if idle for 10 mins

}
// set the starting datestamp;
var closeSession = true;
if(document.addEventListener)
{
	document.addEventListener("keydown", my_onkeydown_handler,true);
}
else if (document.attachEvent)
{
	document.attachEvent("onkeydown", my_onkeydown_handler)
}
function backButtonPressed(arg)
{
	if(arg == false && closeSession == true)
	{
		location.replace('error.php');
	}
	else if(arg == true || closeSession == false)
	return false;
}
var plstart = new Date();
window.history.forward(1);
function my_onkeydown_handler(ev)
{
	var ev = ev || window.event;
	switch (ev.keyCode)
	{
		case 13: //enter
		closeSession = false;
		break;
	}
}

if (document.layers) document.captureEvents(Event.KEYPRESS);

<!--
var message="Sorry, right-click has been disabled";
function clickIE() {if (document.all) {(message);return false;}}
function clickNS(e) {
		if(document.layers||(document.getElementById&&!document.all)) {
				if (e.which==2||e.which==3) {(message);return false;}
		}
}
if (document.layers)        {
		document.captureEvents(Event.MOUSEDOWN);document.onmousedown=clickNS;
}
else        {
		document.onmouseup=clickNS;
		document.oncontextmenu=clickIE;
}
//document.oncontextmenu=new Function("return false")
// -->

function submitForm(val, per)
{
	if(val==2)
	{
		document.getElementById('mode').value = "ttSelection";
	}
	if(val==1 && per>=50 && per < 100)
	{
		var prompt = i18n.t("classLevelCompletion.confirmPrompt");
		var ans = confirm(prompt);
		if(!ans)
			return;
	}
	document.getElementById('higherLevel').value = val;
	document.getElementById('frmClassLevelCompletion').submit();
}

function logoff()
{
	window.location="logout.php";
}

</script>
</head>
<body class="translation" onLoad="load()" onResize="load()">
	<div id="top_bar">
		<div class="logo">
		</div>

        <div id="studentInfoLowerClass" class="forLowerOnly hidden">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span><?=$childName?>&nbsp;</span></a></li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?=$childClass.$childSection?></span></div>
            </div>
        </div>
		<div id="studentInfoLowerClass" class="forHighestOnly">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?=$childName?>&nbsp;&#9660;</span></a>
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
        <div id="logout" class="hidden">
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
                <div id="homeIcon" onClick="getHome()"></div>
                <div id="dashboardHeading" class="forLowerOnly"></div>
                <div class="clear"></div>
            </div>
        </div>
		<div id="info_bar" class="forHigherOnly hidden">
			<div id="topic">
				<div id="home">
                    <div id="homeIcon" onClick="getHome()"></div>
                    <div id="homeText"><span class="removeDecoration" onClick="getHome()" data-i18n="dashboardPage.home"></span></div>
                    <div class="clear"></div>
				</div>
                <div class="clear"></div>

			</div>

			<div id="studentInfo">
            	<div id="studentInfoUpper">
                	<div id="childClassDiv"><span data-i18n="common.class"></span> <?=$childClass.$childSection?></div>
                	<div id="childNameDiv" class="Name"><?=$childName?></div>
                    <div class="clear"></div>
                </div>
            </div>

            <div class="clear"></div>
		</div>
		<div id="info_bar" class="forHighestOnly">
				<a href="home.php" style="text-decoration:none;color:inherit">
				<div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
					<div id="dashboardText"><span class="textUppercase" data-i18n="homePage.recent"></span></div>
                </div></a>
				<div class="arrow-right"></div>
		</div>
        <form id="frmClassLevelCompletion" method="post" action="controller.php" autocomplete='off'>
			<div id="classLevelCompletionDivMain">
            	<div id="formContainer">
                    <!--<div id="pnlBuddy" class="buddy" align="right"></div>-->
                    <div class="message">
                            <span data-i18n="[html]classLevelCompletion.<?=$msg?>">
                            </span>
                            <br/>

                            <?php if($perClustersCleared>=50) { ?>
                                <input type='button' class='button1' data-i18n="[value]classLevelCompletion.btnYesHigherLevel"  value='Yes' name='btnYes' id="btnYes" style="margin-right:25px" onClick="submitForm(1, <?=$perClustersCleared?>)">
                            <?php } else { ?>
                                <input type='button' class='button1' data-i18n="[value]classLevelCompletion.btnRestartTopic" value='Yes' name='btnYes' id="btnYes" style="margin-right:25px" onClick="submitForm(2,<?=$perClustersCleared?>)">
                            <?php } ?>
                                <input type='button' class='button1' data-i18n="[value]classLevelCompletion.btnNo" name='btnNo' id="btnNo" onClick="submitForm(0,<?=$perClustersCleared?>)">

                    </div>
                    <input type="hidden" name="mode" id="mode" value="classLevelCompletion">
                    <input type="hidden" name="higherLevel" id="higherLevel" value="">
                    <input type="hidden" name="ttCode" id="ttCode" value="<?=$teacherTopicCode?>">
                    <div id="pnlLoading" name="pnlLoading">
                    	<div align="center" class="quesDetails">
                    		<br /> <br /> <br /> <br />
                    		<p>
                    			Loading, please wait...<br /> <img
                    				src="assets/loader.gif">
                    		</p>
                    	</div>
                    </div>
                </div>
	        </div>
    	</form>
	</div>
<?php include("footer.php"); ?>

<?php
function getPerClustersCleared($ttAttemptID, $teacherTopicCode, $cls, $objTT)
{
	$per = 0;
	$totalClusters = $objTT ->getNoOfClustersOfLevel($objTT->startingLevel);

	$query  = "SELECT failedClusters FROM ".TBL_TOPIC_STATUS." WHERE ttAttemptID=$ttAttemptID";
	$result = mysql_query($query);
	$line   = mysql_fetch_array($result);
	$failedClusters = $line[0];
	if($line[0]=="")
	    $clustersCleared = $totalClusters;
	else
	{
		$failedClusterArray = explode(",",$failedClusters);
		$clustersCleared = $totalClusters - count($failedClusterArray);
	}
	$per = round($clustersCleared*100/$totalClusters,2);
	return $per;
}
?>