<?php
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);

	@include_once("check1.php");
	include("classes/clsUser.php");
	include("constants.php");
	include("functions/functions.php");
	include_once("classes/clsTopicProgress.php");
	
	if(!isset($_SESSION['userID']))
	{
		header("Location:logout.php");
		exit;
	}
	if(isset($_GET["userType"]))
		$_SESSION["userType"] = $_GET["userType"];
	if(isset($_SESSION['revisionSessionTTArray']) && count($_SESSION['revisionSessionTTArray'])>0)
    {
        header("Location: controller.php?mode=login");
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
	$arrActivities = getActivities($childClass,"",$objUser->packageType);	
	$activeTopicsAndCompletedClusters =getActiveTopicsAndCompletedClusters($schoolCode,$childClass,$childSection,$userID,$category,$subcategory);
	// echo "<pre>";
	// print_r($activeTopicsAndCompletedClusters);	
	$notAttemptedActivityCounter = 0;
	$attemptedActivityCounter = 0;
	$lockedActivityCounter = 0;

	$today = date("Y-m-d");
	$lastMon = date("Y-m-d",strtotime("last Sunday"));

	$timeSpentOnActivitiesToday = getTimeSpentOnActivities($userID,$arrActivities,$today,$today);
	$timeSpentOnActivitiesThisWeek = getTimeSpentOnActivities($userID,$arrActivities,$lastMon,$today);
	$allGamesAttempted = getAllAttemptedGamesByUser($userID,$childClass);
	$newGames	=	getNewActivities($userID,$childClass);
	$totalTimeSpentThisWeek = array_sum($timeSpentOnActivitiesThisWeek);

	$totalTimeAllowedForAcitivitesInaWeek = 60; //in mins
	$isWeekLimitCompleted = false;	
	$lockedActivities = array();
	$unlockedActivities = array();	
	foreach ($arrActivities as $id=>$arrDetails)
	{		
		$locked = 0;
		$lockedMsg = "";
		$TTName = "";			
		
		if($arrDetails["teacherTopicCode"]!="")
		{
			$TTName = getTeacherTopicDesc($arrDetails["teacherTopicCode"]);
			$teacherTopics[$arrDetails["teacherTopicCode"]] = $TTName;
			$topicProgressDetails = getProgressInTopic($arrDetails["teacherTopicCode"],$childClass,$userID);
			$progress = $topicProgressDetails['progress'];
		}
		if($totalTimeSpentThisWeek > $totalTimeAllowedForAcitivitesInaWeek)
		{
			$locked = 1;
			$lockedMsg = "You have completed weekly limit of ".$totalTimeAllowedForAcitivitesInaWeek." minutes.";
			$isWeekLimitCompleted = true;
		}				
		elseif (strcasecmp($category,"STUDENT")==0 && (strcasecmp($subcategory,"SCHOOL")==0 || strcasecmp($subcategory,"Home Center")==0) && $arrDetails["teacherTopicCode"]!="" && !in_array($arrDetails["teacherTopicCode"],$activeTopicsAndCompletedClusters['activeTopics']))
		{
			// echo $arrDetails["teacherTopicCode"]."<br/>";
			// echo "<pre>";
			// print_r($arrDetails);
			unset($arrActivities[$id]);
			continue;			
		}	
		elseif (strcasecmp($category,"STUDENT")==0 && (strcasecmp($subcategory,"SCHOOL")==0 || strcasecmp($subcategory,"Home Center")==0) && $arrDetails["linkedToCluster"]!="") 
		{
			if(in_array($arrDetails["linkedToCluster"],$activeTopicsAndCompletedClusters['activeTopicClusters']))
			{
				if (isClusterCompletedSuccesfully($arrDetails["linkedToCluster"]) || in_array($arrDetails["linkedToCluster"],$activeTopicsAndCompletedClusters['completedClusters']))
				$locked=0;
			
				else {
					$clusterName = getClusterCodeDesc($arrDetails["linkedToCluster"]);
					$locked=5;
					$lockedMsg = "Clear <b>".$clusterName."</b> first to unlock.";
				}
			}
			else
			{
				// echo $arrDetails["linkedToCluster"]."<br/>";
				// echo "<pre>";
				// print_r($arrDetails);
				unset($arrActivities[$id]);	
				continue;			
			}


		}
		elseif (strcasecmp($category,"STUDENT")==0 && (strcasecmp($subcategory,"SCHOOL")==0 || strcasecmp($subcategory,"Home Center")==0) && $arrDetails["topicCompletion"]!="" && $arrDetails["teacherTopicCode"]!="" && $progress < $arrDetails["topicCompletion"] && $arrDetails["topicCompletion"]!=0) 
		{
			$customTopicArray = getCustomizedTopics($arrDetails["teacherTopicCode"],$schoolCode,$childClass);
			if(count($customTopicArray) > 0)
			{
				$progress = getProgressOfCustoms($customTopicArray,$userID);				
				if($progress < $arrDetails["topicCompletion"])
				{
					$locked = 3;
					$lockedMsg = "You must have topic progress of ".$arrDetails['topicCompletion']."% to unlock this.";		
				}
			}
			else
			{				
				$locked = 3;
				$lockedMsg = "You must have topic progress of ".$arrDetails['topicCompletion']."% to unlock this.";
			}
		}
			
		if($locked == 0)
		{				
			$unlockedActivities[] = $id;
			$arrActivities[$id]["locked"] = $locked;
			$arrActivities[$id]["lockedMsg"] = $lockedMsg;
			$arrActivities[$id]["TTName"] = $TTName;
		}
		else
		{					
			$lockedActivities[] = $id;
			$arrActivities[$id]["locked"] = $locked;
			$arrActivities[$id]["lockedMsg"] = $lockedMsg;
			$arrActivities[$id]["TTName"] = $TTName;			
		}
		
		
	}		
	$arrVideoDetails = getVideos($childClass,$userID);
	$sparkieImage = $_SESSION['sparkieImage'];
?>

<?php include("header.php");?>
<link rel="stylesheet" type="text/css" href="css/colorbox.css">
<title>Activities</title>
	<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
	<script src="libs/jquery.colorbox-min.js" type="text/javascript"></script>
	<?php
	if($theme==1) { ?>
	<link href="css/activity/lowerClass.css?ver=11" rel="stylesheet" type="text/css">
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<script>
		function load(){
			$('#clickText').html("");
			if(window.innerHeight>500){
				var a= window.innerHeight -80;
				var b= window.innerHeight -400;
				$('#activitiesContainer').css("height",a);
				$('#notattemptedC').css("height",b);
			}
		}
		var infoClick=0;
		var a=0;
		var b=0;
		var e=0;
		var e1=0;
		function load(){
			$(".notAttempted").show();
			$('#notattemptedC').css("height","auto");
			$('#notattemptedC').css("min-height","400px");
			$('#activitiesContainer').css("height","auto");
			$("#largeContainer").attr("style","width:<?=(count($arrActivities)+3)*220?>px");
			$("#largeContainer1").attr("style","width:<?=(count($arrActivities)+3)*220?>px");
			$("#largeContainer2").attr("style","width:<?=(count($arrActivities)+3)*220?>px");
			$("#largeContainer3").attr("style","width:<?=(count($arrActivities)+3)*220?>px");
			if(androidVersionCheck==1){
				$('#activitiesContainer').css("height","auto");
			}
		}
		function goRight(x){
			var x=x;
			if(a+881<x){
				a = a+880;
				var c=a/880 +1;
				var d=c-1;
				$("#na"+c).css({
				     '-moz-transform':'scale(2,2)','-webkit-transform':'scale(2,2)','-o-transform':'scale(2,2)','-ms-transform':'scale(2,2)'
				});
				$("#na"+d).css({
				     '-moz-transform':'scale(1,1)','-webkit-transform':'scale(1,1)','-o-transform':'scale(1,1)','-ms-transform':'scale(1,1)'
				});
				$(".arrow-leftin").css("display","none");
				$('.mainContainerX').animate({scrollLeft: a}, 'slow');
			}
			else{
				$(".arrow-rightin").css("display","block");
			}
		}
		function goLeft(){
			if(a>0)
			{
				$(".arrow-leftin").css("display","none");
				$(".arrow-rightin").css("display","none");
				a = a-880;
				var c=a/880 +1;
				var d=c+1;
				$("#na"+c).css({
				     '-moz-transform':'scale(2,2)','-webkit-transform':'scale(2,2)','-o-transform':'scale(2,2)','-ms-transform':'scale(2,2)'
				});
				$("#na"+d).css({
				     '-moz-transform':'scale(1,1)','-webkit-transform':'scale(1,1)','-o-transform':'scale(1,1)','-ms-transform':'scale(1,1)'
				});
				$('.mainContainerX').animate({scrollLeft: a}, 'slow');
				if(a==0){
					$(".arrow-leftin").css("display","block");
				}
			}
			else{
				$(".arrow-leftin").css("display","block");
			}
		}
		function goRight1(x){
			var x=x;
			if(b+881<x){
			b = b+880;
			var c=b/880 +1;
			var d=c-1;
			$("#a"+c).css({
			     '-moz-transform':'scale(2,2)','-webkit-transform':'scale(2,2)','-o-transform':'scale(2,2)','-ms-transform':'scale(2,2)'
			});
			$("#a"+d).css({
			     '-moz-transform':'scale(1,1)','-webkit-transform':'scale(1,1)','-o-transform':'scale(1,1)','-ms-transform':'scale(1,1)'
			});
			$(".arrow-leftin1").css("display","none");
			$('.mainContainerY').animate({scrollLeft: b}, 'slow');
			}
			else{
				$(".arrow-rightin1").css("display","block");
			}
		}
		function goLeft1(){
			if(b>0)
			{
				$(".arrow-leftin1").css("display","none");
				$(".arrow-rightin1").css("display","none");
				b = b-880;
				var c=b/880 +1;
				var d=c+1;
				$("#a"+c).css({
				     '-moz-transform':'scale(2,2)','-webkit-transform':'scale(2,2)','-o-transform':'scale(2,2)','-ms-transform':'scale(2,2)'
				});
				$("#a"+d).css({
				     '-moz-transform':'scale(1,1)','-webkit-transform':'scale(1,1)','-o-transform':'scale(1,1)','-ms-transform':'scale(1,1)'
				});
				$('.mainContainerY').animate({scrollLeft: b}, 'slow');
				if(b==0){
					$(".arrow-leftin1").css("display","block");
				}
			}
			else{
				$(".arrow-leftin1").css("display","block");
			}
		}
		function goRight2(x){
			var x=x;
			if(e+881<x){
			e = e+880;
			var c=e/880 +1;
			var d=c-1;
			$("#l"+c).css({
			     '-moz-transform':'scale(2,2)','-webkit-transform':'scale(2,2)','-o-transform':'scale(2,2)','-ms-transform':'scale(2,2)'
			});
			$("#l"+d).css({
			     '-moz-transform':'scale(1,1)','-webkit-transform':'scale(1,1)','-o-transform':'scale(1,1)','-ms-transform':'scale(1,1)'
			});
			$(".arrow-leftin2").css("display","none");
			$('.mainContainerZ').animate({scrollLeft: e}, 'slow');
			}
			else{
				$(".arrow-rightin2").css("display","block");
			}
		}
		function goLeft2(){
			if(e>0)
			{
				$(".arrow-leftin2").css("display","none");
				$(".arrow-rightin2").css("display","none");
				e = e-880;
				var c=e/880 +1;
				var d=c+1;
				$("#l"+c).css({
				     '-moz-transform':'scale(2,2)','-webkit-transform':'scale(2,2)','-o-transform':'scale(2,2)','-ms-transform':'scale(2,2)'
				});
				$("#l"+d).css({
				     '-moz-transform':'scale(1,1)','-webkit-transform':'scale(1,1)','-o-transform':'scale(1,1)','-ms-transform':'scale(1,1)'
				});
				$('.mainContainerZ').animate({scrollLeft: e}, 'slow');
				if(e==0){
					$(".arrow-leftin2").css("display","block");
				}
			}
			else{
				$(".arrow-leftin2").css("display","block");
			}
		}
		
		function goRight3(x){
			var x=x;
			if(e1+881<x){
			e1 = e1+880;
			var c=e1/880 +1;
			var d=c-1;
			$("#3"+c).css({
			     '-moz-transform':'scale(2,2)','-webkit-transform':'scale(2,2)','-o-transform':'scale(2,2)','-ms-transform':'scale(2,2)'
			});
			$("#3"+d).css({
			     '-moz-transform':'scale(1,1)','-webkit-transform':'scale(1,1)','-o-transform':'scale(1,1)','-ms-transform':'scale(1,1)'
			});
			$(".arrow-leftin3").css("display","none");
			
			$('.mainContainerV').animate({scrollLeft: e1}, 'slow');
			}
			else{
				$(".arrow-rightin3").css("display","block");
			}
		}
		function goLeft3(){
			if(e1>0)
			{
				$(".arrow-leftin3").css("display","none");
				$(".arrow-rightin3").css("display","none");
				e1 = e1-880;
				var c=e1/880 +1;
				var d=c+1;
				$("#3"+c).css({
				     '-moz-transform':'scale(2,2)','-webkit-transform':'scale(2,2)','-o-transform':'scale(2,2)','-ms-transform':'scale(2,2)'
				});
				$("#3"+d).css({
				     '-moz-transform':'scale(1,1)','-webkit-transform':'scale(1,1)','-o-transform':'scale(1,1)','-ms-transform':'scale(1,1)'
				});
				$('.mainContainerV').animate({scrollLeft: e1}, 'slow');
				if(e1==0){
					$(".arrow-leftin3").css("display","block");
				}
			}
			else{
				$(".arrow-leftin3").css("display","block");
			}
		}
		
		function hideBar(){
			if (infoClick==0){
				$("#hideShowBar").text("+");
				$('#info_bar').animate({'height':'75px'},600);
				$('#topic').animate({'height':'50px'},600);
				$('#clickText').animate({'margin-top':'1px'},600);
				$('#new').animate({'margin-top':'12px'},600);
				$('.icon_text1').animate({'margin-top':'3px'},600);
				$('.Name').hide();
				$('.class').hide();
				var a= window.innerHeight -130 -45;
				if(androidVersionCheck==1){
				$('#activitiesContainer').animate({'height':'auto'},600);
				}
				else{
					$('#activitiesContainer').animate({'height':a},600);
				}
				infoClick=1;
			}
			else if(infoClick==1){
				$("#hideShowBar").text("-");
				$('#info_bar').animate({'height':'120px'},600);
				$('#topic').animate({'height':'95px'},600);
				$('#new').animate({'margin-top':'58px'},600);
				$('#clickText').animate({'margin-top':'10px'},600);
				$('.icon_text1').animate({'margin-top':'15px'},600);
				$('.Name').show();
				$('.class').show();
				var a= window.innerHeight - (80+ 140 );
				if(androidVersionCheck==1){
				$('#activitiesContainer').animate({'height':'auto'},600);
				}
				else{
					$('#activitiesContainer').animate({'height':a},600);
				}
				infoClick=0;
			}
			if(androidVersionCheck==1){
				$('#activitiesContainer').css("height","auto");
			}
		}
		function attempt(id){
			
		}
	</script>
	<?php } 
	else if($theme==2) { ?>
	<link href="css/activity/midClass.css?ver=1" rel="stylesheet" type="text/css">
    <link href="css/commonMidClass.css" rel="stylesheet" type="text/css">
	<script>
		var infoClick=0;
		var a=0;
		var b=0;
		var e=0;
		var e1=0;
		function load(){
			var attemptedActivityCounter = $(".attemptedCounterClass");
			var notAttemptedActivityCounter = $(".notAttemptedCounterClass");
			var lockedActivityCounter = $(".lockedCounterClass");
			var a= window.innerHeight -220;
			$('#activitiesContainer').css("height",a);
			/*hideBar();*/
			$(".notAttempted").show();
			$("#largeContainer").attr("style","width:<?=(count($arrActivities)+3)*220?>px");
			$("#largeContainer1").attr("style","width:<?=(count($arrActivities)+3)*220?>px");
			$("#largeContainer2").attr("style","width:<?=(count($arrActivities)+3)*220?>px");
			$("#largeContainer3").attr("style","width:<?=(count($arrActivities)+3)*220?>px");
			if(androidVersionCheck==1){
				$('#activitiesContainer').css("height","auto");
			}
			var copyrightHeight = $("#notattemptedC").height()+$("#top_bar").height()+$("#topic").height();
			$("#copyright").css({"position":"absolute","right":"0px","top":copyrightHeight+"px"});

			if(attemptedActivityCounter.length <= 4)
				$(".arrow-left1, .arrow-right1").hide();
			if(notAttemptedActivityCounter.length <= 4)
				$(".arrow-left, .arrow-right").hide();
			if(lockedActivityCounter.length <= 4)
				$(".arrow-left2, .arrow-right2").hide();

			if(window.navigator.userAgent.indexOf("Android") > 0)
			{
				$(".circle").css("line-height","20px");
			}

		}
		function goRight(x){
			var x=x;
			if(a+881<x){
				a = a+880;
				var c=a/880 +1;
				var d=c-1;
				$("#na"+c).css({
				     '-moz-transform':'scale(2,2)','-webkit-transform':'scale(2,2)','-o-transform':'scale(2,2)','-ms-transform':'scale(2,2)'
				});
				$("#na"+d).css({
				     '-moz-transform':'scale(1,1)','-webkit-transform':'scale(1,1)','-o-transform':'scale(1,1)','-ms-transform':'scale(1,1)'
				});
				$(".arrow-left").css("background","url(assets/arrows.png) no-repeat");
				$('.mainContainerX').animate({
				   scrollLeft: a
				}, 'slow');
				if(a+881>x)
					$(".arrow-right").css("background","url(assets/arrows.png) no-repeat -60px");
			}
			else{
				$(".arrow-right").css("background","url(assets/arrows.png) no-repeat -60px");
			}
		}
		function goLeft(){
			if(a>0)
			{
				$(".arrow-left").css("background","url(assets/arrows.png) no-repeat");
				$(".arrow-right").css("background","url(assets/arrows.png) no-repeat -18px");
				a = a-880;
				var c=a/880 +1;
				var d=c+1;
				$("#na"+c).css({
				     '-moz-transform':'scale(2,2)','-webkit-transform':'scale(2,2)','-o-transform':'scale(2,2)','-ms-transform':'scale(2,2)'
				});
				$("#na"+d).css({
				     '-moz-transform':'scale(1,1)','-webkit-transform':'scale(1,1)','-o-transform':'scale(1,1)','-ms-transform':'scale(1,1)'
				});
				$('.mainContainerX').animate({
				   scrollLeft: a
				}, 'slow');
				if(a==0){
					$(".arrow-left").css("background","url(assets/arrows.png) no-repeat -40px");
				}
			}
			else{
				$(".arrow-left").css("background","url(assets/arrows.png) no-repeat -40px");
			}
		}
		function goRight1(x){
			var x=x;
			if(b+881<x){
			b = b+880;
			var c=b/880 +1;
			var d=c-1;
			$("#a"+c).css({
			     '-moz-transform':'scale(2,2)','-webkit-transform':'scale(2,2)','-o-transform':'scale(2,2)','-ms-transform':'scale(2,2)'
			});
			$("#a"+d).css({
			     '-moz-transform':'scale(1,1)','-webkit-transform':'scale(1,1)','-o-transform':'scale(1,1)','-ms-transform':'scale(1,1)'
			});
			$(".arrow-left1").css("background","url(assets/arrows.png) no-repeat");
			$('.mainContainerY').animate({
			   scrollLeft: b
			}, 'slow');
			if(b+881>x)
					$(".arrow-right1").css("background","url(assets/arrows.png) no-repeat -60px");
			}
			else{
				$(".arrow-right1").css("background","url(assets/arrows.png) no-repeat -60px");
			}
		}
		function goLeft1(){
			if(b>0)
			{
				$(".arrow-left1").css("background","url(assets/arrows.png) no-repeat");
				$(".arrow-right1").css("background","url(assets/arrows.png) no-repeat -18px");
				b = b-880;
				var c=b/880 +1;
				var d=c+1;
				$("#a"+c).css({
				     '-moz-transform':'scale(2,2)','-webkit-transform':'scale(2,2)','-o-transform':'scale(2,2)','-ms-transform':'scale(2,2)'
				});
				$("#a"+d).css({
				     '-moz-transform':'scale(1,1)','-webkit-transform':'scale(1,1)','-o-transform':'scale(1,1)','-ms-transform':'scale(1,1)'
				});
				$('.mainContainerY').animate({
				   scrollLeft: b
				}, 'slow');
				if(b==0){
					$(".arrow-left1").css("background","url(assets/arrows.png) no-repeat -40px");
				}
			}
			else{
				$(".arrow-left1").css("background","url(assets/arrows.png) no-repeat -40px");
			}
		}
		function goRight2(x){
			var x=x;
			if(e+881<x){
			e = e+880;
			var c=e/880 +1;
			var d=c-1;
			$("#l"+c).css({
			     '-moz-transform':'scale(2,2)','-webkit-transform':'scale(2,2)','-o-transform':'scale(2,2)','-ms-transform':'scale(2,2)'
			});
			$("#l"+d).css({
			     '-moz-transform':'scale(1,1)','-webkit-transform':'scale(1,1)','-o-transform':'scale(1,1)','-ms-transform':'scale(1,1)'
			});
			$(".arrow-left2").css("background","url(assets/arrows.png) no-repeat");
			$('.mainContainerZ').animate({
			   scrollLeft: e
			}, 'slow');
			if(e+881>x)
					$(".arrow-right2").css("background","url(assets/arrows.png) no-repeat -60px");
			}
			else{
				$(".arrow-right2").css("background","url(assets/arrows.png) no-repeat -60px");
			}
		}
		function goLeft2(){
			if(e>0)
			{
				$(".arrow-left2").css("background","url(assets/arrows.png) no-repeat");
				$(".arrow-right2").css("background","url(assets/arrows.png) no-repeat -18px");
				e = e-880;
				var c=e/880 +1;
				var d=c+1;
				$("#l"+c).css({
				     '-moz-transform':'scale(2,2)','-webkit-transform':'scale(2,2)','-o-transform':'scale(2,2)','-ms-transform':'scale(2,2)'
				});
				$("#l"+d).css({
				     '-moz-transform':'scale(1,1)','-webkit-transform':'scale(1,1)','-o-transform':'scale(1,1)','-ms-transform':'scale(1,1)'
				});
				$('.mainContainerZ').animate({
				   scrollLeft: e
				}, 'slow');
				if(e==0){
					$(".arrow-left2").css("background","url(assets/arrows.png) no-repeat -40px");
				}
			}
			else{
				$(".arrow-left2").css("background","url(assets/arrows.png) no-repeat -40px");
			}
		}
		
		function goRight3(x){
		
			var x=x;
			if(e1+881<x){
			e1 = e1+880;
			var c=e1/880 +1;
			var d=c-1;
			$("#3"+c).css({
			     '-moz-transform':'scale(2,2)','-webkit-transform':'scale(2,2)','-o-transform':'scale(2,2)','-ms-transform':'scale(2,2)'
			});
			$("#3"+d).css({
			     '-moz-transform':'scale(1,1)','-webkit-transform':'scale(1,1)','-o-transform':'scale(1,1)','-ms-transform':'scale(1,1)'
			});
			$(".arrow-leftin3").css("display","none");
			
			$('.mainContainerV').animate({
			   scrollLeft: e1
			}, 'slow');
			}
			else{
				$(".arrow-rightin3").css("display","block");
			}
		}
		function goLeft3(){
			if(e1>0)
			{
				$(".arrow-leftin3").css("display","none");
				$(".arrow-rightin3").css("display","none");
				e1 = e1-880;
				var c=e1/880 +1;
				var d=c+1;
				$("#3"+c).css({
				     '-moz-transform':'scale(2,2)','-webkit-transform':'scale(2,2)','-o-transform':'scale(2,2)','-ms-transform':'scale(2,2)'
				});
				$("#3"+d).css({
				     '-moz-transform':'scale(1,1)','-webkit-transform':'scale(1,1)','-o-transform':'scale(1,1)','-ms-transform':'scale(1,1)'
				});
				$('.mainContainerV').animate({
				   scrollLeft: e1
				}, 'slow');
				if(e1==0){
					$(".arrow-leftin3").css("display","block");
				}
			}
			else{
				$(".arrow-leftin3").css("display","block");
			}
		}
		
		function hideBar(){
			if (infoClick==0){
				/*$("#hideShowBar").text("+");*/
				$('#info_bar').animate({'height':'75px'},600);
				$('#topic').animate({'height':'50px'},600);
				$('#clickText').animate({'margin-top':'1px'},600);
				$('#new').animate({'margin-top':'12px'},600);
				$('.icon_text1').animate({'margin-top':'3px'},600);
				$('.Name').hide();
				$('.class').hide();
				var a= window.innerHeight -130 -45;
				if(androidVersionCheck==1){
				$('#activitiesContainer').animate({'height':'auto'},600);
				}
				else{
					$('#activitiesContainer').animate({'height':a},600);
				}
				infoClick=1;
			}
			else if(infoClick==1){
				/*$("#hideShowBar").text("-");*/
				$('#info_bar').animate({'height':'120px'},600);
				$('#topic').animate({'height':'95px'},600);
				$('#new').animate({'margin-top':'58px'},600);
				$('#clickText').animate({'margin-top':'10px'},600);
				$('.icon_text1').animate({'margin-top':'15px'},600);
				$('.Name').show();
				$('.class').show();
				var a= window.innerHeight - (80+ 140 );
				if(androidVersionCheck==1){
				$('#activitiesContainer').animate({'height':'auto'},600);
				}
				else{
					$('#activitiesContainer').animate({'height':a},600);
				}
				infoClick=0;
			}
			if(androidVersionCheck==1){
				$('#activitiesContainer').css("height","auto");
			}
		}
		function attempt(id){
			
		}
	</script>
	<?php } else if($theme==3) { ?>
	    <link href="css/commonHigherClass.css" rel="stylesheet" type="text/css">
		<link href="css/activity/higherClass.css" rel="stylesheet" type="text/css">
		<script>
		function load(){
			var b= window.innerHeight - (200);
			var a= window.innerHeight - (170);
			$('#notattemptedC').css({"height":b+"px"});
			$('#main_bar').css({"height":a+"px"});
			$('#menu_bar').css({"height":a+"px"});
			$('#sideBar').css({"height":a+"px"});
			if(androidVersionCheck==1){
				$('#notattemptedC').css("height","auto");
				$('#main_bar').css("height",$('#notattemptedC').css("height"));
				$('#menu_bar').css("height",$('#notattemptedC').css("height"));
				$('#sideBar').css("height",$('#notattemptedC').css("height"));
			}
		}
		function attempt(id){
			$(".locked,.attempted,.notAttempted").hide();				
			if(id=="A1"){
				$('#aM').attr("class","pointed1");
				$('#naM').attr("class","pointed2");
				$('#A1').css("color","#2F99CB");
				$('#NA1').css("color","black");
				$('#videos').css("color","black");
				$(".attempted").css("display","inline-block");
				$(".attemptedMessage").css("display","block");
				$("#noVideos").css("display","none");
				load();
			}
			else if(id=="NA1"){
				$('#aM').attr("class","pointed2");
				$('#naM').attr("class","pointed1");
				$('#NA1').css("color","#2F99CB");
				$('#A1').css("color","black");
				$('#videos').css("color","black");
				$(".notAttempted").css("display","inline-block");
				$(".notAttemptedMessage").css("display","block");
				$("#noVideos").css("display","none");
			}
			if(id=="report"){
				$(".locked").css("display","inline-block");
				$('#NA1').css("color","black");
				$('#A1').css("color","black");
				$('#videos').css("color","black");
				$(".lockedMessage").css("display","block");
				$(".lockedMessage").css("margin-top","100px");
				$("#noVideos").css("display","none");
			}
			if(id=="videos"){
				$('#videos').css("color","#2F99CB");
				$('#A1').css("color","black");
				$('#NA1').css("color","black");
				$(".videosContainer").css("display","inline-block");
				
				$("#noVideos").css("display","block");
				/*$(".attemptedMessage").css("display","block");*/
				load();
			}
		}
	</script>
	<?php } ?>
<!--	<script type="text/javascript" src="libs/i18next.js"></script>
	<script type="text/javascript" src="libs/translation.js"></script>
	<script src="libs/closeDetection.js"></script>-->
       <script type="text/javascript" src="/mindspark/userInterface/libs/combined.js"></script>
    <script>
	var langType	=	'<?=$language?>';
	var click=0;
    function showActivity(id)
	{
		document.getElementById('gameID').value = id;
		setTryingToUnload();
		document.getElementById('frmActivitySelection').submit();
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
	$(document).ready(function(e) {
			var closedImage1=0;
	    	$(".locked").click(function() {
				if($(this).find(".closedImage").css("display")=="block" && closedImage1==1){
					$(this).find(".activitiesImage").removeClass("semitransparent");
					$(this).find(".lockedImg").hide();
					$(this).find(".lockedText").hide();
					$(this).find(".closedImage").hide();
					$(this).find(".lockedActivity").hide();
					$(this).find(".activitiesName").show();
					$(this).find(".topicName").show();
					closedImage1=0;
				}else{
					$(this).find(".activitiesImage").addClass("semitransparent");
					$(this).find(".lockedImg").show();
					$(this).find(".lockedText").show();
					$(this).find(".closedImage").show();
					$(this).find(".lockedActivity").show();
					// $(this).find(".activitiesName").hide();
					// $(this).find(".topicName").hide();
					closedImage1=0;
				}
			});
			$(".closedImage").click(function() {
				closedImage1=1;
			});
			attempt("NA1");
	    });
		function playVideo(videoPath)
		{
			
			var obj;
			var vidSource1,vidSource2,counter=0;
			var myNode = document.getElementById("showVideo");
			$('#source').remove();
			
			
			obj = document.getElementById('video');
			
			/*source1 = document.createElement('source');
			$(source1).attr('id','source')
			$(source1).attr('type', 'video/mp4');
			$(source1).attr('src', videoPath+'.mp4');*/
			
			vidSource1 = document.getElementById('source1');
			$(vidSource1).attr('src', videoPath+'.mp4');
			
			vidSource2 = document.getElementById('source2');
			$(vidSource2).attr('src', videoPath+'.ogg');
			
			$("#showVideo").append(obj);
			$(obj).append(vidSource1);
			$(obj).append(vidSource2);
			
			var sources = document.getElementsByTagName('source'),i;
			
			for (i = 0; i < sources.length; i++) {
			    (function (i) {
						
						if (document.addEventListener) {
						sources[i].addEventListener('error', function (e) {
			            /*alert('Error loading: '+e.target.src);*/
						alert('Error loading the video');
						window.org_alert = window.alert;
						window.alert = function() {};
						location.reload(); 
						});
						}
						else if (document.attachEvent) {
						sources[i].attachEvent('onerror', function (e) {
			            /*alert('Error loading: '+e.target.src);*/
						alert('Error loading the video');
						window.org_alert = window.alert;
						window.alert = function() {};
						location.reload(); 
						});
						}
						
			        
					
					
			    }(i));
			}
				
			$.fn.colorbox({'href':'#showVideo','inline':true,'open':true,'escKey':false,'overlayClose':false, 'height':550, 'width':700});
			
			/*window.alert = window.org_alert;*/
			
			/*	$( document ).ready(function() {
				obj.addEventListener("error", function (err) {
                    $('#errorMsg').html("Hello");
                }, true);
			});*/
			
		}
			
		
		function logVideoCount(videoID)
		{
			$.ajax('controller.php?mode=videoViewCount&videoID=' + videoID,
                    {
                        method: 'get',
                        success: function (transport) {
                    		
                        }
                    }
                    );
					
					
                    
		}
    </script>
</head>
<body onLoad="load();" onResize="load();" class="translation">
	<div id="top_bar">
		<div class="logo">
		</div>
		<div id="studentInfoLowerClass" class="forHighestOnly">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?=$Name?>&nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a href='myDetailsPage.php' onClick="javascript:setTryingToUnload();"><span data-i18n="homePage.myDetails"></span></a></li>
								<!--	<li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li> -->
									<li><a href='changePassword.php' onClick="javascript:setTryingToUnload();"><span data-i18n="homePage.changePassword"></span></a></li>
                                    <li><a href='whatsNew.php' onClick="javascript:setTryingToUnload();"><span data-i18n="common.whatsNew"></span></a></li>
                                    <li><a href='logout.php' onClick="javascript:setTryingToUnload();"><span data-i18n="common.logout"></span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?=$childClass.$childSection?></span></div>
            </div>
        </div>
		<div id="logout" onClick="logoff();" class="hidden">
        	<div class="logout"></div>
        	<div class="logoutText" data-i18n="common.logout"></div>		
        </div>
    </div>
	
	<div id="container">
		<div id="info_bar" class="hidden">
			<div id="lowerClassProgress">
				<div id="home" class="linkPointer" onClick="getHome()"></div>
				<div class="icon_text2"> - <span class="textUppercase" data-i18n="homePage.activity"></span></font></div>
				<div style='clear:both;'></div>
				<?php if(($childClass == 2 || $childClass == 3) && strcasecmp($subcategory,"School")!=0) { ?>
					<!-- <span><div style='color: #0099ff;font-size: 1.3em;margin-left: 183px;margin-top: -37px;text-align: center;'><a href='practisetest.php'>Practice Zone</a></div></span> -->
				<?php } ?>
			</div>
			<div id="topic">
					<div class="icon_text1"><span id='txtHome' onClick="getHome()" class="textUppercase linkPointer" data-i18n="dashboardPage.home"></span>  &#10140; <font color="#606062"> <span class="textUppercase" data-i18n="homePage.activity"></span></font></div>
					<!-- <div id="clickText" data-i18n="activityPage.startText" class="hidden forHigherOnly"></div> -->
			</div>
			<div class="class">
				<strong><span id="classText" data-i18n="common.class"></span> </strong> <?=$childClass.$childSection?>
			</div>
			<div class="Name">
				<strong><?=$Name?></strong>
			</div>
			<div id="locked" onClick="attempt(id)" class="forLowerOnly">
				<div class="icon_text" id="lck" data-i18n="activityPage.locked"></div>
				<div id="pointed" class="lckPoited">
				</div>
			</div>
			<div id="new">
				<div class="icon_text" data-i18n="activityPage.new"></div>
				<div id="pointed">
				</div>
			</div>
		</div>
		<!-- <div id="hideShowBar" class="forHigherOnly hidden" onClick="hideBar();">-</div> -->
		<div id="info_bar" class="forHighestOnly">
				<div id="dashboard">
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase" data-i18n="homePage.activity"></span></div>
                </div>
				<div class="arrow-right"></div>
				<div class="clear"></div>
		</div>
		<div id="main_bar" class="forHighestOnly">
			<div id="drawer1">
			<a href="dashboard.php" onclick="javascript:setTryingToUnload();" style="text-decoration:none;color:inherit"> 
			<div id="drawer1Icon"></div>
			DASHBOARD
			</div></a>
			<a href="examCorner.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;color:inherit"><div id="drawer2"><div id="drawer2Icon"></div>EXAM CORNER
			</div></a>
			<a href="home.php" onClick="javascript:setTryingToUnload();"><div id="drawer3"><div id="drawer3Icon"></div>HOME
			</div></a>
			<a href="explore.php" onClick="javascript:setTryingToUnload();"><div id="drawer4"><div id="drawer4Icon"></div>EXPLORE ZONE
			</div></a>
			<div id="plus" onClick="openMainBar();">
				<div id="vertical"></div>
				<div id="horizontal"></div>
			</div>
			<a href="src/rewards/rewardsDashboard.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;"><div id="drawer5"><div id="drawer5Icon" <?php if($_SESSION['rewardSystem']!=1) { echo "style='position: absolute;background: url(\"assets/higherClass/dashboard/rewards.png\") no-repeat 0 0 !important;width: 50px;height: 50px;margin-top: -30px;margin-left: -50px;'";} ?> class="<?=$sparkieImage?>"><div class="redCircle"><?=$sparkieWon?></div></div>
			REWARDS CENTRAL
			</div></a>
			<!--<a href="viewComments.php?from=links&mode=1" onClick="javascript:setTryingToUnload();"><div id="drawer6"><div id="drawer6Icon"></div>NOTIFICATIONS
			</div></a>-->
		</div>
<form name="frmActivitySelection" id="frmActivitySelection" method="POST" action="enrichmentModule.php">
	<div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
				<div id="report" onClick="attempt(id)">
					<span id="reportText">LOCKED</span>
					<div id="reportIcon" class="circle"><div class="arrow-s"></div></div>
				</div>
				<div class="empty">
				</div>
				<div id="NA1" onClick="attempt(id);">
			        <span id="classText" data-i18n="activityPage.notAttempted"></span>
			        <div id="naM" class="pointed1">
			        </div>
			    </div>
				<div class="empty">
				</div>
			    <div id="A1" onClick="attempt(id);">
			        <span id="classText" data-i18n="activityPage.attempted"></span>
			        <div id="aM" class="pointed2">
			        </div>
			    </div>
				<?php
					if($_SESSION["videoSystem"] == 1) { ?>
				<div class="empty">
				</div>
				
			    <div id="videos" onClick="attempt(id);">
			        <span id="classText" data-i18n="activityPage.video"></span>
					
					
			        <div id="videosM" class="pointed2">
			        </div>
					
					
			    </div>
				<?php } ?>
			</div>
			</div>
			<?php if(count($arrActivities)>0) { 
		?>
		<div id="blankWhiteSpace"><div id="clickText" data-i18n="activityPage.startText" class="hidden forLowerOnly"></div></div>
		
        <? } ?>
	<div id="activitiesContainer">
		<div id="notattemptedC">
		<div class="mainContainer">
		<div class="boxHeader hidden">
		<div class="notAttemptedText"> Not Attempted</div>
		<div class="paging">
			<?php
			foreach ($arrActivities as $id=>$arrDetails)
			{				
				if($arrDetails["locked"] == 0 && !in_array($id,array_intersect($allGamesAttempted,array_keys($arrActivities))))
				{
					$checkCounter++;
				}
			}
			for($i=ceil($checkCounter/4)+1;$i>1;$i--)
			{
				if($totalTimeSpentThisWeek < $totalTimeAllowedForAcitivitesInaWeek){
				?>
				<div id="na<?=$i-1?>" class="circle"><?=$i-1?></div>
				<?php 
				} 
			} ?>
		</div>
		<div class="arrow-right" onClick="goRight(<?=ceil($checkCounter/4)*4*220?>);"></div>
		<div class="arrow-left" onClick="goLeft();"></div>
		</div>
		<div class="mainContainerX">
		<div id="largeContainer">
        <?php 
			foreach ($arrActivities as $id=>$arrDetails)
			{				
				if($arrDetails["locked"] == 0 && !in_array($id,array_intersect($allGamesAttempted,array_keys($arrActivities))))
				{
					$notAttemptedActivityCounter++;
				?>
                    <div class="smallContainer notAttempted notAttemptedCounterClass" onClick="showActivity(<?=$id?>)">
                        <div class="activitiesImage <?php if($arrDetails['type']=='multiplayer') echo 'multiplayerGame';?>" style="background: url('<?php echo getThumbnailPathOfGame($id,$arrDetails["type"])?>');background-size: 100% 100%;">
							<?php
								if(in_array($id,$newGames))
								{
								?>
                            		<div class="notificationTop"></div>
								<?php 
							 	} 
							?>
                        </div>
                        <div class="activitiesName" title="<?=$arrDetails["desc"]?>"><?=$arrDetails["desc"]?></div>
                        <div class="topicName" title="<?=$arrDetails["TTName"]?>"><?php if($arrDetails["TTName"] != "") echo $arrDetails["TTName"]?></div>
                    </div>
                <?php } 
			} ?>
			<?php
			if($totalTimeSpentThisWeek > $totalTimeAllowedForAcitivitesInaWeek){ ?>
				<div class="notAttempted notAttemptedMessage">Your activity limit per week is 60 minutes which is exhausted. Kindly check your Locked activity list to view details</div>
			<?php } else if($notAttemptedActivityCounter == 0){ ?>
				<div class="notAttempted notAttemptedMessage" style=<?php if($theme==2) echo "margin-left:25%;" ?>>No Activities found here. Please check Attempted or Locked List.</div>	
			<?php } ?>
			<?php if($theme==2 || $theme==1){
				for($j=0;$j<4;$j++)
				{
				?>
                    <div class="smallContainer notAttempted hidden">
                        <div class="activitiesImage1 hidden">
                        </div>
                        <div class="activitiesName hidden"></div>
                        <div class="topicName hidden"></div>
                    </div><?php  
			}} ?>
            </div>
			</div>
			</div>
			<div class="mainContainer1">
			<div class="boxHeader hidden">
				<div class="notAttemptedText">Attempted</div>
				<div class="paging">
				<?php
					for($i=ceil((count(array_intersect($allGamesAttempted,array_keys($arrActivities)))-count(array_intersect($allGamesAttempted,$lockedActivities)))/4);$i>0;$i--)
					{
					if($totalTimeSpentThisWeek < $totalTimeAllowedForAcitivitesInaWeek){
					?>
				<div id="a<?=$i?>" class="circle"><?=$i?></div><?php 
				} } ?>
				
				</div>
			</div>
			<div class="arrow-right1" onClick="goRight1(<?=ceil((count(array_intersect($allGamesAttempted,array_keys($arrActivities)))-count(array_intersect($allGamesAttempted,$lockedActivities)))/4)*4*220?>);"></div>
			<div class="arrow-left1" onClick="goLeft1();"></div>
			<div class="mainContainerY">
			<div id="largeContainer1">
            
        <?php if(count(array_intersect($allGamesAttempted,array_keys($arrActivities))) != 0) { ?>
                    <?php 
                    foreach ($arrActivities as $id=>$arrDetails)
                    {
                    	$attemptedActivityCounter++;                        
                        if($arrDetails["locked"] == 0 && in_array($id,$allGamesAttempted))
                        {
                    ?>
                       <div class="smallContainer attempted attemptedCounterClass" onClick="showActivity(<?=$id?>)">
                            <div class="activitiesImage <?php if($arrDetails['type']=='multiplayer') echo 'multiplayerGame';?>" style="background: url('<?php echo getThumbnailPathOfGame($id,$arrDetails["type"])?>');background-size: 100% 100%;">
							<?php
									if(in_array($id,$newGames))
									{
							?>
                            <div class="notificationTop">
                            </div>
							<?php 
							 	} 
							?>
                            </div>
                            <div class="activitiesName" title="<?=$arrDetails["desc"]?>"><?=$arrDetails["desc"]?></div>
                            <div class="topicName" title="<?=$arrDetails["TTName"]?>"><?php if($arrDetails["TTName"] != "") echo $arrDetails["TTName"]?></div>
                        </div>
						<?php
						}
					}
				}
				if($totalTimeSpentThisWeek > $totalTimeAllowedForAcitivitesInaWeek) { 
				 ?>
				<div class="attempted attemptedMessage">Your activity limit per week is 60 minutes which is exhausted. Kindly check your Locked activity list to view details</div>
				<?php } else if(count($allGamesAttempted)==0) { ?>
				<div class="attempted attemptedMessage"  style=<?php if($theme==2) echo "margin-left:25%;" ?>>No Activities found here. Please check Not attempted or Locked List.</div>
				<?php } ?>
				<?php if($theme==2 || $theme==1){
			for($j=0;$j<4;$j++)
			{
		?>
                    <div class="smallContainer attempted hidden">
                        <div class="activitiesImage1 hidden">
                        </div>
                        <div class="activitiesName hidden"></div>
                        <div class="topicName hidden"></div>
                    </div><?php  
			}} ?>
                </div>
				</div>
				</div>
				<div class="mainContainer2">
			<div class="boxHeader hidden">
				<div class="notAttemptedText">Locked</div>
				<div class="paging">
				<?php
					for($i=ceil(count($lockedActivities)/4);$i>0;$i--)
					{
					?>
				<div id="l<?=$i?>" class="circle"><?=$i?></div><?php 
				}  ?>
				
				</div>
			</div>
			<div class="arrow-right2" onClick="goRight2(<?=count($lockedActivities)*220?>);"></div>
			<div class="arrow-left2" onClick="goLeft2();"></div>
			<div class="mainContainerZ">
			<div id="largeContainer2">
		<?php if(count($lockedActivities) != 0) { 
                    foreach ($arrActivities as $id=>$arrDetails)
                    {
                    	$lockedActivityCounter++;
                        if($arrDetails["locked"] != 0)
                        {
                    ?>
                       <div class="smallContainer locked lockedCounterClass">
					   		<div class="closedImage"></div>
                            <div class="activitiesImage <?php if($arrDetails['type']=='multiplayer') echo 'multiplayerGame';?>" style="background: url('<?php echo getThumbnailPathOfGame($id,$arrDetails["type"])?>');background-size: 100% 100%;">
                                <!--<div class="notificationTop">
                                </div>-->
                            </div>
                            <div class="lockedText"><?=$arrDetails["lockedMsg"]?></div>
                            <div class="lockedActivity"></div>
                            <div class="activitiesName" title="<?=$arrDetails["desc"]?>"><?=$arrDetails["desc"]?></div>
                            <div class="topicName" title="<?=$arrDetails["TTName"]?>"><?php if($arrDetails["TTName"] != "") echo $arrDetails["TTName"]?></div>
                        </div><?php
						}
					}
				} else { ?>
				<div class="locked lockedMessage"  style=<?php if($theme==2) echo "padding-left:20%;padding-right:20%;" ?>>No Activities found here. Please check Not attempted or Attempted List.</div>
				<?php } ?>
					<?php if($theme==2 || $theme==1){
			for($j=0;$j<4;$j++)
			{
		?>
                    <div class="smallContainer locked hidden">
                        <div class="activitiesImage1 hidden">
                            </div>
                            <div class="lockedText"></div>
                            <div class="lockedActivity"></div>
                            <div class="activitiesName"></div>
                            <div class="topicName"></div>
                    </div><?php  
			}} ?>

		</div>
		</div>
		</div>
				
			<!--a-->
			<?php if($_SESSION["videoSystem"] == 1) { ?>
			<div class="mainContainer3">
			<div class="boxHeader hidden">
				<div class="notAttemptedText">Video</div>
				<div class="paging">
				<?php
					for($i=ceil(count($arrVideoDetails)/4);$i>0;$i--)
					{
					?>
				<div id="3<?=$i?>" class="circle"><?=$i?></div><?php 
				}  ?>
				
				</div>
			</div>
			<div class="arrow-right2" onClick="goRight3(<?=count($arrVideoDetails)*220?>);"><div class="arrow-rightin3"></div></div>
			<div class="arrow-left2" onClick="goLeft3();"><div class="arrow-leftin3"></div></div>

			<div class="mainContainerV">
			<div id="largeContainer3">
		<?php if(count($arrVideoDetails) != 0) { 
                    foreach ($arrVideoDetails as $id=>$arrDetails)
                    {
					$videoPath = LOCAL_VIDEO_PATH."Optional Videos"."/".$arrDetails['mappingID']."/".$arrDetails['videoFiles'];
					$thumbPath = LOCAL_VIDEO_PATH."Optional Videos"."/".$arrDetails['mappingID']."/".$arrDetails['thumbs'];
                    ?>
                       <div class="smallContainer videosContainer">
					   		<div class="closedImage"></div>
                            <div class="activitiesImage" style="background: url('<?php echo $thumbPath?>');background-size: 100% 100%;" onClick="playVideo('<?php echo $videoPath?>');">
                            </div>
							
							
                           <div class="lockedText"><?=$arrDetails["lockedMsg"]?></div>
                            <div class="lockedActivity"></div>
                            <div class="activitiesName" title="<?=$arrDetails["videoTitles"]?>"><?=$arrDetails["videoTitles"]?></div>
                            <!--<div class="topicName" title="<?=$arrDetails["TTName"]?>"><?php if($arrDetails["TTName"] != "") echo $arrDetails["TTName"]?></div>-->
                        </div>
						<div style="display:none">
						<div id="showVideo"> 
						<video id="video" width="640" height="440" controls onPlay="logVideoCount(<?=$arrDetails["videoID"]?>);">
						<source id="source1" type="video/mp4">
						<!--<source id="source2" type="video/ogg" />-->
						</video>
						
						<div id="errorMsg"></div>
						</div>
						</div>
						<?php
						
					}
				} else { ?>

				<div id="noVideos" style="font-size: 20px;margin-left: 2%;color: red;display:none;">Sorry no videos found for your school</div>

				<?php } ?>
					<?php if($theme==2 || $theme==1){
			for($j=0;$j<4;$j++)
			{
		?>
                    <div class="smallContainer locked hidden">
                        <div class="activitiesImage1 hidden">
                            </div>
                            <div class="lockedText"></div>
                            <div class="lockedActivity"></div>
                            <div class="activitiesName"></div>
                            <div class="topicName"></div>
                    </div><?php  
			}} ?>

		</div>
		</div>
		</div>
		<?php } ?>
			
			<!--a-->
		
		</div>
	</div>
    <input type="hidden" name='gameID' id="gameID">
</form>		
	</div>
<?php include("footer.php"); ?>