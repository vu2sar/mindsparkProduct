<?php
	set_time_limit(0);
	error_reporting(E_ERROR);
	include("header.php");
	include("../slave_connectivity.php");
    include("../userInterface/functions/orig2htm.php");
    include("../userInterface/classes/clsQuestion.php");
	include("../userInterface/functions/functionsForDynamicQues.php");
    include("../userInterface/classes/clsNCERTQuestion.php");
    include("../teacherInterface/classes/eipaging.cls.php");		//this is path on internet
    include_once("../userInterface/classes/clsTeacherTopic.php");
	
    $userID      = $_REQUEST['user_passed_id'];
	$user1 = new user($userID);
    $schoolCode  = $user1->schoolcode;
    $category    = $user1->category;
    $subcategory = $user1->subcategory;
    $accessFromStudentInterface = isset($_POST['accessFromStudentInterface'])?$_POST['accessFromStudentInterface']:0;	//This will be set to 1 if accessed from student interface reports section

    if(!isset($_SESSION['openIDEmail']))
	{
		header("Location:../logout.php");
		exit;
	}
	
    $clspaging = new clspaging();
	$clspaging->setgetvars();
	$clspaging->setpostvars();

	$showWrongAns = false;
	if(isset($_POST['chkWrongAns']))
		$showWrongAns = true;
	$errorReportingFlag=0;
	if(isset($_REQUEST["mode"]) && $_REQUEST["mode"]=="errorReporting")
		$errorReportingFlag=1;
?>
<title>Mindspark -
<?php if($errorReportingFlag==1)
			echo "Error Reporting";
		else
			echo "Student Trails";
?>
</title>
<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css?ver=1" rel="stylesheet" type="text/css">
<link href="css/studentTrail.css?ver=1" rel="stylesheet" type="text/css">
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script src="libs/idletimeout.js" type="text/javascript"></script>
<script>
	var langType = '<?=$language;?>';
	function load(){
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#sideBar").css("height",sideBarHeight+"px");
		$("#childSelectedID").attr("disabled","disabled");
	}
	function lastPage(){
		alert("This is the last page!");
	}
	function firstPage(){
		alert("This is the first page!");
	}
</script>
<script>
var userArray= new Array();
var respUserId= new Array();
var question_srno = null;
$(".eqEditorToggler").live("click",function(){
	$(this).parents("td:first").next().find(".eqEditorConatiner:first").toggle("slow");
});
function toggleCommentBox(srno)
{
	question_srno = srno;
	if($("#commentBox").is(":visible"))
	{
		$("#commentBox").fadeOut();
		$("#commentText").val("");
	}
	else
	{
		$("#commentBox").fadeIn();
		$("#commentText").focus();
	}
}
function saveCommentBox()
{
	var questionSrno = question_srno;
	var comment = $.trim($("#commentText").val());
	if(comment == "")
	{
		$("#commentText").css("border-color","#F00");
		return(false);
	}
	else
	{
		$("#commentText,#commentSaveBtn,#commentCancleBtn").attr("disabled",true);
		$.post("controller.php","mode=saveNcertTeacherComment&srno="+questionSrno+"&comment="+comment+"",function(data){
			if($.trim(data) == "true")
			{
				var prefix = '<?php echo "<strong>".$_SESSION["username"]." (".date('d-m-Y')."):</strong>&nbsp;" ?>';
				$("#commentText").css("border-color","");
				$("#commentText,#commentSaveBtn,#commentCancleBtn").attr("disabled",false);
				$("#comments_"+questionSrno).append("<br>"+prefix+comment);
				$("#commentBox").fadeOut();
				$("#commentText").val("");
			}
			else
			{
				$("#commentText").css("border-color","");
				$("#commentText").val("");
				$("#commentText,#commentSaveBtn,#commentCancleBtn").attr("disabled",false);
			}
		})
	}
}
$(document).ready(function(e) {
	
    $("#childNameLast1Hour").click(function(){
		if($(this).is(":checked")){
			setTryingToUnload();
			window.location.href = "studentTrail.php?mode=errorReporting&last1hour=1";
		}			
		else{
			setTryingToUnload();
			window.location.href = "studentTrail.php?mode=errorReporting&last1hour=0";
		}			
	});
	
	<?php if($errorReportingFlag==1) { ?>
		$("#ncertRadio").hide();
		$(".qno").css("cursor","pointer");
		$(".qno").click(function() { 
			var errorIDArr	=	$(this).attr("id").split("_");
			if(!errorIDArr[3])
				errorIDArr[3]="";
			setTryingToUnload();
			window.location.href = "teacherComment.php?qcode="+errorIDArr[1]+"&type="+errorIDArr[0]+"&sessionID="+errorIDArr[2]+"&srno="+errorIDArr[3];
		});
	<?php } ?>
});
</script>
<?php

    $child_All_List=array();
    $cUserID="";
    //fill initial data
	if($_REQUEST["last1hour"]==1)
	{
		$sessionTime = date("Y-m-d H:i:s", strtotime('-60 minutes'));
		$query = "SELECT DISTINCT A.childName, A.childClass,A.userID, A.childSection 
				  FROM   adepts_userDetails A , adepts_sessionStatus B 
				  WHERE  A.userID=B.userID AND B.startTime_int = ".date("Ymd")." AND B.startTime > '$sessionTime'  AND A.category='STUDENT' AND subcategory='SCHOOL' AND enabled=1 
				  AND schoolcode=$schoolCode AND subjects like '%".SUBJECTNO."%'
				  ORDER BY childName";
	}
    else if (strcasecmp($category,"Home Center Admin")==0)
    {
    	$query = "SELECT childName, childClass,userID, childSection
				  FROM   adepts_userDetails
				  WHERE  category='STUDENT' AND subcategory='".$subcategory."' AND enabled=1 AND subjects like '%".SUBJECTNO."%'
				  ORDER BY childName";
    }
    else
    {
    	$query = "SELECT childName, childClass,userID, childSection
				  FROM   adepts_userDetails
				  WHERE  category='STUDENT' AND subcategory='SCHOOL' AND enabled=1 AND schoolcode=$schoolCode AND subjects like '%".SUBJECTNO."%'
				  ORDER BY childName";
    }


    $result = mysql_query($query);
    $userList = "";
    while ($line=mysql_fetch_array($result)) {
        $userList .= $line[1]." (".$line[0].")~";
        $temp=$line[0]." (".$line[1];
        if($line[3]!="")
        	$temp .= $line[3];
        $temp .= ")";
        $child_All_List[]=$temp;
        $cUserID=$line[2];
?>
<script>

        userArray.push(' <?=trim($temp)?>');
        respUserId.push('<?=$cUserID?>');

        </script>
<?php
    }

$bypass_flag=0;

$topic = $childName = $childClass  = $student_userID = $sessionIDStr = "" ;

$topicsAttempted =$exerciseAttempted = $sessions = $sessionID = $startTime = array();
if(isset($_REQUEST['sessionID']))
{
    $sessionID = $_REQUEST['sessionID'];
}
else if (isset($_GET['session_passed_id']) && $_GET['session_passed_id'])
{
    $sessionID = $_GET['session_passed_id'];
    $bypass_flag=1;
}
if(isset($_REQUEST['childName']))
    $childName = $_REQUEST['childName'];
if(isset($_REQUEST['cls']))
    $childClass = $_REQUEST['cls'];
if(isset($_REQUEST['topic']))
{
    $topic = $_REQUEST['topic'];
}
else if(isset($_GET['topic_passed_id']) && $_GET['topic_passed_id'] != "" )
{
    $topic = $_GET['topic_passed_id'];
}
if(isset($_REQUEST['exercise']))
{
    $exercise = $_REQUEST['exercise'];
}
if(isset($_REQUEST['trailType']))
{
    $trailType = $_REQUEST['trailType'];
}
if(isset($_GET['user_passed_id']) && $_GET['user_passed_id'] != "")
{
    $student_userID=$_GET['user_passed_id'];
}
if(isset($_POST['student_userID']) && $_POST['student_userID'] != "")
{
    $student_userID=$_POST['student_userID'];
}
if($student_userID!="")
{
	$query  = "SELECT childName, childClass, childSection FROM adepts_userDetails WHERE userID=$student_userID";
	$result = mysql_query($query);
	$line   = mysql_fetch_array($result);
	$childName = $line[0]." (".$line[1].$line[2].")";
	$childClass = $line[1];
}
if ($bypass_flag == 1)
{
    $sessionIDStr=$sessionID;
}
else
{
    for($i=0;$i<count($sessionID); $i++)
        if($sessionID[$i]!="")
        {
            $sessionIDStr .= $sessionID[$i].",";
        }
        $sessionIDStr = substr($sessionIDStr,0,-1);
}


if($childName!="")
{
    $query  = "SELECT distinct a.teacherTopicCode, b.teacherTopicDesc
               FROM   ".TBL_TOPIC_STATUS." a, adepts_teacherTopicMaster b
               WHERE  userID=$student_userID AND a.teacherTopicCode = b.teacherTopicCode ORDER BY classification";
    $result = mysql_query($query) or die("Error1".  mysql_error());
    $srno = 0;
    while ($line   = mysql_fetch_array($result))
    {
        $topicsAttempted[$srno][0] = $line[0];
        $topicsAttempted[$srno][1] = $line[1];
        $srno++;
    }
	
    $query  = "SELECT sessionID,date_format(startTime, '%d-%m-%Y %H:%i:%s') as startTime FROM ".TBL_SESSION_STATUS." WHERE userID=$student_userID";
	if($errorReportingFlag==1)
	{
		if($_REQUEST["last1hour"]==1)
			$sessionTime = date("Y-m-d H:i:s", strtotime('-60 minutes'));
		$query  .= " AND startTime_int = ".date("Ymd");
	}
	$query  .= " ORDER BY sessionID DESC";
    $result = mysql_query($query) or die("Error");
    while ($line   = mysql_fetch_array($result))
    {
		if($errorReportingFlag==1)
			$sessionIDStr	.=	$line[0].",";
        array_push($sessions,$line[0]);
        array_push($startTime,$line[1]);
    }
	if($errorReportingFlag==1)
		$sessionIDStr	=	substr($sessionIDStr,0,-1);
	$query  = "SELECT distinct a.exerciseCode, b.description
               FROM   adepts_ncertHomeworkStatus a, adepts_ncertExerciseMaster b
               WHERE  userID=$student_userID AND a.exerciseCode = b.exerciseCode ORDER BY chapterNo";
    $result = mysql_query($query) or die("Error1");
    $srno = 0;
    while ($line   = mysql_fetch_array($result))
    {
        $exerciseAttempted[$srno][0] = $line[0];
        $exerciseAttempted[$srno][1] = $line[1];
        $srno++;
    }
}
?>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script>
var langType = '<?=$language;?>';
function validate()
{
    var childname = trim(document.getElementById('childName').value);

    if(childname=="")
    {
        alert("Please specify the Child Name");
        document.getElementById('childName').focus();
        return false;
    }
    //check if the name is from the available list
    var found = 0;
    for(var i=0; i<userArray.length;i++)
    {

        if(trim(userArray[i])==trim(childname))
        {
            found = 1;
            document.getElementById('student_userID').value=respUserId[i];
            break;
        }
    }
    if(!found)
    {
        alert("Please specify a valid child name");
        document.getElementById('childName').focus();
        return false;
    }
    return true;
}

function suggestUserList(userArray)
{
	load();
    document.getElementById('childName').disabled = false;
    var obj1 = new actb(document.getElementById('childName'),userArray);
    document.getElementById('btnGo').disabled = false;

}
function trim(query)
{
    return query.replace(/^\s+|\s+$/g,"");
}

function checkSelected()
{
	var selectedRadio = getSelectedTrail();
	if(selectedRadio == "ncert")
	{
		var exercise = document.getElementById('lstExercise').value;
		if(exercise=="")
		{
			alert("Please select an exercise");
		}
		else
		{
			setTryingToUnload();
			document.getElementById('frmSelect').submit();
		}
	}
	else
	{
		var topic = document.getElementById('lstTopic').value;
		var noOfSessions = document.getElementById('lstSessionID');
		var sessionID= 0;
		for(var i=1; i<noOfSessions.length; i++)
		{
			if(noOfSessions[i].selected)
			{
				sessionID=1;
				break;
			}
		}
		if(topic=="" && !sessionID)
		{
			alert("Please select a topic or session id");
		}
		else
		{
			setTryingToUnload();
			document.getElementById('frmSelect').submit();
		}
	}
}
function navigatepage(varprefix, cp)
{
	document.getElementById(varprefix+'_currentpage').value = cp;
	setTryingToUnload();
	document.getElementById('frmSelect').submit();
}
function submitPage()
{
	setTryingToUnload();
	document.getElementById('frmSelect').submit();
}
function toggleOption()
{
	var selectedRadio = getSelectedTrail();
	if(selectedRadio == "ncert")
	{
		document.getElementById(selectedRadio).style.display = "block";
		document.getElementById("commonDiv").style.display = "block";
		document.getElementById("regular").style.display = "none";
		document.getElementById("regular1").style.display = "none";
		document.getElementById("line").style.display = "none";
		document.getElementById("line1").style.display = "none";
		document.getElementById("session").style.display = "none";
		document.getElementById("session1").style.display = "none";
		document.getElementById("ncert").style.display = "table-cell";
		document.getElementById("ncert1").style.display = "table-cell";
	}
	else if(selectedRadio == "regular")
	{
		document.getElementById(selectedRadio).style.display = "block";
		document.getElementById("commonDiv").style.display = "block";
		document.getElementById("regular").style.display = "table-cell";
		document.getElementById("line").style.display = "table-cell";
		document.getElementById("line1").style.display = "table-cell";
		document.getElementById("regular1").style.display = "table-cell";
		document.getElementById("session").style.display = "table-cell";
		document.getElementById("session1").style.display = "table-cell";
		document.getElementById("ncert").style.display = "none";
		document.getElementById("ncert1").style.display = "none";			
	}
}
function getSelectedTrail()
{
	var radioLength = document.getElementsByName("trailType").length;
	var radioObj = document.getElementsByName("trailType");
	for(var i = 0; i < radioLength; i++)
	{
		if(radioObj[i].checked)
		{
			return(radioObj[i].value);
		}
	}
}
</script>
<script language="javascript" type="text/javascript" src="libs/suggest1.js"></script>
<script language="javascript" type="text/javascript" src="libs/suggest2.js"></script>
<script type="text/javascript" src="/mindspark/js/load.js"></script>
</head>
<body class="translation" onLoad="suggestUserList(userArray)" onmousemove="reset_interval()" onclick="reset_interval()" onkeypress="reset_interval()" onscroll="reset_interval()">
<?php include("eiColors.php") ?>
<div id="fixedSideBar">
	<?php include("fixedSideBar.php") ?>
</div>
<div id="topBar">
	<?php include("topBar.php");$userID1 = $_REQUEST['user_passed_id'];$user2 = new user($userID1);$childName= $user2->childName; ?>
</div>
<div id="sideBar">
	<?php include("sideBar.php") ?>
</div>
<div id="container">
<div id="trailContainer">
<div id="headerBar">
	<div id="pageName">
		<div class="arrow-black"></div>
		<div id="pageText">
			<?php if($errorReportingFlag==1)	echo "Error reporting";	else echo "Student Trails";?>
		</div>
	</div>
</div>
<form method="POST" id="frmQuesTrail" autocomplete='off'>
	<input type="hidden"  id="student_userID" name="student_userID" value="">
</form>

<?php if($errorReportingFlag==1) { 
echo "<div align='center' style='color:red'><strong align='center'>Click on question numbers to report error in that question.</strong></div>";
} ?>
<form id="frmSelect" method="post">
<input type="hidden"  id="student_userID" name="student_userID" value="<?=$student_userID?>">
<input type="hidden" name="clspaging__currentpage" id="clspaging__currentpage">
<input type="hidden" name="accessFromStudentInterface" value="<?=$accessFromStudentInterface?>">
<?php if($accessFromStudentInterface) { ?>
<input type="hidden" name="trailType" id="trailType" value="<?=$trailType?>">
<input type="hidden" name="exercise" id="exercise" value="<?=$exercise?>">
<?php } ?>
<div id="pnlTopicSelect">
	<?php if(!$accessFromStudentInterface && $childName != "") { ?>
	<table id="selectTopicTbl" align="center" border="0" width="80%" <?php if($errorReportingFlag==1) echo "style='display:none'";?>>
		<td>
			<input type="checkbox" name="chkWrongAns" id="chkWrongAns" onClick="submitPage();" <?php if($showWrongAns) echo " checked";?>>
				<label for="chkWrongAns">Show only wrong answers</label>
		</td>
	</table>
	<?php } else {echo "<input type='hidden' name='topic' value='$topic'><input type='hidden' name='topicDesc' value='".$_POST['topicDesc']."'>";}?>
</div>
<div style="clear:both"></div>

<?php
if($topic!="" || $sessionIDStr!="" || $exercise!= "") {
if($trailType=="ncert" && $exercise!="")
{
	$sql = "SELECT ncertAttemptID FROM adepts_ncertHomeworkStatus WHERE userID=$student_userID AND exerciseCode='$exercise' ORDER BY ncertAttemptID DESC LIMIT 1";
	$result = mysql_query($sql);
    if(mysql_num_rows($result)==0)
    {
        $noOfRecords = 0;
    }
	else
	{
		$row = mysql_fetch_array($result);
		$attemptID = $row[0];
		$query="SELECT a.qcode, a.R, a.S, a.A, a.eeresponse,a.sessionID, em.description as teacherTopicDesc, em.exerciseCode as clusterCode, em.description as cluster, a.srno, a.questionNo, em.exerciseCode as teacherTopicCode, a.ncertAttemptID, a.teacherComments FROM adepts_ncertQuesAttempt a, adepts_ncertExerciseMaster em WHERE em.exerciseCode='$exercise' AND ncertAttemptID='$attemptID' AND a.R!=-1";
		if($showWrongAns)
			$query .= " AND a.R=0 ";
		
		$count_query = "SELECT count(srno) as noofques, sum(R) as correct FROM adepts_ncertQuesAttempt WHERE ncertAttemptID='$attemptID' AND R!=-1 AND R!=3";
		if($showWrongAns)
			$count_query .= " AND R=0 ";
		$result = mysql_query($count_query);
		$line   = mysql_fetch_array($result);
		$noOfRecords = $line['noofques'];
	}
}
else if($topic!="" && ($trailType=="regular" || $trailType==""))
{
    $query  = "SELECT ttAttemptID FROM ".TBL_TOPIC_STATUS." WHERE teacherTopicCode='$topic' AND userID=$student_userID";
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
        $query  = "SELECT clusterAttemptID FROM ".TBL_CLUSTER_STATUS." WHERE ttAttemptID in ($topicAttemptID) AND userID=$student_userID";
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

            $query="SELECT  a.qcode, a.R, a.S, a.A, a.sessionID, teacherTopicDesc, cm.clusterCode, cluster, a.srno, questionNo, tm.teacherTopicCode, a.clusterAttemptID, date_format(a.lastModified, '%d-%m-%Y %H:%i:%s') lastModified
                    FROM    ".TBL_QUES_ATTEMPT."_class$childClass a,
                            adepts_teacherTopicMaster tm,
                            adepts_clusterMaster cm
                    WHERE   a.teacherTopicCode = tm.teacherTopicCode AND
                            a.clusterCode = cm.clusterCode AND
                            clusterAttemptID in ($clusterAttemptID) ";

            if($sessionIDStr!="")
                $query .= " AND sessionID in ($sessionIDStr)";
            if($showWrongAns)
            	$query .= " AND a.R=0 ";
            $query .= " ORDER BY srno";
            //echo $query;
            $count_query = "SELECT count(srno) as noofques, sum(R) as correct FROM ".TBL_QUES_ATTEMPT."_class$childClass WHERE clusterAttemptID in ($clusterAttemptID)";
            if($sessionIDStr!="")
                $count_query .= " AND sessionID in ($sessionIDStr)";
            if($showWrongAns)
            	$count_query .= " AND R=0 ";
            $result = mysql_query($count_query) or die(mysql_error()."Invalid search criteria");
            $line   = mysql_fetch_array($result);
            $noOfRecords = $line['noofques'];
        }
    }
}
else if($sessionIDStr!="")
{
    $query  = "SELECT b.qcode, b.R, b.S, b.A, sessionID, teacherTopicDesc, c.clusterCode, cluster, b.srno, questionNo, e.teacherTopicCode, b.clusterAttemptID, date_format(b.lastModified, '%d-%m-%Y %H:%i:%s') lastModified
               FROM   ".TBL_QUES_ATTEMPT."_class$childClass b, adepts_clusterMaster c, adepts_teacherTopicMaster e
               WHERE  b.clusterCode=c.clusterCode AND e.teacherTopicCode=b.teacherTopicCode AND sessionID in ($sessionIDStr)";
   	if($_REQUEST["last1hour"]==1)
		$query .= " AND b.lastModified >= '$sessionTime'";
   	if($showWrongAns)
   		$query .= " AND b.R=0 ";
	if($errorReportingFlag==1)
    	$query .= " ORDER BY srno DESC";
	else
		$query .= " ORDER BY srno";
    //echo $query;
    $count_query = "SELECT count(srno) as noofques, sum(R) as correct FROM ".TBL_QUES_ATTEMPT."_class$childClass WHERE sessionID in ($sessionIDStr)";
	if($_REQUEST["last1hour"]==1)
		$count_query .= " AND lastModified >= '$sessionTime'";
    if($showWrongAns)
    	$count_query .= " AND R=0 ";
    $result = mysql_query($count_query) or die(mysql_error()."Invalid search criteria");
    $line   = mysql_fetch_array($result);
    $noOfRecords = $line['noofques'];


}
if($noOfRecords==0)
{
    echo "<center>No ques found!</center>";
}
else
{

$totalQuestions = $noOfRecords;
$perCorrect = round($line['correct']/$totalQuestions*100,1);
if(!$showWrongAns && $errorReportingFlag!=1)
{?>
<!--<script>
$("#totalQuesDone").text('<?=$totalQuestions?>');
$("#perCorrect").text('<?=$perCorrect?>'+"%");
</script>-->
<?php }
$clspaging->numofrecs = $noOfRecords;
if($clspaging->numofrecs>0)
{
	$clspaging->getcurrpagevardb();
}
if($clspaging->numofpages > 1) {
?>
    <!--<table id="pagingTable" align="center">
    	<tr>
    		<td>
	    		<?php   $clspaging->writeHTMLpagesrange($_SERVER['PHP_SELF'],true,"http://www.mindspark.in/mindspark/");	?>
	    	</td>
	    </tr>
	</table>-->
	<table id="pagingTable">
		<td>
				<?php   $clspaging->writeHTMLpagesrange($_SERVER['PHP_SELF'],true,"http://www.mindspark.in/mindspark/");	?>
		</td>
	</table>
<?php
}

//$srno = 1;
$srno=($clspaging->currentpage-1)*$clspaging->numofrecsperpage+1;
$query .= "  ".$clspaging->limit;
$result = mysql_query($query) or die("<br>Error in query - ".mysql_error());



$tmp_sessionNo = isset($_POST['lastSessionID'])?$_POST['lastSessionID']:"";
$tmp_topic     = isset($_POST['lastTopic'])?$_POST['lastTopic']:"";
$tmp_cluster   = isset($_POST['lastCluster'])?$_POST['lastCluster']:"";
$lowerLevel    = isset($_POST['lowerLevel'])?$_POST['lowerLevel']:false;
$rowno = 1;
$oldGroupID = "";
$boolGroupText = false;
while ($line=mysql_fetch_array($result))
{
	$qcodeArray = array();
    $timeTaken    = $line['S'];
    $response     = $line['R'];
	$eeresponse   = isset($line['eeresponse'])?$line['eeresponse']:"";
    $user_ans     = $line['A'];
    $topic        = $line['teacherTopicDesc'];
    $clusterCode  = $line['clusterCode'];
    $clusterDesc  = $line['cluster'];
    $ttCode       = $line['teacherTopicCode'];
    $clusterAttemptID = $line['clusterAttemptID'];
    $quesAttemptSrno = $line['srno'];
	$teacherComments = (isset($line['teacherComments']))?$line['teacherComments']:"";
	
	$type = ($trailType == "ncert")?"Exercise":"Learning unit";
	if($trailType=="ncert")
	{
		$clusterType = "practice";
		$sqlNew = "SELECT groupID FROM adepts_ncertQuestions WHERE qcode=".$line['qcode'];
		$resultNew = mysql_query($sqlNew);
		$rowNew = mysql_fetch_assoc($resultNew);
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
			$question     = new ncertQuestion($line['qcode']);
		else
			$question     = new Question($line['qcode']);
			
		if($question->isDynamic())
		{
			$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE userID=$student_userID AND class=$childClass AND mode='normal' AND quesAttempt_srno=".$line['srno'];
			$params_result = mysql_query($query);
			$params_line   = mysql_fetch_array($params_result);
			$question->generateQuestion("answer",$params_line[0]);
		}

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
			$optiona_bgcolor="optionIncorrect";
		if($user_ans=="B")
			$optionb_bgcolor="optionIncorrect";
		if($user_ans=="C")
			$optionc_bgcolor="optionIncorrect";
		if($user_ans=="D")
			$optiond_bgcolor="optionIncorrect";

		if($correct_answer=="A")
			$optiona_bgcolor="optionCorrect";
		if($correct_answer=="B")
			$optionb_bgcolor="optionCorrect";
		if($correct_answer=="C")
			$optionc_bgcolor="optionCorrect";
		if($correct_answer=="D")
			$optiond_bgcolor="optionCorrect";

		if($rowno==1)
		{
			$timedTestArray = getTimedTestAttemptedInSession($student_userID,$line['sessionID']);
			$timedTestNo = 0;
			$noOfTimedTests = count($timedTestArray);

			$gamesArray = getGamesAttemptedInSession($student_userID,$line['sessionID']);
			$noOfGames = count($gamesArray);
			$gameNo = 0;
			
			$remedialItemAttemptArray = getRemedialItemAttempts($student_userID,$line['sessionID']);
			$noOfRemedialItems = count($remedialItemAttemptArray);
			$remedialItemAttemptNo = 0;

			$prepostTestQuestionsArray = getPrePostTestQuestionsAttempted($student_userID,$line['sessionID']);
			$noOfPrepostTestQuestions = count($prepostTestQuestionsArray);
			$prepostTestQuestionNo = 0 ;
			
			$challengeQuesArray = getChallengeQuesAttemptedInSession($student_userID,$line['sessionID']);
			for($cqno=0; $cqno<count($challengeQuesArray)&& $challengeQuesArray[$cqno][4]<$line['questionNo']; $cqno++);
		}
		if($tmp_sessionNo!=$line['sessionID'])    {
			$query = "SELECT date_format(startTime,'%d/%m/%Y %H:%i:%s') FROM ".TBL_SESSION_STATUS." WHERE sessionID=".$line['sessionID'];
			$tmp_result = mysql_query($query);
			$tmp_line = mysql_fetch_array($tmp_result);
			echo "<div id='questionContainer'>";
			echo "<div  class='section_header'>";
			echo "<div style='width: 46%;float: left;'> <span>Session ID: </span><span style='color:#2F99CB;' class='title'>".$line['sessionID']."</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span>Start Time: </span><span style='color:#2F99CB;' class='title'>".$tmp_line[0]."</span></div>";
			if ($tmp_cluster!=$clusterCode)
			{
				$tmp_cluster = $clusterCode;
				$lowerLevel = isLowerLevelCluster($ttCode, $clusterCode, $childClass, $clusterAttemptID);
			}
			echo "<div id='classTopic'><div class='arrow-black1'></div><div id='classText'>Total Questions Done : <span id='totalQuesDone' style='color:#E75903;'>".$totalQuestions."</span></div></div><div id='classTopic'><div class='arrow-black1'></div><div id='classText'>Percentage correct : <span id='perCorrect' style='color:#E75903;'>".$perCorrect."%</span></div></div>";
			echo "</div><br/>";
			$tmp_sessionNo = $line['sessionID'];
			$tmp_topic = $topic;

			//Get the challenge ques attempted, if any, in the session
			$challengeQuesArray = array();
			if(SUBJECTNO==2)
			{
				$challengeQuesArray = getChallengeQuesAttemptedInSession($student_userID,$line['sessionID']);
				
				$timedTestArray = getTimedTestAttemptedInSession($student_userID,$line['sessionID']);
				$timedTestNo = 0;
				$noOfTimedTests = count($timedTestArray);

				$gamesArray = getGamesAttemptedInSession($student_userID,$line['sessionID']);
				$noOfGames = count($gamesArray);
				$gameNo = 0;
				
				$remedialItemAttemptArray = getRemedialItemAttempts($student_userID,$line['sessionID']);
				$noOfRemedialItems = count($remedialItemAttemptArray);
				$remedialItemAttemptNo = 0;

				$prepostTestQuestionsArray = getPrePostTestQuestionsAttempted($student_userID,$line['sessionID']);
				$noOfPrepostTestQuestions = count($prepostTestQuestionsArray);
				$prepostTestQuestionNo = 0 ;
			}
			if($rowno!=1)
				$cqno = 0;
		}
		elseif($tmp_topic!=$topic)
		{
			if ($tmp_cluster!=$clusterCode)
			{
				$tmp_cluster = $clusterCode;
				$lowerLevel = isLowerLevelCluster($ttCode, $clusterCode, $childClass, $clusterAttemptID);
			}
			$tmp_topic = $topic;
		}
		elseif ($tmp_cluster!=$clusterCode)
		{
			$lowerLevel = isLowerLevelCluster($ttCode, $clusterCode, $childClass, $clusterAttemptID);
			$tmp_cluster = $clusterCode;
		}
		if($clusterType == "practice")
		{
		?>
        <div <div id='questionContainer'>
        <?php
			if($boolGroupText)
			{
				$boolGroupText = false;
		?>
                <table width="90%" border="0" cellspacing="2" cellpadding="3" align="center">
                    <tr>
                    	<?php
						if($trailType == "ncert" && !$oneQuestionGroup)
						{
						?>
                    	<td align='center' width='5%'><div class="qno"><?=$groupNo?></div></td>
                        <?php
                        }
						?>
                        <td valign="top" align="left"><p><span class="quesDetails"><?php echo $groupText;?></span><br></p></td>
                    </tr>
                </table>
        <?php
			}
		}
		if($clusterType == "practice")
			echo '<div class="singleQuestion column'.$groupColumn.'">';
		if($trailType == "ncert")
			showQuestion($line["qcode"],$line["sessionID"],$question->subQuestionNo, $question->getQuestionForDisplay($eeresponse), $response, $questionType, $question->getOptionA(), $question->getOptionB(), $question->getOptionC(), $question->getOptionD(), $optiona_bgcolor, $optionb_bgcolor, $optionc_bgcolor, $optiond_bgcolor, $correct_answer, $user_ans, $timeTaken, $clusterDesc, $response, $lowerLevel, $accessFromStudentInterface, $question->eeIcon, $trailType, $oneQuestionGroup, $quesAttemptSrno, $teacherComments);
		else
			showQuestion($line["qcode"],$line["sessionID"],$srno, $question->getQuestionForDisplay($eeresponse), $response, $questionType, $question->getOptionA(), $question->getOptionB(), $question->getOptionC(), $question->getOptionD(), $optiona_bgcolor, $optionb_bgcolor, $optionc_bgcolor, $optiond_bgcolor, $correct_answer, $user_ans, $timeTaken, $clusterDesc, $response, $lowerLevel, $accessFromStudentInterface, $question->eeIcon, $trailType);
		if($clusterType == "practice")
			echo '</div>';
		if($i % $groupColumn == 0 && $clusterType == "practice")
			echo '<div style="clear:both;"></div>';
		$i++;
		
	if($clusterType == "practice")
		echo '</div>';
		
	$time_q = mktime(substr($line['lastModified'],11,2),substr($line['lastModified'],14,2),substr($line['lastModified'],17,2),substr($line['lastModified'],3,2),substr($line['lastModified'],0,2),substr($line['lastModified'],6,4));

///--timed test,activities,prepost--added by chirag
	
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
						$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE userID=$student_userID AND class=$childClass AND mode='normal' AND quesAttempt_srno=".$line['srno'];
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
						$optiona_bgcolor="optionIncorrect";
					if($user_ans=="B")
						$optionb_bgcolor="optionIncorrect";
					if($user_ans=="C")
						$optionc_bgcolor="optionIncorrect";
					if($user_ans=="D")
						$optiond_bgcolor="optionIncorrect";
			
					if($correct_answer=="A")
						$optiona_bgcolor="optionCorrect";
					if($correct_answer=="B")
						$optionb_bgcolor="optionCorrect";
					if($correct_answer=="C")
						$optionc_bgcolor="optionCorrect";
					if($correct_answer=="D")
						$optiond_bgcolor="optionCorrect";
					$clusterDesc = "prePost";
					showQuestion($line["qcode"],$line["sessionID"],$srno, $question->getQuestion(), $response, $questionType, $question->getOptionA(), $question->getOptionB(), $question->getOptionC(), $question->getOptionD(), $optiona_bgcolor, $optionb_bgcolor, $optionc_bgcolor, $optiond_bgcolor, $correct_answer, $user_ans, $timeTaken, $clusterDesc, $response, $lowerLevel, $accessFromStudentInterface);
					
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
			$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE userID=$student_userID AND class=$childClass AND mode='normal' AND quesAttempt_srno=".$line['srno'];
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
	        $optiona_bgcolor="optionIncorrect";
	    if($user_ans=="B")
	        $optionb_bgcolor="optionIncorrect";
	    if($user_ans=="C")
	        $optionc_bgcolor="optionIncorrect";
	    if($user_ans=="D")
	        $optiond_bgcolor="optionIncorrect";

	    if($correct_answer=="A")
	        $optiona_bgcolor="optionCorrect";
	    if($correct_answer=="B")
	        $optionb_bgcolor="optionCorrect";
	    if($correct_answer=="C")
	        $optionc_bgcolor="optionCorrect";
	    if($correct_answer=="D")
        	$optiond_bgcolor="optionCorrect";
		showQuestion($line["qcode"],$line["sessionID"],$srno, $question->getQuestionForDisplay($eeresponse), $response, $questionType, $question->getOptionA(), $question->getOptionB(), $question->getOptionC(), $question->getOptionD(), $optiona_bgcolor, $optionb_bgcolor, $optionc_bgcolor, $optiond_bgcolor, $correct_answer, $user_ans, $timeTaken, $clusterDesc, $response, $lowerLevel, $accessFromStudentInterface, $question->eeIcon);
		$cqno++;
	}


if($clusterType != "practice")

$srno++;$rowno++;}  ?>

<input type="hidden" name="lastTopic" id="lastTopic" value="<?=$tmp_topic?>">
<input type="hidden" name="lastSessionID" id="lastSessionID" value="<?=$tmp_sessionNo?>">
<input type="hidden" name="lastCluster" id="lastCluster" value="<?=$tmp_cluster?>">
<input type="hidden" name="lowerLevel" id="lowerLevel" value="<?=$lowerLevel?>">

<div align="right" class="legend" style="width:98%"><br/><a href="#frmQuesTrail" onclick="setTimeout(function() {tryingToUnloadPage = false;},500);"><em>Top</em></a><br/></div>
<?php } }?>
</div>
</form>
<div id="commentBox">
	<div class="commentHeader">&nbsp;&nbsp;&nbsp;&nbsp;Comment</div>
    <div>
		<textarea name="commentText" id="commentText"></textarea>
	</div>
    <div>
		<input name="commentCancleBtn" id="commentCancleBtn" type="button" class="commentBtn" value="Close" onClick="toggleCommentBox(0)">&nbsp;&nbsp;
		<input name="commentSaveBtn" id="commentSaveBtn" class="commentBtn" type="button" value="Save" onClick="saveCommentBox()">&nbsp;&nbsp;
	</div>
</div>

</div>
</div>
<?php include("footer.php") ?>





<?php
function showQuestion($qcode,$sessionID, $srno, $question, $response, $questionType, $optionA, $optionB, $optionC, $optionD, $optiona_bgcolor, $optionb_bgcolor, $optionc_bgcolor, $optiond_bgcolor, $correct_answer, $user_ans, $timeTaken, $clusterDesc, $response, $lowerLevel, $accessFromStudentInterface, $eeIcon="0", $trailType="", $oneQuestionGroup=false, $quesAttemptSrno=0,$teacherComments="")
{
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
		$cls="correctMark";
	else if($response==0)
		$cls = "incorrectMark";
?>
	<div class='question'>
		<div class="<?=$cls?>"></div>
        <table width='90%' border=0 cellspacing=0>
            <tr  bgcolor="" >
                <td align='center' valign='top'  width='5%'><div id="<?=$quesType."_".$qcode."_".$sessionID."_".$srno?>" class="qno<?php if($trailType == "ncert" && !$oneQuestionGroup) { echo " questionQ1"; $srno = "(".$srno.")";}?>"><?=$srno?></div><br/>
				<?php
					if($eeIcon == "1")
						echo '<div class="eqEditorToggler" align="center"></div>';
				?>
                </td>
                <td align='left'><?=$question?><br/></td>
            </tr>
<?php
    if($questionType=='MCQ-4' || $questionType=='MCQ-3' || $questionType=='MCQ-2')    {
?>
            <tr  bgcolor="">
                <td>&nbsp;</td>
                <td>
                <table width="100%" border="0" cellspacing="2" cellpadding="3">

    <?php     if($questionType=='MCQ-4' || $questionType=='MCQ-2')    {    ?>
                <tr valign="center">
                    <td width="5%" nowrap align="left" ><div class="option <?=$optiona_bgcolor?>"><b>A</b></div></td>
                    <td width="45%" class='optionText'><?php echo $optionA;?></td>
                    <td width="5%" nowrap align="left" ><div class="option <?=$optionb_bgcolor?>"><b>B</b></div></td>
                    <td width="45%" class='optionText'><?php echo $optionB;?></td>
                </tr>
    <?php    }    ?>
    <?php    if($questionType=='MCQ-4')    {    ?>
                <tr valign="center">
                    <td width="5%" align="left"><div class="option <?=$optionc_bgcolor?>"><b>C</b></div></td>
                    <td width="45%" class='optionText'><?php echo $optionC;?></td>
                    <td width="5%" align="left"><div class="option <?=$optiond_bgcolor?>"><b>D</b></div></td>
                    <td width="45%" class='optionText'><?php echo $optionD;?></td>
                </tr>
    <?php    }    ?>
    <?php    if($questionType=='MCQ-3')    {    ?>
                <tr valign="center">
                	<td width="3%" nowrap align="left"><div class="option <?=$optiona_bgcolor?>"><b>A</b></div></td>
                    <td width="30%" class='optionText'><?php echo $optionA;?></td>
                    <td width="3%" nowrap align="left"><div class="option <?=$optionb_bgcolor?>"><b>B</b></div></td>
                    <td width="30%" class='optionText'><?php echo $optionB;?></td>
                    <td width="3%" nowrap align="left"><div class="option <?=$optionc_bgcolor?>"><b>C</b></div></td>
                    <td width="30%" class='optionText'><?php echo $optionC;?></td>
                </tr>
            <?php    }    ?>
                  </table>
                </td>
            </tr>

<?php    } ?>


        </table>


        <?php
        	if($clusterDesc =="Challenge Question")
        		$clusterDesc = "<strong>".$clusterDesc."</strong>";
        	else
        	{
				$type = ($trailType == "ncert")?"Exercise":"Learning unit";
        		$clusterDesc = "<span class='title2'>$type: </span> <span style='color:#2F99CB;'>".$clusterDesc."</span>";
        		if($lowerLevel)
        			$clusterDesc .= '<img src="assets/red_star.gif" alt="Red Star" height="20" width="20">';
        	}

        ?>
         <?php if($trailType=="ncert") {?>
        <div class="desk_block">
            <strong>Comments:</strong>
            <div id="comments_<?=$quesAttemptSrno?>">
                <?=$teacherComments?>
            </div>
        </div>
        <?php }?>
		<div class="desk_block">
			<div class="block mid_repeat">
				<div class="block_header">
					<div>
									<span>User Response: </span>&nbsp;&nbsp;
									<span class="title"><?=$user_ans;?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<?php if($correct_answer!="")    {    ?><span>Correct answer: </span><span class="title"><?=$correct_answer?></span>&nbsp;&nbsp;&nbsp;&nbsp;<?php }  ?>
									<span>Time taken: </span><span class="title"><?=$timeTaken?> secs</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								</div>
					<br/>
					<div> <?=$clusterDesc?>
                        <?php if(!$accessFromStudentInterface && $trailType=="ncert") {?>
                        &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" onClick="toggleCommentBox('<?=$quesAttemptSrno?>')">Comment</a>
                        <?php }?> </div>
					<br/>
					<br/>
				</div>
			</div>
		</div>
		
    </div>
<?php
}

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
	$cq_query = "SELECT qcode,A,S,R,ttAttemptID,questionNo
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

function getTimedTestAttemptedInSession($userID,$sessionID)
{
	$timedTestArray = array();
	$query = "SELECT a.timedTestCode, description, quesCorrect, timeTaken, perCorrect, noOfQuesAttempted, date_format(b.lastModified, '%d-%m-%Y %H:%i:%s') lastModified,linkedToCluster
	          FROM   adepts_timedTestMaster a, adepts_timedTestDetails b
	          WHERE  a.timedTestCode=b.timedTestCode AND userID=$userID AND sessionID=$sessionID";
	$result = mysql_query($query) or die("Error in fetching timed test details!");
	$srno = 0;
	while ($line = mysql_fetch_array($result))
	{
		$timedTestArray[$srno]["timedTestCode"] = $line[0];
		$timedTestArray[$srno]["description"] = $line[1];
		$timedTestArray[$srno]["quesCorrect"] = $line[2];
		$timedTestArray[$srno]["timeTaken"] = $line[3];
		$timedTestArray[$srno]["perCorrect"] = $line[4];
		$timedTestArray[$srno]["noOfQuesAttempted"] = $line[5];
		$timedTestArray[$srno]["attemptedOn"] = $line[6];
		$timedTestArray[$srno]["cluster"] = $line[7];
		$srno++;
	}
	return $timedTestArray;
}

function getGamesAttemptedInSession($userID,$sessionID)
{
	$gamesArray = array();
	$query = "SELECT a.gameID, a.gameDesc, score, timeTaken, gameLevel, date_format(b.lastModified, '%d-%m-%Y %H:%i:%s') lastModified, linkedToCluster
	          FROM   adepts_gamesMaster a, adepts_userGameDetails b
	          WHERE  a.gameID=b.gameID AND userID=$userID AND sessionID=$sessionID";
	$result = mysql_query($query) or die("Error in fetching timed test details!");
	$srno = 0;
	while ($line = mysql_fetch_array($result))
	{
		$gamesArray[$srno]["gameID"] = $line[0];
		$gamesArray[$srno]["description"] = $line[1];
		$gamesArray[$srno]["score"] = $line[2];
		$gamesArray[$srno]["timeTaken"] = $line[3];
		$gamesArray[$srno]["level"] = $line[4];
		$gamesArray[$srno]["attemptedOn"] = $line[5];
		$gamesArray[$srno]["cluster"] = $line[6];
		$srno++;
	}
	return $gamesArray;
}

function getRemedialItemAttempts($userID, $sessionID)
{
	$remedialItemAttemptsArray = array();
	$query = "SELECT a.remedialItemCode, a.remedialItemDesc, result, timeTaken, date_format(b.lastModified, '%d-%m-%Y %H:%i:%s') lastModified
	          FROM   adepts_remedialItemMaster a, adepts_remedialItemAttempts b
	          WHERE  a.remedialItemCode=b.remedialItemCode AND userID=$userID AND sessionID=$sessionID";
	$result = mysql_query($query) or die("Error in fetching timed test details!");
	$srno = 0;
	while ($line = mysql_fetch_array($result))
	{
		$remedialItemAttemptsArray[$srno]["remedialItemCode"] = $line[0];
		$remedialItemAttemptsArray[$srno]["description"] = $line[1];
		$remedialItemAttemptsArray[$srno]["result"] = $line[2];
		$remedialItemAttemptsArray[$srno]["timeTaken"] = $line[3];
		$remedialItemAttemptsArray[$srno]["attemptedOn"] = $line[4];
		$srno++;
	}
	return $remedialItemAttemptsArray;
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
?>
