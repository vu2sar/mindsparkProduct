<?php
include("header.php");
?>
<title>AQAD Feature</title>
<style>
#fontblock {
	font-size : 1.1em;
}
</style>
<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/otherFeatures.css" rel="stylesheet" type="text/css">
<script src="libs/jquery-1.9.1.js"></script>
<link rel="stylesheet" href="libs/css/jquery-ui.css" />
<script src="libs/jquery-1.9.1.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script>
	var langType = '<?=$language;?>';
	function load(){
		/*var fixedSideBarHeight = window.innerHeight;*/
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		/*$("#fixedSideBar").css("height",fixedSideBarHeight+"px");*/
		$("#sideBar").css("height",sideBarHeight+"px");
		/*$("#container").css("height",containerHeight+"px");*/
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
			<div id="containerHead">
				<div style="font-size: 18px; color: #000; background-color: rgb(201, 218, 146); width: 98%; border: medium solid rgb(8, 80, 145); padding-bottom: 6px; padding-top: 6px;">&nbsp;AQAD Feature</div>
			</div>
			<div id="containerBody">
			<div id = "fontblock">
				<p>AQADs are thought-provoking questions, selected from the past ASSET papers, which  aim at providing greater exposure to application-oriented questions. AQADs appear 6 days in a week starting from Monday (through Saturday), on the student home page in Mindspark <b><u>only during home usage</u></b> and for the Mindspark students of classes 3 to 9. On selecting the AQAD icon on the student home page, the student can read and submit the answer. The answer submitted is not evaluated by Mindspark.</p>
				<p>Following is the sequence of questions asked –</p>
				<p>Monday - Question on English</p>

				<p>Tuesday - Question on Maths</p>

				<p>Wednesday - Question on Science</p>

				<p>Thursday - Question on Maths</p>

				<p>Friday - Question on Science</p>

				<p>Saturday - Question on Social Studies</p>
				</div>
			</div>
		</div>
		<div id="innerContent"></div>
	</div>
<?php include("footer.php") ?>