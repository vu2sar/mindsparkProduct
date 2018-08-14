<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
  |--------------------------------------------------------------------------
  | File and Directory Modes
  |--------------------------------------------------------------------------
  |
  | These prefs are used when checking and setting modes when working
  | with the file system.  The defaults are fine on servers with proper
  | security, but you may wish (or even need) to change the values in
  | certain environments (Apache running a separate process for each
  | user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
  | always be used to set the mode correctly.
  |
 */

define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);
/*
  |--------------------------------------------------------------------------
  | File Stream Modes
  |--------------------------------------------------------------------------
  |
  | These modes are used when working with fopen()/popen()
  |
 */
define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');
/* End of file constants.php */
/* Location: ./application/config/constants.php */
/* Site Path and Urls */
define('_PATH', substr(dirname(__FILE__), 0, -25));
define('_URL', substr($_SERVER['PHP_SELF'], 0, - (strlen($_SERVER['SCRIPT_FILENAME']) - strlen(_PATH))));
define('SITE_PATH', _PATH . "/");
define('SITE_URL', _URL . "/");
define('WEBSITE_URL', SITE_URL . '/');

define('DOMAIN_URL', 'https://' . $_SERVER['SERVER_NAME']);

define('BASEURL', DOMAIN_URL);
define('gmapkey', '');

define('CONTROLLER_CALL_LIMIT', 1000);
define( 'TBL_NOTICE_BOARD_COMMENTS', 'adepts_noticeBoardComments' );
define( 'TBL_DIAGNOSTIC_QUESTION_ATTEMPT', 'adepts_diagnosticQuestionAttempt' );
define( 'TBL_RESEARCH_QUESTION_ATTEMPT', 'adepts_researchQuesAttempt' );
define( 'SUBJECTNO', 2);
define('VIDEO_THUMB_PATH','https://mindspark-ei.s3.amazonaws.com/videos/');

/*
Controller Related Constants: End
*/

/*
  |--------------------------------------------------------------------------
  | Database tables
  |--------------------------------------------------------------------------
  |
  | These can be extended for different databases/subject 
  | by setting the ACTIVE_DB name if required
  |
 */

//$childClass=$_SESSION['childClass']; 
//$quesAttemptTable="questionAttempt";
//$quesAttemptClassTable=$quesAttemptTable."_class".$childClass;
define ('ACTIVE_DB', 'educatio_adepts');

define( 'TBL_SESSION_STATUS', 'adepts_sessionStatus' );
define( 'TBL_TOPIC_MASTER', 'adepts_teacherTopicMaster');
define( 'TBL_CURRENT_STATUS', 'adepts_ttUserCurrentStatus' );
define( 'TBL_TOPIC_STATUS', 'adepts_teacherTopicStatus');
define( 'TBL_CLUSTER_STATUS', 'adepts_teacherTopicClusterStatus' );
define( 'TBL_QUES_ATTEMPT', 'adepts_teacherTopicQuesAttempt' );
define( 'TBL_TOPIC_REVISION', 'adepts_topicRevisionDetails' );
define( 'TBL_USER_COMMENTS', 'adepts_userComments');
define( 'TBL_USER_COMMENT_DETAILS', 'adepts_userCommentDetails');
define( 'TBL_USER_GAME_DETAILS', 'adepts_userGameDetails');
define( 'TBL_USER_GAME_MASTER', 'adepts_gamesMaster');

define('TBL_QUESTIONS', 'adepts_questions');
define('TBL_DIAGNOSTIC_QUESTIONS', 'adepts_diagnosticQuestionAttempt');
define('TBL_QUESTION_ATTEMPT', 'questionAttempt_class');
define('MS_DEC_TOPICS',"'TT053','TT146'");
define('TBL_ACCESSTOKEN', 'api_accesstoken');
define('TBL_SESSION_EXTEND', 'api_session_extend');
define('currFreeQuestionType','freeques');
//define( 'TBL_QUESTION_ATTEMPT', $quesAttemptClassTable );

define('readingContentTypeConst','Passage_Reading');
define('listeningContentTypeConst','Passage_Conversation');
define('freeQuesContentTypeConst','Free_Question');
define('speakingContentTypeConst','Speaking');


define('contentFlowReading','reading');
define('contentFlowConversation','conversation');
define('contentFlowFreeques','freeques');
define('contentFlowSpeaking','speaking');

define('readingTypeConst',"Textual");
define('readingIllustratedTypeConst',"Illustrated");

define('readingPsgChangeLevelCount','2');
define('listeningPsgChangeLevelCount','2');
define('freeQuesChangeLevelCount','10');
define('gradeScallingConst',3);
define('gradeHigherLimitIncreaseConst',0.9);
define('readingPsgCountConst',5);
define('listeningPsgCountConst',5);
define('freeQuesCountConst',70);
define('freeQuesLoopConst',60);
define('vocabGroupNo',7);
define('readingPsgLevelConst',0.5);
define('listeningPsgLevelConst',1);
define('freeQuesLevelConst',1);
define('currContentTypePsgConst',"passage");
define('currContentTypePsgQuesConst',"passage_ques");
define('currContentTypeFreeQuesConst',"free_ques");
define('currContentTypeSpeakingQuesConst',"speaking");
define('livePassageStatus',7);
define('liveQuestionsStaus',6);
define('readingTypeConst',"Textual");
define('listeningTypeConst',"Conversation");

define('SUCCESS','success');
define('SUCCESS_CALLBACK','success_callback');
define('FAILURE','failure');
define('FAILURE_CALLBACK','failure_callback');
define('ERROR','error');
define('ERROR_CALLBACK','error_callback');
define('INVALID_SESSION','invalid_session');
define('pendingReviewEssayStatus',0);
define('reviewedEssayStatus',1);
define('exhaustionGradeIncrementPerConst',90);
define('exhaustionReadingPsgSetLimitPer',50);
define('exhaustionListeningPsgSetLimitper',50);
define('MinReadPsgsToAvoidExhaustion',20);
define('MinConvPsgsToAvoidExhaustion',30);
define('RemediationConstant',2);
define('RemediationAccuracyConst',50);