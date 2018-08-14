<?php
Class Essay_model extends MY_Model
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
	/*function saveUserTopic($userID, $title, $msLevel)
	{
		$this->dbEnglish->select('essayID, count(*) as count,( select max(srno) from essayMaster ) as srno');
		$this->dbEnglish->from('essayMaster as e');
		$this->dbEnglish->where('userID', $userID);
		$this->dbEnglish->where('LOWER(essayTitle)', strtolower($title));
		
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();
		
		if($queryResult[0]['count'] == 0)
		{
			$this->dbEnglish->select('em.essayTitle,em.essayID, ed.status');
			$this->dbEnglish->from('essayMaster em');
			$this->dbEnglish->join('ews_essayDetails ed','em.essayID = ed.topicID', 'LEFT');
			$where = "REPLACE(LOWER(em.essayTitle), ' ', '') =  REPLACE('".mysql_real_escape_string(strtolower($title))."', ' ', '')";
			$this->dbEnglish->where($where);
			//$this->dbEnglish->where('em.userID = 0');
			$this->dbEnglish->where('ed.status = 0');
			$this->dbEnglish->where('ed.userIDMsEng', $userID);
			$queryMatch = $this->dbEnglish->get();
			$queryResultMatch = $queryMatch->result_array();
			
			if(count($queryResultMatch[0]) > 0)
			{
				$dataArr = array();
				$dataArr['msg'] = 'inlist';
				$dataArr['qid'] = $queryResultMatch[0]['essayID'];
				return $dataArr;
			}
			else
			{
				//check if custom topic should be allowed on not on basis of count
				$this->dbEnglish->select('count(ed.status) as customCount');
				$this->dbEnglish->from('ews_essayDetails ed');
				$this->dbEnglish->join('essayMaster em','em.essayID = ed.topicID');
				//$this->dbEnglish->where('ed.userIDMsEng', $userID);
				$this->dbEnglish->where('em.userID', $userID);
				$this->dbEnglish->where('ed.status', 0);
				$this->dbEnglish->where('em.userID != 0');
				$queryCount = $this->dbEnglish->get();
				$queryResultCount = $queryCount->row_array();
				
				if($queryResultCount['customCount'] >= 3)
				{
					return 'incomplete';
				}
				else
				{
					// Save the custom topic added by student to ews_essayDetails
					$insert_data = array();
					$insert_data['essayID']		= $queryResult[0]['srno'] + 1;
					$insert_data['essayTitle']  = $title;
					$insert_data['wordLimit']	= 100;
					$insert_data['msLevel']		= $msLevel;
					$insert_data['userID'] 		= $userID;
					
					$this->dbEnglish->insert('essayMaster',$insert_data);
					$lastInsertedId = $this->dbEnglish->insert_id();

					$dataUpdate = array(
					   'essayID' => $lastInsertedId
		            );
					$this->dbEnglish->where('srno', $lastInsertedId);
					$this->dbEnglish->update('essayMaster', $dataUpdate);

					return $lastInsertedId;
				}
			}
		}
		else
		{
			$this->dbEnglish->Select('status');
			$this->dbEnglish->from('ews_essayDetails');
			$this->dbEnglish->where('topicID', $queryResult[0]['essayID']);
			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->order_by("essayID", "desc");
			$this->dbEnglish->limit(1);
			$queryStatus = $this->dbEnglish->get();
			$queryStatusResult = $queryStatus->row_array();
			if(!empty($queryStatusResult))
			{
				if($queryStatusResult['status'] == 0)
				{
					return 'Already';
				}
			}
			else
				return $queryResult[0]['essayID'];
		}
	}*/

	function saveUserTopic($userID, $title, $msLevel)
	{
		$isInPredefinedList = 0;
		$result = array();
		//$this->dbEnglish->select('essayID, count(*) as count,( select max(srno) from essayMaster ) as srno');
		$this->dbEnglish->select('topicID, e.essayTitle,( select max(srno) from essayMaster ) as srno');
		//$this->dbEnglish->select('e.srno, e.essayID, e.essayTitle');
		$this->dbEnglish->from('essayMaster as e');
		$this->dbEnglish->where('userID = 0');
		//$where = "REPLACE(LOWER(e.essayTitle), ' ', '') =  REPLACE('".mysql_real_escape_string(strtolower($title))."', ' ', '')";
		//$this->dbEnglish->where($where);
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();
		foreach ($queryResult as $key => $value) {
			if(preg_replace('/[^A-Za-z0-9\-]/', '', strtolower($value['essayTitle'])) == preg_replace('/[^A-Za-z0-9\-]/', '', strtolower($title)))
				{
					$title = $value['essayTitle'];
					$isInPredefinedList = 1;
					$listEssayID = $value['topicID'];
				}
		}
		//if($queryResult[0]['count'] == 0)//this topic is not there in predefined list
		if(!$isInPredefinedList)    //this topic is not there in predefined list
		{
			$isInUserList = 0;
			//$this->dbEnglish->select('e.srno, e.essayID, e.essayTitle');
			$this->dbEnglish->select('topicID, e.essayTitle,( select max(srno) from essayMaster ) as srno');
			//$this->dbEnglish->select('essayID, count(*) as count,( select max(srno) from essayMaster ) as srno');
			$this->dbEnglish->from('essayMaster as e');
			$this->dbEnglish->where('userID', $userID);
			//$this->dbEnglish->where('LOWER(essayTitle)', strtolower($title));
			//$where = "REPLACE(LOWER(e.essayTitle), ' ', '') =  REPLACE('".mysql_real_escape_string(strtolower($title))."', ' ', '')";
			//$this->dbEnglish->where($where);
			$query                                = $this->dbEnglish->get();
			$queryResultPredifined                = $query->result_array();

			foreach ($queryResultPredifined as $key => $value) 
			{
				if(preg_replace('/[^A-Za-z0-9\-]/', '', strtolower($value['essayTitle'])) == preg_replace('/[^A-Za-z0-9\-]/', '', strtolower($title)))
				{
					$title = $value['essayTitle'];
					$isInUserList = 1;
					$userEssayID = $value['topicID'];
				}
			}
			//if($queryResultPredifined[0]['count'] == 0) //this topic is not created by this user 
			if(!$isInUserList) //this topic is not created by this user 
			{
				//allow to insert the new topic for that user

				//check if custom topic should be allowed on not on basis of count
				$this->dbEnglish->select('count(ed.status) as customCount');
				$this->dbEnglish->from('essayDetails ed');
				$this->dbEnglish->join('essayMaster em','em.topicID = ed.topicID');
				$this->dbEnglish->where('em.userID', $userID);
				$this->dbEnglish->where('ed.status', 0);
				$this->dbEnglish->where('em.userID != 0');
				$queryCount = $this->dbEnglish->get();
				$queryResultCount = $queryCount->row_array();
				
				if($queryResultCount['customCount'] >= 3)
				{
					return 'incomplete';
				}
				else
				{
					// Save the custom topic added by student to essayDetails
					$insert_data = array();
					$insert_data['topicID']		= $queryResultPredifined[0]['srno'] + 1;
					$insert_data['essayTitle']  = $title;
					$insert_data['wordLimit']	= 100;
					$insert_data['msLevel']		= $msLevel;
					$insert_data['userID'] 		= $userID;
					
					$this->dbEnglish->insert('essayMaster',$insert_data);
					$lastInsertedId = $this->dbEnglish->insert_id();
					$abbrivation=$this->getAbbreviationOfSchoolFromUserID($userID);
					$dataUpdate = array(
					   'topicID' => $abbrivation.$lastInsertedId
		            );
					$this->dbEnglish->where('srno', $lastInsertedId);
					$this->dbEnglish->update('essayMaster', $dataUpdate);

					$essayIDWithAbbre=$abbrivation.$lastInsertedId;
					return $essayIDWithAbbre;
				}
			}
			else
			{
				//created this topic and check for status
				$this->dbEnglish->Select('status, topicID, userResponse');
				$this->dbEnglish->from('essayDetails');
				$this->dbEnglish->where('topicID', $userEssayID);
				//$this->dbEnglish->where('topicID', $queryResultPredifined[0]['essayID']);
				//$this->dbEnglish->where('userIDMsEng', $userID);
				$this->dbEnglish->where('userID', $userID);
				$this->dbEnglish->order_by("essayID", "desc");
				$this->dbEnglish->limit(1);
				$queryStatus = $this->dbEnglish->get();
				$queryStatusResult = $queryStatus->row_array();
				if(empty($queryStatusResult))
				{
					$dataArr                 = array();
					$dataArr['msg']          = 'inlist';
					$dataArr['qid']          = $userEssayID;		
					$dataArr['userResponse'] = '';
					$dataArr['essayTitle']   = $title;                       // Added by Aditya
					return $dataArr;
				}
				else
				{
					if($queryStatusResult['status'] == 0)
					{
						$dataArr                 = array();
						$dataArr['msg']          = 'Already';
						$dataArr['qid']          = $userEssayID;
						//$dataArr['qid']          = $queryResultPredifined[0]['essayID'];
						$dataArr['userResponse'] = $queryStatusResult['userResponse'];
						$dataArr['essayTitle']   = $title; 					// Added by Aditya
						return $dataArr;
						//return $queryResultPredifined[0]['essayID'];
					}
					else
					{
						/*$dataArr                 = array();
						$dataArr['msg']          = '';
						$dataArr['qid']          = $queryResultPredifined[0]['essayID'];
						$dataArr['userResponse'] = '';
						return $dataArr;*/
						return $userEssayID;             // change Here Aditya
						//return $queryResultPredifined[0]['essayID'];
					}	
					//return 'Already';
				}
			}
		}
		else
		{
			// this topic is there in the predefined list so return return the id and check for the attempt status as well
			$this->dbEnglish->Select('status,topicID,userResponse');
			$this->dbEnglish->from('essayDetails');
			$this->dbEnglish->where('topicID', $listEssayID);
			//$this->dbEnglish->where('topicID', $queryResult[0]['essayID']);
			//$this->dbEnglish->where('userIDMsEng', $userID);
			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->order_by("essayID", "desc");
			$this->dbEnglish->limit(1);
			$queryStatus = $this->dbEnglish->get();
			$queryStatusResult = $queryStatus->row_array();
			if(empty($queryStatusResult))
			{
				//inlist with id return
				$dataArr                 = array();
				$dataArr['msg']          = 'inlist';
				$dataArr['qid']          = $listEssayID;
				//$dataArr['qid']          = $queryResult[0]['essayID'];
				$dataArr['userResponse'] = '';
				$dataArr['essayTitle']   = $title; 
				return $dataArr;
				//return $queryResult[0]['essayID'];	
			}
			else{
				if($queryStatusResult['status'] == 0)
				{
					$dataArr                 = array();
					$dataArr['msg']          = 'Already';
					$dataArr['qid']          = $queryStatusResult['topicID'];
					$dataArr['userResponse'] = $queryStatusResult['userResponse'];
					$dataArr['essayTitle']   = $title; 
					return $dataArr;
					//return $queryStatusResult[0]['topicID'];
				}
				else
					return $queryStatusResult['topicID'];	
			}
		}
	}
	function saveEssayDetails($userID,$topicID,$topic,$userResponse,$status,$sessionID,$timeTaken)
	{

		//$writerID = -1 * $userID;
		//$topicID = $essayID;

		$this->dbEnglish->Select('essayID,topicID,status');
		$this->dbEnglish->from('essayDetails');
		$this->dbEnglish->where('topicID', $topicID);
		$this->dbEnglish->where('userID', $userID);
		$this->dbEnglish->order_by("essayID", "desc");
		$this->dbEnglish->limit(1);
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();


		//if(count($queryResult)>=1)
		if($queryResult[0]['status'] == 0 && count($queryResult) >= 1)
		{
			$this->dbEnglish->Select('essayID,userID,topicID');
			$this->dbEnglish->from('essayDetails');
			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->where('topicID', $topicID);
			$this->dbEnglish->order_by("essayID", "desc");
			$this->dbEnglish->limit(1);
			$queryAttempt = $this->dbEnglish->get();
			$queryResultAttempt = $queryAttempt->row_array();
			$data = array(
			   //'userResponse' => $userResponse,
			   'sessionID' => $sessionID//,
			   //'ews_essayDetailsID' => $queryResult[0]['essayID']
            );

			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->where('topicID', $topicID);
			$this->dbEnglish->where('essayID', $queryResultAttempt['essayID']);
			$this->dbEnglish->update('essayDetails', $data);
			return $this->updateEssayWrittingSystem('Save',$userID,$topicID,$userResponse,$status,$timeTaken);
		}
		else
		{

			
			//needs to be removed since essayAttempt table has been removed and using only essayDetails table
			$this->updateEssayWrittingSystem('Start',$userID,$topicID,$userResponse,$status,$timeTaken,$sessionID);

			$this->dbEnglish->Select('essayID');
			$this->dbEnglish->from('essayDetails');
			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->where('topicID', $topicID);
			$this->dbEnglish->order_by('lastModified','desc');
			$this->dbEnglish->limit(1);
			$queryDetails = $this->dbEnglish->get();
			$queryDetailsResult = $queryDetails->row_array();

			//$data = array(
				//'userID' => $userID,
				//'topicID' => $essayID,
				//'sessionID' => $sessionID,
				//'userResponse' => $userResponse,
				//'ews_essayDetailsID' => $queryDetailsResult['essayID']

			//);
			
			//$query = $this->dbEnglish->insert('essayAttempt', $data);

			
			$dataArr = array(
			   //'userResponse' => $userResponse,
			   'sessionID' => $sessionID
            );

			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->where('topicID', $topicID);
			$this->dbEnglish->where('essayID', $queryDetailsResult['essayID']);
			//$this->dbEnglish->where('ews_essayDetailsID', $queryDetailsResult['essayID']);
			$this->dbEnglish->update('essayDetails', $dataArr);



			return $this->updateEssayWrittingSystem('Save',$userID,$topicID,$userResponse,$status,$timeTaken);
			//needs to be removed since essayAttempt table has been removed and using only essayDetails table
		}
	}

	function getEssayTopics($userID,$msLevel)
	{
		$attemptedEssayIDs = $this->getUserAttemptedEssays($userID);
		$attemptedEssayIDsArr = explode(',',$this->getUserAttemptedEssays($userID));

                $queryResult=$this->showAssignedEssayToStudent($userID);
                //$queryResult[0]['forceEssay']=0;[0]['forceEssay']
                if($queryResult[0]['forceEssay']==0){
                    $this->dbEnglish->Select('topicID as essayID,topicID,essayTitle,msLevel');
                    $this->dbEnglish->from('essayMaster');
                    $where = "FIND_IN_SET('".$msLevel."', msLevel)";
                    $this->dbEnglish->where($where);
                    $this->dbEnglish->where('userID', 0);
                    //$this->dbEnglish->where_not_in('essayID',$attemptedEssayIDsArr);
                    $query = $this->dbEnglish->get();

                    $queryResult = $query->result_array();

                    foreach ($queryResult as $key => $value) 
                    {
                            $this->dbEnglish->Select('essayID');
                            $this->dbEnglish->from('essayDetails');
                            $this->dbEnglish->where('topicID', $value['topicID']);
                            $this->dbEnglish->where('userID', $userID);
                            $this->dbEnglish->where('status', 0);
                            $this->dbEnglish->limit(1);
                            $queryEssDetails = $this->dbEnglish->get();
                            $queryResultDetails = $queryEssDetails->result_array();
                            $queryResult[$key]['forceEssay'] = "no";
                            if(count($queryResultDetails) > 0)
                                    $queryResult[$key]['ews_essayDetailsID'] = $queryResultDetails[0]['essayID'];
                            else
                                    $queryResult[$key]['ews_essayDetailsID'] = 0;
                    }
                }
		return $queryResult;
	}

	function getUserAttemptedEssays($userID)
	{
		$this->dbEnglish->Select('group_concat(topicID) as topicids');
		$this->dbEnglish->from('essayDetails');
		$this->dbEnglish->where('userID', $userID);
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();
		return $queryResult[0]['topicids'];
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

	function updateEssayWrittingSystem($type,$userID,$essayID,$userResponse,$status,$timeTaken,$sessionID="")
	{
		
		$userID = 1 * $userID;
		$serverURL = $_SERVER['HTTP_HOST'];

		

		$server = $this->config->item('offline_url');
		//$server  = 'http://localhost';
		
		$essayWritterURL = 'http://13.251.143.110/mindsparkProduct/essaywriting/mseng_essays.php';

		$essayTitle = $this->getEssayName($essayID);

		if($type=='Save')
		{

			$essaySystemEssayID = $this->getEssaySystemEssayID($userID,$essayID,0);			
			 // echo "<pre>";print_r($essaySystemEssayID);echo "</pre>";
			 // exit();
			$postArr = array('userID' => $userID , 'submitEssay' => 1, 'essayID'=>$essaySystemEssayID , 'eTitle' => $essayTitle,'topicID' => $essayID , 'status' => $status , 'essayBody' => $userResponse,'essayTime' => $timeTaken);
			//echo "<pre>";print_r($postArr);echo "</pre>";

		}
		else if($type=='Start')
		{
			$postArr = array('userID' => $userID , 'submitEssay' => 1, 'essayID'=>0, 'eTitle' => $essayTitle,'topicID' => $essayID,'essayTime' => $timeTaken, 'essayBody' => $userResponse,'sessionID' => $sessionID);
		}


		return json_decode($this->do_post($essayWritterURL,$postArr));
	}
	/**
	 * function role : Get essay name from essay id
	 * param1 : essayID
	 * @return   string, essay name
	 *
	 * */
	function canSubmitEssay($userID)
	{
		$conditionArr = array(
			'userID'  => $userID,
			'status' 	=> 1
		);
		$this->dbEnglish->select('date(lastModified) DATE_LOG');
		$this->dbEnglish->from('essayDetails');
		$this->dbEnglish->where($conditionArr);
		$this->dbEnglish->order_by('essayID','desc');
		$this->dbEnglish->limit(1);
		$result_object = $this->dbEnglish->get();
		$result_array = $result_object->result_array();

		if(count($result_array) > 0)
		{
			if($result_array[0]['DATE_LOG'] < Date('Y-m-d', strtotime("-15 days")))
			{
				return "true";	
			}
			else 
			{
				$diff=date_diff(date_create(Date('Y-m-d', strtotime("-15 days"))),date_create($result_array[0]['DATE_LOG']));
				if($diff->format("%a") == 0)
					return "true";
				else
					return $diff->format("%a");
					
			}
		}
		else
		{
			return "true";
		}

	}

	function getEssayName($essayID,$type="",$ews_essayDetailsID="")
	{

		$teacherIDsArr= $this->getTeacherListArr($this->school_code);
		array_push($teacherIDsArr,'0',$this->user_id);
		//$this->output->enable_profiler(TRUE);
		$this->dbEnglish->_protect_identifiers = FALSE;
		$essayIDs = explode('||',$essayID);

		if($type=='list')
		{
			$arr = explode('||',$essayID);
			$ids = sprintf('FIELD(em.topicID, "%s")', implode('","',$arr));

			$this->dbEnglish->Select("group_concat(em.essayTitle order by($ids) SEPARATOR '||') essayTitle");
		}
		else
			$this->dbEnglish->Select('em.essayTitle');
		$this->dbEnglish->from('essayMaster em');

		if($type == 'list')
			$this->dbEnglish->join('essayDetails ed','ed.topicID = em.topicID');

		if($type=='list')
		{
			$ews_essayDetailsIDs = explode('||',$ews_essayDetailsID);

			$this->dbEnglish->where_in('em.topicID',$essayIDs);
			$this->dbEnglish->where_in('em.userID',$teacherIDsArr);
			$this->dbEnglish->where_in('ed.essayID',$ews_essayDetailsIDs);
		}
		else
			$this->dbEnglish->where('em.topicID', $essayID );
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();
		//echo $this->dbEnglish->last_query();
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
		$this->dbEnglish->Select('topicID,userResponse,extraParams');
		$this->dbEnglish->from('essayDetails');
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

	function getEssaySystemEssayID($userID,$essayID,$status)
	{

		$userID=1*$userID;
		$this->dbEnglish->Select('essayID');
		$this->dbEnglish->from('essayDetails');
		$this->dbEnglish->where('userID', $userID);
		$this->dbEnglish->where('topicID', $essayID);
		$this->dbEnglish->where('status', $status);

		//$this->dbEnglish->order_by('lastModified','asc');
		//$this->dbEnglish->order_by('lastModified','desc');
		$this->dbEnglish->order_by('essayID','desc');
		$this->dbEnglish->limit(1);
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();
		// echo "<pre>";print_r($this->dbEnglish->last_query());echo "</pre>";
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

	function fetchEssayFeedback($userID,$essayID,$fetchFeedback,$ewsDetailsID="")
	{
		//echo "string3454";
		$userID = $userID;
		$serverURL = $_SERVER['HTTP_HOST'];

			
		$server = $this->config->item('offline_url');
		


		$essayWritterURL = 'http://13.251.143.110/mindsparkProduct/essaywriting/mseng_essays.php';
		if($ewsDetailsID == '')
			$essaySystemEssayID = $this->getEssaySystemEssayID($userID,$essayID,2);
		else
			$essaySystemEssayID = $ewsDetailsID;
		
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
	  return $response;
	}

	function fetchEssaySummary($userID)
	{
		//$this->output->enable_profiler(TRUE);
		$teacherIDsArr= $this->getTeacherListArr($this->school_code);
		array_push($teacherIDsArr,'0',$this->user_id);
		$essaySummary = array('inCompleteEssays'=>null,'completedEssays'=>null,'gradedEssays'=>null,'inCompleteEssaysName'=>null,'completedEssaysName'=>null,'gradedEssaysName'=>null,'inCompleteEssayID'=>null,'completedEssaysID'=>null,'gradedEssaysID'=>null);
		$userID = $userID;
		$this->dbEnglish->Select("status,group_concat(ed.topicID SEPARATOR '||') topics,group_concat(ed.essayID SEPARATOR '||') essays");
		$this->dbEnglish->from('essayDetails ed');
		$this->dbEnglish->join('essayMaster em','ed.topicID = em.topicID');
		$this->dbEnglish->where('ed.userID',$userID);
		$this->dbEnglish->where_in('em.userID',$teacherIDsArr);

		$this->dbEnglish->group_by('status');
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();
		$isForce=array();
		if(count($queryResult)>0)
		{
			foreach ($queryResult as $key => $valueArr) {
				if($valueArr['status']==0)
				{
                                        $clearTopicID=explode('||',$valueArr['topics']);
                                        foreach($clearTopicID as $checkForce){
                                            $isForce[]=$this->checkIsForceEssay('',$checkForce);
                                        }
                                        $isForce=implode('||',$isForce);                                        
                                        $essaySummary['inCompleteEssaysisForce'] = $isForce;
					$essaySummary['inCompleteEssays'] = $valueArr['topics'];
					$essaySummary['inCompleteEssayID'] = $valueArr['essays'];
					$essaySummary['inCompleteEssaysName'] = $this->getEssayName($valueArr['topics'],'list',$valueArr['essays']);
				}
				elseif ($valueArr['status']==1)
				{
					$essaySummary['completedEssays'] = $valueArr['topics'];
					$essaySummary['completedEssaysID'] = $valueArr['essays'];
					$essaySummary['completedEssaysName'] = $this->getEssayName($valueArr['topics'],'list',$valueArr['essays']);
				}
				elseif ($valueArr['status']==2)
				{
					$essaySummary['gradedEssays'] = $valueArr['topics'];
					$essaySummary['gradedEssaysID'] = $valueArr['essays'];
					//echo "<pre>";print_r($valueArr['topics']);echo "</pre>";
					$essaySummary['gradedEssaysName'] = $this->getEssayName($valueArr['topics'],'list',$valueArr['essays']);
				}
			}
		}
		//echo "<pre>";print_r($essaySummary);echo "</pre>";
               
		return $essaySummary;
	}

	function fetchEssayResponse($userID,$topicID,$essayID)
	{

		$data = array('userResponse' => '',	'timeTaken' => 0);


		$this->dbEnglish->Select('userResponse');
		$this->dbEnglish->from('essayDetails');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('topicID',$topicID);
		$this->dbEnglish->where('essayID',$essayID);
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();
//		echo "<pre>";print_r($this->dbEnglish->last_query());echo "</pre>";

		if(count($queryResult)>0)
		{
			$data["userResponse"]=$queryResult[0]['userResponse'];
		};



		//else
			//return "";

		$this->dbEnglish->Select('timeTaken');
		$this->dbEnglish->from('essayDetails');
		$this->dbEnglish->where('essayID',$essayID);
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();
		if(count($queryResult)>0)
		{
			$data["timeTaken"]=$queryResult[0]['timeTaken'];

		};

                $isForce=$this->checkIsForceEssay($userID,$topicID);
                
                if(isset($isForce) && $isForce!="no"){
                    $data["isForce"]=$isForce;
                }else{
                    $data["isForce"]="no";
                }
		return $data;
		//return $data;
		//echo count($queryResult)."<br>';

	}
        /**
	 * function description : This function will return whether topic id is created by teacher and active.
	 * param1   userID
         * param2   topicID
	 * @return  return essay id if it is force essay or return no.
	 * 
	 * */
        function checkIsForceEssay($userID,$topicID){
            
            $this->dbEnglish->Select('topicID');
            $this->dbEnglish->from('teacherEssayActivation');
            $this->dbEnglish->where('topicID',$topicID);           
            $query = $this->dbEnglish->get();
            $queryResult = $query->result_array();
            
            if(isset($queryResult[0]) && !empty($queryResult)){
                $queryResult=$queryResult[0]['topicID'];
            }else{
                $queryResult="no";
            }
            return $queryResult;
        }
        
        /**
	 * function description : This function will show essay which assigned by teacher to student.
	 * param1   userid
	 * @return  return essayID,  srno, essay title and ms level in array
	 * 
	 * */
        function showAssignedEssayToStudent($userID){
            $this->dbEnglish->Select('teacherEssayActivation.topicID,teacherEssayActivation.srno, essayMaster.essayTitle,essayMaster.msLevel');
		$this->dbEnglish->from('teacherEssayActivation');
                $this->dbEnglish->join('essayMaster','teacherEssayActivation.topicID = essayMaster.topicID','INNER');
		$this->dbEnglish->where('teacherEssayActivation.class',$this->child_class);
                $this->dbEnglish->where('teacherEssayActivation.section',$this->child_section);
                $this->dbEnglish->where('teacherEssayActivation.schoolCode',$this->school_code);
		$this->dbEnglish->where('teacherEssayActivation.isActive', 1);
                $this->dbEnglish->where('teacherEssayActivation.activationDate<=CURDATE()');
                $this->dbEnglish->where('teacherEssayActivation.deactivationDate>=CURDATE()');
                $this->dbEnglish->order_by('teacherEssayActivation.deactivationDate ASC');
                $this->dbEnglish->limit(1);
		$query = $this->dbEnglish->get();
		$queryResult = $query->result_array();
                if(isset($queryResult[0]) && !empty($queryResult)){
                    $this->dbEnglish->Select('essayDetails.essayID,essayDetails.status');
                    $this->dbEnglish->from('essayDetails');
                    $this->dbEnglish->where('essayDetails.userID',$userID);
                    $this->dbEnglish->where('essayDetails.topicID',$queryResult[0]['topicID']);             
                    $essayDetailQuery = $this->dbEnglish->get();
                    $essayDetailQueryResult = $essayDetailQuery->result_array();
                    if(isset($essayDetailQueryResult[0]) && !empty($essayDetailQueryResult)){
                        $queryResult[0]['ews_essayDetailsID'] = $essayDetailQueryResult[0]['essayID'];
                    }else{
                        $queryResult[0]['ews_essayDetailsID'] = 0;
                    }
                    if(!isset($essayDetailQueryResult[0])){
                        $queryResult[0]['forceEssay'] = '1';
                    }
                    if($essayDetailQueryResult[0]['status']==0){
                        $queryResult[0]['forceEssay'] = '1';
                    }elseif($essayDetailQueryResult[0]['status']==2){
                        $queryResult[0]['forceEssay'] = '0';
                    }elseif($essayDetailQueryResult[0]['status']==1){
                        $queryResult[0]['forceEssay'] = '0';
                    }else{
                        $queryResult[0]['forceEssay'] = '1';
                    } 
                }
                return $queryResult;
        }
		
		function getAbbreviationOfSchoolFromUserID($userID) {			
			$this->dbEnglish->Select('abbreviation', false);
            $this->dbEnglish->from("adepts_offlineSchools os");
            $this->dbEnglish->join('userDetails ud','os.schoolCode = ud.schoolCode','INNER');
            $this->dbEnglish->where('ud.userID',$userID);
            $query = $this->dbEnglish->get();
            $userDataArr = $query->result_array();
            if (isset($userDataArr[0]) && $userDataArr[0]['abbreviation'] != "") {
                return $userDataArr[0]['abbreviation'];
            } else {
                return 'ON';
            }
		}
}

?>