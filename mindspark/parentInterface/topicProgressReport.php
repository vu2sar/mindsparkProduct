<?php
@include("../userInterface/check1.php");
@include("../slave_connectivity.php");
include("../userInterface/constants.php");
include("../userInterface/classes/clsUser.php");
if (!isset($_SESSION['openIDEmail'])) {
    header("Location:../logout.php");
    exit;
}
set_time_limit(0);
error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING);
$startDate = date('Y-m-d', strtotime("-15 days"));
$endDate = date('Y-m-d');
$userID = $_SESSION['childID'];
if ($userID == '')
{
    echo 'No user found';
    exit();
}
$childClass = $_SESSION['childClassUsed'];
if ($childClass == '')
{
    echo 'No class found';
    exit();
}
$oneDay = 24 * 60 * 60;
//$startDate = $_REQUEST['startDate']; 
//if ($startDate == '')
//    $startDate = date('Y-m-d', strtotime("-15 days"));
//$endDate = $_REQUEST['endDate'];
//if ($endDate == '')
//    $endDate = date('Y-m-d');
$subjectNo = 2;
$ts = mktime(0, 0, 0, substr($startDate, 5, 2), substr($startDate, 8, 2), substr($startDate, 0, 4));
$lastMonth = date("Y-m-d", $ts);

//include "/home/educatio/public_html/mindspark/clsTopicProgress.php";
include "../clsTopicProgress.php";

$query = "SELECT distinct a.teacherTopicCode, teacherTopicDesc FROM adepts_teacherTopicStatus a, adepts_teacherTopicMaster b
	          WHERE  a.teacherTopicCode=b.teacherTopicCode AND userID=$userID";
$topic_result = mysql_query($query) or die(mysql_error());
if (mysql_num_rows($topic_result) > 0) {
    $srno = 1;
    $topicAttemptedArray = array();
    $topicAttemptedDetailArray = array();
    while ($topic_line = mysql_fetch_array($topic_result)) {
        array_push($topicAttemptedArray, $topic_line[0]);

        //$topicAttemptedArray[] = $topic_line[0]; //attempted topics this month
        //$perProgress = getTopicProgress($userID, $topic_line[0], $childClass);
        $flow_query = "SELECT flow FROM adepts_teacherTopicStatus WHERE userID=$userID AND teacherTopicCode='" . $topic_line[0] . "'";
        $flow_result = mysql_query($flow_query);
        $flow_line = mysql_fetch_array($flow_result);
        $flow = $flow_line[0];
        $objTopicProgress = new topicProgress($topic_line[0], $childClass, $flow, $subjectNo);
        $perProgress = $objTopicProgress->getProgressInTT($userID);
        if ($perProgress >= 100)
            $topicsCompleted++;
        //$perProgressTillLastMonth = getTopicProgress($userID, $topic_line[0], $childClass,$lastMonth);
        $perProgressTillLastMonth = $objTopicProgress->getProgressInTT($userID, $lastMonth);
        $query = "SELECT count(srno) as totalQues, sum(R) as correct
					  FROM   adepts_teacherTopicQuesAttempt_class$childClass a, adepts_teacherTopicClusterStatus b, adepts_teacherTopicStatus c
					  WHERE  a.userID=b.userID AND b.userID=c.userID AND a.clusterAttemptID=b.clusterAttemptID AND
					         b.ttAttemptID=c.ttAttemptID AND c.userID=$userID AND c.teacherTopicCode='" . $topic_line[0] . "' AND
					         attemptedDate>='$startDate' AND attemptedDate<='$endDate'";
        $ques_result = mysql_query($query) or die(mysql_error());
        $ques_line = mysql_fetch_array($ques_result);
        $quesAttempted = $ques_line['totalQues'];
        if ($quesAttempted != 0)
            $perCorrect = round($ques_line['correct'] / $quesAttempted * 100, 2);
        else
            $perCorrect = "";

//        if ($childClass >= 3) {
//            $query = "SELECT count(srno) as totalQues, sum(R) as correct
//						  FROM   adepts_ttChallengeQuesAttempt a, adepts_teacherTopicStatus b
//						  WHERE  a.userID=b.userID AND a.ttAttemptID=b.ttAttemptID AND b.userID=$userID AND teacherTopicCode='" . $topic_line[0] . "'
//						         AND cast(a.lastModified as date)>='$startDate' AND cast(a.lastModified as date)<='$endDate'";
//            $ques_result = mysql_query($query) or die(mysql_error());
//            $ques_line = mysql_fetch_array($ques_result);
//            $CQAttempted = $ques_line['totalQues'];
//            if ($CQAttempted != 0)
//                $CQCorrect = round($ques_line['correct'] / $CQAttempted * 100, 2);
//            else
//                $CQCorrect = "";
//        }
        $topicAttemptedDetailArray[$topic_line[0]]['Description'] = $topic_line[1];
        $topicAttemptedDetailArray[$topic_line[0]]['ProgressAll'] = $perProgress;
        $topicAttemptedDetailArray[$topic_line[0]]['ProgressLastPeriod'] = $perProgressTillLastMonth;
        $topicAttemptedDetailArray[$topic_line[0]]['totalQues'] = $quesAttempted + (intval($CQAttempted) > 0 ? intval($CQAttempted) : 0);
    }
}

function cmp($a, $b) {
    if ($a['totalQues'] == $b['totalQues']) {
        return 0;
    }
    return ($a['totalQues'] > $b['totalQues']) ? -1 : 1;
}
usort($topicAttemptedDetailArray, "cmp");
?>
<?php
$jsArray = array();
array_push($jsArray, array('Topic', 'Progress 15 days ago', 'Progress till date'));
$count = 0;
foreach ($topicAttemptedDetailArray as $topic) {
    if ($topic['totalQues'] >= 5 && $count < 4) {
        array_push($jsArray, array($topic['Description'], (int) $topic['ProgressLastPeriod'], (int) $topic['ProgressAll']));
        $count++;
    }
//$jsArray[] = array($array['Description'], (int) $array['ProgressAll'], (int) $array['ProgressLastPeriod']); 
}
if($count>0)
    echo json_encode($jsArray);
else
    echo 'No data found';
exit();
?>
