<header class="header white-bg">
    <div class="sidebar-toggle-box">
        <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
    </div>
    <!--logo start-->
    <a href="<?php echo base_url(); ?>tax_summary/index" class="logo"><span> Presentation Demo</span></a>
    <!--logo end-->
    
    <?php   
//    if($this->session->userdata('role_id') == 4)
//        $this->load->view('student/notifications', $this->data);
//    else if($this->session->userdata('role_id') == 3)
//        $this->load->view('teacher/notifications', $this->data);
//    else if($this->session->userdata('role_id') == 2)
//        $this->load->view('school/notifications', $this->data);
    ?>
        
    <div class="top-nav ">
        <!--search & user info start-->
        <ul class="nav pull-right top-menu">
<!--            <li>
                <input type="text" class="form-control search" placeholder="Search">
            </li>-->
            <!-- user login dropdown start-->
            <!-- <li class="dropdown">
                <a href="<?php echo base_url("../main.php"); ?>" style="color: #fff; font-size: 15px;"> Back to Home</a>   
            </li> -->
            <!-- user login dropdown end -->
        </ul>
        <!--search & user info end-->
    </div>
</header>
<script src="<?php echo base_url(); ?>theme/admin/js/jquery.js"></script>
<script src="<?php echo base_url(); ?>theme/admin/js/jquery-1.8.3.min.js"></script>
<script src="<?php echo base_url(); ?>theme/admin/js/bootstrap.min.js"></script>