<?php include("header.php");
$teacherClass = getMaxMappedteacherClass($_SESSION['userID']);
$userID=$_SESSION['userID'];
$user= new User($userID);
// $_SESSION['username']=$user->username;
$_SESSION['category']=$user->category;
// $_SESSION['schoolCode']=$user->schoolCode;
?>

<title>Other Features</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/otherFeatures.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="libs/css/jquery-ui.css" />

<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script>
	var langType = '<?=$language;?>';
	function load(){
		/*var fixedSideBarHeight = window.innerHeight;*/
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		/*$("#fixedSideBar").css("height",fixedSideBarHeight+"px");*/
		$("#sideBar").css("height",sideBarHeight+"px");
		/*$("#container").css("height",containerHeight+"px");*/
		$("#features").css("font-size","1.em");
		$("#features").css("margin-left","40px");
		$(".arrow-right-blue").css("margin-left","10px");
		$(".rectangle-right-blue").css("display","block");
		$(".arrow-right-blue").css("margin-top","3px");
		$(".rectangle-right-blue").css("margin-top","3px");
	}
	
	function goToExamCorner()
	{
		/*document.getElementById('childClass').value = $('#teacherClass').val();*/
		$("#mode").val("examCorner");

		$("#mindsparkTeacherLogin").attr("action", "../userInterface/commonAjax.php");
		setTryingToUnload();
		$('#mindsparkTeacherLogin').submit();
	}
</script>

</head>
<body class="translation" onLoad="load()" onResize="load()">
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
		<div id="innerContainer">
			
			<div id="containerHead">
				
				<span>Click on any of the following links to access respective screen.</span>
			</div>
			<div id="containerBody">
				<table class="gridTable">
				<?php if (strcasecmp($user->category,"Teacher")!=0) { ?>
				<tr>
				<td style="padding-bottom:2px; ">ADMIN REPORTS</td>
				</tr>
				
				
				<tr class="coloredRow">
				<td><b><a href="teacherDetails.php">Teacher Mapping </a></b> </td>
				</tr>
				<tr>
				
				</tr>
				
				
				<tr class="coloredRow">
				<td><b><a href="teacherLoginDetails.php">Teacher Usage Details </a></b> </td>
				</tr>
				<tr class="coloredRow">
				<td><b><a href="dailyUsageReport.php">Daily Usage Report </a></b> </td>
				</tr>
				<!-- <tr class="coloredRow">
				<td><b><a href="ChangeCurriculum.php">Change Curriculum / Topic Activation at Home</a></b> </td>
				</tr> -->
				<?php } ?>
				<tr>
				
				<tr style="height:15px;"></tr>
				
				<td style="padding-bottom:2px; "> REPORTS</td>
				</tr>
				
				<tr class="coloredRow">
				<td><b><a href="cwa.php">Common Wrong Answers </a></b> </td>
				</tr>
				
				<tr class="coloredRow">
				<td><b><a href="timedTestReport.php">Timed Test Report </a></b> </td>
				</tr>
				
				<tr>
				
				</tr>
				<tr class="coloredRow">
				<td><b><a href="revisionSession.php">Revision Session </a></b> </td>
				</tr>
				
				<tr>
				
				</tr>
				<tr class="coloredRow">
				<?php 
					$userid	=	$_SESSION['userID'];
				    $query  = "SELECT category FROM adepts_userDetails WHERE userID=".$userid;
				    $result = mysql_query($query) or die(mysql_error());
				    $line   = mysql_fetch_array($result);
				    $category   = $line[0];

				    $selectQueryuserInterfaceSettingsForMPI = "SELECT settingValue from userInterfaceSettings where schoolCode='".$_SESSION['schoolCode']."' and settingName='mpi'";
					$statusMPI =  mysql_query($selectQueryuserInterfaceSettingsForMPI) or die(mysql_error());
					$row= mysql_fetch_assoc($statusMPI);

					if($row['settingValue'] == "Off"){

						if(strcasecmp($category,"School Admin")==0){
					        echo '<td><b>Mindspark Performance Report - Disabled (Visit <a href="settings.php">My Settings</a> to change settings.) </b> </td>';
					    }else if(strcasecmp($category,"Teacher")==0){
					    	echo '<td><b>Mindspark Performance Report - Disabled (Please contact your school admin to change the settings.)</b> </td>';
					    }
						
					}else{
						echo '<td><b><a href="studentReportPTA.php">Mindspark Performance Report </a></b> </td>';
					}
			 
				   
					

					
				?>
				<!-- <td><b><a href="studentReportPTA.php">Mindspark Performance Report </a></b> </td> -->
				</tr>
				
				<tr>
				
				</tr>
				<tr class="coloredRow">
				<td><b><a href="topicWisePracticeReport.php">Topicwise Practice </a></b> </td>
				</tr>
				
				<tr>
				
				</tr>
				
				<?php if (strcasecmp($user->category,"Teacher")!=0) { ?>
				<tr class="coloredRow">
				<td><b><a href="topicActivationSummary.php">Topic Activation Summary </a></b> </td>
				</tr>
				
				<tr>
				
				</tr>
				<?php } ?>
				<tr class="coloredRow">
				<td><b><a href="studentTrail.php">Student Trail</a></b> </td>
				</tr>
				
				<tr>
				
				</tr>
				
			<!--	<tr>
				
				<tr style="height:15px;"></tr>
				
				<td style="padding-bottom:2px; "> COMMON </td>
				</tr>
				
				<tr class="coloredRow">
				<td><b><a href="cwa.php">Common Wrong Answers </a></b> </td>
				</tr>-->	
				
				<tr>
				
				<tr style="height:15px;"></tr>
				
				<td style="padding-bottom:2px; "> SEARCH </td>
				</tr>
				
				<tr class="coloredRow">
				<td><b><a href="searchQuestions.php">Search Questions </a></b> </td>
				</tr>
				
				<tr>
				
				<tr style="height:15px;"></tr>
				
				<td style="padding-bottom:2px; "> CONTRIBUTE </td>
				</tr>
				<?php 
				//$schoolCodeForWorksheetArray = array(3150387,3147476,2387554,19282,370457,170622,651378,18922);
				//if(in_array($_SESSION['schoolCode'], $schoolCodeForWorksheetArray)) { 
				if(($user->username!='ei.admin')) { ?>
				<tr class="coloredRow">
				<td><b><a onClick="redirectWorksheet();" href="javascript:void(0);" id="teacherWorksheet">Teacher Worksheets </a></b> </td>
				</tr>
				<?php } 
				?>
				<tr class="coloredRow">
				<td><b><a href="addQuestionTopic.php">Add a Question </a></b> </td>
				</tr>
				<tr class="coloredRow">
				<td><b><a href="addQuestionTopic.php?tab=2">View Added Questions </a></b> </td>
			    </tr>


				<?php if($teacherClass>=8) {?>
				<tr>
				
				<tr style="height:15px;"></tr>
				
				<td style="padding-bottom:2px; "> EXAM CORNER </td>
				</tr>
				
				<tr class="coloredRow" onClick="goToExamCorner();" style="cursor:pointer">
				<td><u><b>View Exam Corner</b></u></td>
				</tr>
				
				<form name="mindsparkTeacherLogin" id="mindsparkTeacherLogin" action="" method="post">
				
				<input type="hidden" name="mode" id="mode" value="">
			    <input type="hidden" name="childClass" id="childClass" value="<?=$teacherClass?>">
			    <input type="hidden" name="userType" id="userType" value="teacherAsStudent">
			   
			</form>
				<?php } ?>
				<tr>
				
				<tr style="height:15px;"></tr>
				
				<td style="padding-bottom:2px; "> COMMUNICATION </td>
				</tr>
				
				<tr class="coloredRow">
				<td><b><a href="studentTrail.php?mode=errorReporting&last1hour=1"> Report Errors to Mindspark </a></b> </td>
				</tr>
				
				<tr class="coloredRow">
				<td><b><a href="teacherCommentReport.php"> Errors Reported to Mindspark </a></b> </td>
				</tr>
				<tr class="coloredRow">
                                    <td><b><a href="parentEmail.php">Mail parents</a></b> </td>
				</tr>
				<tr class="coloredRow">
                                    <td><b><a href="studentNoticeBoard.php">Student Notice Board</a></b> </td>
				</tr>
				
				<tr>
				
				<!-- <tr style="height:15px;"></tr>
				
				<td style="padding-bottom:2px; "> CONTRIBUTE A QUESTION</td>
				</tr>
				
				<tr class="coloredRow">
				<td><b><a href="addQuestionTopic.php">Add a question </a></b> </td>
				</tr>
				
				<tr>
				
				</tr>
				<tr class="coloredRow">
				<td><b><a href="addQuestionTopic.php?tab=2">View added questions </a></b></td>
				</tr>  -->
				
				
				<?php if (strcasecmp($user->category,"Teacher")!=0) { ?>
				<tr>
				
				</tr>
				
				<tr>
				
			
				<?php } ?>
				
				<tr>
				
				<tr style="height:15px;"></tr>
				
				<td style="padding-bottom:2px; ">NCERT EXERCISES (Available for classes 6 and above)</td>
				</tr>
				
				<!--<tr class="coloredRow">
				<td><font color="#CC0000"><b>*NCERT EXERCISES are temporarily deactivated.</a></b></td>
				</tr>-->
				
				<tr class="coloredRow">
				<td><b><a href="activateHomework.php">NCERT Homework Activation </a></b> </td>
				</tr>
				
				<tr class="coloredRow">
				<td><b><a href="ncertExerciseReport.php">NCERT Class Report </a></b> </td>
				</tr>
				
				<tr class="coloredRow">
				<td><b><a href="ncertCWA.php">NCERT Common Wrong Answer </a></b> </td>
				</tr>
				
				<tr class="coloredRow">
				<td><b><a href="studentTrail.php?trailType=ncert">NCERT Student Trail </a></b> </td>
				</tr>
				
				<tr style="height:15px;"></tr>
				<tr>
					<td style="padding-bottom:2px; "> OTHERS</td>
				</tr>

				<?php if(($user->category=="School Admin" && $user->subcategory=="All")) { ?>
				<tr class="coloredRow">
					<td><b><a href="getSchoolDetails.php">Change School </a></b> </td>
				</tr>
				<?php } ?>
				<tr class="coloredRow">
				<td><b><a href="editStudentDetails.php">Edit Student Details</a></b> </td>
				</tr>				
				<tr>
				
				</tr>
				
				
				</table>
			</div>
		</div>
		
		<div id="innerContent">
		
		</div>
		
	</div>

<?php include("footer.php") ?>

<?php

function getMaxMappedteacherClass($userID)
{
	$class=0;
	$sql = "Select class from adepts_teacherClassMapping where userID = $userID";
	$result = mysql_query($sql);
	while($line = mysql_fetch_array($result))
	{
	if($line[0] > $class)
	$class = $line[0]; 
	}
	return $class;
}
?>
<script>
function redirectWorksheet(){
	var html = '';
       html +='<form name="frmBrowser" id="frmBrowser" method="POST" action="/mindspark/app/worksheet/api/dashboard" target="_blank">';
       html +='<input type="hidden" name="sessionClass" id="sessionClass" value="">';
       html +='</form>';
       $("#teacherWorksheet").append(html);
       document.getElementById("frmBrowser").submit();       	
}
</script>