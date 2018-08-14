<?php
Class teacherhome_model extends MY_Model
{
	public $schoolCode;
	public $startDate; 
	public $endDate;
	public $class;
	public $section;
	public $studentsArr;
	public $studentsArrIds;
	public $usageArr;

	public function __construct() 
	{
		parent::__construct();
		$this->dbEnglish = $this->load->database('mindspark_english',TRUE);
		$this->load->library('session');
		//$this->output->enable_profiler(TRUE);
		$this->Companies_db = $this->dbEnglish;

	    // Pass reference of database to the CI-instance
	    $CI =& get_instance();
	    $CI->Companies_db =& $this->Companies_db; 

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
	 	$overallUsageSummary = array();

		$this->studentsArr = $this->getStudentDetailsBySection($this->schoolCode,$this->class,$this->section);
	
		$this->studentsArrIds = $this->array_column($this->studentsArr, 'userID');
		$this->usageArr = $this->getUsageArrayForSection();
		if(count($this->studentsArr)==0){
			$overallUsageSummary['noActiveUsersFound']=1; 
			return ($overallUsageSummary);
		}
		uasort($this->usageArr,array("teacherhome_model", "sortByUsageHelper"));

		$overallUsageSummary['usageSummaryGraphDetails'] = $this->getUsageSummaryForSection();

		
		$overallUsageSummary['lowUsageNamesAndTimespent'] = $this->getLowUsageNamesAndTimespent();
		$overallUsageSummary['averageUsageNamesAndTimespent'] = $this->getAverageUsageNamesAndTimespent();
		$overallUsageSummary['greatUsageNamesAndTimespent'] = $this->getGreatUsageNamesAndTimespent();
		$overallUsageSummary['lowAccuracyNames'] = $this->getLowAccuracyNames();
		
		$overallUsageSummary['readingPsgDetails']= $this->getReadingPassageDetailsAndAccuracy();
		
		$overallUsageSummary['listeningPsgDetails']= $this->getListeningPassageDetailsAndAccuracy();
		
		$overallUsageSummary['grammarQuesDetails']= $this->getGrammarDetailsAndAccuracy();

		$overallUsageSummary['VocabQuesDetails']= $this->getVocabDetailsAndAccuracy();
		
		$overallUsageSummary['zeroUsageNamesAndTimespent'] = $this->getZeroUsageNamesAndTimespent();
		return $overallUsageSummary;
	}	

    function getUsageSummaryForSection() {	


		$usageArr=$this->usageArr;
	  	$startDate=$this->startDate ; 
	 	$endDate=$this->endDate;

		$cumulativeTime = 0;
		$zeroVal=0;
		$lowVal=0;
		$averageVal=0;
		$greatVal=0;
		
		foreach ($usageArr as $userDetails) {

			$cumulativeTime += $userDetails['timeSpent']; 
			switch ($userDetails['usage']) {
				case 'zero':
				$zeroVal++;				
				break;
				case 'low':
				$lowVal++;
				break;
				case 'average':
				$averageVal++;				
				break;
				case 'great':
				$greatVal++;				
				break;				
				default:
				break;
			}
		}
		
		$usageSummary['zero']=$zeroVal;		
		$usageSummary['low']=$lowVal;		
		$usageSummary['average']=$averageVal;		
		$usageSummary['great']=$greatVal;		
		$classAvg = $cumulativeTime/sizeof($usageArr);
		$classAvgCategory =$this->categorizeUsage($classAvg);
		$usageSummary['classAvg'] = $classAvgCategory;
		$usageSummary['totalStudents'] = count($usageArr);

		return $usageSummary;	
	}
	function getZeroUsageNamesAndTimespent() {
		$usageArr=$this->usageArr;
		$zeroUsageNames = array();
		foreach ($usageArr as $userDetails) {
			if($userDetails['timeSpent'] == 0) {
				$tmpArr['name'] = $userDetails['name'];
				$tmpArr['timeSpent'] = "0";
				$zeroUsageNames[] = $tmpArr;
			}
		}
		return $zeroUsageNames;
	}
	
	function sortByUsageHelper($a, $b) {
		if($a['usage'] < $b['usage']) {
			return 1;
		} elseif($a['usage'] > $b['usage']) {
			return -1;
		}
		return 0;
	}	



	function getUsageArrayForSection() {
		$startDate=$this->startDate ; 
	 	$endDate=$this->endDate;
	 	$class=$this->class;
		$usageArr = array();
		$userArray=$this->studentsArr;
		foreach ($userArray as $userDetails) {
			$timeSpent = $this->getTimeSpentOfUser($userDetails['userID'],$startDate,$endDate,$this->class);
			$userDetails['timeSpent'] = $timeSpent;
			$userDetails['usage'] = $this->categorizeUsage($timeSpent);
			$userDetails['accuracy'] = $this->getAccuracyForStudent($userDetails['userID']);
			$usageArr[] = $userDetails;
		}
		return $usageArr;

	}
	function getAccuracyForStudent($userID) {

		$startDate=$this->startDate ; 
	 	$endDate=$this->endDate;
	 	$class=$this->class;
	 	$tbl_quesAttempt=$this->questionAttemptTblName.$class;	
		$this->dbEnglish->Select('count(srno) AS total, sum(if(correct=1,1,0)) as correct, round(sum(timeTaken)/60) as timeSpent', false);
		$this->dbEnglish->from($tbl_quesAttempt);
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('DATE(attemptedDate) >=',$this->getIntDate($startDate));
		$this->dbEnglish->where('DATE(attemptedDate) <=',$this->getIntDate($endDate));
		$queryAcc = $this->dbEnglish->get();
		$dataAcc = $queryAcc->result_array();		
		$attempted = $dataAcc[0]['total'];
		$correct = $dataAcc[0]['correct'];
		$accuracy = ($attempted > 0)? round($correct*100/$attempted, 1) : 0;
		return $accuracy;	
	}

	function getReadingPassageDetailsAndAccuracy() {
		$startDate=$this->startDate ; 
	 	$endDate=$this->endDate;
	 	$class=$this->class;
	 	$studentsArr=$this->studentsArrIds;
	 	$tbl_quesAttempt=$this->questionAttemptTblName.$class;	

		$this->dbEnglish->Select('count(srno) AS totalQuesAttempted,count(DISTINCT qa.passageID) as passageAttemptCount ,sum(if(correct=1,1,0)) as correct,passageType', false);
	 	$this->dbEnglish->from($tbl_quesAttempt." qa");
	 	$this->dbEnglish->join('passageMaster pm','qa.passageID = pm.passageID','INNER');
	 	$this->dbEnglish->where('pm.passageType !=','conversation');
	 	$this->dbEnglish->where_in('qa.userID',$studentsArr);
	 	$this->dbEnglish->where('DATE(qa.attemptedDate) >=',$this->getIntDate($startDate));
	 	$this->dbEnglish->where('DATE(qa.attemptedDate) <=',$this->getIntDate($endDate));
	 	$this->dbEnglish->group_by("qa.userID");

		$query = $this->dbEnglish->get();
		$userDataArr = $query->result_array();

		$totalQuesAttempted=0;
		$passageAttemptCount=0;
		$correct=0;
		foreach($userDataArr as $key=>$valueArr)
		{		
			$totalQuesAttempted += $valueArr['totalQuesAttempted'];
			$passageAttemptCount += $valueArr['passageAttemptCount'];
			if($valueArr['correct']==NULL || $valueArr['correct']==''){
				$valueArr['correct']=0;
			}
			$correct+=$valueArr['correct'];
		}
		
		$readingPsgDetails['totalPassageQuesAttempted'] = $totalQuesAttempted;
		$readingPsgDetails['totalPassageRead'] = $passageAttemptCount;
		$readingPsgDetails['totalCorrect'] = $correct;
		$accuracy = ($totalQuesAttempted > 0)? round($correct*100/$totalQuesAttempted, 1) : 0;
		$readingPsgDetails['accuracy'] = $accuracy ;
		
		return $readingPsgDetails;			


	}


	function getListeningPassageDetailsAndAccuracy() {
		$startDate=$this->startDate ; 
	 	$endDate=$this->endDate;
	 	$class=$this->class;
	 	$studentsArr=$this->studentsArrIds;
	 	$tbl_quesAttempt=$this->questionAttemptTblName.$class;

	 	$this->dbEnglish->Select('count(srno) AS totalQuesAttempted,count(DISTINCT qa.passageID) as passageAttemptCount ,sum(if(correct=1,1,0)) as correct,passageType', false);
	 	$this->dbEnglish->from($tbl_quesAttempt." qa");
	 	$this->dbEnglish->join('passageMaster pm','qa.passageID = pm.passageID','INNER');
	 	$this->dbEnglish->where('pm.passageType','conversation');
	 	$this->dbEnglish->where_in('qa.userID',$studentsArr);
	 	$this->dbEnglish->where('DATE(qa.attemptedDate) >=',$this->getIntDate($startDate));
	 	$this->dbEnglish->where('DATE(qa.attemptedDate) <=',$this->getIntDate($endDate));
	 	$this->dbEnglish->group_by("qa.userID");

		$query = $this->dbEnglish->get();
		$userDataArr = $query->result_array();

		$totalQuesAttempted=0;
		$passageAttemptCount=0;
		$correct=0;
		foreach($userDataArr as $key=>$valueArr)
		{		
			$totalQuesAttempted += $valueArr['totalQuesAttempted'];
			$passageAttemptCount += $valueArr['passageAttemptCount'];
			if($valueArr['correct']==NULL || $valueArr['correct']==''){
				$valueArr['correct']=0;
			}
			$correct+=$valueArr['correct'];
		}
		
		$listeningPsgDetails['totalPassageQuesAttempted'] = $totalQuesAttempted;
		$listeningPsgDetails['totalPassageRead'] = $passageAttemptCount;
		$listeningPsgDetails['totalCorrect'] = $correct;
		$accuracy = ($totalQuesAttempted > 0)? round($correct*100/$totalQuesAttempted, 1) : 0;
		

		$listeningPsgDetails['accuracy'] = $accuracy ;

		return $listeningPsgDetails;	

	}

	function getGrammarDetailsAndAccuracy() {
		
		$startDate=$this->startDate ; 
	 	$endDate=$this->endDate;
	 	$class=$this->class;
	 	$studentsArr=$this->studentsArrIds;
		$tbl_quesAttempt=$this->questionAttemptTblName.$class;

		/*Currently concepts covered logic not required 
		$queryGroupSkill= "SELECT skilID from groupSkillMaster";
		$resultGroupSkill = mysql_query($queryGroupSkill) or die (mysql_error());
		$grammarConceptsCovered=0;
		while($line=mysql_fetch_array($resultGroupSkill))
		{
			
			$skillIDs=$line['skilID'];
			$queryConcept = "SELECT count(srno) AS totalQuesAttempted from ".TBL_QUES_ATTEMPT."_class$class qa , questions qs where qa.qcode=qs.qcode AND skillID in (".$skillIDs .") AND qs.passageID=0 AND qa.userID IN (". implodeArrayForQuery($studentsArr).")         AND  qa.attemptedDate >= ".getIntDate($startDate)." AND qa.attemptedDate <= ".getIntDate($endDate);

			$resultConcept = mysql_query($queryConcept) or die (mysql_error());
			$row = mysql_fetch_assoc($resultConcept);
			if($row["totalQuesAttempted"]>30){
				$grammarConceptsCovered++;
			}
		}	*/

		$this->dbEnglish->Select('count(srno) AS totalQuesAttempted, sum(if(correct=1,1,0)) as correct', false);
	 	$this->dbEnglish->from($tbl_quesAttempt." qa");
	 	$this->dbEnglish->join('questions qs','qa.qcode = qs.qcode','INNER');
	 	$this->dbEnglish->where('qs.passageID',0);
	 	$this->dbEnglish->where('qs.topicID',1);
	 	$this->dbEnglish->where_in('qa.userID',$studentsArr);
	 	$this->dbEnglish->where('DATE(qa.attemptedDate) >=',$this->getIntDate($startDate));
	 	$this->dbEnglish->where('DATE(qa.attemptedDate) <=',$this->getIntDate($endDate));

		$query = $this->dbEnglish->get();
		$userDataArr = $query->result_array();
		if($userDataArr[0]['correct']==NULL || $userDataArr[0]['correct']==''){
			$userDataArr[0]['correct']=0;
		}
		$grammarQuesDetails['totalPassageQuesAttempted'] = $userDataArr[0]['totalQuesAttempted'];
		$grammarQuesDetails['totalCorrect'] = $userDataArr[0]['correct'];
		$accuracy = ($userDataArr[0]['totalQuesAttempted'] > 0)? round($userDataArr[0]['correct']*100/$userDataArr[0]['totalQuesAttempted'], 1) : 0;
		$grammarQuesDetails['accuracy'] = $accuracy ;

		return $grammarQuesDetails;	
		

	}


	function getVocabDetailsAndAccuracy() {
		
		$startDate=$this->startDate ; 
	 	$endDate=$this->endDate;
	 	$class=$this->class;
	 	$studentsArr=$this->studentsArrIds;
		$tbl_quesAttempt=$this->questionAttemptTblName.$class;

		$this->dbEnglish->Select('count(srno) AS totalQuesAttempted, sum(if(correct=1,1,0)) as correct', false);
	 	$this->dbEnglish->from($tbl_quesAttempt." qa");
	 	$this->dbEnglish->join('questions qs','qa.qcode = qs.qcode','INNER');
	 	$this->dbEnglish->where('qs.passageID',0);
	 	$this->dbEnglish->where('qs.topicID',2);
	 	$this->dbEnglish->where_in('qa.userID',$studentsArr);
	 	$this->dbEnglish->where('DATE(qa.attemptedDate) >=',$this->getIntDate($startDate));
	 	$this->dbEnglish->where('DATE(qa.attemptedDate) <=',$this->getIntDate($endDate));

		$query = $this->dbEnglish->get();
		$userDataArr = $query->result_array();
		if($userDataArr[0]['correct']==NULL || $userDataArr[0]['correct']==''){
			$userDataArr[0]['correct']=0;
		}
		$vocabQuesDetails['totalPassageQuesAttempted'] = $userDataArr[0]['totalQuesAttempted'];
		$vocabQuesDetails['totalCorrect'] = $userDataArr[0]['correct'];
		$accuracy = ($userDataArr[0]['totalQuesAttempted'] > 0)? round($userDataArr[0]['correct']*100/$userDataArr[0]['totalQuesAttempted'], 1) : 0;
		$vocabQuesDetails['accuracy'] = $accuracy ;

		
		
		return $vocabQuesDetails;	

	}
	function categorizeUsage($timeSpentInSecs) {
		$category = '';
		$startDate=$this->startDate ; 
	 	$endDate=$this->endDate;

		$datediff = strtotime($endDate) - strtotime($startDate);
		$numDays = floor($datediff/(60*60*24)) + 1;
		$avgTimeSpentPerDay = $timeSpentInSecs/$numDays;

		if($avgTimeSpentPerDay == 0) {
			$category = 'zero';
		} elseif($avgTimeSpentPerDay < 129) {
			$category = 'low';
		} elseif ($avgTimeSpentPerDay>=129 && $avgTimeSpentPerDay<386) {
			$category = 'average';
		} elseif ($avgTimeSpentPerDay>=386) {
			$category = 'great';
		} 
		return $category;
	}

	function getLowUsageNamesAndTimespent() {
		$usageArr=$this->usageArr;
		$lowUsageNamesAndTimespent = array();
		usort($usageArr,array("teacherhome_model","cmp_timeSpent"));

		foreach ($usageArr as $userDetails) {
			if(strcmp($userDetails['usage'], 'low') ==0) {
				$tmpArr['name'] = $userDetails['name'];
				$tmpArr['timeSpent'] = $userDetails['timeSpent'];
				$lowUsageNamesAndTimespent[] = $tmpArr;
			}
		}
		return $lowUsageNamesAndTimespent;
	}

	function getAverageUsageNamesAndTimespent() {
		$usageArr=$this->usageArr;
		$averageUsageNamesAndTimespent = array();
		usort($usageArr, array("teacherhome_model","cmp_timeSpent"));
		foreach ($usageArr as $userDetails) {
			if(strcmp($userDetails['usage'], 'average') ==0) {
				//$averageUsageNamesAndTimespent[] = $userDetails['name'];
				$tmpArr['name'] = $userDetails['name'];
				$tmpArr['timeSpent'] = $userDetails['timeSpent'];
				$averageUsageNamesAndTimespent[] = $tmpArr;
			}
		}
		return $averageUsageNamesAndTimespent;
	}

	function getGreatUsageNamesAndTimespent() {
		$usageArr=$this->usageArr;		
		$greatUsageNamesAndTimespent = array();
		usort($usageArr, array("teacherhome_model", "cmp_timeSpent"));
		foreach ($usageArr as $userDetails) {
			if(strcmp($userDetails['usage'], 'great') ==0) {
				//$greatUsageNamesAndTimespent[] = $userDetails['name'];
				$tmpArr['name'] = $userDetails['name'];
				$tmpArr['timeSpent'] = $userDetails['timeSpent'];
				$greatUsageNamesAndTimespent[] = $tmpArr;
			}
		}
		return $greatUsageNamesAndTimespent;
	}
	function getLowAccuracyNames() {
		$usageArr=$this->usageArr;
		$lowAccuracyNames = array();
		usort($usageArr, array("teacherhome_model","cmp_accuracy"));
		foreach ($usageArr as $userDetails) {
			if($userDetails['timeSpent']>0 && $userDetails['accuracy']<20) {
				$lowAccuracyNames[] = $userDetails['name'];
			}
		}
		return $lowAccuracyNames;
	}
	function cmp_timeSpent($a, $b) {
		if($a['timeSpent'] == $b['timeSpent']) {		
			return 0;
		}
		return ($a['timeSpent'] < $b['timeSpent'])? -1 : 1;
	}
	function cmp_accuracy($a, $b) {
		if($a['accuracy'] == $b['accuracy']) {		
			return 0;
		}
		return ($a['accuracy'] < $b['accuracy'])? -1 : 1;
	}	
	
	function getAccuracyDetails($data)
	{
		$responseArr=array();
		$responseArr['acc_bucket']['veryLow']=0; // <= 25
		$responseArr['acc_bucket']['low']=0; // >25 to <=50
		$responseArr['acc_bucket']['average']=0; // > 50 to <=75
		$responseArr['acc_bucket']['high']=0; // >76

		$responseArr['note']['high']='';
		$responseArr['note']['very_low']='';
		$responseArr['note']['not_loggedin']='';

		$totalStudents=0;

		//	questionAttempt table as per class
		$questionAttempt="questionAttempt_class".$data['class'];
		$query="SELECT
						user.userID,
						max(DATE(s.startTime)) as max_date,
						user.childName,
						count(qa.srno) as totalAttempted,
						round((sum(correct) / count(qa.srno)) * 100) as studentAcc
				FROM
						userDetails as user
				LEFT JOIN
						$questionAttempt as qa
				ON
						user.userID=qa.userID
				AND
						DATE(qa.attemptedDate) >= '".$data['start_date']."'
				AND
						DATE(qa.attemptedDate) <= '".$data['end_date']."'
				LEFT JOIN
						questions as q
				ON
						qa.qcode=q.qcode
				AND
						q.qtype!='openEnded'
				LEFT JOIN 
						sessionStatus s on s.userID = user.userID
				WHERE
						user.schoolcode=".$data['school_code']."
				AND
						user.childClass=".$data['class']."
				AND
                        user.enabled = '1'
				AND
						user.childSection='".$data['section']."'
				AND
						user.category='Student'
				GROUP BY
						user.userID
				ORDER BY
						studentAcc DESC";
		$resultData=$this->dbEnglish->query($query);
		foreach ($resultData->result_array() as $key => $value) {
			//if($value['totalAttempted']==0){
			if($value['totalAttempted']==0 && ($value['max_date'] < $data['start_date']) || ($value['max_date'] > $data['end_date'])){
				if($responseArr['note']['not_loggedin']=="")
					$responseArr['note']['not_loggedin']=$value['childName'];
				else
					$responseArr['note']['not_loggedin'] .= ", ".$value['childName'];
			}else{
				$totalStudents++;
				if($value['studentAcc'] <= 25){
					$responseArr['acc_bucket']['veryLow']++;
					if ($value['studentAcc'] < 20){
						if($responseArr['note']['very_low']=="")
							$responseArr['note']['very_low']=$value['childName'];
						else
							$responseArr['note']['very_low'] .= ", ".$value['childName'];
					}
				}
				else if($value['studentAcc'] > 25 &&  $value['studentAcc'] <= 50){
					$responseArr['acc_bucket']['low']++;
				}
				else if($value['studentAcc'] > 50 && $value['studentAcc'] <= 75){
					$responseArr['acc_bucket']['average']++;
				}
				else if($value['studentAcc'] >75){
					$responseArr['acc_bucket']['high']++;
					if($value['studentAcc'] > 90){
						if($responseArr['note']['high']=="")
							$responseArr['note']['high']=$value['childName'];
						else
							$responseArr['note']['high'].= ", ".$value['childName'];
					}
				}
			}
		}
		//	calculate percentage
		$totalPer=0; // with round check last value not > 100
		foreach ($responseArr['acc_bucket'] as $key => $value) {
			if($totalStudents > 0){
				$prev=$totalPer;
				$totalPer+=round(($value / $totalStudents) * 100,0);
				if($totalPer > 100)
					$responseArr['acc_bucket'][$key]=100 - $prev;
				else
					$responseArr['acc_bucket'][$key]=round(($value / $totalStudents) * 100,0);
			}
		}
		$responseArr['is_graph_display']=array_sum($responseArr['acc_bucket']) > 0 ? 1 : 0;
		return $responseArr;
	}

	function getSkillwiseAccuracyDetails($data){
		$responseArr=array();
		$responseArr['skill_acc']['points']=array(0,10,20,30,40,50,60,70,80,90,100);
		$responseArr['skill_usage']['points']=array(0,10,20,30,40,50,60,70,80,90,100);

		$maxmintees=0;
		//	questionAttempt table as per class
		$questionAttempt="questionAttempt_class".$data['class'];
		$skillUsageArr=array();
		$skillAccArr=array();

		//	Get Data for TOPic
		$query="SELECT
						topic.name,
						count(qa.srno) as totalAttempted,
						round((sum(correct) / count(qa.srno)) * 100) as studentAcc,
						(sum(timeTaken) / 60) as time_min
				FROM
						userDetails as user
				JOIN
						$questionAttempt as qa
				ON
						user.userID=qa.userID
				AND
						DATE(qa.attemptedDate) >= '".$data['start_date']."'
				AND
						DATE(qa.attemptedDate) <= '".$data['end_date']."'
				JOIN
						questions as q
				ON
						qa.qcode=q.qcode
				AND
						q.qtype!='openEnded'
				JOIN
						topicMaster as topic
				ON
						q.topicID=topic.topicID
				WHERE
						user.schoolcode=".$data['school_code']."
				AND
						user.childClass=".$data['class']."
				AND
						user.childSection='".$data['section']."'
				AND
						user.category='Student'
				AND
                        user.enabled = '1'
				AND 
						questionType!='passageQues'
				GROUP BY
						topic.name
				ORDER BY
						topic.name";
		$resultData=$this->dbEnglish->query($query);
		foreach ($resultData->result_array() as $key => $value) {
			$skillUsageArr[$value['name']]=round($value['time_min']);
			$skillAccArr[$value['name']]=round($value['studentAcc']);
			if($maxmintees < $skillUsageArr[$value['name']])
				$maxmintees = $skillUsageArr[$value['name']];
		}

		//	GET data for Passages
		$query="SELECT
					(case 
					 when (passageTypeName='Conversation')
					 THEN  'Listening'
					 ELSE  'Reading'
					 END) as name,
					count(qa.srno) as totalAttempted,
					round((sum(correct) / count(qa.srno)) * 100) as studentAcc,
					(sum(timeTaken) / 60) as time_min
			FROM
					userDetails as user
			JOIN
					$questionAttempt as qa
			ON
					user.userID=qa.userID
			AND
					DATE(qa.attemptedDate) >= '".$data['start_date']."'
			AND
					DATE(qa.attemptedDate) <= '".$data['end_date']."'
			JOIN
					questions as q
			ON
					qa.qcode=q.qcode
			AND
					q.qtype!='openEnded'
			JOIN
					topicMaster as topic
			ON
					q.topicID=topic.topicID
			WHERE
					user.schoolcode=".$data['school_code']."
			AND
					user.childClass=".$data['class']."
			AND
					user.childSection='".$data['section']."'
			AND
					user.category='Student'
			AND
                    user.enabled = '1'
			AND 
					questionType='passageQues'
			GROUP BY
					(case 
					 when (passageTypeName='Conversation')
					 THEN  'Listening'
					 ELSE  'Reading'
					 END)
			ORDER BY
					name";
		$resultData=$this->dbEnglish->query($query);
		foreach ($resultData->result_array() as $key => $value) {
			$skillUsageArr[$value['name']]=round($value['time_min']);
			$skillAccArr[$value['name']]=round($value['studentAcc']);
			if($maxmintees < $skillUsageArr[$value['name']])
				$maxmintees = $skillUsageArr[$value['name']];
		}
		$responseArr['is_graph_display']=$maxmintees > 0 ? 1 : 0;

		//	rearrange in order
		$newOrder=array("Reading","Listening","Vocabulary","Grammar");
		foreach ($newOrder as $key => $level) {
			if(isset($skillUsageArr[$level]))
				$responseArr['skill_usage']['data'][$level]=$skillUsageArr[$level];
			if(isset($skillAccArr[$level]))
				$responseArr['skill_acc']['data'][$level]=$skillAccArr[$level];

		}
		// dynamically change the points as per max value and convert all points under 100 based on maxvalue. 
		/*$multiplayVal=ceil($maxmintees/100);
		foreach ($responseArr['skill_usage']['points'] as $key => $value) {
			$responseArr['skill_usage']['points'][$key]=$value * $multiplayVal;
		}
		foreach ($responseArr['skill_usage']['data'] as $key => $value) {
			$responseArr['skill_usage']['data'][$key]=(($value * 100) / (100 * $multiplayVal));
		}*/

		return $responseArr;
	}
	function getTeacherSettingPageData($data){
		$resultArr['session_length']=60;
		$resultArr['ground_enable_after']=30;

		$query="SELECT 
						min(timeAllowedPerDay) as session_length,
						min(minTimeForClass)  as ground_enable_after
				FROM 
						userDetails
				WHERE
						schoolcode=".$data['school_code']."
				AND
						childClass=".$data['class']."
				AND
						childSection='".$data['section']."'";
		$result=$this->dbEnglish->query($query);
		foreach ($result->result_array() as $key => $value) {
			$resultArr['session_length']=$value['session_length'];
			$resultArr['ground_enable_after']=$value['ground_enable_after'];;
		}
		return $resultArr;
	}
	function setTeacherGenSettings($data){
		$query="UPDATE 
					userDetails
				SET
					timeAllowedPerDay='".$data['session_length']."',
					minTimeForClass='".$data['ground_enable_after']."'
				WHERE
						schoolcode=".$data['school_code']."
				AND
						childClass=".$data['class']."
				AND
						category='STUDENT'
				AND
						childSection='".$data['section']."'";
		$this->dbEnglish->query($query);
	}
}

?>