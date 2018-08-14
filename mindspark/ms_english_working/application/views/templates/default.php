<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keyword" content="EI Tax Module">
    <link rel="shortcut icon" href="<?php echo base_url(); ?>theme/favicon.png">
    <title><?php echo $title?></title>
    <!-- Bootstrap core CSS -->
    <link href="<?php echo base_url(); ?>theme/admin/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>theme/admin/css/bootstrap-reset.css" rel="stylesheet">
    <!--external css-->
    <link href="<?php echo base_url(); ?>theme/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <!-- <link href="<?php echo base_url(); ?>theme/admin/jquery-easy-pie-chart/jquery.easy-pie-chart.css" rel="stylesheet" type="text/css" media="screen"/> -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>theme/admin/css/owl.carousel.css" type="text/css">
    <!-- Custom styles for this template -->
    <link href="<?php echo base_url(); ?>theme/admin/css/style.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>theme/admin/css/custom.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>theme/admin/css/style-responsive.css" rel="stylesheet" />
    
    <link href="<?php echo base_url(); ?>theme/css/jquery-ui.css" rel="stylesheet" />
    <script src="<?php echo base_url(); ?>theme/js/jquery-1.9.1.js"></script>
    <script src="<?php echo base_url(); ?>theme/js/jquery-ui.js"></script>
    
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>theme/admin/bootstrap-datepicker/css/datepicker.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>theme/admin/bootstrap-colorpicker/css/colorpicker.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>theme/admin/bootstrap-daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>theme/admin/jquery-multi-select/css/multi-select.css" />
    
    <script src="<?php echo base_url(); ?>theme/admin/js/count.js"></script>
    
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
    <!--[if lt IE 9]>
      <script src="<?php echo base_url(); ?>theme/js/html5shiv.js"></script>
      <script src="<?php echo base_url(); ?>theme/js/respond.min.js"></script>
      <![endif]-->
      <?php echo $_scripts ?>
      <?php echo $_styles ?>
  </head>
  <body>
    <section id="container" >
        <!-- Add your own header here --> 
        <?php print $header; ?>
        <?php print $sidebar; ?>
        <section id="main-content">
            <section class="wrapper ">
             <section>
                 
                        </div>
                        <div class="col-lg-5">
                        </div>
                        
                        <div class="col-lg-4 pull-right">
                        
                  
                    </div>
                </div>
                <div class="space10"></div>  
            </section>
        </div>
    </div>
    
</section>
<?php print $content; ?>
</section>
</section>
<?php print $footer; ?>
<!-- Add your own footer here -->  
</section>
<!-- Modal -->
<div class="modal fade" id="popupcontainerdiv" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id='alert_title'>Modal Title</h4>
            </div>
            <div class="modal-body">
                <div id='popup_content'></div>
            </div>
        </div>
    </div>
</div>
<!-- modal -->
<?php   /*  common alert popups   */  ?>
<style>
    #alertmsgdiv .modal-dialog { width: 300px; }
    #alertmsgdiv .modal-content { border: 1px solid #CCC; text-align: center; }
    #alertmsgdiv .modal-footer { text-align: center; margin-top: 0px; padding: 10px; }
</style>
<a data-toggle="modal" href="#alertmsgdiv" id='showalert'></a>
<!-- Modal -->
<div class="modal fade" id="alertmsgdiv" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id='alert_title'>Modal Title</h4>
            </div>
            <div class="modal-body">
                <div id='alert_msg'></div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">OK</button>
            </div>
        </div>
    </div>
</div>
<!-- modal -->
<?php   /*  end of common alert popups   */  ?>
</body>
<script type="text/javascript">
   var  url= '<?=base_url()?>';
   var urln = url+'tax_module/tax_summary/';
   $( "#userselect" ).change(function() {
    var postData = $("#userselectform").serializeArray();
    var formURL = urln;
    $.ajax(
    {
     url : formURL,
     type: "POST",
     data : postData,
     success:function(data, textStatus, jqXHR) 
     {
               //data: return data from server
           },
           error: function(jqXHR, textStatus, errorThrown) 
           {
               //if fails      
           }
       });
    $("#userselectform").submit();
});
   $(function(){
       $('#taxDateInput').datepicker({
        format: 'dd-mm-yyyy'
    });
   });
   $('#taxDateInput').blur(function(){
   });
</script>
<script type="text/javascript">
      $( document ).ready(function() {
          $('#userselect').css('max-width','200px');
      });
    </script>
</html>