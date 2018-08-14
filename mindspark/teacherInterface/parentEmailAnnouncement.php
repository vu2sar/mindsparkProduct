<?php
include("header.php");
?>
<title>New Parent Connect Feature</title>
<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/otherFeatures.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="libs/css/jquery-ui.css" />
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
				<div style="font-size: 18px; color: #000; background-color: rgb(201, 218, 146); width: 98%; border: medium solid rgb(8, 80, 145); padding-bottom: 6px; padding-top: 6px;">&nbsp;New Parent Connect Feature</div>
			</div>
			<div id="containerBody">
				<p>Parents whose email IDs are registered with Mindspark will be able to view their children's progress. <b>This was an important request from many parents.</b></p>
				<p>Parents who have not registered will be reminded to register when the students work on Mindspark from home. Registration is optional.</p>
				<p>The new Mindspark Parent Connect allows parents to see what their children are doing in Mindspark. The Parent Connect is easily accessible to those parents whose email IDs have been registered with Mindspark.</p>
				<p>Registered parent email IDs also make it easier for teachers to communicate directly with parents, through the "Mail Parents" option on the Mindspark Teacher Interface.</p>
				<p>[Mindspark treats all personal information including email IDs as privileged information. It will be used solely for mailing Mindspark related communications.]</p>
				<p>Please write to us at <a href="mailto:shweta.bhaskar@ei-india.com">shweta.bhaskar@ei-india.com</a>, if you have any questions, or if you do not want the parent email IDs to be taken and linked to allow parents access to the Parent Connect.</p>
			</div>
		</div>
		<div id="innerContent"></div>
	</div>
<?php include("footer.php") ?>