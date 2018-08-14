<?php
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);

	include("header.php");
	include("classes/testTeacherIDs.php");
	
	$flagForOffline = false;
	if($_SESSION['isOffline'] === true && ($_SESSION['offlineStatus']==1 || $_SESSION['offlineStatus']==2))
	{
		$flagForOffline = true;
	}
	if($flagForOffline)
		include("logDeleteQuery.php");
	
	$userID     = $_SESSION['userID'];
	$schoolCode = isset($_SESSION['schoolCode'])?$_SESSION['schoolCode']:"";
	$user   = new User($userID);
	$todaysDate = date("d");

	if(strcasecmp($user->category,"School Admin")==0)
	{
		$query  = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
			       FROM     adepts_userDetails
			       WHERE    schoolCode=$schoolCode AND category='STUDENT' AND subcategory IN ('School','Individual') AND enabled=1 AND endDate>=curdate() AND 
				   subjects like '%".SUBJECTNO."%' GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
	}
	elseif (strcasecmp($user->category,"Teacher")==0)
	{
		$query = "SELECT   class, group_concat(distinct section ORDER BY section)
				  FROM     adepts_teacherClassMapping
				  WHERE    userID = $userID AND subjectno=".SUBJECTNO."
				  GROUP BY class ORDER BY class, section";
	}
	elseif (strcasecmp($user->category,"Home Center Admin")==0)
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
	$classArray = $sectionArray =  array();
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


	$sel_class = isset($_POST['class'])?$_POST['class']:"All";
	$sel_section = isset($_POST['section'])?$_POST['section']:"";
	if(isset($_POST['removeComment']) && $_POST['removeComment']!="")
	{
		$removeComment = $_POST['removeComment'];
		$query = "DELETE FROM adepts_noticeBoardComments WHERE srno=".$_POST['removeComment'];
		if($flagForOffline)
			logDeleteQuery($query,'adepts_noticeBoardComments',$_SESSION["schoolCode"],array('srno'=>$removeComment), "srno = $removeComment",'');
		mysql_query($query);
	}
	if(isset($_POST['btnSubmit']))
	{
	    $noOfDays = $_POST['noOfDays']==""?$noOfDays=2:$_POST['noOfDays'];
		if($sel_class=='All')
			$sel_section = 'All';
   		$query = "INSERT INTO adepts_noticeBoardComments (subjectno, schoolCode, class, section, comment, addedBy, noOfDays, date)
   		            VALUES (".SUBJECTNO.",$schoolCode,'$sel_class','$sel_section','$comment','".$user->childName."',$noOfDays,curdate())";
   		mysql_query($query);
	}
	$commentArray = $commentClassArray = $commentSectionArray = array();		
		if($sel_class == "All")
		{
			$select_class = implode(',', $classArray);
		}	
		else
			$select_class = $sel_class;
		if($select_class != '')
		{
			$query = "SELECT srno, comment, class, section FROM adepts_noticeBoardComments WHERE subjectno=".SUBJECTNO." AND schoolCode=$schoolCode AND class IN($select_class)";
		
		if($sel_section!="" && $sel_section!='All')
		{				
			$query .= " AND (section='$sel_section' OR section='All')";				
		}

		$query .= " AND datediff(curdate(),date)<noOfDays";
		$query .= " UNION SELECT srno, comment, class, section FROM adepts_noticeBoardComments WHERE subjectno=".SUBJECTNO." AND schoolCode=$schoolCode AND class='All'";
		$query .= " AND datediff(curdate(),date)<noOfDays";
		$query .= " ORDER BY srno";		
		$result = mysql_query($query) or die(mysql_error());		
		while($line   = mysql_fetch_array($result))
		{
		    $commentArray[$line['srno']] = $line['comment'];
		    $commentClassArray[$line['srno']] = $line['class'];
		    $commentSectionArray[$line['srno']] = $line['section'];
		}	

		}				
		
?>

<title>Student Notice Board</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/studentNoticeBoard.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
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
		$("#students").css("font-size","1.4em");
		$("#students").css("margin-left","40px");
		$(".arrow-right-yellow").css("margin-left","10px");
		$(".rectangle-right-yellow").css("display","block");
		$(".arrow-right-yellow").css("margin-top","3px");
		$(".rectangle-right-yellow").css("margin-top","3px");
		setSection('<?=$sel_section?>','<?=$user->category?>');
	}
	
	function textFocus(){
		if (document.getElementById("txtComment").value == 'Maximum 150 Characters') 
		{
			{document.getElementById("txtComment").value=''}
		}
	}
	
	function textBlur(){
		if (document.getElementById("txtComment").value == '') 
		{
			{document.getElementById("txtComment").value='Maximum 150 Characters'}
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
function setSection(sec, category)
{
	var cls = document.getElementById('lstClass').value;
	if(cls!="")
	{
		if(document.getElementById('lstSection'))
		{
			var obj = document.getElementById('lstSection');
			$("#lstSection").html("");
			for(var i=0; i<gradeArray.length && gradeArray[i]!=cls; i++);
			if(cls!="All" && sectionArray[i].length>0)
			{
			    if(sectionArray[i].length != 1)
			    {
			        OptNew = document.createElement('option');
					OptNew.text = "All";
					OptNew.value = "All";
					if(sec=="All")
					{
						OptNew.selected = true;						
					}						
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
				document.getElementById('lstSection').style.display = "";
				document.getElementById('lblSection').style.display = "";
				
				if(sec=="")
				{
				    if(document.getElementById('txtComment'))
				    {
    					document.getElementById('txtComment').style.display  = "none";
    					document.getElementById('btnSubmit').style.display   = "none";
    					document.getElementById('lblComment').style.display  = "none";
    					document.getElementById('lblNoOfDays').style.display = "none";
    					document.getElementById('txtNoOfDays').style.display = "none";							
				    }
				}
			}
			else
			{
				document.getElementById('lstSection').style.display = "none";
				document.getElementById('lblSection').style.display = "none";
			}
		}
	}
	else{
		var Opt_New = document.createElement('option');
		Opt_New.text = "All";
		Opt_New.value = "All";
		Opt_New.selected = true;
		var el_Sel = document.getElementById('lstSection');
		el_Sel.options.add(Opt_New);
		$("#lstSection,#lblSection").hide();		
	}
}
function submitForm()
{
	setTryingToUnload();
	document.getElementById('frmNBComment').submit();
}
function removeAllOptions(selectbox)
{
	var i;
	for(i=selectbox.options.length-1;i>0;i--)
	{
		selectbox.remove(i);
	}
}
function removeComment(srno)
{
	document.getElementById('removeComment').value=srno;
	submitForm();
}

function validate()
{
	if(document.getElementById('lstSection') && document.getElementById('lstSection').style.display!="none" && document.getElementById('lstSection').value=="")
	{
		alert("Please select the section!");
		return false;
	}
	if(document.getElementById('txtComment').value=="")
	{
		alert("Please specify the comment!");
		return false;
	}
	else if(document.getElementById('txtComment').value.length>150)
	{
		alert("Comment can be of max. 150 characters!");
		return false;
	}
	else if(isNaN(document.getElementById('txtNoOfDays').value))
	{
	    alert("Invalid value for no. of days!");
	    document.getElementById('txtNoOfDays').focus();
		return false;
	}
	
	if(document.getElementById('txtComment').value == "Maximum 150 Characters")
	{
		alert("Please write a comment for it to be displayed on the Notice Board");
		return false;
	}
	setTryingToUnload();

}

/*window.onload= function()
{
	alert("hello");
	setSection('<?=$sel_section?>',0,'<?=$user->category?>');
}*/


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
	<form id="frmNBComment" method="post" action="">
		<div id="innerContainer">
			
			<div id="containerHead">
				<div id="triangle"> </div>
				<span>Notice Board</span>
			</div>
			
				<table style="margin-top:1%;margin-bottom: 0.3%;">
				<tr>
				<td >
				<span style="font-size: 1.4em;">Class</span>
				</td>
				
				<td>
				<select id="lstClass" name="class" onChange="setSection('','<?=$user->category?>');">
					<?php
						if(count($classArray) != 1)
							echo '<option value="All">All</option>';
						for ($i=0;$i<count($classArray);$i++)
						{
							echo "<option value='".$classArray[$i]."'";
							if ($sel_class==$classArray[$i])
							{
								echo " selected";
							}
							echo ">".$classArray[$i]."</option>";
						}
					?>
				</select>
				</td>
				<td style="width:10%;"></td>
				
				<?php if($hasSections) { ?>
				<td>
				<div id="sectionOption" >
				<span id="lblSection" style="font-size: 1.4em;">Section</span>
				</div>
				</td>
				
				<td>
				<div id="sectionOptions">
				<select id="lstSection" name="section">
				</select>
				</div>
				</td>
				<?php } ?>
				<td style="width:10%;"></td>
				<td>
				<input type='button' style='width:50px;' name='go' id='go' value='Go' onclick="submitForm();" />
				</td>
				</tr>
				</table>
				
				
				
		</div>
		<div id="line"> </div>
		
			<?php if($sel_class!="") { 
		
			?>
		<div id="left" > 
		<div id="lblComment" style="margin-top: 1.2%;margin-bottom: 1.2%;"> TYPE COMMENT HERE		</div>
		
		<textarea id="txtComment" name="comment" rows="10" cols="40" onFocus="textFocus()" onBlur="textBlur()">Maximum 150 Characters</textarea>
		
		<div id="lblNoOfDays" style="margin-top: 1.2%;margin-bottom: 1.2%;"> No. OF DAYS COMMENT APPEARS 		</div>
		<input type="text"	id="txtNoOfDays" name="noOfDays" value="2" size="1" maxlength="2">
		<br> </br>
		
		<input type="submit" value="Submit" name="btnSubmit" id="btnSubmit" class="buttons" onClick="return validate();" <?php if(($user->category=="School Admin" && $user->subcategory=="All" && $schoolCode!=3216130) || in_array(strtolower($user->username),$testIDArray)) echo " disabled"?> />
		</div>
		<?php } ?>
		<input type="hidden" name="removeComment" id="removeComment" value="">
		
		<div id="right" >
		<div class="blackboard_screen"> 
		<div style="padding-top	:10px;text-align: center;">
		Notice Board
		</div>
		<div class='blackboard_message'>
				<ul>
			<?php if(count($commentArray)>0) {
			         foreach ($commentArray as $commentID=>$comment) {
			?>
			       <li>
			<?php			
						$commentText = '';
						$removeFlag = 0;
						if(!empty($commentClassArray[$commentID]))
						{
							if($commentClassArray[$commentID] == 'All')						
								$commentText .= " ".$commentClassArray[$commentID]." classes";										
							else	
								$commentText .= " class ".$commentClassArray[$commentID];
						}
						
						if(!empty($commentSectionArray[$commentID]))
						{
							if($commentSectionArray[$commentID] == 'All')
							$commentText .= " ".$commentSectionArray[$commentID]." sections";
						else
							$commentText .= " section ".$commentSectionArray[$commentID];
						}
						

						  echo $comment." - Active for $commentText.";
						  
                         if(!($user->category=="School Admin" && $user->subcategory=="All" && $schoolCode!=3216130)  && $commentClassArray[$commentID]==$sel_class) {                         	
                         	if($sel_section != "All" && $sel_section != '')
                         	{
                         		if($commentSectionArray[$commentID]==$sel_section)
                         		{
                         			$removeFlag = 1;
                         		}
                         	}
                         	else
                         		$removeFlag = 1;
                         }
                         if($removeFlag){
            ?>
					     <a href="javascript:removeComment(<?=$commentID?>)" title="Click here to remove this comment" id="cross">&#x2716;</a>
				   <?php } else echo "&nbsp;&nbsp;&nbsp;&nbsp;"?>
				   
				   </li>
				<!--<br/>-->
				<!--<span class="nb_note">(This comment will appear for the next 2 days)</span>-->
			<?php } } ?>
			</ul>
			</div>
		</div>
		</div>
		</form>
	</div>

<?php include("footer.php") ?>