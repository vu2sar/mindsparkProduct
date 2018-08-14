<?php


Class Studentsessionflow_model extends MY_Model
{
	
	public function __construct() {
		 parent::__construct();
		 $this->dbEnglish = $this->load->database('mindspark_english',TRUE);
		 $this->load->library('session');
		 $this->load->model('Language/passage_model');
		 $this->load->model('Language/freeques_model');
		 $this->load->model('Language/speaking_model');
		 
	}

	/**
	 * function description : returns the content type and content quantity from contentFlowMaster
	 * param1  : orderNo
	 * @return  array of values
	 * 
	 * */
	public function getContentDetailsFromOrder($order){
		$templateID    = $this->session->userdata('templateID');
		$this->dbEnglish->Select('contentType,contentQuantity');
		$this->dbEnglish->from('contentFlowMaster');
		$this->dbEnglish->where('contentStatus','Yes');
		$this->dbEnglish->where('contentOrder',$order);
		$this->dbEnglish->where('templateID',$templateID);
		$query = $this->dbEnglish->get();
		return   $query->row_array();
	}

	/**
	 * function role : This function returns the next contentType, order and contentQuantity
	 * param1 : orderID
     * @return  array containing the next content details
	 * 
	 * */
	
	public function getContentDetailsOfCurrentOrder($order){
		$nextOrderContent= $this-> getContentDetailsFromOrder($order);
		if(isset($nextOrderContent) && count($nextOrderContent)):
			$contentType=$nextOrderContent['contentType'];
			$contentQuantity=$nextOrderContent['contentQuantity'];
		else:
			$order=1;
			$templateID    = $this->session->userdata('templateID');
			$this->dbEnglish->Select('contentType,contentQuantity');
			$this->dbEnglish->from('contentFlowMaster');
			$this->dbEnglish->where('contentStatus','Yes');
			$this->dbEnglish->where('contentOrder',$order);
			$this->dbEnglish->where('templateID',$templateID);
			$query = $this->dbEnglish->get();
			$result=  $query->row_array();
			$contentType=$result['contentType'];
			$contentQuantity=$result['contentQuantity'];
		endif;
		$resultArr=array(
			'contentType'    	=>  $contentType,
			'order'		  		=>  $order,
			'contentQuantity'	=>  $contentQuantity
			);
		return $resultArr;
	}

	/**
	 * function role : check if user exists in userContentFlowStatus table
	 * param1 : userID
    	 * @return  array containing the user details in  userContentFlowStatus table
	 * 
	 * */
	public function chckUserInUsrContentAttemptLog($userID){
		$this->dbEnglish->Select('userContentFlowType,contentAttemptCount,orderNo');
		$this->dbEnglish->from('userContentFlowStatus');
		$this->dbEnglish->where('userID',$userID);
		$query = $this->dbEnglish->get();
		return  $query->row_array();
	}


	/**
	 * function role : get the first order content details from contentFlowMaster table
    	 * @return  array containing the content details from contentFlowMaster table
	 * 
	 * */
	public function getFirstContent(){
		$templateID    = $this->session->userdata('templateID');
		$this->dbEnglish->Select('contentType,contentQuantity');
		$this->dbEnglish->from('contentFlowMaster');
		$this->dbEnglish->where('templateID',$templateID);
		$this->dbEnglish->order_by('contentOrder','asc');
		$this->dbEnglish->limit(1);
		$query = $this->dbEnglish->get();
		return  $query->row_array();
	}

	/**
	 * function role : fetches the contentQuantity from contentType and orderNo from  contentFlowMaster table
	 * param1 : contenttype,orderNo
    	 * @return  array containing the contentType and orderNo
	 * 
	 * */
	public function getContentQuantity($contentType,$orderNo){
		$templateID    = $this->session->userdata('templateID');
		$this->dbEnglish->Select('contentType,contentQuantity');
		$this->dbEnglish->from('contentFlowMaster');
		$this->dbEnglish->where('contentType',$contentType);
		$this->dbEnglish->where('contentOrder',$orderNo);
		$this->dbEnglish->where('templateID',$templateID);
		$query = $this->dbEnglish->get();
		return  $query->row_array();

	}


	/**
	 * function role : inserts the data into userContentFlowStatus table
	 * param1 :userID,contentType,orderNo
	 * */
	public function addDataToUserContentAttemptLog($userID,$contentType,$orderNo){
		$contentAttemptCount=0;
		$insertData = array(
			'userID'          		=>	$userID,
			'userContentFlowType'   	=> 	$contentType,
			'contentAttemptCount'   	=> 	$contentAttemptCount,
			'orderNo'         		=> 	$orderNo
		);
		$this->dbEnglish->insert('userContentFlowStatus', $insertData); 
		$insert_id = $this->dbEnglish->insert_id();

		$this->updateContentFlowSessionDetails($orderNo,$contentAttemptCount);
	}


	/**
	 * function role : based on the contentQuantity updates the details in the session
	 * param1 : userID,contentQuantity,counterVar,orderNo,contentType
	 * */
	public function updateContentQuantity($userID,$quantity,$counter,$orderNo,$contentType){
		if($counter==0):
			$contentAttemptCount=$quantity;
			$this->session->set_userdata('isContentquantityEqual',0);
			$updatedOrderNo= $orderNo;
			$this->session->set_userdata('presentContentType', $contentType);
			$this->updateContentFlowSessionDetails($updatedOrderNo,$contentAttemptCount);
		else:
			$this->skipCurrentContentFlow($orderNo,$userID);
		endif;
	}

	/**
	 * function role : skips the current flow and fetches the next order contentType and updates in the userContentFlowStatus table
	 * param1 : orderNo,userID
	 * */
	public function skipCurrentContentFlow($orderNo,$userID) {
		$nextOrderContent=$this->getContentDetailsOfCurrentOrder($orderNo);
		$this->session->set_userdata('presentContentType',$nextOrderContent['contentType']);
		$updatedOrderNo=$nextOrderContent['order'];
		$contentAttemptCount=0;
		$this->dbEnglish->set('userContentFlowType', $nextOrderContent['contentType']);
		$this->dbEnglish->set('contentAttemptCount',0);
		$this->dbEnglish->set('orderNo', $updatedOrderNo);
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->update('userContentFlowStatus');
		$contentType=$nextOrderContent['contentType'];	
		$this->session->set_userdata('contentQuantity',$nextOrderContent['contentQuantity']);
		$this->session->set_userdata('sessionfreeQues',0);
		$this->updatecurrentContentType($nextOrderContent['contentType'],$userID);	
		$this->updateContentFlowSessionDetails($updatedOrderNo,$contentAttemptCount);

	}

	/**
	 * function role : updates the currentcontentType in the session 
	 * param1 : contentType,userID
	 * */
	public function updatecurrentContentType($contentType,$userID){
		if($contentType==contentFlowReading || $contentType==contentFlowConversation):
			$contentType=currContentTypePsgConst;
		elseif($contentType==contentFlowFreeques):
			$contentType='freeQues';
		else :
			$contentType=contentFlowSpeaking;
		endif;
		$this->session->set_userdata('currentContentType', $contentType);
	}
	

	/**
	 * function role : updates the present orderNo and contentAttemptCount in the session
	 * param1 : orderNo,contentAttemptCount
	 * */
	public function updateContentFlowSessionDetails($updatedOrderNo,$contentAttemptCount){
		$this->session->set_userdata('orderNo', $updatedOrderNo);
		$this->session->set_userdata('contentAttemptCount', $contentAttemptCount);
	}

	/**
	 * function role : gets the nextContentType from contentFlowMaster table
	 * param1 : userID
	 * */
	public function getNextContentType($userID){
		$checkContentFlowOrder = $this->checkContentFlowOrder($userID);
		$userIDInAtmptLog=$this->chckUserInUsrContentAttemptLog($userID);
		if(count($userIDInAtmptLog)>0):
			$getContentQuantity=$this->getContentQuantity($userIDInAtmptLog['userContentFlowType'],$userIDInAtmptLog['orderNo']);			
			$contentQuantity=$getContentQuantity['contentQuantity'];
			$contentAttemptCount= $userIDInAtmptLog['contentAttemptCount'];	
			if($userIDInAtmptLog['contentAttemptCount']>=$getContentQuantity['contentQuantity']):
				$presentOrdernumber = $userIDInAtmptLog['orderNo'];
				$orderNo = $presentOrdernumber + 1;
				$this->updateContentQuantity($userID,$quantity=0,$counter=1,$orderNo,$userIDInAtmptLog['userContentFlowType']);
			else:
				$orderNo=$userIDInAtmptLog['orderNo'];
				$this->session->set_userdata('contentQuantity',$getContentQuantity['contentQuantity']);
			 	$this->updateContentQuantity($userID,$userIDInAtmptLog['contentAttemptCount'],$counter=0,$orderNo,$userIDInAtmptLog['userContentFlowType']);
			endif;
		else:
			$getFirstContent=$this->getFirstContent();
			$contentQuantity=$getFirstContent['contentQuantity']; 
			$orderNo=1;
			$this->session->set_userdata('contentQuantity',$contentQuantity);
			$this->addDataToUserContentAttemptLog($userID,$getFirstContent['contentType'],$orderNo);
			$this->updatecurrentContentType($getFirstContent['contentType'],$userID);
			$this->session->set_userdata('presentContentType',$getFirstContent['contentType']);
			$userIDInAtmptLog=$this->chckUserInUsrContentAttemptLog($userID);
		endif;
		$presentContentType = $this->session->userdata('presentContentType');
		$sessionFlowStarted=$this->session->userdata('sessionFlowStarted');
		if(!$sessionFlowStarted)
			$this->updateSessionTimeLimits();
		if($presentContentType==contentFlowReading || $presentContentType==contentFlowConversation){
			$this->session->unset_userdata('sessionSpeakingQues');	
			$this->session->unset_userdata("sessionfreeQues");
			$this->passage_model->setNextPassageData($userID);
			$this->session->set_userdata('sessionTypeToShow','Passage');
			return "Passage";
		}
		elseif($presentContentType==contentFlowSpeaking){
			$this->session->unset_userdata('sessionPassages');
			$this->session->unset_userdata('sessionSpeakingQues');			
			$this->session->unset_userdata('currentPsgQuestions');
			$this->session->unset_userdata("sessionfreeQues");
			$this->session->set_userdata('sessionTypeToShow','Speaking');
			$this->speaking_model->setSpeakingQuesData($userID);
			return "Speaking";	
		}
		else{						
			$this->session->unset_userdata('sessionPassages');
			$this->session->unset_userdata('sessionSpeakingQues');
			$this->session->unset_userdata('currentPsgQuestions');
			$this->session->set_userdata('sessionTypeToShow','FreeQuestion');
			$childClass = $this->session->userdata('childClass');
			$schoolCode = $this->session->userdata('schoolCode');
			$groupSkillID = $this->session->userdata('groupSkillID');
			$schoolBunchingOrder = $this->freeques_model->nextSchoolBunchingOrder($schoolCode,$childClass,$groupSkillID);
			if($schoolBunchingOrder){
				$this->session->set_userdata('schoolBunchingOrder',$schoolBunchingOrder);
				$data=array('currentContentType'=>currContentTypeFreeQuesConst);
				$this->session->set_userdata($data);
				$this->freeques_model->setNextFreeQuesData($userID);
				$freeQuestion=$this->session->userdata("sessionfreeQues");				
				$refID = $this->session->userdata('refID');
				$completed = $this->session->userdata('completed');
				$isValidRefID = $this->session->userdata('isValidRefID');
				if($refID == "" || $refID == 0 || $completed == 1){
					$data['refID']=$freeQuestion[0];
					$this->session->set_userdata('isRefIDPresent',0);
				} else {
					if($isValidRefID == 1){ //part of existing flow so insert normally by taking currentBunchID from table
						$this->session->set_userdata('isRefIDPresent',0);
					} else { //not part of existing flow so insert 0 and do no increment totalAttempts
						$this->session->set_userdata('isRefIDPresent',1);
					}					
					$data['refID']=$refID;
				}
				$data['completed'] = 0;
				$this->session->set_userdata($data);
				$this->dbEnglish->where('userID', $userID);
				$this->dbEnglish->update('userCurrentStatus',$data);			
				return "FreeQuestion";
			} else {
				return array('schoolBunchingOrder' => 0);
			}
		}
	}

	 /*
	 * function description :  update passage & free question time limit in session
	 * @return  none 
	 */	
	public function updateSessionTimeLimits(){
		$timeAllowedPerDay=$this->session->userdata('timeAllowedPerDay');
		$this->session->set_userdata('sessionPsgTimeLimit',floor($timeAllowedPerDay/2));
		$this->session->set_userdata('sessionFreeQuesTimeLimit',ceil($timeAllowedPerDay/2));
		$this->session->set_userdata('sessionFlowStarted',1);
	}
	
	 /*
	 *  function description :  calculate passage time spent and free question time spent
	 *  param1   userID
	 *	@return  array,passage/freequestion time spent 
	 */ 	
	public function getTotalTimeSpentPsgFreeQues($userID){
		$getTimeSpentToday=$this->questionspage_model->getTimeSpentToday($userID);
		$sessionPsgTimeLimit=$this->session->userdata('sessionPsgTimeLimit');
		$psgTimeSpentToday=0;
		$freeQuesTimeSpentToday=0;
		if(($getTimeSpentToday+0.05) >= ($sessionPsgTimeLimit-1)){
			$psgTimeSpentToday=$getTimeSpentToday+0.05;	
			$freeQuesTimeSpentToday=(($getTimeSpentToday+0.05)-($sessionPsgTimeLimit-1));
		}	
		else
			$psgTimeSpentToday=$getTimeSpentToday;
				
		return array($psgTimeSpentToday,$freeQuesTimeSpentToday);
	}

	 /*
	 *  function description :  checks the content flow order
	 *  param1 :  userID,orderID
	 */ 	
	public function checkContentFlowOrder($userID,$orderID="") {
		$getCurrentContentOrder =  ($orderID) ? $orderID :  $this->session->userdata('orderNo');
		$userContentFlowType    = $this->session->userdata('presentContentType');
		$curretContentFlow         =  $this->getContentQuantity($userContentFlowType,$getCurrentContentOrder);
		if(empty($curretContentFlow)) :
			$nextconteFlow = $this->nextconteFlow($userContentFlowType);
			$data = array('orderNo' =>$nextconteFlow['contentOrder']);
			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->update('userContentFlowStatus',$data);
			$this->session->set_userdata($data);
			$this->session->set_userdata('contentQuantity',$nextconteFlow['contentQuantity']);
		endif;
	}

	/*
	 *  function description :  fetches the next Content details from contentFlowMaster  based on contentType 
	 *  param1 :  contentType
	 *	@return  array of values containing contentDetails
	 */ 
	public function nextconteFlow($userContentFlowType) {
		$templateID    = $this->session->userdata('templateID');
		$this->dbEnglish->Select('contentType,contentQuantity,contentOrder');
		$this->dbEnglish->from('contentFlowMaster');
		$this->dbEnglish->where('contentType',$userContentFlowType);
		$this->dbEnglish->where('templateID',$templateID);
		$this->dbEnglish->limit(1);
		$query = $this->dbEnglish->get();
		return  $query->row_array();
	}

	/*
	 *  function description :  fetches the templateID based on childClass and schoolCode from schoolBunching order table
	 *  param1 :  schoolCode,childClass
	 *	@return  the templateID
	 */ 
	public function getSchoolContentFlowOrder($schoolCode,$childClass){
		$this->dbEnglish->Select('templateID,remediationTemplateID');
		$this->dbEnglish->from('schoolContentFlowOrder');
		$this->dbEnglish->where('schoolCode',$schoolCode);
		$this->dbEnglish->where('childClass',$childClass);
		$query = $this->dbEnglish->get();
		$result=  $query->row_array();
		$templateID = (isset($result['templateID'])) ? $result['templateID'] : 1; 
		$remediationTemplateID = (isset($result['remediationTemplateID'])) ? $result['remediationTemplateID'] : 1;
		$result_array = array('templateID' =>$templateID,'remediationTemplateID'=>$remediationTemplateID);
		return $result_array;
	}


	public function getCosntantvalue($remediationTemplateID) {

		$this->dbEnglish->Select('constantName,constantValue');
		$this->dbEnglish->from('constantMaster');
		$this->dbEnglish->where('remediationTemplateID',$remediationTemplateID);
		$query = $this->dbEnglish->get();
		$result=  $query->result_array();
		$constanArray = array();
		foreach ($result as $key => $constantValue) {
		$constanArray[$constantValue['constantName']] = $constantValue['constantValue'];
		}
		return $constanArray;
	}

} 
?>