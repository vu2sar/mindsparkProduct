<?php

	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	include("header.php");
         @include("classes/testTeacherIDs.php");
   $keys = array_keys($_REQUEST);
	foreach($keys as $key)
	{
		${$key} = $_REQUEST[$key] ;
	}

	if(!isset($_SESSION['userID']))
	{
		header("Location:logout.php");
		exit();
	}

	$userID     = $_SESSION['userID'];
	$schoolCode = $_SESSION['schoolCode'];

	$query  = "SELECT childName, startDate, endDate, category, subcategory, username FROM adepts_userDetails WHERE userID=".$userID;
	$result = mysql_query($query) or die(mysql_error());
	$line   = mysql_fetch_array($result);
	$userName 	= $line[0];
	$startDate  = $line[1];
	$endDate    = $line[2];
	$category   = $line[3];
	$subcategory = $line[4];
    $loginID = $line[5];

	if(strcasecmp($category,"School Admin")!=0 && strcasecmp($category,"Teacher")!=0 && strcasecmp($category,"Home Center Admin")!=0)
	{
		echo "You are not authorised to access this page!";
		exit;
	}

	if(strcasecmp($category,"School Admin")==0)
	{
		/*$query  = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
		           FROM     adepts_userDetails
		           WHERE    schoolCode=$schoolCode AND category='STUDENT' AND subcategory='School' AND enabled=1 AND subjects like '%".SUBJECTNO."%' AND enddate>=curdate()
		           GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection ";*/
		$query  = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
		           FROM     adepts_userDetails
		           WHERE    schoolCode=$schoolCode AND category='STUDENT' AND subcategory='School' AND enabled=1 AND subjects like '%".SUBJECTNO."%' AND enddate>=curdate()
		           GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection ";
	}
	elseif (strcasecmp($category,"Teacher")==0)
	{
		/*$query = "SELECT   class, group_concat(distinct section ORDER BY section)
				  FROM     adepts_teacherClassMapping
				  WHERE    userID = $userID AND subjectno=".SUBJECTNO."
				  GROUP BY class ORDER BY class, section";*/

		 $query  = "SELECT class, group_concat(distinct section ORDER BY section) FROM adepts_teacherClassMapping WHERE EXISTS (SELECT userID FROM adepts_userDetails WHERE schoolCode = $schoolCode AND childClass = class AND childSection = section AND enabled = 1 AND endDate >= CURDATE()) AND userID = $userID AND subjectno=2 GROUP BY class ORDER BY class, section";
	}
	elseif (strcasecmp($category,"Home Center Admin")==0)
	{
		$query  = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
		           FROM     adepts_userDetails
		           WHERE    category='STUDENT' AND subcategory='Home Center' AND schoolCode=$schoolCode AND enabled=1 AND subjects like '%".SUBJECTNO."%' AND enddate>=curdate()
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
	if($student_userID==""){
		$childName="";
		$name="";
	}
	$class    = isset($_POST['class'])?$_POST['class']:"";
	$section  = isset($_POST['section'])?$_POST['section']:"";
	$name  	  = isset($_POST['childName'])?$_POST['childName']:"";
	
	$allDisplayedUsersNumber = array();
	$fullUserDetails = array();
?>


<title>Edit Student Details</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/editStudentDetails.css" rel="stylesheet" type="text/css">
<!-- <script src="http://code.jquery.com/jquery-1.9.1.js"></script> -->
<!-- <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" /> -->
  <!--<script src="http://code.jquery.com/jquery-1.9.1.js"></script>-->
  <!--<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>-->
  <script>
  $(function() {
    $( ".datepicker" ).datepicker({ dateFormat: 'dd-mm-yy'});
  });
  </script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script language="javascript" type="text/javascript" src="libs/suggest1.js"></script>
<script language="javascript" type="text/javascript" src="libs/suggest2.js"></script>
<script type="text/javascript" src="/mindspark/js/load.js"></script>
<!-- validation js contains all validations of form -->
<script language="javascript" type="text/javascript" src="../../script/form_validation.js"></script>
<script type = "text/javascript">
var isShift=false;
var seperator = "-";
var userArray= new Array();
var classArray1= new Array();
var sectionArray1= new Array();
var respUserId= new Array();
function trim(query)
{
    return query.replace(/^\s+|\s+$/g,"");
}
// function DateFormat(txt , keyCode)
// {
//     if(keyCode==16)
//         isShift = true;
//     //Validate that its Numeric
//     if(((keyCode >= 48 && keyCode <= 57) || keyCode == 8 ||
//          keyCode <= 37 || keyCode <= 39 ||
//          (keyCode >= 96 && keyCode <= 105)) && isShift == false)
//     {
//         if ((txt.value.length == 2 || txt.value.length==5) && keyCode != 8)
//         {
//             txt.value += seperator;
//         }
//         return true;
//     }
//     else
//     {
//         return false;
//     }
// }	

		
</script>
<?php
    $child_All_List=array();
    $cUserID="";
    //fill initial data
		$query = "SELECT childName,childClass,userID, childSection 
				  FROM   adepts_userDetails
				  WHERE category='STUDENT' AND subcategory='SCHOOL' AND enabled=1  AND enddate>=curdate()
				  AND schoolcode=$schoolCode AND subjects like '%".SUBJECTNO."%'
				  ORDER BY childName";

    $result = mysql_query($query);
    $userList = "";
    while ($line=mysql_fetch_array($result)) {
        $userList .= $line[1]." (".$line[0].")~";
        $temp=$line[0]." (".$line[1];
        if($line[3]!="")
        	$temp .= $line[3];
        $temp .= ")";
        $child_All_List[]=$temp;
        $cUserID=$line[2];
		$cClass = $line[1];
		$cSection = $line[3];
?>
<script>

        userArray.push(' <?=trim($temp)?>');
        respUserId.push('<?=$cUserID?>');
		classArray1.push('<?=$cClass?>');
		sectionArray1.push('<?=$cSection?>');

        </script>
<?php
    } ?>
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
	
	var gradeArray   = new Array();
    var sectionArray = new Array();
    var EditedUserIds = new Array();
	<?php
		for($i=0; $i<count($classArray); $i++)
		{
		    echo "gradeArray.push($classArray[$i]);\r\n";
		    echo "sectionArray[$i] = new Array($sectionArray[$i]);\r\n";
		}
	?>
	function setSection(sec)
	{
		var cls = document.getElementById('lstClass').value;

		if(document.getElementById('lstSection'))
		{
		    var obj = document.getElementById('lstSection');
	        removeAllOptions(obj);
		    if(cls=="")
		    {
		        document.getElementById('lstSection').style.display = "inline";
		        document.getElementById('lstSection').selectedIndex = 0;
		    }
		    else
		    {
		    	for(var i=0; i<gradeArray.length && gradeArray[i]!=cls; i++);
		       	if(sectionArray[i].length>0)
		       	{
					$(".noSection").css("visibility","visible");
	    	    	for (var j=0; j<sectionArray[i].length; j++)
	    	       	{
	    	        	OptNew = document.createElement('option');
	    	            OptNew.text = sectionArray[i][j];
	    	            OptNew.value = sectionArray[i][j];
	    	            if(sec==sectionArray[i][j])
	    	            	OptNew.selected = true;
	    	            obj.options.add(OptNew);
	    	        }
	    	        document.getElementById('lstSection').style.display = "inline";
	    	        document.getElementById('lblSection').style.display = "inline";
		        }
				else
				{
					$(".noSection").css("visibility","hidden");
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

	function ajaxObject()
	{
		var xmlHttp;
		try {
			// Firefox, Opera 8.0+, Safari
		  	xmlHttp=new XMLHttpRequest();
		}
		catch (e){
			// Internet Explorer
		  	try{
		    	xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		    }
		  	catch (e){
		    	try{
		      		xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		      	}
		    	catch (e){
		      		alert("Your browser does not support AJAX!");
		      		return false;
		      	}
		    }
		}
		return xmlHttp;
	}
	function checkenterkey(event)
	{
		if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
	}
	function validateUser(userID)
	{
	    val = document.getElementById("userNameTxt"+userID).value;
	    if(!validateUserName(val)){	    	            
            	$("#userNameTxt"+userID).focus();             	         
            return false;         
	    }
	    else{
		if(document.getElementById("userNameTxt"+userID).value != document.getElementById("userNameHdn"+userID).value || document.getElementById("childNameTxt"+userID).value != document.getElementById("childNameHdn"+userID).value)
		{
			document.getElementById("namechangereason"+userID).style.display = "table-row";
		}
		else
		{
			document.getElementById("changereason"+userID).value = '';
			document.getElementById("namechangereason"+userID).style.display = "none";
		}
	}

	}

	function validatechildnametext(userID)
	{
		if(!onlyAlpha($("#childNameTxt"+userID).val())) {
					$("#childNameTxt"+userID).focus();					
				}else{
		if(document.getElementById("userNameTxt"+userID).value != document.getElementById("userNameHdn"+userID).value || document.getElementById("childNameTxt"+userID).value != document.getElementById("childNameHdn"+userID).value)
		{
			document.getElementById("namechangereason"+userID).style.display = "table-row";
		}
		else
		{
			document.getElementById("changereason"+userID).value = '';
			document.getElementById("namechangereason"+userID).style.display = "none";
		}}
	}

	function EditTheFields(srno,userID,userName,childName,childEmail,DOB,parentEmail,childClass,childSection)
	{
		$("#field"+userID+"5").removeAttr('style');
		var classIndex = -1;
		var sectionArr = new Array();

		document.getElementById("edit"+userID).style.display = "none";
		document.getElementById("cancel"+userID).style.display = "";

		//document.getElementById("resetPassword"+userID).style.display = "none";

  		tempHTML = "<input size='15' type='text' name='userNameTxt"+userID+"' id='userNameTxt"+userID+"' onblur='return validateUser("+userID+")' OnKeyPress='checkenterkey(event)' maxlength='20'/>";

		 
		
                tempHTML = tempHTML + "<input size='15' type='hidden' name='userNameHdn"+userID+"' id='userNameHdn"+userID+"' value='"+userName+"' />";
  		document.getElementById("field"+userID+"1").innerHTML=tempHTML;
                document.getElementById("userNameTxt"+userID).value=userName;                

  		tempHTML = "<input size='15' type='text' name='childNameTxt"+userID+"' id='childNameTxt"+userID+"' onblur='validatechildnametext("+userID+")' OnKeyPress='checkenterkey(event)' maxlength='20'>";

		tempHTML = tempHTML + "<input size='15' type='hidden' name='childNameHdn"+userID+"' id='childNameHdn"+userID+"' value='"+childName+"'>";
		
  		document.getElementById("field"+userID+"2").innerHTML=tempHTML;
  		document.getElementById("childNameTxt"+userID).value=childName;

  		tempHTML = "<input size='20' type='text' name='childEmailTxt"+userID+"' id='childEmailTxt"+userID+"' maxlength='50' >";
  		document.getElementById("field"+userID+"3").innerHTML=tempHTML;
  		document.getElementById("childEmailTxt"+userID).value=childEmail;

  		//DOB
  		if(DOB != "N.A.")
  			tempHTML = "<input type='text' readonly class='datepicker' onkeydown='return DateFormat(this, event.keyCode)' maxlength='10'  name='DOBTxt"+userID+"' id='DOBTxt"+userID+"' size='8' value='"+DOB+"'  onblur=\"validateDate(this,'"+DOB+"');\">";
  		else
  			tempHTML = "<input type='text' readonly class='datepicker' onkeydown='return DateFormat(this, event.keyCode)' maxlength='10' name='DOBTxt"+userID+"' id='DOBTxt"+userID+"' size='8' value=''  onblur=\"validateDate(this,'"+DOB+"');\">";
  		document.getElementById("field"+userID+"4").innerHTML=tempHTML;

  		tempHTML = "<input size='27' type='text' name='parentEmailTxt"+userID+"' id='parentEmailTxt"+userID+"' >";
  		document.getElementById("field"+userID+"5").innerHTML=tempHTML;
  		document.getElementById("parentEmailTxt"+userID).value=parentEmail;

  		tempHTML = "<select name='childClassTxt"+userID+"' id='childClassTxt"+userID+"' disabled=true> <option value=''>All</option>";
  		for(var k=0; k<gradeArray.length; k++)
  		{
  			tempHTML += "<option value='"+gradeArray[k]+"'";
  			if(childClass==gradeArray[k])
  			{
  				tempHTML +=" selected ";
  				classIndex = k;
  			}
  			tempHTML +=">"+gradeArray[k]+"</option>";
  		}
  		tempHTML += "</select>";
  		document.getElementById("field"+userID+"6").innerHTML=tempHTML;

  		if(classIndex != -1)
  			var sectionStr = sectionArray[classIndex];

  		if(sectionStr.length>0) //if the class has sections, show the drop down
  		{
      		tempHTML = "<select name='childSectionTxt"+userID+"' id='childSectionTxt"+userID+"' >";
      		for(k=0; k<sectionStr.length; k++)
      		{
      			tempHTML += "<option value='"+sectionStr[k]+"'";
      			if(childSection==sectionStr[k])
      			{
      				tempHTML +=" selected ";
      			}
      			tempHTML +=">"+sectionStr[k]+"</option>";
      		}
      		tempHTML += "</select>";
                tempHTML += "<input type='hidden' id='childSectionHdn"+userID+"' name='childSectionHdn"+userID+"' value='"+childSection+"' />"
      		document.getElementById("field"+userID+"7").innerHTML=tempHTML;
  		}

  		tempHTML = "<select name='password"+userID+"' id='lstPwd"+userID+"' >";
  		tempHTML += "<option value='0'>No change</option>";
  		tempHTML += "<option value='1'>Reset to username</option>";
  		tempHTML += "<option value='2'>Remove password</option>";

  		tempHTML += "</select>";
  		document.getElementById("field"+userID+"8").innerHTML=tempHTML;

		$( ".datepicker" ).datepicker({ dateFormat: 'dd-mm-yy' });
  		document.getElementById("flagCheck"+userID).value = 1;

	}

	function CancelTheFields(srno,userID,userName,childName,childEmail,DOB,parentEmail,childClass,childSection)
	{
		$("#field"+userID+"5").css('word-break','break-word');
		document.getElementById("edit"+userID).style.display = "";
		document.getElementById("cancel"+userID).style.display = "none";
		//document.getElementById("resetPassword"+userID).style.display = "";

		tempHTML = userName;
  		document.getElementById("field"+userID+"1").innerHTML=tempHTML;

  		tempHTML = childName;
  		document.getElementById("field"+userID+"2").innerHTML=tempHTML;

  		tempHTML = childEmail;
  		document.getElementById("field"+userID+"3").innerHTML=tempHTML;

  		tempHTML = DOB;
  		document.getElementById("field"+userID+"4").innerHTML=tempHTML;

  		tempHTML = parentEmail;
  		document.getElementById("field"+userID+"5").innerHTML=tempHTML;

  		tempHTML = childClass;
  		document.getElementById("field"+userID+"6").innerHTML=tempHTML;

  		tempHTML = childSection;
  		document.getElementById("field"+userID+"7").innerHTML=tempHTML;

  		document.getElementById("field"+userID+"8").innerHTML="****";

  		document.getElementById("flagCheck"+userID).value = 0;
		document.getElementById("changereason"+userID).value = '';
		document.getElementById("namechangereason"+userID).style.display = "none";
	 }

	 function resetPAssword(srno, userID)
	 {
	 	var mode = "resetPassword";
	 	xmlHttp=ajaxObject();

	 	if(mode=='resetPassword')
		{
			xmlHttp.onreadystatechange=function()
		    {
		    	if(xmlHttp.readyState==4)
		      	{
		      		alert(xmlHttp.responseText);
		       	}
		    }
		    var url="ajaxRequestResponse_profileEdit.php";
		    url=url + "?mode="+mode+"&srno="+srno+"&userID="+userID;
		  	xmlHttp.open("GET",url,true);
		  	xmlHttp.send(null);
		}
	 }

	function SaveTheData()
	{
		var userStr = document.getElementById('userIDString').value;
		var userArr = userStr.split(",");
		var editedUserID = new Array();
		var repeatUserName = "";
		var changereasonUserName = "";
		var flagBlank = 0;
		var countUpdate = 0;

		for(var t=0; t<userArr.length; t++)
		{
			if(document.getElementById("flagCheck"+userArr[t]).value == 1)
			{
				if($.trim($("#userNameTxt"+userArr[t]).val()) == "" || $.trim($("#childNameTxt"+userArr[t]).val()) == "") {
					alert("Username and student name can not be empty.");
					$("#childNameTxt"+userArr[t]).focus();
					flagBlank = 1;
					break;
				}
			
			/* var ck_username = /^[A-Za-z0-9_.]{3,20}$/; */			
				if(!validateUserName($("#userNameTxt"+userArr[t]).val())){	    	            
            	$("#userNameTxt"+userArr[t]).focus();            
            return false;         
	    }
				if(!onlyAlpha($("#childNameTxt"+userArr[t]).val())) {					
					$("#childNameTxt"+userArr[t]).focus();
					return false;;
				}
				
				if($.trim($("#childEmailTxt"+userArr[t]).val()) != "") {
					if(!validateEmail($("#childEmailTxt"+userArr[t]).val())) {
						alert("Child's email address is invalid.");
						$("#childEmailTxt"+userArr[t]).focus();
						return false;
					}
				}
				/*if($.trim($("#parentEmailTxt"+userArr[t]).val()) != "") {
					if(!validateEmail($("#parentEmailTxt"+userArr[t]).val())) {
						alert("Parent email address is invalid.");
						$("#parentEmailTxt"+userArr[t]).focus();
						break;
					}
				}*/
				if($("#parentEmailTxt"+userArr[t]).val() != "")		// For the mantis task 12360
				{
					var emailIdStr = $("#parentEmailTxt"+userArr[t]).val();
					var wrongEmailIdsCount = 0;
					var wrongEmailIdsArr = new Array();
					if(emailIdStr.indexOf(",") > 0)
					{
						var emailIdArr = emailIdStr.split(",");
						for(var i=0; i<emailIdArr.length; i++)
						{
							var emailId = $.trim(emailIdArr[i]);
							if(!validateEmail(emailId)) 
							{
								wrongEmailIdsArr.push(emailId);
								wrongEmailIdsCount++;
							}
						}
						if(wrongEmailIdsCount > 0)
						{
							alert("One of the parent email addresses are invalid.");
							$("#parentEmailTxt"+userArr[t]).focus();
							break;
						}
					}
					else
					{
						var emailId = $.trim($("#parentEmailTxt"+userArr[t]).val());
						if(!validateEmail(emailId))
						{
							alert("Parent email address is invalid.");
							$("#parentEmailTxt"+userArr[t]).focus();
							break;
						}
					}
				}
				
				countUpdate++;
			}
		}

		if(countUpdate > 0 && flagBlank == 0) {
			for(var t=0; t<userArr.length; t++)
			{
				if(document.getElementById("flagCheck"+userArr[t]).value == 1)
				{
					editedUserID.push(userArr[t]);
				}
			}

			var count = 0;

			for(t=0; t<editedUserID.length; t++)
			{
				count = 0;
				while(count<editedUserID.length)
				{	
					if(document.getElementById("userNameTxt"+editedUserID[t]).value==document.getElementById("userNameTxt"+editedUserID[count]).value && t!=count)
					{
						repeatUserName += " "+document.getElementById("userNameTxt"+editedUserID[t]).value+" ,";
						break;
					}
					if($("#namechangereason"+editedUserID[t]).css('display') != 'none')
					{
						if(document.getElementById("changereason"+editedUserID[t]).value == '')
						{
							changereasonUserName += " "+document.getElementById("userNameTxt"+editedUserID[t]).value+" ,";
							break;
						}
					}
					count++;
				}
			}

			if(changereasonUserName != "")
			{	       
						alert("Reason for change can not be empty for"+changereasonUserName.replace(/,\s*$/, ""));
						flagBlank = 1;
						//break;
			}
			else
			{
				if(repeatUserName == "")
				{
					document.getElementById("pageAction").value = "save";
					setTryingToUnload();
					document.frmTeacherReport.submit();
				}
				else
				{
					repeatUserName = repeatUserName.substr(0,repeatUserName.length-1);
					alert("Usernames can not be the same : "+repeatUserName);
				}
			}
		}
	}
	
	/*
	function validateEmail(email) {
	    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	    return re.test(email);
	}
	
	function onlyAlpha(value) {
		var regex = /^[a-zA-Z ]*$/;
        if (regex.test(value)) {
            return true;
        } else {
            return false;
        }
	}
	*/	
	
function noDataFound(srno)
{
	
	if(srno==0 || typeof srno === 'undefined')
	{
		
		$('.noData').css('display','block');
		$('.dataPresent').css('display','none');
		$("#note").css("display","none");
	}
	
	
}

function suggestUserList(userArray)
{
    
	var e = document.getElementById("lstClass");
	var DummyArray = userArray;
	var strUser = e.options[e.selectedIndex].text;
	var f = document.getElementById("lstSection");
	var strUser1 = f.options[f.selectedIndex].text;
	if(strUser!="" && strUser1=="All" && strUser!="All"){
		var newUserArray= new Array();
		for(var i=0; i<userArray.length;i++)
	    {
			if(userArray[i].indexOf("("+strUser)>-1){
				newUserArray.push(userArray[i]);
			}
		}
		userArray = newUserArray;
		var obj1 = new actb(document.getElementById('childName'),userArray);
	}else if(strUser!="" && strUser1!="" && strUser!="All" && strUser1!="All"){
		var newUserArray= new Array();
		for(var i=0; i<userArray.length;i++)
	    {
			if(userArray[i].indexOf("("+strUser+strUser1)>-1){
				newUserArray.push(userArray[i]);
			}
		}
		userArray = newUserArray;
		var obj1 = new actb(document.getElementById('childName'),userArray);
	}else{
		var obj1 = new actb(document.getElementById('childName'),userArray);
	}
	userArray = DummyArray;
    

}

function showTrail(){
		var childname = trim(document.getElementById('childName').value);
		for(var i=0; i<userArray.length;i++)
	    {

	        if(trim(userArray[i])==trim(childname))
	        {
	            found = 1;
	            document.getElementById('student_userID').value=respUserId[i];
	            break;
	        }
	    }
		setTryingToUnload();
		return true;
	}


</script>
</head>
<body class="translation" onload="load();setSection('<?=$section?>');suggestUserList(userArray)" onresize="load()">

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
					<div id="pageText">Edit Student Details</div>
				</div>
			</div>
			<form id="frmTeacherReport" name="frmTeacherReport" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
			<input type="hidden"  id="student_userID" name="student_userID" value="">
			<table id="topicDetails">
				<td width="7%"><label>Class</label></td>
		        <td width="20%" style="border-right:1px solid #626161">
		            <select name="class" id="lstClass" onchange="setSection('');suggestUserList(userArray);" style="width:95%;">
					<?php if (strcasecmp($category,"Teacher")!=0) { ?>
					<option value="">All</option>
					<?php } ?>
					<?php for($i=0; $i<count($classArray); $i++)	{ ?>
						<option value="<?=$classArray[$i]?>" <?php if($class==$classArray[$i]) echo " selected";?>><?=$classArray[$i]?></option>
					<?php	}	?>
				</select>
		        </td>
				<?php if($hasSections) { ?>
				<td width="6%" class="noSection"><label id="lblSection" for="lstSection" style="margin-left:20px;">Section:</label></td>
		        <td width="24%" style="border-right:1px solid #626161" class="noSection">
		            <select name="section" id="lstSection" style="width:65%;" onchange="suggestUserList(userArray);">
					<option value="All">All</option>
				</select>
		        </td>
				<?php } ?>
				<td width="16%"><label for="childName">Child's Name</label></td>
				<td width="30%" id="childNameDiv">
		            <input type="text" name="childName" id="childName" value="<?=$childName?>" <?php if($childName=="") echo " enabled" ?> autocomplete="off" size="30">
		        </td>
				<td width="10%"><input type="submit" name="btnGo" id="btnGo" value="Go" onclick="return showTrail();"></td>
			</table>

			<input type="hidden" name="schoolCode" id="schoolCode" value="<?=$schoolCode?>">
<div align="center">
<?php

if($pageAction == "save" && !in_array($loginID,$testIDArray))
{
	$flagUserNameSame = 0;
	$storeUpdatedDataArr = array();
	$allDisplayedUsersNumber = explode(",", $userIDString);
	$userNamesRepeat = array();
        $DAUserSection=array();
	for($i=0; $i<count($allDisplayedUsersNumber); $i++)
	{
		$storeUpdatedDataArr = array();
		$temp = $allDisplayedUsersNumber[$i];
		//$NewOriginalStr = str_replace(" ","_",$temp);

		if(${"flagCheck".$temp} == 1)
		{
		    $storeUpdatedDataArr['changeReason'] = ${"changereason".$temp};  
			$storeUpdatedDataArr['userName'] = ${"userNameTxt".$temp};
			//$storeUpdatedDataArr['changeReason'] = ${"changereason".$temp};
                        $storeUpdatedDataArr['childName'] = ${"childNameTxt".$temp};
			$storeUpdatedDataArr['childEmail'] = ${"childEmailTxt".$temp};
			$storeUpdatedDataArr['DOB'] = ${"DOBTxt".$temp};
			if($storeUpdatedDataArr['DOB'] == "N.A." || $storeUpdatedDataArr['DOB']=="")
			{
				$storeUpdatedDataArr['DOB'] = "0000-00-00";
			}
			else
			{
				$storeUpdatedDataArr['DOB'] = date("Y-m-d",strtotime($storeUpdatedDataArr['DOB']));
			}
			$storeUpdatedDataArr['parentEmail'] = ${"parentEmailTxt".$temp};
			//$storeUpdatedDataArr['childClass'] = ${"childClassTxt".$temp};
			$storeUpdatedDataArr['childSection'] = ${"childSectionTxt".$temp};
                        $storeUpdatedDataArr['childSectionOld'] = ${"childSectionHdn".$temp};
                        $storeUpdatedDataArr['password'] = ${"password".$temp};
//			$flagUserNameSame = checkUserNameAvailability($temp,$storeUpdatedDataArr['userName']);
                        $flagDAEnabled=0;
                        if(trim($storeUpdatedDataArr['childSection'])!=trim($storeUpdatedDataArr['childSectionOld']))
                            $flagDAEnabled = checkIfDAEnabled($temp);
			if($flagDAEnabled != 1)
			{

//				$query = "UPDATE adepts_userDetails	SET
//						username = '".trim($storeUpdatedDataArr['userName'])."',
//						childName = '".trim($storeUpdatedDataArr['childName'])."',
//						childEmail = '".trim($storeUpdatedDataArr['childEmail'])."',
//						childDob = '".$storeUpdatedDataArr['DOB']."',
//						parentEmail = '".trim($storeUpdatedDataArr['parentEmail'])."',
//						childSection = '".$storeUpdatedDataArr['childSection']."',
//						updated_by='".$_SESSION['username']."'";
//				if($storeUpdatedDataArr['password']==1) //implies reset the pwd to username
//				    $query .= ", password=password('".trim($storeUpdatedDataArr['userName'])."')";
//				elseif($storeUpdatedDataArr['password']==2) //implies reset the pwd to blank
//				    $query .= ", password=''";
//				$query .= " WHERE userID = '".$temp."'";
                            $nameArray = explode(' ',$storeUpdatedDataArr['childName']);
                            $firstName = trim($nameArray[0]);
                            $lastName = '';
                            if(count($nameArray)>1)
                                $lastName = trim($nameArray[1]);
                                $query = "UPDATE educatio_educat.common_user_details SET 
                                    username = '".trim($storeUpdatedDataArr['userName'])."',
                                      first_name = '$firstName', last_name='$lastName', Name='".$storeUpdatedDataArr['childName']."',
						childEmail = '".trim($storeUpdatedDataArr['childEmail'])."',
						dob = '".$storeUpdatedDataArr['DOB']."',
						additionalEmail = '".trim($storeUpdatedDataArr['parentEmail'])."',
						section = '".$storeUpdatedDataArr['childSection']."',
						updated_by='".$_SESSION['username']."'";
				if($storeUpdatedDataArr['password']==1) //implies reset the pwd to username
				    $query .= ", password=password('".trim($storeUpdatedDataArr['userName'])."')";
				elseif($storeUpdatedDataArr['password']==2) //implies reset the pwd to blank
				    $query .= ", password=''";
				$query .= " WHERE MS_userID = '".$temp."'";
                // echo $query;exit;
                $flag=0;
				$result = mysql_query($query);
                if(mysql_errno()==1062){
                   echo "<b style='color:red;position:relative;top:5px;font-size:120%'>Username already exists for username: ".$storeUpdatedDataArr['userName']."</b><br>";                   
                   $flag=1;
                }
                elseif(mysql_errno()!=0){
                    die("Error in updating the details for username: ".$storeUpdatedDataArr['userName']."-".mysql_errno());
                }
				if($storeUpdatedDataArr['changeReason'] != '')
				{
					$insertquery = "insert into userDetailChangeReason(userID,changeReason, lastModified) values(".$temp.",'".$storeUpdatedDataArr['changeReason']."',NOW())";
					$result = mysql_query($insertquery);
				}
			}
			else
			{
//				$userNamesRepeat[$temp] = $storeUpdatedDataArr[userName];
                            $DAUserSection[$temp] = $storeUpdatedDataArr[userName];
			}
		}
	}

	$btnGo = "Go";
	if(count($DAUserSection)>0)
		echo "<b>Please check the errors higlighted in red</b>";
    elseif($flag==0)
        echo "<b style='color:green;position:relative;top:8px;font-size:120%'>Details updated successfully.</b>";
}

?>
<?php

if(isset($btnGo) && $btnGo != '')
{
	$childName = $name;
	$classValue = $class;
	$sectionValue = $section;
	$jointArray = array();
	$count = 0;
	
  if($sectionValue=="All" && $classValue!="")
  {
  	if($sectionArray[array_search($classValue, $classArray)]!="")
	$sectionOfClass = explode(',',$sectionArray[array_search($classValue, $classArray)]) ;
	if(count($sectionOfClass)==0)
	$sectionOfClass[0] = "";
  }	
  elseif($sectionValue=="All" && $classValue=="")
  	$sectionOfClass[0] = "";
  else
  	$sectionOfClass[0] = "'".$sectionValue."'";

	?>
	<div style="width:97%;margin-bottom:15px;" align="left" class="dataPresent"><br/><strong>Please note that STUDENT DATA IS ESSENTIAL FOR US TO TRACK PROGRESS SO PLEASE ENSURE YOU DO NOT INTERCHANGE STUDENT DETAILS</strong></div>
	<table align="center" cellpadding="3" cellspacing="0" class="gridtable dataPresent" border="1">
		<thead>
		<tr>
		  	<th class="header" align="center" width="2%">Sr.<br/>No.</th>
		    <th class="header" align="center" width="12%">Username</th>
			<th class="header" align="center" width="12%">Name</th>
		    <th class="header" align="center" width="15%">Child's e-mail</th>
		    <th class="header" align="center" width="10%">DOB</th>
		    <th class="header" align="center" width="20%">Parent e-mail</th>
		    <th class="header" align="center" width="5%">Class</th>
		    <th class="header" align="center" width="5%">Section</th>
		    <th class="header" align="center" width="13%">Password</th>
		    <th class="header" width="8%">&nbsp;</th>
	  	</tr>
	  	</thead>
	  	<?php
		
		foreach($sectionOfClass as $key=>$value)
		{
		    $jointArray = getAllUserDetails($schoolCode,$classValue,$value,$student_userID);	
			$fullUserDetails = $jointArray[0];
			$allDisplayedUsersNumber = $jointArray[1];
			//$userIDStr = implode(",",$allDisplayedUsersNumber);
			$userIDStr .= implode(",",$allDisplayedUsersNumber).',';
			
			
	  	for($i=0; $i<count($fullUserDetails); $i++)
	  	{
			$srNo = $count+1;

	  	?>
	  	<tr>
	  		<td align="center" width="2%"><?=$srNo?></td>
	  		<?php
                $uid = $fullUserDetails[$i][0];
	  			for($j=0; $j<count($fullUserDetails[$i]); $j++)
	  			{

	  				$fieldNames = "field$uid$j";
	  				if($j != 0)
	  				{
			  			?>
			  				<td id=<?=$fieldNames?> <?php if($j==4) echo "align='center' nowrap"; else echo "align='left'"?> <?php if($j==5) echo 'style="word-break: break-word;"' ?> >
			  				<?php if($fullUserDetails[$i][$j] == "") echo "&nbsp;"; else echo $fullUserDetails[$i][$j];?>
			  				<?php if($j==1)
			  				{?>
			  					<div id="errorMsg<?=$uid?>" style="color:#FE2E2E"><?php if($DAUserSection[$uid]!="")echo "<b>Can not update section for ".$DAUserSection[$uid]." from this page as he/she is registered for DA. Please contact your Mindspark co-ordinator to change this.</b>"?></div>
			  				<?php }?>
			  				</td>
			  			<?php
	  				}
	  			}
	  		?>
	  		<td id="field<?=$uid.$j?>" >****</td>
  			<td >
				<input type="button" id="edit<?=$uid?>" value="Edit" onclick="EditTheFields('<?=$srNo?>','<?=$fullUserDetails[$i][0]?>','<?=$fullUserDetails[$i][1]?>','<?=$fullUserDetails[$i][2]?>','<?=$fullUserDetails[$i][3]?>','<?=$fullUserDetails[$i][4]?>','<?=$fullUserDetails[$i][5]?>','<?=$fullUserDetails[$i][6]?>','<?=$fullUserDetails[$i][7]?>')">
				<input type="button" style="display:none" id="cancel<?=$uid?>" value="Cancel" onclick="CancelTheFields('<?=$srNo?>','<?=$fullUserDetails[$i][0]?>','<?=$fullUserDetails[$i][1]?>','<?=$fullUserDetails[$i][2]?>','<?=$fullUserDetails[$i][3]?>','<?=$fullUserDetails[$i][4]?>','<?=$fullUserDetails[$i][5]?>','<?=$fullUserDetails[$i][6]?>','<?=$fullUserDetails[$i][7]?>')">
				<!--<br/>
				<input type="button" id="resetPassword<?=$uid?>" value="Reset Password" onclick="resetPAssword('<?=$srNo?>','<?=$fullUserDetails[$i][0]?>')">-->
			</td>
	  	</tr>
		<tr style='display:none;' id='namechangereason<?=$uid?>'>
			
			<td  colspan='10' align='left'  >
				<label style='margin-left: 6px; color:red;'>Reason for change in id/name</label>&nbsp;&nbsp;&nbsp;
				<input  style='box-shadow:0 0 2px 0; width: 400px;' type='text' name='changereason<?=$uid?>' id='changereason<?=$uid?>' OnKeyPress='checkenterkey(event)'/>
			</td>
		</tr>
		
		
	  	<input type="hidden" id="flagCheck<?=$uid;?>" name="flagCheck<?=$uid;?>" value="0">
	  	<?php
		$count++;
	  	}
		 }
		
	  	?>
	  	<span id="toAddNewRow"></span>
                   <?php if(!in_array($loginID,$testIDArray)) { ?>             
		<tr>
			<td colspan="10" align="center">
				<input type="button" id="save" value="Save" onclick="SaveTheData()" class="button">
			</td>
		</tr>
                   <?php } ?>
	</table>
	<div id='note' style="font-size:15px;font-weight:bold;margin-top:10px;margin-bottom:10px;">
		Note: You can enter multiple parent email ids separated by comma.
	</div>
	<div>
	<span style="font-size:15px;" class="noData"> No data found </span>
	</div>
	<input type="hidden" id="pageAction" name="pageAction" value="">
	<input type="hidden" id="userIDString" name="userIDString" value="<?=rtrim($userIDStr,',')?>">
	<?php
	echo "<script> noDataFound($count); </script>";
 }
?>

</div>
</form>		
			
		</div>
	</div>

<?php include("footer.php") ?>


<?php

function getAllUserDetails($schoolCode,$classValue,$sectionValue,$childName)
{
	$details = array();
	$userIdArr = array();
	$jointArray = array();
	$query = "SELECT userID,username,childName,childEmail,childDob,parentEmail,childClass,childSection FROM adepts_userDetails WHERE schoolCode=$schoolCode";

	if($classValue!="")
		 $query .= " AND childClass='$classValue'";
	if($sectionValue!="")
		$query .= " AND childSection=$sectionValue";
	if($childName!="")
		$query .= " AND userID LIKE '$childName'";

	$query .= " AND category='STUDENT' AND subcategory='School' AND enabled=1 AND subjects like '%".SUBJECTNO."%' AND enddate>=curdate() ORDER By childName";
	
	$result = mysql_query($query) or die("Error in query execution: ".mysql_error());

	$count = 0;
	while ($line = mysql_fetch_array($result))
	{
		$details[$count][0] = $line[0];
		$details[$count][1] = $line[1];
		$details[$count][2] = $line[2];
		$details[$count][3] = $line[3];
		$details[$count][4] = $line[4]!="0000-00-00"?date("d-m-Y",strtotime($line[4])):"N.A.";//date("d-m-Y",$line[4]);
		$details[$count][5] = $line[5];
		$details[$count][6] = $line[6];
		$details[$count][7] = $line[7];

		$count++;
		array_push($userIdArr,$line[0]);


	}
	$jointArray[0] = $details;
	$jointArray[1] = $userIdArr;

	return $jointArray;
}

function checkUserNameAvailability($userIDThatChanged,$newUserName)
{
	$flag = 0;
	$query = "select count(userID) FROM adepts_userDetails WHERE username='$newUserName' AND userID!='$userIDThatChanged'";

	$result = mysql_query($query);
	$line = mysql_fetch_array($result);
	$count = $line[0];

	if($count>0)
		$flag = 1;
	else
		$flag = 0;

	return $flag;
}

function checkIfDAEnabled($userID)
{
    	$query = "select DA_enabled FROM educatio_educat.common_user_details WHERE MS_userID=$userID";
        $result = mysql_query($query);
	$line = mysql_fetch_array($result);
	$DAEnabled = $line[0];
    	return $DAEnabled;
}
?>
