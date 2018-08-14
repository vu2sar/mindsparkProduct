<?php
	

Class Speaking_model extends MY_Model
{
	
	public function __construct() {
		 parent::__construct();
		 $this->dbEnglish = $this->load->database('mindspark_english',TRUE);
		 $this->load->library('session');
		 $this->load->model('Language/passage_model');		 
		 
	}

	/**
	 * function description : check for stored session free question and set free questions in the session if not already set   
	 * param1   userID
	 * @return  none
	 * */
	public function setSpeakingQuesData($userID){
		$checkContentFlowOrder = $this->studentsessionflow_model->checkContentFlowOrder($this->session->userdata('userID'));
		if(!$this->session->userdata('sessionSpeakingQues'))
			$this->setNextSessionSpeakingQuestions($userID);
	}

	public function setNextSessionSpeakingQuestions($userID){

		$passageType=$this->session->userdata('presentContentType');
		$contentAttemptCount=$this->session->userdata('contentAttemptCount');
		$contentQuantity=$this->session->userdata('contentQuantity');
		$spkngQstnLimit=$contentQuantity-$contentAttemptCount;
		$childClass=$this->session->userdata('childClass');
		$msLevel=$childClass-3;
		$spkngDataArr=array();
		$attemptedSpkngID='';
		$attemptedSpkngIDArr= $this->speakingAttemptedqcode($userID);

		if($attemptedSpkngIDArr):
			foreach ($attemptedSpkngIDArr as  $value) {
				$attemptedSpkngID[]=$value['qcode'];
			}
		endif;
		
		$unattemptedSpkngIDArr= $this->unattemptedspeakingQcode($passageType,$attemptedSpkngID,$msLevel);
		if(empty($unattemptedSpkngIDArr)) :
			$unattemptedSpkngIDArr = $this->speakingLowestAttempts($userID);
		endif;
		if(isset($unattemptedSpkngIDArr) && count($unattemptedSpkngIDArr)) :
			for ($i=0; $i<$spkngQstnLimit; $i++) {
				if($unattemptedSpkngIDArr[$i]['qcode'])
				$spkngDataArr[] = $unattemptedSpkngIDArr[$i]['qcode'];
			}
			$this->session->set_userdata('sessionSpeakingQues',$spkngDataArr);
			$this->session->set_userdata('sessionTypeToShow','speaking');
			$this->passage_model->updateUserCurrentStatusTbl($userID);
		else :
			$presentOrdernumber = $this->session->userdata('orderNo');
			$orderNo = $presentOrdernumber + 1;
			$this->studentsessionflow_model->skipCurrentContentFlow($orderNo,$userID);
			$this->studentsessionflow_model->getNextContentType($userID);	
		endif;
	}

	public function unattemptedspeakingQcode($passageType,$attemptedSpkngID,$msLevel) 
	{
		$this->dbEnglish->Select('qcode');
		$this->dbEnglish->from('questions');
		$this->dbEnglish->where('qTemplate',$passageType);
		$this->dbEnglish->where('status',6);
		$this->dbEnglish->where('msLevel',$msLevel);
		if($attemptedSpkngID):
			$this->dbEnglish->where_not_in('qcode', $attemptedSpkngID);
			$this->dbEnglish->order_by('qcode','RANDOM');
		endif;
		$attmptdQuery = $this->dbEnglish->get();
		$result=$attmptdQuery->result_array();
		return $result;
	}


	public function speakingAttemptedqcode($userID) 
	{
		$this->dbEnglish->Select('qcode');
		$this->dbEnglish->from('speakingAttempts');
		$this->dbEnglish->where('userID',$userID);
		$attmptdQuery = $this->dbEnglish->get();
		return  $attmptdQuery->result_array();
	}

	public function speakingLowestAttempts($userID) {

		$speakingAttemptCount        =  $this->speakingAttemptCount($userID);
		$subquery = "(SELECT if((SELECT MAX(attemptCount) FROM speakingAttempts WHERE userID = $userID)!=1,qcode,null) as qcode FROM speakingAttempts where attemptCount=($speakingAttemptCount) and userID=$userID) AS b";
		$this->dbEnglish->select('a.qcode');
		$this->dbEnglish->from('speakingAttempts a');
		$this->dbEnglish->join($subquery, 'a.qcode = b.qcode', 'left');
		$this->dbEnglish->where('a.userID',$userID);
		$this->dbEnglish->where('b.qcode',NULL);
		$this->dbEnglish->order_by('a.lastModified,a.attemptCount','ASC');
		$this->dbEnglish->limit(5);
		$result = $this->dbEnglish->get();
        return  $result ->result_array();
              	
	}


	public function speakingAttemptCount($userID) {

		$this->dbEnglish->select_max('attemptCount');
		$this->dbEnglish->from('speakingAttempts');
		$this->dbEnglish->where('userID',$userID);
    	$query = $this->dbEnglish->get();    		
    	$speakingAttemptCount =  $query->row_array();
    	$return_result = (isset($speakingAttemptCount['attemptCount'])) ? $speakingAttemptCount['attemptCount'] : '1';
    	return $return_result;
	}






}

?>
