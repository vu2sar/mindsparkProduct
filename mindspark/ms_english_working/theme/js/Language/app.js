var diagnosticTestComplete = false;

/*var englishInterface = angular.module('englishInterface', ['ngRoute']).run(function($rootScope) {
     $rootScope.grades = [1, 2, 3, 4];
});*/

//declaring the english app.
var englishInterface = angular.module('englishInterface', ['ngSanitize','ultimateDataTableServices','skillometer']).run(function($rootScope) {
    $rootScope.grades = [1, 2, 3, 4];
});
var snowFall = '';
var previousTextValue = '';    
var userSelectedText = '';
var userSearchFlag = 0;
var sessionData = {};
var idel_time_logout = 60;
var inactive_time = 0;
var inactive_interface = 0;
var oldclientX = 0;
var clientX = 0;
var oldclientY = 0;
var clientY = 0;
var essayWriterMsgFlag = '';
var userLogout = false;
//all of these data can be held in sessiondata.
var pageMode = /preview/.test(document.URL) ? 'preview' : 'waitForServerResponse';

var ajaxLoadCount = 0;
var timerIntervalId = '';
var previousQuestion = undefined;
var currentQuestion = {};
var rating_object = {};
var step_after_rating = 0;
var serverResponseTimeLimit = 20000;    //shivam(03/22/2018)- Changed server response time 15000 to 20000.

var firstPassageOfSession = true;
var firstFreeQuesOfSession = true;
var buddyTemp = 'boy';
var questionStartTime = 0;

var saveEssayIntervalId = 0;
var saveEssayTimeId = 0;

var NO_INTERNET = false;                //Flag to trigger offline mode
var lastOnlineQuestion = undefined;     //holds the last question object that was online 
/*var totalTimeQues = new Array();
var counter = 0;*/
var timeTakenInClassroom = 0;
var timerIdClass = 0;
var el, indices, startIndex, endIndex;
var essayPlainText,labeledEssayText;
var opts = {};
jQuery.browser = {};
var tinymceConfig;
var essaySelection = null;
var selectionEndTimeout = null;
var logOutReason = 0;




/*
 * id - tooltip key value pair. Can provide tooltip to any element that has a unique id.
 * The order of appearance of tips is based on the order in this object.
 */
 
var tooltips = {
    'sidebarToggle' : 'Show/Hide Sidebar: Opens and closes the sidebar.',
    'mainTimer' : "Here's how long you've been Mindspark-ing!",
    'commentButton' : 'Comment: Use this to enter a comment while in any section.',
    'helpButton' : 'Help Button: Get help at any point, on any section.',
    'sessionReportButton' : 'Daily Report: Displays a report on how you performed in this session.',
    'logOutButton' : 'Logout: Logs you out of Mindspark English.',
    'sidebar' : 'Sidebar: Helps you navigate in the Interface.',
    'sbi_home_page' : 'Home Page: Takes you to the start page.',
    'sbi_the_classroom' : 'Classroom: Start session here to read passages, listen to conversations and attempt questions that follow.',
    //'sbi_the_skills' : 'You will be shown your progress on skills here.',
    'skillometer' : 'You will be shown your progress on skills here.',
    'sbi_the_grounds' : 'Activity: Play exciting games and interactives.',
    'sbi_trophy_room' : 'Achievements: Keep track of your achievements and unlocked goodies.',
    'sbi_essay_writer' : 'Essay Writer: Write and express your thoughts and get valuable feedback on topics.',
    'sbi_teacher_report' : 'Report: View your students reports',
    'audioPlayer' : 'Put on your headphones, or turn on your speakers to listen to the audio.',
    'saveEssay': 'Save Essay: Save whatever you have written to resume later',
    'submitEssay': 'Submit Essay: Click on this button to submit your essay for evaulation once you have finished writing.',
    'skillometer' : 'Skillometer: Shows how your skills are developing.',
    'prompt' : 'Displays important messages or asks confirmation of user actions.',
    'endTour' : 'dummy', //dummy entry to end tour.
};

window.onload = function(e)
{
    if (document.readyState === 'complete')
    {
        Helpers.check_inactive_interface();
        Helpers.disableHistoryNavigation();
        documentKeyDown();
    }

};


//configure to disable caching on internet explorer.
englishInterface.config([
        '$httpProvider',
        function($httpProvider) {
            //initialize get if not there
            if (!$httpProvider.defaults.headers.get) {
                $httpProvider.defaults.headers.get = {};
            }

            //disable IE ajax request caching
            $httpProvider.defaults.headers.get['If-Modified-Since'] = 'Mon, 26 Jul 1997 05:00:00 GMT';
            // extra
            $httpProvider.defaults.headers.get['Cache-Control'] = 'no-cache';
            $httpProvider.defaults.headers.get['Pragma'] = 'no-cache';
            $httpProvider.defaults.headers.common.Authorization = sessionStorage.getItem('windowName');
        }
    ]
);

//base controller of the app. 
englishInterface.controller('mainControl', ['$scope', '$http', '$rootScope',
function($scope, $http, $rootScope) {

    var context_scope = this;
    $scope.sessionData = {};
    

    $scope.sessionData.loaded = false;
    sessionData.currentLocation = {
        'type' : 'home',
        'value' : '',
        'location' : ''
    };
    
    // Notification prompt.
    $scope.showme = false;
    $scope.message = '';
    $scope.total_sparkies = 0;
    $scope.changeProfile = false;
    $scope.totalCharacters = 20;
    $scope.charactersPath = Helpers.constants['THEME_PATH'] + 'img/Language/';
    $scope.editDesc = true;
    $scope.address  = true;
    $scope.dob      = true;
    $scope.phoneno  = true;
    $scope.email    = true;
    $scope.descWordCount = 160;
    $scope.toStudent = false;
    $scope.showNotification=false;
    $scope.showNotificationFlag = 0;
    $scope.totalNotificationCount = 0;
    $scope.secretQuestions = Helpers.secretQuestion;


    $scope.getCharacters = function(num){
         return new Array(num);
    };

    $scope.characterArr = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20];
    $scope.resetProfilePage = function(){
        $scope.editDesc = true;
        $scope.address  = true;
        $scope.dob      = true;
        $scope.phoneno  = true;
        $scope.email    = true;
        $scope.descWordCount = 60;
        $scope.toStudent = false;
        $scope.changePassword = false;
        $scope.changeProfile = false;
        $scope.secretAns = "";
        $("#secretQuestion").val(sessionData.secretQues == '' ? 'Select' : sessionData.secretQues);
        $scope.$apply();
    }
    $scope.showNotificationToUser = function(msg)
    {
        if(sessionData.category != 'STUDENT')
        {
            $scope.showNotification=false;
            return;
        }

        $scope.message = msg;
        $scope.showme = true;
        try{
            $scope.$apply();
        }catch(e){}
        setTimeout(function(){
            $scope.hideNotification();
        },5000);  
    };
    $scope.showNotificationBox = function()
    {
        if($scope.showNotification == false && $scope.showNotificationFlag == 0)
            $scope.showNotification=true;
        else
            $scope.showNotification=false;
    }
    $scope.loadNotifications = function(event)
    {
        var index = $(".notification-box li").index($(event.target).parent());
        var text_heading = event.target.textContent;
        if(text_heading.indexOf('Essay Review') != -1)
        {
            //Essay Review Page
            showpendingEssays();
        }
        else if(text_heading.indexOf('Reset Password Request') != -1)
        {
            //Reset password page
             $http.get(Helpers.constants['CONTROLLER_PATH'] + 'home/getPasswordResetRequest').success(function(data) {
                showResetPasswordPage();
                $scope.ajax_response( requestPassword , data ,[]);
            });
        }
        $scope.showNotification=false;

    }
    requestPassword = function(data, extraParams){
        $scope.requestObject = data;
        if($scope.requestObject.length == 0)
        {
            // Toggle the view of the list.
            $scope.noRequest = true;
        }
        else
        {
            $scope.noRequest = false;
        }
        sessionData.currentLocation.location = 'reset_password';
        $scope.getUserNotifications();
    }
    $scope.resetUserPassword = function(event){
        var target = $(event.target).html();
        var id = $(event.target).parent().attr('data-id');
        if(target == 'No Action')
        {
            var requestUrl = 'noActionForUser';
            var promptMessage = 'No action to reset password for the user';
        }
        else{
            var requestUrl = 'resetUserPassword';
            var promptMessage = 'User password reset successfully';
        }
        $http({
          method  : 'POST',
          url     : Helpers.constants['CONTROLLER_PATH'] + 'home/'+requestUrl,
          data    : $.param({'id': id }), //forms user object
          headers : {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'} 
         }).success(function(data) {
                Helpers.ajax_response( requestPassword , data , [] );
                Helpers.prompt(promptMessage);
                
         }).error(onAjaxError);
    }
    $scope.essayReview = function(){
        $scope.showNotification = false;
        showpendingEssays();
    }
    $scope.resetPasswordRequest = function(){
        $scope.showNotification = false;
        
    }
    $scope.hideNotification = function()
    {
        $scope.showme = false;
        $scope.message = '';
        try{
            $scope.$apply();
        }catch(e){}
    };
    $scope.updateSpakieCount = function(sparkie)
    {
        $scope.total_sparkies = sparkie;
       
    };

    $scope.ajax_response = function( callback , data, extraParams ){
        if(data.toString().indexOf('<title>Login</title>') != -1)
        {
            
            tryingToUnloadPage = true;
            window.location.assign(Helpers.constants.REARCH_LOGIN_PATH);
            return;
        }

        if( data.active === "true" )
        {
            if(data.status === 'invalid_session')
            {
                // Logout prompt and logout
                
            }
            else if(data.status === 'success')
            {
                if(data.msg != '')
                {
                    Helpers.prompt({
                        text : data.msg,
                        buttons : {
                            'OK' : function() {
                            }
                        },
                        noClose : true,
                    });
                }
                if(callback.toString() != '')
                {
                    if(typeof data.result_data === "string")
                        parameters = jQuery.parseJSON(data.result_data);
                    else
                        parameters = data.result_data;
                    if(typeof extraParams === undefined)
                        callback(parameters);
                    else
                        callback(parameters,extraParams);
                }
            }
            else
            {
                if(data.msg != '')
                {
                    Helpers.prompt({
                        text : data.msg,
                        buttons : {
                            'OK' : function() {
                            }
                        },
                        noClose : true,
                    });
                }
            }
        }
        else
        {
            if(data.status === 'invalid_session')
            {
                // Logout prompt and logout
                $(".english-content-wrapper").fadeOut(200);
                Helpers.prompt({
                    text : 'Your session has expired. Kindly login again.',
                    buttons : {
                        'OK' : function() {
                        }
                    },
                    closeFunction : function(){
                        
                        tryingToUnloadPage = true;
                        //logOut()
                        logOutReason = 7;
                        logOut();
                    },
                    noClose : true,
                });

                $('.ui-dialog-titlebar-close').hide();
                return;
            }
            else
            {
                Helpers.prompt({
                    text : 'Your session is active on other system. Kindly login again to start a new one.',
                    buttons : {
                        'OK' : function() {
                        }
                    },
                    closeFunction : function(){
                        
                        tryingToUnloadPage = true;
                        logOutReason = 8;
                        logOut();
                        //logOut();
                    },
                    noClose : true,
                });

                $('.ui-dialog-titlebar-close').hide();
                return;   
            }
        
        }
    };

    $http.get(Helpers.constants['CONTROLLER_PATH'] + 'questionspage/getMySparkieCount').success(function(data) {
        $scope.ajax_response( showSparkies , data, [] );
    }).error(onAjaxError);

    function showSparkies(sparkie)
    {
        try{
            $scope.updateSpakieCount(sparkie);
        }catch(e){}
    };

    // Profile and password update logic
  
        $scope.closeProfilePic = function(){
            $scope.changeProfile = false;
        };
        $scope.changeProfilePic = function(){
            if($scope.sessionData.loaded)
            {
                sessionData.currentLocation.location = 'profile_details';
                sessionData.currentLocation.type = 'profile_details';
                sidebarToggle(false);
                stopAndHideOtherActivities();
                $scope.changeProfile = true;

            }
        };
        // About me section functions
        $scope.editAboutMe = function()
        {
            $scope.editDesc = false;
            $scope.descAboutMe = $scope.sessionData.personalInfo == false ? '' : $scope.sessionData.personalInfo;   
            $scope.getWordCount($scope.descAboutMe);
            previousTextValue = $scope.descAboutMe;
                    
        };
        $scope.saveAboutMe = function()
        {
            $scope.editDesc = true;
            $scope.sessionData.personalInfo = $scope.descAboutMe;
            $.ajax({
                url: Helpers.constants['CONTROLLER_PATH'] + 'home/updatePersonalInfo', 
                method: "POST",
                data: { 'personalInfo' : $scope.sessionData.personalInfo }
             }).success(function(data){
                Helpers.ajax_response( '' , data, [] );
             }).error(onAjaxError);
        };
        $scope.maxLength = function(event,length)
        {
            event.keyCode = (event.keyCode != 0) ? event.keyCode : event.which; // mozilla hack..
            if(event.keyCode != 9 && event.keyCode != 8 && event.keyCode!=37 && event.keyCode!=38 && event.keyCode!=39 && event.keyCode!=40)
            { 
                
                if(event.target.value.length >= length)
                {   
                    event.preventDefault();
                    var ua = navigator.userAgent;
                    if( ua.indexOf("Android") >= 0 )
                    {
                        event.target.value = previousTextValue;
                    }
                    return;
                }
                else
                {
                    previousTextValue =  event.target.value;
                }
            }
        }
        $scope.maxLengthWords = function(event,length)
        {
            var regex = /[\W]/;
            if(!regex.test(event.key))
            {
        
                event.keyCode = (event.keyCode != 0) ? event.keyCode : event.which; // mozilla hack..
                if(event.keyCode != 9 && event.keyCode != 8 && event.keyCode!=37 && event.keyCode!=38 && event.keyCode!=39 && event.keyCode!=40)
                { 
                    if(event.target.value.length >= length)
                    {
                        event.preventDefault();
                        var ua = navigator.userAgent;
                        if( ua.indexOf("Android") >= 0 )
                        {
                            event.target.value = previousTextValue;
                        }
                        return;
                    }
                    else
                    {
                        previousTextValue = event.target.value;
                    }
                }
            }
            else
            {
                event.preventDefault();
            }
        }
        $scope.resetState = function()
        {
            $scope.editDesc = true;
            $scope.descAboutMe = $scope.sessionData.personalInfo;   
        };
        // -----------------------------
        // save secret question
        $scope.saveSecretQues = function(event)
        {
            $scope.secretQues = $("#secretQuestion").val();
            var secretQues = $scope.secretQues == undefined ? '' : $scope.secretQues;
            var secretAns  = $scope.secretAns == undefined ? '' : $scope.secretAns;
            if(secretQues == '' || secretQues == 'Select')
            {
                Helpers.prompt('Please select the secret question.');
                return;
            }
            if(secretAns == '')
            {
                Helpers.prompt('Please enter the secret answer.');
                return;
            }

            sessionData.secretQues = $scope.secretQues;
            sessionData.secretAns = $scope.secretAns;

            var question = $scope.secretQues;
            var answer = $scope.secretAns;
            
            $scope.sessionData = sessionData;
            $scope.secretAns = '';
            $http({
              method  : 'POST',
              url     : Helpers.constants['CONTROLLER_PATH'] + 'home/saveSecretQuestion',
              data    : $.param({ 'secretQues' : question , 'secretAns' : answer }), //forms user object
              headers : {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'} 
            }).success(function(data) {
                Helpers.ajax_response( '' , data , [] );

            }).error(onAjaxError);
        };
        $scope.toggleChildDOB = function(viewToggle){
            switch(viewToggle){
                case 'edit':
                    var d = new Date();
                    var n = d.getFullYear();
                    $('#childDob').datepicker({dateFormat: 'dd-mm-yy',maxDate: 0, changeYear: true, yearRange: "1990:"+n });
                    $scope.updatedDOB = $scope.sessionData.DOB;
                    $scope.dob = false;
                    break;
                case 'save':
                    $scope.updatedDOB = $("#childDob").val();
                    if($scope.validateDOB())
                    {
                        $scope.dob = true;
                        $scope.sessionData.DOB = $scope.updatedDOB;
                        if($scope.sessionData.DOB == '')
                        {
                            $scope.editableDOB = true;
                        }
                        else
                        {
                            $scope.editableDOB = false;
                        }
                        $.ajax({
                            url: Helpers.constants['CONTROLLER_PATH'] + 'home/updateDOB', 
                            method: "POST",
                            data: { 'dob' : Helpers.formatDateSQL($scope.sessionData.DOB) }
                         }).success(function(data){
                            Helpers.ajax_response( '' , data, [] );
                         }).error(onAjaxError);
                    }
                    break;
                case 'cancel':
                    $scope.updatedDOB = $scope.sessionData.DOB;
                    $scope.dob = true;
                    break;
            }
        };
        $scope.validateDOB = function()
        {
            if($scope.updateDOB == '')
            {
                Helpers.prompt('Please enter your date of birth.');
                return false;
            }
            return true;
        }
        // Address edit
        $scope.toggleAddressEdit = function(viewToggle){
            if($scope.sessionData.address == 'false' || $scope.sessionData.address === false)
            {
                $scope.sessionData.address = '';   
            }
            switch(viewToggle){
                case 'edit':
                    $scope.updatedAddress = $scope.sessionData.address;
                    previousTextValue = $scope.updatedAddress;
                    $scope.address = false;
                    break;
                case 'save':
                    if($scope.validateAddress())
                    {
                        $scope.sessionData.address = '';
                        $scope.sessionData.address = $scope.updatedAddress;
                        $scope.address = true;
                        $("#addressDetails").html($scope.sessionData.address);
                        $.ajax({
                            url: Helpers.constants['CONTROLLER_PATH'] + 'home/updateAddress', 
                            method: "POST",
                            data: { 'address' : $scope.sessionData.address }
                         }).success(function(data){
                            Helpers.ajax_response( '' , data, [] );
                            $(".shortened").removeClass('shortened');
                            $(".comment").shorten();
                         }).error(onAjaxError);
                    }
                    break;
                case 'cancel':
                    $scope.updatedAddress = $scope.sessionData.address;
                    $scope.address = true;
                    break;
            }


        }
        $scope.validateAddress = function()
        {
            if($scope.updatedAddress == undefined)
            {
                Helpers.prompt('Please enter the address.');
                return false;
            }
            if($scope.updatedAddress == ''){
                Helpers.prompt('Please enter the address.');
                return false;
            }
            return true;
        }
         // Address edit
        $scope.toggleChangePassword = function(viewToggle){

            switch(viewToggle){
                case 'edit':
                    $scope.changePassword = true;
                    $scope.oldPassword = '';
                    $scope.newPassword = '';
                    $scope.confirmPassword = '';
                    break;
                case 'save':
                    if($scope.validateChangePassword())
                    {
                        $scope.changePassword = false;
                        $.ajax({
                            url: Helpers.constants['CONTROLLER_PATH'] + 'home/updatePassword', 
                            method: "POST",
                            data: { 'old' : $scope.oldPassword, 'new' : $scope.newPassword}
                         }).success(function(data){
                            Helpers.ajax_response( '' , data, [] );
                         }).error(onAjaxError);
                    }
                    break;
                case 'cancel':
                    $scope.changePassword = false;
                    break;
            }

        }
        $scope.validateChangePassword = function()
        {
           /* if($scope.oldPassword == '' || $scope.oldPassword == undefined)
            {
                Helpers.prompt("Please enter the old password.");
                return false;
            }*/
            if($scope.newPassword == '' || $scope.newPassword == undefined)
            {
                Helpers.prompt("Please enter the new password.");
                return false;
            }
            if($scope.confirmPassword == '' || $scope.confirmPassword == undefined)
            {
                Helpers.prompt("Please enter the confirm password.");
                return false;
            }
            if($scope.newPassword == $scope.confirmPassword)
            {
                return true;
            }
            else
            {
                Helpers.prompt("New password and confirm password are not same.");
            }
            return false;
        }
        $scope.getWordCount = function(info)
        {
            //$scope.descWordCount = (60 - Helpers.checkMinMaxWords(info,"count",0));
            $scope.descWordCount = (160 - info.length);
        }
        // Phone edit
        $scope.togglePhoneEdit = function(viewToggle){
            if($scope.sessionData.contactNo == 'false' || $scope.sessionData.contactNo === false)
            {
                $scope.sessionData.contactNo = '';   
            }
            switch(viewToggle){
                case 'edit':
                    $scope.updatedPhone = $scope.sessionData.contactNo;
                    $scope.phoneno = false;
                    break;
                case 'save':
                    if(!$scope.inValidatePhoneNumber($scope.updatedPhone))
                    {
                        $scope.sessionData.contactNo = $scope.updatedPhone;
                        $scope.phoneno = true;
                        $.ajax({
                            url: Helpers.constants['CONTROLLER_PATH'] + 'home/updatePhoneNo', 
                            method: "POST",
                            data: { 'phone' : $scope.sessionData.contactNo}
                         }).success(function(data){
                            Helpers.ajax_response( '' , data, [] );
                         }).error(onAjaxError);
                    }
                    break;
                case 'cancel':
                    $scope.updatedPhone = $scope.sessionData.contactNo;
                    $scope.phoneno = true;
                    break;
            }

        }
        // Email edit
        $scope.toggleEmailEdit = function(viewToggle){
            if($scope.sessionData.childEmail == 'false' || $scope.sessionData.childEmail === false)
            {
                $scope.sessionData.childEmail = '';   
            }
            switch(viewToggle){
                case 'edit':
                    $scope.updatedEmail = $scope.sessionData.childEmail;
                    previousTextValue = $scope.updatedAddress;
                    $scope.email = false;
                    break;
                case 'save':
                    if(!$scope.inValidateChildEmail($scope.updatedEmail))
                    {
                        $scope.sessionData.childEmail = $scope.updatedEmail;
                        $scope.email = true;
                        $.ajax({
                            url: Helpers.constants['CONTROLLER_PATH'] + 'home/updateEmail', 
                            method: "POST",
                            data: { 'email' : $scope.sessionData.childEmail}
                         }).success(function(data){
                            Helpers.ajax_response( '' , data, [] );
                         }).error(onAjaxError);
                    }
                    else
                    {
                        Helpers.prompt('Please enter a valid email address');
                    }
                    break;
                case 'cancel':
                    $scope.updatedEmail = $scope.sessionData.childEmail;
                    $scope.email = true;
                    break;
            }

        }
        $scope.inValidateChildEmail = function(email)
        {

            var invalid = false;
            try{
                var emailList = email.split(',');
            }
            catch(e){ return true; }
            for(var i=0; i<emailList.length ; i++)
            {
                if(!Helpers.validateEmail(emailList[i]))
                {
                    invalid = true;
                    break;
                }
            }
            return invalid;
        }
        $scope.inValidatePhoneNumber = function(phone)
        {
            var invalid = false;
            try{
                var phoneList = phone.split(',');
            }
            catch(e){ return true; }
            var exist = [];
            for(var i=0;i<phoneList.length;i++)
            {
                if(Helpers.validateNumber(phoneList[i]))
                {
                    if(exist.indexOf(phoneList[i]) == -1)
                    {
                        exist.push(phoneList[i]);
                    }
                    else
                    {
                        invalid = true;
                        Helpers.prompt('Same phone no added more than once.');
                        break;
                    }        
                }
                else
                {
                    invalid = true;
                    Helpers.prompt('Please enter a valid phone number.');
                    break;
                }
            }

            return invalid;
        }
        $scope.setNewProfilePic = function(scopeIndex){
            $scope.sessionData.profileImage = Helpers.constants['THEME_PATH'] + 'img/Language/'+scopeIndex.i+'.png';
            $scope.changeProfile = false;
            // Save new Profile Image ajax
            $http.get(Helpers.constants['CONTROLLER_PATH'] + 'home/updateProfilePic/'+scopeIndex.i ).success(function(data) {
                $scope.ajax_response( '' , data, [] );
            }).error(onAjaxError);

        };
        

    // ----------------------------------


    $scope.getEssays = function() {
        $http.get(Helpers.constants['CONTROLLER_PATH'] + 'home/getEssayTopics').success(function(data) {
            $scope.ajax_response( essayWriter , data, [] );
        }).error(onAjaxError);
    };

    function essayWriter(data, extraParams)
    {
        $scope.essays = data;
        if($scope.essays[0].forceEssay!='no'){
            $scope.forceEssay=1;
        }else{
            $scope.forceEssay='no';
        }
        sessionData.essays = data;
        if(!Object.keys(data).length) // if no essay is pending to attempt
        {
            $scope.essaysPendingYes = false;
            $scope.essaysPendingNo = true;
        }
        else
        {
            $scope.essaysPendingYes = true;
            $scope.essaysPendingNo = false;
        }
    };
        
    $scope.getSummary = function() {
        $http.get(Helpers.constants['CONTROLLER_PATH'] + 'home/getEssaySummary').success(function(data) {
            $scope.ajax_response( essayWriterSummary , data, [] );
        }).error(onAjaxError);
         $scope.getUserNotifications();
    };

    function essayWriterSummary(data){
        $scope.sessionData.essaySummaryData = data;
        sessionData.essaySummaryData = data;
        var tmpArrA = [];
        var tmpArrB = [];
        var tmpArrC = [];
        if (data.inCompleteEssays != null) {
            var tmpArrA = data.inCompleteEssaysName.split("||");
            var tmpArrB = data.inCompleteEssays.split("||");
            var tmpArrC = data.inCompleteEssayID.split("||");
            var tmpArrIsForce = data.inCompleteEssaysisForce.split("||");
        }
        var obj = [];
        for (var p = 0; p < tmpArrA.length; p++) {
            var tmpObj = {
                'name' : tmpArrA[p],
                'id' : tmpArrB[p],
                'essayId' : tmpArrC[p],
                'isForce':tmpArrIsForce[p]
            };
            obj.push(tmpObj);
        }
        $scope.incompleteEssays = obj;

        tmpArrA = [];
        tmpArrB = [];
        tmpArrC = [];
        if (data.completedEssays != null) {
            tmpArrA = data.completedEssaysName.split("||");
            tmpArrB = data.completedEssays.split("||");
            tmpArrC = data.completedEssaysID.split("||");
        }
        obj = [];
        for (var p = 0; p < tmpArrA.length; p++) {
            var tmpObj = {
                'name' : tmpArrA[p],
                'id' : tmpArrB[p],
                'essayId' : tmpArrC[p],
            };
            obj.push(tmpObj);
        }
        $scope.completeEssays = obj;

        tmpArrA = [];
        tmpArrB = [];
        tmpArrC = [];
        if (data.gradedEssaysName != null) {
            tmpArrA = data.gradedEssaysName.split("||");
            tmpArrB = data.gradedEssays.split("||");
            tmpArrC = data.gradedEssaysID.split("||");
        }

        obj = [];
        for (var p = 0; p < tmpArrA.length; p++) {
            var tmpObj = {
                'name' : tmpArrA[p],
                'id' : tmpArrB[p],
                'essayId' : tmpArrC[p]
            };
            obj.push(tmpObj);
        }
        $scope.gradedEssays = obj;
    };
   
    /*
     * IF preview:ie view as student. Then skip to initialize.
     */
    if (pageMode == 'preview') {
        sessionData.mode = 'preview';
        unAngularApp.initialize();
        return;
    }

    $http.get(Helpers.constants['CONTROLLER_PATH'] + 'home/getSidebarInfo').success(function(data) {
        $scope.ajax_response( sidebarInfo , data, [] );
    });
    function sidebarInfo(data, extraParams)
    {
        $scope.sidebarItems = data;
        ajaxComplete();
    }

    $http.get(Helpers.constants['CONTROLLER_PATH'] + 'home/canSubmitEssay').success(function(data) {
        $scope.ajax_response( canSubmitEssay , data , [] );
    });
    
    function canSubmitEssay(data , extraParams)
    {
        if(data != "true")
        {
            $("#submitEssay").html('You can submit your essay after '+data+' day(s).');
            $("#submitEssay").attr('data-value','You can submit your essay after '+data+' day(s).');
            //$("#submitEssay").prop('disabled',true);
            //$("#submitEssay").remove();
        }
    }
    /*var os = getOS();
    var osDetails =  os[0]+os[1];
   
    jQuery.ajax({
        type : "POST",
        url : Helpers.constants.CONTROLLER_PATH + 'diagnosticTest/getUserInfo',
        data:{'osDetails' : osDetails},
        "async" : false,
        dataType:'json',
        success: function(data) 
        {      
           $scope.ajax_response( getUserInfo , data , []);
        }
    });*/
    $http.get(Helpers.constants['CONTROLLER_PATH'] + 'diagnosticTest/getUserInfo').success(function(data) {
        $scope.ajax_response( getUserInfo , data , []);
    });

    $scope.getUserNotifications = function(){
        $http.get(Helpers.constants['CONTROLLER_PATH'] + 'home/getNotifications').success(function(data) {
            $scope.ajax_response( getNotifications , data ,[]);
        });
    }
    $scope.getUserNotifications();

    function getNotifications(data, extraParams)
    {
        if(data.length == 0)
        {
            $scope.showNotificationFlag = 1;
        }
        $scope.notifications = data;
        $scope.totalNotificationCount = 0;
        angular.forEach($scope.notifications, function(value, key) {
            $scope.totalNotificationCount += parseInt(value);
        }, '');
        
        if(data.essayAssigned!==0){
            $scope.essayAssignedToUser="The Essay '"+data.essayAssigned+"' has been assigned to you!";
            $("#sbi_essay_writer").attr('data-tooltip',$scope.essayAssignedToUser);
        }else{
            $scope.essayAssignedToUser=0;
            $("#sbi_essay_writer").removeAttr('data-tooltip');
            $("#sbi_essay_writer").attr('title','');
        }
    }
    function getUserInfo(data, extraParams)
    {
        if( !Helpers.valid_request(data) )
        {
            Helpers.unauthorized_user();
            return;
        }

        data.mode                     = 'normal';
        var nameArr                   = data.childName.split(' ');
        data.firstName                = nameArr[0];
        data.lastName                 = nameArr[1];
        $scope.sessionData            = data;
        sessionData                   = data;
        sessionData.minTimeForClass   = parseInt(sessionData.minTimeForClass);
        sessionData.timeAllowedPerDay = parseInt(sessionData.timeAllowedPerDay);
        
        $("#secretQuestion").val(sessionData.secretQues == '' ? 'Select' : sessionData.secretQues);
        //$scope.secretAns = sessionData.secretAns == false ? '' : sessionData.secretAns;
        $scope.secretAns = '';
        if(sessionData.category == 'STUDENT')
        {
            $scope.showNotification=false;
            $scope.toStudent = true;
        }
        $("#profileImage").click(function(){
            sessionData.classRoomStarted = true;
            $("#dictionaryButton").prop('disabled', false);
            showProfileContainer();
            angular.element(document.body).scope().resetProfilePage();
            
        });

        //added by nivedita
        sessionData.weekComplete = false;
        //end

        // Change the static value 60 after data base driven change.
        //sessionData.totalTimeAllowedPerDay = 60;-
        sessionData.maxQuota = 60;
        // ---------------------------- //
        //Helpers.create_constant(sessionData);
        var arr = [];
        var arr = $scope.sessionData.teacherClass && $scope.sessionData.teacherClass.split("~");
        for ( i = 0; i < arr.length; i++) {
            arr[i] = arr[i].replace(',', '');
        }
        $rootScope.grades = arr;
        if (!$scope.sessionData.userName) {
            sessionData.mode = 'nothing';
            logOutReason = 7;
            logOut();
            //logOut();
            return;
        }

        if ($scope.sessionData.childName == null) {
            $scope.sessionData.childName = $scope.sessionData.childName;
        }else{
            if($scope.sessionData.childName.toString().length > 15)
            {
                var tempName = $scope.sessionData.childName.split(" ");
                $scope.sessionData.childName = tempName[0];    
            }
        }
        
        if(sessionData.category != "TEACHER" || $(".glyphicon-bell").html() == 0)
            $("#PendingEssaysBtn").hide();
		
                //checking if user is super admin or not
                if(sessionData.category.toLocaleLowerCase()=="school admin" && sessionData.subcategory.toLocaleLowerCase()=="all"){
			$("#goback").show();
                }
                
		if(sessionData.category.toLocaleLowerCase()=="school admin" || sessionData.category.toLocaleLowerCase()=="admin")
			$("#sparkieButton").hide();
		
        if(sessionData.picture == false)
            sessionData.picture = 20;
        //TODO: get these variables from the server
        $scope.sessionData.profileImage = Helpers.constants['THEME_PATH'] + 'img/Language/'+sessionData.picture+'.png';
        $scope.sessionData.level = '';
        $scope.sessionData.address = $scope.sessionData.address == false ? '' : $scope.sessionData.address;
        $scope.sessionData.DOB = $scope.sessionData.DOB == '0000-00-00' ? '' : Helpers.formatDate($scope.sessionData.DOB);
        if($scope.sessionData.DOB == '')
        {
            $scope.editableDOB = true;
        }
        //'Level 2'; //disabling for diagnostic test
        //'Sapling'; //disabling for diagnostic test
        $scope.sessionData.sparkies = '36';

        $scope.sessionData.loaded = true;

       
        ajaxComplete();
        $(".english-content-wrapper").removeClass('none').fadeIn(100);

        $scope.sessionData.quoteObject = {
            'Quote' : '',
            'Author' : ''
        };

        $http.get(Helpers.constants['CONTROLLER_PATH'] + 'home/getHomePageInfo').success(function(data) {
            $scope.ajax_response( getHomePageInfo , data , []);
        });

        function getHomePageInfo(data, extraParams)
        {
            $scope.sessionData.quoteObject.Quote       = data.Quote;
            $scope.sessionData.quoteObject.Author      = data.Author;
        }

        $http.get(Helpers.constants['CONTROLLER_PATH'] + 'questionspage/getTimeSpentToday').success(function(data) {
            $scope.ajax_response( getTimeSpentToday , data , []);
        });

        function getTimeSpentToday(data , extraParams)
        {
            
            $scope.sessionData.timeSpent = Math.round(parseFloat(data) * 60);
            $scope.sessionData.timeSpentInHour=0;
            $scope.sessionData.timeSpentInMin=0;
            $scope.sessionData.timeTakenInClassroom = sessionStorage.getItem("timeTakenInClassroom");
            
            //if ($scope.sessionData.timeSpent > ($scope.sessionData.totalTimeAllowedPerDay * 60) && $scope.sessionData.category == 'STUDENT') 
            if ($scope.sessionData.timeSpent > ($scope.sessionData.maxQuota * 60) && $scope.sessionData.category == 'STUDENT') 
            {
                $scope.sessionData.complete = true;
                $(".english-content-wrapper").fadeOut(200);
                 Helpers.prompt({
                    text : 'You have completed your Mindspark quota for the day! You can login again tomorrow to enjoy Mindspark!',
                    buttons : {
                        'OK' : function() {
                            
                            tryingToUnloadPage = true;
                            //logOut();
                            logOutReason = 3;
                            logOutBtnClick();
                            //logOutBtnClick();
                        }
                    },
                    closeFunction : function(){
                        
                        tryingToUnloadPage = true;
                        logOutReason = 3;
                        logOutBtnClick();
                        //logOut();
                        //logOutBtnClick();
                    },
                    noClose : true,
                });
                setTimeout(function() {
                    $('.ui-dialog-titlebar-close').hide();
                    logOutReason = 3;
                    logOutBtnClick();
                    //logOutBtnClick();
                },5000);  
                $('.ui-dialog-titlebar-close').hide();
                return;

            } else {
                $scope.sessionData.complete = false;
            }
            if ($scope.sessionData.timeSpent <= 5) {
                $scope.sessionData.firstLoginToday = true;
            } else {
                $scope.sessionData.firstLoginToday = false;
            }
            //ajaxComplete();

            if($scope.sessionData.category == 'STUDENT')
            {
                var timeLeft = (($scope.sessionData.maxQuota * 60) - $scope.sessionData.timeSpent);
                var time = timeLeft/60;
                
                if(timeLeft == 120)
                {
                    var msg = "Hey! You've completed your Mindspark session for the day. You will be logged out after "+time+" minutes."
                    Helpers.prompt({
                        text : msg,
                        buttons : {
                            'OK' : function() {
                                ajaxComplete();
                            }
                        },
                        noClose : true,
                    });
                }
                else
                {
                    ajaxComplete();
                }
                return;
            }
            else
                ajaxComplete();                

        }

        $http.get(Helpers.constants['CONTROLLER_PATH'] + 'home/getTimeSpentAtHome/').success(function(data) {
            $scope.ajax_response( getTimeSpentAtHome , data , [] );
        });
        
        function getTimeSpentAtHome(data , extraParams)
        {
            var response = data;
            if (data > 45) {
                $scope.sessionData.completeAtHome = true;
            } else {
                $scope.sessionData.completeAtHome = false;
            }
        }

        $http.get(Helpers.constants['CONTROLLER_PATH'] + 'home/gettimeTakenInClassroom/').success(function(data) {
            $scope.ajax_response( getTimeSpentAtClassroon , data , [] );
        });
        
        function getTimeSpentAtClassroon(data , extraParams)
        {
            var response = data;
            $scope.sessionData.classComplete = response;
        }

    }
    
    $http.get(Helpers.constants['CONTROLLER_PATH'] + 'igre/getIGREInfo').success(function(data) {
        $scope.ajax_response( igreListing , data, []);
    });

    function igreListing(data, extraParams)
    {
        $scope.activityData = data;
        activityDetails = $scope.activityData;
        ajaxComplete();
    }
    
    //adding go back button functionality
     $scope.goback = function(event)
    {
        //updating flag to redirect
        tryingToUnloadPage = true;
            //desteoying function but keeping limited require function
             $http.get(Helpers.constants['CONTROLLER_PATH'] + "login/destroyLimitedSession").success(function(data) {
                 //redirecting to back
                window.location.href='/mindspark/teacherInterface/getSchoolDetails.php';
            });
    }
}]);

//custom directive to add background image.
//added watch to wait for sessionData to load
englishInterface.directive('backImg', function() {
    return function(scope, element, attrs) {
        scope.$watch(function($scope) {
            return $scope.sessionData.loaded;
        }, function(newValue, oldValue) {
            var url = attrs.backImg;
            element.css({
                'background-image' : 'url(' + url + ')',
            });
        }, true);
    };
});

englishInterface.directive('ngKeystroke', function(){
    return {
        restrict: 'A',
        link: function(scope, elem, attrs){
            elem.bind("keyup", function(){
                scope.$digest(); //we manually refresh all the watchers. I don't know why the watcher on {{log}} won't trigger if the event is fired on a "space" key :s but the push is done regardless
            });
        }
    };
});
englishInterface.controller('essayEvaluation', ['$scope', '$http', '$rootScope',
function($scope, $http, $rootScope) {
    /*var commentsObject     = {'comment' : 'dummy comments'};
    var commentsObject1      = {'comment' : 'dummy commentsasdf'};
    var commentsObject2      = {'comment' : 'dummy commentsasdfasdf'};*/
    $scope.comments          = [];
    $scope.rubric            = 'Show Rubric';
    $scope.autocomment       = 'Auto Comment';
    $scope.selectedText      = '';
    $scope.togglecomment     = false;
    $scope.commentindexkey   = 0;
    $scope.newcomment        = false;
    $scope.save              = 0;
    $scope.submit            = 1;
    $scope.submitMode        = 0;
    $scope.disableSubmission = false;
    


    /*auto comment nivedita */
    $scope.essayForm = 
     [
        { sr_no: "1", comment: "Your essay is too short."},
        { sr_no: "2", comment: "Your essay looks incomplete."},
        { sr_no: "3", comment: "Please separate your essay into an introduction, body and conclusion to make it easier to read, and it also helps you put your points across more neatly."},
    ];
    $scope.puntuationComment = 
     [
        { sr_no: "1", comment: "You have used too many ellipses (i.e. 'dots') in this essay. We only use ellipses when we want to imply a continuation from a previous sentence (or to end a sentence uncertainly)."},
        { sr_no: "2", comment: "Do not use unnecessary abbreviations and internet slang (like 'u' for 'you') etc."},
        { sr_no: "3", comment: "Please break sentences when they are too long, by using commas and full stops."},
        { sr_no: "4", comment: "Always capitalize the first letter of a word while starting a sentence."},
        { sr_no: "5", comment: "'I' is always capital."},
        { sr_no: "6", comment: "Do not put spaces before punctuation marks. Always add a space AFTER a punctuation mark (like an exclamation, full stop, comma etc.)"},
    ];
    $scope.prepositionComment = 
     [
        { sr_no: "1", comment: "Please look up the proper usage of prepositions. You seem to be confusing 'to', 'on' 'at' etc."},
        { sr_no: "2", comment: "Never start sentences with conjunctions like 'because', 'and', 'since' etc."},
    ];
    $scope.verbsComment = 
     [
        { sr_no: "1", comment: "Please always follow a single tense throughout the essay. Do not switch from past to present or vice-versa in the middle of a sentence."},
        { sr_no: "2", comment: "See how singular and plural tenses are used, by checking the detailed feedback given."},
    ];
    $scope.autoCommentArrGeneral = [];
    $scope.autoCommentArrGeneral1 = [];
    $scope.autoCommentArrSpecific = [];
    $scope.autoCommentArrSpecific1 = [];
    $scope.getActiveElementId = '';
    

    //Helpers.contenteditableEnter();
    
    
    $scope.getCheckedValue = function(checked, getValue){
        var getElement = $scope.getActiveElementId;
    };

    $scope.getActiveElement = function(elementId)
    {
        //if(elementId == 'generalFeedback')
            $scope.getActiveElementId = elementId;
            $scope.operation = '';
    }
    
    /*auto comment nivedita end*/

    /*
	 *  function description :  calls the getEssay(essay detail request ajax) and handles the output in the getEvaluationEssay function
	 *	@return  none 
	 *
	 */ 
	
    $scope.getEssayDetails = function(){
        // Set the essay content.
        // Write ajax call to get the essay content
        $scope.autocomment == 'Close';
        $scope.hideAutoComment();

        $('#loader').show();
        $http({
          method  : 'POST',
          url     : Helpers.constants['CONTROLLER_PATH'] + 'essayAllotment/getEssay',
          data    : $.param({'eMode':eMode ,'essayScoreID':eScoreID}), //forms user object
          headers : {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'} 
         }).success(function(data) {
                $('#loader').hide();
                $('#essay_evaluation').show();
                $('#essay_evaluation > .none').show();
                if(Helpers.ajax_response( '' , data , [] )){
                         $scope.getEvaluationEssay($.parseJSON(data.result_data));
                  }
         }).error(onAjaxError);
    };
    
    /*
	 *  function description :  handles the essay detail response and UI elements
	 *  param1 : data , response data to render
	 *  param2 : extraparams , if passed
	 *	@return  none 
	 *
	 */
	
	$scope.getEvaluationEssay=function(data, extraParams)
    {
        window.getSelection().removeAllRanges();
        clearSelection();
        essaySelection = null;
        
        $scope.compareCommentGeneral = [];
        $scope.compareCommentSpecific = [];

        $("#essay_detail").html("");
		$scope.comments = [];
        $scope.togglecomment = false;
		$scope.comments = [];
		$scope.essayDetails = {};
		window.essaySpellingErrors=[];

        tinyMCE.activeEditor.setContent(data.data.essay);

        var editor = tinymce.activeEditor;
        // Count no of words in the essay.
        var wordCnt = editor.plugins.wordcount.getCount();

        if(data.data.essaySpellingErrors!=undefined)
            window.essaySpellingErrors=data.data.essaySpellingErrors;
		
        // Remove the junk space, tags(p,br)
        data.data.essay=data.data.essay.replace(/&nbsp;/ig,' ');
        // starting p tag replaced with blank.
        data.data.essay=data.data.essay.replace(/<p[^>]*?>/g,'');
        // Ending p tag replaced with breakline.
        data.data.essay=data.data.essay.replace(/<\/p>/ig,'<br />');
        // Empty p tag replaced with breakline.
        data.data.essay=data.data.essay.replace(/<p><\/p>/ig,'<br/>');
        // Empty br with self closing br tag.
        data.data.essay=data.data.essay.replace(/\<br \/>/ig,'<br /> ');
        // Add space after li tag. Fix for indexing of miss spelled words.
 		data.data.essay=data.data.essay.replace(/\<li>/ig,'<li> ');
        // Fix for removing p tag around other tags.
        data.data.essay=data.data.essay.replace(/(<p[^>]+?>|<p>|<\/p>)/ig,''); 
        // Fix for removing p tag around other tags.
        data.data.essay = data.data.essay.replace(/^\<p\>/,"").replace(/\<\/p\>$/,"");
        
        //essayPlainText=data.data.essay.replace(/<span class=\'sp_err\'>(.*?)<\/span>/g,"$1");
        //essayPlainText=essayPlainText.replace(/<span class=\'for_err\'>(.*?)<\/span>/g,"$1");
        //essayPlainText=essayPlainText.replace(/\n/ig,' ');
        //essayPlainText=essayPlainText.replace(/\s+/ig,' ');
        
        essayPlainText=data.data.essay;
        $("#essay_detail").html(essayPlainText);

        var essayBody = $("#essay_detail").text();
        
        // If there are comments in the database saved then separate them, save in comments variable and highlight the comment words
		
		if (data.data.report.selectFeedback != "") {
            // Comment seperator {|~|}
            var sfbck = data.data.report.selectFeedback.split('{|~|}');
            var commentStr;
            for (var i = 0; i < sfbck.length; i++) {
                // Comment data seperator |*|
                var sfbi = sfbck[i].split('|*|');
                //BY NIVEDITA
                $scope.compareCommentSpecific.push(sfbi[2]);
                //BY NIVEDITA
                if ((sfbi).length != 3) continue;
                sfbi.push(i);
                    
                var re = /\n|\r/g; 
                var str = ""+sfbi[0]+"";
                var subst = ''; 
                var result = str.replace(re, subst);
                sfbi[0] = result ;
				$scope.createCommentBox(sfbi);
                var indexArr = sfbi[0].match(/\[(.*?)\]/);
                commentStr   = indexArr[1];
               
                //indices      = sfbi[1].split('~');
                
                var indicesMultiple      = sfbi[1].split('-');
                var newArray =  indicesMultiple.filter(function(v){return v!==''});
                
                el  = document.getElementById('essay_detail');
                for (var k = 0; k < newArray.length; k++) 
                {
                    var indicesSting =  newArray[k].split('~');
                    var startIndex   = indicesSting[0];
                    var endIndex     = indicesSting[1];
                    $scope.selectAndHighlightRange(el, startIndex, endIndex, commentStr);  
                }
                //$scope.selectAndHighlightRange(el, startIndex, endIndex);
            }    
            data.data.essay=$("#essay_detail").html();
        }else if(Object.keys(window.essaySpellingErrors).length>0){
            // If there are no comments saved in the database then separate them, save in comments variable and highlight the comment words
            var uniqueWords = [];
            $.each(window.essaySpellingErrors, function(i, el){
            if($.inArray(el.toLowerCase(), uniqueWords) === -1) uniqueWords.push(el.toLowerCase());
            });

            window.essaySpellingErrors = uniqueWords;

			for (var i = 0; i < window.essaySpellingErrors.length; i++) {
                var spellWord=window.essaySpellingErrors[i];
                
                var regex = new RegExp('(^|\\W)(' + spellWord.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&') + ')(?!\\w)', 'ig');
                
                var indicesMultiple = [];
                while ((m=regex.exec(essayBody))!==null) {
                    if (m[1]) {
                        indicesMultiple.push(m.index+1);
                        startIndex = m.index+1;
                     
                        //break;
                    } else { 
                        indicesMultiple.push(m.index);
                        startIndex = m.index;
                        //break; 
                   }
			    }
                
                endIndex=startIndex+spellWord.length;
                var title='Comment for [' + spellWord + ']';
                var label=startIndex + '~' + endIndex;
                //var commentText="Misspelling : "+spellWord;
                var commentText="Misspelt : "+spellWord;
                var commentIndex=$scope.comments.length;
                // var commentObj=[title,label,commentText,commentIndex];
                // $scope.createCommentBox(commentObj);
                el = document.getElementById('essay_detail');
                var label = ''
                for (var k = 0; k < indicesMultiple.length; k++) 
                {
                    var endIndex = indicesMultiple[k]+spellWord.length;
                    label += indicesMultiple[k] + '~' + endIndex+'-';
                    $scope.selectAndHighlightRange(el, indicesMultiple[k], endIndex, spellWord);  
                }
                var commentObj=[title,label,commentText,commentIndex];
                
                $scope.createCommentBox(commentObj);
                //$scope.selectAndHighlightRange(el, startIndex, endIndex);
            }
            data.data.essay=$("#essay_detail").html();
        }
        if (!el) el = document.getElementById('essay_detail');
        /*by nivedita*/
        $scope.compareCommentGeneral.push(data.data.report.feedback);
        /*by nivedita end*/
        $scope.essayDetails = {
            essayContent: data.data.essay,
            generalFeedback: data.data.report.feedback,
            essayTitle: data.data.title,
            timeTaken: Math.floor(data.data.timeTaken / 60) + ':' + data.data.timeTaken % 60,
            submitted: data.data.submittedOn,
            words: wordCnt,
            author: data.data.author,
            essayscorevalue: parseFloat(data.data.report.score).toFixed(2),
            essayScoreID: data.data.report.scoreID
        };
        var characterCountTitle = data.data.title.length;

        if(characterCountTitle > 76)
        {
            $(".rubric-heading-btn").css('margin-top', '-495px');
            $(".auto-comment-btn").css('margin-top', '-272px');    
            $(".rubric").css('top', '66px');
            $(".auto-comment").css('top', '66px');
        }
        else
        {
            $(".rubric-heading-btn").css('margin-top', '-504px');
            $(".auto-comment-btn").css('margin-top', '-281px');    
            $(".rubric").css('top', '57px');
            $(".auto-comment").css('top', '57px');
        }
        
        //$("#generalFeedback").val(data.data.report.feedback);
        $("#generalFeedback").html(data.data.report.feedback);
        $scope.essayMode=eMode; 
        $scope.disableSubmission=false;
        sessionData.essays = data;
        $scope.newcomment = false;
        
		// reset the rubric checkboxes when ever the new essay is loaded
		$scope.setRubric([-1, -1, -1, -1, -1, -1, -1]);
       
        
        if(eMode){
            $( "#closeBtn" ).removeClass( "pull-right" ).addClass( "moveCloseBtn" );
            $('#generalFeedback').attr('contenteditable',false);
            $("#essayScore").attr("readonly", true);

            $("#rubricBtn").hide();
            $("#autobtn").hide();
            
		}else{

            $("#rubricBtn").show();
            $("#autobtn").show();
            $(".rubric-heading-btn").css('left', '83%');
            $(".auto-comment-btn").css('left', '83%');

            $( "#closeBtn" ).removeClass( "moveCloseBtn" ).addClass( "pull-right" );
            $('#generalFeedback').attr('contenteditable',true);
            $("#essayScore").attr("readonly", false);
        }
        
       
        if(data.data.report.score > 0)
            $("#sugScore").html(parseFloat(data.data.report.score).toFixed(2));
        else
            $("#sugScore").html("-");

        // set the saved rubric score on ui along with check boxes checked

        if (data.data.report.rubricSc) {
            if (data.data.report.rubricSc.split(',').length == 7) $scope.setRubric(data.data.report.rubricSc.split(','));
        }
        
        $("#essay_detail li").each(function(){
            
            if($(this).children().length > 0)
            {
                if($(this).children().text() == '')
                {
                    $(this).remove();
                }
            }
            if($(this).text() == '' || $(this).text() == ' ')
            {
               $(this).remove();
            }
        });

    };

	/*
	 *  function description :  to get the selection made by user, highlight the selection , add the selection related blank comment in comments object
	 *	@return  none 
	 *
	 */
	
   
    $scope.getSelection = function(){
       if (essaySelection !=  null && essaySelection.toString() !=  "") 
        {
            var selIndexArr = $scope.getSelectionCharOffsetsWithin(el);

            if(!elementContainsSelection(document.getElementById('essay_detail')))
            {
               setTimeout(function() {
                    $scope.showPrompt('Please select the essay text to add comment.');
                    essaySelection = null;
                },900);    
                return;
            }

            //var sIndex=essayPlainText.indexOf(window.getSelection().toString());
            //var eIndex = sIndex+window.getSelection().toString().length;
            var sIndex=selIndexArr.start;
            var eIndex =selIndexArr.end;
           
            $scope.selectedText = essaySelection.toString();
            var mytext = $scope.selectHTML(sIndex,eIndex);
            
            //$scope.highlight("pink");
            //$scope.setHilights();
			$scope.selectedText=$scope.selectedText.replace(/\n|\r;/g , '')
			//$scope.selectedText.replace(/\n;/ig,'');
            var title='Comment for [' + $scope.selectedText.replace(/\"/g, '\\"') + ']';
            var label=sIndex + '~' + eIndex;
            var commentText="";
            var commentIndex=$scope.comments.length;
            var commentObj=[title,label,commentText,commentIndex];
            if(mytext !== false)
            {
                $scope.createCommentBox(commentObj);
                $scope.switchView('edit',commentIndex);
                $scope.newcomment=true;    
            }
            
			$scope.$digest();
            essaySelection = null;
        }
        else
		{
			setTimeout(function() {
                $scope.showPrompt('Please select the essay text to add comment.');
                essaySelection = null;
            },800);    

		}
	};

    /*
	 *  function description :  create comment box object and push it in the comments object
	 *  param1 : commentObj , comment object
	 *	@return  none 
	 *
	 */
	
	
	$scope.createCommentBox = function(commentObj){
        var commentObject = { 'comment' : {'commentFor' : commentObj[0] , 'commentRange' : commentObj[1] , 'commentText' : commentObj[2], 'commentIndex' : commentObj[3]}};
        $scope.comments.push(commentObject);
    };

    /*
	 *  function description :  saves comments in the database with the use of comments object
	 *  param1 : indexKey for which the commenttext is being saved along with comment object 
	 *  param2 : commentText , comment text which need to be saved for the comment index key 
	 *	@return  none 
	 *
	 */
	
	$scope.saveComment = function(indexKey,commentText)
    {
        if (typeof commentText != "undefined"){
            commentText = commentText.replace(/&nbsp;/ig,' '); 
            var comment = $.trim(commentText);
            var regex = /(<([^>]+)>)/ig;
            comment = comment.replace(regex, "");
            if(comment.length == 0){
                $scope.showPrompt('Please add comment in comment box.');
                return;   
            }
            $scope.comments[indexKey].comment.commentText=commentText;
        } 
        
        $scope.setInlineComments();
        
        $http({
              method  : 'POST',
              url     : Helpers.constants['CONTROLLER_PATH'] + 'essayAllotment/saveComment',
              data    : $.param({'essaySpecificFeedback':$scope.sfbMaster ,'essayScoreID':eScoreID}), //forms user object
              headers : {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'} 
         }).success(function(data) {
                if(Helpers.ajax_response( '' , data , [] )){
                         //$scope.saveCommentCallBack($.parseJSON(data.result_data));
                  }
          }).error(onAjaxError);

          $scope.switchView('cancel',indexKey);
          if($scope.newcomment)
             $scope.newcomment=false;
    };

    /*
	 *  function description :  switches the view of comment box from edit mode to non-edit mode and non-edit mode to edit mode
	 *	param1 : operation :, performs "edit" or "cancel" operation for comment box based on the user action 
	 *  param2 : index , comment index for which the operation need to be performed
	 *	@return  none 
	 *
	 */
	
	$scope.switchView = function(operation,index)
    {
       /* $scope.commentindexkey=index;
        if(operation === 'cancel')
            $scope.togglecomment = false;   
        else
            $scope.togglecomment = true;*/
        $scope.operation = operation;
        $scope.commentindexkey=index;
         var getAddedIndex = index + 1;
        if(operation === 'cancel')
        {
            var commentText = $("#specificFeedback"+getAddedIndex).html();
            if (typeof commentText != "undefined"){
                commentText = commentText.replace(/&nbsp;/ig,' '); 
                var comment = $.trim(commentText);
                var regex = /(<([^>]+)>)/ig;
                comment = comment.replace(regex, "");
                if(comment.length == 0){
                    $scope.showPrompt('Please add comment in comment box.');
                    return; 
                }     
            }
            $("#specificFeedbackDiv"+getAddedIndex).html($("#specificFeedback"+getAddedIndex).html());
            $scope.togglecomment = false;
            $scope.getActiveElementId = '';
        }
        else
            $scope.togglecomment = true;

        if(operation == 'edit')
            $scope.getActiveElementId = 'specificFeedback'+getAddedIndex;

        //Helpers.contenteditableEnter();

        
        //$scope.getActiveElementId = 'specificFeedback'+index;
    };

    /*
	 *  function description :  sets the inline comments from the comments object to save the comments in the database
	 *	@return  none 
	 *
	 */
	
	$scope.setInlineComments = function(){
        /*$scope.sfbMaster="";
        var sfbChildren = $scope.comments;
        for (var i = 0; i < sfbChildren.length; i++) {
            var y = sfbChildren[i].comment;
            if (i > 0) $scope.sfbMaster += '{|~|}';
            $scope.sfbMaster += $scope.addslashes($scope.stripslashes(y.commentFor)) + '|*|' + $scope.addslashes(y.commentRange) + '|*|' + $scope.addslashes(y.commentText);
        }*/
        $scope.sfbMaster="";

        var sfbChildren = $scope.comments;
            var j = 1;
        for (var i = 0; i < sfbChildren.length; i++) {
            var id= j; //issue was here for deleting from top
            var y = sfbChildren[i].comment;
            if (i > 0) $scope.sfbMaster += '{|~|}';
            //$scope.sfbMaster += $scope.addslashes($scope.stripslashes(y.commentFor)) + '|*|' + $scope.addslashes(y.commentRange) + '|*|' + $scope.addslashes(y.commentText);
            if($scope.removeComment != undefined && $scope.removeComment != '')
                $scope.sfbMaster += $scope.addslashes($scope.stripslashes(y.commentFor)) + '|*|' + $scope.addslashes(y.commentRange) + '|*|' + y.commentText;
            else
                $scope.sfbMaster += $scope.addslashes($scope.stripslashes(y.commentFor)) + '|*|' + $scope.addslashes(y.commentRange) + '|*|' + jQuery("#specificFeedback"+id).html();
            j++;
        }
        //$scope.removeComment = '';
    };

    $scope.addslashes = function(str) {
        str = str.replace(/\\/g, '\\\\');
        str = str.replace(/\'/g, '\\\'');
        str = str.replace(/\"/g, '\\"');
        str = str.replace(/\0/g, '\\0');
        return str;
    }

    $scope.stripslashes = function(str) {
        str = str.replace(/\\'/g, '\'');
        str = str.replace(/\\"/g, '"');
        str = str.replace(/\\0/g, '\0');
        str = str.replace(/\\\\/g, '\\');
        return str;
    }
    
    /*
	 *  function description :  To save the removed comment in the database in the form of save comment way.
	 *	param1 : indexKey , index key of the removing comment
	 *	param2 : isDeleteConfirm , true or false based on the delete alert confirmation
	 *	@return  none 
	 *
	 */
	
	
	$scope.removeComment = function(indexKey,isDeleteConfirm)
    {
        indexKey = indexKey.indexKey;
        if (isDeleteConfirm == undefined) {
				Helpers.prompt({
					text : "Are you sure you want to delete this comment?",
					buttons : {
						'OK' :  function(){angular.element($("#essay_evaluation")).scope().removeComment(indexKey,1)},
						'Cancel' : function(){},
					},
					'force': true
				});
				return;
		}
		
		var commentFor = $scope.comments[indexKey].comment.commentFor;
		var indexArr   = commentFor.match(/\[(.*?)\]/);
		commentFor     = indexArr[1];
		var lbl        = $scope.comments[indexKey].comment.commentRange;
		var lblArr     = lbl.split('~');
		var startLbl   = lblArr[0];
		var endLbl     = lblArr[1];
        $('.startIndex-' + startLbl + '.endIndex-'+endLbl).css("background-color", "");
		$scope.comments.splice(indexKey,1);
        $scope.removeComment = 'remove';
		$scope.saveComment(indexKey);
		if($scope.newcomment)
			$scope.newcomment=false;
		
    };
    // Rubric functions
    
	/*
	 *  function description :  show/hide the rubric as per the scope rubric value set
	 *	@return  none 
	 *
	 */
	
	$scope.showRubric = function()
    {
        if($scope.rubric == 'Hide Rubric')
        {
            $scope.hideRubric();
            $("#rubric").hide();
            $(".rubric-heading-btn").css('left', '83%');
        }
        else
        {
            $("#rubric").show();
            $(".rubric-heading-btn").css('left', '33%');

            $(".rubric").css('z-index',6);
            $(".auto-comment").css('z-index',5);

            $(".rubric").animate({'left':'50%'},'linear');
            $scope.rubric = 'Hide Rubric';            
            $('.fa-square').on('click',function(){
                var parent = $(this).parent().parent();
                parent.find('.fa-square').removeClass('fa-check-square');
                $(this).addClass('fa-check-square');
            });

        }
    }
    $scope.hideRubric = function()
    {
        $(".rubric").css('z-index',5);
        $(".auto-comment").css('z-index',5);
        
        $('.fa-square').off('click');
        $(".rubric").animate({'left':'100%'},'linear');
        $scope.rubric = 'Show Rubric';            
    }
    // Auto-comment functions
    /*$scope.showAutoComment = function()
    {
        if($scope.autocomment == 'Close')
        {
            $scope.hideAutoComment();
        }
        else
        {
            $(".rubric").css('z-index',5);
            $(".auto-comment").css('z-index',6);

            $(".auto-comment").animate({'left':'50%'},'linear');
            $scope.autocomment = 'Close';            
            $('.fa-square').on('click',function(){
                var parent = $(this).parent().parent();
                parent.find('.fa-square').removeClass('fa-check-square');
                $(this).addClass('fa-check-square');
            });

        }
    }*/
    
	
	$scope.showAutoComment = function()
    {
        var id = $scope.commentindexkey + 1;

        var getElement = $scope.getActiveElementId;
        
        /*$scope.autoCommentArrGeneral =[];
        $scope.autoCommentArrSpecific = [];*/
       
        if($scope.autocomment == 'Close')
        {
            $("#autocomment").hide();
            $("#generalFeedback").attr('contenteditable', 'true');

            $('#specificFeedback'+id).attr('contenteditable', 'true');

            $(".auto-comment-btn").css('left', '83%');
            $scope.hideAutoComment();
            if(getElement == 'generalFeedback')
            {

                $scope.autoCommentArrGeneral = jQuery('input[name=autocomment]:checked').map(function()
                {
                    return jQuery(this).val();
                }).get();
            }
            else if(getElement == 'specificFeedback'+id)
            {
                $scope.autoCommentArrSpecific = jQuery('input[name=autocomment]:checked').map(function()
                {
                    return jQuery(this).val();
                }).get();
            }
            
            if($scope.autoCommentArrGeneral.length > 0)
            {

                for(var i = 0; i<$scope.autoCommentArrGeneral.length; i++)
                {
                    if($scope.autoCommentArrGeneral[i] != '' && $scope.autoCommentArrGeneral[i] != undefined )
                    {
                        if($scope.generalcommentOpen[0] != '' && $scope.generalcommentOpen[0] != undefined)
                        {
                            var generalcomment = $scope.autoCommentArrGeneral[i].toString();
                            
                            var savedGC        = $scope.generalcommentOpen[0].toString();
                            
                            var matchString    = savedGC.indexOf(generalcomment);
                            
                            if(matchString != -1)
                            {
                                Helpers.prompt("One of the selected comment already exist in General Feedback.");
                                //$scope.autoCommentArrGeneral.splice(i, 1);
                            }
                            else
                            {
                                $scope.autoCommentArrGeneral1.push($scope.autoCommentArrGeneral[i]);
                            }
                        }
                        else
                        {
                            $scope.autoCommentArrGeneral1.push($scope.autoCommentArrGeneral[i]);
                        }
                    }
                }
                
                //$("#generalFeedback").focus();
                var htmlGeneral = $("#generalFeedback").html();
                var html        = htmlGeneral+'<ul>';

                for (i=0; i<$scope.autoCommentArrGeneral1.length; i++) 
                {
                    html+='<li>'+$scope.autoCommentArrGeneral1[i]+'</li>';
                }
                html+='</ul>';

                //var newhtml = html.replace(/(<br\s*\/?>){2,}/gi, '<br>');
                $("#generalFeedback").html(html);   

                $( "div#generalFeedback > div" ).each(function() {
                    var hastext = $(this).text().length != 0;
                    if(hastext) {
                    }else{ 
                        $(this).remove(); 
                    }
                });


                $('#generalFeedback').focus();
                var editableDivGeneral = document.getElementById("generalFeedback");
                cursorManager.setEndOfContenteditable(editableDivGeneral);
                $scope.autoCommentArrGeneral  = [];
                $scope.autoCommentArrGeneral1 = [];
                $scope.generalCommentOpen     = [];

                /*$('#generalFeedback').on('focus', function() {
                  alert('hi');
                })*/
            }
            else if($scope.autoCommentArrSpecific.length > 0)
            {

                for(var i = 0; i<$scope.autoCommentArrSpecific.length; i++)
                {
                    if($scope.autoCommentArrSpecific[i] != '' && $scope.autoCommentArrSpecific[i] != undefined )
                    {
                        if($scope.specificcommentOpen[0] != '' && $scope.specificcommentOpen[0] != undefined)
                        {
                            var specificCommentOpen = $scope.autoCommentArrSpecific[i].toString();
                            //console.log('specificCommentOpen=>'+specificCommentOpen);
                            var savedSC        = $scope.specificcommentOpen[0].toString();
                            //console.log('saved=>'+savedSC);
                            var matchString    = savedSC.indexOf(specificCommentOpen);
                            //console.log('match=>'+matchString);
                            if(matchString != -1)
                            {
                                Helpers.prompt("One of the selected comment already exist in this Specific Feedback.");
                                //console.log('matched');
                                //$scope.autoCommentArrSpecific.splice(i, 1);
                            }
                            else
                            {
                                $scope.autoCommentArrSpecific1.push($scope.autoCommentArrSpecific[i]);
                            }
                        }
                        else
                        {
                            $scope.autoCommentArrSpecific1.push($scope.autoCommentArrSpecific[i]);
                        }
                    }
                }

                $("#specificFeedback"+id).focus();
                var html = $("#specificFeedback"+id).html();
                
                var editableDiv1 = document.getElementById("specificFeedback"+id);

                if(editableDiv1.lastChild != 'null' && editableDiv1.lastChild != undefined && editableDiv1.lastChild != '')
                {
                    var getBr = editableDiv1.lastChild.tagName;

                    if(html != '' && getBr != 'BR')
                    {
                        html += '</br>';
                    }
                }
                for (i=0; i<$scope.autoCommentArrSpecific1.length; i++) 
                {
                    
                    html+=$scope.autoCommentArrSpecific1[i]+'</br>';
                }
                //var newhtml = html.replace(/(<br\s*\/?>){2,}/gi, '<br>');
                var newhtml = html.replace(/(<br>)+/g, '<br>');
                $("#specificFeedback"+id).html(newhtml);

                $( "div#specificFeedback"+id+" > div" ).each(function() {
                    var hastext = $(this).text().length != 0;
                    if(hastext) {
                        
                    }else{ 
                        $(this).remove(); 
                    }
                });
                
                $scope.autoCommentArrSpecific  = [];
                $scope.autoCommentArrSpecific1 = [];
                $scope.compareCommentSpecific  = [];


              
                var getHtml = $("#specificFeedback"+id).html();
                var newhtml = getHtml.replace(/(<br>)+/g, '<br>');
                $("#specificFeedback"+id).html();
                $("#specificFeedback"+id).html(newhtml);

                var editableDiv = document.getElementById("specificFeedback"+id);
                cursorManager.setEndOfContenteditable(editableDiv);
                
            }
            //$scope.getActiveElementId      = '';
        }
        else
        {
            $("#autocomment").show();
            //$("#generalFeedback").prop('disabled','true');
            $("#generalFeedback").removeAttr('contenteditable');
            $("#specificFeedback"+id).removeAttr('contenteditable');
            //$("#autobtn").focus();
            //alert('hello');
            $(".auto-comment-btn").css('left', '33%');
            $(".auto-comment-body").scrollTop(0);
            $scope.generalcommentOpen = [];
            var generalcommentText = $("#generalFeedback").text();
            if(generalcommentText != '')
                $scope.generalcommentOpen.push(generalcommentText);

            $scope.specificcommentOpen = [];
            var specificcommentText = $("#specificFeedback"+id).text();
            if(specificcommentText != '')
                $scope.specificcommentOpen.push(specificcommentText);

            /*if($scope.getActiveElementId != '')
            {*/
                //$('input:checkbox').removeProp('checked');
                $("input[name=autocomment]").prop('checked', false); 
                $(".rubric").css('z-index',5);
                $(".auto-comment").css('z-index',6);

                $(".auto-comment").animate({'left':'50%'},'linear');
                $scope.autocomment = 'Close';            
                $('.fa-square').on('click',function(){
                    var parent = $(this).parent().parent();
                    parent.find('.fa-square').removeClass('fa-check-square');
                    $(this).addClass('fa-check-square');
                });
            //}
        }
    }
    $scope.hideAutoComment = function()
    {
        $(".rubric").css('z-index',5);
        $(".auto-comment").css('z-index',5);

        $('.fa-square').off('click');
        $(".auto-comment").animate({'left':'100%'},'linear');
        $scope.autocomment = 'Auto Comment';            
    }
   
    /*
	 *  function description :  saves/submits the essay feedback
	 *  param1 : mode , silent if it is automatically calls
	 *  param2 : isSubmitted is 1 if it is for submit essay request 
	 *	@return  none 
	 *
	 */
	
	
	$scope.saveFeedback = function(mode,isSubmitted)
    {
        $scope.submitMode=mode;
        $scope.setInlineComments();
        
        if ($("#essayScore").val().trim() == "") {
            $scope.showPrompt('You cannot leave the score blank.');
            $("#essayScore").focus();
            return;
        } else if (isNaN($("#essayScore").val().trim())) {
            $scope.showPrompt('Score has to be numeric. Please add a valid score.');
            $("#essayScore").focus();
            return;
        } else if ($("#essayScore").val().trim() * 1 > 10 || $("#essayScore").val().trim() * 1 < 0) {
            $scope.showPrompt('Score has to be between 0 and 10. Please add a valid score.');
            $("#essayScore").focus();
            return;
        }
        if (mode == 1 && $("#essayScore").val().trim() * 1 == 0) {        // stat==1 && 
            $scope.showPrompt('You seem to have not graded this essay. Score has to be greater than 0.');
            $("#essayScore").focus();
            return;
        }
        if($scope.newcomment){
            $scope.showPrompt('Please save the added comment.');
            $("#essayScore").focus();
            return;
        }

		if (mode == 1 && isSubmitted == undefined) {
				Helpers.prompt({
					text : "'Are you sure you want to submit the feedback and score? You will not be able to edit it once you submit it.'",
					buttons : {
						'OK' :  function(){angular.element($("#essay_evaluation")).scope().saveFeedback(mode,1)},
						'Cancel' : function(){},
					},
					'force': true
				});
				return;
		} else if ($scope.submitMode == 1.5) {
            $scope.submitMode = 1;
        }

        $scope.disableSubmission=true;
        var eScore = $("#essayScore").val().trim()*1;
       
        /*data={
            'essaySpecificFeedback':$scope.sfbMaster ,
            'essayScoreID':eScoreID,
            'status':mode,
            'generalFeedback':$("#generalFeedback").val(),
            'rubricSc': rubricVals.join(','),
            'essayScore':eScore,
			'isFeedbackSaved':1
        };*/
        data={
            'essaySpecificFeedback':$scope.sfbMaster ,
            'essayScoreID':eScoreID,
            'status':mode,
            'generalFeedback':$("#generalFeedback").html().trim(),
            'rubricSc': rubricVals.join(','),
            'essayScore':eScore,
            'isFeedbackSaved':1
        };
        
        $http({
              method  : 'POST',
              url     : Helpers.constants['CONTROLLER_PATH'] + 'essayAllotment/saveFeedBack',
              data    : $.param({'postData':data}), //forms user object
              headers : {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'} 
         }).success(function(data) {
                if(Helpers.ajax_response( '' , data , [] )){
                         $scope.saveFeedbackCallBack($.parseJSON(data.result_data));
                  }
          }).error(onAjaxError);
    };
    
    
	$scope.saveFeedbackCallBack=function(data, extraParams){
        $("#generalFeedback").html("");
        if (data.data == "success") {
            $scope.closeFeedback();
        } else {
           $scope.disableSubmission=true;
           $("#closeBtn").attr("disabled",false);
           
        } 

    };

    $scope.closeFeedback = function(){
        showTeacherAllotedEssaysPage();
        //showTeacherAllotmentViewPage();
        currentQuestion.qID    = undefined;
        sessionData.currentLocation.type     = 'essayReview';
        $("#v2-showEssayData").click();
    };
	
	$scope.showPrompt = function(msg){
		Helpers.prompt({
				text : msg,
				buttons : {
					'OK' : function(){
						Helpers.close_prompt();
					}
				},
				'force': true 
         });
	}

	/*
	 *  function description :  highlights the user selected text while clicking add comment button
	 *  param1 : sI , start index of selected text
	 *  param2 : eI, end index of the selected text 
	 *	@return  none 
	 *
	 */
	
	
	$scope.selectHTML=function(sI,eI){
		try {
			if (window.ActiveXObject) {
				//var c = document.selection.createRange();
                var range = essaySelection;
                var html = $("#essay_detail").html();

                content = range.extractContents(),
                span = document.createElement('SPAN');
                //span = document.createElement('DIV');
                span.className='startIndex-'+sI+' endIndex-'+eI+' span-like-div';
                span.style.background = 'pink';
               
                span.appendChild(content);
                var htmlContent = span.innerHTML;
                range.insertNode(span);
                if($(".span-like-div > li").length > 0 || $(".span-like-div > ul > li").length > 0)
                {
                    $("#essay_detail").html('')
                    $("#essay_detail").html(html);

                    Helpers.prompt("Cannot comment on more then 1 bullet point together");
                    return false;
                }
				//return c.htmlText;
			}

			if (window.getSelection) {
				var range = essaySelection;
                
                var html = $("#essay_detail").html();
                
				content = range.extractContents(),

				span = document.createElement('SPAN');
                
				span.className = 'startIndex-'+sI+' endIndex-'+eI+' span-like-div';
				span.style.background = 'pink';
				span.appendChild(content);
                

				range.insertNode(span);
                
                if($(".span-like-div > li").length > 0 || $(".span-like-div > ul > li").length > 0)
                {
                    $("#essay_detail").html('')
                    $("#essay_detail").html(html);

                    Helpers.prompt("Cannot comment on more then 1 bullet point together");
                    return false;
                }
			}    
			return nNd.innerHTML;
		} catch (e) {
			if (window.ActiveXObject) {
				return document.selection.createRange();
			} else {
				return getSelection();
			}
		}
	}

	/*
	 *  function description :  sets the rubric check box selection as per the passed array
	 *  param1 : array , rubric array 
	 *	@return  none 
	 *
	 */
	
	
	$scope.setRubric=function(rubricArr) {
        $(rubricArr).each(function(index) {
            rubricVals[index] = rubricArr[index] * 1
        });
        for (var i = 0; i < 7; i++) {
            if (rubricVals[i] >= 0) 
                $("#r" + (i + 1) + rubricVals[i]).addClass("fa-check-square");
            else if($(".fa-square[name='c"+(i + 1)+"']").hasClass("fa-check-square"))
                $(".fa-square[name='c"+(i + 1)+"']").removeClass("fa-check-square");
        }
    }

    $scope.getTextNodesIn = function (node) {
        var textNodes = [];
        if (node.nodeType == 3) {
            textNodes.push(node);
        } else {
            var children = node.childNodes;
            for (var i = 0, len = children.length; i < len; ++i) {
                textNodes.push.apply(textNodes, $scope.getTextNodesIn(children[i]));
            }
        }
        return textNodes;
    }

    /*
	 *  function description :  sets the selection range for the passed document element between start index and end endex
	 *  param1 : el , document element in which words need to be selected
	 *  param2 : start , from where in the document selection need to be started 
	 *  param3 : end , to where the document selection need to be ended 
	 *	@return  none 
	 *
	 */
	
	$scope.setSelectionRange = function(el, start, end, spellWord) {
        if (document.createRange && window.getSelection) {
            
            var range = document.createRange();
            range.selectNodeContents(el);

            var textNodes = $scope.getTextNodesIn(el);
            
            var foundStart = false;
            var charCount = 0, endCharCount;
            //start = adjustOffset(el, start);
			//end = adjustOffset(el, end);
			for (var i = 0, textNode; textNode = textNodes[i++]; ) {
                endCharCount = charCount + textNode.length;
                if (!foundStart && start >= charCount && (start < endCharCount || (start == endCharCount && i <= textNodes.length))) {
					range.setStart(textNode, start - charCount);
                    foundStart = true;
                }
                if (foundStart && end <= endCharCount) {
					range.setEnd(textNode, end - charCount);
                    break;
                }
                charCount = endCharCount;
                
            }
			//var range = dig(el,start,end);
            var sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
            
        } else if (document.selection && document.body.createTextRange) {
            var textRange = document.body.createTextRange();
            textRange.moveToElementText(el);
            textRange.collapse(true);
            textRange.moveEnd("character", end);
            textRange.moveStart("character", start);
            textRange.select();
        }
    }

    /*
	 *  function description :  gets the selected range object and highlights the text for which range object is created
	 *  param1 : color , color to use for highlighting
	 *	@return  none 
	 *
	 */
	
	$scope.makeEditableAndHighlight = function(colour,start,end, spellWord) {
        
        var content,span,sel;
        sel = window.getSelection();
        if (sel.rangeCount && sel.getRangeAt) {
            range = sel.getRangeAt(0);
        }
        if (range) {
            sel.removeAllRanges();
            sel.addRange(range);
        }
        // Use HiliteColor since some browsers apply BackColor to the whole block
        
        if(typeof window.getSelection().anchorNode.parentElement != 'undefined')
			var cloneContent = window.getSelection().focusNode.parentElement;
		else
			var cloneContent = window.getSelection().focusNode.parentNode; 
        var range = window.getSelection().getRangeAt(0);

		var regex = new RegExp('\\b' + range.toString());
		var commonAncestorContainer=range.commonAncestorContainer.toString();
		
		
		if(cloneContent.outerHTML.search(regex) == -1 && commonAncestorContainer != '[object HTMLULElement]'){
			$scope.highlightTagStrings(start,end);
			sel.removeAllRanges();
			return;	
		}

        

        if(commonAncestorContainer != '[object Text]' && commonAncestorContainer != '[object HTMLParagraphElement]' && commonAncestorContainer != '[object HTMLElement]')
        {

			span = document.createElement('SPAN');
            //span = document.createElement('DIV');
            //span.className='startIndex-'+start+' endIndex-'+end+' span-like-div 1'+spellWord+' ';
            span.className='startIndex-'+start+' endIndex-'+end+' span-like-div';
            span.style.background = 'pink';
            //span.style.display = 'inline-block';
            //span.style.margin-bottom = '2px';
            span.innerHTML = range.toString();
            //cloneContent.outerHTML.replace(range.toString(),span.outerHTML);
            $(cloneContent).replaceWith(cloneContent.outerHTML.replace(range.toString(),span.outerHTML));
            sel.removeAllRanges();
			return;
        }
		
        
		content = range.extractContents();
        span = document.createElement('SPAN');
        //span = document.createElement('DIV');
        //span.className='startIndex-'+start+' endIndex-'+end+' span-like-div 1'+spellWord+'';
        span.className='startIndex-'+start+' endIndex-'+end+' span-like-div';
        span.style.background = 'pink';
        //span.style.display = 'inline-block';
        //span.style.margin-bottom = '2px';
        span.appendChild(content);
        var htmlContent = span.innerHTML;
        range.insertNode(span);

        /*added by nivedita for highlighting all the occurance of the selected text*/
        if(cloneContent.outerHTML.search(regex) != -1)
        {
            /*var sentences = document.querySelector('#essay_detail');
            var target = range.toString();
            var text = sentences.textContent;
            var regex = new RegExp('('+target+')', 'ig');

            if(text.search(target) !=-1)
            {
                text = text.replace(regex, '<span style="background:pink;">$1</span>');
                sentences.innerHTML = text;
            }*/
        }
        /*end*/

        sel.removeAllRanges();

        

    }

    /*
	 *  function description :  highlights the word for the selected word which is set using the setSelectionRange
	 *  param1 : color , color to use for highlighting
	 *	@return  none 
	 *
	 */
	
	$scope.highlight = function(colour,start,end,spellWord) {
        var range, sel;
        if (window.getSelection) {
            // IE9 and non-IE
            try {
                //if (!document.execCommand("backColor", false, colour)) {
                    $scope.makeEditableAndHighlight(colour,start,end,spellWord);
                //}
            } catch (ex) {
                $scope.makeEditableAndHighlight(colour,start,end,spellWord)
            }
        } else if (document.selection && document.selection.createRange) {
            // IE <= 8 case
            range = document.selection.createRange();
            range.execCommand("backColor", false, colour);
        }
    }

    /*
	 *  function description :  selects the range between start index and end index and highlights the word
	 *  param1 : id , document id for which highlighting need to done
	 *  param2 : start , start index of the highlighting text 
	 *  param3 : end , end index of the highlighting text
	 *	@return  none 
	 *
	 */
	
	
	$scope.selectAndHighlightRange=function(id, start, end, spellWord) {
        $scope.setSelectionRange(id, start, end, spellWord);
		$scope.highlight("pink",start, end, spellWord);
	}
	
	/*
	 *  function description :  returns the start index and end index based on the selection made by user
	 *  param1 : element , for which word need to highlighted
	 *	@return  array , of start index and end index of selected word 
	 *
	 */
	
	$scope.getSelectionCharOffsetsWithin = function(element){

        var start = 0, end = 0;
        var sel, range, priorRange;
        if (typeof window.getSelection != "undefined") {
            range = essaySelection;
            priorRange = range.cloneRange();
            priorRange.selectNodeContents(element);
            priorRange.setEnd(range.startContainer, range.startOffset);
            start = priorRange.toString().length;
            end = start + range.toString().length;
        } else if (typeof document.selection != "undefined" &&
                (sel = document.selection).type != "Control") {
            range = sel.createRange();
            priorRange = document.body.createTextRange();
            priorRange.moveToElementText(element);
            priorRange.setEndPoint("EndToStart", range);
            start = priorRange.text.length;
            end = start + range.text.length;
        }
            return {
            start: start,
            end: end
             };
    }
	
	/*
	 *  function description :  highlights the word using rangy object
	 *	@return  none 
	 *
	 */
	
	$scope.highlightTagStrings=function(startIndex,endIndex) {
        var selection,selectedRange;
        selection = rangy.getSelection(); //console.log(rSel);
        var txt = selection.toHtml();
        selectedRange = selection.getRangeAt(0);
        selectedRange.deleteContents();
        
        var node = selectedRange.createContextualFragment('<span style="background-color:pink" class="startIndex-' + startIndex + ' endIndex-' + endIndex + ' span-like-div">' + txt + '</span>');
        //var node = selectedRange.createContextualFragment('<div style="background-color:pink;" class="startIndex-' + startIndex + ' endIndex-' + endIndex + ' span-like-div">' + txt + '</div>');
        selectedRange.insertNode(node);
        selection.removeAllRanges();
        return;
    }



   
    //$scope.getEssayDetails();
}]);


(function( cursorManager ) {

    var voidNodeTags = ['AREA', 'BASE', 'BR', 'COL', 'EMBED', 'HR', 'IMG', 'INPUT', 'KEYGEN', 'LINK', 'MENUITEM', 'META', 'PARAM', 'SOURCE', 'TRACK', 'WBR', 'BASEFONT', 'BGSOUND', 'FRAME', 'ISINDEX'];

    Array.prototype.contains = function(obj) {
        var i = this.length;
        while (i--) {
            if (this[i] === obj) {
                return true;
            }
        }
        return false;
    }

    //Basic idea from: http://stackoverflow.com/questions/19790442/test-if-an-element-can-contain-text
    function canContainText(node) {
        if(node.nodeType == 1) { //is an element node
            return !voidNodeTags.contains(node.nodeName);
        } else { //is not an element node
            return false;
        }
    };

    function getLastChildElement(el){
        var lc = el.lastChild;
        while(lc && lc.nodeType != 1) {
            if(lc.previousSibling)
                lc = lc.previousSibling;
            else
                break;
        }
        return lc;
    }

    cursorManager.setEndOfContenteditable = function(contentEditableElement)
    {

        while(getLastChildElement(contentEditableElement) &&
              canContainText(getLastChildElement(contentEditableElement))) {
            contentEditableElement = getLastChildElement(contentEditableElement);
        }

        var range,selection;
        if(document.createRange)//Firefox, Chrome, Opera, Safari, IE 9+
        {    
            range = document.createRange();//Create a range (a range is a like the selection but invisible)
            range.selectNodeContents(contentEditableElement);//Select the entire contents of the element with the range
            range.collapse(false);//collapse the range to the end point. false means collapse to end rather than the start
            selection = window.getSelection();//get the selection object (allows you to change selection)
            selection.removeAllRanges();//remove any selections already made
            selection.addRange(range);//make the range you have just created the visible selection
        }
        else if(document.selection)//IE 8 and lower
        { 
            range = document.body.createTextRange();//Create a range (a range is a like the selection but invisible)
            range.moveToElementText(contentEditableElement);//Select the entire contents of the element with the range
            range.collapse(false);//collapse the range to the end point. false means collapse to end rather than the start
            range.select();//Select the range (make it the visible selection
        }
    }
    //console.log(contentEditableElement.lastChild.tagName.toLowerCase());
}( window.cursorManager = window.cursorManager || {}));

function isBr(el) {
    return el && el.nodeType == 1 && el.tagName == "BR";
}
function isFirstOrLastChildBr(el) {
    var last = el.lastChild;
    //if (isWhitespaceNode(last)) last = last.previousSibling;
    if (isBr(last)) 
        return true;
    else
        return false;
}

englishInterface.directive('evaluateComment', function() {

    return {
        restrict : 'EA',
        scope : {
            save: '&',
            view: '&',
            remove: '&',
            toggle: '=',
            commenttext: '=',
            commentindex: '=',
            index: '=',
            newcomment: '=',
            commentmodel: '=',
            essaymode: '=',
            commentlabel: '=',
        },
       /* template : ['<div class="commentDiv">',
                        '<div class="row comment-{{ commentindex }}" ng-show="commentindex  ==  index  && toggle" >',
                            '<div class="col-md-10">',
                                '<textarea class="form-control" ng-model="commentmodel"></textarea>',
                            '</div>',
                            '<div class="col-md-2 commentIcon">',
                                '<span ng-click="save({indexKey:index,commentText:commentmodel})"><i class=" fa-2x fa fa-floppy-o"></i></span><!-- Save -->',
                                '<span ng-hide="newcomment" ng-click="view({value:\'cancel\',indexKey:index})"><i class=" fa-2x fa fa-times"></i></span><!-- Cancel -->',
                                '<span ng-show="newcomment" ng-click="remove({indexKey:index})"><i class=" fa-2x fa fa-trash"></i></span>',
                            '</div>',
                        '</div>',
                        '<div class="row comment-{{ commentindex }}"  ng-hide=" commentindex  == index && toggle ">',
                            '<div class="col-md-10">',
                                '<div class="evaluation-comment" label="{{ commentlabel }}">{{ commentmodel }}</div>',
                            '</div>',
                            '<div class="col-md-2 commentIcon">',
                                '<span ng-click="view({value:\'edit\',indexKey:index})" ng-hide="essaymode"><i class=" fa-2x fa fa-pencil"></i></span><!-- Edit -->',
                                '<span ng-click="remove({indexKey:index})" ng-hide="essaymode"><i class=" fa-2x fa fa-trash"></i></span><!-- Delete -->',
                            '</div>',
                        '</div>',
                    '</div>'
                   ].join(''),*/
        template : ['<div class="commentDiv">',
                        '<div class="row comment-{{ commentindex }}" ng-show="commentindex  ==  index  && toggle" >',
                            '<div class="col-md-10">',
                                '<div contenteditable="true" ng-click="getElement({indexKey:index})"  id="specificFeedback{{ index + 1 }}" class="form-control" ng-model="commentmodel" editable ng-bind-html="commentmodel"></div>',
                            '</div>',
                            '<div class="col-md-2 commentIcon">',
                                '<span ng-click="save({indexKey:index,commentText:commentmodel})"><i class=" fa-2x fa fa-floppy-o"></i></span><!-- Save -->',
                                '<span ng-hide="newcomment" ng-click="view({value:\'cancel\',indexKey:index})"><i class=" fa-2x fa fa-times"></i></span><!-- Cancel -->',
                                '<span ng-show="newcomment" ng-click="directRemoveComment({indexKey:index})"><i class=" fa-2x fa fa-times"></i></span>',
                            '</div>',
                        '</div>',
                        '<div class="row comment-{{ commentindex }}"  ng-hide=" commentindex  == index && toggle ">',
                            '<div class="col-md-10">',
                                '<div class="evaluation-comment" id="specificFeedbackDiv{{ index + 1 }}" label="{{ commentlabel }}" ng-bind-html="commentmodel"></div>',
                            '</div>',
                            '<div class="col-md-2 commentIcon">',
                                '<span ng-click="view({value:\'edit\',indexKey:index})" ng-hide="essaymode"><i class=" fa-2x fa fa-pencil"></i></span><!-- Edit -->',
                                '<span ng-click="removeCommentConf({indexKey:index})" ng-hide="essaymode"><i class=" fa-2x fa fa-trash"></i></span><!-- Delete -->',
                            '</div>',
                        '</div>',
                    '</div>'
                   ].join(''),

                link: function (scope, element) {

                    scope.getElement = function(indexKey){

                        indexKey = indexKey.indexKey;
                        indexKey = indexKey + 1;
                        var scope = $("#essay_evaluation").scope();
                        var specificElement = 'specificFeedback'+indexKey;
                        scope.getActiveElement(specificElement);
                        
                    }
                    
                    scope.removeCommentConf = function (indexKey,isDeleteConfirm) {
                        
                        if (isDeleteConfirm == undefined) {
                                Helpers.prompt({
                                    text : "Are you sure you want to delete this comment?",
                                    buttons : {
                                        'OK' :  function(){
                                            //angular.element($("#essay_evaluation")).scope().removeComment(indexKey,1);
                                            removeComment(indexKey,1);
                                        },
                                        'Cancel' : function(){},
                                    },
                                    'force': true
                                });
                                return;
                        }
                    };

                    scope.directRemoveComment = function (indexKey) {
                         //removeComment(indexKey,1);
                        indexKey = indexKey.indexKey;
                        var scope = $("#essay_evaluation").scope();
                        
                        var commentFor = scope.comments[indexKey].comment.commentFor;
                        var indexArr   = commentFor.match(/\[(.*?)\]/);
                        commentFor     = indexArr[1];
                        var lbl        = scope.comments[indexKey].comment.commentRange;
                        var lblArr     = lbl.split('~');
                        var startLbl   = lblArr[0];
                        var endLbl     = lblArr[1];
                        $('.startIndex-' + startLbl + '.endIndex-'+endLbl).css("background-color", "");
                        scope.comments.splice(indexKey,1);
                        
                        if(scope.newcomment)
                            scope.newcomment=false;
                    };

                    function removeComment(indexKey,isDeleteConfirm)
                    {
                        
                        indexKey = indexKey.indexKey;
                        
                        var scope = $("#essay_evaluation").scope();
                        if(isDeleteConfirm == 1)
                        {
                            
                            var commentFor = scope.comments[indexKey].comment.commentFor;
                            var indexArr   = commentFor.match(/\[(.*?)\]/);
                            
                            commentFor     = indexArr[1];
                            
                            var lbl        = scope.comments[indexKey].comment.commentRange;
                            
                            var newArr      = lbl.split('-');
                            var newArray =  newArr.filter(function(v){return v!==''});
                            
                            for (var i = 0; i < newArray.length; i++) 
                            {

                                var lblArr     = newArray[i].split('~');
                                var startLbl   = lblArr[0];
                                var endLbl     = lblArr[1];    
                                $('.startIndex-' + startLbl + '.endIndex-'+endLbl+':first').css("background-color", "");
                                $('.startIndex-' + startLbl + '.endIndex-'+endLbl+':first').removeClass('startIndex-' + startLbl+' endIndex-' + endLbl);
                            }

                            for (var j = 0; j < scope.comments.length; j++) 
                            {
                                
                                var indArr = scope.comments[j].comment.commentFor.match(/\[(.*?)\]/);
                                if(indArr[1].toLowerCase() == commentFor.toLowerCase())
                                {
                                   scope.comments.splice(j,1);
                                   scope.saveComment(j);
                                }
                                
                            }
                            
                            //scope.comments.splice(indexKey,1);
                            //scope.saveComment(indexKey);
                            if(scope.newcomment)
                                scope.newcomment=false;
                        }
                    };
                }
        
    }
});

englishInterface.directive("editable", function() {
  return {
    restrict: "A",
    require: "^?ngModel",
    link: function(scope, element, attrs, ngModel) {
      function read() {
        ngModel.$setViewValue(element.html());
      }

      ngModel.$render = function() {
        element.html(ngModel.$viewValue || "");
      };

      element.bind("blur change", function() {
        scope.$apply(read);
      });
    }
  };
});

//call back on ajaxees. Waits for all the ajaxes to complete and then initiates the application.
function ajaxComplete() {
    ajaxLoadCount++;
    if (ajaxLoadCount >= 4) {
        /*
         * this is the only ajax request that requires cache mentioned explicity, because setup is being called in initialize.
         * This piece of code is arbitrarily placed here. The request can be set in the controller and the dom can be defined in setDom.
         * Bindings in set Bindings.
         */
        $($('.sidebar-nav li')[0]).addClass('active');
        if(sessionData.category == 'STUDENT')
        {
            $.ajax({
                context : this,
                //url : Helpers.constants['CONTROLLER_PATH'] + 'buddy/getBuddyDetails/' + sessionData.userName,
                url : Helpers.constants['CONTROLLER_PATH'] + 'buddy/getBuddyDetails/',
                cache : false
            }).done(function(response) {
                Helpers.ajax_response( getBuddyDetails , response , [] );
            });
        }
        else
        {
            unAngularApp.initialize();
        }
    }
   if (sessionData.category=='School Admin' && sessionData.subcategory=='All') {
           $('#sbi_settings').attr("data-title","You don't have necessary rights to perform this action.");
    }
}

function getBuddyDetails(response , extraParams)
{
    if(sessionData.category == "STUDENT")
    {

        if (response == "" || response == null) {
            var overlay = document.createElement("div");
            overlay.innerHTML = '<div class="buddyGirl"></div><div class="buddyBoy"></div>';
            $(overlay).addClass("buddyOverlay appContainers");
            $(overlay).insertBefore($(".navbar"));

            function onUserSelection() {
                $(".buddyOverlay").hide("scale",function(){
                    $(".buddyOverlay").remove();
                });

                $.ajax({
                    context : unAngularApp,
                    //url : Helpers.constants['CONTROLLER_PATH'] + 'buddy/setBuddyDetails/' + sessionData.userName + '/' + buddyTemp
                    url : Helpers.constants['CONTROLLER_PATH'] + 'buddy/setBuddyDetails/' + buddyTemp
                }).done( function(data) 
                        { 
                            if(Helpers.ajax_response( '' , data , [] )){
                                unAngularApp.initialize();
                            }
                        });
            }

            $(".buddyGirl").click(function() {
                buddyTemp = 'girl';
                onUserSelection();
            });

            $(".buddyBoy").click(onUserSelection);
        } else {
            
            buddyTemp = response;
            unAngularApp.initialize();
        }
    }
    else
    {
        unAngularApp.initialize();
    }

    $(".characterArrow").css({
        "background-image" : "url("+ Helpers.constants['THEME_PATH'] +"img/"+Helpers.constants['UI_THEME_IMAGES']+"/classroomImages/" + buddyTemp + "-Arrow.png)"
    });
}

var unAngularApp = {
    
    initialize : function() {

        $.ajaxSetup({
            cache : false,
            success : function() {
                NO_INTERNET = false;
            }
        });
        if (pageMode !== 'preview') {
            if (sessionData.userName == false || sessionData.userID == false) {
                Helpers.prompt({
                    text : 'Invalid Session',
                    buttons : {
                        'OK' : function() {
                            
                            tryingToUnloadPage = true;
                        }
                    },
                    noClose : true,
                });
                return;
            }
        }
        sessionData.currentLocation = {
            'type' : 'home',
            'value' : '',
            'location' : ''
        };

        Helpers.valid_student();
        startSnowFall();
        sessionData.questionsAttempted = 0;
        sessionData.classRoomStarted = false;

        pageMode === "preview" || ( pageMode = sessionData.mode);
        
        this.createComponents();
        this.setBindings();
    
        switch(pageMode) {
        case 'diagnostic':
            Helpers.loadJs(Helpers.constants['THEME_PATH'] + 'js/Language/diagnostic.js?ver2', function() {
                diagnosticTest.fetchQuestions();
            });
            break;
        case 'preview':
            Helpers.loadJs(Helpers.constants['THEME_PATH'] + 'js/Language/preview.js?ver2', function() {
                preparePreview();
            });
            break;
        case 'report':
            Helpers.loadJs(Helpers.constants['THEME_PATH'] + 'js/Language/diagnostic.js?ver2', function() {
                diagnosticTest.fetchQuestions();
                diagnosticTest.initializeReport();
            });
            break;
        case 'normal':
            this.startInterval();
            $('#loader').hide();
            $('#home').show();
            sessionData.currentLocation.location='home_page';
			$("#sidebarToggle").css('visibility','hidden');
            hidePluginIfAny();
            this.setDom();
            // if(!sessionData.completeAtHome || !sessionData.complete) // No need to show if timeSpentAtHome>45min or timeSpent>60min
            if(sessionData.category.toLocaleLowerCase()!="teacher" && sessionData.category.toLocaleLowerCase()!="school admin" && sessionData.category.toLocaleLowerCase()!="admin"){
                flashTip('sbi_the_classroom');
                Helpers.toast('<div class="prompt-heading"><button class="close-prompt toast-close" prompt-close=\'#toast\'><i class="fa fa-close"></i></button> </div>This session may contain audio. You will be notified when to use headphones.');
            }
            break;
        }
        
        var isMobile=this.isMobile();
        $("#sbi_essay_writer").on({
            mouseenter: function () {
                var checkEssayActive = $("#sbi_essay_writer").attr('data-tooltip');
                if (typeof checkEssayActive !== "undefined" && checkEssayActive !== null) {
                    if(isMobile===false){
                       flashTip('sbi_essay_writer', $("#sbi_essay_writer").attr('data-tooltip'));
                    }
                }
            },
            mouseleave: function () {
                
            }
        });
        if(isMobile===true){
            var checkEssayActive = $("#sbi_essay_writer").attr('data-tooltip');
            if (typeof checkEssayActive !== "undefined" && checkEssayActive !== null) {
                flashTip('sbi_essay_writer', $("#sbi_essay_writer").attr('data-tooltip'));
            }
            
        }
    },
    isMobile : function(){
         var isMobile = false; //initiate as false
// device detection
                                if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
                                        || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0, 4)))
                                    isMobile = true;
                                return isMobile;
    },
    setDom : function() {
        $('#mainContentContainer > div').hide();
        $('.none').hide();
        $('.all').show();

        //  Load teacher Home screen
        if(sessionData.category.toLocaleLowerCase()=="teacher" || 
            sessionData.category.toLocaleLowerCase()=="school admin" || 
            sessionData.category.toLocaleLowerCase()=="admin"){
            showTeacherHomePage();
            showTeacherDashboard();
            $('#sessionReportButton').hide();
        }else{
            $('.homepage').show();
            $('#home').show();
            $('#sessionReportButton').show();
            showskillometer();
        }

        hidePluginIfAny();
        $("#mainTimer").show();
        Helpers.preloadImages([
            Helpers.constants['THEME_PATH'] + 'img/Language/match/blueCmark.png',
            Helpers.constants['THEME_PATH'] + 'img/Language/match/greenCmark.png',
            Helpers.constants['THEME_PATH'] + 'img/Language/match/puzzle_left.png',
            Helpers.constants['THEME_PATH'] + 'img/Language/match/puzzle_right.png',
            Helpers.constants['THEME_PATH'] + 'img/Language/match/wrongCmark.png'
        ]);
        sizeQuoteContainer();
        $('#usageReportStartDate').datepicker();
        $('#usageReportEndDate').datepicker();
        Helpers.data.toastElement = $('#toast');
    },
    createComponents : function() {
        //skillometerObject = new SKILLOMETER(sessionData.userID, $('#skillometer')[0]);
        //skillometerObject = new SKILLOMETER($('#skillometer')[0]);
       

        passageObject = new Passage($('#passageContainer')[0], pageMode != 'diagnostic');
        passageObject.view.setOnPageChange(saveQuestion);
        
        // super question Object. -> Epic Win.
        questionObject = new Question($('#questionContainer')[0]);
        
        audioObject = new Audio($('#audioContainer')[0]);

    },
    startInterval : function() {
        timerIntervalId = setInterval(function() {
            sessionData.timeSpent++;
            
            if(idel_time_logout <= 0) // Check for inactive till 10 minutes.
            {
                
                clearInterval(timerIntervalId);  
                $(".english-content-wrapper").fadeOut(200);
                Helpers.prompt({
                    text : 'Mindspark has not detected any input from you in the last 10 minutes. You will be logged out.',
                    buttons : {
                        'OK' : function() {
                            tryingToUnloadPage = true;
                             logOut();
                            //logOut();
                        }
                    },
                    closeFunction : function(){
                        tryingToUnloadPage = true;
                        logOut();
                        //logOut();
                    },
                    noClose : true,
                });
                setTimeout(function(){
                    tryingToUnloadPage = true;
                    logOut();
                    //logOut();
                      
                },1000);
                return;
            }
            else if(inactive_time > 540 && inactive_interface == 0) // Check inactive time to 9 minutes
            {
                logOutReason = 4;
                Helpers.stop_inactive_interface_check();
                // show prompt.
                Helpers.prompt({
                    text : 'Mindspark has not detected any input from you in the last 9 minutes. You will be logged out in <span id="idealTimeRemaining" style="color:#F00;"></span> seconds.',
                    buttons : {
                        'Continue' : function() {
                            Helpers.check_inactive_interface();
                            idel_time_logout = 60;
                            inactive_time = 0;
                            inactive_interface = 0;
                        }
                    },
                    closeFunction : function(){
                        
                    },
                    noClose : true,
                });
                $('.ui-dialog-titlebar-close').hide();
                inactive_interface = 1;
                return;
            }
            else if(inactive_interface == 1)
            {
                $("#idealTimeRemaining").html(idel_time_logout);
                idel_time_logout--;
                return;
            }
            else
            {
                inactive_time++;
            }
            //timer main page
            sessionData.timeSpentInHour = Math.floor(sessionData.timeSpent / 3600);
            sessionData.timeSpentInMin = Math.floor((sessionData.timeSpent - (Math.floor(sessionData.timeSpent / 3600) * 3600)) / 60);
            $("#mainHour").html(sessionData.timeSpentInHour);
            if (sessionData.timeSpentInMin < 10)
                $("#mainMin").html('0' + sessionData.timeSpentInMin);
            else
                $("#mainMin").html(sessionData.timeSpentInMin);

            if(sessionData.category == 'STUDENT')
            {
                var timeLeft = ((sessionData.maxQuota * 60) - sessionData.timeSpent);
                var time = timeLeft/60;

                
                if(timeLeft == 120)
                {
                    var msg = "Hey! You've completed your Mindspark session for the day. You will be logged out after "+Math.round(time)+" minutes."
                    Helpers.prompt(msg);
                }
            }

             //added by nivedita on page load showing the alert
            if (sessionData.classComplete >= 180 && !sessionData.weekComplete && sessionData.category == 'STUDENT') 
            {
                sessionData.weekComplete = true;
                stopAndHideOtherActivities();
                unAngularApp.setDom();
                Helpers.prompt({
                    text : 'You have used the Classroom section for 180 minutes this week! Come back again next week to use the Classroom again!',
                    noClose : true
                });
            }
            //end
            
            //if (sessionData.timeSpent > (sessionData.totalTimeAllowedPerDay*60) && sessionData.category == 'STUDENT') 
            if (sessionData.timeSpent > (sessionData.maxQuota*60) && sessionData.category == 'STUDENT') 
            {
                clearInterval(timerIntervalId);
                sessionData.complete = true;
                $(".english-content-wrapper").fadeOut(200);
                Helpers.prompt({
                    text : 'You have completed your Mindspark quota for the day! You can login again tomorrow to enjoy Mindspark!',
                    buttons : {
                        'OK' : function() {
                            
                            tryingToUnloadPage = true;
                            //logOut();
                            logOutReason = 3;
                            logOutBtnClick();
                            //logOutBtnClick();
                        }
                    },
                    closeFunction : function(){
                        
                        tryingToUnloadPage = true;
                        //logOut();
                        logOutReason = 3;
                        logOutBtnClick();
                        //logOutBtnClick();
                    },
                    noClose : true,
                });
                setTimeout(function() {
                    $('.ui-dialog-titlebar-close').hide();
                    logOutReason = 3;
                    logOutBtnClick();
                    //logOutBtnClick();
                },5000);  
                $('.ui-dialog-titlebar-close').hide();
                return;
            }
            
            // opens classroom automatically if inactivity of 10 seconds
            if (sessionData.timeSpent > Helpers.constants.IDLE_TIME && sessionData.firstLoginToday && sessionData.category =='STUDENT') {
                if (!sessionData.classRoomStarted) {
                    sessionData.classRoomStarted = true;
                    Helpers.prompt({
                        text : 'You will now get some interesting passages and questions!',
                        buttons : {
                            'OK' : function(){
                                        $($('.sidebar-nav li')[1]).trigger('click');
                                        //added by nivedita, to close the dictionary
                                        var modal = document.getElementById('myModal');
                                        modal.style.display = "none";
                                        sessionData.currentLocation.location = '';
                                        $("#modalBlockerCommentSystem").hide();
                                        
                                    },
                        },
                        noClose : true
                    });    
                }
            }

            //1 hour session - 3600
            
            //if (sessionData.timeSpent > (sessionData.minTimeForClass * 60) && !sessionData.complete && sessionData.category == 'STUDENT') 
            /*if (sessionData.timeSpent > (sessionData.timeAllowedPerDay * 60) && !sessionData.complete && sessionData.category == 'STUDENT' && currentQuestion.completed) 
            {
                sessionData.complete = true;
                stopAndHideOtherActivities();
                unAngularApp.setDom();
                Helpers.prompt({
                    text : 'You have spent your '+sessionData.timeAllowedPerDay+' minutes in the Classroom today, so you\'re back at the home page! Come back to the Classroom tomorrow.',
                    noClose : true
                });
            }*/
        }, 1000);
    },
    setBindings : function() {
        $('#passageNext').bind('click', function() {
            $('.qtypeClLabelOpt').removeClass('bgcolorBlue').removeClass('bgcolorRed').removeClass('bgcolorGreen');
            $('.signCls').remove();
            $(this).hide();
            saveQuestion();
        });
        $('#gamePassageNext').bind('click', function() {
            $(this).hide();
           // saveQuestion();
            $.ajax({
                url : Helpers.constants['CONTROLLER_PATH'] + 'questionspage/checkActivityRatingCount',
                type : 'POST',
                data : {'contentID':currentQuestion.qID,'contentType':'gre'},
                success : function(response){
                    if(response){
                         rating_object = new RATING({ 
                            title : 'Would you like to rate this activity? (Optional)',
                            fade_element : '.gameContainers',
                            additional_info_star : 3,
                            title_on_select_star : 'Please tell us more! (Optional)',
                            additional_info:['Boring','Difficult','Easy','Too Long','Too Short','Slow'],
                            parameters : {
                                contentID : currentQuestion.qID,
                                contentType : 'gre',
                                rating : 0,
                                comment : '',
                                ratingReasonOther : ''
                            },
                            callback : {
                                callback_function : 'saveQuestion',
                                callback_parameters : '',
                            } 
                            
                        });
                    }
                    else
                        saveQuestion();
                 }
            })
            //saveQuestion();
        });

        /*
        var textarea = document.getElementById('essay');
        $(textarea).on("contextmenu",function(e){
            return false;
        });
        
        textarea.addEventListener('keydown', function(e) {
            clearTimeout(saveEssayIntervalId);
            // Helpers.checkNumWords.apply(this, [e]);
            var valid_keys = [8, 46];
            
            var wordCount = 0;
            if(textarea.value.trim() != '')
                wordCount = Helpers.checkMinMaxWords(textarea.value,"count",0);

            if(wordCount >= Helpers.constants.MAX_WORDS && valid_keys.indexOf(e.keyCode) == -1)
            {
                e.preventDefault();
                 Helpers.prompt('You have reached the maximum word limit.');
            }

        });
        */
        /*
        textarea.addEventListener('keyup', function(e) {
            clearTimeout(saveEssayIntervalId);
            saveEssayIntervalId = setTimeout(function() {
                saveEssay('silent');
            }, 3000);
            
            var count = 0;
            if(textarea.value.trim() != '')
                count = Helpers.checkMinMaxWords(textarea.value,"count",0);

            $("#essay_instructions .textAreaAfter").html("[ Words entered: "+count+" ]");
        });
        */
        $('.activityThumbnail').bind('click', function() {
            var key = $(this).attr('data-key');
            showGame(key);
        });

       $('#gameExitButton').bind('click', function() {

            sessionData.currentLocation.type = 'activity';
             $.ajax({
                url : Helpers.constants['CONTROLLER_PATH'] + 'questionspage/checkActivityRatingCount',
                type : 'POST',
                data : {'contentID':currentQuestion.qID,'contentType':'gre'},
                success : function(response){
                    if(response){
                         rating_object = new RATING({ 
                            title : 'Would you like to rate this activity? (Optional)',
                            fade_element : '.gameContainers',
                            additional_info_star : 3,
                            title_on_select_star : 'Please tell us more! (Optional)',
                            additional_info:['Boring','Difficult','Easy','Too Long','Too Short','Slow'],
                            parameters : {
                                contentID : currentQuestion.qID,
                                contentType : 'gre',
                                rating : 0,
                                comment : '',
                                ratingReasonOther : ''
                            },
                            callback : {
                                callback_function : 'activityCloseButtonAction',
                                callback_parameters : '',
                            } 
                            
                        });
                    }
                    else
                        activityCloseButtonAction();
           }
        })

            /*hidePluginIfAny();
            $("#gameFrame").attr('src','');
            $('.gameContainers').hide();
            $('#activitySelector').show();*/
     });
        
        $("#mainTimer").bind('click',function() {
            var str;
            if(!sessionData.timeSpentInHour)
            {
                if(sessionData.timeSpentInMin == 1)
                    str = 'You have been Mindspark-ing for '+sessionData.timeSpentInMin+' minute.'
                else
                    str = 'You have been Mindspark-ing for '+sessionData.timeSpentInMin+' minutes.'
            }
            else
            {
                if(sessionData.timeSpentInHour == 1)
                    str = 'You have been Mindspark-ing for '+sessionData.timeSpentInHour+' hour and '+sessionData.timeSpentInMin+' minutes.'
                else
                    str = 'You have been Mindspark-ing for '+sessionData.timeSpentInHour+' hours and '+sessionData.timeSpentInMin+' minutes.'
            }

            Helpers.prompt(str);
        });

        $(document).off('keypress').on('keypress', function(e){accessDocument(e)});
        
        $('.tableContainer').on('click', 'li.newEssay', function() {
            
            $(this).siblings().removeClass('active');
            $(this).addClass('active');
            $('#startEssay').show();
            sessionData.timeSpentInEssay=0; 
            $("#ownTopic").val('');  
            $(this).parent().navigateList();
        });
        $('.tableContainer').on('dblclick doubletap', 'li.newEssay', function() {
            clearTimeout(saveEssayIntervalId);
            $(this).siblings().removeClass('active');
            $(this).addClass('active');
            $('#startEssay').show();
            sessionData.timeSpentInEssay=0;   
            $('#startEssay').trigger('click');
        });
         $('.tableContainer').on('click', '.essaySummay', function() {
            $(this).siblings().removeClass('active');
            $(this).addClass('active');
            $(this).parent().navigateList();

        });
        if(!!navigator.platform && /iPad|iPhone|iPod/.test(navigator.platform))
        {
            $('.tableContainer').on('doubletap','.essaySummay',function(e){
                clearTimeout(saveEssayIntervalId);
                $(this).trigger('dblclick');
            });
            /*HIDE EXCEL DOWNLOAD BUTTON ON TEACHERINTERFACE FOR IPAD*/
            $("#download_excel_div").hide();
            /*END*/
        } 
        var ua = navigator.userAgent;
        if( ua.indexOf("Android") >= 0 )
        {
          var androidversion = parseFloat(ua.slice(ua.indexOf("Android")+8)); 
          if (androidversion < 4.1)
          {
                $('.tableContainer').on('doubletap','.essaySummay',function(e){
                    clearTimeout(saveEssayIntervalId);
                    $(this).trigger('dblclick');
                });
          }
        }
        $('.tableContainer').on('dblclick', '.essaySummay', function(e) {
            clearTimeout(saveEssayIntervalId);
            $(this).siblings().removeClass('active');
            $(this).addClass('active');
            $(".essay_writer").css('display','none');
            var curObj = ($(this).closest("div").attr('id'));
            var tmpCurDiv;
            if (curObj == "incompleteEssays") {
                tmpCurDiv = $('#incompleteEssays .active');
                sessionData.timeSpentInEssay=0;        
            } else if (curObj == "completeEssays") {
                tmpCurDiv = $('#completeEssays .active');
                sessionData.timeSpentInEssay=0;       
            } else if (curObj == "gradedEssays") {
                tmpCurDiv = $('#gradedEssays .active');
                sessionData.mode="report";
            }
            else
            {
                tmpCurDiv = $(this);
            } // uncomment this and comment below 'if' condition for enabling graded essays
            // if(curObj != "gradedEssays")
            // {
                var essayID = tmpCurDiv.attr('essay-id');
                var topicId = tmpCurDiv.attr('data-id');
                topicEssay = essayID;
                $.ajax({
                    //url : Helpers.constants['CONTROLLER_PATH'] + 'home/getEssayResponse/' + sessionData.userID + '/' + topicId + '/' + essayID,
                    url : Helpers.constants['CONTROLLER_PATH'] + 'home/getEssayResponse/' + topicId + '/' + essayID,
                    async:false
                }).done(function(response) {
                    Helpers.ajax_response( showCompletedEssay , response, [essayID, topicId, tmpCurDiv, curObj]);
                });
            // }
        });

        var valid_keys = [8, 46];
        $("#ownTopic").on('focus keypress',function(){
            $('#essaysToAttempt .active').removeClass('active');
            $("#startEssay").show();
        });
        $("#ownTopic").on('keydown', function(evt) {
            if($(this).val().length >= 100 && valid_keys.indexOf(evt.keyCode) == -1)
            {
                evt.preventDefault();
                Helpers.prompt('You have reached the maximum word limit.');
            }
        });

        $('#startEssay').on('click', function() {

            var saveCustomEssay = 0;
            var ownTopic = $("#ownTopic").val().trim();
            
            var selectedTopic = $('#essaysToAttempt .active').html();
            if($('#essaysToAttempt .active').length == 0 && ownTopic == '')
            {
                Helpers.prompt({
                    title : '',
                    text : 'Please select a topic or write your own topic',
                    noClose : true
                });
                return;
            }
            if(ownTopic != '' && $('#essaysToAttempt .active').length == 0)
            {
                selectedTopic = ownTopic; 
                saveCustomEssay = 1;
            }
            /*var specialChars = "!@#$^&%*() += -[]\/{}|:<>?,.";
            var desired      = selectedTopic.replace(/[^\w\s]/gi, '');*/
            selectedTopic    = selectedTopic.trim();

            var essayID = $('#essaysToAttempt .active').attr('data-id');
            var isForce = $('#essaysToAttempt .active').attr('essay-force');
            if(saveCustomEssay == 1 && essayID == undefined)
            {
            /*$('.appContainers').hide();
            $('.the_classroom').show();*/
                sessionData.timeSpentInEssay = 0;
                // Save the topic entered to essay list.
                var page = sessionData.currentLocation.type;
                $.ajax({
                    type : 'POST',
                    url : Helpers.constants['CONTROLLER_PATH'] + "home/saveUserEssayTopic",
                    data : { 'title' : selectedTopic, 'page' : page},
                }).done(function(data) {
                    Helpers.ajax_response(setEssayID, data, [selectedTopic]);
                });
                //start timer here
                startEssayInterval();
            }
            else
            {
                var questionObject = {
                    qID : essayID == undefined ? 0 : essayID,
                    qType : 'essay',
                    value : essayID + '||' + selectedTopic,
                    info : {
                        userResponse : '',
                        isForce:isForce
                    }
                };
                showAppContainer();
                setQuestion(questionObject);                
            }
        });
        function setEssayID(data,selectedTopic)
        {
            if(data.essayTitle)
                selectedTopic[0] = data.essayTitle;
            
            if(data.msg != '' && data.msg != undefined)
            {
                if(data.msg == 'inlist')
                {
                    var msg = 'This topic already exists!';
                }
                if(data.msg == 'Already')
                    var msg = 'You are already writting essay on this topic.';

                Helpers.prompt(msg);
            }

            if(data.userResponse != '' && data.userResponse != undefined)
            {
                var questionObject = {
                    qID : data.qid,
                    qType : 'essay',
                    value : data.qid + '||' + selectedTopic[0],
                    info : {
                        userResponse : data.userResponse,
                        //submitted : curObj == "completeEssays" ? true : false
                    }
                };
            }
            else if(data.qid != '' && data.qid != undefined)
            {
                var questionObject = {
                    qID : data.qid,
                    qType : 'essay',
                    value : data.qid + '||' + selectedTopic[0],
                    info : {
                        userResponse : '',
                        //submitted : curObj == "completeEssays" ? true : false
                    }
                };   
            }
            else
            {
                var questionObject = {
                    qID : data,
                    qType : 'essay',
                    value : data + '||' + selectedTopic[0],
                    info : {
                        userResponse : '',
                        //submitted : curObj == "completeEssays" ? true : false
                    }
                };   
            }
            
            showAppContainer();
            setQuestion(questionObject);
        }

        $('.hovers').on('click', onHoverClick);

        $('body').on('cut copy paste','input,textarea', function (e) {
            if(e.target.id != "comment" || !e.target.id)
                e.preventDefault(); //disable cut,copy,paste
        });

        var contentContainer = document.getElementById("mainContentContainer");
        contentContainer.addEventListener("click",function(event) { if(event.target.className.indexOf('collapsor') == -1) sidebarToggle(false) });
    }
};





function showCompletedEssay(response , extraParams)
{
    var essayID     = extraParams[0]; 
    var topicId     = extraParams[1];
    var tmpCurDiv   = extraParams[2];
    var curObj      = extraParams[3];

    if (response["userResponse"] == undefined || response["userResponse"] == null) {
        response["userResponse"] == "";
        sessionData.timeSpentInEssay = 0;
    }
    else
        sessionData.timeSpentInEssay = response["timeTaken"]; // uncomment this line and remove above line
        // sessionData.timeSpentInEssay = 0;
    var questionObject = {
        qID : topicId,
        qType : 'essay',
        value : topicId + '||' + tmpCurDiv.html(),
        info : {
            userResponse : response["userResponse"],
            submitted : curObj == "completeEssays" ? true : false,
            isForce:response["isForce"]
        }
    };
    $('.appContainers').hide();
    $('.the_classroom').show();
    setQuestion(questionObject);
}

function sizeQuoteContainer() {
    var width = ((Math.max(($('#quote')[0].innerHTML.length - 100), 0) / 350) * 150) + 160;
    //524 is max char length
    $('#quoteContainer').css({
        width : width + 'px',
        height : width + 'px'
    });
}

/*
 * Epic win hoverable item.
 * if click event is at the close button then close the hover
 */
function onHoverClick(event) {
    if (event.offsetX > this.offsetWidth - 4 && event.offsetY < 2) {
        if ($(this).hasClass('toastInfo')) {
            disableToolTips();
        }
        hideHovers(this.id);
    }
}

/*
 * Implementation: Each item in sidebar has an container div with class as the item name:
 * ex: The Classroom - the_classroom. On click other containers are hidden and container corresponding to the item name is shown.
 */
function onSidebarItemClick(e,custom) {
    if (typeof custom === "undefined" || custom === null) {
        custom = "";
    }
    // Hide the plugin if displayed
    hidePluginIfAny();

    var locationText = $('a', e.currentTarget || e.target).clone();
    locationText.find('span').remove();
    //var string = locationText.html();
    var string=locationText.attr("menuname");

 //checking if user is super admin or not
    if (sessionData.category=='School Admin' && sessionData.subcategory=='All') {
        if (string=='Settings') {
            $('#sbi_settings').addClass('opacity-0');
            var message = "You don't have necessary rights to perform this action.";
            Helpers.prompt(message);
            return false;
        }
        if (string=='My Students') {
            $('#activate_students_li').attr('data-title',"You don't have necessary rights to perform this action.");
            $('#view_teachers_li').attr('data-title',"You don't have necessary rights to perform this action.");
        }  
    }
    //end
    string = string.trim();
    string = string.toLowerCase();
    string = string.replace(/\s/g, '_');
    disableToolTips('sbi_the_classroom');

    /*$(".ui-menu-item").hide();
    $("#ui-id-1").empty();
    $('#ui-id-1').attr('style','display: none !important');*/
    
    if (sessionData.currentLocation.location === string && !/the_grounds|session_report|essay_writer|home_page|the_classroom/.test(string))
    {
        return;
    }
    
    var teacherInterface=["home","reports","my_students","essay_review","do_mindspark","settings"];
    string=string.toLowerCase();
    if($.inArray(string,teacherInterface)!=-1){
        //  All teacher Interface screens
        switch(string){
            case 'home':
                if(sessionData.currentLocation.location!=string){
                    showTeacherHomePage();
                    showTeacherDashboard();
                    sessionData.currentLocation.location = string;
                    sessionData.currentLocation.type = 'home';
                }
            break;
            case 'reports':
                if(sessionData.currentLocation.location!=string){
                    showTeacherReportsPage();
                    if(custom=="custom"){
                        showTeacherReportsOverAllPage('custom');
                    }else{
                        showTeacherReportsOverAllPage('default');
                    }
                    sessionData.currentLocation.location = string;
                    sessionData.currentLocation.type = 'reports';
                }
            break;
            case 'my_students':
                if(sessionData.currentLocation.location!=string){
                    showTeacherMyStudentsPage();
                    showTeacherMyStudentViewPage();
                    sessionData.currentLocation.location = string;
                    sessionData.currentLocation.type = 'myStudents';
                }
            break;
            case 'essay_review':
                if(sessionData.currentLocation.location!=string){
                    showTeacherAllotedEssaysPage();
                    showTeacherAllotmentViewPage();
                    sessionData.currentLocation.location = string;
                    currentQuestion.qID    = undefined;
                    sessionData.currentLocation.type     = 'essayReview';
                }
            break;
            case 'do_mindspark':
                //added by nivedita
                if(sessionData.classComplete >= 180)
                {
                    /*Helpers.prompt('You have used the Classroom section for 180 minutes this week! Come back again next week to use the Classroom again!');
                    return;*/
                    //sessionData.complete = true;
                    stopAndHideOtherActivities();
                    unAngularApp.setDom();
                    Helpers.prompt({
                        text : 'You have used the Classroom section for 180 minutes this week! Come back again next week to use the Classroom again!',
                        noClose : true
                    });
                    return;
                }
                //added by nivedita end
                else
                {
                    string = 'the_classroom';
                    sessionData.currentLocation.location = string;
                    $('.none').hide();
                    $('.' + string).show();
                    sidebarToggle(false);
                    stopAndHideOtherActivities();

                    /*added by nivedita*/
                    showGradesDMS();
                    $("#dms_grade").show();
                    /*end*/

                    sessionData.currentLocation.type = 'misc';
                    //startClassRoom();
                }

            break;
            case 'settings':
                sessionData.currentLocation.type = 'misc'
                if(sessionData.currentLocation.location!=string){
                    showTeacherDefaultSttingControls();
                    showTeacherSettingsPage();
                    sessionData.currentLocation.location = string;
                }
            break;
            default:
                console.log("Default");
            return;
            break;

        }

        sidebarToggle(false);
    }else{
        //$('#userLocation').html('> ' + string);

        switch(string.toLowerCase()) {
        case 'the_classroom':
             //added by nivedita
            //sessionData.classComplete =166;
            if(sessionData.classComplete >= 180)
            {
                /*Helpers.prompt('You have used the Classroom section for 180 minutes this week! Come back again next week to use the Classroom again!');
                return;*/
                //sessionData.complete = true;
                stopAndHideOtherActivities();
                unAngularApp.setDom();
                Helpers.prompt({
                    text : 'You have used the Classroom section for 180 minutes this week! Come back again next week to use the Classroom again!',
                    noClose : true
                });
                return;
            }
            //added by nivedita end
            //if (sessionData.timeSpent > (sessionData.minTimeForClass * 60) && !sessionData.complete && sessionData.category == 'STUDENT') 
            if (sessionData.timeSpent > (sessionData.timeAllowedPerDay * 60) && !sessionData.complete && sessionData.category == 'STUDENT') 
            {
                sessionData.complete = true;
                stopAndHideOtherActivities();
                unAngularApp.setDom();
                Helpers.prompt({
                    text : 'You have spent your '+sessionData.timeAllowedPerDay+' minutes in the Classroom today, so you\'re back at the home page! Come back to the Classroom tomorrow.',
                    noClose : true
                });
            }
            if (sessionData.complete) {

                Helpers.prompt('You are done with questions for the day! Come back tomorrow!');
                return;
            } else if (sessionData.completeAtHome) {
                Helpers.prompt('Hey ' + sessionData.childName + '! You have reached the home usage limit for this week (45 minutes). You can continue using Mindspark in school hours!');
                return;
            }
            break;
        case 'home_page':
                //$(".ui-dialog").hide();
                $("#home").css('display', 'block');
                stopSnowFall();
                startSnowFall();
            break;
        case 'the_grounds':
            // Time for grounds 
            if (sessionData.timeSpent < (sessionData.minTimeForClass * 60)) {
                Helpers.prompt('This feature will be enabled after spending '+(sessionData.minTimeForClass)+' minutes in the Classroom');
                return;
            }
            break;
        case 'essay_writer':
            showEssayWriter();
            $('.tableContainer').show();
            break;
        //case 'teacher_report':
            //break;
        default:
            Helpers.prompt('Coming soon!');
            return;
            break;
        }

        if(string == undefined)
            string = '';
        
        sessionData.currentLocation.location = string;
        $('.none').hide();
        $('.' + string).show();
        sidebarToggle(false);
        stopAndHideOtherActivities();

        sessionData.currentLocation.type = 'misc';
        switch(string.toLowerCase()) {
        case 'the_classroom':

            if (sessionData.classComplete >= 180) {
                //sessionData.complete = true;
                stopAndHideOtherActivities();
                unAngularApp.setDom();
                Helpers.prompt({
                    text : 'You have used the Classroom section for 180 minutes this week! Come back again next week to use the Classroom again!',
                    noClose : true
                });
            }
            
            //if (sessionData.timeSpent > (sessionData.minTimeForClass * 60) && !sessionData.complete && sessionData.category == 'STUDENT') 
            if (sessionData.timeSpent > (sessionData.timeAllowedPerDay * 60) && !sessionData.complete && sessionData.category == 'STUDENT') 
            {
                sessionData.complete = true;
                stopAndHideOtherActivities();
                unAngularApp.setDom();
                Helpers.prompt({
                    text : 'You have spent your '+sessionData.timeAllowedPerDay+' minutes in the Classroom today, so you\'re back at the home page! Come back to the Classroom tomorrow.',
                    noClose : true
                });
            }
            stopCountingTimeClassroom(); //stoping counting time for classroom before it start again. It avaoid serious problem of multiplicating the count time when student again and again click on the classroom without going to other menu.
            startClassRoom();          
            break;
        case 'home_page':
            stopCountingTimeClassroom();
            sessionData.currentLocation.type = 'home';
            unAngularApp.setDom();
            stopSnowFall();
            startSnowFall();    
            break;
        case 'the_grounds':
            stopSnowFall();
            stopCountingTimeClassroom();
            sessionData.currentLocation.type = 'activity';
            break;
        case 'essay_writer':
            stopCountingTimeClassroom();
            angular.element(document.body).scope().getEssays();
            angular.element(document.body).scope().getSummary();

            $('.moduleContainer', '.essay_writer .tableContainer').show();
            $('#essaysToAttempt').hide();
            $('.essay_writer button').show();
            $('#saveEssay').show();
            $('#submitEssay').show();
            $('#startEssay').hide();

            sessionData.classRoomStarted = true;
            sessionData.currentLocation.type = 'essayWriter';
            $('#loader').hide();
            break;
       /* case 'teacher_report':
            var element = $('.teacher_report')[0];
            sessionData.classRoomStarted = true;
            angular.element(element).scope().canShowReport = false;
            break;*/
        default:
            Helpers.prompt('Coming soon!');
            return;
            break;
        }
    }

    $('.sidebar-nav .active').removeClass('active');
   // $(e.currentTarget).addClass('active');

    if(sessionData.currentLocation.location !== 'the_classroom')
        $("#dictionaryButton").prop('disabled', false);
}

function stopCountingTimeClassroom()
{
    clearInterval(timerIdClass);
}

function startEssayInterval() {
    if(!saveEssayTimeId)
    {
        saveEssayTimeId = setInterval(function(){
            sessionData.timeSpentInEssay++;
        },1000);
    }
}

function startClassRoom() {
    sessionData.classRoomStarted = true;
    //timer
    timerIdClass = setInterval(function(){
        sessionData.timeTakenInClassroom++;
        sessionStorage.setItem("timeTakenInClassroom", sessionData.timeTakenInClassroom);
    },1000);
    //end
    getNextQuestion();
}

/*
 * param1: close: BOOLEAN: if passes as true the sidebar will remain closed if it is already closed.
 * when passed false the sidebar will toggle.
 */
function sidebarToggle(close) { 
    var showSideBarForceFully=false;
	$("#sidebarToggle").css('visibility','hidden');
    //&& isMobile()===false
    if(sessionData.category === "STUDENT" && sessionData.currentLocation.location === 'home_page' )
    {
        if($(window).width()>942){
            showSideBarForceFully=true;
        }
    }else{
		$("#sidebarToggle").css('visibility','visible');
	}
    
    if(sessionData.currentLocation.location !== 'dictionary' && showSideBarForceFully===false )
    {
        if (!( typeof close === 'undefined')) {
            if (close) {
                $('#contentContainer').addClass('toggled');
                $('#sidebarToggle').addClass('toggled');
            } else {
                $('#contentContainer').removeClass('toggled');
                $('#sidebarToggle').removeClass('toggled');
            }
        }
        $('#contentContainer').toggleClass('toggled');
        $('#sidebarToggle').toggleClass('toggled');
       
        if($('#sidebarToggle').hasClass('toggled')){
            disableToolTips('sbi_the_classroom');
            //disableToolTips('sbi_essay_writer');
        }
        if (isMobile() == true) {
            var checkEssayActive = $("#sbi_essay_writer").attr('data-tooltip');
            if (typeof checkEssayActive !== "undefined" && checkEssayActive !== null) {
                if ($('#sidebarToggle').hasClass('toggled')) {
                    disableToolTips('sbi_essay_writer');
                }
            }
        }
    }
    
}

/*
 * param1: INT: gameID
 */
function showGame(key) {
    
    stopAndHideOtherActivities();
    showGameContainer();
    $.ajax({
        url : Helpers.constants['CONTROLLER_PATH'] + 'igre/getIGREInfo/' + key,
        data : 'JSON',
    }).done(function(data) {
        Helpers.ajax_response(loadGameOnGrounds , data, []);
    });
    currentQuestion.qType = 'gre';
     $('#gameExitButton').show();
}
function loadGameOnGrounds(data, extraParams)
{
    
    $('#gameFrame').attr('src', '');
    $('#gameFrame').attr('src', Helpers.constants.IGRE_PATH + data[0]['igreType'] + '/' + data[0]['igreid'] + data[0]['igrePath'] + '?' + data[0]['params']);
    $('#loader').hide();

    currentQuestion.qID = data[0]['igreid'];
    //currentQuestion.qType = 'gre';
    sessionData.currentLocation.type = 'singleGame';
    //currentQuestion.qType = data[0]['igreType'];
}

/*
 * loads essay/question/activity/conversation/passage.
 * param1: Object
 */
 
function setQuestion(question,mode) {

    $("#subContainerQuestion").html('');
    $('.the_classroom > .question').show();
    $("#passageNext").hide();
    previousQuestion = currentQuestion;
    currentQuestion = question;
    if(mode)
        sessionData.mode = mode;
    var type = (question.quesType || question.qType);
    sessionData.currentLocation.type = type;
    
    /*question.qType="passageQues";
    question.qID=12641;*/

    //added by nivedita
    updateTimeTakenInClassRoomSessionActive();
    //end

    switch(type) {/*TODO getlowerCase values */
    case 'essay':
        sessionData.currentLocation.value = "";
        $('#loader').hide();
        $('#essayContainer').show();
        var essayReportAlreadyShown;
        if (sessionData.mode == 'report') {
            // $('#essayFeedbackContainer').show(); // will have to check why container is used here
            sessionData.mode = "normal";
            if(currentQuestion.curObj == "reviewedEssays" || currentQuestion.curObj == "essaysToReview"){
                showEvaluateEssayContainer();
                var essayId = currentQuestion.qID;
            }
            else{
                showGradedEssayContainer();
                var essayId = question.qID.split('||')[0];
            }
            var frame = document.getElementById("essayFeedback"),
            frameDoc = frame.contentDocument || frame.contentWindow.document;
            frameDoc.removeChild(frameDoc.documentElement);

            $('#saveEssay').hide();
            $('#submitEssay').hide();
            if (!essayReportAlreadyShown) {
                
                tryingToUnloadPage = true;
                 if(currentQuestion.curObj == "reviewedEssays" || currentQuestion.curObj == "essaysToReview"){
                        
                        $('#reviewEssay').attr('src', 'application/views/Language/essayReport.html?userId=' + sessionData.userID + '&' + 'essayId=' + essayId + '&' + 'curObj=' +currentQuestion.curObj+ '&' + 'mode=' +currentQuestion.mode+ '&' + 'ewsdetailID=' +topicEssay);
                    }
                    else
                        $('#essayFeedback').attr('src', 'application/views/Language/essayReport.html?userId=' + sessionData.userID + '&' + 'essayId=' + essayId + '&' + 'curObj=' +currentQuestion.curObj+ '&' + 'mode=' +currentQuestion.mode+ '&' + 'ewsdetailID=' +topicEssay);
                essayReportAlreadyShown = true;
            }
        } else {
            
            showAppContainer();
            $("#essay_instructions").show();
            
            var getString = question.value.split('||')[1].replace(/</g, "&lt;");
            var getFinalString = getString.replace(/>/g, "&gt;");
            $('#essayTopic').html('Essay Topic: ' +getFinalString.trim());
            
            $('#essayContainer').show();
            $("#forwardNavigationContainer").css('padding-top',($('#essayTopic').innerHeight()+5) + 'px');
            // Unbind the document key events
            Helpers.document_keydown_unbind();

            question.info.userResponse = question.info.userResponse === undefined ? '' : question.info.userResponse;
            tinyMCE.activeEditor.setContent(question.info.userResponse);

            var essay_iframe = window.frames['essay_ifr'];
            var frameDocument = essay_iframe.contentDocument || essay_iframe.document;
            frameDocument.oncontextmenu  = function(){return false;};

            // adds word count for saved/submitted essays
            var count = 0;
            if(question.info.userResponse.trim() != ''){
                 var editor = tinymce.activeEditor;
                 count = editor.plugins.wordcount.getCount();
            }
            
            $("#essay_instructions .textAreaAfter").html("[ Words entered: "+count+" ]");
            if (question.info.submitted) {
                $('#saveEssay').hide();
                $('#submitEssay').hide();
                $('#essayContainer iframe').contents().find('body').removeAttr('contenteditable');
                $('#essayContainer iframe').contents().find('body').addClass('isCompletedEssay');
                $("#essayContainer").find("div.mce-toolbar-grp").css('display','none');
                $("#essayContainer iframe").css('height','431px');
                $('.autoSaveLblDiv').hide();
                $('.textAreaAfter').addClass('wordEnterDivMargin');


                // $('#essay').css('pointer-events', 'none');
            } else {
                $('#saveEssay').show();
                $('#submitEssay').show();
                tinymce.activeEditor.getBody().setAttribute('contenteditable', true);
                $('#essayContainer iframe').contents().find('body').removeClass('isCompletedEssay');
                $("#essayContainer").find("div.mce-toolbar-grp").css('display','block');
                $("#essayContainer iframe").css('height','385px');
                $('#essay').css('pointer-events', '');
                if($(".textAreaAfter").hasClass("wordEnterDivMargin")){
                    $('.textAreaAfter').removeClass('wordEnterDivMargin');
                }
                $('.autoSaveLblDiv').show();
                
                if(question.info.isForce!=="no"){
                    $("#submitEssay").html("Submit");
                }else{
                    $("#submitEssay").html($("#submitEssay").attr('data-value'));
                }
                startEssayInterval();
            }
        }
        break;
    case 'introduction':
    case 'game':
    case 'activity':
        showGameContainer();
        showGame(question.qID);
        $('#gameExitButton').hide();
        showNext(true);
        break;
    case 'passage':
        sessionData.qID = question.qID;
        sessionData.qType = 'passage';

        showAppContainer();
        var id = question.value ? question.value.split('||')[1] : question.qID;
        currentQuestion.complete = 0;
        if (question.info.passageType == 'Conversation') {
            audioObject.view.show(id, afterComponentLoad);
        } else {
            passageObject.view.show({
                id : question.qID,
                onload : afterComponentLoad,
                isolated : pageMode == 'preview' ? true : false,
            });
        }
        break;
    case 'passageQues':
        sessionData.qID = question.qID;
        sessionData.qType = 'passageQues';

        if(sessionData.currentLocation.location != 'session_report')
            $("#dictionaryButton").prop('disabled', true);
        case 'speaking':
            sessionData.qID = question.qID;
            sessionData.qType = 'speaking';

            if (sessionData.currentLocation.location != 'session_report')
                $("#dictionaryButton").prop('disabled', true);
        //  alert(question.qID);
        //  break;
    case 'freeQues':
        sessionData.qID = question.qID;
        sessionData.qType = 'freeQues';

        if(sessionData.currentLocation.location != 'session_report')
            $("#dictionaryButton").prop('disabled', true);
        try{
            rating_object.reset_parameters();
            rating_object.vanish();
        }catch(e){}
        showAppContainer();

        sessionData.questionsAttempted++;
        var modifyString = null;
        if(!NO_INTERNET && pageMode == 'normal')
            $("#queNum").show();
        if (sessionData.mode == 'diagnostic' && question.info.userResponse) {
            modifyString = {
                'userResponse' : question.info.userResponse.toLowerCase(),
                'correct' : question.info.correct
            };
        }

        $(".characterArrow").show();
        $("#arrow").show();
        $("#arrowText").show();
        $(".leftRock").show();
        $(".rightRock").show();
        questionObject.view.show({
            qcode : (question.value || question.qID),
            mode : NO_INTERNET ? 'offline' : sessionData.mode,
            onLoad : function() {
                return function() {
                    afterComponentLoad(question);
                };
            }(),
            onAttempt : afterQuestionAttempt
        });
        break;
    }
    if(sessionData.mode == "preview")
    {
        $(".english-content-wrapper").fadeIn(200).removeClass('none').css('display','block');
    }

}
/*
 *  Stops Everything. clears any running intervals timeouts.
 *  param1: withLoader: Boolean: need a loader animation or not.
 */
function stopAndHideOtherActivities(withLoader) {
    //hiding elements
    $("#queNum").hide();
    $('.gameContainers').hide();
    $('#diagnosticEnd').hide();
    $("#audioContainer").hide();
    $('#passageContainer').hide();
    $('#questionContainer').hide();
    $('#essayContainer').hide();
    $('.question').hide();
    $('.the_classroom').css('background-image', 'url("'+ Helpers.constants['THEME_PATH'] +'img/'+Helpers.constants["UI_THEME_IMAGES"]+'/backgrounds/classroomBackground1.png")');
    $('.navigationButtons').hide();
    $('#lookBackFigure').hide();
    $('#essayFeedbackContainer').hide();
    if (withLoader)
        $('#loader').show();
    $(".characterArrow").hide();
    $("#arrow").hide();
    $("#arrowText").hide();
    $('#modalBlocker').hide();
    $(".leftRock").hide();
    $(".rightRock").hide();
    //added by nivedita
    $("#prompt").hide();

    $(".characterArrow").css({
        "background-image" : "url("+ Helpers.constants['THEME_PATH'] +"img/"+Helpers.constants['UI_THEME_IMAGES']+"/classroomImages/" + buddyTemp + "-Arrow.png)"
    });

    // removing classes
    $("#arrow").removeClass("correctArrow incorrectArrow");
    $("#arrowText").removeClass("correctText incorrectText");

    //stopping components.
    try{
        questionObject.stop();
        passageObject.stop();
        audioObject.stop();
    }
    catch(e){}


    //clearing any running intervals
    clearTimeout(saveEssayIntervalId);
    if(saveEssayTimeId)
    {       
        // alert(sessionData.timeSpentInEssay);
        clearInterval(saveEssayTimeId);
        saveEssayTimeId = 0;
    }
}

/*Callback after component loads.
 * param1: question object
 */
function afterComponentLoad(question) {
   
   if(AUDIO.currentAudio){
      $('.jp-intro').remove();
      audioIntro  ='<div class="jp-intro" style="margin: auto;color:#ffffff;word-wrap: break-word;max-width: 420px;background-color: rgba(0, 0, 0, 0.09);font-size: initial; margin-top:5px; padding-top: 1px;" >'+AUDIO.currentAudio.intro+'</div>';  
      $("#jp_container_1").after(audioIntro);
     }


    if (sessionData.mode == "diagnostic")
        $('.navigationButtons').show();

    var type = 'passage';
    if (/freeQues|passageQues|speaking/.test(currentQuestion.qType)) {
        currentQuestion = questionObject.getQuestion();
        if(!NO_INTERNET){
            lastOnlineQuestion = currentQuestion;
        }
        
        type = 'question';
        //preserving old Data;
        for (var key in question) {
            if (question.hasOwnProperty(key)) {
                currentQuestion[key] = question[key];
            }
        }
        if (question.qType == 'passageQues') {
            if (refQuesViewObj.quesDataArr.passageTypeName == 'Conversation') {
                $('#lookBackFigure figcaption').html("Listen here!");
                $('#lookback').attr({
                    'src': Helpers.constants['THEME_PATH'] + 'img/Language/templates/soundIconGrey.png'
                });
                tooltips.lookback = null;
                tooltips.lookback = "Passage Lookback: Click here to listen to the audio again!";
            } else {
                $('#lookBackFigure figcaption').html("Read here!");
                $('#lookback').attr({
                    'src': Helpers.constants['THEME_PATH'] + 'img/Language/lookback.png'
                });
                tooltips.lookback = null;
                tooltips.lookback = "Passage Lookback: Allows you to view the passage text.";
            }
            $('#lookBackFigure').show();
        }
        $('.question').show();
    } 

    else if(question.qType == 'speaking') {
             $('.navigationButton').css('opacity', '1');
            setTimeout(function() {
                $('.navigationButton').css('opacity', '');
            }, 5000);
    }
    else if (currentQuestion.info.passageType != 'Conversation') {
        //$('.navigationButtons').show();
        saveQuestion(0);
        $('.navigationButton').css('opacity', '1');
        setTimeout(function() {
            $('.navigationButton').css('opacity', '');
        }, 5000);
    } else {
        showNext(true);
        $('.question').show(100,function(){
            setTimeout(function(){ flashTip('audioPlayer'); },200);});
        
    }
    
    if(typeof currentQuestion.qID != 'undefined')
        $.ajax({
            url:  Helpers.constants['CONTROLLER_PATH'] + 'interfaceanalysis/saveContentLoadingTime/' + currentQuestion.qID + '/' + type + '/' + (sessionData.timeSpent - questionStartTime),
        });
    
    $('#loader').hide();
}

/*
 * hides any hoverable item and translucent screen. Called on click of psuedo close button and modalBlocker
 */
function hideHovers() {
    $(document).off('keypress').on('keypress', function(e){accessDocument(e)});   
    if ($('#audioLookback').is(":visible")) {
        audioObject.stop();
    }

    if ($('#feedbackForm').is(":visible") && dt.mode != 'demo') {
        return;
    }
    
    //hovers are hidden also means instruction is hidden.
    if(sessionData.mode == 'diagnostic'){
        dt.seeingInstructions = false;
    }

    if(sessionData.currentLocation.location != 'session_report')
    {
        $('.hovers').hide();
        $('.prompts').hide();
        $('#modalBlocker').hide();
    }
}

function showLookBack() {
    $(document).off('keypress');
    var passageID = currentQuestion.json.passageID; 
    if (currentQuestion.json.passageTypeName == "Conversation") {
        audioObject.view.showLookback(passageID, $('#audioLookback')[0], function() {
            $('#modalBlocker').show();
            $('#audioLookback').show();
        });
    } else {
        passageObject.view.showLookback(passageID, $('#lookbackContent')[0], function() {
            $('#modalBlocker').show();
            $('#lookbackContent').show();
        });
    }
}

function beforelogOut() {
    if (currentQuestion.questTypeLabel == 'essay' || currentQuestion.qType == 'essay') {
        var flagEssaySave = false;
        if(sessionData.essaySummaryData && sessionData.essaySummaryData.inCompleteEssays != null) {
            // if essay is in incompleted essays list then only save it
            if(sessionData.essaySummaryData.inCompleteEssays.split("||").indexOf(currentQuestion.qID) != -1) {
                flagEssaySave = true;
            }
        }
        if(flagEssaySave)
        {
            saveEssay('silent');
        }
    }
    
    //setTryingToUnload();
    if (sessionData.currentLocation.location != "session_report" && sessionData.category == 'STUDENT') {
        /*$(".ui-widget-overlay").show();
        $(".ui-dialog").show();*/
        logOutReason = 2;
        Helpers.prompt({
            text : "Do you wish to view your <b>session report</b> before you sign out?",
            buttons : {
                'Yes' : showSessionReport,
                'Logout' : logOutBtnClick,
                'Cancel' : unsetTryingToUnload
            },
            noClose : true
        });
    } else {
        logOutReason = 2;
        Helpers.prompt({
            text : "Are you sure you want to sign out?",
            buttons : {
                'OK' : logOutBtnClick,
                'Cancel' : unsetTryingToUnload,
            },
            noClose : true
        });
        //added show digalog. it will show dialog if user loagged as super admin or school admin.
        $(".ui-dialog").show();
    }
}

function logOutBtnClick() {
    userLogout = true;
    if(!logOutReason)
        logOutReason = 1;

    logOutReasonFlag = logOutReason;
    sessionStorage.removeItem('questionNumber');
    tryingToUnloadPage = true;
    localStorage.clear();
    logTime(userLogout,logOutReasonFlag);
    //goToLogin()

}

function logOut() {
    if(!logOutReason)
        logOutReason = 1;
    logOutReasonFlag = logOutReason;
    sessionStorage.removeItem('questionNumber');
    tryingToUnloadPage = true;
    localStorage.clear();
    logTime(false,logOutReasonFlag);
    //goToLogin()

}
function redirect(response)
{
    response = $.parseJSON(response);
    if(response.redirect == true)
        goToLogin();
}
function goToLogin() {
    window.location.assign(Helpers.constants.LOGIN_PATH);
}

/*
###################################################################
## As setProperLogoutFlag is passed as false fom logout() function 
###################################################################
*/


function logTime(setProperLogoutFlag,logOutReasonFlag) {
    
    if(setProperLogoutFlag != '' && setProperLogoutFlag != undefined && setProperLogoutFlag != false)    
        var logoutTime = 'true';
    else
        var logoutTime = 'false';
    switch(sessionData.mode) {
        case 'diagnostic':
            $.ajax({
                //url : Helpers.constants['CONTROLLER_PATH'] + 'diagnosticTest/updateTestDetails/' + sessionData.userID + '/' + sessionData.timeSpent
                url : Helpers.constants['CONTROLLER_PATH'] + 'diagnosticTest/updateTestDetails/' + sessionData.timeSpent
            });
            break;
        case 'normal':
            updateTimeTakenInClassRoom();
            $.ajax({
                type : "POST",
                url : Helpers.constants['CONTROLLER_PATH'] + 'login/updateEndTime',
                data:{'logoutTime' : logoutTime, 'logoutReason':logOutReasonFlag},
                async : true,
                success: redirect
            });
            break;
        default :
            updateTimeTakenInClassRoom();
            $.ajax({
                type : "POST",
                url : Helpers.constants['CONTROLLER_PATH'] + 'login/updateEndTime',
                data:{'logoutTime' : logoutTime, 'logoutReason':logOutReasonFlag},
                async : true,
                success: redirect
            });
            break;
    }
}

//function added by nivedita
function updateTimeTakenInClassRoom(){
     //save the time taken in class 
    jQuery.ajax({
            type : "POST",
            url : Helpers.constants.CONTROLLER_PATH + 'login/timeTakenInClassroom',
            data:{'timeTakenInClassroom' : sessionData.timeTakenInClassroom},
            "async" : true,
            success : function() {
                sessionData.timeTakenInClassroom = 0;
                sessionStorage.setItem("timeTakenInClassroom", 0);
            }
    });
}

//function added by nivedita
function updateTimeTakenInClassRoomSessionActive(){
     //save the time taken in class 
    jQuery.ajax({
            type : "POST",
            url : Helpers.constants.CONTROLLER_PATH + 'login/timeTakenInClassroom',
            data:{'timeTakenInClassroom' : sessionData.timeTakenInClassroom},
            "async" : true,
            success : function() {
                //sessionData.timeTakenInClassroom = 0;
                //localStorage.setItem("timeTakenInClassroom", 0);
            }
    });

    jQuery.ajax({
            type : "POST",
            url : Helpers.constants.CONTROLLER_PATH + 'home/gettimeTakenInClassroom/',
            "async" : true,
            success : function(data) {
                Helpers.ajax_response( getTimeSpentAtClassroon , data , []);
            }
    });
    
}

function getTimeSpentAtClassroon(data , extraParams)
{
    var response = data;
    sessionData.classComplete = response;
}

function showCommentPanel() {
    hidePluginIfAny();
    sessionData.seeingInstructions = true;
    $('#modalBlocker').show();
    $('#commentPanel').show();
    $('#comment').focus(250);
    $('#comment')[0].value = '';
}

function submitComment() {

    var type = 'misc';
    sessionData.currentLocation.value = null;

    switch(sessionData.currentLocation.type) {
    case 'passage':
        type = 'passage';
        sessionData.currentLocation.value = currentQuestion.qID;
        break;
    case 'freeQues':
    case 'passageQues':
        type = 'questions';
        sessionData.currentLocation.value = currentQuestion.qID;
        break;
    case 'home':
        type = 'home';
        break;
    case 'activity':
        type = 'activity';
        // sessionData.currentLocation.value = currentQuestion.qID;
        break;
    }

    if (!Helpers.isBlank($('#comment')[0].value)) {
        hideHovers();
        var arrayToPost = {
            //'userID' : sessionData.userID,
            'type' : type,
            'comment' : $('#comment')[0].value,
            'qcode' : sessionData.currentLocation.value,
        };

        $.ajax({
            type : "POST",
            url : Helpers.constants['CONTROLLER_PATH'] + "diagnosticTest/insertUserComments",
            data : arrayToPost
        }).done(function(data) {
            //Helpers.prompt('Your comment has been submitted.');
            Helpers.ajax_response( '' , data, []);
        });
    }
    else
    {
        Helpers.prompt('Please enter the comment.');
    }
}

function showSessionReport(userid,startDate,endDate,attemptTableClass) {
    
    
    $('.sidebar-nav .active').removeClass('active');
    // Hide plugin 
    hidePluginIfAny();
    
    Helpers.loadJs('theme/js/Language/sessionReport.js?20022017', function() {
        //sessionReportObject = new SESSIONREPORT(sessionData.userID, $('.sessionReport'));
        sessionReportObject = new SESSIONREPORT($('.sessionReport'),userid,startDate,endDate,attemptTableClass);
        //sessionReportObject.getData(sessionData.userID, function() {
        
        sessionReportObject.getData(function() {
            $('#mainContentContainer > .none').hide();
            //risky thing to do.
            $('#quoteContainer').hide();
            $('#home').show();
            hidePluginIfAny();
            $('#skillometer').hide();
            showSessionReportPage();
            sidebarToggle(false);
            sessionData.currentLocation.type = 'misc';
            sessionData.currentLocation.value = 'sessionreport';
        });                    
    });
}

/*function showSessionReport() {
    
    
    $('.sidebar-nav .active').removeClass('active');
    // Hide plugin 
    hidePluginIfAny();
    
    Helpers.loadJs('theme/js/Language/sessionReport.js?08022017', function() {
        //sessionReportObject = new SESSIONREPORT(sessionData.userID, $('.sessionReport'));
        sessionReportObject = new SESSIONREPORT($('.sessionReport'));
        //sessionReportObject.getData(sessionData.userID, function() {
        
        sessionReportObject.getData(function() {
            $('#mainContentContainer > .none').hide();
            //risky thing to do.
            $('#quoteContainer').hide();
            $('#home').show();
            hidePluginIfAny();
            $('#skillometer').hide();
            showSessionReportPage();
            sidebarToggle(false);
            sessionData.currentLocation.type = 'misc';
            sessionData.currentLocation.value = 'sessionreport';
        });                    
    });
}*/

function feedbackClose() {
    goToLogin()
}

function showpendingEssays() {

    currentQuestion.qID    = undefined;
    $(".diaLog-explanation").hide();
    sessionData.currentLocation.type = 'essayReview';
    $('.sidebar-nav .active').removeClass('active');
    $('#sbi_essay_review').addClass('active');
    sidebarToggle(false)
    if(sessionData.currentLocation.location!='essay_review'){
        showTeacherAllotedEssaysPage();
        showTeacherAllotmentViewPage();
        sessionData.currentLocation.location = 'essay_review';
    }
}

/*gets the next question in adaptive logic
 * param1: OBJECT: question.
 */  
function getNextQuestion(question) {

    if(checkTimeBeforeGivingQuestion())
        return;
    stopAndHideOtherActivities(true);
    //if the object is not passed or is undefined default the type to 'passage'
    questionStartTime = sessionData.timeSpent;
    var type;
    if ( typeof question == "undefined") {
        type = 'passage';
    } else {
        type = (question.quesType || question.qType);
    }

    switch(type) {
    case 'passage':
        $.ajax({
            //url : Helpers.constants['CONTROLLER_PATH'] + 'questionspage/getStudentPosition/' + sessionData.userID,
            url : Helpers.constants['CONTROLLER_PATH'] + 'questionspage/getStudentPosition',
            success : function(data){ Helpers.ajax_response( afterGettingQuestion , data, [] ); }, 
            timeout : serverResponseTimeLimit,
            error : onAjaxError
        });
        break;
    case 'passageQues':
        $.ajax({
            //url : Helpers.constants['CONTROLLER_PATH'] + 'questionspage/getNextPassageQuestion/' + sessionData.userID + '/' + question.json.passageID,
            url : Helpers.constants['CONTROLLER_PATH'] + 'questionspage/getNextPassageQuestion/'+ question.json.passageID,
            success : function(data){ Helpers.ajax_response( afterGettingQuestion , data, [] ); },
            type : 'POST',
            data : question,
            timeout : serverResponseTimeLimit,
            error : onAjaxError
        });
        break;
    case 'freeQues':
        $.ajax({
            //url : Helpers.constants['CONTROLLER_PATH'] + 'questionspage/getNextNonContextualQuestions/' + sessionData.userID,
            url : Helpers.constants['CONTROLLER_PATH'] + 'questionspage/getNextNonContextualQuestions',
            success : function(data){ Helpers.ajax_response( afterGettingQuestion , data, [] ); },
            type : 'POST',
            data : question,
            timeout : serverResponseTimeLimit,
            error : onAjaxError
        });
        break;
        case 'speaking':
            $.ajax({
                url : Helpers.constants['CONTROLLER_PATH'] + 'questionspage/getStudentPosition',
                success : function(data){ Helpers.ajax_response( afterGettingQuestion , data, [] ); },
                type : 'POST',
                data : question,
                timeout : serverResponseTimeLimit,
                error : onAjaxError
            });
        break;
    default:
        $.ajax({
            //url : Helpers.constants['CONTROLLER_PATH'] + 'questionspage/getStudentPosition/' + sessionData.userID,
            url : Helpers.constants['CONTROLLER_PATH'] + 'questionspage/getStudentPosition',
            success : function(data){ Helpers.ajax_response( afterGettingQuestion , data, [] ); },
            timeout : serverResponseTimeLimit,
            error : onAjaxError
        });
        break;
    }
};

/*
 * param1: jqXHR : xml response object
 * param2: textStatus: status string of response
 * param3: error info 
 * 
 * creates offline route if determined that net is not working.
 */
function onAjaxError(jqXHR, textStatus, errorThrown) {
    if (!jqXHR || jqXHR.status != 500) {
        if(!NO_INTERNET){
            if(/freeQues|passageQues/.test(currentQuestion.qType)){
                currentQuestion.userResponse = '';
            }
            Helpers.prompt({
                'text': 'There appears to be a problem with your network. You will be redirected to the home page.', // You can answer a few questions till you reconnect.
                'buttons': {
                    'OK' : function(){
                         showHomePage();
                        /*Helpers.close_prompt();
                        $('.none').hide();
                        $('.appContainers').hide();
                        $('.the_classroom').show();
                        stopAndHideOtherActivities();
                        $("#loader").hide();
                        $($('.sidebar-nav li')[0]).trigger('click');
*/
                       /* try{
                            setQuestion({
                                'qType' : 'freeQues',
                                'info' : {}
                            });
                            
                        }catch(e){}*/
                    }
                },
                'force': true 
            });
            $('.ui-dialog-titlebar-close').hide();
        }
        else{
            $('.none').hide();
            $('.appContainers').hide();
            $('.the_classroom').show();
            stopAndHideOtherActivities();
            setQuestion({
                'qType' : 'freeQues',
                'info' : {}
            });
        }
        NO_INTERNET = true;
    }
}

/*
 * server response for getting next question
 * param1: response:{
 *     qtype: STRING: passage|passageQues|freeQues|introduction|essay,
 *     qID: INT
 *     info: {
 *          passageType: STRING: Conversation|Illustrated,
 *          userResponse: STRING: essay response|question's response
 *      }
 * }
 */
function afterGettingQuestion(response , extraParams) {
    /*console.log(response);
    if(response.qID == "")
      {  
          hidePluginIfAny();
          Helpers.prompt("Weekly question over"); 
          $("#home").css('display', 'block');
          return;
      }*/
   
    if (!Helpers.isResponseValid(response)) {
        return;
    }
    NO_INTERNET = false;
    var question = response;
    if(question.isContentExhaustedTeacher)
    {
        var msgTeacher = "Your Content is Exhausted for this grade!";
        Helpers.prompt(msgTeacher);
        $('#loader').hide();
        //stopAndHideOtherActivities(true);
        return;
    }
     if (question.isRedirectToEssayWriter) {
       Helpers.prompt({
           text: "You do not seem to have written anything in the last fortnight! Let's write something now.",
           buttons: {
               'OK': function () {
                stopCountingTimeClassroom();
                showEssayWriter();
                $('.tableContainer').show();
                $('.moduleContainer', '.essay_writer .tableContainer').show();
                $('#essaysToAttempt').hide();
                $('.essay_writer button').show();
                $('#saveEssay').show();
                $('#submitEssay').show();
                $('#startEssay').hide();
                $('#loader').hide();
               }
           },
           noClose: true
       });
        angular.element(document.body).scope().getEssays();
        angular.element(document.body).scope().getSummary();
        sessionData.classRoomStarted = true;
        sessionData.currentLocation.type = 'essayWriter';
        return;
    }
    if (question.schoolBunchingOrder == 0) {
        var msgTeacher = "This feature is not activated for this school. Please reach your teacher for assistance";
        Helpers.prompt(msgTeacher);
        $('#loader').hide();
        //stopAndHideOtherActivities(true);
        return;
    }
    //if (question.qType == 'passage' && ((previousQuestion && previousQuestion.qType == 'freeQues') || firstPassageOfSession)) {
    if (question.qType == 'passage' && ((previousQuestion && previousQuestion.qType == "passageQues" ) || firstPassageOfSession)) {
        firstPassageOfSession = false;
        if(question.info.passageType=='Conversation')
        {
            var promptText='Hey! You will get a conversation now! Listen to it carefully and answer questions that follow.';
            counter = 0;
        }
        else
        {
            var promptText='Hey! You will get a passage now! Read it carefully and answer questions that follow.';
            counter = 0;
        }
        
        setQuestion(question);
        Helpers.prompt({
            text : promptText,
            buttons : {
                'OK' : function() {
                }
            },
            noClose : true
        });
    } else if (currentQuestion.qType === "passageQues" && question.qType == "freeQues") {
        setQuestion(question);
        Helpers.prompt({
            text : 'Good Job! We are done with passages for now. Time to answer some more exciting questions!',
            buttons : {
                'OK' : function() {
                }
            },
            noClose : true
        });
    } else if (question.qType == "speaking") {
        var promptText = 'Hey! You will get a Speaking Question now!.';
        setQuestion(question);
        Helpers.prompt({
            text: promptText,
            buttons: {
                'OK': function () {
                }
            },
            noClose: true
        });
    }
    else {
        setQuestion(question);
    }
}

/*
 * save response immediately after question attempt.
 * param1: response object: holds all the question attempt info
 */
function afterQuestionAttempt(response) {
    //saving the response in the attempt itself
    //currentQuestion.userID = sessionData.userID;
    currentQuestion.questionType = currentQuestion.qType;
    currentQuestion.questionNo = questionObject.model.parent.questionNumber;
    
    
    if(typeof currentQuestion.qID != 'undefined'){ //check for an offline question.
        //for getting the time taken and handeling accordingly if after 3 ques time taken is less then 30 sec
        if(currentQuestion.qType == 'passageQues' || currentQuestion.qType == 'freeQues')
        {
            if(currentQuestion.json.explanation)
            {
                $(".diaLog-explanation").show();
                $("#prompt").show();
            }
            
            /*counter += 1;
            
            totalCorrectQues.push(currentQuestion.correct);
            var delayNextBtn = false;
            sessionData.delayNextBtn = delayNextBtn;
            var givenWrong = false;
            totalTimeQues.push(currentQuestion.timeTaken);

            if(counter == 3)
            {
                var totalTime = 0;
                for (var i = 0; i < counter; i++) 
                {
                    totalTime += totalTimeQues[i] << 0;
                    if(totalCorrectQues[i] == 0)
                        givenWrong = true;

                }
                
                if(totalTime < 30)
                {
                    if(givenWrong)
                    {
                        totalTimeQues = [];
                        totalCorrectQues = [];
                        // Helpers.prompt('You seem to be answering questions hurriedly. Please read the question carefully before proceeding.');
                        
                        if($('#passageNext').css('display') == 'block' || $('#passageNext').css('display') == 'inline-block');
                        {
                            console.log('passageNext');
                            $("#passageNext").hide()
                            $("#delayNext").show();
                            //$('#passageNext').hide();  
                            //$('#passageNext').prop('disabled', true);  
                            sessionData.delayNextBtn = true
                            delayNextBtn = true; 
                        } //commenting this as this task needs more info from dev's end
                    }
                }
                counter = 0;
            }*/
        }
        //end
        if(currentQuestion.completed == 1)
            counterPath += 1;
        currentQuestion.page = sessionData.currentLocation.type;
        $.ajax({
            url : Helpers.constants['CONTROLLER_PATH'] + 'questionspage/saveResponse',
            type : 'POST',
            data : currentQuestion,
            success : function(data){ 
                $("#dictionaryButton").prop('disabled', false);
                Helpers.ajax_response( showNext , data, [] ); 
            },
            timeout : serverResponseTimeLimit,
            error : onAjaxError
        });
    }
    else{
        showNext();
    }
}

function checkTimeBeforeGivingQuestion()
{

if(sessionData.timeSpent > (sessionData.timeAllowedPerDay * 60) && !sessionData.complete && sessionData.category == 'STUDENT') 
    {
        sessionData.complete = true;
        stopAndHideOtherActivities();
        unAngularApp.setDom();
        Helpers.prompt({
            text : 'You have spent your '+sessionData.timeAllowedPerDay+' minutes in the Classroom today, so you\'re back at the home page! Come back to the Classroom tomorrow.',
            noClose : true
        });
        return true;
    }
    else
        return false;
}


/*
 * Components to show to allow next navigation.
 * param1: withoutFocus. passed true if nextButton doesn't need focus.
 */
function showNext(withoutFocus, extraParams) {

    if(typeof withoutFocus === "object" && typeof withoutFocus.responseIsJunk != 'undefined' && currentQuestion.json.qType == 'openEnded'){
         $('#' + Helpers.constants.PROMPT_ID).dialog( "destroy" );
         Helpers.prompt({
                    title : "", 
                    text : withoutFocus.responseMsg,
                    buttons : {
                        'OK' :  function(){ 
                               $('#questionSubmitButton').show();
                               $('#passageNext').hide();
                               $('.blank-textarea').removeAttr('disabled');
                               $('.blank-textarea').focus();
                           },
                    },
                    closeFunction : function(){
                        $('#questionSubmitButton').show();
                        $('#passageNext').hide();
                        $('.blank-textarea').removeAttr('disabled');
                        $('.blank-textarea').focus();
                    },
                    'force': true
        });
        return;
    }
    else if (currentQuestion.json)
        if( currentQuestion.json.qType == 'openEnded')    // Changed Here to show prompt before Explanation By Aditya
            refQuesViewObj.giveExplanation();

    if(typeof currentQuestion.qType != 'undefined')
    {
        if(currentQuestion.qType == 'passageQues' || currentQuestion.qType == 'freeQues')
        {
            if(currentQuestion.json.qType!="openEnded"){
                showCorrectIncorrect(parseInt(currentQuestion.correct));
            }else{
                showCorrectIncorrect(1);
            }
        }    
    }    
    

    /*if(extraParams !== undefined)
        var delayNext = extraParams[0];*/

    if(typeof withoutFocus === "object")
    {
        Helpers.notification(withoutFocus.data);
        angular.element(document.body).scope().updateSpakieCount(withoutFocus.total_sparkies);
    }
    if (sessionData.mode != 'preview') {

        //added by nivedita
        /*if(delayNext)
        {
            setTimeout(function(){
                $('#passageNext').show();   
            }, 10000); //commenting this as this task still needs more info from dev's end
        }//end
        else */
        //alert('counterPathapp=>'+counterPath);
        var checkCounterPath = counterPath - 1;
        if(checkCounterPath == 1 || checkCounterPath == 2 || checkCounterPath == 0 )  // By Aditya 
            $('#passageNext').show();

        if(sessionData.qType == 'passage')
            $('#passageNext').show();

        $('#gamePassageNext').show();

        if (!withoutFocus)
        {
            $('#passageNext').focus();
            $('#gamePassageNext').focus();
        }
        switch(sessionData.currentLocation.type)
        {
            case 'introduction':
            case 'game':
            case 'activity':
            {
                $('#passageNext').html("Exit  <i class='fa fa-caret-right'></i>"); // for intros in flow
                break;
            }
            default:
                $('#passageNext').html("Next  <i class='fa fa-caret-right'></i>");
        }
    }
}

/* plays wrong/correct animation on the background. 
 * param1: type correct/incorrect
 */
function showCorrectIncorrect(correct) {
    if (correct) {
        $(".characterArrow").css({
            "background-image" : "url(" + Helpers.constants['THEME_PATH'] + "img/"+Helpers.constants['UI_THEME_IMAGES']+"/classroomImages/" + buddyTemp + "-WellDone.png)"
        });
        $("#arrow").addClass("correctArrow");
        $("#arrowText").addClass("correctText");
    }
    else{
        $(".characterArrow").css({
            "background-image" : "url(" + Helpers.constants['THEME_PATH'] + "img/"+Helpers.constants['UI_THEME_IMAGES']+"/classroomImages/" + buddyTemp + "-Oops.png)"
        });
        $("#arrow").addClass("incorrectArrow");
        $("#arrowText").addClass("incorrectText");
    }
}

/* saves current part of passage. gets next question incase of questions and activities
 * param1: pageNo. incase of passage only, the current page number to be saved.
 * param2: finish. incase of passage only, indicate if the passage is finished.
 */
function saveQuestion(pageNo, finish) {

    if(currentQuestion.qType != 'passage')
    {
        try{
            // Added to remove the prompt message on screen while attempting the questions.
            Helpers.close_prompt();
                
        }
        catch(e){}
        
    }

    if(NO_INTERNET){
        getNextQuestion(lastOnlineQuestion);
        return;
    }
    
    switch(currentQuestion.qType) {
    case 'passage':
        if (pageMode == 'preview')
            return;

        //currentQuestion.userID = sessionData.userID;
        //adding 1 to indicate that the page has been completed.

        currentQuestion.passageID = currentQuestion.qID;
        
        if (currentQuestion.info.passageType == 'Conversation') {
            // Show the button to proceed to questions from passage.
            // -- The next button gets hide as per the logic. For rating we need to proceed in flow so need to show the passage next button.
            $("#passageNext").show();
                

            currentQuestion.currentPassagePart = 0;
            currentQuestion.complete = 1;
            currentQuestion.timeTaken = audioObject.getTimeTaken() || 0;
            if(step_after_rating == 1)
            {
                // Save the rating and call the callback function.
                // -- Here the getNextQuestion is called as the rating for the passage is saved.
                //$("#passageNext").hide();
                rating_object.save_rating('no_callback');
            }
            else
            {
                // Show the rating plugin.
                step_after_rating++;
                rating_object = new RATING({ 
                        title : 'Would you like to rate this conversation? (Optional)',
                        fade_element : '.passage',
                        parameters : {
                            contentID : currentQuestion.passageID,
                            contentType : 'Passage',
                            rating : 0,
                            comment : '',
                            ratingReasonOther : ''
                        },
                        callback : {
                            callback_function : getNextQuestion,
                            callback_parameters : currentQuestion
                        }
                       
                    });

                return;
            }


        } else {
            currentQuestion.currentPassagePart = pageNo;
            currentQuestion.timeTaken = passageObject.getTimeTaken() || 0;
            if (finish) {
                currentQuestion.complete = 1;
                if(step_after_rating == 1)
                {
                    // Save the rating and call the callback function.
                    // -- Here the getNextQuestion is called as the rating for the passage is saved.
                    rating_object.save_rating('no_callback');
                }
                else
                {
                    // Show the rating plugin.
                    step_after_rating++;
                    rating_object = new RATING({ 
                            title : 'Would you like to rate this passage? (Optional)',
                            fade_element : '.passage',
                            parameters : {
                                contentID : currentQuestion.passageID,
                                contentType : 'Passage',
                                rating : 0,
                                comment : '',
                                ratingReasonOther : ''
                            },
                            callback : {
                                callback_function : getNextQuestion,
                                callback_parameters : currentQuestion
                            }
                           
                        });
                    return;
                }
            }
            else
            {
                step_after_rating = 0;
            }
        }
    

          // hide the rating plugin.
        try{
            if(!rating_object.isRatingValid() && step_after_rating == 1)
            {
                // Show the alert if the rating given fails any validation.
                Helpers.prompt('Please select or type in the reason.');
                return;
            }
            rating_object.vanish();
        }
        catch(e){}
        
        $.ajax({
            url : Helpers.constants['CONTROLLER_PATH'] + 'questionspage/savePassageDetails',
            type : 'POST',
            data : currentQuestion,
            success : function(response) {
                Helpers.ajax_response( savePassageDetails , response , []);
            },
            timeout : serverResponseTimeLimit,
            error : function(response){
                Helpers.ajax_response( savePassageDetails , response , []);
            }
        });

        //added by nivedita calling the function
        //addToDic(currentQuestion);
        //end
        break;

    case 'freeQues':
        case 'speaking':
    case 'passageQues':
    case 'introduction':
    case 'game':
    case 'gre':                               
        getNextQuestion(currentQuestion);
        break;
    }
}

function savePassageDetails(data, extraParams)
{
    //passageObject.controls.enablePassageViewerControl();
    if (currentQuestion.complete == 1 && step_after_rating == 1) {
        //Reset the step after plugin to 0. For the prev page in the passage viewer.
        step_after_rating = 0;
        getNextQuestion(currentQuestion);
    }
    if(currentQuestion.qType=='passage' && currentQuestion.complete != 1)
       imageExpantion();
}

function imageExpantion(){

    var w = $(window).width();
    var h = $(window).height();
    var img_w = 800;
    var img_h = 450;
    crosW = (w - img_w)/2 + 4;
    crosH = (h - img_h)/2 - 6;
    w = (w / 2) - (img_w / 2);
    h = (h / 2) - (img_h / 2);
    var html = "<div id='popUpImage' class='modal'  style='display: none;position: fixed;left: 0;width: 100%;height: 100%;background-color: rgba(0,0,0,0.4); z-index:100;'><span class='imagePopUpClose'style ='color:#000000;float:left;font-size:30px;font-weight:bold;top:"+crosH+"px;right:"+crosW+"px;position:absolute;cursor:pointer;z-index:100;'>X</span><img id='upImage'  style ='display: block;'></div>"
    $(".passage").append(html);
    $(".passage img").css({'cursor':'pointer'});
    var popUpImg = document.getElementById('popUpImage');
    var upImg = document.getElementById("upImage");
    $(".passage img").bind('click',function()
    {       
        popUpImg.style.display = "block";
        upImg.src = this.src;
         $(upImg).css({'display': 'block','width': img_w+'px','height': img_h+'px','position': 'fixed','left':w+'px','top':h+'px'});
    });

   /* $(".imagePopUpClose").hover(function(){$(this).css({'color':'#ffffff'})},function(){$(this).css({'color':'#8E2800'})});*/

    $(".imagePopUpClose").bind('click',function(){
            popUpImg.style.display = "none";
            upImg.src = "";
    });

    $("#popUpImage").bind('click',function(e){
        if(e.target.id == 'popUpImage'){
         popUpImg.style.display = "none";
         upImage.scr = "";
      }
    });

}


function showInstructions() {
    hidePluginIfAny();
    if (sessionData.mode == 'diagnostic') {
        diagnosticTest.showInstructions();
    } else {
        enableTooltips();
    }
}
/*
 * param1: key: sets tooltip for particular element when passed. 
 */
function setTooltips(key) {
    $(document).tooltip({
        position : {
            my : "left top",
            at : "right top"
        }
    });
    $(document).tooltip('enable');
    // ALWAYS keep above piece of code before setting title - else resets last added title text insead of new
    if (key) {
        $('#' + key).attr('title', tooltips[key]);
        $('#' + key).addClass('showTooltip');
    } else {
        for (var key in tooltips) {
            $('#' + key).attr('title', tooltips[key]);
            $('#' + key).addClass('showTooltip');
        }
    }

}

function enableTooltips() {
    setTooltips();
    $('.navbar').css('opacity', '0.8');
    $('#contentContainer').css('opacity', '0.8');
    document.getElementsByClassName('navbar')[0].addEventListener('click', disAllowClicks, true);
    document.getElementById('contentContainer').addEventListener('click', disAllowClicks, true);
    $('body').append('<div id="toast" class="toastInfo hovers"> <div class="prompt-heading"><button class="close-prompt toast-close" prompt-close=\'#toast\'><i class="fa fa-close"></i></button> </div> Hover over the elements you want help with or <button> start </button> an auto tour.</div>');

    var buttons = $('.toastInfo button:not(".close-prompt")');
    buttons[0].addEventListener('click', startTour, true);
    $('.toastInfo')[0].addEventListener('click', onHoverClick, true);
}

var timeoutIDs = [];

//show a tip for an element. The element id is passed as key. Tip needs to be defined in "tooltips" object declared above
function flashTip(key,customMsg) {
    var $element = $('#' + key);
    if($element.length > 0){
        if (typeof customMsg === "undefined" || customMsg === null) {
        customMsg=tooltips[$element[0].id];
        }
        
        
        $element.attr('title',customMsg);
        $element.addClass('hasToolTip');
        $element.tooltip({
            position : {
                my : "left top",
                at : "right top"
            },
            open : function(event, ui) {
                $(this).addClass('tooltipHighlight');
                if($element[0].id == 'sbi_the_classroom')
                {
                    $('.ui-tooltip-content').addClass('left-arrow-pointer');    
                }else if($element[0].id == 'sbi_essay_writer')
                {
                    var tooltipId=$("li#"+$element[0].id).attr('aria-describedby');
                    $('#'+tooltipId).css('margin-left','30px');
                    $('.ui-tooltip-content').addClass('left-arrow-pointer');    
                }
            },
            close : function(event, ui) {
                $(this).removeClass('tooltipHighlight');
            }
        });

        $element.tooltip('open');
        var timer = 6000;
        if($element[0].id == 'sbi_the_classroom')
            timer = 12000;
        if($element[0].id == 'sbi_essay_writer')
            timer = 12000;
        setTimeout(function() {
            $element.tooltip('close');
            $element.tooltip('disable');
        }, timer);
    }
}

/*
 * Auto tour. Iterates throught the tooltips.
 */
function startTour() {
    var timeout = 0;
    $(document).tooltip('disable');
    $('.toastInfo').hide();
    for (var key in tooltips) {
        if (tooltips.hasOwnProperty(key)) {
            var $element = $('#' + key);
            if (key === 'endTour') {
                timeoutIDs.push(setTimeout(function() {
                    $('.hasToolTip').tooltip('close');
                    $('.hasToolTip').tooltip('disable');
                    setTooltips();
                    $('.toastInfo').show();
                }, (timeout) * 4000));
                continue;
            }

            //dont create tool tips in tour for these cases.
            if (!$element.is(":visible") || $element.css('visibility') === "hidden" || $element.css('opacity') === '0')
                continue;

            timeout++;

            timeoutIDs.push(setTimeout((function($element) {
                return function() {
                    $('.hasToolTip').tooltip('close');
                    $('.hasToolTip').tooltip('disable');
                    $element.attr('title', tooltips[$element[0].id]);
                    $element.addClass('hasToolTip');
                    $element.tooltip({
                        position : {
                            my : "left top",
                            at : "right top"
                        },
                        open : function(event, ui) {
                            $(this).addClass('tooltipHighlight');
                        },
                        close : function(event, ui) {
                            $(this).removeClass('tooltipHighlight');
                        }
                    });
                    $element.tooltip('open');
                };
            })($element), (timeout - 1) * 4000));
        }
    }
}

function disAllowClicks(e) {
    e.stopPropagation();
    e.preventDefault();

    //endTour
    for ( i = 0; i < timeoutIDs.length; i++) {
        clearTimeout(timeoutIDs[i]);
    }
    disableToolTips();
}

/*
 * param1: key: id of the element. If passed disables tooltip only for that element.
 */
function disableToolTips(key) {
    if (key) {
        $('#' + key).tooltip('disable');
        return;
    }

    $('#contentContainer').css('opacity', '1');
    $('.navbar').css('opacity', '1');
    $('.showTooltip').removeClass('showTooltip');
    try{
        $(document).tooltip('disable');
        document.getElementsByClassName('navbar')[0].removeEventListener('click', disAllowClicks, true);
        document.getElementById('contentContainer').removeEventListener('click', disAllowClicks, true);
        $('.toastInfo')[0].removeEventListener('click', onHoverClick, true);
        $('.toastInfo').remove();
        $('.hasToolTip').tooltip('close');
        $('.hasToolTip').tooltip('disable');
    }
    catch(e){}

}

/*
 * Show list of essays that can be attempted.
 */
function showEssaysToAttempt(event) {
    $(event.target).hide();
    $('.tableContainer').hide();
    $('#essaysToAttempt').show();
    $("#essaysToAttemptLi > li").each(function( index ) {
        var existingEssay = $(this).attr('essay-id');
        if(existingEssay == 0)
        {
            $(this).removeAttr('class');
            $(this).attr('class', 'list-group-item newEssay ng-binding ng-scope');
        }
        else
        {
            $(this).removeAttr('class');
            $(this).attr('class', 'list-group-item essaySummay ng-binding ng-scope');   
        }
    });
    $("#startEssay").show();
    $("#ownTopic").val('');
}

/*
 * param1: mode: mode = 1 is to save and submit essay, else only saves essay. 
 */
var canSaveEssay = true;

function saveEssay(mode) {

    //for hiding the keyboard that is kept open after clicking on save button 
        tinyMCE.execCommand('mceFocus',false,'essay');
    //end

    if($("#submitEssay").html().trim() != "Submit" && mode == 1)
        return;

    essayWriterMsgFlag = mode;
    if(mode != 'silent')
    {
        $("#saveEssay").attr('disabled',true);
        $("#submitEssay").attr('disabled',true);
    }
    clearTimeout(saveEssayIntervalId);
    var essayObject = {};
    //essayObject.userID = sessionData.userID;
    essayObject.essayID = currentQuestion.value.split('||')[0];
    essayObject.topicID = currentQuestion.qID;
    essayObject.info = {};
    essayObject.info.userResponse = tinyMCE.activeEditor.getContent();
    essayObject.info.timeTaken = sessionData.timeSpentInEssay;
    //essayObject.info.timeTaken = 50;
    var wordCount = tinymce.activeEditor.plugins.wordcount.getCount();


    if (!essayObject.info.userResponse.trim() && mode == 1) {
        Helpers.prompt('Please write your essay before clicking on the submit button.');
        $("#saveEssay").attr('disabled',false);
        $("#submitEssay").attr('disabled',false);
        return;
    }
    //var text = tinyMCE.activeEditor.getContent();
    //checking if entered words count < 30
    
	
    if(mode == 1 && wordCount < 30)
    {
        Helpers.prompt("Your essay is too short! <br>Please write at least 30 words.");
        $("#saveEssay").attr('disabled',false);
        $("#submitEssay").attr('disabled',false);
        return;
    }
    //for hiding the keyboard that is kept open after clicking on save button 
        //tinyMCE.execCommand('mceFocus',false,'essay');
    //end
    canSaveEssay = false;
    essayObject.status = (!mode || mode == 'silent') ? 0 : mode;
    essayObject.page = sessionData.currentLocation.type;
    $.ajax({
        type : 'POST',
        url : Helpers.constants['CONTROLLER_PATH'] + "home/saveEssayDetails",
        data : essayObject,
    }).done(function(data) {
        Helpers.ajax_response(essaySaveOrSubmit, data, [mode]);
    });
};
function essaySaveOrSubmit(data , extraParams)
{
   /*document.activeElement.blur();*/
    
    var mode = extraParams[0];  
    $("#saveEssay").removeAttr('disabled');
    $("#submitEssay").removeAttr('disabled');  
        
    sessionData.currentLocation.location = null;
    if (mode === 1 && data == "isJunk") {
        return;
    }
    if (mode === 1) {
        canSaveEssay = true;
        $('#sbi_essay_writer').trigger('click');
        Helpers.prompt('Your essay has been sent for evaluation.');
        //$("#submitEssay").remove();
        $("#submitEssay").html('You can submit your essay after 15 day(s).');
        $("#submitEssay").attr('data-value','You can submit your essay after 15 day(s).');
        //$("#submitEssay").prop('disabled',true);
    } else {
        if (mode == 'silent') {
            canSaveEssay = true;
            return;
        }
        canSaveEssay = true;
    }

}
function hidePluginIfAny()
{   
    // Rating Plugin.
    try{
        // Reset the rating step variable
        step_after_rating = 0;
        Helpers.close_prompt();
        rating_object.vanish();
        rating_object = Helpers.destroy_object();

    }catch(e){}

    try{
        Helpers.data.toastElement.fadeOut();
        angular.element(document.body).scope().hideNotification();
        essayWriterMsgFlag = '';
    }
    catch(e)
    {

    }
    try{
        audioObject.stop();
    }
    catch(e){}
    $('.ui-tooltip').hide();
    clearTimeout(saveEssayIntervalId);
    $(".footer").show();
    $("#contentContainer").css('height','calc(100% - 75px)');
    //$("#gameFrame").attr('src','');
}

function showEssayWriter()
{
    stopSnowFall();
    $(".moduleContainer").hide();
    $(".essay_writer").show();
}
function showGameContainer()
{
    stopSnowFall();
    $(".moduleContainer").hide();
    $(".gameContainers").show();
    $(".footer").hide();
    $("#contentContainer").css('height','calc(100% - 50px)');
}
function showGradedEssayContainer()
{
    stopSnowFall();
    $(".moduleContainer").hide();
    $(".gradedEssayContainer").show();
}
function showProfileContainer()
{
    stopSnowFall();
    $(".moduleContainer").hide();
    $(".profileContainer").show();
    $(".comment").shorten();
    sessionData.currentLocation.location = 'profile_details';
    sessionData.currentLocation.type = 'profile_details';
    sidebarToggle(false);
    hidePluginIfAny();
    stopAndHideOtherActivities();
    removeFlashTooltips();
}
function showHomePage()
{
    sessionData.currentLocation.type = 'home';

    stopSnowFall();
    startSnowFall();
    if(sessionData.category == "STUDENT")
    {
        $(".moduleContainer").hide();
        $(".home_page").show();
        $(".homepage").show();
    }
    else
    {
        showTeacherHomePage();
    }
}
function showAppContainer()
{
    stopSnowFall();
    if(sessionData.currentLocation.value != 'sessionreport')
    {
        $(".moduleContainer").hide();
        $(".appContainers").show();
    }
}
function showResetPasswordPage()
{
    stopSnowFall();
    $(".moduleContainer").hide();
    $(".resetPassword").show();   
    sidebarToggle(false);
}
function showSessionReportPage()
{
    
    stopSnowFall();
    $("#dictionaryButton").prop('disabled', false);
    $(".moduleContainer").hide();
    $(".sessionReportContainer").show(); 
    //For displaying the session report after 30 min prompt
    $('.sessionH1,.sessionH2').show();
    $('#homeReport,#homeReportNCQ,#homeReportEssay').show();
    
    if($("#sessionReport").find( "dt" ).length <= 1) {
        $('#homeReport').hide();
    }
    if($("#sessionReportNCQ").find( "dt" ).length <= 1) {
        $('#homeReportNCQ').hide();
    }
    if($("#sessionReportEssay").find( "dt" ).length <= 1) {
        $('#homeReportEssay').hide();
    }  
}
/*function showTeacherReport()
{
    $(".moduleContainer").hide();
    $(".teacher_report").show(); 
}*/
function showTeacherHomePage()
{
    stopSnowFall();
    $(".v2-usage-container").hide();
    $(".moduleContainer").hide();
    $('#teacher_home').show();
    $('#teacher_home > .none').show();
}
function showTeacherReportsPage()
{
    stopSnowFall();
    $(".moduleContainer").hide();
    $('#teacher_reports_page').show();
    $('#teacher_reports_page > .none').show();
    $('#teacherReportDatatable').hide();
}
function showEssayEvaluation()
{
    stopSnowFall();
    $(".moduleContainer").hide();
}
function showTeacherMyStudentsPage()
{
    stopSnowFall();
    $(".moduleContainer").hide();
    $('#teacher_my_students').show();
    $('#teacher_my_students > .none').show();
}
function showTeacherAllotedEssaysPage()
{
    stopSnowFall();
    $(".moduleContainer").hide();
    $('#teacher_essay_allotment').show();
    $('#teacher_essay_allotment div > .none').show();
}
function showTeacherDoMindsparkPage()
{
    stopSnowFall();
    $(".moduleContainer").hide();
    $('#teacher_do_mindspark').show();
    $('#teacher_do_mindspark > .none').show();
}
function showTeacherSettingsPage()
{
    stopSnowFall();
    $(".moduleContainer").hide();
    $('#teacher_settings').show();
    $('#teacher_settings > .none').show();
    
}
        
function accessDocument(e)
{

    try
    {
        if($("#"+document.activeElement.id).prop('contenteditable') == "true")
        return;
    }
    catch(e){}


    if(sessionData.currentLocation.location != 'dictionary' && (sessionData.multipleSelection == '' || sessionData.multipleSelection == undefined))
    {
         if (e.target.type == 'textarea')
        {
            if (e.keyCode == 13) {
                if( Helpers.object_length(rating_object) > 0)
                {
                   if(defaults.parameters.contentType=='gre')
                        rating_object.save_rating('gre');  
                    else
                        rating_object.save_rating();
                }
            }   
            //return;
        }

        if (e.keyCode == 13 && currentQuestion.json.qType !="openEnded") {
            if (!$('#prompt').is(':visible')) {
                e.preventDefault();
                //need to replace it with the common button.
                if ($('#questionSubmitButton:visible').length > 0) {
                    $('#questionSubmitButton').trigger('click');
                    return;
                }
            }
            if ($('#passageNext').is(':visible')) {
                $('#passageNext').trigger('click');
                return;
            }
        }
    }
}

function passageViewer(e)
{
    if(sessionData.currentLocation.location != 'dictionary' && (sessionData.multipleSelection == '' || sessionData.multipleSelection == undefined))
    {
        if (e.keyCode == 13) {
            if ($("#passageContainer").css('display') == 'block')
            {
                $('.navigationButton.rightButton').trigger('click');
                /*if($('.rating-feedback').is(':visible'))
                {
                    rating_object.save_rating();       
                }*/
            }
        }
    }
}

$("#merge").click(function(){
    essayWriterMsgFlag = '';
});

//added by nivedita testing my dictionary
englishInterface.controller('myDictionaryController', ['$scope', '$http', '$rootScope',
function($scope, $http, $rootScope) {
    //$scope.getAlphaValue('A', '');
    //setting tabs
    this.tab = 1;
    $scope.notInDic = false;
    $scope.showPreviousPage = false;
    $scope.alphabet = 'A';
    //$scope.pageNo = 1;
    $scope.pageNo = 1;
    $scope.limit = 20;

    this.setTab = function (tabId) {
        this.tab = tabId;

        if(tabId == 1)
        {
            /*$(".alphaBtns").attr('disabled', 'disabled');
            $scope.meaningSearch = false;
            $("#search_in_dict").val('');*/
            $(".alphaBtns").removeAttr('disabled', 'disabled');
            $scope.meaningSearchMyDict = false;
            $scope.getAlphaValue('A');

        }
        else if(tabId == 2)
        {
            $(".alphaBtns").removeAttr('disabled', 'disabled');
            $scope.meaningSearchMyDict = false;
            $scope.getAlphaValue('A');
        }
    };

    this.isSet = function (tabId) {
        return this.tab === tabId;
    };
    //end

    $scope.temparray2 = [];
    $scope.meaningSearch = false;
    //$scope.finalArray = [];
    var i,j,temparray,chunk = 10;

    $scope.alphabetes = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    
    $scope.getMeaning = function(value)
    {

        // getting the value for each searched word 
        var searchValue = value;
        if(searchValue != '')
        {

            /*if(searchValue.length >= 3)
                $scope.getReport();*/
        }
        else
        {
            $scope.meaningSearch = false;
            $scope.meaningSearchMyDict = false;
            $scope.getAlphaValue('A', '', 0);
        }
    }
    $scope.getReport = function() {
        

        var allFlag = 1;
        var searchBox = $('#search_in_dict').val();
        /*if($scope.showPreviousPage == true)
            $scope.showPreviousPage = true; 
        else*/
            $scope.showPreviousPage = false;

        //for the purpose of the reference id and reference type
        var referenceText = searchBox;

        if(referenceText !== undefined && referenceText != '')
        {
            setFlagForReference = true;
        }
        else
        {
            /*sessionData.qID = '';
            sessionData.qType = '';*/
            setFlagForReference = false;
        }
        clearSelection(); 

        searchBox = searchBox.replace(/ /g,'');
        $scope.notInDic = false;
        $scope.meaningSearchMyDict = false;

        if(searchBox !== undefined && searchBox != '')
        {

            if(/^[A-Za-z.-]+$/.test(searchBox.trim()) == false)
            {
                $(".ui-dialog").show();
                Helpers.prompt('Your word contains illegal characters please correct the word');
                return;
            }
            else
                searchBox = searchBox.trim();
        }
        
        if(searchBox == '')
            allFlag = 0;

        if (!allFlag) 
        {
            Helpers.prompt('Please type word to search');
            return;
        }

        //get the first letter of the searched word 
        var getFirstLetter = searchBox.charAt(0).toUpperCase(); 
        //console.log(getFirstLetter);
        $scope.getAlphaValue(getFirstLetter, searchBox);
        
        
        $http.get(Helpers.constants['CONTROLLER_PATH'] + 'myDictionary/searchMeaningsUser/' + searchBox +'/').success(function(data) 
        {
            userSearchFlag = 0;
            //$scope.meaningSearch = false;
            $scope.meaningSearchMyDict = false;
            
            if (data != "" && data != 'null' )// if no data found
            {
                //console.log('m here');
                //console.log($scope.finalArray);
               /* $scope.finalArray = [];*/
                $scope.finalArray1 = [];

                $scope.finalArray = [{ sr_no: "1", user_word: data.user_word}];
                //console.log($scope.finalArray);
                //$scope.meaningSearch = true;
                $scope.meaningSearchMyDict = true;
                $scope.notInDic = false;
                //save the word in the database
                //addToDic('',searchBox);
                //change user_word, wordtype, definition once change the table name in the modal getWordMeaning();
                $scope.word       = data.user_word;
                $scope.type       = data.wordType;
                $scope.definition = data.definition;
            } 
            else 
            {

                $http.get(Helpers.constants['CONTROLLER_PATH'] + 'myDictionary/searchMeanings/' + searchBox +'/').success(function(dataUser) 
                {
                   // console.log('m here1');

                    if(dataUser == '' || dataUser === 'null')
                    {
                        
                        $(".ui-dialog").show();
                        $("#prompt").show();
                        //alert($scope.showPreviousPage);
                        Helpers.prompt('No such word found.');
                        //$('#search_in_dict').val('');
                        //$scope.getAlphaValue('A', '', 0); Commented by Aditya so that User Remain on same page
                         $scope.notInDic = false;
                        //$scope.meaningSearch = false;
                        $scope.meaningSearchMyDict = false;
                    }
                    else
                    {
                        //console.log('m here3');
                        //////////////
                        //$scope.finalArray = [];
                        //$scope.finalArray1 = [];
                        //$scope.finalArray = [{ sr_no: "1", user_word: dataUser.user_word}];
                        
                        $scope.meaningSearchMyDict = true;
                        $scope.notInDic = true;
                        //change user_word, wordtype, definition once change the table name in the modal getWordMeaning();
                        $scope.word       = dataUser.user_word;
                        $scope.type       = dataUser.wordType;
                        $scope.definition = dataUser.definition;
                    }
                });
            }
        }).error(function(data) {
        });
    };

    $scope.getNext = function(pageNo, alphabet) {
      
        /*if($scope.temparray2[pageNo] !== undefined)
            $scope.finalArray = $scope.temparray2[pageNo];*/
           // console.log(pageNo);
        if(pageNo != 0)
            $scope.showPreviousPage = true; 

        $scope.limit = $scope.limit + 20;
        $scope.getAlphaValue(alphabet, '', $scope.limit);
        $scope.pageNo = pageNo + 1;
        //console.log($scope.pageNo);
        if($scope.meaningSearchMyDict = true)
            $scope.meaningSearchMyDict = false;
        
        
    };

    $scope.getPrevious = function(pageNo, alphabet) {

        /*if($scope.temparray2[pageNo] !== undefined)
            $scope.finalArray = $scope.temparray2[pageNo];*/
        $scope.limit = $scope.limit - 20;
        $scope.getAlphaValue(alphabet, '', $scope.limit);
        $scope.pageNo = pageNo - 1;
        //console.log($scope.pageNo);
        if($scope.pageNo == 1)
            $scope.showPreviousPage = false;

        if($scope.meaningSearchMyDict = true)
            $scope.meaningSearchMyDict = false;
        
    };

    $scope.getData = function(rowData) {
        //$scope.meaningSearch = true;
        $("#search_in_dict").val('');
        $scope.notInDic = false;
        $scope.meaningSearchMyDict = true;

        $scope.word       = rowData.user_word;
        $scope.type       = rowData.wordType;
        $scope.definition = rowData.definition;
    };
    $scope.check = function ($event) {
        if ($event.target.tagName === 'BUTTON') {
        } 
        else 
        {
            $event.preventDefault(); // should never reach this, if a link is clicked.
            $event.stopPropagation(); // should never reach this, if a link is clicked.

            //$scope.meaningSearchMyDict = false;
        };   
    }

    //$scope.getAlphaValue = function(value, searchBox, limit, offset)
    $scope.getAlphaValue = function(value, searchBox, limit)
    {
        if(value != '')
        {
            var value = value.trim();
        }
        $scope.pageNo = 1;
        
        $scope.meaningSearchMyDict = false;
        
        //show button selected
        var get_id = $scope.alphabetes.indexOf(value);
        for (var i = 0; i < $scope.alphabetes.length; i++) 
        {
            if(get_id == i)
            {
                //console.log('match');
                $("#alpha_"+i).removeAttr('class');
                $("#alpha_"+i).attr('class', 'form-control btn btn-sm alphaBtns ng-binding ng-scope selected-btn-dict');
            }
            else
            {
                //console.log('not_match');
                $("#alpha_"+i).attr('class', 'form-control btn btn-sm alphaBtns ng-binding ng-scope');   
            }

        }
        //end

        if(limit == '' || limit == undefined)
        {
            $scope.limit = 0;
            limit = $scope.limit;
        }

        $scope.notInDic       = false;
        var alphabet          = value;
        $scope.alphabet       = value;
        $scope.words          = [];
        $scope.completeResult = [];
        
        //$scope.finalArray = [];
        $scope.temparray2 = [];
        $scope.temparray3 = [];
        $scope.checkForSearch = [];

        //get the total rows needed for pagination
        $http.get(Helpers.constants['CONTROLLER_PATH'] + 'myDictionary/getMeanings/' + alphabet +'/'+limit+'/count').success(function(getCount) 
        {
            
            $scope.completeResult = getCount.completeResult;
            $scope.total_rows     = getCount.total_rows;

            if($scope.total_rows == 0 || $scope.total_rows < 20)
                $scope.showPreviousPage = false;
        });
        //end

        //get the data
        $http.get(Helpers.constants['CONTROLLER_PATH'] + 'myDictionary/getMeanings/' + alphabet +'/'+limit+'/').success(function(data) {
            $scope.words = data;

            //show,hide next button
            if($scope.words.length == 0 || $scope.words.length < 20 || $scope.total_rows == 20)
                $scope.showNextPage = false;
            else
                $scope.showNextPage = true;
            
            //for (k=0; k< $scope.words.length; k++)  
            if($scope.limit == 0)
            {
                for (var k=0; k< $scope.words.length; k++)  
                {
                    $scope.words[k].sr_no = k + 1;
                }
            }
            else
            {
                var j = 1;
                
                for (var k = 0; k < $scope.words.length; k++)  
                {
                    $scope.words[k].sr_no = $scope.limit + j;
                    j++
                }   
            }

            //check if not in my dictionary thus showing button and a message
            /*if(searchBox != '' && searchBox != 'null' && searchBox !== undefined)
            {
                //for(var k = 0; k < $scope.words.length; k++) 
                for(var k = 0; k < $scope.completeResult.length; k++) 
                {
                    $scope.checkForSearch.push($scope.completeResult[k].user_word);   
                }
                
                var inarrayCheck = $scope.checkForSearch.indexOf(searchBox);
                if(inarrayCheck == -1)
                {
                    $scope.notInDic = true;
                }
            }*/
            //end

            //array chunks
            var i,j,temparray, temparray1,chunk = 10;
            //for (i=0,j= $scope.words.length; i<j; i+=chunk) 
            for (i=0,j= chunk; i<j; i+=chunk) 
            {
                temparray =  $scope.words.slice(i,i+chunk);
                $scope.temparray2.push(temparray);
            }
            //checking
            if($scope.words.length >= chunk)
            {
                for (i=chunk,j= $scope.words.length; i<j; i+=chunk) 
                {
                    temparray1 =  $scope.words.slice(i,i+chunk);
                    $scope.temparray3.push(temparray1);
                }
            }
            //end
            
            $scope.finalArray = $scope.temparray2[0];

            $scope.finalArray1 = $scope.temparray3[0];
            

           
            /*for (k=1; k<= $scope.words.length; k++)  
            {
                $scope.testing = $scope.words[k].tooltip.split("|");
                $scope.spliting = $scope.testing[0]+ "\n" +$scope.testing[1]+"\n"+$scope.testing[2];
                $scope.words[k].sending = $scope.spliting;
                // $scope.words[k].sr_no = k;
            }*/
            
        });

        var getsearchboxchar = $("#search_in_dict").val();
        getsearchboxchar     = getsearchboxchar.trim();
        value                = value.trim();
        if(getsearchboxchar != '')
        {
            getsearchboxchar = getsearchboxchar.charAt(0).toUpperCase(); 
            if(getsearchboxchar !== value && userSearchFlag == 0)
            {
                $("#search_in_dict").val('');
            }
        }
    }

    //calling this function on page load;
    $scope.getAlphaValue('A', '', 0);

    $scope.trigerEnterKey = function(event, tab)
    {
        if(event.which == 13)
        {
            if(tab == 1)
            {
                //alert(navigator.appCodeName);
                //sidebarToggle(true);
                var ua = window.navigator.userAgent;
                var msie = ua.indexOf("MSIE "); 
                if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))
                {
                    Helpers.prompt('hello');
                }

                $scope.getReport();
                //( "#search_in_dict_btn" ).trigger( "click" );
                //angular.element('#search_in_dict_btn').triggerHandler('click');
            }
        }
    }

    $scope.addToDic = function(searchBoxValue)
    {
        var searchBoxValue = $("#search_in_dict").val();

        if(searchBoxValue == '' || searchBoxValue == undefined)
            searchBoxValue = $("#search_in_dict").val();
                   
        if(searchBoxValue != '' && searchBoxValue != undefined)
        {
            searchBoxValue = searchBoxValue.trim();
            addToDic('',searchBoxValue);
            $scope.meaningSearchMyDict = false;

            var getFirstLetter = searchBoxValue.charAt(0).toUpperCase(); 
            $scope.getAlphaValue(getFirstLetter, searchBoxValue);
        }
    }

    $scope.closeNote = function()
    {
        if($scope.word !== undefined && $scope.word != '')
        {
            var getFirstLetter = $scope.word.charAt(0).toUpperCase(); 
            $scope.getAlphaValue(getFirstLetter);
        }
        //$("#search_in_dict").val('');
        $scope.meaningSearchMyDict = false;
    }

}]);

/*AUTOCOMPLETE START*/
$( function() {
    $( "#search_in_dict" ).autocomplete({
        //appendTo: "#autocompleteDiv",
        //minLength: 3,
        //source: availableTags,
        source: function( request, response ) { 
            $.ajax({
                type : "POST",
                url: Helpers.constants.CONTROLLER_PATH + 'myDictionary/getDicWords',
                dataType: "json",
                data: {
                    'word':request.term
                },
                success: function( data ) {
                    if(data.length > 0)
                        response( data );
                    else
                    {
                        $(".ui-menu-item").hide();
                        $("#ui-id-1").css('z-index', '-1');
                    }
                    return;
                }
            });
        },
        minLength:3,
        select: function (e, ui) {
            
            $(".ui-menu-item").hide();
            $("#ui-id-1").css('z-index', '-1');
        },
        change: function (e, ui) {
            $(".ui-menu-item").hide();
        },
    }).keyup(function (e) {
        var getword = $("#search_in_dict").val();
        if(getword.length >= 3)
        {
          $(".ui-autocomplete").css('z-index', '2147483647');  
        }
        else if(getword.length == 0)
        {
            $(".ui-menu-item").hide();
            $("#ui-id-1").css('z-index', '-1');
        }
        if(e.which === 13) {
            $("#ui-id-1").css('z-index', '-1');
            $(".ui-menu-item").hide();
        }            
    });
});
/*AUTOCOMPLETE END*/

englishInterface.directive('tooltip', function () {
    return {
        restrict:'A',
        link: function(scope, element, attrs)
        {
            $(element)
                .attr('title', scope.$eval(attrs.tooltip))
                .tooltip({placement: "right"});
        }
    }
});

function addToDic(currentQuestion, searchBoxValue)
{
    //var myWord = searchBoxValue;
    var myWord = $("#search_in_dict").val();
    if(myWord !== undefined && myWord != '')
        myWord = myWord.trim();

    if(setFlagForReference !== undefined && setFlagForReference != '' && setFlagForReference == true)
    {
        //var qType = currentQuestion.qType;
        var qType = sessionData.qType;
        if(qType == 'passage')
        {
            //var referenceID = currentQuestion.passageID;
            var referenceID = sessionData.qID;
            var referenceType = 'P';
        }
        else if(qType == 'passageQues' || qType == 'freeQues')
        {
            //var referenceID = currentQuestion.qcode;
            var referenceID = sessionData.qID;
            var referenceType = 'Q';
        }
    }
    else
    {
        var referenceID = '';
        var referenceType = '';
    }
    
    $.ajax({
            type : "POST",
            url : Helpers.constants.CONTROLLER_PATH + 'myDictionary/saveUserWord',
            data:{'word' : myWord, 'referenceID' : referenceID, 'referenceType' : referenceType},
            "async" : false,
            dataType:'json',
            success: function(data) 
            {    

                /*var getFirstLetter = myWord.charAt(0).toUpperCase(); 
                angular.element(document.getElementById("dragg")).scope().getAlphaValue(getFirstLetter, myWord);  */
                //Helpers.ajax_response( getMakeWordsLibResult, data, [possibleOptions, minWordLength]);
            }
        });
        $("#search_in_dict").val('');
       /* $(function() {
            $.contextMenu({
                selector: 'p.context-menu-one', 
                callback: function(key, options) {
                    var m = "clicked: " + key;
                    var myWord = getSelectionText();
                    if(myWord == '')
                        Helpers.prompt('Please select the word to add in the dictionary!');
                    else
                    {
                        $.ajax({
                            type : "POST",
                            url : Helpers.constants.CONTROLLER_PATH + 'myDictionary/saveUserWord',
                            data:{'word' : myWord, 'referenceID' : referenceID, 'referenceType' : referenceType},
                            "async" : false,
                            dataType:'json',
                            success: function(data) 
                            {      
                                //Helpers.ajax_response( getMakeWordsLibResult, data, [possibleOptions, minWordLength]);
                            }
                        });
                        console.log(getSelectionText());
                    }
                },
                items: {
                    "dictionary": {name: "Add to Dictionary"},
                }
            });

            $('.context-menu-one').on('click', function(e){
                console.log('clicked', this);
            })    
        });*/
}


function getSelectionText() 
{
    var text = "";
    if (window.getSelection) {
        text1 = window.getSelection();
        //alert(text1);
        text = window.getSelection().toString();
        //alert(text);
    } 
    else if (document.selection && document.selection.type != "Control") 
    {
        text = document.selection.createRange().text;
        //alert('m here');
    }

    return text;
}
/*function makeEditableAndHighlight(colour) 
{
    var text = "";
    var range, sel = window.getSelection();
    if (sel.rangeCount && sel.getRangeAt) {
        range = sel.getRangeAt(0);
    }
    document.designMode = "on";
    if (range) 
    {
        sel.removeAllRanges();
        sel.addRange(range);
        text = range.toString();
    }

    // Use HiliteColor since some browsers apply BackColor to the whole block
    // if (!document.execCommand("HiliteColor", false, colour)) 
    // {
    //     document.execCommand("BackColor", false, colour);
    // }
    // document.designMode = "off";
    return text;
}
function getSelectionText() 
{
    var seleText = '';
    var range, sel;
    if (window.getSelection) {
        // IE9 and non-IE
        try {
            //if (!document.execCommand("BackColor", false, colour)) {

            seleText =   makeEditableAndHighlight();
            //}
        } catch (ex) {
            makeEditableAndHighlight();
        }
    } else if (document.selection && document.selection.createRange) {
        // IE <= 8 case
        range = document.selection.createRange();
        //range.execCommand("BackColor", false, colour);
    }
    return seleText;
}*/

function clearSelection() 
{
    if ( document.selection ) {
        document.selection.empty();
    } else if ( window.getSelection ) {
        window.getSelection().removeAllRanges();
    }
}

//document.oncontextmenu = document.body.oncontextmenu = function() {return false;}
$(function(){
    /*
	jQuery.browser.msie = false;
    jQuery.browser.version = 0;
    if (navigator.userAgent.match(/MSIE ([0-9]+)\./)) {
        jQuery.browser.msie = true;
        jQuery.browser.version = RegExp.$1;
    }
    
    if ($.browser.msie) {
        this.doc.body.contentEditable = true;
    }  
	$( "#dialog-6" ).dialog({
        autoOpen: false, 
        modal: true,
           
        width: 800,
        height: 631,
        closeText : "&#215;",
        //close : $(this).dialog('close'),
        open: function(event, ui) {
            //$(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
        }
    });*/
    
    $("#passageQuestionsContainer").on(Helpers.constants.EVENTUP,function(e){
        userSelectedText = getSelectionText();
    });

    $( "#dictionaryButton" ).on(Helpers.constants.EVENTDOWN, function(e) {

        $(".newEssay").removeClass('active');
        $(".essaySummay").removeClass('active');

        // Added 
        userSearchFlag = 0;
        if($(this).prop('disabled'))
            return;

        /*setTimeout(function(){
            $("#search_in_dict").focus();
        }, 1);*/

        $(".diaLog-explanation").hide();
        sessionData.previousLocation         = sessionData.currentLocation.location;
        sessionData.previousType             = sessionData.currentLocation.type;
        sessionData.currentLocation.location = "dictionary";
        sessionData.currentLocation.type     = "dictionary";
        removeFlashTooltips();
      
        var modal = document.getElementById('myModal');

        var scope = angular.element(document.getElementById("dragg")).scope();
        scope.showPreviousPage = false; 

        $("#search_in_dict").focus(200);
        $("#search_in_dict").val('');

        //var selectedText = userSelectedText;
        var selectedText = '';
        selectedText = getSelectionText();
        //alert(selectedText);
        //return;
        var getWords = '';
        //clearSelection(); // dsselects the selection
        if(selectedText !== undefined && selectedText != '')
        {
            setFlagForReference = true;
            getWords     = selectedText.split(/[ ]+/);
            getWords     = getWords.filter(Boolean);
            $("#search_in_dict").val('');
        }
        else
            setFlagForReference = false;

        //calling functin of angular
       /* scope.$apply(function () {
            scope.meaningSearchMyDict = false;
            scope.getAlphaValue('A', '', 0);
        });*/
        //end
        if(getWords.length > 1)
        {
            
            $(".diaLog-explanation").show();
            Helpers.prompt('Select only one word to see the meaning!!');
            sessionData.multipleSelection = 'yes';
            sessionData.currentLocation.location = 'the_classroom';

        }
        else
        {
            sessionData.multipleSelection = '';
            $(".diaLog-explanation").hide();
            modal.style.display = "block";
            if(selectedText !== undefined && selectedText != '')
            {
                //$("#hidden_selected_text").val(selectedText);
                //$("#search_in_dict").val(selectedText);

                //calling functin of angular
                scope.$apply(function () {
                    $("#search_in_dict").val(selectedText);
                    userSearchFlag = 1;
                    scope.getReport();
                    userSelectedText = '';
                });
                //end
            }
            else
            {
                //calling functin of angular
                scope.$apply(function () {
                    scope.meaningSearchMyDict = false;
                    scope.getAlphaValue('A', '', 0);
                });
                //end
            }
           //$( "#dialog-6" ).dialog( "open" );
        }
    });

// tinymce configuration for the essay textarea
	
tinymceConfig = {
    mode:"exact",
    elements:"essay",
    menubar: false,
    statusbar: false,
    plugins: ["lists", "wordcount","paste","autoresize"],
    content_css : '/techmCodeCommit/mindsparkProduct/mindspark/ms_english/theme/css/Language/editor.css',
    toolbar : "bold italic underline bullist",
    resize:"height",
	force_br_newlines : true,
    force_p_newlines : false,
    autoresize_max_height: 325,
    paste_preprocess : function(pl, o) {
      o.content = '';
    },
    setup : function(editor,o) {
        if ($('#essay').prop('readonly')) {
            editor.settings.readonly = true;
        }
       editor.on('PostProcess', function(ed) {
                // we are cleaning empty paragraphs
                ed.content = ed.content.replace(/(<p>&nbsp;<\/p>)/gi,'<br />');
            });

        editor.on('keydown', function (evt) {
           if(currentQuestion.info.submitted)
               return; 
           var wordCount = 0;
           var valid_keys = [8, 46];
           text = editor.getContent().replace(/(< ([^>]+)<)/g, '').replace(/\s+/g, ' ');
           text = text.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
           wordCount = text.split(' ').length;

           theEditor = tinymce.activeEditor;
           wordCount = theEditor.plugins.wordcount.getCount();

           if(text == "")
                wordCount = 0;

           if(wordCount >= Helpers.constants.MAX_WORDS && valid_keys.indexOf(evt.keyCode) == -1)
            {
                evt.preventDefault();
                Helpers.prompt('You have reached the maximum word limit.');
                //evt.stopPropagation();
                return false;
            }
        });
		
		
		editor.on('postRender', function(event) {
		  var target = event.target;
		  if (!target || !target.contentWindow || !target.contentWindow.focus || typeof target.contentWindow.focus !== 'function') {
			return false;
		  }

		  var refocus = function() {
			target.contentWindow.focus();
		  };
          target.contentDocument.addEventListener('keydown', refocus);
		  target.contentDocument.addEventListener('touchstart', refocus);
		  target.contentDocument.addEventListener('touchend', refocus);
		});
		
		
		

        editor.on('keyup', function (evt) {
            if(currentQuestion.info.submitted)
               return;
            var text = '';
            clearTimeout(saveEssayIntervalId);
            saveEssayIntervalId = setTimeout(function() {
                saveEssay('silent');
            }, 3000);
            
            text = editor.getContent().replace(/(< ([^>]+)<)/g, '').replace(/\s+/g, ' ');
            text = text.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
            var wordCount = text.split(' ').length;

            theEditor = tinymce.activeEditor;
            wordCount = theEditor.plugins.wordcount.getCount();

            if(text == "")
                wordCount = 0;

            $("#essay_instructions .textAreaAfter").html("[ Words entered: "+wordCount+" ]");
        });
    }   
}
;

// Initializes the tinymce configuration for essay textarea

tinyMCE.init(tinymceConfig);

});

function removeFlashTooltips()
{
    $('.ui-tooltip-content').removeClass('left-arrow-pointer');   
    $('.ui-tooltip').hide();
    //$(".ui-dialog").removeClass("diaLog-explanation");
    //$(".ui-dialog").hide();
    $("#toast").hide();
    //$(".diaLog-explanation").hide();
    //$("#prompt").hide();
}

// Get the modal
var modal = document.getElementById('myModal');

// Get the button that opens the modal
//var btn = document.getElementById("myBtn");
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal

// When the user clicks the button, open the modal
/*btn.onclick = function() {
    modal.style.display = "block";
}*/

// When the user clicks on <span> (x), close the modal
/*var span = document.getElementsByClassName("closee")[0];
span.onclick = function() {
    modal.style.display = "none";
    sessionData.currentLocation.location = '';
    $(".diaLog-explanation").show();
    $("#prompt").show();
}*/

function closeModal()
{
    $("#search_in_dict").autocomplete("close");
    $(".ui-menu-item").hide();
    $("#ui-id-1").css('z-index', '-1');

    modal.style.display = "none";
    if(sessionData.previousLocation != '')
        sessionData.currentLocation.location = sessionData.previousLocation;
    else
        sessionData.currentLocation.location = '';

    if(sessionData.previousType != '')
        sessionData.currentLocation.type = sessionData.previousType;
    else
        sessionData.currentLocation.type = '';
   // $(".diaLog-explanation").show();       // commented this part 
    $("#prompt").show();

    if(sessionData.currentLocation.type =='freeQues'||sessionData.currentLocation.type=='passageQues' )
         $(".diaLog-explanation").show();
     else
        $(".diaLog-explanation").hide();

}   

// When the user clicks anywhere outside of the modal, close it
/*window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}*/
 /*$("#dragg").draggable({
    containment: "parent",
    scroll: false
 });*/

 /*HELP TOUR CAROUSEL*/
 
function elementContainsSelection(el) {
    if (window.getSelection) {
        if(window.getSelection().rangeCount > 0)
        {
           if (!isOrContains(essaySelection.commonAncestorContainer, el)) {
             return false;
         } 
        }
        
        return true;
      
    } else if ( (sel = document.selection) && sel.type != "Control") {
        return isOrContains(sel.createRange().parentElement(), el);
    }
    return false;
}

function isOrContains(node, container) {
    while (node) {
        if (node === container) {
            return true;
        }
        node = node.parentNode;
    }
    return false;
}

var getSelectedRange = function() {
   try {
        if (window.getSelection) {
            if(window.getSelection().rangeCount > 0){
                essaySelection = window.getSelection().getRangeAt(0);
            }
        } else {
              if(window.getSelection().rangeCount > 0){
                essaySelection = document.getSelection().getRangeAt(0);
              }  
        }
    } catch (err) {

    }
}

$(document).ready( function() {

    $('#myCarousel').carousel({
        //interval: false
    });
   

    $("input").keydown(function(event){
        if($(this).attr('length') != undefined)
        {
            var length = $(this).attr('length');
            event.keyCode = (event.keyCode != 0) ? event.keyCode : event.which; // mozilla hack..
            if(event.keyCode != 9 && event.keyCode != 8 && event.keyCode!=37 && event.keyCode!=38 && event.keyCode!=39 && event.keyCode!=40)
            { 

                if($(this).val().trim().length > length)
                {
                    $(this).val($(this).val());
                    event.preventDefault();
                }
            }
        }
    });
    /* $('#addCommentBtn').click(function() {
         alert("Hi");
         getSelectedRange();
         alert(essaySelection);
         angular.element(document.getElementById('essay_evaluation')).scope().getSelection();
    });*/

    $('#addCommentBtn').on(Helpers.constants.EVENTDOWN,function(){
        if(!(navigator.userAgent.match(/iPad/i) || navigator.userAgent.match(/iPhone/i))){
                getSelectedRange();
        }   
         
        angular.element(document.getElementById('essay_evaluation')).scope().getSelection();
        $("#essay_detail li").each(function(){ 

            if($(this).children().length > 0)
            {
                if($(this).children().text() == '')
                {
                    $(this).remove();
                }
            }
            if($(this).text() == '' || $(this).text() == ' ')
            {
               $(this).remove();
            }
            
        });  

    });

    if((navigator.userAgent.match(/iPad/i) || navigator.userAgent.match(/iPhone/i))){
        document.onselectionchange = userSelectionChanged;
    }
    
    function userSelectionChanged() {
        
        // wait 10 ms after the last selection change event
        if (selectionEndTimeout) {
            clearTimeout(selectionEndTimeout);
        }

        selectionEndTimeout = setTimeout(function () {
            $(window).trigger('selectionEnd');
        }, 10);
    }

    $(window).bind('selectionEnd', function () {

        // reset selection timeout
        selectionEndTimeout = null;
        
        // get user selection
        getSelectedRange();
        
    });

    /*var hammertime = new Hammer(document.getElementById('essay_detail'));
    
    hammertime.on('pan', function(ev) {
       
    });*/

    /*$('#essay_detail').on('mouseup touchend', function() { 
         getSelectedRange();
    });
    */
    var clickEvent = false;
    $('#myCarousel').on('click', '.nav a', function() {
            clickEvent = true;
            $('.nav li').removeClass('active');
            $(this).parent().addClass('active');        
    }).on('slid.bs.carousel', function(e) {
        if(!clickEvent) {
            var count = $('#help_ul').children().length -1;
            var current = $('.nav li.active');
            current.removeClass('active').next().addClass('active');
            var id = parseInt(current.data('slide-to'));
            if(count == id) {
                $('.nav li').first().addClass('active');    
            }
        }
        clickEvent = false;
    });
});

$("#helpButton").on('click', function() 
{
    removeFlashTooltips(); 
    var modal = document.getElementById('myModalHelpTour');
    modal.style.display = "block";
});

function closeHelpModal()
{
    var helpModal = document.getElementById('myModalHelpTour');
    helpModal.style.display = "none";
}

function activityCloseButtonAction()
{   
    $("#gameFrame").attr('src','');
    $('.gameContainers').hide();
    $('#activitySelector').show();
}
function startSnowFall(){
    if($("#user_theme").val() != 'christmas')
    {
        return;
    }
    (function() {
    $("#canvas").show();
    var requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame ||
    function(callback) {
        window.setTimeout(callback, 1000 / 60);
    };
    window.requestAnimationFrame = requestAnimationFrame;
    })();


    var flakes = [],
    canvas = document.getElementById("canvas"),
    ctx = canvas.getContext("2d"),
    flakeCount = 400,
    mX = -100,
    mY = -100

    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight - 25;

    function snow() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

        for (var i = 0; i < flakeCount; i++) {
            var flake = flakes[i],
                x = mX,
                y = mY,
                minDist = 150,
                x2 = flake.x,
                y2 = flake.y;

            var dist = Math.sqrt((x2 - x) * (x2 - x) + (y2 - y) * (y2 - y)),
                dx = x2 - x,
                dy = y2 - y;

            if (dist < minDist) {
                var force = minDist / (dist * dist),
                    xcomp = (x - x2) / dist,
                    ycomp = (y - y2) / dist,
                    deltaV = force / 2;

                flake.velX -= deltaV * xcomp;
                flake.velY -= deltaV * ycomp;

            } else {
                flake.velX *= .98;
                if (flake.velY <= flake.speed) {
                    flake.velY = flake.speed
                }
                flake.velX += Math.cos(flake.step += .05) * flake.stepSize;
            }

            ctx.fillStyle = "rgba(255,255,255," + flake.opacity + ")";
            flake.y += flake.velY;
            flake.x += flake.velX;
                
            if (flake.y >= canvas.height || flake.y <= 0) {
                reset(flake);
            }


            if (flake.x >= canvas.width || flake.x <= 0) {
                reset(flake);
            }

            ctx.beginPath();
            ctx.arc(flake.x, flake.y, flake.size, 0, Math.PI * 2);
            ctx.fill();
        }
        requestAnimationFrame(snow);
    };

    function reset(flake) {
        flake.x = Math.floor(Math.random() * canvas.width);
        flake.y = 0;
        flake.size = (Math.random() * 3) + 2;
        flake.speed = (Math.random() * 1) + 0.5;
        flake.velY = flake.speed;
        flake.velX = 0;
        flake.opacity = (Math.random() * 0.5) + 0.3;
    }

    function init() {
        for (var i = 0; i < flakeCount; i++) {
            var x = Math.floor(Math.random() * canvas.width),
                y = Math.floor(Math.random() * canvas.height),
                size = (Math.random() * 3) + 2,
                speed = (Math.random() * 1) + 0.1,
                opacity = (Math.random() * 0.5) + 0.3;

            flakes.push({
                speed: speed,
                velY: speed,
                velX: 0,
                x: x,
                y: y,
                size: size,
                stepSize: (Math.random()) / 30,
                step: 0,
                opacity: opacity
            });
        }

        snow();
    };

    canvas.addEventListener("mousemove", function(e) {
        mX = e.clientX,
        mY = e.clientY
    });

    window.addEventListener("resize",function(){
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    })

    init();
}

function stopSnowFall(){
    $("#canvas").hide();
}
/*HELP TOUR CAROUSEL END*/

/*TO GET OS INFO*/
// function getOS(obj) {

//     var os=new Array("unknown", "unknown");

//     (isEmpty(obj) ? brs=navigator.userAgent.toLowerCase() : brs=obj);
//     if (brs.search(/windows\sce/) != -1) {
//         os[0]="Windows CE";
//         try {
//             os[1]=brs.match(/windows\sce\/(\d+(\.?\d)*)/)[1];
//         } catch (e) { }
//         return os;
//     } 
//     else if ( (brs.search(/windows/) !=-1) || ((brs.search(/win9\d{1}/) !=-1))) {
//         os[0]="Windows";
//         if (brs.search(/nt\s5\.1/) != -1) {
//             os[1]="XP";
//         }else if (brs.search(/nt\s6\.1/) != -1) {
//             os[1]="7";
//         }else if (brs.search(/nt\s6\.0/) != -1) {
//             os[1]="Vista";
//         }else if (brs.search(/nt\s6\.2/) != -1) {
//             os[1]="8";
//         }else if (brs.search(/nt\s6\.3/) != -1) {
//             os[1]="8.1";
//         }else if (brs.search(/nt\s5\.0/) != -1) {
//             os[1]="2000";
//         } else if ( (brs.search(/win98/) != -1) || (brs.search(/windows\s98/)!= -1 ) ) {
//             os[1]="98";
//         } else if (brs.search(/windows\sme/) != -1) {
//             os[1]="Me";
//         } else if (brs.search(/nt\s5\.2/) != -1) {
//             os[1]="Windows 2003";
//         } else if ( (brs.search(/windows\s95/) != -1) || (brs.search(/win95/)!= -1 ) ) {
//             os[1]="95";
//         } else if ( (brs.search(/nt\s4\.0/) != -1) || (brs.search(/nt4\.0/) ) != -1) {
//             os[1]="NT 4";
//         }

//         return os;
//     } else if (brs.search(/linux/) !=-1) {
//         os[0]="Linux";
//         var brsValue=brs.split(";");
//         if(brs.search("ubuntu") != -1){
//             os[1]="Ubuntu";
//         }
//         else if (brs.search("android") != -1) {
//             if(brs.search(/chrome\/(\d)*/) != -1){
//                 os[1]=brsValue[1];
//             }else{
//                 os[1]=brsValue[2];
//             }
//         }
//         try {
//             os[1] = brs.match(/linux\s?(\d+(\.?\d)*)/)[1];
//         } catch (e) { }
//         return os;
//         } 
//     else if (brs.search(/mac\sos\sx/) !=-1) {
//         os[0]="Mac OS";
//         var osVersion=brs.split(";");
//         var osVersion1=osVersion[1].split(")");
//         var osVersion2=osVersion1[0].split("os");
//         var osVersion3=osVersion2[1].split("like");
//         os[1]= osVersion3[0].replace(/_/g,".");
//         os[1]= os[1].replace("x","X");
//         return os;
//     } else if (brs.search(/freebsd/) !=-1) {
//         os[0]="Free BSD";
//         try {
//             os[1] = brs.match(/freebsd\s(\d(\.\d)*)*/)[1];
//         } catch (e) { }
//         return os;
//     } else if (brs.search(/sunos/) !=-1) {
//         os[0]="Sun Solaris";
//         try {
//             os[1]=brs.match(/sunos\s(\d(\.\d)*)*/)[1];
//         } catch (e) { }
//         return os;
//     } else if (brs.search(/irix/) !=-1) {
//         os[0]="Irix";
//         try {
//             os[1]=brs.match(/irix\s(\d(\.\d)*)*/)[1];
//         } catch (e) { }
//         return os;
//     } else if (brs.search(/openbsd/) !=-1) {
//         os[0]="Open BSD";
//         try {
//             os[1] = brs.match(/openbsd\s(\d(\.\d)*)*/)[1];
//         } catch (e) { }
//         return os;
//     } else if ( (brs.search(/macintosh/) !=-1) || (brs.search(/mac\x5fpowerpc/) != -1) ) {
//         os[0]="Mac Classic";
//         var osVersion=brs.split(";");
//         var osVersion1=osVersion[1].split(")");
//         var osVersion2=osVersion1[0].split("os");
//         var osVersion3=osVersion2[1].split("like");
//         os[1]= osVersion3[0].replace(/_/g,".");
//         os[1]= os[1].replace("x","System");
//         return os;
//     } else if (brs.search(/os\/2/) !=-1) {
//         os[0]="OS 2";
//         try {
//             os[1]=brs.match(/warp\s((\d(\.\d)*)*)/)[1];
//         } catch (e) { }
//         return os;
//     } else if (brs.search(/openvms/) !=-1) {
//         os[0]="Open VMS";
//         try {
//             os[1]=brs.match(/openvms\sv((\d(\.\d)*)*)/)[1];
//         } catch (e)  { }
//         return os;
//     } else if ( (brs.search(/amigaos/) !=-1) || (brs.search(/amiga/) != -1) ) {
//         os[0]="Amigaos";
//         try {
//             os[1]=brs.match(/amigaos\s?(\d(\.\d)*)*/)[1];
//         } catch (e) { }
//         return os;
//     } else if (brs.search(/hurd/) !=-1) {
//         os[0]="Hurd";
//         return os;
//     } else if (brs.search(/hp\-ux/) != -1) {
//         os[0]="HP UX";
//         try {
//             os[1]=brs.match(/hp\-ux\sb\.[\/\s]?(\d+([\._]\d)*)/)[1];
//         } catch (e) { }
//         return os;
//     } else if ( (brs.search(/unix/) !=-1) || (brs.search(/x11/) != -1 ) ) {
//         os[0]="Unix";
//         os[1]="";
//         return os;
//     } else if (brs.search(/cygwin/) !=-1) {
//         os[0]="CygWin";
//         os[1]="";
//         return os;
//     } else if (brs.search(/java[\/\s]?(\d+([\._]\d)*)/) != -1) {
//         os[0]="Java";
//         try {
//             os[1]=brs.match(/java[\/\s]?(\d+([\._]\d)*)/)[1];
//         } catch (e) { }
//         return os;
//     } else if (brs.search(/palmos/) != -1) {
//         os[0]="Palm OS";
//         os[1]="";
//         return os;
//     } else if (brs.search(/symbian\s?os\/(\d+([\._]\d)*)/) != -1) {
//         os[0]="Symbian";
//         try {
//             os[1]=brs.match(/symbian\s?os\/(\d+([\._]\d)*)/)[1];
//         } catch (e) { }
//         return os;
//     } else {
//         os[0]="unknown";
//         if (brs.search("android") != -1 && brs.search("firefox") != -1) {
//                 os[0]="Linux";
//                 os[1]="Android";
//         }
//         return os;
//     }
// }
// // Is input empty?
// function isEmpty(input) {
//     return (input==null || input =="")
// }

/*TO GET OS INFO END*/

function imgNotLoading()
{
    /*to log in db if image not loading*/
    if(sessionData.currentLocation.location == 'the_classroom')
        var page   = sessionData.currentLocation.type;
    else
        var page = sessionData.currentLocation.location;

    var itemid = sessionData.qID;
    var msg    = 'img not loading';
    
    $.ajax({
        type : "POST",
        url : Helpers.constants.CONTROLLER_PATH + 'home/logForImgAudioNotLoading',
        data:{'page' : page, 'itemid' : itemid, 'msg' : msg},
        "async" : false,
        dataType:'json',
        success: function(data) 
        {    
        }
    });
    /*end*/
}
function isMobile(){
         var isMobile = false; //initiate as false
// device detection
                                if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
                                        || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0, 4)))
                                    isMobile = true;
                                return isMobile;
    }