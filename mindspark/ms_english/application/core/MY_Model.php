<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model
{
	const questionAttemptTbl="questionAttempt";
	public $questionAttemptClassTbl;
	public $questionAttemptTblName;
	
	public function __construct()
    {
        parent::__construct();
		$this->questionAttemptClassTbl = $this->getQuesAttemptClass();

		$this->questionAttemptTblName = "questionAttempt_class";
		$this->contExhaustMailSubject = 'Content exhaustion Alert';
		//$this->alertStudentMailSubject = 'Essay Reviewed - Mindspark Essay Writer';
		$this->freeQuesContExhaustMailMessage = 'Content exhausted for Grade '.$this->session->userdata('freeQuesLevel').' Free Questions';
		$this->readingPsgContExhaustMailMessage = 'Content is exhausted for Grade '.$this->session->userdata('passageLevel').' Reading Passages';
		$this->listeningPsgContExhaustMailMessage = 'Content exhausted for Grade '.$this->session->userdata('conversationLevel').' Conversation Passages';
		$this->readingPsgMoveNextLevelMessage = 'The user '.$this->session->userdata('childName').' of class '.$this->session->userdata('childClass').' from '.$this->session->userdata('schoolName').' has been upgraded to higher level due to higher accuracy in existing reading passages level';
		$this->listeningPsgMoveNextLevelMessage = 'The user '.$this->session->userdata('childName').' of class '.$this->session->userdata('childClass').' from '.$this->session->userdata('schoolName').' has been upgraded to higher level due to higher accuracy in existing listening passages level';

		$this->remediationConstant = $this->session->userdata('remediationConstant');
		$this->remediationAccuracyConst = $this->session->userdata('remediationAccuracyConst');
	}

	public function getQuesAttemptClass(){
		$childClass=$this->session->userdata('childClass');
		$questionAttemptClassTbl=self::questionAttemptTbl.'_class'.$childClass;
		return $questionAttemptClassTbl;
	}

	function sendMail($message)
	{
	    /*$config = Array(
		  'protocol' => 'smtp',
		  'smtp_host' => 'ssl://smtp.googlemail.com',
		  'smtp_port' => 465,
		  'smtp_user' => 'bhaveshtilvani@gmail.com', // change it to yours
		  'smtp_pass' => '27021992', // change it to yours
		  'mailtype' => 'html',
		  'charset' => 'iso-8859-1',
		  'wordwrap' => TRUE
		);
	    $this->load->library('email', $config);
	    $this->email->set_newline("\r\n");
        $this->email->from('bhaveshtilvani@gmail.com'); // change it to yours
        $list = array('harsha.dediya@ei-india.com', 'venkateshwarlu.maguloori@ei-india.com');
        $this->email->to($list);// change it to yours
        $this->email->subject($this->contExhaustMailSubject);
        $this->email->message($message);
        if($this->email->send())
        {
	      	return true;
	    }
	    else
	    {
	    	show_error($this->email->print_debugger());
	    }*/

	}

	/*function alertStudentMail($message,$toEmail){
		
		$config = Array(
		  'protocol' => 'smtp',
		  'smtp_host' => 'ssl://smtp.googlemail.com',
		  'smtp_port' => 465,
		  'smtp_user' => 'bhaveshtilvani@gmail.com', // change it to yours
		  'smtp_pass' => '27021992', // change it to yours
		  'mailtype' => 'html',
		  'charset' => 'iso-8859-1',
		  'wordwrap' => TRUE
		);
	    
	    $this->load->library('email', $config);
	    $this->email->set_newline("\r\n");
        $this->email->from('essay.admin@ei-india.com'); // change it to yours
        //$list = ($toEmail=="")?"essay.admin@ei-india.com":$line['email'];
        //print $toEmail;
        $list = array('bhavesh.tilvani@ei-india.com');
        $this->email->to($list);// change it to yours
        $this->email->subject($this->alertStudentMailSubject);
        $this->email->message($message);
        if($this->email->send())
        {
	      	return true;
	    }
	    else
	    {
	    	show_error($this->email->print_debugger());
	    }
	}*/

	/**
	 * function role : Get time spent by user 
	 * param1 : userID, startDate, endDate, class
	 * @return   value - timespent
	 *
	 * */

	public function getTimeSpentOfUser($userID,$startDate,$endDate,$class)
	{
		//if($class==""){
			///$tbl_quesAttempt=$this->questionAttemptClassTbl;
		//}else{
			$tbl_quesAttempt="questionAttempt_class".$class;
		//}		
	 	$tbl_sessionStatus = "sessionStatus";		
		$this->dbEnglish->Select('DISTINCT(sessionID), startTime, endTime');
	 	$this->dbEnglish->from($tbl_sessionStatus);
	 	//$this->dbEnglish->join('userDetails B','A.userID = B.userID','RIGHT');
	 	$this->dbEnglish->where('userID',$userID);
	 	$this->dbEnglish->where('startTime_int >=',$this->getIntDate($startDate));
	 	$this->dbEnglish->where('startTime_int <=',$this->getIntDate($endDate));

		$timeSpent = 0;		
		$query = $this->dbEnglish->get();
		$timeSpentArr = $query->result_array();
		foreach($timeSpentArr as $key=>$time_line)
		{		 	
			$startTime = $this->convertToTime($time_line["startTime"]);
			if($time_line["endTime"]!="")
				$endTime = $this->convertToTime($time_line["endTime"]);
			else
			{
				$this->dbEnglish->Select('max(lastModified) as dateModified');
	 			$this->dbEnglish->from($tbl_quesAttempt);
	 			$this->dbEnglish->where('sessionID',$time_line["sessionID"]);
	 			$this->dbEnglish->where('userID',$userID);
				$queryMaxL = $this->dbEnglish->get();
				$dataMaxL = $queryMaxL->result_array();	

				if($dataMaxL[0]["dateModified"]=="")
					continue;
				else
					$endTime = $this->convertToTime($dataMaxL[0]["dateModified"]);
			}
			$timeSpent = $timeSpent + ($endTime - $startTime);
		} 

		return $timeSpent;
	 
	}

	/**
	 * function role : Get total student of section
	 * param1 : schoolCode,class,section, returnArrType = associative or index array
	 * @return   array 
	 *
	 * */

	public function getStudentDetailsBySection($schoolCode,$class,$section,$returnArrType='') {
		$userArray = array();
		$this->dbEnglish->Select('userID,childName,username');
	 	$this->dbEnglish->from('userDetails');
	  	$this->dbEnglish->where('schoolCode',$schoolCode);
	 	$this->dbEnglish->where('childClass',$class);
	 	
	 	if($section != "") {
			$sectionsArray = explode(",",$section);
			if(count($sectionsArray)>1){
				$this->dbEnglish->where_in('childSection',$sectionsArray);
			}
			else{
				$this->dbEnglish->where('childSection',$sectionsArray[0]);			
			}
		}
		$this->dbEnglish->where('enabled', '1');
		$this->dbEnglish->where('endDate >= curdate()');
		$this->dbEnglish->where('category', 'STUDENT');
		$this->dbEnglish->order_by('childName');
		//$this->dbEnglish->group_by("B.userID");

		$query = $this->dbEnglish->get();

		$userDataArr = $query->result_array();
		foreach($userDataArr as $key=>$valueArr)
		{		 		
		 	$userDetails = array();
			$userDetails['userID'] = $valueArr['userID'];
			$userDetails['name'] = $valueArr['childName'];
			$userDetails['username']=$valueArr['username'];
			if($returnArrType=="assoc"){
				$userArray[$valueArr['userID']] = $userDetails;
			}else{
				$userArray[] = $userDetails;
			}
			
		}
		return $userArray;
	}


	public function getTeacherListArr($schoolCode) {
	
		$this->dbEnglish->Select('userID');
	 	$this->dbEnglish->from('userDetails');
	  	$this->dbEnglish->where('schoolCode',$schoolCode);
	 	$this->dbEnglish->where('category', 'TEACHER');
		$query = $this->dbEnglish->get();
		$resultArr = $query->result_array();
		$teacherIDArr= array();
		foreach($resultArr as $key=>$value)
		{
			array_push($teacherIDArr,$value['userID']);
		}
		return $teacherIDArr;
	}
	/**
	 * function role : Get column values of array
	 * param1 : array input, columnKey, indexKey
	 * @return   array 
	 *
	 * */

	public function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( ! isset($value[$columnKey])) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( ! isset($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }

    /**
	 * function role : convert the time in hour minute second : HMS
	 * param1 : time
	 * @return    value in HMS
	 *
	 * */

	public function convertTimeInHMS($timeSpent,$hms="hms")
	{

		$hours = str_pad(intval($timeSpent/3600),2,"0",STR_PAD_LEFT);
		//converting secs to hours.
		$timeSpent = $timeSpent%3600;
		$mins  = str_pad(intval($timeSpent/60),2,"0", STR_PAD_LEFT);
		$timeSpent = $timeSpent%60;
		$secs  = str_pad($timeSpent,2,"0",STR_PAD_LEFT);
		if($hms=="hms"){
			return $hours.":".$mins.":".$secs;
		}else if($hms=="hm"){
			return $hours.":".$mins;
		}	
		

	}

	 /**
	 * function role : convert date into time
	 * param1 : date
	 * @return    value time
	 *
	 * */

	public function convertToTime($date)
	{
		$hr   = substr($date,11,2);
		$mm   = substr($date,14,2);
		$ss   = substr($date,17,2);
		$day  = substr($date,8,2);
		$mnth = substr($date,5,2);
		$yr   = substr($date,0,4);
		$time = mktime($hr,$mm,$ss,$mnth,$day,$yr);
		return $time;
	}

	public function getIntDate($date) {
		return str_replace('-', '', $date);
	}

	function logForImgAudioNotLoading($data)
	{
		if(!empty($data) && count($data) > 0)
		{
			$data_insert = array(
			   'page'   => $data['page'],
			   'itemId' => $data['itemId'],
			   'msg'    => $data['msg'],
			   'userID' => $data['userID']
			);

			$this->dbEnglish->Select('*');
	    	$this->dbEnglish->from('imgAudioNotLoadingLog');
	    	$this->dbEnglish->where('page',$data['page']);
	    	$this->dbEnglish->where('itemId',$data['itemId']);
	    	$this->dbEnglish->where('msg',$data['msg']);
	    	$this->dbEnglish->where('userID',$data['userID']);
	    	$query = $this->dbEnglish->get();
	    	$result = $query->result_array();
	    	if(count($result) > 0)
	    	{	
				$this->dbEnglish->where('page',$data['page']);
		    	$this->dbEnglish->where('itemId',$data['itemId']);
		    	$this->dbEnglish->where('msg',$data['msg']);
		    	$this->dbEnglish->where('userID',$data['userID']);
				$this->dbEnglish->update('imgAudioNotLoadingLog',$data_insert);
	    	}
	    	else
				$this->dbEnglish->insert('imgAudioNotLoadingLog', $data_insert);
		}
	}
	
	/**
	 * function description : This function return the the days/week/month/years from current date to target date.
	 * param1   datetime - must be in date time format
         * param2   full default false, full true if you want to show exact duration example - 1 year, 9 months, 3 days, 12 hours, 19 minute & 10 seconds
	 * @return  return in string date ago from current date
	 * 
	 * */
        function time_elapsed_string($datetime, $full = false) {
            $now = new DateTime();
            $then = new DateTime( $datetime );
            $diff = (array) $now->diff( $then );
            $diff['w']  = floor( $diff['d'] / 7 );
            $diff['d'] -= $diff['w'] * 7;

            $string = array(
                'y' => 'year',
                'm' => 'month',
                'w' => 'week',
                'd' => 'day',
                'h' => 'hour',
                'i' => 'minute',
                's' => 'second',
            );

            foreach( $string as $k => & $v )
            {
                if ( $diff[$k] )
                {
                    $v = $diff[$k] . ' ' . $v .( $diff[$k] > 1 ? 's' : '' );
                }
                else
                {
                    unset( $string[$k] );
                }
            }

            if (!$full)
                $string = array_slice($string, 0, 1);
            return $string ? implode(', ', $string) . ' ' : '';
    }
}