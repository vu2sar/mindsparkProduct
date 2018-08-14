<?php
    set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	include("header.php");
	include("../slave_connectivity.php");
    include("../userInterface/functions/orig2htm.php");
	include("../userInterface/constants.php");
    error_reporting(E_ERROR);
?>

<?php
    if(!isset($_SESSION['userID']) || $_SESSION['userID']=="")
    {
    	echo "You are not authorised to access this page!";
    	exit;
    }
    $userID     = $_SESSION['userID'];
    $category   = $_SESSION['admin'];
    $schoolCode = $_SESSION['schoolCode'];
	

	if(strcasecmp($category,"School Admin")==0)
	{
		$query  = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
		           FROM     adepts_userDetails
		           WHERE    schoolCode=$schoolCode AND category='STUDENT' AND subcategory='School' AND enabled=1 AND subjects LIKE '%".SUBJECTNO."%' AND endDate>=curdate()
		           GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
	}
	elseif (strcasecmp($category,"Teacher")==0)
	{
		$query = "SELECT   class, group_concat(distinct section ORDER BY section)
				  FROM     adepts_teacherClassMapping
				  WHERE    userID = $userID AND subjectno='".SUBJECTNO."'
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
	$topic_code_array_string = $topic_desc_array_string = "";
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

		//Get the list of activated topics

		$topic_code_array_string .= "var code_".$line[0]." = new Array( ";
		$topic_desc_array_string .= "var desc_".$line[0]." = new Array( ";

		$topic_query="SELECT distinct a.teacherTopicCode, a.teacherTopicDesc 
					  from adepts_teacherTopicMaster a, adepts_topicRevisionDetails b, adepts_userDetails c
					  where a.teacherTopicCode = b.teacherTopicCode and b.userID = c.userID and c.schoolCode = $schoolCode and c.childClass=$line[0] and c.subjects=2 
					  ORDER BY a.teacherTopicDesc";

		$topic_result = mysql_query($topic_query) or die("<br>Error in topic query - ".mysql_error().$topic_query);
		while ($topic_data = mysql_fetch_array($topic_result))
		{
			$topic_code_array_string .= "'".$topic_data['teacherTopicCode']."',";
			$topic_desc_array_string .= "'".$topic_data['teacherTopicDesc']."',";
		}
		$topic_code_array_string = substr($topic_code_array_string, 0, -1);
		$topic_desc_array_string = substr($topic_desc_array_string, 0, -1);
		$topic_code_array_string .= ");\n";
		$topic_desc_array_string .= ");\n";
	}	

	$cls     = isset($_REQUEST['cls'])?$_REQUEST['cls']:"";
	$section = isset($_REQUEST['section'])?$_REQUEST['section']:"";
	$topic   = isset($_REQUEST['topic'])?$_REQUEST['topic']:"";
	
	$tillDate = date('Y-m-d', strtotime(str_replace('-', '-', $tillDate)));
	/*$fromDate = date('Y-m-d', strtotime(str_replace('-', '-', $fromDate)));
	*/
	$tillDate = isset($_POST['tillDate'])?$_POST['tillDate']:date("d-m-Y");
	/*$fromDate = isset($_POST['fromDate'])?$_POST['fromDate']:date("d-m-Y",mktime(0,0,0,date("m"),1,date("Y")));*/

	$todaysDate = date("d");
	$studentDetails = array();
	if($cls!="")
	{
	
		$tillDate = date('Y-m-d', strtotime(str_replace('-', '-', $tillDate)));
		$fromDate = date('Y-m-d', strtotime(str_replace('-', '-', $fromDate)));
		$query = "SELECT a.userID, a.childName, a.childClass, a.childSection, count(b.srno) as totalq, sum(R) as correct, count(distinct teacherTopicCode) as nooftopics, max(b.attemptedDate) as max_attemptDate
		          FROM   adepts_userDetails a LEFT JOIN ".TBL_TOPIC_REVISION." b ON (a.userID=b.userID";
		if($fromDate!="")
	        $query .= " AND attemptedDate>='$fromDate'";
	    if($tillDate!="")
	        $query .= " AND attemptedDate<='$tillDate'";
		$query .= ")
		          WHERE  category='STUDENT' AND subcategory='School' AND enabled=1 AND enddate>=curdate() AND
		                 schoolCode=$schoolCode AND childClass='$cls'";
		if($section!="")
			$query .= " AND childSection='$section' ";
		if($topic!="")
			$query .= " AND b.teacherTopicCode='$topic' ";

		$query .= " GROUP BY a.userID ORDER BY childClass, childSection, childName";
		//echo $query;
		$result = mysql_query($query) or die("Error in query!".$query.mysql_error());
		$srno = 0;
		while ($line = mysql_fetch_array($result))
		{
			$studentDetails[$srno][0] = $line['userID'];
			$studentDetails[$srno][1] = $line['childName'];
			$studentDetails[$srno][2] = $line['childClass'].$line['childSection'];
			$studentDetails[$srno][3] = $line['totalq'];
			$studentDetails[$srno][4] = "";
			if($line['totalq']!=0)
				$studentDetails[$srno][4] = round($line['correct']/$line['totalq']*100,2);
			else
				$studentDetails[$srno][4] = "&nbsp;";
			$studentDetails[$srno][5] = $line['nooftopics'];
			$studentDetails[$srno][6] = $line['max_attemptDate'];

			$srno++;
		}
	}

	$tillDate = date('d-m-Y', strtotime(str_replace('-', '-', $tillDate)));
	$fromDate = date('d-m-Y', strtotime(str_replace('-', '-', $fromDate)));
?>

<title>Topicwise Practice Report</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/topicWisePracticeReport.css" rel="stylesheet" type="text/css">
<!-- <script src="libs/jquery-1.9.1.js"></script> -->
<link rel="stylesheet" href="css/jquery-ui.css" />
  <!--<script src="http://code.jquery.com/jquery-1.9.1.js"></script>-->
<!--<script src="libs/jquery-ui.js"></script>-->
  <!--<link rel="stylesheet" href="/resources/demos/style.css" />-->
  <script>
  $(function() {
    $( ".datepicker" ).datepicker({ dateFormat: 'dd-mm-yy' });
  });
  </script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script src="libs/idletimeout.js" type="text/javascript"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
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
		$("#features").css("font-size","1.4em");
		$("#features").css("margin-left","40px");
		$(".arrow-right-blue").css("margin-left","10px");
		$(".rectangle-right-blue").css("display","block");
		$(".arrow-right-blue").css("margin-top","3px");
		$(".rectangle-right-blue").css("margin-top","3px");
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
		print($topic_code_array_string);
		print($topic_desc_array_string);
	?>

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
	        $("#lstSection").html("");
		    if(cls=="")
		    {
		    	OptNew = document.createElement('option');
	            OptNew.text = "All";
	            OptNew.value = "";
            	OptNew.selected = true;
	            obj.options.add(OptNew);
				$(".noSection").css("visibility","visible");
		        document.getElementById('lstSection').style.display = "inline";
		        document.getElementById('lstSection').selectedIndex = 0;
		    }
		    else
		    {
		    	for(var i=0; i<gradeArray.length && gradeArray[i]!=cls; i++);
		       	if(sectionArray[i].length>0)
		       	{
		       		if(sectionArray[i].length != 1)
					{
						OptNew = document.createElement('option');
	    	            OptNew.text = "All";
	    	            OptNew.value = "";
	    	           	OptNew.selected = true;
	    	            obj.options.add(OptNew);
					}
		       		
	    	    	for (var j=0; j<sectionArray[i].length; j++)
	    	       	{
	    	        	OptNew = document.createElement('option');
	    	            OptNew.text = sectionArray[i][j];
	    	            OptNew.value = sectionArray[i][j];
	    	            if(sec==sectionArray[i][j])
	    	            	OptNew.selected = true;
	    	            obj.options.add(OptNew);
	    	        }
					$(".noSection").css("visibility","visible");
	    	        document.getElementById('lstSection').style.display = "inline";
	    	        document.getElementById('lblSection').style.display = "inline";

	    	        if(sectionArray[i].length == 1)
	    	        {
	    	        	$($("#lstSection option")[1]).attr("selected","selected");
	    	        }
		        }
				else
				{
					$(".noSection").css("visibility","hidden");
					
				}
		    }
		}

	}
	function populateTopic(topic)
	{
		var obj = document.getElementById('lstTopic');
		removeAllOptions(obj);
		var childClass = document.getElementById('lstClass').value;
		if (childClass)
		{
			var x = eval('code_'+childClass);
			var y = eval('desc_'+childClass);
			if(x.length == 0)
			{
				$('#lstTopic').attr('disabled',true);
			}
			else
			{
				$('#lstTopic').attr('disabled',false);
			}

			for (j=0; j<x.length; j++)
			{
				var OptNew = document.createElement('option');
				OptNew.text = eval('desc_'+childClass)[j];
				OptNew.value = eval('code_'+childClass)[j];
				if (eval('code_'+childClass)[j]==topic)
				{
					OptNew.selected = true;
				}
				obj.options.add(OptNew);
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

	function showReport(userID)
	{
		document.getElementById('studentID').value = userID;
		document.getElementById('frmRevision').action = "topicrevision_studentwisedetails.php";
		setTryingToUnload();
		document.getElementById('frmRevision').submit();
	}
	function validate()
	{
		if($('#lstTopic').is(':disabled'))
		{
			alert('No student has done practice in any topic for selected class.');
			return false;
		}
	
		var val=document.getElementById('dateFrom').value;
   		
        var splits = val.split("-");
        var dt = new Date(splits[1] + "/" + splits[0] + "/" + splits[2]);
       
       /* //Validation for Dates
        if(dt.getDate()==splits[0] && dt.getMonth()+1==splits[1]
            && dt.getFullYear()==splits[2])
        {
            
        }
        else
        {
            alert("Invalid FROM date.Required yy-mm-dd format. dashes will come up automatically.");
            return false;
        }
       
        
    	var val=document.getElementById('dateTo').value;
   		
        var splits = val.split("-");
        var dt = new Date(splits[1] + "/" + splits[0] + "/" + splits[2]);
       
        //Validation for Dates
        if(dt.getDate()==splits[0] && dt.getMonth()+1==splits[1]
            && dt.getFullYear()==splits[2])
        {
            
        }
        else
        {
            alert("Invalid To date.Required yy-mm-dd format. dashes will come up automatically.");
            return false;
        }
		
		var a = document.getElementById('dateFrom').value;
		var arr = a.split('-');
		var mmd = new Date(parseInt(arr[2]),(parseInt(arr[1]) - 1),parseInt(arr[0])) ;
		//alert(mmd);
		
		var b = document.getElementById('dateTo').value;
		var arr1 = b.split('-');
		var mmd1 = new Date(parseInt(arr1[2]),(parseInt(arr1[1]) - 1),parseInt(arr1[0])) ;
		//alert(mmd1);
		
		
		
		if(mmd > mmd1)
		{
			alert("Your TO date should be greater than FROM date");
			return false;
		}*/
		
		var cls = document.getElementById('lstClass').value;
		if(cls=="")
		{
			alert("Please select a class");
			return false;
		}
		setTryingToUnload();
		return true;
	}


</script>
</head>
<body class="translation" onLoad="load();setSection('<?=$section?>'); populateTopic('<?=$topic?>')" onResize="load()" onmousemove="reset_interval()" onclick="reset_interval()" onkeypress="reset_interval()" onscroll="reset_interval()">
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
					<div id="pageText">TOPICWISE PRACTICE</div>
				</div>
			</div>
			
			<form id="frmRevision" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
			
			<table id="topicDetails">
				<td width="5%"><label for="lstClass">Class</label></td>
		        <td width="15%" style="border-right:1px solid #626161">
		            <select name="cls" id="lstClass"  onchange="setSection(''); populateTopic('');" style="width:65%;">
					<?php
						if(count($classArray) != 1)
							echo '<option value="">Select</option>';
						for ($i=0;$i<count($classArray);$i++)
						{
							echo "<option value='".$classArray[$i]."'";
							if ($cls==$classArray[$i])
							{
								echo " selected";
							}
							echo ">".$classArray[$i]."</option>";
						}
						if(count($classArray) == 1)
						{
					?>
							<script type="text/javascript">
								$("#lstClass").val("<?=$classArray[0]?>");
							</script>
					<?php
						}
					?>
					</select>
		        </td>
				<?php if($hasSections) { ?>
				<td width="6%" class="noSection"><label id="lblSection" for="lstSection" style="margin-left:10px;">Section:</label></td>
		        <td width="15%"  class="noSection">
		            <select name="section" id="lstSection" style="width:65%;">
				</select>
		        </td>
				<?php } ?>
				<td width="8%" style="border-left:1px solid #626161"><label for="lstTopic" id="lblTopic" style="margin-left:10px;">Topic :</label></td>
			
		        <td width="31%">
			            <select name="topic" id="lstTopic" style="width:85%;">
						   <option value=''>All</option>
					    </select>
		        </td>
		        <td width="20%" style="border-left:1px solid #626161;">&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="button" name="generate" id="btnGo" value="Generate" onClick="return validate();"></td>
		    
			</table>
			
			<table id="generateTable" style="display:none;">
				<td width="5%">
					<label for="fromDate">From</label>
		        </td>
				<td width="22%" style="border-right:1px solid #626161"><input type="text" name="fromDate" value="<?=$fromDate?>" /></td>
				<td width="6%">
					<label style="margin-left:20px;" for="tillDate">To</label>
		        </td>
				<td width="22%" style="border-right:1px solid #626161"><input type="text" name="tillDate" value="<?=$tillDate?>"/></td>
		        
			</table>
			<input type="hidden" id="studentID" name="studentID">
			</form>
			
			<?php
			if($cls!="")
			{
			?>
			<div align="center" id="pnlRevisionDetails">
			<?php if(count($studentDetails)>0) { ?>
			<table id="pagingTable">
		        <td width="35%" align="left">CLASS <?=$cls?><?=$section?></td>
				<td>
				<!-- 	From: <?=$fromDate?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;To: <?=$tillDate?> -->
				</td>
			</table>
			<table class="gridtable" border="1" cellpadding="3" cellspacing="0" width="100%" align="center">
					<tr align="center">
						<th class="header">Sr.No.</th>
						<th class="header">Name</th>
						<th class="header">Class</th>
						<th class="header">Total Ques</th>
						<th class="header">% Correct</th>
			<?php if($topic=="") { ?>
						<th class="header">No. of topics revised</th>
			<?php } ?>
						<th class="header">Last Attempted</th>
					</tr>
			<?php	for($i=0; $i<count($studentDetails); $i++)	{	?>
					<tr align="center">
						<td><?=$i + 1 ?></td>
			<?php if($studentDetails[$i][3]==0) { ?>
						<td align="left"><?=$studentDetails[$i][1]?></td>
			<?php }  else { ?>
						<td align="left"><a style="text-decoration:underline" href="javascript:showReport(<?=$studentDetails[$i][0]?>)"><?=$studentDetails[$i][1]?></a></td>
			<?php } ?>
						<td><?=$studentDetails[$i][2]?></td>
						<td><?=$studentDetails[$i][3]?></td>
						<td><?=$studentDetails[$i][4]?></td>
			<?php if($topic=="") { ?>
						<td><?=$studentDetails[$i][5]?></td>
			<?php } ?>
						<td><?=$studentDetails[$i][6]?></td>
					</tr>
			<?php	}	?>
				</table>
			<br/><br/>

			<?php } else echo "<div id='noRecords'><center>No students have attempted the practice module for this topic from selected section!</center></div><br><br>";?>
			</div>
			<?php } ?>

			<div id="impNote"><center>Note: Only topics where at least one student has done practice are shown.</center></div>
		</div>
	</div>

<?php include("footer.php") ?>