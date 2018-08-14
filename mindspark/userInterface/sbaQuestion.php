<?php
include("check1.php");
require_once 'constants.php';
if(!isset($_SESSION['userID']) || count($_POST)==0)
{
    echo "You are not authorised to access this page! (URL copy pasted in the browser!)";
    exit;
}
$teacherTopicName = $_SESSION['teacherTopicName'];
$sessionID = $_SESSION['sessionID'];

$Name = explode(" ", $_SESSION['childName']);
$Name = $Name[0];
$childClass = $_SESSION['childClass'];
$higherLevel = $_SESSION['higherLevel'];
$quesCategory = "sba";
$qcode	=	$_POST["qcode"];
$totalQuestion	=	$_POST["totalQuestion"];
$totalTime		=	$_POST["totalTime"];
$timeLeft		=	$_POST["timeLeft"];
$qcodeStr		=	$_SESSION["qcodeStr"];

unset($_SESSION["qcodeStr"]);
?>

<?php include("header.php");?>
        <title>Questions</title>
        <link href="css/sba/common.css" rel="stylesheet" type="text/css">
        <link href="css/sba/glossary.css" rel="stylesheet" type="text/css">
        <link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/mindspark/js/load.js"></script>
    <?php
        if ($theme==1) { ?>
            <link href="css/sba/lowerClass.css" rel="stylesheet" type="text/css">
            <link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
            <script type="text/javascript" src="libs/sba/scriptLowerClass.js"></script>
<?php } else if($theme==2) { ?>
            <link href="css/sba/middleClass.css" rel="stylesheet" type="text/css">
            <link href="css/common.css" rel="stylesheet" type="text/css">
            <script type="text/javascript" src="libs/sba/scriptMiddleClass.js"></script>
<?php } else if($theme==3) { ?>
			<link href="css/commonHigherClass.css" rel="stylesheet" type="text/css">
            <link href="css/sba/higherClass.css" rel="stylesheet" type="text/css">
            <script type="text/javascript" src="libs/sba/scriptHigherClass.js"></script>
<?php }?>
        <script type="text/javascript" src="libs/i18next.js"></script>
        <script type="text/javascript" src="libs/translation.js"></script>
        <script type="text/javascript" src="libs/sba/scriptCommon.min.js"></script>
        <script type="text/javascript" src="libs/question/ms_ques.min.js"></script>
        <script type="text/javascript" src="libs/question/glossary.js"></script>
        <script type="text/javascript" src="libs/buddy2.js"></script>
        <script type="text/javascript" src="libs/easeljs-0.5.0.min.js"></script>
        <script type="text/javascript" src="libs/fracbox.js"></script>
        <script type="text/javascript" src="libs/maxlength.js"></script>
        <script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>

        <script>
            var langType = '<?=$language?>';
			function logoff()
			{
				window.location="logout.php";
			}
		<?php 
		$emoteToolbarTagCount = emotToolbarTaggingCount($_SESSION['userID']);
		echo "var emoteToolbarTagCount = ".$emoteToolbarTagCount.";\n";
		?>
		document.onkeypress = checkKeyPress;
		if (document.layers) document.captureEvents(Event.KEYPRESS);
		<!--
		
		var message="Sorry, right-click has been disabled";
		if (document.layers)
		{
				document.captureEvents(Event.MOUSEDOWN);document.onmousedown=clickNS;
		}
		else
		{
				document.onmouseup=clickNS;
				document.oncontextmenu=clickIE;
		}
		document.oncontextmenu=new Function("return false")
		// -->
		</script>
    </head>
    <body onResize="adjustScreenElements();" class="translation">
        <span class='math' style='display:none'>{1 \over 2}</span>
        <input type="hidden" name="problemid" id="problemid" value="">
        <?php if($childClass>7) { ?>
            <div id="wildcardInfo" title="Wildcard Information" style="display: none;">
            <table border="0" bgcolor="#DEB887" cellpadding="5" cellspacing="3">
                <tr bgcolor="#FFD39B">
                    <th>Here is an opportunity to earn 10 reward points by answering a wild card question!!!</th>
                </tr>
                <tr bgcolor="#FFD39B">
                    <th>A wild card question is a question that may not be related to the topic that you are currently doing. It is asked to test your alertness and ability to answer a question from other topics. You will get 10 reward points for answering a wild card question correctly. Remember that wild card questions will not affect your performance in the regular topics.</th>
                </tr>
            </table>
            </div>
            <?php } else { ?>
            <div id="wildcardInfo" title="Wildcard Information" style="display: none;">
            <table border="0" bgcolor="#DEB887" cellpadding="5" cellspacing="3">
                <tr bgcolor="#FFD39B">
                    <th>Here is an opportunity to earn a full sparkie by answering a wild card question!!!</th>
                </tr>
                <tr bgcolor="#FFD39B">
                    <th>A wild card question is a question that may not be related to the topic that you are currently doing. It is asked to test your alertness and ability to answer a question from other topics. You will get a sparkie for answering a wild card question correctly. Remember that wild card questions will not affect your performance in the regular topics.</th>
                </tr>
            </table>
            </div>
        <?php } ?>
        <div id="top_bar" class="top_bar_part3">
            <div class="logo">
            </div>
			
			<div id="studentInfoLowerClass" class="forLowerOnly">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?=$Name?>&nbsp;</span></a>
                            </li>
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
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?=$Name?>&nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a href='logout.php'><span data-i18n="common.logout"></span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?=$childClass.$childSection?></span></div>
            </div>
        </div>

            <div id="help" style="visibility:hidden;">
                <div class="help"></div>
                <div class="helpText" data-i18n="common.help"></div>
            </div>
            <div id="logout" class="linkPointer hidden" onClick="logoff();">
                <div class="logout"></div>
                <div class="logoutText" data-i18n="common.logout"></div>
            </div>
            <div id="whatsNew" style="visibility:hidden;">
                <div class="whatsNew"></div>
                <div class="whatsNewText" data-i18n="common.whatsNew"></div>
            </div>
        </div> <!-- End top_bar -->

        <div id="container">
            <div id="endTopic">
                <div id="topicText" data-i18n="questionPage.endTopicText"></div>
                <div class="button1" data-i18n="common.yes" onClick="changeTopic()"></div>
                <div class="button1 textUppercase" onClick="cancel();" data-i18n="common.no"></div>
            </div>
            <div id="endSessionClick">
                <div id="topicText" data-i18n="questionPage.endSessionText"></div><br/>
                <div class="button1" data-i18n="common.yes" onClick="finalSubmit(1);"></div>
                <div class="button1 textUppercase" onClick="cancel();" data-i18n="common.no"></div>
            </div>
            <div id="higherLevelClick" class="hidden">
                <div id="topicText" data-i18n="questionPage.quitHighLevelText"></div>
                <div class="button1" data-i18n="common.yes" onClick="quitTopic();"></div>
                <div class="button1 textUppercase" onClick="cancel();" data-i18n="common.no"></div>
            </div>
            <div id="info_bar">
				<div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase">TEST</span></div>
                </div>
				<div class="arrow-1"></div>
                <div id="lowerClassProgress"></div>
                <div id="topic">
                    <div id="questionType" style="display:none;">
                        <div id="questionImage">
                        </div>
                        <div id="QT"></div>
                    </div>

                    <div id="topic_name" class="forLowerOnly hidden"></div>

                    <div id="session">
                        <span data-i18n="questionPage.session" class="labelText"></span><span class="sessionColor" ><?php echo $sessionID; ?></span>
                    </div>
                    <div id="totalQuestion">
                        <span class="labelText">Questions: </span><span id="curQuesNumber" class="sessionColor"></span><span class="sessionColor">/<?=$totalQuestion?></span>
                    </div>
                    <div id="totalTime">
                        <span class="labelText">Total time: </span><span class="sessionColor"><?=$totalTime?> mins</span>
                    </div>
                    <div id="timeLeftDiv">
                        <span class="labelText">Time left: </span><span class="sessionColor" id="timeLeftDisp"></span>
                    </div>
                </div>
                <div id="student" class="hidden forHigherOnly">
                </div>
                <div class="class hidden forHigherOnly">
                    <strong data-i18n="common.class"></strong> <?php echo $childClass; ?>
                </div>
                <div class="Name hidden forHigherOnly">
<?php echo $Name ?>
                </div>
                
				<div id="percent" class="align forHighestOnly"><span  id="spnProgress1"></span>
                </div>
            </div>
            <div id="pnlQuestion">
                <div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
				<!--<div id="report" onClick="endtopic();">
					<span id="reportText" data-i18n="questionPage.changeTopic"></span>
					<div id="reportIcon" class="circle11"><div class="arrow-s"></div></div>
					
				</div>
				<div id="questionTrail" onClick="endsession();">
					<span id="questionTrailText" data-i18n="questionPage.endSession"></span>
					<div id="questionTrailIcon" class="circle11"><div class="arrow-s"></div></div>
					
				</div>
				<div class="empty">
				</div>
				<div id="practice">
					<span id="practiceText">Quit Higher level</span>
					<div id="practiceIcon" class="circle11"><div class="arrow-s"></div></div>
					
				</div>-->
				<div id="drawer5"><div id="drawer5Icon"><div class="redCircle"></div></div>
				</div>
				<?php if($quesCategory == "NCERT" && $dueDate < date('Y-m-d')) { $buttonText = "lateSubmit"; $title = "lateSubmitTitle"; ?>
                        <!--<input type="button" title="Exercise will be considered as late submit" class="button" value="LATE SUBMIT" id="submitQuestion" name="btnSubmit" onClick="submitAnswer();" style="margin-top:25px; display:none;">-->
                        <?php } else if($quesCategory == "NCERT" && $dueDate >= date('Y-m-d')){ $buttonText = "submit"; $title = "ncertQuesSubmitTitle";?>
                        <!--<input type="button" title="You can not edit answers after clicking submit" class="button" value="SUBMIT" id="submitQuestion" name="btnSubmit" onClick="submitAnswer();" style="margin-top:25px; display:none;">-->
                        <?php } else { $buttonText = "submit"; $title = "";?>

                        <?php } ?>
                <div id="reviewQuestion" class="button" onClick="reviewQuestion()" >Review Question</div><!--data-i18n="questionPage.reviewQuestion"-->
                <div id="reviewArrow" class="arrow-right"></div>
                <div id="completeTest" class="button completeHigher" onClick="submitTest()" >Submit Test</div>
				<div id="submitQuestion2" class="button" onClick="submitAnswer()" data-i18n="questionPage.nextQuestion"></div>
                <div id="nextQuestion2" class="button" onClick="handleClose();" data-i18n="questionPage.nextQuestion"></div>
				<div id="submitArrow" class="arrow-right"></div>
			</div>
			</div>
                <div id="mainBG">
                <div id="scroll">
                	<div id="reviewDiv">
                    	<table align="center" width="auto" border="0" cellspacing="3" cellpadding="3">
                        	<tr>
                        <?php $q=1; foreach($qcodeStr as $qcode1=>$attempt) { ?>
                                <td align="right" onClick="displayQuestion(<?=$q?>,<?=$qcode1?>)" class="quesNoTd" id="quesno_<?=$qcode1?>"><?=$q?></td>
                                <td align="center" onClick="displayQuestion(<?=$q?>,<?=$qcode1?>)" class="<?php if($attempt==1) echo "attemptedTd"; else echo "notAttemptedTd";?>" id="ques_<?=$qcode1?>"><?php if($attempt==0) echo "Not ";?>Answered</td>
                        <?php if($q%2==0) { ?>
                            </tr><tr>
						<?php } $q++; } ?>
                        </table>
                    </div>
                    <div id="question">
                        <form name="quesform" id="quesform" method="post" autocomplete=off onSubmit="return false;">
                            <input type="hidden" name="qcode" id="qcode" value="<?=$qcode?>">
                            <input type="hidden" name="pageloadtime" id="pageloadtime">
                            <input type="hidden" name="mode" id="mode" value="">
                            <input type="hidden" name="quesType" id="quesType" value="">
                            <input type="hidden" name="secsTaken" id="secsTaken" value="">
                            <input type="hidden" name="result" id="result" value="">
                            <input type="hidden" name="qno" id="qno" value="<?= $qno ?>">
                            <input type="hidden" name="clusterCode" id="clusterCode" value="">
                            <input type="hidden" name="refresh" id="refresh" value="0">
                            <input type="hidden" name="endTime" id="endTime">
                            <input type="hidden" name="nextQuesLoaded" id="nextQuesLoaded" value="0">
                            <input type="hidden" name="quesCategory" id="quesCategory" value="<?= $quesCategory ?>">
                            <input type="hidden" name="showAnswer" id="showAnswer" value="">
                            <input type="hidden" name="tmpMode" id="tmpMode" value="">
                            <input type="hidden" name="quesVoiceOver" id="quesVoiceOver" value="">
                            <input type="hidden" name="ansVoiceOver" id="ansVoiceOver" value="">
                            <input type="hidden" name="hasExpln" id="hasExpln" value="">
                            <input type="hidden" name="childClass" id="childClass" value="<?= $childClass ?>">
                            <input type="hidden" name="userResponse" id="userResponse" value="">
                            <input type="hidden" name="eeResponse" id="eeResponse" value="">
                            <input type="hidden" name="dynamicQues" id="dynamicQues" value="">
                            <input type="hidden" name="dynamicParams" id="dynamicParams" value="">
                            <input type="hidden" name="noOfTrialsAllowed" id="noOfTrialsAllowed" value="">
                            <input type="hidden" name="noOfTrialsTaken" id="noOfTrialsTaken" value="0">
                            <input type="hidden" name="hintAvailable" id="hintAvailable" value="0" />
                            <input type="hidden" name="hintUsed" id="hintUsed" value="0" />
                            <input type="hidden" name="signature" id="signature" value="" />
                            <input type="hidden" name="totalQuestion" id="totalQues" value="<?=$totalQuestion?>" />
                            <input type="hidden" name="totalTime" id="totalTimeAllowed" value="<?=$totalTime?>" />
							<input type="hidden" name="timeLeft" id="timeLeft" value="<?=$timeLeft?>" />
                            <input type="hidden" name="reviewQuestions" id="reviewQuestions" value="0" />
                            <div id="questionText">
                                <span data-i18n="questionPage.question"></span><br><span id="q1"></span>
                            </div>
                            <br />
                            <div class="circle1" id='lblQuestionNoCircle'>
                            </div>
                            <div class="eqEditorToggler" id="eqEditorToggler" align="center"></div>
                            <div id="quesStem">
                            <div id="q2" name="q2"></div>
                            <div id="hint" name="hint"></div>
                            <div id="mainHint" align="center">
                                <div class="hintBtn" id="showHint" data-i18n="questionPage.showHint"></div>
                                <div class="hintDiv">
                                    <div id="displayHint">
                                        <div class="hintText" id="hintText1"></div>
                                        <div class="hintText" id="hintText2"></div>
                                        <div class="hintText" id="hintText3"></div>
                                        <div class="hintText" id="hintText4"></div>
                                        <div id="bottomBtn" align="center">
                                            <div class="hintBtn" id="prevHint" data-i18n="questionPage.previousHint"></div>
                                            <div class="hintBtn" id="nextHint" data-i18n="questionPage.nextHint"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="pnlOptions" style="display: none;">
                                <div class="option" id="optionA"><div class="optionX optionInactive" onClick="submitAnswer('A')" data-i18n="questionPage.optionAButton"></div><div class="optionText" id="pnlOptionTextA"> </div>
                                </div>
                                <div class="option" id="optionB"><div class="optionX optionInactive" onClick="submitAnswer('B')" data-i18n="questionPage.optionBButton"></div><div class="optionText" id="pnlOptionTextB"></div>
                                </div>
                                <div class="option" id="optionC"><div class="optionX optionInactive" onClick="submitAnswer('C')" data-i18n="questionPage.optionCButton"></div><div class="optionText" id="pnlOptionTextC"></div>
                                </div>
                                <div class="option" id="optionD"><div class="optionX optionInactive" onClick="submitAnswer('D')" data-i18n="questionPage.optionDButton"></div><div class="optionText" id="pnlOptionTextD"> </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                            </div>
                            <div class="groupQues">
                            </div>
                            <div id="pnlAnswer">
                            	<div id="feedbackContainer_correct">
                                    <div id="feedback_header_img">
                                        <div id="feedback_header"></div>
                                    </div>
                                </div>
                                <div id="pnlDisplayAnswerContainer">
                                    <div id="displayanswer">
                                    </div>
                                    <label for="markedWrong" class="markedWrong">
                                        <input name="markedWrong" id="markedWrong" type="checkbox" value="markedWrong" class="checkbox" />
                                        <span data-i18n="questionPage.markedWrongText"></span><br />
                                    </label>
                                    <div id="markedWrongTextTR" style="display:none;">
                                        <textarea name="markedWrongText" id="markedWrongText" style="width:600px;" data-maxsize="250" data-output="status1" wrap="virtual" onFocus="if (this.value == placeHolderText) {
                this.value = '';
            }" onBlur="if (this.value == '') {
                this.value = placeHolderText;
            }"></textarea><br/>
                                        <div id="status1" style="width:600px;font-weight:bold;text-align:right;font-size:x-small;"></div>
                                    </div>
									<div id="arrow" onClick="top1()" class="forHighestOnly"></div>
                                </div>
                            </div>
                        </form>
                    </div> <!-- end question div -->
                    <div id="submit_bar" class="top_bar_part4 forHigherOnly">
                        <div id="arrow" onClick="top1()" data-i18n="questionPage.question"></div>
                        <?php if($quesCategory == "NCERT" && $dueDate < date('Y-m-d')) { $buttonText = "lateSubmit"; $title = "lateSubmitTitle"; ?>
                        <!--<input type="button" title="Exercise will be considered as late submit" class="button" value="LATE SUBMIT" id="submitQuestion" name="btnSubmit" onClick="submitAnswer();" style="margin-top:25px; display:none;">-->
                        <?php } else if($quesCategory == "NCERT" && $dueDate >= date('Y-m-d')){ $buttonText = "submit"; $title = "ncertQuesSubmitTitle";?>
                        <!--<input type="button" title="You can not edit answers after clicking submit" class="button" value="SUBMIT" id="submitQuestion" name="btnSubmit" onClick="submitAnswer();" style="margin-top:25px; display:none;">-->
                        <?php } else { $buttonText = "submit"; $title = "";?>

                        <?php } ?>
                        <div id="reviewQuestion" class="button hidden" onClick="reviewQuestion()" >Review Test</div><!--data-i18n="questionPage.reviewQuestion"-->
                        <div id="completeTest" class="button hidden" onClick="submitTest()" >Submit Test</div>
                        <div id="mcqText" data-i18n="questionPage.mcqText"></div>
                        <div id="submitQuestion" class="button hidden" onClick="submitAnswer()" data-i18n="questionPage.nextQuestion"></div>
                        <div id="nextQuestion" class="button hidden" onClick="handleClose();" data-i18n="questionPage.nextQuestion"></div>
                    </div>
                </div> <!-- end scroll div -->
                </div>
                <div class="clear forLowerOnly"></div>
                <div id="submit_bar1" class="forLowerOnly">
                    <div id="reviewQuestion" class="button hidden" onClick="reviewQuestion()" >Review Question</div><!--data-i18n="questionPage.reviewQuestion"-->
                    <div id="completeTest" class="button hidden" onClick="submitTest()" >Submit Test</div>
                    <div id="mcqText" data-i18n="questionPage.mcqText"></div>
                    <div id="submitQuestion1" class="button" onClick="submitAnswer()" data-i18n="questionPage.nextQuestion"></div>
                    <div id="nextQuestion1" class="button" onClick="handleClose();" data-i18n="questionPage.nextQuestion"></div>
                </div>
            </div> <!-- end pnlQuestion div -->
            <div id="pnlLoading" name="pnlLoading">
                <div align="center" class="quesDetails"><br/><br/><br/><br/><p>Loading, please wait...<br/><img src="assets/loader.gif"></p></div>
            </div>
        </div>    <!-- end container div -->
        <?php //Added By Manish Dariyani For Practice Cluster  // Starts Here..   ?>
        <div id="questionTemplate" style="display:none;">
            <div class="singleQuestion">
                <table width="100%" border="0" cellspacing="1" cellpadding="3">
                    <tr>
                        <td width="40px">
                            <div></div>
                        </td>
                        <td width="3%" align="left" valign="top">
                            <div align="left">
                                <div id="q1" name="q1" class="subQuestion" align="center"></div>
                                <div class="eqEditorToggler" align="center"></div>
                            </div>
                        </td>

                        <td valign="top" align="left">
                            <div>
                                <div id="q2" name="q2" class="question"></div>
                            </div>
                            <div id="hint" name="hint"></div>
                        </td>
                    </tr>
                </table>
                <div id="q4" name="q4" style="width:100%;margin-left:100px;" align="left"></div>
            </div>
        </div>
    </div>
    <?php // Ends Here..    ?>
    <div id="glossaryDiv">
        <div id="glossaryContainer">
            <div id="glossaryClose" title="Close"></div>
            <div style="clear:both"></div>
            <div id="leftGlossary">
                <div id="glossaryTitle"></div>
                <div style="clear:both"></div>
                <div id="glossaryImage"></div>
            </div>
            <div id="rightGlossary">
                <div id="glossaryBody"></div>
            </div>
            <div style="clear:both"></div>
            <div id="relatedGlossary">
                <strong>Related Terms: </strong> <a href="javascript:void(0)">Angle</a>, <a href="javascript:void(0)">Right Angle</a>
            </div>
        </div>
    </div>
    
    <div id="tagMsgBox" style="position: fixed; right:90px; bottom:90px; background-color: #00FFFF;width: 230px;padding: 10px;color: black;border: #0000cc 2px dashed;display: none;">
    <table>
        <tr><td><span id="showTaggedQcode"></span><br><strong>Comment:</strong></td></tr>
        <tr><td><textarea rows="4" cols="25" id="tagComment" name="tagComment"></textarea><input type="hidden" name="tagQcode" id="tagQcode" value=""></td></tr>
        <tr><td align="center"><input type="submit" id="tagComentSave" name="tagCommentSave" value="Save"><input type="button" id="closeBox" name="closeBox" value="Close" onClick="showTagBox('tagMsgBox', 'none', '');"></td></tr>
    </table>
</div>

<?php include("footer.php");?>
<?php
function emotToolbarTaggingCount($userID)
{
	$taggedCount = 0;
	$sql = "SELECT COUNT(likeID) FROM adepts_emotToolbarTagging WHERE userID=".$userID." AND DATE(time)=CURDATE()";
	$result = mysql_query($sql);
	if($row = mysql_fetch_array($result))
		$taggedCount = $row[0];
	return $taggedCount;
}
?>