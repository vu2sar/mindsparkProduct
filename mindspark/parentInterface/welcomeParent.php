<?php include("header.php");
	$curDate = new DateTime("now");
 ?>

<title>Welcome</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/welcome.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="libs/closeDetection.js"></script>
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
				<td width="33%" id="sectionRemediation" class="pointer"><div class="smallCircle red"></div><label class="textRed pointer" value="secRemediation">WELCOME PARENT!</label></td>
		</table>
		<table class="studentDetails" align="left">
				<tr>
					<th align="left" style="font-size:1.3em">Tell us more!</th>
				<tr>
					<td>&nbsp;</td>
				</tr>
				</tr>
				<tr>
					<th align="left">Subscription</th>
					<td>
						: <select name="gender" style="width:180px;" id="gender" class="textBoxes">
                              <option value="">Select</option>
                              <option value="1">7 day free trial of mindspark</option>
                              <option value="2">3 month subscription</option>
							  <option value="2">6 month subscription</option> 
							  <option value="2">1 year subscription</option>							                                 
                          </select>
					</td>
				</tr>
				<tr>
					<th align="left">Child First Name</th>
					<td>
						: <input class="textBoxes usingPlaceHolder" type="text" name="Email" id="txtEmail" style="width:180px;" size="40" maxlength="100">
					</td>
				</tr>
				<tr>
					<th align="left">Child Last Name</th>
					<td>
						: <input class="textBoxes usingPlaceHolder" type="text" name="Email" id="txtEmail" style="width:180px;" size="40" maxlength="100">
					</td>
				</tr>
				<tr>
					<th align="left">School Name</th>
					<td>
						: <input class="textBoxes usingPlaceHolder" type="text" name="Email" id="txtEmail" style="width:180px;" size="40" maxlength="100">
					</td>
				</tr>
				<tr>
					<th align="left">Class</th>
					<td>
						: <select name="gender" style="width:180px;" id="gender" class="textBoxes">
                              <option value="">Select</option>
                              <option value="1">1</option>
                              <option value="2">2</option>
							  <option value="2">3</option> 
							  <option value="2">4</option>		
							  <option value="1">5</option>
                              <option value="2">6</option>
							  <option value="2">7</option> 
							  <option value="2">8</option>	
							  					                                 
                          </select>
					</td>
				</tr>
				<tr>
					<th align="left">Gender</th>
					<td>
						: <select name="gender" style="width:180px;" id="gender" class="textBoxes">
                              <option value="">Select</option>
                              <option value="1">Boy</option>
                              <option value="2">Girl</option>                                  
                          </select>
					</td>
				</tr>
				<tr>
					<th align="left" valign="top" style="padding-top:8px;">Username</th>
					<td>
						: <input class="textBoxes usingPlaceHolder" type="text" name="Email" id="txtEmail" style="width:180px;" size="40" maxlength="100"><br/>
						<div class="italicsText"><i>Child's password will be same as username which can be changed after first login.</i></div>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
						<td class="createButton"><a href="welcomeMindspark.php" target="_blank">Create</a></td>
				</tr>
			
			</table>
		
	</div>

<?php include("footer.php") ?>