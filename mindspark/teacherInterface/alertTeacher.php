<?php

	include("../userInterface/constants.php");
	include("header.php");

	set_time_limit (0);
	error_reporting(E_ERROR);

	if(!isset($_SESSION['userID']))
	{
		header("Location:../logout.php");
		exit;
	}
	$userID     = $_SESSION['userID'];
	$schoolCode = isset($_SESSION['schoolCode'])?$_SESSION['schoolCode']:"";
	$user   = new User($userID);
	$todaysDate = date("d");
	

	if(strcasecmp($user->category,"Teacher")==0 || strcasecmp($user->category,"School Admin")==0)	{
		$query = "SELECT schoolname FROM educatio_educat.schools WHERE schoolno=".$schoolCode;
		$r = mysql_query($query);
		$l = mysql_fetch_array($r);
		$schoolName = $l[0];
	}

	if(!isset($_SESSION['userID']))
	{
		header("Location: logout.php");
		exit;
	}
	$keys = array_keys($_REQUEST);
	foreach($keys as $key)
	{
		${$key} = $_REQUEST[$key] ;
	}

	$userID   = $_SESSION['userID'];
	$buddy_id = $_SESSION['buddy'];
	$childClass	=	$_SESSION['childClass'];

	$query = "SELECT   class, group_concat(distinct section ORDER BY section)
					  FROM     adepts_teacherClassMapping
					  WHERE    userID = $userID AND subjectno=".SUBJECTNO."
					  GROUP BY class ORDER BY class, section";


		$classArray = $sectionArray =  array();
		$hasSections = false;
		$result = mysql_query($query) or die(mysql_error());
		while($line=mysql_fetch_array($result))
		{
			array_push($classArray, $line[0]);
			if($line[1]!='')
				$hasSections = true;
			$sections = explode(",",$line[1]);
			$sectionStr = "";
			for($i=0; $i<count($sections); $i++)
			{
				$classSectionArr[]	=	$line[0].$sections[$i];
				if($sections[$i]!="")
					$sectionStr .= $sections[$i].",";
			}
			$sectionStr = substr($sectionStr,0,-1);
			array_push($sectionArray, $sectionStr);
		}
?>

<title>Students Detail</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<!-- <link href="css/feedBackForm.css" rel="stylesheet" type="text/css"> -->
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script>
	var langType = '<?=$language;?>';
	function load(){
		var fixedSideBarHeight = window.innerHeight;
		var sideBarHeight = window.innerHeight-95;
		$("#fixedSideBar").css("height",fixedSideBarHeight+"px");
		$("#sideBar").css("height",sideBarHeight+"px");
	}
</script>
<script>

	$(document).ready(function() {
	    var element = document.getElementById('clssection');
		element.value = "select";
		var cbs = document.getElementsByClassName("cb");
				for (var i = 0; i < cbs.length; i++) {
				cbs[i].checked = false;
				}
				document.getElementById(1).checked = true;
	});

	function logoff()
	{
		setTryingToUnload();
		window.location="logout.php";
	}
	
	var click=0;
	function openMainBar(){
	
	if(click==0){
		if(window.innerWidth>1024){
			$("#main_bar").animate({'width':'245px'},600);
			$("#plus").animate({'margin-left':'227px'},600);
		}
		else{
			$("#main_bar").animate({'width':'200px'},600);
			$("#plus").animate({'margin-left':'182px'},600);
		}
		$("#vertical").css("display","none");
		click=1;
	}
	else if(click==1){
		$("#main_bar").animate({'width':'26px'},600);
		$("#plus").animate({'margin-left':'7px'},600);
		$("#vertical").css("display","block");
		click=0;
	}
}

	function getrecords(cls , day)
	{
		if(day == '')
		{
			 var cbs = document.getElementsByClassName("cb");
												for (var i = 0; i < cbs.length; i++) {
												cbs[i].checked = false;
												}
												document.getElementById(1).checked = true;
		}
		var e = document.getElementById("clssection");
		var clsdata = e.options[e.selectedIndex].value;
		if(clsdata == 'select')
		{
			document.getElementById(day).checked = false;
			document.getElementById(1).checked = true;
			alert("Please select class and section.");
		}
		else{
		if(cls != 'select')
		{
			if(day != '')
			{
				var cbs = document.getElementsByClassName("cb");
				for (var i = 0; i < cbs.length; i++) {
				cbs[i].checked = false;
				}
				document.getElementById(day).checked = true;
			}else
			{
				day = $('.cb:checked').val();
			}
			if(cls == "select")
			{
				document.getElementById(1).checked = true;
				document.getElementById(day).checked = false;
				alert('Please select class section.');
				
			}
			else
			{
				if(cls == '')
				{
					var e = document.getElementById("sectiondata");
					if (typeof(e) != 'undefined' && e != null)
					{
						var cls = e.options[e.selectedIndex].value;
					}
				}
				if(cls >= 1)
					cls = '';

				if(cls == 'select')
				{
						document.getElementById(day).checked = false;
						document.getElementById(1).checked = true;
						alert("Please select section.");
				}
				else
				{
				
			$("#resultdata").html('');
			var selectedclass = document.getElementById("clssection");
			selectedclass = selectedclass.options[selectedclass.selectedIndex].text;
			document.getElementById('waitingimage').style.display = 'block';
			$.ajax({
			  url: 'ajaxRequest.php',
			  type: 'post',
			  data: {'mode': 'fetchresult','section':cls, 'day':day , 'selectedclass':selectedclass},
			  success: function(response) {
				 document.getElementById('waitingimage').style.display = 'none';
				 $("#resultdata").html(response);

				var flag = $('#checkflag').val();
				 if(flag != 1) { 

				var count = (response.match(/<tr>/g) || []).length;
				if(count > 0) 
					 {
						count = count - 1;
						if($('#noticenos').text() > 0)
						 {
							var count = $('#noticenos').text() - count;
							var reducecount = count
							if(count == 0)
							 {
								$( "#noticenos" ).hide();
								reducecount = '';
								$("#imagetag").attr('src', 'assets/Notification-Icon-grey.png');
							 }
							$("#noticenos").html(reducecount);
							
						 

						// for set decrease notice counter

								$.ajax({
									  url: 'ajaxRequest.php',
									  type: 'post',
									  data: {'mode': 'setcounter','noticecount':count,'section':cls, 'selectedclass':selectedclass},
									  success: function(response) {
											// $("#resultdata").html(response);
											  }
											 });
						 }
					  // for set decrease notice counter
					 }
			   }
			   $('#checkflag').val('0');
			  }
			 });
				}
			}
		}
		}
	}

	function getsection(classname)
	{
		if(classname != 'select')
		{
		$.ajax({
									  url: 'ajaxRequest.php',
									  type: 'post',
									  data: {'mode': 'getsections','classname':classname},
									  success: function(response) {
										  var cbs = document.getElementsByClassName("cb");
												for (var i = 0; i < cbs.length; i++) {
												cbs[i].checked = false;
												}
												document.getElementById(1).checked = true;
										  if(response.indexOf("no records") > -1)
										  {
											  $("#sectiondiv").html('');
											  getrecords(classname,'')
										  }
										  else
										  {
											  $("#sectiondiv").html(response);
										  }
										  $("#resultdata").html('');
											  
										  
											  }
											 });
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

	<div id="container" >
	<center>
	<table><tr><td style='color: #626161; font-size: 1.2em;'>
	Class&nbsp;&nbsp;&nbsp;
	<?php
	echo "<select id='clssection' onchange=getsection(this.value)>";
	echo "<option value='select'>Select Class</option>";
		//while($lineresult=mysql_fetch_array($result2))
		foreach($classArray as $val)
		{
			echo "<option value='".$val."'>".$val."</option>";	
		}
		echo "</select>";
	?></td><td id="sectiondiv">
	&nbsp;&nbsp;<font style='color: #626161; font-size: 1.2em;'>Section</font>&nbsp;&nbsp;&nbsp;&nbsp;
	<select id='sectiondata'>
	<option value='select'>Select Section</option>
	</select>
	</td></tr></table>
	</center>
	<div id="waitingimage" style="display:none;"><center><img src="assets/loadingImg.gif"></center></div>
	<br><br>
		<div style="width: 100%;">
			<div id="resultdata" style="float:left; width: 70%"></div>
			<div style="float:right;margin-right: 65px;">
				<h3>Select number of days</h3>
				
				<!--<input type="checkbox" class="cb" id="1" onclick="getrecords('',1)" value="1" checked> <b>7</b><br>
				<input type="checkbox" class="cb" id="2" onclick="getrecords('',2)" value="2" ><b> 14 </b><br>
				<input type="checkbox" class="cb" id="3" onclick="getrecords('',3)" value="3"><b> 21 </b><br>
				<input type="checkbox" class="cb" id="4" onclick="getrecords('',4)" value="4"><b> 28 </b><br>-->

				<input type="radio" class="cb" id="1" name="selectradio"  onclick="getrecords('',1)" value="1" checked><b>7</b><br>
				<input type="radio" class="cb" id="2" name="selectradio" onclick="getrecords('',2)" value="2" checked><b>14</b><br>
				<input type="radio" class="cb" id="3" name="selectradio" onclick="getrecords('',3)" value="3" checked><b>21</b><br>
				<input type="radio" class="cb" id="4" name="selectradio" onclick="getrecords('',4)" value="4" checked><b>28</b><br>
			</div>
		</div>
	<input type="hidden" name="checkflag" id="checkflag" value="">
	</div>
<?php include("footer.php") ?>