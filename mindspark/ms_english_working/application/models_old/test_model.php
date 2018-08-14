<?php
 	
error_reporting(0);
Class test_model extends MY_Model
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
	//public $currReadingExScoreIDPsgArr;
	//public $currListeningExScoreIDPsgArr;
	//public $currFreeQuesExScoreIDPsgArr;

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
		 //$this->currReadingExScoreIDPsgArr = array();
		 //$this->currListeningExScoreIDPsgArr = array();
		 //$this->currFreeQuesExScoreIDPsgArr = array();
		 
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

			/*if($this->session->userdata('currentContentType') == 'free_ques')
				$isPsgQnsPending = 0;*/
			
		}
		else
		{	
			$isPsgQnsPending=0;	
			$isPsgQnsPending=$this->lastCompletedPassageQuestionsPending($sessionID,$userID);
			$cct = $this->session->userdata('currentContentType');
			if(!$isPsgQnsPending && ($cct =="passage_ques"))         // check if  passage_ques of attempted passage is not set in userCurrentStatus
				{
					$passageIDArr = $this->getQcodePassageDetails($this->session->userdata('refID'));
					$data=array('currentContentType'=>"passage",'refID'=>$passageIDArr['qcodePassageID']);
					$this->session->set_userdata($data);
					
				}
		}
		/*END*/	

		if($isPsgQnsPending){
			
			// For handling currentContentType : passage_ques,passage,free_ques and refID : 0 issue 
			//$sessionTypeToShow=$this->studentsessionflow_model->getSessionTypeToShow($userID);
			if ($this->session->userdata('refID')==0 && $this->session->userdata('currentContentType') !='N/A' || $this->session->userdata('currentContentType')==currContentTypeFreeQuesConst){
				
				// below function will set sessionPassages or free question array next to load depending on the session type
				$sessionTypeToShow=$this->studentsessionflow_model->getSessionTypeToShow($userID);
				$sessionPassagesArr = $this->session->userdata('sessionPassages');
				$data = array('currentContentType'=>currContentTypePsgConst,'refID'=>$sessionPassagesArr[0],'completed'=>0);
				$this->session->set_userdata($data);
				$this->dbEnglish->where('userID',$userID);
				$this->dbEnglish->update('userCurrentStatus',$data);
			}
			$this->getsessionFlowData($sessionID,$userID,null,"Passage");				
		}
		else{
			
			// below function will set sessionPassages or free question array next to load depending on the session type
			$sessionTypeToShow=$this->studentsessionflow_model->getSessionTypeToShow($userID);
			$this->getsessionFlowData($sessionID,$userID,null,$sessionTypeToShow);		
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
					
					$sessionTypeToShow=$this->studentsessionflow_model->getSessionTypeToShow($userID);
					
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
				$sessionTypeToShow=$this->studentsessionflow_model->getSessionTypeToShow($userID);
				
				$this->getsessionFlowData($sessionID,$userID,$passageID,$sessionTypeToShow);		
			}
			
			$this->studentFlowArr['isContentExhaustedTeacher'] = $this->session->userdata('isContentExhaustedTeacher');
			return  $this->studentFlowArr;
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
		//print_r($this->session->all_userdata());
		//$sessionTypeToShow=$this->studentsessionflow_model->getSessionTypeToShow($userID);
		$this->getsessionFlowData($sessionID,$userID,null);

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
		//$currentContentType=$this->session->userdata('currentContentType');
		//$currentContentType='passage_ques';
		$sessionPsgTimeLimit=$this->session->userdata('sessionPsgTimeLimit');
		if($sessionTypeToShow == "Passage")
		{	
			
			if($currentContentType != "N/A"){	 

				if($currentContentType == currContentTypePsgConst){
					//print "IIII";
					//print $this->session->userdata('refID');

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
				else if($currentContentType == currContentTypePsgQuesConst){
					
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
			}		
			
			
			$studentFlowArr=$this->studentFlowArr;			
			$data = array('currentContentType' => $currentContentType,'refID' => $studentFlowArr['qID']);
			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->update('userCurrentStatus', $data);
			$this->session->set_userdata('currentContentType',$currentContentType);
		}
		else
		{
			if(count($this->session->userdata('sessionfreeQues')) == 0){
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
			$this->isIntroductionToBeGiven($userID,$sessionID,$refID);	
			$data = array('currentContentType' => $currentContentType,'refID' => $refID);
			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->update('userCurrentStatus', $data);
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
		
		$this->studentFlowArr['qType'] =  'passage';
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
	
	function getFirstSessionPassage($userID){
		
		$sessionPassages = $this->session->userdata('sessionPassages');
		
		$passageID = $sessionPassages[0];	
		
		$this->setCurrentPsgQuestions($userID,$passageID,true);
		
		$this->studentFlowArr['qType'] =  'passage';
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
		
		/*$attemptedPassageQuestionsArr = array(0=>'');
		if($chkAttemptedQues){
			$attemptedPassageQuestions = $this->getUserAttemptedPassageQuestions($userID,$passageID);
			if($attemptedPassageQuestions!="")
				$attemptedPassageQuestionsArr = explode(',', $attemptedPassageQuestions);
		}*/

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
		//print $this->dbEnglish->last_query();
		$psgQuestionsArr=array();
		
		foreach($psgQuestionsRes as $key=>$value)
		{
			array_push($psgQuestionsArr,$value['qcode']);
		}
		//print_r($psgQuestionsArr);
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

	function lastCompletedPassageQuestionsPending($sessionID,$userID,$passageID=false,$chkForAdaptive=true)
	{	
		if(!$passageID)				
		{
			/*nivedita*/
			if($this->category == 'ADMIN' || $this->category == 'TEACHER' || $this->category == 'School Admin')
			{

				$contentTypeDms = $this->session->userdata('currentContentType');
				/*if($contentTypeDms == 'passage_ques')
				{*/
					$level       = $this->session->userdata('passageLevel');
					$msLevel     = $level - 3;
					$diffRating = $level + 0.9;
					$pasgArrCon  = array();
					$pasgArrRead = array();
					$finalPsgArr = array();
					/*get conversation psg arr*/
					$this->dbEnglish->Select('passageID');
					$this->dbEnglish->from('passageMaster');
					$this->dbEnglish->where('passageType','conversation');
					$this->dbEnglish->where('status','7');
					$this->dbEnglish->where('msLevel',$msLevel);
					$query = $this->dbEnglish->get();
					$psgArrSql = $query->result_array();
					foreach ($psgArrSql as $key => $value) 
					{
						array_push($pasgArrCon, $value['passageID']);
					}
					/*end*/

					/*get reading psg arr*/
					$this->dbEnglish->Select('passageID');
					$this->dbEnglish->from('passageAdaptiveLogicParams');
					$this->dbEnglish->where('passageStatus','7');
					$this->dbEnglish->where("diffRating BETWEEN $level AND $diffRating");
					$query = $this->dbEnglish->get();
					$psgArrReadSql = $query->result_array();
					foreach ($psgArrReadSql as $key => $value) 
					{
						array_push($pasgArrRead, $value['passageID']);
					}
					/*end*/

					$finalPsgArr = array_merge($pasgArrCon,$pasgArrRead);

					$this->dbEnglish->Select('passageID');
					$this->dbEnglish->from('passageAttempt');
					$this->dbEnglish->where('userID',$userID);
					$this->dbEnglish->where_in('passageid',$finalPsgArr);
					$this->dbEnglish->order_by('lastModified','desc');
					$this->dbEnglish->limit(1);
					$query = $this->dbEnglish->get();
					$lastAttemptedPassageInfo = $query->result_array();
					
					if(count($lastAttemptedPassageInfo)==0)
						return 0;
					$passageID = $lastAttemptedPassageInfo[0]['passageID'];
				//}
			}//end
			else
			{
				$this->dbEnglish->Select('passageID');
				$this->dbEnglish->from('passageAttempt');
				$this->dbEnglish->where('userID',$userID);
				//$this->dbEnglish->where('completed = 1');
				$this->dbEnglish->order_by('lastModified','desc');
				$this->dbEnglish->limit(1);
				$query = $this->dbEnglish->get();


				//print $this->dbEnglish->last_query();
				$lastAttemptedPassageInfo = $query->result_array();
				if(count($lastAttemptedPassageInfo)==0)
					return 0;
				$passageID = $lastAttemptedPassageInfo[0]['passageID'];
			}
		}

	

		
		$psgContentType=$this->getPassageType($passageID);
		
		$this->dbEnglish->Select('group_concat(distinct(qcode)) quesAttemptSrno');
		$this->dbEnglish->from($this->questionAttemptClassTbl);
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('passageID',$passageID);
		
		if(($psgContentType == "Textual" || $psgContentType == "Illustrated") && ($this->session->userdata('isReadingContExhaust')) && $chkForAdaptive)
			$this->dbEnglish->where('exScoringID IS NOT NULL');
		else if(($psgContentType == "Conversation") && ($this->session->userdata('isListeningContExhaust')) && $chkForAdaptive)
			$this->dbEnglish->where('exScoringID IS NOT NULL');
		else	
			$this->dbEnglish->where('scoringID IS NOT NULL');

		$query = $this->dbEnglish->get();
		//print $this->dbEnglish->last_query();
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
	
	/**
	 * function role : Gets the next passage from the session passages array and sets it in the return array with other details
	 * param1 : userID
	 * param2 : sessionID
	 * param3 : PassageID of on going passage
	 * param4 : currentContentType , which is need to be set in userCurrentStatus and session variable  
	 * @return  none
	 * 
	 * */
	
	function getNextPassage($userID,$sessionID,$passageID,$currentContentType){
		$sessionPassages = $this->session->userdata('sessionPassages');
		if(count($sessionPassages) == 0)
		{
			getNextNonContextualQuestions($sessionID,$userID);
			return $this->studentFlowArr;
			exit;
		}	
		$index = array_search($passageID, $sessionPassages);
		unset($sessionPassages[$index]);
		$sessionPassages = array_values($sessionPassages);
		$this->session->set_userdata('sessionPassages', $sessionPassages);
		
		$this->studentFlowArr['qType'] =  'passage';
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
		
		if($isContentExhausted)
			$this->dbEnglish->where('exScoringID IS NOT NULL');
		else	
			$this->dbEnglish->where('scoringID IS NOT NULL');
		

		$query = $this->dbEnglish->get();
		$attemptedPassageIDArr = $query->result_array();
		//print $this->dbEnglish->last_query();
		foreach($attemptedPassageIDArr as $key=>$value)
		{
			//if(!$this->lastCompletedPassageQuestionsPending(null,$userID,$value['passageID'],$isContentExhausted)){
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
			//}
			//else
				//array_push($this->unCompletedPassageArrAdaptive,$value['passageID']);
		}	

		/*var_dump($allQuesCompletedPsgArr);
		exit;*/
		//if($this->session->userdata('currentContentType')==currContentTypePsgConst && $this->session->userdata('completed')==0)
				//array_push($allQuesCompletedPsgArr,$this->session->userdata('refID'));	

		if(count($allQuesCompletedPsgArr)>0)
			return implode(",",$allQuesCompletedPsgArr);				
		else 
			return "";
		

		/*$this->dbEnglish->Select('group_concat(passageID) as passages');
		$this->dbEnglish->from('passageAttempt');
		$this->dbEnglish->where('userID',$userID);
		$query = $this->dbEnglish->get();

		$attemptedPassageID = $query->result_array();

		if($attemptedPassageID[0]['passages']!=null)
			return $attemptedPassageID[0]['passages'];
		else 
			return "";*/
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
		//print $this->session->userdata('currentContentType');
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

	function savePassageAttempts($userID,$passageID,$currentPassagePart,$complete,$timeSpent,$sessionID)
	{
		$psgContentType=$this->getPassageType($passageID);

		if(($psgContentType == "Textual" || $psgContentType == "Illustrated") && ($this->session->userdata('isReadingContExhaust')))
			$exScoringID=$this->getOrSetPsgCurrExScoringID($userID,$psgContentType,$passageID);
		else if(($psgContentType == "Conversation") && ($this->session->userdata('isListeningContExhaust')))
			$exScoringID=$this->getOrSetPsgCurrExScoringID($userID,$psgContentType,$passageID);
		else
			$scoringID=$this->getOrSetPsgCurrScoringID($userID,$psgContentType,$passageID);	
		
		$this->dbEnglish->Select('passageAttemptID,totalTime');
		$this->dbEnglish->from('passageAttempt');
		$this->dbEnglish->where('passageID', $passageID);
		
		if(($psgContentType == "Textual" || $psgContentType == "Illustrated") && ($this->session->userdata('isReadingContExhaust')))
			$this->dbEnglish->where('exScoringID',$exScoringID);
		else if(($psgContentType == "Conversation") && ($this->session->userdata('isListeningContExhaust')))
			$this->dbEnglish->where('exScoringID',$exScoringID);
		else
			$this->dbEnglish->where('scoringID',$scoringID);
		
		$this->dbEnglish->where('userID', $userID);
		$query = $this->dbEnglish->get();
		//print $this->dbEnglish->last_query();
		$checkPassageAttempt = $query->result_array();

		if(count($checkPassageAttempt)>0)
		{
			$data = array(
			   'currentPassagePart' => $currentPassagePart,
			   'completed' => $complete,
			   'totalTime' => $checkPassageAttempt[0]['totalTime'] + $timeSpent,
			   'sessionID' => $sessionID
            );

			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->where('passageID', $passageID);

			if(($psgContentType == "Textual" || $psgContentType == "Illustrated") && ($this->session->userdata('isReadingContExhaust')))
				$this->dbEnglish->where('exScoringID',$exScoringID);
			else if(($psgContentType == "Conversation") && ($this->session->userdata('isListeningContExhaust')))
				$this->dbEnglish->where('exScoringID',$exScoringID);
			else
				$this->dbEnglish->where('scoringID',$scoringID);
		
			$this->dbEnglish->update('passageAttempt', $data);

			
			$this->passageAttemptID = $checkPassageAttempt[0]['passageAttemptID'];
		}
		else
		{
			$data = array(
				'userID' => $userID,
				'passageID' => $passageID,
				'sessionID' => $sessionID,
				'currentPassagePart' => $currentPassagePart,
				'completed' => $complete,
				'totalTime' => $timeSpent,
			);

			$psgContentType=$this->getPassageType($passageID);
				
			if(($psgContentType == "Textual" || $psgContentType == "Illustrated") && ($this->session->userdata('isReadingContExhaust')))
				$data['exScoringID'] = $exScoringID;	
			else if(($psgContentType == "Conversation") && ($this->session->userdata('isListeningContExhaust')))
				$data['exScoringID'] = $exScoringID;
			else
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

	function getUserAttemptedPassageQuestions($userID,$passageID)
	{
		$psgContentType=$this->getPassageType($passageID);

		if(($psgContentType == "Textual" || $psgContentType == "Illustrated") && ($this->session->userdata('isReadingContExhaust')))
			$exScoringID=$this->getOrSetPsgCurrExScoringID($userID,$psgContentType,$passageID);
		else if(($psgContentType == "Conversation") && ($this->session->userdata('isListeningContExhaust')))
			$exScoringID=$this->getOrSetPsgCurrExScoringID($userID,$psgContentType,$passageID);
		else
			$scoringID=$this->getOrSetPsgCurrScoringID($userID,$psgContentType,$passageID);	
		

		$isListeningContExhaust=$this->session->userdata('isListeningContExhaust');
		$isReadingContExhaust=$this->session->userdata('isReadingContExhaust');
		
		$this->dbEnglish->Select('group_concat(qcode) as qcodes');
		$this->dbEnglish->from($this->questionAttemptClassTbl);
		$this->dbEnglish->where('questionType','passageQues');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('passageID',$passageID);
		if(($psgContentType == "Textual" || $psgContentType == "Illustrated") && ($this->session->userdata('isReadingContExhaust')))
			$this->dbEnglish->where('exScoringID',$exScoringID);
		else if(($psgContentType == "Conversation") && ($this->session->userdata('isListeningContExhaust')))
			$this->dbEnglish->where('exScoringID',$exScoringID);
		else	
			$this->dbEnglish->where('scoringID',$scoringID);
			
		$query = $this->dbEnglish->get();
		//print $this->dbEnglish->last_query();
		$attemptedPassageQues = $query->result_array();

		if($attemptedPassageQues[0]['qcodes'] != null && $attemptedPassageQues[0]['qcodes'] != "")
			return $attemptedPassageQues[0]['qcodes'];
		else
			return "";
		

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
		
		
		if(($psgContentType == "Textual" || $psgContentType == "Illustrated") && ($this->session->userdata('isReadingContExhaust')))
			$exScoringID=$this->getOrSetPsgCurrExScoringID($userID,$psgContentType,$qcodePassageID);
		else if(($psgContentType == "Conversation") && ($this->session->userdata('isListeningContExhaust')))
			$exScoringID=$this->getOrSetPsgCurrExScoringID($userID,$psgContentType,$qcodePassageID);
		else
			$scoringID=$this->getOrSetPsgCurrScoringID($userID,$psgContentType,$qcodePassageID);	


		$data = array(
				'userID' => $userID,
				'qcode' => $qcode,
				'questionNo' => $questionNo,
				'timeTaken' => $timeTaken,
				'timeTakenExpln' => $timeTakenExpln,
				'correct' => $correct,
				'userResponse' => $userResponse,
				'questionType' => $questionType,
				'sessionID' => $sessionID,
				'passageID' => $qcodePassageID
		);

		

		if(($psgContentType == "Textual" || $psgContentType == "Illustrated") && ($this->session->userdata('isReadingContExhaust'))){
			$data['exScoringID'] = $exScoringID;	
		}
		else if(($psgContentType == "Conversation") && ($this->session->userdata('isListeningContExhaust'))){
			$data['exScoringID'] = $exScoringID;
		}
		else{
			$data['scoringID'] = $scoringID;
		}
				
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
				//print_r($this->session->userdata('sessionPassages'));
				$isLastCompletedPsgQnsPending = $this->lastCompletedPassageQuestionsPending($sessionID,$userID,0);

				if($isLastCompletedPsgQnsPending == 0)
				{
					/*ADDED BY NIVEDITA FOR UPDATING  THE FLAG TO 2 IN PASSAGE ATTEMPT IF ALL THE QUESTIONS ARE COMPLETED OF THAT PASSAGE*/
					if($qcodePassageID != 0)
					{
						$data = array('completed' => '2');
						$this->dbEnglish->where('userID', $userID);
						$this->dbEnglish->where('passageID', $qcodePassageID);
						$this->dbEnglish->update('passageAttempt', $data);
					}
					/*END*/
					$sessionPassagesArr =array();
					if(($psgContentType == "Textual" || $psgContentType == "Illustrated") && $this->session->userdata('isReadingContExhaust'))
						$this->passage_model->updateUserExhaustPsgLog($userID,$psgContentType,$qcode);	
					else if($psgContentType == "Conversation" && $this->session->userdata('isListeningContExhaust'))
						$this->passage_model->updateUserExhaustPsgLog($userID,$psgContentType,$qcode);	
					else
						$this->passage_model->updateUserLevelAndAccPsgLog($userID,$psgContentType,$qcode);
					$sessionPassagesArr = $this->session->userdata('sessionPassages');
					if((!$this->session->userdata('sessionPassages')) || (count($sessionPassagesArr) == 1)){
						//$this->passage_model->setNextPassageData($userID,$prevrefID);
						$this->passage_model->setNextSessionPassages($userID,$prevrefID);
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
		
		 //{
			$data = array(
				'sessionID' => $sessionID
			);
		
		$this->dbEnglish->where('userID', $userID);
		$this->dbEnglish->where('passageID', $qcodePassageID);

		if(($psgContentType == "Textual" || $psgContentType == "Illustrated") && ($this->session->userdata('isReadingContExhaust')))
			$this->dbEnglish->where('exScoringID IS NOT NULL');
		else if(($psgContentType == "Conversation") && ($this->session->userdata('isListeningContExhaust')))
			$this->dbEnglish->where('exScoringID IS NOT NULL');
		else
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

		if($valID!=''){
			if($this->session->userdata('isFreeQuesContentExhaust'))
				$this->dbEnglish->where('exScoringID IS NOT NULL');
			else
				$this->dbEnglish->where('scoringID IS NOT NULL');

		}
		
		$query = $this->dbEnglish->get();
		$resultArr = $query->result_array();
		//print $this->dbEnglish->last_query(); 
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
		//print_r($qcodeDetailsArr);
		if($this->isIntroductionDoneToday($userID,$qcodeDetailsArr->skillID) == 0)
		{
			if($this->isIntroductionMapped($userID,$sessionID,$qcodeDetailsArr) == 1)
				return 1;
			else
				return 0;
		}
		else
			return 0;
	}

	/**
	 * function role : Check if any introduction is done by the user today
	 * param1 : userID
	 * param2 : qcodeSkillID
	 * @return  1=> Intorduction done today , 0=> Intorduction not done today
	 * 
	 **/

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
	    //print $this->dbEnglish->last_query();
		//print_r($introductionAttemptArr);
		if($query->num_rows()  > 0)
		{
			$introductionAttemptArr = $query->result_array();
			$this->studentFlowArr['qType'] =  'introduction';
			$this->studentFlowArr['qID'] = $introductionAttemptArr[0]['igreid'];
			$this->studentFlowArr['info']['passagePartNo']  = null;
			$this->studentFlowArr['info']['passageType'] = null;
			$this->insertUserIGREAttempt($userID,$introductionAttemptArr[0]['igreid'],$sessionID);
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

		
		//print $this->session->userdata('isListeningContExhaust');
		if($this->session->userdata('isFreeQuesContentExhaust')){
			$exScoringID=$this->getOrSetFreeQuesCurrExScoringID($userID);
			$data['exScoringID'] = $exScoringID;
		}
		else{
			$scoringID=$this->getOrSetFreeQuesCurrScoringID($userID);	
			$data['scoringID'] = $scoringID;
		}

		$this->dbEnglish->set('attemptedDate', 'NOW()', FALSE);
		$this->dbEnglish->insert($this->questionAttemptClassTbl, $data);
	
		//test

		if($this->dbEnglish->affected_rows() == 1){

			 $refID=0;
             if($this->session->userdata('isFreeQuesContentExhaust'))
             	$this->freeques_model->updateUserExhaustLevelAndAccLog($userID,freeQuesContentTypeConst);
			 else
			 	$this->freeques_model->updateUserLevelAndAccFreeQuesLog($userID,freeQuesContentTypeConst);

			 $sessionFreeQuesArr = $this->session->userdata('sessionfreeQues');

			 if(($key = array_search($qcode, $sessionFreeQuesArr)) !== false) { 
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
			
			$groupSkillIDArr=array();		
			$skillGroupArr=array();
			$skillGroupID=0;			
			$groupSkillIDArr=explode(',',$qcodeSkillID);
			$getSkillGroupSql=$this->dbEnglish->query("select groupSkillID from groupSkillMaster where FIND_IN_SET((".$groupSkillIDArr[0]."), `skilID`)"); 
			$skillGroupArr = $getSkillGroupSql->row();
			$skillGroupID=$skillGroupArr->groupSkillID;
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
		
		if(($psgContentType == "Textual" || $psgContentType == "Illustrated") && ($this->session->userdata('isReadingContExhaust')))
			$this->dbEnglish->where('exScoringID IS NOT NULL');
		else if(($psgContentType == "Conversation") && ($this->session->userdata('isListeningContExhaust')))
			$this->dbEnglish->where('exScoringID IS NOT NULL');
		else
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
	 * function role : To correct attempt count mismatch of userexhaustionlogiclog table for reading,listening and free questions 
	 * @return : none
	 * 
	 * */

	function correctExhaustAccLogMismatchCount(){
		$contentTypeArr=array(readingContentTypeConst,listeningContentTypeConst,freeQuesContentTypeConst);
		$userAttemptedPsgs=array();
		$this->currReadingScoreIDPsgArr=array();
		$this->currListeningScoreIDPsgArr=array();
		foreach($contentTypeArr as $key=>$value)
		{
			$this->dbEnglish->Select('psgAttemptCount,exScoringID,contentType,level');
			$this->dbEnglish->from('userexhaustionlogiclog');
			$this->dbEnglish->where('userID', $this->user_id);
			$this->dbEnglish->where('contentType', $value);
			$this->dbEnglish->order_by('exScoringID','desc');
			$this->dbEnglish->limit(1);
			$query = $this->dbEnglish->get();
			
			$getAllAccLogEntriesRes = $query->result_array();
			foreach($getAllAccLogEntriesRes as $key=>$value)
			{
				if(($value['contentType'] == readingContentTypeConst && $this->session->userdata('isReadingContExhaust')) || ($value['contentType'] == listeningContentTypeConst && $this->session->userdata('isListeningContExhaust'))){
					$this->getUserAttemptedPassage($this->user_id,true);
					if($value['contentType'] == readingContentTypeConst)
						$userAttemptedPsgs = $this->currReadingScoreIDPsgArr;
					else
						$userAttemptedPsgs = $this->currListeningScoreIDPsgArr;
					
					if($value['psgAttemptCount']!=count($userAttemptedPsgs)){
						$quesPsgAttemptCount=count($userAttemptedPsgs);
						$data=array('psgAttemptCount'=> $quesPsgAttemptCount);
						$this->dbEnglish->where('exScoringID',$value['exScoringID']);
						$this->dbEnglish->update('userexhaustionlogiclog', $data);
					}
				}
				else if($value['contentType'] == freeQuesContentTypeConst){
					$this->getPreviousAttemptedNCQQues($this->user_id,$value['level']-gradeScallingConst,$value['exScoringID']);
					if($value['psgAttemptCount']!=count($this->currFreeQuesScoreIDPsgArr) && $this->session->userdata('isFreeQuesContentExhaust')){
						$quesPsgAttemptCount=count($this->currFreeQuesScoreIDPsgArr);
						$data=array('psgAttemptCount'=> $quesPsgAttemptCount);
						$this->dbEnglish->where('exScoringID',$value['exScoringID']);
						$this->dbEnglish->update('userexhaustionlogiclog', $data);
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
		//print $this->dbEnglish->last_query();
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
	
	
	/**
	@EI_start : Start of Functions for getting non contextual questions in flow
	 **/

		/*$islastCompletedPassageQuestionsPending = $this->lastCompletedPassageQuestionsPending($sessionID,$userID,$langLevel);
		$this->dbEnglish->Select('passageAttemptID,passageID,currentPassagePart');
		$this->dbEnglish->from('passageAttempt');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('completed <> 1');
		$this->dbEnglish->order_by('lastModified','desc');
		$this->dbEnglish->limit(1);
		$query = $this->dbEnglish->get();


		$unAttemptedPassage = $query->result_array();
		
		if(count($unAttemptedPassage)>=1)
		{
			$passageID = $unAttemptedPassage[0]['passageID'];
			$this->studentFlowArr['qType'] =  'passage';
			$this->studentFlowArr['qID'] = $passageID;
			$this->studentFlowArr['info']['passagePartNo']  = $unAttemptedPassage[0]['currentPassagePart'];
			$this->studentFlowArr['info']['passageType']  = $this->getPassageType($passageID);
		}
		else if($islastCompletedPassageQuestionsPending==1)
		{
			$this->getPassageQuestion($sessionID,$userID,$langLevel);
		}
		else if($this->getQuesTypeToBeGiven($userID)=="NCQ" || $this->getMaxPassagesAttemptedToday($userID) >= 4)
		{
			$this->getNonContextualQuestions($sessionID,$userID,$langLevel);	
		}
		else 
		{
			
			$this->studentFlowArr['qType'] =  'passage';
			$this->studentFlowArr['qID'] = $this->passageDetailsToBeGiven[0]['passageID'];
			$this->studentFlowArr['info']['passagePartNo']  = null;
			$this->studentFlowArr['info']['passageType']  = $this->passageDetailsToBeGiven[0]['passageType'];
		}

		*/	
	
	
	
	/**
	 * function role : Return information regarding more non contextual questions based on time and attempts
	 * param1 : sessionID
	 * param2 : userID
	 * param3 : Language level of the user
	 * @return  none
	 * 
	 * */

	/*function getNonContextualQuestions($sessionID,$userID,$langLevel)
	{

		if($this->getQuesTypeToBeGiven($userID)=="Passage" && $this->getMaxPassagesAttemptedToday($userID) < 4)
		{
			$this->studentFlowArr = array();
			$this->getInFlowPassageID($sessionID,$userID,$langLevel);
			$this->studentFlowArr['qType'] =  'passage';
			$this->studentFlowArr['qID'] = $this->passageDetailsToBeGiven[0]['passageID'];
			$this->studentFlowArr['info']['passagePartNo']  = null;
			$this->studentFlowArr['info']['passageType']  = $this->passageDetailsToBeGiven[0]['passageType'];	
		}
		else
		{
			$this->getNCQInfo($userID);

			$this->getPreviousSkillSubSkill($userID);

			$this->getNextSkillSubSkill($userID);

			$this->getPreviousAttemptedNCQQues($userID);
				
			$this->getNextNCQQuestionFn($userID,$sessionID);
			
			$this->isIntroductionToBeGiven($userID,$sessionID);
		}
	}*/

	/**
	 * function role : Get skillID , subskillID distribution for non contextual questions in mindspark english 
	 * param1 : userID
	 * @return  array, subskillID distribution skill wise 
	 * 
	 * */

	/*function getNCQInfo($userID)
	{

		$this->dbEnglish->Select('count(distinct qcode) as count');
		$this->dbEnglish->from('questionAttempt');
		$this->dbEnglish->where('userID', $userID);
		$this->dbEnglish->where('questionType','freeQues');
		$query = $this->dbEnglish->get();
		$quesCountInfo = $query->result_array();

		$this->questionAttemptsCount = $quesCountInfo[0]['count'];

		if($this->questionAttemptsCount % 6 < 3)
			$this->NCQType = 1;
		else
			$this->NCQType = 2;

		if($this->NCQType==1)
		{
			$this->dbEnglish->Select('B.skillID,group_concat(A.subSkillID) as subSkillIDs');
			$this->dbEnglish->from('skillMaster B');
			// $this->dbEnglish->from('skillMaster B');
			$this->dbEnglish->join('subSkillMaster A', 'A.skillID = B.skillID', 'left');
			// $this->dbEnglish->where('A.skillID = B.skillID');
			$this->dbEnglish->where('B.topicID',1);
			$this->dbEnglish->group_by("B.skillID"); 
			$this->dbEnglish->order_by('B.skillID','asc');
			$query = $this->dbEnglish->get();
			$NCQInfoGrammarArr = $query->result_array();

			foreach ($NCQInfoGrammarArr as $key => $value) {
				unset($subSkillArr);
				if($NCQInfoGrammarArr[$key]['subSkillIDs']!=null)
				{
					$subSkillArr = "0".",";
					$subSkillArr.=trim($NCQInfoGrammarArr[$key]['subSkillIDs']);
				}
				else
					$subSkillArr = "0";

				$this->NCQMasterArr[trim($NCQInfoGrammarArr[$key]['skillID'])] = explode(',',$subSkillArr); 
			}
		}
		else
		{

			$this->dbEnglish->Select('B.skillID,group_concat(A.subSkillID) as subSkillIDs');
			$this->dbEnglish->from('skillMaster B');
			// $this->dbEnglish->from('skillMaster B');
			$this->dbEnglish->join('subSkillMaster A', 'A.skillID = B.skillID', 'left');
			// $this->dbEnglish->where('A.skillID = B.skillID');
			$this->dbEnglish->where('B.topicID',2);
			$this->dbEnglish->group_by("B.skillID"); 
			$this->dbEnglish->order_by('B.skillID','asc');
			$query = $this->dbEnglish->get();
			$NCQInfoVocabArr = $query->result_array();

			foreach ($NCQInfoVocabArr as $key => $value) {
				unset($subSkillArr);
				

				if($NCQInfoVocabArr[$key]['subSkillIDs']!=null)
				{
					$subSkillArr = "0".",";
					$subSkillArr.=trim($NCQInfoVocabArr[$key]['subSkillIDs']);
				}
				else
					$subSkillArr = "0";

				$this->NCQMasterArr[trim($NCQInfoVocabArr[$key]['skillID'])] = explode(',',$subSkillArr); 
			}
		}

	}

	/**
	 * function role : Get previous skillID , subskillID attmpted by user
	 * param1 : userID
	 * @return  none
	 * 
	 * */

	/*
	function getPreviousSkillSubSkill($userID)
	{
		$this->dbEnglish->Select('skillID,subSkillID');
		$this->dbEnglish->from('freeQtypeAttempts');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('topicID',$this->NCQType);
		$this->dbEnglish->order_by('lastModified','desc');
		$this->dbEnglish->limit(1);
		$query = $this->dbEnglish->get();
		$previousAttemptedNCQInfo = $query->result_array();

		if(count($previousAttemptedNCQInfo)>0)
		{
			$this->previousSkillID = $previousAttemptedNCQInfo[0]['skillID'];
			$this->previousSubSkillID = $previousAttemptedNCQInfo[0]['subSkillID'];	
		}
		else
		{
			if($this->NCQType==1)
				$this->previousSkillID = 1;
			else
				$this->previousSkillID = 18;

			$this->previousSubSkillID = 0;	
		}
		
	}

	/**
	 * function role : Get next skillID , subskillID to be given to user
	 * param1 : userID
	 * @return none
	 * 
	 * */

	/*function getNextSkillSubSkill($userID,$from="")
	{
		//echo $this->previousSkillID."<br>";
		//echo $this->previousSubSkillID."<br>";
		//echo $from."<br>";
		if($this->previousSkillID == 1 && $this->previousSubSkillID == 0 && $from!='getNextNCQQuestion' && $this->questionAttemptsCount==0)
		{	
			$this->skillID = 1;
			$this->subSkillID = 0;
		}
		else if($this->previousSkillID == 18 && $this->previousSubSkillID == 0 && $from!='getNextNCQQuestion' && $this->questionAttemptsCount==0)
		{
			$this->skillID = 18;
			$this->subSkillID = 0;
		}

		else
		{
			foreach ($this->NCQMasterArr as $skillValue => $subSkillArr) {
				foreach ($subSkillArr as $subSkillKey => $subSkillValue) {
					if($this->previousSkillID==$skillValue &&  $this->previousSubSkillID==$subSkillValue)
					{
						if( array_key_exists($skillValue,$this->NCQMasterArr) && array_key_exists($subSkillKey+1,$this->NCQMasterArr[$skillValue]) )
						{
							// echo "in"."<br>";
							// exit;
							$this->skillID = $skillValue;
							$this->subSkillID = $this->NCQMasterArr[$skillValue][$subSkillKey+1];

							// echo $this->skillID."<br>";
							// echo $this->subSkillID."<br>";
							// echo "<br> <br>";
						}
						else if( array_key_exists($skillValue+1,$this->NCQMasterArr) )
						{
							//  echo "2"."<br>";
							// exit;
							$this->skillID = $skillValue+1;
							$this->subSkillID = 0;
						}
						else
						{
							// echo "3"."<br>";
							// exit;
							if($this->NCQType==1)
								$this->skillID = 1;
							else
								$this->skillID = 18;

							$this->subSkillID = 0;
						}
					}
				}
			}
		}

	}*/
	
	
	/**
	 * function role : Get skill,subSkill information for the passed qcode
	 * param1 : qcode
	 * @return  array, skill , subSKill information
	 * 
	 **/

	/*function getQcodeSKillValues($qcode)
	{
		$this->dbEnglish->Select('skillID,subSkillID');
		$this->dbEnglish->from('questions');
		$this->dbEnglish->where('qcode',$qcode);
		$query = $this->dbEnglish->get();
		$qcodeSKillValues = $query->result_array();

		return $qcodeSKillValues[0];
	}
	*/
	
	/**
	 @EI_End : End of Time based functions
	 **/

		
	
	
	

	/**
	@EI_end : End of Functions for getting non contextual questions in flow
	 **/





		

	/**
	 @EI_start : Start of Time based functions
	 **/




	/**
	 @EI_start : Start of Helper functions
	 **/

	 /**
	 * function role : Fetch question to be given to user. This for repeating same logic flow after every 30 mins
	 * param1 : userID
	 * @return  string, Content type
	 * 
	 * */

	 /*function getQuesTypeToBeGiven($userID)
	 {
		if( $this->getTimeSpentToday($userID) > ($this->maxContextualContentTimePeriod+30) )
			return "NCQ";
		else if( $this->getTimeSpentToday($userID) > ($this->maxContextualContentTimePeriod+15) )
			return "Passage";
		else if( $this->getTimeSpentToday($userID) > $this->maxContextualContentTimePeriod )
	 		return "NCQ";

	 }

	 /**
	 * function role : Fetch passage type for passage. This is for giving user different passage types according to levelwise flow.
	 * param1 : passageType
	 * @return  string, Passage type
	 * 
	 * */	

	/**
	 * function role : Check if a question is tagged to multiple skills/subSkills
	 * param1 : qcode
	 * @return  boolean, 1=>yes , 0=>no
	 * 
	 * */

	/*function isQuestionMultiTagged($qcode)
	{
		$this->dbEnglish->Select('skillID,subSkillID');
		$this->dbEnglish->from('questions');
		$this->dbEnglish->where('qcode',$qcode);
		$query = $this->dbEnglish->get();
		$tagInfo = $query->result_array();

		if (strpos($tagInfo[0]['skillID'],',') !== false || strpos($tagInfo[0]['subSkillID'],',') !== false) 
		    return 1;
		else
			return 0;
	}*/

	/**
	 * function role : Check if a question is attempted by user
	 * param1 : userID
	 * param2 : qcode
	 * @return  boolean, 1=>yes , 0=>no
	 * 
	 * */

	/*function isQcodeAttempted($userID,$qcode)
	{
		$this->dbEnglish->Select('qcode');
		$this->dbEnglish->from('questionAttempt');
		$this->dbEnglish->where('qcode',$qcode);
		$this->dbEnglish->where('userID',$userID);
		$query = $this->dbEnglish->get();
		$attemptInfo = $query->result_array();

		if(count($attemptInfo) > 0)
			return 1;
		else
			return 0;
	}
	
	/**
	 * function role : Return next passage details given to the user according to language level wise predifined passage flow
	 * param1 : sessionID
	 * param2 : userID
	 * param3 : Language level of the user
	 * @return  none
	 * 
	 * */

	/*function getInFlowPassageID($sessionID,$userID,$langLevel,$key=-1)
	{
		$attemptedPassageIDArr = array(0=>'');
		$this->dbEnglish->where('userID', $userID);
		$this->dbEnglish->from('passageAttempt');
		$passageAttemptsCount = $this->dbEnglish->count_all_results();

		$attemptedPassageID = $this->getUserAttemptedPassage($userID);

		if($attemptedPassageID!="")
		$attemptedPassageIDArr = explode(',', $attemptedPassageID);

		$passageFlowRatio =  array(
			0=>'Textual',
			1=>'Conversation',
			2=>'Illustrated',
			3=>'Conversation',
			4=>'Textual'
		);

		if($key==-1)
		{
			$key =  $passageAttemptsCount % 5;
		}
		

		$passageTypeToBeGiven = $passageFlowRatio[$key];


		$this->dbEnglish->Select('passageID,passageType');
		$this->dbEnglish->from('passageMaster');
		$this->dbEnglish->where('passageType',$passageTypeToBeGiven);
		$this->dbEnglish->where('status',$this->livePassageStatus);
		$this->dbEnglish->where_in('msLevel',$this->msLevel);
		$this->dbEnglish->where_in('msLevel',$this->msLevel);
		$this->dbEnglish->where_not_in('passageID', $attemptedPassageIDArr);
		$this->dbEnglish->order_by('passageID','asc');

		$query = $this->dbEnglish->get();

		$this->passageDetailsToBeGiven = $query->result_array();
		// Danger -- Please check the logic for selecting passages where alert is show on interface as check the network.
		if(count($this->passageDetailsToBeGiven)<=0)
		{
			$key = ($key+1)%5;
			$this->getInFlowPassageID($sessionID,$userID,$langLevel,$key);
		}
	}*/

	/**
	 * function role : List of all passages attempted by user
	 * param1 : userID
	 * @return  string , List of passages
	 * 
	 * */

	

	/**  
	@EI_start : Start of Saving passage attempt functions
	 **/

	/**
	 * function role : Save passage attempt details for users. 
	 * param1 : userID
	 * param2 : passageID
	 * param3 : current passage part where user is in
	 * param4 : flag for completition
	 * param5 : time spent by user on passage
	 * param6 : sessionID
	 * @return  none
	 * 
	 * */

	

	/**  
	@EI_start : Start of Saving passage attempt functions
	 **/

	/**
	@EI_end : End of Functions for getting passages in flow
	 **/

	





	/**
	@EI_start : Start of Functions for getting passage questions
	 **/

	/**
	 * function role : Returns information regading more new passage or non contextual questions based on time and attempts
	 * param1 : sessionID
	 * param2 : userID
	 * param3 : Language leve of the user
	 * param4 : passageID
	 * @return  none
	 * 
	 * */

	/*function getPassageQuestion($sessionID,$userID,$langLevel,$passageID="")
	{

		$attemptedPassageQuestionsArr = array(0=>'');
		$attemptedPassageQuestions = $this->getUserAttemptedPassageQuestions($sessionID,$userID,$langLevel,$passageID);
		if($attemptedPassageQuestions!="")
			$attemptedPassageQuestionsArr = explode(',', $attemptedPassageQuestions);

		$this->dbEnglish->_protect_identifiers = FALSE;
		$this->dbEnglish->Select('qcode');
		$this->dbEnglish->from('questions');
		$this->dbEnglish->where('passageID',$passageID);
		$this->dbEnglish->where('status',$this->liveQuestionsStaus);
		$this->dbEnglish->where_not_in('qcode',$attemptedPassageQuestionsArr);
		$this->dbEnglish->where_in('msLevel',$this->msLevel);
		$this->dbEnglish->order_by("FIELD(qType,'openEnded')");
		$this->dbEnglish->limit(1);
		$query = $this->dbEnglish->get();
		$passageQuesToBeGiven = $query->result_array();
		$this->dbEnglish->_protect_identifiers = TRUE;

		if(count($passageQuesToBeGiven)>0 && count($attemptedPassageQuestionsArr)<$this->maxQuestionsToBeGivenInPassage)
		{
			$this->studentFlowArr = array();
			$this->studentFlowArr['qType'] =  'passageQues';
			$this->studentFlowArr['qID'] = $passageQuesToBeGiven[0]['qcode'];
			$this->studentFlowArr['info']['passagePartNo']  = null;
			$this->studentFlowArr['info']['passageType'] = null;
		}
		else
		{

			if($this->getQuesTypeToBeGiven($userID)=="NCQ")
			{
				$this->getNonContextualQuestions($sessionID,$userID,$langLevel);	
			}
			else 
			{
				$this->studentFlowArr = array();
				$this->getInFlowPassageID($sessionID,$userID,$langLevel);
				$this->studentFlowArr['qType'] =  'passage';
				$this->studentFlowArr['qID'] = $this->passageDetailsToBeGiven[0]['passageID'];
				$this->studentFlowArr['info']['passagePartNo']  = null;
				$this->studentFlowArr['info']['passageType']  = $this->passageDetailsToBeGiven[0]['passageType'];
			}


		}
			// if($this->getQuesTypeToBeGiven($userID)!="NCQ" && $this->getMaxPassagesAttemptedToday($userID) < 3)
			// 	 	$this->getPassage($sessionID,$userID,$langLevel);
			// else
			// 		$this->getNonContextualQuestions($sessionID,$userID,$langLevel);
		
	}*/

	
	
	/**
	 @EI_end : End of Helper functions
	 **/
}

?>
