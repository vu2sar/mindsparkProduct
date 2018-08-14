/*
 * @author Kalpesh Awathare
 * 
 * Inserts audio elements inside passed audio container. Fetches and stores audio paths.
 * Dependencies:
 * jquery 1.7+
 * jplayer lib
 *  - jplayer.blue.monday.min.css
 *  - jquery.jplayer.min.js
 */

AUDIO = {
    'AUDIO_VIEW': '' +
            '<div id="thisIsListening"><strong>Listen to this conversation carefully, and answer the questions that follow.</strong></div>' + 
            '<div id="jquery_jplayer_1" class="jp-jplayer"></div>' +
            '<div id="jp_container_1" class="jp-audio" role="application" aria-label="media player">' +
              '<div class="jp-type-single" id="audioPlayer">' +
                '<div class="jp-details">' +
                  '<div class="jp-title" aria-label="title">&nbsp;</div>' +
                '</div>' +
                '<div class="jp-gui jp-interface">' +
                  '<div class="jp-controls">' +
                    '<button class="nonTheme jp-play" role="button" tabindex="0">play</button>' +
                    '<button class="nonTheme jp-pause" role="button" tabindex="0">stop</button>' +
                    '<button class="nonTheme jp-stop" role="button" tabindex="0">stop</button>' +
                  '</div>' +
                  '<div class="jp-progress">' +
                    '<div class="jp-seek-bar">' +
                      '<div class="jp-play-bar"></div>' +
                    '</div>' +
                  '</div>' +
                  '<div class="jp-volume-controls">' +
                    '<button class="nonTheme jp-mute" role="button" tabindex="0">mute</button>' +
                    '<button class="nonTheme jp-volume-max" role="button" tabindex="0">max volume</button>' +
                    '<div class="jp-volume-bar">' +
                      '<div class="jp-volume-bar-value"></div>' +
                    '</div>' +
                  '</div>' +
                  '<div class="jp-time-holder">' +
                    '<div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>' +
                    '<div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>' +
                    '<div class="jp-toggles">' +
                      '<button class="nonTheme jp-repeat" role="button" tabindex="0">repeat</button>' +
                    '</div>' +
                  '</div>' +
                '</div>' +
                '<div class="jp-no-solution">' +
                  '<span>Update Required</span>' +
                  'To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.' +
                '</div>' +
              '</div>' +
            '</div>',
};

function Audio(element) {
    /*
     * Holds all the audio data and the functions like gettig audi URL.
     */
    this.Model = function(context) {
        this.parent = context;

        this.storedAudio = {};
        this.currentAudio = {
            'path': '',
            'passageID': '',
            'timeTaken': 0
        };
        this.timerId = 0;

        /*
         *  Get the passage from database. If already loaded once then gets them from the passages stored.
         */
        this.getAudioPath = function(passageID) {
            if (this.storedAudio[passageID]) {
                this.currentAudio = this.storedAudio[passageID];
                this.parent.notifyChange();
                return;
            }
            
            this.currentAudio = {
                'path': '',
                'passageID': passageID,
                'timeTaken': 0
            };
            
            var formData = {
                'loadSaveMode' : 1,
                'passageID' : passageID,
            };
            
            $.ajax({
                context : this,
                url : Helpers.constants['GET_PASSAGE_PATH'],
                type : "POST",
                data : formData,
                dataType: 'json',
            }).done(this.handleGetAudioResponse);
        };
        
        this.handleGetAudioResponse = function(response){
            if (response != '0') {
                this.currentAudio = response;
                this.currentAudio.timeTaken = 0;
                this.currentAudio.path = Helpers.constants.LIVE_CONTENT_PATH + Helpers.constants['PASSAGE_FOLDER'] + response['passageID'] + '/assets/' + response['passageID'];
                this.storedAudio[this.currentAudio.passageID] = this.currentAudio;
            } else {
                alert('no such audio found');
            }

            this.parent.notifyChange();
        };
        
        this.startCountingTime = function(){
            clearInterval(this.timerId);
            this.timerId = setInterval(this.timeStep, 1000);
        };
        
        var _this = this;
        this.timeStep = function(){
            _this.currentAudio.timeTaken++;
        };
        
        this.stopCountingTime = function(){
            clearInterval(this.timerId);
        };
    };

    this.notifyChange = function(){
        AUDIO.currentAudio = this.audioModel.currentAudio;
        //preload the audio
        var audioView = this.view;
        // because now they decided that all firefox versions should load ogg only. irrespective of whethey they support mp3 or not. wo!!.
        //var is_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
        var loadCollection = [AUDIO.currentAudio.path + '.mp3'];
        if(document.createElement('audio').canPlayType('audio/ogg')){
            loadCollection = [AUDIO.currentAudio.path + '.ogg'];
        }
        Helpers.loadAllSounds(loadCollection, function(){
            audioView.update();
        });
    };
    
    /*
     * Creates Views. Functions to show and update Views.
     * param1: model = the model to bind to
     * param2: element = the container inside which views need to be generated.
     */
    this.View = function(model, element) {
        this.model = model;
        this.container = $(element);
        this.mode = 'passage';
        this.container.html(AUDIO.AUDIO_VIEW);
        this.show = function(passageID, callback) {
            this.player = $($('.jp-jplayer', this.container)[0]);
            this.afterViewUpdate = callback;
            this.mode = 'passage';
            this.model.getAudioPath(passageID);
        };
        
        this.showLookback = function(passageID, target, callback) {
            this.mode = "lookback";
            this.lookbackTarget = target;
            
            //If the target was never populated populate it once.
            if($(target).children('.jp-jplayer').length == 0){
                var audioContent = AUDIO.AUDIO_VIEW;
                audioContent = audioContent.replace('jp_container_1', 'jp_container_2');
                audioContent = audioContent.replace('jquery_jplayer_1', 'jquery_jplayer_2');
                $(target).html(audioContent);
                $('#thisIsListening', target).remove();
            }
            $(this.lookbackTarget).append('<div class="prompt-heading"><button class="close-prompt toast-close" prompt-close=\'#audioLookBack\'><i class="fa fa-close"></i></button> </div>');
            
            this.player = $($('.jp-jplayer', this.lookbackTarget)[0]);
            this.afterViewUpdate = callback;
            this.model.getAudioPath(passageID);
        };
        
        this.update = function() {
            this.player.jPlayer( "destroy" );
            this.player.jPlayer({
                ready: function(event) {
                    $(this).jPlayer("setMedia", {
                        title: AUDIO.currentAudio.passageName.replace(/\\/g, ''),
                        mp3: AUDIO.currentAudio.path + '.mp3',
                        
                    }).jPlayer('play',0);
                },
                solution: "html, flash",
                supplied: "oga, mp3",
                preload:"metadata",
                wmode: "window",
                autoBlur: false,
                error: function(event) {
                    
                    /*to log in db if image not loading*/
                    switch(event.jPlayer.error.type) {
                        case $.jPlayer.error.URL:
                            var msg = 'Error with media URL.';
                            logError(msg); 
                        break;
                        case $.jPlayer.error.NO_SOLUTION:
                            var msg = 'No media playback solution is available.';
                            logError(msg); 
                        break;
                        case $.$.jPlayer.error.FLASH:
                            var msg = 'A problem with the Flash on the page';
                            logError(msg); 
                        break;
                        case $.jPlayer.error.FLASH_DISABLED:
                            var msg = 'The Flash has been disabled by the browser';
                            logError(msg); 
                        break;
                        case $.jPlayer.error.NO_SUPPORT:
                            var msg = 'Not possible to play any media format.';
                            logError(msg); 
                        break;
                         case $.jPlayer.error.URL_NOT_SET:
                            var msg= 'Media playback command not possible as no media is set.'
                            logError(msg); 
                        break;
                    }
                    /*end*/
                },
                cssSelectorAncestor: (this.mode == 'passage') ? '#jp_container_1': '#jp_container_2'
            });
                
            if (this.afterViewUpdate) {
                this.afterViewUpdate(this.model.currentAudio);
            }
            
            if(this.mode == 'passage'){
                this.container.show();
                this.model.startCountingTime();
            }
        };
    };

    this.Controller = function(model, view) {
        this.view = view;
        this.model = model;
    };

    /*
     * creating instances of the classes created above.
     */
    this.audioModel = new this.Model(this);
    this.view = new this.View(this.audioModel, element);
    this.controls = new this.Controller(this.audioModel, this.view);
    
    //pause all instances of jPlayer
    this.stop = function(){
        this.audioModel.stopCountingTime();
        $.jPlayer.pause();
    };
    
    this.getTimeTaken = function(){
        return this.audioModel.currentAudio.timeTaken;
    };
}

function logError(msg)
{
    if(sessionData.currentLocation.location == 'the_classroom')
        var page1   = sessionData.currentLocation.type;
    else
        var page1 = sessionData.currentLocation.location;

    var page   = page1;
    var itemid = sessionData.qID;
    var msg    = 'audio not loading : '+msg;
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
}