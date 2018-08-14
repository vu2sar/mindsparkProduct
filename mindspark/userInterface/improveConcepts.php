<?php
set_time_limit(0);
@include("check1.php");
include_once("constants.php");
include("classes/clsUser.php");error_reporting(1);
include("functions/examCornerFunction.php");
if( !isset($_SESSION['userID'])) {
	header( "Location: error.php");
	exit();
}
error_reporting(1);
$userID = $_SESSION['userID'];

$Name = explode(" ", $_SESSION['childName']);
$Name = $Name[0];

$objUser = new User($userID);
$schoolCode    = $objUser->schoolCode;
$childClass    = $objUser->childClass;
$childSection  = $objUser->childSection;
$category 	   = $objUser->category;
$subcategory   = $objUser->subcategory;

$topicsActivated	=	array();

$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
$Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
$sparkieImage = $_SESSION['sparkieImage'];
if(isset($_POST["mode"]) && ($_POST["mode"]=="bucketing" || $_POST["mode"]=="choiceScreen" ))
{
	unset($_SESSION['bucketAttemptID'],$_SESSION['bucketClusterCode'],$_SESSION['examCornerCluster'],$_SESSION["importantQuestions"]);
	$ttCode	=	$_POST["ttCode"];
	$bronzeBucket	=	array();
	$silverBucket	=	array();
	$goldBucket		=	array();
	
	$arrayClusterList	=	getClusterForBucketing($userID,$ttCode);
	$arrayClusterBucketing	=	bucketLogicCalculation($userID,$ttCode,$arrayClusterList);
	$bronzeBucket	=	$arrayClusterBucketing[0];
	$silverBucket	=	$arrayClusterBucketing[1];
	$goldBucket		=	$arrayClusterBucketing[2];
	
	$arrayMovedCluster	=	getMovedCluster($arrayClusterBucketing,$userID,$ttCode);
}
else
{
	$topicsActivated	=	getTopicAttempted($userID, $childClass, $childSection, $category, $subcategory, $schoolCode, 2);
}
	
?>
<?php include("header.php"); ?>
<title>IMPROVE YOUR CONCEPTS</title>
<link rel="stylesheet" href="css/commonHigherClass.css" />
<link rel="stylesheet" href="css/examTips/higherClass.css" />
<link rel="stylesheet" type="text/css" href="css/examCorner/dd.css" />
<script type="text/javascript" src="libs/examCorner/jquery.js"></script>
<script type="text/javascript" src="libs/examCorner/jquery.dd.js"></script>
<script type="text/javascript" src="libs/examCorner/dd.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script src="libs/closeDetection.js"></script>
<script>
var langType = '<?=$language;?>';
var click=0;
function load(){
	var a= window.innerHeight - (170);
	$('#topicInfoContainer').css({"height":(a)+"px"});
	$('#menuBar').css({"height":a+"px"});
	$('#main_bar').css({"height":a+"px"});
	$('#sideBar').css({"height":a+"px"});
	<?php if($Android === false && $iPad === false) { ?>
	$("#topicList").css({"height":(a-80)+"px"});
	<?php } ?>
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
		$("#main_bar").animate({'width':'22px'},600);
		$("#plus").animate({'margin-left':'4px'},600);
		$("#vertical").css("display","block");
		click=0;
	}
}

$(document).ready(function(e) {
	<?php if($Android === false && $iPad === false) { ?>
	$("#topicList").click(function() {
		setTryingToUnload();
		if($(this).val()!="" && $(this).val()!=null)
			getClusterBucket($(this).val());
	});
	<?php } else { ?>
	$("#topicList").change(function() {
		setTryingToUnload();
		if($(this).val()!="" && $(this).val()!=null)
			getClusterBucket($(this).val());
	});
	<?php } ?>
	
	$(".clusterList").click(function() {
		setTryingToUnload();
		if($(this).val()!="" && $(this).val()!=null)
		{
			startClusterAttempt($(this).val(),$(this).attr("id"));
		}
	});

	try {
		$(".clusterList").msDropDown({mainCSS:'dd2'});
		$("#ver").html($.msDropDown.version);
	} catch(e) {
		alert("Error: "+e.message);
	}
	
	$("#bronze_child").css("border-left","5px solid red");
	$("#gold_child").css("border-left","5px solid green");
	$("#silver_child").css("border-left","5px solid yellow");
	$("#gold_msa_0,#silver_msa_0,#bronze_msa_0").hide();
});


function getClusterBucket(topicCode)
{
	$("#topicList").hide();
	$("#pnlLoading").show();
	$("#mode").val("bucketing");
	$("#ttCode").val(topicCode);
	$("#frmTeacherTopicSelection").submit();
}

function startClusterAttempt(clusterCode,clusterType)
{
	$("#mode").val("startExamCornerCluster");
	$("#clusterCode").val(clusterCode);
	$("#clusterType").val(clusterType);

	$("#frmTeacherTopicSelection").attr("action","controller.php");
	$("#frmTeacherTopicSelection").submit();
}
$(document).ready(function(e) {
    var heightSet	=	$("#gold_child").width();
	$("#silver_child").width(heightSet);
	$("#bronze_child").width(heightSet);
});
</script>
</head>
<body class="translation" onLoad="load()" onResize="load()">
<form name="frmTeacherTopicSelection" id="frmTeacherTopicSelection" method="POST">
    <input type="hidden" name='mode' id="mode" value="">
    <input type="hidden" name="ttCode" id="ttCode" value="<?=$ttCode?>">
    <input type="hidden" name="clusterCode" id="clusterCode" value="">
    <input type="hidden" name="clusterType" id="clusterType" value="">
    <?php
        if(isset($_POST["mode"]) && $_POST["mode"]=="choiceScreen" )
        {
            echo '<input type="hidden" name="fromChoiceScreen" id="fromChoiceScreen" value="1">';
        }
    ?>    
</form>
    <div id="top_bar">
        <div class="logo"> </div>
        <div id="studentInfoLowerClass" class="forLowerOnly hidden">
            <div id="nameIcon"></div>
            <div id="infoBarLeft">
                <div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC">
                                <?=$objUser->childName?>
                                &nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a href='javascript:void(0)'><span data-i18n="homePage.myDetails"></span></a></li>
                                    <li><a href='javascript:void(0)'><span data-i18n="homePage.changePassword"></span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass">
                    <?=$childClass.$childSection?>
                    </span></div>
            </div>
        </div>
        <div id="studentInfoLowerClass" class="forHighestOnly">
            <div id="nameIcon"></div>
            <div id="infoBarLeft">
                <div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?php echo $Name ?>&nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a href='myDetailsPage.php'><span data-i18n="homePage.myDetails"></span></a></li>
                               <!--     <li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li>-->
                                    <li><a href='changePassword.php'><span data-i18n="homePage.changePassword"></span></a></li>
                                    <li><a href='whatsNew.php'><span data-i18n="common.whatsNew"></span></a></li>
                                    <li><a href='logout.php'><span data-i18n="common.logout"></span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass">
                    <?=$childClass.$childSection?>
                    </span></div>
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
                <div id="homeIcon" class="linkPointer" onClick="getHome()"></div>
                <div id="dashboardHeading" class="forLowerOnly">&ndash;&nbsp;<span class="textUppercase" data-i18n="dashboardPage.dashboard"></span></div>
                <div class="clear"></div>
            </div>
        </div>
        <div id="info_bar" class="forHigherOnly">
            <div id="topic">
                <div id="home">
                    <div id="homeIcon" onClick="getHome()"></div>
                    <div id="homeText" class="hidden"><span onClick="getHome()" class="textUppercase" data-i18n="dashboardPage.home"></span> > <font color="#606062"> <span class="textUppercase" data-i18n="dashboardPage.dashboard"></span></font></div>
                </div>
                <div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase"><a href="examCorner.php">EXAM CORNER</a></span></div>
                </div>
                <div class="arrow-right"></div>
                <a href="improveConcepts.php" class="removeDecoration" style="text-decoration:none;color:inherit"><div id="competitiveExamText" class="textUppercase">IMPROVE YOUR CONCEPTS</div></a>
                <div class="clear"></div>
            </div>
            <div class="class hidden"> <strong><span data-i18n="common.class">Class</span> </strong>
                <?=$childClass.$childSection?>
            </div>
            <div class="Name hidden"> <strong>
                <?=$Name?>
                </strong> </div>
            <div class="clear"></div>
            <?php if(strcasecmp($subcategory,"School")!=0 && strcasecmp($subcategory,"Center")!=0) { ?>
            <div id="viewAllTopics">
                <input type="checkbox" id="chkAllTopics" name="chkAllTopics" onClick="showAllTopics()" <?php if($showAllTopics) echo " checked"?>/>
                Click here to see all topics&nbsp;&nbksp;&nbsp;&nbsp;
                <?php } ?>
            </div>
            <a href="sessionWiseReport.php" class="removeDecoration hidden">
            <div id="sessionWiseReport" class="textUppercase" data-i18n="dashboardPage.sessionWise"></div>
            </a> </div>
        <div id="hideShowBar" class="forHigherOnly hidden" onClick="hideBar();">-</div>
        <div id="main_bar" class="forHighestOnly">
            <div id="drawer1"> <a href="activity.php" style="text-decoration:none;color:inherit">
                <div id="drawer1Icon"></div>
                ACTIVITIES </div>
            </a> <a href="dashboard.php">
            <div id="drawer2">
                <div id="drawer2Icon"></div>
                DASHBOARD </div>
            </a> <a href="home.php">
            <div id="drawer3">
                <div id="drawer3Icon"></div>
                HOME </div>
            </a>
            <a href="explore.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;color:inherit"><div id="drawer4"><div id="drawer4Icon"></div>EXPLORE ZONE
			</div></a>
            <div id="plus" onClick="openMainBar();">
                <div id="vertical"></div>
                <div id="horizontal"></div>
            </div>
			<a href="src/rewards/rewardsDashboard.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;">
            <div id="drawer5"><div id="drawer5Icon" style='<?php if($_SESSION['rewardSystem']!=1) { echo 'position: absolute;background: url("assets/higherClass/dashboard/rewards.png") no-repeat 0 0 !important;width: 50px;height: 50px;margin-top: -30px;margin-left: -50px;";';} ?>' class="<?=$sparkieImage?>"><div class="redCircle"><?=$sparkieWon?></div></div>REWARDS CENTRAL</div></a>
            <!--<a href="viewComments.php?from=links&mode=1">
            <div id="drawer6">
                <div id="drawer6Icon"></div>
                NOTIFICATIONS </div>
            </a>--> </div>
        <div id="tableContainerMain">
            <div id="menuBar" class="forHighestOnly">
                <div id="sideBar"> </div>
            </div>
		<?php if(count($topicsActivated) == 0 && isset($_POST["ttCode"])) { ?>
            <div id="topicInfoContainer">
            <?php if($_POST['onlyBronze']) { ?>
                <div class="head" id="text1">Select a concept under the weak section to practice the concept. A concept will move to the strong, weak or average sections based on your performance in that particular concept.</div>
            <?php } else{ ?>
            	<div class="head" id="text1">Select any concept from strong, average or weak section to practice the concept. The concept will get shifted to strong, weak or average based on your performance in that particular concept.</div>

                <div id="goldBucket">
                	<div id="imgGold"></div>
                	<div id="" class="levelInfo">LEVEL:STRONG</div>
                	<select id="gold" class="clusterList" name="topicCode" multiple="multiple" style="height:105px;width:96%">
                    	<option title="assets/examCorner/icon-ok.png"  value="" selected></option>
                <?php  $arrDescGoldCluster	=	getClusterDesc($goldBucket);
					foreach($goldBucket as $clusterCode) { ?>
                        <option value="<?=$clusterCode?>" title="assets/examCorner/<?php if(in_array($clusterCode,$arrayMovedCluster[1])) echo "redToGreen.png";
								 else if(in_array($clusterCode,$arrayMovedCluster[3])) echo "yellowToGreen.png";
								 else echo "icon-ok.png"; ?>">
							
						<?=$arrDescGoldCluster[$clusterCode]?></option>
                <?php } ?>
                	</select>
				</div>
                <div id="silverBucket">
                	<div id="imgSilver"></div>
                	<div id="" class="levelInfo" style="padding-top:10px">LEVEL:MODERATE</div>
                    <select id="silver" class="clusterList" name="topicCode" multiple="multiple" style="height:105px;width:96%">
                    	<option title="assets/examCorner/icon-ok.png" value="" selected></option>
                <?php $arrDescSilverCluster	=	getClusterDesc($silverBucket);
					foreach($silverBucket as $clusterCode) { ?>
                        <option title="assets/examCorner/<?php if(in_array($clusterCode,$arrayMovedCluster[0])) echo "redToYellow.png";
								 else if(in_array($clusterCode,$arrayMovedCluster[5])) echo "greenToYellow.png"; 
								 else echo "icon-ok.png"; ?>" value="<?=$clusterCode?>">
								 <?=$arrDescSilverCluster[$clusterCode]?></option>
                <?php } ?>
                	</select>
				</div>
            <?php } ?>
                <div id="bronzeBucket">
                	<div id="imgBronze"></div>
                	<div id="" class="levelInfo" style="padding-top:10px">LEVEL:WEAK</div>
                    <select class="clusterList" id="bronze" name="topicCode" multiple="multiple" style="height:105px;width:96%">
                    	<option title="assets/examCorner/icon-ok.png" value="" selected></option>
                <?php $arrDescBronzeCluster	=	getClusterDesc($bronzeBucket);
					foreach($bronzeBucket as $clusterCode) { ?>
                        <option value="<?=$clusterCode?>" title="assets/examCorner/<?php if(in_array($clusterCode,$arrayMovedCluster[2])) echo "yellowToRed.png";
								 else if(in_array($clusterCode,$arrayMovedCluster[4])) echo "greenToRed.png"; 
								 else echo "icon-ok.png"; ?>"><?=$arrDescBronzeCluster[$clusterCode]?></option>
                <?php } ?>
                	</select>
				</div>
            </div>
		<?php }
		else if(count($topicsActivated)==0) { ?>
			<div id="topicInfoContainer">
				<br><br><br><br>
            	<div class="head" id="text2">You will get access to this section once you have completed half of a topic.</div>
			</div>	
		<?php } else { ?>
            <div id="topicInfoContainer">
            	<div class="head" id="text2">Select a topic from the list given below.</div>
                <select id="topicList" name="topicCode" <?php if($Android === false && $iPad === false) echo "multiple";?>  >
                <?php if($Android === false && $iPad === false) { ?>
                
                <?php } else { ?>
                <option value="" selected>Select</option>
                <?php } ?>
                <?php $classification=""; 
				foreach($topicsActivated as $topicCode=>$details) { 
					if($classification != $details["classification"])
					{
						if($classification!="")
						{ ?>
						</optgroup>
						<?php
						}
						$classification = $details["classification"];
						?>
						<optgroup label="<?=$details["classification"]?>" value="">
					<?php } ?>
					<option value="<?=$topicCode?>" style="cursor:pointer"><?=$details["topicName"]?></option>
                <?php } ?>
                </optgroup>
                </select>
                <div id="pnlLoading">
                    <div align="center" class="quesDetails"><br/><br/><br/><br/><p>Loading, please wait...<br/><img src="assets/loader.gif"></p></div>
                </div>
            </div>
		<?php } ?>
        </div>
    </div>
<?php include("footer.php") ?>