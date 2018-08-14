<?php

Class InterfaceAnalysis extends MY_Controller
{
	function __construct(){
       
        parent::__construct();
    }
	function index()
	{	
		$this->load->view('Language/index.html');
	}

	function saveContentLoadingTime($entityCode,$entityType,$totalTime)
	{
		//$sessionID = $this->session->userdata('sessionID');
		//$userID = $this->session->userdata('userID');
		$this->load->model('Language/interfaceanalysis_model');
		//$this->interfaceanalysis_model->saveContentLoadingTime($entityCode,$entityType,$totalTime,$sessionID,$userID);
		$this->interfaceanalysis_model->saveContentLoadingTime($entityCode,$entityType,$totalTime,$this->session_id,$this->user_id);
		echo $this->return_response();
	}
}

?>