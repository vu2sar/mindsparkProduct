<?php
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	/*error_reporting(E_ERROR);*/

	@include("header.php");
	include("classes/clsTeacher.php");
	
	$offlineMode = false; //school is using in offline mode
	if($_SESSION['isOffline'] === true && SERVER_TYPE=='LOCAL')
		$offlineMode = true;
?>

<?php
	$userID 	= $_SESSION['userID'];
	$category   = $_SESSION['admin'];
	// if($category!="School Admin" || $offlineMode === true)
	// {
	// 	echo "<center><strong>You are not authorised to access this page</strong></center>";
	// 	exit();
	// }
	$flagForOffline = false;	
	if($_SESSION['isOffline'] === true && SERVER_TYPE=='LIVE')
	{
		$flagForOffline = true;
	}
	if($flagForOffline)
		include("logDeleteQuery.php");

	$errmsg = "";
	$query  = "SELECT childName,schoolCode FROM adepts_userDetails WHERE userID=".$userID;
	$result = mysql_query($query) or die($query.mysql_error());
	$line   = mysql_fetch_array($result);
	$Name 	    = $line[0];
	$schoolCode = $line[1];

	$classArray = $sectionArray = array();
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
	
	$result = mysql_query($query);
	while ($line=mysql_fetch_array($result))
	{
		array_push($classArray, $line[0]);
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
	/*$query  = "SELECT DISTINCT childSection FROM adepts_userDetails WHERE schoolCode=$schoolCode AND category='STUDENT' AND !isnull(childSection) AND childSection<>'' ORDER BY childSection";
	$result = mysql_query($query);
	while ($line=mysql_fetch_array($result))
		array_push($sectionArray, $line[0]);*/


	$teacher = new teacher();
	if(!isset($_POST['save']) && $_POST['userID']!="")
		$teacher->getDetails($_POST['userID']);

	if(isset($_POST['save']))
	{
		$teacher->setPostVariables();
		if($teacher->userID=="")
		{
			$errmsg = $teacher->insertDetails($schoolCode, $_POST['grade'],$_POST['section'], $_POST['subj']);
			
			if($errmsg == "-9") {
				$errmsg = "";
				$expected_username = strtolower(str_replace(" ","",$_POST['firstName']));
				if(!empty($_POST['lastName']))
					$expected_username .= ".".strtolower(str_replace(" ","",$_POST['lastName']));
				addtoforum_member($teacher->password,$schoolCode);
				$show_notification = "Teacher details saved successfully!<br />";
				$show_notification .= "<b>Name:</b> <u>".ucfirst($teacher->firstName)." ".ucfirst($teacher->lastName)."</u>, <b>Login ID:</b> <u>".$teacher->password."</u>";
				$show_notification .= "<span style=\"font-size: 11px;\">(*Login ID \"".$expected_username."\" is already taken by someone)</span>";
				
				$_SESSION['notification'] = "<center>".$show_notification."</center>";
				
			} else if($errmsg == "") {
				addtoforum_member($teacher->password,$schoolCode);
				$show_notification = "Teacher details saved successfully!<br />";
				$show_notification .= "<b>Name:</b> <u>".ucfirst($teacher->firstName)." ".ucfirst($teacher->lastName)."</u>, <b>Login ID:</b> <u>".$teacher->password."</u>";
				
				$_SESSION['notification'] = "<center>".$show_notification."</center>";
			}
			
			//echo "A".$errmsg;
			//if($errmsg=="")
				//mailDetails($teacher);
		}
		else
		{
			$errmsg = $teacher->updateDetails($_POST['grade'],$_POST['section'],  $_POST['subj']);
			
			if($errmsg == "") {
				$_SESSION['notification'] = "<center>Teacher details saved successfully!</center>";
			}
		}
		
		if($errmsg=="")
		{
			echo "<script>window.location='teacherDetails.php';</script>";
		}

	}
	if(isset($_POST['action']) && $_POST['action']=="Delete")
	{
	    $teacher->removeClassMapping($_POST['delClass'],$_POST['delSection'],$_POST['delSubject'], $_POST['delUserID'],$flagForOffline);
	}

	$noOfSubjects = getNoOfSubjects($schoolCode);

?>
 <meta charset="utf-8">

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/addEditTeacherDetails.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="/mindspark/js/plugins/countryFlagPlugin/css/intlTelInput.css">
    
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery_ui.js" type="text/javascript"></script>
<script type="text/javascript" src="/mindspark/js/load.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script src="/mindspark/js/plugins/countryFlagPlugin/js/intlTelInput.js"></script>
<script language="javascript" type="text/javascript" src="../../script/form_validation.js"></script>
<style type="text/css">
    .ui-menu .ui-menu-item a,
    .ui-menu .ui-menu-item a.ui-state-hover,
    .ui-menu .ui-menu-item a.ui-state-active {
        font-weight: normal;
        margin: -1px;
        text-align:left;
        font-size:14px;
    }
    .ui-autocomplete-loading {
        background: white url("css/images/ajax.gif") right center no-repeat;
    }    
    </style>
<script>
	var langType = '<?=$language;?>';
	function load(){
		var fixedSideBarHeight = window.innerHeight;
		var sideBarHeight = window.innerHeight-95;
		/*var containerHeight = window.innerHeight-115;*/
		$("#fixedSideBar").css("height",fixedSideBarHeight+"px");
		$("#sideBar").css("height",sideBarHeight+"px");
		$("#container").css("height",containerHeight+"px");
	}
</script>
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
	 function split(val) {
        return val.split(/,\s*/);
    }

    function extractLast(term) {
        return split(term).pop();
    }

    function extractFirst(term) {
        return split(term)[0];
    }
	 jQuery(function () {
        var $citiesField = jQuery("#txtCity");

        $citiesField.autocomplete({
            source: function (request, response) {
                jQuery.getJSON(
                    "http://gd.geobytes.com/AutoCompleteCity?callback=?&q=" + extractLast(request.term),
                    function (data) {
                        response(data);
                    }
                );
            },
            minLength: 3,
            select: function (event, ui) {
                var selectedObj = ui.item;
                placeName = selectedObj.value;
                if (typeof placeName == "undefined") placeName = $citiesField.val();

                if (placeName) {
                    var terms = split($citiesField.val());
                    // remove the current input
                    terms.pop();
                    // add the selected item (city only)
                    terms.push(extractFirst(placeName));
                    // add placeholder to get the comma-and-space at the end
                   // terms.push("");
                    $citiesField.val(terms.join(", "));
                }

                return false;
            },
            focus: function() {
                // prevent value inserted on focus
                return false;
            },
        });

        $citiesField.autocomplete("option", "delay", 100);
    });
</script>
<script>

    var gradeArray   = new Array();
    var sectionArray = new Array();
	<?php
		$tempClass = "";

		for($i=0; $i<count($classArray); $i++)
		{
		    echo "gradeArray.push($classArray[$i]);\r\n";
		    //echo "sectionArray.push($sectionArray[$i]);\r\n";
		    echo "sectionArray[$i] = new Array($sectionArray[$i]);\r\n";
		}
			/*$tempClass .= "'".$classArray[$i]."',";
		$tempClass = substr($tempClass,0,-1);

		echo "var gradeArray = new Array($tempClass);\n";
		$tempSection = "";
		for($i=0; $i<count($sectionArray); $i++)
			$tempSection .= "'".$sectionArray[$i]."',";
		$tempSection = substr($tempSection,0,-1);
		echo "var sectionArray = new Array($tempSection);";*/
	?>

	history.forward();
	function submitForm()
	{
		setTryingToUnload();
		document.getElementById('frmTopicActivation').submit();
	}
	function init()
	{
		document.cookie = 'SHTS=;';
		document.cookie = 'SHTSP=;';
		document.cookie = 'SHTParams=;';
		if(document.getElementById('pnlErrmsg').value!="")
			document.getElementById('pnlErrmsg').style.display="inline";

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

	function validate(noOfSubjects)
	{
		var firstName = trim(document.getElementById('txtFirstName').value);
		var lastName  = trim(document.getElementById('txtLastName').value);
		var emailID   = trim(document.getElementById('txtEmailID').value);
		//var grade     = document.getElementById('lstGrade').value;
		var contactno_res = trim(document.getElementById('txtContactRes').value);
		var contactno_cel = trim(document.getElementById('txtContactCel').value);
		var errmsg  = "";
		if(firstName==""){
			// errmsg += "Please specify the First Name.\n";
				alert("Please enter the First Name.");
				document.getElementById('txtFirstName').focus();
			 	return false;
		}
		if(lastName==""){
			// errmsg += "Please specify the Last Name.\n";
				alert("Please enter the Last Name.");
				document.getElementById('txtLastName').focus();
			 	return false;
		}
		if(!validateName(firstName) || !validateName(lastName)) {
			alert('Names can have only letters and spaces.');
			document.getElementById('txtFirstName').focus();
			return false;
		}
		if(emailID!="")
		{
			if(!validateEmail(emailID))
			{
				alert("Teacher e-mail address is not valid");
				document.getElementById('txtEmailID').focus();
			 	return false;
			}			
		}
		if(contactno_cel != '')
		{
			if(!validatePhoneNo(contactno_cel, document.getElementById('cell_no').value))
			{
				document.getElementById('txtContactCel').focus();
				return false;
			}
			else
			{
				var contact_cell_no = $.trim($('#contactisdcode').val())+'-'+($('#txtContactCel').val());
				$("#txtContactnoCel").val(contact_cell_no);
			}	
		}
		if(contactno_res != '')
		{
			if(!validateLandlineNo(contactno_res, document.getElementById('txtContactResArea').value))
			{
				document.getElementById('txtContactnoRes').focus();
				return false;
			}	
			else
			{
				var contact_res_no = $.trim($('#contactisdrescode').val())+'-'+$('#txtContactResArea').val()+'-'+($('#txtContactRes').val());
				$("#txtContactnoRes").val(contact_res_no);
			}
		}
		
		//if(contactno_res=="" && contactno_cel=="")
			//errmsg += "Please specify atlease one contact number.\n";
		var tbl = document.getElementById('tblClassMapping');
		var isSpecified = false;
		for(var j=0; j<tbl.rows.length-1; j++)
		{
			var grade = document.getElementById('lstGrade'+j).value;

			if(grade!="")
			{
				if(document.getElementById('lstSection'+j) && document.getElementById('lstSection'+j).style.display!="none")
				{
					var section = document.getElementById('lstSection'+j).value;
					if(section=="")
					{
						errmsg += " Please specify the section against class "+grade+".\n";
						isSpecified = false;
					}
					else
						isSpecified = true;

				}
				else
					isSpecified = true;
				if(document.getElementById('lstSubject'+j))
				{
					var subj = document.getElementById('lstSubject'+j).value;
					if(subj=="")
					{
						errmsg += " Please specify the subject against class "+grade+".\n";
						isSpecified = false;
					}
					else
						isSpecified = true;
				}
				else
					isSpecified = true;

			}
			if(document.getElementById('lstSection'+j) && document.getElementById('lstSection'+j).value!="" && grade=="")
				errmsg += " Please specify the class for row"+(j+1)+".\n";
			if(document.getElementById('lstSubject'+j) && document.getElementById('lstSubject'+j).value!="" && grade=="")
				errmsg += " Please specify the class for row"+(j+1)+".\n";

		}
		if(!isSpecified)
			errmsg += "Please specify atleast one class-mapping.\n";
		if(errmsg!="")
		{
			alert(errmsg);
			return false;
		}
		else{
			setTryingToUnload();
			return true;
		}
	}
	function setSection(srno)
	{
	    var obj = document.getElementById('lstSection'+srno);
        removeAllOptions(obj);
	    var cls = document.getElementById('lstGrade'+srno).value;
	    if(cls=="")
	    {
	        document.getElementById('lstSection'+srno).style.display = "none";
	        document.getElementById('lblSection'+srno).style.display = "none";
	        document.getElementById('lstSection'+srno).selectedIndex = 0;
	    }
	    else
	    {

	       for(var i=0; i<gradeArray.length && gradeArray[i]!=cls; i++);

	       if(sectionArray[i].length>0)
	       {
    	       for (var j=0; j<sectionArray[i].length; j++)
    	       {
    	               OptNew = document.createElement('option');
    	               OptNew.text = sectionArray[i][j];
    	               OptNew.value = sectionArray[i][j];
    	               obj.options.add(OptNew);

    	       }
    	       document.getElementById('lstSection'+srno).style.display = "inline";
    	       document.getElementById('lblSection'+srno).style.display = "inline";
	       }
	       else
	       {
	           document.getElementById('lstSection'+srno).style.display = "none";
	           document.getElementById('lblSection'+srno).style.display = "none";
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

	function addRow(noOfSubjects)
	{
	    var tbl = document.getElementById('tblClassMapping');
	    // grab how many rows are in the table
	    var lastRow = tbl.rows.length;

	    var iteration = lastRow - 1;

	    // creates a new row
	    if(iteration<0)
	       iteration=0;
	    var row = tbl.insertRow(iteration);

	    var cellno =0;
	    if(noOfSubjects>1)
	    {
	    	var cellLeft = row.insertCell(cellno);
	    	var textNode = document.createTextNode("Subject:");
	    	cellLeft.appendChild(textNode);
	    	cellno++;
	    	var cellGrade = row.insertCell(cellno);
	        var sel1 = document.createElement('select');
	        sel1.name = 'subj[]';
	    	sel1.id = 'lstSubject'+iteration;
	    	sel1.options[0] = new Option('Select', '');

	        sel1.options[1] = new Option("Maths",2);
	        sel1.options[2] = new Option("Science",3);
	        cellGrade.appendChild(sel1);
	        cellno++;
	    }
	    var cellLeft = row.insertCell(cellno);
	    //var textNode = document.createTextNode("Class:");
	    var textNode = document.createElement("label");
	    textNode.innerHTML = "Class:";

	    cellLeft.appendChild(textNode);
		cellno++;
	    var cellGrade = row.insertCell(cellno);
	    // create another element, this time a select box
	    var sel1 = document.createElement('select');

	    sel1.name = 'grade[]';
	    sel1.id = 'lstGrade'+iteration;
	    sel1.onchange = function(){setSection(iteration)};
	    /*var onChangeHandler = new Function(sel1.onchange);
	    if (sel1.addEventListener)
	    {
	        sel1.addEventListener('change', onChangeHandler, false );
	    }
	    else if (sel1.attachEvent)
	    {
	        sel1.attachEvent('onchange', onChangeHandler);
	    }*/
	    //sel.id
	    sel1.options[0] = new Option('Select', '');
	    for(var i=0; i<gradeArray.length; i++)
	    {
	        //alert(gradeArray[i]);
	        sel1.options[i+1] = new Option(gradeArray[i], gradeArray[i]);
	    }

	    cellGrade.appendChild(sel1);
	    cellno++;

	    /*//Check if section exists
	    if(sectionArray.length>0)
	    {*/
	    var cellLeft = row.insertCell(cellno);
	    var textNode = document.createElement("label");
	    textNode.id = "lblSection" + iteration;
	    textNode.innerHTML = "Section:";
	    textNode.style.display = "none";

	    cellLeft.appendChild(textNode);
	    cellno++;

	    var cellSection = row.insertCell(cellno);
	    // create another element, this time a select box
	    var sel = document.createElement('select');

	    sel.name = 'section[]';
	    sel.id = 'lstSection'+iteration;
	    sel.options[0] = new Option('Select', '');
	    sel.style.display = "none";
	    /* for(var i=0; i<sectionArray.length; i++)
	    sel.options[i+1] = new Option(sectionArray[i], sectionArray[i]);*/
	    cellSection.appendChild(sel);
	    row.insertCell(++cellno);
	    //}

	}
    function viewDetails()
    {
    	document.getElementById('frmAddTeacherDetails').action="teacherDetails.php";
		setTryingToUnload();
     	document.getElementById('frmAddTeacherDetails').submit();
    }
    function confirmClassRemoval(userID, subject, cls, sec)
    {
//        document.getElementById('delSubject').value = document.getElementById('lstSubject'+srno).value;
//        document.getElementById('delClass').value   = document.getElementById('lstGrade'+srno).value;
//        document.getElementById('delSection').value = document.getElementById('lstSection'+srno).value;
        document.getElementById('delSubject').value = subject;
        document.getElementById('delClass').value   = cls;
        document.getElementById('delSection').value = sec;
        document.getElementById('delUserID').value = userID;
        document.getElementById('action').value = "Delete";
		setTryingToUnload();
        document.getElementById('frmAddTeacherDetails').submit();
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

	<div id="container">
		<div id="innerContainer">
			
			<div id="containerHead">
				<div id="triangle"> </div>
				<span>ADD/EDIT TEACHER DETAILS</span>
			</div>
			<div id="containerBody">
			<center><div style="display: hidden; color:#FF0000; font-weight:bold; width:50%;margin-bottom: 3%" id="pnlErrmsg"><?=$errmsg?></div></center>
			<form id="frmAddTeacherDetails" method="POST">
			<table id="mainContent">
			<tr>
				<td>FIRST NAME <span class="after" style="color:red;">*</span></td>
				<td>LAST NAME <span class="after" style="color:red;">*</span></td>
			</tr>
			<tr>
				<td>
				<input type="text" name="firstName" id="txtFirstName" size="30" value="<?=$teacher->firstName?>" maxlength="20">
				</td>
				<td>
				<input type="text" name="lastName" id="txtLastName" size="30" value="<?=$teacher->lastName?>" maxlength="20">
				</td>
			</tr>
			<tr class="space"> 
				<td>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<!-- <td>CONTACT NO. (Primary) </td>
				<td>CONTACT NO. (Secondary) </td> -->
				<td>MOBILE </td>
				<td>Landline </td>
			</tr>
			<tr>
				<td>
				<?php				
					if($teacher->contactno_cel != '-,-' || $teacher->contactno_cel!= ',' || $teacher->contactno_cel != '--')
			{				
				$pmnodetails = explode('-', $teacher->contactno_cel);				
			}
					?>
				<script>
				$(window).load(function(){
			document.getElementById("cell_no").value = document.getElementById("cell_no").value;				
		});
			function getcontactisdcode()
				{
					document.getElementById("contactisdcode").value = document.getElementById("cell_no").value;
					document.getElementById('contact_cel').focus();
				}
																
															
						</script>
						
				<input id="cell_no" size="3" maxlength="3" style="color: white;border-radius: 0px;width:26px; height:25px; border-color: grey;margin-top: 5px;" name="cell_no" class="Box" type="text" value="<?= (isset($pmnodetails[0]) && $pmnodetails[0]!= "" && $pmnodetails[1]!='')?$pmnodetails[0]:"+91"?>" onBlur="getcontactisdcode()"></input>

				<input type="hidden" name="contactisdcode" id="contactisdcode" value="<?= (isset($pmnodetails[0]) && $pmnodetails[0]!="" && $pmnodetails[1]!= "")?$pmnodetails[0] : "+91"; ?>"></input>
				<input type="text" name="contact_cel" id="txtContactCel" size="23"  value="<?= (isset($pmnodetails[1]) && $pmnodetails[0]!= "" )?$pmnodetails[1] : "";?>"  onkeypress="return contanctKeypress(event)" maxlength="10">	</input>

				<input type="hidden" name="contactno_cel" id="txtContactnoCel" size="23"  value="<?= (isset($teacher->contactno_cel))?$teacher->contactno_cel : "";?>" >	</input>
			</td>		
							
				</td>
				<td>
					<?php				
					if($teacher->contactno_res != '--')
			{				
				$rsnodetails = explode('-', $teacher->contactno_res);				
			}
					?>
				<script>
				$(window).load(function(){
			document.getElementById("res_no").value = document.getElementById("res_no").value;				
		});
			function getcontactisdrescode()
				{
					document.getElementById("contactisdrescode").value = document.getElementById("res_no").value;
					document.getElementById('contact_res').focus();
				}
				</script>
				<input id="res_no" size="3" maxlength="3" style="color: white;border-radius: 0px;width:26px; height:25px; border-color: grey;margin-top: 5px;" name="res_no" class="Box" type="text" value="<?= (isset($rsnodetails[0]) && $rsnodetails[0]!= "" && $rsnodetails[1]!='' && $rsnodetails[2]!='' )?$rsnodetails[0]:"+91"?>" onBlur="getcontactisdrescode()"></input>

				<input type="hidden" name="contactisdrescode" id="contactisdrescode" value="<?= (isset($rsnodetails[0]) && $rsnodetails[0]!="" && $rsnodetails[1]!= "" && $rsnodetails[2]!='' )?$rsnodetails[0] : "+91"; ?>"></input>

				<input type="text" name="contact_res_area" id="txtContactResArea" size="6"  value="<?= (isset($rsnodetails[1]) && $rsnodetails[0]!= "" && $rsnodetails[2]!='' )?$rsnodetails[1] : "";?>"  onkeypress="return contanctKeypress(event)" maxlength="5" placeholder="Area Code">	</input>

				<input type="text" name="contact_res" id="txtContactRes" size="11"  value="<?= (isset($rsnodetails[2]) && $rsnodetails[0]!= "" && $rsnodetails[1]!='' )?$rsnodetails[2] : "";?>"  onkeypress="return contanctKeypress(event)" maxlength="10">	</input>

				<input type="hidden" name="contactno_res" id="txtContactnoRes" value="<?=$teacher->contactno_res?>">


				</td>
			</tr>
			
			<tr class="space"> 
				<td>
				</td>
				<td>
				</td>
			</tr>
			
			<tr>
				<td>EMAIL ID</td>
				<td></td>
			</tr>
			<tr>
				<td>
				<input type="text" name="emailID" id="txtEmailID" size="30" value="<?=$teacher->emailID?>"  maxlength="50">
				</td>
				<td></td>
			</tr>
			
			<tr class="space"> 
				<td>
				</td>
				<td>
				</td>
			</tr>
			
			<tr>
				<td>CITY</td>
				<td>COUNTRY</td>
			</tr>
			<tr>
				<td>
				<input type="text" name="city" id="txtCity" size="30" value="<?=$teacher->city?>"  maxlength="50">
				</td>
				<td>
				<input type="text" name="country" id="txtCountry" size="30" value="<?=$teacher->country?>"  maxlength="50">
				</td>
			</tr>
			
			<tr class="space"> 
				<td>
				</td>
				<td>
				</td>
			</tr>
			
			
			<tr>
				<td>
				<fieldset style="width:100%; border-color:#FFFFFF;">
				<legend>CLASSES MAPPED <span class="after" style="color:red;">*</span></legend>
				<table id="tblClassMapping" border="0">
			<?php
				if($teacher->userID!="")
				{
					$query = "SELECT class, section, subjectno FROM adepts_userDetails a, adepts_teacherClassMapping b WHERE a.userID=b.userID AND a.userID=$teacher->userID ORDER BY subjectno, class, section";
					$result = mysql_query($query);
					$srno = 0;

					while ($line=mysql_fetch_array($result))	{
						$k = "-1";
			?>
					<tr>
						<?php  if($noOfSubjects>1) { ?>
						<td><label for="lstSubject">Subject:</label></td>
						<td>
							<select name="subj[]" id="lstSubject<?=$srno?>" disabled>
								<option value="">Select</option>
								<option value="2" <?php if($line['subjectno']=="2") {echo " selected";}?>>Maths</option>
								<option value="3" <?php if($line['subjectno']=="3") {echo " selected";}?>>Science</option>
							</select>
						</td>
						<?php } else echo "<input type='hidden' name='subj[]' id='lstSubject$srno' value='".SUBJECTNO."'>"; ?>
						<td><label for="lstGrade">Class:</label></td>
						<td>
							<select name="grade[]" id="lstGrade<?=$srno?>" disabled>
								<option value="">Select</option>
							<?php for($i=0; $i<count($classArray); $i++)	{ ?>
								<option value="<?=$classArray[$i]?>" <?php if($line['class']==$classArray[$i]) {echo " selected";$k=$i;}?>><?=$classArray[$i]?></option>
							<?php	}	?>
							</select>
						</td>

						<td ><label id="lblSection<?=$srno?>" <?php if($line['section']=='') echo " style='display:none;'"?>>Section:</label></td>
						<td style="">
							<select name="section[]" id="lstSection<?=$srno?>" disabled <?php if($line['section']=='') echo " style='display:none;'"?>>
								<!--<option value="">Select</option>-->
							<?php
							     //$clsSectionArray = array();
							     if($k!="-1")
							     {

							         $clsSectionArray = explode(",",$sectionArray[$k]);
									 $tempSectionStr = "'".$line['section']."'";	
									 if(!in_array($tempSectionStr,$clsSectionArray))	//This is needed if a section mapped to a teacher is not present against any student ids
									 	array_push($clsSectionArray,$tempSectionStr);
							     	for($i=0; $i<count($clsSectionArray); $i++)	{ $sec = str_replace("'","",$clsSectionArray[$i]);?>
								    	<option value="<?=$sec?>" <?php if($line['section']==$sec) echo "selected";?>><?=$sec?></option>
							<?php	}	}?>
							</select>
						</td>
						<td><a href="javascript:confirmClassRemoval('<?=$teacher->userID?>',<?php echo $line['subjectno']; ?>,<?php echo $line['class']; ?>, '<?php echo $line['section']; ?>')" style="text-decoration:underline;" title="Click here to remove this class mapping">Delete</td>
					</tr>
			<?php	$srno++;}
				}
			?>

					<tr>
						<td colspan="5"><input type="button" style="margin-left: 15%;margin-top: 5%;width: :30%" value="Add More" onClick="addRow(<?=$noOfSubjects?>);" class="addMore"></td>
					</tr>

				</table>
				<script>addRow(<?=$noOfSubjects?>);</script>
			</fieldset>
				</td>
			<td>
			<div align="center" style="padding-left:12%" class="legend">Note: <span class="after" style="color:red;">*</span> indicates mandatory field.</div>
			</td>
			</tr>
	
			</table>
			<div id="buttonSection">
			<input type="submit" class="buttons" name="save" id="btnSave" value="SAVE" onclick="return validate(<?=$noOfSubjects?>);" class="button">
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" class="buttons" name="Cancel" id="btnCancel" value="CANCEL" onclick="viewDetails()" class="button">
		
			</div>
			
<input type="hidden" name="userID" value="<?=$teacher->userID?>">
<input type="hidden" name="delSubject" id="delSubject">
<input type="hidden" name="delUserID" id="delUserID">
<input type="hidden" name="delClass" id="delClass">
<input type="hidden" name="delSection" id="delSection">
<input type="hidden" name="action" id="action" value="">
<p>
	<?php //@include("disclaimer.php"); ?>
</p>
			</form>
			</div>
			
		
		</div>
		
		
	</div>
 <script>
	 $("#cell_no").intlTelInput();
	 $("#res_no").intlTelInput();			 
 </script>
<?php
function mailDetails($teacher)
{
	$subject = "Mindspark - Login Details";
	$body = "Dear Ms ".$teacher->firstName." ".$teacher->lastName." <br/><br/>";
	$body .= "Welcome to the Mindspark program!<br/><br/>";
	$body .= "A login has been created for you to access this program at your own convenience.<br/>";
	$body .= "The program may be accessed at www.mindspark.in<br/><br/>";
	$body .= "Here are the login details you will need to access the program<br/><br/> ";
	$body .= "Username: ".$teacher->username."<br/>";
	$body .= "Password: ".$teacher->password."<br/><br/>";
	$body .= "Regards,<br/>Mindspark Team";
	$headers = "From:<notification@ei-india.com>\r\n";

	mail($teacher->emailID,$subject,$body, $headers);
}

function getNoOfSubjects($schoolCode)
{
	$subjectArray = array();
	$query  = "SELECT distinct subjects FROM adepts_userDetails WHERE category='STUDENT' AND schoolCode=$schoolCode";
	$result = mysql_query($query) or die(mysql_error());
	while ($line = mysql_fetch_array($result))
	{
		$tmpArray = explode(",",$line[0]);
		for($i=0; $i<count($tmpArray); $i++)
		array_push($subjectArray, $tmpArray[$i]);
	}
	$subjectArray = array_values(array_unique($subjectArray));	//array_values is used to reset the keys.
	return count($subjectArray);
}
// to add member on teacher forum
function addtoforum_member($password,$schoolCode)
{
	/*if(check_allow_forum($password,$schoolCode))
	{*/
		
		$query = "insert into teacherForum.forum_member (username,password,confirmed,joinTime,lastActionTime,preferences) values ('$password',password('$password'),1,UNIX_TIMESTAMP(now()),UNIX_TIMESTAMP(now()),'613A333A7B733A31363A22656D61696C2E70726976617465416464223B623A313B733A31303A22656D61696C2E706F7374223B623A313B733A31313A22737461724F6E5265706C79223B623A303B7D')";
		mysql_query($query);
                $member_id = mysql_insert_id();
                if($member_id>0) {            
                
                    $q = "SELECT schoolName FROM educatio_educat.schools WHERE schoolno=$schoolCode";
        	    $r = mysql_query($q);
        	    $l = mysql_fetch_array($r);
        	    $schoolName = $l[0];
                
                    $sql = "INSERT INTO teacherForum.forum_profile_data SET memberid=$member_id, fieldId=1, data='$schoolName' ";
                    mysql_query($sql);
                }
		
	//}
		
	return 1;
}

?>

<?php include("footer.php") ?>


