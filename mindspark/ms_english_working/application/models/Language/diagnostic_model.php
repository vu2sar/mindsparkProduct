<?php

Class Diagnostic_model extends CI_model
{
	public function __construct() 
	{
		 $this->dbEnglish = $this->load->database('mindspark_english',TRUE);
		 //$this->dbESL = $this->load->database('mindspark_ESL',TRUE);
		 $this->load->model('Language/user_model');
		 $this->Companies_db = $this->dbEnglish;

	       // Pass reference of database to the CI-instance
	     $CI =& get_instance();
	     $CI->Companies_db =& $this->Companies_db; 
	}

	/**
	 * function role : Get defined flow of contextual questions,non contextual questions ,passages to be given in diagnostic test
	 * param1 : userID
	 * @return   array, Diagnostic test flow
	 * 
	 * */

	function getTestFlowData($userID)
	{
		$flowData = array();
		$this->dbEnglish->Select('quesType,quesTypeLabel,value');
		$this->dbEnglish->from('diagnosticTestMaster');
		$this->dbEnglish->order_by('flowNo','asc');
		$query = $this->dbEnglish->get();

		$testFlowArr = $query->result_array();

		foreach($testFlowArr as $key=>$value)
		{
				$tmpArr = array();
				if($value['quesType']=='passage')
				{	
					$value['value'] = trim($value['value']);
					$tmpArr['quesType']=$value['quesType'];
					$tmpArr['quesTypeLabel']=$value['quesTypeLabel'];
					$tmpArr['value'] = $value['value'].'||'.$this->getpassageName($value['value']);
					$info = $this->getUserAttemptInfo($userID,$value['quesType'],$value['value']);;
					$tmpArr['info'] = $info;
					// $tmpArr['userResponse'] = null;
					array_push($flowData, $tmpArr);
				}
				else if($value['quesType']=='essay')
				{

					$value['value'] = trim($value['value']);
					$tmpArr['quesType']=$value['quesType'];
					$tmpArr['quesTypeLabel']=$value['quesTypeLabel'];
					$tmpArr['value'] = $value['value'].'||'.$this->getEssayName($value['value']);
					$info = $this->getUserAttemptInfo($userID,$value['quesType'],$value['value']);
					$tmpArr['info'] = $info;
					// $tmpArr['userResponse'] = null;
					array_push($flowData, $tmpArr);
				}
				else 
				{
					foreach( explode(',',$value['value']) as $index=>$qcode)
					{
						$qcode = trim($qcode);
						$tmpArr['quesType']=$value['quesType'];
						$tmpArr['quesTypeLabel']=$value['quesTypeLabel'];
						$tmpArr['value'] = $qcode;
						$info = $this->getUserAttemptInfo($userID,$tmpArr['quesType'],$qcode);
						$tmpArr['info'] = $info;
						// $tmpArr['userResponse'] = $info['userResponse'];
						array_push($flowData, $tmpArr);	
					}
				}
			 
		}

		return $flowData;
	}

	/**
	 * function role : Get current user atttempts in the flow for diagnostic test
	 * param1 : userID
	 * param2 :question type, ex: passage,questions,essay
	 * param3 :qcode
	 * @return   array, user attempt info
	 * 
	 * */

	function getUserAttemptInfo($userID,$qtype,$qcode)
	{
		$attemptData = $this->getUserAttemptData($userID);
		$attemptPassageData = $this->getUserPassageAttemptData($userID);
		$attemptedEssayData = $this->getUserEssayAttemptData($userID);

		if(($qtype=="freeQues" || $qtype=="passageQues") && array_key_exists($qcode, $attemptData))
		{
			if($attemptData[$qcode]['extraParams'] == 'FLAGGED')
				$tmpArr['info'] = 'FLAGGED';
			else if($attemptData[$qcode]['userResponse'] == 'SKIP')
				$tmpArr['info'] = 'SKIP';
			else if($attemptData[$qcode]['userResponse'] != "" && $attemptData[$qcode]['userResponse'] != null)
				$tmpArr['info'] = 'A';

			$tmpArr['completed'] = null;
			$tmpArr['currentPassagePart'] = null;
			$tmpArr['correct'] = $attemptData[$qcode]['correct'];
			$tmpArr['userResponse'] = $attemptData[$qcode]['userResponse'];
		}
		else if($qtype=="passage" &&  array_key_exists($qcode, $attemptPassageData))
		{
			$tmpArr['info'] = 'A';
			$tmpArr['completed'] = $attemptPassageData[$qcode]['completed'];
			$tmpArr['currentPassagePart'] = $attemptPassageData[$qcode]['currentPassagePart'];
			$tmpArr['correct'] = null;
			$tmpArr['userResponse'] = null;
		}
		else if($qtype=="essay" &&  array_key_exists($qcode, $attemptedEssayData))
		{
			$tmpArr['info'] = 'A';
			$tmpArr['completed'] = null;
			$tmpArr['currentPassagePart'] = null;
			$tmpArr['correct'] = null;
			$tmpArr['userResponse'] = $attemptedEssayData[$qcode]['userResponse'];
		}
		else
		{
			$tmpArr['info'] = 'NA';	
			$tmpArr['completed'] = null;
			$tmpArr['currentPassagePart'] = null;
			$tmpArr['correct'] = null;
			$tmpArr['userResponse'] = null;
		}
	
		return $tmpArr;
	}

	/**
	 * function role : Get user response for a question in diagnostic test
	 * param1 : userID
	 * param2 :qcode
	 * @return   string, user Response ; null, if question not attempted 
	 * 
	 * */

	function getDiagnosticTestUserResponse($userID,$qcode)
	{
		$this->dbEnglish->Select('userResponse');
		$this->dbEnglish->from('diagnosticQuestionAttempt');
		$this->dbEnglish->where('userID', $userID);
		$this->dbEnglish->where('qcode', $qcode);
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();
		if (sizeof($queryResult)>0) {
			return $queryResult[0]['userResponse'];
		}
		else
			return null;
	}

	/**
	 * function role : Get data for diagnostic test question attempt for a user
	 * param1 : userID
	 * @return   array, Question Attempt info
	 * 
	 * */

	function getUserAttemptData($userID)
	{
		$attemptData = array();
		$this->dbEnglish->Select('qcode,userResponse,extraParams,correct');
		$this->dbEnglish->from('diagnosticQuestionAttempt');
		$this->dbEnglish->where('userID', $userID );
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();

		foreach($queryResult as $key=>$value)
		{
			$attemptData[$value['qcode']] = array('userResponse' => $value['userResponse'], 'extraParams' => $value['extraParams'], 'correct' => $value['correct']);
		}
		return $attemptData;
	}

	/**
	 * function role : Get data for diagnostic test passage attempt for a user
	 * param1 : userID
	 * @return   array, Passage Attempt info
	 * 
	 * */

	function getUserPassageAttemptData($userID)
	{
		$passageAttemptData = array();
		$this->dbEnglish->Select('passageID,passageAttemptID,currentPassagePart,completed');
		$this->dbEnglish->from('passageAttempt');
		$this->dbEnglish->where('userID', $userID );
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();

		foreach($queryResult as $key=>$value)
		{
			$passageAttemptData[$value['passageID']] = array('passageAttemptID' => $value['passageAttemptID'], 'currentPassagePart' => $value['currentPassagePart'],'completed' => $value['completed']);
		}

		return $passageAttemptData;
	}

	/**
	 * function role : Get passage name from passageID
	 * param1 : passageID
	 * @return   string, Passage name
	 * 
	 * */

	function getpassageName($passageID)
	{
		$this->dbEnglish->Select('passageName');
		$this->dbEnglish->from('passageMaster');
		$this->dbEnglish->where('passageID', $passageID );
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();

		return $queryResult[0]['passageName'];
	}

	/**
	 * function role : Save question response of user, user and question info passed through POST
	 * @return  none
	 * 
	 * */

	function saveResponse($userID,$qcode,$questionNo,$timeTaken,$timeTakenExpln,$correct,$sessionID,$userResponse)
	{
		$this->dbEnglish->Select('srno');
		$this->dbEnglish->from('diagnosticQuestionAttempt');
		$this->dbEnglish->where('qcode', $qcode);
		$this->dbEnglish->where('userID', $userID);

		if($this->dbEnglish->count_all_results()>=1)
		{
			$data = array(
			   'questionNo' => $questionNo,
               'userResponse' => $userResponse,
               'timeTaken' => $timeTaken,
               'timeTakenExpln' => $timeTakenExpln,
               'correct' => $correct,
               'sessionID' => $sessionID
            );

			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->where('qcode', $qcode);
			$this->dbEnglish->update('diagnosticQuestionAttempt', $data);
		}
		else
		{
			$data = array(
				'userID' => $userID,
				'questionNo' => $questionNo,
				'qcode' => $qcode,
				'userResponse' => $userResponse,
				'timeTaken' => $timeTaken,
				'timeTakenExpln' => $timeTakenExpln,
				'correct' => $correct,
				'sessionID' => $sessionID
			);

			$this->dbEnglish->insert('diagnosticQuestionAttempt', $data); 
		}
		
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

	function flagUnFlagQues($flagType,$userID,$qcode,$questionNo,$sessionID)
	{
		$this->dbEnglish->Select('srno');
		$this->dbEnglish->from('diagnosticQuestionAttempt');
		$this->dbEnglish->where('qcode', $qcode);
		$this->dbEnglish->where('userID', $userID);

		if($this->dbEnglish->count_all_results()>=1)
		{
			$data = array(
			   'extraParams' => $flagType,
			   'sessionID' => $sessionID
            );

			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->where('qcode', $qcode);
			$this->dbEnglish->update('diagnosticQuestionAttempt', $data);
		}
		else
		{
			$data = array(
				'userID' => $userID,
				'questionNo' => $questionNo,
				'qcode' => $qcode,
				'sessionID' => $sessionID,
				'extraParams' => $flagType

			);

			$this->dbEnglish->insert('diagnosticQuestionAttempt', $data); 
		}

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

	function savePassageDetails($userID,$passageID,$currentPassagePart,$complete,$sessionID)
	{

		$this->dbEnglish->Select('passageAttemptID');
		$this->dbEnglish->from('passageAttempt');
		$this->dbEnglish->where('passageID', $passageID);
		$this->dbEnglish->where('userID', $userID);
		$this->dbEnglish->where('completed', '1');

		if($this->dbEnglish->count_all_results()>=1)
		{
			$data = array(
			   'currentPassagePart' => $currentPassagePart,
			   'completed' => $complete,
			   'sessionID' => $sessionID
            );

			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->where('passageID', $passageID);
			$this->dbEnglish->update('passageAttempt', $data);
		}
		else
		{
			$data = array(
				'userID' => $userID,
				'passageID' => $passageID,
				'sessionID' => $sessionID,
				'currentPassagePart' => $currentPassagePart,
				'type' => 'Diagnostic',
				'completed' => $complete

			);

			$this->dbEnglish->insert('passageAttempt', $data); 
		}
	}

	/**
	 * function role : Update time taken by the user for the diagnostic test
	 * param1 : userID
	 * param2 : total time taken by user in the test
	 * @return  none
	 * 
	 * */

	function updateTestDetails($userID,$totalTimeTaken,$sessionID,$diagnosticTestID)
	{
		$this->dbEnglish->Select('srno');
		$this->dbEnglish->from('diagnosticTestUserDetails');
		$this->dbEnglish->where('userID', $userID);
		$this->dbEnglish->where('diagnosticTestID', $diagnosticTestID);
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();

		if($query->num_rows()>=1)
		{
			$data = array(
			   'totalTime' => $totalTimeTaken
            );

			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->where('diagnosticTestID', $diagnosticTestID);
			$this->dbEnglish->update('diagnosticTestUserDetails', $data);
		}
		else
		{
			$data = array(
				'userID' => $userID,
				'totalTime' => $totalTimeTaken,
				'diagnosticTestID' => $diagnosticTestID
			);

			$this->dbEnglish->set('startDate', 'NOW()', FALSE);
			$this->dbEnglish->insert('diagnosticTestUserDetails', $data); 
		}
	}

	/**
	 * function role : Insert user comments 
	 * @return  none
	 * 
	 * */

	function insertUserComments($userID,$qcode,$sessionID,$comments,$type)
	{
		$data = array(
				'userID' => $userID,
				'qcode' => $qcode,
				'sessionID' => $sessionID,
				'comments' => $comments,
				'type' => $type
		);

		$this->dbEnglish->insert('userComments', $data); 
	}

	/**
	 * function role : Get time spent information
	 * param1 : userID
	 * param2 : unique diagnotic test id
	 * @return   integer, time spendt info ; null, if not started attempting
	 * 
	 * */

	function getTimeSpentOnTest($userID,$diagnosticTestID)
	{
		$this->dbEnglish->Select('srno,totalTime');
		$this->dbEnglish->from('diagnosticTestUserDetails');
		$this->dbEnglish->where('userID', $userID);
		$this->dbEnglish->where('diagnosticTestID', $diagnosticTestID);
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();

		if($query->num_rows()>=1)
			return $queryResult[0]['totalTime'];
		else
			return null;
	}

	/**
	 * function role : Get essay name from essay id
	 * param1 : essayID
	 * @return   string, essay name
	 * 
	 * */

	function getEssayName($essayID)
	{
		$this->dbEnglish->Select('essayTitle');
		$this->dbEnglish->from('essayMaster');
		$this->dbEnglish->where('topicID', $essayID );
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();

		return $queryResult[0]['essayTitle'];
	}

	/**
	 * function role : Get user specific essay attempt information 
	 * param1 : userID
	 * @return   array, essay attempt info
	 * 
	 * */

	function getUserEssayAttemptData($userID)
	{
		$essayAttemptData = array();
		$this->dbEnglish->Select('essayID,userResponse');
		$this->dbEnglish->from('essayDetails');
		$this->dbEnglish->where('userID', $userID );
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();

		foreach($queryResult as $key=>$value)
		{
			$essayAttemptData[$value['essayID']] = array('userResponse' => $value['userResponse']);
		}
		return $essayAttemptData;
	}

	/**
	 * function role : Save essay details attempted by user, update if attempted earlier else insert
	 * param1 : userID
	 * param2 : essayID
	 * param3 : user's response
	 * param4 : current statatus of the user on essay
	 * param5 : sessionID
	 * @return  none
	 * 
	 * */

	function saveEssayDetails($userID,$essayID,$userResponse,$status,$sessionID)
	{

		$this->dbEnglish->Select('srno');
		$this->dbEnglish->from('essayAttempt');
		$this->dbEnglish->where('essayID', $essayID);
		$this->dbEnglish->where('userID', $userID);

		if($this->dbEnglish->count_all_results()>=1)
		{
			$data = array(
			   'userResponse' => $userResponse,
			   'sessionID' => $sessionID
            );

			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->where('essayID', $essayID);
			$this->dbEnglish->update('essayAttempt', $data);
			$this->updateEssayWrittingSystem('Save',$userID,$essayID,$userResponse,$status); 
		}
		else
		{
			$data = array(
				'userID' => $userID,
				'essayID' => $essayID,
				'sessionID' => $sessionID,
				'userResponse' => $userResponse

			);

			$this->dbEnglish->insert('essayAttempt', $data);
			$this->updateEssayWrittingSystem('Start',$userID,$essayID,$userResponse,$status); 
			
		}
	}

	/**
	 * function role : Update Essay writing system based on user's essay attempt in mindspark.
	 * Note : Essay writing system is a seperate system. Need to update it so the essay reviewers get notification regarding grading user essays
	 * param1 : userID
	 * param2 : essayID
	 * param3 : user's response
	 * param4 : current statatus of the user on essay
	 * param5 : sessionID
	 * @return  none
	 * 
	 * */

	function updateEssayWrittingSystem($type,$userID,$essayID,$userResponse,$status)
	{
		$userID = -1 * $userID;
		$serverURL = $_SERVER['HTTP_HOST'];
		
		if(strpos($serverURL,$this->config->item('staging_url')) !==false){
			$server = 'https://mindspark.in';
		}else if(strpos($serverURL,$this->config->item('mindspark_url')) !==false){
			$server = 'https://mindspark.in';
		}else {
			$server = $this->config->item('offline_url');
		}
		// if(strpos($serverURL,'educationalinitiatives') !==false)
		// 	$server = 'http://educationalinitiatives.com';
		// else 
		// 	$server = 'http://192.168.0.7';
		
		$essayWritterURL = $server.'/essaywriting'.'/mseng_essays.php';
		$essayTitle = $this->getEssayName($essayID);

		if($type=='Save')
		{
			$essaySystemEssayID = $this->getEssaySystemEssayID($userID,$essayID);
			$postArr = array('userID' => $userID , 'submitEssay' => 1, 'essayID'=>$essaySystemEssayID , 'eTitle' => $essayTitle,'topicID' => $essayID , 'status' => $status , 'essayBody' => $userResponse,'essayTime' => 0);

			
		}
		else if($type=='Start')
		{
			$postArr = array('userID' => $userID , 'submitEssay' => 1, 'essayID'=>0 , 'eTitle' => $essayTitle,'topicID' => $essayID,'essayTime' => 0, 'essayBody' => $userResponse);
		}



		$this->do_post($essayWritterURL,$postArr);
	} 

	/**
	 * function role : fetch essay system essayID from unique essay id generated for Mindspark english
	 * param1 : userID
	 * param2 : Mindspark english essayID
	 * @return  string, essayID
	 * 
	 * */

	function getEssaySystemEssayID($userID,$essayID)
	{
		$this->dbEnglish->Select('essayID');
		$this->dbEnglish->from('essayDetails');
		$this->dbEnglish->where('userID', $userID);
		$this->dbEnglish->where('topicID', $essayID);
		$this->dbEnglish->order_by('lastModified','desc');
		$this->dbEnglish->limit(1);
		$query = $this->dbEnglish->get();

		$queryResult = $query->result_array();
		if (sizeof($queryResult)>0) {
			return $queryResult[0]['essayID'];
		}
		else
			return "''";
	}

	/**
	 * function role : Fetch feedback posted by evaluaters on essay attempted by user
	 * param1 : userID
	 * param2 : unique essayID
	 * param3 : fetch feedback flag, 1=>fetching feedback
	 * @return  POST, essay feedback
	 * 
	 * */

	function fetchEssayFeedback($userID,$essayID,$fetchFeedback)
	{
		$userID = -1 * $userID;
		$serverURL = $_SERVER['HTTP_HOST'];
		if(strpos($serverURL,$this->config->item('staging_url')) !==false){
			$server = 'https://mindspark.in';
		}else if(strpos($serverURL,$this->config->item('mindspark_url')) !==false){
			$server = 'https://mindspark.in';
		}else {
			$server = $this->config->item('offline_url');
		}
		$essayWritterURL = $server.'/essaywriting'.'/mseng_essays.php';

		$essaySystemEssayID = $this->getEssaySystemEssayID($userID,$essayID);

		$postArr = array('userID' => $userID , 'fetchEssayFeedback' => $fetchFeedback , 'essayID' => $essaySystemEssayID);
		return $this->do_post($essayWritterURL,$postArr);
	}

	/**
	 * function role : Posting data using curl
	 * param1 : Url for curl request
	 * param2 : data to be posted
	 * @return  Curl Repsonse
	 * 
	 * */

	function do_post($url, $data)
	{
	  $ch = curl_init($url);

	  curl_setopt($ch, CURLOPT_POST, 1);
	  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	  $response = curl_exec($ch);
	  curl_close($ch);

	  echo $response;
	}

	/**
	 * function role : Generate diagnostic test report from all attempted questions in diagnostic test
	 * param1 : schoolCode
	 * @return  none
	 * 
	 * */

	function generateDiagnosticTestReportData($schoolCode)
	{
		$this->dbEnglish->Select('userID');
		$this->dbEnglish->from('userDetails');
		$this->dbEnglish->where('schoolCode', $schoolCode);
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();
		
		foreach ($queryResult as $key => $value) {
			$this->insertPerformanceData($queryResult[$key]['userID']);
		}
	}

	/**
	 * function role : Insert diagnostic test attempt data for user in diagnostic user summaty table for further analysis
	 * param1 : userID
	 * @return  none
	 * 
	 * */	

	function insertPerformanceData($userID)
	{
		$qcodes = $this->getTopicWiseQcodes();

		$totalAttempts = array('Grammar' => 10, 'Vocabulary' => 15, 'Comprehension' =>  32, 'Listening' => 11 );
		// $totalAttempts = $this->getTotalAttemptedQues('total',$qcodes,$userID);
		$correct = $this->getTotalAttemptedQues('correct',$qcodes,$userID);

		foreach ($totalAttempts as $key => $value) {
			if($totalAttempts[$key] != 0)
				$accuracy[$key] = round(($correct[$key] / $totalAttempts[$key]) , 4);
			else
				$accuracy[$key] = 0;
		}

		$data = array(
				'totalQA' => implode('~', $totalAttempts),
				'totalC' => implode('~', $correct),
				'accuracy' => implode('~', $accuracy)
			);

		$this->dbEnglish->where('userID', $userID);
		$this->dbEnglish->where('diagnosticTestID', 1);
		$this->dbEnglish->update('diagnosticTestUserDetails', $data);
		

	}

	/**
	 * function role : [Temp function] Created for fetching topic wise qcodes for passages and question for mt. carmel banglore diagnostic test
	 * @return  array, qcode info topic wise
	 * 
	 * */	


	function getTopicWiseQcodes()
	{
		 $topicArr = array();
		 $topicQuery1  =   "select tm.name AS topic, group_concat(q.qcode) as qcodes from questions q, skillMaster skm , topicMaster tm
where q.skillID = skm.skillID and q.topicID = tm.topicID and tm.name NOT LIKE 'Comprehension' group by topic";
        $result = $this->db->query($topicQuery1);
        if($result->num_rows() >0)
        {
        	$data = $result->result_array();
        	
        	foreach ($data as $key => $val)
        	{
        		$topicArr[$val['topic']] = $val['qcodes'];
        	}
        }

        $topicQuery2  =   "select tm.name AS topic, group_concat(q.qcode) as qcodes from questions q, topicMaster tm
where q.topicID = tm.topicID and q.passageID NOT IN (16, 17) and tm.name LIKE 'Comprehension' group by tm.name";
        $result = $this->db->query($topicQuery2);
        if($result->num_rows() >0)
        {
        	$data = $result->result_array();
        	
        	foreach ($data as $key => $val)
        	{
        		$topicArr[$val['topic']] = $val['qcodes'];
        	}
        }

         $topicQuery3  =   "select tm.name AS topic, group_concat(q.qcode) as qcodes from questions q, topicMaster tm
where q.topicID = tm.topicID and q.passageID IN (16, 17) and tm.name LIKE 'Comprehension' group by tm.name";
        $result = $this->db->query($topicQuery3);
        if($result->num_rows() >0)
        {
        	$data = $result->result_array();
        	
        	foreach ($data as $key => $val)
        	{
        		$topicArr['Listening'] = $val['qcodes'];
        	}
        }

        return $topicArr;
	}

	/**
	 * function role : Get total correct/incorrect for diagnostic test question attempt  
	 * param1 : type => correct/incorrect
	 * param2 : qcodes
	 * param3 : userID
	 * @return  none
	 * 
	 * */	


	function getTotalAttemptedQues($type,$qcodes,$userID)
	{
		$demoTestDay = '2015-03-30';
		$totalAttemptedQuesArr = array();
		$topicMasterArr = array(0 => 'Grammar', 1 => 'Vocabulary' , 2 => 'Comprehension' , 3 => 'Listening');

		foreach($topicMasterArr as $key=>$val)
		{
			$qcodeArr = array();
			$qcodeArr = explode(',', $qcodes[$val]);
			$this->dbEnglish->from('diagnosticQuestionAttempt');
			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->where("date(lastModified)>'".$demoTestDay."'");
			$this->dbEnglish->where_in('qcode', $qcodeArr);

			if($type=='correct')
				$this->dbEnglish->where('correct > 0.5');
			$totalAttemptedQuesArr[$val] = $this->dbEnglish->count_all_results();
		}

		return $totalAttemptedQuesArr;
		
	}

	/**
	 * function role : Retun diagnsotic test attempt statistics
	 * param1 : userID (optional if want for some specific student) for the passed schoolCode
	 * param2 : schoolCode
	 * @return  array, report data
	 * 
	 * */

	function getDiagnosticTestReportData($userID,$schoolCode)
	{
		$jsondata = array();
		$topicMasterArr = array(0 => 'Grammar', 1 => 'Vocabulary' , 2 => 'Comprehension' , 3 => 'Listening');
		$userAccuracyData = $this->fetchUserAccuracyData($schoolCode);
		$getAvgAndMaxData = $this->getAvgAndMaxAccuracy($userAccuracyData);
		$avgAccuracyData = $getAvgAndMaxData[0];
		$highestAccuracyData = $getAvgAndMaxData[1];
		$this->dbEnglish->Select('userID');
		$this->dbEnglish->from('userDetails');
		$this->dbEnglish->where('schoolCode', $schoolCode);
		if($userID!="")
			$this->dbEnglish->where('userID', $userID);
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();

		foreach ($queryResult as $key => $value) {

			if( array_key_exists($value['userID'], $userAccuracyData))
			{
				$reportData = array();
				$userID = $value['userID'];
				$childName  = $this->user_model->getChildName($userID);
				$data = explode('~',$userAccuracyData[$userID]);

				if(count($data) == count($topicMasterArr))
				foreach ($data as $key => $value) 
				{
					$reportData[$childName][$topicMasterArr[$key]]['your'] = $data[$key];
					$reportData[$childName][$topicMasterArr[$key]]['avg'] = $avgAccuracyData[$key];
					$reportData[$childName][$topicMasterArr[$key]]['highest'] = $highestAccuracyData[$key];
				}
				array_push($jsondata ,$reportData);
			}
		}
		
		return $jsondata ;
	}

	/**
	 * function role : Fetch diagnostic test accuracy data for users  from diagnostuc test user analysis table
	 * param1: schoolCode
	 * @return  array, user accuracy info
	 * 
	 * */

	function fetchUserAccuracyData($schoolCode)
	{
		$userAccuracyData = array();
		$userIDArr = explode(',', $this->getUsersInSchool($schoolCode));
		$this->dbEnglish->Select('userID,totalQA,totalC,accuracy');
		$this->dbEnglish->from('diagnosticTestUserDetails');
		$this->dbEnglish->where('diagnosticTestID', 1);
		$this->dbEnglish->where_in('userID', $userIDArr);
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();

		foreach ($queryResult as $key => $value) {
			$userAccuracyData[$value['userID']] = $value['accuracy'];
		}

		return $userAccuracyData;

	}

	/**
	 * function role : Fetch all users part of the passed schoolCode
	 * param1: schoolCode
	 * @return  string, list of users
	 * 
	 * */

	function getUsersInSchool($schoolCode)
	{
			$this->dbEnglish->select('group_concat(userID) as users');
			$this->dbEnglish->from('userDetails');
			$this->dbEnglish->where('schoolCode', $schoolCode);
			$query = $this->dbEnglish->get();
			$queryResult = $query->result_array();

			return $queryResult[0]['users'];
	}

	/**
	 * function role : Calculate max and avg accuracy from the passaged user specific attempt statistics
	 * param1: User specific accuracy statistics
	 * @return  array, Max and avg accuracy
	 * 
	 * */


	function getAvgAndMaxAccuracy($userAccuracyData)
	{

		$total  = array('0' =>  0 , '1' =>  0 , '2' =>  0 , '3' =>  0);
		$avgAccuracy = array();
		$maxAccuracy = array('0' =>  0 , '1' =>  0 , '2' =>  0 , '3' =>  0);
		$count = 0;
		foreach ($userAccuracyData as $key => $value) {
			if (strpos($userAccuracyData[$key],'~') !== false)
			$data[$key] = explode('~',$userAccuracyData[$key]);
			
		}
		foreach ($data as $key => $value) {
			$count++;
			foreach ($value as $k => $val) {
				if(array_key_exists($k, $value))
				{
					$total[$k] +=  $value[$k];
					if($value[$k] > $maxAccuracy[$k])
					$maxAccuracy[$k] = $value[$k];
				}			
			}	
		}

		foreach ($total as $key => $value) {
			$avgAccuracy[$key] = round(($total[$key]/$count),4);
			$maxAccuracy[$key] = $maxAccuracy[$key] ;
		}
		return array($avgAccuracy,$maxAccuracy);
		
	}

	/**
	 * function role : Save user feedback on the diaagnostic test
	 * param1 : userID
	 * param2 : Feedback
	 * @return  none
	 * 
	 * */

	function saveUserFeedback($userID,$feedbackArr)
	{
		foreach($feedbackArr as $key => $feedback)
		{
			$data = array(
				'userID' => $userID,
				'qId' => $key,
				'userFeedback' => $feedback
			);

			$this->dbEnglish->insert('userFeedback', $data); 
		}
	}

	/**
	 * function role : Temporary function created for correcting diagnotic test statistics for Mt. Carmel school teacher test. Will not be needed now.
	 * @return   none
	 * 
	 * */

	function correctReportData()
	{

		$this->dbEnglish->Select('userID,qcode,userResponse,correct');
		$this->dbEnglish->from('diagnosticQuestionAttempt');
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();

		foreach ($queryResult as $key => $value) 
		{
			$this->dbEnglish->Select('qcode,correctAnswer');
			$this->dbEnglish->from('questions');
			$this->dbEnglish->where('qcode',$value['qcode']);
			$questionsQuery = $this->dbEnglish->get();
			$questionsQueryResult = $questionsQuery->result_array();
		   if(count($questionsQueryResult)>0)
		   if($value['userResponse'] == $questionsQueryResult[0]['correctAnswer'] && $value['correct']==0)
			{

				// $sql = "update diagnosticQuestionAttempt set correct = 1 where userID=".$value['userID']." and qcode=".$value['qcode'];
				// echo $sql."<br>";
				

				//exit; 
				
				$data = array(
				'correct' => 1
				);

				

				$this->dbEnglish->where('userID', $value['userID']);
				$this->dbEnglish->where('qcode', $value['qcode']);
				$this->dbEnglish->update('diagnosticQuestionAttempt', $data);
			}
		}

		echo "Done";
	}

}

?>