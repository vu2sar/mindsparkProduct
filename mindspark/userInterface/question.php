<?php
include ("check1.php");
include_once ("constants.php");
if(!isset($_SESSION['userID']) || count($_POST)==0)
{
	echo "You are not authorised to access this page! (URL copy pasted in the browser!)";
    exit;
}
$quesCategory = isset ( $_POST ['quesCategory'] ) ? $_POST ['quesCategory'] : "";
$progressBarFlag = 0;
if ($_SESSION ['progressBarFlag'] == 1 && $quesCategory != 'topicRevision' && $quesCategory != "diagnosticTest" && $quesCategory != "kstdiagnosticTest")
	$progressBarFlag = 1;

$teacherTopicName = $_SESSION['teacherTopicName'];
$sessionID = $_SESSION ['sessionID'];
$Name = explode ( " ", $_SESSION ['childName'] );
$Name = $Name [0];
$childClass = $_SESSION ['childClass'];
$higherLevel = $_SESSION ['higherLevel'];
$_SESSION ['sessionReportFlag'] = 1;

if ($quesCategory=='topicRevision' || $quesCategory=='NCERT'  || $quesCategory=='practiseModule')
	$higherLevel = 0;

$iPad = stripos ( $_SERVER ['HTTP_USER_AGENT'], "iPad" );
$Android = stripos ( $_SERVER ['HTTP_USER_AGENT'], "Android" );
$IE = stripos ( $_SERVER ['HTTP_USER_AGENT'], "MSIE" );
$flagcheck = false;
if (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'Android' ) !== false && strpos ( $_SERVER ['HTTP_USER_AGENT'], 'Chrome' ) !== false)
	$flagcheck = true;

$budddySchoolArr = array ();
$baseurl = IMAGES_FOLDER . "/newUserInterface/";
$baseurlProgressBar = IMAGES_FOLDER_S3 . "/topicProgressBar/";
$sparkieImage = $_SESSION ['sparkieImage'];
$skipedQuestions = skipQuestions ();

if ($quesCategory == "practiseTest")
	$practiseset = true;

?>
<?php include("header.php");?>
<title>Questions</title>
<?php
// Code for daily drill starts here.
if (isset ( $_POST ['quesCategory'] ) && $_POST ['quesCategory'] == "practiseModule") {

	$practiseModuleTestStatusId = $_POST ['practiseModuleTestStatusId'];
	$quesCategory = $_POST ['quesCategory'];
	$isPractiseModule = true;
	if(isset($_POST['pageName']) && $_POST['pageName'] == 'topicPage')
	$topicPage = 1;
	else
	$topicPage = 0;
		
	echo "<script> var isdailyDrillFirstQuestion = true;</script>";

}

if(isset($_POST['quesCategory']) && $_POST['quesCategory']=="daTest")
{
	$DAset = true;
	$daTestCode = $_POST ['daTestCode'];
	$quesCategory = $_POST ['quesCategory'];
	$qno = 1;
	
	echo "<script> var isDaFirstQue = true;</script>";
}
if($DAset)
{
	$spendtime = 0;
	$maxAttemptQno = 1;
	$daPaperCode = $_SESSION ['daPaperCode'];
	$sql = "SELECT spendTime, lastAttemptQue FROM da_questionTestStatus WHERE userID=" . $_SESSION ['userID'] . " and paperCode = '$daPaperCode' ";
	$rs = mysql_query ( $sql );
		if(mysql_num_rows($rs) > 0)
		{
		$rw = mysql_fetch_array ( $rs );
		$spendtime = $rw [0];
		$maxAttemptQno = $rw [1];
	}
	$sqDaTopic = "SELECT topicName FROM da_paperCodeMaster WHERE paperCode = '$daPaperCode' ";
	$rsDaTopic = mysql_query ($sqDaTopic);
	$rwDaTopic = mysql_fetch_array ($rsDaTopic);
	$daTopicName = $rwDaTopic[0];
}
if(isset($_POST['quesCategory']) && $_POST['quesCategory']=="worksheet")
{
	$WSset = true;
	$wsm_id = $_POST ['worksheetID'];
	$worksheetID = $_POST ['worksheetID'];
	$_SESSION ['worksheetID']=$_POST ['wsm_id'];
	$worksheetAttemptID = $_POST ['worksheetAttemptID'];
	$quesCategory = $_POST ['quesCategory'];
	$spendtime=isset($_POST ['timeLeft'])?$_POST ['timeLeft']:0;
	$maxAttemptQno=isset($_POST ['qno'])?$_POST ['qno']:1;
	$qno = $maxAttemptQno;
	$WSTopicName = stripcslashes($_POST['worksheetName']);
	echo "<script> var isWSFirstQue = true;</script>";
}
$topicAttemptStatus = $_SESSION['topicAttemptStatus'] ?: '';
unset($_SESSION['topicAttemptStatus']);

?>
<meta content="IE=9" http-equiv="X-UA-Compatible">
<link rel="dns-prefetch" href="http://d2tl1spkm4qpax.cloudfront.net/">
<link href="css/question/common.css?ver=14" rel="stylesheet" type="text/css">
<link href="css/home/prompt1.css?ver=2" rel="stylesheet" type="text/css">
<link href="css/question/glossary.css" rel="stylesheet" type="text/css">
<!--<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">-->
<script type="text/javascript" src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="<?php echo HTML5_COMMON_LIB; ?>/jquery_ui_touch.js"></script>
<link rel="stylesheet" type="text/css" href="css/colorbox.css">
<script src="libs/jquery.colorbox-min.js" type="text/javascript"></script>
<script type="text/javascript" src="libs/question/ms_ques.min.js?ver=12"></script>
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery_ui.js"></script>
<script type="text/javascript" src="/mindspark/js/load.js"></script>
<script src="libs/jquery.raty.js"></script>
<!--<script src="libs/closeDetection.js"></script>-->
<?php
if ($theme == 1) {
?>
<link href="css/question/lowerClass.css?ver=7.8" rel="stylesheet" type="text/css">
<link href="css/commonLowerClass.css?ver=2" rel="stylesheet" type="text/css">
<script type="text/javascript" src="libs/question/scriptLowerClass.js?ver=8.3"></script>

<?php } else if($theme==2) { ?>
<link href="css/common.css?ver=2" rel="stylesheet" type="text/css">
<link href="css/question/middleClass.css?ver=9.10" rel="stylesheet" type="text/css">
<link href="../teacherInterface/fancybox/jquery.fancybox.css?1" rel="stylesheet" type="text/css">
<script type="text/javascript"	src="../teacherInterface/fancybox/jquery.fancybox.js?2"></script>
<script type="text/javascript" src="libs/question/scriptMiddleClass.js?ver=12.4"></script>
<script src="libs/speedometer.js?version=2" type="text/javascript"></script>
<?php } else if($theme==3) { ?>
<link href="css/commonHigherClass.css?ver=9.2" rel="stylesheet" type="text/css">
<link href="css/question/higherClass.css?ver=7.10" rel="stylesheet" type="text/css">
<script type="text/javascript"	src="libs/question/scriptHigherClass.js?ver=15.3"></script>
<script src="libs/speedometer.js?version=2" type="text/javascript"></script>
<link href="../teacherInterface/fancybox/jquery.fancybox.css?1" rel="stylesheet" type="text/css">
<script type="text/javascript"	src="../teacherInterface/fancybox/jquery.fancybox.js?2"></script>
<?php }

if($quesCategory=='topicRevision')
{
	echo "<script>var topicRevisionAttemptNo=".$_SESSION['topicRevisionAttemptNo'].";</script>";
}

?>
<!--<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js?ver=3"></script>-->
<link href="css/question/choiceScreen.css?ver2" rel="stylesheet" type="text/css">
<script type="text/javascript"	src="libs/question/choiceScreen.js?ver=999"></script>
<script type="text/javascript" src="/mindspark/userInterface/libs/combined.js?ver=3"></script>
<script type="text/javascript"	src="libs/question/scriptCommon.js?ver=2146"></script>
<!-- <script type="text/javascript" src="libs/question/scriptCommon.min.js?ver=1036"></script> -->
<script type="text/javascript" src="libs/question/glossary.js"></script>
<!--<script type="text/javascript" src="libs/fracbox.js"></script>-->
<script type="text/javascript" src="libs/maxlength.js"></script>
<!--<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/jquery.ui.draggable.min.js"></script>-->
<script type="text/javascript" src="<?php echo HTML5_COMMON_LIB; ?>/jquery_ui_touch.js"></script>
<?php if( $iPad ==true|| $Android ==true) { ?>
<link type="text/css" href="libs/question/keypad2/jquery.keypad.css"
	rel="stylesheet">
<link type="text/css" href="libs/question/keypad2/keypadCustomStyle.css"
	rel="stylesheet">
<script type="text/javascript"
	src="libs/question/keypad2/jquery.keypad.js?datetime=2016.12.20.14.33.59"></script>
<script type="text/javascript"
	src="libs/question/keypad2/keypad2.js?ver=5"></script>
<?php }?>
<?php if(in_array($_SESSION['schoolCode'],$budddySchoolArr) && $Android === false && $iPad === false && $IE === false) { ?>
<script type="text/javascript" src="libs/easeljs-0.5.0.min.js"></script>
<script type="text/javascript" src="libs/buddy.js"></script>
<link href="css/buddy.css" rel="stylesheet">
<?php } ?>
<script>

    <?php if(isset($_SESSION['rewardSystem'])) echo "var rewardSystem=".$_SESSION['rewardSystem'].";"; else { echo "var rewardSystem=0;";$_SESSION['rewardSystem']=0; } ?>
    <?php echo "var progressBarFlag=".$progressBarFlag.";"; ?>
    var skipQuestions	=	new Array(<?=$skipedQuestions?>);
    var userType="";
    var langType = '<?=$language?>';
	<?php
		$emoteToolbarTagCount = emotToolbarTaggingCount ( $_SESSION ['userID'] );
		echo "var emoteToolbarTagCount = " . $emoteToolbarTagCount . ";\n";
		if (isset($_POST['iycAsChoice'])){
			echo 'var iycAsChoiceFlag=true;';
		}
	?>
	function showPrompt(){
		var prompts = new Prompt({
	        text: "Here's how long you've been Mindspark-ing!<br/>Time is in h:mm format.",
	        type: 'alert',
	        func1: function() {
	            jQuery("#prmptContainer_openID").remove();
	        },
	        promptId: 'openID'
	    });
	}

	function addHoverClass()
	{
		$( "#cc" ).addClass( "hovering" );
	}
	function removeHoverClass()
	{
		$( "#cc" ).removeClass( "hovering" );
	}
	function resetStyle()
	{
		$("#dislike").removeAttr('style');
		$("#like").removeAttr('style');
		$("#comment").removeAttr('style');
	}
</script>
<style>
	#pmSparkieInfo td,#pmSparkieInfo tr:nth-child(2)>th{
		text-align: left;
	}
	#submittest.inActive {opacity: 0.4;}
	.wsLegendKey{display: inline-block;width: 10px;height: 10px;border-radius: 15px;-webkit-border-radius: 15px;vertical-align: middle;}
</style>
</head>
<body onResize="adjustScreenElements()" class="translation" >
<span class="math" style="display: none">{1 \over 2}</span>
<input type="hidden" name="problemid" id="problemid" value="">
<?php if($childClass>7 && $_SESSION['rewardSystem']==0) { ?>
<div id="wildcardInfo" title="Wildcard Information"
		style="display: none;">
	<table border="0" bgcolor="#DEB887" cellpadding="5" cellspacing="3">
		<tr bgcolor="#FFD39B">
			<th>Here is an opportunity to earn 10 reward points by answering a
				wild card question!!!</th>
		</tr>
		<tr bgcolor="#FFD39B">
			<th>A wild card question is a question that may not be related to
				the topic that you are currently doing. It is asked to test your
				alertness and ability to answer a question from other topics. You
				will get 10 reward points for answering a wild card question
				correctly. Remember that wild card questions will not affect your
				performance in the regular topics.</th>
		</tr>
	</table>
</div>
<?php } else { ?>
<div id="wildcardInfo" title="Wildcard Information"
		style="display: none;">
	<table border="0" bgcolor="#DEB887" cellpadding="5" cellspacing="3">
		<tr bgcolor="#FFD39B">
			<th>Here is an opportunity to earn a full sparkie by answering a
				wild card question!!!</th>
		</tr>
		<tr bgcolor="#FFD39B">
			<th>A wild card question is a question that may not be related to
				the topic that you are currently doing. It is asked to test your
				alertness and ability to answer a question from other topics. You
				will get a sparkie for answering a wild card question correctly.
				Remember that wild card questions will not affect your performance
				in the regular topics.</th>
		</tr>
	</table>
</div>
<?php } ?>
<div id="top_bar" class="top_bar_part3 <?= $isPractiseModule == true ?'daily-drill-top-bar':'';?>">
	<div class="logo"></div>
	<?php if(in_array($_SESSION['schoolCode'],$budddySchoolArr) && $Android === false && $iPad === false && $IE === false) { ?>
	<div id="ichar">
		<canvas id="buddyCanvas" width="200px" height="240px">HTML5 canvas not supported</canvas>
		<div id="closeBtn"> <a class="arrow" onClick="closeBtnFunc('hide')"> </a> </div>
	</div>
	<div id="showBuddyDiv" onClick="closeBtnFunc('show')"
			style="display: none"></div>
	<?php } ?>
	<div id="studentInfoLowerClass" class="forLowerOnly">
		<div id="nameIcon"></div>
		<div id="infoBarLeft">
			<div id="nameDiv">
				<div id="cssmenu">
					<ul>
						<li class="has-sub "><a href="javascript:void(0)"><span
									id="nameC">
							<?=$Name?>
							&nbsp;</span></a></li>
					</ul>
				</div>
			</div>
			<div id="classDiv"> <span id="classText" data-i18n="common.class"></span> <span
						id="userClass">
				<?=$childClass.$childSection?>
				</span> </div>
		</div>
	</div>
	<div id="studentInfoLowerClass" class="forHighestOnly">
		<div id="nameIcon"></div>
		<div id="infoBarLeft">
			<div id="nameDiv">
				<div id="cssmenu">
					<ul>
						<li class="has-sub "><a href="javascript:void(0)"><span
									id="nameC">
							<?=$Name?>
							&nbsp;&#9660;</span></a>
							<ul>
								<li><a href="javascript:void(0)"
										onClick="openHelp(<?=$theme?>,'<?=$baseurl?>')"><span
											data-i18n="common.help"></span></a></li>
							</ul>
						</li>
					</ul>
				</div>
			</div>
			<div id="classDiv"> <span id="classText" data-i18n="common.class"></span> <span
						id="userClass">
				<?=$childClass.$childSection?>
				</span> </div>
		</div>
	</div>
	<div id="help" style="visibility: hidden;">
		<div class="help"></div>
		<div class="helpText" data-i18n="common.help"></div>
	</div>
	<div id="logout" class="linkPointer hidden" onClick="logout();"
			style="visibility: hidden;">
		<div class="logout"></div>
		<div class="logoutText" data-i18n="common.logout"></div>
	</div>
	<div id="whatsNew" style="visibility: hidden;">
		<div class="whatsNew"></div>
		<div class="whatsNewText" data-i18n="common.whatsNew"></div>
	</div>
	<?php if(!$practiseset && !$DAset && !$WSset) { ?>
	<div id="sparkieContainer" class="hidden">
		<div id="noOfSparkie"></div>
		<div class="sparkie"></div>
	</div>
	<?php } ?>
</div>
<!-- End top_bar -->
<?php if($progressBarFlag) { ?>
<div id="progressOverlay" style="cursor: pointer;">
	<div id="correct_bar_new">
		<div id="cText" class="forHigherOnly"> <strong data-i18n="questionPage.correctAnswer"></strong> </div>
		<div class="text" data-i18n="questionPage.correct"></div>
		<?php if($_SESSION['examCornerCluster']==1) { ?>
		<div class="circle" id="spnQuesCorrectEC">0</div>
		<div class="text1" data-i18n="questionPage.outOf"></div>
		<div class="circle" id="spnQuestionsDoneEC">0</div>
		<?php } else { ?>
		<div class="circle" id="spnQuesCorrect"></div>
		<div class="text1" data-i18n="questionPage.outOf"></div>
		<div class="circle" id="spnQuestionsDone"></div>
		<?php } ?>
		<div class="arrow-a"></div>
	</div>
	<div id="topicProgressBar">
		<div id="greenBars"></div>
		<div id="topicProgressPer">
			<div id="topicPercent" class="align"> <span id="spnProgressNew"></span> </div>
		</div>
	</div>
	<div id="topicNameProgress"> Your progress in topic, <span style="font-weight: bold">
		<?php
					if ($quesCategory == "diagnosticTest")
					{
						if($_SESSION['diagnosticTestType'] == 'Assessment')							
							echo "Assessment: " .$teacherTopicName;
						else
							echo "Checkpoint: " .$teacherTopicName;

					}
					else if ($quesCategory == "topicRevision")
						echo "Topic-wise Practice: ";
					if (isset ( $_SESSION ['bucketTopicName'] ) && $quesCategory != "diagnosticTest")
						echo $_SESSION ['bucketTopicName'];
					else if ($quesCategory != "diagnosticTest")
						echo $teacherTopicName;
					?>
		</span> </div>
	<?php } ?>
	<?php
								
if ($progressBarFlag) {
									
									?>
	<a href="javascript:void(0)"
			onClick="openHelp1(<?=$theme?>,'<?=$baseurlProgressBar?>')">
	<div id="learnMore" class="forHigherOnly"> <u>Click here to know more</u> </div>
	</a> </div>
<?php } ?>
<div id="container">
<?php
								
	if (! $DAset && ! $WSset) {
		if ($_SESSION ["userType"] == "msAsStudent") {
										?>
<div class="bubble forHigherOnly hidden <?php if($isPractiseModule) echo 'practiseModule'; ?>" >
	<div id="sparkieImage" class="<?php if($isPractiseModule) echo 'practiseIcon'; else echo 'level1';?>"> <span id="sparkieInfo"></span> </div>
</div>
<?php }else{ ?>
<div class="bubble forHigherOnly hidden <?php if($isPractiseModule) echo 'practiseModule'; ?>" >
	<div id="sparkieImage" class="<?php if($isPractiseModule) echo 'practiseIcon'; else echo $sparkieImage;?>"> <span id="sparkieInfo"></span> </div>
</div>
<?php } 
    }?>
<div id="blackScreen">
	<div id="endTopic" class="questionPrompts">
		<div id="topicText" data-i18n="questionPage.endTopicText"></div>
		<div class="button1" data-i18n="common.yes"
					onClick="javascript:setTryingToUnload(); changeTopic()"></div>
		<div class="button1 textUppercase" onClick="cancel();"
					data-i18n="common.no"></div>
	</div>
	<div id="endSessionClick" class="questionPrompts">
		<?php if($practiseset) { ?>
		<div id="topicText" class="endSessionText">Are you sure you want to
			end practice?</div>
		<?php } else { ?>
		<div id="topicText" class="endSessionText"
					data-i18n="questionPage.endSessionText"></div>
		<?php } ?>
		<br />
		<div class="button1" data-i18n="common.yes"
					onClick="javascript:setTryingToUnload();finalSubmit(1);"></div>
		<div class="button1 textUppercase" onClick="cancel();"
					data-i18n="common.no"></div>
		<div id="loading"
					style="display: none; font-size: 18px; margin-top: 12%;"
					data-i18n="questionPage.loadingText"></div>
		<div id="loadingImage" style="display: none;"></div>
	</div>
	<div id="toughQuestionClick" class="questionPrompts"
				style="width: 400px; left: 33%"> <img
					style="float: left; width: 28%; margin-top: 20px; margin-right: 30px; left: 0px; z-index: -1; opacity: 0.7;"
					src="assets/toughIcon.png" alt="toughQuestion">
		<div id="topicText"
					style="text-align: left; margin-top: 30px; padding-right: 10px; min-height: 170px"
					data-i18n="[html]questionPage.toughQuestionText"></div>
		<div id="toughYes" class="button1" data-i18n="common.yes"
					onClick="cancelToughQuestionAlert();"></div>
		<div class="button1 textUppercase"
					onClick="submitToughQuestionAlert();" data-i18n="common.no"></div>
		<br>
		<div
					style="position: absolute; bottom: 5px; width: 100%; font-size: 130%; color: #1E35DA; display: none; text-align: center;"
					id="toughCheck">
			<input type="checkbox" id="dontShowTough" name="dontShowTough">
			<label
						for="dontShowTough">Don't give this warning again</label>
		</div>
	</div>
	<div id="higherLevelClick" class="questionPrompts">
		<div id="topicText" data-i18n="questionPage.quitHighLevelText"></div>
		<div class="button1" data-i18n="common.yes" onClick="quitTopic();"></div>
		<div class="button1 textUppercase" onClick="cancel();"
					data-i18n="common.no"></div>
	</div>
	<div id="topicRepeatAttempt" class="questionPrompts">
		<div id="promptHeader"> <img src="assets/sparkie.png" /> </div>
		<div id="promptData"></div>
		<div class="button1 textUppercase"
					onClick="closeTopicRepeatAttempt();" data-i18n="common.ok"></div>
	</div>
</div>
<!--Prompt 1-->
<?php $Name = $Name=strtoupper($Name); ?>
<div id="prompt">
	<div id="image">
		<div id="congrats"> <b>Congratulations</b> </div>
		<div id="congrats_name"> <b>
			<?=$Name?>
			!</b> </div>
		<div id="desc1"></div>
		<div id="desc2"></div>
		<div id="sparkie"></div>
		<div id="badge"
					style="width: 137px; height: 120px; position: relative; top: 64; left: 328px;"> </div>
		<div id="close_rewards" onClick="sample();"></div>
		<div id="bottom_info1"></div>
		<a id="rewardsLink" href="">
		<div id="bottom_info2"></div>
		</a> </div>
</div>
<div style="display: none">
	<div id="instruction"
				style="font-family: 'Conv_HelveticaLTStd-Light'; font-size: 18px; text-align: justify; padding-top: 34px;"> Here is an opportunity to earn a Sparkie by answering a wild card
		question! <br>
		A wild card question is a question that may not be
		related to the topic that you are currently doing. It is asked to
		test your alertness and ability to answer questions from other
		topics. You will get a Sparkie for answering this question
		correctly. Remember that wild card questions will not affect your
		performance in the regular topics. </div>
	<?php if($progressBarFlag) {?>
	<div id="clusterStatusPrompts"
				style="font-family: 'Conv_HelveticaLTStd-Light'; font-size: 18px; text-align: -webkit-center; padding-top: 34px;"> </div>
	<?php } ?>
</div>

<div id="info_bar" style="<?= ($quesCategory == 'daTest' || $quesCategory == 'worksheet') && $childClass > 7 ?'overflow:visible':''; ?>">
<div id="dashboard" class="forHighestOnly">
	<div id="dashboardIcon"></div>
	<div id="dashboardText">
		<?php
		if ($DAset) {
			?>
		<span class="">SUPER TEST</span>
		<?php
		} else if ($WSset) {
			?>
		<span class="">WORKSHEET</span>
		<?php
		} else {
			?>
		<span class="textUppercase" data-i18n="homePage.recent"></span>
		<?php
		}
		?>
	</div>
</div>
<div class="arrow-1"></div>
<?php if(!$WSset) { ?>
<div id="lowerClassProgress">
	<?php if($practiseset  == true) { ?>
	<center>
		<table cellspacing="1" cellpadding="1" width="100%">
			<tr>
				<td valign='top' style='padding-top: 10px; text-align: right;'><font
								style='font-size: 20px;'>Score</font></td>
				<td valign='middle'><center>
						<h3
										style='color: #124AB2; width: 32px; border-radius: 50px; height: 32px; background: #fefef5; border: 3px solid #124AB2; margin-top: 0;'> <b>
							<div style="margin-top: 7px;" id='scorecard'>0</div>
							</b> </h3>
					</center></td>
				<td valign='top' style='padding-top: 10px; text-align: right;'><font
								style='font-size: 20px;'>Energy</font></td>
				<td valign='middle'><center>
						<h4
										style='color: #B22209; width: 32px; border-radius: 50px; height: 32px; background: #fefef5; border: 3px solid #B22209; margin-top: 0;'> <b>
							<div style="margin-top: 7px;" id='lifecount'>25</div>
							</b> </h4>
					</center></td>
			</tr>
		</table>
		<div id='wrongquestioncount' style='display: none;'>0</div>
	</center>
	<? } ?>
</div>
<?php } ?>
<div id="linkBar" class="forLowerOnly hidden">
<?php if($flagcheck === false) { ?>
<div id="endSessionDiv"
					class="lowerClassIcon linkPointer" onClick="endsession();">
<?php } else {?>
<div id="endSessionDiv" class="lowerClassIcon linkPointer"
						onClick="endsessionJsAlert();">
	<?php } ?>
	<div class="endSessionDiv"></div>
	<?php if($practiseset  != true) { ?>
	<div class="quesLinkText"
							data-i18n="questionPage.endSession"></div>
	<?php } else { ?>
	<div class="quesLinkText" style="font-size: 23px;">Quit</div>
	<? } ?>
</div>
<?php if($practiseset  != true) { 
	// submit worksheet button added for class 3 worksheet
if($WSset && $theme==1)
{ ?>
	<div id="endTopicDiv" class="lowerClassIcon linkPointer submitWorksheet"
						onClick="handleClose();">											
		<div class="quesLinkText" data-i18n="questionPage.submittest1" id="submittest"></div>
	</div>
<?php }
else if($flagcheck === false) { ?>
	<div id="endTopicDiv" class="lowerClassIcon linkPointer"
							onClick="endtopic();">
		<div class="endTopicDiv"></div>
		<div class="quesLinkText" data-i18n="questionPage.changeTopic"></div>
	</div>
<?php } else {?>
	<div id="endTopicDiv" class="lowerClassIcon linkPointer"
								onClick="endtopicJsAlert();">
		<div class="endTopicDiv"></div>
		<div class="quesLinkText" data-i18n="questionPage.changeTopic"></div>
	</div>
	<?php } ?>
	

<? } ?>
<?php if($higherLevel && !$DAset  && !$WSset && $quesCategory != "NCERT" ) { ?>
	<?php if($flagcheck === false) { ?>
	<div id="quitHigherLevel" class="lowerClassIcon linkPointer" onClick="quitHigherLevel();">
	<?php } else {?>
	<div id="quitHigherLevel" class="lowerClassIcon linkPointer" onClick="quittopicJsAlert();">
	<?php } ?>
		<div class="quitHigherLevel"></div>
		<div class="quesLinkText" data-i18n="questionPage.quitHighLevel"></div>
	</div>
<?php } ?>
</div>
<?php
if($DAset){ ?>
<div class="da-test-div">
	<?php		
		$DATestsql = "SELECT qcode_list FROM educatio_educat.da_paperDetails where papercode = '$daTestCode' and version = 1";
		
		$DAresultSet = mysql_query ( $DATestsql ) or die ( mysql_error () . $DAsql );
		$DArw = mysql_fetch_array ( $DAresultSet );
		
		$DAqcodeLists = $DArw ['qcode_list'];
		$DATmpQcodeLists = explode ( ",", $DAqcodeLists );
		$paperQueCount = count ( $DATmpQcodeLists );
		$qPageCount=0;
		echo '<div id="pre_box" class="daPagingNav" ></div>';
		echo '<div id="daQcodeListBox" data-min="0" data-max="'.floor(($paperQueCount-1)/10).'" data-showing="0" data-quesCount="'.$paperQueCount.'"><table style="border-spacing: 0;"><tr>';
		for($c = 1; $c <= $paperQueCount ; $c++)
		{
			$d = $c - 1; 
			echo '<td><div id="' . $c . 'box" class="daPagingnormal" onclick="getDAQuestionDirect(' . $c . ',' . $DATmpQcodeLists [$d] . ');" >' . $c . '</div></td>';
		}
		echo '</tr></table></div>';
		echo '<div id="next_box" class="daPagingNav"></div>';
		?>
	<?php
		if ($theme != 3) { if(!($iPad || $Android)) {  ?>
			<strong>&nbsp;&nbsp;&nbsp;&nbsp;Time Remaining: &nbsp; <span id='countdown'> </span></strong>
		<?php } }?>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<input type='checkbox' id='daFlagCheck' onClick="daFlagHandle('yes');">
		<strong><span data-i18n="questionPage.flagThisForLater"></span></strong>
	<?php
		if ($theme == 3) { ?>
			<div id="super-test-hideShowBar" class="forHigherOnly"	onClick="hideSuperTestBar();">-</div>
			<div class="color-info">
			<?php }
			else{ ?>
				<br>
				<br>
				<br>
			<?php } ?>
		<span class="colorInfoSpan"><font class="daPagingBlue"></font>&nbsp;&nbsp;<b><i>Current Question</i></b></span><span class="colorInfoSpan"><font class="daPagingYellow"></font>&nbsp;&nbsp;<b><i>Question(s) to check later</i></b></span><span class="colorInfoSpan"><font class="daPagingred"></font>&nbsp;&nbsp;<b><i>Unanswered Question(s)</i></b></span>
		<?php if ($theme == 3) { ?>
		</div>
		<?php } ?>
	<?php if(($iPad || $Android) && $theme != 3) {  ?>
		<strong>&nbsp;&nbsp;&nbsp;&nbsp;Time Remaining: &nbsp; <span id='countdown'> </span></strong> &nbsp;&nbsp;&nbsp;&nbsp;
		<!-- <input type='checkbox' id='daFlagCheck' onClick="daFlagHandle('yes');">
		<strong><span data-i18n="questionPage.flagThisForLater"></span></strong> -->
	<?php } ?>
</div>
<?php
}
if($WSset){ ?>
<div class="da-test-div ws-test-div">
	<div class="worksheetName">
		<?=$WSTopicName ?> 
	</div>
	<?php		
		$WSTestsql = "SELECT GROUP_CONCAT(wsd_id) qcode_list FROM worksheet_detail a WHERE a.wsm_id='$worksheetID'";
		
		$WSresultSet = mysql_query ( $WSTestsql ) or die ( mysql_error () . $WSsql );
		$WSrw = mysql_fetch_array ( $WSresultSet );
		
		$WSqcodeLists = $WSrw ['qcode_list'];
		$WSTmpQcodeLists = explode ( ",", $WSqcodeLists );
		$paperQueCount = count ( $WSTmpQcodeLists );
		$qPageCount=0;
		echo '<div class="worksheetPaging"> ';
		if ($paperQueCount>10) echo '<div id="pre_box" class="daPagingNav" ></div>';
		echo '<div id="daQcodeListBox" data-min="0" data-max="'.floor(($paperQueCount-1)/10).'" data-showing="0" data-quesCount="'.$paperQueCount.'"><table style="border-spacing: 0;"><tr>';
		for($c = 1; $c <= $paperQueCount ; $c++)
		{
			$d = $c - 1; 
			echo '<td><div id="' . $c . 'box" class="daPagingnormal" style="cursor: pointer;" onclick="getWSQuestionDirect(' . $c . ',' . $WSTmpQcodeLists [$d] . ');" >' . $c . '</div></td>';
		}
		echo '</tr></table></div>';
		if ($paperQueCount>10) echo '<div id="next_box" class="daPagingNav"></div>';
		?>
	<?php
		if ($theme != 3) {
			if(!($iPad  || $Android)) {  ?>
			<div class="remainingTime"><strong >&nbsp;&nbsp;&nbsp;&nbsp;Time Remaining: &nbsp; <span id='countdown'> </span></strong></div>
		<?php } 
		}?>		
	<?php
		if ($theme == 3) { ?>
			<div id="super-test-hideShowBar" class="forHigherOnly"	onClick="hideSuperTestBar();">-</div>
			<?php } echo '</div>';?>
		<?php if ($theme == 3) { ?>
			<div class="color-info">
		<?php } ?>
		<!-- time remaining for class 3 worksheet -->
		<?php if($theme==1 && ($iPad  || $Android)) { ?>
		<div class="timeRemainingIpad">
		<span ><strong >Time Remaining:<br/><span id='countdown'></span></strong></span>
		</div>
		<?php } ?>
		<div class="worksheetColorBox">
			<?php if(($iPad  || $Android) && $theme==2) { ?>
				<span style="float: left;"><strong >&nbsp;&nbsp;&nbsp;&nbsp;Time Remaining: &nbsp; <span id='countdown'></strong></span>
			<?php }?>
			<span class="colorInfoSpan" <?php if($theme==2 && ($iPad || $Android)) echo 'style="margin-left: 75px;" '; else if ($theme==2) echo 'style="margin-left: 210px;clear: both;" ';else if ($theme==3) echo 'style="margin-left: 20px;"';?> ><font class="wsLegendKey daPagingBlue"></font>&nbsp;&nbsp;<b><i>Current Question</i></b></span><span class="colorInfoSpan" style="margin-left:20px;"><font class="wsLegendKey daPagingGrey"></font>&nbsp;&nbsp;<b><i>Answered Question(s)</i></b></span><span class="colorInfoSpan" style="margin-left:20px;"><font class="wsLegendKey daPagingnormal"></font>&nbsp;&nbsp;<b><i>Unanswered Question(s)</i></b></span>
			</div>
		<?php if ($theme == 3) { ?>
		</div>
		<?php } ?>
		
</div>
<?php
}
if($isPractiseModule){
	?>
<?php if (!isset($_POST['fromChoiceScreen']) || $_POST['fromChoiceScreen']!=1){?>
<div style="margin-left: 12%; font-size: 1.3em; margin-top: 13px; float: left;width:65%">
	<div> 
		<!-- <span  style="float:left;font-size:0.8em;">Practise <?= date("l - d/m/Y");?> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span> --> 
		<span style="float:right;font-size:0.8em;margin-left: 50px; margin-top: 10px;text-align:right;">
		<?php 
			$practiseMsgArray=array(
					"Regular practice is as important as learning new concept.",
					"There is no glory in practice, but without practice, there is no glory.",
					"Practice is the best of all instructors.",
					"Practice does not make perfect. Only perfect practice makes perfect",
					"Don't practice until you get it right. Practice until you can't get it wrong.",
					"Daily practice in Maths is as important as the need to exercise regularly to keep good health.");
			echo $practiseMsgArray[array_rand($practiseMsgArray)];
		?>
		<br>
		<?php if(!$topicPage){ ?>
		<span id="time-div" style="float:right;font-size:0.9em;margin-left: 50px; margin-top: 6px;">Time<span id="timeRemaining">4m:30s</span></span>		
		<?php } ?>
		</span>
		<div>
			<?=$_SESSION['dailyDrillArray']['description'];?>
		</div>
	</div>
</div>
<?php } else {?>
<div style="margin-left: 12%; font-size: 1.3em; margin-top: 13px; float: left;">
	<div style="float:left">
		<div>
			<?=$_SESSION['dailyDrillArray']['description'];?>
		</div>
	</div>
</div>
<?php } ?>
<!-- <div class="info-for-dailydrill">
						<span class="info-content">Regular PRACTICE is as important as Learning new Concepts</span>
					</div> -->

<?php 
				if (isset($_POST['fromChoiceScreen']) && $_POST['fromChoiceScreen']==1){
					echo '<input type="hidden" value="1" name="fromChoiceScreen" id="fromChoiceScreen" />';
					$returnToPage = isset($_POST['returnTo'])?$_POST['returnTo']:"";
					$redirectionPage = 'question.php';
					if ($returnToPage=='topic'){
						?>
<form name="choiceScreenRedirectFromPractiseModule" id="choiceScreenRedirectFromPractiseModule" action="controller.php" method="post">
	<input type="hidden" name="choiceTtCode" id="choiceTtCode" value="<?=$_SESSION['teacherTopicCode']?>">
	<input type="hidden" name="choiceMode" id="choiceMode" value="ttSelection">
</form>
<?php 
					} else {
						?>
<form name="choiceScreenRedirectFromPractiseModule" id="choiceScreenRedirectFromPractiseModule" action="question.php" method="post">
	<?php $qNo = isset($_SESSION['qno'])?$_SESSION['qno']:"1"; ?>
	<input type="hidden" name="choiceQno" id="choiceQno" value="<?=$qNo?>">
	<input type="hidden" name="choiceQuesCategory" id="choiceQuesCategory" value="normal">
	<input type="hidden" name="choiceShowAnswer" id="choiceShowAnswer" value="1">
</form>
<?php
					}
				}
			}
			if ($practiseset != true && ! $DAset && ! $WSset && !$isPractiseModule) {
				?>
<div id="topic">
	<div id="topic_name" class="topic_name_large">
		<div class="dynamic-text"> <span>
			<?php
                    if($quesCategory=="topicRevision")
                        echo "Topic-wise Practice: ";
                    else if ($quesCategory == "diagnosticTest")
					{
						if($_SESSION['diagnosticTestType'] == 'Assessment')
							echo "Assessment: " .$teacherTopicName;
						else
							echo "Checkpoint: " . getClusterName ( $_SESSION ['clusterCode'] );
					}
					else if ($quesCategory == "kstdiagnosticTest")
					{
						if($_SESSION['kstdiagnosticTestType'] == 'Assessment')
							echo "Assessment: " .$teacherTopicName;
						else
							echo "Checkpoint: " .$teacherTopicName;
					}
                    if(isset($_SESSION['bucketTopicName']) && $quesCategory!="diagnosticTest") echo $_SESSION['bucketTopicName'];
                    else if($quesCategory!="diagnosticTest" && $quesCategory != "kstdiagnosticTest") echo $teacherTopicName;
				?>
			</span> </div>
		<!--<div id="letsPractice" class="extraPCtext" style="display:none;">Let's Practice !</div>--> 
	</div>
	<?php if($progressBarFlag) {
			if ($_SESSION ['examCornerCluster'] != 1) {
				?>
	<div id="progress_bar_new">
		<div id="yellowBars"></div>
		<div id="progress_text_new"></div>
		<?php if($theme != 1){ ?>
		<a href="javascript:void(0)"
									onClick="openHelp1(<?=$theme?>,'<?=$baseurlProgressBar?>')">
		<div class="forHigherOnly" id="progressBarDemo">[?]</div>
		</a>
		<?php } ?>
	</div>
	<?php
			}
    } else 
                                           {
	?>
	<div id="progress_bar">
		<div id="correct_bar">
			<div id="cText" class="forHigherOnly"> <strong data-i18n="questionPage.correctAnswer"></strong> </div>
			<div class="text" data-i18n="questionPage.correct"></div>
			<?php if($_SESSION['examCornerCluster']==1) { ?>
			<div class="circle" id="spnQuesCorrectEC">0</div>
			<div class="text1" data-i18n="questionPage.outOf"></div>
			<div class="circle" id="spnQuestionsDoneEC">0</div>
			<?php } else { ?>
			<div class="circle" id="spnQuesCorrect"></div>
			<div class="text1" data-i18n="questionPage.outOf"></div>
			<div class="circle" id="spnQuestionsDone"></div>
			<?php } ?>
			<div class="arrow-a"></div>
		</div>
		<div id="green" class="hidden"></div>
		<div id="progress_text" class="align hidden"> <strong data-i18n="questionPage.progress"></strong> </div>
		<div id="percent" class="align hidden"> <span id="spnProgress"></span> </div>
	</div>
	<?php } ?>
	<div id="sessionTime"> <span class="labelText">Time: </span><span class="sessionColor"
									id="bgclocknoshade"></span><sup
									title="Here's how long you've been Mindspark-ing!"
									style='cursor: pointer; font-size: 10px; color: blue;'
									onClick="showPrompt();">?</sup> 
		<script>
					clockon();
				</script> 
	</div>
	<div id="session"> <span data-i18n="questionPage.session" class="labelText"></span>
		<span class="sessionColor"><?php echo $sessionID; ?></span>
	</div>
	<div id="question_number">
		<? if($quesCategory != 'kstdiagnosticTest') { ?>
		<span
			<?php if($theme!=1){ echo 'data-i18n="questionPage.questionText"'; } ?>
			class="labelText">
			<?php if($theme==1){ ?>
			Q No.:
			<?php } ?>
		</span>
		<span class="sessionColor" id="lblQuestionNo"></span>
		<? } ?>
		<?php if($quesCategory == 'diagnosticTest') { ?>
			<span data-i18n="questionPage.outOf"></span>
			<span id="lblQuestionTotal"></span>
		 <?php } ?>
	</div>
</div>
<? } ?>
<?php
												
if ($practiseset != true) {
	if ($progressBarFlag) {
		if ($_SESSION ['examCornerCluster'] != 1) {			
			?>
<?php if(!$DAset && !$WSset && !$isPractiseModule && !$_SESSION['comprehensiveCluster']) { ?>
<div id="showProgressBar">+</div>
<?php
															
}
		}
	}
}
?>
<div id="showHide" class="forHigherOnly hidden">Hide</div>
<?php if($quesCategory == "NCERT" && ($theme==1 || $theme==2)) { ?>
<div id="topic_ncert" class="hidden">
	<div id="home">
		<div class="icon_text11"> HOME > <font color="#606062"> NCERT EXERCISE >
			<?=stripslashes($_POST['exerciseName'])?>
			</font> </div>
	</div>
	<div id="commentError" data-i18n="questionPage.commentError" style="visibility: hidden"></div>
</div>
<?php  } ?>
<?php if($quesCategory == "NCERT" && $childClass>7) { ?>
<div class="icon_text11"> <font color="#606062"> NCERT EXERCISE > <?=stripslashes($_POST['exerciseName'])?> </font> </div>
<?php  } ?>
<div id="student" class="hidden forHigherOnly"></div>
<div class="class hidden forHigherOnly" style='margin-top: 0px;'> <strong data-i18n="common.class"></strong> <?php echo $childClass; ?> </div>
<div class="Name hidden forHigherOnly" style='margin-top: 0px;'> <?php echo $Name?> </div>
<?php if($higherLevel && !$DAset  && !$WSset && $quesCategory != "NCERT" ) { ?>
	<?php if($flagcheck === false) { ?>
	<div id="quitHigherLevel" class="changeTopic_blue forHigherOnly" onClick="quitHigherLevel();">
	<?php } else { ?>
	<div id="quitHigherLevel" class="changeTopic_blue forHigherOnly" onClick="quittopicJsAlert();">
	<?php } ?>
		<div class="icon_text_higher" data-i18n="questionPage.quitHighLevel"></div>
	</div>
<?php } ?>
<?php if($_SESSION['examCornerCluster']!=1 && !$DAset && !$WSset) { ?>
<?php if($flagcheck === false) { ?>
<div id="changeTopic" class="changeTopic_blue hidden" onClick="endtopic();">
<?php } else { ?>
<div id="changeTopic" class="changeTopic_blue hidden" onClick="endtopicJsAlert();">
	<?php } ?>
	<div class="icon_text" data-i18n="questionPage.changeTopic"></div>
	<div id="pointed"></div>
</div>
<?php } ?>
<?php if($flagcheck === false) { ?>
<div id="endSession" class="endSession_blue hidden" onClick="endsession();">
	<?php } else { ?>
	<div id="endSession" class="endSession_blue hidden" onClick="endsessionJsAlert();">
		<?php } ?>
		<div class="icon_text" data-i18n="questionPage.endSession"></div>
		<div id="pointed"></div>
	</div>
	<!--<?php
									
if (! $progressBarFlag) { ?>
<div class="pieContainer forHighestOnly" <?php if($_SESSION['examCornerCluster']==1) echo "style='display:none'" ?>>
     <div class="pieBackground"></div>
     <div id="pieSlice1" class="hold"><div class="pie"></div></div>
     <div id="pieSlice2"><div class="pie1"></div></div>
</div>
<div id="percent" class="align forHighestOnly"><span  id="spnProgress1"></span>
</div>
<?php } ?>--> 
	<!--	<?php
			if (! $progressBarFlag) { ?>
				<div class="pieContainer forHighestOnly" <?php if($_SESSION['examCornerCluster']==1) echo "style='display:none'" ?>>
				     <div class="pieBackground"></div>
				     <div id="pieSlice1" class="hold"><div class="pie"></div></div>
					 <div id="pieSlice2"><div class="pie1"></div></div>
				</div>
				<div id="percent" class="align forHighestOnly"><span  id="spnProgress1"></span>
                </div>
            <?php } ?>--> 
</div>
<div id="hideShowBar" class="forHigherOnly hidden" onClick="hideBar();">&ndash;</div>
<div id="pnlQuestion">
<div id="menuBar" class="forHighestOnly">
	<div id="sideBar">
		<?php if($_SESSION['examCornerCluster']!=1) { ?>
		<?php if ($DAset || $WSset ) { ?>
		<div id="report" style="text-align: center;cursor:auto;"> <strong>&nbsp;&nbsp;&nbsp;&nbsp;Time Remaining: &nbsp; <span id='countdown'> </span></strong> </div>
		<?php } else if ($isPractiseModule) { ?>
		<div style="margin-top: 20px;margin-left: auto;margin-right: auto;margin-bottom: 10px;height: 70px;width: 70px;background: url(assets/practiseModule.png) no-repeat center center;"></div>
		<div id="practice" onClick="quitPractiseModule();"> <span id="practiceText">Quit</span>
			<div id="practiceIcon" class="circle11">
				<div class="arrow-s"></div>
			</div>
		</div>
		<?php } else {
				if ($flagcheck === false) { ?>
		<div id="report" onClick="endtopic();">
			<?php } else { ?>
			<div id="report" onClick="endtopicJsAlert();">
				<?php } ?>
				<span id="reportText" data-i18n="questionPage.changeTopic"></span>
				<div id="reportIcon" class="circle11">
					<div class="arrow-s"></div>
				</div>
			</div>
			<?php
											}
										}
										?>
			<?php if(isset($_SESSION["importantQuestions"]) && count($_SESSION["importantQuestions"])>0) { ?>
			<div id="report" onClick="getImportantQues();"> <span id="reportText">Important Questions</span>
				<div id="reportIcon" class="circle11">
					<div class="arrow-s"></div>
				</div>
			</div>
			<?php } ?>
			<?php if($flagcheck === false) { ?>
			<div id="questionTrail" onClick="endsession();">
				<?php } else {?>
				<div id="questionTrail" onClick="endsessionJsAlert();">
					<?php } ?>
					<span id="questionTrailText"data-i18n="questionPage.endSession"></span>
					<div id="questionTrailIcon" class="circle11">
						<div class="arrow-s"></div>
					</div>
				</div>
				<div class="empty"></div>
				<?php if($higherLevel && !$isPractiseModule && !$DAset  && !$WSset && $quesCategory != "NCERT") { ?> 
				<?php if($flagcheck === false) { ?>
				<div id="practice" onClick="quitHigherLevel();">
					<?php } else {?>
					<div id="practice" onClick="quittopicJsAlert();">
						<?php } ?>
						<span id="practiceText">Quit Higher level</span>
						<div id="practiceIcon" class="circle11">
							<div class="arrow-s"></div>
						</div>
					</div>
					<?php } else{ ?>
					<div id="dummy"></div>
					<?php }?>
					<?php 
						if (!$isPractiseModule){
							if($quesCategory == "NCERT") { ?>
					<div id="drawer5" style="display: none;">
						<div id="drawer5Icon" class="<?=$sparkieImage?> <?php if($_SESSION["userType"]=="msAsStudent") { echo "level1";}?>"
								<?php if($_SESSION['rewardSystem']!=1) { ?>
								style="position: absolute; background: url(assets/higherClass/dashboard/rewards.png) no-repeat 0 0 !important;width: 50px;height: 50px;margin-top: -30px;margin-left: -50px;" <?php } ?>>
							<div class="redCircle"></div>
						</div>
						<?php } else { ?>
						<div id="drawer5" onClick="javascript:void(0);">
							<div id="drawer5Icon" class="<?=$sparkieImage?> <?php if($_SESSION["userType"]=="msAsStudent") { echo "level1";}?>"
								<?php if($_SESSION['rewardSystem']!=1) { ?>
								style="position: absolute; background: url("
								assets/higherClass/dashboard/rewards.png") no-repeat 0 0 !important;width: 50px;height: 50px;margin-top: -30px;margin-left: -50px;" <?php } ?>>
							<div class="redCircle"></div>
						</div>
						<?php } ?>
					</div>
					<?php } ?>
					<?php if($quesCategory == "NCERT" && $dueDate < date('Y-m-d') && $dueDate!="") { 
					                    	$buttonText = "lateSubmit"; $title = "lateSubmitTitle"; ?>
					<?php } else if($quesCategory == "NCERT" && $dueDate >= date('Y-m-d')){ 
					                    	$buttonText = "saveandsubmitNCERT"; $title = "ncertQuesSubmitTitle";?>
					<?php } else { 
					                    	$buttonText = "submit"; $title = "";?>
					<?php } ?>
					<div id="skipQuestion2" class="button dontKnowAnswer" onClick="submitAnswer('skipped');" data-i18n="questionPage.skipQuestion"></div>
					<?php if($quesCategory == "NCERT") { ?>
					<div id="saveNCERTQuestion2" class="button" onClick="saveNCERTAnswer();" data-i18n="common.saveNCERT" style="display: none;"></div>
					<?php } ?>
					<div id="submitQuestion2" class="button" onClick="submitAnswer()" data-i18n="common.<?=$buttonText?>"></div>
					<?php if($DAset||$WSset) {?>
					<div id="nextQuestion2" class="button <?=($WSset?"worksheetB":"");?>" onClick="submitAnswer(<?=($DAset?"'No Answer'":"");?>); resetStyle();" data-i18n="questionPage.nextQuestion"
						<?php if($quesCategory == "NCERT" && $iPad != true) { ?>style="margin-top: 60px;" <?php } ?>></div>
					<?php } else { ?>
					<div id="nextQuestion2" class="button" onClick="handleClose(); resetStyle();" data-i18n="questionPage.nextQuestion"
						<?php if($quesCategory == "NCERT" && $iPad != true) { ?> style="margin-top: 60px;" <?php } ?>></div>
					<?php } ?>
					<?php if($DAset||$WSset) {?>
					<div id="submittest" class="button <?=($WSset?"worksheetB":"");?>" onClick="handleClose();" data-i18n="questionPage.submittest<?=($WSset?1:"")?>"></div>
					<?php } ?>
					<div id="submitArrow" class="arrow-right"></div>
				</div>
			</div>
			<?php if($quesCategory != "NCERT" && $quesCategory != "topicRevision" && $quesCategory != "diagnosticTest" && $quesCategory != "kstdiagnosticTest" && $_SESSION['examCornerCluster']!=1 && $WSset !=1) { ?>
			<div id="emotToolBar" class="forLowerOnly hidden">
				<div style="clear: both"></div>
				<div id="radioButtons" class="emotSingleDiv">
					<!-- <div id="close" class="emotImage openToolbar" onMouseUp="toolbar();"></div> -->
					<!-- <div id="close" class="emotImage closeToolbar" onMouseUp="toolbar();" style="display: none"></div> -->
					<div id="open" class="emotIcons">
						<label for="like" class="emotImage like" title="Like">
							<input type="radio" name="emotRespond" value="Like" id="like" class="radio" />
						</label>
						<label for="dislike" class="emotImage dislike" title="Dislike">
							<input type="radio" name="emotRespond" value="Dislike" id="dislike" class="radio" />
						</label>
						<!-- <label for="excited" class="emotImage excited" title="Excited">
							<input type="radio" name="emotRespond" value="Excited" id="excited" class="radio" />
						</label>
						<label for="bored" class="emotImage bored" title="Bored">
							<input type="radio" name="emotRespond"value="Bored" id="bored" class="radio" />
						</label>
						<label for="zapped" class="emotImage zapped" title="Confused">
							<input type="radio" name="emotRespond" value="Confused"id="zapped" class="radio" />
						</label> -->

						<label id="cc" title="Comment" onClick="comment(); toolbar(); addHoverClass();" class="emotImage comment_new">
							<div id="comment_new" class="radio"
										data-i18n="questionPage.comment"></div>
						</label>
					</div>
				</div>
			</div>
			<?php if(!$DAset && !$WSset) {
					if ($isPractiseModule) {
			 ?>
					<div id="toolContainer" title="Drag me!" style="display:inline-block;width:72px;height:64px;left:93%;top:443px;">
				<?php 
			}
			else
				echo '<div id="toolContainer" title="Drag me!">';
				?>
					<div id="whiteContainer"></div>
					<!-- <label for="zapped" class="emotImage" title="Confused">
					<input type="radio" name="emotRespond" value="Confused" id="zapped" class="radio" style="display: none" />
					<div class="fixSize">
						<div id="confused" onClick="toolbar1(id);">
							<div class="toolbarText1"
											data-i18n="questionPage.confused"></div>
						</div>
					</div>
					</label>
					<label for="bored" class="emotImage" title="Bored">
					<input type="radio" name="emotRespond" value="Bored" class="radio" style="display: none" />
					<div class="fixSize">
						<div id="bored" onClick="toolbar1(id);">
							<div class="toolbarText2"
											data-i18n="questionPage.bored"></div>
						</div>
					</div>
					</label>
					<label for="excited" class="emotImage" title="Excited">
					<input type="radio" name="emotRespond" value="Excited" class="radio" style="display: none" />
					<div class="fixSize">
						<div id="excited" onClick="toolbar1(id);">
							<div class="toolbarText3"
											data-i18n="questionPage.excited"></div>
						</div>
					</div>
					</label> -->
			<?php	if(!$isPractiseModule)
				{
					?>
					<label for="like" class="emotImage" title="Like">
					<input type="radio" name="emotRespond" value="Like" class="radio" style="display: none" />
					<div class="fixSize">
						<div id="like" onClick="toolbar1(id);">
							<div class="toolbarText5"
											data-i18n="questionPage.like"></div>
						</div>
					</div>
					</label>
					<label for="dislike" class="emotImage" title="Dislike">
					<input type="radio" name="emotRespond" value="Dislike" class="radio" style="display: none" />
					<div class="fixSize">
					
						<div id="dislike" onClick="toolbar1(id);">
							<div class="toolbarText4"
											data-i18n="questionPage.dislike"></div>
						</div>
					</div>
					</label>
			<?php } ?>
					
						<div class="fixSize">
					
						<div id="comment" onClick="comment();" <?php if($isPractiseModule) 
						echo "style='margin-top:8px'";  ?>
						>
							<div class="toolbarText6"
										data-i18n="questionPage.comment"></div>
						</div>
					</div>
				
			</div>
			<?php } 
        	} ?>
			<div class="dd_feedback_header forHigherOnly hidden" >
				<div class="dd_feedback_container"> <img src="assets/correct_answer.png"  class="correnct_answer dd_responce_image"> <img src="assets/wronganswer.png"  class="wrong_answer dd_responce_image">
					<div class="dd_feedback_responce"></div>
				</div>
			</div>
			<?php if($isPractiseModule){
        		if ($_SESSION['dailyDrillArray']['isInternalRequest']) echo '<input type="hidden" id="isInternalRequest" value="1"/>';
        		//$timedTest = $_SESSION['dailyDrillArray']['timedTestCode'];
            	//if ($timedTest!=""){ ?>
			<a class="fancybox fancybox.iframe timedTestForDd" href=""></a>
			<?php
        		if ($theme==2){ ?>
			<div style="float: right;position: absolute;right:18px;margin-top:0px;width:300px;text-align: center;z-index: 9;" id="dailyDrillRightSection">
				<canvas id="tutorial" width="" height="200"> Canvas not available. </canvas>
				<label style="float: left; font-size: 1.6em; width: 78%; margin-left: 65px;z-index: 9;" class="daily-drill-top-bar" id="score-animation">
				<div>Score: <span id="currentScore">0</span></div>
				<div>Level <span id="currentLevel"></span></div>
				</label>
			</div>
			<?php }
            }?>
			<div id="mainBG">
				<div id="scroll">
					<?php if($quesCategory == "NCERT") { ?>
					<div id="exerciseNav">
						<?php
																					$totalGroups = isset ( $_POST ["totalGroups"] ) ? $_POST ["totalGroups"] : 0;
																					$totalGroups = explode ( ",", $totalGroups );
																					$completedGroups = isset ( $_POST ["completedGroups"] ) ? $_POST ["completedGroups"] : 0;
																					$completedGroups = explode ( ",", $completedGroups );
																					$dueDate = isset ( $_POST ["dueDate"] ) ? $_POST ["dueDate"] : 0;
																					foreach ( $totalGroups as $i ) {
																						$completedClass = " pending";
																						if (in_array ( $i, $completedGroups ))
																							$completedClass = " complete";
																						?>
						<span class="groupNav<?=$completedClass?>"
																	id="groupNav<?=$i?>">
						<?=$i?>
						</span>
						<?php
																					}
																					?>
					</div>
					<div id="ncert_legends"> <span data-i18n="questionPage.submitted"
																	style="float: right;"></span><span
																	style="float: right;">&nbsp;</span><span id="boxGreen"
																	style="float: right;"></span> <span
																	style="float: right;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> <span data-i18n="questionPage.pending"
																	style="float: right;"></span><span
																	style="float: right;">&nbsp;</span><span id="boxRed"
																	style="float: right;"></span> </div>
					<div style="clear: both;">&nbsp;</div>
					<?php } ?>
					<?php if($quesCategory != "NCERT" && $_SESSION['examCornerCluster']!=1) { ?>
					<div id="commentBox" <?php if($isPractiseModule && (!isset($_POST['fromChoiceScreen'])||$_POST['fromChoiceScreen']!=1)) echo 'class="practiseMode"'; ?>>
						<div id="commentText" data-i18n="questionPage.commentText"></div>
						<div id="questionSelect"> My comment is on
							<input type="radio" id="commentOn1" name="commentOn" class="commentOn" value="1" checked>this question</input>
							<input type="radio" id="commentOn2" name="commentOn" class="commentOn" value="2">on the previous question</input>
							<input type="radio" id="commentOn3" name="commentOn" class="commentOn" value="3">not related to the questions</input>
						</div>
						<div id="category"> Category :
							<select id="selCategory" name="selCategory" onclick="openTextBox();">
								<option value="">Select</option>
								<option value="Doubt about the question">Doubt about the question</option>
								<option value="Difficulty of questions">Difficulty of questions</option>
								<option value="Error in the question">Error in the question</option>
								<option value="On topic progress">On topic progress</option>
								<option value="About images">About images</option>
								<option value="Doubt about my answer">Doubt about my answer</option>
								<option value="Idea for a Question">Idea for a Question in Mindspark</option>
								<option value="Other">Other</option>
							</select>
						</div>
						<?php //--for comments ?>
						<div style="display:none">
							<div id="commentInfo" title="Comment on previous question"></div>
						</div>
						<div id="commentBoxTr">
							<textarea id="txtcomment" name="comment" wrap="virtual"></textarea>
							<div class="button1" data-i18n="common.submit" onClick="mailComment();"></div>
							<br />
							<div <?php
									if ($theme == 2) { echo 'id="commentCancel2"'; }
									else if($theme == 3) { echo 'id="commentCancel3"'; }
									else if($theme == 1) { echo 'id="commentCancel1"'; }
									?> class="button1" onClick="hideCommentBox();" data-i18n="common.cancel"></div>
						</div>
						<?php if(strcasecmp($_SESSION['subcategory'],"School")==0){ ?>
						<div style='color: red; margin-left: 5%'>Note:
							Your comment can now be seen by your teacher.</div>
						<?php } ?>
					</div>
					<?php } ?>
					<div id="question">
						<input type="hidden" name="avgTimeValue"
																	id="avgTimeValue" value="" />
						<input type="hidden"
																	name="childNm" id="childNm" value="<?= $Name ?>">
						<input
																	type="hidden" name="sdlList" id="sdlList" value="" />
						<form name="quesform" id="quesform" method="post" <?= $isPractiseModule == true ?'class="hasSpeedometer"':'';?>
																	autocomplete="off" onSubmit="return false;">
							<?php if($practiseset != true) { ?>
							<input type="hidden" name="qcode"
																		id="qcode" value="">
							<?php } else{?>
							<input type="hidden" name="qcode" id="qcode"
																		value="<?= $_POST['qcode'] ?>">
							<?php } ?>
							<input type="hidden" name="pageloadtime" id="pageloadtime">
							<input type="hidden" name="mode" id="mode" value="">
							<input type="hidden" name="quesType" id="quesType" value="">
							<input type="hidden" name="secsTaken" id="secsTaken" value="">
							<input type="hidden" name="result" id="result" value="">
							<input type="hidden" name="toughResult" id="toughResult" value="NA">
							<input type="hidden" name="qno" id="qno" value="<?=$qno?>">
							<input type="hidden" name="clusterCode" id="clusterCode" value="">
							<input type="hidden" name="refresh" id="refresh" value="0">
							<input type="hidden" name="endTime" id="endTime">
							<input type="hidden" name="nextQuesLoaded" id="nextQuesLoaded" value="0">
							<input type="hidden" name="nextDAQuesLoaded" id="nextDAQuesLoaded" value="0">
							<input type="hidden" name="topicAttemptNumber" id="topicAttemptNumber" value="<?=$_SESSION['topicAttemptNo']?>">
							<input type="hidden" name="topicAttemptStatus" id="topicAttemptStatus" value="<?=$topicAttemptStatus?>">
							<input type="hidden" name="prevDAQuesFlag" id="prevDAQuesFlag" value="0">
							<input type="hidden" name="DASubmitTestBtn" id="DASubmitTestBtn" value="0">
							<input type="hidden" name="DASubmitTestBetween" id="DASubmitTestBetween" value="0">
							<input type="hidden" name="daPaperCode" id="daPaperCode" value="<?= $daTestCode ?>">
							<input type="hidden" name="worksheetID" id="worksheetID" value="<?= $worksheetID ?>">
							<input type="hidden" name="getDirectQuestion" id="getDirectQuestion" value="0">
							<input type="hidden" name="submitWorksheet" id="submitWorksheet" value="0">
							<input type="hidden" name="worksheetAttemptID" id="worksheetAttemptID" value="<?= $worksheetAttemptID ?>">
							<input type="hidden" name="daInstructionPrompt" id="daInstructionPrompt" value="0">
							<input type="hidden" name="maxAttemptQno" id="maxAttemptQno" value="<?= $maxAttemptQno ?>">
							<input type="hidden" name="daTestQueCount" id="daTestQueCount" value="<?= $paperQueCount ?>">
							<input type="hidden" name="daTestMultipleClickCheck" id="daTestMultipleClickCheck" value="0">
							<input type="hidden" name="DAQuesPromptLoad" id="DAQuesPromptLoad" value="0">
							<input type="hidden" name="quesCategory" id="quesCategory" value="<?= $quesCategory ?>">
							<input type="hidden" name="showAnswer" id="showAnswer" value="">
							<input type="hidden" name="tmpMode" id="tmpMode" value="">
							<input type="hidden" name="quesVoiceOver" id="quesVoiceOver" value="">
							<input type="hidden" name="ansVoiceOver" id="ansVoiceOver" value="">
							<input type="hidden" name="hasExpln" id="hasExpln" value="">
							<input type="hidden" name="childClass" id="childClass" value="<?= $childClass ?>">
							<input type="hidden" name="userResponse" id="userResponse" value="">
							<input type="hidden" name="extraParameters" id="extraParameters" value="">
							<input type="hidden" name="eeResponse" id="eeResponse" value="">
							<input type="hidden" name="dynamicQues" id="dynamicQues" value="">
							<input type="hidden" name="dynamicParams" id="dynamicParams" value="">
							<input type="hidden" name="noOfTrialsAllowed" id="noOfTrialsAllowed" value="">
							<input type="hidden" name="noOfTrialsTaken" id="noOfTrialsTaken" value="0">
							<input type="hidden" name="hintAvailable" id="hintAvailable" value="0" />
							<input type="hidden" name="hintUsed" id="hintUsed" value="0" />
							<input type="hidden" name="userAllAnswers" id="userAllAnswers" value="" />
							<input type="hidden" name="timeTakenHints" id="timeTakenHints" value="0" />
							<input type="hidden" name="timeTakenToughQues" id="timeTakenToughQues" value="0" />
							<input type="hidden" name="signature" id="signature" value="" />
							<input type="hidden" name="validToken" id="validToken" value="" />
							<input type="hidden" name="toughType"  id="toughType" value="" />
							<input type="hidden" name="promptNo" id="promptNo" value="">
							<input type="hidden" name="totalSDLS" id="totalSDLS" value="">
							<input type="hidden" name="practiseModuleTestStatusId" id="practiseModuleTestStatusId" value="<?= $_POST['practiseModuleTestStatusId']; ?>">
							<input type="hidden" name="attemptNo" id="attemptNo" value="<?= $_POST['attemptNo']; ?>">
							<input type="hidden" name="iTargetSpeed" value = "<?=$_POST['scoreForDd']?>" id="iTargetSpeed" />
							<input type="hidden" name="fromPractisePage" value = "<?=$_POST['fromPractisePage']?>" id="fromPractisePage" />
							<input type="hidden" name="isInternelRequest" id="isInternelRequest" value="<?= $_POST['isInternelRequest']; ?>">
							<?php if($_SESSION['examCornerCluster']==1) { ?>
							<input type="hidden" name="impQuesMode"
																		id="impQuesMode" value="0" />
							<?php } ?>
							<input type="hidden" name="daTopicName" id="daTopicName" value="<?=$daTopicName?>" />
							<input type="hidden" name="offlineStatus" id="offlineStatus" value="<?=$_SESSION['offlineStatus']?>" />
							<div id="questionText"> <span data-i18n="questionPage.question"></span><br>
								<span id="q1"></span> </div>
							<div id="questionType" style="display: none;">
								<div id="questionImage"></div>
							</div>
							<br />
							<div class="circle1" id="lblQuestionNoCircle"></div>
							<div id="voiceover" name="voiceover"
																		style="margin-left: -30px;"></div>
							<div class="eqEditorToggler" id="eqEditorToggler"
																		align="center"></div>
							<div id="quesStem">
								<div id="q2" name="q2"
																			<?php if($quesCategory == "NCERT") echo "style='min-height:0px;'"?>></div>
								<div id="hint" name="hint"></div>
								<div id="mainHint" align="center">
									<div class="hintBtn" id="showHint"
																				data-i18n="questionPage.showHint"></div>
									<div class="hintDiv">
										<div id="displayHint">
											<div class="hintText" id="hintText1"></div>
											<div class="hintText" id="hintText2"></div>
											<div class="hintText" id="hintText3"></div>
											<div class="hintText" id="hintText4"></div>
											<div id="bottomBtn" align="center">
												<div class="hintBtn" id="prevHint"
																							data-i18n="questionPage.previousHint"></div>
												<div class="hintBtn" id="nextHint"
																							data-i18n="questionPage.nextHint"></div>
											</div>
										</div>
										<div id="usefullHint">
											<input type="checkbox" name="isHintUsefull"
																						id="isHintUsefull" value="0" />
											I found this hint
											useful. </div>
									</div>
								</div>
								<div id="pnlOptions" style="display: none;">
									<div class="option" id="optionA">
										<div class="optionX optionInactive"
																					onClick="submitAnswer('A')"
																					data-i18n="questionPage.optionAButton"></div>
										<div class="optionText" id="pnlOptionTextA"></div>
									</div>
									<div class="option" id="optionB">
										<div class="optionX optionInactive"
																					onClick="submitAnswer('B')"
																					data-i18n="questionPage.optionBButton"></div>
										<div class="optionText" id="pnlOptionTextB"></div>
									</div>
									<div class="option" id="optionC">
										<div class="optionX optionInactive"
																					onClick="submitAnswer('C')"
																					data-i18n="questionPage.optionCButton"></div>
										<div class="optionText" id="pnlOptionTextC"></div>
									</div>
									<div class="option" id="optionD">
										<div class="optionX optionInactive"
																					onClick="submitAnswer('D')"
																					data-i18n="questionPage.optionDButton"></div>
										<div class="optionText" id="pnlOptionTextD"></div>
									</div>
									<div class="clear"></div>
								</div>
							</div>
							
							<!--For Wild card Questions-->
							<textarea name="wildCardMessage" id="wildcardMessage"
																		style="" wrap="virtual"
																		onKeyPress="return noenter(event)"
																		placeholder="Good if you can explain your answer."></textarea>
							<?php
																		if ($_SESSION ['childClass'] != 1 && $_SESSION ['childClass'] != 2) {
																			?>
							<div class="cq-message-div"> <span>Would you like to explain your answer? <br />
								The
								best explanation may be added on Mindspark along with
								the name of the student who wrote it. </span>
								<input type="hidden" name="setAllowSend"
																			id="setAllowSend" value="0" />
								<textarea name="clusterQuestionMessage"
																			id="clusterQuestionMessage" style="" wrap="virtual"
																			onKeyUp="checkSubmitVisibility(this.value)"></textarea>
							</div>
							<div class="cqebuttondiv">
								<input type="button" name="Send" value="Send"
																			class="cqebutton" id="cqebutton"
																			onClick="saveClusterQuestionMessage()" />
							</div>
							<?php
																		}
																		?>
							
							<!--End-->
							
							<div class="groupQues"></div>
							<div id="pnlAnswer">
								<div id="feedbackContainer_correct">
									<div id="feedback_header_img">
										<div id="feedback_header"></div>
									</div>
								</div>
								<div id="pnlDisplayAnswerContainer">
									<div id="displayanswer"></div>
									<!-- <div id="pnlRateDa">
										<div id="daRatingText">How useful was the answer explanation<?php //if($childClass<3) echo "answer"; else echo "explaination"; ?>?</div>
										<div id="daRating"></div>
										<div id="daHint"></div>
										<br>
										<textarea id="daComment" name="daComment" wrap="virtual" placeholder="Tell us why..." maxlength="255"></textarea><br><br>
										Please click the Next Question button to continue.
									</div> -->									
									<div id="pnlNextButtonInstruction">Please click the Next Question button to continue.</div>
									<div id="arrow" onClick="top1()" class="forHighestOnly"></div>
								</div>
							</div>
							<div id="submit_bar1" class="forLowerOnly">
								<div id="mcqText" data-i18n="questionPage.mcqText"></div>
								<div id="skipQuestion1"
																			class="button <?php if($quesCategory=='diagnosticTest' || $quesCategory == "kstdiagnosticTest") echo 'dntKnow1'?>"
																			onClick="submitAnswer('skipped');"
																			data-i18n="questionPage.skipQuestion"
																			style="color: yellow;top: 169%;float: right;margin-left: 1% !important;	"></div>
								<div id="submitQuestion1" class="button"
																			onClick="submitAnswer()" data-i18n="common.submit"></div>
								<div id="nextQuestion1" class="button"
																			onClick="handleClose();"
																			data-i18n="questionPage.nextQuestion"></div>
							</div>
							<!-- next button added for class 3 worksheet -->
							<?php if($WSset && $theme==1){ ?>
								<div id="nextQuestion" class="button worksheetNextButton" onClick="submitAnswer();" data-i18n="questionPage.nextQuestion" ></div>
								<?php } ?>
						</form>
					</div>
					<!-- end question div -->
					<div id="submit_bar"
																class="top_bar_part4 forHigherOnly hidden <?= $isPractiseModule == true ?'daily-drill-top-bar':'';?>">
						<div id="arrow2" onClick="top1()"
																	data-i18n="questionPage.question"></div>
						<?php if($quesCategory == "NCERT" && $dueDate < date('Y-m-d') && $dueDate!="") { $buttonText = "lateSubmit"; $title = "lateSubmitTitle"; ?>
						<?php } else if($quesCategory == "NCERT" && $dueDate >= date('Y-m-d')){ $buttonText = "saveandsubmitNCERT"; $title = "ncertQuesSubmitTitle";?>
						<?php } else { $buttonText = "submit"; $title = "";?>
						<?php } ?>
						<div id="mcqText"
																	data-i18n="questionPage.mcqText"></div>
						<div id="submitQuestion" class="button hidden"
																	onClick="submitAnswer()"
																	data-i18n="common.<?=$buttonText?>"></div>
						<?php if($quesCategory == "NCERT") { ?>
						<div id="saveNCERTQuestion"
																	class="button hidden" onClick="saveNCERTAnswer();"
																	data-i18n="common.saveNCERT" style="display: none;"></div>
						<?php } ?>
						<div id="skipQuestion" class="button hidden"
																	onClick="submitAnswer('skipped');"
																	data-i18n="questionPage.skipQuestion"
																	style="color: yellow"></div>
						<?php if($DAset||$WSset) {?>
						<div id="nextQuestion" class="button hidden"
																	onClick="submitAnswer(<?=($DAset?"'No Answer'":"");?>);"
																	data-i18n="questionPage.nextQuestion"></div>
						<?php } else { ?>
						<div id="nextQuestion" class="button hidden"
																	onClick="handleClose();"
																	data-i18n="questionPage.nextQuestion"></div>
						<?php } ?>
						<?php if($DAset||$WSset) {?>
						<div id="submittest" class="button hidden <?=($WSset?"worksheetB":"");?>"
																	onClick="handleClose();"
																	data-i18n="questionPage.submittest<?=($WSset?1:"")?>"></div>
						<?php } ?>
					</div>
				</div>
				<!-- end scroll div --> 
			</div>
		</div>
		<!-- end pnlQuestion div -->
		<div id="pnlLoading" name="pnlLoading">
			<div align="center" class="quesDetails"> <br />
				<br />
				<br />
				<br />
				<p> Loading, please wait...<br />
					<img
																src="assets/loader.gif"> </p>
			</div>
		</div>
	</div>
	<!-- end container div -->
	<?php // For Practice Cluster  // Starts Here..   ?>
	<div id="questionTemplate" style="display: none;">
		<div class="singleQuestion">
			<table width="100%" border="0" cellspacing="1"
														cellpadding="3">
				<tr>
					<td width="40px"><div></div></td>
					<td width="3%" align="left" valign="top"><div align="left">
							<div id="q1" name="q1" class="subQuestion"
																		align="center"></div>
							<div class="eqEditorToggler" align="center"></div>
						</div></td>
					<td valign="top" align="left"><div>
							<div id="q2" name="q2" class="question"
																		<?php if($quesCategory == "NCERT") echo "style='min-height:0px;'"?>></div>
						</div>
						<div id="hint" name="hint"></div></td>
				</tr>
			</table>
			<div id="q4" name="q4"
														style="width: 100%; margin-left: 100px;" align="left"></div>
		</div>
	</div>
</div>
<?php // Ends Here..    ?>
<div id="glossaryDiv">
	<div id="glossaryContainer">
		<div id="glossaryClose" title="Close"></div>
		<div style="clear: both"></div>
		<div id="leftGlossary">
			<div id="glossaryTitle"></div>
			<div style="clear: both"></div>
			<div id="glossaryImage"></div>
		</div>
		<div id="rightGlossary">
			<div id="glossaryBody"></div>
		</div>
		<div style="clear: both"></div>
		<div id="relatedGlossary"> <strong>Related Terms: </strong> <a
														href="javascript:void(0)">Angle</a>, <a
														href="javascript:void(0)">Right Angle</a> </div>
	</div>
</div>
<?php if($_SESSION["userType"]=="msAsStudent") { ?>
<script>
        userType = 'msAsStudent';
    </script>
<div id="msAsStudentInfo"
											style="position: absolute; top: 215px; min-width: 160px; right: 0px;z-index: 10000;"
											class="questionPrompts1"></div>
<?php } ?>
<div id="tagMsgBox"
											style="position: fixed; right: 90px; bottom: 90px; background-color: #00FFFF; width: 230px; padding: 10px; color: black; border: #0000cc 2px dashed; display: none;">
	<table>
		<tr>
			<td><span id="showTaggedQcode"></span><br>
				<strong>Comment:</strong></td>
		</tr>
		<tr>
			<td><textarea rows="4" cols="25" id="tagComment"
															name="tagComment"></textarea>
				<input type="hidden"
														name="tagQcode" id="tagQcode" value=""></td>
		</tr>
		<tr>
			<td align="center"><input type="submit" id="tagComentSave"
														name="tagCommentSave" value="Save">
				<input type="button"
														id="closeBox" name="closeBox" value="Close"
														onClick="showTagBox('tagMsgBox', 'none', '');"></td>
		</tr>
	</table>
</div>
<script type="text/javascript"
											src="libs/question/nparse.js?ver=1"></script> 
<script type="text/javascript"
											src="libs/question/commonErrors.js?ver=1"></script> 
<script type="text/javascript"
											src="libs/question/nonnparse.js?ver=2"></script> 
<script type="text/javascript" src="libs/parse.js"></script>
<?php if(isset($_SESSION['diagnosticTest']) && $_SESSION['diagnosticTest']!="") { ?>

<form id="diagnosticTest" name="diagnosticTest" method="post"
											autocomplete="off" onKeyPress="return checkEnter(event)">
	<input type="hidden" name="nextQcode" id="nextQcode"
												value="<?=$nextQcode?>">
	<input type="hidden" name="qno"
												id="qno" value="<?=$currQues?>">
	<input type="hidden"
												name="quesCategory" id="quesCategory" value="comprehensive">
	<input type="hidden" name="showAnswer" id="showAnswer"
												value="<?=$showAnswer?>">
	<input type="hidden"
												name="remedialMode" id="remedialMode" value="0">
	<input
												type="hidden" name="timedtestMode" id="timedtestMode"
												value="0">
	<input type="hidden" name="timedTest"
												id="timedTest" value="0">
	<input type="hidden"
												name="activityMode" id="activityMode" value="0">
	<input
												type="hidden" name="clusterMode" id="clusterMode" value="0">
	<input type="hidden" name="gameID" id="gameID" value="">
	<input
												type="hidden" name="qcode" id="qcode" value="0">
	<input
												type="hidden" name="mode" id="mode">
</form>
<?php } ?>

<form name="kstdiagnosticTest" id="kstdiagnosticTest" action="controller.php" method="post">
	<input type="hidden" name="ttCode" id="ttCode" value="<?=$_SESSION['teacherTopicCode']?>">
	<input type="hidden" name="mode" id="mode" value="ttSelection">
	<input type="hidden" name="kstAttemptNo" id="kstAttemptNo" value="<?=$_SESSION['topicAttemptNo']?>">
</form>

<div style="display: none">
	<div id="openHelp">
		<h2 align="center">Quick Tutorial</h2>
		<iframe id="iframeHelp" width="960px" height="440px"
													scrolling="no"></iframe>
	</div>
</div>
<?php if($quesCategory=="diagnosticTest") {?>
		<?php if($_SESSION['diagnosticTestType']=="Assessment") { 
			$alertText = "Awesome ".ucfirst(strtolower($_SESSION ['childName']))."! You completed the topic.Taking a test helps to understand what you can do next.<br>Give it your best shot! <br>";
			$label = "Continue";
		}
		else
		{
			$alertText = "Doing a few questions first will tell us where we can help you.<br>";
			$label = "Let us start!";
		}
			?>
<script>diagnosticTestPromptPromptfn('<?= $alertText; ?>','<?= $label ?>');</script>
<?php }?>
<?php if($quesCategory=="kstdiagnosticTest") {?>
		<?php if($_SESSION['kstdiagnosticTestType']=="Assessment") { 
			$alertText = "Awesome ".ucfirst(strtolower($_SESSION ['childName']))."! You completed the topic.Taking a test helps to understand what you can do next.<br>Give it your best shot! <br>";
			$label = "Continue";
		}
		else
		{
			$alertText = "Doing a few questions first will tell us where we can help you.<br>";
			$label = "Let us start!";
		}
			?>
	<script>diagnosticTestPromptPromptfn('<?= $alertText; ?>','<?= $label ?>');</script>
<?php }?>
<?php include("footer.php");?>
<?php
        function emotToolbarTaggingCount($userID)
        {
			$taggedCount = 0;
			$sql = "SELECT COUNT(likeID) FROM adepts_emotToolbarTagging WHERE userID=" . $userID . " AND DATE(lastModified)=CURDATE()";
			$result = mysql_query ( $sql );
			if ($row = mysql_fetch_array ( $result ))
				$taggedCount = $row [0];
			return $taggedCount;
		}
        function skipQuestions()
        {
					$skippedQuestions = "";
					$sql = "Select qcode from adepts_skipQuestionsMaster where skip=1";
					$result = mysql_query ( $sql );
            while($rw=mysql_fetch_array($result))
            {
						$skippedQuestions = $skippedQuestions . $rw [0] . ",";
					}
					$skippedQuestions = substr ( $skippedQuestions, 0, - 1 );
					$skippedQuestions = "'" . str_replace ( ",", "','", $skippedQuestions ) . "'";
					return $skippedQuestions;
				}
        function getDiagnosticTestName($diagnosticTestID)
        {
					$sq = "SELECT title FROM adepts_diagnosticTestMaster WHERE diagnosticTestID='$diagnosticTestID'";
					$rs = mysql_query ( $sq );
					$rw = mysql_fetch_array ( $rs );
					return $rw [0];
				}
        function getDiagnosticTestType()
        {
					$sq = "SELECT testType FROM adepts_diagnosticTestAttempts WHERE userID=" . $_SESSION ['userID'] . " ORDER BY attemptID DESC LIMIT 1";
					$rs = mysql_query ( $sq );
					$rw = mysql_fetch_array ( $rs );
					return $rw [0];
		}

		function getClusterName($clusterCode)
		{
			$clusterName = "";
			$sql = "SELECT cluster FROM adepts_clusterMaster WHERE clusterCode='$clusterCode'";
			$result = mysql_query($sql);
			$line = mysql_fetch_array($result);
			return $line[0];
		}	
		?>
<?php if($DAset) { ?>
<script>

var countdown = {
    startInterval : function() {
		<?php if($spendtime > 0) { ?>
			var count = <?php echo json_encode($spendtime); ?>;
			<?php } else { ?>
        var count = 1800; // 30 minute timeout
			<? } ?>
        var currentId = setInterval(function(){

			var minutes = parseInt( count / 60 );
			if((count % 60)>0)
				minutes++;
			if(minutes>30)
				minutes = 30;
			if(count <= 0)
			{
				daSessionExpire();
				clearInterval(currentId);
			}
			if(minutes==1)	
            	$('#countdown').html(minutes + " Minute");
			else
				$('#countdown').html(minutes + " Minutes");
			if(count <= 300) { // when there's thirty seconds...
                $('#countdown').css("color","red");
            }
            --count;
			if((count % 30)==0)
			{
				$.post("controller.php", "mode=updateDaTimeSpent&timeSpent=" + count+"&qno="+$("#qno").val(), function (data) {
					
        		});
			}
        }, 1000);
        countdown.intervalId = currentId;
    }
};
countdown.startInterval();
</script>
<?php } ?>
<?php if($WSset) { ?>
<script>

var countdown = {
    startInterval : function() {
		<?php if($spendtime > 0) { ?>
			var count = <?php echo json_encode($spendtime); ?>;
	        var currentId = setInterval(function(){
				var minutesWs = parseInt( count / 60 );
				if((count % 60)>0)
					minutesWs++;

				if(count <= 0)
				{
					wsSessionExpire();
					clearInterval(currentId);
				}
				if(minutesWs==1)	
	            	$('#countdown').html(minutesWs + " Minute");
				else
					$('#countdown').html(minutesWs + " Minutes");
				if(count <= 300) { // when there's thirty seconds...
	                $('#countdown').css("color","red");
	            }
	            --count;
				if((count % 30)==0)
				{
					$.post("controller.php", "mode=updateWSTimeSpent&timeSpent=" + count+"&qno="+$("#qno").val(), function (data) {
						
	        		});
				}
	        }, 1000);
	        countdown.intervalId = currentId;
			<?php } else { ?>
				$('#countdown').parent().hide();
        		//var count = 1800; // 30 minute timeout
			<? } ?>
    }
};
countdown.startInterval();
</script>
<?php } ?>
<?php if($isPractiseModule) { 
		if(!isset($_POST['fromPractisePage']) || $_POST['fromPractisePage'] == 0) {
			?>
		<script>
				function startTimer(duration, display) {
				    var timer = duration, minutes, seconds;
				    var ddTimer = setInterval(function () {
				        minutes = parseInt(timer / 60, 10)
				        seconds = parseInt(timer % 60, 10);

				        minutes = minutes < 10 ? "0" + minutes : minutes;
				        seconds = seconds < 10 ? "0" + seconds : seconds;

				        display.text(minutes + ":" + seconds);

				        if ($('.fancybox-iframe').length>0 && $('.fancybox-iframe').attr('src').indexOf('timedTest')>=0) return;
				        if ($("#prmptContainer_resultPrompt").is(':visible')){
				        	clearInterval(ddTimer);
				        }
				        if(++timer >= 300){
				        	minutes = parseInt(300 / 60, 10)
				        	seconds = parseInt(300 % 60, 10);

				        	minutes = minutes < 10 ? "0" + minutes : minutes;
				        	seconds = seconds < 10 ? "0" + seconds : seconds;

				        	display.text(minutes + ":" + seconds);
				        	if (!$(".promptContainer").is(":visible")) {
				        		ddSessionExpire();
				        		clearInterval(ddTimer);
				        	}
				        	practiseModuleTimeStatus=1;
				        }
				        //update table every 10 secounds.
				        if(timer % 10 == 0){
				        	$.post("controller.php", "mode=updateDdTimeSpent&timeSpent=" + (300-timer), function (data) {});
				        }
				    }, 1000);
				}
				<?php if(!$topicPage) {?>
				var fiveMinutes = 300-<?=$_POST['timeTakenForDd'];?>, display = $('#timeRemaining');
				startTimer(fiveMinutes, display);
				<?php } ?>
		</script>
<?php 
		}?>
	<style type="text/css">
.promptContainer {
	z-index: 9998;
}
</style>
	<script>
	$(document).ready(function(){
		var timedTestIntervel;
		$(".timedTestForDd").fancybox({
			'width'  : '100%',
			<?php
			if($Android ==true){ ?>
				'height'  : '100%',
				'position'  : 'absolute',
				'padding'  : '0px',
				'margin'  : '0px', 
			<?php } ?>
	        closeBtn : false,
	        closeClick : false,
	        helpers : { 
	            overlay : {
	                closeClick: false
	            } // prevents closing when clicking OUTSIDE fancybox
	        },
	        keys : {
	            close: null
	        },
			 afterLoad:function(){
					setTimeout(CheckTimedTestStatus, 100);
					//setTryingToUnload();
			}

	    });
		$(".info-sign").click(function(){
			var currentSign = $(this).attr("id");
			if(currentSign == "+"){
				$(".info-content").show("slow");
				$(this).attr("id","-");
			}else{
				$(".info-content").hide("slow");
				$(this).attr("id","+");
			}
		});


	});


	</script>
<div style="display:none">
	<div id="resultPrompt">
		<div id="pnlLoading" name="pnlLoading">
			<div align="center" class="quesDetails"><br />
				<br />
				<br />
				<br />
				<p>Loading, please wait...<br />
					<img
						src="assets/loader.gif"></p>
			</div>
		</div>
	</div>
</div>
<?php } ?>
