<?php

class Sample extends Home {

    function __construct(){
       
        parent::__construct();
    }
	
    public function index()
    {
    	
    	 $this->template->write('title', 'Welcome User');
         $this->template->write_view('content', 'index', $this->data);
         $this->template->render();
    }
   }
 ?>