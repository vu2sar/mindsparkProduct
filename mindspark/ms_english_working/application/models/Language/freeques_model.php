<?php
	

Class Freeques_model extends MY_Model
{
	
	public function __construct() {
		 parent::__construct();
		 $this->dbEnglish = $this->load->database('mindspark_english',TRUE);
		 $this->load->library('session');
	}

	/**
	 * function description : check for stored session free question and set free questions in the session if not already set   
	 * param1   userID
	 * @return  none
	 * */
	
	public function setNextFreeQuesData($userID){
		if(!$this->session->userdata('sessionfreeQues'))
			$this->setNextSessionFreeQuestions($userID);
	}

	/**
	 * function description : Check for bunchID and get free questions according to limit
	 * param1   schoolBunchingOrder
	 * param2   userID
	 * param3   attemptedQuestionsinBunch
	 * param4   contentQuantity 
	 * */
	public function obtainBunchingQcodes($currentBunch,$contentQuantity,$qcodesArr){
	
		$freeQuesLevel = $this->session->userdata('freeQuesLevel');
		$userID = $this->session->userdata('userID');
		$groupSkillID = $this->session->userdata('groupSkillID');
		$endSkillDate = $this->session->userdata('endSkillDate');
		$childClass = $this->session->userdata('childClass');
		$refID = $this->session->userdata('refID');
		$qstnAtmptClss = TBL_QUESTION_ATTEMPT;
		$qstnAtmptClss = $qstnAtmptClss.$childClass;
		$todays_date = date("Y-m-d");
		$isGroupSkillActive = $this->session->userdata('isGroupSkillActive');
		//echo "currentBunch - $currentBunch \n";
		$attemptedQuestionsinBunch = $this->selectAttmptdQstnCurrentBnchQuery($currentBunch,$userID,$qstnAtmptClss);
		$attemptedQuestionsinBunch = array_map('current', $attemptedQuestionsinBunch);
		//echo "attempted questions are \n";
		//print_r($attemptedQuestionsinBunch);
		$this->dbEnglish->select('qcode');
		$this->dbEnglish->from("bunchMaster");
		$this->dbEnglish->where("bunchID",$currentBunch);
		$this->dbEnglish->where("childClass",$freeQuesLevel);
		if($isGroupSkillActive == 1){
			$this->dbEnglish->where("groupSkillID",$groupSkillID);
		}
		if(count($attemptedQuestionsinBunch)>0){ 
			$this->dbEnglish->where_not_in("qcode",$attemptedQuestionsinBunch);
		}
		$limitValue = $contentQuantity - count($qcodesArr);
		$this->dbEnglish->order_by('RAND()');
		$this->dbEnglish->limit($limitValue);
		$bunchingQcodes = $this->dbEnglish->get();
		$bunchingQcodesPresent = $bunchingQcodes->num_rows();
		$qcodesArr = array_merge($qcodesArr,$bunchingQcodes->result_array());
		//echo $this->dbEnglish->last_query()."\n";
		//print_r($qcodesArr);
		//check if qcodes are present  and check if qcodes == contentQuantity
		if($bunchingQcodesPresent > 0 && $bunchingQcodesPresent == $limitValue){
			//echo "exit";exit;
			$qcodesArr = $this->SplitArrayValue($qcodesArr);
			$bunchingFlowQcode = explode(",",$qcodesArr);
			$this->session->set_userdata('sessionfreeQues',$bunchingFlowQcode);
			$this->session->set_userdata('sessionTypeToShow',currFreeQuestionType);
		} else {
			$oldbunchID = $currentBunch;
			$nextBunchDetails = $this->nextBunchID($oldbunchID); //nextBunchID
			$currentBunch  = $nextBunchDetails['bunchID'];
			$isLastBunchID = $nextBunchDetails['isLastBunchID'];
			//not equal need to go to nextBunch and get qcodes
			if($isLastBunchID == 1){
				//echo "lastBunchID - $currentBunch \n";
				$qcodesArr = $this->SplitArrayValue($qcodesArr);
				$bunchingFlowQcode = explode(",",$qcodesArr);
					//group skill not active
				if($qcodesArr){
					$remainingfreeQues = $contentQuantity - count($bunchingFlowQcode);
					$this->session->set_userdata('remainingfreeQues',$remainingfreeQues);
					$this->session->set_userdata('sessionfreeQues',$bunchingFlowQcode);
				} else {
					$remainingfreeQues = $contentQuantity;
					$this->dbEnglish->Select("bunchCompleted");
					$this->dbEnglish->from("userContentAttemptDetails");
					$this->dbEnglish->where("userID",$userID);
					$this->dbEnglish->where("contentType",currFreeQuestionType);

					$bunchCompletedSql = $this->dbEnglish->get();
					$bunchCompleted = $bunchCompletedSql->row()->bunchCompleted;

					//obtain bunchCompleted for the currentBunch
					//check for bunchCompleted == 1 for the last bunch id
					//if true set nextBunchId as currentBunchID -> first 
					//if false whatever present bunchId in db that should be passed to exhaustion.
					$qcodesArr = array();
					$isFreeQuesContentExhaust = $this->session->userdata('isFreeQuesContentExhaust');
					$this->session->set_userdata('remainingfreeQues',$remainingfreeQues);
					if($isGroupSkillActive == 1){
						$this->dbEnglish->Select('bunchBeforeGroupSkillActivation');
						$this->dbEnglish->from('userCurrentStatus');
						$this->dbEnglish->where('userID', $userID);
						$bunchBeforeGroupSkillActivationSql = $this->dbEnglish->get();
						$currentBunchData = $bunchBeforeGroupSkillActivationSql->row();
						$currentBunch = $currentBunchData->bunchBeforeGroupSkillActivation;
					} elseif($isFreeQuesContentExhaust == '1') {
						$currentBunch = $this->session->userdata('currentBunch');
						$this->fetchFromContentExhaustionLogic($currentBunch,$remainingfreeQues,$qcodesArr);				 
					} else {
						$this->fetchFromContentExhaustionLogic($currentBunch,$remainingfreeQues,$qcodesArr);
					}									
				}																							
			} else {
				$this->obtainBunchingQcodes($currentBunch,$contentQuantity,$qcodesArr);
			}
		}
	}

	function getQueCodeBeforeGivingExhaustion($currentBunch,$userID){
	
		$this->dbEnglish->Select('DISTINCT(qcode) as questionCode');
		$this->dbEnglish->from($this->questionAttemptClassTbl.' a');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('passageID',0);
		$this->dbEnglish->where('bunchID',$currentBunch);
		$this->dbEnglish->order_by('a.attemptedDate , a.attemptCount');
		$this->dbEnglish->limit(1);
		$getQueCodeBeforeGivingExhaustionSql = $this->dbEnglish->get();
		$result = $getQueCodeBeforeGivingExhaustionSql->row_array();
		return $result['questionCode'];
	}

	/**
	 * function description : Obtain qcodes from groupskill exhaustion or from exhaustionlogic.
	 * param1   currentBunchID
	 * param2   remainingfreeQues
	 * param3   qcodesArr
	 * @return none
	 * */
	function fetchFromContentExhaustionLogic($currentBunchID,$remainingfreeQues,$qcodesArr){

		$freeQuesLevel = $this->session->userdata('freeQuesLevel');
		$userID = $this->session->userdata('userID');
		$groupSkillID = $this->session->userdata('groupSkillID');
		$endSkillDate = $this->session->userdata('endSkillDate');
		$childClass = $this->session->userdata('childClass');
		$qstnAtmptClss = TBL_QUESTION_ATTEMPT;
		$qstnAtmptClss = $qstnAtmptClss.$childClass;
		
		$todays_date = date("Y-m-d");
		$refID = $this->session->userdata('refID');
		//echo "currentBunchID - $currentBunchID \n";
		$this->session->set_userdata('isFreeQuesContentExhaust','1');
		$isFreeQuesContentExhaust = $this->session->userdata('isFreeQuesContentExhaust');
		$this->dbEnglish->set('isFreeQuesContentExhaust', $isFreeQuesContentExhaust);
		$this->dbEnglish->where('userID', $userID);
		$this->dbEnglish->update('userCurrentStatus');
		$limitValue = $remainingfreeQues - count($qcodesArr);
		$isGroupSkillActive = $this->session->userdata('isGroupSkillActive');
		
		if($isGroupSkillActive == 1){
			$attemptedQuestionsinBunch = $this->selectAttmptdQstnCurrentBnchQuery($currentBunchID,$userID,$qstnAtmptClss);
			$bunchingQcodes = $this->getfreeQueNormalQcodes($userID,$qstnAtmptClss,$currentBunchID,$limitValue,$freeQuesLevel,$attemptedQuestionsinBunch);
		} else {
			$bunchingQcodes = $this->getfreeQueMaxAttemptedQcodes($userID,$qstnAtmptClss,$currentBunchID,$limitValue);
		}
		//echo $this->dbEnglish->last_query()."\n";
		$bunchingQcodesPresent = $bunchingQcodes->num_rows();
		$bunchingQcodesResult = $bunchingQcodes->result_array();
		foreach ($bunchingQcodesResult as $key => $value){
			$qcodesArr[] = $value['qcode'];
		}
		//print_r($qcodesArr);
		
		//$qcodesArr = array_merge($qcodesArr,$bunchingQcodesResult);
		//print_r($qcodesArr);
		if($bunchingQcodesPresent > 0 && $bunchingQcodesPresent == $limitValue){
			//echo "done - got all";exit;
			$this->session->set_userdata('sessionfreeQues',$qcodesArr);
			$this->session->unset_userdata('remainingfreeQues');
		} elseif($isFreeQuesContentExhaust == '1' && $bunchingQcodesPresent == 0) {

			//echo "inside extra condition - $currentBunchID \n";
			$singleQuestion[] = $this->getQueCodeBeforeGivingExhaustion($currentBunchID,$userID);
			//echo "singleQuestion is \n";print_r($singleQuestion);
			$qcodesArr = array_merge($qcodesArr,$singleQuestion);
			//echo "merged final qcodeArray is \n";
			//print_r($qcodesArr);
			$remainingfreeQues = $limitValue - count($bunchingFlowQcode);
			//echo "remainingfreeQues - $remainingfreeQues";exit;
			$this->session->set_userdata('remainingfreeQues',$remainingfreeQues);
			$this->session->set_userdata('sessionfreeQues',$qcodesArr);
			//not equal need to go to nextBunch and get qcodes					
		} else {
			$nextBunchDetails = $this->nextBunchID($currentBunchID); //nextBunchID
			$currentBunchID  = $nextBunchDetails['bunchID'];
			$isLastBunchID = $nextBunchDetails['isLastBunchID'];
			if($isLastBunchID == 1){
				$this->session->set_userdata('isGroupSkillActive', 0);
			} 
			$this->fetchFromContentExhaustionLogic($currentBunchID,$remainingfreeQues,$qcodesArr);	
		}
	}

	/**
	 * function description : Obtain qcodes from groupskill exhaustion
	 * param1   userID
	 * param2   qstnAtmptClss
	 * param3   currentBunchID
	 * param3   limitValue
	 * param3   freeQuesLevel
	 * param3   attemptedQuestionsinBunch
	 * @return $this->dbEnglish->get();
	 * */

	function getfreeQueNormalQcodes($userID,$qstnAtmptClss,$currentBunchID,$limitValue,$freeQuesLevel,$attemptedQuestionsinBunch){
		
		$this->dbEnglish->select('qcode');
		$this->dbEnglish->from("bunchMaster");
		$this->dbEnglish->where("bunchID",$currentBunchID);
		$this->dbEnglish->where("childClass",$freeQuesLevel);
		if(count($attemptedQuestionsinBunch['questionsAttempted'])>0){ 
			$this->dbEnglish->where_not_in("qcode",$attemptedQuestionsinBunch['questionsAttempted']);
		}
		$this->dbEnglish->order_by('RAND()');
		$this->dbEnglish->limit($limitValue);
		return $this->dbEnglish->get();
	}
	/**
	 * function description : returns the maximum attempt count for a particular user in question attempt table
	 * param1 : userID
	 * param1 : qstnAtmptClss
	 * param1 : currentBunchID
	 * @return : returns the maximum attempt count
	 * */
	public function freeQuesMaxattemptCount($userID,$qstnAtmptClss,$currentBunchID) {
		$this->dbEnglish->select_max('attemptCount');
		$this->dbEnglish->from($qstnAtmptClss);
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('passageID',0);
		$this->dbEnglish->where('bunchID',$currentBunchID);
		$query = $this->dbEnglish->get();  		
		$freeQuesMaxattemptCount =  $query->row_array();
		$return_result = $freeQuesMaxattemptCount['attemptCount'];
		return $return_result;
	}

	/**
	 * function description : returns the maximum qcodes query for free questions to obtain qcodes
	 * param1 : userID
	 * param1 : qstnAtmptClss
	 * param1 : currentBunchID
	 * param1 : limitValue
	 * @return : returns the maximum attemptd free question query to obtain qcodes
	 * */
	public function getfreeQueMaxAttemptedQcodes($userID,$qstnAtmptClss,$currentBunchID,$limitValue) {
		$freeQuesMaxattemptCount        =  $this->freeQuesMaxattemptCount($userID,$qstnAtmptClss,$currentBunchID);	
		$subquery = "(SELECT if(($freeQuesMaxattemptCount)!=1,qcode,null) as qcode FROM $qstnAtmptClss
		WHERE attemptCount=($freeQuesMaxattemptCount) and userID=$userID and passageID=0 and bunchID=$currentBunchID) b";
		
		$this->dbEnglish->select("distinct(a.qcode) as qcode");
		$this->dbEnglish->from("$qstnAtmptClss a");
		$this->dbEnglish->join($subquery, 'a.qcode = b.qcode', 'left');
		$this->dbEnglish->where('a.userID',$userID);
		$this->dbEnglish->where('a.passageID',0);
		$this->dbEnglish->where('a.bunchID',$currentBunchID);
		$this->dbEnglish->where('b.qcode',NULL);
		$this->dbEnglish->order_by('a.attemptedDate , a.attemptCount');
		$this->dbEnglish->limit($limitValue);

		return $this->dbEnglish->get();
	}

	/**
	 * function description : Obtain nextBunchID as an array.
	 * param1   currentBunch
	 * @return bunchDetails
	 * */
	public function nextBunchID($currentBunch){
		
		$bunchDetails = array();
		$schoolBunchingOrder = $this->session->userdata('schoolBunchingOrder');
		$bunchDetails['isLastBunchID'] = 0;
		if($schoolBunchingOrder){
			$lastBunchKey = end(array_keys($schoolBunchingOrder));
			$currentBunchKey = array_search($currentBunch,$schoolBunchingOrder);
			if($currentBunchKey == $lastBunchKey){ //If end of array reached restart and obtain first element7
				$bunchDetails['bunchID'] = $schoolBunchingOrder[0];
				$bunchDetails['isLastBunchID'] = 1;
			} else {
				//obtain next element until end of line has reached
				$bunchDetails['bunchID'] = $schoolBunchingOrder[$currentBunchKey+1];
				if($schoolBunchingOrder[$currentBunchKey] == $schoolBunchingOrder[$lastBunchKey]){
					$bunchDetails['isLastBunchID'] = 1;
				}
			}
			return $bunchDetails;
		}
		return NULL;
	}

	/*
	function description : Obtain initial parameters and initiate question fetching from bunches.
	 * param1   userID
	*/ 
	public function setNextSessionFreeQuestions($userID){
		//Step 1: Obtain last or current bunching id from educatio_msenglish.questionAttempt_class using userID

		$childClass = $this->session->userdata('childClass');
		$schoolCode = $this->session->userdata('schoolCode');
		$groupSkillID = $this->session->userdata('groupSkillID');
		$endSkillDate = $this->session->userdata('endSkillDate');
		$todays_date = date("Y-m-d");

		$qcodesArr= array();
		
		$contentAttemptCount = $this->session->userdata('contentAttemptCount');
		$schoolBunchingOrder = $this->session->userdata('schoolBunchingOrder');
		
	
		$qstnAtmptClss = TBL_QUESTION_ATTEMPT;
		$qstnAtmptClss = $qstnAtmptClss.$childClass;
		
		//Check if any previously attempted bunchID is there -- contentAttemptLog.
		$contentQuantity = $this->session->userdata('contentQuantity') - $contentAttemptCount;
		$isCurrentBunchIDPresent = $this->isCurrentBunchIDPresent($userID,$schoolBunchingOrder);
		if(is_array($isCurrentBunchIDPresent) && $isCurrentBunchIDPresent['isCurrentBunchIDPresent'] == 0){
			//no history of attempt take first element of schoolBunchingOrder and start
			$currentBunch = $schoolBunchingOrder[0];
			$this->session->set_userdata('isValidRefID',0);	
		} else {
			$currentBunch = $isCurrentBunchIDPresent;
			$this->session->set_userdata('isValidRefID',1);
		}

		if($groupSkillID != 0 && strtotime($todays_date) <= strtotime($endSkillDate)){
			$this->session->set_userdata('isGroupSkillActive', 1);
			$this->dbEnglish->set('bunchBeforeGroupSkillActivation', $currentBunch);
			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->update('userCurrentStatus');
		} else {
			$this->session->set_userdata('isGroupSkillActive', 0);
		}

		$this->session->set_userdata('currentBunch',$currentBunch);

		//Obtain attempted Questions for the currentBunch - if empty will return none
		if($contentQuantity != 0){
			$this->obtainBunchingQcodes($currentBunch,$contentQuantity,$qcodesArr);
		}
		
	}

	/*
	function description : Obtain current bunchID from contentAttemptDetails if present or insert if not
	 * param1   userID
	 * param1   schoolBunchingOrder
	 * @return array or currentBunch
	*/ 
	public function isCurrentBunchIDPresent($userID,$schoolBunchingOrder){
		$contentType = currFreeQuestionType;
		$isCurrentBunchIDPresentSQL = "SELECT currentBunchID,bunchCompleted FROM userContentAttemptDetails WHERE userID=$userID AND contentType='$contentType'";
		$isCurrentBunchID=$this->dbEnglish->query($isCurrentBunchIDPresentSQL);
		$isCurrentBunchIDPresent=$isCurrentBunchID->num_rows();
		if($isCurrentBunchIDPresent > 0){
			//this means there is data and we have to resume with existing data
			$currentBunchID = $isCurrentBunchID->row()->currentBunchID;
			$bunchCompleted = $isCurrentBunchID->row()->bunchCompleted;
			if($bunchCompleted == 1){
				$currentBunchID = $this->nextBunchID($currentBunchID);
				return $currentBunchID;
			} else {
				return $currentBunchID;
			}
		} else {
			//this means there is no data and we have to insert data
			$data = array(
				'userID' => $userID,
				'totalAttempts' => 0,
				'contentType' => currFreeQuestionType,
				'currentBunchID' => $schoolBunchingOrder[0],
				'bunchCompleted' => 0
			);
			$this->dbEnglish->insert('userContentAttemptDetails', $data);
			$this->session->set_userdata('currentBunch',$schoolBunchingOrder[0]);
			return array('isCurrentBunchIDPresent' => 0, 'contentAttemptLog_insertSrNo' => $this->dbEnglish->insert_id());
		}
	}

	/**
	 * function description : To unset the isContentExhaust flag is all three flags are unset.
	 * @return  string , comma separated group skill IDs 
	 *
	 * */
	
	
	/* public function getGroupSkills($no){
		$this->dbEnglish->Select('skilID');
		$this->dbEnglish->from('groupSkillMaster');
		$this->dbEnglish->where('groupSkillID',$no);
		$getGroupSkillsSql = $this->dbEnglish->get();
		$groupSkillData = $getGroupSkillsSql->row();
		return $groupSkillData->skilID;
	} */
	
	/**
	 * function description : checks userLevelAndAccLog free question latest entry if it is not there then creates entry for it
	 * param1   userID
	 * param2   contentType
	 * @return  none;
	 * 
	 * */
	
	public function updateUserLevelAndAccFreeQuesLog($userID,$contentType){
		
		$level=$this->session->userdata('freeQuesLevel');
		
		$this->dbEnglish->Select('*');
		$this->dbEnglish->from('userLevelAndAccuracyLog');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('contentType',$contentType);
		$this->dbEnglish->order_by('scoringID','desc');
		$this->dbEnglish->limit(1);
		$getUserLevelAndAccLogSql = $this->dbEnglish->get();
		$result=  $getUserLevelAndAccLogSql->row_array();
		if($level==$result['level']):
			if($getUserLevelAndAccLogSql->num_rows() > 0){
				$userLevelAndAccData = $getUserLevelAndAccLogSql->row();
				$this->updateQuesPsgAttemptCount($userLevelAndAccData);	
			}
			else{
				$this->insertUserLevelData($userID,$contentType,$level);
				
			}
		else:
			$this->insertUserLevelData($userID,$contentType,$level);
		endif;			
	} 
	
	//greater than or equal to condition added for offline cases where count will be increased

	/**
	 * function description : update userLevelAndAccLog free question latest entry count calls accuracy calculation function if maximum count is reached
	 * param1   userLevelAndAccData , row data of current free question entry for the current user
	 * @return  none;
	 * 
	 * */
	
	public function updateQuesPsgAttemptCount($userLevelAndAccData){
		
		if($this->category == 'ADMIN' || $this->category == 'School Admin' || $this->category == 'TEACHER')//added by nivedita
		{
			$level = $this->session->userdata('freeQuesLevel'); //change passageLevel to freeques level

			$this->dbEnglish->Select('quesPsgAttemptCount');
			$this->dbEnglish->from('userLevelAndAccuracyLog');
			$this->dbEnglish->where('contentType', $userLevelAndAccData->contentType);
			$this->dbEnglish->where('userID', $userLevelAndAccData->userID);
			$this->dbEnglish->where('level', $level);
			$getFreeAttmpCountSql = $this->dbEnglish->get();
			$getFreeAttmpCountRow = $getFreeAttmpCountSql->row_array();

			$userLevelAndAccData->quesPsgAttemptCount = $getFreeAttmpCountRow['quesPsgAttemptCount']+1;
			$data=array('quesPsgAttemptCount'=> $userLevelAndAccData->quesPsgAttemptCount);

			$this->dbEnglish->where('level', $level);
			$this->dbEnglish->where('userID', $userLevelAndAccData->userID);
			$this->dbEnglish->where('contentType', $userLevelAndAccData->contentType);
		}
		else
		{
			$userLevelAndAccData->quesPsgAttemptCount=$userLevelAndAccData->quesPsgAttemptCount+1;
			$data=array('quesPsgAttemptCount'=> $userLevelAndAccData->quesPsgAttemptCount);

			$this->dbEnglish->where('scoringID', $userLevelAndAccData->scoringID);
			$this->dbEnglish->where('userID', $userLevelAndAccData->userID);
			$this->dbEnglish->where('contentType', $userLevelAndAccData->contentType);
		}
		$this->dbEnglish->update('userLevelAndAccuracyLog', $data);
	}

	/**
	 * function description : Checks accuracy for the passed scoringID data and calculates new level based on accuracy
	 * param1   userLevelAndAccData , row data of current free question entry for the current user
	 * @return  none;
	 * 
	 * */
		
	public function checkAccuracyAndChangeLevel($userLevelAndAccData)
	{
		$totalCorrects=0;		
		$this->dbEnglish->Select('p.qcode as qcode,p.correct as correct,q.accuracy as Accuracy,q.level as userLevel');
		$this->dbEnglish->from($this->questionAttemptClassTbl.' p');
		$this->dbEnglish->join('userLevelAndAccuracyLog q', 'p.scoringID=q.scoringID', 'inner');
		$this->dbEnglish->where('q.scoringID',$userLevelAndAccData->scoringID);
		$freeQuesDataSql = $this->dbEnglish->get();
		$freeQuesDataArr = $freeQuesDataSql->result_array();	
		
		foreach ($freeQuesDataArr as $row)
		{	
			$totalCorrects=$totalCorrects+$row['correct'];
		}	
		
		$accuracy=$totalCorrects/freeQuesChangeLevelCount*100;
		
		$data=array('accuracy'=> round($accuracy,2));
		$this->dbEnglish->where('scoringID', $userLevelAndAccData->scoringID);
		$this->dbEnglish->where('contentType', $userLevelAndAccData->contentType);
		$this->dbEnglish->update('userLevelAndAccuracyLog', $data);
		$userLevel=$freeQuesDataArr[0]['userLevel'];
		
		if($accuracy <= 40 && $userLevel!=4)
			$userLevel=$userLevel-freeQuesLevelConst;	
		else if($accuracy >= 80 && $userLevel!=9)
			$userLevel=$userLevel+freeQuesLevelConst;	
		
		
		$data = array(
				'userID' => $userLevelAndAccData->userID,
				'contentType' => $userLevelAndAccData->contentType,
				'accuracy' => 0,
				'level' => $userLevel
			);

		//print $userLevel;
		$this->dbEnglish->insert('userLevelAndAccuracyLog', $data);
		$data=array('freeQuesLevel'=>$userLevel);
		$this->dbEnglish->where('userID', $userLevelAndAccData->userID);
		$this->dbEnglish->update('userCurrentStatus', $data);
		$this->session->set_userdata($data);
		$this->session->unset_userdata('sessionfreeQues');
		$this->setNextSessionFreeQuestions($userLevelAndAccData->userID,true);

		$curLevel=$this->session->userdata('freeQuesLevel');
		$isFreeQuesExhausted=$this->calRemainingFreeQues($curLevel);
		if($isFreeQuesExhausted)
		{
			if($this->category == 'ADMIN' || $this->category == 'School Admin' || $this->category == 'TEACHER') //added by nivedita
			{
				$isContentExhaustedTeacher = array('isContentExhaustedTeacher'=>1);
				$this->session->set_userdata($isContentExhaustedTeacher);
				return;
			}
			else
			{
				$this->setExhaustionLogic($curLevel);			
				return;
			}
		}

	}
	
	/**
	 * function description : To get previously attempted qcode groupskillID details  
	 * param1   userID
	 * param2   selectedGroupID 
	 * @return  array , of which groupskillID qcodes was attempted previously and how many;
	 * 
	 * */
	
	function getPreviousAttemptedGroupID($userID,$selectedGroupID=null){
		$limit = "4";  // 3+1(To know the 3 questions skill and 4th questions skill)
		$attemptedGroupQns=0;
		if($selectedGroupID > 0)
			$limit ="8"; // 7+1(To know the 7 group skill and 8th group skill)
		$getPreviousAttGroupDetSql=$this->dbEnglish->query("select q.skillID,p.qcode from ".$this->questionAttemptClassTbl." p,questions q where p.userId=".$userID." and p.passageId=0 and p.qcode=q.qcode order by p.lastModified desc LIMIT ".$limit);
		if($getPreviousAttGroupDetSql->num_rows() > 0){
			$groupArr=array();
			foreach($getPreviousAttGroupDetSql->result_array() as $row){
				$skillIDArr=array();
				$skillIDArr=explode(',', $row['skillID']);
				$getGroupSkillIDSql=$this->dbEnglish->query("select groupSkillID,skilID from groupSkillMaster where find_in_set('".$skillIDArr[0]."',skilID) <> 0");
				$groupSkillIDData = $getGroupSkillIDSql->row();
				$groupSkills=$groupSkillIDData->skilID;
				$qcodeArr[$groupSkillIDData->groupSkillID]++;
				array_push($groupArr,$groupSkillIDData->groupSkillID);
			}
			
			if($selectedGroupID > 0){
				//$groupArr=array_reverse($groupArr);

				//arrayfilter() added by nivedita as this array was containing empty elements as well so it was getting stuck for freeQuestions
				$groupArr=array_reverse(array_filter($groupArr));
				$qcodeArr=array();
				$j=0;
				for($i=count($groupArr)-1;$i>=0;$i--)
				{
						if($j==0 || $j==1)
							$final[$groupArr[$i]] += 1;	
						if($groupArr[$i] != $groupArr[$i-1])
						{
							if($j==0)
								$j=1;
							else{
								$j=2;
							}
						}
				}
				
				foreach($final as $key=>$val)
				{
					$qcodeArr[$key]=$final[$key];
				}
			}else{
				$qcodeArr=array();
			}
			
			return $qcodeArr;
		}
	}
	
	/**
	 * function description : To get the latest userLevelAccLog free question content type attempt count  
	 * param1   userID
	 * @return  int , quesPsgAttemptCount value for the latest entry of the quesPsgAttemptCount.
	 * 
	 * */
	
	
	function getLogFreeQuesTotal($userID){
		$this->dbEnglish->Select('*');
		$this->dbEnglish->from('userLevelAndAccuracyLog');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('contentType',freeQuesContentTypeConst);
		$this->dbEnglish->order_by('scoringID','desc');
		$this->dbEnglish->limit(1);
		$getUserLevelAndAccLogSql = $this->dbEnglish->get();
		if($getUserLevelAndAccLogSql->num_rows() > 0){
			$userLevelAndAccData = $getUserLevelAndAccLogSql->row();
			return $userLevelAndAccData->quesPsgAttemptCount;
		}
		else
			return 0;
	}
	
	/**
	 * function description : To calculate the unattempted free questions on the passed free question level,check exhaustion condition and set exhaustion flag  
	 * param1   freeQuesLevel
	 * @return  boolean , true if in exhuastion and false if not.
	 * 
	 * */

	function calRemainingFreeQues($freeQuesLevel){
		$freeQuesMsLevel=$freeQuesLevel-3;
		$getCurrLevelFreeQuesCntSql=$this->dbEnglish->query('select count(*) as totalFreeQues from questions where passageID=0 and msLevel='.$freeQuesMsLevel.' and status=6 and skillID in (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23)');
		$currLevelFreeQuesCntData = $getCurrLevelFreeQuesCntSql->row();
		$currLevelFreeQuesCnt=$currLevelFreeQuesCntData->totalFreeQues;
	
		$attemptedFreeQuesArr=$this->questionspage_model->getPreviousAttemptedNCQQues($this->user_id,$freeQuesMsLevel);
		$attemptedFreeQuesCnt=count($attemptedFreeQuesArr);
		if($currLevelFreeQuesCnt-$attemptedFreeQuesCnt < 100){
			$data=array('isFreeQuesContentExhaust'=>1);
			$this->dbEnglish->where('userID', $this->user_id);
			$this->dbEnglish->update('userCurrentStatus', $data);
			$this->session->set_userdata($data);
			//print "AAA";
			return true;
		}
		else{
			
			return false;
		}
			
	}

	/**
	 * function description : checks userExhaustionlogiclog entry for the user and updates the count of attempt for free question  
	 * param1   userID
	 * param1   contentType
	 * @return  none.
	 * 
	 * */
	
	
	public function updateUserExhaustLevelAndAccLog($userID,$contentType){
		$level=$this->session->userdata('freeQuesLevel');
		$this->dbEnglish->Select('*');
		$this->dbEnglish->from('userexhaustionlogiclog');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('contentType',$contentType);
		$this->dbEnglish->order_by('exScoringID','desc');
		$this->dbEnglish->limit(1);
		$getUserExhaustionLogSql = $this->dbEnglish->get();
		if($getUserExhaustionLogSql->num_rows() > 0){
			$userLevelAndAccData = $getUserExhaustionLogSql->row();
			$this->updateFreeQuesAttemptCount($userLevelAndAccData);	
		}
		else{
			$this->calFreeQuesAccAndSetExhaustionLog($level);
		}			
	}

	//greater than or equal to condition added for offline cases where count will be increased

	/**
	 * function description : calculates free question exhuastion accuracy and change level and set content according to that 
	 * param1   userExhaustionData , user exhaustion free question row for the logged in user
	 * @return  none.
	 * 
	 * */
	
	public function calExhaustFreeQuesAccAndChangeLevel($userExhaustionData){
		$totalCorrects=0;		
		$this->dbEnglish->Select('p.qcode as qcode,p.correct as correct,q.accuracy as Accuracy,q.level as userLevel');
		$this->dbEnglish->from($this->questionAttemptClassTbl.' p');
		$this->dbEnglish->join('userexhaustionlogiclog q', 'p.exScoringID=q.exScoringID', 'inner');
		$this->dbEnglish->where('q.exScoringID',$userExhaustionData->exScoringID);
		$freeQuesDataSql = $this->dbEnglish->get();
		//print $this->dbEnglish->last_query();
		$freeQuesDataArr = $freeQuesDataSql->result_array();	
		//print_r($freeQuesDataArr);

		foreach ($freeQuesDataArr as $row)
		{	
			$totalCorrects=$totalCorrects+$row['correct'];
		}	
		
		$avgAccuracy=$totalCorrects/$userExhaustionData->psgsToShow*100;
		
		$data=array('accuracy'=> round($avgAccuracy,2));
		$this->dbEnglish->where('exScoringID', $userExhaustionData->exScoringID);
		$this->dbEnglish->where('contentType', $userExhaustionData->contentType);
		$this->dbEnglish->update('userexhaustionlogiclog', $data);
		$userLevel=$freeQuesDataArr[0]['userLevel'];
		
		if(($avgAccuracy <= 50 && $userLevel!=4) || ($avgAccuracy >= 50 && $userLevel==9))
			$userLevel=$userLevel-freeQuesLevelConst;	
		else if(($avgAccuracy >= 50 && $userLevel!=9) || ($avgAccuracy <= 50 && $userLevel==4))
			$userLevel=$userLevel+freeQuesLevelConst;
		
		$freeQuesMsLevel=$userLevel-3;
		$getCurrLevelFreeQuesCntSql=$this->dbEnglish->query('select count(*) as totalFreeQues from questions a LEFT JOIN '.$this->questionAttemptClassTbl.' b on a.qcode=b.qcode and b.userID='.$this->user_id.' and b.passageID=0 where a.passageID=0 and a.msLevel='.$freeQuesMsLevel.' and a.status=6 and a.skillID in (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23) and b.qcode is null ');
		$currLevelFreeQuesCntData = $getCurrLevelFreeQuesCntSql->row();
		$currLevelFreeQuesCnt=$currLevelFreeQuesCntData->totalFreeQues;

		//Shivam- If currlevel freequestion count is 0 then call function to get the newlevel so as to make the correct entry in the table.
		if ($currLevelFreeQuesCnt == 0){
			list($userLevel, $currLevelFreeQuesCnt) = $this->getmslevel($userExhaustionData->userID);
		}
		
		$data = array(
				'userID' => $userExhaustionData->userID,
				'contentType' => $userExhaustionData->contentType,
				'accuracy' => 0,
				'level' => $userLevel,
				'psgsToShow' => $currLevelFreeQuesCnt
			);
		$this->dbEnglish->insert('userexhaustionlogiclog', $data);
		$this->session->unset_userdata('sessionfreeQues');
		$this->setNextSessionFreeQuestions($userExhaustionData->userID,true,$userLevel);
	}

/**
	 * function description : This function will be called for Getting the newmslevel and no of 
	 						unattempted free questions which needs to be set in the userexhaustionlogiclog table.
	 * param1   userid
	 * @return  array.
	 * 
	 *
*/
	public function getmslevel($userID){

		$this->dbEnglish->Select('count(distinct(a.qcode)) as unattempt_question, a.mslevel as newmslevel ');
		$this->dbEnglish->from('questions a ');
		$this->dbEnglish->join($this->questionAttemptClassTbl." b", 'a.qcode = b.qcode and b.userID ='.$userID, 'left outer');
		$this->dbEnglish->where('a.status',6);
		$this->dbEnglish->where('a.passageID',0);
		$this->dbEnglish->where('b.qcode is null');
		$this->dbEnglish->group_by('newmslevel');
		$this->dbEnglish->order_by("unattempt_question desc");
		$this->dbEnglish->limit(1);

		$getmaxunattempt = $this->dbEnglish->get();

		$maxquesPending = 0;	
		$maxquesPending=$getmaxunattempt->num_rows();

		//Get the new ms level which we need to set in the userexhaustionlogic log.
		if($maxquesPending>0){

			$row = $getmaxunattempt->row();

			$newlevel = $row->newmslevel;
			$newpsgstoshow = $row->unattempt_question;

			//Change the newlevel as per our mindspark logic.
			$newlevel = $newlevel + 3;
			
		}
		else{
			//In worst scenereo if no row is present then give alert to user.
			echo '<script language="javascript">';
			echo 'alert("No free questions are available.")';
			echo '</script>';
			return;
		}

		//Return the array which contains the newlevel and no of unattempted questions to show.
		return array($newlevel, $newpsgstoshow);
		
	}

/*
	 * function description : This function will insert/update the userexhaustionlogiclog table for
	 						 the mslevel for which maximum no of questions are available to attempt.
	 * param1   userid
	 * @return  none.
	 * 
*/
	public function updateOrInsertAfterNoQuesleftinExhaustion($userID){

		//Get the new level and passages to show in the variable.
		list($newlevel, $psgtoshow) = $this->getmslevel($userID);

		$this->dbEnglish->select('exScoringID');
		$this->dbEnglish->from('userexhaustionlogiclog');
		$this->dbEnglish->where('psgsToShow',0);
		$this->dbEnglish->where('accuracy',0);
		$this->dbEnglish->where('contentType',"Free_Question");
		$this->dbEnglish->where('userid',$userID);
		$this->dbEnglish->order_by('exScoringID','desc');
		$this->dbEnglish->limit(1);
		$checkifexists = $this->dbEnglish->get();

		$row = $checkifexists->row();

		$rowcount = 0;	
		$rowcount=$checkifexists->num_rows();

		If($rowcount == 1){
			//If row is present then update the same row.
			$data=array('level'=> $newlevel,'psgsToShow'=>$psgtoshow );
			$this->dbEnglish->where('exScoringID',$row->exScoringID);
			$this->dbEnglish->update('userexhaustionlogiclog', $data);

		}else{

			//Insert row in the table if it is not present. 
			$data = array(
					'userID' => $userID,
					'contentType' => 'Free_Question',
					'accuracy' => 0,
					'level' => $newlevel,
					'psgsToShow' => $psgtoshow
				);
			$this->dbEnglish->insert('userexhaustionlogiclog', $data);

		}
		//Call the below function to set the free questions.
		$this->setNextSessionFreeQuestions($userID,true,$newlevel);

	}

	public function bunchingOrderFlow($bunchingjson,$childClass)
	{
		$currentOrder = "";
		$json = json_decode($bunchingjson);
		$data = $json->bunchids->class;
		foreach ($data as $key => $value) {
		if($key==$childClass) :
			$currentOrder = $value->orders;
		endif;
		}
		return $currentOrder;
  	}

	public function nextSchoolBunchingOrder($schoolCode,$childClass,$groupSkillID){
		$bunchsql = "select bunchOrder from schoolBunchingOrder where schoolCode = '$schoolCode'";
		$bunching_order = $this->dbEnglish->query($bunchsql)->row();
		if($bunching_order) : 
		$currentBunching = $this->bunchingorderFlow($bunching_order->bunchOrder,$childClass);
		endif;
		return $currentBunching;
	}

	public function selectAttmptdQstnCurrentBnchQuery($currentBunch,$userID,$qstnAtmptClss) {
		$selectAttmptdQstnCurrentBnch="select distinct (qcode) as questionsAttempted from $qstnAtmptClss qa where passageID=0 and userID=".$userID." AND bunchID=$currentBunch"; 
		return $this->dbEnglish->query($selectAttmptdQstnCurrentBnch)->result_array();
	}

	//function to get next bunch Id
	public function findNextBunchID($schoolBunchingOrder,$currentBunch,$userInfo) {        
		$schoolBunchingFlow = $this->nextSchoolBunchingOrder($userInfo['schoolCode'],$userInfo['childClass'],$userInfo['groupSkillID']);
		if($currentBunch!=0) :
			$arrLast = array_slice($schoolBunchingFlow, array_search($currentBunch,$schoolBunchingFlow)+1);
			$arrInit = array_slice($schoolBunchingFlow, 0,array_search($currentBunch,$schoolBunchingFlow)+1);
			$finalbunchFlowArr = implode($arrInit,",");
		else :
			$finalbunchFlowArr = $schoolBunchingFlow;
		endif;
		return $finalbunchFlowArr[0];
	}

	//function to conver array into string
	function SplitArrayValue($result){
		$values = array_map('array_pop', $result);
		return implode(',', $values);
	}

	//function to get Attempted Bunching Qcode
	public function getAttemptedBunchingQcode($userID,$newBunchingID,$currentAttempFlowCount,$qstnAtmptClss) {
		$nextFlowCount = $currentAttempFlowCount + 1;
		$SQL = "select qcode from $qstnAtmptClss where userID=$userID and passageID=0 and bunchID=$newBunchingID and attemptCount =$nextFlowCount ";
		return  $this->dbEnglish->query($SQL)->result();  
	}

		/**
	 * function description : updates userLevelAndAccLog table after the passage attempt 
	 * param1 : userID 
	 * param2 : contentType = Content type for which attempted count to check
	 * param3 : passageID = Attempted passage after which userLevelAndAccLog need to be updated
	 * @return : none.  
	 * */

	public function insertUserLevelData( $userID,$contentType,$level){
		$data = array(
			'userID' => $userID,
			'contentType' => $contentType,
			'quesPsgAttemptCount' => 1,
			'accuracy' => 0,
			'level' => $level
		);

		$this->dbEnglish->insert('userLevelAndAccuracyLog', $data);

	}
	
}
