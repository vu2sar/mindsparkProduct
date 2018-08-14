<?php include("header.php") ?>

<title>Individual Report</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/individualReport.css" rel="stylesheet" type="text/css">
<script src="libs/jquery-1.9.1.js"></script>
<link rel="stylesheet" href="libs/css/jquery-ui.css" />
<script src="libs/jquery-ui.js"></script>
<link rel="stylesheet" href="/resources/demos/style.css" />
  <script>
  $(function() {
    $( ".datepicker" ).datepicker();
  });
  </script>
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
		$("#trailContainer").css("height",containerHeight+"px");
		$("#students").css("font-size","1.4em");
		$("#students").css("margin-left","40px");
		$(".arrow-right-yellow").css("margin-left","10px");
		$(".rectangle-right-yellow").css("display","block");
		$(".arrow-right-yellow").css("margin-top","3px");
		$(".rectangle-right-yellow").css("margin-top","3px");
	}
	function showTrail(){
		$(".gridtable").css("visibility","visible");
		$("#pagingTable").css("visibility","visible");
		$(".textPurpleBelow").css("visibility","visible");
	}
	function openCalender(id){
		var id=id;
		if(id=="from"){
			$("#dateFrom").focus();
		}
		else{
			$("#dateTo").focus();
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
		<div id="trailContainer">
			<div id="headerBar">
				<div id="pageName">
					<div class="arrow-black"></div>
					<div id="pageText">INDIVIDUAL REPORT</div>
				</div>
			</div>
			
			<table id="topicDetails">
				<td width="5%"><label>Class</label></td>
		        <td width="25%" style="border-right:1px solid #626161">
		            <select name="topic" id="lstTopic" style="width:65%;">
	                <option value="">Select</option>
					<option value="">1</option>
					<option value="">2</option>
					<option value="">3</option>
					<option value="">4</option>
					<option value="">5</option>
					<option value="">6</option>
					<option value="">7</option>
					<option value="">8</option>
					<option value="">9</option>
	                <!--<?php
	                    for($i=0; $i<count($topicsAttempted); $i++)
	                    {
	                        echo "<option value='".$topicsAttempted[$i][0]."'";
	                        if($topicsAttempted[$i][0]==$topic)
	                            echo " selected";
	                        echo ">".$topicsAttempted[$i][1]."</option>";
	                    }
	                ?>-->
	           		</select>
		        </td>
				<td width="6%"><label style="margin-left:20px;">Section</label></td>
		        <td width="24%" style="border-right:1px solid #626161">
		            <select name="topic" id="lstTopic" style="width:65%;">
	                <option value="">Select</option>
					<option value="">A</option>
					<option value="">B</option>
					<option value="">C</option>
					<!--<?php
	                    for($i=0; $i<count($sessions); $i++)
	                    {
	                        echo "<option value='".$sessions[$i]."'";
	                        if((in_array($sessions[$i],$sessionID)) || ($bypass_flag == 1 && $sessionIDStr==$sessions[$i]))

	                            echo " selected";
	                        echo ">".$sessions[$i]." (".$startTime[$i].")</option>";
	                    }
	                ?>-->
	           		</select>
		        </td>
				<td width="24%">Show Topics Attempted<input type="checkbox"  name="checkTopicAttempted" id="checkTopicAttempted"></td>
			</table>
			
			<table id="generateTable">
				<td width="5%">
					<label>FROM</label>
		        </td>
				<td width="25%" style="border-right:1px solid #626161"><input type="text" name="dateFrom" class="datepicker floatLeft" id="dateFrom" value="" autocomplete="off" size="20"/><div class="calenderImage linkPointer" id="from" onClick="openCalender(id)"></div></td>
				<td width="6%">
					<label style="margin-left:20px;">TO</label>
		        </td>
				<td width="25%" style="border-right:1px solid #626161"><input type="text" name="dateTo" class="datepicker floatLeft" id="dateTo" value="" autocomplete="off" size="20"/><div class="calenderImage linkPointer" id="to" onClick="openCalender(id)"></div></td>
		        <td width="24%"><input type="submit" class="button" name="btnGenerate" id="generate" value="Generate" onClick="return showTrail();"></td>
			</table>

			<table id="pagingTable">
		        <td width="35%">CLASS 3B</td>
				<td>
					<div class="textRed">NOTE : All underlined fields below can be sorted. Click on field name to sort.</div>
				</td>
			</table>
			
			<table class="gridtable" border="1" width="100%">
				<tr>
					<th width="16%" rowspan="2">STUDENT’S NAME</th>
					<th colspan="5" width="40%">Regular Question</th>
					<th colspan="5" width="44%">Other Tasks</th>
				</tr>
				<tr>
					<th width="6%">LOGIN DAYS (SESSIONS)</th>
					<th width="6%">LOG IN TIME HH:MM:SS</th>
					<th width="6%">TOTAL Q ATTEMPTED</th>
					<th width="6%">% CORRECT</th>
					<th width="6%">AVG. TIME TAKEN</th>
					<th width="6%">C.Q</th>
					<th width="6%">P.Q</th>
					<th width="6%">TIMED TESTS</th>
					<th width="6%">ACTIVITIES</th>
					<th width="6%">NCERT EXC.</th>
				</tr>
				<tr>
					<td width="6%">Nikhil Jain</td>
					<td width="6%">0(0)</td>
					<td width="6%">00:00:00</td>
					<td width="6%">112</td>
					<td width="6%">62.66</td>
					<td width="6%">9.1</td>
					<td width="6%">0</td>
					<td width="6%">0</td>
					<td width="6%">0</td>
					<td width="6%">0</td>
					<td width="6%">0</td>
				</tr>
			</table>
			
		</div>
	</div>

<?php include("footer.php") ?>