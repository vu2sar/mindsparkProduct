<?php


Class Passage_model extends MY_Model
{

	public function __construct() {
		 parent::__construct();
 		 $this->dbEnglish = $this->load->database('mindspark_english',TRUE);
		 $this->load->library('session');
	}

	/**
	 * function description : Check session passages...whether they are set ,if they are not set set session passages
	 * param1  : userID  
	 * param2  : prevrefID = previous refID 
	 * @return : true 
	 * */
	public function setNextPassageData($userID,$prevrefID=NULL){
		$sessionPsg=$this->session->userdata('sessionPassages');
		if(!$sessionPsg):
			$this->setNextSessionPassages($userID,$prevrefID);
		endif;
		return true;
	}

	public function getCurrentPassagetype($refID) {
		$this->dbEnglish->Select('passageType');
		$this->dbEnglish->from('passageMaster');
		$this->dbEnglish->where('passageID',$refID);
		$query = $this->dbEnglish->get();
		$value = $query->row();
	          return $value;
      	}

      	public function getQcodePassageDetails($qcode) {
		    $this->dbEnglish->Select('passageID as qcodePassageID');
		    $this->dbEnglish->from('questions');
		    $this->dbEnglish->where('qcode',$qcode);
		    $query = $this->dbEnglish->get();
		    $qcodePassageDetailArr = $query->row();
		    if($qcodePassageDetailArr->qcodePassageID !=0){
			      $this->dbEnglish->Select('passageType');
			      $this->dbEnglish->from('passageMaster');
			      $this->dbEnglish->where('passageID',$qcodePassageDetailArr->qcodePassageID);
			      $query = $this->dbEnglish->get();
			      $passageTypeArr = $query->row();
			      $qcodePassageDetailArr->passageType=$passageTypeArr->passageType;
		    }      
		    return (array)$qcodePassageDetailArr;
  	}
	
	/**
	 * function description : Set session passages[10 psgs=5-Reading,5-listening] as per adaptive/exhuastion logic
	 * param1 : userID , 
	 * param2 : prevrefID = previous refID 
	 * param3 : exhaustionLogLevel shows the level in which user is in for exhaustion
	 * param4 : exhautionContentType is the exhaustion content type [Reading-comprehension]
	 * @return : array, Next session passages to show.  
	 * */
	
	public function getRecentAttmptdPsgID($userID,$passageType){
		$this->dbEnglish->Select('pa.passageID');
		$this->dbEnglish->from('passageAttempt pa');
		$this->dbEnglish->join('passageMaster pm', 'pm.passageID=pa.passageID', 'inner');
		$this->dbEnglish->where('pa.userID',$userID);
		$this->dbEnglish->where('pa.completed',2);
		if($passageType==readingTypeConst || $passageType==readingIllustratedTypeConst ||  $passageType==contentFlowReading):
			$this->dbEnglish->where_in('pm.passageType',array('Textual','Illustrated'));
		else:
			$this->dbEnglish->where('pm.passageType',$passageType);
		endif;	
		$this->dbEnglish->order_by('pa.lastModified','desc');
		$this->dbEnglish->limit($this->remediationConstant);
		$query = $this->dbEnglish->get();
		$result=  $query->result_array();
		return $result;
	}


  	/**
	 * function description : returns the totalAttempts and remediationPosition from userContentAttemptDetails table
	 * param1 : userID , 
	 * param2 : passageType
	 * @return : array, lowest accuracy passageID
	 * */
  	public function userContentAttemptLog($userID,$passageType) {  
  		$this->dbEnglish->Select('totalAttempts,remediationPosition');
		$this->dbEnglish->from('userContentAttemptDetails');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('contentType',$passageType);
		$query = $this->dbEnglish->get();
		$result = $query->row_array();	
		return $result;
  	}

  	public function checkForUserInContentAttemptLog($passageType){	
		$userID=$this->session->userdata('userID');
		$userContentAttempt = $this->userContentAttemptLog($userID,$passageType);	
		if(empty($userContentAttempt)):
			$getContentSpecificTotalAttempts=$this->getContentSpecificTotalAttempts($passageType);
			$totalAttempts=$getContentSpecificTotalAttempts['totalCount'];
			$insertData = array(
				'userID'          	=>	$userID,
				'totalAttempts'         	=> 	$totalAttempts,
				'contentType'          	=> 	$passageType
			);
			$this->dbEnglish->insert('userContentAttemptDetails', $insertData); 
			$userContentAttempt = $this->userContentAttemptLog($userID,$passageType);
		endif;
		$totalAttempts=$userContentAttempt['totalAttempts'];
		if($passageType==contentFlowReading):
			$this->session->set_userdata('totalReadingAttempts', $totalAttempts);
		elseif($passageType==contentFlowSpeaking):
			$this->session->set_userdata('totalSpeakingAttempts', $totalAttempts);
		elseif($passageType==contentFlowConversation):
			$this->session->set_userdata('totalConversationAttempts', $totalAttempts);
		endif;
		return $userContentAttempt;
	}

	/**
	 * function description : returns remediation passage ID 
	 * param1 : total Attempts , 
	 * param2 : userID,
	   param3 : Passage Type
	 * @return : array, lowest accuracy passageID
	 * */
	public function checkNextRemediation($totalAttempts,$userID,$passageType) {
		$resultAttmptdPsgID=$this->getRecentAttmptdPsgID($userID,$passageType);
		if($resultAttmptdPsgID):
			foreach ($resultAttmptdPsgID as  $value) {
				$recentAttemptedID[]= $value['passageID'];
			}
		endif;
		$passageID 		= $this->userLowestAccurcyPsg($userID,$recentAttemptedID);
		$totalAttemptCount    = (empty($passageID)) ? $totalAttempts+$this->remediationConstant+1 : $totalAttempts+2+$this->remediationConstant;
		$this->session->set_userdata('remediationPosition',$totalAttemptCount);
		$data=array('remediationPosition'=> $totalAttemptCount);
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('contentType',$passageType);
		$this->dbEnglish->update('userContentAttemptDetails', $data);
		return $passageID; 
	}

	/**
	 * function description : returns the passage ID having accuracy less than remediationConstant
	 * param1 : recent attempted passage ID , 
	 * param2 : userID
	 * @return : array, lowest accuracy passageID
	 * */
	public function userLowestAccurcyPsg($userID,$recentAttemptedID) {
		$remediationAccuracyConst=$this->remediationAccuracyConst;		
		$questionAttemptTbl=$this->questionAttemptClassTbl;
		$sql_query = array('passageID','round(avg(correct)*100,2) AS acc','attemptedDate');
		$this->dbEnglish->Select($sql_query);		
		$this->dbEnglish->from($questionAttemptTbl);
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where_in('passageID',$recentAttemptedID);
		$this->dbEnglish->group_by('passageID');
		$this->dbEnglish->having('acc <'. $remediationAccuracyConst, NULL, TRUE); 
		$this->dbEnglish->order_by('acc,attemptedDate');
		
		$this->dbEnglish->limit(1);
		$query = $this->dbEnglish->get();
		$getLowestAcc = $query->row();
		return $getLowestAcc;
	}

	public function getReadingMaxAttemptedPassages($userID) {		
		$getReadingMaxAttemptedPassages=array();
		$query = "SELECT a.passageID FROM passageAttempt a 
		LEFT JOIN  (SELECT if((SELECT MAX(attemptCount) FROM passageAttempt WHERE userID = $userID)!=1,passageID,null) as passageID FROM passageAttempt
		WHERE attemptCount = (SELECT MAX(attemptCount) FROM passageAttempt
		WHERE userID = ".$userID.") AND userID = ".$userID.") AS t ON a.passageID = t.passageID
		INNER JOIN passageMaster p on a.passageID=p.passageID 
		WHERE a.userID = ".$userID." and a.completed=2 AND t.passageID IS NULL 
		and p.passageType in ('Textual','Illustrated') ORDER BY a.lastModified , a.attemptCount ASC limit 5";
    		$result=$this->dbEnglish->query($query);
              	$attemptedPassageIDArr =  $result->result_array();
              	foreach($attemptedPassageIDArr as $value)
		{
			if($contentType == readingContentTypeConst){
				if (in_array($value['passageID'], $psgArr)) {
				   array_push($getReadingMaxAttemptedPassages,$value['passageID']);
				}
			}else{
				array_push($getReadingMaxAttemptedPassages,$value['passageID']);
			}
		}

		return $getReadingMaxAttemptedPassages;

	}


	public function getConversationMaxAttemptedPassages($userID) {		
		$getConversationMaxAttemptedPassages=array();
              	$query = "SELECT a.passageID FROM passageAttempt a 
		LEFT JOIN  (SELECT if((SELECT MAX(attemptCount) FROM passageAttempt WHERE userID = $userID)!=1,passageID,null) as passageID FROM passageAttempt
		WHERE attemptCount = (SELECT MAX(attemptCount) FROM passageAttempt
		WHERE userID = ".$userID.") AND userID = ".$userID.") AS t ON a.passageID = t.passageID
		INNER JOIN passageMaster p on a.passageID=p.passageID 
		WHERE a.userID = ".$userID." and a.completed=2 AND t.passageID IS NULL 
		and p.passageType in ('conversation') ORDER BY a.lastModified , a.attemptCount ASC limit 5";
    		$result=$this->dbEnglish->query($query);
              	$attemptedPassageIDArr =  $result->result_array();
              	foreach($attemptedPassageIDArr as $value)
		{
			if($contentType == listeningContentTypeConst){
				if (in_array($value['passageID'], $psgArr)) {
				   array_push($getConversationMaxAttemptedPassages,$value['passageID']);
				}
			}else{
				array_push($getConversationMaxAttemptedPassages,$value['passageID']);
			}
		}

		return $getConversationMaxAttemptedPassages;
	}

	public function setNextSessionPassages($userID,$prevrefID=NULL,$exhaustionLogLevel=false,$exhaustionContentType=false){		
		$readingPsgsArr=array(); $listeningPsgsArr=array();
		$psgDataArr=array(); 
		$passageID='';
		$remediationStatus = 0;
		$attemptedPassageID=""; 
		$userID = $this->session->userdata('userID');                        
		$passageType = $this->session->userdata('presentContentType');
		$currentContentType = $this->session->userdata('currentContentType');

		// Online and offline code is sync checking before content flow started
		/*-------------------------Online and Offline sync pending code----------------------------- */
		$passageID = $this->checkPendingPassage($userID,$passageType);
		$userInContentAttemptLog=$this->checkForUserInContentAttemptLog($passageType);
		//echo $passageID;exit;
		/*-------------------------Online and Offline sync pending code----------------------------- */
		if(!$passageID) : 			
			$remediationPstn=$userInContentAttemptLog['remediationPosition'];
			$totalAttempts=$userInContentAttemptLog['totalAttempts'];
			$checkVal =$totalAttempts %  $this->remediationConstant;
			/*checking the remediation count it's dived / RemediationConstant  */
			if(($remediationPstn!="" &&  ($totalAttempts==($remediationPstn-1))) || ($remediationPstn=="" &&  ($checkVal==0)) && ($totalAttempts!=0)) :
				$passageAttempt=$this->checkNextRemediation($userInContentAttemptLog['totalAttempts'],$userID,$passageType);
				if($passageAttempt['passageID']) :
					$passageID = $passageAttempt['passageID'];
					$remediationStatus=1;
				endif;
			endif;

			/*Normal passage flow started here*/
			if(!$passageID) :
				$remediationStatus=0;
				$currentContent=$this->session->userdata('currentContentType');
				$passageLevel=$this->session->userdata('passageLevel');
				$conversationLevel=$this->session->userdata('conversationLevel');
				$attemptedPassageID = $this->questionspage_model->getUserAttemptedPassage($userID);
				if($attemptedPassageID!=""):
					$attemptedPassageIDArr = explode(',', $attemptedPassageID);
				endif;
				if($passageType=='reading'): 
					$readingPsgsDataArr=$this->getReadingPassageArr($userID,$attemptedPassageIDArr);
					foreach($readingPsgsDataArr as $key=>$value)  {
						array_push($readingPsgsArr,$value['passageID']);
					}
					$readingPsgsArr=$this->setUncompletedPassages($readingPsgsArr,$attemptedPassageIDArr,readingContentTypeConst);
					if(empty($readingPsgsArr)) :
						$readingPsgsArr = $this-> getReadingMaxAttemptedPassages($userID,$passageType);
					endif;
				elseif($passageType=="conversation"):					
					$listeningpsgsDataArr=$this->getConversatnPassageArr($userID,$attemptedPassageIDArr);
					foreach($listeningpsgsDataArr as $key=>$value) {
						array_push($listeningPsgsArr,$value['passageID']);
					}
					$listeningPsgsArr=$this->setUncompletedPassages($listeningPsgsArr,$attemptedPassageIDArr,listeningContentTypeConst,$convesationMsLevel);
					if(empty($listeningPsgsArr)) :
						$listeningPsgsArr=$this->getConversationMaxAttemptedPassages($userID,$passageType);
					endif;
				endif;

				$userRemediationLog = $this->checkUserRemediation($userID,$passageType);
				if($userRemediationLog['remediationPosition']==''):        
					$nextPassagelimit=1;
				else:
					$nextPassagelimit = $userRemediationLog['remediationPosition'] - ($userRemediationLog['totalAttempts'] + 1);
				endif;

				$contentFlowQuantity=$this->session->userdata('contentQuantity');
				$attemptCount=$this->session->userdata('contentAttemptCount');
				$attemptdQty=$contentFlowQuantity-$attemptCount;
				if($nextPassagelimit>=$attemptdQty):
					$nextPassagelimit=$attemptdQty;
				endif;
				for ($i=0; $i<$nextPassagelimit; $i++) { 
					if($passageType=='reading')
					{
						$psgDataArr[$i] = $readingPsgsArr[$i];
					}
					else
					{
						$psgDataArr[$i] = $listeningPsgsArr[$i];  
					}
				}
			endif;
		endif;	

		if($passageID && !$this->session->userdata('sessionPassages')):                                                           
			$psgDataArr[]  = $passageID;
		elseif($passageID):
			$psgDataArr[1] = $passageID;
		endif;
		$this->session->set_userdata('sessionPassages',$psgDataArr);
		$this->session->set_userdata('remediationStatus',$remediationStatus);
		$this->updateUserCurrentStatusTbl($userID);  					
     }



     	public function checkPendingPassage($userID,$presentContentType) {

		$query = "SELECT  a.userID, a.passageID, a.completed, tmp.completed
		FROM  passageAttempt a  LEFT JOIN
		(SELECT a.passageID, a.userID, a.completed
		FROM  passageAttempt a, passageMaster b
		WHERE a.passageID = b.passageID";
		if($presentContentType=='reading') :
			$query.=" AND b.passageType IN('Textual','Illustrated')";
		else :
			$query.=" AND b.passageType = 'conversation' ";
		endif;

		$query.=" AND a.userID = $userID AND completed = 2) tmp ON a.passageID = tmp.passageID
		AND a.userID = tmp.userID JOIN passageMaster c ON a.passageID = c.passageID
		WHERE a.userID = $userID ";

		if($presentContentType=='reading') :
			$query.=" AND c.passageType IN('Textual','Illustrated')";
		else :
			$query.=" AND c.passageType = 'conversation' ";
		endif;
		$query.=" AND tmp.completed IS NULL";

		$result = $this->dbEnglish->query($query)->row_array();
		if(isset($result) && count($result)) :
			return $result['passageID'];
		endif;
	}

	public function checkUserRemediation($userID,$passageType) {
		$this->dbEnglish->Select('totalAttempts,remediationPosition');
		$this->dbEnglish->from('userContentAttemptDetails');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('contentType',$passageType);
		$query = $this->dbEnglish->get();
		return $query->row_array();
	}

	public function updateUserCurrentStatusTbl($userID) {		
		$presentContentType=$this->session->userdata('presentContentType');
		$presentContentType = strtolower($presentContentType);
		if($presentContentType==contentFlowReading || $presentContentType==contentFlowConversation):
			$contentType=currContentTypePsgConst;
			$sessionPassages=$this->session->userdata('sessionPassages');
			$refID=$sessionPassages[0];
		elseif($presentContentType==contentFlowSpeaking):
			$contentType=contentFlowSpeaking;
			$speakingQues=$this->session->userdata('sessionSpeakingQues');
			$refID=$speakingQues[0];
		endif;
		$this->session->set_userdata('currentContentType',$contentType);
		$this->session->set_userdata('refID',$refID);
		$this->session->set_userdata('completed',0);
		$data=array('currentContentType'=>$contentType,'refID'=>$refID,'completed'=>0);
		$this->dbEnglish->where('userID', $userID);
		$this->dbEnglish->update('userCurrentStatus',$data);
	}

	public function getReadingPassageArr($userID,$attemptedPassageIDArr){
		$passageLevel=$this->session->userdata('passageLevel');
		$gradeLowerLimit=number_format($passageLevel, 2);
		$gradeHigherLimit=$gradeLowerLimit+gradeHigherLimitIncreaseConst;
		$readingPsgCondArr = array('q.passageStatus' => livePassageStatus, 'q.diffRating >=' => $gradeLowerLimit, 'q.diffRating <=' => $gradeHigherLimit);
		$this->dbEnglish->Select('p.passageID as passageID');
		$this->dbEnglish->from('passageMaster p');
		$this->dbEnglish->join('passageAdaptiveLogicParams q', 'p.passageID=q.passageID', 'inner');
		$this->dbEnglish->where($readingPsgCondArr);
		$this->dbEnglish->where_in('p.passageType',array('Textual','Illustrated'));
		$this->dbEnglish->where_not_in('p.passageID', $attemptedPassageIDArr);
		$this->dbEnglish->order_by('q.passageId','RANDOM');
		$readingPsgsSql = $this->dbEnglish->get();
		$readingPsgsDataArr = $readingPsgsSql->result_array();
		return $readingPsgsDataArr;
	}

	public function getConversatnPassageArr($userID,$attemptedPassageIDArr){
		$psgDataArr=array();
		$conversationLevel=$this->session->userdata('conversationLevel');
		$convesationMsLevel=$conversationLevel-gradeScallingConst;
		$listeningPsgCondArr = array('status' => livePassageStatus,'msLevel' => $convesationMsLevel, 'passageType' => 'Conversation');
		$this->dbEnglish->Select('passageID as passageID');
		$this->dbEnglish->from('passageMaster');
		$this->dbEnglish->where($listeningPsgCondArr);
		$this->dbEnglish->where_not_in('passageID', $attemptedPassageIDArr);
		$this->dbEnglish->order_by('passageID','RANDOM');
		$listeningpsgsSql = $this->dbEnglish->get();
		$listeningpsgsDataArr = $listeningpsgsSql->result_array();
		return 	$listeningpsgsDataArr;	
	}

	/**
	 * function description : Set uncompleted/half attempted passages as highest priority in reading/listening array
	 * param1 : psgArr = Unattempted Reading/listening passages array 
	 * param2 : attemptedPassageIDs = All questions passages array 
	 * param3 : contentType = Passage content type
	 * param4 : convesationMsLevel is the ms level of conversation
	 * param5 : isContentExhausted will be true if need to check for exhuastion passages
	 * @return : array, with unattempted passages as highest priority.  
	 * */

	public function getContentSpecificTotalAttempts($passageType){
		$userID=$this->session->userdata('userID');
		if($passageType==contentFlowSpeaking):
			$this->dbEnglish->Select('count(qcode) as totalCount');
			$this->dbEnglish->from('speakingAttempts');
			$this->dbEnglish->where('userID',$userID);
			$query = $this->dbEnglish->get();

		else:
			$this->dbEnglish->Select('count(pa.passageID) as totalCount');
			$this->dbEnglish->Select('count(distinct pa.passageID) as distinctCount');
			$this->dbEnglish->from('passageAttempt pa');
			$this->dbEnglish->join('passageMaster pm', 'pm.passageID=pa.passageID', 'inner');
			$this->dbEnglish->where('pa.userID',$userID);
			$this->dbEnglish->where('pa.completed',2);
			if($passageType=='reading'):
				$this->dbEnglish->where_in('pm.passageType',array('Textual','Illustrated'));
			else:
				$this->dbEnglish->where('pm.passageType',$passageType);
			endif;
			$query = $this->dbEnglish->get();
		endif;
		$result=  $query->row_array();
		return $result;
	}

	public function setUncompletedPassages($psgArr,$attemptedPassageIDs,$contentType,$convesationMsLevel='',$isContentExhausted=false){
		$allQuesNotCompletedPsgArr=$this->userUnCompletedPassages($psgArr,$attemptedPassageIDs,$contentType,$convesationMsLevel,$isContentExhausted);
		$removeDuplicateSamePsg = array_diff($psgArr, $allQuesNotCompletedPsgArr);
			
		// resetting the keys after removal of same passageID from psgArr
		$removeDuplicateSamePsg=array_values($removeDuplicateSamePsg);
		$i=0;
		if(count($allQuesNotCompletedPsgArr)>0)
		{
			
			foreach($allQuesNotCompletedPsgArr as $passageID){
				$removeDuplicateSamePsg[$i]=$passageID;
				$i++;	
			}
			
		}
		
		// Remove refID passage set in usercurrentstatus[if completed=0] from the array 
		$refID=$this->session->userdata('refID');
		if (($key = array_search($refID, $removeDuplicateSamePsg)) !== false) {
		    unset($removeDuplicateSamePsg[$key]);
		}
		$removeDuplicateSamePsg=array_values($removeDuplicateSamePsg);		
		return $removeDuplicateSamePsg;
	}

	/**
	 * function description : returns uncompleted/half attempted passages
	 * param1 : psgArr = Unattempted Reading/listening passages array 
	 * param2 : attemptedPassageIDs = All questions passages array 
	 * param3 : contentType = Passage content type
	 * param4 : convesationMsLevel is the ms level of conversation
	 * param5 : isContentExhausted will be true if need to check for exhuastion passages
	 * @return : array, of unattempted passages.  
	 * */
	
	public function userUnCompletedPassages($psgArr,$attemptedPassageIDs,$contentType,$convesationMsLevel='',$isContentExhausted=false){		
		$allQuesNotCompletedPsgArr=array();
		$psgCondArr = array('q.userID' => $this->user_id);		
		$this->dbEnglish->Select('distinct(p.passageID) as passageID');
		$this->dbEnglish->from('passageMaster p');
		$this->dbEnglish->join('passageAttempt q', 'p.passageID=q.passageID', 'inner');
		$this->dbEnglish->where($psgCondArr);
		if($contentType == readingContentTypeConst){
			$this->dbEnglish->where_in('p.passageType',array('Textual','Illustrated'));
		}
		else if($contentType == listeningContentTypeConst){
			$this->dbEnglish->where('p.msLevel',$convesationMsLevel);
			$this->dbEnglish->where_in('p.passageType','Conversation');
		}
		
		
		$this->dbEnglish->where_not_in('p.passageID', $attemptedPassageIDs);
		$this->dbEnglish->order_by('q.lastmodified','asc');		
		$query = $this->dbEnglish->get();
		$attemptedPassageIDArr = $query->result_array();		
		foreach($attemptedPassageIDArr as $value)
		{
			// To check whether half attempted passage is of same level or not,by checking it in the unattempted array
			//if($this->questionspage_model->lastCompletedPassageQuestionsPending(null,$userID,$value['passageID']))
			//{
				if($contentType == readingContentTypeConst){
					if (in_array($value['passageID'], $psgArr)) {
					   array_push($allQuesNotCompletedPsgArr,$value['passageID']);
					}
				}else{
					array_push($allQuesNotCompletedPsgArr,$value['passageID']);
				}
				
			//}	
		}

		return $allQuesNotCompletedPsgArr;
	}	

	

	/**
	 * function description : updates userLevelAndAccLog table after the passage attempt 
	 * param1 : userID 
	 * param2 : contentType = Content type for which attempted count to check
	 * param3 : passageID = Attempted passage after which userLevelAndAccLog need to be updated
	 * @return : none.  
	 * */
	public function updateUserLevelAndAccPsgLog($userID,$contentType,$passageID){
		if($contentType == readingTypeConst || $contentType == readingIllustratedTypeConst){
			$contentType=readingContentTypeConst;
			$level=$this->session->userdata('passageLevel');
		}
		else if($contentType == "Conversation"){
			$contentType=listeningContentTypeConst;
			$level=$this->session->userdata('conversationLevel');
		}elseif($contentType == "Speaking"){
			$contentType=speakingContentTypeConst;
			$childClass=$this->session->userdata('childClass');
			$level=$childClass-3;
		}
	
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
				$this->updateQuesPsgAttemptCount($userLevelAndAccData,$passageID);	
			}
			else{
				$this->insertUserLevelData( $userID,$contentType,$level);
				
			}
		else:
			$this->insertUserLevelData( $userID,$contentType,$level);
		endif;


	}
	public function insertUserLevelData( $userID,$contentType,$level){
		$data = array(
			'userID' => $userID,
			'contentType' => $contentType,
			'quesPsgAttemptCount' => 0,
			'accuracy' => 0,
			'level' => $level
		);

		$this->dbEnglish->insert('userLevelAndAccuracyLog', $data);

	}
	
	/**
	 * function description : updates quesPsgAttemptCount in the userLevelAndAccLog table 
	 * param1 : userLevelAndAccData = user content type row data which needs to be updated  
	 * param2 : passageID = passageID for which the table should be updated 
	 //greater than or equal to condition added for offline cases where count will be increased
	 * @return : none.  
	 * */

	public function updateQuesPsgAttemptCount($userLevelAndAccData,$passageID){
		

		if($this->category == 'ADMIN' || $this->category == 'School Admin' || $this->category == 'TEACHER')//added by nivedita
		{
			$level = $this->session->userdata('passageLevel');
			$this->dbEnglish->Select('quesPsgAttemptCount');
			$this->dbEnglish->from('userLevelAndAccuracyLog');
			$this->dbEnglish->where('contentType', $userLevelAndAccData->contentType);
			$this->dbEnglish->where('userID', $userLevelAndAccData->userID);
			$this->dbEnglish->where('level', $level);
			$getPsgAttmpCountSql = $this->dbEnglish->get();
			$getPsgAttmpCountRow = $getPsgAttmpCountSql->row_array();

			$userLevelAndAccData->quesPsgAttemptCount = $getPsgAttmpCountRow['quesPsgAttemptCount']+1;

			$data=array('quesPsgAttemptCount'=> $userLevelAndAccData->quesPsgAttemptCount);

			$this->dbEnglish->where('userID', $userLevelAndAccData->userID);
			$this->dbEnglish->where('contentType', $userLevelAndAccData->contentType);
			$this->dbEnglish->where('level', $level);
		}
		else
		{
			$userLevelAndAccData->quesPsgAttemptCount=$userLevelAndAccData->quesPsgAttemptCount+1;
			$data=array('quesPsgAttemptCount'=> $userLevelAndAccData->quesPsgAttemptCount);
			$this->dbEnglish->where('userID', $userLevelAndAccData->userID);
			$this->dbEnglish->where('contentType', $userLevelAndAccData->contentType);
			$this->dbEnglish->where('scoringID', $userLevelAndAccData->scoringID);	
		}

		$this->dbEnglish->update('userLevelAndAccuracyLog', $data);
	} 
	
	
	
	
	
	public function getmslevel($userID, $contentType){
		//echo "content type".$contentType ; die("getmslevel");
		//Get the unattempted questions and mslevel through the query. Subquerry is used so that it will eliminate the truncation of comma separated passage id's. 
		if($contentType == readingContentTypeConst ){

			//For reading querry
			$this->dbEnglish->Select('count(distinct(a.passageid)) as unattempted_psgs, floor(a.diffrating) as newmslevel ');
			$this->dbEnglish->from('passageAdaptiveLogicParams a');
			$this->dbEnglish->join("passageMaster b", 'a.passageid = b.passageID', 'inner');

			$this->dbEnglish->join("passageAttempt c", 'a.passageid = c.passageID and c.userID ='.$userID, 'left outer');
			$this->dbEnglish->where('b.status',7);
			$this->dbEnglish->where('b.passagetype <>\'conversation\'');
			$this->dbEnglish->where('c.passageid is null');
			$this->dbEnglish->where('a.diffrating >=4');
			$this->dbEnglish->group_by('newmslevel');
			$this->dbEnglish->order_by("unattempted_psgs desc");
			$this->dbEnglish->limit(1);

		} else{

			//For listening query.
			$this->dbEnglish->Select('count(distinct(a.passageid)) as unattempted_psgs, a.mslevel as newmslevel ');
			$this->dbEnglish->from('passageMaster a');
			$this->dbEnglish->join("passageAttempt b", 'a.passageid = b.passageID and b.userID ='.$userID, 'left outer');
			$this->dbEnglish->where('a.status',7);
			$this->dbEnglish->where('a.passagetype','conversation');
			$this->dbEnglish->where('b.passageid is null');
			$this->dbEnglish->group_by('newmslevel');
			$this->dbEnglish->order_by("unattempted_psgs desc");
			$this->dbEnglish->limit(1);
		}

		$getmaxunattempt = $this->dbEnglish->get();

		$maxquesPending = 0;	
		$maxquesPending=$getmaxunattempt->num_rows();

		//Get the new ms level which we need to set in the userexhaustionlogic log.
		if($maxquesPending>0){

			$row = $getmaxunattempt->row();
			$newlevel = $row->newmslevel;
			$newpsgstoshow = $row->unattempted_psgs;	

			//Change the newlevel as per our mindspark logic.
			if($contentType == listeningContentTypeConst)
				$newlevel = $newlevel + 3;
			
		}
		else{
			//In worst scenereo if no row is present then give alert to user.
			echo '<script language="javascript">';
			echo 'alert("No Passages are available.")';
			echo '</script>';
			return;
		}

		//By default return the array which contains the newlevel and no of unattempted questions to show. 
		
		return array($newlevel, $newpsgstoshow);
		
	}

/*
	Added by Shivam - 03/17/2018
		 * function description : This function will insert/update the userexhaustionlogiclog table for
		 						 the mslevel for which maximum no of questions are available to attempt.
		 * param1   userid
		 * param2	contentType
		 * param3 	newlevel- newlevel which needs to be set.
		 * param4	psgtoshow- unattempted passage count which need to be set.
		 * @return  none.
		 * 
*/
	public function updateOrInsertAfterNoPsgsleftinExhaustion($userID, $contentType, $newlevel,$psgtoshow){

		//Check if row exists or not in the userexhaustionlogiclog table.
		$this->dbEnglish->select('exScoringID');
		$this->dbEnglish->from('userexhaustionlogiclog');
		$this->dbEnglish->where('psgsToShow',0);
		$this->dbEnglish->where('accuracy',0);
		$this->dbEnglish->where('contentType',$contentType);
		$this->dbEnglish->where('userid',$userID);
		$this->dbEnglish->order_by('exScoringID','desc');
		$this->dbEnglish->limit(1);
		$checkifexists = $this->dbEnglish->get();

		$rowcount = 0;	
		$rowcount=$checkifexists->num_rows();

		//Get the row in variable.
		$row = $checkifexists->row();

		If($rowcount == 1){
			//If row is present then update the same row.
			$data=array('level'=> $newlevel,'psgsToShow'=>$psgtoshow );
			$this->dbEnglish->where('exScoringID',$row->exScoringID);
			$this->dbEnglish->update('userexhaustionlogiclog', $data);

		}else{

			//Insert row in the table if it is not present. 
			$data = array(
					'userID' => $userID,
					'contentType' => $contentType,
					'accuracy' => 0,
					'level' => $newlevel,
					'psgsToShow' => $psgtoshow
				);
			$this->dbEnglish->insert('userexhaustionlogiclog', $data);

		}

	}
}

