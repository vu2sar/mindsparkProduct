<?php



Class Endsessionreport extends MY_Controller
{
	
	function __construct(){
        parent::__construct();
    }
	function index()
	{
		
	}

	/**
	 * function role : Generate end sesion report
	 * param1 : userID
	 * @return  json object, End sesison report statistics
	 * 
	 * */

	function getStudentEndSessionReport()
	{

		//$sessionID = $this->session->userdata('sessionID');
		$this->load->model('Language/endsessionreport_model');
		//echo $this->endsessionreport_model->getReport($userID);
		if($this->category == 'School Admin' || $this->category == 'TEACHER' || $this->category == 'ADMIN')
		{
			$user_id           = $_POST['userid'];
			$startDate         = $_POST['startDate'];
			$endDate           = $_POST['endDate'];
			$attemptTableClass = $_POST['childClass'];
		}
		else
			$user_id = $this->user_id;

		$responseArr=$this->endsessionreport_model->getReport($user_id,$startDate,$endDate,$attemptTableClass);

		//echo $this->endsessionreport_model->getReportDetails($userID); -- Already commented

		echo $this->return_response($responseArr);
	}


}



?>