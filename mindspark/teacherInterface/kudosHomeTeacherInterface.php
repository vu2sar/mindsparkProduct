<?php
//error_reporting(E_ALL);
set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
//error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);

include("header.php"); 
include('../userInterface/src/kudos/common_functions.php');
$userID = $_SESSION['userID'];
$objUser = new User($userID);
//$schoolCode    = $objUser->schoolCode;
$childClass    = $objUser->childClass;
$childSection  = $objUser->childSection;
$childName = $objUser->childName;
$userName= $objUser->username;
$category = $objUser->category;
$schoolCode = isset($_SESSION['schoolCode'])?$_SESSION['schoolCode']:"";
$userNameArr = array();
//echo $schoolCode.$childClass.$childName.$childSection.$userName.$category;
//echo "SESSION SCHOOL CODE IS -".$schoolCode;
if(!isset($_SESSION['category'])){
    
    $_SESSION['category']=$category;
}
//print_r($objUser);
 
//echo fetchFullName($userName);
//echo "</br>".$childName;
 
if( !isset($_SESSION['userID'])) {
    header( "Location: error.php");
}
if(isset($_SESSION['revisionSessionTTArray']) && count($_SESSION['revisionSessionTTArray'])>0)
{
    header("Location: controller.php?mode=login");
}
$dataSynchronised = true;
if($_SESSION['isOffline'] === true && ($_SESSION['offlineStatus']==2 || $_SESSION['offlineStatus']==4))
    $dataSynchronised = false;
 
if(!isset($_SESSION['notice'])){
    $notice=1;
    $_SESSION['notice']=1;
}
else {
    $notice=0;
}
//exit();
 
$Name = explode(" ", $_SESSION['childName']);
$Name = $Name[0];
$baseurl = IMAGES_FOLDER."/newUserInterface/";
 
/*
echo "User ID is: ".$userID;
echo "</br>School Code is: ".$schoolCode;
echo "</br>Child Class is: ".$childClass;
echo "</br>Child Section is: ".$childSection;*/
 
//KUDOS PHP STARTS HERE
 
//set_time_limit(0);
//include('../check.php');
//checkPermission('MNU');
 
//mysql_connect("192.168.0.7","root","") or die (mysql_errno()."-".mysql_error()."Could not connect to localhost");
//mysql_select_db ("educatio_adepts")  or die ("Could not select database".mysql_error());

if(isset($_POST['hdnAction']) && $_POST['hdnAction'] == 'sendKudo')
{
    sendKudoNewInterface($userName, $schoolCode, $childClass, $childSection, $category);
    /*echo "<script>window.location=\"kudosHomeTeacherInterface.php\"</script>";*/	
}
 
$myWall = FALSE;
 
if(isset($_REQUEST['wall']) && $_REQUEST['wall']=='my')
    $myWall = TRUE;

//if(isset($_REQUEST['wall']) && $_REQUEST['wall']=='showAll')
//{ $myWall = 'showAll'; }

$arrKudos = getAllKudos($myWall, $schoolCode, $childClass,$childSection, $userName , $category);
//print_r($arrKudos);

 
?>

<html>
<head>

<title>Kudos Home</title>
<!--<link href="../../../teacherInterface/libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<script src="../../../teacherInterface/libs/jquery.js"></script>
<script type="text/javascript" src="../../../teacherInterface/libs/jquery-ui-1.8.16.custom.min.js"></script>-->

<link href="css/common.css?ver=2" rel="stylesheet" type="text/css">
<link href="css/revisionSession.css" rel="stylesheet" type="text/css">

<!-- Styles for kudos -->


<link rel="stylesheet" href="../userInterface/src/kudos/styles/inputosaurus.css"/>
<!--<script src="js/jquery-1.9.1.js"></script>-->
<!-- <script src="../userInterface/libs/jquery.js"></script> -->

<script src="libs/jquery-ui.js"></script>
<script type="text/javascript" src="../userInterface/src/kudos/js/inputosaurus.js?ver=4"></script> 
<script src="libs/kudosTeacherHome.js?ver=7" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="../userInterface/css/colorbox.css">
<script src="../userInterface/libs/jquery.colorbox-min.js" type="text/javascript"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<!--Styles for Kudos end here -->

<script>
var langType = '<?=$language;?>';
function loadClassDetails(id) 
{
// console.log("<?= $_SESSION['category'] ?>");
var actionUrl = 'get-class-for-dropdown.php';
var dataString= "schoolCode=<?= $schoolCode ?>&userID=<?= $userID ?>&category=<?= $_SESSION['category'] ?>&id="+id;
// console.log(dataString);
$.ajax({
  type: "POST",
  url: actionUrl,
  data: dataString, 
  success: function(data) {
	var result = jQuery.parseJSON(data);
	//console.log(result);
	displayClassDetails(result,id);
  }
});
}

function displayClassDetails(data,id) 
{	
	$("#section-select-dropdown").hide();
	$("#section-select-dropdown-2").hide();
	
	var r = new Array();
	var j = -1;	

	if(id == 3 || id == 2)
	{		
		var html = '';
		var allClassArr = new Array();
		var checkExistsArr = new Array();
		//r[++j] = '<option value= "" >All</option>';
		for (var i = 0; i < data.length; i++) {
		  if(data[i]["childClass"].indexOf("-") > 0)
		  {
			  allClassArr.push(data[i]["childClass"]);
		  	  if($.inArray(data[i]["childClass"].split("-")[0], checkExistsArr) < 0)
			  {
				  checkExistsArr.push(data[i]["childClass"].split("-")[0]);
				  html += '<option value= ' + data[i]["childClass"].split("-")[0] + ' >' + data[i]["childClass"].split("-")[0] + '</option>';
			  }
		  }
		  else
		  {
		  	  allClassArr.push(data[i]["childClass"]);
		  	  html += '<option value= ' + data[i]["childClass"] + ' >' + data[i]["childClass"] + '</option>';
		  }
		}
		r[++j] = '<option value= "'+allClassArr.join(",")+'" >All</option>';	
		r[++j] = html;
	}
	else
	{
		r[++j] = '<option value= "" >All</option>';
		for (var i = 0; i < data.length; i++) 
		{
	  		r[++j] = '<option value= ' + data[i]["childClass"] + ' >' + data[i]["childClass"] + '</option>';
	  	}
	}
	//alert('In display class details');
	$("#class-select-dropdown").html(r.join(''));
	$("#class-select-dropdown-2").html(r.join(''));

	return false;
}

function loadSectionDetails(id) 
{
	var str = $( "#useridval" ).val();
	var lenOfStr=str.length;
	/*if(lenOfStr>0)
	{onChangeSubmit(id); return;}
	else{*/
	var actionUrl = 'get-section-for-dropdown.php';
	var classSelect= $("#class-select-dropdown option:selected").val();
	var classSelect2= $("#class-select-dropdown-2 option:selected").val();
	//console.log("CLASS SELECTED IS- "+classSelect);
	
	var dataString= {'schoolCode' : <?= $schoolCode ?> , 'childClassSelected': classSelect, 'userID' : '<?= $userID ?>', 'category': "<?= $_SESSION['category'] ?>" } //
	var dataString2= {'schoolCode' : <?= $schoolCode ?> , 'childClassSelected': classSelect2, 'userID' : '<?= $userID ?>', 'category': "<?= $_SESSION['category'] ?>"  } 

//console.log(dataString);
//console.log(dataString2);
if(id==2){
$.ajax({
  type: "POST",
  url: actionUrl,
  data: dataString2, 
  success: function(data) {
	var result = jQuery.parseJSON(data);
	var tempRes = result[0];
	// console.log("SUCCESS"+data);
	if(tempRes["childSection"] != null && tempRes['childSection'] != "")
	{
		if(result.length>0){$("#section-select-dropdown-2").show();}
		else
		{
			$("#section-select-dropdown-2").val("");
			$("#section-select-dropdown-2").hide();
		}
	}	
	else
	{	
		$("#section-select-dropdown-2").val("");
		$("#section-select-dropdown-2").hide();
	}
	onChangeSubmit(id, 'class');
	displaySectionDetails(result,2);		
  }
});}
else
{
	if(classSelect.indexOf(",") < 0)
	{
		if(lenOfStr == 0)
		{
			$.ajax({
			  type: "POST",
			  url: actionUrl,
			  data: dataString, 
			  success: function(data) {
				var result = jQuery.parseJSON(data);
				var tempRes = result[0]; 
				tempResultSection=result;
				//console.log("SUCCESS"+data);
				/*if(tempRes["childSection"] != null && tempRes['childSection'] != "")
				{
					if(result.length>0){$("#section-select-dropdown").show();}
					else
					{
						$("#section-select-dropdown").val("");
						$("#section-select-dropdown").hide();
					}
				}	
				else
				{
					$("#section-select-dropdown").val("");
					$("#section-select-dropdown").hide();
				}*/
				displaySectionDetails(result,1);		
				onChangeSubmit(id, 'class');
			  }
			});			
		}
		else
			onChangeSubmit(id, 'class');
	}
	else
	{
		$("#section-select-dropdown").hide();
		lastSelectedCategory = $("#category-select-dropdown option:selected");
        lastSelectedClass = $("#class-select-dropdown option:selected");
        lastSelectedSection = $("#section-select-dropdown option:selected");
        var categorySelect = lastSelectedCategory.val();
        var classSelect = lastSelectedClass.val();
        var sectionSelect = lastSelectedSection.val();
        var actionUrl = "../userInterface/src/kudos/names_ajax.php";
        $.post(actionUrl,{categorySelect:categorySelect,classSelect:classSelect,sectionSelect:sectionSelect,newInterface:true});
	}
}
}

function displaySectionDetails(data, secDropdownNo) {
	var r = new Array();
	var j = -1;
	if(secDropdownNo == 1)
	{
		var html = '';
		var allClassArr = new Array();
		for (var i = 0; i < data.length; i++) {
		  if(data[i]['childSection'] != "")
		  {
			  allClassArr.push(data[i]["childSection"]);
			  html += '<option value= ' + data[i]["childSection"] + ' >' + data[i]["childSection"] + '</option>';		  	
		  }
		}
		r[++j] = '<option value= "'+allClassArr.join(",")+'" >All</option>';
		if(html != "")
			r[++j] = html;		
	}	
	else
	{
		r[++j] = '<option value= "" >All</option>';
		for (var i = 0; i < data.length; i++) {
		  r[++j] = '<option value= ' + data[i]["childSection"] + ' >' + data[i]["childSection"] + '</option>';
		}
	}	
	//alert('In display Section details');
	if(secDropdownNo==1){$("#section-select-dropdown").html(r.join(''));}
	if(secDropdownNo==2){$("#section-select-dropdown-2").html(r.join(''));}
	//if(secDropdownNo==2){$("#section-select-dropdown-2").empty().append(r.join(''));}
	return false;
}

function showAllKudos()
{
	//console.log("In Show All Kudos");
	loadClassDetails(2);
	$("#monthHeaderKudos").remove();
	$("#divKudos").html("");
	$('#showAllButton').css({'background-color':'#36A9E1'});
	$('#myWallButton').css({'background-color':'#4D4D4D'});	
	$('#wallOfFameButton').css({'background-color':'#4D4D4D'});	
						
	document.getElementById("showAll").style.display = 'block'; 
	var categorySelect2= $("#category-select-dropdown option:selected").val();
	
							
}

function loadKudosByFilter(categoryFilter, classFilter, sectionFilter, schoolFilter, userCategory, userName)
{
	var dataString = {category: categoryFilter, class: classFilter, section: sectionFilter, schoolCode: schoolFilter, userCategory:userCategory, userName: userName};
	var actionUrl = "get-kudos-by-filters.php";
	//console.log(dataString);
	
	$.ajax({
	type: "POST",
	url: actionUrl,
	data: JSON.stringify(dataString),				 
	success: function(data) {	
	//console.log("data= " + data);
	//			var result = jQuery.parseJSON(data);
	//			console.log("res =" + result);
	
		$('#divKudos').html(data);
	}
	});
	return false;		
}
		
function initKudosFilter()
{
	//console.log("inside initKudosFilter");
	$('#submitCustomKudos').click(function(){

	   //console.log("inside submitCustomKudos");
	   var categoryFilter = $("#category-select-dropdown-2").val();
	   var classFilter = $("#class-select-dropdown-2").val();
	   var sectionFilter = $("#section-select-dropdown-2").val();
	   
	   //console.log('Category is ' + categoryFilter );
	   //console.log('Class is ' + classFilter );
	   //console.log('Section is ' + sectionFilter );
	   
	   loadKudosByFilter(categoryFilter, classFilter, sectionFilter, <?php echo $schoolCode ?>, '<?php echo $category?>', '<?php echo $userName?>');
	 });

}

function deleteThisKudo(kudos_id, filterKudoPageFlag)
{
	var r = confirm("Are you sure you want to delete this?");
	var userCategory = $("#userCategory").val();
	var childClass = $("#childClass").val();
	var childSection = $("#childSection").val();
	if(r)
	{
		$.ajax({
		type:'POST',
		url: "../userInterface/src/kudos/deleteKudoAjax.php",
		data: {kudos_id:kudos_id, myWall:"<?=$myWall?>", schoolCode:"<?=$schoolCode?>", childClass:childClass, childSection:childSection, userName:"<?=$userName?>", category:"<?=$category?>", filterKudoPageFlag:filterKudoPageFlag, userCategory:userCategory},
		success: function(data)
		{
		  if(data)
		  {
		  	$("#divKudos").html("'"+data+"'");
		  	if(window.navigator.userAgent.indexOf("MSIE") > 0)	
			  	$(".deleteKudoTd").css({"width":"0px"});
		  	alert("Kudo deleted.");
		  }
		  else
		  	alert("Enable to delete. Please report this to Mindspark team.");
		},
		});	
	}
};
		
</script>


<script>
	var categorySelect = 'student';
	var categorySelect2 = 'student';
	var classSelect = '';
	var sectionSelect = '';
	var tempCategorySelect = '';
	var tempClassSelect = '';
	var tempSectionSelect = '';
	var lastSelectedCategory;
	var lastSelectedClass;
	var lastSelectedSection;
	function load()
	{
		var sideBarHeight = window.innerHeight-95;
		$("#sideBar").css("height",sideBarHeight+"px");
	}
	
	function deleteAllNamesFromList()
	{
		var ulData = $(".inputosaurus-container li");
		for(i=0;i<ulData.length;i++)
		{
			var attr = $(ulData[i]).attr('data-inputosaurus');
			if(attr)
			{
			  $("[data-inputosaurus='"+attr+"']").remove(); 
			}
		}
	}

	function onChangeSubmit(condition, whichDropDownChanged)				
	{
		 if(condition==22){$( "#TblKudos" ).remove();}
	 	 var actionUrl = "../userInterface/src/kudos/names_ajax.php";
		 				 
		 var str = $( "#useridval" ).val();
		 var lenOfStr=str.length;
		 if(lenOfStr>0)
		 {
		 	 tempCategorySelect = $("#category-select-dropdown").val();
			 tempClassSelect = $("#class-select-dropdown").val();
			 tempSectionSelect = $("#section-select-dropdown").val();
		 	 lastSelectedCategory.attr("selected",true);
		 	 lastSelectedClass.attr("selected",true);
		 	 lastSelectedSection.attr("selected",true);
			 var confirmVar = confirm("You are changing one of the filters. This will reset the names and message entered so far. Are you sure you want to continue?");	
		 	 if(confirmVar == true)
		 	 {
				deleteAllNamesFromList();
				$('#userid, #useridval').val('');	
				$("#section-select-dropdown").val(tempSectionSelect);
				$("#category-select-dropdown").val(tempCategorySelect);
				$("#class-select-dropdown").val(tempClassSelect);
		 	 	if(whichDropDownChanged == 'category')
		 	 	{
		 	 		$("#class-select-dropdown").show();
		 	 	}
		 	 	else if(whichDropDownChanged == 'class')
		 	 	{
		 	 		classSelect = $("#class-select-dropdown option:selected").val();
	 	 			if(classSelect.indexOf(",") > 0)
		 				$("#section-select-dropdown").hide();
		 	 		loadSectionDetails(3);
		 	 		$("#section-select-dropdown").show();
		 	 	}
				lastSelectedCategory = $("#category-select-dropdown option:selected");
				lastSelectedClass = $("#class-select-dropdown option:selected");
            	lastSelectedSection = $("#section-select-dropdown option:selected");
				$(".ui-autocomplete-input").attr('placeholder',"To");
				$("#txtMessage").val("");
				$("#userid,#useridval").val("");
				$('#userid').inputosaurus('refresh');
		 	 }
		 	 else
		 	 {
		 	 	return;
		 	 }					 					 					 
		 }
		 else
		 {
		 	lastSelectedCategory = $("#category-select-dropdown option:selected");
            lastSelectedClass = $("#class-select-dropdown option:selected");
            lastSelectedSection = $("#section-select-dropdown option:selected");
		 	if(whichDropDownChanged == 'category')
	 	 	{
	 	 		$("#class-select-dropdown").show();
	 	 	}
	 	 	else if(whichDropDownChanged == 'class')
	 	 	{
	 	 		classSelect = $("#class-select-dropdown option:selected").val();
	 	 		if(classSelect.indexOf(",") > 0)
		 			$("#section-select-dropdown").hide();
	 	 		$("#section-select-dropdown").show();
	 	 	}
		 }
		 
	
	 if(condition==1)
	 {
		 categorySelect = $("#category-select-dropdown option:selected").val();
		 categorySelect2 = $("#category-select-dropdown-2 option:selected").val();
		 classSelect = $("#class-select-dropdown option:eq(0)").val();
		 sectionSelect = $("#section-select-dropdown option:eq(0)").val();
		 //console.log('condition 1 ='+condition+' Class Select is - '+classSelect+' Section Select is- '+sectionSelect);
	 }
	 else if(condition==3)
	 {
		 categorySelect = $("#category-select-dropdown option:selected").val();
		 categorySelect2 = $("#category-select-dropdown-2 option:selected").val();	 		 				 
		 classSelect = $("#class-select-dropdown option:selected").val();
		 sectionSelect = $("#section-select-dropdown option:selected").val();
		 //console.log('condition 3 ='+condition+' Class Select is - '+classSelect+' Section Select is- '+sectionSelect);
	 }
	 else
	 {
		 categorySelect = $("#category-select-dropdown option:selected").val();
		 categorySelect2 = $("#category-select-dropdown-2 option:selected").val();
		 classSelect = $("#class-select-dropdown option:selected").val();
		 sectionSelect = $("#section-select-dropdown option:selected").val();
		 tempCategorySelect = categorySelect;
		 tempClassSelect= classSelect;
		 tempSectionSelect= sectionSelect;
		 //console.log('condition else ='+condition+' Class Select is - '+classSelect+' Section Select is- '+sectionSelect);
		 
	 }
	 
	 
	 if(categorySelect=='teacher')
	 { 
	  $("#class-select-dropdown").hide(); 
	  $("#section-select-dropdown").hide();
	  $($("#class-select-dropdown option")[0]).attr("selected",true);
	  $($("#section-select-dropdown option")[0]).attr("selected",true)
	 }
	  
	  if(categorySelect=='student')
	  { 	   
	   $("#class-select-dropdown").show();	   
	  }
	  
	  if(categorySelect2=='teacher')
	 { 
	  $("#class-select-dropdown-2").hide(); 
	  $("#section-select-dropdown-2").hide();
	 }
	  
	  if(categorySelect2=='student')
	  { 
	   $("#class-select-dropdown-2").show(); 
	   
	  }
	  //console.log('Before POST - actionURL- '+actionUrl+' Class Select - '+classSelect+' Section Select - '+sectionSelect);
	  var randomNumber = Math.random()*1000;	
	  
	  
	  /*$.ajax({
	  type: "POST",
	  url: actionUrl,
	  data: {'categorySelect':categorySelect,'classSelect':classSelect,'sectionSelect':sectionSelect}, 
	  success: function(data) {
		resultNamesOfStudents = jQuery.parseJSON(data);
		console.log(resultNamesOfStudents);
		
		
	//	$('#userid').inputosaurus('option','autoCompleteSource', resultNamesOfStudents);
		}
	});*/
	  
	  
	  $.post(actionUrl,{categorySelect:categorySelect,classSelect:classSelect,sectionSelect:sectionSelect,newInterface:true})//,dummyNo:randomNumber});
	 			
	}

	function addKudoTable()
	{
		var html = '';
		<?php            
            if(is_array($arrKudos) && count($arrKudos)>0)
            {
                $onGoingMonth = 0;
                $onGoingYear = 0;
                $i = 0;
                $monthCnt = 0;
                foreach($arrKudos as $kudo_id=>$kudo_details)
                { 
                    $month = date('m', strtotime($kudo_details['sent_date']));                  
                    $year = date('Y', strtotime($kudo_details['sent_date']));
                    $gender = getGender($kudo_details['receiver']); 
                    $type = $kudo_details['kudo_type']; 
                    $message = $kudo_details['message'];
                    $message = preg_replace("/[\r\n]+/", " ", $message);
                    $imageSrc = preg_replace('/\s+/', '', $type);
                    $imageSrc = strtolower($imageSrc);
                    if($gender=='Boy'||$gender=='Girl'){ $genderToShow=$gender;} else {$genderToShow='noGender';}
					//echo "onGoingMonth - ".$onGoingMonth." onGoingYear - ".$onGoingYear."<br>";
                    if($onGoingMonth != $month || $onGoingYear != $year)    
                    {
                    	//echo "inside if <br>";
                        $onGoingMonth = $month;
                        $monthCnt++;
                        $onGoingYear = $year;
                        $i = 0;
                      
                       $html .= "<table class=\"kudoMonthYearTbl\"><tr>";	
                       $html .= "<td class=\"kudoMonthYearTd\"><hr></td><td class=\"kudoMonthYearTd\">- - - -  ".date("F, Y", strtotime("01-$month-$year"))."  - - - -</td><td class=\"kudoMonthYearTd\"><hr></td>";
                       $html .= "</table>";
                    }       
                        $html .= "<div id=\"kudosTd-$kudo_id\" class=\"kudosTd\">";
                           $html .= "<table border=\"0\" width=\"100%\" id=\"KudoSummary\" class=\"KudoSummary\">";
                               $html .= "<tr>";
                                   $html .= "<td id=\"Figurine\">";
                                       $html .= "<img src=\"../userInterface/src/kudos/images/".$genderToShow.".png\" title=\"".fetchFullName($kudo_details['receiver'])."\" height=\"50px\" width=\"50px\"/>";
                                   $html .= "</td>";
                                   $html .= "<td class=\"kudoTitle\">";
                                       $html .= "<span style=\"color:black;font-weight:bold;\">".fetchFullName($kudo_details['receiver'])." received ".$type." from ".fetchFullName($kudo_details['sender'])."</span> <br/>";
                                  	   $html .= "<span style=\"color:#4D4D4D;\">".date('d F, Y', strtotime($kudo_details['sent_date']))."<span>";
                                   $html .= "</td>";
                                   $html .= "<td style=\"text-align: right;vertical-align:top;\">";
                                       $html .= "<img  src=\"../userInterface/src/kudos/images/".$imageSrc.".png\" height=\"100px\" width=\"100px\">";
                                   $html .= "</td>";
                                   //echo "username - ".$userName." sender - ".$kudo_details['sender']." userCategory - ".$category."<br>";
                                   if(($userName == $kudo_details['sender'] && strcasecmp($category, "teacher") == 0) || strcasecmp($category, "School Admin") == 0)
                                   {
	                                   $html .= "<td class=\"deleteKudoTd\" style=\"text-align:right;vertical-align:top;width:16px;\">";
	                                    	$html .= "<span class=\"deleteKudo\" onclick=\"deleteThisKudo(".$kudo_id.",0)\"></span>";
	                                   		$html .= "<input type=\"hidden\" name=\"userCategory\" id=\"userCategory\" value=\"".$category."\"/>";
	                                   		$html .= "<input type=\"hidden\" name=\"childClass\" id=\"childClass\" value=\"".$childClass."\"/>";
	                                   		$html .= "<input type=\"hidden\" name=\"childSection\" id=\"childSection\" value=\"".$childSection."\"/>";
	                                   $html .= "</td>";                                   	
                                   }
                               $html .= "</tr>";
                               $html .= "<tr>";
                               	   $html .= "<td colspan=\"4\" class=\"message\">";
                               		   $html .= $message;
                               	   $html .= "</td>";                                   
                               $html .= "</tr>";
                            $html .= "</table>";
                       $html .= "</div>";
                }
            }
            ?>
		$("#divKudos").html('<?=$html?>');
		if(window.navigator.userAgent.indexOf("MSIE") > 0)
			$(".deleteKudoTd").css({"width":"0px"});
	}

</script>

<style>
	#countDown {
	width:100%;
	position:absolute;
	z-index:1000;
	background-color:#FFFFFF;
	display:none;
	text-align:center;
}
</style>
</head>

<body class="translation" onLoad="load()" onResize="load()" style="overflow: auto">
<!--<div id="countDown"><img src="http://d2tl1spkm4qpax.cloudfront.net/content_images/newUserInterface/teasers/countDown.gif" width="401" height="401"></div>-->
<?php include("eiColors.php") ?>
<div id="fixedSideBar">
	<?php include("fixedSideBar.php") ?>
</div>
<div id="topBar">
	<?php include("topBar.php"); ?>
	<link rel="stylesheet" href="../userInterface/src/kudos/styles/jquery-ui.css" />
	<link rel="stylesheet" href="../userInterface/src/kudos/styles/style.css?ver=3"/>
</div>
<div id="sideBar">
			<?php include("sideBar.php") ?>
</div>

<div id="container">

			<form name="formmain" id="formmain" method="POST">
            <input type="hidden" name="hdnAction" id="hdnAction" value="<?=$_POST['hdnAction']?>"/>	
			<input type="hidden" name="hdnType" id="hdnType" value="<?=$_POST['hdnType']?>"/>	
			<input type="hidden" name="hdnTo" id="hdnTo" value="<?=$_POST['hdnTo']?>"/>	
            <input type="hidden" name="hdnToClass" id="hdnToClass" value="<?=$_POST['hdnToClass']?>"/>
            <input type="hidden" name="hdnToSection" id="hdnToSection" value="<?=$_POST['hdnToSection']?>"/>
			<input type="hidden" name="hdnMessage" id="hdnMessage" value="<?=$_POST['hdnMessage']?>"/>
            <input type="hidden" name="hdnCategoryDropdown" id="hdnCategoryDropdown" value="<?=$_POST['hdnCategoryDropdown']?>"/>			
			
            <?php
				include('../userInterface/src/kudos/kudos_header_teacherInterface.php');
			?>
            			
			<table align="center" width="95%" border="0" class="tblTypes" style="padding-top:10px;"> <!--style="background-image: url('../images/background/bg.png')">-->
				<tr>
					<td>
						<input type="button" class="TypeButton" name="btnThankYou" id="btnThankYou" value="Thank You"/>
					</td>
					<td>
						<input type="button" class="TypeButton" name="btnGoodWork" id="btnGoodWork" value="Good Work"/>
					</td>
					<td>
						<input type="button" class="TypeButton" name="btnImpressive" id="btnImpressive" value="Impressive"/>
					</td>
					<td>
						<input type="button" class="TypeButton" name="btnExceptional" id="btnExceptional" value="Exceptional"/>
					</td>
				</tr>
			</table>
			
            
            
           <div id="kudo_body_teacher">
           
           <div id="showAll" style="display:none;">
            
            <form id="getCustomKudos">
            <table style="margin-left:38%; margin-top:2%;">
            <tr>
            <td>
            <select onChange="onChangeSubmit(22, 'category')" style="font-family:Conv_HelveticaLTStd-Cond;" name="category-select-dropdown-2" id="category-select-dropdown-2" type="text">
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>               
                            </select>
            </td><td>                
                            <select onChange="loadSectionDetails(2);" style="font-family:Conv_HelveticaLTStd-Cond;" name="class-select-dropdown-2" id="class-select-dropdown-2" type="text">
                            <option id="class-list" >Class</option>               
                            </select>
            </td><td>                
                            <select onChange="onChangeSubmit(2, 'section')" style="font-family:Conv_HelveticaLTStd-Cond;" name="section-select-dropdown-2" id="section-select-dropdown-2" type="text">
                            <option id="section-list" >Section</option>               
                            </select>
            </td>
            <td><input id="submitCustomKudos" name="submitCustomKudos" type="button" value="Go" style="margin-left:15px;" /></td>
            </tr>
            </table>
            </form>
                            
                            
            
            </div>
           
           
           <div id="kudo_all_custom">
           
           </div>

			<div id="divKudos"></div>
			<script type="text/javascript">addKudoTable();</script>
			</div>
            
<? // MODAL BOX FOR SENDING KUDOS ?>
<table id="sendKudo" class="sendKudo" border="0" width="100%">
		<tr>
			<td>
				<img id='typeImage'/>
			</td>
		</tr>
        <tr>
        <td>


        		<form id="category-select" name="category-select" method="post">
        		<select onChange="onChangeSubmit(2, 'category')" style="font-family:Conv_HelveticaLTStd-Cond;" name="category-select-dropdown" id="category-select-dropdown" type="text">
             	<option value="student">Student</option>
 				<option value="teacher">Teacher</option>               
                </select>
                
                <select onChange="loadSectionDetails(3);" style="font-family:Conv_HelveticaLTStd-Cond;" name="class-select-dropdown" id="class-select-dropdown" type="text">
                <option id="class-list" value= "" >All</option>               
                </select>
                
                <select onChange="onChangeSubmit(2, 'section')" style="font-family:Conv_HelveticaLTStd-Cond;" name="section-select-dropdown" id="section-select-dropdown" type="text">
             	<option id="section-list" value= "" >All</option>               
                </select>
                
                
                </form>
        </td>                                         
        </tr>
        <tr>
			<td>
				<input type="text" autocomplete=off name="userid" class="To" id="userid" placeholder="To" required spellcheck="false" title="To"/>
				<input type="hidden" name="useridval" id="useridval">
			</td>
		</tr>
		<tr>
			<td>
				<textarea class="Message" name="txtMessage" id="txtMessage" rows="5" cols="30" placeholder="Message" title="Message"></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<hr/>
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" id="btnSendKudo" name="btnSendKudo" class="btnSendKudo" value="Send" />
			</td>
		</tr>
		<tr>
			<td>&nbsp;
				
			</td>
		</tr>
</table>
            
            </form>
            
<div style="display:none"><div id="certificateModal" class="certificateModal" style="width:1000px; height:513px;"></div></div>
	
</div>
<?php include("footer.php");?>
</body>
</html>