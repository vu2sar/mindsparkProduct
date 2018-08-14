<?php
 	
error_reporting(0);
Class Questionspage_model extends MY_Model
{

	public $previousSkill;
	public $previousSubSkill;
	public $skillID;
	public $subSkillID;
	public $NCQType;
	public $NCQQuesAttemptedInCurrentSkillSubSkill;
	public $maxQuestionsToBeGivenInPassage;
	public $passageAttemptID; 
	public $studentFlowArr;
	public $qcode;
	public $subSubSkillID;
	public $unCompletedPassageArrAdaptive;
	public $currReadingScoreID;
	public $currListeningScoreID;
	public $currFreeQuesScoreID;
	public $currReadingScoreIDPsgArr;
	public $currListeningScoreIDPsgArr;
	public $currFreeQuesScoreIDPsgArr;
	public $currReadingExScoreID;
	public $currListeningExScoreID;
	public $currFreeQuesExScoreID; 


	public function __construct() 
	{
		 parent::__construct();			 
		 $this->dbEnglish = $this->load->database('mindspark_english',TRUE);
		 $this->load->model('Language/user_model');
		 $this->load->model('Language/passage_model');
		 $this->load->model('Language/studentsessionflow_model');
		 $this->load->model('Language/freeques_model');
		 
		// Initializing variables
		 $this->NCQType = 1;
		 $this->maxQuestionsToBeGivenInPassage = 100;
		 $this->studentFlowArr = array();
		 $this->unCompletedPassageArrAdaptive = array();
		 $this->currReadingScoreIDPsgArr = array();
		 $this->currListeningScoreIDPsgArr = array();
		 $this->currFreeQuesScoreIDPsgArr = array();

		 
		 // Get current scoringid for reading,listening and free questions and ex-scoringid if user is in exhaustion
		 $this->currReadingScoreID=$this->getPsgCurrScoringID(readingContentTypeConst);
		 $this->currListeningScoreID=$this->getPsgCurrScoringID(listeningContentTypeConst);
		 $this->currFreeQuesScoreID=$this->getFreeQuesCurrScoringID($this->user_id);
		 $this->currReadingExScoreID=$this->getPsgCurrExScoringID(readingContentTypeConst);
		 $this->currListeningExScoreID=$this->getPsgCurrExScoringID(listeningContentTypeConst);
		 $this->currFreeQuesExScoreID=$this->getFreeQuesCurrExScoringID($this->user_id); 
	
	}

	/**
	 * function role : Call this function every time the users logs in
	 * It will lead to old passage, new passage , more new passage or non contextual questions based on time and attempts
	 * param1 : sessionID
	 * param2 : userID
	 * @return  array, Redirection to appropriate content according to student's current position
	 * 
	 * */

	function getStudentPosition($sessionID,$userID)
	{	
		/*NIVEDITA*/
		
		if($this->category == 'ADMIN' || $this->category == 'TEACHER' || $this->category == 'School Admin')
		{
			if($this->session->userdata('refID') == 0 && $this->session->userdata('currentContentType') == 'N/A')
				$isPsgQnsPending = 0;
			else
			{
				$isPsgQnsPending=$this->lastCompletedPassageQuestionsPending($sessionID,$userID);
			}

		}
		else
		{	

			$isPsgQnsPending=0;
			$isPsgQnsPending=$this->lastCompletedPassageQuestionsPending($sessionID,$userID);
			$cct = $this->session->userdata('currentContentType');
			if(!$isPsgQnsPending && ($cct ==currContentTypePsgQuesConst))      
			{
				$passageIDArr = $this->getQcodePassageDetails($this->session->userdata('refID'));

				$data=array('currentContentType'=>currContentTypePsgConst,'refID'=>$passageIDArr['qcodePassageID']);
				$this->session->set_userdata($data);
				
			}
		}
		/*END*/	
		if($isPsgQnsPending){


			// For handling currentContentType : passage_ques,passage,free_ques and refID : 0 issue 
			//$sessionTypeToShow=$this->studentsessionflow_model->getSessionTypeToShow($userID);

			//  Write here contentflow order codes 


			if($this->session->userdata('refID')==0 && $this->session->userdata('currentContentType') !='N/A' || $this->session->userdata('currentContentType')==currContentTypeFreeQuesConst){
				// below function will set sessionPassages or free question array next to load depending on the session type
				
				$sessionPassagesArr = $this->session->userdata('sessionPassages');
				$data = array('currentContentType'=>currContentTypePsgConst,'refID'=>$sessionPassagesArr[0],'completed'=>0);
				$this->session->set_userdata($data);
				$this->dbEnglish->where('userID',$userID);
				$this->dbEnglish->update('userCurrentStatus',$data);
			}
				$cct_type = $this->session->userdata('currentContentType');

				$cct_type=($cct_type==currContentTypePsgQuesConst) ? currContentTypePsgConst : $cct_type;
				$this->getsessionFlowData($sessionID,$userID,null,$cct_type);
		}
		else{

			$checkContentFlowOrder = $this->studentsessionflow_model->checkContentFlowOrder($this->session->userdata('userID'));
			// below function will set sessionPassages or free question array next to load depending on the session type
			if($this->session->userdata('currentContentType')==currContentTypePsgConst && !$this->session->userdata('refID')):
				$sessionTypeToShow=$this->studentsessionflow_model->getNextContentType($userID);
				$this->session->set_userdata('sessionTypeToShow',$sessionTypeToShow);
				$this->getsessionFlowData($sessionID,$userID,$sessionTypeToShow);
			endif;

			if($this->session->userdata('isIntroductionToBeGiven') != 1){
				$sessionTypeToShow=$this->studentsessionflow_model->getNextContentType($userID);
			}

			if($sessionTypeToShow == "FreeQuestion" || $sessionTypeToShow =="Speaking" || $sessionTypeToShow == "Passage" || $this->session->userdata('isIntroductionToBeGiven') == 1){
				$this->getsessionFlowData($sessionID,$userID,null,$sessionTypeToShow);
			} else {
				$this->studentFlowArr['schoolBunchingOrder'] = 0;
				
			}
		}
		$this->studentFlowArr['isContentExhaustedTeacher'] = $this->session->userdata('isContentExhaustedTeacher');
		/*end*/
		return  $this->studentFlowArr;
	}

	/**
	 * function role : On next button click of passage question this function will be called and will bring next passage question 
	 * param1 : sessionID
	 * param2 : userID
	 * param3 : passageID , passageID for which next question to bring
	 * @return  array, of passage question qcode and qtype and other details
	 * 
	 * */

	function getNextPassageQuestion($sessionID,$userID,$passageID)
	{

		if($this->category == 'ADMIN' || $this->category == 'TEACHER' || $this->category == 'School Admin')
		{
			if($this->session->userdata('currentContentType') != 'N/A' && ($this->session->userdata('refID') == 0 || $this->session->userdata('refID') == ''))
			{
				$this->studentFlowArr['isContentExhaustedTeacher'] = $this->session->userdata('isContentExhaustedTeacher');
				return  $this->studentFlowArr;
			}
			else
			{
				$isPsgQnsPending=0;		
				$isPsgQnsPending=$this->lastCompletedPassageQuestionsPending($sessionID,$userID);

				if($isPsgQnsPending){
					$this->getsessionFlowData($sessionID,$userID,$passageID,"Passage");		
				}
				else{
					
					$sessionTypeToShow=$this->studentsessionflow_model->getNextContentType($userID);

					
					$this->getsessionFlowData($sessionID,$userID,$passageID,$sessionTypeToShow);		
				}
				
				$this->studentFlowArr['isContentExhaustedTeacher'] = $this->session->userdata('isContentExhaustedTeacher');
				return  $this->studentFlowArr;
			}
		}
		else
		{
			$isPsgQnsPending=0;	
			$isPsgQnsPending=$this->lastCompletedPassageQuestionsPending($sessionID,$userID);
			
			if($isPsgQnsPending){
				$this->getsessionFlowData($sessionID,$userID,$passageID,"Passage");
			}
			else{
				$checkContentFlowOrder = $this->studentsessionflow_model->checkContentFlowOrder($this->session->userdata('userID'));
				$sessionTypeToShow=$this->studentsessionflow_model->getNextContentType($userID);
				$this->session->set_userdata('sessionTypeToShow',$sessionTypeToShow);
				$this->getsessionFlowData($sessionID,$userID,$sessionTypeToShow);
			}
			if($this->session->userdata('currentContentType')==currContentTypePsgConst && $this->session->userdata('refID')==0):
				$sessionTypeToShow=$this->studentsessionflow_model->getNextContentType($userID);
				$this->session->set_userdata('sessionTypeToShow',$sessionTypeToShow);
				$this->getsessionFlowData($sessionID,$userID,$sessionTypeToShow);
			endif;
			//
			$this->studentFlowArr['isContentExhaustedTeacher'] = $this->session->userdata('isContentExhaustedTeacher');
			return  $this->studentFlowArr;
		}
	}

	/**
	 * function role : To get totalAttempt if exists otherwise null for free questions.
	 * return : bunchID for free questions , null if not exists
	 **/

 	function getFreeQuesCurrTotalAttempt(){
		$userID = $this->session->userdata('userID');
		$this->dbEnglish->Select('totalAttempts');
		$this->dbEnglish->from('userContentAttemptDetails');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('contentType',$this->session->userdata('currentContentType'));
		$query = $this->dbEnglish->get();
		if($query->num_rows() > 0){
			return $query->row()->totalAttempts;
		}
		else
		{
			return null;
		}
	}

	/**
	 * function role :Call this function on next button whenever the non cotextual questions are on screen of the user
	 * It will give more non contextual questions based on time and attempts
	 * param1 : sessionID
	 * param2 : userID
	 * @return  array , Free question qcode,qType and other details 
	 * 
	 * */


	function getNextNonContextualQuestions($sessionID,$userID)
	{
		$sessionTypeToShow = "";
		$checkContentFlowOrder = $this->studentsessionflow_model->checkContentFlowOrder($this->session->userdata('userID'));
		if($this->session->userdata('sessionfreeQues') == 0){
		// 	exhaustion-freequestion
			$this->session->unset_userdata('sessionfreeQues');
			$this->session->unset_userdata('schoolBunchingOrder');
			$sessionTypeToShow=$this->studentsessionflow_model->getNextContentType($userID);
			$this->session->set_userdata('sessionTypeToShow',$sessionTypeToShow);
		}
		$this->getsessionFlowData($sessionID,$userID,$sessionTypeToShow);

		$this->studentFlowArr['isContentExhaustedTeacher'] = $this->session->userdata('isContentExhaustedTeacher');
		return  $this->studentFlowArr;
	}


	/**
	 * function role : This function fills the return array data as per the session type and content type[Core function which handles the content giving logic] 
	 * param1 : sessionID
	 * param2 : userID
	 * param3 : passageID , passageID for which next question to bring
     * sessionTypeToShow : session type will be session type going on as per curr date's attempt  
	 * @return  array, of content type ID with it's content type and other information
	 * 
	 * */
	
	function getsessionFlowData($sessionID,$userID,$passageID=null,$sessionTypeToShow="")
	{

		if($this->category == 'ADMIN' || $this->category == 'TEACHER' || $this->category == 'School Admin')
		{
			if($this->session->userdata('isContentExhaustedTeacher') == 1)
				return;
		}
		
		$this->studentFlowArr = array();
		$timeSpentToday = 0;
		$currentContentType=$this->session->userdata('currentContentType');
		$sessionPsgTimeLimit=$this->session->userdata('sessionPsgTimeLimit');
		$sessionTypeToShowSession = $this->session->userdata('sessionTypeToShow');

		if(strtolower($sessionTypeToShow) == currContentTypePsgConst || strtolower($sessionTypeToShowSession) == currContentTypePsgConst )
		{	

			if($currentContentType != "N/A"){
				if($currentContentType == currContentTypePsgConst || strtolower($currentContentType) == currContentTypePsgConst){
					$passageID = $this->session->userdata('refID');
					$this->setCurrentPsgQuestions($userID,$passageID,true);
					if(count($this->session->userdata('currentPsgQuestions')) == 0){
						$this->getNextPassage($userID,$sessionID,$passageID,$currentContentType);
						$passageID = $this->session->userdata('refID');
						$this->setCurrentPsgQuestions($userID,$passageID,true);
					}

					if(!$this->session->userdata('completed')){
						$currentContentType=$this->getIncompletedPassage($userID,$passageID);
					}else{
						$currentContentType=$this->getPassageQuestion($userID,$passageID);
					}
				}
				elseif($currentContentType == currContentTypePsgQuesConst || strtolower($currentContentType) == currContentTypePsgQuesConst){
					// if there is user current status is passage question then load all passage questions of that question passage in session 
					if(!$this->session->userdata('currentPsgQuestions'))
						$this->setCurrentPsgQuestions($userID,null,true);
				
					if(!$this->session->userdata('completed'))
						$unAttemptedQcode = $this->session->userdata('refID');
					else{
						$currentPsgQuestions = 0;
						$currentPsgQuestions = $this->session->userdata('currentPsgQuestions');
						$unAttemptedQcode = $currentPsgQuestions[0];
					}
					
					$this->studentFlowArr['qType'] =  'passageQues';
					$this->studentFlowArr['qID'] = $unAttemptedQcode;
					$this->studentFlowArr['info']['passagePartNo']  = null;
					$this->studentFlowArr['info']['passageType'] = null;
					
					// if all the questions of the passage are attempted load next passage
					if(count($this->session->userdata('currentPsgQuestions')) == 0){
						$this->getNextPassage($userID,$sessionID,$passageID,$currentContentType);
					}

					$data = array('completed' => 0);
					$this->dbEnglish->where('userID', $userID);
					$this->dbEnglish->update('userCurrentStatus', $data);
				}
				else{
					
					$currentContentType=$this->getfirstSessionPassage($userID);
					$data = array('completed' => 0);
					$this->dbEnglish->where('userID', $userID);
					$this->dbEnglish->update('userCurrentStatus', $data);
					$this->session->set_userdata($data);
				}
			}else
			{
				// if there is no user current status (the user logges in first time then load first ever session passage )
				$currentContentType=$this->getfirstSessionPassage($userID);
				//  Content logic :  new user load first order function 
			}

			$studentFlowArr=$this->studentFlowArr;		
			$data = array('currentContentType' => $currentContentType,'refID' => $studentFlowArr['qID']);
			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->update('userCurrentStatus', $data);
			$this->session->set_userdata('currentContentType',$currentContentType);
		}
		elseif(strtolower($sessionTypeToShow) == currContentTypeSpeakingQuesConst || strtolower($sessionTypeToShowSession) == currContentTypeSpeakingQuesConst){	

			if(count($this->session->userdata('sessionSpeakingQues')) == 0){
				$this->speaking_model->setSpeakingQuesData($userID);
			}
			if($currentContentType != "N/A"){	
				if(!$this->session->userdata('completed')){
					$unAttemptedQcode = $this->session->userdata('refID');		
					$this->studentFlowArr['qType'] =  currContentTypeSpeakingQuesConst;
					$this->studentFlowArr['qID'] = $unAttemptedQcode;
					$this->studentFlowArr['info']['passagePartNo']  = null;
					$this->studentFlowArr['info']['passageType'] = null;
				}else{
					$this->getNextSpeakingQuestionFn($userID,$sessionID);
				}
			}
			else{
				$this->getNextSpeakingQuestionFn($userID,$sessionID);
			}
			$refID=$this->studentFlowArr['qID'];		
			$data = array('currentContentType' => $currentContentType,'refID' => $refID);
			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->update('userCurrentStatus', $data);
			$this->session->set_userdata($data);
		}
		else
		{
			if(count($this->session->userdata('sessionfreeQues')) == 0){
				//exhaustion-freequestion
				$this->freeques_model->setNextSessionFreeQuestions($userID);
			}

			if($currentContentType != "N/A"){
				if(!$this->session->userdata('completed')){
					$unAttemptedQcode = $this->session->userdata('refID');
					
					$this->studentFlowArr['qType'] =  'freeQues';
					$this->studentFlowArr['qID'] = $unAttemptedQcode;
					$this->studentFlowArr['info']['passagePartNo']  = null;
					$this->studentFlowArr['info']['passageType'] = null;
				}else{
					$this->getNextFreeQuestionFn($userID,$sessionID);
				}
			}
			else{
				$this->getNextFreeQuestionFn($userID,$sessionID);
			}

			$refID=$this->studentFlowArr['qID'];
			//GRE
			$data = array('currentContentType' => $currentContentType,'refID' => $refID);
			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->update('userCurrentStatus', $data);
			$data['isIntroductionToBeGiven'] = $isIntroductionToBeGiven;
			$this->session->set_userdata($data);
		}
	}
	
	/**
	 * function role : This function fills the return array data with the incomplete passage information
	 * param1 : userID
	 * param2 : passageID , passageID for which next question to bring
     * @return : string , content type as "Passage"
	 * 
	 * */
	
	function getIncompletedPassage($userID,$passageID){
		$currentPassagePart=null;		
		$this->dbEnglish->Select('passageID,currentPassagePart');
		$this->dbEnglish->from('passageAttempt');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('passageID',$passageID);
		$this->dbEnglish->order_by('lastModified','desc');
		$this->dbEnglish->limit(1);
		$query = $this->dbEnglish->get();
		$unAttemptedPassage = $query->result_array();		
		if($query->num_rows() > 0)		
			$currentPassagePart=$unAttemptedPassage[0]['currentPassagePart'];		
		
		$this->studentFlowArr['qType'] =  currContentTypePsgConst;
		$this->studentFlowArr['qID'] = $passageID;
		$this->studentFlowArr['info']['passagePartNo']  = $currentPassagePart;
		$this->studentFlowArr['info']['passageType']  = $this->getPassageType($passageID);
	
		$currentContentType=currContentTypePsgConst;
		$data = array('currentContentType' => $currentContentType,'refID' => $passageID);
		$this->session->set_userdata($data);
		return $currentContentType;
	}
	
	/**
	 * function role : This function fills the return array with the ongoing passage's unattempted question information and sets it in session and userCurrentStatus
	 * param1 : userID
	 * param2 : passageID , passageID for which next question to bring
     * @return  string, of content type "passage_ques"
	 * 
	 * */
	
	function getPassageQuestion($userID,$passageID)
	{	
		$currentPsgQuestions = $this->session->userdata('currentPsgQuestions');
		$qcode = $currentPsgQuestions[0];	
		
		$this->studentFlowArr['qType'] =  'passageQues';
		$this->studentFlowArr['qID'] = $qcode;
		$this->studentFlowArr['info']['passagePartNo']  = null;
		$this->studentFlowArr['info']['passageType'] = null;
		$currentContentType=currContentTypePsgQuesConst;
		
		$data = array('completed' => 0,'refID' => $qcode);
		$this->session->set_userdata($data);
		
		$data=array('completed'=>'0');
		$this->dbEnglish->where('userID', $userID);
		$this->dbEnglish->update('userCurrentStatus',$data);
		return $currentContentType;
	}
	
	/**
	 * function role : This function fills the return array with the first passage's pasasgeID from session passages variable along and other details
	 * param1 : userID
	 * @return  string, of content type "passage"
	 * 
	 * */


	// Content Logic : Change the function content flow order.

	function getFirstSessionPassage($userID){
		
		$sessionPassages = $this->session->userdata('sessionPassages');
		
		$passageID = $sessionPassages[0];	
		
		$this->setCurrentPsgQuestions($userID,$passageID,true);
		
		$this->studentFlowArr['qType'] =  currContentTypePsgConst;
		$this->studentFlowArr['qID'] = $passageID;
		$this->studentFlowArr['info']['passagePartNo']  = null;
		$this->studentFlowArr['info']['passageType']  = $this->getPassageType($passageID);
		
		$currentContentType=currContentTypePsgConst;
		$data = array('currentContentType' => $currentContentType,'refID' => $passageID);
		$this->session->set_userdata($data);
		return $currentContentType;
	}
	
	/**
	 * function role : This function sets the session passage questions variable for the ongoing passage.
	 * param1 : userID
	 * param2 : passageID
	 * param3 : check attempted question flag , if set it will filter out the attempted questions from return array
	 * @return  none
	 * 
	 * */
	
	function setCurrentPsgQuestions($userID,$passageID=false,$chkAttemptedQues=false){
		if(!$passageID){
			$this->dbEnglish->Select('passageID');
			$this->dbEnglish->from('questions');
			$this->dbEnglish->where('qcode',$this->session->userdata('refID'));
			$this->dbEnglish->where('status',liveQuestionsStaus);
			$getPassageIDSql = $this->dbEnglish->get();
			$psgQuestionsArr = $getPassageIDSql->row();
			$passageID=$psgQuestionsArr->passageID;
		}

		$attemptedPassageQuestionsArr = array(0=>'');
		if($chkAttemptedQues){
			$attemptedPassageQuestionsArr = $this->getUserAttemptedPassageQuestions($userID,$passageID);
			if($attemptedPassageQuestionsArr!="")
				$attemptedPassageQuestionsArr = explode(',',$attemptedPassageQuestionsArr);
		}
		$this->dbEnglish->_protect_identifiers = FALSE;
		$this->dbEnglish->Select('qcode');
		$this->dbEnglish->from('questions');
		$this->dbEnglish->where('passageID',$passageID);
		$this->dbEnglish->where('status',liveQuestionsStaus);
		$this->dbEnglish->where_not_in('qcode',$attemptedPassageQuestionsArr);
		$this->dbEnglish->order_by("FIELD(qType,'openEnded')");
		$getPsgQuestionsSql = $this->dbEnglish->get();
		$psgQuestionsRes = $getPsgQuestionsSql->result_array();
		$this->dbEnglish->_protect_identifiers = TRUE;
		$psgQuestionsArr=array();
		
		foreach($psgQuestionsRes as $key=>$value)
		{
			array_push($psgQuestionsArr,$value['qcode']);
		}
		$this->session->set_userdata('currentPsgQuestions',$psgQuestionsArr);
		
	}
	
	/**
	 * function role : Check if last attempted passage has any questions pending
	 * param1 : sessionID
	 * param2 : userID
	 * param3 : PassageID , if passed consider the passed passage ID
	 * param4 : chkForAdaptive , it will be true by default, so consider for exhaustion conditions but if passed false then do not consider exhuastion condition 
	 * @return  boolean , 1=>pending, 0=>Not pending
	 * 
	 * */


	/**
	 * function role : Gets the next passage from the session passages array and sets it in the return array with other details
	 * param1 : userID
	 * param2 : sessionID
	 * param3 : PassageID of on going passage
	 * param4 : currentContentType , which is need to be set in userCurrentStatus and session variable  
	 * @return  none
	 * 
	 * */

	function speakingCompletedDetails($userID){
		if(($this->session->userdata('currentContentType')=='speaking') && ($this->session->userdata('completed')==0) && $this->session->userdata('refID')!=0) :
			return $this->session->userdata('refID');
		else:
			return false;
		endif;
	}

	function conversationCompletedPassage ($userID) {

		/** Get the the contentFlow sesssion and compare the data **/
		if(!$this->session->userdata('remediationStatus')):
			$remediationPosition = $this->session->userdata('remediationPosition');
			$totalAttempts = $this->session->userdata('totalConversationAttempts');
			$contentAttemptCount = $this->session->userdata('contentAttemptCount');
			if($remediationPosition!="" && $remediationPosition ==($totalAttempts+2+RemediationConstant)):
				$remediationstatus=1;
			$this->session->set_userdata('remediationStatus',$remediationstatus);
			elseif($remediationPosition!="" && $remediationPosition ==($totalAttempts+1+RemediationConstant)):
				$remediationstatus=0;
			$this->session->set_userdata('remediationStatus',$remediationstatus);
				
			endif;
			
			if(($this->session->userdata('currentContentType')==currContentTypePsgConst) && ($this->session->userdata('completed')==0) && $this->session->userdata('refID')!=0) :
				return $this->session->userdata('refID');
			else:
				return false;
			endif;
		else:
				return false;
		endif;
	}



	/**
	 * function role : Function check the conversation passage have remediation mode based on next remediation status.
	 * param1 : userID
	 * @return  if current content status incomplete it will return $passID or False 
	 * 
	 * */





	function lastCompletedPassageQuestionsPending($sessionID,$userID,$passageID=false,$chkForAdaptive=true)
	{	

		//Check Content flow Model Conversation completed or not 
		$presentContentType  = $this->session->userdata('presentContentType');
		$currentContentType  = $this->session->userdata('currentContentType');
		if($presentContentType==contentFlowSpeaking) : 
			$passageID = $this->speakingCompletedDetails($userID);
			if($passageID):
				return 1;
			else:
				return 0;
			endif;
		elseif($presentContentType==contentFlowConversation) : 
			$passageID = $this->conversationCompletedPassage($userID);
		          $completed  = $this->session->userdata('completed');
		elseif(($this->session->userdata('currentContentType')==currContentTypePsgConst) && ($this->session->userdata('completed')==0) && $this->session->userdata('refID')!=0) :
				$passageID = $this->session->userdata('refID');
			$completed  = $this->session->userdata('completed');
		endif;
		if(!$passageID)				
		{
			/*nivedita*/
			if($this->category == 'ADMIN' || $this->category == 'TEACHER' || $this->category == 'School Admin')
			{

					$this->dbEnglish->from('passageAttempt');
					$this->dbEnglish->where('userID',$userID);
					$this->dbEnglish->order_by('lastModified','desc');
					$this->dbEnglish->limit(1);
					$query = $this->dbEnglish->get();
					$lastAttemptedPassageInfo = $query->result_array();
					if(count($lastAttemptedPassageInfo)==0)
						return 0;
					$passageID = $lastAttemptedPassageInfo[0]['passageID'];
					$completed= $lastAttemptedPassageInfo[0]['completed'];
				
			}//end
			else
			{
				

				$this->dbEnglish->Select('passageID,completed');
				$this->dbEnglish->from('passageAttempt');
				$this->dbEnglish->where('userID',$userID);
				$this->dbEnglish->order_by('lastModified','desc');
				$this->dbEnglish->limit(1);
				$query = $this->dbEnglish->get();
				$lastAttemptedPassageInfo = $query->result_array();
				if(count($lastAttemptedPassageInfo)==0)
					return 0;
				$passageID = $lastAttemptedPassageInfo[0]['passageID'];
				$completed= $lastAttemptedPassageInfo[0]['completed'];
			}
		}


		$attemptCount=$this->getPassagePreviousAttemptCount($userID,$passageID);

		//added by sannidhi
                     if($presentContentType==contentFlowConversation && $currentContentType ==currContentTypePsgConst && $completed==0):
			$presentAttemptCount=(isset($attemptCount['attemptCount']))? $attemptCount['attemptCount']+1:1;
		elseif ($presentContentType==contentFlowReading && $currentContentType ==currContentTypePsgConst):
			$presentAttemptCount=(isset($attemptCount['attemptCount'])) ? $attemptCount['attemptCount']:''
		;		
		elseif($presentContentType==contentFlowConversation && $currentContentType ==currContentTypePsgConst && $completed==2):
			$presentAttemptCount=$attemptCount['attemptCount'];
		elseif($currentContentType==currContentTypePsgConst && isset($attemptCount)):
			$presentAttemptCount = $attemptCount['attemptCount'];
		elseif($currentContentType ==currContentTypePsgQuesConst):
			$presentAttemptCount=$attemptCount['attemptCount'];
		endif;

		$psgContentType=$this->getPassageType($passageID);

		$this->dbEnglish->Select('group_concat(distinct(qcode)) quesAttemptSrno');
		$this->dbEnglish->from($this->questionAttemptClassTbl);
		$this->dbEnglish->where('userID',$userID);
		if($presentAttemptCount) :
			$this->dbEnglish->where('attemptCount',$presentAttemptCount);
		endif;
		$this->dbEnglish->where('passageID',$passageID);
		
		$query = $this->dbEnglish->get();
		$lastAttemptedPassageQuesInfo = $query->result_array();


		$quesAttemptSrno = $lastAttemptedPassageQuesInfo[0]['quesAttemptSrno'];
		$this->dbEnglish->Select('count(qcode) as qcodes');
		$this->dbEnglish->from('questions');
		$this->dbEnglish->where('passageID',$passageID);
		$this->dbEnglish->where('status',liveQuestionsStaus);
		$query = $this->dbEnglish->get();
		
		$passageTotalQues = $query->result_array();

		if($quesAttemptSrno == null || $quesAttemptSrno == "")
			$noOfQuesDoneInPassage = 0;
		else
			$noOfQuesDoneInPassage = count(explode(',', $quesAttemptSrno));
		

		if($noOfQuesDoneInPassage < $this->maxQuestionsToBeGivenInPassage  && $noOfQuesDoneInPassage < $passageTotalQues[0]['qcodes'])
		{
			return 1;
		}	
		else
		{
			return 0;
		}
	}
	
	function getNextPassage($userID,$sessionID,$passageID,$currentContentType){
		$sessionPassages = $this->session->userdata('sessionPassages');
		if(count($sessionPassages) == 0)
		{
			$this->getNextNonContextualQuestions($sessionID,$userID); //illegal coding error
			return $this->studentFlowArr;
			exit;
		}	
		$index = array_search($passageID, $sessionPassages);
		if($index):
			unset($sessionPassages[$index]);
		endif;
		$sessionPassages = array_values($sessionPassages);
		$this->session->set_userdata('sessionPassages', $sessionPassages);
		
		$this->studentFlowArr['qType'] =  currContentTypePsgConst;
		$this->studentFlowArr['qID'] = $sessionPassages[0];
		$this->studentFlowArr['info']['passagePartNo']  = null;
		$this->studentFlowArr['info']['passageType'] = null;
		
		$data = array('currentContentType' => $currentContentType,'refID' => $sessionPassages[0],'completed'=>0); // temperory change here aditya

		$this->dbEnglish->where('userID', $userID);
		$this->dbEnglish->update('userCurrentStatus', $data);
		$this->session->set_userdata($data);
		return;
	}
	
	/**
	 * function role : returns the passages from passage attempt which are completely attempted.
	 * param1 : userID
	 * param2 : isContentExhausted
	 * @return  array , if passages completely attempted or empty string if not any
	 * 
	 * */
	
	function getUserAttemptedPassage($userID,$isContentExhausted=false)   // changed here 
	{
		$allQuesCompletedPsgArr=array();
		$this->dbEnglish->Select('distinct(passageID),scoringID,exScoringID');
		$this->dbEnglish->from('passageAttempt');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('completed' ,'2');       		// get complted passages
		$this->dbEnglish->order_by('lastmodified','asc');
		
	
			$this->dbEnglish->where('scoringID IS NOT NULL');
		

		$query = $this->dbEnglish->get();
		$attemptedPassageIDArr = $query->result_array();
		foreach($attemptedPassageIDArr as $key=>$value)
		{
				array_push($allQuesCompletedPsgArr,$value['passageID']);
				if(!$isContentExhausted){
					if($this->currReadingScoreID == $value['scoringID'] && !in_array($value['passageID'], $this->currReadingScoreIDPsgArr)){
						array_push($this->currReadingScoreIDPsgArr,$value['passageID']);
					}
					else if($this->currListeningScoreID == $value['scoringID'] && !in_array($value['passageID'], $this->currListeningScoreIDPsgArr)){
						array_push($this->currListeningScoreIDPsgArr,$value['passageID']);
					}	
				}
				else{
					if($this->currReadingExScoreID == $value['exScoringID'] && !in_array($value['passageID'], $this->currReadingScoreIDPsgArr))
						array_push($this->currReadingScoreIDPsgArr,$value['passageID']);
					else if($this->currListeningExScoreID == $value['exScoringID'] && !in_array($value['passageID'], $this->currListeningScoreIDPsgArr))	
						array_push($this->currListeningScoreIDPsgArr,$value['passageID']); 
				}
			
		}	

		

		if(count($allQuesCompletedPsgArr)>0)
			return implode(",",$allQuesCompletedPsgArr);				
		else 
			return "";
		

		
	}
	
	/**
	 * function role : save passage related attempt details.
	 * param1 : userID
	 * param2 : passageID , attempted passageID for which data need to be saved
	 * param3 : currentPassagePart , which need to be saved
	 * param4 : completed flag 0 if passage slides are going on , it will be 1 if user is on last slide and it is called
	 * @return  none
	 * 
	 * */
	
	function savePassageDetails($userID,$passageID,$currentPassagePart,$complete,$timeSpent,$sessionID)
	{
		
		$this->savePassageAttempts($userID,$passageID,$currentPassagePart,$complete,$timeSpent,$sessionID);
		$this->savePassageAttemptDetails($userID,$passageID,$currentPassagePart,$complete,$timeSpent,$sessionID);
	}

	/**
	 * function role : Save passage attempt information for users for new passages and update details for already attempted passages.
	 * param1 : userID
	 * param2 : passageID
	 * param3 : current passage part where user is in
	 * param4 : flag for completition
	 * param5 : time spent by user on passage
	 * param6 : sessionID
	 * @return  none
	 * 
	 * */
	function getPassagePreviousAttemptCount($userID,$passageID){
		$this->dbEnglish->Select('attemptCount,completed');
		$this->dbEnglish->from('passageAttempt');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('passageID',$passageID);
		$this->dbEnglish->order_by('lastModified','desc');
		$this->dbEnglish->limit(1);
		$query = $this->dbEnglish->get();
		return  $query->row_array();
	}

	function savePassageAttempts($userID,$passageID,$currentPassagePart,$complete,$timeSpent,$sessionID)
	{
		$psgContentType=$this->getPassageType($passageID);		
		$scoringID=$this->getOrSetPsgCurrScoringID($userID,$psgContentType,$passageID);		
		$this->dbEnglish->Select('passageAttemptID,totalTime');
		$this->dbEnglish->from('passageAttempt');
		$this->dbEnglish->where('passageID', $passageID);				
		$this->dbEnglish->where('userID', $userID);
		$query = $this->dbEnglish->get();
		$checkPassageAttempt = $query->result_array();
		
		if(($this->session->userdata('remediationStatus'))==1):
			$remediationStatus=1;
		else:
			$remediationStatus=0;
		endif;
		if(isset($checkPassageAttempt) && count($checkPassageAttempt))
		{
			//function to fetch the previos attempted count
			$previousAttemptCount=$this->getPassagePreviousAttemptCount($userID,$passageID);
		
			if($previousAttemptCount['completed']==2):
				$tempVal=$previousAttemptCount['attemptCount'];
				$attemptCount=$tempVal+1;
				$this->session->set_userdata('presentAttemptCount',$attemptCount);
				$insertData=array(
					'userID'		=>	$userID,
					'passageID'		=>	$passageID,
					'currentPassagePart'	=>	$currentPassagePart,
					'sessionID' 		=> 	$sessionID,
					'totalTime' 		=> 	$checkPassageAttempt[0]['totalTime'] + $timeSpent,
					'completed' 		=> 	$complete,
					'scoringID'		=>	$scoringID,
					'remediationStatus'	=>	$remediationStatus,
					'attemptCount'		=> 	$attemptCount

				);
				$this->dbEnglish->insert('passageAttempt', $insertData); 
				$this->passageAttemptID = $this->dbEnglish->insert_id();
			else:
				$data = array(
					   'currentPassagePart' => $currentPassagePart,
					   'completed' => $complete,
					   'totalTime' => $checkPassageAttempt[0]['totalTime'] + $timeSpent,
					   'sessionID' => $sessionID
	            		);

				$this->dbEnglish->where('userID', $userID);
				$this->dbEnglish->where('attemptCount', $previousAttemptCount['attemptCount']);
				$this->dbEnglish->where('passageID', $passageID);
				
				$this->dbEnglish->where('scoringID',$scoringID);		
				$this->dbEnglish->update('passageAttempt', $data);

				
				$this->passageAttemptID = $checkPassageAttempt[0]['passageAttemptID'];
			endif;
		}
		else
		{
			$data = array(
				'userID' 			=> 	$userID,
				'passageID' 		=> 	$passageID,
				'sessionID' 		=> 	$sessionID,
				'currentPassagePart' => 	$currentPassagePart,
				'completed' 		=> 	$complete,
				'totalTime'			=> 	$timeSpent,
				'attemptCount'		=>	1,
				'remediationStatus'	=>	$remediationStatus

			);
			$this->session->set_userdata('presentAttemptCount',1);
			$psgContentType=$this->getPassageType($passageID);
			
				$data['scoringID'] = $scoringID;
			
			$this->dbEnglish->insert('passageAttempt', $data); 
			$this->passageAttemptID = $this->dbEnglish->insert_id();
		}
		if($complete){
			$currentPsgQuestions = $this->session->userdata('currentPsgQuestions');
			$data = array('currentContentType' => currContentTypePsgQuesConst,'refID' => $currentPsgQuestions[0],'completed' => 1);
			$this->session->set_userdata($data);
		}
	}

	/**
	 * function role : Save pane-wise passage attempt information for users 
	 * param1 : userID
	 * param2 : passageID
	 * param3 : current passage part where user is in
	 * param4 : flag for completition
	 * param5 : time spent by user on passage
	 * param6 : sessionID
	 * @return  none
	 * 
	 * */

	function savePassageAttemptDetails($userID,$passageID,$currentPassagePart,$complete,$timeSpent,$sessionID)
	{
		$data = array(
				'userID' => $userID,
				'sessionID' => $sessionID,
				'currentPassagePart' => $currentPassagePart,
				'totalTime' => $timeSpent,
				'passageAttemptID' => $this->passageAttemptID
			);

			$this->dbEnglish->insert('passageAttemptDetails', $data); 
	}
	
	/**
	 * function role : Get attempted passage questions info
	 * param1 : userID
	 * param2 : passageID
	 * @return  string, list of attempted qcodes , empty string if there are no questions attempted
	 * 
	 * */
	function getRemediationStatus($userID,$passageID){
		$this->dbEnglish->Select('remediationStatus');
		$this->dbEnglish->from('passageAttempt');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('passageID',$passageID);-
		$this->dbEnglish->order_by('lastModified','desc');
		$this->dbEnglish->limit(1);
		$result = $this->dbEnglish->get();
		$resultArr = $result->row_array();
		return $resultArr;
	}

	function getMaxAttemptCountForQuestion($userID,$passageID){
		if(!$passageID) :
			$currPassageID= $this->session->userdata('sessionPassages');
			$currPassageID=$currPassageID[0];
		else :
			$currPassageID= $passageID;
		endif;
		$this->dbEnglish->Select('max(attemptCount) as maxCount');
		$this->dbEnglish->from($this->questionAttemptClassTbl);
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('passageID',$currPassageID);
		$result = $this->dbEnglish->get();
		$resultArr = $result->row_array();
		return $resultArr['maxCount'];
	}



	function getUserAttemptedPassageQuestions($userID,$passageID)
 	{
		
		$psgContentType=$this->getPassageType($passageID);

		$this->dbEnglish->Select('remediationStatus,attemptCount');
		$this->dbEnglish->from('passageAttempt');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('passageID',$passageID);
		$this->dbEnglish->order_by('lastModified','desc');
		$this->dbEnglish->limit(1);
		$result = $this->dbEnglish->get();
		$remediationstatus = $result->row_array();
		$attemptCount=$remediationstatus['attemptCount'];
		if(isset($attemptCount)):
			$presentAttemptCount=$attemptCount;
		else:
			$presentAttemptCount=1;
		endif;

		$presentContentType=$this->session->userdata('presentContentType');
	     	$currentContentType=$this->session->userdata('currentContentType');
	     	$remediationStatus=$this->session->userdata('remediationStatus');
			$repetationConstant=$this->session->userdata('isRepetation');
	     	$completed=$this->session->userdata('completed');
	     	if($presentContentType==contentFlowConversation && isset($attemptCount)  &&  $currentContentType==currContentTypePsgConst &&  $completed==0):
	     		$finalAttemptCount=$presentAttemptCount+1;
				
	     	elseif($presentContentType==contentFlowReading && $currentContentType==currContentTypePsgConst &&  $completed==0 && $remediationStatus==1):
	     		$presentAttemptCount=$this->getMaxAttemptCountForQuestion($userID,$passageID);
	     		$finalAttemptCount=$presentAttemptCount+1; 
			elseif($presentContentType==contentFlowReading && $currentContentType==currContentTypePsgConst &&  $completed==0 &&  $repetationConstant==1):
	     		$presentAttemptCount=$this->getMaxAttemptCountForQuestion($userID,$passageID);
	     		$finalAttemptCount=$presentAttemptCount+1; 
				
	     	else:
	     		$finalAttemptCount=$presentAttemptCount;
	     	endif;
	    $this->dbEnglish->Select('qcode');
		$this->dbEnglish->from($this->questionAttemptClassTbl);
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('passageID',$passageID);
		$this->dbEnglish->where('attemptCount',$finalAttemptCount);
		$query = $this->dbEnglish->get();
		$resultArr = $query->result_array();
		foreach($resultArr as $value):
			$tmpVal.=$value['qcode'].',';
		endforeach;
		$result=rtrim($tmpVal,',');
		return $result;

     }


	function setSpeakingQuestionCompletedFlag($userID,$speakingAttemptID){
		$data = array('completed'=> '1');		
		$this->dbEnglish->where('speakingAttemptID',$speakingAttemptID);
		$this->dbEnglish->update('speakingAttempts',$data);

		// Add here usercurrent s

		$data=array('completed'=>1);
		$this->session->set_userdata($data);
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->update('userCurrentStatus',$data);
		$updateTotalAttempt=$this->incrementTotalAttempts($userID,'speaking');
		$contentAttemptCount = $this->session->userdata('contentAttemptCount');
		$updateContentQuantity = ($contentAttemptCount==0) ? 1 : $contentAttemptCount +1;
		$result=$this->updateTotalAttemptsContentAttmptTbl($userID,$updateContentQuantity);	

	}


	//function saveSpeakingResponse($userID,$qcode,$questionNo,$timeTaken,$timeTakenExpln,$correct,$userResponse,$questionType,$sessionID)
	function saveSpeakingResponse($userID)
	{
		$qcode=$this->session->userdata('refID');
		$contentType=$this->session->userdata('presentContentType');
		$this->dbEnglish->Select('completed');
		$this->dbEnglish->from('speakingAttempts');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('qcode',$qcode);
		$query = $this->dbEnglish->get();
		$completedStatus=  $query->row_array();


		if($completedStatus['completed']==1):
			$updateTotalAttempt=$this->incrementTotalAttempts($userID,$contentType);
			$contentAttemptCount=$this->session->userdata('contentAttemptCount');
			if($contentAttemptCount==0):
	 			$contentAttemptCount=$contentAttemptCount+1;
	 		endif;
			$result=$this->updateTotalAttemptsContentAttmptTbl($userID,$contentAttemptCount);	

			$sessionSpeakingQues=$this->session->userdata('sessionSpeakingQues');

			if((!$this->session->userdata('sessionSpeakingQues')) || (count($sessionSpeakingQues) == 1)){
				
				$this->session->unset_userdata('refID');
				$this->session->unset_userdata('sessionSpeakingQues');
				$result=$this->studentsessionflow_model->getNextContentType($userID);
				
			}

			if(($key = array_search($qcode, $sessionSpeakingQues)) !== false)  
				unset($sessionSpeakingQues[$key]);

			$sessionSpeakingQues = array_values($sessionSpeakingQues);
	
			$this->session->set_userdata('sessionSpeakingQues',$sessionSpeakingQues);
			$data = array('currentContentType'=>currContentTypeSpeakingQuesConst,'refID'=>$sessionSpeakingQues[0],'completed'=>0);
			$this->session->set_userdata($data);
			$this->dbEnglish->where('userID',$userID);
			$this->dbEnglish->update('userCurrentStatus',$data);
		endif;

	}


	
	/**
	 * function role : Save passage question response details for users.
	 * param1 : userID
	 * param2 : qcode which need to be saved
	 * param3 : question number attempted by user in the session
	 * param4 : time taken by user to attempt
	 * param5 : time taken by user for explaination
	 * param6 : correct value true or false
	 * param7 : user response submitted by user
	 * param8 : question type which attempted by user
	 * param9 : sessionID 
	 * @return  none
	 * 
	 * */

	function savePassageQuestionsResponse($userID,$qcode,$questionNo,$timeTaken,$timeTakenExpln,$correct,$userResponse,$questionType,$sessionID)
                {
                                
			$qcodePassageDetails = $this->getQcodePassageDetails($qcode);
			$keys = array_keys($qcodePassageDetails);
			
			foreach($keys as $key)
			{
							${$key} = $qcodePassageDetails[$key] ;
			}
			$psgContentType=$this->getPassageType($qcodePassageID);

			if($this->session->userdata('presentAttemptCount')):
				$attemptCount=$this->session->userdata('presentAttemptCount');
			else:
				$previousAttemptCount=$this->getPassagePreviousAttemptCount($userID,$qcodePassageID);
				$attemptCount=($previousAttemptCount['attemptCount']) ? $previousAttemptCount['attemptCount']: '1';				
			endif;

			$scoringID=$this->getOrSetPsgCurrScoringID($userID,$psgContentType,$qcodePassageID);          


			$data = array(
				'userID'                                 =>           $userID,
				'qcode'                		  =>           $qcode,
				'questionNo'                      	=>           $questionNo,
				'timeTaken'                         	 =>           $timeTaken,
				'timeTakenExpln'              	 =>           $timeTakenExpln,
				'correct'                                =>           $correct,
				'userResponse' 		 =>           $userResponse,
				'questionType' 		 =>           $questionType,
				'sessionID'                           =>           $sessionID,
				'passageID'                          =>           $qcodePassageID,
				'attemptCount'                 	=>           $attemptCount
			);

		
							$data['scoringID'] = $scoringID;

											
			$this->dbEnglish->set('attemptedDate', 'NOW()', FALSE);
			$this->dbEnglish->insert($this->questionAttemptClassTbl, $data); 
			$data=array('currentContentType'=>currContentTypePsgQuesConst);
			$this->session->set_userdata($data);
			if($this->dbEnglish->affected_rows() == 1){
				if($qcodePassageID!=0){
					$refID=0;
					
					if(!$this->session->userdata('currentPsgQuestions'))
									$this->setCurrentPsgQuestions($userID,$qcodePassageID,true);
									
					$currentPsgQuesArr=$this->session->userdata('currentPsgQuestions');
					if(($key = array_search($qcode, $currentPsgQuesArr)) !== false)  
									unset($currentPsgQuesArr[$key]);
					$currentPsgQuesArr = array_values($currentPsgQuesArr);
					$prevrefID=$this->session->userdata('refID');
					if(count($currentPsgQuesArr) > 0)                                                            
									$refID=$currentPsgQuesArr[0];                                                 
					$data=array('completed'=>1,'currentPsgQuestions'=>$currentPsgQuesArr,'refID'=>$refID);
					$this->session->set_userdata($data);
					
					$data=array('completed'=>1);
					$this->dbEnglish->where('userID',$userID);
					$this->dbEnglish->update('userCurrentStatus',$data);
					$isLastCompletedPsgQnsPending = $this->lastCompletedPassageQuestionsPending($sessionID,$userID,0);
					if($isLastCompletedPsgQnsPending == 0)
					{
						/*ADDED BY NIVEDITA FOR UPDATING  THE FLAG TO 2 IN PASSAGE ATTEMPT IF ALL THE QUESTIONS ARE COMPLETED OF THAT PASSAGE*/
						if($qcodePassageID != 0)
						{
										
							$data = array('completed' => '2');
							$this->dbEnglish->where('userID', $userID);
							$this->dbEnglish->where('attemptCount', $attemptCount);
							$this->dbEnglish->where('passageID', $qcodePassageID);
							$this->dbEnglish->update('passageAttempt', $data);

							$this->updatePsgTotalAttemptCount();

						}					
						
						/*END*/
						$sessionPassagesArr =array();
						$this->passage_model->updateUserLevelAndAccPsgLog($userID,$psgContentType,$qcode);
						$sessionPassagesArr = $this->session->userdata('sessionPassages');
						if((!$this->session->userdata('sessionPassages')) || (count($sessionPassagesArr) == 1)){
							$this->studentsessionflow_model->getNextContentType($userID); 
							$sessionPassagesArr = $this->session->userdata('sessionPassages');
						}
						if(($key = array_search($qcodePassageID, $sessionPassagesArr)) === 0)  
							unset($sessionPassagesArr[$key]);
						$sessionPassagesArr = array_values($sessionPassagesArr);
						$this->session->set_userdata('sessionPassages',$sessionPassagesArr);
						$data = array('currentContentType'=>currContentTypePsgConst,'refID'=>$sessionPassagesArr[0],'completed'=>0);
						$this->session->set_userdata($data);
						$this->dbEnglish->where('userID',$userID);
						$this->dbEnglish->update('userCurrentStatus',$data);
					}              
				}
							
			}
			
				$data = array(
					'sessionID' => $sessionID
				);
			
			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->where('passageID', $qcodePassageID);
			$this->dbEnglish->where('attemptCount', $attemptCount);
			$this->dbEnglish->where('scoringID IS NOT NULL');
			
			$this->dbEnglish->update('passageAttempt', $data);

                                //Update the passageAttempts table based on attempts
     }


	/**
	 * function role : To get the qcode related passage details.
	 * param1 : qcode
	 * @return  array , of passage details
	 * 
	 * */
	


	public function incrementTotalAttempts($userID,$contentType){
		$userContentAttempt = $this->passage_model->userContentAttemptLog($userID,$contentType);
	 	$totalAttempts=$userContentAttempt['totalAttempts'];
	 	$totalAttemptData=$totalAttempts+1;	

	 	$this->dbEnglish->set('totalAttempts',$totalAttemptData);
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('contentType',$contentType);
		$this->dbEnglish->update('userContentAttemptDetails');
		if($contentType=='speaking'):
			$totalAttempts=$this->session->set_userdata('totalSpeakingAttempts',$totalAttemptData);
		endif;
	}

	
	 public function updatePsgTotalAttemptCount(){
	 	
	 	$contentType=$this->session->userdata('presentContentType');
	 	$userID=$this->session->userdata('userID');

	 	// User content attempt update query
	 	$userContentAttempt = $this->passage_model->userContentAttemptLog($userID,$contentType);


	 	$totalAttempts=$userContentAttempt['totalAttempts'];
	 	$totalAttemptData=$totalAttempts+1;	 	
	 	$this->dbEnglish->set('totalAttempts',$totalAttemptData);
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('contentType',$contentType);
		$this->dbEnglish->update('userContentAttemptDetails');

		if($contentType=='reading'):
	 		$totalAttempts=$this->session->set_userdata('totalReadingAttempts',$totalAttemptData);
	 	else:
	 		$totalAttempts=$this->session->set_userdata('totalConversationAttempts',$totalAttemptData);
	 	endif;
	 	$userContentFlowType=$this->session->userdata('presentContentType');
	 	$orderNo=$this->session->userdata('orderNo');


	 	// Session value not there it will  check database
	 	if(!$this->session->userdata('contentAttemptCount')) :
	 		$getContentattempt = $this->studentsessionflow_model->chckUserInUsrContentAttemptLog($userID);
	 		$contentAttemptCount = $getContentattempt['contentAttemptCount'];
	 		$contentQty=$this->studentsessionflow_model->getContentQuantity($getContentattempt['userContentFlowType'],$getContentattempt['orderNo']);
	 		$contentQuantity=$contentQty['contentQuantity'];

	 		if($contentAttemptCount!=$contentQuantity):
	 			$flag=0;
	 		endif;	 
			$this->session->set_userdata('presentContentType',$contentQty['contentType']);
			//Add this flow functionlity to while login // Suggest by karthick 
			//Ananad , After login /logout present content flow master session (where we need to load ?)
			//Before clicking the classroom or this flow it's Fine ?
	 	else :
	 		$contentAttemptCount=$this->session->userdata('contentAttemptCount');
	 		$flag=$this->session->userdata('isContentquantityEqual');
	 	endif;
	 	if($contentQuantity=($contentAttemptCount+1)):
	 		$flag==0;
	 	endif;
	 	if($contentAttemptCount==0 || $flag==0):
	 		$contentAttemptCount=$contentAttemptCount+1;
	 	endif;
		$this->dbEnglish->set('contentAttemptCount',$contentAttemptCount);
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->update('userContentFlowStatus');
		$this->session->unset_userdata('isContentquantityEqual');
		$this->session->unset_userdata('isRepetation');
		$this->session->set_userdata('contentAttemptCount',$contentAttemptCount);
		$this->session->set_userdata('remediationStatus', 0);
		$this->session->set_userdata('presentAttemptCount',0);

	 }


	 function updateTotalAttemptsContentAttmptTbl($userID,$contentAttemptCount){
		
		$orderNo=$this->session->userdata('orderNo');
		$this->dbEnglish->set('contentAttemptCount',$contentAttemptCount);
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('orderNo',$orderNo);
		$this->dbEnglish->update('userContentFlowStatus');
		$this->session->set_userdata('contentAttemptCount',$contentAttemptCount);
	}






	function getQcodePassageDetails($qcode)
	{
		$this->dbEnglish->Select('passageID as qcodePassageID');
		$this->dbEnglish->from('questions');
		$this->dbEnglish->where('qcode',$qcode);
		$query = $this->dbEnglish->get();
		$qcodePassageDetailArr = $query->row();

		if($qcodePassageDetailArr->qcodePassageID !=0){
			$this->dbEnglish->Select('passageType');
			$this->dbEnglish->from('passageMaster');
			$this->dbEnglish->where('passageID',$qcodePassageDetailArr->qcodePassageID);
			$query = $this->dbEnglish->get();
			$passageTypeArr = $query->row();
			$qcodePassageDetailArr->passageType=$passageTypeArr->passageType;
		}
			
		return (array)$qcodePassageDetailArr;
	}

	/**
	 * function role : Get number of passaged attempted on the current day
	 * param1 : userID
	 * @return  integer, passage attempt count
	 * 
	 * */


	function getMaxPassagesAttemptedToday($userID)
	{
		$today = date('Y-m-d');
		$this->dbEnglish->where('userID', $userID);
		$this->dbEnglish->where("date(lastModified)='".$today."'");
		$this->dbEnglish->from('passageAttempt');
		$passageAttemptCount = $this->dbEnglish->count_all_results();

		return $passageAttemptCount;
	}

	/**
	@EI_end : End of Functions for getting passage questions
	 **/


	/**
	 * function role : Get list of non contextual questions attempted by user for a set of skill/subSkill and store the current scoringID attempted free question in it's array
	 * param1 : userID
	 * param2 : freeQuesMsLevel , free question msLevel
	 * param3 : valID , it check for the free question exhaustion condition
	 * @return  array , previously attempted free questions for the mentioned ms Level 
	 * 
	 * */

	function getPreviousAttemptedNCQQues($userID,$freeQuesMsLevel,$valID='')
	{
		$this->dbEnglish->_protect_identifiers = FALSE;
		$this->dbEnglish->Select('A.qcode as qcodes,scoringID,exScoringID');
		$this->dbEnglish->from($this->questionAttemptClassTbl.' A');
		$this->dbEnglish->from('questions B');
		$this->dbEnglish->where('A.qcode = B.qcode');
		$this->dbEnglish->where('userID',$userID); 
		$this->dbEnglish->where('A.questionType','freeQues'); 
		$this->dbEnglish->where('B.passageID',0);
		$this->dbEnglish->where('B.msLevel',$freeQuesMsLevel);
		
		
		$query = $this->dbEnglish->get();
		$resultArr = $query->result_array();
		$this->dbEnglish->_protect_identifiers = TRUE;
		$NCQQuesAttemptedArr = array();
		
		foreach($resultArr as $key=>$value)
		{
			array_push($NCQQuesAttemptedArr,$value['qcodes']);
			if($this->session->userdata('isFreeQuesContentExhaust')){
				if($this->currFreeQuesExScoreID == $value['exScoringID'] && !in_array($value['qcodes'], $this->currFreeQuesScoreIDPsgArr))
					array_push($this->currFreeQuesScoreIDPsgArr,$value['qcodes']);
			}
			else{
				if($this->currFreeQuesScoreID == $value['scoringID'] && !in_array($value['qcodes'], $this->currFreeQuesScoreIDPsgArr))
					array_push($this->currFreeQuesScoreIDPsgArr,$value['qcodes']);
			}	
		}
		return $NCQQuesAttemptedArr;		
	}

	/**
	 * function role : sets next non contextual qcode to be given to user in the return array from the free question session variable 
	 * param1 : userID
	 * param2 : SessionID
	 * @return  none
	 * 
	 **/

	function getNextFreeQuestionFn($userID,$sessionID)
	{
		$this->studentFlowArr = array();
		$freeQuestions=$this->session->userdata('sessionfreeQues');
		$this->studentFlowArr['qType'] =  'freeQues';
		$this->studentFlowArr['qID'] = $this->qcode = $freeQuestions[0] ;
		$this->studentFlowArr['info']['passagePartNo']  = null;
		$this->studentFlowArr['info']['passageType'] = null;
		
		$data = array('currentContentType' => currContentTypeFreeQuesConst,'refID' => $freeQuestions[0],'completed' => 0);
		$this->session->set_userdata($data);
		$this->dbEnglish->where('userID', $userID);
		$this->dbEnglish->update('userCurrentStatus', $data);
	}



	function getNextSpeakingQuestionFn($userID,$sessionID)
	{
		$this->studentFlowArr = array();
		$speakingQuestions=$this->session->userdata('sessionSpeakingQues');
		$this->studentFlowArr['qType'] =  'speaking';
		$this->studentFlowArr['qID'] = $this->qcode = $speakingQuestions[0] ;
		$this->studentFlowArr['info']['passagePartNo']  = null;
		$this->studentFlowArr['info']['passageType'] = null;
		
		$data = array('currentContentType' => currContentTypeSpeakingQuesConst,'refID' => $speakingQuestions[0],'completed' => 0);
		$this->session->set_userdata($data);
		$this->dbEnglish->where('userID', $userID);
		$this->dbEnglish->update('userCurrentStatus', $data);
	}
	

	/**
	 * function role : Check if an introduction is given in flow
	 * param1 : userID
	 * param2 : sessionID
	 * param3 : refID of the userCurrentStatus
	 * @return  1=> Intorduction to be given , 0=> introduction not to be given
	 * 
	 **/

	/**
	 * function role : Check if an introduction is given in flow
	 * param1 : userID
	 * param2 : sessionID
	 * param3 : refID of the userCurrentStatus
	 * @return  1=> Intorduction to be given , 0=> introduction not to be given
	 * 
	 **/

	function isIntroductionToBeGiven($userID,$sessionID,$refID)
	{
		$qcodeDetailsArr=$this->getQcodeDetails($refID);
		if($this->isIntroductionDoneToday($userID,$qcodeDetailsArr->skillID) == 0) //enter if no
		{
			if($this->isIntroductionMapped($userID,$sessionID,$qcodeDetailsArr) == 1){
				return 1;
			} else {
				return 0;
			}
		}
		else{
			return 0;
		}
	}

	/**
	 * function role : Check if any introduction is done by the user today
	 * param1 : userID
	 * param2 : qcodeSkillID
	 * @return  1=> Intorduction done today , 0=> Intorduction not done today
	 * 
	 **/
	//gre
	function isIntroductionDoneToday($userID,$qcodeSkillID)
	{
		$today = date('Y-m-d');

		$getAttemptedIntrosql =$this->dbEnglish->query("SELECT `p`.`srno` FROM (`IGREAttemptDetails` p) INNER JOIN `IGREMaster` q ON `p`.`igreid`=`q`.`igreid` WHERE `p`.`userID` =  ".$userID." AND `q`.`skillID` =  '".$qcodeSkillID."' AND date(p.lastModified)='".$today."'");
		
		if($getAttemptedIntrosql->num_rows()>0)
			return 1;
		else
			return 0;
	}

	/**
	 * function role : Check if introduction is mapped to the current skill, subSKill available as part of the class variables. Sets the flow type as introduction, to be given to the user
	 * param1 : userID
	 * param2 : sessionID
	 * param3 : array of qcode details
	 * @return  1=> Intorduction mapped to skill,subSKill , 0=> Intorduction not mapped to skill,subSKill
	 * 
	 **/
	

	function isIntroductionMapped($userID,$sessionID,$qcodeDetailsArr)
	{
		$userAttemptedIGRE = $this->getIGREAttemptedByUser($userID);
		$userAttemptedIGREArr = explode(',', $userAttemptedIGRE);
		$skillIDArr=explode(',', $qcodeDetailsArr->skillID);
		$subSkillIDArr=explode(',', $qcodeDetailsArr->subSkillID);
		$subSubSkillIDArr=explode(',', $qcodeDetailsArr->subSubSkillID);
		$skillID=$skillIDArr[0];
		$subSkillID=$subSkillIDArr[0];
		$subSubSkillID=$subSubSkillIDArr[0];
		$this->dbEnglish->Select('igreid');
		$this->dbEnglish->from('IGREMaster');
		$this->dbEnglish->where("igreType",'introduction');
		$this->dbEnglish->where("topicID",$qcodeDetailsArr->topicID);
		$this->dbEnglish->where("(find_in_set( ".$skillID." ,skillID) > 0 or skillID is null)");
		$this->dbEnglish->where("(find_in_set( ".$subSkillID." ,subSkillID) > 0 or subSkillID is null)");
		$this->dbEnglish->where("(find_in_set( ".$subSubSkillID." ,subSubSkillID) > 0 or subSubSkillID is null)");
		$this->dbEnglish->where_not_in("igreid",$userAttemptedIGREArr);
		$query = $this->dbEnglish->get();
		if($query->num_rows()  > 0)
		{
			$introductionAttemptArr = $query->result_array();
			$this->studentFlowArr['qType'] =  'introduction';
			$this->studentFlowArr['qID'] = $introductionAttemptArr[0]['igreid'];
			$this->studentFlowArr['info']['passagePartNo']  = null;
			$this->studentFlowArr['info']['passageType'] = null;
			$this->insertUserIGREAttempt($userID,$introductionAttemptArr[0]['igreid'],$sessionID);
			return 1;
		}
	}

	/**
	 * function role : Get list of igres attempted by user
	 * param1 : userID
	 * @return  List of igres
	 * 
	 **/

	function getIGREAttemptedByUser($userID)
	{
		$this->dbEnglish->Select('group_concat(igreid) as igres');
		$this->dbEnglish->from('IGREAttemptDetails');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('lastModified',$today);
		$query = $this->dbEnglish->get();
		$introductionAttemptArr = $query->result_array();
		return $introductionAttemptArr[0]['igres'];
	}

	/**
	 * function role : Function made to simulate igres attempted by user. Currently temporarirly called when a particular introduction is attempted by user.
	 * param1 : userID
	 * param2 : igre id attempted by user
	 * param3  : sesisonID
	 * @return : none
	 *
	 **/

	function insertUserIGREAttempt($userID,$igreid,$sessionID)
	{
		$data = array(
				'userID' => $userID,
				'igreid' => $igreid,
				'sessionID' => $sessionID
			);

		$this->dbEnglish->insert('IGREAttemptDetails', $data); 
	}


	/*
	function : Save speaking question response details for users.
	Author : Praneeth
	*/
	
	function saveSpeakingResponses($content,$userID,$qcode,$userResponse,$sessionID,$questionPart,$percentage,$speakingAttemptID){
		$qcode=$this->session->userdata('refID');
		$data = array(
				'speakingAttemptID' => $speakingAttemptID,
				'sessionID' => $sessionID,
				'contentText' => $content,
				'userResponse' =>$userResponse,
				'score'=>$percentage,
				'userID'=>$userID,
				'qcode' =>$qcode
			);
		$this->dbEnglish->insert('speakingAttemptDetails', $data);
	}

	function saveSpeakingAttempt($userid,$qcode,$sessionid,$questionPart,$totalTimeTaken,$completed){
		$qcode=$this->session->userdata('refID');
		$data = array(
				'sessionID' => $sessionid,
				'qcode' =>$qcode,
				'userID'=>$userid,
				'questionPart'=> $questionPart,
				'totalTimeTaken'=> $totalTimeTaken,
				'completed'=> $completed
			);
		$this->dbEnglish->insert('speakingAttempts', $data);

		$this->dbEnglish->Select('questionPart,completed,speakingAttemptID');
		$this->dbEnglish->from('speakingAttempts');
		$this->dbEnglish->where('userID',$userid);
		$this->dbEnglish->where('qcode',$qcode);
		$this->dbEnglish->where('sessionID',$sessionid);
		$query = $this->dbEnglish->get();
		$speakingCompleted = $query->result_array();
		return $speakingCompleted[0];	
	}

	function isSpeakingQuesCompleted($userid,$qcode){
		$this->dbEnglish->Select('questionPart,completed,speakingAttemptID');
		$this->dbEnglish->from('speakingAttempts');
		$this->dbEnglish->where('userID',$userid);
		$this->dbEnglish->where('qcode',$qcode);
		$this->dbEnglish->order_by('lastModified','desc');
		$query = $this->dbEnglish->get();
		$speakingCompleted = $query->result_array();
		return $speakingCompleted[0];	
	}

	function updateSpeakingQuestionPart($questionPart,$speakingAttemptID){
		$data = array(
			'questionPart'=> $questionPart
		);
		$this->dbEnglish->where('speakingAttemptID',$speakingAttemptID);
		$this->dbEnglish->update('speakingAttempts',$data);
	}








	/**
	 * function role : Save non contextual question response details for users.
	 * param1 : userID
	 * param2 : qcode which need to be saved
	 * param3 : question number attempted by user in the session
	 * param4 : time taken by user to attempt
	 * param5 : time taken by user for explaination
	 * param6 : correct value true or false
	 * param7 : user response submitted by user
	 * param8 : question type which attempted by user
	 * param9 : sessionID  
	 * param10 : skillID  
	 * param11 : subSkillID  
	 * @return  none
	 * 
	 * */
	 
	function saveNCQQuestionsResponse($userID,$qcode,$questionNo,$timeTaken,$timeTakenExpln,$correct,$userResponse,$questionType,$sessionID,$skillID,$subSkillID)
	{
		
		$data = array(
				'userID' => $userID,
				'qcode' => $qcode,
				'questionNo' => $questionNo,
				'timeTaken' => $timeTaken,
				'timeTakenExpln' => $timeTakenExpln,
				'correct' => $correct,
				'userResponse' => $userResponse,
				'questionType' => $questionType,
				'sessionID' => $sessionID
			);
		$isAttemptHistoryAvailable = $this->getFreeQuesAttemptCount($userID,$qcode);

		if( $isAttemptHistoryAvailable !=null){
			//when it is not null it means there is previous attemptCount, increment it by 1 and update
			$data['attemptCount'] = $isAttemptHistoryAvailable+1;
		}else {
			//if it is null it means there is no previous attemptHistory set attemptCount as 1
			$data['attemptCount'] = 1;
		}
		$scoringID=$this->getOrSetFreeQuesCurrScoringID($userID);		

		$data['scoringID'] = $scoringID;
		$isRefIDPresent = $this->session->userdata('isRefIDPresent');
		if($isRefIDPresent == 1){
			$data['bunchID'] = 0;
		} else {
			$this->updateContentAttemptLog();
			$data['bunchID'] = $this->session->userdata('currentBunch');
		}
		$this->session->set_userdata('isRefIDPresent',0);
		$this->dbEnglish->set('attemptedDate', 'NOW()', FALSE);
		$this->dbEnglish->insert($this->questionAttemptClassTbl, $data);
		//test

		if($this->dbEnglish->affected_rows() == 1){

			 $refID=0;

			 $sessionFreeQuesArr = $this->session->userdata('sessionfreeQues');

			 if(($key = array_search($qcode, $sessionFreeQuesArr)) !== false) { 
					unset($sessionFreeQuesArr[$key]);
			 } if(($key = array_search($qcode, $sessionFreeQuesArr)) !== false) { 
					unset($sessionFreeQuesArr[$key]);
			 }
			 $sessionFreeQuesArr = array_values($sessionFreeQuesArr);

			 if(count($sessionFreeQuesArr) > 0)
				$refID=$sessionFreeQuesArr[0];

			 $data=array('sessionfreeQues'=>$sessionFreeQuesArr,'completed'=>1,'refID'=>$refID);
			 $this->session->set_userdata($data);
			 $data=array('completed'=>1);

			 $this->dbEnglish->where('userID',$userID);
			 $this->dbEnglish->update('userCurrentStatus',$data);
			 $orderNo=$this->session->userdata('orderNo');

			
			 $contentFlowQuantity=$this->session->userdata('contentQuantity');
			 $contentAttemptCount=$this->session->userdata('contentAttemptCount');
			 $flag = $this->session->userdata('isContentCountEqual');
			 if($contentAttemptCount==0 || $flag == 0):
	 			$contentAttemptCount=$contentAttemptCount+1;
			 endif;
			
			$level=$this->session->userdata('freeQuesLevel');
			
			$this->dbEnglish->Select('quesPsgAttemptCount');
			$this->dbEnglish->from('userLevelAndAccuracyLog');
			$this->dbEnglish->where('userID',$userID);
			$this->dbEnglish->where('level',$level);
			$this->dbEnglish->where('contentType','Free_Question');
			$this->dbEnglish->order_by('scoringID','desc');
			$this->dbEnglish->limit(1);
			$quesPsgAttempt = $this->dbEnglish->get();
			if($quesPsgAttempt->num_rows() > 0){
				$quesPsgAttemptCount = $quesPsgAttempt->row();
				$quesPsgAttemptCount = $quesPsgAttemptCount->quesPsgAttemptCount+1;
			}
			$this->dbEnglish->set('quesPsgAttemptCount',$quesPsgAttemptCount);
			$this->dbEnglish->where('userID',$userID);
			$this->dbEnglish->where('level',$level);
			$this->dbEnglish->where('scoringID',$scoringID);
			$this->dbEnglish->where('contentType','Free_Question');
			$this->dbEnglish->update('userLevelAndAccuracyLog');
			
			$this->dbEnglish->set('contentAttemptCount',$contentAttemptCount);
			$this->dbEnglish->where('userID',$userID);
			$this->dbEnglish->where('orderNo',$orderNo);
			$this->dbEnglish->update('userContentFlowStatus');
			
			$sessionfreeQues = $this->session->userdata("sessionfreeQues");
			$currentBunchID = $this->session->userdata("currentBunch");
			$schoolBunchingOrder = $this->session->userdata('schoolBunchingOrder');
			$lastBunchID = end($schoolBunchingOrder);
			$remainingfreeQues = $this->session->userdata('remainingfreeQues');
			$isGroupSkillActive = $this->session->userdata('isGroupSkillActive');
			if($lastBunchID == $currentBunchID && count($sessionfreeQues) == 0 && $remainingfreeQues != ""){
				if($isGroupSkillActive == 1){
					$this->dbEnglish->Select('bunchBeforeGroupSkillActivation');
					$this->dbEnglish->from('userCurrentStatus');
					$this->dbEnglish->where('userID', $userID);
					$bunchBeforeGroupSkillActivationSql = $this->dbEnglish->get();
					$currentBunchData = $bunchBeforeGroupSkillActivationSql->row();
					$currentBunchID = $currentBunchData->bunchBeforeGroupSkillActivation;
				} else {
					$currentBunchID = $this->freeques_model->nextBunchID($currentBunchID);
					$currentBunchID = $currentBunchID['bunchID']; //set to 11
				}
				$qcodesArr = array();
				$this->freeques_model->fetchFromContentExhaustionLogic($currentBunchID,$remainingfreeQues,$qcodesArr);
			}

			if($contentAttemptCount!=$contentFlowQuantity):
			  	$this->studentsessionflow_model->updateContentQuantity($userID,$contentAttemptCount,$counter=0,$orderNo);
			else:
				$this->studentsessionflow_model->updateContentQuantity($userID,$contentAttemptCount,$counter=1,$orderNo+1);
				$sessionTypeToShow=$this->studentsessionflow_model->getNextContentType($userID);
				$this->session->set_userdata('sessionTypeToShow',$sessionTypeToShow);

			endif;
		}
	
		$qcodeSkillID = $skillID;
		$qcodeSubSkillID = $subSkillID;

		$this->dbEnglish->Select('srno,totalQA');
		$this->dbEnglish->from('freeQtypeAttempts');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('skillID',$qcodeSkillID);
		$this->dbEnglish->where('subSkillID',$qcodeSubSkillID);
		$query = $this->dbEnglish->get();
		$skillFreeQtypeAttemptsStatus = $query->result_array();

		if(count($skillFreeQtypeAttemptsStatus)>0)
		{
			// Update the freeQuesType table based on attempts
			$totalQAInSkill = $skillFreeQtypeAttemptsStatus[0]['totalQA'];

			$data = array(
				'totalQA' => $totalQAInSkill+1,
			);

			$this->dbEnglish->where('userID',$userID);
			$this->dbEnglish->where('skillID',$qcodeSkillID);
			$this->dbEnglish->where('subSkillID',$qcodeSubSkillID);
			$this->dbEnglish->update('freeQtypeAttempts', $data); 
			
		}
		else
		{
			$skillGroupID=$this->session->userdata('groupSkillID');
			$topicID = $this->getTopicInfo($qcode);

			$data = array(
				'userID' => $userID,
				'skillID' => $qcodeSkillID,
				'subSkillID' => $qcodeSubSkillID,
				'topicID' => $topicID,
				'totalQA' => 1,
				'groupSkillID' => $skillGroupID,
			);

			$this->dbEnglish->set('startDate', 'NOW()', FALSE);
			$this->dbEnglish->insert('freeQtypeAttempts', $data); 

		}
		// Update the end date of a skill
	}

	/* function updateContentAttemptLog(){
		$userID = $this->session->userdata('userID');
		$presentContentType = $this->session->userdata('presentContentType');
		$this->dbEnglish->Select('currentBunchID,totalAttempts,bunchCompleted');		
		$this->dbEnglish->from('userContentAttemptDetails');
		$this->dbEnglish->where('userID', $userID);
		$this->dbEnglish->where('contentType', $presentContentType);
		$bunchDetailsSQL = $this->dbEnglish->get();
		$bunchDetails = $bunchDetailsSQL->result_array();	
		
		$currentBunchID = $bunchDetails[0]['currentBunchID'];
		$totalAttempts = $bunchDetails[0]['totalAttempts'];
		$bunchCompleted = $bunchDetails[0]['bunchCompleted'];
		$freeQuesLevel = $this->session->userdata('freeQuesLevel');
		
		if($bunchCompleted == 1){//i
			$currentBunchID = $this->freeques_model->nextBunchID($currentBunchID);
			$currentBunchID = $currentBunchID['bunchID']; //set to 11

			$this->session->set_userdata('currentBunch',$currentBunchID);
			$this->dbEnglish->where('userID',$userID);
			$this->dbEnglish->where('contentType',$presentContentType);
			$this->dbEnglish->update('userContentAttemptDetails', array('currentBunchID' => $currentBunchID,'bunchCompleted' => 0, 'totalAttempts' => 1));
			$this->session->set_userdata('currentBunch',$currentBunchID);
			
			
			$this->dbEnglish->Select('count(*) as bunchCount');		
			$this->dbEnglish->from('bunchMaster');
			$this->dbEnglish->where('childClass', $freeQuesLevel);
			$this->dbEnglish->where('bunchID', $currentBunchID);
			$bunchMasterCountSQL = $this->dbEnglish->get();
			$bunchMasterCount = $bunchMasterCountSQL->result_array();	
			
			
			$this->dbEnglish->Select('totalAttempts');		
			$this->dbEnglish->from('userContentAttemptDetails');
			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->where('contentType',$presentContentType);
			$totalAttemptsSQL = $this->dbEnglish->get();
			$totalAttempts = $totalAttemptsSQL->result_array();
		
			if($bunchMasterCount == $totalAttempts){
				$data = array(
					'bunchCompleted' => 1
				);
				$this->dbEnglish->where('userID',$userID);
				$this->dbEnglish->where('contentType',$presentContentType);
				$this->dbEnglish->update('userContentAttemptDetails', $data);
			}
		} else {
			$freeQuesLevel = $this->session->userdata('freeQuesLevel');			
			$this->dbEnglish->Select('count(*) as bunchCount');		
			$this->dbEnglish->from('bunchMaster');
			$this->dbEnglish->where('childClass', $freeQuesLevel);
			$this->dbEnglish->where('bunchID', $currentBunchID);
			$bunchMasterCountSQL = $this->dbEnglish->get();
			$bunchMasterCount = $bunchMasterCountSQL->result_array();

			$totalAttempts+=1;
			$this->dbEnglish->where('userID',$userID);
			$this->dbEnglish->where('contentType',$presentContentType);
			$this->dbEnglish->update('userContentAttemptDetails',array('totalAttempts' => $totalAttempts));

			if($bunchMasterCount == $totalAttempts){
				$data = array(
					'bunchCompleted' => 1
				);
				$this->dbEnglish->where('userID',$userID);
				$this->dbEnglish->where('contentType',$presentContentType);
				$this->dbEnglish->update('userContentAttemptDetails', $data);
			}
		}
	} */

	function updateContentAttemptLog(){
		$userID = $this->session->userdata('userID');
		$presentContentType = 'freeques';
		$this->session->set_userdata('presentContentType','freeques');
		$bunchDetailsSQL = "SELECT currentBunchID,totalAttempts,bunchCompleted from educatio_msenglish.userContentAttemptDetails where userID=$userID and contentType = '$presentContentType'";
		$bunchDetails = $this->dbEnglish->query($bunchDetailsSQL)->result_array();
		$currentBunchID = $bunchDetails[0]['currentBunchID'];
		$totalAttempts = $bunchDetails[0]['totalAttempts'];
		$bunchCompleted = $bunchDetails[0]['bunchCompleted'];
		$freeQuesLevel = $this->session->userdata('freeQuesLevel');
		
		if($bunchCompleted == 1){//i
			$currentBunchID = $this->freeques_model->nextBunchID($currentBunchID);
			$currentBunchID = $currentBunchID['bunchID']; //set to 11

			$this->session->set_userdata('currentBunch',$currentBunchID);
			$this->dbEnglish->where('userID',$userID);
			$this->dbEnglish->where('contentType',$presentContentType);
			$this->dbEnglish->update('userContentAttemptDetails', array('currentBunchID' => $currentBunchID,'bunchCompleted' => 0, 'totalAttempts' => 1));
			$this->session->set_userdata('currentBunch',$currentBunchID);
			
			$bunchMasterCountSQL = "SELECT count(*) as bunchCount from bunchMaster where childClass=$freeQuesLevel and bunchID=$currentBunchID";
			$bunchMasterCount = $this->dbEnglish->query($bunchMasterCountSQL)->row()->bunchCount;
			$totalAttemptsSQL = "SELECT totalAttempts FROM educatio_msenglish.userContentAttemptDetails where userID=$userID and contentType = '$presentContentType'";
			$totalAttempts = $this->dbEnglish->query($totalAttemptsSQL)->row()->totalAttempts;
			if($bunchMasterCount == $totalAttempts){
				$data = array(
					'bunchCompleted' => 1
				);
				$this->dbEnglish->where('userID',$userID);
				$this->dbEnglish->where('contentType',$presentContentType);
				$this->dbEnglish->update('userContentAttemptDetails', $data);
			}
		} else {
			$freeQuesLevel = $this->session->userdata('freeQuesLevel');
			$bunchMasterCountSQL = "SELECT count(*) as bunchCount from bunchMaster where childClass=$freeQuesLevel and bunchID=$currentBunchID";
			$bunchMasterCount = $this->dbEnglish->query($bunchMasterCountSQL)->row()->bunchCount;
			$totalAttempts+=1;
			$this->dbEnglish->where('userID',$userID);
			$this->dbEnglish->where('contentType',$presentContentType);
			$this->dbEnglish->update('userContentAttemptDetails',array('totalAttempts' => $totalAttempts));

			if($bunchMasterCount == $totalAttempts){
				$data = array(
					'bunchCompleted' => 1
				);
				$this->dbEnglish->where('userID',$userID);
				$this->dbEnglish->where('contentType',$presentContentType);
				$this->dbEnglish->update('userContentAttemptDetails', $data);
			}
		}
	}

	/**
	 * function role : To get scoring id if exists or to create it if it does not exist.
	 * param1 : userID
	 * param2 : contentType - Reading/listening
	 * param3 : passageID
	 * return : scoringID as per content type
	 **/

	function getOrSetPsgCurrScoringID($userID,$contentType,$passageID){
		if($contentType == "Textual" || $contentType == "Illustrated")
			$contentTypeConst=readingContentTypeConst;
		else if($contentType == "Conversation")
			$contentTypeConst=listeningContentTypeConst;
		
		$scoringID=$this->getPsgCurrScoringID($contentTypeConst);
		
		if($scoringID != null){
			return $scoringID;	
		}
		else{
			$this->passage_model->updateUserLevelAndAccPsgLog($userID,$contentType,$passageID);	
			$scoringID=$this->getOrSetPsgCurrScoringID($userID,$contentType,$passageID);
			return $scoringID;
		}
	}

	/**
	 * function role : To get scoring id if exists otherwise null.
	 * param1 : content Type Constant - Reading/listening
	 * return : scoringID as per content type,null if not exists
	 **/

	function getPsgCurrScoringID($contentTypeConst){

		$this->dbEnglish->Select('scoringID');
		$this->dbEnglish->from('userLevelAndAccuracyLog');
		
		$this->dbEnglish->where('userID',$this->user_id);
		if($this->category == 'ADMIN' || $this->category == 'TEACHER' || $this->category == 'School Admin')
		{
			
			$level   = $this->session->userdata('passageLevel');
				
			$this->dbEnglish->where('level',$level);
			$this->dbEnglish->where('contentType',$contentTypeConst);
		}
		else
		{
			$this->dbEnglish->where('contentType',$contentTypeConst);
		}
		$this->dbEnglish->order_by('scoringID','desc');
		$this->dbEnglish->limit(1);
		$currPsgScoringIDSql = $this->dbEnglish->get();

		if($currPsgScoringIDSql->num_rows() > 0){
			$currPsgScoringIDArr = $currPsgScoringIDSql->row();
			return $currPsgScoringIDArr->scoringID;	
		}
		else{
			return null;
		}
	}

	/**
	 * function role : To get scoring id if exists or to create it if it does not exist for free questions.
	 * param1 : userID
	 * return : scoringID for free questions
	 **/


	function getOrSetFreeQuesCurrScoringID($userID){
		$scoringID=$this->getFreeQuesCurrScoringID();

		if($scoringID != null){
			return $scoringID;	
		}
		else
		{
			$this->freeques_model->updateUserLevelAndAccFreeQuesLog($userID,freeQuesContentTypeConst);	
			$scoringID=$this->getOrSetFreeQuesCurrScoringID($userID);
			return $scoringID;
		}		
	}

	/**
	 * function role : To get scoring id if exists otherwise null for free questions.
	 * return : scoringID for free questions , null if not exists
	 **/

	function getFreeQuesCurrScoringID(){
		$this->dbEnglish->Select('scoringID');
		$this->dbEnglish->from('userLevelAndAccuracyLog');

		if($this->category == 'ADMIN' || $this->category == 'TEACHER' || $this->category == 'School Admin')
		{
			
			$level   = $this->session->userdata('freeQuesLevel');
				
			$this->dbEnglish->where('level',$level);
			$this->dbEnglish->where('contentType',freeQuesContentTypeConst);
		}
		else
		{
			$this->dbEnglish->where('contentType',freeQuesContentTypeConst);	
		}
		
		$this->dbEnglish->where('userID',$this->user_id);
		$this->dbEnglish->order_by('scoringID','desc');
		$this->dbEnglish->limit(1);
		$currFreeQuesScoringIDSql = $this->dbEnglish->get();
		
		if($currFreeQuesScoringIDSql->num_rows() > 0){
			$currFreeQuesScoringIDArr = $currFreeQuesScoringIDSql->row();
			return $currFreeQuesScoringIDArr->scoringID;	
		}
		else
		{
			return null;
		}	
	}

	/**
	 * function role : To get ex-scoring id if exists or to create it if it does not exist.
	 * param1 : userID
	 * param2 : contentType - Reading/listening
	 * param3 : passageID
	 * return : ex-scoringID as per content type
	 **/

	//exhaustion
	function getOrSetPsgCurrExScoringID($userID,$contentType,$passageID){
		if($contentType == "Textual" || $contentType == "Illustrated")
			$contentTypeConst=readingContentTypeConst;
		else if($contentType == "Conversation")
			$contentTypeConst=listeningContentTypeConst;
		
		$exScoringID = $this->getPsgCurrExScoringID($contentTypeConst);
		
		if($exScoringID != null){
			return $exScoringID;	
		}
		else{
			$this->passage_model->updateUserExhaustPsgLog($userID,$contentType,$passageID);	
			$exScoringID=$this->getOrSetPsgCurrExScoringID($userID,$contentType,$passageID);
			return $exScoringID;
		}
	}

	/**
	 * function role : To get ex-scoring id if exists otherwise null.
	 * param1 : contentType - Reading/listening.
	 * return : ex-scoringID as per content type,null if not exists.
	 **/


	function getPsgCurrExScoringID($contentTypeConst){
		$this->dbEnglish->Select('exScoringID');
		$this->dbEnglish->from('userexhaustionlogiclog');
		$this->dbEnglish->where('contentType',$contentTypeConst);
		$this->dbEnglish->where('userID',$this->user_id);
		$this->dbEnglish->order_by('exScoringID','desc');
		$this->dbEnglish->limit(1);
		$userCurrExScoringIDSql = $this->dbEnglish->get();
		if($userCurrExScoringIDSql->num_rows()  > 0){
			$userCurrExScoringIDArr = $userCurrExScoringIDSql->row();
			return $userCurrExScoringIDArr->exScoringID;	
		}
		else
		{
			return null;
		}	

	}

	/**
	 * function role : To get ex-scoring id if exists or to create it if it does not exist for free questions.
	 * param1 : userID
	 * return : ex-scoringID for free questions
	 **/

	function getOrSetFreeQuesCurrExScoringID($userID){
		$exScoringID = $this->getFreeQuesCurrExScoringID();
		if($exScoringID != null){
			return $exScoringID;	
		}
		else{
			$this->freeques_model->updateUserExhaustLevelAndAccLog($userID,freeQuesContentTypeConst);	
			$exScoringID=$this->getOrSetFreeQuesCurrExScoringID($userID);
			return $exScoringID;
		}
	}

	/**
	 * function role : To get ex-scoring id if exists otherwise null for free questions.
	 * return : ex-scoringID for free questions , null if not exists
	 **/

	function getFreeQuesCurrExScoringID(){
		$this->dbEnglish->Select('exScoringID');
		$this->dbEnglish->from('userexhaustionlogiclog');
		$this->dbEnglish->where('contentType',freeQuesContentTypeConst);
		$this->dbEnglish->where('userID',$this->user_id);
		$this->dbEnglish->order_by('exScoringID','desc');
		$this->dbEnglish->limit(1);
		$userCurrExScoringIDSql = $this->dbEnglish->get();
		if($userCurrExScoringIDSql->num_rows()  > 0){
			$userCurrExScoringIDArr = $userCurrExScoringIDSql->row();
			return $userCurrExScoringIDArr->exScoringID;	
		}
		else
		{
			return null;
		}	
	}	


	 /**
	 * function role : Fetch time spent spent by user on current day
	 * param1 : userID
	 * @return  float, time spent
	 * 
	 * */

	function getTimeSpentToday($userID)
	{
		$this->dbEnglish->Select('sessionID,startTime,endTime');
		$this->dbEnglish->from('sessionStatus');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('startTime > curdate()');
		$query = $this->dbEnglish->get();
		$sessionTimeDetails = $query->result_array();

		$timeSpent = 0;

		if(count($sessionTimeDetails) > 0)
		{
			foreach ($sessionTimeDetails as $key => $value) 
			{
				$startTime = $this->convertToTime($value['startTime']);
				if($value['endTime']!="" && $value['endTime']!=null)       
				{
					$endTime = $this->convertToTime($value['endTime']);
				}
				else
				{
				    $this->dbEnglish->Select('max(lastModified) as lastTime');
					$this->dbEnglish->from($this->questionAttemptClassTbl);
					$this->dbEnglish->where('userID',$userID);
					$this->dbEnglish->where('sessionID',$value['sessionID']);
					$query = $this->dbEnglish->get();
					$questionTimeDetails = $query->result_array();

					if($questionTimeDetails[0]['lastTime']=="" || $questionTimeDetails[0]['lastTime']==null)
					    continue;
					else
					    $endTime = $this->convertToTime($questionTimeDetails[0]['lastTime']);
				}
				$timeSpent = $timeSpent + ($endTime - $startTime);        //in secs
			}
		}
		return round( ($timeSpent/60) , 2);
	}

	/**
	 * function role : Changing time format
	 * param1 : time
	 * @return  time
	 * 
	 **/

	function convertToTime($time)
	{
		$hr   = substr($time,11,2);
	    $mm   = substr($time,14,2);
	    $ss   = substr($time,17,2);
	    $day  = substr($time,8,2);
	    $mnth = substr($time,5,2);
	    $yr   = substr($time,0,4);
	    $time = mktime($hr,$mm,$ss,$mnth,$day,$yr);
	    return $time;
	}


	/**
	 * function role : To get the passage type
	 * param1 : passageID
	 * @return  string, type of the passage
	 * 
	 * */

	function getPassageType($passageID)
	{
		$this->dbEnglish->Select('passageType');
		$this->dbEnglish->from('passageMaster');
		$this->dbEnglish->where('passageID',$passageID);
		$query = $this->dbEnglish->get();
		$passageTypeInfo = $query->result_array();

		return $passageTypeInfo[0]['passageType'];
	}

	/**
	 * function role : To get the qcode details
	 * param1 : qcode
	 * @return  array, of qcode details
	 * 
	 * */

	function getQcodeDetails($qcode){
		$this->dbEnglish->Select('*');
		$this->dbEnglish->from('questions');
		$this->dbEnglish->where('qcode',$qcode);
		$query =  $this->dbEnglish->get();
		$qcodeDetailsArr = $query->row();
		
		return $qcodeDetailsArr;
	}
	
	/**
	 * function role : Fetch topic info of a question
	 * param1 : qcode
	 * @return  string, topicID for given qcode
	 * 
	 * */

	function getTopicInfo($qcode)
	{
		$this->dbEnglish->Select('topicID');
		$this->dbEnglish->from('questions');
		$this->dbEnglish->where('qcode',$qcode);
		$query = $this->dbEnglish->get();
		$topicInfo = $query->result_array();

		return $topicInfo[0]['topicID'];
	}

	/**
	 * function role : Save the time taken for explaination
	 * param1 : userID
	 * param2 : qcode
	 * param3 : timeTakenExpln which need to be saved
	 * param4 : passageID , passageID to get passage content type
	 * @return  none
	 * 
	 * */

	function saveTimeTakenForExpln($userID,$qcode,$timeTakenExpln,$passageID=0)
	{
		$psgContentType=$this->getPassageType($passageID);	

		$data = array(
				'timeTakenExpln' => $timeTakenExpln
			    );
				
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('qcode',$qcode);
		$this->dbEnglish->where('scoringID IS NOT NULL');


		$this->dbEnglish->limit(1);
		$this->dbEnglish->update($this->questionAttemptClassTbl, $data); 
	}


	/**
	 * function role : Fetch total time taken to read the passage 
	 * param1 : userID
	 * param2 : passageId
	 * @return  array , of get passage total time taken
	 * 
	 * */

	function getPassageAttemptTotalTime($userID, $passageID)
	{
		$this->dbEnglish->Select('totalTime');
		$this->dbEnglish->from('passageAttempt');
		$this->dbEnglish->where('passageID', $passageID);
		$this->dbEnglish->where('userID', $userID);

		$query = $this->dbEnglish->get();
		$getPassageAttemptTime = $query->result_array();
		$getPassageAttemptTime[0]['totalTime'] = round($getPassageAttemptTime[0]['totalTime'] / 60, 1); //convert seconds into minutes
		return $getPassageAttemptTime;
	}

	/**
	 * function role : To correct attempt count mismatch of userLevelAndAccLog table for reading,listening and free questions 
	 * @return : none
	 * 
	 * */

	function correctAccLogMismatchCount(){
		$contentTypeArr=array(readingContentTypeConst,listeningContentTypeConst,freeQuesContentTypeConst);
		$userAttemptedPsgs=array();
		foreach($contentTypeArr as $key=>$value)
		{
			$this->dbEnglish->Select('quesPsgAttemptCount,scoringID,contentType,level');
			$this->dbEnglish->from('userLevelAndAccuracyLog');
			$this->dbEnglish->where('userID', $this->user_id);
			$this->dbEnglish->where('contentType', $value);
			$this->dbEnglish->order_by('scoringID','desc');
			$this->dbEnglish->limit(1);
			$query = $this->dbEnglish->get();
			//print $this->dbEnglish->last_query();

			$getAllAccLogEntriesRes = $query->result_array();
			foreach($getAllAccLogEntriesRes as $key=>$value)
			{
				if(($value['contentType'] == readingContentTypeConst && !$this->session->userdata('isReadingContExhaust')) || ($value['contentType'] == listeningContentTypeConst && !$this->session->userdata('isListeningContExhaust'))){
					$this->getUserAttemptedPassage($this->user_id,false);
					if($value['contentType'] == readingContentTypeConst)
						$userAttemptedPsgs = $this->currReadingScoreIDPsgArr;
					else
						$userAttemptedPsgs = $this->currListeningScoreIDPsgArr;
					
					if($value['quesPsgAttemptCount']!=count($userAttemptedPsgs)){
						$quesPsgAttemptCount=count($userAttemptedPsgs);
						$data=array('quesPsgAttemptCount'=> $quesPsgAttemptCount);
						$this->dbEnglish->where('scoringID',$value['scoringID']);
						$this->dbEnglish->update('userLevelAndAccuracyLog', $data);
					}
				}
				else if($value['contentType'] == freeQuesContentTypeConst){
					$this->getPreviousAttemptedNCQQues($this->user_id,$value['level']-gradeScallingConst,$value['scoringID']);
					if($value['quesPsgAttemptCount']!=count($this->currFreeQuesScoreIDPsgArr) && !$this->session->userdata('isFreeQuesContentExhaust')){
						$quesPsgAttemptCount=count($this->currFreeQuesScoreIDPsgArr);
						$data=array('quesPsgAttemptCount'=> $quesPsgAttemptCount);
						$this->dbEnglish->where('scoringID',$value['scoringID']);
						$this->dbEnglish->update('userLevelAndAccuracyLog', $data);
					}
				}
			
			}	
		}
	}



	
	/**
	 * function role : Fetch total time taken to read the passage 
	 * param1 : array which need to be updated
	 * param2 : value which need to be pushed in array
	 * param3 : session variable name
	 * @return  none
	 * 
	 * */
	
	/**
	 * function role : To check if the word is junk 
	 * param1 : For word which need to be checked
	 * param2 : frequency lower limit
	 * param3 : three word frequency
	 * @return  boolean , 1 = is junk , 0 = is not junk
	 * 
	 * */

	function isthiswordJunk($word, $frequency_lower_limit, $three_word_frequency){
		if((strlen($word) < 3) OR (strcasecmp($word,"yes") == 0) OR (strcasecmp($word,"bad") == 0) OR (strcasecmp($word,"nil") == 0) OR (strcasecmp($word,"okay") == 0)){
			return 0;
		}else{
			$check_numeric = 0;
			$one_word = $word;
			$array_non_letters = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'fuck', 'bitch','sex');
			foreach($array_non_letters as $each_non_letter){
				if(stripos($one_word,$each_non_letter)==true){
					$check_numeric += 1;
				}
			}
			if(($check_numeric>0) OR (strlen($one_word) > 18)){
				return 1;
			}else{
				$word_frequency_sum = 0;
				$each_word_array = array();
				for ($i = 0; $i <= ((strlen($one_word))-3); $i++){
					array_push($each_word_array,substr($one_word, $i, 3));
				}
				foreach($each_word_array as $part_each_word_all){
					$part_each_word = strtolower($part_each_word_all);
					$temp_count = 0;
					if(!empty($three_word_frequency[$part_each_word])){
						$temp_count = $three_word_frequency[$part_each_word];
					}
					$word_frequency_sum += $temp_count;
				}
				
				if($word_frequency_sum <= $frequency_lower_limit[strlen($one_word)]) {
					return 1;
				}else{
					return 0;
				}
			}
		}
	}
	
	/**
	 * function role : To check if the string is junk 
	 * param1 : comment , string which need to be checked
	 * @return  boolean , 1 = is junk , 0 = is not junk
	 * 
	 * */

	function isitjunk($comment){
		$frequency_lower_limit = array();
		$three_word_frequency = array();

		$frequency_lower_limit[3] = 50;
		$frequency_lower_limit[4] = 50;
		$frequency_lower_limit[5] = 100;
		$frequency_lower_limit[6] = 100;
		$frequency_lower_limit[7] = 200;
		$frequency_lower_limit[8] = 400;
		$frequency_lower_limit[9] = 800;
		$frequency_lower_limit[10] = 1200;
		$frequency_lower_limit[11] = 1800;
		$frequency_lower_limit[12] = 2400;
		$frequency_lower_limit[13] = 2800;
		$frequency_lower_limit[14] = 3000;
		$frequency_lower_limit[15] = 3400;
		$frequency_lower_limit[16] = 3500;
		$frequency_lower_limit[17] = 4400;
		$frequency_lower_limit[18] = 4600;

		$query_get_entropy_sql = $this->dbEnglish->query("select a.threeLetterWord, a.frequency
									from threeletterwordfrequency a
									order by a.threeLetterWord asc");
		$data_get_entropy = $query_get_entropy_sql->result_array();
		
		foreach($data_get_entropy as $selected_three_word){
			$selected_three_word = array_values($selected_three_word);
			$one_three_word = $selected_three_word[0];
			$one_three_word_count = $selected_three_word[1];
			$three_word_frequency[$one_three_word] = $one_three_word_count;
		}
		$onc_comment_strp_tags = strip_tags($comment);
		$one_comment_need_editing = $onc_comment_strp_tags;
		$one_comment = str_replace(' - ','',$one_comment_need_editing);
		$one_comment = str_replace('.',' ',$one_comment);
		$one_comment = str_replace('?',' ',$one_comment);
		$one_comment = str_replace('!',' ',$one_comment);
		$one_comment = str_replace('&nbsp;',' ',$one_comment);

		$response = trim($one_comment);
		$wordArray = preg_split('/\s+/',$response, -1, PREG_SPLIT_NO_EMPTY);
		$lengthOfComment = count($wordArray);
		$num_row = 0;
		
		foreach($wordArray as $each_word){
			$num_row += $this->isthiswordJunk($each_word, $frequency_lower_limit, $three_word_frequency);
		}
		/*added by nivedita*/
		$junkValueArr = array();
		$junkValueArr['comparedWithValue'] = 0.5*$lengthOfComment;
		$junkValueArr['junkValue']         = $num_row;
		$junkValueArr['userResponse']      = $comment;
		/*end*/
		if($num_row >= (0.5*$lengthOfComment)){
			$junkValueArr['isJunk']            = 1;
			//return 1;
			return $junkValueArr;
		}else{
			$junkValueArr['isJunk']            = 0;
			//return 0;
			return $junkValueArr;
		}
	}

	function isItProfanity($data)
	{
		$userEssay = strip_tags($data);
		if(preg_match('/fuck|(\b|_)sex(\b|_)|asshole|(\b|_)ass(\b|_)|bitch|kutte/i', $userEssay))
			return 1;
		else
			return 0;
	}


	/**
	 * function role : To log the junk in db
	 * param1 : data
	 * 
	 * */
	function logJunk($data)
	{
		if(!empty($data) && count($data) > 0)
		{
			$data_insert = array(
			   'userID'         => $data['userID'],
			   'itemID'         => $data['itemID'],
			   'page'           => $data['page'],
			   'userResponse'   => $data['userResponse'],
			   'junkValue'      => $data['junkValue'],
			   'thresholdValue' => $data['comparedWithValue']
			);

			$this->dbEnglish->insert('junkDataLog', $data_insert);
		}
	}
	

function updateUserPassageAttemptStatus($userID)
{	
    	$userReadingStatus = $this->session->userdata('isReadingContExhaust');
    	$userListeningStatus = $this->session->userdata('isListeningContExhaust');

    	if($userReadingStatus)
    		$checkForReadingExhaust = true;
    	else
    		$checkForReadingExhaust = false;

    	if($userListeningStatus)
    		$checkForListeningExhaust = true;
    	else
    		$checkForListeningExhaust = false;

    	$tableName   =  "userLevelAndAccuracyLog";
    	$fieldName   =  "scoringID";
    	$contentType =  "Passage_Reading";

    	if($checkForReadingExhaust)
		{
			$tableName   =  "userexhaustionlogiclog";
			$fieldName   =   "exScoringID";
		}
	
		$this->dbEnglish->Select($fieldName);
	    $this->dbEnglish->from($tableName);
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('contentType',$contentType);
		$this->dbEnglish->order_by('lastModified','desc');
		$this->dbEnglish->limit(1);
		$query = $this->dbEnglish->get();
		$currentReadingScoringID = $query->result_array();
		$readingScoringID = $currentReadingScoringID[0][$fieldName];


		$tableName   =  "userLevelAndAccuracyLog";
    	$fieldName   =  "scoringID";
    	$contentType =  "Passage_Conversation";


		if($checkForListeningExhaust)
		{
			$tableName   =  "userexhaustionlogiclog";
    		$fieldName   =  "exScoringID";    	
		}
		
		$this->dbEnglish->Select($fieldName);
		$this->dbEnglish->from($tableName);
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('contentType',$contentType);
		$this->dbEnglish->order_by('lastModified','desc');
		$this->dbEnglish->limit(1);
		$query = $this->dbEnglish->get();
		$currentListeningScoringID = $query->result_array();
		$listeningScoringID = $currentListeningScoringID[0][$fieldName];

		$this->dbEnglish->Select("passageID");
		$this->dbEnglish->from("passageAttempt");
		$this->dbEnglish->where('userID',$userID);
		if($checkForReadingExhaust)
			$this->dbEnglish->where('exScoringID',$readingScoringID);
		else
			$this->dbEnglish->where('scoringID',$readingScoringID);

		$this->dbEnglish->where('completed','1');
		$query = $this->dbEnglish->get();

		if ($query->num_rows())
		{
			$readingPsgArr = $query->result_array();
			foreach ($readingPsgArr as $key => $value)
            {
            	if(!$this->checkPassageCompleted($this->questionAttemptClassTbl,$userID,$value['passageID'],$readingScoringID,$checkForReadingExhaust))
            	{
            		$passageID = $value['passageID'];
            		
            		$this->dbEnglish->set('completed', '2');
            		$this->dbEnglish->set('lastModified', 'lastModified', FALSE);
					$this->dbEnglish->where('userID', $userID);
					$this->dbEnglish->where('passageID', $passageID);
					if($checkForReadingExhaust)
						$this->dbEnglish->where('exScoringID', $readingScoringID);
					else
						$this->dbEnglish->where('scoringID', $readingScoringID);

					$this->dbEnglish->update('passageAttempt');
            	}
            }

		}
		

		$this->dbEnglish->Select("passageID");
		$this->dbEnglish->from("passageAttempt");
		$this->dbEnglish->where('userID',$userID);
		if($checkForListeningExhaust)
			$this->dbEnglish->where('exScoringID',$listeningScoringID);
		else
			$this->dbEnglish->where('scoringID',$listeningScoringID);

		$this->dbEnglish->where('completed','1');
		$query = $this->dbEnglish->get();

		if ($query->num_rows()) 
		{
			$listeningPsgArr = $query->result_array();

			foreach ($listeningPsgArr as $key => $value)
            {
            	if(!$this->checkPassageCompleted($this->questionAttemptClassTbl,$userID,$value['passageID'],$listeningScoringID,$checkForListeningExhaust))
            	{
            		$passageID = $value['passageID'];

            		$this->dbEnglish->set('completed', '2');
            		$this->dbEnglish->set('lastModified', 'lastModified', FALSE);
					$this->dbEnglish->where('userID', $userID);
					$this->dbEnglish->where('passageID', $passageID);

					if($checkForListeningExhaust)
						$this->dbEnglish->where('exScoringID', $listeningScoringID);
					else
						$this->dbEnglish->where('scoringID', $listeningScoringID);
					
					 $this->dbEnglish->update('passageAttempt');
					
            	}
            }
		}

}


 function checkPassageCompleted($questionAttemptTable, $userID, $passageID, $scoringID, $chkForAdaptive=true)
	{	
		$this->dbEnglish->Select('group_concat(distinct(qcode)) quesAttemptSrno');
		$this->dbEnglish->from($questionAttemptTable);
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('passageID',$passageID);
		
		if($chkForAdaptive)
			$this->dbEnglish->where('exScoringID',$scoringID);
		else
			$this->dbEnglish->where('scoringID', $scoringID);
		

		$query = $this->dbEnglish->get();
		$lastAttemptedPassageQuesInfo = $query->result_array();
		$quesAttemptSrno = $lastAttemptedPassageQuesInfo[0]['quesAttemptSrno'];

		$this->dbEnglish->Select('count(qcode) as qcodes');
		$this->dbEnglish->from('questions');
		$this->dbEnglish->where('passageID',$passageID);
		$this->dbEnglish->where('status',liveQuestionsStaus);
		$query = $this->dbEnglish->get();
		
		$passageTotalQues = $query->result_array();
		
		if($quesAttemptSrno == null || $quesAttemptSrno == "")
			$noOfQuesDoneInPassage = 0;
		else
			$noOfQuesDoneInPassage = count(explode(',', $quesAttemptSrno));

		if($noOfQuesDoneInPassage < $this->maxQuestionsToBeGivenInPassage  && $noOfQuesDoneInPassage < $passageTotalQues[0]['qcodes'])
		{
			return 1;
		}	
		else
		{
			return 0;
		}

	}
	
	function getFreeQuesAttemptCount($userID,$qcode){
		$this->dbEnglish->Select('attemptCount');
		$this->dbEnglish->from($this->questionAttemptClassTbl);
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('qcode',$qcode);
		$this->dbEnglish->order_by('lastModified','desc');
		$this->dbEnglish->limit(1);
		$userCurrAttemptCountSql = $this->dbEnglish->get();
		if($userCurrAttemptCountSql->num_rows() > 0){
			$userCurrAttemptCount = $userCurrAttemptCountSql->row();
			return $userCurrAttemptCount->attemptCount;
		}
		else
		{
			return null;
		}
	}


	function sessionCheckUsersDays($userID) {

		$lastSessionQuery = "select datediff(CURDATE(), startTime) as days FROM sessionStatus WHERE userID = $userID order by lastModified ASC limit 1";
		$result = $this->dbEnglish->query($lastSessionQuery)->row();
		$days = ($result->days > 15) ? true : false;
		return $days;
	}

	function isRedirectToEssayWriter($userID){

		$checkNewuserSession = $this->sessionCheckUsersDays($userID);
		if($checkNewuserSession && $this->category == 'STUDENT') :
			$isRedirectToEssayWriterSQL = "SELECT count(*) as cnt FROM educatio_msenglish.ews_essayDetails WHERE userID = $userID AND lastModified BETWEEN (date_sub(now(),INTERVAL 15 DAY)) AND (now())";
			$isRedirectToEssayWriter = $this->dbEnglish->query($isRedirectToEssayWriterSQL)->row();
			// echo $this->dbEnglish->last_query();
			if($isRedirectToEssayWriter->cnt == 0)
			{
				return true;
			}
			return false;
		else :
			return false;
		endif;
	}
	
	
}

?>
