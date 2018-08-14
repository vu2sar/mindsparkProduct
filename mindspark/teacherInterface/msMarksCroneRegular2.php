<?php

set_time_limit(0);
error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING);

/*$link = mysql_connect("192.168.0.15","ms_analysis","sl@vedb@e!")  or die (mysql_errno()."-".mysql_error()."Could not connect to localhost");*/

@include("../slave_connectivity.php");
//    include "/home/educatio/public_html/connect.php";
mysql_select_db("educatio_adepts");
//$querySchool="SELECT distinct schoolCode,order_id,start_date,end_date,B.schoolname FROM educatio_educat.ms_orderMaster A, educatio_educat.schools B WHERE A.schoolCode=B.schoolno AND order_type='regular' AND A.year=2013 AND A.schoolCode!=2387554 ORDER BY city";
//$schoolResult = mysql_query($querySchool) or die(mysql_error());
$counter = 0;
$schoolArr = array(64811);
$arrlength = count($schoolArr);
for ($x = 0; $x < $arrlength; $x++) {
    $querySchool = "SELECT distinct schoolCode,order_id,start_date,end_date,B.schoolname FROM educatio_educat.ms_orderMaster A, educatio_educat.schools B WHERE A.schoolCode=B.schoolno AND order_type='regular' AND A.year=2014 AND A.schoolCode!=2387554 AND A.schoolCode=" . $schoolArr[$x] . " ORDER BY city";
    $schoolResult = mysql_query($querySchool) or die(mysql_error());
    $schoolLine = mysql_fetch_array($schoolResult);
//       runCroneforSchool($schoolLine[0],$schoolLine[1],$schoolLine[2],$schoolLine[3],$schoolLine[4]);
    runCroneforSchool($schoolLine[0], $schoolLine[1], '2014-11-15', '2014-12-15', $schoolLine[4]);
    //$counter++;
    //if($counter==10)
    //     break;
}
$test = 0;
$tableExtension = '';
$schoolArr = array();
$schoolLine = array();
//while($schoolLine=mysql_fetch_array($schoolResult)){
//    $test++;
//   if(!in_array($schoolLine['schoolCode'],$schoolArr))
//        array_push($schoolArr,$schoolLine['schoolCode']);
//    else{
//        continue;
//       }
//    
//  
//}
$schoolLine['schoolCode'] = $_GET['schoolCode'];

function runCroneforSchool($schoolCode, $orderID, $start_date, $end_date, $schoolname) {
    $csv_string = '';
//        $upgradeQuery="SELECT is_upgrade FROM educatio_educat.upgradation_conformation WHERE schoolCode=".$schoolCode;
//        $upgradeResult = mysql_query($upgradeQuery) or die(mysql_error());
//        $upgradeResult=mysql_fetch_array($upgradeResult);
//        if($upgradeResult[0]!=1)
//            $upgraded=0;
//        else
//            $upgraded=1;
    $upgraded = 0;
    $cnt2 = 0;
    $counter++;
   // echo $orderID;
    $queryOrder = "SELECT class FROM educatio_educat.ms_studentBreakup WHERE order_id=" . $orderID;
    $schoolOrder = mysql_query($queryOrder) or die(mysql_error() . "~~1");
    while ($orderLine = mysql_fetch_array($schoolOrder)) {
        $classCnt = $orderLine[0];
        $query = "select distinct A.userID,B.childSection,B.childName from adepts_sessionStatus A,adepts_userDetails B WHERE B.schoolCode=$schoolCode and A.userid=B.userid and B.childClass=" . ($classCnt) . " and A.startTime<='$end_date' AND startTime>='$start_date' AND B.category='student'";
        $usersResult = mysql_query($query) or die(mysql_error());
        $userArray = array();
        $classArray = $dobArray = array();
        $userNameArr = array();
        $childClassArr = array();
        $stud = 0;
        $msMarks = array();
        $tableExtensionArr = array();
        $schoolNameArr = array();
        while ($usersLine = mysql_fetch_array($usersResult)) {

            $stud++;
            $userArray[$usersLine['userID']] = $usersLine['childName'];
            $classArray[$usersLine['userID']] = $classCnt . $usersLine['childSection'];
            $schoolNameArr[$usersLine['userID']] = $schoolname;
            $tableExtensionArr[$usersLine['userID']] = $upgraded;
            $msMarks[$usersLine['userID']] = 0;
            $msMarks[$usersLine['userID']] = explode("~", newScore($usersLine['userID'], $start_date, $end_date, $classCnt, 0, $tableExtensionArr[$usersLine['userID']]));
            $msMarks[$usersLine['userID']] = $msMarks[$usersLine['userID']][0];
        
		
        $cnt = 0;
        arsort($msMarks);
        $strength = count($msMarks);
        $avgMPI = round(array_sum($msMarks) / $strength, 1);
        $maxMPI = max($msMarks);
        if (!is_numeric($maxMPI))
            $maxMPI = 0;
        if ($stud != 0) {
            foreach ($msMarks as $userID => $marks) {
                $msMarks[$userID] = getScore($marks,$avgMPI, $maxMPI,100);
            }
        }

			$csv_string .=  $usersLine['userID'].",".$usersLine['childName'].",".$classArray[$usersLine['userID']].','.$msMarks[$userID]. "\r\n";

		}
        $header = "User ID, child name,class, MPI score";
        $content = $header . "\r\n" . $csv_string;
        $fp = fopen("mpi.csv", "w");
        fwrite($fp, $content);
        fclose($fp);
    }
}

function getScore($childmpi, $avgmpi, $maxmpi, $scoreOutOf) {
        $childscore = 0;
        if ($avgmpi <= 0)
            $childscore = 0;
        elseif ($childmpi < .3 * $avgmpi) {
            if ($childmpi < 0) {
                $childscore = 0;
            } else {
                $childscore = round(($childmpi / (.3 * $avgmpi)) * 3, 1);
            }
        } elseif ($childmpi < 3 * $avgmpi) {
            if ($maxmpi < 3 * $avgmpi) {
                $childscore = 3 + 7 * round(($childmpi - (.3 * $avgmpi)) / ($maxmpi - (.3 * $avgmpi)), 1);
            } else {
                $childscore = 3 + 5 * round(($childmpi - (.3 * $avgmpi)) / (3 * $avgmpi - .3 * $avgmpi), 1);
            }
        } else {
            $childscore = 8 + 2 * round(($childmpi - 3 * $avgmpi) / ($maxmpi - 3 * $avgmpi), 1);
        }

//The child score obtained is by default out of 10 - convert it on the scale of score out of variable
        $childscore = round($scoreOutOf * $childscore / 10);
        return $childscore;
    }

function newScore($userid, $fromDate, $tillDate, $class, $gracePeriod, $archivedOrNot) {
    $calculationFactot = array();
    $param = array();
    if ($archivedOrNot)
        $tableExtension = "_archive";
    else
        $tableExtension = '';
    $param[0] = explode("~", getTimeSpent($userid, $fromDate, $tillDate, $tableExtension));

    if ($archivedOrNot)
        $tableExtension = '_archive_201213';
    else
        $tableExtension = '';
    $param[1] = explode("~", totalQues($userid, $class, $fromDate, $tillDate, $tableExtension));
    if ($archivedOrNot)
        $tableExtension = "_archive";
    else
        $tableExtension = '';
    $param[2] = explode("~", noOfTopicandProgress($userid, $fromDate, $tillDate, $tableExtension));
    if ($archivedOrNot)
        $tableExtension = "_archive";
    else
        $tableExtension = '';
    $param[3] = explode("~", activityDetails($userid, $fromDate, $tillDate, $tableExtension));

    $datediff = strtotime($tillDate) - strtotime($fromDate);
    $days = floor($datediff / (60 * 60 * 24));
    $noOfMonths = $days / 30;
    $gracePeriod = $gracePeriod / 30;
    $gracePeriod = round($gracePeriod, 1);
    $noOfMonths = round($noOfMonths, 1);
    $totPeriod = $noOfMonths - $gracePeriod;

    $targetPerMonth = array();
    $targetPerMonth['time'] = 28800 * $totPeriod; //in seconds
    $targetPerMonth['question'] = 240 * $totPeriod;
    $targetPerMonth['topics'] = 2 * $totPeriod;
    $targetPerMonth['activities'] = 6 * $totPeriod;
    $targetPerMonth['activitiesTime'] = 1800 * $totPeriod; //in seconds
    $totalTime = $param[0][2];
    if ($totalTime > $targetPerMonth['time']) {
        $totalTime = $targetPerMonth['time'] + ($totalTime - $targetPerMonth['time']) * 0.5;
    }
    $calculationFactot[0] = $totalTime / $targetPerMonth['time'];
    $calculationFactot[0] = min($calculationFactot[0], 3);

    $totalQuestions = $param[1][0];
    if ($totalQuestions > $targetPerMonth['question']) {
        $totalQuestions = $targetPerMonth['question'] + ($totalQuestions - $targetPerMonth['question']) * 0.5;
    }
    $calculationFactot[1] = $totalQuestions / $targetPerMonth['question'];
    $calculationFactot[1] = min($calculationFactot[1], 3);
    if ($param[1][0] != 0)
        $accuracy = $param[1][1] / $param[1][0];
    else
        $accuracy = 0;
    $calculationFactot[2] = $accuracy / 0.75;

    $totalTopics = $param[2][0];
    if ($totalTopics > $targetPerMonth['topics']) {
        $totalTopics = $targetPerMonth['topics'] + ($totalTopics - $targetPerMonth['topics']) * 0.5;
    }
    $calculationFactot[3] = $totalTopics / $targetPerMonth['topics'];
    $calculationFactot[3] = min($calculationFactot[3], 3);
    $calculationFactot[4] = $param[2][1] / 60;

    $totalActivities = $param[3][0];
    if ($totalActivities > $targetPerMonth['activities']) {
        $totalActivities = $targetPerMonth['activities'] + ($totalActivities - $targetPerMonth['activities']) * 1;
    }
    $calculationFactot[5] = $totalActivities / $targetPerMonth['activities'];
    $calculationFactot[5] = min($calculationFactot[5], 3);
    $totalActivitiesTime = $param[3][1];
    if ($totalActivitiesTime > $targetPerMonth['activitiesTime']) {
        $totalActivitiesTime = $targetPerMonth['activitiesTime'] + ($totalActivitiesTime - $targetPerMonth['activitiesTime']) * 1;
    }
    $calculationFactot[6] = $totalActivitiesTime / $targetPerMonth['activitiesTime'];
    $calculationFactot[6] = min($calculationFactot[6], 3);

    $score = $calculationFactot[0] * 45 + $calculationFactot[1] * 0 + $calculationFactot[2] * 0 + $calculationFactot[3] * 0 + $calculationFactot[4] * 50 + $calculationFactot[5] * 5 + $calculationFactot[6] * 0;
    if ($score > 100) {
        $score2 = 100;
    } else
        $score2 = $score;
    $score2 = round($score2, 1);
    $score = round($score, 1);
    $excelArr = array();
    $excelArr = $targetPerMonth . "==" . $calculationFactot . "==" . $param;
    //print_r($userid."~~".$days."~~".$noOfMonths."~~".$gracePeriod);
    //print_r($targetPerMonth);
    //print_r($calculationFactot);
    //print_r($param);
    //print_r("~~".$score2."~~~");
    //exit;
    return $score . "~" . $score2 . "~" . $excelArr;
}

function getTimeSpent($userID, $startDate, $endDate, $tableExtension = "") {

    $noOfSessions = 0;
    $dateArray = array();

    $query = "SELECT DISTINCT sessionID, startTime, endTime FROM adepts_sessionStatus$tableExtension WHERE  userID=" . $userID;

    if ($startDate != "") {
        //$startDate = substr($startDate,6,4)."-".substr($startDate,3,2)."-".substr($startDate,0,2);
        $query .= " AND cast(startTime as date) >='$startDate'";
    }
    if ($endDate != "") {
        //$endDate   = substr($endDate,6,4)."-".substr($endDate,3,2)."-".substr($endDate,0,2);
        $query .= " AND cast(startTime as date) <='$endDate'";
    }
    $time_result = mysql_query($query) or die(mysql_error());

    $timeSpent = 0;
    while ($time_line = mysql_fetch_array($time_result)) {
        $noOfSessions++;
        $dateStr = substr($time_line[1], 0, 10);
        if (!in_array($dateStr, $dateArray))
            array_push($dateArray, $dateStr);
        $startTime = convertToTime($time_line[1]);
        if ($time_line[2] != "")
            $endTime = convertToTime($time_line[2]);
        else {
            if ($tableExtension = "")
                $query = "SELECT max(lastModified) FROM adepts_teacherTopicQuesAttempt$tableExtension WHERE sessionID=" . $time_line[0] . " AND userID=" . $userID;
            else
                $query = "SELECT max(lastModified) FROM adepts_teacherTopicQuesAttempt_archive_201213 WHERE sessionID=" . $time_line[0] . " AND userID=" . $userID;
            $r = mysql_query($query) or die(mysql_error());
            $l = mysql_fetch_array($r);
            if ($l[0] == "")
                continue;
            else
                $endTime = convertToTime($l[0]);
        }
        $timeSpent = $timeSpent + ($endTime - $startTime); //in secs
    }

    //$hours = str_pad(intval($timeSpent/3600),2,"0",STR_PAD_LEFT);	//converting secs to hours.
    //$timeSpent = $timeSpent%3600;
    //$mins  = str_pad(intval($timeSpent/60),2,"0", STR_PAD_LEFT);
    //$timeSpent = $timeSpent%60;
    //$secs  = str_pad($timeSpent,2,"0",STR_PAD_LEFT);

    $daysLoggedIn = count($dateArray);
    return $daysLoggedIn . "~" . $noOfSessions . "~" . $timeSpent;
}

function totalQues($userID, $childClass, $fromdate, $tilldate, $tableExtension = "") {
    if ($tableExtension == "")
        $query = "SELECT count(srno), sum(if(R=1,1,0)) FROM adepts_teacherTopicQuesAttempt$tableExtension" . "_class" . $childClass . " WHERE userID = " . $userID;
    else
        $query = "SELECT count(srno), sum(if(R=1,1,0)) FROM adepts_teacherTopicQuesAttempt$tableExtension" . " WHERE userID = " . $userID . " and childClass=" . $childClass;
    if ($fromdate != "")
        $query .= " AND attemptedDate>='" . $fromdate . "'";
    if ($tilldate != "")
        $query .= " AND attemptedDate<='" . $tilldate . "'";
    $ques_result = mysql_query($query) or die(mysql_error());
    if ($ques_line = mysql_fetch_array($ques_result)) {
        $totalQuest = $ques_line[0];
        $correctques = $ques_line[1];
    }
    return $totalQuest . "~" . $correctques;
}

function noOfTopicandProgress($userID, $fromDate, $tillDate, $tableExtension = "") {
    $query = "SELECT COUNT(a.ttAttemptId),AVG(a.progress) FROM adepts_teacherTopicStatus$tableExtension a WHERE a.userID=$userID AND a.lastModified>='$fromDate' AND a.lastModified<='$tillDate'";
    $ques_result = mysql_query($query) or die(mysql_error());
    if ($ques_line = mysql_fetch_array($ques_result)) {
        $topicAttempted = $ques_line[0];
        $avgTopicProgress = $ques_line[1];
    }
    return $topicAttempted . "~" . $avgTopicProgress;
}

function activityDetails($userID, $fromdate, $tillDate, $tableExtension = "") {

    $query = "select count(distinct gameid), sum(timeTaken) from adepts_userGameDetails$tableExtension where userid=$userID and attemptedDate>='$fromdate' AND attemptedDate<='$tillDate'";
    $ques_result = mysql_query($query) or die(mysql_error());
    if ($ques_line = mysql_fetch_array($ques_result)) {
        $gameCount = $ques_line[0];
        $avgActivityTime = $ques_line[1];
    }
    return $gameCount . "~" . $avgActivityTime;
}

function convertToTime($date) {

    $hr = substr($date, 11, 2);
    $mm = substr($date, 14, 2);
    $ss = substr($date, 17, 2);
    $day = substr($date, 8, 2);
    $mnth = substr($date, 5, 2);
    $yr = substr($date, 0, 4);
    $time = mktime($hr, $mm, $ss, $mnth, $day, $yr);
    return $time;
}
?>


