<?php

Class Simulationflow extends CI_Controller
{

	function __construct()
    {
        parent::__construct();
        
        $this->dbEnglish = $this->load->database('mindspark_english',TRUE);
        $this->questionAttemptClassTbl="questionAttempt_class";
        $this->load->model('Language/User_model','user_model');
        $this->load->model('Language/simulationContentFlowlogic_model','simulationContentFlowlogic');
    }	
	/*simulation functionality*/
 	public function contentFlowsimulation($userID) {
 		$testing = $this->simulationContentFlowlogic->contentFlowsimulation($userID);
 	}


 	public function index() {

 		$this->load->view('Language/simulation/index');
 	}


 	public function userIDcheck() {	
 		if(isset($_POST)) :
 			$userID =  $this->input->post('userID');
 		 	$resultArr=$this->user_model->getUserData($userID);
 		 	if($resultArr):
 		 		$userID=$resultArr['userID'];
 		 	endif;
 			echo $userID;
 		else :
 			echo 0;
 		endif;

 	}

 	public function filter () {

 		if(isset($_POST)) :
 			$this->load->view('Language/simulation/index');	
 		else :
 			redirect(site_url('Language/simulation/index'));
 		endif;
 	}
}
?>