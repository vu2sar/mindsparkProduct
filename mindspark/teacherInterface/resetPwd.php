<?php include("header.php") ?>

<title>Reset Password</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/resetPwd.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script>
	var langType = '<?=$language;?>';
	function load(){
		var fixedSideBarHeight = window.innerHeight;
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#fixedSideBar").css("height",fixedSideBarHeight+"px");
		$("#sideBar").css("height",sideBarHeight+"px");
		$("#container").css("height",containerHeight+"px");
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
		<div id="innerContainer">
			
			<div id="containerHead">
				<div id="triangle"> </div>
				<span>RESET PASSWORD</span>
			</div>
			<div id="containerBody">
		
		<div> <label for="teacherID">TEACHER'S USERNAME</label> </div>
		
			<div> <input type="textbox"  id="teacherID" /> </div>
			
	<div>	<input type='submit' name="submit" id= "button1" value='SUBMIT'  class="buttons"> </div>
			
			
			</div>
		</div>
		
		
	</div>

<?php include("footer.php") ?>