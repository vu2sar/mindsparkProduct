<?php

	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
	@include("header.php");       

	include("../userInterface/classes/clsTopicProgress.php");
	
	include("functions/functions.php");
	
	include("functions/dashboardFunctions.php");  

	include("functions/schoolwiseUsageAjax.php");	

	//include("../slave_connectivity.php");
	//echo $_SESSION['userID'];
	//$_SESSION['userID']='102014';
	if(!isset($_SESSION['userID']))
	{
		header("Location:../logout.php");
		exit;
	}
	// echo date("Y-m-d", strtotime("-7 days"))."~~~".date("Y-m-d"); 
?>
<title>Individual Student Report</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/chart.css" rel="stylesheet" type="text/css">

<!-- <link href="css/ion.tabs.css" rel="stylesheet" type="text/css">
 --><link rel="stylesheet" href="css/jquery-ui.css" />
<link href="css/ion.tabs.skinBordered.css" rel="stylesheet" type="text/css">
<link href="css/studentWiseReport.css?ver=1" rel="stylesheet" type="text/css">
<script type="text/javascript" src="libs/jquery.js"></script>
<script src="libs/jquery-ui-1.11.2.js"></script>
<!-- <script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
 -->
 <!-- <script type="text/javascript" src="libs/migrate.js"></script> -->
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>	
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script type="text/javascript" src="libs/dashboardCommon.js"></script>
<script type="text/javascript" src="libs/tiDashboard.js"></script>
<script language="javascript" type="text/javascript" src="libs/suggest1.js"></script>
<script language="javascript" type="text/javascript" src="libs/suggest2.js"></script>
<script type="text/javascript" src="libs/jquery.flot.js"></script>
<script type="text/javascript" src="libs/jquery.flot.pie.js"></script>
<script type="text/javascript" src="libs/jquery.flot.categories.js"></script>
<script type="text/javascript" src="libs/jquery.flot.tooltip.js"></script>
<script type="text/javascript" src="libs/jquery.flot.axislabels.js"></script>

<?php
	
	$userID     = $_SESSION['userID'];
	$schoolCode = isset($_SESSION['schoolCode'])?$_SESSION['schoolCode']:"";
	$user   = new User($userID);
    $iscustom=true; // To check which date parameter has to be selected
	if(strcasecmp($user->category,"Teacher")==0 || strcasecmp($user->category,"School Admin")==0)	{
		$query = "SELECT schoolname FROM educatio_educat.schools WHERE schoolno=".$schoolCode;
		$r = mysql_query($query);
		$l = mysql_fetch_array($r);
		$schoolName = $l[0];
	}

	/*if(isset($_POST) && (!empty($_POST)))
	{
	$class	=	isset($_POST['cls'])?$_POST['cls']:$_GET['cls'];
	$section  = isset($_POST['section'])?$_POST['section']:$_GET['section'];
	$studentID	= isset($_POST['studentID'])?$_POST['studentID']:$_GET['studentID'];
		
	}*/

	$class	=	isset($_REQUEST['cls'])?$_REQUEST['cls']:"";
	$section	=	isset($_REQUEST['section'])?$_REQUEST['section']:"";
	$studentID	=	isset($_REQUEST['studentID'])?$_REQUEST['studentID']:"";

	$teacherTopics	=	getTTs($class, $schoolCode, $section);
	
	$oneDay = 24*60*60;
	$lastWeek = date("d-m-Y",strtotime("-1 day"));
	
	$allstudentArray	=	array();
	$allstudentArray=getStudentDetailsBySection($schoolCode,$class,$section);
	
	$lowerLevelStudent = array();  
	$classArray = $sectionArray = array();
	$hasSections = false;      
	
	$fromDate = isset($_REQUEST['fromDate'])?$_REQUEST['fromDate']:date("Y-m-d");
	$tillDate = isset($_REQUEST['tillDate'])?$_REQUEST['tillDate']:date("Y-m-d");
	
	$selectedDate=$fromDate."~".$tillDate;
	$topicsAttemptlistarr =array();
	$total_topic_attempted_list=array();
	if(!empty($studentID)){    
	$childUserName =studentName($studentID);
	$childfullName=studentfullName($studentID);
	
	$topicsAttemptlistarr=getTotalTopicsAttempted($studentID,$class,date("Y-m-d",strtotime($fromDate)),date("Y-m-d",strtotime($tillDate)));
	
	$total_no_of_TopicsAttempt =isset($topicsAttemptlistarr['total'])?$topicsAttemptlistarr['total']:0;
	$total_topic_attempted_list=isset($topicsAttemptlistarr['tt'])?explode(',',$topicsAttemptlistarr['tt']):'';
	
	$totalcountSummaryDetail=getcountofTopicsAndQues($studentID, $class, $section, $fromDate, $tillDate);
	$totalLearningUnits=$totalcountSummaryDetail['totalclusters'];
	$totaltopicsAttempted=$totalcountSummaryDetail['totaltopics'];
	$totalNoOfQuesAttempted=$totalcountSummaryDetail['totalques'];

	
	$topicDetailslist=array();
	$topicDetailslist=getTopicProgresFromTT($studentID,$total_topic_attempted_list);
	
	$topic_progress_master=array();
	$topic_progress_master=getTopicProgresFromTT($studentID,$total_topic_attempted_list);
	
	$overallUsage=getUsageAndAccuracyForStudent($studentID,$schoolCode, $class, $section, date("Y-m-d",strtotime($fromDate)), date("Y-m-d",strtotime($tillDate)));

	$studentTimeSpent=isset($overallUsage['timeSpent'])?$overallUsage['timeSpent']:0;
	$studentAvgTimeSpent=isset($overallUsage['avgtimeSpent'])?$overallUsage['avgtimeSpent']:0;
	$studentOverallusage=isset($overallUsage['usage'])?$overallUsage['usage']:0;
	$studentAccuracy=isset($overallUsage['accuracy'])?$overallUsage['accuracy']:0;
	$studentAccuracyColor=isset($overallUsage['accuracyusage'])?$overallUsage['accuracyusage']:0;

	$timeSpentInHomeAndSchool=getTimeSpentHomeAndSchool($studentID,$class,$section,date("Y-m-d",strtotime($fromDate)),date("Y-m-d",strtotime($tillDate)));
	$timeSpentAcrossDaysDetails=timeSpentperDayBarChart($studentID,date("Y-m-d",strtotime($fromDate)), date("Y-m-d",strtotime($tillDate)));
	$timeSpentAcrossDays=json_encode($timeSpentAcrossDaysDetails['tableData']);
	$timeSpentAcrossDaysTag=$timeSpentAcrossDaysDetails['tag'];
	$studentProgress	=	getTeacherTopicProgress2($teacherTopics,$class,$studentID); 

	uasort($studentProgress, function ($i, $j) {                
            $a = $i['progress'];
            $b = $j['progress'];
            if ($a == $b) return 0;
            elseif ($a > $b) return 1;
            else return -1;
        });
	
	foreach($teacherTopics as $teacherTopicCode=>$teacherTopicDesc)        
	{
		$flowstr = $studentProgress[$teacherTopicCode]['flow'];
		if($flowstr=="")
			$flowstr	=	"MS";
		$flow = str_replace(" ","_",$flowstr);
		${"objTopicProgress[".$teacherTopicCode."]".$flowStr} = new topicProgress($teacherTopicCode, $class, $flow, SUBJECTNO);
		$clusterArray[$teacherTopicCode][$flow]	=	${"objTopicProgress[".$teacherTopicCode."]".$flowStr}->clusterArray;
		$sdlArray[$teacherTopicCode][$flow]	=	${"objTopicProgress[".$teacherTopicCode."]".$flowStr}->sdlArray;
		
		
		$ttObj[$teacherTopicCode] = new teacherTopic($teacherTopicCode,$class,$flowstr);
		$lowerLevelClusters[$teacherTopicCode]	=	$ttObj[$teacherTopicCode]->getLowerLevelClusters();

		if(count($lowerLevelClusters)!=0)
		{
			//students in low level
			$sq	=	"SELECT DISTINCT a.userID FROM ".TBL_CURRENT_STATUS." AS a, ".TBL_TOPIC_STATUS." AS b 
						 WHERE b.teacherTopicCode='$teacherTopicCode' AND b.ttAttemptNo=1 AND b.userID=$studentID AND 
						 a.ttAttemptID=b.ttAttemptID AND flow='$flowstr' AND clusterCode in ('".implode("','",$lowerLevelClusters[$teacherTopicCode])."')";
			$rs =	mysql_query($sq) or die(mysql_error());

			while($rw=mysql_fetch_array($rs))
			{
				$lowerLevelStudent[$teacherTopicCode][]	=	$rw[0];
			}
		}
	}

	} // end of studentID if bracket

	$query  = "SELECT childName, startDate, endDate, category, subcategory FROM adepts_userDetails WHERE userID=".$userID;
	$result = mysql_query($query) or die(mysql_error());
	$line   = mysql_fetch_array($result);
	$userName 	 =  $line[0];
	$startDate   =  $line[1];
	$endDate     =  $line[2];
	$category    =  $line[3];
	$subcategory =  $line[4];
	
	//echo $childName;
	if(strcasecmp($category,"School Admin")!=0 && strcasecmp($category,"Teacher")!=0 && strcasecmp($category,"Home Center Admin")!=0)
	{
		echo "You are not authorised to access this page!";
		exit;
	}

	if(strcasecmp($category,"School Admin")==0)
	{
		$query  = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
		           FROM     adepts_userDetails
		           WHERE    schoolCode=$schoolCode AND category='STUDENT' AND subcategory='School' AND enabled=1 AND endDate>=curdate() AND subjects like '%".SUBJECTNO."%'
		           GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
	}
	elseif (strcasecmp($category,"Teacher")==0)
	{
		$query = "SELECT   class, group_concat(distinct section ORDER BY section)
				  FROM     adepts_teacherClassMapping
				  WHERE    userID = $userID AND subjectno=".SUBJECTNO."
				  GROUP BY class ORDER BY class, section";
	}
	elseif (strcasecmp($category,"Home Center Admin")==0)
	{
		$query  = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
		           FROM     adepts_userDetails
		           WHERE    category='STUDENT' AND subcategory='Home Center' AND schoolCode=$schoolCode AND endDate>=curdate() AND enabled=1 AND  subjects like '%".SUBJECTNO."%'
		           GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
	}
	
	
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

function getDateRangeValue($startDate, $endDate) {
	return $startDate . "~" . $endDate;
}
function getDateFormated($startDate,$endDate)
{
	return date("d/m/Y",strtotime($startDate)).'-'.date("d/m/Y",strtotime($endDate));
}
?>

<script>
var userArray= new Array(); // GLobal Declaration of userArray (Listing of students username based on class and section selected)
var respUserId= new Array();    
    $(document).ready(function() {
         $("#lstClass,#lstSection").change(function(){
         	 var childClass=$("#lstClass").val();
    		 var childSection=$("#lstSection").val();
    		 if(typeof(childSection)=="undefined")
   			 {
    		   childSection='';
    		 }
         	 $.ajax({
	         	 url: "myStudentReport.php", 
	         	 type: "POST",
	         	 data: {childclass:childClass,section:childSection,schoolCode:<?=$schoolCode?>},
	         	 success: function(data){
		         	 $("#nametag").val("");
		         	 userArray.length = 0;
		         	 respUserId.length = 0;
		         	 var arr=$.parseJSON(data);
		         	 for (var i=0, len=arr.length; i < len; i++) {  
		         		
		      			userArray.push(arr[i]["username"]);
		      			respUserId.push(arr[i]["userID"]);
		    		 }	
		         	 suggestUserList(userArray);
	            
	             }});
           
       	});
        
		// $("#fromDate").on("focus", function() {
		// 	$("#toDate").val("");			
		// });		
	    $("#fromDate").datepicker({
	      defaultDate: "today",
	      changeMonth: true,
	      dateFormat: 'dd-mm-yy',
	      numberOfMonths: 1,
	      maxDate: 'today',
	      minDate: "-90d",	      
	      onSelect: function( selectedDate ) {
	        $( "#toDate" ).datepicker( "option", "minDate", selectedDate );
	      }
	    });

	    $( "#toDate" ).datepicker({
	      defaultDate: "today",
	      changeMonth: true,
	      dateFormat: 'dd-mm-yy',
	      numberOfMonths: 1,
	      maxDate: 'today',
	      minDate: "-90d",	      
	      onSelect: function( selectedDate ) {
	        $( "#fromDate" ).datepicker( "option", "maxDate", selectedDate );
	      }
	    });
	    $("#fromDate").change(function (){
	    	var today_in_server = '<?=date("d-m-Y",strtotime($fromDate))?>';
	    	var today_in_server_to = '<?=date("d-m-Y",strtotime($tillDate))?>'; 
	    	var fromDate = $("#fromDate").datepicker("getDate"); 
	    	var toDate = $("#toDate").datepicker("getDate");   	
	    	if(fromDate.length !=0){
	    	var today = new Date();
	    	var dateLimit = new Date();
	    	dateLimit.setDate((dateLimit.getDate())-90);	    	
	    	if(fromDate>today){
	    		alert("Future dates are not allowed !!");
	    		$("#fromDate").val('');
	    		$("#fromDate").focus();
	    	}
	    	
	    	else if(fromDate < new Date(dateLimit.setHours(0, 0, 0, 0, 0))){
	    		alert("Dates less than 90 days are not allowed!!");
	    		$("#fromDate").val(today_in_server);
	    		$("#toDate").val(today_in_server_to);
	    		$("#fromDate").focus();
	    	}  
	    	else if(toDate.length !=0)
	    	{
		    	if(new  Date(fromDate) > new Date(toDate))
	    		{
	    			alert("From date should come before To date");  	    			      				
	    			$("#fromDate").val(today_in_server);
	    			$("#toDate").val(today_in_server_to);	    			
		    		$("#fromDate").focus();
	    		}
	    	}
	    }	    
	    });
	    $("#toDate").change(function (){

	    	var today_in_server = '<?=date("d-m-Y",strtotime($tillDate))?>';
	    	var today_in_server_from = '<?=date("d-m-Y",strtotime($fromDate))?>';   	
	    	var toDate = $("#toDate").datepicker("getDate");
	    	var fromDate = $("#fromDate").datepicker("getDate");
	    	var today = new Date();
	    	var dateLimit = new Date();
	    	dateLimit.setDate((dateLimit.getDate())-90);	    	
	    	if(toDate.length !=0){
		    	if(toDate>today){
		    		alert("Future dates are not allowed !!");
		    		$("#toDate").val(today_in_server);
		    		$("#toDate").focus();
		    	}
		    	
		    	else if(toDate < new Date(dateLimit.setHours(0, 0, 0, 0, 0))){
		    		alert("Dates less than 90 days are not allowed!!");
		    		$("#toDate").val(today_in_server);
		    		$("#fromDate").val(today_in_server_from);
		    		$("#toDate").focus();
		    	} 
		    	else if(fromDate.length !=0)
		    	{
		    	 if(new  Date(toDate) < new Date(fromDate))
		    		{
		    			alert("To date should come after from date");  	    			      				
		    			$("#toDate").val(today_in_server);
		    			$("#fromDate").val(today_in_server_from);
			    		$("#toDate").focus();
		    		}  	  
		    	}
		    }
	    });
	    
		$("#dateRange").on("click change", function() { 
			if(this.selectedIndex == 4) {
				var dialog = $( "#dialog-form" ).dialog({
				  autoOpen: false,
				  height: 300,
				  width: 540,
				  modal: true,
				  dialogClass: "no-close",
				  buttons: {
				    "OK": function() {
				    	var fD = formatDateForSubmit($("#fromDate").val()); 
				    	var	tD = formatDateForSubmit($("#toDate").val());
				    	if(fD == "" || tD == "") {
				    		alert("Select dates");
				    		return false;
				    	}
				    	$("#dateRange")[0].options[4].value = fD+"~"+tD;
				    	$("#dateRange")[0].options[4].innerHTML = formatDate(fD)+" - "+formatDate(tD) + " (Change)";
				    	//$("#generateReportButton").click();
				      dialog.dialog("close");
				    },
				    Cancel: function() {
				      $("#dateRange")[0].selectedIndex = 0;
				      dialog.dialog( "close" );
				    }
				  },
				  close: function() {
				      dialog.dialog( "close" );
				  }
				});

				dialog.dialog( "open" );
				//idhi
			}
		});

	
	<?php 
	if($studentID!="")
	{ ?>
	
    drawpiechart('<?=$timeSpentInHomeAndSchool?>','p','1');
    drawBarChart('<?=$timeSpentAcrossDays?>','vert','<?=$timeSpentAcrossDaysTag?>');
    //configureDropDownLists(document.getElementById('leftsel'),document.getElementById('resultleftsel'));
	//configureDropDownLists(document.getElementById('rightsel'),document.getElementById('rightsel2'));

    <? } ?>
 });



	var langType = '<?=$language;?>';
	function load(){
		var fixedSideBarHeight = window.innerHeight;
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#fixedSideBar").css("height",fixedSideBarHeight+"px");
		$("#sideBar").css("height",sideBarHeight+"px");
		$("#students").css("font-size","1.4em");
		$("#students").css("margin-left","40px");
		$(".arrow-right-yellow").css("margin-left","10px");
		$(".rectangle-right-yellow").css("display","block");
		$(".arrow-right-yellow").css("margin-top","3px");
		$(".rectangle-right-yellow").css("margin-top","3px");
		$(".datepicker" ).datepicker({dateFormat:'dd-mm-yy'});
		suggestUserList(userArray);
	
	    $('ul.tabs li').click(function(){
		var tab_id = $(this).attr('data-tab');
		if(tab_id=='tab-2')
			$(".rangedate").hide();
		else
			$(".rangedate").show();

		$('ul.tabs li').removeClass('current');
		$('.tab-content').removeClass('current');

		$(this).addClass('current');
		$("#"+tab_id).addClass('current');
	    });
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

	function backBtn(){
		setTryingToUnload();
		window.history.back();
	}

	function redrawchart(elem,elem2)
	{
		drawProgressBar("topicProgressScale",20,40,60,15); // to draw a bar for explanation in topic progress section
		var selectoption2=elem2.value;
		var selectoption1=elem.value;
	    var childClass=$("#lstClass").val();
        var childSection=$("#lstSection").val();
        var seldaterange=$("#datevalue").val();
        var note_to_show=1;
        var note_to_hide=0;
        var studentID=<?php if ($studentID && $studentID!="") echo $studentID; else echo '""'; ?>;
        if(studentID==="") return;
        if(elem.value=='timeSpent') 
        {
        	 $("#img_ajax").show();
			switch (elem2.value)
			{
			case 'Home and School':
				
				 $.ajax({
	         	 url: "ResponseChartAjax.php", 
	         	 type: "POST",
	         	 data: {studentID:studentID,childclass:childClass,section:childSection,dateRange:seldaterange,mode:'timeSpentHomeSchool'},
	         	 success: function(data){ $("#img_ajax").hide();  drawpiechart(data,'p',note_to_show);}
	         	});
			break;

			case 'Across Topics':
			 $.ajax({
	         	 url: "ResponseChartAjax.php", 
	         	 type: "POST",
	         	 data: {studentID:studentID,childclass:childClass,dateRange:seldaterange,mode:'timeSpentAcrossTopicsAjax'},
	         	 success: function(data){ $("#img_ajax").hide();  drawpiechart(data,'p',note_to_hide);}
	         	});
			break;
			
			case 'Activities and questions':
			 $.ajax({
	         	 url: "ResponseChartAjax.php", 
	         	 type: "POST",
	         	 data: {studentID:studentID,childclass:childClass,dateRange:seldaterange,mode:'timeSpentActivitiesQuestAjax'},
	         	 success: function(data){ $("#img_ajax").hide(); drawpiechart(data,'p',note_to_hide);}
	         	});
			break;
		}

	}
		if(elem.value=='timeSpentbar')
		{ 
			$("#img_ajax2").show();// loader image
		    switch (elem2.value)
			{	
				case 'Duration':
			
				 $.ajax({
		         	 url: "ResponseChartAjax.php", 
		         	 type: "POST",
		         	 data: {studentID:studentID,childclass:childClass,dateRange:seldaterange,mode:'timeSpentBarChartAjax'},
		         	 success: function(data){
		         	 	var taglabel=data.split('~');
		         	 	var finaldata=taglabel[0];
		         	  $("#img_ajax2").hide(); drawBarChart(finaldata,'vert',taglabel[1]);}
		         	});
				break;
		   }

	 }
	 if(elem.value=="progress")
	 {
		switch (elem2.value) {	
		case 'Categories':
		 $.ajax({
         	 url: "ResponseChartAjax.php", 
         	 type: "POST",
         	 data: {studentID:studentID,childclass:childClass,dateRange:seldaterange,mode:'topicProgressCategoriesAjax'},
         	 success: function(data){ $("#img_ajax").hide();drawpiechart(data,'y',note_to_hide);}
         	});
		break;
		}
	  }

	  if(elem.value=="accuracy")
	  { 
		switch (elem2.value) {	
		case 'Categories':
		 $.ajax({
         	 url: "ResponseChartAjax.php", 
         	 type: "POST",
         	 data: {studentID:studentID,childclass:childClass,dateRange:seldaterange,mode:'AccuracyForTopicsAjax'},
         	 success: function(data){$("#img_ajax").hide(); drawpiechart(data,'y',note_to_hide);}
         	});
		break;
		}
	 }

	 if(elem.value=='questionsbar')
		{ 
		    switch (elem2.value)
			{	
				case 'Topics':
		    	 $.ajax({
		         	 url: "ResponseChartAjax.php", 
		         	 type: "POST",
		         	 data: {studentID:studentID,childclass:childClass,dateRange:seldaterange,mode:'getnoofquestAcrossTopicsAjax'},
		         	 success: function(data){ $("#img_ajax2").hide(); drawBarChart(data,'hor');}
		         	});
				break;
		   }
	 }

	 if(elem.value=='progressbar')
		{ 
		    switch (elem2.value)
			{	
				case 'Topics':
				$.ajax({
		         	 url: "ResponseChartAjax.php", 
		         	 type: "POST",
		         	 data: {studentID:studentID,childclass:childClass,dateRange:seldaterange,mode:'getTopicProgressSummaryDetailsAjax'},
		         	 success: function(data){
		         	 	$("#img_ajax2").hide();
		         	 	$("#placeholder").hide();
		         	 	$("#topicProgressChart_list").show();
		         	 	drawTopicProgressSummaryChart(data);

		         	 }
		         	});
			     break;
		   }
	 }

	 if(elem.value=="questions")
	 {
		switch (elem2.value) {	
		case 'Home and School':
		 $.ajax({
         	 url: "ResponseChartAjax.php", 
         	 type: "POST",
         	 data: {studentID:studentID,dateRange:seldaterange,mode:'getnoofquesthomeschoolAjax'},
         	 success: function(data){$("#img_ajax").hide(); drawpiechart(data,'y',note_to_show);}
         	});
		break;
		}
	 }
}

function  drawpiechart(data,type,notestatus)
{	

	//notestatus indicates whether menu_note div to be display or not.
	//type indicates to show progress or numbers 
	
	try
	{   var jsondata = jQuery.parseJSON(data);
		var length = Object.keys(jsondata).length;
		if(typeof(jsondata)=='object' && length>0)
		{  
		drawUsagePieChart(jsondata,type);
		if(notestatus){ $("#menu_note").show(); } else {  $("#menu_note").hide(); }
		}
		else
		{ $("#pie_placeholder").text('No data found for this date range');
		   $("#menu_note").hide();

		}
	}
	catch(e)
	{
		$("#pie_placeholder").text('No data found for this date range');	
		$("#menu_note").hide();
	
	}
}

function drawTopicProgressSummaryChart(data) {
	//$("img.canvas-image").remove();
	//var jsondata=JSON.parse(result);
	var result = jQuery.parseJSON(data);
	var numOfTopics = result.ttProgress.length;
	if(numOfTopics == 0) {
		$("#topicProgressChart_noTopics").show();
		$("#topicProgressChart_scale").hide();
		$("#placeholder").text('No data found for this date range');
	} else {
		$("#topicProgressChart_noTopics").hide();
		$("#topicProgressChart_scale").show();
	}

	if(numOfTopics < 3) {
		for(var i = numOfTopics+1; i <= 3; i++) {
			$("#topicProgressChart_topicDesc" + i).hide();
			$("#topicProgressChart_canvas" + i).hide();
		}
	}

	for(var i = 1; i <=numOfTopics; i++) {
		var barWidth = 15;
		var startPoint = result.ttProgress[i-1].startProgress;
		var endPoint = result.ttProgress[i-1].endProgress;
		var currentPoint = result.ttProgress[i-1].currentProgress;
		$("#topicProgressChart_topicDesc" + i).text(result.ttProgress[i-1].ttDesc).show();
		$("#topicProgressChart_canvas" + i).show();
		drawProgressBar("topicProgressChart_canvas" + i, startPoint, endPoint, currentPoint, barWidth);
		if(i == 3) {
			break;
		}
	}
	$("#topicProgressChart").show(); 
}

function drawBarChart(data,type,tg)
{ 
    try
	{   
		$("#placeholder").show();
		$("#topicProgressChart_list").hide();
		var jsondata=JSON.parse(data);
	    var length = Object.keys(jsondata).length;
		if(typeof(jsondata)=='object' && length>0)
		{	
			if(type=="vert")
		    drawUsageBarChart(jsondata,tg); //tg helps to determine that duration in months,weeks or sec.
			else
			 drawUsageHorBarChart(jsondata);	
		}
		else
			$("#placeholder").text('No data found for this date range');
	}
	catch(e)
	{
		$("#placeholder").text('No data found for this date range');		
	}

}


    function configureDropDownLists(ddl1,ddl2) { 
    // Creating options dynamically for dropdown 2 and 4.
    var timeSpentOptions = new Array('Select','Home and School', 'Across Topics', 'Activities and questions');
    var topicProgressOptions = new Array('Select','Categories');
    var learningUnitAccuracyOptions = new Array('Select','Categories');
    var noOfquestionOptions = new Array('Select','Home and School');
    var timeSpentOptionsBar =new Array('Select','Duration');
    var topicProgressOptionsBar = new Array('Select','Topics');
    var noOfquestionOptionsBar= new Array('Select','Topics');

        switch (ddl1.value) {
        case 'timeSpent':
            ddl2.options.length = 0;
            for (i = 0; i < timeSpentOptions.length; i++) {
                createOption(ddl2, timeSpentOptions[i], timeSpentOptions[i]);
            }
            break;

        case 'progress':
            ddl2.options.length = 0; 
       		for (i = 0; i < topicProgressOptions.length; i++) {
            createOption(ddl2, topicProgressOptions[i], topicProgressOptions[i]);
            }
            break;

        case 'accuracy':
            ddl2.options.length = 0;
            for (i = 0; i < learningUnitAccuracyOptions.length; i++) {
                createOption(ddl2, learningUnitAccuracyOptions[i], learningUnitAccuracyOptions[i]);
            }
            break;

        case 'questions':
            ddl2.options.length = 0;
            for (i = 0; i < noOfquestionOptions.length; i++) {
                createOption(ddl2, noOfquestionOptions[i], noOfquestionOptions[i]);
            }
            break;
       case 'timeSpentbar':
            ddl2.options.length = 0;
            for (i = 0; i < timeSpentOptionsBar.length; i++) {
                createOption(ddl2, timeSpentOptionsBar[i], timeSpentOptionsBar[i]);
            }
            break;

       case 'questionsbar':
            ddl2.options.length = 0;
            for (i = 0; i < noOfquestionOptionsBar.length; i++) {
                createOption(ddl2, noOfquestionOptionsBar[i], noOfquestionOptionsBar[i]);
            }
            break;
		case 'progressbar':
            ddl2.options.length = 0; 
       		for (i = 0; i < topicProgressOptionsBar.length; i++) {
            createOption(ddl2, topicProgressOptionsBar[i], topicProgressOptionsBar[i]);
            }
            break;
            default:
                ddl2.options.length = 0;
            break;
    }

}

  function createOption(ddl, text, value) {
        var opt = document.createElement('option');
        opt.value = value;
        opt.text = text;
        ddl.options.add(opt);
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
	<?php
		for($i=0; $i<count($allstudentArray); $i++)
		{
			  $temp=$allstudentArray[$i]['username'];
			  $cUserID=$allstudentArray[$i]['userID'];
		
		 echo "	userArray.push('$temp');\r\n";
    	 echo "	respUserId.push($cUserID);\r\n";
    	}
?>
 </script>	
	
<script>
function validate()
{
    var childname = $("#nametag").val().trim();
    var childClass=$("#lstClass").val();
    var childSection=$("#lstSection").val();
    var seldaterange;
    if(childClass=="")
    {
    	alert("Please select a class");
        document.getElementById('lstClass').focus();
        return false;
    }
    if(document.getElementById('dateRange'))
    seldaterange=$("#dateRange").val();


    if(typeof(childSection)=="undefined")
    {
    	childSection='';
    }
    var studentid;
    if(childname=="")
    {
        alert("Please specify the Child Name");
        document.getElementById('nametag').focus();
        return false;
    }
    //check if the name is from the available list
    var found = 0;
    for(var i=0; i<userArray.length;i++)
    {
    	//alert("User Array"+userArray[i]+" "+childname);
        if(userArray[i].trim()==childname.trim())
        {
            found = 1;
            document.getElementById('student_userID').value=respUserId[i];
            studentid=respUserId[i];
            break;
        }
    }
    if(!found)
    {
        alert("Please specify a valid child name");
        document.getElementById('nametag').focus();
        return false;
    }
    else
    {
     if(seldaterange!="" || typeof(seldaterange)=="undefined")
     { 
    	var pdate=seldaterange.split('~');
    	var fdate=pdate[0];
    	var tdate=pdate[1];
    	var location='cls='+childClass+'&section='+childSection+'&studentID='+studentid+'&tillDate='+tdate+'&fromDate='+fdate;
    	
    }
    else
    {
    	 var location='cls='+childClass+'&section='+childSection+'&studentID='+studentid;
    }
  
   //All successful and  true. 	
    window.location="<?=$_SERVER['PHP_SELF'].'?'?>"+location;
    return false;
    }	
	//setTryingToUnload(); // Not known 
    return true;
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
		        document.getElementById('lstSection').style.display = "inline";
		        document.getElementById('lstSection').selectedIndex = 0;
		    }
		    else
		    {
		    	for(var i=0; i<gradeArray.length && gradeArray[i]!=cls; i++);
		       	if(sectionArray[i].length>0)
		       	{
					/*$(".noSection").show();*/
					$(".noSection").css("visibility","visible");
	    	    	
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
	    	        var allSections = sectionArray[i].join();
	    	        $("#lstSection option:first-child").val(allSections);
	    	        document.getElementById('lstSection').style.display = "inline";
	    	        document.getElementById('lblSection').style.display = "inline";

	    	        if(sectionArray[i].length == 1)
	    	        {
	    	        	$($("#lstSection option")[1]).attr("selected","selected");
	    	        }
		        }
				else
				{
					/*$(".noSection").hide();*/
					$(".noSection").css("visibility","hidden");
 	       			$("#lstSection option:first-child").val("");
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
<body class="translation" onLoad="load();setSection('<?=$section?>');" onResize="load()">
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
				<div id="classReportMenu">
					<div class="classReport"></div>
					<?php $path="classLevelReport.php?cls=$class&section=$section&fromDate=$fromDate&tillDate=$tillDate&schoolCode=".$_SESSION['schoolCode'];?>
					<a href="<?=$path?>"><div class="pageText" style="color:#FAAC1B !important">Class Report</div></a>
				</div>
				<div id="studentWiseUsageReportMenu">
					<div class="editDetails"></div>
					<?php $stupath="myStudents.php?class=$class&section=$section&tillDate=$tillDate&fromDate=$fromDate";?>
					<a href="<?=$stupath?>"><div class="pageText">Student-wise Usage Report</div></a>
				</div>
				<div id="individualBoard">
					<div class="noticeBoard"></div>
					<a href="studentWiseReport.php"><div class="pageText" style="color:#f16321 !important">Individual Student Report</div></a>
				</div>
				<!-- <div id="noticeBoard">
					<div class="noticeBoard"></div>
					<a href="studentNoticeBoard.php"><div class="pageText">Student Notice Board</div></a>
				</div> -->
		</div>


<!-- Another header section for choosing class and section -->
<form name="form1" method="post" id="form1">
       <input type="hidden" name="student_userID" id="student_userID" value="">
       <input type="hidden" name="datevalue" id="datevalue" value="<?= getDateRangeValue($fromDate,$tillDate)?>">
			<table id="topicDetails" data-intro="Select class, section and date range.">
				<td width="5%"><label for="lstClass">Class</label></td>
		        <td width="20%" style="border-right:1px solid #626161">
		            <select name="class" id="lstClass"  onchange="setSection('')" style="width:65%;">
					<?php 
						if(count($classArray) != 1)
							echo '<option value="">Select</option>';
						for($i=0; $i<count($classArray); $i++)	{ 
					?>
						<option value="<?=$classArray[$i]?>" <?php if($class==$classArray[$i]) echo " selected";?>><?=$classArray[$i]?></option>
					<?php	}
						if(count($classArray) == 1){
					?>
						<script type="text/javascript">
							$("#lstClass").val("<?=$classArray[0]?>");
						</script>
					<?php }	?>
					</select>
		        </td>
				<?php if($hasSections) { ?>
				<td width="6%" class="noSection"><label  id="lblSection" for="lstSection" style="margin-left:20px;">Section</label></td>
		        <td class="noSection" width="20%" style="border-right:1px solid #626161">
		            <select name="section" id="lstSection" style="width:65%;">
				</select>
		        </td>
				<?php }  
			
				?>
			  <td colspan="8" width="12%" class="rangedate"> <label id="lblDateRange" for="dateRange" style="margin-left:20px;">Date Range</label></td>
			  <td width="20%" style="border-right:1px solid #626161" class="rangedate" data-intro="Choose the most relevant date range. We have made it easy for you to select the frequently used options, but feel free to customize the range." >
		     <?php 
		     //$setdate=getDateFormated($fromDate,$tillDate);
		     $setdate ="Custom Range";
		   

		     ?>
		      	<select name="dateRange" id="dateRange" style="width:85%;">
		      		<option value="<?php echo getDateRangeValue(date("Y-m-d"), date("Y-m-d"));?>" <?php if($selectedDate==getDateRangeValue(date("Y-m-d"), date("Y-m-d"))){ echo 'selected="selected"'; $iscustom=false; }?>>Today</option>
					<option value="<?php echo getDateRangeValue(date("Y-m-d", strtotime("-1 days")), date("Y-m-d", strtotime("-1 days"))); ?>" <?php if($selectedDate==getDateRangeValue(date("Y-m-d", strtotime("-1 days")), date("Y-m-d", strtotime("-1 days")))){echo 'selected="selected"';  $iscustom=false; }?>>Yesterday</option>
					<option value="<?php echo getDateRangeValue(date("Y-m-d", strtotime("-7 days")), date("Y-m-d", strtotime("-1 days"))); ?>" <?php if($selectedDate==getDateRangeValue(date("Y-m-d", strtotime("-7 days")), date("Y-m-d", strtotime("-1 days")))){ echo 'selected="selected"';  $iscustom=false;  }?>>Previous 7 days</option>
					<option value="<?php echo getDateRangeValue(date("Y-m-d", strtotime("-30 days")), date("Y-m-d", strtotime("-1 days"))); ?>" <?php if($selectedDate==getDateRangeValue(date("Y-m-d", strtotime("-30 days")), date("Y-m-d", strtotime("-1 days")))){ echo 'selected="selected"';  $iscustom=false;  }?>>Previous 30 days</option>
					<option 
						<?php  
							/*if($fromDate>=date("Y-m-d", strtotime("-3 months")) || $tillDate <=date("Y-m-d") )
							{ }*/
							if($iscustom)
							{	
							
 							echo 'value="'.getDateRangeValue($fromDate, $tillDate).'" '; $setvalue=getDateFormated($fromDate,$tillDate).'(Change)';
							echo 'selected="selected"'; 
							}
							else
							{
								echo 'value="" '; $setvalue='Custom Range';	
							}
							echo '>'.$setvalue;
							
						?>
					</option>
				</select>
					
			</td>

	<td colspan="8" width="12%"> 
	<label id="lblDateRange" for="dateRange">Child's Name</label>
    </td>
	<td><input type="text" id="nametag" name="nametag" value="<?=$childUserName?>" autocomplete="off"></td>
	<td width="20%" style="border-right:1px solid #626161" data-intro="Choose the most relevant date range. We have made it easy for you to select the frequently used options, but feel free to customize the range." >
	<input type="button" value="Go" id="generateReportButton" class="button" style="margin-left:20px;" onClick="return validate()">
	</td>
	
	</table>
</form>
<!-- end of header part 2-->
  <!-- code for Bar Graph Implementation -->
   
    <!-- End of Code for Bar Graph -->
			<!-- Tabbing Structure -->
			
			<?php if(!empty($studentID)) { ?>
			<div class="container">

 
    		<!-- <ul class="tabs">
		        <li class="tab-link current" data-tab="tab-1">Individual Report (New)</li>
		        <li  class="tab-link" data-tab="tab-2">Individual Report (Old)</li>
			</ul> -->
		    <div class="ionTabs__body">

		        <div id="tab-1" class="tab-content current">
		           <!-- tab 1 Content -->
		           <div id="tab_1_report" class="tabContainertop">

		           	
		           	<div class="tabContainerTopBar">
		           	<span style='font-family:verdana,arial,sans-serif;font-size:14px;font-weight:bold;'>Summary report for <?=$childfullName?> from  <?=date("d/m/Y", strtotime($fromDate))?> to <?=date("d/m/Y", strtotime($tillDate))?></span>
		           	</div>
		           	<div class="profileContainer">
		           	<div class="profilepictab">
		           		<img src="images/blank-profile2.jpg" />
		           		<div><?=$childfullName?></div>
		           	</div>
		           	<div class="profilepictab2">
		           		<div align="center">Total Login Time - <?=$studentTimeSpent?>
		           		<br>Total Topic(s) Attempted - <?=$totaltopicsAttempted?>
		           		<br>Total Learning Unit(s) Attempted - <?=$totalLearningUnits?>
		           		<br>Total No. of Question(s) Attempted - <?=$totalNoOfQuesAttempted?>
		           	</div>
		           	</div>
		           	<div class="profiletab3">
		           		<img src="images/averageUsage.png" style="margin:10px;"/>
		           	<div align="center">Average Usage <br> per day = <span class="<?=$studentOverallusage?>"><?=$studentAvgTimeSpent?> (<?=ucfirst($studentOverallusage)?>)</span>
		           	</div>	
		           	</div>
		             <div class="profiletab4">
		           		<img src="images/averageAccuracy.png" style="margin:10px;"/>
		           		<div align="center">Average Accuracy of <br> questions = <span class="<?=$studentAccuracyColor?>"><?=$studentAccuracy.'% '?>(<?=ucfirst($studentAccuracyColor)?>)</span></div>
		           	</div>
		           	</div>
		        
		           	<div class="plowerContainer">
		           	<div class="leftplower">
  
		           	<div style="margin:10px">
		           	<label> Breakdown of </label>
		           	<select id="leftsel" name="leftsel" style="width:170px" onChange="configureDropDownLists(this,document.getElementById('resultleftsel'))">
		            <option value="timeSpent">Time Spent</option> 
		           	<option value="progress">Topic Progress</option>
		            <option value="accuracy">Learning units Accuracy</option>
		           	<option value="questions">Numbers of questions</option>
		           	</select>
		           	<label> with </label>
		           	<select id="resultleftsel" name="resultleftsel" style="width:145px" onChange="redrawchart(document.getElementById('leftsel'),this)">
		           	<option value="Home and School" selected="selected">Home and School</option>
		           	<option value="Across Topics">Across Topics</option>
		           	<option value="Activities and questions">Activities and questions</option>
		           	</select>
		            </div>
		            
		            <div id="pie-content">
		            <div class="demo-container">
					<div id="pie_placeholder" class="demo-placeholder">
						<div id="img_ajax" class="image_ajax_load" style="display:none">	
						<img src="images/ajax-loader.gif" />
						</div>

					</div>
					

					<div id="menu_note" style="width:100%; display:none">
					<h5 align="center">Data for this graph is only available upto yesterday</h5>
					</div>

					</div>
		            </div>

		           	</div>

		           	<div class="rightplower">
		           	<div style="margin:10px;width: 308px;">    
		           	<select id="rightsel" name="rightsel" style="margin-left:5px;" onChange="configureDropDownLists(this,document.getElementById('rightsel2'))">
		            <option value="timeSpentbar">Time Spent</option> 
		           	<option value="progressbar">Topic Progress</option>
		           	<option value="questionsbar">Numbers of questions</option>
		           	</select>
		           	<select id="rightsel2" name="rightsel2" style="margin-left:20px;" onChange="redrawchart(document.getElementById('rightsel'),this)">
		   		    <option>Select</option>
		   		    <option value="Duration" selected="selected">Duration</option>
		           	</select>
		           </div>
		             <div id="content">
	 					 <div class="demo-container">
	  					<div id="placeholder" class="demo-placeholder" >
	  						<div id="img_ajax2" class="image_ajax_load" style="display:none">	
						     <img src="images/ajax-loader.gif" />
						    </div>
	  					</div>
	  					<div id="topicProgressChart_list" style="display:none;">
								<div id="topicProgressChart_noTopics" style="text-align: center; width:400px; height:100;">No data to display</div>
								<div id="topicProgressChart_topicTitle1" class="topic-progress-chart">
									<span id="topicProgressChart_topicDesc1"></span>
								</div>
								<canvas id="topicProgressChart_canvas1" width="400" height="50" class="topic-progress-chart base2"></canvas>

								<div id="topicProgressChart_topicTitle2" class="topic-progress-chart">
									<span id="topicProgressChart_topicDesc2"></span>
								</div>
								<canvas id="topicProgressChart_canvas2" width="400" height="50" class="topic-progress-chart base2"></canvas>

								<div id="topicProgressChart_topicTitle3" class="topic-progress-chart">
									<span id="topicProgressChart_topicDesc3"></span>
								</div>
								<canvas id="topicProgressChart_canvas3" width="400" height="50" class="topic-progress-chart base2"></canvas>
								<div id="topicProgressChart_scale" class="topic-progress-scale" style="background-color: #F5F5F5;">

									<canvas id="topicProgressScale" width="400" height="50" class="topic-progress-chart base2" style="margin-top: 10px;"></canvas>

								</div>
							</div>

	  					</div>
    				 </div>	
		         
		           	</div>	
		           	</div>
		           </div>
		           <!-- End of COntent 1 -->
		         <br/><br/>
				<table id="result_grid" cellpadding="5" border="1" width="97%" align="center" class="gridtable">
		        </div>
		        <!-- <div  id="tab-2" class="tab-content"> -->
				<!-- Report Div -->	    
    			<!-- <div id="fliptopdiv" class="flipped" style="overflow-y:auto;"> -->
    	
				<tr>
					<th colspan="8" class="grid_header_top" style='font-size:14px;'><?="Topic-wise individual Report of ".studentName($studentID)?></th>	
				</tr>
		        <tr>
		        	<th scope="col" style='vertical-align:middle;' width="30px">Sr No</th>
		            <th scope="col" style='vertical-align:middle;' width="250px">Topic</th>
		            <th scope="col" style='vertical-align:middle;' width="160px">Progress</th>
		            <th scope="col" style='vertical-align:middle;' width="50px">Trail</th>
					<th scope="col" style='vertical-align:middle;' width="70px">Total Qs</th>
					<th scope="col" style='vertical-align:middle;' width="50px">% Correct</th>
					<th scope="col" style='vertical-align:middle;' width="50px">Total Attempts</th>
					<th scope="col" style='vertical-align:middle;' width="150px">Learning units not cleared</th>
		        </tr>
		    		
		    		 
				    <?php 
				    $i=0;
				    foreach ($studentProgress as $ttCode=>$arrDetails) { 
				    	$i++;
						$currentStatusArray[$ttCode]	=	getCurrentStatus($studentID,$ttCode);
				    	?>
				 			<tr>
				        	<td align="center"><?=$i?></td>
				            <td><?=$arrDetails['desc']?></td>
				            <td>
							<?php if($studentProgress[$ttCode]["progress"]!="") { 
							$flowstr	=	$studentProgress[$ttCode]["flow"];
							if($flowstr=="")
								$flowstr	=	"MS";
							$flow = str_replace(" ","_",$flowstr);
							?>
								
							<div class="topicProgress">
							<?php echo showTopicProgress($studentID,$studentProgress[$ttCode]["progress"],$studentProgress[$ttCode]["failedCluster"],$clusterArray[$ttCode][$flow], $sdlArray[$ttCode][$flow], $currentStatusArray[$ttCode]["currentCluster"], $currentStatusArray[$ttCode]["currentSdl"],$class); ?>
							
							<?php if(in_array($studentID,$lowerLevelStudent[$ttCode])) { ?>
				            <img src="assets/red_star.gif" alt="Red Star" height="20" width="20" style="clear:left;">
					        <?php } ?>
					        <?php if($studentProgress[$ttCode]["higherLevel"]==1) { ?>
					            <img src="assets/green_star.gif" alt="Green Star" height="20" width="20" style="clear:left;">
					        <?php } 
							} else { echo $detail["progress"]==""?"&nbsp;":$detail["progress"]; } ?>

							</div>
							
							</td>
				            <td align="center"><a class="buttonLink" href="studentTrail.php?topic_passed_id=<?=$ttCode?>&user_passed_id=<?=$studentID?>" target="_blank" style="text-decoration:underline;color:blue;" onClick="setTimeout(function() {tryingToUnloadPage = false;},500);">trail</a></td>
							<td align="center"><?=$studentProgress[$ttCode]["totalQuesAttmpt"]?>		</td>
							<td align="center"><?=$studentProgress[$ttCode]["accuracy"]?>		</td>
							<td align="center"><?=$studentProgress[$ttCode]["attempt"]?>		</td>
							<td><?=getClusterName($studentProgress[$ttCode]["failedCluster"])?>		</td>
				        </tr>
				     
					   <?php } ?>
					</table>
					</div>


					 <div align="center" class='legend'>
								<img src="assets/green_star.gif" alt="Green Star" height="20" width="20"> Gone to a higher level &nbsp;&nbsp;
								<img src="assets/red_star.gif" alt="Green Star" height="20" width="20"> CURRENTLY in a lower level
								<br/>
								<span style="font-size:20px; font-weight:bold">&darr;</span> indicates current position of students who have fallen back in the topic in the first attempt &nbsp;&nbsp;
								<span style="font-size:15px; font-weight:bold">&darr;<sub>R</sub></span> indicates current position of students in case of repeat attempt in the topic
								<br/>(Note: This indication is not available to the student)
				</div>    
				</div>
		       <!--  <div class="ionTabs__preloader"></div> -->
		   	</div>
			</div>

	<!-- End of tabbing structure -->
		
				<!-- </div> -->
			</div>
	<? } ?>
</div>
<?php include("footer.php") ?>
<div id="dialog-form" title="Select Date Range" style="display: none"> 
  <form>
    <fieldset>
    <div id="dateRangeSelector" >
    	<label for="fromDate">From</label>
    	<input type="text" id="fromDate" name="fromDate" value="<?=date("d-m-Y",strtotime($fromDate))?>" onKeyDown="return DateFormat(this, event.keyCode)" maxlength="10">
    	<label for="toDate" style="margin-left: 4p0x;">To</label>
    	<input type="text" id="toDate" name="toDate" value="<?=date("d-m-Y",strtotime($tillDate))?>"  onKeyDown="return DateFormat(this, event.keyCode)" maxlength="10">
    </div>
      <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
    </fieldset>
  </form>
</div>
<div id="printLoading" width="300" height="300" style="display: none">
	<img src="images/fetching_data.gif">
	<p>This may take a few minutes</p> 
</div>
<?php
function studentName($userID)
{
	$sq	=	"SELECT username FROM adepts_userDetails WHERE userID='$userID'";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	return $rw[0];
}
function studentfullName($userID)
{
	$sq	=	"SELECT childName FROM adepts_userDetails WHERE userID='$userID'";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	return $rw[0];
}


function getTeacherTopicProgress2($teacherTopics,$cls,$userID)
{	
	$flowN	=	array();
	$total	=	0;
	$teacherTopicDetails = array();	
	foreach($teacherTopics as $ttCode=>$ttDesc)
	{		
                $teacherTopicDetails[$ttCode]["desc"]	=	$ttDesc;
        	$q = "SELECT distinct flow FROM ".TBL_TOPIC_STATUS." WHERE  userID = $userID AND teacherTopicCode='".$ttCode."'";
            // echo $q;   
                $r = mysql_query($q);
                if(mysql_num_rows($r)>0) {            
                        while($l = mysql_fetch_array($r))
                        {
                        	$flowN = $l[0];
                        	$flowStr = str_replace(" ","_",$flowN);
                        	${"objTopicProgress".$flowStr} = new topicProgress($ttCode, $cls, $flowN, SUBJECTNO);
                        }
                
                	$sq	=	"SELECT userID, MAX(progress), SUM(noOfQuesAttempted),ROUND(SUM(perCorrect*noOfQuesAttempted)/SUM(noOfQuesAttempted),2),
                			 MAX(ttAttemptNo), GROUP_CONCAT(ttAttemptID), flow 
                			 FROM ".TBL_TOPIC_STATUS." WHERE teacherTopicCode='$ttCode' AND userID = $userID";
	              // echo $sq;
	               	$rs	=	mysql_query($sq);
                	while($rw=mysql_fetch_array($rs))
                	{
                		$flowK	=	$rw[6];
                        $flowK	=	str_replace(" ","_",$flowK);
                		//$teacherTopicDetails[$rw[0]]["progress"]	=	$rw[1];
                		$teacherTopicDetails[$ttCode]["progress"]	=	max($rw[1],${"objTopicProgress".$flowK}->getProgressInTT($rw[0]));
                		//$teacherTopicDetails[$ttCode]["higherLevel"] = ${"objTopicProgress".$flowK}->higherLevel;
						$teacherTopicDetails[$ttCode]["higherLevel"] = ${"objTopicProgress".$flowK}->getHigherLevel($rw[0]);
                		$arrayQuesDetails	=	getQuesAccuracy($rw[0],$ttCode,$cls);
                		$teacherTopicDetails[$ttCode]["totalQuesAttmpt"]	=	$arrayQuesDetails["totalQ"];
                		$teacherTopicDetails[$ttCode]["accuracy"]	=	$arrayQuesDetails["accuracy"];			
                		$teacherTopicDetails[$ttCode]["attempt"]	=	$rw[4];
                		$teacherTopicDetails[$ttCode]["failedCluster"]	=	getFailedLUs($rw[5], $cls, ${"objTopicProgress".$flowK});
                		$teacherTopicDetails[$ttCode]["flow"]	=	$rw[6];                		
                	}
                }
                else
                {
                        $teacherTopicDetails[$ttCode]["progress"]	=	"";
                        $teacherTopicDetails[$ttCode]["higherLevel"] = "";
                        $teacherTopicDetails[$ttCode]["totalQuesAttmpt"] = 0;
                        $teacherTopicDetails[$ttCode]["accuracy"] = "";
                        $teacherTopicDetails[$ttCode]["attempt"] = 0;
                        $teacherTopicDetails[$ttCode]["failedCluster"] = "";
                        $teacherTopicDetails[$ttCode]["flow"] = "";
                }
	}
	return $teacherTopicDetails;
}

function getFailedLUs($ttAttemptID, $class, $objTopicProgress)
{
	//Get the failed clusters in the last completed attempt, if any, or the current attempt
	$failedClusterArray = array();
	$query  = "SELECT ttAttemptID, result, failedClusters FROM ".TBL_TOPIC_STATUS." WHERE ttAttemptID in ($ttAttemptID) ORDER BY ttAttemptID DESC";
	$result = mysql_query($query);
	$noOfAttempts = mysql_num_rows($result);
	while ($line = mysql_fetch_array($result))
	{
		if(($line[1]!="" && $noOfAttempts>1) || ($noOfAttempts==1))
		{
			if($line[2]!="")
			{
				$tmpCluster = explode(",",$line[2]);
				for($i=0; $i<count($tmpCluster); $i++)
				{
					$clusterCode = trim($tmpCluster[$i]);
					$levelArray = $objTopicProgress->objTT->getClusterLevel($clusterCode);
					if($levelArray[0] <= $class )	//Do not show  the clusters failed of  a higher level.
						array_push($failedClusterArray,trim($tmpCluster[$i]));
				}
			}
			break;
		}
	}
	return $failedClusterArray;
}

?>
<script>
function suggestUserList(userArray)
{   
//console.log(userArray);  
	var obj = actb(document.getElementById('nametag'),userArray);
 // document.getElementById('childName').disabled = false;
}
</script>