
<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Session_model extends CI_Model {
    private $table_session_status = '';
    private $table_diagnostic_question_attempt = '';
    public function __construct() {
        parent::__construct(); 
        $this->load->model('user_model');
        $this->table_session_status = TBL_SESSION_STATUS; 
        $this->table_diagnostic_question_attempt = TBL_DIAGNOSTIC_QUESTIONS;   
        $this->table_research = TBL_RESEARCH_QUESTION_ATTEMPT;
        $this->table_session_extend = TBL_SESSION_EXTEND;
    }

    public function get_active_session($userID)
    {
        
    }

    public function create_session($userID,$ip='')
    {
       $start_time_int = date("Ymd");
       $ip=$_SERVER['REMOTE_ADDR'];
        if(strpos($ip,',') !== false) {
            $ip = substr($ip,0,strpos($ip,','));
        }
        $start_time = date('Y-m-d H:i:s');
        $query = "INSERT INTO $this->table_session_status SET userID=$userID,startTime='$start_time', startTime_int=$start_time_int, noOfJumps=0, ipaddress='$ip'";
        $this->db->query($query);
        return $this->db->insert_id();
    }
    public function create_session_api($userID,$accesstoken,$lat,$long,$device_token,$device_id,$device_details,$device_type,$ip='')
    {
        $sessionid = $this->create_session($userID);
        $insert_array = array('session_id' => $sessionid,
                                'access_token' => $accesstoken,
                                'userID' =>$userID,
                                'lat' => $lat,
                                'long' => $long,
                                'device_token' => $device_token,
                                'device_id' => $device_id,
                                'device_details' => $device_details,
                                'device_type' => $device_type,
                                'insert_time' => date('Y-m-d H:i:s'),
                                'last_modified_time' => '0000-00-00 00:00:00' 
                                );
        $this->db->insert($this->table_session_extend,$insert_array);
        return $sessionid;

    }
    public function session_logout($accesstoken)
    {
        $session_id = $this->get_session_id_from_accesstoken($accesstoken);
        $query = $this->db->query("UPDATE $this->table_session_status SET endTime=if(isnull(endTime), now(), endTime), endType=concat_ws(',',endType,'7'), logout_flag=1 WHERE sessionID=$session_id");

    }
    function check_duplicate_login($timeAllowedPerDay,$userID, $browser="")
    {
        $duplicateSessionID = -1;
        $check_already_login_query  =   "SELECT startTime,endTime,sessionID,'2' as subjectno,ipaddress,browser FROM $this->table_session_status
                                         WHERE userID=$userID AND startTime_int = ".date("Ymd")." AND logout_flag=0
                                         ORDER BY startTime DESC LIMIT 1";
        $result = $this->db->query($check_already_login_query);
        if($result->num_rows() >0)
        {
            $data = $result->result_array();
            foreach ($data as $row) {
            $lastStartTime = $row['startTime'];
            $lastEndTime   = $row['endTime'];
            $lastSessionID = $row['sessionID'];
            $ipaddress  =   $row['ipaddress'];
            $prevBrowser    =   $row['browser'];
           /* $ip = $_SERVER['HTTP_X_FORWARDED_FOR']?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR']; */  //Needed since apps behind a load balancer
           $ip = $_SERVER['REMOTE_ADDR'];
            if(strpos($ip,',') !== false) {
                $ip = substr($ip,0,strpos($ip,','));
            }
            if($lastEndTime=='')
            {                
                if($timeAllowedPerDay=="")
                    $timeAllowedPerDay = 30;
                $startTime = strtotime(date("Y-m-d H:i:s"));                
                $startTime = $startTime - ($timeAllowedPerDay * 60);
                $startTime = date("Y-m-d H:i:s",$startTime);
                if($lastStartTime >= $startTime)
                {
                    if($ipaddress==$ip && $browser==$prevBrowser)
                    {
                        $this->db->query("UPDATE $this->table_session_status SET logout_flag=1 WHERE sessionID=$lastSessionID");
                    }
                    else
                    {
                        $duplicateSessionID = $lastSessionID;
                    }
                }
            }
            }
        }
        
        return $duplicateSessionID;
    }
    public function calculate_time_allowed($userID)
    {
        $total_time_allowed = 30;
        $information_required_array = array('time_allowed_day');
        $data = $this->user_model->get_ms_user_details($userID,$information_required_array);
        if($data)
        {
            $total_time_allowed = $data['time_allowed_day'];
        }
        return $total_time_allowed;
    }
    public function get_session_id_from_accesstoken($accesstoken)
    {
        $session_id =0;
        $this->db->select('session_id');
        $query = $this->db->get_where($this->table_session_extend,array('access_token'=>$accesstoken));
        if($query->num_rows() > 0)
        {
            $data = $query->row_array();
            $session_id =  $data['session_id'];
        }
        return $session_id;

    }
    public function get_session_list($userID)
    {
        $response_array = array();
        $session_list  =   array();
        $query = "SELECT sessionID,date_format(startTime,'%d-%b-%Y %H:%i') starttime,date_format(endTime,'%d-%b-%Y %H:%i') endtime,date_format(tmLastQues,'%d-%b-%Y %H:%i:%s') tmLastQues, time_format(timediff(if(endTime>ifnull(tmLastQues,0),endTime,tmLastQues),startTime),'%H:%i:%s') duration, totalQ,totalTopRevQ,totalCQ,totalTmTst,totalGms,totalMonRevQ FROM $this->table_session_status  WHERE userID = $userID ORDER BY sessionID DESC limit 30";
        $result = $this->db->query($query);
        if($result->num_rows() > 0)
        {
            $all_session_array = $result->result_array();
            foreach ($all_session_array as $session) {
                $temp['session_id'] =  $session['sessionID'];
                $temp['start_time'] =  $session['starttime'];
                $temp['end_time'] =  $this->get_end_time_of_session($session['endtime'],$session['tmLastQues']);
                $temp['duration'] =  $session['duration'];
                $temp['session_details'] = $this->get_sesion_details($userID,$session['sessionID'],$session['totalQ'],$session['totalCQ'],$session['totalTmTst'],$session['totalGms'],$session['totalMonRevQ'],$session['totalTopRevQ']);
                $session_list['session_details'][] = $temp;
            }
            $response_array =  set_model_response(1,'1','success',$session_list); 
           
        }
        else
        {
            $response_array =  set_model_response(0,'SESSION001','No last session found');
        }
        return $response_array;

       
    }
    public function get_end_time_of_session($endtime,$time_of_last_question)
    {
            if($endtime > $time_of_last_question)
                return $endtime;
            else
                return $time_of_last_question;
    }
    public function get_sesion_details($userID,$session_id,$total_question,$total_challenge_question,$total_time_question,$total_games_question,$total_revision_question,$total_topic_revision_question)
    {
        $details_array = array();
        $get_wild_card_sesssion_ids = $this->get_wild_card_question_session_array($userID);
        $total_diagnostic_question = $this->get_diagnostic_question($session_id);
        if(!empty($get_wild_card_question))
        {
           if(in_array($session_id,$get_wild_card_sesssion_ids))
                $details_array[] = '1 Wild card Question';
        }
        if($total_question > 0)
        {
             $details_array[] = ($total_question > 1)? "$total_question questions": "$total_question question";
        }
        if($total_challenge_question > 0)
        {
             $details_array[] = ($total_challenge_question > 1)? "$total_challenge_question challenge questions": "$total_challenge_question chellenge question";
        }
        if($total_time_question >  0)
        {
            $details_array[] = ($total_time_question > 1)? "$total_time_question time questions": "$total_time_question time question";
        }
        if($total_games_question > 0)
        {
            $details_array[] = ($total_games_question > 1)? "$total_games_question game questions": "$total_games_question game question";

        }
         if($total_revision_question > 0)
        {
            $details_array[] =  ($total_revision_question > 1)? "$total_revision_question revision questions": "$total_revision_question revision question";

        }
         if($total_topic_revision_question > 0)
        {
            $details_array[] = ($total_topic_revision_question > 1)? "$total_topic_revision_question topic revision questions": "$total_topic_revision_question topic revision question";
        }

        if($total_diagnostic_question > 0)
        {
            $details_array[] = ($total_diagnostic_question > 1)? "$total_diagnostic_question topic diagnostic questions": "$total_diagnostic_question topic diagnostic question";
        }

        if(!empty($details_array))
        return implode(',', $details_array);
        else
        return '';

    }
    public function get_diagnostic_question($session_id)
    {
            $query= "SELECT COUNT(*) as count FROM $this->table_diagnostic_question_attempt WHERE sessionID = $session_id";
            $result = $this->db->query($query);
            $data = $result->row_array();
            return $data['count'];
    }
    public function get_wild_card_question_session_array($userID)
    {
      $wild_card_array = array();
      $sq =   "SELECT sessionID FROM  $this->table_research
             WHERE userID=".$userID." AND questionType IN ('normal','research') ORDER BY lastModified desc limit 30";
      $result = $this->db->query($sq);
      if($result->num_rows() > 0)
      {
            $data = $result->result_array();
            foreach ($data as  $row) {
                
                $wild_card_array[] = $row['sessionID'];
            }
      }
      return $wild_card_array;
    }
    

}

/* End of file user_model.php */
