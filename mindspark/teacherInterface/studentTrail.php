<?php
	set_time_limit(0);
	error_reporting(E_ERROR);
	include("header.php");
	//include("../slave_connectivity.php");
    include("../userInterface/functions/orig2htm.php");
    include("../userInterface/classes/clsQuestion.php");
    include("../userInterface/classes/clsResearchQuestion.php");
	include("../userInterface/functions/functionsForDynamicQues.php");
	include("../userInterface/classes/clsTimedTest.php");
    include("../userInterface/classes/clsNCERTQuestion.php");
	include("../userInterface/constants.php");
    include("classes/eipaging.cls.php");		//this is path on internet
    include_once("../userInterface/classes/clsTeacherTopic.php");
    include("../userInterface/classes/clsDiagnosticTestQuestion.php");
	include("functions/functions.php");
    $userID      = $_SESSION['userID'];
    $schoolCode  = $_SESSION['schoolCode'];
    $category    = $_SESSION['admin'];
    $subcategory = $_SESSION['subcategory'];
    $accessFromStudentInterface = isset($_POST['accessFromStudentInterface'])?$_POST['accessFromStudentInterface']:0;	//This will be set to 1 if accessed from student interface reports section
    if(strcasecmp($category,"School Admin")!=0 && strcasecmp($category,"Teacher")!=0 && strcasecmp($category,"Home Center Admin")!=0 && !$accessFromStudentInterface)
	{
		echo "You are not authorised to access this page!";
		exit();
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
		
	$flagSet=0;
	$schoolCodeArray = array();
  	$coteacherInterfaceFlag = 0;
    $query  = "SELECT schoolCode from adepts_rewardSystemPilot where flag=2";
    $result = mysql_query($query) or die(mysql_error());
    while($line   = mysql_fetch_array($result))
    {
       $schoolCodeArray[] =$line[0];
    }
    if(in_array($schoolCode,  $schoolCodeArray) || empty($schoolCodeArray))
    {          
    	$coteacherInterfaceFlag = 1;
    } 
?>
<title>Mindspark -
<?php if($errorReportingFlag==1)
			echo "Error Reporting";
		else
			echo "Student Trail";
?>
</title>
<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/myClasses.css?ver=2" rel="stylesheet" type="text/css">
<link href="css/studentTrail.css?ver=2" rel="stylesheet" type="text/css">
<!-- <script src="libs/jquery.js"></script> -->
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script src="libs/idletimeout.js" type="text/javascript"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script>
	var langType = '<?=$language;?>';
	function load(){
		var fixedSideBarHeight = window.innerHeight;
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#fixedSideBar").css("height",fixedSideBarHeight+"px");
		$("#sideBar").css("height",sideBarHeight+"px");
		$("#students").css("font-size","1.4em");
		$("#students").css("margin-left","40px");
		$(".arrow-right-yellow").css("margin-left","10px");
		$(".rectangle-right-yellow").css("display","block");
		$(".arrow-right-yellow").css("margin-top","3px");
		$(".rectangle-right-yellow").css("margin-top","3px");
	}
	function lastPage(){
		alert("This is the last page!");
	}
	function firstPage(){
		alert("This is the first page!");
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

	function submitTeacherCommentForm(qcode, type, qno, sessionID, childClass,quesAttemptSrno)
	{
		document.getElementById("qcode").value = qcode;
		document.getElementById("type").value = type;
		document.getElementById("qno").value = qno;
		document.getElementById("sessionID").value = sessionID;
		document.getElementById("childClass").value = childClass;
		document.getElementById("quesAttemptSrno").value = quesAttemptSrno;
		document.getElementById("teacherComment").submit();
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
		$.post("../userInterface/controller.php","mode=saveNcertTeacherComment&srno="+questionSrno+"&comment="+comment+"",function(data){
			
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

	/*Begin Condition for the task 11267 */
	<?php
		if($_POST['whichClick'] == 'checkBoxClick')
		{
			if($_POST['last1hour']==1) 
				echo "$('#childNameLast1Hour').attr('checked','checked');";
			else
				echo "$('#childNameLast1Hour').removeAttr('checked');";			
		} 
		else if($_POST['whichClick'] == 'btnGoClick')
		{ ?>
			if($('#childNameLast1Hour').attr('checked'))
			{
				$('#last1hour').val('1');
			}
			else
			{
				$('#last1hour').val('0');
			}
	<?php }
	?>	
	/*End */
    $("#childNameLast1Hour").click(function()
    {
    	if($("#childName").val() != "")
    	{
	    	$("#clickedFlag").val("1");					// for task 11267
	    	$("#whichClick").val('checkBoxClick');		// for task 11267
			if($(this).is(":checked")){
				$("#last1hour").val('1');				// for task 11267
				setTryingToUnload();
				$("#frmQuesTrail").attr("action","/mindspark/teacherInterface/studentTrail.php?mode=errorReporting&last1hour=1");
				$("#btnGo").trigger('click');			// for task 11267
			}			
			else{	
				$("#last1hour").val('0');				// for task 11267
				setTryingToUnload();
				$("#frmQuesTrail").attr("action","/mindspark/teacherInterface/studentTrail.php?mode=errorReporting&last1hour=0");
				$("#btnGo").trigger('click');			// for task 11267
			}    		
    	}
    	else
    	{
    		if($(this).is(":checked"))
    		{
    			$("#last1hour").val('1');
    		}
    		else
    		{
    			$("#last1hour").val('0');
    		}
    	}
	});
	
	<?php if($errorReportingFlag==1) { ?>
		$("#ncertRadio").hide();
		$(".qno").css("cursor","pointer");
		$(".qno").click(function() { 
			var errorIDArr	=	$(this).attr("id").split("_");
			var type = errorIDArr[0];
			if(errorIDArr[3] == 'WC')
				type = "wildcard_"+errorIDArr[0];
			if(!errorIDArr[3])
				errorIDArr[3]="";
			setTryingToUnload();
			submitTeacherCommentForm(errorIDArr[1], type, errorIDArr[3], errorIDArr[2], errorIDArr[4], errorIDArr[5]);
		});
	<?php 
		} 
		if($_POST['clickedFlag'] == 1 && isset($_POST['childName']))	// for task 11267
		{
			echo "$('#clickedFlag').val('0');";
		}
	?>

	$("#dailyPractice").on("change",function(){
		var statusId = $("#dailyPractice").val();
		if(statusId != "")
		{
			$("#dailyPracticeSessionID").val("");
			$("#dailyPracticeSessionID option").hide();
			$("#dailyPracticeSessionID option[data='"+statusId+"']").show();
		}
		else
		{
			$("#dailyPracticeSessionID").val("");
			$("#dailyPracticeSessionID option").show();
		}
	});
	$("#assessment").on("change",function(){
		var statusId = $("#assessment").val();
		if(statusId != "")
		{
			$("#assessmentSessionID").val("");
			$("#assessmentSessionID option").hide();
			$("#assessmentSessionID option[data='"+statusId+"']").show();
		}
		else
		{
			$("#assessmentSessionID").val("");
			$("#assessmentSessionID option").show();
		}
	});
});

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
</script>
<?php

    $child_All_List=array();
    $cUserID="";
    //fill initial data
	/*if($_REQUEST["last1hour"]==1)		// removed condition for the task 11267
	{
		$sessionTime = date("Y-m-d H:i:s", strtotime('-60 minutes'));
		$query = "SELECT DISTINCT A.childName, A.childClass,A.userID, A.childSection 
				  FROM   adepts_userDetails A , adepts_sessionStatus B 
				  WHERE  A.userID=B.userID AND B.startTime_int = ".date("Ymd")." AND B.startTime > '$sessionTime'  AND A.category='STUDENT' AND subcategory='SCHOOL' AND enabled=1 
				  AND schoolcode=$schoolCode AND subjects like '%".SUBJECTNO."%'
				  ORDER BY childName";
	}
    else */if (strcasecmp($category,"Home Center Admin")==0)
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
$topic = $dailyPractice = $childName = $childClass  = $student_userID = $sessionIDStr = $assessment= "" ;

$topicsAttempted =$exerciseAttempted =$dailyPracticesAttempted = $sessions = $sessionID = $startTime = $dailyPracticeSessions = $dailyPracticeStartTime = $dailyPracticeStatusIdArrForSessions = $assessmentSessions = $assessmentStartTime= $assessmentStatusIdArrForSessions = array();
if(isset($_REQUEST['sessionID']))
{
    $sessionID = $_REQUEST['sessionID'];
}
else if (isset($_GET['session_passed_id']) && $_GET['session_passed_id'])
{
    $sessionID = $_GET['session_passed_id'];
    $bypass_flag=1;
}
if(isset($_REQUEST['dailyPracticeSessionID']))
{
    $dailyPracticeSessionID = $_REQUEST['dailyPracticeSessionID'];
}
if(isset($_REQUEST['assessmentSessionID']))
{
    $assessmentSessionID = $_REQUEST['assessmentSessionID'];  
}
else if (isset($_GET['session_passed_id']) && $_GET['session_passed_id'])
{
    $dailyPracticeSessionID = $_GET['session_passed_id'];
    $daily_practice_bypass_flag=1;
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
	$trailType = 'regular';
    $topic = $_GET['topic_passed_id'];
}
if(isset($_REQUEST['assessment']))
{
	$trailType = 'assessment';
    $assessment = $_REQUEST['assessment'];
}
if(isset($_REQUEST['dailyPractice']))
{
    $dailyPractice = $_REQUEST['dailyPractice'];
}
else if(isset($_GET['practice_passed_id']) && $_GET['practice_passed_id'] != "" )
{
	$trailType = 'dailyPractice';
    $dailyPractice = $_GET['practice_passed_id'];
}
if(isset($_REQUEST['exercise']) && $_REQUEST['exercise']!="")
{
	$trailType = 'ncert';
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
	$_SESSION['selectedChildClass'] = $childClass;
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
$dailyPracticeSessionIDStr=$dailyPracticeSessionID;


if($childName!="")
{
    $query  = "SELECT distinct a.teacherTopicCode, b.teacherTopicDesc,b.customTopic,b.parentTeacherTopicCode,c.flow
               FROM   ".TBL_TOPIC_STATUS." a, adepts_teacherTopicMaster b, adepts_teacherTopicActivation c
               WHERE  userID=$student_userID AND a.teacherTopicCode = b.teacherTopicCode AND b.teacherTopicCode=c.teacherTopicCode group by b.teacherTopicCode ORDER BY classification";  

    $result = mysql_query($query) or die("Error1");
    $srno = 0;
    while ($line   = mysql_fetch_array($result))
    {
        $topicsAttempted[$srno][0] = $line[0];
        $topicsAttempted[$srno][1] = $line[1];  
        if( $coteacherInterfaceFlag == 1)     
        	$topicsAttempted[$srno][2] = checkForCoteacherTopic($line[0],$childClass,$line[2],$line[3],$line[4]);
        $srno++;
    }   

    $query  = "SELECT sessionID,date_format(startTime, '%d-%m-%Y %H:%i:%s') as startTime FROM ".TBL_SESSION_STATUS." WHERE userID=$student_userID";
	if($errorReportingFlag==1)
	{
		if($_POST["last1hour"]==1)
			$sessionTime = date("Y-m-d H:i:s", strtotime('-60 minutes'));
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

	if( $coteacherInterfaceFlag == 1) 
	{
		
	    $srno = 0;
	    foreach($topicsAttempted as $topics)
	    {
	    	if($topics[2])
	    	{
	    		$query = "SELECT GROUP_CONCAT(a.attemptID) as assessmentAttemptIds from adepts_diagnosticTestAttempts a JOIN adepts_teacherTopicStatus b ON b.ttAttemptID=a.ttAttemptID JOIN adepts_diagnosticTestMaster c ON c.diagnosticTestID=a.diagnosticTestID where b.teacherTopicCode = '$topics[0]' and a.userID=$student_userID and c.testType = 'Assessment'";
	    		// echo $query;
	    		$result = mysql_query($query) or die("Error1");
			    
			    while ($line = mysql_fetch_array($result))
			    {
			    	if($line[0] != '')
			    	{		    		
				    	$assessmentTopic[$srno][0] = $line[0];
				    	$assessmentTopic[$srno][1] = $topics[1];
				    	$assessmentTopic[$srno][2] = $topics[0];
				    	$srno++;
			    	}
			    }
	    	}
	    }  
	    // make list of sessions of Assessment
	    $query = "SELECT DISTINCT a.sessionID as assessmentSessionID,GROUP_CONCAT(DISTINCT d.attemptID) as assesmentStatusIds from adepts_diagnosticQuestionAttempt a JOIN adepts_diagnosticTestMaster b ON a.diagnosticTestID=b.diagnosticTestID JOIN adepts_comprehensiveModuleAttempt c  ON c.srno=a.attemptID JOIN adepts_diagnosticTestAttempts d ON d.srno=c.srno where b.testType='Assessment' and a.sessionID IN(".implodeArrayForQuery($sessions).") GROUP BY a.sessionID ORDER BY assessmentSessionID DESC";       
	    
	     $result = mysql_query($query) or die("Error");
	    while ($line   = mysql_fetch_array($result))
	    {	
	    	$index = array_search($line[0], $sessions);
	    	array_push($assessmentSessions,$line[0]);
	    	array_push($assessmentStartTime,$startTime[$index]);
	    	array_push($assessmentStatusIdArrForSessions, $line[1]);  
	    }   
	}
    // make assesment array

    $query = "SELECT a.id as dailyPracticeStatusId,a.practiseModuleId as dailyPracticeId,b.description as dailyPracticeName from practiseModulesTestStatus a, practiseModuleDetails b where a.practiseModuleId=b.practiseModuleId and a.userID= $student_userID order by dailyPracticeId, a.lastModified asc";
    $result = mysql_query($query) or die("Error1");
    $srno = 0;
    $attemptNo = 2;
    $dailyPracticeNameToShow = "";
    $dailyPracticeAttemptCheckArr = array();
    $dailyPracticeStatusIdStr = "";
    while ($line   = mysql_fetch_array($result))
    {
    	$dailyPracticeStatusIdStr .= $line[0].",";
    	if(!in_array($line['dailyPracticeId'], $dailyPracticeAttemptCheckArr, true))
    	{
			$attemptNo = 2;
    		array_push($dailyPracticeAttemptCheckArr, $line['dailyPracticeId']);
    		$dailyPracticeNameToShow = $line[2];
    	}
    	else
    	{
    		$dailyPracticeNameToShow = $line[2]." - Attempt ".$attemptNo;
    		$attemptNo++;
    	}
        $dailyPracticesAttempted[$srno]['attemptNo'] = $line[0];
        $dailyPracticesAttempted[$srno]['attemptName'] = $dailyPracticeNameToShow;
        $attemptNames[$srno] = $dailyPracticeNameToShow;
        $srno++;
    }
    $dailyPracticeStatusIdStr = substr($dailyPracticeStatusIdStr,0,-1);
	array_multisort($attemptNames, SORT_ASC, $dailyPracticesAttempted);

    $query  = "(SELECT distinct(a.sessionID) as dailyPracticeSessionID, a.practiseModuleTestStatusId as dailyPracticeStatusID from practiseModulesQuestionAttemptDetails a where a.sessionID in(".implodeArrayForQuery($sessions).") ORDER BY dailyPracticeSessionID DESC) UNION ALL (SELECT distinct(a.sessionID) as dailyPracticeSessionID, a.practiseModuleTestStatusId as dailyPracticeStatusID from practiseModulesTimedTestAttempt a where a.sessionID in(".implodeArrayForQuery($sessions).") ORDER BY dailyPracticeSessionID DESC)";

    $result = mysql_query($query) or die("Error");
    while ($line   = mysql_fetch_array($result))
    {
    	if(!in_array($line[0], $dailyPracticeSessions))
    	{
    		$index = array_search($line[0], $sessions);
	        array_push($dailyPracticeSessions,$line[0]);
	        array_push($dailyPracticeStartTime,$startTime[$index]);
	        array_push($dailyPracticeStatusIdArrForSessions, $line[1]);    		
    	}
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
    $('#whichClick').val('btnGoClick');		// for task 11267
    /*$("#frmQuesTrail").attr("action","/mindspark/teacherInterface/studentTrail.php?mode=errorReporting&last1hour="+$("#last1hour").val());*/		// for the mantis task 12544
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
	setTryingToUnload();
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
	else if(selectedRadio == "regular")
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
	else if(selectedRadio == "assessment")
	{
		var topic = document.getElementById('assessment').value;
		var noOfSessions = document.getElementById('assessmentSessionID');
		var sessionID= 0;
		for(var i=1; i<noOfSessions.length; i++)
		{
			if(noOfSessions[i].selected)
			{
				sessionID=1;
				break;
			}
		}
		if(topic=="")
		{
			alert("Please select a topic.");
		}
		else
		{
			setTryingToUnload();
			document.getElementById('frmSelect').submit();
		}
	}
	else
	{
		var topic = document.getElementById('dailyPractice').value;
		var noOfSessions = document.getElementById('dailyPracticeSessionID');
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
		$("#"+selectedRadio+",#commonDiv").css("display","block");
		$("#regularTopicLabel,#regularTopicSelection,#regularLine,#regularLine1,#regularSessionLabel,#regularSessionSelection").css("display","none");
		$("#dailyPracticeTopicLabel,#dailyPracticeTopicSelection,#dailyPracticeLine,#dailyPracticeLine1,#dailyPracticeSessionLabel,#dailyPracticeSessionSelection").css("display","none");
		$("#assessmentTopicLabel,#assessmentTopicSelection,#assessmentLine,#assessmentSessionLabel,#assessmentSessionSelection").css("display","none");
		$("#ncert,#ncert1").css("display","table-cell");
	}
	else if(selectedRadio == "regular")
	{
		$("#"+selectedRadio+",#commonDiv").css("display","block");
		$("#regularTopicLabel,#regularTopicSelection,#regularLine,#regularLine1,#regularSessionLabel,#regularSessionSelection").css("display","table-cell");
		$("#regularLine,#regularLine1").css({"border-right":"1px solid #626161"});
		$("#dailyPracticeTopicLabel,#dailyPracticeTopicSelection,#dailyPracticeLine,#dailyPracticeLine1,#dailyPracticeSessionLabel,#dailyPracticeSessionSelection").css("display","none");
		$("#assessmentTopicLabel,#assessmentTopicSelection,#assessmentLine,#assessmentSessionLabel,#assessmentSessionSelection").css("display","none");
		$("#ncert,#ncert1").css("display","none");			
	}
	else if(selectedRadio == "dailyPractice")
	{
		$("#"+selectedRadio+",#commonDiv").css("display","block");
		$("#regularTopicLabel,#regularTopicSelection,#regularLine,#regularLine1,#regularSessionLabel,#regularSessionSelection").css("display","none");
		$("#dailyPracticeTopicLabel,#dailyPracticeTopicSelection,#dailyPracticeLine,#dailyPracticeLine1,#dailyPracticeSessionLabel,#dailyPracticeSessionSelection").css("display","table-cell");
		$("#assessmentTopicLabel,#assessmentTopicSelection,#assessmentLine,#assessmentSessionLabel,#assessmentSessionSelection").css("display","none");
		$("#dailyPracticeLine,#dailyPracticeLine1").css({"border-right":"1px solid #626161"});
		$("#ncert,#ncert1").css("display","none");
	}
	else if(selectedRadio == "assessment")
	{
		$("#"+selectedRadio+",#commonDiv").css("display","block");
		$("#regularTopicLabel,#regularTopicSelection,#regularLine,#regularLine1,#regularSessionLabel,#regularSessionSelection").css("display","none");
		$("#dailyPracticeTopicLabel,#dailyPracticeTopicSelection,#dailyPracticeLine,#dailyPracticeLine1,#dailyPracticeSessionLabel,#dailyPracticeSessionSelection").css("display","none");
		$("#assessmentTopicLabel,#assessmentTopicSelection,#assessmentLine,#assessmentSessionLabel,#assessmentSessionSelection").css("display","table-cell");
		$("#assessmentLine").css({"border-right":"1px solid #626161"});
		$("#ncert,#ncert1").css("display","none");
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
	<?php include("topBar.php") ?>
</div>
<div id="sideBar">
	<?php include("sideBar.php") ?>
</div>
<div id="container">
<?php if($errorReportingFlag==1){ ?>
<table id="childDetails" style="float:none;">
				<td width="33%" id="sectionRemediation" class="activatedTopic"><a href="Comments.php" style="text-decoration:none;"><div id="actTopicCircle1" class="smallCircle" style="cursor:pointer;"></div><div id="1" style="cursor:pointer;" class="pointer">Comments</div></a></td>
		        <td width="40%" id="studentRemediation" class="activateTopicAll"><div id="actTopicCircle2" class="smallCircle red" style="cursor:pointer;"></div><div id="2" style="cursor:pointer;" class="pointer textRed">Error reporting</div></td>
			</table>
<?php } ?>
<div id="trailContainer">
<div id="headerBar">
	<div id="pageName">
		<div class="arrow-black"></div>
		<div id="pageText">
			<?php if($errorReportingFlag==1)	echo "Error reporting";	else echo "STUDENT TRAIL";?>
		</div>
		<?php if($errorReportingFlag==1){ ?>
		<a href="teacherCommentReport.php" style="text-decoration:none;">
		<div id="add">
				<div id="circle">
				<div id="plushorizontal"> </div>
				<div id="plusVertical"> </div>
				 </div>
				 
				 
			</div>
			
			<span id="addComment">View Errors Reported</span></a>
			<div style="clear:both"></div>
		<?php } ?>
	</div>
</div>
<form method="POST" id="frmQuesTrail" autocomplete='off'>
	<input type="hidden"  id="student_userID" name="student_userID" value="">
	<table align="center" border="0" id="pnlSelect">
		<tr>
			<td width="15%" id="childNameDiv"><label for="childName">Child Name</label></td>
			<td width="30%"><input type="text" name="childName" id="childName" value="<?=$childName?>" <?php if($childName=="") echo " disabled" ?> autocomplete="off" size="30"></td>
			<td><img src="image/load.gif" id="imgChildNameLoading"  align="Loading" style="display:none;"></td>
			<?php if($errorReportingFlag==1) { ?>
			<td width="14%"><label for="childNameLast1Hour">
					<!--Begin These 3 hidden inputs are added and check box is placed inbetween the text box and btn for task 11267 -->
					<input type='hidden' id='clickedFlag' name='clickedFlag' value='0'/>
					<input type='hidden' id='last1hour' name='last1hour' value='1'/>
					<input type='hidden' id='whichClick' name='whichClick'/>
					<!-- End -->
					<input type="checkbox" value="" name="childNameLast1Hour" id="childNameLast1Hour" <?php if($_REQUEST['last1hour'] == '1')echo 'checked'; ?>>
					Last 1 hour</label>
				&nbsp;&nbsp;&nbsp;</td>
			<?php }?>
			<td width="50%" id="buttonDiv"><input type="submit" class="button" name="btnGo" id="btnGo" value="<?php if($errorReportingFlag==1) echo "Search"; else echo "Go"?>" onClick="return validate();"></td>
		</tr>
	</table>
</form>

<form method="POST" id="teacherComment" name="teacherComment" action="teacherComment.php">
	<input type="hidden" name="qcode" id="qcode"/>
	<input type="hidden" name="type" id="type"/>
	<input type="hidden" name="qno" id="qno"/>
	<input type="hidden" name="sessionID" id="sessionID"/>
	<input type="hidden" name="childClass" id='childClass'/>
	<input type="hidden" name="quesAttemptSrno" id='quesAttemptSrno'/>
</form>

<form id="frmSelect" method="post">
<input type="hidden" name="childName" value="<?=$childName?>">
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
		<td valign="top" width="65%" id="ncertRadio" style="border-right:1px solid #626161">
			<label>
				<input type="radio" name="trailType" value="ncert" id="trailType_0" <?php if($trailType == "ncert") echo "checked"; ?> onChange="toggleOption()">
				NCERT Exercises</label>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<label>
				<input type="radio" name="trailType" value="regular" id="trailType_1" <?php if($trailType == "regular") echo "checked"; ?> onChange="toggleOption()">
				Regular</label>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<?php if($childClass>=4 && $childClass<=7)
			{?>
			<label>
				<input type="radio" name="trailType" value="dailyPractice" id="trailType_2" <?php if($trailType == "dailyPractice") echo "checked"; ?> onChange="toggleOption()">
				Daily Practice</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<?php }?>
			<?php if($coteacherInterfaceFlag == 1){?>
				<label>
					<input type="radio" name="trailType" value="assessment" id="trailType_3" <?php if($trailType == "assessment") echo "checked"; ?> onChange="toggleOption()">
					Assessment</label>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<?php } ?>
		</td>
		<td>
			<input type="checkbox" name="chkWrongAns" id="chkWrongAns" onClick="submitPage();" <?php if($showWrongAns) echo " checked";?>>
				<label for="chkWrongAns">Show only wrong answers</label>
		</td>
		<td></td>
	</table>
	<table id="selectTopicTbl" align="center" border="0" width="100%" <?php if($errorReportingFlag==1) echo "style='display:none'";?>>
		<td id="regularTopicLabel" <?php if($trailType != "regular") echo 'style="display:none;"'; ?>  width="10%">
			<label for="lstTopic">Topic:</label>
		</td>
		<td width="40%" id="regularTopicSelection" <?php if($trailType != "regular") echo 'style="display:none;"'; ?>>
			<select name="topic" id="lstTopic" style="width:80%;">
				<option value="">Select</option>
				<?php
				for($i=0; $i<count($topicsAttempted); $i++)
				{
				echo "<option value='".$topicsAttempted[$i][0]."'";
				if($topicsAttempted[$i][0]==$topic)
				echo " selected";
				echo ">".$topicsAttempted[$i][1]."</option>";
				}
				?>
			</select>
		</td>
		<td align="left" colspan="2" id="regularLine" <?=$trailType != 'regular';?> <?php if($trailType != "regular") echo 'style="display:none";'; else echo 'style="border-right:1px solid #626161"';  ?>></td>
		<td width="20%" id="regularSessionLabel" <?php if($trailType != "regular") echo 'style="display:none;"'; ?>>
			<label for="lstSessionID">Session ID: </label></td>
		<td id="regularSessionSelection" width="40%" <?php if($trailType != "regular") echo 'style="display:none;"'; ?>>
			<select name="sessionID[]" id="lstSessionID" >
				<option value="">Select</option>
				<?php
				for($i=0; $i<count($sessions); $i++)
				{
				echo "<option value='".$sessions[$i]."'";
				if((in_array($sessions[$i],$sessionID)) || ($bypass_flag == 1 && $sessionIDStr==$sessions[$i]))
				
				echo " selected";
				echo ">".$sessions[$i]." (".$startTime[$i].")</option>";
				}
				?>
			</select>
		</td>
		<td align="left" colspan="2" id="regularLine1" <?php if($trailType != "regular") echo 'style="display:none;"'; else echo 'style="border-right:1px solid #626161"';  ?>></td>

		<td <?php if($trailType != "ncert") echo 'style="display:none;"'; ?> id="ncert" width="10%">
			<label for="lstExercise">Exercise:</label>
		</td>
		<td width="40%" id="ncert1" <?php if($trailType != "ncert") echo 'style="display:none;"'; ?>>
			<select name="exercise" id="lstExercise" style="width:80%;">
				<option value="">Select</option>
				<?php
				for($i=0; $i<count($exerciseAttempted); $i++)
				{
				echo "<option value='".$exerciseAttempted[$i][0]."'";
				if($exerciseAttempted[$i][0]==$exercise)
				echo " selected";
				echo ">".$exerciseAttempted[$i][1]."</option>";
				}
				?>
			</select>
		</td>

		<td id="dailyPracticeTopicLabel" <?php if($trailType != "dailyPractice") echo 'style="display:none;"'; ?>  width="10%">
			<label for="dailyPractice">Daily Practice:</label>
		</td>
		<td width="40%" id="dailyPracticeTopicSelection" <?php if($trailType != "dailyPractice") echo 'style="display:none;"'; ?>>
			<select name="dailyPractice" id="dailyPractice" style="width:80%;">
				<option value="">Select</option>
				<?php
				for($i=0; $i<count($dailyPracticesAttempted); $i++)
				{
				echo "<option value='".$dailyPracticesAttempted[$i]['attemptNo']."'";
				if($dailyPracticesAttempted[$i]['attemptNo']==$dailyPractice)
				echo " selected";
				echo ">".$dailyPracticesAttempted[$i]['attemptName']."</option>";
				}
				?>
			</select>
		</td>
		<td align="left" colspan="2" id="dailyPracticeLine" <?=$trailType != 'dailyPractice';?> <?php if($trailType != "dailyPractice") echo 'style="display:none";'; else echo 'style="border-right:1px solid #626161"';  ?>></td>
		<td width="20%" id="dailyPracticeSessionLabel" <?php if($trailType != "dailyPractice") echo 'style="display:none;"'; ?>>
			<label for="dailyPracticeSessionID">Session ID: </label></td>
		<td id="dailyPracticeSessionSelection" width="40%" <?php if($trailType != "dailyPractice") echo 'style="display:none;"'; ?>>
			<select name="dailyPracticeSessionID" id="dailyPracticeSessionID" >
				<option value="">Select</option>
				<?php
				for($i=0; $i<count($dailyPracticeSessions); $i++)
				{
					$display = $dailyPractice != "" ? ($dailyPracticeStatusIdArrForSessions[$i]==$dailyPractice)?'block':'none':'block';
				echo "<option style='display:".$display.";' data='".$dailyPracticeStatusIdArrForSessions[$i]."' value='".$dailyPracticeSessions[$i]."'";
				if(($dailyPracticeSessions[$i]==$dailyPracticeSessionID) || ($daily_practice_bypass_flag == 1 && $dailyPracticeSessionIDStr==$dailyPracticeSessions[$i]))				
					echo " selected";

				if($dailyPracticeStartTime[$i] != "" || $dailyPracticeStartTime[$i] != null)
					echo ">".$dailyPracticeSessions[$i]." (".$dailyPracticeStartTime[$i].")</option>";
				else
					echo ">".$dailyPracticeSessions[$i]."</option>";
				}
				?>
			</select>
		</td>
		<td id="assessmentTopicLabel" <?php if($trailType != "assessment") echo 'style="display:none;"'; ?>  width="10%">
			<label for="assessmentTopic">Topic:</label>
		</td>
		<td width="40%" id="assessmentTopicSelection" <?php if($trailType != "assessment") echo 'style="display:none;"'; ?>>
			<select name="assessment" id="assessment" style="width:80%;">
				<option value="">Select</option>
				<?php
				for($i=0; $i<count($assessmentTopic); $i++)
				{									
					echo "<option value='".$assessmentTopic[$i][0]."'";
					if($assessment !='' )
					{
						if($assessmentTopic[$i][0]==$assessment)
							echo " selected";
					}
					else if($assessmentTopic[$i][2]==$topic)
						echo " selected";
					
					echo ">".$assessmentTopic[$i][1]."</option>";					
				}
				?>
			</select>
		</td>
		<td align="left" colspan="2" id="assessmentLine" <?=$trailType != 'assessment';?> <?php if($trailType != "assessment") echo 'style="display:none";'; else echo 'style="border-right:1px solid #626161"';  ?>></td>
		<td width="20%" id="assessmentSessionLabel" <?php if($trailType != "assessment") echo 'style="display:none;"'; ?>>
			<label for="assessmentSessionID">Session ID: </label></td>
		<td id="assessmentSessionSelection" width="40%" <?php if($trailType != "assessment") echo 'style="display:none;"'; ?>>
			<select name="assessmentSessionID" id="assessmentSessionID" >
				<option value="">Select</option>
				<?php
				for($i=0; $i<count($assessmentSessions); $i++)
				{
					$display = $assessment != "" ? (strpos($assessment,$assessmentStatusIdArrForSessions[$i]) !== false )?'block':'none':'block';
					echo "<option style='display:".$display.";' data='".$assessmentStatusIdArrForSessions[$i]."'  value='".$assessmentSessions[$i]."'";
					if($assessmentSessions[$i]==$assessmentSessionID)			
					echo " selected";
				if($assessmentStartTime[$i] != "" || $assessmentStartTime[$i] != null)
					echo ">".$assessmentSessions[$i]." (".$assessmentStartTime[$i].")</option>";
				else
					echo ">".$assessmentSessions[$i]."</option>";
				}
				?>
			</select>
		</td>
		<td align="left" colspan="2" id="dailyPracticeLine1" <?php if($trailType != "dailyPractice") echo 'style="display:none;"'; else echo 'style="border-right:1px solid #626161"';  ?>></td>
		<td>
			<div id="commonDiv">
				<input type="button" class="button" value="Show Questions" id="btnShowQues" name="btnShowQues" onClick="return checkSelected();" <?php if($childName=="") echo " disabled";?>>
			</div>
		</td>
	</table>
	<?php } else {echo "<input type='hidden' name='topic' value='$topic'><input type='hidden' name='topicDesc' value='".$_POST['topicDesc']."'>";}?>
</div>
<div style="clear:both"></div>

<?php
if($topic!="" || $sessionIDStr!="" || $exercise!= "" || $dailyPractice!="" || $dailyPracticeSessionIDStr !="" || $assessment != "") {
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
if($trailType == "assessment" && $assessment!="")
{	
    $query  = "SELECT a.qcode, a.R, a.S, a.A, a.sessionID,d.linkToCluster,e.cluster,a.srno,a.questionNo,date_format(a.lastModified, '%d-%m-%Y %H:%i:%s') lastModified from adepts_diagnosticQuestionAttempt a JOIN adepts_comprehensiveModuleAttempt b ON a.attemptID=b.srno JOIN adepts_diagnosticTestAttempts c ON c.srno=b.srno JOIN adepts_diagnosticTestMaster d ON d.diagnosticTestID=c.diagnosticTestID JOIN adepts_clusterMaster e ON e.clusterCode = d.linkToCluster where c.attemptID in($assessment) and b.userID=$student_userID";       
        if($assessmentSessionID!="")
            $query .= " AND a.sessionID in ($assessmentSessionID)";
        if($showWrongAns)
        	$query .= " AND a.R=0 ";
        $query .= " ORDER BY a.srno";                   
        $count_query = "SELECT count(a.srno) as noofques, sum(R) as correct from adepts_diagnosticQuestionAttempt a JOIN adepts_comprehensiveModuleAttempt b ON a.attemptID=b.srno JOIN adepts_diagnosticTestAttempts c ON c.srno=b.srno JOIN adepts_diagnosticTestMaster d ON d.diagnosticTestID=c.diagnosticTestID where c.attemptID in($assessment) and b.userID=$student_userID";
        if($assessmentSessionID!="")
            $count_query .= " AND a.sessionID in ($assessmentSessionID)";
        if($showWrongAns)
        	$count_query .= " AND R=0 ";
        $result = mysql_query($count_query) or die(mysql_error()."Invalid search criteria");            
        $line   = mysql_fetch_array($result);
        $noOfRecords = $line['noofques'];       
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
else if($dailyPractice!="" && ($trailType=="dailyPractice" || $trailType==""))
{
	$additionalCond = "";
	if($dailyPracticeSessionIDStr!="")
        $additionalCond = " AND sessionID in ($dailyPracticeSessionIDStr)";
    if($showWrongAns)
    {
    	$additionalCond .= " AND pmqad.R=0 ";
    	$additionalCondForTT .= " AND ttqa.result=0 ";
    }

    $query = "Select * from ((SELECT pmqad.qcode, pmqad.R, pmqad.S, pmqad.A, pmqad.sessionID, ttm.teacherTopicDesc, pmd.linkedToCluster as clusterCode, cm.cluster, pmqad.id as srno, pmqad.qno as questionNo, ttcm.teacherTopicCode, '' as clusterAttemptID, date_format(pmqad.lastModified, '%d-%m-%Y %H:%i:%s') lastModified, pmqad.questionLevel as questionLevel from practiseModulesTestStatus pmts, practiseModuleDetails pmd, adepts_teacherTopicMaster ttm, adepts_teacherTopicClusterMaster ttcm, adepts_clusterMaster cm, practiseModulesQuestionAttemptDetails pmqad where pmts.practiseModuleId=pmd.practiseModuleId and pmd.linkedToCluster=ttcm.clusterCode and ttcm.teacherTopicCode=ttm.teacherTopicCode and pmts.id=pmqad.practiseModuleTestStatusId and pmts.id IN(".$dailyPractice.")".$additionalCond." GROUP by pmqad.qcode,pmqad.qno) UNION ALL (SELECT ttqa.question as qcode, ttqa.result as R, ttqa.S, ttqa.userResponse as A, pmtta.sessionID, ttm.teacherTopicDesc, pmd.linkedToCluster as clusterCode, cm.cluster, pmtta.timedTestAttemptId as srno, ttqa.qno as questionNo, ttcm.teacherTopicCode, '' as clusterAttemptID, date_format(pmtta.lastModified, '%d-%m-%Y %H:%i:%s') lastModified, 'Timed Test' as questionLevel from practiseModulesTestStatus pmts, practiseModulesTimedTestAttempt pmtta, adepts_timedTestQuesAttempt ttqa, practiseModuleDetails pmd, adepts_clusterMaster cm, adepts_teacherTopicClusterMaster ttcm, adepts_teacherTopicMaster ttm where pmts.id=pmtta.practiseModuleTestStatusId and pmtta.timedTestAttemptId=ttqa.timedTestID and pmts.practiseModuleId=pmd.practiseModuleId and pmd.linkedToCluster=cm.clusterCode and pmd.linkedToCluster=ttcm.clusterCode and pmts.id IN(".$dailyPractice.")".$additionalCondForTT." GROUP BY pmtta.timedTestAttemptId,ttqa.question)) as dt ORDER BY srno,questionNo";

    $count_query = "SELECT SUM(qcount) as noofques,SUM(ccount) as correct, SUM(qTime) as totalTime from ((select count(pmqad.id) as qcount,sum(pmqad.R) as ccount, sum(pmqad.S) as qTime from practiseModulesTestStatus pmts, practiseModulesQuestionAttemptDetails pmqad where pmts.id=pmqad.practiseModuleTestStatusId and pmts.id in(".$dailyPractice.")".$additionalCond.") UNION ALL (select count(ttqa.qno) as qcount,sum(ttqa.result) as ccount, sum(ttqa.S) as qTime from practiseModulesTestStatus pmts, practiseModulesTimedTestAttempt pmtta, adepts_timedTestQuesAttempt ttqa where pmts.id=pmtta.practiseModuleTestStatusId and pmtta.timedTestAttemptId=ttqa.timedTestID and pmts.id in(".$dailyPractice.")".$additionalCondForTT.")) as dt";   
    $result = mysql_query($count_query) or die(mysql_error()."Invalid search criteria");
    $line   = mysql_fetch_array($result);
    $noOfRecords = $line['noofques'];
}
else if($sessionIDStr!="")
{
    $query  = "SELECT b.qcode, b.R, b.S, b.A, sessionID, teacherTopicDesc, c.clusterCode, cluster, b.srno, questionNo, e.teacherTopicCode, b.clusterAttemptID, date_format(b.lastModified, '%d-%m-%Y %H:%i:%s') lastModified
               FROM   ".TBL_QUES_ATTEMPT."_class$childClass b, adepts_clusterMaster c, adepts_teacherTopicMaster e
               WHERE  b.clusterCode=c.clusterCode AND e.teacherTopicCode=b.teacherTopicCode AND sessionID in ($sessionIDStr)";
   	if($_POST["last1hour"]==1)
		$query .= " AND b.lastModified >= '$sessionTime'";
   	if($showWrongAns)
   		$query .= " AND b.R=0 ";
	if($errorReportingFlag==1)
    	$query .= " ORDER BY srno DESC";
	else
		$query .= " ORDER BY srno";
    //echo $query;
    $count_query = "SELECT count(srno) as noofques, sum(R) as correct FROM ".TBL_QUES_ATTEMPT."_class$childClass WHERE sessionID in ($sessionIDStr)";
	if($_POST["last1hour"]==1)
		$count_query .= " AND lastModified >= '$sessionTime'";
    if($showWrongAns)
    	$count_query .= " AND R=0 ";
    $result = mysql_query($count_query) or die(mysql_error()."Invalid search criteria");
    $line   = mysql_fetch_array($result);
    $noOfRecords = $line['noofques'];
}
else if($dailyPracticeSessionIDStr!="")
{
	$additionalCond = "";
    if($showWrongAns)
    {
    	$additionalCond = " AND pmqad.R=0 ";
    	$additionalCondForTT = " AND ttqa.result=0 ";
    }

    $query = "Select * from ((SELECT pmqad.qcode, pmqad.R, pmqad.S, pmqad.A, pmqad.sessionID, ttm.teacherTopicDesc, pmd.linkedToCluster as clusterCode, cm.cluster, pmqad.id as srno, pmqad.qno as questionNo, ttcm.teacherTopicCode, '' as clusterAttemptID, date_format(pmqad.lastModified, '%d-%m-%Y %H:%i:%s') lastModified, pmqad.questionLevel as questionLevel from practiseModulesTestStatus pmts, practiseModuleDetails pmd, adepts_teacherTopicMaster ttm, adepts_teacherTopicClusterMaster ttcm, adepts_clusterMaster cm, practiseModulesQuestionAttemptDetails pmqad where pmts.practiseModuleId=pmd.practiseModuleId and pmd.linkedToCluster=ttcm.clusterCode and ttcm.teacherTopicCode=ttm.teacherTopicCode and pmts.id=pmqad.practiseModuleTestStatusId and sessionID IN(".$dailyPracticeSessionIDStr.")".$additionalCond." GROUP by pmqad.qcode,pmqad.qno) UNION ALL (SELECT ttqa.question as qcode, ttqa.result as R, ttqa.S, ttqa.userResponse as A, pmtta.sessionID, ttm.teacherTopicDesc, pmd.linkedToCluster as clusterCode, cm.cluster, pmtta.timedTestAttemptId as srno, ttqa.qno as questionNo, ttcm.teacherTopicCode, '' as clusterAttemptID, date_format(pmtta.lastModified, '%d-%m-%Y %H:%i:%s') lastModified, 'Timed Test' as questionLevel from practiseModulesTestStatus pmts, practiseModulesTimedTestAttempt pmtta, adepts_timedTestQuesAttempt ttqa, practiseModuleDetails pmd, adepts_clusterMaster cm, adepts_teacherTopicClusterMaster ttcm, adepts_teacherTopicMaster ttm where pmts.id=pmtta.practiseModuleTestStatusId and pmtta.timedTestAttemptId=ttqa.timedTestID and pmts.practiseModuleId=pmd.practiseModuleId and pmd.linkedToCluster=cm.clusterCode and pmd.linkedToCluster=ttcm.clusterCode and sessionID IN(".$dailyPracticeSessionIDStr.")".$additionalCondForTT." GROUP BY pmtta.timedTestAttemptId,ttqa.question)) as dt ORDER BY srno,questionNo";

    $count_query = "SELECT SUM(qcount) as noofques,SUM(ccount) as correct, SUM(qTime) as totalTime from ((select count(pmqad.id) as qcount,sum(pmqad.R) as ccount, sum(pmqad.S) as qTime from practiseModulesTestStatus pmts, practiseModulesQuestionAttemptDetails pmqad where pmts.id=pmqad.practiseModuleTestStatusId and pmqad.sessionID in(".$dailyPracticeSessionIDStr.")".$additionalCond.") UNION ALL (select count(ttqa.qno) as qcount,sum(ttqa.result) as ccount, sum(ttqa.S) as qTime from practiseModulesTestStatus pmts, practiseModulesTimedTestAttempt pmtta, adepts_timedTestQuesAttempt ttqa where pmts.id=pmtta.practiseModuleTestStatusId and pmtta.timedTestAttemptId=ttqa.timedTestID and pmtta.sessionID in(".$dailyPracticeSessionIDStr.")".$additionalCondForTT.")) as dt";

    $result = mysql_query($count_query) or die(mysql_error()."Invalid search criteria");
    $line   = mysql_fetch_array($result);
    $noOfRecords = $line['noofques'];
}
if($noOfRecords==0)
{
    echo "<center style='font-size: 18px;margin-top: 10px;color: red;'>No questions found!</center>";	// for task 11267
}
else
{

$totalQuestions = $noOfRecords;
$perCorrect = $totalQuestions!=0 ? round(($line['correct']/$totalQuestions)*100,1):"0";
if(isset($line['totalTime']))
	$avgTimePerQuestion = $totalQuestions!=0 ? round($line['totalTime']/$totalQuestions):"0";
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
	    		<?php   $clspaging->writeHTMLpagesrange($_SERVER['PHP_SELF'],true,"https://www.mindspark.in/mindspark/");	?>
	    	</td>
	    </tr>
	</table>-->
	<table id="pagingTable">
		<td>
				<?php   $clspaging->writeHTMLpagesrange($_SERVER['PHP_SELF'],true,"https://www.mindspark.in/mindspark/");	?>
		</td>
	</table>
<?php
}

//$srno = 1;
//echo $clspaging->currentpage;
$srno=($clspaging->currentpage-1)*$clspaging->numofrecsperpage+1;
$query .= "  ".$clspaging->limit;
$result = mysql_query($query) or die("<br>Error in query - ".mysql_error());



$tmp_sessionNo = "";//isset($_POST['lastSessionID'])?$_POST['lastSessionID']:"";
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
    $user_ans     = html_entity_decode($line['A']);
    $topic        = $line['teacherTopicDesc'];
    $clusterCode  = $line['clusterCode'];
    $clusterDesc  = $line['cluster'];
    $ttCode       = $line['teacherTopicCode'];
    $clusterAttemptID = $line['clusterAttemptID'];
    $quesAttemptSrno = $line['srno'];
	$teacherComments = (isset($line['teacherComments']))?$line['teacherComments']:"";
	$questionLevel = (isset($line['questionLevel']))?$line['questionLevel']:"";
	
	$type = ($trailType == "ncert")?"Exercise":($trailType == "ncert")?"Learning unit":"Daily Practice";
	if($trailType=="ncert")
	{
		$clusterType = "practice";
		$sqlNew = "SELECT groupID FROM adepts_ncertQuestions WHERE qcode=".$line['qcode'];
		$resultNew = mysql_query($sqlNew);
		$rowNew = mysql_fetch_assoc($resultNew);
	}
	else if($trailType=="regular" || $trailType=="dailyPractice")
	{
		if(is_numeric($line['qcode']))
		{
			$sqlNew = "SELECT clusterType, groupID FROM adepts_clusterMaster a, adepts_questions b WHERE a.clusterCode=b.clusterCode AND qcode=".$line['qcode'];
			$resultNew = mysql_query($sqlNew);
			$rowNew = mysql_fetch_assoc($resultNew);
			$clusterType = $rowNew["clusterType"];			
		}
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
		
		$groupText = orig_to_html($rowNew['groupText'],"images","Q");
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
	else if($trailType =='assessment' )
		$question = new diagnosticTestQuestion($line['qcode']);
	else
	{
		if($questionLevel == "Timed Test")
			$question     = new timedTestQuestion($line['qcode']);
		else
			$question     = new Question($line['qcode']);
	}
		if($question->isDynamic())
		{
			$mode = 'normal';
			if($trailType == 'dailyPractice')
				$mode='practiseModule';
			$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE userID=$student_userID AND class=$childClass AND mode='".$mode."' AND quesAttempt_srno=".$line['srno'];
			$params_result = mysql_query($query);
			$params_line   = mysql_fetch_array($params_result);
			$question->generateQuestion("answer",$params_line[0]);
		}

		$questionType = $question->quesType;
		$correct_answer = $question->getCorrectAnswerForDisplay();
		
		if($trailType == "ncert") {
			$eeresponse = getEEresponse($quesAttemptSrno,$childClass,"normal",$trailType);
		} else {
			if($question->eeIcon == "1" && $clusterType!="practice")
				$eeresponse = getEEresponse($quesAttemptSrno,$childClass,"normal",$trailType);
			else if($question->eeIcon == "1" && $clusterType=="practice")
				$eeresponse = getEEresponse($quesAttemptSrno,$childClass,"practice",$trailType);
		}
		if($trailType!='ncert') {
			$long_user_response = getLongUserResponse($quesAttemptSrno, $student_userID, $line['qcode'], $line['sessionID'], $user_ans);
			//$user_ans='';
		}

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
		if($rowno==1 && $trailType != 'dailyPractice' && $trailType != 'assessment')
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
			
			$wildCardArray = getWildCardQuesAttemptedInSession($student_userID,$line['sessionID']);			
			for($wildcardno=0; $wildcardno<count($challengeQuesArray)&& $challengeQuesArray[$wildcardno][4]<$line['questionNo']; $wildcardno++);
		}
		if($tmp_sessionNo!=$line['sessionID'])    {
			$query = "SELECT date_format(startTime,'%d/%m/%Y %H:%i:%s') FROM ".TBL_SESSION_STATUS." WHERE sessionID=".$line['sessionID'];
			$tmp_result = mysql_query($query);
			$tmp_line = mysql_fetch_array($tmp_result);
			if($errorReportingFlag==1 && $flagSet==0) { 
				echo "<div align='center' style='color:red;margin-top:10px;margin-bottom:10px;' id='blkMsg'><strong align='center' style='font-size:1.3em;'>Click on question numbers to report error in that question.</strong></div>";
				$flagSet=1;
			}
			echo "<div id='questionContainer'>";
			echo "<div  class='section_header'>";
			if($trailType != 'dailyPractice')
				$width = "46";
			else
				$width = "23";
			echo "<div style='width: ".$width."%;float: left;'> ";
			if($trailType != 'dailyPractice')
				echo "<span>Session ID: </span><span style='color:#2F99CB;' class='title'>".$line['sessionID']."</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ";
			echo "<span>Start Time: </span><span style='color:#2F99CB;' class='title'>".$tmp_line[0]."</span></div>";
			if ($tmp_cluster!=$clusterCode)
			{
				$tmp_cluster = $clusterCode;
				$lowerLevel = isLowerLevelCluster($ttCode, $clusterCode, $childClass, $clusterAttemptID);
			}
			echo "<div id='classTopic'><div class='arrow-black1'></div><div id='classText'>Total Questions Done : <span id='totalQuesDone' style='color:#E75903;'>".$totalQuestions."</span></div></div><div id='classTopic'><div class='arrow-black1'></div><div id='classText'>Percentage correct : <span id='perCorrect' style='color:#E75903;'>".$perCorrect."%</span></div></div>";
			if($trailType == 'dailyPractice')
				echo "<div id='classTopic'><div class='arrow-black1'></div><div id='classText'>Average Time per Question : <span id='avgTimePerQuestion' style='color:#E75903;'>".$avgTimePerQuestion." seconds</span></div></div>";
			echo "</div><br/>";
			$tmp_sessionNo = $line['sessionID'];
			$tmp_topic = $topic;

			//Get the challenge ques attempted, if any, in the session
			$challengeQuesArray = array();
			$wildCardArray = array();
			if(SUBJECTNO==2 && $trailType != 'dailyPractice' && $trailType != 'assessment')
			{
				$challengeQuesArray = getChallengeQuesAttemptedInSession($student_userID,$line['sessionID']);
				$wildCardArray = getWildCardQuesAttemptedInSession($student_userID,$line['sessionID']);
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
			{
				$cqno = 0;
				$wildcardno = 0;
			}
				
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
			if($errorReportingFlag==1) { 
				echo "<div align='center' style='color:red' id='blkMsg'><strong align='center' style='font-size:1.3em;' class='blink'>Click on question numbers to report error in that question.</strong></div>";
			}
		?>
        <div id='questionGroupContainer'>
        <?php
			if($boolGroupText)
			{
				$boolGroupText = false;
		?>
                <table width="100%" border="0" cellspacing="2" cellpadding="3" align="right">
                    <tr>
                    	<?php
						if($trailType == "ncert")
						{
						?>
                    	<td align='center' width='5%' style="vertical-align:top;"><div class="qno"><?=$groupNo?></div></td>
                        <?php
                        }
						?>
                        <td valign="top" align="left"><?php if ($groupText!="") { ?>
                        	<p><span class="quesDetails"><?php echo $groupText;?></span><br></p>
                        	<?php } ?>
                        </td>
                    </tr>
                </table>
        <?php
			}
		}

		if($clusterType == "practice")
			echo '<div class="singleQuestion column'.$groupColumn.'">';
		if($trailType == "ncert")
			showQuestion($line["qcode"],$line["sessionID"], $childClass, $question->subQuestionNo, $question->getQuestionForDisplay($eeresponse), $response, $questionType, $question->getOptionA(), $question->getOptionB(), $question->getOptionC(), $question->getOptionD(), $optiona_bgcolor, $optionb_bgcolor, $optionc_bgcolor, $optiond_bgcolor, $correct_answer, $user_ans, $timeTaken, $clusterDesc, $response, $lowerLevel, $accessFromStudentInterface, $question->eeIcon, $trailType, $oneQuestionGroup, $quesAttemptSrno, $teacherComments);
		else
			if($questionLevel == "Timed Test")
			{
				showQuestion($line["qcode"],$line["sessionID"], $childClass, $srno, $question->questionStem, $response, $questionType, $question->getOptionA(), $question->getOptionB(), $question->getOptionC(), $question->getOptionD(), $optiona_bgcolor, $optionb_bgcolor, $optionc_bgcolor, $optiond_bgcolor, $correct_answer, $user_ans, $timeTaken, $clusterDesc, $response, $lowerLevel, $accessFromStudentInterface, $question->eeIcon, $trailType ,$oneQuestionGroup,$quesAttemptSrno,"","",$questionLevel);
			}
			else
			{
				showQuestion($line["qcode"],$line["sessionID"], $childClass, $srno, (strpos($question->questionStem,"ADA_eqs")!==false?$question->getQuestionForDisplay($long_user_response, 2):$question->getQuestionForDisplay($eeresponse)), $response, $questionType, $question->getOptionA(), $question->getOptionB(), $question->getOptionC(), $question->getOptionD(), $optiona_bgcolor, $optionb_bgcolor, $optionc_bgcolor, $optiond_bgcolor, $correct_answer, $user_ans, $timeTaken, $clusterDesc, $response, $lowerLevel, $accessFromStudentInterface, $question->eeIcon, $trailType ,$oneQuestionGroup,$quesAttemptSrno,"","",$questionLevel);
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
					$html	.=	'<div id="timedtest_'.$timedTestArray[$timedTestNo]["timedTestCode"].'_'.$line["sessionID"].'" style="margin-left:10px"></div><div class="desk_block">
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
						$html	.=	'<div><span class="title">Learning unit: </span>'.getClusterdescription($timedTestArray[$timedTestNo]["cluster"]).'</div>';
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
					$html	.=	'<div id="activity_'.$gamesArray[$gameNo]["gameID"].'_'.$line["sessionID"].'" style="margin-left:10px"></div><div class="desk_block">
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
									 $html	.=	'<div><span class="title">Learning unit: </span>'.getClusterdescription($gamesArray[$gameNo]["cluster"]).'</div>';
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
					$html	.=	'<div id="remedial_'.$remedialItemAttemptArray[$remedialItemAttemptNo]["remedialItemCode"].'_'.$line["sessionID"].'" class="qno" style="margin-left:10px;float: left;margin-top: 15px;"></div><div class="desk_block">
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
					showQuestion($line["qcode"],$line["sessionID"], $childClass, $srno, $question->getQuestion(), $response, $questionType, $question->getOptionA(), $question->getOptionB(), $question->getOptionC(), $question->getOptionD(), $optiona_bgcolor, $optionb_bgcolor, $optionc_bgcolor, $optiond_bgcolor, $correct_answer, $user_ans, $timeTaken, $clusterDesc, $response, $lowerLevel, $accessFromStudentInterface);
					
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
		showQuestion($line["qcode"],$line["sessionID"], $childClass, $srno, $question->getQuestionForDisplay($eeresponse), $response, $questionType, $question->getOptionA(), $question->getOptionB(), $question->getOptionC(), $question->getOptionD(), $optiona_bgcolor, $optionb_bgcolor, $optionc_bgcolor, $optiond_bgcolor, $correct_answer, $user_ans, $timeTaken, $clusterDesc, $response, $lowerLevel, $accessFromStudentInterface, $question->eeIcon);
		$cqno++;
	}

	//Check if wildcard was given after this ques, if yes display it
	while($wildcardno <count( $wildCardArray) &&  $wildCardArray[$wildcardno][4]==$line['questionNo'])
	{
		if($wildCardArray[$wildcardno][6] == 'normal' || $wildCardArray[$wildcardno][6] == 'comprehensive')
			$question     = new Question($wildCardArray[$wildcardno][0]);
		else
			$question     = new researchQuestion($wildCardArray[$wildcardno][0]);
		if($question->isDynamic())
		{
			$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE userID=$student_userID AND class=$childClass AND mode='normal' AND quesAttempt_srno=".$line['srno'];
			$params_result = mysql_query($query);
			$params_line   = mysql_fetch_array($params_result);
			$question->generateQuestion("answer",$params_line[0]);
		}
	    $questionType = $question->quesType;
	    $timeTaken    = $wildCardArray[$wildcardno][2];
	    $response     = $wildCardArray[$wildcardno][3];
	    $wildcardQType = $wildCardArray[$wildcardno][6];
	    $quesAttemptSrno = $wildCardArray[$wildcardno][7];
	    $user_ans     = html_entity_decode($wildCardArray[$wildcardno][1]);
	    $correct_answer = $question->getCorrectAnswerForDisplay();
	   
		if($question->eeIcon == "1")
			$eeresponse = getEEresponse($quesAttemptSrno,$childClass,"wildcard");
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

        $ques = strpos($question->questionStem,"ADA_eqs")!==false?$question->getQuestionForDisplay($long_user_response, 2):$question->getQuestionForDisplay($eeresponse);
        if($wildcardQType == 'comprehensive')
        {        	
        	$comprehensiveClusterQuery = "SELECT b.clusterCode from adepts_researchQuesAttempt a JOIN adepts_questions b on a.qcode=b.qcode where a.qcode=".$wildCardArray[$wildcardno][0];        	
        	$comprehensiveClusterResult = mysql_query($comprehensiveClusterQuery);
			$comprehensiveClusterLine = mysql_fetch_row($comprehensiveClusterResult);
			$clusterDesc = getClusterdescription($comprehensiveClusterLine[0]);
        	$srno++;        	
        }
        else
        {
        	 $clusterDesc = "Wild Card Question";
        }
		showQuestion($wildCardArray[$wildcardno][0],$line["sessionID"], $childClass, $srno, $ques, $response, $questionType, $question->getOptionA(), $question->getOptionB(), $question->getOptionC(), $question->getOptionD(), $optiona_bgcolor, $optionb_bgcolor, $optionc_bgcolor, $optiond_bgcolor, $correct_answer, $user_ans, $timeTaken, $clusterDesc, $response, $lowerLevel, $accessFromStudentInterface, $question->eeIcon,"",false,$quesAttemptSrno,"",$wildcardQType);
		$wildcardno++;
	}

if($clusterType != "practice")

$srno++;$rowno++;}  ?>

<input type="hidden" name="lastTopic" id="lastTopic" value="<?=$tmp_topic?>">
<input type="hidden" name="lastSessionID" id="lastSessionID" value="<?=$tmp_sessionNo?>">
<input type="hidden" name="lastCluster" id="lastCluster" value="<?=$tmp_cluster?>">
<input type="hidden" name="lowerLevel" id="lowerLevel" value="<?=$lowerLevel?>">

<div align="right" class="legend" style="width:98%"><br/><a href="#pnlTopicSelect"><em>Top</em></a><br/></div>
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

function implodeArrayForQuery($arr) {
	$str = "'" . implode("','", $arr) . "'";
	return $str;
}

function showQuestion($qcode,$sessionID, $childClass, $srno,  $question, $response, $questionType, $optionA, $optionB, $optionC, $optionD, $optiona_bgcolor, $optionb_bgcolor, $optionc_bgcolor, $optiond_bgcolor, $correct_answer, $user_ans, $timeTaken, $clusterDesc, $response, $lowerLevel, $accessFromStudentInterface, $eeIcon="0", $trailType="", $oneQuestionGroup=false, $quesAttemptSrno , $teacherComments="",$wildcardQType="",$questionLevel="")
{	
	$quesType="";
	if($clusterDesc=="Challenge Question")
	{
		$srno = "CQ";
		$quesType="challenge";
	}	
	else if($clusterDesc=="Wild Card Question" && $wildcardQType != 'comprehensive') 
	{
		$srno = "WC";		
		$quesType=$wildcardQType;
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
        <table width='100%' border=0 cellspacing=0>
            <tr  bgcolor="" >
                <td align='center' valign='top'  width='5%'>
                	<?php if (!$oneQuestionGroup){
                		$dataPassedOnQ = (isset($_REQUEST['mode']) && $_REQUEST['mode']=='errorReporting') ? $quesType."_".$qcode."_".$sessionID."_".$srno."_".$childClass."_".$quesAttemptSrno : $srno;
                	 ?>
                	<div id="<?php echo $dataPassedOnQ;?>" class="qno<?php if($trailType == "ncert") { echo " questionQ1"; }?>"><?php echo (!$oneQuestionGroup) ? $srno : ""; ?></div><br/>
                	<?php } ?>
				<?php
					if($eeIcon == "1")
						echo '<div class="eqEditorToggler" align="center"></div>';
				?>
                </td>
                <td align='left'>
                <?php 
                	if(is_numeric($qcode))
                		echo $question;
                	else
						echo utf8_decode($qcode);
				?><br/></td>
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
	        if($questionType=='Blank' || $questionType=='D')    {
	        	$user_ans=implode(' | ',explode('|',$user_ans));
	        }
	        if($questionType=='I' && strpos($question, "ADA_eqs")!==false)    {
	        	$user_ans="";
	        }

        	if($clusterDesc =="Challenge Question" || $clusterDesc=="Wild Card Question")
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
						<?php if($trailType == 'dailyPractice')    {    ?><span>Level: </span><span class="title"><?=$questionLevel?></span>&nbsp;&nbsp;&nbsp;&nbsp;<?php }  ?>
						<span>User Response: </span>&nbsp;&nbsp;
						<span class="title"><?=$user_ans;?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<?php if($correct_answer!="")    {    ?><span>Correct answer: </span><span class="title"><?=$correct_answer?></span>&nbsp;&nbsp;&nbsp;&nbsp;<?php }  ?>
						<span>Time taken: </span><span class="title"><?=$timeTaken?> secs</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</div>
					<br/>
					<div> <?php echo ($trailType != 'dailyPractice')? $clusterDesc : "";?>
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

function getWildCardQuesAttemptedInSession($userID,$sessionID)
{
	$wildCardArray = array();
	$wildCardsq	=	"SELECT   srno, questionNo, questionType, qcode, A, R, s, date_format(lastModified, '%d-%m-%Y %H:%i:%s') lastModified
		             FROM     adepts_researchQuesAttempt
		             WHERE    userID=".$userID." AND sessionID='".$sessionID."' ORDER BY lastModified,questionNo";
					 
	//echo $cq_query;
	$wildcard_result = mysql_query($wildCardsq) or die(mysql_error());
	$wildcardno = 0;
	while ($wildcard_line=mysql_fetch_array($wildcard_result))
	{
		$wildCardArray[$wildcardno][0] = $wildcard_line['qcode'];
		$wildCardArray[$wildcardno][1] = $wildcard_line['A'];
		$wildCardArray[$wildcardno][2] = $wildcard_line['s'];
		$wildCardArray[$wildcardno][3] = $wildcard_line['R'];
		$wildCardArray[$wildcardno][4] = $wildcard_line['questionNo'];
		$wildCardArray[$wildcardno][5] = $wildcard_line['ttAttemptID'];
		$wildCardArray[$wildcardno][6] = $wildcard_line['questionType'];
		$wildCardArray[$wildcardno][7] = $wildcard_line['srno'];
		$wildcardno++;
	}
	return $wildCardArray;
}
function getClusterdescription($clusterCode)
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
	if($trailType == "ncert") {
		$query = "SELECT eeresponse, eeResponseImg FROM adepts_ncertQuesAttempt WHERE srno='$srno'";
		$result = mysql_query($query);
		if(mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			$eeResponse = $row[0];
			$eeResponse .= "@$*@";
			$eeResponse .= $row[1];
		}
		return $eeResponse;
	} else {
		$query = "SELECT eeResponse FROM adepts_equationEditorResponse WHERE srno='$srno'";
		$query .= " AND childClass='$childClass' AND question_type='$question_type'";
		$result = mysql_query($query);
		if(mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			$eeResponse = $row[0];
		}
		return $eeResponse;
	}
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

?>
