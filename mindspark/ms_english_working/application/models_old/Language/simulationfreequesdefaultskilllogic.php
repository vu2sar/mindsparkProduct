<?php
Class simulationfreequesdefaultskilllogic extends CI_Model
{
	
	public function __construct() {
		 parent::__construct();
		 $this->dbEnglish = $this->load->database('mindspark_english',TRUE);
		 $this->load->library('session');
		 $this->class=0;
		 //$userID=500010;
	}
	public function intiallazeVarAndCallFunc($selectedGroupID,$userID,$qAttempt1="",$qAttempt2=""){
		$query=$this->dbEnglish->query("select childClass from userDetails where userId=".$userID."");
		$this->class = $query->row();
		$this->questionAttemptClassTbl="questionAttempt_class".$this->class->childClass;

		$totalQuesForSelectedSkill=3;
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
		if($freeQuesLevel==3){
			$freeQuesLevel=4;
		}
		$freeQuesMsLevel=$freeQuesLevel-gradeScallingConst;	
		$addParams="";

		$this->simulationfreequesdefaultskilllogic->finalGroupQuesArr ($attemptedGroupQnsData,$selectedGroupID,$totalQuesForSelectedSkill,$totalQuesForNonSelectedSkill,$userID);
}

public function finalGroupQuesArr ($attemptedGroupQnsData,$selectedGroupID,$totalQuesForSelectedSkill,$totalQuesForNonSelectedSkill,$userID){

	
	$arrGroupQues= array();
	$freeQuesArr=array();
	$addParams="";
	$freeQuesLevel=$this->class->childClass;
	if($freeQuesLevel==3){
			$freeQuesLevel=4;
		}
	$freeQuesMsLevel=$freeQuesLevel-gradeScallingConst;
	$attemptedFreeQuesArr = $this->simulationfreequesdefaultskilllogic->getPreviousAttemptedNCQQues($userID,$freeQuesMsLevel);
		
	if(count($attemptedFreeQuesArr) > 0)
		$addParams=" and qcode NOT IN (".implode(',', $attemptedFreeQuesArr).")";


	//$skills=$this->getGroupSkills($selectedGroupID);	
	//$checkQuesPending=$this->dbEnglish->query("select qcode,msLevel from questions where skillID IN (".$skills.") and status=".liveQuestionsStaus." and passageID=0 and msLevel=".$freeQuesMsLevel.$addParams);
		//$checkQuesPending=$this->dbEnglish->query("select qcode,msLevel from questions where skillID IN (1,4) and status=6 and passageID=0 and msLevel=2 and qcode NOT IN (6490,5774,8260,7908,7870,7860,27,331,762,763,943,968,969,62,669,883,1570,1572,1575,1587,1610,1612,1616,211,709,713,1617,1627,1691,4064,4065,4066,4067,835,1010,1017,4068,4069,4070,4071,4072,4073,5098,363,379,414,5100,5102,7495,7496,7497,7498,7499,262,288,360,7500,8842,8843,8844,8845,8846,8847,40,674,676,8848,8849,8850,8851,9992,9993,10050)");
	//$quesPending=$checkQuesPending->num_rows();

	//if($quesPending==0)
	//{
		//echo "No questions pending";
		//exit;
	//}

	//echo "abc".$addParams."abc";
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
	   	echo "Grammar skill questions will come in below order for this User";
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
	

	//echo $freeQuesMsLevel."a".$addParams."b";
	//echo "<br>";
	
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
		//echo "select qcode,msLevel from questions where skillID IN (".$skills.") and status=".liveQuestionsStaus." and passageID=0 and msLevel=".$freeQuesMsLevel.$addParams."".$limit;
		//echo "<br>";
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
				//echo "<br>";
				//echo "ding ding";
				//echo "<br>";
				//break;
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

public function getPreviousAttemptedGroupID($userID,$selectedGroupID=null){
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
	public function getGroupSkills($no){
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
		//print $this->dbEnglish->last_query(); 
		$this->dbEnglish->_protect_identifiers = TRUE;
		$NCQQuesAttemptedArr = array();
		//echo "DB";
		
		foreach($resultArr as $key=>$value)
		{
			array_push($NCQQuesAttemptedArr,$value['qcodes']);
			if($this->session->userdata('isFreeQuesContentExhaust')){
				//if($this->currFreeQuesExScoreID == $value['exScoringID'] && !in_array($value['qcodes'], $this->currExScoreIDFreeQuesArr))
					//array_push($this->currExScoreIDFreeQuesArr,$value['qcodes']);
			}
			else{
				//if($this->currFreeQuesScoreID == $value['scoringID'] && !in_array($value['qcodes'], $this->currScoreIDFreeQuesArr))
					//array_push($this->currScoreIDFreeQuesArr,$value['qcodes']);
			}	
		}
		return $NCQQuesAttemptedArr;		
	}
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

}
	//exit;
?>
