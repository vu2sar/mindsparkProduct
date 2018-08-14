<?php
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
	error_reporting(0);
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	include("../userInterface/constants.php");
	include("header.php");
	include("../slave_connectivity.php");
	mysql_select_db("educatio_adepts") or die (mysql_errno());
	
	$userID     = $_SESSION['userID'];
	$schoolCode = $_SESSION['schoolCode'];
	$sessionID  = $_SESSION['sessionID'];
	$schoolCodeArray = array();
	$coteacherInterfaceFlag = 0;
	$query  = "SELECT childName, startDate, endDate, category, subcategory FROM adepts_userDetails WHERE userID=".$userID;
	$result = mysql_query($query) or die(mysql_error());
	$line   = mysql_fetch_array($result);
	$userName 	 =  $line[0];
	$startDate   =  $line[1];
	$endDate     =  $line[2];
	$category    =  $line[3];
	$subcategory =  $line[4];
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


	$oneDay = 24*60*60;
	$lastWeek = date("d-m-Y",strtotime("-1 day"));
	
	$class    = isset($_REQUEST['class'])?$_REQUEST['class']:"";
	$cls    = isset($_REQUEST['cls'])?$_REQUEST['cls']:"";
	$section  = isset($_REQUEST['section'])?$_REQUEST['section']:"";
	$tillDate = isset($_REQUEST['tillDate'])?$_REQUEST['tillDate']:date("Y-m-d",strtotime("-1 day"));
	$fromDate = isset($_REQUEST['fromDate'])?$_REQUEST['fromDate']:date("Y-m-d",strtotime("-7 day"));
	$chkTopicsAttempted = isset($_REQUEST['chkTopicsAttempted'])?1:0;
	//echo "Start Date ".$startDate;
	$selectedDate=getDateRangeValue($fromDate,$tillDate);	
	$query  = "SELECT schoolCode from adepts_rewardSystemPilot where flag=2";
	$result = mysql_query($query) or die(mysql_error());
	  while($line   = mysql_fetch_array($result))
	  {
	      $schoolCodeArray[] =$line[0];
	  }
	  if(in_array($schoolCode,  $schoolCodeArray) || empty($schoolCodeArray))
	  {          
	    $coteacherInterfaceFlag = 1;
	  } 
	  $learningUnitText = 	$coteacherInterfaceFlag == 1 ? 'Accuracy of Learning Units' : 'Topic Accuracy Report';
?>

<title>Class Level Report</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css" media="all">
<link href="css/myStudents.css?ver=7" rel="stylesheet" type="text/css" media="all">
<script type="text/javascript" src="libs/jquery-1.10.2.js"></script>
<script type="text/javascript" src="libs/intro.js"></script>
<script type="text/javascript" src="<?php echo HTML5_COMMON_LIB; ?>/jcanvas.new.min.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css" media="all" />
<link rel="stylesheet" href="css/chartsection.css?ver=3" media="all" />
<link rel="stylesheet" href="css/dashboard.css" media="all" />
<link rel="stylesheet" href="css/introjs.css" />
<script src="libs/jquery-ui-1.11.2.js"></script>
<!-- <script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script> -->
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="libs/tablesort.js"></script>
<script type="text/javascript" src="libs/idletimeout.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script type="text/javascript" src="libs/dashboardCommon.js"></script>      
<script type="text/javascript" src="libs/tiDashboard.js?ver=9"></script>
<script type="text/javascript" src="libs/topicReport.js?ver=6"></script>
<script type="text/javascript" src="libs/jquery.flot.js"></script>
<script type="text/javascript" src="libs/jquery.flot.pie.js"></script>

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

	function checkCookie()
	{
		if(document.getElementById('lstClass').value != "")
		{	
			var cookieWithSection = document.cookie.split(';');
			var sectionCookieArray = cookieWithSection[0].split('=');
			setSection(sectionCookieArray[1]);

			if(document.getElementById('dateRange').value == "")
			{
				var dateRangeCookieArray = cookieWithSection[1].split('=');

				$("#dateRange")[0].options[4].value = dateRangeCookieArray[1];

				var Dates = dateRangeCookieArray[1].split('~');

				$("#dateRange")[0].options[4].innerHTML = formatDate(Dates[0])+" - "+ formatDate(Dates[1])+ " (Change)";
				sDate = formatDate(Dates[0]);
				eDate = formatDate(Dates[1]);
				$("#fromDate").val(formatDate(Dates[0]));
				$("#toDate").val(formatDate(Dates[1]));
				$("#dateRange").val($("#dateRange")[0].options[4].value);
			}

			$("#generateReportButton").click();
		}
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
	$(document).on('click', '*', function(e) {
		if (!$(e.target).closest('.check').length && !$(e.target).closest('.checkCanvas').length) {							
			$(".arrow-white-side").css("display","none");	
			$(".arrow-right-side").css("display","none");		
		}
	});	
	$(document).ready(function(e) {
		//Fix for jquery-ui compatibility
		$.browser = {};
		$.browser.msie = false;

		initDashboard(); 
		var sDate= '<?=$fromDate?>';
		var eDate= '<?=$tillDate?>';

		//alert(sDate+eDate);

		checkCookie();
		setSection('<?=$section?>'); 

		if(document.URL.split('?').length > 1) {
			<?php if($cls != "") { ?>
		  		generateClassLevelReportRedirect(sDate,eDate);
		  <?php } ?>
		}

		$("#generateReportButton").click(function() {
		
			var dataString = $("#frmTeacherReport :input").serializeArray();
			var path;
			var cls = getValueFromSerializedArray("class", dataString);
			var sec = getValueFromSerializedArray("section", dataString);
			var startDate = (getValueFromSerializedArray("dateRange", dataString)).split("~")[0];
			var endDate = (getValueFromSerializedArray("dateRange", dataString)).split("~")[1];

			if(cls == "") {
				alert("Please select Class");
				return false;
			}

			initDashboardLoading();
			$("#dashboardReportTitle_span").text("Class Report for class " + cls + sec + " from " + formatDate(startDate) + " to " + formatDate(endDate));
			$("#dashboardReportTitle").show();
			var location='cls='+cls+'&section='+sec+'&tillDate='+endDate+'&fromDate='+startDate;
			var location2='class='+cls+'&section='+sec+'&tillDate='+endDate+'&fromDate='+startDate;
			path="myStudents.php?"+location2;
			pathone="studentWiseReport.php?"+location;
			$("#studentlink").attr("href",path);
			$("#individuallink").attr("href",pathone);
			if(cls>3)
			{
					$('#classLeaderBoardHyperlink').show();
					$('#classLeaderBoardHyperlink').attr({
					'data-class': cls,
					'data-section': sec,
				});
			}
			else
				$('#classLeaderBoardHyperlink').hide();
			
			getUsageSummaryAjax(dataString);
			getAccuracySummaryAjax(dataString);
			getAreasHandledByMSAjax(dataString, cls, sec);
			getTopicProgressSummaryAjax(dataString);
			getImpactSummaryAjax(dataString);
		}); 

		$("#classLevelReportPrintButton").click(function() {
			generatePrintableReport();
		});
		$(document).on('click', '#classLeaderBoardHyperlink', function() {
			if($(this).attr('data-section').indexOf(',')!=-1) {
				alert('Please select a single section to view its leaderboard.');
			} else {
				window.open('classLeaderBoard.php?class='+$(this).attr('data-class')+'&section='+$(this).attr('data-section'), '_blank');
			}
		});
		
		function setFromToDate() {
			var today_in_server = '<?=date("d-m-Y")?>';
			var yesterday = '<?=date("d-m-Y",strtotime("-1 day"))?>'
			var ninety_days_ago = '<?=date("d-m-Y", time()-90*24*60*60)?>';
			$( "#fromDate" ).val(yesterday).datepicker({
				dateFormat: 'dd-mm-yy',
				minDate: ninety_days_ago,
				maxDate: today_in_server,
				beforeShow: function(input, instance) { 
		            $(input).datepicker('setDate', yesterday);
		        },								
				onSelect: function( selectedDate ) {
					$( "#toDate" ).datepicker( "option", "minDate", selectedDate );
					if($('#toDate').val().replace(/^(\d{2})-(\d{2})-(\d{4})$/, '$3-$2-$1') < selectedDate.replace(/^(\d{2})-(\d{2})-(\d{4})$/, '$3-$2-$1'))
					$('#toDate').val(selectedDate);
				},
			});
			$( "#toDate" ).val(yesterday).datepicker({
				dateFormat: 'dd-mm-yy',
				minDate: yesterday,
				maxDate: today_in_server,
				beforeShow: function(input, instance) { 
		            $(input).datepicker('setDate', yesterday);
		        },				
				onSelect: function( selectedDate ) {
	        $( "#fromDate" ).datepicker( "option", "maxDate", selectedDate );
	      }
			});
		}
		setFromToDate();
		$("#fromDate").change(function (){				
	    	var fromDate = $("#fromDate").datepicker("getDate"); 
	    	var toDate = $("#toDate").datepicker("getDate"); 
	    	var dateLimit = new Date();
	    	dateLimit.setDate((dateLimit.getDate())-90);   	
	    	if(fromDate.length !=0)
	    	{
		    	var today = new Date();	    		    
		    	if(fromDate>today)
		    	{
		    		alert("Future dates are not allowed !!");
		    		setFromToDate();
		    		$("#fromDate").focus();
		    	}

		    	else if(fromDate < new Date(dateLimit.setHours(0, 0, 0, 0, 0))){
		    		alert("Dates less than 90 days are not allowed!!");		    		
	    			setFromToDate();    		
		    		$("#fromDate").focus();
		    	} 

	    		else if(new  Date(fromDate) > new Date(toDate))
	    		{
	    			alert("From date should come before To date");  	    			      			
	    			setFromToDate(); 
		    		$("#fromDate").focus();
	    		}	    	
	    		
	    	}
	    });
	    $("#toDate").change(function (){	    	    		   
	    	var toDate = $("#toDate").datepicker("getDate");
	    	var fromDate = $("#fromDate").datepicker("getDate");
	    	var today = new Date();	 
	    	var dateLimit = new Date();
	    	dateLimit.setDate((dateLimit.getDate())-90);   	 
	    	if(toDate.length !=0)
	    	{
		    	if(toDate>today){
		    		alert("Future dates are not allowed !!");
		    		setFromToDate();
		    		$("#toDate").focus();
		    	}
		    	else if(toDate < new Date(dateLimit.setHours(0, 0, 0, 0, 0))){
		    		alert("Dates less than 90 days are not allowed!!");
		    		setFromToDate(); 
		    		$("#toDate").focus();
		    	} 

	    		else if(new  Date(toDate) < new Date(fromDate))
	    		{
	    			alert("To date should come after from date");  	    			      				
	    			setFromToDate(); 
		    		$("#toDate").focus();
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
				    	$("#generateReportButton").click();
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

		$("#ajaxData").on("click",".downloadXL",function() { 
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
				$(".homeSchoolUsage").show();
				$("#rgQues").attr("colspan",13);
			}
			else
			{
				$(".homeSchoolUsage").hide();
				$("#rgQues").attr("colspan",7);
			}
		});
	});

</script>
</head>
<body class="translation" onLoad="load();" onResize="load()" onmousemove="reset_interval()" onclick="reset_interval()" onkeypress="reset_interval()" onscroll="reset_interval()">
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

	<div class="modal fade" id="learningUnit" tabindex="-1" role="dialog" aria-labelledby="simpleModal">
		<div class="modal-fade" onclick="closeLearningUnit()"></div>
	    <div class="modal-dialog" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h4 class="modal-title pull-left" id="myModalLabel-2"></h4>
	                <button type="button" class="btn btn-dialog pull-right" data-dismiss="modal" onclick="closeLearningUnit()"><span class="close">&times;</span>CLOSE</h4>
	            </div>

	            <div class="modal-body">
	            </div>
	            <div class="modal-footer">
	            </div>
	        </div><!-- modal-content -->
	    </div><!-- modal-dialog -->
	</div><!-- modal -->
	<div id="container">
		<div id="trailContainer">
			<div id="headerBar">
				<div id="classReportMenu" data-intro="Click here to get overall information about your class.">
					<div class="classReport"></div>
					<a href="#"><div class="pageText" >Class Report</div></a>
				</div>
				<div id="studentWiseUsageReportMenu">
					<div class="editDetails"></div>
					<?php 
			$pathhref='';
			$pathhref='myStudents.php?class='.$cls.'&section='.$section.'&tillDate='.$tillDate.'&fromDate='.$fromDate;
					?>
					<a id="studentlink" href="<?=$pathhref?>"><div class="pageText">Student-wise Usage Report</div></a>
				</div>
				<div id="individualBoard">
					<div class="noticeBoard"></div>
			<?php 
			$pathhref2='';
			$pathhref2='studentWiseReport.php?cls='.$cls.'&section='.$section.'&tillDate='.$tillDate.'&fromDate='.$fromDate;
			?>
					<a id="individuallink" href="<?=$pathhref2?>"><div class="pageText">Individual Student Report</div></a>
				</div>
			</div>
			<form id="frmTeacherReport" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
			<table id="topicDetails" data-intro="Select class, section and date range.">
				<td width="5%"><label for="lstClass">Class</label></td>
		        <td width="28%" style="border-right:1px solid #626161">
		            <select name="class" id="lstClass"  onchange="setSection('')" style="width:65%;">
					<?php 
						if(count($classArray)!=1)
							echo '<option value="">Select</option>';
						for($i=0; $i<count($classArray); $i++)	{ 
					?>
						<option value="<?=$classArray[$i]?>" <?php if($cls==$classArray[$i]) echo " selected";?>><?=$classArray[$i]?></option>
					<?php	}
						if(count($classArray) == 1) {
					?>
						<script type="text/javascript">
							$("#lstClass").val("<?=$classArray[0]?>");
						</script>
					<?php } ?>
					</select>
		        </td>
				<?php if($hasSections) { ?>
				<td width="6%" class="noSection"><label  id="lblSection" for="lstSection" style="margin-left:20px;">Section</label></td>
		        <td class="noSection" width="27%" style="border-right:1px solid #626161">
		            <select name="section" id="lstSection" style="width:65%;">
				</select>
		        </td>
				<?php }  ?>
				<td colspan="8" width="12%"> <label id="lblDateRange" for="dateRange" style="margin-left:20px;">Date Range</label></td>
				<td width="21%" data-intro="Choose the most relevant date range. We have made it easy for you to select the frequently used options, but feel free to customize the range." >
		      <select name="dateRange" id="dateRange" style="width:65%;">
						<option value="<?php echo getDateRangeValue(date("Y-m-d"), date("Y-m-d")); ?>">Today</option>
						<option value="<?php echo getDateRangeValue(date("Y-m-d", strtotime("-1 days")), date("Y-m-d", strtotime("-1 days"))); ?>">Yesterday</option>
						<option value="<?php echo getDateRangeValue(date("Y-m-d", strtotime("-7 days")), date("Y-m-d", strtotime("-1 days"))); ?>">Previous 7 days</option>
						<option value="<?php echo getDateRangeValue(date("Y-m-d", strtotime("-30 days")), date("Y-m-d", strtotime("-1 days"))); ?>">Previous 30 days</option>
						<option value="">Custom Range</option>
					</select>
					<input type="button" value="Go" id="generateReportButton" class="button" style="margin-left:20px;">
			</td>

			</table>
			<input type="hidden" name="schoolCode" id="schoolCode" value="<?=$schoolCode?>">
			<input type="hidden" name="userID" id="userID" value="<?=$userID?>">
			<input type="hidden" name="sessionID" id="sessionID" value="<?=$sessionID?>">
			<input type="hidden" name="coteacherInterfaceFlag" id="coteacherInterfaceFlag" value="<?=$coteacherInterfaceFlag?>">
			</form>
			<div id="dashboard" class="col-md-12 dashboard">
				<div id="dashboardReportTitle" class="dashboard-report-title">
					<table width="95%"><tr >
						<td class="intro-launch" onClick='introJs().start();' title="Help" style="cursor: help;" width="4%"></td>
						<td width="80%"><span id="dashboardReportTitle_span"></span><input id="classLevelReportPrintButton" style="margin-left: 1%;" type="button" value="Print"></input></td>
						<td align="right" width="16%">						
						<a href="#" id="classLeaderBoardHyperlink" data-class="<?=$cls?>" data-section="<?=$section?>" style="text-decoration:none;<?php if($cls<4){echo "display:none"; }?>" >
						<div id="classLeaderBoardIcon"></div>Class Leaderboard</a>						
						</td>
					</tr></table>
				</div>

				<div id="dashboardTopRow">
					<div id="overallUsageChart" class="col-md-3 dashboard-top-row donut-container" style="margin-left: 0px" data-intro="This chart allows you to see the overall usage of your class. We recommend that you talk to the children mentioned under highlights to ensure that they are on track.">
						<span id="overallUsageChart_heading" class="section-heading chart-heading">Overall Usage: <span id="overallUsageChart_classAvg"> </span></span>
						<div id="overallUsageChart_loading" class="dashboard-loading">
							<p>This may take a few minutes</p> 
						</div>
<!-- 						old
						<div class="canvas-chart">
							<canvas id="usagePiChart" width="150" height="150"></canvas>
						</div>

						<div class="panel-body">
						    <div id="graph1" class="chart"></div>
						</div>

						end of old -->
						<div class="canvas-chart" data-intro="Beware of the red! It means that the overall usage of the class is low.">
<!-- 							<img id="usagePiChartImg" style="  position: absolute; width: 220px; height: 220px; left: 48px;"></img> -->
							<div id="usagePiChart" class="chart" style="width:220px; height:320px"></div>
						</div>

						<div id="overallUsageChart_highlights">
							<span class="highlights-text">Highlights</span>
							
							<div id="overallUsageChart_highlightsLowUsage">
								<span id="overallUsageChart_highlightsLowUsageTitle" class="section-title">Lowest Usage</span>
								<span id="overallUsageChart_highlightsLowUsageNames" class="user-names"></span>
							</div>

							<div id="overallUsageChart_highlightsLowAccuracy">
								<span id="overallUsageChart_highlightsLowAccuracyTitle" class="section-title">Lowest Accuracy</span>
								<span id="overallUsageChart_highlightsLowAccuracyNames" class="user-names"></span>
							</div>

							<div id="overallUsageChart_highlightsNumerousFailures">
								<span id="overallUsageChart_highlightsNumerousFailuresTitle" class="section-title">Numerous Failures in Learning Units</span>
								<span id="overallUsageChart_highlightsNumerousFailuresNames" class="user-names"></span>
							</div>

							<div id="overallUsageChart_highlightsAllTopicsComplete">
								<span id="overallUsageChart_highlightsAllTopicsCompleteTitle" class="section-title">Children with all topics complete</span>
								<span id="overallUsageChart_highlightsAllTopicsCompleteNames" class="user-names"></span>
							</div>
						</div>
						<div id="overallUsageChart_more">
							<span class="highlights-text" style="margin-top: 20px">More</span>
							<div id="overallUsageChart_moreLinks">
								<span class="section-title"><a id="overallUsageChart_studentWiseReportUrl" href="myStudents.php" target="_blank">Student-wise Usage Report</a></span>
							</div>

						</div>

					</div>

					<div id="overallAccuracyChart" class="col-md-3 dashboard-top-row donut-container" data-intro="Similarly, this chart allows you to see how topics are doing overall. Focus on the highlights and see more details in the Topic Accuracy Report.">
						<span id="overallAccuracyChart_heading" class="section-heading chart-heading">Overall Accuracy: <span id="overallAccuracyChart_classAvg"> </span></span>
						<div id="overallAccuracyChart_loading" class="dashboard-loading">
							<p>This may take a few minutes</p> 
						</div>
						<div class="canvas-chart">
							<div id="accuracyPiChart" class="chart" style="width:220px; height:320px"></div>
						</div>
						<div id="overallAccuracyChart_highlights">
							<span class="highlights-text">Highlights</span>
							
							<div id="overallAccuracyChart_highlightsGreatAccuracy">
								<span id="overallAccuracyChart_highlightsGreatAccuracyTitle" class="section-title">Highest Accuracy (over 80%)</span>
								<span id="overallAccuracyChart_highlightsGreatAccuracyNames" class="user-names"></span>
							</div>

							<div id="overallAccuracyChart_highlightsLowAccuracy">
								<span id="overallAccuracyChart_highlightsLowAccuracyTitle" class="section-title">Lowest Accuracy (less than 40%)</span>
								<span id="overallAccuracyChart_highlightsLowAccuracyNames" class="user-names"></span>
							</div>

							<div id="overallAccuracyChart_highlightsMisconceptionsIdentified">
								<span id="overallAccuracyChart_highlightsMisconceptionsIdentifiedTitle" class="section-title">Misconceptions identified</span>
								<span id="overallAccuracyChart_highlightsMisconceptionsIdentifiedNames" class="user-names"></span>
							</div>

						</div>
						<div id="overallAccuracyChart_more">
							<span class="highlights-text" style="margin-top: 20px">More</span>
							<div id="overallAccuracyChart_moreLinks">
								<span id="overallAccuracyChart_topicWiseReportUrl" class="section-title">
									<a href="topicReport.php" target="_blank"><?php echo $learningUnitText;?></a>
								</span>
							</div>

						</div>
					</div>

					<div id="topicProgressContainer" class="dashboard-top-row" style="width: 420px">
						<div id="topicProgressChart" class="col-md-3" style="width: 420px" data-intro="Quickly check the topics with maximum progress and use the information to guide your class effectively.">
						<span id="topicProgressChart_heading" class="section-heading chart-heading" style="margin-bottom: 10px">Topic Progress</span>
							<div id="topicProgressChart_loading" class="dashboard-loading">
								<p>This may take a few minutes</p> 
							</div>
							<div id="topicProgressChart_list">
								<div id="topicProgressChart_noTopics" style="text-align: center; width:400px; height:100; padding-top: 20px">No data to display</div>
								<div id="topicProgressChart_topicTitle1" class="topic-progress-chart" style="padding-top:10px;">
									<span id="topicProgressChart_topicDesc1"></span>
								</div>
								<canvas id="topicProgressChart_canvas1" width="400" height="50" class="topic-progress-chart base2" style="margin-top: 10px;"></canvas>

								<div id="topicProgressChart_topicTitle2" class="topic-progress-chart" style="padding-top:10px;">
									<span id="topicProgressChart_topicDesc2"></span>
								</div>
								<canvas id="topicProgressChart_canvas2" width="400" height="50" class="topic-progress-chart base2" style="margin-top: 10px;"></canvas>

								<div id="topicProgressChart_topicTitle3" class="topic-progress-chart" style="padding-top:10px;">
									<span id="topicProgressChart_topicDesc3"></span>
								</div>
								<canvas id="topicProgressChart_canvas3" width="400" height="50" class="topic-progress-chart base2" style="margin-top: 10px;"></canvas>

								<div style="background-color: #F5F5F5;">
									<canvas id="topicProgressScale" width="400" height="50" class="topic-progress-chart base2" style="margin-top: 10px;"></canvas>
									<!-- <img src="images/topic-progress-scale-label-400.png" width="420" height="50" /> -->
								</div>
							</div>
						</div>

						<div id="areasHandledByMS" class="col-md-3" style="width: 420px; margin-top: 20px" data-intro="Navigate to the common wrong answer report and check out the activities and remedials that have helped your class.">							
								<span id="areasHandledByMS_heading" class="section-heading chart-heading">Areas Handled by Mindspark</span>
								<div id="areasHandledByMS_loading" class="dashboard-loading">
									<p>This may take a few minutes</p> 
								</div>
								<div id="areasHandledByMS_list">
									<br />
									<ul style="margin-top: 20px">
										<li>Check the <a id="areasHandledByMS_commonAnswerReportUrl" href="cwa.php" target="_blank">Common Wrong Answer report </a> to see the most problematic questions in your class. </li>
										<br />
										<li id="areasHandledByMS_misconception"><a id="areasHandledByMS_remedialItemUrl" href="" target="_blank">This </a> activity helped children resolve misconceptions in <span id="areasHandledByMS_remedialItemDesc">"Remedial Item Desc"</span> in the topic <span id="areasHandledByMS_topicName">"topic name"</span></li>
										<br /> 
										<li id="areasHandledByMS_activity"><span id="areasHandledByMS_mostAttemptedActivity">"activity name"</span> was the most attempted activity by children of <span id="areasHandledByMS_classSection">"class" "section"</span>. <a id="areasHandledByMS_mostAttemptedActivityUrl" href="" target="_blank" >Play it.</a></li>
									</ul>
								</div>
							<!-- Building Fluency through Daily Practice section start -->
								<div id='buildingFluencyDiv'>	
									<div id="buildingFluencyByDP_heading" class="section-heading chart-heading">Building Fluency through Daily Practice</div>
									<div id="buildingFluencyByDP_loading" class="dashboard-loading">
										<p>This may take a few minutes</p> 
									</div>
									<div id="buildingFluencyByDP_list">
										<br/>
										<ul style="margin-top: 20px">
										</ul>
									</div>
								</div>
							<!-- Building Fluency through Daily Practice section end -->	
						</div>
					</div>
				</div>
<!-- akkada -->
				<div id="dashboardBottomRow" class="col-md-9 section-head dashboard-bottom-row" data-intro="Mindspark in Numbers section allows you to see the number of questions, activities, misconceptions and higher-level instances.">
					<div id="dashboardBottomRow_loading" class="dashboard-loading">
						<p>This may take a few minutes</p> 
					</div>
					<div id="impactSummary_questions" class="impact-summary-element">
						<div class="impact-summary-icon-container">
							<div id="impactSummary_questionsIcon" class="impact-summary-icon">
								<img width="65" height="65" src="images/q.svg"></img>
							</div>
							<div id="impactSummary_questionsValues" class="impact-summary-values">
							<span id="impactSummary_questionsTotal"></span>
						</div>
						<div id="impactSummary_questionsTitle" class="impact-summary-title">Questions</div>
					</div>

					</div>

					<div id="impactSummary_misconceptions" class="impact-summary-element">
						<div class="impact-summary-icon-container">
							<div id="impactSummary_misconceptionsIcon" class="impact-summary-icon">
								<img width="65" height="65" src="images/flag4.svg"></img>
							</div>
							<div id="impactSummary_misconceptionsValues" class="impact-summary-values">
								<span id="impactSummary_misconceptionsTotal"></span>
							</div>
							<div id="impactSummary_misconceptionsTitle" class="impact-summary-title">Misconceptions remediated</div>
						</div>
					</div>

					<div id="impactSummary_higherLevel" class="impact-summary-element">
						<div class="impact-summary-icon-container">
							<div id="impactSummary_higherLevelIcon" class="impact-summary-icon">
								<img width="65" height="65" src="images/favorite5.svg"></img>
							</div>
							<div id="impactSummary_higherLevelValues" class="impact-summary-values">
								<span id="impactSummary_higherLevelTotal"></span>
							</div>
							<div id="impactSummary_higherLevelTitle" class="impact-summary-title">Higher Level reached</div>
						</div>
					</div>

					<div id="impactSummary_activities" class="impact-summary-element">
						<div class="impact-summary-icon-container">
							<div id="impactSummary_activitiesIcon" class="impact-summary-icon">
								<img width="65" height="65" src="images/thought.svg"></img>
							</div>
							<div id="impactSummary_activitiesValues" class="impact-summary-values">
								<span id="impactSummary_activitiesTotal"></span>
							</div>
							<div id="impactSummary_activitiesTitle" class="impact-summary-title">Activities attempted</div>
						</div>
					</div>

				</div>
				<div id="printableReports" style="display: none">
					<div id="printableReports_section2" >
						<div id="zeroUsageTableDiv">
							<h1>Students who have not logged in the specified time period</h1>
							<table id="zeroUsageTable">
							  <thead id="zeroUsageTable_thead"></thead>
							  <tbody id="zeroUsageTable_tbody"></tbody>
							</table>
						</div>

						<div id="higherLevelTableDiv">
							<h1>Students who are at a higher level in one or more topics</h1>
							<table id="higherLevelTable">
							  <thead id="higherLevelTable_thead"></thead>
							  <tbody id="higherLevelTable_tbody"></tbody>
							</table>
						</div>
						
						<div id="printableReports_topicReport">
							<h1><span id="printableReports_topicName"></span></h1>
							<table id="topicReportTable">
							  <thead id="topicReportTable_thead"></thead>
							  <tbody id="topicReportTable_tbody"></tbody>
							</table>
						</div>
					</div>
					<div id="section3">
						<h1><span id="printableReports_studentUsageReport">Student-wise Usage Report</span></h1>
						<div id="section3_reportContainer"></div>
					</div>
					<div id="section4">
						<div id="printableReports_accuracyReport">
							<h1><span id="printableReports_accuracyReportHeading"></span></h1>
							<table id="accuracyReportTable">
							  <thead id="accuracyReportTable_thead"></thead>
							  <tbody id="accuracyReportTable_tbody"></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

			<div id="ajaxData" align="center"></div>
			<form name='frmExcel' target="" action="export.php" method="POST">
				<input type="hidden" name="content" id="contentXL" value=''>
			</form>
		</div>
	</div>
	
<?php include("footer.php") ?>

<div id="dialog-form" title="Select Date Range" style="display: none"> 
  <form>
    <fieldset>
    <div id="dateRangeSelector" >
    	<label for="fromDate">From</label>
    	<input type="text" id="fromDate" name="fromDate" onKeyDown="return DateFormat(this, event.keyCode)" maxlength="10" >
    	<label for="toDate" style="margin-left: 4px;">To</label>
    	<input type="text" id="toDate" name="toDate" onKeyDown="return DateFormat(this, event.keyCode)" maxlength="10">
    </div>
      <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
    </fieldset>
  </form>
</div>
<div id="printLoading" width="300" height="300" style="display: none; padding-left: 75px; padding-top: 50px;">
	<img src="images/fetching_data.gif">
	<p>This may take a few minutes</p> 
</div>

<?php

function getDateRangeValue($startDate, $endDate) {
	return $startDate . "~" . $endDate;
}

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