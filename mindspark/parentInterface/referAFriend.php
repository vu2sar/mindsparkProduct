<?php include("header.php") ?>

<title>Refer Friends</title>

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
		<table id="childDetails">
				<td width="33%" id="sectionRemediation" class="pointer"><div class="smallCircle red"></div><label class="textRed pointer" value="secRemediation">REFER A FRIEND</label></a></td>
		</table>
		<?php
//                if(strtolower($_SESSION['childSubcategory'])!='school')  {
                    include("referral.php");
//                }
//                else
//                    echo "<div style='text-align: center; font-size: medium; padding-top: 10%;'>Referral scheme is not available for Mindspark school users.</div>";
                ?>
	</div>

<?php include("footer.php") ?>