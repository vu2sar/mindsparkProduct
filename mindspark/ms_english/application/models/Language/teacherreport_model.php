<?php

Class teacherreport_model extends MY_Model
{	
	
	public $class;
	public $section;
	public $studentsArr;
	public $studentsArrIds;
	public $usageArr;

	public $teacherReportData;
	public $studentUserIDArr;
	public $contentSummaryArr;
	public $outlierArr;
	public $schoolCode;
	public $startDate;
	public $endDate;
	public $lastLoggedInUserInfo;
	public $lastLoggedInStartDate;

	public $sentence_group        = '14,15,16,12,13,10';
	public $object_refrence_group = '6,11,9,7';
	public $verbs_group           = '2,8';
	public $describing_group      = '3,5';
	public $nouns_group           = '1,4';
	public $punctuation_group     = '17,21,18';
	public $word_group            = '19,20,22,23';


	public function __construct() 
	{
		 parent::__construct();
		 //$this->output->enable_profiler(TRUE);
		 $this->dbEnglish = $this->load->database('mindspark_english',TRUE);
		 //$this->dbESL = $this->load->database('mindspark_ESL',TRUE);
		 $this->load->model('Language/user_model');
		 $this->load->model('Language/teachermystudents_model');
		 $this->Companies_db = $this->dbEnglish;

	       // Pass reference of database to the CI-instance
	     $CI =& get_instance();
	     $CI->Companies_db =& $this->Companies_db; 

	     $this->teacherReportData     = array();
	     $this->studentUserIDArr      = array();
	     $this->contentSummaryArr     = array();
	     $this->outlierArr            = array();
	  	   
	    $this->lastLoggedInUserInfo  = array();
	    $this->lastLoggedInStartDate = "";

	    $this->schoolCode = "";
	 	$this->startDate = ""; 
	 	$this->endDate = "";
	 	$this->class = "";
	 	$this->section = "";
	 	$this->studentsArr    = array();
	    $this->studentsArrIds = array();
	    $this->usageArr      = array();

	 }
	 function getOverallUsageSummary($schoolCode, $class, $section, $startDate, $endDate) {

 		$this->schoolCode = $schoolCode;
	 	$this->startDate = $startDate; 
	 	$this->endDate = $endDate;
	 	$this->class = $class;
	 	$this->section = $section;
	 	$overallUsageSummary;

		$this->studentsArr = $this->getStudentDetailsBySection($this->schoolCode,$this->class,$this->section,"assoc");
		//return ($this->studentsArr);
		if(count($this->studentsArr)==0){
			$overallUsageSummary[]['noActiveUsersFound']=1; 
			return ($overallUsageSummary);
		}
		$this->studentsArrIds = $this->array_column($this->studentsArr, 'userID');
		$this->usageArr = $this->updateDefaultValAndGetTimeSpent();

		$overallUsageSummary = $this->getAccuracyAttemptDetails($this->usageArr);

		$overallUsageSummary= $this->getUserReadListenPsgDetailsAndAcc($overallUsageSummary);

		$overallUsageSummary= $this->getGrammarVocabDetailsAndAccuracy($overallUsageSummary);
		$overallUsageSummary= $this->getEssayAndSparkieDetails($overallUsageSummary);
		return $overallUsageSummary;
		
	}

	function updateDefaultValAndGetTimeSpent() {
		$usageArr = array();
		$userArray=$this->studentsArr;
		$startDate=$this->startDate; 
	 	$endDate=$this->endDate;
		foreach ($userArray as $userDetails) {

			$timeSpent = $this->getTimeSpentOfUser($userDetails['userID'],$startDate,$endDate,$this->class);
			$timeSpentHMS = $this->convertTimeInHMS($timeSpent,"hm");
			$userDetails['timeSpent'] = $timeSpentHMS;
			$userDetails['totalQuesAttempted'] = 0;
			$userDetails['totalPassageAttempted'] = 0;
			$userDetails['totalCorrect'] = 0;
			$userDetails['totalDaysSessions'] = 0;
			$userDetails['avgTimePerQues'] = 0;
			$userDetails['accuracy'] = 0;
			$userDetails['sparkies'] = 0;
			$userDetails['listenTotalPsgRead'] = 0;
			$userDetails['listenTotalQues'] = 0;
			$userDetails['listenQuesCorrect'] = 0;
			$userDetails['listenQuesAcc'] = 0;
			$userDetails['readTotalPsgRead'] = 0;
			$userDetails['readTotalQues'] = 0;
			$userDetails['readQuesCorrect'] = 0;
			$userDetails['readQuesAcc'] = 0;
			$userDetails['grammarTotalQues'] = 0;
			$userDetails['grammarQuesAcc'] = 0;
			$userDetails['vocabTotalQues'] = 0;
			$userDetails['vocabQuesAcc'] = 0;
			$userDetails['totalEssayAttempt'] = 0;
			$usageArr[$userDetails['userID']] = $userDetails;
		}
		return $usageArr;
		
	}

	function getAccuracyAttemptDetails($usageArr) {

		$startDate=$this->startDate ; 
	 	$endDate=$this->endDate;
	 	$class=$this->class;
	 	$studentsArr=$this->studentsArrIds;
	 	$tbl_quesAttempt=$this->questionAttemptTblName.$class;

	 	$this->dbEnglish->Select('s.userID, count(q.srno) totalQuesAttempted, sum(timeTaken) avgTime, sum(if(correct=1,1,0)) as correct, count(distinct startTime_int) as days, count(distinct s.sessionID) as sessions', false);
	 	$this->dbEnglish->from("sessionStatus s");
	 	$this->dbEnglish->join($tbl_quesAttempt.' q','s.userID=q.userID AND s.sessionID=q.sessionID','LEFT');	 	
	 	$this->dbEnglish->where_in('s.userID',$studentsArr);
	 	$this->dbEnglish->where('startTime_int >=',$this->getIntDate($startDate));
	 	$this->dbEnglish->where('startTime_int <=',$this->getIntDate($endDate));	 
	 	$this->dbEnglish->group_by("s.userID");

	 	$query = $this->dbEnglish->get();
		$userDataArr = $query->result_array();

		foreach($userDataArr as $key=>$valueArr)
		{		
			$totalQuesAttempted = intval($valueArr['totalQuesAttempted']);
			if($valueArr['correct']==NULL || $valueArr['correct']==''){
				$valueArr['correct']=0;
			}			
			$totalCorrect =intval ($valueArr['correct']);
			//$totalDaysSessions= $valueArr['days']." (".$valueArr['sessions'].")";
			$totalDaysSessions= intval($valueArr['days']);
			if($valueArr['avgTime']==NULL || $valueArr['avgTime']==''){
				$avgTimePerQues=0;
			}else{
				$avgTimePerQues = round($valueArr['avgTime']/$valueArr['totalQuesAttempted'],1);

			}
			$accuracy  = ($valueArr['totalQuesAttempted']> 0)? round($valueArr['correct']*100/$valueArr['totalQuesAttempted'], 1) : 0;
		
			$usageArr[$valueArr['userID']]['totalQuesAttempted'] =$totalQuesAttempted ;
			$usageArr[$valueArr['userID']]['totalCorrect'] =$totalCorrect;
			$usageArr[$valueArr['userID']]['totalDaysSessions'] =$totalDaysSessions ;
			$usageArr[$valueArr['userID']]['avgTimePerQues'] =$avgTimePerQues ;
			$usageArr[$valueArr['userID']]['accuracy'] =$accuracy;	
			
		}
		return($usageArr);
		
	}

	function getUserReadListenPsgDetailsAndAcc($usageArr) {
		$startDate=$this->startDate ; 
	 	$endDate=$this->endDate;
	 	$class=$this->class;
	 	$studentsArr=$this->studentsArrIds;
	 	$tbl_quesAttempt=$this->questionAttemptTblName.$class;	

		$this->dbEnglish->Select('qa.userID,count(srno) AS totalQuesAttempted,count(DISTINCT qa.passageID) as passageAttemptCount ,sum(if(correct=1,1,0)) as correct,passageType', false);
	 	$this->dbEnglish->from($tbl_quesAttempt." qa");
	 	$this->dbEnglish->join('passageMaster pm','qa.passageID = pm.passageID','INNER');
	 	$this->dbEnglish->where('pm.passageType !=','conversation');
	  	$this->dbEnglish->where_in('qa.userID',$studentsArr);
	 	$this->dbEnglish->where('DATE(qa.attemptedDate) >=',$this->getIntDate($startDate));
	 	$this->dbEnglish->where('DATE(qa.attemptedDate) <=',$this->getIntDate($endDate));
	 	$this->dbEnglish->group_by("qa.userID");

		$query = $this->dbEnglish->get();
		$userDataArr = $query->result_array();

		$totalReadPsg=0;
		$totalReadQues=0;
		$totalReadCorrect=0;
		$accuracyRead=0;		
		foreach($userDataArr as $key=>$valueArr)
		{		
			
			$totalReadQues = $valueArr['totalQuesAttempted'];
			$totalReadPsg= $valueArr['passageAttemptCount'];
			if($valueArr['correct']==NULL || $valueArr['correct']==''){
				$valueArr['correct']=0;
			}
			$totalReadCorrect=$valueArr['correct'];			
			$accuracyRead = ($totalReadQues > 0)? round($totalReadCorrect*100/$totalReadQues, 1) : 0;

			$usageArr[$valueArr['userID']]['readTotalPsgRead'] =intval($totalReadPsg);
			$usageArr[$valueArr['userID']]['readTotalQues'] =intval($totalReadQues);
			$usageArr[$valueArr['userID']]['readQuesAcc'] =$accuracyRead;
			$usageArr[$valueArr['userID']]['readQuesCorrect'] =intval($totalReadCorrect);
			$usageArr[$valueArr['userID']]['totalPassageAttempted'] +=$totalReadPsg;
		
				
		}


		$this->dbEnglish->Select('qa.userID,count(srno) AS totalQuesAttempted,count(DISTINCT qa.passageID) as passageAttemptCount ,sum(if(correct=1,1,0)) as correct,passageType', false);
	 	$this->dbEnglish->from($tbl_quesAttempt." qa");
	 	$this->dbEnglish->join('passageMaster pm','qa.passageID = pm.passageID','INNER');
	 	$this->dbEnglish->where('pm.passageType =','conversation');
	  	$this->dbEnglish->where_in('qa.userID',$studentsArr);
	 	$this->dbEnglish->where('DATE(qa.attemptedDate) >=',$this->getIntDate($startDate));
	 	$this->dbEnglish->where('DATE(qa.attemptedDate) <=',$this->getIntDate($endDate));
	 	$this->dbEnglish->group_by("qa.userID");

		$query = $this->dbEnglish->get();
		$userDataArr = $query->result_array();

			
		$totalListenPsg=0;
		$totalListenQues=0;
		$totalListenCorrect=0;
		$accuracyListen=0;	

		foreach($userDataArr as $key=>$valueArr)
		{		
			
			$totalListenQues = $valueArr['totalQuesAttempted'];
			$totalListenPsg= $valueArr['passageAttemptCount'];
			if($valueArr['correct']==NULL || $valueArr['correct']==''){
				$valueArr['correct']=0;
			}
			$totalListenCorrect=$valueArr['correct'];			
			$accuracyListen = ($totalListenQues > 0)? round($totalListenCorrect*100/$totalListenQues, 1) : 0;

			$usageArr[$valueArr['userID']]['listenTotalPsgRead'] = intval($totalListenPsg);
			$usageArr[$valueArr['userID']]['listenTotalQues'] =intval($totalListenQues);
			$usageArr[$valueArr['userID']]['listenQuesAcc'] =$accuracyListen;
			$usageArr[$valueArr['userID']]['listenQuesCorrect'] =intval($totalListenCorrect);
			$usageArr[$valueArr['userID']]['totalPassageAttempted'] +=$totalListenPsg;
		
				
		}
		return $usageArr;
	}

	function getGrammarVocabDetailsAndAccuracy($usageArr) {
		
		$startDate=$this->startDate ; 
	 	$endDate=$this->endDate;
	 	$class=$this->class;
	 	$studentsArr=$this->studentsArrIds;
		$tbl_quesAttempt=$this->questionAttemptTblName.$class;		

		$this->dbEnglish->Select('qa.userID,count(srno) AS totalQuesAttempted, sum(if(correct=1,1,0)) as correct', false);
	 	$this->dbEnglish->from($tbl_quesAttempt." qa");
	 	$this->dbEnglish->join('questions qs','qa.qcode = qs.qcode','INNER');
	 	$this->dbEnglish->where('qs.passageID',0);
	 	$this->dbEnglish->where('qs.topicID',1);
	 	$this->dbEnglish->where_in('qa.userID',$studentsArr);
	 	$this->dbEnglish->where('DATE(qa.attemptedDate) >=',$this->getIntDate($startDate));
	 	$this->dbEnglish->where('DATE(qa.attemptedDate) <=',$this->getIntDate($endDate));
		$this->dbEnglish->group_by("qa.userID");

		$query = $this->dbEnglish->get();
		$userDataArr = $query->result_array();
		
		$grammarTotalQues=0;
		$grammarTotalCorrect=0;
		$grammarQuesAcc=0;
		foreach($userDataArr as $key=>$valueArr)
		{		
			
			$grammarTotalQues = $valueArr['totalQuesAttempted'];
			if($valueArr['correct']==NULL || $valueArr['correct']==''){
				$valueArr['correct']=0;
			}
			$grammarTotalCorrect=$valueArr['correct'];			
			$grammarQuesAcc = ($grammarTotalQues > 0)? round($grammarTotalCorrect*100/$grammarTotalQues, 1) : 0;

			$usageArr[$valueArr['userID']]['grammarTotalQues'] =intval($grammarTotalQues);
			$usageArr[$valueArr['userID']]['grammarQuesAcc'] =$grammarQuesAcc;		
				
		}


		$this->dbEnglish->Select('qa.userID,count(srno) AS totalQuesAttempted, sum(if(correct=1,1,0)) as correct', false);
	 	$this->dbEnglish->from($tbl_quesAttempt." qa");
	 	$this->dbEnglish->join('questions qs','qa.qcode = qs.qcode','INNER');
	 	$this->dbEnglish->where('qs.passageID',0);
	 	$this->dbEnglish->where('qs.topicID',2);
	 	$this->dbEnglish->where_in('qa.userID',$studentsArr);
	 	$this->dbEnglish->where('DATE(qa.attemptedDate) >=',$this->getIntDate($startDate));
	 	$this->dbEnglish->where('DATE(qa.attemptedDate) <=',$this->getIntDate($endDate));
		$this->dbEnglish->group_by("qa.userID");

		$query = $this->dbEnglish->get();
		$userDataArr = $query->result_array();
		
			

		$vocabTotalQues=0;
		$vocabTotalCorrect=0;
		$vocabQuesAcc=0;
		foreach($userDataArr as $key=>$valueArr)
		{		
			
			$vocabTotalQues = $valueArr['totalQuesAttempted'];
			if($valueArr['correct']==NULL || $valueArr['correct']==''){
				$valueArr['correct']=0;
			}
			$vocabTotalCorrect=$valueArr['correct'];			
			$vocabQuesAcc = ($vocabTotalQues > 0)? round($vocabTotalCorrect*100/$vocabTotalQues, 1) : 0;

			$usageArr[$valueArr['userID']]['vocabTotalQues'] =intval($vocabTotalQues);
			$usageArr[$valueArr['userID']]['vocabQuesAcc'] =$vocabQuesAcc;		
				
		}
		

		return $usageArr;	
		

	}

	function getEssayAndSparkieDetails($usageArr) {
		
		$startDate=$this->startDate ; 
	 	$endDate=$this->endDate;
	 	$studentsArr=$this->studentsArrIds;
		$tempArr=array(1,2);
		$this->dbEnglish->Select('userID,count(essayID) as totalEssayAttempt,status');
	 	$this->dbEnglish->from("ews_essayDetails");
	 	$this->dbEnglish->where_in('userID',$studentsArr);
	 	$this->dbEnglish->where_in('status',$tempArr);
	 	$this->dbEnglish->where('DATE(submittedOn) >=',$this->getIntDate($startDate));
	 	$this->dbEnglish->where('DATE(submittedOn) <=',$this->getIntDate($endDate));
		$this->dbEnglish->group_by("userID");

		$query = $this->dbEnglish->get();
		$userDataArr = $query->result_array();
	
		foreach($userDataArr as $key=>$valueArr)
		{		
			if($valueArr['totalEssayAttempt']==NULL || $valueArr['totalEssayAttempt']==''){
				$valueArr['totalEssayAttempt']=0;
			}		
			$usageArr[$valueArr['userID']]['totalEssayAttempt'] =$valueArr['totalEssayAttempt'];
				
				
		}		

		$this->dbEnglish->Select('userID,sparkies');
	 	$this->dbEnglish->from('rewardPoints');
	 	$this->dbEnglish->where_in('userID',$studentsArr);
	 	$this->dbEnglish->group_by("userID");

		$query = $this->dbEnglish->get();
		$userDataArr = $query->result_array();
		
		foreach($userDataArr as $key=>$valueArr)
		{		
			
			if($valueArr['sparkies']==NULL || $valueArr['sparkies']==''){
				$valueArr['sparkies']=0;
			}		
			$usageArr[$valueArr['userID']]['sparkies'] =intval($valueArr['sparkies']);
		}
		return $usageArr;
	}
	 function fetchStudentsInSelectedPeriod($startDate,$endDate,$reportMode,$grade,$section,$schoolCode,$dateRestriction=0)
	 {
	 	$this->dbEnglish->_protect_identifiers = false;
	 	$this->schoolCode = $schoolCode;
	 	$this->startDate = $startDate; 
	 	$this->endDate = $endDate;
	 	$this->reportMode = $reportMode;
	 	$this->grade = $grade;
	 	$this->section = $section;

	 	$this->dbEnglish->Select('distinct B.userID,childClass,childSection,childName');
	 	$this->dbEnglish->from('sessionStatus A');
	 	$this->dbEnglish->join('userDetails B','A.userID = B.userID','RIGHT');
	 	$this->dbEnglish->where('B.schoolCode',$this->schoolCode );
	 	$this->dbEnglish->where('B.category', 'STUDENT');
	 	//check enabled = 1
	 	$this->dbEnglish->where('B.enabled', '1');

	 	if($this->reportMode=="ALL" && (strtolower($this->category) == 'teacher'))
		{
			$array = explode('~', $this->session->userdata('teacherClass'));
			$where = "(" ;
			foreach($array as &$value){	
				$value = explode(',', $value);
				$where .= "(B.childClass='$value[0]' AND B.childSection='$value[1]') OR ";		
			}	

			$this->dbEnglish->where(trim(substr($where,0,-3)).")");

		}else{		
		
			if($this->grade!=""){					
				$this->dbEnglish->where('B.childSection',$this->section);
				$this->dbEnglish->where('B.childClass',$this->grade);
			}
		}

		if($dateRestriction==0)
		{
			$this->dbEnglish->where("date(A.startTime)>='".$this->startDate."'");
			$this->dbEnglish->where("date(A.startTime)<='".$this->endDate."'");
		}
		
		$query = $this->dbEnglish->get();
		$this->teacherReportData = $query->result_array();	

		foreach ($this->teacherReportData as $key => $valueArr) {
			$this->teacherReportData[$key]['timeTaken'] = "";
			$this->teacherReportData[$key]['accuracy'] = "0";
			$this->teacherReportData[$key]['totalQues'] = "0";
			$this->teacherReportData[$key]['contentAttempted'] = "";
			array_push($this->studentUserIDArr, $valueArr['userID']);
		}
		
		$this->dbEnglish->_protect_identifiers = true;

	 }

	 function generateQuestionAttemptData()
	 {

	 	$this->dbEnglish->_protect_identifiers = false;
	 	if(count($this->studentUserIDArr) != 0 && !empty($this->studentUserIDArr))
	 	{
	 		$ids = sprintf('FIELD(userID, %s)', implode(', ',$this->studentUserIDArr));

	 		$questionAttemptData = $this->getQuestionAttemptData();

		 	/*$query = "Select B.userID,T.timeTaken,T.accuracy,T.totalQues from (select A.userID, sum(timeTaken)/60 timeTaken, sum(if(correct>0.5, 1, 0))/count(*)*100 accuracy, count(*) totalQues 
			from ".$this->questionAttemptClassTbl." A, questions C where A.qcode=C.qcode and A.userID in (".implode(', ',$this->studentUserIDArr).") and date(A.attemptedDate) >= '".$this->startDate."' and date(A.attemptedDate) <= '".$this->endDate."' group by A.userID)T Right Join userDetails B ON T.userID=B.userID where B.userID IN (".implode(', ',$this->studentUserIDArr).") order by field (B.userID,".implode(', ',$this->studentUserIDArr).")";
			echo "<pre>";print_r($query);echo "</pre>";*/
			//echo "<pre>";print_r($query);echo "</pre>";
		 	// 	$this->dbEnglish->Select('A.userID,sum(timeTaken)/60 timeTaken,sum(if(correct>0.5,1,0))/count(*)*100 accuracy,count(*) totalQues');
			// $this->dbEnglish->from('questionAttempt A');
			// $this->dbEnglish->join('questions C','A.qcode = C.qcode','INNER');
			// $this->dbEnglish->where('C.credit',1);
			// $this->dbEnglish->where_in('A.userID',$this->studentUserIDArr);
			// $this->dbEnglish->order_by($ids);

			// $this->dbEnglish->where("date(A.attemptedDate)>='".$this->startDate."'");
			// $this->dbEnglish->where("date(A.attemptedDate)<='".$this->endDate."'");
			
			// $this->dbEnglish->group_by("A.userID");
			
			//$result = $this->dbEnglish->query($query);

			//$questionAttemptData = $result->result_array();

			$this->dbEnglish->_protect_identifiers = true;
			
			/*foreach ($questionAttemptData as $key => $valueArr) {
				if(in_array($valueArr['userID'], $this->studentUserIDArr))
				{
					foreach ($valueArr as $k => $val) {
						if($k == 'accuracy' || $k == 'timeTaken')
						{
							if($val==null)
								$val = 0.00;
							else
								$val = round($val);
						}	
						$this->teacherReportData[$key]['CLASS'][$k] = $val;
					}
				}
			}*/

			foreach ($questionAttemptData as $question_key => $question_value) {

				foreach ($this->teacherReportData as $teacher_key => $teacher_value) {
					
					if($teacher_value['userID'] == $question_value['userID'])
					{
						$this->teacherReportData[$teacher_key]['timeTaken'] = $questionAttemptData[$question_key]['timeTaken'];
						$this->teacherReportData[$teacher_key]['accuracy'] = $questionAttemptData[$question_key]['accuracy'] == '' ? 0 : $questionAttemptData[$question_key]['accuracy'];
						$this->teacherReportData[$teacher_key]['totalQues'] = $questionAttemptData[$question_key]['totalQues'] == '' ? 0 :$questionAttemptData[$question_key]['totalQues'];
					}
				}
			}
	 	}
	 	else
	 	{
	 		$this->teacherReportData = '';
	 	}
	 }

	 function generatePassageTimeData()
	 {

	 	$this->dbEnglish->_protect_identifiers = false;
	 	if(count($this->studentUserIDArr) > 0 && !empty($this->studentUserIDArr))
	 	{
	 		$ids = sprintf('FIELD(B.userID, %s)', implode(', ',$this->studentUserIDArr));
		 	$this->dbEnglish->Select('B.userID,sum(totalTime)/60 totalTime');
			$this->dbEnglish->from('passageAttempt A');
			$this->dbEnglish->join('userDetails B',"A.userID = B.userID and date(A.lastModified) >= '$this->startDate' and date(A.lastModified) <= '$this->endDate'",'RIGHT');
			$this->dbEnglish->where_in('B.userID',$this->studentUserIDArr);
			//check enabled = 1
	 		$this->dbEnglish->where('B.enabled', '1');

			$this->dbEnglish->order_by($ids);
			$this->dbEnglish->group_by("B.userID");
			
			$query = $this->dbEnglish->get();	
			
			$passageTimeData = $query->result_array();

			$this->dbEnglish->_protect_identifiers = true;

			foreach ($passageTimeData as $key => $valueArr) {
				if(in_array($valueArr['userID'], $this->studentUserIDArr))
				{
					$this->teacherReportData[$key]['timeTaken'] = $this->teacherReportData[$key]['timeTaken'] + $valueArr['totalTime'];
					if($this->teacherReportData[$key]['timeTaken']==null)
							$this->teacherReportData[$key]['timeTaken'] = 0.00;
				}
			}
	 	}
		else
		{
			$this->teacherReportData = '';
		}
	 }

	 function generateContentAttemptedSummary()
	 {
	 	
	 	$startDate = $this->startDate;
	 	$endDate = $this->endDate;

	 	if(count($this->studentUserIDArr) > 0 && !empty($this->studentUserIDArr))
	 	{
	 		$array1 = implode("','",$this->studentUserIDArr);
	 		$ids = sprintf('FIELD(B.userID, %s)', implode(', ',$this->studentUserIDArr));

			foreach ($this->studentUserIDArr as $key => $userID) {
		 				$this->contentSummaryArr[$userID] = "";
		 	}

		 	$this->dbEnglish->_protect_identifiers = FALSE;


		 	$this->dbEnglish->select('B.userID,count(passageID) passageCount');
		 	$this->dbEnglish->from('passageAttempt A');
			$this->dbEnglish->join('userDetails B',"A.userID = B.userID and date(A.lastModified) >= '$this->startDate' and date(A.lastModified) <= '$this->endDate'",'RIGHT');
			$this->dbEnglish->where_in('B.userID',$this->studentUserIDArr);

			//check enabled = 1
	 		$this->dbEnglish->where('B.enabled', '1');

			$this->dbEnglish->order_by($ids);
			$this->dbEnglish->group_by("B.userID");

		 	$query = $this->dbEnglish->get();

		 	$passageAttemptCountArr = $query->result_array();

		 	foreach($passageAttemptCountArr as $key=>$valueArr){
		 		
		 		$this->contentSummaryArr[$valueArr['userID']] .= $valueArr['passageCount']." passage(s), ";
		 	}

		 	

			//start from here
			
		 	//end here

		 	/*$this->dbEnglish->select('B.userID,questionType,count(qcode) qcodeCount');
		 	$this->dbEnglish->from($this->questionAttemptClassTbl.' A');
		 	$this->dbEnglish->join('userDetails B',"A.userID = B.userID and date(A.lastModified) >= '$this->startDate' and date(A.lastModified) <= '$this->endDate'",'RIGHT');
		 	$this->dbEnglish->where_in('B.userID',$this->studentUserIDArr);
			$this->dbEnglish->group_by("B.userID");
		 	$this->dbEnglish->group_by('questionType');
		 	$this->dbEnglish->order_by('questionType','desc');
		 	$this->dbEnglish->order_by($ids);
		 	$query = $this->dbEnglish->get();
		 	echo "<pre>";print_r($this->dbEnglish->last_query());echo "</pre>";
		 	$quesAttemptCountArr = $query->result_array();*/
		 	$quesAttemptCountArr = $this->getContemptAttemptedPassage();
		 	
		 	foreach($quesAttemptCountArr as $key=>$valueArr){
		 		
		 		if($valueArr['questionType']=='passageQues')
		 		{
		 			$this->contentSummaryArr[$valueArr['userID']] .= $valueArr['qcodeCount']." passage question(s), ";
		 		}
		 		elseif($valueArr['questionType']=='freeQues')
		 		{
		 			$this->contentSummaryArr[$valueArr['userID']] .= $valueArr['qcodeCount']." non-passage questions(s), ";
		 		}
		 		elseif($valueArr['questionType']==null)
		 		{
		 			$this->contentSummaryArr[$valueArr['userID']] .= "0 passage question(s), 0 non-passage questions(s), ";
		 		}

		 	}


		 	$this->dbEnglish->select('B.userID,count(srno) igreCount');
		 	$this->dbEnglish->from('IGREAttemptDetails A');
		 	$this->dbEnglish->join('userDetails B',"A.userID = B.userID and date(A.lastModified) >= '$this->startDate' and date(A.lastModified) <= '$this->endDate'",'RIGHT');
			$this->dbEnglish->where_in('B.userID',$this->studentUserIDArr);

			//check enabled = 1
	 		$this->dbEnglish->where('B.enabled', '1');

			$this->dbEnglish->order_by($ids);
			$this->dbEnglish->group_by("B.userID");
		 	$query = $this->dbEnglish->get();


		 	$passageAttemptCountArr = $query->result_array();

		 	foreach($passageAttemptCountArr as $key=>$valueArr){
		 		
		 		$this->contentSummaryArr[$valueArr['userID']] .= $valueArr['igreCount']." activity ";
		 	}

		 	$this->dbEnglish->_protect_identifiers = TRUE;

		 	foreach ($this->teacherReportData as $key => $userAttemptDataArr) {
		 		$this->teacherReportData[$key]['contentAttempted'] =  substr($this->contentSummaryArr[$userAttemptDataArr['userID']],0,-1);
		 	}
	 	}
	 	else
	 	{
	 		$this->teacherReportData = '';
	 	}
	 }

	 //function getOutliersData($dateMode)
	 function getOutliersData()
	 {
	 	$this->fetchStudentsSatisfyingAccuracyOutliers();
	 	//$this->fetchStudentsNotLoggedInSelectedPeriod($dateMode);
	 	$this->fetchStudentsNotLoggedInSelectedPeriod();
	 	//$this->fetchStudentsSatisfyingTimeSpentOutliers();
	 }

	 function fetchStudentsSatisfyingAccuracyOutliers()
	 {
	 	
	 	if(count($this->teacherReportData) == 0 || empty($this->teacherReportData) ||  $this->teacherReportData == '')
	 	{
	 		$this->outlierArr['accuracyLessThan20Per'] = '';
	 		$this->outlierArr['accuracyMoreThan80Per'] = '';
	 	}
	 	else
	 	{
	 		foreach ($this->teacherReportData as $key => $valueArr) 
	 		{
	 			
		 		//if($valueArr['accuracy']<20 && $valueArr['totalQues']>=15)
		 		if($valueArr['accuracy']<20 && $valueArr['totalQues'] > 0 && $valueArr['totalQues'] != '')
		 		{
		 			$lessAccuracyArr = array(
		 				'userID'=> $valueArr['userID'],
		 				'childName' => $valueArr['childName'],
		 				'childClass' => $valueArr['childClass'],
		 				'childSection' => $valueArr['childSection'],
		 				'accuracy' => round($valueArr['accuracy']),
		 				'totalQues' => $valueArr['totalQues'] == '' ? 0 : $valueArr['totalQues']
		 			);

		 			$this->outlierArr['accuracyLessThan20Per'][] = $lessAccuracyArr;
		 		}
		 		//else if($valueArr['accuracy']>80 && $valueArr['totalQues']>=15)
		 		else if($valueArr['accuracy']>80)
		 		{
		 			$moreAccuracyArr = array(
		 				'userID'=> $valueArr['userID'],
		 				'childName' => $valueArr['childName'],
		 				'childClass' => $valueArr['childClass'],
		 				'childSection' => $valueArr['childSection'],
		 				'accuracy' => $valueArr['accuracy'],
		 				'totalQues' => $valueArr['totalQues']
		 			);

		 			$this->outlierArr['accuracyMoreThan80Per'][] = $moreAccuracyArr;
		 		}
		 	}

		 	if(count($this->outlierArr['accuracyLessThan20Per']) > 0 || !empty($this->outlierArr['accuracyLessThan20Per']))
		 	{
		 		foreach ($this->outlierArr['accuracyLessThan20Per'] as $key20 => $value20) 
			 	{
			 		if($value20['childName'] == '')
			 			unset($this->outlierArr['accuracyLessThan20Per'][$key20]);
			 	}
		 	}

		 	if(count($this->outlierArr['accuracyMoreThan80Per']) > 0 || !empty($this->outlierArr['accuracyMoreThan80Per']))
		 	{
		 		foreach ($this->outlierArr['accuracyMoreThan80Per'] as $key80 => $value80) 
			 	{
			 		if($value80['childName'] == '')
			 			unset($this->outlierArr['accuracyMoreThan80Per'][$key80]);
			 	}
		 	}
		 	
	 	}
	 	
	}

	/*function fetchStudentsNotLoggedInSelectedPeriod()
	{
	 	$startDate = $this->startDate;
	 	$endDate = $this->endDate;
	 	$studentNotLoggedInPeriodArr = array();

	 	$this->dbEnglish->Select('userID,childClass,childSection,childName');
	 	$this->dbEnglish->from('userDetails');
	 	$this->dbEnglish->where('schoolCode',$this->schoolCode);
	 	$this->dbEnglish->where('category','STUDENT');
	 	if($this->reportMode=="ALL")
		{
			$array = explode('~', $this->session->userdata('teacherClass'));
			$child_class = array() ;
			foreach($array as &$value){	
				$value = explode(',', $value);
				//$where .= "(B.childClass='$value[0]' AND B.childSection='$value[1]') OR ";		
				array_push($child_class, $value[0]);
			}	
			
	 		$this->dbEnglish->where_in('childClass',$child_class);
		}
		else
		{
	 		$this->dbEnglish->where('childClass',$this->grade);
	 		$this->dbEnglish->where('childSection',$this->section);
		}
		$result_all = $this->dbEnglish->get();
		$all_users = $result_all->result_array();
		
		$this->dbEnglish->Select('group_concat(distinct sessionStatus.userID) USERS,date(startTime) DATE_LOG');
	 	$this->dbEnglish->from('sessionStatus');
	 	$this->dbEnglish->join('userDetails','userDetails.userID = sessionStatus.userID and userDetails.schoolCode = "'.$this->schoolCode.'" and userDetails.category="STUDENT"');
	 	$this->dbEnglish->where('date(startTime) >=',$this->startDate);
	 	$this->dbEnglish->where('date(startTime) <=',$this->endDate);
	 	$this->dbEnglish->group_by('date(startTime)');

		$result_session = $this->dbEnglish->get();
		$session_users = $result_session->result_array();

		$user_list_array = array();
		
		foreach($all_users as $master)
		{
			array_push($user_list_array, $master['userID']);
			$this->outlierArr['noLoggedMoreThanSelectedDays'][] = $master;
		}

		foreach($session_users as $user)
		{
			$users_list = explode(',',$user['USERS']);

			$diff_array = array_diff($user_list_array, $users_list);
			
			foreach ($this->outlierArr['noLoggedMoreThanSelectedDays'] as $key => $value) 
			{
				foreach($diff_array as $diff)
				{
					if( $this->outlierArr['noLoggedMoreThanSelectedDays'][$key]['userID'] == $diff )
					{
						if(!isset($this->outlierArr['noLoggedMoreThanSelectedDays'][$key]['count']))
							$this->outlierArr['noLoggedMoreThanSelectedDays'][$key]['count'] = 1;
						else
							$this->outlierArr['noLoggedMoreThanSelectedDays'][$key]['count'] += 1;
					}
				}
			}	
			
		}

		
	 	// $this->dbEnglish->Select('A.userID,childClass,childSection,childName,max(date(A.lastModified)) date');
	 	// $this->dbEnglish->from('sessionStatus A');
	 	// $this->dbEnglish->join('userDetails B','A.userID = B.userID','RIGHT');
	 	// $this->dbEnglish->where('B.schoolCode',$this->schoolCode );
	 	// $this->dbEnglish->where("date(A.lastModified) NOT BETWEEN '$startDate' AND '$endDate'");
	 	//$where = "WHERE  B.schoolCode ='".$this->schoolCode."' AND ";
	 // 	$child_where = "";
	 // 	if($this->reportMode=="ALL")
		// {
		// 	$array = explode('~', $this->session->userdata('teacherClass'));
		// 	$child_where .= "" ;
		// 	foreach($array as &$value){	
		// 		$value = explode(',', $value);
		// 		//$where .= "(B.childClass='$value[0]' AND B.childSection='$value[1]') OR ";		
		// 		$child_where .= "$value[0],";		
		// 	}	
		// 	$section_where = '';
		// 	//$child_where .= " AND childClass in (".substr($child_where,0,strlen($child_where)-1).')');
		
		// }else{		
		
		// 	if($this->grade !=""){
		// 		$section_where = " B.childSection =  '".$this->section."'";
		// 		$child_where .= " AND B.childClass =  '".$this->grade."'";
		// 	}
		// }

		// $group_by = "GROUP BY B.userID) as new_table where date  > '$startDate' AND date  < '$endDate'";
		// $query = "select new_table.userID, new_table.childClass, new_table.childSection, date, new_table.childName from ( SELECT A.userID, childClass, childSection, childName, max(date(A.lastModified)) date FROM (sessionStatus A) Right JOIN userDetails B ON A.userID = B.userID WHERE  B.schoolCode ='".$this->schoolCode."' and B.category = 'STUDENT' AND  $where $group_by";
		// //$this->dbEnglish->group_by('B.userID');
	 // 	$query_result = $this->dbEnglish->query($query);
	 // 	//echo $this->dbEnglish->last_query();

	 // 	//$lastLoggedInDateArr = $query->result_array();
	 // 	$lastLoggedInDateArr = $query_result->result_array();

	 // 	$today = date( "Y-m-d" );


	 // 	if(count($lastLoggedInDateArr)>=1)
	 // 	{
	 // 		foreach ($lastLoggedInDateArr as $key => $valueArr) 
		//  	{
		// 		// if($dateMode=='TW' || $dateMode=='LW')
		// 		// 	$lastWeekDate = strtotime( $today . " -1 week" );
		// 		// else
		// 		// 	$lastWeekDate = strtotime( $today . " -1 month" );

		// 		// $this->lastLoggedInStartDate = date("Y-m-d", $lastWeekDate);

		// 		// $lastLoggedInDate = strtotime($valueArr['date']);

		// 		// if($valueArr['date']==null)
		// 		// 	$lastLoggedInDateFormat = "NEVER";
		// 		// else
		// 		// 	$lastLoggedInDateFormat = date("d-m-Y", $lastLoggedInDate);

		// 		// $this->lastLoggedInUserInfo[$valueArr['userID']] = array(
		// 		// 		'lastLoggedInDate' => $lastLoggedInDateFormat,
		// 		// 	);
		// 		// $getTodayYesterday = $this->getDayName($lastLoggedInDateFormat);
		// 		// if($lastWeekDate>=$lastLoggedInDate)
		// 		// { 

		// 			 $this->outlierArr['noLoggedMoreThanSelectedDays'][] = array(
		// 				'userID' => $valueArr['userID'],
		// 				'childName' => $valueArr['childName'],
		// 				'childClass' => $valueArr['childClass'],
		// 				'childSection' => $valueArr['childSection'],
		// 				'lastLoggedInDate' => $getTodayYesterday,
		// 			);
		// 		//}	
	 //       }
	 // 	} 	
	}*/

	function fetchStudentsNotLoggedInSelectedPeriod()
	{
		$dateMode = '';
		$startDate = $this->startDate;
	 	$endDate = $this->endDate;

	 	$studentNotLoggedInPeriodArr = array();
	 	$this->dbEnglish->Select('B.userID,childClass,childSection,childName');
	 	$this->dbEnglish->from('userDetails B');
	 	//$this->dbEnglish->join('userDetails B','A.userID = B.userID','RIGHT');
	 	$this->dbEnglish->where('B.schoolCode',$this->schoolCode );
	 	$this->dbEnglish->where('B.category','STUDENT');
	 	//check enabled = 1
	 	$this->dbEnglish->where('B.enabled', '1');
	 	$childClass_arr = array();
	 	$userIds = array();

	 	if($this->reportMode=="ALL")
		{
			//$array = explode('~', $this->session->userdata('teacherClass'));
			
			/*foreach($array as &$value){	
				array_push($childClass_arr, $value[0]);	
			}	

			$this->dbEnglish->where_in('B.childClass',$childClass_arr);*/
			$array = explode('~', $this->session->userdata('teacherClass'));
			$where = "(" ;
			foreach($array as &$value){	
				$value = explode(',', $value);
				$where .= "(B.childClass='$value[0]' AND B.childSection='$value[1]') OR ";		
			}	

			$this->dbEnglish->where(trim(substr($where,0,-3)).")");

		}else{		
		
			if($this->grade!=""){					
				$this->dbEnglish->where('B.childSection',$this->section);
				$this->dbEnglish->where('B.childClass',$this->grade);
			}
		}
		$this->dbEnglish->group_by('B.userID');
	 	$query = $this->dbEnglish->get();	
	 	$lastLoggedInDateArr = $query->result_array();
	 	
	 	$today = date( "Y-m-d" );

	 	
	 	
	 	
	 	foreach ($lastLoggedInDateArr as $key => $value) 
	 	{
	 		array_push($userIds, $value['userID']);
	 	}

	 	if(strtolower($this->session->userdata('category')) != "teacher")
	 		$lastLoggedInDateArrNew = 	$lastLoggedInDateArr;
	 	else
	 	{

	 		$questionAttemptData = $this->getQuestionAttemptDataLoggedin($userIds,$this->reportMode);

	 		$lastLoggedInDateArrNew = $this->myArrayDiff($lastLoggedInDateArr, $questionAttemptData);
	 	}
	 	

	 	//if(count($this->teacherReportData)>=1)
	 	if(count($lastLoggedInDateArrNew)>=1)
	 	{
	 		//foreach ($this->teacherReportData as $key => $valueArr) 
	 		foreach ($lastLoggedInDateArrNew as $key => $valueArr) 
		 	{
				//get max start time from sessionStatus
				$this->dbEnglish->Select('max(DATE(startTime)) as max_date');
			 	$this->dbEnglish->from('sessionStatus');
			 	$this->dbEnglish->where('userid',$valueArr['userID']);
			 	$query = $this->dbEnglish->get();	
	 			$session_status_user = $query->result_array();
	 			
				//end
				/*$lastLoggedInDate = strtotime($valueArr['date']);

				$startDate        = strtotime($this->startDate);
	 			$endDate = strtotime($this->endDate);
				
				if($valueArr['date']==null)
					$lastLoggedInDateFormat = "NEVER";
				else
					$lastLoggedInDateFormat = date("d-m-Y", $lastLoggedInDate);
				
					
				
				$this->lastLoggedInUserInfo[$valueArr['userID']] = array(
						'lastLoggedInDate' => $lastLoggedInDateFormat,
					);*/
			
				if(($session_status_user[0]['max_date'] >= $startDate) && ($session_status_user[0]['max_date'] <= $endDate) && $valueArr['totalQues'] <= 0)
				{ 
					 //echo "<pre>";print_r($valueArr);echo "</pre>";
					$this->outlierArr['noLoggedMoreThanSelectedDays'][] = array(
						'userID'           => $valueArr['userID'],
						'childName'        => $valueArr['childName'],
						'childClass'       => $valueArr['childClass'],
						'childSection'     => $valueArr['childSection'],
					);	
				}
				else
				{
					/*$this->outlierArr['noLoggedMoreThanSelectedDays'][] = array(
						'userID'           => $valueArr['userID'],
						'childName'        => $valueArr['childName'],
						'childClass'       => $valueArr['childClass'],
						'childSection'     => $valueArr['childSection'],
					);	*/
				}
				
	       	}
	       	//echo "<pre>";print_r($this->outlierArr['noLoggedMoreThanSelectedDays']);echo "</pre>";
	 	}
	}

	// function to remove duplicates
	function myArrayDiff($array1, $array2) {
	    // loop through each item on the first array
	    foreach ($array1 as $key => $row) {
	        // loop through array 2 and compare
	        foreach ($array2 as $key2 => $row2) {
	            if ($row['userID'] == $row2['userID']) {
	                // if we found a match unset and break out of the loop
	                unset($array1[$key]);
	                break;
	            }
	        }
	    }

	    return array_values($array1);
	}

	function fetchStudentsSatisfyingTimeSpentOutliers()
	{
		if(count($this->teacherReportData) == 0 || empty($this->teacherReportData))
		{
			$this->outlierArr['timeLessThan30Mins'][] = '';
			$this->outlierArr['timeMoreThan90Mins'][] = '';

		}
		else
		{
			foreach ($this->teacherReportData as $key => $valueArr) 
			{
	 		
		 		$timeSpentByUser = $this->getTimeSpent($valueArr['userID']);
		 		$getTodayYesterday = $this->getDayName($this->lastLoggedInUserInfo[$valueArr['userID']]['lastLoggedInDate']);
		 		if($timeSpentByUser<30)
		 		{
		 			$lessTimeSpentArr = array(
		 				'userID'=> $valueArr['userID'],
		 				'childName' => $valueArr['childName'],
		 				'childClass' => $valueArr['childClass'],
		 				'childSection' => $valueArr['childSection'],
		 				'timeSpentByUser' => $timeSpentByUser,
		 				'lastLoggedInDate' => $getTodayYesterday
		 			);

		 			$this->outlierArr['timeLessThan30Mins'][] = $lessTimeSpentArr;
		 		}
		 		if($timeSpentByUser>90)
		 		{
		 			$moreTimeSpentArr = array(
		 				'userID'=> $valueArr['userID'],
		 				'childName' => $valueArr['childName'],
		 				'childClass' => $valueArr['childClass'],
		 				'childSection' => $valueArr['childSection'],
		 				'timeSpentByUser' => $timeSpentByUser,
		 				'lastLoggedInDate' => $getTodayYesterday
		 			);

		 			$this->outlierArr['timeMoreThan90Mins'][] = $moreTimeSpentArr;
		 		}
		 		

		 	}
		}
		
	}


	function getTimeSpent($userID)
	{
	    $this->dbEnglish->Select('sessionID,startTime,endTime');
		$this->dbEnglish->from('sessionStatus');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where("date(startTime)>='".$this->startDate."'");
		$this->dbEnglish->where("date(startTime)<='".$this->endDate."'");

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
					$this->dbEnglish->where('userID',$userID);
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
		//return round( ($timeSpent/60) , 2);
		return round(($timeSpent/60));
	}

	/**
	 * function role : Changing time format
	 * param1 : time
	 * @return  time
	 * 
	 **/

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

	function getDayName($date) 
	{
		if($date == date('d-m-Y'))
			$getDay = 'TODAY';
		elseif($date == date('d-m-Y',strtotime("-1 days")))
			$getDay = 'YESTERDAY';
		else
			$getDay = $date;
		return $getDay;
	}

	//vocab grammer
	function getSkillwiseAccuracyDetailsTopic($data)
	{
		$responseArr=array();
		
		$grade_arr=array();
		$grade_arr=$this->get_teacher_admin_classes($data['reportMode']);

		//	questionAttempt table as per class

		//	Get Data for TOPic
		if(count($grade_arr) > 0)
		{	
			$query = '';

			if(in_array('3', $grade_arr))
			{
				$query .="(SELECT user.userName, topic.name, user.userID,user.childClass, user.childSection, user.childName, count(qa.srno) as totalAttempted, round((sum(correct) / count(qa.srno)) * 100) as studentAcc, (sum(timeTaken) / 60) as time_min
				FROM userDetails as user JOIN questionAttempt_class3 as qa ON user.userID=qa.userID
				AND DATE(qa.attemptedDate) >= '".$data['start_date']."' AND DATE(qa.attemptedDate) <= '".$data['end_date']."' JOIN 
				questions as q ON qa.qcode=q.qcode AND q.qtype!='openEnded' JOIN topicMaster as topic ON q.topicID=topic.
				topicID WHERE user.schoolcode=".$data['school_code']." and user.enabled='1'";
				$where1 ="AND user.childClass='3'";

				if(in_array('4', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where1 AND user.category='Student' AND  questionType!='passageQues' GROUP BY user.userName, topic.name ORDER BY user.userName, topic.name) $check_union";
			}
			if(in_array('4', $grade_arr))
			{
				$query .="(SELECT user.userName, topic.name, user.userID,user.childClass, user.childSection, user.childName, count(qa.srno) as totalAttempted, round((sum(correct) / count(qa.srno)) * 100) as studentAcc, (sum(timeTaken) / 60) as time_min
				FROM userDetails as user JOIN questionAttempt_class4 as qa ON user.userID=qa.userID
				AND DATE(qa.attemptedDate) >= '".$data['start_date']."' AND DATE(qa.attemptedDate) <= '".$data['end_date']."' JOIN 
				questions as q ON qa.qcode=q.qcode AND q.qtype!='openEnded' JOIN topicMaster as topic ON q.topicID=topic.
				topicID WHERE user.schoolcode=".$data['school_code']." and user.enabled='1'";
				$where1 ="AND user.childClass='4'";
				if(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where1 AND user.category='Student' AND  questionType!='passageQues' GROUP BY user.userName, topic.name ORDER BY user.userName, topic.name) $check_union";
			}
			if(in_array('5', $grade_arr))
			{
				$query .="(SELECT user.userName, topic.name, user.userID,user.childClass, user.childSection, user.childName, count(qa.srno) as totalAttempted, round((sum(correct) / count(qa.srno)) * 100) as studentAcc, (sum(timeTaken) / 60) as time_min
				FROM userDetails as user JOIN questionAttempt_class5 as qa ON user.userID=qa.userID
				AND DATE(qa.attemptedDate) >= '".$data['start_date']."' AND DATE(qa.attemptedDate) <= '".$data['end_date']."' JOIN 
				questions as q ON qa.qcode=q.qcode AND q.qtype!='openEnded' JOIN topicMaster as topic ON q.topicID=topic.
				topicID WHERE user.schoolcode=".$data['school_code']." and user.enabled='1'";
				$where2 ="AND user.childClass='5'";
				if (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where2 AND user.category='Student' AND  questionType!='passageQues' GROUP BY user.userName, topic.name ORDER BY user.userName, topic.name) $check_union";
			}
			if(in_array('6', $grade_arr))
			{
				$query .="(SELECT user.userName, topic.name, user.userID,user.childClass, user.childSection, user.childName, count(qa.srno) as totalAttempted, round((sum(correct) / count(qa.srno)) * 100) as studentAcc, (sum(timeTaken) / 60) as time_min
				FROM userDetails as user JOIN questionAttempt_class6 as qa ON user.userID=qa.userID
				AND DATE(qa.attemptedDate) >= '".$data['start_date']."' AND DATE(qa.attemptedDate) <= '".$data['end_date']."' JOIN 
				questions as q ON qa.qcode=q.qcode AND q.qtype!='openEnded' JOIN topicMaster as topic ON q.topicID=topic.
				topicID WHERE user.schoolcode=".$data['school_code']." and user.enabled='1'";
				$where3 ="AND user.childClass='6'";
				if (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where3 AND user.category='Student' AND  questionType!='passageQues' GROUP BY user.userName, topic.name ORDER BY user.userName, topic.name) $check_union ";
			}
			if(in_array('7', $grade_arr))
			{
				$query .="(SELECT user.userName, topic.name, user.userID,user.childClass, user.childSection, user.childName, count(qa.srno) as totalAttempted, round((sum(correct) / count(qa.srno)) * 100) as studentAcc, (sum(timeTaken) / 60) as time_min
				FROM userDetails as user JOIN questionAttempt_class7 as qa ON user.userID=qa.userID
				AND DATE(qa.attemptedDate) >= '".$data['start_date']."' AND DATE(qa.attemptedDate) <= '".$data['end_date']."' JOIN 
				questions as q ON qa.qcode=q.qcode AND q.qtype!='openEnded' JOIN topicMaster as topic ON q.topicID=topic.
				topicID WHERE user.schoolcode=".$data['school_code']." and user.enabled='1'";
				$where4 ="AND user.childClass='7'";
				if (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where4 AND user.category='Student' AND  questionType!='passageQues' GROUP BY user.userName, topic.name ORDER BY user.userName, topic.name) $check_union";
			}
			if(in_array('8', $grade_arr))
			{
				$query .="(SELECT user.userName, topic.name, user.userID,user.childClass, user.childSection, user.childName, count(qa.srno) as totalAttempted, round((sum(correct) / count(qa.srno)) * 100) as studentAcc, (sum(timeTaken) / 60) as time_min
				FROM userDetails as user JOIN questionAttempt_class8 as qa ON user.userID=qa.userID
				AND DATE(qa.attemptedDate) >= '".$data['start_date']."' AND DATE(qa.attemptedDate) <= '".$data['end_date']."' JOIN 
				questions as q ON qa.qcode=q.qcode AND q.qtype!='openEnded' JOIN topicMaster as topic ON q.topicID=topic.
				topicID WHERE user.schoolcode=".$data['school_code']." and user.enabled='1'";
				$where5 ="AND user.childClass='8'";
				if (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where5 AND user.category='Student' AND  questionType!='passageQues' GROUP BY user.userName, topic.name ORDER BY user.userName, topic.name) $check_union";
			}
			if(in_array('9', $grade_arr))
			{
				$query .="(SELECT user.userName, topic.name, user.userID,user.childClass, user.childSection, user.childName, count(qa.srno) as totalAttempted, round((sum(correct) / count(qa.srno)) * 100) as studentAcc, (sum(timeTaken) / 60) as time_min
				FROM userDetails as user JOIN questionAttempt_class9 as qa ON user.userID=qa.userID
				AND DATE(qa.attemptedDate) >= '".$data['start_date']."' AND DATE(qa.attemptedDate) <= '".$data['end_date']."' JOIN 
				questions as q ON qa.qcode=q.qcode AND q.qtype!='openEnded' JOIN topicMaster as topic ON q.topicID=topic.
				topicID WHERE user.schoolcode=".$data['school_code']." and user.enabled='1'";
				$where6 ="AND user.childClass='9'";
				$query .=" $where6 AND user.category='Student' AND  questionType!='passageQues' GROUP BY user.userName, topic.name ORDER BY user.userName, topic.name)";
			}
			$resultData=$this->dbEnglish->query($query);
		}
		else
		{
			$questionAttempt="questionAttempt_class".$data['class'];
			$query="(SELECT user.userName, topic.name, user.userID,user.childClass, user.childSection, user.childName, count(qa.srno) as totalAttempted, round((sum(correct) / count(qa.srno)) * 100) as studentAcc, (sum(timeTaken) / 60) as time_min
				FROM userDetails as user JOIN $questionAttempt as qa ON user.userID=qa.userID
				AND DATE(qa.attemptedDate) >= '".$data['start_date']."' AND DATE(qa.attemptedDate) <= '".$data['end_date']."' JOIN 
				questions as q ON qa.qcode=q.qcode AND q.qtype!='openEnded' JOIN topicMaster as topic ON q.topicID=topic.
				topicID WHERE user.schoolcode=".$data['school_code']." and user.enabled='1' and user.childClass=".$data['class']." and user.childSection='".$data['section']."'";	
				
			$query .="AND user.category='Student' AND  questionType != 'passageQues' GROUP BY user.userName, topic.name ORDER BY user.userName, topic.name)";
	
				$resultData=$this->dbEnglish->query($query);
		}
		return $resultData->result_array();

	}

	//reading listining
	function getSkillwiseAccuracyDetailsPassage($data)
	{

		$grade_arr=array();
		$grade_arr=$this->get_teacher_admin_classes($data['reportMode']);
		
		if(count($grade_arr) > 0)
		{
			$query = '';

			if(in_array('3', $grade_arr))
			{
				$query .="(SELECT user.userName, topic.name, user.userID,user.childClass, user.childSection, user.childName, (case when (passageTypeName='Conversation') THEN  'Listening' ELSE  'Reading' END) as name, count(qa.srno) as totalAttempted, round((sum(correct) / count(qa.srno)) * 100) as studentAcc, (sum(timeTaken) / 60) as time_min
				FROM userDetails as user JOIN questionAttempt_class3 as qa ON user.userID=qa.userID
				AND qa.attemptedDate >= '".$data['start_date']."' AND DATE(qa.attemptedDate) <= '".$data['end_date']."' JOIN 
				questions as q ON qa.qcode=q.qcode AND q.qtype!='openEnded' JOIN topicMaster as topic ON q.topicID=topic.
				topicID WHERE user.schoolcode=".$data['school_code']."  and user.enabled='1' ";
				$where1 ="AND user.childClass='3'";

				if(in_array('4', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where1 AND user.category='Student' AND  questionType='passageQues' GROUP BY user.userName, (case when (passageTypeName='Conversation') THEN  'Listening' ELSE  'Reading' END) ORDER BY user.userName,name) $check_union";
			}
			if(in_array('4', $grade_arr))
			{
				$query .="(SELECT user.userName, topic.name, user.userID,user.childClass, user.childSection, user.childName, (case when (passageTypeName='Conversation') THEN  'Listening' ELSE  'Reading' END) as name, count(qa.srno) as totalAttempted, round((sum(correct) / count(qa.srno)) * 100) as studentAcc, (sum(timeTaken) / 60) as time_min
				FROM userDetails as user JOIN questionAttempt_class4 as qa ON user.userID=qa.userID
				AND qa.attemptedDate >= '".$data['start_date']."' AND DATE(qa.attemptedDate) <= '".$data['end_date']."' JOIN 
				questions as q ON qa.qcode=q.qcode AND q.qtype!='openEnded' JOIN topicMaster as topic ON q.topicID=topic.
				topicID WHERE user.schoolcode=".$data['school_code']."  and user.enabled='1' ";
				$where1 ="AND user.childClass='4'";
				if(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where1 AND user.category='Student' AND  questionType='passageQues' GROUP BY user.userName, (case when (passageTypeName='Conversation') THEN  'Listening' ELSE  'Reading' END) ORDER BY user.userName,name) $check_union";
			}
			if(in_array('5', $grade_arr))
			{
				$query .="(SELECT user.userName, topic.name, user.userID,user.childClass, user.childSection, user.childName, (case when (passageTypeName='Conversation') THEN  'Listening' ELSE  'Reading' END) as name, count(qa.srno) as totalAttempted, round((sum(correct) / count(qa.srno)) * 100) as studentAcc, (sum(timeTaken) / 60) as time_min
				FROM userDetails as user JOIN questionAttempt_class5 as qa ON user.userID=qa.userID
				AND DATE(qa.attemptedDate) >= '".$data['start_date']."' AND DATE(qa.attemptedDate) <= '".$data['end_date']."' JOIN 
				questions as q ON qa.qcode=q.qcode AND q.qtype!='openEnded' JOIN topicMaster as topic ON q.topicID=topic.
				topicID WHERE user.schoolcode=".$data['school_code']."  and user.enabled='1' ";
				$where2 ="AND user.childClass='5'";
				if (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where2 AND user.category='Student' AND  questionType='passageQues' GROUP BY user.userName, (case when (passageTypeName='Conversation') THEN  'Listening' ELSE  'Reading' END) ORDER BY user.userName,name) $check_union";
			}
			if(in_array('6', $grade_arr))
			{
				$query .="(SELECT user.userName, topic.name, user.userID,user.childClass, user.childSection, user.childName, (case when (passageTypeName='Conversation') THEN  'Listening' ELSE  'Reading' END) as name, count(qa.srno) as totalAttempted, round((sum(correct) / count(qa.srno)) * 100) as studentAcc, (sum(timeTaken) / 60) as time_min
				FROM userDetails as user JOIN questionAttempt_class6 as qa ON user.userID=qa.userID
				AND DATE(qa.attemptedDate) >= '".$data['start_date']."' AND DATE(qa.attemptedDate) <= '".$data['end_date']."' JOIN 
				questions as q ON qa.qcode=q.qcode AND q.qtype!='openEnded' JOIN topicMaster as topic ON q.topicID=topic.
				topicID WHERE user.schoolcode=".$data['school_code']."  and user.enabled='1' ";
				$where3 ="AND user.childClass='6'";
				if (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where3 AND user.category='Student' AND  questionType='passageQues' GROUP BY user.userName, (case when (passageTypeName='Conversation') THEN  'Listening' ELSE  'Reading' END) ORDER BY user.userName,name) $check_union";
			}
			if(in_array('7', $grade_arr))
			{
				$query .="(SELECT user.userName, topic.name, user.userID,user.childClass, user.childSection, user.childName, (case when (passageTypeName='Conversation') THEN  'Listening' ELSE  'Reading' END) as name, count(qa.srno) as totalAttempted, round((sum(correct) / count(qa.srno)) * 100) as studentAcc, (sum(timeTaken) / 60) as time_min
				FROM userDetails as user JOIN questionAttempt_class7 as qa ON user.userID=qa.userID
				AND DATE(qa.attemptedDate) >= '".$data['start_date']."' AND DATE(qa.attemptedDate) <= '".$data['end_date']."' JOIN 
				questions as q ON qa.qcode=q.qcode AND q.qtype!='openEnded' JOIN topicMaster as topic ON q.topicID=topic.
				topicID WHERE user.schoolcode=".$data['school_code']."  and user.enabled='1' ";
				$where4 ="AND user.childClass='7'";
				if (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where4 AND user.category='Student' AND  questionType='passageQues' GROUP BY user.userName, (case when (passageTypeName='Conversation') THEN  'Listening' ELSE  'Reading' END) ORDER BY user.userName,name) $check_union";
			}
			if(in_array('8', $grade_arr))
			{
				$query .="(SELECT user.userName, topic.name, user.userID,user.childClass, user.childSection, user.childName, (case when (passageTypeName='Conversation') THEN  'Listening' ELSE  'Reading' END) as name, count(qa.srno) as totalAttempted, round((sum(correct) / count(qa.srno)) * 100) as studentAcc, (sum(timeTaken) / 60) as time_min
				FROM userDetails as user JOIN questionAttempt_class8 as qa ON user.userID=qa.userID
				AND DATE(qa.attemptedDate) >= '".$data['start_date']."' AND DATE(qa.attemptedDate) <= '".$data['end_date']."' JOIN 
				questions as q ON qa.qcode=q.qcode AND q.qtype!='openEnded' JOIN topicMaster as topic ON q.topicID=topic.
				topicID WHERE user.schoolcode=".$data['school_code']."  and user.enabled='1'";
				$where5 ="AND user.childClass='8'";
				if (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where5 AND user.category='Student' AND  questionType='passageQues' GROUP BY user.userName, (case when (passageTypeName='Conversation') THEN  'Listening' ELSE  'Reading' END) ORDER BY user.userName,name) $check_union";
			}
			if(in_array('9', $grade_arr))
			{
				$query .="(SELECT user.userName, topic.name, user.userID,user.childClass, user.childSection, user.childName, (case when (passageTypeName='Conversation') THEN  'Listening' ELSE  'Reading' END) as name, count(qa.srno) as totalAttempted, round((sum(correct) / count(qa.srno)) * 100) as studentAcc, (sum(timeTaken) / 60) as time_min
				FROM userDetails as user JOIN questionAttempt_class9 as qa ON user.userID=qa.userID
				AND DATE(qa.attemptedDate) >= '".$data['start_date']."' AND DATE(qa.attemptedDate) <= '".$data['end_date']."' JOIN 
				questions as q ON qa.qcode=q.qcode AND q.qtype!='openEnded' JOIN topicMaster as topic ON q.topicID=topic.
				topicID WHERE user.schoolcode=".$data['school_code']."  and user.enabled='1' ";
				$where6 ="AND user.childClass='9'";
				$query .=" $where6 AND user.category='Student' AND  questionType='passageQues' GROUP BY user.userName, (case when (passageTypeName='Conversation') THEN  'Listening' ELSE  'Reading' END) ORDER BY user.userName,name)";
			}
			
			$resultData=$this->dbEnglish->query($query);
		}
		else
		{
			$questionAttempt="questionAttempt_class".$data['class'];
			$query="(SELECT user.userName, topic.name, user.userID,user.childClass, user.childSection, user.childName, (case when (passageTypeName='Conversation') THEN  'Listening' ELSE  'Reading' END) as name, count(qa.srno) as totalAttempted, round((sum(correct) / count(qa.srno)) * 100) as studentAcc, (sum(timeTaken) / 60) as time_min
				FROM userDetails as user JOIN $questionAttempt as qa ON user.userID=qa.userID
				AND DATE(qa.attemptedDate) >= '".$data['start_date']."' AND DATE(qa.attemptedDate) <= '".$data['end_date']."' JOIN 
				questions as q ON qa.qcode=q.qcode AND q.qtype!='openEnded' JOIN topicMaster as topic ON q.topicID=topic.
				topicID WHERE user.schoolcode=".$data['school_code']."  and user.enabled='1' and user.childClass=".$data['class']." and user.childSection='".$data['section']."'";	
				
			$query .="AND user.category='Student' AND  questionType='passageQues' GROUP BY user.userName, (case when (passageTypeName='Conversation') THEN  'Listening' ELSE  'Reading' END) ORDER BY user.userName,name)";
			
			$resultData=$this->dbEnglish->query($query);
		}

		return $resultData->result_array();
	}

	function  get_sentence_formation_accuracy($data)
	{

		$grade_arr=array();
		$grade_arr=$this->get_teacher_admin_classes($data['reportMode']);

		//	Get Data for sentance formation accuracy
		if(count($grade_arr) > 0)
		{	
			$query = '';


			if(in_array('3', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class3 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID and c.qtype != 'openEnded' and c.qtype != 'openEnded'  and a.qcode=c.qcode and g.skilID = '".$this->sentence_group."' and c.skillID in (10,12,13,14,15,16) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']."  and ud.enabled='1' ";
				$where1 ="and ud.childClass='3'";

				if(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where1 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('4', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class4 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID and c.qtype != 'openEnded' and c.qtype != 'openEnded'  and a.qcode=c.qcode and g.skilID = '".$this->sentence_group."' and c.skillID in (10,12,13,14,15,16) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']."  and ud.enabled='1' ";
				$where1 ="and ud.childClass='4'";
				if(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where1 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('5', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class5 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID and c.qtype != 'openEnded'  and a.qcode=c.qcode and g.skilID = '".$this->sentence_group."' and c.skillID in (10,12,13,14,15,16) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where2 ="and ud.childClass='5'";
				if (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where2 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('6', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class6 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID and c.qtype != 'openEnded'  and a.qcode=c.qcode and g.skilID = '".$this->sentence_group."' and c.skillID in (10,12,13,14,15,16) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where3 ="and ud.childClass='6'";
				if (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where3 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('7', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class7 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID and c.qtype != 'openEnded'  and a.qcode=c.qcode and g.skilID = '".$this->sentence_group."' and c.skillID in (10,12,13,14,15,16) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where4 ="and ud.childClass='7'";
				if (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where4 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('8', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class8 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID and c.qtype != 'openEnded'  and a.qcode=c.qcode and g.skilID = '".$this->sentence_group."' and c.skillID in (10,12,13,14,15,16) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where5 ="and ud.childClass='8'";
				if (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where5 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('9', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class9 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID and c.qtype != 'openEnded'  and a.qcode=c.qcode and g.skilID = '".$this->sentence_group."' and c.skillID in (10,12,13,14,15,16) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where6 ="and ud.childClass='9'";
				
				$query .=" $where6 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode)";
			}
			$resultData=$this->dbEnglish->query($query);
		}	
		else
		{
			//start from ere
			$questionAttempt="questionAttempt_class".$data['class'];
			$query = "(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc 
				from $questionAttempt a, questions c, userDetails ud, groupSkillMaster g 
				where a.userID=ud.userID 
				and c.qtype != 'openEnded'
				and a.qcode=c.qcode 
				and g.skilID = '".$this->sentence_group."'
				and c.skillID in (10,12,13,14,15,16) 
				and ud.userID = a.userID
			 
				AND 
				DATE(a.attemptedDate) >= '".$data['start_date']."'
				AND
				DATE(a.attemptedDate) <= '".$data['end_date']."'
				and
				ud.schoolcode=".$data['school_code']."  and ud.enabled='1' and ud.childClass='".$data['class']."' and  ud.childSection= '".$data['section']."'";

			$query .=" $where AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode)";
			$resultData=$this->dbEnglish->query($query);
		}
		
		return $resultData->result_array();		
	}
	function  get_obj_ref_relation_accuracy($data)
	{
		$grade_arr=array();
		$grade_arr=$this->get_teacher_admin_classes($data['reportMode']);

		//	Get Data for objective reference relation accuracy
		if(count($grade_arr) > 0)
		{
			$query = '';


			if(in_array('3', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class3 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->object_refrence_group."' and c.skillID in (6,7,9,11) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where1 ="and ud.childClass='3'";

				if(in_array('4', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where1 AND ud.category='Student' and c.passageID=0  group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('4', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class4 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->object_refrence_group."' and c.skillID in (6,7,9,11) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where1 ="and ud.childClass='4'";
				if(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where1 AND ud.category='Student' and c.passageID=0  group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('5', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class5 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->object_refrence_group."' and c.skillID in (6,7,9,11) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where2 ="and ud.childClass='5'";
				if (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where2 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('6', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class6 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->object_refrence_group."' and c.skillID in (6,7,9,11) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where3 ="and ud.childClass='6'";
				if (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where3 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('7', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class7 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->object_refrence_group."' and c.skillID in (6,7,9,11) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where4 ="and ud.childClass='7'";
				if (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where4 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('8', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class8 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->object_refrence_group."' and c.skillID in (6,7,9,11) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where5 ="and ud.childClass='8'";
				if (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where5 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('9', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class9 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->object_refrence_group."' and c.skillID in (6,7,9,11) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where6 ="and ud.childClass='9'";
				$query .=" $where6 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode)";
			}
			$resultData=$this->dbEnglish->query($query);
		}
		else
		{
			$questionAttempt="questionAttempt_class".$data['class'];	
			$query = "(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from $questionAttempt a,questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->object_refrence_group."' and c.skillID in (6,7,9,11) and ud.userID = a.userID AND DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."'
				and ud.schoolcode=".$data['school_code']."  and ud.enabled='1' and ud.childClass='".$data['class']."' and  ud.childSection= '".$data['section']."'";
			$query .="AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode)";
			$resultData=$this->dbEnglish->query($query);
		}
		return $resultData->result_array();	
	}
	function  get_verbs_verbforms_accuracy($data)
	{

		$grade_arr=array();
		$grade_arr=$this->get_teacher_admin_classes($data['reportMode']);

		//	Get Data for verbs verbs forms accuracy
		if(count($grade_arr) > 0)
		{
			$query = '';


			if(in_array('3', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class3 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->verbs_group."' and c.skillID in (2,8) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where1 ="and ud.childClass='3'";

				if(in_array('4', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where1 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('4', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class4 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->verbs_group."' and c.skillID in (2,8) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where1 ="and ud.childClass='4'";
				if(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where1 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('5', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class5 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->verbs_group."' and c.skillID in (2,8) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where2 ="and ud.childClass='5'";
				if (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where2 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('6', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class6 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->verbs_group."' and c.skillID in (2,8) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where3 ="and ud.childClass='6'";
				if (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where3 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('7', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class7 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->verbs_group."' and c.skillID in (2,8) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where4 ="and ud.childClass='7'";
				if (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where4 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('8', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class8 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->verbs_group."' and c.skillID in (2,8) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where5 ="and ud.childClass='8'";
				if (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where5 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('9', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class9 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->verbs_group."' and c.skillID in (2,8) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where6 ="and ud.childClass='9'";
				$query .=" $where6 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode)";
			}
			$resultData=$this->dbEnglish->query($query);
		}
		else
		{
			$questionAttempt="questionAttempt_class".$data['class'];
			$query = "(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc
				from $questionAttempt a,questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->verbs_group."' and c.skillID in (2,8) and ud.userID = a.userID AND DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1' and ud.childClass='".$data['class']."' and  ud.childSection= '".$data['section']."'";
			$query .="AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode)";
			$resultData=$this->dbEnglish->query($query);
		}
		return $resultData->result_array();	
	}
	function  get_describing_words_accuracy($data)
	{

		$grade_arr=array();
		$grade_arr=$this->get_teacher_admin_classes($data['reportMode']);

		//	Get Data for describing words accuracy
		if(count($grade_arr) > 0)
		{
			$query = '';


			if(in_array('3', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class3 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->describing_group."' and c.skillID in (3,5) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where1 ="and ud.childClass='3'";

				if(in_array('4', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where1 AND ud.category='Student' and c.passageID=0 and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('4', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class4 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->describing_group."' and c.skillID in (3,5) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where1 ="and ud.childClass='4'";
				if(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where1 AND ud.category='Student' and c.passageID=0 and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('5', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class5 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->describing_group."' and c.skillID in (3,5) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where2 ="and ud.childClass='5'";
				if (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where2 AND ud.category='Student' and c.passageID=0 and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('6', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class6 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->describing_group."' and c.skillID in (3,5) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where3 ="and ud.childClass='6'";
				if (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where3 AND ud.category='Student' and c.passageID=0 and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('7', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class7 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->describing_group."' and c.skillID in (3,5) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where4 ="and ud.childClass='7'";
				if (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where4 AND ud.category='Student' and c.passageID=0 and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('8', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class8 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->describing_group."' and c.skillID in (3,5) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where5 ="and ud.childClass='8'";
				if (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where5 AND ud.category='Student' and c.passageID=0 and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('9', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class9 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->describing_group."' and c.skillID in (3,5) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where6 ="and ud.childClass='9'";
				$query .=" $where6 AND ud.category='Student' and c.passageID=0 and c.passageID=0 group by ud.userID, ud.schoolCode)";
			}
			$resultData=$this->dbEnglish->query($query);
		}
		else
		{
			$questionAttempt="questionAttempt_class".$data['class'];	
			$query = "(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from $questionAttempt a,questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID   and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->describing_group."' and c.skillID in (3,5) and ud.userID = a.userID AND DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."'
				and ud.schoolcode=".$data['school_code']." and ud.enabled='1' and ud.childClass='".$data['class']."' and  ud.childSection= '".$data['section']."'";
			$query .="AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode)";
			$resultData=$this->dbEnglish->query($query);
		}
		return $resultData->result_array();	
	}
	function  get_nouns_pronouns_accuracy($data)
	{

		$grade_arr=array();
		$grade_arr=$this->get_teacher_admin_classes($data['reportMode']);

		//	Get Data for nouns pronouns accuracy
		if(count($grade_arr) > 0)
		{
			$query = '';


			if(in_array('3', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class3 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->nouns_group."' and c.skillID in (1,4) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where1 ="and ud.childClass='3'";

				if(in_array('4', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where1 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('4', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class4 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->nouns_group."' and c.skillID in (1,4) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where1 ="and ud.childClass='4'";
				if(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where1 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('5', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class5 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->nouns_group."' and c.skillID in (1,4) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where2 ="and ud.childClass='5'";
				if (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where2 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('6', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class6 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->nouns_group."' and c.skillID in (1,4) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where3 ="and ud.childClass='6'";
				if (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where3 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('7', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class7 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->nouns_group."' and c.skillID in (1,4) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where4 ="and ud.childClass='7'";
				if (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where4 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('8', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class8 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->nouns_group."' and c.skillID in (1,4) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where5 ="and ud.childClass='8'";
				if (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where5 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('9', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class9 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->nouns_group."' and c.skillID in (1,4) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where6 ="and ud.childClass='9'";
				$query .=" $where6 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode)";
			}
			$resultData=$this->dbEnglish->query($query);
		}
		else
		{
			$questionAttempt="questionAttempt_class".$data['class'];	
			$query = "(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from $questionAttempt a,questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID   and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->nouns_group."' and c.skillID in (1,4) and ud.userID = a.userID AND DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."'
				and ud.schoolcode=".$data['school_code']." and ud.enabled='1' and ud.childClass='".$data['class']."' and  ud.childSection= '".$data['section']."'";
			$query .="AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode)";
			$resultData=$this->dbEnglish->query($query);
		}
		return $resultData->result_array();	
	}
	function  get_punctuations_spelling_phonics_accuracy($data)
	{

		$grade_arr=array();
		$grade_arr=$this->get_teacher_admin_classes($data['reportMode']);

		//	Get Data for punctuatuions spelling phonics accuracy
		if(count($grade_arr) > 0)
		{
			$query = '';


			if(in_array('3', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class3 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->punctuation_group."' and c.skillID in (17,18,21) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where1 ="and ud.childClass='3'";

				if(in_array('4', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where1 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('4', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class4 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->punctuation_group."' and c.skillID in (17,18,21) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where1 ="and ud.childClass='4'";
				if(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where1 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('5', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class5 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->punctuation_group."' and c.skillID in (17,18,21) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where2 ="and ud.childClass='5'";
				if (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where2 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('6', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class6 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->punctuation_group."' and c.skillID in (17,18,21) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where3 ="and ud.childClass='6'";
				if (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where3 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('7', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class7 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->punctuation_group."' and c.skillID in (17,18,21) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where4 ="and ud.childClass='7'";
				if (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where4 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('8', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class8 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->punctuation_group."' and c.skillID in (17,18,21) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where5 ="and ud.childClass='8'";
				if (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where5 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('9', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class9 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->punctuation_group."' and c.skillID in (17,18,21) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where6 ="and ud.childClass='9'";
				$query .=" $where6 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode)";
			}
			$resultData=$this->dbEnglish->query($query);
		}
		else
		{
			$questionAttempt="questionAttempt_class".$data['class'];	
			$query = "(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from $questionAttempt a,questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID   and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->punctuation_group."' and c.skillID in (17,18,21) and ud.userID = a.userID AND DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."'
				and ud.schoolcode=".$data['school_code']." and ud.enabled='1' and ud.childClass='".$data['class']."' and  ud.childSection= '".$data['section']."'";
			$query .="AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode)";
			$resultData=$this->dbEnglish->query($query);
		}
		return $resultData->result_array();		
	}
	function  get_word_meanings_accuracy($data)
	{

		$grade_arr=array();
		$grade_arr=$this->get_teacher_admin_classes($data['reportMode']);

		//	Get Data for word meaning accuracy
		if(count($grade_arr) > 0)
		{
			$query = '';

			if(in_array('3', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class3 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->word_group."' and c.skillID in (19,20,22,23) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where1 ="and ud.childClass='3'";

				if(in_array('4', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where1 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('4', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class4 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->word_group."' and c.skillID in (19,20,22,23) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where1 ="and ud.childClass='4'";
				if(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where1 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('5', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class5 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->word_group."' and c.skillID in (19,20,22,23) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where2 ="and ud.childClass='5'";
				if (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where2 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('6', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class6 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->word_group."' and c.skillID in (19,20,22,23) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where3 ="and ud.childClass='6'";
				if (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where3 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('7', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class7 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->word_group."' and c.skillID in (19,20,22,23) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where4 ="and ud.childClass='7'";
				if (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where4 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('8', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class8 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->word_group."' and c.skillID in (19,20,22,23) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where5 ="and ud.childClass='8'";
				if (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" $where5 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode) $check_union";
			}
			if(in_array('9', $grade_arr))
			{
				$query .="(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from questionAttempt_class9 a, questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID  and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->word_group."' and c.skillID in (19,20,22,23) and ud.userID = a.userID AND  DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."' and ud.schoolcode=".$data['school_code']." and ud.enabled='1'";
				$where6 ="and ud.childClass='9'";
				$query .=" $where6 AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode)";
			}
			//echo $query;
			$resultData=$this->dbEnglish->query($query);
		}
		else
		{
			$questionAttempt="questionAttempt_class".$data['class'];	
			$query = "(select ud.userID, ud.userName, ud.childClass, ud.childName, ud.childSection, a.userID,c.skillID, g.name, round((sum(a.correct) / count(a.srno)) * 100) as studentAcc from $questionAttempt a,questions c, userDetails ud, groupSkillMaster g where a.userID=ud.userID   and a.qcode=c.qcode and c.qtype != 'openEnded' and g.skilID = '".$this->word_group."' and c.skillID in (19,20,22,23) and ud.userID = a.userID AND DATE(a.attemptedDate) >= '".$data['start_date']."' AND DATE(a.attemptedDate) <= '".$data['end_date']."'
				and ud.schoolcode=".$data['school_code']." and ud.enabled='1' and ud.childClass='".$data['class']."' and  ud.childSection= '".$data['section']."'";
			$query .="AND ud.category='Student' and c.passageID=0 group by ud.userID, ud.schoolCode)";
			$resultData=$this->dbEnglish->query($query);
		}
		return $resultData->result_array();
	}

	function getContemptAttemptedPassage()
	{


	 	$startDate = $this->startDate;
	 	$endDate = $this->endDate;

	 	$array1 = implode("','",$this->studentUserIDArr);
	 	$ids = sprintf('FIELD(B.userID, %s)', implode(', ',$this->studentUserIDArr));

		$grade_arr=array();
		$grade_arr=$this->get_teacher_admin_classes($this->reportMode);

		//	Get Data for word meaning accuracy
		if(count($grade_arr) > 0)
		{
			$query = '';

			if(in_array('3', $grade_arr))
			{
				$query .= "(SELECT B.userID, questionType, count(A.qcode) qcodeCount FROM (questionAttempt_class3 A) JOIN userDetails B ON A.userID = B.userID and date(A.attemptedDate) >= '$startDate' and date(A.attemptedDate) <= '$endDate' JOIN 
				questions q ON A.qcode=q.qcode AND q.qtype!='openEnded'";
				
				if(in_array('4', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" WHERE B.userID IN ('".$array1."')  and B.enabled='1' GROUP BY B.userID, questionType ORDER BY questionType desc, $ids) $check_union";
			}
			if(in_array('4', $grade_arr))
			{
				$query .= "(SELECT B.userID, questionType, count(A.qcode) qcodeCount FROM (questionAttempt_class4 A) JOIN userDetails B ON A.userID = B.userID and date(A.attemptedDate) >= '$startDate' and date(A.attemptedDate) <= '$endDate' JOIN 
				questions q ON A.qcode=q.qcode AND q.qtype!='openEnded'";
				
				if(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" WHERE B.userID IN ('".$array1."')  and B.enabled='1' GROUP BY B.userID, questionType ORDER BY questionType desc, $ids) $check_union";
			}
			if(in_array('5', $grade_arr))
			{
				$query .= "(SELECT B.userID, questionType, count(A.qcode) qcodeCount FROM (questionAttempt_class5 A) JOIN userDetails B ON A.userID = B.userID and date(A.attemptedDate) >= '$startDate' and date(A.attemptedDate) <= '$endDate' JOIN 
				questions q ON A.qcode=q.qcode AND q.qtype!='openEnded'";
				if (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" WHERE B.userID IN ('".$array1."') and B.enabled='1' GROUP BY B.userID, questionType ORDER BY questionType desc, $ids) $check_union";
			}
			if(in_array('6', $grade_arr))
			{
				$query .= "(SELECT B.userID, questionType, count(A.qcode) qcodeCount FROM (questionAttempt_class6 A) JOIN userDetails B ON A.userID = B.userID and date(A.attemptedDate) >= '$startDate' and date(A.attemptedDate) <= '$endDate' JOIN 
				questions q ON A.qcode=q.qcode AND q.qtype!='openEnded'";
				if (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" WHERE B.userID IN ('".$array1."') and B.enabled='1' GROUP BY B.userID, questionType ORDER BY questionType desc, $ids) $check_union";
			}
			if(in_array('7', $grade_arr))
			{
				$query .= "(SELECT B.userID, questionType, count(A.qcode) qcodeCount FROM (questionAttempt_class7 A) JOIN userDetails B ON A.userID = B.userID and date(A.attemptedDate) >= '$startDate' and date(A.attemptedDate) <= '$endDate' JOIN 
				questions q ON A.qcode=q.qcode AND q.qtype!='openEnded'";
				if (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" WHERE B.userID IN ('".$array1."') and B.enabled='1' GROUP BY B.userID, questionType ORDER BY questionType desc, $ids) $check_union";
			}
			if(in_array('8', $grade_arr))
			{
				$query .= "(SELECT B.userID, questionType, count(A.qcode) qcodeCount FROM (questionAttempt_class8 A) JOIN userDetails B ON A.userID = B.userID and date(A.attemptedDate) >= '$startDate' and date(A.attemptedDate) <= '$endDate' JOIN 
				questions q ON A.qcode=q.qcode AND q.qtype!='openEnded'";
				$where5 ="and ud.childClass='8'";
				if (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .=" WHERE B.userID IN ('".$array1."') and B.enabled='1' GROUP BY B.userID, questionType ORDER BY questionType desc, $ids) $check_union";
			}
			if(in_array('9', $grade_arr))
			{
				$query .= "(SELECT B.userID, questionType, count(A.qcode) qcodeCount FROM (questionAttempt_class9 A) JOIN userDetails B ON A.userID = B.userID and date(A.attemptedDate) >= '$startDate' and date(A.attemptedDate) <= '$endDate' JOIN 
				questions q ON A.qcode=q.qcode AND q.qtype!='openEnded'";
				$query .=" WHERE B.userID IN ('".$array1."') and B.enabled='1' GROUP BY B.userID, questionType ORDER BY questionType desc, $ids)";
			}
			$resultData=$this->dbEnglish->query($query);
		}
		else
		{
			$questionAttempt="questionAttempt_class".$this->grade;	
			$query .= "(SELECT B.userID, questionType, count(A.qcode) qcodeCount FROM ($questionAttempt A) JOIN userDetails B ON A.userID = B.userID and date(A.attemptedDate) >= '$startDate' and date(A.attemptedDate) <= '$endDate' JOIN 
				questions q ON A.qcode=q.qcode AND q.qtype!='openEnded'";
			$query .=" WHERE B.userID  IN ('".$array1."') and B.enabled='1' GROUP BY B.userID, questionType ORDER BY questionType desc, $ids)";
			
			$resultData=$this->dbEnglish->query($query);
		}
		//echo "<pre>";print_r($query);echo "</pre>";
		$quesAttemptCountArr =  $resultData->result_array();
		return $quesAttemptCountArr;
	}

	function getQuestionAttemptData()
	{

		$startDate = $this->startDate;
		$endDate = $this->endDate;

		$array1 = implode("','",$this->studentUserIDArr);
		$ids = sprintf('FIELD(B.userID, %s)', implode(', ',$this->studentUserIDArr));

		$grade_arr=array();
		$grade_arr=$this->get_teacher_admin_classes($this->reportMode);
		//	Get Data for word meaning accuracy
		if(count($grade_arr) > 0)
		{
			$query = '';

			if(in_array('3', $grade_arr))
			{
				$query .= "(Select B.userID,T.timeTaken,T.accuracy,T.totalQues from (select A.userID, sum(timeTaken)/60 timeTaken, round(sum(if(correct>0.5, 1, 0))/count(*)*100) accuracy, count(*) totalQues 
					from questionAttempt_class3 A, questions C where A.qcode=C.qcode and C.qtype != 'openEnded' and A.userID in (".implode(', ',$this->studentUserIDArr).") and date(A.attemptedDate) >= '".$startDate."' and date(A.attemptedDate) <= '".$endDate."' group by A.userID)T LEFT JOIN userDetails B ON T.userID=B.userID";

				if(in_array('4', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .= " where B.userID IN (".implode(', ',$this->studentUserIDArr).") and B.enabled='1' order by field (B.userID,".implode(', ',$this->studentUserIDArr).")) $check_union";
			}
			if(in_array('4', $grade_arr))
			{
				$query .= "(Select B.userID,T.timeTaken,T.accuracy,T.totalQues from (select A.userID, sum(timeTaken)/60 timeTaken, round(sum(if(correct>0.5, 1, 0))/count(*)*100) accuracy, count(*) totalQues 
					from questionAttempt_class4 A, questions C where A.qcode=C.qcode and C.qtype != 'openEnded' and A.userID in (".implode(', ',$this->studentUserIDArr).") and date(A.attemptedDate) >= '".$startDate."' and date(A.attemptedDate) <= '".$endDate."' group by A.userID)T LEFT JOIN userDetails B ON T.userID=B.userID";
				
				if(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .= " where B.userID IN (".implode(', ',$this->studentUserIDArr).") and B.enabled='1' order by field (B.userID,".implode(', ',$this->studentUserIDArr).")) $check_union";
			}
			if(in_array('5', $grade_arr))
			{
				$query .= "(Select B.userID,T.timeTaken,T.accuracy,T.totalQues from (select A.userID, sum(timeTaken)/60 timeTaken, round(sum(if(correct>0.5, 1, 0))/count(*)*100) accuracy, count(*) totalQues 
					from questionAttempt_class5 A, questions C where A.qcode=C.qcode and C.qtype != 'openEnded' and A.userID in (".implode(', ',$this->studentUserIDArr).") and date(A.attemptedDate) >= '".$startDate."' and date(A.attemptedDate) <= '".$endDate."' group by A.userID)T LEFT JOIN userDetails B ON T.userID=B.userID";
				if (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .= " where B.userID IN (".implode(', ',$this->studentUserIDArr).") and B.enabled='1' order by field (B.userID,".implode(', ',$this->studentUserIDArr).")) $check_union";
			}
			if(in_array('6', $grade_arr))
			{
				$query .= "(Select B.userID,T.timeTaken,T.accuracy,T.totalQues from (select A.userID, sum(timeTaken)/60 timeTaken, round(sum(if(correct>0.5, 1, 0))/count(*)*100) accuracy, count(*) totalQues 
					from questionAttempt_class6 A ,questions C where A.qcode=C.qcode and C.qtype != 'openEnded' and A.userID in (".implode(', ',$this->studentUserIDArr).") and date(A.attemptedDate) >= '".$startDate."' and date(A.attemptedDate) <= '".$endDate."' group by A.userID)T LEFT JOIN userDetails B ON T.userID=B.userID";
				if (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .= " where B.userID IN (".implode(', ',$this->studentUserIDArr).") and B.enabled='1' order by field (B.userID,".implode(', ',$this->studentUserIDArr).")) $check_union";
			}
			if(in_array('7', $grade_arr))
			{
				$query .= "(Select B.userID,T.timeTaken,T.accuracy,T.totalQues from (select A.userID, sum(timeTaken)/60 timeTaken, round(sum(if(correct>0.5, 1, 0))/count(*)*100) accuracy, count(*) totalQues 
					from questionAttempt_class7 A, questions C where A.qcode=C.qcode and C.qtype != 'openEnded' and A.userID in (".implode(', ',$this->studentUserIDArr).") and date(A.attemptedDate) >= '".$startDate."' and date(A.attemptedDate) <= '".$endDate."' group by A.userID)T LEFT JOIN userDetails B ON T.userID=B.userID";
				if (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .= " where B.userID IN (".implode(', ',$this->studentUserIDArr).") and B.enabled='1' order by field (B.userID,".implode(', ',$this->studentUserIDArr).")) $check_union";
			}
			if(in_array('8', $grade_arr))
			{
				$query .= "(Select B.userID,T.timeTaken,T.accuracy,T.totalQues from (select A.userID, sum(timeTaken)/60 timeTaken, round(sum(if(correct>0.5, 1, 0))/count(*)*100) accuracy, count(*) totalQues 
					from questionAttempt_class8 A, questions C where A.qcode=C.qcode and C.qtype != 'openEnded' and A.userID in (".implode(', ',$this->studentUserIDArr).") and date(A.attemptedDate) >= '".$startDate."' and date(A.attemptedDate) <= '".$endDate."' group by A.userID)T LEFT JOIN userDetails B ON T.userID=B.userID";
				$where5 ="and ud.childClass='8'";
				if (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .= " where B.userID IN (".implode(', ',$this->studentUserIDArr).") and B.enabled='1' order by field (B.userID,".implode(', ',$this->studentUserIDArr).")) $check_union";
			}
			if(in_array('9', $grade_arr))
			{
				$query .= "(Select B.userID,T.timeTaken,T.accuracy,T.totalQues from (select A.userID, sum(timeTaken)/60 timeTaken, round(sum(if(correct>0.5, 1, 0))/count(*)*100) accuracy, count(*) totalQues 
					from questionAttempt_class9 A, questions C where A.qcode=C.qcode and C.qtype != 'openEnded' and A.userID in (".implode(', ',$this->studentUserIDArr).") and date(A.attemptedDate) >= '".$startDate."' and date(A.attemptedDate) <= '".$endDate."' group by A.userID)T LEFT JOIN userDetails B ON T.userID=B.userID";
				$query .= " where B.userID IN (".implode(', ',$this->studentUserIDArr).") and B.enabled='1' order by field (B.userID,".implode(', ',$this->studentUserIDArr)."))";
			}
			$resultData=$this->dbEnglish->query($query);
		}
		else
		{
			$questionAttempt="questionAttempt_class".$this->grade;	
			$query .= "(Select B.userID,T.timeTaken,T.accuracy,T.totalQues from (select A.userID, sum(timeTaken)/60 timeTaken, round(sum(if(correct>0.5, 1, 0))/count(*)*100) accuracy, count(*) totalQues 
					from $questionAttempt A, questions C where A.qcode=C.qcode and C.qtype != 'openEnded' and A.userID in (".implode(', ',$this->studentUserIDArr).") and date(A.attemptedDate) >= '".$startDate."' and date(A.attemptedDate) <= '".$endDate."' group by A.userID)T LEFT JOIN userDetails B ON T.userID=B.userID";
			$query .= " where B.userID IN (".implode(', ',$this->studentUserIDArr).") and B.enabled='1' order by field (B.userID,".implode(', ',$this->studentUserIDArr)."))";
			$resultData=$this->dbEnglish->query($query);
		}
		$quesAttemptCountArr =  $resultData->result_array();
		return $quesAttemptCountArr;
	}

	function getQuestionAttemptDataLoggedin($userIds,$reportMode)
	{

		$startDate = $this->startDate;
		$endDate = $this->endDate;

		$array1 = implode("','",$userIds);
		$ids = sprintf('FIELD(B.userID, %s)', implode(', ',$userIds));

		$grade_arr=array();
		$grade_arr=$this->get_teacher_admin_classes($reportMode);
		//	Get Data for word meaning accuracy
		if(count($grade_arr) > 0)
		{
			$query = '';

			if(in_array('3', $grade_arr))
			{
				$query .= "(Select B.userID,T.timeTaken,T.accuracy,T.totalQues from (select A.userID, sum(timeTaken)/60 timeTaken, round(sum(if(correct>0.5, 1, 0))/count(*)*100) accuracy, count(*) totalQues 
					from questionAttempt_class3 A, questions C where A.qcode=C.qcode and C.qtype != 'openEnded' and A.userID in (".implode(', ',$userIds).") and date(A.attemptedDate) >= '".$startDate."' and date(A.attemptedDate) <= '".$endDate."' group by A.userID)T LEFT JOIN userDetails B ON T.userID=B.userID";

				if(in_array('4', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .= " where B.userID IN (".implode(', ',$userIds).") and B.enabled='1' order by field (B.userID,".implode(', ',$userIds).")) $check_union";
			}
			if(in_array('4', $grade_arr))
			{
				$query .= "(Select B.userID,T.timeTaken,T.accuracy,T.totalQues from (select A.userID, sum(timeTaken)/60 timeTaken, round(sum(if(correct>0.5, 1, 0))/count(*)*100) accuracy, count(*) totalQues 
					from questionAttempt_class4 A, questions C where A.qcode=C.qcode and C.qtype != 'openEnded' and A.userID in (".implode(', ',$userIds).") and date(A.attemptedDate) >= '".$startDate."' and date(A.attemptedDate) <= '".$endDate."' group by A.userID)T LEFT JOIN userDetails B ON T.userID=B.userID";
				
				if(in_array('5', $grade_arr))
				{
					$check_union = 'union all';
				}
				elseif (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .= " where B.userID IN (".implode(', ',$userIds).") and B.enabled='1' order by field (B.userID,".implode(', ',$userIds).")) $check_union";
			}
			if(in_array('5', $grade_arr))
			{
				$query .= "(Select B.userID,T.timeTaken,T.accuracy,T.totalQues from (select A.userID, sum(timeTaken)/60 timeTaken, round(sum(if(correct>0.5, 1, 0))/count(*)*100) accuracy, count(*) totalQues 
					from questionAttempt_class5 A, questions C where A.qcode=C.qcode and C.qtype != 'openEnded' and A.userID in (".implode(', ',$userIds).") and date(A.attemptedDate) >= '".$startDate."' and date(A.attemptedDate) <= '".$endDate."' group by A.userID)T LEFT JOIN userDetails B ON T.userID=B.userID";
				if (in_array('6', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .= " where B.userID IN (".implode(', ',$userIds).") and B.enabled='1' order by field (B.userID,".implode(', ',$userIds).")) $check_union";
			}
			if(in_array('6', $grade_arr))
			{
				$query .= "(Select B.userID,T.timeTaken,T.accuracy,T.totalQues from (select A.userID, sum(timeTaken)/60 timeTaken, round(sum(if(correct>0.5, 1, 0))/count(*)*100) accuracy, count(*) totalQues 
					from questionAttempt_class6 A ,questions C where A.qcode=C.qcode and C.qtype != 'openEnded' and A.userID in (".implode(', ',$userIds).") and date(A.attemptedDate) >= '".$startDate."' and date(A.attemptedDate) <= '".$endDate."' group by A.userID)T LEFT JOIN userDetails B ON T.userID=B.userID";
				if (in_array('7', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .= " where B.userID IN (".implode(', ',$userIds).") and B.enabled='1' order by field (B.userID,".implode(', ',$userIds).")) $check_union";
			}
			if(in_array('7', $grade_arr))
			{
				$query .= "(Select B.userID,T.timeTaken,T.accuracy,T.totalQues from (select A.userID, sum(timeTaken)/60 timeTaken, round(sum(if(correct>0.5, 1, 0))/count(*)*100) accuracy, count(*) totalQues 
					from questionAttempt_class7 A, questions C where A.qcode=C.qcode and C.qtype != 'openEnded' and A.userID in (".implode(', ',$userIds).") and date(A.attemptedDate) >= '".$startDate."' and date(A.attemptedDate) <= '".$endDate."' group by A.userID)T LEFT JOIN userDetails B ON T.userID=B.userID";
				if (in_array('8', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				elseif (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .= " where B.userID IN (".implode(', ',$userIds).") and B.enabled='1' order by field (B.userID,".implode(', ',$userIds).")) $check_union";
			}
			if(in_array('8', $grade_arr))
			{
				$query .= "(Select B.userID,T.timeTaken,T.accuracy,T.totalQues from (select A.userID, sum(timeTaken)/60 timeTaken, round(sum(if(correct>0.5, 1, 0))/count(*)*100) accuracy, count(*) totalQues 
					from questionAttempt_class8 A, questions C where A.qcode=C.qcode and C.qtype != 'openEnded' and A.userID in (".implode(', ',$userIds).") and date(A.attemptedDate) >= '".$startDate."' and date(A.attemptedDate) <= '".$endDate."' group by A.userID)T LEFT JOIN userDetails B ON T.userID=B.userID";
				$where5 ="and ud.childClass='8'";
				if (in_array('9', $grade_arr)) 
				{
					$check_union = 'union all';
				}
				else
				{
					$check_union = '';
				}
				$query .= " where B.userID IN (".implode(', ',$userIds).") and B.enabled='1' order by field (B.userID,".implode(', ',$userIds).")) $check_union";
			}
			if(in_array('9', $grade_arr))
			{
				$query .= "(Select B.userID,T.timeTaken,T.accuracy,T.totalQues from (select A.userID, sum(timeTaken)/60 timeTaken, round(sum(if(correct>0.5, 1, 0))/count(*)*100) accuracy, count(*) totalQues 
					from questionAttempt_class9 A, questions C where A.qcode=C.qcode and C.qtype != 'openEnded' and A.userID in (".implode(', ',$userIds).") and date(A.attemptedDate) >= '".$startDate."' and date(A.attemptedDate) <= '".$endDate."' group by A.userID)T LEFT JOIN userDetails B ON T.userID=B.userID";
				$query .= " where B.userID IN (".implode(', ',$userIds).") and B.enabled='1' order by field (B.userID,".implode(', ',$userIds)."))";
			}
			$resultData=$this->dbEnglish->query($query);
		}
		else
		{
			$questionAttempt="questionAttempt_class".$this->grade;	
			$query .= "(Select B.userID,T.timeTaken,T.accuracy,T.totalQues from (select A.userID, sum(timeTaken)/60 timeTaken, round(sum(if(correct>0.5, 1, 0))/count(*)*100) accuracy, count(*) totalQues 
					from $questionAttempt A, questions C where A.qcode=C.qcode and C.qtype != 'openEnded' and A.userID in (".implode(', ',$userIds).") and date(A.attemptedDate) >= '".$startDate."' and date(A.attemptedDate) <= '".$endDate."' group by A.userID)T LEFT JOIN userDetails B ON T.userID=B.userID";
			$query .= " where B.userID IN (".implode(', ',$userIds).") and B.enabled='1' order by field (B.userID,".implode(', ',$userIds)."))";
			$resultData=$this->dbEnglish->query($query);
		}
		$quesAttemptCountArr =  $resultData->result_array();
		return $quesAttemptCountArr;
	}

	function getTeacherMappedClassSec()
	{
		$curr_logged_in_userid = $this->user_id;
		$class_sec = array();
		$query = "SELECT CONCAT_WS('', childclass, childsection) AS teacher_mapped  from teacherClassMapping where userid = '".$curr_logged_in_userid."'";

		$getMappedSecClass = $this->dbEnglish->query($query);

		foreach ($getMappedSecClass ->result_array() as $key) 
		{
		 	array_push($class_sec, $key['teacher_mapped']);
		} 
		return $class_sec;

	}
	
	function getAdminMappedClassSec($schoolCode)
	{
		$class_sec = array();
		$query = "SELECT CONCAT_WS('', childclass, childsection) AS admin_mapped  from userDetails where schoolCode = '".$schoolCode."' and enabled=1 and category='Student'";
		//print $query; 
		$getMappedSecClass = $this->dbEnglish->query($query);

		foreach ($getMappedSecClass ->result_array() as $key) 
		{
		 	array_push($class_sec, $key['admin_mapped']);
		} 
		return $class_sec;

	}
	
	function get_teacher_admin_classes($reportMode){
		$grade_arr = array();
		if($reportMode=="ALL" && strtolower($this->session->userdata('category')) == "teacher")
		{
			$array = explode('~', $this->session->userdata('teacherClass'));
			foreach($array as &$value){	
				
				$value = explode(',', $value);
				$grade = $value['0'];
				array_push($grade_arr, $value['0']);
			}	
		}else if($reportMode=="ALL" && strtolower($this->session->userdata('category')) != "teacher")
		{
			$array=$this->teachermystudents_model->getAdminClassArr($this->session->userdata('schoolCode'));
			foreach($array as &$value){	
				
				$value = explode(',', $value);
				$grade = $value['0'];
				array_push($grade_arr, $value['0']);
			}
			//print_r($grade_arr);	
		}		
		return $grade_arr;
	}
}

?>