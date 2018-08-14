<?php


Class Home extends MY_Controller
{
	function __construct(){
        parent::__construct();
        $this->load->model('Language/login_model');
		if(! $this->login_model->isUserActive( $this->user_id, $this->session_id) )
		{
			echo $this->return_response(null, '', INVALID_SESSION);
			exit;
		}	
    }
	function index()
	{	
		$this->load->view('Language/index.html');

		// $data['userInfo'] = $this->getUserInfo('kushal.shah');
		// $data['sidebarInfo'] = $this->getSidebarInfo();
		// $data['skillOMeterInfo'] = $this->getSkillOMeterInfo();
	}


	/**
	 * function role : Listing all items of sidebar menu
	 * @return : json object, Side bar menu information
	 * 
	 * */

	function getSidebarInfo() {
		$sideBar = array();
		if(strtolower($this->session->userdata('category'))=='teacher' && strtolower($this->session->userdata('subcategory'))=='school'){
			array_push($sideBar, array('interfaceType' => 'TeacherInterface', 'optionName' => 'Home', 'icon' => "fa fa-home fa-2x")) ;
			array_push($sideBar, array('interfaceType' => 'TeacherInterface', 'optionName' => 'Reports', 'icon' => "fa fa-signal fa-2x")) ;
			array_push($sideBar, array('interfaceType' => 'TeacherInterface', 'optionName' => 'My Students', 'icon' => "fa fa-user fa-2x")) ;
			array_push($sideBar, array('interfaceType' => 'TeacherInterface', 'optionName' => 'Essay Review', 'icon' => "fa fa-pencil fa-2x")) ;
			array_push($sideBar, array('interfaceType' => 'TeacherInterface', 'optionName' => 'Do Mindspark', 'icon' => "fa fa-lightbulb-o fa-2x")) ;
			array_push($sideBar, array('interfaceType' => 'TeacherInterface', 'optionName' => 'Settings', 'icon' => "fa fa-cog fa-2x")) ;
		}else if(strtolower($this->session->userdata('category'))=='school admin' || strtolower($this->session->userdata('subcategory'))=='all'){
			array_push($sideBar, array('interfaceType' => 'TeacherInterface', 'optionName' => 'Home', 'icon' => "fa fa-home fa-2x")) ;
			array_push($sideBar, array('interfaceType' => 'TeacherInterface', 'optionName' => 'Reports', 'icon' => "fa fa-signal fa-2x")) ;
			array_push($sideBar, array('interfaceType' => 'TeacherInterface', 'optionName' => 'My Students', 'icon' => "fa fa-user fa-2x")) ;
			array_push($sideBar, array('interfaceType' => 'TeacherInterface', 'optionName' => 'Do Mindspark', 'icon' => "fa fa-lightbulb-o fa-2x")) ;
			array_push($sideBar, array('interfaceType' => 'TeacherInterface', 'optionName' => 'Settings', 'icon' => "fa fa-cog fa-2x")) ;
		}else if(strtolower($this->session->userdata('category'))=='school admin' || strtolower($this->session->userdata('category'))=='admin'){
			array_push($sideBar, array('interfaceType' => 'TeacherInterface', 'optionName' => 'Home', 'icon' => "fa fa-home fa-2x")) ;
			array_push($sideBar, array('interfaceType' => 'TeacherInterface', 'optionName' => 'Reports', 'icon' => "fa fa-signal fa-2x")) ;
			array_push($sideBar, array('interfaceType' => 'TeacherInterface', 'optionName' => 'My Students', 'icon' => "fa fa-user fa-2x")) ;
			array_push($sideBar, array('interfaceType' => 'TeacherInterface', 'optionName' => 'Do Mindspark', 'icon' => "fa fa-lightbulb-o fa-2x")) ;
		}
		else{
			array_push($sideBar, array('interfaceType' => 'StudentInterface', 'optionName' => 'Home Page', 'icon' => 1)) ;
			array_push($sideBar, array('interfaceType' => 'StudentInterface', 'optionName' => 'The Classroom', 'icon' => 2));
			array_push($sideBar, array('interfaceType' => 'StudentInterface', 'optionName' => 'The Grounds', 'icon' => 3));
			//array_push($sideBar, array('interfaceType' => 'StudentInterface', 'optionName' => 'Trophy Room', 'icon' => 4));
			array_push($sideBar, array('interfaceType' => 'StudentInterface', 'optionName' => 'Essay Writer', 'icon' => 5));
			/*if($this->session->userdata('category')=='School' && ($this->session->userdata('subcategory')=='Teacher' || $this->session->userdata('subcategory')=='Admin'))
				array_push($sideBar, array('interfaceType' => 'StudentInterface', 'optionName' => 'Teacher Report', 'icon' => 6));*/
			// array_push($sideBar, array('interfaceType' => 'StudentInterface', 'optionName' => 'Practice', 'icon' => 6));
		}
		echo $this->return_response(json_encode($sideBar));
		//echo json_encode($sideBar);
	}
    
    /**
     * function role : Get information related to all home page components
     * @param1   userID
     * @return : json object,home page information
     * 
     * */

     function getHomePageInfo(){
        $this->load->model('Language/home_model');
		$this->load->model('Language/questionspage_model');
        $this->questionspage_model->updateUserPassageAttemptStatus($this->user_id);
        $response=$this->home_model->getHomePageInfo($this->user_id);
        //$this->questionspage_model->correctAccLogMismatchCount();
		//$this->questionspage_model->correctExhaustAccLogMismatchCount();
		echo $this->return_response(json_encode($response));
    }
    /**
		Profile related functions
    */
	function updatePassword()
	{
		// Update Password in common user details
		$old_password = $_POST['old'];
		$new_password = $_POST['new'];

		if($old_password == $new_password)
		{
			echo $this->return_response('','New password cannot be same as the old password.',SUCCESS);
			return;
		}

		$this->load->model('Language/user_model');
		$correctOldPassword = $this->user_model->validOldPassword($this->user_id, $old_password);
		if($correctOldPassword)
		{
			$this->user_model->changePassword($this->user_id, $new_password);
			echo $this->return_response('','Password updated successfully.',SUCCESS);
		}
		else
		{	
			if($old_password == '')
			{
				echo $this->return_response('','Please enter the old password.',SUCCESS);
			}
			else
			{
				echo $this->return_response('','Old password entered is incorrect.',SUCCESS);
			}
		}
	}
	function updateDOB()
	{
		// Update Address in common user details
		$this->load->model('Language/user_model');
		$dob = trim($_POST['dob']);
		$this->user_model->updateChildDOBInCommon( $this->user_id , $dob );
		$this->setChildDOB($dob);
		echo $this->return_response('','Date of birth updated successfully.',SUCCESS);	
	}
	function updateAddress()	{
		// Update Address in common user details
		$this->load->model('Language/user_model');
		$address = trim($_POST['address']);
		$this->user_model->updateAddressInCommonUser( $this->user_id , $address );
		$this->setAddress($address);
		echo $this->return_response('','Address updated successfully.',SUCCESS);
	}
	function updatePhoneNo()
	{
		// Update Phone
		$this->load->model('Language/user_model');
		$phone = trim($_POST['phone']);
		$this->user_model->updatePhoneInCommonUser( $this->user_id , $phone );
		$this->setPhoneNo($phone);
		echo $this->return_response('','Phone no updated successfully.',SUCCESS);
	}
	function updateEmail()
	{
		// Update Email
		$this->load->model('Language/user_model');
		$email = trim($_POST['email']);
		$this->user_model->updateEmailInCommonUser( $this->user_id , $email );
		$this->setEmail($email);
		echo $this->return_response('','Email updated successfully.',SUCCESS);
	}
	function updatePersonalInfo()
	{
		// Update Personal Info userDetails
		$this->load->model('Language/user_model');
		$personalInfo = trim($_POST['personalInfo']);
		$this->user_model->updatePersonalInfo( $this->user_id , $personalInfo );
		$this->setPersonalInfo($personalInfo);
		echo $this->return_response('','Personal information saved successfully.',SUCCESS);
	}
	function updateProfilePic($pictureID)
	{
		// Update Profile Pic userDetails
		$this->load->model('Language/user_model');
		$this->user_model->updateProfilePic($this->user_id,$pictureID);
		$this->setProfilePic($pictureID);
		echo $this->return_response('','Profile pic has been updated successfully',SUCCESS);
	}
	function saveSecretQuestion()
	{
		$secretQues = $_POST['secretQues'];
		$secretAns  = $_POST['secretAns'];
		$this->load->model('Language/user_model');
		$this->user_model->updateSecretQuestion( $this->user_id , $secretQues , $secretAns );
		$this->setSecretQues($secretQues);
		$this->setSecretAns($secretAns);
		echo $this->return_response('','Secret question and answer updated successfully.',SUCCESS);
	}

	// -----------------------

	/**
	 * function role : Skill meter generation head function
	 * @param1   userID
	 * @return : json object, skill-o-meter information
	 * 
	 * */

	//function getSkillOMeterInfo($userID) {
	function getSkillOMeterInfo() {
		$this->load->model('Language/home_model');
		//echo json_encode($this->home_model->buldSkillTree($userID) );
		$response=$this->home_model->buldSkillTree($this->user_id);
		echo $this->return_response(json_encode($response));		
	}

	function getNotifications()
	{       
                $this->load->model('Language/home_model');
		$this->load->model('Language/user_model');
		$notifications = array();
		$request_count = $this->user_model->resetPasswordRequestCount($this->user_id);
		$essay_review_count = $this->session->userdata('totalEssayPendingCnt');
		if( $request_count != 0 )
			$notifications['Reset Password Request'] = $request_count;
		if( $essay_review_count != 0)
			$notifications['Essay Review'] = $this->session->userdata('totalEssayPendingCnt');
		
                if($this->category=="STUDENT"){
                    $notifications['essayAssigned']=$this->home_model->getAssignedEssayCount();
                }
                
		echo $this->return_response(json_encode($notifications));
	}
	function getPasswordResetRequest()
	{
		$this->load->model('Language/user_model');
		$request = $this->user_model->resetPasswordRequestUserList($this->user_id);
		echo $this->return_response(json_encode($request));
	}
	function resetUserPassword()
	{
		$id = $_POST['id'];
		$this->load->model('Language/user_model');
		$request = $this->user_model->resetPasswordRequestUserList($this->user_id);
		$this->user_model->resetPassword($id,1);
		$ms_userID = $this->user_model->getMSUserId($request[0]['childUserID']);
		$this->user_model->updateMSPasswordNotification($ms_userID,1);
		$request = $this->user_model->resetPasswordRequestUserList($this->user_id);
		echo $this->return_response(json_encode($request));
	}
	function noActionForUser()
	{
		$id = $_POST['id'];
		$this->load->model('Language/user_model');
		$request = $this->user_model->resetPasswordRequestUserList($this->user_id);
		$this->user_model->resetPassword($id,2);
		$ms_userID = $this->user_model->getMSUserId($request[0]['childUserID']);
		$this->user_model->updateMSPasswordNotification($ms_userID,2);
		$request = $this->user_model->resetPasswordRequestUserList($this->user_id);
		echo $this->return_response(json_encode($request));	
	}
	/**
	 * function role : fetch essay topics based on mslevel of user
	 * @return  topics list
	 * 
	 * */

	//function getEssayTopics($userID,$msLevel)
	function getEssayTopics()
	{
		//$this->output->enable_profiler(TRUE);
		$this->load->model('Language/essay_model');
		//$topicArr = $this->essay_model->getEssayTopics($userID,$msLevel);
		$topicArr = $this->essay_model->getEssayTopics($this->user_id,$this->lang_level);
		echo $this->return_response(json_encode($topicArr));
	}	
	
	/**
	 * function role : Save essay details attempted by user, update if attempted earlier else insert
	 * @return  none
	 * 
	 * */
	function saveUserEssayTopic()
	{
		$essayTitle = $_POST['title'];

		$isJunk=$this->getIsJunkData($essayTitle);
		$isProfanity= $this->getProfanityData($essayTitle);
		if($isJunk['isJunk']){

			/*log the junk in db*/
			$this->load->model('Language/questionspage_model');

			$isJunk['page']   = $_POST['page'];
			$isJunk['userID'] = $this->user_id;
			$this->questionspage_model->logJunk($isJunk);
			/*end*/
			$msg="You do not seem to have entered valid topic.";
			echo $this->return_response('' ,$msg, SUCCESS);
			return;
		}
		elseif($isProfanity)
		{
			$msg="You seem to have used inappropriate language in your response. Please edit it, or write to us if there is no inappropriate language and you are still getting this message";
			echo $this->return_response('' ,$msg, SUCCESS);
			return;
		}

		$this->load->model('Language/essay_model');
		
		$response=$this->essay_model->saveUserTopic( $this->user_id, $essayTitle, $this->lang_level );
		
		if($response['msg'] == 'inlist')
		{
			$tempArr = array('msg' => 'inlist', 'qid' => $response['qid'], 'userResponse' => $response['userResponse'], 'essayTitle'=>$response['essayTitle']);
			echo $this->return_response(json_encode($tempArr));

			/*echo $this->return_response('','This topic is already present in the predefined list, kindly write any other topic ', SUCCESS);
			return;	*/
		}
		elseif($response == 'incomplete')
		{
			/*$tempArr = array('msg' => 'You are allowed to create only 3 custom topics. Please submit one of the incomplete custom topic to create new topic.');
			echo $this->return_response(json_encode($tempArr));*/

			echo $this->return_response('','You are allowed to create only 3 custom topics. Please submit one of the incomplete custom topic to create new topic. ', SUCCESS);
			return;	
		}
		elseif($response['msg'] == 'Already')
		{
			/*$tempArr = array('msg' => 'You are already writting essay on this topic.');
			*/
			$tempArr = array('msg' => 'Already', 'qid' => $response['qid'], 'userResponse' => $response['userResponse'],'essayTitle'=>$response['essayTitle']);
			echo $this->return_response(json_encode($tempArr));

			/*echo $this->return_response('','You are already writting essay on this topic.', SUCCESS);
			return;*/
		}
		else
		{
			//$tempArr = array('msg' => '', 'qid' => $response, 'userResponse' => $response['userResponse']);
			echo $this->return_response(json_encode($response));
		}
	}

	function saveEssayDetails()
	{
		$this->load->model('Language/login_model');
		if(!$this->login_model->isUserActive( $this->user_id, $this->session_id)){
			echo $this->return_response();
			exit;
		}
		
		/*$_POST = array('userID' => 51,'essayID'=>1);
		 $_POST['info']['userResponse'] = 'Anand testing code';
		 $_POST['status'] = 1;*/
		 
		//$sessionID = $this->session->userdata('sessionID');
	
		//$_POST['essayID']=17;
		//$_POST['info']['userResponse']="Tasdba asidbasd isdasd iksbdasd iasbdasd iasbdasd";
		//$_POST['status']=1;
		//$_POST['info']['timeTaken']=105;
		
		$this->load->model('Language/essay_model');
		if($_POST['status']){
			$isJunk=$this->getIsJunkData($_POST['info']['userResponse']);
			$isProfanity= $this->getProfanityData($_POST['info']['userResponse']); // my code
			if($isJunk['isJunk']){

				/*log the junk in db*/
				$this->load->model('Language/questionspage_model');
				$isJunk['itemID'] = $_POST['essayID'];
				$isJunk['page']   = $_POST['page'];
				$isJunk['userID'] = $this->user_id;
				$this->questionspage_model->logJunk($isJunk);
				/*end*/

				$msg="You seem to have written insufficient or incorrect text. Please check what you have written once again.";
				$data = array("isJunk");
				echo $this->return_response($data ,$msg, SUCCESS);
				exit;
			}
			elseif($isProfanity)
			{
				$msg="You seem to have used inappropriate language in your response. Please edit it, or write to us if there is no inappropriate language and you are still getting this message";
				$data = array("isJunk");
				echo $this->return_response($data ,$msg, SUCCESS);
				exit;
			}
		}	
		//$this->essay_model->saveEssayDetails($_POST['userID'],$_POST['essayID'],$_POST['info']['userResponse'],$_POST['status'],$sessionID,$_POST['info']['timeTaken']);
		$response=$this->essay_model->saveEssayDetails($this->user_id,$_POST['essayID'],$_POST['topicID'],$_POST['info']['userResponse'],$_POST['status'],$this->session_id,$_POST['info']['timeTaken']);
		
		if($response->val == SUCCESS)
			echo $this->return_response(null,$response->message,SUCCESS);
		else
			echo $this->return_response(null,$response->message,FAILURE);

	}

	function canSubmitEssay()
	{
		$this->load->model('Language/essay_model');
		$response=$this->essay_model->canSubmitEssay($this->user_id);
		echo $this->return_response(json_encode($response));
	}

	/**
	 * function role : Fetch feedback posted by evaluaters on essay attempted by user
	 * param1 : userID
	 * param2 : unique essayID
	 * @return  POST, essay feedback
	 * 
	 * */

	function fetchEssayFeedback($essayID,$ewsDetailsID)
	{
		//fetchEssayFeedback/54/10140
		$this->load->model('Language/essay_model');
		//$essayFeedback = $this->essay_model->fetchEssayFeedback($userID,$essayID,1);
		$essayFeedback = $this->essay_model->fetchEssayFeedback($this->user_id,$essayID,1,$ewsDetailsID);
		echo $this->return_response(json_decode($essayFeedback));
		//echo $essayFeedback;
	}

	function getEssaySummary()
	{
		$this->load->model('Language/essay_model');
		//$essaySummary = $this->essay_model->fetchEssaySummary($userID);
		$essaySummary = $this->essay_model->fetchEssaySummary($this->user_id);
		echo $this->return_response(json_encode($essaySummary));
	}

	function getEssayResponse($topicID,$essayID)
	{
		$this->load->model('Language/essay_model');
		//$essayResponse = $this->essay_model->fetchEssayResponse($userID,$topicID,$essayID);
		$essayResponse = $this->essay_model->fetchEssayResponse($this->user_id,$topicID,$essayID);
		echo $this->return_response(json_encode($essayResponse));
	}

	function getEssayName($essayID)
	{
		$this->load->model('Language/essay_model');
		$essayName = $this->essay_model->getEssayName($essayID);
		echo $this->return_response($essayName);
	}
    
	function getTimeSpentAtHome()
	{
		$this->load->model('Language/login_model');
		$timeSpent = $this->login_model->getTimeSpentAtHome('week');
		if(date('H')>=15)
			echo $this->return_response($timeSpent."||"."1");
		else
			echo $this->return_response(json_encode("0"."||"."0"));
	}

	function gettimeTakenInClassroom()
	{
		$this->load->library('session');
		$this->load->model('Language/login_model');
		
		$get_result = $this->login_model->getTimeTakenInClassroom($this->session->userdata('userID'));
		echo $this->return_response(json_encode($get_result));
	}

	function logForImgAudioNotLoading()
	{
		$this->load->model('Language/home_model');
		$page     = $_POST['page'];
		$itemId   = $_POST['itemid'];
		$msg      = $_POST['msg'] ;
		$tempArr  = array("page" => $page,"itemId" => $itemId,"msg" =>$msg, "userID" => $this->user_id);
		$response = $this->home_model->logForImgAudioNotLoading($tempArr);
	}
        
        /**
	 * function description : This function will show skill o meter data from home modal.
	 * @return  return reading and accuracy of reading, Listing, grammar & vocab with their accuracy in json
	 * 
	 * */
        function getSkillometer(){
            $this->load->model('Language/home_model');
            $startDate = date( 'Y-m-d', strtotime( 'monday this week' ) );
            $endDate = date('Y-m-d', strtotime( 'sunday this week' ) );
            $response = $this->home_model->getSkillometer($this->user_id,$startDate,$endDate);
            echo $this->return_response(json_encode($response));
        }
	
}

?>