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
	if(strcasecmp($objUser->subcategory,'Individual')!=0)
	{
		header("Location:logout.php");
		exit;
	}
	$Name = explode(" ", $_SESSION['childName']);
	$Name = $Name[0];

	/* MODEL - Save data */
	if(isset($_POST) && array_key_exists('startSummerProgram',$_POST)) {
		$sq	=	"INSERT INTO adepts_summerProgram SET userID='".mysql_real_escape_string($userID)."'";
		$rs	=	mysql_query($sq);
		header("location:dashboard.php?programMode=summerProgram");
	}

	/* Get fresh data to display in below form */
	$userArray = getDetails(); // find users details - based on username set in session
	$category = $userArray['category'];
	$subcategory = $userArray['subcategory'];
	$homePage = "home.php";
	if(strcasecmp($category,"School Admin")==0 || strcasecmp($category,"Teacher")==0 || strcasecmp($category,"Home Center Admin")==0)
		$homePage = "ti_home.php";

	/*init ques type*/
	$sparkieImage = $_SESSION['sparkieImage'];
?>

<?php include("header.php"); ?>

<title>MINDSPARK - SUMMER</title>
	<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="libs/combined.js"></script>
<!--	<script type="text/javascript" src="libs/i18next.js"></script>
	<script type="text/javascript" src="libs/translation.js"></script>-->
	<script type="text/javascript" src="/mindspark/js/load.js"></script>
<!--	<script type="text/javascript" src="libs/closeDetection.js"></script>-->
	<script>
	var langType = '<?=$language;?>';
	</script>
	<?php
	if($theme==1) { ?>
	<link href="css/changePassword/lowerClass.css" rel="stylesheet" type="text/css">
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
	<link href="css/changePassword/midClass.css" rel="stylesheet" type="text/css">
    <link href="css/commonMidClass.css" rel="stylesheet" type="text/css">
	<script>
		function load(){
			var a= window.innerHeight -220;
			$('#formContainer').css("height",a);
		}
		
	</script>
	<?php } else { ?>
	<link href="css/commonHigherClass.css" rel="stylesheet" type="text/css">
	<link href="css/changePassword/higherClass.css" rel="stylesheet" type="text/css">
	<script>
		function load(){
			var a= window.innerHeight - (170);
			$('#formContainer').css({"height":a+"px"});
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
								<!--	<li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li> -->
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
		<div id="logout" onClick="logoff()" class="hidden">
        	<div class="logout"></div>
        	<div class="logoutText">Logout</div>		
        </div>
    </div>
	
	<div id="container">
		<div id="info_bar" class="hidden">
			<div id="lowerClassProgress">
				<a href="home.php"><div id="homeIcon"></div></a>
				<div class="icon_text2">-  SUMMER</font></div>
			</div>
			<div id="topic">
				<div id="home" onClick="getHome()" class="linkPointer">
				</div>
				<div class="icon_text1"><a href="home.php" style="text-decoration:none;color:inherit">HOME</a> > <font color="#606062"> SUMMER</font></div>
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
				<a href="myDetailsPage.php" style="text-decoration:none;color:inherit"><div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase">student information</span></div>
                </div></a>
				<div class="arrow-right"></div>
				<div class="clear"></div>
		</div>
		<div id="main_bar" class="forHighestOnly">
			<div id="drawer1">
			<a href="activity.php" style="text-decoration:none;color:inherit"> 
			<div id="drawer1Icon"></div>
			ACTIVITIES
			</div></a>
			<a href="examCorner.php" style="text-decoration:none;color:inherit"><div id="drawer2"><div id="drawer2Icon"></div>EXAM CORNER
			</div></a>
			<a href="home.php"><div id="drawer3"><div id="drawer3Icon"></div>HOME
			</div></a>
			<div id="plus" onClick="openMainBar();">
				<div id="vertical"></div>
				<div id="horizontal"></div>
			</div>
			<div id="drawer5"><div id="drawer5Icon" <?php if($_SESSION['rewardSystem']!=1) { echo "style='position: absolute;background: url(\"assets/higherClass/dashboard/rewards.png\") no-repeat 0 0 !important;width: 50px;height: 50px;margin-top: -30px;margin-left: -50px;\";";} ?> class="<?=$sparkieImage?>"><div class="redCircle"><?=$sparkieWon?></div></div>
			<?php if($sparkieImage=="level1"){ ?>
				SPARKIES
			<?php } else if($sparkieImage=="level2"){ ?>
				SUPERSPARKIES
			<?php } else if($sparkieImage=="level3"){ ?>
				MEGASPARKIES
			<?php }else{ ?>
				REWARD POINTS
			<?php } ?>
			</div>
			<!--<a href="viewComments.php?from=links&mode=1"><div id="drawer6"><div id="drawer6Icon"></div>NOTIFICATIONS
			</div></a>-->
		</div>
		<div id="NA" onClick="redirect()" class="hidden">

				</div>
				<div id="A" class="hidden">
				</div>
				<div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
			</div>
			</div>
	<div id="formContainer">
        <form action="" id="frmChngPwd" name="frmChngPwd" method="POST">
            <div id="container_form">
				<br><br>
				<strong style="font-size:20px">Summer Mindspark</strong>
            	<p style="font-size:18px">This summer take mindspark wherever you go.<br>Join our Summer Mindspark sessions today to enjoy special lesson plans created just for you. Mindpspark will allow you to revise concepts from your previous class as well as give fun filled introductions and lessons of topics you will learn this year!<br>A fun filled 60 days adventure awaits you!<br><br>Enroll today at no extra cost.</p>
				<br>
				<input type="submit" value="Yes! Let's get started" name="startSummerProgram" id="submit_button" class="button1" style="cursor:pointer" onClick="javascript:setTryingToUnload();"></input>
            	<input type="button" value="Cancel" id="submit_button" class="button1" onClick="getHome();" style="cursor:pointer"></input>
			</div>
        </form>

	</div>
		
	</div>
    
<?php include("footer.php"); ?>