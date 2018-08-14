<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Parent_model extends CI_Model {
	
    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');
    }
   /**
	@EI:function will return the parent details required on the basis of student user id
    Now we are taking data from the user data model.

   **/
    public function get_parent_details($userID)
    {
        $information_required = array('parent_name','parent_email','contact_no_res','contact_no_cell');
        $parent_details_arr = array();
        $data_user = $this->user_model->get_ms_user_details($userID,$information_required);
        if($data_user)
        {
                extract($data_user);
                $explode_details_name = explode(',', $parent_name);
                $explode_details_email = explode(',', $parent_email);
                if(isset($explode_details_name[0]))
                {
                    $parent_details_arr['father_name'] = $explode_details_name[0];
                }
                else
                {
                    $parent_details_arr['father_name'] = '';
                }
                 if(isset($explode_details_name[1]))
                {
                    $parent_details_arr['mother_name'] = $explode_details_name[1];
                }
                else
                {
                    $parent_details_arr['mother_name'] = '';
                }
                 if(isset($explode_details_email[0]))
                {
                    $parent_details_arr['father_email'] = $explode_details_email[0];
                }
                else
                {
                    $parent_details_arr['father_email'] = '';
                }
                 if(isset($explode_details_email[1]))
                {
                    $parent_details_arr['mother_email'] = $explode_details_email[1];
                }
                else
                {
                    $parent_details_arr['mother_email'] = '';
                }
                $parent_details_arr['phone_number_cell'] = $contact_no_cell;
                $parent_details_arr['phone_number_res'] = $contact_no_res;

        }
        return $parent_details_arr;

    }
}