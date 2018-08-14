<?php
@include("check1.php");
include_once("constants.php");
//include("functions/functions.php");
include("classes/clsUser.php");
if( !isset($_SESSION['userID'])) {
	header( "Location: error.php");
}
$userID = $_SESSION['userID'];
$Name = explode(" ", $_SESSION['childName']);
$Name = $Name[0];

$objUser = new User($userID);
$schoolCode    = $objUser->schoolCode;
$childClass    = $objUser->childClass;
$childSection  = $objUser->childSection;
$category 	   = $objUser->category;
$subcategory   = $objUser->subcategory;

$baseurl = IMAGES_FOLDER."/newUserInterface/examTips/";
$arrSummarySheet	=	getSummarySheets();

$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");

$Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
$sparkieImage = $_SESSION['sparkieImage'];
?>
<?php include("header.php"); ?>
<title>SUMMARY SHEETS</title>
<link rel="stylesheet" href="css/commonHigherClass.css" />
<link rel="stylesheet" href="css/examTips/higherClass.css" />
<link href="css/colorbox.css" rel="stylesheet" type="text/css">
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script language="javascript" type="text/javascript" src="libs/jquery.colorbox-min.js"></script>

<script>
var langType = '<?=$language;?>';
var click=0;
function load() {
	var a= window.innerHeight - (170);
	$('#topicInfoContainer').css({"height":a+"px"});
	$('#menuBar').css({"height":a+"px"});
	$('#main_bar').css({"height":a+"px"});
	$('#sideBar').css({"height":a+"px"});
	$('#iframe').css({"height":(a-3)+"px"});
	<?php if($Android === false && $iPad === false) { ?>
	$("#topicList").css({"height":(a-50)+"px"});
	<?php } ?>
	<?php if($Android !== false) { ?>
	$('#iframe').css({"height":(a+143)+"px"});
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
	var pdfLink	=	'<?=WHATSNEW."SummurySheets/";?>';
	<?php if($Android === false && $iPad === false) { ?>
    $("#topicList").click(function(){
		var pdfLinkArr	=	$(this).val()[0].split("#$#");
	<?php } else { ?>
	$("#topicList").change(function(){
		var pdfLinkArr	=	$(this).val().split("#$#");
	<?php } ?>
		
		$.post("commonAjax.php","mode=downLoadCount&summuryID="+pdfLinkArr[3],function(data) {
			//save download count
		});
		pdfLink	=	pdfLink+pdfLinkArr[0];
		pdfLink = "https://docs.google.com/viewer?url="+encodeURIComponent(pdfLink)+"&embedded=true";
		$("iframe").attr("src",pdfLink);
		//$("iframe").removeAttr("scrolling");
		$("#topicInfoContainer").hide();
		$("#viewPdfContainer").show();
		//$("#viewPdfContainer").css({"margin-left":"300px"});
		//$("#viewPdfContainer").css({"border":"1px solid"});
	});
});

</script>
</head>
<body class="translation" onLoad="load()" onResize="load()">
<form name="frmTeacherTopicSelection" id="frmTeacherTopicSelection" method="POST">
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
                              <!--      <li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li>-->
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
                <div id="competitiveExamText" class="textUppercase linkPointer" onClick="javascript:window.location.href='summarySheets.php'">SUMMARY SHEETS</div>
                <div class="clear"></div>
            </div>
            <div class="class hidden"> <strong><span data-i18n="common.class">Class</span> </strong>
                <?=$childClass.$childSection;?>
            </div>
            <div class="Name hidden"> <strong>
                <?=$Name?>
                </strong> </div>
            <div class="clear"></div>
            <?php if(strcasecmp($subcategory,"School")!=0 && strcasecmp($subcategory,"Center")!=0) { ?>
            <div id="viewAllTopics">
                <input type="checkbox" id="chkAllTopics" name="chkAllTopics" onClick="showAllTopics()" <?php if($showAllTopics) echo " checked"?>/>
                Click here to see all topics&nbsp;&nbsp;&nbsp;&nbsp;
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
			<?php $style=""; if($_SESSION['rewardSystem']!=1) { $style = "style='position: absolute;background: url(assets/higherClass/dashboard/rewards.png) no-repeat 0 0 !important;width: 50px;height: 50px;margin-top: -30px;margin-left: -50px;'";} ?>
			<a href="src/rewards/rewardsDashboard.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;">
            <div id="drawer5"><div id="drawer5Icon" <?=$style?> class="<?=$sparkieImage?>"><div class="redCircle"><?=$sparkieWon?></div></div>REWARDS CENTRAL</div></a>
           <!-- <a href="viewComments.php?from=links&mode=1">
            <div id="drawer6">
                <div id="drawer6Icon"></div>
                NOTIFICATIONS </div>
            </a>--> </div>
        <div id="tableContainerMain">
            <div id="menuBar" class="forHighestOnly">
                <div id="sideBar">
                    <!--<div id="downloadLink">
                        <a href="">Download (<span id="totalDownloads">0</span>)</a>
                        <input type="hidden" id="summaryID" value="">
                    </div>-->
                </div>
            </div>
            <div id="viewPdfContainer" style="display:none">
            	<iframe id="iframe" src="" frameborder="0" scrolling="no" seamless></iframe>
            </div>
            <div id="topicInfoContainer">
				<div class="head">Select any summary sheet from the list given below.</div>
                <select id="topicList" name="topicCode" <?php if($Android === false && $iPad === false) echo "multiple";?>>
				<?php if($Android === false && $iPad === false) { ?>
                
                <?php } else { ?>
                <option value="" selected>Select</option>
                <?php } ?>
				<?php 
				foreach($arrSummarySheet as $classification=>$details) { ?>
					<optgroup label="<?=$classification?>">
                <?php foreach($details as $summaryID=>$titlePath) { ?>
						<option value="<?=$titlePath["filePath"]."#$#".$titlePath["noOfPages"]."#$#".$titlePath["downloadCount"]."#$#".$summaryID?>" style="cursor:hand;cursor:pointer;"><?=$titlePath["title"]?></option>
                <?php } ?>
    	            </optgroup>
                <?php } ?>
                </select>
            </div>
        </div>
    </div>
</form>

<?php include("footer.php") ?>

<?php
function getSummarySheets()
{
	$arrSummarySheet	=	array();
	$sq	=	"SELECT summuryID,mappedTTs,title,addedBy,topic,filePath,noOfPages,downloadCount FROM adepts_summarySheets ORDER BY topic,title";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$arrSummarySheet[$rw["topic"]][$rw["summuryID"]]["title"]	=	$rw["title"];
		$arrSummarySheet[$rw["topic"]][$rw["summuryID"]]["filePath"]	=	$rw["filePath"];
		$arrSummarySheet[$rw["topic"]][$rw["summuryID"]]["noOfPages"]	=	$rw["noOfPages"];
		$arrSummarySheet[$rw["topic"]][$rw["summuryID"]]["downloadCount"]	=	$rw["downloadCount"];
	}
	return $arrSummarySheet;
}
?>


<?php

function download_file( $fullPath ) {

  // Must be fresh start
  if( headers_sent() )
    die('Headers Sent');

  // Required for some browsers
  if(ini_get('zlib.output_compression'))
    ini_set('zlib.output_compression', 'Off');

  // File Exists?
  if( file_exists($fullPath) ){

    // Parse Info / Get Extension
    $fsize = filesize($fullPath);
    $path_parts = pathinfo($fullPath);
    $ext = strtolower($path_parts["extension"]);

    // Determine Content Type
    switch ($ext) {
      case "pdf": $ctype="application/pdf"; break;
      case "exe": $ctype="application/octet-stream"; break;
      case "zip": $ctype="application/zip"; break;
      case "doc": $ctype="application/msword"; break;
      case "xls": $ctype="application/vnd.ms-excel"; break;
      case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
      case "gif": $ctype="image/gif"; break;
      case "png": $ctype="image/png"; break;
      case "jpeg":
      case "jpg": $ctype="image/jpg"; break;
      default: $ctype="application/force-download";
    }

    header("Pragma: public"); // required
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false); // required for certain browsers
    header("Content-Type: $ctype");
    header("Content-Disposition: attachment; filename=\"".basename($fullPath)."\";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$fsize);
    ob_clean();
    flush();
    readfile( $fullPath );

  } else
    die('File Not Found');
}
?>