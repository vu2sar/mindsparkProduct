<!DOCTYPE html>
<html data-ng-app="mindspark">
     <head>       
        <link rel="stylesheet" type="text/css" href="/mindspark/teacherInterface/css/jquery-ui.css">
        <link rel="stylesheet" type="text/css" href="/mindspark/teacherInterface/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="/mindspark/teacherInterface/css/bootstrap-material-design.min.css">
        <link rel="stylesheet" type="text/css" href="/mindspark/teacherInterface/css/ripples.min.css">
        
        <link rel="stylesheet" type="text/css" href="/mindspark/teacherInterface/co-teacher/co-teacher.css?ver=4">
        
        <script type="text/javascript" src="<?php echo HTML5_COMMON_LIB; ?>/angular-1.3.8.min.js"></script>
        <script type="text/javascript" src="<?php echo HTML5_COMMON_LIB; ?>/node_modules/angular-aria/angular-aria.min.js"></script>      
        <script type="text/javascript" src="<?php echo HTML5_COMMON_LIB; ?>/node_modules/angular-sanitize/angular-sanitize.min.js"></script>       
        <script type="text/javascript" src='<?php echo HTML5_COMMON_LIB; ?>/bootstrap.min.js'></script>
        <script type="text/javascript" src="<?php echo HTML5_COMMON_LIB; ?>/material.min.js"></script>
        <script type="text/javascript" src="<?php echo HTML5_COMMON_LIB; ?>/ripples.min.js"></script>
        <script type="text/javascript" src="<?php echo HTML5_COMMON_LIB; ?>/jcanvas.new.min.js"></script>
        <script type="text/javascript" src="/mindspark/teacherInterface/libs/jquery.flot.js"></script>
        <script type="text/javascript" src="/mindspark/teacherInterface/libs/jquery.flot.pie.js"></script>
        <script type="text/javascript" src="/mindspark/teacherInterface/co-teacher/requestUrl.js"></script>
        <script type="text/javascript" src="/mindspark/teacherInterface/co-teacher/script.js?ver=3"></script>
    </head>
<body>
    <div id="topicReport" class="wrapper container-fluid" ng-controller="TopicReport" ng-cloak>
        <div class="modal fade" id="whatsGoing" tabindex="-1" role="dialog" aria-labelledby="simpleModal">
            <div class="modal-fade"></div>
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title pull-left" id="myModalLabel-2">What's Going On</h4>
                        <button type="button" class="btn btn-dialog pull-right" data-dismiss="modal"><span class="close">&times;</span> Close</h4>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div ng-if="all_whats_going.positive.length == 0" class="no-positive-message">
                                    <div class="data-message">
                                        <h4>BRIGHT-SPARKS WAITING</h4>
                                        <p class="font-class">Great updates about your class are on the way.</p>
                                    </div>
                                </div>
                                <div ng-if="all_whats_going.positive.length > 0" class="message-box green-message" ng-repeat="data in all_whats_going.positive" ng-click="whatsGoingDetail(data)">
                                    <span class="ghost"></span>
                                    <span>{{ data.message }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div ng-if="all_whats_going.need_attention.length == 0" class="no-concern-message">
                                    <div class="data-message">
                                        <h4>NO CONCERNS!</h4>
                                        <p class="font-class">Currently there are no concerns over the learning of the topic in your class.</p>
                                    </div>
                                </div>
                                <div class="message-box red-message" ng-repeat="data in all_whats_going.need_attention" ng-click="whatsGoingDetail(data)">
                                    <span class="ghost"></span>
                                    <span>{{ data.message }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                    </div>
                </div><!-- modal-content -->
            </div><!-- modal-dialog -->
        </div><!-- modal -->
        <div class="modal fade" id="allProgressData" tabindex="-1" role="dialog" aria-labelledby="simpleModal">
            <div class="modal-fade"></div>
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title pull-left" id="myModalLabel-2">Progress Summary</h4>
                        <button type="button" class="btn btn-dialog pull-right" data-dismiss="modal"><span class="close">&times;</span> Close</h4>
                    </div>

                    <div class="modal-body">
                        <div class="col-md-4" ng-repeat="student in progress_summary.student_info">
                            <div class="progress-chart text-center">
                                <canvas  width="100" height="100" progress-chart="{{student.color}}" dash="0" sliceColor = "{{student.strokeColor}}" students="{{length(student.students)}}" start="{{student.start}}" end="{{student.end}}"></canvas>
                            </div>
                            <div class="student_information">
                                <div ng-repeat="(key,info) in student.students">
                                    <div class="md-list-item-text">
                                        <div ng-click="openStudentTrail(key,'topic','','')">{{ info.name }}</div>
                                        <div style="color:{{student.color}}">{{ info.progress }}%</div>
                                        <student-progress data="{{ info.progress }}" ng-click="openStudentTrail(key,'topic','','')"></student-progress>
                                    </div>
                                    <span class="line-break"></span>
                                    
                                </div>
                                
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                    </div>
                </div><!-- modal-content -->
            </div><!-- modal-dialog -->
        </div><!-- modal -->
         <div class="modal fade" id="allClassAccuracy" tabindex="-1" role="dialog" aria-labelledby="simpleModal">
          <div class="modal-fade"></div>
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title pull-left" id="myModalLabel-2">Class Accuracy</h4>
                        <button type="button" class="btn btn-dialog pull-right" data-dismiss="modal"><span class="close">&times;</span> Close</h4>
                    </div>

                    <div class="modal-body">
                        <div class="col-md-3" ng-repeat="student in class_accuracy.student_info">
                            <div class="progress-chart text-center">
                                <canvas  width="100" height="100" progress-chart="{{student.color}}" dash="0" students="{{length(student.students)}}" sliceColor = "{{student.strokeColor}}" start="{{student.start}}" end="{{student.end}}"></canvas>
                            </div>
                            <div class="student_information">
                                <div ng-repeat="(key,info) in student.students">
                                    <div class="md-list-item-text">
                                        <div ng-click="openStudentTrail(key,'assessment',info.id,student.type)" >{{ info.name }}</div>
                                        <div style="color:{{student.color}}" ng-if="student.type !='Incompleted'">{{ info.accuracy }}%</div>
                                        <div style="color:{{student.color}}" ng-if="student.type =='Incompleted'"> - </div>
                                        <student-progress data="{{ info.accuracy }}" ng-click="openStudentTrail(key,'assessment',info.id,student.type)" ></student-progress>
                                    </div>
                                    <span class="line-break"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                    </div>
                </div><!-- modal-content -->
            </div><!-- modal-dialog -->
        </div><!-- modal -->
        <div class="row">
            <div class="col-md-12">
                <div class="topic-header" ng-show="topic_info.topicDetails">
                    <span class="topic-header-detail"><span>Topic : </span><span class="bold-header"> {{ topic_info.topicDetails.name }}</span></span>
                    <span class="topic-header-detail"><span>Class : </span> <span class="bold-header">{{ initialObj.cls + '' + initialObj.section }}</span></span>
                    <span class="topic-header-detail"><span>{{ topic_info.topicDetails.message1 }}</span><span class="bold-header">{{ topic_info.topicDetails.message2 }}</span></span>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12 ">
                <div ng-show="assessment_flag==1 && class_accuracy_flag == 1 && time_to_complete_flag==1 && progress_summary_flag==1" class="row">
                    <topic-progress topic="topic_progress" class="col-md-4" ng-if="topic_progress"></topic-progress>                     
                    <assessment-completed data="assessment_data"  class="col-md-4" ng-if="assessment_data"></assessment-completed> 
                    
                    <time-to-complete data="time_to_complete" class="col-md-4" ng-if="time_to_complete"></time-to-complete>                                    
                </div>
                <div ng-show="assessment_flag==0 && class_accuracy_flag == 1 && time_to_complete_flag==1 && progress_summary_flag==1" class="row">
                    <topic-progress topic="topic_progress"  class="col-md-6" ng-if="topic_progress"></topic-progress>
                    <time-to-complete data="time_to_complete" class="col-md-6" ng-if="time_to_complete"></time-to-complete>                                      
                </div>
                <div class="header-loader" ng-hide="class_accuracy_flag == 1 && time_to_complete_flag==1 && progress_summary_flag==1"></div>
            </div>
        </div>
        <br>
        <div>
            <div id="reports-tabs">
                <ul>
                    <li id="progressReportTab"><a href="#tabs-1" ng-click="pageTrack('progressReport')" >PROGRESS REPORT</a></li>
                    <li id="assessmentReportTab" ng-show="assessment_flag" ><a href="#tabs-2" ng-click="pageTrack('assessmentReport')">ASSESSMENT REPORT</a></li>
                </ul>
                <div id="tabs-1" class="row">
                    <div class="col-md-12">
                        <div class="row sticky">
                            <div class="col-md-3 text-center">
                                <button class="btn md-hue-1 report-button progress_summary_button active" ng-click="autoScroll('details_section|progress_summary|progressSummary')">Progress Summary</button>
                            </div>
                            <div class="col-md-3 text-center">
                                <button class="btn md-hue-1 report-button whats_going_on_button" ng-click="autoScroll('details_section|whats_going_on|whatsGoingOn')">What's Going On</button>
                            </div>
                            <div class="col-md-3 text-center">
                                <button class="btn md-hue-1 report-button common_wrong_answer_button" ng-click="autoScroll('details_section|common_wrong_answer|CWA')">Common Wrong Answers</button>
                            </div>
                            <div class="col-md-3 text-center">
                                <button class="btn md-hue-1 report-button learning_unit_button" ng-click="autoScroll('details_section|learning_unit|learningUnitSummary')">Learning Unit Summary</button>
                            </div>                                             
                        </div>
                        <div class="details_section">
                            <div class="progress_summary row" >
                                <div class="col-md-12">
                                    <h4 >Progress Summary</h4> 
                                    <div >                                       
                                        <div class="chart col-md-4">
                                            <div id="chart" class="donut-hack" style="width:300px;height:250px;"></div>
                                            <div id="chart_ring" class="donut-hack" style="width:300px;height:250px;"></div>
                                        </div>
                                        <div class="progress_info col-md-8" >
                                            <div class="row">
                                                <div class="md-dense col-md-4" ng-repeat="student in progress_summary.student_info">
                                                    <div class="md-no-sticky"></div>
                                                    <div class="list-header">
                                                        <span class="header">
                                                            {{ length(student.students) }}
                                                        </span> Students
                                                        <div class="sub-header" style="color:{{ student.color }}">
                                                            {{ student.range }}
                                                        </div>
                                                    </div>
                                                    <div ng-repeat="(key, info) in student.students">
                                                        <div class="md-list-item-text" ng-if="($index) < limit_student_data">
                                                            <div ng-click="openStudentTrail(key,'topic','','')">{{ info.name }}</div>
                                                            <div style="color:{{student.color}}">{{ info.progress }}%</div>
                                                            <student-progress data="{{ info.progress }}" ng-click="openStudentTrail(key,'topic','','')"></student-progress>
                                                        </div>
                                                        <span class="line-break"></span>
                                                        
                                                    </div>
                                                    <div class="text-center">
                                                        <button class="{{student.type}}" ng-if="(length(student.students) - limit_student_data) > 0" data-toggle="modal" data-target="#allProgressData"> + {{ length(student.students) - limit_student_data }} More</button>
                                                    </div>
                                                </div>
                                                 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="details-loader" ng-show="progress_summary_flag == 0"></div>
                                </div>
                            </div>
                            <div class="whats_going_on row" >
                                <div class="col-md-12">
                                 <div class="header">
                                    <span class="ghost"></span>
                                    <h4 class="pull-left">What's Going On</h4>
                                    <h5 class="pull-right link" ng-click="getAllWhatsGoing()" data-toggle="modal" data-target="#whatsGoing" ng-if="all_whats_going.positive.length > 2 || all_whats_going.need_attention.length > 2">
                                    SEE ALL</h5>
                                    </div>
                                </div>
                                <div class="col-md-12" >
                                    <div ng-if="all_whats_going.positive.length == 0 && all_whats_going.need_attention.length == 0">
                                        <div class="no-data row col-md-12">
                                            <div class="col-md-4 text-center">
                                                <img src="assets/co-teacher/no_whats_new.png"/>
                                            </div>
                                            <div class="col-md-8 text-center">
                                                <div class="empty-message text-left">
                                                    <h3>CLASS UPDATES</h3>
                                                    <p>
                                                        You will get updpates on what your class has been doing in Mindspark.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" ng-if="all_whats_going.positive.length > 0 || all_whats_going.need_attention.length > 0">
                                        <div class="col-md-6">
                                            <div ng-if="all_whats_going.positive.length == 0" class="no-positive-message-preview">
                                                <div class="data-message-preview">
                                                    <h4>BRIGHT-SPARKS WAITING</h4>
                                                    <p class="font-class">Great updates about your class are on the way.</p>
                                                </div>
                                            </div>
                                            <div class="message-box green-message" ng-repeat="data in all_whats_going.positive" ng-if="($index) < 2" ng-click="whatsGoingDetail(data)">
                                                <span class="ghost"></span>
                                                <span>{{ data.message }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div ng-if="all_whats_going.need_attention.length == 0" class="no-concern-message-preview">
                                                <div class="data-message-preview">
                                                    <h4>NO CONCERNS!</h4>
                                                    <p class="font-class">Currently there are no concerns over the learning of the topic in your class.</p>
                                                </div>
                                            </div>
                                            <div class="message-box red-message" ng-repeat="data in all_whats_going.need_attention" ng-if="($index) < 2" ng-click="whatsGoingDetail(data)">
                                                <span class="ghost"></span>
                                                <span>{{ data.message }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="details-loader" ng-show="whats_going_on_flag == 0"></div>
                                </div>
                            </div>
                            <div class="common_wrong_answer row" >
                                <div class="col-md-12">
                                    <div class="header">
                                        <span class="ghost"></span>
                                        <h4>Common Wrong Answer ({{common_wrong_answer.length}})</h4>
                                        <span class="header-extension"></span>
                                        
                                        <button class="btn btn-default download" ng-click="downloadData()" ng-if="common_wrong_answer.length > 0">
                                        <img src="assets/co-teacher/Download.svg">DOWNLOAD</button>
                                        <form id="frmDownloadCWA" method="POST" target="_blank" action="downloadCWA.php" ng-if="common_wrong_answer.length > 0">
                                        <input  type="hidden" name="qcodeStr" id="qcodeStr" value="{{downloadStr}}"/>
                                        <input  type="hidden" name="class" value="{{initialObj.cls}}"/>
                                        <input  type="hidden" name="section" value="{{initialObj.section}}"/>
                                        <input  type="hidden" name="ttCode" value="{{initialObj.ttCode}}"/>
                                        <input  type="hidden" name="totalQues" id="totalQues" value="{{common_wrong_answer.length}}"/>
                                        <input  type="hidden" name="animationQues" id="animationQues" value="{{animationQues}}"/>
                                        </form>
                                    </div>
                                    <div class="col-md-12" ng-if="common_wrong_answer.length == 0" >
                                        <div class="no-data row col-md-12">
                                            <div class="col-md-6 text-center">
                                                <img src="assets/co-teacher/no_data.png"/>
                                            </div>
                                            <div class="col-md-6 text-center">
                                                <div class="empty-message text-left">
                                                    <h3>ALL CLEAN !</h3>
                                                    <p>Looks like your students have a great understanding in this topic.</p>
                                                    <p>No common mistakes found !</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div ng-if="common_wrong_answer.length > 0" >
                                        <div class="col-md-12 question_data loader-frame" id="{{'questionArea' + ($index + 1)}}"  ng-repeat="question in common_wrong_answer" on-finish-render="ngRepeatFinished" >
                                            <div id="{{'question_data'+($index + 1)}}">
                                                <span class="question-no">{{ ($index + 1) }}</span>
                                                <iframe id="{{'questionFrame'+($index + 1)}}" src="{{'/mindspark/teacherInterface/topicReportController.php?mode='+ question.mode + '&quesDetails=' + question.qcodeListData }}" class="frame-element " onload="finished(this)"></iframe>
                                                <div class="common-wrong-answer-modal"></div>
                                                <div class="misconception_flag" ng-if="question.misconception != '' ">
                                                    <button class="flag-icon">
                                                        <img src="assets/redflag.ico"/>
                                                    </button>
                                                    <span class="misconception_detail">   
                                                     <span class="misconception_label">Misconception: </span> {{ question.misconception }}
                                                    </span>

                                                </div>
                                                <div class="question_details">
                                                    <div class="arrow" ng-click="slideDetails()">
                                                        <<
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="class-performance">
                                                            <h4>Class Performance</h4>
                                                            <span class="per-percent">{{ question.classWisePerformance }}%</span>
                                                            <div ng-if="question.failedStudentList.length > 0" class="highlight-parent">
                                                                <a tabindex="0"
                                                                   class="highlight" 
                                                                   role="button" 
                                                                   data-html="true" 
                                                                   data-toggle="popover"
                                                                   data-trigger="focus" 
                                                                   data-placement="left"
                                                                   data-content="{{question.failedList}}">{{ question.students_failed }} student(s) who did it wrong </a>
                                                            </div>
                                                        </div>
                                                        <div class="school-performance">
                                                            <h4>School Performance</h4>
                                                            <span class="per-percent">{{ question.schoolAVG }}%</span>
                                                        </div>
                                                        <div class="national-performance">
                                                            <h4>National Performance</h4>
                                                            <span class="per-percent">{{ question.nationalAVG }}%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="col-md-12 question-navigation">
                                            <span class="left-button" ng-show="question_index != 0" ng-click="showPrev()">
                                                <
                                            </span>
                                            <div class="previewArea">
                                                    <span class="question-preview" id="{{ 'Q'+($index+1) }}" ng-repeat="preview in common_wrong_answer" data="{{'questionArea' + ($index + 1)}}" data-active="{{'highlightFrameCWA'+($index + 1)}}" onclick="highlightPreview(this)">
                                                        <div class="iframe-modal" ng-class="{'active' : ($index + 1) == 1 }"></div>
                                                        <div class="ques-preview">
                                                            <iframe id="{{'questionFrame'+($index + 1)}}" src="{{'/mindspark/teacherInterface/topicReportController.php?mode='+ preview.mode + '&quesDetails=' + preview.qcodeListData }}" class="small-frame-element scale-down"  scrolling="no"></iframe>
                                                        </div>
                                                        <div class="question-number" id="{{'QCWA'+($index + 1)}}">
                                                            {{ "Q" + ($index+1) }}
                                                        </div>
                                                        
                                                            <div class="activate-preview" id="{{'highlightFrameCWA'+($index + 1)}}"></div> 
                                                       
                                                    </span>
                                            </div>
                                            <span class="right-button" ng-show="question_index < (common_wrong_answer.length - 5)" ng-click="showNext()">
                                                >
                                            </span>
                                        </div>
                                    </div>
                                    <div class="details-loader" ng-show="cwa_flag == 0"></div>
                                </div>
                                
                            </div>
                            <div class="learning_unit row" >
                                <div class="col-md-12">
                                    <div class="header">
                                        <span class="ghost"></span>
                                        <h4>Learning Unit Summary</h4>
                                        <span class="header-extension"></span>
                                    </div>
                                    <div class="details-loader" ng-show="lu_summary_flag == 0"></div>
                                    <div class="learning row" ng-repeat="data in learning_unit" on-finish-render="ngRepeatFinished">
                                        <div class="checkbox col-md-1 text-right display-table">
                                            <div class="ghost">
                                            </div>
                                            <label class="middle">
                                              <span class="checkbox-material"><button class="check" ng-class="{'check-mark' : data.tick != 0 }" data-toggle="tooltip" data-placement="right" title="" data-original-title="{{data.tooltip}}" data-trigger="focus"></button></span>
                                            </label>
                                            <div class="learning-points"></div>
                                        </div>
                                        <div class="learning-list col-md-11 display-table">
                                            <div class="middle font-class" style="width:75%">
                                                <span>{{data.heading}}</span>
                                            </div>
                                            <div class="middle" style="width:10%">
                                                <canvas width="50" height="50" progress-chart="{{data.progress.value}}" dash="{{data.progress.dash}}" ng-class="{'chart-tooltip' : data.progress.dash == 1}" data-toggle="tooltip" data-placement="left" title="" data-original-title="{{data.progress.tooltip}}"></canvas>
                                                
                                            </div>
                                            <div class="middle" style="width:15%">
                                                <span ng-if="data.progress.dash == 0" class="font-class">{{data.label}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            
                            </div>
                        </div>
                    </div>
                </div>
                <div id="tabs-2" class="row" ng-show="assessment_flag">
                    <div class="col-md-12">
                        <div class="row sticky">
                            <div class="col-md-3 text-center">
                                <button class="btn md-hue-1 report-button active class_accuracy_button" ng-click="autoScroll('assessment_details|class_accuracy|classAccuracy')">Class Accuracy</button>
                            </div>
                            <div class="col-md-3 text-center">
                                <button class="btn md-hue-1 report-button class_discussion_button" ng-click="autoScroll('assessment_details|class_discussion|questionForClassDiscussion')">Questions for Class Discussion</button>
                            </div>
                        </div>
                        <div class="assessment_details col-md-12">
                            <div class="class_accuracy row">
                            <div class="col-md-12">
                                <div class="header row">                                    
                                    <h4 >Class Accuracy</h4>
                                    <span class="header-extension"></span>
                                </div>
                                <div ng-if="length(class_accuracy.student_info) == 0" class="col-md-12">
                                    <div class="no-data row col-md-12">
                                        <div class="col-md-4 text-center">
                                            <img src="assets/co-teacher/no_accuracy.png"/>
                                        </div>
                                        <div class="col-md-8 text-center">
                                            <div class="empty-message text-left">
                                                <h3>Not THERE YET !</h3>
                                                <p>
                                                    No student has completed the assessment yet.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" ng-if="length(class_accuracy.student_info) > 0">
                                    <div class="col-md-12 space-bottom">
                                        <div class="col-md-12">
                                            <div class="{{ avg_accuracy_highlight }}">
                                                <span class="class_accuracy_progress">{{avg_accuracy}}</span><span>% Accuracy</span>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="chart col-md-4">
                                        <div id="accuracyChart" class="donut-hack" style="width:300px;height:250px;"></div>
                                        <div id="accuracyChart_ring" class="donut-hack" style="width:300px;height:250px;"></div>
                                    </div>
                                    <div class="progress_info col-md-8">
                                        <div class="row">
                                            <div class="md-dense col-md-4" ng-repeat="student in class_accuracy.student_info" ng-if="student.type != 'Incompleted'">
                                                <div class="md-no-sticky"></div>
                                                <div class="list-header">
                                                    <span class="header">
                                                        {{ length(student.students) }}
                                                    </span> Students
                                                    <div class="sub-header" style="color:{{ student.color}}">
                                                        <span class="uppercase">{{ student.type }}</span> {{ student.range }}
                                                    </div>
                                                </div>

                                                <div ng-repeat="(key,info) in student.students">
                                                    <div class="md-list-item-text" layout="column" ng-if="($index) < limit_student_data">
                                                        <div ng-click="openStudentTrail(key,'assessment',info.id,student.type)" >{{ info.name }}</div>
                                                        <div style="color:{{student.color}}">{{ info.accuracy }}%</div>
                                                        <student-progress data="{{ info.accuracy }}" ng-click="openStudentTrail(key,'assessment',info.id,student.type)" ></student-progress>
                                                    </div>
                                                    <span class="line-break"></span>
                                                    
                                                </div>
                                                <div class="text-center">
                                                    <button class="{{student.type}}" ng-if="(length(student.students) - limit_student_data) > 0" data-toggle="modal" data-target="#allClassAccuracy"> + {{ length(student.students) - limit_student_data }} More</button>
                                                </div>
                                            </div>
                                             
                                        </div>
                                    </div>
                                </div>
                                <div class="details-loader" ng-show="class_accuracy_flag == 0"></div>
                            </div>
                            <div class="class_discussion row col-md-12">
                                <div class="col-md-12">
                                    <div class="header row">
                                        <span class="ghost"></span>
                                        <h4>Question for class discussion</h4>
                                        <span class="header-extension"></span>
                                        <button class="btn btn-default download" ng-click="downloadAssessmentData()" ng-if="critical_data.length+recommended_data.length+performed_data.length > 0">
                                        <img src="assets/co-teacher/Download.svg">DOWNLOAD</button>
                                        <form id="frmDownloadAssessment" method="POST" target="_blank" action="downloadCWA.php" ng-if="critical_data.length+recommended_data.length+performed_data.length > 0">
                                            <input  type="hidden" name="qcodeStr" id="qcodeStr" value="{{downloadStrAssessement}}"/>
                                            <input  type="hidden" name="class" value="{{initialObj.cls}}"/>
                                            <input  type="hidden" name="section" value="{{initialObj.section}}"/>
                                            <input  type="hidden" name="ttCode" value="{{initialObj.ttCode}}"/>
                                            <input  type="hidden" name="totalQues" id="totalQuesAssessment" value="{{critical_data.length+recommended_data.length+performed_data.length}}"/>
                                            <input  type="hidden" name="animationQues" id="animationQuesAssessment" value="{{animationQuesAssessement}}"/>
                                        </form>
                                    </div>
                                    <div ng-show="class_discussion_flag == 1">
                                        <div class="discussion_tab row">
                                            <span class="equal-width col-md-4 active" ng-click="showDiscussion($event,'critical')">
                                                Critical(<40%)
                                            </span>
                                            <span class="equal-width col-md-4" ng-click="showDiscussion($event,'recommended')">
                                                Recommended(40-80%)
                                            </span>
                                            <span class="equal-width col-md-4" ng-click="showDiscussion($event,'performed')">
                                                Performed Well(80-100%)
                                            </span>
                                        </div>
                                        <div class="critical row" ng-show="critical">
                                            <div class="col-md-12" ng-if="critical_data.length == 0">
                                                <div class="no-data col-md-12">
                                                    <div class="col-md-6 text-center">
                                                        <img src="assets/co-teacher/no_data.png"/>
                                                    </div>
                                                    <div class="col-md-6 text-center">
                                                        <div class="empty-message text-left">
                                                            <h3>ALL CLEAN !</h3>
                                                            <p>Looks like your students have a great understanding in this topic as of now. No critical questions found for class discussion !</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div ng-if="critical_data.length > 0">
                                                <div class="col-md-12 question_data loader-frame question-section" id="{{'questionArea' + ($index + 1)}}"  ng-repeat="question in critical_data">
                                                    <div id="{{'question_data'+($index + 1)}}">
                                                    <span class="question-no">{{ ($index + 1) }}</span>                                                
                                                        <iframe id="{{'questionFrame'+($index + 1)}}" src="{{'/mindspark/teacherInterface/topicReportController.php?mode='+ question.mode + '&quesDetails=' + question.qcodeListData }}" class="frame-element " ng-show="critical" onload="finished(this)" ></iframe>
                                                        <div class="common-wrong-answer-modal"></div>                                                
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="col-md-12 question-navigation">
                                                    <span class="left-button" ng-show="question_index_critical != 0" ng-click="showPrevCritical()">
                                                        <
                                                    </span>
                                                    <div class="previewArea">
                                                            <span class="question-preview" id="{{ 'Q'+($index+1) }}" ng-repeat="preview in critical_data" data="{{'questionArea' + ($index + 1)}}" data-active="{{'highlightFrameCritical'+($index + 1)}}" onclick="highlightPreview(this)">
                                                                <div class="iframe-modal" ng-class="{'active' : ($index + 1) == 1 }"></div>
                                                                <div class="ques-preview">
                                                                    <iframe id="{{'questionFrame'+($index + 1)}}" src="{{'/mindspark/teacherInterface/topicReportController.php?mode='+ preview.mode + '&quesDetails=' + preview.qcodeListData }}" class="small-frame-element scale-down"  scrolling="no"></iframe>
                                                                </div>
                                                                <div class="question-number" id="{{'QCritical'+($index + 1)}}">
                                                                    {{ "Q" + ($index+1) }} | {{ preview.accuracy }}% Correct
                                                                </div>
                                                                <div class="activate-preview" id="{{'highlightFrameCritical'+($index + 1)}}"></div> 
                                                            </span>
                                                    </div>
                                                    <span class="right-button" ng-show="question_index_critical < (critical_data.length - 5)" ng-click="showNextCritical()">
                                                        >
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="recommended row" ng-show="recommended">
                                            <div class="col-md-12" ng-if="recommended_data.length == 0">
                                                <div class="no-data col-md-12">
                                                    <div class="col-md-6 text-center">
                                                        <img src="assets/co-teacher/no_data.png"/>
                                                    </div>
                                                    <div class="col-md-6 text-center">
                                                        <div class="empty-message text-left">
                                                            <h3>ALL CLEAN !</h3>
                                                            <p>No recommended questions found for class discussion !</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div ng-if="recommended_data.length > 0">
                                                <div class="col-md-12 question_data loader-frame question-section" id="{{'questionArea' + ($index + 1)}}"  ng-repeat="question in recommended_data" >
                                                    <div id="{{'question_data'+($index + 1)}}">
                                                        <span class="question-no">{{ ($index + 1) }}</span>
                                                        <iframe id="{{'questionFrame'+($index + 1)}}" src="{{'/mindspark/teacherInterface/topicReportController.php?mode='+ question.mode + '&quesDetails=' + question.qcodeListData }}" class="frame-element " ng-show="recommended"  onload="finished(this)"></iframe>
                                                        <div class="common-wrong-answer-modal"></div>
                                                        
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="col-md-12 question-navigation">
                                                    <span class="left-button" ng-show="question_index_recommended != 0" ng-click="showPrevRecommended()">
                                                        <
                                                    </span>
                                                    <div class="previewArea">
                                                        <span class="question-preview" id="{{ 'Q'+($index+1) }}" ng-repeat="preview in recommended_data" data="{{'questionArea' + ($index + 1)}}" data-active="{{'highlightFrameRecomended'+($index + 1)}}" onclick="highlightPreview(this)">
                                                            <div class="iframe-modal" ng-class="{'active' : ($index + 1) == 1 }"></div> 
                                                            <div class="ques-preview">
                                                                <iframe id="{{'questionFrame'+($index + 1)}}" src="{{'/mindspark/teacherInterface/topicReportController.php?mode='+ preview.mode + '&quesDetails=' + preview.qcodeListData }}" class="small-frame-element scale-down"  scrolling="no"></iframe>
                                                            </div>
                                                            <div class="question-number" id="{{'QRecomended'+($index + 1)}}">
                                                                {{ "Q" + ($index+1) }} | {{ preview.accuracy }}% Correct
                                                            </div>
                                                            <div class="activate-preview" id="{{'highlightFrameRecomended'+($index + 1)}}"></div> 
                                                        </span>
                                                    </div>
                                                    <span class="right-button" ng-show="question_index_recommended < (recommended_data.length - 5)" ng-click="showNextRecommended()">
                                                        >
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="performed_well row" ng-show="performed">
                                            <div class="col-md-12" ng-if="performed_data.length == 0">
                                                <div class="no-data col-md-12">
                                                    <div class="col-md-6 text-center">
                                                        <img src="assets/co-teacher/no_data.png"/>
                                                    </div>
                                                    <div class="col-md-6 text-center">
                                                        <div class="empty-message text-left">
                                                            <h3>YET TO REACH !</h3>
                                                            <p>We have not found any questions where students have performed well.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div ng-if="performed_data.length > 0">
                                                <div class="col-md-12 question_data loader-frame question-section" id="{{'questionArea' + ($index + 1)}}"  ng-repeat="question in performed_data">
                                                    <div id="{{'question_data'+($index + 1)}}">
                                                        <span class="question-no">{{ ($index + 1) }}</span>                                                     
                                                        <iframe id="{{'questionFrame'+($index + 1)}}" src="{{'/mindspark/teacherInterface/topicReportController.php?mode='+ question.mode + '&quesDetails=' + question.qcodeListData }}" class="frame-element " ng-show="performed" onload="finished(this)"></iframe>
                                                        <div class="common-wrong-answer-modal"></div>                                                
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="col-md-12 question-navigation">
                                                    <span class="left-button" ng-show="question_index_performed != 0" ng-click="showPrevPerformed()">
                                                        <
                                                    </span>
                                                    <div class="previewArea">
                                                            <span class="question-preview" id="{{ 'Q'+($index+1) }}" ng-repeat="preview in performed_data" data="{{'questionArea' + ($index + 1)}}" data-active="{{'highlightFramePerformed'+($index + 1)}}" onclick="highlightPreview(this)">
                                                                <div class="iframe-modal" ng-class="{'active' : ($index + 1) == 1 }"></div>
                                                                <div class="ques-preview">
                                                                    <iframe id="{{'questionFrame'+($index + 1)}}" src="{{'/mindspark/teacherInterface/topicReportController.php?mode='+ preview.mode + '&quesDetails=' + preview.qcodeListData }}" class="small-frame-element scale-down"  scrolling="no"></iframe>
                                                                </div>
                                                                <div class="question-number" id="{{'QPerformed'+($index + 1)}}">
                                                                    {{ "Q" + ($index+1) }} | {{ preview.accuracy }}% Correct
                                                                </div>
                                                                <div class="activate-preview" id="{{'highlightFramePerformed'+($index + 1)}}"></div> 
                                                            </span>
                                                    </div>
                                                    <span class="right-button" ng-show="question_index_performed < (performed_data.length - 5)" ng-click="showNextPerformed()">
                                                        >
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="details-loader" ng-show="class_discussion_flag == 0"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    <form target='window.open' name="mindsparkTeacherLogin" id="mindsparkTeacherLogin" action="" method="post">
        <input type="hidden" name="mode" id="mode" value="">        
        <input type="hidden" name="childClass" id="childClass" >
        <input type="hidden" name="userType" id="userType" value="teacherAsStudent">        
        <input type="hidden" name="ttCode" id="ttCode" >
        <input type="hidden" name="clusterCode" id="clusterCode" >        
    </form>
</body>



</html>