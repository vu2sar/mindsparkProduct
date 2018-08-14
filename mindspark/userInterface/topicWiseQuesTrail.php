<?php
set_time_limit(0);
include("check1.php");
include("constants.php");
include("functions/orig2htm.php");
include("functions/functionsForDynamicQues.php");
include("classes/clsQuestion.php");
include("classes/clsNCERTQuestion.php");
include("classes/clsWorksheetQuestion.php");
include("classes/eipaging.cls.php");
include_once("classes/clsTeacherTopic.php");
include("classes/clsUser.php");
include ("functions/functions.php");

error_reporting(E_ERROR);
if(!isset($_SESSION['userID']))
{
	header("Location:logout.php");
	exit;
}
$userID		=	$_SESSION['userID'];
$Name = explode(" ", $_SESSION['childName']);
$Name = $Name[0];
$objUser	=	new User($userID);
$schoolCode	=	$objUser->schoolCode;
$childClass	=	$objUser->childClass;
$childSection 	=	$objUser->childSection;


$topic = $_REQUEST['ttCode'];
$topicDesc	=	$_REQUEST['topicDesc'];

$accessFromStudentInterface = isset($_POST['accessFromStudentInterface'])?$_POST['accessFromStudentInterface']:0;

$clspaging = new clspaging();
$clspaging->setgetvars();
$clspaging->setpostvars();
$trailType = $_REQUEST['trailType'];
$pageName = $_REQUEST['pageName'];
if($pageName == 'topicPage')
{
    $higherLevel = $_REQUEST['higherLevel'];
    $isDeactive = $_REQUEST['isDeactive'];
}
if($topic!="")
{
    $query  = "SELECT ttAttemptID FROM ".TBL_TOPIC_STATUS." WHERE teacherTopicCode='$topic' AND userID=$userID";
    //echo $query;
    $result = mysql_query($query);
    if(mysql_num_rows($result)==0)
    {
        $noOfRecords = 0;
    }
    else {
        $topicAttemptID = "";
        while ($line=mysql_fetch_array($result))
            $topicAttemptID .= $line['ttAttemptID'].",";
        $topicAttemptID = substr($topicAttemptID,0,-1);
        $query  = "SELECT clusterAttemptID FROM ".TBL_CLUSTER_STATUS." WHERE ttAttemptID in ($topicAttemptID) AND userID=$userID";
        //echo $query;
        $result = mysql_query($query);
        if(mysql_num_rows($result)==0)
        {
            $noOfRecords = 0;
        }
        else {
            $clusterAttemptID = "";
            while ($line=mysql_fetch_array($result))
                $clusterAttemptID .= $line['clusterAttemptID'].",";
            $clusterAttemptID = substr($clusterAttemptID,0,-1);

            $questrail_query="SELECT  a.qcode, a.R, a.S, a.A, a.sessionID, teacherTopicDesc, cm.clusterCode, cluster, a.srno, questionNo, tm.teacherTopicCode, a.clusterAttemptID, date_format(a.lastModified, '%d-%m-%Y %H:%i:%s') lastModified
                    FROM    ".TBL_QUES_ATTEMPT."_class$childClass a,
                            adepts_teacherTopicMaster tm,
                            adepts_clusterMaster cm
                    WHERE   a.teacherTopicCode = tm.teacherTopicCode AND
                            a.clusterCode = cm.clusterCode AND
                            clusterAttemptID in ($clusterAttemptID) ORDER BY srno";
			//echo $query;
            $count_query = "SELECT count(srno) as noofques, sum(R) as correct FROM ".TBL_QUES_ATTEMPT."_class$childClass WHERE clusterAttemptID in ($clusterAttemptID)";
            $result = mysql_query($count_query) or die(mysql_error()."Invalid search criteria");
            $line   = mysql_fetch_array($result);
            $noOfRecords = $line['noofques'];
        }
    }
} 
else if(isset($_REQUEST['trailType']) && $_REQUEST['trailType']=='ncert') {
    $ncertExerciseCode = $_REQUEST['exercise'];
    $ncertDescQuery = "SELECT description FROM adepts_ncertExerciseMaster WHERE exerciseCode='$ncertExerciseCode'";
    $ncertDescResult = mysql_query($ncertDescQuery);
    $ncertDescRow = mysql_fetch_array($ncertDescResult);
    $ncertDescription = $ncertDescRow['description'];
    $questrail_query="SELECT a.qcode, a.R, a.S, a.A, a.sessionID, nem.description, a.srno, a.questionNo, nem.exerciseCode, a.ncertAttemptID, date_format(a.lastModified, '%d-%m-%Y %H:%i:%s') lastModified FROM adepts_ncertQuesAttempt a, adepts_ncertExerciseMaster nem WHERE a.userID=$userID AND a.exerciseCode='$ncertExerciseCode' AND a.exerciseCode=nem.exerciseCode ORDER BY srno";
    $count_query = "SELECT count(srno) as noofques, sum(if(R=1,1,0)) as correct FROM adepts_ncertQuesAttempt WHERE userID=$userID AND exerciseCode='$ncertExerciseCode'";
    $result = mysql_query($count_query) or die(mysql_error()."Invalid search criteria");
    $line   = mysql_fetch_array($result);
    $noOfRecords = $line['noofques'];
    $perCorrectQuery = "SELECT perCorrect FROM adepts_ncertHomeworkStatus WHERE userID=$userID AND exerciseCode='$ncertExerciseCode'";
    $perCorrectResult = mysql_query($perCorrectQuery);
    $perCorrectRow = mysql_fetch_array($perCorrectResult);
    $line['correct'] = $perCorrectRow['perCorrect']*$noOfRecords/100;
}
 else if(isset($_REQUEST['trailType']) && $_REQUEST['trailType']=='worksheet') {
    $worksheetID = $_REQUEST['worksheetID'];
    /*header("Location:worksheetSelection.php");*/
    $worksheetDescQuery = "SELECT wm.wsm_id,wm.wsm_name, COUNT(wd.wsd_id), ws.srno,ws.status, ws.accuracy, ws.feedback, SUM(IF(wa.RW=1,1,0)), IF(wm.end_datetime<NOW(),1,0)
                        FROM (worksheet_master wm LEFT JOIN worksheet_detail wd ON wm.wsm_id=wd.wsm_id) LEFT JOIN worksheet_attempt_status ws ON ws.wsm_id=wm.wsm_id AND ws.userID=$userID LEFT JOIN worksheet_attempt_detail wa ON wa.ws_srno=ws.srno and wd.wsd_id=wa.wsd_id
                        WHERE wm.wsm_id='$worksheetID' GROUP BY wm.wsm_id";
    $worksheetDescResult = mysql_query($worksheetDescQuery);
    $worksheetDescRow = mysql_fetch_array($worksheetDescResult);
    $worksheetDescription = stripcslashes($worksheetDescRow[1]);
    $topicDesc= $worksheetDescription;
    $worksheetTeacherFeedback= $worksheetDescRow[6];
    $noOfRecords = $worksheetDescRow[2];
    $numCorrect = $worksheetDescRow[7];    

    $questrail_query="SELECT wm.wsm_id,wm.wsm_name, wd.wsd_id,wd.qcode,wd.source,wd.qno,ws.`status`, ws.accuracy, ws.feedback, wa.id, wa.RW, wa.answer, wa.sessionID,  date_format(wa.lastModified, '%d-%m-%Y %H:%i:%s') lastModified
                    FROM (worksheet_master wm LEFT JOIN worksheet_detail wd ON wm.wsm_id=wd.wsm_id) LEFT JOIN worksheet_attempt_status ws ON ws.wsm_id=wm.wsm_id AND ws.userID=$userID LEFT JOIN worksheet_attempt_detail wa ON wa.ws_srno=ws.srno and wd.wsd_id=wa.wsd_id
                    WHERE wm.wsm_id='$worksheetID'  
                    ORDER BY wd.qno";
    $line['correct'] = $numCorrect;
}
//echo $clusterAttemptID;
?>

<?php include("header.php"); ?>

<title>Topic Wise Question Trail</title>
<?php
	if($theme==1) { ?>
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/topicWiseQuesTrail/lowerClass.css" rel="stylesheet" type="text/css">
<?php } else if($theme==2) { ?>
    <link rel="stylesheet" href="css/topicWiseQuesTrail/midClass.css?1" />
	<link rel="stylesheet" href="css/commonMidClass.css" />
<?php } else { ?>
	<link rel="stylesheet" href="css/commonHigherClass.css" />
    <link rel="stylesheet" href="css/topicWiseQuesTrail/higherClass.css?ver=3" />
<?php } ?>

<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="libs/combined.js?ver=1"></script>
<!--<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>-->
<script type="text/javascript" src="/mindspark/js/load.js"></script>
<!--<script type="text/javascript" src="libs/closeDetection.js"></script>-->
<script>
    var langType = '<?=$language;?>';
    function load(){
    	init();
        <?php if($theme==1) { ?>
    	var a= window.innerHeight - (50 + 60 + 140);
    	$('#topicInfoContainer').css("height",a+"px");
        <?php } else if($theme==2){ ?>
    	var a= window.innerHeight - (80 + 17 + 140 );
    	$('#topicInfoContainer').css("height",a+"px");
        <?php } else if($theme==3) { ?>
        	var a= window.innerHeight - (170);
        	var b= window.innerHeight - (610);
        	$('#topicInfoContainer').css({"height":a+"px"});
        	$('#sideBar').css({"height":a+"px"});
        	$('#main_bar').css({"height":a+"px"});
        	$('#menubar').css({"height":a+"px"});
        <?php } ?>
        if(androidVersionCheck==1){
        	$('#topicInfoContainer').css("height","auto");
        	$('#main_bar').css("height",$('#topicInfoContainer').css("height"));
        	$('#menu_bar').css("height",$('#topicInfoContainer').css("height"));
        	$('#sideBar').css("height",$('#topicInfoContainer').css("height"));
        }
        $('#topicInfoContainer').find('input,select').attr('disabled','disabled');
    }
    function init()
    {

    }
    function logoff()
    {
    	setTryingToUnload();
    	window.location="logout.php";
    }
    function navigatepage(varprefix, cp)
    {
    	document.getElementById(varprefix+'_currentpage').value = cp;
    	setTryingToUnload();
    	document.getElementById('frmSelect').submit();
    }
    function getHome()
    {
    	setTryingToUnload();
    	window.location.href	=	"home.php";
    }
    function goToTopicPage(ttCode,isDeactive,higherLevel)
    {           
        setTryingToUnload();            
        document.getElementById('ttCode').value=ttCode;         
        document.getElementById('isDeactive').value=isDeactive;
        document.getElementById('higherLevel').value = higherLevel;
        document.getElementById('frmTeacherTopicSelection').action='topicPage.php';         
        document.getElementById('frmTeacherTopicSelection').submit();        
    }
    function loadConstrFrame(cfr)
    {
            var cfrw=cfr.contentWindow;
            // cfrw.drawcode.setDrawnShapes(cfr.getAttribute("data-response"));
            cfrw.postMessage(JSON.stringify({
                subject: 'trail',
                content: {
                    type: 'display',
                    trail: cfr.getAttribute("data-response"),
                },
            }), getWindowOrigin(cfr.src));
    }
    function startInteractive(frame)
    {   
        try {
            var win = frame.contentWindow;
            frames.push(frame);windows.push(win);
            //win.postMessage('setUserResponse='+$(frame).attr('data-response'), '*');
        }
        catch (ex) {
            //alert('error in getting the response from interactive');
        }
    }
    function getWindowOrigin(url) {
        var dummy = document.createElement('a');
        dummy.href = url;
        return dummy.protocol+'//'+dummy.hostname+(dummy.port ? ':'+dummy.port : '');
    }
    var frames=[],windows=[];
    var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
    var eventer = window[eventMethod];
    var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";
    // Listen to message from child window
    eventer(messageEvent, function (e) {
        var response1 = ""; 
        response1 = e.data;
        //e.source.postMessage('setUserResponse='+$(frame).attr('data-response'), '*');
        if ($.inArray(e.source,windows)>-1){
            var frame=frames[$.inArray(e.source,windows)];
            if(response1.indexOf("loaded=1") == 0) {
                e.source.postMessage('setUserResponse='+$(frame).attr('data-response'), '*');
            }
            else if(response1.indexOf("frameHeight=") == 0) {
              frameHeight=response1.replace('frameHeight=','');$(frame).attr('height',frameHeight);
            }
        }
    }, false);
</script>
</head>
<body class="translation" onLoad="load()" onResize="load();">
	<div id="top_bar">
		<div class="logo">
		</div>

        <div id="studentInfoLowerClass" class="forLowerOnly">
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
                <div id="classDiv"><span id="classText" data-i18n="common.class">Class</span> <span id="userClass"><?=$childClass.$childSection?></span></div>
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
                                    <li><a href='changePassword.php'><span data-i18n="homePage.changePassword"></span></a></li>
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
        <div id="logout" class="linkPointer hidden" onClick="logoff();">
        	<div class="logout"></div>
        	<div class="logoutText" data-i18n="common.logout"></div>
        </div>
		<div id="whatsNew" style="visibility:hidden">
            <div class="whatsNew"></div>
            <div class="whatsNewText" data-i18n="common.whatsNew"></div>
        </div>
    </div>
    <div class="clear"></div>
<?php
    if($noOfRecords==0)
    {
        $msg	=	"<div class='notFound'>No question found!</div>";
    	$perCorrect	=	0;
    	//exit();
    }
    else {
    	$totalQuestions = $noOfRecords;
    	$perCorrect = round($line['correct']/$totalQuestions*100,1);
    	$clspaging->numofrecs = $noOfRecords;
    	if($totalQuestions>180)
    		$clspaging->numofrecsperpage	=	40;

    	if($clspaging->numofrecs>0)
    	{
    		$clspaging->getcurrpagevardb();
    	}
    	$srno=($clspaging->currentpage-1)*$clspaging->numofrecsperpage+1;
    	$questrail_query .= "  ".$clspaging->limit;
    	$result = mysql_query($questrail_query) or die("<br>Error in query - ".mysql_error());
    	$tmp_sessionNo = isset($_POST['lastSessionID'])?$_POST['lastSessionID']:"";
    	$tmp_topic     = isset($_POST['lastTopic'])?$_POST['lastTopic']:"";
    	$tmp_cluster   = isset($_POST['lastCluster'])?$_POST['lastCluster']:"";
    	$lowerLevel    = isset($_POST['lowerLevel'])?$_POST['lowerLevel']:false;
    	$rowno = 1;
    	$oldGroupID = "";
    	$boolGroupText = false;
    }
?>
    

	<div id="container">
        <div id="info_bar" class="forLowerOnly hidden">
        	<div id="blankWhiteSpace">

            </div>
             <div id="home">              
                <div id="homeIcon" class="linkPointer" onClick="getHome()"></div>
                <div id="dashboardHeading" class="forLowerOnly"> - <span class="textUppercase linkPointer" <?=(($trailType!='ncert')?(($trailType=='worksheet')?'data-i18n="worksheetSelectionPage.title"':'data-i18n="dashboardPage.dashboard"'):'')?> onClick="javascript:setTryingToUnload();window.location.href='<?=(($trailType!='ncert')?(($trailType!='worksheet')?'dashboard.php':'worksheetSelection.php'):'homeworkSelection.php')?>'"><?=(($trailType!='ncert')?'':'NCERT HOMEWORK')?></span>                                 
                - <span data-i18n="topicWiseQuesTrailPage.quesTrail" class="textUppercase"></span> : <span title="<?=$topicDesc?>"><?php if(strlen($topicDesc)>26) echo substr($topicDesc,0,27)."..."; else echo $topicDesc;	?></span></div>
                <div class="clear"></div>
            </div>
        </div>

		<div id="info_bar" class="forHigherOnly hidden">
			<div id="topic" <?php if ($trailType=='worksheet') echo 'style="height:70px;"';?>>
            	<div id="home">
                    <div id="homeIcon" onClick="getHome()"></div>
                    <div id="homeText"><span onClick="getHome()" class="textUppercase linkPointer" data-i18n="dashboardPage.home"></span> > <font color="#606062"><span class="textUppercase linkPointer" <?=(($trailType!='ncert')?(($trailType=='worksheet')?'data-i18n="worksheetSelectionPage.title"':'data-i18n="dashboardPage.dashboard"'):'')?> onClick="javascript:setTryingToUnload();window.location.href='<?=(($trailType!='ncert')?(($trailType!='worksheet')?'dashboard.php':'worksheetSelection.php'):'homeworkSelection.php')?>'"><?=(($trailType!='ncert')?'':'NCERT HOMEWORK')?></span>
                        <?php                
                        if($pageName == 'topicPage') {?>
                        > <span data-i18n="dashboardPage.topicPage" class="textUppercase linkPointer" onClick="goToTopicPage('<?php echo $topic; ?>',<?php echo $isDeactive; ?>,<?php echo $higherLevel; ?>)"></span>
                        <?php } ?> 
                     > <?php if ($trailType!='worksheet') { ?><span class="textUppercase" data-i18n="topicWiseQuesTrailPage.quesTrail"></span> :<?php } ?></font><font color="#606062"> <?=$topicDesc?></font></div>
                </div>
				<div class="clear"></div>
                <div>
                <form name="frmTeacherTopicSelection" id="frmTeacherTopicSelection" method="POST">
                <input type="hidden" name='ttCode' id="ttCode">     
                <input type="hidden" name='isDeactive' id="isDeactive">
                <input type="hidden" name='higherLevel' id="higherLevel"> 
                </form>               
                </div>
			</div>
			<div class="class">
				<strong>Class </strong>: <?=$childClass.$childSection?>
			</div>
			<div class="Name">
				<strong><?=$Name?></strong>
			</div>
            <div class="clear"></div>
            <div id="totalQuesDone">
                <span data-i18n="topicWiseQuesTrailPage.totalQuesDone"></span> : <font color="#606062"><?=$noOfRecords?></font>
            </div>
            <div id="perCorrect">
                <span data-i18n="topicWiseQuesTrailPage.perCorrect"></span> : <font color="#606062"><?=$perCorrect?>%</font>
            </div>
            <?php 
            /*if ($trailType=='worksheet'){ ?>
                <div class="clear"></div>
                <div class="teacherFeedback" >Teacher feedback : <?=$worksheetTeacherFeedback ?></div>
            <?php }*/
            ?>
            <?php if($clspaging->numofpages > 1) { ?>
            <div id="pagingDiv">
            	<div id="pagingCircleLeft">></div>
                <?php //$clspaging->writeHTMLpagesrange($_SERVER['PHP_SELF'],true,"http://www.mindspark.in/mindspark/");	?>
                <?php for($p=$clspaging->numofpages;$p>=1;$p--) { ?>
                <div class="pagingNos">
                	<?php if($clspaging__currentpage!=$p) { ?><a href="javascript:navigatepage('clspaging_',<?=$p?>);"><?=$p?></a><?php } else  echo $p;?>
                </div>
                <?php } ?>
                <div id="pagingCircleRight"><</div>
            </div>
            <?php } ?>
            <div class="clear"></div>
		</div>
		
		<div id="info_bar" class="forHighestOnly">
				<a href="<?=(($trailType!='ncert')?(($trailType!='worksheet')?'dashboard.php':'worksheetSelection.php'):'homeworkSelection.php')?>" style="text-decoration:none;color:inherit"><div id="dashboard" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase" <?=(($trailType!='ncert')?(($trailType=='worksheet')?'data-i18n="worksheetSelectionPage.title"':'data-i18n="dashboardPage.dashboard"'):'')?>><?=(($trailType!='ncert')?'':'NCERT HOMEWORK')?></span></div>
                </div></a>
				<div class="arrow-top"></div>
				<div id="questionTrailText" class="forHighestOnly">
					<div id="homeText"><?php if ($trailType!='worksheet') { ?><span class="textUppercase" data-i18n="topicWiseQuesTrailPage.quesTrail"></span> > <?php } ?><font color="#000000"> <?=$topicDesc?></font></div>
				</div></br></br>
				<div id="totalQuesDone"><span data-i18n="topicWiseQuesTrailPage.totalQuesDone">Total Questions </span> : <font style="font-weight:bold;" ><?=$noOfRecords?></font></div>
				<div id="perCorrect"><span data-i18n="topicWiseQuesTrailPage.perCorrect">Total Questions Done </span> : <font style="font-weight:bold;" ><?=$perCorrect?>%</font></div>                
				<div id="pagingDiv" class="forHighestOnly">
	            	<div id="pagingCircleLeft"><div class="arrow-right"></div></div>
	                <?php //$clspaging->writeHTMLpagesrange($_SERVER['PHP_SELF'],true,"http://www.mindspark.in/mindspark/");	?>
	                <?php for($p=$clspaging->numofpages;$p>=1;$p--) { ?>
	                <div class="pagingNos">
	                	<?php if($clspaging__currentpage!=$p) { ?><a href="javascript:navigatepage('clspaging_',<?=$p?>);"><?=$p?></a><?php } else  echo $p;?>
	                </div>
	                <?php } ?>
	                <div id="pagingCircleRight"><div class="arrow-left"></div></div>
	            </div>
				<div class="clear"></div>
		</div>
		
        <div id="topicInfoContainerMain">
            <form id="frmSelect" method="post">
            <input type="hidden" value="<?php echo $pageName; ?>" name="pageName" id="pageName" />
            <?php if($pageName == 'topicPage')
            {?>
            <input type="hidden" name='isDeactive' id="isDeactive" value="<?php echo $isDeactive; ?>">
            <input type="hidden" name='higherLevel' id="higherLevel" value="<?php echo $higherLevel; ?>"> 
            <?php } ?>
            <input type="hidden" name="childName" value="<?=$childName?>">
            <input type="hidden" name="ttCode" value="<?=$topic?>">
            <input type="hidden" name="topicDesc" value="<?=$topicDesc?>">
            <input type="hidden"  id="student_userID" name="student_userID" value="<?=$student_userID?>">
            <input type="hidden" name="clspaging__currentpage" id="clspaging__currentpage">
            <input type="hidden" name="accessFromStudentInterface" value="<?=$accessFromStudentInterface?>">
            <input type="hidden" name="trailType" id="trailType" value="<?=$trailType?>">
            <input type="hidden" name="exercise" id="exercise" value="<?=$exercise?>">
            <input type="hidden" name="worksheetID" id="worksheetID" value="<?=$worksheetID?>">
            <div id="topicInfoDiv" class="forLowerOnly hidden">
            	<?php //if($noOfRecords==0) echo $msg; ?>
            	<div id="divDetailUpper" <?php if($noOfRecords==0) echo "style='visibility:hidden'"?>>
                	<div id="totalQuesDone"><span data-i18n="topicWiseQuesTrailPage.totalQuesDone">Total Questions </span> - <?=$noOfRecords?></div>
                	<div id="perCorrect"><span data-i18n="topicWiseQuesTrailPage.perCorrect">Total Questions Done </span> - <?=$perCorrect?>%</div>
                    <div class="clear"></div>                    
                </div>

    	       <?php if($clspaging->numofpages > 1) { ?>
                <div id="pagingDiv">
                	<div id="recordText">No. of records / page <input type="text" name="clspaging__numofrecsperpage" id="numOfRecsPerPage" value="<?=$clspaging->numofrecsperpage?>" size="2"> (<?=$clspaging->numofrecs?> Total)</div>
    				<div id="pagingCircleLeft">&#9668;</div>
                    <?php for($p=1;$p<=$clspaging->numofpages;$p++) { ?>
                    	<div class="pagingNos">
    						<?php if($clspaging__currentpage!=$p) { ?><a href="javascript:navigatepage('clspaging_',<?=$p?>);"><?=$p?></a><?php } else  echo $p;?>
                        </div>
                    <?php }
    				?>
                    <div id="pagingCircleRight">&#9658;</div>
    				<div class="clear"></div>
                </div>
    	       <?php } ?>
                <div class="clear"></div>
            </div>
    		<div id="menuBar" class="forHighestOnly">
    			<div id="sideBar">
    			</div>
    		</div>
            </form>
            <div id="topicInfoContainer">
            <?php if($noOfRecords==0) echo "<br><br>".$msg; ?>
            <?php
            while ($line=mysql_fetch_array($result))
            {
            	$qcodeArray = array();
                //wm.wsm_id,wm.wsm_name, wd.qcode,wd.source,wd.qno,ws.`status`, ws.accuracy, ws.feedback, wa.id,wa.wsd_id, wa.RW, wa.answer, wa.sessionID,  date_format(wa.lastModified, '%d-%m-%Y %H:%i:%s') lastModified
                $qcode        = ($trailType=="worksheet")?$line['wsd_id']:$line['qcode'];
                $timeTaken    = ($trailType=="worksheet")?"":$line['S'];
                $response     = ($trailType=="worksheet")?$line['RW']:$line['R'];
            	$eeresponse   = isset($line['eeresponse'])?$line['eeresponse']:"";
                $user_ans     = ($trailType=="worksheet")?$line['answer']:$line['A'];
                $topic        = ($trailType=="worksheet")?stripcslashes($line['wsm_name']):$line['teacherTopicDesc'];
                $clusterCode  = ($trailType=="worksheet")?$line['wsm_id']:$line['clusterCode'];
                $clusterDesc  = ($trailType=="worksheet")?stripcslashes($line['wsm_name']):$line['cluster'];
                $ttCode       = ($trailType=="worksheet")?$line['wsm_id']:$line['teacherTopicCode'];
                $clusterAttemptID = isset($line['clusterAttemptID'])?$line['clusterAttemptID']:"";
                $quesAttemptSrno = ($trailType=="worksheet")?$line['id']:$line['srno'];
            	$teacherComments = ($trailType=="worksheet")?$line['feedback']:((isset($line['teacherComments']))?$line['teacherComments']:"");

            	$type = ($trailType == "ncert")?"Exercise":(($trailType == "worksheet")?"Worksheet":"Learning unit");
            	if($trailType=="ncert")
            	{
            		$clusterType = "practice";
            		$sqlNew = "SELECT groupID FROM adepts_ncertQuestions WHERE qcode=".$line['qcode'];
            		$resultNew = mysql_query($sqlNew);
            		$rowNew = mysql_fetch_assoc($resultNew);
            	}
            	else if($trailType=="worksheet")
                {
                    $clusterType = "worksheet";
                    /*
                    $sqlNew = "SELECT groupID FROM adepts_ncertQuestions WHERE qcode=".$line['qcode'];
                    $resultNew = mysql_query($sqlNew);
                    $rowNew = mysql_fetch_assoc($resultNew);*/
                }
                else
            	{
            		$sqlNew = "SELECT clusterType, groupID FROM adepts_clusterMaster a, adepts_questions b WHERE a.clusterCode=b.clusterCode AND qcode=".$line['qcode'];
            		$resultNew = mysql_query($sqlNew);
            		$rowNew = mysql_fetch_assoc($resultNew);
            		$clusterType = $rowNew["clusterType"];
            	}
            	if($clusterType == "practice" || $trailType=="ncert")
            	{
            		$questionTable = ($trailType == "ncert")?"adepts_ncertQuestions":"adepts_questions";
            		$oneQuestionGroup = false;
            		$oldGroupID = $groupID;
            		$groupID = $rowNew["groupID"];
            		$sqlNew = "SELECT groupText, groupColumn, groupNo, COUNT(DISTINCT(qcode)) as noOfQuestions FROM adepts_groupInstruction a, $questionTable b WHERE a.groupID='$groupID' AND a.groupID=b.groupID";
            		$resultNew = mysql_query($sqlNew);
            		$rowNew = mysql_fetch_assoc($resultNew);
            		$groupText = $rowNew['groupText'];
            		$groupColumn = $rowNew['groupColumn'];
            		$groupNo = $rowNew['groupNo'];
            		$noOfQuestions = $rowNew['noOfQuestions'];
            		if($noOfQuestions == 1 && $trailType == "ncert")
            			$oneQuestionGroup = true;
            	}
            	if($groupID != $oldGroupID)
            		$boolGroupText = true;

            	if($trailType == "ncert")
            		$question     = new ncertQuestion($qcode);
            	else if($trailType == "worksheet")
                    $question     = new WorksheetQuestion($qcode);
                else
            		$question     = new Question($qcode);

            	if($question->isDynamic())
            	{
            		$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE userID=$userID AND class=$childClass AND mode='normal' AND quesAttempt_srno=".$line['srno'];
            		$params_result = mysql_query($query);
            		$params_line   = mysql_fetch_array($params_result);
            		$question->generateQuestion("answer",$params_line[0]);
            	}
            	if($trailType!='ncert' && $trailType!='worksheet') {
            		$long_user_response = getLongUserResponse($quesAttemptSrno, $userID, $line['qcode'], $line['sessionID'], $user_ans);
            	}
            	if(strpos($question->questionStem,"ADA_eqs") !== false)
            		$user_ans = "";
            	$questionType = $question->quesType;
            	$correct_answer = $question->getCorrectAnswerForDisplay();

            	if($question->eeIcon == "1" && $clusterType!="practice")
            		$eeresponse = getEEresponse($quesAttemptSrno,$childClass,"normal",$trailType);
            	else if($question->eeIcon == "1" && $clusterType=="practice")
            		$eeresponse = getEEresponse($quesAttemptSrno,$childClass,"practice",$trailType);

            	$optiona_bgcolor="";
            	$optionb_bgcolor="";
            	$optionc_bgcolor="";
            	$optiond_bgcolor="";

            	if($user_ans=="A")
            		$optiona_bgcolor="#FFA500";
            	if($user_ans=="B")
            		$optionb_bgcolor="#FFA500";
            	if($user_ans=="C")
            		$optionc_bgcolor="#FFA500";
            	if($user_ans=="D")
            		$optiond_bgcolor="#FFA500";

            	if($correct_answer=="A")
            		$optiona_bgcolor="#00FF00";
            	if($correct_answer=="B")
            		$optionb_bgcolor="#00FF00";
            	if($correct_answer=="C")
            		$optionc_bgcolor="#00FF00";
            	if($correct_answer=="D")
            		$optiond_bgcolor="#00FF00";

            	if($rowno==1 && $trailType!="worksheet")
            	{
            		$timedTestArray = getTimedTestAttemptedInSession($userID,$line['sessionID']);
            		$timedTestNo = 0;
            		$noOfTimedTests = count($timedTestArray);

            		$gamesArray = getGamesAttemptedInSession($userID,$line['sessionID']);
            		$noOfGames = count($gamesArray);
            		$gameNo = 0;

            		$remedialItemAttemptArray = getRemedialItemAttempts($userID,$line['sessionID']);
            		$noOfRemedialItems = count($remedialItemAttemptArray);
            		$remedialItemAttemptNo = 0;

            		$prepostTestQuestionsArray = getPrePostTestQuestionsAttempted($userID,$line['sessionID']);
            		$noOfPrepostTestQuestions = count($prepostTestQuestionsArray);
            		$prepostTestQuestionNo = 0 ;

            		$challengeQuesArray = getChallengeQuesAttemptedInSession($userID,$line['sessionID']);
            		for($cqno=0; $cqno<count($challengeQuesArray)&& $challengeQuesArray[$cqno][4]<$line['questionNo']; $cqno++);
            	}
            	if($tmp_sessionNo!=$line['sessionID']  && $trailType!="worksheet") 
                {
            		$query = "SELECT date_format(startTime,'%d/%m/%Y %H:%i:%s') FROM ".TBL_SESSION_STATUS." WHERE sessionID=".$line['sessionID'];
            		$tmp_result = mysql_query($query);
            		$tmp_line = mysql_fetch_array($tmp_result);
            		?>
                    <div id="sessionDetails" class="forLowerOnly hidden">
                       <div id="session">Session ID : <?=$line['sessionID']?></div>
                        <div id="duration">Start Time : <?=$tmp_line[0]?></div>
                        <div class="clear"></div>
                        <div id="learningUnit">Learning Unit : <?=$clusterDesc?></div>
                    </div>
                    <?php
            		$tmp_sessionNo = $line['sessionID'];
            		$tmp_topic = $topic;

            		//Get the challenge ques attempted, if any, in the session
            		$challengeQuesArray = array();
            		if(SUBJECTNO==2)
            		{
            			$challengeQuesArray = getChallengeQuesAttemptedInSession($userID,$line['sessionID']);

            			$timedTestArray = getTimedTestAttemptedInSession($userID,$line['sessionID']);
            			$timedTestNo = 0;
            			$noOfTimedTests = count($timedTestArray);

            			$gamesArray = getGamesAttemptedInSession($userID,$line['sessionID']);
            			$noOfGames = count($gamesArray);
            			$gameNo = 0;

            			$remedialItemAttemptArray = getRemedialItemAttempts($userID,$line['sessionID']);
            			$noOfRemedialItems = count($remedialItemAttemptArray);
            			$remedialItemAttemptNo = 0;

            			$prepostTestQuestionsArray = getPrePostTestQuestionsAttempted($userID,$line['sessionID']);
            			$noOfPrepostTestQuestions = count($prepostTestQuestionsArray);
            			$prepostTestQuestionNo = 0 ;
            		}
            		if($rowno!=1)
            			$cqno = 0;
            	}

            	if($clusterType == "practice")
            	{
                    echo '<div class="groupQues">';
            		
            		if($boolGroupText)
            		{
            			$boolGroupText = false;
                        ?>
                        <table width="<?=$theme==3?80:100?>%" border="0" cellspacing="2" cellpadding="3" align="center" <?=$theme==3?'style="margin-left: 15%"':''?>>
                            <tr>
                            	<?php
            					if($trailType == "ncert")
            					{
            					?>
                            	<td align='center' valign='top'  width='5%'><div class="qno"><?=$groupNo?></div></td>
                                <?php
                                }
            					?>
                                <td valign="top" align="left"><p><span class="quesDetails"><?=orig_to_html($groupText,"images","Q")?></span><br></p></td>
                            </tr>
                        </table>
                        <?php
            		}
            	}
            	if($clusterType == "practice")
            		echo '<div class="singleQuestion column'.$groupColumn.'">';
            	if($trailType == "ncert")
            		showQuestion($qcode,$line["sessionID"],$question->subQuestionNo, $question->getQuestionForDisplay($eeresponse), $response, $questionType, $question->getOptionA(), $question->getOptionB(), $question->getOptionC(), $question->getOptionD(), $optiona_bgcolor, $optionb_bgcolor, $optionc_bgcolor, $optiond_bgcolor, $correct_answer, $user_ans, $timeTaken, $clusterDesc, $response, $lowerLevel, $accessFromStudentInterface,$line['sessionID'],$tmp_line[0],$clusterDesc, $question->eeIcon, $trailType, $oneQuestionGroup, $quesAttemptSrno, $teacherComments, $ncertDescription);
            	else
            	{
            		showQuestion($qcode,$line["sessionID"],$srno, (strpos($question->questionStem,"ADA_eqs")!==false?$question->getQuestionForDisplay($long_user_response, 2):$question->getQuestionForDisplay($eeresponse)), $response, $questionType, $question->getOptionA(), $question->getOptionB(), $question->getOptionC(), $question->getOptionD(), $optiona_bgcolor, $optionb_bgcolor, $optionc_bgcolor, $optiond_bgcolor, $correct_answer, $user_ans, $timeTaken, $clusterDesc, $response, $lowerLevel, $accessFromStudentInterface,$line['sessionID'],$tmp_line[0],$clusterDesc, $question->eeIcon, $trailType);
            	}
            	if($clusterType == "practice")
            		echo '</div>';
            	if($i % $groupColumn == 0 && $clusterType == "practice")
            		echo '<div style="clear:both;"></div>';
            	$i++;

            	if($clusterType == "practice")
            		echo '</div>';

                $time_q = mktime(substr($line['lastModified'],11,2),substr($line['lastModified'],14,2),substr($line['lastModified'],17,2),substr($line['lastModified'],3,2),substr($line['lastModified'],0,2),substr($line['lastModified'],6,4));

                ///--timed test,activities,prepost--added by chirag
                if($trailType!='ncert' && $trailType!='worksheet') {
                	if($noOfTimedTests>$timedTestNo || $noOfGames >$gameNo  || $noOfRemedialItems >$remedialItemAttemptNo || $noOfPrepostTestQuestions > $prepostTestQuestionNo)
                	{
                		$noOfIterations =0;
                		$html="";
                		while($noOfIterations<50)
                		{	//Just to keep an upper cap so that it doesnot go to an infinite loop
                			$noOfIterations++;
                			$tmArray = array();
                			if($noOfTimedTests>$timedTestNo)
                			{
                				$TT_time = mktime(substr($timedTestArray[$timedTestNo]["attemptedOn"],11,2),substr($timedTestArray[$timedTestNo]["attemptedOn"],14,2),substr($timedTestArray[$timedTestNo]["attemptedOn"],17,2),substr($timedTestArray[$timedTestNo]["attemptedOn"],3,2),substr($timedTestArray[$timedTestNo]["attemptedOn"],0,2),substr($timedTestArray[$timedTestNo]["attemptedOn"],6,4));
                				$tmArray["timedTest"] = $TT_time;
                			}
                			if($noOfGames>$gameNo)
                			{
                				$game_time = mktime(substr($gamesArray[$gameNo]["attemptedOn"],11,2),substr($gamesArray[$gameNo]["attemptedOn"],14,2),substr($gamesArray[$gameNo]["attemptedOn"],17,2),substr($gamesArray[$gameNo]["attemptedOn"],3,2),substr($gamesArray[$gameNo]["attemptedOn"],0,2),substr($gamesArray[$gameNo]["attemptedOn"],6,4));
                				$tmArray["game"] = $game_time;
                			}
                			if($noOfRemedialItems>$remedialItemAttemptNo)
                			{
                				$remedialItem_time = mktime(substr($remedialItemAttemptArray[$remedialItemAttemptNo]["attemptedOn"],11,2),substr($remedialItemAttemptArray[$remedialItemAttemptNo]["attemptedOn"],14,2),substr($remedialItemAttemptArray[$remedialItemAttemptNo]["attemptedOn"],17,2),substr($remedialItemAttemptArray[$remedialItemAttemptNo]["attemptedOn"],3,2),substr($remedialItemAttemptArray[$remedialItemAttemptNo]["attemptedOn"],0,2),substr($remedialItemAttemptArray[$remedialItemAttemptNo]["attemptedOn"],6,4));
                				$tmArray["remedial"] = $remedialItem_time;
                			}
                			if($noOfPrepostTestQuestions>$prepostTestQuestionNo)
                			{
                				$prepostTestQues_time = mktime(substr($prepostTestQuestionsArray[$prepostTestQuestionNo]["lastModified"],11,2),substr($prepostTestQuestionsArray[$prepostTestQuestionNo]["lastModified"],14,2),substr($prepostTestQuestionsArray[$prepostTestQuestionNo]["lastModified"],17,2),substr($prepostTestQuestionsArray[$prepostTestQuestionNo]["lastModified"],3,2),substr($prepostTestQuestionsArray[$prepostTestQuestionNo]["lastModified"],0,2),substr($prepostTestQuestionsArray[$prepostTestQuestionNo]["lastModified"],6,4));
                				$tmArray["prepostTestQues"] = $prepostTestQues_time;
                			}
                			$minTime = min($tmArray);

                			if($minTime < $time_q)
                			{
                				$whichItem = array_search($minTime,$tmArray);
                				if($whichItem=="timedTest")
                				{
                					$html	.=	'<div id="timedtest_'.$timedTestArray[$timedTestNo]["timedTestCode"].'_'.$line["sessionID"].'" class="qno" style="margin-left:10px"></div><div class="desk_block">
                						<div class="top_left">
                						</div>
                						<div class="top_right">
                						</div>
                						<div class="top_repeat">
                						</div>
                						<div class="block mid_left">
                						</div>
                						<div class="block mid_right">
                						</div>
                						<div class="block mid_repeat">
                							<div class="block_header">
                								<div>
                									<span class="title">Timed Test: </span>
                									<span>'.$timedTestArray[$timedTestNo]["description"].'</span>&nbsp;&nbsp;&nbsp;&nbsp;
                									<span class="title">Questions Attempted: </span><span>'.$timedTestArray[$timedTestNo]['noOfQuesAttempted'].'</span>&nbsp;&nbsp;&nbsp;&nbsp;
                									<span class="title">Questions Correct: </span><span>'.$timedTestArray[$timedTestNo]['quesCorrect'].'</span>&nbsp;&nbsp;&nbsp;&nbsp;
                									<span class="title">% Correct: </span><span>'.$timedTestArray[$timedTestNo]["perCorrect"].'%</span>&nbsp;&nbsp;&nbsp;&nbsp;
                									<span class="title">Time taken: </span><span>'.$timedTestArray[$timedTestNo]["timeTaken"].' secs</span>&nbsp;&nbsp;&nbsp;&nbsp;
                								</div>';
                						$html	.=	'<div><span class="title">Learning unit: </span>'.getClusterdesc($timedTestArray[$timedTestNo]["cluster"]).'</div>';
                						$html	.=	'</div>
                						</div>
                						<div class="bot_left">
                						</div>
                						<div class="bot_right">
                						</div>
                						<div class="bot_repeat">
                						</div>
                					</div>';
                					echo $html;
                					$html="";
                					$timedTestNo++;
                				}
                				if($whichItem=="game")
                				{
                					$html	.=	'<div id="activity_'.$gamesArray[$gameNo]["gameID"].'_'.$line["sessionID"].'" class="qno" style="margin-left:10px"></div><div class="desk_block">
                								<div class="top_left">
                								</div>
                								<div class="top_right">
                								</div>
                								<div class="top_repeat">
                								</div>
                								<div class="block mid_left">
                								</div>
                								<div class="block mid_right">
                								</div>
                								<div class="block mid_repeat">
                									<div class="block_header">
                										<div>
                											<span class="title">Activity: </span>
                											<span>'.$gamesArray[$gameNo]["description"].'</span>&nbsp;&nbsp;&nbsp;&nbsp;';
                								$html	.=	'<span class="title">Time taken: </span><span>'.$gamesArray[$gameNo]["timeTaken"].' secs</span>&nbsp;&nbsp;&nbsp;&nbsp;';
                								if($gamesArray[$gameNo]["level"]>0)
                									$html	.=	'<span class="title">Level: </span><span>'.$gamesArray[$gameNo]["level"].'</span>&nbsp;&nbsp;&nbsp;&nbsp;';
                								$html	.=	'</div>';
                								if($gamesArray[$gameNo]["cluster"]!="")
                								{
                									 $html	.=	'<div><span class="title">Learning unit: </span>'.getClusterdesc($gamesArray[$gameNo]["cluster"]).'</div>';
                								}
                								else
                									$html	.=	'<div><span class="title">Independent activity</span></div>';
                								$html	.=	'</div>
                								</div>
                								<div class="bot_left">
                								</div>
                								<div class="bot_right">
                								</div>
                								<div class="bot_repeat">
                								</div>
                							</div>';
                					echo $html;
                					$html="";
                					$gameNo++;
                				}
                				if($whichItem=="remedial")
                				{
                					$html	.=	'<div id="remedial_'.$remedialItemAttemptArray[$remedialItemAttemptNo]["remedialItemCode"].'_'.$line["sessionID"].'" class="qno" style="margin-left:10px"></div><div class="desk_block">
                						<div class="top_left">
                						</div>
                						<div class="top_right">
                						</div>
                						<div class="top_repeat">
                						</div>
                						<div class="block mid_left">
                						</div>
                						<div class="block mid_right">
                						</div>
                						<div class="block mid_repeat">
                							<div class="block_header">
                								<div>
                									<span class="title">Remedial: </span>
                									<span>'.$remedialItemAttemptArray[$remedialItemAttemptNo]["description"].'</span>&nbsp;&nbsp;&nbsp;&nbsp;
                									<span class="title">Time taken: </span><span>'.$remedialItemAttemptArray[$remedialItemAttemptNo]["timeTaken"].' secs</span>&nbsp;&nbsp;&nbsp;&nbsp;

                									<span class="title">Result: </span><span>'.$remedialItemAttemptArray[$remedialItemAttemptNo]["result"].'</span>&nbsp;&nbsp;&nbsp;&nbsp;
                								</div>';
                						$html	.=	'</div>
                						</div>
                						<div class="bot_left">
                						</div>
                						<div class="bot_right">
                						</div>
                						<div class="bot_repeat">
                						</div>
                					</div>';
                					echo $html;
                					$html="";
                					$remedialItemAttemptNo++;
                				}
                				if($whichItem=="prepostTestQues")
                				{
                					$question     = new Question($prepostTestQuestionsArray[$prepostTestQuestionNo]["qcode"]);
                					if($question->isDynamic())
                					{
                						$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE userID=$userID AND class=$childClass AND mode='normal' AND quesAttempt_srno=".$line['srno'];
                						$params_result = mysql_query($query);
                						$params_line   = mysql_fetch_array($params_result);
                						$question->generateQuestion("answer",$params_line[0]);
                					}
                					$questionType = $question->quesType;
                					$timeTaken    = $challengeQuesArray[$cqno][2];
                					$response     = $challengeQuesArray[$cqno][3];
                					$user_ans     = $challengeQuesArray[$cqno][1];
                					$correct_answer = $question->getCorrectAnswerForDisplay();
                					$clusterDesc = "Challenge Question";
                					$optiona_bgcolor = $optionb_bgcolor = $optionc_bgcolor = $optiond_bgcolor = "";
                					if($user_ans=="A")
                						$optiona_bgcolor="#FFA500";
                					if($user_ans=="B")
                						$optionb_bgcolor="#FFA500";
                					if($user_ans=="C")
                						$optionc_bgcolor="#FFA500";
                					if($user_ans=="D")
                						$optiond_bgcolor="#FFA500";

                					if($correct_answer=="A")
                						$optiona_bgcolor="#00FF00";
                					if($correct_answer=="B")
                						$optionb_bgcolor="#00FF00";
                					if($correct_answer=="C")
                						$optionc_bgcolor="#00FF00";
                					if($correct_answer=="D")
                						$optiond_bgcolor="#00FF00";
                					$clusterDesc = "prePost";
                					showQuestion($line["qcode"],$line["sessionID"],$srno, $question->getQuestion(), $response, $questionType, $question->getOptionA(), $question->getOptionB(), $question->getOptionC(), $question->getOptionD(), $optiona_bgcolor, $optionb_bgcolor, $optionc_bgcolor, $optiond_bgcolor, $correct_answer, $user_ans, $timeTaken, $clusterDesc, $response, $lowerLevel, $accessFromStudentInterface,$line['sessionID'],$tmp_line[0],$clusterDesc);

                					$prepostTestQuestionNo++;
                				}
                			}
                			else
                				break;
                		}
                	}

                	//Check if c.q. was given after this ques, if yes display it
                	while($cqno<count($challengeQuesArray) && $challengeQuesArray[$cqno][4]==$line['questionNo'])
                	{
                		$question     = new Question($challengeQuesArray[$cqno][0]);
                		if($question->isDynamic())
                		{
                			$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE userID=$userID AND class=$childClass AND mode='normal' AND quesAttempt_srno=".$line['srno'];
                			$params_result = mysql_query($query);
                			$params_line   = mysql_fetch_array($params_result);
                			$question->generateQuestion("answer",$params_line[0]);
                		}
                	    $questionType = $question->quesType;
                	    $timeTaken    = $challengeQuesArray[$cqno][2];
                	    $response     = $challengeQuesArray[$cqno][3];
                	    $user_ans     = $challengeQuesArray[$cqno][1];
                	    $correct_answer = $question->getCorrectAnswerForDisplay();
                	    $clusterDesc = "Challenge Question";
                		if($question->eeIcon == "1")
                			$eeresponse = getEEresponse($quesAttemptSrno,$childClass,"challenge");
                	    $optiona_bgcolor = $optionb_bgcolor = $optionc_bgcolor = $optiond_bgcolor = "";
                	    if($user_ans=="A")
                	        $optiona_bgcolor="#FFA500";
                	    if($user_ans=="B")
                	        $optionb_bgcolor="#FFA500";
                	    if($user_ans=="C")
                	        $optionc_bgcolor="#FFA500";
                	    if($user_ans=="D")
                	        $optiond_bgcolor="#FFA500";

                	    if($correct_answer=="A")
                	        $optiona_bgcolor="#00FF00";
                	    if($correct_answer=="B")
                	        $optionb_bgcolor="#00FF00";
                	    if($correct_answer=="C")
                	        $optionc_bgcolor="#00FF00";
                	    if($correct_answer=="D")
                        	$optiond_bgcolor="#00FF00";
                        if($challengeQuesArray[$cqno][7]!=1 && $challengeQuesArray[$cqno][6]==1 && $challengeQuesArray[$cqno][3]==0)
                            $correct_answer = '';
                		showQuestion($line["qcode"],$line["sessionID"],$srno, $question->getQuestionForDisplay($eeresponse), $response, $questionType, $question->getOptionA(), $question->getOptionB(), $question->getOptionC(), $question->getOptionD(), $optiona_bgcolor, $optionb_bgcolor, $optionc_bgcolor, $optiond_bgcolor, $correct_answer, $user_ans, $timeTaken, $clusterDesc, $response, $lowerLevel, $accessFromStudentInterface,$line['sessionID'],$tmp_line[0],$clusterDesc, $question->eeIcon);
                		$cqno++;
                	}
                }

                if($clusterType != "practice")
                    $srno++;$rowno++;
            }  
            ?>

            </div>
        </div>
	</div>
<?php 


include("footer.php"); ?>

<?php

function isLowerLevelCluster($teacherTopicCode, $clusterCode, $cls, $clusterAttemptID)
{
	$lowerLevelCluster = false;
	$query  = "SELECT attemptType, flow FROM ".TBL_CLUSTER_STATUS." a, ".TBL_TOPIC_STATUS." b WHERE a.ttAttemptID=b.ttAttemptID AND clusterAttemptID=$clusterAttemptID";
	$result = mysql_query($query);
	$line   = mysql_fetch_array($result);
	if($line['attemptType']=='N')
	{
		$flow  = $line['flow'];
		$objTT = new teacherTopic($teacherTopicCode,$cls,$flow);

		$level = $objTT->getClusterLevel($clusterCode);
		$maxLevel = $level[count($level) - 1];
		if($maxLevel<$objTT->startingLevel)
			$lowerLevelCluster = true;
	}
	return $lowerLevelCluster;
}

function getChallengeQuesAttemptedInSession($userID, $sessionID)
{
	$challengeQuesArray = array();
	$cq_query = "SELECT qcode,A,S,R,ttAttemptID,questionNo,attemptNo,EolMode
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
		$challengeQuesArray[$cqno][6] = $cq_line['attemptNo'];
		$challengeQuesArray[$cqno][7] = $cq_line['EolMode'];
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

function getPrePostTestQuestionsAttempted($userID, $sessionID)
{
	$prepostTestQuestionsArray = array();
	$query = "SELECT a.qcode,userResponse,timeTaken,correct, question, date_format(a.lastModified, '%d-%m-%Y %H:%i:%s') lastModified, b.clusterCode, subdifficultylevel
	          FROM   adepts_prepost_Test_Details a, adepts_questions b
	  		  WHERE  a.qcode=b.qcode AND userID=$userID AND sessionID=".$line['sessionID']." ORDER BY a.lastModified";
	$result = mysql_query($query);
	$srno = 0;
	while ($line = mysql_fetch_array($result))
	{
		$prepostTestQuestionsArray[$srno]["qcode"] = $line[0];
		$prepostTestQuestionsArray[$srno]["question"] = $line[4];
		$prepostTestQuestionsArray[$srno]["userResponse"] = $line[1];
		$prepostTestQuestionsArray[$srno]["timeTaken"] = $line[2];
		$prepostTestQuestionsArray[$srno]["correct"] = $line[3];
		$prepostTestQuestionsArray[$srno]["lastModified"] = $line[5];
		$prepostTestQuestionsArray[$srno]["clusterCode"] = $line[6];
		$prepostTestQuestionsArray[$srno]["sdl"] = $line[7];
	}
	return $prepostTestQuestionsArray;
}

function getEEresponse($srno, $childClass, $question_type, $trailType)
{
	$eeResponse = "";
	if($trailType == "ncert")
		$table = "adepts_ncertQuesAttempt";
	else
		$table = "adepts_equationEditorResponse";
	$query = "SELECT eeResponse FROM $table WHERE srno='$srno'";
	if($trailType != "ncert")
		$query .= " AND childClass='$childClass' AND question_type='$question_type'";
	$result = mysql_query($query);
	if(mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$eeResponse = $row[0];
	}
	return $eeResponse;
}
function getLongUserResponse($srno, $userID, $qcode, $sessionID,$user_ans)
{
    $longResponse = "";
    $query = "SELECT userResponse FROM longUserResponse WHERE srno='$srno' AND userID='$userID' AND sessionID='$sessionID' AND qcode='$qcode'";
    $result = mysql_query($query);
    if(mysql_num_rows($result) > 0)
    {
        $row = mysql_fetch_array($result);
        $longResponse = $row[0];
    }
    else {
        $longResponse = $user_ans;
    }
    /*else {
        $query = "SELECT A FROM ".TBL_QUES_ATTEMPT_CLASS." WHERE srno='$srno' AND userID='$userID' AND sessionID='$sessionID' AND qcode='$qcode'";
        $result = mysql_query($query);
        if(mysql_num_rows($result) > 0)
        {
            $row = mysql_fetch_array($result);
            $longResponse = $row[0];
        }
    }*/
    return $longResponse;
}

function showQuestion($qcode,$sessionID, $srno, $question, $response, $questionType, $optionA, $optionB, $optionC, $optionD, $optiona_bgcolor, $optionb_bgcolor, $optionc_bgcolor, $optiond_bgcolor, $correct_answer, $user_ans, $timeTaken, $clusterDesc, $response, $lowerLevel, $accessFromStudentInterface,$sessionID,$startTime,$clusterDesc, $eeIcon="0", $trailType="", $oneQuestionGroup=false, $quesAttemptSrno=0,$teacherComments="", $ncertDescription='')
{
    $type = ($trailType == "ncert")?"Exercise":(($trailType == "worksheet")?"Worksheet":"Learning unit");
	$quesType="";
	if($clusterDesc=="Challenge Question")
	{
		$srno = "C.Q.";
		$quesType="challenge";
	}
	else
		$quesType="normal";
	$cls = "";
	if($response==1)
		$cls="correct_mark";
	else if($response==0)
		$cls = "incorrect_mark";
?>
	<div id="quesTrailDiv">
            	<div id="quesNoDiv">
                	<div id="quesNo"><?=$srno?></div>
                    <div class="forLowerOnly <?=$cls?>"></div>
                </div>
                <div id="quesTextDiv">
                	<div id="quesText"><?=$question?></div>
					<div id="userAnsMain" class="forHighestOnly">
                    	<div id="userAnsText" data-i18n="topicWiseQuesTrailPage.yourResponse">Your response</div>
                        <div id="userAnsImotion" class="<?=$cls?>"></div>
                    </div>
                    <?php 
                    if ($type!='Worksheet'){ ?>
                    <div id="timeTaken" class="forHighestOnly" ><div id="alignSec">SEC : <?=$timeTaken?></div></div>
                    <div id="session" class="forHighestOnly">
                        <span data-i18n="common.sessionID">Session ID</span> : <font color="#606062" style="font-weight:bold;"><?=$sessionID?></font>
                    </div>
                    <div id="duration" class="forHighestOnly">
                        <span data-i18n="topicWiseQuesTrailPage.startTime">Start Time</span>: <br/> <font color="#606062" style="font-weight:bold;"><?=$startTime?></font>
                    </div>
                    <?php } ?>
                    <div id="userAnsMain" class="forHigherOnly hidden">
                    	<div id="userAnsText" data-i18n="topicWiseQuesTrailPage.yourResponse">Your response</div>
                        <div id="userAnsImotion" class="<?=$cls?>"></div>
                    </div>
                    <div class="clear"></div>

<?php	if($questionType=='MCQ-4' || $questionType=='MCQ-3' || $questionType=='MCQ-2')	{	?>
                    <div id="optionDiv">
                    	<div class="optionAdiv floatLeft">
                        	<div class="optA floatLeft <?php if($correct_answer=="A") echo "option"; else echo "option"?>" data-i18n="common.optA">A</div>
                            <div class="optAText floatLeft"><?=$optionA;?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="optionBdiv floatLeft">
                        	<div class="optB floatLeft <?php if($correct_answer=="B") echo "option"; else echo "option"?>" data-i18n="common.optB">B</div>
                            <div class="optAText floatLeft"><?=$optionB;?></div>
                            <div class="clear"></div>
                        </div>
<?php	if($questionType=='MCQ-4') { ?>
                        <div class="clear"></div>
<?php }	if($questionType=='MCQ-4' || $questionType=='MCQ-3') { ?>
                        <div class="optionCdiv floatLeft">
                        	<div class="optC floatLeft <?php if($correct_answer=="C") echo "option"; else echo "option"?>" data-i18n="common.optC">C</div>
                            <div class="optAText floatLeft"><?=$optionC;?></div>
                            <div class="clear"></div>
                        </div>
	<?php	if($questionType=='MCQ-4') { ?>
                        <div class="optionDdiv" class="floatLeft">
                        	<div class="optD floatLeft <?php if($correct_answer=="D") echo "option"; else echo "option"?>" data-i18n="common.optD">D</div>
                            <div class="optDText floatLeft"><?=$optionD;?></div>
                            <div class="clear"></div>
                        </div>
	<?php } } ?>
                        <div class="clear"></div>
                    </div>
<?php } ?>

                    <div id="answerDiv">
                    	<div id="userResponseText" data-i18n="topicWiseQuesTrailPage.userResponse">User Response</div>
                        <div id="userResponse"><?=is_null($user_ans)?'Not Attempted':$user_ans;?></div>
                        <div id="correctAnsText" data-i18n="topicWiseQuesTrailPage.correctAns">Correct Answer</div>
                        <div id="correctAns"><?=$correct_answer?></div>

                        <div class="clear forHigherOnly"></div>
                        
                        <?php if ($type!='Worksheet'){ ?>
                        <div id="session" class="forHigherOnly hidden">
                            <span data-i18n="common.sessionID">Session ID</span> : <font color="#e52e00"><?=$sessionID?></font>
                        </div>
                        <div id="duration" class="forHigherOnly hidden">
                            <span data-i18n="topicWiseQuesTrailPage.startTime">Start Time</span> : <font color="#e52e00"><?=$startTime?></font>
                        </div>
                        <div id="learningUnit" class="forHigherOnly hidden">
                            <span <?=(($type!='Exercise' && $type!='Worksheet')?'data-i18n="topicWiseQuesTrailPage.learningUnit"':'')?>><?=$type?></span> : <font color="#e52e00"><?=($type!='Exercise')?$clusterDesc:$ncertDescription?></font>
                        </div>
                        <div id="learningUnit" class="forHighestOnly">
                            <span <?=(($type!='Exercise' && $type!='Worksheet')?'data-i18n="topicWiseQuesTrailPage.learningUnit"':'')?>><?=$type?></span> : <font color="#606062" style="font-weight:bold;"><?=($type!='Exercise')?$clusterDesc:$ncertDescription?></font>
                        </div>
                        <!--<div id="timeTakenText" class="forLowerOnly" data-i18n="topicWiseQuesTrailPage.timeTaken">Time Taken</div>-->
                        <div id="timeTaken" class="forLowerOnly hidden"><span data-i18n="topicWiseQuesTrailPage.timeTaken"></span> : <?=$timeTaken?></div>
                        <div id="timeTakenUnit" class="hidden" data-i18n="topicWiseQuesTrailPage.timeUnit"></div>
                        <div id="timeTaken" class="forHigherOnly hidden"> <?=$timeTaken?></div>
                        <?php } ?>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
<?php
}
?>