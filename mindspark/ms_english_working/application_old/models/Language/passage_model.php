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

	/**
	 * function description : returns the passage type of the particular passage id
	 * param1  : userID  
	 * @return : passageType 
	 * */
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
	 * function description : returns the recently attempted passage id by the user
	 * param1 : userID , 
	 * param2 : passageType
	 * @return : array, recently attempted passage id
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
	 * @return : array,totalAttempts and remediationPosition
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
	
	/**
	 * function description : If entry is not present for particular contentType then inserts the record in userContentAttemptDetails table
	 * param1 : passageType
	 * @return : returns the totalAttempts
	 * */
  	public function checkForUserInContentAttemptLog($passageType){	
		$userID=$this->session->userdata('userID');
		$userContentAttempt = $this->userContentAttemptLog($userID,$passageType);	
		if(empty($userContentAttempt)):
			$getContentSpecificTotalAttempts=$this->getContentSpecificTotalAttempts($passageType);
			$totalAttempts=$getContentSpecificTotalAttempts['totalCount'];
			$insertData = array(
				'userID'          	=>	$userID,
				'totalAttempts'     => 	$totalAttempts,
				'contentType'       => 	$passageType
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
	 * function description : converts the array into comma separated string
	 * param1 : array
	 * @return : returns the comma separated string
	 * */
	public function implodeArrayvalue($result){
	    $values = array_map('array_pop', $result);
	    return implode(',', $values);
	}
	
	
	/**
	 * function description : sets the next remediationPosition in userContentAttemptDetails table and returns the passageID
	 * param1 : totalAttempts
	   param2 : userID
	   param3 : passageType
	 * @return : returns the passageID
	 * */
	public function checkNextRemediation($totalAttempts,$userID,$passageType) {
		$resultAttmptdPsgID=$this->getRecentAttmptdPsgID($userID,$passageType);
		$recentAttemptedID   = $this->implodeArrayvalue($resultAttmptdPsgID);
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
	 * function description : returns the lowest accuracy passageID
	 * param1 : userID
	   param2 : recentAttemptedID
	 * @return : returns the passageID with the lowest accuracy
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
		$getLowestAcc = $query->row_array();
		return $getLowestAcc;
	}
	
	/**
	 * function description : returns the maximum attempt count for a particular user in passage
	 attempt table
	 * param1 : userID
	 * @return : returns the maximum attempt count
	 * */
	public function passageMaxattemptCount($userID) {
		$this->dbEnglish->select_max('attemptCount');
		$this->dbEnglish->from('passageAttempt');
		$this->dbEnglish->where('userID',$userID);
		$query = $this->dbEnglish->get();    		
		$passageMaxattemptCount =  $query->row_array();
		$return_result = $passageMaxattemptCount['attemptCount'];
		return $return_result;
	}

	/**
	 * function description : returns the maximum attemptd reading passage id's
	 * param1 : userID
	 * @return : returns the maximum attemptd reading passage id
	 * */
	public function getReadingMaxAttemptedPassages($userID) {
		$passageMaxattemptCount        =  $this->passageMaxattemptCount($userID);	
		$getReadingMaxAttemptedPassages=array();

		$subquery = "(SELECT if(($passageMaxattemptCount)!=1,passageID,null) as passageID FROM passageAttempt WHERE attemptCount = $passageMaxattemptCount AND 
		userID = $userID) t";

		$this->dbEnglish->select('a.passageID');
		$this->dbEnglish->from('passageAttempt a');
		$this->dbEnglish->join($subquery, 'a.passageID = t.passageID', 'left');
		$this->dbEnglish->join('passageMaster p','a.passageID=p.passageID');
		$this->dbEnglish->where('a.userID',$userID);
		$this->dbEnglish->where('a.completed',2);
		$this->dbEnglish->where('t.passageID',NULL);
		$this->dbEnglish->where_in('p.passageType',array('Textual','Illustrated'));
		$this->dbEnglish->order_by('a.lastModified , a.attemptCount','ASC');
		$this->dbEnglish->limit(5);
		$result = $this->dbEnglish->get();
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

	/**
	 * function description : returns the maximum attemptd conversation passage id's
	 * param1 : userID
	 * @return : returns the maximum attemptd conversation passage id
	 * */
	public function getConversationMaxAttemptedPassages($userID) {

		$passageMaxattemptCount        =  $this->passageMaxattemptCount($userID);			
		$getConversationMaxAttemptedPassages=array();
		$subquery = "(SELECT if(($passageMaxattemptCount)!=1,passageID,null) as passageID FROM passageAttempt WHERE attemptCount = $passageMaxattemptCount AND 
		userID = $userID) t";
		$this->dbEnglish->select('a.passageID');
		$this->dbEnglish->from('passageAttempt a');
		$this->dbEnglish->join($subquery, 'a.passageID = t.passageID', 'left');
		$this->dbEnglish->join('passageMaster p','a.passageID=p.passageID');
		$this->dbEnglish->where('a.userID',$userID);
		$this->dbEnglish->where('a.completed',2);
		$this->dbEnglish->where('t.passageID',NULL);
		$this->dbEnglish->where('p.passageType','conversation');
		$this->dbEnglish->order_by('a.lastModified , a.attemptCount','ASC');
		$this->dbEnglish->limit(5);
		$result = $this->dbEnglish->get();
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
	/**
	 * function description : sets the passage id to be attempted by the user into the session
	 * param1 : userID
	 * */
	public function setNextSessionPassages($userID,$prevrefID=NULL,$exhaustionLogLevel=false,$exhaustionContentType=false){
		
		$readingPsgsArr=array(); 
		$listeningPsgsArr=array();
		$psgDataArr=array(); $passageID='';
		$remediationStatus = 0;
		$attemptedPassageID=""; 
		$userID = $this->session->userdata('userID');                        
		$passageType = $this->session->userdata('presentContentType');
		$currentContentType = $this->session->userdata('currentContentType');

		// Online and offline code is sync checking before content flow started
		/*-------------------------Online and Offline sync pending code----------------------------- */
		$passageID = $this->checkPendingPassage($userID,$passageType);
		/*-------------------------Online and Offline sync pending code----------------------------- */
		if(!$passageID) : 
			$userInContentAttemptLog=$this->checkForUserInContentAttemptLog($passageType);
			$remediationPstn=$userInContentAttemptLog['remediationPosition'];
			$totalAttempts=$userInContentAttemptLog['totalAttempts'];
			$checkVal =$totalAttempts %  RemediationConstant;
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
				if($passageType==contentFlowReading): 
					$readingPsgsDataArr=$this->getReadingPassageArr($userID,$attemptedPassageIDArr);
					foreach($readingPsgsDataArr as $key=>$value)  {
						array_push($readingPsgsArr,$value['passageID']);
					}
					$readingPsgsArr=$this->setUncompletedPassages($readingPsgsArr,$attemptedPassageIDArr,readingContentTypeConst);
					if(empty($readingPsgsArr)) :
						$readingPsgsArr = $this-> getReadingMaxAttemptedPassages($userID,$passageType);
						$this->session->set_userdata('isRepetation',1);
					endif;
				elseif($passageType==contentFlowConversation):					
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
					if($passageType==contentFlowReading)
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



     	/**
	 * function description : returns the totalAttempts and remediationPosition from userContentAttemptDetails table
	 * param1 : userID,
	  param2 : passageType
	 * @return : returns the totalAttempts and remediationPosition
	 * */
	public function checkUserRemediation($userID,$passageType) {
		$this->dbEnglish->Select('totalAttempts,remediationPosition');
		$this->dbEnglish->from('userContentAttemptDetails');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('contentType',$passageType);
		$query = $this->dbEnglish->get();
		return $query->row_array();
	}

	
	/**
	 * function description : fetches the  unattempted reading passage id's
	 * param1 : userID,
	  param2 : attemptedPassage
	 * @return : returns the tunattempted reading passage id's
	 * */
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

	/**
	 * function description : fetches the  unattempted conversation passage id's
	 * param1 : userID,
	  param2 : attemptedPassage
	 * @return : returns the unattempted conversation passage id's
	 * */
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
	 * function description : fetches the content specific total attempts for particular user
	 * param1 : passageType
	 * @return : array, returns the content specific total attempts for particular user
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
			if($passageType==contentFlowReading):
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
			
				if($contentType == readingContentTypeConst){
					if (in_array($value['passageID'], $psgArr)) {
					   array_push($allQuesNotCompletedPsgArr,$value['passageID']);
					}
				}else{
					array_push($allQuesNotCompletedPsgArr,$value['passageID']);
				}
				
		}

		return $allQuesNotCompletedPsgArr;
	}	


	
	/**
	 * function description : returns User Current reading/listening contenttype attempted count from userLevelAndAccuracyLog
	 * param1 : userID 
	 * param2 : contentType = Content type for which attempted count to check
	 * @return : array, count of attempts for the perticular content type.  
	 * */

	public function returnCurrentLevelPsgAttemptCnt($userID,$contentType){
		$this->dbEnglish->Select('*');
		$this->dbEnglish->from('userLevelAndAccuracyLog');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('contentType',$contentType);
		$this->dbEnglish->order_by('scoringID','desc');
		$this->dbEnglish->limit(1);
		$getUserLevelAndAccLogSql = $this->dbEnglish->get();
		$userLevelAndAccData=0;
		
		if($getUserLevelAndAccLogSql->num_rows() > 0){
			$tmpRow = $getUserLevelAndAccLogSql->row();
			$userLevelAndAccData=$tmpRow->quesPsgAttemptCount;				
		}
		return $userLevelAndAccData;
		
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
			'quesPsgAttemptCount' => 1,
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
	

/*
	Added by Shivam - 03/17/2018
		 * function description : This function will be called for two purposes - 
	 						1) Getting the newmslevel and no of unattempted free questions which needs to be set in the userexhaustionlogiclog table.
	 						2) If param2 value is passed as true then it will also call another function to update/insert the userexhaustionlogiclog table.
		 * param1   userid
		 * param2	passagetype
		 * @return  array containing the newlevel and psgs to attempt.
		 * 
*/
	public function getmslevel($userID, $contentType){
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

}

