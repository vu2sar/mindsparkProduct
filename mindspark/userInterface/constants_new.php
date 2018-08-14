<?php
$subjectno = isset($_SESSION['subjectno'])?$_SESSION['subjectno']:2;
$session_class = isset($_SESSION['childClass'])?$_SESSION['childClass']:5;
if($subjectno==3)
{
	$sessionStatusTable  = "adepts_sessionStatus_sc";
	$topicStatusTable    = "adepts_teacherTopicStatus_sc";
	$clusterStatusTable  = "adepts_teacherTopicClusterStatus_sc";
	$quesAttemptTable    = "adepts_teacherTopicQuesAttempt_sc";
	$currentStatusTable  = "adepts_ttUserCurrentStatus_sc";
	$topicRevisionTable  = "adepts_topicRevisionDetails_sc";
    $quesAttemptTable_class = $quesAttemptTable."_class".$session_class;
}
else
{
	$sessionStatusTable  = "adepts_sessionStatus";
	$topicStatusTable    = "adepts_teacherTopicStatus";
	$clusterStatusTable  = "adepts_teacherTopicClusterStatus";
	$quesAttemptTable    = "adepts_teacherTopicQuesAttempt";
	$currentStatusTable  = "adepts_ttUserCurrentStatus";
	$topicRevisionTable  = "adepts_topicRevisionDetails";
	$quesAttemptTable_class = $quesAttemptTable."_class".$session_class;
	$revisionSessionTable = "adepts_revisionSessionDetails";
}
define( 'TBL_SESSION_STATUS', $sessionStatusTable );
define( 'TBL_TOPIC_STATUS', $topicStatusTable );
define( 'TBL_CLUSTER_STATUS', $clusterStatusTable );
define( 'TBL_QUES_ATTEMPT', $quesAttemptTable );
define( 'TBL_CURRENT_STATUS', $currentStatusTable );
define( 'TBL_TOPIC_REVISION', $topicRevisionTable );
define( 'TBL_REVISION_SESSION', $revisionSessionTable);
define( 'SUBJECTNO', $subjectno);
define( 'TBL_QUES_ATTEMPT_CLASS', $quesAttemptTable_class);
define('VIDEO_THUMB_PATH','/mindspark/videos/');
define('AQAD_URL','http://192.168.0.7/aqad_new.in/');

$buddyName = array("","Geo","Pyro","Addy");
if($_SERVER['SERVER_NAME'] == "programserver")
	$gamesFolder = "/mindspark/TT/Games";
else
	$gamesFolder = "/mindspark/Maths_Eng_Games";

define( 'GAMES_FOLDER', $gamesFolder);

if($_SERVER['SERVER_NAME'] == "programserver")
	$remedialItemFolder = "../../Remedial_Items/";
else
	$remedialItemFolder = "/mindspark/Remedial_Items";
define( 'REMEDIAL_ITEM_FOLDER', $remedialItemFolder);

//define( 'ENRICHMENT_MODULE_FOLDER', "/mindspark/Enrichment_Modules");
//define( 'ENRICHMENT_MODULE_FOLDER', "/mindspark/Enrichment_Modules");
if($_SERVER['SERVER_NAME'] == "localhost" || $_SERVER['SERVER_NAME'] == "192.168.0.93")
{
    define( 'ENRICHMENT_MODULE_FOLDER', "http://192.168.0.7");
	define( 'ENRICHMENT_MODULE_FOLDER_DEV', "http://192.168.0.7");
}
else
{
    define( 'ENRICHMENT_MODULE_FOLDER', "/mindspark/Enrichment_Modules");
	define( 'ENRICHMENT_MODULE_FOLDER_DEV', "/mindspark/Enrichment_Modules");
}
define( 'IMAGES_FOLDER', "/mindspark/content_images");
define( 'TEACHER_IMAGES_FOLDER', "/mindspark/teacher_images");
define( 'IMAGES_FOLDER_S3', "/mindspark/content_images");
define( 'TEACHER_IMAGES_FOLDER_S3', "/mindspark/teacher_images");
define( 'VOICEOVER_FOLDER', "/mindspark/voiceovers");
define( 'VOICEOVER_FOLDER_AUTOMATED', "/mindspark/voiceovers_automated");
//define( 'NEW_VOICEOVER_FOLDER', "/mindspark/newVoiceOvers");
define('BASE_FOLDER','http://mindspark.in/mindspark');
define( 'HTML_QUESTIONS_FOLDER', "/mindspark/Enrichment_Modules/html5/questions");
//define( 'HTML_QUESTIONS_FOLDER', "http://192.168.0.7/html5/questions");
define('WHATSNEW','/mindspark/');
define('SPARKIE_IMAGE_SOURCE','/mindspark/content_images');
define('SPARKIE_IMAGE_SOURCE_S3','/mindspark/content_images');
define('HTML5_COMMON_LIB','/mindspark/Enrichment_Modules/html5/libs');
define('HTML5_COMMON_CSS','/mindspark/Enrichment_Modules/html5/css');
define('CLOUDFRONTURL','/mindspark/');
if($_SERVER['SERVER_NAME'] == "programserver")
	$folder = "/mindspark/TT/mouse_activities";
else
	$folder = "/mindspark/mouse_activities";
define( 'MOUSE_ACTIVITY_FOLDER', $folder);
define('MS_DEC_TOPICS',"'TT053','TT146'");
$ip = gethostbyname('mindsparkserver');
if($_SERVER['SERVER_NAME'] == "programserver")
	$localVideoPath = "http://programserver/";
else
	$localVideoPath = "http://".$ip."/videos/";
define( 'LOCAL_VIDEO_PATH', $localVideoPath);
define('rsrPaperFullPath','/mindspark/ResearchPapers/');
define('studentInterviewFullPath','/mindspark/StudentInterviews/');
$exploreZone='/mindspark/exploreZone/';
$questionVideoType = array(1=>"Before Question",2=>"Question Screen",3=>"Display Answer");
$teacherTopicVideoType = array(1=>"Introduction",2=>"Teaching");
$topicVideoType = array(1=>"Misconception",2=>"Introduction",3=>"Teaching");

define('AWS_ACCESS_KEY','AKIAIAB5RJZWCSA6MT6A');
define('AWS_SECRET_KEY','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');
define('SERVER_TYPE','LOCAL');
define('PRACTISE_MODULES_SCORE_FOR_LEVEL_1',40);
define('PRACTISE_MODULES_SCORE_FOR_LEVEL_2',80);
define('PRACTISE_MODULES_SCORE_FOR_LEVEL_3',100);

define('PARENT_CONNECT_PATH','../app/parent/ui/redirect.php');
define('BASE_URL','http://192.168.0.61/');
define('PARENT_CONNECT_UI_URL','http://localhost/mindspark/app/parent/ui/#/parents/home/homeContent');


?>
