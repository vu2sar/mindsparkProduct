<?php include("header.php");
	include("../userInterface/constants.php");
	$baseurl = IMAGES_FOLDER."/newUserInterface/"; ?>

<title>Other Features</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/help.css" rel="stylesheet" type="text/css">
<!-- <script type="text/javascript" src="libs/jquery.js"></script> -->
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<link rel="stylesheet" type="text/css" href="css/colorbox.css">
<script>
	var langType = '<?=$language;?>';
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
	}
	function openHelp1(){
		var k = window.innerWidth;
		var helpSource= "<?=$baseurl?>theme5/help.html";
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
		setTimeout(function(){tryingToUnloadPage=false},500);
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
		<div id="innerContainer">
			<div class="menuContainer">
				<a href="faq.php"><div class="iconsNew" id="faq"></div><div class="faqText">FAQ</div></a>

				<a href="faq.php"><div class="messages"><b>Frequently Asked Questions</b><br/><br/>This section contains answers to frequently asked questions. If you are having 
doubts about Mindspark and student related issues, you will find this section 
very useful.  
</div></a>
			</div>
			<div class="menuContainer">
				<a href="javascript:void(0)" onclick="openHelp1()"><div class="iconsNew" id="tutorial"></div><div class="previewText">Full Preview</div></a>
				<a href="javascript:void(0)" onclick="openHelp1()"><div class="messages"><b>Teacher Interface tutorial</b></div></a>
			</div>
			<!--<div class="menuContainer">
				<a href="teacherVideos.php"><div class="iconsNew" id="teacherVideos"></div></a>
				<div class="messages"><b>Learning through Mindspark- Videos</b></div>
			</div>-->
			<div class="menuContainer">
				<a href="teacherManual.php"><div class="iconsNew" id="teacherManual"></div><div class="manualText">Mindspark Manual</div></a>

				<a href="teacherManual.php"><div class="messages"><b>Mindspark user manual</b><br/><br/>This manual covers different aspects of Mindspark</div></a>
			</div>
			<div class="menuContainer">
				<a href="trainingModule.php"><div class="iconsNew" id="trainingModule"></div><div class="moduleText">Training Module</div></a>
				<a href="trainingModule.php"><div class="messages"><b>Mindspark Teacher Training Module</b>
				<br/><br/>
				This module will help you understand Mindspark to a greater depth and will help in integrating Mindspark well with your curriculum.
				</div></a>
			</div>
		</div>
		
		<div id="innerContent">
		
		</div>
		
	</div>

<?php include("footer.php") ?>