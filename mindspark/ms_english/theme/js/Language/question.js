/**
 * @author Anand Mishra
 *
 * Component Question Class. Handles all the question related management - getting question from db,
 * parameterising the responsere and loading iframe
 */
// Audio to be preloaded.
var allAudio         =  '<audio  id="allAudio"><source src="" type="audio/mp3" /><source src="" type="audio/ogg"/></audio>';
var mcqHtmlStructure = '<div id="subContainerQuestion" class="container-fluid qtypeTopBuffer"><div id="explantionArea"></div><div class="qtypeClTitleDiv row"><div class="col-sm-11"><span class=qtypeClTitleTxt id="idTitleTxt"></span></div></div><div class="qtypeClQuesDiv row" ><div id=quesDiv></div></div><div class="qtypeClSndIcon row"><div class="col-sm-11" id=sndIcon></div></div><div class="qtypeClImgMcq row"><div class="col-sm-11" id=imgDiv></div></div><div id="qtypeContainerOpt"></div><div id="submitContainer" class="row" ></div><div class="row" id="forALignExplanation"></div><div id="correctWrongSign"></div><div id ="animSndIcon"></div></div>';
var matchHtmlStructure = '<div id="subContainerQuestion" class="container-fluid qtypeTopBuffer"></div>';
var sequenceHtmlStructure = '<div id="subContainerQuestion" class="container-fluid qtypeTopBuffer"></div>';


var refQuesViewObj;

var quesHtml                = '';
var quesSeqHtml             = '';
var quesMatchHtml           = '';
var quesMakingWordHtml      = '';
var quesMakingWordInputHtml = '';
var quesBlankHtml           = '';

var optLabelArr = ["A", "B", "C", "D", "E", "F", "G", "H"];
var optLabelForCorrectAns = ["a", "b", "c", "d", "e", "f"];
var assetsPath = Helpers.constants.THEME_PATH + "img/Language/templates/";

var counterPath = 0;
//var checkQues = '';
var totalTimeQues = new Array();
var totalCorrectQues = new Array();

var sndCorrect = 'correct2.mp3';
var sndWrong = 'wrong2.mp3';
var playSound = {};

var NO_NET_MATCH_ELEMENTS = [
    "Diamond~Hardest mineral",
    "Pacific~Ocean",
    "Sundial~To keep time",
    "Nitrogen~70% of air",
    "Blue whale~Largest animal",
    "Venus~Fly Trap  Carnivorous plant",
    "Plastic~Decomposes very slowly",
    "Quarantine~ Comes from the number '40'",
    "Saturn's rings~Made of ice",
    "Mercury~Hottest planet",
    "Ironic~ Contrary to expectation",
    "Oxymoron~Bittersweet",
    "Shakespeare~To be or not to be",
    "Wasps ~Neither bees nor ants",
    "Mahjong ~Shapes on tiles",
    "Moby Dick ~Fictional white whale",
    "Gandalf~A fictional wizard",
    "Clandestine~Kept hidden, or done secretly",
    "Kaleidoscope~Made of coloured glass pieces",
    "Houdini~A famous magician"
];
//var mcqHtmlStructure = '<audio  id="allAudio"><source src="" type="audio/mp3" /><source src="" type="audio/ogg"/></audio><div id="subContainerQuestion" class="container-fluid"><div class="qtypeClTitleDiv row"  ><span class=qtypeClTitleTxt id="idTitleTxt"></span></div><div class=qtypeClQuesDiv id=quesDiv></div><div class=qtypeClSndIcon id=sndIcon></div><div class="qtypeClImgMcq" id=imgDiv></div><div id="qtypeContainerOpt"></div><div id="correctWrongSign"></div><div id ="animSndIcon"></div></div>';

function Question(element) {
    
    this.Model = function(context) {
        this.storedQuestions = {};
        this.parent = context;
        var _myself = this; 
        this.getQuestion = function(qcode, mode) {
            //added by nivedita
           //if(counterPath == 0)
               // counterPath += 1;
            //end
            this.questionMode = mode;

            if (this.storedQuestions[qcode] && mode != 'offline') {
                this.currentQuestion = this.storedQuestions[qcode];
                this.currentQuestion.qcode = qcode;
                this.parent.notifyChange();
                return;
            }

            this.currentQuestion = {
                json : {},
                qcode : qcode,
                string : '',
                timeTakenExpln : 0,
                timeTaken : 0
            };

            if (mode == 'offline') {
                data = this.generateLocalData();
                this.handleGetQuestionResponse(data);
            } else {
                $.ajax({
                    context : this,
                    url : Helpers.constants['CONTROLLER_PATH'] + 'diagnosticTest/getQuestionCompleteInfo/' + qcode,
                    dataType : 'JSON'
                }).done(function(data){ 
                    if(Helpers.ajax_response( '' , data , [] ) )
                    {
                         if(typeof data.result_data === "string")
                            parameters = $.parseJSON(data.result_data);
                        else
                            parameters = data.result_data;
                        //console.log(parameters);
                        this.handleGetQuestionResponse(parameters);
                    }
                });
            }
        };
        
        //this is highly dependent on db design and view implementation.
        this.generateLocalData = function() {
            var generateType = 'match';
            
            switch(generateType){
                case 'match':
                    var array = Helpers.getRandom(NO_NET_MATCH_ELEMENTS, 5);
                    return {
                        "qType" : "match",
                        "quesSubType" : "text",
                        "quesText" : "Match the words given on the left, with the words on the right.",
                        "totalOptions" : "5",
                        "optSubType" : "textAndtext",
                        "quesTypeCode" : "matchTemplate",
                        'option_a': array[0],
                        'option_b': array[1],
                        'option_c': array[2],
                        'option_d': array[3],
                        'option_e': array[4],
                    };
                break;
            }
        };

        this.handleGetQuestionResponse = function(data) {
            this.currentQuestion.json = data;
            if(this.questionMode != 'offline')
                this.storedQuestions[this.currentQuestion.qcode] = this.currentQuestion;
            //queNum is not part of question object. this bit of code destroys modularity.
            if(sessionStorage.getItem('questionNumber') == null)
                sessionStorage.setItem('questionNumber',1)
            $("#queNum").html(sessionStorage.getItem('questionNumber'));
            this.parent.questionNumber = sessionStorage.getItem('questionNumber');
            this.parent.notifyChange();
        };
    };

    this.questionNumber = 0;

    this.notifyChange = function() {
        this.view.update();
    };

    this.View = function(model, element) {
        this.model = model;
        this.imagePath = Helpers.constants.LIVE_CONTENT_PATH + 'templates_qtype/images/';
        this.soundPath = Helpers.constants.LIVE_CONTENT_PATH + 'templates_qtype/sounds/';
        this.container = $(element);
        this.userResponse = "";
        this.completed = 0;
        this.extraParam = "";
        this.correct = 0;
        this.score = 0;
        this.submitText = "Submit";
        this.arrCorrectAnswer = [];
        this.tmpQSubType = "";
        this.displayReportCorrectChk = 0;
        this.allOptionsSoundArr = [];
        this.allOptionsArr = [];
        this.descAllOptionsArr = [];
        // Sound preload for correct and wrong.
            this.playSound = Helpers.PLAYSOUND;
        // Ends
        $('#questionSubmitButton').bind('click', function() {
            sessionData.currentLocation.location = "the_classroom";
            if (refQuesViewObj.onSubmit()) {
                $(this).hide();
                refQuesViewObj.incrementQuestionCount();
            }
            //restrictQuestions(refQuesViewObj.correct);
        });

        this.show = function(_parameters) {
            this.onLoad = _parameters.onLoad;
            this.onAttempt = _parameters.onAttempt;
            this.model.getQuestion(_parameters.qcode, _parameters.mode);
        };
        this.startCountingTime = function() {
            clearInterval(refQuesViewObj.timerId);
            this.timerId = setInterval(refQuesViewObj.timeStep, 1000);
        };

        this.stopCountingTime = function() {
            clearInterval(this.timerId);
        };

        this.timeStep = function() {
            if (refQuesViewObj.model.currentQuestion.completed == 1) {
                refQuesViewObj.model.currentQuestion.timeTakenExpln++;
            } else {
                refQuesViewObj.model.currentQuestion.timeTaken++;
            }
        };

        this.update = function() {
            this.container.html('');
            this.container.show();
            this.quesDataArr = this.model.currentQuestion.json;

            refQuesViewObj = this;
            refQuesViewObj.completed = 0;
            refQuesViewObj.userResponse = "";
            refQuesViewObj.extraParam = "";
            refQuesViewObj.correct = 0;
            refQuesViewObj.score = 0;
            refQuesViewObj.tmpQSubType = (refQuesViewObj.quesDataArr.quesSubType).toLowerCase();
            refQuesViewObj.displayReportCorrectChk = 0;
			
			for(var key in refQuesViewObj.quesDataArr)
			{
					if(key == "queParams") continue;
                    refQuesViewObj.quesDataArr[key]=Helpers.removeHighlight(refQuesViewObj.quesDataArr[key]);
			}	
			
			switch (this.quesDataArr.qTemplate) {
            case 'blank':
                Helpers.loadJs('theme/js/Language/templates/blank.js?24082017', function() {
                    showBlankQuestion(refQuesViewObj);
                    refQuesViewObj.afterUpdate();
                });
                break;
            case 'match': {
                Helpers.loadJs("theme/js/Language/templates/match.js?09102017", function() {
                    showMatchQuestion(refQuesViewObj);
                    refQuesViewObj.afterUpdate();
                });
                break;
            }
            case 'sequencing': {
                Helpers.loadJs("theme/js/Language/templates/sequences.js?20022017", function() {
                    showSequence(refQuesViewObj);
                    refQuesViewObj.afterUpdate();
                });
                break;
            }
            case 'makingword': {
                Helpers.loadJs("theme/js/Language/templates/makingword.js?16012017", function() {
                    showMakingWordQuestion(refQuesViewObj);
                    refQuesViewObj.afterUpdate();
                });
                break;
            }
            case 'speaking': {
                Helpers.loadJs("theme/js/Language/templates/speaking.js?13042018", function() {
                    showSpeakingQuestion(refQuesViewObj);
                    refQuesViewObj.afterUpdate();
                });
                break;
            }
            default:
                this.container.html(mcqHtmlStructure);
                this.container.append(allAudio);
                this.titleBin = $("#idTitleTxt", this.container);
                this.quesImgBin = $("#imgDiv", this.container);
                this.quesDivBin = $("#quesDiv", this.container);
                this.quesSndIconBin = $("#sndIcon", this.container);
                this.preLoadImages();
                break;
            }
        };
        // This function is called every time when the question is loaded completly 

        this.afterUpdate = function() {
            this.onLoad && this.onLoad();
            refQuesViewObj.startCountingTime();
            sessionData.currentLocation.location = "the_classroom";
            switch(this.quesDataArr.qTemplate) {
            case 'blank':
            case 'makingword':
            case 'openEnded':
                $('#questionSubmitButton').show();
                break;
            default:
                if (refQuesViewObj.quesDataArr.qType == "multiCorrect") {
                    $('#questionSubmitButton').show();
                }
                break;
            }
            restrictQuestions(); // This is called on every question is load To check if delay need to be give or not
        };                      //  On 4th question if delay condition satisfy then the delay will be given to the user 

        this.preLoadImages = function() {
            var imageArray = [];

            if (refQuesViewObj.quesDataArr.qTemplate.toLowerCase() == 'mcq') {
                refQuesViewObj.allOptionsSoundArr = [refQuesViewObj.quesDataArr.sound_a, refQuesViewObj.quesDataArr.sound_b, refQuesViewObj.quesDataArr.sound_c, refQuesViewObj.quesDataArr.sound_d, refQuesViewObj.quesDataArr.sound_e, refQuesViewObj.quesDataArr.sound_f];
                refQuesViewObj.allOptionsArr = [refQuesViewObj.quesDataArr.option_a, refQuesViewObj.quesDataArr.option_b, refQuesViewObj.quesDataArr.option_c, refQuesViewObj.quesDataArr.option_d, refQuesViewObj.quesDataArr.option_e, refQuesViewObj.quesDataArr.option_f];
                refQuesViewObj.descAllOptionsArr = [refQuesViewObj.quesDataArr.desc_a, refQuesViewObj.quesDataArr.desc_b, refQuesViewObj.quesDataArr.desc_c, refQuesViewObj.quesDataArr.desc_d, refQuesViewObj.quesDataArr.desc_e, refQuesViewObj.quesDataArr.desc_f];

                if (refQuesViewObj.tmpQSubType == "textandimage" || refQuesViewObj.tmpQSubType == "image" || refQuesViewObj.tmpQSubType == "imageandaudioicon") {
                    imageArray.push(refQuesViewObj.imagePath + "" + refQuesViewObj.quesDataArr.quesImage);
                }

                if (refQuesViewObj.quesDataArr.optSubType == "image") {
                    for (var p = 0; p < refQuesViewObj.quesDataArr.totalOptions; p++) {
                        if (refQuesViewObj.allOptionsArr[p] != "") {
                            imageArray.push(refQuesViewObj.imagePath + "" + refQuesViewObj.allOptionsArr[p]);
                        } else {
                            alert("No image file is found in option " + p);
                        }
                    }
                }

                if (imageArray.length > 0) {
                    Helpers.preloadImages(imageArray, this.preloadSounds);
                } else {
                    this.preloadSounds();
                }
            }
        };
        this.preloadSounds = function() {
            var allSndArr = [];
            if (refQuesViewObj.quesDataArr.qTemplate.toLowerCase() == 'mcq') {
                if (refQuesViewObj.quesDataArr.titleSound != "") {
                    allSndArr.push(refQuesViewObj.quesDataArr.titleSound);
                }
                if (refQuesViewObj.quesDataArr.quesSound != "") {
                    allSndArr.push(refQuesViewObj.quesDataArr.quesSound);
                }
                if (refQuesViewObj.quesDataArr.quesAudioIconSound != "") {
                    allSndArr.push(refQuesViewObj.quesDataArr.quesAudioIconSound);
                }
                for (var p = 0; p < refQuesViewObj.quesDataArr.totalOptions; p++) {
                    if (refQuesViewObj.allOptionsSoundArr[p] != "") {
                        allSndArr.push(refQuesViewObj.allOptionsSoundArr[p]);
                    } else {
                        if (refQuesViewObj.quesDataArr.optSubType == "audioicon") {
                            alert("No Sound file is found in option " + p);
                        }
                    }
                }
            }
            allSndArr.push(sndCorrect);
            allSndArr.push(sndWrong);
            if (allSndArr.length > 0) {
                //this.playSound = Helpers.loadAllSounds(allSndArr, refQuesViewObj.startDisplayMcqQues, refQuesViewObj.soundPath);
                refQuesViewObj.startDisplayMcqQues();
            } else {
                refQuesViewObj.startDisplayMcqQues();
            }

        };
        this.startDisplayMcqQues = function() {
            refQuesViewObj.quesSubTypeDispLogic();
            refQuesViewObj.startDispLogicQues();
            if (refQuesViewObj.model.questionMode != "report") {
                refQuesViewObj.enableClick();
            }
            refQuesViewObj.afterUpdate();
        };

        this.quesSubTypeDispLogic = function() {

            refQuesViewObj.titleBin.html("");
            refQuesViewObj.quesImgBin.html("");
            refQuesViewObj.quesDivBin.html("");
            refQuesViewObj.quesSndIconBin.html("");
            var fontSizeTitle = 22;
            $quesImgImg = $("#imgDiv img", this.container);
            // $("#imgDiv", this.container).css('width', '20%');
            refQuesViewObj.titleBin.css('display', 'none');
            refQuesViewObj.quesImgBin.css('display', 'none');
            if (refQuesViewObj.quesDataArr.titleText != "") {
                refQuesViewObj.titleBin.css('font-size', (fontSizeTitle + 'px'));
                refQuesViewObj.titleBin.css('display', '');
                refQuesViewObj.titleBin.html(refQuesViewObj.quesDataArr.titleText);
            }

            if (refQuesViewObj.tmpQSubType == "text" || refQuesViewObj.tmpQSubType == "textandaudioicon" || refQuesViewObj.tmpQSubType == "audioicon") {
                if (refQuesViewObj.tmpQSubType != "audioicon") {
                    refQuesViewObj.quesDivBin.html(refQuesViewObj.quesDataArr.quesText);
                }

                if (refQuesViewObj.tmpQSubType == "textandaudioicon" || refQuesViewObj.tmpQSubType == "audioicon") {

                    refQuesViewObj.quesSndIconBin.html('<img src="' + assetsPath + 'soundIconGrey.png"/>');
                }
            } else if (refQuesViewObj.tmpQSubType == "textandimage") {
                refQuesViewObj.quesDivBin.html(refQuesViewObj.quesDataArr.quesText);
                refQuesViewObj.quesImgBin.css('display', '');
                refQuesViewObj.quesImgBin.html('<img class="img-responsive" id=imgId src="' + refQuesViewObj.imagePath + refQuesViewObj.quesDataArr.quesImage + '"/>');

            } else if (refQuesViewObj.tmpQSubType == "image" || refQuesViewObj.tmpQSubType == "imageandaudioicon") {
                refQuesViewObj.quesImgBin.css('display', '');
                refQuesViewObj.quesImgBin.html('<img class="img-responsive" id=imgId src="' + refQuesViewObj.imagePath + refQuesViewObj.quesDataArr.quesImage + '"/>');

                if (refQuesViewObj.tmpQSubType == "imageandaudioicon") {
                    refQuesViewObj.quesSndIconBin.html('<img src="' + assetsPath + 'soundIconGrey.png"/>');
                }
                $quesImgImg.height("140px");
                $quesImgImg.width("140px");
            }
		};

        this.startDispLogicQues = function() {
            var imageWidth = 0;
            var totalOptions = refQuesViewObj.quesDataArr.totalOptions;
            if (refQuesViewObj.tmpQSubType == "image" && refQuesViewObj.tmpQSubType == "textandimage") {
                imageWidth = 140;
            } else if (refQuesViewObj.quesDataArr.optSubType == "image" && refQuesViewObj.tmpQSubType == "imageandaudioicon") {
                imageWidth = 120;
            }
            if (refQuesViewObj.quesDataArr.qType == "multiCorrect") {
				refQuesViewObj.quesDataArr.correctAnswer = refQuesViewObj.nAth(refQuesViewObj.quesDataArr.correctAnswer);
				refQuesViewObj.arrCorrectAnswer = refQuesViewObj.quesDataArr.correctAnswer.split("~");
            }

            $("#qtypeContainerOpt", this.container).html("");
            if (refQuesViewObj.quesDataArr.optSubType == "text") {
                for (var p = 0; p < totalOptions; p++) {
                    if (refQuesViewObj.quesDataArr.qType == "multiCorrect") {
                        var htm = '';
                        htm += '<div class="row qtypeTopBuffer">';
                            htm += '<div class="qtypeClLabelCheckBoxOpt options col-md-2 col-lg-1">';
                                htm += '<div class="row">';
                                    htm += '<div class="col-md-6">';
                                        var str = refQuesViewObj.allOptionsArr[p].replace(/"/g, '&quot;');
                                        htm += '<input type="checkbox" name="opt_CB" id=cb_Id' + p + ' class="optCbox" value="' + str + '">';
                                        htm += '<label for="cb_Id' + p + '" class="check_option"><i for class="fa fa-square-o"></i></label>';
                                    htm += '</div>';
                                    htm += '<div class="col-md-6">';
                                        htm += '<div class="options" id=labId' + p + '>' + optLabelArr[p];
                                        htm += '</div>';
                                    htm += '</div>';
                                htm += '</div>';
                            htm += '</div>';
                            htm += '<div class="col-md-9 col-lg-10 qtypeClOptDiv qtypeNoLpadding qtypeClOptSndIcon options" id=optId' + p + '>' + refQuesViewObj.allOptionsArr[p] + '</div>';
                        htm += '</div>';
                        $("#qtypeContainerOpt", this.container).append(htm);
                    } else {
                        var htm = '';
                        htm += '<div class="row qtypeTopBuffer">';
                            htm += '<div class="row qtypeTopBuffer">';
                                htm += '<div class="col-md-1 col-lg-1 col-sm-1">';
                                    htm += '<div class="qtypeClLabelOpt qtypeClickObjCl options" id=labId' + p + '>' + optLabelArr[p];
                                htm += '</div>';
                            htm += '</div>';
                            htm += '<div class="col-md-10 col-lg-10 col-sm-10 qtypeClOptDiv qtypeNoLpadding qtypeClOptSndIcon options" id=optId' + p + '>' + refQuesViewObj.allOptionsArr[p] + '</div>';
                        htm += '</div>';
                        $("#qtypeContainerOpt", this.container).append(htm);
                        //$("#qtypeContainerOpt", this.container).append('<div class="row qtypeTopBuffer"><div class="col-md-12"><div class="qtypeClLabelOpt qtypeClickObjCl options" id=labId' + p + '>' + optLabelArr[p] + '</div> <div class="qtypeClOptDiv qtypeNoLpadding qtypeClOptSndIcon options" id=optId' + p + '>' + refQuesViewObj.allOptionsArr[p] + '</div></div></div>');
                    }
                }
            } else if (refQuesViewObj.quesDataArr.optSubType == "image") {
                var tmpColMiddle = "";
                var tmpRowS = '<div class="row qtypeTopBuffer">';
                var tmpRowE = '</div>';
                for (var p = 0; p < totalOptions; p++) {
                    if (refQuesViewObj.quesDataArr.qType == "multiCorrect") {
                        tmpColMiddle += '<div class="col-md-4 col-lg-4">';
                            tmpColMiddle += '<div class="row">';
                                // Create the option and the checkbox.
                                tmpColMiddle += '<div class="col-md-4">';
                                    tmpColMiddle += '<div class="row qtypeClLabelCheckBoxOpt">';
                                        tmpColMiddle += '<div class="col-md-6">';
                                            tmpColMiddle += '<input type="checkbox" name="opt_CB" id=cb_Id' + p + ' class="optCbox" value="' + refQuesViewObj.allOptionsArr[p] + '">';
                                            tmpColMiddle += '<label for="cb_Id' + p + '" class="check_option"><i for class="fa fa-square-o"></i></label>';
                                        tmpColMiddle += '</div>';
                                        tmpColMiddle += '<div class="col-md-6" id=labId' + p + '>' + optLabelArr[p];
                                        tmpColMiddle += '</div>';
                                    tmpColMiddle += '</div>';
                                tmpColMiddle += '</div>';

                                // Show the option content
                                tmpColMiddle += '<div class="col-md-8">';
                                    tmpColMiddle += '<div class="qtypeClOptDiv qtypeClOptSndIcon" id=optId' + p + '>';
                                        tmpColMiddle += '<img class="img-responsive" src="' + refQuesViewObj.imagePath + refQuesViewObj.allOptionsArr[p] + '"/>';
                                    tmpColMiddle += '</div>';
                                tmpColMiddle += '</div>';
                            tmpColMiddle +='</div>';
                        tmpColMiddle += '</div>';
                    } else {
                        tmpColMiddle += '<div class="col-md-4 col-lg-4">';
                            tmpColMiddle += '<div class="row">';
                                // Create the option and the checkbox.
                                tmpColMiddle += '<div class="col-md-1">';
                                    tmpColMiddle += '<div class="row">';
                                        tmpColMiddle += '<div class="qtypeClLabelOpt qtypeClickObjCl" id=labId' + p + '>' + optLabelArr[p];
                                        tmpColMiddle += '</div>';
                                    tmpColMiddle += '</div>';
                                tmpColMiddle += '</div>';

                                // Show the option content
                                tmpColMiddle += '<div class="col-md-10">';
                                    tmpColMiddle += '<div class="qtypeClOptDiv qtypeNoLpadding qtypeClOptSndIcon" id=optId' + p + '>';
                                        tmpColMiddle += '<img class="img-responsive" src="' + refQuesViewObj.imagePath + refQuesViewObj.allOptionsArr[p] + '"/>';
                                    tmpColMiddle += '</div>';
                                tmpColMiddle += '</div>';
                            tmpColMiddle +='</div>';
                        tmpColMiddle += '</div>';
                        
                        /*tmpColMiddle += '<div class="col-md-1 col-lg-1">';
                            tmpColMiddle += '<div class="qtypeClLabelOpt qtypeClickObjCl" id=labId' + p + '>' + optLabelArr[p];
                            tmpColMiddle += '</div>';
                        tmpColMiddle += '</div>';*/

                    }
                    if (totalOptions == 2||totalOptions == 4) {                     
                        if (p == 1 || p == 3) {
                            $("#qtypeContainerOpt", this.container).append(tmpRowS + tmpColMiddle + tmpRowE);
                            tmpColMiddle = "";
                        }
                    }else if (totalOptions == 6){
                         if (p == 2 ||p == 5) 
                         {
                            $("#qtypeContainerOpt", this.container).append(tmpRowS + tmpColMiddle + tmpRowE);
                            tmpColMiddle = "";
                         }
                    } else {                        
                        if (p == 2 || p == 4) {
                            $("#qtypeContainerOpt", this.container).append(tmpRowS + tmpColMiddle + tmpRowE);
                            tmpColMiddle = "";
                        }
                    }
                }
            } else if (refQuesViewObj.quesDataArr.optSubType == "audioicon") {
                var tmpColMiddle = "";
                var tmpRowS = '<div class="row qtypeTopBuffer">';
                var tmpRowE = '</div>';
                var iconImage = assetsPath + 'sndIconOpt.png';
                for (var p = 0; p < totalOptions; p++) {
                    if (refQuesViewObj.quesDataArr.qType == "multiCorrect") {
                        tmpColMiddle += '<div class="col-md-4 col-lg-4">';
                            tmpColMiddle += '<div class="row">';
                                // Create the option and the checkbox.
                                tmpColMiddle += '<div class="col-md-4">';
                                    tmpColMiddle += '<div class="row qtypeClLabelCheckBoxOpt audio_option">';
                                        tmpColMiddle += '<div class="col-md-6">';
                                            tmpColMiddle += '<input type="checkbox" name="opt_CB" id=cb_Id' + p + ' class="optCbox" value="' + refQuesViewObj.allOptionsArr[p] + '">';
                                            tmpColMiddle += '<label for="cb_Id' + p + '" class="check_option"><i for class="fa fa-square-o"></i></label>';
                                        tmpColMiddle += '</div>';
                                        tmpColMiddle += '<div class="col-md-6" id=labId' + p + '>' + optLabelArr[p];
                                        tmpColMiddle += '</div>';
                                    tmpColMiddle += '</div>';
                                tmpColMiddle += '</div>';

                                // show  content
                                tmpColMiddle += '<div class="col-md-8">';
                                    tmpColMiddle += '<div class="qtypeClOptDiv qtypeClOptSndIcon" id=optId' + p + '>';
                                        tmpColMiddle += '<i id=optId' + p + ' class="fa fa-volume-up fa-3x qtypeClOptSndIcon"></i>';
                                    tmpColMiddle += '</div>';
                                tmpColMiddle += '</div>';
                            tmpColMiddle += '</div>';
                        tmpColMiddle += '</div>';
                    } else {
                        tmpColMiddle += '<div class="col-md-4 col-lg-4">';
                            tmpColMiddle += '<div class="row">';
                                // Create the option and the checkbox.
                                tmpColMiddle += '<div class="col-md-1">';
                                    tmpColMiddle += '<div class="row audio_option">';
                                        tmpColMiddle += '<div class="qtypeClLabelOpt qtypeClickObjCl" id=labId' + p + '>' + optLabelArr[p];
                                        tmpColMiddle += '</div>';
                                    tmpColMiddle += '</div>';
                                tmpColMiddle += '</div>';

                                // show  content
                                tmpColMiddle += '<div class="col-md-10">';
                                    tmpColMiddle += '<div class="qtypeClOptDiv qtypeClOptSndIcon" id=optId' + p + '>';
                                        tmpColMiddle += '<i id=optId' + p + ' class="fa fa-volume-up fa-3x qtypeClOptSndIcon"></i>';
                                    tmpColMiddle += '</div>';
                                tmpColMiddle += '</div>';
                            tmpColMiddle += '</div>';
                        tmpColMiddle += '</div>';
                        //tmpColMiddle += '<div class="col-sm-1"><div class="qtypeClLabelOpt qtypeClickObjCl" id=labId' + p + '>' + optLabelArr[p] + '</div></div><div class="qtypeClOptDiv qtypeNoLpadding col-sm-2 qtypeClOptSndIcon" id=optId' + p + '><img class="img-responsive" src="' + iconImage + '"/></div>');

                    }
                    if (totalOptions == 2||totalOptions == 4) {                     
                        if (p == 1 || p == 3) {
                            $("#qtypeContainerOpt", this.container).append(tmpRowS + tmpColMiddle + tmpRowE);
                            tmpColMiddle = "";
                        }
                    }else if (totalOptions == 6){
                         if (p == 2 ||p == 5) 
                         {
                            $("#qtypeContainerOpt", this.container).append(tmpRowS + tmpColMiddle + tmpRowE);
                            tmpColMiddle = "";
                         }
                    } else {                        
                        if (p == 2 || p == 4) {
                            $("#qtypeContainerOpt", this.container).append(tmpRowS + tmpColMiddle + tmpRowE);
                            tmpColMiddle = "";
                        }
                    }
                }
            }
            for (var p = 0; p < totalOptions; p++) {
                if (refQuesViewObj.model.questionMode == "report") {
                    if (refQuesViewObj.model.currentQuestion.userResponse != "") {
                        if (refQuesViewObj.model.currentQuestion.userResponse == optLabelForCorrectAns[p]) {
                            if (refQuesViewObj.model.currentQuestion.userResponse == refQuesViewObj.nAth(refQuesViewObj.quesDataArr.correctAnswer)) {
                                $(("#labId" + p), this.container).append('<div class="signCls" id="signC1"><img src="' + assetsPath + 'greenCmark.png"/></div>');
                                $("#signC1 img").height("35px");
                                $("#signC1 img").width("35px");
                                $("#labId" + p).addClass("bgcolorGreen");
                                $("#signC1").css({
                                    'position' : 'absolute',
                                    'left' : '-10px',
                                    'top' : '0px'
                                });

                            } else {
                                $(("#labId" + p), this.container).append('<div class="signCls" id="signC2"><img src="' + assetsPath + 'wrongCmark.png"/></div>');

                                $("#signC2 img").height("35px");
                                $("#signC2 img").width("35px");
                                $("#labId" + p).addClass("bgcolorRed");
                                $("#signC2").css({
                                    'position' : 'absolute',
                                    'left' : '-10px',
                                    'top' : '0px'
                                });
                                refQuesViewObj.displayReportCorrectChk = 1;

                            }
                        }
                    } else {
                        if (refQuesViewObj.nAth(refQuesViewObj.quesDataArr.correctAnswer) == optLabelForCorrectAns[p]) {
                            $(("#labId" + p), this.container).append('<div class="signCls" id="signC3"><img src="' + assetsPath + 'blueCmark.png"/></div>');
                            $("#signC3 img").height("35px");
                            $("#signC3 img").width("35px");
                            $("#labId" + p).addClass("bgcolorBlue");
                            $("#signC3").css({
                                'position' : 'absolute',
                                'left' : '-10px',
                                'top' : '0px'
                            });

                        }
                    }

                } else {
                    /*if (refQuesViewObj.model.currentQuestion.userResponse == optLabelForCorrectAns[p]) {
                        $(("#labId" + p), this.container).append('<div class="signCls" id="signC4"><img src="' + assetsPath + 'blueCmark.png"/></div>');

                        $("#signC4 img").height("35px");
                        $("#signC4 img").width("35px");
                        $("#labId" + p).addClass("bgcolorBlue");
                        $("#signC4").css({
                            'position' : 'absolute',
                            'left' : '-10px',
                            'top' : '0px'
                        });
                    }*/
                }
            }

            if (refQuesViewObj.model.questionMode == "report" && refQuesViewObj.displayReportCorrectChk == 1) {
                for (var j = 0; j < totalOptions; j++) {
                    if (refQuesViewObj.nAth(refQuesViewObj.quesDataArr.correctAnswer) == optLabelForCorrectAns[j]) {
                        $(("#labId" + j), this.container).append('<div class="signCls" id="signC2"><img src="' + assetsPath + 'wrongCmark.png"/></div>');

                        $("#signC2 img").height("35px");
                        $("#signC2 img").width("35px");
                        $("#labId" + j).addClass("bgcolorRed");
                        $("#signC2").css({
                            'position' : 'absolute',
                            'left' : '-10px',
                            'top' : '0px'
                        });
                    }
                }
            }

            $(".check_option").click(function(){
                var child = $(this).children();
                if($(child).hasClass('fa-check-square-o'))
                {
                    $(child).removeClass('fa-check-square-o');
                    $(child).addClass('fa-square-o');
                }
                else
                {
                    $(child).addClass('fa-check-square-o');
                    $(child).removeClass('fa-square-o');
                }
            });
            $("#correctWrongSign", this.container).html("");
            $("#explantionArea", this.container).html("");
        };

        this.enableClick = function() {
            var arrQcode = ["0", "3", "4", "668", "5", "6", "7", "8", "668"];

            $(".qtypeClickObjCl", this.container).css('cursor', 'pointer');

            if (refQuesViewObj.tmpQSubType == "textandaudioicon" || refQuesViewObj.tmpQSubType == "audioicon" || refQuesViewObj.tmpQSubType == "imageandaudioicon") {
                $sndIconBin = $("#sndIcon", this.container);
                $sndIconBin.css('cursor', 'pointer');
                $sndIconBin.mousedown(function() {
                    $sndIconBin.css('opacity', '0.5');
                });

                $sndIconBin.mouseup(function() {
                    $sndIconBin.css('opacity', '1');
                    Helpers.sndPlayLoadErrEnd(refQuesViewObj.soundPath, "allAudio", refQuesViewObj.quesDataArr.quesAudioIconSound);
                });
            }

            if (refQuesViewObj.quesDataArr.optSubType == "audioicon") {
                $OptSndIconBin = $(".qtypeClOptSndIcon", this.container);
                $OptSndIconBin.css('cursor', 'pointer');
                $OptSndIconBin.mousedown(function() {
                    $OptSndIconBin.css('opacity', '0.5');
                });

                $OptSndIconBin.mouseup(function() {
                    var tmpIdSnd = $(this).attr("id");
                    tmpIdSnd = tmpIdSnd.substr(5, 2);
                    $OptSndIconBin.css('opacity', '1');

                    Helpers.sndPlayLoadErrEnd(refQuesViewObj.soundPath, "allAudio", refQuesViewObj.allOptionsSoundArr[tmpIdSnd]);

                });

            } else if (refQuesViewObj.quesDataArr.optSubType == "text") {
                $OptSndIconBin = $(".qtypeClickObjCl", this.container);
                $OptSndIconBin.css('cursor', 'pointer');
                $OptSndIconBin.mouseenter(function() {
                    var tmpIdSnd = $(this).attr("id");
                    tmpIdSnd = tmpIdSnd.substr(5, 2);

                    if (refQuesViewObj.allOptionsSoundArr[tmpIdSnd] == null) {

                        refQuesViewObj.allOptionsSoundArr[tmpIdSnd] = "";
                    }
                    if (refQuesViewObj.allOptionsSoundArr[tmpIdSnd] != "") {
                        Helpers.sndPlayLoadErrEnd(refQuesViewObj.soundPath, "allAudio", refQuesViewObj.allOptionsSoundArr[tmpIdSnd]);
                    }

                });
            }

            $(".qtypeClickObjCl", this.container).mouseup(function() {
                var tmpId = $(this).attr("id");
                tmpId = tmpId.substr(5, 2);
                refQuesViewObj.userResponse = optLabelForCorrectAns[tmpId];
                refQuesViewObj.singleCorrectOptClick(tmpId);
                var object = {
                    qcode : arrQcode[Object.keys(questionObject.model.storedQuestions).length],
                    mode : 'r'
                };
            });

            if (refQuesViewObj.quesDataArr.qType == "multiCorrect") {
                refQuesViewObj.onSubmit = refQuesViewObj.multiCorrectSubmitClick;
            }
       
        };

        this.incrementQuestionCount = function() {
            //counterPath += 1;
            
            var questionNumber = parseInt(sessionStorage.getItem("questionNumber"));
            questionNumber++;
            sessionStorage.setItem("questionNumber",questionNumber);
        };

        this.singleCorrectOptClick = function(tmpId) {
            var delayText = '';
            //counterPath += 1;
            

            if (refQuesViewObj.model.questionMode == "diagnostic") {

                $("#labId" + tmpId).addClass('bgcolorBlue');
                if (userResponse == refQuesViewObj.nAth(refQuesViewObj.quesDataArr.correctAnswer)) {
                    refQuesViewObj.correct = 1;
                } else {
                    refQuesViewObj.correct = 0;
                }
                refQuesViewObj.completed = 1;
            } else {
                
                var checkQues = false;
                this.incrementQuestionCount();
                refQuesViewObj.disableClick();
                refQuesViewObj.quesDataArr.explanation = refQuesViewObj.quesDataArr.explanation.trim();
                refQuesViewObj.descAllOptionsArr[tmpId] = refQuesViewObj.descAllOptionsArr[tmpId].trim();
                /*restrict passage question here*/
                if (refQuesViewObj.userResponse == refQuesViewObj.nAth(refQuesViewObj.quesDataArr.correctAnswer))
                    var correctans = 1;
                else 
                    var correctans = 0;
                sessionData.currentLocation.location="the_classroom";
                restrictQuestions(correctans);
                if(sessionData.delayNextBtn !== undefined && sessionData.delayNextBtn != '' && sessionData.delayNextBtn == true)
                {
                    delayText = 'You seem to be answering questions hurriedly. Please read questions carefully before answering them.' // You will be able to go to the next question (and to other pages) after 10 seconds.;
                    delayText = delayText.bold();
                }
                else
                    delayText = '';
                /*END*/
                //alert(refQuesViewObj.userResponse);
                if (refQuesViewObj.userResponse == refQuesViewObj.nAth(refQuesViewObj.quesDataArr.correctAnswer)) {
                    refQuesViewObj.correct = 1;
                    //Helpers.sndPlayLoadErrEnd(refQuesViewObj.soundPath, "allAudio", sndCorrect);
                    //this.playSound[0].play();
                    $(".correct_audio")[0].play();
                    if (refQuesViewObj.descAllOptionsArr[tmpId] != "") {
                        if (refQuesViewObj.quesDataArr.explanation != "") {
                            Helpers.prompt({
                                title : 'Explanation',
                                text : refQuesViewObj.descAllOptionsArr[tmpId] + '<br><br>' + refQuesViewObj.quesDataArr.explanation+'</br><span style="color:red;">'+delayText+'</span>',
                                class : 'diaLog-explanation',
                                modal : true,
                                width : '600px',
                                my : 'left top',
                                at : 'left+200 top+5',
                                ofObject : '.characterArrow',
                                callback: function(){
                                    $('.diaLog-explanation').prev('div').css( {'opacity':0.0} );
                                }
                            });
                        } else {
                            Helpers.prompt({
                                title : 'Explanation',
                                text : refQuesViewObj.descAllOptionsArr[tmpId]+'</br><span style="color:red;">'+delayText+'</span>',
                                class : 'diaLog-explanation',
                                modal : true,
                                width : '600px',
                                my : 'left top',
                                at : 'left+200 top+5',
                                ofObject : '.characterArrow',
                                callback: function(){
                                    $('.diaLog-explanation').prev('div').css( {'opacity':0.0} );
                                }
                            });
                        }

                    } else {
                        if (refQuesViewObj.quesDataArr.explanation != "") {
                            Helpers.prompt({
                                title : 'Explanation',
                                text : refQuesViewObj.quesDataArr.explanation+'</br><span style="color:red;">'+delayText+'</span>',
                                class : 'diaLog-explanation',
                                modal : true,
                                width : '600px',
                                my : 'left top',
                                at : 'left+200 top+5',
                                ofObject : '.characterArrow',
                                callback: function(){
                                    $('.diaLog-explanation').prev('div').css( {'opacity':0.0} );
                                }
                            });
                        }
                        else {
                           
                           if(delayText != '' && sessionData.delayNextBtn == true) 
                           {
                                Helpers.prompt({
                                    title : 'Explanation',
                                    text : '<span style="color:red;">'+delayText+'</span>',
                                    class : 'diaLog-explanation',
                                    modal : true,
                                    width : '600px',
                                    my : 'left top',
                                    at : 'left+200 top+5',
                                    ofObject : '.characterArrow',
                                    callback: function(){
                                        $('.diaLog-explanation').prev('div').css( {'opacity':0.0} );
                                    }
                                });
                           }
                        }
                    }

                    $(("#labId" + tmpId), this.container).append('<div class="signCls" id="signC1"><img src="' + assetsPath + 'greenCmark.png"/></div>');

                    $("#signC1 img").height("35px");
                    $("#signC1 img").width("35px");
                    $("#labId" + tmpId).addClass("bgcolorGreen");
                    $("#signC1").css({
                        'position' : 'absolute',
                        'left' : '-10px',
                        'top' : '0px'
                    });
                    refQuesViewObj.completed = 1;
                    refQuesViewObj.submitDataOfQues();
                } else {
                    refQuesViewObj.correct = 0;
                    //Helpers.sndPlayLoadErrEnd(refQuesViewObj.soundPath, "allAudio", sndWrong);
                    //this.playSound[1].play();
                    $(".wrong_audio")[0].play();

                    if (refQuesViewObj.descAllOptionsArr[tmpId] != "") {
                        Helpers.prompt({
                            title : 'Explanation',
                            text : refQuesViewObj.descAllOptionsArr[tmpId] + '<br><br>' + refQuesViewObj.quesDataArr.explanation+'</br><span style="color:red;">'+delayText+'</span>',
                            class : 'diaLog-explanation',
                            modal : true,
                            width : '600px',
                            my : 'left top',
                            at : 'left+200 top+5',
                            ofObject : '.characterArrow',
                            callback: function(){
                                $('.diaLog-explanation').prev('div').css( {'opacity':0.0} );
                            }
                        });

                    } else {
                        if (refQuesViewObj.quesDataArr.explanation != "") {
                            Helpers.prompt({
                                title : 'Explanation',
                                text : refQuesViewObj.quesDataArr.explanation+'</br><span style="color:red;">'+delayText+'</span>',
                                class : 'diaLog-explanation',
                                modal : true,
                                width : '600px',
                                my : 'left top',
                                at : 'left+200 top+5',
                                ofObject : '.characterArrow',
                                callback: function(){
                                    $('.diaLog-explanation').prev('div').css( {'opacity':0.0} );
                                }
                            });
                        }
                        else {
                            if(delayText != '' && sessionData.delayNextBtn == true)
                            {
                                Helpers.prompt({
                                    title : 'Explanation',
                                    text : '<span style="color:red;">'+delayText+'</span>',
                                    class : 'diaLog-explanation',
                                    modal : true,
                                    width : '600px',
                                    my : 'left top',
                                    at : 'left+200 top+5',
                                    ofObject : '.characterArrow',
                                    callback: function(){
                                        $('.diaLog-explanation').prev('div').css( {'opacity':0.0} );
                                    }
                                });
                            }
                        }
                    }
                    $(("#labId" + tmpId), this.container).append('<div class="signCls" id="signC1"><img src="' + assetsPath + 'wrongCmark.png"/></div>');
                    $("#signC1 img").height("35px");
                    $("#signC1 img").width("35px");
                    $("#signC1").css({
                        'position' : 'absolute',
                        'left' : '-10px',
                        'top' : '0px'
                    });
                    $("#labId" + tmpId).addClass("bgcolorRed");
                    refQuesViewObj.showAns();
                }
            }
        };

        this.multiCorrectSubmitClick = function(tmpId) {
            if ($('.optCbox:checked', this.container).length == 0) {
                Helpers.prompt("Please select your Answer!");
                return false;
            } else {
                $(".optCbox", this.container).unbind('click');
                $(".check_option").unbind('click');
                $('input[type="checkbox"]', this.container).click(function(e) {
                    e.preventDefault();
                });
                //var markPerQues = (1 / refQuesViewObj.arrCorrectAnswer.length);
                var markPerQues = 0;
                var i = 0;
                var j = 0;
                refQuesViewObj.userResponse = "";
                if (refQuesViewObj.model.questionMode == "diagnostic") {
                    //multiCorrectDisplayAnswerNo();
                } else {
                    
                    for ( i = 0; i < refQuesViewObj.quesDataArr.totalOptions; i++) {
                        var meetAnswer = false;
                        if ($("#cb_Id" + i).prop('checked') == true) {
                            refQuesViewObj.userResponse += optLabelForCorrectAns[i] + "~";
                        }
                        ;
                        for ( j = 0; j < refQuesViewObj.arrCorrectAnswer.length; j++) {
                            if (optLabelForCorrectAns[i] == refQuesViewObj.arrCorrectAnswer[j]) {
                                meetAnswer = true;
                                if ($("#cb_Id" + i).prop('checked') == true) {
                                    $(("#labId" + i), this.container).append('<div class="signCls" id="signC' + i + '"><img src="' + assetsPath + 'greenCmark.png"/></div>');
                                    $(".signCls img").height("35px");
                                    $(".signCls img").width("35px");
                                    //$("#labId" + i).addClass("bgcolorGreen");
                                    $($(".qtypeClLabelCheckBoxOpt")[i]).addClass("bgcolorGreen");
                                    $("#signC" + i).css({
                                        'position' : 'absolute',
                                        'left' : '-10px',
                                        'top' : '0px'
                                    });
                                    // Change is done to count the number of correct option selected.
                                    markPerQues++;
                                } else {
                                    //refQuesViewObj.correct=0;
                                    //markPerQues=0;
                                    $(("#labId" + i), this.container).append('<div class="signCls" id="signC' + i + '"><img src="' + assetsPath + 'blueCmark.png"/></div>');
                                    $(".signCls img").height("35px");
                                    $(".signCls img").width("35px");
                                    //$("#labId" + i).addClass("bgcolorBlue");
                                    $($(".qtypeClLabelCheckBoxOpt")[i]).addClass("bgcolorBlue");
                                    $("#signC" + i).css({
                                        'position' : 'absolute',
                                        'left' : '-10px',
                                        'top' : '0px'
                                    });
                                }
                            }
                        }
                        // Either mark 1 or 0 for the correct answer change is done.
                        if(markPerQues == refQuesViewObj.arrCorrectAnswer.length)
                            refQuesViewObj.correct = 1;
                        else
                            refQuesViewObj.correct = 0;

                        
                        if (!meetAnswer) {
                            if ($("#cb_Id" + i).prop('checked') == true) {
                                refQuesViewObj.correct = 0;
                                markPerQues = 0;
                                $(("#labId" + i), this.container).append('<div class="signCls" id="signC' + i + '"><img src="' + assetsPath + 'wrongCmark.png"/></div>');
                                $(".signCls img").height("35px");
                                $(".signCls img").width("35px");
                                //$("#labId" + i).addClass("bgcolorRed");
                                $($(".qtypeClLabelCheckBoxOpt")[i]).addClass("bgcolorRed");
                                $("#signC" + i).css({
                                    'position' : 'absolute',
                                    'left' : '-10px',
                                    'top' : '0px'
                                });
                            }
                            //Helpers.sndPlayLoadErrEnd(refQuesViewObj.soundPath, "allAudio", sndWrong);
                            //alert("playSound");
                            //this.playSound[1].play();
                            
                        } else {
                            //Helpers.sndPlayLoadErrEnd(refQuesViewObj.soundPath, "allAudio", sndCorrect);
                            //this.playSound[0].play();
                           
                        }
                    }
                    if(refQuesViewObj.correct == 1)
                    {
                        $(".correct_audio")[0].play();
                    }
                    else
                    {
                        $(".wrong_audio")[0].play();
                    }
                    /*restrict passage question here*/
                    //alert(refQuesViewObj.correct);
                    sessionData.currentLocation.location="the_classroom";
                    restrictQuestions(refQuesViewObj.correct);
                    if(sessionData.delayNextBtn !== undefined && sessionData.delayNextBtn != '' && sessionData.delayNextBtn == true)
                    {
                        delayText = 'You seem to be answering questions hurriedly. Please read questions carefully before answering them.'// You will be able to go to the next question (and to other pages) after 10 seconds. old message;
                        delayText = delayText.bold();
                    }
                    else
                        delayText = '';
                    /*END*/
                    refQuesViewObj.quesDataArr.explanation = refQuesViewObj.quesDataArr.explanation.trim();
                    if (refQuesViewObj.quesDataArr.explanation != "") {
                        Helpers.prompt({
                            title : 'Explanation',
                            text : refQuesViewObj.quesDataArr.explanation+'</br><span style="color:red;">'+delayText+'</span>',
                            class : 'diaLog-explanation',
                            modal : true,
                            width : '600px',
                            my : 'left top',
                            at : 'left+200 top+5',
                            ofObject : '.characterArrow',
                            callback: function(){
                                $('.diaLog-explanation').prev('div').css( {'opacity':0.0} );
                            }
                        });
                    }
                    else {
                            if(delayText != '' && sessionData.delayNextBtn == true)
                            {
                                 Helpers.prompt({
                                    title : 'Explanation',
                                    text : '<span style="color:red;">'+delayText+'</span>',
                                    class : 'diaLog-explanation',
                                    modal : true,
                                    width : '600px',
                                    my : 'left top',
                                    at : 'left+200 top+5',
                                    ofObject : '.characterArrow',
                                    callback: function(){
                                        $('.diaLog-explanation').prev('div').css( {'opacity':0.0} );
                                    }
                                });
                            }
                    }
                    refQuesViewObj.completed = 1;
                    refQuesViewObj.submitDataOfQues();
                }
            }
            return true;
        };

        this.showAns = function() {
            for (var p = 0; p < refQuesViewObj.quesDataArr.totalOptions; p++) {
                if (optLabelForCorrectAns[p] == refQuesViewObj.nAth(refQuesViewObj.quesDataArr.correctAnswer)) {
                    $(("#labId" + p), this.container).append('<div class="signCls" id="signC2"><img src="' + assetsPath + 'blueCmark.png"/></div>');
                    $("#signC2 img").height("35px");
                    $("#signC2 img").width("35px");
                    $("#signC2").css({
                        'position' : 'absolute',
                        'left' : '-10px',
                        'top' : '0px'
                    });
                    $("#labId" + p).addClass("bgcolorBlue");
                    refQuesViewObj.completed = 1;
                    refQuesViewObj.submitDataOfQues();

                }
            }
        };
		
		this.disableClick = function() {
            $(".qtypeClickObjCl", this.container).css('cursor', 'default');
            $(".qtypeClickObjCl", this.container).unbind('mouseup');
        };

        this.submitDataOfQues = function() {
            refQuesViewObj.model.currentQuestion.correct = refQuesViewObj.correct;
            refQuesViewObj.model.currentQuestion.userResponse = refQuesViewObj.userResponse;
            refQuesViewObj.model.currentQuestion.extraParam = refQuesViewObj.extraParam;
            refQuesViewObj.model.currentQuestion.score = refQuesViewObj.score;
            refQuesViewObj.model.currentQuestion.completed = refQuesViewObj.completed;
            refQuesViewObj.onAttempt();
        };
		
		this.nAth = function(str) {
			if (str=="") return "";
			var strtodecrypt = str.split("-");
			var msglength = strtodecrypt.length;
			decrypted_message = "";
			for (var position = 0; position < msglength; position++) {
				ascii_num_byte_to_decrypt = strtodecrypt[position];
				ascii_num_byte_to_decrypt = ascii_num_byte_to_decrypt / 2;
				ascii_num_byte_to_decrypt = ascii_num_byte_to_decrypt - 5;
				decrypted_byte = String.fromCharCode(ascii_num_byte_to_decrypt);
				decrypted_message += decrypted_byte;
			}
			return decrypted_message;
		}
    };

    this.model = new this.Model(this);
    this.view = new this.View(this.model, element);

    this.getQuestion = function() {

        sessionData.quesComHtml     = '';
        var quesHtml                = $("#subContainerQuestion").html();
        var quesSeqHtml             = $("#sequenceContainer").html();
        var quesMatchHtml           = $("#matchContainer").html();
        var quesMakingWordHtml      = $("#makingWordContainer").html();
        var quesMakingWordInputHtml = $("#qtypeContainerOptMaking").html();

        var quesBlankHtml = $(".blankContainer").html();
        var quesSpeakingHtml=$("#mainSpeakingContainer").html();
        
        sessionData.quesComHtml             = quesHtml;
        sessionData.quesSeqHtml             = quesSeqHtml;
        sessionData.quesMatchHtml           = quesMatchHtml;
        sessionData.quesMakingWordHtml      = quesMakingWordHtml;
        sessionData.quesMakingWordInputHtml = quesMakingWordInputHtml;
        sessionData.quesBlankHtml = quesBlankHtml;
        sessionData.quesSpeakingHtml= quesSpeakingHtml;

        $( "img" )
          .error(function() {
            imgNotLoading();
        });

        
        return this.model.currentQuestion;
    };

    this.stop = function() {
        this.view.stopCountingTime();
    };
}
//added by nivedita
//this.restrictQuestions = function(){
function restrictQuestions(correctAnswer){
    
    //totalCorrectQues.push(refQuesViewObj.correct);
    if(correctAnswer == 1)
    {
        var pushInArr = '1';
        totalCorrectQues.push(pushInArr);
    }
    else if(correctAnswer == '0')
    {
        var pushInArr = '2';
        totalCorrectQues.push(pushInArr);
    }
    
    var delayNextBtn = false;
    
    sessionData.delayNextBtn = delayNextBtn;
    var givenWrong = false;
    if(refQuesViewObj.model.currentQuestion.timeTaken)
        totalTimeQues.push(refQuesViewObj.model.currentQuestion.timeTaken);

    if(previousQuestion.timeTakenExpln)
            totalTimeQues.push(previousQuestion.timeTakenExpln);
    
    var timecounter = 10;
    //alert('counter=>'+counterPath);
    
    if(counterPath == 3 && currentQuestion.qType!='passage' && sessionData.currentLocation.location =="the_classroom")
    {
        var totalTime = 0;
        if(totalCorrectQues.filter(function(x){return x==2}).length == 3)
                givenWrong = true;

        for (var i = 0; i < counterPath; i++) 
        {
            totalTime += totalTimeQues[i] << 0;
        }
        totalCorrectQues = [];
        totalTimeQues = [];
        
        if(totalTime < 20 && givenWrong)    // Time change to 20 seconds 
        {
           // if(givenWrong)
            //{
                totalTimeQues = [];
                totalCorrectQues = [];
                
                if($('#passageNext').css('display') == 'block' || $('#passageNext').css('display') == 'inline-block' || $('#passageNext').css('display') == 'none')
                {

                    $("#passageNext").hide();
                    $("#delayNext").show();

                    sessionData.delayNextBtn = true;
                    delayNextBtn = true; 
                } 
                var id ='';
                if(delayNextBtn)
                {
                    $("#hide_element").show();
                    checkQues = true;
                    timecounter = 10;
                    holdquestion = 1;
                    id = setInterval(function() {
                        timecounter--;
                        if(timecounter <= 0) {
                            clearInterval(id);
                             //$("#passageNext").show();
                            $("#delayNext").hide();
                            $("#hide_element").hide();
                        }
                        else
                        { 
                            if(timecounter==1)
                                $("#delayNext").html("You can submit your response in "+timecounter+" second");
                            else
                                $("#delayNext").html("You can submit your response in "+timecounter+" seconds");
                        }
                    }, 1000);
                    counterPath = 0;
                }
                else
                {
                    checkQues = false;
                    $("#hide_element").hide();
                }
           // }

             $("#delayNext").html('You can submit your response in 10 seconds');
          
        }
    
        counterPath = 0;   
    }
    else if(totalCorrectQues.filter(function(x){return x==2}).length == 3 && totalTimeQues.reduce(function(a, b) { return a + b; }, 0) < 20)
          sessionData.delayNextBtn = true;  

    /*if(givenWrong == false)
        $('#passageNext').show();*/
   /* else
        $('#passageNext').show();*/
}
//end