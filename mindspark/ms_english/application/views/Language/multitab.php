<!doctype html>
<html lang="en" >
    <head>
        <base href="<?php echo base_url(); ?>">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1 , user-scalable=no">
        <title>Mindspark English</title>
        <link type="text/css" href="theme/css/bootstrap.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="theme/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="theme/css/Language/appTheme.css">
    </head>
    <body>
        <div class="english-content-wrapper">
            <nav class="navbar">
                <div class="container-fluid" id="headerContainer">
                    <div id="branding" class="navbar-header">
                        <span class="branding"> <img src="theme/img/Language/logo-02.png"> </span>
                    </div>
                </div>
            </nav>
            <div class="container-fluid message-box row text-center custom-page">
                <div id="multiple-tab">
		            <div class="orientation-message">
		                <div><font color="red"><b>Note</b></font> : Opening Mindspark in multiple tabs is <font color="red"><b>NOT</b></font> allowed.</div>
		                <div>Click <font color="red"><button id="logout_all" type="button" class="btn btn-danger">here</button></font> to logout of all other Mindspark sessions and login again.</div>
		            </div>
		        </div>
            </div>
        </div>
    </body>
    <script type="text/javascript" src="theme/js/jquery-1.11.1.min.js" ></script>
    <script type="text/javascript" src="theme/js/jquery-ui.js"  ></script>
    <script>
        Helpers = {
            constants : {
                CONTROLLER_PATH : '/mindsparkProduct/msengFinalVersion/mindspark/ms_english/Language/',
                THEME_PATH : 'theme/',
                LOGIN_PATH : '../../../../mindsparkProduct/msengFinalVersion/mindspark/login/',
                REARCH_LOGIN_PATH:'https://mindspark.in/Mindspark/Login/',
            },
            disableHistoryNavigation: function() {
                if(!history.pushState){
                    return;
                }
                history.pushState(null, null, '/mindsparkProduct/msengFinalVersion/mindspark/ms_english/Language/session/multitab');
                window.addEventListener('popstate', function(event) {
                    history.pushState(null, null, '/mindsparkProduct/msengFinalVersion/mindspark/ms_english/Language/session/multitab');
                });
            }
        };
        
        $(document).ready(function(){
            Helpers.disableHistoryNavigation();
            $("#logout_all").on('click',function(){
                var logoutTime = 'true';
                 jQuery.ajax({
                    type : "POST",
                    url : Helpers.constants.CONTROLLER_PATH + 'login/updateEndTime',
                    data:{'logoutTime' : logoutTime,'logoutReason': 6},
                    "async" : false,
                    success : function(data) {
                        redirect(data);
                    }
                });
            });
            
        });
        function redirect(response)
        {
            response = $.parseJSON(response);
            if(response.redirect == true)
                goToLogin();
        }
        function goToLogin()
        {
            window.location.assign(Helpers.constants.LOGIN_PATH);
        }
        window.onstorage = function(){
            jQuery.ajax({
                type : "POST",
                url : Helpers.constants.CONTROLLER_PATH + 'login/updateEndTime',
                "async" : false,
                success : function(data) {
                    redirect(data);
                }
            });
        };

    </script>
</html>
