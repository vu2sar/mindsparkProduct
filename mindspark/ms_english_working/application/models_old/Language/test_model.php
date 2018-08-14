<?php
Class test_model extends CI_Model
{
	
	public function __construct()
	{
		 parent::__construct();
		 $this->dbEnglish = $this->load->database('mindspark_english',TRUE);
		 $this->load->library('session');
		 $this->class=0;
		 //$userID=500010;
	}
	public function intiallazeVarAndCallFunc($selectedGroupID,$userID,$qAttempt1="",$qAttempt2="")
	{
		$query=$this->dbEnglish->query("select childClass from userDetails where userId=".$userID."");
		$this->class = $query->row();
		$this->questionAttemptClassTbl="questionAttempt_class".$this->class->childClass;

		$totalQuesForSelectedSkill=7;
		$totalQuesForNonSelectedSkill=3;

		echo '<pre>';
		
		echo "<br>";
		echo $this->questionAttemptClassTbl;
		
		if($qAttempt1==""){
			$attemptedGroupQnsData=$this->getPreviousAttemptedGroupID($userID,$selectedGroupID);
		}else{			
			$arr1=explode("-", $qAttempt1);
			$arr2=explode("-", $qAttempt2);
			$attemptedGroupQnsData=array($arr1[0] => $arr1[1] ,$arr2[0] => $arr2[1]);
			//print_r($attemptedGroupQnsData);
			//exit;
			//$attemptedGroupQnsData=array("2" => 3 ,"5"=> 4);
		}
		
		
		$freeQuesLevel=$this->class->childClass;
		$freeQuesMsLevel=$freeQuesLevel-gradeScallingConst;	
		$addParams="";

		$this->test_model->finalGroupQuesArr ($attemptedGroupQnsData,$selectedGroupID,$totalQuesForSelectedSkill,$totalQuesForNonSelectedSkill,$userID);
	}

	public function finalGroupQuesArr ($attemptedGroupQnsData,$selectedGroupID,$totalQuesForSelectedSkill,$totalQuesForNonSelectedSkill,$userID)
	{
		$arrGroupQues= array();
		$freeQuesArr=array();
		$addParams="";
		$freeQuesLevel=$this->class->childClass;
		$freeQuesMsLevel=$freeQuesLevel-gradeScallingConst;	
		$attemptedFreeQuesArr = $this->test_model->getPreviousAttemptedNCQQues($userID,$freeQuesMsLevel);
			
		if(count($attemptedFreeQuesArr) > 0)
			$addParams=" and qcode NOT IN (".implode(',', $attemptedFreeQuesArr).")";


		$skills=$this->getGroupSkills($selectedGroupID);	
		$checkQuesPending=$this->dbEnglish->query("select qcode,msLevel from questions where skillID IN (".$skills.") and status=".liveQuestionsStaus." and passageID=0 and msLevel=".$freeQuesMsLevel.$addParams);
			//$checkQuesPending=$this->dbEnglish->query("select qcode,msLevel from questions where skillID IN (1,4) and status=6 and passageID=0 and msLevel=2 and qcode NOT IN (6490,5774,8260,7908,7870,7860,27,331,762,763,943,968,969,62,669,883,1570,1572,1575,1587,1610,1612,1616,211,709,713,1617,1627,1691,4064,4065,4066,4067,835,1010,1017,4068,4069,4070,4071,4072,4073,5098,363,379,414,5100,5102,7495,7496,7497,7498,7499,262,288,360,7500,8842,8843,8844,8845,8846,8847,40,674,676,8848,8849,8850,8851,9992,9993,10050)");
		$quesPending=$checkQuesPending->num_rows();

		if($quesPending==0)
		{
			echo "No questions pending";
			exit;
		}

		echo "abc".$addParams."abc";
		echo "<br>";
			print_r($attemptedGroupQnsData);		
		if($attemptedGroupQnsData==NULL){
			$attemptedGroupQnsData=array();		
		}else{

			if(!$changeLevel){

				$attemptedGroupQnsData = array_reverse($attemptedGroupQnsData, true);
			
			}else{
				$attemptedGroupQnsData=array();		
			}
		}
		print_r($attemptedGroupQnsData);
		
		
		if (count($attemptedGroupQnsData)>0){
			
			$key_skill = array_keys($attemptedGroupQnsData);		
			$val_totalQAttempt= array_values($attemptedGroupQnsData);
			if(count($key_skill)==1){
				array_unshift($key_skill,"0"); 
			}
			if(count($val_totalQAttempt)==1){
				array_unshift($val_totalQAttempt,"3"); 
			}
			

			$allGroupSkill = array(1,2,3,4,5,6,7);
			if($key_skill[1]!=$selectedGroupID){
				$val=$key_skill[1]+1;
			}else{
				$val=$key_skill[0]+1;
			}
			if($val==$selectedGroupID){
				$val=$val+1;
			}

			$skillArr = array();
			for ($x = 1; $x <= 7; $x++) {
				if($x>=$val){
					//echo "The number is: $val <br>";
					if (($key = array_search($val, $allGroupSkill)) !== false) {
	   				 	array_push($skillArr,$allGroupSkill[$key]); 
	   				 	$val=$allGroupSkill[$key];   				 
	   				 	unset($allGroupSkill[$key]);
					}
					$val=$val+1;
				}
				
	    	}
	    	

			foreach ($allGroupSkill as $value) {
				array_push($skillArr,$value); 
			}
			if (($key = array_search($selectedGroupID, $skillArr)) !== false) {
	   			 unset($skillArr[$key]);
			}
			
			$skillArr=array_values($skillArr);
		   	echo "Non Selected skill questions will come in below order for this User";
	    	echo "<br>";
			print_r($skillArr);
	    	
			
			if($key_skill[1]==$selectedGroupID && $val_totalQAttempt[1]<=$totalQuesForSelectedSkill){
				if($val_totalQAttempt[1]<$totalQuesForSelectedSkill){			
					$tmpArr=array("skill" => $selectedGroupID ,"totalQuesofSkill"=> $totalQuesForSelectedSkill-$val_totalQAttempt[1]);
					array_push($arrGroupQues,$tmpArr);
				}	
			}
			else
			{
				//echo "recieved";
				if($val_totalQAttempt[1]<$totalQuesForNonSelectedSkill){
					$tmpArr=array("skill" => $key_skill[1] ,"totalQuesofSkill"=> $totalQuesForNonSelectedSkill-$val_totalQAttempt[1]);
					array_push($arrGroupQues,$tmpArr);
				}
				//below case taken for offline scenario in case selected group questions are attempted greater than 7 
				if($key_skill[1]!=$selectedGroupID){
					$tmpArr=array("skill" => $selectedGroupID ,"totalQuesofSkill"=> $totalQuesForSelectedSkill);
					array_push($arrGroupQues,$tmpArr);
				}
						//
					//$tmpArr=array("skill" => $selectedGroupID ,"totalQuesofSkill"=> $totalQuesForSelectedSkill);
					//array_push($arrGroupQues,$tmpArr);
					

			}


		}else{
			$skillArr = array(1,2,3,4,5,6,7);
			if (($key = array_search($selectedGroupID, $skillArr)) !== false) {
	   			 unset($skillArr[$key]);
			}
			$skillArr=array_values($skillArr);
			$tmpArr=array("skill" => $selectedGroupID ,"totalQuesofSkill"=> $totalQuesForSelectedSkill);
			array_push($arrGroupQues,$tmpArr);
		}
		
		foreach ($skillArr as $value) {
				$tmpArr=array("skill" =>$value ,"totalQuesofSkill"=> $totalQuesForNonSelectedSkill);
				array_push($arrGroupQues,$tmpArr);
				$tmpArr=array("skill" => $selectedGroupID ,"totalQuesofSkill"=> $totalQuesForSelectedSkill);
				array_push($arrGroupQues,$tmpArr);
	    		
			}

		//echo "Final flow for this user will be as below";
	   echo "<br>Final skill flow for this user will be as below";

	   echo "<table border=1 style=text-align:center>";
		echo "<tr>";
	 		echo "<th>skill</th>";
	 		echo "<th>totalQuesofSkill</th>";
	  		
		echo "</tr>";
		foreach ($arrGroupQues as $key=>$value){
		 echo "<tr>";
	   	 	echo "<td>";
	   	 		echo $value["skill"];
	   	 	echo "</td>";
	   	 	echo "<td>";
	   	 		echo $value["totalQuesofSkill"]; 
	   	 	echo "</td>";   	 	
	    
	  	echo "</tr>";
		}
		echo "</table>";

		
		$count=0;
		//$attemptedFreeQuesArr = array();
		//$freeQuesLevel=$this->class->childClass;
		//$freeQuesMsLevel=$freeQuesLevel-gradeScallingConst;	
		

		echo $freeQuesMsLevel."a".$addParams."b";
		echo "<br>";
		
		foreach ($arrGroupQues as $value) {
			$skill=  $value["skill"];
			$tQues= $value["totalQuesofSkill"];
			$skills=$this->getGroupSkills($skill);	
			$limit=" limit ".$tQues;
			//echo $check." a ".$totalQuesNotAttempted;
			 if ( $check == 1 ) { 
			 	$check=0;
			 	continue; 
			 }
			echo "select qcode,msLevel from questions where skillID IN (".$skills.") and status=".liveQuestionsStaus." and passageID=0 and msLevel=".$freeQuesMsLevel.$addParams."".$limit;
			echo "<br>";
			$getTotalQuesAtSkill=$this->dbEnglish->query("select qcode,msLevel from questions where skillID IN (".$skills.") and status=".liveQuestionsStaus." and passageID=0 and msLevel=".$freeQuesMsLevel.$addParams."".$limit);
			$totalQuesNotAttempted=$getTotalQuesAtSkill->num_rows();
			//echo $check." a ".$totalQuesNotAttempted;
			//echo "<br>";
			if($totalQuesNotAttempted>0)
			{
				$srNo=1;
				foreach($getTotalQuesAtSkill->result_array() as $row)
				{
					array_push($attemptedFreeQuesArr,$row['qcode']);
					$freeQuesDataArr[$count]['srNo']=$srNo;
					$freeQuesDataArr[$count]['qcode']=$row['qcode'];
					$freeQuesDataArr[$count]['msLevel']=$row['msLevel'];
					//$freeQuesDataArr[$count]['groupCat']="selectedgroup";
					$freeQuesDataArr[$count]['groupNo']=$skill;
					
					$count++;
					$srNo++;
				}
				$addParams=" and qcode NOT IN (".implode(',', $attemptedFreeQuesArr).")";
				//echo "<br>";
				//echo 	$addParams;echo "<br>";
			}

			//echo "select qcode,msLevel from questions where skillID IN (".$skills.") and status=".liveQuestionsStaus." and passageID=0 and msLevel=".$freeQuesMsLevel.$addParams."".$limit;
			//echo "<br>";
			if($selectedGroupID==$skill)
			{	
				if($totalQuesNotAttempted<$tQues){
					#" when less or no No questions pending for this skill - Scenario can also be like when questions is left 3 and question is not pending in next flow";
					break;
				}			
			}else{
				if($totalQuesNotAttempted==0){
					$check=1;
				}else{
					$check=0;
				}
			}
			
		}
		echo "final arr with flow ,skill and questions";
		echo "<br>";
		echo "<br>";
		//print_r( $attemptedFreeQuesArr);


		echo "<table border=1 style=text-align:center>";
		echo "<tr>";
			echo "<th>Srno</th>";
	 		echo "<th>SkillSrno</th>";
	 		echo "<th>qcode</th>";
	  		echo "<th>Mslevel</th>";
	  		echo "<th>groupNo</th>";
		echo "</tr>";
		$No=1;
		foreach ($freeQuesDataArr as $key=>$value){
		 echo "<tr>";
		 	echo "<td>";
	   	 		echo $No;
	   	 	echo "</td>";
	   	 	echo "<td>";
	   	 		echo $value["srNo"];
	   	 	echo "</td>";
	   	 	echo "<td>";
	   	 		echo $value["qcode"]; 
	   	 	echo "</td>";
	   	 	echo "<td>";
	   	 		echo $value["msLevel"];
	   	 	echo "</td>";
	   	 	echo "<td>";
	   	 		echo $value["groupNo"];
	   	 	echo "</td>";    
	  	echo "</tr>";
	  	$No++;
		}
		echo "</table>";

		foreach($freeQuesDataArr as $key=>$value)
		{
			array_push($freeQuesArr,$value['qcode']);
		}	
		



	}

	public function getPreviousAttemptedGroupID($userID,$selectedGroupID=null)
	{
			$limit = "4";  // 3+1(To know the 3 questions skill and 4th questions skill)
			$attemptedGroupQns=0;
			if($selectedGroupID > 0)
				$limit ="8"; // 7+1(To know the 7 group skill and 8th group skill)
			$getPreviousAttGroupDetSql=$this->dbEnglish->query("select q.skillID,p.qcode from ".$this->questionAttemptClassTbl." p,questions q where p.userId=".$userID." and p.passageId=0 and p.qcode=q.qcode order by p.lastModified desc LIMIT ".$limit);
			//echo "select q.skillID,p.qcode from ".$this->questionAttemptClassTbl." p,questions q where p.userId=".$userID." and p.passageId=0 and p.qcode=q.qcode order by p.lastModified desc LIMIT ".$limit;
			//exit;
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
					$groupArr=array_reverse($groupArr);
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
	public function getGroupSkills($no)
	{
		$this->dbEnglish->Select('skilID');
		$this->dbEnglish->from('groupSkillMaster');
		$this->dbEnglish->where('groupSkillID',$no);
		$getGroupSkillsSql = $this->dbEnglish->get();
		$groupSkillData = $getGroupSkillsSql->row();
		return $groupSkillData->skilID;
	}
	public function getPreviousAttemptedNCQQues($userID,$freeQuesMsLevel,$valID='')
	{
		

		$this->dbEnglish->_protect_identifiers = FALSE;
		$this->dbEnglish->Select('A.qcode as qcodes,scoringID,exScoringID');
		$this->dbEnglish->from($this->questionAttemptClassTbl.' A');
		$this->dbEnglish->from('questions B');
		$this->dbEnglish->where('A.qcode = B.qcode');
		$this->dbEnglish->where('userID',$userID); 
		$this->dbEnglish->where('A.questionType','freeQues'); 
		$this->dbEnglish->where('B.passageID',0);
		$this->dbEnglish->where('B.msLevel',$freeQuesMsLevel);

		if($valID!=''){
			if($this->session->userdata('isFreeQuesContentExhaust'))
				$this->dbEnglish->where('exScoringID IS NOT NULL');
			else
				$this->dbEnglish->where('scoringID IS NOT NULL');

		}
		
		$query = $this->dbEnglish->get();
		$resultArr = $query->result_array();
		print $this->dbEnglish->last_query(); 
		$this->dbEnglish->_protect_identifiers = TRUE;
		$NCQQuesAttemptedArr = array();
		
		foreach($resultArr as $key=>$value)
		{
			array_push($NCQQuesAttemptedArr,$value['qcodes']);
			if($this->session->userdata('isFreeQuesContentExhaust')){
				if($this->currFreeQuesExScoreID == $value['exScoringID'] && !in_array($value['qcodes'], $this->currExScoreIDFreeQuesArr))
					array_push($this->currExScoreIDFreeQuesArr,$value['qcodes']);
			}
			else{
				if($this->currFreeQuesScoreID == $value['scoringID'] && !in_array($value['qcodes'], $this->currScoreIDFreeQuesArr))
					array_push($this->currScoreIDFreeQuesArr,$value['qcodes']);
			}	
		}
		return $NCQQuesAttemptedArr;		
	}
	function getLogFreeQuesTotal($userID,$date)
	{
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

	function getWeeklyUserAttempt($userID,$date="",$tmpArr,$tmpArrWeek,$tmpContentFlowArr)
	{
			//echo $tmpArr[1].'<br>';
		$weeklyFreeQuesAttemptCountArr = array();
		$arrWeeklyPendingData = array();
		$UserCurrentStatus = array();
		$passageAttempt = array();
		$passageArray = array();
		$passageAttemptID = array();
		$passageToGive = array();
		
		if(count($tmpArrWeek)>0){
			$weeklyReadingLimit=$tmpArrWeek[0];
			$weeklyConversationLimit=$tmpArrWeek[1];
			$weeklyFreeQuesLimit=$tmpArrWeek[2];
		}else{
			$weeklyReadingLimit = 5;
			$weeklyConversationLimit = 5;
			$weeklyFreeQuesLimit = 100;
		}
		
		$weeklyReadPending = 0;
		$weeklyConvPending = 0;
		$weeklyFreeQuesPending=0;
		$freeQuesFlowLimit = 20;

		if($date!="")
			{
				if(date('N',strtotime($date))!=7){
		      		$startDate =  "".date('Y-m-d', strtotime("monday this week", strtotime($date)))."" ;
			  		$endDate =   "".date('Y-m-d', strtotime("monday next week", strtotime($date)))."";
			  		$endDate2 =   "".date('Y-m-d', strtotime("sunday this week", strtotime($date)))."";
					//$endDate2 =  date("Y-m-d", strtotime('sunday this week,'));
				}
				else
				{ 				

					$startDate =  "".date('Y-m-d', strtotime("monday previous week", strtotime($date)))."" ;
			  		$endDate =   "".date('Y-m-d', strtotime("monday this week", strtotime($date)))."";
			  		$endDate2 =   "".date('Y-m-d', strtotime("sunday previous week", strtotime($date)))."";
				}
			  
			}
		else
			{
				if(date('N')!=7){
					
					$startDate = date("Y-m-d", strtotime('monday this week'));   
					$endDate =  date("Y-m-d", strtotime('monday next week'));
					$endDate2 =  date("Y-m-d", strtotime('sunday this week'));
				 }
				 else
				 {

					
					
				 	$startDate = date("Y-m-d", strtotime('monday previous week'));   
					$endDate =  date("Y-m-d", strtotime('monday this week'));
					$endDate2 =  date("Y-m-d", strtotime('sunday previous week'));
				 }
			}
	 
		echo "<b><br>User week :</b> Monday: ".$startDate." to ";echo "Sunday: ".$endDate2."</br><br>";
		//echo "Next Monday- ".$endDate."</br>";
		
		//exit;
	 	$startDate = ''.$startDate.'';
		$endDate = ''.$endDate.'';

		$query=$this->dbEnglish->query("select childClass from userDetails where userId=".$userID."");
		$this->class = $query->row();
		$this->questionAttemptClassTbl="questionAttempt_class".$this->class->childClass;
      	
		$freeQuestionAttempt = $this->dbEnglish->query("SELECT count(*) as count from ".$this->questionAttemptClassTbl." WHERE userID = ".$userID." and questionType = 'freeQues'  and  lastModified> '".$startDate."' and lastModified<'".$endDate."'");
		$weeklyFreeQuesAttemptCountArr =  $freeQuestionAttempt->result_array();
		$weeklyFreeQuesAttemptCount = $weeklyFreeQuesAttemptCountArr[0]['count'];
		$tmpStr1="SELECT count(*) as count from ".$this->questionAttemptClassTbl." WHERE userID = ".$userID." and questionType = 'freeQues'  and  lastModified> '".$startDate."' and lastModified<'".$endDate."'";
		//echo $tmpStr1.'<br><pre>';
		
     	//exit;
		$passageAttempt = $this->dbEnglish->query("SELECT count(*) as count , pm.passageType from passageAttempt pa inner join  passageMaster pm on pm.passageID = pa.passageID  where userID = '$userID' and pa.completed = '2' and pa.lastModified> '$startDate' and pa.lastModified< '$endDate' group by pm.passageType");
		$passage = $passageAttempt->result_array();
		$tmpStr2="SELECT count(*) as count , pm.passageType from passageAttempt pa inner join  passageMaster pm on pm.passageID = pa.passageID  where userID = '$userID' and pa.completed = '2' and pa.lastModified> '$startDate' and pa.lastModified< '$endDate' group by pm.passageType";
		//echo $tmpStr2.'<br>';
		//echo '<pre>';
		$passageTypeCount['Conversation'] = 0;
		$passageTypeCount['reading'] = 0;
		$countConv=0;
		$countRead=0;
		foreach ($passage as $key => $value) {
			echo $value;
			  if($value['passageType'] == 'Conversation')
			  	   $countConv = $value['count'];
			  	else
			  		$countRead += $value['count'];
		}
		$passageTypeCount['reading']=$countRead;
		$passageTypeCount['Conversation']=$countConv;
		$weeklyReadAttemptCount = $passageTypeCount['reading'];
		$weeklyConvAttemptCount = $passageTypeCount['Conversation'];
		//echo $weeklyReadAttemptCount.' a<br>';
		//echo $weeklyConvAttemptCount.' b<br>';

		$userCurrentStatusArr = $this->currentOngoingQtype($userID);    					// detail of current pending question from userCurrentStatus
		
		//array_push($userCurrentStatusArr, array('status' => 'free_ques Pending','qcode' => $curRefID));
		//var_dump($userCurrentStatusArr);

		//echo "Following scenarios need to be implemented and check in case of content exhaustation : If no reading/converstion/freequestion passage found";
		//echo "<br><br>";
		//echo print_r($userCurrentStatusArr);

		//foreach ($URLS as $URL){
		echo "<table border=1 style=text-align:center>";
		echo "<tr><th colspan=3>User current status</th></tr>";
		echo "<tr>";
			echo "<th>Current Running Content</th>";
	 		echo "<th>ID Passage/PassageQues/Freeques</th>";
	 		echo "<th>ID Completed (0/1)</th>";
		echo "</tr>";
		echo'<tr>'; 
        	echo'<td>'. $userCurrentStatusArr[0]['currentContentType']."</td>";
        	echo'<td>'. $userCurrentStatusArr[0]['refID'].'</td>';
        	echo'<td>'. $userCurrentStatusArr[0]['completed'].'</td>';
        echo'</tr></table>';
    	//}
		
		$curContentType=$userCurrentStatusArr[0]['currentContentType'];
		$curRefID=$userCurrentStatusArr[0]['refID'];
		$curIDCompleted=$userCurrentStatusArr[0]['completed'];
		$curQuestionType='';

		if(count($tmpArr)>0){
			$weeklyFreeQuesAttemptCount=$tmpArr[2];
			$weeklyConvAttemptCount=$tmpArr[1];
			$weeklyReadAttemptCount=$tmpArr[0];
		}
			//echo $tmpArr[1].'<br>';
		//echo "a".$weeklyFreeQuesAttemptCount;
		//echo "b".$weeklyConvAttemptCount;
		//echo "c".$weeklyReadAttemptCount;
		//exit;
		if($curRefID==0){


		}else{
			if($curContentType!="passage" )
			{
				$refCodeDetails =$this->getQcodePassageDetails($curRefID);
				if($refCodeDetails['qcodePassageID']!=0)
				{
					$currentPassageID = $refCodeDetails['qcodePassageID'];
					$currentPassageQuesID = $curRefID;
					$currentPassageType = $refCodeDetails['passageType'];
					$tempArr['passageQuesPending'] = $this->setCurrentPsgQuestions($userID,$currentPassageID);
					//echo "chk".$tempArr['passageQuesPending'];

					if($currentPassageType=='Textual'||$currentPassageType=='Illustrated'){
						$curQuestionType='Reading';
						if($tempArr['passageQuesPending']){
							//echo "chk2".$weeklyReadAttemptCount;
							$weeklyReadAttemptCount++;
						}
					}else{
						$curQuestionType='Conversation';
						if($tempArr['passageQuesPending']){
							$weeklyConvAttemptCount++;					
						}
					}
					//exit;
				}else {
					$currentFreeQuesID=$curRefID;
					$curQuestionType='free_ques';
				}
			}else{
				$currentPassageID=$curRefID;
				$tempPsgType = $this->getPassageType($currentPassageID);
				$currentPassageType =$tempPsgType;
				$tempArr['passageQuesPending'] = $this->setCurrentPsgQuestions($userID,$currentPassageID);
				

				if($tempPsgType=='Textual'||$tempPsgType=='Illustrated'){
					$curQuestionType='Reading';
					if($tempArr['passageQuesPending']){
						$weeklyReadAttemptCount++;
					}
					
				}else{
					$curQuestionType='Conversation';
					if($tempArr['passageQuesPending']){
						$weeklyConvAttemptCount++;					
					}
				}

			}
			
			//echo ($currentPassageID).' d<br>';
			//echo ($currentPassageType).' e<br>';
			
			
		}
		
		//echo $weeklyReadingLimit-$weeklyReadAttemptCount;
		if($weeklyReadAttemptCount<$weeklyReadingLimit&& ($weeklyReadingLimit-$weeklyReadAttemptCount >0))
			$weeklyReadPending = $weeklyReadingLimit-$weeklyReadAttemptCount;
			
			
		if($weeklyConvAttemptCount<$weeklyConversationLimit&&($weeklyConversationLimit-$weeklyConvAttemptCount>0))
			$weeklyConvPending = $weeklyConversationLimit-$weeklyConvAttemptCount;
			
		if($weeklyFreeQuesAttemptCount<$weeklyFreeQuesLimit&&($weeklyFreeQuesLimit - $weeklyFreeQuesAttemptCount>0))
			$weeklyFreeQuesPending = $weeklyFreeQuesLimit - $weeklyFreeQuesAttemptCount;
			

		//	echo $weeklyReadPending;
		//echo $weeklyConvPending;
		//echo $weeklyFreeQuesPending;
		
		//echo $weeklyReadingLimit;
		//echo $weeklyConversationLimit;
		//echo $weeklyFreeQuesLimit;
		//exit;
		
		//echo ($weeklyFreeQuesLimit).' f<br>';
		//echo ($weeklyFreeQuesAttemptCount).' f<br>';
		//echo ($weeklyReadPending).' f<br>';
		//echo ($weeklyConvPending).' g<br>';
		//echo ($weeklyFreeQuesPending).' h<br>';

		$arrWeeklyPendingData['passageCount'] = $weeklyReadAttemptCount;
		$arrWeeklyPendingData['conversationCount'] = $weeklyConvAttemptCount;
		$arrWeeklyPendingData['weeklyFreeQuesAttemptCount']  = $weeklyFreeQuesAttemptCount;
		
		$this->htmlWeeklyPendingData($arrWeeklyPendingData,$weeklyFreeQuesLimit,$weeklyConversationLimit,$weeklyReadingLimit,$userID);
		
		//print_r($arrWeeklyPendingData) ;

		if($curContentType=='passage'|| $curContentType=='passage_ques')
			$initialQuestionFlowArr =  $this->getInitialQuestionFlowArr($userID,$currentPassageID,$curContentType,$currentPassageType,$curIDCompleted);
		elseif ($curContentType=='free_ques'&&$curIDCompleted == 0){										// changed here 
			$initialQuestionFlowArr = array('status' => 'free_ques Pending','qcode' => $curRefID);
			$weeklyFreeQuesAttemptCount++;
			$weeklyFreeQuesPending--;
		}elseif ($curContentType=='free_ques'&&$curIDCompleted == 1){										// changed here 
			$initialQuestionFlowArr = array('status' => 'current free ques completed','qcode' => 'N/A');
			
			
		}else if($curContentType=="N/A"){
			
				$initialQuestionFlowArr = array('status' => 'New user','qcode' => 'N/A','passageQuesPending' => '0');

			
		}
		//echo $weeklyReadPending;
		//echo $weeklyConvPending;
		//echo $weeklyFreeQuesPending;
		//exit;
		if($weeklyReadPending<0){
			$weeklyReadPending=0;
		}
		if($weeklyConvPending<0){
			$weeklyConvPending=0;
		}
		if($weeklyFreeQuesPending<0){
			$weeklyFreeQuesPending=0;
		}
		
		//$contentFlowArr = array('reading','converstation','freeques');
		//print_r($contentFlowArr);

	//exit;
		// first element = lastToLast , second element = lastAttempt
		$userLastAttemptArr  = array('R','R');
		//$passageAttempt = $this->dbEnglish->query("SELECT count(*) as count , pm.passageType from passageAttempt pa inner join  passageMaster pm on pm.passageID = pa.passageID  where userID = '$userID' and pa.completed = 2 and pa.lastModified> '$startDate' and pa.lastModified< '$endDate' group by pm.passageType");
		$getLastAttemptByUser = $this->dbEnglish->query("SELECT b.passageTypeName as passageType FROM ".$this->questionAttemptClassTbl." a,questions b where a.qcode=b.qcode and a.userID='$userID' group by  b.passageID,b.passageTypeName order by MAX(a.lastModified) desc limit 2");
		//echo "SELECT b.passageTypeName as passageType FROM ".$this->questionAttemptClassTbl." a,questions b where a.qcode=b.qcode and a.userID='$userID' group by  b.passageID,b.passageTypeName order by MAX(a.lastModified) desc limit 2";
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
			//echo '<br> a '.$row['passageType'].' b<br>';
			if($curContentType=='passage'){
				$userLastAttemptArr[0]=$tmpLastAttempt;
				if($curQuestionType=='Conversation'){
						$userLastAttemptArr[1]="C";
				}else if($curQuestionType=='Reading'){
						$userLastAttemptArr[1]="R";
				}
			}else if ($curContentType=='passage_ques'){	
				$refCodeDetails =$this->getQcodePassageDetails($curRefID);
				if($refCodeDetails['qcodePassageID']!=0)
				{					
					$currentPassageID = $refCodeDetails['qcodePassageID'];
						//echo "annad";
					$checkPsgQuesAttempt=$this->userAttemptedPsgQues($currentPassageID,$userID);
					//echo "annad";
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
				//check here;
				
			}else if ($curContentType=='free_ques'){				
				if($tmpLastAttempt=="F"){
					$userLastAttemptArr[0]=$tmpLastToLastAttempt;					
				}else{
					$userLastAttemptArr[0]=$tmpLastAttempt;
					
				}
				$userLastAttemptArr[1]="F";					
			}			
			
			//$totalCorrects=$row['passageTypeAttempt'];
		}
		else if(count($passageTypeAttempt)==1)
		{	
			
			$userLastAttemptArr  = array();
			array_push($userLastAttemptArr, 'NA');						
			array_push($userLastAttemptArr, $this->contentType($passageTypeAttempt[0]['passageType']));
				
			$tmpLastAttempt=$userLastAttemptArr[1];
			if($curContentType=='passage'){
				//$currentPassageID = $curRefID;
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
				$refCodeDetails =$this->getQcodePassageDetails($curRefID);
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
			//$valType= $this->contentType($passageTypeAttempt[0]['passageType']);
			if($curContentType=="N/A"){
				array_push($userLastAttemptArr, 'N/A');
				array_push($userLastAttemptArr, 'N/A');
			}else{
				array_push($userLastAttemptArr, 'NA');
				if($curContentType=='passage' || $curContentType=='passage_ques'){
					if($curQuestionType=='Conversation'){
							array_push($userLastAttemptArr, "C");
					}else if($curQuestionType=='	Reading'){							
							array_push($userLastAttemptArr, "R");
					}
				}else if ($curContentType=='free_ques'){					
					array_push($userLastAttemptArr, "F");
				}
			}
			
			
		}
		
		//exit;
		//echo ' <br>A<br>'.$curQuestionType;
		//echo '<br>B<br>';
		///print_r($passageTypeAttempt);
		//echo '<br>C<br>';
		//print_r($userLastAttemptArr);
		//echo 'D<br><br>';

    	//}
		echo "<br><table border=1 style=text-align:center>";
		echo "<tr><th colspan=2>User content attempt details</th></tr>";
		echo "<tr>";
			echo "<th>Last Attempt</th>";
	 		echo "<th>Current Attempt</th>";	 	
		echo "</tr>";
		echo'<tr>'; 
        	echo'<td>'. $userLastAttemptArr[0]."</td>";
        	echo'<td>'. $userLastAttemptArr[1].'</td>';        	
        echo'</tr></table>';

		//echo "<br>User previous 2 Attempt Flow : Last to Last Attempt = <b><font size='5'>".$userLastAttemptArr[0]."</font></b> Last Attempt = <b><font size='5'>".$userLastAttemptArr[1]."</font></b><br>";
		//print_r($userLastAttemptArr);
		//exit;
		//echo 'E<br>';
        //print_r($tmpContentFlowArr);
        //echo 'F<br>';

		if(count($tmpContentFlowArr)>0){
			$contentFlowArr =  $tmpContentFlowArr;
		}else{
			$contentFlowArr =  array('R','C','F');
		}
		
		//$contentFlowArr =  array('R','R','R','R','R','R');
		$counter = 0;
		//select a.userID,a.questionNo,a.qcode,a.passageID,b.passageID,b.passageTypeName,a.lastModified from questionAttempt_class5 a,questions b where a.qcode=b.qcode and a.userID=2443 group by  b.passageID,b.passageTypeName order by MAX(a.lastModified) desc limit 4; 
		
	if($curContentType!="N/A"){
		//echo "<br>".count($userLastAttemptArr);
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
		  // print_r($finalContentFlowArr);

		    //exit;
	

	//exit;	
		$data=array('refID'=>$curRefID);
			$this->session->set_userdata($data);
		//echo '<br>';
		$tmpStrFlow=implode($finalContentFlowArr, " -> ");

		//echo "<br><table border=1 style='text-align:center;border-collapse:collapse' >";
		echo "<br><table border=1 style='text-align:center' >";
		echo "<tr><th colspan=2>User next flow : Dependent on user current attempt and last attempt</th></tr>";
		
		echo'<tr>'; 
        	echo'<td><font size="5">'. $tmpStrFlow."</font></td>";        	    	
        echo'</tr></table>';
		//echo "User next flow will be = <font size='5'>".$tmpStrFlow."</font></b>";

		//echo $tmpStrFlow;
		//print_r($finalContentFlowArr);
		//exit;
		$this->user_id=$userID;
		$questionFlowArr =  $this->getQuestionFlowArray($userID,$curQuestionType,$weeklyConvPending,$weeklyFreeQuesPending,$weeklyReadPending,$weeklyFreeQuesLimit,$weeklyFreeQuesAttemptCount,$freeQuesFlowLimit,$finalContentFlowArr);
		//print_r($questionFlowArr);
		//echo '<br>';
		//exit;
		//echo 'User Current Data pending to Attempt<br>';
		//print_r($initialQuestionFlowArr);
		//echo implode(",",$initialQuestionFlowArr["passageQuesPending"]);
		$totalQuesPendingIDs=array();
		if($curQuestionType!="free_ques"){
			$a1=$initialQuestionFlowArr["passageQuesPending"];
			//print_r($a1);	
			//echo "a1".$a1;
			if($a1!="No passage Question pending to Attempt"){
				if($curContentType=="N/A"){

					//echo "anand";
					array_push($totalQuesPendingIDs,"0");
				}else{
					foreach ($a1 as $a1){
						//echo  $a1['qcode'];
						///$totalQuesPendingIDs=
						array_push($totalQuesPendingIDs, $a1['qcode']);
					
					}

				}
				
				
			}else{
				array_push($totalQuesPendingIDs,"0");
			}	
			
		}
		
		//echo implode(",",$totalQuesPendingIDs["quesPending"]);
		//exit;
		//array_push($totalQuesPendingIDs["passageType"],$initialQuestionFlowArr["passageType"]);
		//array_push($totalQuesPendingIDs["status"],$initialQuestionFlowArr["status"]);
		//echo implode(",",$totalQuesPendingIDs["quesPending"]);
		
		echo "<br><table border=1 style=text-align:center>";
		echo "<tr><th colspan=3>User data pending to Attempt as per user current status (In case of passages it will check for all the pending questions)</th></tr>";
		echo "<tr>";
			echo "<th>Status</th>";
			if($curContentType=='passage'|| $curContentType=='passage_ques'){
	 			echo "<th>Passage Question Pending</th>";
	 			echo "<th>passageType</th>";
	 		}else if($curContentType=='N/A'){
	 			echo "<th>Data Pending QCODE/PassageID</th>";
	 		}else {
	 			echo "<th>Free question pending</th>";
	 		}
		echo "</tr>";
		echo'<tr>'; 
        	echo'<td>'. $initialQuestionFlowArr["status"]."</td>";
        	if($curContentType=='passage'|| $curContentType=='passage_ques'){
        		echo'<td>'. implode(",",$totalQuesPendingIDs).'</td>';
        		echo'<td>'. $initialQuestionFlowArr["passageType"].'</td>';

        	}else if($curContentType=='N/A'){
        		echo'<td>'. $initialQuestionFlowArr["qcode"]."</td>";
        	}else{
        		echo'<td>'. $initialQuestionFlowArr["qcode"]."</td>";
        	}
        echo'</tr></table>';
		

		//exit;

		//echo $weeklyReadingLimit;
		//echo $weeklyConversationLimit;
		//echo $weeklyFreeQuesLimit;
		$this->getResultTableFlow($arrWeeklyPendingData,$questionFlowArr,$weeklyFreeQuesLimit,$freeQuesFlowLimit,$weeklyFreeQuesAttemptCount,$userID,$weeklyConversationLimit,$weeklyReadingLimit);
		
		
		exit;
		/*if($this->lastCompletedPassageQuestionsPending(null,$userID) && ($userCurrentStatusArr[0]['currentContentType']=="passage_ques" or $userCurrentStatusArr[0]['currentContentType']=='passage'))
		{		
				$currentPassgeQue = $this->setCurrentPsgQuestions($userID,$currentPassageID);
				if($currentPassgeQue && $userCurrentStatusArr[0]['completed']==1)
				{
					echo "Passage Question Left</br>";
					$questionFlowArr[0]['passageID'] = $currentPassgeQue;
					$questionFlowArr[0]['passageType'] = $this->getPassageType($currentPassageID);
				}
				elseif ($currentPassgeQue && $userCurrentStatusArr[0]['completed']==0)
				{
					echo "Passage and its question left</br>";
			   		$questionFlowArr[0]['passageID'] = $currentPassageID;
			   		$questionFlowArr[0]['passageType'] = $this->getPassageType($currentPassageID);
				}
				$questionFlowArr =  $this->getQuestionFlowArray($userID,$questionFlowArr,$curQuestionType,$weeklyConvPending,$weeklyFreeQuesPending,$weeklyReadPending,$weeklyFreeQuesLimit,$weeklyFreeQuesAttemptCount,$freeQuesFlowLimit);
				 $this->getResultTableFlow($arrWeeklyPendingData,$questionFlowArr,$weeklyFreeQuesLimit,$freeQuesFlowLimit,$weeklyFreeQuesAttemptCount,$userID);

		}
		else if(!$this->lastCompletedPassageQuestionsPending(null,$userID) && ($userCurrentStatusArr[0]['currentContentType']=="passage_ques" or $userCurrentStatusArr[0]['currentContentType']=='passage'))
		{
					echo "Passage Just Completed</br>";
					$questionFlowArr =  getQuestionFlowArray($userID,$questionFlowArr,$curQuestionType,$weeklyConvPending,$weeklyFreeQuesPending,$weeklyReadPending,$weeklyFreeQuesLimit,$weeklyFreeQuesAttemptCount,$freeQuesFlowLimit);
					 $this->getResultTableFlow($arrWeeklyPendingData,$questionFlowArr,$weeklyFreeQuesLimit,$freeQuesFlowLimit,$weeklyFreeQuesAttemptCount,$userID);
		}
		else
		{
			echo "freeQuestion Left</br>";
			$questionFlowArr =  $this->getQuestionFlowArray($userID,$questionFlowArr,$curQuestionType,$weeklyConvPending,$weeklyFreeQuesPending,$weeklyReadPending,$weeklyFreeQuesLimit,$weeklyFreeQuesAttemptCount,$freeQuesFlowLimit);
		 		
		 $this->getResultTableFlow($arrWeeklyPendingData,$questionFlowArr,$weeklyFreeQuesLimit,$freeQuesFlowLimit,$weeklyFreeQuesAttemptCount,$userID);
		}*/
}

function htmlWeeklyPendingData($arrWeeklyPendingData,$weeklyFreeQuesLimit,$weeklyConversationLimit,$weeklyReadingLimit,$userID){
	echo '<br>';
		echo "<table border=1 style=text-align:center>";
		echo "<tr><th colspan=3 >User current week attempt details</th></tr>";
		echo "<tr>";
			echo "<th>Data Type</th>";
	 		echo "<th>Attempted</th>";
	 		echo "<th>Remaining/Pending</th>";
		echo "</tr>";

		
		foreach ($arrWeeklyPendingData as $key=>$value){
		 echo "<tr>";
		 	echo "<td>";
		 	if($key=="weeklyFreeQuesAttemptCount"){
		 		echo "Free question";
		 	}
		 	else if($key=="conversationCount")
		   	{
		   	 	echo "Conversation";
		   	 }
		   	 else if($key=="passageCount")
		   	 {
		   	 	echo "Reading";
		   	 }
	   	 		
	   	 	echo "</td>";
	   	 	echo "<td>";
	   	 		echo $value;
	   	 	echo "</td>";
	   	 	if($key=="weeklyFreeQuesAttemptCount")
		   	 {	

		   	 	echo "<td>";
		   	 		if(($weeklyFreeQuesLimit-$value)>0){
		   	 			
		   	 			echo $weeklyFreeQuesLimit-$value; 
		   	 		}else {
		   	 			echo 0 ;
		   	 			
		   	 		}
		   	 	echo "</td>";
		   	 }
		   	 else if($key=="conversationCount")
		   	 {
		   	 	
	   	 		echo "<td>";
	   	 		if(($weeklyConversationLimit-$value)>0)
		   		 {	
	   	 			echo $weeklyConversationLimit-$value; 
	   	 		}else{
	   	 			echo 0;
	   	 		}
	   	 			echo "</td>";
	   	 	}  else if($key=="passageCount")
		   	 {
	   	 		echo "<td>";
	   	 		if(($weeklyReadingLimit-$value)>0)
		   		 {	
	   	 			echo $weeklyReadingLimit-$value; 
	   	 		}else{
	   	 			echo 0;
	   	 		}
	   	 			echo "</td>";
	   	 	}   
	  	echo "</tr>";
		}
		echo "</table>";
}

	function userAttemptedPsgQues($passageID,$userID)
	{
		//echo $this->questionAttemptClassTbl;

		$this->dbEnglish->Select('*');
		$this->dbEnglish->from($this->questionAttemptClassTbl);
		$this->dbEnglish->where('passageID',$passageID);
		$this->dbEnglish->where('userID',$userID);
		//select count(qcode) as qcodes from passageAttempt where passageID=2 and userID=
				//print $this->dbEnglish->last_query();
		$query = $this->dbEnglish->get();
		//print $this->dbEnglish->last_query();
		//$query = $this->dbEnglish->get();
		if($query->num_rows() > 0){			
			return true;	
		}else{
			return false;
		}	
		//$passageTypeInfo = $query->result_array();

		//return $passageTypeInfo[0]['passageType'];
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
	function getUserAttemptedPassage($userID,$isContentExhausted=false)
	{
		$allQuesCompletedPsgArr=array();
		$this->dbEnglish->Select('distinct(passageID),scoringID,exScoringID');
		$this->dbEnglish->from('passageAttempt');
		$this->dbEnglish->where('userID',$userID);
		//$this->dbEnglish->where('completed = 1');
		$this->dbEnglish->order_by('lastmodified','asc');
		
		if($isContentExhausted)
			$this->dbEnglish->where('exScoringID IS NOT NULL');
		else	
			$this->dbEnglish->where('scoringID IS NOT NULL');
		

		$query = $this->dbEnglish->get();
		$attemptedPassageIDArr = $query->result_array();
		 //var_dump($attemptedPassageIDArr);
		//print $this->dbEnglish->last_query();
		foreach($attemptedPassageIDArr as $key=>$value)
		{
			//echo $value['passageID']."</br>";
			if(!$this->lastCompletedPassageQuestionsPending(null,$userID,$value['passageID'],$isContentExhausted)){
				 array_push($allQuesCompletedPsgArr,$value['passageID']);
				
				if(!$isContentExhausted){
					if($this->currReadingScoreID == $value['scoringID'] && !in_array($value['passageID'], $this->currReadingScoreIDPsgArr)){
						array_push($this->currReadingScoreIDPsgArr,$value['passageID']);
					}
					else if($this->currListeningScoreID == $value['scoringID'] && !in_array($value['passageID'], $this->currListeningScoreIDPsgArr)){
						array_push($this->currListeningScoreIDPsgArr,$value['passageID']);
					}	
				}
				else{
					if($this->currReadingExScoreID == $value['exScoringID'] && !in_array($value['passageID'], $this->currReadingScoreIDPsgArr))
						array_push($this->currReadingScoreIDPsgArr,$value['passageID']);
					else if($this->currListeningExScoreID == $value['exScoringID'] && !in_array($value['passageID'], $this->currListeningScoreIDPsgArr))	
						array_push($this->currListeningScoreIDPsgArr,$value['passageID']); 
				}
			}
			else
				{	
					$unCompletedPassageArrAdaptive = array();
					array_push($unCompletedPassageArrAdaptive,$value['passageID']);
				}
		}
		//var_dump($unCompletedPassageArrAdaptive);
		if(count($allQuesCompletedPsgArr)>0)
			return implode(",",$allQuesCompletedPsgArr);				
		else 
			return "";
    } 	

    function lastCompletedPassageQuestionsPending($sessionID,$userID,$passageID=false,$chkForAdaptive=true)
	{
		if(!$passageID)				
		{
			$this->dbEnglish->Select('passageID');
			$this->dbEnglish->from('passageAttempt');
			$this->dbEnglish->where('userID',$userID);
			//$this->dbEnglish->where('completed = 1');
			$this->dbEnglish->order_by('lastModified','desc');
			$this->dbEnglish->limit(1);
			$query = $this->dbEnglish->get();
			//print $this->dbEnglish->last_query();
			
			$lastAttemptedPassageInfo = $query->result_array();
			if(count($lastAttemptedPassageInfo)==0)
				return 0;
			$passageID = $lastAttemptedPassageInfo[0]['passageID'];
		}

	

		
		$psgContentType=$this->getPassageType($passageID);
		
		$this->dbEnglish->Select('group_concat(distinct(qcode)) quesAttemptSrno');
		$this->dbEnglish->from($this->questionAttemptClassTbl);
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('passageID',$passageID);
		
		if(($psgContentType == "Textual" || $psgContentType == "Illustrated") && ($this->session->userdata('isReadingContExhaust')) && $chkForAdaptive)
			$this->dbEnglish->where('exScoringID IS NOT NULL');
		else if(($psgContentType == "Conversation") && ($this->session->userdata('isListeningContExhaust')) && $chkForAdaptive)
			$this->dbEnglish->where('exScoringID IS NOT NULL');
		else	
			$this->dbEnglish->where('scoringID IS NOT NULL');

		$query = $this->dbEnglish->get();
		//print $this->dbEnglish->last_query();
		$lastAttemptedPassageQuesInfo = $query->result_array();
		$quesAttemptSrno = $lastAttemptedPassageQuesInfo[0]['quesAttemptSrno'];

		$this->dbEnglish->Select('count(qcode) as qcodes');
		$this->dbEnglish->from('questions');
		$this->dbEnglish->where('passageID',$passageID);
		$this->dbEnglish->where('status',liveQuestionsStaus);
		$query = $this->dbEnglish->get();
		
		$passageTotalQues = $query->result_array();
		
		if($quesAttemptSrno == null || $quesAttemptSrno == "")
			$noOfQuesDoneInPassage = 0;
		else
			$noOfQuesDoneInPassage = count(explode(',', $quesAttemptSrno));
		$maxQuestionsToBeGivenInPassage = 100;
		if($noOfQuesDoneInPassage < $maxQuestionsToBeGivenInPassage  && $noOfQuesDoneInPassage < $passageTotalQues[0]['qcodes'])
		{
			return 1;
		}	
		else
		{
			return 0;
		}
	}
	

	function getPassageType($passageID)
	{
		if($passageID==0)
			return "freeQuestion";

		$this->dbEnglish->Select('passageType');
		$this->dbEnglish->from('passageMaster');
		$this->dbEnglish->where('passageID',$passageID);
		$query = $this->dbEnglish->get();
		$passageTypeInfo = $query->result_array();

		return $passageTypeInfo[0]['passageType'];
	}


	public function setNextSessionPassages($userID,$readingPassageLmt=5,$listningPassageLmt=5)
	{
		

		$readingPsgsArr=array();
		$listeningPsgsArr=array();
		$attemptedPassageID="";
		// gets all live questions attempted passages
		$attemptedPassageID = $this->getUserAttemptedPassage($userID);    // change here
		//$currentContent=$this->session->userdata('currentContentType');
		//echo $currentContent;
		/*if($currentContent=='passage'){
			$passageType=$this->questionspage_model->getPassageType($this->session->userdata('refID'));
		}else if($currentContent=='passage_ques'){
			$arrDetailPsg=$this->questionspage_model->getQcodePassageDetails($prevrefID);
			$passageType=$arrDetailPsg['passageType'];
		}*/


		if($attemptedPassageID!="")
			$attemptedPassageIDArr = explode(',', $attemptedPassageID);

		$userCurrentLevel = $this->getUserCurrentLevel($userID);                           // changed here 
		$passageLevel= $userCurrentLevel[0]['passageLevel'];
		$conversationLevel=$userCurrentLevel[0]['conversationLevel'];

		/*if($exhaustionLogLevel){
			if($exhaustionContentType == readingContentTypeConst)
				$passageLevel=$exhaustionLogLevel;
			else
				$conversationLevel=$exhaustionLogLevel;
		}*/
		$convesationMsLevel=$conversationLevel-gradeScallingConst;
		//echo "convesationMsLevel".$convesationMsLevel."convesationMsLevel";
		$psgDataArr=array();
		$gradeLowerLimit=number_format($passageLevel, 2);
		$gradeHigherLimit=$gradeLowerLimit+gradeHigherLimitIncreaseConst;
		
		$readingPsgCondArr = array('p.status' => livePassageStatus, 'q.diffRating >=' => $gradeLowerLimit, 'q.diffRating <=' => $gradeHigherLimit);
		
		$this->dbEnglish->Select('p.passageID as passageID , passageType');
		$this->dbEnglish->from('passageMaster p');
		$this->dbEnglish->join('passageAdaptiveLogicParams q', 'p.passageID=q.passageID', 'inner');
		$this->dbEnglish->where($readingPsgCondArr);
		$this->dbEnglish->where_in('p.passageType',array('Textual','Illustrated'));
		$this->dbEnglish->where_not_in('p.passageID', $attemptedPassageIDArr);
		$this->dbEnglish->order_by('q.passageId','RANDOM');
		$this->dbEnglish->limit($readingPassageLmt);  					      // changed here in query
		$readingPsgsSql = $this->dbEnglish->get();
		$readingPsgsDataArr = $readingPsgsSql->result_array();

		
		$listeningPsgCondArr = array('status' => livePassageStatus,'msLevel' => $convesationMsLevel, 'passageType' => 'Conversation');

		$this->dbEnglish->Select('passageID as passageID, passageType');
		$this->dbEnglish->from('passageMaster');
		$this->dbEnglish->where($listeningPsgCondArr);
		$this->dbEnglish->where_not_in('passageID', $attemptedPassageIDArr);
		$this->dbEnglish->order_by('passageID','RANDOM');
		$this->dbEnglish->limit($listningPassageLmt);                         // changed here in query
		$listeningpsgsSql = $this->dbEnglish->get();
		$listeningpsgsDataArr = $listeningpsgsSql->result_array();	
		
		foreach($readingPsgsDataArr as $key=>$value)
		{
			array_push($readingPsgsArr,$value);
		}

		//print_r($readingPsgsArr);
		//echo 'reading';
		$readingPsgsArr=$this->setUncompletedPassages($readingPsgsArr,$attemptedPassageIDArr,readingContentTypeConst);
		//print_r($readingPsgsArr);

		foreach($listeningpsgsDataArr as $key=>$value)
		{
			array_push($listeningPsgsArr,$value);
		}
		
		//print_r($listeningPsgsArr);
		//echo "readingContentTypeConst ".readingContentTypeConst." readingContentTypeConst";
		//echo "listeningContentTypeConst ".listeningContentTypeConst." listeningContentTypeConst";
		//echo 'leasting';
		$listeningPsgsArr=$this->setUncompletedPassages($listeningPsgsArr,$attemptedPassageIDArr,listeningContentTypeConst,$convesationMsLevel);
		//print_r($listeningPsgsArr);
		
		//$readingPsgsSql->num_rows() < 5;
		//$listeningpsgsSql->num_rows() < 5
		//$readingPsgsSql->num_rows() < MinReadPsgsToAvoidExhaustion
		/*------- Check if user is in exhustion start -------*/
		
		/*$totalReadPsgAttempt=$this->returnCurrentLevelPsgAttemptCnt($userID,readingContentTypeConst);
		
		$totalCnt=$totalReadPsgAttempt+$readingPsgsSql->num_rows();
	
		if($totalCnt < MinReadPsgsToAvoidExhaustion && !$exhaustionLogLevel || $totalCnt < MinReadPsgsToAvoidExhaustion && $this->session->userdata('isReadingContExhaust')){
			$readingPsgsArr=$this->setExhaustionLogic(readingContentTypeConst,$passageLevel);
			if(!$readingPsgsArr){							
				$this->setNextSessionPassages($this->user_id);
				return;
			}
		}else if ($totalCnt > MinReadPsgsToAvoidExhaustion && !$exhaustionLogLevel && $this->session->userdata('isContentExhaust')){
			$data=array('isReadingContExhaust'=>0);
			$this->dbEnglish->where('userID', $this->user_id);
			$this->dbEnglish->update('userCurrentStatus', $data);
			$this->session->set_userdata($data);
			$this->unsetIsContentExhaustionFlag();	
		}*/

		/*$totalListPsgAttempt=$this->returnCurrentLevelPsgAttemptCnt($userID,listeningContentTypeConst);
		$totalCnt=$totalListPsgAttempt+$listeningpsgsSql->num_rows();*/

		/*if($totalCnt < MinConvPsgsToAvoidExhaustion && !$exhaustionLogLevel || $totalCnt < MinConvPsgsToAvoidExhaustion && $this->session->userdata('isListeningContExhaust')){
			$listeningPsgsArr=$this->setExhaustionLogic(listeningContentTypeConst,$conversationLevel);
			if(!$listeningPsgsArr){ 
				$this->setNextSessionPassages($this->user_id);				
				return;
			}
		}else if ($totalCnt > MinConvPsgsToAvoidExhaustion && !$exhaustionLogLevel && $this->session->userdata('isContentExhaust')){
			$data=array('isListeningContExhaust'=>0);
			$this->dbEnglish->where('userID', $this->user_id);
			$this->dbEnglish->update('userCurrentStatus', $data);
			$this->session->set_userdata($data);
			$this->unsetIsContentExhaustionFlag();	
		}*/

		/*------- Check if user is in exhustion end -------*/
		
		/*for ($i=0; $i<readingPsgCountConst; $i++) {
			if($passageType=='Textual'|| $passageType=='Illustrated'){
		   		$psgDataArr[] = $listeningPsgsArr[$i];	
		    	$psgDataArr[] = $readingPsgsArr[$i];
				
			}
			else{
				$psgDataArr[] = $readingPsgsArr[$i];
			    $psgDataArr[] = $listeningPsgsArr[$i];	
			}
		}*/
		$psgDataArr['reading'] = $readingPsgsArr;
		$psgDataArr['conversation'] = $listeningPsgsArr;
		//$psgDataArr = array_merge($readingPsgsArr,$listeningPsgsArr);
		
		return $psgDataArr;

		//$this->session->set_userdata('sessionPassages',$psgDataArr);
	}
	function setUncompletedPassages($psgArr,$attemptedPassageIDs,$contentType,$convesationMsLevel='',$isContentExhausted=false){
		$allQuesNotCompletedPsgArr=$this->userUnCompletedPassages($psgArr,$attemptedPassageIDs,$contentType,$convesationMsLevel,$isContentExhausted);
		//echo "Z1";
		//print_r($allQuesNotCompletedPsgArr);
		//echo "Z2";
		//will remove the same passageID if found in psgArr
		//print_r($psgArr);
		$removeDuplicateSamePsg = array_diff($psgArr, $allQuesNotCompletedPsgArr);
			//print_r($removeDuplicateSamePsg);
			//echo "Z3";
			
		// resetting the keys after removal of same passageID from psgArr
		$removeDuplicateSamePsg=array_values($removeDuplicateSamePsg);
		//print_r($removeDuplicateSamePsg);
			//echo "Z22";
			//check here;
		$i=0;
		if(count($allQuesNotCompletedPsgArr)>0)
		{
			//echo '<b1>';
			foreach($allQuesNotCompletedPsgArr as $passageID){
				$removeDuplicateSamePsg[$i]['passageID']=$passageID;
				$i++;	
			}
			//echo '<b2>'	;
		}
		//echo "Z4";
		//print_r($removeDuplicateSamePsg);
			//echo "Z5";
		//print($psgArr);
		// Remove refID passage set in usercurrentstatus[if completed=0] from the array 
		$refID=$this->session->userdata('refID');
		//echo "refID".$refID."refID";
		if (($key = array_search($refID, $removeDuplicateSamePsg)) !== false) {
		    unset($removeDuplicateSamePsg[$key]);
		}
		//echo "Z6";
		//print_r($removeDuplicateSamePsg);
			//echo "Z7";
		$removeDuplicateSamePsg=array_values($removeDuplicateSamePsg);		
		return $removeDuplicateSamePsg;
	}
	function userUnCompletedPassages($psgArr,$attemptedPassageIDs,$contentType,$convesationMsLevel='',$isContentExhausted=false){
		//echo 'a1';
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
		
		//echo 'b1';
		$this->dbEnglish->where_not_in('p.passageID', $attemptedPassageIDs);
		
		if($isContentExhausted)
			$this->dbEnglish->where('exScoringID IS NOT NULL');
		else	
			$this->dbEnglish->where('scoringID IS NOT NULL');

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
	function getUserCurrentLevel($userID)
	{

	$this->dbEnglish->select('passageLevel,conversationLevel');
	$this->dbEnglish->from('userCurrentStatus');
	$this->dbEnglish->where('userID',$userID);
	$userLevelSql = $this->dbEnglish->get();
	$userLevelArr = $userLevelSql->result_array();
	return $userLevelArr;

	}

	function getFreeQuestion($userID,$freeQuesLeft)
	{
	$freeQuestionLevel = $this->getQuesLevel($userID);
	
	$this->dbEnglish->Select('qcode');
	$this->dbEnglish->from('questions');
	$this->dbEnglish->where('passageID','0');
	$this->dbEnglish->where('status',liveQuestionsStaus);
	//$this->dbEnglish->where('msLevel',$freeQuestionLevel);
	$this->dbEnglish->order_by('qcode','RANDOM');
	$this->dbEnglish->limit($freeQuesLeft);
	$freeQuesQuery = $this->dbEnglish->get();

	foreach ($freeQuesQuery->result_array() as $key => $value) {
		$freeQuesArr['freeQuestion'][] = $value;
	 } 

	 return $freeQuesArr;
	}

	function getQuesLevel($userID)
	{
		$this->dbEnglish->Select('freeQuesLevel');
		$this->dbEnglish->from('userCurrentStatus');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->limit(1);
		$currentFreeQuesLevel = $this->dbEnglish->get();
		if($currentFreeQuesLevel->num_rows() > 0){
			$currentFreeQuesLevelArr = $currentFreeQuesLevel->row();
			return $currentFreeQuesLevelArr->freeQuesLevel;	
		}
		else
		{
			return null;
		}	
	}



	function setCurrentPsgQuestions($userID,$passageID=false,$chkAttemptedQues=false)
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
		//if($chkAttemptedQues){
			$attemptedPassageQuestions = $this->getUserAttemptedPassageQuestions($userID,$passageID);
			if($attemptedPassageQuestions!="")
				$attemptedPassageQuestionsArr = explode(',', $attemptedPassageQuestions);
		//}

		//$attemptedPassageQuestionsArr = array(0=>'');
		//if($chkAttemptedQues){
			//$attemptedPassageQuestionsArr = $this->getUserAttemptedPassageQuestions($userID,$passageID);
			//if($attemptedPassageQuestionsArr!="")
				//$attemptedPassageQuestionsArr = explode(',',$attemptedPassageQuestionsArr);
		//}
		
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
		//print $this->dbEnglish->last_query();
		$psgQuestionsArr=array();
		//var_dump($psgQuestionsRes);
		foreach($psgQuestionsRes as $key=>$value)
		{
			array_push($psgQuestionsArr,$value['qcode']);
		}

		//print_r($psgQuestionsArr);
		//$psgQuestionsRes = array(0=>'NO passage Question pending to Attempt');
		//return $psgQuestionsRes;

		if(count($psgQuestionsRes)>0){

			return $psgQuestionsRes;
			//return "a";
		}else{
			//$psgQuestionsRes = array();
			return false;
		}
		
		//$this->session->set_userdata('currentPsgQuestions',$psgQuestionsArr);
		
	}

	function getUserAttemptedPassageQuestions($userID,$passageID)
	{
		$psgContentType=$this->getPassageType($passageID);

		if(($psgContentType == "Textual" || $psgContentType == "Illustrated") && ($this->session->userdata('isReadingContExhaust')))
			$exScoringID=$this->getOrSetPsgCurrExScoringID($userID,$psgContentType,$passageID);
		else if(($psgContentType == "Conversation") && ($this->session->userdata('isListeningContExhaust')))
			$exScoringID=$this->getOrSetPsgCurrExScoringID($userID,$psgContentType,$passageID);
		else
			$scoringID=$this->getOrSetPsgCurrScoringID($userID,$psgContentType,$passageID);	
		

		$isListeningContExhaust=$this->session->userdata('isListeningContExhaust');
		$isReadingContExhaust=$this->session->userdata('isReadingContExhaust');
		
		$this->dbEnglish->Select('group_concat(qcode) as qcodes');
		$this->dbEnglish->from($this->questionAttemptClassTbl);
		$this->dbEnglish->where('questionType','passageQues');
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->where('passageID',$passageID);
		if(($psgContentType == "Textual" || $psgContentType == "Illustrated") && ($this->session->userdata('isReadingContExhaust')))
			$this->dbEnglish->where('exScoringID',$exScoringID);
		else if(($psgContentType == "Conversation") && ($this->session->userdata('isListeningContExhaust')))
			$this->dbEnglish->where('exScoringID',$exScoringID);
		else	
			$this->dbEnglish->where('scoringID',$scoringID);
			
		$query = $this->dbEnglish->get();
		//print $this->dbEnglish->last_query();
		$attemptedPassageQues = $query->result_array();

		if($attemptedPassageQues[0]['qcodes'] != null && $attemptedPassageQues[0]['qcodes'] != "")
			return $attemptedPassageQues[0]['qcodes'];
		else
			return "";
		

	}

	function getOrSetPsgCurrScoringID($userID,$contentType,$passageID)
	{
		if($contentType == "Textual" || $contentType == "Illustrated")
			$contentTypeConst=readingContentTypeConst;
		else if($contentType == "Conversation")
			$contentTypeConst=listeningContentTypeConst;
		
		$scoringID=$this->getPsgCurrScoringID($contentTypeConst,$userID);
		
		if($scoringID != null){
			return $scoringID;	
		}
		/*else{
			$this->passage_model->updateUserLevelAndAccPsgLog($userID,$contentType,$passageID);	
			$scoringID=$this->getOrSetPsgCurrScoringID($userID,$contentType,$passageID);
			return $scoringID;
		}*/
	}

 	function currentOngoingQtype($userID)
 	{
 		$this->dbEnglish->Select('currentContentType,refID,completed');
	 	$this->dbEnglish->from('userCurrentStatus');
	 	$this->dbEnglish->where('userID',$userID);
	 	$query = $this->dbEnglish->get();
	 	$userCurrentQtype = $query->result_array();
	 	return $userCurrentQtype; 	

 	}

 	function getPsgCurrScoringID($contentTypeConst,$userID)
 	{
		$this->dbEnglish->Select('scoringID');
		$this->dbEnglish->from('userLevelAndAccuracyLog');
		$this->dbEnglish->where('contentType',$contentTypeConst);
		$this->dbEnglish->where('userID',$userID);
		$this->dbEnglish->order_by('scoringID','desc');
		$this->dbEnglish->limit(1);
		$currPsgScoringIDSql = $this->dbEnglish->get();
		//print $this->dbEnglish->last_query();

		if($currPsgScoringIDSql->num_rows() > 0){
			$currPsgScoringIDArr = $currPsgScoringIDSql->row();
			return $currPsgScoringIDArr->scoringID;	
		}
		else{
			return null;
		}
	}

	function getQuestionFlowArray($userID,$lastAttemptQtype,$weeklyConvPending,$weeklyFreeQuesPending,$weeklyReadPending,$weeklyFreeQuesLimit,$weeklyFreeQuesAttemptCount,$freeQuesFlowLimit,$contentFlowArr)
	{

		//echo $lastAttemptQtype;


		
				//exit;
	//echo 'anand'. $this->session->userdata('refID');
	//echo 'anand'. $this->user_id;
	//exit;
	//exit;
	$passageToGive = $this->setNextSessionPassages($userID,$weeklyReadPending,$weeklyConvPending);

	// need to check for content exhaustation 


	//$passageToGive = $this->setNextSessionPassages($userID,2,2);
    $freeQuesToGive = $this->getFreeQuestion($userID,$weeklyFreeQuesPending);
   // $freeQuesToGive = $this->getFreeQuestion($userID,50);
 //echo '<br>a';
 // echo count($passageToGive['reading']);
   // echo '<br>b';
  //echo count($passageToGive['conversation']);
   // echo '<br>c';
    //echo count($freeQuesToGive['freeQuestion']);
    //echo '<br>d';
    //exit;
   	//print_r($passageToGive);
  //print_r($freeQuesToGive);
  //exit;
   //	echo $lastAttemptQtype;
   	
   	//echo $weeklyFreeQuesPending;
   //	print_r ($contentFlowArr);
   	
   	$freeQuesToGiveCount=0;
   	$convGiveCount=0;
   	$readGiveCount=0;
	if($lastAttemptQtype=="free_ques"){
		$totalF=$freeQuesFlowLimit - $weeklyFreeQuesAttemptCount%$freeQuesFlowLimit;
		//echo $freeQuesFlowLimit." a ".$weeklyFreeQuesAttemptCount;
		//echo "totalF".$totalF."totalF";
		
		if($totalF<$freeQuesFlowLimit){
			if(count($freeQuesToGive['freeQuestion'])>=$freeQuesToGiveCount)
			{
				while($totalF)
				{
					$questionFlowArr[] = $freeQuesToGive['freeQuestion'][$freeQuesToGiveCount];
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
			//echo count($passageToGive['reading'])." dd ".$readGiveCount;
			//echo count($passageToGive['reading']);
			if(count($passageToGive['reading'])>$readGiveCount){
				//echo "R1";
				$questionFlowArr[] = $passageToGive['reading'][$readGiveCount];
				$readGiveCount++;
			}else{
				//echo "R2";
				break;
			}
			
		}
		else if($row=="C")
		{
			if(count($passageToGive['conversation'])>$convGiveCount){
				//echo "C1";
				$questionFlowArr[] = $passageToGive['conversation'][$convGiveCount];
				$convGiveCount++;
			}else{
					//echo "C2";
				break;
			}
		}
		else if($row=="F")
		{			
			//$totalF=$freeQuesFlowLimit;
			$totalF=$freeQuesFlowLimit - $weeklyFreeQuesAttemptCount%$freeQuesFlowLimit;
			//echo $j= $freeQuesFlowLimit - $weeklyFreeQuesAttemptCount%$freeQuesFlowLimit;
			if(count($freeQuesToGive['freeQuestion'])>$freeQuesToGiveCount)
			{
				while($totalF)
				{
					$questionFlowArr[] = $freeQuesToGive['freeQuestion'][$freeQuesToGiveCount];
					$totalF--;
					$freeQuesToGiveCount++;
					$weeklyFreeQuesAttemptCount++;
					
				}
			}else{
				break;
			}
			

		}
		//print_r($questionFlowArr);
		//break;
		//$valType= $this->contentType($row['passageType']);				
		//array_push($userLastAttemptArr, $valType);
				
	}
	//print_r($questionFlowArr);
	//exit;
    return $questionFlowArr;	
   	exit;


    $i=0;
	$k=0;
	$c=0;
	if($lastAttemptQtype=="Conversation"){
		//echo "in Conversation";
			/*if ($weeklyConvPending) {
					$questionFlowArr[] = $passageToGive['conversation'][$i];
					$weeklyConvPending--;
				}*/
						
			if($weeklyFreeQuesPending)
			{	
				if($weeklyFreeQuesPending>=$freeQuesFlowLimit - $weeklyFreeQuesAttemptCount%$freeQuesFlowLimit  && $freeQuesToGive)
					$j= $freeQuesFlowLimit - $weeklyFreeQuesAttemptCount%$freeQuesFlowLimit;
				else
					break;
				while($j){
					$questionFlowArr[] = $freeQuesToGive['freeQuestion'][$k];
					$j--;
					$k++;
					$weeklyFreeQuesPending--;
					$weeklyFreeQuesAttemptCount++;
				}
			}

		foreach ($passageToGive['conversation'] as $key => $value) {
					$questionFlowArr[] = $passageToGive['reading'][$i];
					if ($weeklyConvPending) {
						$questionFlowArr[] = $passageToGive['conversation'][$i];
						$weeklyConvPending--;
					}
					else
						break;
					if($weeklyFreeQuesPending)
					{	
						if($weeklyFreeQuesPending>=$freeQuesFlowLimit - $weeklyFreeQuesAttemptCount%$freeQuesFlowLimit  && $freeQuesToGive)
							$j= $freeQuesFlowLimit - $weeklyFreeQuesAttemptCount%$freeQuesFlowLimit;
						else
							break;
						while($j){
							$questionFlowArr[] = $freeQuesToGive['freeQuestion'][$k];
							$j--;
							$k++;
							$weeklyFreeQuesPending--;
							$weeklyFreeQuesAttemptCount++;
						}
					}
					else
						break;

					$i++;
			}
		//var_dump(json_encode($questionFlowArr));
	}
	elseif ($lastAttemptQtype=="free_ques") {
			if($weeklyFreeQuesPending && $weeklyFreeQuesAttemptCount%$freeQuesFlowLimit)
			{	
						if($weeklyFreeQuesPending>=$freeQuesFlowLimit - $weeklyFreeQuesAttemptCount%$freeQuesFlowLimit  && $freeQuesToGive)
							$j= $freeQuesFlowLimit - $weeklyFreeQuesAttemptCount%$freeQuesFlowLimit;
						else
							break;
						while($j){
							$questionFlowArr[] = $freeQuesToGive['freeQuestion'][$k];
							$j--;
							$k++;
							$weeklyFreeQuesPending--;
							$weeklyFreeQuesAttemptCount++;
						}
					}
		if($weeklyReadPending){
		 foreach ($passageToGive['reading']as $key => $value) {
					$questionFlowArr[] = $passageToGive['reading'][$i];
					if ($weeklyConvPending) {
						$questionFlowArr[] = $passageToGive['conversation'][$i];
						$weeklyConvPending--;
					}
					else
						break;
					if($weeklyFreeQuesPending)
					{	
						if($weeklyFreeQuesPending>=$freeQuesFlowLimit - $weeklyFreeQuesAttemptCount%$freeQuesFlowLimit  && $freeQuesToGive)
							$j= $freeQuesFlowLimit - $weeklyFreeQuesAttemptCount%$freeQuesFlowLimit;
						else
							break;
						while($j){
							$questionFlowArr[] = $freeQuesToGive['freeQuestion'][$k];
							$j--;
							$k++;
							$weeklyFreeQuesPending--;
							$weeklyFreeQuesAttemptCount++;
						}
					}
					else
						break;

					$i++;
			}
		}
		//var_dump(($questionFlowArr));
	}
	else{
		if ($weeklyConvPending) {
						$questionFlowArr[] = $passageToGive['conversation'][$c];
						$weeklyConvPending--;
						$c++;

					
					
						if($weeklyFreeQuesPending)
						{	
							if($weeklyFreeQuesPending>=$freeQuesFlowLimit - $weeklyFreeQuesAttemptCount%$freeQuesFlowLimit  && $freeQuesToGive)
								$j= $freeQuesFlowLimit - $weeklyFreeQuesAttemptCount%$freeQuesFlowLimit;
							else
								break;
							while($j){
								$questionFlowArr[] = $freeQuesToGive['freeQuestion'][$k];
								$j--;
								$k++;
								$weeklyFreeQuesPending--;
								$weeklyFreeQuesAttemptCount++;
							}
						}
					
		//echo  " cde";
		//echo $weeklyReadPending;
						//print_r($passageToGive);
		foreach ($passageToGive['reading'] as $key => $value) {

			//echo  $value." abc";
					$questionFlowArr[] = $passageToGive['reading'][$i];
					if ($weeklyConvPending) {
						$questionFlowArr[] = $passageToGive['conversation'][$c];
						$weeklyConvPending--;
					}
					else
						break;
					if($weeklyFreeQuesPending)
					{	
						if($weeklyFreeQuesPending>=$freeQuesFlowLimit - $weeklyFreeQuesAttemptCount%$freeQuesFlowLimit  && $freeQuesToGive)
							$j= $freeQuesFlowLimit - $weeklyFreeQuesAttemptCount%$freeQuesFlowLimit;
						else
							break;
						while($j){
							$questionFlowArr[] = $freeQuesToGive['freeQuestion'][$k];
							$j--;
							$k++;
							$weeklyFreeQuesPending--;
							$weeklyFreeQuesAttemptCount++;
						}
					}
					else
						break;

					$i++;
					$c++;
			}
		}
		
		}
		//print_r(($questionFlowArr));
		return $questionFlowArr;

	}


	function getResultTableFlow($arrWeeklyPendingData,$questionFlowArr,$weeklyFreeQuesLimit,$freeQuesFlowLimit,$weeklyFreeQuesAttemptCount,$userID,$weeklyConversationLimit,$weeklyReadingLimit)
	{


//	echo "</br>sample URL http://localhost:8080/mindspark/ms_english/Language/test/getWeeklyUserAttempt/450561(userID)/2016-11-17(date if any)</br></br>";
		//echo "Total Number of Attempts and Pending Data by UserID= ".$userID." in this Week</br>";
		
		//print_r($arrWeeklyPendingData);
		

		echo "<table border=1 style=text-align:center>";
		echo "<tr><th colspan=2>Next Flow of the User</th></tr>";
		echo "<tr>";
			echo "<th>QuestionType</th>";
	 		echo "<th>PassageID / Qcode</th>";
		echo "</tr>";

		echo "<br><br>";
	
		//echo "Next Flow of the UserID= ".$userID." </br>";
		$n=0;
		if(count($questionFlowArr)>0){
			foreach ($questionFlowArr as $key=>$value){
			if($value['passageID']){
				 echo "<tr>";
				 	echo "<td>";
			   	 		echo $value['passageType'];
			   	 	echo "</td>"; 
				 	echo "<td>";
				 		if(isset($value['passageID'][0]['qcode']))
				 		{
				 			foreach ($value['passageID'] as $key => $val) {
				 				echo $val['qcode']." ,";
				 			}
				 		}
				 		else
			   	 			echo $value['passageID'];
			   	 	echo "</td>";
			  	
		    }
		  	else
		  	{
			 		$m = $key;
			 		$l = $freeQuesFlowLimit - $weeklyFreeQuesAttemptCount%$freeQuesFlowLimit;
			 		//echo "t1<br>";
			 		//echo $l;
			 		//echo "t2<br>";
			 		//echo  $weeklyFreeQuesAttemptCount%$freeQuesFlowLimit;
			 		//echo "t3<br>";'
			 		//echo $key. " k ".$n;
			 		if($key  >= $n)
				 	{	
				 		echo "<tr>";
					  	echo "<td>";
				   	 		echo 'freeQuestion';
				   	 	echo "</td>";
				   	 	echo "</td>";
					 	echo "<td>";
				 		while ($l) 
				 		{
				 		 	echo $questionFlowArr[$m]['qcode']." , ";
				 		 	$m++;
				 		 	$l--;
				 		 	$n = $m;
				 		 	$weeklyFreeQuesAttemptCount++;
				 		 }  
		   	 		}
		   	 		else
		   	 			continue;
		   	 	echo "</td>";
			  	
		  	}

		}


		}
		
	}

	function getQcodePassageDetails($qcode)
	{
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
	function getInitialQuestionFlowArr($userID,$currentPassageID,$curContentType,$currentPassageType,$curIDCompleted)
	{								
		
			if($curContentType=="passage_ques" || ($curContentType=="passage" && $curIDCompleted==1) ||($curContentType=="passage" && $curIDCompleted==0))
			{
					$initialQuestionFlowArr['passageQuesPending'] = $this->setCurrentPsgQuestions($userID,$currentPassageID);
					if(!$initialQuestionFlowArr['passageQuesPending']){
						//echo "Z!".$initialQuestionFlowArr['passageQuesPending']."z2";
						$initialQuestionFlowArr['passageQuesPending']="No passage Question pending to Attempt";
						$initialQuestionFlowArr['status'] = "No passage questions pending";
						$initialQuestionFlowArr['passageType'] = $currentPassageType;
						//echo "Z!".$initialQuestionFlowArr['passageQuesPending']."z2";
					} else{
						$initialQuestionFlowArr['status'] = "passage questions remaining";
						$initialQuestionFlowArr['passageType'] = $currentPassageType;
					}

					
			}
			else{
				$tempArr['passageQuesPending'] = $this->setCurrentPsgQuestions($userID,$currentPassageID);
				if($tempArr['passageQuesPending']){
					//echo "anand";
					$initialQuestionFlowArr =  array('status' => "passage not completed",'passageType' => $currentPassageType , 'passageID' => $currentPassageID);
				}else {
					$initialQuestionFlowArr =  array('status' => "user Already attempted this passage though it has marked completed as 0");

				}

			}

	
		return $initialQuestionFlowArr;

	}

}
?>
