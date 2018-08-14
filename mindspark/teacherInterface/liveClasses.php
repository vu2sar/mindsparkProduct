<?php
	if(isset($_REQUEST["go"])){
		error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
		@include("../userInterface/check1.php");
		include("../userInterface/constants.php");
		include("../userInterface/classes/clsUser.php");
	}
	else include("header.php");
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	//error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
	include("functions/functions.php");
	include("classes/testTeacherIDs.php");
	include("functions/liveClassFunctions.php");
	include_once("../userInterface/classes/clsTopicProgress.php");
	include("../slave_connectivity.php");
		
	$userID     = $_SESSION['userID'];
	$schoolCode = isset($_SESSION['schoolCode'])?$_SESSION['schoolCode']:"";
	$user   = new User($userID);
    $todaysDate = date("d");
    if(isset($_REQUEST["go"]))
    {
    	$classArray	=	array();
    	$sectionArray	=	array();
    	$childClass	=	$_POST["childClass"];
    	$status	=	0;
    	$childSection	=	$_POST["childSection"];
    	$classArray[]	=	$childClass;
    	$sectionArray[]	=	$_POST["childSection"];
    	$_SESSION["topicData"]	=	"";
    	$liveStudent=1;
    	$userIDArrayLive     = getStudentDetails1($childClass, $schoolCode, $childSection,$status);
    	echo json_encode($userIDArrayLive);exit;
    }

    //exit;
	if(!isset($_SESSION["topicData"]))
	{
		$_SESSION["topicData"]	=	"";
		$_SESSION["ttName"]	=	"";
	}
	if($_REQUEST["live"])
	{
		$_SESSION["topicData"]	=	"";
		$_SESSION["ttName"]	=	"";
		$live = $_REQUEST["live"];
	}
	else
		$live=0;

	if(strcasecmp($user->category,"School Admin")==0)
	{
		$query  = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
			       FROM     adepts_userDetails
			       WHERE    schoolCode=$schoolCode AND category='STUDENT' AND subcategory='School' AND enabled=1 AND endDate>=curdate() 
				   AND subjects like '%".SUBJECTNO."%'
			       GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
	}
	elseif (strcasecmp($user->category,"Teacher")==0)
	{
		$query = "SELECT   class, group_concat(distinct section ORDER BY section)
				  FROM     adepts_teacherClassMapping
				  WHERE    userID = $userID AND subjectno=".SUBJECTNO."
				  GROUP BY class ORDER BY class, section";
	}
	elseif (strcasecmp($user->category,"Home Center Admin")==0)
	{
		$query  = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
			       FROM     adepts_userDetails
			       WHERE    category='STUDENT' AND subcategory='Home Center' AND schoolCode=$schoolCode AND enabled=1 
				   AND endDate>=curdate() AND subjects like '%".SUBJECTNO."%'
			       GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
	}
	else
	{
		echo "You are not authorised to access this page!";
		exit;
	}
		
	$classArray = $sectionArray =  array();
	$hasSections = false;
	$result = mysql_query($query) or die(mysql_error());
	while($line=mysql_fetch_array($result))
	{
		array_push($classArray, $line[0]);
		if($line[1]!='')
			$hasSections = true;
		$sections = explode(",",$line[1]);
		$sectionStr = "";
		for($i=0; $i<count($sections); $i++)
		{
			$classSectionArr[]	=	$line[0].$sections[$i];
			if($sections[$i]!="")
				$sectionStr .= $sections[$i].",";
		}
		$sectionStr = substr($sectionStr,0,-1);
		array_push($sectionArray, $sectionStr);
	}
	$ref="";
	if(isset($_GET['ref']))
		$ref	=	$_GET['ref'];
?>
<?php
	$sec=0;
	$classAll	=	$classArray;
	$sectionAll	=	$sectionArray;
	$totalClass	=	count($classArray);
	$sectionStr	=	implode(",",$sectionArray);
	$totalsection	=	count(explode(",",$sectionStr));
?>

<title>Live Classes</title>
<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/topicActivationSummary.css" rel="stylesheet" type="text/css">
<script src="libs/angularjs/1.2.5/angular.min.js"></script>
<script>
    angular.module("lcapp", [])
        .controller('liveClass', ['$scope', '$timeout', '$interval', '$http', function($scope, $timeout, $interval, $http) {
            var liveCl=$scope;
            liveCl.userIDArrayLive=[];
            liveCl.noLive=0;
            liveCl.liveStudent=0;
            liveCl.loadingNewData=0;
            liveCl.childClass=0;
            liveCl.childSection='';
            liveCl.doClick = function() {
            	$timeout.cancel($scope.autoRefresh);            	
            	if (!validateClass()) return false;
            	liveCl.loadingNewData=1;
            	liveCl.childClass=document.getElementById('childClass').value;
            	liveCl.childSection=document.getElementById('childSection').value;
            	//$scope.$apply();
				$timeout(liveCl.loadData,10);
            };
            liveCl.loadData=function(){
            	$.post("liveClasses.php",
	            	{
	                   	go:1,
	                   	childClass:liveCl.childClass,
	                   	childSection:liveCl.childSection
	               	},
	                function(html) {
	                	liveCl.loadingNewData=0;
	                    try{
	                		if (html!=""){
		                    	$scope.userIDArrayLive = JSON.parse(html);
		                    	$scope.noLive=$scope.userIDArrayLive.length>0?0:1;
		                    	$scope.liveStudent=1;
		                    	$scope.$apply();
		                    }
	                    }
	                    catch(e){console.log(e);}
	                    $scope.autoRefresh=$timeout(liveCl.loadData,60000);
	                }
	            );
            }
        }]);
</script>
<style>
	.greenCircle{
		width: 20px;
	    height: 20px;
	    float: left;
	    -moz-border-radius: 10px;
	    -webkit-border-radius: 10px;
	    border-radius: 10px;
	    background-color: green;
	    -moz-box-shadow: inset 0px 0px 3px 1px #999999;
	    -webkit-box-shadow: inset 0px 0px 3px 1px #999999;
	    box-shadow: inset 0px 0px 3px 1px #999999;
	    margin:4px;
	}

	.grayCircle{
		width: 20px;
	    height: 20px;
	    float: left;
	    -moz-border-radius: 10px;
	    -webkit-border-radius: 10px;
	    border-radius: 10px;
	    background-color: gray;
	    -moz-box-shadow: inset 0px 0px 3px 1px #999999;
	    -webkit-box-shadow: inset 0px 0px 3px 1px #999999;
	    box-shadow: inset 0px 0px 3px 1px #999999;
	    margin:4px;
	}

	#pagingTable{
		position:relative;
		font-size:1.4em;
		width:95%;
		/*border-bottom:1px solid #626161;*/
		padding-top:3px;
		padding-bottom:3px;
		font-weight:bold;
		color:#626161;
		margin-left:40px;
	}
	#container {
		min-height:525px;
	}
</style>
<!-- <script src="libs/jquery.js"></script> -->
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="libs/tablesort.js"></script>
<script>
	var langType = '<?=$language;?>';
	function load()
	{
		var sideBarHeight = window.innerHeight-95;
		$("#sideBar").css("height",sideBarHeight+"px");
	}
	
</script>

<script>
var gradeArray   = new Array();
var sectionArray = new Array();
var noOfSections = new Array();
<?php
	for($i=0; $i<count($classAll); $i++)
	{
		$sectionStr	=	str_replace(",","','",$sectionAll[$i]);
		echo "gradeArray.push($classAll[$i]);\r\n";
		echo "gradeArray[$classAll[$i]] = new Array('$sectionStr');\r\n";
	}
?>
	$(document).ready(function(e) {
		$(".techerUpperLink").change(function() {
			var val	=	$(this).val();
			changeMode(val);
		});
    });
	
function viewSection(sec)
{
	var obj = document.getElementById('childSection');
	removeAllOptions(obj);
	var grade = document.getElementById('childClass').value;
	if(grade=='') {
		OptNew = document.createElement('option');
		OptNew.text = 'Select';
		OptNew.value = '';
		obj.options.add(OptNew);
		return;
	}
	var allsectionArray	=	new Array();
	allsectionArray	=	gradeArray[grade];
	
	if(gradeArray[grade] == "")
	{
		$(".noSection").hide();
		/*document.getElementById("childSection").disabled=true;*/
		$(".noClass").css("border-right","0px solid #626161");
		noOfSections[grade] = 0;
	}
	else
	{
		$(".noSection").show();
		$(".noClass").css("border-right","1px solid #626161");
		
		noOfSections[grade] = allsectionArray.length;
		if(allsectionArray.length>1)
		{
			OptNew = document.createElement('option');
			OptNew.text = 'Select';
			OptNew.value = '';
			obj.options.add(OptNew);
		}
		/*document.getElementById("childSection").disabled=false;*/
		for (var j=0; j<allsectionArray.length; j++)
		{
			OptNew = document.createElement('option');
			OptNew.text = allsectionArray[j];
			OptNew.value = allsectionArray[j];
			if(sec==allsectionArray[j])
			OptNew.selected = true;
			obj.options.add(OptNew);
		}
	}
}

function validateClass(){
	if($("#childClass").val()==""){
		alert("Please select a class.");
		return false;
	}else if($("#childSection").val()=="" && noOfSections[$("#childClass").val()]!=0){
		alert("Please select a section.");
		return false;
	}else{
		return true;
		//$("#frmMain").submit();
	}
}

function removeAllOptions(selectbox)
{
	var i;
	for(i=selectbox.options.length-1;i>=0;i--)
	{
		selectbox.remove(i);
	}
}
<?php
	if($ref=="feature")
		echo "changeMode('$ref')";
		?>
		$(document).ready(function(e) {
			viewSection('<?=$childSection?>');
		});
	/*<?php if($_SESSION["countDown"]==1) { ?>
		$(document).ready(function(e) {
			$("#countDown").show();
		});
	<?php } ?>*/ 
</script>

<style>
#countDown {
	width:100%;
	position:absolute;
	z-index:1000;
	background-color:#FFFFFF;
	display:none;
	text-align:center;
}
</style>
</head>
<body class="translation" onLoad="load()" onResize="load()" style="overflow: auto" ng-app="lcapp">
<!--<div id="countDown"><img src="http://d2tl1spkm4qpax.cloudfront.net/content_images/newUserInterface/teasers/countDown.gif" width="401" height="401"></div>-->
<?php include("eiColors.php") ?>
<div id="fixedSideBar">
	<?php include("fixedSideBar.php") ?>
</div>
<div id="topBar">
	<?php include("topBar.php"); ?>
</div>
<div id="sideBar">
	<?php include("sideBar.php") ?>
</div>

<div id="container"  ng-controller="liveClass">
	<form id="frmMain" action="" method="post">
		<table id="topicDetails">
			<tr>
				<td width="6%"><label>Class</label></td>
				<td width="24.6%" style="border-right:1px solid #626161" class="noClass">
					<select name="childClass" id="childClass" onChange="viewSection('')">
						<option ng-if="<?=count($classAll)!=1?>" selected value="">Select</option>
						<?php
							foreach($classAll as $class) { 
								$selected="";
								if($childClass==$class)
									$selected	=	"selected";
								?>
								<option value="<?=$class?>" <?=$selected?>><?=$class?></option>
								<?php 
							} ?>
					</select>
				</td>
				<td width="6%" class="noSection"><label style="margin-left:20px;">Section</label></td>
				<td width="21%" class="noSection" style="border-right:1px solid #626161">
					<select name="childSection" id="childSection">
						<option value="">Select</option>
					</select>
				</td>
				<!--<td width="6%" class="noSection"><label style="margin-left:20px;">Status</label></td>
				<td width="25%" class="noSection">
					<select name="status" id="status">
						<option value="0" <?php if(isset($status) && $status=="0") echo "selected"; ?>>All</option>
						<option value="1" <?php if(isset($status) && $status=="1") echo "selected"; ?>>Gone to lower/higher level</option>
						<option value="2" <?php if(isset($status) && $status=="2") echo "selected"; ?>>Low accuracy</option>
						<option value="3" <?php if(isset($status) && $status=="3") echo "selected"; ?>>Present but not doing questions</option>
						<option value="4" <?php if(isset($status) && $status=="4") echo "selected"; ?>>Currently not logged in</option>
						<option value="5" <?php if(isset($status) && $status=="5") echo "selected"; ?>>Doing fine</option>
					</select>
				</td>-->
				<td width="20%">
					<!--<input type="hidden" onclick="validateClass();" name="go" id="go" value="Go" />-->
					<input type="button" ng-click="doClick()" value="Go" ng-show="!loadingNewData"/>
					<!--<input type="button" onclick="validateClass();" id="go" value="Go" />-->
				</td>
			<tr>
		</table>
		<!--<table id="pagingTable">
		    <td>TOPIC RECOMMENDED FOR REMEDIATION</td>
		</table>-->
	</form>

	<table id='pagingTable' width="100%">
		<tr  ng-show="!liveStudent && !loadingNewData"><td>Please select a class and section. </td></tr>
		<tr  ng-show="loadingNewData"><td style="text-align:center;"><img src="assets/loadingImg.gif" /></td></tr>
		<tr  ng-show="liveStudent && noLive"><td>No students found in this class for the status you selected. </td></tr>
	</table>
	<table cellspacing="0" cellpadding="3" class="gridtable" border="1" width="100%" ng-show="liveStudent && !noLive && !loadingNewData">
		<thead>
			<tr style="font-weight:bold;">
				<td class="header" width="30px;">&nbsp;</td>
				<td align="center" class="header" width="15%" onClick="sortColumn(event)" type="CaseInsensitiveString" scope="col">Name</td>
				<td align="center" id="hdMS" class="header">Topic</td>
				<td align="center" class="header" onClick="sortColumn(event)" type="CaseInsensitiveString" scope="col">Status</td>
			</tr>
		</thead>
		<tbody>
			<tr ng-repeat="student in userIDArrayLive | orderBy:'-loggedInFlag' ">
				<td width="30px;">
					<div ng-class="{true:'greenCircle', false:'grayCircle'}[student.loggedInFlag==1]"></div>
				</td>
				<td align="left" width="25%">{{student.childName}}</td>
				<td align="left" ng-switch="student.textToShow">
					<span ng-switch-when="competitiveExam">Competitive Exam</span>
					<span ng-switch-when="improveConcepts">Improving concepts</span>
					<span ng-switch-when="ncertQuestions">Attempting ncert questions</span>
					<span ng-switch-when="activityAttempt">Attempting activity</span>
					<span ng-switch-when="revisionSession">Attempting revision session</span>
					<span ng-switch-when="topicPractice">Doing topic Practice</span>
					<span ng-switch-when="practiceModules">Doing Practice modules</span>
					<span ng-switch-when="presentButNotDoing">Others</span>
					<span ng-switch-when="teacherTopicDesc">{{student.teacherTopicDesc}}</span>
					<span ng-switch-default>- </span>
				</td>
				<td align="left" ng-if="student.loggedInFlag==1">{{student.statusFinal}}</td>
				<td align="left" ng-if="student.loggedInFlag==0">Currently not logged in</td>
			</tr>
		</tbody>
	</table>
	<br/><br/>
	<table cellspacing="0" cellpadding="3" border="0" style="font-family: verdana,arial,sans-serif;font-size: 11px;margin-left:40px;" ng-show="userIDArrayLive.length && !loadingNewData">
		<tr>
			<td width="30px;"><div class="greenCircle"></div></td>
			<td>Currently logged In</td>
		</tr>
		<tr>
			<td width="30px;"><div class="grayCircle"></div></td>
			<td>Currently not logged In </td>
		</tr>
		<tr>
			<td colspan="2">
				<br/>What each status means?<br/>
				<ul>
					<li>Gone to lower/higher level - Child going to lower level needs your  attention</li>
					<li>Low accuracy - Your attention required again as child is getting lot of questions wrong in the current session</li>
					<li>Present but not doing questions - This status should not be there for too long for a child.</li>
					<li>Currently not logged in - Students who have not logged in currently</li>
					<li>Doing fine - No immediate attention required</li>
					<li>Other - Child might be doing other important tasks like activity, revision .etc. So, this is nothing to worry about.</li>
				</ul>
			</td>
		</tr>
	</table>
</div>

<?php include("footer.php") ?>
<?php

function getStudentDetails1($cls, $schoolCode, $section,$status)
{
    $category  = $_SESSION['admin'];
	$userArray = array();

    $query = "SELECT ud.userID,ud.childName as childName
              FROM   adepts_userDetails ud
              WHERE  ud.category='STUDENT' AND ud.endDate>=curdate() AND ud.enabled=1  AND ud.schoolCode =$schoolCode AND ud.childClass='$cls' AND ud.subjects like '%".SUBJECTNO."%'";
    if(strcasecmp($category,"School Admin")==0 || strcasecmp($category,"TEACHER")==0)
		$query .= " AND subcategory='School'";
	if(strcasecmp($category,"Home Center Admin")==0)
		$query .= " AND subcategory='Home Center'";
    if($section!="")
		$query .= " AND childSection ='$section'";
	$query .= " order by ud.username";

	$dbFlag=0;
	$link1 = mysql_connect(SLAVE_HOST, REPL_USER, REPL_PWD);
	if($link1) {
		$res = mysql_query("SHOW SLAVE STATUS", $link1);
		$row = mysql_fetch_assoc($res);
		mysql_close($link1);
		if(is_null($row['Seconds_Behind_Master']))
		{
			$data = NULL;
			$dbFlag = 1;
		}
		else
			{
				$data = (int) $row['Seconds_Behind_Master'];
				if ($data>30) $dbFlag=1;
			}
	}
	else{
		$dbFlag=1;
	}
	if ($dbFlag==1)
		include('../userInterface/dbconf.php');
	else 
		include("../slave_connectivity.php");
	
	$r = mysql_query($query) or die($query."<br/>".mysql_error());

	while($l=mysql_fetch_array($r))
	{
		if($status==0){
			$uarray=array();
			$userID = $l[0];
			$uarray['userID']=$userID;
		    $uarray['childName'] = $l[1];
			$currentSessionString	=	currentSession($l[0]);
			$uarray['loggedInFlag'] = loggedInStatus($l[0],$currentSessionString);
			$teacherTopic = getTeacherTopic($l[0]);
			$uarray['teacherTopicDesc'] = $teacherTopic[0];
			
			$accuracy = getCurrentAccuracy($l[0],$cls,$teacherTopic[1],$currentSessionString);
			if($accuracy['attempts']>=20){
				$uarray['accuracyStudent'] = $accuracy['accuracy'];	
			}else{
				$uarray['accuracyStudent'] = 0;
			}
			if($uarray['loggedInFlag']==1){
				$idleUserArray = idleUser($l[0],$currentSessionString);
				$doingQuestions = doingQuestions($l[0],$currentSessionString,$cls);
				$competitiveExam = doingCompetetiveExam($l[0],$currentSessionString);
				$improveConcepts = doingImproveConcepts($l[0],$currentSessionString);
				$ncertQuestions = ncertQuestionAttempt($l[0],$currentSessionString);
				$activityAttempt = activityAttempt($l[0],$currentSessionString);
				$revisionSession = doingRevisionSession($l[0],$currentSessionString);
				$topicPractice = doingTopicPractice($l[0],$currentSessionString);
				$practiceModules = doingPracticeModules($l[0],$currentSessionString);
				$now = time();
				if($idleUserArray[0]+120<$now){
					$uarray['presentButNotDoing'] = 1;
				}else{
					$uarray['presentButNotDoing'] = 0;
				}
				if($competitiveExam>$now-120 && $doingQuestions<$competitiveExam){
					$uarray['competitiveExam'] = 1;
				}else{
					$uarray['competitiveExam'] = 0;
				}
				if($improveConcepts>$now-120 && $doingQuestions<$improveConcepts){
					$uarray['improveConcepts'] = 1;
				}else{
					$uarray['improveConcepts'] = 0;
				}
				if($ncertQuestions>$now-120 && $doingQuestions<$ncertQuestions){
					$uarray['ncertQuestions'] = 1;
				}else{
					$uarray['ncertQuestions'] = 0;
				}
				if($activityAttempt>$now-120 && $doingQuestions<$activityAttempt){
					$uarray['activityAttempt'] = 1;
				}else{
					$uarray['activityAttempt'] = 0;
				}
				if($revisionSession>$now-120 && $doingQuestions<$revisionSession){
					$uarray['revisionSession'] = 1;
				}else{
					$uarray['revisionSession'] = 0;
				}
				if($topicPractice>$now-120 && $doingQuestions<$topicPractice){
					$uarray['topicPractice'] = 1;
				}else{
					$uarray['topicPractice'] = 0;
				}
				if($practiceModules>$now-120 && $doingQuestions<$practiceModules){
					$uarray['practiceModules'] = 1;
				}else{
					$uarray['practiceModules'] = 0;
				}
			}else{
				$uarray['presentButNotDoing'] = 0;
				$uarray['ncertQuestions'] = 0;
				$uarray['improveConcepts'] = 0;
				$uarray['competitiveExam'] = 0;
				$uarray['activityAttempt'] = 0;
				$uarray['revisionSession'] = 0;
				$uarray['topicPractice'] = 0;
				$uarray['practiceModules'] = 0;
			}
			$cluster	=	$teacherTopic[2];
			$ttCode	=	$teacherTopic[1];
			$userLevel=0;
			$sqFlow	=	"SELECT flow FROM ".TBL_TOPIC_STATUS." WHERE userID=$userID AND teacherTopicCode='$ttCode' LIMIT 1";
			$rsFlow	=	mysql_query($sqFlow);
			$rwFlow	=	mysql_fetch_array($rsFlow);
			$flow	= str_replace(" ","_",$rwFlow[0]);
		
			$ttObj = new teacherTopic($ttCode,$cls,$flow);
			$levelArray = $ttObj->getClusterLevel($cluster);
			if($levelArray[0]!='')
			{
				if(!in_array($cls,$levelArray))
				{
					if(in_array($cls-2,$levelArray))
						$userLevel = 4;
					else if(in_array($cls-1,$levelArray))
						$userLevel = 3;
					else if(in_array($cls+1,$levelArray))
						$userLevel = 1;
					else if(in_array($cls+2,$levelArray))
						$userLevel = 2;
					else $userLevel = 0;
				}
			}
			$uarray['topicLevel'] = $userLevel;

			if ($uarray['presentButNotDoing'] ==1) $textToShow='presentButNotDoing';
			else if($uarray['ncertQuestions'] ==1) $textToShow='ncertQuestions';
			else if($uarray['improveConcepts'] ==1) $textToShow='improveConcepts';
			else if($uarray['competitiveExam'] ==1) $textToShow='competitiveExam';
			else if($uarray['activityAttempt'] ==1) $textToShow='activityAttempt';
			else if($uarray['revisionSession'] ==1) $textToShow='revisionSession';
			else if($uarray['topicPractice'] ==1) $textToShow='topicPractice';
			else if($uarray['practiceModules'] ==1) $textToShow='practiceModules';
			else $textToShow='teacherTopicDesc';

			$uarray['textToShow']=$uarray['loggedInFlag']==1?$textToShow:0;

			if($uarray['presentButNotDoing']==1)
				$statusFinal="Present but not doing questions";
				
			else if($uarray['improveConcepts']==1 || $uarray['ncertQuestions']==1 || $uarray['competitiveExam']==1 || $uarray['activityAttempt']==1 || $uarray['revisionSession']==1 || $uarray['topicPractice']==1 || $uarray['practiceModules']==1)
				$statusFinal="Other";
				
			else if(($uarray['topicLevel']==1 || $uarray['topicLevel']==2))
				$statusFinal = "Doing higher level";
				
			else if(($uarray['topicLevel']==3 || $uarray['topicLevel']==4))
				$statusFinal = "Doing lower level";
				
			else if($uarray['accuracyStudent']!=0 && $uarray['accuracyStudent']<40)
				$statusFinal="Low Accuracy";
				
			else if($uarray['improveConcepts']==0 && $uarray['ncertQuestions']==0 && $uarray['competitiveExam']==0 && $uarray['activityAttempt']==0 && $uarray['revisionSession']==0 && $uarray['topicPractice']==0  && $uarray['practiceModules']==0 && $uarray['presentButNotDoing']==0)
				$statusFinal="Doing Fine";
			
			else if($uarray['loggedInFlag']==0)
				$statusFinal="Currently not logged in";

			$uarray['statusFinal'] = $statusFinal;
		}
		/*else if($status==1){
			$userID = $l[0];
			$teacherTopic = getTeacherTopic($l[0]);
			$currentSessionString	=	currentSession($l[0]);
			$loggedInFlag = loggedInStatus($l[0],$currentSessionString);
			$cluster	=	$teacherTopic[2];
			$ttCode	=	$teacherTopic[1];
			$userLevel=0;
			$sqFlow	=	"SELECT flow FROM ".TBL_TOPIC_STATUS." WHERE userID=$userID AND teacherTopicCode='$ttCode' LIMIT 1";
			$rsFlow	=	mysql_query($sqFlow);
			$rwFlow	=	mysql_fetch_array($rsFlow);
			$flow	= str_replace(" ","_",$rwFlow[0]);
		
			$ttObj = new teacherTopic($ttCode,$cls,$flow);
			$levelArray = $ttObj->getClusterLevel($cluster);
			if($levelArray[0]!='' && $loggedInFlag==1)
			{
				if(!in_array($cls,$levelArray))
				{
					if(in_array($cls-2,$levelArray))
						$userLevel = 4;
					else if(in_array($cls-1,$levelArray))
						$userLevel = 3;
					else if(in_array($cls+1,$levelArray))
						$userLevel = 1;
					else if(in_array($cls+2,$levelArray))
						$userLevel = 2;
					else $userLevel = 0;
				}
			}
			if($userLevel!=0){
				$uarray['topicLevel'] = $userLevel;
				$uarray['teacherTopicDesc'] = $teacherTopic[0];	
		    	$uarray['childName'] = $l[1];
				$uarray['loggedInFlag'] = loggedInStatus($l[0],$currentSessionString);
			}
		}else if($status==2){
			$teacherTopic = getTeacherTopic($l[0]);
			$currentSessionString	=	currentSession($l[0]);
			$accuracy = getCurrentAccuracy($l[0],$cls,$teacherTopic[1],$currentSessionString);
			if($accuracy['accuracy']!=0 && $accuracy['accuracy']<40){
				if($accuracy['attempts']>=20){
					$uarray['accuracyStudent'] = $accuracy['accuracy'];	
				}else{
					$uarray['accuracyStudent'] = 0;
				}
				$uarray['teacherTopicDesc'] = $teacherTopic[0];
				$uarray['childName'] = $l[1];
				$uarray['loggedInFlag'] = loggedInStatus($l[0],$currentSessionString);
			}
		}else if($status==3){
			$currentSessionString	=	currentSession($l[0]);
			$loggedInFlag = loggedInStatus($l[0],$currentSessionString);
			$teacherTopic = getTeacherTopic($l[0]);
			if($loggedInFlag==1){
				$idleUserArray = idleUser($l[0],$currentSessionString);
				$now = time();
				if($idleUserArray[0]+240<$now){
					$presentButNotDoing = 1;
				}else{
					$presentButNotDoing = 0;
				}
			}else{
				$presentButNotDoing=0;
			}
			if($presentButNotDoing==1){
				$uarray['teacherTopicDesc'] = $teacherTopic[0];
				$uarray['childName'] = $l[1];
				$uarray['loggedInFlag'] = $loggedInFlag;
				$uarray['presentButNotDoing'] =1;
			}
		}else if($status==4){
			$currentSessionString	=	currentSession($l[0]);
			$loggedInFlag = loggedInStatus($l[0],$currentSessionString);
			$teacherTopic = getTeacherTopic($l[0]);
			if($loggedInFlag==0){
				$uarray['teacherTopicDesc'] = $teacherTopic[0];
				$uarray['childName'] = $l[1];
				$uarray['loggedInFlag'] = $loggedInFlag;
			}
		}else if($status==5){
			$currentSessionString	=	currentSession($l[0]);
			$loggedInFlag = loggedInStatus($l[0],$currentSessionString);
			if($loggedInFlag==1){
				$idleUserArray = idleUser($l[0],$currentSessionString);
				$now = time();
				if($idleUserArray[0]+120<$now){
					$presentButNotDoing = 1;
				}else{
					$presentButNotDoing = 0;
				}
			}else{
				$presentButNotDoing=0;
			}
			$userID = $l[0];
			$teacherTopic = getTeacherTopic($l[0]);
			$cluster	=	$teacherTopic[2];
			$ttCode	=	$teacherTopic[1];
			$userLevel=0;
			$sqFlow	=	"SELECT flow FROM ".TBL_TOPIC_STATUS." WHERE userID=$userID AND teacherTopicCode='$ttCode' LIMIT 1";
			$rsFlow	=	mysql_query($sqFlow);
			$rwFlow	=	mysql_fetch_array($rsFlow);
			$flow	= str_replace(" ","_",$rwFlow[0]);
		
			$ttObj = new teacherTopic($ttCode,$cls,$flow);
			$levelArray = $ttObj->getClusterLevel($cluster);
			$accuracy = getCurrentAccuracy($l[0],$cls,$teacherTopic[1],$currentSessionString);
			if($levelArray[0]!='')
			{
				if(!in_array($cls,$levelArray))
				{
					if(in_array($cls-2,$levelArray))
						$userLevel = 4;
					else if(in_array($cls-1,$levelArray))
						$userLevel = 3;
					else if(in_array($cls+1,$levelArray))
						$userLevel = 1;
					else if(in_array($cls+2,$levelArray))
						$userLevel = 2;
					else $userLevel = 0;
				}
			}
			if($loggedInFlag==1 && $userLevel!=3 && $userLevel!=4 && $accuracy['accuracy']>=40){
				$uarray['teacherTopicDesc'] = $teacherTopic[0];
				$uarray['childName'] = $l[1];
				$uarray['loggedInFlag'] = $loggedInFlag;
				$uarray['topicLevel'] = $userLevel;
				$uarray['presentButNotDoing'] =$presentButNotDoing;
				if($accuracy['attempts']>=20){
					$uarray['accuracyStudent'] = $accuracy['accuracy'];	
				}else{
					$uarray['accuracyStudent'] = 0;
				}
			}
		}*/
		array_push($userArray, $uarray);
	}
	return $userArray;
}
?>