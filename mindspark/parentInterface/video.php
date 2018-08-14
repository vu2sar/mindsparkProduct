<?php include("header.php") ?>

<title>Videos</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/help.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script>
	var langType = '<?=$language;?>';
	function load(){
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#sideBar").css("height",sideBarHeight+"px");
		/*$("#container").css("height",containerHeight+"px");*/
	}
</script>
</head>
<body class="translation" onload="load()" onresize="load()">
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
		<?php include('referAFriendIcon.php') ?>
		<table id="childDetails">
				<td width="33%" id="sectionRemediation" class="pointer"><div class="smallCircle red"></div><label class="textRed pointer" value="secRemediation">VIDEOS</label></a></td>
		</table>
		<object style="height: 350px; width: 400px;margin-left: 28%;"><param name="movie" value="http://www.youtube.com/v/ej7lytwhiP0?version=3&feature=player_detailpage"><param name="allowFullScreen" value="true"><param name="allowScriptAccess" value="always"><embed src="http://www.youtube.com/v/ej7lytwhiP0?version=3&feature=player_detailpage" type="application/x-shockwave-flash" allowfullscreen="true" allowScriptAccess="always" width="400" height="350"></object>
	
	</div>

<?php include("footer.php") ?>