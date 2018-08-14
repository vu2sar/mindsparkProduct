<?php
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	include("../userInterface/constants.php");
	include("header.php");
	include("../slave_connectivity.php");
	
	$userID     = $_SESSION['userID'];
	$schoolCode = $_SESSION['schoolCode'];

	$query  = "SELECT childName, startDate, endDate, category, subcategory FROM adepts_userDetails WHERE userID=".$userID;
	
	$result = mysql_query($query) or die(mysql_error());
	$line   = mysql_fetch_array($result);
	$userName 	= $line[0];
	$startDate  = $line[1];
	$endDate    = $line[2];
	$category   = $line[3];
	$subcategory = $line[4];

	

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
	//echo $query;
	$classArray = $sectionArray = array();
	$hasSections = false;
	$result = mysql_query($query) or die(mysql_error());
	$newSection=array();
	
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
		      	  $newSection[]=$sections[$i];
		}
		$sectionStr = substr($sectionStr,0,-1);
		array_push($sectionArray, $sectionStr);
	}



	$oneDay = 24*60*60;
	
	$class    = isset($_REQUEST['class'])?$_REQUEST['class']:"";
	$section  = isset($_REQUEST['section'])?$_REQUEST['section']:"";
	$tillDate = isset($_REQUEST['tillDate'])?$_REQUEST['tillDate']:date("d-m-Y");
	
	$fromDate = isset($_REQUEST['fromDate'])?$_REQUEST['fromDate']:date("d-m-Y");
	$chkTopicsAttempted = isset($__REQUEST['chkTopicsAttempted'])?1:0;
	$chkOtherTask = isset($_POST['chkOtherTask'])?1:0;
	if((!empty($class)) && (!empty($fromDate)) && (!empty($tillDate)))
	{
		$fill_to_generate=1;
		//generateReport();
	}
	else
		$fill_to_generate=0;
	//echo $section;
?>

<title>Student Wise Report</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/myStudents.css?ver=3" rel="stylesheet" type="text/css">
<link href="css/topicReport.css?ver=2" rel="stylesheet" type="text/css">
<!-- <script src="libs/jquery-1.9.1.js"></script> -->
<link rel="stylesheet" href="css/jquery-ui.css" />

<script src="libs/jquery-ui.js"></script>

<script>
$(function() {
		
	    $("#dateFrom").datepicker({
	      defaultDate: "today",	      
	      dateFormat: 'dd-mm-yy',
	      numberOfMonths: 1,
	      maxDate: 'today',	      
	    
	      onSelect: function( selectedDate ) {
	        $( "#dateTo" ).datepicker( "option", "minDate", selectedDate );
	      }
	    });

	    $( "#dateTo" ).datepicker({
	      defaultDate: "today",	      
	      dateFormat: 'dd-mm-yy',
	      numberOfMonths: 1,
	      maxDate: 'today',	      	      
	      onSelect: function( selectedDate ) {
	        $( "#dateFrom" ).datepicker( "option", "maxDate", selectedDate );
	      }
	    });
	    $("#dateFrom").change(function (){	
	    	var today_in_server = '<?=date("d-m-Y",strtotime($fromDate));?>';
	    	var today_in_server_to = '<?=date("d-m-Y",strtotime($tillDate));?>';     	
	    	var fromDate = $("#dateFrom").datepicker("getDate");  
	    	var toDate = $("#dateTo").datepicker("getDate");  	
	    	if(fromDate.length !=0)
	    	{
		    	var today = new Date();
		    	    	
		    	if(fromDate>today){
		    		alert("Future dates are not allowed !!");
		    		$("#dateFrom").val(today_in_server);
		    		$("#dateFrom").focus();
		    	}
		    	else if(toDate.length != 0)
		    	{
			    	if(new  Date(fromDate) > new Date(toDate))
		    		{
		    			alert("From date should come before To date");  	    			      				
		    			$("#dateFrom").val(today_in_server);
		    			$("#dateTo").val(today_in_server_to);	    			
			    		$("#dateFrom").focus();
		    		}	
		    	}	    	
	    	}	    
	    });
	    $("#dateTo").change(function (){	
	    	var today_in_server = '<?=date("d-m-Y",strtotime($tillDate));?>'; 
	    	var today_in_server_from = '<?=date("d-m-Y",strtotime($fromDate));?>';    	
	    	var toDate = $("#dateTo").datepicker("getDate");
	    	var fromDate = $("#dateFrom").datepicker("getDate");
	    	if(toDate.length !=0)
	    	{	    	
	    		var today = new Date();	    
		    	if(toDate>today)
		    	{
		    		alert("Future dates are not allowed !!");
		    		$("#dateTo").val(today_in_server);
		    		$("#dateTo").focus();
		    	}
	    		else if(new  Date(toDate) < new Date(fromDate))
	    		{
	    			alert("To date should come after From date");  	    			      				
	    			$("#dateTo").val(today_in_server);
	    			$("#dateFrom").val(today_in_server_from);	    			
		    		$("#dateTo").focus();
	    		}  	   
	    	}
	    });
});
</script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="libs/tablesort.js"></script>
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
		$("#students").css("font-size","1.4em");
		$("#students").css("margin-left","40px");
		$(".arrow-right-yellow").css("margin-left","10px");
		$(".rectangle-right-yellow").css("display","block");
		$(".arrow-right-yellow").css("margin-top","3px");
		$(".rectangle-right-yellow").css("margin-top","3px");
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
var k;
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


function getformattedDate(dd1)
{
	var p = dd1.split("-");
	var date = p[2]+"-"+p[1]+"-"+p[0];
	return date;
}
		
</script>
<script>
	var gradeArray   = new Array();
    var sectionArray = new Array();
    var sectionStrText = new Array();
    var sectionAll;
	<?php
	
		for($i=0; $i<count($classArray); $i++)
		{
		    echo "gradeArray.push($classArray[$i]);\r\n";
		    echo "sectionArray[$i] = new Array($sectionArray[$i]);\r\n";
		    echo "sectionStrText.push($sectionArray[$i]);\r\n";
		}
	
	?>
	
	var SectionString = '\'' + sectionStrText.join('\',\'') + '\'';
	//console.log("DSSSS "+SectionString);
	
	function setSection(sec)
	{   //alert(sec);
		var sectionArraysplit=sec.split(',');
		var path='';
		//console.log(sectionArraysplit+sectionArraysplit.length);
		var cls = document.getElementById('lstClass').value;
		var section=document.getElementById('lstSection').value;
		
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
		       		//console.log("SARray"+sectionArray[i]);
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
	    	        var allSections=sectionArray[i].join();
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
	function generateReport()
	{ 
		var cls = document.getElementById('lstClass').value;
		var sec=$("#lstSection").val();
		var path='';
		var sdate=getformattedDate(document.getElementById('dateFrom').value);
		var edate=getformattedDate(document.getElementById('dateTo').value);
		

		if(cls ==""){
			alert("Please select a class!");
			document.getElementById('lstClass').focus();
			return false;
		}
		else{
			$(".downloadXL").css("visibility", "visible"); // To visible download option.
			
			var chkTopicsAttempted = 0;
			var chkOtherTask=0;
			var clsSection = "NoSection";
			if($("#chkTopicsAttempted").is(":checked"))
				chkTopicsAttempted = 1;
			if($("#chkOtherTask").is(":checked"))
				chkOtherTask = 1;
			if(document.getElementById("lstSection"))
				clsSection = $("#lstSection").val();

			if($("#lstSection").val()==""){
				clsSection = "NoSection";
			}
			if (typeof(sec)== "undefined") { sec='';}

			
			path="classLevelReport.php?cls="+cls+"&section="+sec+"&tillDate="+edate+"&fromDate="+sdate+"&schoolCode="+<?=$_SESSION['schoolCode']?>;
			$("#classreportlink").attr("href",path);
			pathone="studentWiseReport.php?cls="+cls+"&section="+sec+"&tillDate="+edate+"&fromDate="+sdate;
			
			$("#individuallink").attr("href",pathone);
		
			$("#ajaxData").html('<span id="loadingImg"><img src="assets/loadingImg.gif" height="332" width="332"><br><center>Fetching large data, it will take some time.</center></span>');
			if(clsSection=="NoSection")
			{
				
				$.post("myStudentAjax.php","class="+$("#lstClass").val()+"&section="+sec+"&fromDate="+$("#dateFrom").val()+"&tillDate="+$("#dateTo").val()+"&schoolCode="+<?=$_SESSION["schoolCode"]?>+"&chkTopicsAttempted="+chkTopicsAttempted+"&chkOtherTask="+chkOtherTask,function(data) { 
					$("#loadingImg").remove();
					
					$("#ajaxData").html(data+'<br><span id="loadingImg"><img src="assets/loadingImg.gif" height="332" width="332"><br><center>Fetching large data, it will take some time.</center></span>');
					$("#loadingImg").remove();
					check_colspanning_of_columns();
				});
			}
			else if($("#lstSection").val()!="")
			{			
					
					var download_tag = $(".downloadXL");
					var class_name=$("#lstClass").val();
					var section_name=$("#lstSection").val();
					$(".downloadXL").attr( 'id', 'download_'+class_name+section_name);

				$.post("myStudentAjax.php","class="+$("#lstClass").val()+"&section="+$("#lstSection").val()+"&fromDate="+$("#dateFrom").val()+"&tillDate="+$("#dateTo").val()+"&schoolCode="+<?=$_SESSION["schoolCode"]?>+"&chkTopicsAttempted="+chkTopicsAttempted+"&chkOtherTask="+chkOtherTask,function(data) { 
					$("#loadingImg").remove();
					$("#ajaxData").html(data);
					
					check_colspanning_of_columns();
				});
			}
			else
			{
				
				$.each(gradeArray,function(key,value)
				{
					if($("#lstClass").val()==value)
					{
						var newSectionArray	=	sectionArray[key];
						k=0;
						$.each(newSectionArray,function(key,value)
						{
							if(key==0)
							{
								$.post("myStudentAjax.php","class="+$("#lstClass").val()+"&section="+value+"&fromDate="+$("#dateFrom").val()+"&tillDate="+$("#dateTo").val()+"&schoolCode="+<?=$_SESSION["schoolCode"]?>+"&chkTopicsAttempted="+chkTopicsAttempted+"&chkOtherTask="+chkOtherTask,function(data) { 
									$("#loadingImg").remove();
									k++;
									$("#ajaxData").html(data+'<br><span id="loadingImg"><img src="assets/loadingImg.gif" height="332" width="332"><br><center>Fetching large data, it will take some time.</center></span>');
									
								});
							}
							else if(key==1)
							{
								setTimeout(function(){
									$.post("myStudentAjax.php","class="+$("#lstClass").val()+"&section="+value+"&fromDate="+$("#dateFrom").val()+"&tillDate="+$("#dateTo").val()+"&schoolCode="+<?=$_SESSION["schoolCode"]?>+"&chkTopicsAttempted="+chkTopicsAttempted+"&chkOtherTask="+chkOtherTask,function(data) { 
										k++;
										$("#loadingImg").remove();
										$("#ajaxData").html($("#ajaxData").html()+data+'<br><span id="loadingImg"><img src="assets/loadingImg.gif" height="332" width="332"><br><center>Fetching large data, it will take some time.</center></span>');
										
										check_colspanning_of_columns();
									});		
								},15000);
							}
							else
							{
								setTimeout(function() { 
									$.post("myStudentAjax.php","class="+$("#lstClass").val()+"&section="+value+"&fromDate="+$("#dateFrom").val()+"&tillDate="+$("#dateTo").val()+"&schoolCode="+<?=$_SESSION["schoolCode"]?>+"&chkTopicsAttempted="+chkTopicsAttempted,function(data) { 
										k++;
										$("#loadingImg").remove();
										$("#ajaxData").html($("#ajaxData").html()+data+'<br><span id="loadingImg"><img src="assets/loadingImg.gif" height="332" width="332"><br><center>Fetching large data, it will take some time.</center></span>');
										
										check_colspanning_of_columns();
									});		
								},key*10000);
							}
						});
						var checkForCompletion	=	setInterval(function() {
							if(k==newSectionArray.length)
							{
								clearInterval(checkForCompletion);
								$("#loadingImg").remove();
								$("#ajaxData").html($("#ajaxData").html()+'<br><center>|---End of report---|</center>');

							}
						},5000);
					}
				});
			}
			return true;
		}

	}
	function showHideTopics(userid) {
		var obj = document.getElementById('pnlAttemptedTopics'+userid);
		var img = document.getElementById('img'+userid);
		if (obj.style.display=="none") {
			obj.style.display="block";
			img.src="assets/collapse.gif";
			img.title='';
			document.getElementById('pnlDefaultTopic'+userid).style.display="none";
		}
		else {
			obj.style.display="none";
			img.src="assets/expand.gif";
			img.title='Click to see all topics done';
			document.getElementById('pnlDefaultTopic'+userid).style.display="inline";
		}
	}

	$(document).ready(function(e) {
	
		$("#generateTable").on("click",".downloadXL",function() { 
			
			var ids	=	$(this).attr("id");
			var idsArr	=	ids.split("_");
			var tblData	=	"";
			$(".gridtable").each(function() {
				tblData	+=	$(this).html();
			});
			/*setTimeout(function(){*/
				tblData	=	"<table border='1' align='center' cellpadding='3' cellspacing='0' class='gridtable'  width='92%'>"+tblData+"</table>";
				$("#contentXL").val(tblData);
				setTryingToUnload();
				document.frmExcel.submit();	
			/*},1500);*/
		});
	
		$(document).on("click","#dispHomeUsage",function() {
			if($(this).is(":checked"))
			{
				//$("#btnGo").click();
				if($("#dateTo").val()=="<?=date("d-m-Y")?>")
				alert("Home usage data gets updated at the end of every day. For accurate home usage data, please select yesterday's date in the 'to' field.");
				$(".homeSchoolUsage").show();
				//$("#rgQues").attr("colspan",13);
			}
			else
			{
				$(".homeSchoolUsage").hide();
				//$("#rgQues").attr("colspan",7);
			}
			check_colspanning_of_columns();
		});

		$(document).on("click","#chkOtherTask",function() {
			
		 if($(this).is(":checked"))
			{
				$(".othertask").show();
				//$("#rgQues").attr("colspan",13);
			}
			else
			{
				$(".othertask").hide();
				//$("#rgQues").attr("colspan",7);
			}	
			 check_colspanning_of_columns();
		});
		$(document).on("click","#chkTopicsAttempted",function() {
			if(!$(this).is(":checked"))
			{
				$(".topicattempt").hide();				
			}
			else{

			$("#btnGo").click();
			}	
			check_colspanning_of_columns();		 
		});	
	  });
	
	 function check_colspanning_of_columns(){
			
			var disp_othrtask=$("#chkOtherTask");
			var disp_home=$("#dispHomeUsage");
			var disp_topics=$("#chkTopicsAttempted");
			var childClass=$("#lstClass").val();
			//	alert("Check Status for topic attempts :"+$("#chkTopicsAttempted").is(":checked")+"\n"+"Check Status for Home Usage"+$("#dispHomeUsage").is(":checked")+"\n"+"Check Status for Other Task :"+$("#chkOtherTask").is(":checked"));
			var jcolspan=7;
			//alert(childClass);
			if(childClass<3) { 
			$("#othrTask").attr("colspan",3);
		    }
		    if(childClass>=6) { 
			$("#othrTask").attr("colspan",5);
		    }
		    if(childClass>=8) {  	
			$("#lasthd").attr("colspan",4);
		    }
			if($(disp_home).is(":checked") && (!$(disp_topics).is(':checked')) && (!$(disp_othrtask).is(":checked")))
			{ // Case 1  When only dispHomeUsage has been clicked .
			 jcolspan=13;
			 $(".homeSchoolUsage").show(); $(".topicattempt").hide();  $(".othertask").hide();
			}
			else if($(disp_home).is(":checked") && ($(disp_topics).is(":checked")) && (!$(disp_othrtask).is(":checked")))	
			{ // Case 2  When both home usage and topics has been clicked .
				jcolspan=16;
				 $(".homeSchoolUsage").show();  $(".topicattempt").show();  $(".othertask").hide();
			}
			else if($(disp_home).is(":checked") && ($(disp_topics).is(":checked")) && ($(disp_othrtask).is(":checked")))	
			{ // Case 3  When all three are clicked.
				jcolspan=16;  
				 $(".homeSchoolUsage").show();
				  $(".othertask").show();
				   $(".topicattempt").show();
			}
			else if(!$(disp_home).is(":checked") && ($(disp_topics).is(":checked")) && ($(disp_othrtask).is(":checked")))	
			{ // Case 4  When both SHow TOpics Attempted and  Show Other Tasks has been clicked .
			//	alert("4");
			jcolspan=10;	
			$(".othertask").show();  $(".homeSchoolUsage").hide();
				   $(".topicattempt").show();		
			}
			else if(!$(disp_home).is(":checked") && (!$(disp_topics).is(":checked")) && ($(disp_othrtask).is(":checked")))	
			{ // Case 5  When only Show Other Tasks has been clicked .
				jcolspan=7;	
				 $(".homeSchoolUsage").hide();
				$(".othertask").show();		
				 $(".topicattempt").hide();	
			}
			else if(!$(disp_home).is(":checked") && ($(disp_topics).is(":checked")) && (!$(disp_othrtask).is(":checked")))	
			{ // Case 6  When only Show Topics attempted has been clicked .
			 jcolspan=10;
			 $(".homeSchoolUsage").hide();
			 $(".topicattempt").show();
			 $(".othertask").hide();
			}
			else if(!$(disp_home).is(":checked") && (!$(disp_topics).is(":checked")) && (!$(disp_othrtask).is(":checked")))	
			{ // Case 7  When nothing has been clicked .
		
			jcolspan=7;		
			 $(".homeSchoolUsage").hide();
				$(".othertask").hide();		
				 $(".topicattempt").hide();		 
			}
			else if($(disp_home).is(":checked") && (!$(disp_topics).is(":checked")) && ($(disp_othrtask).is(":checked")))	
			{ // Case 8  When both home and other task has been clicked .
		     jcolspan=13;
		      $(".homeSchoolUsage").show();
				$(".othertask").show();		
				 $(".topicattempt").hide();	
			}

			$("#rgQues").attr("colspan",jcolspan);
		}


</script>
</head>
<body class="translation" onLoad="load(); setSection('<?=$section?>');" onResize="load()" onmousemove="reset_interval()" onclick="reset_interval()" onkeypress="reset_interval()" onscroll="reset_interval()">
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
		<input type="hidden" id="display_report" name="display_report" value="<?=$fill_to_generate?>">
		<div id="trailContainer">


				<div id="headerBar">
				<div id="classReportMenu">
					<div class="classReport"></div>
					<?php $path="classLevelReport.php?cls=$class&section=$section&tillDate=$tillDate&fromDate=$fromDate";?>
					<a id="classreportlink" href="<?=$path?>"><div class="pageText" style="color:#FAAC1B !important">Class Report</div></a>
				</div>
				<div id="studentWiseUsageReportMenu">
					<div class="editDetails"></div>
					<a href="myStudents.php"><div class="pageText" style="color:#f16321 !important">Student-wise Usage Report</div></a>
				</div>
				<div id="individualBoard">
					<div class="noticeBoard"></div>
				<?php $pathone="studentWiseReport.php?cls=$class&section=$section&tillDate=$tillDate&fromDate=$fromDate"; ?>
					<a id="individuallink" href="<?=$pathone?>"><div class="pageText">Individual Student Report</div></a>
				</div>
				<!-- <div id="noticeBoard">
					<div class="noticeBoard"></div>
					<a href="studentNoticeBoard.php"><div class="pageText">Student Notice Board</div></a>
				</div> -->
			</div>



			<div id="headerBar" style="display:none">
				<div id="resetPassword2">
					<div class="resetPassword2"></div>
					<a href="resetStudentPassword.php"><div class="pageText" >Reset Password</div></a>
				</div>
				<div id="noticeBoard">
					<div class="noticeBoard"></div>
					<a href="studentNoticeBoard.php"><div class="pageText">Student Notice Board</div></a>
				</div>
				<div id="editDetails">
					<div class="editDetails"></div>
					<a href="editStudentDetails.php"><div class="pageText">Edit Student Details</div></a>
				</div>
			</div>
			<form id="frmTeacherReport" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
			
			<table id="topicDetails">
				<td ><label for="lstClass">Class</label></td>
		        <td width="15%" style="border-right:1px solid #626161">
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
					<?php } ?>
					</select>
		        </td>
				<?php if($hasSections) { ?>
				<td  class="noSection"><label  id="lblSection" for="lstSection" style="margin-left:20px;">Section:</label></td>
		        <td class="noSection" width="15%" style="border-right:1px solid #626161">
		            <select name="section" id="lstSection" style="width:65%;">
				</select>
		        </td>
				<?php } ?>
				<td >
					<label for="fromDate">From</label>
		        </td>
				<td  style="border-right:1px solid #626161">
			<input type="text" name="fromDate" value="<?=date("d-m-Y",strtotime($fromDate))?>" class="datepicker floatLeft" id="dateFrom" value="" autocomplete="off" onkeydown="return DateFormat(this, event.keyCode)"  maxlength="10" size="15"/>
			<div class="calenderImage linkPointer" id="from" onClick="openCalender(id)"></div></td>
				<td >
					<label for="tillDate">To</label>
		        </td>
				<td ><input type="text" name="tillDate" value="<?=date("d-m-Y",strtotime($tillDate))?>" class="datepicker floatLeft" id="dateTo" value="" autocomplete="off"  onkeydown="return DateFormat(this, event.keyCode)" maxlength="10" size="15"/><div class="calenderImage linkPointer" id="to" onClick="openCalender(id)"></div></td>
		       
				<!--<td colspan="8" width="24%"><input type="checkbox" id="chkTopicsAttempted" name="chkTopicsAttempted" <?php if($chkTopicsAttempted) echo " checked";?>>Show Topics Attempted</td>-->
			</table>
		
			<table id="generateTable" class="pagingTable">
			<td><label><input type="checkbox" name="dispHomeUsage" id="dispHomeUsage" />Show Home Usage</label></td> <!-- <a href="javascript:void(0)" class="downloadXL" id="download_<?=$class.$section?>">Download Table In Excel</a></b>) --> </td>
			<td><label><input type="checkbox" name="chkTopicsAttempted" id="chkTopicsAttempted" <?php if($chkTopicsAttempted) echo " checked";?>/>Show Topics Attempted </label></td>
			<td><label><input type="checkbox" name="chkOtherTask" id="chkOtherTask" <?php if($chkOtherTask) echo " checked";?>/>Show other Tasks</label></td>
			<td width="12%"><input style="margin-left:20px; margin-right:20px" type="button" class="button" name="generate" id="btnGo" value="Generate" onClick="return generateReport();"></td> 
		    <td><input type="button" style="margin-left:20px;visibility:hidden" onclick="javascript:void(0)" class="downloadXL button" id="download_"  name="Download" value="Download"/></td> 
			</table>
			<!-- <table id="generateTable">
				<td width="5%">
					<label for="fromDate">From</label>
		        </td>
				<td width="ty25%" sle="border-right:1px solid #626161"><input type="text" name="fromDate" value="<?=$fromDate?>" class="datepicker floatLeft" id="dateFrom" value="" autocomplete="off" onkeydown="return DateFormat(this, event.keyCode)" maxlength="10" size="20"/><div class="calenderImage linkPointer" id="from" onClick="openCalender(id)"></div></td>
				<td width="5%">
					<label style="margin-left:20px;" for="tillDate">To</label>
		        </td>
				<td width="25%"  style="border-right:1px solid #626161"><input type="text" name="tillDate" value="<?=$tillDate?>" class="datepicker floatLeft" id="dateTo" value="" autocomplete="off" onkeydown="return DateFormat(this, event.keyCode)" maxlength="10" size="20"/><div class="calenderImage linkPointer" id="to" onClick="openCalender(id)"></div></td>
		        <td width="12%" style="border-right:1px solid #626161"><input style="margin-left:20px; margin-right:20px" type="button" class="button" name="generate" id="btnGo" value="Generate" onClick="return generateReport();"></td> 
		        <td><input type="button" style="margin-left:20px;visibility:hidden" onclick="javascript:void(0)" class="downloadXL button" id="download_"  name="Download" value="Download"/></td> 
			</table> -->
			<input type="hidden" name="schoolCode" id="schoolCode" value="<?=$schoolCode?>">
			</form>
			<div id="ajaxData" align="center"></div>
			<form name='frmExcel' target="" action="export.php" method="POST">
				<input type="hidden" name="content" id="contentXL" value=''>
			</form>
		</div>
	</div>

<?php include("footer.php") ?>

<?php

//Function to check if the user has the rights to access the reports of the selected class/section
function canAccessReport($userID, $class, $section)
{
	$category = $_SESSION['admin'];
	if(strcasecmp($category,"School Admin")==0)
		$flag = true;
	elseif (strcasecmp($category,"Teacher")==0)
	{
		$flag   = false;
		$query  = "SELECT class, section FROM adepts_teacherClassMapping WHERE userID=".$userID;
		$result = mysql_query($query);
		while ($line=mysql_fetch_array($result))
		{
			if($class==$line['class'] && $section==$line['section'])
			{
				$flag = true;
				break;
			}
		}
	}
	else
		$flag = false;
	return $flag;
}
?>
<script type="text/javascript">

var get_fill_value=$("#display_report").val();
//alert("DR"+get_fill_value);
setSection('<?=$section?>');
if(get_fill_value==1)
$("#btnGo").click(); // To generate report if all parameter is filled.
</script>