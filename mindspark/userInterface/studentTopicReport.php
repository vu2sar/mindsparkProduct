<?php
include("check1.php");
error_reporting(E_ERROR);

include("constants.php");
include("classes/clsQuestion.php");
include("function/functionsForDynamicQues.php");
include("classes/clsUser.php");
include_once("functions/orig2htm.php");
include_once("classes/clsTopicProgress.php");
include_once("classes/clsProgressCalculation.php");

if(!isset($_SESSION['userID']))
{
	header("Location:logout.php");
	exit;
}
$userID = $_SESSION['userID'];
$Name = explode(" ", $_SESSION['childName']);
$Name = $Name[0];
$ttCode = $_REQUEST['ttCode'];

$user = new User($userID);

$childName	=	$user->childName;
$childClass	=	$user->childClass;
$childSection	=	$user->childSection;

if(!(strcasecmp($user->category,"STUDENT")==0 && (strcasecmp($user->subcategory,"SCHOOL")==0 || strcasecmp($user->subcategory,"Home Center")==0)) && $user->childClass<=3) {
	$query = "SELECT IF(c.newTTDesc='' OR ISNULL(c.newTTDesc),a.teacherTopicDesc,c.newTTDesc) as 'teacherTopicDesc'
				FROM   adepts_teacherTopicMaster a LEFT JOIN adepts_teacherTopicFlow_classwise c ON a.teacherTopicCode=c.teacherTopicCode
				WHERE a.teacherTopicCode='$ttCode'";
}
else 
	$query  = "SELECT teacherTopicDesc FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$ttCode'";
$result = mysql_query($query);
$line   = mysql_fetch_array($result);
$TTname = $line['teacherTopicDesc'];
$clustersCleared = getClustersCleared($userID, $ttCode);
$clustersFailed  = getClusterNeedingAttention($userID,$ttCode, $user->childClass, $childSection, $_SESSION['schoolCode'], $_SESSION['admin'], $_SESSION['subcategory']);
$mode = "";
if(isset($_POST['mode']))
	$mode = $_POST['mode'];
?>

<?php include("header.php"); ?>

<title>Student Topic Report</title>
<?php
	 if($theme==1) { ?>
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/studentTopicReport/lowerClass.css" rel="stylesheet" type="text/css">
<?php } else if($theme==2){ ?>
    <link rel="stylesheet" href="css/studentTopicReport/midClass.css" />
	<link rel="stylesheet" href="css/commonMidClass.css" />
<?php } else { ?>
	<link rel="stylesheet" href="css/commonHigherClass.css" />
    <link rel="stylesheet" href="css/studentTopicReport/higherClass.css?ver=1" />
<?php } ?>
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="libs/combined.js"></script>
<!--<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>-->
<script type="text/javascript" src="/mindspark/js/load.js"></script>
<!--<script type="text/javascript" src="libs/closeDetection.js"></script>-->

<script>
var langType = '<?=$language;?>';
function load(){
<?php if($theme==1) { ?>	
	var a= window.innerHeight - (57	+ 60 + 60);
	$('#topicInfoContainer').css("height",a+"px");
<?php } else if($theme==2){ ?>
	var a= window.innerHeight - (80 + 50 + 140 );
	$('#topicInfoContainer').css("height",a+"px");
<?php } else if($theme==3) { ?>
			var a= window.innerHeight - (170);
			var b= window.innerHeight - (610);
			$('#topicInfoContainerMain').css({"height":a+"px"});
			$('#topicInfoContainer').css({"height":a+"px"});
			$('#sideBar').css({"height":a+"px"});
			$('#main_bar').css({"height":a+"px"});
			$('#menubar').css({"height":a+"px"});
		<?php } ?>
		if(androidVersionCheck==1){
			$('#topicInfoContainer').css("height","auto");
			$('#topicInfoContainerMain').css("height","auto");
			$('#main_bar').css("height",$('#topicInfoContainer').css("height"));
			$('#menu_bar').css("height",$('#topicInfoContainer').css("height"));
			$('#sideBar').css("height",$('#topicInfoContainer').css("height"));
		}	
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
</script>
</head>
<body class="translation" onLoad="load()" onResize="load();">
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
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"> <?=$childClass.$childSection?></span></div>
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
        <!--<div id="info_bar" class="forLowerOnly">
        	<div id="blankWhiteSpace">
            	<div id="topicName"><?=$TTname?></div>
            </div>
             <div id="home">
                <div id="homeIcon"></div>
                <div id="dashboardHeading" class="forLowerOnly"> - QUESTION TRAIL </div>
                <div class="clear"></div>
            </div>
        </div>-->
        
        <div id="info_bar" class="forLowerOnly hidden">
        	<div id="blankWhiteSpace"><div id="topicName"><?=$TTname?></div></div>
             <div id="home" class="textUppercase">
                <div id="homeIcon" class="linkPointer" onClick="getHome()"></div>
                <div id="dashboardHeading" class="forLowerOnly"> - <a class="removeDecoration" href="dashboard.php" data-i18n="dashboardPage.dashboard"></a> - <font color="#606062"><span data-i18n="studentTopicReportPage.sessionWiseReport"></span></font></div>
                <div class="clear"></div>
            </div>
        </div>
        
        <div id="info_bar" class="forHigherOnly hidden">
			<div id="topic">
				<div id="home">
                  	<div id="homeIcon" class="linkPointer" onClick="getHome()"></div>
                    <div id="homeText" class="forHigherOnly"><span onClick="getHome()" class="textUppercase linkPointer" data-i18n="dashboardPage.home"></span> > <font color="#606062"><a class="removeDecoration textUppercase" href="dashboard.php" data-i18n="dashboardPage.dashboard"></a></font> > <font color="#606062"> <span data-i18n="studentTopicReportPage.sessionWiseReport"></span> > </font><font color="#606062">  <?=$TTname?></font></div>
                    <div class="clear"></div>
				</div>
                <div class="clear"></div>
			</div>
            
			<div class="class">
				<strong><span id="classText" data-i18n="common.class"></span> </strong> <?=$childClass.$childSection?>
			</div>
			<div class="Name">
				<strong><?=$Name?></strong>
			</div>
            
            <div class="clear"></div>
		</div>
        
		<div id="info_bar" class="forHighestOnly">
				<a href="dashboard.php" style="text-decoration:none;color:inherit"><div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase" data-i18n="dashboardPage.dashboard"></span></div>
                </div></a>
				<div id="sessionHeading"><?=$TTname?></div>
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
			<!--<div id="drawer6"><div id="drawer6Icon"></div>NOTIFICATIONS
			</div>-->
		</div>
		
        <div id="topicInfoContainerMain">
		<div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
			</div>
			</div>
        <div id="topicInfoContainer">
			<div id="topicBasicInfo">
            	<table width="90%" border="0" cellspacing="0" cellpadding="3">
                  <tr>
                    <td id="conceptClearText" data-i18n="studentTopicReportPage.conceptsCleared"></td>
                    <td id="conceptClearData">
						<?php
                            if(count($clustersCleared)==0)
                                echo "None <br/>(Note: You could have made some progress in this topic but yet to clear an entire learning unit.)";
                            else
                            {
                                for($i=0; $i<count($clustersCleared); $i++)
                                {
                                    echo $clustersCleared[$i]."<br>";
                                }
                            }
                        ?>
                    </td>
                  </tr>
                  <tr class="forLowerOnly hidden"><td colspan="2" id="midSpace">&nbsp;</td></tr>
                  <tr>
                    <td id="conceptAttentionText" data-i18n="studentTopicReportPage.ConceptsAttention"></td>
                    <td id="conceptAttentionData">
						<?php
                            if(count($clustersFailed)>0)
                            {
                                foreach ($clustersFailed as $clusterCode => $desc)
                                {
                                    echo "<br/><span style='margin-left:20px;'>&#8226; $desc</span><br/>";
                                    $qcodeArray = getQuestions($userID,$ttCode,$clusterCode,$user->childClass,$qcodeArray);
                                }
                            }
                            else
                                echo "None";
                        ?>
                    </td>
                  </tr>
                </table>
                </div>
            
            	<?php
            	for($i=0; $i<count($qcodeArray); $i++) { ?>
					<div id="quesTrailDiv"><?php
					showQuestion($qcodeArray[$i], ($i+1),$userID, $user->childClass); ?>
					</div><?php
				}
				?>
			</div>            
        </div>
        </div>
	</div>
    
<?php include("footer.php"); ?>

<?php
function getClustersCleared($userID, $ttCode)
{
	$clustersCleared = array();
	$query  = "SELECT clusterCode, max(clusterAttemptID) maxid
	           FROM   ".TBL_CLUSTER_STATUS." a, ".TBL_TOPIC_STATUS." b
	           WHERE  a.userID=b.userID AND b.userID=$userID AND a.ttAttemptID=b.ttAttemptID AND b.teacherTopicCode='$ttCode'
	           GROUP BY clusterCode";
	$result = mysql_query($query);
	while ($line = mysql_fetch_array($result))
	{
		$maxcluster  = $line['maxid'];
		$getclresultQuery = "SELECT result, cluster FROM ".TBL_CLUSTER_STATUS." a, adepts_clusterMaster b
		                     WHERE  clusterAttemptID = $maxcluster AND a.clusterCode=b.clusterCode";
		$gotclresult = mysql_query($getclresultQuery);
		$outclresult = mysql_fetch_array($gotclresult);
		$clresult    = $outclresult['result'];
		$cldesc      = $outclresult['cluster'];

		if (strcasecmp($clresult,'SUCCESS')==0)
		{
			array_push($clustersCleared,$cldesc);
		}
	}
	return $clustersCleared;
}

/*function getClusterNeedingAttention($userID, $ttCode, $class)
{
	$clusterArray  = array();
	//get max failed clusters
	$query = "SELECT a.clusterCode, level
              FROM   ".TBL_CLUSTER_STATUS." a, ".TBL_TOPIC_STATUS." b, adepts_teacherTopicClusterMaster c
              WHERE  a.result='FAILURE' AND a.userID=b.userID AND b.userID=$userID AND a.ttAttemptID=b.ttAttemptID AND
                     b.teacherTopicCode=c.teacherTopicCode AND a.clusterCode=c.clusterCode AND c.teacherTopicCode='$ttCode'
              GROUP BY a.clusterCode HAVING count(a.result) >= 2 ORDER BY c.flowno DESC";
	$gotmaxfail = mysql_query($query);

	while ($outmaxfail = mysql_fetch_array($gotmaxfail))
	{
		$maxfailcl = $outmaxfail['clusterCode'];
		$level     = explode(",",$outmaxfail['level']);
		$getlastfail = "SELECT max(clusterAttemptID) maxlastid
    	                FROM   ".TBL_CLUSTER_STATUS." a, ".TBL_TOPIC_STATUS." b
		                WHERE  clusterCode='$maxfailcl' and a.userID=b.userID AND b.userID=$userID AND
		                       a.ttAttemptID=b.ttAttemptID AND teacherTopicCode='$ttCode'";
		$gotlastfail = mysql_query($getlastfail);
		$outlastfail = mysql_fetch_array($gotlastfail);
		$maxfailclID = $outlastfail['maxlastid'];

		$getlastresult = "SELECT result, cluster FROM ".TBL_CLUSTER_STATUS." a, adepts_clusterMaster b
		                  WHERE clusterAttemptID = $maxfailclID AND a.clusterCode=b.clusterCode";
		$gotlastresult = mysql_query($getlastresult);
		$outlastresult = mysql_fetch_array($gotlastresult);
		$lastresult = $outlastresult['result'];
		$maxfaildesc = $outlastresult['cluster'];


		if(strcasecmp($lastresult,'FAILURE')==0 && $class>=$level[0])
		{
			$clusterArray[$maxfailcl] = $maxfaildesc;
			break;
		}
	}
	return $clusterArray;
}*/
function getClusterNeedingAttention($userID, $ttCode, $class, $section, $schoolCode, $category, $subcategory) {
	$weakClusters = array();
	if(strcasecmp($category, "STUDENT")!=0 || strcasecmp($subcategory, "SCHOOL")!=0) {
		$flow = 'MS';
	} else {
		if(is_null($section) || $section=='')
			$sectionCheck = '';
		else
			$sectionCheck = "and section='$section'";
		$query = "SELECT flow FROM adepts_teacherTopicActivation WHERE teacherTopicCode='$ttCode' AND schoolCode=$schoolCode AND class=$class $sectionCheck ORDER BY srno DESC LIMIT 1";
		$result = mysql_query($query);
		$data = mysql_fetch_assoc($result);
		$flow = $data['flow'];
		if(is_null($flow) || $flow=='')
			$flow='MS';
	}
	
	$clustersInFlowOrder = array();
	if(strcasecmp(substr($flow, 0, 6), 'Custom')==0) {
		$customCode = substr($flow,9);
		$query = "SELECT clusterCodes FROM adepts_customizedTopicDetails WHERE code=$customCode";
		$result = mysql_query($query);
		$data = mysql_fetch_assoc($result);
		$clustersInFlowOrder = array_reverse(explode(',', $data['clusterCodes']));
	} else {
		$board = strtolower($flow);
		$query = "SELECT a.clusterCode FROM adepts_teacherTopicClusterMaster a,adepts_clusterMaster b WHERE a.teacherTopicCode='$ttCode' AND a.clusterCode=b.clusterCode AND b.{$board}_level!='' AND LEFT(b.{$board}_level, LOCATE(',', CONCAT(b.{$board}_level, ','))-1)<='$class' ORDER BY a.flowno DESC";
		$result = mysql_query($query);
		while($row = mysql_fetch_assoc($result)) {
			$clustersInFlowOrder[] = $row['clusterCode'];
		}
	}
	$clustersInFlowOrderString = "'".implode("','", $clustersInFlowOrder)."'";
	
	$twiceFailedClusterCodes = array();
	$query = "SELECT b.clusterCode FROM ".TBL_TOPIC_STATUS." a,".TBL_CLUSTER_STATUS." b WHERE a.userID=$userID AND a.teacherTopicCode='$ttCode' AND a.ttAttemptID=b.ttAttemptID AND b.clusterCode in ($clustersInFlowOrderString) AND b.result='FAILURE' GROUP BY b.clusterCode HAVING count(b.result)>=2";
	$result = mysql_query($query);
	while($row = mysql_fetch_assoc($result)) {
		$twiceFailedClusterCodes[] = $row['clusterCode'];
	}
	foreach($clustersInFlowOrder as $clusterCode) {
		if(in_array($clusterCode, $twiceFailedClusterCodes)) {
			$query = "SELECT b.cluster,a.result FROM ".TBL_CLUSTER_STATUS." a,adepts_clusterMaster b WHERE a.userID=$userID AND a.clusterCode='$clusterCode' AND a.result IS NOT NULL AND a.clusterCode=b.clusterCode ORDER BY a.clusterAttemptID DESC LIMIT 1";
			$result = mysql_query($query);
			$data = mysql_fetch_assoc($result);
			if(strcasecmp($data['result'], 'FAILURE')==0) {
				$weakClusters[$clusterCode] = $data['cluster'];
				break;
			}
		}
	}
	return $weakClusters;
}

function getQuestions($userID, $ttCode, $clusterCode, $class)
{
	$qcodeArray = array();
	$getqcodes = "SELECT a.qcode FROM ".TBL_QUES_ATTEMPT."_class$class a, adepts_questions b
	              WHERE  a.qcode=b.qcode AND a.userID=$userID AND a.teacherTopicCode='$ttCode' AND
	                     a.clusterCode='$clusterCode' AND R=0
	              GROUP BY subdifficultylevel ORDER BY count(subdifficultylevel) DESC limit 3";
	$gotqcodes = mysql_query($getqcodes);

	while ($outqcodes = mysql_fetch_array($gotqcodes))
	{
		$qcode = $outqcodes['qcode'];
		array_push($qcodeArray,$qcode);
	}
	return $qcodeArray;
}

function showQuestion($qcode, $srno, $userID, $class)
{
	$question = new Question($qcode);
	if($question->isDynamic())
	{
		$question->generateQuestion();
	}
	$correct_answer = $question->getCorrectAnswerForDisplay();
	$getqdetails = "SELECT A FROM ".TBL_QUES_ATTEMPT."_class$class
	                WHERE  qcode=$qcode AND userID=$userID AND R=0
	                ORDER BY srno DESC limit 1";
	$gotqdetails = mysql_query($getqdetails);
	$outqdetails = mysql_fetch_array($gotqdetails);
	$useranswer = $outqdetails['A'];
	
	
?>

            <div id="quesTrailDiv">
            	<div id="quesNoDiv">
                	<div id="quesNo"><?=$srno?></div>
                </div>
                <div id="quesTextDiv">
                	<div id="quesText"><?=$question->getQuestion()?></div>
                    <div id="userAnsMain"></div>
                    <div class="clear"></div>
                    
<?php	if($question->quesType=='MCQ-4' || $question->quesType=='MCQ-3' || $question->quesType=='MCQ-2')	{	?>
                    <div id="optionDiv">
                    	<div class="optionAdiv floatLeft">
                        	<div class="optA floatLeft <?php if($correct_answer=="A") echo "optionCorrect"; else echo "option"?>" data-i18n="common.optA">A</div>
                            <div class="optAText floatLeft"><?=$question->getOptionA();?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="optionBdiv floatLeft">
                        	<div class="optB floatLeft <?php if($correct_answer=="B") echo "optionCorrect"; else echo "option"?>" data-i18n="common.optB">B</div>
                            <div class="optAText floatLeft"><?=$question->getOptionB();?></div>
                            <div class="clear"></div>
                        </div>
<?php	if($question->quesType=='MCQ-4') { ?>
                        <div class="clear"></div>
<?php }	if($question->quesType=='MCQ-4' || $question->quesType=='MCQ-3') { ?>
                        <div class="optionCdiv floatLeft">
                        	<div class="optC floatLeft <?php if($correct_answer=="C") echo "optionCorrect"; else echo "option"?>" data-i18n="common.optC">C</div>
                            <div class="optAText floatLeft"><?=$question->getOptionC();?></div>
                            <div class="clear"></div>
                        </div>
	<?php	if($question->quesType=='MCQ-4') { ?>                        
                        <div class="optionDdiv" class="floatLeft">
                        	<div class="optD floatLeft <?php if($correct_answer=="D") echo "optionCorrect"; else echo "option"?>" data-i18n="common.optD">D</div>
                            <div class="optDText floatLeft"><?=$question->getOptionD();?></div>
                            <div class="clear"></div>
                        </div>
	<?php } } ?>
                        <div class="clear"></div>
                    </div>
<?php } ?>
                    
                    <div id="answerDiv">
                    	<div id="userResponseText" data-i18n="topicWiseQuesTrailPage.userResponse">User Response</div>
                        <div id="userResponse"><?=$useranswer?></div>
                        <div id="correctAnsText" data-i18n="topicWiseQuesTrailPage.correctAns">Correct Answer</div>
                        <div id="correctAns"><?=$correct_answer?></div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
<?php } ?>            