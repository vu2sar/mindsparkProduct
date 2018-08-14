<?php
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);

	include("header.php");
	include("../slave_connectivity.php");
	include("classes/clsTeacher.php");
	include("classes/testTeacherIDs.php");
	include("../userInterface/functions/functions.php");

	if(!isset($_SESSION['userID']))
	{
		header("Location:logout.php");
		exit();
	}

	$userName   = $_SESSION['username'];
	$userid 	= $_SESSION['userID'];
	$category   = $_SESSION['admin'];
	$schoolCode = $_SESSION['schoolCode'];
	$subcategory = $_SESSION['subcategory'];

	/*if(strcasecmp($category,"School Admin")!=0)
	{
	    echo "You are not authorised to access this page!";
	    exit;
	}*/




	if (isset($_REQUEST['Search']))
	{
		$startDate	=	$_POST['startDate'];
		$tillDate	=	$_POST['tillDate'];
	}
	else
	{
		$tillDate	=	date("d-m-Y");
		$startDate	=	date("d-m-Y",strtotime("-7 days"));
	}
	$tmpTeacherIDs = "'".implode("','",$testIDArray)."'";
	$teacherDetailsArray = getTeacherDetails($schoolCode,$startDate,$tillDate,$tmpTeacherIDs);


?>

<title>Teacher Usage Details</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css"> 
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/teacherLoginDetails.css" rel="stylesheet" type="text/css">
<!-- <script src="libs/jquery-1.9.1.js"></script> -->
<link rel="stylesheet" href="css/jquery-ui.css" />
  <!--<script src="http://code.jquery.com/jquery-1.9.1.js"></script>-->
  <!--<script src="libs/jquery-ui.js"></script>-->
 <!--<link rel="stylesheet" href="/resources/demos/style.css" />-->
  <script>
  $(function() {
    //$( ".datepicker" ).datepicker({ dateFormat: 'dd-mm-yy' });
    $("#startDate").datepicker({
	      defaultDate: "today",	      
	      dateFormat: 'dd-mm-yy',
	      numberOfMonths: 1,
	      maxDate: 'today',	      
	      selectedDate: 'today',
	      onSelect: function( selectedDate ) {
	        $( "#tillDate" ).datepicker( "option", "minDate", selectedDate );
	      }
	    });

	    $( "#tillDate" ).datepicker({
	      defaultDate: "today",	      
	      dateFormat: 'dd-mm-yy',
	      numberOfMonths: 1,
	      maxDate: 'today',	      
	      selectedDate: 'today',
	      onSelect: function( selectedDate ) {
	        $( "#startDate" ).datepicker( "option", "maxDate", selectedDate );
	      }
	    });
	    $("#startDate").change(function (){
	    	var today_in_server = '<?=date("d-m-Y",strtotime($startDate));?>';
	    	var today_in_server_to = '<?=date("d-m-Y",strtotime($tillDate));?>';    
	    	var fromDate = $("#startDate").datepicker("getDate");  
	    	var toDate = $("#tillDate").datepicker("getDate");  	
	    	if(fromDate.length !=0)
	    	{
		    	var today = new Date();
		    	    	
		    	if(fromDate>today){
		    		alert("Future dates are not allowed !!");
		    		$("#startDate").val(today_in_server);
		    		$("#startDate").focus();
		    	}
		    	else if(toDate.length != 0)
		    	{
			    	if(new  Date(fromDate) > new Date(toDate))
		    		{
		    			alert("From date should come before To date");  	    			      				
		    			$("#startDate").val(today_in_server);
		    			$("#tillDate").val(today_in_server_to);	    			
			    		$("#startDate").focus();
		    		}	
		    	}	    	
	    	}	    
	    });
	    $("#tillDate").change(function (){	
	    	var today_in_server = '<?=date("d-m-Y",strtotime($tillDate));?>'; 
	    	var today_in_server_from = '<?=date("d-m-Y",strtotime($startDate));?>';    	
	    	var toDate = $("#tillDate").datepicker("getDate");
	    	var fromDate = $("#startDate").datepicker("getDate");
	    	if(toDate.length !=0)
	    	{	    	
	    		var today = new Date();	    
		    	if(toDate>today)
		    	{
		    		alert("Future dates are not allowed !!");
		    		$("#tillDate").val(today_in_server);
		    		$("#tillDate").focus();
		    	}
	    		else if(new  Date(toDate) < new Date(fromDate))
	    		{
	    			alert("To date should come after From date");  	    			      				
	    			$("#tillDate").val(today_in_server);
	    			$("#startDate").val(today_in_server_from);	    			
		    		$("#tillDate").focus();
	    		}  	   
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
		/*$("#container").css("height",containerHeight+"px");*/
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
			$("#startDate").focus();
		}
		else{
			$("#tillDate").focus();
		}
	}
</script>
<script>
	function init()
	{
		document.cookie = 'SHTS=;';
		document.cookie = 'SHTSP=;';
		document.cookie = 'SHTParams=;';

	}
	function logoff()
	{
		setTryingToUnload();
		window.location="logout.php";
	}
	function trim(str) {
		// Strip leading and trailing white-space
		return str.replace(/^\s*|\s*$/g, "");
	}

/*	$(document).ready(function(){
		$("td table tr:odd").css('background-color','#FFFFFF');
	}*/

	function validateDate()
	{
		
		var val=document.getElementById('startDate').value;
		
        var splits = val.split("-");
        var dt = new Date(splits[1] + "/" + splits[0] + "/" + splits[2]);
       
        //Validation for Dates
        if(dt.getDate()==splits[0] && dt.getMonth()+1==splits[1]
            && dt.getFullYear()==splits[2])
        {
            
        }
        else
        {
            alert("Invalid FROM date.Required dd/mm/yy format. Seperators will come up automatically.");
            return false;
        }
       
        
    	var val=document.getElementById('tillDate').value;
   		
        var splits = val.split("-");
        var dt = new Date(splits[1] + "/" + splits[0] + "/" + splits[2]);
       
        //Validation for Dates
        if(dt.getDate()==splits[0] && dt.getMonth()+1==splits[1]
            && dt.getFullYear()==splits[2])
        {
            
        }
        else
        {
            alert("Invalid To date.Required dd/mm/yy format. Seperators will come up automatically.");
            return false;
        }
		
		var a = document.getElementById('startDate').value;
		var arr = a.split('-');
		var mmd = new Date(parseInt(arr[2]),(parseInt(arr[1]) - 1),parseInt(arr[0])) ;
		/*alert(mmd);*/
		
		var b = document.getElementById('tillDate').value;
		var arr1 = b.split('-');
		var mmd1 = new Date(parseInt(arr1[2]),(parseInt(arr1[1]) - 1),parseInt(arr1[0])) ;
		/*alert(mmd1);*/
		
		
		
		if(mmd > mmd1)
		{
			alert("Your TO date should greater than FROM date");
			return false;
		}
		setTryingToUnload();
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
</head>
<body class="translation" onLoad="init();load()" onResize="load()">
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
				<span>Teacher Usage Details</span>
			</div>
			<div id="containerBody">
			<form method="POST" action="" id="frmMain" name="frmMain">
			<div id="dateChoose">
					<div id="fromDate">
					From 
					<input type="text" name="startDate" class="datepicker" id="startDate" value="<?=$startDate?>" onKeyDown="return DateFormat(this, event.keyCode)" maxlength="10">
					</div>
					<div class="calenderImage linkPointer" id="from" onClick="openCalender(id)"></div>
					<div id="toDate">
					To
					<input type="text" name="tillDate" class="datepicker" id="tillDate"  value="<?=$tillDate?>" onKeyDown="return DateFormat(this, event.keyCode)" maxlength="10">
					</div>
					<div class="calenderImage linkPointer" id="to" onClick="openCalender(id)"></div>
					<div>
					<input type="submit" name="Search" value="Search" onClick="return validateDate();" class="button">
					</div>
				</div>		
			
			</form>
			<hr></hr>
			</div>
			
			<div>
			<table  cellpadding="3" cellspacing="0" class="tblContent" border="0" width="100%">
	<tr>
		<th align="left" class="header" width="5%">Sr.No.</th>
		<th align="left" class="header">Name</th>
		<th align="left" nowrap class="header">Classes Mapped</th>
		<th align="left" nowrap class="header">No. of days</th>
		<th align="left" nowrap class="header">Total login time<br/>(hh:mm:ss)</th>
	</tr>
<?php $i=1;
	foreach ($teacherDetailsArray as $userID=>$details)
	{	?>
		<tr>
			<td align="left"><?=$i?></td>
			<td align="left"><?=$details['name']?></td>
			<td align="left"><?=$details['classMapped']?></td>
			<td align="left"><?=$details['totalDays']?></td>
			<td align="left"><?=$details['timeSpent']?></td>
		</tr><?php	$i++;
	}

?>
</table>
			</div>
		</div>
		
		
	</div>

<?php include("footer.php") ?>
<?php

function getTeacherDetails($schoolCode,$startDate,$tillDate,$tmpTeacherIDs)
{
    $arrTeacher = array();
    $startDate	=	changeDateFormat($startDate);
	$tillDate	=	changeDateFormat($tillDate);
    $sq = "SELECT userID,category,childName FROM adepts_userDetails WHERE schoolCode=$schoolCode AND category IN ('TEACHER','School Admin') AND username NOT in ($tmpTeacherIDs) and enabled=1 and endDate>=curdate() ORDER BY category, childName";
    $rs = mysql_query($sq);
    while ($rw = mysql_fetch_array($rs))
    {
    	$userID	=	$rw['userID'];

        $arrTeacher[$userID]['name']		=	$rw['childName'];
        $arrTeacher[$userID]['classMapped']	=	getClassesMapped($userID);
       	$arrTeacher[$userID]['totalDays']	=	getTotalLoginDays($userID,$startDate,$tillDate);
        $arrTeacher[$userID]['timeSpent']	=	getTimeSpentCommon($userID,$startDate,$tillDate);
    }
    return $arrTeacher;
}

function getClassesMapped($userID)
{
    $classesMapped = "";
    $sq  = "SELECT class,section FROM adepts_teacherClassMapping WHERE userID='$userID' ORDER BY class,section";
    $rs = mysql_query($sq);
    while ($rw=mysql_fetch_array($rs))
        $classesMapped .= $rw[0].$rw[1].", ";
    $classesMapped = substr($classesMapped,0,-2);

    return $classesMapped;
}

function getTotalLoginDays($userID,$startDate,$tillDate)
{
	$sq  =	"SELECT COUNT(DISTINCT cast(startTime as DATE)) AS daysLoggedIn FROM adepts_sessionStatus
			 WHERE userID=$userID AND startTime BETWEEN '$startDate' AND '$tillDate 23:59:59'";
    $rs = mysql_query($sq);
    $rw = mysql_fetch_array($rs);
    $days	=	$rw['daysLoggedIn'];
    return $days;
}

/*function getTimeSpent($userID,$startDate,$tillDate)
{
	$query = "SELECT DISTINCT sessionID, startTime, endTime FROM adepts_sessionStatus WHERE  userID='$userID' AND startTime BETWEEN '$startDate' AND '$tillDate 23:59:59'";

	$time_result = mysql_query($query) or die(mysql_error());
    $timeSpent = 0;
    while ($time_line = mysql_fetch_array($time_result))
    {
    	$startTime = convertToTimeCommon($time_line[1]);
		if($time_line[2]!="0000-00-00 00:00:00")
		{
			$endTime = convertToTimeCommon($time_line[2]);
			$timeSpent = $timeSpent + ($endTime - $startTime);    //in secs
		}
	}

    $hours = str_pad(intval($timeSpent/3600),2,"0",STR_PAD_LEFT);    //converting secs to hours.
    $timeSpent = $timeSpent%3600;
    $mins  = str_pad(intval($timeSpent/60),2,"0", STR_PAD_LEFT);
    $timeSpent = $timeSpent%60;
    $secs  = str_pad($timeSpent,2,"0",STR_PAD_LEFT);

    return $hours.":".$mins.":".$secs;
}
*/

function changeDateFormat($date)
{
	$newDate	=	explode("-", $date);

	return $newDate[2]."-".$newDate[1]."-".$newDate[0];
}
?>