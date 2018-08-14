$(document).ready(function(){
    $('.highlight').popover();
    $("#reports-tabs").tabs({
        activate: function( event, ui ) {
            if(ui.newTab[0].id == "assessmentReportTab"){
                angular.element(document.getElementById('topicReport')).scope().getAssessmentChart();
            }
        }
    });    
    $.material.init();   
});
var imagesPath = "/mindspark/teacherInterface/assets/co-teacher/";
var common_left = 0;
var common_left_critical = 0;
var common_left_recommended = 0;
var common_left_performed = 0;
var miscon = 0;
    angular.module('mindspark', [
        'ngAria',        
        'ngSanitize',       
    ]).controller('TopicReport',['$scope', '$sce','$http','TopicReportService', function($scope, $sce,$http,TopicReportService){
        // Variables
        $scope.limit_student_data = 2;

        $scope.student_remain = 4;
        $scope.question_index = 0;
        $scope.question_index_critical = 0;
        $scope.question_index_recommended = 0;
        $scope.question_index_performed = 0;
        
        $scope.open = true;
        
        $scope.length = function(sourceObj){
            if(sourceObj != undefined || sourceObj != null)
            {
                if(Object.keys(sourceObj).length < 10)
                    return '0'+ Object.keys(sourceObj).length;
                else
                    return Object.keys(sourceObj).length;                
            }
            else
                return 0;
        }
        $scope.go = function(){                 
            $scope.topic_progress = '';
            $scope.assessment_data = ''
            $scope.time_to_complete = '';
            $scope.goInitParams();            
            $scope.init();
            $( "#reports-tabs" ).tabs();
            $("#topicReport").show();
        };
        $scope.preinit = function(){
            $scope.initializeParameters();                
            $scope.init();              
        };
        $scope.getURLParameters = function() {
            var parameters = new Object();
            var id = document.URL.indexOf('?');
            if (id != -1) {
            var keyValuePair = document.URL.substring(id+1, document.URL.length).split('&');
            for (var i=0; i<keyValuePair.length; i++) {
                keyValue = keyValuePair[i].split('=');
                parameters[keyValue[0]] = decodeURIComponent((keyValue[1]+'').replace(/\+/g, '%20'));
                }
            }            
            return parameters;
        };    
        $scope.init = function(){             
            $scope.progress_summary_flag = 0;                      
            $scope.whats_going_on_flag = 0;                      
            $scope.cwa_flag = 0;                      
            $scope.lu_summary_flag = 0;                      
            $scope.class_accuracy_flag = 0; 
            $scope.class_discussion_flag = 0;
            $scope.time_to_complete_flag = 0; 
            $scope.progressPageTrackFlag=0;
            $scope.whatsGoingOnPageTrackFlag=0;
            $scope.CWAPageTrackFlag=0;
            $scope.LUSummaryPageTrackFlag=0;
            $scope.classAccuracyPageTrackFlag=0;
            $scope.assessmentReportPageTrackFlag=0;                      
            $scope.attachEvent();
            $scope.getTopicInfo();
            $scope.getAssessment();                              
            $scope.getTimeToComplete();            
            $scope.getTopicProgress();            
            $scope.whatsGoingOn();
            $scope.commonWrongAnswer();
            $scope.learningUnit();    
            $scope.classDiscussion();   
           

        };
        $scope.showFailedPopover = function(event,failedList){
            $(event.target).attr('data-content',html);
            //$(event.target).popover();
        };
        $scope.attachEvent = function(){
            $(".report-button").click(function(){
                $(".report-button").removeClass('active');
                $(this).addClass('active');
            }); 
            $(window).scroll(function () {
                var top = $("#reports-tabs").position().top - $(window).scrollTop();
                if(top < -180)
                {
                    $(".sticky").addClass('stick');
                }
                else
                {   
                    $(".sticky").removeClass('stick');
                }    

                $(".report-button").removeClass('active');                
                if((($(".progress_summary").offset().top - 65) - $(window).scrollTop()) < 15 && (($(".progress_summary").offset().top - 65) - $(window).scrollTop()) > -265){
                    $(".progress_summary_button").addClass('active');
                    $(".progress_summary_button").focus();
                    if(!$scope.progressPageTrackFlag)
                    {
                        $scope.progressPageTrackFlag =1;
                        $scope.pageTrack('progressSummary');
                    }
                }
                else if((($(".whats_going_on").offset().top - 65) - $(window).scrollTop()) < 15 && (($(".whats_going_on").offset().top - 65) - $(window).scrollTop()) > -175)
                {
                    $(".whats_going_on_button").addClass('active');
                    $(".whats_going_on_button").focus();  
                    if(!$scope.whatsGoingOnPageTrackFlag)
                    {
                        $scope.whatsGoingOnPageTrackFlag =1;                              
                        $scope.pageTrack('whatsGoingOn');
                    }
                }
                else if((($(".common_wrong_answer").offset().top - 65) - $(window).scrollTop()) < 15 && (($(".common_wrong_answer").offset().top - 65) - $(window).scrollTop()) > -615)
                {
                    $(".common_wrong_answer_button").addClass('active');
                    $(".common_wrong_answer_button").focus();
                    if(!$scope.CWAPageTrackFlag)
                    {
                        $scope.CWAPageTrackFlag =1;
                        $scope.pageTrack('CWA');
                    }
                }
                else if((($(".learning_unit").offset().top - 65) - $(window).scrollTop()) < 15)
                {
                    $(".learning_unit_button").addClass('active');
                    $(".learning_unit_button").focus();
                    if(!$scope.LUSummaryPageTrackFlag)
                    {
                        $scope.LUSummaryPageTrackFlag =1;
                        $scope.pageTrack('learningUnitSummary');
                    }
                }
                $(".report-button").removeClass('active');
                if((($(".assessment_details").offset().top - 65) - $(window).scrollTop()) < 15 && (($(".assessment_details").offset().top - 65) - $(window).scrollTop()) > -350 ) {
                    $(".class_accuracy_button").addClass('active');
                    $(".class_accuracy_button").focus();
                    if(!$scope.classAccuracyPageTrackFlag)
                    {
                        $scope.classAccuracyPageTrackFlag =1;
                        $scope.pageTrack('classAccuracy');
                    }
                }
                else if((($(".class_discussion").offset().top - 65) - $(window).scrollTop()) < 15){
                    $(".class_discussion_button").addClass('active');
                    $(".class_discussion_button").focus();
                    if(!$scope.assessmentReportPageTrackFlag)
                    {
                        $scope.assessmentReportPageTrackFlag =1;
                        $scope.pageTrack('questionForClassDiscussion');
                    }
                }
            });           
            $(document).on('click',function(e){
                if(document.activeElement.className != 'flag-icon'){
                    $(".misconception_detail").hide();
                    $(".common-wrong-answer-modal").hide();
                }                                      
            });
            
            $(".common_wrong_answer").on('click','.question-preview',function(){
                $(".question_data").hide(); 
                // $(".question-preview").removeClass('highlight-card');  
                // $(this).addClass('highlight-card');             
                $(".common_wrong_answer #"+$(this).attr('data')).show();
            });
             $(".critical").on('click','.question-preview',function(){
                $(".question_data").hide();
                $(".critical #"+$(this).attr('data')).show();
            });
              $(".recommended").on('click','.question-preview',function(){
                $(".question_data").hide();
                $(".recommended #"+$(this).attr('data')).show();
            });
            $(".performed_well").on('click','.question-preview',function(){
                $(".question_data").hide();
                $(".performed_well #"+$(this).attr('data')).show();
            });
            $(".common_wrong_answer").on('focus','.flag-icon',function(){
                $(".misconception_detail").show();
                $(".common-wrong-answer-modal").show();
            });
        }

        $scope.showDiscussion = function(event,tab){
            $scope.critical = false;
            $scope.recommended = false;
            $scope.performed = false;
            if(event != '')
                $(".equal-width").removeClass('active');
            $(".showDiscussion").addClass("active");
            if(tab == 'critical'){
                $scope.critical = true;
                $($(".critical .question-preview")[$scope.question_index_critical]).trigger('click');                
            }
            else if(tab == 'recommended'){
                $scope.recommended = true;
                $($(".recommended .question-preview")[$scope.question_index_recommended]).trigger('click');   
            }
            else{
                $scope.performed = true;
                $($(".performed_well .question-preview")[$scope.question_index_performed]).trigger('click');   
            } 

            try{
                $(event.target).addClass('active');
            }catch(e){}
        };
        $scope.autoScroll = function(target){
            var target = target.split('|');
            var parent = target[0];
            var element = target[1];
            var page = target[2];                            
            if($(".sticky").hasClass("stick"))
            {
               var scrollTop = 60;
            }
            else
            {
                var scrollTop = 120;
            }           
                var scroll = $("."+element).offset().top - scrollTop;
            $("html, body").animate({scrollTop: scroll},1000);            
            $scope.pageTrack(page);
        }        
        $scope.getAssessmentChart = function(){
            getChart("#accuracyChart",$scope.class_accuracy_chart_data);
            getInnerChart("#accuracyChart_ring",$scope.class_accuracy_chart_data);
        }
        $scope.pageTrack = function(page){
            var config = {
                headers : {
                    'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
                }
            }
            var pageObj = $.param({
                pageUrl: 'topicReport.php/'+page,
                type : 'teacherInterface',
                userID : $scope.topic_info.userID,
                sessionID : $scope.topic_info.sessionID            
            });           
            $http.post(request.pageTrack,pageObj,config);                                
            if(page == 'assessmentReport')
            {
                $(".class_accuracy_button").trigger('click');
            } 
            if(page == 'progressReport')
            {
                $(".progress_summary_button").trigger('click');
            }  
        }
        
        $scope.learningUnit = function(){
            TopicReportService.GetLearningUnit($scope.initialObj,function(response){
                $scope.lu_summary_flag = 1;           
                $scope.learning_unit = response.data;
                if($scope.initialObj.fromHomePage == 1 )
                {                  
                    $scope.initialObj.fromHomePage =0;
                    $scope.pageTrack('learningUnitSummary');                       
                }                
            });
        } 
        $scope.classDiscussion = function(){
            TopicReportService.GetClassDiscussion($scope.initialObj,function(response){
                $scope.class_discussion_flag = 1; 
                $scope.critical_data = response.data.cwaDetails.critical;
                $scope.recommended_data = response.data.cwaDetails.recommended;
                $scope.performed_data = response.data.cwaDetails.performed;
                $scope.downloadStrAssessement = response.data.downloadStr;
                $scope.animationQuesAssessement = response.data.animatedQuestions;
                $scope.critical = true;
                $scope.recommended = false;
                $scope.performed = false;
                $scope.showDiscussion('','critical');
            });
        };
        $scope.commonWrongAnswer = function(){
            TopicReportService.GetCommonWrongAnswer($scope.initialObj,function(response){
                $scope.cwa_flag = 1;
                $scope.common_wrong_answer = response.data.cwaDetails;                
                $scope.downloadStr = response.data.downloadStr;
                $scope.animationQues = response.data.animatedQuestions;
                angular.forEach($scope.common_wrong_answer, function(value, key) {
                    $scope.common_wrong_answer[key].students_failed = value.failedStudentList.length;
                    if(value.failedStudentList.length > 0){
                        var html = '';
                        html += '<ul>';
                        angular.forEach(value.failedStudentList, function(fail, key) {
                            html += '<li>'+fail+'</li>';
                        });
                        html += '</ul>';
                        $scope.common_wrong_answer[key].failedList = html;
                    }
                });
                $scope.total_pages = $scope.common_wrong_answer.length;               
            });
        }                        
        $scope.slideDetails = function(){
            $scope.open = !$scope.open;
            if($scope.open){
                $(".question_details>.arrow").html('<<');
                $(".question_details").animate({'right' : "-23%"},500);
            }
            else{
                $(".question_details>.arrow").html('>>');
                $(".question_details").animate({'right' : "-4%"},500);
            }
        }
        $scope.goInitParams = function(){            
            $scope.initialObj = {};  
            $scope.initialObj.cls = $("#lstClass").val();
            $scope.initialObj.section = $("#lstSection").val() == undefined ? '' : $("#lstSection").val();
            $scope.initialObj.ttCode = $("#lstTopic").val();            
        }
        
        $scope.initializeParameters = function(){
            $scope.url_parameters = $scope.getURLParameters(); 
            $scope.initialObj = {};  
            $scope.initialObj.cls = $scope.url_parameters['cls'];
            $scope.initialObj.section = $scope.url_parameters['sec'];
            $scope.initialObj.ttCode = $scope.url_parameters['topics'];
            $scope.initialObj.fromHomePage = $scope.url_parameters['fromHomePage']; 
            $scope.modeVal = $scope.url_parameters['mode'];             
                     
        }
        $scope.getTopicProgress = function(){ 
            var params = Object.assign( {}, $scope.initialObj );                   
            params.limit = $scope.limit_student_data;
            TopicReportService.GetTopicProgress(params, function(response){
                $scope.progress_summary_flag =1;
                $scope.topic_progress = response.data.topicProgress;
                $scope.progress_summary = response.data.progressSummary; 
                $scope.chart_data = response.data.chartData;
                            
                var start = 0;
                angular.forEach($scope.progress_summary.student_info, function(value, key) {
                  $scope.progress_summary.student_info[key]['start'] = start;
                  $scope.progress_summary.student_info[key]['end'] = start + ($scope.length(value.students)  * 100 / $scope.progress_summary.total_students) * 360 /100;
                  start = $scope.progress_summary.student_info[key]['end'];                
                });
                getChart("#chart", $scope.chart_data);
                getInnerChart("#chart_ring",$scope.chart_data);

            });
        }
        $scope.getAssessment = function(){
            TopicReportService.GetAssessment($scope.initialObj,function(response){
                $scope.class_accuracy_flag = 1;
                $scope.coteacher_topic_flag = response.data.coteacherTopicFlag;
                $scope.assessment_flag = response.data.assessmentFlag == 1 ? true : false;
                if($scope.assessment_flag) 
                {
                    $scope.assessment_data = response.data.assessmentCompleted;
                    $scope.class_accuracy = response.data.assessmentDetails.accuracySummary;
                    $scope.avg_accuracy = response.data.assessmentDetails.avgAccuracy;                   
                    if($scope.avg_accuracy < 40){
                        $scope.avg_accuracy_highlight = "Low";
                    }
                    else if($scope.avg_accuracy < 80){
                        $scope.avg_accuracy_highlight = "Average";
                    }
                    else {
                        $scope.avg_accuracy_highlight = "Good";
                    }
                    $scope.class_accuracy_chart_data = response.data.assessmentDetails.chartData;
                    var start = 0;
                    angular.forEach($scope.class_accuracy.student_info, function(value, key) {
                      if (value.range == 'Incompleted') {
                          $scope.class_accuracy.student_info[key]['start'] = 0;
                          $scope.class_accuracy.student_info[key]['end'] = 0;
                          return;
                      }
                      $scope.class_accuracy.student_info[key]['start'] = start;
                      $scope.class_accuracy.student_info[key]['end'] = start + ($scope.length(value.students) * 100 / $scope.assessment_data.total_students) * 360 /100;
                      start = $scope.class_accuracy.student_info[key]['end'];
                    });

                    $("#progressReportTab").css('width','50%');
                    $("#assessmentReportTab").css('width','50%');
                
                }   
                else
                {       
                     $scope.setTab(); 
                }                           
                
            });
        }
        $scope.setTab = function()
        {
            $("#progressReportTab").css('width','100%');
            $("#assessmentReportTab").css('width','00%');
        }
        $scope.getTimeToComplete = function(){                    
            TopicReportService.GetTimeToComplete($scope.initialObj,function(response){
                $scope.time_to_complete_flag = 1;  
                $scope.time_to_complete = response.data;
            });
        }        
        $scope.getTopicInfo = function(){                    
            TopicReportService.GetTopicInfo($scope.initialObj,function(response){                
                $scope.topic_info = response.data;                 
                if($scope.modeVal == 0) 
                {
                    $("#myTopicsUrl").unbind("click");
                    $("#myTopicsUrl").attr("href","mytopics.php?ttCode="+ $scope.initialObj.ttCode +"&cls="+$scope.initialObj.cls+"&section="+$scope.initialObj.section+"&flow="+$scope.topic_info.topicDetails.flow+"&interface=new&gradeRange=1-9");                     
                }               
            });
        }
        $scope.whatsGoingOn = function(){
            TopicReportService.GetWhatsGoing($scope.initialObj,function(response){
                $scope.whats_going_on_flag = 1;
                $scope.all_whats_going = response.data;                               
            });
        }
        $scope.whatsGoingDetail = function(data){
            if(data.type == "message"){
                return;
            }
            if(data.type == "sampleQuestion")
            {
                window.open('sampleQuestions.php?ttCode='+$scope.initialObj.ttCode+'&learningunit='+data.id+'&cls='+$scope.initialObj.cls);
            }
            else if(data.type=="dailyPractice")
            {
                $("#clusterCode").val(data.id);
                $("#childClass").val($scope.initialObj.cls);
                $("#ttCode").val($scope.initialObj.ttCode);               
                $("#mindsparkTeacherLogin").attr("action", "../userInterface/practisePage.php?");    
                $("#mindsparkTeacherLogin").submit();
            }
        }; 
        $scope.downloadData = function()
        {            
            if($("#animationQues").val()>0)
            {
                if($("#totalQues").val()==$("#animationQues").val())
                    alert("Please note that all the questions have animations/html5 interactives and hence will not get downloaded in the report.");
                else if($("#animationQues").val()==1)
                    alert("Please note that "+$("#animationQues").val()+" question has an animation/html5 interactive and hence will not get downloaded in the report.");
                else
                    alert("Please note that "+$("#animationQues").val()+" questions have animations/html5 interactives and hence will not get downloaded in the report.");
            }

            $("#frmDownloadCWA").submit();
        }    
        $scope.downloadAssessmentData = function()
        {            
            if($("#animationQuesAssessment").val()>0)
            {
                if($("#totalQuesAssessment").val()==$("#animationQuesAssessment").val())
                    alert("Please note that all the questions have animations/html5 interactives and hence will not get downloaded in the report.");
                else if($("#animationQuesAssessment").val()==1)
                    alert("Please note that "+$("#animationQuesAssessment").val()+" question has an animation/html5 interactive and hence will not get downloaded in the report.");
                else
                    alert("Please note that "+$("#animationQuesAssessment").val()+" questions have animations/html5 interactives and hence will not get downloaded in the report.");
            }

            $("#frmDownloadAssessment").submit();
        }   
        $scope.openStudentTrail = function(userID, type, id, range_type)
        {
            if(type=='assessment'){
                if(range_type == 'Incompleted')
                    return;

                window.open('studentTrail.php?assessment=' + id + '&user_passed_id='+userID); 
            }
            else
                window.open('studentTrail.php?topic_passed_id='+$scope.initialObj.ttCode+'&user_passed_id='+userID); 

        }
        $scope.showPrev = function(){
            $scope.question_index-=5;
            animatePreview($scope.question_index,'left');
        }
        $scope.showNext = function(){
            $scope.question_index+=5;
            animatePreview($scope.question_index,'right');
        }
        $scope.showPrevCritical = function(){
            $scope.question_index_critical-=5;
            animatePreviewCritical($scope.question_index_critical,'left');
        }
        $scope.showNextCritical = function(){
            $scope.question_index_critical+=5;
            animatePreviewCritical($scope.question_index_critical,'right');
        }
        $scope.showPrevRecommended = function(){
            $scope.question_index_recommended-=5;
            animatePreviewRecommended($scope.question_index_recommended,'left');
        }
        $scope.showNextRecommended = function(){
            $scope.question_index_recommended+=5;
            animatePreviewRecommended($scope.question_index_recommended,'right');
        }
        $scope.showPrevPerformed = function(){
            $scope.question_index_performed-=5;
            animatePreviewPerformed($scope.question_index_performed,'left');
        }
        $scope.showNextPerformed = function(){
            $scope.question_index_performed+=5;
            animatePreviewPerformed($scope.question_index_performed,'right');
        }
        $scope.$on('ngRepeatFinished', function(ngRepeatFinishedEvent) {
            $(".check").popover();
            $(".chart-tooltip").popover();
            $(".highlight").popover();
           
        });           
        $scope.preinit();

    }]).directive('onFinishRender', function ($timeout) {
        return {
            restrict: 'A',
            link: function (scope, element, attr) {
                if (scope.$last === true) {
                    $timeout(function () {
                        scope.$emit(attr.onFinishRender);
                    });
                }
            }
        }
    }).directive('studentProgress',function($timeout){
        return {
            transclude:true,
            restrict : 'E',
            scope : {
                data : '@',
                condition : '@'
            },
            replace: true,
            template : [
                '<div class="progress-indicator">',
                    '<div class="progress-container">',
                        '<div class="progress-data">',
                        '</div>',
                        '<div class="progress-handle">',
                        '</div>',
                    '</div>',
                    
                    
                '</div>'
            ].join(''),
            controller : function($scope, $element, $rootScope){
                $timeout(function(){
                    var prog = $scope.data;
                    if($scope.condition == "false")
                    {
                        $element.children().children('.progress-data').addClass('green-progress');
                        $element.children().children('.progress-handle').addClass('green-progress');
                    }
                    else{
                        if(prog == '-'){
                            $element.children().children('.progress-data').addClass('no-progress');
                            $element.children().children('.progress-handle').addClass('no-progress');
                        }
                        else if(prog < 50){
                            $element.children().children('.progress-data').addClass('red-progress');
                            $element.children().children('.progress-handle').addClass('red-progress');
                        }
                        else if(prog < 99){
                            $element.children().children('.progress-data').addClass('orange-progress');
                            $element.children().children('.progress-handle').addClass('orange-progress');
                        }
                        else{
                            $element.children().children('.progress-data').addClass('green-progress');
                            $element.children().children('.progress-handle').addClass('green-progress');                       
                        }
                    }
                    $element.children().children('.progress-data').css('width', prog + '%');
                    $element.children().children('.progress-handle').css('left', --prog + '%');
                });
            }
        }
    }).directive('topicProgress',function(){
        return {
            restrict : 'E',
            scope : {
                topic : '='               
            },
            template : [
                '<div class="topics-report-card progress-card">',
                    '<div class="card-heading">', 
                    '{{topic.heading}}',                       
                        '<span class="underline"></span>',
                        '<span class="icon pull-right progressIcon"><span class="{{topic.icon}}"></span></span>',
                    '</div>',
                    '<div class="card-body">',
                        '<div class="prog-data">',
                            '<h2>{{ topic.progress }}</h2> <h4>%</h4>',
                        '</div>',
                        '<student-progress  data="{{topic.progress}}" condition="false">',

                        '</student-progress>',
                    '</div>',                    
                '</div>'
            ].join(''),
            controller : function($scope, $element, $rootScope){
                // console.log($scope);
                //$element.find("student-progress")[0].attr('data',$scope.data.progress);
            }
        }
    }).directive('assessmentCompleted',function(){
        return {
            restrict : 'E',
            scope : {
                data : '='               
            },
            template : [
                '<div class="assessment-data-card progress-card">',
                    '<div class="card-heading">', 
                        '{{data.heading}}',                        
                        '<span class="underline"></span>',
                        '<span class="icon pull-right assessmentIcon"></span>',
                    '</div>',
                    '<div class="card-body">',
                        '<div class="prog-data">',
                            '<h2>{{ data.students_count }}</h2><h4>/{{ data.total_students }} Students</h4>',
                        '</div>',
                        '<student-progress data="{{data.progress}}" condition="false">',

                        '</student-progress>',
                    '</div>',                   
                '</div>'
            ].join(''),
            controller : function($scope, $element, $rootScope){
                // console.log($scope.data);   
            }
        }
    }).directive('timeToComplete',function(){
        return {
            restrict : 'E',
            scope : {
                data : '='             
            },
            template : [
                '<div class="time-to-complete-card progress-card">',
                    '<div class="card-heading" layout="row" layout-align="">',
                    '{{data.heading}}',                        
                        '<span class="underline"></span>',
                        '<span class="icon pull-right timeToCompleteIcon"></span>',
                    '</div>',
                    '<div class="card-body">',
                        '<div>',
                            '<div><h2 class="inline">{{ data.sessionToComplete }}</h2> <h5 class="inline">Session(s) /</h5></div>',
                            '<div><h2 class="inline">{{ data.minsToComplete }}</h2> <h5 class="inline">Minutes approx</h5></div>',
                            '<div class="card-footer icon pull-right" data-toggle="tooltip" data-placement="left" title="" data-original-title="Estimate from historical data">',                           
                            '</div>',                       
                        '</div>',                        
                    '</div>',                    
                    
                '</div>'
            ].join(''),
            controller : function($scope, $element, $rootScope){
                 $(".card-footer").popover();
            }
        }
    }).directive('progressChart',function(){
        return {
            restrict : 'A',
            scope : {
            },
            link : function(scope, element, attrs){
                if(!attrs.start && !attrs.end){
                    createProgress(element[0],attrs.progressChart,attrs.dash);
                }else{
                    createDetailedChart(element[0], attrs.students, attrs.progressChart, attrs.slicecolor, attrs.start,attrs.end);
                }
            }
        }
    }).factory('TopicReportService',[ '$http', '$rootScope','$timeout' , function($http, $rootScope, $timeout){
        var service = {};

        // service.GetClassAccuracyChart = function(callback){
        //      this.request(request.get_class_accuracy_chart,callback);
        // }        
         service.InitializeParameters = function(paramsObj, callback){
            this.request('',paramsObj,callback);            
        }
        service.GetProgressReport = function(callback){
            
             this.request(request.progress_summary,callback);
        }
        // service.GetClassAccuracy = function(callback){
        //      this.request(request.class_accuracy,callback);
        // }
        service.GetWhatsGoing = function(paramsObj,callback){
            
             this.request(request.whats_going_on,paramsObj,callback);
        }            
        service.GetTopicProgress = function(paramsObj,callback){
            
             this.request(request.get_topic_progress,paramsObj,callback);
        }
        service.GetTopicReport = function(paramsObj,callback){
                    
            this.request(request.get_topic_progress,paramsObj,callback);
        }
        service.GetTimeToComplete = function(paramsObj,callback){
            
             this.request(request.time_to_complete,paramsObj,callback);
        }
        service.GetTopicInfo = function(paramsObj,callback){
            
             this.request(request.topic_info,paramsObj,callback);
        }
        service.GetAssessment = function(paramsObj,callback){
            
             this.request(request.get_assessment,paramsObj,callback);
        }
        service.GetLearningUnit = function(paramsObj,callback){
            this.request(request.learning_unit,paramsObj,callback);
        } 

        service.GetCommonWrongAnswer = function(paramsObj,callback){
            this.request(request.common_wrong_answer,paramsObj,callback);
        }
        service.GetClassDiscussion = function(paramsObj,callback){
            this.request(request.class_discussion,paramsObj,callback);   
        }
        service.request = function(mode,params,callback){
            $timeout(function(){
                if(typeof params === 'undefined'){
                    params = {};
                }
                params.mode = mode; 
                $http({
                    'method' : 'GET',
                    'url' : request.get_topic_report,
                    'params' : params,
                }).then(function(response){
                    //check for logout
                    if(typeof response.data.logout !== 'undefined' && response.data.logout ==1)
                    {
                        window.location.href = '/mindspark/logout.php';
                    }
                    else
                        callback(response);
                });
            });
        }
        return service;
    }]);



function createProgress(canvas, data, dash){
    var fillStyle = "";
    if(dash == 1){
        fillStyle = '#E7E7E7';
        data = 0;
    }
    else if(data < 40){
        fillStyle = '#F98053';
    }
    else if(data < 80){
        fillStyle = '#FBD542';
    }
    else{
        fillStyle = '#79D84B';
    }
    var end = 360 * data / 100;
    var canvasElement = canvas;
    $(canvasElement).drawArc({
        fillStyle: '#fff',
        x: 25, y: 25,
        radius: 23,
        start:0,
        end:360
   }).drawArc({
        strokeStyle: fillStyle,
        strokeWidth: '1',
        x: 25, y: 25,
        radius: 23,
        start : 0,
        end : 360
   }).drawArc({
        strokeStyle: fillStyle,
        strokeWidth: '4',       
        x: 25, y: 25,
        radius: 23,
        start : 0,
        end : end
   }).drawText({
        fillStyle: '#000',
        x: 25, y: 25,
        fontSize: 12,
        fontFamily: 'Verdana, sans-serif',
        fontStyle : 'bold',
        text: dash == 1 ? '-' : data + '%' 
   });

}    
function animatePreview(index, direction){
    var left = direction == "left" ? 5 : -5;
    var elements = $(".common_wrong_answer .question-preview");
    $.each(elements,function(){
        $(this).animate({ 'left' : (common_left + (20.3 * left)) + '%' },400);
    });
    common_left = common_left + (20.3 * left);
    $($(".common_wrong_answer .question-preview")[index]).trigger('click'); 
}   
function animatePreviewCritical(index, direction){
    var left = direction == "left" ? 5 : -5;
    var elements = $(".critical .question-preview");
    $.each(elements,function(){
        $(this).animate({ 'left' : (common_left_critical + (20.3 * left)) + '%' },400);
    });
    common_left_critical = common_left_critical + (20.3 * left);
    $($(".critical .question-preview")[index]).trigger('click');   
}   
function animatePreviewRecommended(index, direction){
    var left = direction == "left" ? 5 : -5;
    var elements = $("recommended .question-preview");
    $.each(elements,function(){
        $(this).animate({ 'left' : (common_left_recommended + (20.3 * left)) + '%' },400);
    });
    common_left_recommended = common_left_recommended + (20.3 * left);
    $($(".recommended .question-preview")[index]).trigger('click'); 
}   
function animatePreviewPerformed(index, direction){
    var left = direction == "left" ? 5 : -5;
    var elements = $(".performed_well .question-preview");
    $.each(elements,function(){
        $(this).animate({ 'left' : (common_left_performed + (20.3 * left)) + '%' },400);
    });
    common_left_performed = common_left_performed + (20.3 * left);
    $($(".performed_well .question-preview")[index]).trigger('click'); 
}  

function getInnerChart(div,data)
{
    var createInnerData  = modifyInnerData(data);
    createInnerData =   createInnerData.data; 
    $.plot(div,createInnerData,{
        series: {
            pie: {
                show: true,
                radius : 40,
                innerRadius : 30,
                label: {
                    show: false             
                },
                stroke : {
                       width : 0.1 
                    }
            }
        },
        legend : {
            show: false         
        }
    });
}  
function getChart(div,data){  
    var createData = modifyData(data);
    createData = createData.data;   
    $.plot(div,createData,{
        series: {
            pie: {
                show: true,
                radius : 75,
                innerRadius : 30,
                label: {
                    show: true,
                    radius: 90,
                    formatter: function (label, series) {
                        var formatLabel = label < 10 ? "0"+label : label;                        
                        var element = '<div style="color:' + series.color + ';font-size: medium;"><b>' + formatLabel + '</b></div>';
                        return element;
                    },
                    
                },
                stroke : {
                       width : 0.1 
                    }
            }
        },
        legend : {
            show: false         
        }
    });
    
}
function createDetailedChart(canvas, data, fillStyle, sliceColor, start, end){
    var canvasElement = canvas;
    $(canvasElement).drawSlice({
        fillStyle: fillStyle,
        x: 50, y: 50,
        radius: 50,
        start:0,
        end:359.99,
        opacity : 0.4        
    }).drawSlice({
        fillStyle: fillStyle,
        x: 50, y: 50,
        radius: 50,
        start:start,
        end:end
    }).drawArc({
        strokeStyle : sliceColor,
        strokeWidth : 20,
        x: 50, y: 50,
        radius: 20,
        start:start,
        end:end

    }).drawSlice({
        fillStyle: '#fff',
        x: 50, y: 50,
        radius: 20,
        start:0,
        end:359.99
    }).drawText({
        fillStyle: sliceColor,
        x: 50, y: 50,
        fontSize: 14,
        fontFamily: 'Verdana, sans-serif',
        text: data      
    });
}
function modifyData(data)
{

    var newData = [];   
    $.each(data,function(key,value){
        newData[key] = {
            data : value.data,
            label : value.data,
            color : value.color            
        }        
    });
    
    return {'data' : newData};
}
function modifyInnerData(data)
{

    var newData = [];    
    $.each(data,function(key,value){
        newData[key] = {
            data : value.data,
            label : value.data,
            color : value.strokeColor            
        }         
    });
    
    return {'data' : newData};
}

function finished(element){
    $(element).parent().parent().removeClass('loader-frame');
    var iframe = $(element)[0];
    var cwa = $($(iframe.contentWindow.document.body)[0]).find('.cwa');
    $.each(cwa, function() {
            var currentElement = $(this)[0];
            var details =   $(currentElement).attr("id");
            var detailsInput =   $(currentElement).next("input");
            $.post("ajaxRequest.php","mode=commonWrongAnswer&quesDetails="+detailsInput.val(),function(data) {                 
                $(currentElement).html(data);
        });
    });
    jsMath.ProcessBeforeShowing(document.getElementsByClassName('question_data'));    
}

function highlightPreview(div){
    $(".activate-preview").hide();
    $(".question-number").show();    
    $(".iframe-modal").removeClass('active');
    $(".question-preview #"+div.dataset.active).show();
    $(div).children(".iframe-modal").addClass('active');
    $(div).children(".question-number").hide();
}







