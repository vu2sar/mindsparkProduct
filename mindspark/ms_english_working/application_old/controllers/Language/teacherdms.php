<?php


Class TeacherDms extends MY_Controller
{
	function __construct(){
        parent::__construct();
		$this->load->model('Language/teacherdms_model');
		$this->load->model('Language/teachermystudents_model');

    }

	function getTeacherMappedClassDMS(){
		
		
		if(strtolower($this->category)=='teacher' && strtolower($this->sub_category)=='school')
		{
			$teacher_class=$this->teacher_class;
			$classArr=array();
			foreach (explode("~",$teacher_class) as $key => $cls_section) {
				$data=explode(",",$cls_section);
				if(!isset($classArr[$data[0]]))
					$classArr[$data[0]]=$data[0];
			}
			$classArr[$this->child_class] = $this->child_class;
		}
		else if(strtolower($this->category) == 'school admin' || strtolower($this->category)=='admin'){
			$classArr=$this->teachermystudents_model->getAdminClassArr($this->school_code);

		}
		echo $this->return_response(json_encode(array_filter(array_unique($classArr))));
	}

	function getCurrentStatus()
	{
		
		$grade = $_POST['class'];

		$studentFlowArr = $this->teacherdms_model->getCurrentStatus($grade,$this->user_id);
		echo $this->return_response(json_encode($grade));
	}
}

?>