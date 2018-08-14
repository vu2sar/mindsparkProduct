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
                <div>
                    <h2>
                        You are already logged into Mindspark.
                    </h2>
                    <h5>
                        Another session is active for this account else where. Do you wish to continue and log out the other session?     
                    </h5>
                </div>
                <div>
                    <button id="new_session" type="button" class="btn btn-primary">Yes</button>
                    <button id="kill_current_session" type="button" class="btn btn-primary">No</button>
                </div>
            </div>
        </div>
    </body>
    <script type="text/javascript" src="theme/js/jquery-1.11.1.min.js" ></script>
    <script type="text/javascript" src="theme/js/jquery-ui.js"  ></script>
    <script type="text/javascript" src="../userInterface/libs/brwsniff.js?ver=9"></script>
    <script>
        Helpers = {
            constants : {
               CONTROLLER_PATH : '/techmCodeCommit/mindsparkProduct/mindspark/ms_english/Language/',
                THEME_PATH : 'theme/',
                LOGIN_PATH : '../../../../mindspark/login/',
                MULTITAB : '/techmCodeCommit/mindsparkProduct/mindspark/ms_english/Language/session/multitab',
            }
        };

       

        $(document).ready(function(){
            localStorage.removeItem('tab_info');
            localStorage.removeItem('questionNumber');
            sessionStorage.clear();

            var os = getOS();
            var osDetails =  os[0]+os[1];
            osDetails = osDetails.replace(/ /g,'');

            var br             = getBrowser();
            var browserName    = br[0].replace(/ /g,'');
            var browserVersion = br[1];
            
            $("#new_session").click(function(){
                 window.location.assign(Helpers.constants.CONTROLLER_PATH + 'login/logoutOtherSessions/'+osDetails+'/'+browserName+'/'+browserVersion);
            });
            $("#kill_current_session").click(function(){
                //window.location = Helpers.constants.LOGIN_PATH;
                window.location.assign(Helpers.constants.CONTROLLER_PATH + 'login/logoutCurrentSessions/'+osDetails+'/'+browserName+'/'+browserVersion);
            });
        });
    </script>
</html>