<?php

Class Home_model extends MY_Model
{

	public $tableMasterArr;
	public $fieldsMasterArr;
	public $homepageInfo;
	public function __construct() {
	  parent::__construct();	
	  $this->dbEnglish = $this->load->database('mindspark_english',TRUE);
	  $this->Companies_db = $this->dbEnglish;

	  // Pass reference of database to the CI-instance
	  $CI =& get_instance();
	  $CI->Companies_db =& $this->Companies_db; 


     $this->tableMasterArr = $this->getTableInfo();
     $this->fieldsMasterArr = $this->getFieldsInfo();
     $this->homepageInfo = array();
	}

	/**
	 * function role : Handling skill-o-meter build logic recursively
	 * param2 : userID
	 * @return : array, skill-o-meter 
	 * 
	 * */

	public function buldSkillTree($userID,$parentId = 0) {
		$elements = $this->getSKMInfo();
	    $branch = array();

	    foreach ($elements as $element) {

	    	if (strpos($element['parent_id'],',') !== false) 
	    	{
	    		$parentIDs = explode(',', $element['parent_id']);
	    		foreach ($parentIDs as $parID) {

	    			if ($parID == $parentId)
			        {
			            $children = $this->buldSkillTree($userID,$element['id']);
			            if ($children) 
			            {
			                $element['children'] = $children;
			                $branch[$element['SKMName']] = $element['children'];
			            }
			            else
			            {
			            	$element['skillDataValue'] = $this->getComputedData($userID,$element['id']);
			            	$branch[$element['SKMName']] = $element['skillDataValue'];
			            }
			        }
	    		}
	    	}
	    	else
	    	{
		        if ($element['parent_id'] == $parentId)
		        {
		            $children = $this->buldSkillTree($userID,$element['id']);
		            if ($children) 
		            {
		                $element['children'] = $children;
		                $branch[$element['SKMName']] = $element['children'];
		            }
		            else
		            {
		            	$element['skillDataValue'] = $this->getComputedData($userID,$element['id']);
			            	
			            $branch[$element['SKMName']] = $element['skillDataValue'];
		            }
		             
		        }

	    	}

	    }

     return $branch;
    }

    /**
	 * function role : Get skill-o-meter skill/sub skill info from skil-o-meter table
	 * @return : array, skill-o-meter skill/sub skill info
	 * 
	 * */

	public function getSKMInfo(){
		$this->dbEnglish->Select('SKMId as id, masterSkmId as parent_id,SKMName');
		$this->dbEnglish->from('SKMMaster');
		$query = $this->dbEnglish->get();

		$SKMInfoArr = $query->result_array();

		return $SKMInfoArr;
	}

	/**
	 * function role : Computing user performance in concerned skills/subskills of skill-o-meter
	 * param1 : userID
	 * param2 : skill-o-meter id
	 * @return : array, skill-o-meter skill/sub skill user performance data
	 * 
	 * */

    public function getComputedData($userID,$SKMid)
    {

		// $this->output->enable_profiler(TRUE);
    	$this->dbEnglish->Select('tableUsed,fieldUsed,valuesUsed,useQuesAttemptData');
    	$this->dbEnglish->from('SKMMaster');
    	$this->dbEnglish->where('SKMId',$SKMid);
    	$query = $this->dbEnglish->get();
    	$SKMInfoArr = $query->result_array();
    	$SKMInfo = $SKMInfoArr[0];
    	extract($SKMInfo);

    	$query = "";
 		
 			$tableArr = explode('~', $tableUsed);
	    	$delimiters1 = Array("~",",");
	    	$delimiters2 = Array("~",'#');
	    	$fieldsArr = $this->multiexplode($delimiters1, $fieldUsed);
	    	$valuesArr = $this->multiexplode($delimiters2, $valuesUsed);

 			$query .= " Select group_concat(qcodes) as qcodes from ( ";
	    	foreach($fieldsArr as $key=>$value)
	    	{
	    		foreach($value as $k=>$val)
	    		{
	    			if($useQuesAttemptData == 1)
	    			{
	    				if($this->fieldsMasterArr[$val]=="passageType")
	    					$a = "passageTypeName";
	    				else
	    					$a = $this->fieldsMasterArr[$val];
	    				$b = $this->fieldsMasterArr[$val];


	    				$query .= " (Select A.qcode as qcodes from questions A, ".$this->tableMasterArr[$tableArr[$key]]." B where FIND_IN_SET( B.".$b." ,A.".$a." ) > 0 and B.".$b." in ( ".$valuesArr[$key][$k]." ) ) ";

		    			if($key==(count($fieldsArr)-1) and $k==(count($value)-1) )
		    				$query.= " ";
		    			else
		    				$query.= " UNION ";
	    			}
	    			else
	    			{
	    				$this->dbEnglish->Select($this->fieldsMasterArr[$val]);
				    	$this->dbEnglish->from($this->tableMasterArr[$tableArr[$key]]);
				    	$this->dbEnglish->where('userID',$userID);
				    	$query1 = $this->dbEnglish->get();
				    	$queryArr = $query1->result_array();
	    			}
	    		}
	    	}
	    	$query .= " ) as Q";
			if($useQuesAttemptData == 1)
			{
				$this->dbEnglish->query("SET SESSION group_concat_max_len = 1000000");
				$qcodes = array();
				$result = $this->dbEnglish->query($query);
				if($result->num_rows() > 0)
				{

					$qcodes = $result->result_array(); 
				}

				return $this->getUserPerformance($userID,$qcodes[0]['qcodes']);
			}
			else
			{
				return $queryArr[0][$this->fieldsMasterArr[$val]];
			}
    }

	/**
	 * function role : Fetch MS English table details using unique table ids
	 * @return : array, Table details
	 * 
	 * */

	public function getTableInfo()
	{
		$tableInfoArr = array();
		$this->dbEnglish->Select('tableID,tableName');
    	$this->dbEnglish->from('tableMaster');

    	$query = $this->dbEnglish->get();
    	$queryArr = $query->result_array();
    	foreach ($queryArr as $key => $value) {
    		$tableInfoArr[$value['tableID']] =$value['tableName'] ;
    	}

    	
    	return $tableInfoArr;
 
	}

	/**
	 * function role : Fetch MS English field details using unique field ids
	 * @return : array, Table field details
	 * 
	 * */

	public function getFieldsInfo()
	{

		$fieldInfoArr = array();
		$this->dbEnglish->Select('fieldID,fieldName');
    	$this->dbEnglish->from('tableFieldsMaster');
    	$query = $this->dbEnglish->get();
    	$queryArr = $query->result_array();
    	foreach ($queryArr as $key => $value) {
    		$fieldInfoArr[$value['fieldID']] =$value['fieldName'] ;
    	}

    	return $fieldInfoArr;
 
	}

	/**
	 * function role : Get information related to all home page components
	 * @param1   userID
	 * @return : array,home page information
	 * 
	 * */

	function getHomePageInfo($userID)
	{
		$this->getQuote();
		return $this->homepageInfo;
	}

	/**
	 * function role : Get information related to quotes
	 * @return : none
	 * 
	 * */

	function getQuote()
	{
		$date1 = new DateTime('2015-05-12');
		$date2 = new DateTime();
		
		$interval = $date1->diff($date2);
		$difference =  $interval->format('%a');

		$this->dbEnglish->Select('count(*) as totalQuotes');
    	$this->dbEnglish->from('quotesMaster');
    	$query = $this->dbEnglish->get();
    	$countArr = $query->result_array();
    	$totalQuotes = $countArr[0]['totalQuotes'];

    	$srno = ($difference%$totalQuotes) + 1; 
		$this->dbEnglish->Select('Quote,Author');
    	$this->dbEnglish->from('quotesMaster');
    	$this->dbEnglish->where('srno',$srno);
    	$query = $this->dbEnglish->get();
    	$quotesInfo = $query->result_array();
    	$this->homepageInfo['Quote'] = $quotesInfo[0]['Quote'];
    	$this->homepageInfo['Author'] = $quotesInfo[0]['Author'];

	}

	/**
	 * function role : Computing user performance of user for passed qcodes
	 * param1 : userID
	 * param2 : qcodes
	 * @return : float, performance stats
	 * 
	 * */


	function getUserPerformance($userID,$qcodes)
	{
		$this->dbEnglish->_protect_identifiers=false;
		$qcodeArr = explode(',', $qcodes);
		$this->dbEnglish->Select(" sum( if ( correct>=0.5 , 1 , 0 ) ) as totalCorrect,count( srno ) as totalAttempted");
    	$this->dbEnglish->from($this->questionAttemptClassTbl);
    	$this->dbEnglish->where('userID',$userID);
    	$this->dbEnglish->where_in('qcode',$qcodeArr);
    	$query = $this->dbEnglish->get();
    	$queryArr = $query->result_array();
    	$x =  $queryArr[0]['totalCorrect'];
    	$y =  $queryArr[0]['totalAttempted'];
    	$z = count($qcodeArr);

    	$performace = ((1/$z)*($x+($x/$z)*($z-$y)))*100;

    	return $performace;
	}

	   /**
	 * function role : Multi explode php array using generice explode available in php
	 * param1 : Delimiters
	 * param2 : generic String
	 * @return : array, exploded on passed deimiters
	 * 
	 * */

    public function multiexplode ($delimiters,$string) 
    {
	    $arr = explode($delimiters[0],$string);
	    array_shift($delimiters);
	    if($delimiters != NULL) {
	        foreach($arr as $key => $val) {
	             $arr[$key] = $this->multiexplode($delimiters, $val);
	        }
	    }
	    return  $arr;
	}
	
	function setTeacherPendingEssayCount($userID)
	{

		$getEssayDetail=$this->dbEnglish->query("SELECT count(*) as totalEssayPendingCnt from ews_essayScoring a , ews_essayDetails b ,essayMaster c where a.essayID=b.essayID and b.topicID=c.essayID and a.evaluatorID=$userID and a.status=0");
		$queryArr = $getEssayDetail->row();
    	$totalEssayPendingCnt =  $queryArr->totalEssayPendingCnt;
		return $totalEssayPendingCnt;
	}
	/**
	 * function description : This function will check whether essay is active or not for current user. if active then show notification in sidebar.
	 * @return  return  title if essay is available for student. return 0 if no active essay.
	 * 
	 * */
        function getAssignedEssayCount(){
            $count=0;
            $class=$this->child_class;
            $section=$this->child_section;
            $school_code=$this->school_code;
            $userID=$this->user_id;
            
            $this->dbEnglish->Select('teacherEssayActivation.essayID , essayMaster.essayTitle');
            $this->dbEnglish->from('teacherEssayActivation');
            $this->dbEnglish->join('essayMaster',"teacherEssayActivation.essayID = essayMaster.essayID",'INNER');
            $this->dbEnglish->where('activationDate<=CURDATE()');
            $this->dbEnglish->where('deactivationDate>=CURDATE()');
            $this->dbEnglish->where('isActive',1);
            $this->dbEnglish->where('class',$class);
            $this->dbEnglish->where('section',$section);
            $this->dbEnglish->where('schoolCode',$school_code);
            $this->dbEnglish->limit(1);
            $query = $this->dbEnglish->get();
            $queryResult = $query->result_array();
            
            if(isset($queryResult[0]) && !empty($queryResult)){
                $this->dbEnglish->Select('essayID,status');
                $this->dbEnglish->from('ews_essayDetails');
                $this->dbEnglish->where('topicID',$queryResult[0]['essayID']);
                $this->dbEnglish->where('userID',$userID);
                $queryEssayDetail = $this->dbEnglish->get();
                $queryEssayDetailResult = $queryEssayDetail->result_array();
                if(isset($queryEssayDetailResult[0]['essayID']) && !empty($queryEssayDetailResult)){
                    if($queryEssayDetailResult[0]['status']==0){
                        $count=$queryResult[0]['essayTitle'];
                    }else{
                        $count=0;
                    }
                }else{
                    $count=$queryResult[0]['essayTitle'];
                }
            }else{
                $count=0;
            }
            return $count;
        }
         /**
	 * function description : This function will get skill o meter data from DB.
         * param1 : userid
         * param2 : startdate
         * param3 : enddate
	 * @return  return reading and accuracy of reading, Listing, grammar & vocab with their accuracy in json
	 * 
	 * */
        function getSkillometer($userid,$startDate,$endDate){
            $passageListenData=$this->getUserReadListenPsgDetailsAndAcc($userid,$startDate,$endDate);
            $grammarVocabData=$this->getGrammarVocabDetailsAndAccuracy($userid,$startDate,$endDate);
            $skillometer = array_merge($passageListenData, $grammarVocabData);
            return $skillometer;
        }
        
        
        /**
	 * function description : This function will passage and listing data with accuracy required in skillometer function.
         * param1 : userid
         * param2 : startdate
         * param3 : enddate
	 * @return  return reading and accuracy of reading, Listing with their accuracy
	 * 
	 * */
        function getUserReadListenPsgDetailsAndAcc($userid,$startDate,$endDate){
                $class =$this->child_class;
	 	$tbl_quesAttempt=$this->questionAttemptTblName.$class;	
                $usageArr=array();
		$this->dbEnglish->Select('qa.userID,count(srno) AS totalQuesAttempted,count(DISTINCT qa.passageID) as passageAttemptCount ,sum(if(correct=1,1,0)) as correct,passageType', false);
	 	$this->dbEnglish->from($tbl_quesAttempt." qa");
	 	$this->dbEnglish->join('passageMaster pm','qa.passageID = pm.passageID','INNER');
	  	$this->dbEnglish->where('qa.userID',$userid);
	 	$this->dbEnglish->where('DATE(qa.attemptedDate) >=',$this->getIntDate($startDate));
	 	$this->dbEnglish->where('DATE(qa.attemptedDate) <=',$this->getIntDate($endDate));
	 	$this->dbEnglish->group_by("qa.userID");
                $this->dbEnglish->group_by("pm.passageType");
		$query = $this->dbEnglish->get();
		$userDataArr = $query->result_array();
                $passageData=array();
                $listeningData=array();
                foreach($userDataArr as $userData){
                    if($userData['passageType']=="Conversation"){
                        $listeningData['totalQuesAttempted']= $userData['totalQuesAttempted'];
                        $listeningData['passageAttemptCount']= $userData['passageAttemptCount'];
                        $listeningData['correct']= $userData['correct'];
                    }else{                 
                        //for multiple row sum - PassageType = illistrator, textual
                        $passageData['totalQuesAttempted'] +=$userData['totalQuesAttempted'];
                        $passageData['passageAttemptCount'] +=$userData['passageAttemptCount'];
                        $passageData['correct'] +=$userData['correct'];
                    }
                }               
		$totalReadPsg=0;
		$totalReadQues=0;
		$totalReadCorrect=0;
		$accuracyRead=0;		
                $totalReadQues = $passageData['totalQuesAttempted'];
                $totalReadPsg= $passageData['passageAttemptCount'];
                if($passageData['correct']==NULL || $passageData['correct']==''){
                        $passageData['correct']=0;
                }
                $totalReadCorrect=$passageData['correct'];			
                $accuracyRead = ($totalReadQues > 0)? round($totalReadCorrect*100/$totalReadQues, 1) : 0;
                $usageArr['readTotalPsgRead'] =intval($totalReadPsg);
                $usageArr['readQuesAcc'] =$accuracyRead;
		//Listening
		$totalListenPsg=0;
		$totalListenQues=0;
		$totalListenCorrect=0;
		$accuracyListen=0;	
                $totalListenQues = $listeningData['totalQuesAttempted'];
                $totalListenPsg= $listeningData['passageAttemptCount'];
                if($listeningData['correct']==NULL || $listeningData['correct']==''){
                        $listeningData['correct']=0;
                }
                $totalListenCorrect=$listeningData['correct'];			
                $accuracyListen = ($totalListenQues > 0)? round($totalListenCorrect*100/$totalListenQues, 1) : 0;

                $usageArr['listenTotalPsgRead'] = intval($totalListenPsg);
                $usageArr['listenQuesAcc'] =$accuracyListen;

		return $usageArr;	
        }
        /**
	 * function description : This function will grammar and vocab data with accuracy required in skillometer function.
         * param1 : userid
         * param2 : startdate
         * param3 : enddate
	 * @return  return reading and accuracy of grammar, vocab with their accuracy
	 * 
	 * */
        function getGrammarVocabDetailsAndAccuracy($userid,$startDate,$endDate) {
            $class =$this->child_class;
            $tbl_quesAttempt = $this->questionAttemptTblName . $class;
            $this->dbEnglish->Select('qa.userID,qs.topicID,count(srno) AS totalQuesAttempted, sum(if(correct=1,1,0)) as correct', false);
            $this->dbEnglish->from($tbl_quesAttempt . " qa");
            $this->dbEnglish->join('questions qs', 'qa.qcode = qs.qcode', 'INNER');
            $this->dbEnglish->where('qs.passageID', 0);
            $this->dbEnglish->where('qa.userID', $userid);
            $this->dbEnglish->where('DATE(qa.attemptedDate) >=', $this->getIntDate($startDate));
            $this->dbEnglish->where('DATE(qa.attemptedDate) <=', $this->getIntDate($endDate));
            $this->dbEnglish->group_by("qa.userID");
            $this->dbEnglish->group_by("qs.topicID");
            $query = $this->dbEnglish->get();
            $userDataArr = $query->result_array();
            $usageArr = array();
            $grammarData=array();
            $vocabData=array();
            foreach($userDataArr as $userData){
                if($userData['topicID']=="1"){
                    $grammarData['totalQuesAttempted'] =$userData['totalQuesAttempted'];
                    $grammarData['correct'] =$userData['correct'];
                }else{
                    $vocabData['totalQuesAttempted'] =$userData['totalQuesAttempted'];
                    $vocabData['correct'] =$userData['correct'];
                }
            }
            $grammarTotalQues = 0;
            $grammarTotalCorrect = 0;
            $grammarQuesAcc = 0;
            $grammarTotalQues = $grammarData['totalQuesAttempted'];
            if ($grammarData['correct'] == NULL || $grammarData['correct'] == '') {
                $grammarData['correct'] = 0;
            }
            $grammarTotalCorrect = $grammarData['correct'];
            $grammarQuesAcc = ($grammarTotalQues > 0) ? round($grammarTotalCorrect * 100 / $grammarTotalQues, 1) : 0;
            $usageArr['grammarTotalQues'] = intval($grammarTotalQues);
            $usageArr['grammarQuesAcc'] = $grammarQuesAcc;
            //vocab
            $vocabTotalQues = 0;
            $vocabTotalCorrect = 0;
            $vocabQuesAcc = 0;
            $vocabTotalQues = $vocabData['totalQuesAttempted'];
            if ($vocabData['correct'] == NULL || $vocabData['correct'] == '') {
                $vocabData['correct'] = 0;
            }
            $vocabTotalCorrect = $vocabData['correct'];
            $vocabQuesAcc = ($vocabTotalQues > 0) ? round($vocabTotalCorrect * 100 / $vocabTotalQues, 1) : 0;
            $usageArr['vocabTotalQues'] = intval($vocabTotalQues);
            $usageArr['vocabQuesAcc'] = $vocabQuesAcc;
            return $usageArr;
        }


        function spellCheckupdate($spellCheckupdate)
        {
        	$data = array('spellCheck' =>$spellCheckupdate);
        	$this->dbEnglish->where('userID', $this->user_id);
			$this->dbEnglish->update('userCurrentStatus', $data);
			$this->session->set_userdata('spellCheck', $spellCheckupdate);
        }
}

?>