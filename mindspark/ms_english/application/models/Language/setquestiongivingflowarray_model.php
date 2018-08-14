<?php
Class Setquestiongivingflowarray_model extends MY_Model
{
	public function __construct() 
	{
		 parent::__construct();
		 $this->dbEnglish = $this->load->database('mindspark_english',TRUE);
		 $this->load->library('session');
	}

	// This function is used to load Next content type to give Array 
	// If $flowArray is Empty means weekly limit is over and "End" is inserted in the array
	public function setSessionToGiveArray($userID){

		$flowArray = $this->getWeeklyUserAttempt($userID);
		if(count($flowArray))
			$this->session->set_userdata('NextSessionToShow', $flowArray);
		else
		{
			$flowArray = array(0 =>"End");
			$this->session->set_userdata('NextSessionToShow', $flowArray);
		}
	}

	// This Function check weekly Attempt and last 2 attempt of user And return question flow accordiengly 
	// $questionFlowArr contain the Question type to give Array  which is being returned 
	public function getWeeklyUserAttempt($userID)
	{
			$this->load->model('Language/questionspage_model');
			$weeklyFreeQuesAttemptCountArr = array();
			$arrWeeklyPendingData = array();
			$UserCurrentStatus = array();
			$passageAttempt = array();
			$passageArray = array();
			$passageAttemptID = array();
			$passageToGive = array();
			$questionFlowArr = array();

			$this->dbEnglish->Select("contentType,weeklyLimit");
			$this->dbEnglish->from("contentWeeklyLimitMaster");
			$resultQuery = $this->dbEnglish->get();
			$weeklyLimitArr = $resultQuery->result_array();
		
			foreach ($weeklyLimitArr as $key => $value)
			 {
				if($value['contentType']=="Reading")
					$weeklyReadingLimit = $value['weeklyLimit']; 
				elseif ($value['contentType']=="Conversation")
					$weeklyConversationLimit = $value['weeklyLimit']; 
				else
					$weeklyFreeQuesLimit =  $value['weeklyLimit'];
			}

			$freeQuesFlowLimit = freeQuestionToGivePerCycle;
			//echo $weeklyReadingLimit = 5;      // change here make it weeklyReading constant Aditya
			//echo $weeklyConversationLimit= 5;
			//echo $weeklyFreeQuesLimit = 10;
			
			$weeklyReadPending = 0;
			$weeklyConvPending = 0;
			$weeklyFreeQuesPending=0;

			if(date('N')!=7){
				$startDate =  date("Y-m-d", strtotime('monday this week'));   
				$endDate   =  date("Y-m-d", strtotime('monday next week'));
				$endDate2  =  date("Y-m-d", strtotime('sunday this week'));
			 }
			 else
			 {
			 	$startDate =  date("Y-m-d", strtotime('monday previous week'));   
				$endDate   =  date("Y-m-d", strtotime('monday this week'));
				$endDate2  =  date("Y-m-d", strtotime('sunday previous week'));
			 }
				
		 	$startDate = ''.$startDate.'';
			$endDate = ''.$endDate.'';


			$query=$this->dbEnglish->query("SELECT childClass from userDetails where userId=".$userID."");
			$this->class = $query->row();
			$this->questionAttemptClassTbl="questionAttempt_class".$this->class->childClass;
	      	
			$freeQuestionAttempt = $this->dbEnglish->query("SELECT count(*) as count from ".$this->questionAttemptClassTbl." WHERE userID = ".$userID." and questionType = 'freeQues'  and  lastModified> '".$startDate."' and lastModified<'".$endDate."'");
			//print $this->dbEnglish->last_query();

			$weeklyFreeQuesAttemptCountArr =  $freeQuestionAttempt->result_array();
			$weeklyFreeQuesAttemptCount = $weeklyFreeQuesAttemptCountArr[0]['count'];
			//$tmpStr1="SELECT count(*) as count from ".$this->questionAttemptClassTbl." WHERE userID = ".$userID." and questionType = 'freeQues'  and  lastModified> '".$startDate."' and lastModified<'".$endDate."'";
		
			$passageAttempt = $this->dbEnglish->query("SELECT count(*) as count , pm.passageType from passageAttempt pa inner join  passageMaster pm on pm.passageID = pa.passageID  where userID = '$userID' and pa.completed = '2' and pa.lastModified> '$startDate' and pa.lastModified< '$endDate' group by pm.passageType");
			$passage = $passageAttempt->result_array();
			//$tmpStr2="SELECT count(*) as count , pm.passageType from passageAttempt pa inner join  passageMaster pm on pm.passageID = pa.passageID  where userID = '$userID' and pa.completed = '2' and pa.lastModified> '$startDate' and pa.lastModified< '$endDate' group by pm.passageType";
			

			$passageTypeCount['Conversation'] = 0;
			$passageTypeCount['reading'] = 0;
			$countConv=0;
			$countRead=0;
			foreach ($passage as $key => $value) {
				  if($value['passageType'] == 'Conversation')
				  	   $countConv = $value['count'];
				  	else
				  		$countRead += $value['count'];
			}
			$passageTypeCount['reading']=$countRead;
			$passageTypeCount['Conversation']=$countConv;
			$weeklyReadAttemptCount = $passageTypeCount['reading'];
			$weeklyConvAttemptCount = $passageTypeCount['Conversation'];
				
			$userCurrentStatusArr = $this->currentOngoingQtype($userID);    					// detail of current pending question from userCurrentStatus
			$curContentType=$userCurrentStatusArr[0]['currentContentType'];
			$curRefID=$userCurrentStatusArr[0]['refID'];
			$curIDCompleted=$userCurrentStatusArr[0]['completed'];
			$curQuestionType='';
				
			if($curRefID==0){
			}else{
				if($curContentType!="passage" )
				{	
					$refCodeDetails =$this->questionspage_model->getQcodePassageDetails($curRefID);
					if($refCodeDetails['qcodePassageID']!=0)
					{
						$currentPassageID = $refCodeDetails['qcodePassageID'];
						$currentPassageQuesID = $curRefID;
						$currentPassageType = $refCodeDetails['passageType'];
						$tempArr['passageQuesPending'] = $this->setCurrentPsgQuestions($userID,$currentPassageID);
						if($currentPassageType=='Textual'||$currentPassageType=='Illustrated'){
							$curQuestionType='Reading';
							if($tempArr['passageQuesPending']){
								$questionFlowArr[] = "Reading"; // $questionFlowArr contain the Question type to give Array 
								$weeklyReadAttemptCount++;
							}
						}else{
							$curQuestionType='Conversation';
							if($tempArr['passageQuesPending']){
								$questionFlowArr[] = "Conversation"; // $questionFlowArr contain the Question type to give Array 
								$weeklyConvAttemptCount++;					
							}
						}
					}else {
						$currentFreeQuesID=$curRefID;
						$curQuestionType='free_ques';
					}
				}else{
					$currentPassageID=$curRefID;
					$tempPsgType = $this->questionspage_model->getPassageType($currentPassageID);
					$currentPassageType =$tempPsgType;
					$tempArr['passageQuesPending'] = $this->setCurrentPsgQuestions($userID,$currentPassageID);

					if($tempPsgType=='Textual'||$tempPsgType=='Illustrated'){
						$curQuestionType='Reading';
						if($tempArr['passageQuesPending']){
							$questionFlowArr[] = "Reading";  // $questionFlowArr contain the Question type to give Array 
							$weeklyReadAttemptCount++;
						}
						
					}else{
						$curQuestionType='Conversation';
						if($tempArr['passageQuesPending']){
							$questionFlowArr[] = "Conversation";  // $questionFlowArr contain the Question type to give Array 
							$weeklyConvAttemptCount++;					
						}
					}
				}
			}

			if($weeklyReadAttemptCount<$weeklyReadingLimit&& ($weeklyReadingLimit-$weeklyReadAttemptCount >0))
				$weeklyReadPending = $weeklyReadingLimit-$weeklyReadAttemptCount;
				
				
			if($weeklyConvAttemptCount<$weeklyConversationLimit&&($weeklyConversationLimit-$weeklyConvAttemptCount>0))
				$weeklyConvPending = $weeklyConversationLimit-$weeklyConvAttemptCount;
				
			if($weeklyFreeQuesAttemptCount<$weeklyFreeQuesLimit&&($weeklyFreeQuesLimit - $weeklyFreeQuesAttemptCount>0))
				$weeklyFreeQuesPending = $weeklyFreeQuesLimit - $weeklyFreeQuesAttemptCount;

			$arrWeeklyPendingData['passageCount'] = $weeklyReadAttemptCount;
			$arrWeeklyPendingData['conversationCount'] = $weeklyConvAttemptCount;
			$arrWeeklyPendingData['weeklyFreeQuesAttemptCount']  = $weeklyFreeQuesAttemptCount;
			
			if($weeklyReadPending<0){
				$weeklyReadPending=0;
			}
			if($weeklyConvPending<0){
				$weeklyConvPending=0;
			}
			if($weeklyFreeQuesPending<0){
				$weeklyFreeQuesPending=0;
			}
			
			$userLastAttemptArr  = array();
			$getLastAttemptByUser = $this->dbEnglish->query("SELECT b.passageTypeName as passageType FROM ".$this->questionAttemptClassTbl." a,questions b where a.qcode=b.qcode and a.userID='$userID' group by  b.passageID,b.passageTypeName order by MAX(a.lastModified) desc limit 2");
			$passageTypeAttempt = $getLastAttemptByUser->result_array();

			$userLastAttemptArr  = array();
			if(count($passageTypeAttempt)==2)
			{
				$userLastAttemptArr  = array();
				foreach ($passageTypeAttempt as $row)
				{	
					$valType= $this->contentType($row['passageType']);				
					array_push($userLastAttemptArr, $valType);
				}
				$userLastAttemptArr=array_reverse($userLastAttemptArr);
				$tmpLastToLastAttempt=$userLastAttemptArr[0];
				$tmpLastAttempt=$userLastAttemptArr[1];
				if($curContentType=='passage'){
					$userLastAttemptArr[0]=$tmpLastAttempt;
					if($curQuestionType=='Conversation'){
							$userLastAttemptArr[1]="C";
					}else if($curQuestionType=='Reading'){
							$userLastAttemptArr[1]="R";
					}
				}else if ($curContentType=='passage_ques'){	
					$refCodeDetails =$this->questionspage_model->getQcodePassageDetails($curRefID);
					if($refCodeDetails['qcodePassageID']!=0)
					{					
						$currentPassageID = $refCodeDetails['qcodePassageID'];
						$checkPsgQuesAttempt=$this->userAttemptedPsgQues($currentPassageID,$userID);
						if($checkPsgQuesAttempt){
							$userLastAttemptArr[0]=$tmpLastToLastAttempt;						
						}else{
							$userLastAttemptArr[0]=$tmpLastAttempt;						
						}
						if($curQuestionType=='Conversation'){
							$userLastAttemptArr[1]="C";
						}else if($curQuestionType=='Reading'){
							$userLastAttemptArr[1]="R";
						}
					}			
				}else if ($curContentType=='free_ques'){				
					if($tmpLastAttempt=="F"){
						$userLastAttemptArr[0]=$tmpLastToLastAttempt;					
					}else{
						$userLastAttemptArr[0]=$tmpLastAttempt;
					}
					$userLastAttemptArr[1]="F";					
				}			
			}
			else if(count($passageTypeAttempt)==1)
			{	
				$userLastAttemptArr  = array();
				array_push($userLastAttemptArr, 'NA');						
				array_push($userLastAttemptArr, $this->contentType($passageTypeAttempt[0]['passageType']));
					
				$tmpLastAttempt=$userLastAttemptArr[1];
				if($curContentType=='passage'){
					$checkPsgQuesAttempt=$this->userAttemptedPsgQues($curRefID,$userID);
					if($checkPsgQuesAttempt){
						$userLastAttemptArr[0]='NA';	
						$userLastAttemptArr[1]=$tmpLastAttempt;						
					}else{
						$userLastAttemptArr[0]=$tmpLastAttempt;
						if($curQuestionType=='Conversation'){
								$userLastAttemptArr[1]="C";
						}else if($curQuestionType=='Reading'){
								$userLastAttemptArr[1]="R";
						}						
					}				
					
				}else if ($curContentType=='passage_ques'){	
					$refCodeDetails =$this->questionspage_model->getQcodePassageDetails($curRefID);
					if($refCodeDetails['qcodePassageID']!=0)
					{					
						$currentPassageID = $refCodeDetails['qcodePassageID'];
						$checkPsgQuesAttempt=$this->userAttemptedPsgQues($currentPassageID,$userID);
						if($checkPsgQuesAttempt){						
							$userLastAttemptArr[0]='NA';	
							$userLastAttemptArr[1]=$tmpLastAttempt;							
						}else{
							$userLastAttemptArr[0]=$tmpLastAttempt;
							if($curQuestionType=='Conversation'){
									$userLastAttemptArr[1]="C";
							}else if($curQuestionType=='Reading'){
									$userLastAttemptArr[1]="R";
							};						
						}					
					}				
				}else if ($curContentType=='free_ques'){
					$userLastAttemptArr[0]=$tmpLastAttempt;				
					$userLastAttemptArr[1]="F";					
				}
			}else if(count($passageTypeAttempt)==0)
			{				
				if($curContentType=="N/A"){
					array_push($userLastAttemptArr, 'N/A');
					array_push($userLastAttemptArr, 'N/A');
				}else{
					array_push($userLastAttemptArr, 'NA');
					if($curContentType=='passage' || $curContentType=='passage_ques'){
						if($curQuestionType=='Conversation'){
								array_push($userLastAttemptArr, "C");
						}else if($curQuestionType=='Reading'){							
								array_push($userLastAttemptArr, "R");
						}
					}else if ($curContentType=='free_ques'){					
						array_push($userLastAttemptArr, "F");
					}
				}	
			}

		//$contentFlowArr =  array('R','C','F');    
		$contentFlowArr =  $this->getContentFlow();   // get Content flow Array from database.
		$counter = 0;
			
		if($curContentType!="N/A"){
			if(count($userLastAttemptArr)>=1){
				if($this->generateArrayFlow($userLastAttemptArr,$contentFlowArr,$counter))
					$finalContentFlowArr =  $this->generateArrayFlow($userLastAttemptArr,$contentFlowArr,$counter);
				else
				{
					$arrLast = array_slice($contentFlowArr, array_search($userLastAttemptArr[1],$contentFlowArr)+1);
					$arrInit = array_slice($contentFlowArr, 0,array_search($userLastAttemptArr[1],$contentFlowArr)+1);
					$finalContentFlowArr = array_merge($arrLast,$arrInit);
				}
			}
			else{
				$finalContentFlowArr = $contentFlowArr;
			}
		}else{
			$finalContentFlowArr = $contentFlowArr;
		}
			$this->user_id=$userID;
			$questionFlowArr =  $this->getQuestionFlowArray($userID,$curQuestionType,$weeklyConvPending,$weeklyFreeQuesPending,$weeklyReadPending,$weeklyFreeQuesLimit,$weeklyFreeQuesAttemptCount,$freeQuesFlowLimit,$finalContentFlowArr);

			return $questionFlowArr;
	}
	
	// This Function generates guestion to give Array return empty array if weekly limit is over  
	public function getQuestionFlowArray($userID,$lastAttemptQtype,$weeklyConvPending,$weeklyFreeQuesPending,$weeklyReadPending,$weeklyFreeQuesLimit,$weeklyFreeQuesAttemptCount,$freeQuesFlowLimit,$contentFlowArr)
	{
			$passageToGive['reading'] = $weeklyReadPending;
			$passageToGive['conversation'] = $weeklyConvPending;
			$freeQuesToGive = $weeklyFreeQuesPending;
		   	
		   	$freeQuesToGiveCount=0;
		   	$convGiveCount=0;
		   	$readGiveCount=0;
			if($lastAttemptQtype=="free_ques"){
				$totalF=$freeQuesFlowLimit - $weeklyFreeQuesAttemptCount%$freeQuesFlowLimit;
				
				if($totalF<$freeQuesFlowLimit){
					if($freeQuesToGive >= $freeQuesToGiveCount)
					{
						while($totalF)
						{
							$questionFlowArr[] = "FreeQuestion";
							$totalF--;
							$freeQuesToGiveCount++;
							$weeklyFreeQuesAttemptCount++;	
						}
					}else{
						break;
					}
				}	
			}

			foreach ($contentFlowArr as $row)
			{	
				if($row=="R")
				{	
					if($passageToGive['reading']>$readGiveCount){
						$questionFlowArr[] = "Reading";
						$readGiveCount++;
					}else{
						break;
					}
				}
				else if($row=="C")
				{
					if($passageToGive['conversation']>$convGiveCount){
						$questionFlowArr[] = "Conversation";
						$convGiveCount++;
					}else{
						break;
					}
				}
				else if($row=="F")
				{			
					$totalF=$freeQuesFlowLimit - $weeklyFreeQuesAttemptCount%$freeQuesFlowLimit;
					
					if($freeQuesToGive>$freeQuesToGiveCount)
					{
						while($totalF)
						{
							$questionFlowArr[] = "FreeQuestion";
							$totalF--;
							$freeQuesToGiveCount++;
							$weeklyFreeQuesAttemptCount++;
						}
					}else{
						break;
					}
				}		
			}
		    return $questionFlowArr;	
	}

	// return current qtype and ID and sompleted status 
	public function currentOngoingQtype($userID)
 	{
 		$this->dbEnglish->Select('currentContentType,refID,completed');
	 	$this->dbEnglish->from('userCurrentStatus');
	 	$this->dbEnglish->where('userID',$userID);
	 	$query = $this->dbEnglish->get();
	 	$userCurrentQtype = $query->result_array();
	 	return $userCurrentQtype; 	
 	}

 	// Function is used to return currennt passage Question if finised empty array is returned 
 	// Useful to check if current passage is completed or not to genrate questionFlowArray
 	public function setCurrentPsgQuestions($userID,$passageID=false,$chkAttemptedQues=false)
	{

		if(!$passageID){
			$this->dbEnglish->Select('passageID');
			$this->dbEnglish->from('questions');
			$this->dbEnglish->where('qcode',$this->session->userdata('refID'));
			$this->dbEnglish->where('status',liveQuestionsStaus);
			$getPassageIDSql = $this->dbEnglish->get();
			$psgQuestionsArr = $getPassageIDSql->row();
			$passageID=$psgQuestionsArr->passageID;
		}
		
		$attemptedPassageQuestionsArr = array(0=>'');
			$attemptedPassageQuestions = $this->questionspage_model->getUserAttemptedPassageQuestions($userID,$passageID);
			if($attemptedPassageQuestions!="")
				$attemptedPassageQuestionsArr = explode(',', $attemptedPassageQuestions);
		
		$this->dbEnglish->_protect_identifiers = FALSE;
		$this->dbEnglish->Select('qcode');
		$this->dbEnglish->from('questions');
		$this->dbEnglish->where('passageID',$passageID);
		$this->dbEnglish->where('status',liveQuestionsStaus);
		$this->dbEnglish->where_not_in('qcode',$attemptedPassageQuestionsArr);
		$this->dbEnglish->order_by("FIELD(qType,'openEnded')");
		$getPsgQuestionsSql = $this->dbEnglish->get();
		$psgQuestionsRes = $getPsgQuestionsSql->result_array();
		$this->dbEnglish->_protect_identifiers = TRUE;
		
		$psgQuestionsArr=array();
		
		foreach($psgQuestionsRes as $key=>$value)
		{
			array_push($psgQuestionsArr,$value['qcode']);
		}

		if(count($psgQuestionsRes)>0){
			return $psgQuestionsRes;
		}else{
			return false;
		}		
	}

	function userAttemptedPsgQues($passageID,$userID)
	{
		$this->dbEnglish->Select('*');
		$this->dbEnglish->from($this->questionAttemptClassTbl);
		$this->dbEnglish->where('passageID',$passageID);
		$this->dbEnglish->where('userID',$userID);
		$query = $this->dbEnglish->get();
		if($query->num_rows() > 0){			
			return true;	
		}else{
			return false;
		}	
	}

	function contentType($type){
		if($type=='0'){
			return 'F';			
		}else if($type=='Textual'){
			return 'R';		
		}else if($type=='Illustrated'){
			return 'R';		
		}else if($type=='Conversation'){
			return 'C';		
		}
	}

	// Function generate flow pattern to give according to last 2 attempt of the user ex.(C-F-R)
	function generateArrayFlow($userLastAttemptArr,$contentFlowArr,$counter)
	{		
		if(in_array($userLastAttemptArr[1],array_slice($contentFlowArr,array_search($userLastAttemptArr[0], $contentFlowArr)+1)))
			$val = array_search($userLastAttemptArr[1],array_slice($contentFlowArr,array_search($userLastAttemptArr[0], $contentFlowArr)+1));
		else
			$val = 1;

		if($counter==count($contentFlowArr))
			return 0; 
		
		if(in_array($userLastAttemptArr[0], $contentFlowArr) && !$val){
			$arrStartIndx =  array_search($userLastAttemptArr[0],$contentFlowArr)+2;
			for($i=0;$i<count($contentFlowArr);$i++)
			{
				$outArr[$i] = $contentFlowArr[$arrStartIndx%count($contentFlowArr)];
				$arrStartIndx++; 
			}
			return $outArr;
		}
		else{
				$arrLast = array_slice($contentFlowArr, array_search($userLastAttemptArr[0],$contentFlowArr)+1);
				$arrInit = array_slice($contentFlowArr, 0,array_search($userLastAttemptArr[0],$contentFlowArr)+1);
				$contentFlowArr = array_merge($arrLast,$arrInit);
				$counter++;
				 $this->generateArrayFlow($userLastAttemptArr,$contentFlowArr,$counter);
		   }
	}
	
	// Function return the content flow to give (R-F-C) from DB
	function getContentFlow()
	{
		$this->dbEnglish->Select("contentType1,contentType2,contentType3");
		$this->dbEnglish->from("contentFlowMaster");
		$contentFlowQuery = $this->dbEnglish->get();
		$contentFlowArr = $contentFlowQuery->result_array();
		return array_values($contentFlowArr[0]);
	}
}



?>
