<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require(APPPATH . '/libraries/REST_Controller.php');

class Admin extends REST_Controller {

    var $client;
    protected $methods = array(
        'index_get' => array('level' => 10, 'limit' => 1000000),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('admin_model');
    }
    public function login_post() {
        if (checkparams($this->post(), array('vEmail', 'vPassword', 'ePlatform', 'vDeviceToken','dLat','dLong')))
         {
            $postData = $this->post();
            extract($postData);
            if ($this->admin_model->checkEmailAvailable($vEmail)) {
                //When email is available then Login
                $iUserID = $this->admin_model->getUserDetails($this->post());
                if ($iUserID != '') {
                    $this->admin_model->updateUserLatLong($dLat,$dLong,$iUserID);
                    $macstatus = genratemac($iUserID);
                         
                    $data = array('iUserID' => $iUserID,'vProfilePic' => $profilepic ,'vEmail'=>$this->admin_model->getEmail($iUserID) ,'iVicinity' =>$this->admin_model->getVicinity($iUserID), 'vUserName'=>$this->admin_model->getName($iUserID),"accesstoken" => $macstatus);
                    $this->send_success($data);
                } else {
                    $this->send_fail('Please login using correct credentials.');
                }   
            } else {
                $this->send_fail('No data found.');
            }
        } else {
            $this->send_fail('Insufficient Data');
        }
    }

    public function register_post() {
        if (checkparams($this->post(), array('vEmail','vPassword','vFirstName','vLastName','eGender','dLat','dLong', 'ePlatform', 'vDeviceToken'))) {
            //mprd($this->post());
             
            $postData = $this->post();
            extract($postData);
            if (!$this->admin_model->checkEmailAvailable($vEmail)) {
                //no email available then simple register.
                $iUserID = $this->admin_model->addUser($postData);
                //mprd($iUserID);
                if ($iUserID) {
                    /*if (isset($_FILES['vProfilePic']) && $_FILES['vProfilePic']['tmp_name'] != '' && $_FILES['vProfilePic']['error'] == 0) {
                        $profile_image = $this->save($iUserID);
                    }*/
                    
                    $macstatus = genratemac($iUserID);
                    $data = array(
                        'iUserID' => $iUserID,
                        'vUserName'=>$vFirstName.' '.$vLastName,
                        'vEmail' => $vEmail,
                        'iVicinity' =>$this->admin_model->getVicinity($iUserID),
                        "accesstoken" => $macstatus
                    );
                    
                    $this->send_success($data);
                } else {
                    $this->send_fail('Problem adding data.');
                }
            } else {
                $this->send_fail('Email is already registred, please login.');
            }
        } else {
            $this->send_fail('Insufficient Data');
        }
    }

    function save($iUserID) {
        // mprd($_FILES);
        if (isset($_FILES['vProfilePic']) && $_FILES['vProfilePic']['name'] != '') {
            $upload_name = $_FILES['vProfilePic']['name'];
            $file_name = time() . "_" . random_string('alnum', 5);
            $targetpath = PROFILE_PIC_ROOT . $iUserID;
            //mprd($targetpath);
            if (!is_dir($targetpath)) {
                if (!mkdir($targetpath, 0777, TRUE)) {
                    exit('dir not created.');
                }
            }
            $config['upload_path'] = $targetpath;
            $config['file_name'] = $file_name;
            $config['allowed_types'] = '*';
            $config['max_size'] = 1024 * 6;
            $config['overwrite'] = false;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if ($this->upload->do_upload('vProfilePic')) {
                extract($this->upload->data());
                $mypath = $targetpath . '/' . $file_name;
                $this->make_thumb($mypath, $targetpath);
                //$this->make_main($mypath, $targetpath);
                $this->db->update('tbl_user', array('vProfilePic' => $file_name), array('iUserID' => $iUserID));
                return $file_name;
            } else {
                $this->send_fail($this->upload->display_errors());
            }
        }
    }

    public function make_thumb($mypath, $img_root_folder) {
        $source_path = $mypath;
        $list = list($width, $height) = getimagesize($mypath);
        $ratio = 200.00 / min($width, $height);
        $w = $width * $ratio;
        $h = $height * $ratio;
        $target_path = $img_root_folder . '/thumb/';
        if (!is_dir($target_path))
            mkdir($target_path, 0777, TRUE);
        $config_manip = array(
            'image_library' => 'gd2',
            'source_image' => $source_path,
            'new_image' => $target_path,
            'maintain_ratio' => TRUE,
            'create_thumb' => FALSE,
            //'thumb_marker' => '_thumb',
            'width' => $w,
            'height' => $h
        );
        $this->load->library('image_lib', $config_manip);
        $this->image_lib->clear();
        $this->image_lib->initialize($config_manip);
        if (!$this->image_lib->resize()) {
            
        }
    }

    function send_fail($msg) {
        $row = array("MESSAGE" => "$msg", "SUCCESS" => 0);
        $this->response($row, 200);
    }

    function send_success($data) {
        $row = array("DATA" => $data, "SUCCESS" => 1);
        $this->response($row, 200);
    }

}

/* End of file admin.php */
/* Location: .//C/Users/Rahul-Kumawat/AppData/Local/Temp/fz3temp-1/admin.php */