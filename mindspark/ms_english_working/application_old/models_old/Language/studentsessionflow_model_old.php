<?php


Class Studentsessionflow_model extends MY_Model
{
	
	public function __construct() {
		 parent::__construct();
		 $this->dbEnglish = $this->load->database('mindspark_english',TRUE);
		 $this->load->library('session');
		 //$this->load->model('Language/questionspage_model');
		 $this->load->model('Language/passage_model');
		 $this->load->model('Language/freeques_model');
	}

	/**
	 * function description : To check which type(passage/question) of session to show to the child 
	 * param1   userID
	 * @return  string, sessionType 
	 * 
	 * */

	//This function need to modify the content flow next order
	// public function getSessionType($userID){
	// 	$userContentAtmptDetails=$this->session->userdata('content_attempt_details');
	// 	if($userContentAtmptDetails[0]['userContentFlowType'] == '')
	// 			$order=0;
	// 	else:			
	// 		$order=$userContentAtmptDetails[0]['orderNo'] ;		
	// 	$contentFlowArray = $this->defaultContentFlowOrder($order);
	// 	return $contentFlowArray[0]['contentType'];		
	// }
	public function getContentDetailsFromOrder($order){
		$this->dbEnglish->Select('contentType,contentQuantity');
		$this->dbEnglish->from('contentFlowMaster');
		$this->dbEnglish->where('contentStatus','Yes');
		$this->dbEnglish->where('contentOrder',$order);
		$query = $this->dbEnglish->get();
		return   $query->row_array();
	}

	public function getContentDetailsOfCurrentOrder($order){
		$nextOrderContent= $this-> getContentDetailsFromOrder($order);
		//print_r($nextOrderContent);exit;
		if(count($nextOrderContent)<=0):
			$order=1;
			$this->dbEnglish->Select('contentType,contentQuantity');
			$this->dbEnglish->from('contentFlowMaster');
			$this->dbEnglish->where('contentStatus','Yes');
			$this->dbEnglish->where('contentOrder',$order);
			$query = $this->dbEnglish->get();
			$result=  $query->row_array();
			$contentType=$result['contentType'];
			$contentQuantity=$result['contentQuantity'];
		else:
			$contentType=$nextOrderContent['contentType'];
			$contentQuantity=$nextOrderContent['contentQuantity'];
		endif;
		$resultArr=array(
			'contentType'    	=>  $contentType,
			'order'		  	=>  $order,
			'contentQuantity'	=>  $contentQuantity
			);
		return $resultArr;
	}

	/*public function contentFlowContentType($userID,$type){

		if($type=='reading' || $type=='conversation') :
			$this->passage_model->setNextPassageData($userID);
			$contentType='Passage';
		else :.
			$this->session->unset_userdata('sessionPassages');
			$this->session->unset_userdata('currentPsgQuestions');
			$this->freeques_model->setNextFreeQuesData($userID);	
			$freeQuestion=$this->session->userdata("sessionfreeQues");			
			$data=array('currentContentType'=>currContentTypeFreeQuesConst,'completed'=>0,'refID'=>$freeQuestion[0]);
			$this->session->set_userdata($data);
			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->update('userCurrentStatus',$data);
			$contentType='FreeQuestion';
		endif;
		return $contentType;
	}*/

	/*public function getSessionTypeToContentFlowShow($userID,$order) {
		$getCurrentContentType = $this->getContentFlowOrder($order);
		print_r($getCurrentContentType);
		$currentContentType = $this->contentFlowContentType($userID,$getCurrentContentType['contentType']);
		return $currentContentType;
	}


	


	*/

	// public function getUserContentAttemptDetails($userID){echo "hai";exit;
	// 	$this->dbEnglish->Select('userContentFlowType,contentAttemptCount,orderNo');
	// 	$this->dbEnglish->from('userContentAttemptLog');
	// 	$this->dbEnglish->where('userID',$userID);
	// 	$query = $this->dbEnglish->get();
	// 	return  $query->result_array();
	// }

	// public function defaultContentFlowOrder($order) {
	// 	$this->dbEnglish->Select('contentType,contentQuantity');
	// 	$this->dbEnglish->from('contentFlowMaster');
	// 	$this->dbEnglish->where('contentStatus','Yes');
	// 	$this->dbEnglish->where('contentOrder',$order+1);
	// 	$query = $this->dbEnglish->get();
	// 	return  $query->row_array();
	// }
	public function chckUserInUsrContentAttemptLog($userID){
		$this->dbEnglish->Select('userContentFlowType,contentAttemptCount,orderNo');
		$this->dbEnglish->from('userContentAttemptLog');
		$this->dbEnglish->where('userID',$userID);
		$query = $this->dbEnglish->get();
		return  $query->row_array();
	}

	public function getFirstContent(){
		$this->dbEnglish->Select('contentType,contentQuantity');
		$this->dbEnglish->from('contentFlowMaster');
		$this->dbEnglish->order_by('contentOrder','asc');
		$this->dbEnglish->limit(1);
		$query = $this->dbEnglish->get();
		return  $query->row_array();
	}

	public function getContentQuantity($contentType,$orderNo,$contentType){
		//echo $contentType;
		$this->dbEnglish->Select('contentQuantity');
		$this->dbEnglish->from('contentFlowMaster');
		$this->dbEnglish->where('contentType',$contentType);
		$this->dbEnglish->where('contentOrder',$orderNo);
		$query = $this->dbEnglish->get();
		return  $query->row_array();
	}

	public function addDataToUserContentAttemptLog($userID,$contentType,$orderNo){
		$contentAttemptCount=0;
		$insertData = array(
			'userID'          		=>	$userID,
			'userContentFlowType'         => 	$contentType,
			'contentAttemptCount'           => 	$contentAttemptCount,
			'orderNo'         		=> 	$orderNo
		);
		$this->dbEnglish->insert('userContentAttemptLog', $insertData); 
		$insert_id = $this->dbEnglish->insert_id();
		$this->updateContentFlowSessionDetails($orderNo,$contentAttemptCount);
	}

	public function updateContentQuantity($userID,$quantity,$counter,$orderNo,$contentType){
		if($counter==0):
			//echo "come here";exit;
			$contentAttemptCount=$quantity+1;
			$updatedOrderNo= $orderNo;
		else:
			//echo "dsgdg";exit;
			$nextOrderContent=$this->getContentDetailsOfCurrentOrder($orderNo);
		//print_r($nextOrderContent);exit;
		//$this->session->set_userdata('presentContentType', $contentType);
			$updatedOrderNo=$nextOrderContent['order'];
			$contentAttemptCount=0;
			$this->updateUserCurrentStatus($nextOrderContent['contentType'],$userID);
			$this->session->set_userdata('userContentFlowType', $nextOrderContent['contentType']);
			$this->session->set_userdata('contentQuantity',$nextOrderContent['contentQuantity']);
			$this->dbEnglish->set('userContentFlowType', $nextOrderContent['contentType']);
			$contentType=$nextOrderContent['contentType'];		
		endif;
		$this->dbEnglish->set('orderNo', $updatedOrderNo);
		$this->dbEnglish->set('contentAttemptCount',$contentAttemptCount);
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->update('userContentAttemptLog');
		$this->session->set_userdata('presentContentType', $contentType);
		$this->updateContentFlowSessionDetails($updatedOrderNo,$contentAttemptCount);

	}


	public function updateUserCurrentStatus($contentType,$userID){
		if($contentType=='reading' || $contentType=='conversation'):
			$contentType='passage';
		elseif($contentType=='freeques'):
			$contentType='free_ques';
		endif;
		//$sessionPsg=$this->session->userdata('sessionPassages');
		$this->session->set_userdata('currentContentType', $contentType);
	}

	public function updateContentFlowSessionDetails($updatedOrderNo,$contentAttemptCount){
		$this->session->set_userdata('orderNo', $updatedOrderNo);
		$this->session->set_userdata('contentAttemptCount', $contentAttemptCount);
	}

	public function getNextContentType($userID){
		$userIDInAtmptLog=$this->chckUserInUsrContentAttemptLog($userID);
		if(count($userIDInAtmptLog)>0):
			$getContentQuantity=$this->getContentQuantity($userIDInAtmptLog['userContentFlowType'],$userIDInAtmptLog['orderNo']);
			$contentQuantity=$getContentQuantity['contentQuantity'];
			$contentAttemptCount= $userIDInAtmptLog['contentAttemptCount'];			

			if($userIDInAtmptLog['contentAttemptCount'] ==$getContentQuantity['contentQuantity']):
				$orderNo=$userIDInAtmptLog['orderNo']+1;
				//echo 'inside if';exit;
				$this->updateContentQuantity($userID,$quantity=0,$counter=1,$orderNo,$userIDInAtmptLog['userContentFlowType']);
			else:
				//echo 'inside else';exit;
				$orderNo=$userIDInAtmptLog['orderNo'];
			 	$this->updateContentQuantity($userID,$userIDInAtmptLog['contentAttemptCount'],$counter=0,$orderNo,$userIDInAtmptLog['userContentFlowType']);
			endif;
		else:
			$getFirstContent=$this->getFirstContent();
			$contentQuantity=$getFirstContent['contentQuantity']; //reading
			$contentAttemptCount= 1;
			$orderNo=1;
			$this->addDataToUserContentAttemptLog($userID,$getFirstContent['contentType'],$orderNo);
			$userIDInAtmptLog=$this->chckUserInUsrContentAttemptLog($userID);
		endif;

		$this->session->set_userdata('presentContentType', $userIDInAtmptLog['userContentFlowType']);
		$this->session->set_userdata('contentQuantity',$contentQuantity);



		$getCurrentContentType = $this->getContentDetailsOfCurrentOrder($orderNo);
		//$this->session->set_userdata('contentType', $getCurrentContentType['contentType']);
		$sessionFlowStarted=$this->session->userdata('sessionFlowStarted');
		if(!$sessionFlowStarted)
			$this->updateSessionTimeLimits();
		//print_r($getCurrentContentType);exit;
		// $getTimeSpentToday=$this->questionspage_model->getTimeSpentToday($userID);
		// $sessionPsgTimeLimit=$this->session->userdata('sessionPsgTimeLimit');
		//echo  $getCurrentContentType['contentType'];exit;
		if($getCurrentContentType['contentType']=='reading' || $getCurrentContentType['contentType']=='conversation'){

		//if(($getTimeSpentToday+0.05) <= ($sessionPsgTimeLimit-1)){

			//print "Passage";
			$this->passage_model->setNextPassageData($userID);
			return "Passage";
		}
		else{

			$this->session->unset_userdata('sessionPassages');
			$this->session->unset_userdata('currentPsgQuestions');
			$this->freeques_model->setNextFreeQuesData($userID);	
			$freeQuestion=$this->session->userdata("sessionfreeQues");			
			$data=array('currentContentType'=>currContentTypeFreeQuesConst,'completed'=>0,'refID'=>$freeQuestion[0]);
			$this->session->set_userdata($data);
			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->update('userCurrentStatus',$data);
			return "FreeQuestion";
		}
	}


	/*public function getSessionTypeToShow($userID){
		//new
		//$getCurrentContentType = $this->getContentFlowOrder($order=0);
		$sessionFlowStarted=$this->session->userdata('sessionFlowStarted');
		if(!$sessionFlowStarted)
			$this->updateSessionTimeLimits();
		
		$getTimeSpentToday=$this->questionspage_model->getTimeSpentToday($userID);
		$sessionPsgTimeLimit=$this->session->userdata('sessionPsgTimeLimit');
		//if($getCurrentContentType['contentType']=='reading' || $getCurrentContentType['contentType']=='conversation') {

		if(($getTimeSpentToday+0.05) <= ($sessionPsgTimeLimit-1)){

			//print "Passage";
			$this->passage_model->setNextPassageData($userID);
			return "Passage";
		}
		else{

			$this->session->unset_userdata('sessionPassages');
			$this->session->unset_userdata('currentPsgQuestions');
			$this->freeques_model->setNextFreeQuesData($userID);	
			$freeQuestion=$this->session->userdata("sessionfreeQues");			
			$data=array('currentContentType'=>currContentTypeFreeQuesConst,'completed'=>0,'refID'=>$freeQuestion[0]);
			$this->session->set_userdata($data);
			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->update('userCurrentStatus',$data);
			return "FreeQuestion";
		}
	}
	*/
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
} 
