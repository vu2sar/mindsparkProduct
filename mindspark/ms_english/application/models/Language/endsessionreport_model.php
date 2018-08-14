<?php

Class Endsessionreport_model extends MY_Model
{
	public function __construct() 
	{
		 parent::__construct();
		 $this->dbEnglish = $this->load->database('mindspark_english',TRUE);
		 $this->Companies_db = $this->dbEnglish;

	       // Pass reference of database to the CI-instance
	     $CI =& get_instance();
	     $CI->Companies_db =& $this->Companies_db; 
	}

	/**
	 * function role : Generate end sesion report
	 * param1 : userID
	 * @return  json object, End sesison report statistics
	 * 
	 * */

	function getReport($userID,$startDate,$endDate,$attemptTableClass)
	{
		$finalReport = $this->getUserAttemptedInfo($userID,$startDate,$endDate,$attemptTableClass);
		$passage_array = array();
		foreach ($finalReport['passage'][0] as $key) 
		{
			array_push($passage_array, $key);
		}
		if(!empty($passage_array) && count($passage_array) > 0)
			$finalReport['passage'][0] = $passage_array;

		//return json_encode($this->getUserAttemptedInfo($userID));
		return json_encode($finalReport);
	}

	/**
	 * function role : Fetch all information regarding user attempts like questions,passages,games for a user
	 * param1 : userID
	 * @return  array, Report summary
	 * 
	 * */

	function getUserAttemptedInfo($userID,$startDate,$endDate,$attemptTableClass)
	{

		$date = date('Y-m-d');

		//$date = '2016-03-30';
		//$date = '2016-11-08';

		if($this->category == 'STUDENT')
		{
			
			$startDate = $date;
			
			$endDate = '';

		}

		$essayAttemptedInfo = $this->getEssayAttemptedInfo($userID,$startDate,$endDate);
		$passageAttemptedInfo = $this->getPassageAttemptedInfo($userID,$startDate,$endDate);
		$freeQuesAttemptedInfo = array();

		//Initializing total correct and incorrect counts for free questions
		$freeQuesAttemptedInfo[1]['TC'] = $freeQuesAttemptedInfo[2]['TC'] = 0;
		$freeQuesAttemptedInfo[1]['TIC'] = $freeQuesAttemptedInfo[2]['TIC'] = 0;

		$reportSummary = array('passage'=>array(),'nonContextual'=>array(),'essay'=>array());

		/*$this->dbEnglish->Select('qAmpt.questionType questionType,ques.passageID id,qAmpt.qcode qcode,ques.topicID topicID,ques.quesText questionText,qAmpt.userResponse YA,ques.correctAnswer CA,
			qAmpt.correct C,ques.qTemplate quesTypeCode');
		$this->dbEnglish->from($this->questionAttemptClassTbl.' qAmpt');
		$this->dbEnglish->from('questions ques');
		$this->dbEnglish->where('qAmpt.qcode = ques.qcode');
		$this->dbEnglish->where("date(qAmpt.lastModified) = '".$date."'");
		$this->dbEnglish->where('userID',$userID);
		$query = $this->dbEnglish->get();*/
		if($this->category == 'School Admin' || $this->category == 'TEACHER' || $this->category == 'ADMIN')
		{
			$whereDate = "date(qAmpt.lastModified) >= '".date('Y-m-d',strtotime($startDate))."' AND date(qAmpt.lastModified) <= '".date('Y-m-d',strtotime($endDate))."'";
			$attemptTable = 'questionAttempt_class'.$attemptTableClass;
		}
		else
		{
			$whereDate = "date(qAmpt.lastModified) = '".$date."'";
			$attemptTable = $this->questionAttemptClassTbl;
		}

		$query = "SELECT qAmpt.questionType questionType,ques.quesSubType,ques.optSubType,ques.passageID id,qAmpt.qcode qcode,ques.topicID topicID,ques.quesText questionText,qAmpt.userResponse YA,ques.correctAnswer CA, qAmpt.correct C,ques.qTemplate quesTypeCode,ques.qType,ques.totalOptions,ques.option_a,ques.option_b,ques.option_c,ques.option_d,ques.option_e,ques.option_f,ques.sound_a,ques.sound_b,ques.sound_c,ques.sound_d,ques.sound_e,ques.sound_f,ques.desc_a,ques.desc_b,ques.desc_c,ques.desc_d,ques.desc_e,ques.desc_f,ques.explanation,ques.quesAudioIconSound,ques.quesImage, SUBSTRING_INDEX(skillID, ',', 1) as seperatedSkillID, ques.skillID, (CASE
					WHEN SUBSTRING_INDEX(skillID, ',', 1) in ('19','20','22','23') then 'vocab'
					WHEN SUBSTRING_INDEX(skillID, ',', 1) in ('14','15','16','12','13','10','6','11','9','7','2','8','3','5','1','4','17','21','18') then 'Grammar'
					else 'none'
					END) as type FROM ".$attemptTable." qAmpt
					JOIN  questions ques on qAmpt.qcode = ques.qcode  
					 WHERE qAmpt.qcode = ques.qcode AND ".$whereDate." AND userID = $userID group by qcode";
		
		$resultData=$this->dbEnglish->query($query);
		$quesAttemptedInfo = $resultData->result_array();
		//echo "<pre>";print_r($this->dbEnglish->last_query());echo "</pre>";
		foreach($quesAttemptedInfo as $key=>$quesInfo)
		{
			//for not showing the words eg dropdown_1 and blank_1 in user response
			if($quesInfo['quesTypeCode'] == 'blank' and $quesInfo['qType'] != 'openEnded')
			{
				//get paramater value/correct answer for blank and dropdown
				$correct_ans_arr       = array();
				$correct_ans_arrReport = array();
				$final_correct_array   = array();
				$paramNameArr          = array();
				
				$questionParamInfo = array();
				$questionParamInfoCorrect = array();

				$this->dbEnglish->Select('qcode, paramValue, paramName');
				$this->dbEnglish->from('questionParameters');
				$this->dbEnglish->where('qcode', $quesInfo['qcode']);
				$getParamValue = $this->dbEnglish->get();
				$paramValueData = $getParamValue->result_array();
				
				foreach ($paramValueData as $paramKey => $paramValue) 
				{
					array_push($correct_ans_arr, $paramValue['paramValue']);
					array_push($correct_ans_arrReport, $paramValue['paramValue']);
					array_push($paramNameArr, $paramValue['paramName']);
					$questionParamInfo[$paramValue['paramName'].'_'.$paramValue['qcode']] = $paramValue['paramValue'];
					$corrAns = explode(';', $paramValue['paramValue']);
					$questionParamInfoCorrect[$paramValue['paramName'].'_'.$paramValue['qcode']] = $corrAns[0];
				}
				//echo "<pre>";print_r($questionParamInfo);echo "</pre>";
				foreach ($correct_ans_arr as $correctKeyy => $correctKey) 
				{
					
					$exploded_value = explode(';', $correctKey);

					array_push($final_correct_array, $exploded_value[0]);

					$final_response_ans = implode('|', array_filter($final_correct_array));
				}
				//end
				$user_response = explode('|', $quesInfo['YA']);
				$user_resp_arr = array();
				$actualUserResponse = array();

				foreach ($user_response as $key1=> $key) 
				{
					$user_response_final = explode(':', $key);

					$actualUserResponse[$user_response_final[0].'_'.$quesInfo['qcode']] = $user_response_final[1];

					array_push($user_resp_arr, $user_response_final[1]);

					$final_response = implode('|', array_filter($user_resp_arr));

					$quesInfo['YA'] = $final_response;

					//fetching correct answer for blank and dropdown
					if($quesInfo['CA'] == '')
					{
						$quesInfo['CA'] = $final_response_ans;
					}
				}
				$actualUserResponse = array_filter($actualUserResponse);
			}
			

			if($quesInfo['questionType']=='passageQues')
			{
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['questionText'] = $quesInfo['questionText'];
				
				//$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['questionText'] = 'text';

				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['CA'] = $quesInfo['CA'];

				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['YA']  = $quesInfo['YA'];

				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['C'] = $quesInfo['C'];

				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['quesTypeCode'] = $quesInfo['quesTypeCode'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['quesSubType'] = $quesInfo['quesSubType'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['optSubType'] = $quesInfo['optSubType'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['qType'] = $quesInfo['qType'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['totalOptions'] = $quesInfo['totalOptions'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['explanation'] = $quesInfo['explanation'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['option_a'] = $quesInfo['option_a'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['option_b'] = $quesInfo['option_b'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['option_c'] = $quesInfo['option_c'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['option_d'] = $quesInfo['option_d'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['option_e'] = $quesInfo['option_e'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['option_f'] = $quesInfo['option_f'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['sound_a'] = $quesInfo['sound_a'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['sound_b'] = $quesInfo['sound_b'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['sound_c'] = $quesInfo['sound_c'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['sound_d'] = $quesInfo['sound_d'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['sound_e'] = $quesInfo['sound_e'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['sound_f'] = $quesInfo['sound_f'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['desc_a'] = $quesInfo['desc_a'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['desc_b'] = $quesInfo['desc_b'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['desc_c'] = $quesInfo['desc_c'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['desc_d'] = $quesInfo['desc_d'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['desc_e'] = $quesInfo['desc_e'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['desc_f'] = $quesInfo['desc_f'];

				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['CAA'] = $correct_ans_arrReport;
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['paramname'] = $paramNameArr;
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['paramTotal'] = $questionParamInfo;
				
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['actualUserResponse'] = $actualUserResponse;
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['actualcorrAns'] = $questionParamInfoCorrect;
				

				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['quesAudioIconSound'] = $quesInfo['quesAudioIconSound'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['qcode'] = $quesInfo['qcode'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['questionType'] = $quesInfo['questionType'];
				$passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['quesImage'] = $quesInfo['quesImage'];
				
				
			
				if($passageAttemptedInfo[$quesInfo['id']]['questionDetails'][$quesInfo['qcode']]['C'] >= 0.5)
				{
					if(array_key_exists($quesInfo['id'], $passageAttemptedInfo))
						$passageAttemptedInfo[$quesInfo['id']]['TC'] ++;
					else
						$passageAttemptedInfo[$quesInfo['id']]['TC'] = 0;
				}
				else
				{
					if(array_key_exists($quesInfo['id'], $passageAttemptedInfo))
						$passageAttemptedInfo[$quesInfo['id']]['TIC'] ++;
					else
						$passageAttemptedInfo[$quesInfo['id']]['TIC'] = 0;
				}
					

			}
			else
			{
				//if($quesInfo['topicID'] == 2)
				if($quesInfo['type'] == 'vocab')
				{
					$freeQuesAttemptedInfo[1]['name'] = 'Vocabulary';

					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['questionText'] = $quesInfo['questionText'] ;

					//$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['questionText'] = 'text';

					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['CA'] = $quesInfo['CA'];

					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['YA']  = $quesInfo['YA'];

					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['C'] = $quesInfo['C'];

					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['quesTypeCode'] = $quesInfo['quesTypeCode'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['quesSubType'] = $quesInfo['quesSubType'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['optSubType'] = $quesInfo['optSubType'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['qType'] = $quesInfo['qType'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['totalOptions'] = $quesInfo['totalOptions'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['explanation'] = $quesInfo['explanation'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['option_a'] = $quesInfo['option_a'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['option_b'] = $quesInfo['option_b'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['option_c'] = $quesInfo['option_c'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['option_d'] = $quesInfo['option_d'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['option_e'] = $quesInfo['option_e'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['option_f'] = $quesInfo['option_f'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['sound_a'] = $quesInfo['sound_a'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['sound_b'] = $quesInfo['sound_b'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['sound_c'] = $quesInfo['sound_c'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['sound_d'] = $quesInfo['sound_d'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['sound_e'] = $quesInfo['sound_e'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['sound_f'] = $quesInfo['sound_f'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['desc_a'] = $quesInfo['desc_a'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['desc_b'] = $quesInfo['desc_b'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['desc_c'] = $quesInfo['desc_c'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['desc_d'] = $quesInfo['desc_d'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['desc_e'] = $quesInfo['desc_e'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['desc_f'] = $quesInfo['desc_f'];

					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['CAA'] = $correct_ans_arrReport;
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['paramname'] = $paramNameArr;
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['paramTotal'] = $questionParamInfo;
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['actualUserResponse'] = $actualUserResponse;
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['actualcorrAns'] = $questionParamInfoCorrect;
					
					
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['quesAudioIconSound'] = $quesInfo['quesAudioIconSound'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['qcode'] = $quesInfo['qcode'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['questionType'] = $quesInfo['questionType'];
					$freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['quesImage'] = $quesInfo['quesImage'];
					
					
					

					if($freeQuesAttemptedInfo[1]['questionDetails'][$quesInfo['qcode']]['C'] >= 0.5)
						$freeQuesAttemptedInfo[1]['TC'] ++;
					else
						$freeQuesAttemptedInfo[1]['TIC'] ++;
				}
				//else if($quesInfo['topicID'] == 1)
				else if($quesInfo['type'] == 'Grammar')
				{
					$freeQuesAttemptedInfo[2]['name'] = 'Grammar';

					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['questionText'] = $quesInfo['questionText'] ;

					//$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['questionText'] = 'text';

					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['CA'] = $quesInfo['CA'];

					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['YA']  = $quesInfo['YA'];

					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['C'] = $quesInfo['C'];

					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['quesTypeCode'] = $quesInfo['quesTypeCode'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['quesSubType'] = $quesInfo['quesSubType'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['optSubType'] = $quesInfo['optSubType'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['qType'] = $quesInfo['qType'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['totalOptions'] = $quesInfo['totalOptions'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['explanation'] = $quesInfo['explanation'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['option_a'] = $quesInfo['option_a'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['option_b'] = $quesInfo['option_b'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['option_c'] = $quesInfo['option_c'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['option_d'] = $quesInfo['option_d'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['option_e'] = $quesInfo['option_e'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['option_f'] = $quesInfo['option_f'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['sound_a'] = $quesInfo['sound_a'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['sound_b'] = $quesInfo['sound_b'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['sound_c'] = $quesInfo['sound_c'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['sound_d'] = $quesInfo['sound_d'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['sound_e'] = $quesInfo['sound_e'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['sound_f'] = $quesInfo['sound_f'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['desc_a'] = $quesInfo['desc_a'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['desc_b'] = $quesInfo['desc_b'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['desc_c'] = $quesInfo['desc_c'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['desc_d'] = $quesInfo['desc_d'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['desc_e'] = $quesInfo['desc_e'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['desc_f'] = $quesInfo['desc_f'];

					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['CAA'] = $correct_ans_arrReport;
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['paramname'] = $paramNameArr;
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['paramTotal'] = $questionParamInfo;
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['actualUserResponse'] = $actualUserResponse;
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['actualcorrAns'] = $questionParamInfoCorrect;
					

					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['quesAudioIconSound'] = $quesInfo['quesAudioIconSound'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['qcode'] = $quesInfo['qcode'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['questionType'] = $quesInfo['questionType'];
					$freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['quesImage'] = $quesInfo['quesImage'];
					


					if($freeQuesAttemptedInfo[2]['questionDetails'][$quesInfo['qcode']]['C'] >= 0.5)
						$freeQuesAttemptedInfo[2]['TC'] ++;
					else
						$freeQuesAttemptedInfo[2]['TIC'] ++;
				}
			}
		}
		
		array_push($reportSummary['passage'],$passageAttemptedInfo);
		array_push($reportSummary['nonContextual'],$freeQuesAttemptedInfo);
		array_push($reportSummary['essay'], $essayAttemptedInfo);
		return $reportSummary;
	}

	/**
	 * function role : Fetch all information regarding passage attempts for a user
	 * param1 : userID
	 * param2 : Target date for fetching attempts
	 * @return  array, Passage attempt summary
	 * 
	 * */

	function getPassageAttemptedInfo($userID,$startDate,$endDate)
	{
		
		$passageAttemptedInfo = array();

		$this->dbEnglish->distinct('A.passageID,B.passageName,B.passageContent,B.passageType,B.Form');
		$this->dbEnglish->from('passageAttempt A');
		$this->dbEnglish->from('passageMaster B');	 
		$this->dbEnglish->where('A.passageID = B.passageID');
		$this->dbEnglish->where('userID',$userID);

		if($this->category == 'School Admin' || $this->category == 'TEACHER' || $this->category == 'ADMIN')
		{
			$this->dbEnglish->where("date(A.lastModified) >= '".date('Y-m-d',strtotime($startDate))."'");
			$this->dbEnglish->where("date(A.lastModified) <= '".date('Y-m-d',strtotime($endDate))."'");	
		}
		else
		{
			$this->dbEnglish->where("date(A.lastModified) = '".$startDate."'");
		}
		
		$query = $this->dbEnglish->get();
		$passageAttempt = $query->result_array();
		//echo "<pre>";print_r($this->dbEnglish->last_query());echo "</pre>";


		foreach ($passageAttempt as $key => $passageVal) 
		{
			//$passageAttemptedInfo[$passageVal['passageID']]['id']           = $passageVal['passageID'];
			$passageAttemptedInfo[$passageVal['passageID']]['passageID']      = $passageVal['passageID'];
			$passageAttemptedInfo[$passageVal['passageID']]['name']           = stripslashes($passageVal['passageName']);
			$passageAttemptedInfo[$passageVal['passageID']]['passageContent'] = stripslashes($passageVal['passageContent']);
			$passageAttemptedInfo[$passageVal['passageID']]['passageType']    = $passageVal['passageType'];
			$passageAttemptedInfo[$passageVal['passageID']]['Form']           = $passageVal['Form'];
			$passageAttemptedInfo[$passageVal['passageID']]['TC']             = 0;
			$passageAttemptedInfo[$passageVal['passageID']]['TIC']            = 0;	
		}

		return $passageAttemptedInfo;
	}

	function getEssayAttemptedInfo($userID,$startDate,$endDate)
	{
		$essayAttemptedInfo = array();

		$this->dbEnglish->select('A.essayID,A.userResponse,B.essayTitle,A.ews_essayDetailsID');
		$this->dbEnglish->from('essayAttempt A, essayMaster B, ews_essayDetails C');	 
		$this->dbEnglish->where('A.userID',$userID);
		$this->dbEnglish->where('A.essayID = B.essayID');
		$this->dbEnglish->where('A.ews_essayDetailsID = C.essayID');

		if($this->category == 'School Admin' || $this->category == 'TEACHER' || $this->category == 'ADMIN')
		{
			$this->dbEnglish->where("date(A.lastModified) >= '".date('Y-m-d',strtotime($startDate))."'");
			$this->dbEnglish->where("date(A.lastModified) <= '".date('Y-m-d',strtotime($endDate))."'");
		}
		else
		{
			$this->dbEnglish->where("date(A.lastModified) = '".$startDate."'");	
		}

		$query = $this->dbEnglish->get();
		$essayAttempt = $query->result_array();
		/*echo "<pre>";print_r($essayAttempt);echo "</pre>";
		echo "<pre>";print_r($this->dbEnglish->last_query());echo "</pre>";*/
		
		foreach ($essayAttempt as $key => $valueArr) 
		{
			/*$essayAttemptedInfo[$valueArr['essayID']]['userResponse'] = $valueArr['userResponse'];
			$essayAttemptedInfo[$valueArr['essayID']]['essayTitle'] = $valueArr['essayTitle'];*/
			$essayAttemptedInfo[$key]['userResponse'] = $valueArr['userResponse'];
			$essayAttemptedInfo[$key]['essayTitle'] = $valueArr['essayTitle'];

			/*if($this->isEssaySubmitted($userID,$valueArr['essayID']) == 1)
				$essayAttemptedInfo[$valueArr['essayID']]['isSubmitted'] = 1;
			else
				$essayAttemptedInfo[$valueArr['essayID']]['isSubmitted'] = 0;*/

			if($this->isEssaySubmitted($userID,$valueArr['essayID'],$valueArr['ews_essayDetailsID']) == 1)
				$essayAttemptedInfo[$key]['isSubmitted'] = 1;
			else
				$essayAttemptedInfo[$key]['isSubmitted'] = 0;
		}
		
		return $essayAttemptedInfo;
	}

	function isEssaySubmitted($userID,$essayID,$ews_essayDetailsID)
	{
		$userID = -1 * $userID;
		$this->dbEnglish->select("status");
		$this->dbEnglish->from('ews_essayDetails');	 
		$this->dbEnglish->where('writerID',$userID);
		$this->dbEnglish->where('topicID',$essayID);
		$this->dbEnglish->where('essayID',$ews_essayDetailsID);
		$query = $this->dbEnglish->get();
		$essayAttempt = $query->result_array();
		return $essayAttempt[0]['status'];

	}

}


?>