<?php
function checkPreTestStatus($userID, $teacherTopicCode, $class)
{
    $query  = "SELECT prepostTestID, preTestStatus, postTestStatus FROM adepts_prepostTestSummary WHERE userID=$userID AND teacherTopicCode='$teacherTopicCode'";
    $result = mysql_query($query);
    if($line = mysql_fetch_array($result))
    {
    	$prepostTestID = $line['prepostTestID'];
    	$_SESSION['prePostTestID']     = $prepostTestID;
        if($line['preTestStatus']=="In-progress")
        {
            $qcodeArray = array();
            $attemptedQuesArray = getQuesAttemptedInPrePostTest($userID, $prepostTestID,"pretest");
            $cluster_query  = "SELECT qcode FROM adepts_prepostTestMaster a, adepts_questions b
                               WHERE  a.preTestCluster=b.clusterCode AND prepostTestID=$prepostTestID AND b.status=3
                               ORDER BY subdifficultylevel";
            $cluster_result = mysql_query($cluster_query);
            while($cluster_line = mysql_fetch_array($cluster_result))
            {
                if(!in_array($cluster_line[0],$attemptedQuesArray))
                    array_push($qcodeArray, $cluster_line[0]);
            }
            $_SESSION['prePostTestQcodes'] = $qcodeArray;
            $_SESSION['prePostTestFlag']   = 1;   // 1 implies pretest and 2 for post test
            $_SESSION['prePostTestTopic']  = $teacherTopicCode;
        }
    }
}

function checkPostTestStatus($userID, $teacherTopicCode="",$class="")
{
    $query  = "SELECT prepostTestID, teacherTopicCode, postTestStatus FROM adepts_prepostTestSummary WHERE userID=$userID";
    if($teacherTopicCode!="")
        $query .= " AND teacherTopicCode='$teacherTopicCode'";
    $result = mysql_query($query);
    if($line = mysql_fetch_array($result))
    {
    	$prepostTestID = $line['prepostTestID'];
        $_SESSION['prePostTestID']     = $prepostTestID;
        if ($line['postTestStatus']=="In-progress")
        {
            $qcodeArray = array();
            $teacherTopicCode = $line['teacherTopicCode'];
            $attemptedQuesArray = getQuesAttemptedInPrePostTest($userID, $prepostTestID, "posttest");
            $cluster_query  = "SELECT qcode FROM adepts_prepostTestMaster a, adepts_questions b
                               WHERE  a.postTestCluster=b.clusterCode AND prepostTestID=$prepostTestID AND b.status=3
                               ORDER BY subdifficultylevel";
            $cluster_result = mysql_query($cluster_query);
            while($cluster_line = mysql_fetch_array($cluster_result))
            {
                if(!in_array($cluster_line[0],$attemptedQuesArray))
                    array_push($qcodeArray, $cluster_line[0]);
            }
            $_SESSION['prePostTestQcodes'] = $qcodeArray;
            $_SESSION['prePostTestFlag']   = 2;   // 1 implies pretest and 2 for post test
            $_SESSION['prePostTestTopic']  = $teacherTopicCode;

        }
    }
}

function isPostTestPending($userID, $teacherTopicCode)
{
	$postTestPending = 0;
	$query  = "SELECT prepostTestID, teacherTopicCode, postTestStatus FROM adepts_prepostTestSummary WHERE userID=$userID AND teacherTopicCode='$teacherTopicCode'";
    $result = mysql_query($query);
    if($line = mysql_fetch_array($result))
    {
    	if($line['status']=="pending")
    		$postTestPending = 1;
    }
    return $postTestPending;
}

function getQuesAttemptedInPrePostTest($userID, $prepostTestID, $type)
{
    $attemptedQuesArray = array();
    $attemptedques_query = "SELECT qcode FROM adepts_prepost_Test_Details WHERE userID=$userID AND testType='$type' AND prepostTestID=$prepostTestID";
    $attemptedques_result = mysql_query($attemptedques_query) or die("Error in fetching prepost attempted questions");
    while($attemptedques_line = mysql_fetch_array($attemptedques_result))
    {
        array_push($attemptedQuesArray,$attemptedques_line[0]);
    }
    return $attemptedQuesArray;
}
function activatePostTest($userID, $teacherTopicCode)
{
    $query  = "SELECT postTestStatus FROM adepts_prepostTestSummary WHERE userID=$userID AND teacherTopicCode='$teacherTopicCode'";
    $result = mysql_query($query);
    if($line = mysql_fetch_array($result))
    {
        if($line['postTestStatus']=="pending")
        {
            $query = "UPDATE adepts_prepostTestSummary SET postTestStatus='In-progress' WHERE userID=$userID AND teacherTopicCode='$teacherTopicCode'";
            $result = mysql_query($query);
        }
    }
}
function checkPrePostTestActivation($userID, $teacherTopicCode, $topicAttemptNo)
{
    $status = "";
    $user = new User($userID);
    $schoolCode = $user->schoolCode;
    $class = $user->childClass;
    $section = $user->childSection;

    //Prepost test would be available for school users and given on the first attempt of the topic
    if(strcasecmp($user->category,"STUDENT")==0 && strcasecmp($user->subcategory,"School")==0 && $topicAttemptNo==1)
    {
        /*$flag = checkPrePostTestAvailability($teacherTopicCode, $class);
        if($flag)   //if pre/post test available for this topic, check if the teacher has enabled it
        {*/
            $active_query = "SELECT status, prepostTestID FROM adepts_prepostTestActivation WHERE schoolCode=$schoolCode AND class=$class";
            if($section!="")
                $active_query .= " AND section='$section'";
            $active_query .= " AND teacherTopicCode='$teacherTopicCode'";
            $active_result = mysql_query($active_query);
            if(mysql_num_rows($active_result)>0)
            {
	            $active_line = mysql_fetch_array($active_result);
	            if($active_line['status']=="active")    //if enabled, check if student entry for prepost test present, if not enter it
	            {
	            	$prepostTestID = $active_line['prepostTestID'];
	                $query  = "SELECT count(*) FROM adepts_prepostTestSummary WHERE userID=$userID AND teacherTopicCode='$teacherTopicCode' AND prepostTestID=$prepostTestID";
	                $result = mysql_query($query) or die(mysql_error());
	                $line = mysql_fetch_array($result);
	                if($line[0]==0)
	                {
	                    //make the pre test status as in progress since this will start immediately, post test will start on class level completion of the topic
	                    $query = "INSERT INTO adepts_prepostTestSummary(userID, teacherTopicCode, preTestStatus, postTestStatus,prepostTestID) VALUES
	                                   ($userID, '$teacherTopicCode', 'In-progress', 'pending',$prepostTestID) ";
	                    $result = mysql_query($query) or die("Error in inserting pre test data");
	                }
	            }
            }
        //}
    }
}

function savePrePostTestQuesAttempt($userID, $prepostTestID, $teacherTopicCode, $prePostTestFlag, $qcode, $response, $result, $secs, $sessionID)
{
    if($prePostTestFlag==1)
        $testType = "pretest";
    else
        $testType = "posttest";
    $query = "INSERT INTO adepts_prepost_Test_Details (userID, prepostTestID, teacherTopicCode, testType, qcode, userResponse, correct, timeTaken, sessionID) VALUES
              ($userID, $prepostTestID, '$teacherTopicCode', '$testType',$qcode, '$response','$result','$secs', $sessionID)";
    mysql_query($query);
}

function updatePrePostTestStatus($userID, $teacherTopicCode, $prePostTestFlag, $prepostTestID)
{
    if($prePostTestFlag==1)
        $testType = "pretest";
    else
        $testType = "posttest";
    //user test score
    $query	=	"SELECT sum(if(correct=1,1,0)), sum(timeTaken) FROM adepts_prepost_Test_Details WHERE userID=$userID AND prepostTestID=$prepostTestID AND testType='$testType'";
    $result	=	mysql_query($query);
    $line	=	mysql_fetch_array($result);
    $score	=	$line[0];
    $totalTime = $line[1];


    //update user test details and score in the adepts_prepostTestSummary table
    $query	=	"UPDATE adepts_prepostTestSummary SET ";
    $where	=	" WHERE prepostTestID=$prepostTestID AND userid='$userID' ";
    if($testType=='pretest')
    	$sqUpdate	=	$query." preTestStatus='Completed',preTestScore='$score',totalTimeSpentOnPretest='$totalTime'".$where;
    else if($testType=='posttest')
    	$sqUpdate	=	$query." postTestStatus='Completed',postTestScore='$score',totalTimeSpentOnPosttest='$totalTime'".$where;
    $rsUpdate	=	mysql_query($sqUpdate) or die(mysql_error());

}
?>