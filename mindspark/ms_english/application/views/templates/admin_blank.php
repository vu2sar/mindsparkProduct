<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="keyword" content="EI Tax Module">
        <link rel="shortcut icon" href="<?php echo base_url(); ?>theme/favicon.png">

        <title><?php echo $title ?></title>

        <!-- Bootstrap core CSS -->
        <link href="<?php echo base_url(); ?>theme/admin/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>theme/admin/css/bootstrap-reset.css" rel="stylesheet">
        <!--external css-->
        <link href="<?php echo base_url(); ?>theme/font-awesome/css/font-awesome.css" rel="stylesheet" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>theme/admin/css/owl.carousel.css" type="text/css">

        <!-- Custom styles for this template -->
        <link href="<?php echo base_url(); ?>theme/admin/css/style.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>theme/admin/css/style-responsive.css" rel="stylesheet" />

        <link href="<?php echo base_url(); ?>theme/css/jquery-ui.css" rel="stylesheet" />
        <script src="<?php echo base_url(); ?>theme/js/jquery-1.9.1.js"></script>
        <script src="<?php echo base_url(); ?>theme/admin/js/bootstrap.min.js"></script>
        <script src="<?php echo base_url(); ?>theme/js/jquery-ui.js"></script>

        <link href="<?php echo base_url(); ?>theme/css/common.css" rel="stylesheet">
        <script src="<?php echo base_url(); ?>theme/js/common.js"></script>


        <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
        <!--[if lt IE 9]>
          <script src="<?php echo base_url(); ?>theme/js/html5shiv.js"></script>
          <script src="<?php echo base_url(); ?>theme/js/respond.min.js"></script>
        <![endif]-->
        <?php echo $_scripts ?>
        <?php echo $_styles ?>
    </head>
    <body>
        <!-- Add your own header here -->   
        <?php print $content ?>
        <!-- Add your own footer here -->
        <div id='dialog-message' title='Message' style='display: none;'></div>
        
    </body>
</html>