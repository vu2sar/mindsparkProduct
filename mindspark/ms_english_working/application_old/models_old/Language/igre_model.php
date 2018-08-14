<?php

Class Igre_model extends CI_model
{
	public function __construct() 
	{
		 $this->dbEnglish = $this->load->database('mindspark_english',TRUE);
		 //$this->dbESL = $this->load->database('mindspark_ESL',TRUE);
		 $this->load->model('Language/user_model');
		 $this->Companies_db = $this->dbEnglish;

	       // Pass reference of database to the CI-instance
	     $CI =& get_instance();
	     $CI->Companies_db =& $this->Companies_db; 
	}


	function getIGREInfo($igreid="")
	{
		$this->dbEnglish->Select('igreid,igreDesc,igrePath,igreType,params');
		$this->dbEnglish->from('IGREMaster');
		$this->dbEnglish->where('status',1);
		if($igreid!="")
			$this->dbEnglish->where('igreid',$igreid);	
		$query = $this->dbEnglish->get();
		$igreInfo = $query->result_array();
		return $igreInfo;
	}
}

?>