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

        <style type="text/css">
            .table-browser{
                margin-left: 20%;
                margin-top: 40px;
                width: 50%;
            }
            .table-header{
                margin-left: 18%;
                margin-top: 40px;
                width: 55%;
            }
            .message{
                color:red;
                /*background:red;*/
                width:70%;
                margin-left:12%;
                margin-top: 20px;
                text-align: center;
            }
            .login-btn {
                margin-left: 38%
            }
            .text-color{
                color: white;
            }
            .alignment{
                text-align: left;
            }
       </style>
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
                <div class="table-header">
                    <h3>
                        YOUR BROWSER IS NOT SUPPORTED. PLEASE UPGRADE YOUR BROWSER TO CONTINUE USING MINDSPARK ENGLISH.
                    </h3>
                </div>
                <!-- <div>
                    <button id="new_session" type="button" class="btn btn-primary">Yes</button>
                    <button id="kill_current_session" type="button" class="btn btn-primary">No</button>
                </div> -->
                <div id="head" class="table-browser"> 
                    <table class="table table-bordered text-color">
                        <thead>
                          <tr style="background-color:lightslategray;">
                            <th>Browser/Device</th>
                            <th>Version Supported</th>
                          </tr>
                        </thead>
                        <tbody class="alignment">
                          <tr>
                            <td>Mozilla Firefox</td>
                            <td>35 and above</td>
                          </tr>
                          <tr>
                            <td>Google Chrome</td>
                            <td>38 and above</td>
                          </tr>
                          <tr>
                            <td>Internet Explorer</td>
                            <td>10 and above</td>
                          </tr>
                          <tr>
                            <td>IOS</td>
                            <td>6 and above</td>
                          </tr>
                          <tr>
                            <td>Android</td>
                            <td>4 and above</td>
                          </tr>
                        </tbody>
                    </table>
                </div>
                <div>
                    <button id="loginToMSE" type="button" class="btn btn-primary">LOGIN TO MINDSPARK</button>
                </div> 
            </div>
        </div>
    </body>
    <script type="text/javascript" src="theme/js/jquery-1.11.1.min.js" ></script>
    <script type="text/javascript" src="theme/js/jquery-ui.js"  ></script>
    <script>
        Helpers = {
            constants : {
                CONTROLLER_PATH : '/techmCodeCommit/mindsparkProduct/mindspark/ms_english/Language/',
                THEME_PATH : 'theme/',
                LOGIN_PATH : '../../../../techmCodeCommit/mindsparkProduct/mindspark/login/',
                MULTITAB : '/techmCodeCommit/mindsparkProduct/mindspark/ms_english/Language/session/multitab',
                REARCH_LOGIN_PATH:'https://mindspark.in/Mindspark/Login/',
            }
        };

       

        $(document).ready(function(){
            logOut();
            localStorage.removeItem('tab_info');
            sessionStorage.removeItem('questionNumber');
            sessionStorage.clear();

            
            $("#loginToMSE").on('click',function(){
                window.location.assign(Helpers.constants.LOGIN_PATH);
            });
        });
        function logOut() {
            sessionStorage.removeItem('questionNumber');
            tryingToUnloadPage = true;
            localStorage.clear();
            logTime();
        }
        function logTime() {
                //updateTimeTakenInClassRoom();
                $.ajax({
                    type : "POST",
                    url : Helpers.constants.CONTROLLER_PATH + 'login/updateEndTime',
                    async : true,
                });
        }
    </script>
</html>