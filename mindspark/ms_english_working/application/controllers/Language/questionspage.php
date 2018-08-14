<?php

Class Questionspage extends MY_Controller
{

	public $passageID;
	public $sparkies = 0;
	public $sparkie_question = 3;
	public $bonus_sparkie_question = 5;
	public $recieved_sparkie = false;

	function __construct()
    {
        parent::__construct();
        $this->load->model('Language/login_model');
        // To check whether current session is active , if not return response with active false and do not go with further execution perticularly for cross browser issues
        if(!$this->login_model->isUserActive( $this->user_id, $this->session_id)){
			echo $this->return_response();
			exit;
		}
    }

	function index()
	{
		$this->load->view('Language/index');
	}

	/**
	 * function role : Call this function every time the users logs in
	 * It will lead to old passage, new passage , more new passage or non contextual questions based on time and attempts
	 * param1 : userID
	 * @return  json object, Redirection to appropriate content according to student's current position
	 * 
	 * */

	//function getStudentPosition($userID)
	function getStudentPosition()
	{
		
		$this->load->model('Language/questionspage_model');
		
		//speaking
		$this->load->model('Language/freeques_model');
		$this->questionspage_model->__construct();
		$studentFlowArr = $this->questionspage_model->getStudentPosition($this->session_id,$this->user_id);
		//$studentFlowArr['isRedirectToEssayWriter'] = $this->questionspage_model->isRedirectToEssayWriter($this->user_id);
		$childClass = $this->session->userdata('childClass');
		$schoolCode = $this->session->userdata('schoolCode');
		$groupSkillID = $this->session->userdata('groupSkillID');
		$schoolBunchingOrder = $this->freeques_model->nextSchoolBunchingOrder($schoolCode,$childClass,$groupSkillID);
		$studentFlowArr['schoolBunchingOrder'] = ($schoolBunchingOrder) ? 1 : 0;
		$this->passageID = $studentFlowArr['qID'];
		//$this->session->set_userdata('skillID', $this->questionspage_model->skillID);
		$this->setSkillID($this->questionspage_model->skillID);
		//$this->session->set_userdata('subSkillID', $this->questionspage_model->subSkillID);
		$this->setSubSkillID($this->questionspage_model->subSkillID);
		echo $this->return_response(json_encode($studentFlowArr));
   }

	/**
	 * function role :Call this function on next button whenever the passage questions is on screen of the user
	 * It will given more new passage or non contextual questions based on time and attempts
	 * param1 : userID
	 * param2 : passageID
	 * @return  json object, Redirection to appropriate content according to student's current position
	 * 
	 * */

	//function getNextPassageQuestion($userID,$passageID)
	function getNextPassageQuestion($passageID)
	{
		$this->load->model('Language/questionspage_model');
		$this->questionspage_model->__construct();
		// $timeSpentToday = $this->questionspage_model->getTimeSpentToday($userID);
		// if($timeSpentToday>=60)
		// 	header('Location: '. 'home');
		
		//$sessionID = $this->session->userdata('sessionID');
		$timeTakenExpln = isset($_POST['timeTakenExpln']) ? $_POST['timeTakenExpln']:0 ;
		$qcode = $_POST['qcode'];
		$this->questionspage_model->saveTimeTakenForExpln($this->user_id,$qcode,$timeTakenExpln,$passageID);
		//$studentFlowArr = $this->questionspage_model->getNextPassageQuestion($sessionID,$userID,$langLevel,$passageID); 
		$studentFlowArr = $this->questionspage_model->getNextPassageQuestion($this->session_id,$this->user_id,$passageID); 
		
		//$this->session->set_userdata('skillID', $this->questionspage_model->skillID);
		$this->setSkillID($this->questionspage_model->skillID);
		//$this->session->set_userdata('subSkillID', $this->questionspage_model->subSkillID);
		$this->setSubSkillID($this->questionspage_model->subSkillID);
		echo $this->return_response(json_encode($studentFlowArr));
	}

	/**
	 * function role :Call this function on next button whenever the non cotextual questions are on screen of the user
	 * It will give more non contextual questions based on time and attempts
	 * param1 : userID
	 * @return  json object, Redirection to appropriate content according to student's current position
	 * 
	 * */

	//function getNextNonContextualQuestions($userID)
	function getNextNonContextualQuestions()
	{
		$this->load->model('Language/questionspage_model');
		$this->questionspage_model->__construct();
		/* Already
		// $timeSpentToday = $this->questionspage_model->getTimeSpentToday($userID);
		// if($timeSpentToday>=60)
		// 	header('Location: '. 'home');
		*/

		//$sessionID = $this->session->userdata('sessionID');
		$timeTakenExpln = isset($_POST['timeTakenExpln']) ? $_POST['timeTakenExpln']:0 ;
		$qcode = $_POST['qcode'];

		//$this->questionspage_model->saveTimeTakenForExpln($userID,$qcode,$timeTakenExpln);
		$this->questionspage_model->saveTimeTakenForExpln($this->user_id,$qcode,$timeTakenExpln);
		//$studentFlowArr = $this->questionspage_model->getNextNonContextualQuestions($sessionID,$userID,$langLevel); 
		$studentFlowArr = $this->questionspage_model->getNextNonContextualQuestions($this->session_id,$this->user_id); 
		//$this->session->set_userdata('skillID', $this->questionspage_model->skillID);
		$this->setSkillID($this->questionspage_model->skillID);		
		//$this->session->set_userdata('subSkillID', $this->questionspage_model->subSkillID);
		$this->setSubSkillID($this->questionspage_model->subSkillID);

		echo $this->return_response(json_encode($studentFlowArr));
	}

	/**
	 * function role : Save passage attempt details for users. 
	 * @return  none
	 * 
	 * */

	function savePassageDetails()
	{
		/* Already
		// $_POST['userID'] = $userID;
		// $_POST['passageID'] = $passageID;
		// $_POST['currentPassagePart'] = $currentPassagePart;
		// $_POST['complete'] = $complete;
		// $_POST['timeSpent'] = $timeSpent;
		*/

		//$sessionID = $this->session->userdata('sessionID');
		//$params = array('langLevel' => $this->session->userdata('langLevel'));
		$this->load->model('Language/questionspage_model');
		$this->questionspage_model->__construct();
		//$this->questionspage_model->savePassageDetails($_POST['userID'] ,$_POST['passageID'] ,$_POST['currentPassagePart'],$_POST['complete'],$_POST['timeTaken'],$sessionID);
		$this->questionspage_model->savePassageDetails($this->user_id ,$_POST['passageID'] ,$_POST['currentPassagePart'],$_POST['complete'],$_POST['timeTaken'],$this->session_id);

		echo $this->return_response();
	}

	/**
	 * function role : Save student speaking responses (mp3 files)
	 * Author :Praneeth
	 * 
	 * */
	function speakingSaveResponse(){
    	$this->load->model('Language/questionspage_model');
		$this->questionspage_model->__construct();
		$rawData =file_get_contents($_FILES["data"]["tmp_name"]);
		$rawData=base64_encode($rawData);	
    	$this->questionspage_model->saveSpeakingResponses($_POST['content'],$this->user_id,$_POST['qcode'],$rawData,$this->session_id,$_POST['questionPart'],$_POST['speakingPercentage'],$_POST['speakingAttemptID']);
	}

	function isSpeakingCompleted(){
		$this->load->model('Language/questionspage_model');
		$this->questionspage_model->__construct();
		$isSpeakingCompleted=$this->questionspage_model->isSpeakingQuesCompleted($this->user_id,$_POST['qcode']);
		echo $this->return_response(json_encode($isSpeakingCompleted));
	} 

	function saveSpeakingAttempt(){
		$this->load->model('Language/questionspage_model');
		$this->questionspage_model->__construct();
		$saveSpeakingAttempt=$this->questionspage_model->saveSpeakingAttempt($this->user_id,$_POST['qcode'],$this->session_id,$_POST['questionPart'],$_POST['totalTimeTaken'],$_POST['completed']);
		echo $this->return_response(json_encode($saveSpeakingAttempt));
	}

	function updateSpeakingQuestionPart(){
		$this->load->model('Language/questionspage_model');
		$this->questionspage_model->__construct();
		$this->questionspage_model->updateSpeakingQuestionPart($_POST["questionPart"],$_POST["speakingAttemptID"]);
	}

	function setSpeakingQuestionCompletedFlag(){
		$this->load->model('Language/questionspage_model');
		$this->questionspage_model->__construct();
		$this->questionspage_model->setSpeakingQuestionCompletedFlag($this->user_id,$_POST["speakingAttemptID"]);
	}
    
	/**
	 * function role : Save question response details for users.
	 * @return  none
	 * 
	 * */

	function saveResponse()
	{
		/*Already
		// $_POST['userID'] = $userID;
		// $_POST['qcode'] = $qcode;
		// $_POST['questionNo'] = $questionNo;
		// $_POST['timeTaken'] = $timeTaken;
		// $_POST['timeTakenExpln'] = $timeTakenExpln;
		// $_POST['correct'] = $correct;
		// $_POST['userResponse'] = $userResponse;
		// $_POST['questionType'] = $questionType;
*/
		//$sessionID = $this->session->userdata('sessionID');
		//$skillID = $this->session->userdata('skillID');
		//$subSkillID = $this->session->userdata('subSkillID');
		
		$this->load->model('Language/questionspage_model');
		$this->questionspage_model->__construct();

		if($_POST['json']['qType'] == "openEnded"){
			$isJunk=$this->getIsJunkData($_POST['userResponse']);
			$isProfanity= $this->getProfanityData($_POST['userResponse']); // my code
			if($isJunk['isJunk']){
				/*log the junk in db*/
				$isJunk['itemID'] = $_POST['qID'];
				$isJunk['page']   = $_POST['page'];
				$isJunk['userID'] = $this->user_id;
				$this->questionspage_model->logJunk($isJunk);
				/*end*/
				$data = array();
				$data['responseIsJunk'] = true;
				$data['responseMsg']="You seem to have written insufficient or incorrect text. Please check what you have written once again.";
				echo $this->return_response(json_encode($data));
				exit;
			}
			elseif($isProfanity)
			{
				$data = array();
				$data['responseIsJunk'] = true;
				$data['responseMsg']="You seem to have used inappropriate language in your response. Please edit it, or write to us if there is no inappropriate language and you are still getting this message";
				echo $this->return_response(json_encode($data));
				exit;
			}
		}
		//echo $_POST['passageID'];
	//	$previousAttemptCount=$this->questionspage_model->getMaxAttemptCountForQuestion($this->user_id,$_POST['passageID'],$_POST['qcode']);
	//	$attemptCount= $previousAttemptCount+1;


		if($_POST['questionType'] == 'passageQues'){
			//$this->questionspage_model->savePassageQuestionsResponse($_POST['userID'],$_POST['qcode'],$_POST['questionNo'],$_POST['timeTaken'],$_POST['timeTakenExpln'],$_POST['correct'],$_POST['userResponse'],$_POST['questionType'],$sessionID);
			$this->questionspage_model->savePassageQuestionsResponse($this->user_id,$_POST['qcode'],$_POST['questionNo'],$_POST['timeTaken'],$_POST['timeTakenExpln'],$_POST['correct'],$_POST['userResponse'],$_POST['questionType'],$this->session_id);
		}
        elseif($_POST['questionType'] == 'speaking')
        {

        	
			//$this->questionspage_model->saveSpeakingResponse($this->user_id,$_POST['qcode'],$_POST['questionNo'],$_POST['timeTaken'],$_POST['timeTakenExpln'],$_POST['correct'],$_POST['userResponse'],$_POST['questionType'],$this->session_id);

			$this->questionspage_model->saveSpeakingResponse($this->user_id);
		
        }
        else{
			//$this->questionspage_model->saveNCQQuestionsResponse($_POST['userID'],$_POST['qcode'],$_POST['questionNo'],$_POST['timeTaken'],$_POST['timeTakenExpln'],$_POST['correct'],$_POST['userResponse'],$_POST['questionType'],$sessionID,$skillID,$subSkillID);
			$this->questionspage_model->saveNCQQuestionsResponse($this->user_id,$_POST['qcode'],$_POST['questionNo'],$_POST['timeTaken'],$_POST['timeTakenExpln'],$_POST['correct'],$_POST['userResponse'],$_POST['questionType'],$this->session_id,$_POST['json']['skillID'],$_POST['json']['subSkillID']);
		}
		/**
		 	Sparkie Loigc
		*/
		$this->updateMySparkieCount($_POST['qcode'],$_POST['json']['credit'],$_POST['correct']);
		/** 
			End Of Sparkie Logic
		*/
		$data = '';			
		if($this->recieved_sparkie)
		{
			$data = "You have received ".$this->sparkies." sparkie(s)"; 
		}

		$return_array = array();
		$return_array['data'] = $data;

		$this->getMySparkie();
		$return_array['total_sparkies'] = $this->sparkies;

		$this->recieved_sparkie = false;
		
		echo $this->return_response(json_encode($return_array));
	}

	/**
	 * function role : Fetch time spent spent by user on current day
	 * param1 : userID
	 * @return  float, time spent
	 * 
	 * */

	//function getTimeSpentToday($userID)
	function getTimeSpentToday()
	{

		// Call this function on next button whenever the non cotextual questions are on screen of the user
		// It will give more non contextual questions based on time and attempts
		$this->load->model('Language/questionspage_model');
		$this->questionspage_model->__construct();
		//echo $this->questionspage_model->getTimeSpentToday($userID);
		$logged_time =  $this->questionspage_model->getTimeSpentToday($this->user_id);

		
		$start_time = strtotime($this->session->userdata('session_start_time'));
		$current_time = strtotime(date('Y-m-d H:i:s'));
		$current_session_time = round(abs($start_time - $current_time) / 60,2);
		
		//echo ($logged_time + $current_session_time);

		echo $this->return_response(json_encode($logged_time + $current_session_time));
	}

	/**
	 * function role : get passage info for a specific passage [Created by Kalpesh]
	 * @return  json object, passage info
	 * 
	 * */
	

	function getPassage(){
		$passageInfo = array();
		$this->load->model('Language/question_model');
		$passageInfo = $this->question_model->getPassageInfo($_POST['passageID']);
		echo $this->return_response(json_encode($passageInfo));	
	}

	/**
	 * function role : Save user content rating information for passages attempted 
	 * @return  none
	 * 
	 * */

	function saveUserRating()
	{

		$contentID         = $this->input->post('contentID');
		$contentType       = $this->input->post('contentType');
		$rating            = $this->input->post('rating');
		$comment           = $this->input->post('comment');
		$ratingReasonOther = $this->input->post('ratingReasonOther');

		$this->saveUserContentRating($contentID,$contentType,$rating,$comment,$ratingReasonOther);
	
		echo $this->return_response();
	}
	
	// my code for Activity rating count Aditya
	function checkActivityRatingCount()
	{
		$contentID         = $this->input->post('contentID');
		$contentType       = $this->input->post('contentType');
		$this->load->model('Language/rating_model');
		echo $this->rating_model->checkUserActivityRatingCount($this->user_id,$contentID,$contentType);
		
	}


	/** 
		Starting 
			Sparkie Logic functions -- Author Rochak
	*/
	function getMySparkie()
	{
		// Load the models needed for this function
		$this->load->model('Language/user_model');
		// End Loading Models

		// Get the sparkies for the current user.
		$this->sparkies = $this->user_model->getSparkiesCount($this->user_id);

	}
	function getMySparkieCount()
	{
		$this->getMySparkie();
		echo $this->return_response(json_encode($this->sparkies));
	}

	function calculateSparkie($qcode, $credit, $correct)
	{
		if($credit == 1)
		{
			if($correct == 1 || $correct == 1.00)
			{
				$this->first_criteria++;
				$this->second_criteria++;

				$this->setSparkieLogicVariables();
				// Set sparkie if to be updated.
				$this->giveSparkie();
			}
			else
			{
				$this->resetSparkieCriteria();	
			}
		}
	}
	function giveSparkie()
	{
		
		if($this->first_criteria == $this->sparkie_question)
		{
			$this->recieved_sparkie = true;
			$this->sparkies = 1;
			// Update the sparkies for the user
			$this->updateSparkieInDatabase();

		}
		else if($this->second_criteria == $this->bonus_sparkie_question)
		{
			$this->recieved_sparkie = true;
			$this->sparkies = 2;
			
			// Update the sparkies for the user
			$this->updateSparkieInDatabase();

			// Reset the session variables to count the sparkie from the same criteria again.
			$this->resetSparkieCriteria();

		}
		else
		{
			$this->recieved_sparkie = false;
		}
	}
	function resetSparkieCriteria()
	{
		$this->first_criteria = 0;
		$this->second_criteria = 0;
		$this->setSparkieLogicVariables();
	}
	function resetSparkieCount()
	{
		$this->sparkies = 0;
	}

	function updateMySparkieCount($qcode, $credit, $correct)
	{
		// Load the models needed for this function
		$this->load->model('Language/user_model');
		
		// Calculate the sparkies.
		$this->calculateSparkie($qcode, $credit, $correct); 

	}
	function updateSparkieInDatabase()
	{
		$this->load->model('Language/user_model');
		$this->user_model->updateSpakieCount($this->user_id, $this->sparkies, $this->child_class);
	}
	/** 
		End
			Sparkie Logic
	*/

	/*function makeWords()
	{

		$data = $_POST['wordToBeUsed'];
		$data = preg_replace('/\s+/', '', $data);
		$url  = $this->config->item('mslanguage_url').'ajax/makewords.php?word='.$data;
		$ch   = curl_init($url);

		curl_setopt($ch, CURLOPT_POST, 1);
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		curl_close($ch);
		echo $this->return_response($response);
	}*/

	/**
	 * function role : Get the total time spent by the user to readh the passage
	 * @return  none
	 * 
	 * */

	function getPassageAttemptTime()
	{
		$totalTimeInfo = array();
		$this->load->model('Language/questionspage_model');
		$passageID         = $this->input->post('passageID');
		$totalTime =  $this->questionspage_model->getPassageAttemptTotalTime($this->user_id, $passageID);
		$totalTimeInfo['totalTime'] = $totalTime[0]['totalTime'];
		echo $this->return_response(json_encode($totalTimeInfo));
		//echo json_encode($totalTimeInfo);
	}
}


?>
