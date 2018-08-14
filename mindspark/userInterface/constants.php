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
define('VIDEO_THUMB_PATH','http://mindspark-ei.s3.amazonaws.com/videos/');
define('AQAD_URL','http://192.168.0.7/aqad_new.in/');

$buddyName = array("","Geo","Pyro","Addy");
if($_SERVER['SERVER_NAME'] == "programserver")
	$gamesFolder = "/mindspark/TT/Games";
else
	$gamesFolder = "http://d2tl1spkm4qpax.cloudfront.net/Maths_Eng_Games";

define('GAMES_FOLDER', $gamesFolder);

if($_SERVER['SERVER_NAME'] == "programserver")
	$remedialItemFolder = "../../Remedial_Items/";
else
	$remedialItemFolder = "http://mindspark-ei.s3.amazonaws.com/Remedial_Items";
define( 'REMEDIAL_ITEM_FOLDER', $remedialItemFolder);

//define( 'ENRICHMENT_MODULE_FOLDER', "http://d2tl1spkm4qpax.cloudfront.net/Enrichment_Modules");
//define( 'ENRICHMENT_MODULE_FOLDER', "http://mindspark-ei.s3.amazonaws.com/Enrichment_Modules");
if($_SERVER['SERVER_NAME'] == "localhost" || $_SERVER['SERVER_NAME'] == "192.168.0.93")
{
    define( 'ENRICHMENT_MODULE_FOLDER', "http://d2tl1spkm4qpax.cloudfront.net/Enrichment_Modules");
	define( 'ENRICHMENT_MODULE_FOLDER_DEV', "http://mindspark-ei.s3.amazonaws.com/Enrichment_Modules");
}
else
{
    define( 'ENRICHMENT_MODULE_FOLDER', "http://d2tl1spkm4qpax.cloudfront.net/Enrichment_Modules");
	define( 'ENRICHMENT_MODULE_FOLDER_DEV', "http://mindspark-ei.s3.amazonaws.com/Enrichment_Modules");
}
define( 'IMAGES_FOLDER', "http://d2tl1spkm4qpax.cloudfront.net/content_images");
//define( 'IMAGES_FOLDER', "http://localhost/content_images");
define( 'TEACHER_IMAGES_FOLDER', "http://d2tl1spkm4qpax.cloudfront.net/teacher_images");
define( 'IMAGES_FOLDER_S3', "http://mindspark-ei.s3.amazonaws.com/content_images");
define( 'TEACHER_IMAGES_FOLDER_S3', "http://mindspark-ei.s3.amazonaws.com/teacher_images");
define( 'VOICEOVER_FOLDER', "http://d2tl1spkm4qpax.cloudfront.net/voiceovers");
define( 'VOICEOVER_FOLDER_AUTOMATED', "http://d2tl1spkm4qpax.cloudfront.net/voiceovers_automated");
//define( 'NEW_VOICEOVER_FOLDER', "http://d2tl1spkm4qpax.cloudfront.net/newVoiceOvers");
define('BASE_FOLDER','http://mindspark.in/mindspark');
define( 'HTML_QUESTIONS_FOLDER', "http://d2tl1spkm4qpax.cloudfront.net/Enrichment_Modules/html5/questions");
//define( 'HTML_QUESTIONS_FOLDER', "http://localhost/html5/questions");
define('WHATSNEW','http://mindspark-ei.s3.amazonaws.com/');
define('SPARKIE_IMAGE_SOURCE','http://d2tl1spkm4qpax.cloudfront.net/content_images');
define('SPARKIE_IMAGE_SOURCE_S3','http://mindspark-ei.s3.amazonaws.com/content_images');
define('HTML5_COMMON_LIB','http://d2tl1spkm4qpax.cloudfront.net/Enrichment_Modules/html5/libs');
define('HTML5_COMMON_CSS','http://d2tl1spkm4qpax.cloudfront.net/Enrichment_Modules/html5/css');
define('CLOUDFRONTURL','http://d2tl1spkm4qpax.cloudfront.net/');
if($_SERVER['SERVER_NAME'] == "programserver")
	$folder = "/mindspark/TT/mouse_activities";
else
	$folder = "http://d2tl1spkm4qpax.cloudfront.net/mouse_activities";
define( 'MOUSE_ACTIVITY_FOLDER', $folder);
define('MS_DEC_TOPICS',"'TT053','TT146'");
$ip = gethostbyname('mindsparkserver');
if($_SERVER['SERVER_NAME'] == "programserver")
	$localVideoPath = "http://programserver/";
else
	$localVideoPath = "http://".$ip."/videos/";
define( 'LOCAL_VIDEO_PATH', $localVideoPath);
define('rsrPaperFullPath','http://mindspark-ei.s3.amazonaws.com/ResearchPapers/');
define('studentInterviewFullPath','http://mindspark-ei.s3.amazonaws.com/StudentInterviews/');
$exploreZone='http://mindspark-ei.s3.amazonaws.com/exploreZone/';
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
