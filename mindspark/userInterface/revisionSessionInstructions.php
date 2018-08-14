<?php
	@include("check1.php");
	include("classes/clsUser.php");
	require_once 'constants.php';
	if(!isset($_SESSION['userID']))
	{
		header("Location:logout.php");
		exit;
	}
	include "functions/functions.php";
	$userID	=	$_SESSION["userID"];
	$objUser = new User($userID);
	$schoolCode		=	$objUser->schoolCode;
	$childName		=	$objUser->childName;
	$childClass		=	$objUser->childClass;
	$childSection	=	$objUser->childSection;
	$childDob		=	$objUser->childDob;
	$Name = explode(" ", $_SESSION['childName']);
	$Name = $Name[0];
	$sessionID 		  = $_SESSION["sessionID"];
	$revisionSessionID= $_POST['revisionSessionID'];
	$qcode            = $_POST['qcode'];
	$sdl              = $_POST['sdl'];
	$clusterCode      = $_POST['clusterCode'];
	$ttCode           = $_POST['ttCode'];
	$qno              = $_POST['qno'];

	function convertToTime($date)
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


	if($revisionSessionID)
	{
		$query = "select count(srno) from adepts_revisionSessionDetails where revisionSessionID =$revisionSessionID and userID=$userID";
		$result = mysql_query($query) or die(mysql_error());
		$line = mysql_fetch_array($result);


		$query = "SELECT DISTINCT a.sessionID, startTime, endTime
			  FROM   adepts_sessionStatus a, adepts_revisionSessionDetails b
		      WHERE  b.userID=".$userID." AND a.userID=b.userID AND a.sessionID=b.sessionID AND revisionSessionID=$revisionSessionID";
		//echo $query."<br/>";
		$time_result = mysql_query($query) or die(mysql_error());
		$timeSpent = 0;
		while ($time_line = mysql_fetch_array($time_result))
		{
			$startTime = convertToTime($time_line[1]);
			if($time_line[2]!="")        {
				$endTime = convertToTime($time_line[2]);
			}
			else
			{
				$query = "SELECT max(lastModified) FROM adepts_revisionSessionDetails WHERE sessionID=".$time_line[0]." AND userID=".$userID;
				$r     = mysql_query($query);
				$l     = mysql_fetch_array($r);
				if($l[0]=="")
					continue;
				else
					$endTime = convertToTime($l[0]);
			}
			
			$timeSpent = $timeSpent + ($endTime - $startTime);        //in secs
		}
		if($timeSpent != 0)
		{
			$timeSpent = $timeSpent/60;
			$timeSpent = 30 - $timeSpent;
		}else{
			$timeSpent = 30;
		}
	}

	if($qno==1)
		$buttonText = "Start Session";
	else
		$buttonText = "Resume Session";
?>

<?php include("header.php"); ?>

<title>Revision Session Instructions</title>
	<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="libs/combined.js"></script>
<!--	<script type="text/javascript" src="libs/i18next.js"></script>
	<script type="text/javascript" src="libs/translation.js"></script>-->
	<script type="text/javascript" src="/mindspark/js/load.js"></script>
<!--	<script src="libs/closeDetection.js"></script>-->
	<script>
	var langType = '<?=$language;?>';
	</script>
	<?php
	if($theme==1) { ?>
	<link href="css/revisionSessionInstructions/lowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<script>
		function load(){
			$('#clickText').html("");
			var a= window.innerHeight -75;
			var b= window.innerHeight -220;
			$('#formContainer').css("height",a);
			$('#container_form').css("height",b);
		}
	</script>
	<?php } else if($theme==2){ ?>
	<link href="css/revisionSessionInstructions/midClass.css" rel="stylesheet" type="text/css">
    <link href="css/commonMidClass.css" rel="stylesheet" type="text/css">
	<script>
		function load(){
			var a= window.innerHeight -220;
			//$('#formContainer').css("height",a);
		}
		
	</script>
	<?php } else { ?>
	<link href="css/commonHigherClass.css" rel="stylesheet" type="text/css">
	<link href="css/revisionSessionInstructions/higherClass.css" rel="stylesheet" type="text/css">
	<script>
		function load(){
			var a= window.innerHeight - (170);
			//$('#formContainer').css({"height":a+"px"});
			$('#menu_bar').css({"height":a+"px"});
			$('#main_bar').css({"height":a+"px"});
			$('#sideBar').css({"height":a+"px"});
		}
	</script>
	<?php } ?>
    <script language="JavaScript" src="libs/gen_validatorv31.js" type="text/javascript"></script>
    <script>
		var click=0;
    	function redirect()
		{
			setTryingToUnload();
			window.location.href	=	"myDetailsPage.php";
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
<body onLoad="load()" onResize="load();" class="translation">
	<div id="top_bar" class="top_bar_part4">
		<div class="logo">
		</div>
		<div id="studentInfoLowerClass" class="forHighestOnly">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?php echo $childName ?>&nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a href='logout.php'><span data-i18n="common.logout"></span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?=$childClass.$childSection?></span></div>
            </div>
        </div>
		<div id="logout" onClick="logoff()" class="hidden">
        	<div class="logout"></div>
        	<div class="logoutText">Logout</div>		
        </div>
    </div>
	
	<div id="container">
		<div id="info_bar" class="hidden">
			<div id="lowerClassProgress">
				<div id="homeIcon" style="cursor: default;"></div>
				<div class="icon_text2">- Revision Session</font></div>
			</div>
			<div id="topic">
				<div id="home" class="linkPointer">
				</div>
				<div class="icon_text1">HOME > <font color="#606062"> Revision Session</font></div>
			</div>
			<div class="class">
				<strong>Class </strong>: <?=$childClass.$childSection?>
			</div>
			<div class="Name">
				<strong><?=$Name?></strong>
			</div>
			<div id="dob" style="visibility:hidden">Date of Birth - <?=$childDob?>
			</div>
		</div>
		<div id="info_bar" class="forHighestOnly">
				<div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase">Revision Session</span></div>
                </div>
				<div class="arrow-right"></div>
				<div class="clear"></div>
		</div>
		
				<div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
			</div>
			</div>
	<div id="formContainer">
	<?php
	if($theme==1) { ?>
		<div id="revisionMessage" style='padding-top:0px;'>
	<?php } else { ?>
	<div id="revisionMessage">
	<?php } ?>
				<b>Welcome <?=$childName?>!</b><br/><br/>
				We hope you are enjoying Mindspark sessions!<br/>
				Today, you will get a Revision Session.
				<br>
				
		</div>
		<form name="frmInstrn" method="POST" action="revisionSessionQuestion.php">
		<br/>
		<div id="revisionInstruction">
		 	<img src="assets/revisionsession.jpg" alt="Revision Session" width="400" height="200">
		 	<div>
			
					<ul type="disc" style="text-align:justify;margin-bottom: 1em">
						<li>You have <?php echo floor($timeSpent); ?> Minutes left for this Revision Session.</li>
						<li>You have answered <?php echo $line[0]; ?> out of <?php echo $_SESSION['questioncount']; ?> Questions yet. </li>
						<li>Answer every question. Click on 'Submit' to submit your answer and move to the next question.<br/></li>
						<li>At the end of the session you will get your report.<br/></li>
					</ul>
			</div>
		</div>

		<div id="revisionInstruction"><input type='submit' class='buttonRevision' value='<?=$buttonText?>' onClick="setTryingToUnload()"></div>

		<input type="hidden" name="qcode" id="qcode" value="<?=$qcode?>">
		<input type="hidden" name="qno" id="qno" value="<?=$qno?>">
		<input type="hidden" name="sdl" id="sdl" value="<?=$sdl?>">
		<input type="hidden" name="clusterCode" id="clusterCode" value="<?=$clusterCode?>">
		<input type="hidden" name="ttCode" id="ttCode" value="<?=$ttCode?>">
		<input type="hidden" name="revisionSessionID" id="revisionSessionID" value="<?=$revisionSessionID?>">
		</form>
		<br/><br/>
	</div>
		
	</div>
    
<?php include("footer.php"); ?>
