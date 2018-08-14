<?php
include ("../userInterface/constants.php");
include ("header.php");
// include("../slave_connectivity.php");
set_time_limit ( 0 ); // Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
error_reporting ( E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING );
include ("functions/functions.php");
include ("classes/testTeacherIDs.php");
include_once ("../userInterface/classes/clsTopicProgress.php");
include_once ("../userInterface/classes/clsTeacherTopic.php");
$userID = $_SESSION ['userID'];
$schoolCode = isset ( $_SESSION ['schoolCode'] ) ? $_SESSION ['schoolCode'] : "";
$user = new User ( $userID );
$category = $user->category;
$subcategory = $user->subcategory;
$teacherName = $user->childName;
$username= $_SESSION['childName'];
$durationPopup = 0;
if(in_array($schoolCode, array('3332611','524522','23246','206357','376207','34736','2387554')))
{
	if(date("2016-09-17") >= date("Y-m-d"))
	$durationPopup = 1;
}
if(isset($_REQUEST['openTab'])){
	$redirectTTCode = $_REQUEST['ttCode'];
	$redirectFlow = $_REQUEST['flow'];
}
		
if ($_POST ['action'] == "savepriority") {
	$idlist = $_POST ['ttlist'];
	$data = explode ( ",", rtrim ( $idlist, "," ) );
	
	$cls = $_POST ['cls'];
	$section = $_POST ['section'];
	$i = 1;
	
	$sessionID = $_SESSION ['sessionID'];
	$trackQuery = "INSERT INTO trackingTeacherInterface (userID, sessionID, pageID, lastmodified) values ($userID,$sessionID,74,now())";
	mysql_query ( $trackQuery ) or die ( mysql_error () );
	
	foreach ( $data as $val ) {
		$query = "update adepts_teacherTopicActivation set priority = $i where teacherTopicCode = '$val' and schoolCode=$schoolCode and class = $cls and section = '$section' and deactivationdate = '0000-00-00'";
		mysql_query ( $query ) or die ( mysql_error () );
		$i ++;
	}
}

if (strcasecmp ( $category, "Teacher" ) == 0 || strcasecmp ( $category, "School Admin" ) == 0) {
	$query = "SELECT schoolname FROM educatio_educat.schools WHERE schoolno=" . $schoolCode;
	$r = mysql_query ( $query );
	$l = mysql_fetch_array ( $r );
	$schoolName = $l [0];
}

if (strcasecmp ( $category, "School Admin" ) == 0) {
	$query = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
				   FROM     adepts_userDetails
				   WHERE    schoolCode=$schoolCode AND category='STUDENT' AND subcategory='School' AND enabled=1 AND endDate>=curdate() AND subjects like '%" . SUBJECTNO . "%'
				   GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
} elseif (strcasecmp ( $category, "Teacher" ) == 0) {
	$query = "SELECT   class, group_concat(distinct section ORDER BY section)
				  FROM     adepts_teacherClassMapping
				  WHERE    userID = $userID AND subjectno=" . SUBJECTNO . "
				  GROUP BY class ORDER BY class, section";
} elseif (strcasecmp ( $category, "Home Center Admin" ) == 0) {
	$query = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
				   FROM     adepts_userDetails
				   WHERE    category='STUDENT' AND subcategory='Home Center' AND schoolCode=$schoolCode AND enabled=1 AND endDate>=curdate() AND subjects like '%" . SUBJECTNO . "%'
				   GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
} else {
	echo "You are not authorised to access this page!";
	exit ();
}
$classArray = $sectionArray = $topicArray = array ();
$hasSections = false;
$checkOtherGrades = 0;
$checkGrade1 = 0;
$checkGrade2 = 0;
$result = mysql_query ( $query ) or die ( mysql_error () );
while ( $line = mysql_fetch_array ( $result ) ) {
	array_push ( $classArray, $line [0] );
	if ($line [1] != '')
		$hasSections = true;
	$sections = explode ( ",", $line [1] );
	$sectionStr = "";
	for($i = 0; $i < count ( $sections ); $i ++) {
		if ($sections [$i] != "")
			$sectionStr .= "'" . $sections [$i] . "',";
	}
	
	$sectionStr = substr ( $sectionStr, 0, - 1 );
	
	if (strcasecmp ( $category, "Home Center Admin" ) == 0) {
		$query = "SELECT b.class, a.teacherTopicCode, a.teacherTopicDesc
					  FROM   adepts_teacherTopicMaster a, adepts_teacherTopicActivation b
					  WHERE  a.teacherTopicCode=b.teacherTopicCode AND subjectno=" . SUBJECTNO . " AND b.schoolCode=$schoolCode AND b.class=" . $line [0];
	} else {
		$query = "SELECT b.class, a.teacherTopicCode, a.teacherTopicDesc
					  FROM   adepts_teacherTopicMaster a, adepts_teacherTopicActivation b
					  WHERE  a.teacherTopicCode=b.teacherTopicCode AND subjectno=" . SUBJECTNO . " AND b.schoolCode=$schoolCode AND b.class=" . $line [0];
	}
	
	if ($sectionStr != "")
		$query .= " AND section in ($sectionStr)";
	$query .= " ORDER BY teacherTopicDesc";
	$topic_result = mysql_query ( $query ) or die ( mysql_error () );
	while ( $topic_line = mysql_fetch_array ( $topic_result ) ) {
		$topicArray [$topic_line ['class']] [$topic_line ['teacherTopicCode']] = $topic_line ['teacherTopicDesc'];
	}
	$sectionArray[$line[0]]= $sectionStr;
}
$searchTerm = $_REQUEST ['searchingTerm'];
$class = $_REQUEST ['cls'];

if (isset ( $_REQUEST ['checkflag'] ) && $_REQUEST ['checkflag'] == 1)
	$checkflag = $_REQUEST ['checkflag'];

$section = $_REQUEST ['section'];
$masterTopic = "";
if (isset ( $_REQUEST ['masterTopic'] )) {
	$masterTopic = $_REQUEST ['masterTopic'];
}

$defaultFlow = "MS";

$ttCode = $_REQUEST ['ttCode'];

$ttDesc = $cls = $section = "";
if (isset ( $_REQUEST ['cls'] ))
	$cls = $_REQUEST ['cls'];
if (isset ( $_REQUEST ['ttDesc'] ))
	$ttDesc = $_REQUEST ['ttDesc'];
if (isset ( $_REQUEST ['section'] ))
	$section = $_REQUEST ['section'];
$sectionList = isset ( $_REQUEST ['sectionList'] )?$_REQUEST ['sectionList']:"";
$notCovered = isset ($_REQUEST['notCovered']) ? $_REQUEST['notCovered'] : 0;
$fromDate = isset ($_REQUEST['fromDate']) && $_REQUEST['fromDate']!='' ? date("Y-m-d", strtotime($_REQUEST['fromDate'])) : '';
$toDate = isset ($_REQUEST['toDate']) && $_REQUEST['toDate']!='' ? date("Y-m-d", strtotime($_REQUEST['toDate'])) : '';

// $flow = isset($_REQUEST['flow'])?$_REQUEST['flow']:"MS";
if(isset($_REQUEST['flow']))
{
	$flow = $_REQUEST['flow'];
	$defaultFlow=$flow; // added 19 June to set flow in topic page.
	if(substr($flow,0,6)=="Custom")
		$flow = "Custom";
	else if($flow=="")
		$flow = "MS";
}
else
	$flow = "MS";

//echo "Floww".$flow."Default ".$defaultFlow;

$topicsActivated = getTTsActivated ( $cls, $schoolCode, $section );
$liveClusterList = disableLiveClusters ( $ttCode, $schoolCode, $cls, $section, $flow );
$interface = "";

if ((isset ( $_POST ['save'] ) || isset ( $_POST ['save_activate'] ) || isset ( $_POST ["saveAndActivate"] )) && ! in_array ( $loginID, $testIDArray )) {
	$flag = 1;
	$flow = $_POST ['rdTTFlow'];
	$newFlow = $_POST ["generatedFlow"];

	$sectionList=$sectionList!=""?$sectionList:$section;
	
	$sectionList=explode(",", $sectionList);
	
	if ($flow == "MS" || $flow == "CBSE" || $flow == "ICSE" || $flow == "IGCSE") {
		foreach ($sectionList as $key => $sec) {
			$activeMessage = activatedTopic ( $schoolCode, $cls, $sec, $ttCode, $flow, $loginID ,$notCovered,$fromDate,$toDate);
		}
	} else if ($newFlow != "CUSTOM" && $newFlow != "") {
		foreach ($sectionList as $key => $sec) {
			$activeMessage = activatedTopic ( $schoolCode, $cls, $sec, $ttCode, $newFlow, $loginID ,$notCovered,$fromDate,$toDate);
		}
	} else if ($flow == "Custom") {
		$clusterArray = $_POST ['chkCluster'];
		if (count ( $clusterArray ) > 0) {
			$topicDetailsArr = createCustomTT ( $ttCode, $schoolCode, $cls, $clusterArray, $username, $ttDesc );
			$activeMessage = "Customized successfully";
			if ($_POST ["saveAndActivate"] == 1) {
				$topicDetails = explode ( "~", $topicDetailsArr );
				$flowActivation = $topicDetails [0];
				$newTopicCode = $topicDetails [1];
				foreach ($sectionList as $key => $sec) {
					$activeMessage = activatedTopic ( $schoolCode, $cls, $sec, $newTopicCode, $flowActivation, $loginID ,$notCovered,$fromDate,$toDate);
				}
			}
			/*
			 * $clusters = implode(",",$clusterArray);
			 * $customCode = getCustomizedTopicCode($clusters, $username, $schoolCode);
			 * $flow .= " - ".$customCode;
			 */
		} else
			$flag = 0;
	} elseif ($flow != "") {
		foreach ($sectionList as $key => $sec) {
			saveMapping ( $schoolCode, $cls, $sec, $ttCode, $flow );
		}
	}
	echo "<script>";
	echo "alert('" . $activeMessage . "');";
	echo "window.location = 'myClasses.php?ttCode=".$ttCode."&cls=".$cls."&section=".$section."&mytopicreferrel=1' ;";
	echo "</script>";
}

if (strcasecmp ( $flow, "Custom" ) == 0) {
	$clustersChosenArray = getClustersChosen ( $schoolCode, $cls, $section, $ttCode );
}
// Get the no of attempts on this topic for the class, if more than 0 i.e. students have started then disable the option of changing the mapping.
$noOfAttempts = getNoOfAttempts ( $schoolCode, $cls, $section, $ttCode );

$customizedTopic = isCustomizedTopic ( $ttCode );

if ($customizedTopic == "") {
	$query = "SELECT a.clusterCode, cluster, ms_level, cbse_level, icse_level,igcse_level, a.level, b.clusterType
		FROM   adepts_teacherTopicClusterMaster a, adepts_clusterMaster b
		WHERE  a.clusterCode=b.clusterCode AND status='Live' AND teacherTopicCode='$ttCode'
		ORDER BY a.flowno";
} else {
	$query = "SELECT a.clusterCode, cluster, ms_level, cbse_level, icse_level,igcse_level, a.level, b.clusterType
		FROM   adepts_teacherTopicClusterMaster a, adepts_clusterMaster b
		WHERE  a.clusterCode=b.clusterCode AND status='Live' AND teacherTopicCode='$customizedTopic'
		ORDER BY a.flowno";
}
$result = mysql_query ( $query );
$clusterDetails = array ();
$srno = 0;
$ms_clusters = $cbse_clusters = $icse_clusters = $igcse_clusters = 0;
$totalSdls = 0;
$avgTimePerSdl;
$objTT = new teacherTopic ( $ttCode, $cls, $_REQUEST ['flow'] ); // create the object for TTcode, class, flow combination: and childSection = '$childsection'

foreach($objTT->clusterFlowArray as $val){
	$passedArray[] = $val[0];
}
$teacherTopicDesc = $objTT->ttDescription;

while ( $line = mysql_fetch_array ( $result ) ) {
	$avgTimePerSdl = $objTT->avgTimePerSdl;
	
	$clusterCode = $line ['clusterCode'];
	$sdlPerCluster = $objTT->getNumberOfSDLs ( $clusterCode );
	$clusterDetails [$srno] [0] = $clusterCode;
	$clusterDetails [$srno] [1] = $line ['cluster'];
	$clusterDetails [$srno] [2] = '-'; // ms
	$clusterDetails [$srno] [3] = '-'; // cbse
	$clusterDetails [$srno] [4] = '-'; // icse
	$clusterDetails [$srno] [5] = '-'; // customized
	$clusterDetails [$srno] [6] = '-'; // MS Old flow
	$clusterDetails [$srno] [7] = $sdlPerCluster; // Storing no. of sdls per cluster.
	$clusterDetails [$srno] [8] = $line ['clusterType']; // Storing cluster type.
	$clusterDetails [$srno] [9] = '-'; // igcse
	$clusterDetails [$srno] [10] = getSDLDetails($clusterCode); // subdificultylevel
	$clusterLevel = $line ['ms_level'];
	$clusterLevelArray = explode ( ",", $clusterLevel );
	if (in_array ( $cls, $clusterLevelArray )) {
		$clusterDetails [$srno] [2] = 'Y';
		$ms_clusters ++;
	}
	else
	{
		if(count($clusterLevelArray)==1 && $clusterLevel!='')
			$clusterDetails [$srno] [2] = "Class ".$clusterLevel;
		else if($clusterLevel!='')
			$clusterDetails [$srno] [2] = "Classes ".str_ireplace(",",", ",$clusterLevel);
	}
	
	$clusterLevel = $line ['cbse_level'];
	$clusterLevelArray = explode ( ",", $clusterLevel );
	if (in_array ( $cls, $clusterLevelArray )) {
		$clusterDetails [$srno] [3] = 'Y';
		$cbse_clusters ++;
	}
	else if($clusterLevel!='')
	{
		$clusterDetails [$srno] [3] = str_ireplace(",",", ",$clusterLevel);;
	}
	
	$clusterLevel = $line ['icse_level'];
	$clusterLevelArray = explode ( ",", $clusterLevel );
	if (in_array ( $cls, $clusterLevelArray )) {
		$clusterDetails [$srno] [4] = 'Y';
		$icse_clusters ++;
	}
	else if($clusterLevel!='')
	{
		$clusterDetails [$srno] [4] = str_ireplace(",",", ",$clusterLevel);;
	}
	
	$clusterLevel = $line ['igcse_level'];
	$clusterLevelArray = explode ( ",", $clusterLevel );
	if (in_array ( $cls, $clusterLevelArray )) {
		$clusterDetails [$srno] [9] = 'Y';
		$igcse_clusters ++;
	}
	else if($clusterLevel!='')
	{
		$clusterDetails [$srno] [9] = str_ireplace(",",", ",$clusterLevel);;
	}
	
	if (strcasecmp ( $flow, "Custom" ) == 0 && in_array ( $clusterCode, $clustersChosenArray )) {
		$clusterDetails [$srno] [5] = 'Y';
	} /*else
		$clusterDetails [$srno] [5] = 0;*/
		
		// This is for the schools where the topics are activated as per the old flow
		// This case will happen during the transition to the new system, can be removed after all such cases are done with after some time
	$clusterLevel = $line ['level'];
	$clusterLevelArray = explode ( ",", $clusterLevel );
	if (in_array ( $cls, $clusterLevelArray )) {
		$clusterDetails [$srno] [6] = 'Y';
	}
	
	switch ($flow) {
		case "MS" :
			if ($clusterDetails [$srno] [2] == 'Y')
				$totalSdls += $clusterDetails [$srno] [7];
			break;
		case "CBSE" :
			if ($clusterDetails [$srno] [3] == 'Y')
				$totalSdls += $clusterDetails [$srno] [7];
			break;
		case "ICSE" :
			if ($clusterDetails [$srno] [4] == 'Y')
				$totalSdls += $clusterDetails [$srno] [7];
			break;
		case "Custom" :
			if ($clusterDetails [$srno] [5] == 'Y')
				$totalSdls += $clusterDetails [$srno] [7];
			break;
		case "MSOld" :
			if ($clusterDetails [$srno] [6] == 'Y')
				$totalSdls += $clusterDetails [$srno] [7];
			break;
		case "IGCSE" :
			if ($clusterDetails [$srno] [9] == 'Y')
				$totalSdls += $clusterDetails [$srno] [7];
			break;
	}
	
	$srno ++;
}
$totalClusters = count ( $clusterDetails );
$totalTimeForTopic = $totalSdls * $avgTimePerSdl;

$childClass = $class;

?>


<title>My Topics</title>
<meta charset="UTF-8">
<link href="css/common.css?version=1.0" rel="stylesheet" type="text/css">
<link href="css/myClasses.css?ver=3" rel="stylesheet" type="text/css">
<script src="libs/jquery-ui-1.11.2.js"></script>
<script src="libs/intro.js"></script>
<script src="libs/touchpunch.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript"
	src="../userInterface/libs/closeDetection.js"></script>
<script type="text/javascript" src="../userInterface/libs/prompt.js"></script>
<link href="../userInterface/css/prompt.css" rel="stylesheet" type="text/css">
<link href="../userInterface/css/activity/midClass.css" rel="stylesheet" type="text/css">
<script src="fancybox/jquery.fancybox.js"></script>
<link href="fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css">
<link href="teacherforum/introjs.css" rel="stylesheet" type="text/css">

<script>
	var langType = '<?=$language;?>';
	var gradeArray   = new Array();
	var sectionArray = new Array();
	var topicCodeArray   = new Array();
	var topicArray   = new Array();
	
	<?php
		for($i=0; $i<count($classArray); $i++)
		{
			echo "gradeArray.push($classArray[$i]);\r\n";
			echo "sectionArray[$classArray[$i]] = new Array(".$sectionArray[$classArray[$i]].");\r\n";
		}
	?>
	
	function load(){
		var fixedSideBarHeight = window.innerHeight;
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#fixedSideBar").css("height",fixedSideBarHeight+"px");
		$("#sideBar").css("height",sideBarHeight+"px");
		$("#classes").css("font-size","1.4em");
		$("#classes").css("margin-left","40px");
		$(".rectangle-right").css("display","block");
		$(".rectangle-right").css("margin-top","3px");
		if(($("div#searchResults").children().length)>1)
		$('#noTopics').css("display","none");
		else
		$('#noTopics').css("display","block");
		var category = '<?=$category;?>';
		var subcategory = '<?=$subcategory;?>';
		
		if(category.localeCompare('School Admin')==0 && subcategory.localeCompare('All')==0)
			$('.doAsStudent').css('display','none');
		else
			$('.doAsStudent').css('display','block');
	}
	function openActivateTab(){
		$(".arrow-black-side").css("visibility","visible");
	}
	function submitCheckBox()
	{
		setTryingToUnload();
		$("#generate").click();
	}
	function activateLimitOver()
	{		
		//$("#frmMain").submit();
		var prompts=new Prompt({
			text:'Mindspark does not allow more than 15 topics to be active at a time. <a href="<?=WHATSNEW?>helpManual/Too_many_active_topics_in_a_school_is_hazardous_to_a_child_s_health.pdf" target="_blank">Click here</a> to know why.',
			type:'alert',
			label1:'Deactive some topics',
			func1:function(){
				$("#actTopicCircle1").click();
				$("#prmptContainer_activateLimitOver").remove();
				showbutton();
			},
			promptId:"activateLimitOver"
		});
	}
	function topicClassDifference(childClass,classRange)
	{
		var prompts=new Prompt({
			text:'This topic is recommended for Grade '+classRange+'. You cannot activate this topic for Grade '+childClass+'. <a href="<?=WHATSNEW?>helpManual/Topics_not_recommended_for_the_current_grade_note.pdf" target="_blank">Click here</a> to know why.',
			type:'alert',
			label1:'Activate other topics',
			func1:function(){
				$("#prmptContainer_topicClassDifference").remove();
			},
			promptId:"topicClassDifference"
		});
	}
	function mindsparkHandshake()
	{
		var msgArr = new Array();
		var msgInfo="";
		msgArr["1"] = "start from beginning";
		msgArr["2"] = "continue from where last left off";
		msgArr["3"] = "start from customized point";
		var childClass = $("#selClass").val();
		if($("input[name=msFlowSel]:checked").val() == "activities")
		{
			var msg = "start with activities";
			if(confirm("Are you sure you want to "+msg+" ?"))
			{
				//window.location.href	=	"activities.php";
				$("#mode").val("ttSelection");
				$("#userType").val("teacherAsStudent");
				$("#mindsparkTeacherLogin").attr("action", "activities.php");
				$("#mindsparkTeacherLogin").submit();
			}
		}
		else if($("input[name=msFlowSel]:checked").val() == "questions")
		{
			var dispMsg	=	"";
			var msgInfo	=	"";
			var msg = msgArr[$("input[name=ttOptions]:checked").val()];
			var topicStart = $("input[name=ttOptions]:checked").val();
			var forceNew = (topicStart == 2)?"no":"yes";
			var clusterCode = $("input[name=clusterSelection]:checked").val();
			clusterCode = (topicStart != 3)?"":clusterCode;
			clusterCode = (topicStart != 3)?"":clusterCode;
			var forceFlow = $("#flow").val();
			forceFlow = (topicStart == 2)?"":forceFlow;
			if(msgInfo=="")
				dispMsg	=	"Are you sure you want to "+msg+" ?";
			else
				dispMsg	=	msgInfo;

			if(confirm(dispMsg))
			{
				$("#mode").val("ttSelection");
				$("#userType").val("teacherAsStudent");
				$("#forceNew").val(forceNew);
				$("#customClusterCode").val(clusterCode);
				$("#mindsparkTeacherLogin").attr("action", "controller.php");
				$("#mindsparkTeacherLogin").submit();
			}
		}
	}
	function handleCategoryChange(id){	
		$("#openTab").val(id);
	}
	$(document).ready(function(e) {


		$("#seeCustomize").click(function(){
			$("#friendlyNameForm").show();
			$("#hdCustom").show();
			$("#rdCustom").show();
			$("#rdCustom").removeAttr("disabled");
			$("#rdCustom").click();
			$("#seeCustomize,#activateTopic").hide();
			$(".btnSave").show();
			$("#rdMS,#rdCBSE,#rdICSE,#rdIGCSE").attr("disabled",true);
			$("#rdMS,#rdCBSE,#rdICSE,#rdIGCSE").hide();
				<?php if(!isset($_REQUEST["seeCustomize"])) { ?>
			$("#backToDefauld").show();
			<?php }else{ ?>
				$("#cancel").show();
			<?php } ?>
			
			$(".colspan-change").attr('colspan','7');
			for(var i=0; i<<?=$totalClusters?>; i++)
			{
				$('#rowCustom'+i).show();
			}
			$('html,body').animate({ scrollTop: 0 }, 'slow', function () {			});
		});
		$("#backToDefauld").click(function(){
			$("#friendlyNameForm").hide();
			$("#hdCustom").hide();
			$("#rdCustom").hide();
			$("#rdCustom").attr("disabled",true);
			/*	$("#saveCustom").show();*/
			$("#rd<?=$_REQUEST['flow']?>").prop("checked", true);
			<?php
			if(empty($_REQUEST["activateMode"]))
				echo '$("#seeCustomize").show();';
			else
				echo '$("#seeCustomize,#activateTopic").show();';

			if(isset($_REQUEST['activateButton']))
				echo '$("#activateTopic").show();'; 
			?>
			//$("#seeCustomize,#activateTopic").show();
			$(".btnSave").hide();
			
			$("#rdMS,#rdCBSE,#rdICSE,#rdIGCSE").show();
			<?php if($_REQUEST['openTab'] == 3){ ?>
			$("#rdMS,#rdCBSE,#rdICSE,#rdIGCSE").removeAttr("disabled");
			<?php } ?>
			$("#backToDefauld").hide();
			if(<?=$ms_clusters?> == 0)
			{
				$("#rdMS").attr("disabled",true);
			}
			if(<?=$cbse_clusters?> == 0)
			{
				$("#rdCBSE").attr("disabled",true);
			}
			if(<?=$icse_clusters?> == 0)
			{
				$("#rdICSE").attr("disabled",true);
			}
			if(<?=$igcse_clusters?> == 0)
			{
				$("#rdIGCSE").attr("disabled",true);
			}
			getEstimatedTimeToCompleteTopic('MS');
			$(".colspan-change").attr('colspan','6');
			for(var i=0; i<<?=$totalClusters?>; i++)
			{
				$('#rowCustom'+i).hide();
			}
			$('html,body').animate({ scrollTop: 0 }, 'slow', function () {			});
		});
		$("#flow").change(function(){
			$(".showAll").show();
			$(".clusterSelection").attr("checked",false);
			$(".showAll").removeClass("pb");
			if($(this).val()=="MS")
				$(".ms_level").hide();
			else if($(this).val()=="CBSE")
				$(".cbse_level").hide();
			else if($(this).val()=="ICSE")
				$(".icse_level").hide();
			else if($(this).val()=="IGCSE")
				$(".igcse_level").hide();
		});
		$(".radio").change(function(e) {
			$(".selectNavLab").removeClass("selectNavLab");
			$(this).parent().addClass("selectNavLab");
			if($(this).val() == "questions")
			{
				$("#startOptions").show();
				if($("input[name=ttOptions]:checked").val() == "1" || $("input[name=ttOptions]:checked").val() == "3")
					$("#moreOptions").show();
				else
					$("#moreOptions").hide();
			}
			else
			{
				$("#startOptions").hide();
				$("#moreOptions").show();
			}
	    });
		$("#submitBtn").click(function(e) {
			if($("input[name=msFlowSel]:checked").val() == "activities")
			{
				if($("#selClass").val() == "" || $("#flow").val() == "")
					alert("Select class and flow.");
				else
					mindsparkHandshake();
			}
			else if($("input[name=msFlowSel]:checked").val() == "questions")
			{
				if($("input[name=ttOptions]:checked").val() == 1 || $("input[name=ttOptions]:checked").val() == 3)
				{
					if($("#selClass").val() == "" || $("#flow").val() == "")
						alert("Select class and flow.");
					else if($("input[name=ttOptions]:checked").val() == 3)
					{
						if($("input[name=clusterSelection]:checked").length == 0)
							alert("Select starting cluster.");
						else
							mindsparkHandshake();
					}
					else
						mindsparkHandshake();
				}
				else if($("input[name=ttOptions]:checked").val() == 2)
				{
					mindsparkHandshake();
				}
			}
	    });
	    
		$("#currenScrollId").val(1);
		
		<?php
		if ($openTab == 1 || $openTab == "") {
			?>
			$("#1").addClass("textRed");
			$("#actTopicCircle1").addClass("red");
			$(".questionContainer").hide();
			$("#activatedTopic").show();
			$("#generatediv").show();
			$("#openTab").attr("value","1");
		<?php
		} else if ($openTab == 2) {
			?>
			$("#2").addClass("textRed");
			$("#actTopicCircle2").addClass("red");
			$(".questionContainer").hide();
			$("#activateTopicAll").show();
			$("#openTab").attr("value","2");
		<?php
		} else if ($openTab == 3) {
			?>
			$("#3").addClass("textRed");
			$("#actTopicCircle3").addClass("red");
			$("#openTab").attr("value","3");
		<?php
		}
		?>
		/*$("body").click(function() {
			$(".arrow-black-side").css("visibility","hidden");	
		});*/
		$(".actionsTab").click(function() {
			$(".arrow-black-side").css("visibility","hidden");
			$("."+$(this).attr("id")).css("visibility","visible");
		});
		$('#container').on('click', '*', function(e) {
		  if (!$(e.target).closest('.actionsTab').length) {
		    $(".arrow-black-side").css("visibility","hidden");
		  }
		});
// 		$(".smallCircle").click(function() {
// 			$(".questionContainer").hide();
// 			$("#"+$(this).closest("td").attr("class")).show();
// 			$(".smallCircle").removeClass("red");
// 			$(".pointer").removeClass("textRed");
// 			$(this).addClass("red");
// 			$(this).next().addClass("textRed");
// 			$("#openTab").attr("value",$(this).next().attr("id"));
// 			if($("#"+$(this).closest("td").attr("class")).css("display")=="none" || !$("#"+$(this).closest("td").attr("class")).css("display")){
// 				$("#topicActivated").show();
// 			}else{
// 				$("#topicActivated").hide();
// 			}
// 		});
// 		if($(".questionContainer").css("display")=="none"){
// 				$("#topicActivated").show();
// 			}else{
// 				$("#topicActivated").hide();
// 			}
		setSection('<?=$section?>');
		
		
		if($('.pagingTable').is(':visible') )
		{
			$('#searchClick').css('display','block');
		}

		if($( "#masterTopic" ).val() != '')
		{
			//$("#generatedragdiv").css("display","none");
			$("#alertMessage").attr('disabled','disabled');
			$('#alertMessage').attr('title', 'You can assign priority to topics only when the master topic field is set to all');
		}

		if (!$("#1" ).hasClass( "pointer textRed" )) {
			$("#generatedragdiv").css("display","none");
		}
		<?php if(isset($_REQUEST['activateButton']) && $_REQUEST['activateButton'] == 1)
		{ ?>
				$("#activateTopic").show();
	<?php 	
		if(count($topicsActivated)>=15){ ?>
	
			$("#activateTopic").attr("disabled", true);
			$("#activateTopic").css("opacity", "0.3");
			$("#activateTopic").attr("title", 'Mindspark does not allow more than 15 topics to be active at a time.');
			
	<?php	}
		}  
		else if(isset($_REQUEST['activateButton']) && $_REQUEST['activateButton'] == 0)
		{
			?> 
			$("#rdMS,#rdCBSE,#rdICSE,#rdIGCSE,#rdCustom").attr("disabled",true);
	<?php } ?>
	});
	
	function removeAllOptions(selectbox)
	{
		var i;
		for(i=selectbox.options.length-1;i>0;i--)
		{
			selectbox.remove(i);
		}
	}
	
	function showMapping(ttCode, cls, section,flow,redirectTTCode,redirectFlow,gradeRange)
	{
		window.location = "mytopics.php?ttCode="+ttCode+"&cls="+cls+"&section="+section+"&flow="+flow+"&interface=new&gradeRange="+gradeRange+"&seeCustomize=1&redirectTTCode="+redirectTTCode+"&redirectFlow="+redirectFlow+"&openTab=<?=$_REQUEST['openTab']?>";
	}
	function showMappingOnCancel(ttCode, flow, cls, section,gradeRange)
	{
		window.location = "mytopics.php?ttCode="+ttCode+"&cls="+cls+"&section="+section+"&flow="+flow+"&interface=new&gradeRange="+gradeRange+"&openTab=<?=$_REQUEST['openTab']?>";
	}

	function filterTopicWise(data)
	{
		if(data == 'true')
		{
			$('#checkflag').val('1');
		}
		if(document.getElementById('topicCategory').value=="0"){
			alert("Please select Topic Category.");
			document.getElementById('topicCategory').focus();
			return false;
		}
		if(document.getElementById('lstClass').value=="")
		{
			alert("Please select a Class!");
			document.getElementById('lstClass').focus();
			return false;
		}else if(document.getElementById('lstSection').value=="" && $(".noSection").is(":visible"))
		{
			alert("Please select a Section!");
			document.getElementById('lstSecton').focus();
			return false;
		}
		else{
			document.getElementById('searchingTerm').value = document.getElementById('name').value;
			setTryingToUnload();
			document.forms["frmMain"].submit();
		}
	}
	
	function setSection(sec)
	{
		var cls = document.getElementById('lstClass').value;
		if(document.getElementById('lstSection'))
		{
			var obj = document.getElementById('lstSection');
			removeAllOptions(obj);
			if(cls=="")
			{
				document.getElementById('lstSection').style.display = "inline";
				document.getElementById('lstSection').selectedIndex = 0;
			}
			else
			{
				for(var i=0; i<gradeArray.length && gradeArray[i]!=cls; i++);
				if(sectionArray[gradeArray[i]].length>0)
				{
					$(".noSection").show();
					for (var j=0; j<sectionArray[gradeArray[i]].length; j++)
					{
						OptNew = document.createElement('option');
						OptNew.text = sectionArray[gradeArray[i]][j];
						OptNew.value = sectionArray[gradeArray[i]][j];
						if(sec==sectionArray[gradeArray[i]][j])
						OptNew.selected = true;
						obj.options.add(OptNew);
					}
				}
				else
				{
					$(".noSection").hide();
				}
			}
		}
	}
	
</script>

<script>
function trackTeacher1(code,type)
{
	switch(type){
		case 'activity':
			var pageId=75;break;
		case 'timeTest':
			var pageId=76;break;
		case 'remedial':
			var pageId=212;break;
		case 'dailyPractice':
			var pageId=213;break;
	}
	var childClass=$('#childClass').val();
	var theme=childClass>7?3:(childClass<4?1:2);
	$.ajax({
	    url: "ajaxRequest.php",
	    data: "pageId="+pageId+"&theme="+theme+"&childClass="+childClass+"&mode=doasActivity",
	    type: "POST",
	    async: false,
	    success: function(data){
			if(data == "multitab")
			{
				window.location.href = "../userInterface/newTab.php";
			}
			else if(type == 'activity')
			{
				url = "../userInterface/enrichmentModule.php?gameID="+code;
				window.open(url, '_blank');
			}
			else if(type == 'timeTest')
			{
				url = "../userInterface/timedTest.php?timedTest="+code+"&tmpMode=sample";
				window.open(url, '_blank');
			}
			else if(type == 'remedial')
			{
				url = "../userInterface/remedialItem.php?qcode="+code;
				window.open(url, '_blank');
			}
			else if(type = 'dailyPractice'){
				// url = "../userInterface/question.php";
				// window.open(url, '_blank');
				//doMindspark();
				$("#clusterCode").val(code);
				$("#userType").val("teacherAsStudent");
				$("#mindsparkTeacherLogin").attr("action", "../userInterface/practisePage.php?");
				setTryingToUnload();
				$("#mindsparkTeacherLogin").submit();	
			}
	    }
	});
}
function getQuestionForSDL(clusterCode,subDifficultyLevel,ttcode,classDetail,flow,misconceptoinString){
	var data = "mode=getQuestionBySDL&clusterCode="+clusterCode+"&subDifficultyLevel="+subDifficultyLevel+"&ttcode="+ttcode+"&classDetail="+classDetail+"&flow="+flow+"&misconceptoinString="+misconceptoinString;
	$.ajax({
	      url: '../userInterface/commonAjax.php',
	      type: 'post',
	      data: data,
	      beforeSend:function(){
		      $("#pnlLoading").show();
		      },
	      success: function(response) {
	    	  $("#pnlLoading").hide();
		      $("#showQuestionDiv").html(response);
		      $("#showQuestionDiv :input").prop("disabled", true);
		      $("#showQuestionDivClick").trigger("click");
		      
	      }
	    });
    
	
}

function doMindspark(){
	$.ajax({
		url: "ajaxRequest.php",
		data: "pageId=80&mode=doasMindsparkSampleQuestion",
		type: "POST",
		async: false,
		success: function(data){
		}
	});


	if($("#forceFlow").val()=="")
	{
		alert("Sorry not able to start the session.");
		return false;
	}
	$("#mode").val("ttSelection");
	$("#userType").val("teacherAsStudent");
	$("#mindsparkTeacherLogin").attr("action", "../userInterface/controller.php");    		
	setTryingToUnload();
	$("#mindsparkTeacherLogin").submit();	
}
    $(document).ready(function(){
    	$(".top-right-domindspark").live("click",function(){
    		doMindspark();
    	});
        $("#showQuestionDivClick").fancybox({
        	wrapCSS : 'showQuestionSDLDiv'
         });
        $(function(){
            $('span.clickMe').click(function(e){
				if(document.getElementById('lstClass').value=="")
				{
					alert("Please select a Class!");
					document.getElementById('lstClass').focus();
					return false;
				}else if(document.getElementById('lstSection').value=="" && $(".noSection").is(":visible"))
				{
					alert("Please select a Section!");
					document.getElementById('lstSecton').focus();
					return false;
				}
                var hiddenSection = $('section.hidden');
                hiddenSection.fadeIn("slow","linear")
                    // unhide section.hidden
                    .css({ 'display':'block' })
                    // set to full screen
                    .css({ width: $(window).width() + 30 + 'px', height: $(window).height() + 'px' })
                    .css({ top:($(window).height() - hiddenSection.height())/2 + 'px', 
                        left:($(window).width() - hiddenSection.width())/2 + 'px' })
                    // greyed out background
                    .css({ 'background-color': 'rgba(0,0,0,0.5)' })
                    .appendTo('body');
                    // console.log($(window).width() + ' - ' + $(window).height());
                    $('span.close').click(function(){ $(hiddenSection).fadeOut(); });
					
					$('#noTopics').css("display","none");
					
					
					<?php if($searchTerm=="") { ?>
					$('#token-input-name').focus();
					<?php } ?>
					
					$('html, body').css({
					    'overflow': 'hidden'
					})
					
					$('body').css({
					    'position': 'absolute'
					})
            });
        });
 
    });
	
	function closeSearch()
	{
		$('#generate').click();
	}
function activateDeactivateTopic(ttCode,schoolCode,cls,section,flow,modifiedBy,mode,idRemove,notCovered,fromDate,toDate)
{
	if (typeof notCovered === "undefined" || notCovered === null) { 
      notCovered =0; 
    }
    if (typeof fromDate === "undefined" || fromDate === null) { 
      fromDate = ''; 
    }
    if (typeof toDate === "undefined" || toDate === null) { 
      toDate = ''; 
    }
	flow = $("input:radio[name=rdTTFlow]:checked").val();
	var minClass = "";
	var maxClass = "";
	<?php
	$gradeRangeArr = explode("-",$_REQUEST["gradeRange"]);
	if($gradeRangeArr[0]!="") 
    	echo "minClass=".$gradeRangeArr[0].";";
	if($gradeRangeArr[1]!="") 
		echo "maxClass=".$gradeRangeArr[1].";";
	?>
	var sectionList=section.split(',');
	var forSections=[];
	for(var i=0;i<sectionList.length;i++){
		if ($.trim(sectionList[i])!="")
			forSections.push(cls+'-'+sectionList[i]);
		else 
			forSections.push(cls);
	}
	var msg = "Do you want to activate this topic for class"+(forSections.length>1?"es ":" ")+forSections.join(',')+"?";
	if((cls < minClass && minClass !="") || (cls > maxClass && maxClass !=""))
		var msg = "Please note that these learning units are recommended for grade <?=$_REQUEST["gradeRange"]?>. Are you sure you want to activate it?";
	if(confirm(msg))
	{
		var linkTo	=	"ajaxRequest.php?mode="+mode+"&ttCode="+ttCode+"&schoolCode="+schoolCode+"&cls="+cls+"&section="+section+"&flow="+flow+"&modifiedBy="+modifiedBy+"&notCovered="+notCovered+"&fromDate="+fromDate+"&toDate="+toDate;
		$.post(linkTo,function(data){
			alert(data);
			console.log(data);
			return false;
			section = $('#lstSection').val()!=""?$('#lstSection').val():sectionList[0];
			window.location="myClasses.php?ttCode="+ttCode+"&cls="+cls+"&section="+section;
			
		});
	}
}
function openActivateDeactivatePrompt(this1,ttCode,schoolCode,cls,section,flow,modifiedBy,mode,idRemove,sectionList,idADPrompt,isValidate,durationPopup,reactivate){
	var action=mode=='deactivate'?'getDeactivationList':'getActivationList';
	var dataToSend={'mode': action,'ttCode':ttCode,'cls':cls,'flow':flow,'sectionList':sectionList};
	var diffTop=0;
	var openTab= reactivate;
	
	console.log('inside openActivaateDeactivatePrompt');
	if (isValidate){
		var flow1;
		var objArray = document.forms['frmTeacherTopicFlow'].elements['rdTTFlow'];
		radioLength = objArray.length;
		for(var i = 0; i < radioLength; i++) {
			if(objArray[i].checked) {
				flow1 = objArray[i].value;
				break;
			}
		}
		if(flow1!="Custom" && flow!="MS" && flow!="CBSE" && flow!="ICSE" && flow!="IGCSE"){
			alert("Please select custom flow.");
			return false;
		}
		var clusterList =[];
		$("input[name='chkCluster[]']:checked").each(function(){
			clusterList.push($(this).val());
		});
		if (flow1=="Custom") {
			if (clusterList.length==0){
				alert("Please choose at least one cluster!");
				return false;
			}
			dataToSend['clusterList']=clusterList.join(',');
		}
		dataToSend['flow']=flow1;
		diffTop=44;
	}
 	var ADPrompt = $(this1).prevAll('.bulkActivateDeactivatePrompt');
 	$(ADPrompt).find("*").html('');setTimeout(function(){$(ADPrompt).show();},500);
 	$(ADPrompt).find(".bulkADMain").html('<img src="assets/loadingTiny.gif" width="50px" height="50px" style="margin:auto;display: block;">');
 	$(ADPrompt).css('top','-'+($(ADPrompt).outerHeight()+10-diffTop)+'px');
 	$.ajax({
       url: 'ajaxRequest.php',
       type: 'post',
       data: dataToSend,
       success: function(response) {
       		var responseArray='';
	       	try{
	       		var responseArray=JSON.parse(response);
	       	}
	       	catch(er){}
	       	if (responseArray==""){
	       		alert('Unable to '+(mode)+' topic now.');
	       		$(ADPrompt).hide();
	       		return;
	       	}	       	
	       	if((mode != 'activate' && durationPopup==1) || durationPopup == 0)
	       	{
	       		if(Object.keys(responseArray).length==1 && typeof responseArray[section]!='undefined')
	       		{
		       		if (isValidate)
		       			validate(1);
		       		else 
		       			activateDeactivateTopic(ttCode,schoolCode,cls,section,flow,modifiedBy,mode,idRemove);
		       		return;
		       	}
		       	else if(Object.keys(responseArray).length==1)
		       	{
		       		alert('Unable to '+(mode)+' topic now.');
		       		$(ADPrompt).hide();
		       		return;
		       	}
	       	}
	       	
	       	sectionRow='<div class="section-div"><label><input type="checkbox" class="selectAllSections" value="allSections"><span>All</span></label>';
	       	var sectionsL=sectionArray[cls];
	       	for(var u=0;u<sectionsL.length;u++){
	       		var thisRow='<label ';var enCB=true;
	       		progress=responseArray[sectionsL[u]];
	       		if(mode=='activate'){
	       			thisRow+=(progress==-1?'class="inactive" title="One or more learning units selected are already covered for this section. To enable it, you should deactivate the topic where it is currently selected." ':(progress==-2?'class="inactive" title="This section already has 15 active topics activated. Please deactivate a topic to activate a new topic." ':(!progress?'class="inactive" title="This topic is already activated for this section." ':'')));
	       			if(!progress){enCB=false;}
	       			else if (progress==-1 || progress==-2){enCB=false;}
	       			else {enCB=true;}
	       		}
	       		thisRow+='><input type="checkbox" class="checkboxSection" '+(sectionsL[u]==section && enCB?'checked':'')+(!enCB?' disabled ':'')+' value="'+sectionsL[u]+'"><span>'+sectionsL[u]+(mode=='deactivate'?" ("+Math.round(progress)+'%)':"")+'</span></label>';
	       		if(mode=='activate' || (mode=='deactivate' && typeof progress!='undefined')){ sectionRow+=thisRow;}
	       	}
	       
	       	sectionRow+='</div><br>';
	       	var dateRow='';
	       	if(durationPopup == 1 && mode=='activate' && openTab != 1)
	       	{
	       		var dateRow= 'Duration of teaching this in class:<br><span class="durationFromTo"> <label> From: </label> <input size="10" id="fromDate" readonly="readonly" maxlength="10" > <label class="durationTo"> To: </label> <input size="10" id="toDate" readonly="readonly"  maxlength="10" >  <span class="durationHelp" title="Providing the duration in which the selected LUs were covered in class will help in synchronizing Mindspark with your classroom teaching" onclick="notifyTooltip()"><label >?</label></span></span><br><input type="checkbox" id="notCovered" onchange=checkCovering()><span class="notCovering">Not covering in classroom </span><br>';		 
	       	}
		       
		    if(Object.keys(responseArray).length==1 && durationPopup == 1 && mode=='activate')
		    {
		    	$(ADPrompt).find(".bulkADMain").html(dateRow);
		    }
		    else
		    {
		    	$(ADPrompt).find(".bulkADMain").html("Do you want to "+mode+" this topic for other sections as well?<br>Choose sections:<br>"+sectionRow+dateRow);
		    	if($('.section-div label.inactive').length==$('.checkboxSection').length)
		    		$('.section-div input.selectAllSections').attr('disabled','disabled').parent().addClass('.inactive');

		    }
       		
       		var helpText=mode=='deactivate'?'It is recommended to deactivate a topic only after a section reaches a progress of 75%.':'';
       		$(ADPrompt).find(".bulkADHelp").html(helpText);
       		var buttonText=mode=='deactivate'?'Deactivate Now':'Activate Now';
       		var bulkADAction=$(ADPrompt).find(".bulkADActionButton").html('');
       		$('<a href="#" class="bulkButton">'+buttonText+'</a>').appendTo(bulkADAction).click(function(){
       			var sections=[];
       			var durationCount = 0;
       			var fromDate = '';
       			var toDate = '';
       			var notCovered = 0 ;       			
       			if(Object.keys(responseArray).length==1)
			    {
			    	sections.push(Object.keys(responseArray));
			    }
			    else
			    {
			    	$(ADPrompt).find(".checkboxSection:checked").each(function(i,item){sections.push($(item).val());});
	       			if (sections.length==0){
	       				if(mode == 'activate')
	       				{
	       					alert('Please select at least one section to '+mode+' the topic. Note that some section(s) may be grayed out if one or more learning units are already covered for the section(s).');return;
	       				}
	       				else
	       				{
	       					alert('Please select at least one section to '+mode+' the topic.');return;
	       				}
	       				
	       			}
			    }
       			
       			if(mode=='activate' && durationPopup==1 && openTab != 1)
       			{
       				if($("#notCovered").is(':checked'))
       				{
       					notCovered = 1;
       				}
       				else
       				{       					
       					fromDate = $("#fromDate").val();
	       				toDate = $("#toDate").val();
	       				if(fromDate == '')
	       				{
	       					$("#fromDate").addClass('durationBorder');
	       					durationCount++;
	       				}       				
	       				if(toDate == '')
	       				{
	       					$("#toDate").addClass('durationBorder');
	       					durationCount++;
	       				}
       				}       				
       				
       			}
       			if(durationCount != 0)
       			{
       				return false;
       			}
       			if (isValidate){
       				$('#frmTeacherTopicFlow #sectionList').val(sections);
       				validate(1,notCovered,fromDate,toDate);
       			}
       			else
       				activateDeactivateTopic(ttCode,schoolCode,cls,sections.join(','),flow,modifiedBy,mode,idRemove,notCovered,fromDate,toDate);
       		});
       		$(ADPrompt).css('top','-'+($(ADPrompt).outerHeight()+10-diffTop)+'px');
       }
    });
}
</script>

<script type="text/javascript" src="libs/jquery.tokeninput_search.js"></script>
<link rel="stylesheet" type="text/css"
	href="css/token-input-facebook_search.css" />

<script type="text/javascript">

$(document).ready(function () {
	var rs = "";
	var searchedTerm = "";
	var selectClass = "<?php echo $childClass ?>";
	var flow = "<?php echo $defaultFlow ?>";
    $("#name").tokenInput("getautocomplete.php?class="+selectClass+"&flow="+flow,{
                hintText:"Search a topic/learning unit",
				theme : "facebook",
				searchingText : "Mindspark is searching...",
				noResultsText : "No similar topic found",
				tokenLimit : 1,
				preventDuplicates: true,
				onAdd: function (item) {
					
					searchedTerm = document.getElementById('name').value;					
					var linkTo	='ajaxRequest.php?mode=searchLog&searchTerm='+rs+'&searchResult='+searchedTerm;
					$.get(linkTo,function(data){
						/*alert(data);*/
						$("#generate").click();
					});
					
                },
				onResult: function (results) {
				
					var tokenInput = document.getElementById('token-input-name');
					rs = tokenInput.value;
                    $('div.token-input-dropdown-facebook').css('height','400px');
					rs = rs.replace(/\W+/g, " ");
					if(rs == " ")
					{
						alert("Enter valid topic name");
						$('#token-input-name').html("");
						$('#searchClick').click();
						return false;
					}
					return results;
                }				
            });

	//For show/hide of customized topics
$(".slidingDiv").hide();
	$(".show_hide").show();
	
	$('.show_hide1').click(function(){
	$("#collapsibleTopicWrapper1").slideToggle();
	});

	$('.show_hide2').click(function(){
	$("#collapsibleTopicWrapper2").slideToggle();
	});

	$('.show_hide3').click(function(){
	$("#collapsibleTopicWrapper3").slideToggle();
	});

	$('.show_hide4').click(function(){
	$("#collapsibleTopicWrapper4").slideToggle();
	});
	
});

/*$(document).keyup(function(e) {
  if (e.keyCode == 27) { $('.hidden').hide() }   // esc
  $('#generate').click();
});*/
</script>
<script>
$(document).ready(function(){
<?php if($_REQUEST['flow'] == ''){ ?>
jQuery(function(){
	jQuery("#rdMS,#rdCBSE,#rdICSE,#rdIGCSE").hide();
});
<?php } ?>

<?php if(!isset($_REQUEST["activateMode"])) { ?>
	<?php if(isset($_REQUEST["seeCustomize"])) { ?>
			jQuery("#seeCustomize").hide();
	<?php } ?>

setTimeout(function() {
	/*jQuery("#rdCustom").click();*/
	/*jQuery("#rdMS,#rdCBSE,#rdICSE,#rdIGCSE,#rdCustom").attr("disabled",true);*/
	<?php if(isset($_REQUEST["seeCustomize"])) { ?>

	jQuery("#seeCustomize").click();
	
	<?php } ?>
},1000);
<?php } ?>
});

$(window).load(function(){
	
	<?php
	if(!isset($_REQUEST["activateMode"])) { ?>
		/*jQuery("#rdCustom").click();*/
		/*jQuery("#rdMS,#rdCBSE,#rdICSE,#rdIGCSE,#rdCustom").attr("disabled",true);*/
	<?php } ?>
	
});
</script>



<script>
$(function() {

$( "#sortable2" ).sortable({
stop: function(event, ui) {
        var i = 1;
		  var updatelist ='';
	
		var array = $('#sortable2 .topicContainer').map(function(){
		updatelist = $(this).attr('value');
		
		$("#"+updatelist+"box").html(i);
		i++;
		}).get();
    }
});
$( "#sortable1 li, #sortable2 li" ).disableSelection();

});
</script>

<script>
function saveids(cls,section,flag)
{
	
	document.getElementById('waitingimage').style.display = 'block';
	var idlist ='';
	
	var array = $('#sortable2 .topicContainer').map(function(){
		idlist += $(this).attr('value')+',';
		//$("#"+idname+i).html(i);
    return $(this).attr('value');
}).get();

	

	$.ajax({
      url: 'myClasses.php',
      type: 'post',
      data: {'action': 'savepriority','ttlist':idlist,'cls':cls,'section':section},
      success: function(response) {
		  document.getElementById('waitingimage').style.display = 'none';
		  if(flag == 'true')
		  {
			alert("Priority saved sucessfully");
		  }

		  var i = 1;
		  var updatelist ='';
	
		var array = $('#sortable2 .topicContainer').map(function(){
		updatelist = $(this).attr('value');
		
		$("#"+updatelist+"box").html(i);
		i++;
		}).get();
		//$("#"+idname+i).html(i);
			//$(TT0372).html("koko");
		 filterTopicWise('false');
      }
    }); // end ajax call
}

function showMessage()
{
	var prompts=new Prompt({
			text:'Drag and Drop the topics to decide the order in which topics are visible to your students. \nThis will help them work on the  most important topics first. \n Click "save priority" to save.',
			type:'alert',
			label1:'OK',
			func1:function(){
				$("#prmptContainer_popupDisplay").remove();
				filterTopicWise('true');
			},
			promptId:"popupDisplay"
		});
}

function showbutton()
{
	document.getElementById('generatedragdiv').style.display = 'block';
	if($( "#masterTopic" ).val() != '')
	{
		$("#alertMessage").attr('disabled','disabled');
		$('#alertMessage').attr('title', 'You can assign priority to topics only when the master topic field is set to all');
	}
}

function hidebutton()
{
	document.getElementById('generatedragdiv').style.display = 'none';
}

</script>
<style>
	.bulkActivateDeactivatePrompt{
		display: none;
	    position: absolute;
	    width: 370px;
	    background-color: #30302F;
	    color: #fff;
	    padding: 5px;
	    font-size: 1.3em;
	    line-height: 1.3em;
	    text-align: left;
	    left: 150px;
        top: -70px;
        z-index: 999;
        font-family: Calibri, Tahoma, sans-serif;
	} 
	.bulkActivateDeactivatePrompt:after {
	    border-left: 10px solid transparent;
	    border-right: 10px solid transparent;
	    content: '';
	    position: absolute;
	    bottom: -10px;
	    left: 183px;
	    width: 0px;
	    height: 0px;
	    border-top: 10px solid #30302F;
	}
	.bulkActivateDeactivatePrompt .bulkADMain{
		line-height: 1.3em;
		margin-bottom: 5px;
		padding: 5px;
	}
	.bulkActivateDeactivatePrompt .bulkADMain .section-div{
		margin-top: 5px;
	}
	.bulkActivateDeactivatePrompt .bulkADHelp{
		font-style: italic;
		font-size: 0.9em;
	}
	.bulkActivateDeactivatePrompt .bulkADActionButton{
		text-align: right;
		margin-right: 10px;
	}
	.bulkActivateDeactivatePrompt .bulkADActionButton .bulkButton{
		text-decoration: none;
		color: #9FCB50;
    	font-weight: bold;
	}
	.bulkADActionButton .bulkButton.white{
		color: #fff;
	}
	.bulkActivateDeactivatePrompt .durationFromTo{
		color: #9FCB50;
		margin-top: 5px;
		float: left;
        margin-bottom: 5px;  
        width: 100%;	
	}
	.bulkActivateDeactivatePrompt #notCovered{
		float: left;
		margin: 0 3px !important;
		height: 19px;
	}

	.bulkActivateDeactivatePrompt .notCovering
	{
		color: #f26722;
		float: left;
		margin-top: -1px;
	}
	.bulkActivateDeactivatePrompt .durationTo
	{
		padding-left: 5px;
	}
	.bulkActivateDeactivatePrompt .durationBorder
	{
		border: 2px solid red;		
    	border-style: inset;
	}
	.bulkActivateDeactivatePrompt .durationHelp
	{
		border: 2px solid #9FCB50;
	    border-radius: 12px;
	    width: 20px;   
	    text-align: center;
	    display: inline-block;
	    margin-left: 10px;
	}
	.section-div label input[type="checkbox"]{
		display: none;
	}
	.section-div label{
		margin: 3px;
		text-align: center;
		cursor: pointer;
		display: inline-block;
	}
	.section-div label span{
	    text-align:center;
	    padding:3px 3px;
	    display:block;
	    background-color:#fff;
	    color:#000;
	    font-weight: bold;
	    min-width: 40px;
	}
	.section-div input:checked + span {
	    background-color:#9FCB50;
	    color:#000;
	}
	.section-div input:disabled + span {
	    color:#AAA;
	}
</style>
</head>
<body class="translation" onLoad="highlightSelection('<?=$flow?>',<?=$totalClusters?>,<?=$noOfAttempts?>);" onResize="load()">
<div name="pnlLoading" id="pnlLoading">
<div align="center" class="quesDetails" style="margin-top: 20%;">
	<p>
		Loading, please wait...<br>
		<img src="../userInterface/assets/loader.gif">
	</p>
</div>
</div>
	<?php include("eiColors.php");?>
	<div id="fixedSideBar">
		<?php include("fixedSideBar.php");?>
	</div>
	<div id="topBar">
		<?php include("topBar.php");?>
	</div>
	<div id="sideBar" class="my-topic-page">
		<?php include("sideBar.php");?>
	</div>
	<form target='window.parent' name="mindsparkTeacherLogin" id="mindsparkTeacherLogin" action="" method="post">
		<input type="hidden" name="mode" id="mode" value="">
		<input type="hidden" name="sessionID" id="sessionID" value="<?=$_SESSION["sessionID"]?>">
	    <input type="hidden" name="childClass" id="childClass" value="<?=$_REQUEST['cls']?>">
	    <input type="hidden" name="userType" id="userType" value="teacherAsStudent">
	    <input type="hidden" name="forceNew" id="forceNew" value="">
	    <input type="hidden" name="ttCode" id="ttCode" value="<?=$ttCode?>">
	    <input type="hidden" name="customClusterCode" id="customClusterCode" value="<?=$_REQUEST['learningunit']?>">
	    <input type="hidden" name="clusterCode" id="clusterCode" value="<?=$_REQUEST['learningunit']?>">
	    <input type="hidden" name="forceFlow" id="forceFlow" value="<?=$_REQUEST['flow']?>">
	    <input type="hidden" name="startPoint" id="startPoint" value="">
	</form>
	<div id="container" class="topics-page-main-class">
		<div id="trailContainer">
	<table id="childDetails">
		<tbody>
			<tr>
				<td width="33%" class="activatedTopic" ><div style="cursor:pointer;" class="smallCircle red" ></div><label class="pointer textRed" style="cursor:pointer;">Topic Page / Research</label></td>
	        	<td width="33%" class="activateTopicAll" ><a href="topicReport.php?schoolCode=<?= $schoolCode;?>&cls=<?=$cls?>&sec=<?=$section?>&topics=<?=$ttCode?>&mode=0&topicName=<?= rawurlencode($teacherTopicDesc);?>" style="text-decoration: none;"><div  style="cursor:pointer;" class="smallCircle" ></div><label class="pointer" style="cursor:pointer;">Topic Report</label></a></td>
	        	<td width="33%" class="activateTopics" ><a href="topicProgress.php?cls=<?=$cls?>&section=<?=$section?>&ttCode=<?=$ttCode;?>" style="text-decoration: none;"><div  style="cursor:pointer;" class="smallCircle" ></div> <label class="pointer" style="cursor:pointer;">Topic Progress Report</label></a></td>
			</tr> 
		</tbody>
	</table>
			<?php include("topicSearch.php");?>
				<div id="customizeTbl" align="center" style="float: left;border-right: 2px solid #e75903;" data-intro="This table allows you to see, activate or customize topics easily.">
				<div id="pageName" style="float: left;width:100%">
					<div class="arrow-black"></div>
					<div id="pageText"><?= $teacherTopicDesc." (Class-$cls)"?> &nbsp; <a class="intro-launch" onClick="introJs().start();" style="float:right;"></a></div> 
					<div class="top-right-domindspark" data-intro="The button readily allows you to do Mindspark the way children do it.">Do Mindspark</div>
				</div>
				<form id="frmTeacherTopicFlow" name="frmTeacherTopicFlow"
					method="post" action="<?=$_SERVER['PHP_SELF']?>" style="float: left;">
					<input type="hidden" id="ttCode" name="ttCode" value="<?=$ttCode?>">
					<input type="hidden" id="cls" name="cls" value="<?=$cls?>">
				    <input type="hidden" id="section" name="section" value="<?=$section?>">
					<input type="hidden" id="sectionList" name="sectionList" value="">
					<input type="hidden" id="fromDateValue" name="fromDate" value="">
					<input type="hidden" id="toDateValue" name="toDate" value="">
					<input type="hidden" id="notCoveredValue" name="notCovered" value="">

		
		<?php
		if (isset ( $flag ) && $flag == 0) {
			echo "<div style='font-color:red'>Please select atleast one cluster!</div>";
		}
		if ($totalClusters > 0) {
			$arrayToString = "";
			for($i = 0; $i < $totalClusters; $i ++) {
				$arrayToString .= implode ( "##", $clusterDetails [$i] );
				$arrayToString = $arrayToString . "~";
			}
			$arrayToString = substr ( $arrayToString, 0, - 1 );
			?>
			<input type="hidden" name="clusterDetailsStr" id="clusterDetailsStr"
						value="<?=$arrayToString?>"> <input type="hidden"
						name="avgTimePerSdl" id="avgTimePerSdl"
						value="<?=$avgTimePerSdl?>">
					<table cellspacing="3" cellpadding="3" border="0" width="90%"
						class="tblContent">
						<tr>
							<th class="customPoint"></th>
							<th align="center" class="header">Sr.No.</th>
							<th align="left" class="header">Learning Unit</th>
							<th align="center" id="hdMS" class="header">Mindspark Recommended<br />
								
								<input type="radio" value="MS" <?=$chk?> id="rdMS" name="rdTTFlow" <?php echo ($flow=='MS' ? 'checked' : '');?>
								onClick="highlightSelection('MS',<?=$totalClusters?>,<?=$noOfAttempts?>); getEstimatedTimeToCompleteTopic('MS');"
								<?php if($ms_clusters==0) echo " disabled";?>
								<?php if($customizedTopic!="") echo " disabled"; ?>></th>
							<th align="center" id="hdCBSE" class="header">CBSE<br /> <input
								type="radio" value="CBSE" id="rdCBSE" name="rdTTFlow" <?php echo ($flow=='CBSE' ? 'checked' : '');?>
								onClick="highlightSelection('CBSE',<?=$totalClusters?>,<?=$noOfAttempts?>); getEstimatedTimeToCompleteTopic('CBSE');"
								<?php if($cbse_clusters==0) echo " disabled";?>
								<?php if($customizedTopic!="") echo " disabled"; ?>></th>
							<th align="center" id="hdICSE" class="header">ICSE<br /> <input 
								type="radio" value="ICSE" id="rdICSE" name="rdTTFlow" <?php echo ($flow=='ICSE' ? 'checked' : '');?>
									onClick="highlightSelection('ICSE',<?=$totalClusters?>,<?=$noOfAttempts?>); getEstimatedTimeToCompleteTopic('ICSE');"
								<?php if($icse_clusters==0) echo " disabled";?>
								<?php if($customizedTopic!="") echo " disabled"; ?>></th>
							<th align="center" id="hdIGCSE" class="header">IGCSE<br /> <input
								type="radio" value="IGCSE" id="rdIGCSE" name="rdTTFlow" <?php echo ($flow=='IGCSE' ? 'checked' : '');?>
								onClick="highlightSelection('IGCSE',<?=$totalClusters?>,<?=$noOfAttempts?>); getEstimatedTimeToCompleteTopic('IGCSE');"
								<?php if($igcse_clusters==0) echo " disabled";?>
								<?php if($customizedTopic!="") echo " disabled"; ?>></th>
							
							<th align="center" id="hdCustom" class="header" <?php if(isset($_REQUEST["activateMode"]) && $_REQUEST["activateMode"]=="yes") echo "style='display:none'"?>>Customized<br />
								<input type="radio" value="Custom" id="rdCustom" name="rdTTFlow" <?php echo ($flow=='Custom' ? 'checked' : '');?>
								onClick="highlightSelection('Custom',<?=$totalClusters?>,<?=$noOfAttempts?>); getEstimatedTimeToCompleteTopic('Custom'); checkIfclustersAreCustomized('Custom');"
								<?php if($customizedTopic!="" || (isset($_REQUEST["activateMode"]) && $_REQUEST["activateMode"]=="yes")) echo " disabled"; ?>></th>
					<?php if($flow=="MSOld") { ?>
					<th align="center" id="hdMSOld" class="header">Mindspark Old<br />
								<input type="radio" value="" id="rdMSOld" name="rdTTFlow"
								onClick="highlightSelection('MSOld',<?=$totalClusters?>,<?=$noOfAttempts?>); getEstimatedTimeToCompleteTopic('MSOld');"
								<?php if($customizedTopic!="") echo " disabled"; ?>></th>
					<?php } ?>
						</tr>
				<?php
			$msTotalLevel = 0;
			$cbseTotalLevel = 0;
			$icseTotalLevel = 0;
			$igcseTotalLevel = 0;
			$colSpan = (isset($_REQUEST["activateMode"]) && $_REQUEST["activateMode"]=="yes") ? '6':'7';
			for($i = 0; $i < $totalClusters; $i ++) {
				$msRecomended = "MS@0";
				$cbseRecomended = "CBSE@0";
				$icseRecomended = "ICSE@0";
				$igcseRecomended = "IGCSE@0";
				?>
				<tr>
					<td class="customPoint">
						<input name="clusterSelection" class="clusterSelection" type="radio" value="<?=$clusterDetails[$i][0]?>" />
					</td>
					<td align="center"><?=($i+1)?></td>
					<td align="left" class="clusterDetail<?=($clusterDetails[$i][8]=="practice")?' practiceCluster':''; ?>">
						<a href="sampleQuestions.php?ttCode=<?=$ttCode?>&learningunit=<?=$clusterDetails[$i][0]?>&cls=<?=$cls?>&flow=<?=$defaultFlow; ?>"	target="_blank" title="Click here to view sample questions"  style="width:95%;float:left;">
							<?=$clusterDetails[$i][1];?>
						</a>
						<?php 
							if($i == 0){
								?>
									<a type="text" value="+" class="plus-sign" id="plus-sign-<?php echo $clusterDetails [$i] [0]; ?>" style="font-size: 20px;text-decoration:none;width: 20px;cursor: pointer;background:#fff;color:black;float:right;width: 5%;float:right;line-height: 15px;color: #e75903;" href="javascript:void(0);" title="Click here to see specific sample questions as per the Learning Objectives." data-intro="Clicking on the + symbol will allow you to see the Learning Unit Flow and a sample question from each of these.">+</a>
								<?php 		
							}
							else{
								?>
									<a type="text" value="+" class="plus-sign" id="plus-sign-<?php echo $clusterDetails [$i] [0]; ?>" style="font-size: 20px;text-decoration:none;width: 20px;cursor: pointer;background:#fff;color:black;float:right;width: 5%;float:right;line-height: 15px;color: #e75903;" href="javascript:void(0);" title="Click here to see specific sample questions as per the Learning Objectives.">+</a>
								<?php 
							}?>
						
					</td>
					<td align="center" id="rowMS<?=$i?>" <?php if($clusterDetails[$i][2]=='-') { ?> title="This LU is not part of the recommended syllabus for this curriculum." <?php } ?>>
						<?php if($clusterDetails[$i][2]=='Y') { echo " <img src='assets/right.png' width='15' height='15'>"; $msRecomended = "MS@1";$msTotalLevel++; } else echo $clusterDetails[$i][2]; ?>
					</td>
					<td width="43px" align="center" id="rowCBSE<?=$i?>" <?php if($clusterDetails[$i][3]=='-') { ?> title="This LU is not part of the recommended syllabus for this curriculum." <?php } ?> >
						<?php if($clusterDetails[$i][3]=='Y') { echo " <img src='assets/right.png' width='15' height='15'>"; $cbseRecomended = "CBSE@1";$cbseTotalLevel++; } else echo $clusterDetails[$i][3]; ?>
					</td>
					<td width="38px" align="center" id="rowICSE<?=$i?>" <?php if($clusterDetails[$i][4]=='-') { ?> title="This LU is not part of the recommended syllabus for this curriculum." <?php } ?>>
						<?php if($clusterDetails[$i][4]=='Y') { echo " <img src='assets/right.png' width='15' height='15'>"; $icseRecomended = "ICSE@1"; $icseTotalLevel++; } else echo $clusterDetails[$i][4]; ?>
					</td>
					<td width="49px" align="center" id="rowIGCSE<?=$i?>" <?php if($clusterDetails[$i][9]=='-') { ?> title="This LU is not part of the recommended syllabus for this curriculum." <?php } ?>>
						<?php if($clusterDetails[$i][9]=='Y') { echo " <img src='assets/right.png' width='15' height='15'>"; $igcseRecomended = "IGCSE@1"; $igcseTotalLevel++; } else echo $clusterDetails[$i][9]; ?>
					</td>
					<td align="center" id="rowCustom<?=$i?>" <?php if(isset($_REQUEST["activateMode"]) && $_REQUEST["activateMode"]=="yes") echo "style='display:none'"?> <?php if($clusterDetails[$i][5]=='-' && $customizedTopic!="") { ?> title="Not included in customised flow." <?php } ?>>
					<span style='position: relative;'>
					<?php if($customizedTopic=="") { ?>
						
						<input type="checkbox" <?php if($clusterDetails[$i][5]=='Y') echo " checked"?> name="chkCluster[]" id="chkCluster<?=$i?>" class="<?=$msRecomended."_".$cbseRecomended."_".$icseRecomended."_".$igcseRecomended;?>" value="<?=$clusterDetails[$i][0]?>" onClick="checkIfClusterCustumize('Custom', this.id)" > 
						
										<?php }  else {
						if($clusterDetails[$i][5]=='Y') echo " <img src='assets/right.png' width='15' height='15'>"; else echo "-";
						}
						?>
						
						<div id="messagediv<?=$i?>"  onclick="notify();" style="height:17px;width:17px;position: absolute;left: 0;right: 0;top: 0;bottom: 0;background: url(assets/transperent.png) repeat;display: none;"></div>
						</span>
					</td>
				<?php if($flow=="MSOld") { ?>
					<td align="center" id="rowMSOld<?=$i?>"><?php if($clusterDetails[$i][6]=='Y') echo " <img src='assets/right.png' width='15' height='15'>"; else echo "&nbsp;" ?></td>
				<?php } ?>
				</tr>
				 <tr class="cluster-tr" id="cluster-tr-<?php echo $clusterDetails [$i] [0]; ?>"><td  colspan="<?=$colSpan;?>">
					<label style="margin: 5px 2px 0px 0px; float: left;">Sub learning units :</label>
					<?php 
						$clusters = $clusterDetails [$i] [10][$clusterDetails [$i] [0]];
						$clustersArray = explode(",",$clusters);
						$qCountDisplay = 1;
						foreach ( $clustersArray as $cluster){
							$flagQuery ="SELECT a.misconception,b.description FROM educatio_adepts.adepts_questions a LEFT JOIN  educatio_educat.misconception_master b ON a.misconception=b.id WHERE a.clusterCode= '".$clusterDetails [$i] [0]."' AND a.subdifficultylevel= '$cluster' limit 1; ";
							$flagSQL = mysql_query($flagQuery);
							$flagResult = mysql_fetch_array($flagSQL,MYSQL_ASSOC);
							$redflag = $flagResult['misconception'] != ''?'<img src="assets/redflag.ico"  alt=""/>':'';
							$clear = strip_tags($flagResult['description']);
							$clear = html_entity_decode($clear);
							$clear = urldecode($clear);
							//$clear = preg_replace('/[^A-Za-z0-9:-+]/', ' ', $clear);
							$clear = preg_replace('/[^A-Za-z0-9\:\-\+\.\?\/,\=]/', ' ', $clear);
							$clear = preg_replace('/ +/', ' ', $clear);
							$clear = trim($clear);
							$misconceptoinString = $clear !=''?base64_encode($clear):'';
							?>
								<a href="javascript:void(0);" class="sdlbox" onClick="getQuestionForSDL('<?=$clusterDetails [$i] [0]; ?>','<?=$cluster; ?>','<?=$ttCode?>','<?=$cls?>','<?=$defaultFlow?>','<?=$misconceptoinString?>');" title="<?=$clear?>"><span>&nbsp;</span><?=$redflag ;?></a>
							<?php 
							$qCountDisplay++;
						}
					?>
					<a href="sampleQuestions.php?ttCode=<?=$ttCode?>&learningunit=<?=$clusterDetails[$i][0]?>&cls=<?=$cls?>&flow=<?=$defaultFlow; ?>"	target="_blank" title="Click here to view sample questions" style="margin: 5px 2px 0px 0px; float: left;"> More >> </a>
				</td></tr>
				<?php
				$timedTestArray[] =  $clusterDetails[$i][0];
				$timedTestArrayOld = getTimedTestMappedToCluster ( $clusterDetails [$i] [0] );
				$dailyPractice = getDailyPracticeMappedToCluster( $clusterDetails [$i] [0]);
				$activitiesArray = getActivitiesMappedToCluster ( $clusterDetails [$i] [0] );		
				$remedialsArray = findRemedialCluster($clusterDetails [$i] [0]);
				foreach ( $dailyPractice as $code => $arrDetails ) {
				 	?>
				<tr style="text-align:left">
				 	<td colspan='<?=$colSpan;?>' class="colspan-change">
				 	<?php 				 	
				 	if ($arrDetails ["drill"] == 1) { ?>
				 	<strong title='Upon successful completion of above learning unit, students will be given this as part of Daily Practice'>Daily Practice: </strong>				 
				 	<?php } 
				 	 else { ?>
				 	<strong title='Upon successful completion of above learning unit, students will be given this as a choice in Choice Screen'>Practice Module: </strong>
				 	
				 		<?php } ?>
				 	<a style="cursor:pointer;" onClick="trackTeacher1('<?=$code?>','dailyPractice')" href="javascript:void(0);"><?=$arrDetails ["desc"];?> </a>
				 	</td>
				 	</tr>
					<?php 
				 } 
				foreach ( $timedTestArrayOld as $code => $arrDetails ) {
					?>
					<tr style="text-align:left">
					<td colspan='<?=$colSpan;?>' class="colspan-change">
					<strong>Timed Test: </strong><a style="cursor:pointer;" onClick="trackTeacher1('<?=$code?>','timeTest')" href="javascript:void(0);"><?=$arrDetails ["desc"];?> </a>
					</td>
					</tr>
					<?php 
				} 
				 
			    foreach ( $activitiesArray as $code => $arrDetails ) {
					?>
					<tr style="text-align:left">
					<td colspan='<?=$colSpan;?>' class="colspan-change">
					<strong>Activity: </strong><a style="cursor:pointer;" onClick="trackTeacher1('<?=$code;?>','activity');" href="javascript:void(0);"><?=$arrDetails ["desc"];?></a>
					</td>
					</tr>
					<?php 
				}
				foreach ( $remedialsArray as $arrDetails ) {
					?>
					<tr style="text-align:left">
					<td colspan='<?=$colSpan;?>' class="colspan-change">
					<strong>Remedial: </strong><a style="cursor:pointer;" onClick="trackTeacher1('<?=$arrDetails['remedialItemCode']?>','remedial')"><?=$arrDetails['remedialItemDesc'];?></a>
					</td>
					</tr>
					<?php 
				}
			}
			?>
			</table>

					<div id="estimation" align="left" style="width: 90%; text-align: justify; font-size: 12px;padding: 25px;" class="legend">
						<br />
				Estimated time to complete the topic for selected flow:
				<?=$totalTimeForTopic?>
				minutes<br />
						<div class="legend" style="color: black">
							<em>(Please note that this is just an estimated time based on the
								past data and the actual time may vary with each student)</em>
						</div>
						<br />
					</div>
		<?php 
			 if(!in_array($loginID,$testIDArray)) {// && !($_SESSION['isOffline'] === true && SERVER_TYPE=="LIVE")) { ?>
			<div align="center" class="estimate-time" style="position:relative;">

				
				<?php
				
if ($customizedTopic == "") { // show save button only when it is non-customized TT (i.e. new cust ttcode in master table
					$newTTDesc = getCustomTeacherTopicDescSuggestion ( $ttCode, $schoolCode, $cls );
					$tdList = getTeacherTopicDescList ( $schoolCode, $cls );
					$validationData = implode ( "~", $tdList );
					?>

				<div id="friendlyNameForm" <?php if((isset($_REQUEST["activateMode"]) && $_REQUEST["activateMode"]=="yes") || 
						$flow !="Custom")  echo " style='display:none'";?>>
					<div style="float: left;  width:250px">
					Give a friendly name to the customised topic before you activate it for your class:</div> 
					<div style="width:5px;float:left;"><input name="ttDesc" type="text" value="<?=$teacherTopicDesc . $newTTDesc ?>" id="customizedTopicDesc" data="<?=$validationData ?>" required></div>
					<br /><br /><br />
				</div>
						<!-- <input type="button" name="activateTopic" id="activateTopic"
							value="Activate"
							onClick="activateDeactivateTopic('<?=$ttCode?>',<?=$schoolCode?>,<?=$cls?>,'<?=$section?>','<?=$flow?>','<?=ucfirst($username)?>','activate','topicContainer<?=$i?>')"
							class="submitBtn"
							<?php if(isset($_REQUEST["activateButton"]) && $_REQUEST["activateButton"]!=1) { echo " style='display:none'"; } ?>> -->
						<div class="bulkActivateDeactivatePrompt" id="bulkADPrompt<?=$i?>">
							<div class="bulkADMain"></div>
							<div class="bulkADHelp"></div>
							<div class="bulkADActionButton"></div>
						</div>						
						<input type="button" name="activateTopic" id="activateTopic"
							value="Activate Selected Units"
							<?php							
							$thisClassSections=$sectionArray[$cls];
							if(strlen($thisClassSections)==0) $thisClassSections="''";
							$thisClassSections=explode(",", $thisClassSections);
							$availableForSections=array();
							$activatedForSections = getCurrentActivatedSectionList($schoolCode,$cls,$ttCode,$thisClassSections);
							foreach ($thisClassSections as $key => $value) {
								if(!in_array(str_replace("'", "", $value), $activatedForSections))
									$availableForSections[]=str_replace("'", "", $value);
							}
							
							if (((count($availableForSections)==1 && $availableForSections[0]==$section) || count($thisClassSections)==1 && $thisClassSections[0]=="''" ) && ($durationPopup == 0 || (isset($_REQUEST["openTab"]) && $_REQUEST["openTab"] == 2))){
							?>
							onClick="activateDeactivateTopic('<?=$ttCode?>',<?=$schoolCode?>,<?=$cls?>,'<?=$section?>','<?=$flow?>','<?=ucfirst($username)?>','activate','topicContainer<?=$i?>')"
								<?php
							}
							else{
								?>
							onClick="openActivateDeactivatePrompt(this,'<?=$ttCode?>',<?=$schoolCode?>,<?=$cls?>,'<?=$section?>','<?=$flow?>','<?=ucfirst($username)?>','activate','topicContainer<?=$i?>','<?=implode(',',$availableForSections)?>','bulkADPrompt<?=$i?>',false,<?=$durationPopup?>,<?=(isset($_REQUEST['openTab']) && $_REQUEST['openTab'] == 2)? 1 : 0 ?>)"
								<?php
							}
							?>
								
							class="submitBtn"
							<?php if(isset($_REQUEST["activateMode"]) && $_REQUEST["activateMode"]=="yes") { } else echo " style='display:none'";?>>
						<input type="button" name="seeCustomize" id="seeCustomize"
							value="Customize Units" class="submitBtn"
							<?php if((isset($_REQUEST["activateMode"]) && $_REQUEST["activateMode"]=="yes") || $flow !="Custom") { } else echo " style='display:none'";?>>
						<input type="button" name="backToDefauld" id="backToDefauld"
							value="Cancel" class="submitBtn"
							<?php if((isset($_REQUEST["activateMode"]) && $_REQUEST["activateMode"]=="yes" ) || $flow != "") echo "style='display:none'";?>>
							<input type="button" name="cancel" id="cancel"
							value="Cancel" class="submitBtn" onClick="showMappingOnCancel('<?=$_REQUEST["redirectTTCode"]?>','<?=$_REQUEST["redirectFlow"]?>','<?=$_REQUEST["cls"]?>','<?=$_REQUEST["section"]?>','	')"
							<?php if((isset($_REQUEST["activateMode"]) && $_REQUEST["activateMode"]=="yes") || $flow != "") echo "style='display:none'";?>>
						<input type="button" name="save" id="saveCustom"
							value="Activate Custom NOW"
							<?php if(count($topicsActivated)>=15) echo "style='opacity:0.3; display:none;' title='Mindspark does not allow more than 15 topics to be active at a time.'"; else { ?>
							<?php
							$thisClassSections=$sectionArray[$cls];
							if(strlen($thisClassSections)==0) $thisClassSections="''";
							$thisClassSections=explode(",", $thisClassSections);
							$availableForSections=array();
							$activatedForSections = getCurrentActivatedSectionList($schoolCode,$cls,$ttCode,$thisClassSections);
							foreach ($thisClassSections as $key => $value) {
								if(!in_array(str_replace("'", "", $value), $activatedForSections))
									$availableForSections[]=str_replace("'", "", $value);
							}
							if (((count($availableForSections)==1 && $availableForSections[0]==$section) || count($thisClassSections)==1 && $thisClassSections[0]=="''") && $durationPopup == 0){
							?>
							onClick="return validate(1);"
								<?php
							}
							else{
								foreach ($sectionList as $key => $value) {
									$sectionList[$key]=str_replace("'", "", $value);
								}
								?>
							onClick="openActivateDeactivatePrompt(this,'<?=$ttCode?>',<?=$schoolCode?>,<?=$cls?>,'<?=$section?>','<?=$flow?>','<?=ucfirst($username)?>','activate','topicContainer<?=$i?>','<?=implode(',',$availableForSections)?>','bulkADPrompt<?=$i?>','validate(1)',<?=$durationPopup?>,0)"
								<?php
							}
							?>
							<?php } 
							if((isset($_REQUEST["activateMode"]) && $_REQUEST["activateMode"]=="yes") || $flow !="Custom") echo "style='display:none'"?>
							class="submitBtn btnSave"> <input type="submit"
							name="save_activate" id="save_activate"
							value="Activate Custom LATER" onClick="return validate(2);"
							<?php if((isset($_REQUEST["activateMode"]) && $_REQUEST["activateMode"]=="yes") || $flow!="Custom") echo "style='display:none'"?>
							class="submitBtn btnSave"> <input type="hidden"
							name="saveAndActivate" id="saveAndActivate" value="0">
				<?php }  else {?>
				<input type="button"
							onClick="showMapping('<?=$customizedTopic?>','<?=$cls?>','<?=$section?>','','<?=$redirectTTCode?>','<?=$redirectFlow?>'); "
							value="Click here to re-customize the topic" class="submitBtn">
				<?php } ?>
			</div>
		<?php
			
} else {
				?>
			<div align="center" style="font-size: 1.5em">Can not customize the
						topic in online mode.</div>
		<?php } ?>
			<div id="notesDiv" align="left"	style="width: 99%; text-align: justify; font-size: 12px;" class="legend">
						<br />
						<span style="margin-left:25px;">Note:</span>
						<ul>
							<li>Some learning units may not be selected for any stream
								because they do not relate to the current class level</li>
							<li>The option of choosing a stream<!--/customizing the curriculum-->
								is available only as long as NO student has started the topic.
								The moment even 1 student starts the topic, this choice will no
								longer be available.
							</li>
							<!--<li>If you choose a customized curriculum, the students will not be taken to a higher/lower class level in that topic.</li>-->
						</ul>
					</div>
			<?php } else echo "No records found!"; ?>
		<input type="hidden" name="interface" value="<?=$interface?>"> <input
						type="hidden" id="totalLevelCount" name="totalLevelCount"
						value="<?=$msTotalLevel."-".$cbseTotalLevel."-".$icseTotalLevel."-".$igcseTotalLevel;?>">
					<input type="hidden" id="generatedFlow" name="generatedFlow"
						value="">
				</form>
			</div>
			<form target='window.parent' name="mindsparkTeacherLogin"
				id="mindsparkTeacherLogin" action="" method="post">
				<input type="hidden" name="mode" id="mode" value=""> <input
					type="hidden" name="sessionID" id="sessionID"
					value="<?=$_SESSION["sessionID"]?>"> <input type="hidden"
					name="childClass" id="childClass" value="<?=$cls?>"> <input
					type="hidden" name="userType" id="userType"
					value="teacherAsStudent"> <input type="hidden" name="forceNew"
					id="forceNew" value=""> <input type="hidden" name="ttCode"
					id="ttCode" value="<?=$ttCode?>"> <input type="hidden"
					name="customClusterCode" id="customClusterCode" value=""> <input
					type="hidden" name="forceFlow" id="forceFlow"
					value="<?=$_REQUEST['flow']?>"> <input type="hidden"
					name="startPoint" id="startPoint" value="">
			</form>
			<?php 
				$masterTeacherTopicCodeQuery = "SELECT teacherTopicDesc, parentTeacherTopicCode FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$ttCode'";
				$masterTeacherTopicCodeQuerySQL = mysql_query($masterTeacherTopicCodeQuery);
				$masterTeacherTopicCodeQueryResult = mysql_fetch_array($masterTeacherTopicCodeQuerySQL);
				if($masterTeacherTopicCodeQueryResult[1] != ''){
					$topicForVideo = $masterTeacherTopicCodeQueryResult[1];
				}
				else{
					$topicForVideo = $ttCode;
				}
				$videoQuery = "SELECT thumb, videoTitle, videoType, videoFile, videoID, clickCnt, likeCnt as likeCnt1, dislikeCnt FROM adepts_msVideos WHERE mappingType in ('topic','teacherTopic') AND mappingID='$topicForVideo' order by clickCnt DESC limit 1;";				
				$videoQuerySQL = mysql_query($videoQuery);
				$videoQueryResult = mysql_fetch_row($videoQuerySQL);
				
				$researchPaperQuery  = "SELECT moduleID, title, description, author, link1, link2, MAX(clickCnt) as mclick FROM adepts_researchModules WHERE FIND_IN_SET('$topicForVideo',mappedTTs)";
				$researchPaperSQL = mysql_query($researchPaperQuery);
				$researchPaperResult =  mysql_fetch_assoc($researchPaperSQL);
				
				$interviewSummaryQuery = "SELECT interviewID, title, description, interviewer, link, filename, videoLink, MAX(clickCnt) as mClick FROM adepts_studentInterviews WHERE FIND_IN_SET('$topicForVideo',mappedTTs)";
				$interviewSummarySQL = mysql_query($interviewSummaryQuery);
				$interviewSummaryResult = mysql_fetch_assoc($interviewSummarySQL);
				
			?>
			<div class="topic-research" data-intro="The section displays the rich research available in Mindspark around misconceptions in this topic.">
				<div class="topic-research-title" >Topic Research</div>
				<?php 
					if($videoQueryResult[5] != ''){
						?>
						<div class="video-image">
							<a href="misconceptionVideos.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$defaultFlow?>&mytopicpagerefferal=1" class="titleLink" target="_blank"><img src="assets/mv.png" alt="" /></a>
							<span class="video-title"><a href="misconceptionVideos.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$defaultFlow?>&mytopicpagerefferal=1" class="titleLink" target="_blank"><?php echo $videoQueryResult[1]; ?></a></span>
						</div>
						<?php 						
					}
					if($researchPaperResult['mclick'] != ''){
						$title = stripslashes ($researchPaperResult['title']);
						$description = $researchPaperResult['description'];
						$author = $researchPaperResult['author'];
						$link1 = stripslashes ($researchPaperResult['link1']);
						$link2 = stripslashes ($researchPaperResult['link2']);
						$moduleid = $researchPaperResult['moduleID'];
						// Prepare author string..
						$authorSTR = $author;
						if($author != "")
						{
							$authorSTR = "";
							$authors = explode(",",$author);
							if(count($authors) > 0)
							{
								for($i=0;$i<count($authors);$i++)
								{
								if($i == count($authors)-2)
									$authorSTR .= $authors[$i]." and ";
									else
											$authorSTR .= $authors[$i].", ";
								}
								$authorSTR = substr($authorSTR,0,-2);
							}
							else
								$authorSTR = $authors[0];
						}
						?>
						<div class="research-paper-div">
						<a href="researchPapers.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$defaultFlow?>&section=<?=$section;?>&mytopicpagerefferal=1" class="titleLink" target="_blank"><img src="assets/rps.png" alt="" /></a>
						<div class="research-paper-title"><a href="researchPapers.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$defaultFlow?>&mytopicpagerefferal=1" class="titleLink" target="_blank">Summary - <?=$title?></a></div>
						
						</div>
						<?php 
					}
					if($interviewSummaryResult['mClick'] != ''){
						$title = stripslashes($interviewSummaryResult['title']);
						$description = $interviewSummaryResult['description'];
						$link = stripslashes($interviewSummaryResult['filename']);
						?>
						<div class="interview-summary-div">
						<a href="studentInterviews.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$defaultFlow?>&mytopicpagerefferal=1" class="titleLink" target="_blank"><img src="assets/SIW-icon.png" alt="" /></a>
							<div class="student-interview-title"><a href="studentInterviews.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$defaultFlow?>&mytopicpagerefferal=1" class="titleLink" target="_blank"><?=$title?></a></div>
							<!-- <span class="descText"><?=$description?></span> -->
						</div>
						<?php 
					}
				?>
				<div class="interview-summary-div">
					<a href="cwa.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&section=<?=$section;?>" class="titleLink" target="_blank"><img src="assets/CWA-icon.png" alt="" /></a>
				</div>
			</div>
			<div class="bottom-scroll">
			<?php 
			$implodedArray = implode("','",$timedTestArray);
			$igreArray = getIGREArray($implodedArray);
			$timedTestArray = getTimedTestArray($implodedArray);
			$remedials = getRemedialCluster($implodedArray);
			$totalCountForSlider =count($igreArray) + count($timedTestArray) + count($remedials);
			
			?>
				<input type="hidden" name="currenScrollId" id="currenScrollId" value="1" />
				<input type="hidden" name="totalCount" value="<?=$totalCountForSlider;?>" id="totalCount" />
				<hr style="width:100%;float:left;border: 1px solid #e75903;"/>
				<div class="mainContainerX" style="float:left;<?=$totalCountForSlider == 0?'height:50px; text-align: center;':'';?>" data-intro="Different varieties of activities are seen here, such as games, enrichments, remedials and introductions. You can click on any of the items to experience it.">
				<label style="color: #626161;float: left;font-size: 1.4em;font-weight: bold;width: 100%;text-align: left;margin-left:20px;">Activities : </label>
			<?php 
			if($totalCountForSlider == 0){
				echo '<span style="font-size: 16px;">No activities are present in this topic.</span>';
			}else{
			?>	
				<div class="arrow-left" onClick="goLeft();"><div class="arrow-leftin"></div></div>
					<div id="largeContainer" style="margin-top: 0;margin-left: 15px;">
					<?php
						$j = 1;
						$i = 1;
						if(!empty($igreArray)){
							foreach($igreArray as $igre){
								if($i == 1 || $i % 4 == 1){
									$fadeInClass = "fadeinout-".$j."";
									$j ++;
								}
								if($igre['type']= "enrichment"){
									$folderPath	=	ENRICHMENT_MODULE_FOLDER."/html5/enrichments/".$igre['gameID']."/".$igre['thumbImg'];
								}
								else
								{
									$folderPath	=	ENRICHMENT_MODULE_FOLDER."/html5/games/".$igre['gameID']."/".$igre['thumbImg'];
								}
								$ch = curl_init($folderPath);
								curl_setopt($ch, CURLOPT_NOBODY, true);
								curl_exec($ch);
								$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
								curl_close($ch);
								if($retcode == '404'){
									$folderPath = "assets/Defaultactivity-icon.png";
								}else{
									$folderPath = $folderPath;
								}
								?>
								<div class="smallContainer currenHide notAttempted <?php echo $fadeInClass; ?>">
									<a href="javascript:void(0);" onClick="return trackTeacher1('<?=$igre['gameID']?>','activity');">
										 <!-- <img style="float:left;" src="http://<?php echo $_SERVER["HTTP_HOST"]?>/mindspark/html5/games/<?php echo $igre['gameID']; ?>/<?php echo $igre['thumbImg']; ?>" alt="<?php echo $igre['gameDesc']; ?>" /> -->  
										<img style="float:left;" src="<?=$folderPath;?>" alt="<?php echo $igre['gameDesc']; ?>" />
									</a>
									<!-- <img style="float:left;" src="http://localhost/mindspark/html5/games/200/thumbImage_200.JPG"  width="200" height="200"/> -->
									<span title="<?=$igre['gameDesc']?>"><b>Activity : </b><a href="javascript:void(0);" onClick="return trackTeacher1('<?=$igre['gameID']?>','activity');"><?= strlen($igre['gameDesc']) >= 65 ?substr($igre['gameDesc'], 0, 55)."...":$igre['gameDesc'];?></a></span>
									<?php 
									if($igre['noteForSchools'] != '' && strlen($igre['noteForSchools'])>=10){
										?>
											<span title="<?=$igre['noteForSchools'];?>"><b>Note:</b><?= strlen($igre['noteForSchools']) >= 65 ?substr($igre['noteForSchools'], 0, 50)."...":$igre['noteForSchools'];?></span>
										<?php 										
									}
									?>
								</div>
								<?php
								$i++; 
							}
						}
						if(!empty($timedTestArray)){
							foreach($timedTestArray as $timedTest){
								if($i == 1 || $i % 4 == 1){
									$fadeInClass = "fadeinout-".$j."";
									$j ++;
								}
								$ttcodetopass = $timedTest["timedTestCode"];
								?>
								<div class="smallContainer currenHide notAttempted <?php echo $fadeInClass; ?>" style="">
									<a href="javascript:void(0);" onClick="return trackTeacher1('<?=$ttcodetopass;?>','timeTest')"><img style="float:left;" src="assets/timedtest-icon.png"  /></a>
									<span style="float:left;" title="<?=$timedTest['description']?>"><b>Timed Test:</b><a href="javascript:void(0);" onClick="trackTeacher1('<?=$ttcodetopass;?>','timeTest')"><?= strlen($timedTest['description']) >= 65 ?substr($timedTest['description'], 0, 50)."...":$timedTest['description'];?></a></span>
								</div>
								<?php 
								$i++;
							}
						}
						if(!empty($remedials)){
							foreach($remedials as $remedial){
								if($i == 1 || $i % 4 == 1){
									$fadeInClass = "fadeinout-".$j."";
									$j ++;
								}
								?>
								<div class="smallContainer currenHide notAttempted <?php echo $fadeInClass; ?>" style="">
									<a href="javascript:void(0);" onClick="trackTeacher1('<?=$remedial['remedialItemCode']?>','remedial')"><img style="float:left;" src="assets/remedial-icon.png"   /></a>
									<span style="float:left;" title="<?=$remedial['remedialItemDesc']?>"><b>Remedial:</b><a href="javascript:void(0);" onClick="trackTeacher1('<?=$remedial['remedialItemCode']?>','remedial')"><? echo strlen($remedial['remedialItemDesc']) >= 65 ? substr($remedial['remedialItemDesc'], 0, 50)."...":$remedial['remedialItemDesc'];?></a></span>
								</div>
								<?php
								$i++;
							}
						}
					?>				
					</div>
					<div class="arrow-right" onClick="goRight();"><div class="arrow-rightin"></div></div>
				</div>
				<?php 	} ?>
			</div>
		</div>
	</div>
	
<?php 
function getCurrentActivatedSectionList($schoolCode,$cls,$ttCode,$thisClassSections){
	$query = "SELECT GROUP_CONCAT(DISTINCT A.section ORDER BY A.section)
	  FROM adepts_teacherTopicActivation A , adepts_teacherTopicMaster B  
	  WHERE A.schoolcode='$schoolCode' AND A.class='$cls' AND A.section IN ($thisClassSections) AND A.teacherTopicCode=B.teacherTopicCode AND B.live=1 AND ISNULL(A.deactivationDate) AND A.teacherTopicCode='$ttCode'";
	  //echo $query;
	  $result = mysql_query($query); 
	  if($line=mysql_fetch_array($result))
	  	return explode(",", $line[0]);
	  else return array();
}
function getIGREArray($timedTestArray){
	$q = "SELECT a.gameID, a.gameDesc,  a.thumbImg ,a.type, b.noteForSchools FROM adepts_gamesMaster a LEFT JOIN adepts_greApproval b ON a.gameID=b.greCode WHERE live='Live' AND   linkedToCluster IN('$timedTestArray')";
	$rs	=	mysql_query($q) or die(mysql_error());
	while($rw	=	mysql_fetch_array($rs)){
		$returnArray[] = $rw; 
	}
	return $returnArray;
}
function getTimedTestArray($clusterCodeArray)
{
	$timedTestArray = array();
	$query = "SELECT timedTestCode, description FROM adepts_timedTestMaster WHERE linkedToCluster IN ('$clusterCodeArray') AND status='Live'";
	$result = mysql_query($query) or die("Error in fetching timed test details");
	while($line = mysql_fetch_array($result)){
		$returnArray[] = $line; 
	}
	return $returnArray;
}

include("footer.php");?>

<?php
function getStudentAttempted($ttCodeArray, $userIDs, $class) {
	foreach ( $ttCodeArray as $ttCode ) {
		$attemptedArray [$ttCode] = 0;
		foreach ( $userIDs as $userID ) {
			$query = "select * from " . TBL_TOPIC_STATUS . " where userID='$userID' and teacherTopicCode='$ttCode' and ttAttemptNo=1";
			$r = mysql_query ( $query );
			while ( $l = mysql_fetch_array ( $r ) ) {
				$attemptedArray [$ttCode] ++;
			}
		}
	}
	return $attemptedArray;
}
function getTeacherTopicProgress($ttCodeArray, $userIDArray, $class) {
	$userIDstr = implode ( ",", $userIDArray );
	
	foreach ( $ttCodeArray as $ttCode ) {
		$q = "SELECT distinct flow FROM " . TBL_TOPIC_STATUS . " WHERE  userID in (" . $userIDstr . ") AND teacherTopicCode='" . $ttCode . "'";
		$r = mysql_query ( $q );
		while ( $l = mysql_fetch_array ( $r ) ) {
			$flowN = $l [0];
			$flowStr = str_replace ( " ", "_", $flowN );
			${"objTopicProgress" . $flowStr} = new topicProgress ( $ttCode, $class, $flowN, SUBJECTNO );
		}
		
		$sq = "SELECT userID,MAX(progress),flow,result FROM " . TBL_TOPIC_STATUS . " WHERE teacherTopicCode='$ttCode'
				 AND userID IN ($userIDstr) GROUP BY userID";
		$rs = mysql_query ( $sq );
		$userttProgress = array ();
		
		while ( $rw = mysql_fetch_array ( $rs ) ) {
			$sqProgress = "SELECT srno FROM " . TBL_CURRENT_STATUS . " WHERE progressUpdate=0 AND teacherTopicCode='$ttCode' AND userID=" . $rw [0];
			$rsProgress = mysql_query ( $sqProgress );
			if ($rwProgress = mysql_fetch_assoc ( $rsProgress ))
				$userttProgress [$rw [0]] = $rw [1];
			else {
				$flowK = $rw [2];
				$flowStr = str_replace ( " ", "_", $flowK );
				$objTopicProgress = new topicProgress ( $ttCode, $class, $flowK, SUBJECTNO );
				$userttProgress [$rw [0]] = ${"objTopicProgress" . $flowStr}->getProgressInTT ( $rw [0] );
			}
		}
		$topicProgress [$ttCode] = round ( array_sum ( $userttProgress ) / count ( $userIDArray ), 2 );
	}
	return $topicProgress;
}
function getTTsActivatedN($cls, $schoolCode, $section, $masterTopic, $mode = "active", $limit = 0, $activationPeriod = 0) {
	$ttAttemptedArray = array ();
	$ttCodeArr = array ();
	$query = "SELECT A.teacherTopicCode,teacherTopicDesc,A.activationDate,A.deactivationDate,flow,A.priority FROM adepts_teacherTopicActivation A , adepts_teacherTopicMaster B  
		      WHERE A.schoolcode='$schoolCode' AND A.class='$cls' AND A.section='$section' AND A.teacherTopicCode=B.teacherTopicCode";
	if ($mode == "active") {
		$query .= " AND ISNULL(deactivationDate)";
	}
	
	if ($mode == "priority") {
		$query .= " AND ISNULL(deactivationDate)";
	}
	
	if ($activationPeriod != 0) {
		$lastDate = date ( 'Y-m-d', strtotime ( "-20 days" ) );
		$query .= " AND A.activationDate<'$lastDate'";
	}
	if ($masterTopic != "") {
		$query .= " AND classification='$masterTopic'";
	}
	$query .= " ORDER by A.activationDate";
	if ($limit != 0)
		$query .= " LIMIT $limit";
	
	$result = mysql_query ( $query ) or die ( mysql_error () );
	
	while ( $line = mysql_fetch_array ( $result ) ) {
		$pos = 0;
		$ttCode = $line [0];
		$clsLevelArray = getClassLevel ( $ttCode, $line [4] );
		$clsLevel = "";
		if (count ( $clsLevelArray ) > 0)
			$clsLevel = implode ( ",", $clsLevelArray );
		$class_explode = explode ( ",", $clsLevel );
		$max_grade = max ( $class_explode );
		$min_grade = min ( $class_explode );
		for($a = $min_grade; $a <= $max_grade; $a ++) {
			if ($a == $cls) {
				$pos = 1;
			}
		}
		if ($max_grade == $min_grade) {
			$ttAttemptedArray [$line [0]] ["grade"] = $min_grade;
			$ttAttemptedArray [$line [0]] ["gradeSequence"] = 0;
		} else {
			if ($pos != 1) {
				$ttAttemptedArray [$line [0]] ["gradeSequence"] = 0;
			} else {
				$ttAttemptedArray [$line [0]] ["gradeSequence"] = 1;
			}
			$ttAttemptedArray [$line [0]] ["grade"] = $min_grade . "-" . $max_grade;
		}
		if ($max_grade == "" && $min_grade == "") {
			$ttAttemptedArray [$line [0]] ["grade"] = $cls;
			$ttAttemptedArray [$line [0]] ["gradeSequence"] = 2;
		}
		if ($max_grade == $cls && $min_grade == $cls) {
			$ttAttemptedArray [$line [0]] ["grade"] = $cls;
			$ttAttemptedArray [$line [0]] ["gradeSequence"] = 2;
		}
		array_push ( $ttCodeArr, $ttCode );
		if (! (in_array ( $ttCodeArr, $ttCode ))) {
			$ttAttemptedArray [$line [0]] ["ttName"] = $line [1];
			$ttAttemptedArray [$line [0]] ["activationDate"] = $line [2];
			if ($line [3] == "0000-00-00")
				$ttAttemptedArray [$line [0]] ["deactivationDate"] = "";
			else
				$ttAttemptedArray [$line [0]] ["deactivationDate"] = $line [3];
			if ($line [4] != "")
				$ttAttemptedArray [$line [0]] ["flow"] = $line [4];
			else
				$ttAttemptedArray [$line [0]] ["flow"] = "MS";
		}
		$ttAttemptedArray [$line [0]] ["priority"] = $line [5];
	}
	
	uasort ( $ttAttemptedArray, "sortByPriorityAndActivationDateHelper" );
	return $ttAttemptedArray;
}
function sortByPriorityAndActivationDateHelper($a, $b) {
	if ($a ['priority'] < $b ['priority']) {
		return - 1;
	} elseif ($a ['priority'] > $b ['priority']) {
		return 1;
	} else {
		return sortByActivationDateHelper ( $a, $b );
	}
}
function sortByActivationDateHelper($a, $b) {
	if (strcmp ( $a ['activationDate'], $b ['activationDate'] ) == 0) {
		return 0;
	} else if (strcmp ( $a ['activationDate'], $b ['activationDate'] ) < 0) {
		return - 1;
	} else {
		return 1;
	}
}
function getCurrentTTsActivated($class, $schoolCode, $section) {
	$topicsActivated = 0;
	$query = "SELECT COUNT(srno) FROM adepts_teacherTopicActivation WHERE schoolcode=$schoolCode AND class=$class AND section='$section' AND deactivationDate='0000-00-00'";
	$rs = mysql_query ( $query ) or die ( mysql_error () . $query );
	if ($rw = mysql_fetch_array ( $rs )) {
		$topicsActivated = $rw [0];
	}
	return $topicsActivated;
}
function classSort(&$array, $key) {
	$sorter = array ();
	$ret = array ();
	reset ( $array );
	foreach ( $array as $ii => $va ) {
		$sorter [$ii] = $va [$key];
	}
	asort ( $sorter );
	foreach ( $sorter as $ii => $va ) {
		$ret [$ii] = $array [$ii];
	}
	$array = $ret;
}


function teacherTopicNeverActivated($schoolCode, $cls, $section, $masterTopic, $defaultFlow, $customizedTopicsArray, $savedMappingArray, $teacherName = "") {
	$query = "SELECT teacherTopicDesc, teacherTopicCode FROM adepts_teacherTopicMaster
	           WHERE  live=1 AND customTopic=0 AND subjectno=" . SUBJECTNO . " AND teacherTopicCode 
			   NOT IN (SELECT DISTINCT(teacherTopicCode) FROM adepts_teacherTopicActivation WHERE schoolCode=$schoolCode AND class=$cls";
	if ($section != "") {
		$query .= " AND section='$section'";
	}
	$query .= ")";
	if ($masterTopic != "") {
		$query .= " AND classification='$masterTopic'";
	}
	$query .= " ORDER BY classification, teacherTopicOrder";
	$result = mysql_query ( $query );
	$broadTopics = $teacherTopics = array ();
	$topic = "";
	
	$temp_rows = mysql_num_rows ( $result );
	while ( $line = mysql_fetch_array ( $result ) ) {
		$pos = 0;
		$ttCode = $line ['teacherTopicCode'];
		$teacherTopics [$ttCode] [0] = $line [0];
		$teacherTopics [$ttCode] [2] = $ttCode;
		$clsLevelArray = getClassLevel ( $line ['teacherTopicCode'], $defaultFlow );
		$clsLevel = "";
		if (count ( $clsLevelArray ) > 0) {
			$clsLevel = implode ( ",", $clsLevelArray );
			$teacherTopics [$ttCode] [3] = $clsLevel;
		} else
			$teacherTopics [$ttCode] [3] = 0;
		if (isset ( $savedMappingArray [$ttCode] ))
			$teacherTopics [$ttCode] [1] = $savedMappingArray [$ttCode];
		elseif (isset ( $topicsFollowingOldFlow ) && in_array ( $ttCode, $topicsFollowingOldFlow ))
			$teacherTopics [$ttCode] [1] = "MSOld";
		else
			$teacherTopics [$ttCode] [1] = $defaultFlow;
		
		if (in_array ( $ttCode, array_keys ( $customizedTopicsArray ) )) {
			$tmpTopicArray = $customizedTopicsArray [$ttCode];
			foreach ( $tmpTopicArray as $key => $arrDetails ) {
				$query2 = "select A.customizedBy from adepts_customizedTopicDetails A inner join adepts_teacherTopicMaster B on A.code=B.customCode where B.teacherTopicCode='" . $arrDetails [0] . "'";
				$result2 = mysql_query ( $query2 );
				while ( $line1 = mysql_fetch_array ( $result2 ) ) {
					$customizedBy = $line1 [0];
				}
				if ($customizedBy == "") {
					$query1 = "select lastModifiedby from adepts_teacherTopicActivation where teacherTopicCode='" . $arrDetails [0] . "'";
					$result1 = mysql_query ( $query1 );
					while ( $line1 = mysql_fetch_array ( $result1 ) ) {
						$customizedBy = $line1 [0];
					}
				}
				$teacherTopics [$arrDetails [0]] [0] = $arrDetails [1];
				$teacherTopics [$arrDetails [0]] [1] = "Custom - " . $arrDetails [2];
				$teacherTopics [$arrDetails [0]] [2] = $arrDetails [0];
				$teacherTopics [$arrDetails [0]] [3] = $arrDetails [3];
				$teacherTopics [$arrDetails [0]] [4] = $customizedBy;
				
				if (strcmp ( $customizedBy, $teacherName ) == 0) {
					$teacherTopics [$arrDetails [0]] ['category'] = '1-customizedByYou';
				} else {
					$teacherTopics [$arrDetails [0]] ['category'] = '2-customizedByOthers';
				}
			}
			unset ( $customizedTopicsArray [$ttCode] );
		}
		
		if (isOtherGrades ( $teacherTopics [$ttCode] [3], $cls )) {
			$teacherTopics [$ttCode] ['category'] = '4-otherGrades';
		} else {
			$teacherTopics [$ttCode] ['category'] = '3-notCustomized';
		}
		
		$innerarray = $teacherTopics [$ttCode];
		$class_explode = explode ( ",", $innerarray [3] );
		$max_grade = max ( $class_explode );
		$min_grade = min ( $class_explode );
		for($a = $min_grade; $a <= $max_grade; $a ++) {
			if ($a == $cls) {
				$pos = 1;
			}
		}
		if ($max_grade == $min_grade) {
			$teacherTopics [$ttCode] [3] = $min_grade;
			if ($pos != 1) {
				$teacherTopics [$ttCode] ["gradeSequence"] = 3;
			} else {
				$teacherTopics [$ttCode] ["gradeSequence"] = 1;
			}
		} else {
			if ($pos != 1) {
				$teacherTopics [$ttCode] ["gradeSequence"] = 3;
			} else {
				$teacherTopics [$ttCode] ["gradeSequence"] = 2;
			}
			$teacherTopics [$ttCode] [3] = $min_grade . "-" . $max_grade;
		}
		if ($max_grade == "" && $min_grade == "") {
			$teacherTopics [$ttCode] [3] = $cls;
			$teacherTopics [$ttCode] ["gradeSequence"] = 1;
		}
		
		// $teacherTopics[$ttCode]['category'] = '3-notCustomized';
	}
	
	foreach ( $customizedTopicsArray as $ttCode => $tmpTopicArray ) {
		foreach ( $tmpTopicArray as $key => $tmpCustomTopicArray ) {
			$query2 = "select A.customizedBy from adepts_customizedTopicDetails A inner join adepts_teacherTopicMaster B on A.code=B.customCode where B.teacherTopicCode='" . $tmpCustomTopicArray [0] . "'";
			$result2 = mysql_query ( $query2 );
			while ( $line1 = mysql_fetch_array ( $result2 ) ) {
				$customizedBy = $line1 [0];
			}
			if ($customizedBy == "") {
				$query1 = "select lastModifiedby from adepts_teacherTopicActivation where teacherTopicCode='" . $tmpCustomTopicArray [0] . "'";
				$result1 = mysql_query ( $query1 );
				while ( $line = mysql_fetch_array ( $result1 ) ) {
					$customizedBy = $line [0];
				}
			}
			$teacherTopics [$tmpCustomTopicArray [0]] [4] = $customizedBy;
			$teacherTopics [$tmpCustomTopicArray [0]] [0] = $tmpCustomTopicArray [1];
			$teacherTopics [$tmpCustomTopicArray [0]] [1] = "Custom - " . $tmpCustomTopicArray [2];
			$teacherTopics [$tmpCustomTopicArray [0]] [2] = $tmpCustomTopicArray [0];
			$teacherTopics [$tmpCustomTopicArray [0]] [3] = $tmpCustomTopicArray [3];
			$teacherTopics [$tmpCustomTopicArray [0]] ["gradeSequence"] = 1;
			if (strcmp ( $customizedBy, $teacherName ) == 0) {
				$teacherTopics [$tmpCustomTopicArray [0]] ['category'] = '1-customizedByYou';
			} else {
				$teacherTopics [$tmpCustomTopicArray [0]] ['category'] = '2-customizedByOthers';
			}
		}
	}
	
	uasort ( $teacherTopics, "ttSortHelper" );
	
	return $teacherTopics;
}
function isOtherGrades($grades, $cls) {
	$isOtherGrades = 1;
	$gradeArr = explode ( ',', $grades );
	if (sizeof ( $gradeArr ) == 1) {
		if ($gradeArr [0] == $cls) {
			$isOtherGrades = 0;
		}
	} else {
		if ($gradeArr [0] <= $cls && $gradeArr [(sizeof ( $gradeArr )) - 1] >= $cls) {
			$isOtherGrades = 0;
		}
	}
	return $isOtherGrades;
}
function ttSortHelper($a, $b) {
	if (strcmp ( $a ['category'], $b ['category'] ) == 0) {
		if ($a [3] == $b [3]) {
			if (strcmp ( $a [0], $b [0] ) < 0) {
				//
				return - 1;
			} else if (strcmp ( $a [0], $b [0] ) < 0) {
				return 0;
			} else {
				return 1;
			}
		} else if ($a [3] < $b [3]) {
			return - 1;
		} else {
			return 1;
		}
	} else if (strcmp ( $a ['category'], $b ['category'] ) < 0) {
		return - 1;
	} else {
		return 1;
	}
}
function checkForClusters($ttCode, $flow) {
	$num = 1;
	if (strtoupper ( $flow ) == "MS" || strtoupper ( $flow ) == "CBSE" || strtoupper ( $flow ) == "ICSE" || strtoupper ( $flow ) == "IGCSE") {
		$sq = "SELECT B.clusterCode FROM adepts_teacherTopicClusterMaster A, adepts_clusterMaster B
					 WHERE teacherTopicCode='$ttCode' AND status='Live' AND A.clusterCode=B.clusterCode AND " . $flow . "_level <>''";
		$rs = mysql_query ( $sq );
		$num = mysql_num_rows ( $rs );
	}
	return $num;
}
function getCustomizedTopicCode($clusters, $username, $schoolCode) {
	$customCode = "";
	$query = "SELECT code FROM adepts_customizedTopicDetails WHERE clusterCodes='$clusters'";
	$result = mysql_query ( $query ) or die ( "Error while fetching details" );
	if ($line = mysql_fetch_array ( $result )) {
		$customCode = $line ['code'];
	} else {
		$query = "INSERT INTO adepts_customizedTopicDetails (clusterCodes, customizedBy, schoolCode) VALUES ('$clusters','$username',$schoolCode)";
		$result = mysql_query ( $query ) or die ( "Error while saving customization of learning units" );
		$customCode = mysql_insert_id ();
	}
	return $customCode;
}
function getClustersChosen($schoolCode, $cls, $section, $ttCode) {
	$clusterArray = array ();
	// Get the code of customized topic for the TT
	$query = "SELECT flow FROM adepts_schoolTeacherTopicFlow
	WHERE  schoolCode=$schoolCode AND class=$cls AND teacherTopicCode='$ttCode'";
	if ($section != "")
		$query .= " AND section='$section'";
	
	$result = mysql_query ( $query ) or die ( mysql_error () . $query );
	if ($line = mysql_fetch_array ( $result )) // old approach where in case of customization same teacher topic code was used
	{
		$code = trim ( substr ( $line [0], 9 ) );
		$query = "SELECT clusterCodes FROM adepts_customizedTopicDetails WHERE code='$code'";
		$result = mysql_query ( $query ) or die ( mysql_error () . $query );
		$line = mysql_fetch_array ( $result );
		
		$clusterArray = explode ( ",", $line [0] );
	} else {
		$query = "SELECT customCode FROM adepts_teacherTopicMaster WHERE schoolCode=$schoolCode AND class=$cls AND teacherTopicCode='$ttCode' AND customTopic=1 AND live=1";
		$result = mysql_query ( $query ) or die ( mysql_error () . $query );
		$line = mysql_fetch_array ( $result );
		$code = $line [0];
		
		$query = "SELECT clusterCodes FROM adepts_customizedTopicDetails WHERE code='$code'";
		$result = mysql_query ( $query ) or die ( mysql_error () . $query );
		$line = mysql_fetch_array ( $result );
		
		$clusterArray = explode ( ",", $line [0] );
	}
	return $clusterArray;
}
/**
 * Function to get the no.
 * of attempts on the topic for the class.
 */
function getNoOfAttempts($schoolCode, $cls, $section, $ttCode) {
	$query = "SELECT count(ttAttemptID)
	          FROM   adepts_userDetails a, " . TBL_TOPIC_STATUS . " b
	          WHERE  a.userID=b.userID AND category='STUDENT' AND subcategory='School' AND enabled=1 AND
	          schoolCode=$schoolCode AND childClass='$cls' AND teacherTopicCode='$ttCode'";
	if ($section != "")
		$query .= " AND childSection='$section'";
	
	$result = mysql_query ( $query ) or die ( "Error in fetching no. of attempts on the topic" );
	$line = mysql_fetch_array ( $result );
	$noOfAttempts = $line [0];
	return $noOfAttempts;
}
function saveMapping($schoolCode, $cls, $section, $ttCode, $flow) {
	// Query to check if there is a previous mapping saved for this topic, if yes update the flow else save the mapping as a new entry
	$query = "SELECT count(*) FROM adepts_schoolTeacherTopicFlow WHERE schoolCode=$schoolCode AND class=$cls AND teacherTopicCode='$ttCode'";
	if ($section != "")
		$query .= "  AND section='$section'";
	$result = mysql_query ( $query ) or die ( "Error in fetching previous details of customization" );
	$line = mysql_fetch_array ( $result );
	if ($line [0] == 0) {
		$query = "INSERT INTO adepts_schoolTeacherTopicFlow (schoolCode, class, section, teacherTopicCode, flow, lastModifiedBy)
		VALUES ($schoolCode,$cls, '$section','$ttCode','$flow','" . $_SESSION ['username'] . "')";
	} else {
		$query = "UPDATE adepts_schoolTeacherTopicFlow SET flow='$flow' WHERE schoolCode=$schoolCode AND class=$cls AND section='$section' AND teacherTopicCode='$ttCode'";
	}
	mysql_query ( $query ) or die ( "Error while saving the customized mapping<br/>" );
	
	// Check if the change is made in currently active topic - if so update the flow in the activation table for students to follow the changed mapping.
	// This case can happen when the topic is active and no student has started the topic yet, so it will allow the teacher to change the mapping till then.
	$query = "SELECT srno FROM adepts_teacherTopicActivation WHERE schoolCode=$schoolCode AND class=$cls AND teacherTopicCode='$ttCode' AND deactivationDate='0000-00-00'";
	if ($section != "")
		$query .= "  AND section='$section'";
	$result = mysql_query ( $query ) or die ( "Error in query for checking topic activated or not" );
	if ($line = mysql_fetch_array ( $result )) {
		$query = "UPDATE adepts_teacherTopicActivation SET flow='$flow' WHERE srno=" . $line ['srno'];
		mysql_query ( $query ) or die ( "Error in updating the flow in activation table" );
	}
}
function createCustomTT($ttCode, $schoolCode, $cls, $clusterArray, $username, $ttDescription) {
	$customCode = "";
	$newTTCode = "";
	$insertFlag = 1;
	$clustersChosen = implode ( ",", $clusterArray );
	// Check if a customized TT for the school/class combination already present i.e. customized by some other section
	$query = "SELECT teacherTopicCode, customCode FROM adepts_teacherTopicMaster WHERE customTopic=1 AND schoolCode=$schoolCode AND class=$cls AND parentTeacherTopicCode='$ttCode' AND live=1";
	$result = mysql_query ( $query ) or die ( mysql_error () . $query );
	while ( $line = mysql_fetch_array ( $result ) ) {
		$customCode = $line ['customCode'];
		$query = "SELECT clusterCodes FROM adepts_customizedTopicDetails WHERE code='$customCode'";
		$cluster_result = mysql_query ( $query ) or die ( "Error while fetching details" );
		if ($cluster_line = mysql_fetch_array ( $cluster_result )) {
			$clusterCodes = $cluster_line ['clusterCodes'];
			if ($clusterCodes == $clustersChosen) // same set of clusters already customized previously for this school/class
			{
				$newTTCode = $line ['teacherTopicCode'];
				$insertFlag = 0;
				break;
			}
		}
	}
	if ($insertFlag) {
		$query = "INSERT INTO adepts_customizedTopicDetails(clusterCodes, customizedBy, schoolCode) VALUES ('$clustersChosen','$username',$schoolCode)";
		$result = mysql_query ( $query ) or die ( "Error while saving customization of learning units" );
		$customCode = mysql_insert_id ();
		
		$query = "SELECT teacherTopicDesc, mappedToTopic, classification FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$ttCode'";
		$result = mysql_query ( $query ) or die ( mysql_error () . $query );
		$line = mysql_fetch_array ( $result );
		$classification = $line ['classification'];
		$desc = $line ['teacherTopicDesc'];
		$mappedToTopic = $line ['mappedToTopic'];
		// Changes made for mantis: 8219
		$desc = $ttDescription;
		// $desc = getNewTeacherTopicDesc($ttCode,$schoolCode, $cls, $desc);
		
		$newTTCode = getNewTTCode ( $_SESSION ['isOffline'] );
		
		$query = "INSERT INTO adepts_teacherTopicMaster (teacherTopicCode, teacherTopicDesc, live, subjectno, mappedToTopic, classification, customTopic, schoolCode, class, parentTeacherTopicCode, customCode)
		VALUES ('$newTTCode','" . mysql_escape_string ( $desc ) . "',1," . SUBJECTNO . ",'$mappedToTopic','$classification',1,$schoolCode,$cls,'$ttCode','$customCode')";
		mysql_query ( $query ) or die ( "Error in saving custom TT" );
	}
	return "Custom - " . $customCode . "~" . $newTTCode;
}
function getNewTTCode($isOffline) {
	$q = "SELECT max(cast(substring(teacherTopicCode,3) as unsigned)) FROM adepts_teacherTopicMaster";
	if ($isOffline && SERVER_TYPE=="LOCAL") {
		$sq = "SELECT abbreviation FROM adepts_offlineSchools WHERE schoolCode=" . $_SESSION ["schoolCode"];
		$rs = mysql_query ( $sq );
		$rw = mysql_fetch_assoc ( $rs );
		$q .= " WHERE teacherTopicCode LIKE '" . $rw ['abbreviation'] . "%'";
	}
	$r1 = mysql_query ( $q );
	$l1 = mysql_fetch_array ( $r1 );
	$no = $l1 [0] + 1;
	$no = str_pad ( $no, 3, "0", STR_PAD_LEFT );
	if ($isOffline && SERVER_TYPE=="LOCAL")
		$newTTCode = $rw ['abbreviation'] . $no;
	else
		$newTTCode = "TT" . $no;
	return $newTTCode;
}
function getNewTeacherTopicDesc($ttCode, $schoolCode, $class, $oldDesc) {
	$no = 1;
	$query = "SELECT max(cast(substring_index(teacherTopicDesc,'- Custom ',-1) as unsigned)) FROM adepts_teacherTopicMaster
	WHERE  customTopic=1 AND schoolCode=$schoolCode AND class=$class AND parentTeacherTopicCode='$ttCode'";
	$result = mysql_query ( $query );
	
	if ($line = mysql_fetch_array ( $result ))
		$no = $line [0] + 1;
	$newDesc = $oldDesc . " - Custom " . $no;
	return $newDesc;
}
function getCustomTeacherTopicDescSuggestion($ttCode, $schoolCode, $class) {
	$no = 1;
	$query = "SELECT count(teacherTopicCode) FROM adepts_teacherTopicMaster
	WHERE customTopic=1 AND schoolCode=$schoolCode AND class=$class AND parentTeacherTopicCode='$ttCode'";
	$result = mysql_query ( $query );
	
	if ($line = mysql_fetch_array ( $result ))
		$no = $line [0] + 1;
	$newDesc = " - Custom " . $no;
	return $newDesc;
}
function isCustomizedTopic($ttCode) {
	$parentTT = "";
	$query = "SELECT customTopic, parentTeacherTopicCode FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$ttCode'";
	$result = mysql_query ( $query );
	$line = mysql_fetch_array ( $result );
	if ($line [0] == 1)
		$parentTT = $line [1];
	return $parentTT;
}
function getTimedTestMappedToCluster($clusterCode) {
	$timedTestArray = array ();
	$query = "SELECT timedTestCode, description FROM adepts_timedTestMaster WHERE linkedToCluster='$clusterCode' AND status='Live'";
	$result = mysql_query ( $query ) or die ( "Error in fetching timed test details" );
	while ( $line = mysql_fetch_array ( $result ) ) {
		$timedTestArray [$line [0]] ["desc"] = $line [1];
	}
	return $timedTestArray;
}
function getDailyPracticeMappedToCluster($clusterCode) {
	$dailyPrcaticeArray = array ();
	$query = "SELECT practiseModuleId, description,dailyDrill FROM practiseModuleDetails WHERE linkedToCluster='$clusterCode' AND status='Approved'";
	$result = mysql_query ( $query ) or die ( "Error in fetching practice module details" );
	while ( $line = mysql_fetch_array ( $result ) ) {
		$dailyPrcaticeArray [$line [0]] ["desc"] = $line [1];
		$dailyPrcaticeArray [$line [0]] ["drill"] = $line [2];
	}
	return $dailyPrcaticeArray;
}
function getActivitiesMappedToCluster($clusterCode) {
	$activitiesArray = array ();
	$query = "SELECT gameID, gameDesc, type FROM adepts_gamesMaster WHERE linkedToCluster='$clusterCode' AND live='Live'";
	$result = mysql_query ( $query ) or die ( "Error in fetching cluster details" );
	while ( $line = mysql_fetch_array ( $result ) ) {
		$activitiesArray [$line [0]] ["desc"] = $line [1];
	}
	return $activitiesArray;
}
function findRemedialCluster($clusterCode)
{
	$query  = "SELECT remedialItemCode,remedialItemDesc FROM adepts_remedialItemMaster WHERE linkedToCluster = '$clusterCode' AND status='Live'";
	$result = mysql_query($query) or die(mysql_error());
	while($user_row = mysql_fetch_array($result))
	{
		$remedialArray[] = $user_row;
	}
	return $remedialArray;
}
function getRemedialCluster($clusterCode)
{
	$nextClusterCode = "";
	$query  = "SELECT remedialItemCode,remedialItemDesc,misconception FROM adepts_remedialItemMaster WHERE linkedToCluster IN ('$clusterCode') AND status='Live'";
	$result = mysql_query($query) or die(mysql_error());
	while($user_row = mysql_fetch_array($result))
	{
		$remedialArray[] = $user_row;
	}
	return $remedialArray;
}
function getCustomizeCluster($ttCode, $schoolCode, $class, $section) {
	$arrayCluster = array ();
	$clusterList = "";
	$sq = "SELECT clusterCodes FROM adepts_teacherTopicMaster A, adepts_customizedTopicDetails B, adepts_teacherTopicActivation C
	WHERE A.customCode=B.code AND parentTeacherTopicCode='$ttCode' AND A.teacherTopicCode=C.teacherTopicCode
	AND C.schoolCode=$schoolCode AND C.class=$class AND C.section='$section' and deactivationDate = '0000-00-00'";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$clusterList	=	$clusterList.$rw[0].",";
	}
	$clusterList	=	substr($clusterList,0,-1);
	$clusterList	=	"'".str_replace(",","','",$clusterList)."'";
	return $clusterList;
}

function getTeacherTopicDescList($schoolCode, $class) {
	$ttDescList = array();
	$query = "SELECT teacherTopicDesc FROM adepts_teacherTopicMaster WHERE schoolCode = $schoolCode AND class = $class";
	$result = mysql_query($query);
	while($row = mysql_fetch_array($result)) {
		$ttDescList[] = $row[0];
	}
	return $ttDescList;
}

function getSDLDetails($clusterArray)
{
	$sdlArray = array();
	//$clusterArray = array_values(array_diff($clusterArray,$pcClusterArray));	//Remove practice cluster, since the questions of pc are not mapped to sdl
	$query = "SELECT clusterCode, group_concat(DISTINCT subdifficultylevel ORDER BY subdifficultylevel)
	FROM   adepts_questions
	WHERE  clusterCode = '$clusterArray' AND status=3
	GROUP BY clusterCode";
	$result = mysql_query($query) or die("Error in getting SDL details!");
		while ($line = mysql_fetch_array($result))
	{
	$sdlArray[$line['clusterCode']] = $line[1];
	}
	return $sdlArray;
}
?>
<script type="text/javascript">
	$(document).ready(function(){
		$("#classes").css("font-size","1.4em");
		$("#classes").css("margin-left","40px");
		var currentActive = parseInt($("#currenScrollId").val());
		var totalCount = $("#totalCount").val();
		if(currentActive * 4 < totalCount){
			$(".arrow-rightin").hide();
		}
		else{
			$(".arrow-rightin").show();
		}
		if(parseInt(currentActive) - 1 >= 1){
			$(".arrow-leftin").hide();
		}
		else{
			$(".arrow-leftin").show();
		}
		
		$(".plus-sign").click(function(){
			var showTr = $(this).attr("id").split("-");
			if($(this).html() != "-"){
				$("#cluster-tr-"+showTr[2]).show("slow");
				$(this).html("-");
			}
			else{
				$("#cluster-tr-"+showTr[2]).hide("slow");
				$(this).html("+");
			}
			
		});
		$(".arrow-left,.arrow-right").click(function(){
			var currentActive = parseInt($("#currenScrollId").val());
			var totalCount = $("#totalCount").val();
			if(currentActive * 4 < totalCount){
				$(".arrow-rightin").hide();
			}
			else{
				$(".arrow-rightin").show();
			}
			if(parseInt(currentActive) - 1 >= 1){
				$(".arrow-leftin").hide();
			}
			else{
				$(".arrow-leftin").show();
			}
		});

		$('#container').on('click', '*', function(e) {
			if($('.bulkActivateDeactivatePrompt').is(':visible')){
				if(!$(e.target).closest('.bulkActivateDeactivatePrompt').length){
					$('.bulkActivateDeactivatePrompt').hide();
					$(".arrow-black-side").css("visibility","hidden");		
					e.stopPropagation();
				}
			}
		});

		$('.bulkActivateDeactivatePrompt').delegate('.selectAllSections','change',function(){
	        if(this.checked){
	            $(this).parent().parent().find('.checkboxSection:not(:disabled)').each(function(){
	                this.checked = true;
	            });
	        }else{
	             $(this).parent().parent().find('.checkboxSection:not(:disabled)').each(function(){
	                this.checked = false;
	            });
	        }
	    });
	    
	    $('.bulkActivateDeactivatePrompt').delegate('.checkboxSection:not(:disabled)','change',function(){
	        if($(this).parent().parent().find('.checkboxSection:checked').length == $('.checkboxSection:not(:disabled)').length){
	            $(this).parent().parent().find('.selectAllSections').prop('checked',true);
	        }else{
	            $(this).parent().parent().find('.selectAllSections').prop('checked',false);
	        }
	    });

	  $('#fromDate,#toDate').live('focus', function()
	  {	  		  	
	  		 $("#fromDate").datepicker({
				dateFormat: 'dd-mm-yy',
				minDate: '-1Y',
				maxDate: '+1Y',																		
				onSelect: function( selectedDate ) {
					$("#toDate").datepicker( "option", "minDate", selectedDate );					
					$("#fromDate").removeClass('durationBorder');					
				},
			});	
	 
			$( "#toDate" ).datepicker({
				dateFormat: 'dd-mm-yy',					
				maxDate: '+1Y',
				minDate: '-1Y',					
				onSelect: function( selectedDate ) {
				$("#toDate").removeClass('durationBorder');
			}
			});	
	  });	   	 
	});
	// var isShift=false;
	// var seperator = "-";
	// function DateFormat(txt , keyCode)
	// {
	//     if(keyCode==16)
	//         isShift = true;
	//     //Validate that its Numeric
	//     if(((keyCode >= 48 && keyCode <= 57) || keyCode == 8 ||
	//          keyCode <= 37 || keyCode <= 39 ||
	//          (keyCode >= 96 && keyCode <= 105)) && isShift == false)
	//     {
	//         if ((txt.value.length == 2 || txt.value.length==5) && keyCode != 8)
	//         {
	//             txt.value += seperator;
	//         }
	//         return true;
	//     }
	//     else
	//     {
	//         return false;
	//     }
	// }
	function checkCovering()
    {
    	if($("#notCovered").is(':checked'))
    	{
    		$('#fromDate').attr("disabled","disabled");
    		$('#toDate').attr("disabled","disabled");
    		$('#fromDate').val('');
    		$('#toDate').val('');
    		$("#fromDate").removeClass('durationBorder');
    		$("#toDate").removeClass('durationBorder');
    		$("#toDate").datepicker( "option", "minDate", "-1Y" );
    	}
    	else
    	{
    		$('#fromDate').removeAttr("disabled");
    		$('#toDate').removeAttr("disabled");
    	}
    }	
	function goRight(){
		var currentActive = $("#currenScrollId").val();
		var totalCount = $("#totalCount").val();
		if(parseInt(currentActive) * 4 < totalCount){
			$(".fadeinout-"+currentActive).hide("slow");
			var nectShow = parseInt(currentActive)+1;
			$(".fadeinout-"+nectShow).toggle("slide", {
	             direction: "right"
	         },"slow");
			$("#currenScrollId").val(parseInt(currentActive)+1);
		}
	}
	function goLeft(){
		var currentActive = $("#currenScrollId").val();
		var totalCount = $("#totalCount").val();
		if(parseInt(currentActive) - 1 >= 1){
			$(".fadeinout-"+currentActive).hide("slow");
			var nectShow = parseInt(currentActive)-1;
			$(".fadeinout-"+nectShow).show("slow");
			$("#currenScrollId").val(parseInt(currentActive)-1);
		}
	}

	var clustersTotal = <?php echo json_encode($totalClusters); ?>;

	var customizeCluster	=	new Array(<?=getCustomizeCluster($ttCode,$schoolCode,$cls,$section)?>);



	function notify()
	{
		alert('This learning unit is disabled as you have already covered it earlier. To enable it, you should de-activate the topic where it is currently selected.');
		 return false;
	}
	function notifyTooltip()
	{
		if(navigator.userAgent.indexOf("Android") != -1 || window.navigator.userAgent.indexOf("iPad")!=-1 || window.navigator.userAgent.indexOf("iPhone")!=-1)
		{
			alert("Providing the duration in which the selected LUs were covered in class will help in synchronizing Mindspark with your classroom teaching");
			return false;
		}
		else
		{
			return true;
		}
		
	}
	function checkIfclustersAreCustomized(flow)
	{
		var liveClusters	=	new Array(<?=$liveClusterList?>);
		
		for(var i=0;i<clustersTotal;i++)
		{
			
			if($.inArray($("#"+"chkCluster"+i).val(),liveClusters)!=-1 || $.inArray($("#"+"chkCluster"+i).val(),customizeCluster)!=-1)
			{
				document.getElementById('messagediv'+i).style.display = "block";
				$("#"+"chkCluster"+i).attr("disabled", true);
				

				$("#"+"chkCluster"+i).attr("title","This learning unit is disabled as you have already covered it earlier. To enable it, you should de-activate the topic where it is currently selected.");
									
				$("#"+"rowCustom"+i).attr("title","This learning unit is disabled as you have already covered it earlier. To enable it, you should de-activate the topic where it is currently selected.");
				//$("#"+"rowCustom"+i).attr("onclick","alert('This learning unit is disabled as you have already covered it earlier. To enable it, you should de-activate the topic where it is currently selected.')");
				
			}
			else
			{
				$("#"+"chkCluster"+i).attr("disabled", false);
				
			}
		}
	}

	function getEstimatedTimeToCompleteTopic(flow, checkID)
	{
		var sdlsCorrToFlow = new Array();
		sdlsCorrToFlow[flow] = 0;
		
		var clusterDetails = document.getElementById('clusterDetailsStr').value;

		var clusterDetailsArr = clusterDetails.split("~");
		for(var i=0; i<clusterDetailsArr.length; i++)
		{ 
			var tempArr = clusterDetailsArr[i].split("##");
			
			clusterDetailsArr[i] = new Array(tempArr.length);
			for(var j=0; j<tempArr.length; j++)
			{
				clusterDetailsArr[i][j] = tempArr[j];
			}


			if(flow == "MS")
			{	
				
				if(clusterDetailsArr[i][2] == 'Y')
				{
					sdlsCorrToFlow[flow] += parseInt(clusterDetailsArr[i][7]);
				}
					
				
			}
			else if(flow == "CBSE")
			{
				if(clusterDetailsArr[i][3] == 'Y')
				{
					sdlsCorrToFlow[flow] += parseInt(clusterDetailsArr[i][7]);
				}
					
					
			}
			else if(flow == "ICSE")
			{
				if(clusterDetailsArr[i][4] == 'Y')
				{
					sdlsCorrToFlow[flow] += parseInt(clusterDetailsArr[i][7]);
				}
			}
			else if(flow == "IGCSE")
			{
				if(clusterDetailsArr[i][9] == 'Y')
				{
					sdlsCorrToFlow[flow] += parseInt(clusterDetailsArr[i][7]);
				}
			}
			else if(flow == "Custom")
			{
				var clstr = "chkCluster"+i;
				if($('#'+clstr).is(':checked'))
				{
					sdlsCorrToFlow[flow] += parseInt(clusterDetailsArr[i][7]);
				}
			}
			else if(flow == "MSOld")
			{
				if(clusterDetailsArr[i][6] == 'Y')
					sdlsCorrToFlow[flow] += clusterDetailsArr[i][7];
			}
		}

		var avgTimePerSdl = document.getElementById('avgTimePerSdl').value;
		var TimeToCompleteTheTopic = parseFloat(avgTimePerSdl)*sdlsCorrToFlow[flow];
		TimeToCompleteTheTopic = Math.round(TimeToCompleteTheTopic*Math.pow(10,1))/Math.pow(10,1);
		var tempHTML = "<div><br/>Estimated time to complete the topic for selected flow: "+TimeToCompleteTheTopic+" minutes<br/><br/></div>"
		document.getElementById('estimation').innerHTML = tempHTML;

	}
	function isSelected(){
		var totalchecked=0;
		var checks = document.getElementsByName('chkCluster[]');
		var boxLength = checks.length;
		for ( i=0; i < boxLength; i++ ) {
			if(checks[i].checked == true)
				totalchecked++;
	    }
	    if(totalchecked==0)
	    	return false;
	    else
	    	return true;
	}
	function validate(val,notCovered,fromDate,toDate)
	{
		if (typeof notCovered === "undefined" || notCovered === null) { 
      		notCovered =0; 
	    }
	    if (typeof fromDate === "undefined" || fromDate === null) { 
	      fromDate = ''; 
	    }
	    if (typeof toDate === "undefined" || toDate === null) { 
	      toDate = ''; 
	    }
		if(val==1)
			$("#saveAndActivate").val("1");
		else
			$("#saveAndActivate").val("0");
		var flow;
		var objArray = document.forms['frmTeacherTopicFlow'].elements['rdTTFlow'];
		radioLength = objArray.length;
		for(var i = 0; i < radioLength; i++) {
			if(objArray[i].checked) {
				flow = objArray[i].value;
				break;
			}
		}
		if(flow=='Custom')
		{
			var msClusters = 0;
			var cbseClusters = 0;
			var icseClusters = 0;
			var igcseClusters = 0;
			var msFlow = true;
			var cbseFlow = true;
			var icseFlow = true;
			var igcseFlow = true;
			$("input[name='chkCluster[]']:checked").each(function() { 
				var levelList = $(this).attr("class");
				var levelListArr = levelList.split("_");
				for(var i=0;i<4;i++)
				{
					var levelCountArr = levelListArr[i].split("@");
					if(i==0)
					{
						if(levelCountArr[1]==0)
						{
							msClusters = 0;
							msFlow = false;
						}
						else
							msClusters = parseInt(levelCountArr[1]) + parseInt(msClusters);
					}
					else if(i==1)
					{
						if(levelCountArr[1]==0)
						{
							cbseClusters = 0;
							cbseFlow = false;
						}
						else
							cbseClusters = parseInt(levelCountArr[1]) + parseInt(cbseClusters);
					}
					else if(i==2)
					{
						if(levelCountArr[1]==0)
						{
							icseClusters = 0;
							icseFlow = false;
						}
						else
							icseClusters = parseInt(levelCountArr[1]) + parseInt(icseClusters);
					}
					else if(i==3)
					{
						if(levelCountArr[1]==0)
						{
							igcseClusters = 0;
							igcseFlow = false;
						}
						else
							igcseClusters = parseInt(levelCountArr[1]) + parseInt(igcseClusters);
					}
				}
			});
			var totalLevelCountArr = $("#totalLevelCount").val().split("-");
			if(msClusters == totalLevelCountArr[0] && msClusters>0 && msFlow===true)
				$("#generatedFlow").val("MS");
			else if(cbseClusters == totalLevelCountArr[1] && cbseClusters>0 && cbseFlow===true)
				$("#generatedFlow").val("CBSE");
			else if(icseClusters == totalLevelCountArr[2] && icseClusters>0 && icseFlow===true)
				$("#generatedFlow").val("ICSE");
			else if(igcseClusters == totalLevelCountArr[3] && igcseClusters>0 && igcseFlow===true)
				$("#generatedFlow").val("IGCSE");
			else
				$("#generatedFlow").val("CUSTOM");
		    if(!isSelected())
		    {
	    		alert("Please choose at least one cluster!");
				$("#saveAndActivate").val("0");
		    }
		    else
			{	
				<?php if($noOfAttempts==0) { ?>	
				var generatedFlow = $("#generatedFlow").val();
				if(generatedFlow!="CUSTOM")
				{
					generatedFlow = (generatedFlow == 'MS') ? 'Mindspark Recommended':generatedFlow;
					if(val==1)
					{
						var newMessage = "The learning units customized are same as in "+generatedFlow+" curriculum. Hence, activating " +generatedFlow+" curriculum.";
						var prompts=new Prompt({
							text:newMessage,
							type:'confirm',
							label2:'Cancel',
							label1:'Ok',
							func1:function(){
								$("#notCoveredValue").val(notCovered);
								$("#fromDateValue").val(fromDate);
								$("#toDateValue").val(toDate);
								$("#frmTeacherTopicFlow").submit();
							},
							func2:function(){
								$("#prmptContainer_createCustom").remove();
							},
							promptId:"createCustom"
						});
					}
					else
					{
						var newMessage = "The learning units customized are same as in "+generatedFlow+" curriculum. Hence, activate "+generatedFlow+" curriculum later.";
						var prompts = new Prompt({
							text:newMessage,
							type:'alert',
							label1:'Ok',
							func1:function(){
								/*$("input[name='chkCluster[]']").removeAttr("checked");*/
								/*$("input[name='chkCluster[]']").attr("disabled",true);
								$("#rdCustom").attr("disabled",true);*/
								/*$("#activateTopic,#seeCustomize").show();*/
								/*$("#saveCustom,#save_activate").hide();
								$("#activateTopic,#seeCustomize").show();*/
								/*$("#rdMS,#rdCBSE,#rdICSE,#rd"+generatedFlow).removeAttr("disabled");*/
								/*$("#rd"+generatedFlow).attr("checked","checked");	*/	
								$("#prmptContainer_createCustom").remove();
							},
							promptId:"createCustom"
						});
					}
				}
				else
				{
				<?php } ?>
					$("#generatedFlow").val("CUSTOM");
					var prompts=new Prompt({
					<?php if(count($topicsActivated)>=15) { ?>
						text:'Please note that this will create a new custom topic. It can be activated only after deactivating some other topics. Mindspark does not allow more than 15 topics to be active at a time. <a href="<?=WHATSNEW?>helpManual/Too_many_active_topics_in_a_school_is_hazardous_to_a_child_s_health.pdf" target="_blank">Click here</a> to know why.',
					<?php } else { ?>
						text:'Please note that this will create a new custom topic. ',
					<?php } ?>					
						type:'confirm',
						label1:'Ok',
						label2:'Cancel',
						func2:function(){
							$("#prmptContainer_createCustom").remove();
						},
						func1:function(){
							$("#notCoveredValue").val(notCovered);
							$("#fromDateValue").val(fromDate);
							$("#toDateValue").val(toDate);
							$("#frmTeacherTopicFlow").submit();
						},
						promptId:"createCustom"
					});
					<?php if($noOfAttempts==0) { ?>			
				}
			<?php } ?>	
			}
			return false;
		}
		else if(flow=="MS" || flow=="CBSE" || flow=="ICSE" || flow=="IGCSE")
		{
			if(val==2)
			{
			
			}
			else
			{
				var prompts=new Prompt({
					text:"Are you sure you want to activate it in "+flow+" flow?",
					type:'confirm',
					label2:'No',
					label1:'Yes',
					func1:function(){
						$("#notCoveredValue").val(notCovered);
						$("#fromDateValue").val(fromDate);
						$("#toDateValue").val(toDate);
						$("#frmTeacherTopicFlow").submit();
					},
					func2:function(){
						$("#prmptContainer_createCustom").remove();
					},
					promptId:"createCustom"
				});
				return false;
			}
		}
		else
		{
			alert("Please select custom flow.");
			return false;
		}
	}
	function highlightSelection(mode,rows, noofattempts)
	{
		var modeArray = new Array('MS','CBSE','ICSE','IGCSE','Custom');
		if(mode=="MSOld")
			modeArray.push("MSOld");
		var cname;
		var disabledmode = 1;
		if(mode=="Custom")
		{
			disabledmode = 0;
		}
		document.getElementById('rd'+mode).checked = true;

		for(var i=0; i<modeArray.length; i++)
		{
			cname = '';
			if(modeArray[i]==mode)
				cname ='selected';
			document.getElementById('hd'+modeArray[i]).className = cname;
			for(var j=0; j<rows; j++)
			{
				document.getElementById('row'+modeArray[i]+j).className = cname;
			}
		}

		for(var i=0; i<rows; i++)
		{
		    if(document.getElementById('chkCluster'+i))
			    document.getElementById('chkCluster'+i).disabled = disabledmode;
		}
		if(noofattempts>0)
		{
			var objArray = document.getElementsByName('rdTTFlow');
			for(var i=0; i<objArray.length; i++)
			{
			    if(objArray[i].value=="Custom")
			    {
			        continue;
			    }
				objArray[i].disabled = true;
			}
		}
		if($('.btnSave'))
			$('.btnSave').attr("disabled",false);
	}
	function checkIfClusterCustumize(flow, checkID)
	{ 
		if($("#"+checkID).is(":checked") && $.inArray($("#"+checkID).val(),customizeCluster)!=-1)
		{
			if(confirm("This learning unit has already been included as a part of teacher topic customised earlier. would you like to include it again in this current customisation process?\n(NOTE - INCLUSION OF LEARNING UNITS ALREADY CUSTOMISED WILL RESULT IN REPETITION OF QUESTIONS)"))
			{
				getEstimatedTimeToCompleteTopic(flow, checkID);
			}
			else {
				$("#"+checkID).attr("checked",false);
			}
		}
		else
			getEstimatedTimeToCompleteTopic(flow, checkID);		
	}
</script>
<div style="display:none">
<a href="#showQuestionDiv"  id="showQuestionDivClick"></a>
	<div id="showQuestionDiv">
			
	</div>
</div>
<style>
		
.intro-launch {
    background-image: url("images/Help-button.png");
    background-repeat: no-repeat;
    background-size: 24px 24px;
    float: left;
    height: 24px;
    width: 24px;
}
</style>
