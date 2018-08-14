<?php
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	include("header.php");
	include("classes/testTeacherIDs.php");
	include("../userInterface/functions/functions.php");

	error_reporting(E_ERROR);
	//print_r($_REQUEST);

	if(!isset($_SESSION['userID']))
	{
		header("Location:logout.php");
		exit;
	}
	$userID 	= $_SESSION['userID'];
	$category   = $_SESSION['admin'];
	$schoolCode = $_SESSION['schoolCode'];

	$query  = "SELECT childName,username, category, subcategory FROM adepts_userDetails WHERE userID=".$userID;
	$result = mysql_query($query) or die(mysql_error());
	$line   = mysql_fetch_array($result);
	$name 	    = $line[0];
	$loginID    = $line[1];
	$subcategory = $line[3];


	$classArray = array();
	if(isset($_REQUEST['section']))
	    $section = $_REQUEST['section'];
	else
		$section = "";


	if(strcasecmp($category,"School Admin")==0)
	{
		$query  = "SELECT distinct childClass FROM adepts_userDetails a, adepts_ncertExerciseMaster b WHERE a.childClass=b.class AND schoolCode=$schoolCode AND category='STUDENT' AND subcategory='School' AND endDate>=curdate() AND enabled=1 AND subjects like '%".SUBJECTNO."%' AND childClass>5 AND status='Live' ORDER BY cast(childClass as unsigned)";
		$result = mysql_query($query);
		while ($line=mysql_fetch_array($result))
		{
			array_push($classArray, $line[0]);
		}
	}
	else if (strcasecmp($category,"TEACHER")==0)
	{
		$query = "SELECT distinct a.class FROM adepts_teacherClassMapping a, adepts_ncertExerciseMaster b WHERE a.class=b.class AND status='Live' AND userID=$userID  AND subjectno=".SUBJECTNO;
		$result = mysql_query($query) or die("<br>Error in teacher class query - ".mysql_error());
		while($line=mysql_fetch_array($result))
		{
			array_push($classArray, $line[0]);
		}
	}
	elseif(strcasecmp($category,"Home Center Admin")==0)
	{
		$query  = "SELECT distinct childClass FROM adepts_userDetails a, adepts_ncertExerciseMaster b WHERE a.childClass=b.class AND status='Live' schoolCode=$schoolCode AND subcategory='Home Center' AND category='STUDENT'  AND subjects like '%".SUBJECTNO."%' AND childClass>5 ORDER BY cast(childClass as unsigned)";
		$result = mysql_query($query);
		while ($line=mysql_fetch_array($result))
		{
			array_push($classArray, $line[0]);
		}
	}
	else
	{
		echo "You are not authorised to access this page!";
		exit;
	}
	$childClass = $_POST['childClass'];
	$query = "SELECT description, status, exerciseCode, chapterName, chapterNo, exerciseNo FROM adepts_ncertExerciseMaster WHERE class='$childClass' AND status='Live' ORDER BY chapterNo, exerciseNo";
	$result = mysql_query($query) or die(mysql_error());
	$broadTopics = $teacherTopics = array();
	$topic = "";

	$temp_rows = mysql_num_rows($result);
	while ($line=mysql_fetch_array($result))
	{
		$lineCluster = $line['chapterName'];
		if($topic!=$lineCluster)
		{
			$topic = $lineCluster;
			array_push($broadTopics,$topic);
			$srno=0;
		}
		$ttCode = $line['exerciseCode'];
		$teacherTopics[$topic][$srno][0] = $line[0];
		$teacherTopics[$topic][$srno][1] = $line[1];
		$teacherTopics[$topic][$srno][2] = $ttCode;
		$teacherTopics[$topic][$srno][3] = $_POST['childClass'];
		$srno++;
	}

	if(isset($_POST['activate']) && ($_POST['activate']=="Submit"))
	{
		$current_active_topic = array();
		$activeDates = $_POST["activeDate"];
		$dueDates = $_POST["dueDate"];
		$get_active_topic_query = "SELECT a.exerciseCode FROM adepts_ncertHomeworkActivation a, adepts_ncertExerciseMaster b
		                           WHERE  a.exerciseCode=b.exerciseCode AND a.schoolCode=$schoolCode AND a.class=".$_POST['childClass']."";
		if($section!="")
		{
			$sectionStr = "";
			$sectionArr = explode(",",$section);
			for($k=0; $k<count($sectionArr); $k++)
				$sectionStr .= "'".$sectionArr[$k]."',";
			$sectionStr = substr($sectionStr,0,-1);
			$get_active_topic_query .= " AND section in ($sectionStr)";
		}

		//echo "<br>Get topic query - ".$get_active_topic_query;
		$get_active_topic_result = mysql_query($get_active_topic_query) or die("<br>Error in get topic query ".mysql_error());
		while ($data=mysql_fetch_array($get_active_topic_result))
		{
			array_push($current_active_topic, $data[0]);
		}

		$rows = $_POST['rows'];
		$selected_topics = array();
		for ($i=1;$i<=$rows;$i++)
		{
			if (isset(${"chk_".$i}) && ${"chk_".$i}!='')
			{
				array_push($selected_topics, ${"chk_".$i});
			}
		}
		for ($i=0;$i<count($selected_topics);$i++)
		{
			if (!in_array($selected_topics[$i], $current_active_topic))
			{
				$activeDate = $activeDates[$selected_topics[$i]];
				$dueDate = $dueDates[$selected_topics[$i]];
				$insert_query = "INSERT INTO adepts_ncertHomeworkActivation SET schoolCode=$schoolCode, class=".$_POST['childClass'].", section='".$section."', exerciseCode='".$selected_topics[$i]."', activationDate='".parseDate($activeDate)."',deactivationDate='".parseDate($dueDate)."', lastModifiedBy='".$name."'";
				$insert_result = mysql_query($insert_query) or die("Error in insert query - ".mysql_error());
			}
			else
			{
				$activeDate = $activeDates[$selected_topics[$i]];
				$dueDate = $dueDates[$selected_topics[$i]];
				$update_query = "UPDATE adepts_ncertHomeworkActivation SET deactivationDate='".parseDate($dueDate)."', activationDate='".parseDate($activeDate)."', lastModifiedBy='".$name."' WHERE schoolCode=$schoolCode AND class=".$_POST['childClass']." AND section='".$section."' AND exerciseCode='".$selected_topics[$i]."'";
				$update_result = mysql_query($update_query) or die("Error in update query - ".mysql_error());
				
			}
		}
		//echo "<br>Current Active Topic Length is - ".count($current_active_topic);
		/*for ($i=0;$i<count($current_active_topic);$i++)
		{
			if (!in_array($current_active_topic[$i], $selected_topics))
			{
				$update_query = "UPDATE adepts_ncertHomeworkActivation SET deactivationDate='".date("Y-m-d")."' , lastModifiedBy='".$name."' WHERE schoolCode=$schoolCode AND class=".$_POST['childClass']." AND section='".$section."' AND exerciseCode='".$current_active_topic[$i]."' AND deactivationDate='0000-00-00'";
				//$update_result = mysql_query($update_query) or die("Error in update query - ".mysql_error());
			}
		}*/
	}
	$active_topic = array();
	$active_dates = array();
	$status_array = array();
	$select_query = "SELECT a.exerciseCode, DATE_FORMAT(activationDate, '%d-%m-%Y') as active_date, DATE_FORMAT(deactivationDate, '%d-%m-%Y') as deactive_date
	                 FROM adepts_ncertHomeworkActivation a, adepts_ncertExerciseMaster b WHERE a.exerciseCode=b.exerciseCode AND a.schoolCode=$schoolCode";
	if (isset($_POST['childClass']) && $_POST['childClass']!="")
	{
		$select_query .= " AND a.class=".$_POST['childClass'];
	}
	if ($section!="")
	{
		$select_query .= " AND section='$section'";
	}
	$select_query .= " ORDER BY activationDate";
	//echo "<br>Select query is - ".$select_query;
	if (isset($_POST['childClass']) && $_POST['childClass']!="")
	{
		$select_result = mysql_query($select_query) or die("Error in select query - ".mysql_error());
		if (mysql_num_rows($select_result)>0)
		{
			while ($select_data = mysql_fetch_array($select_result))
			{
				array_push($active_topic, $select_data[0]);
				$active_dates[$select_data[0]] = array($select_data[1], $select_data[2]);
				if (parseDate($select_data[2]) >= date('Y-m-d') && parseDate($select_data[1]) <= date('Y-m-d'))
				{
					$status_array[$select_data[0]] = "Active";
				}
			}
		}
		
		$userArray = getStudentDetails($childClass,$schoolCode,$section);
		$userIDs = array_keys($userArray);
		
		$questionDetailArr = getQuestionDetails($childClass);
		$exerciseDetailArr = getReviewForExcercise($childClass,$schoolCode,$userIDs);
	}

?>

<title>Activate Homework</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" style="text/css" href="css/colorbox.css">
<link href="css/activateHomework.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="libs/css/jquery-ui.css" />
  <script>
  $(function() {
    $( ".datepicker" ).datepicker({ dateFormat: 'dd-mm-yy',minDate:0 });
  });
  </script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script src="libs/jquery.colorbox-min.js" type="text/javascript"></script>
<script src="libs/idletimeout.js" type="text/javascript"></script>
<script type = "text/javascript">
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
function checkDate(type,row){
	var today = new Date().setHours(0, 0, 0, 0, 0);
	var id = "#"+type+"_"+row;
	//var currentdate = $(id).val();
	var currentdate = $(id).datepicker("getDate");
	if(currentdate.length != 0)
 	{	
		oldDate = $(id).parent().parent().text();
		oldDate = oldDate.trim();	
		if(new Date(currentdate) < new Date(today))
		{
	         // if the test fails, change the value to default
	        alert("Past dates are not allowed !!");
	        if(oldDate != '')
	        {                   		         
		         $(id).val(oldDate);     	                           
		 	}
		 	else
		 	{
		     	if(type == "assignDate"){		     		
		     		$(id).datepicker('setDate',new Date(currentdate));
		     	}
		     	else
		     	{
		     	    $(id).val('');
		 	    }
		     }
	    }
	    else{
	    	if(type == "assignDate")
	    	{
	    		var dueDate = $("#dueDate_"+row).datepicker("getDate");
	    		if(dueDate.length != 0){
	    		if(new  Date(dueDate) < new Date(currentdate))
	    		{
	    			alert("Assign date should come before Due date");  	    			      				
	    			$(id).val(oldDate);
	    			$(id).focus();
	    		}
	    		// else{
	    		// 	currentdate = $.datepicker.formatDate('dd-mm-yy',new Date(currentdate));
	    		// 	$("#dueDate_"+row).datepicker('destroy');
	    		// 	$("#dueDate_"+row).datepicker({dateFormat: 'dd-mm-yy',minDate: new Date(currentdate)});
	    		// }
	    	}
	    	}
	    	else
	    	{
	    		var assignDate = $("#assignDate_"+row).datepicker("getDate");
	    		if(assignDate.length !=0){
		    		if(new  Date(assignDate) > new Date(currentdate))
		    		{
		    			alert("Due date should come after assign date !!");
		    			if(oldDate != "")
		    			{
		    				// if(new  Date(oldDate) < new Date(assignDate))
		    				// {
			    			// 	assignDate = $.datepicker.formatDate('dd-mm-yy',new Date(assignDate));
			    			// 	$(id).datepicker('setDate',new Date(assignDate));
			    			// }
			    			// else
			    			// {			    			      				
			    			$(id).val(oldDate);			    			
			    			//}
						}
						else
						{
							$(id).val('');
						}
		    			$(id).focus();  
	    			}  			
	    		}
	    		// else{
	    		// 	currentdate = $.datepicker.formatDate('dd-mm-yy',new Date(currentdate));
	    		// 	$("#assignDate_"+row).datepicker('destroy');
	    		// 	$("#assignDate_"+row).datepicker({dateFormat: 'dd-mm-yy',minDate:0,maxDate: new Date(currentdate)});
	    		// }
	    	}
	    }
	}
}
		
</script>
<script>
	$(document).ready(function(e) {
        $(".activateCheck").change(function(e) {
			if($(this).is(":checked"))
			{
            	$(this).parents("tr:first").find(".activeDateText,.dueDateText").hide();
            	$(this).parents("tr:first").find(".activeDateCalender, .dueDateCalender").show();
			}
			else
			{
				var assignDate = $(this).parents("tr:first").find(".activeDateText").text().trim();
				if(assignDate == ''){
					var currentDate = $.datepicker.formatDate('dd-mm-yy',new Date()); 					
					$(this).parents("tr:first").find(".activeDateCalender").find("input").val(currentDate);
				}else{										
					$(this).parents("tr:first").find(".activeDateCalender").find("input").val(assignDate);
				}
				var dueDate = $(this).parents("tr:first").find(".dueDateText").text().trim();				
					$(this).parents("tr:first").find(".dueDateCalender").find("input").val(dueDate);			    
            	$(this).parents("tr:first").find(".activeDateText,.dueDateText").show();
            	$(this).parents("tr:first").find(".activeDateCalender, .dueDateCalender").hide();
			}
        });
	<?php
	if(isset($_POST['childClass']))
	{
	?>
		$("#childClass,#section").attr("disabled",true);
		$("#selectClass").css("visibility","visible");
		var html="<input type='checkbox' name='onlyActivatedTopics' id='onlyActivatedTopics' value='on' <? if ((isset($_POST['onlyActivatedTopics']) && $_POST['onlyActivatedTopics']=='on')) echo ' checked';?> onclick='submitCheckBox()'>Show only activated exercises";
		$("#showActivated").html(html);
	<?php
	}
	?>
    });
	function showReport(winTitle, exerciseCode)
	{
		$("#exerciseNo").text(winTitle);
		var urlOpen = 'ncertHomeworkStatus.php?exerciseCode='+exerciseCode+'&winTitle='+winTitle+'&schoolCode=<?=$schoolCode?>&childClass=<?=$childClass?>&section=<?=$section?>';
		urlOpen = encodeURI(urlOpen);
		$.fn.colorbox({'href':urlOpen,'open':true,'escKey':true, 'height':600, 'width':800,
			onOpen: function(){
				setTimeout(function(){tryingToUnloadPage=false},500);
			}
		});
	}
	var flag_confirm = 1;
	<?php
	for ($i=0; $i<count($classArray); $i++)
	{
		$javascript_section_array = "var section_".$classArray[$i]." = new Array( ";
		if($category=="School Admin")
		{
			$section_query = "SELECT DISTINCT(childSection) FROM adepts_userDetails WHERE category='STUDENT' AND schoolCode=".$schoolCode." AND childClass='".$classArray[$i]."' AND subjects LIKE '%".SUBJECTNO."%' AND endDate>=curdate() AND enabled=1 ORDER BY childSection";
		}
		if($category=="Home Center Admin")
		{
			$section_query = "SELECT DISTINCT(childSection) FROM adepts_userDetails WHERE category='STUDENT' AND subcategory='Home Center' AND schoolCode=".$schoolCode." AND childClass='".$classArray[$i]."' AND enabled=1 ORDER BY childSection";
		}
		if ($category=="TEACHER")
		{
			$section_query = "SELECT DISTINCT(section) FROM adepts_teacherClassMapping WHERE userID=$userID AND class=$classArray[$i] AND subjectno=".SUBJECTNO." ORDER BY section";
		}
		$section_result = mysql_query($section_query) or die("<br>Error in section query - ".mysql_error());
		while ($section_data = mysql_fetch_array($section_result))
		{
			if ($section_data[0]!="")
			{
				$javascript_section_array .= "'".$section_data[0]."',";
			}
		}
		$javascript_section_array = substr($javascript_section_array, 0, -1);
		$javascript_section_array .= ");\n";
		print($javascript_section_array);
	}
	?>

	function submitForm()
	{
		if (document.getElementById('childClass').value=="")
		{
			alert('Please select class');
			document.getElementById('childClass').focus();
			return false;
		}

		//alert(document.getElementById('section').length);
		var selectedSection = document.getElementById('section').value;
		//alert(selectedSection.length)
		if (selectedSection.length > 1)
		{
			//alert('Please select section');
			//document.getElementById('section').focus();
			//return false;
		}
		setTryingToUnload();
		return true;
	}
	function populateSection()
	{
		removeAllOptions(document.getElementById('section'));
		var schoolCode = <?=$schoolCode?>;
		var childClass = document.getElementById('childClass').value;
		var childSection = "<?=$section?>";
		//alert("School Code is - "+schoolCode);
		if (schoolCode)
		{
			if(childClass != "")
			{
				var x = eval('section_'+childClass);
				var elSel = document.getElementById('section');
				//alert(x);
				if(x.length != 1)
				{
					var OptNew = document.createElement('option');
					OptNew.text = "Select";
					OptNew.value = "";
					elSel.options.add(OptNew);
				}
				for (j=0; j<x.length; j++)
				{
					var OptNew = document.createElement('option');
					OptNew.text = eval('section_'+childClass)[j];
					OptNew.value = eval('section_'+childClass)[j];
					if (eval('section_'+childClass)[j]==childSection)
					{
						OptNew.selected = true;
					}
					elSel.options.add(OptNew);
				}
				if(x.length==0)
					$('#section').hide();
				else
					$("#section").show();
				var sel_length = elSel.length;
				//alert(sel_length);
				if (sel_length==0)
				{
					var OptNew = document.createElement('option');
					OptNew.text = "Select";
					OptNew.value = "";
					elSel.options.add(OptNew);
					elSel.disabled = true;
				}
				else
				{
					if(sel_length == 1)
					{
						$($("#childSection option")[1]).attr("selected","selected");
					}
					<?php if(!isset($_POST['childClass'])) { ?>
						elSel.disabled = false;
					<?php } ?>
				}				
			}
			else
			{
				var elSel = document.getElementById('section');
				var OptNew = document.createElement('option');
				OptNew.text = "Select";
				OptNew.value = "";
				elSel.options.add(OptNew);
				elSel.disabled = true;
			}
		}
	}
	function removeAllOptions(selectbox)
	{
		var i;
		for(i=selectbox.options.length-1;i>=0;i--)
		{
			selectbox.remove(i);
		}
	}
	function trim(str)
	{
		// Strip leading and trailing white-space
		return str.replace(/^\s*|\s*$/g, "");
	}

	function validateActivate()
	{
		if (document.getElementById('childClass').value=="")
		{
			alert('Please select class');
			document.getElementById('childClass').focus();
			return false;
		}
		if (document.getElementById('section').length > 1)
		{
			if (document.getElementById('section').value=="")
			{
				alert('Please select section');
				document.getElementById('section').focus();
				return false;
			}
		}
		if($(".activateCheck:checked").length == 0)
		{
			alert("Select atleast one checkbox for change !");
			return false;
		}
		else
		{		
			var returnVal = true;
			var forwardDateBool = false;
			$(".activateCheck:checked").each(function(index, element) {
				var tmpActiveDate = $.trim($(this).parents("tr:first").find(".activeDateCalender input[type=text]").val());
				var tmpDueDate = $.trim($(this).parents("tr:first").find(".dueDateCalender input[type=text]").val());
				var tmpLastActiveDate = $.trim($(this).parents("tr:first").find(".activeDateText").text());
				var tmpLastDueDate = $.trim($(this).parents("tr:first").find(".dueDateText").text());
				var toDay = new Date();
				toDay.setDate(toDay.getDate() - 1);
				if(tmpActiveDate == "" || tmpDueDate == "")
				{
					returnVal = false;
					alert("Please select all dates !!")
					return false;
				}
				else if(tmpLastActiveDate == "" && tmpLastDueDate == "" && (parseDate(tmpActiveDate) < toDay || parseDate(tmpDueDate) < toDay))
				{
					returnVal = false;
					alert("Past dates are not allowed !!")
					return(false);
				}
				else if(parseDate(tmpActiveDate)>parseDate(tmpDueDate))
				{
					returnVal = false;
					alert("Due date should come after assign date !!")
					return(false);
				}
				else if(!isDate(tmpActiveDate))
				{
					returnVal = false;
					return(false);
				}
				else if(!isDate(tmpDueDate))
				{
					returnVal = false;
					return(false);
				}
				else if(tmpLastActiveDate != "" && parseDate(tmpActiveDate) > parseDate(tmpLastActiveDate))
				{
					forwardDateBool = true;
				}
            });
			setTryingToUnload();
			if(forwardDateBool)
				return(confirm("You are about to forward assigned date.\n\nPlease note that if a student has started this exercise, he/she can continue to work on it."));
			else{
				return(returnVal);
			}
		}
		
	}
	function submitFrom()
	{
		setTryingToUnload();
		document.frmActivateTopic.submit();
	}
	function submitCheckBox()
	{
		setTryingToUnload();
		document.frmActivateTopic.submit();
	}
	function parseDate(str)
	{
		var parts = str.split("-");
		return new Date(parts[2], parts[1]-1, parts[0]);
	}
	//populateSection();
</script>
<script>
	var langType = '<?=$language;?>';
	function load(){
		var fixedSideBarHeight = window.innerHeight;
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#fixedSideBar").css("height",fixedSideBarHeight+"px");
		$("#sideBar").css("height",sideBarHeight+"px");
		$("#classes").css("font-size","1.4em");
		$("#classes").css("margin-left","40px");
		$(".arrow-right").css("margin-left","10px");
		$(".rectangle-right").css("display","block");
		$(".arrow-right").css("margin-top","3px");
		$(".rectangle-right").css("margin-top","3px");
	}
</script>
</head>
<body class="translation" onLoad="load();populateSection();" onResize="load()" onmousemove="reset_interval()" onclick="reset_interval()" onkeypress="reset_interval()" onscroll="reset_interval()">
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
					<div id="pageText">NCERT HOMEWORK ACTIVATION</div>
				</div>
				<input type="button" class="button" id="selectClass" name="back" value="Select Another Class" onClick="javascript:setTryingToUnload();window.location='activateHomework.php';">
			</div>
			<form id="frmActivateTopic" name="frmActivateTopic" method="post" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="setTryingToUnload();">
			<table id="topicDetails">
				<td width="6%"><label for="childClass">Class</label></td>
		        <td width="25%">
		            <select name="childClass" id="childClass" onChange="populateSection()" style="width:65%;">
								<?php
									$class_string = "";
									if(count($classArray) != 1)
										echo '<option value="">Select</option>';
									for ($i=0;$i<count($classArray);$i++)
									{
										if ($classArray[$i]=="")
										{
											$class_string = "";
											continue;
										}
										$class_string .= "<option value=\"".$classArray[$i]."\"";
										if (isset($_POST['childClass']) && $_POST['childClass']==$classArray[$i])
										{
											$class_string .= " selected";
										}
										$class_string .= " >".$classArray[$i]."</option>";
										echo $class_string;
										$class_string = "";
									}
								?>
							</select>
		        </td>
				<td width="6%"><label for="section">Section</label></td>
				<td width="25%">
					<select name="section" id="section" style="width:65%;">
						<option value="">Select</option>
						<?php
							if($section!='')
							{
								$tmpArray = explode(",",$section);
								echo "<option value='".$section."' selected>";
								if(count($tmpArray) != 1)
									echo "Select";
								else
									echo $section;
								echo "</option>";
							}
						?>
					</select>
				</td>
				<td id="showActivated" width="25%"><div id="checkActive"><input type="submit" class="button" name="btnGenerate" id="generate" value="Go" onClick="return submitForm();"></div></td>
			</table>
			<?php
				if ((isset($_POST['save']) && $_POST['save']=="Get Exercise List") || (isset($_POST['activate']) && $_POST['activate']=="Submit") || isset($_POST['onlyActivatedTopics']) || isset($_POST['childClass']))
				{
					$rows = 1;
			?>
						<input type="hidden" name="childClass" value="<?=$_POST['childClass']?>" />
						<input type="hidden" name="section" value="<?=$_POST['section']?>" />
						<table id="pagingTable">
					        <td width="35%">CLASS <?=$_POST['childClass']?><?php if ($section!="") echo "$section";?></td>
							<td>
								<div class="textRed">* The number in brackets is the total number of parts of questions in the exercise.</div>
							</td>
						</table>
						<!-- <input type="hidden" name="childClass" id="hidChildClass" value="<?=$_POST['childClass']?>">
						<input type="hidden" name="section" id="hidSection" value="<?=$section?>">
						<input type="hidden" name="schoolCode" id="hidSchoolCode" value="<?=$schoolCode?>"> -->
						<table align="center" border="1" cellpadding="3" cellspacing="0" class="gridtable" width="100%">
							<tr>
								<th width="5%" align="center" class="header">Edit</th>
								<th width="40%" valign="center" align="left" class="header">Exercise</th>
								<th width="10%" align="center" class="header">Total Questions * (Parts)</th>
								<th width="15%" align="center" class="header">Assigned On</th>
								<th width="15%" align="center" class="header">Due On</th>
								<th width="15%" align="center" class="header">Submitted by<br>(before due date)</th>
							</tr>
							<?php
							$foundarray = array();
							for ($i=0; $i<count($broadTopics);$i++)
							{
								if (isset($_POST['classification']) && $_POST['classification']!="")
								{
									if ($_POST['classification']!=$broadTopics[$i])
									{
										continue;
									}
								}
								if (isset($_POST['onlyActivatedTopics']) && $_POST['onlyActivatedTopics']=="on")
								{
									$count1=0;
									for($j=0; $j<count($teacherTopics[$broadTopics[$i]]); $j++)
									{
										if(in_array($teacherTopics[$broadTopics[$i]][$j][2], $active_topic) && ($status_array[$teacherTopics[$broadTopics[$i]][$j][2]]=="Active"))
											$count1++;
									}
									if($count1==0)
										continue;
								}
								else
								{
									$count1=0;
									if (!(isset($_POST['allClass']) && $_POST['allClass']=="on"))
									{
										for($j=0; $j<count($teacherTopics[$broadTopics[$i]]); $j++)
										{
											$innerarray = $teacherTopics[$broadTopics[$i]][$j];
											$class_explode = explode(",",$innerarray[3]);
											if ((!in_array($teacherTopics[$broadTopics[$i]][$j][2], $active_topic)) && (!in_array($_POST['childClass'],$class_explode)))
											{
												continue;
											}
											else
											{
												$count1++;
											}
										}
									}
									if($count1==0 && (!isset($_POST['allClass'])))
										continue;
								}
								?>
							<tr style='background-color:#F5DEB3;'>
								<td width="5%" class="header">&nbsp;</td>
								<td colspan="5" valign="top" align="left" class="header"><strong><?php echo $broadTopics[$i]?></strong></td>
							</tr>
							<?php
								for($j=0; $j<count($teacherTopics[$broadTopics[$i]]); $j++)
								{
									$innerarray = $teacherTopics[$broadTopics[$i]][$j];
									if ((isset($_POST['onlyActivatedTopics']) && $_POST['onlyActivatedTopics']=="on") && !(in_array($teacherTopics[$broadTopics[$i]][$j][2], $active_topic) && ($status_array[$teacherTopics[$broadTopics[$i]][$j][2]]=="Active")))
									{
										continue;
									}
									if (!isset($_POST['allClass']))
									{
										if ((!in_array($teacherTopics[$broadTopics[$i]][$j][2], $active_topic)) && (!in_array($_POST['childClass'],$class_explode)))
										{
											continue;
										}
									}
									if(!isset($active_dates[$teacherTopics[$broadTopics[$i]][$j][2]]))
									{
										$active_dates[$teacherTopics[$broadTopics[$i]][$j][2]] = array("&nbsp;","&nbsp;");
									}
									if(!isset($status_array[$teacherTopics[$broadTopics[$i]][$j][2]]))
									{
										$status_array[$teacherTopics[$broadTopics[$i]][$j][2]] = "";
									}

							?>
							<tr>
								<td align="center">
								<?php							
								if($active_dates[$teacherTopics[$broadTopics[$i]][$j][2]][1] == "&nbsp;" || (strtotime($active_dates[$teacherTopics[$broadTopics[$i]][$j][2]][1])>=strtotime(date('d-m-Y'))))
								{	?>								
									<input type="checkbox" name="chk_<?=$rows?>" value="<?=$teacherTopics[$broadTopics[$i]][$j][2]?>" class="activateCheck">
								<?php }	?>							
								</td>
								<td valign="middle" align="left" class="topic">
                                	<a href="ncertSampleQuestions.php?exerciseCode=<?=$teacherTopics[$broadTopics[$i]][$j][2]?>" style="text-decoration:underline;" title="Click here to see exercise questions!">
                                    	Exercise <?=preg_replace("/[^0-9,.]/", "",$teacherTopics[$broadTopics[$i]][$j][0])?>
                                    </a>
								</td>
								<td align="center">
                                	<?=(isset($questionDetailArr[$teacherTopics[$broadTopics[$i]][$j][2]])?$questionDetailArr[$teacherTopics[$broadTopics[$i]][$j][2]]:"0 (0)")?>
								</td>
								<td nowrap align="center">
									<span class="activeDateText"><?php echo $active_dates[$teacherTopics[$broadTopics[$i]][$j][2]][0];?></span>
									<span class="activeDateCalender"><input id="assignDate_<?=$rows?>" onchange="checkDate('assignDate','<?=$rows?>')" type="text" class="datepicker" onKeyDown="return DateFormat(this, event.keyCode)" maxlength="10" name="activeDate[<?=$teacherTopics[$broadTopics[$i]][$j][2]?>]" value="<?=($active_dates[$teacherTopics[$broadTopics[$i]][$j][2]][0] != "&nbsp;")?$active_dates[$teacherTopics[$broadTopics[$i]][$j][2]][0]:date('d-m-Y');?>" size="10"></span>
								</td>
								<td nowrap align="center">
									<span class="dueDateText"><?php echo $active_dates[$teacherTopics[$broadTopics[$i]][$j][2]][1];?></span>
									<span class="dueDateCalender"><input id="dueDate_<?=$rows?>" onchange="checkDate('dueDate','<?=$rows?>')" type="text" class="datepicker" onKeyDown="return DateFormat(this, event.keyCode)" maxlength="10"  name="dueDate[<?=$teacherTopics[$broadTopics[$i]][$j][2]?>]" value="<?=($active_dates[$teacherTopics[$broadTopics[$i]][$j][2]][1] != "&nbsp;")?$active_dates[$teacherTopics[$broadTopics[$i]][$j][2]][1]:"";?>" size="10"></span>
								</td>
								<td nowrap align="center">
                                    <a style="text-decoration:underline;" href="javascript:void(0)" onClick="showReport('Exercise <?=preg_replace("/[^0-9,.]/", "",$teacherTopics[$broadTopics[$i]][$j][0])?>','<?=$teacherTopics[$broadTopics[$i]][$j][2]?>')">
										<?=(isset($exerciseDetailArr[$teacherTopics[$broadTopics[$i]][$j][2]])?$exerciseDetailArr[$teacherTopics[$broadTopics[$i]][$j][2]]:"0 (0)")?>
									</a>
								</td>
							</tr>
						<?php
									$rows = $rows+1;
								}
							}
						?>
							<tr>
								<input type="hidden" name="rows" id="rows" value="<?=$rows?>">
								<td colspan="7" align="center">
									<input type="submit" class="button" name="activate" value="Submit" onClick="return validateActivate();" <?php if(($category=="School Admin" && $subcategory=="All") || in_array($loginID,$testIDArray)) echo " disabled";?>>
								</td>
							</tr>
						</table>
						<br/>
			<?php
				}
			?>
			</form>
			
		</div>
	</div>
	<div id="incompleteListDiv">
			    <ul id="incompleteListUL">
			    </ul>
			</div>

<?php include("footer.php") ?>

<?php
function getReviewForExcercise($childClass,$schoolCode,$userIDs)
{
	$exerciseDetailArr = array();
	$userIDs = implode(",",$userIDs);
	$query = "SELECT COUNT(DISTINCT userID), COUNT(DISTINCT IF(a.submitDate < b.deactivationDate,userID,NULL)), a.exerciseCode FROM adepts_ncertHomeworkStatus a, adepts_ncertHomeworkActivation b WHERE result='SUCCESS' AND userID IN ($userIDs) AND a.exerciseCode=b.exerciseCode AND b.schoolCode='$schoolCode' GROUP BY a.exerciseCode HAVING a.exerciseCode IN (SELECT exerciseCode FROM adepts_ncertExerciseMaster WHERE class='$childClass' AND status='Live')";
	$result = mysql_query($query);
	while($row = mysql_fetch_array($result))
	{
		$exerciseDetailArr[$row[2]] = $row[0] ." ($row[1])";
	}
	return($exerciseDetailArr);
}

function getCompletedExercises($userIDs)
{
	$completeExArray = array();
	$userIDs = implode(",",$userIDs);
	$sql = "SELECT exerciseCode, GROUP_CONCAT(DISTINCT userID) FROM adepts_ncertHomeworkStatus WHERE result='SUCCESS' AND userID IN ($userIDs) GROUP BY exerciseCode";
	$result = mysql_query($sql);
	while($row = mysql_fetch_array($result))
	{
		$completeExArray[$row[0]] = $row[1];
	}
	return($completeExArray);
}
function getQuestionDetails($childClass)
{
	$questionDetailArr = array();
 	$query = "SELECT COUNT(DISTINCT qcode), COUNT(DISTINCT groupID), exerciseCode FROM adepts_ncertQuestions WHERE status=3 GROUP BY exerciseCode HAVING exerciseCode IN (SELECT exerciseCode FROM adepts_ncertExerciseMaster WHERE class='$childClass' AND status='Live')";
	$result = mysql_query($query);
	while($row = mysql_fetch_array($result))
	{
		$questionDetailArr[$row[2]] = $row[1] ." ($row[0])";
	}
	return($questionDetailArr);
}
function parseDate($dmyDate)
{
	$tmpArr = explode("-",$dmyDate);
	return($tmpArr[2]."-".$tmpArr[1]."-".$tmpArr[0]);
}
?>