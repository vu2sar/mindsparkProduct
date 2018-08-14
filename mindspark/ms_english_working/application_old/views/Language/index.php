<?php
	if($this->session->userdata('logged_in') != 1)
    {
        ob_start();
        header("Location: ".$this->config->item('login_url'));
    }
	//$pendingEssays     = $this->session->userdata('totalEssayPendingCnt');
    $category          = $this->session->userdata('category');
    $cmntNotifications = $this->session->userdata('totalCmntNotificaCnt');
    if($user_theme == 'default')
    {
        $folder = 'Language';
        $common = '';
    }
    else if($user_theme == 'christmas')
    {
        $folder = 'Language/Christmas';
        $common = 'Christmas';
    }

?>


<!doctype html>
<html lang="en" ng-app="englishInterface">
    <head>
        <base href="<?php echo base_url(); ?>">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1 , user-scalable=no">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <title>Mindspark English</title>
        <link type="text/css" href="theme/css/Language/jplayer.blue.monday.min.css" rel="stylesheet" />
		
        <link type="text/css" href="theme/css/bootstrap.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="theme/css/Language/common/commonTemplate<?php echo $common ?>.css?07022017">
        <link rel="stylesheet" href="theme/font-awesome/css/font-awesome.min.css">

        <link rel="stylesheet" href="theme/css/bootstrap-tokenfield.css">
        <link rel="stylesheet" href="theme/css/tokenfield-typeahead.css">
        <link rel="stylesheet" href="theme/css/Language/<?php echo $user_theme ?>.theme.css?20170914">
        <link rel="stylesheet" href="theme/css/Language/teacherReports.css?ver=20170817">
        <!-- <link rel="stylesheet" href="theme/css/Language/appTheme-autosearchdic.css?ver9"> -->
        
        <link rel="stylesheet" href="theme/css/Language/jquery.jqplot.css">

        <!-- <link rel="stylesheet" href="theme/js/elrte-1.3/css/elrte.min.css" type="text/css" media="screen" charset="utf-8"> -->
        
		
    </head>
    <body ng-controller="mainControl" ng-cloak onbeforeunload="return onbeforeunload()">
        <canvas id="canvas"></canvas>
        <input type="hidden" id="user_theme" value="<?php echo $user_theme ?>">
        <input type="hidden" id="img_ref" value="<?php echo $folder ?>">
        <audio  id="allAudio_correct" class="correct_audio"><source src="" type="audio/mp3" /><source src="" type="audio/ogg"/></audio>
        <audio  id="allAudio_wrong" class="wrong_audio"><source src="" type="audio/mp3" /><source src="" type="audio/ogg"/></audio>
        
        <div id="hide_element" class="hide_element none"></div>
        <input id="category" type="hidden" value="<?= $category ?>"/>
        <input id="authorize" type="hidden" value="<?= $authorize ?>"/>
        <input id="myself" type="hidden" value=""/>
        <span class="notification animation" ng-show="showme">
            <button type="button" ng-click="showme=false"><i class="fa fa-times" aria-hidden="true"></i></button>
            <span>{{ message }}</span>
        </span>
        <button type="button" id="helper_autoplay" class="none"></button>
        <!-- header -->
        <div id="warning-orientation-message">
            <div class="magic-div">
            </div>
            <div class="orientation-message">
                Mindspark is best viewed and worked with in the landscape(horizontal) mode.<br>Please shift to landscape mode to proceed further.
            </div>
        </div>
        <div class="english-content-wrapper none">
            <nav class="navbar">
                <div class="container-fluid" id="headerContainer">
                    <div id="branding" class="navbar-header">
                        <button id="sidebarToggle" onclick="sidebarToggle()" class="merge"><span class="icon-dash"> </span><span class="icon-dash"> </span><span class="icon-dash"> </span></button>
                        <span class="branding"> <img src="theme/img/Language/logo-02.png"> </span>
                    </div>

                    <div id="userBin">
                        <div id="userGreet">
                            {{'Hi ' + sessionData.childName + '!'}}
                        </div>
                        <div id="profileImage">
                            <img src="{{sessionData.profileImage}}" class="profile-icon-img"/>
                        </div>
                        
                        <div id="mainTimer" style="display:none;" data-title="Here's how long you've been Mindspark-ing! (HH:MM)">
                            <span id="mainHour">-</span>:<span id="mainMin">-</span> <span id="mainSec" class="none">-</span>
                        </div>
                    </div>

                    <div id="userButtons" class="pull-right">
                         <button class="merge" id="goback" ng-click="goback();" style="display: none;" data-title="Go back to school selection."><span class="fa fa-arrow-circle-left" style="border-radius:50%"></span></button>
                        <button class="merge" id="PendingEssaysBtn" ng-click="showNotificationBox()"><span class="fa fa-bell" style="border-radius:50%"><span class="badge">{{ totalNotificationCount }}</span></span></button>
                        <?php if($category == 'STUDENT') {?>
                            <button class="merge" id="dictionaryButton" data-title="Dictionary" ><i class="fa fa-book" aria-hidden="true"></i></button>
                            <button class="merge" id="sparkieButton" data-title="{{ total_sparkies }} sparkie(s)"><i class="fa fa-lightbulb-o fa-2x" aria-hidden="true"></i></button>
                        <?php }?>
                      <!--  <button class="merge" id="commentListShow" data-title="list" ><i class="fa fa-book" aria-hidden="true"></i></button> -->
                        <!-- <button class="merge" id="myBtn"><i class="fa fa-book" aria-hidden="true"></i></button> -->
                        
					    <button class="merge" id="commentButton" data-title="Comment Box"  onclick="showCommentPanel()"><span class="glyphicon glyphicon-comment" ><span id="cmtNoti" class="badge cmntNoti"></span></span></button>
                        <!-- <button class="merge" id="helpButton"  onclick="showInstructions()"><span class="glyphicon glyphicon-question-sign"> </span></button> -->
                        <?php if($category == 'STUDENT') {?>
                            <button class="merge" id="helpButton" data-title="Help Tour" ><span class="glyphicon glyphicon-question-sign"> </span></button>
                        <?php }?>
                        <button class="merge" id="sessionReportButton" data-title="Session Report"  onclick="showSessionReport()"><span class="glyphicon glyphicon-report"> </span></button>
                        <button class="merge" id="logOutButton" onclick="beforelogOut()" data-title="Logout" ><span class="glyphicon glyphicon-log-out" > </span></button>
                        <!--testing-->
                           <!-- <button id="myBtn">Open Modal</button> -->
                           <!-- The Modal -->

                            <div id="myModal" class="modal">

                              <!-- Modal content -->
                              <!-- <div class="row modal-content moduleContainer"> -->
                                <!-- <div class="row modal-content" id="dialog-6" ng-controller="myDictionaryController as tab" ng-click="check($event)"> -->
                                <div class="row modal-content col-lg-8 col-md-10 col-sm-10 col-lg-offset-2 col-md-offset-1 col-sm-offset-1" id="dragg" ng-controller="myDictionaryController as tab" ng-click="check($event)">
                                    <!-- <span class="close">x</span> -->
                                    <!-- <div class="close-btn-dic text-right">
                                        
                                    </div> -->
                                    <!-- <div class="text-right cursor-close">
                                        
                                    </div> -->
                                    <!-- <button class="closee pull-right">x</button> -->
                                    <div ng-show="meaningSearchMyDict" class="quote-container">
                                        <div class="note">
                                            <div class="">
                                                <input type="button" ng-click="closeNote()" class="form-control btn btn-sm dic-btn-note" value="x">
                                            </div>
                                            <div class="row">
                                                <span ng-show="notInDic" class="text-highlight"><strong>This word is not in your dictionary.</strong></span>
                                            </div>
                                            </br>
                                            <div class="row">
                                                <div class="col-lg-3 col-md-4"><strong>Word :</strong></div>
                                                <div class="col-lg-9 col-md-8">{{ word }}</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-3 col-md-4"><strong>Type :</strong></div>
                                                <div class="col-lg-9 col-md-8">{{ type }}</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-3 col-md-4"><div class="row" style="padding-left: 15px"><strong>Definition :</strong></div></div>
                                                <div class="col-lg-9 col-md-8">{{ definition }}</div>
                                            </div>
                                            </br>
                                            <input type="button" ng-click="addToDic(searchValue)" ng-model="searchValue" ng-show="notInDic" class="btn btn-small dic-btn-color" value="Add to my dictionary"></input>
                                        </div>
                                    </div>
                                    <div class="dic_header">
                                        <input type="button" ng-repeat="alpha in alphabetes" ng-click="getAlphaValue(alpha)" id="alpha_{{$index}}" class="form-control btn btn-sm alphaBtns" value="{{ alpha }}"></input>

                                        <input type="button" onclick="closeModal()" class="form-control btn btn-sm dic-btn-cls" value="x"></input>
                                        <!-- <img src="theme/img/Language/Pencil.png"> -->
                                    </div>
                                    <div id="dictionay_book" class="col-md-12 col-sm-12 dictionary_content">
                                        <div class="row">
                                            <div class="form-inline" >
                                                <div id="my_dict" ng-show="tab.isSet(1)" class="col-md-12 form-inline">
                                                    <input type="hidden" id="save_limit" value="{{limit}}"></input>
                                                    <div class="row">
                                                        <div class="col-md-11">
                                                            <div class="row">
                                                                <div class="col-md-6 pages" style="border-right: 3px solid; " >
                                                                    <!-- <div class="col-md-12 ui-widget" style="margin-bottom: 11px;" id="testing"> -->
                                                                    <div class="col-md-12" style="margin-bottom: 11px;" id="autocompleteDiv">
                                                                      <!-- <input type="text" ng-keypress="trigerEnterKey($event, 1)" ng-keyup="getMeaning(searchValue)" ng-model="searchValue" id="search_in_dict" class="form-control" placeholder="Search for..." style="width: 75% !important;"> -->
                                                                        <input type="text" ng-keypress="trigerEnterKey($event, 1)" id="search_in_dict" class="form-control" placeholder="Search for..." style="width: 75% !important;">
                                                                        
                                                                            <!-- <input type="text" ng-keypress="trigerEnterKey($event, 1)" id="search_in_dict" class="form-control" placeholder="Search for..." style="width: 75% !important;"> -->
                                                                        <span>
                                                                            <button class="btn" style="margin: auto" ng-click="getReport()" type="button">Go!</button>
                                                                        </span>
                                                                    </div>
                                                                    <div>
                                                                        <table class="table table-responsive" style="text-align: left">
                                                                            <tbody>
                                                                                <tr ng-if="finalArray.length === 0 || finalArray.length === undefined">
                                                                                    <td colspan="2"> No data available </td>
                                                                                </tr>
                                                                                <tr ng-repeat="row in finalArray | filter:search_word as results">
                                                                                    <td style="width:4%"> {{row.sr_no}} </td>
                                                                                    <td>
                                                                                        <button class="btn-link-dic" type="button" ng-click="getData(row)">{{row.user_word}}</button>
                                                                                        <!-- <a ng-click="getData(row)" href="javascript:void(0)">{{row.user_word}}</a> -->
                                                                                    </td>
                                                                                </tr>

                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                    <button type="button" ng-show="showPreviousPage" ng-click="getPrevious(pageNo, alphabet)"  class="btn btn-default btn-lg navigate-left-button" aria-label="Left Align">
                                                                      <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                                                                    </button>
                                                                </div>
                                                                <div class="col-md-6 pages">
                                                                    <div class="col-md-12 selected-word text-right">
                                                                        <span>{{ alphabet }}</span>
                                                                    </div>
                                                                    <div>
                                                                        <table class="table" style="text-align: left">
                                                                            <tbody>
                                                                                <tr ng-repeat="row in finalArray1 | filter:search_word as results">
                                                                                    <td style="width:4%"> {{row.sr_no}} </td>
                                                                                    <td>
                                                                                        <!-- <a ng-click="getData(row)" href="javascript:void(0)">{{row.user_word}}</a> -->
                                                                                        <button class="btn-link-dic" type="button" ng-click="getData(row)">{{row.user_word}}</button>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                    <button type="button" ng-show="showNextPage" ng-click="getNext(pageNo, alphabet)" class="btn btn-default btn-lg navigate-right-button" aria-label="Right Align">
                                                                      <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1 text-center">
                                                            <img src="theme/img/Language/Pencil.png" class="img-responsive pencil"/>
                                                        </div>
                                                    </div>
                                                </div>
                                             </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                         <!--testing-->
                            <!--sessionreport-->
                            <div id="myModalSessionReport" class="modal">

                                <div class="session-btn-cls"><input type="button" onclick="closeModalSession()" class="form-control btn btn-sm session-input-btn-cls" value="X"></div>
                                <div class="row modal-content sessionReportContent" id="sessionReportQues" style="">
                                    
                                </div>
                            </div>

                            <!--sessionreport-->
                    </div>
                </div>
                <div ng-show="showNotification" class="col-md-4 col-lg-3 notification-box">
                    <ul>
                        <li ng-repeat="(key, value) in notifications">
                            <span  class="notification-block" ng-click="loadNotifications($event)">{{ key }}<span class="badge">{{ value }}</span></span>
                        </li>
                    </ul>
                </div>
            </nav>
            <!--  Header ends here. -->
            <!-- Create a rating div for rating plugin. -->
            <div class="col-md-6 col-lg-5 rating-feedback none">
                <button class="rating_cross cross"><i class="fa fa-times" aria-hidden="true"></i></button>
                <div class="rating-heading"></div>
                <div class="rating">
                    <!-- Sample Html to create a star for rating. -->
                    <!-- <i class="icon-star icon-3"></i> -->
                </div>
                <div id="custom-popover" class="col-md-10 col-lg-8 none">
                    <div class="arrow"></div>
                    <div id="popover"></div>
                </div>
            </div>
            <!-- End of rating plugin. -->
            <div id="contentContainer">

                <!--carousel For Tour-->
                <div id="myModalHelpTour" class="modal">
                  <!-- Modal content -->
                    <div class="row col-lg-8 col-md-10 col-lg-offset-2 col-md-offset-1">
                        <div id="" class="col-md-12 help_tour_content">
                            <div class="row">
                                <div class="form-inline" >
                                    <div id="" class="col-md-12 form-inline">
                                        <div class="row">
                                            <div class="col-md-11">
                                                <div class="container" id="tour_carousel" >
                                                    <div id="myCarousel" class="carousel slide" data-ride="carousel">
                                                      <div class="carousel-inner">
                                                        <div class="item active" id="0">
                                                          <img src="theme/img/Language/helpTour/help_daily_report.jpg">
                                                        </div>
                                                 
                                                        <div class="item" id="1">
                                                          <img src="theme/img/Language/helpTour/help_classroom.jpg">
                                                        </div>
                                                        
                                                        <div class="item" id="2">
                                                          <img src="theme/img/Language/helpTour/help_comment.jpg">
                                                        </div>
                                                        
                                                        <div class="item" id="3">
                                                          <img src="theme/img/Language/helpTour/help_essay.jpg">
                                                        </div>
                                                        <div class="item" id="4">
                                                          <img src="theme/img/Language/helpTour/help_grounds.jpg">
                                                        </div>
                                                        <div class="item" id="5">
                                                          <img src="theme/img/Language/helpTour/help_sparkie.jpg">
                                                        </div>
                                                        <div class="item" id="6">
                                                          <img src="theme/img/Language/helpTour/help_dictionary1.jpg">
                                                        </div>
                                                        <div class="item" id="7">
                                                          <img src="theme/img/Language/helpTour/help_dictionary2.jpg">
                                                        </div>
                                                      </div>
                                                        <ul class="nav nav-pills nav-justified li-align" id="help_ul">
                                                            <li data-target="#myCarousel" data-slide-to="0" id="li_0" class="help_li active"><a href="#">Daily Report<small></small></a></li>
                                                            <li data-target="#myCarousel" class="help_li" data-slide-to="1" id="li_1"><a href="#">Classroom<small></small></a></li>
                                                            <li data-target="#myCarousel" class="help_li" data-slide-to="2" id="li_2"><a href="#">Comment<small></small></a></li>
                                                            <li data-target="#myCarousel" class="help_li" data-slide-to="3" id="li_3"><a href="#">Essay Writer<small></small></a></li>
                                                            <li data-target="#myCarousel" class="help_li" data-slide-to="4" id="li_4"><a href="#">Grounds<small></small></a></li>
                                                            <li data-target="#myCarousel" class="help_li" data-slide-to="5" id="li_5"><a href="#">Sparkie<small></small></a></li>
                                                            <li data-target="#myCarousel" class="help_li" data-slide-to="6" id="li_6"><a href="#">Dictionary(1)<small></small></a></li>
                                                            <li data-target="#myCarousel" class="help_li" data-slide-to="7" id="li_7"><a href="#">Dictionary(2)<small></small></a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                 </div>
                            </div>
                        </div>
                        <div class="">
                            <input type="button" onclick="closeHelpModal()" class="form-control btn btn-sm help-btn-cls" value="x"></input>
                        </div>
                    </div>
                </div>
                <!--carousel end-->

            <!-- sidebar -->
             <div id="sidebar">
                <ul class="sidebar-nav">
                    <li ng-repeat="sidebarItem in sidebarItems" onclick="onSidebarItemClick(event)" id="sbi_{{sidebarItem.optionName.replace(' ','_') | lowercase}}">
                        <a ng-switch on="sidebarItem.interfaceType" menuName="{{sidebarItem.optionName}}">
                            <div ng-switch-when="TeacherInterface">
                                <span><i class="{{sidebarItem.icon}}"></i></span>{{sidebarItem.optionName}}
                            </div>
                            <div ng-switch-when="StudentInterface">
                                <span back-img="{{'theme/img/Language/sidebarIcon' + sidebarItem.icon + '.png'}}"> </span> {{sidebarItem.optionName}}
                                <span ng-show="sidebarItem.optionName=='Essay Writer'" ng-class="sidebarItem.optionName=='Essay Writer' ? 'v2-essayNotificationWrapper' :''"><span class="v2-essay-count" ng-show="essayAssignedToUser!=0">1 New</span></span>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
                <!-- Sparkie part is not show currently -->
            <div id="mainContentContainer" class="container-fluid">
                <div id="resetPassword" class="none resetPassword moduleContainer">
                    <h3>Reset Password Request</h3>
                    <div ng-show="noRequest">
                        <h4>No request to reset password.</h4>
                    </div>
                    <div ng-hide="noRequest" class="col-md-12">
                         <div class="table-ul-list tableContainer col-md-12 col-lg-offset-1 col-lg-10">
                             <h4 style="height:38px" class="row allotmentTableHeaders">
                                 <span align='center' style="width:5%">#</span>
                                 <span align='left' style="width:20%">UserName</span>
                                 <span align='left' style="width:20%">Child Name</span>
                                 <span align='center' style="width:8%">Class</span>
                                 <span align='center' style="width:8%">Section</span>
                                 <span align='center' style="width:18%">Requested On</span>
                                 <span align='center' style="width:10%">Action</span>
                             </h4>
                             <ul class="list-group">
                                <li ng-repeat="request in requestObject" ng-if="request.userName != null">
                                    <span align='center' style="width:5%">{{($index+1)}}</span>
                                    <span align='left' style="width:20%">{{request.userName}}</span>
                                    <span align='left' style="width:20%">{{request.childName}}</span>
                                    <span align='center' style="width:8%">{{request.childClass}}</span>
                                    <span align='center' style="width:8%">{{request.section}}</span>
                                    <span align='center' style="width:18%">{{request.requestDate}}</span>
                                    <span align='center' style="width:10%"  data-id="{{request.id}}"><button type="button" class="btn btn-primary small-button" ng-click="resetUserPassword($event)">Reset</button><button class="btn btn-primary small-button" ng-click="resetUserPassword($event)">No Action</button></span>
                                </li>
                             </ul>
                         </div>
                    </div>
                    
                </div>
                <div id="teacher_home" class="row none teacher_home moduleContainer">
                    <div  class="col-md-12 col-lg-12 none accordion">
                        <div class="container text-align-right">
                        <div class="row form-inline">
                            <div class="form-group">
                                <label>Grade: </label>
                                <select class="form-control vcenter" id="th_grade_select">
                                    <option value=''>select</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Section: </label>
                                <select class="form-control vcenter" id="th_section_select">
                                    <option value=''>select</option>
                                </select>
                              
                            </div>
                             <!-- Hiding it with default values -->
                            <div class="form-group display_none">
                                <label >Start Date: </label>
                                <input type="text"  class="ll-skin-nigran text-box" id="teacherHomeReportStartDate" readonly="true" >
                            </div>

                            <div class="form-group display_none">
                                <label >End Date: </label>
                                <input type="text"  class="ll-skin-nigran text-box" id="teacherHomeReportEndDate" readonly="true">
                               
                            </div>
                            <!-- end hiding -->
                            <div class="form-group">
                                <button type="button" id="th_go" class="btn btn-primary form-control" style="font-size:1.3em !important">GO</button>
                            </div>
                        </div>
                            </div>
                       <!-- V2 Dashboard start from here -->
                       <div class="row" id="home_message" style=""><br></div>
                        <div class="v2-usage-container container none">
            <div class="v2-usage-overview">Usage Overview : <span id="v2-usage-overview" class="v2-usage-overview"></span></div>

            <div class="v2-container"  data-toggle="modal" data-target="#v2-usage-overview-detail">
                <div class="row">
                <div class="col-md-4 col-sm-12 v2-usage-stats">
                <!-- canvas will appear here -->
                <div id="v2_usage_report" class="autoHeight"></div>
<!--               <canvas id="v2_usage_report" width="270" height="110"></canvas> -->
               <!-- canvas legends will show here -->
                          <div class="v2-chart_ledgends" id="v2-chart_ledgends">  

                          </div>
                </div>
                <div class="col-md-8 col-sm-12" >
                    <div class="col-md-4 col-sm-12 v2-usage v2-low autoHeight">
                        <div class="v2-bold">Low Usage</div>
                        <!-- Low usage data limit to 3 -->
                        <div id="v2-low-data-3" >
                        </div>
                        <div class="v2-usage-more" id="v2-low-data-more"></div>
                    </div>
                    <div class="col-md-4 col-sm-12 v2-usage v2-average autoHeight">
                        <div class="v2-bold">Average Usage</div>
                        <!-- Average usage data limit to 3 -->
                        <div id="v2-average-data-3">
                        </div>
                        <div class="v2-usage-more" id="v2-average-data-more"></div>
                    </div>
                    <div class="col-md-4 col-sm-12 v2-usage v2-high autoHeight">
                        <div class="v2-bold">High Usage</div>
                        <!-- high usage data limit to 3 -->
                        <div id="v2-high-data-3">
                        </div>
                        <div class="v2-usage-more"  id="v2-high-data-more"></div>
                    </div>
                </div>
                </div>
            </div>
            <!-- Modal box will apear on click on upper div -->
            <div class="modal fade" id="v2-usage-overview-detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <div class="v2-close-modal"  class="close" data-dismiss="modal" aria-label="Close">&times; Close</div>
                    <h4 class="modal-title" id="myModalLabel">Usage Overview : <span id="v2-usage-overview" class="v2-usage-overview"></span></h4>
                  </div>
                  <div class="modal-body">
                  <div class="container v2-modal-box-wrapper">
                    <div class="col-md-4 col-sm-12 v2-low-usage v2-usage-detail v2-low">
                        <div class="v2-low-usage-pie">
                        <canvas id="v2_usage_detail_report_low" width="100" height="100"></canvas></div>
                        <div class="v2-bold">Low Usage</div>
                        <!-- low usage data without limit -->
                        <div id="v2-low-data-n" >
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 v2-average-usage v2-usage-detail v2-average">
                        <div class="v2-average-usage-pie">
                            <canvas id="v2_usage_detail_report_average" width="100" height="100"></canvas>
                            </div>
                        <div class="v2-bold">Average Usage</div>
                        <!-- average usage data without limit -->
                         <div id="v2-average-data-n"></div>
                    </div>
                    <div class="col-md-4 col-sm-12 v2-good-usage v2-usage-detail v2-high">
                        <div class="v2-good-usage-pie"><canvas id="v2_usage_detail_report_good" width="100" height="100"></canvas></div>
                        <div class="v2-bold">Good Usage</div>
                        <!-- good usage data without limit -->
                        <div id="v2-high-data-n">
                        </div>
                    </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="v2-usage-overview">Skill Overview</div>
            <div class="v2-skill-overview">
          
                <div class="row row-eq-height">
                <div class="col-md-3  col-sm-12">
                <div class="v2-skill-overview-blocks">
                    <h2 class="v2-bold">Reading Comprehension</h2>
                    <div class="v2-skill-body">
                    <!-- skill items will appear here -->
                        <ul class="v2-skill-item" id="v2-rc-item">
                            
                        </ul>
                        <!-- donut in organge color -->
                        <div class="v2-skill-pie v2-donut-size" id="v2-rc-p1">
                            
                          <div class="pie-wrapper">
                            <span class="label">
                              <span class="num">0</span>
                          </span>
                          <div class="pie">
                              <div class="left-side half-circle"></div>
                              <div class="right-side half-circle"></div>
                          </div>
                          <div class="shadow"></div>
                      </div>
                  </div>   
                  <!-- view report link -->
                   <div class="v2-view-report" onclick="onSidebarItemClick(event,'custom')"><a menuname="Reports">VIEW REPORTS</a></div>             
               </div>
                </div>
            </div>
             <div class="col-md-3  col-sm-12">
              <div class="v2-skill-overview-blocks">
                    <h2 class="v2-bold">Listening Comprehension</h2>
                    <div class="v2-skill-body">
                        <!-- skill items will appear here -->
                        <ul class="v2-skill-item" id="v2-lc-item">
                           
                        </ul>
                        <!-- donut in blue color -->
                        <div class="v2-skill-pie v2-donut-size" id="v2-lc-p2"><div class="pie-wrapper">
                            <span class="label">
                              <span class="num">0</span>
                          </span>
                          <div class="pie">
                              <div class="left-side half-circle"></div>
                              <div class="right-side half-circle"></div>
                          </div>
                          <div class="shadow"></div>
                      </div>
                      </div>
                      <!-- View report link -->
                      <div class="v2-view-report" onclick="onSidebarItemClick(event,'custom')"><a menuname="Reports">VIEW REPORTS</a></div>
                       
                </div>
                </div>
            </div>
             <div class="col-md-3  col-sm-12">
              <div class="v2-skill-overview-blocks">
                    <h2 class="v2-bold">Grammar Concepts</h2>
                    <div class="v2-skill-body">
                    <!-- skill items will appear here -->
                        <ul class="v2-skill-item" id="v2-gc-item">
                           
                        </ul>
                         <!-- donut in green color -->
                        <div class="v2-skill-pie v2-donut-size" id="v2-gc-p3"><div class="pie-wrapper">
                            <span class="label">
                              <span class="num">0</span>
                          </span>
                          <div class="pie">
                              <div class="left-side half-circle"></div>
                              <div class="right-side half-circle"></div>
                          </div>
                          <div class="shadow"></div>
                      </div></div>
                      <!-- View report link -->
                      <div class="v2-view-report" onclick="onSidebarItemClick(event,'custom')"><a menuname="Reports">VIEW REPORTS</a></div>
                       </div> 
                
                </div>
            </div>
             <div class="col-md-3  col-sm-12">
             <div class="v2-skill-overview-blocks">
                    <h2 class="v2-bold">Vocabulary Concepts</h2>
                    <div class="v2-skill-body">
                     <!-- skill items will appear here -->
                        <ul class="v2-skill-item" id="v2-vc-item">
                            
                        </ul>
                         <!-- donut in green color -->
                        <div class="v2-skill-pie  v2-donut-size" id="v2-vc-p4"><div class="pie-wrapper">
                            <span class="label">
                              <span class="num">0</span>
                          </span>
                          <div class="pie">
                              <div class="left-side half-circle"></div>
                              <div class="right-side half-circle"></div>
                          </div>
                          <div class="shadow"></div>
                      </div>
                      </div>
                      <!-- view report link -->
                       <div class="v2-view-report" onclick="onSidebarItemClick(event,'custom')"><a menuname="Reports">VIEW REPORTS</a></div>
                      
               </div>
                </div>
            </div>
            
            </div>
            </div>
        </div>
        <!-- End v2 Dashboard -->

                    </div>
                </div>

                <!-- Teacher Reports Tab start here-->
                   
                <div id="teacher_reports_page" class="none teacher_home moduleContainer" ng-controller="reportController">
                
                    <div id="" class="col-md-12 col-lg-offset-1 col-lg-10  none table-responsive accordion">
                       
                   
<form name="detailReport" ng-submit="submitFormDetailReport()">
   <div class="row form-inline">
                            <div class="form-group">
                                <label>Grade: </label>
                                <select id="th_teacher_report_grade_select" name="childclass" class="form-control vcenter" ng-model="report.childclass">
                                    <option value=''>select</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Section: </label>
                                <select id="th_teacher_report_section_select" name="childsection" class="form-control vcenter" ng-model="report.childsection">
                                    <option value=''>select</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Start Date: </label>
                                <input type="text" id="teacherReportStartDate" name="start_date" readonly="true" class="ll-skin-nigran text-box" ng-model="report.startDate">
                            </div>
                            <div class="form-group">
                                <label>End Date: </label>
                                <input type="text" id="teacherReportEndDate" onkeydown="return DateFormat(this, event.keyCode)" readonly="true" class="ll-skin-nigran text-box" name="end_date" ng-model="report.endDate" >
                            </div>
                        
                            <div class="form-group">
                              <input type="hidden" name="report_mode" class="ll-skin-nigran text-box" value="0" ng-model="report.reportMode" ng-init="reportMode='0'">
                                <button style="font-size:1.3em !important" class="btn btn-primary" id="th_report_go-1" type="submit">GO</button>
                                <!-- <button style="font-size:1.3em !important; display: none" onclick="tablesToExcel(['low_accuracy_table','high_accuracy_table', 'not_logged_in_table', 'all_students_data_table'], ['Low Accuracy','High Accuracy', 'Not Logged In', 'All Students'], 'OverallSkill.xls', 'Excel')" class="btn btn-primary" id="download_excel_overall" type="button">Download Excel</button>
                                <button style="font-size:1.3em !important; display: none" onclick="tablesToExcel(['grammer_skill_details_table','all_skill_details_table'], ['Grammar Skill Details','All Skills'], 'SkillWise.xls', 'Excel')" class="btn btn-primary" id="download_excel_skillwise" type="button">Download Excel</button> -->
                            </div>
                            <input type="hidden" id="report_mode" class="ll-skin-nigran text-box" value="0">
                        </div>
   
                </form>
     
                    </div>
                    <div class="col-md-12 col-lg-offset-1 col-lg-10 v2-detail-report">
                        <div id="datatableNoData" class="datatableNoData" style="display: none;"></div>
                        <div class="datatable" ultimate-datatable="datatable" id="teacherReportDatatable">
                        </div>
                    </div>
                    <iframe style="display: none" id="txtArea1"></iframe>
                </div>
                <!-- Teacher Reports Tab Ends here-->
                <div ng-controller="essayEvaluation" id="essay_evaluation" class="row none essay_evaluation moduleContainer">
                    <div class="col-lg-8 col-lg-offset-2 col-md-offset-1 col-md-10 stretch-height text-left text-white x-hidden" id="evaluationDiv">
                        <!-- Heading for essay Evaluation -->
                        <div class="stretch-height-10 essayHeaders" style="font-size: 1.1em;">
                            <div class="row">
                                <div class="row col-md-10">
                                    <strong>
                                        <span class="col-md-1 col-sm-2">
                                            <label>Title:</label>
                                        </span>
                                        <span class="col-md-11 col-sm-10 einnerTitle">{{ essayDetails.essayTitle }}</span>
                                    </strong>
                                </div>
                                <div class="col-md-2 text-right">
                                    <strong><label>Words</label><span>:</span>{{ essayDetails.words }}</strong>
								</div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong><label>Author</label><span>:</span>{{ essayDetails.author }}</strong> 
                                </div>
                                <div class="col-md-5 text-center">
                                    <strong><label>Submitted</label><span>:</span>{{ essayDetails.submitted }}</strong>
                                </div>
                                <div class="col-md-3 text-right">
                                    <strong><label>Time Taken</label><span>:</span>{{ essayDetails.timeTaken }}</strong> 
                                </div>
                            </div>
                        </div>
                        <!-- Content Grid -->
                        <div class="row stretch-height-80">
                            <div class="col-md-6 stretch-height reset-padding">
                                <div class="stretch-height-90">
                                    <div id="essay_detail" readonly class="form-control"></div>
                                </div>
                                <div class="stretch-height-10 text-center addCmtBtnDiv">
                                    <button class="btn btn-primary small-button" data-ng-disabled="newcomment" ng-hide="essayMode" id="addCommentBtn">Add Comment</button>
                                </div>
                            </div>
                            
                            <div class="col-md-6 stretch-height">
                                <div class="row stretch-height-50">
                                    <div class="info-box">
                                        <div class="info-box-heading">
                                            Specific Feedback
                                        </div>
                                        <div class="info-box-content form-control" id="specificComment">
                                            <dl>
                                                <dt ng-repeat="commentObj in comments">
                                                    <evaluate-comment save="saveComment(indexKey,commentText)" view="switchView(value,indexKey)" index="$index"  toggle="togglecomment"  commenttext="commentObj.comment.commentText" commentmodel="commentObj.comment.commentText" commentindex="commentindexkey" newcomment="newcomment" essaymode="essayMode" commentlabel="commentObj.comment.commentRange"></evaluate-comment>
                                                </dt>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                                <div class="row stretch-height-50">
                                    <div class="info-box">
                                        <div class="info-box-heading">
                                            General Feedback
                                        </div>
                                        <div class="info-box-content">
                                            <!-- <textarea class="form-control" ng-model="general_comment" id="generalFeedback"></textarea> -->
                                           <div ng-model="general_comment" ng-click="getActiveElement('generalFeedback')" id="generalFeedback" contenteditable="true">
                                               <ul>
                                                   <li ng-repeat="generalComment in autoCommentArrGeneral">{{ generalComment }}</li>
                                               </ul>
                                           </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Footer for essay Evaluation -->
                        <div class="row stretch-height-10">
                            <div class="col-md-3" id="score_evaluation">
                                <strong><label>Score:</label></strong>
                                <input type="text" ng-model="essayDetails.essayscorevalue" id="essayScore" pattern="[0-9]*" size="4" /> on 10&nbsp;&nbsp;&nbsp; 
                            </div>
                             <div class="col-md-9">
                                <div class="row">
                                    <button class="btn btn-primary small-button pull-right" data-ng-disabled="disableSubmission" id="closeBtn" ng-click="closeFeedback()">Close</button>
                                    <button class="btn btn-primary small-button pull-right" ng-click="saveFeedback(submit)" data-ng-disabled="disableSubmission" ng-hide="essayMode">Submit Feedback</button>
                                    <button class="btn btn-primary small-button pull-right" ng-click="saveFeedback(save)" data-ng-disabled="disableSubmission" ng-hide="essayMode">Save Feedback</button>
                                </div>
                            </div>
                        </div>
                        <div class="rubric-heading-btn" id="rubricBtn" ng-click="showRubric();" >{{ rubric }}</div>
                        <div class="rubric col-md-6" ng-hide="essayMode" id="rubric" style="display:none" >
                            <!-- <div class="rubric-heading" ng-click="showRubric();" ng-hide="essayMode">{{ rubric }}</div> -->
                            <div class="rubric-body">
                                <!-- body for rubric -->
                                <div class="text-center">
                                    <span><h3>Rubric for evaluation</h3><span>(Place cursor over a cell for description)</span></span>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th>5</th>
                                                <th>4</th>
                                                <th>3</th>
                                                <th>2</th>
                                                <th>1</th>
                                                <th>N.A.</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr min="10">
                                                <td>Punctuation, Tense & Spelling</td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c1" value="5" id="r15" title="No spelling/punctuation errors. Skilful and subtle transition between tenses where necessary."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c1" value="4" id="r14" title="Few spelling errors in advanced words. Good knowledge of tense. Showcases successful switch in tense."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c1" value="3" id="r13" title="No spelling errors in basic words. Lacking knowledge of advanced punctuation. Several spelling errors in advanced words, satisfactory knowledge of basic tense."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c1" value="2" id="r12" title="Poor punctuation, few spelling errors in basic words. Some knowledge of tense."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c1" value="1" id="r11" title="Poor use of punctuation, spelling errors in basic words. No knowledge of tense."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c1" value="0" id="r10" title="Not applicable"></i></td>
                                            </tr>
                                            <tr min="10">
                                                <td>Word choice/Vocabulary</td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c2" value="5" id="r25" title="Correct use of complex words and only where necessary."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c2" value="4" id="r24" title="Correct yet unnecessary/repetitive use of complex words."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c2" value="3" id="r23" title="Incorrect and repetitive use of advanced/complex words."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c2" value="2" id="r22" title="Correct but repetitive use of basic words, no knowledge of advanced words."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c2" value="1" id="r21" title="Incorrect and repetitive use of basic words."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c2" value="0" id="r20" title="Not applicable"></i></td>
                                            </tr>
                                            <tr min="10">
                                                <td>Syntax & Semantics</td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c3" value="5" id="r35" title="Coherent and complete sentences, employing correct use of parts of speech. Sentences are consistently semantically sound."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c3" value="4" id="r34" title="Coherent and complete sentences, employing correct use of parts of speech. Sentences are syntactically sound but extremely non-contextual."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c3" value="3" id="r33" title="Coherent sentences but employing incorrect use of parts of speech at times. Sentences are semantically unsound, lack meaning."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c3" value="2" id="r32" title="Somewhat coherent yet incomplete sentences (lacking parts of speech where necessary)."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c3" value="1" id="r31" title="Poor sentence construction. Incomplete and incoherent sentences."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c3" value="0" id="r30" title="Not applicable"></i></td>
                                            </tr>
                                            <tr min="6">
                                                <td>Organisation/Flow</td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c4" value="5" id="r45" title="Additional subdivision of intro, body and conclusion to avoid repetition of content."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c4" value="4" id="r44" title="Clear demarcation between intro, body and conclusion, with some restatement/repetition."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c4" value="3" id="r43" title="Structurally coherent, but no clear demarcation between introduction, body and conclusion."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c4" value="2" id="r42" title="The flow of essay is somewhat coherent; Gaps/jumps in content flow at times."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c4" value="1" id="r41" title="Incomprehensible; structure of essay is not logical."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c4" value="0" id="r40" title="Not applicable."></i></td>
                                            </tr>
                                            <tr min="4">
                                                <td>Narrative Style/Technique</td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c5" value="5" id="r55" title="Very engaging; insightfully narrative and vividly descriptive. Usage of advanced techniques like meta-fiction."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c5" value="4" id="r54" title="Very engaging; Narrative and also vividly descriptive. Usage of uncommon styles (such as those having multiple voices/perspectives)."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c5" value="3" id="r53" title="Engaging; Narrative rather than descriptive. Usage of creative writing styles like prose."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c5" value="2" id="r52" title="Descriptive but not very engaging; use of context limited."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c5" value="1" id="r51" title="Very basic, dull, e.g. in the format of plain information dissemination."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c5" value="0" id="r50" title="Not applicable."></i></td>
                                            </tr>
                                            <tr min="2">
                                                <td>Knowledge on subject</td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c6" value="5" id="r65" title="Very strong knowledge base: Ability to tie up main subject matter to other subjects logically and successfully."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c6" value="4" id="r64" title="Strong grasp of subject matter; does not indulge in exaggeration / vagueness."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c6" value="3" id="r63" title="Decent grasp of subject matter; somewhat superfluous in content."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c6" value="2" id="r62" title="Lacks in-depth knowledge; bears several misconceptions and is vague."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c6" value="1" id="r61" title="Completely lacking in knowledge of subject matter; no details."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c6" value="0" id="r60" title="Not applicable."></i></td>
                                            </tr>
                                            <tr min="2">
                                                <td>Conciseness</td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c7" value="5" id="r75" title="Very crisp and concise, while explicating nearly all points."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c7" value="4" id="r74" title="Concise, yet expressing few points on a topic."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c7" value="3" id="r73" title="Short, but a bit tautological."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c7" value="2" id="r72" title="Moderately long and very tautological."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c7" value="1" id="r71" title="Long-winded and pointless."></i></td>
                                                <td><i class="fa fa-square" aria-hidden="true" name="c7" value="0" id="r70" title="Not applicable."></i></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    
                                    <div style="font-size:1.1em;">Suggested score: <span id="sugScore">-</span>/10
                                    </div><script></script>
                                </div>
                            </div>
                        </div>
                        <div class="auto-comment-btn" id="autobtn" ng-click="showAutoComment();" ng-hide="">{{ autocomment }}</div>
                        <div class="auto-comment col-md-6" ng-hide="essayMode" id="autocomment" style="display:none">
                            
                            <div class="auto-comment-body">
                                <!-- body for rubric -->
                                <div class="text-center">
                                    <span><h3>Auto Comment</h3></span>
                                    <div class="essay-form">
                                        <h4>Essay Form/Length</h4>
                                        <div class="auto-comment-box" ng-repeat="comment in essayForm">
                                            <!-- <i class="fa fa-circle-o" aria-hidden="true"></i> -->
                                            <div class="checkbox">
                                              <label class="text-black"><input type="checkbox" ng-model="selected" ng-click="getCheckedValue(selected, comment.comment)" name="autocomment" value="{{comment.comment}}">{{comment.comment}}</label>
                                            </div>
                                            <!-- To be replaced <i class="fa fa-check-circle" aria-hidden="true"></i> -->
                                            <!-- <label>{{comment.comment}}<label> -->
                                        </div>
                                    </div>
                                    <div class="puntuation-spelling">
                                        <h4>Punctuation and Spelling</h4>
                                        <div class="auto-comment-box" ng-repeat="comment in puntuationComment">
                                    <!-- <i class="fa fa-circle-o" aria-hidden="true"></i> -->
                                    <div class="checkbox">
                                       <label class="text-black"><input type="checkbox" ng-model="selected" ng-click="getCheckedValue(selected, comment.comment)" name="autocomment" value="{{comment.comment}}">{{comment.comment}}</label>
                                    </div>
                                    <!-- To be replaced <i class="fa fa-check-circle" aria-hidden="true"></i> -->
                                    <!-- <label>{{comment.comment}}<label> -->
                                 </div>
                              </div>
                              <div class="preposition-conjunction">
                                 <h4>Prepositions and Conjunction</h4>
                                 <div class="auto-comment-box" ng-repeat="comment in prepositionComment">
                                    <!-- <i class="fa fa-circle-o" aria-hidden="true"></i> -->
                                    <div class="checkbox">
                                       <label class="text-black"><input type="checkbox" ng-model="selected" ng-click="getCheckedValue(selected, comment.comment)" name="autocomment" value="{{comment.comment}}">{{comment.comment}}</label>
                                    </div>
                                    <!-- To be replaced <i class="fa fa-check-circle" aria-hidden="true"></i> -->
                                    <!-- <label>{{comment.comment}}<label> -->
                                 </div>
                              </div>
                              <div class="verbs-tenses">
                                 <h4>Verbs and Tenses</h4>
                                 <div class="auto-comment-box" ng-repeat="comment in verbsComment">
                                    <!-- <i class="fa fa-circle-o" aria-hidden="true"></i>  -->
                                    <div class="checkbox">
                                       <label class="text-black"><input type="checkbox" ng-model="checked" ng-click="getCheckedValue(checked, comment.comment)" name="autocomment" value="{{comment.comment}}">{{comment.comment}}</label>
                                    </div>
                                    <!-- To be replaced <i class="fa fa-check-circle" aria-hidden="true"></i> -->
                                    <!-- <label>{{comment.comment}}<label> -->
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- MY Students Tab start here-->
               <div id="teacher_my_students" class="none teacher_home moduleContainer">
                  <!-- <div class="none">My Students</div> -->
                  <div id="" class="col-md-12 col-lg-offset-1 col-lg-10 hide-overflow-x  none table-responsive accordion">
                     <div class="row">
                        <!-- <div class="form-inline col-md-6 col-lg-offset-3" > -->
                        <div class="form-inline" >
                           <ul class="nav nav-tabs">
                              <li id="view_students_li" class="active">
                                 <a onclick="showTeacherMyStudentViewPage()">View Students</a>
                              </li>
                              <li id="activate_students_li">
                                 <a onclick="showTeacherMyStudentActivatePage()">Activate Topic</a>
                              </li>
                              <?php if($category == 'ADMIN' || $category == 'School Admin') {?>
                              <li id="view_teachers_li">
                                 <a onclick="viewTeachersMyStudentActivatePage()">View Teachers</a>
                              </li>
                              <?php }?>
                           </ul>
                           <div id="view_students" class="form-inline">
                              <br>
                              <div class="form-group">
                                 <label>Grade: </label>
                                 <select class="form-control vcenter" id="th_my_student_grade_select">
                                    <option value=''>select</option>
                                 </select>
                              </div>
                              <div class="form-group">
                                 <label>Section: </label>
                                 <select class="form-control vcenter" id="th_my_student_section_select">
                                    <option value=''>select</option>
                                 </select>
                              </div>
                              <div class="form-group">
                                 <label> Child Name: </label>
                                 <input class="form-control" id="ChildNameInput" value=""></input>
                              </div>
                              <div class="form-group">
                                 <button type="button" id="th_my_student_go" class="btn btn-primary" style="font-size:1.3em !important">GO</button>
                              </div>
                           </div>
                           <br>
                           <div id="activate_topic" style="display: none;" class="form-inline">
                              <br>
                              <div class="form-group">
                                 <label>Grade: </label>
                                 <select class="form-control vcenter" id="th_my_student_activate_grade_select">
                                    <option value=''>select</option>
                                 </select>
                              </div>
                              <div class="form-group">
                                 <label>Section: </label>
                                 <select class="form-control vcenter" id="th_my_student_activate_section_select">
                                    <option value=''>select</option>
                                 </select>
                              </div>
                              <!--  <button type="button" id="th_my_student_activate_go" class="btn btn-primary" style="font-size:1em !important">GO</button> -->
                           </div>
                           <!-- <div id="view_teachers" class="form-inline">
                              <br>
                              <div class="form-group">
                                  <label>Grade: </label>
                                  <select class="form-control vcenter" id="th_my_student_grade_select">
                                      <option value=''>select</option>
                                  </select>
                              </div>
                              <div class="form-group">
                                  <label>Section: </label>
                                  <select class="form-control vcenter" id="th_my_student_section_select">
                                      <option value=''>select</option>
                                  </select>
                              </div>
                              <div class="form-group">
                                  <label> Child Name: </label>
                                  <input class="form-control" id="ChildNameInput" value=""></input>
                              </div>
                              <div class="form-group">
                                  <button type="button" id="th_my_student_go" class="btn btn-primary" style="font-size:1.3em !important">GO</button>
                              </div>
                              </div> -->
                        </div>
                     </div>
                     <br>
                     <div class="form-inline col-md-9 col-lg-offset-2" style="display: none;" id='my_student_no_data'><br><br>No Data Found</div>
                     <div id="student_data_note" class="form-inline col-md-8 col-md-offset-2 col-xs-10 col-xs-offset-1" style="display: none;">Please note that STUDENT DATA IS ESSENTIAL FOR US TO TRACK PROGRESS SO PLEASE ENSURE YOU DO NOT INTERCHANGE STUDENT DETAILS</div>
                     <div id="student_table_view" class="row none" style="margin-bottom: 21px;">
                        <table class="table  table-bordered  students_table" id="my_students_view">
                           <thead>
                              <tr>
                                 <th style="width:5%"> #</th>
                                 <th style="width:15%"> Username </th>
                                 <th style="width:15%"> Name </th>
                                 <!-- <th style="width:20%"> Child's e-mail </th> -->
                                 <th style="width:10%"> DOB </th>
                                 <th style="width:15%"> Parent e-mail </th>
                                 <th style="width:6%"> Class </th>
                                 <th style="width:7%"> Section </th>
                                 <th style="width:13%"> Password </th>
                                 <th style="width:12%"> </th>
                              </tr>
                           </thead>
                           <tbody id="my_student_view_tbody">
                           </tbody>
                        </table>
                        <input type="hidden" id= "userIDString" value=""></input>
                     </div>
                     <div id="student_dataparentemail_note" class="form-inline col-md-8 col-md-offset-2 col-xs-10 col-xs-offset-1" style="display: none; bottom:9px; margin-bottom: 21px;">Note: You can enter multiple parent email ids separated by comma.</div>
                     <div id="student_table_activate" class="row col-md-12" style="display: none">
                        <!-- <div class="form-inline col-md-8 col-lg-offset-3" > -->
                        <div class="form-inline">
                           <div class="form-group">
                              <label>Start Date: </label>
                              <input class='ll-skin-nigran text-box' readonly='true' type='text' onkeydown='return DateFormat(this, event.keyCode)'  id='studentActivateStartDate'>
                           </div>
                           <div class="form-group">
                              <label>End Date: </label>
                              <input class='ll-skin-nigran text-box' readonly='true' type='text' onkeydown='return DateFormat(this, event.keyCode)'  id='studentActivateEndDate'>
                           </div>
                           <div class="form-group">
                              <label>Focus on: </label>
                              <select class="form-control vcenter" id="th_my_student_activate_groupskill_select">
                                 <option value=''></option>
                              </select>
                           </div>
                        </div>
                        <div class="row" style="margin-top: 20%;">
                           <div class="col-md-2 text-right" style="margin: 15px 0;">
                              <i class="fa fa-exclamation-triangle fa-3x"></i>
                           </div>
                           <div class="col-md-10">
                              <div>
                                 <textarea disabled="disabled" rows="3" class="form-control" id="activate_topic_txtarea"></textarea>
                              </div>
                           </div>
                        </div>
                        <div class="form-inline" style="margin-top:2%">
                           <button type="button" onclick="" id="save_activate_changes" style="bottom:5px; font-size:1.3em !important" class="form-control btn btn-primary">Save Changes</button>
                        </div>
                     </div>
                     <div id="teacher_data_note" class="form-inline col-md-8 col-md-offset-2 col-xs-10 col-xs-offset-1" style="display: none;">Please note that Admin IDs cannot be edited. By default, they have no primary class and can view reports of all classes.</div>
                     <div id="teacher_table_view" class="row none" style="margin-bottom: 21px;display: none;">
                        <table class="table  table-bordered  teachers_table" id="teachers_view" style="display: none;">
                           <thead>
                              <tr>
                                 <th style="width:5%"> #</th>
                                 <th style="width:15%; text-align: left;"> Username </th>
                                 <th style="width:15%;text-align: left;"> Name </th>
                                 <th style="width:10%;text-align: left;"> Classes Assigned </th>
                                 <th style="width:15%;text-align: left;"> Primary Class </th>
                                 <th style="width:12%"> </th>
                              </tr>
                           </thead>
                           <tbody id="my_teacher_view_tbody">
                           </tbody>
                        </table>
                        <input type="hidden" id= "userIDString" value=""></input>
                        <div class="form-inline" style="margin-top:2%" id="teacherBtns">
                           <button type="button" onclick="" id="save_viewteacher_changes" style="bottom:5px; font-size:1.3em !important" class="form-control btn btn-primary">Save Changes
                           </button>
                                <button type="button" onclick="" id="cancel_viewteacher_changes" style="bottom:5px; font-size:1.3em !important" class="form-control btn btn-primary">Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- MY Students Tab Ends here-->

                <!-- Teacher essay start here-->
                
                <div id="teacher_essay_allotment" class="none teacher_home moduleContainer" ng-controller="EssayAllotmentCtrl">
                    
                    <div class="container" >
                        <div class="v2-essay-list-loading dots-loader" id="v2-modal-loader"  ng-show="showCurrentTopicDataLoding"></div>
                        <div class="v2-teacher-essay-allotment">
                            <div id="" style="" class="table-responsive accordion">
                                <form name="v2-showEssayData" class="text-align-right" ng-submit="showEssayAssignment()">
                                    <div class="form-inline">
                                        <div class="form-group">
                                            <label>Grade: </label>
                                            <select id="v2-showEssayData-grade-select" name="childclass" class="form-control vcenter ng-pristine ng-untouched ng-valid" ng-model="essayAssignement.childclass">
                                                <option value="">select</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Section: </label>
                                            <select id="v2-showEssayData-section-select" name="childsection" class="form-control vcenter ng-pristine ng-untouched ng-valid" ng-model="report.childsection">
                                                <option value="">select</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <button style="font-size:1.3em !important" class="btn btn-primary " id="v2-showEssayData" type="submit">GO</button>
                                        </div>
                                    </div>
                                    <div class="v2-active-new" id="v2-active-new">
                                        <div class="form-group">
                                            <a href="javascript:void(0);" id="v2-active-essay-accordion" class="btn btn-primary v2-essay-mini" ng-click="showHideActiveNew()">Activate new topic</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="v2-essayAssignment" id="v2-essayAssignment" style="display: none;">
                                <div class="text-left v2-new-topic-heading">
                                    <h3>New Essay Assignment <span class="fa fa-info-circle" title="When you assign an essay, students will be prompted to write only on that particular topic. Other topics will be disabled for the duration of the assignment."></span></h3>
                                </div>
                                <div class="row">
                                    <form name="v2-activateNewEssayTopic" class="" ng-submit="activateNewEssayTopic()">
                                    <div class="col-md-10 col-sm-12 v2-new-topic-form">
                                        <div class="row text-left">
                                            <div class="col-md-3 col-sm-12 v2-new-topic-form-col-md-3">
                                                <label>Assign Essay Topic:</label>
                                            </div>
                                            <div class="col-md-9 col-sm-12 v2-new-topic-form-col-md-9">
                                                <input type="text" name="topicName" class="form-control v2-topicName" id="v2-topicName" maxlength="200">
                                            </div>
                                        </div>
                                        <div class="row text-left">
                                            <div class="col-md-3 col-sm-12 v2-new-topic-form-col-md-3">
                                                <label>Assign Duration:</label>
                                            </div>
                                            <div class="col-md-9 col-sm-12 v2-new-topic-form-col-md-9 row">
                                                <div class="col-md-4 col-sm-12">
                                                    <select name="duration" class="form-control v2-duration" id="v2-essayDuration" ng-change="updateDateRange()" ng-model="durationSelected">
                                                        <option value="7">7 Days</option>
                                                        <option value="15" selected="selected">15 Days (Default)</option>
                                                        <option value="30">30 Days</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-1 col-sm-12 v2-essay-seprator">OR</div>
                                                <div class="col-md-7 col-sm-12">
                                                    <span class="v2-essayDatePicker">
                                                        From <input class="v2-topicStartDate form-control" name="v2-topicStartDate" id="v2-topicStartDate" type="text" readonly="">
                                                        <span class="v2-essay-calendarImage"></span>
                                                        <span class="v2-essay-calendar-to">
                                                            To <input class="v2-topicEndDate form-control" name="v2-topicEndDate" id="v2-topicEndDate" type="text" readonly="">
                                                            <span class="v2-essay-calendarImage"></span>
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-12 text-right">
                                        <button name="v2-new-essay-save" id="v2-new-essay-save" class="v2-new-essay-save btn-save" type="submit">ACTIVATE</button>
                                        <button name="v2-new-essay-reset" type="button" id="v2-new-essay-reset" class="v2-new-essay-reset btn-reset" ng-click="hideHideActiveNew()">CANCEL</button>
                                    </div>
                                    </form>
                                </div>
                                
                            </div>
                            
                            <div class="v2-essay-active-topic-wrapper">
                                 <div class="v2-essay-active-topic text-left" id="v2-essay-active-topic" ng-show="topicActive!=0">
                                     <p>The topic '<span>{{topicName}}</span>' is currently active. It will be deactivated on {{topicEndDate}}. <span class="v2-essay-deactivate"><a href="javascript:void(0);" ng-click="essayActiveTopicDeactivate()">Deactivate now</a></span></p>
                                </div>
                                
                                <div class="v2-othertopicbystudent"><a href="javascript:void(0);" ng-click="openModalEssay('otherTopic')" >View all essays with topics chosen by students</a></div>
                                
                                <div class="v2-previouslytopic-wrapper">
                                    <div class="v2-previouslytopic-heading"><span class="v2-heading">Recently Assigned Topics</span> <span ng-show="recentlyPagignation"><a href="javascript:void(0);" ng-click="ShowAllRecentlyPagignation();">({{recentlyPagignation}})</a></span></div>
                                    <div class="v2-essay-table-wrapper" ng-show="currentlyArray.length>0">
                                    <table class="table-responsive table-hover" ng-show="currentlyArray.length>0">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;text-align: center;">S.No.</th>
                                                <th>Essay Name</th>
                                                <th>Dates Assigned</th>
                                                <th>Submissions</th>
                                                <th>Pending Review</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr ng-repeat="currentData in currentlyArray  | limitTo:limitrecentlyPagignation ">
                                                <td style="width: 50px;text-align: center;">{{ $index + 1 }}</td>
                                                <td><a href="javascript:void(0);" ng-click="openModalEssay('currentTopic',currentData.essayID,currentData.essayTitle )">{{ currentData.essayTitle }}</a></td>
                                                <td  ng-class="currentData.isActive=='1' ? 'v2-essayActive' :'v2-essayDeactive'">{{ currentData.activationDate }} - {{ currentData.deactivationDate }} </td>
                                                <td><a href="javascript:void(0);"  ng-click="openModalEssay('submitted',currentData.essayID,currentData.essayTitle )">{{ currentData.submission }}</a></td>
                                                <td><a href="javascript:void(0);"  ng-click="openModalEssay('pending',currentData.essayID,currentData.essayTitle)">{{currentData.pending}}</a></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    </div> 
                                    <div class="v2-no-essay-data-found" ng-show="currentlyArray.length==0 && showCurrentTopicDataLoding!=true">No assigned topic found.</div>
                                </div>
                                
                                <div class="v2-previouslytopic-wrapper v2-previouslytopic-wrapper-last">
                                    <div class="v2-previouslytopic-heading"><span class="v2-heading">Essays Pending for Review</span> <span  ng-show="pendingPagignation"><a href="javascript:void(0);" ng-click="ShowAllPendingPagignation();">({{pendingPagignation}})</a></span></div>
                                    <div class="v2-essay-table-wrapper" ng-show="pendingArray.length>0">
                                    <table class="table-responsive table-hover" ng-show="pendingArray.length>0">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;text-align: center;">S.No.</th>
                                                <th>Essay Name</th>
                                                <th>Submitted By</th>
                                                <th>Submitted On</th>
                                                <th>Pending Since</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr ng-repeat="pendingData in pendingArray | limitTo:limitpendingPagignation ">
                                                <td style="width: 50px;text-align: center;">{{ $index + 1 }}</td>
                                                <td><a href="javascript:void(0);" ng-click="getEssay(pendingData.scoreID,this,0,pendingData.topicID);">{{ pendingData.essayTitle }}</a></td>
                                                <td>{{ pendingData.Author }}</td>
                                                <td>{{ pendingData.allotedOn }}</td>
                                                <td>{{ pendingData.pendingsince }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    </div>
                                    <div class="v2-no-essay-data-found" ng-show="pendingArray.length==0 && showCurrentTopicDataLoding!=true">No pending essay found.</div>
                                </div>
                                <modal visible="showModal" id="essayModalBox"></modal>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <!-- Teacher essay Reports Tab Ends here-->
                <div class="row none evaluateEssayContainer moduleContainer">
                    <iframe id="reviewEssay" width="67%" height="500" ></iframe>
                </div>     

                <div id="teacher_do_mindspark" class="none teacher_home moduleContainer">
                    <div class="none">DO Mindspark</div>
                </div>
                <!-- Teacher DO Mindspark Reports Tab Ends here-->

                <!-- Teacher Settings Tab start here-->
                <div id="teacher_settings" class="none teacher_home moduleContainer">
                     <div  class="col-md-10 col-md-offset-1 col-lg-offset-2 col-lg-8  none table-responsive accordion">
                        <div class="row">
                            <div class="form-inline col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 text-left" >
                                <div class="form-group">
                                    <label>Grade: </label>
                                    <select class="form-control vcenter" id="th_setting_grade_select">
                                        <option value=''>select</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Section: </label>
                                    <select class="form-control vcenter" id="th_setting_section_select">
                                        <option value=''>select</option>
                                    </select>
                                </div>
                               <button type="button" id="th_setting_go" class="btn btn-primary" style="font-size:1.3em !important">GO</button>
                             </div>
                        </div>
                        <br><br><br>
                        <div id='generalSetting'>
                             <div class="row">
                                <div class="form-inline col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 text-left" >
                                    <div class="form-group">
                                        <label>Session Length: </label>
                                        <select class="form-control vcenter" id="ti_setting_session_length">
                                            <option value='15'>15</option>
                                            <option value='20'>20</option>
                                            <option value='30' selected>30</option>
                                        </select>
                                        <label> minutes. </label><span class='glyphicon glyphicon-info-sign' title='Lets you decide how long students do Mindspark English in a day.'></span> <span class='glyphicon glyphicon-asterisk' title='Usage exceeding 30 minutes per day is currently disabled.'></span>
                                    </div>
                                 </div>
                            </div>
                            <br>
                            <div class="row form-inline">
                                <div class="form-inline col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 text-left" >
                                    <div class="form-group">
                                        <label>Enable The Grounds Only after </label>
                                        <select class="form-control vcenter" id="ti_setting_ground_enable_after">
                                            <option value='10'>10</option>
                                            <option value='15' selected>15</option>
                                            <option value='20'>20</option>
                                        </select> 
                                        <label>minutes per day.</label> <span class='glyphicon glyphicon-info-sign' title='Enabling the grounds after a set period of usage per day.'>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row" >
                                <button type="button" id="ti_setting_save" class="btn btn-primary" style="margin:13px;font-size:1.3em !important">Save Changes</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Teacher Settings Tab Ends here-->
                <div id="home" class="none home_page moduleContainer">
                    <div id="skillometer" class="homepage alignLeft" ng-controller="skillOmeterController">
                            <div skillometer="skillometer" >
                            </div>
                    </div>
                        <div id="quoteContainer" class="homepage">
                            <!-- <div id="quoteSparkie"></div> -->
                            <!-- <div id="quoteSparkieCount">
                                {{'SPARKIES : ' + sessionData.sparkies }}
                            </div> -->
                            <div id="quoteHeader">
                                Quote of the day:
                            </div>
                            <div id="quote">
                                {{sessionData.quoteObject.Quote}}
                            </div>
                            <div id="quoteAuthor">
                                {{'-' + sessionData.quoteObject.Author}}
                            </div>
                        </div>
                </div>
                <div id="sessionReportContainer" class="none sessionReportContainer moduleContainer">
                     <!-- session Report -->
                    <h1 id="reportSection" class='sessionH1 none'>Daily Report</h1>
                    <div id="homeReport" class="col-md-12 col-lg-offset-1 col-lg-11  none table-responsive accordion">
                        <h2 class='sessionH2 none'>Passages</h2>
                        <dl id="sessionReport" class="sessionReport homepage"></dl>
                    </div>
                    <div id="homeReportNCQ" class="col-md-12 col-lg-offset-1 col-lg-11 none table-responsive accordion">
                        <h2 class='sessionH2 none'>Non-passage Questions</h2>
                        <dl id="sessionReportNCQ" class="sessionReport homepage"></dl>
                    </div>
                    <div id="homeReportEssay" class="col-md-12 col-lg-offset-1 col-lg-10 none table-responsive accordion">
                        <h2 class='sessionH2 none'>Essays</h2>
                        <dl id="sessionReportEssay" class="sessionReport homepage"></dl>
                    </div>
                    <div id="essayLookbackContent" class="col-md-12 col-lg-10 hovers none"></div>
                    <input type="hidden" id="treportGrade" value="">
                    <input type="hidden" id="treportSection" value="">
                    <input type="hidden" id="treportStartDate" value="">
                    <input type="hidden" id="treportEndDate" value="">
                    <input type="hidden" id="treportTabClicked" value="">
                    <input type="hidden" id="treportBtnClicked" value="">
                    <input type="hidden" id="trchildName" value="">
                </div>
                <div id="activitySelector" class="the_grounds none moduleContainer">
                    <div ng-repeat="(key,data) in activityData" class="gameIconContainer col-md-2">
                        <div class="activityThumbnail" id="gameButton-{{data.igreDesc}}" data-key="{{data.igreid}}">
                            <div class="gameIcon" back-img="{{'theme/img/Language/activityThumbnails/' + data.igreDesc + '.png'}}">
                            </div>
                            <button class="merge gameName">
                                {{data.igreDesc.split('_').join(' ')}}
                            </button>
                        </div>
                    </div>
                </div>
                <div id="profileContainer" class="row profileContainer none moduleContainer">
                    <div ng-show="changePassword" class="modal-box" ng-click="toggleChangePassword('cancel')">
                    </div>
                    <div ng-show="changePassword" class="detail-box">

                        <div class="detail-header">
                            <label>Change Password</label>
                            <button ng-click="toggleChangePassword('cancel')" class="close-prompt"><i class="fa fa-close"></i></button>
                        </div>
                        <div class="detail-body row">
                            <div class="col-sm-12 col-md-12">
                                <label>Enter Original Password:</label>
                                <input type="password" class="form-control" ng-model="oldPassword"/>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <label>Enter New Password:</label>
                                <input type="password" class="form-control" ng-model="newPassword"/>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <label>Confirm New Password:</label>
                                <input type="password" class="form-control" ng-model="confirmPassword"/>
                            </div>
                        </div>
                        <div class="detail-footer text-right">
                            <button class="btn btn-primary small-button" ng-click="toggleChangePassword('save')">Save</button>
                            <button class="btn btn-primary small-button" ng-click="toggleChangePassword('cancel')">Cancel</button>
                        </div>
                    </div>
                    <div class="profile-header col-md-12 col-lg-offset-1 col-lg-10">
                        <span class="pull-left">
                            <i class="fa fa-home fa-3x cursor-pointer navigate-icon" aria-hidden="true" onclick="showHomePage()"></i>
                        </span>
                        <span class="text-center">
                            <label>PROFILE</label>
                        </span>
                    </div>
                    <div class="profile-box col-md-12 col-lg-offset-1 col-lg-10"> 
                       
                        <div ng-show="changeProfile" class="modal-box" ng-click="closeProfilePic()">
                        </div>
                        <div ng-show="changeProfile" class="change-profile">
                            <button type="button" class="close-profile" ng-click="closeProfilePic()"><i class="fa fa-close"></i></button>
                            <div class="col-md-2" ng-repeat="i in characterArr">
                                <img src="{{charactersPath+($index+1)+'.png'}}" class="characters" ng-click="setNewProfilePic(this)">
                            </div>
                        </div>
                        <div>
                            <div class="col-md-4 text-center">
                                <div class="profile-image">
                                    <img src="{{sessionData.profileImage}}" class="profile-icon-img"/>
                                    <span class="pull-right bottom-corner">
                                        <i class="fa fa-pencil-square fa-2x" aria-hidden="true" ng-click="changeProfilePic()"></i>
                                    </span>
                                </div>
                                <div class="text-left">
                                    <br>
                                    <label style="font-size: larger;">About me:</label>
                                </div>
                                <div>
                                    <div ng-show="editDesc" class="space-top">
                                        <div class="about-me">
                                            {{ sessionData.personalInfo == false ? '' : sessionData.personalInfo }}
                                        </div>
                                        <i class="fa fa-pencil-square fa-2x pull-right" ng-click="editAboutMe()" aria-hidden="true">
                                        </i>
                                    </div>
                                    <div ng-hide="editDesc">
                                        <div class="col-md-10">
                                            <textarea ng-model="descAboutMe" ng-keystroke  ng-trim="false" ng-change="getWordCount(descAboutMe)" ng-keydown="maxLength($event,160)" ng-keypress="maxLength($event,160)" class="form-control"></textarea>
                                        </div>
                                        <div class="col-md-2">
                                            <i class="fa fa-floppy-o fa-2x pull-right" ng-click="saveAboutMe()" aria-hidden="true"></i>
                                            <i class="fa fa-close fa-2x pull-right" ng-click="resetState()" aria-hidden="true"></i>
                                        </div>
                                        <div class="col-md-10">
                                            <span class="pull-right">Characters left:{{ descWordCount }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <form class="left-bar col-md-8 profile-form">
                                <div class="row">
                                    <div class="col-md-4">
                                        <i class="fa fa-user"></i>
                                        <label>First Name:</label>
                                    </div>
                                    <div class="col-md-8">{{sessionData.firstName}}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <i class="fa fa-user"></i>
                                        <label>Last Name:</label>
                                    </div>
                                    <div class="col-md-8">{{sessionData.lastName}}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <i class="fa fa-user"></i>
                                        <label>Username:</label>
                                    </div>
                                    <div class="col-md-8">{{sessionData.childName}}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                                        <label>Password:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-10 sol-sm-10">
                                                <label>******</label>
                                            </div>
                                            <i class="fa fa-pencil-square" ng-click="toggleChangePassword('edit')" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                        <label>Date of Birth:</label>
                                    </div>
                                    <div ng-hide="editableDOB" class="col-md-8">
                                        <div class="row">
                                            <span class="col-sm-10 col-md-10 comment more">{{ sessionData.DOB }}</span>
                                        </div>
                                    </div>
                                    <div ng-show="editableDOB">
                                        <div ng-show="dob" class="col-md-8">
                                            <div class="row">
                                                <span class="col-sm-10 col-md-10 comment more">{{ sessionData.DOB }}</span>
                                                <i class="fa fa-pencil-square icon-top" ng-click="toggleChildDOB('edit')" aria-hidden="true">
                                                </i>
                                            </div>
                                        </div>
                                        <div ng-hide="dob" class="col-md-8">
                                            <div class="row">
                                                <div class="col-md-10 col-sm-10">
                                                    <input type="text" class="form-control" ng-model="updatedDob" id="childDob">
                                                </div>
                                                <i class="fa fa-floppy-o icon-top" ng-click="toggleChildDOB('save')" aria-hidden="true"></i>
                                                <i class="fa fa-close icon-top" ng-click="toggleChildDOB('cancel')" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                                        <label>Class:</label>
                                    </div>
                                    <div class="col-md-8">{{sessionData.childClass}}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                                        <label>Section:</label>
                                    </div>
                                    <div class="col-md-8">{{sessionData.section}}</div>
                                </div>
                                <div class="row"> <div class="col-md-4">
                                        <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                                        <label>School:</label>
                                    </div>
                                    <div class="col-md-8">{{sessionData.schoolName}}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <i class="fa fa-map-marker" aria-hidden="true"></i>
                                        <label>Address:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <div ng-show="address" class="row">
                                            <span id="addressDetails" class="col-sm-10 col-md-10 comment more">{{ sessionData.address === false ? '' : sessionData.address }}</span>
                                            <i class="fa fa-pencil-square icon-top" ng-click="toggleAddressEdit('edit')" aria-hidden="true"></i>
                                        </div>
                                        <div ng-hide="address" class="row">
                                            <div class="col-md-10 col-sm-10">
                                                <textarea ng-model="updatedAddress" class="form-control "></textarea>
                                            </div>
                                            <i class="fa fa-floppy-o icon-top" ng-click="toggleAddressEdit('save')" aria-hidden="true"></i>
                                            <i class="fa fa-close icon-top" ng-click="toggleAddressEdit('cancel')" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <i class="fa fa-mobile" aria-hidden="true"></i>
                                        <label>Phone:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <div ng-show="phoneno" class="row">
                                            <span  class="col-sm-10 col-md-10">{{sessionData.contactNo === false || sessionData.contactNo == 'false' ? '' : sessionData.contactNo}}</span>
                                            <i class="fa fa-pencil-square icon-top" ng-click="togglePhoneEdit('edit')" aria-hidden="true"></i>
                                        </div>
                                        <div ng-hide="phoneno" class="row">
                                            <div class="col-md-10 col-sm-10">
                                                <input type="text" class="form-control" ng-keydown="maxLength($event,25)" ng-keypress="maxLength($event,25)" ng-model="updatedPhone">
                                            </div>
                                            <i class="fa fa-floppy-o icon-top" ng-click="togglePhoneEdit('save')" aria-hidden="true"></i>
                                            <i class="fa fa-close icon-top" ng-click="togglePhoneEdit('cancel')" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                                <div ng-show="toStudent" class="row">
                                    <div class="col-md-4">
                                        <i class="fa fa-mobile" aria-hidden="true"></i>
                                        <label>Parent's Phone:</label>
                                    </div>
                                    <div class="col-md-8">{{ sessionData.parentContactNo===false ? '' : sessionData.parentContactNo }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <i class="fa fa-envelope" aria-hidden="true"></i>
                                        <label>Email:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <div ng-show="email" class="row">
                                            <span class="col-sm-10 col-md-10">{{sessionData.childEmail===false?'':sessionData.childEmail}}</span>
                                    <i class="fa fa-pencil-square icon-top" ng-click="toggleEmailEdit('edit')" aria-hidden="true"></i>
                                 </div>
                                 <div ng-hide="email" class="row">
                                    <div class="col-md-10 col-sm-10">
                                       <input type="text" class="form-control" ng-keydown="maxLength($event,100)" ng-keypress="maxLength($event,100)" ng-model="updatedEmail">
                                    </div>
                                    <i class="fa fa-floppy-o icon-top" ng-click="toggleEmailEdit('save')" aria-hidden="true"></i>
                                            <i class="fa fa-close icon-top" ng-click="toggleEmailEdit('cancel')" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                                <div  ng-show="toStudent" class="row">
                                    <div class="col-md-4">
                                        <i class="fa fa-envelope" aria-hidden="true"></i>
                                        <label>Parent's Email:</label>
                                    </div>
                                    <div class="col-md-8">{{sessionData.parentEmail===false?'':sessionData.parentEmail}}</div>
                                </div>
                                <div  class="row">
                                    <div class="col-md-4">
                                        <i class="fa fa-envelope" aria-hidden="true"></i>
                                        <label>Secret Question:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <select id="secretQuestion">
                                            <option ng-repeat="question in secretQuestions" value="{{ question }}">{{question}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div  class="row">
                                    <div class="col-md-4">
                                        <i class="fa fa-envelope" aria-hidden="true"></i>
                                        <label>Secret Answer:</label>
                                    </div>
                                    <div class="row col-md-8">
                                        <div class="col-md-8">
                                            <input type="text" class="form-control"  ng-keydown="maxLengthWords($event,50)" ng-keypress="maxLengthWords($event,50)" ng-model="secretAns"/>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="profile-save btn btn-primary small-button" ng-click="saveSecretQues($event)" ng-model="secretAns">
                                                Save
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div id="diagnosticTestContainer" class="row none">
                    <div id="diagnosticProgressPanel" class="col-lg-12">
                        <button id="scrollLeftProgress" class="none"></button>
                        <div id="progressPanelBubblesContainer"></div>
                        <button id="scrollRightProgress" class="none"></button>
                        <div id="innerBubblesHolder"></div>
                    </div>
                </div>

                <!--COMMENTSYSTEM LIST START-->
                <div id="listView" class="none moduleContainer" ng-controller="commentSystemListController">
                    <div id="commentList">
                        <div id="" class="col-md-12 col-lg-offset-1 col-lg-10  table-responsive accordion">
                            <!-- <h2 class="sessionH2 none" style="display: block;">Passages</h2> -->
                            <dl id="sessionReport" class="sessionReport homepage" style="font-weight:inherit;">
                               <!--  <dt class="accordionTitleNoBefore" id="headerQuestionsTab">
                                    <span style="width: 10%;">Sr. No.</span>
                                    <span style="width: 70%; text-align: left;">Passage Name</span>
                                    <span style="width: 16%;">Total Attempted</span>
                                </dt>
                                <dt class="accordionTitleNoBefore">
                                    <span style="width: 10%">1</span>
                                    <span style="width: 70%; text-align: left;">Bill Gates - Birth of Microsoft at Harvard</span>
                                        <a data-toggle="tooltip" title="Click to open" class="accordion-title accordionTitle accordionTitleNoBefore js-accordionTrigger is-collapsed is-expanded" aria-expanded="false" aria-controls="accordionAll1">1</a>
                                </dt> -->
                                <span class="cmtPgTitle">This page shows only those comments (made by you) which have been responded by the Mindspark English team.</span>
                                <dd class="accordion-content accordionItem is-expanded animateIn" id="accordionAll1" aria-hidden="true">
                                    <table id="commentListTab">
                                        <tbody>
                                            <tr ng-show = "showHeader">
                                                <th colspan="5">  </th>
                                            </tr>
                                            <tr ng-show = "showHeader">
                                                <th style="width:10%">Sr. No.</th>
                                                <th style="text-align:justify; width:60%;">Comment</th>
                                                <th style="width:10%">Comment Category</th>
                                                <th style="width:10%">Commented From</th>
                                                <th style="width:10%">Comment Date</th>
                                            </tr>
                                            <tr class="sessionReport-container" ng-if="userComments.length === 0 || userComments.length === undefined">
                                                <td colspan="5" style="font-weight: bolder;font-size: 1.3em;"> No data available </td>
                                            </tr>
                                            <tr class="sessionReport-container cursor-pointer" ng-style="set_color(row.viewed)"  ng-click="showComtDetails(row.commentID)" ng-repeat="row in userComments">
                                                <td style="width:9%">{{ $index + 1 }}</td>
                                                <td style="text-align:left;width:60%; color: #0000EE;">{{ row.comment }}</td>
                                                <td style=" width:10%;">{{row.categoryName}}</td>
                                                <td style=" width:10%">{{row.page}}</td>
                                                <td style="width:10%;">{{ row.lastModified| date : 'MM/dd/yyyy' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </dd>
                            </dl>
                        </div>
                    </div>
                    <div id="viewComment" class="viewCommentBoxDiv" style="display: none;">
                        <div id="" class="none col-md-12 viewCommentBox">

                            <div class="row" ng-show="showQues" style="border-bottom: 2px solid black; padding: 3px;">
                                <div class="col-md-4" ng-show="showQues"><b>{{ heading }} : </b></div>
                                <div class="col-md-6 psgQuesSnippet" ng-show="showQues">
                                    {{ content }}
                                </div>
                            </div>
                            <div class="row" ng-show="showPsg" style="border-bottom: 2px solid black; padding: 3px;">
                                <div class="col-md-4" ng-show="showPsg"><b>{{ heading }} : </b></div>
                                <div class="col-md-6" ng-show="showPsg">
                                    <a ng-click="showContent(passageType,itemID)" style="color: #1373AB !important;" class="psgQuesSnippet cursor-pointer">{{ contentName }}</a>
                                </div>
                            </div>
                            <div class="row" ng-show="showEssay" style="border-bottom: 2px solid black; padding: 3px;">
                                <div class="col-md-4" ng-show="showEssay"><b>{{ heading }} : </b></div>
                                <div class="col-md-6 psgQuesSnippet" ng-show="showEssay">
                                    {{ contentName }}
                                </div>
                            </div>
                            <div class="row" ng-show="showIGRE" style="border-bottom: 2px solid black; padding: 3px;">
                                <div class="col-md-4" ng-show="showIGRE"><b>{{ heading }} : </b></div>
                                <div class="col-md-6 psgQuesSnippet" ng-show="showIGRE">
                                    {{ contentName }}
                                </div>
                            </div>
                            <div class="row" style="border-bottom: 2px solid black; padding: 6px;" ng-repeat="details in userCommentDetails" >
                                <div class="row" ng-if="details.userID != 0 ">
                                    <div class="col-md-5  psgQuesSnippet" style="text-align: left;">{{details.counter}}. <b>Your comment : </b></div>
                                    <div class="col-md-7" ng-if="details.comment != '' " style="text-align: left;">{{ catContent }} -> {{ details.comment }}</div>
                                    <div class="col-md-7" ng-if="details.comment == '' " style="text-align: left;">{{ catContent }}</div>
                                    <!-- <div class="col-md-7" style="text-align: left;" ng-if="details.comment == '' ">{{ catContent }}</div> -->
                                </div>
                                <div ng-if="details.userID == 0 ">
                                    <div class="col-md-12 psgQuesSnippet" style="text-align: left"><b>Our Reply :</b></div>
                                    <div class="col-md-12" style="text-align: left"  ng-bind-html="trustAsHtml(details.comment)"></div>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 1%; padding: 3px;" ng-show="showReply">
                                <div class="col-md-4 psgQuesSnippet" style="text-align: left;">Reply to Mindspark :</div>
                                <div class="col-md-8">
                                    <textarea id="replyToMs" class="form-control" name="replyToMs" value="" rows="2" placeholder="Enter your reply here"> </textarea>
                                    <!-- <input type="text" class="form-control" id="replyToMs"  name="replyToMs" value=""> -->
                                    <input type="hidden" id="commentID" name="" value="{{ commentID }}">
                                    <input type="hidden" id="commentCatID" name="" value="{{ commentCategoryID }}">
                                    <input type="hidden" id="commentSubCatID" name="" value="{{ commentSubCategoryID }}">
                                </div>
                            </div>
                            <div class="row" style="margin-top: 1%; padding: 3px;">
                                <button id="submitComment" class="btn-primary" ng-show="showReply" style="font-size: 1em !important" ng-click="submitReplyComment()"> Submit </button>
                                    
                                <button id="submitComment" class="btn-primary" style="font-size: 1em !important" ng-click="showComtList()"> Back </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--COMMENT SYSTEM LIST END-->
                    
                <!-- essay Writer Tables -->
                <div class="row none essay_writer moduleContainer">
                    <button class="btn-primary" onclick="showEssaysToAttempt(event)"> Start New Essay </button>
                    <!-- <button class="btn-primary" onclick="writeYourOwnTopic(event)"> Write Your Own Essay </button>
                    <div class="write-topic">
                        <div class="topic-modal"></div>
                        <div class="own-topic col-sm-3">
                            <div class="topic-header">
                                <span>Topic</span>
                                <span class="pull-right"><i class="fa fa-times" aria-hidden="true"></i></span>
                            </div>
                            <div class="topic-box">
                                <input type="text" id="ownTopic" class="form-control" ng-model="ownTopic"/>
                            </div>  
                            <div class="text-right">
                                <button id="startOwnTopic" type="button" class="btn btn-primary small-button">Submit</button>
                            </div>
                        </div>
                    </div> -->
                    <div class="tableContainer  col-md-12 col-lg-offset-1 col-lg-10 none" id="essaysToAttempt"> 
                        <h4 ng-show="essaysPendingYes">Choose a topic</h4>
                        <h4 ng-show="essaysPendingNo" class="essaysPending">No more essay topics to attempt</h4>
                        <ul class="list-group" id="essaysToAttemptLi">
                        
                            <!-- <li ng-repeat="essay in essays" class="list-group-item newEssay" data-id="{{essay.essayID}}"> 
                            -->

                            <li ng-repeat="essay in essays" class="list-group-item newEssay" data-id="{{essay.essayID}}" essay-force="{{essay.forceEssay}}" essay-id="{{essay.ews_essayDetailsID}}">
                                {{essay.essayTitle}}
                            </li>  
                            <li ng-hide="essays.length" class="list-group-item newEssay none"> No essays in this list!</li>
                        </ul>
                        <h4>
                            <div class="row" ng-show="forceEssay=='no'">
                                <div class="col-sm-4 col-md-3" style="margin-top:6px;">
                                        Enter your own topic:
                                </div>
                                <div class="col-sm-8 col-md-9">
                                    <input type="text" id="ownTopic" maxlength="100" class="form-control">
                                </div>
                            </div>
                        </h4>
                        
                    </div>
                    <div class="tableContainer  col-md-12 col-lg-offset-1 col-lg-10" id="incompleteEssays">
                        <h4>Incomplete Essays</h4>
                        <ul class="list-group">
                            <li ng-repeat="essayIncomplete in incompleteEssays" class="list-group-item essaySummay" data-id="{{essayIncomplete.id}}" essay-id="{{essayIncomplete.essayId}}" essay-force="{{essayIncomplete.isForce}}">
                                {{essayIncomplete.name}}
                            </li>
                            <li ng-hide="incompleteEssays.length" class="list-group-item emptyEntry"> No essays in this list!</li>
                        </ul>
                     </div>
                     <div class="tableContainer col-md-12 col-lg-offset-1 col-lg-10" id="completeEssays">
                         <h4>Recently Completed Essays</h4>
                         <ul class="list-group">
                            <li ng-repeat="completeEssay in completeEssays" class="list-group-item essaySummay" data-id="{{completeEssay.id}}" essay-id="{{completeEssay.essayId}}">
                                {{completeEssay.name}}
                            </li>
                            <li ng-hide="completeEssays.length" class="list-group-item emptyEntry"> No essays in this list!</li>
                        </ul>
                     </div>
                     <div class="tableContainer col-md-12 col-lg-offset-1 col-lg-10" id="gradedEssays">
                         <h4>Graded Essays</h4>
                         <ul class="list-group">
                            <li ng-repeat="gradedEssay in gradedEssays" class="list-group-item essaySummay" data-id="{{gradedEssay.id}}" essay-id="{{gradedEssay.essayId}}">
                                {{gradedEssay.name}}
                            </li>
                            <li ng-hide="gradedEssays.length" class="list-group-item emptyEntry"> No essays in this list!</li>
                        </ul>
                     </div>
                     <button id="startEssay" class="none btn-primary" ng-show="forceEssay=='no'"> Start Essay </button>
                </div>
                <div class="row none gameContainers moduleContainer">
                    <iframe id="gameFrame" width="800px" height="600px" frameborder="0" scrolling="no" class=""></iframe>
                    
                    <button id="gameExitButton" class="btn-primary none"> <i class="fa fa-times fa-6"></i> </button>
                    <button id="gamePassageNext" class="none btn-primary">Exit <i class="fa fa-caret-right"></i></button>
                </div>
                <div class="row none gradedEssayContainer moduleContainer">
                    <iframe id="essayFeedback" width="800" height="500" ></iframe>
                </div>
				<div class="row the_classroom none appContainers moduleContainer">
                    <!--By Nivedita for selection of grade for DMS-->
                    <div id="dms_grade" class="col-md-12 col-lg-offset-1 col-lg-10  none table-responsive accordion">
                       <div class="row form-inline">
                            <div class="form-group">
                                <label>Grade: </label>
                                <select id="dms_grade_select" class="form-control vcenter">
                                    <option value=''>select</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <button style="font-size:1.3em !important" class="btn btn-primary" id="dms_go" type="button">GO</button>
                                <input type="hidden" name="" id="dataLengthCount" value="">
                            </div>
                        </div>
                    </div>
                    <!--END-->
                    <div class ="none leftRock"></div>
                    <div class ="none rightRock"></div>
                    <div class ="none characterArrow"></div>
                    <div id="arrow" class="none"></div>
                    <div id="arrowText" class="none"></div>

                    <!-- question Component-->
                    <div id="backwardNavigationContainer" class="col-md-2 col-sm-2 question">
                        <div id="queNum"></div>
                        <button id="leftButton" class="navigationButtons none"> ◀ </button><br>
                        <img id="flagger" src="theme/img/Language/flaggingFlag.png" onclick="flagQuestion()" class="none"/><br>
                        <div id="legendContainer" class="none">
                            <span> Legend: </span>
                            <table id="legendTable">
                                <tbody>
                                    <tr>
                                        <td>P:</td><td>Passage</td>
                                    </tr>
                                    <tr>
                                        <td>L:</td><td>Listening Exercise</td>
                                    </tr>
                                    <tr>
                                        <td>Q:</td><td>Question</td>
                                    </tr>
                                    <tr>
                                        <td>W:</td><td>Writing Exercise</td>
                                    </tr>
                                    <tr>
                                        <td></td><td>Not Attempted</td>
                                    </tr>
                                    <tr>
                                        <td></td><td>Current Position</td>
                                    </tr>
                                    <tr>
                                        <td></td><td>Marked for later</td>
                                    </tr>
                                </tbody>
                            </table>
                            <span> (click yellow flag to mark) </span>
                        </div>
                    </div>

                    <div id="passageQuestionsContainer" class="col-md-8 col-sm-8 question">
                        <div id="questionContainer"  class="none"></div>
                        <div id="audioContainer" class="none"></div>
                        <div id="passageContainer" class="none"></div>
                        <!-- essay Component-->
                        <div id="essayContainer" class="none">
                            <div id="essayTopic"></div>
                            <div>
                                <textarea id="essay"  value="" rows="17" spellcheck="false"> </textarea>
                            </div>
                        </div>
                        <button id="saveEssay" class="none btn-primary" onclick="saveEssay()"> Save </button>
                        <button id="submitEssay" class="none btn-primary" onclick="saveEssay(1)"> Submit </button>
                    </div>
                    
                    <div id="forwardNavigationContainer" class="col-md-2 col-sm-2 question">
                        <div id="essay_instructions" class="none">
                            <div id="essayTopic"></div>
                            <div>
                                <div class="autoSaveLblDiv text-center text-white"><label>Your essay will be saved automatically after every 3 seconds.</label></div>
                            </div>
                            <div class="textAreaAfter">[ Words entered: 0 ]</div>
                        </div>
                        <?php if($category == 'STUDENT') {?>
                            <figure id='lookBackFigure' class='none'>
                        <?php }?>
                        <?php if($category == 'TEACHER' || $category == 'ADMIN' || $category == 'School Admin') {?>
                            <figure id='lookBackFigure' class='teacherLookBack' style='display:inline-block;text-align:left;position:relative;top: -39%;'>
                        <?php }?>
                        <!-- <figure id='lookBackFigure' class='none'> -->
                            <img id="lookback" src="theme/img/Language/lookback.png" onclick="showLookBack()">
                            <figcaption></figcaption>
                        </figure>
                        <button id="gameExitButton" class="btn-primary none"> Exit </button>
                        <button id="passageNext" class="none btn-primary">Next <i class="fa fa-caret-right"></i></button>
                        <!-- <button id="delayNext" style="display: none" class="btn-primary">10</button> -->
                        <p><lable id="delayNext" style="display: none;color:white;font-size:20px;" > You can submit your response in 10 seconds </lable></p>
                        <button id="questionSubmitButton" class="none btn-primary"> Submit </button> 
                        <button id="rightButton" class="navigationButtons none btn-primary"> <i class="fa fa-caret-right"></i> </button>
                    </div>
                </div>

                <!--nivedita testing for my dictionary-->

                <!-- <div class="row none moduleContainer" id="dialog-61" ng-controller="myDictionaryController as tab" ng-click="check($event)">

                    <div ng-show="meaningSearchMyDict" class="quote-container">
                        <blockquote class="note">
                            <div class="row">
                                <span ng-show="notInDic" class="text-highlight">This word is not in your dictionary.</span>
                            </div>
                            <div class="row">
                                <div class="col-lg-5">Word :</div>
                                <div class="col-lg-6">{{ word }}</div>
                            </div>
                            <div class="row">
                                <div class="col-lg-5">Type :</div>
                                <div class="col-lg-6">{{ type }}</div>
                            </div>
                            <div class="row">
                                <div class="col-lg-5">Definition :</div>
                                <div class="col-lg-6">{{ definition }}</div>
                            </div>
                            <button ng-click="addToDic(searchValue)" ng-model="searchValue" ng-show="notInDic" class="btn btn-small">Add to my dictionary</button>
                        </blockquote>
                    </div>

                    <div class="dic_header">
                        <button ng-repeat="alpha in alphabetes" ng-click="getAlphaValue(alpha)" id="alpha_{{$index}}" class="form-control btn btn-sm alphaBtns">{{ alpha }}</button>
                    </div>
                    <div id="dictionay_book" class="col-md-12 dictionary_content">
                        <div class="row">
                            <div class="form-inline" >
                                <div id="my_dict" ng-show="tab.isSet(1)" class="col-md-12 form-inline">
                                    <input type="hidden" id="save_limit" value="{{limit}}"></input>
                                    <div class="row">
                                        <div class="col-md-11">
                                            <div class="row">
                                                <div class="col-md-6 pages" style="border-right: 3px solid; " >
                                                    <div class="col-md-12" style="margin-bottom: 11px;">
                                                      <input type="text" ng-keypress="trigerEnterKey($event, 1)" ng-keyup="getMeaning(searchValue)" ng-model="searchValue" id="search_in_dict" class="form-control" placeholder="Search for..." style="width: 75% !important;">
                                                      <span>
                                                        <button class="btn btn-default" style="margin: auto" ng-click="getReport()" type="button">Go!</button>
                                                      </span>
                                                    </div>
                                                    <div>
                                                        <table class="table table-responsive" style="text-align: left">
                                                            <tbody>
                                                                <tr ng-if="finalArray.length === 0 || finalArray.length === undefined">
                                                                    <td colspan="2"> No data available </td>
                                                                </tr>
                                                                <tr ng-repeat="row in finalArray | filter:search_word as results">
                                                                    <td style="width:4%"> {{row.sr_no}} </td>
                                                                    <td>
                                                                        <a ng-click="getData(row)" href="javascript:void(0)">{{row.user_word}}</a>
                                                                    </td>
                                                                </tr>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <button type="button" ng-show="showPreviousPage" ng-click="getPrevious(pageNo, alphabet)"  class="btn btn-default btn-lg navigate-left-button" aria-label="Left Align">
                                                      <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                                                    </button>
                                                </div>
                                                <div class="col-md-6 pages">
                                                    <div class="col-md-12 selected-word text-right">
                                                        <span>{{ alphabet }}</span>
                                                    </div>
                                                    <div>
                                                        <table class="table" style="text-align: left">
                                                            <tbody>
                                                                <tr ng-repeat="row in finalArray1 | filter:search_word as results">
                                                                    <td style="width:4%"> {{row.sr_no}} </td>
                                                                    <td>
                                                                        <a ng-click="getData(row)" href="javascript:void(0)">{{row.user_word}}</a>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <button type="button" ng-show="showNextPage" ng-click="getNext(pageNo, alphabet)" class="btn btn-default btn-lg navigate-right-button" aria-label="Right Align">
                                                      <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1 text-center">
                                            <img src="theme/img/Language/Pencil.png" class="img-responsive pencil"/>
                                        </div>
                                    </div>
                                </div>
                             </div>
                        </div>
                    </div>
                </div> -->
                <!-- <button id="opener-5">Open Dialog</button> -->
                <!--end nivedita testing-->
                
                <!-- <div class="teacher_report moduleContainer row none" ng-controller="teacherReportController">
                    <h1 id="">Usage Report</h1>
                    
                    <div class="form-group" >
                        <label for="usageReportGrade"> Select Grade: </label>
                        <select class="form-control" id="usageReportGrade">
                            <option>select</option>
                            <option ng-repeat="grade in grades" value="{{grade}}"> {{grade}}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label> Show Data For:</label>
                        <select id="userReportFor" class="form-control" ng-click="checkMode()">
                            <option ng-repeat="key in showData" value="{{key.split('|')[1]}}"> {{key.split('|')[0]}}</option>
                        </select>
                        <div ng-show="dateMode">
                            <div class="ll-skin-nigran text-box" id="usageReportStartDate">Start Date</div>
                            <div class="ll-skin-nigran text-box" id="usageReportEndDate">End Date</div>
                        </div> 
                    </div>
                    
                    <div class="form-group">
                        <button class="btn-primary" ng-click="getReport()">Submit</button>
                        <button class="btn-primary" ng-click="getReport(1)">View All Students</button>
                    </div>

                    <h5 ng-show="dateAvailabel">Data from {{reportStartDate}} to {{reportEndDate}}</h5>

                    <div ng-show="less30MinText" ng-click="canShowLess30Min=!canShowLess30Min" class="exceptionReport exceptionReportHeaderColorChangeRed">These students spent less than 30 minutes or have not logged in on Mindspark {{lessMinDateModeText}}</div>
                    <table ng-show="canShowLess30Min" class="table exceptionReportTable alignLeft" id="less30MinTable">
                        <thead>
                            <tr>
                                <th> Name </th>
                                <th> Class/Section </th>
                                <th> Time Spent<br>(mins) </th>
                                <th> Last Logged In On </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="row in less30MinData">
                                <td> {{row.childName}}</td>
                                <td> {{row.childClass + ' ' + row.childSection}}</td>
                                <td> {{row.timeSpentByUser}} </td>
                                <td> {{row.lastLoggedInDate}}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div ng-show="more90MinText" ng-click="canShowMore90Min=!canShowMore90Min" class="exceptionReport">These students spent more than 90 minutes on Mindspark {{moreMinDateModeText}}</div>
                    <table ng-show="canShowMore90Min" class="table exceptionReportTable alignLeft" id="more90MinTable">
                        <thead>
                            <tr>
                                <th> Name </th>
                                <th> Class/Section </th>
                                <th> Time Spent<br>(mins) </th>
                                <th> Last Logged In On </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="row in more90MinData">
                                <td> {{row.childName}}</td>
                                <td> {{row.childClass + ' ' + row.childSection}}</td>
                                <td> {{row.timeSpentByUser}} </td>
                                <td> {{row.lastLoggedInDate}}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div ng-show="lowAccuracyText" ng-click="canShowLowAccuracy=!canShowLowAccuracy" class="exceptionReport exceptionReportHeaderColorChangeRed">These students have attempted questions with low accuracy (<20%)</div>
                    <table ng-show="canShowLowAccuracy" class="table exceptionReportTable alignLeft" id="lowAccuracyTable">
                        <thead>
                            <tr>
                                <th> Name </th>
                                <th> Class/Section </th>
                                <th> Accuracy </th>
                                <th> Number of questions attempted </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="row in lowAccuracyData">
                                <td> {{row.childName}}</td>
                                <td> {{row.childClass + ' ' + row.childSection}}</td>
                                <td> {{row.accuracy}} %</td>
                                <td> {{row.totalQues}}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div ng-show="highAccuracyText" ng-click="canShowHighAccuracy=!canShowHighAccuracy" class="exceptionReport exceptionReportHeaderColorChangeGreen">These students have attempted questions with high accuracy (>80%)</div>
                    <table ng-show="canShowHighAccuracy" class="table exceptionReportTable alignLeft" id="highAccuracyTable">
                        <thead>
                            <tr>
                                <th> Name </th>
                                <th> Class/Section </th>
                                <th> Accuracy </th>
                                <th> Number of questions attempted </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="row in highAccuracyData">
                                <td> {{row.childName}}</td>
                                <td> {{row.childClass + ' ' + row.childSection}}</td>
                                <td> {{row.accuracy}}</td>
                                <td> {{row.totalQues}}</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div ng-show="notLoggedInText" ng-click="canShowNotLoggedIn=!canShowNotLoggedIn" class="exceptionReport">These students have not logged in {{notLoggedDateModeText}} ({{notLoggedInStartDate}} to {{notLoggedInEndDate}})</div>
                    <table ng-show="canShowNotLoggedIn" class="table exceptionReportTable alignLeft" id="notLoggedInTable">
                        <thead>
                            <tr>
                                <th> Name </th>
                                <th> Class/Section </th>
                                <th> Last Logged In On </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="row in notLoggedData">
                                <td> {{row.childName}}</td>
                                <td> {{row.childClass + ' ' + row.childSection}}</td>
                                <td> {{row.lastLoggedInDate}}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div ng-show="usageReportText" ng-click="canShowReport=!canShowReport" class="exceptionReport">Class Data Usage</div>
                    <div ng-show="noReport" class="noReport">No Data availabel for this selection</div>
                    <table ng-show="canShowReport" class="table alignLeft" id="usageReportTable">
                        <thead>
                            <tr>
                                <th> Name </th>
                                <th> Class </th>
                                <th width="15%"> Time Spent<br>(mins) </th>
                                <th> Accuracy </th>
                                <th> Content Attempted </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="row in reportData">
                                <td> {{row.childName}}</td>
                                <td> {{row.childClass + ' ' + row.childSection}}</td>
                                <td> {{row.timeTaken}}</td>
                                <td> {{row.accuracy}} %</td>
                                <td> {{row.contentAttempted}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div> -->
            </div>
                <!-- ********************************** -->
				
				
			</div>

        <!-- hoverable items that appear over almost everything-->
        <div id='loader' class="dots-loader"></div>
        <!--COMMENT SYSTEM START-->
        <div id="commentPanel" class="col-lg-6 col-md-8 none prompts" ng-controller="commentSystemController">
            <div class="prompt-heading">
            <div class="comnt-title">What's on your mind?</div>
                <button class="close-prompt close-comment" ng-click="closeCommentPanel()"> 
                    <i class="fa fa-close"></i>
                </button>
            </div>
            <!-- <div class="row" ng-hide="hideSubCat">
                <div class="comment_sys_header" ng-hide="hideSubCat">
                    <input type="button" ng-hide="hideSubCat" ng-c  lick="subCatBtn(subcat.commentSubCategoryID, subcat.subCategoryName)"  class="btn subCatBtns col-md-{{12/subCategories.length}}" ng-repeat="subcat in subCategories" id="sub_cat_{{subcat.commentSubCategoryID}}" value="{{subcat.subCategoryName}}"></input>
                </div>
            </div> -->
            <div class="row" style="margin-top: 9px;">
                <div class="col-md-3 col-sm-2">
                    <div class="comment_sys_cat">
                        <div class="arrow" id="div_{{row.commentCategoryID}}" ng-repeat="row in categories">
                            <input type="button"  class="form-control btn btn-sm catBtns" ng-click="getSubCategories(row.commentCategoryID, row.categoryName)" id="cat_{{row.commentCategoryID}}" value="{{row.categoryName}}"></input>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-4" style="top: 12px; padding-right: 15px;" ng-hide="hideSubCat" >
                    <!-- <div class="comment_sys_header" ng-hide="hideSubCat">
                        <input type="button" ng-hide="hideSubCat" ng-click="subCatBtn(subcat.commentSubCategoryID, subcat.subCategoryName)"  class="form-control btn subCatBtns btn-sm" ng-repeat="subcat in subCategories" id="sub_cat_{{subcat.commentSubCategoryID}}" value="{{subcat.subCategoryName}}"></input>
                    </div> -->
                    <input type="button" ng-hide="hideSubCat" ng-click="subCatBtn(subcat.commentSubCategoryID, subcat.subCategoryName)"  class="form-control btn subCatBtns btn-sm" ng-repeat="subcat in subCategories" id="sub_cat_{{subcat.commentSubCategoryID}}" value="{{subcat.subCategoryName}}"></input>
                    
                </div>
                <div class="col-md-6 col-sm-6">
                    <textarea id="comment" class="form-control" value="" rows=8 placeholder="Enter your comment here"> </textarea>
                </div>
            </div>
            <div class="row" style="margin-top:3px;">
                <button id='commentListShow' style="background-color: #b5b733 !important;" onclick="" class="btn btn-primary small-button viewCmtButton"> View Comments </button>
                <button id='submitButton' onclick="submitCommentCheck()" class="btn btn-primary small-button"> Submit </button>
            </div>
        </div>
        <!--COMMENT SYSTEM END-->
        <div id="instructions" class="none hovers">
            <div id="instructionsContainer">
                <img src="theme/img/Language/instructions.jpg">
            </div>
        </div>
        <div id="lookbackContent" class="hovers none"></div>
        <div id="lookbackContentQues" style="background: #1373a6 !important; width:50% !important;" class="hovers none"></div>
        <div id="lookbackContentComment" class="hovers none"></div>
        <div id="toast" class="hovers  none"> </div>
        <div id="audioLookback" class="hovers none">
            
        </div>
        <div id="modalBlocker" onclick="hideHovers()" class="ui-widget-overlay"></div>
        <div id="modalBlockerCommentSystem" class="ui-widget-overlay"></div>
        <div id="prompt" class="none"></div>
        <div class="footer"> © 2009-2018, Educational Initiatives Pvt. Ltd.</div>
    </div>
    
		<!-- will need later for feedback
        <iframe id="feedbackForm" class="none hovers" src="../application/views/Language/feedback.html" width="400" height="415"> </iframe>
        <button id="feedbackClose" class="closeButton" onclick="feedbackClose()"> x </button>-->

        <script type="text/javascript" src="theme/js/jquery-1.11.1.min.js" ></script>
        <script type="text/javascript" src="theme/js/bootstrap.js"  ></script>
        <script type="text/javascript" src="theme/js/jquery-ui.js"  ></script>
        <script type="text/javascript" src="theme/js/angular.min.js"  ></script>
        <script type="text/javascript" src="theme/js/angular-sanitize.min.js" ></script>

        <script type="text/javascript" src="theme/js/Language/helpers.js?23012018"  ></script>

        <script type="text/javascript" src="theme/js/rangy-core.js"  ></script>
        <script type="text/javascript" src="theme/js/rangy-textrange.js"  ></script>
    
		<script type="text/javascript" src="theme/js/jQuery.fastClick.js"  ></script>
       <!-- <script type="text/javascript" src="theme/js/elrte-1.3/js/elrte.min.js"  charset="utf-8"></script> -->
       <script type="text/javascript" src="theme/tinymce/tinymce.min.js"></script>
<!--    
        <script type="text/javascript" src="theme/js/Language/sessionReport.js"  ></script>
 -->

        <script type="text/javascript" src="theme/js/Language/passage.js?14092017"  ></script>

        <!-- <script type="text/javascript" src="theme/js/Language/templates/match.js?20022017"  ></script> -->
        <script type="text/javascript" src="theme/js/Language/audio.js?20022017"  ></script>

        <script type="text/javascript" src="theme/js/Language/question.js?10092017"  ></script>
        <script type="text/javascript" src='theme/js/skill-o-meter-1.1.js?ver=20170817'></script>
        <script type="text/javascript" src="theme/js/Language/app.js?23032018"  ></script>

        <script type="text/javascript" src="theme/js/Language/commentSystem.js?ver20012017"  ></script>
       
        <script type="text/javascript" src="theme/js/Language/rating.js?ver06122016"  ></script>
        <script type="text/javascript" src="theme/js/jquery.jplayer.min.js?ver2"  ></script>
        <script type="text/javascript" src="theme/js/jquery_ui_touch.js?ver2"  ></script>
        <script type="text/javascript" src="theme/js/jquery.jqplot.js?ver2"  ></script>
        <script type="text/javascript" src="theme/js/jqplot.pieRenderer.js?ver2"  ></script>
        <script type="text/javascript" src="theme/js/jqplot.barRenderer.js?ver2"  ></script>
        <script type="text/javascript" src="theme/js/jqplot.categoryAxisRenderer.js?ver2"  ></script>
        <script type="text/javascript" src="theme/js/jqplot.pointLabels.js?ver2"  ></script>
        <script type="text/javascript" src="theme/js/jcanvas/jcanvas.min.js?ver=20170608"></script>
        <script type="text/javascript" src="theme/js/Language/ei-charts.js?ver=20170621"></script>
        <script type="text/javascript" src="theme/js/d3charts/d3.min.js?ver=20170608"></script>
        <script type="text/javascript" src="theme/js/d3charts/d3pie.min.js?ver=20170627"></script>
        <script type="text/javascript" src="theme/js/ultimate-datatable-3.3.1.js?ver=20170621"></script>
        <script type="text/javascript" src="theme/js/FileSaver.js?ver=20170608"></script>
        <script type="text/javascript" src="theme/js/Language/teacher_interface.js?ver=20170817"  ></script>
        <script type="text/javascript" src="theme/js/bootstrap-tokenfield.js"></script>
        <script type="text/javascript" src="theme/js/Language/skillmeter.js?ver=20170724"  ></script>
        
        
    </body>
</html>
