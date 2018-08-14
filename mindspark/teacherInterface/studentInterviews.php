<?php

include("header.php");
include("../slave_connectivity.php");
set_time_limit(0);
include("../userInterface/functions/functions.php");
error_reporting(E_ERROR);

	$userID = $_SESSION['userID'];

    if(!isset($_REQUEST['ttCode']) || !isset($_SESSION['userID']))
    {
    	echo "You are not authorised to access this page!";
    	exit;
    }
$counter=0;
$keys = array_keys($_REQUEST);
foreach($keys as $key)
{
	${$key} = $_REQUEST[$key] ;
}



	$query = "SELECT teacherTopicDesc, mappedToTopic, customTopic, parentTeacherTopicCode FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$ttCode'";
	$result = mysql_query($query);
	$line = mysql_fetch_array($result);
	$teacherTopicDesc = $line[0];
	$topicCode = $line[1];
	$customTopic = $line[2];
	$parentTeacherTopicCode = $line[3];

//echo  "cls == ".$cls." section == ".$section."  ttCode == ".$ttCode."  clusterCode == ".$clusterCode;

?>

<title>Student Interviews</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/studentInterviews.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<link href="css/colorbox.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="libs/jquery.colorbox-min.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script>
	var langType = '<?=$language;?>';
	function load(){
		var fixedSideBarHeight = window.innerHeight;
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#fixedSideBar").css("height",fixedSideBarHeight+"px");
		$("#sideBar").css("height",sideBarHeight+"px");
		$("#questionContainer").css("min-height",(containerHeight-160)+"px");
		$("#classes").css("font-size","1.4em");
		$("#classes").css("margin-left","40px");
		$(".arrow-right").css("margin-left","10px");
		$(".rectangle-right").css("display","block");
		$(".arrow-right").css("margin-top","3px");
		$(".rectangle-right").css("margin-top","3px");
	}
</script>
<script type="text/javascript">
$(document).ready(function(e) {
	$(".titleLink,.videoLink").colorbox({inline:true, width:"80%", height:"95%",
		onClosed:function(){
			$("#iFrame").attr("src","");
		},
		onOpen: function(){
			tryingToUnloadPage = false;				
		}
	});
});
function setURL(linkText,titleText,interviewno)
{
    $.ajax('ajaxRequest.php?mode=studentInterviewsCounter&interviewID=' + interviewno,
                    {
                        method: 'get',
                        success: function (transport) {
                    
                        }
                    }
                    );
	if(linkText != "")
		linkText = "http://docs.google.com/viewer?url="+encodeURI(linkText)+"&embedded=true";
	$("#iFrame").attr("src",linkText);
	$("#modelTitleText").text(titleText);
}

function showModalVideo(videoElem)
{
	if($.browser.msie)
		$(".fullScreenButton").hide();
	videoElem[0].pause();
	var videoTitle = videoElem.next().text();
	$("#videoTitle").text(videoTitle);
	var videoPoster = videoElem.attr("poster");
	$("#videoFile1000").attr("poster",videoPoster);
	var mp4SRC = videoElem.find("source:first").attr("src");
	$("#mp4SRC").attr("src",mp4SRC);
	var ogvSRC = videoElem.find("source:last").attr("src");
	$("#ogvSRC").attr("src",ogvSRC);
	$(".videoLink").click();
	$("#videoFile1000")[0].play();
}
function changePoster(i)
{
	var initialPath = "<?=VIDEO_THUMB_PATH?>";
	document.getElementById("videoFile"+i).setAttribute("poster",initialPath+"novideo.png");
	document.getElementById("videoFile"+i).removeAttribute("controls");
}
function showFullScreen()
{
	if($.isFunction($("#videoFile1000")[0].webkitEnterFullscreen))
	{
		$("#videoFile1000")[0].webkitEnterFullscreen();
	}
	 else if($.isFunction($("#videoFile1000")[0].mozRequestFullScreen))
	{
		$("#videoFile1000")[0].mozRequestFullScreen();
	}
	return false;
}

</script>
</head>
<body class="translation" onLoad="load()" onResize="load()">
	<?php include("eiColors.php") ?>
	<div id="fixedSideBar">
		<?php include("fixedSideBar.php") ?>
	</div>
	<div id="topBar">
		<?php include("topBar.php") ?>
	</div>
	<div id="sideBar">
			<?php include("sideBar.php") ?>
	</div>

	<div id="container">
		<div id="trailContainer">
			<div id="headerBar">
				<div id="pageName">
					<div class="arrow-black"></div>
					<div id="pageText">TOPIC RESEARCH</div>
				</div>
			</div>
			
			<table id="childDetails" align="top">
				<td width="18%" id="sampleQuestions" class="pointer"><a href="sampleQuestions.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$flow?>"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition" style="width:125px;">SAMPLE QUESTIONS</div></div></div></a></td>
		        <td width="18%" id="wrongAnswers" class="pointer"><a href="cwa.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$flow?>"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition">COMMON WRONG ANSWERS</div></div></div></a></td>
		        <td width="18%" id="researchStudies" class="pointer"><a href="researchPapers.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$flow?>"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition">SUMMARY OF RESEARCH STUDIES</div></div></div></a></td>
				<td width="22%" id="studentInterviews" class="pointer"><a href="studentInterviews.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$flow?>"><div id="setHeight"><div class="smallCircle red"><div class="textRed pointer setPosition">SUMMARY OF STUDENT INTERVIEWS</div></div></div></a></td>
				<td width="22%" id="studentInterviews" class="pointer"><a href="misconceptionVideos.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$flow?>"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition">MISCONCEPTION VIDEOS</div></div></div></a></td>
			</table>
			
			<table id="pagingTable">
		        <td width="35%"><?= $teacherTopicDesc?></td>
			</table>
			
			<div id="questionContainer">
				<div id="rightBody">
    	<h2 align="center">Summary of Student Interviews</h2>
<?php
	if($customTopic==1){
		$ttCode = $parentTeacherTopicCode;
	}
	$sql = "SELECT interviewID, title, description, interviewer, link, filename, videoLink FROM adepts_studentInterviews WHERE FIND_IN_SET('$ttCode',mappedTTs)";
	$result = mysql_query($sql);
	if(mysql_num_rows($result) > 0)
	{ 
		$idcountforrefferal =1;
		while($row = mysql_fetch_assoc($result))
		{
			$counter++;
			$title = addslashes($row['title']);
			$description = $row['description'];
            $interviewID = $row['interviewID'];
			$interviewer = $row['interviewer'];
			$displayname = explode(".",$interviewer);
			$interviewer = ucfirst($displayname[0]);
			$link = addslashes($row['filename']);
			$videoLink = addslashes($row['videoLink']);
			$videoPath = LOCAL_VIDEO_PATH.$ttCode."/StudentInterviews/".$videoLink;
			
			// Prepare interviewer string..
			$interviewerSTR = $interviewer;
			if($interviewer != "")
			{
				$interviewerSTR = "interviewed by ";
				$interviewers = explode(",",$interviewer);
				
				if(count($interviewers) > 0)
				{
					for($i=0;$i<count($interviewers);$i++)
					{
						if($i == count($interviewers)-2)
							$interviewerSTR .= $interviewers[$i]." and ";
						else
							$interviewerSTR .= $interviewers[$i].", ";
					}
					$interviewerSTR = substr($interviewerSTR,0,-2);
				}
				else
					$interviewerSTR = $interviewers[0];
			}
			
			?>

        	<div class="researchModule">
            	<table>
                  <tr>
                    <td valign="top" style="padding-right:10%;">
						<?php if(SERVER_TYPE=='LIVE') { ?>
                        <a href="#modelView" onClick="setURL('<?=studentInterviewFullPath.$link?>','<?=$title?>','<?=$interviewID?>')" class="titleLink" id="mytopicpopup-<?=$idcountforrefferal;?>"><?=$title?></a>
						<?php } else { ?>
						<a href="<?=studentInterviewFullPath.$link?>" class="titleLink" id="mytopicpopup-<?=$idcountforrefferal;?>" target="_blank"><?=$title?></a>
						<?php } ?>
                        <!--<span class="interviewerText"><?=$interviewerSTR?></span>-->
                        <br />
                        <span class="descText"><?=$description?></span>
                    </td>
                    <td>
					<?php if($videoLink!=''){
					?>
                        <video id="videoFile<?=$counter?>" controls height="120" width="180" poster="images/playvid.jpg" preload="metadata" onPlay="showModalVideo($(this))" onError="changePoster(<?=$counter?>)">
                            <source src="<?=$videoPath?>.ogv" type="video/ogg" />
							<source src="<?=$videoPath?>.mp4" type="video/mp4" onError="changePoster(<?=$counter?>)"/>
                            Unable to play video :(
                        </video>
                        <span style="display:none"><?=$title?></span>
					<?php } ?>
                    </td>
                  </tr>
                </table>
				<br/>
			</div>
		<?php
		$idcountforrefferal ++;
		}
		?>
<?php
	}
	else
	{
		?>
        <h3>No Records Found!</h3>
        <?php
	}
?>
</div>
<a href="#videoBox" class="videoLink">Dummy Link</a>
<div style="display:none">
    <div id="modelView">
        <div id="modelTitleText" style="float:left; width:100%;">
        </div>
        <div style="clear:both"></div>
        <div style="float:left; width:100%; height:90%">
        	<iframe id="iFrame" class="iFrame" height="95%" width="100%" src="" allowtransparency="true" style="height:95%"></iframe>
        </div>
    </div>
</div>
<div style="display:none">
    <div id="videoBox">
        <div id="videoTitleDiv">
        	<h2 id="videoTitle"><?=$title?></h2>
            <a href="javascript:void(0)" onClick="showFullScreen()" class="fullScreenButton" title="View fullscreen"></a>
		</div>
        <div id="videoTitleDiv">
            <video id="videoFile1000" controls width="480" height="360" poster="images/playvid.jpg">
                <source src="<?=$videoPath?>.mp4" id="mp4SRC" type="video/mp4" />
                <source src="<?=$videoPath?>.ogv" id="ogvSRC" type="video/ogg" />
            </video>
        </div>
    </div>
</div>
			</div>
			
		</div>
	</div>
<?php 
if(isset($_REQUEST["mytopicpagerefferal"])){
	echo '<script>';
	echo '$(window).load(function(){$("#mytopicpopup-1").trigger("click");});' ;
	echo '</script>';
}
?>
<?php include("footer.php") ?>