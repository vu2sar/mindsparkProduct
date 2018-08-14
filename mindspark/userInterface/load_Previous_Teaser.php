<?php
	//Trick names taken form database are stored in this array.
	global $trick_names;
	global $j;
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);

	@include_once("check1.php");
	include("classes/clsUser.php");
	include("constants.php");
	include("functions/functions.php");
	include_once("classes/clsTopicProgress.php");
	$sparkieImage = $_SESSION['sparkieImage'];
	if(!isset($_SESSION['userID']))
	{
		header("Location:logout.php");
		exit;
	}
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
	$schoolCode    = $objUser->schoolCode;
	$arrActivities = getActivities($childClass,"",$objUser->packageType);
	$today = date("Y-m-d");

	$dateToday = $_POST['teaserID'];


	$sql = "SELECT `Teaser_Description`,`Teaser_answer`,`Teaser_No`,`descriptionImage`,`answerImage` FROM `adepts_exploreTeasers` WHERE `Teaser_Date` = '$dateToday'" ;
	$result = mysql_query($sql) or die(mysql_error());
	$j = 0;
	while($row = mysql_fetch_array($result))
	{

		$TeaserDescription[$j] = $row['Teaser_Description'];
		$TeaserAns[$j] = $row['Teaser_answer'];
		$TeaserNo[$j] = $row['Teaser_No'];
		$description_Image[$j] = $row['descriptionImage'];
		$answer_Image[$j]=$row['answerImage'];


 		$j++;
	}

	$ans = $TeaserNo[0];
	if($ans == null)
	{
		echo '<script type="text/javascript">alert("' ."Teaser for this date does not exist". '"); </script>';
		echo '<script type="text/javascript">window.history.back(); </script>';
		exit();
	}


	$sql = "SELECT `Teaser_No` FROM `adepts_exploreTeaserStudent`" ;
	$result = mysql_query($sql) or die(mysql_error());
	$j=0;
	while($row = mysql_fetch_array($result))
	{
		$TNo[$j] = $row['Teaser_No'];
		$j++;
	}

	$sql = "SELECT `userResponse` FROM `adepts_exploreTeaserStudent` WHERE userID='$userID' and Teaser_No='$ans'" ;
	$result = mysql_query($sql) or die(mysql_error());
	$j=0;
	while($row = mysql_fetch_array($result))
	{
		$resp[$j] = $row['userResponse'];
		$j++;
	}

	if (in_array($ans, $TNo))
	{

		$sql =  "UPDATE `adepts_exploreTeaserStudent` set `isSolution`='1' WHERE `userID` = '$userID' and `Teaser_No` = '$ans' " ;
	mysql_query($sql) or die(mysql_error());
	}

	// Time
	
	date_default_timezone_set('Asia/Kolkata');
	$today = getdate();
	$day = $today["wday"];
	
	
	
	if($day == 1 || $day == 2 || $day == 3)
	{
		$set_date = date('Y-m-d', strtotime($dateToday .' +4 day'));
	}
	if($day == 4 || $day == 5 || $day == 6)
	{
		$set_date = date('Y-m-d', strtotime($dateToday .' +3 day'));
	}
	

?>

<?php include("header.php");?>

<title>Explore Zone</title>
	<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>

	<?php if($theme==3) { ?>
	    <link href="css/commonHigherClass.css" rel="stylesheet" type="text/css">
		<link href="css/explorezone/higherClass.css" rel="stylesheet" type="text/css">


		<script type="text/javascript">

				var d = new Date();
				var d1 = d.getDate();
				var d2 = d.getDay();



		function foo()
		{
				$('.t1').css("color","black");
				$('.t2').css("color","black");
				$('.t3').css("color","#885E9E");

				var container1 = document.getElementById ("mtrickss");


			 var a = $('#mtrickss').height();
			 var b = container1.scrollHeight;
			 var c = b + 10 ;
			 if(b > a)
			 {

			 var message = "The height of the contents without padding: " + $('#mtrickss').height() + "px.\n";
   			 message += "The height of the contents with padding: " + container1.scrollHeight + "px.\n";



			 	$('#mtrickss').css({"overflow":+"hidden"});


				$('#mtrickss').css({"height":700+"px"});
				$('#main_bar').css({"height":710+"px"});
				$('#sideBar').css({"height":710+"px"});
				$('#container').css({"overflow":+"auto"});

			 }
		}

		function previous_answer()
		{
				document.getElementById('teaserID').value = document.getElementById('dates').value ;
				setTryingToUnload();
				document.getElementById('mtrickss').submit();


		}

		function saveAnswer()
		{

			 var a = document.getElementById('teaser_answer').value;
			 var b = document.getElementById('Dummy').value;
			 var c = a+"-"+b;

			 document.getElementById('ans1').value = c;
			 setTryingToUnload();
			 document.getElementById('save_answer').submit();
		}

		
		function trick_topic_load(idd)
		{

			document.getElementById('topicID').value = idd;
			setTryingToUnload();
			document.getElementById('mtrickss').submit();

		}

		function explore_more_tricks()
		{
			setTryingToUnload();
			window.location	 = "explore_more_tricks.php";
			value=0;
		}
		function load(){

			var b= window.innerHeight - (190);
			var a= window.innerHeight - (185);
			$('#mtrickss').css({"height":b+"px"});
			$('#main_bar').css({"height":a+"px"});
			$('#menu_bar').css({"height":a+"px"});
			$('#sideBar').css({"height":a+"px"});
			if(androidVersionCheck==1){
				$('#mtrickss').css("height","auto");
				$('#main_bar').css("height",$('#mtrickss').css("height"));
				$('#menu_bar').css("height",$('#mtrickss').css("height"));
				$('#sideBar').css("height",$('#mtricks').css("height"));
			}

		}



		function attempt(id){

			if(id=="Shortcuts")
			{



				$('.t1').css("color","#885E9E");
				$('.t2').css("color","black");
				$('.t3').css("color","black");
				$('.t4').css("color","black");
				setTryingToUnload();
				window.location = "explore.php";

			}
			
			if(id=="Back")
			{
				$('.t1').css("color","black");
				$('.t2').css("color","black");
				$('.t3').css("color","black");
				$('.t4').css("color","#885E9E");
				setTryingToUnload();
				window.history.back();

			}
			if(id=="Games")
			{


				$('.t1').css("color","black");
				$('.t2').css("color","#885E9E");
				$('.t3').css("color","black");
				$('.t4').css("color","black");

			}
			if(id=="Teasers")
			{

				$('.t1').css("color","black");
				$('.t2').css("color","black");
				$('.t3').css("color","#885E9E");
				$('.t4').css("color","black");

				/*alert( document.getElementById('days').value);
				alert( document.getElementById('dates').value);*/
				
				var d1 = document.getElementById('days').value ;
				var d2 = document.getElementById('dates').value;
				
				if(d1==1 || d1==2 || d1==3)
				{
					document.getElementById('datem').value = d2;
					setTryingToUnload();
					document.getElementById('send_mdate').submit();
				}
				else
				{
					document.getElementById('datet').value = d2;
					setTryingToUnload();
					document.getElementById('send_tdate').submit();
				}

			}


		}




	</script>
	<?php } ?>
<!--	<script type="text/javascript" src="libs/i18next.js"></script>
	<script type="text/javascript" src="libs/translation.js"></script>-->
<script type='text/javascript' src='libs/combined.js'></script>
	<link rel="stylesheet" type="text/css" href="css/CalendarControl.css" >
<script type="text/javascript" src="libs/CalendarControl.js" language="javascript"></script>
<script type="text/javascript" src="libs/calendarDateInput.js" language="javascript"></script>
<script type="text/javascript" src="libs/dateValidator.js"></script>
<!--<script type="text/javascript" src="libs/closeDetection.js"></script>-->

	<script language="javascript">





	</script>

    <script>
	var langType	=	'<?=$language?>';
	var click=0;


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

    </script>
</head>
<body  class="translation" onLoad="foo()" onResize="load()">
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
                                    <li><a href='myDetailsPage.php'><span data-i18n="homePage.myDetails"></span></a></li>
								<!--	<li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li> -->
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
			</div>
			<div id="topic">
					<div class="icon_text1"><span onClick="getHome()" class="textUppercase linkPointer" data-i18n="dashboardPage.home"></span> > <font color="#606062"> <span class="textUppercase" data-i18n="homePage.activity"></span></font></div>
					<div id="clickText" data-i18n="activityPage.startText" class="hidden forHigherOnly"></div>
			</div>
			<div class="class">
				<strong><span id="classText" data-i18n="common.class"></span> </strong>: <?=$childClass.$childSection?>
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
		<div id="hideShowBar" class="forHigherOnly hidden" onClick="hideBar();">-</div>
		<div id="info_bar" class="forHighestOnly">
				<div id="dashboard">
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText">
					<a href="explore.php">
					<span class="textUppercase" data-i18n="homePage.explore"></span>
					</a>
					</div>
                </div>
				<div class="arrow-right"></div>
				<div class="clear"></div>
		</div>
		<div id="main_bar" class="forHighestOnly">
			<div id="drawer1">
			<a href="dashboard.php" style="text-decoration:none;color:inherit">
			<div id="drawer1Icon"></div>
			DASHBOARD
			</div></a>
			<a href="examCorner.php" style="text-decoration:none;color:inherit"><div id="drawer2"><div id="drawer2Icon"></div>EXAM CORNER
			</div></a>
			<a href="home.php"><div id="drawer3"><div id="drawer3Icon"></div>HOME
			</div></a>
			<a href="activity.php"><div id="drawer4"><div id="drawer4Icon"></div>ACTIVITIES
			</div></a>
			<div id="plus" onClick="openMainBar();">
				<div id="vertical"></div>
				<div id="horizontal"></div>
			</div>
			<a href="src/rewards/rewardsDashboard.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;">
			<div id="drawer5"><div id="drawer5Icon" style='<?php if($_SESSION['rewardSystem']!=1) { echo 'position: absolute;background: url("assets/higherClass/dashboard/rewards.png") no-repeat 0 0 !important;width: 50px;height: 50px;margin-top: -30px;margin-left: -50px;";';} ?>' class="<?=$sparkieImage?>"><div class="redCircle"><?=$sparkieWon?></div></div>REWARDS CENTRAL</div></a>
			<!--<a href="viewComments.php?from=links&mode=1"><div id="drawer6"><div id="drawer6Icon"></div>NOTIFICATIONS
			</div></a>-->
		</div>


	<div id="menuBar" class="forHighestOnly">
			<div id="sideBar" style="height:510px;">

				<div class="empty1">
				</div>
				<div id="Shortcuts" onclick=attempt(id)>
			        <span id="classText" class="t1"  data-i18n="explorepage.shortcuts" ></span>
			        <div id="naM" class="pointed1">
			        </div>
			    </div>
				<div class="empty1">
				</div>

			    <div id="Teasers" onclick=attempt(id)>
			        <span id="classText" class="t3" data-i18n="explorepage.teasers" ></span>
			        <div id="aM" class="pointed2">
			        </div>
			    </div>
				
				<div class="empty1">
				</div>
			   
			    <div id="Back" onclick=attempt(id) style="padding-top: 5px;">
			        <span id="classText" class="t4" data-i18n="Go Back" ></span>
			        <div id="aM" class="pointed2">
			        </div>
			    </div>
				<form id="send_mdate" action="brainTeaser.php" style="display:none" method="POST">
				<input type="hidden" name='datem' id="datem"> </input>
				</form>
				<form id="send_tdate" action=" brainTeasers.php" style="display:none" method="POST">
				<input type="hidden" name='datet' id="datet"> </input>
				</form>
			</div>
			</div>

	<form name="mtrickss" id="mtrickss" method="POST" style="height:500px;" >

	<div id="teaser_content" ><div style="font-size:2em; margin-bottom:30px;margin-top:-10px;"> Teaser Question </div><?=$TeaserDescription[0]?></div>
	<?php
	
	
	
	$path = $exploreZone."teasers/teaser_".$dateToday.".jpg";
	 ?>

	<div id=ddd_image style="background-image:url(<?php echo $path?>); background-size:100% 100% ; " >

	</div>
	<?php ?>
	<div id="teaser_contentss"><div style="font-size:2em; margin-bottom:30px;margin-top:-10px;"> Teaser Answer </div><?=$TeaserAns[0]?></div>
	<?php
	
	$path1 = $exploreZone."teasers/answer_".$dateToday.".jpg";
	?>

	<div id=dd_image style="background-image:url(<?php echo $path1?>); background-size:100% 100% ;" >

	</div>
	<?php ?>

	<?php if($resp[0]!= null) { ?>
	<div id="teaser_contentss"><div style="font-size:2em; margin-bottom:30px;margin-top:-10px;"> Your answer </div><?=$resp[0]?></div>
	<?php } ?>

	<?php if($resp[0]== null) { ?>
	<div id="teaser_contentss"><div style="font-size:2em; margin-bottom:30px;margin-top:-10px;"> Your answer </div>You have not attempted this teaser.</div>
	<?php } ?>

	</form>
	
	<input type="hidden" name="days" id="days" value="<?=$day?>">
	<input type="hidden" name="dates" id="dates" value="<?=$set_date?>">
	</div>
<?php include("footer.php");?>