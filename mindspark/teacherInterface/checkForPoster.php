<?php	
	function checkForTeacherPosterPrompt($sessionuser, $schoolcode)
	{    
	    $teacherPosterPrompt = 0;//unset($_SESSION ['viewPosterPrompt']);
	    if(! isset ( $_SESSION ['viewPosterPrompt'])) {
	        $_SESSION ['viewPosterPrompt'] = 1;      
	        global $postersList;
	        global $posterHeader;
	        global $posterImage;
	        global $posterLink;
	        global $posterFooter;

	        foreach ($postersList as $key => $value) {
	        	$trigger=$value['trigger'];
	        	$triggerVal=$trigger($sessionuser, $schoolcode);
	        	if ($triggerVal>0){
	        		$posterHeader=$value['posterHeader'];
	        		$posterFooter=$value['posterFooter'];
	        		$posterLink=$value['posterLink'];
	        		$posterIArr=explode(",",$value['posterImage']);
	        		$posterImage=$posterIArr[$triggerVal-1];
	        		$teacherPosterPrompt = 1;
	        		break;
	        	}
	        }
	        /*$loginType=getLoginType($sessionuser, $schoolcode);
         	switch ($loginType) {
         		case 1:
         			$posterHeader="Dear teacher, welcome to the revamped teacher interface!";
         			$posterHeader="";
         			$posterImage="assets/posters/dailyPractice1.jpg";
         			if (checkDailyPracticePosterAllowed($sessionuser, $schoolcode)) $teacherPosterPrompt = 1;
         			break;

         		case 2:
         			$posterHeader="Dear teacher, the new landing page gives you summary of everything related to your classes at your finger tips!";
         			$posterImage="assets/newTeacherInterface/poster2.png";
         			//$teacherPosterPrompt = 1;
         			break;
         		case 3:
         			$posterHeader="Dear teacher, the revamped class report which gives you a summary of everything going on in your class.";
         			$posterImage="assets/newTeacherInterface/poster3.png";
         			//$teacherPosterPrompt = 1;
         			break;

         		case 4:
         			$posterHeader="Dear teacher, the new topic page incorporates activation/customisation, topic research and activities - all at one place!";
         			$posterImage="assets/newTeacherInterface/poster4.png";
         			//$teacherPosterPrompt = 1;
         			break;
         		
         		default:
         			# code...
         			break;
         	}*/	
	     }
	     return $teacherPosterPrompt;
	}

	function getLoginDaysSinceTeacher($dateSince = "20160102")
	{
		//echo 'getLoginDaysSinceTeacher';
		$sq = "SELECT count(sessionID) FROM adepts_sessionStatus WHERE userID=".$_SESSION["userID"]." AND startTime_int>'$dateSince' GROUP BY startTime_int";
		$rs = mysql_query($sq);
		$line = mysql_fetch_array($rs);
		$num = $line[0];
		$numLoginDays = mysql_num_rows($rs);
		//if (isFirstSessionToday()==1) 
			return $numLoginDays;
		//return 0;

	}
	function checkTeacherWorksheetPosterAllowed($sessionuser, $schoolcode){
		//echo 'checkDailyPracticePosterAllowed';
		$numLoginDays=getLoginDaysSinceTeacher('20160511');
		if (isFirstSessionToday()==0 || $numLoginDays>3) return 0;
		$sq="SELECT COUNT(a.userID) FROM adepts_userDetails a LEFT JOIN adepts_teacherClassMapping b ON a.userID=b.userID
			WHERE ((a.category = 'TEACHER' AND (class>=4)) || (a.category IN( 'School Admin', 'ADMIN') AND isNULL(class))) AND username='$sessionuser'";
		$rs = mysql_query($sq);
		$line = mysql_fetch_array($rs);
		if ($line[0]>0) return $numLoginDays;
		return 0;
	}
	function checkDailyPracticePosterAllowed($sessionuser, $schoolcode){
		//echo 'checkDailyPracticePosterAllowed';
		if (isFirstSessionToday()==0 || getLoginDaysSinceTeacher('20160104')>3) return 0;
		$sq="SELECT COUNT(a.userID) FROM adepts_userDetails a LEFT JOIN adepts_teacherClassMapping b ON a.userID=b.userID
			WHERE ((a.category = 'TEACHER' AND (class>=4 AND class<=7)) || (a.category IN( 'School Admin', 'ADMIN') AND isNULL(class))) AND username='$sessionuser'";
		$rs = mysql_query($sq);
		$line = mysql_fetch_array($rs);
		if ($line[0]>0) return 1;
		return 0;
	}
	function checkAllowPoster($sessionuser,$schoolcode)
	{
		$schoolcode_array = array(2387554,365439,23246,2474876,205449,1752,348782,359413,384445,525210,420525,650967,207093,33367,173767,208013);        
		if(in_array($schoolcode,$schoolcode_array) )
		{
			$query = "select userID from adepts_userDetails where category IN ('School Admin','TEACHER') and username = '$sessionuser' ";
			$result = mysql_query($query) or die(mysql_error());
			$num_rows = mysql_num_rows($result);
			if($num_rows > 0)
				return 1;
			else
				return 0;
		}
        else if(date("Y-m-d")>='2015-04-02' )
            return 1;
        else     
	    	return 0;
	}
	function isFirstTimeLogin($sessionuser,$schoolcode)
	{
		$returnflag = 0;
	    $schoolcode_array = array(2387554,365439,23246,2474876,205449,1752,348782,359413,384445,525210,420525,650967,207093,33367,173767,208013);  
	    if(in_array($schoolcode,$schoolcode_array) )
				$sq = "SELECT count(sessionID) FROM adepts_sessionStatus WHERE userID=".$_SESSION["userID"]." AND startTime_int>20150316";
	    else
	            $sq = "SELECT count(sessionID) FROM adepts_sessionStatus WHERE userID=".$_SESSION["userID"]." AND startTime_int>20150401";

		$rs = mysql_query($sq);
		$line = mysql_fetch_array($rs);
	        $num = $line[0];
		if($num <=3)
		    $returnflag = 1;
					   
		return $returnflag;
	}
	function isFirstSessionToday()
	{
		//echo 'isFirstSessionToday';
	    $isFirstSessionToday = 0;
	    $today = date("Ymd");
	    $sq = "SELECT count(sessionID) FROM adepts_sessionStatus WHERE userID=".$_SESSION["userID"]." AND startTime_int=$today";
	    $rs = mysql_query($sq);
	    $line = mysql_fetch_array($rs);
	    $num = $line[0];
	    if($num==1)
	        $isFirstSessionToday = 1;
	    return $isFirstSessionToday;
	}


	$postersList=array();

	$thisPoster=array();
	$thisPoster['posterFor']='Teacher Worksheets';
	$thisPoster['posterHeader']='';
	$thisPoster['posterImage']="assets/posters/teacherWorksheets1.jpg,assets/posters/teacherWorksheets2.jpg,assets/posters/teacherWorksheets3.jpg";
	$thisPoster['posterFooter']='';
	$thisPoster['posterLink']='/mindspark/app/worksheet/api/dashboard';
	$thisPoster['trigger']='checkTeacherWorksheetPosterAllowed';
	$postersList[]	=	$thisPoster;

	$thisPoster=array();
	$thisPoster['posterFor']='Daily Practice Live';
	$thisPoster['posterHeader']='';
	$thisPoster['posterImage']="assets/posters/dailyPractice1.jpg";
	$thisPoster['posterFooter']='';
	$thisPoster['posterLink']='';
	$thisPoster['trigger']='checkDailyPracticePosterAllowed';
	$postersList[]	=	$thisPoster;

	$posterHeader='';
	$posterImage='';
	$posterLink='';
	$posterFooter='';
?>