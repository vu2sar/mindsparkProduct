<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Auth_model extends CI_Model {
	private $db_educat;
    public  $table_common_user_details = '';
    public function __construct() {
        parent::__construct();
     $this->table_common_user_details = 'common_user_details';
     $this->db_educat = $this->load->database('database_educat',TRUE);
     $this->load->model('user_model');    
     $this->load->model('parent_model');
     $this->load->model('dashboard_model');
     $this->load->model('session_model');        
    }
   /**
	@EI: auth_check will check the credentials for api and return respected details after login
   **/
    public function auth_check_api($username, $password,$device_token,$device_unique_id,$device_type,$lat=0,$long=0)
    {
            $return_array = array();
            $student_data = array();
            // take student as category as for now our api will be applicable for student only;
            $check_user_credential = $this->check_credentials($username,$password,'STUDENT','MS_userID');
          
            if($check_user_credential)
            {
              
                $information_required = array('user_id','username','child_name','profile_picture','school_name','child_email','child_class','child_dob','city','start_date','end_date','school_code','child_section','child_gender');
                $student_data = $this->user_model->get_ms_user_details($check_user_credential['MS_userID'],$information_required);
                $return_array['remaining_time_of_the_day'] = $this->user_model->remaining_time_of_the_day($check_user_credential['MS_userID']);
                $return_array['last_session_available'] = $this->user_model->last_session_avaliable($check_user_credential['MS_userID']);
                $return_array['comment_message_count'] = $this->user_model->comment_message_count($check_user_credential['MS_userID']);
                $return_array['notice_board_message_count'] = $this->user_model->notice_board_message_count($check_user_credential['MS_userID'],$student_data['school_code'],$student_data['child_class'],$student_data['child_section']);
                $return_array['sparkie_count'] = $this->user_model->get_sparkies_count($check_user_credential['MS_userID']);
                $return_array['topic_details'] = $this->dashboard_model->get_topics_by_user($check_user_credential['MS_userID']);
                if(!$student_data)
                $return_array['student_details'] = array();
                else
                {
                    $return_array['student_details'] = $student_data;
                   
                }
                $parent_details =   $this->parent_model->get_parent_details($check_user_credential['MS_userID']);
                if(!$parent_details)
                $return_array['parent_details'] = array();
                else
                {
                    $return_array['parent_details'] = $parent_details;
                   
                }


            }
            else
            {
               return 0;
                
            }
            return $return_array;
    }
/**
	@EI: Check User Detail in common user detail function and return the array of user details	

**/
    public function check_credentials($username,$password,$check_with_category='',$select_information = "")
    {
    	$add_where = "";
    	if($check_with_category != "" )
    	{
    		$add_where = "category in ('$check_with_category') and";	
    	}
    	$q=$this->db_educat->query("select $select_information from $this->table_common_user_details where  $add_where  username = '$username' and (IF(category='STUDENT',IF(class>2, password=password('".$password."'), 1=1),password=password('".$password."'))) ");
    	if($q->num_rows() > 0)
    	{

    			return $q->row_array();
    	}
    	else
    	{
    			return 0;
    	}
    }
 /**
	@EI: This function will validate username 
 **/
 public function validate_username($username)
    {
    	
    	$q=$this->db_educat->get_where($this->table_common_user_details,array('username' => $username));
    	if($q->num_rows() > 0)
    	{

    			return 1;
    	}
    	else
    	{
    			return 0;
    	}
    }
/**
    @EI: Check user  logged in from other device 
**/
public function check_user_logged_in($userID)
    {
       $query = $this->db->get_where('api_accesstoken',array('userID' => $userID));
       if($query->num_rows() > 0)
       {
            return 1;
       }
       else
       {
            $time_allowed = $this->session_model->calculate_time_allowed($userID);
            $check = $this->session_model->check_duplicate_login($time_allowed,$userID);
            if($check == -1)
            {
                return 0;
            }
            else
            {
                 return 1;
            }
            
           
       }
    }

}

/* End of file auth_model.php */
