<?php
set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
//error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
error_reporting(E_ERROR);

@include("check1.php");
require_once 'constants.php';
if(!isset($_SESSION['userID']))
{
    echo "You are not authorised to access this page! (URL copy pasted in the browser!)";
    exit;
}
$sessionID	=	$_SESSION['sessionID'];
$userID=$_SESSION['userID'];
$userName = $_SESSION['username'];
$Name = explode(" ",$_SESSION['childName']);
$childClass  = $_SESSION['childClass'];
$higherGradeQues = "";
if(isset($_POST["higherGradeQues"]))
	$higherGradeQues = $_POST["higherGradeQues"];

//---------
if (isset($_POST['mode']) && $_POST['mode']=='choiceScreen'){
    $examReportArray    =   array();
    $pendingChallengeNo =   isset($_POST['challengeNo'])?$_POST['challengeNo']:'';
}
else {
    $examReportArray	=	checkPreviousChallenge($userID);
    $pendingChallengeNo	=	(count($examReportArray["pending"])==0)?'':count($examReportArray["pending"]);
}
//unset($examReportArray["PendingChallengeNo"]);
$arraySources	=	getSources($higherGradeQues);
$arrayTopics	=	getTopics($higherGradeQues);

$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
$Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
?>

<?php include("header.php"); ?>
<title>Competitive Exam</title>
<link rel="stylesheet" href="css/commonHigherClass.css" />
<link rel="stylesheet" href="css/competitiveExam/higherClass.css" />
<script>
var langType = '<?=$language;?>';
</script>
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type='text/javascript' src='libs/combined.js'></script>
<!--<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>-->
<script type="text/javascript" src="libs/question/nonnparse.js?ver=2"></script>
<script type="text/javascript" src="libs/question/nparse.js?ver=1"></script>
<?php if( $iPad ==true|| $Android ==true){?>
<link type="text/css" href="libs/question/keypad2/jquery.keypad.css" rel="stylesheet">
<link type="text/css" href="libs/question/keypad2/keypadCustomStyle.css" rel="stylesheet">
<script type="text/javascript" src="libs/question/keypad2/jquery.keypad.js"></script>
<script type="text/javascript" src="libs/question/keypad2/keypad2.js?ver=2"></script>
<?php }?>
<script type="text/javascript" src="libs/competitiveExam/competitiveExam.js"></script>
<script type="text/javascript" src="/mindspark/js/load.js"></script>
<!--<script src="libs/closeDetection.js"></script>-->

<script>
var click=0;var fromChoice=false;
function load() {
	var a= window.innerHeight - (180);
		$('#topicInfoContainer').css("height",a+"px");
		$('#menuBar').css("height",a+"px");
		$('#sideBar').css("height",a+"px");
		$('#main_bar').css("height",a+"px");
		if(androidVersionCheck==1){
			$('#topicInfoContainer').css("height","auto");
			$('#main_bar').css("height",$('#topicInfoContainer').css("height"));
			$('#menu_bar').css("height",$('#topicInfoContainer').css("height"));
			$('#sideBar').css("height",$('#topicInfoContainer').css("height"));
		}

    <?php if (isset($_POST['mode']) && $_POST['mode']=='choiceScreen' && isset($_POST['forTTcode']) && ((isset($_POST['sources']) && isset($_POST['topics']) && isset($_POST['qcodes'])) || isset($_POST['challengeNo']))) { 
            echo 'fromChoice=true;';
            echo '$("#forTTcode").val("'.$_POST['forTTcode'].'");';
            if (isset($_POST['challengeNo'])){
                echo 'startPending('.$_POST['challengeNo'].');';
            }
            else{
                echo 'var sources="'.$_POST['sources'].'".split("|~|");';
                echo 'var topics="'.$_POST['topics'].'".split(",");';
                echo '$("#qcodes").val("'.$_POST['qcodes'].'");';
                echo '$("#totQues").text("'.$_POST['totQues'].'");';
            ?>
                $(".sourceCls").each(function (index, element) { if (sources.indexOf($(this).val())>=0) $(this).attr('checked',true);});
                $(".topicChkCls").each(function (index, element) { if (topics.indexOf($(this).val())>=0) $(this).attr('checked',true);});
                $("#startChallengeBtn").click();
            <?php
            }
     }?>
}
function openMainBar(){
	
	if(click==0){
		if(window.innerWidth>1024){
			$("#main_bar").animate({'width':'245px'},600);
			$("#plus").animate({'margin-left':'227px'},600);
		}
		else{
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
else {
        document.onmouseup=clickNS;
        document.oncontextmenu=clickIE;
}
document.oncontextmenu=new Function("return false");


function checkForHigherGrades()
{
	setTryingToUnload();
	$("#higherGradeClsFrm").submit();
}
</script>

</head>

<body class="translation" onLoad="load()" onResize="load()">
<span class='math' style='display:none'>{1 \over 2}</span>
<div id="top_bar">
    <div class="logo"></div>
    <div id="studentInfoLowerClass" class="forLowerOnly">
        <div id="nameIcon"></div>
        <div id="infoBarLeft">
            <div id="nameDiv">
                <div id='cssmenu'>
                    <ul>
                        <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?=$Name[0]?>&nbsp;</span></a>
                            <!--<ul>
                                <li><a href='javascript:void(0)'><span data-i18n="homePage.myDetails"></span></a></li>
                                <li><a href='javascript:void(0)'><span data-i18n="homePage.changePassword"></span></a></li>
                            </ul>-->
                        </li>
                    </ul>
                </div>
            </div>
            <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?=$childClass.$childSection?></span></div>
        </div>
    </div>
</div>


<div id="container" align="center">
    <div id="info_bar" class="forHigherOnly">
        <div id="topic">
            <div id="home">
                <div id="homeIcon" onClick="getHome()"></div>
                <div id="homeText" class="hidden"><span onClick="getHome()" class="textUppercase" data-i18n="dashboardPage.home"></span> > <font color="#606062"> <span class="textUppercase" data-i18n="competitiveExamPage.examCorner"></span></font></div>
            </div>
            
            <a href="#" onclick="javascript:closeFromChoice();window.location.href='examCorner.php'" style="text-decoration:none;color:inherit"><div id="dashboard" class="forHighestOnly" >
                <div id="dashboardIcon"></div>
                <div id="dashboardText"><span class="textUppercase" data-i18n="competitiveExamPage.examCorner"></span></div>
            </div></a>
            <div class="arrow-right forHighestOnly"></div>
            <div id="competitiveExamText" onClick="javascript:setTryingToUnload();window.location.href='competitiveExam.php'" class="textUppercase" data-i18n="competitiveExamPage.competitiveExam"></div>
            <div class="arrowDetail"></div>
            <div id="faqDivHead" class="textUppercase">FAQs for Competitive Exam Question Challenges</div>
            <div id="pastReportDivHead">PAST REPORTS</div>
            <div id="challengeNoText"></div>
            <div id="minQuesValidation" <?php if($pendingChallengeNo!="") echo "style='display:none'";?>>
            	<div id="totQues">0</div>
                <div id="availQuesDivText">No of questions in the current selection</div>
                <div class="clear"></div>
                <div id="minQuesDivText">Selection should have alteast 10 questions</div>
            </div>
            <div id="studenScoreMain">
            	<div id="studentScore">0</div>
                <div id="maxScoreText">Your score out of 10</div>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
            <div id="sessionID"><span id="sessionIDText">Session ID</span><span id="sessionIDDigit">: <?=$_SESSION["sessionID"]?></span></div>
            <div id="startText"></div>
            <div class="clear"></div>
        </div>
    </div>
    
    <div id="competitiveContainerMain">
    	<div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
            	<div id="saveAnswerBtn">
                	<div id="saveAnswerBtnIcon"></div>
                	<div id="saveAnswerBtnText">Save Answers</div>
                </div>
            	<div id="faqDiv" class="tabMenu"><div class="arrowBottom" id="arrowBottomFaq"></div>FAQ</div>
				<div id="reportDiv" class="tabMenu"><div class="arrowBottom" id="arrowBottomReport"></div>Past Reports</div>
                <div class="arrowSubmit" <?php if($pendingChallengeNo!="") echo "style='display:none'";?>></div>
                <div class="btnSubmit textUppercase" <?php if($pendingChallengeNo!="") echo "style='display:none'";?> id="startChallengeBtn">Start Challenge</div>
                <div class="btnSubmit textUppercase" id="endChallengeBtn">End Challenge</div>
			</div>
		</div>
  		<div class="clear"></div>
        <div id="topicInfoContainer">
            <div id="msgPrompt"></div>
            <div id="msgAutoPrompt"></div>
            <div id="confirmPrompt">Are you sure you want to end this challenge?<div id="clickYes">Yes</div><div id="clickNo">No</div></div>
            <div id="startNewChallengText" <?php if(count($examReportArray['pending'])==0) echo "style='display:none'";?>>Click <span id="startNew">here</span> to start a new challenge.</div>
            <!-- <div id="pendingChallengePrompt">A challenge is pending.<br />Click yes to continue.<div id="pendingYes" onClick="startPending(<?php /*$pendingChallengeNo;*/?>)">Yes</div><div id="pendingNo">No</div></div> -->
            <div class="difVertDiv"></div>
            <div id="rightDiv">
                <div class="difHorizDiv"></div>
                <div id="mainContentDiv" class="contentBorder" align="center">
                    <div id="pendingChallengeMsg" <?php if(count($examReportArray['pending'])==0) echo "style='display:none;'"; else echo 'style="font-size:1em;"';?>>
                        <div style="font-size:1.5em;">Resume a pending challenge from the list below</div>
                        <table width="100%" border="0" cellspacing="1" cellpadding="2" id="pendingTable" <?php if(count($examReportArray['pending'])==0) echo "style=display:none";?>>
                            <tr>
                              <td class="tdHeader" width="12%">Name</td>
                              <td class="tdHeader" width="18%">Sources</td>
                              <td class="tdHeader">Topics</td>
                              <td class="tdHeader" width="12%">Start time</td>
                              <td class="tdHeader" width="9%">-</td>
                            </tr>
                        <?php foreach($examReportArray['pending'] as $challengeReport=>$challengeDetails) { 
                                $newBg="style='background:#FFFCCC'";
                            ?>
                            <tr>
                                <td nowrap="nowrap" class="tdData" <?=$newBg?>><a class="linkDisp" href="javascript:void(0)" onClick="startPending(<?=$challengeReport?>)">Challenge <?=$challengeReport?></a></td>
                                <td class="tdData" <?=$newBg;?>><?=$challengeDetails["sources"]?></td>
                                <td class="tdData showMoreLessTd" <?=$newBg;?>><?=$challengeDetails["topics"]?></td>
                                <td class="tdData" <?=$newBg;?>><?=$challengeDetails["startTime"]?></td>
                                <td class="tdData" <?=$newBg;?>><a class="linkDisp" href="javascript:void(0)" onClick="startPending(<?=$challengeReport?>)">Resume</a></td>
                            </tr>
                        <?php }?>
                        </table>
                    </div>
                    <div id="prevReportDiv">
                        <table width="100%" border="0" cellspacing="1" cellpadding="2" id="reportTable" <?php if(count($examReportArray['completed'])==0 && count($examReportArray['pending'])==0) echo "style=display:none";?>>
                            <tr>
                                <td class="tdHeader" width="12%">Name</td>
                                <td class="tdHeader" width="18%">Sources</td>
                                <td class="tdHeader">Topics</td>
                                <td class="tdHeader" width="12%">Start time</td>
                                <td class="tdHeader" width="12%">End time</td>
                                <td class="tdHeader" width="9%">Score<br />(out of 10)</td>
                            </tr>
                        <?php foreach($examReportArray['pending'] as $challengeReport=>$challengeDetails) { 
                                $newBg="style='background:#FFFCCC'";
                            ?>
                            <tr>
                                <td nowrap="nowrap" class="tdData" <?=$newBg?>><a class="linkDisp" href="javascript:void(0)" onClick="startPending(<?=$challengeReport?>)">Challenge <?=$challengeReport?></a></td>
                                <td class="tdData" <?=$newBg;?>><?=$challengeDetails["sources"]?></td>
                                <td class="tdData showMoreLessTd" <?=$newBg;?>><?=$challengeDetails["topics"]?></td>
                                <td class="tdData" <?=$newBg;?>><?=$challengeDetails["startTime"]?></td>
                                <td class="tdData" <?=$newBg;?>> </td>
                                <td class="tdData" <?=$newBg;?>><a class="linkDisp" href="javascript:void(0)" onClick="startPending(<?=$challengeReport?>)">Complete</a></td>
                            </tr>
                        <?php }?>
                        <?php foreach($examReportArray['completed'] as $challengeReport=>$challengeDetails) { ?>
                            <tr>
                                <td nowrap="nowrap" class="tdData"><a class="linkDisp" href="javascript:void(0)" onClick="getReport(<?=$challengeReport?>)">Challenge <?=$challengeReport?></a></td>
                                <td class="tdData" ><?=$challengeDetails["sources"]?></td>
                                <td class="tdData showMoreLessTd" ><?=$challengeDetails["topics"]?></td>
                                <td class="tdData" ><?=$challengeDetails["startTime"]?></td>
                                <td class="tdData" ><?=$challengeDetails["endTime"]?></td>
                                <td align="center" class="tdData" ><?=$challengeDetails["score"]?></td>
                            </tr>
                        <?php } ?>
                        </table>
                    </div>
               <?php /*Challeng start div*/ ?>
                    <div id="newChallengeSelectDiv" <?php if($pendingChallengeNo!="") echo "style='display:none'";?>>
                        <div id="newChallengeHead" align="left">Make your own 10 questions test! Select the sources and topics from below:</div><br />
                        <div id="sourceDiv" align="left">
						<form action="" method="post" name="higherGradeClsFrm" id="higherGradeClsFrm">
                            <p title="select source"><span class="subHead">Sources</span><br /><br />
								<label>
                                <input type="checkbox" id="higherGradeCls" name="higherGradeQues" onClick="checkForHigherGrades()" <?php if($higherGradeQues == "allowHigherGrade") echo "checked";?> value="allowHigherGrade" />
                                <span class="subHead">Give higher grade<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;questions</span></label><br><br>
							<?php
                            	foreach($arraySources as $source=>$sourceFull) { ?>
                                <label>
                                <input type="checkbox" class="sourceCls" name="sourcesGroup" value="<?=$sourceFull?>" id="source_<?=$source?>" />
                                <span class="sourcesTopics"><?=$source?></span></label>
                                <br /><?php } ?>
                            </p>
						</form>
                        </div>
                    
                        <div id="topicDiv" align="left">
                            <p><span class="subHead">Topics</span><br />
                            <label>
                                <input type="checkbox" class="defaultSelectCls" name="topicsGroup" value="" id="defaultSelect" />
                                <span class="subHead">Default topic selection</span></label><br /> <table width="90%" border="0" cellspacing="0" cellpadding="0"><tr><?php $i=1;
                            foreach($arrayTopics as $topic=>$clusterTopic) { ?>
                                <td>
                                <label>
                                <input type="checkbox" class="topicChkCls" name="topicsGroup" value="<?=$clusterTopic?>" id="topic_<?=$clusterTopic?>" disabled="disabled" />
                                <span class="sourcesTopics"><?=$topic?></span></label></td>
								<?php 
                                if($i % 1 == 0)
                                    echo "</tr><tr>";
                                $i++;} ?>
                                </table>
                            </p>
                        </div>
                        
                        <div class="clearDiv"></div>
                        <input type="hidden" id="qcodes" value="<?=$qcodeStr?>" />
                        <input type="hidden" id="forTTcode" value="" />
                        <input type="hidden" id="userID" value="<?=$userID?>" />
                        <input type="hidden" id="ansPrevStr" value="<?=$ansPrevStr?>" />
                        <input type="hidden" id="scorePrevStr" value="" />
                        <input type="hidden" id="challengeNoHid" value="" />
                        <input type="hidden" id="pendingChallenge" value="<?=$pendingChallengeNo?>" />
                        <!--<div class="btnSubmit" id="startChallengeBtn">Start Challenge</div>-->
                    </div>
                <?php /*Challeng start div---ends here*/
                    /*Display Questions Div*/
                 ?>
                    <div id="questionDiv">
                <?php for($q=1;$q<=10;$q++) { ?>
                        <div class="quesDisp" id="quesDisp_<?=$q?>">
                        	<div class="responseBar">
                            	<div class="quesSourceReport sourceQues_<?=$q?>"></div>
                                <!--<div id="responseImageText_<?=$q?>">
                            		<div class="responseImg" id="responseImg_<?=$q?>"></div>
                                    <div class="responseText textUppercase">Your response</div>
                                </div>-->
                            </div>
                            <div class="q1" align="center" name="q1"><?=$q?></div>
                            <div class="questionDispDiv">
                                <div class="quesTexts" id="quesText_<?=$q?>"></div>
                                <div class="options" id="option_<?=$q?>"></div>
                            </div>
                            <div class="clearDiv"></div>
                            <div class="quesSource sourceQues_<?=$q?>"></div>
                            <div class="clearDiv"></div>
                            <input type="hidden" id="qcode_<?=$q?>" value="" />
                            <input type="hidden" id="qtype_<?=$q?>" value="" />
                            <input type="hidden" id="correctAns_<?=$q?>" value="" />
                        </div>
                        <div class="desk_block">
                        	<div id="responseImageText_<?=$q?>">
                                <div class="responseImg" id="responseImg_<?=$q?>"></div>
                                <div class="responseText textUppercase">Your response</div>
                            </div>
                            <div class="block mid_repeat">
                                <div class="block_header">
                                    <div class="dispAnsText" id="dispAns_<?=$q?>"></div>
                                </div>
                            </div>
                            <div class="dlgAnswer_inner" id="dlgAnswer_inner_<?=$q?>" name="dlgAnswer_inner<?=$q?>"></div>
                            <div class="clear"></div>
                        </div>
                        <hr class="horizLine" />
                <?php } ?>
                        <br />
                    </div>
                    
                    <div id="faqDispDiv">
                              <div class="faqHead">1) What is a Competitive Exam Question Challenge?</div>
                                    <div class="faqSubHead">A Competitive Exam Questions Challenge is an untimed Challenge consisting of 10 questions selected from the various Competitive Exams. You can select the Competitive Exams and the Topics from which a Challenge should be made of.</div>
                                
                                <div class="faqHead">2) I see a lot of abbreviations as Sources of Questions. What are those?</div>
                                    <div class="faqSubHead">The abbreviations are the Competitive Exams. Full-forms as below-</div>
                                    <div class="faqSubSubHead">CBSE- Central Board of Secondary Education (class 10)</div>
                                    <div class="faqSubSubHead">ICSE - Indian Certificate of Secondary Education (class 10)</div>
                                    <div class="faqSubSubHead">IGCSE - International General Certificate of Secondary Education (class 10)</div>
                                    <div class="faqSubSubHead">IB - International Baccalaureate (class 12)</div>
                                    <div class="faqSubSubHead">NTSE - National Talent Search Examination (class 8)</div>
                                    <div class="faqSubSubHead">JEE - Joint Entrance Examination (class 12, entrance exam for IITs)</div>
                                    <div class="faqSubSubHead">HKDSE - Hong Kong Diploma of Secondary Education (class 10)</div>
                                    <div class="faqSubSubHead">FME - Finland Matriculation Examination (class 10)</div>
                                <div class="faqHead">3) I selected a few sources but I don't see any questions for that source?</div>
                                    <div class="faqSubHead">All the questions in the system so far, will appear in only 1 challenge for you. This is done to avoid repetition of questions. So, if you see 0 questions for a source, it means that you have finished all the questions from that source. </div>
                                
                                <div class="faqHead">4) The questions are out of syllabus. What should I do?</div>
                                    <div class="faqSubHead">Most of the competitive exams are taken after 10th or 12th class. So, it is possible that you have not covered all the topics yet, especially if you are a class 8 or 9 student.</div>
                                
                                <div class="faqHead">5) My topic progress is not increasing inspite of doing Competitive Exam Question Challenges. Why is that?</div>
                                    <div class="faqSubHead">The purpose of Competitive Exam Question Challenges are for you to see what are the kind of questions asked in various examinations, how do you fare on such questions and to learn from the explanations given for each question at the end of a challenge. </div>
                                <div class="faqSubHead">It is not compulsory. Also, the scores you get do not affect your performance in Mindspark in any way.</div>
                                
                                <div class="faqHead">6) Why should I attempt such Challenges?</div>
                                    <div class="faqSubHead">Refer to the answer of question 5 above.</div>
                                   <div class="endDiv">--------------</div>
                            </div>
                    </div>
                </div>
            </div>
            <div class="clearDiv"></div>
        </div>
    </div>
<?php include("footer.php");

function getSources($higherGrade)
{
	$arraySorces	=	array();
	$sq	=	"SELECT DISTINCT source FROM adepts_competitiveExamMaster WHERE status=3";
	if($higherGrade!="allowHigherGrade")
		$sq	.=	" AND level<=".$_SESSION["childClass"];
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$arraySorces[$rw[0]]	=	$rw[0];
	}
	ksort($arraySorces);
	return $arraySorces;
}

function getTopics($higherGrade)
{
	$topicArray	=	array();
	$sq	=	"SELECT DISTINCT topic,A.topicCode FROM adepts_topicMaster A, adepts_competitiveExamMaster B WHERE A.topicCode=B.topicCode AND B.status=3";
	if($higherGrade!="allowHigherGrade")
		$sq	.=	" AND level<=".$_SESSION["childClass"];
	$sq .= " ORDER BY topic";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$topicArray[$rw[0]]	=	$rw[1];
	}
	return $topicArray;
}

function checkPreviousChallenge($userID)
{
	$examReportArray	=	array('pending'=>array(),'completed'=>array(),);
	$pendingChallengeNo="";
	$sq	=	"SELECT challengeNo,sources,topics,forTTcode,DATE_FORMAT(startTime,'%D %M %Y<br>%h:%i %p'),DATE_FORMAT(endTime,'%D %M %Y<br>%h:%i %p'),score,timeTaken,status FROM adepts_competitiveExamStatus
			 WHERE userID=$userID ORDER BY challengeNo DESC";
	$rs	=	mysql_query($sq);
	if(mysql_num_rows($rs)!=0)
	{
		while($rw=mysql_fetch_array($rs))
		{
            $ceqArray = array();
            $ceqArray["sources"] =   str_replace(",",", ",$rw[1]);
            $ceqArray["topics"]  =   getTopicDesc($rw[2]);
            $ceqArray["fromTTcode"]  =   getTopicDesc($rw[3]);
            $ceqArray["startTime"]   =   $rw[4];
            $ceqArray["endTime"] =   $rw[5];
            $ceqArray["score"]   =   $rw[6];
            $ceqArray["timeTaken"]   =   $rw[7];
			if($rw[8]=='Pending')
				$examReportArray['pending'][$rw[0]]=$ceqArray;
            else 
                $examReportArray['completed'][$rw[0]]=$ceqArray;
		}
	}
	//$examReportArray["PendingChallengeNo"]	=	$challengeNo;
	return $examReportArray;
}

function getTopicDesc($topicCodeStr)
{
	$topicCodeStr	=	str_replace(",","','",$topicCodeStr);
	$topicStr	=	"";
	$i=0;
	$sq	=	"SELECT topic FROM adepts_topicMaster WHERE topicCode IN ('$topicCodeStr')";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$i++;
		if($i==3)
			$topicStr	.=	"<span class='showMoreLess' id='showMore'>...&nbsp;<img src='assets/higherClass/competitiveExam/more.png'></span><span class='hideTopic'>";
			//$topicStr	.=	"<span class='showMoreLess' id='showMore'>...Show More</span><span class='hideTopic'>";
		$topicStr	.=	", ".$rw[0];
	}
	if($i>2)
		$topicStr	.=	"</span><span class='showMoreLess' id='showLess'>&nbsp;<img src='assets/higherClass/competitiveExam/more.png'></span>";
		//$topicStr	.=	"</span><span class='showMoreLess' id='showLess'>Show Less</span>";
	$topicStr	=	substr($topicStr,2,strlen($topicStr));
	return $topicStr;
}

function changeDateFormat($dateTime)
{
	$dateTimeArray	=	explode(" ",$dateTime);
	$dateArray	=	explode("-",$dateTimeArray[0]);
	return $dateArray[2]."-".$dateArray[1]."-".$dateArray[0]." ".$dateTimeArray[1];
}
?>