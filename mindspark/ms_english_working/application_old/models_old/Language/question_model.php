<?php

Class Question_model extends CI_model
{
	public function __construct() 
	{
		 $this->dbEnglish = $this->load->database('mindspark_english',TRUE);
		 $this->Companies_db = $this->dbEnglish;

	       // Pass reference of database to the CI-instance
	     $CI =& get_instance();
	     $CI->Companies_db =& $this->Companies_db; 
	}

	/**
	 * function description : Get question details
	 * @param   qcode
	 * @return  array, question information 
	 * 
	 * */

	function getQuestionInfo($qcode)
	{
		$questionInfo = array();
		//$this->dbEnglish->Select('qcode,tagType,passageTypeName,passageID,qTemplate,qType,titleText,titleSound,quesSubType,quesText,quesSound,quesImage,quesAudioIconSound,totalOptions,optSubType,option_a,option_b,option_c,option_d,option_e,option_f,sound_a,sound_b,sound_c,sound_d,sound_e,sound_f,desc_a,desc_b,desc_c,desc_d,desc_e,desc_f,explanation,credit,topicID,tagType,status,playTitle,misConception,skillID,subSkillID,subSubSkillID,playTitle,trail,msLevel,difficulty,questionmaker,currentAlloted,first_alloted,second_alloted,firstReviewerAnswer,secondReviewerAnswer,submitdate');
		$tbl='questions';
		if($this->session->userdata('liveEditPreview')=='liveEditPreview'){
			$tbl='liveEditQuestions';
			$this->dbEnglish->order_by('srno', 'desc');
			$this->dbEnglish->limit(1);	
		}
		reRunUsingGoto:
		$this->dbEnglish->Select('*');
		$this->dbEnglish->from($tbl);
		$this->dbEnglish->where('qcode',$qcode);
		$query = $this->dbEnglish->get();
		$questionInfo = $query->result_array();
		
		if(empty($questionInfo) && $tbl=='liveEditQuestions'){
			$tbl='questions';
			goto reRunUsingGoto;
		}
		
		$removeKeys = array('tagType','topicID' ,'playTitle','misConception','subSubSkillID','msLevel','difficulty','questionmaker','currentAlloted','first_alloted','second_alloted','firstReviewerAnswer','secondReviewerAnswer','status','trail','submitdate','lastModified','lastModifiedBy','liveOn');
		foreach($removeKeys as $key) {
		   unset($questionInfo[0][$key]);
		}
		
		$questionInfo[0]['correctAnswer']=$this->encrypt($questionInfo[0]['correctAnswer']);
		$questionInfo[0]['queParams'] = $this->fetchquesParams($qcode);
		foreach ($questionInfo[0] as $key => $value) {
			if($key!="queParams")
			{
				$tmpVal=stripcslashes($value);
				$tmpVal=str_replace("&nbsp;",' ', $tmpVal);
				$questionInfo[0][$key] = $tmpVal;			
			}
			
		}
		return $questionInfo[0];
	}
	
	/**
	 * function description : Get question parameter details
	 * @param   qcode
	 * @return  array, question parameter information 
	 * 
	 * */
	
	function fetchquesParams($qcode)
	{
		$questionParamInfo = array();
		$tbl='questionParameters';
		if($this->session->userdata('liveEditPreview')=='liveEditPreview'){
			$tbl='liveEditQuestionParameters';
		}
		reRunUsingGoto:
		$this->dbEnglish->Select('*');
		$this->dbEnglish->from($tbl);
		$this->dbEnglish->where('qcode',$qcode);
		$query = $this->dbEnglish->get();
		$paramInfoSet = $query->result_array();
		
		if(empty($paramInfoSet) && $tbl=="liveEditQuestionParameters"){
			$tbl='questionParameters';
			goto reRunUsingGoto;
		}
		
        foreach($paramInfoSet as $value){
            $questionParamInfo[$value['paramName']] = $value['paramValue'];
        }
		return $questionParamInfo;
	}

	/**
	 * function description : Get passage details
	 * @param   passageID
	 * @return  array, passage information 
	 * 
	 * */
	function getPassageInfo($passageID){
		$passageInfo = array();
		$this->dbEnglish->Select('*');
		$this->dbEnglish->from('passageMaster');
		$this->dbEnglish->where('passageID',$passageID);
		$query = $this->dbEnglish->get();
		$passageInfo = $query->result_array();
		foreach ($passageInfo[0] as $key => $value) {
			$passageInfo[0][$key] = stripcslashes($value);
			
		}
		return $passageInfo[0];
	}
	
	function encrypt($str_message) {
		$len_str_message=strlen($str_message);
		$Str_Encrypted_Message="";
		for ($Position = 0;$Position<$len_str_message;$Position++)        {
			$Byte_To_Be_Encrypted = substr($str_message, $Position, 1);
			$Ascii_Num_Byte_To_Encrypt = ord($Byte_To_Be_Encrypted);

				   $Ascii_Num_Byte_To_Encrypt = $Ascii_Num_Byte_To_Encrypt + 5;
				   $Ascii_Num_Byte_To_Encrypt = $Ascii_Num_Byte_To_Encrypt * 2;

			$Str_Encrypted_Message .= $Ascii_Num_Byte_To_Encrypt."-";
		}
		$Str_Encrypted_Message = substr($Str_Encrypted_Message,0,-1);
		return $Str_Encrypted_Message;
	} //end function

}

?>