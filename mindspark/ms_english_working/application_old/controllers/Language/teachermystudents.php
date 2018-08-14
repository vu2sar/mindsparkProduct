<?php

Class teachermystudents extends MY_Controller
{
	function __construct()
    {
        parent::__construct();
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


	//function showEditStudentDetails($class, $section, $childname='', $school_code)
	function showEditStudentDetails()
	{	
		$responseArr=array();
		$tempArr=array("class" => $_REQUEST['class'],"section" => $_REQUEST['section'],"school_code" =>$this->school_code, "childName" => $_REQUEST['childName']);
		//echo "<pre>";print_r($tempArr);echo "</pre>";
 		$responseArr['studentData'] = $this->teachermystudents_model->getUserData($tempArr);

		echo $this->return_response(json_encode($responseArr));

	}

	function updateStudentDetails()
	{
		$tempArr = array("msg" => 'false',"userID" => $_POST['userID'], "userName" => $_POST['userNameTxt'], "childName" => $_POST['childNameTxt'], "childEmail" => $_POST['childEmailTxt'], "DOB" => $_POST['DOBTxt'], "parentEmail" => $_POST['parentEmailTxt'], "password" => $_POST['lstPwd'], "childClass" => $_POST['childClass'], "childSection" => $_POST['childSection']);

		$checkResult = $this->teachermystudents_model->checkUserDetails($tempArr);
		
		
		if(count($checkResult) == 0)
		{
			$this->teachermystudents_model->updateUserDetails($tempArr);
			//change reason save
			$changereason = $_POST['changereason'];
			if($changereason != '')
			{
				$tempArrChangeReason = array("userID" => $_POST['userID'], "changeReason" => $_POST['changereason']);
				$this->teachermystudents_model->saveChangeReason($tempArrChangeReason);			
			}
			echo $this->return_response(json_encode($tempArr));
		}
		else
		{
			$tempArr = array('msg' => 'true');
			echo $this->return_response(json_encode($tempArr));	
		}

		
	}

	function getGroupSkill(){
		$classArr=array();
		$getGroupSkill = $this->teachermystudents_model->getGroupSkillMaster();
		foreach ($getGroupSkill as $key => $value) {
				$classArr[$key]=$value;
		}
		echo $this->return_response(json_encode($classArr));
	}

	function saveCurrentStatusSkill()
	{
		$tempArr = array("userID" => $this->user_id, "groupSkillID" => $_POST['groupSkillID'], "start_date" => $_POST['start_date'], "end_date" => $_POST['end_date'], "grade" => $_POST['grade'], "section" => $_POST['section'], "school_code" =>$this->school_code);
		$this->teachermystudents_model->saveCurrentStatusSkill($tempArr);
		$msg = 'Details updated/saved successfully.';
		//echo $this->return_response(json_encode($tempArr));
		echo $this->return_response(null, $msg, SUCCESS);
	}


	function showEditTeacherDetails()
	{	
		$responseArr = array();
		$userID      = $this->user_id;
		$school_code = $this->school_code;
		//$tempArr   = array("class" => $_REQUEST['class'],"section" => $_REQUEST['section'],"school_code" =>$this->school_code, "childName" => $_REQUEST['childName']);
		//echo "<pre>";print_r($tempArr);echo "</pre>";
 		$responseArr['teacherData'] = $this->teachermystudents_model->getTeacherData($userID,$school_code);

		echo $this->return_response(json_encode($responseArr));
	}

	function updateTeacherDetails()
	{
		$tempArr = $_POST['userData'];
		$this->teachermystudents_model->updateTeacherDetails($tempArr);

		echo $this->return_response(json_encode($tempArr));
	}

}

?>