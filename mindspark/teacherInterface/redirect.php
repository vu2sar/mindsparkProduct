<?php 

//include("header.php"); 

?>
<title>Mindspark</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/common.css" rel="stylesheet" type="text/css">
<style>
body {
	min-width:1024px;
	overflow:auto;
}
#sideBar {
	width:5%;
}
#container {
	width:82%;
	padding-top:20px;
	border:0px !important;
}
#upperDiv {
	border:1px solid;
	width:100%;
	height:300px;
}
#lowerDiv {
	border:1px solid;
	width:67%;
	height:200px;
	float:left;
}

#newInterface {
	background-image:url(assets/halt/Picture8.png);
	background-size:100%;
	width: 53%;
	height: 100%;
	float: left;
}
#oldInterface {
	background-image:url(assets/halt/Picture9.png);
	background-size:100%;
	width: 58%;
	height: 100%;
	float: right;
}
#newInterfaceText {
	font-size:40px;
	width:100%;
}
#oldInterfaceText {
	font-size:30px;
	width:100%;
}
#newInterfaceIcon {
	background-image:url(assets/halt/Picture4.png);
	background-size:100%;
	width:100px;
	height:105px;
	margin-left:30%;
}
#oldInterfaceIcon {
	background-image:url(assets/halt/Picture5.png);
	background-size:100%;
	width:100px;
	height:100px;
	margin-left:30%;
}
#newInterfaceRight {
	text-align:center;
	float:left;
	margin-left:7%;
	padding:20px;
}
#oldInterfaceLeft {
	text-align:center;
	float:left;
	width:41%;
}
#midDiv {
	font-size:24px;
	padding:10px;
}
#instruction {
	float: right;
	height: 100px;
	width: 27%;
	border: 1px solid;
	border-radius: 40px;
	margin-top: 90px;
}
#instructionIcon {
	background-image:url(assets/halt/Picture7.png);
	background-size:100%;
	width:60px;
	height:53px;
	margin-left:5%;
	margin-top:20px;
	float:left;
}
#instructionText {
	font-size:17px;
	margin-left:5px;
	margin-top:10px;
	padding-top:5px;
	float:left;
	width:66%;
}
.noDecor {
	text-decoration:none;
}
.clear {
	clear:both;
}
#helpDiv {
	font-size:18px;
	padding-top:20px;
}
</style>
<script>
function exploreNewInterface()
{
	document.frmHidForm.submit();
}
</script>
</head>

<body>
<?php include("eiColors.php") ?>
	<div id="fixedSideBar">

	</div>
	<div id="sideBar">

	</div>

	<div id="container">
	
		<div id="upperDiv">
			<div id="newInterface"></div>
			<div id="newInterfaceRight">
				<div id="newInterfaceText"><a href="javascript:void(0)" onClick="exploreNewInterface()" class="noDecor">Explore the new<br>Teacher Interface</a></div>
				<a href="javascript:void(0)" onClick="exploreNewInterface()" class="noDecor"><div id="newInterfaceIcon"></div></a>
				<div id="helpDiv">Refer <b>Quick Tutorial</b> in Help section.</div>
			</div>
		</div>
		
		<div id="midDiv"><a href="javascript:void(0)" onClick="exploreNewInterface()" class="noDecor">We strongly recommend you to explore the new interface  because we believe that the information is better arranged to help Teachers better understand your class and individual student progress.</a></div>
		
		<div id="lowerDiv">
			<div id="oldInterfaceLeft">
				<div id="oldInterfaceText"><a href="<?php if(isset($_GET["admin"])) echo '../getSchoolDetails.php'; else echo '../ti_home.php';?>" class="noDecor">Continue with the <br>old Interface</a></div>
				<a href="<?php if(isset($_GET["admin"])) echo '../getSchoolDetails.php'; else echo '../ti_home.php';?>" class="noDecor"><div id="oldInterfaceIcon"></div></a>
			</div>
			<div id="oldInterface"></div>
		</div>
		
		<div id="instruction">
			<div id="instructionIcon"></div>
			<div id="instructionText">Switchback option available in <b>OTHER FEATURES</b> for few days only.</div>
		</div>
		
	</div>
	<form id="frmHidForm" name="frmHidForm" action="../controller.php<?php if(isset($_GET["admin"])) echo '?admin=1';?>" method="post">
		<input type="hidden" name="mode" value="changeInterface">
		<input type="hidden" name="interfaceFlag" value="1">
	</form>
</body>
</html>