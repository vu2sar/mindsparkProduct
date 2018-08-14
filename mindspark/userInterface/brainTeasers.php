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
	//$arrActivities = getActivities($childClass,"",$objUser->packageType);
	$today = date("Y-m-d");
	$lastMon = date("Y-m-d",strtotime("last Monday"));
	$dateToday = $_REQUEST['datet'];
	

	
	$sql = "SELECT `Teaser_Description`,`Teaser_No`,`descriptionImage` FROM `adepts_exploreTeasers` WHERE `Teaser_Date` = '$dateToday'" ;
	$result = mysql_query($sql) or die(mysql_error());
	$j = 0;
	while($row = mysql_fetch_array($result))
	{
 		
		$TeaserDescription[$j] = $row['Teaser_Description'];
		$TeaserNo[$j] = $row['Teaser_No'];
		$description_Image[$j] = $row['descriptionImage'];
		
 		$j++;
	}
	
	
	$ans = $TeaserNo[0];
	
	$sql = "SELECT `userResponse` FROM `adepts_exploreTeaserStudent` WHERE `userID` = '$userID' and `Teaser_No` = '$ans'";
	$result1 = mysql_query($sql) or die(mysql_error());
	$j = 0;
	while($row1 = mysql_fetch_array($result1))
	{
 		$sResponse[$j] = $row1['userResponse'];
 		$j++;
	}
	
	
	$sql = "SELECT `userID` FROM `adepts_exploreTeaserStudent` WHERE `Teaser_No` = '$ans'";
	$result1 = mysql_query($sql) or die(mysql_error());
	$j = 0;
	while($row1 = mysql_fetch_array($result1))
	{
 		$U_Id[$j] = $row1['userID'];
 		$j++;
	}

	if (in_array($userID, $U_Id)) {
    
	}
	else
	{
	
		if($ans!="")
		{
			$sql =  "INSERT INTO `adepts_exploreTeaserStudent`(`userID`,`isViewed`,`Teaser_No`) VALUES('$userID','1','$ans')"; 
	mysql_query($sql) or die(mysql_error());
		}
	
	}
	
	// Time
	
	date_default_timezone_set('Asia/Kolkata');
	$today = getdate();
	$day = $today["wday"];
	
	$set_soln_date = date('Y-m-d', strtotime($dateToday .' -3 day'));

?>

<?php include("header.php");?>

<title>Explore Zone</title>
	<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
	
	<?php  if($theme==3) { ?>
	    <link href="css/commonHigherClass.css" rel="stylesheet" type="text/css">
		<link href="css/explorezone/higherClass.css" rel="stylesheet" type="text/css">
		
		
		<script type="text/javascript">
		
				var d = new Date();
				var d1 = d.getDate();
				var d2 = d.getDay();
				
		function validate()
		{
			validateDate(document.getElementById('datepicker').value);
		}
		
		function foo()
		{		
				$('.t1').css("color","black");
				$('.t2').css("color","black");
				$('.t3').css("color","#885E9E");	
		}	
				
				
		function previous_answer()
		{		
				document.getElementById('teaserID').value = document.getElementById('dates').value ;
				setTryingToUnload();
				document.getElementById('mtricks').submit();
				
		}
		
		function loadPreviousTeaser()
		{
				var a = document.getElementById('datepicker').value;
				var arr = a.split('/');
			
				/*var mmd = new Date();
				mmd.setFullYear(parseInt(arr[2]));
				mmd.setMonth(parseInt(arr[1]) - 1); 
				mmd.setDate(parseInt(arr[0]));*/  
				
				var mmd = new Date(parseInt(arr[2]),(parseInt(arr[1]) - 1),parseInt(arr[0])) ;
				
				var today = document.getElementById('teaseDate').value;
				var arr1 = today.split('-');
			
				/*var mmd1 = new Date();
				mmd1.setFullYear(parseInt(arr1[0]));
				mmd1.setMonth(parseInt(arr1[1]) - 1); 
				mmd1.setDate(parseInt(arr1[2]));*/  
				
				var mmd1 = new Date(parseInt(arr1[0]),(parseInt(arr1[1]) - 1),parseInt(arr1[2])) ;
			
		if(mmd >= mmd1)
		{
					alert("Set a date before this Thursday");
		}
		else
		{
			
			if(document.getElementById('datepicker').value == "" || document.getElementById('datepicker').value == "dd/mm/yyyy")
			{
				alert("Enter Valid Date");
			}
			else
			{
				
				var a = document.getElementById('datepicker').value;
				var arr = a.split('/');
				var b = arr[2]+"-"+arr[1]+"-"+arr[0];
				
				document.getElementById('teaserID').value = b;
				setTryingToUnload();
				document.getElementById('mtricks').submit();
				
			}
			
		
		 }
		}	
		
		
		function saveAnswer()
		{	
			
			 var a = document.getElementById('teaser_answer').value;
			 var b = document.getElementById('teaseNo').value;
			 var c = a+"-"+b;
			
			 document.getElementById('ans1').value = c;
			 if(a=="Write your answer here")
			 {
			 	alert("Enter valid response");
			 }
			 else
			 {
			 	setTryingToUnload();
			 	document.getElementById('save_answer').submit();	
			 }
			 
		}
		
		
		function trick_topic_load(idd)
		{
			
			document.getElementById('topicID').value = idd;
			setTryingToUnload();
			document.getElementById('mtricks').submit();
			
			
		}
		
		function explore_more_tricks()
		{
			setTryingToUnload();
			window.location	 = "explore_more_tricks.php";
			value=0;
		}
		function load(){
			
				$('.t1').css("color","black");
				$('.t2').css("color","black");
				$('.t3').css("color","#885E9E");
			
			var b= window.innerHeight - (190);
			var a= window.innerHeight - (185);
			$('#mtricks').css({"height":b+"px"});
			$('#main_bar').css({"height":a+"px"});
			$('#menu_bar').css({"height":a+"px"});
			$('#sideBar').css({"height":a+"px"});
			if(androidVersionCheck==1){
				$('#mtricks').css("height","auto");
				$('#main_bar').css("height",$('#mtricks').css("height"));
				$('#menu_bar').css("height",$('#mtricks').css("height"));
				$('#sideBar').css("height",$('#mtricks').css("height"));
			}
		}
		
			
	
		
		function attempt(id){
			$(".locked,.attempted,.notAttempted").hide();				
		
			if(id=="Shortcuts")
			{
	
				$('.t1').css("color","#885E9E");
				$('.t2').css("color","black");
				$('.t3').css("color","black");
				setTryingToUnload();
				window.location = "explore.php";
				
			}
			if(id=="Games")
			{	
				
				$('.t1').css("color","black");
				$('.t2').css("color","#885E9E");
				$('.t3').css("color","black");
				
				
			}
			if(id=="Teasers")
			{
				
				$('.t1').css("color","black");
				$('.t2').css("color","black");
				$('.t3').css("color","#885E9E");
				
				
			}
			
		}
		
		
		
		
	</script>
	<?php } ?>
	<script type="text/javascript" src="libs/i18next.js"></script>
	<script type="text/javascript" src="libs/translation.js"></script>
	
	<link rel="stylesheet" type="text/css" href="css/CalendarControl.css" >
<script type="text/javascript" src="libs/CalendarControl.js" language="javascript"></script>
<script type="text/javascript" src="libs/calendarDateInput.js" language="javascript"></script>
<script type="text/javascript" src="libs/dateValidator.js"></script>
<script type="text/javascript" src="libs/closeDetection.js"></script>
	
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
	
	<script src="libs/css_browser_selector.js" type="text/javascript"></script>
	<link rel="stylesheet" href="libs/css/jquery-ui.css" />
  <!--<script src="http://code.jquery.com/jquery-1.9.1.js"></script>-->
  <script src="libs/jquery-ui-1.10.3.js"></script>
  <!-- <link rel="stylesheet" href="/resources/demos/style.css" /> -->
</head>
<body  class="translation"  onload="load()" onResize="load()">
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
	<input type="hidden" id="teaseDate" value="<?=$dateToday?>" />
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
			<div id="drawer5"><div id="drawer5Icon"></div>REWARD POINT
			</div>
			<!--<a href="viewComments.php?from=links&mode=1"><div id="drawer6"><div id="drawer6Icon"></div>NOTIFICATIONS
			</div></a>-->
		</div>
		

	<div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
				
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
			        <span id="classText" class="t3" data-i18n="explorepage.teasers"></span>
			        <div id="aM" class="pointed2">
			        </div>
			    </div>
			</div>
			<input type="hidden" name="dates" id="dates" value="<?=$set_soln_date?>">
			</div>
	
	<form name="mtricks" id="mtricks" method="POST" action="load_Previous_Teaser.php">
	<?php if($TeaserDescription[0]!= ""){ ?>
	<div id="teaser_head"> Thursday's Brain Teaser </div>
	<?php } 
	else{ ?>
		<div id="teaser_head"> Sorry no teaser for today.. </div>
	<?php }
	 ?>
	<div id="archieved" > <div id="a_text"> <I><B><U>Archived teasers</U></B></I> </div> 
	<input type="text" id="datepicker" value="dd/mm/yyyy" onFocus="if (value == 'dd/mm/yyyy') {value=''}" onBlur="if (value== '') {value='dd/mm/yyyy'}"  />
	
	<input id="ok_button"  type="button" value="Ok" onClick="loadPreviousTeaser()" onFocus="validate()"/>
	<script>
		$( "#datepicker" ).val('dd/mm/yyyy').datepicker({ dateFormat: 'dd/mm/yy' });
	</script>

	</div>
	<div id="teaser_content"><?=$TeaserDescription[0]?></div>
	<?php 
	$path = $exploreZone."teasers/teaser_".$dateToday.".jpg";
	?>
	<div id=d_image style="background-image:url(<?php echo $path ?>); background-size:100% 100% ;" > </div>
	
	<?php if ($sResponse[0]=="")
	{ ?>
			<?php if($TeaserDescription[0]!= ""){ ?>
	<input type="text" id=teaser_answer value="Write your answer here" onFocus="if (value == 'Write your answer here') {value=''}" onBlur="if (value== '') {value='Write your answer here'}" >  </input>
		<input type="hidden" id="teaseNo" value="<?=$ans?>"> </input>
	<input id="submit_button" type="button" value="Submit" onClick="saveAnswer()"> </input>
	
	<div id=teaser_notification> Come back on next Monday for the answer to this teaser </div>
	
		<?php } ?>
	<?php } ?>
	
	<?php if ($sResponse[0]!="")
	{ ?>
	<div id=teaser_notification>You have already answered. Come back on next Monday for the answer to this teaser </div>
	<?php } ?>
	
	<div id="more_teaser" >
	<div id = "more_teaser_text"> Want to view solution to last teaser ?
	
	<input id="solution_button" type="button" value="YES" onClick="previous_answer()"> </input>
	
	</div>
	</div>
	<input type="hidden" name='teaserID' id="teaserID">
	</form>		
	
	
	
	<form id=save_answer action="brainTeaserAnswer.php" style="display:none" method="POST">
	<input type="hidden" name='ans1' id="ans1"> </input>
	</form>


<?php include("footer.php");?>

<!--Brain Teaser Answer-->
