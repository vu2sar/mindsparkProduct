<?php
	@include_once("check1.php");
	include_once("classes/clsUser.php");
	include_once("constants.php");
	include("functions/functions.php");
	if(!isset($_SESSION['userID']))
	{
		header("Location:logout.php");
		exit;
	}
	$userID = $_SESSION['userID'];
	$objUser = new User($userID);

	$Name = explode(" ", $_SESSION['childName']);	
	$Name = $Name[0];

	$childName 	   = $objUser->childName;
	$schoolCode    = $objUser->schoolCode;
	$childClass    = $objUser->childClass;
	$childSection  = $objUser->childSection;
	$category 	   = $objUser->category;
	$subcategory   = $objUser->subcategory;

	if(strcasecmp($category,"School Admin")==0 || strcasecmp($category,"Teacher")==0)
	{
		header("Location:ti_home.php");
		exit;
	}
	/* update worksheet accuracy and status which have status='allAttempted' and enddate is passed*/
	$pendingRecordsQuery = "SELECT a.srno,a.status,ROUND((SUM(IF(b.RW=1,1,0))/COUNT(b.wsd_id))*100,2) as accuracy from worksheet_attempt_status a JOIN worksheet_attempt_detail b ON b.ws_srno=a.srno JOIN worksheet_master c ON c.wsm_id=a.wsm_id where a.status ='allAttempted' and a.userID =$userID and c.schoolCode=$schoolCode and c.end_datetime<NOW() GROUP BY b.ws_srno";	
		$pendingRecordsResult = mysql_query($pendingRecordsQuery);	
		if(mysql_num_rows($pendingRecordsResult) != 0 )
		{	
			while($line=mysql_fetch_array($pendingRecordsResult))
			{
				$query = "UPDATE worksheet_attempt_status SET status='completed', accuracy=".$line['accuracy']." where srno=".$line['srno'];		
				mysql_query($query);				
				$_SESSION["noOfJumps"] =    $_SESSION["noOfJumps"] + 10;
				$_SESSION['sparkie']['worksheetCompletion'] += 10;
				$query = "UPDATE ".TBL_SESSION_STATUS." SET noOfJumps = (noOfJumps + 10) WHERE sessionID=".$_SESSION['sessionID'];				
				mysql_query($query);
			}
		}
	$teacherWorksheets = array();	
	$query = 'SELECT wm.wsm_id,wm.wsm_name,if(wm.end_datetime>NOW(),if(ws.status = "pending" || ws.status = "allAttempted","Pending",if(ws.status = "completed","Completed","Not Attempted")),if(ws.status = "completed" || ws.status = "allAttempted","Completed",if(ws.status = "pending","Incomplete Submission","Not Attempted"))) as status,DATE_FORMAT(wm.end_datetime,"%d-%b-%Y  %l:%i %p") as dueDate,ws.feedback,if((wm.end_datetime<NOW()),1,0) as statusFlag,SUM(IF(wa.RW!=-1,1,0)) as attempted,COUNT(wd.wsd_id) as total,if(ws.status != "completed",ROUND((SUM(IF(wa.RW=1,1,0))/COUNT(wd.wsd_id))*100,1),ROUND(ws.accuracy,1)) as accuracy,ws.lastModified from worksheet_master as wm 	 
		JOIN worksheet_detail as wd ON wd.wsm_id=wm.wsm_id
		LEFT JOIN worksheet_attempt_status as ws ON ws.wsm_id = wm.wsm_id AND ws.userID='.$userID.' 	
		LEFT JOIN worksheet_attempt_detail wa ON wa.wsd_id=wd.wsd_id AND wa.userID='.$userID.'		   
		WHERE wm.assign_flag = 1 AND wm.start_datetime<=NOW() AND wm.schoolCode='.$schoolCode.' AND wm.class='.$childClass;			
	if($childSection != "")
		$query .= " AND FIND_IN_SET('$childSection',wm.section_list)";

		$query .=" GROUP BY wm.wsm_id ORDER BY wm.start_datetime DESC";		

		$result = mysql_query($query);		
		if(mysql_num_rows($result) != 0 )
		{
			while($row = mysql_fetch_array($result))
			{        				
				$teacherWorksheets[]=$row;			                       			
	        } 
		}
		      
?>

<?php include("header.php"); ?>

<title>Worksheets</title>

<link rel="stylesheet" type="text/css" href="css/colorbox.css">
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script src="libs/jquery.colorbox-min.js" type="text/javascript"></script>
<script type='text/javascript' src='libs/combined.js?ver=2'></script>
<script>
var langType = '<?=$language;?>';
</script>
	<?php
	if($theme==2) { ?>
    <link href="css/commonMidClass.css" rel="stylesheet" type="text/css">
    <link href="css/homeworkSelection/midClass.css?ver=3" rel="stylesheet" type="text/css">
	<script>
		function load(){
			var a= window.innerHeight -240;
			$('#reportContainer').css("height",a);
		}
	</script>
	<?php } else if($theme==3) { ?>
			<link href="css/commonHigherClass.css" rel="stylesheet" type="text/css">
            <link href="css/homeworkSelection/higherClass.css?ver=4" rel="stylesheet" type="text/css">	
	<?php } else if($theme == 1) {?>
	<!-- stylesheets added for class 3 worksheets -->
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
    <link href="css/homeworkSelection/lowerClass.css?ver=3.1" rel="stylesheet" type="text/css">

    <?php } ?>
    <script>
		$(document).ready(function(e) {
		if (window.location.href.indexOf("localhost") > -1) {	
		    var langType = 'en-us';
		}	
		<?php if(isset($_GET['instruction']) && $_GET['instruction'] == "yes") { ?>
			openInstruction();
		<?php } ?>
			i18n.init({ lng: langType,useCookie: false }, function(t) {
				$(".translation").i18n();
				$(document).attr("title",i18n.t("worksheetSelectionPage.title"));
				if($("#exitMsg"))
				{
					$("#exitMsg").html(i18n.t("worksheetSelectionPage.title"));
				}
			});
		});
		function load(){
				var a= window.innerHeight - (150);
				$('#reportContainer').css({"height":a+"px"});
				$('#topicInfoContainer').css({"height":a+"px"});
				$('#menuBar').css({"height":a+"px"});
				$('#main_bar').css({"height":a+"px"});
				$('#sideBar').css({"height":a+"px"});
				<?php if($theme==2) { ?>
					var a= window.innerHeight - (240);
					$('#reportContainer').css({"height":a+"px"});
				<?php } ?>
			}
		var click=0;
		function openInstruction()
		{  
			$.fn.colorbox({'href':'#instruction','inline':true,'open':true,'escKey':true, 'height':420, 'width':500});
		}
		function worksheetAttempt(wsm_id, linkType)
		{
			setTryingToUnload();
			if(linkType == "block"){
				alert('You will only be able to see the report after the due date.');
				return false;
			}
			else if(linkType == "question")
			{
				document.getElementById("wsm_id").value = wsm_id;
				document.getElementById("frmTeacherTopicSelection").action = "controller.php";
				document.getElementById("frmTeacherTopicSelection").submit();
			}
			else
			{
				document.getElementById("worksheetID").value = wsm_id;
				document.getElementById("frmTeacherTopicSelection").action = "topicWiseQuesTrail.php";
				document.getElementById("frmTeacherTopicSelection").submit();
			}
		}
		function logoff()
		{
			setTryingToUnload();
			window.location="logout.php";
		}
		function getHome()
		{
			setTryingToUnload();
			window.location.href	=	"home.php";
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
	</script>
</head>
<body onLoad="load();" onResize="load();" class="translation">
	<div id="top_bar" class="top_bar_part4">
		<div class="logo">
		</div>
		<!-- div added for class 3 worksheet -->
		<div id="studentInfoLowerClass" class="forLowerOnly hidden">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?=$Name?>&nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a href='myDetailsPage.php' onClick="javascript:setTryingToUnload();" ><span data-i18n="homePage.myDetails"></span></a></li>
									<li><a href='changePassword.php' onClick="javascript:setTryingToUnload();" ><span data-i18n="homePage.changePassword"></span></a></li>
                                </ul>
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
		<div id="logout" class="hidden">
        	<div class="logout" onClick="logoff()"></div>
        	<div class="logoutText"  onclick="logoff()">Logout</div>
        </div>
    </div>

	<div id="container">
		<div id="info_bar" class="hidden forHigherOnly">
			<div id="topic">
				<div id="clickText" data-i18n="worksheetSelectionPage.activityTxt"></div>
				<div id="home">
					<div class="icon_text1" style="cursor: default;text-align:left;"><span class="textUppercase" id="homeText" data-i18n="dashboardPage.home" onClick="getHome()"></span> > <font color="#606062"><span class="textUppercase" data-i18n="worksheetSelectionPage.title"></span></font></div>
				</div>
			</div>
			<div class="class">
				<strong><span data-i18n="common.class"></span> </strong> <?=$childClass.$childSection?>
			</div>
			<div class="Name">
				<strong><?=$Name?></strong>
			</div>
            <div id="new" onClick="openInstruction()">
                <div class="icon_text textUppercase" data-i18n="worksheetSelectionPage.readInst"></div>
                <div id="pointed">
                </div>
            </div>
		</div>
		<!-- header added for class 3 worksheet -->
		<div id="info_bar" class="forLowerOnly hidden">
        	<div id="blankWhiteSpace"><div id="clickText" data-i18n="worksheetSelectionPage.activityTxt"></div></div>
                <div id="home">
                	<div id="homeIcon" class="linkPointer" onClick="getHome()"></div>
                    <div id="dashboardHeading" class="forLowerOnly">&ndash;&nbsp;
                   	<span class="textUppercase" data-i18n="worksheetSelectionPage.title"></span>
                    </div>
                    
                </div>
                <div id="newInstrucion" onClick="openInstruction()">
	                <div class="icon_text textUppercase" data-i18n="worksheetSelectionPage.readInst"></div>	                
                </div>
            </div>              
		<div id="info_bar" class="forHighestOnly">
			<div id="topic">
                <div id="home">
                	<a href="home.php">
                        <div id="homeIcon"></div>
                    </a>
                    <div id="homeText" class="hidden"><span class="textUppercase" data-i18n="dashboardPage.home"></span> > <font color="#606062"> <span class="textUppercase" data-i18n="dashboardPage.dashboard"></span></font></div>
                </div>
				
				<a href="home.php" style="text-decoration:none;color:inherit"><div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase" data-i18n="homePage.recent"></span></div>
                </div></a>
                <div id="activatedAtHome" class="forLowerOnly">
                	<div id="activatedAtHomeIcon"></div>
                    <div class="clear"></div>
                </div>
				<div class="clear"></div>
			</div>
			<div class="class">
				<strong>Class </strong>: <?=$childClass.$childSection?>
			</div>
			<div class="Name">
				<strong><?=$objUser->childName?></strong>
			</div>
            <div class="clear"></div>
            <a href="sessionWiseReport.php"><div id="sessionWiseReport" class="textUppercase" data-i18n="dashboardPage.sessionWise"></div></a>
		</div>
		<div id="main_bar" class="forHighestOnly">
			<div id="drawer1">
			<a href="activity.php" style="text-decoration:none;color:inherit"> 
			<div id="drawer1Icon"></div>
			ACTIVITIES
			</div></a>
			<a href="examCorner.php"><div id="drawer2"><div id="drawer2Icon"></div>EXAM CORNER
			</div></a>
			<a href="home.php"><div id="drawer3"><div id="drawer3Icon"></div>HOME
			</div></a>
            <a href="explore.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;color:inherit"><div id="drawer4"><div id="drawer4Icon"></div>EXPLORE ZONE
			</div></a>
			<div id="plus" onClick="openMainBar();">
				<div id="vertical"></div>
				<div id="horizontal"></div>
			</div>
			<a href="src/rewards/rewardsDashboard.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;">
			<div id="drawer5"><div id="drawer5Icon" style='<?php if($_SESSION['rewardSystem']!=1) { echo 'position: absolute;background: url("assets/higherClass/dashboard/rewards.png") no-repeat 0 0 !important;width: 50px;height: 50px;margin-top: -30px;margin-left: -50px;";';} ?>' class="<?=$sparkieImage?>"><div class="redCircle"><?=$sparkieWon?></div></div>REWARDS CENTRAL</div></a>			
		</div>
		<div id="containerMainWorksheet">
		<form name="frmTeacherTopicSelection" action="homeworkSelection.php" id="frmTeacherTopicSelection" method="POST">
			<div id="reportContainer">
			<div id="menuBar" class="forHighestOnly">
					<div id="sideBar">
						<div id="questionTrail" onClick="openInstruction()">
							<span id="questionTrailText">READ DIRECTIONS</span>
							<div id="questionTrailIcon" class="circle11"></div>
						</div>
						<div class="empty">
						</div>
					</div>
					</div>
			<?php			
			{
			?>
			<br />
				<table id="heading" width="86%" align="center">
					<tr>
						<td class="headingText" align="center" id="head" data-i18n="worksheetSelectionPage.worksheetName" width="25%"></td>
						<td class="headingText" align="center" id="head" width="12%">Status</td>
						<td class="headingText" align="center" data-i18n="worksheetSelectionPage.dueDate" width="15%"></td>
						<td class="headingText" align="center" data-i18n="worksheetSelectionPage.quesAnswered" width="15%"></td>
						<td class="headingText" align="center" data-i18n="worksheetSelectionPage.accuracy" width="8%"></td>
						<td class="headingText" align="center" data-i18n="worksheetSelectionPage.sparkies" width="8%"></td>
						<td class="headingText" align="center" data-i18n="worksheetSelectionPage.teacherComments" width="17%"></td>
					</tr>
				</table>
			    <table class="topicPosition" align="center" width="85%">
					<?php 
					if (count($teacherWorksheets)>0){
		            foreach($teacherWorksheets as $tw)
		            {																						
													
						if($tw['statusFlag'])
							$linkType = 'report';
						else if($tw['status']=='Pending' || $tw['status']=='Not Attempted')
							$linkType = 'question';						
						else
							$linkType = 'block';						

						if($tw['status']=='Completed' && date($tw['lastModified']) > ("2017-01-20 18:00:00"))
							$tw['sparkies'] =10;
						else
							$tw['sparkies']='';
					?>
						<tr class="exContainer">
						<td class="exercise" width="25%"  onclick="worksheetAttempt('<?=$tw['wsm_id']?>','<?=$linkType?>')"><a href="javascript:void(0)" class="topic" > <?=stripcslashes($tw['wsm_name'])?></a></td>
						<td align="center" id="head" width="12%"><?=$tw['status']?></td>
						<td class="date" width="15%"><?=$tw['dueDate'];?></td>
						<td class="TQ" width="15%">
		                	<?php
		                		echo $tw['attempted'].' of '.$tw['total'];																
							?>
		                </td>
						<td class="accuracy" width="8%">
						<?php 						
						if($tw['statusFlag'])
							echo $tw['accuracy']=='0.0' || $tw['accuracy']==''? '0%' : $tw['accuracy'].'%';
						else
							echo 'NA';						
						?></td>
						<td class="teacherComments" width="8%"><?php echo $tw['sparkies']!='' ? $tw['sparkies']:'-';?></td>
						<td class="teacherComments" width="17%"><?php echo $tw['feedback']!='' ? $tw['feedback']:'-';?></td>
						</tr>
					<?php } 
					}
					else {
						?>
						<tr>
							<td colspan="6" align="center" >You have not been assigned a worksheet yet. Please ask your teacher to assign a worksheet to you.</td>
						</tr>
						<?php
					}
					?>
				</table>
				<br/>
				<br/>
		        <br/>
		        <br/>
			</div>
			<?php
			}
			?>
			<input type="hidden" name="mode" id="mode" value="worksheet">
			<input type="hidden" name="trailType" id="trailType" value="worksheet">
			<input type="hidden" name="wsm_id" id="wsm_id">
			<input type="hidden" name="worksheetID" id="worksheetID">
			<input type="hidden" name="quesCategory" id="quesCategory" value="worksheet">
			<input type="hidden" name="section" id="section" value="<?=$childSection?>">
			<input type="hidden" name="student_userID" id="student_userID" value="<?=$userID?>">
			<input type="hidden" name="accessFromStudentInterface" id="accessFromStudentInterface" value="1">
			<input type="hidden" name="userType" id="userType" value="">
		</form>
		</div>
		
	</div>

<div style="display:none">
	<div id="instruction" style="font-family: 'Conv_HelveticaLTStd-Light';font-size:18px;">
		<h3 align="center">Instructions</h3>
    	<ol>
        	<li>Answers to questions will be saved automatically, but you can only submit the worksheet after you attempt all questions.</li>
			<li>You can skip questions in the worksheet for later by clicking on the question number on the top.</li>
			<li>Once the worksheet is submitted, the answers cannot be changed.</li>
			<li>You cannot submit the worksheet after the due date and time.</li>
			<li>After you attempt all quesions and submit your worksheet,you will be awarded 10 sparkies.<img src="assets/sparkie.png"  height="20px"></li>
        </ol>
    </div>
</div>
<?php include("footer.php"); ?>

