<?php

Class teacherdms_model extends CI_model
{

	public function __construct() 
	{
		$this->dbEnglish = $this->load->database('mindspark_english',TRUE);
 	}

 	function getCurrentStatus($grade,$userid)
 	{

 		if($grade == 3)
 			$grade = 4;
 		
 		$isContentExhaustedTeacher = array('isContentExhaustedTeacher'=>0);
		$this->session->set_userdata($isContentExhaustedTeacher);
 		//echo "<pre>";print_r($this->session->all_userdata());echo "</pre>";
 		$this->dbEnglish->Select('*');
		$this->dbEnglish->from('userCurrentStatus');
		$this->dbEnglish->where('userID', $userid);
		$query = $this->dbEnglish->get();
		$get_rows =  $query->result_array(); 
		$num_rows = count($get_rows);
		
		$contentType = array('Passage_Reading','Passage_Conversation','Free_Question');
		foreach ($contentType as $key => $value) 
		{
			$this->dbEnglish->Select('*');
			$this->dbEnglish->from('userLevelAndAccuracyLog');
			$this->dbEnglish->where('contentType', $value);
			$this->dbEnglish->where('level', $grade);
			$this->dbEnglish->where('userID', $userid);
			$query = $this->dbEnglish->get();
			$get_rows_level =  $query->result_array(); 
			$num_rows_level = count($get_rows_level);

			if($num_rows_level == 0)
			{
				$dataLevel = array(
					'userID'              => $userid,
					'contentType'         => $value,
					'quesPsgAttemptCount' => 0,
					'accuracy'            => 0,
					'level'               => $grade,
				);
				$this->dbEnglish->insert('userLevelAndAccuracyLog', $dataLevel);
			}
		}
		//INSERT IN USERLEVELACCURACYLOG TABLE IF SELECTED GRADE IS NOT THERE 

		
		if($num_rows > 0)
		{
			$data = array(
				'userID'             => $userid,
				'passageLevel'       => $get_rows[0]['passageLevel'],
				'conversationLevel'  => $get_rows[0]['conversationLevel'],
				'freeQuesLevel'      => $get_rows[0]['freeQuesLevel'],
				'currentContentType' => $get_rows[0]['currentContentType'],
				'refID'              => $get_rows[0]['refID'],
				'completed'          => $get_rows[0]['completed'],
				'groupSkillID'       => $get_rows[0]['groupSkillID']
			);

			$this->dbEnglish->insert('userCurrentStatusDmsLog', $data); 
		}

		$this->dbEnglish->Select('*');
		$this->dbEnglish->from('userCurrentStatusDmsLog');
		$this->dbEnglish->where('passageLevel', $grade);
		$this->dbEnglish->where('conversationLevel', $grade);
		$this->dbEnglish->where('freeQuesLevel', $grade);
		$this->dbEnglish->where('userID', $userid);
		$this->dbEnglish->order_by('srno','desc');
		$this->dbEnglish->limit(1);
		$query = $this->dbEnglish->get();
		$get_rowsDms =  $query->result_array(); 
		
		$num_rowsDms = count($get_rowsDms);
		
		if($num_rowsDms == 0)
		{
			
			$dataUpdate = array(
				'passageLevel'       => $grade,
				'conversationLevel'  => $grade,
				'freeQuesLevel'      => $grade,
				'currentContentType' => 'N/A',
				'refID'              =>  0,
				'completed'          => '0',
				'groupSkillID'       => 0
			);
			$this->dbEnglish->where('userID', $userid);
			$this->dbEnglish->update('userCurrentStatus', $dataUpdate);

			$dataSession = array('passageLevel'=>$grade,'conversationLevel' => $grade,'freeQuesLevel'=>$grade,'currentContentType'=>'N/A','refID'=>0,'completed'=>'0');
			$this->session->set_userdata($dataSession);

			$array_items = array('sessionPsgTimeLimit' => '', 'sessionFreeQuesTimeLimit' => '', 'sessionFlowStarted' => '', 'sessionPassages' => '', 'currentPsgQuestions' => '', 'sessionfreeQues' => '' ,'skillID' => '', 'subSkillID' => '');
			$this->session->unset_userdata($array_items);
			
		}
		else
		{
			
			$dataUpdate = array(
				'passageLevel'       => $get_rowsDms[0]['passageLevel'],
				'conversationLevel'  => $get_rowsDms[0]['conversationLevel'],
				'freeQuesLevel'      => $get_rowsDms[0]['freeQuesLevel'],
				'currentContentType' => $get_rowsDms[0]['currentContentType'],
				'refID'              => $get_rowsDms[0]['refID'],
				'completed'          => $get_rowsDms[0]['completed'],
				'groupSkillID'       => $get_rowsDms[0]['groupSkillID']
			);
			
			$this->dbEnglish->where('userID', $userid);
			$this->dbEnglish->update('userCurrentStatus', $dataUpdate);

			$array_items = array('sessionPsgTimeLimit' => '', 'sessionFreeQuesTimeLimit' => '', 'sessionFlowStarted' => '', 'sessionPassages' => '', 'currentPsgQuestions' => '','sessionfreeQues' => '', 'skillID' => '', 'subSkillID' => '');
			$this->session->unset_userdata($array_items);

			$dataSession = array('passageLevel'=>$get_rowsDms[0]['passageLevel'],'conversationLevel' => $get_rowsDms[0]['conversationLevel'],'freeQuesLevel'=>$get_rowsDms[0]['freeQuesLevel'],'currentContentType'=>$get_rowsDms[0]['currentContentType'],'refID'=>$get_rowsDms[0]['refID'],'completed'=>$get_rowsDms[0]['completed']);
			$this->session->set_userdata($dataSession);
		}
		//echo "<pre>";print_r($this->session->all_userdata());echo "</pre>";

		$this->dbEnglish->Select('max(srno) as srno');
		$this->dbEnglish->from('userCurrentStatusDmsLog');
		$this->dbEnglish->where('passageLevel', $grade);
		$this->dbEnglish->where('conversationLevel', $grade);
		$this->dbEnglish->where('freeQuesLevel', $grade);
		$this->dbEnglish->where('userID', $userid);
		$query = $this->dbEnglish->get();
		$get_rowsDms =  $query->row_array(); 
		$srno = $get_rowsDms['srno'];

		if($srno != '')
		{
			$query = "DELETE  FROM userCurrentStatusDmsLog where userid = $userid and passageLevel = $grade and conversationLevel = $grade and freeQuesLevel = $grade and srno != $srno ";
			$resultData=$this->dbEnglish->query($query);
		}
 	}
}

?>