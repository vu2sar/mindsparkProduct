<?php
set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
include("header.php");
include("../userInterface/classes/eipaging.cls.php");
include_once("../userInterface/functions/orig2htm.php");
include("../userInterface/classes/clsQuestion.php");
include("../userInterface/classes/clsResearchQuestion.php");
include("../userInterface/constants.php");
include("../constants.php");
include("../slave_connectivity.php");
if(!isset($_SESSION['userID']) || $_SESSION['userID']=="")
{
  	echo "You are not authorised to access this page!";
    	exit;
}

$clspaging = new clspaging();
$clspaging->setgetvars();
$clspaging->setpostvars();

$basedir   = "http://www.educationalinitiatives.com/mindspark/explanation_images/";

$userID    = $_SESSION['userID'];
$category   = $_SESSION['admin'];
$schoolCode = $_SESSION['schoolCode'];

	if(strcasecmp($category,"School Admin")==0)
	{
		$query  = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
		           FROM     adepts_userDetails
		           WHERE    schoolCode=$schoolCode AND category='STUDENT' AND subcategory='School' AND enabled=1 AND subjects LIKE '%".SUBJECTNO."%' AND endDate>=curdate()
		           GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
	}
	elseif (strcasecmp($category,"Teacher")==0)
	{
		$query = "SELECT   class, group_concat(distinct section ORDER BY section)
				  FROM     adepts_teacherClassMapping
				  WHERE    userID = $userID AND subjectno='".SUBJECTNO."'
				  GROUP BY class ORDER BY class, section";
	}
	else
	{
		echo "You are not authorised to access this page!";
        	exit;
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
		//array_push($sectionArray, $sectionStr);
                $sectionArray[$line[0]] = $sectionStr;
        }

	$cls     = isset($_REQUEST['cls'])?$_REQUEST['cls']:"";
	$section = isset($_REQUEST['section'])?$_REQUEST['section']:"";
        $childName = isset($_REQUEST['childName'])?$_REQUEST['childName']:"";
        
?>

<title>Student Comments</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/myClasses.css?ver=1" rel="stylesheet" type="text/css">
<link href="css/Comments.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script language="javascript" type="text/javascript" src="libs/suggest1.js"></script>
<script language="javascript" type="text/javascript" src="libs/suggest2.js"></script>
<script>
    var langType = '<?=$language;?>';
    function load()
    {

        var fixedSideBarHeight = window.innerHeight;
        var sideBarHeight = window.innerHeight-95;
        var containerHeight = window.innerHeight-115;
        $("#fixedSideBar").css("height",fixedSideBarHeight+"px");
        $("#sideBar").css("height",sideBarHeight+"px");


    }
    var gradeArray   = new Array();
    var sectionArray = new Array();
	<?php
		for($i=0; $i<count($classArray); $i++)
		{
		    echo "gradeArray.push($classArray[$i]);\r\n";
		    echo "sectionArray[$i] = new Array(".$sectionArray[$classArray[$i]].");\r\n";

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
				$(".noSection").css("visibility","visible");
		        document.getElementById('lstSection').style.display = "inline";
		        document.getElementById('lstSection').selectedIndex = 0;
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
	    	            if(sec==sectionArray[i][j])
	    	            	OptNew.selected = true;
	    	            obj.options.add(OptNew);
	    	        }
					$(".noSection").css("visibility","visible");
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
        function showHideCommentDetails(srno)
        {            
            var elem = document.getElementById("pnlMoreComments"+srno);            
            if(elem.getAttribute('data-state') === 'closed')
            {
                  elem.setAttribute('data-state','open');
                  document.getElementById("pnlMoreQuesData"+srno).setAttribute('data-state','open');
                  document.getElementById("lnkComment"+srno).innerHTML = "(...less)";
            }
            else if(elem.getAttribute('data-state') === 'open')
            {
                  elem.setAttribute('data-state','closed');
                  document.getElementById("pnlMoreQuesData"+srno).setAttribute('data-state','closed');
                  document.getElementById("lnkComment"+srno).innerHTML = "(...more)";
            }
        }
        function getStudentList(schoolCode)
        {
            var cls = document.getElementById("lstClass").value;
            var section = "";
            try {          
                if(document.getElementById("lstSection"))
                      section = document.getElementById("lstSection").value;
            } catch(err) { section ='';   }         
                      
            $.post("ajaxRequest.php","mode=getUserList&schoolCode="+schoolCode+"&class="+cls+"&section="+section,function(data) {
			var userData = JSON.parse(data);
                        var userNameArray = new Array();
                        for (var i = 0; i < userData.length; i++) {
                            userNameArray[i] = userData[i].studentname;                            
                        }                        
                        var obj1 = new actb(document.getElementById('childName'),userNameArray);
                        
		});    
        }
        function navigatepage(varprefix, cp)
        {            
        	document.getElementById(varprefix+'_currentpage').value = cp;        	
                document.getElementById('frmStudentComments').submit();
        }
        function submitTeacherCommentForm(type, qcode, qno, comment_srno, childClass)
        {
            document.getElementById("type").value = type;
            document.getElementById("qcode").value = qcode;
            document.getElementById("qno").value = qno;
            document.getElementById("comment_srno").value = comment_srno;
            document.getElementById("childClass").value = childClass;
            document.getElementById("teacherComment").submit();
        }
</script>
<style>
    /* semantic data states! */
    .moreComments[data-state=closed] {
        display: none;
    }
    .moreComments[data-state=open] {
        display: inherit;
    }
    .spnMoreLess {
        color: blue;
        text-decoration: none;  
    }
</style>
</head>
<body class="translation" onLoad="load();setSection('<?=$section?>');getStudentList(<?=$schoolCode?>);" onResize="load()">
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
    <table id="childDetails" style="float:none;">
        <td width="33%" id="sectionRemediation" class="activatedTopic">
            <a href="Comments.php" style="text-decoration:none;">
                <div id="actTopicCircle1" class="smallCircle" style="cursor:pointer;">
                </div>
                <div id="1" style="cursor:pointer;" class="pointer">
                    Comments
                </div>
            </a>
        </td>
        <td width="33%" id="studentRemediation" class="activateTopicAll">
            <a href="teacherCommentReport.php" style="text-decoration:none;">
                <div id="actTopicCircle2" class="smallCircle" style="cursor:pointer;">
                </div>
                <div id="2" style="cursor:pointer;" class="pointer">
                    Error reporting
                </div>
            </a>
        </td>
        <td width="34%" id="studentComments" class="activateTopicAll">
            <a href="studentComments.php" style="text-decoration:none;">
                <div id="actTopicCircle3" class="smallCircle red" style="cursor:pointer;">
                </div>
                <div id="2" style="cursor:pointer;" class="pointer textRed">
                    Student Comment Summary
                </div>
            </a>
        </td>
    </table>
    <div id="innerContainer">

        <div id="containerHead">
            <div id="triangle">
            </div>
            <span> Summary of comments sent by students of your class </span>
            <form method="post" id="frmStudentComments">
            <input type="hidden" name="clspaging__currentpage" id="clspaging__currentpage">
            <table id="tblForm" width="70%">
				<td width="5%"><label for="lstClass">Class:</label></td>
		        <td width="10%">
		            <select name="cls" id="lstClass"  onchange="setSection(''); getStudentList(<?=$schoolCode?>);" >
					<option value="">All</option>
					<?php
						for ($i=0;$i<count($classArray);$i++)
						{
							echo "<option value='".$classArray[$i]."'";
							if ($cls==$classArray[$i])
							{
								echo " selected";
							}
							echo ">".$classArray[$i]."</option>";
						}
					?>
					</select>
		        </td>
				<?php if($hasSections) { ?>
				<td width="6%" class="noSection"><label id="lblSection" for="lstSection" style="margin-left:10px;">Section:</label></td>
		        <td width="10%"  class="noSection">
		            <select name="section" id="lstSection">
					<option value="">All</option>
				</select>
		        </td>                        
				<?php } ?>
                        <td>
                            <label for="childName">Student's name:</label> <input type="text" name="childName" id="childName" value="<?=$childName?>" autocomplete="off" size="30">
                        </td>
                        <td><input type="submit" value="Search"/></td>
            </table>            
        </div>        
    </div>
    <div id="line">
    </div>
    <div id="containerBody">        
        <p>
        <div name="detail" id="detail">
            <br/>
            <?php            
            $clspaging->numofrecs = getStudentCommentCount($schoolCode, $classArray, $sectionArray, $cls, $section, $childName);         
            if($clspaging->numofrecs > 0){                
                $clspaging->getcurrpagevardb();            
                $srno = ($clspaging->currentpage - 1) * $clspaging->numofrecsperpage + 1;                
                $arrStudentCommentDetails = getStudentCommentDetails($clspaging->limit,$schoolCode, $classArray, $sectionArray, $cls, $section,$childName);
                if($clspaging->numofpages > 1){
                    ?>
                    <div align="center">
                                <?php
                                $clspaging->writeHTMLpagesrange($_SERVER['PHP_SELF']."?from=links",TRUE,"https://www.mindspark.in/mindspark/");
                                ?>
                            </div>
            <?php }  ?>
            <table border="0" cellpadding="5" cellspacing="0" width="90%" class="tblContent" align="center">
                <tr>
                    <td align="center" width="5%" class="header"><b> S. No.</b></td>
                    <td align="center" class="header"><b>Student Details</b></td>
                    <td align="center" class="header" style="max-width:400px;"><b>Comment Details</b></td>
                </tr>
                <?php                              
                foreach($arrStudentCommentDetails as $key=>$arrDetails) 
                {    

                    echo "<tr>";
                    echo "<td align=\"center\">".$srno++."</td>";
                    echo "<td valign='top'>".$arrDetails['name']." || ".$arrDetails['class'].$arrDetails['section'];                    

                    echo "<div id='pnlMoreQuesData".$arrDetails['comment_srno']."' class='moreComments' data-state='closed'>";
                    if ($arrDetails['notRelatedToQuestion']==0) 
                    {                                 
            			if($arrDetails["type"]=="normal" || $arrDetails["type"]=="challenge" || $arrDetails["type"]=="prepostTestQues" || $type=="question" || $type=="comprehensive" || strpos($arrDetails["type"], 'wildcard') !== false || $arrDetails['type']=="bonusCQ" || $arrDetails['type']=='practiseModule')
                        {
            				echo "<br><br><a target='_blank' href='javascript:void(0);' style='color:blue' onclick='submitTeacherCommentForm(\"".$arrDetails["type"]."\",".$arrDetails["qcode"].",".$arrDetails['qno'].",".$arrDetails['comment_srno'].",".$arrDetails['class'].");tryingToUnloadPage=false;'>View Question</a>";
                                    if($arrDetails['correctAnswer']!="")
                                            echo "<div><br><strong>Correct Answer: </strong>".$arrDetails['correctAnswer']."</div>";
                                    if($arrDetails['userResponse']!="")
                                            echo "<div><strong>Student's Answer: </strong>".$arrDetails['userResponse']."</div>";                   
                        }
                    }
                    else 
                    {                        
                          echo "<div><br><strong>Not related to question</strong></div>";
                    }
                    if($arrDetails['os']!="") 
                    {                
                                echo "<div><br><strong>Student's system information:</strong></div>";
                                echo "<div>".$arrDetails['os']."</div>";
                    }
                    if($arrDetails['browser']!="")
                                echo "<div>".$arrDetails['browser']."</div>";                                                
                    echo "</div>";
                    echo "</td>";                    
                    echo "<td align=\"justify\" style=\"max-width:400px;\">";                    
                    $commentTrail = $arrDetails['commentTrail'];
                    if(count($commentTrail)==0)
                            echo '<div class="comment">'.$arrDetails['comment'].'</div>';
                    else
                    {
                            echo '<div class="comment">'.$commentTrail[1];
                            //if(count($commentTrail)>1)
                                echo " <a href='javascript:showHideCommentDetails(".$arrDetails['comment_srno'].")' id='lnkComment".$arrDetails['comment_srno']."' class='spnMoreLess'>(...more)</a>";
                            echo '</div>';
			             echo "<div id='pnlMoreComments".$arrDetails['comment_srno']."' class='moreComments' data-state='closed'>";				
                            if(count($commentTrail)>1)
                            {                                                        
                                for($j=2; $j<=count($commentTrail); $j++) {                                                   
                                        echo '<div class="comment">'.$commentTrail[$j].'</div>';              
                                    }
                                if ($arrDetails['image']!="")
				                {
                					$imgName = $arrDetails['image'];
                					$tempArray = explode(".",$imgName);
                					$extension = $tempArray[count($tempArray)-1];
                					if($extension!="swf")
                						echo "<br/><img src=\"".$basedir.$imgName."\" align=\"middle\">";
                					else
                					{
                						$imagedetails = @getimagesize($basedir."/".$imgName);
                						$width = $imagedetails[0];
                				                $height = $imagedetails[1];
                						echo "<OBJECT classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0'
                						HEIGHT='$height' WIDTH='$width'>
                						<PARAM NAME=movie VALUE='".$basedir."/".$imgName."'>
                						<PARAM NAME=quality VALUE=high>
                						<PARAM name='wmode' VALUE='transparent'>
                						<PARAM name='menu' VALUE='false'>
                						<EMBED src='".$basedir."/".$imgName."'
                						quality=high
                						menu='false'
                						TYPE='application/x-shockwave-flash'
                						PLUGINSPAGE='http://www.macromedia.com/go/getflashplayer'
                						WMODE='Transparent'
                						HEIGHT='$height' WIDTH='$width'
                						LOOP=true>
                						</EMBED>
                						</OBJECT>";
                					}
                				}                                
                            }    
                            echo "</div>";                            
                    }
                    echo "</td>";                    
                    echo "</tr>";                   
                }
                ?>
            </table>
        </div>
        <?php 
            }            
            else {
                echo "<div align='center'><h3>No comments found!<h3></div>";                
            }            
        ?>
    </div>    
    </p>
    </form>
    <form id='teacherComment' name='teacherComment' method='post' target="_blank" action="teacherComment.php">
        <input type='hidden' name='mode' id='mode' value='studentComment'/>
        <input type='hidden' name='type' id='type' value/>
        <input type='hidden' name='qcode' id='qcode' value/>
        <input type='hidden' name='qno' id='qno' value/>
        <input type='hidden' name='childClass' id='childClass' value/>
        <input type='hidden' name='comment_srno' id='comment_srno' value/>
    </form>
    
</div>
</div>

<?php include("footer.php") ?>

<?php 
function getWhereClause($schoolCode, $allClassArray, $allSectionsArray, $selectedClass, $selectedSection, $selectedUser)
{
    $whereClause =" WHERE a.userID=b.userID AND a.category='STUDENT' and a.subcategory='School' AND a.enabled=1 AND a.schoolCode=$schoolCode";
    if($selectedClass!="")
    {
        $whereClause .= " AND childClass=$selectedClass";
        
        if($selectedSection!="")
        {
            $whereClause .= " AND childSection='$selectedSection'";    
        }  
        else if($allSectionsArray[$selectedClass]!="")
        {
            $whereClause .= " AND childSection in (".$allSectionsArray[$selectedClass].")";    
        }
    }    
    else
    {
        $whereClause .= " AND (";
        foreach($allClassArray as $cls)
        {
            $whereClause .= "  (childClass=$cls ";
            if($allSectionsArray[$cls]!="")
                $whereClause .= " AND childSection in (".$allSectionsArray[$cls].")";
            $whereClause .= ") OR ";      
        }  
        $whereClause = substr($whereClause, 0, -4 ).")";
    }
    if($selectedUser!="")
        $whereClause .= " AND childName LIKE '".mysql_escape_string($selectedUser)."%'";
    return $whereClause;
}
function getStudentCommentCount($schoolCode, $allClassArray, $allSectionsArray, $selectedClass, $selectedSection, $selectedUser="")
{    
    
    $comment_query = "SELECT count(*) FROM adepts_userDetails a,  adepts_userComments b ";
    $comment_query .= getWhereClause($schoolCode, $allClassArray, $allSectionsArray, $selectedClass, $selectedSection, $selectedUser);                             
    $comment_result= mysql_query($comment_query) or die("<br>Error in fetching comment details".mysql_error().$comment_query);    
    $comment_line = mysql_fetch_array($comment_result);    
    return ($comment_line[0]);
}

function getStudentCommentDetails($limitClause,$schoolCode, $allClassArray, $allSectionsArray, $selectedClass, $selectedSection, $selectedUser="")
{   
    $arrCommentDetails = array();
    $comment_query = "SELECT a.userID, a.childName, a.childClass, a.childSection, b.comment, b.srno, b.type, b.image, notRelatedToQuestion, b.previousQuestionDetails, b.sessionID, b.questionNo, b.qcode
                                     FROM adepts_userDetails a,  adepts_userComments b ";
    $comment_query .= getWhereClause($schoolCode, $allClassArray, $allSectionsArray, $selectedClass, $selectedSection, $selectedUser);                                 
    $comment_query .= " ORDER BY srno DESC ".$limitClause;
    $comment_result= mysql_query($comment_query) or die("<br>Error in fetching comment details".mysql_error());    
    $srno =0;
    while($comment_line = mysql_fetch_array($comment_result))
    {
        $arrCommentDetails[$srno]["userID"] = $comment_line['userID'];  
        $arrCommentDetails[$srno]["comment_srno"] = $comment_line['srno'];  
        $arrCommentDetails[$srno]["name"] = $comment_line['childName'];  
        $arrCommentDetails[$srno]["class"] = $comment_line['childClass'];
        $arrCommentDetails[$srno]['section'] = $comment_line['childSection'];  
        $arrCommentDetails[$srno]["comment"] = $comment_line['comment'];
        $arrCommentDetails[$srno]["type"] = $comment_line['type'];
        $arrCommentDetails[$srno]["sessionID"] = $comment_line['sessionID'];
        //$arrCommentDetails[$srno]["questionNo"] = $comment_line['questionNo'];
        $arrCommentDetails[$srno]["notRelatedToQuestion"] = $comment_line['notRelatedToQuestion'];
        $arrCommentDetails[$srno]["image"] = $comment_line['image'];
        $arrCommentDetails[$srno]["qcode"] = $comment_line['qcode'];
        $arrCommentDetails[$srno]["qno"] = $comment_line['questionNo'];
        $arrCommentDetails[$srno]["commentTrail"] = getCommentTrail($comment_line['srno'],$comment_line['childClass'],$comment_line['childName']);  
        $arrCommentDetails[$srno]["os"] = "";
        $arrCommentDetails[$srno]["browser"] = "";    
        $arrCommentDetails[$srno]["userResponse"] = "";            
        $arrCommentDetails[$srno]["correctAnswer"] = "";            
        $arrOSBrowserDetails = getBrowserDetails($comment_line['sessionID']);
        $arrCommentDetails[$srno]["os"] = $arrOSBrowserDetails['os'];
        $arrCommentDetails[$srno]["browser"] = $arrOSBrowserDetails['browser'];                
        
        $qno = $comment_line['questionNo'];
        if($comment_line['previousQuestion']==1)    //if comment is marked for previous question, show the previous question details
        {
                $prevDetails	=	explode("~",$comment_line['previousQuestionDetails']);
                $arrCommentDetails[$srno]["qcode"] = $prevDetails[0];
                if(isset($prevDetails[1]))
                    $arrCommentDetails[$srno]["type"] = $prevDetails[1];
                $qno =     $comment_line['questionNo'] - 1;
        }                
        if($comment_line['notRelatedToQuestion']==0)
        {                       
            $type = $arrCommentDetails[$srno]["type"];
            if($type=="normal" || $type=="challenge" || $type=="prepostTestQues" || $type=="question" || $type=="comprehensive" || strpos($type, 'wildcard') !== false)
            {
                  $arrCommentDetails[$srno]["userResponse"] = getUserResponse($comment_line['userID'],$comment_line['childClass'], $comment_line['sessionID'],$arrCommentDetails[$srno]["qcode"], $type);
                  if(strpos($type, 'research') !== false)
                    $objQuestion     = new researchQuestion($arrCommentDetails[$srno]["qcode"]);
                  else                           
                    $objQuestion     = new Question($arrCommentDetails[$srno]["qcode"]);
                  if(!$objQuestion->isDynamic())
    		  {             
                      $arrCommentDetails[$srno]["correctAnswer"] = $objQuestion->getCorrectAnswerForDisplay();
                  }
            }
        }
        else
        {
            $arrCommentDetails[$srno]["correctAnswer"] = "Not related to question";
        }
        $srno++;
    }
    return ($arrCommentDetails);
}

function getCommentTrail($comment_srno,$childClass,$firstName)
{
	$arrayComments	=	array();	
	$sq	=	"SELECT id,srno,comment,image,DATE_FORMAT(commentDate, '%M %e, %Y %h:%i %p') as commentDate,commenter,flag
			 FROM adepts_userCommentDetails 
			 WHERE srno=$comment_srno";
	$rs	=	mysql_query($sq);                   
	while($rw=mysql_fetch_array($rs))
	{
		$comment = explode('~',$rw[2]);
		if(count($comment)>1){
			$commentShow = $comment[count($comment)-1];
			$date = explode('::',$comment[count($comment)-1]);
			$rw[4] = $date[1];
		}else{
			$commentShow = stripslashes($comment[0]);
		}
		if($rw[6]==1)
                        $arrayComments[$rw[6]]	=	"<b>(".$rw[4]."): </b>".$commentShow;
		else if($rw[6]==3)
			$arrayComments[$rw[6]]	=	"<b>".$firstName." (".$rw[4]."): </b>".$commentShow;
		else
		{			
			$arrayComments[$rw[6]]	=	"<b>Mindspark (".$rw[4]."): </b>".$commentShow;
		}
	}
	return $arrayComments;
}

function getBrowserDetails($sessionID)
{
    $query = "SELECT browser FROM adepts_sessionStatus WHERE sessionID=$sessionID";
    $result = mysql_query($query);
    $line = mysql_fetch_array($result);
    $arrTemp = explode(",",$line['browser']);
    $arrOSBrowserDetails["browser"] = "Browser: ".$arrTemp[0];
    $arrOSBrowserDetails["os"] = $arrTemp[1];
    return $arrOSBrowserDetails;
}

function getUserResponse($userID, $cls, $sessionID,$qcode, $quesType)
{    
    $userResponse  = "";
    if($quesType=="challenge")
        $sqUserResponse =	"SELECT A FROM adepts_ttChallengeQuesAttempt WHERE userID=$userID AND sessionID=$sessionID AND qcode=$qcode";
    else if(strpos($quesType, 'wildcard') !== false)
    	$sqUserResponse =	"SELECT A FROM adepts_researchQuesAttempt WHERE userID=$userID AND qcode=$qcode AND sessionID=$sessionID";
    else
	   $sqUserResponse =	"SELECT A FROM adepts_teacherTopicQuesAttempt_class$cls WHERE userID=$userID AND sessionID=$sessionID AND qcode=$qcode";
       
    $result = mysql_query($sqUserResponse) or die("<br>Error in fetching user response");
    if($line = mysql_fetch_array($result))
    {
          $userResponse = $line['A'];    
    }
    return $userResponse;
}
?>