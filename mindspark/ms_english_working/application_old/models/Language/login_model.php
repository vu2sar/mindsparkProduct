<?php

Class Login_model extends MY_Model
{
	public function __construct() 
	{
		 parent::__construct();
		 $this->dbEnglish = $this->load->database('mindspark_english',TRUE);
		 $this->Companies_db = $this->dbEnglish;

	       // Pass reference of database to the CI-instance
	     $CI =& get_instance();
	     $CI->Companies_db =& $this->Companies_db; 
	}

	/**
	 * function role : Check if user exists
	 * @param1   user name
	 * @param2   password
	 * @return   boolean, 1=>if exists, 0=>if not exists
	 * 
	 * */

	function isUserValid($user_id,$password = '')
	{
		$this->dbEnglish->from('userDetails');
		$this->dbEnglish->where('userID',$user_id);
		//$this->dbEnglish->where("password=password('".$password."')");
		$this->dbEnglish->where("enabled",1);
		if($this->dbEnglish->count_all_results()>=1)
			return 1;
		else
			return 0;
	}

	/**
	 * function role : Logging user current session
	 * @param1   userID
	 * @param2   logged in browser details
	 * @return   MS English unique sessionID
	 * 
	 * */
	function getLastLoginSessionID($userID)
	{
		$data = array(
			'userID' => $userID
		);
		$this->dbEnglish->select('sessionID,userID,logout_flag');
		$this->dbEnglish->from('sessionStatus');
		$this->dbEnglish->where($data);
		$this->dbEnglish->order_by('sessionID','desc');
		$this->dbEnglish->limit(1);
			
		$result_array = $this->dbEnglish->get()->result_array();	
		if(count($result_array) > 1)
		{
			return $result_array[0]['sessionID'];
		}
		else
		{
			return '';
		}
	}
	function getLastLogoutStatus($userID)
	{
		$data = array(
			'userID' => $userID
		);
		$this->dbEnglish->select('sessionID,userID,logout_flag');
		$this->dbEnglish->from('sessionStatus');
		$this->dbEnglish->where($data);
		$this->dbEnglish->order_by('sessionID','desc');
		$this->dbEnglish->limit(1);
			
		$result_array = $this->dbEnglish->get()->result_array();
		if(count($result_array) > 0)
		{
			if($result_array[0]['logout_flag'] == 1)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	}
	function isUserActive($userID,$sessionID)
	{
		
		$data = array(
			'userID' => $userID,
			'sessionID' => $sessionID
		);
		
		$this->dbEnglish->select('logout_flag');
		$this->dbEnglish->from('sessionStatus');
		$this->dbEnglish->where($data);
		$result_object = $this->dbEnglish->get();
		$result_array = $result_object->result_array();
		if(count($result_array) > 0)
		{
			if($result_array[0]['logout_flag'] == 1)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
			
	}
	function updateLastLogoutStatus($userID, $childclass,$logOutReasonFlag)
	{
		$data = array(
			'userID' => $userID,
		);

		if(!$logOutReasonFlag)
			$logOutReasonFlag=1;

		$this->dbEnglish->select('sessionID');
		$this->dbEnglish->from('sessionStatus');
		$this->dbEnglish->where($data);
		$this->dbEnglish->order_by('sessionID','desc');
		$this->dbEnglish->limit(1);
		$result = $this->dbEnglish->get();
		$result_array = $result->result_array();


		$this->dbEnglish->select('childclass');
		$this->dbEnglish->from('userDetails');
		$this->dbEnglish->where($data);
		$childResult = $this->dbEnglish->get();
		$result_array_child = $childResult->result_array();
		
		
		$data = array(
			'userID' => $userID,
			'sessionID' => $result_array[0]['sessionID']
		);
		$sessionID = $result_array[0]['sessionID'];

		$this->dbEnglish->select('endTime');
		$this->dbEnglish->from('sessionStatus');
		$this->dbEnglish->where($data);
		$result_endTime = $this->dbEnglish->get();
		$result_array_endTime = $result_endTime->row_array();
		
		/*GET MAX LAST MODIFIED AMONG THE 4 TABLES AND UPDATE THAT AS END TIME IN SESSION STATUS*/
		if($userID !='' && $sessionID != '')
		{
			if($result_array_endTime['endTime'] == '')
			{
				$getLastModified = $this->dbEnglish->query("SELECT MAX(t1.lastModified) as maxLastModified FROM (SELECT max(lastModified) as lastModified FROM passageAttempt where userID = $userID AND sessionID = $sessionID UNION SELECT max(lastModified) as lastModified FROM questionAttempt_class".$result_array_child[0]['childclass']." where userID = $userID AND sessionID = $sessionID UNION SELECT max(lastModified) as lastModified FROM IGREAttemptDetails where userID = $userID AND sessionID = $sessionID UNION SELECT max(lastModified) as lastModified FROM essayAttempt where userID = $userID AND sessionID = $sessionID) as t1");
			
			
				if($getLastModified->num_rows() > 0)
				{
					$result_array_essay = $getLastModified->row_array();

					$endTime = 	$result_array_essay['maxLastModified'];
				}
				else
					$endTime = '';

				if($endTime != '')
				{

					$this->dbEnglish->set('endTime', "'$endTime'", FALSE);
				}
				else
				{
					/*IF NO MAX LAST MODIFIED FOUND SET END TIME AS 2 MIN AHEAD OF THE START TIME*/

					$this->dbEnglish->select('max(startTime) + INTERVAL 2 MINUTE as startTime');
					$this->dbEnglish->from('sessionStatus');
					//$this->dbEnglish->where('userid', $userID);
					$this->dbEnglish->where($data);
					$result_startTime = $this->dbEnglish->get();
					$result_startTime_row = $result_startTime->row_array();
					$result_startTime_row = $result_startTime_row['startTime'];

					$this->dbEnglish->set('endTime', "'$result_startTime_row'", FALSE);
				}

				$this->dbEnglish->set('logoutReason', $logOutReasonFlag);    // for multisystem logout reason. 
				$this->dbEnglish->set('logout_flag', 1);
				//$this->dbEnglish->set('problem_function', 'updateLastLogoutStatus');
				$this->dbEnglish->where($data);
				$this->dbEnglish->update('sessionStatus');
			}
		} 
		
	}
	function logUserSession($userID,$clientBrowser,$ip)
	{

		/** 
			get the last current session status of the user
		*/
			//$status = $this->getLastLogoutStatus($userID);
		
		/** 
			Update the logout status of the active one and then create  a new one.
		*/
			

		/**
			Insert the user current session in session status
		*/
		$data = array(
			'userID'        => $userID,
			'languageType'  => 7,
			'startTime_int' => date("Ymd"),
			'browser'       => $clientBrowser,
			'ipaddress'     => $ip
		);

		$this->dbEnglish->set('startTime', 'NOW()', FALSE);
		$this->dbEnglish->insert('sessionStatus', $data); 
		return $this->dbEnglish->insert_id();
	}

	/**`
	 * function role : Update User end time
	 * @return : none
	 * 
	 * */

	function updateUserEndTime($userID,$sessionID,$childclass,$logOutBtnClick,$logOutReasonFlag)
	{
		$data = array(
				'userID' => $userID,
				'sessionID' => $sessionID
			);

		if(!$logOutReasonFlag)
			$logOutReasonFlag=1;

		$this->dbEnglish->select('endTime');
		$this->dbEnglish->from('sessionStatus');
		$this->dbEnglish->where($data);
		$result_endTime = $this->dbEnglish->get();
		$result_array_endTime = $result_endTime->row_array();

		if($userID != '' && $sessionID !='')
		{
			if($result_array_endTime['endTime'] == '')
			{
				/*GET MAX LAST MODIFIED AMONG THE 4 TABLES AND UPDATE THAT AS END TIME IN SESSION STATUS*/
				if($logOutBtnClick == 'true')
				{
					$this->dbEnglish->set('endTime', 'NOW()', FALSE);
				}
				else
				{

					$getLastModified = $this->dbEnglish->query("SELECT MAX(t1.lastModified) as maxLastModified FROM (SELECT max(lastModified) as lastModified FROM passageAttempt where userID = $userID AND sessionID = $sessionID UNION SELECT max(lastModified) as lastModified FROM questionAttempt_class".$childclass." where userID = $userID AND sessionID = $sessionID UNION SELECT max(lastModified) as lastModified FROM IGREAttemptDetails where userID = $userID AND sessionID = $sessionID UNION SELECT max(lastModified) as lastModified FROM essayAttempt where userID = $userID AND sessionID = $sessionID) as t1");
					
					if($getLastModified->num_rows() > 0)
					{
						$result_array_essay = $getLastModified->row_array();

						$endTime = 	$result_array_essay['maxLastModified'];
					}
					else
						$endTime = '';

					if($endTime != '')
					{

						$this->dbEnglish->set('endTime', "'$endTime'", FALSE);
					}
					else
					{
						/*IF NO MAX LAST MODIFIED FOUND SET END TIME AS 2 MIN AHEAD OF THE START TIME*/

						$this->dbEnglish->select('max(startTime) + INTERVAL 2 MINUTE as startTime');
						$this->dbEnglish->from('sessionStatus');
						$this->dbEnglish->where('userid', $userID);
						$result_startTime = $this->dbEnglish->get();
						$result_startTime_row = $result_startTime->row_array();
						$result_startTime_row = $result_startTime_row['startTime'];

						$this->dbEnglish->set('endTime', "'$result_startTime_row'", FALSE);
					}
				}
				
				$this->dbEnglish->set('logoutReason', $logOutReasonFlag);
				$this->dbEnglish->set('logout_flag', 1);
				$this->dbEnglish->where('userID',$userID);
				$this->dbEnglish->where('sessionID',$sessionID);
				$this->dbEnglish->update('sessionStatus');	
				
			} 
		}
	}

	function getTimeSpentAtHome($mode)
	{
		if($mode=='week')
		{

			$monday = strtotime("last monday");
			$monday = date('w', $monday)==date('w') ? $monday+7*86400 : $monday;
			 
			$sunday = strtotime(date("Y-m-d",$monday)." +6 days");
			 
			$startDate = date("Y-m-d",$monday);
			$endDate = date("Y-m-d",$sunday);
			
			$this->dbEnglish->Select('sessionID,startTime,endTime');
			$this->dbEnglish->from('sessionStatus');
			$this->dbEnglish->where('userID',$this->session->userdata('userID'));
			$this->dbEnglish->where("date(startTime)>='".$startDate."'");
			$this->dbEnglish->where("date(startTime)<='".$endDate."'");
			$this->dbEnglish->where("hour(startTime)>=15");
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
						$this->dbEnglish->where('userID',$this->session->userdata('userID'));
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
	}

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

	function temp()
	{
		$this->dbEnglish->Select('A.passageID, A.quesAttemptSrno');
		$this->dbEnglish->from('passageAttempt A, userDetails B');
		$this->dbEnglish->where('A.userID = B.userID');
		$this->dbEnglish->where('B.schoolCode = 52564');
		$this->dbEnglish->where("A.quesAttemptSrno is not null and A.quesAttemptSrno<>''");

		//$this->dbEnglish->limit('5');
		$query = $this->dbEnglish->get();
		$passageAttemptInfo = $query->result_array();

		echo "<pre>";
			print_r($passageAttemptInfo);
		echo "<pre>";


		foreach ($passageAttemptInfo as $key => $value) {
			$srnoValueArr = explode(',', $value['quesAttemptSrno']);
			$passageID = $value['passageID'];

			foreach ($srnoValueArr as $k => $val) {
				$query = "Update ".$this->questionAttemptClassTbl." set passageID=".$passageID." where srno=".$val;
				echo $query."<br>";
			}
		}


	}

	function isSyncDne($schoolCode)
	{
		$this->dbEnglish->Select('unsynchedStatus');
		$this->dbEnglish->from('adepts_offlineSchools');
		$this->dbEnglish->where('schoolCode',$schoolCode);
		$query = $this->dbEnglish->get();
		$syncInfo = $query->result_array();

		if(count($syncInfo)>=1)
			return $syncInfo[0]['unsynchedStatus'];
		else
			return "Allowed";
	}

	/**`
	 * function role : Update User time spent in classroom
	 * @return : none
	 * 
	 * */

	function updateTimeTakenInClassroom($userID,$sessionID, $totalTimeTakenClassroom)
	{
		$this->dbEnglish->set('timeTakenInClassroom', $totalTimeTakenClassroom);
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('sessionID',$sessionID);
		$this->dbEnglish->update('sessionStatus');
		
	}


	function getTimeTakenInClassroom($userID)
	{

		$this->dbEnglish->select('sum(timeTakenInClassroom) as timeTakenInClassroom');
		$this->dbEnglish->from('sessionStatus');
		$this->dbEnglish->where('userID',$userID);
		//$this->dbEnglish->where('DATE(startTime) > DATE_SUB(NOW(), INTERVAL 1 WEEK)');
		$this->dbEnglish->where('YEARWEEK(DATE(startTime), 1) = YEARWEEK(CURDATE(), 1)');
		$time_result_array = $this->dbEnglish->get()->result_array();
		if(count($time_result_array) > 0)
		{
			$totalTime                                    = $time_result_array[0]['timeTakenInClassroom'];
			$time_result_array[0]['timeTakenInClassroom'] = round($totalTime / 60); //converting from sec to min
			return $time_result_array[0]['timeTakenInClassroom'];
		}
		else
		{
			return '';
		}	
	}
}

?>