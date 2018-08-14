<?php

Class DiagnosticTest extends MY_Controller
{
	function __construct(){
        parent::__construct();
    }
	function index()
	{
		$this->load->view('Language/index.html');
	}

	/**
	 * function role : Get defined flow of contextual questions,non contextual questions ,passages to be given in diagnostic test including current user atttempts in the flow
	 * @return   json object, Diagnostic test flow
	 * 
	 * */

	function getTestFlowData()
	{
		//$userID = $this->session->userdata('userID');

		$this->load->model('Language/diagnostic_model');
		//echo json_encode($this->diagnostic_model->getTestFlowData($userID));
		
		echo json_encode($this->diagnostic_model->getTestFlowData($this->user_id));
	}

	/**
	 * function role : Get user and time spent information
	 * @return   json object, user details, time spendt info
	 * 
	 * */

	function getUserInfo()
	{
		// Static DiagnosticTestID. Change this in future.
		$diagnosticTestID = 1;
		$this->load->model('Language/diagnostic_model');
		//$timeTaken = $this->diagnostic_model->getTimeSpentOnTest($this->session->userdata('userID'),$diagnosticTestID);
		$timeTaken = $this->diagnostic_model->getTimeSpentOnTest($this->user_id,$diagnosticTestID);
		
		$today = date("Ymd");
		if($today==20150330)
			$mode = 'demo';
		else if($today==20150331)
			$mode = 'test';
		else
			$mode = 'report';

		$this->load->model('Language/user_model');

		/*UPDATE OS, IP DETAILS HERE*/
		/*$ip = $_SERVER['HTTP_X_FORWARDED_FOR']?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'];   //Needed since apps behind a load balancer
	   if(strpos($ip,',') !== false) {
	       $ip = substr($ip,0,strpos($ip,','));
	   }
		
	   	$OsDetails =  $this->input->post('osDetails');
		$updateOsDetails = $this->user_model->updateOSIPDetails($this->user_id,$this->session->userdata('sessionID'), $ip, $OsDetails, $this->session->userdata('clientBrowser'));*/
		/*UPDATE OS, IP DETAILS HERE END*/

		//$data = array('userID'=>$this->session->userdata('userID'),'userName'=>$this->session->userdata('userName'),'childName'=>$this->session->userdata('childName'),'timeTaken'=> $timeTaken ,'mode'=>$mode,'msLevel'=>$this->session->userdata('msLevel'),'teacherClass'=>$this->session->userdata('teacherClass'),'totalSessionCount'=>$this->user_model->getTotalSessionsDoneByUser(),'childClass'=>$this->session->userdata('childClass'),'childSection'=>$this->session->userdata('childSection'));
		$name_array = explode(' ',$this->childName);
		$data = array('userID' => $this->user_id, 'userName' => $this->user_name, 'picture' => $this->picture ,'password' => $passwordLength,'DOB' => $this->childDOB,'section' => $this->child_section ,'schoolName' => $this->school_name,'address' => $this->address ,'contactNo' => $this->phone,'parentContactNo' => $this->parent_contact,'childEmail' => $this->child_email,'parentEmail' => $this->parentEmail , 'name' => $this->child_name,'firstName' => $name_array[0] , 'lastName' => $name_array[1] , 'childName'=>$this->child_name,'mode'=>$mode , 'personalInfo' => $this->personal_info ,'teacherClass'=>$this->teacher_class,'timeAllowedPerDay'=>$this->timeAllowedPerDay,'minTimeForClass' => $this->minTimeForClass,'category'=>$this->category,'subcategory'=>$this->sub_category,'childClass' => $this->child_class,'secretQues' => $this->secret_ques , 'secretAns' => $this->secret_ans );
		echo $this->return_response(json_encode($data));
	}

	/**
	 * function role : Get question extra paramaters
	 * param1 : qcode
	 * @return   array, extra parametrs
	 * 
	 * */

	function getQuestionExtraParam($qcode)
	{
		$questionInfo = array();
		$this->load->model('Language/question_model');
		$questionInfo = $this->question_model->getQuestionInfo($qcode);
		echo $this->return_response($questionInfo['question']);
	}

	/**
	 * function role : Get question related information
	 * param1 : qcode
	 * @return   json object, question info
	 * 
	 * */
	
	function getQuestionCompleteInfo($qcode)
	{
		$questionInfo = array();
		$this->load->model('Language/question_model');
		$questionInfo = $this->question_model->getQuestionInfo($qcode);
		echo $this->return_response(json_encode($questionInfo));
	}

	/**
	 * function role : Save question response of user, user and question info passed through POST
	 * @return  none
	 * 
	 * */
	
	function submitResponse()
	{
		$sessionID = $this->session->userdata('sessionID');
		$this->load->model('Language/diagnostic_model');
		//$this->diagnostic_model->saveResponse($_POST['userID'],$_POST['qcode'],$_POST['questionNo'],$_POST['timeTaken'],$_POST['timeTakenExpln'],$_POST['correct'],$sessionID,$_POST['info']['userResponse']);
		$this->diagnostic_model->saveResponse($this->user_id,$_POST['qcode'],$_POST['questionNo'],$_POST['timeTaken'],$_POST['timeTakenExpln'],$_POST['correct'],$this->session_id,$_POST['info']['userResponse']);
		echo $this->return_response();
	}

	/**
	 * function role : Flag/Unflag question for future answering
	 * param1 :flagType
	 * param2 : userID
	 * param3 : qcode
	 * param4 : question no [Appearing in sequence to user]
	 * @return  none
	 * 
	 * */

	//function flagUnFlagQues($flagType,$userID,$qcode,$questionNo)
	function flagUnFlagQues($flagType,$qcode,$questionNo)
	{
		//$sessionID = $this->session->userdata('sessionID');
		$this->load->model('Language/diagnostic_model');
		//$this->diagnostic_model->flagUnFlagQues($flagType,$userID,$qcode,$questionNo,$sessionID);
		$this->diagnostic_model->flagUnFlagQues($flagType,$this->user_id,$qcode,$questionNo,$this->session_id);

		$this->return_response();
	}

	/**
	 * function role : Save passage details attempted by user
	 * param1 : userID
	 * param2 : passageID
	 * param3 : passage part where the user is in currently
	 * param4 : complete flag of the passage attempted by the user (boolean)
	 * @return  none
	 * 
	 * */

	//function savePassageDetails($userID,$passageID,$currentPassagePart,$complete)
	function savePassageDetails($passageID,$currentPassagePart,$complete)
	{
		//$sessionID = $this->session->userdata('sessionID');
		$this->load->model('Language/diagnostic_model');
		//$this->diagnostic_model->savePassageDetails($userID,$passageID,$currentPassagePart,$complete,$sessionID);
		$this->diagnostic_model->savePassageDetails($this->user_id,$passageID,$currentPassagePart,$complete,$this->session_id);

		echo $this->return_response();
	}

	/**
	 * function role : Save essay details attempted by user, update if attempted earlier else insert
	 * @return  none
	 * 
	 * */

	function saveEssayDetails()
	{
		 // $_POST = array('userID' => 11,'essayID'=>1);
		 // $_POST['info']['userResponse'] = 'Testing response 2';
		 // $_POST['status'] = 1;
		//$sessionID = $this->session->userdata('sessionID');
		$this->load->model('Language/diagnostic_model');
		//$this->diagnostic_model->saveEssayDetails($_POST['userID'],$_POST['essayID'],$_POST['info']['userResponse'],$_POST['status'],$sessionID);
		$this->diagnostic_model->saveEssayDetails($this->user_id,$_POST['essayID'],$_POST['info']['userResponse'],$_POST['status'],$this->session_id);
	
		echo $this->return_response();
	}

	/**
	 * function role : Fetch feedback posted by evaluaters on essay attempted by user
	 * param1 : userID
	 * param2 : unique essayID
	 * @return  POST, essay feedback
	 * 
	 * */

	//function fetchEssayFeedback($userID,$essayID)
	function fetchEssayFeedback($essayID)
	{
		$this->load->model('Language/diagnostic_model');
		//$essayFeedback = $this->diagnostic_model->fetchEssayFeedback($userID,$essayID,1);
		// Static parameter $fetchFeedback for fetchEssayFeedback method in model. Check the logic and change if needed.
		$essayFeedback = $this->diagnostic_model->fetchEssayFeedback($this->user_id,$essayID,1);
		
		echo $this->return_response($essayFeedback);
	}

	/**
	 * function role : Update time taken by the user for the diagnostic test
	 * param1 : userID
	 * param2 : total time taken by user in the test
	 * @return  none
	 * 
	 * */


	//function updateTestDetails($userID,$totalTimeTaken)
	function updateTestDetails($totalTimeTaken)
	{
		//$sessionID = $this->session->userdata('sessionID');
		$diagnosticTestID = 1;
		$this->load->model('Language/diagnostic_model');
		//$this->diagnostic_model->updateTestDetails($userID,$totalTimeTaken,$sessionID,$diagnosticTestID);
		$this->diagnostic_model->updateTestDetails($this->user_id,$totalTimeTaken,$this->session_id,$diagnosticTestID);

		echo $this->return_response();
	}

	/**
	 * function role : Insert user comments 
	 * @return  none
	 * 
	 * */

	function insertUserComments()
	{
		//$sessionID = $this->session->userdata('sessionID');
		$this->load->model('Language/diagnostic_model');
		//$this->diagnostic_model->insertUserComments($_POST['userID'],$_POST['qcode'],$sessionID,$_POST['comment'], $_POST['type']);
		$this->diagnostic_model->insertUserComments($this->user_id,$_POST['qcode'],$this->session_id,$_POST['comment'], $_POST['type']);

		echo $this->return_response( null , 'Your comment has been submitted.' , SUCCESS);
	}

	/**
	 * function role : Generate diagnostic test report from all attempted questions in diagnostic test
	 * param1 : schoolCode
	 * @return  none
	 * 
	 * */

	function generateDiagnosticTestReportData($schoolCode)
	{
		$this->load->model('Language/diagnostic_model');
		$this->diagnostic_model->generateDiagnosticTestReportData($schoolCode);
		$this->return_response();
	}

	/**
	 * function role : Retun diagnsotic test attempt statistics
	 * param1 : userID (optional if want for some specific student) for the passed schoolCode
	 * param2 : schoolCode
	 * @return  none
	 * 
	 * */

	//function getDiagnosticTestReportData($userID="",$schoolCode)
	function getDiagnosticTestReportData($schoolCode)
	{
		// Doubtfull call from front end passing only one argument where it will be the userID, and school code is not passed.

		$this->load->model('Language/diagnostic_model');
		//$reportData = $this->diagnostic_model->getDiagnosticTestReportData($userID,$schoolCode);
		$reportData = $this->diagnostic_model->getDiagnosticTestReportData($this->user_id,$schoolCode);
		$this->return_response(json_encode($reportData));
	}

	/**
	 * function role : Save user feedback on the diaagnostic test
	 * @return  none
	 * 
	 * */

	function saveUserFeedback()
	{
		/* Already
		//$_POST['userID'] = 1;
		//$_POST['feedback'][1] = "test1";
		//$_POST['feedback'][2] = "test2";
		//$_POST['feedback'][3] = "test3";
		*/

		//$userID = $this->session->userdata('userID');
		$this->load->model('Language/diagnostic_model');
		//$this->diagnostic_model->saveUserFeedback($_POST['userID'],$_POST['feedback']);
		$this->diagnostic_model->saveUserFeedback($this->user_id,$_POST['feedback']);
		$this->return_response();
	}

	/**
	 * function role : Temporary function created for correcting diagnotic test statistics for Mt. Carmel school teacher test. Will not be needed now.
	 * @return   none
	 * 
	 * */

	function correctReportData()
	{
		$this->load->model('Language/diagnostic_model');
		$this->diagnostic_model->correctReportData();
		$this->return_response();

	}
}

?>