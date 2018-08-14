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
	//$secretQuestion =   $objUser->secretQues;
	//$secretAnswer =   $objUser->secretAns;
	$Name = explode(" ", $_SESSION['childName']);
	$Name = $Name[0];

	/* MODEL - Save data */
	if(isset($_POST) && array_key_exists('subBtnCngPass',$_POST)){
		$userArray = getDetails(); // find users details - based on username set in session
		$message = ''; //init
		#-- validate data
		if($_POST['oldPassword'] == $_POST['newPasswordalpha'])
		{
			$message = '<b>New password cannot be the same as current password!</b>';
		}
		if($userArray['username'] == $_POST['newPasswordalpha'])
		{
			$message = '<b>Login name and Password cannot be the same.</b>';
		}
		//if(!validateOldPassword($_SESSION['userID'],$_POST['oldPassword']))
		//{
		//	$message = '<b>Old password does not match.</b>';
		//}

		# save to DB
		if(empty($message)){
			db_updateChangedPassDetails($_SESSION['userID']);
			/*$flag_secretq = 0;
			$flag_secretans = 0;
			if(trim($userArray['secretQues']) != trim($_POST['secretQuestion']))
			{
				$flag_secretq=1;
			}
			if(trim($userArray['secretAns']) != trim($_POST['secretAnswer']))
			{
				$flag_secretans=1;
			}
			if($flag_secretans and $flag_secretq)
			{
				$message = '<b>Your password, secret question, and secret answer have been changed</b>';
			}
			elseif (($flag_secretans==1) and ($flag_secretq==0) )
			{
				$message = '<b>Your password, secret answer have been changed</b>';
			}
			elseif (($flag_secretans==0) and ($flag_secretq==1) )
			{
				$message = '<b>Your password, secret question have been changed</b>';
			}
			else*/
				//$message = '<b>Your password has been changed</b>';

				//$message .= ', <b>Click on Home to continue</b>';
				//redirectPage();
				echo '<script type="text/javascript">
				alert("Your Password has been changed.");
				window.location	=	"myDetailsPage.php";
				</script>';

		}
	}


	/* Get fresh data to display in below form */
	$userArray = getDetails(); // find users details - based on username set in session
	$category = $userArray['category'];
	$subcategory = $userArray['subcategory'];
	$homePage = "home.php";
	if(strcasecmp($category,"School Admin")==0 || strcasecmp($category,"Teacher")==0 || strcasecmp($category,"Home Center Admin")==0)
		$homePage = "ti_home.php";

	/*init ques type*/
	$question_array = array('What is your place of birth?','Who is your favourite cricketer?','What is your favourite car?','Who is your favourite actor or actress?','What is your favorite colour?');
	$sparkieImage = $_SESSION['sparkieImage'];
?>

<?php include("header.php"); ?>

<title>Change Password</title>
	<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
        <script type='text/javascript' src='libs/combined.js'></script>
	<!--<script type="text/javascript" src="libs/i18next.js"></script>-->
	<!--<script type="text/javascript" src="libs/translation.js"></script>-->
	<script type="text/javascript" src="/mindspark/js/load.js"></script>
	<!--<script type="text/javascript" src="libs/closeDetection.js"></script>-->
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
	<link href="css/changePassword/midClass.css?ver=2" rel="stylesheet" type="text/css">
    <link href="css/commonMidClass.css" rel="stylesheet" type="text/css">
	<script>
		function load(){
			var a= window.innerHeight -220;
			//$('#formContainer').css("height",a);
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
		function redirecttheme2()
		{
			setTryingToUnload();
			window.location.href	=	"myDetailsPageTheme2.php";
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
								<!--	<li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li>-->
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
				<div class="icon_text2">- Change Password</font></div>
			</div>
			<div id="topic">
				<div id="home" onClick="getHome()" class="linkPointer">
				</div>
				<div class="icon_text1"><a href="home.php" style="text-decoration:none;color:inherit">HOME</a> > <font color="#606062"> Change Password</font></div>
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
			<a href="explore.php"><div id="drawer4"><div id="drawer4Icon"></div>EXPLORE ZONE
			</div></a>
			<!--<div id="drawer4"><div id="drawer4Icon"></div>EXPLORE ZONE
			</div>-->
			<div id="plus" onClick="openMainBar();">
				<div id="vertical"></div>
				<div id="horizontal"></div>
			</div>
			<a href="src/rewards/rewardsDashboard.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;"><div id="drawer5"><div id="drawer5Icon" <?php if($_SESSION['rewardSystem']!=1) { echo "style='position: absolute;background: url(\"assets/higherClass/dashboard/rewards.png\") no-repeat 0 0 !important;width: 50px;height: 50px;margin-top: -30px;margin-left: -50px;'";} ?> class="<?=$sparkieImage?>"><div class="redCircle"><?=$sparkieWon?></div></div>
			REWARDS CENTRAL
			</div></a>
			<!--<a href="viewComments.php?from=links&mode=1"><div id="drawer6"><div id="drawer6Icon"></div>NOTIFICATIONS
			</div></a>-->
		</div>
		<!--<?php if($theme==2){ ?>
			<div id="NA" onClick="redirecttheme2()" class="hidden">
		<?php } else { ?>
			<div id="NA" onClick="redirect()" class="hidden">
		<?php }  ?>-->
				<div id="NA" onClick="redirect()" class="hidden">
					Personal Info
					<div id="naM" class="pointed2">
					</div>
				</div>
				<div id="A" class="hidden">
					Change Password
					<div id="aM" class="pointed1">
					</div>
				</div>
				<div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
				<div id="NA" onClick="redirect()">
					<div id="naM" class="pointed1">
					</div></br>
					My Details
				</div>
				<div id="A">
					<div id="aM" class="pointed2">
					</div></br>
					Change Password
				</div>
			</div>
			</div>
	<div id="formContainer">
        <form action="changePassword.php" id="frmChngPwd" name="frmChngPwd" method="POST">
            <div id="container_form">
            <span style="color:red"><?=$message?></span>
            <!--<div id="question">Secret Question<font style="color:red">*</font> :</div>
            <select name="secretQuestion" id="secretQuestion" tabindex="1" class="Box1">
                <option id="select" value="">Select</option>
                <?php
				foreach ($question_array as $secQus)
				{
					if($secretQuestion ==$secQus){
						echo "<option value='$secQus' selected >$secQus</option>";
					}
					else
						echo "<option value='$secQus'>$secQus</option>";
				}
				?>
            </select>
            <div id="answer" class="cellpadding">Secret Answer<font style="color:red">*</font> :</div>
            <input class="Box" type="text" name="secretAnswer" id="secretAnswer" maxlength="30" value="<?=$secretAnswer?>"></input>-->
            <div id="currentP" class="cellpadding" name="oldPassword" id="p1">Your current Password  :</div>
            <input class="Box" type="password"  maxlength="30" name="oldPassword" id="p1"></input>
            <div id="newP" class="cellpadding">New Password<font style="color:red">*</font> :</div>
            <input class="Box" type="password" name="newPasswordalpha" id="newPasswordalpha" maxlength="15"></input>
            <div id="retypeP" class="cellpadding">Retype Password<font style="color:red">*</font> :</div>
            <input class="Box" type="password"  name="newPasswordbeta" id="newPasswordbeta" maxlength="15"></input>
            <br/>
            <input type="submit" value="Submit" name="subBtnCngPass" id="submit_button" class="button1" onClick="javascript:setTryingToUnload();"></input>
            <input type="button" value="Cancel" id="submit_button" class="button1" onClick="getHome();"></input>
			</div>
			
            <!--<div id="submitQuestion" class="button1">Submit</div>-->
        </form>
<script language="JavaScript" type="text/javascript">
	var frmvalidator = new Validator("frmChngPwd");
	
	/*frmvalidator.addValidation("secretQuestion",'req','You do not have a secret question - please choose one and the answer to it. You will need to fill it to change your password in future.');
	frmvalidator.addValidation("secretAnswer",'req','Please specify the secret answer.');*/
	
	//frmvalidator.addValidation("oldPassword",'req','Please specify the current password');
	
	frmvalidator.addValidation("newPasswordalpha", "req", "Please specify the new password.");
	
	frmvalidator.addValidation("newPasswordalpha", "maxlen=15", "Your new password cannot be more than 15 characters.");
	
	frmvalidator.addValidation("newPasswordbeta", "req", "Please re-enter your new password.");
	
	frmvalidator.setAddnlValidationFunction("changePassValidation");
</script>
	</div>
		
	</div>
    <div style="padding-top:185px;">
<?php include("footer.php"); ?>
</div>
<?php

function db_updateChangedPassDetails($userID)
{
	if($userID and $_POST['newPasswordalpha']) 
	{
//	$query = "UPDATE adepts_userDetails SET
//			 password = password('".$_POST['newPasswordalpha']."'),
//			 secretQues = '".mysql_escape_string($_POST['secretQuestion'])."',
//			 secretAns = '".mysql_escape_string($_POST['secretAnswer'])."'
//			 WHERE userID = $userID ";
	/*$query = "UPDATE educatio_educat.common_user_details SET
			 password = password('".$_POST['newPasswordalpha']."'),
			 secretQues = '".mysql_escape_string($_POST['secretQuestion'])."',
			 secretAns = '".mysql_escape_string($_POST['secretAnswer'])."'
			 WHERE MS_userID = $userID ";*/

	$query = "UPDATE educatio_educat.common_user_details SET
			 password = password('".$_POST['newPasswordalpha']."') 
			 WHERE MS_userID = $userID ";
    //$result = mysql_query($query) or die("# $query #".mysql_error());// get res

    //var_dump(mysql_error());
    if(mysql_query($query)){
    	return true;
     }
     else return false;
	}
}
?>