<?php
error_reporting(0);
include("../check.php");
include("../slave_connectivity.php");
$_SESSION["userType"]="msAsStudent";
$keys=array_keys($_REQUEST);
foreach($keys as $key)
{
	${$key}=$_REQUEST[$key];
}
if($modified==1)
	$_SESSION["modified"]	=	1;
if($html5version==1)
	$_SESSION["html5version"]	=	1;
$language="en";
if(count($_POST)==0)
{
	echo "You are not authorised to access this page! (URL copy pasted in the browser!)";
    exit;
}

/*$teacherTopicName = $_SESSION['teacherTopicName'];
$sessionID = $_SESSION['sessionID'];
$Name = explode(" ", $_SESSION['childName']);
$Name = $Name[0];
$childClass = $_SESSION['childClass'];
$higherLevel = $_SESSION['higherLevel'];*/

$quesCategory = isset($_POST['quesCategory'])?$_POST['quesCategory']:"";
$tmpMode = isset($_POST['tmpMode'])?$_POST['tmpMode']:"";

$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
$Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
$IE		 = stripos($_SERVER['HTTP_USER_AGENT'],"MSIE");
$budddySchoolArr	=	array();

?>

<?php include("header_dev.php");?>
        <title>Questions</title>
		<meta content="IE=9" http-equiv="X-UA-Compatible">
        <link href="css/question/common.css?ver=2" rel="stylesheet" type="text/css">
        <link href="css/question/glossary.css" rel="stylesheet" type="text/css">
        <link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="libs/jquery.js"></script>
		<script type="text/javascript" src="libs/question/ms_ques.js?ver=2"></script>
		<script src="libs/jquery_ui.js"></script>
        <script type="text/javascript" src="/mindspark/js/load.js"></script>
    <?php 
        if ($theme==1) { ?>
            <link href="css/question/lowerClass.css" rel="stylesheet" type="text/css">
            <link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
            <script type="text/javascript" src="libs/question/scriptLowerClass_dev.js?ver=1"></script>
<?php } else if($theme==2) { ?>
			<link href="css/common.css" rel="stylesheet" type="text/css">
            <link href="css/question/middleClass.css" rel="stylesheet" type="text/css">
            <script type="text/javascript" src="libs/question/scriptMiddleClass_dev.js?ver=3"></script>
<?php } else if($theme==3) { ?>
			<link href="css/commonHigherClass.css" rel="stylesheet" type="text/css">
            <link href="css/question/higherClass.css" rel="stylesheet" type="text/css">
            <script type="text/javascript" src="libs/question/scriptHigherClass_dev.js?ver=4"></script>
<?php }?>
        <script type="text/javascript" src="libs/i18next.js"></script>
        <script type="text/javascript" src="libs/translation.js?ver=2"></script>
        <script type="text/javascript" src="libs/question/scriptCommon_dev.js?ver=925"></script>
        <script type="text/javascript" src="libs/question/glossary.js"></script>
        <!--<script type="text/javascript" src="libs/fracbox.js"></script>-->
        <script type="text/javascript" src="libs/maxlength.js"></script>
        <script type="text/javascript" src="libs/mathquill/mathquill.js"></script>
        <link href="libs/mathquill/mathquill.css" rel="stylesheet" type="text/css">
        <link href="css/tinykeypad.css" rel="stylesheet" type="text/css">

        <script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
		<script type="text/javascript" src="libs/niftyplayer.js"></script>
        <?php if( $iPad ==true|| $Android ==true) { ?>
        <link type="text/css" href="libs/question/keypad2/jquery.keypad.css"
            rel="stylesheet">
        <link type="text/css" href="libs/question/keypad2/keypadCustomStyle.css"
            rel="stylesheet">
        <script type="text/javascript"
            src="libs/question/keypad2/jquery.keypad.js"></script>
        <script type="text/javascript"
            src="libs/question/keypad2/keypad2.js?ver=5"></script>
        <?php }?>
        
        <script>
			
            var langType = '<?=$language?>';
		<?php 
		/*<!--$emoteToolbarTagCount = emotToolbarTaggingCount($_SESSION['userID']);
		echo "var emoteToolbarTagCount = ".$emoteToolbarTagCount.";\n";-->*/
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
		
	   function doActionOnSubmitAnswer(qcode)   // for mantis task 13522
       {
            window.opener.showDivsAfterQuesAttempt(qcode);
        /*if(fromQuesApprovalPage == 0)
            handleClose();
        else
            window.location = "../quesApproval.php?clusterCode="+clusterCode+"&viewMode="+viewMode+"&fromQuesDevPage=1&qcodeForShowApproval="+qcode;*/
       }

	
		</script>
    </head>
    <body onResize="adjustScreenElements()" class="translation" >
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
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?=$Name?>&nbsp;</span></a>
                                <ul>
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
            <div id="logout" class="linkPointer hidden" onClick="logoff();" style="visibility:hidden;">
                <div class="logout"></div>
                <div class="logoutText" data-i18n="common.logout"></div>
            </div>

            <div id="whatsNew" style="visibility:hidden;">
                <div class="whatsNew"></div>
                <div class="whatsNewText" data-i18n="common.whatsNew"></div>
            </div>

            <div id="sparkieContainer"  class="hidden">

                <div id="noOfSparkie">
                </div>
                <div class="sparkie">
                </div>
            </div>
        </div> <!-- End top_bar -->

        <div id="container">
            <div class="bubble forHigherOnly hidden">
                <div class="speech_sparkie"><span id="sparkieInfo"></span>
                </div>
            </div>
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
                    <div id="dashboardText"><span class="textUppercase" data-i18n="homePage.recent"></span></div>
                </div>
				<div class="arrow-1"></div>
                <div id="lowerClassProgress"></div>
                
                <div id="linkBar" class="forLowerOnly hidden">
					<div id="endSessionDiv" class="lowerClassIcon linkPointer" onClick="endsession();">
                        <div class="endSessionDiv"></div>
                        <div class="quesLinkText" data-i18n="questionPage.endSession"></div>
                    </div>
                    <div id="endTopicDiv" class="lowerClassIcon linkPointer" onClick="endtopic();">
                        <div class="endTopicDiv"></div>
                        <div class="quesLinkText" data-i18n="questionPage.changeTopic"></div>
                    </div>
                	<?php if($higherLevel==0) { ?>
                    <div id="quitHigherLevel" class="lowerClassIcon linkPointer" onClick="quitHigherLevel();">
                        <div class="quitHigherLevel"></div>
                        <div class="quesLinkText" data-i18n="questionPage.quitHighLevel"></div>
                    </div>
                    <?php } ?>
                </div>
                <div id="questionType" style="display:none;" class="forHighestOnly">
                        <div id="questionImage">
                        </div>
                        <div id="QT"></div>
                    </div>
                <div id="topic">
                    <div id="questionType" style="display:none;">
                        <div id="questionImage">
                        </div>
                        <div id="QT"></div>
                    </div>

                    <div id="topic_name">
						<?php
						if($quesCategory=="topicRevision")
                            echo "Topic-wise Practice: ";
						?>
                        <div id="letsPractice" class="extraPCtext" style="display:none;">Let's Practice !</div>
                    </div>
					
                    
                    <div id="progress_bar">
                        <div id="correct_bar">
                            <div id="cText" class="forHigherOnly"><strong data-i18n="questionPage.correctAnswer"></strong></div>
                            <div class="text" data-i18n="questionPage.correct"></div>
                            <div class="circle" id="spnQuesCorrect"></div>
                            <div class="text1" data-i18n="questionPage.outOf"></div>
                            <div class="circle" id="spnQuestionsDone"></div>
							<div class="arrow-a"></div>
                        </div>
                        <div id="green" class="hidden">
                        </div>
                        <div id="progress_text" class="align hidden"><strong data-i18n="questionPage.progress"></strong>
                        </div>
                        <div id="percent" class="align hidden"><span  id="spnProgress"></span>
                        </div>
                    </div>
                    <div id="session">
                        <span data-i18n="questionPage.session" class="labelText"></span><span class="sessionColor" ><?php echo $sessionID; ?></span>
                    </div>
                    <div id="question_number">
                        <span data-i18n="questionPage.questionText"  class="labelText"></span><span class="sessionColor" id="lblQuestionNo"></span>
                    </div>
                </div>
				<div id="showHide" class="forHigherOnly hidden">Hide</div>
                <?php if($quesCategory == "NCERT" && ($theme==1 || $theme==2)) { ?>
                <div id="topic_ncert" class="hidden" onClick="hideBar();">
                    <div id="home">
                        <div class="icon_text11">HOME > <font color="#606062"> NCERT EXERCISE > <?=$_POST['exerciseName']?></font></div>
                    </div>
                    <div id="commentError" data-i18n="questionPage.commentError" style="visibility:hidden"></div>
                </div>
                <?php  } ?>
				<?php if($quesCategory == "NCERT" && $childClass>6) { ?>
                 	<div class="icon_text11"><font color="#606062"> NCERT EXERCISE > <?=$_POST['exerciseName']?></font></div>
                <?php  } ?>
                <div id="student" class="hidden forHigherOnly">
                </div>
                <div class="class hidden forHigherOnly">
                    <strong data-i18n="common.class"></strong>: <?php echo $childClass; ?>
                </div>
                <div class="Name hidden forHigherOnly">
<?php echo $Name ?>
                </div>
                <?php if($higherLevel) { ?>
                <div id="quitHigherLevel" class="changeTopic_blue forHigherOnly" onClick="quitHigherLevel();">
                    <div class="icon_text_higher" data-i18n="questionPage.quitHighLevel"></div>
                    <div id="pointed">
                    </div>
                </div>
                <?php } ?>
                
                <div id="endSession" class="endSession_blue hidden" onClick="endsession();">
                    <div class="icon_text" data-i18n="questionPage.endSession"></div>
                    <div id="pointed">
                    </div>
                </div>
				<div class="pieContainer forHighestOnly" >
				     <div class="pieBackground"></div>
				     <div id="pieSlice1" class="hold"><div class="pie"></div></div>
					 <div id="pieSlice2"><div class="pie1"></div></div>
				</div>
				<div id="percent" class="align forHighestOnly"><span  id="spnProgress1"></span>
                </div>
            </div>
            <div id="hideShowBar" class="forHigherOnly hidden" onClick="hideBar();">&ndash;</div>
            <div id="pnlQuestion">
                <div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
				<div id="questionTrail" onClick="endsession();">
					<span id="questionTrailText" data-i18n="questionPage.endSession"></span>
					<div id="questionTrailIcon" class="circle11"><div class="arrow-s"></div></div>
				</div>
				<div class="empty">
				</div>
				<?php if($higherLevel) { ?>
				<div id="practice">
					<span id="practiceText">Quit Higher level</span>
					<div id="practiceIcon" class="circle11"><div class="arrow-s"></div></div>
				</div>
				<?php } else{ ?>
					<div id="dummy"></div>
				<?php }?>
				<div id="drawer5"><div id="drawer5Icon"><div class="redCircle"></div></div>
				</div>
				<?php if($quesCategory == "NCERT" && $dueDate < date('Y-m-d')) { $buttonText = "lateSubmit"; $title = "lateSubmitTitle"; ?>
                        <?php } else if($quesCategory == "NCERT" && $dueDate >= date('Y-m-d')){ $buttonText = "submit"; $title = "ncertQuesSubmitTitle";?>
                        <?php } else { $buttonText = "submit"; $title = "";?>
                        <?php } ?>
				<div id="submitQuestion2" class="button" onClick="submitAnswer()" data-i18n="common.<?=$buttonText?>"></div>
                <div id="nextQuestion2" class="button" onClick="window.close();" data-i18n="questionPage.nextQuestion"></div>
				<div id="submitArrow" class="arrow-right"></div>
			</div>
			</div>
                <?php if($quesCategory != "NCERT" && $quesCategory != "topicRevision") { ?>
                <div id="emotToolBar" class="forLowerOnly hidden">
				<div style="clear:both"></div>
                    <div id="radioButtons" class="emotSingleDiv">
                        <div id="close" class="emotImage openToolbar" onClick="toolbar();"></div>
                        <div id="close" class="emotImage closeToolbar" onClick="toolbar();" style="display:none"></div>
                        <div id="open" class="emotIcons" style="display:none" onClick="toolbar();">
                            <label for="like" class="emotImage like" title="Like">
                                <input type="radio" name="emotRespond" value="Like" id="like" class="radio" /></label>
                            <label for="dislike" class="emotImage dislike" title="Dislike">
                                <input type="radio" name="emotRespond" value="Dislike" id="dislike" class="radio" /></label>
                            <label for="excited" class="emotImage excited" title="Excited">
                                <input type="radio" name="emotRespond" value="Excited" id="excited" class="radio" /></label>
                            <label for="bored" class="emotImage bored" title="Bored">
                                <input type="radio" name="emotRespond" value="Bored" id="bored" class="radio" /></label>
                            <label for="zapped" class="emotImage zapped" title="Confused">
                                <input type="radio" name="emotRespond" value="Confused" id="zapped" class="radio" /></label>
                            <label id="cc" for="comment_new" class="emotImage comment_new" title="Comment" onClick="comment(); toolbar();">
                                <input type="radio" name="emotRespond" value="Comment" id="comment_new" class="radio" /></label>
                        </div>
                    </div>
                </div>
                <div id="toolContainer" class="forHigherOnly">
                    <div id="rate" onClick="toolbar();">
                        <div class="toolbarText" data-i18n="questionPage.rate"></div>
                    </div>
                    <div id="elements">
                        <div id="whiteContainer">
                        </div>
                        <label for="zapped" class="emotImage" title="Confused">
                            <input type="radio" name="emotRespond" value="Confused" id="zapped" class="radio" style="display:none" />
							<div class="fixSize">
                            <div id="confused" onClick="toolbar1(id);">
                                <div class="toolbarText1" data-i18n="questionPage.confused"></div>
                            </div></div>
                        </label>
                        <label for="bored" class="emotImage" title="Bored">
                            <input type="radio" name="emotRespond" value="Bored" class="radio" style="display:none" />
							<div class="fixSize">
                            <div id="bored" onClick="toolbar1(id);">
                                <div class="toolbarText2" data-i18n="questionPage.bored"></div>
                            </div></div>
                        </label>
						<label for="excited" class="emotImage" title="Excited">
                            <input type="radio" name="emotRespond" value="Excited" class="radio" style="display:none" />
                            <div class="fixSize"><div id="excited" onClick="toolbar1(id);">
                                <div class="toolbarText3" data-i18n="questionPage.excited"></div>
                            </div></div>
                        </label>
                        <label for="like" class="emotImage" title="Like">
                            <input type="radio" name="emotRespond" value="Like" class="radio" style="display:none" />
                            <div class="fixSize"><div id="like" onClick="toolbar1(id);" >
                                <div class="toolbarText5" data-i18n="questionPage.like"></div>
                            </div></div>
                        </label>
                        <label for="dislike" class="emotImage" title="Dislike">
                            <input type="radio" name="emotRespond" value="Dislike" class="radio" style="display:none" />
                            <div class="fixSize"><div id="dislike" onClick="toolbar1(id);">
                                <div class="toolbarText4" data-i18n="questionPage.dislike"></div>
                            </div></div>
                        </label>
						<div class="fixSize">
                        <div id="comment" onClick="comment();">
                            <div class="toolbarText6" data-i18n="questionPage.comment"></div>
                        </div></div>
                    </div>
                </div>
                <?php } ?>
                <div id="mainBG">
                <div id="scroll">
                <?php if($quesCategory == "NCERT") { ?>
                    <div align="center" id="exerciseNav">
                    <?php
                        $totalGroups = isset($_POST["totalGroups"]) ? $_POST["totalGroups"] : 0;
                        $totalGroups = explode(",", $totalGroups);
                        $completedGroups = isset($_POST["completedGroups"]) ? $_POST["completedGroups"] : 0;
                        $completedGroups = explode(",", $completedGroups);
                        $dueDate = isset($_POST["dueDate"]) ? $_POST["dueDate"] : 0;
                        foreach ($totalGroups as $i) {
                            $completedClass = " pending";
                            if (in_array($i, $completedGroups))
                                $completedClass = " complete";
                    ?>
                        <span class="groupNav<?=$completedClass?>" id="groupNav<?=$i?>"><?=$i?></span>
                    <?php
                        }
                    ?>
                    <div id="chart" style="clear:both;">
                        <div id="boxRed"><div class="PS" data-i18n="questionPage.pending"></div></div>
                        <div id="boxGreen"><div class="PS" data-i18n="questionPage.submitted"></div></div>
                    </div>
                <?php } ?>
                <?php if($quesCategory != "NCERT") { ?>
                    <div id="commentBox">
                        <div id="commentText"  data-i18n="questionPage.commentText"></div>
                        <div id="questionSelect">My comment is on
                            <input type="radio" id="commentOn1" name="commentOn" class="commentOn" value="1" checked>this question</input>
                            <input type="radio" id="commentOn2" name="commentOn" class="commentOn" value="2">on the previous question</input>
                            <input type="radio" id="commentOn3" name="commentOn" class="commentOn" value="3">not related to the questions</input>
                        </div>
                        <div id="category"> Category :
                            <select id="selCategory" name="selCategory" onclick="openTextBox();">
                                <option value=''>Select</option>
                                <option value='Doubt about the question'>Doubt about the question</option>
                                <option value='Difficulty of questions'>Difficulty of questions</option>
                                <option value='On topic progress'>On topic progress</option>
                                <option value='About images'>About images</option>
                                <option value='Doubt about my answer'>Doubt about my answer</option>
                                <option value='Other'>Other</option>
                            </select>
                        </div>
                        <?php //--for comments ?>
                        <div id="commentInfo" title="Comment on previous question" style="display: none;"></div>
                        <div id="commentBoxTr">
                            <textarea id="txtcomment" name="comment" wrap="virtual"></textarea>
                            <div class="button1" data-i18n="common.submit" onClick="mailComment();" ></div><br/>
                            <div class="button1" onClick="hideCommentBox();" data-i18n="common.cancel"></div>
                        </div>
                    </div>
                <?php } ?>
                    <div id="question">
						<form name="quesform" id="quesform" method="post" autocomplete=off onSubmit="return false;">
                            <input type="hidden" name="qcode" id="qcode" value="<?=$qcode;?>">
                            <input type="hidden" name="theme" id="theme" value="<?=$_SESSION['theme'] = $theme;?>">
                            <input type="hidden" name="pageloadtime" id="pageloadtime">
                            <input type="hidden" name="mode" id="mode" value="">
                            <input type="hidden" name="quesType" id="quesType" value="">
                            <input type="hidden" name="secsTaken" id="secsTaken" value="">
                            <input type="hidden" name="result" id="result" value="">
                            <input type="hidden" name="qno" id="qno" value="<?=$qno?>">
                            <input type="hidden" name="clusterCode" id="clusterCode" value="">
                            <input type="hidden" name="refresh" id="refresh" value="0">
                            <input type="hidden" name="endTime" id="endTime">
                            <input type="hidden" name="nextQuesLoaded" id="nextQuesLoaded" value="0">
                            <input type="hidden" name="quesCategory" id="quesCategory" value="<?= $quesCategory ?>">
                            <input type="hidden" name="showAnswer" id="showAnswer" value="">
                            <input type="hidden" name="tmpMode" id="tmpMode" value="<?=$tmpMode?>">
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
                            <input type="hidden" name="userAllAnswers" id="userAllAnswers" value="" />
                            <input type="hidden" name="timeTakenHints" id="timeTakenHints" value="0" />
                            <input type="hidden" name="signature" id="signature" value="" />
							<input type="hidden" name="validToken" id="validToken" value="" />
                            <input type="hidden" name="commentSrNo" id="commentSrNo" value="<?=$commentSrNo;?>" />
							
                            <div id="questionText">
                                <span data-i18n="questionPage.question"></span><br><span id="q1"></span>
                            </div>
                            <br />
                            <div class="circle1" id='lblQuestionNoCircle'>
                            </div>
							<div id="voiceover" name="voiceover" style="margin-left:-30px;"></div>
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
                                    <div id="usefullHint"><input type="checkbox" name="isHintUsefull" id="isHintUsefull" value="0" />I found this hint useful.</div>
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
                            <div class="clear"></div>
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
									
									<div id="checkBoxContainer" style="height: 20px;">
									
									<label for="markedCheck" class="markedCheck" style="display:none;">
                                        <input name="markedCheck" id="markedCheck" type="checkbox" value="markedCheck" class="checkbox" />
                                        <span>I have a problem with this question.</span><br />
                                    </label>
									<label for="markedCheck1" class="markedCheck1" style="display:none;">
                                        <input name="markedCheck1" id="markedCheck1" type="checkbox" value="markedCheck1" class="checkbox" />
                                        <span>I have a problem with this question.</span><br />
                                    </label>
									<br>
									<label for="markedRepeat" class="markedRepeat"  style="display:none;margin-left: 20px;">
                                        <input name="markedRepeat" id="markedRepeat" type="checkbox" value="markedRepeat" class="checkbox" />
                                        <span>This is a repeat question.</span><br />
                                    </label>
									
									<div id="markedRepeatTextTR" style="display:none;">
									<br>
                                        <textarea name="markedRepeatText" id="markedRepeatText" style=""  wrap="virtual" onKeyPress="return noenter(event)" onFocus="if (this.value == placeHolderRepeatText) {
                this.value = '';
            }" onBlur="if (this.value == '') {
                this.value = placeHolderRepeatText;
            }"></textarea><br/>
                                        <div id="status2" style="width:600px;font-weight:bold;text-align:right;font-size:x-small;"></div>
										<br>
                                    </div>
									
                                    <label for="markedWrong" class="markedWrong" style="display:none;margin-left: 20px;">
                                        <input name="markedWrong" id="markedWrong" type="checkbox" value="markedWrong" class="checkbox" />
                                        <span data-i18n="questionPage.markedWrongText"></span><br />
                                    </label>
									
									
									 <div id="markedWrongTextTR" style="display:none;">
									 <br>
                                        <textarea name="markedWrongText" id="markedWrongText" style=""  wrap="virtual" onKeyPress="return noenter(event)" onFocus="if (this.value == placeHolderText) {
                this.value = '';
            }" onBlur="if (this.value == '') {
                this.value = placeHolderText;
            }"></textarea><br/>
                                        <div id="status1" style="width:600px;font-weight:bold;text-align:right;font-size:x-small;"></div>	
										
                                    </div>
									
									
									<br>
									</div>
									<div id="arrow" onClick="top1()" class="forHighestOnly"></div>
                                </div>
                            </div>
							<div id="submit_bar1" class="forLowerOnly">
			                    <div id="mcqText" data-i18n="questionPage.mcqText"></div>
			                    <div id="submitQuestion1" class="button" onClick="submitAnswer()" data-i18n="common.submit"></div>
			                    <div id="nextQuestion1" class="button" onClick="window.close();" data-i18n="questionPage.nextQuestion"></div>
			                </div>
                        </form>
                    </div> <!-- end question div -->
                    <div id="submit_bar" class="top_bar_part4 forHigherOnly hidden">
                        <div id="arrow2" onClick="top1()" data-i18n="questionPage.question"></div>
                        <?php if($quesCategory == "NCERT" && $dueDate < date('Y-m-d')) { $buttonText = "lateSubmit"; $title = "lateSubmitTitle"; ?>
                        <?php } else if($quesCategory == "NCERT" && $dueDate >= date('Y-m-d')){ $buttonText = "submit"; $title = "ncertQuesSubmitTitle";?>
                        <?php } else { $buttonText = "submit"; $title = "";?>

                        <?php } ?>
                        <div id="mcqText" data-i18n="questionPage.mcqText"></div>
                        <div id="submitQuestion" class="button hidden" onClick="submitAnswer()" data-i18n="common.<?=$buttonText?>"></div>
                        <div id="nextQuestion" class="button hidden" onClick="window.close();" data-i18n="questionPage.nextQuestion"></div>
                    </div>
                </div> <!-- end scroll div -->
                </div>
            </div> <!-- end pnlQuestion div -->
            <div id="pnlLoading" name="pnlLoading">
                <div align="center" class="quesDetails"><br/><br/><br/><br/><p>Loading, please wait...<br/><img src="assets/loader.gif"></p></div>
            </div>
        </div>    <!-- end container div -->
        <?php // For Practice Cluster  // Starts Here..   ?>
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
        <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="0px" height="0px" id="niftyPlayer1" align="" style="position:absolute;">
            <param name=movie value="assets/niftyplayer.swf">
            <param name=quality value=high>
            <param name=bgcolor value=#FFFFFF>
            <embed src="assets/niftyplayer.swf" quality='high' bgcolor='#FFFFFF' width="0px" height="0px" name="niftyPlayer1" align="" type="application/x-shockwave-flash" swLiveConnect="true" pluginspage="http://www.macromedia.com/go/getflashplayer">
            </embed>
        </object>
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
<?php if($_SESSION["userType"]=="msAsStudent") { ?>
    <div id="msAsStudentInfo" style="position:absolute"></div>
<?php } ?>    
    <div id="tagMsgBox" style="position: fixed; right:90px; bottom:90px; background-color: #00FFFF;width: 230px;padding: 10px;color: black;border: #0000cc 2px dashed;display: none;">
    <table>
        <tr><td><span id="showTaggedQcode"></span><br><strong>Comment:</strong></td></tr>
        <tr><td><textarea rows="4" cols="25" id="tagComment" name="tagComment"></textarea><input type="hidden" name="tagQcode" id="tagQcode" value=""></td></tr>
        <tr><td align="center"><input type="submit" id="tagComentSave" name="tagCommentSave" value="Save"><input type="button" id="closeBox" name="closeBox" value="Close" onClick="showTagBox('tagMsgBox', 'none', '');"></td></tr>
    </table>
    
</div>
<script type="text/javascript" src="libs/question/commonErrors.js"></script>
<script type="text/javascript" src="libs/question/nparse.js"></script>
<script type="text/javascript" src = "libs/parse.js"></script>
<script type="text/javascript" src = "libs/question/nonnparse.js"></script>
<?php include("footer_dev.php");?>
<?php
function emotToolbarTaggingCount($userID)
{
	$taggedCount = 0;
	$sql = "SELECT COUNT(likeID) FROM adepts_emotToolbarTagging WHERE userID=".$userID." AND DATE(lastModified)=CURDATE()";
	$result = mysql_query($sql);
	if($row = mysql_fetch_array($result))
		$taggedCount = $row[0];
	return $taggedCount;
}
?>