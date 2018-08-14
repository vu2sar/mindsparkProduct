<?php
    set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
    include("header.php");
	
	
    include("../userInterface/functions/orig2htm.php");
    error_reporting(E_ERROR);

    
    $userID     = $_SESSION['userID'];
    $category   = $_SESSION['admin'];
    $schoolCode = $_SESSION['schoolCode'];
    $username   = $_SESSION['username'];
    $subcategory = $_SESSION['subcategory'];

	if(strcasecmp($category,"School Admin")==0)
	{
		$query  = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
		           FROM     adepts_userDetails
		           WHERE    schoolCode=$schoolCode AND category='STUDENT' AND subcategory='School' AND enabled=1
		           AND endDate>=curdate() GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
	}
	elseif (strcasecmp($category,"Teacher")==0)
	{
		$query = "SELECT   class, group_concat(distinct section ORDER BY section)
				  FROM     adepts_teacherClassMapping
				  WHERE    userID = $userID
				  GROUP BY class ORDER BY class, section";
	}
	else
	{	
		
		echo "You are not authorised to access this page!";
    	exit;
	}

	$classArray = $sectionArray = array();
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
		    if($sections[$i]!="")
		          $sectionStr .= "'".$sections[$i]."',";
		}
		$sectionStr = substr($sectionStr,0,-1);
		array_push($sectionArray, $sectionStr);
	}

	//print_r($_POST);

	$cls     = isset($_REQUEST['cls'])?$_REQUEST['cls']:"";
	$section = isset($_REQUEST['section'])?$_REQUEST['section']:"";
	$todaysDate = date("d");

	//Deactivate the revision session those are active for more than a month (25 days)(by default, the revision session becomes inactive after a month).
	mysql_query("UPDATE adepts_revisionSessionMaster SET isActive=0, lastUpdatedBy='system' WHERE isActive=1 AND schoolCode=$schoolCode AND datediff(curdate(), activationDate)>25") or die(mysql_error());

	

	$revisionSessionArray = array();
	$srno = 0;
	$forClassArray = $forSectionArray = array();
	if($cls=="")
	{
		$forClassArray = $classArray;
		$forSectionArray = $sectionArray;
	}
	else
	{
		array_push($forClassArray,$cls);
		if($section!="")
			array_push($forSectionArray,"'".$section."'");
		else
		{
			$key = array_search($cls,$classArray);
			array_push($forSectionArray,$sectionArray[$key]);
		}
	}
	for($k=0; $k<count($forClassArray); $k++)
	{
		$query = "SELECT revisionSessionID, class, section, activationDate, isActive
				  FROM   adepts_revisionSessionMaster
				  WHERE  schoolCode=$schoolCode ";
		$query .= " AND class=".$forClassArray[$k];
		if($forSectionArray[$k]!="")
			$query .= " AND section in (".$forSectionArray[$k].")";
		$query .= " ORDER BY class, section, activationDate";
		$result = mysql_query($query) or die($query.mysql_error());

		while ($line = mysql_fetch_array($result))
		{
			$revisionSessionArray[$srno][0] = $line['revisionSessionID'];
			$revisionSessionArray[$srno][1] = $line['class'];
			$revisionSessionArray[$srno][2] = $line['section'];
			$revisionSessionArray[$srno][3] = $line['activationDate'];
			$revisionSessionArray[$srno][4] = $line['isActive'];

			$query = "SELECT count(userID) FROM adepts_revisionSessionStatus WHERE revisionSessionID=".$line['revisionSessionID']." AND noOfQuestions>0";
			$noofstudents_result = mysql_query($query);
			$noofstudents_line   = mysql_fetch_array($noofstudents_result);
			$revisionSessionArray[$srno][5] = $noofstudents_line[0];
			

			$srno++;
		}
	}
	//print_r($_POST);
	$errMsg = "";
	if(isset($_POST['activate']))
	{
		if(isset($_POST['color']))
		{
			$teachertopic = '';
			foreach($_POST['color'] as $val)
			{
				$teachertopic .= $val.",";
			}
		}    
		$teachertopic = rtrim($teachertopic,',');
		$date = substr($_POST['fromDate'],6,4)."-".substr($_POST['fromDate'],3,2)."-".substr($_POST['fromDate'],0,2);
		$today = date("Y-m-d");
		if($date<$today)
		{
			$errMsg .= "Activation date cannot be less than today's date!";
		}
		else
		{
			//activateRevisionSession();
			$activateForClassesArray = array();
			$activateForSectionsArray = array();
			if($cls=="")
			{
				$activateForClassesArray  = $classArray;
				$activateForSectionsArray = $sectionArray;
			}
			else
			{
				array_push($activateForClassesArray,$cls);
				if($section!="")
					array_push($activateForSectionsArray,$section);
				else
				{
					$key = array_search($cls,$classArray);
					array_push($activateForSectionsArray,$sectionArray[$key]);
				}
			}
			for($i=0; $i<count($activateForClassesArray); $i++)
			{
				$tmpClass = $activateForClassesArray[$i];
				if($activateForSectionsArray[$i]!="")
				{
					$tmpSectionArray = explode(",",$activateForSectionsArray[$i]);
				}
				else
					$tmpSectionArray = array('');


				for($j=0; $j<count($tmpSectionArray); $j++)
				{
					$tmpSection = str_replace("'","",$tmpSectionArray[$j]);
					$isActive = false;
					for($k=0; $k<count($revisionSessionArray); $k++)
					{
						if($revisionSessionArray[$k][1]==$tmpClass && $revisionSessionArray[$k][2]==$tmpSection && $revisionSessionArray[$k][4]==1)
							$isActive = true;
					}
					
					if(!$isActive && !empty($teachertopic))
					{

						$query  = "INSERT INTO adepts_revisionSessionMaster (schoolCode, class, section, activationDate, isActive, lastUpdatedBy, lastModified ,teacherTopicCode) VALUES
									($schoolCode,$tmpClass,'$tmpSection','$date',1,'".$username."',now(),'$teachertopic')";
						//echo "<br/>".$query; exit;
						mysql_query($query) or die(mysql_error());
						$revisionSessionID = mysql_insert_id();
						$revisionSessionCount = count($revisionSessionArray);
						$revisionSessionArray[$revisionSessionCount][0] = $revisionSessionID;
						$revisionSessionArray[$revisionSessionCount][1] = $tmpClass;
						$revisionSessionArray[$revisionSessionCount][2] = $tmpSection;
						$revisionSessionArray[$revisionSessionCount][3] = $date;
						$revisionSessionArray[$revisionSessionCount][4] = 1;
						$errMsg .="Revision Session Activated for ".$tmpClass.$tmpSection.".<br/>";
					}
					else
					{
						if(empty($teachertopic))
						{
							$errMsg .= "Please select topics for ".$tmpClass.$tmpSection." to activate Revision Session<br/>";
						}
						if($isActive)
						{
							$errMsg .= "A revision session for ".$tmpClass.$tmpSection." is already active. Please deactivate it first.<br/>";
						}
					}
				}
			}
		}
	}

?>

<title>Revision Session</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/revisionSession.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="css/jquery-ui.css" />
  <script>
  $(function() {
  	var dateToday = new Date();
    //$( ".datepicker" ).datepicker({ dateFormat: 'dd/mm/yy',minDate: dateToday });
    $("#fromDate").datepicker({
	      defaultDate: "today",	      
	      dateFormat: 'dd-mm-yy',
	      numberOfMonths: 1,
	      minDate	: 'today',	      
	      selectedDate: 'today',	      
	    });
    $("#fromDate").change(function (){    	
    	var fromDate = $("#fromDate").datepicker("getDate");    	    	 
    	var currentDate = new Date(); 
       	if(new Date(fromDate) < new  Date(currentDate.setHours(0, 0, 0, 0, 0))){
    		alert("Past dates are not allowed !!");
    		currentDate = $.datepicker.formatDate('dd-mm-yy', new Date(currentDate));
    		$("#fromDate").val(currentDate);
    	}
    });
  });
  var isShift=false;
	var seperator = "-";
	function DateFormat(txt , keyCode)
	{
	    if(keyCode==16)
	        isShift = true;
	    //Validate that its Numeric
	    if(((keyCode >= 48 && keyCode <= 57) || keyCode == 8 ||
	         keyCode <= 37 || keyCode <= 39 ||
	         (keyCode >= 96 && keyCode <= 105)) && isShift == false)
	    {
	        if ((txt.value.length == 2 || txt.value.length==5) && keyCode != 8)
	        {
	            txt.value += seperator;
	        }
	        return true;
	    }
	    else
	    {
	        return false;
	    }
	}	

  </script>

<script src="../userInterface/libs/css_browser_selector.js" type="text/javascript"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script>
	var langType = '<?=$language;?>';
	function load(){
		var fixedSideBarHeight = window.innerHeight;
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#fixedSideBar").css("height",fixedSideBarHeight+"px");
		$("#sideBar").css("height",sideBarHeight+"px");
		/*$("#container").css("height",containerHeight+"px");*/
		$("#features").css("font-size","1.em");
		$("#features").css("margin-left","40px");
		$(".arrow-right-blue").css("margin-left","10px");
		$(".rectangle-right-blue").css("display","block");
		$(".arrow-right-blue").css("margin-top","3px");
		$(".rectangle-right-blue").css("margin-top","3px");
		
	}
</script>
<script>

	var gradeArray   = new Array();
    var sectionArray = new Array();
	<?php
		for($i=0; $i<count($classArray); $i++)
		{
		    echo "gradeArray.push($classArray[$i]);\r\n";
		    echo "sectionArray[$i] = new Array($sectionArray[$i]);\r\n";
		}
	?>
    function validate()
    {
        if(document.getElementById('fromDate').value=='')
        {
        	alert("Please specify the activation date!");
        	return false;
        }
		if(document.getElementById('lstClass').value=='')
        {
        	alert("Please select Class");
        	return false;
        }
		setTryingToUnload();
    }

    function trim(str) {
        // Strip leading and trailing white-space
        return str.replace(/^\s*|\s*$/g, "");
    }

    function setSection(sec)
	{
		var cls = document.getElementById('lstClass').value;

		if(document.getElementById('lstSection'))
		{
		    var obj = document.getElementById('lstSection');
	        removeAllOptions(obj);
		    if(cls=="")
		    {
				$(".noSection").show();
		        document.getElementById('lstSection').style.display = "inline";
		        document.getElementById('lstSection').selectedIndex = 0;
				document.getElementById('lblSection').style.display = "inline";
		    }
		    else
		    {
		    	for(var i=0; i<gradeArray.length && gradeArray[i]!=cls; i++);
		       	if(sectionArray[i].length>0)
		       	{
	    	    	for (var j=0; j<sectionArray[i].length; j++)
	    	       	{
	    	        	OptNew = document.createElement('option');
	    	            OptNew.text = sectionArray[i][j];
	    	            OptNew.value = sectionArray[i][j];
	    	            if(sec==sectionArray[i][j])
	    	            	OptNew.selected = true;
	    	            obj.options.add(OptNew);
	    	        }
					$(".noSection").show();
	    	        document.getElementById('lstSection').style.display = "inline";
	    	        document.getElementById('lblSection').style.display = "inline";
		        }
		        else
		        {
					$(".noSection").hide();;
		        	document.getElementById('lstSection').style.display = "none";
				    document.getElementById('lblSection').style.display = "none";
					
		        }
		    }
		}
	}

	function removeAllOptions(selectbox)
	{
	    var i;
	    for(i=selectbox.options.length-1;i>0;i--)
	    {
	        selectbox.remove(i);
	    }
	}

	function deActivate(revisionSessionID, cls)
	{
			document.getElementById('revisionSessionID').value = revisionSessionID;
			document.getElementById('mode').value = "deactivate";
			document.getElementById('frmRevisionSession').submit();
	}

	function showReport(revisionSessionID)
	{
		document.getElementById('revisionSessionID').value = revisionSessionID;
		document.getElementById('frmRevisionSession').action = "studentList.php";
		document.getElementById('frmRevisionSession').submit();
	}


</script>

<body class="translation" onLoad="setSection('<?=$section?>');load()" onResize="load()">
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
	<form id="frmRevisionSession" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
		<div id="innerContainer">
			
			<div id="containerHead">
				<div id="triangle"> </div>
				<span>Revision Session</span>
			</div>
			
			
	<table class="tab" cellpadding="5">
		<tr>
	    	<td ><label for="lstClass">Class</label></td>
	        <td>
				<select id="lstClass" name="cls" onChange="setSection('')" style="width:100px";>
					<option value="" >All</option>
					<?php
						for ($i=0;$i<count($classArray);$i++)
						{
							echo "<option value='".$classArray[$i]."'";
							if ($cls==$classArray[$i])
							{
								echo " selected";
							}
							echo ">".$classArray[$i]."</option>";
						}
					?>
				</select>
	        </td>
			
	        <?php if($hasSections) { ?>
	        <td><label for="lstSection" id="lblSection">Section</label></td>
	        <td>
				<select id="lstSection" name="section" style="">
					<option value="">All</option>
				</select>
	         </td>
	         <?php } ?>
			 <td style="margin-left:10%;padding-left:0%;">
				<input type="submit" onClick="setTryingToUnload();" value="Go" id="btnGo" name="btnGo">
			 </td>
	    </tr>
	</table>


		
		
		<div id="line"> </div>
		
	<?php
	if($errMsg!="")
	{
		echo "<div align='center' style='color:red;font-size:1.5em;margin-top:15px;'>$errMsg</div><br/>";
	}
	?>
<?php if($cls!="") { ?>
<div  id="pnlActivateRevisionSession">
	<fieldset style="width:80%; font-size:1em;">
	<legend>Activate Revision Session</legend>
	
	<table align="left">
	<tr><td>
	
	<?php
	if(isset($_POST['btnGo']))
	{
			$class = $_POST['cls'];
			$section = $_POST['section'];
			$todate =  $_POST['fromDate'];

			$query =  "SELECT distinct a.teacherTopicCode, b.teacherTopicDesc FROM adepts_teacherTopicActivation a, adepts_teacherTopicMaster b where a.schoolCode = $schoolCode and a.class = $class ";
			
			if($section)
			{
				$query .= "and a.section = '$section'";
			}
			
			$startdate = explode('/',date('d/m/Y', strtotime('-60 days')));
			$startdate = $startdate[2].'-'.$startdate[1].'-'.$startdate[0]; 
			$enddate = date('Y-m-d');
			$query .= " AND (a.activationDate BETWEEN '$startdate' AND '$enddate') and a.teacherTopicCode = b.teacherTopicCode order by a.lastmodified";

			if($class)
		{
			$ttArray = array();
			$result = mysql_query($query) or die(mysql_error().$query);
			if(mysql_num_rows($result) > 0)
			{
				echo '<font style="color:red;">Select topics to be considered for revision</font>'."<br>";
				while ($line=mysql_fetch_array($result))
				{
					
					echo '<input type="checkbox" name="color[]" id="color" checked value="'.$line['teacherTopicCode'].'">'.$line['teacherTopicDesc'];
					echo '<br>';
				}
			}else
			{
				echo '<font style="color:red;">No Topic available.</font>'."<br>";
			}
		}
	}
	?>
	</td></tr>
	</table>


	<table align="right">
	<tr><td style='align:right;'>
	<table>
		<tr>
			<td>Activate From:</td>
	        <td>
				<input type="text" name="fromDate" id="fromDate"  size="10" class="datepicker" value="<?=date("d-m-Y")?>" onKeyDown="return DateFormat(this, event.keyCode)" maxlength="10" >
	        </td>
	        <td align="center">
	        	<input type="submit" name="activate" id="btnActivate" value="Activate" onClick="return validate();" <?php if($category=="School Admin" && $subcategory=="All" || mysql_num_rows($result) == 0) echo " disabled";?>>
	        </td>
	    </tr>
	</table>
	</td></tr></table>
	</fieldset>
</div>
<?php } ?>
<br/>
<div  id="pnlRevisionSessionDetails">
<?php
	$noOfRevisionSessions = count($revisionSessionArray);
	if($noOfRevisionSessions==0)
	{
		echo "<span style='color:#FF0000;margin-left:5%;font-size:15px'>No revision session activated till date!</span>";
	}
	else
	{
?>
	<table class="tblContent" border="0" cellpadding="3" cellspacing="0" width="95%">
		<tr>
		<span style="font-size:1.5em;">Past Reports </span>
		</tr>
		<tr>
			<th class="header">Sr. No.</th>
			<th class="header">Class</th>
			<th class="header">Activation Date</th>
			<th class="header">No. of students attempted</th>
			<th class="header">Status</th>
		</tr>
	<?php
		$ifActivatedTopic = 0; 
		for($i=0; $i<count($revisionSessionArray); $i++)	
	{	?>
		<tr align="center">
			<td><?=$i + 1 ?></td>
			<td><?=$revisionSessionArray[$i][1].$revisionSessionArray[$i][2]?></td>
			<td><?=substr($revisionSessionArray[$i][3],8,2)."-".substr($revisionSessionArray[$i][3],5,2)."-".substr($revisionSessionArray[$i][3],0,4)?></td>
			<?php if($revisionSessionArray[$i][5]>0)	{ ?>
				<td><a href="javascript:showReport(<?=$revisionSessionArray[$i][0]?>)" title="Click here to see the student wise report"><span style="color: #2F99CB"><b><u><?=$revisionSessionArray[$i][5]?></u></b></span></a></td>
			<?php } else {?>
				<td><?=$revisionSessionArray[$i][5]?></td>
			<?php }?>
			<td>
				<?php 
					if($revisionSessionArray[$i][4]==1)
					{
						if(!($category=="School Admin" && $subcategory=="All"))
						{
				?>
							<span class='deactivateLink'>
								<?php	if($revisionSessionArray[$i][4]==1)		{ $ifActivatedTopic++; ?>
									<a href="javascript:deActivate('<?=$revisionSessionArray[$i][0]?>','<?=$revisionSessionArray[$i][1].$revisionSessionArray[$i][2]?>')">Deactivate</a>
								<?php 	} ?>
								<input type="hidden" id="classsec" name="classsec" value="<?=$revisionSessionArray[$i][1].','.$revisionSessionArray[$i][2]?>">
							</span>			
				<?php 
						}
						else
						{
				?>
							Active
				<?php
						}
					}
						else
						{
				?>	
							Inactive	
				<?php		}?>
			</td>
		</tr>
<?php	}	?>
	</table>
<?php
	}

?>
</div>
<input type="hidden" id="revisionSessionID" name="revisionSessionID">
<input type="hidden" id="mode" name="mode">

</form>
	</div>	
</div>	

<?php include("footer.php") ?>
<?php
if(isset($_POST['mode']) && $_POST['mode']=="deactivate")
	{
		
		$data = explode(',',$_POST['classsec']);
		$sq = "select DISTINCT userID from adepts_userDetails where schoolcode=$schoolCode and childclass=$data[0] and childsection = '$data[1]' AND enabled=1 AND enddate>=curdate() And subcategory='school'";
		
		$result = mysql_query($sq);
		$totalstudent = mysql_num_rows($result);
		$rvsionseid = $_POST['revisionSessionID'];

		$query = "select DISTINCT a.userID from adepts_revisionSessionDetails a , adepts_revisionSessionMaster b where b.schoolcode=$schoolCode and b.class=$data[0] and b.section = '$data[1]' and a.revisionSessionID = b.revisionSessionID and b.revisionSessionID = $rvsionseid";

	

		$result = mysql_query($query);
		$studentattempt = mysql_num_rows($result);

		$studentcount = $totalstudent - $studentattempt;

		//echo $studentcount ; exit;
		?>
		<script src="libs/jquery-1.9.1.js"></script>
		<script>
			var assign = "delete";
		if(confirm('<?=$studentcount?> student(s) have not completed the revision session yet. \n Are you sure you want to deactivate the session?'))	// For the mantis task 8192
			{ 
											$.ajax({
					  type: 'POST',
					  url: "revisionSession.php", 
					  data: {'action': 'updatedata','revisionSessionID': <?= $rvsionseid ?>},
					  success: function(response){
							setTryingToUnload();
							window.location = window.location;
					  }
					});
			}else{
				setTryingToUnload();
			}
		    
		</script>

		<?php
	}

	if($_POST['action'] == "updatedata")
	{
		if(mysql_query("UPDATE adepts_revisionSessionMaster SET isActive=0, lastUpdatedBy='".$username."' WHERE revisionSessionID=".$_POST['revisionSessionID']))
		{
			//header('Location: revisionSession.php');
		}
	}
?>