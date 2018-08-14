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
	 * function description : fetches the contentType and contentQuantity from contentFlowMaster based on templateID
	 * param1   orderID
	 * @return  array, contentType and contentQuantity  
	 * 
	 * */
	public function getContentTypeAndQtyFromOrder($order){
		$templateID    = $this->session->userdata('templateID');
		$this->dbEnglish->Select('contentType,contentQuantity');
		$this->dbEnglish->from('contentFlowMaster');
		$this->dbEnglish->where('contentStatus','Yes');
		$this->dbEnglish->where('contentOrder',$order);
		$this->dbEnglish->where('templateID',$templateID);
		$query = $this->dbEnglish->get();
		return $query->row_array();
	}

	/**
	 * function description : fetches the content details from contentOrder
	 * param1   orderID
	 * @return  array, contentType, order and contentQuantity  
	 * 
	 * */
	public function getContentDetailsOfCurrentOrder($order){
		$contentTypeAndQtyFrmOrder= $this-> getContentTypeAndQtyFromOrder($order);
		if(isset($contentTypeAndQtyFrmOrder) && count($contentTypeAndQtyFrmOrder)):
			$contentType=$contentTypeAndQtyFrmOrder['contentType'];
			$contentQuantity=$contentTypeAndQtyFrmOrder['contentQuantity'];
		else:
			$order=1;
			$result=$this->getContentTypeAndQtyFromOrder($order);
			$contentType=$result['contentType'];
			$contentQuantity=$result['contentQuantity'];
		endif;
		$contentTypeOrderQty=array(
			'contentType'    	=>  $contentType,
			'order'		  	=>  $order,
			'contentQuantity'	=>  $contentQuantity
			);
		return $contentTypeOrderQty;
	}

	/**
	 * function description : fetches the userContentFlowType,contentAttemptCount,orderNo
	 * param1 :  userID
	 * @return  array, userContentFlowType,contentAttemptCount,orderNo
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
	 * function description : fetches the first contentType,contentQuantity based on templateID
	 * @return  array, contentType,contentQuantity
	 * 
	 * */
	/*public function getFirstContent(){
		$templateID    = $this->session->userdata('templateID');
		$this->dbEnglish->Select('contentType,contentQuantity');
		$this->dbEnglish->from('contentFlowMaster');
		$this->dbEnglish->where('templateID',$templateID);
		$this->dbEnglish->order_by('contentOrder','asc');
		$this->dbEnglish->limit(1);
		$query = $this->dbEnglish->get();
		return  $query->row_array();
	}*/
	/**
	 * function description : fetches the contentQuantity based on contentType
	  * param1 :  contentType
	   * param2 :  orderNo
	 * @return  array, contentType,contentQuantity
	 * 
	 * */
	public function getContentTypeAndQty($contentType,$orderNo){
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
	 * function description : updates the currentContentType in userCurrentStatus table.
	 * param1 : userID
	 * */
	public function updateUserCurrentStatusTbl($userID) {		
		$presentContentType=$this->session->userdata('presentContentType');
		$presentContentType = strtolower($presentContentType);
		if($presentContentType==contentFlowReading || $presentContentType==contentFlowConversation):
			$contentType=currContentTypePsgConst;
			$sessionPassages=$this->session->userdata('sessionPassages');
			$refID=$sessionPassages[0];
		elseif($presentContentType==contentFlowSpeaking):
			$contentType=contentFlowSpeaking;
			$speakingQues=$this->session->userdata('sessionSpeakingQues');
			$refID=$speakingQues[0];
		endif;
		$this->session->set_userdata('currentContentType',$contentType);
		$this->session->set_userdata('refID',$refID);
		$this->session->set_userdata('completed',0);
		$data=array('currentContentType'=>$contentType,'refID'=>$refID,'completed'=>0);
		$this->dbEnglish->where('userID', $userID);
		$this->dbEnglish->update('userCurrentStatus',$data);
	}

	/**
	 * function description : inserts the record into userContentFlowStatus
	  * param1 :  userID
	   * param2 :  contentType
	   * param2 :  orderNo
	 * @return  none
	 * 
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
		$this->updateContentFlowSessionDetails($orderNo,$contentAttemptCount);
	}
	
	/**
	 * function description : inserts the record into userContentFlowStatus
	  * param1 :  userID
	   * param2 :  contentType
	   * param2 :  orderNo
	 * @return  none
	 * 
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
		$this->updateUserCurrentStatus($nextOrderContent['contentType'],$userID);	
		$this->updateContentFlowSessionDetails($updatedOrderNo,$contentAttemptCount);

	}


	public function updateUserCurrentStatus($contentType,$userID){
		if($contentType==contentFlowReading || $contentType==contentFlowConversation):
			$contentType=currContentTypePsgConst;
		elseif($contentType==contentFlowFreeques):
			$contentType=currContentTypeFreeQuesConst;
		else :
			$contentType=contentFlowSpeaking;
		endif;
		$this->session->set_userdata('currentContentType', $contentType);
	}

	public function updateContentFlowSessionDetails($updatedOrderNo,$contentAttemptCount){
		$this->session->set_userdata('orderNo', $updatedOrderNo);
		$this->session->set_userdata('contentAttemptCount', $contentAttemptCount);
	}

	public function getNextContentType($userID){
		$checkContentFlowOrder = $this->checkContentFlowOrder($userID);
		$userIDInAtmptLog=$this->chckUserInUsrContentAttemptLog($userID);
		if(count($userIDInAtmptLog)>0):
			$getContentQuantity=$this->getContentTypeAndQty($userIDInAtmptLog['userContentFlowType'],$userIDInAtmptLog['orderNo']);			
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
			//$getFirstContent=$this->getFirstContent();
			$orderNo=1;
			$getFirstContent=$this->getContentTypeAndQtyFromOrder($orderNo);
			$contentQuantity=$getFirstContent['contentQuantity']; //reading
			$this->session->set_userdata('contentQuantity',$contentQuantity);
			$this->addDataToUserContentAttemptLog($userID,$getFirstContent['contentType'],$orderNo);
			$this->updateUserCurrentStatus($getFirstContent['contentType'],$userID);
			$this->session->set_userdata('presentContentType',$getFirstContent['contentType']);
			$userIDInAtmptLog=$this->chckUserInUsrContentAttemptLog($userID);
		endif;

		
		$presentContentType = $this->session->userdata('presentContentType');
		$sessionFlowStarted=$this->session->userdata('sessionFlowStarted');
		//if(!$sessionFlowStarted)
		//	$this->updateSessionTimeLimits();

		if($presentContentType==contentFlowReading || $presentContentType==contentFlowConversation){
			$this->session->unset_userdata('sessionSpeakingQues');
			$this->session->unset_userdata('schoolBunchingOrder');
			$this->session->unset_userdata("sessionfreeQues");
			$this->passage_model->setNextPassageData($userID);			
			$this->session->set_userdata('sessionTypeToShow','Passage');
			return "Passage";
		}
		elseif($presentContentType==contentFlowSpeaking){
			$this->session->unset_userdata('sessionPassages');
			$this->session->unset_userdata('schoolBunchingOrder');
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
	
	/*public function updateSessionTimeLimits(){
		$timeAllowedPerDay=$this->session->userdata('timeAllowedPerDay');
		$this->session->set_userdata('sessionPsgTimeLimit',floor($timeAllowedPerDay/2));
		$this->session->set_userdata('sessionFreeQuesTimeLimit',ceil($timeAllowedPerDay/2));
		$this->session->set_userdata('sessionFlowStarted',1);
	}*/
	
	

	public function checkContentFlowOrder($userID,$orderID="") {
		$getCurrentContentOrder =  ($orderID) ? $orderID :  $this->session->userdata('orderNo');
		$userContentFlowType    = $this->session->userdata('presentContentType');
		$curretContentFlow         =  $this->getContentTypeAndQty($userContentFlowType,$getCurrentContentOrder);
		if(empty($curretContentFlow)) :
			$nextconteFlow = $this->nextconteFlow($userContentFlowType);
			$data = array('orderNo' =>$nextconteFlow['contentOrder']);
			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->update('userContentFlowStatus',$data);
			$this->session->set_userdata($data);
			$this->session->set_userdata('contentQuantity',$nextconteFlow['contentQuantity']);

		endif;
	}


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

	


	public function getConstantValue($remediationTemplateID) {

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