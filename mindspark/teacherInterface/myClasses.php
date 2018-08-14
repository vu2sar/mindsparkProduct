<?php 
	include("../userInterface/constants.php");	
	include("header.php");
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
	include("functions/functions.php");
	include("classes/testTeacherIDs.php");
	include("../functions/functions.php");
	include_once("../userInterface/classes/clsTopicProgress.php");
	include_once("../userInterface/classes/clsTeacherTopic.php");

	//error_reporting(E_ALL);
	$userID     = $_SESSION['userID'];
	$schoolCode = isset($_SESSION['schoolCode'])?$_SESSION['schoolCode']:"";
	$user   = new User($userID);
	$md5UserID = md5($userID);
	$category	=	$user->category;
	$subcategory =	$user->subcategory;
	$teacherName = $user->childName;
	$durationPopup = 0;
	$coteacherInterfaceFlag = 0;
	$schoolCodeArray = array();
	if(in_array($schoolCode, array('3332611','524522','23246','206357','376207','34736','2387554')))
	{				
		if(date("2016-09-17") >= date("Y-m-d"))
		$durationPopup = 1;
	}		
	if (isset($_POST['action'])){
	if($_POST['action'] == "savepriority")
	{
		$idlist = $_POST['ttlist'];
		$data = explode(",",rtrim($idlist, ","));

		$cls = $_POST['cls'];
		$section = $_POST['section'];
		$sectionList = explode(",",rtrim($section, ","));
		$i = 1;
		
		$sessionID = $_SESSION['sessionID'];
		$trackQuery = "INSERT INTO trackingTeacherInterface (userID, sessionID, pageID, lastmodified) values ($userID,$sessionID,74,now())";
			mysql_query($trackQuery) or die(mysql_error());

		foreach ($data as $val)
		{
			foreach ($sectionList as $sec)
			{
				$query = "update adepts_teacherTopicActivation set priority = $i where teacherTopicCode = '$val' AND schoolCode=$schoolCode AND class = $cls AND section = '$sec' AND deactivationdate = '0000-00-00' ";
				mysql_query($query) or die(mysql_error());
				$i++;
			}
		}
	}
	}
	if(strcasecmp($category,"Teacher")==0 || strcasecmp($category,"School Admin")==0) {
		$query = "SELECT schoolname FROM educatio_educat.schools WHERE schoolno=".$schoolCode;
		$r = mysql_query($query);
		$l = mysql_fetch_array($r);
		$schoolName = $l[0];
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
				   WHERE    category='STUDENT' AND subcategory='Home Center' AND schoolCode=$schoolCode AND enabled=1 AND endDate>=curdate() AND subjects like '%".SUBJECTNO."%'
				   GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
	}
	else
	{
		echo "You are not authorised to access this page!";
		exit;
	}
	$classArray = $sectionArray = $topicArray = array();
	$hasSections = false;
	$checkOtherGrades=0;
	$checkGrade1=0;
	$checkGrade2=0;
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
	
		if (strcasecmp($category,"Home Center Admin")==0)
		{
			$query = "SELECT b.class, a.teacherTopicCode, a.teacherTopicDesc
					  FROM   adepts_teacherTopicMaster a, adepts_teacherTopicActivation b
					  WHERE  a.teacherTopicCode=b.teacherTopicCode AND subjectno=".SUBJECTNO." AND a.live=1 AND b.schoolCode=$schoolCode AND b.class=".$line[0];
		}
		else
		{
			$query = "SELECT b.class, a.teacherTopicCode, a.teacherTopicDesc
					  FROM   adepts_teacherTopicMaster a, adepts_teacherTopicActivation b
					  WHERE  a.teacherTopicCode=b.teacherTopicCode AND subjectno=".SUBJECTNO." AND a.live=1 AND b.schoolCode=$schoolCode AND b.class=".$line[0];
		}
	
		if($sectionStr!="")
			$query .= " AND section in ($sectionStr)";
		$query .= " ORDER BY teacherTopicDesc";
		$topic_result = mysql_query($query) or die(mysql_error());
		while ($topic_line=mysql_fetch_array($topic_result))
		{
			$topicArray[$topic_line['class']][$topic_line['teacherTopicCode']] = $topic_line['teacherTopicDesc'];
		}
		$sectionArray[$line[0]] = $sectionStr;
	}
	$searchTerm = isset($_REQUEST['searchingTerm'])?$_REQUEST['searchingTerm']:"";
	$class	=	isset($_REQUEST['cls'])?$_REQUEST['cls']:"";
	$openTab	=	isset($_REQUEST['openTab'])?$_REQUEST['openTab']:"";
	
	if(isset($_REQUEST['checkflag']) && $_REQUEST['checkflag'] == 1)
		$checkflag	=	isset($_REQUEST['checkflag'])?$_REQUEST['checkflag']:0;

	$section	=	isset($_REQUEST['section'])?$_REQUEST['section']:"";
	$masterTopic	=	"";
	if(isset($_REQUEST['masterTopic']))
	{
		$masterTopic	=	$_REQUEST['masterTopic'];
	}
	
	//$defaultFlow = "MS"; -was already here

	$defaultFlow = getDefaultFlowForTheSchool($schoolCode);			//  $allowDeactivatedTopicsAtHome is not used anywhere on this page --shams
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
	if($_REQUEST['cls'])
	{
		$savedMappingArray = getSavedMappingOfTT($schoolCode,$class, $section);
		$topicsFollowingOldFlow = getTopicsActivatedInOldFlow($schoolCode, $class, $section);
		$customizedTopicsArray = getCustomTTs($schoolCode, $class, $section, $masterTopic);
		/*$allowDeactivatedTopicsAtHome = 0;
		$flow_query  = "SELECT defaultFlow, allowDeactivatedTopicsAtHome FROM adepts_schoolRegistration WHERE school_code=$schoolCode";
		$flow_result = mysql_query($flow_query);
		if($flow_line=mysql_fetch_array($flow_result))
		{
			$defaultFlow = $flow_line[0];
			$allowDeactivatedTopicsAtHome = $flow_line[1];
		}*/
	
		
		
		$myteacherTopicActivated	=	getTTsActivatedN($class, $schoolCode, $section,$masterTopic,$mode="priority",0,$coteacherInterfaceFlag);		
		$teacherTopicActivatedAll	=	getTTsActivatedN($class, $schoolCode, $section,$masterTopic, $mode="all",$defaultFlow,$coteacherInterfaceFlag);
		$teacherTopicActivatedAllSearch	=	getTTsActivatedN($class, $schoolCode, $section,"", $mode="all",$defaultFlow,$coteacherInterfaceFlag);
		$currentActivated = getCurrentTTsActivated($class, $schoolCode, $section);
		$userDetails	=	getStudentDetails($class, $schoolCode, $section);
		$userIDs	=	array_keys($userDetails);
		$teacherTopicCodes	=	array_keys($teacherTopicActivatedAll);
		
		$teacherTopicNeverActivated	=	teacherTopicNeverActivated($schoolCode,$cls,$section,$masterTopic,$defaultFlow,$customizedTopicsArray,$savedMappingArray, $teacherName,$coteacherInterfaceFlag);		
		$teacherTopicNeverActivatedSearch	=	teacherTopicNeverActivated($schoolCode,$cls,$section,"",$defaultFlow,$customizedTopicsArray,$savedMappingArray, $teacherName,$coteacherInterfaceFlag);
		//include("../slave_connectivity.php");
		$ttProgress	=	getTeacherTopicProgress($teacherTopicCodes,$userIDs,$class);
		$studentAttempted = getStudentAttempted($teacherTopicCodes,$userIDs,$class);
	}
		
	$childClass = $class;
	/*echo "<pre>";
		    print_r($teacherTopicNeverActivated);
		    print_r($customizedTopicsArray);
		       echo "</pre>";*/
	  
?>


	<title>My Topics</title>

	<!--<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css"> -->
	<link href="css/common.css?version=1.1" rel="stylesheet" type="text/css">
	<link href="css/myClasses.css?ver=11" rel="stylesheet" type="text/css">
	<!-- <script src="libs/jquery-1.10.2.js"></script> -->
	<script src="libs/jquery-ui-1.11.2.js"></script>
	<script src="libs/touchpunch.js"></script>
	<script type="text/javascript" src="libs/i18next.js"></script>
	<script type="text/javascript" src="libs/translation.js"></script>
	<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
	<script type="text/javascript" src="../userInterface/libs/prompt.js"></script>
	<link href="../userInterface/css/prompt.css" rel="stylesheet" type="text/css">
	<style>
		.bulkActivateDeactivatePrompt{
			display: none;
		    position: absolute;
		    width: 390px;
		    background-color: #30302F;
		    padding: 5px;
		    font-size: 1.1em;
		    line-height: 1.3em;
		    text-align: left;
		    min-height: 122px;
		    left: -570px;
	        top: -70px;
	        z-index: 999;
	        font-family: Calibri, Tahoma, sans-serif;
		} 
		.bulkActivateDeactivatePrompt:before {
		    border-top: 10px solid transparent;
		    border-bottom: 10px solid transparent;
		    content: '';
		    position: absolute;
		    right: -10px;
		    width: 0px;
		    height: 0px;
		    border-left: 10px solid #30302F;
		}
		.bulkActivateDeactivatePrompt:not(.BAPromptShift):before {
			top: 25px;
		}
		.bulkActivateDeactivatePrompt.BAPromptShift:before {
			bottom: 95px;
		}
		#searchResults .bulkActivateDeactivatePrompt{
			background-color: rgba(120, 140, 150, 1.0);
		}
		#searchResults .bulkActivateDeactivatePrompt:before{
			border-left: 10px solid rgba(120, 140, 150, 1.0);
		}
		.bulkActivateDeactivatePrompt .bulkADMain{
			line-height: 1.3em;
			margin-bottom: 5px;
			padding: 5px;
		}
		.bulkActivateDeactivatePrompt .bulkADMain .section-div{
			margin-top: 5px;
		}
		.bulkActivateDeactivatePrompt .bulkADHelp{
			font-style: italic;
			font-size: 0.9em;
		}
		.bulkActivateDeactivatePrompt .bulkADActionButton{
			text-align: right;
			margin-right: 10px;
		}
		.bulkActivateDeactivatePrompt .bulkADActionButton .bulkButton{
			text-decoration: none;
			color: #9FCB50;
	    	font-weight: bold;
		}
		.bulkADActionButton .bulkButton.white{
			color: #fff;
		}
		.bulkActivateDeactivatePrompt .durationFromTo{
		color: #9FCB50;
		margin-top: 5px;
		float: left;
        margin-bottom: 5px;  
        width: 100%;	
	}
	.bulkActivateDeactivatePrompt #notCovered{
		float: left;
	}

	.bulkActivateDeactivatePrompt .notCovering
	{
		color: #f26722;
		float: left;
		margin-top: 2px;
	}
	.bulkActivateDeactivatePrompt .durationTo
	{
		padding-left: 5px;
	}
	.bulkActivateDeactivatePrompt .durationBorder
	{
		border: 2px solid red;		
    	border-style: inset;
	}
	.bulkActivateDeactivatePrompt .durationHelp
	{
		border: 2px solid #9FCB50;
	    border-radius: 10px;
	    width: 15px;   
	    text-align: center;
	    display: inline-block;
	    margin-left: 10px;
	}
		.section-div label input[type="checkbox"]{
			display: none;
		}
		.section-div label{
			margin: 3px;
			text-align: center;
			cursor: pointer;
			display: inline-block;
		}
		.section-div label span{
		    text-align:center;
		    padding:3px 3px;
		    display:block;
		    background-color:#fff;
		    color:#000;
		    font-weight: bold;
		    min-width: 40px;
		}
		.section-div input:checked + span {
		    background-color:#9FCB50;
		    color:#000;
		}
		.section-div input:disabled + span {
		    color:#AAA;
		}
		.topicDateDiv span{
			color:#9FCB50;
		}
		.topicDateDiv input.dateField{
			width: 100px;
		    text-align: center;
		    margin-right: 10px;
		    cursor: pointer;
		}
		.topicDateDiv label.topicDateHelp{
			display: inline-block;
		    width: 18px;
		    height: 18px;
		    border-radius: 20px;
		    border: 2px solid white;
		    text-align: center;
		    line-height: 20px;
		}
		.topicDateDiv input.notCoveredTopic{
			vertical-align: middle;
		    margin-top: 0;
		    margin-bottom: 0;
		}
	</style>
<script>
	var langType = '<?=$language;?>';
	var gradeArray   = new Array();
	var sectionArray = {};
	var topicCodeArray   = new Array();
	var topicArray   = new Array();
	
	
	<?php
		for($i=0; $i<count($classArray); $i++)
		{
			echo "gradeArray.push($classArray[$i]);\r\n";
			echo "sectionArray[$classArray[$i]] = new Array(".$sectionArray[$classArray[$i]].");\r\n";
		}
	?>
	
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
		if(($("div#searchResults").children().length)>1)
		$('#noTopics').css("display","none");
		else
		$('#noTopics').css("display","block");
		var category = '<?=$category;?>';
		var subcategory = '<?=$subcategory;?>';
		
		if(category.localeCompare('School Admin')==0 && subcategory.localeCompare('All')==0)
			$('.doAsStudent').css('display','none');
		else
			$('.doAsStudent').css('display','block');
		if($('#sortable2').length>0)
			$('#sortable2').css('height', ($(window).height()-$('#sortable2').offset().top-$('#copyright').height()-25)+'px');
	}
	function openActivateTab(){
		$(".arrow-black-side").css("visibility","visible");
	}
	function submitCheckBox()
	{
		setTryingToUnload();
		$("#generate").click();
	}
	function activateLimitOver()
	{		
		//$("#frmMain").submit();
		var prompts=new Prompt({
			text:'Mindspark does not allow more than 15 topics to be active at a time. <a href="<?=WHATSNEW?>helpManual/Too_many_active_topics_in_a_school_is_hazardous_to_a_child_s_health.pdf" target="_blank">Click here</a> to know why.',
			type:'alert',
			label1:'Deactivate some topics',
			func1:function(){
				$("#actTopicCircle1").click();
				$("#prmptContainer_activateLimitOver").remove();
				showbutton();
			},
			promptId:"activateLimitOver"
		});
	}
	function topicClassDifference(childClass,classRange)
	{
		var prompts=new Prompt({
			text:'This topic is meant for grade(s) '+classRange+'. You can activate this topic for your grade by customising it. <a href="<?=WHATSNEW?>helpManual/Topics_not_recommended_for_the_current_grade_note.pdf" target="_blank">Click here</a> to know why.',
			type:'alert',
			label1:'Activate other topics',
			func1:function(){
				$("#prmptContainer_topicClassDifference").remove();
			},
			promptId:"topicClassDifference"
		});
	}
	$(document).ready(function(e) {
		$("#customConfirm").hide();

		<?php
			if($openTab==1 || $openTab==""){
		?>
			$("#1").addClass("textRed");
			$("#actTopicCircle1").addClass("red");
			$(".questionContainer").hide();
			$("#activatedTopic").show();
			$("#generatediv").show();
			$("#openTab").attr("value","1");
		<?php
			}else if($openTab==2){
		?>
			$("#2").addClass("textRed");
			$("#actTopicCircle2").addClass("red");
			$(".questionContainer").hide();
			$("#activateTopicAll").show();
			$("#openTab").attr("value","2");
		<?php
			}else if($openTab==3){
		?>
			$("#3").addClass("textRed");
			$("#actTopicCircle3").addClass("red");
			$("#openTab").attr("value","3");
		<?php
			}
		?>
		/*$("body").click(function() {
			$(".arrow-black-side").css("visibility","hidden");	
		});*/
		if(navigator.userAgent.indexOf("Android") != -1)
		{			
		    $(".clickMe").css({"padding-top":"6px","padding-bottom":"0px"});		    
		}
		$(".actionsTab").click(function() {
			$(".arrow-black-side").css("visibility","hidden");
			$("."+$(this).attr("id")).css("visibility","visible");
		});
		$(".coTeacherImg").click(function() {
			$(".arrow-white-side").css("visibility","hidden");
			$("."+$(this).attr("id")).css("visibility","visible");
		});
		$('#container').on('click', '*', function(e) {
			if($('.bulkActivateDeactivatePrompt').is(':visible')){
				if(!$(e.target).closest('.bulkActivateDeactivatePrompt').length){
					$('.bulkActivateDeactivatePrompt').hide();
					$(".arrow-black-side").css("visibility","hidden");		
					$(".arrow-white-side").css("visibility","hidden");		
					e.stopPropagation();
				}
			}
			else{
				if (!$(e.target).closest('.actionsTab').length && $(e.target).attr('rel')!='openBulkPopup' && !$(e.target).closest('.coTeacherImg').length) {
					$('.bulkActivateDeactivatePrompt').hide();
					$(".arrow-black-side").css("visibility","hidden");
					$(".arrow-white-side").css("visibility","hidden")
					e.stopPropagation();
				}
				else if ($(e.target).attr('rel')=='openBulkPopup'){
					e.stopPropagation();	
				}
			}
		});
		$('#searchSection').on('click', function(e) {
			if($('.bulkActivateDeactivatePrompt').is(':visible')){
				if(!$(e.target).closest('.bulkActivateDeactivatePrompt').length){
					$('.bulkActivateDeactivatePrompt').hide();
					$(".arrow-black-side").css("visibility","hidden");	
					$(".arrow-white-side").css("visibility","hidden")	
					e.stopPropagation();
				}
			}
			else{
				if (!$(e.target).closest('.actionsTab').length && $(e.target).attr('rel')!='openBulkPopup' && !$(e.target).closest('.coTeacherImg').length) {
					$('.bulkActivateDeactivatePrompt').hide();
					$(".arrow-black-side").css("visibility","hidden");
					$(".arrow-white-side").css("visibility","hidden")
					e.stopPropagation();
				}
				else if ($(e.target).attr('rel')=='openBulkPopup'){
					e.stopPropagation();	
				}
			}
		});
		$(".smallCircle").click(function() {
			$(".questionContainer").hide();
			$("#"+$(this).closest("td").attr("class")).show();
			$(".smallCircle").removeClass("red");
			$(".pointer").removeClass("textRed");
			$(this).addClass("red");
			$(this).next().addClass("textRed");
			$("#openTab").attr("value",$(this).next().attr("id"));
			if($("#"+$(this).closest("td").attr("class")).css("display")=="none" || !$("#"+$(this).closest("td").attr("class")).css("display")){
				$("#topicActivated").show();
			}else{
				$("#topicActivated").hide();
			}
			if($(this).attr('id')=="actTopicCircle1")
				showbutton();
		});
		$(".pointer").click(function() {
			$(".questionContainer").hide();
			$("#"+$(this).closest("td").attr("class")).show();
			$(".smallCircle").removeClass("red");
			$(".pointer").removeClass("textRed");
			$(this).addClass("textRed");
			$(this).prev().addClass("red");
			$("#openTab").attr("value",$(this).attr("id"));
			if($("#"+$(this).closest("td").attr("class")).css("display")=="none" || !$("#"+$(this).closest("td").attr("class")).css("display")){
				$("#topicActivated").show();
			}else{
				$("#topicActivated").hide();
			}
		});
		if($(".questionContainer").css("display")=="none"){
				$("#topicActivated").show();
			}else{
				$("#topicActivated").hide();
			}
		setSection('<?=$section?>');
		
		/*if(($('#activatedTopic').is(':visible') || $('#activateTopics').is(':visible') || $('#activateTopicAll').is(':visible')))
		{
			$('#searchClick').css('display','block');
		}*/
		
		if($('.pagingTable').is(':visible') )
		{
			$('#searchClick').css('display','block');
		}

		if($( "#masterTopic" ).val() != '')
		{
			//$("#generatedragdiv").css("display","none");
			$("#alertMessage").attr('disabled','disabled');
			$('#alertMessage').attr('title', 'You can assign priority to topics only when the master topic field is set to all');
		}

		if (!$("#1" ).hasClass( "pointer textRed" )) {
			$("#generatedragdiv").css("display","none");
		}


		$('.bulkActivateDeactivatePrompt').delegate('.selectAllSections','change',function(){
	        if(this.checked){
	            $(this).parent().parent().find('.checkboxSection:not(:disabled)').each(function(){
	                this.checked = true;
	            });
	        }else{
	             $(this).parent().parent().find('.checkboxSection:not(:disabled)').each(function(){
	                this.checked = false;
	            });
	        }
	    });
	    
	    $('.bulkActivateDeactivatePrompt').delegate('.checkboxSection:not(:disabled)','change',function(){
	        if($(this).parent().parent().find('.checkboxSection:checked').length == $('.checkboxSection:not(:disabled)').length){
	            $(this).parent().parent().find('.selectAllSections').prop('checked',true);
	        }else{
	            $(this).parent().parent().find('.selectAllSections').prop('checked',false);
	        }
	    });
	    $('.bulkActivateDeactivatePrompt').delegate('.notCoveredTopic','change',function(){
	    	if ($(this).is(":checked"))
	    		$(this).closest('.topicDateDiv').find('.dateField').attr('disabled','disabled');
	    	else
	    		$(this).closest('.topicDateDiv').find('.dateField').removeAttr('disabled');
	    });
	    $('#fromDate,#toDate').live('focus', function()
		  {		  
		  		 $("#fromDate").datepicker({
					dateFormat: 'dd-mm-yy',
					minDate: '-1Y',
					maxDate: '+1Y',					
					onSelect: function( selectedDate ) {
						$("#toDate").datepicker( "option", "minDate", selectedDate );					
						$("#fromDate").removeClass('durationBorder');					
					},
				});	
				$( "#toDate" ).datepicker({
					dateFormat: 'dd-mm-yy',					
					maxDate: '+1Y',
					minDate: '-1Y',						
					onSelect: function( selectedDate ) {
					$("#toDate").removeClass('durationBorder');
				}
				});	
		  });	   	
	});
	function checkCovering(this1)
    {
    	var ADPromptCheck = $(this1).parent();
    	var fromDate = $(ADPromptCheck).find('#fromDate');
    	var toDate = $(ADPromptCheck).find('#toDate');
    	if($(ADPromptCheck).find("#notCovered").is(':checked'))
    	{

    		fromDate.attr("disabled","disabled");
    		toDate.attr("disabled","disabled");
    		fromDate.val('');
    		toDate.val('');
    		fromDate.removeClass('durationBorder');
    		toDate.removeClass('durationBorder');
    		toDate.datepicker( "option", "minDate", "-1Y" );
    	}
    	else
    	{
    		fromDate.removeAttr("disabled");
    		toDate.removeAttr("disabled");
    	}
    }	
    function notifyTooltip()
	{
		if(navigator.userAgent.indexOf("Android") != -1 || window.navigator.userAgent.indexOf("iPad")!=-1 || window.navigator.userAgent.indexOf("iPhone")!=-1)
		{
			alert("Providing the duration in which the selected LUs were covered in class will help in synchronizing Mindspark with your classroom teaching");
			return false;
		}
		else
		{
			return true;
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

	function activateDeactivateTopic(ttCode,schoolCode,cls,section,flow,modifiedBy,gradeRange,mode,idRemove,notCovered,fromDate,toDate,isCoteacher,ttName)
	{
		<?php
			/*if($_SESSION['isOffline'] === true && SERVER_TYPE=='LIVE')
			{				
				echo "alert('Can not activate/deactivate in online mode.')";
			}
			else
			{*/
		?>		
				if (typeof notCovered === "undefined" || notCovered === null) { 
			      notCovered =0; 
			    }
			    if (typeof fromDate === "undefined" || fromDate === null) { 
			      fromDate = ''; 
			    }
			    if (typeof toDate === "undefined" || toDate === null) { 
			      toDate = ''; 
			    }
				$('.bulkActivateDeactivatePrompt').hide();
				$(".arrow-black-side").css("visibility","hidden");
				$(".arrow-white-side").css("visibility","hidden")
				var sectionList=section.split(',');
				var forSections=[];
				for(var i=0;i<sectionList.length;i++){
					if ($.trim(sectionList[i])!="")
						forSections.push(cls+'-'+sectionList[i]);
					else 
						forSections.push(cls);
				}
				if(mode=="seeActivate")
				{
					var openTab = document.getElementById("openTab").value;
					window.location = "mytopics.php?ttCode="+ttCode+"&cls="+cls+"&section="+section+"&flow="+flow+"&interface=new&activateMode=yes&gradeRange="+gradeRange+"&openTab="+openTab;
					return false;
				}
				else if(mode=="activate")
				{		
					var confirmText	=	"Do you want to activate this topic for class"+(forSections.length>1?"es ":" ")+forSections.join(',')+"?";	
					if(confirm(confirmText))
					{		
						triggerConfirmAction(ttCode,schoolCode,cls,section,flow,modifiedBy,gradeRange,mode,idRemove,notCovered,fromDate,toDate,isCoteacher);
					}
				}
				else
				{	

					if(isCoteacher == 2)
					{
						var confirmText	=	"Do you want to deactivate this topic for class"+(forSections.length>1?"es ":" ")+forSections.join(',')+"?";
						if(confirm(confirmText))
						{		
							triggerConfirmAction(ttCode,schoolCode,cls,section,flow,modifiedBy,gradeRange,mode,idRemove,notCovered,fromDate,toDate,isCoteacher);
						}
					}
					else 
					{
						if(isCoteacher == 1)
						{						
							$.ajax({
								url:'topicReportController.php?mode=inCompleteAssessment&cls='+cls+'&section='+section+'&ttCode='+ttCode,
								type:'get',
								async: 'false',				
								success: function(data){
									if($.trim(data) != '')
									{
										$(".box-style").show();
										$("#alert-details").html(data+' have not completed the assessment yet. Assessment report will not include their perfomance.');
									}
									else
										$(".box-style").hide();							
								}
							});
						}
						else
							$(".box-style").hide();		

						$("#topic-name").html('You are going to deactivate <b>'+ttName+'.</b>');
						$("#customConfirm").dialog({
							width : "500px",
							title : 'Deactivate Topic',
							modal: true,
						    buttons: {
						        "NO, WAIT FOR COMPLETION": function() {
						          $( this ).dialog( "close" );					          
						        },
						        "YES, DEACTIVATE" : function() {
						          $( this ).dialog( "close" );
						          triggerConfirmAction(ttCode,schoolCode,cls,section,flow,modifiedBy,gradeRange,mode,idRemove,notCovered,fromDate,toDate,isCoteacher);
						        }
						    }
						});	
					}						
										
				}
				
		<?php  ?>
		 
	}
	function triggerConfirmAction(ttCode,schoolCode,cls,section,flow,modifiedBy,gradeRange,mode,idRemove,notCovered,fromDate,toDate,isCoteacher){
			var linkTo	=	"ajaxRequest.php?mode="+mode+"&ttCode="+ttCode+"&schoolCode="+schoolCode+"&cls="+cls+"&section="+section+"&flow="+flow+"&modifiedBy="+modifiedBy+"&ver=<?=date("Y-m-d H:i:s");?>"+"&notCovered="+notCovered+"&fromDate="+fromDate+"&toDate="+toDate;
			$.ajax({
				url:linkTo,
				type:'get',
				async: 'false',
				success: function(data){
					if(mode=="activate"){
						alert(data);
						$("#"+idRemove).remove();
						$("#generate").click();
						section = $('#lstSection').val()!=""?$('#lstSection').val():sectionList[0];
						saveids(cls,section,'false');
					}
					else {
						var topicDetails=data.split('|~|');	
						if (cls<3 || typeof topicDetails[1]=='undefined' || topicDetails[1]==0)
						{
							alert('Your topic has been successfully deactivated.');
							$("#"+idRemove).remove();
							$("#generate").click();
							section = $('#lstSection').val()!=""?$('#lstSection').val():sectionList[0];
							saveids(cls,section,'false');
						}
						else if(isCoteacher == 2)
						{

							$( "#dialog-ws-confirm-prompt" ).html('<p>Topic '+topicDetails[0]+' has been successfully deactivated. Would you like to make a worksheet for this topic?</p>').dialog({
							  close:function(){
						      	$("#"+idRemove).remove();
						      	$("#generate").click();
						      	section = $('#lstSection').val()!=""?$('#lstSection').val():sectionList[0];
						      	saveids(cls,section,'false');
						      },	
						      draggable: false,	
						      resizable: false,
						      height:200,
						      width:400,
						      modal: true,
						      buttons: {
						        "Yes": function(){
						        	window.open('createWorksheets.php?cls='+cls+'&ttCode='+topicDetails[1],"_blank");						        	
						        	$( this ).dialog( "close" );
								},
						        "No": function() {
						          $( this ).dialog( "close" );
						        }
						      }
						    });
						}
						else
						{
							$( "#dialog-ws-confirm" ).show();
							$( "#dialog-ws-confirm" ).html('<p>'+topicDetails[0]+' topic has been successfully deactivated. You can now view report or create a worksheet.</p>').dialog({
							  close:function(){
						      	$("#"+idRemove).remove();
						      	$("#generate").click();
						      	section = $('#lstSection').val()!=""?$('#lstSection').val():sectionList[0];
						      	saveids(cls,section,'false');
						      },	
						      title : 'Topic Deactivated',
						      draggable: false,	
						      resizable: false,
						      width:450,
						      modal: true,
						      buttons: {
						        "CANCEL": function() {
						          $( this ).dialog( "close" );
						        },
						        "CREATE WORKSHEET": function(){
						        	window.open('createWorksheets.php?cls='+cls+'&ttCode='+topicDetails[1],"_blank");
						        	$( this ).dialog( "close" );
								},
								"VIEW REPORT" : function(){
									window.location ='topicReport.php?schoolCode='+schoolCode+'&cls='+cls+'&sec='+section+'&topics='+ttCode+'&mode=0&topicName='+topicDetails[0];
								}
						      }
						    });		
						}											
																		
					}
					$("#"+idRemove).remove();
				}
			});
		
	}
	function createWorksheet(myClass,ttCode,t)
	   {
	       <?php $http = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http'; ?>
	       var baseUrl = "../app/worksheet/";
	       var makeWorksheetUrl = editWorksheetUrl = baseUrl + 'api/dashboard/make_worksheet';
	       var createWorksheetUrl = baseUrl + 'api/dashboard/create_worksheet';
	       // proceed if any token/s are selected:
	       $.ajax({
	           url: createWorksheetUrl,
	           data: {class: myClass,user_id:'<?=$md5UserID?>',ajax:1},
	           type: 'post',
	           async: 'false',
	           success: function (id) {
	               if($.isNumeric(id))
	               {
	                   var html = "<html><body>";
	                   html +='<form name="frmBrowser" id="frmBrowser" method="POST" target="_blank" action="'+makeWorksheetUrl+'">';
	                   html +='<input type="hidden" name="wsm_id" id="wsm_id" value="'+id+'">';
	                   html +='<input type="hidden" name="tt_code" id="tt_code" value="'+ttCode+'">';
	                   html +='<input type="hidden" name="fromTopicDeactivation" id="fromTopicDeactivation" value="1">';
	                   html +='</form><script>document.getElementById("frmBrowser").submit();<\/script></body></html>';
	                   //newWSTab.document.write(html);
	                   $("#container").append(html);//$('#container #frmBrowser').get(0).target=newWSTab;
	                   document.getElementById("frmBrowser").submit();
	                   $("#dialog-ws-confirm").dialog( "close" );
	                   $("#dialog-ws-confirm-prompt").dialog( "close" );
	                   $("#generate").click();
	                   section = $('#lstSection').val()!=""?$('#lstSection').val():sectionList[0];
	                   saveids(myClass,section,'false');
	               }
	               else
	               {
	               		//newWSTab.close();
	                   var obj = jQuery.parseJSON(id);
	                   if(obj.eiCode == 'sessionError')
	                   {
	                       alert(obj.eiMsg);
	                       window.open('','_self').close();
	                   }
	                   else
	                   {
	                       alert(obj.eiMsg);
	                   }
	               }
	           }
	       }); 
	       return false;        
	   }
	function openActivateDeactivatePrompt(this1,ttCode,schoolCode,cls,section,flow,modifiedBy,gradeRange,mode,idRemove,sectionList,idADPrompt,durationPopup,isCoteacher,ttName){
	 	var action=mode=='deactivate'?'getDeactivationList':'getActivationList';
	 	var ADPrompt = $(this1).parent().prev('.bulkActivateDeactivatePrompt');

	 	$(ADPrompt).find("*").html('');setTimeout(function(){$(ADPrompt).removeClass('BAPromptShift').css('top',(-70)+"px").show();},500);	 	
	 	$(ADPrompt).find(".bulkADMain").html('<img src="assets/loadingTiny.gif" width="50px" height="50px" style="margin:auto;display: block;">');
	 	$.ajax({
	       url: 'ajaxRequest.php',
	       type: 'post',
	       async: 'false',
	       data: {'mode': action,'ttCode':ttCode,'cls':cls,'flow':flow,'sectionList':sectionList},
	       success: function(response) {
	       		var responseArray='';
		       	try{
		       		var responseArray=JSON.parse(response);
		       	}
		       	catch(er){}
		       	if (responseArray==""){
		       		alert('Unable to '+(mode)+' topic now.');
		       		$(ADPrompt).hide();
		       		return;
		       	}
		       	var rKeys = Object.keys(responseArray);
		       	if((mode != 'activate' && durationPopup==1) || durationPopup == 0)
		       	{
			       	if(rKeys.length==1 && typeof responseArray[section]!='undefined'){
			       		$(ADPrompt).hide();
			       		activateDeactivateTopic(ttCode,schoolCode,cls,section,flow,modifiedBy,gradeRange,mode,idRemove,0,'','',isCoteacher,ttName);
			       		return;
			       	}
			       	else if(rKeys.length==1){
			       		alert('Unable to '+(mode)+' topic now.');
			       		$(ADPrompt).hide();
			       		return;
			       	}
			    }
		       	sectionRow='<div class="section-div"><label><input type="checkbox" class="selectAllSections" value="allSections"><span>All</span></label>';
		       	var sectionsL=sectionArray[cls];
		       	for(var u=0;u<sectionsL.length;u++){
		       		var thisRow='<label ';var enCB=true;
		       		progress=responseArray[sectionsL[u]];
		       		if(mode=='activate'){
		       			thisRow+=(progress==-1?'class="inactive" title="One or more learning units selected are already covered for this section. To enable it, you should deactivate the topic where it is currently selected." ':(progress==-2?'class="inactive" title="This section already has 15 active topics activated. Please deactivate a topic to activate a new topic." ':(!progress?'class="inactive" title="This topic is already activated for this section." ':'')));
		       			if(!progress){enCB=false;}
		       			else if (progress==-1 || progress==-2){enCB=false;}
		       			else {enCB=true;}
		       		}
		       		thisRow+='><input type="checkbox" class="checkboxSection" '+(sectionsL[u]==section && enCB?'checked':'')+(!enCB?' disabled ':'')+' value="'+sectionsL[u]+'"><span>'+sectionsL[u]+(mode=='deactivate'?" ("+Math.round(progress)+'%)':"")+'</span></label>';
		       		if(mode=='activate' || (mode=='deactivate' && typeof progress!='undefined')){ sectionRow+=thisRow;}
		       	}
		       	sectionRow+='</div><br>';
		       	var dateRow='';

		       	if(durationPopup == 1 && mode=='activate')
		       	{
		       		$(".durationFromTo").html('');
		       		var dateRow= 'Duration of teaching this in class:<br><span class="durationFromTo"> <label> From: </label> <input size="10" id="fromDate" readonly="readonly" maxlength="10" > <label class="durationTo"> To: </label> <input size="10" id="toDate" readonly="readonly"  maxlength="10" >  <span class="durationHelp" title="Providing the duration in which the selected LUs were covered in class will help in synchronizing Mindspark with your classroom teaching" onclick="notifyTooltip()"><label >?</label></span></span><br><input type="checkbox" id="notCovered" onchange=checkCovering(this)><span class="notCovering">Not covering in classroom </span><br>';		 
		       	}

		       	if(rKeys.length==1 && durationPopup == 1 && mode=='activate')
			    {
			    	$(ADPrompt).find(".bulkADMain").html(dateRow);
			    }
			    else
			    {			    		    
			    	$(ADPrompt).find(".bulkADMain").html("Do you want to "+mode+" this topic for other sections as well?<br>Choose sections:<br>"+sectionRow+dateRow);
			    	if($(ADPrompt).find('.section-div label.inactive').length==$(ADPrompt).find('.checkboxSection').length)
			    		$(ADPrompt).find('.section-div input.selectAllSections').attr('disabled','disabled').parent().addClass('.inactive');
			    }
	       		$
	       		var helpText=mode=='deactivate'?'It is recommended to deactivate a topic only after a section reaches a progress of 75%.':'';
	       		$(ADPrompt).find(".bulkADHelp").html(helpText);
	       		var buttonText=mode=='deactivate'?'Deactivate Now':'Activate Now';
	       		var bulkADAction=$(ADPrompt).find(".bulkADActionButton").html('');
	       		
	       		$('<a href="javascript:void(0);" class="bulkButton">'+buttonText+'</a>').appendTo(bulkADAction).click(function(){
	       			var sections=[section];
		       			sections = [];
		       		var durationCount = 0;
	       			var fromDate = '';
	       			var toDate = '';
	       			var notCovered = 0 ;       			
	       			if(rKeys.length==1)
				    {
				    	sections.push(rKeys);
				    }
				    else
				    {
			       			$(ADPrompt).find(".checkboxSection:checked").each(function(i,item){sections.push($(item).val());});
			       			if (sections.length==0){
			       				if(mode == 'activate')
			       				{
			       					alert('Please select at least one section to '+mode+' the topic. Note that some section(s) may be grayed out if one or more learning units are already covered for the section(s).');return;
			       				}
			       				else
			       				{
			       					alert('Please select at least one section to '+mode+' the topic.');return;
			       				}
			       			}
			       	}
			       	if(mode=='activate' && durationPopup==1)
	       			{
	       				if($(ADPrompt).find("#notCovered").is(':checked'))
	       				{
	       					notCovered = 1;
	       				}
	       				else
	       				{       					
	       					fromDate = $(ADPrompt).find("#fromDate").val();
		       				toDate = $(ADPrompt).find("#toDate").val();
		       				if(fromDate == '')
		       				{
		       					$(ADPrompt).find("#fromDate").addClass('durationBorder');
		       					durationCount++;
		       				}       				
		       				if(toDate == '')
		       				{
		       					$(ADPrompt).find("#toDate").addClass('durationBorder');
		       					durationCount++;
		       				}
	       				}       				
	       				
	       			}
	       			if(durationCount != 0)
	       			{
	       				return false;
	       			}
	       			else
		       			activateDeactivateTopic(ttCode,schoolCode,cls,sections.join(','),flow,modifiedBy,gradeRange,mode,idRemove,notCovered,fromDate,toDate,isCoteacher,ttName);
	       		});
				var ADBottom=ADPrompt.offset().top+ADPrompt.outerHeight();
				var TCBottom=$('#trailContainer').offset().top+$('#trailContainer').innerHeight();
				if (ADBottom>TCBottom) $(ADPrompt).addClassClass('BAPromptShift').css('top',(-70-ADBottom+TCBottom)+"px");
	        }
	    });
	}
	function openDateOfTopicPrompt(ttCode,schoolCode,cls,section,flow,modifiedBy,gradeRange,mode,idRemove,idADPrompt){
		/*var ADPrompt = $(this1).parent().prev('.bulkActivateDeactivatePrompt');
		$(ADPrompt).find("*").html('');setTimeout(function(){$(ADPrompt).show();},500);
		$(ADPrompt).find(".bulkADMain").html('<div class="topicDateDiv"><br>Duration of teaching this in class:<br><span style="color: #9FCB50;">From: <input type="text" readonly class="dateField" name="topicStartDate">  To: <input type="text" readonly class="dateField" name="topicEndDate">  <label title="Providing the duration in which the selected LUs were covered in class will help in synchronizing Mindspark with your classroom teaching." class="topicDateHelp">?<label></span><br><label style="font-size:0.9em;"><input type="checkbox" class="notCoveredTopic" name="notCoveredTopic">Not covering in classroom </label><br>');
		var buttonText=mode=='deactivate'?'Deactivate Now':'Activate Now';
	    var bulkADAction=$(ADPrompt).find(".bulkADActionButton").html('');
	    $('<a href="#" class="bulkButton">'+buttonText+'</a>').appendTo(bulkADAction).click(function(){*/
	    	var sections=[section];
	    	activateDeactivateTopic(ttCode,schoolCode,cls,sections.join(','),flow,modifiedBy,gradeRange,mode,idRemove,0,'','',0,'');
	    //});
	}
	function showMapping(ttCode, cls, section,flow,gradeRange,activateButton)
	{	 
		activateButton = activateButton || 0;
		var openTab = document.getElementById("openTab").value;	
		window.location = "mytopics.php?ttCode="+ttCode+"&cls="+cls+"&section="+section+"&flow="+flow+"&interface=new&gradeRange="+gradeRange+"&openTab="+openTab+"&activateButton="+activateButton;
	}

	function filterTopicWise(data)
	{
		if(data == 'true')
		{
			$('#checkflag').val('1');
		}
		if(document.getElementById('lstClass').value=="")
		{
			alert("Please select a Class!");
			document.getElementById('lstClass').focus();
			return false;
		}else if(document.getElementById('lstSection').value=="" && $(".noSection").is(":visible"))
		{
			alert("Please select a Section!");
			document.getElementById('lstSecton').focus();
			return false;
		}
		else{
			document.getElementById('searchingTerm').value = document.getElementById('name').value;
			setTryingToUnload();
			document.forms["frmMain"].submit();
		}
	}
	
	function doAsAStudent(ttcode,forceFlow,customClusterCode){
			if(forceFlow=="")
			{
				alert("Sorry not able to start the session.");
				return false;
			}
			$("#mode").val("ttSelection");
			$("#ttCode").val(ttcode);
			$("#forceFlow").val(forceFlow);
			$("#customClusterCode").val(customClusterCode);
			$("#userType").val("teacherAsStudent");
			$("#mindsparkTeacherLogin").attr("action", "../userInterface/controller.php");
			setTryingToUnload();
			$("#mindsparkTeacherLogin").submit();
	}
	
	function setSection(sec)
	{
		if(document.getElementById('lstSection'))
		{
			var obj = document.getElementById('lstSection');
			removeAllOptions(obj);
			var cls = document.getElementById('lstClass').value;
			if(cls=="")
			{
				var OptNew = document.createElement('option');
				OptNew.text = 'Select';
				OptNew.value = '';
				obj.options.add(OptNew);
				obj.style.display = "inline";
				obj.selectedIndex = 0;
			}
			else
			{
				var OptNew;
				for(var i=0; i<gradeArray.length && gradeArray[i]!=cls; i++);
				if(sectionArray[gradeArray[i]].length>0)
				{
					$(".noSection").show();
					if(sectionArray[gradeArray[i]].length>1)
					{
						OptNew = document.createElement('option');
						OptNew.text = 'Select';
						OptNew.value = '';
						obj.options.add(OptNew);
					}
					for (var j=0; j<sectionArray[gradeArray[i]].length; j++)
					{
						OptNew = document.createElement('option');
						OptNew.text = sectionArray[gradeArray[i]][j];
						OptNew.value = sectionArray[gradeArray[i]][j];
						if(sec==sectionArray[gradeArray[i]][j])
						OptNew.selected = true;
						obj.options.add(OptNew);
					}
				}
				else
				{
					$(".noSection").hide();
				}
			}
		}
	}
	
</script>

<script>
	var newWSTab;
    $(document).ready(function(){
 
        $(function(){
            $('span.clickMe').click(function(e){
				if(document.getElementById('lstClass').value=="")
				{
					alert("Please select a Class!");
					document.getElementById('lstClass').focus();
					return false;
				}else if(document.getElementById('lstSection').value=="" && $(".noSection").is(":visible"))
				{
					alert("Please select a Section!");
					document.getElementById('lstSecton').focus();
					return false;
				}
                var hiddenSection = $('section.hidden');
                hiddenSection.fadeIn("slow","linear")
                    // unhide section.hidden
                    .css({ 'display':'block' })
                    // set to full screen
                    .css({ width: $(window).width() + 30 + 'px', height: $(window).height() + 'px' })
                    .css({ top:($(window).height() - hiddenSection.height())/2 + 'px', 
                        left:($(window).width() - hiddenSection.width())/2 + 'px' })
                    // greyed out background
                    .css({ 'background-color': 'rgba(0,0,0,0.5)' })
                    .appendTo('body');
                    // console.log($(window).width() + ' - ' + $(window).height());
                    $('span.close').click(function(){ $(hiddenSection).fadeOut(); });
					
					$('#noTopics').css("display","none");
					
					
					<?php if($searchTerm=="") { ?>
					$('#token-input-name').focus();
					<?php } ?>
					
					$('html, body').css({
					    'overflow': 'hidden'
					})
					
					$('body').css({
					    'position': 'absolute'
					})
            });
        });
 
    });
	
	function closeSearch()
	{
		$('#generate').click();
	}
	
</script>

<script type="text/javascript" src="libs/jquery.tokeninput_search.js"></script>
<link rel="stylesheet" type="text/css" href="css/token-input-facebook_search.css" />

<script type="text/javascript">

$(document).ready(function () {
	var rs = "";
	var searchedTerm = "";
	var selectClass = "<?php echo $childClass ?>";
	var flow = "<?php echo $defaultFlow ?>";
    $("#name").tokenInput("getautocomplete.php?class="+selectClass+"&flow="+flow,{
                hintText:"Search a topic/learning unit",
				theme : "facebook",
				searchingText : "Mindspark is searching...",
				noResultsText : "No similar topic found",
				tokenLimit : 1,
				preventDuplicates: true,
				onAdd: function (item) {
					
					searchedTerm = document.getElementById('name').value;					
					var linkTo	='ajaxRequest.php?mode=searchLog&searchTerm='+rs+'&searchResult='+searchedTerm;
					$.get(linkTo,function(data){
						/*alert(data);*/
						$("#generate").click();
					});
					
                },
				onResult: function (results) {
				
					var tokenInput = document.getElementById('token-input-name');
					rs = tokenInput.value;
                    $('div.token-input-dropdown-facebook').css('height','400px');
					rs = rs.replace(/\W+/g, " ");
					if(rs == " ")
					{
						alert("Enter valid topic name");
						$('#token-input-name').html("");
						$('#searchClick').click();
						return false;
					}
					return results;
                }				
            });

	//For show/hide of customized topics
	$(".slidingDiv").hide();
	$(".show_hide").show();
	
	$('.show_hide1').click(function(){
		togglePanel("collapsibleTopicWrapper1", "pnlToggleCustomYours");
	});

	$('.show_hide2').click(function(){
		togglePanel("collapsibleTopicWrapper2", "pnlToggleCustomOthers");
	});

	$('.show_hide3').click(function(){
		togglePanel("collapsibleTopicWrapper3", "pnlToggleReadyForUse");
	});

	$('.show_hide4').click(function(){
		togglePanel("collapsibleTopicWrapper4", "pnlToggleOtherGrades");
	});
	
});
function togglePanel(elementID, toggleID)
{
	$("#"+elementID).slideToggle();	
	if($("#"+toggleID).html()=="+")
		$("#"+toggleID).html("-");
	else
		$("#"+toggleID).html("+");
}
/*$(document).keyup(function(e) {
  if (e.keyCode == 27) { $('.hidden').hide() }   // esc
  $('#generate').click();
});*/
</script>

<script>
$(document).ready(function(){
<?php if($searchTerm!="") {?>


jQuery(function(){
   jQuery('#searchClick').click();
});

<?php } ?>
});

</script>



<script>
$(function() {

$( "#sortable2" ).sortable({
	axis: 'y',
	stop: function(event, ui) {
        var i = 1;
		  var updatelist ='';
	
		var array = $('#sortable2 .topicContainer').map(function(){
		updatelist = $(this).attr('value');
		
		$("#"+updatelist+"box").html(i);
		i++;
		}).get();
    },
});
$( "#sortable1 li, #sortable2 li" ).disableSelection();
if($('#sortable2').length>0)
	$('#sortable2').css('height', ($(window).height()-$('#sortable2').offset().top-$('#copyright').height()-25)+'px');
$('#sortable2>*').css('cursor', 'move');

});
</script>

<script>
function saveids(cls,section,flag)
{
	
	document.getElementById('waitingimage').style.display = 'block';
	var idlist ='';
	
	var array = $('#sortable2 .topicContainer').map(function(){
		idlist += $(this).attr('value')+',';
		//$("#"+idname+i).html(i);
    	return $(this).attr('value');
	}).get();

	

	$.ajax({
      url: 'myClasses.php',
      type: 'post',
      data: {'action': 'savepriority','ttlist':idlist,'cls':cls,'section':section},
      success: function(response) {
		  document.getElementById('waitingimage').style.display = 'none';
		  if(flag == 'true')
		  {
			alert("Priority saved sucessfully");
		  }

		  var i = 1;
		  var updatelist ='';
	
		var array = $('#sortable2 .topicContainer').map(function(){
			updatelist = $(this).attr('value');
			$("#"+updatelist+"box").html(i);
			i++;
		}).get();
		//$("#"+idname+i).html(i);
			//$(TT0372).html("koko");
		 filterTopicWise('false');
      }
    }); // end ajax call
}

function showMessage()
{
	var prompts=new Prompt({
			text:'Drag and Drop the topics to decide the order in which topics are visible to your students. \nThis will help them work on the  most important topics first. \n Click "save priority" to save.',
			type:'alert',
			label1:'OK',
			func1:function(){
				$("#prmptContainer_popupDisplay").remove();
				filterTopicWise('true');
			},
			promptId:"popupDisplay"
		});
}

function showbutton()
{
	$('#generatedragdiv').css('display','block');
	if($( "#masterTopic" ).val() != '')
	{
		$("#alertMessage").attr('disabled','disabled');
		$('#alertMessage').attr('title', 'You can assign priority to topics only when the master topic field is set to all');
	}
}

function hidebutton()
{
	$('#generatedragdiv').css('display','none');
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
	
	<form target='window.parent' name="mindsparkTeacherLogin" id="mindsparkTeacherLogin" action="" method="post">
		<input type="hidden" name="mode" id="mode" value="">
		<input type="hidden" name="sessionID" id="sessionID" value="<?=$_SESSION["sessionID"]?>">
	    <input type="hidden" name="childClass" id="childClass" value="<?=$_REQUEST['cls']?>">
	    <input type="hidden" name="userType" id="userType" value="teacherAsStudent">
	    <input type="hidden" name="forceNew" id="forceNew" value="">
	    <input type="hidden" name="ttCode" id="ttCode" value="">
	    <input type="hidden" name="customClusterCode" id="customClusterCode" value="<?=$_REQUEST['learningunit']?>">
	    <input type="hidden" name="forceFlow" id="forceFlow" value="<?=$_REQUEST['flow']?>">
	    <input type="hidden" name="startPoint" id="startPoint" value="">
	</form>
	<div id="container">
		<div id="trailContainer">
			<table id="childDetails">
				<td width="33%" id="sectionRemediation" class="activatedTopic"><div id="actTopicCircle1" class="smallCircle" style="cursor:pointer;"></div><label id="1" style="cursor:pointer;" class="pointer" onClick="javascript:showbutton()">CURRENTLY ACTIVE TOPICS <span style="color:red"><?php if(count($myteacherTopicActivated)>0) echo "(".count($myteacherTopicActivated).")"; else echo "(0)"; ?></span></label></td>
		        <td width="40%" id="studentRemediation" class="activateTopicAll"><div id="actTopicCircle2" class="smallCircle" style="cursor:pointer;" onClick="javascript:hidebutton()"></div><label id="2" style="cursor:pointer;" class="pointer" onClick="javascript:hidebutton()">ALL ACTIVE AND DEACTIVATED TOPICS <span style="color:red"><?php if(count($teacherTopicActivatedAll)>0) echo "(".count($teacherTopicActivatedAll).")"; else echo "(0)"; ?></span></label></td>
		        <td width="43%" id="classRemediation" class="activateTopics"><div id="actTopicCircle3" class="smallCircle" style="cursor:pointer;" onClick="javascript:hidebutton()"></div> <label id="3" style="cursor:pointer;" class="pointer" onClick="javascript:hidebutton()">ACTIVATE A TOPIC <span style="color:red"><?php if(count($teacherTopicNeverActivated)>0) echo "(".count($teacherTopicNeverActivated).")"; else echo "(0)"; ?></span></label></td>
				<td> 
				
       <span class="clickMe" id="searchClick">Topic Search</span>
				 </td>
			</table>
			
			<?php include("topicSearch.php") ?>
			
			<?php 
			if(count($teacherTopicNeverActivated)>0){
			?>
			<table class="pagingTable">
		        <td width="35%">Class <?=$class.$section?></td>
		        <?php
				if(count($myteacherTopicActivated)==0)
				echo "<td id='topicActivated'>No topic activated</td>";
				?>

				 <td width="35%" align="center">
				 <div id="generatedragdiv">
				 <?php if($checkflag == 1) {  ?>
				 <input  class="button" type="button"  style="cursor:pointer;" onClick="saveids(<?=$class?>,'<?=$section?>','true');" value="Save priority">
				 &nbsp;&nbsp;&nbsp;
				 <input  class="button" type="button" style="cursor:pointer;" onClick="filterTopicWise('false');" value="Cancel">
				 <?php } else {
				 if(!isset($_SESSION['popupDisplay']))
				 {
					 $_SESSION['popupDisplay'] = 'true';
				 ?>
				 <input  class="button" id="alertMessage" style="cursor:pointer;" type="button" onClick="showMessage()"  value="Assign priority" title='Drag and Drop the topics to decide the order in which topics are visible to your students. This will help them work on the  most important topics first. Click "save priority" to save.'>
					<?php } else { ?>
				 <input  class="button" id="alertMessage" style="cursor:pointer;" type="button" onClick="filterTopicWise('true');" value="Assign priority" title='Drag and Drop the topics to decide the order in which topics are visible to your students. This will help them work on the  most important topics first. Click "save priority" to save.'>
				 
				 <?php } } ?>
				 </div>
				 </td>
			</table>
			<div class="questionContainer" id="activateTopics">
			<?php 
		    $i=0;
		    $checkCustomizedByYou = 0;
		    $checkCustomizedByOthers = 0;
		   	$checkNotCustomized = 0;
		    $checkOpenDiv = 0;		
		    foreach($teacherTopicNeverActivated as $ttCode=>$ttDetails)
				{
					$isCustom = 0;
					foreach ($customizedTopicsArray as $parentTTCode => $internalArray) 
					{
						foreach ($internalArray as $topicCode) 
						{
							if($topicCode[0] == $ttCode)
							{
								$isCustom = 1;
								break;
							}
						}
						if($isCustom)
							break;
					}
					$i++;
					$testArray[$ttCode] = $isCustom;

					if(strcmp($ttDetails['category'], '4-otherGrades') == 0 && $checkOtherGrades==0){
						if($checkOpenDiv) {
							echo '</div>';
							$checkOpenDiv=0;
						}
						echo '<a href="#pnlTopicOtherGrades" title="Expand/Collapse" class="show_hide4" style="text-decoration:none;"><div class="otherGrades" id="pnlTopicOtherGrades" >Topics from other Grades <div style="float:right; padding-right:20px" id="pnlToggleOtherGrades">+</div></div></a> <div class="slidingDiv" id="collapsibleTopicWrapper4">';
						$checkOtherGrades=1;
						$checkOpenDiv=1;
						// Other Grades is the last irrespective of the value of $ttDetails['category']
						$checkCustomizedByYou = 1;
						$checkCustomizedByOthers = 1;
					 	$checkNotCustomized = 1;

					} elseif($checkCustomizedByYou == 0 && strcmp($ttDetails['category'], '1-customizedByYou') == 0) {
						if($checkOpenDiv) {
							echo '</div>';
							$checkOpenDiv=0;
						}
						echo '<a href="#pnlCustomYours" title="Expand/Collapse" class="show_hide1" style="text-decoration:none;"><div class="otherGrades" id="pnlCustomYours" >Topics customised by you <div style="float:right; padding-right:20px" id="pnlToggleCustomYours">+</div></div></a> <div id="collapsibleTopicWrapper1" class="slidingDiv">';
						$checkOpenDiv=1;
						$checkCustomizedByYou = 1;
						
					} elseif($checkCustomizedByOthers == 0 && strcmp($ttDetails['category'], '2-customizedByOthers') == 0) {
						if($checkOpenDiv) {
							echo '</div>';
							$checkOpenDiv=0;
						}
						echo '<a href="#pnlCustomOthers" title="Expand/Collapse" class="show_hide2" style="text-decoration:none;"><div class="otherGrades" id="pnlCustomOthers" >Topics customised by other teachers in your school <div style="float:right; padding-right:20px" id="pnlToggleCustomOthers">+</div></div></a> <div id="collapsibleTopicWrapper2" class="slidingDiv">';
						$checkOpenDiv=1;
						$checkCustomizedByOthers = 1;
						
					} elseif($checkNotCustomized == 0 && strcmp($ttDetails['category'], '3-notCustomized') == 0) {
						if($checkOpenDiv) {
							echo '</div>';
							$checkOpenDiv=0;
						}
						echo '<a href="#pnlReadyForUse" title="Expand/Collapse"  class="show_hide3"  style="text-decoration:none;"><div class="otherGrades" id="pnlReadyForUse">Topics ready for use (for grade '.$class.') <div style="float:right; padding-right:20px" id="pnlToggleReadyForUse">+</div></div></a> <div id="collapsibleTopicWrapper3" class="slidingDiv">';
						$checkNotCustomized = 1;
						$checkOpenDiv=1;
					}
				
				$gradeArr = explode("-",$ttDetails[3]);
				$minGrade = $gradeArr[0];
				$maxGrade = $gradeArr[count($gradeArr)-1];


					$activatedForSections=array();
					foreach ($ttDetails['activatedForSections'] as $key => $value) {
						$activatedForSections[]=$value;//str_replace(" ", "", $value);
					}
					global $sectionArray;
					$thisClassSections=$sectionArray[$class];
					if(strlen($thisClassSections)==0) $thisClassSections="''";
					$thisClassSections=explode(",", $thisClassSections);
					$availableForSections=array();
					foreach ($thisClassSections as $key => $value) {
						if(!in_array(str_replace("'", "", $value), $activatedForSections))
							$availableForSections[]=str_replace("'", "", $value);
					}
		    	?>
				<div class="topicContainer" id="topicContainer<?=$i?>">
					<div class="actionsTab linkPointer" id="actionTab<?=$i?>"><span id="actionsText" class="linkPointer">Actions</span></div>
					<div id="topicBoxDescTopic" >
						<!-- <a href="sampleQuestions.php?ttCode=<?=$ttDetails[2]?>&cls=<?=$class?>&flow=<?=$teacherTopicNeverActivated[$ttDetails[2]][1]?>"><?=$ttDetails[0]?></a>  -->
						<?php 
						if(!$isCustom)	{
							?>
								<a class="topicBoxName" href="javascript:void(0);" onClick="activateDeactivateTopic('<?=$ttDetails[2]?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','<?=$ttDetails[1]?>','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','seeActivate','topicContainer<?=$i?>',0,'','',0,'<?=$ttDetails[0]?>')" title="<?=stripcslashes($ttDetails[0])?>"><?=$ttDetails[0]?></a>
							<?php 
						}else{
							?>
								<a class="topicBoxName" href="javascript:void(0);" onClick="showMapping('<?=$ttDetails[2]?>','<?=$class?>','<?=$section?>','<?=$ttDetails[1]?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>')"  title="<?=stripcslashes($ttDetails[0])?>"><?=$ttDetails[0]?></a>
							<?php 
						}
						?>						
						<?php if($ttDetails['isCoteacher'] == 1)
							{ ?>
								<img src='assets/co-teacher/co_teacher_tag.png' class="coTeacherImg" id="tooltipTab<?=$i?>">
								<div class="arrow-white-side tooltipTab<?=$i?>" style="margin-right:-145px;">
									<div id="activateTab">
										Empowered for students to learn the topic on their own.
									</div>
								</div> 
								<?php } ?>
						
					</div>
					<div id="topicBoxDescInfo1">
						Grade: <?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>
					</div>
					<div id="topicBoxDescInfo2">
						<?php if($ttDetails[4]!=""){
							?>
						Customized By : <?=$ttDetails[4]?>
						<?php } ?>
					</div>
					<div class="arrow-black-side actionTab<?=$i?>">
						<div id="activateTab">
							<div class="activate"></div>
							<div class="bulkActivateDeactivatePrompt" id="bulkADPrompt<?=$i?>">
								<div class="bulkADMain"></div>
								<div class="bulkADHelp"></div>
								<div class="bulkADActionButton"></div>
							</div>
							<div id="activate" class="tabText linkPointer" <?php if($ttDetails[3]==0 || checkForClusters($ttCode,$ttDetails[1])==0) echo "style='visibility:hidden'";?>>
						<?php if($currentActivated>=15) { 
								if(!$isCustom) { 
								?>	
									<span class="activateSpan" onClick="activateLimitOver()" id="<?=$ttDetails[2]?>">See/Activate</span>
								<?php }
								else { ?>
									<span class="activateSpan" onClick="activateLimitOver()" id="<?=$ttDetails[2]?>">Activate</span>
								<?php	
								}
						
							}
							 else if($class+2 <= $minGrade || $class-2 >= $maxGrade) { ?>
							<span class="activateSpan" onClick="topicClassDifference(<?=$class?>,'<?=$ttDetails[3]?>')" id="<?=$ttDetails[2]?>">See/Activate</span>
						<?php } 
							 else if(!$isCustom)	{ ?>
								<span class="activateSpan see-active-customize" onClick="activateDeactivateTopic('<?=$ttDetails[2]?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','<?=$ttDetails[1]?>','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','seeActivate','topicContainer<?=$i?>',0,'','',0,'<?=$ttDetails[0]?>')" id="<?=$ttDetails[2]?>" style="">See/Activate/customize</span>
						<?php } else { 

								if (((count($availableForSections)==1 && $availableForSections[0]==$section) || count($thisClassSections)==1 && $thisClassSections[0]=="''") && $durationPopup==0){
								?>
								<span class="activateSpan" onClick="openDateOfTopicPrompt('<?=$ttDetails[2]?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','<?=$ttDetails[1]?>','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','activate','topicContainer<?=$i?>','bulkADPrompt<?=$i?>')" id="<?=$ttDetails[2]?>">Activate</span>
									<?php
								}
								else{
									?>
								<span class="activateSpan" rel="openBulkPopup" onClick="openActivateDeactivatePrompt(this,'<?=$ttDetails[2]?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','<?=$ttDetails[1]?>','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','activate','topicContainer<?=$i?>','<?=implode(',',$availableForSections)?>','bulkADPrompt<?=$i?>',<?=$durationPopup?>,<?=$ttDetails['isCoteacher']?>,'<?=$ttDetails[0]?>')" id="<?=$ttDetails[2]?>">Activate</span>
									<?php
								}
								?>

						<?php } ?>
							</div>
							<?php 
							if(($isCustom) || ($class+2 <= $minGrade || $class-2 >= $maxGrade) )	{
								?>
								<div class="customize"></div>	
								<div id="customize" class="tabText linkPointer">
									<span onClick="showMapping('<?=$ttDetails[2]?>','<?=$class?>','<?=$section?>','<?=$ttDetails[1]?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>')">See/Customize</span>
								</div>
								<?php 
							}
							?>
							<div id="download" class="tabText linkPointer" onClick="doAsAStudent('<?=$ttDetails[2]?>','<?=$teacherTopicNeverActivated[$ttDetails[2]][1]?>')" <?php if($ttDetails[3]==0) echo "style='visibility:hidden'";?>><span class="doAsStudent">Do Mindspark</span></div>
						</div>
					</div>
				</div>
			<?php 
			}
			?></div>
			<?php			
			} 
			if($checkOpenDiv) {
				echo '</div>';
				$checkOpenDiv=0;
			} 
			 	
			?>
			
			<?php 
			if(count($myteacherTopicActivated)>0){
			//$teacherTopicActivated = array_reverse($teacherTopicActivated);
			?>
			<div class="questionContainer" id="activatedTopic">
			<center>
				<div id="waitingimage" style="display:none;"><img src='assets/loadingImg.gif' ></div>
			</center>
			<?php if($checkflag == 1) {  ?>
				<div id="sortable2" class="listdrag" style="overflow: auto;">
			<?php } else {?>
				<div  class="listdrag">

			<?php 
			}
		    $i=0;
		    $myteacherTopicActivated = array_reverse($myteacherTopicActivated);
			foreach ($myteacherTopicActivated as $ttCode=>$ttDetails) { 
				$i++;
				$ttName	=	$ttDetails["ttName"];
				$flow	=	$ttDetails["flow"];
				$isCoteacher = $ttDetails["isCoteacher"];
				if($ttDetails["gradeSequence"]==0 && $checkGrade1==0){
					$checkGrade1=1;
					//echo '<div class="otherGrades" >Other Grades</div>';
					//echo '<table bgcolor="#00FF99" class="pagingTable" style="color:red;border-bottom: 1px solid red;">
		        		//<tbody><tr><td width="35%">Other Grades</td>
							//</tr></tbody></table><br/><br/>';
				}
				$checkGrade1=0;
				if($checkGrade1 != 1)
				{
		    	?>
					<style>

					<?php if($checkflag == 1) {  ?>
					.topicContainer:hover {
						color: #CB000F;
						border:1px solid blue;
					}
					<?php } ?>
					</style>
				<div class="" id="<?=$ttCode?>" value="<?=$ttCode?>">
				
				<div class="topicContainer" id="deactive<?=$i?>" value="<?=$ttCode?>">
					<?php if($checkflag == 1) {  ?>
					<div class="draggableContainer">
						<div id="<?=$ttCode?>box" class="left" >
							<?php echo $i; ?>
						</div>
						<!-- <div id="second-div" class="right"> -->
						<div class="actionsTab"><span id="actionsText">Actions</span></div>
						<?php } else { ?>
						<!-- <div id="second-div"> -->
						<div class="actionsTab linkPointer" id="actionTab_<?=$i?>"><span id="actionsText" class="linkPointer">Actions</span></div>
						<?php } ?>
						
						<div id="outerCircle" class="outerCircle"  title="Average topic progress of class">
							<div id="percentCircle" class="progressCircle forHighestOnly circleColor<?=round($ttProgress[$ttCode]/10)?>"><?=round($ttProgress[$ttCode],1)?>%</div>
						</div>
						<div id="topicBoxDescTopic">
							<?php if($checkflag == 1) {  ?>
								<span class="topicBoxName" title="<?=stripcslashes($ttName)?>"><?=$ttName?></span>
							<? } else { ?>
								<!--  <a href="topicProgress.php?ttCode=<?=$ttCode?>&cls=<?=$class?>&section=<?=$section?>&flow=<?=$defaultFlow?>"><?=$ttName?></a>-->
								<a class="topicBoxName" href="topicReport.php?schoolCode=<?= $schoolCode;?>&cls=<?=$class?>&sec=<?=$section?>&topics=<?=$ttCode?>&mode=0&topicName=<?= rawurlencode($ttName);?>" title="<?=stripcslashes($ttName)?>"><?=$ttName?></a>
							<? } ?>
							<?php if($isCoteacher == 1)
							{ ?>
								<img src='assets/co-teacher/co_teacher_tag.png' class="coTeacherImg" id="tooltipTab<?=$i?>">
								<div class="arrow-white-side tooltipTab<?=$i?>" style="margin-right:-145px;">
									<div id="activateTab">
										Empowered for students to learn the topic on their own.
									</div>
								</div>
								<?php } ?>
						</div>
						<div id="topicBoxDescInfo1">
							Grade: <?=$ttDetails["grade"]?>
						</div>
						<div id="topicBoxDescInfo1">
							<!--Activated on <?=setDateFormate($ttDetails["activationDate"])?>-->
							<?=$studentAttempted[$ttCode]?> out of <?=count($userIDs)?> students attempting
						</div>
						<?php $activeSince	=	getDaysTillActivated($ttDetails["activationDate"]); ?>
						
						<div id = "topicBoxDescInfo_custom"> 
							<div id="topicBoxDescInfo2" <?php if($activeSince>30) echo "style='cursor:default;'";else echo "style='cursor:default;'";  ?>>
							</div>						
							<span <?php if($activeSince>30) echo "style='color:red;cursor:pointer;' title='Activated on ".setDateFormate($ttDetails['activationDate']).". It is not advisable to have a topic active for more than 30 days.'"; ?>>Activated on <?= setDateFormate($ttDetails['activationDate']) ?> (<?=$activeSince; if($activeSince==1 || $activeSince==0) echo " Day"; else echo " Days"?> ago) <?php if($activeSince>30){ ?><sup> ?</sup><?php } ?></span>
						</div>
						<!-- </div> -->
						<?php if($checkflag == 1) {  ?>
						</div>
						<div class="arrow-black-side actionTab_<?=$i?>" style="margin-right:25px;margin-top: -40px;">
						<?php } else { ?>
						<div class="arrow-black-side actionTab_<?=$i?>" style="margin-right:-145px;">
						<?php } ?>

						<?php
							$activatedForSections=array();
							foreach ($ttDetails['activatedForSections'] as $key => $value) {
								$activatedForSections[]=$value;//str_replace(" ", "", $value);
							}
						?>

						<div id="activateTab">
							<div class="activate"></div>
							<div class="bulkActivateDeactivatePrompt" id="bulkADPrompt<?=$i?>">
								<div class="bulkADMain"></div>
								<div class="bulkADHelp"></div>
								<div class="bulkADActionButton"></div>
							</div>
							<div id="activate" class="tabText linkPointer">
								<?php
								if (count($activatedForSections)==1 && $activatedForSections[0]==$section){

								?>
								<span class="deactivateSpan" onClick="activateDeactivateTopic('<?=$ttCode?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','','<?=ucfirst($user->childName)?>','-','deactivate','deactive<?=$i?>',0,'','',<?=$ttDetails['isCoteacher']?>,'<?=$ttName?>')" id="<?=$ttCode?>">Deactivate</span>
									<?php
								}
								else{
									?>
								<span class="deactivateSpan" rel="openBulkPopup" onClick="openActivateDeactivatePrompt(this,'<?=$ttCode?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','deactivate','deactive<?=$i?>','<?=implode(',',$activatedForSections)?>','bulkADPrompt<?=$i?>',<?=$durationPopup?>,<?=$ttDetails['isCoteacher']?>,'<?=$ttName?>');" id="<?=$ttCode?>">Deactivate</span>
									<?php
								}
								?>
							</div>
							<div class="customize"></div>
							<div id="customize" class="tabText linkPointer">
								<span onClick="showMapping('<?=$ttCode?>','<?=$class?>','<?=$section?>','<?=$flow?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>')">See/Customize</span>
							</div>
							<div id="download" class="tabText linkPointer" onClick="doAsAStudent('<?=$ttCode?>','<?=$flow?>')"><span class="doAsStudent">Do Mindspark</span></div>
						</div>
					</div>
					
					</div>
				</div>
			<?php } } ?>
			</div></div>
			<?php			
			} 
			?> 
			
			
			
			<?php 
			if(count($teacherTopicActivatedAll)>0){
			$teacherTopicActivatedAll = array_reverse($teacherTopicActivatedAll);
			?>
			<div class="questionContainer" id="activateTopicAll">
			<?php 
				$i=0;
				foreach($teacherTopicActivatedAll as $ttCode=>$ttDetails)
				{
					$isCustom = 0;
					if($teacherTopicActivatedAll[$ttCode]['isCustom'] == 1)
					{
						$isCustom = 1;
					}

					
		    		$i++;

					$i++;
					$ttName	=	$ttDetails["ttName"];
					$flow	=	$ttDetails["flow"];
					$isCoteacher = $ttDetails["isCoteacher"];
					if(($ttDetails["gradeSequence"]==0 || $ttDetails["gradeSequence"]==3) && $checkGrade2==0){
					$checkGrade2=1;
					echo '<div class="otherGrades" >Other Grades</div>';
					//echo '<table bgcolor="#00FF99" class="pagingTable" style="color:red;border-bottom: 1px solid red;">
		        		//<tbody><tr><td width="35%">Other Grades</td>
							//</tr></tbody></table><br/><br/>';
				}
					?>
				<div class="topicContainer" id="deactive<?=$i?>">
					<div class="actionsTab linkPointer" id="actionTab<?=$i?>"><span id="actionsText" class="linkPointer">Actions</span></div>
					<div id="outerCircle" class="outerCircle" title="Average topic progress of class">
						<div id="percentCircle" class="progressCircle forHighestOnly circleColor<?=round($ttProgress[$ttCode]/10)?>"><?=round($ttProgress[$ttCode],1)?>%</div>
					</div>
					<div id="topicBoxDescTopic">
						<!--  <a href="topicProgress.php?ttCode=<?=$ttCode?>&cls=<?=$class?>&section=<?=$section?>"><?=$ttName?></a>-->
						<a class="topicBoxName" href="topicReport.php?schoolCode=<?=$schoolCode?>&cls=<?=$class?>&sec=<?=$section?>&topics=<?=$ttCode?>&mode=0&topicName=<?= rawurlencode($ttName);?>"  title="<?=stripcslashes($ttName)?>"><?=$ttName?></a>						
						<?php if($isCoteacher == 1)
							{ ?>
								<img src='assets/co-teacher/co_teacher_tag.png' class="coTeacherImg" id="tooltipTab<?=$i?>">
								<div class="arrow-white-side tooltipTab<?=$i?>" style="margin-right:-145px;">
									<div id="activateTab">
										Empowered for students to learn the topic on their own.
									</div>
								</div>
								<?php } ?>
					</div>
					
					
					<div id="topicBoxDescInfo1" >
						Grade: <?=$ttDetails["grade"]?>
					</div>
					
					<div id="topicBoxDescInfo1" ><!--
						Activated on <?=setDateFormate($ttDetails["activationDate"])?>-->
						<?=$studentAttempted[$ttCode]?> out of <?=count($userIDs)?> students attempting
					</div>
	
					<?php $activeSince	=	getDaysTillActivated($ttDetails["activationDate"]); ?>
					<div id = "topicBoxDescInfo_custom" > 
					<div id="topicBoxDescInfo2" <?php if($activeSince>30) echo "style='cursor:default;'";else echo "style='cursor:default;'";  ?>> </div>
						<?php if($ttDetails["deactivationDate"] !=""){ 
						
							$activeSince1	=	getDaysTillActivatedDeactive($ttDetails["activationDate"],$ttDetails["deactivationDate"]); ?>
						<!--Deactivated on <?=setDateFormate($ttDetails["deactivationDate"])?>-->
						Was active from <?php echo (setDateFormate($ttDetails["activationDate"]) . " to " . setDateFormate($ttDetails["deactivationDate"])); echo " (" . $activeSince1;  if($activeSince1==1 || $activeSince1==0) echo " Day)"; else echo " Days)" ?> 
						<?php } else{
						$activeSince	=	getDaysTillActivated($ttDetails["activationDate"]); ?>
						<span <?php if($activeSince>30) echo "style='color:red;cursor:pointer;' title='Activated on ".setDateFormate($ttDetails['activationDate']).". It is not advisable to have a topic active for more than 30 days.'"; ?>>Activated on <?php echo (setDateFormate($ttDetails["activationDate"]) . " ("); echo ($activeSince); if($activeSince==1 || $activeSince==0) echo " Day ago)"; else echo " Days ago)"?>  <?php if($activeSince>30){ ?><sup> ?</sup><?php } ?></span>
						<?php } ?>
					
					</div>
					<?php
						$activatedForSections=array();
						foreach ($ttDetails['activatedForSections'] as $key => $value) {
							$activatedForSections[]=$value;//str_replace(" ", "", $value);
						}
						global $sectionArray;
						$thisClassSections=$sectionArray[$class];
						if(strlen($thisClassSections)==0) $thisClassSections="''";
						$thisClassSections=explode(",", $thisClassSections);
						$availableForSections=array();
						foreach ($thisClassSections as $key => $value) {
							if(!in_array(str_replace("'", "", $value), $activatedForSections))
								$availableForSections[]=str_replace("'", "", $value);
						}

					?>
					<?php if($ttDetails["deactivationDate"] == "") { ?>
					<div class="arrow-black-side actionTab<?=$i?>" style="margin-right:-145px;">
						<div id="activateTab"> 
							<div class="activate"></div>
							<div class="bulkActivateDeactivatePrompt" id="bulkADPrompt<?=$i?>">
								<div class="bulkADMain"></div>
								<div class="bulkADHelp"></div>
								<div class="bulkADActionButton"></div>
							</div>
							<div id="activate" class="tabText linkPointer">
								<?php
								if (count($activatedForSections)==1 && $activatedForSections[0]==$section){
									?>
								<span class="activateSpan" onClick="activateDeactivateTopic('<?=$ttCode?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','deactivate','deactive<?=$i?>',0,'','',<?=$ttDetails['isCoteacher']?>,'<?=$ttName?>')" id="<?=$ttCode?>">Deactivate</span>
									<?php
								}
								else{
									?>
								<span class="deactivateSpan" rel="openBulkPopup" onClick="openActivateDeactivatePrompt(this,'<?=$ttCode?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','deactivate','deactive<?=$i?>','<?=implode(',',$activatedForSections)?>','bulkADPrompt<?=$i?>',<?=$durationPopup?>,<?=$ttDetails['isCoteacher']?>,'<?=$ttName?>');" id="<?=$ttCode?>">Deactivate</span>
									<?php
								}
								?>
							</div>
							<div class="customize"></div>
							<div id="customize" class="tabText linkPointer">
								<span onClick="showMapping('<?=$ttCode?>','<?=$class?>','<?=$section?>','<?=$flow?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>')">See/Customize</span>
							</div>
							<div id="download" class="tabText linkPointer" onClick="doAsAStudent('<?=$ttCode?>','<?=$flow?>')"><span class="doAsStudent">Do Mindspark</span></div>
							
						</div>
					</div>
					<?php } 
					else { ?>
					
					<div class="arrow-black-side actionTab<?=$i?>" style="margin-right:-145px;">
						<div id="activateTab">
							<div class="activate"></div>
							<div class="bulkActivateDeactivatePrompt" id="bulkADPrompt<?=$i?>">
								<div class="bulkADMain"></div>
								<div class="bulkADHelp"></div>
								<div class="bulkADActionButton"></div>
							</div>
							<div id="activate" class="tabText linkPointer" <?php if(checkForClusters($ttCode,$flow)==0) echo "style='visibility:hidden'";?>>
							<?php if($currentActivated>=15) { ?>
							<span class="activateSpan" onClick="activateLimitOver()" id="<?=$ttDetails[2]?>">Activate</span>
							<?php }
							else  {  

								if ((count($availableForSections)==1 && $availableForSections[0]==$section) || count($thisClassSections)==1 && $thisClassSections[0]=="''"){
								?>
								<span class="activateSpan" onClick="openDateOfTopicPrompt('<?=$ttCode?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','<?=$flow?>','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','activate','topicContainer<?=$i?>','bulkADPrompt<?=$i?>')" id="<?=$ttDetails[2]?>">Activate</span>
									<?php
								}
								else{
									?>
								<span class="activateSpan" rel="openBulkPopup" onClick="openActivateDeactivatePrompt(this,'<?=$ttCode?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','<?=$flow?>','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','activate','topicContainer<?=$i?>','<?=implode(',',$availableForSections)?>','bulkADPrompt<?=$i?>',0,0,'<?=$ttDetails[0]?>')" id="<?=$ttDetails[2]?>">Activate</span>
									<?php
								}
								?>
							<?php } ?> 
							</div>
							<div class="customize"></div>
							<div id="customize" class="tabText linkPointer">
								<span onClick="showMapping('<?=$ttCode?>','<?=$class?>','<?=$section?>','<?=$flow?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>',1)">See/Customize</span>
							</div>
							<div id="download" class="tabText linkPointer" onClick="doAsAStudent('<?=$ttCode?>','<?=$flow?>')"><span class="doAsStudent">Do Mindspark</span></div>
						</div>
					</div>
					
					<?php } ?>
				</div>
			<?php } ?> 	
			</div>
			<?php			
			} 
	
			?>
			</div>
	</div>

	<div id="dialog-ws-confirm">
	  <p></p>
	</div>
	<div id="dialog-ws-confirm-prompt">
		<p></p>
	</div>	
	<div id="customConfirm">
		  <p>
		  	<span id="topic-name">		  		
		  	</span>
		  	<br>
		  	<div class="box-style" style="display:none;">
		  		<span id="alert-details"></span> 
		  		<br><br>
		  		Are you sure you want to deactivate the topic?
		  	</div>
		  </p>
	</div>		   
<?php include("footer.php") ?>

<?php
/*function getTeacherTopicProgress($ttCodeArray,$userIDArray)
{
	$userIDstr	=	implode(",",$userIDArray);
	foreach($ttCodeArray as $ttCode)
	{		 
		$sq	=	"SELECT userID,MAX(progress) FROM ".TBL_TOPIC_STATUS." WHERE teacherTopicCode='$ttCode'
				 AND userID IN ($userIDstr) GROUP BY userID";
		$rs	=	mysql_query($sq);
		$userttProgress	=	array();
		while($rw=mysql_fetch_array($rs))
		{
			$userttProgress[]	=	$rw[1];
		}
		$topicProgress[$ttCode]	=	round(array_sum($userttProgress)/count($userIDArray),2);
	}
	return $topicProgress;
}*/

function getStudentAttempted($ttCodeArray,$userIDs,$class){
	$attemptedArray=array();
	foreach($ttCodeArray as $ttCode){
		$attemptedArray[$ttCode]=0;
		foreach($userIDs as $userID){
			$query = "select count(distinct srno) from ".TBL_QUES_ATTEMPT."_class$class where userID='$userID' and teacherTopicCode='$ttCode'";
			$r = mysql_query($query);
			$l = mysql_fetch_array($r);
            if($l[0]>0)
			{
				$attemptedArray[$ttCode]++;
			}
		}
	}
	return $attemptedArray;
}

function getTeacherTopicProgress($ttCodeArray,$userIDArray,$class)
{
	$userIDstr	=	implode(",",$userIDArray);
	$topicProgress=array();
	foreach($ttCodeArray as $ttCode)
	{
		$q = "SELECT distinct flow FROM ".TBL_TOPIC_STATUS." WHERE  userID in (".$userIDstr.") AND teacherTopicCode='".$ttCode."'";
		$r = mysql_query($q);
		while($l = mysql_fetch_array($r))
		{
			$flowN = $l[0];
			$flowStr = str_replace(" ","_",$flowN);
			${"objTopicProgress".$flowStr} = new topicProgress($ttCode, $class, $flowN, SUBJECTNO);
		}
		
		$sq	=	"SELECT userID,MAX(progress),flow,result FROM ".TBL_TOPIC_STATUS." WHERE teacherTopicCode='$ttCode'
				 AND userID IN ($userIDstr) GROUP BY userID";
		$rs	=	mysql_query($sq);
		$userttProgress	=	array();
		
		while($rw=mysql_fetch_array($rs))
		{
			$sqProgress	=	"SELECT srno FROM ".TBL_CURRENT_STATUS." WHERE progressUpdate=0 AND teacherTopicCode='$ttCode' AND userID=".$rw[0];
			$rsProgress	=	mysql_query($sqProgress);
			if($rwProgress=mysql_fetch_assoc($rsProgress))
				$userttProgress[$rw[0]]	=	$rw[1];
			else
			{
				$flowK = $rw[2];
				$flowStr = str_replace(" ","_",$flowK);
				${"objTopicProgress".$flowStr} = new topicProgress($ttCode, $class, $flowK, SUBJECTNO);
				$userttProgress[$rw[0]] = ${"objTopicProgress".$flowStr}->getProgressInTT($rw[0]);
			}
		}
		$topicProgress[$ttCode]	=	round(array_sum($userttProgress)/count($userIDArray),2);
	}
	return $topicProgress;
}

function getTTsActivatedN($cls, $schoolCode, $section,$masterTopic,$mode="active",$limit=0,$coteacherInterfaceFlag)
{
	global $sectionArray;
	$thisClassSections=$sectionArray[$cls];
	if(strlen($thisClassSections)==0) $thisClassSections="''";
	
    $query = /*"SELECT A.teacherTopicCode,teacherTopicDesc,GROUP_CONCAT(IF(A.section='$section',A.activationDate,'') ORDER BY A.srno) 'activationDate',GROUP_CONCAT(IF(A.section='$section',A.deactivationDate,'') ORDER BY A.srno) 'deactivationDate',GROUP_CONCAT(IF(A.section='$section',flow,'') ORDER BY A.srno) 'flow',GROUP_CONCAT(IF(A.section='$section',A.priority,'') ORDER BY A.srno) 'priority', B.customTopic, GROUP_CONCAT(CONCAT(' ',A.section,' ') ORDER BY A.srno)  activatedFor  
    		FROM adepts_teacherTopicActivation A , adepts_teacherTopicMaster B  
		      WHERE A.schoolcode='$schoolCode' AND A.class='$cls' AND A.section IN ($thisClassSections) AND A.teacherTopicCode=B.teacherTopicCode AND B.live=1"*/

		      "SELECT A.teacherTopicCode,teacherTopicDesc,A.activationDate,A.deactivationDate,flow,A.priority, B.customTopic,B.parentTeacherTopicCode,B.customCode FROM adepts_teacherTopicActivation A , adepts_teacherTopicMaster B  
		      		      WHERE A.schoolcode='$schoolCode' AND A.class='$cls' AND A.section='$section' AND A.teacherTopicCode=B.teacherTopicCode AND B.live=1";
	if($mode=="active")
	{
		$query .= " AND ISNULL(deactivationDate)";
	}

	if($mode=="priority")
	{
		$query .= " AND ISNULL(deactivationDate)";
	}
		
	if($masterTopic!="")
	{
		$query .= " AND classification='$masterTopic'";
	}
	//$query .= " GROUP BY A.teacherTopicCode ";
	//$query .= " HAVING FIND_IN_SET(' ".$section." ',activatedFor) ";
	$query .= " ORDER by A.priority,A.activationDate,A.lastModified,A.srno";
	if($limit != 0)
		$query .= " LIMIT $limit";
	//echo $query;
    $result = mysql_query($query) or die(mysql_error());
	
	
    $ttAttemptedArray = array();
	$ttCodeArr = array();
	
    while ($line = mysql_fetch_array($result))
    {
		$pos=0;
		$ttCode=$line[0];
		if(!(in_array($ttCode,$ttCodeArr))) {

			$clsLevelArray  = getClassLevel($ttCode,$line[4]);
			$clsLevel = "";
			if(count($clsLevelArray)>0)
			  $clsLevel = implode(",",$clsLevelArray);
			$class_explode = explode(",",$clsLevel);
			$max_grade = max($class_explode);
			$min_grade = min($class_explode);
			for($a=$min_grade;$a<=$max_grade;$a++){
				if($a==$cls){
					$pos=1;
				}
			}
			if ($max_grade==$min_grade)
			{
				$ttAttemptedArray[$ttCode]["grade"] = $min_grade;
				$ttAttemptedArray[$ttCode]["gradeSequence"] = 0;
			}
			else
			{
				if($pos!=1){
					$ttAttemptedArray[$ttCode]["gradeSequence"] = 0;
				}else{
					$ttAttemptedArray[$ttCode]["gradeSequence"] = 1;
				}
				$ttAttemptedArray[$ttCode]["grade"] = $min_grade."-".$max_grade;
			}
			if($max_grade=="" && $min_grade==""){
				$ttAttemptedArray[$ttCode]["grade"] = $cls;
				$ttAttemptedArray[$ttCode]["gradeSequence"] = 2;
			}
			if ($max_grade == $cls && $min_grade == $cls)
			{
				$ttAttemptedArray[$ttCode]["grade"] = $cls;
				$ttAttemptedArray[$ttCode]["gradeSequence"] = 2;
			}
		
			$ttAttemptedArray[$ttCode]["ttName"]	= $line[1];
			$ttAttemptedArray[$ttCode]["activationDate"]	= $line[2];
			if($line[3]=="0000-00-00")
				$ttAttemptedArray[$ttCode]["deactivationDate"]	= "";
			else
				$ttAttemptedArray[$ttCode]["deactivationDate"]	= $line[3];
			if($line[4]!="")
				$ttAttemptedArray[$ttCode]["flow"]	=	$line[4];
			else
				$ttAttemptedArray[$ttCode]["flow"]	=	"MS";
			$ttAttemptedArray[$ttCode]["priority"]	=	$line[5];

			$ttAttemptedArray[$ttCode]["isCustom"]	=	$line['customTopic'];
			
			$ttAttemptedArray[$ttCode]["isCoteacher"] = $coteacherInterfaceFlag == 1 ? checkForCoteacherTopic($ttCode,$cls,$line['customTopic'],$line['parentTeacherTopicCode'],$line[4]) : 2;
			$currentActivatedSections = getCurrentActivatedSectionList($schoolCode,$cls,$ttCode,$thisClassSections);
			$topicSectionList=array_unique($currentActivatedSections);
			sort($topicSectionList);
			$ttAttemptedArray[$ttCode]["activatedForSections"]=$topicSectionList ;
			array_push($ttCodeArr,$ttCode);
		}	
		//echo $ttCode." - ";print_r($currentActivatedSections);
		
    }

		uasort($ttAttemptedArray, "sortByPriorityAndActivationDateHelper");
	/*echo "<pre>";*/
	/*print_r($ttAttemptedArray);*/
	/*exit;*/
//	if($mode=="priority")
//		classSort($ttAttemptedArray,"priority");
//	else
//		classSort($ttAttemptedArray,"gradeSequence");
	/*echo "<pre>";
	print_r($ttAttemptedArray);*/
    return $ttAttemptedArray;
}
function getCurrentActivatedSectionList($schoolCode,$cls,$ttCode,$thisClassSections){
	$query = "SELECT GROUP_CONCAT(DISTINCT A.section ORDER BY A.section)
	  FROM adepts_teacherTopicActivation A , adepts_teacherTopicMaster B  
	  WHERE A.schoolcode='$schoolCode' AND A.class='$cls' AND A.section IN ($thisClassSections) AND A.teacherTopicCode=B.teacherTopicCode AND B.live=1 AND ISNULL(A.deactivationDate) AND A.teacherTopicCode='$ttCode'";
	  //echo $query;
	  $result = mysql_query($query); 
	  if($line=mysql_fetch_array($result))
	  	return explode(",", $line[0]);
	  else return array();
}
function sortByPriorityAndActivationDateHelper($a, $b) {
    if($a['priority'] < $b['priority']) {
        return 1;
    }  elseif($a['priority'] > $b['priority']) {
        return -1;
    } else {
        return sortByActivationDateHelper($a, $b);
    }
}

function sortByActivationDateHelper($a, $b) {
	if(strcmp($a['activationDate'], $b['activationDate']) ==0) {
			return 0;
		} 
	 else if (strcmp($a['activationDate'], $b['activationDate']) < 0) {
		return -1;
	} else {
		return 1;
	}
}

function getCurrentTTsActivated($class, $schoolCode, $section)
{
	$topicsActivated = 0;
	$query = "SELECT COUNT(srno) FROM adepts_teacherTopicActivation WHERE schoolcode=$schoolCode AND class=$class AND section='$section' AND deactivationDate='0000-00-00'";
	$rs = mysql_query($query) or die(mysql_error().$query);
	if($rw = mysql_fetch_array($rs))
	{
		$topicsActivated = $rw[0];
	}
	return $topicsActivated;
}

function classSort (&$array, $key) {
    $sorter=array();
    $ret=array();
    reset($array);
    foreach ($array as $ii => $va) {
        $sorter[$ii]=$va[$key];
    }
    asort($sorter);
    foreach ($sorter as $ii => $va) {
        $ret[$ii]=$array[$ii];
    }
    $array=$ret;
}

function teacherTopicNeverActivated($schoolCode,$cls,$section,$masterTopic,$defaultFlow,$customizedTopicsArray,$savedMappingArray, $teacherName="",$coteacherInterfaceFlag)
{
	global $sectionArray;
	$thisClassSections=$sectionArray[$cls];
	if(strlen($thisClassSections)==0) $thisClassSections="''";

	$query  = "SELECT tm.teacherTopicDesc, tm.teacherTopicCode, GROUP_CONCAT(if(ta.schoolCode=$schoolCode AND ta.class=$cls,ta.section,NULL)) activatedFor
				FROM adepts_teacherTopicMaster tm LEFT JOIN adepts_teacherTopicActivation ta ON tm.teacherTopicCode=ta.teacherTopicCode 
	           WHERE  tm.live=1 AND tm.customTopic=0 AND tm.subjectno=".SUBJECTNO." AND tm.teacherTopicCode 
			   NOT IN (SELECT DISTINCT(teacherTopicCode) FROM adepts_teacherTopicActivation WHERE schoolCode=$schoolCode AND class=$cls";
	if ($section!="")
	{
		$query .= " AND section='$section'";
	}
	$query.= ") ";

	if($masterTopic!="")
	{
		$query .= " AND tm.classification='$masterTopic'";
	}
	$query.= " GROUP BY tm.teacherTopicCode";
	$query	.=	" ORDER BY tm.classification, tm.teacherTopicOrder";	
	$result = mysql_query($query);
	$broadTopics = $teacherTopics = array();
	$topic = "";

	$temp_rows = mysql_num_rows($result);
	while ($line=mysql_fetch_array($result))
	{
		$pos=0;
		$ttCode = $line['teacherTopicCode'];
		$teacherTopics[$ttCode][0] = $line[0];
		$teacherTopics[$ttCode][2] = $ttCode;
		
		$sectionList	=	explode(",",$line[2]);
		/*for($lafs=0;$lafs<count($sectionList);$lafs++){
			$sectionList[$lafs]=str_replace(" ", "", $sectionList[$lafs]);
		}*/
		$topicSectionList=array_unique($sectionList);
		sort($topicSectionList) ;

		$teacherTopics[$ttCode]['activatedForSections'] = $topicSectionList;
		$clsLevelArray  = getClassLevel($line['teacherTopicCode'],$defaultFlow);
		$clsLevel = "";
		if(count($clsLevelArray)>0)
		{
			$clsLevel = implode(",",$clsLevelArray);
			$teacherTopics[$ttCode][3] = $clsLevel;
		}
		else
			$teacherTopics[$ttCode][3] = 0;
		if(isset($savedMappingArray[$ttCode]))
			$teacherTopics[$ttCode][1] = $savedMappingArray[$ttCode];
		elseif (isset($topicsFollowingOldFlow) && in_array($ttCode, $topicsFollowingOldFlow))
			$teacherTopics[$ttCode][1] = "MSOld";
		else
			$teacherTopics[$ttCode][1] = $defaultFlow;				
	    $teacherTopics[$ttCode]['isCoteacher'] = $coteacherInterfaceFlag == 1 ? checkForCoteacherTopic($ttCode,$cls,0,'',$defaultFlow) : 2;
	    if(in_array($ttCode,array_keys($customizedTopicsArray)))
	    {
            $tmpTopicArray = $customizedTopicsArray[$ttCode];
            foreach ($tmpTopicArray as $key=>$arrDetails)
            {
            	$customizedBy="";
				$query2= "select if(A.customizedBy='',C.lastModifiedby,A.customizedBy) as customizedBy,B.customTopic,B.parentTeacherTopicCode,B.customCode,C.flow from adepts_customizedTopicDetails A inner join adepts_teacherTopicMaster B on A.code=B.customCode left join adepts_teacherTopicActivation C on B.teacherTopicCode=C.teacherTopicCode where B.teacherTopicCode='".$arrDetails[0]."' AND B.live=1 group by B.teacherTopicCode";
				$result2 = mysql_query($query2);
				while ($line1=mysql_fetch_array($result2))
				{
					$customizedBy = $line1[0];
					$customTopic = $line1[1];
					$parentTeacherTopicCode = $line1[2];
					$customCode = $line1[3];
					$customFlow = $line1[4] !='' ? $line1[4] : 'Custom - '.$customCode;				
				}
				// if($customizedBy == ""){
				// 	$query1= "select lastModifiedby,flow from adepts_teacherTopicActivation where teacherTopicCode='".$arrDetails[0]."'";
				// 	$result1 = mysql_query($query1);
				// 	while ($line1=mysql_fetch_array($result1))
				// 	{
				// 		$customizedBy = $line1[0];
				// 	}
				// }
	        	$teacherTopics[$arrDetails[0]][0] = $arrDetails[1];
	     		$teacherTopics[$arrDetails[0]][1] = "Custom - ".$arrDetails[2];
				$teacherTopics[$arrDetails[0]][2] = $arrDetails[0];
				$teacherTopics[$arrDetails[0]][3] = $arrDetails[3];
				$teacherTopics[$arrDetails[0]][4] = $customizedBy;

				if(strcmp($customizedBy, $teacherName)==0) {
					$teacherTopics[$arrDetails[0]]['category'] = '1-customizedByYou';
				} else {
					$teacherTopics[$arrDetails[0]]['category'] = '2-customizedByOthers';		
				}				
				
	    		$teacherTopics[$arrDetails[0]]['isCoteacher'] = $coteacherInterfaceFlag == 1 ? checkForCoteacherTopic($arrDetails[0],$cls,$customTopic,$parentTeacherTopicCode,$customFlow) : 2;	
        	}        	
			unset($customizedTopicsArray[$ttCode]);
	    }

	  if(isOtherGrades($teacherTopics[$ttCode][3], $cls)) {
	  	$teacherTopics[$ttCode]['category'] = '4-otherGrades';	  	
	  } else {
	  	$teacherTopics[$ttCode]['category'] = '3-notCustomized';
	  }

		$innerarray = $teacherTopics[$ttCode];
		$class_explode = explode(",",$innerarray[3]);
		$max_grade = max($class_explode);
		$min_grade = min($class_explode);
		for($a=$min_grade;$a<=$max_grade;$a++){
			if($a==$cls){
				$pos=1;
			}
		}
		if ($max_grade==$min_grade)
		{
			$teacherTopics[$ttCode][3] = $min_grade;
			if($pos!=1){
				$teacherTopics[$ttCode]["gradeSequence"] = 3;
			}else{
				$teacherTopics[$ttCode]["gradeSequence"] = 1;
			}
		}
		else
		{
			if($pos!=1){
				$teacherTopics[$ttCode]["gradeSequence"] = 3;
			}else{
				$teacherTopics[$ttCode]["gradeSequence"] = 2;
			}
			$teacherTopics[$ttCode][3] = $min_grade."-".$max_grade;
		}
		if($max_grade=="" && $min_grade==""){
			$teacherTopics[$ttCode][3] = $cls;
			$teacherTopics[$ttCode]["gradeSequence"] = 1;
		}
		
		//$teacherTopics[$ttCode]['category'] = '3-notCustomized';
	}
	// echo "<pre>";
	// print_r($teacherTopics);
	// exit;
	foreach($customizedTopicsArray as $ttCode=>$tmpTopicArray)
	{
		foreach($tmpTopicArray as $key=>$tmpCustomTopicArray)
		{
			$query2= "select if(A.customizedBy='',C.lastModifiedby,A.customizedBy) as customizedBy,B.customTopic,B.parentTeacherTopicCode,B.customCode,C.flow from adepts_customizedTopicDetails A inner join adepts_teacherTopicMaster B on A.code=B.customCode join adepts_teacherTopicActivation C on B.teacherTopicCode=C.teacherTopicCode where B.teacherTopicCode='".$tmpCustomTopicArray[0]."' AND B.live=1 group by B.teacherTopicCode";
			$result2 = mysql_query($query2);
			while ($line1=mysql_fetch_array($result2))
			{
				$customizedBy = $line1[0];
				$customTopic = $line1[1];
				$parentTeacherTopicCode = $line1[2];
				$customCode = $line1[3];
				$customFlow = $line[4]!= '' ? $line[4] : 'Custom - '.$customCode ;
			}
			if($customizedBy == ""){
				$query1= "select lastModifiedby from adepts_teacherTopicActivation where teacherTopicCode='".$tmpCustomTopicArray[0]."'";
				$result1 = mysql_query($query1);
				while ($line=mysql_fetch_array($result1))
				{
					$customizedBy = $line[0];
				}
			}
			$teacherTopics[$tmpCustomTopicArray[0]][4] = $customizedBy;
			$teacherTopics[$tmpCustomTopicArray[0]][0] = $tmpCustomTopicArray[1];
			$teacherTopics[$tmpCustomTopicArray[0]][1] = "Custom - ".$tmpCustomTopicArray[2];
			$teacherTopics[$tmpCustomTopicArray[0]][2] = $tmpCustomTopicArray[0];
			$teacherTopics[$tmpCustomTopicArray[0]][3] = $tmpCustomTopicArray[3];
			$teacherTopics[$tmpCustomTopicArray[0]]["gradeSequence"] = 1;

			$teacherTopics[$tmpCustomTopicArray[0]]['isCoteacher'] = $coteacherInterfaceFlag == 1 ? checkForCoteacherTopic($tmpCustomTopicArray[0],$cls,$customTopic,$parentTeacherTopicCode,$customFlow) : 2;			
			if(strcmp($customizedBy, $teacherName)==0) {
				$teacherTopics[$tmpCustomTopicArray[0]]['category'] = '1-customizedByYou';
			} else {
				$teacherTopics[$tmpCustomTopicArray[0]]['category'] = '2-customizedByOthers';		
			}
		}
	}	
	// echo "<pre>";
	// print_r($teacherTopics); exit;
	uasort($teacherTopics, "ttSortHelper");

	return $teacherTopics;
}

function isOtherGrades($grades, $cls) {
	$isOtherGrades = 1;
	$gradeArr = explode(',', $grades);
	if(sizeof($gradeArr) == 1) {
		if($gradeArr[0] == $cls) {			
			$isOtherGrades = 0;
		}
	} else {
		if($gradeArr[0] <= $cls && $gradeArr[(sizeof($gradeArr)) - 1] >= $cls) {
			$isOtherGrades = 0;
		}
	}
	return $isOtherGrades;
}

function ttSortHelper($a, $b) {
	if(strcmp($a['category'], $b['category']) ==0) {
		if($a[3] == $b[3]) {
			if(strcmp($a[0], $b[0]) < 0) {
				//
				return -1;
			} else if (strcmp($a[0], $b[0]) < 0){
				return 0;
			} else {
				return 1;
			}
		} else if($a[3] < $b[3]) {
			return -1;
		} else {
			return 1;
		}
	} else if (strcmp($a['category'], $b['category']) < 0) {
		return -1;
	} else {
		return 1;
	}
}

function checkForClusters($ttCode,$flow)
{
	$num=1;
	if(strtoupper($flow)=="MS" || strtoupper($flow)=="CBSE" || strtoupper($flow)=="ICSE" || strtoupper($flow)=="IGCSE")
	{
		$sq		=	"SELECT B.clusterCode FROM adepts_teacherTopicClusterMaster A, adepts_clusterMaster B
					 WHERE teacherTopicCode='$ttCode' AND status='Live' AND A.clusterCode=B.clusterCode AND ".$flow."_level <>''";
		$rs		=	mysql_query($sq);
		$num	=	mysql_num_rows($rs);
	}
	return $num;
}

?>
