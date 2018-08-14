<?php

Class Essay_model extends CI_model
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
	 * function role : Save essay details attempted by user, update if attempted earlier else insert
	 * param1 : userID
	 * param2 : essayID
	 * param3 : user's response
	 * param4 : current statatus of the user on essay
	 * param5 : sessionID
	 * @return  none
	 *
	 * */

	function saveEssayDetails($userID,$essayID,$userResponse,$status,$sessionID,$timeTaken)
	{
		$writerID = -1 * $userID;
		$topicID = $essayID;

		$this->dbEnglish->Select('essayID,topicID');
		$this->dbEnglish->from('ews_essayDetails');
		$this->dbEnglish->where('topicID', $topicID);
		$this->dbEnglish->where('writerID', $writerID);
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();

		if(count($queryResult)>=1)
		{
			//echo 'ananddsadgshagdhgas<br>';
			$data = array(
			   'userResponse' => $userResponse,
			   'sessionID' => $sessionID
            );

			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->where('essayID', $essayID);
			$this->dbEnglish->update('essayAttempt', $data);
			$this->updateEssayWrittingSystem('Save',$userID,$essayID,$userResponse,$status,$timeTaken);
		}
		else
		{

			$data = array(
				'userID' => $userID,
				'essayID' => $essayID,
				'sessionID' => $sessionID,
				'userResponse' => $userResponse

			);
			$query = $this->dbEnglish->insert('essayAttempt', $data);
			$this->updateEssayWrittingSystem('Start',$userID,$essayID,$userResponse,$status,$timeTaken);

			$dataArr = array(
			   'userResponse' => $userResponse,
			   'sessionID' => $sessionID
            );

			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->where('essayID', $essayID);
			$this->dbEnglish->update('essayAttempt', $dataArr);
			$this->updateEssayWrittingSystem('Save',$userID,$essayID,$userResponse,$status,$timeTaken);

		}
	}

	function getEssayTopics($userID,$msLevel)
	{
		$attemptedEssayIDs = $this->getUserAttemptedEssays($userID);
		$attemptedEssayIDsArr = explode(',',$this->getUserAttemptedEssays($userID));

		$this->dbEnglish->Select('essayID,essayTitle,msLevel');
		$this->dbEnglish->from('essayMaster');
		$where = "FIND_IN_SET('".$msLevel."', msLevel)";
		$this->dbEnglish->where($where);
		$this->dbEnglish->where_not_in('essayID',$attemptedEssayIDsArr);
		$query = $this->dbEnglish->get();

		$queryResult = $query->result_array();
		return $queryResult;
	}

	function getUserAttemptedEssays($userID)
	{
		$this->dbEnglish->Select('group_concat(essayID) as essayids');
		$this->dbEnglish->from('essayAttempt');
		$this->dbEnglish->where('userID', $userID);
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();
		return $queryResult[0]['essayids'];
	}


	/**
	*
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

	function updateEssayWrittingSystem($type,$userID,$essayID,$userResponse,$status,$timeTaken)
	{
		$userID = -1 * $userID;
		$serverURL = $_SERVER['HTTP_HOST'];

		if(strpos($serverURL,'122.248.236.40') !==false){
			$server = 'http://122.248.236.40';
		}else if(strpos($serverURL,'mindspark') !==false){
			$server = 'http://mindspark.in';
		}
		else {
			$server = 'http://27.109.14.77:8080';
		}

		//$server  = 'http://localhost';

		$essayWritterURL = $server.'/essaywriting'.'/mseng_essays.php';

		$essayTitle = $this->getEssayName($essayID);

		if($type=='Save')
		{
			$essaySystemEssayID = $this->getEssaySystemEssayID($userID,$essayID);
			$postArr = array('userID' => $userID , 'submitEssay' => 1, 'essayID'=>$essaySystemEssayID , 'eTitle' => $essayTitle,'topicID' => $essayID , 'status' => $status , 'essayBody' => $userResponse,'essayTime' => $timeTaken);


		}
		else if($type=='Start')
		{
			$postArr = array('userID' => $userID , 'submitEssay' => 1, 'essayID'=>0 , 'eTitle' => $essayTitle,'topicID' => $essayID,'essayTime' => $timeTaken, 'essayBody' => $userResponse);
		}



		$this->do_post($essayWritterURL,$postArr);
	}
	/**
	 * function role : Get essay name from essay id
	 * param1 : essayID
	 * @return   string, essay name
	 *
	 * */

	function getEssayName($essayID,$type="")
	{

		//$this->output->enable_profiler(TRUE);
		$this->dbEnglish->_protect_identifiers = FALSE;
		$essayIDs = explode('||',$essayID);

		if($type=='list')
		{
			$arr = explode('||',$essayID);
			$ids = sprintf('FIELD(essayID, %s)', implode(',',$arr));

			$this->dbEnglish->Select("group_concat(essayTitle order by($ids) SEPARATOR '||') essayTitle");
		}
		else
			$this->dbEnglish->Select('essayTitle');
		$this->dbEnglish->from('essayMaster');
		if($type=='list')
		{
			$this->dbEnglish->where_in('essayID',$essayIDs);
		}
		else
			$this->dbEnglish->where('essayID', $essayID );
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();
		$this->dbEnglish->protect_identifiers = TRUE;
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
		$this->dbEnglish->Select('essayID,userResponse,extraParams');
		$this->dbEnglish->from('essayAttempt');
		$this->dbEnglish->where('userID', $userID );
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();

		foreach($queryResult as $key=>$value)
		{
			$essayAttemptData[$value['essayID']] = array('userResponse' => $value['userResponse'], 'extraParams' => $value['extraParams']);
		}
		return $essayAttemptData;
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
		$this->dbEnglish->from('ews_essayDetails');
		$this->dbEnglish->where('writerID', $userID);
		$this->dbEnglish->where('topicID', $essayID);
		$this->dbEnglish->order_by('lastModified','asc');
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

		if(strpos($serverURL,'122.248.236.40') !==false){
			$server = 'http://mindspark.in';
		}else if(strpos($serverURL,'mindspark') !==false){
			$server = 'http://mindspark.in';
		}else {
			$server = 'http://27.109.14.77:8080';
		}

		// if(strpos($serverURL,'educationalinitiatives') !==false)
		// 	$server = 'http://mindspark.in';
		// else
		// 	$server = 'http://27.109.14.77:8080';
		//$server  = 'http://localhost';

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

	function fetchEssaySummary($userID)
	{
		$essaySummary = array('inCompleteEssays'=>null,'completedEssays'=>null,'gradedEssays'=>null,'inCompleteEssaysName'=>null,'completedEssaysName'=>null,'gradedEssaysName'=>null,'inCompleteEssayID'=>null,'completedEssaysID'=>null,'gradedEssaysID'=>null);
		$userID = '-'.$userID;
		$this->dbEnglish->Select("status,group_concat(topicID SEPARATOR '||') topics,group_concat(essayID SEPARATOR '||') essays");
		$this->dbEnglish->from('ews_essayDetails');
		$this->dbEnglish->where('writerID',$userID);
		$this->dbEnglish->group_by('status');
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();

		if(count($queryResult)>0)
		{

			foreach ($queryResult as $key => $valueArr) {
				if($valueArr['status']==0)
				{
					$essaySummary['inCompleteEssays'] = $valueArr['topics'];
					$essaySummary['inCompleteEssayID'] = $valueArr['essays'];
					$essaySummary['inCompleteEssaysName'] = $this->getEssayName($valueArr['topics'],'list');
				}
				elseif ($valueArr['status']==1)
				{
					$essaySummary['completedEssays'] = $valueArr['topics'];
					$essaySummary['completedEssaysID'] = $valueArr['essays'];
					$essaySummary['completedEssaysName'] = $this->getEssayName($valueArr['topics'],'list');
				}
				elseif ($valueArr['status']==2)
				{
					$essaySummary['gradedEssays'] = $valueArr['topics'];
					$essaySummary['gradedEssaysID'] = $valueArr['essays'];
					$essaySummary['gradedEssaysName'] = $this->getEssayName($valueArr['topics'],'list');
				}
			}
		}

		return $essaySummary;
	}

	function fetchEssayResponse($userID,$topicID,$essayID)
	{

		$data = array('userResponse' => '',	'timeTaken' => 0);


		$this->dbEnglish->Select('userResponse');
		$this->dbEnglish->from('essayAttempt');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('essayID',$topicID);
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();



		if(count($queryResult)>0)
		{
			$data["userResponse"]=$queryResult[0]['userResponse'];

		};



		//else
			//return "";

		$this->dbEnglish->Select('timeTaken');
		$this->dbEnglish->from('ews_essayDetails');
		$this->dbEnglish->where('essayID',$essayID);
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();
		if(count($queryResult)>0)
		{
			$data["timeTaken"]=$queryResult[0]['timeTaken'];

		};



		return $data;
		//return $data;
		//echo count($queryResult).'<br>';

	}

}

?>