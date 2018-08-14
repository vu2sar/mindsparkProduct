<?php
include("check1.php");
include("classes/clsUser.php");
require_once 'constants.php';
$userID = $_SESSION['userID'];
$user = new User($userID);
/*  Lines to be removed
$user->childClass = '6';
$user->childName = 'Manish Dariyani';
$user->childSection = 'C';
Lines to be removed */
if(!isset($_SESSION['userID']))
{
	header("Location: logout.php");
	exit;
}
$Name = explode(" ", $_SESSION['childName']);
$Name = $Name[0];
if(isset($_POST['submit']))
{
	$feedbackset = 24;
	$query  = "SELECT qid FROM adepts_feedbackSet WHERE setno=$feedbackset";
	$result = mysql_query($query);
	$line   = mysql_fetch_array($result);
	$qids = $line[0];
	$arrQids	=	explode(",",$qids);
	$ratingResponseArray = $_POST['ratingSlider']; // Associative array of ratings, keys : 1 to 5
	$quesResponseArray = $_POST['ques']; // Associative two dimensional array of responses, keys : 1 to 3 & 1,b,c
	$i=1;
	foreach($arrQids as $qid)
	{
		if($i<=5)
		{
			$respose	=	$ratingResponseArray[$i];
		}
		else
		{
			$respose	=	implode("||",$quesResponseArray[$i]);
		}
		$sq	=	"INSERT INTO adepts_feedbackresponse (userID, qid, response, feedbackset, type, feedbackdate) VALUES ($userID,$qid,'".$respose."',".$feedbackset.",'',now())";
		$rs	=	mysql_query($sq);
		$i++;
	}
	header("Location: home.php");
}

include("header.php");

?>

<title>Feedback Form</title>
<link rel="stylesheet" href="css/feedBackForm/midClass.css" />
<link rel="stylesheet" href="css/commonMidClass.css" />
<link rel="stylesheet" href="css/jquery.slider.min.css" />
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script src="libs/jquery.slider.min.js"></script>
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery_ui_touch.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script>
var langType = '<?=$language;?>';
$(document).ready(function(e) {
	$("#ratingSlider1").slider({
		from: 10,
		to: 1,
		step: 1,
		round:1,
		scale:['Great, Awesome','Bad, Don\'t like it at all'],
		limits:false,
		skin:'round_plastic'
	});
	$("#ratingSlider2").slider({
		from: 10,
		to: 1,
		step: 1,
		round:1,
		scale:['Very nice, soothiing to the eye','Difficult to read'],
		limits:false,
		skin:'round_plastic'
	});
	$("#ratingSlider3").slider({
		from: 10,
		to: 1,
		step: 1,
		round:1,
		scale:['Easy to find things and navigate','Very confusing, can\'t find my way'],
		limits:false,
		skin:'round_plastic'
	});
	$("#ratingSlider4").slider({
		from: 10,
		to: 1,
		step: 1,
		round:1,
		scale:['Clear and easy to understand','Not easy to get a clear picture'],
		limits:false,
		skin:'round_plastic'
	});
	$("#ratingSlider5").slider({
		from: 10,
		to: 1,
		step: 1,
		round:1,
		scale:['Super Exciting, Delightful','Dull, boring. Old interface was better'],
		limits:false,
		skin:'round_plastic'
	});
	$(".sliderRatings").slider("value",5);
});
function logoff()
{
	window.location="logout.php";
}
</script>
<style>
.jslider .jslider-scale ins {
	font-size:15px;
}
.jslider .jslider-value {
	font-size:15px;
}
.bottomBorder {
	width:650px;
	height:2px;
	background:#999;
}
</style>
</head>
<body class="translation">
	<div id="top_bar">
		<div class="logo">
		</div>
        <div id="studentInfoLowerClass" class="forLowerOnly">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span><?=$Name?>&nbsp;</span></a></li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.classSmall">Class</span> <span id="userClass"><?=$user->childClass?><?=$user->childSection?></span></div>
            </div>
        </div>
        <div id="help" style="visibility:hidden">
        	<div class="help"></div>
            <div class="helpText" data-i18n="common.help"></div>
        </div>
        <div id="logout" onClick="logoff()" class="linkPointer">
        	<div class="logout"></div>
        	<div class="logoutText" data-i18n="common.logout"></div>
        </div>
		<div id="whatsNew" style="visibility:hidden">
            <div class="whatsNew"></div>
            <div class="whatsNewText" data-i18n="common.whatsNew"></div>
        </div>
    </div>

	<div id="container">
    	<div id="info_bar" class="forLowerOnly">
        	<div id="blankWhiteSpace"><div id="timedTestIcon"></div></div>
             <div id="home">
                <div id="homeIcon" class="linkPointer" onClick="javascript:window.location.href='home.php'"></div>
                <div id="dashboardHeading" class="forLowerOnly">FEEDBACK FORM</div>
                <div class="clear"></div>
            </div>
        </div>
		<div id="info_bar" class="forHigherOnly">
			<div id="topic">
				<div id="home">
                	<div id="homeIcon" class="linkPointer" onClick="javascript:window.location.href='home.php'"></div>
                    <div id="homeText" class="linkPointer" onClick="javascript:window.location.href='home.php'">HOME > <font color="#606062"> FEEDBACK FORM</font></div>
				</div>
                <div id="feedbackInfo">Please take a few minutes out to answer the questions below</div>
				<div class="clear"></div>
			</div>
			<div class="class">
				<strong>Class </strong>: <?=$user->childClass?><?=$user->childSection?>
			</div>
			<div class="Name">
				<strong><?=$Name?></strong>
			</div>
            <div class="clear"></div>
		</div>

        <div id="feedbackFormMainDiv">
        <div id="school" class="forLowerOnly">School: <?=$user->schoolName?></div>

        <div id="feedbackFormMain">
        	<div id="feedbackInfo" class="forLowerOnly">Please take a few minutes out to answer the questions below</div>
            <form name="feedbackform" id="frmFeedback" method="post" action="">
            <div class="ratingQuestions" id="pnlRate">
            	<strong>Rate your new mindspark experience below by moving the slider.</strong>
            </div>
            <div class="ratingQuestions" id="pnlRate1">
            	<div class="sliderText"><b>Over all look and feel</b></div>
				<div class="sliderPlugin"><input type="slider" value="5" class="sliderRatings" name="ratingSlider[1]" id="ratingSlider1" /></div>
            </div>
            <br>
            <div class="bottomBorder"></div>
            <br>
            <div class="ratingQuestions" id="pnlRate2">
            	<div class="sliderText"><b>Text size and font</b></div>
				<div class="sliderPlugin"><input type="slider" value="5" class="sliderRatings" name="ratingSlider[2]" id="ratingSlider2" /></div>
            </div>
            <br>
            <div class="bottomBorder"></div>
            <br>
            <div class="ratingQuestions" id="pnlRate3">
            	<div class="sliderText"><b>Finding different sections</b></div>
				<div class="sliderPlugin"><input type="slider" value="5" class="sliderRatings" name="ratingSlider[3]" id="ratingSlider3" /></div>
            </div>
            <br>
            <div class="bottomBorder"></div>
            <br>
            <div class="ratingQuestions" id="pnlRate4">
            	<div class="sliderText"><b>Information in reports</b></div>
				<div class="sliderPlugin"><input type="slider" value="5" class="sliderRatings" name="ratingSlider[4]" id="ratingSlider4" /></div>
            </div>
            <br>
            <div class="bottomBorder"></div>
            <br>
            <div class="ratingQuestions" id="pnlRate5">
            	<div class="sliderText"><b>Doing New Mindspark</b></div>
				<div class="sliderPlugin"><input type="slider" value="5" class="sliderRatings" name="ratingSlider[5]" id="ratingSlider5" /></div>
            </div>
            <br>
            <div class="bottomBorder"></div>
            <br>
            <div id="pnlQues1">
            	<div class="quesNo">1.</div>
                <div class="quesText">What are the 3 things you <strong>LIKED</strong> the most in the <strong>NEW</strong> Mindspark?</div>
                <div class="clear"></div>
                <div class="quesOpt" id="quesOpt1">
                    <div class="quesNo">a.</div>
                    <div class="quesText"><input type="text" name="ques[6][a]" id="ques1a" class="openText" /></div>
                    <div class="clear"></div>
                    <div class="quesNo">b.</div>
                    <div class="quesText"><input type="text" name="ques[6][b]" id="ques1b" class="openText" /></div>
                    <div class="clear"></div>
                    <div class="quesNo">c.</div>
                    <div class="quesText"><input type="text" name="ques[6][c]" id="ques1c" class="openText" /></div>
                    <div class="clear"></div>
                </div>
            </div>
            <br />

            <div id="pnlQues2">
            	<div class="quesNo">2.</div>
                <div class="quesText">What are the 3 things you <strong>LIKED</strong> the most in the <strong>OLD</strong> Mindspark?</div>
                <div class="clear"></div>
                <div class="quesOpt" id="quesOpt2">
                    <div class="quesNo">a.</div>
                    <div class="quesText"><input type="text" name="ques[7][a]" id="ques2a" class="openText" /></div>
                    <div class="clear"></div>
                    <div class="quesNo">b.</div>
                    <div class="quesText"><input type="text" name="ques[7][b]" id="ques2b" class="openText" /></div>
                    <div class="clear"></div>
                    <div class="quesNo">c.</div>
                    <div class="quesText"><input type="text" name="ques[7][c]" id="ques2c" class="openText" /></div>
                    <div class="clear"></div>
                </div>
            </div>
            <br />


            <div id="pnlQues3">
            	<div class="quesNo">3.</div>
                <div class="quesText">How is the <strong>NEW</strong> interface different from the <strong>OLD</strong> interface?</div>
                <div class="clear"></div>
                <div class="quesOpt" id="quesOpt3">
                    <div class="quesNo">a.</div>
                    <div class="quesText"><input type="text" name="ques[8][a]" id="ques3a" class="openText" /></div>
                    <div class="clear"></div>
                    <div class="quesNo">b.</div>
                    <div class="quesText"><input type="text" name="ques[8][b]" id="ques3b" class="openText" /></div>
                    <div class="clear"></div>
                    <div class="quesNo">c.</div>
                    <div class="quesText"><input type="text" name="ques[8][c]" id="ques3c" class="openText" /></div>
                    <div class="clear"></div>
                </div>
            </div>
            <br />


            <div id="submitButtonDiv1" align="center">
            	<input type="submit" id="btnSubmit1" class='button1' name ="submit" value="Submit" />
            </div>
	        </form>
        </div>
	</div>
<?php include("footer.php");?>