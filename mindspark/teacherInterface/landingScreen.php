<?php include("header.php") ?>

<title>Landing Screen</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/landingScreen.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script>
	var langType = '<?=$language;?>';
	var click=0;
	
	function load(){
		
		var fixedSideBarHeight = window.innerHeight;
		var sideBarHeight = window.innerHeight-95;
	
		$("#fixedSideBar").css("height",fixedSideBarHeight+"px");
		$("#sideBar").css("height",sideBarHeight+"px");
		$("#container").css("height",containerHeight+"px");
	}
	
</script>
</head>
<body class="translation" onload="load()" onresize="loaded()" style="overflow: auto">
	<?php include("eiColors.php") ?>
	<div id="fixedSideBar">
		<?php include("fixedSideBar.php") ?>
	</div>
	<div id="topBar">
		<?php include("topBar.php") ?>
	</div>
	<div id="sideBar">
			<?php include("landingSideBar.php") ?>
	</div>

	<div id="container" >
	
	<?php for($i=0;$i<2;$i++){
		?>
		<div id="flipContent" style="">
			<div id="topicBox">
			<div id="topicBoxDesc">
				<div id="topicBoxDescTopic">
				Addition upto 999
				</div>
				<div id="topicBoxDescInfo1">
				Activated on 24/03/2013
				</div>
				<div id="topicBoxDescInfo2">
				Active since 28 Days
				</div>
			</div>
			
			
			<div id="outerCircle" class="outerCircle">
			<div id="percentCircle" class="progressCircle forHighestOnly circleColor5">40%</div>
			</div>
			
			</div>
			
			<div id="topicBox">
			<div id="topicBoxDesc">
				<div id="topicBoxDescTopic">
				Addition upto 999
				</div>
				<div id="topicBoxDescInfo1">
				Activated on 24/03/2013
				</div>
				<div id="topicBoxDescInfo2">
				Active since 28 Days
				</div>
			</div>
			
			
			<div id="outerCircle" class="outerCircle">
			<div id="percentCircle" class="progressCircle forHighestOnly circleColor0">7%</div>
			</div>
				
			</div>
			
			
			<div id="topicBox">
			<div id="topicBoxDesc">
				<div id="topicBoxDescTopic">
				Addition upto 999
				</div>
				<div id="topicBoxDescInfo1">
				Activated on 24/03/2013
				</div>
				<div id="topicBoxDescInfo2">
				Active since 28 Days
				</div>
			</div>
			
			
			<div id="outerCircle" class="outerCircle">
			<div id="percentCircle" class="progressCircle forHighestOnly circleColor8">90%</div>
			</div>
			
			
			</div>
				
			</div>
		
		<!--Live symbol-->
		<div id="tabcls1" class="classTab">
				<div id="circle"> 
					<div id="triangle" style="" > </div>
				</div>
			</div>
			<div class="classTabTriangle" id="trianglecls1" style=""> </div>
		<!--Live symbol end-->	
		
		<div id="flip">
			
			<div id="classTriangle" style="" > </div>
			<div class="text" id="cls1" onclick="classSlide(id)"> CLASS  3 </div>
			<div id="line"> </div>
		</div>
	
		<?php } ?>
	</div>

<?php include("footer.php") ?>