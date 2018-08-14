/**
 * @author Kalpesh
 *
 * Dependencies: dialog depends on jquery UI.
 * Events refer to Helpers with Helpers reference, routines refer to it with 'this' reference
 *
 */
var offlineDomain = !(/27\.109\.14\.77:8080|192\.168\.0\.61|122\.248\.236\.40|192\.168\.0\.114|localhost|13\.251\.143\.110|192\.168\.1\.242|192\.168\.0\.7|educationalinitiatives\.com|mindspark\.in/.test(document.URL));
PATH_PRE = offlineDomain ? 'S3_Mseng/' : 'https://disxat8ptf3fv.cloudfront.net/'; 
var authorize = { 'key' : '' , 'category' : ''};
var isOnIOS = navigator.userAgent.match(/iPad/i)|| navigator.userAgent.match(/iPhone/i);
var eventName = isOnIOS ? "pagehide" : "beforeunload";

Helpers = {
    constants : {
        LIVE_CONTENT_PATH : PATH_PRE,
        CONTROLLER_PATH : 'Language/',
        THEME_PATH : 'theme/',
        LOGIN_PATH : '../../../../mindsparkProduct/msengFinalVersion/mindspark/login/',
        REARCH_LOGIN_PATH:'https://mindspark.in/Mindspark/Login/',
        UI_THEME_IMAGES : $("#img_ref").val(),
        IGRE_PATH :  offlineDomain ? 'S3_Mseng/enrichment_modules/html5/msenglish/' : 'https://disxat8ptf3fv.cloudfront.net/enrichment_modules/html5/msenglish/',
        // IGRE_PATH :  offlineDomain ? 'http://localhost/S3_Mseng/enrichment_modules/html5/msenglish/' : 'http://d2tl1spkm4qpax.cloudfront.net/Enrichment_Modules/html5/msenglish/',
        GET_PASSAGE_PATH : '/mindsparkProduct/msengFinalVersion/mindspark/mslanguage/ProofTypePassageBranch/src/saverLoader.php',
        MULTITAB : '/mindsparkProduct/msengFinalVersion/mindspark/ms_english/Language/session/multitab',
        PASSAGE_FOLDER : 'passages/eng/',
        MAX_WORDS : 600,
        PROMPT_ID : 'prompt',
        POPOVER : 'popover',
        TOAST_ID : 'toast',
        IDLE_TIME : 60,
         MSLANGUAGE_PATH : '/mindsparkProduct/msengFinalVersion/mindspark/mslanguage/',
        EVENTDOWN: isOnIOS ? "touchstart" : "mousedown",
        EVENTUP: isOnIOS ? "touchend" : "mouseup",
        PLAYSOUND : {}    
    },
    secretQuestion : {
        0 : 'Select',
        1 : 'What is your place of birth?',
        2 : 'Who is your favourite cricketer?',
        3 : 'What is your favourite car?',
        4 : 'Who is your favourite actor or actress?',
        5 : 'What is your favorite colour?'
    },
    data : {
        toastTimeoutId : 0,
        toastElement : null,
    },
    create_constant : function(para_object)
    {
        Object.freeze(para_object);
    },
    valid_student : function(){

        if(Helpers.object_length(sessionData) == 0)
        {
            window.location.assign(this.constants.LOGIN_PATH);
        }

	},
    valid_request : function(data){
        if( data.toString().indexOf('Click Here to Login') != -1 )
        {
            return false;
        }
        else
        {
            return true;
        }
    },
    unauthorized_user : function()
    {
        //For invalid session]
        
        tryingToUnloadPage = true;
        goToLogin();
        return;
    },
    // Loaders
    loadJs : function(src, callback) {
        var s = document.createElement('script');
        s.src = src;
        s.async = true;
        s.onreadystatechange = s.onload = function() {
            var state = s.readyState;
            if (callback) {
                if (!callback.done && (!state || /loaded|complete/.test(state))) {
                    callback.done = true;
                    callback();
                }
            }
        };
        document.getElementsByTagName('head')[0].appendChild(s);
    },
    preloadImages : function(url, callback) {
        var imageArray = [];
        if ( typeof url === 'string') {
            imageArray.push(url);
        } else if (url.constructor === Array) {
            imageArray = url;
        }

        var count = 0;
        var onImageLoad = function() {
            count++;
            if (count >= imageArray.length) {
                callback && callback();
            }
        };
        
        var onImageError = function(){
            console.log(this.src + ' was not found.');
            /*to log in db if image not loading*/
            /*var page   = sessionData.currentLocation.type;
            var itemid = sessionData.qID;
            var msg    = 'image not loading';
            console.log('m here');
            $.ajax({
                type : "POST",
                url : Helpers.constants.CONTROLLER_PATH + 'home/logForImgAudioNotLoading',
                data:{'page' : page, 'itemid' : itemid, 'msg' : msg},
                "async" : false,
                dataType:'json',
                success: function(data) 
                {    
                }
            });*/
            /*end*/
            onImageLoad();
        };

        if (imageArray.length == 0) {
            onImageLoad();
        }

        for (var i = 0; i < imageArray.length; i++) {
            var image = new Image();
            image.src = imageArray[i];
            image.onload = onImageLoad;
            image.onerror = onImageError;
        }
    },
    getUrlParameters : function() {
        var queryString = {};
        var query = window.location.search.substring(1);
        var vars = query.split("&");
        for (var i = 0; i < vars.length; i++) {
            var pair = vars[i].split("=");
            if ( typeof queryString[pair[0]] === "undefined") {
                queryString[pair[0]] = pair[1];
            } else if ( typeof queryString[pair[0]] === "string") {
                var arr = [queryString[pair[0]], pair[1]];
                queryString[pair[0]] = arr;
            }
        }
        return queryString;
    },
    //DOM Manipulation, Walking, Creation
    createElement : function(tagName, options) {
        var element = document.createElement(tagName);
        if ( typeof options === 'string') {
            element.innerHTML = options;
        } else if ( typeof options === 'object') {
            this.applyProperties(element, options);
        }
        return element;
    },
    populateSelectElement : function(element, source) {
    	this.append = function() {
            source.unshift('Choose one');
            for (var i = 0; i < source.length; i++) {
                $(element).append(this.createElement('option', {
                    value : source[i],
                    html : source[i]
                }));
            }
        };

        $(element).html('');

		if (Object.prototype.toString.call(source) === "[object Array]") {
            this.append();
        } else if ( typeof source === 'object') {
            for (var key in source) {
                if (source.hasOwnProperty(key)) {
                    $(element).append(this.createElement('option', {
                        html : source[key],
                        value : key
                    }));
                }
            }
        } else if ( typeof source === 'string') {
            source = source.split(',');
            this.append();
        }
    },
    applyProperties : function(element, options) {
        options.html && (element.innerHTML = options.html);
        options.id && (element.id = options.id);
        options.click && $(element).bind('click', options.click);
        options.className && (element.className = options.className);
        options.value && (element.value = options.value);
        options.href && (element.href = options.href);
        options.type && (element.type = options.type);
        if(this.object_length(options.attributes) > 0)
            $.each(options.attributes, function(attribute,attribute_value){
                element.setAttribute(attribute,attribute_value);
            });
    },
    closestParent : function(element, tagName) {
        while (!(element == undefined || element.tagName == tagName)) {
            element = element.parentNode;
        }
        return element;
    },

    //general Help
    isBlank : function(value) {
        if (value === undefined)
            return true;

        if ( typeof value === 'string') {
            if (value.trim() == '') {
                return true;
            } else if ( typeof value === 'number') {
                if (isNaN(value)) {
                    return true;
                }
            }
        }
    },
    isResponseValid : function(e) {
        if (e == '') {
            return true;
        }

        var json;
        var responseJson = false;
        try {
            json = JSON.parse(e);
            responseJson = true;
        } catch (e) {
            responseJson = false;
        }

        if (responseJson) {
            if (json.eiMsg && json.eiDebug) {
                alert(json.eiMsg);
                return false;
            } else {
                return true;
            }
        } else {
            if (/^((?=.*\berror\b)|(?=.*\bwarning\b))(?=.*\bsql\b).*$/.test(e)) {
                return false;
            } else {
                return true;
            }
        }
    },
    checkNumWords : function(e) {
        var valid_keys = [8, 46];
        // 8-backspace, 46-delete
        var words = this.value.split(' ');

        if (words.length >= Helpers.constants.MAX_WORDS && valid_keys.indexOf(e.keyCode) == -1) {
            e.preventDefault();
            words.length = Helpers.constants.MAX_WORDS;
            this.value = words.join(' ');
            //this refers to the element this event has been assigned to.
            Helpers.prompt('You have reached the maximum word limit');
        }
    },
    decimalToPlaces : function(string, places) {
        return parseFloat(parseFloat(string).toFixed(places));
    },
    toast : function(text, persist) {
        clearTimeout(this.data.toastTimeoutId);
        this.data.toastElement.html(text);
        this.data.toastElement.show();
        if (!persist) {
            this.data.toastTimeoutId = setTimeout(function() {
                Helpers.data.toastElement.fadeOut();
            }, 10000);
        }
    },
    notification : function(msg){
        if(msg !== undefined && msg !== '')
            angular.element(document.body).scope().showNotificationToUser(msg);
    },
    /*Disabling back: crude method before routing comes into play*/
    disableHistoryNavigation: function() {
        if(!history.pushState){
            return;
        }
        history.pushState(null, null, '/mindsparkProduct/msengFinalVersion/mindspark/ms_english/Language/session');
        window.addEventListener('popstate', function(event) {
            history.pushState(null, null, '/mindsparkProduct/msengFinalVersion/mindspark/ms_english/Language/session');
        });
    },
    multitab : function(){
        
		if( localStorage.getItem('tab_info') == null )
        {
            window.name = authorize.key;
            sessionStorage.setItem('windowName',authorize.key);
            localStorage.setItem('tab_info',sessionStorage.getItem('windowName'));
        }
        else    
        {
           
            if(localStorage.getItem('tab_info') != sessionStorage.getItem('windowName'))
            {
                tryingToUnloadPage = true;
                return true;
            }
            return false;
        }
    },
    prompt : function(options) {
        // if the prompt is already open do nothing and return early
        /*if (!options.force && $('#' + this.constants.PROMPT_ID).is(':visible')) {
            return false;
        }*/
        var modalFlag = true;
        if ( typeof options == 'string') {
            $('#' + this.constants.PROMPT_ID).html(options);
            if(options.title === 'Explanation')
            {
                modalFlag = false;
            }
        } else {
            try{
                
                if(options.title === 'Explanation')
                {
                    modalFlag = false;
                }
                options.text = options.text.replace(/&nbsp;/ig,' ');
            }catch(e){}
            $('#' + this.constants.PROMPT_ID).html(options.text);
        }

        var buttons = [];
        for (var key in options.buttons) {
            if (options.buttons.hasOwnProperty(key)) {
                var buttonObject = {};
                buttonObject.text = key;
                buttonObject.class = 'btn btn-primary small-button';
                buttonObject.click = (function(key) {
                    return function() {
                        if ( typeof options.buttons[key] === 'function') {
                            options.buttons[key]();
                        }
                        $(this).dialog('close');
                    };
                })(key);
                buttons.push(buttonObject);
            }
        }

        //add ok button if there is no Button.
        if (buttons.length == 0 && modalFlag) {
            var buttonObject = {};
            buttonObject.text = 'OK';
            buttonObject.class = 'btn btn-primary small-button';
            buttonObject.click = function() {
                $(this).dialog('close');

                if(sessionData.currentLocation.location != 'comment_system')
                    $("#modalBlockerCommentSystem").hide();
            };
            buttons.push(buttonObject);
        }

        $('#' + this.constants.PROMPT_ID).dialog({
            
            modal : modalFlag,
            closeText : "&#215;",
            dialogClass : options.class,
            title : "",
            // title : options.title || "Information",
            buttons : buttons,
            width : options.width || 350,
            position : {
                my : options.my || 'center',
                at : options.at || 'center',
                of : options.ofObject || window
            },
            close : options.closeFunction,
            open : options.callback,
            beforeClose: function( event, ui ) { promptClose(); }
        }).show();
        $('.ui-dialog-titlebar-close').show();
        $(".ui-dialog").draggable('option', {
            cancel: '.ui-dialog-titlebar-close',
            handle: '.ui-dialog-titlebar, .ui-dialog-content'
        });
        $('.ui-dialog-titlebar-close').attr('title', '');
        
    },
    // Add the popover -- argument for the function.
    // --- The argument is the elements that are to be added in the popover. Pass an array.
    // --- Eg: Simple Text or buttons <p>Text</p> can be created using Helpers.createElement. Can also add multiple elements.
    close_prompt : function()
    {
         $('#' + this.constants.PROMPT_ID).dialog('close');
    },
    popover : function(element_obj)
    {
        if(typeof(element_obj.element) != "undefined")
        {
            var element = element_obj.element;
            var index = $(element).index();
            var element_left = $(element).offset().left/2;
            var element_width = Math.ceil($(element).width() / 2);
            var element_height = Math.ceil($(element).height());
            var popover_width = Math.ceil($('#custom-popover').width() / 2);
            //var left_pos = element_left - (element_width + popover_width - 7);
            var left_pos = (index) * 40;

            $('#custom-popover').css('left', (left_pos+16) + 'px');
            
        }

        if(element_obj.show == 'true')
        {
            $('#custom-popover').show();
        }
        else
        {
            $('#custom-popover').hide();
        }
    },
	shuffleArray:function (array) {
	    var currentIndex = array.length, temporaryValue, randomIndex ;

	  	 // While there remain elements to shuffle...
	 	while (0 !== currentIndex) {

	    	// Pick a remaining element...
	   		randomIndex = Math.floor(Math.random() * currentIndex);
	    	currentIndex -= 1;

	    	// And swap it with the current element.
	    	temporaryValue = array[currentIndex];
	    	array[currentIndex] = array[randomIndex];
	    	array[randomIndex] = temporaryValue;
	  	}

	  	return array;
	},
    // randomize array in place: Please note: don't use array with same elements e.g arr=[0,0,0,0,0,0,0,0,0]
    randomize : function(array) {
        var firstArray = array.slice(0);
        var copyFlag = 0;
        for (var i = array.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var temp = array[i];
            array[i] = array[j];
            array[j] = temp;
        }
        for(i=0; i<array.length; i++)
        {
            if(firstArray[i] == array[i])
            {
                copyFlag++;
            }
        }
        /*if(copyFlag < array.length)
            return array;
        else
           this.randomize(array);*/
       if(copyFlag >= array.length)
             this.randomize(array);

        return array;
    },
    //use this to get a random element from an array. When used with a count you get an array.
    //when used with count = array length. This can effectively randomize the array out of place. Win-win
    getRandom : function(array, count) {
        if(count){
            if(count > array.length){
                count = array.length;
            }
            var toReturn  = [];
            toReturn.push(Helpers.getRandomElement(array));
            var got = 1;
            while(got < count){
                var element = Helpers.getRandomElement(array);
                if(toReturn.indexOf(element) == -1){
                    got++;
                    toReturn.push(element);
                }
            }
            return toReturn;
        }
        else{
            return Helpers.getRandomElement(array);
        }
    },
    getRandomElement : function(array) {
        return array[parseInt(Math.random() * array.length)];
    },
    getTransitionEndEventName : function() {
        var el = document.createElement('fakeelement');
        var transitions = {
            'transition' : 'transitionend',
            'OTransition' : 'oTransitionEnd',
            'MozTransition' : 'transitionend',
            'WebkitTransition' : 'webkitTransitionEnd'
        };

        for (var t in transitions) {
            if (el.style[t] !== undefined) {
                return transitions[t];
            }
        }
    },
    //can be just playSound. can pass arguments as object.
    sndPlayLoadErrEnd : function(sndPath, targObj, strSound, loadedSnd, errorSnd, endSnd) {

        document.getElementById(targObj).src = (sndPath + strSound);
        document.getElementById(targObj).load();
        document.getElementById(targObj).addEventListener("loadeddata", function() {
            this.removeEventListener('loadeddata', arguments.callee, false);
            this.removeEventListener("error", onError, true);
            try {
                document.getElementById(targObj).play();
            } catch(ex) {
            }

            if (loadedSnd)
                loadedSnd();

            document.getElementById(targObj).addEventListener('ended', function() {
                this.removeEventListener('ended', arguments.callee, false);
                this.removeEventListener("error", onError, true);

                if (endSnd)
                    endSnd();
            });
        });

        function onError(e) {

            /*to log in db if image not loading*/
            var page   = sessionData.currentLocation.type;
            var itemid = sessionData.qID;
            var msg    = 'audio not loading';
            
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
            document.getElementById(targObj).removeEventListener("error", onError, true);
            if (errorSnd)
                errorSnd();

        }


        document.getElementById(targObj).addEventListener("error", onError, true);
    },
    getSelectedOption: function(element){
        return element.options[element.selectedIndex];
    },
    getInputBoxValue : function(element)
    {
        var InputValue = angular.element(document.querySelector(element));
        return InputValue.val();
    },
    //can be just loadSounds or preloadSounds. can pass arguments as object.
    loadAllSounds : function(allSndArr, callback, changeSndPath, errorLoadSnd, noCache) {
        changeSndPath = typeof changeSndPath !== 'undefined' ? changeSndPath : "";
        noCache = typeof noCache !== 'undefined' ? noCache : false;
        var loadTimer = null;
        var i = 0;
        var totalSoundLoad = 0;
        var missingSoundFile = "";
        var allsounds = [];
        //issue with chrome on request of more than four parralel requests
        var parallelRequestCount = 10;

        var soundTimer = window.setInterval(function() {

            if ( typeof allSndArr[i] === 'undefined') {
                window.clearInterval(soundTimer);
            } else if (i - totalSoundLoad < parallelRequestCount) {
                loadSound(allSndArr[i]);
                i++;
            }
        }, 300);
        loadTimer = window.setInterval(function() {
            if (totalSoundLoad == allSndArr.length) {
                window.clearInterval(loadTimer);
                if (callback) {
                    callback();
                }

            }
        }, 1000);

        function isMp3Supported() {
            var audioObj2 = document.createElement('audio');
            return (audioObj2.canPlayType('audio/mp4'));
        }

        function loadSound(soundFile) {

            soundFile = (isMp3Supported()) ? soundFile : soundFile.replace(/mp3/g, 'ogg');
            var finalSoundPath = "";
            
            if (changeSndPath == "")
                finalSoundPath = soundFile;
            //finalSoundPath = soundPath+soundFile;
            else
                finalSoundPath = changeSndPath + soundFile;
            if (noCache)
                finalSoundPath += "?" + Math.random();

            var audioObj = document.createElement('audio');
            audioObj.preload = 'auto';

            audioObj.addEventListener("error", function() {
                this.removeEventListener('error', arguments.callee, false);
                if (errorLoadSnd)
                {
                    errorLoadSnd(soundFile);
                }
                loadedFile(this);
            });
            audioObj.addEventListener("canplaythrough", function() {
                this.removeEventListener('canplaythrough', arguments.callee, false);
                allsounds.push(audioObj);
                loadedFile(this);
            
            });
            audioObj.src = finalSoundPath;

            audioObj.style.visibilty = 'hidden';
            (document.body || document.getElementsByTagName("body")[0]).appendChild(audioObj);
            audioObj.load();
            
        }

        function loadedFile(elem) {
            (document.body || document.getElementsByTagName("body")[0]).removeChild(elem);
            totalSoundLoad++;
        }

        return allsounds;
    },

    //changes date format from yyyy-mm-dd to dd-mm-yyyy
    formatDate : function(input) {
        var datePart = input.match(/\d+/g);
        return datePart[2]+'-'+datePart[1]+'-'+datePart[0];
    },
    //changes date format from dd-mm-yyyy to yyyy-mm-dd 
    formatDateSQL : function(input) {
        var datePart = input.match(/\d+/g);
        console.log(datePart);
        return datePart[2]+'-'+datePart[1]+'-'+datePart[0];
    },
    /*checks if number of words < min words 
    arguments: string, check min count or max count, word limit count*/
    checkMinMaxWords : function(my_arr,scenario,count) {
        var wordCount = 0;
        if (my_arr.length == 0) {
            wordCount = 0;
        }
        else
        {
            var regex = /\s+/gi;
            wordCount = my_arr.trim().replace(regex, ' ').split(' ').length;
        }
        /*if(!my_arr)
            my_arr = "";*/
        //var arr;

        //my_arr = my_arr.split(" ");

        /*arr = my_arr;
        for(var i=0;i<my_arr.length;i++) {
            if(my_arr[i] === "")  {
                arr.splice(i,1);
                my_arr=arr;
                i--;
            }
        }*/
        if(scenario == "min") {
            //if(arr.length < count)
            if(wordCount < count)
                return false;
            return true;
        }
        else if(scenario == "max") {
            //if(arr.length > count)
            if(wordCount > count)
                return false;
            return true;
        }
        else if(scenario == "count") {
            //return arr.length;
            return wordCount;
        }
    },
    object_length : function(obj)
    {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) size++;
        }
        return size;
    },
    destroy_object : function()
    {
        return null;
    },
    document_keydown_unbind : function()
    {
        $(document).unbind('keydown');
        documentKeyDown();
        //jQuery(document).keydown(documentReloadEvents);
    },
    check_inactive_interface : function(){
        $(window).off('keyup').on('keyup',function(){
            // User is inactive from 10 minutes.
            inactive_time = 0;
                
        });
        $(window).off('mousemove').on('mousemove',function(e){
            // User is inactive from 10 minutes.
            inactive_time = 0;
        });
    },
    stop_inactive_interface_check : function(){
        $(window).off('keyup');
        $(window).off('mousemove');
    },
    ajax_response : function( callback , data , extraParams){
        if(data.toString().indexOf('<title>Login</title>') != -1)
        {
            
            tryingToUnloadPage = true;
            window.location.assign(Helpers.constants.LOGIN_PATH);
            return;
        }


        if(typeof data === "string")
            data = $.parseJSON(data);

        if( data.active === "true" )
        {
            if(data.status === 'invalid_session')
            {
                // Logout prompt and logout
                
            }
            else if(data.status === 'success')
            {
                if(data.msg != '' && essayWriterMsgFlag != 'silent')
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
                        parameters = $.parseJSON(data.result_data);
                    else
                        parameters = data.result_data;
                    if(typeof extraParams === undefined)
                        callback(parameters);
                    else
                        callback(parameters,extraParams);
                }
                else
                {
                    return true;
                }
            }
            else
            {
                if(data.msg != '')
                {
                    Helpers.prompt({
                        text : '1.2: Oops! There seems to be a technical error. You will be redirected to the home page', // You can answer a few questions till you reconnect.
                        buttons : {
                            'OK' : function() {
                                showHomePage();
                                
                            }
                        },
                        /*closeFunction : function(){
                            tryingToUnloadPage = true;
                            logOutReason = 9;
                            logOut();
                        },*/
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
                        logOutReason = 7;
                        logOut();
                    },
                    noClose : true,
                });

                $('.ui-dialog-titlebar-close').hide();
                return;
            }
             else if(data.status === 0)                                             // Change by Aditya For Network error message in passage.
            {
                 Helpers.prompt({
                    text : 'There appears to be a problem with your network. You will be redirected to the home page. (Status:0)', // You can answer a few questions till you reconnect.
                    buttons : {
                        'OK' : function() {
                            showHomePage();
                        }
                    },
                   /* closeFunction : function(){
                        tryingToUnloadPage = true;
                        logOutReason = 9;
                        logOut();
                    },*/
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
                    },
                    noClose : true,
                });

                $('.ui-dialog-titlebar-close').hide();
                return;   
            }
        }
		
	},
	
	removeHighlight : function(highlightedText){
		var span = document.createElement('span');
			span.innerHTML = highlightedText;
			var allSpans = span.getElementsByTagName('span');
			for (var i=0;i<allSpans.length;i++) {
				if (allSpans[i].style.backgroundColor) {
					allSpans[i].style.backgroundColor = '';
				}
			}   
		return span.innerHTML;
	},

    getSuitableEvent : function(){
        var ua = navigator.userAgent;
        if( ua.indexOf("Android") >= 0 )
        {
          var androidversion = parseFloat(ua.slice(ua.indexOf("Android")+8)); 
          if (androidversion < 4.1)
          {
                Helpers.constants.EVENTDOWN = 'touchstart';
                Helpers.constants.EVENTUP = 'touchend';
          }
        }
    },
    validateEmail : function(email) {
        atpos = email.indexOf('@');
        dotpos = email.lastIndexOf('.');
        if(atpos < 1 || (dotpos - atpos < 2))
        {
            return false;
        }
        return true;
    },
    validateNumber : function(number)
    {
        var regex = /^\d+$/;
        return regex.test(number);
    },
    formatDate : function(input)
    {
        var datePart = input.match(/\d+/g),
        year = datePart[0], // get only two digits
        month = datePart[1], day = datePart[2];
        return day+'/'+month+'/'+year;
    }
	
	
};
// Create constant for Helper File
Helpers.create_constant(Helpers.constants);

/* code to run before navigating away from the page */
var tryingToUnloadPage = false;

function setTryingToUnload() {
    
    tryingToUnloadPage = true;
}

function unsetTryingToUnload() {
    tryingToUnloadPage = false;
}


jQuery(document).ready(function(e) {
    //Helpers.getSuitableEvent();
    /*jQuery("a").click(function() {
        setTryingToUnload();
    });*/
    Helpers.PLAYSOUND = Helpers.loadAllSounds(['correct2.mp3','wrong2.mp3'], '' , Helpers.constants.LIVE_CONTENT_PATH + 'templates_qtype/sounds/');
    $(".correct_audio").attr('src',Helpers.constants.LIVE_CONTENT_PATH + 'templates_qtype/sounds/correct2.mp3');
    $(".wrong_audio").attr('src',Helpers.constants.LIVE_CONTENT_PATH + 'templates_qtype/sounds/wrong2.mp3');
    $("body").on('click','.close-prompt',function(){
        $(document).off('keypress').on('keypress', function(e){accessDocument(e)});
        $($(this).attr('prompt-close')).hide();
        disableToolTips();
        hideHovers();
    });
    window.addEventListener('storage',function(event){
        handleStorage(event);
    }, false);
    function handleStorage(event)
    {
        if(!document.hasFocus() && event.key == 'tab_info')
        {
            clearInterval(timerIntervalId);  
            $(document).children().unbind('click');
            $(document).unbind('keypress');
            setTryingToUnload();
            window.location.assign(window.document.URL);
        }
    }
});
 
function documentKeyDown()
{
    $(document).unbind('keydown').bind('keydown',function(e){
        documentReloadEvents(e);
    });
}
function documentReloadEvents(e)
{
    if ((e.which || e.keyCode) == 116) {
        tryingToUnloadPage = true;
        logOutReason = 5;
    }
    if ((e.which || e.keyCode) == 82 && e.ctrlKey) {
        tryingToUnloadPage = true;
        logOutReason = 5;
    }
    if ((e.which || e.keyCode) == 82 && e.shiftKey && e.ctrlKey) {
        tryingToUnloadPage = true;
        logOutReason = 5;
    }

}
$.fn.shorten = function (settings) {

    var config = {
        showChars: 100,
        ellipsesText: "...",
        moreText: "more",
        lessText: "less"
    };

    if (settings) {
        $.extend(config, settings);
    }
    
    $(document).off("click", '.morelink');
    
    $(document).on({click: function () {

            var $this = $(this);
            if ($this.hasClass('less')) {
                $this.removeClass('less');
                $this.html(config.moreText);
            } else {
                $this.addClass('less');
                $this.html(config.lessText);
            }
            $this.parent().prev().toggle();
            $this.prev().toggle();
            return false;
        }
    }, '.morelink');

    return this.each(function () {
        var $this = $(this);
        if($this.hasClass("shortened")) return;
        
        $this.addClass("shortened");
        var content = $this.html();
        if (content.length > config.showChars) {
            var c = content.substr(0, config.showChars);
            var h = content.substr(config.showChars, content.length - config.showChars);
            var html = c + '<span class="moreellipses">' + config.ellipsesText + ' </span><span class="morecontent"><span>' + h + '</span> <a href="#" class="morelink">' + config.moreText + '</a></span>';
            $this.html(html);
            $(".morecontent span").hide();
        }
    });
    
};
$.fn.navigateList = function(e)
{
    var element = $(this);
    
    $(document).unbind('keydown').bind('keydown',function(e){
        
        documentReloadEvents(e);

        if(tryingToUnloadPage == true)
        {
            return;   
        }

        e.keyCode = (e.keyCode != 0) ? e.keyCode : e.which; // mozilla hack..
        var index = $(element).find('.active').index();
        var max_child = $(element).children().length;

        if(e.keyCode == 13)
        {
            $(document).unbind('keydown');
            $(element).find('.active').trigger('dblclick');
            return;
        }

        if(e.keyCode == 38)
            index--;
        else if(e.keyCode == 40)
            index++;

        if(index < 0)
        {
            if(max_child > 2)
                index = max_child - 2;
            else
                index = 0;
        }
        else if(index > max_child - 2)
            index = 0;

        
        if(e.keyCode == 38 || e.keyCode == 40)
        {
           $(element).children().removeClass('active');
           $($(element).children().get(index)).addClass('active');
        }  
    });
}

onbeforeunload = function()
{
    logoutReason = 5;
    document.getElementById("myself").value =''; // Blank the state out.
    if (tryingToUnloadPage == false) {
        return 'Thanks for using Mindspark';
    }
}

window.onunload = function(event) {
    if(eventName == 'pagehide' && !userLogout)
    {
        alert('Thanks for using Mindspark');
    }
    //save the time taken in class 
    var answer = true;
    jQuery.ajax({
            type : "POST",
            url : Helpers.constants.CONTROLLER_PATH + 'login/timeTakenInClassroom',
            data:{'timeTakenInClassroom' : sessionStorage.getItem("timeTakenInClassroom")},
            "async" : false,
            success : function() {
            }
    });
    
    if (tryingToUnloadPage === false) {
        var logoutTime = 'true';
        jQuery.ajax({
            type : "POST",
            url : Helpers.constants.CONTROLLER_PATH + 'login/updateEndTime',
            data:{'logoutTime' : logoutTime, 'logoutReason':5},
            "async" : false,
            success : function(data) {
                sessionData.timeTakenInClassroom = 0;
                sessionStorage.setItem("timeTakenInClassroom", 0);
                redirect(data);
            }
        });
    }
    
};

function msieversion() 
{
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");
    

    if (msie > 0) // If Internet Explorer, return version number
    {
        return true;
    }
    else  // If another browser, return 0
    {
       return false;
    }
}

window.onhashchange = function(){
    if(window.location.pathname == '/mindsparkProduct/msengFinalVersion/mindspark/login/' || window.location.pathname == '/mindsparkProduct/msengFinalVersion/mindspark/login/index.php')
    {
        setTryingToUnload();
        sessionStorage.clear();
        show_multi_tab_error();
        return;  
    }  
};
document.addEventListener('DOMContentLoaded', function () {

    sidebarToggle();
    sidebarToggle();
    
    if(sessionData.mode == 'preview')
        return;

    authorize.key = $("#authorize").val();
    authorize.category = $("#category").val();
    Object.freeze(authorize);
    if(authorize.category == 'STUDENT')
    {
        var body = document.getElementsByTagName("body")[0];     
        body.addEventListener("load", onDocumentLoad(), false);
    }
    else
    {
        window.name = authorize.key;
        sessionStorage.setItem('windowName',authorize.key);
        localStorage.setItem('tab_info',sessionStorage.getItem('windowName'));
    }
   
});
function show_multi_tab_error()
{
    if(sessionData.mode == 'preview')
        return;
    
    if(authorize.category == "STUDENT")
        window.location.assign(Helpers.constants['MULTITAB']);
    else
        window.location.assign(window.document.URL);

}

function onDocumentLoad()
{
    if(sessionData.mode == 'preview')
        return;
    
    if(document.getElementById("myself").value == '')
    {
        document.getElementById("myself").value = 'myself';

        if(sessionStorage.getItem('windowName') == "" && sessionStorage.getItem('user') == 'yes')
        {
            window.name = authorize.key;
            sessionStorage.setItem('windowName',authorize.key);
            localStorage.setItem('tab_info',sessionStorage.getItem('windowName'));
        }
        
        if(Helpers.multitab())
        {
            setTryingToUnload();
            show_multi_tab_error();
        }
    }
    else
    {
            setTryingToUnload();
            show_multi_tab_error();
        
    }   
}
function promptClose(){
    if (sessionData.category=='School Admin' && sessionData.subcategory=='All') {
        $("#sbi_settings").removeClass('opacity-0');
        $("#activate_students_li").removeClass('opacity-0');
        $("#view_teachers_li").removeClass('opacity-0');
    }
}