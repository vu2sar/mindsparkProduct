<?php

Class teacherreport extends MY_Controller
{
	function __construct()
    {
        parent::__construct();
        $this->load->model('Language/teacherreport_model');
		$this->load->model('Language/teachermystudents_model');

    }
	/*function index()
	{
		$this->load->view('Language/index.html');
	}*/

	//	Teacher Interface Functions
	function getTeacherMappedClass(){
		//print $this->category;
		if(strtolower($this->category)=='teacher' && strtolower($this->sub_category)=='school')
		{
			$teacher_class=$this->teacher_class;
			$classArr=array();
			foreach (explode("~",$teacher_class) as $key => $cls_section) {
				$data=explode(",",$cls_section);
				if(!isset($classArr[$data[0]]))
					$classArr[$data[0]]=$data[0];
			}
		}
		else if(strtolower($this->category) == 'school admin' || strtolower($this->category)=='admin'){
			$classArr=$this->teachermystudents_model->getAdminClassArr($this->school_code);
		}
		
		echo $this->return_response(json_encode($classArr));
	}

	function getTeacherMappedSection(){
		
		$selectedClass=$_REQUEST['selectedClass'];

		if(strtolower($this->category)=='teacher' && strtolower($this->sub_category)=='school')
		{
			$teacher_class=$this->teacher_class;
			$sectionArr=array();
			foreach (explode("~",$teacher_class) as $key => $cls_section) {
				$data=explode(",",$cls_section);
				if($data[0]==$selectedClass)
				array_push($sectionArr,$data[1]);
			}
			sort($sectionArr);
		}else if(strtolower($this->category) == 'school admin' || strtolower($this->category)=='admin'){
			$sectionArr=$this->teachermystudents_model->getAdminSectionArr($selectedClass,$this->school_code);
			sort($sectionArr);
		}
		
		echo $this->return_response(json_encode($sectionArr));
	}


	//function generateTeacherReportForStudentInterface($dateMode,$reportMode,$grade,$section,$startDate="",$endDate="")
	function generateTeacherReportForStudentInterface()
	{		

		$reportData = array();
		$outliersData = array();
		
		$startDate  = date("Y-m-d", strtotime($_REQUEST['startDate']));
 		$endDate    = date("Y-m-d", strtotime($_REQUEST['endDate']));
 		$report_Mode = $_REQUEST['reportMode'];
 		if($report_Mode == '0')
 			$reportMode = 'none';
 		if($report_Mode == '1')
 			$reportMode = 'ALL';

 		$grade      = $_REQUEST['childclass'];
 		$section    = $_REQUEST['childsection'];

 		//$tempArr=array("class" => $_REQUEST['childclass'],"section" => $_REQUEST['childsection'],"school_code" =>$this->school_code, "start_date" => $_REQUEST['startDate'], "end_date" =>$_REQUEST['endDate']);


 		$responseArr=$this->teacherreport_model->getOverallUsageSummary($this->school_code,$grade,$section  ,$startDate,$endDate);
                $responseArr=array_values($responseArr);
 		echo $this->return_response(json_encode($responseArr));

		//$fetch_student_details = $this->teacherreport_model->fetchStudentsInSelectedPeriod($startDate,$endDate,$reportMode,$grade,$section,$this->school_code);
		
		/*if(count($fetch_student_details) == 0 && empty($fetch_student_details))
		{
			$reportData = '';
			$outliersData['accuracyLessThan20Per'] = '';
			$outliersData['accuracyMoreThan80Per'] = '';
			$outliersData['noLoggedMoreThanSelectedDays'] = $this->teacherreport_model->fetchStudentsNotLoggedInSelectedPeriod();

		}
		else
		{*/
			//$this->teacherreport_model->generateQuestionAttemptData();
	 		//$this->teacherreport_model->generatePassageTimeData();
	 		//$this->teacherreport_model->generateContentAttemptedSummary();
	 		//$reportData = $this->teacherreport_model->teacherReportData;
	 		//echo "<pre>";print_r($reportData);echo "</pre>";
	 		//echo "<pre>";print_r($reportData);echo "</pre>";
	 		//echo "<pre>";print_r($reportData);echo "</pre>";
	 		//$this->teacherreport_model->getOutliersData();
 			//$outliersData = $this->teacherreport_model->outlierArr;
		//}

 		//$this->teacherreport_model->getOutliersData($dateMode);
 		

		//echo $this->return_response(json_encode(array('reportData'=>$reportData,'outliersData'=>$outliersData,'startDate'=>$startDate,'endDate'=>$endDate,'lastLoggedInStartDate'=>$this->teacherreport_model->lastLoggedInStartDate)));

	}

	function generateTeacherReportForAllSkills()
	{
		/*$startDate = '2016-02-22';
 		$endDate = '2016-03-22';
 		$reportMode = 'ALL'; // if all then it will show the data of all the class and section
 		$grade = '5';
 		$section = 'A';*/

 		$startDate  = date("Y-m-d", strtotime($_REQUEST['startDate']));
 		$endDate    = date("Y-m-d", strtotime($_REQUEST['endDate']));
 		$grade      = $_REQUEST['childclass'];
 		$section    = $_REQUEST['childsection'];
 		$reportMode = $_REQUEST['reportMode'];
 		if($reportMode == '0')
 			$reportMode_send = 'none';
 		if($reportMode == '1')
 			$reportMode_send = 'ALL';
 		//get mapped class and section
 		
		if(strtolower($this->category)=="teacher")
			$get_mapped_class_sec = $this->teacherreport_model->getTeacherMappedClassSec();
		else
			$get_mapped_class_sec = $this->teacherreport_model->getAdminMappedClassSec($this->school_code);
		
		
 		//end
		// $tempArr=array("class" => $_REQUEST['class'],"section" => $_REQUEST['section'],"school_code" =>$this->school_code, "start_date" => $_REQUEST['start_date'], "end_date" =>$_REQUEST['end_date']);
		$tempArr=array("class" => $grade,"section" => $section,"school_code" =>$this->school_code, "start_date" => $startDate, "end_date" =>$endDate, "reportMode" => $reportMode_send);
		$vocab_grammer = $this->teacherreport_model->getSkillwiseAccuracyDetailsTopic($tempArr);
		
		$reading_listining = $this->teacherreport_model->getSkillwiseAccuracyDetailsPassage($tempArr);
		$arr2 = array();
		$new_array_new = array();

		$new_array = array_merge($vocab_grammer,$reading_listining);

		foreach ($new_array as $key => $value) 
		{
			$arr2[$value['userID']][]   = $value;
		}

		$final_array = array();
		$index = 0;
		foreach ($arr2 as $key => $value)
		{
			$str_value = '';
			foreach ($value as $sub_key => $sub_value) 
			{
				foreach ($sub_value as $key2 => $value2) 
				{
					if($key2 == 'name')
						$str_value = $value2;
					elseif ($key2 == 'studentAcc')
					{
						$str_value .= '|'.$value2;
					}
					else
						$final_array[$index][$key2] = $value2;
				}
				$temp_arr = explode('|', $str_value);
				$final_array[$index][$temp_arr[0]] = $temp_arr[1];
			}
			$index++;
		}
		$final_array_all_skills['all_skills'] = $final_array;
		$final_array_all_skills['class_sec'] = $get_mapped_class_sec;
		echo $this->return_response(json_encode($final_array_all_skills));
	}

	function generateTeacherReportForGrammerSkills()
	{
		$startDate  = date("Y-m-d", strtotime($_REQUEST['startDate']));
 		$endDate    = date("Y-m-d", strtotime($_REQUEST['endDate']));
 		$grade      = $_REQUEST['childclass'];
 		$section    = $_REQUEST['childsection'];
 		$reportMode = $_REQUEST['reportMode'];
 		if($reportMode == '0')
 			$reportMode_send = 'none';
 		if($reportMode == '1')
 			$reportMode_send = 'ALL';


 		/*$startDate  = "2016-03-22";
 		$endDate    = "2016-03-23";
 		$grade      = '7';
 		$section    = 'A';*/
 		//get mapped class and section
 		
		if(strtolower($this->category)=="teacher")
			$get_mapped_class_sec = $this->teacherreport_model->getTeacherMappedClassSec();
		else
			$get_mapped_class_sec = $this->teacherreport_model->getAdminMappedClassSec($this->school_code);
	
 		//end
 		

		$tempArr=array("class" => $grade,"section" => $section,"school_code" =>$this->school_code, "start_date" => $startDate, "end_date" =>$endDate, "reportMode" => $reportMode_send);

		$sentence_formation_accuracy            = $this->teacherreport_model->get_sentence_formation_accuracy($tempArr);
		
		$obj_ref_relation_accuracy              = $this->teacherreport_model->get_obj_ref_relation_accuracy($tempArr);
		
		$verbs_verbforms_accuracy               = $this->teacherreport_model->get_verbs_verbforms_accuracy($tempArr);
		
		$describing_words_accuracy              = $this->teacherreport_model->get_describing_words_accuracy($tempArr);
		
		$nouns_pronouns_accuracy                = $this->teacherreport_model->get_nouns_pronouns_accuracy($tempArr);
		
		$punctuations_spelling_phonics_accuracy = $this->teacherreport_model->get_punctuations_spelling_phonics_accuracy($tempArr);
		
		$word_meanings_accuracy                 = $this->teacherreport_model->get_word_meanings_accuracy($tempArr);
		
		$userid_wise_array = array();
		$merged_arrays = array_merge($sentence_formation_accuracy, $obj_ref_relation_accuracy, $verbs_verbforms_accuracy, $describing_words_accuracy, $nouns_pronouns_accuracy, $punctuations_spelling_phonics_accuracy, $word_meanings_accuracy);

		foreach ($merged_arrays as $key => $value) 
		{
			$userid_wise_array[$value['userID']][]   = $value;
		}

		$final_array = array();
		$index = 0;
		foreach ($userid_wise_array as $key => $value)
		{
			$str_value = '';
			foreach ($value as $sub_key => $sub_value) 
			{
				foreach ($sub_value as $key2 => $value2) 
				{
					if($key2 == 'name')
					{
						$str_value = str_replace(' ', '_', strtolower($value2));
					}
					elseif ($key2 == 'studentAcc')
					{
						$str_value .= '|'.$value2;
					}
					else
						$final_array[$index][$key2] = $value2;
				}
				
				$temp_arr = explode('|', $str_value);
				$final_array[$index][$temp_arr[0]] = $temp_arr[1];
			}
			$index++;
		}
		$final_array_all_skills['grammer_skills'] = $final_array;
		$final_array_all_skills['class_sec'] = $get_mapped_class_sec;
		//echo "<pre> final_array_all_skills=>";print_r($final_array_all_skills);echo "</pre>";
		echo $this->return_response(json_encode($final_array_all_skills));
	}

}

?>