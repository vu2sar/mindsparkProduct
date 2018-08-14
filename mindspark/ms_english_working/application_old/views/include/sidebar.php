<aside>
    <div id="sidebar"  class="nav-collapse ">
        <!-- sidebar menu start-->
        <ul class="sidebar-menu" id="nav-accordion">
            
            <?php   if(count($menu_array) > 0)  {
                foreach($menu_array as $key => $value) {
                    if(is_array($value)) { 

                        if(isset($value['icon']))
                        {
                            $icon = $value['icon'];
                            unset($value['icon']);
                        }
                        else
                            $icon = 'fa-folder';
                        ?>
                        <li class="sub-menu dcjq-parent-li">
                            <a href="#" class="dcjq-parent">
                                <i class="fa <?=$icon;?>"></i>
                                <span><?=$key;?></span>
                                <span class="dcjq-icon"></span>
                            </a>
                            <ul style="display: block;" class="sub">
                                <?php   foreach($value as $k => $v) {   ?>
                                    <li><a href="<?=base_url($v);?>"><span><?=$k;?></span></a></li>
                                <?php   }   ?>
                            </ul>
                        </li>
                <?php } else {
                        $temp = explode('~', $value);
                        $temp[0] = str_replace(base_url(), '', $temp[0]);
                        $href = explode('/', $temp[0]);
                        $activeflag = false;
                        if(count($href) == 1 && strtolower($this->uri->segment(1)) == $href[0])
                            $activeflag = true;
                        else if(count($href) == 2 && strtolower($this->uri->segment(1)) == $href[0] && strtolower($this->uri->segment(2)) == $href[1])
                            $activeflag = true;     
                        ?>
                        <li>
                            <a href="<?=base_url($temp[0]);?>" 
                               <?php if($activeflag) { ?> class="active" <?php } ?> >
                                <?php   if(isset($temp[1])) { ?>
                                    <i class="fa <?=$temp[1];?>"></i>
                                <?php   }   ?>
                                <span><?=$key;?></span>
                            </a>
                        </li>
                <?php   }
                }
            }   ?>
           
                        </li>
        </ul>
        <!-- sidebar menu end-->
    </div>
</aside>
<script src="<?php echo base_url(); ?>theme/admin/js/bootstrap-switch.js"></script>