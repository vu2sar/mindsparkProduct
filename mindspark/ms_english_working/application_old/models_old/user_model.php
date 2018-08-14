<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User_model extends CI_Model {

    public $db_educat;
    private  $table_userdetails = '';
    private  $table_common_user_details = '';
    private  $table_adepts_user_comments = '';
    private $table_adepts_rewardPoints = '' ;
    private $table_adepts_sessionStatus = '';
    private $table_accesstoken = '';
    private  $subject_code = 0;
    
    public function __construct() {
        parent::__construct();
        $this->db_educat = $this->load->database('database_educat',TRUE);
        $this->table_userdetails = 'adepts_userDetails';
        $this->table_common_user_details = 'common_user_details';
        $this->table_adepts_user_comments = 'adepts_userComments';
        $this->table_notice_board_comments = 'adepts_noticeBoardComments';
        $this->subject_code = SUBJECTNO; 
        $this->table_adepts_rewardPoints = 'adepts_rewardPoints';
        $this->table_adepts_sessionStatus = 'adepts_sessionStatus';
        $this->table_accesstoken = TBL_ACCESSTOKEN;
      
    }
   /**
	@EI: get_ms_user_details will give the user details of all user in mindspark adepts database
    In information_required you have to pass the columns you want to fetch  for particular userID

   **/
    public function get_ms_user_details($userID,$information_required=array())
    {
        $field = $this->get_user_fields($information_required);
        if($field)
        {
            $information_required = $field;
        }
        else
        {
            return 0;
        }
        $q=$this->db->query("select $information_required from $this->table_userdetails where userID = '$userID'");
        if($q->num_rows() > 0)
        {
            return $q->row_array();
        }
        else
        {
            return 0;
        }
    }

    public function logout($userID)
    {
                                 $this->db->select('accesstoken');
        $query_get_accesstoken = $this->db->get_where($this->table_accesstoken,array('userID' => $userID));
        if($query_get_accesstoken->num_rows() > 0)
        {
            $accesstoken = $query_get_accesstoken->row_array();
            $accesstoken = $accesstoken['accesstoken'];
            $this->session_model->session_logout($accesstoken);
            $query = $this->db->delete( $this->table_accesstoken,array('userID' => $userID));
        }
        
    }

/**
 * This is to decouple the column names from the field names 
 **/
    public function get_user_fields($fields) {
        $information_required = "";
        $array_maped_result = array();
        if(!empty($fields)) {
            for ($i=0; $i<count($fields); $i++) { 
                if($this->get_column_name_by_field_name($fields[$i])) {
                    $array_maped_result[] = $this->get_column_name_by_field_name($fields[$i]);             
                }
            }

            if(!empty($array_maped_result)) {
                
                return implode(',', $array_maped_result);               
            }            
        }
        return 0;
    }
/**
Function to change password
**/
public function change_password($userID,$password)
{
    $query = $this->db->query("update $this->table_userdetails set password  = password('$password') where userID = $userID");
    $query = $this->db_educat->query("update $this->table_common_user_details set password  = password('$password') where MS_userID = $userID");
}
   
/**
    @EI: This function will check that user has subscribtion till date  or not
**/
public function is_subscribed_student($userID)
{
    $information_required = array('end_date','start_date','enabled','subcategory');
    $todays_date = time();
    $return_array = array();
    $get_user_data = $this->get_ms_user_details($userID,$information_required);
    if($get_user_data)
    {
        extract($get_user_data);
        $student_type = $this->get_student_type($userID);
        if(!$this->is_user_blocked($userID))
        {
            if(strcasecmp($student_type, 'RETAIL')==0)
            {
                if($this->check_subscription_expire_retail($end_date,$todays_date))
                {
                    $return_array['status'] = 2;
                    $return_array['message'] = 'Your subscription has been expired';
                }
                else
                {
                    $return_array['status'] = 1;
                    $return_array['message'] = 'subscription is active';
                }
            }
            else
            {
                if($this->check_subscription_expire_for_school($enabled,$end_date,$todays_date,$subcategory))
                {
                    $return_array['status'] = 2;
                    $return_array['message'] = 'Your subscription has been expired';
                }
                else
                {
                    $return_array['status'] = 1;
                    $return_array['message'] = 'Your subscription is active';
                }

            }

        }
        else
        {
            $return_array['status'] = 3;
            $return_array['message'] = 'Your account is temporarily deactivated.<br/>Please contact your ' . (strcasecmp($student_type, 'RETAIL')==0)? 'Mindspark customer care': 'School/Mindspark customer care' . 'for more information';
            }

        return $return_array;
    }
    else
    {
        return 0;
    }
}
public function update_user_information($post_data,$userID)
{
    extract($post_data);
    $update_array = array('childDob' => $dob,
                          'gender' => $gender,
                          'childEmail' => $email,
                          'city' => $city,
                          'parentName' => $father_name.','.$mother_name,
                          'parentEmail' => $father_email.','.$mother_email,
                          'secondaryParentEmail' => $mother_email,
                          'contactno_res' => $residence_phone,
                          'contactno_cel' => $father_mobile.','.$mother_mobile
                          );
    $this->db->update($this->table_userdetails, $update_array,array('userID'=>$userID));
    return 1;
}
public function check_subscription_expire_retail($end_date,$todays_date)
{
   
    if($end_date!='' && strtotime($end_date) < $todays_date)
        {   
               
            return 1;
        }
        else
        {
            return 0;
        }
}
public function check_subscription_expire_for_school($enabled,$end_date,$todays_date,$sub_category)
{
    $subscription_status = 0;
    if(strcasecmp($sub_category, "School") == 0) {
        if($enabled == 3) {
            $subscription_status = 1;
        } elseif(($enabled == 0) && ($end_date != "") && (strtotime($end_date) > $todays_date)) {
            $subscription_status = 1;
        }
    }

    return $subscription_status;
}

function is_user_blocked($userID)
{      
        $this->db_educat = $this->load->database('database_educat',TRUE);
        $query=$this->db_educat->query("select * from $this->table_common_user_details where MS_userID = $userID");

        if($query->num_rows() > 0)
        {
            $d= $query->row_array();
            return $d['is_block'];
        }
        else
        {
            return 0;
        }
}

    public function is_offline_student($userID)
    {
           // to do 
    }
    public function remaining_time_of_the_day($userID)
    {
           // to do  
           // this is temporary   
           return '30';       
    }
    public function last_session_avaliable($userID)
    {
           // to do 
          // have to change
            return '0';
    }
    public function comment_message_count($userID)
    {
        $this->db->select('count(srno)');
        $query = $this->db->get_where($this->table_adepts_user_comments,array('userID' => $userID,'viewed' => '0','status' => 'Closed'));
        if($query->num_rows() > 0)
        {
            $data_row = $query->row_array();
            return $data_row['count(srno)']; 
        }
        else
        {
            return 0;
        }
    }

    public function notice_board_message_count($userID,$school_code,$child_class,$child_section)
    {
        $query = "SELECT count(srno) as count_message_notice_board FROM $this->table_notice_board_comments WHERE subjectno = $this->subject_code AND schoolCode=$school_code AND (class='$child_class' OR class='All')";
        if($child_section!="")
            $query .= " AND (section='$child_class' OR section='All')";
        $query .= " AND datediff(curdate(),date) < noOfDays ORDER BY srno";
        
        $result = $this->db->query($query);
        if($result->num_rows() > 0)
        {
            $data = $result->row_array();
            return $data['count_message_notice_board'];
        }
        else
        {
            return 0;
        }
    }
    public function get_sparkies_count($userID)
    {
        $sparkie_prev = $this->get_sparkie_prev($userID);
        $sparkie_today = $this->get_todays_sparkie($userID);
        return $sparkie_prev + $sparkie_today;
    }
    public function get_sparkie_prev($userID)
    {
            $sq =   "SELECT sparkies FROM $this->table_adepts_rewardPoints WHERE userID=$userID";
            $rs =   $this->db->query($sq);
            if($rs->num_rows() > 0)
            {
                return $rs->row()->sparkies;
            }
            else
            {
                return 0;
            }
    }
    public function get_todays_sparkie($userID='')
    {
        
        $sq =   "SELECT SUM(noOfJumps) as sparkie FROM $this->table_adepts_sessionStatus WHERE userID= '$userID' AND startTime_int=".date("Ymd");
        $rs =   $this->db->query($sq);
        if($rs->num_rows() > 0)
        {
            return $rs->row()->sparkie;
        }
        else
        {   
            return 0;
        }
        
    
    }

//Added by Jayanth: begin

    public function get_child_class($userID) {
        $this->get_ms_user_details($userID, array('child_class'));
    }

    /**
     * @param $userID
     * @return string 
     * Returns either "SCHOOL", "GUEST", "RETAIL" depending on the type of the student. Returns "NA" if user is not student
    **/
    public function get_student_type($userID) {
        $studentType = "";
        $studentTypeArr = $this->get_ms_user_details($userID, array('category','subcategory')); 
        if(strcasecmp($studentTypeArr['category'], "STUDENT") == 0) {
            if(strcasecmp($studentTypeArr['subcategory'], "School") == 0) {
                $studentType = "SCHOOL";

            } elseif (strcasecmp($studentTypeArr['subcategory'], "Individual") == 0) {
                $studentType = "RETAIL";

            }
              elseif (strcasecmp($studentTypeArr['subcategory'], "Home Center") == 0) {
                $studentType = "HOME_CENTER";

            } else {
                $studentType = "NA";
            }

        } elseif($studentTypeArr['category'] == "GUEST") {
            $studentType = "GUEST";
        } else {
            $studentType = "NA";
        }

        return $studentType;
    }

public function get_package_type($userID)
{
    $information_required = array('comment');
    $user_data = $this->get_ms_user_details($userID,$information_required);
    if($user_data)
    {
        if($user_data['comment'] == 'MS_DEC')
            return 'MS_DEC';
        else
            return 'All';
    }
    else
    {
            return 'All';
    }
}
//Added by Jayanth: end

 private function get_column_name_by_field_name($column_name) {

        $array_mapping_field_column_name = array('user_id' => 'userID as user_id',
                                                'user_name' => 'username as user_name' ,
                                                'password' => 'password',
                                                'secret_question' => 'secretQues as secret_question',
                                                'secret_answer' => 'secretAns as secret_answer',
                                                'buddy_id' => 'buddyID as buddy_id' ,
                                                'category' => 'category',
                                                'subcategory' => 'IFNULL(subcategory,"") as subcategory',
                                                'child_name' => 'childName as child_name',
                                                'child_class' => 'childClass as child_class' ,
                                                'child_gender' => 'IFNULL(gender,"") as child_gender',
                                                'child_upgrade_date' => 'classUpgradeDate as child_upgrade_date',
                                                'upgrade_month' => 'upgrade_month',
                                                'child_section' => 'childSection as child_section' ,
                                                'child_dob' => 'childDob as child_dob',
                                                'pan_number' => 'pan_number',
                                                'school_code' => 'schoolCode as school_code',
                                                'child_email' => 'IFNULL(childEmail,"") as child_email' ,
                                                'parent_name' => 'parentName as parent_name',
                                                'parent_email' => 'parentEmail as parent_email',
                                                'secondary_parent_email' => 'IFNULL(secondaryParentEmail,"") as secondary_parent_email',
                                                'city' => 'city',
                                                'state' => 'state' ,
                                                'country' => 'country',
                                                'pincode' => 'pincode',
                                                'contact_no_res' => 'IFNULL(contactno_res,"") as contact_no_res',
                                                'contact_no_cell' => 'IFNULL(contactno_cel,"") as contact_no_cell',
                                                'start_date' => 'startDate as start_date',
                                                'end_date' => 'endDate as end_date',
                                                'time_allowed_day' => 'timeAllowedPerDay as time_allowed_day',
                                                'school_name' => 'schoolName as school_name',
                                                'address' => 'address',
                                                'order_id' => 'orderID as order_id',
                                                'amount' => 'amount',
                                                'package' => 'package',
                                                'discount' => 'discount',
                                                'type' => 'type',
                                                'payment_mode' => 'paymentMode as payment_mode',
                                                'bank_name' => 'bankName as bank_name',
                                                'chequeno' => 'chequeno',
                                                'heard_from' => 'heardFrom as heard_from',
                                                'registration_date' => 'registrationDate as registration_date',
                                                'verified' => 'verified',
                                                'reference_code' => 'referenceCode as reference_code',
                                                'comment' => 'comment',
                                                'call_date' => 'callDate as call_date',
                                                'call_time' => 'callTime as call_time',
                                                'updated_by' => 'updated_by',
                                                'class_change_history' => 'classChangeHistory as class_change_history',
                                                'call_time' => 'callTime as call_time',
                                                'class_change_history' => 'classChangeHistory as class_change_history',
                                                'enabled' => 'enabled',
                                                'deactivation_history' => 'deactivationHistory as deactivation_history',
                                                'subjects' => 'subjects',
                                                'picture'  => 'picture',
                                                'theme' => 'theme',
                                                'board' => 'board',
                                                'home_school'=>'homeSchool as home_school',
                                                'profile_picture'=> 'IFNULL(profilePicture,"") as profile_picture',
                                                'last_modified' => 'lastModified as last_modified'
                                                );

        if(array_key_exists($column_name,$array_mapping_field_column_name))
        {
            return $array_mapping_field_column_name[$column_name];
        }
        else
        {
            return 0;
        }
    } 

}

/* End of file user_model.php */
