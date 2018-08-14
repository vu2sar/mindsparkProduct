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

	//speaking
	public function setNextSessionSpeakingQuestions($userID){

		$passageType=$this->session->userdata('presentContentType');
		$contentAttemptCount=$this->session->userdata('contentAttemptCount');
		$contentQuantity=$this->session->userdata('contentQuantity');
		$spkngQstnLimit=$contentQuantity-$contentAttemptCount;
		$userInContentAttemptLog=$this->passage_model->checkForUserInContentAttemptLog($passageType);
		$childClass=$this->session->userdata('childClass');
		$msLevel=$childClass-3;
		$spkngDataArr=array();
		$attemptedSpkngID='';
		$attemptedSpkngIDArr= $this->speakingAttemptedqcode($userID);
		if($attemptedSpkngIDArr):
			foreach ($attemptedSpkngIDArr as  $value) {
				$tmpVar.= "'".$value['qcode']."'".',';
			}
			$attemptedSpkngID=rtrim($tmpVar,',');
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
		$query="SELECT qcode FROM questions WHERE qTemplate ='".$passageType."' AND  status=6 AND msLevel =  ".$msLevel." ";
		if($attemptedSpkngID):
			$query.=" AND qcode NOT IN (".$attemptedSpkngID.") ORDER BY  RAND()";
		endif;
		$result=$this->dbEnglish->query($query)->result_array();
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

	public function speakingLowestAttempts($userID,$spkngQstnLimit)
	{
		$query = "SELECT a.qcode FROM speakingAttempts a LEFT JOIN (SELECT if((SELECT MAX(attemptCount) FROM speakingAttempts WHERE userID = $userID)!=1,qcode,null) as qcode FROM speakingAttempts where attemptCount=(SELECT MAX(attemptCount) FROM speakingAttempts WHERE userID = $userID) and userID=$userID) AS b on (a.qcode = b.qcode)WHERE a.userID = $userID and b.qcode is null ORDER BY a.lastModified , a.attemptCount ";
		$result = $this->dbEnglish->query($query);
		return  $result ->result_array();
	}






}

?>
