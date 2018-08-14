<?php

Class ContentFlow extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('Language/contentflow_model','contentFlow');
	}

	public function defaultContentFlowOrder() {

		$contentFlow = $this->contentFlow->defaultContentFlowOrder($userID);
	}

	public function nextUserContentFlow($userID) {

		if($userID) :
			$userLastAttmpt = $this->contentFlow->getUserLastAttmpt($userID);
			//echo '<pre>'; print_r($userLastAttmpt); exit;
			// Find the next flow order function 
		else :
			echo 'UserID Parameter Missing';
		endif;
	}

	public function nextContentOrderFlow() {

	}

}
?>