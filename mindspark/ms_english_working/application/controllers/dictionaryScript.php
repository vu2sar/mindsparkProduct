<?php

class DictionaryScript extends CI_Controller {

    function __construct(){
       
        parent::__construct();
        $this->load->model('dictionaryScript_model');
    }
	
    public function index()
    {
    	
    	$get_data = $this->dictionaryScript_model->getDicWords();
        echo "<pre>";print_r($get_data);echo "</pre>";
    }
}
?>