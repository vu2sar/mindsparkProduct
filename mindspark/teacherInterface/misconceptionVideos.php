<?php

include("header.php");

set_time_limit(0);
include("../userInterface/functions/functions.php");
error_reporting(E_ERROR);



	$userID = $_SESSION['userID'];
	$user   = new User($userID);

	
	$schoolCode= $_SESSION['schoolCode'];
	$user_agent     =   $_SERVER['HTTP_USER_AGENT'];

	$category = $user->category;
	$subcategory =  $user->subcategory;

	$getOS= getOSforIP();
	if($getOS=="useIP"){
		$query = "SELECT ip FROM adepts_apacheInstallation WHERE schoolCode='$schoolCode' order by lastModified Desc limit 1";
		$result = mysql_query($query);
		$line = mysql_fetch_array($result);
		if($line[0]!=""){
			$pathForVideos="http://".$line[0]."/videos/";
		}else{
			$pathForVideos=LOCAL_VIDEO_PATH;
		}
	}else{
		$pathForVideos=LOCAL_VIDEO_PATH;
	}
	
	



if(!isset($_REQUEST['ttCode']) || !isset($_SESSION['userID']))
{
	echo "You are not authorised to access this page!";
	exit;
}
$keys = array_keys($_GET);
foreach($keys as $key)
{
	${$key} = $_GET[$key] ;
}

if($category=='School Admin' && $subcategory=='All')
		$pathForVideos = VIDEOS_FOLDER;
		
if(SERVER_TYPE=="LOCAL")
	$pathForVideos = "http://localhost/mindspark/videos/";

$query = "SELECT teacherTopicDesc, parentTeacherTopicCode FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$ttCode'";
	$result = mysql_query($query);
	$line = mysql_fetch_array($result);
	$teacherTopicDesc = $line[0];
	if($line[1]!=""){
		$topicCode = $line[1];
	}else{
		$topicCode = $ttCode;
	}
		$sql = "SELECT thumb, videoTitle, videoType, videoFile, videoID FROM adepts_msVideos WHERE mappingType in ('topic','teacherTopic') AND mappingID='$topicCode' AND videoType IN (1,2)";
		$result = mysql_query($sql);
		if($row = mysql_fetch_assoc($result))
		{
			array_push($misconceptionVideoArr,$row);
			$noOfMisconceptionVideos++;
		}

?>

<title>Misconception Videos</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/misconceptionVideos.css" rel="stylesheet" type="text/css">
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
var isIntroComplete = false;
var pageNo = 1;
$(document).ready(function(e) {
	showVideos();
	$(".videoLink").colorbox({inline:true, width:"auto", height:"auto",
		onComplete:function(){
			$("#videoFile1000").attr("controls",true);
			$("#videoFile1000")[0].play();
		},
		onClosed:function(){
			if(!isIntroComplete)
				showMoreVideos();
		},
		onOpen: function(){
			tryingToUnloadPage = false;				
		}
	});
	$(".nextLink").click(function(e) {
        pageNo++;
		showVideos();
		$(".prevLink").show();
		$(".prevLink").css("display","inline-block");
		var lastPage = Math.ceil(totalVideos / 5);
		if(pageNo == lastPage)
			$(".nextLink").hide();
    });
	$(".prevLink").click(function(e) {
        pageNo--;
		showVideos();
		$(".nextLink").show();
		$(".nextLink").css("display","inline-block");
		if(pageNo == 1)
			$(".prevLink").hide();
    });
});
function showVideos()
{
	//$("#misconceptionVideo").find("tr").show();
	var minSrno = ((pageNo - 1) * 5);
	var maxSrno = (pageNo * 5) - 1;
	$("#misconceptionVideo").find("tr.connectedVideo").hide();
	$("#misconceptionVideo").find("tr.mainVideo").each(function(index, element) {
        if(index >= minSrno && index <= maxSrno)
		{
			$(this).show();
			$(this).nextAll().each(function(index1, element1) {
                if($(this).hasClass("mainVideo"))
					return false;
				else
					$(this).show();
            });
		}
		else
			$(this).hide();
    });
	$("#misconceptionVideo").find("tr:first").show()
}
var like =0;
var dislike=0;
var newCount =0;
function likeVideo(videono){
	if(like!=1 && dislike!=1){
		$.ajax('ajaxRequest.php?mode=misconceptionVideosLikeCounter&videoID=' + videono,
	    {
	        method: 'get',
	        success: function (transport) {
	            console.log(transport);

	        }
	    }
	    );
		newCount = parseInt($("#likeCnt").html())+1;
		$("#likeCnt").text(newCount);
		like=1;
	}else{
		if(like==1){
			alert("You have already liked this video!");
		}else{
			alert("You have already disliked this video!");
		}
	}
}
function dislikeVideo(videono){
	if(like!=1 && dislike !=1){
	$.ajax('ajaxRequest.php?mode=misconceptionVideosDislikeCounter&videoID=' + videono,
    {
        method: 'get',
        success: function (transport) {
            console.log(transport);

        }
    }
    );
	newCount = parseInt($("#dislikeCnt").html())+1;
	$("#dislikeCnt").text(newCount);
	dislike=1;
	}else{
		if(like==1){
			alert("You have already liked this video!");
		}else{
			alert("You have already disliked this video!");
		}
	}
}
function showModalVideo(videoElem,videoTitle,videono,likeCnt,dislikeCnt,counter,videoID)
{
	like =0;
	dislike=0;
	newCount =0;
	var counter=counter;
	if($.browser.msie)
		$(".fullScreenButton").hide();
	videoElem[0].pause();
	var videoLinkOgv=$("#ogv"+counter).attr("class");
	var videoLinkMp4=$("#mp4"+counter).attr("class");
	$("#ogv"+counter).attr("src",videoLinkOgv);
	$("#mp4"+counter).attr("src",videoLinkMp4);
	
	var videoLinkOgvLive=$("#ogvLive"+counter).attr("class");
	var videoLinkMp4Live=$("#mp4Live"+counter).attr("class");
	$("#ogvLive"+counter).attr("src",videoLinkOgvLive);
	$("#mp4Live"+counter).attr("src",videoLinkMp4Live);
	
	
	$.ajax('ajaxRequest.php?mode=misconceptionVideosCounter&videoID=' + videono,
    {
        method: 'get',
        success: function (transport) {
            console.log(transport);

        }
    }
    );
	$("#videoTitle").text(videoTitle);
	$("#showComment").attr("href","Comments.php?mode=addacomment&videono="+videono+"&videoname="+encodeURIComponent(videoTitle));
	$("#like").attr("onclick","likeVideo("+videono+")");
	$("#dislike").attr("onclick","dislikeVideo("+videono+")");
	if(newCount!=0){
		if(like==1){
			$("#likeCnt").html(newCount);
			$("#dislikeCnt").text(dislikeCnt);
		}else{
			$("#dislikeCnt").html(newCount);
			$("#likeCnt").text(likeCnt);
		}
	}else{
		$("#dislikeCnt").text(dislikeCnt);
		$("#likeCnt").text(likeCnt);
	}

	var videoPoster = videoElem.attr("poster");
	var mp4SRC = videoElem.find("source:eq(0)").attr("src");
	var ogvSRC = videoElem.find("source:eq(1)").attr("src");
	var mp4SRCLive = videoElem.find("source:eq(2)").attr("src");
	var ogvSRCLive = videoElem.find("source:eq(3)").attr("src");
	$("#videoDiv").html('<video id="videoFile1000" controls width="480" height="360" poster="'+videoPoster+'" onError="changePoster(1000,this)" onEnded="introVideoComplete()" onPause="introVideoComplete()"><source src="'+mp4SRC+'" id="mp4SRC" type="video/mp4" onError="changePoster(1000,this)"/><source src="'+ogvSRC+'" id="ogvSRC" type="video/ogg" onError="changePoster(1000,this)" /><source src="'+mp4SRCLive+'" id="mp4SRCLive" type="video/mp4" onError="changePoster(1000,this)" /><source src="'+ogvSRCLive+'" id="ogvSRCLive" type="video/ogg" onError="changePoster(1000,this)" /></video>');
	$(".videoLink").click();
	$("#nextVideoLink").hide();
}
function changePoster(i,a)
{
	var initialPath = "<?=VIDEO_THUMB_PATH?>";
    var liveVideoPath = "<?= LIVE_VIDEO_DOMAIN ?>";
    var videoPath = $("#videoFile"+i+" source").attr('src');
    var videoDomain = new URL(videoPath).hostname;
    var changePath = false;
    
    <?php 
	//echo date("H");
    //if(date("H")>=16 || date("H")<=7)
        //echo 'changePath=true;';
    ?>
    if(videoDomain!=liveVideoPath && changePath==true)
    {
        $("#videoFile"+i+" source").each(function() {
            videoPath = $(this).attr('src');
	        var newPath = videoPath.replace(videoDomain,liveVideoPath);
	        $(this).attr('src',newPath);
	        $(this).load();
        });            
        $(".videoLink").click();
    }
    else if ($(a).is('#ogvSRCLive') || $(a).is('#videoFile1000') )
    {
		document.getElementById("videoFile"+i).setAttribute("poster",initialPath+"novideo.png");
		document.getElementById("videoFile"+i).removeAttribute("controls");
    }
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
function introVideoComplete()
{
	if(!isIntroComplete)
	{
		$("#nextVideoLink").show();
	}
}
function nextVideos()
{
	$.colorbox.close();
}
function showMoreVideos()
{
	$(".anchorButton").hide();
	if(totalVideos > 5)
	{
		$(".nextLink").show();
		$(".nextLink").css("display","inline-block");
	}
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
				<td width="22%" id="studentInterviews" class="pointer"><a href="studentInterviews.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$flow?>"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition">SUMMARY OF STUDENT INTERVIEWS</div></div></div></a></td>
				<td width="22%" id="studentInterviews" class="pointer"><a href="misconceptionVideos.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$flow?>"><div id="setHeight"><div class="smallCircle red"><div class="textRed pointer setPosition">MISCONCEPTION VIDEOS</div></div></div></a></td>
			</table>
			
			<table id="pagingTable">
		        <td width="35%">Misconceptions in <?= $teacherTopicDesc?></td>
			</table>
			
			<div id="questionContainer">
			<!--<div style='color:red;'>*If the video is not available in your location, please come back between 4pm-7am to view them online.</div>-->
<script type="text/javascript">
var totalVideos = <?=$noOfMisconceptionVideos-1?>;
</script>
<div id="introVideo">
<?php
	$sql = "SELECT thumb, videoTitle, videoType, videoFile, videoID, clickCnt, likeCnt, dislikeCnt FROM adepts_msVideos WHERE mappingType in ('topic','teacherTopic') AND mappingID='$topicCode' AND videoType=2";
	$result = mysql_query($sql);
	if(mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_assoc($result);
		$counter++;
		$title = $row['videoTitle'];
		$thumb = $row['thumb'];
		$videoID = $row['videoID'];
		$views = $row['clickCnt'];
		$likeCnt = $row['likeCnt'];
		$dislikeCnt = $row['dislikeCnt'];
		$videoFile = $row['videoFile'];
		$videoType = $topicVideoType[$row['videoType']];
		$videoPath = $pathForVideos.$topicCode."/".$videoType."/".$videoFile;
		$liveVideoPath = LIVE_VIDEO_PATH.$topicCode."/".$videoType."/".$videoFile;
		
?>
	<div class="video">
    	<div class="videoTitle"><?=$title?></div><br/>
        <div class="videoProp">
        <video id="videoFile<?=$counter?>" height="160" width="200" poster="<?=VIDEO_THUMB_PATH.$thumb?>" onClick="showModalVideo($(this),'<?=addslashes($title)?>','<?=$videoID?>','<?=$likeCnt?>','<?=$dislikeCnt?>',<?=$counter?>,$(this).attr('id'))" style="cursor:pointer;">
            <source id="mp4<?=$counter?>" class="<?=$videoPath?>.mp4" type="video/mp4" />
            <source id="ogv<?=$counter?>" class="<?=$videoPath?>.ogv" type="video/ogg" />
			<source id="mp4Live<?=$counter?>" class="<?=$liveVideoPath?>.mp4" type="video/mp4" />
            <source id="ogvLive<?=$counter?>" class="<?=$liveVideoPath?>.ogv" type="video/ogg" />
            Unable to play video :(
        </video>
        </div>
		<div class="videoName"><b>Video Name : </b><?=$title?></div>
		<div class="videoViews"><b>No. of Views : </b><?=$views?></div>
		<br/><br/><br/><br/>
	</div>
<?php
	}
?>
</div><br/>
<div id="misconceptionVideo">
<div class="video">
    	<div class="videoTitle">Misconception Videos</div><br/>
<?php
	$sql = "SELECT thumb, videoTitle, videoType, videoFile, videoID, clickCnt, likeCnt, dislikeCnt FROM adepts_msVideos WHERE mappingType in ('topic','teacherTopic') AND mappingID='$topicCode' AND videoType=1";
	$result = mysql_query($sql);
	if(mysql_num_rows($result) > 0)
	{
	while($row = mysql_fetch_assoc($result))
	{
		$counter++;
		$title = $row['videoTitle'];
		$thumb = $row['thumb'];
		$videoFile = $row['videoFile'];
		$views = $row['clickCnt'];
		$likeCnt = $row['likeCnt'];
		$dislikeCnt = $row['dislikeCnt'];
		$videoID = $row['videoID'];
		$videoType = $topicVideoType[$row['videoType']];
		$localPath = $pathForVideos.$topicCode."/".$videoType."/".$videoFile;
		$livePath = LIVE_VIDEO_PATH.$topicCode."/".$videoType."/".$videoFile;
		$videoPath = $localPath;
?>
	<div class="videoProp">
        <video id="videoFile<?=$counter?>" height="160" width="200" poster="<?=VIDEO_THUMB_PATH.$thumb?>" onClick="showModalVideo($(this),'<?=addslashes($title)?>','<?=$videoID?>','<?=$likeCnt?>','<?=$dislikeCnt?>',<?=$counter?>,$(this).attr('id'))" style="cursor:pointer;">
            <source id="mp4<?=$counter?>" class="<?=$videoPath?>.mp4" type="video/mp4" />
            <source id="ogv<?=$counter?>" class="<?=$videoPath?>.ogv" type="video/ogg"/>
			<source id="mp4Live<?=$counter?>" class="<?=$livePath?>.mp4" type="video/mp4" />
            <source id="ogvLive<?=$counter?>" class="<?=$livePath?>.ogv" type="video/ogg"/>
            Unable to play video :(
        </video>
        </div>
		<div class="videoName"><b>Description of Misconception : </b><?=$title?></div>
		<div class="videoViews"><b>No. of Views : </b><?=$views?></div>
		<br/><br/><br/><br/><br/><br/>

<?php }
		}else{ ?>
			<div class="noVideo" style="font-weight:bold;">Oops! We don't have any Misconception Videos for this topic. <br/> Please write to us if you are looking for any specific misconception and we will get back to you. <br/>Thank you.</div>
		<?php } ?>
	</div>
</div><br/><br/>
<div id="teacherVideo">
<div class="video">
 <?php
			$sql1 = "SELECT thumb, videoTitle, videoType, videoFile, videoID, clickCnt, likeCnt, dislikeCnt FROM adepts_msVideos WHERE mappingType in ('topic','teacherTopic') AND mappingID='$topicCode' AND videoType=3";
			$result1 = mysql_query($sql1);
			if(mysql_num_rows($result1) > 0)
			{ ?>
    	<div class="videoTitle">Teacher Videos</div><br/>
		 <?php
			while($row1 = mysql_fetch_assoc($result1))
			{
					$counter++;
					$title = $row1['videoTitle'];
					$thumb = $row1['thumb'];
					$views = $row1['clickCnt'];
					$likeCnt = $row1['likeCnt'];
					$dislikeCnt = $row1['dislikeCnt'];
					$videoID = $row1['videoID'];
					$videoFile = $row1['videoFile'];
					$videoType = $topicVideoType[$row1['videoType']];
                    $localPath = $pathForVideos.$topicCode."/".$videoType."/".$videoFile;
					$livePath = LIVE_VIDEO_PATH.$topicCode."/".$videoType."/".$videoFile;
					$videoPath = $localPath;
		?>
		<div class="videoProp">
        <video id="videoFile<?=$counter?>" height="160" width="200" poster="<?=VIDEO_THUMB_PATH.$thumb?>" onClick="showModalVideo($(this),'<?=addslashes($title)?>','<?=$videoID?>','<?=$likeCnt?>','<?=$dislikeCnt?>',<?=$counter?>),$(this).attr('id')" style="cursor:pointer;">
            <source id="mp4<?=$counter?>" class="<?=$videoPath?>.mp4" type="video/mp4" />
            <source id="ogv<?=$counter?>" class="<?=$videoPath?>.ogv" type="video/ogg"/>
            <source id="mp4Live<?=$counter?>" class="<?=$livePath?>.mp4" type="video/mp4" />
            <source id="ogvLive<?=$counter?>" class="<?=$livePath?>.ogv" type="video/ogg"/>
			Unable to play video :(
        </video>
        </div>
		<div class="videoName"><b>Description of Misconception : </b><?=$title?></div>
		<div class="videoViews"><b>No. of Views : </b><?=$views?></div>
		<br/><br/><br/><br/><br/><br/>
<?php		
	}
		} ?>
	</div>
</div><br/><br/>
</div>
<div style="display:none">
    <div id="videoBox">
        <div id="videoTitleDiv">
        	<h2 id="videoTitle" style="max-width:480px;"><?=$videoID?></h2>
            <a href="javascript:void(0)" onClick="showFullScreen()" class="fullScreenButton" title="View fullscreen"></a>
		</div>
        <div id="videoDiv">
        </div>
		<a href="Comments.php?mode=addacomment" target="_blank" id="showComment"><div id="add">
				<div id="circle">
				<div id="plushorizontal"> </div>
				<div id="plusVertical"> </div>
				 </div>
				 
				 
			</div></a>
		<div id="addComment"> Add a Comment </div>		
		<div id="dislike"><div class="liketext" id="dislikeCnt"><?=$dislikeCnt?></div></div>
		
		<div id="like"><div class="liketext" id="likeCnt"><?=$likeCnt?></div></div>
    </div>
</div>
<a href="#videoBox" class="videoLink">Dummy Link</a>
			</div>
			
		</div>
<?php 
if(isset($_REQUEST["mytopicpagerefferal"])){
	echo '<script>';
	echo '$(window).load(function(){$("#videoFile1").trigger("click");});' ;
	echo '</script>';
}
?>
<?php include("footer.php");
function getOSforIP() { 

    global $user_agent;

    $os_platform    =   "Unknown OS Platform";

    $os_array       =   array(
                            '/linux/i'              =>  'useIP',
                            '/ubuntu/i'             =>  'useIP',
                            '/iphone/i'             =>  'iPhone',
                            '/ipod/i'               =>  'useIP',
                            '/ipad/i'               =>  'useIP',
                            '/android/i'            =>  'useIP',
                            '/blackberry/i'         =>  'useIP',
                            '/webos/i'              =>  'useIP'
                        );

    foreach ($os_array as $regex => $value) { 

        if (preg_match($regex, $user_agent)) {
            $os_platform    =   $value;
        }

    }   

    return $os_platform;

}

?>