<?php 
	//error_reporting(0);
	include("header.php") ;
  include ("../userInterface/classes/clsTeacherTopic.php");
	include("functions/functions.php");
	include("classes/testTeacherIDs.php");
  $userID = $_SESSION['userID'];
  $schoolCode = $_SESSION['schoolCode'];
	$username= $_SESSION['childName'];

    if(!isset($_REQUEST['ttCode']) || !isset($_SESSION['userID']))
    {
    	echo "You are not authorised to access this page!";
    	exit;
    }
	$query  = "SELECT username FROM adepts_userDetails WHERE userID=".$userID;
    $result = mysql_query($query) or die(mysql_error());
    $line   = mysql_fetch_array($result);
    $loginID    = $line['username'];

    $ttCode = $_REQUEST['ttCode'];
	
	$ttDesc = $cls = $section = "";
	if(isset($_REQUEST['cls']))
		$cls = $_REQUEST['cls'];
	if(isset($_REQUEST['ttDesc']))
		$ttDesc = $_REQUEST['ttDesc'];
	if(isset($_REQUEST['section']))
		$section = $_REQUEST['section'];
	if(isset($_REQUEST['flow']))
	{
		$flow = $_REQUEST['flow'];
		if(substr($flow,0,6)=="Custom")
			$flow = "Custom";
	    else if($flow=="")
	       $flow = "MS";
	}
	else
		$flow = "MS";
		
	$topicsActivated	=	getTTsActivated($cls,$schoolCode,$section);	
	$liveClusterList	=	disableLiveClusters($ttCode,$schoolCode,$cls,$section,$flow);
	$interface	=	"";

	
	if((isset($_POST['save']) || isset($_POST['save_activate']) || isset($_POST["saveAndActivate"])) && !in_array($loginID,$testIDArray))
	{
		$flag = 1;
		$flow = $_POST['rdTTFlow'];
		$newFlow = $_POST["generatedFlow"];
		
		if($flow=="MS" || $flow=="CBSE" || $flow=="ICSE" || $flow=="IGCSE")
		{
			$activeMessage = activatedTopic($schoolCode,$cls,$section,$ttCode,$flow,$loginID);
		}
		else if($newFlow!="CUSTOM" && $newFlow!="")
		{
			$activeMessage = activatedTopic($schoolCode,$cls,$section,$ttCode,$newFlow,$loginID);
		}
		else if($flow=="Custom")
		{
			$clusterArray = $_POST['chkCluster'];
			if(count($clusterArray)>0)
			{
			  $topicDetailsArr = createCustomTT($ttCode, $schoolCode, $cls, $clusterArray,$username, $ttDesc);
				$activeMessage = "Customized successfully";
				if($_POST["saveAndActivate"]==1)
				{
					$topicDetails	=	explode("~",$topicDetailsArr);
					$flowActivation = $topicDetails[0];
					$newTopicCode = $topicDetails[1];
					$activeMessage = activatedTopic($schoolCode,$cls,$section,$newTopicCode,$flowActivation,$loginID);
				}
			    /*$clusters     = implode(",",$clusterArray);
			    $customCode   = getCustomizedTopicCode($clusters, $username, $schoolCode);
			    $flow .= " - ".$customCode;*/
			}
			else
				$flag = 0;
		}
		elseif($flow!="")
		{
			saveMapping($schoolCode, $cls, $section, $ttCode, $flow);
		}
		
			
		echo "<script>";
			echo "alert('".$activeMessage."');";
			echo "window.opener.submitCheckBox();";
		echo "self.close();";
		echo "</script>";
	}

	if(strcasecmp($flow,"Custom")==0)
	{
		$clustersChosenArray = getClustersChosen($schoolCode,$cls,$section,$ttCode);

	}
	//Get the no of attempts on this topic for the class, if more than 0 i.e. students have started then disable the option of changing the mapping.
	$noOfAttempts = getNoOfAttempts($schoolCode, $cls, $section, $ttCode);

	$customizedTopic = isCustomizedTopic($ttCode);

    if($customizedTopic=="")
    {
    	$query = "SELECT a.clusterCode, cluster, ms_level, cbse_level, icse_level,igcse_level, a.level, b.clusterType
    	          FROM   adepts_teacherTopicClusterMaster a, adepts_clusterMaster b
    	          WHERE  a.clusterCode=b.clusterCode AND status='Live' AND teacherTopicCode='$ttCode'
    	          ORDER BY a.flowno";
    }
    else
    {
        $query = "SELECT a.clusterCode, cluster, ms_level, cbse_level, icse_level,igcse_level, a.level, b.clusterType
    	          FROM   adepts_teacherTopicClusterMaster a, adepts_clusterMaster b
    	          WHERE  a.clusterCode=b.clusterCode AND status='Live' AND teacherTopicCode='$customizedTopic'
    	          ORDER BY a.flowno";
    }


	$result = mysql_query($query);
	$clusterDetails = array();
	$srno = 0;
	$ms_clusters = $cbse_clusters = $icse_clusters = $igcse_clusters = 0;
	$totalSdls = 0;
	$avgTimePerSdl;
	$objTT = new teacherTopic($ttCode,$cls,$_REQUEST['flow']);     //create the object for TTcode, class, flow combination:  and childSection = '$childsection'
	$teacherTopicDesc = $objTT->ttDescription;

	while ($line = mysql_fetch_array($result))
	{
		$avgTimePerSdl = $objTT->avgTimePerSdl;

		$clusterCode              = $line['clusterCode'];
		$sdlPerCluster = $objTT->getNumberOfSDLs($clusterCode);
		$clusterDetails[$srno][0] = $clusterCode;
		$clusterDetails[$srno][1] = $line['cluster'];
		$clusterDetails[$srno][2] = 0;					//ms
		$clusterDetails[$srno][3] = 0;					//cbse
		$clusterDetails[$srno][4] = 0;					//icse
		$clusterDetails[$srno][5] = 0;					//customized
		$clusterDetails[$srno][6] = 0;					//MS Old flow
		$clusterDetails[$srno][7] = $sdlPerCluster;     //Storing no. of sdls per cluster.
		$clusterDetails[$srno][8] = $line['clusterType'];     //Storing cluster type.
		$clusterDetails[$srno][9] = 0;					//igcse
		$clusterLevel = $line['ms_level'];
		$clusterLevelArray = explode(",", $clusterLevel);
		if(in_array($cls, $clusterLevelArray))
		{
			$clusterDetails[$srno][2] = 1;
			$ms_clusters++;
		}

		$clusterLevel = $line['cbse_level'];
		$clusterLevelArray = explode(",", $clusterLevel);
		if(in_array($cls, $clusterLevelArray))
		{
			$clusterDetails[$srno][3] = 1;
			$cbse_clusters++;
		}

		$clusterLevel = $line['icse_level'];
		$clusterLevelArray = explode(",", $clusterLevel);
		if(in_array($cls, $clusterLevelArray))
		{
			$clusterDetails[$srno][4] = 1;
			$icse_clusters++;
		}
		$clusterLevel = $line['igcse_level'];
		$clusterLevelArray = explode(",", $clusterLevel);
		if(in_array($cls, $clusterLevelArray))
		{
			$clusterDetails[$srno][9] = 1;
			$igcse_clusters++;
		}
		if(strcasecmp($flow,"Custom")==0 && in_array($clusterCode,$clustersChosenArray))
		{
			$clusterDetails[$srno][5] = 1;
		}
		else
			$clusterDetails[$srno][5] = 0;

		//This is for the schools where the topics are activated as per the old flow
		//This case will happen during the transition to the new system, can be removed after all such cases are done with after some time
		$clusterLevel = $line['level'];
		$clusterLevelArray = explode(",", $clusterLevel);
		if(in_array($cls,$clusterLevelArray))
		{
			$clusterDetails[$srno][6] = 1;
		}

		switch ($flow)
		{
			case "MS":
				if($clusterDetails[$srno][2] == 1)
					$totalSdls += $clusterDetails[$srno][7];
			break;
			case "CBSE":
				if($clusterDetails[$srno][3] == 1)
					$totalSdls += $clusterDetails[$srno][7];
			break;
			case "ICSE":
				if($clusterDetails[$srno][4] == 1)
					$totalSdls += $clusterDetails[$srno][7];
			break;
			case "Custom":
				if($clusterDetails[$srno][5] == 1)
					$totalSdls += $clusterDetails[$srno][7];
			break;
			case "MSOld":
				if($clusterDetails[$srno][6] == 1)
					$totalSdls += $clusterDetails[$srno][7];
			break;
			case "IGCSE":
				if($clusterDetails[$srno][9] == 1)
					$totalSdls += $clusterDetails[$srno][7];
			break;
		}

		$srno++;
	}
	$totalClusters = count($clusterDetails);

	$totalTimeForTopic = $totalSdls*$avgTimePerSdl;

?>


<title>Topic Section Remediation</title>
<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/customize_topic.css?v=2" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="../userInterface/libs/prompt.js"></script>
<script>
var clustersTotal = <?php echo json_encode($totalClusters); ?>;

var customizeCluster	=	new Array(<?=getCustomizeCluster($ttCode,$schoolCode,$cls,$section)?>);


function highlightSelection(mode,rows, noofattempts)
{
	var modeArray = new Array('MS','CBSE','ICSE','IGCSE','Custom');
	if(mode=="MSOld")
		modeArray.push("MSOld");
	var cname;
	var disabledmode = 1;
	if(mode=="Custom")
	{
		disabledmode = 0;
	}
	document.getElementById('rd'+mode).checked = true;

	for(var i=0; i<modeArray.length; i++)
	{
		cname = '';
		if(modeArray[i]==mode)
			cname ='selected';
		document.getElementById('hd'+modeArray[i]).className = cname;
		for(var j=0; j<rows; j++)
		{
			document.getElementById('row'+modeArray[i]+j).className = cname;
		}
	}

	for(var i=0; i<rows; i++)
	{
	    if(document.getElementById('chkCluster'+i))
		    document.getElementById('chkCluster'+i).disabled = disabledmode;
	}
	if(noofattempts>0)
	{
		var objArray = document.getElementsByName('rdTTFlow');
		for(var i=0; i<objArray.length; i++)
		{
		    if(objArray[i].value=="Custom")
		    {
		        continue;
		    }
			objArray[i].disabled = true;
		}
	}
	if($('.btnSave'))
		$('.btnSave').attr("disabled",false);
}

function checkIfClusterCustumize(flow, checkID)
{
	if($("#"+checkID).is(":checked") && $.inArray($("#"+checkID).val(),customizeCluster)!=-1)
	{
		if(confirm("This learning unit has already been included as a part of teacher topic customised earlier. would you like to include it again in this current customisation process?\n(NOTE - INCLUSION OF LEARNING UNITS ALREADY CUSTOMISED WILL RESULT IN REPETITION OF QUESTIONS)"))
		{
			getEstimatedTimeToCompleteTopic(flow, checkID);
		}
		else {
			$("#"+checkID).attr("checked",false);
		}
	}
	else
		getEstimatedTimeToCompleteTopic(flow, checkID);		
}


function notify()
{
	alert('This learning unit is disabled as you have already covered it earlier. To enable it, you should de-activate the topic where it is currently selected.');
	 return false;
}

function checkIfclustersAreCustomized(flow)
{
	var liveClusters	=	new Array(<?=$liveClusterList?>);
	
	for(var i=0;i<clustersTotal;i++)
	{
		
		if($.inArray($("#"+"chkCluster"+i).val(),liveClusters)!=-1 || $.inArray($("#"+"chkCluster"+i).val(),customizeCluster)!=-1)
		{
			document.getElementById('messagediv'+i).style.display = "block";
			$("#"+"chkCluster"+i).attr("disabled", true);
			

			$("#"+"chkCluster"+i).attr("title","This learning unit is disabled as you have already covered it earlier. To enable it, you should de-activate the topic where it is currently selected.");
								
			$("#"+"rowCustom"+i).attr("title","This learning unit is disabled as you have already covered it earlier. To enable it, you should de-activate the topic where it is currently selected.");
			//$("#"+"rowCustom"+i).attr("onclick","alert('This learning unit is disabled as you have already covered it earlier. To enable it, you should de-activate the topic where it is currently selected.')");
			
		}
		else
		{
			$("#"+"chkCluster"+i).attr("disabled", false);
			
		}
	}
}

function getEstimatedTimeToCompleteTopic(flow, checkID)
{
	var sdlsCorrToFlow = new Array();
	sdlsCorrToFlow[flow] = 0;
	
	var clusterDetails = document.getElementById('clusterDetailsStr').value;

	var clusterDetailsArr = clusterDetails.split("~");
	for(var i=0; i<clusterDetailsArr.length; i++)
	{ 
		var tempArr = clusterDetailsArr[i].split("##");
		
		clusterDetailsArr[i] = new Array(tempArr.length);
		for(var j=0; j<tempArr.length; j++)
		{
			clusterDetailsArr[i][j] = tempArr[j];
		}


		if(flow == "MS")
		{	
			
			if(clusterDetailsArr[i][2] == 1)
			{
				sdlsCorrToFlow[flow] += parseInt(clusterDetailsArr[i][7]);
			}
				
			
		}
		else if(flow == "CBSE")
		{
			if(clusterDetailsArr[i][3] == 1)
			{
				sdlsCorrToFlow[flow] += parseInt(clusterDetailsArr[i][7]);
			}
				
				
		}
		else if(flow == "ICSE")
		{
			if(clusterDetailsArr[i][4] == 1)
			{
				sdlsCorrToFlow[flow] += parseInt(clusterDetailsArr[i][7]);
			}
		}
		else if(flow == "IGCSE")
		{
			if(clusterDetailsArr[i][9] == 1)
			{
				sdlsCorrToFlow[flow] += parseInt(clusterDetailsArr[i][7]);
			}
		}
		else if(flow == "Custom")
		{
			var clstr = "chkCluster"+i;
			if(document.getElementById(clstr).checked)
			{
				sdlsCorrToFlow[flow] += parseInt(clusterDetailsArr[i][7]);
			}
		}
		else if(flow == "MSOld")
		{
			if(clusterDetailsArr[i][6] == 1)
				sdlsCorrToFlow[flow] += clusterDetailsArr[i][7];
		}
	}

	var avgTimePerSdl = document.getElementById('avgTimePerSdl').value;
	var TimeToCompleteTheTopic = parseFloat(avgTimePerSdl)*sdlsCorrToFlow[flow];
	TimeToCompleteTheTopic = Math.round(TimeToCompleteTheTopic*Math.pow(10,1))/Math.pow(10,1);
	var tempHTML = "<div><br/>Estimated time to complete the topic for selected flow: "+TimeToCompleteTheTopic+" minutes<br/><br/></div>"
	document.getElementById('estimation').innerHTML = tempHTML;

}

function validate(val)
{
	if(val==1)
		$("#saveAndActivate").val("1");
	else
		$("#saveAndActivate").val("0");
	var flow;
	var objArray = document.forms['frmTeacherTopicFlow'].elements['rdTTFlow'];
	radioLength = objArray.length;
	for(var i = 0; i < radioLength; i++) {
		if(objArray[i].checked) {
			flow = objArray[i].value;
			break;
		}
	}
	if(flow=='Custom')
	{
		var msClusters = 0;
		var cbseClusters = 0;
		var icseClusters = 0;
		var igcseClusters = 0;
		var msFlow = true;
		var cbseFlow = true;
		var icseFlow = true;
		var igcseFlow = true;
		$("input[name='chkCluster[]']:checked").each(function() { 
			var levelList = $(this).attr("class");
			var levelListArr = levelList.split("_");
			for(var i=0;i<4;i++)
			{
				var levelCountArr = levelListArr[i].split("@");
				if(i==0)
				{
					if(levelCountArr[1]==0)
					{
						msClusters = 0;
						msFlow = false;
					}
					else
						msClusters = parseInt(levelCountArr[1]) + parseInt(msClusters);
				}
				else if(i==1)
				{
					if(levelCountArr[1]==0)
					{
						cbseClusters = 0;
						cbseFlow = false;
					}
					else
						cbseClusters = parseInt(levelCountArr[1]) + parseInt(cbseClusters);
				}
				else if(i==2)
				{
					if(levelCountArr[1]==0)
					{
						icseClusters = 0;
						icseFlow = false;
					}
					else
						icseClusters = parseInt(levelCountArr[1]) + parseInt(icseClusters);
				}
				else if(i==3)
				{
					if(levelCountArr[1]==0)
					{
						igcseClusters = 0;
						igcseFlow = false;
					}
					else
						igcseClusters = parseInt(levelCountArr[1]) + parseInt(igcseClusters);
				}
			}
		});
		var totalLevelCountArr = $("#totalLevelCount").val().split("-");
		if(msClusters == totalLevelCountArr[0] && msClusters>0 && msFlow===true)
			$("#generatedFlow").val("MS");
		else if(cbseClusters == totalLevelCountArr[1] && cbseClusters>0 && cbseFlow===true)
			$("#generatedFlow").val("CBSE");
		else if(icseClusters == totalLevelCountArr[2] && icseClusters>0 && icseFlow===true)
			$("#generatedFlow").val("ICSE");
		else if(igcseClusters == totalLevelCountArr[3] && igcseClusters>0 && igcseFlow===true)
			$("#generatedFlow").val("IGCSE");
		else
			$("#generatedFlow").val("CUSTOM");
	    if(!isSelected())
	    {
    		alert("Please choose at least one cluster!");
			$("#saveAndActivate").val("0");
	    }
	    else
		{
		<?php if($noOfAttempts==0) { ?>	
			var generatedFlow = $("#generatedFlow").val();
			if(generatedFlow!="CUSTOM")
			{
				if(val==1)
				{
					var newMessage = "The learning units customized are same as in "+generatedFlow+" flow. Hence, activating " +generatedFlow+" flow.";
					var prompts=new Prompt({
						text:newMessage,
						type:'confirm',
						label2:'Cancel',
						label1:'Ok',
						func1:function(){
							$("#frmTeacherTopicFlow").submit();
						},
						func2:function(){
							$("#prmptContainer_createCustom").remove();
						},
						promptId:"createCustom"
					});
				}
				else
				{
					var newMessage = "The learning units customized are same as in "+generatedFlow+" flow. Hence, activate "+generatedFlow+" flow later.";
					var prompts = new Prompt({
						text:newMessage,
						type:'alert',
						label1:'Ok',
						func1:function(){
							$("input[name='chkCluster[]']").removeAttr("checked");
							$("input[name='chkCluster[]']").attr("disabled",true);
							$("#rdCustom").attr("disabled",true);
							$("#activateTopic,#seeCustomize").show();
							$("#saveCustom,#save_activate").hide();
							$("#activateTopic,#seeCustomize").show();
							$("#rdMS,#rdCBSE,#rdICSE,#rd"+generatedFlow).removeAttr("disabled");
							$("#rd"+generatedFlow).attr("checked","checked");		
							$("#prmptContainer_createCustom").remove();
						},
						promptId:"createCustom"
					});
				}
			}
			else
			{
		<?php } ?>
				$("#generatedFlow").val("CUSTOM");
				var prompts=new Prompt({
<?php if(count($topicsActivated)>=15) { ?>
					text:'Please note that this will create a new custom topic. It can be activated only after deactivating some other topics. Mindspark does not allow more than 15 topics to be active at a time. <a href="<?=WHATSNEW?>helpManual/Too_many_active_topics_in_a_school_is_hazardous_to_a_child_s_health.pdf" target="_blank">Click here</a> to know why.',
<?php } else { ?>
					text:'Please note that this will create a new custom topic. ',
<?php } ?>					
					type:'confirm',
					label2:'Ok',
					label1:'Cancel',
					func1:function(){
						$("#prmptContainer_createCustom").remove();
					},
					func2:function(){
						$("#frmTeacherTopicFlow").submit();
					},
					promptId:"createCustom"
				});
		<?php if($noOfAttempts==0) { ?>			
			}
		<?php } ?>	
		}
		return false;
	}
	else if(flow=="MS" || flow=="CBSE" || flow=="ICSE" || flow=="IGCSE")
	{
		if(val==2)
		{
		
		}
		else
		{
			var prompts=new Prompt({
				text:"Are you sure you want to activate it in "+flow+" flow?",
				type:'confirm',
				label2:'No',
				label1:'Yes',
				func1:function(){
					$("#frmTeacherTopicFlow").submit();
				},
				func2:function(){
					$("#prmptContainer_createCustom").remove();
				},
				promptId:"createCustom"
			});
			return false;
		}
	}
	else
	{
		alert("Please select custom flow.");
		return false;
	}
}

//js fun. to check if atleast one check box is selected.
function isSelected(){
	var totalchecked=0;
	var checks = document.getElementsByName('chkCluster[]');
	var boxLength = checks.length;
	for ( i=0; i < boxLength; i++ ) {
		if(checks[i].checked == true)
			totalchecked++;
    }
    if(totalchecked==0)
    	return false;
    else
    	return true;
}

function getMisConceptionQuestions(cls,section,ttCode,clusterCode)
{
	var url = 'misconceptionQuestions.php';
	url = url + "?cls="+cls+"&section="+section+"&ttCode="+ttCode+"&clusterCode="+clusterCode;

	window.open(url, '_blank');

}
function showMapping(ttCode, cls, section,flow)
{
	window.location = "customize_topic.php?ttCode="+ttCode+"&cls="+cls+"&section="+section+"&flow="+flow;
}

//for ms as student
$(document).ready(function(e) {
    $(".clusterSelection").change(function(e) {
		$(".pb").removeClass("pb");
		$(this).parents("tr:first").addClass("pb");
    });

    $("#seeCustomize").click(function() {
    	$("#friendlyNameForm").show();
    });

	$("#flow").change(function(){
		$(".showAll").show();
		$(".clusterSelection").attr("checked",false);
		$(".showAll").removeClass("pb");
		if($(this).val()=="MS")
			$(".ms_level").hide();
		else if($(this).val()=="CBSE")
			$(".cbse_level").hide();
		else if($(this).val()=="ICSE")
			$(".icse_level").hide();
		else if($(this).val()=="IGCSE")
			$(".igcse_level").hide();
	});

	//blinkText();
	$(".radio").change(function(e) {
		$(".selectNavLab").removeClass("selectNavLab");
		$(this).parent().addClass("selectNavLab");
		if($(this).val() == "questions")
		{2
			$("#startOptions").show();
			if($("input[name=ttOptions]:checked").val() == "1" || $("input[name=ttOptions]:checked").val() == "3")
				$("#moreOptions").show();
			else
				$("#moreOptions").hide();
		}
		else
		{
			$("#startOptions").hide();
			$("#moreOptions").show();
		}
    });

	$("input[name=ttOptions]").change(function(e) {
		if($(this).val() == "1" || $(this).val() == "2")
		{
			$(".customPoint").hide();
		}
		else
		{
			$(".customPoint").show();
		}
		if($(this).val() == "1" || $(this).val() == "3")
		{
			$("#moreOptions").show();
		}
		else
		{
			$("#moreOptions").hide();
		}
    });

	$("#submitBtn").click(function(e) {
		if($("input[name=msFlowSel]:checked").val() == "activities")
		{
			if($("#selClass").val() == "" || $("#flow").val() == "")
				alert("Select class and flow.");
			else
				mindsparkHandshake();
		}
		else if($("input[name=msFlowSel]:checked").val() == "questions")
		{
			if($("input[name=ttOptions]:checked").val() == 1 || $("input[name=ttOptions]:checked").val() == 3)
			{
				if($("#selClass").val() == "" || $("#flow").val() == "")
					alert("Select class and flow.");
				else if($("input[name=ttOptions]:checked").val() == 3)
				{
					if($("input[name=clusterSelection]:checked").length == 0)
						alert("Select starting cluster.");
					else
						mindsparkHandshake();
				}
				else
					mindsparkHandshake();
			}
			else if($("input[name=ttOptions]:checked").val() == 2)
			{
				mindsparkHandshake();
			}
		}
    });
	
	$("#seeCustomize").click(function(){
		$("#rdCustom").removeAttr("disabled");
		$("#rdCustom").click();
		$("#seeCustomize,#activateTopic").hide();
		$(".btnSave").show();
		$("#rdMS,#rdCBSE,#rdICSE,#rdIGCSE").attr("disabled",true);
		$('html,body').animate({ scrollTop: 0 }, 'slow', function () {
		});
	});

	$("#customizedTopicDesc").blur(function() {
		validateCustomTopicDesc();
	});

	$("#saveCustom").attr('disabled','disabled');
	$("#save_activate").attr('disabled','disabled');

});

function validateCustomTopicDesc() {
		var ctd = $("#customizedTopicDesc").val().trim();
		var alertMsg = "";
		var data = $("#customizedTopicDesc").attr('data'); 
		var tdList = data.split("~");

		if(ctd.length == 0) {
			alertMsg = "Field cannot be empty!";
			$("#saveCustom").attr('disabled','disabled');
			$("#save_activate").attr('disabled','disabled');
			alert(alertMsg);

		} else if (tdList.indexOf(ctd) != -1) {
			alertMsg = "This name already exists. Please try some other name.";
			$("#saveCustom").attr('disabled','disabled');
			$("#save_activate").attr('disabled','disabled');
			alert(alertMsg);
		} else {
			$("#saveCustom").removeAttr('disabled');
			$("#save_activate").removeAttr('disabled');
		}		
}

function mindsparkHandshake()
{
	var msgArr = new Array();
	var msgInfo="";
	msgArr["1"] = "start from beginning";
	msgArr["2"] = "continue from where last left off";
	msgArr["3"] = "start from customized point";
	var childClass = $("#selClass").val();
	if($("input[name=msFlowSel]:checked").val() == "activities")
	{
		var msg = "start with activities";
		if(confirm("Are you sure you want to "+msg+" ?"))
		{
			//window.location.href	=	"activities.php";
			$("#mode").val("ttSelection");
			$("#userType").val("teacherAsStudent");
			$("#mindsparkTeacherLogin").attr("action", "activities.php");
			$("#mindsparkTeacherLogin").submit();
		}
	}
	else if($("input[name=msFlowSel]:checked").val() == "questions")
	{
		var dispMsg	=	"";
		var msgInfo	=	"";
		var msg = msgArr[$("input[name=ttOptions]:checked").val()];
		var topicStart = $("input[name=ttOptions]:checked").val();
		var forceNew = (topicStart == 2)?"no":"yes";
		var clusterCode = $("input[name=clusterSelection]:checked").val();
		clusterCode = (topicStart != 3)?"":clusterCode;
		clusterCode = (topicStart != 3)?"":clusterCode;
		var forceFlow = $("#flow").val();
		forceFlow = (topicStart == 2)?"":forceFlow;
		if(msgInfo=="")
			dispMsg	=	"Are you sure you want to "+msg+" ?";
		else
			dispMsg	=	msgInfo;

		if(confirm(dispMsg))
		{
			$("#mode").val("ttSelection");
			$("#userType").val("teacherAsStudent");
			$("#forceNew").val(forceNew);
			$("#customClusterCode").val(clusterCode);
			$("#mindsparkTeacherLogin").attr("action", "controller.php");
			$("#mindsparkTeacherLogin").submit();
		}
	}
}

function activateDeactivateTopic(ttCode,schoolCode,cls,section,flow,modifiedBy,mode,idRemove)
{
	flow = $("input:radio[name=rdTTFlow]:checked").val();
	var minClass = "";
	var maxClass = "";
<?php
	$gradeRangeArr = explode("-",$_REQUEST["gradeRange"]);
	if($gradeRangeArr[0]!="") 
    	echo "minClass=".$gradeRangeArr[0].";";
	if($gradeRangeArr[1]!="") 
		echo "maxClass=".$gradeRangeArr[1].";";
?>
	var msg = "Are you sure you want to activate the topic?";
	if((cls < minClass && minClass !="") || (cls > maxClass && maxClass !=""))
		var msg = "Please note that these learning units are recommended for grade <?=$_REQUEST["gradeRange"]?>. Are you sure you want to activate it?";
	if(confirm(msg))
	{
		var linkTo	=	"ajaxRequest.php?mode="+mode+"&ttCode="+ttCode+"&schoolCode="+schoolCode+"&cls="+cls+"&section="+section+"&flow="+flow+"&modifiedBy="+modifiedBy;
		$.post(linkTo,function(data){
			alert(data);
			window.opener.submitCheckBox();
			self.close();
			return false;
		});
	}
}


function trackTeacher(code,type)
{
	if(type == 'activity')
		var pageId=75;
	else
		var pageId=76;
	 $.ajax({
            url: "ajaxRequest.php",
            data: "pageId="+pageId+"&mode=doasActivity",
            type: "POST",
            async: false,
            success: function(data){
				if(data == "multitab")
				{
					window.location.href = "../userInterface/newTab.php";
				}
				else if(type == 'activity')
				{
					url = "../userInterface/enrichmentModule.php?gameID="+code;
					window.open(url, '_blank');
				}
				else if(type == 'timeTest')
				{
					url = "../userInterface/timedTest.php?timedTest="+code+"&tmpMode=sample";
					window.open(url, '_blank');
				}
            }
        });
}
</script>
</head>

<body onLoad="highlightSelection('<?=$flow?>',<?=$totalClusters?>,<?=$noOfAttempts?>);">
<?php include("eiColors.php") ?>
<div id="topBar">
	<?php include("topBar.php") ?>
</div>
<div id="containerPopup">
	<div id="headerBar">
		<div id="pageName">
			<div class="arrow-black"></div>
			<div id="pageText"><?= $teacherTopicDesc." (Class-$cls)"?></div>
		</div>
	</div>
	<div id="customizeTbl" align="center">
	<form id="frmTeacherTopicFlow" name="frmTeacherTopicFlow" method="post" action="<?=$_SERVER['PHP_SELF']?>">
		<input type="hidden" id="ttCode" name="ttCode" value="<?=$ttCode?>">
		<input type="hidden" id="cls" name="cls" value="<?=$cls?>">
		<input type="hidden" id="section" name="section" value="<?=$section?>">
		
		<?php
		if(isset($flag) && $flag==0)
		{
			echo "<div style='font-color:red'>Please select atleast one cluster!</div>";
		}
		if($totalClusters>0)
		{
			$arrayToString = "";
			for ($i=0; $i<$totalClusters; $i++)
			{
				$arrayToString .= implode("##", $clusterDetails[$i]);
				$arrayToString = $arrayToString."~";
			}
			$arrayToString = substr($arrayToString, 0, -1);
		?>
			<input type="hidden" name="clusterDetailsStr" id="clusterDetailsStr" value="<?=$arrayToString?>">
			<input type="hidden" name="avgTimePerSdl" id="avgTimePerSdl" value="<?=$avgTimePerSdl?>">
			<table cellspacing="3" cellpadding="3" border="0" width="90%" class="tblContent">
				<tr>
					<th class="customPoint"></th>
					<th align="center" class="header">Sr.No.</th>
					<th align="left" class="header">Learning Unit</th>
					<th align="center" id="hdMS" class="header">Mindspark Recommended<br/>
						<input type="radio" value="MS" id="rdMS" name="rdTTFlow" onClick="highlightSelection('MS',<?=$totalClusters?>,<?=$noOfAttempts?>); getEstimatedTimeToCompleteTopic('MS')" <?php if($ms_clusters==0) echo " disabled";?> <?php if($customizedTopic!="") echo " disabled"; ?>></th>
					<th align="center" id="hdCBSE" class="header">CBSE<br/>
						<input type="radio" value="CBSE" id="rdCBSE" name="rdTTFlow" onClick="highlightSelection('CBSE',<?=$totalClusters?>,<?=$noOfAttempts?>); getEstimatedTimeToCompleteTopic('CBSE')" <?php if($cbse_clusters==0) echo " disabled";?> <?php if($customizedTopic!="") echo " disabled"; ?>></th>
					<th align="center" id="hdICSE" class="header">ICSE<br/>
						<input type="radio" value="ICSE" id="rdICSE" name="rdTTFlow" onClick="highlightSelection('ICSE',<?=$totalClusters?>,<?=$noOfAttempts?>); getEstimatedTimeToCompleteTopic('ICSE')" <?php if($icse_clusters==0) echo " disabled";?> <?php if($customizedTopic!="") echo " disabled"; ?>></th>
					<th align="center" id="hdIGCSE" class="header">IGCSE<br/>
						<input type="radio" value="IGCSE" id="rdIGCSE" name="rdTTFlow" onClick="highlightSelection('IGCSE',<?=$totalClusters?>,<?=$noOfAttempts?>); getEstimatedTimeToCompleteTopic('IGCSE')" <?php if($igcse_clusters==0) echo " disabled";?> <?php if($customizedTopic!="") echo " disabled"; ?>></th>
					<th align="center" id="hdCustom" class="header">Customized<br/>
						<input type="radio" value="Custom" id="rdCustom" name="rdTTFlow" onClick="highlightSelection('Custom',<?=$totalClusters?>,<?=$noOfAttempts?>); getEstimatedTimeToCompleteTopic('Custom');checkIfclustersAreCustomized('Custom')"  <?php if($customizedTopic!="" || (isset($_REQUEST["activateMode"]) && $_REQUEST["activateMode"]=="yes")) echo " disabled"; ?>></th>
					<?php if($flow=="MSOld") { ?>
					<th align="center" id="hdMSOld" class="header">Mindspark Old<br/>
						<input type="radio" value="" id="rdMSOld" name="rdTTFlow" onClick="highlightSelection('MSOld',<?=$totalClusters?>,<?=$noOfAttempts?>); getEstimatedTimeToCompleteTopic('MSOld')"  <?php if($customizedTopic!="") echo " disabled"; ?>></th>
					<?php } ?>
					<!--<th align="center" id="hdMisconception" class="header">Misconceptions</th>--> 
				</tr>
				<?php  
					$msTotalLevel = 0;
					$cbseTotalLevel = 0;
					$icseTotalLevel = 0;
					$igcseTotalLevel = 0;
				for ($i=0; $i<$totalClusters; $i++) { 
					$msRecomended = "MS@0";
					$cbseRecomended = "CBSE@0";
					$icseRecomended = "ICSE@0";
					$igcseRecomended = "IGCSE@0";
				?>

				<tr>
					<td class="customPoint"><input name="clusterSelection" class="clusterSelection" type="radio" value="<?=$clusterDetails[$i][0]?>"></td>
					<td align="center"><?=($i+1)?></td>
					<td align="left" class="clusterDetail<?=($clusterDetails[$i][8]=="practice")?' practiceCluster':''; ?>"><a href="sampleQuestions.php?ttCode=<?=$ttCode?>&learningunit=<?=$clusterDetails[$i][0]?>&cls=<?=$cls?>&flow=<?=($_REQUEST['flow']=="")?'MS':$_REQUEST['flow']; ?>" target="_blank" title="Click here to view sample questions">
						<?=$clusterDetails[$i][1];?>
						</a></td>
					<td align="center" id="rowMS<?=$i?>"><?php if($clusterDetails[$i][2]!=0) { echo " <img src='assets/right.png' width='15' height='15'>"; $msRecomended = "MS@1";$msTotalLevel++; } else echo "&nbsp;" ?></td>
					<td align="center" id="rowCBSE<?=$i?>"><?php if($clusterDetails[$i][3]!=0) { echo " <img src='assets/right.png' width='15' height='15'>"; $cbseRecomended = "CBSE@1";$cbseTotalLevel++; } else echo "&nbsp;" ?></td>
					<td align="center" id="rowICSE<?=$i?>"><?php if($clusterDetails[$i][4]!=0) { echo " <img src='assets/right.png' width='15' height='15'>"; $icseRecomended = "ICSE@1"; $icseTotalLevel++; } else echo "&nbsp;" ?></td>
					<td align="center" id="rowIGCSE<?=$i?>"><?php if($clusterDetails[$i][9]!=0) { echo " <img src='assets/right.png' width='15' height='15'>"; $igcseRecomended = "IGCSE@1"; $igcseTotalLevel++; } else echo "&nbsp;" ?></td>
					<td align="center" id="rowCustom<?=$i?>"><?php if($customizedTopic=="") { ?>


<span style='position: relative;'>
<input type="checkbox" <?php if($clusterDetails[$i][5]!=0) echo " checked"?> name="chkCluster[]" id="chkCluster<?=$i?>" class="<?=$msRecomended."_".$cbseRecomended."_".$icseRecomended."_".$igcseRecomended;?>" value="<?=$clusterDetails[$i][0]?>" onClick="checkIfClusterCustumize('Custom', this.id)" > 

				<?php }  else {
if($clusterDetails[$i][5]!=0) echo " <img src='assets/right.png' width='15' height='15'>"; else echo "&nbsp;";
}
?>

<div id="messagediv<?=$i?>"  onclick="notify();" style="height:17px;width:17px;position: absolute;left: 0;right: 0;top: 0;bottom: 0;background: url(assets/transperent.png) repeat;display: none;"></div>
</span>
      
</td>


			<?php if($flow=="MSOld") { ?>
					<td align="center" id="rowMSOld<?=$i?>"><?php if($clusterDetails[$i][6]!=0) echo " <img src='assets/right.png' width='15' height='15'>"; else echo "&nbsp;" ?></td>
					<?php } ?>
				</tr>
	<?php
	$timedTestArray = getTimedTestMappedToCluster($clusterDetails[$i][0]);
	$activitiesArray = getActivitiesMappedToCluster($clusterDetails[$i][0]);
	foreach($timedTestArray as $code => $arrDetails)
	{
		echo "<tr>";
		echo "<td colspan='2'>";
		echo "<strong>Timed test: </strong><a style='cursor:pointer;' onclick=trackTeacher('".$code."','timeTest');>".$arrDetails["desc"]."</a>";
		echo "</td>";
		echo "</tr>";
	}
	foreach($activitiesArray as $code => $arrDetails)
	{
		echo "<tr>";
		echo "<td colspan='2'>";
		echo "<strong>Activity: </strong><a style='cursor:pointer;' onclick=trackTeacher(".$code.",'activity');>".$arrDetails["desc"]."</a>";
		echo "</td>";
		echo "</tr>";
	}
	} ?>
			</table>

			<div id="estimation" align="left" style="width:80%; text-align:justify; font-size:1.2em;" class="legend"> <br/>
				Estimated time to complete the topic for selected flow:
				<?=$totalTimeForTopic?>
				minutes<br/>
				<div class="legend" style="color:black"><em>(Please note that this is just an estimated time based on the past data and the actual time may vary with each student)</em></div>
				<br/>
			</div>
		<?php if(!in_array($loginID,$testIDArray) && !($_SESSION['isOffline'] === true && ($_SESSION['offlineStatus']==1 || $_SESSION['offlineStatus']==2 || $_SESSION['offlineStatus']==5 || $_SESSION['offlineStatus']==6 || $_SESSION['offlineStatus']==7))) { ?>
			<div align="center">
				
				<input type="button" name="activateTopic" id="activateTopic" value="Activate Selected Units" onClick="activateDeactivateTopic('<?=$ttCode?>',<?=$schoolCode?>,<?=$cls?>,'<?=$section?>','<?=$flow?>','<?=ucfirst($username)?>','activate','topicContainer<?=$i?>')" class="submitBtn" <?php if(isset($_REQUEST["activateMode"]) && $_REQUEST["activateMode"]=="yes") { } else echo " style='display:none'";?>>
				<input type="button" name="seeCustomize" id="seeCustomize" value="Customize Units" class="submitBtn" <?php if(isset($_REQUEST["activateMode"]) && $_REQUEST["activateMode"]=="yes") { } else echo " style='display:none'";?>>
				
				<?php if($customizedTopic=="") { //show save button only when it is non-customized TT (i.e. new cust ttcode in master table
						$newTTDesc = getCustomTeacherTopicDescSuggestion($ttCode,$schoolCode, $cls);
						$tdList = getTeacherTopicDescList($schoolCode, $cls);
						$validationData = implode("~", $tdList);
				?>
				<div id="friendlyNameForm" <?php if(isset($_REQUEST["activateMode"]) && $_REQUEST["activateMode"]=="yes") echo " style='display:none'";?>>
					<div style="float: left; padding-left: 250px; width:250px">
					Give a friendly name to the customised topic before you activate it for your class:</div> 
					<div style="width:5px"><input style="width:350px" name="ttDesc" type="text" value="<?=$teacherTopicDesc . $newTTDesc ?>" id="customizedTopicDesc" data="<?=$validationData ?>" required></div>
					<br /><br />
				</div>
				<input type="button" name="save" id="saveCustom" value="Activate Custom NOW" <?php if(count($topicsActivated)>=15) echo "style='opacity:0.3' title='Mindspark does not allow more than 15 topics to be active at a time.'"; else { ?> onClick="return validate(1);" <?php } if(isset($_REQUEST["activateMode"]) && $_REQUEST["activateMode"]=="yes") echo "style='display:none'" ?> class="submitBtn btnSave">

				<input type="submit" name="save_activate" id="save_activate" value="Activate Custom LATER" onClick="return validate(2);" <?php if(isset($_REQUEST["activateMode"]) && $_REQUEST["activateMode"]=="yes") echo "style='display:none'" ?> class="submitBtn btnSave">
					<input type="hidden" name="saveAndActivate" id="saveAndActivate" value="0">
				<?php }  else {?>
				<input type="button" onClick="showMapping('<?=$customizedTopic?>','<?=$cls?>','<?=$section?>','')" value="Click here to re-customize the topic" class="submitBtn">
				<?php } ?>
			</div>
		<?php }
		else { ?>
			<div align="center" style="font-size:1.5em">Can not customize the topic in online mode.</div>
		<?php } ?>
			<div id="notesDiv" align="left" style="width:80%; text-align:justify;font-size:1.2em" class="legend"> <br/>
				Note:<br/>
				<ul>
					<li>Some learning units may not be selected for any stream because they do not relate to the current class level</li>
					<li>The option of choosing a stream<!--/customizing the curriculum--> is available only as long as NO student has started the topic. The moment even 1 student starts the topic, this choice will no longer be available.</li>
					<!--<li>If you choose a customized curriculum, the students will not be taken to a higher/lower class level in that topic.</li>-->
				</ul>
			</div>
			<?php } else echo "No records found!"; ?>
		<input type="hidden" name="interface" value="<?=$interface?>">
		<input type="hidden" id="totalLevelCount" name="totalLevelCount" value="<?=$msTotalLevel."-".$cbseTotalLevel."-".$icseTotalLevel."-".$igcseTotalLevel;?>">
		<input type="hidden" id="generatedFlow" name="generatedFlow" value="">
	</form>
	</div>
	<form target='window.parent' name="mindsparkTeacherLogin" id="mindsparkTeacherLogin" action="" method="post">
		<input type="hidden" name="mode" id="mode" value="">
		<input type="hidden" name="sessionID" id="sessionID" value="<?=$_SESSION["sessionID"]?>">
		<input type="hidden" name="childClass" id="childClass" value="<?=$cls?>">
		<input type="hidden" name="userType" id="userType" value="teacherAsStudent">
		<input type="hidden" name="forceNew" id="forceNew" value="">
		<input type="hidden" name="ttCode" id="ttCode" value="<?=$ttCode?>">
		<input type="hidden" name="customClusterCode" id="customClusterCode" value="">
		<input type="hidden" name="forceFlow" id="forceFlow" value="<?=$_REQUEST['flow']?>">
		<input type="hidden" name="startPoint" id="startPoint" value="">
	</form>
</div>
<?php if(!isset($_REQUEST["activateMode"])) { ?>
<script>
	setTimeout(function() {
		$("#rdCustom").click();
		$("#rdMS,#rdCBSE,#rdICSE,#rdIGCSE").attr("disabled",true);
	},100);
	
</script>
<?php } ?>
<?php include("footer.php") ?>
<?php
function getCustomizedTopicCode($clusters, $username, $schoolCode)
{
	$customCode = "";
	$query  = "SELECT code FROM adepts_customizedTopicDetails WHERE clusterCodes='$clusters'";
	$result = mysql_query($query) or die("Error while fetching details");
	if($line = mysql_fetch_array($result))
	{
		$customCode = $line['code'];
	}
	else
	{
		$query  = "INSERT INTO adepts_customizedTopicDetails (clusterCodes, customizedBy, schoolCode) VALUES ('$clusters','$username',$schoolCode)";
		$result = mysql_query($query) or die("Error while saving customization of learning units");
		$customCode = mysql_insert_id();
	}
	return $customCode;
}
function getClustersChosen($schoolCode, $cls, $section, $ttCode)
{
	$clusterArray = array();
	//Get the code of customized topic for the TT
	$query = "SELECT flow FROM adepts_schoolTeacherTopicFlow
	          WHERE  schoolCode=$schoolCode AND class=$cls AND teacherTopicCode='$ttCode'";
	if($section!="")
		$query .= " AND section='$section'";

	$result = mysql_query($query) or die(mysql_error().$query);
	if($line   = mysql_fetch_array($result))   //old approach where in case of customization same teacher topic code was used
	{
    	$code   = trim(substr($line[0],9));
    	$query = "SELECT clusterCodes FROM adepts_customizedTopicDetails WHERE code=$code";
    	$result = mysql_query($query) or die(mysql_error().$query);
    	$line   = mysql_fetch_array($result);

    	$clusterArray = explode(",",$line[0]);
	}
	else
	{
	    $query  = "SELECT customCode FROM adepts_teacherTopicMaster WHERE schoolCode=$schoolCode AND class=$cls AND teacherTopicCode='$ttCode' AND customTopic=1";
	    $result = mysql_query($query) or die(mysql_error().$query);
	    $line   = mysql_fetch_array($result);
	    $code   = $line[0];

	    $query = "SELECT clusterCodes FROM adepts_customizedTopicDetails WHERE code=$code";
    	$result = mysql_query($query) or die(mysql_error().$query);
    	$line   = mysql_fetch_array($result);

    	$clusterArray = explode(",",$line[0]);
	}
	return $clusterArray;
}
/**
 * Function to get the no. of attempts on the topic for the class.
 */
function getNoOfAttempts($schoolCode, $cls, $section, $ttCode)
{
	$query = "SELECT count(ttAttemptID)
	          FROM   adepts_userDetails a, ".TBL_TOPIC_STATUS." b
	          WHERE  a.userID=b.userID AND category='STUDENT' AND subcategory='School' AND enabled=1 AND
	                 schoolCode=$schoolCode AND childClass='$cls' AND teacherTopicCode='$ttCode'";
	if($section!="")
		$query .= " AND childSection='$section'";

	$result = mysql_query($query) or die("Error in fetching no. of attempts on the topic");
	$line   = mysql_fetch_array($result);
	$noOfAttempts = $line[0];
	return $noOfAttempts;
}

function saveMapping($schoolCode, $cls, $section, $ttCode, $flow)
{
	//Query to check if there is a previous mapping saved for this topic, if yes update the flow else save the mapping as a new entry
	$query = "SELECT count(*) FROM adepts_schoolTeacherTopicFlow WHERE schoolCode=$schoolCode AND class=$cls AND teacherTopicCode='$ttCode'";
	if($section!="")
		$query .= "  AND section='$section'";
	$result = mysql_query($query) or die("Error in fetching previous details of customization");
	$line   = mysql_fetch_array($result);
	if($line[0]==0)
	{
		$query = "INSERT INTO adepts_schoolTeacherTopicFlow (schoolCode, class, section, teacherTopicCode, flow, lastModifiedBy)
				      VALUES ($schoolCode,$cls, '$section','$ttCode','$flow','".$_SESSION['username']."')";
	}
	else
	{
		$query = "UPDATE adepts_schoolTeacherTopicFlow SET flow='$flow' WHERE schoolCode=$schoolCode AND class=$cls AND section='$section' AND teacherTopicCode='$ttCode'";
	}
	mysql_query($query) or die("Error while saving the customized mapping<br/>");

	//Check if the change is made in currently active topic - if so update the flow in the activation table for students to follow the changed mapping.
	//This case can happen when the topic is active and no student has started the topic yet, so it will allow the teacher to change the mapping till then.
	$query = "SELECT srno FROM adepts_teacherTopicActivation WHERE schoolCode=$schoolCode AND class=$cls AND teacherTopicCode='$ttCode' AND deactivationDate='0000-00-00'";
	if($section!="")
		$query .= "  AND section='$section'";
	$result = mysql_query($query) or die("Error in query for checking topic activated or not");
	if($line = mysql_fetch_array($result))
	{
		$query = "UPDATE adepts_teacherTopicActivation SET flow='$flow' WHERE srno=".$line['srno'];
		mysql_query($query) or die("Error in updating the flow in activation table");
	}
}

function createCustomTT($ttCode, $schoolCode, $cls, $clusterArray, $username, $ttDescription)
{
    $customCode = "";
    $newTTCode  = "";
    $insertFlag = 1;
    $clustersChosen = implode(",",$clusterArray);
    //Check if a customized TT for the school/class combination already present i.e. customized by some other section
    $query  = "SELECT teacherTopicCode, customCode FROM adepts_teacherTopicMaster WHERE customTopic=1 AND schoolCode=$schoolCode AND class=$cls AND parentTeacherTopicCode='$ttCode'";
    $result = mysql_query($query) or die(mysql_error().$query);
    while($line = mysql_fetch_array($result))
    {
        $customCode = $line['customCode'];
        $query  = "SELECT clusterCodes FROM adepts_customizedTopicDetails WHERE code='$customCode'";
    	$cluster_result = mysql_query($query) or die("Error while fetching details");
    	if($cluster_line = mysql_fetch_array($cluster_result))
    	{
    		$clusterCodes = $cluster_line['clusterCodes'];
    		if($clusterCodes==$clustersChosen)    //same set of clusters already customized previously for this school/class
    		{
    		    $newTTCode = $line['teacherTopicCode'];
    		    $insertFlag = 0;
    		    break;
    		}
    	}
    }
    if($insertFlag)
    {
        $query  = "INSERT INTO adepts_customizedTopicDetails(clusterCodes, customizedBy, schoolCode) VALUES ('$clustersChosen','$username',$schoolCode)";
		$result = mysql_query($query) or die("Error while saving customization of learning units");
		$customCode = mysql_insert_id();

		$query  = "SELECT teacherTopicDesc, mappedToTopic, classification FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$ttCode'";
		$result = mysql_query($query) or die(mysql_error().$query);
		$line   = mysql_fetch_array($result);
		$classification = $line['classification'];
		$desc           = $line['teacherTopicDesc'];
		$mappedToTopic  = $line['mappedToTopic'];
		//Changes made for mantis: 8219
		$desc = $ttDescription;
		// $desc           = getNewTeacherTopicDesc($ttCode,$schoolCode, $cls, $desc); 

		$newTTCode = getNewTTCode($_SESSION['isOffline']);

		$query = "INSERT INTO adepts_teacherTopicMaster (teacherTopicCode, teacherTopicDesc, live, subjectno, mappedToTopic, classification, customTopic, schoolCode, class, parentTeacherTopicCode, customCode)
		          VALUES ('$newTTCode','".mysql_escape_string($desc)."',1,".SUBJECTNO.",'$mappedToTopic','$classification',1,$schoolCode,$cls,'$ttCode','$customCode')";
		mysql_query($query) or die("Error in saving custom TT");
    }
	return "Custom - ".$customCode."~".$newTTCode;
}

function getNewTTCode($isOffline)
{
    $q = "SELECT max(cast(substring(teacherTopicCode,3) as unsigned)) FROM adepts_teacherTopicMaster";
	if($isOffline)
	{
		$sq	=	"SELECT abbreviation FROM adepts_offlineSchools WHERE schoolCode=".$_SESSION["schoolCode"];
		$rs	=	mysql_query($sq);
		$rw	=	mysql_fetch_assoc($rs);
		$q .= " WHERE teacherTopicCode LIKE '".$rw['abbreviation']."%'";
	}
    $r1 = mysql_query($q);
    $l1 = mysql_fetch_array($r1);
    $no = $l1[0]+1;
    $no = str_pad($no,3,"0",STR_PAD_LEFT);
	if($isOffline)
    	$newTTCode = $rw['abbreviation'].$no;
	else
    	$newTTCode = "TT".$no;
    return $newTTCode;
}

function getNewTeacherTopicDesc($ttCode, $schoolCode, $class, $oldDesc)
{
    $no = 1;
    $query = "SELECT max(cast(substring_index(teacherTopicDesc,'- Custom ',-1) as unsigned)) FROM adepts_teacherTopicMaster
              WHERE  customTopic=1 AND schoolCode=$schoolCode AND class=$class AND parentTeacherTopicCode='$ttCode'";
    echo $query;
    $result = mysql_query($query);

    if($line = mysql_fetch_array($result))
        $no = $line[0] + 1;
    $newDesc = $oldDesc." - Custom ".$no;
    return $newDesc;

}

function getCustomTeacherTopicDescSuggestion($ttCode, $schoolCode, $class)
{
    $no = 1;
    $query = "SELECT count(teacherTopicCode) FROM adepts_teacherTopicMaster
              WHERE customTopic=1 AND schoolCode=$schoolCode AND class=$class AND parentTeacherTopicCode='$ttCode'";
    $result = mysql_query($query);

    if($line = mysql_fetch_array($result))
        $no = $line[0] + 1;
    $newDesc = " - Custom ".$no;
    return $newDesc;

}

function isCustomizedTopic($ttCode)
{
    $parentTT = "";
    $query = "SELECT customTopic, parentTeacherTopicCode FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$ttCode'";
    $result = mysql_query($query);
    $line   = mysql_fetch_array($result);
    if($line[0]==1)
        $parentTT = $line[1];
    return $parentTT;
}

function getTimedTestMappedToCluster($clusterCode)
{
	$timedTestArray = array();
	$query = "SELECT timedTestCode, description FROM adepts_timedTestMaster WHERE linkedToCluster='$clusterCode' AND status='Live'";
	$result = mysql_query($query) or die("Error in fetching timed test details");
	while($line = mysql_fetch_array($result))
	{
		$timedTestArray[$line[0]]["desc"] = $line[1];
	}
	return $timedTestArray;
}


function getActivitiesMappedToCluster($clusterCode)
{
	$activitiesArray = array();
	$query = "SELECT gameID, gameDesc, type FROM adepts_gamesMaster WHERE linkedToCluster='$clusterCode' AND live='Live'";
	$result = mysql_query($query) or die("Error in fetching cluster details");
	while($line = mysql_fetch_array($result))
	{
		$activitiesArray[$line[0]]["desc"] = $line[1];
	}
	return $activitiesArray;
}

function getCustomizeCluster($ttCode,$schoolCode,$class,$section)
{
	$arrayCluster	=	array();
	$clusterList	=	"";
	$sq	=	"SELECT clusterCodes FROM adepts_teacherTopicMaster A, adepts_customizedTopicDetails B, adepts_teacherTopicActivation C
			 WHERE A.customCode=B.code AND parentTeacherTopicCode='$ttCode' AND A.teacherTopicCode=C.teacherTopicCode 
			 AND C.schoolCode=$schoolCode AND C.class=$class AND C.section='$section' and deactivationDate = '0000-00-00'";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$clusterList	=	$clusterList.$rw[0].",";
	}
	$clusterList	=	substr($clusterList,0,-1);
	$clusterList	=	"'".str_replace(",","','",$clusterList)."'";
	return $clusterList;
}

function getTeacherTopicDescList($schoolCode, $class) {
	$ttDescList = array();
	$query = "SELECT teacherTopicDesc FROM adepts_teacherTopicMaster WHERE schoolCode = $schoolCode AND class = $class";
	$result = mysql_query($query);
	while($row = mysql_fetch_array($result)) {
		$ttDescList[] = $row[0];		
	}
	return $ttDescList;
}

?>