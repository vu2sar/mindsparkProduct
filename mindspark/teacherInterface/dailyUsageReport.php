<?php
	include("header.php");
	include("../slave_connectivity.php");
	if(!isset($_SESSION['userID']) || $_SESSION['userID']=="")
	{
		echo "You are not authorised to access this page!";
		exit;
	}
	
	
	
	$userID      = $_SESSION['userID'];
	$category    = $_SESSION['admin'];
	$subcategory = $_SESSION['subcategory'];
	$schoolCode = $_SESSION['schoolCode'];


	$class    = isset($_POST['childClass'])?$_POST['childClass']:"";
	if(strcasecmp($category,"School Admin")!=0)
	{
		echo "You are not authorised to access this page";
		exit;
	}

	$forDate = isset($_POST['forDate'])?$_POST['forDate']:date("d-m-Y");
	$date    = substr($forDate,6,4).substr($forDate,3,2).substr($forDate,0,2);
	$class   = isset($_POST['class'])?$_POST['class']:"1";
	$today   = date("Ymd");

	$classArray = $classWiseDetailsArray = array();
	$classArray = getClassDetails($schoolCode);
	$query = "SELECT childClass, childSection, count(userID) as totalStudents FROM adepts_userDetails
	          WHERE  schoolCode=$schoolCode AND category='STUDENT' AND subcategory='School' AND enabled=1 AND endDate>=curdate() AND subjects like '%".SUBJECTNO."%'";
	if($class!="")
		$query .= " AND childClass='$class'";
	$query.= "GROUP BY childClass, childSection ORDER BY childClass, childSection";
	$result = mysql_query($query) or die(mysql_error());
	$srno = 0;
	while ($line = mysql_fetch_array($result))
	{
		$classWiseDetailsArray[$srno][0] = $line['childClass'];
		$classWiseDetailsArray[$srno][1] = $line['childSection'];
		$classWiseDetailsArray[$srno][2] = $line['totalStudents'];

		$query = "SELECT count(distinct a.userID) FROM adepts_userDetails a, ".TBL_SESSION_STATUS." b
		          WHERE  a.userID=b.userID AND schoolCode=$schoolCode AND category='STUDENT' AND subcategory='School' AND enabled=1 AND endDate>=curdate() AND subjects like '%".SUBJECTNO."%' AND
		                 childClass='".$line['childClass']."' AND startTime_int=$date";
		if($line['childSection']!="")
			$query .= " AND childSection='".$line['childSection']."'";
		$loggedin_result = mysql_query($query);
		$loggedin_line   = mysql_fetch_array($loggedin_result);
		$classWiseDetailsArray[$srno][3] = $loggedin_line[0];
		$srno++;
	}
?>

<title>Daily Usage Summary</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/timedTestReport.css?ver=1" rel="stylesheet" type="text/css">
<!-- <script src="libs/jquery-1.9.1.js"></script> -->
<link rel="stylesheet" href="css/jquery-ui.css" />
<script src="libs/jquery-ui.js"></script>
<script>
$(function() {
	//$( ".datepicker" ).datepicker({ dateFormat: 'dd-mm-yy' });
	 $("#txtForDate").datepicker({
	      defaultDate: "today",	      
	      dateFormat: 'dd-mm-yy',
	      numberOfMonths: 1,
	      maxDate	: 'today',	      
	      selectedDate: 'today',	      
	    });
    $("#txtForDate").change(function (){    	
    	var fromDate = $("#txtForDate").datepicker("getDate");    	    	 
    	var currentDate = new Date();    	
       	if(new Date(fromDate) > currentDate){
    		alert("Future dates are not allowed !!");
    		currentDate = $.datepicker.formatDate('dd-mm-yy', new Date(currentDate));
    		$("#txtForDate").val(currentDate);
    	}
    });
 
});
</script>
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
		$("#features").css("font-size","1.em");
		$("#features").css("margin-left","40px");
		$(".arrow-right-blue").css("margin-left","10px");
		$(".rectangle-right-blue").css("display","block");
		$(".arrow-right-blue").css("margin-top","3px");
		$(".rectangle-right-blue").css("margin-top","3px");
	}
	function openCalender(id){
		var id=id;
		if(id=="from"){
			$("#txtForDate").focus();
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
<script language="javascript">
	
	function validate()
	{
		/*if (document.getElementById('lstClass').value=="")
		{
			alert('Please select class');
			document.getElementById('lstClass').focus();
			return false;
		}*/
		if (document.getElementById('txtForDate').value=="")
		{
			alert('Please select a date');
			document.getElementById('txtForDate').focus();
			return false;
		}
		setTryingToUnload();
		return true;
	}

</script>
</head>
<body class="translation" onLoad="load();" onResize="load()">
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
					<div id="pageText">DAILY USAGE REPORT</div>
				</div>
				<div id="classTopic">
				</div>
			</div>
			<form name="frmTeacherDetails" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<table id="topicDetails">
				<td width="5%" class=""><label for="lstClass">Class</label></td>
		        <td width="25%" style="border-right:1px solid #626161" class="noClass">
		            <select name="class" id="lstClass" style="width:65%">
						<option value="">All</option>
						<?php
							for($clsCounter=0; $clsCounter<count($classArray); $clsCounter++)
							{
								echo "<option value='".$classArray[$clsCounter]."'";
								if($classArray[$clsCounter]==$class)
									echo " selected";
								echo ">".$classArray[$clsCounter]."</option>";
							}
						?>
					</select>
		        </td>
				<td width="10%" class="noSection1"><label for="timedtest" style="margin-left:10px;">Select Date :</label></td>
		        <td width="25%" class="noSection1">
					<input type="text" name="forDate" value="<?=$forDate?>" class="datepicker floatLeft" id="txtForDate" value="" autocomplete="off" onkeydown="return DateFormat(this, event.keyCode)" maxlength="10" size="20"/><div class="calenderImage linkPointer" id="from" onClick="openCalender(id)"></div>
		        </td>
			</table>
			
			<table id="generateTable">
		        <td width="24%"><input type="submit" class="button" name="submit" id="btnSubmit" value="Submit" onClick="return validate();"></td>
			</table>
			</form>
			<?php
	if($date>$today)
	{
		echo "<div id='noRecords'><center>Please enter a valid date!</center></div>";
		exit;
	}
?>
			<div id="pnlSummary" align="center">
	
		<table class="gridtable" cellspacing="0" cellpadding="3" border="1" align="center" width="100%">
    		<tr>
					<th class="header">Sr.No.</th>
					<th class="header">Class</th>
					<th class="header">Section</th>
					<th class="header">Students logged in / Total class strength</th>
				</tr>
				<?php
		for($i=0; $i<count($classWiseDetailsArray); $i++)
		{
	?>
	<tr>
		<td><?=($i+1)?></td>
		<td><?=$classWiseDetailsArray[$i][0]?></td>
		<td><?=$classWiseDetailsArray[$i][1]?></td>
		<td align="center"><?=$classWiseDetailsArray[$i][3]." / ".$classWiseDetailsArray[$i][2]?></td>

	</tr>
	<?php
		}
	?>
		</table>
	</div>
			
		</div>
	</div>

<?php include("footer.php") ?>
<?php
function getClassDetails($schoolCode)
{
	$classArray = array();
	$query = "SELECT distinct childClass FROM adepts_userDetails
	          WHERE  schoolCode=$schoolCode AND category='STUDENT' AND subcategory='School' AND enabled=1 AND endDate>=curdate() AND subjects like '%".SUBJECTNO."%'";
	$result = mysql_query($query) or die(mysql_error());
	while ($line = mysql_fetch_array($result))
	{
		array_push($classArray, $line[0]);
	}
	sort($classArray,SORT_NUMERIC);
	return $classArray;
}
?>