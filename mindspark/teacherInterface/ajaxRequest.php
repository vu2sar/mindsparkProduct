<?php
set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
@include("../userInterface/check1.php");
include("functions/functions.php");
include("../userInterface/constants.php");
include("classes/testTeacherIDs.php");
include_once("../userInterface/classes/clsTopicProgress.php");
include_once("../userInterface/classes/clsTeacherTopic.php");
// Added for mantis task ID-17783 
// include("/home/educatio/public_html/ses.php");
// include("/home/educatio/public_html/mindspark/clsCrypt.php");
// include("/home/educatio/public_html/constants.php)";

$mode	=	$_REQUEST["mode"];
if($mode=="activate" || $mode=="deactivate")
{
	if(!isset($_SESSION['userID']))
	{
		echo "You are not authorised to access this page!";
		exit;
	}
	$query  = "SELECT username FROM adepts_userDetails WHERE userID=".$_SESSION['userID'];
	$result = mysql_query($query) or die(mysql_error());
	$line   = mysql_fetch_array($result);
	$loginID    = $line['username'];
	
	if(in_array($loginID,$testIDArray))
	{
		echo  "Sorry, you can't activate a topic from this id!";
		exit;
	}
	$ttCode	=	$_GET["ttCode"];
	$schoolCode	=	$_GET["schoolCode"];
	$class	=	$_GET["cls"];
	$flow	=	$_GET["flow"];
	$section	=	$_GET["section"];
	$modifiedBy	=	$_GET["modifiedBy"];
}

if($mode=="activate")
{
	$notCovered	=	$_GET["notCovered"];
	$fromDate	=	$_GET["fromDate"] != '' ? date("Y-m-d", strtotime($_GET["fromDate"])) : '';
	$toDate	=	$_GET["toDate"] != '' ? date("Y-m-d", strtotime($_GET["toDate"])) :'' ;
	$sectionList=explode(",", $section);
	$msg='';
	foreach ($sectionList as $key => $sec) {
		$msg=activatedTopic($schoolCode,$class,$sec,$ttCode,$flow,$modifiedBy,$notCovered,$fromDate,$toDate);
	}
	echo $msg;
}	
else if($mode=="deactivate")
{
	$sectionList=explode(",", $section);
	$msg='';
	foreach ($sectionList as $key => $sec) {
		$msg=deactivateTopic($schoolCode,$class,$sec,$ttCode,$modifiedBy);
	}
	echo str_replace(" deactivated successfully", '', $msg);
}
else if($mode == "getDeactivationList"){
	$ttCode=$_REQUEST['ttCode'];
	$class=$_REQUEST['cls'];
	$schoolCode=$_SESSION['schoolCode'];
	$sectionList=isset($_REQUEST['sectionList'])?explode(",",$_REQUEST['sectionList']):array("");
	$sectionProgress = array();
	foreach ($sectionList as $key => $section) {
		$userArray	=	getStudentDetails($class, $schoolCode, $section);
		$studentWiseProgress = getStudentProgress($ttCode,array_keys($userArray),$class);
		$sectionProgress[$section]=getAverageProgress($studentWiseProgress);
	}
	echo json_encode($sectionProgress);
	exit;
}
else if($mode == "getActivationList"){
	$ttCode=$_REQUEST['ttCode'];
	$class=$_REQUEST['cls'];
	$schoolCode=$_SESSION['schoolCode'];
	$flow=$_REQUEST['flow'];
	$clusterList=isset($_REQUEST['clusterList'])?$_REQUEST['clusterList']:"";
	$sectionList=explode(",",$_REQUEST['sectionList']);
	$sectionProgress = array();
	foreach ($sectionList as $key => $section) {
		if($clusterList!=""){
			$prg=topicAvailableForActivation($schoolCode,$class,$section,$clusterList,$flow,true);
		}
		else {
			$prg=topicAvailableForActivation($schoolCode,$class,$section,$ttCode,$flow);
		}
		if($prg==1 || $prg==-1 || $prg==-2)
			$sectionProgress[$section]=$prg;
	}
	echo json_encode($sectionProgress);
	exit;
}
else if($mode=="researchPaperCounter")
{
	$query  = "UPDATE adepts_researchModules set clickCnt=clickCnt+1 where moduleID=".mysql_escape_string($_GET['moduleID']);
    $result = mysql_query($query) or die(mysql_error());
    //return; 
}
else if($mode=="studentInterviewsCounter")
{
	$query  = "UPDATE adepts_studentInterviews set clickCnt=clickCnt+1 where interviewID=".mysql_escape_string($_GET['interviewID']);
    $result = mysql_query($query) or die(mysql_error());
    //return; 
}
else if($mode=="misconceptionVideosCounter")
{
	$query  = "UPDATE adepts_msVideos set clickCnt=clickCnt+1 where videoID=".$_GET['videoID'];
    $result = mysql_query($query) or die(mysql_error());
    //return; 
}
else if($mode=="misconceptionVideosLikeCounter")
{
	$query  = "UPDATE adepts_msVideos set likeCnt=likeCnt+1 where videoID=".$_GET['videoID'];
    $result = mysql_query($query) or die(mysql_error());
    //return; 
}
else if($mode=="misconceptionVideosDislikeCounter")
{
	$query  = "UPDATE adepts_msVideos set dislikeCnt=dislikeCnt+1 where videoID=".$_GET['videoID'];
    $result = mysql_query($query) or die(mysql_error());
    //return; 
}
else if($mode=="commonWrongAnswer")
{
	include("../slave_connectivity.php");
	$quesDetailsArr	=	explode("#",$_POST["quesDetails"]);
	$qcode	=	$quesDetailsArr[0];
	$dynamic	=	$quesDetailsArr[1];
	$showMostCommonWrongAns	=	$quesDetailsArr[2];
	$question_type	=	$quesDetailsArr[3];
	$correctAnswer	=	$quesDetailsArr[4];
	$class			=	$quesDetailsArr[5];
	$userIDStr		=	$quesDetailsArr[6];
	$cwaType = $quesDetailsArr[6];
	$questionStr = "";
	if(!$dynamic && $showMostCommonWrongAns)
    {
	    if(($question_type=='MCQ-4' || $question_type=='MCQ-2' || $question_type=='MCQ-3') && $class!="")	{
			if($cwaType==1)
			{
				$query = "SELECT A, count(srno) FROM ".TBL_QUES_ATTEMPT."_class$class
						  WHERE  userID in ($userIDStr) AND qcode=".$qcode;
				$query .= " GROUP BY A";
			}
			else if($class!="")
			{
				$query = "SELECT A, count(srno) FROM ".TBL_QUES_ATTEMPT."_class$class A, adepts_userDetails B 
	            	      WHERE  A.userID=B.userID AND qcode=".$qcode;
	        	$query .= " AND category='STUDENT'  GROUP BY A";
			}
	        $result = mysql_query($query) or die(mysql_error().$query);
	        $totalAttempts = 0;
	        $optionsData = array();
	        while ($line = mysql_fetch_array($result)) {
	            $optionsData[$line[0]] = $line[1];
	            $totalAttempts += $line[1];
	        }
	        $max = 0;

	        foreach ($optionsData as $opt => $val)
	        {
	            $percentageOpted = $val/$totalAttempts*100;
	            if($percentageOpted>$max && $opt!=$correctAnswer)
	            {
	                $mostCommonWrongAnswer = $opt;
	                $max = $percentageOpted;
	            }
	        }
	    }
	    elseif ($question_type=="Blank" && $class!="")
	    {
			if($cwaType==1)
			{
				$query = "SELECT A,count(srno) FROM ".TBL_QUES_ATTEMPT."_class$class
	                  WHERE  userID in ($userIDStr) AND R=0 AND qcode=".$qcode." GROUP BY A ORDER BY 2 DESC limit 1";
			}
			else
			{
				$query = "SELECT A,count(srno) FROM ".TBL_QUES_ATTEMPT."_class$class A, adepts_userDetails B 
		                  WHERE  A.userID=B.userID AND R=0 AND qcode=".$qcode." AND category='STUDENT' GROUP BY A ORDER BY 2 DESC limit 1";
			}
			$result = mysql_query($query) or die(mysql_error().$query);
			$line = mysql_fetch_array($result);
			$mostCommonWrongAnswer = $line[0];
	    }
    }
    if($mostCommonWrongAnswer!="")
    {	
        $questionStr .= "<span class='title'>Most common wrong answer: </span>$mostCommonWrongAnswer";
    }
    if($dynamic)
    {
    	$questionStr .= "<div class='legend'>Note: This is a dynamically generated question. Students might not have got the same question.</div>";
    }
	echo $questionStr;
}
else if($mode == "questionForClassDiscsussion")
{
	include("../slave_connectivity.php");
	$quesDetailsArr	=	explode("#",$_POST["quesDetails"]);
	$qcode	=	$quesDetailsArr[0];
	$dynamic	=	$quesDetailsArr[1];
	$showMostCommonWrongAns	=	$quesDetailsArr[2];
	$question_type	=	$quesDetailsArr[3];
	$correctAnswer	=	$quesDetailsArr[4];	
	$userIDStr		=	$quesDetailsArr[5];	
	$questionStr = "";
	if(!$dynamic && $showMostCommonWrongAns)
    {
	    if($question_type=='MCQ-4' || $question_type=='MCQ-2' || $question_type=='MCQ-3')	
	    {			
			$query = "SELECT A, count(srno) FROM adepts_diagnosticQuestionAttempt WHERE userID in ($userIDStr) AND qcode=$qcode GROUP BY A";					 
	        $result = mysql_query($query) or die(mysql_error().$query);
	        $totalAttempts = 0;
	        $optionsData = array();
	        while ($line = mysql_fetch_array($result)) 
	        {
	            $optionsData[$line[0]] = $line[1];
	            $totalAttempts += $line[1];
	        }
	        $max = 0;

	        foreach ($optionsData as $opt => $val)
	        {
	            $percentageOpted = $val/$totalAttempts*100;
	            if($percentageOpted>$max && $opt!=$correctAnswer)
	            {
	                $mostCommonWrongAnswer = $opt;
	                $max = $percentageOpted;
	            }
	        }
	    }
	    else if($question_type=="Blank")
	    {
			
			$query = "SELECT A,count(srno) FROM adepts_diagnosticQuestionAttempt WHERE userID in ($userIDStr) AND R=0 AND qcode=$qcode GROUP BY A ORDER BY 2 DESC limit 1";			
			$result = mysql_query($query) or die(mysql_error().$query);
			$line = mysql_fetch_array($result);
			$mostCommonWrongAnswer = $line[0];
	    }
    }
    if($mostCommonWrongAnswer!="")
    {	
        $questionStr .= "<span class='title'>Most common wrong answer: </span>$mostCommonWrongAnswer";
    }
    if($dynamic)
    {
    	$questionStr .= "<div class='legend'>Note: This is a dynamically generated question. Students might not have got the same question.</div>";
    }
	echo $questionStr;
}
else if($mode=='searchLog')
{
	$userID = $_SESSION['userID'];
	$sessionID = $_SESSION['sessionID'];
	$searchedTerm = $_GET['searchTerm'];
	$resultedTerm = $_GET['searchResult'];
	$query = "Insert into adepts_userSearchLog(userID,searchTerm,resultTerm,sessionID) values($userID,'$searchedTerm','$resultedTerm',$sessionID)";
	
	mysql_query($query);
}
else if($mode=='helpVideoCounter')
{
	$query  = "UPDATE adepts_counterMaster set counter=counter+1 where videoID=".$_GET['videoID'];
	echo $query;
    $result = mysql_query($query) or die(mysql_error());
}
else if($mode=='saveEndTime')
{
	if(isset($_POST["sessionID"]) && $_POST["sessionID"]!="")
	{
		$sessionID = $_POST["sessionID"];
		$query  = "UPDATE adepts_sessionStatus set endTime=NOW() where sessionID=".$sessionID;
		$result = mysql_query($query) or die(mysql_error());
	}
}
else if($mode=='getClusters')
{
	$clusterArray = array();
	if(isset($_POST["teacherTopicCode"]) && $_POST["teacherTopicCode"]!="")
	{
		$teacherTopicCode = $_POST["teacherTopicCode"];
		$childClass = $_POST["childClass"];
		$childSection = $_POST["childSection"];
		$schoolCode = $_SESSION['schoolCode'];
		$query  = "SELECT flow FROM adepts_teacherTopicActivation WHERE schoolCode=$schoolCode AND class=$childClass";
		if($childSection!="")
			$query  .= " AND section='$childSection'";
		$result = mysql_query($query) or die(mysql_error());
		$rw = mysql_fetch_array($result);
		if(stripos("custom",$rw["flow"]) === false)
		{
			
			$fieldName = "ms_level";
			if($flow=="CBSE")
				$fieldName = "cbse_level";
			elseif($flow=="ICSE")
				$fieldName = "icse_level";
			elseif($flow=="IGCSE")
				$fieldName = "igcse_level";
			$sq = "SELECT A.cluster,A.clusterCode FROM adepts_clusterMaster A, adepts_teacherTopicClusterMaster B 
				   WHERE A.clusterCode=B.clusterCode AND teacherTopicCode='$teacherTopicCode' AND !FIND_IN_SET($childClass,$fieldName) AND status='Live' ";

			$rs = mysql_query($sq);
			while($rw=mysql_fetch_array($rs))
			{
				$clusterArray[$rw[1]] = $rw[0];
			}
		}
		else
		{
			$sq = "SELECT clusterCodes FROM adepts_customizedTopicDetails A, adepts_teacherTopicMaster B WHERE A.code=B.customCode AND teacherTopicCode='$teacherTopicCode'";
			$rs = mysql_query($sq);
			$rw = mysql_fetch_array($rs);
			$clusterCodes = $rw[0];
			$clusterCodesStr = str_ireplace(",","','",$clusterCodes);
			$sq = "SELECT A.cluster,A.clusterCode FROM adepts_clusterMaster WHERE clusterCode IN ('$clusterCodesStr')";
			$rs = mysql_query($sq);
			$rs = mysql_query($sq);
			while($rw=mysql_fetch_array($rs))
			{
				$clusterArray[$rw[1]] = $rw[0];
			}
		}
	}
	echo json_encode($clusterArray);
}
else if($mode=='getsections')
{
	$class = $_POST['classname'];
	$userID = $_SESSION['userID'];
	$query = "SELECT distinct section 
				  FROM     adepts_teacherClassMapping
				  WHERE    userID = $userID AND subjectno=".SUBJECTNO."
				  and class = $class ORDER BY section";
	$result = mysql_query($query) or die(mysql_error());
	$str = "&nbsp;&nbsp;<font style='color: #626161; font-size: 1.2em;'>Section</font>&nbsp;&nbsp;&nbsp;&nbsp;";
	$str .= "<select id='sectiondata' onchange=getrecords(this.value,'')>";
	$str .= "<option value='select'>Select Section</option>";
	while($line=mysql_fetch_array($result))
	{
		if(empty($line[0]))
		{
			$str = 'no records';
			echo $str;
			exit;
		}
		else
			$str .= "<option value='".$line[0]."'>".$line[0]."</option>";
	}
	$str .= "</select>";
	echo $str;
	exit;
}
else if($mode=='setcounter')
{
	$userID = $_SESSION['userID'];
	$noticecount = $_POST['noticecount'];
	$selectedsection = $_POST['section'];
	$selectedclass = $_POST['selectedclass'];
	$viewclass = $selectedclass.'-'.$selectedsection;
	$_SESSION['logincount'] = $noticecount;
	
	$query = "select id,viewClass from teacherAlertLoginUser where userID = $userID and DATE(lastmodified) = DATE(NOW())";
	$result = 	mysql_query($query) or die(mysql_error());
	while($line = mysql_fetch_array($result))
	{
		$viewclass = $line['viewClass'].','.$viewclass;
	}

	if(mysql_num_rows($result) > 0)
		$query = "update  teacherAlertLoginUser set viewCount = $noticecount , viewClass = '$viewclass'  where userID = $userID";
	else
		$query = "insert into teacherAlertLoginUser (userID , viewCount, viewClass, lastmodified) values ($userID,$noticecount,'$viewclass',now())";
    mysql_query($query) or die(mysql_error());
	exit;
}
else if($mode=='fetchresult')
{
		$userID = $_SESSION['userID'];
		$selectedsection = $_POST['section'];
		$selectedclass = $_POST['selectedclass'];
		$schoolCode = $_SESSION['schoolCode'];
		$classsection = $selectedclass.'-'.$selectedsection;
		$sessionID = $_SESSION['sessionID'];

		$trackQuery = "INSERT INTO trackingTeacherInterface (userID, sessionID, pageID, lastmodified) values ($userID,$sessionID,73,now())";
			mysql_query($trackQuery) or die(mysql_error());
		
		$query = "select id,viewClass from teacherAlertLoginUser where userID = $userID and DATE(lastmodified) = DATE(NOW())";
		$result = 	mysql_query($query) or die(mysql_error());
		while($line = mysql_fetch_array($result))
		{
			$viewclass = $line['viewClass'];
		}
			
		if(isset($viewclass))
		{
			$class2 = explode(",",$viewclass);
			if (in_array($classsection, $class2)) {
				?>
				<script>
					$('#checkflag').val('1');
				</script>
				<?php

			}
		}

		$_SESSION['prevclasssec'] .= "*".$classsection;
		$classsection = explode("-",$classsection);
		$days = $_POST['day'];
		$daysgap =  7*$days;
		$prevdate = date('Ymd', strtotime('-'.$daysgap.' days')); 
		$query = "SELECT A.userID FROM adepts_userDetails A LEFT JOIN adepts_sessionStatus B on A.userID = B.userID AND B.startTime_int >= $prevdate where A.schoolcode=$schoolCode AND A.childclass = $selectedclass";
		if(!empty($selectedsection))
			$query .= " AND A.childsection = '$selectedsection'";
		$query .= " AND A.enabled = 1 AND A.category!= 'TEACHER' AND A.endDate > CURDATE() AND B.userID is NULL";

		

		$result  = mysql_query($query) or die(mysql_error());
		while( $row = mysql_fetch_assoc( $result)){
			$datalist[] = $row; 
		}

		
						
		$i = 0;
		foreach($datalist as $val)
		{
			$val = $datalist[$i]['userID'];
			$query = "select A.username,B.startTime,A.userid  from  adepts_userDetails A , adepts_sessionStatus B where A.userID = $val and A.userID=B.userID order by B.lastmodified desc";

			
			$result  = mysql_query($query) or die($query.mysql_error());

			if(	mysql_num_rows($result) == 0)
			{
				$query = "select username from  adepts_userDetails  where userID = $val";
				$result  = mysql_query($query) or die(mysql_error());
			}
			if ($line = mysql_fetch_array($result))
			{
				if($line[1])
				{
					$now = time();
					$your_date = strtotime($line[1]);
					$datediff = $now - $your_date;
					$daydiff =  floor($datediff/(60*60*24));
					$studentnames[$i]['login'] = $daydiff;
				}else
					$studentnames[$i]['login'] = 365;


				$studentnames[$i]['name'] = $line[0];
				
				
			}
			$i++;
		}
	
		 function sortbysession($a, $b) {
				return $a['login'] - $b['login'];
				}
		 usort($studentnames, "sortbysession");
		
		if(count($studentnames) > 0)
			{
		$str .= "<center><b style='color:red;'>Students who haven't logged in the last ".$daysgap." days </b></center><br>";
		$str .= '<table width="40%" cellspacing="0" cellpadding="3" border="1" align="center" style="text-align:center;" >';
		$str .=		'<tr>';
		$str .=		'<th class="header" align="center" width="10%">S. No</th>';
		$str .=		'<th class="header" align="center" width="8%">Student Name</th>';
		$str .=		'<th class="header" align="center" width="25%">Last log in</th>';
		$str .=		'</tr>';

				$i = 1;
				foreach($studentnames as $data) 
				{
					if($data['login'] >= $daysgap)
					{
						$now = time();
						$your_date = strtotime($data['login']);
						$datediff = $now - $your_date;
						$daydiff =  floor($datediff/(60*60*24));

						if($data['login'] == 365)
							$displayMsg = "Never logged in";
						else
							$displayMsg = $data['login']." days ago";

						$str .= "<tr><td>".$i."</td>";
						$str .= "<td>".$data['name']."</td>";
						$str .= "<td>".$displayMsg."</td></tr>";
						$i++;
					}
				}
		$str .= '</table>';
		$str .= '<br>';
				}else{
					$str .= "<center><h3 style='color:#9ec956;'>Congrats! All your students have logged in the last ".$daysgap." days</h3></center>";
				}
		echo $str;
		exit;
	}

else if($mode=='doasmindspark')
{
	if(isset($_POST['blog']))
	{
		$data = $_POST['blog'];
		$pageID = 70;
	}
	if(isset($_POST['dms']))
	{
		$data = $_POST['dms'];
		$pageID = 69;
	}

	$userID = $_SESSION['userID'];
	$sessionID = $_SESSION['sessionID'];

	$query = "INSERT INTO trackingTeacherInterface (userID, sessionID, pageID, lastmodified) values ($userID,$sessionID,$pageID,now())";
	mysql_query($query) or die(mysql_error());
}
else if($mode=='doasActivity')
{
	if($_SESSION['admin']=="STUDENT" || $_SESSION['admin']=="GUEST")
	{
		echo "multitab";
		exit();
	}
	if(isset($_POST['pageId']))
    {
		$userID = $_SESSION['userID'];
		$sessionID = $_SESSION['sessionID'];
		$pageID = $_POST['pageId'];

			$query = "INSERT INTO trackingTeacherInterface (userID, sessionID, pageID, lastmodified)  values ($userID , $sessionID , $pageID , now())";  
			mysql_query($query) or die($query.mysql_error());
    }
	if(isset($_POST['theme'])){
		$_SESSION['theme']=$_POST['theme'];
	}
	if(isset($_POST['childClass'])){
		$_SESSION['childClass']=$_POST['childClass'];
	}
	$_SESSION["userType"]="teacherAsStudent";
}
else if($mode=='whatsKeepingBusy')
{
	if(isset($_POST['pageId']))
    {
		$userID = $_SESSION['userID'];
		$sessionID = $_SESSION['sessionID'];
		$pageID = $_POST['pageId'];

			$query = "INSERT INTO trackingTeacherInterface (userID, sessionID, pageID, lastmodified)  values ($userID , $sessionID , $pageID , now())";  
			mysql_query($query) or die($query.mysql_error());
    }
}
else if($mode=='doasMindsparkSampleQuestion')
{
	if(isset($_POST['pageId']))
    {
		$userID = $_SESSION['userID'];
		$sessionID = $_SESSION['sessionID'];
		$pageID = $_POST['pageId'];

			$query = "INSERT INTO trackingTeacherInterface (userID, sessionID, pageID, lastmodified)  values ($userID , $sessionID , $pageID , now())";  
			mysql_query($query) or die($query.mysql_error());
    }
}
else if($mode=='teacherForum')
{	
		$userID = $_SESSION['userID'];
		$sessionID = $_SESSION['sessionID'];
		$pageID = 82;

		$query = "INSERT INTO trackingTeacherInterface (userID, sessionID, pageID, lastmodified)  values ($userID , $sessionID , $pageID , now())";  
		mysql_query($query) or die($query.mysql_error());
 }
else if($mode=="getUserList")
{
    $schoolCode = isset($_POST['schoolCode'])?$_POST['schoolCode']:0;
    $class = isset($_POST['class'])?$_POST['class']:"";
    $section = isset($_POST['section'])?$_POST['section']:"";
    $arrStudents = array();
    $query  = "SELECT userID, childName FROM adepts_userDetails WHERE category='STUDENT' AND enabled=1 and endDate>=curdate() AND schoolCode=$schoolCode";
    if($class!="")
        $query .= " AND childClass=$class";
    if($childSection!="")
        $query .= " AND childSection='$section'";
    $query .= " ORDER BY childName";
    $result = mysql_query($query);
    while($line = mysql_fetch_array($result))
    {
        $arrStudents[] = array(
        'id' => $line["userID"],
        'studentname' => $line["childName"]
        );
    }
    echo json_encode($arrStudents);
}


if($mode == "mpiLinkStudentReportPTA"){

	
	$mpiSettings = array();
	$result = mysql_query("
		SELECT settingValue as JSON
		from userInterfaceSettings
		where schoolCode=$_REQUEST[schoolCode] and class=$_REQUEST[classValue] and section='$_REQUEST[sectionValue]' and settingName='mpi'
	") or die(mysql_error());
	if(mysql_num_rows($result)>0) {
		$mpiSettings = mysql_fetch_assoc($result);
		$mpiSettings = json_decode($mpiSettings['JSON'], true);
		if(isset($mpiSettings['others']['Recommended Weekly Usage'])) {
		  $mpiSettings['others']['Minimum weekly usage'] = $mpiSettings['others']['Recommended Weekly Usage'];
		  unset($mpiSettings['others']['Recommended Weekly Usage']);
		}
		$mpiSettings['weightages'] = array_filter($mpiSettings['weightages'], function($weightage) {
			return $weightage>0;
		});
	}
	exit(json_encode($mpiSettings));
	 $query = "SELECT class,section,settingValue from userInterfaceSettings where schoolCode='".$_POST['schoolCode']."' and settingName='mpi'";

                            $statusMPI =  mysql_query($query) or die(mysql_error());

                                        
                            while ($row = mysql_fetch_assoc($statusMPI)) {

                               /*  echo $row["class"]."--".$_REQUEST['classValue'];
                                   

                                   echo $row["section"]."--".$_REQUEST['section'];*/

                               if($row["settingValue"] == "CustomOff" && $row["class"]== $_POST['classValue'] && $row["section"]== $_POST['sectionValue']){

                                   
        
                                    /*echo "This Class does not have access to Mindspark Progress Report";
                                    */
                                    echo "Off";
                                    exit();
                                   
                                   
                                }else if($row["class"]== $_POST['classValue'] && $row["section"]== $_POST['sectionValue']){

 
                                    echo "On";
                                    exit();
                               
                                }

                              
                            }
}
else if($mode == "teacherQuestionRating")
{
		$ratingScore = $_POST['score'] + 14;
		$currentQcode = $_POST['currentQcode'];
		$sessionID = $_POST['sessionID'];
		$userID = $_POST['userID'];

		if($_POST['radioValue'] != "-1")
			$commentType = $_POST['radioValue']."~";
		else
			$commentType = "";
		
		$ratingComment = trim($ratingComment);
		if($ratingComment!="")
		{
			include_once("classes/clsbucketCommentsV1.php");
			$obj = new commentCategorization();
			$systemCategory	=	$obj->mark($ratingComment);
		}
		$ratingComment = mysql_real_escape_string($_POST['ratingComment']);
		$sq = "INSERT INTO adepts_emotToolbarTagging SET type='da',userID=$userID, fieldID=$currentQcode, sessionID=$sessionID, response='$ratingScore', comments='$commentType$ratingComment', commentCategory='$systemCategory'";
		$rs = mysql_query($sq);

		echo "1";  // to signal successfully completed
}
else if($mode == 'trackInterface')
{	
	$pageUrl = $_POST['pageUrl'];
	$type = $_POST['type'];
	$userID = $_POST['userID'];
	$sessionID = $_POST['sessionID'];
	$query = "SELECT pageID FROM trackingPageDetails WHERE pageName='".$pageUrl."' and pageType='".$type."'";	
	$result = mysql_query($query);
	$l = mysql_fetch_array($result);
	
	$pageid = $l[0];
	if($pageid)
	{
		$query = "insert into trackingTeacherInterface (userID, sessionID, pageID, lastmodified) values (".$userID.",".$sessionID.",$pageid,now())";
		mysql_query($query) or die(mysql_error());
	}
	echo "1";
}
else if($mode = "mpiStudentReportPTAMailer")
{
	$userID = $_POST["userID"];
	$mailBody = $_POST["mailBody"];
	$sq = "SELECT userName, childEmail, parentEmail, secondaryParentEmail,childName FROM adepts_userDetails WHERE userID=$userID";	
	$rs = mysql_query($sq);
	$rw = mysql_fetch_array($rs);	
	$userName = $rw[0];
	$childEmail = $rw[1];
	$parentEmail = $rw[2];
	$secondaryParentEmail = $rw[3];
	$childName = ucwords($rw[4]);		
	 if ($parentEmail != "") {
        $mailto = $parentEmail;		
		$mailto = explode(",", $mailto);
		
        if (count($mailto)>1)
        {
            $cc = $mailto[1];
            $mailto = $mailto[0];
        }
        else $mailto = $mailto[0];   
	}
	if ($mailto == $cc)
        $cc = "";
    
    if ($mailto != "")
    {    	
	    $con = new SimpleEmailService(awsAccessKey,awsSecretKey);
	    $output  = '';
		$output .= $mailBody;        
	    $crypt = new Crypt();
	    $encryptUserID = $crypt->encrypt($userID);
	    $output .= "<br/>";
	    $output .= "You can also refer a friend and get benefits - <a href='www.mindspark.in/referral.php?userID=" . rawurlencode($encryptUserID) . "'>Click here</a>";
	    $output .= "<div style='font-style:italic'>You can now post your comments and query on Mindspark page on Facebook: www.facebook.com/mindspark.ei</div>";
	    $output .= "<br/><br/>";
	    $output .= "Regards,<br/>";
	    $output .= "Team Mindspark<br/>";
	    $output .= "Toll Free no: 1800 102 8885<br/>";
	    $output .= "www.mindspark.in<br/>";
	    $output .= "www.facebook.com/mindspark.ei<br/>";
	    $output .= "www.twitter.com/mindspark_ei<br/>";
	    $output .= "</body></html>";    
	    $headers = "From:mindspark@ei-india.com\r\n";
	    $headers .= "Reply-To:<mindspark@ei-india.com>\r\n";
	    if ($cc != "") {
	        $headers .= "Cc:$cc\r\n";       
	    }	    
	    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	    $subject = "Mindspark: " . $childName . "'s performance report ";    	    
	    if ($cc != "")
	        $m->addCC($cc);
	    $m->addReplyTo("mindspark@ei-india.com");    
	    $m->addTo($mailto);
	    $m->setFrom('mindspark@ei-india.com');
	    $m->addBcc('notification@ei-india.com');
	    $m->setSubject($subject);
	    $m->setMessageFromString("", $output);
	    $response = $con->sendEmail($m);
	    if($response == false)
	    	echo $childName;	
	    else  
	    {
	    	$messageId = $response['MessageId'];
	    	insert(17, $mailto, $cc, "", "mindspark@ei-india.com", "mindspark@ei-india.com", 1, $messageId);
	    	echo 'success';
	    }  	    	
    }
    else
    	echo $childName;

}


function insert($type, $to, $cc, $bcc, $from, $reply_to, $success, $messageId = '') {

    $time_stamp = time();
    $query = " insert into adepts_mail set ";
    $query.= " type = '" . $type . "', ";
    $query.= " sent_to = '" . $to . "', ";
    $query.= " sent_cc = '" . $cc . "', ";
    $query.= " sent_bcc = '" . $bcc . "', ";
    $query.= " sender = '" . $from . "', ";
    $query.= " reply_to = '" . $reply_to . "', ";
    $query.= " success = '" . $success . "' ";
    if ($messageId != '')
        $query.= ", messageId = '" . $messageId . "' ";
    //echo $query;
    mysql_query($query) or die(mysql_error());
}
?>