<?php


Class TeacherHome extends MY_Controller
{
	function __construct(){
        parent::__construct();
		$this->load->model('Language/teacherhome_model');
		$this->load->model('Language/teachermystudents_model');

    }

	//	Teacher Interface Functions
	function getTeacherMappedClass(){
		//print $this->subcategory;
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

	function getTeacherDashBoardReport(){
		$responseArr=array();
		$sdate = str_replace('/', '-', $_REQUEST['start_date']);
		$_REQUEST['start_date']=date('Y-m-d', strtotime($sdate));

		$edate = str_replace('/', '-', $_REQUEST['end_date']);
		$_REQUEST['end_date']=date('Y-m-d', strtotime($edate));
		
		$schoolCode=$this->school_code;
		$class=$_REQUEST['class'];
		$section=$_REQUEST['section'];
		$startDate=$_REQUEST['start_date'];
		$endDate=$_REQUEST['end_date'];

		$responseArr=$this->teacherhome_model->getOverallUsageSummary($schoolCode, $class, $section, $startDate, $endDate);
		
		echo $this->return_response(json_encode($responseArr));
		
	}
	function getTeacherSettingPageData(){
		$tempArr=array("class" =>$_REQUEST['class'],"section" => $_REQUEST['section'],"school_code" =>$this->school_code,);

		$responseArr=$this->teacherhome_model->getTeacherSettingPageData($tempArr);
		echo $this->return_response(json_encode($responseArr));
	}
	function setTeacherGenSettings(){
		$tempArr=array("class" =>$_REQUEST['class'],"section" => $_REQUEST['section'],"school_code" =>$this->school_code,"session_length" =>$_REQUEST['session_length'],"ground_enable_after" => $_REQUEST['ground_enable_after']);
		$responseArr=$this->teacherhome_model->setTeacherGenSettings($tempArr);
		//echo $this->return_response(json_encode($responseArr));
		$msg = 'Settings saved successfully.';
		echo $this->return_response(null, $msg, SUCCESS);
	}
        
        /**
	*
	 * function role : this function will return the class where the primary teacher not available.
	 * param1 : userID
	 * param2 : essayID
	 * param3 : user's response
	 * param4 : current statatus of the user on essay
	 * param5 : sessionID
	 * @return  none
	 *
	 * */
        function getAlertForNotPrimaryTeacherAvailable(){
                $classNotPrimary=$this->teachermystudents_model->getAlertForNotPrimaryTeacherAvailable($this->school_code);
		echo $this->return_response(json_encode($classNotPrimary));
        }
}

?>