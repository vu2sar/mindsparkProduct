<?php
function checkForTeacherForumPrompt($sessionuser, $schoolcode)
{    
    $teacherForumPrompt = 0;
        
    if(! isset ( $_SESSION ['viewForumPrompt'])) {                                
         $_SESSION ['viewForumPrompt'] = 1;         
         if(!check_allow_forum ( $sessionuser, $schoolcode) && date("Y-m-d")<='2015-04-01' ) //countdown timer
         {
                if(date("Y-m-d")>='2015-03-26' && isFirstSessionForToday() )
                    $teacherForumPrompt = 1;    //Countdown prompt
         }
         else if (checkfirsttimelogin ( $sessionuser, $schoolcode ) ) {
                $teacherForumPrompt = 2; //Teacher forum prompt 
         }
     }
     return $teacherForumPrompt;
}
function check_allow_forum($sessionuser,$schoolcode)
{
	$schoolcode_array = array(2387554,365439,23246,2474876,205449,1752,348782,359413,384445,525210,420525,650967,207093,33367,173767,208013);        
	if(in_array($schoolcode,$schoolcode_array) )
	{
		$query = "select userID from adepts_userDetails where category IN ('School Admin','TEACHER') and username = '$sessionuser' ";
		$result = mysql_query($query) or die(mysql_error());
		$num_rows = mysql_num_rows($result);
		if($num_rows > 0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
        else if(date("Y-m-d")>='2015-04-02' )
            return 1;
        else     
	    return 0;
}
function checkfirsttimelogin($sessionuser,$schoolcode)
{
	$returnflag = 0;
        $schoolcode_array = array(2387554,365439,23246,2474876,205449,1752,348782,359413,384445,525210,420525,650967,207093,33367,173767,208013);  
	//if(check_allow_forum($sessionuser,$schoolcode))
        if(in_array($schoolcode,$schoolcode_array) )
	{
		$sq = "SELECT count(sessionID) FROM adepts_sessionStatus WHERE userID=".$_SESSION["userID"]." AND startTime_int>20150316";
        }
        else
                $sq = "SELECT count(sessionID) FROM adepts_sessionStatus WHERE userID=".$_SESSION["userID"]." AND startTime_int>20150401";
	$rs = mysql_query($sq);
	$line = mysql_fetch_array($rs);
        $num = $line[0];
	if($num <=3)
	    $returnflag = 1;
				   
	return $returnflag;
}
function isFirstSessionForToday()
{
    $isFirstSessionForToday = 0;
    $today = date("Ymd");
    $sq = "SELECT count(sessionID) FROM adepts_sessionStatus WHERE userID=".$_SESSION["userID"]." AND startTime_int=$today";
    $rs = mysql_query($sq);
    $line = mysql_fetch_array($rs);
    $num = $line[0];
    if($num==1)
        $isFirstSessionForToday = 1;
    return $isFirstSessionForToday;
}

// popupbox config
$header_text = "Dear Teacher, now connect to fellow teachers across the country:";
$image_path = 'assets/forum.jpg';
$button_text = "Take me to the teacher forum";


?>