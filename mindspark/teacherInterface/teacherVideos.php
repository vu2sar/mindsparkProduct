<?php include("header.php");
	include("../userInterface/constants.php");
	header('X-Frame-Options: GOFORIT'); 
	$baseurl = IMAGES_FOLDER."/newUserInterface/";
	
	//$videoCount=1;
	
	 ?>

<title>Other Features</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/teacherVideos.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="libs/jquery.js"></script>

<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script>
	var langType = '<?=$language;?>';
	var videoCount=1;
	var counter=0;
	
	function load(){
		/*var fixedSideBarHeight = window.innerHeight;*/
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		/*$("#fixedSideBar").css("height",fixedSideBarHeight+"px");*/
		$("#sideBar").css("height",sideBarHeight+"px");
		/*$("#container").css("height",containerHeight+"px");*/
		$("#features").css("font-size","1.em");
		$("#features").css("margin-left","40px");
		$(".arrow-right-blue").css("margin-left","10px");
		$(".rectangle-right-blue").css("display","block");
		$(".arrow-right-blue").css("margin-top","3px");
		$(".rectangle-right-blue").css("margin-top","3px");
//		document.getElementById("check1").addEventListener('error', function (e) {
//				
//	        });

		
			
	}
	
		function openHelp1(){
			var k = window.innerWidth;
			var helpSource= "<?=$baseurl?>theme4/help.html";
			if(k>1024)
			{
				$("#iframeHelp").attr("height","440px");
				$("#iframeHelp").attr("width","960px");
				$("#iframeHelp").attr("src",helpSource);
				$.fn.colorbox({'href':'#openHelp','inline':true,'open':true,'escKey':true, 'height':570, 'width':1024});
			}
			else
			{
				$("#iframeHelp").attr("height","390px");
				$("#iframeHelp").attr("width","700px");
				$("#iframeHelp").attr("src",helpSource);
				$.fn.colorbox({'href':'#openHelp','inline':true,'open':true,'escKey':true, 'height':500, 'width':800});
			}
		}
		
		
		
		function setCounter(videoNo,videoSource){
		var ua = navigator.userAgent.toLowerCase();
		var isAndroid = ua.indexOf("android") > -1;
		var isIpad = ua.indexOf("ipad") > -1;
		if(isAndroid || isIpad){
			if(videoNo==1){
				var videoSource ="http://www.youtube.com/watch?v=J5XreZxJqMk";
			}else if(videoNo==2){
				var videoSource ="http://www.youtube.com/watch?v=jBfy9Yc7C_Y";
			}else if(videoNo==3){
				var videoSource ="http://www.youtube.com/watch?v=TJGkKUoqI5I";
			}else if(videoNo==4){
				var videoSource ="http://www.youtube.com/watch?v=rFLoCmCefTw";
			}
			var datetime = new Date().getTime();
			$.ajax('ajaxRequest.php?mode=helpVideoCounter&videoID='+videoNo+'&date='+datetime,
		    {
		        method: 'get',
		        success: function (transport) {
		            console.log(transport);

		        }
		    }
		    );
			
			window.open(videoSource);
		}else{
				if(counter==0){
					var myVid=document.getElementById("video22");
					if(myVid.readyState===4){
						videoCount=1;
					}else{
						videoCount=0;
					}
					$("#videoCheck").html("");
					counter++;
				}
			
				if(videoCount==1){
					var datetime1 = new Date().getTime();
					if(videoNo==1){
						var videoSource ="http://mindsparkserver/videos/orientationVideos/Students.mp4?"+datetime1;
					}else if(videoNo==2){
						var videoSource ="http://mindsparkserver/videos/orientationVideos/Highlights.mp4";
					}else if(videoNo==3){
						var videoSource ="http://mindsparkserver/videos/orientationVideos/Reports.mp4";
					}else if(videoNo==4){
						var videoSource ="http://mindsparkserver/videos/orientationVideos/Conclusion.mp4";
					}
					$("#videoTag").html('<video controls width="750px" height="380px" style="margin-top:30px;"><source src="'+videoSource+'" class="videoSource" type="video/mp4" /></video>');
				}else{
					$("#videoTag").html('<iframe class="videoSource" src="" width="750px" height="380px" style="margin-top:30px;"></iframe>');
					$(".videoSource").attr("src",videoSource);
				}
				
				var datetime = new Date().getTime();
				$.ajax('ajaxRequest.php?mode=helpVideoCounter&videoID='+videoNo+'&date='+datetime,
			    {
			        method: 'get',
			        success: function (transport) {

			        }
			    }
			    );
				$.fn.colorbox({'href':'#videoBox','inline':true,'open':true,'escKey':true, 'height':500, 'width':800});
			
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
	
		<table id="childDetails" align="top">
				<td width="18%" class="pointer"><a href="faq.php"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition" style="width:125px;">FAQ's</div></div></div></a></td>
		        <td width="18%" class="pointer"><div id="setHeight"><div class="smallCircle" onclick="openHelp1()"><div class="pointer setPosition" onclick="openHelp1()">Interface Preview</div></div></td>
		        <td width="18%" class="pointer"><div id="setHeight"><div class="red smallCircle"><div class="textRed pointer setPosition">Teacher Videos</div></div></div></td>
				<td width="22%" class="pointer"><a href="teacherManual.php"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition">Teacher Manual</div></div></div></a></td>
			</table>
		<div id="innerContainer">
			<div class="videoContainer">
				<div onclick="setCounter(1,'http://www.youtube.com/v/J5XreZxJqMk?version=3&feature=player_detailpage')" target="_blank">
				<div id="video1" class="video">
				</div>
				<div class="videoText">Mindspark for students & teachers
				</div>
				</div>
			</div>
			<div class="videoContainer">
				<div onclick="setCounter(2,'http://www.youtube.com/v/jBfy9Yc7C_Y?version=3&feature=player_detailpage')" target="_blank">
				<div id="video2" class="video">
				</div>
				<div class="videoText">Highlights of the teacher interface
				</div>
				</div>
			</div>
			<div class="videoContainer">
				<div onclick="setCounter(3,'http://www.youtube.com/v/TJGkKUoqI5I?version=3&feature=player_detailpage')" target="_blank">
				<div id="video3" class="video">
				</div>
				<div class="videoText">Mindspark Reports
				</div>
				</div>
			</div>
			<div class="videoContainer">
				<div onclick="setCounter(4,'http://www.youtube.com/v/rFLoCmCefTw?version=3&feature=player_detailpage')" target="_blank">
				<div id="video1" class="video">
				</div>
				<div class="videoText">Conclusion
				</div>
				</div>
			</div>
		</div>
		
		<div id="innerContent">
		
		</div>
		<div style="display:none">
		<div id="videoBox">
			<span id="videoTag">
			
			</span>
</div>

		</div>
		<div id="videoCheck" style="display:none;">
			<video controls="controls" width="750px" height="380px" style="margin-top:30px;" id="video22"><source src="http://mindsparkserver/videos/orientationVideos/Students.mp4" class="videoSource" type="video/mp4" /></video>
		</div>
		
	</div>

<?php include("footer.php") ?>