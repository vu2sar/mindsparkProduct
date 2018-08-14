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
	$practiseTarr = getpractisetestdetails($childClass);

	$arrTopicsActivated = getTopicActivatedTillDate($userID,$childClass,$childSection, $objUser->category,$objUser->subcategory,$objUser->schoolCode, SUBJECTNO, $objUser->packageType,$showAllTopics,$programMode);

	$flagcheck = 1;

	if(!count($arrTopicsActivated)==0)
	{
		$topicWiseDetails = getTopicWiseDetailsNew($arrTopicsActivated, $userID, $childClass);
	}
	 //echo "<pre>";
     //print_r($topicWiseDetails);
	foreach($topicWiseDetails as $var) 
	{
		if((int)$var[1] != 100)
		{
			$flagcheck = 0; 
			break;
		}
	}

	if(count($topicWiseDetails) == 0)
		$flagcheck = 0;
?>

<?php include("header.php");
?>
<link rel="stylesheet" type="text/css" href="css/colorbox.css">
<title>Practice Test</title>
	<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
	<script src="libs/jquery.colorbox-min.js" type="text/javascript"></script>
	<?php

	if($theme==1) { ?>
	<link href="css/activity/lowerClass.css" rel="stylesheet" type="text/css">
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
			$("#largeContainer").attr("style","width:<?=(count($practiseTarr)+3)*220?>px");
			$("#largeContainer1").attr("style","width:<?=(count($practiseTarr)+3)*220?>px");
			$("#largeContainer2").attr("style","width:<?=(count($practiseTarr)+3)*220?>px");
			$("#largeContainer3").attr("style","width:<?=(count($practiseTarr)+3)*220?>px");
			if(androidVersionCheck==1){
				$('#activitiesContainer').css("height","auto");
			}
		}
		function goRight(x){
			var x=x;
			if(parseInt(a+881)<x){
				a = parseInt(a+880);
				var c=a/880 +1;
				var d=c-1;
				$("#na"+c).css({
				     '-moz-transform':'scale(2,2)',
				     '-webkit-transform':'scale(2,2)',
				     '-o-transform':'scale(2,2)',
				     '-ms-transform':'scale(2,2)'
				});
				$("#na"+d).css({
				     '-moz-transform':'scale(1,1)',
				     '-webkit-transform':'scale(1,1)',
				     '-o-transform':'scale(1,1)',
				     '-ms-transform':'scale(1,1)'
				});
				$(".arrow-leftin").css("display","none");
				$('.mainContainerX').animate({
				   scrollLeft: a
				}, 'slow');
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
				     '-moz-transform':'scale(2,2)',
				     '-webkit-transform':'scale(2,2)',
				     '-o-transform':'scale(2,2)',
				     '-ms-transform':'scale(2,2)'
				});
				$("#na"+d).css({
				     '-moz-transform':'scale(1,1)',
				     '-webkit-transform':'scale(1,1)',
				     '-o-transform':'scale(1,1)',
				     '-ms-transform':'scale(1,1)'
				});
				$('.mainContainerX').animate({
				   scrollLeft: a
				}, 'slow');
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
			     '-moz-transform':'scale(2,2)',
			     '-webkit-transform':'scale(2,2)',
			     '-o-transform':'scale(2,2)',
			     '-ms-transform':'scale(2,2)'
			});
			$("#a"+d).css({
			     '-moz-transform':'scale(1,1)',
			     '-webkit-transform':'scale(1,1)',
			     '-o-transform':'scale(1,1)',
			     '-ms-transform':'scale(1,1)'
			});
			$(".arrow-leftin1").css("display","none");
			$('.mainContainerY').animate({
			   scrollLeft: b
			}, 'slow');
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
				     '-moz-transform':'scale(2,2)',
				     '-webkit-transform':'scale(2,2)',
				     '-o-transform':'scale(2,2)',
				     '-ms-transform':'scale(2,2)'
				});
				$("#a"+d).css({
				     '-moz-transform':'scale(1,1)',
				     '-webkit-transform':'scale(1,1)',
				     '-o-transform':'scale(1,1)',
				     '-ms-transform':'scale(1,1)'
				});
				$('.mainContainerY').animate({
				   scrollLeft: b
				}, 'slow');
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
			//alert("E will be:"+parseInt(e+881)+" and x will be :"+x);
			if(parseInt(e+881)<x){
			e = parseInt(e+880);
			var c=e/880 +1;
			var d=c-1;

			$("#l"+c).css({
			     '-moz-transform':'scale(2,2)',
			     '-webkit-transform':'scale(2,2)',
			     '-o-transform':'scale(2,2)',
			     '-ms-transform':'scale(2,2)'
			});
			$("#l"+d).css({
			     '-moz-transform':'scale(1,1)',
			     '-webkit-transform':'scale(1,1)',
			     '-o-transform':'scale(1,1)',
			     '-ms-transform':'scale(1,1)'
			});
			$(".arrow-leftin2").css("display","none");
			
			$('.mainContainerZ').animate({
			   scrollLeft: e
			}, 'slow');
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
				     '-moz-transform':'scale(2,2)',
				     '-webkit-transform':'scale(2,2)',
				     '-o-transform':'scale(2,2)',
				     '-ms-transform':'scale(2,2)'
				});
				$("#l"+d).css({
				     '-moz-transform':'scale(1,1)',
				     '-webkit-transform':'scale(1,1)',
				     '-o-transform':'scale(1,1)',
				     '-ms-transform':'scale(1,1)'
				});
				$('.mainContainerZ').animate({
				   scrollLeft: e
				}, 'slow');
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
			     '-moz-transform':'scale(2,2)',
			     '-webkit-transform':'scale(2,2)',
			     '-o-transform':'scale(2,2)',
			     '-ms-transform':'scale(2,2)'
			});
			$("#3"+d).css({
			     '-moz-transform':'scale(1,1)',
			     '-webkit-transform':'scale(1,1)',
			     '-o-transform':'scale(1,1)',
			     '-ms-transform':'scale(1,1)'
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
				     '-moz-transform':'scale(2,2)',
				     '-webkit-transform':'scale(2,2)',
				     '-o-transform':'scale(2,2)',
				     '-ms-transform':'scale(2,2)'
				});
				$("#3"+d).css({
				     '-moz-transform':'scale(1,1)',
				     '-webkit-transform':'scale(1,1)',
				     '-o-transform':'scale(1,1)',
				     '-ms-transform':'scale(1,1)'
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
	<?php } else if($theme==2) { ?>
	<link href="css/activity/midClass.css" rel="stylesheet" type="text/css">
    <link href="css/commonMidClass.css" rel="stylesheet" type="text/css">
	<script>
		var infoClick=0;
		var a=0;
		var b=0;
		var e=0;
		var e1=0;
		function load(){
			var a= window.innerHeight -220;
			$('#activitiesContainer').css("height",a);
			hideBar();
			$(".notAttempted").show();
			$("#largeContainer").attr("style","width:<?=(count($practiseTarr)+3)*220?>px");
			$("#largeContainer1").attr("style","width:<?=(count($practiseTarr)+3)*220?>px");
			$("#largeContainer2").attr("style","width:<?=(count($practiseTarr)+3)*220?>px");
			$("#largeContainer3").attr("style","width:<?=(count($practiseTarr)+3)*220?>px");
			if(androidVersionCheck==1){
				$('#activitiesContainer').css("height","auto");
			}
		}
		function goRight(x){
			var x=x;
			if(parseInt(a+881)<x){
				a = parseInt(a+880);
				var c=a/880 +1;
				var d=c-1;
				$("#na"+c).css({
				     '-moz-transform':'scale(2,2)',
				     '-webkit-transform':'scale(2,2)',
				     '-o-transform':'scale(2,2)',
				     '-ms-transform':'scale(2,2)'
				});
				$("#na"+d).css({
				     '-moz-transform':'scale(1,1)',
				     '-webkit-transform':'scale(1,1)',
				     '-o-transform':'scale(1,1)',
				     '-ms-transform':'scale(1,1)'
				});
				$(".arrow-leftin").css("display","none");
				$('.mainContainerX').animate({
				   scrollLeft: a
				}, 'slow');
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
				     '-moz-transform':'scale(2,2)',
				     '-webkit-transform':'scale(2,2)',
				     '-o-transform':'scale(2,2)',
				     '-ms-transform':'scale(2,2)'
				});
				$("#na"+d).css({
				     '-moz-transform':'scale(1,1)',
				     '-webkit-transform':'scale(1,1)',
				     '-o-transform':'scale(1,1)',
				     '-ms-transform':'scale(1,1)'
				});
				$('.mainContainerX').animate({
				   scrollLeft: a
				}, 'slow');
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
			if(parseInt(b+881)<x){
			b = parseInt(b+880);
			var c=b/880 +1;
			var d=c-1;
			$("#a"+c).css({
			     '-moz-transform':'scale(2,2)',
			     '-webkit-transform':'scale(2,2)',
			     '-o-transform':'scale(2,2)',
			     '-ms-transform':'scale(2,2)'
			});
			$("#a"+d).css({
			     '-moz-transform':'scale(1,1)',
			     '-webkit-transform':'scale(1,1)',
			     '-o-transform':'scale(1,1)',
			     '-ms-transform':'scale(1,1)'
			});
			$(".arrow-leftin1").css("display","none");
			$('.mainContainerY').animate({
			   scrollLeft: b
			}, 'slow');
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
				     '-moz-transform':'scale(2,2)',
				     '-webkit-transform':'scale(2,2)',
				     '-o-transform':'scale(2,2)',
				     '-ms-transform':'scale(2,2)'
				});
				$("#a"+d).css({
				     '-moz-transform':'scale(1,1)',
				     '-webkit-transform':'scale(1,1)',
				     '-o-transform':'scale(1,1)',
				     '-ms-transform':'scale(1,1)'
				});
				$('.mainContainerY').animate({
				   scrollLeft: b
				}, 'slow');
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
			if(parseInt(e+881)<x){
			e = parseInt(e+880);
			var c=e/880 +1;
			var d=c-1;
			$("#l"+c).css({
			     '-moz-transform':'scale(2,2)',
			     '-webkit-transform':'scale(2,2)',
			     '-o-transform':'scale(2,2)',
			     '-ms-transform':'scale(2,2)'
			});
			$("#l"+d).css({
			     '-moz-transform':'scale(1,1)',
			     '-webkit-transform':'scale(1,1)',
			     '-o-transform':'scale(1,1)',
			     '-ms-transform':'scale(1,1)'
			});
			$(".arrow-leftin2").css("display","none");
			$('.mainContainerZ').animate({
			   scrollLeft: e
			}, 'slow');
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
				     '-moz-transform':'scale(2,2)',
				     '-webkit-transform':'scale(2,2)',
				     '-o-transform':'scale(2,2)',
				     '-ms-transform':'scale(2,2)'
				});
				$("#l"+d).css({
				     '-moz-transform':'scale(1,1)',
				     '-webkit-transform':'scale(1,1)',
				     '-o-transform':'scale(1,1)',
				     '-ms-transform':'scale(1,1)'
				});
				$('.mainContainerZ').animate({
				   scrollLeft: e
				}, 'slow');
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
			if(parseInt(e1+881)<x){
			e1 = parseInt(e1+880);
			var c=e1/880 +1;
			var d=c-1;
			$("#3"+c).css({
			     '-moz-transform':'scale(2,2)',
			     '-webkit-transform':'scale(2,2)',
			     '-o-transform':'scale(2,2)',
			     '-ms-transform':'scale(2,2)'
			});
			$("#3"+d).css({
			     '-moz-transform':'scale(1,1)',
			     '-webkit-transform':'scale(1,1)',
			     '-o-transform':'scale(1,1)',
			     '-ms-transform':'scale(1,1)'
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
				     '-moz-transform':'scale(2,2)',
				     '-webkit-transform':'scale(2,2)',
				     '-o-transform':'scale(2,2)',
				     '-ms-transform':'scale(2,2)'
				});
				$("#3"+d).css({
				     '-moz-transform':'scale(1,1)',
				     '-webkit-transform':'scale(1,1)',
				     '-o-transform':'scale(1,1)',
				     '-ms-transform':'scale(1,1)'
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
        <script type='text/javascript' src='libs/combined.js'></script>
<!--	<script type="text/javascript" src="libs/i18next.js"></script>
	<script type="text/javascript" src="libs/translation.js"></script>
	<script src="libs/closeDetection.js"></script>-->
    <script>
	var langType	=	'<?=$language?>';
	var click=0;
   

	function generatetest(qcode,practiceid)
	{
		setTryingToUnload();
		document.getElementById("mode").value= "getpractisetest";
		document.getElementById("QCode").value=qcode;
		document.getElementById("practiceid").value=practiceid;
		document.getElementById("quesCategory").value="practiseTest";
		document.getElementById("quesno").value= 1;
		document.getElementById("practisetestform").action="controller.php";
		document.getElementById("practisetestform").submit();
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
	
	$(document).ready(function(e) {
			var closedImage1=0;
	    	$(".locked").click(function() {
				if($(this).find(".closedImage").css("display")=="block" && closedImage1==1){
					$(this).find(".activitiesImage").removeClass("semitransparent");
					$(this).find(".lockedImg").hide();
					$(this).find(".lockedText").hide();
					$(this).find(".closedImage").hide();
					$(this).find("#lockedActivity").hide();
					closedImage1=0;
				}else{
					$(this).find(".activitiesImage").addClass("semitransparent");
					$(this).find(".lockedImg").show();
					$(this).find(".lockedText").show();
					$(this).find(".closedImage").show();
					$(this).find("#lockedActivity").show();
					closedImage1=0;
				}
			});
			$(".closedImage").click(function() {
				closedImage1=1;
			});
			attempt("NA1");
	    });
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
				<div class="icon_text2" style="width:100%"><span class="textUppercase">Practice Zone</span></font></div>
			</div>
			<div id="topic">
					<div class="icon_text1"><span onClick="getHome()" class="textUppercase linkPointer" data-i18n="dashboardPage.home"></span> > <font color="#606062"> <span class="textUppercase" data-i18n="homePage.activity"></span></font></div>
					<div id="clickText" data-i18n="activityPage.startText" class="hidden forHigherOnly"></div>
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
		
<form id="practisetestform" method="post">
	<input type="hidden" name="mode" id="mode" value="nextAction">
	<input type="hidden" name="QCode" id="QCode">
	<input type="hidden" name="quesCategory" id="quesCategory">
	<input type="hidden" name="quesno" id="quesno">
	<input type="hidden" name="practiceid" id="practiceid">
</form>
<form name="frmActivitySelection" id="frmActivitySelection" method="POST" action="enrichmentModule.php">
	<?php 
 //echo "<pre>";
 //echo "Count ".count($practiseTarr);
//print_r($practiseTarr);
//exit;	
		?>
			<?php 
//echo "<pre>";
//print_r($practiseTarr);
			if(count($practiseTarr)>0) { 
		?>
		<div id="blankWhiteSpace"></div>
		
        <?php } ?>
	<div id="activitiesContainer">
		<div id="notattemptedC">
		<div class="mainContainer">
		<div class="boxHeader hidden">
		<div class="notAttemptedText"> Not Attempted</div>
		<div class="paging">

		<?php 


			foreach ($practiseTarr as $id=>$arrDetails)
			{
				
				if($arrDetails["clusterstatus"] == 0)
				{
					$checkCounter++;
				}
			}
			
			for($i=ceil($checkCounter/4)+1;$i>1;$i--)
			{ ?>
		<div id="na<?=$i-1?>" class="circle"><?=$i-1?></div><?php 
		}  ?>
		
		</div>
		<div class="arrow-right" onClick="goRight(<?=ceil($checkCounter/4)*4*220?>);"><div class="arrow-rightin"></div></div>
		<div class="arrow-left" onClick="goLeft();"><div class="arrow-leftin"></div></div>
		</div>
		
		<div class="mainContainerX">
		<div id="largeContainer">
        <?php 
       // $practiseTarr is list of all topics.
			foreach ($practiseTarr as $id=>$arrDetails)
			{
				if($arrDetails["clusterstatus"] == 0)
				{
					 if($flagcheck == 0) { 
		?>			
					<div class="smallContainer">
					<?php } else {?>
                    <div class="smallContainer" onClick="generatetest('<?=$arrDetails['Qcodestr']?>',<?=$arrDetails['practiceid']?>)"> 
					<?php } ?>
                        <div class="activitiesImage" style="background: url('http://mindspark-ei.s3.amazonaws.com/Enrichment_Modules/practiceQuestions/<?php echo $arrDetails['thumbnail'];?>');background-size: 100% 100%;">
							
                        </div>
						
						<?php if($flagcheck == 0) {?>
						<br />
						<br />
						<br />
							<div class="lockedText" style="display: block;"></div>
							<div id="lockedActivity" style="display: block;"></div>
						<?php } ?>

                        <div class="activitiesName" title="<?=$arrDetails["topicName"]?>"><?=$arrDetails["topicName"]?></div>
                       
                    </div><?php 
			 	} 
			} ?>
			
			
            </div>
			</div>
			</div>

			

			<div class="mainContainer2">
			<div class="boxHeader hidden">
				<div class="notAttemptedText">In Progress</div>
				<div class="paging">
				<?php
				$checkCounter = 0;
			foreach ($practiseTarr as $id=>$arrDetails)
			{
				
				if($arrDetails["clusterstatus"] == 1)
				{
					$checkCounter++;
				}
			}
			
			//echo "Counter ".$checkCounter; // Non zero cluster status
			for($i=ceil($checkCounter/4)+1;$i>1;$i--)
			{ ?>
		<div id="l<?=$i-1?>" class="circle"><?=$i-1?></div><?php 
		}  ?>
				
				</div>
			</div>
			<div class="arrow-right1" onClick="goRight2(<?=ceil($checkCounter/4)*4*220?>);"><div class="arrow-rightin2"></div></div>
			<div class="arrow-left1" onClick="goLeft2();"><div class="arrow-leftin2"></div></div>
			<div class="mainContainerZ">
			<div id="largeContainer2">
		 <?php 
			foreach ($practiseTarr as $id=>$arrDetails)
			{
				if($arrDetails["clusterstatus"] == 1)
				{
		?>			
					<?php if($flagcheck == 0) { ?>
					<div class="smallContainer">
					<?php } else { ?>
                    <div class="smallContainer notAttempted" onClick="generatetest('<?=$arrDetails['Qcodestr']?>',<?=$arrDetails['practiceid']?>)">
					<?php } ?>
                        <div class="activitiesImage" style="background: url('https://d2tl1spkm4qpax.cloudfront.net/Enrichment_Modules/practiceQuestions/<?php echo $arrDetails['thumbnail'];?>');background-size: 100% 100%;">
							
                        </div>
						
						<?php if($flagcheck == 0) { ?>
						<br />
						<br />
						<br />
							<div class="lockedText" style="display: block;"></div>
							<div id="lockedActivity" style="display: block;"></div>
						<?php } ?>
                        <div class="activitiesName" title="<?=$arrDetails["topicName"]?>"><?=$arrDetails["topicName"]?></div>
                       
                    </div><?php 
			 	} 
			} 
				 ?>
					

		</div>
		</div>
		</div>


			<div class="mainContainer1">
			<div class="boxHeader hidden">
				<div class="notAttemptedText">Completed</div>
				<div class="paging">
				<?php
				$checkCounter = 0;
			foreach ($practiseTarr as $id=>$arrDetails)
			{
				
				if($arrDetails["clusterstatus"] == 2)
				{
					$checkCounter++;
				}
			}
			
			for($i=ceil($checkCounter/4)+1;$i>1;$i--)
			{ ?>
		<div id="na<?=$i-1?>" class="circle"><?=$i-1?></div><?php 
		}  ?>
				
				</div>
			</div>
			<div class="arrow-right1" onClick="goRight1(<?=ceil($checkCounter/4)*4*220?>);"><div class="arrow-rightin1"></div></div>
			<div class="arrow-left1" onClick="goLeft1();"><div class="arrow-leftin1"></div></div>
			<div class="mainContainerY">
			<div id="largeContainer1">
            
        <?php 
			foreach ($practiseTarr as $id=>$arrDetails)
			{
				if($arrDetails["clusterstatus"] == 2)
				{
					if($flagcheck == 0) {
		?>
					<div class="smallContainer notAttempted">
					<?php } else { ?>
                    <div class="smallContainer notAttempted" onClick="generatetest('<?=$arrDetails['Qcodestr']?>',<?=$arrDetails['practiceid']?>)">
					<?php } ?>
                        <div class="activitiesImage" style="background: url('https://d2tl1spkm4qpax.cloudfront.net/Enrichment_Modules/practiceQuestions/<?php echo $arrDetails['thumbnail'];?>');background-size: 100% 100%;">
							
                        </div>
						
						<?php if($flagcheck == 0) { ?>
						<br />
						<br />
						<br />
							<div class="lockedText" style="display: block;"></div>
							<div id="lockedActivity" style="display: block;"></div>
						<?php } ?>
                        <div class="activitiesName" title="<?=$arrDetails["topicName"]?>"><?=$arrDetails["topicName"]?></div>
                       
                    </div><?php 
			 	} 
			} 
				 ?>
				
                </div>
				</div>
				</div>
				
		</div>
	</div>
</form>		
	</div>
<?php include("footer.php"); ?>