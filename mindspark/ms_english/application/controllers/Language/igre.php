<?php

Class Igre extends MY_Controller
{
	function __construct(){
       
        parent::__construct();
    }
	function index()
	{
		$this->load->view('Language/index.html');
	}

	function getIGREInfo($key = '')
	{
		$this->load->model('Language/igre_model');
		$responseArr=$this->igre_model->getIGREInfo($key);
		echo $this->return_response(json_encode($responseArr));
	}
}

?>