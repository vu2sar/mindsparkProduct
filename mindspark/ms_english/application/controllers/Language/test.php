<?php

Class test extends CI_Controller
{

	function __construct()
    {
        parent::__construct();
        
        $this->dbEnglish = $this->load->database('mindspark_english',TRUE);
        $this->questionAttemptClassTbl="questionAttempt_class";
        


    }	

    function getallSession()
    {
    	//$this->session->unset_userdata('sessionfreeQues');
    	echo '<pre>'; print_r($this->session->all_userdata());
    }

	//7/500010
	function testGroupSkillLogic($selectedGroupID="",$userID="",$qAttempt1="",$qAttempt2="")	{
		if($selectedGroupID==""||$userID==""){
			echo "wrong parameter passed";
			echo "<br>";echo "<br>";
			echo "sample param1 localhost mindspark/ms_english/Language/test/testGroupSkillLogic/7/500010";
			echo "<br>";echo "<br>";
			echo "sample param2 localhost mindspark/ms_english/Language/test/testGroupSkillLogic/7/500010/3-5/4-2";
			echo "<br>";echo "<br>";
			echo "cumpulosory param1=selectedGroupID param2=userID ";
			echo "<br>";echo "<br>";
			echo "optional param3=prevSkillAttempt-PrevSkillquesattempt param4=lastSkillAttempt-lastSkillquesattempt";
			exit;

		}

		echo "User will not come out of group skill logic till no questions are pending in that skill";
		echo "<br>";echo "<br>";
		echo "sample param1 localhost mindspark/ms_english/Language/test/testGroupSkillLogic/7/500010";
		echo "<br>";echo "<br>";
		echo "sample param2 localhost mindspark/ms_english/Language/test/testGroupSkillLogic/7/500010/3-5/4-2";
		echo "<br>";echo "<br>";
		echo "cumpulosory param1=selectedGroupID param2=userID ";
		echo "<br>";echo "<br>";
		echo "optional param3=prevSkillAttempt-PrevSkillquesattempt param4=lastSkillAttempt-lastSkillquesattempt";

		$this->load->model('Language/test_model');
		$this->test_model->__construct();
		//echo "select class from userDetails where userId=".$userID."";

		
		
		//$selectedGroupID=7;
		//$userID=500010;
		$this->test_model->intiallazeVarAndCallFunc($selectedGroupID,$userID,$qAttempt1,$qAttempt2);
		exit;
		
	}
	function testDefaultSkillLogic($userID="",$qAttempt1="",$qAttempt2="")	{

		$selectedGroupID=7;
		if($selectedGroupID==""||$userID==""){
			echo "wrong parameter passed";
			echo "<br>";echo "<br>";
			echo "sample param1 localhost mindspark/ms_english/Language/test/testDefaultSkillLogic/500010";
			echo "<br>";echo "<br>";
			echo "sample param2 localhost mindspark/ms_english/Language/test/testDefaultSkillLogic/500010/3-5/4-2";
			echo "<br>";echo "<br>";
			echo "cumpulosory param1=selectedGroupID param2=userID ";
			echo "<br>";echo "<br>";
			echo "optional param3=prevSkillAttempt-PrevSkillquesattempt param4=lastSkillAttempt-lastSkillquesattempt";
			exit;

		}

	
		echo "sample param1 localhost mindspark/ms_english/Language/test/testDefaultSkillLogic/500010";
		echo "<br>";echo "<br>";
		echo "sample param2 localhost mindspark/ms_english/Language/test/testDefaultSkillLogic/500010/3-5/4-2";
		echo "<br>";echo "<br>";
		echo "cumpulosory param1=selectedGroupID param2=userID ";
		echo "<br>";echo "<br>";
		echo "optional param3=prevSkillAttempt-PrevSkillquesattempt param4=lastSkillAttempt-lastSkillquesattempt";

		$this->load->model('Language/simulationfreequesdefaultskilllogic');
		$this->simulationfreequesdefaultskilllogic->__construct();
		//echo "select class from userDetails where userId=".$userID."";

		
		
		//$selectedGroupID=7;
		//$userID=500010;
		$this->simulationfreequesdefaultskilllogic->intiallazeVarAndCallFunc($selectedGroupID,$userID,$qAttempt1,$qAttempt2);
		exit;
		
	}

	public function sessiontest()
	{
		$session_data = $this->session->all_userdata();

		echo '<pre>';
		print_r($session_data);
	}

	
}


?>