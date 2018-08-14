<?php

Class Interfaceanalysis_model extends CI_model
{
	public function __construct() 
	{
		 $this->dbEnglish = $this->load->database('mindspark_english',TRUE);
		 $this->Companies_db = $this->dbEnglish;

	       // Pass reference of database to the CI-instance
	     $CI =& get_instance();
	     $CI->Companies_db =& $this->Companies_db; 
	}

	function saveContentLoadingTime($entityCode,$entityType,$totalTime,$sessionID,$userID)
	{
		$data = array(
				'userID' => $userID,
				'sessionID' => $sessionID,
				'entityCode' => $entityCode,
				'entityType' => $entityType,
				'totalTime' => $totalTime
			);

			$this->dbEnglish->insert('contentLoadingTime', $data); 
	}

}
?>