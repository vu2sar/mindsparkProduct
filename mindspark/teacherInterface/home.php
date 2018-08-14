<?php
error_reporting ( E_ALL );
ini_set ( "display_errors", 1 );
include ("header.php");
set_time_limit ( 0 );
include ("functions/functions.php");
include ("classes/testTeacherIDs.php");
include_once ("../userInterface/classes/clsTopicProgress.php");
// include("../slave_connectivity.php");
require_once 'common-code.php';
// require_once 'functions/dashboardFunctions.php';
$endDate = date ( "Y-m-d" ,strtotime("-1 day") );
$startDate=date ( "Y-m-d" ,strtotime("-7 day"));
		
function getAnnouncements1($currentDate, $schoolCode, $userID, $userCategory) {
	$userName = $_SESSION ['username'];

	$currentKudosCount = newKudosCounter ( $userName );
	if ($currentKudosCount > 0) {
		$kudosNotification = 1;
	}

	$announcementsArray = array ();

	/*// Added for temporary announcement of parent email notification feature
	if (! ($_SESSION ['isOffline'] === true && ($_SESSION ['offlineStatus'] == 1 || $_SESSION ['offlineStatus'] == 2))) {
		$checkValidSchool = "SELECT schoolCode FROM parentEmailSchools WHERE schoolCode = " . mysql_real_escape_string ( $schoolCode );
		$execValidSchool = mysql_query ( $checkValidSchool );
		if (mysql_num_rows ( $execValidSchool ) == 0) {
			$notification_to_show = '<span><b>New Parent Connect Feature</b></span><br />';
			$notification_to_show .= '<span>Teachers can now directly mail parents if parent email ids are mapped to the student\'s account.</span><br />';
			$notification_to_show .= '<span><a href="parentEmailAnnouncement.php">Click</a> to know more or disallow this.</span><br/>';
			$announcementsArray [0] = array (
					"contentId" => "",
					"link" => "",
					"title" => $notification_to_show
			);
		}
	}*/
	$notification_to_show = "";
	if ($kudosNotification == 1) {
		$notification_to_show .= '<span onClick="resetKudosCounter();">You have received ' . $currentKudosCount . ' new Kudos!</span>';
	}
	// 	else {
	// 		$notification_to_show .= '<table><tr><td><a href="kudosHomeTeacherInterface.php"><img  style="border-style:none !important; border:none;"  src="../userInterface/assets/kudosNew.png" width=43px height=43px /></a></td><td><h2>Send your students a kudos!</h2></td></tr></table>';
	// 	}
	$announcementsArray [1] = array (
			"contentId" => "",
			"link" => "",
			"title" => $notification_to_show
	);

	$sq = "SELECT id,contentId,title,description,category,schoolCode,class,status FROM adepts_teacherAnnouncements WHERE status='Approved' AND '$currentDate' BETWEEN fromDate AND tillDate";
	$rs = mysql_query ( $sq );
	$aqadCount = 0;
	while ( $rw = mysql_fetch_assoc ( $rs ) ) {
		$schoolcodeAnnounce = $rw ['schoolCode'];
		$id = $rw ['id'];
		$class = $rw ['class'];

		// check class belong to teacher or not
		if ($class == 'All' || $userCategory == 'Admin')
			$classValidity = 'valid';
		else
			$classValidity = checkTeacherClass ( $class, $userID );
			
		// check for schoolcode
		if ($schoolcodeAnnounce == 'All')
			$schoolcodeValidity = 'valid';
		else
			$schoolcodeValidity = checkTeacherSchoolCode ( $schoolcodeAnnounce, $schoolCode );

		if ($classValidity == '' || $schoolcodeValidity == '')
			continue;

		if ($rw ['category'] != 'Other' && $rw ['category'] != 'AQAD') {
			if ($rw ['category'] == 'Topic') {
				$announcementsArray [$id] ['link'] = "announcements.php?id=" . $rw ['id'];
			}

			else if ($rw ['category'] == 'Remedial') {
				$announcementsArray [$id] ['link'] = "../userInterface/remedialItem.php?qcode=" . $rw ['contentId'];
			}

			else if ($rw ['category'] == 'Cluster') {
				$ttCode = getTTCode ( $rw ['contentId'] );
				$announcementsArray [$id] ['link'] = "sampleQuestions.php?ttCode=$ttCode&learningunit=" . $rw ['contentId'];
			}

			else if ($rw ['category'] == 'Timed test') {
				$announcementsArray [$id] ['link'] = "../userInterface/timedTest.php?timedTest=" . $rw ['contentId'] . "&tmpMode=sample";
			}

			else if ($rw ['category'] == 'Games') {
				$announcementsArray [$id] ['link'] = "../userInterface/enrichmentModule.php?gameID=" . $rw ['contentId'];
			}
				
			$announcementsArray [$id] ['title'] = $rw ['title'];
			$announcementsArray [$id] ['contentId'] = $rw ['contentId'];
			// display title on the page
		} 
		else if ($rw ['category'] == 'Other' and $rw ['status'] == 'Approved') {
			$announcementsArray [$id] ['title'] = $rw ['title'];
			$announcementsArray [$id] ['class'] = $rw ['class'];
			if ($rw ['id'] == 43)
				$announcementsArray [$id] ['link'] = "feedbackform_content.php";
			else if (!empty($rw ['description']))
				$announcementsArray [$id] ['link'] = "announcements.php?id=" . $rw ['id'];
		}
	}
	return $announcementsArray;
}

if($offlineMode !== true) //dont show in offline mode
	$teacherPosterPrompt = checkForTeacherPosterPrompt($_SESSION ['username'], $_SESSION ['schoolCode']);
else 
	$teacherPosterPrompt = 0;

?>
<title>Landing Screen</title>
<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet"	type="text/css">
<link href="css/common.css?ver=17" rel="stylesheet" type="text/css">
<link href="css/landingScreen.css" rel="stylesheet" type="text/css">
<link href="css/popupwindow.css" rel="stylesheet" type="text/css">
<link href="css/dashboard.css" rel="stylesheet" type="text/css">
<!-- <script src="libs/jquery.js"></script> -->
<!-- <script type="text/javascript" src="libs/jquery-ui.js"></script> -->
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/popupwindow.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript"src="../userInterface/libs/closeDetection.js"></script>
<script type="text/javascript" src="libs/carousels.js"></script>
<script type="text/javascript">
var langType = '<?=$language;?>';
$(document).ready(function(){
	var sideBarHeight = window.innerHeight-95;
	$("#sideBar").css("height",sideBarHeight+"px");
	<?php
	if ($teacherPosterPrompt == "1" || $teacherPosterPrompt == 1) {
		?>         
	            $('#iLoginPrompt').popUpWindow({
	                action: "open", // open or close
	                modal: true, // modal mode
	                size: "medium" // large, medium or large
	            });
	<?php
	}
	?>
	<?php if(SERVER_TYPE=="LIVE") { ?>
		$.ajax('getHomeDetails.php',
		{
			type: 'post',
			data: "mode=alerts",
		  success: function(msg){
			if(msg == ""){
				 $('#alerts').hide();
			}else{
				$('#alerts').html(msg);
				$('#alerts #overallUsageChart_loading').hide();
			}
		  },
		  error: function(msg){  
			$('#alerts').html('Unable to load data right now.');
			$('#alerts #overallUsageChart_loading').hide();
		  }
		});
	<?php } else { ?>
		$('#alerts').hide();
	<?php } ?>	
		
	/*$.ajax('getHomeDetails.php', 
	{  
		type: "post",
		data : "mode=mindsparkinnumbers",
		success: function(msg){
			$('#testimonial-list').html(msg);
			$('#testimonials #overallUsageChart_loading').hide();
		},
		error: function(msg){
			$('#testimonial-list').html('Unable to load data right now.');
			$('#testimonials #overallUsageChart_loading').hide();
		}  
	});*/
				
	<?php 	
	if($user->category == "School Admin"){ ?>	
		$.ajax('getHomeDetails.php', 
		{  
			type: "post",
			data : "mode=class-data",
			success: function(msg){
				$('#class-details .strength-container').html(msg);
				$('#class-details .strength-container').show();
				$('#class-details #overallUsageChart_loading').hide();
			},
			error: function(msg){				
				$('#class-details .strength-container').html('Unable to load data right now.');
				$('#class-details .strength-container').show();
				$('#class-details #overallUsageChart_loading').hide();
			}
		});
	<?php  } else { ?>	
		$.ajax('getHomeDetails.php', 
		{  
			type: "POST",  
			data : "mode=strength-weakness",
			success: function(msg){
				$('#strength-weekness .strength-container').html(msg);
				$('#strength-weekness .strength-container').show();
				$('#strength-weekness #overallUsageChart_loading').hide();
			},
			error: function(msg){				
			    $('#strength-weekness .strength-container').html('Unable to load data right now.');
				$('#strength-weekness .strength-container').show();
				$('#strength-weekness #overallUsageChart_loading').hide();
			}

		});
	<?php  } ?>
});
</script>
<script type="text/javascript">
function resetKudosCounter()
{
	username="<?= $_SESSION['username'] ?>";
	$.ajax({
	type:'POST',
	url: "../userInterface/kudosAjax.php",
	data: {resetKudos: "YES",userName: username},
	success: function(){console.log("In RESET");} // or function=date if you want date
	});
}
function trackTeacherForum()
{
	$.ajax({
			  url: 'ajaxRequest.php',
			  type: 'post',
			  data: {'mode': 'teacherForum'},
			  success: function(response) {  }
          });				  
}
</script>
<body class="new-homepage translation">
	<?php include("eiColors.php")?>
	<div id="fixedSideBar">
		<?php include("fixedSideBar.php");?>
	</div>
	<div id="topBar">
		<?php include("topBar.php"); ?>
	</div>
	<div id="sideBar" class="new-homepage-sidebar">
		<?php include("sideBar.php");?>
	</div>
	<div id="container">
	<?php 
		for($iindex = 0; $iindex < count($classArray); $iindex++){
			$classSectionKeyValue [$classArray[$iindex]] = $sectionArray[$iindex];  
		}		
	?>

		<div class="header-section">
		<label class="dashboard-label">Dashboard - Mindspark in your classes from <?= date("j<\s\u\p>S</\s\u\p> F",strtotime("-7 day"));?> to <?= date("j<\s\u\p>S</\s\u\p> F",strtotime("-1 day"));?></label>
		<?php
		$classButtons = "";
		$classButtonCount = 1;
		foreach($classSectionKeyValue as $classSectionKey => $classSectionValue){			
				$subClassesArray = explode(",",$classSectionValue);
				foreach($subClassesArray as $subClasse){
						$classButtons .="<button type='button' class='btn btn-default dropdown-toggle btn-success' onclick='generateReport($schoolCode,$classSectionKey,\"$subClasse\")'>".$classSectionKey.$subClasse."</button>";
						$classButtonCount++;
				}
			}			
			if($classButtonCount > 10){
				?>
					<button type="button" class="btn btn-default dropdown-toggle btn-success btn-green-hover" style="float: right;margin-right: 60px; width: auto;" onClick="generateReport(0)">Class Reports</button>
				<?php 
			}
			else{
				?>
				<label class="header-lebal">Class Reports: </label>
				<div class="btn-group">
					<?= $classButtons; ?>
				</div>
				<?php 
			}
		?>
			
		</div>
		<?php
			if($user->category == "School Admin" ){
				?>
					<div class="strength-weekness" id="class-details">
						<div id="overallUsageChart_loading" class="dashboard-loading">
							<p>This may take a few minutes</p> 
						</div>
						<div class="strength-container">
						</div>
					</div>
				<?php 
				
			}else{
				?>
					<div class="strength-weekness" id="strength-weekness">
						<div id="overallUsageChart_loading" class="dashboard-loading">
							<p>This may take a few minutes</p> 
						</div>
						<div class="strength-container">
						</div>
					</div>
				<?php 
			}
		?>
		<?php 
			if($user->category == "School Admin"){
				?>
					<div class="alerts" id="teacherUsageDetails">
						<?php
			        $timeSpent = 0;
              $teachersLoggedInArray = array();
              $notLoggedInTeachersCount = 0; 

              $arrTeacherIDs = array();
			  $tmpTeacherIDs = "'".implode("','",$testIDArray)."'";
              $query = "SELECT userID FROM adepts_userDetails WHERE schoolCode = $schoolCode AND category IN ('TEACHER','School Admin') and enabled=1 and endDate>=curdate() AND username NOT in ($tmpTeacherIDs) ";
              $result_tid = mysql_query($query) or die(mysql_error());
              while($line_tid = mysql_fetch_array($result_tid))
                  array_push($arrTeacherIDs,$line_tid['userID']);
                  
              $teacherUsageSQL = "select userID,sessionID,startTime,endTime from adepts_sessionStatus WHERE userID in (". implode(",",$arrTeacherIDs) .") AND startTime_int >= ".str_replace("-","",$startDate)." AND startTime_int<=".str_replace("-","",$endDate);
              $teacherUsageQuery = mysql_query($teacherUsageSQL);
              while($teacherusageResult = mysql_fetch_assoc($teacherUsageQuery)){
              	
              	$endTsime = $teacherusageResult['endTime'] != ""?convertToTimeCommon($teacherusageResult['endTime']):"";
              	$startsTime = convertToTimeCommon($teacherusageResult['startTime']);
              	$timeSpent = $endTsime !="" ?$timeSpent + ($endTsime - $startsTime):"";    //in secs        	        
                  
                      if(!in_array($teacherusageResult['userID'], $teachersLoggedInArray)){
              		array_push($teachersLoggedInArray, $teacherusageResult['userID']);
              	}
              }
              $notLoggedInTeachersArray = array_diff($arrTeacherIDs, $teachersLoggedInArray);
              $notLoggedInTeachersCount = count($notLoggedInTeachersArray);
              if($notLoggedInTeachersCount != 0){
              	if($notLoggedInTeachersCount == 1){
              		$t = "teacher";
              	}else{
              		$t = "teachers";
              	}
              	echo "<span><span style='color:red;'>$notLoggedInTeachersCount </span>$t  did not log into Mindspark in the past week. Please encourage them to log in more often.</span></span><br />";                       
              	
              }
              $timeSpent = $timeSpent/count($arrTeacherIDs);
              $hours = intval($timeSpent/3600);    //converting secs to hours.
              $timeSpent = $timeSpent%3600;
              $mins  = str_pad(intval($timeSpent/60),2,"0", STR_PAD_LEFT);
              $timeSpent = $timeSpent%60;
              $secs  = str_pad($timeSpent,2,"0",STR_PAD_LEFT);
               
              if($hours == "00"){
              	$timeToShowForTeacher = "<span style='color:red;'>".$mins . " minute(s) </span>";
              }
              else{
              	$hoursString = $hours == "1"?" hour ":" hours ";
              	$hoursString .= $mins == "00" ? "":$mins." minute(s)";
              	$timeToShowForTeacher = "<span style='color:green;'>".$hours . $hoursString."</span>";
              }
              echo "<span>On an average, teachers spent $timeToShowForTeacher on Mindspark in the past week. We recommend that they spend at least <span style='color:green'>1 hour</span> on Mindspark in a week.</span><br />";
              echo "<span>See more details <a href='teacherLoginDetails.php' target='_blank'>here.</a></span>";
//               else{
//               	echo '<span>Great! There are no alerts for you. </span>';
//               }
						?>
					</div>
				<?php 
			}
		?>
		
		
		<div class="announcments">
		<?php
		if (strcasecmp ( $user->category, "School Admin" ) == 0) {
			$query = "SELECT COUNT(*) FROM adepts_userDetails a, adepts_forgetPassNotification b 
					   WHERE a.userID=b.childUserID AND b.status=0 AND schoolCode=$schoolCode AND a.category='STUDENT' AND subcategory='School' 
					   AND enabled=1 AND endDate>=curdate() AND subjects LIKE '%" . SUBJECTNO . "%'";
		} else {
			$query = "SELECT COUNT(*) FROM adepts_userDetails a, adepts_forgetPassNotification b 
					   WHERE a.userID=b.childUserID AND b.status=0 AND b.teacherUserID=$userID AND schoolCode=$schoolCode AND a.category='STUDENT' AND subcategory='School' 
					   AND enabled=1 AND endDate>=curdate() AND subjects LIKE '%" . SUBJECTNO . "%'";
		}
		$result = mysql_query ( $query ) or die ( mysql_error () );
		$row = mysql_fetch_array ( $result );
		if ($row [0] > 0) {
			if ($row [0] == 1)
				echo "<ul><li style='color:#f00;'><span><a href='updateStudentPassword.php'>" . $row [0] . " request is pending for password reset / account unlock.</a></span></li></ul>";
			else
				echo "<ul><li style='color:#f00;'><span><a href='updateStudentPassword.php'>" . $row [0] . " requests are pending for password reset / account unlock.</a></span></li></ul>";
		}
		?>
		<?php
		$currentDate = date ( "Y-m-d" );
		//code for aqad start
		if($offlineMode !== true) //dont show in offline mode
		{
			$clssecarr = implode("','",$classSectionArr);
			
			$andQ = " AND (";
			$andbadge = " AND (";
			$andaqad = " AND (";
			for($ci =0; $ci < count($classArray); $ci++){
				if($ci > 0){
					$andQ .= " OR ";
					$andbadge .= " OR ";
					$andaqad .= " OR ";
				}
				$secIn = str_replace(",", "','", $sectionArray[$ci]);
				$andQ .= "(a.studentClass = '$classArray[$ci]'  AND b.childSection IN ('$secIn'))";
				$andbadge .= "(a.childClass = '$classArray[$ci]'  AND a.childSection IN ('$secIn'))";
				$andaqad .= "(b.childClass = '$classArray[$ci]'  AND b.childSection IN ('$secIn'))";
			}
			$andQ .= ")";
			$andbadge .= ")";
			$andaqad .= ")";
			
			$aqadQuery ="select b.childName,b.childClass,b.childSection from educatio_educat.aqadExplaination a inner join adepts_userDetails b on a.studentId=b.userID where a.date between '$startDate' and  '$endDate' and b.enddate>curdate() and b.enabled=1 and b.schoolCode= '$schoolCode' $andaqad and IsExplainationOfDay = 1";
			// 		$aqadQuery = "SELECT title FROM adepts_teacherAnnouncements WHERE status='Approved' AND category = 'AQAD' AND '2014-11-14' BETWEEN fromDate AND tillDate AND schoolCode='$schoolCode' AND class IN ('$clssecarr')";
			$aqadSQL 	=	mysql_query($aqadQuery);
			$aqadrow	=	mysql_num_rows($aqadSQL);
			if($aqadrow!=0){
				while($aqadResult = mysql_fetch_assoc($aqadSQL)){
					$aqadArray []= $aqadResult['childName']." of ".$aqadResult['childClass'].$aqadResult['childSection'];
				}
				if(count($aqadArray) >1){
					$printAQAD .= "<span>Congrats! ".implode(', ',$aqadArray)." was awarded AQAD explanation of the day.</span>";
				}
				else{
					$printAQAD = "<span>Congrats! ".$aqadArray[0]." was awarded AQAD explanation of the day.</span>";
				}
			}
		}
		
		// code for aqad ends
		
		$announcementsArray = getAnnouncements1 ( $currentDate, $schoolCode, $userID, $userCategory );
		array_push($announcementsArray, array("title" => $printAQAD));
		
		
		$scQuery = "SELECT a.studentName, a.studentClass, b.childSection FROM adepts_loginPageDetails a inner join adepts_userDetails b  on a.userID = b.userID WHERE ((a.fromDate BETWEEN '$startDate' AND '$endDate') OR (a.tillDate BETWEEN '$startDate' AND '$endDate')) AND b.schoolCode= '$schoolCode' $andQ ";
		$scSQL 	=	mysql_query($scQuery);
		$scrow	=	mysql_num_rows($scSQL);
		if($scrow!=0){
			while($scResult = mysql_fetch_assoc($scSQL)){
				array_push($announcementsArray, array("title" => "Congratulations! ".$scResult['studentName']." of ". $scResult['studentClass'].$scResult['childSection']." was the SparkieChamp."));
			}
		}
		
		$userBagdesSQL = "SELECT a.childName,a.childClass,a.childSection from adepts_userBadges b inner join adepts_userDetails a on b.userID=a.userID where b.batchDate BETWEEN '$startDate' AND '$endDate' AND a.schoolCode= '$schoolCode' $andbadge and b.batchType='milestone3'";
		
		$badgeQuery 	=	mysql_query($userBagdesSQL);
		$badgeRow	=	mysql_num_rows($badgeQuery);
		if($badgeRow!=0){
			while($badgeResult = mysql_fetch_assoc($badgeQuery)){
				array_push($announcementsArray, array("title" => "Congrats! ".$badgeResult['childName']." of ". $badgeResult['childClass'].$badgeResult['childSection']." earned 1500 sparkies."));
			}
		}
		if (count ( $announcementsArray ) <= 3) {
			foreach ( $announcementsArray as $id => $details ) {
				if($details['title'] != ''){
					if(isset($details['link']) && !empty($details['link'])) { ?>
						<span><a href='<?=$details['link']?>' class='anouncementLink'><?=$details['title']?></a></span> 
						<?php
					} else {
						echo "<span>".$details ['title']."</span>";
					}
				}
			}
		} else {
			?>
			<marquee scrollamount=3 direction="up" height="160" style="width: 85%;" onMouseOver="this.setAttribute('scrollamount', 0, 0);this.stop();" OnMouseOut="this.setAttribute('scrollamount', 3, 0);this.start();">
					<ol>
			<?php
			foreach ( $announcementsArray as $id => $details ) {
				if($details['title'] != ''){
				?>
						<li>
				<?php if(isset($details['link']) && !empty($details['link'])) { ?>
				<span><a href='<?=$details['link']?>' class='anouncementLink'><?=$details['title']?></a></span>
				<?php
				} else {
					echo '<span>'.$details ['title'].'<span>';
				}
				?>
				</li>
					
			<?php } 
		}
		}
		 ?>
		 </ol>
			</marquee>
		</div>
		<div class="mindspark-in-numbers" style="display:none;">
			 <div id="content">
		      <div id="testimonials">
		        <div class="carousel-wrap">
		        <div id="overallUsageChart_loading" class="dashboard-loading">
						<p>This may take a few minutes</p> 
					</div>
		          <ul id="testimonial-list" class="clearfix">
		            
		          </ul><!-- @end #testimonial-list -->
		        </div><!-- @end .carousel-wrap -->
		      </div><!-- @end #testimonials -->
	    </div>
		</div>
		<div class="alerts" id="alerts">
			<div id="overallUsageChart_loading" class="dashboard-loading">
				<p>This may take a few minutes</p> 
			</div>
		</div>
	</div>
	<?php if($teacherPosterPrompt > 0) { ?>      
	<!-- added for login prompt --> 
		<div style="display: none">
			<div id="iLoginPrompt" class="pop-up-display-content">
				<div id="login_prompt">
					<center>
						<?php
						if ($posterHeader!="")
							echo '<h2>'.$posterHeader.'</h2>';
						?>
						<div class="img-class" style='text-decoration: none'>
							<?php if ($posterLink!="")
								echo '<a href="'.$posterLink.'" target="_blank"><img style="border: 0px;"	src="'.$posterImage.'"/></a>';
							else 
								echo '<img style="border: 0px;"	src="'.$posterImage.'"/>';
							?>
						</div> 
						<?php
						if ($posterFooter!="")
							echo '<div>'.$posterFooter.'</div>';
						?>
						<br><br>
						<a	href="javascript:void(0);" onClick="$('#iLoginPrompt').popUpWindow({action: 'close'});"  style='text-decoration: none'><span class="forumbutton">OK</span></a>
					</center>
				</div>
			</div>
		</div>
	<?php }  ?> 
<?php 
function getTTsActivated1($cls, $schoolCode, $section, $limit = 0, $activationPeriod = 0) {
	$ttAttemptedArray = array ();
	$query = "SELECT A.teacherTopicCode,teacherTopicDesc FROM adepts_teacherTopicActivation A , adepts_teacherTopicMaster B
	WHERE A.schoolcode='$schoolCode' AND A.class='$cls' AND A.section='$section' AND A.teacherTopicCode=B.teacherTopicCode AND ISNULL(deactivationDate)";
	if ($activationPeriod != 0) {
		$lastDate = date ( 'Y-m-d', strtotime ( "-20 days" ) );
		$query .= " AND A.activationDate<'$lastDate'";
	}
	$query .= " ORDER by A.activationDate desc";
	if ($limit != 0)
		$query .= " LIMIT $limit";
	$result = mysql_query ( $query ) or die ( mysql_error () );
	while ( $line = mysql_fetch_array ( $result ) ) {
		$ttAttemptedArray [$line [0]] = $line [1];
	}
	return $ttAttemptedArray;
}
function getStudentDetails1($cls, $schoolCode, $section) {
	$category = $_SESSION ['admin'];
	$userArray = array ();
	$query = "SELECT userID
	FROM   adepts_userDetails
	WHERE  category='STUDENT' AND endDate>=curdate() AND enabled=1  AND schoolCode =$schoolCode AND childClass='$cls' AND subjects like '%" . SUBJECTNO . "%'";
	if (strcasecmp ( $category, "School Admin" ) == 0 || strcasecmp ( $category, "TEACHER" ) == 0)
		$query .= " AND subcategory='School'";
	if (strcasecmp ( $category, "Home Center Admin" ) == 0)
		$query .= " AND subcategory='Home Center'";
	if ($section != "")
		$query .= " AND childSection ='$section'";
	$r = mysql_query ( $query ) or die ( $query . "<br/>" . mysql_error () );
	while ( $l = mysql_fetch_array ( $r ) ) {
		$userArray [] = $l [0];
	}
	return $userArray;
}
function checkTeacherClass($class, $userID) {
	$validClass = '';
	$sq = "SELECT DISTINCT class FROM  adepts_teacherClassMapping WHERE userID='$userID' AND subjectNo=" . SUBJECTNO;
	$rs = mysql_query ( $sq );
	while ( $rw = mysql_fetch_assoc ( $rs ) ) {
		$classNew = $rw ['class'];
		if (stripos ( ',' . $class . ',', ',' . $classNew . ',' ) !== false) {
			$validClass = "valid";
			break;
		}
	}
	return $validClass;
}
function convertToTimeCommon($date)
{

	$hr   = substr($date,11,2);
	$mm   = substr($date,14,2);
	$ss   = substr($date,17,2);
	$day  = substr($date,8,2);
	$mnth = substr($date,5,2);
	$yr   = substr($date,0,4);
	$time = mktime($hr,$mm,$ss,$mnth,$day,$yr);
	return $time;
}
function getLiveClasses($schoolCode, $classSectionArr = 0) {
	$arrayLoggedIn = array ();

	$sq = "SELECT b.userID,childClass,childSection,time_to_sec(timediff(now(),starttime))/60 minlogged,schoolCode FROM adepts_sessionStatus a,adepts_userDetails b
			 WHERE isnull(endtime) AND logout_flag=0 AND a.userid=b.userid AND category='STUDENT' AND startTime_int=" . date ( "Ymd" ) . "
			 HAVING minlogged<45";
	$rs = mysql_query ( $sq );
	while ( $rw = mysql_fetch_array ( $rs ) ) {
		if ($rw [4] == $schoolCode) {
			if ($classSectionArr == 0)
				$arrayLoggedIn [$rw [1]] [$rw [2]] ++;
			else if (in_array ( $rw [1] . $rw [2], $classSectionArr ))
				$arrayLoggedIn [$rw [1]] [$rw [2]] ++;
		}
	}
	return $arrayLoggedIn;
}
?>
<script type="text/javascript">
function generateReport(schoolCode,classvalue,sectionvalue){
	if(schoolCode == 0){
		window.location = "classLevelReport.php";
	}else{
		window.location = "classLevelReport.php?schoolCode="+schoolCode+"&cls="+classvalue+"&section="+sectionvalue;
	}
}

</script>
<?php include_once ("../userInterface/commonAQAD.php");?>
<?php include("footer.php");?>
