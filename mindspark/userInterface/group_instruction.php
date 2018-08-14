<?php
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);

	@include("check1.php");
	include("constants.php");
	include("functions/orig2htm.php");

	$qcode = $currQues = $quesCategory = $showAnswer = $tmpMode = "";

	if(isset($_POST['qcode']))
	{
		$qcode 		  = $_POST['qcode'];
		$currQues 	  = $_POST['qno'];
		$quesCategory = $_POST['quesCategory'];
		$showAnswer   = $_POST['showAnswer'];
		$tmpMode 	  = $_POST['tmpMode'];
	}
	else
	{
		header("Location:logout.php");
		exit;
	}
	$childClass			= $_SESSION["childClass"];
	$clusterCode 		= $_SESSION["clusterCode"];
	$currentSDL			= $_SESSION["currentSDL"];
	$Name = explode(" ", $_SESSION['childName']);
	$Name = $Name[0];

	$query = "SELECT groupID, groupText  FROM adepts_groupInstruction WHERE clusterCode='$clusterCode' AND linkedToSDL=$currentSDL";
	$result = mysql_query($query) or die(mysql_error());
	$num	=	mysql_num_rows($result);
	if($num!=0)
	{
		if($line=mysql_fetch_array($result))
		{
			$groupText = $line['groupText'];
		}
	}
	else
	{
		$sq	=	"SELECT gameID FROM adepts_gamesMaster
				 WHERE linkedToCluster='$clusterCode' AND linkedToSDL=$currentSDL AND type='introduction' AND live='Live'";
		$rs =	mysql_query($sq) or die(mysql_error());
		if($rw=mysql_fetch_array($rs))
		{
			$groupText = "[".$rw[0]."/src/index.html]";
		}
	}
	$topicName = $_SESSION['teacherTopicName'];

?>

<?php  include("header.php"); ?>

<title>Mindspark</title>

<?php if($theme==1) { ?>
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/group_instruction/lowerClass.css" rel="stylesheet" type="text/css">
<?php } else if($theme==2) { ?>
    <link rel="stylesheet" href="css/commonMidClass.css" />
    <link rel="stylesheet" href="css/group_instruction/midClass.css" />
<?php } else { ?>
    <link rel="stylesheet" href="css/commonHigherClass.css" />
    <link rel="stylesheet" href="css/group_instruction/higherClass.css" />
<?php } ?>
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<style>
.continue {
        width                   :       125px;
        height                  :       37px;
        background              :       url(images/buttons/question/continue.png) no-repeat -2px 0px;
        margin-top              :       10px;
        margin-right            :       30px;
}

.continue:hover {
        background-position     :       -2px -39px;
}

.continue:active {
        background-position     :       -2px -78px;
}
</style>
<script>
var langType = '<?=$language;?>';

function load(){
	 init();
<?php if($theme==1) { ?>
	var a= window.innerHeight - (155);
	//$('#formContainer').css("height",a+"px");
<?php } else if($theme==2) { ?>
	$("body").animate({ scrollTop: $(document).height() }, 1000);
<?php } else if($theme==3) { ?>
			var a= window.innerHeight - (170);
			var b= window.innerHeight - (610);
			$('#formContainer').css({"height":a+"px"});
			$('#sideBar').css({"height":a+"px"});
			$('#main_bar').css({"height":a+"px"});
			$('#menubar').css({"height":a+"px"});
		<?php } ?>
}

function logoff()
{
	window.location="logout.php";
}
function getHome()
{
	window.location.href	=	"home.php";
}
function submitForm()
{
	document.getElementById('quesform').submit();
}
function init()
{
	/*if(parseInt(document.getElementById("pnlTeacherTopicSelection").offsetHeight)<300)
		document.getElementById("pnlTeacherTopicSelection").style.height = "300px";*/
	setTimeout("logoff()", 600000);	//log off if idle for 10 mins

}
</script>
</head>
<body class="translation" onLoad="load()" onResize="load()">
	<div id="top_bar">
		<div class="logo">
		</div>

        <div id="studentInfoLowerClass" class="forLowerOnly hidden">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span><?=$childName?>&nbsp;</span></a></li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.classSmall">Class</span> <span id="userClass"><?=$childClass.$childSection?></span></div>
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

        <div id="help" style="visibility:hidden">
        	<div class="help"></div>
            <div class="helpText" data-i18n="common.help"></div>
        </div>
        <div id="logout" onClick="logoff();" class="hidden">
        	<div class="logout"></div>
        	<div class="logoutText" data-i18n="common.logout"></div>
        </div>
		<div id="whatsNew" style="visibility:hidden">
            <div class="whatsNew"></div>
            <div class="whatsNewText" data-i18n="common.whatsNew"></div>
        </div>
    </div>

	<div id="container">
    	<div id="info_bar" class="forLowerOnly hidden">
        	<div id="blankWhiteSpace"></div>
             <div id="home">
                <div id="homeIcon" onClick="getHome()"></div>
                <div id="dashboardHeading" class="forLowerOnly"> - <?=$topicName?></div>
                <div class="clear"></div>
            </div>
        </div>
		<div id="info_bar" class="forHigherOnly hidden">
			<div id="topic">
				<div id="home">
                    <div id="homeIcon" onClick="getHome()"></div>
                    <div id="homeText"><span class="removeDecoration" onClick="getHome()" data-i18n="dashboardPage.home"></span> > <font color="#606062"> <?=$topicName?></font></div>
                    <div class="clear"></div>
				</div>
                <div class="clear"></div>
			</div>

			<div id="studentInfo">
            	<div id="studentInfoUpper">
                	<div id="childClassDiv"><span data-i18n="common.class">Class</span>  <?=$childClass.$childSection?></div>
                	<div id="childNameDiv"><?=$Name?></div>
                    <div class="clear"></div>
                </div>
            </div>

            <div class="clear"></div>
            <div id="session">
                <span data-i18n="common.sessionID"></span>: <font color="#39a9e0"><?=$_SESSION["sessionID"]?></font>
            </div>
		</div>
        <div id="info_bar" class="forHighestOnly">
				<a href="home.php" style="text-decoration:none;color:inherit"><div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase" data-i18n="homePage.recent"></span></div>
                </div></a>
				<div class="arrow-1"></div>
            </div>
        <div id="groupInstructionDivMain">
        	<div id="formContainer">
                <form name="quesform" id="quesform" method="post" action="question.php" autocomplete=off>
                    <input type="hidden" name="qcode" id="qcode" value="<?=$qcode?>">
                    <input type="hidden" name="qno" id="qno" value="<?=$currQues?>">
                    <input type="hidden" name="refresh" id="refresh" value="0">
                    <input type="hidden" name="quesCategory" id="quesCategory" value="<?=$quesCategory?>">
                    <input type="hidden" name="showAnswer" id="showAnswer" value="<?=$showAnswer?>">
                    <input type="hidden" name="tmpMode" id="tmpMode" value="<?=$tmpMode?>">
                    <input type="hidden" name="mode" id="mode">

                    <table align="center" width="80%" style="margin-top:15px;">
                    	<tr>
                        	<td class="quesDetails" style="text-align:justify;"><?php echo orig_to_html($groupText,"images","CI")?><br></td>
                        </tr>
                    </table>
                    <br/>
                    <div align='center'>
                        <input type='button' class='button1' onClick="submitForm()" name='btnSubmit' id="btnSubmit" value="Quit">
                        <a href="javascript:submitForm()" class="continue" style="display:block;"></a>
                    </div>
                </form>
            </div>
        </div>
	</div>
<?php include("footer.php"); ?>