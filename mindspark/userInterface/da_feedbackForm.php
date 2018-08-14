<?php
@include("check1.php");
include("classes/clsUser.php");

set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
error_reporting(E_ERROR);

if(!isset($_SESSION['userID']))
{
	header("Location: logout.php");
	exit;
}

$userID   = $_SESSION['userID'];
$daPaperCode = $_SESSION['daPaperCode'];
$sqlCheck = "select * from da_feedback where userId = $userID and paperCode = '$daPaperCode' ";
$resultCheck =  mysql_query($sqlCheck) or die(mysql_error().$sqlCheck);
$user = new User($userID);
$Name = explode(" ", $_SESSION['childName']);
$Name = $Name[0];
if(mysql_num_rows($resultCheck) > 0)
	header('Location: daTestReport.php');


if(isset($_POST['question'])) {
	
	
	$valueString = '';		
	
	foreach ($_POST['question'] as $questionno => $userresponse) {
		$explaination = str_replace("'","&#39;",$userresponse[0]);
		$valueString .= "($userID, '".$questionno."', '".$explaination."', '$daPaperCode' ,NOW() ),";
	}
	$valueString = substr($valueString, 0, strlen($valueString)-1);

	$sql = "INSERT INTO da_feedback(userID, questionNo, response,paperCode, lastModified) VALUES ".$valueString;  

	$result =  mysql_query($sql) or die(mysql_error().$sql);

	if ($result) {
		header('Location: daTestReport.php');
	}
}
	
?>

<?php include("header.php");?>
<title>Feedback Form</title>
<?php 
if($theme == 2){
?>
	<link rel="stylesheet" href="css/feedBackForm/midClass.css" />
	<link rel="stylesheet" href="css/commonMidClass.css" />
<?php 	
}else if($theme == 3){
	?>
		<link rel="stylesheet" href="css/commonHigherClass.css" />
    	<link rel="stylesheet" href="css/feedBackForm/higherClass.css" />
	<?php 
}
?>

<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script>
var langType = '<?=$language;?>';
</script>
<script>
function load(){
	<?php if($theme==1) { ?>	
	var a= window.innerHeight -200;
    $('#feedbackFormMain').css("height",a);
<?php } else if($theme==2){ ?>
<?php } else if($theme==3) { ?>
		var a= window.innerHeight - (170);
		var b= window.innerHeight - (610);
		var c= parseInt($('#frmFeedback').css("height")) +150;
		$('#frmFeedback').css("height",c+"px");
		$('#feedbackFormMain').css({"height":a+"px"});
		$('#sideBar').css({"height":a+"px"});
		$('#main_bar').css({"height":a+"px"});
		$('#menuBar').css({"height":a+"px"});
	<?php } ?>
	if(androidVersionCheck==1) {
		$('#frmFeedback').css("height","auto");
		$('#main_bar').css("height",$('#frmFeedback').css("height"));
		$('#menu_bar').css("height",$('#frmFeedback').css("height"));
		$('#sideBar').css("height",$('#frmFeedback').css("height"));
	}
}
	function logoff()
	{
		
		window.location="logout.php";
	}

	 function checkedornot(classname) {
      var rd = document.getElementsByClassName(classname);
      var countrd = rd.length;

      for(var i=0; i < countrd; i++) {
        if(rd[i].checked) {
          return true;
        }
      }

      return false;
    }
    function selectAnswer(event) {
		
      if(!(checkedornot('radioBtn1') && checkedornot('radioBtn2') && checkedornot('radioBtn3') && checkedornot('radioBtn4')))
      {
        alert('Please complete the feedback form to proceed to the report.');
        event.preventDefault();
      }
    }
    var click=0;
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

 <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback Form</title>
</head>
<body class="translation"  onLoad="load()">

<div id="top_bar">
		<div class="logo">
		</div>
        
        <div id="studentInfoLowerClass" class="forLowerOnly hidden">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span><?=$user->childName?>&nbsp;</span></a></li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.classSmall">Class</span> <span id="userClass"><?=$user->childClass?><?=$user->childClass.$user->childSection?></span></div>
            </div>
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
								<!--	<li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li>-->
									<li><a href='changePassword.php'><span data-i18n="homePage.changePassword"></span></a></li>
                                    <li><a href='whatsNew.php'><span data-i18n="common.whatsNew"></span></a></li>
                                    <li><a href='logout.php'><span data-i18n="common.logout"></span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?=$user->childClass.$user->childSection?></span></div>
            </div>
        </div>
        <div id="help" style="visibility:hidden">
        	<div class="help"></div>
            <div class="helpText" data-i18n="common.help"></div>
        </div>
        <div id="logout" onClick="logoff()" class="linkPointer hidden">
        	<div class="logout"></div>
        	<div class="logoutText" data-i18n="common.logout"></div>
        </div>
		<div id="whatsNew" style="visibility:hidden">
            <div class="whatsNew"></div>
            <div class="whatsNewText" data-i18n="common.whatsNew"></div>
        </div>
    </div>
	
	<div id="container">
 
		<div id="info_bar" class="forHigherOnly hidden">
			<div id="topic">
				<div id="home">
                	<div id="homeIcon"></div>
                    <div id="homeText">HOME > <font color="#606062"> FEEDBACK FORM</font></div>
				</div>
                <div id="feedbackInfo">Please take a few minutes out to answer the questions below</div>
				<div class="clear"></div>
			</div>
			<div class="class">
				<strong>Class </strong>: <?=$user->childClass?><?=$user->childSection?>
			</div>
			<div class="Name">
				<strong><?=$user->childName?></strong>
			</div>
            <div class="clear"></div>
		</div>
		 <div id="info_bar" class="forHighestOnly">
				<div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase"> FEEDBACK FORM</span></div>
                </div>
				<div class="arrow-right"></div>
				<div id="feedbackInfo">Please take a few minutes out to answer the questions below</div>
				<div class="clear"></div>
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
			<!--<a href="viewComments.php?from=links&mode=1"><div id="drawer6"><div id="drawer6Icon"></div>NOTIFICATIONS
			</div></a>-->
		</div>
		<div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
			</div>
			</div>
        <div id="feedbackFormMainDiv">
			<div id="school" class="forLowerOnly hidden">School: <?=$user->schoolName?></div>
			
			<div id="feedbackFormMain">
				<div id="feedbackInfo" class="forLowerOnly hidden">Please take a few minutes out to answer the questions below</div>

    <form action="da_feedbackForm.php" method="POST" id="frmFeedback">
		 <b><p>You have successfully completed the test. Please answer 5 simple questions to continue to the results.</p>
    <p><em>Questions</em></p></b>
      <dl>
<!--        <dt><b>Test qulality</b></dt>-->
        <dd>
          <p><label>1. How often would you like to take the Super Test?</lable></p>
          <p>
            <input type="radio" class="radioBtn1" name="question[1][]" value="Every month">Every month
            <input type="radio" class="radioBtn1" name="question[1][]" value="Every 15 days">Every 15 days
            <input type="radio" class="radioBtn1" name="question[1][]" value="Every week">Every week
          </p>
        </dd>
   
        <dd>
          <p><label>2. Did you like taking the Mindspark Super Test?</lable></p>
          <p>
            <input type="radio" class="radioBtn2" name="question[2][]" value="Yes">Yes
            <input type="radio" class="radioBtn2" name="question[2][]" value="It was okay">It was okay
            <input type="radio" class="radioBtn2" name="question[2][]" value="No">No
          </p>
        </dd>
        
        <dd>
          <p><label>3.  Rate the difficulty level of the paper.</lable></p>
          <p>
            <input type="radio" class="radioBtn3" name="question[3][]" value="Easy">Easy
            <input type="radio" class="radioBtn3" name="question[3][]" value="Balanced">Balanced
            <input type="radio" class="radioBtn3" name="question[3][]" value="Difficult">Difficult
          </p>
        </dd>
        <dd>
		
          <p><label>4. Did you feel that some questions were out of syllabus?</lable></p>
          <p>
            <input type="radio" class="radioBtn4" name="question[4][]" value="No">No
            <input type="radio" class="radioBtn4" name="question[4][]" value="Yes, some">Yes, some
            <input type="radio" class="radioBtn4" name="question[4][]" value="Yes, many">Yes, many
           
          </p>
        </dd>
		<dd>
          <p><label>5. Please share any other comments or suggestions that you have.</label></p>
          <p>
            <textarea class="optional" name="question[5][]" cols="60" rows="3"></textarea>
          </p>
        </dd>
      </dl>

      <!--<button type="submit" onClick="selectAnswer(event);">Submit</button>-->
<input type="submit" id="btnSubmit1" class='button1' name ="submit" value="Submit" onClick="selectAnswer(event);">
    </form>

	</div>
	</div>
<?php include("footer.php");?>