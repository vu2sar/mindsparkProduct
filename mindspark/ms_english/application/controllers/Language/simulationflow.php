<?php

Class simulationflow extends CI_Controller
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
}
?>