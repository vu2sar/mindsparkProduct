<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');
require(APPPATH . '/libraries/REST_Controller.php');

class Dashboard extends REST_Controller {

  protected $methods = array(
    'get_dashboard_content_post' => array('level' => 10, 'limit' => CONTROLLER_CALL_LIMIT),
  );

  function __construct() {
    parent::__construct();
    $this->load->model('dashboard_model');
    if(!check_access_token()) {
      $array = $this->common_model->set_response_array(0,'auth001',"Unauthorized Access");
      $this->response($array, 401);
    }
  }

  function get_dashboard_content_post() {
    $user_id = get_userID();
    $dashboard_content = $this->dashboard_model->get_user_topic_details($user_id);

    $array = $this->common_model->set_response_array($dashboard_content['eiSuccess'],$dashboard_content['eiCode'],$dashboard_content['eiMsg'],$dashboard_content['data']);
    $this->response($array, 200);
  }

}