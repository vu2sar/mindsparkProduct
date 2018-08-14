

var arrID = ["sessionReport","sessionReportNCQ","sessionReportEssay"];
var elementName;
var SESSIONREPORT_DATA2;
var SESSIONREPORT_DATA = {
  "passage": [
    {
      "29": {
        "name": "What was the first Google Doodle?",
        "TC": 4,
        "TIC": 6,
        "questionDetails": {
          "155": {
            "questionText": "“specialty stand-in artwork”<\/strong>  The phrase above means that Google Doodles are __________.",
            "CA": "c",
            "YA": "d",
            "C": "0.00",
            "quesTypeCode": "mcq"
          },
          "156": {
            "questionText": "“…deemed worth of replacing the search engine's ubiquitous<\/u> logo for a day.”<\/strong><\/em>  The underlined word in the sentence above means _____________________.",
            "CA": "d",
            "YA": "a",
            "C": "0.00",
            "quesTypeCode": "mcq"
          },
          "157": {
            "questionText": "What was the first Google Doodle used to show?",
            "CA": "d",
            "YA": "c",
            "C": "0.00",
            "quesTypeCode": "mcq"
          },
          "158": {
            "questionText": "For which of the following events are you most likely to see a Google Doodle on your computer?",
            "CA": "a",
            "YA": "c",
            "C": "0.00",
            "quesTypeCode": "mcq"
          },
          "2713": {
            "questionText": "Google has Doodles for almost every day of the year. What interesting Doodle do you think you would make, and for which occasion?",
            "CA": "",
            "YA": "I would make another google doodle.",
            "C": "1.00",
            "quesTypeCode": "blank"
          }
        }
      },
      "10": {
        "questionDetails": {
          "3210": {
            "questionText": "Match the actions of the characters on the left, with the mood\/emotion on the right.",
            "CA": "",
            "YA": "The donkey's burden reduced:gladness|The donkey fell again on purpose:trickery|The merchant took better care of the donkey from that day:forgiveness|The merchant wanted to teach a lesson:revenge|",
            "C": "1.00",
            "quesTypeCode": "match"
          }
        },
        "TC": 1
      },
      "46": {
        "questionDetails": {
          "304": {
            "questionText": "Which one of these will Alex NOT<\/strong> do?",
            "CA": "a~d",
            "YA": "a~d~",
            "C": "1.00",
            "quesTypeCode": "mcq"
          }
        },
        "TC": 1
      },
      "797": {
        "questionDetails": {
          "3811": {
            "questionText": "",
            "CA": "",
            "YA": "gdfgfdgfg :images12.jpg|dfgfd :2015-09-22.jpg|gfgfd :images5.jpg|gfdg :images8.jpg|",
            "C": "0.00",
            "quesTypeCode": "match"
          }
        },
        "TIC": 1
      }
    }
  ],
  "nonContextual": [
    {
      "2": {
        "TC": 2,
        "TIC": 1,
        "name": "Grammar",
        "questionDetails": {
          "1378": {
            "questionText": "Identify the ADJECTIVE <\/strong>in the following sentence:  The Violet is a particularly delightful flower. <\/strong><\/em>",
            "CA": "c",
            "YA": "c",
            "C": "1.00",
            "quesTypeCode": "mcq"
          },
          "3831": {
            "questionText": "jjhgj dfhdiuf hfid dhgiufdsghdfuighfdiughighiugfdg dfgdfgfd",
            "CA": "",
            "YA": "images5.jpg:55.mp3|images11.jpg:3722.mp3|images6.jpg:3707.mp3|images4.jpg:17.mp3|",
            "C": "0.00",
            "quesTypeCode": "match"
          },
          "1802": {
            "questionText": "Choose the option, which is the correct form of the word given in brackets, to complete the sentence.  Rahul is so [Dropdown_1] (fascinate) with cars that he wants to become a Formula 1 racer.<\/strong><\/em>",
            "CA": "",
            "YA": "Dropdown_1:fascinated|",
            "C": "1.00",
            "quesTypeCode": "blank"
          }
        }
      },
      "1": {
        "TC": 2,
        "TIC": 1,
        "name": "Vocabulary",
        "questionDetails": {
          "3828": {
            "questionText": "fsa saf saf s fsaf sa fsaf sa f",
            "CA": "",
            "YA": "d wsdadasdsa",
            "C": "1.00",
            "quesTypeCode": "blank"
          },
          "3251": {
            "questionText": "Four words from the audio have been listed below. Match the words with the meanings. Note:<\/strong> Listen to the audio again to identify the words in context.",
            "CA": "",
            "YA": "endowed :produced something |spawned :gifted |woven :put together |sequel :a continuation |",
            "C": "0.00",
            "quesTypeCode": "match"
          },
          "2851": {
            "questionText": "sayfsyf yugyuas fayf ayuf yauf yfysaufgsyafgsayf [Blank_1]",
            "CA": "",
            "YA": "Blank_1:genuine|",
            "C": "1.00",
            "quesTypeCode": "blank"
          }
        }
      }
    }
  ],
  "essay": [
    {
      "9": {
        "userResponse": "ani",
        "essayTitle": "If you could be an animal, which one would you be?",
        "isSubmitted": 0
      },
      "10": {
        "userResponse": "It is running out in the meadows.",
        "essayTitle": "What is your favourite memory?",
        "isSubmitted": 1
      }
    }
  ]
};
var setFlag = false;
var setFlagBlankMcq = false;

var mcq        = [];
var blank      = [];
var sequence1   = [];
var match      = [];
var makingWord = [];

//uses classList, setAttribute, and querySelectorAll
//if you want this to work in IE8/9 youll need to polyfill these
var startAccordion = function() {
  var d = document,
    accordionToggles = d.querySelectorAll('.js-accordionTrigger'),
    setAria,
    setAccordionAria,
    switchAccordion,
    touchSupported = ('ontouchstart' in window),
    pointerSupported = ('pointerdown' in window);

  skipClickDelay = function(e) {
    e.preventDefault();
    e.target.click();
  };

  setAriaAttr = function(el, ariaType, newProperty) {
    el.setAttribute(ariaType, newProperty);
  };
  setAccordionAria = function(el1, el2, expanded) {
    switch (expanded) {
      case "true":
        setAriaAttr(el1, 'aria-expanded', 'true');
        $(el1).attr({"title": "Click again to close"});
        setAriaAttr(el2, 'aria-hidden', 'false');
        break;
      case "false":
        setAriaAttr(el1, 'aria-expanded', 'false');
        $(el1).attr({"title": "Click to open"});
        setAriaAttr(el2, 'aria-hidden', 'true');
        break;
      default:
        break;
    }
  };
  switchAccordion = function(e) {
        e.preventDefault();
        var ID = $(e.target).attr("aria-controls");
        var thisAnswer = document.getElementById(ID);
        var thisQuestion = e.target;

        //added by nivedita
        var classTarget = $(e.target).attr("class");
        if(classTarget == 'accordion-title accordionTitle accordionTitleNoBefore js-accordionTrigger')
        {
            $(e.target).attr("title", 'Click to close');
        }
        else if(classTarget == 'accordion-title accordionTitle accordionTitleNoBefore js-accordionTrigger is-collapsed is-expanded')
        {
            $(e.target).attr("title", 'Click to open');
        }
        //end
        /*if (thisAnswer.classList.contains('is-collapsed')) {
            //resets all the expanded anchor tags
            while($('a.is-expanded').length) {
                $('a.is-expanded')[0].click();
            }
            setAccordionAria(thisQuestion, thisAnswer, 'true');
        }
        else {
            setAccordionAria(thisQuestion, thisAnswer, 'false');
        }*/
        thisQuestion.classList.toggle('is-collapsed');
        thisQuestion.classList.toggle('is-expanded');
        thisAnswer.classList.toggle('is-collapsed');
        thisAnswer.classList.toggle('is-expanded');

        thisAnswer.classList.toggle('animateIn');
  };

  
  for (var i = 0, len = accordionToggles.length; i < len; i++) {
    if (touchSupported) {
      accordionToggles[i].addEventListener('touchstart', skipClickDelay, false);
    }
    if (pointerSupported) {
      accordionToggles[i].addEventListener('pointerdown', skipClickDelay, false);
    }
    accordionToggles[i].addEventListener('click', switchAccordion, false);
    accordionToggles[i].click();

  }
  
};

// var SESSIONREPORT_DATA2;
//function SESSIONREPORT(parent) {
function SESSIONREPORT(parent,useridreport,startDate,endDate,attemptTableClass) {
    var _myself = this;
    this.countRows = 0;
    this.rowNum = 0;
    //this.getData = function(id, callback) {
    this.getData = function(callback) {
        this.callback = callback;
        $.ajax({
            context: this,
            //url : Helpers.constants['CONTROLLER_PATH'] + 'endsessionreport/getStudentEndSessionReport/'+ id,
            url : Helpers.constants['CONTROLLER_PATH'] + 'endsessionreport/getStudentEndSessionReport/',
            type : 'POST',
            data : {'userid':useridreport,'startDate':startDate,'endDate':endDate,'childClass':attemptTableClass},
            dataType : 'JSON'
        }).done(function(response) {
            Helpers.ajax_response( _myself.showSessionReport , response, [])        
        });
    };
    
    this.showSessionReport = function(response, extraParams){
        if(response){
            SESSIONREPORT_DATA2 = response;
            if(_myself.checkObject()) {
                _myself.createSessionReport(SESSIONREPORT_DATA2,parent);
                startAccordion();
            }
        }
    }
    this.checkObject = function() {
        var testP = Object.keys(SESSIONREPORT_DATA2["passage"][0]).length;
        var testN1TC = SESSIONREPORT_DATA2["nonContextual"][0][1]["TC"];
        var testN1TIC = SESSIONREPORT_DATA2["nonContextual"][0][1]["TIC"];
        var testN2TC = SESSIONREPORT_DATA2["nonContextual"][0][2]["TC"];
        var testN2TIC = SESSIONREPORT_DATA2["nonContextual"][0][2]["TIC"];
        var testEssay =Object.keys(SESSIONREPORT_DATA2["essay"][0]).length;

        if(testP == 0 && testN2TIC == 0 && testN2TC == 0 && testN1TIC == 0 && testN1TC == 0 && testEssay == 0) {
            Helpers.prompt({
                text : 'You have not attempted any questions yet.',
                noClose : true
            });
            return false;
        }
        return true;
    };

    this.createSessionReport = function(data) {
        var dataPassage = '<dt class="accordionTitleNoBefore" id="headerQuestionsTab">';
        dataPassage+='<span style="width: 10%;">Sr. No.</span>';
        dataPassage+='<span style="width: 70%; text-align: left;">Passage Name</span>';
        dataPassage+='<span style="width: 16%;" >Total Attempted</span>';
        dataPassage+='</dt>';

        var dataNCQ = '<dt class="accordionTitleNoBefore" id="headerQuestionsTabNCQ">';
        dataNCQ+='<span style="width: 10%;">Sr. No.</span>';
        dataNCQ+='<span style="width: 70%; text-align: left;">Topic Name</span>';
        dataNCQ+='<span style="width: 16%;" >Total Attempted</span>';
        dataNCQ+='</dt>';

        var dataEssay = '<dt class="accordionTitleNoBefore" id="headerQuestionsTabEssay">';
        dataEssay+='<span style="width: 9%; text-align: left;">Sr. No.</span>';
        dataEssay+='<span style="width: 75%; text-align: left;">Essay Name</span>';
        dataEssay+='<span style="width: 10%; text-align: center;">Submitted<br>(Yes/No)</span>';
        dataEssay+='</dt>';
        
        // // Un-comment following if correctly/incorrectly answered is required
        // dataPassage+='<span class="accordionTitleNoBefore" style="background-color: #f48a53; width: 15%">Correctly answered</span>';
        // dataPassage+='<span style="width: 15%;" >Incorrectly answered</span>';
        // dataNCQ+='<span class="accordionTitleNoBefore" style="background-color: #f48a53; width: 15%">Correctly answered</span>';
        // dataNCQ+='<span style="width: 15%;" >Incorrectly answered</span>';
        
        $("#sessionReport").html(dataPassage);
        $("#sessionReportNCQ").html(dataNCQ);
        $("#sessionReportEssay").html(dataEssay);
        
        this.createNodes(data["passage"], parent[0],0);
        this.createNodes(data["nonContextual"], parent[1],0);
        this.createNodes(data["essay"], parent[2],0);
        
        this.checkDiv();

        if(sessionData.category != 'STUDENT')
        {   

            $("#backDiv").remove();
            $("#sessionReportContainer").append('<div class="col-md-12" id="backDiv"><button type="button" onclick="" id="backSessionReport" style="bottom:5px; font-size:1.3em !important" class="btn-small btn-primary">Back</button></div>');
        }
    };
    
    this.checkDiv = function() {

        //latest condition so that views can also be checked for questions
        if(sessionData.currentLocation.location != "session_report" && ($("#sessionReport").find( "dt" ).length > 1 || $("#sessionReportNCQ").find( "dt" ).length > 1 || $("#sessionReportEssay").find( "dt" ).length > 1)) { 
            sessionData.currentLocation.location = "session_report";

            while($('a.is-expanded').length) {
                $('a.is-expanded')[0].click();
            }
            
            // $("#passageNext,#lookBackFigure").hide();
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
        if(sessionData.category != 'STUDENT')
        {
            $("#reportSection").text('');
            $("#reportSection").text('Session Report of -'+$("#trchildName").val());
        }
        else
        {
            $("#reportSection").text('Daily Report');   
        }
        if(this.callback) {
            this.callback();
        }
    };
    
    this.createNodes = function(data,parent,recursionFlag,key) {
        var element;
        var subElement;
        if(recursionFlag == 0) {
            this.rowNum = 0;
        }
        if(parent.id == arrID[0]) {
            elementName = "passage";
        }
        else if(parent.id == arrID[1]) {
            elementName = "nonContextual";
        }
        else if(parent.id == arrID[2]) {
            elementName = "essay";
        }
            var temp = SESSIONREPORT_DATA2[elementName][0];

        if(typeof data[0][key] == "object") {
            if(key && (data[0][key].name != undefined)) {
                
                element  = document.createElement('dt');
                subElement = document.createElement('dl');
                element.className = 'accordionTitleNoBefore';
                this.countRows++;
                this.rowNum++;

                if(elementName != 'nonContextual' && elementName != 'essay')
                    $(element).append("<span style='width: 10%;cursor: pointer;text-decoration: underline;' class='passage"+key+"'>" + this.rowNum + "</span>");
                else    
                    $(element).append("<span style='width: 10%;' class='passage"+key+"'>" + this.rowNum + "</span>");
                $(element).append("<span style='width: 70%; text-align: left;' >" + data[0][key].name + "</span>");
                if(elementName == "passage" && data[0][key].passageType != 'Conversation')
                {
                    $(element).on("click",".passage"+key,function() {
                        showContent( key, $('#lookbackContent')[0],data[0][key].Form);
                    });
                }
                else if(elementName == "passage" && data[0][key].passageType == 'Conversation')
                {
                    $(element).on("click",".passage"+key,function() {
                        showConversation( key, data[0][key].passageType, data[0][key].passageID);
                    });   
                }

                // Un-comment following if correctly/incorrectly answered is required
                /*if(data[0][key].TC != 0) {
                    var dataReturned;
                    dataReturned = this.createQuestionList(key,"correct");
                    $(element).append("<a data-toggle='tooltip' title='Click to open' class='accordion-title accordionTitle accordionTitleNoBefore js-accordionTrigger' aria-expanded='false' aria-controls='accordionTC"+this.countRows+"'>"+data[0][key].TC+"</a>");
                    $(subElement).append('<dd class="accordion-content accordionItem is-collapsed" id="accordionTC'+this.countRows+'" aria-hidden="true">'+dataReturned+'</dd>');
                }
                else {
                    $(element).append("<a class='accordion-title accordionTitleNoBefore noLink'>" + data[0][key].TC + "</a>");
                }

                if(data[0][key].TIC != 0) {
                    var dataReturned;
                    dataReturned = this.createQuestionList(key,"incorrect");
                    $(element).append("<a data-toggle='tooltip' title='Click to open' class='accordion-title accordionTitle accordionTitleNoBefore js-accordionTrigger' aria-expanded='false' aria-controls='accordionTIC"+this.countRows+"'>"+data[0][key].TIC+"</a>");
                    $(subElement).append('<dd class="accordion-content accordionItem is-collapsed" id="accordionTIC'+this.countRows+'" aria-hidden="true">'+dataReturned+'</dd>');
                }
                else {
                    $(element).append("<a class='accordion-title accordionTitleNoBefore noLink'>" + data[0][key].TIC + "</a>");
                }*/
                if(parseInt(data[0][key].TIC+data[0][key].TC) != 0) {
                    var dataReturned;                    
                    dataReturned = this.createQuestionList(key,"all");
                    $(element).append("<a data-toggle='tooltip' title='Click to open' class='accordion-title accordionTitle accordionTitleNoBefore js-accordionTrigger' aria-expanded='false' aria-controls='accordionAll"+this.countRows+"'>"+parseInt(data[0][key].TC+data[0][key].TIC)+"</a>");
                    $(subElement).append('<dd class="accordion-content accordionItem is-collapsed" id="accordionAll'+this.countRows+'" aria-hidden="true">'+dataReturned+'</dd>');
                }
                else {
                    $(element).append("<a class='accordion-title accordionTitleNoBefore noLink'>" + parseInt(data[0][key].TC+data[0][key].TIC) + "</a>");
                }
            }
            else if(data[0][key].essayTitle != undefined) {
                element  = document.createElement('dt');
                subElement = document.createElement('dl');
                element.className = 'accordionTitleNoBefore';
                this.countRows++;
                this.rowNum++;
                $(element).append("<span style='width: 10%'>" + this.rowNum + "</span>");
                $(element).append("<span style='width: 75%; text-align: left; cursor: pointer;' class='essayHeadings essay"+key+"'>" + data[0][key].essayTitle + "</span>");
                if(data[0][key].isSubmitted)
                    $(element).append("<span style='width: 10%; text-align:center;'>Yes</span>");
                else
                    $(element).append("<span style='width: 10%; text-align:center;'>No</span>");
                $(element).on("click",".essay"+key,function() {
                    showEssayLookback( key, $('#essayLookbackContent')[0]);
                });
            }
        }

        if(recursionFlag == 0) {
            recursionFlag = 1;
            for(var key in temp) {
                var value = this.createNodes(data, parent,recursionFlag, key);
            }
        }
       
        $(parent).append(element);
        $(parent).append($(subElement).html());
        return data;
    };
    this.createQuestionList = function(element,columnType) {
        var newText  = document.createElement('Table');
        var countSeparateRows = 0;
        if(columnType == "all") {
            $(newText).append("<tr><th style='width:9%'>Sr. No.</th><th style='text-align:left; width:31%'>Question</th><th style='text-align:center; width:35%;max-width:35%;'>Your Answer</th><th style='text-align:center; width:5%'>#</th><th style='text-align:center; width:20%'>Correct Answer</th></tr>");
        }
        else {
            $(newText).append("<tr><th style='width:9%'>Sr. No.</th><th style='text-align:left; width:31%'>Question</th><th style='text-align:center; width:35%;max-width:35%;'>Your Answer</th><th style='text-align:center; width:5%'>#</th><th style='text-align:center; width:20%'>Correct Answer</th></tr>");
        }
        var elem;
        newText.id="questionDetailsTab";

        var dataArr;
        $.each(SESSIONREPORT_DATA2[elementName][0],function(key,value) {
            if(element == key)
                dataArr = value;
        });
        $.each(dataArr, function(key2, value) {
           if(typeof value == "object") {
                $.each(value, function(key3, value2) {
                    
                    //console.log(JSON.stringify(value2));
                    //var abc1 = JSON.stringify(value2);
                    //console.log(JSON.parse(abc));
                    //  Un-comment following if correctly/incorrectly answered is required
                    /*if((columnType == "correct" && value2.C == 1) || (columnType == "incorrect" && value2.C == 0)) {
                        elem  = document.createElement('tr');

                        elem.className = 'sessionReport-container';
                        countSeparateRows++;
                        $(elem).append("<td style='width:10%'>" + countSeparateRows + "</td>");
                        $(elem).append("<td style='text-align:left; width:40%; padding-right: 30px;'>" + value2.questionText + "</td>");
                        if(value2.quesTypeCode == "mcq") {
                            $(elem).append("<td style='text-align:left; width:25%; padding-right: 30px;'>" + (value2.YA).toUpperCase() + "</td>");
                            $(elem).append("<td style='text-align:left; width:25%'>" + (value2.CA).toUpperCase() + "</td>");    
                        }
                        else {
                            $(elem).append("<td style='text-align:left; width:25%; padding-right: 30px;'>" + value2.YA + "</td>");
                            $(elem).append("<td style='text-align:left; width:25%'>" + value2.CA + "</td>");
                        }
                        $(newText).append($(elem));
                    }
                    else */if(columnType == "all") {
                        elem  = document.createElement('tr');

                        elem.className = 'sessionReport-container';
                        //elem.setAttribute('onclick', 'showQuesContent()');
                        countSeparateRows++;
                        

                        
                        
                        
                        
                        if(value2.quesTypeCode == 'mcq')
                        {
                            
                            //$(elem).append("<td style='width:9%'><a onclick=\"showQuesContentMcq('"+value2.quesSubType+"','"+value2.qType+"','"+value2.totalOptions+"','"+value2.explanation+"','"+value2.YA+"','"+value2.CA+"','"+value2.C+"','"+value2.optSubType+"','"+handleQuotesMatch(option_a)+"','"+handleQuotesMatch(option_b)+"','"+handleQuotesMatch(option_c)+"','"+handleQuotesMatch(option_d)+"','"+handleQuotesMatch(option_e)+"','"+handleQuotesMatch(option_f)+"','"+handleQuotesMatch(value2.sound_a)+"','"+handleQuotesMatch(value2.sound_b)+"','"+handleQuotesMatch(value2.sound_c)+"','"+handleQuotesMatch(value2.sound_d)+"','"+handleQuotesMatch(value2.sound_e)+"','"+handleQuotesMatch(value2.sound_f)+"','"+handleQuotesMatch(desc_a)+"','"+handleQuotesMatch(desc_b)+"','"+handleQuotesMatch(desc_c)+"','"+handleQuotesMatch(desc_d)+"','"+handleQuotesMatch(desc_e)+"','"+handleQuotesMatch(desc_f)+"','"+value2.questionText+"','"+handleQuotesMatch(value2.quesImage)+"')\">" + countSeparateRows + "</a></td>");    
                            
                            mcq[value2.qcode] = {'quesSubType' : value2.quesSubType,'qType' : value2.qType, 'qcode' : value2.qcode, 'totalOptions' : value2.totalOptions,'explanation':value2.explanation,'YA':value2.YA, 'CA': value2.CA,'C': value2.C,'optSubType':value2.optSubType,'option_a':value2.option_a,'option_b':value2.option_b,'option_c':value2.option_c,'option_d':value2.option_d,'option_e':value2.option_e,'option_f' : value2.option_f,'sound_a':value2.sound_a,'sound_b':value2.sound_b,'sound_c':value2.sound_c,'sound_d':value2.sound_d,'sound_e':value2.sound_e,'sound_f':value2.sound_f,'desc_a':value2.desc_a,'desc_b':value2.desc_b,'desc_c':value2.desc_c,'desc_d':value2.desc_d,'desc_e':value2.desc_e,'desc_f':value2.desc_f,'questionText':value2.questionText,'quesImage':value2.quesImage};
                            
                            $(elem).append("<td style='width:9%'><a onclick=\"showQuesContentMcq('"+value2.qcode+"')\">" + countSeparateRows + "</a></td>");
                        }
                        else if(value2.quesTypeCode == 'blank')
                        {
                            /*var strText1 = value2.questionText.replace(/"|'/g, "").replace(/\(|\)/g, "").replace(/&ldquo;/g, ' ').replace(/&rdquo;/g, ' ').replace(/&#10;/g,"").replace(/&nbsp;/g," ").replace(/&lsquo;/g," ").replace(/&rsquo;/g," ").replace(/<br\s*[\/]?>/g, "");

                            var strExp1 = value2.explanation.replace(/"|'/g, "").replace(/\(|\)/g, "").replace(/&ldquo;/g, ' ').replace(/&rdquo;/g, ' ').replace(/&#10;/g,"").replace(/&nbsp;/g," ").replace(/&lsquo;/g," ").replace(/&rsquo;/g," ").replace(/<br\s*[\/]?>/g, "");*/
                            
                            blank[value2.qcode] = {'qType' : value2.qType,'quesText' : value2.questionText, 'queParams' : value2.CAA, 'quesAudioIconSound' : value2.quesAudioIconSound,'userResponse':value2.YA,'qcode':value2.qcode, 'explanation': value2.explanation,'correctResponse':value2.CA,'correct':value2.C,'paramname':value2.paramname,'paramTotal':value2.paramTotal,'actualUserResponse':value2.actualUserResponse,'actualcorrAns':value2.actualcorrAns};
                            
                            $(elem).append("<td style='width:9%'><a onclick=\"showQuesContentBlank('"+value2.qcode+"')\">" + countSeparateRows + "</a></td>");
                        }
                        else if(value2.quesTypeCode == 'sequencing')
                        {
                            sequence1[value2.qcode] = {'totalOptions' : value2.totalOptions,'quesSubType' : value2.quesSubType, 'optSubType' : value2.optSubType, 'explanation' : value2.explanation,'userResponse':value2.YA,'option_a':value2.option_a, 'option_b': value2.option_b,'option_c': value2.option_c,'option_d': value2.option_d,'option_e': value2.option_e,'option_f': value2.option_f,'correct': value2.C,'quesType': value2.questionType,'quesId': value2.qcode,'sound_a': value2.sound_a,'sound_b': value2.sound_b,'sound_c': value2.sound_c,'sound_d': value2.sound_d,'sound_e': value2.sound_e,'sound_f': value2.sound_f};
                            
                            $(elem).append("<td style='width:9%'><a onclick=\"showQuesSeq1('"+value2.qcode+"')\">" + countSeparateRows + "</a></td>");

                            /*var strText = value2.questionText.replace(/"|'/g, "").replace(/\(|\)/g, "").replace(/&ldquo;/g, ' ').replace(/&rdquo;/g, ' ').replace(/<br\s*[\/]?>/g, "").replace(/&#10;/g,"").replace(/&nbsp;/g," ").replace(/&lsquo;/g," ").replace(/&rsquo;/g," ");
                            var strExp = value2.explanation.replace(/"|'/g, "").replace(/&#10;/g, ' ').replace(/\(|\)/g, "").replace(/&ldquo;/g, ' ').replace(/&rdquo;/g, ' ').replace(/<br\s*[\/]?>/g, "").replace(/&lsquo;/g," ").replace(/&rsquo;/g," ");
                            var desc_a = value2.desc_a.replace(/"|'/g, "").replace(/\(|\)/g, "").replace(/&ldquo;/g, ' ').replace(/&rdquo;/g, ' ');
                            var desc_b = value2.desc_b.replace(/"|'/g, "").replace(/\(|\)/g, "").replace(/&ldquo;/g, ' ').replace(/&rdquo;/g, ' ');
                            var desc_c = value2.desc_c.replace(/"|'/g, "").replace(/\(|\)/g, "").replace(/&ldquo;/g, ' ').replace(/&rdquo;/g, ' ');
                            var desc_d = value2.desc_d.replace(/"|'/g, "").replace(/\(|\)/g, "").replace(/&ldquo;/g, ' ').replace(/&rdquo;/g, ' ');
                            var desc_e = value2.desc_e.replace(/"|'/g, "").replace(/\(|\)/g, "").replace(/&ldquo;/g, ' ').replace(/&rdquo;/g, ' ');
                            var desc_f = value2.desc_f.replace(/"|'/g, "").replace(/\(|\)/g, "").replace(/&ldquo;/g, ' ').replace(/&rdquo;/g, ' ');

                            var option_a = value2.option_a.replace(/<br\s*[\/]?>/gi, "");
                            var option_b = value2.option_b.replace(/<br\s*[\/]?>/gi, "");
                            var option_c = value2.option_c.replace(/<br\s*[\/]?>/gi, "");
                            var option_d = value2.option_d.replace(/<br\s*[\/]?>/gi, "");
                            var option_e = value2.option_e.replace(/<br\s*[\/]?>/gi, "");
                            var option_f = value2.option_f.replace(/<br\s*[\/]?>/gi, "");

                            $(elem).append("<td style='width:9%'><a onclick=\"showQuesSeq1('"+value2.totalOptions+"','"+value2.quesSubType+"','"+value2.optSubType+"','"+strExp+"','"+handleQuotesMatch(value2.YA)+"','"+handleQuotesMatch(option_a)+"','"+handleQuotesMatch(option_b)+"','"+handleQuotesMatch(option_c)+"','"+handleQuotesMatch(option_d)+"','"+handleQuotesMatch(option_e)+"','"+handleQuotesMatch(option_f)+"','"+value2.C+"','"+value2.questionType+"','"+value2.qcode+"','"+handleQuotesMatch(value2.sound_a)+"','"+handleQuotesMatch(value2.sound_b)+"','"+handleQuotesMatch(value2.sound_c)+"','"+handleQuotesMatch(value2.sound_d)+"','"+handleQuotesMatch(value2.sound_e)+"','"+handleQuotesMatch(value2.sound_f)+"')\">" + countSeparateRows + "</a></td>");
*/
                            
                        }
                        else if(value2.quesTypeCode == 'match')
                        {

                            match[value2.qcode] = {'totalOptions' : value2.totalOptions,'explanation' : value2.explanation, 'quesType' : value2.questionType, 'quesId' : value2.qcode,'quesSubType':value2.quesSubType,'optSubType':value2.optSubType, 'option_a': value2.option_a,'option_b': value2.option_b,'option_c': value2.option_c,'option_d': value2.option_d,'option_e': value2.option_e,'option_f': value2.option_f,'userResponsee': value2.YA,'correct': value2.C};

                           /* var option_a = value2.option_a.replace(/<br\s*[\/]?>/gi, "");
                            var option_b = value2.option_b.replace(/<br\s*[\/]?>/gi, "");
                            var option_c = value2.option_c.replace(/<br\s*[\/]?>/gi, "");
                            var option_d = value2.option_d.replace(/<br\s*[\/]?>/gi, "");
                            var option_e = value2.option_e.replace(/<br\s*[\/]?>/gi, "");
                            var option_f = value2.option_f.replace(/<br\s*[\/]?>/gi, "");*/


                            //$(elem).append("<td style='width:9%'><a onclick=\"showQuesMatch('"+value2.totalOptions+"','"+strExp+"','"+value2.questionType+"','"+value2.qcode+"','"+value2.quesSubType+"','"+value2.optSubType+"','"+handleQuotesMatch(option_a)+"','"+handleQuotesMatch(option_b)+"','"+handleQuotesMatch(option_c)+"','"+handleQuotesMatch(option_d)+"','"+handleQuotesMatch(option_e)+"','"+handleQuotesMatch(option_f)+"','"+handleQuotesMatch(value2.YA)+"','"+value2.C+"')\">" + countSeparateRows + "</a></td>");

                            $(elem).append("<td style='width:9%'><a onclick=\"showQuesMatch('"+value2.qcode+"')\">" + countSeparateRows + "</a></td>");
                        }
                        else if(value2.quesTypeCode == 'makingword')
                        {
                            makingWord[value2.qcode] = {'quesType' : value2.questionType,'quesId' : value2.qcode, 'explanation' : value2.explanation, 'userResponsee' : value2.YA};

                            //$(elem).append("<td style='width:9%'><a onclick=\"makingwordQues('"+value2.questionType+"','"+value2.qcode+"','"+strExp+"','"+handleQuotesMatch(value2.YA)+"')\">" + countSeparateRows + "</a></td>");
                            $(elem).append("<td style='width:9%'><a onclick=\"makingwordQues('"+value2.qcode+"')\">" + countSeparateRows + "</a></td>");
                        }
                        
                        //$(elem).append("<td style='width:9%'><a onclick='showQuesContent1()'>" + countSeparateRows + "</a></td>");
                        var icon = '';
                        if(value2.C == 1 || value2.qType == 'openEnded')
                            icon = '<i class="fa fa-check mark-correct" aria-hidden="true"></i>';
                        else if(value2.C == 0)
                            icon = '<i class="fa fa-times mark-wrong" aria-hidden="true"></i>';
                            

                        // for mcqs : uncomment below condition for handling all qtypes on session report
                        // if(value2.quesTypeCode == "mcq") {
                            $(elem).append("<td style='text-align:left; width:31%;' >" + value2.questionText + "</td>");
                            $(elem).append("<td style='text-align:center; width:35%; max-width:35%; position:relative;'>" + (value2.YA).toUpperCase()+"</td>");
                            $(elem).append("<td style='text-align:center; width:5%; position:relative;'>"+ icon+"</td>");
                            $(elem).append("<td style='text-align:center; width:20%'>" + (value2.CA).toUpperCase() + "</td>");    
                        // }
                        // for blanks : uncomment below condition for handling all qtypes on session report
                        // else if(value2.quesTypeCode == "blank") {
                        //     var question = value2.questionText;
                        //     //replace blank with inputs
                        //     question = question.replace(/\[(Blank_\d+)\]/gi, '<input id="$1" type="text" disabled=true>');
                        //     //replace dropDowns with select tags
                        //     question = question.replace(/\[(Dropdown_\d+)\]/gi, '<select id="$1" disabled=true><option value="Choose one">Choose one</option></select>');
                        //     $(elem).append("<td style='text-align:left; width:43%; padding-right: 30px;'>" + question + "</td>");
                        //     $(elem).append("<td style='text-align:left; width:15%; padding-right: 30px;'>" + value2.YA + "</td>");
                        //     $(elem).append("<td style='text-align:left; width:15%'>" + value2.CA + "</td>");
                        // }

                        $(newText).append($(elem));
                    }
                });
           }
        });
        return newText.outerHTML; // returns object as string
    };
};
function showEssayLookback( key, target) {
    var essayLookbackContainer = document.createElement('div');
    essayLookbackContainer.className = 'essayLookbackContainer';
    var close_button = '<div class="prompt-heading"><button onclick="closeMcq(\'essay\')" class="close-prompt toast-close" prompt-close=\'#essayLookbackContainer\'><i class="fa fa-close"></i></button> </div>';
    var header = document.createElement('h2');
    header.innerHTML = SESSIONREPORT_DATA2["essay"][0][key].essayTitle;
    SESSIONREPORT_DATA2["essay"][0][key].userResponse=SESSIONREPORT_DATA2["essay"][0][key].userResponse.replace(/&nbsp;/ig,'');
    SESSIONREPORT_DATA2["essay"][0][key].userResponse=SESSIONREPORT_DATA2["essay"][0][key].userResponse.replace(/<p><\/p>/ig,'<br/>');
    essayLookbackContainer.innerHTML =  close_button + header.outerHTML + SESSIONREPORT_DATA2["essay"][0][key].userResponse;
    $(target).html(essayLookbackContainer);
    $('#modalBlocker').show();
    $(target).show();
};

function showContent(key, target,form)
{

    sessionData.qID = SESSIONREPORT_DATA2["passage"][0][key].passageID;
    sessionData.currentLocation.location = 'session_report';

    var lookbackContainer = document.createElement('div');
    lookbackContainer.className = 'lookbackContainer';
    var close_button = '<div class="prompt-heading"><button onclick="closeMcq(\'passage\')" class="close-prompt toast-close" prompt-close=\'#lookbackContainer\'><i class="fa fa-close"></i></button> </div>';
    var header = document.createElement('h2');
    header.innerHTML = SESSIONREPORT_DATA2["passage"][0][key].name.replace(/\\/g, '');
    SESSIONREPORT_DATA2["passage"][0][key].passageContent=SESSIONREPORT_DATA2["passage"][0][key].passageContent.replace(/&nbsp;/ig,'');
    SESSIONREPORT_DATA2["passage"][0][key].passageContent=SESSIONREPORT_DATA2["passage"][0][key].passageContent.replace(/<p><\/p>/ig,'<br/>');
    //lookbackContainer.innerHTML =  close_button + header.outerHTML + SESSIONREPORT_DATA2["passage"][0][key].passageContent;
    lookbackContainer.innerHTML =  close_button + header.outerHTML + SESSIONREPORT_DATA2["passage"][0][key].passageContent.replace(/<img\s[^>]*?src\s*=\s*['\"]([^'\"]*?)['\"][^>]*?>/ig, "<img src=''/>");  // To not show image in lookback if there is any Change By Aditya
    $(target).html(lookbackContainer);

    if(form == "Poem")
    {
        $(".lookbackContainer p").css("text-align","center");
        $(".lookbackContainer h2").css("text-align","center");
    }       
    $('#modalBlocker').show();
    $(target).show();
}

function showConversation(key, passageType, itemID)
{
    var itemID      = itemID
    var passageType = passageType;
    sessionData.qID = itemID;
    sessionData.currentLocation.location = 'session_report';

    if(passageType == 'Conversation')
    {

        audioObject = new Audio($('#audioContainer')[0]);
        audioObject.view.showLookback(itemID, $('#audioLookback')[0], function() {
            $('#modalBlocker').show();
            $(".prompt-heading").html('');
            var btn = '<button onclick="closeMcq(\'conversation\')" class="close-prompt toast-close" prompt-close=\'#audioLookBack\'><i class="fa fa-close"></i></button>'
            $(".prompt-heading").html(btn);
            $('#audioLookback').show();
        });
    } 
}

//function showQuesContentMcq(quesSubType,qType,totalOptions,explanation,userResponse,correctAnswer,isCorrect,optionSubType,option_a, option_b, option_c, option_d, option_e, option_f,sound_a, sound_b, sound_c, sound_d, sound_e, sound_f,desc_a, desc_b, desc_c, desc_d, desc_e, desc_f,quesTextMcq,quesImgText)
function showQuesContentMcq(qcode)
{
    setFlagBlankMcq = true;
    sessionData.qID = mcq[qcode].qcode;
    var quesSubType   = mcq[qcode].quesSubType;
    var qType         = mcq[qcode].qType;
    var qcode         = mcq[qcode].qcode;
    var totalOptions  = mcq[qcode].totalOptions;
    var explanation   = mcq[qcode].explanation
    var userResponse  = mcq[qcode].YA;
    var correctAnswer = mcq[qcode].CA;
    var isCorrect     = mcq[qcode].C;
    var optionSubType = mcq[qcode].optSubType;
    var option_a      = mcq[qcode].option_a;
    var  option_b     = mcq[qcode].option_b;
    var  option_c     = mcq[qcode].option_c;
    var  option_d     = mcq[qcode].option_d;
    var  option_e     = mcq[qcode].option_e;
    var  option_f     = mcq[qcode].option_f;
    var sound_a       = mcq[qcode].sound_a;
    var  sound_b      = mcq[qcode].sound_b;
    var  sound_c      = mcq[qcode].sound_c;
    var  sound_d      = mcq[qcode].sound_d;
    var  sound_e      = mcq[qcode].sound_e;
    var  sound_f      = mcq[qcode].sound_f;
    var desc_a        = mcq[qcode].desc_a;
    var  desc_b       = mcq[qcode].desc_b;
    var  desc_c       = mcq[qcode].desc_c;
    var  desc_d       = mcq[qcode].desc_d;
    var  desc_e       = mcq[qcode].desc_e;
    var  desc_f       = mcq[qcode].desc_f;
    var quesTextMcq   = mcq[qcode].questionText;
    var quesImgText   = mcq[qcode].quesImage;

    //end here
    var imageArray = [];

    var allOptionsArr = [];
    //allOptionsArr = [arrMcq[0], arrMcq[1], arrMcq[2], arrMcq[3], arrMcq[4], arrMcq[5]];
    allOptionsArr = [option_a, option_b, option_c, option_d, option_e, option_f];

    var allOptionsSoundArr = [];
    allOptionsSoundArr = [sound_a, sound_b, sound_c, sound_d, sound_e, sound_f];

    var descAllOptionsArr = []
    descAllOptionsArr = [desc_a, desc_b, desc_c, desc_d, desc_e, desc_f];

    //var quesText = quesTextMcq[0];
    var quesText = quesTextMcq;

    var optLabelArr           = ["A", "B", "C", "D", "E", "F", "G", "H"];
    var optLabelForCorrectAns = ["a", "b", "c", "d", "e", "f"];

    var assetsPath = Helpers.constants.THEME_PATH + "img/Language/templates/";
    var imagePath = Helpers.constants.LIVE_CONTENT_PATH + 'templates_qtype/images/';
    var soundPath = Helpers.constants.LIVE_CONTENT_PATH + 'templates_qtype/sounds/';

    var quesImg = quesImgText;
    
    setTimeout(function(){

        var modal                       = document.getElementById('myModalSessionReport');
        modal.style.display             = "block";


        /*var lookbackContainerQues = document.createElement('div');
        lookbackContainerQues.className = 'lookbackContainerQues';
        var close_button = '<div class="prompt-heading"><button onclick="closeMcq()" class="close-prompt toast-close" prompt-close=\'#lookbackContainerQues\'><i class="fa fa-close"></i></button> </div>';*/

        var bodyHtml = '<div id="subContainerQuestion" class="container-fluid qtypeTopBuffer"><div class="qtypeClQuesDiv row" ><div id=quesDiv1></div></div><div class="qtypeClSndIcon row"><div class="col-sm-11" id=sndIcon></div></div><div class="qtypeClImgMcq row"><div class="col-sm-11" id=imgDiv1></div></div><div id="qtypeContainerOpt1"></div><div id="qtypeContainerOptMaking"></div><div id="explantionAreaMcq"></div><div id="submitContainer" class="row" ></div><div class="row" id="forALignExplanation"></div><div id="correctWrongSign"></div><div id ="animSndIcon"></div></div>';

        $("#sessionReportQues").html(bodyHtml);

        /*lookbackContainerQues.innerHTML =  close_button + bodyHtml;
        $("#lookbackContentQues").html(lookbackContainerQues);*/
        

        var arrCorrectAnswer = [];
        var userResponseArr  = [];
        var newArray         = [];


        if (qType == "multiCorrect") {
            arrCorrectAnswer = correctAnswer.split("~");
            userResponseArr = userResponse.split("~");
            var newArray = userResponseArr.filter(function(v){return v!==''});
        }
        
        /*if (quesSubType == "textandimage" || quesSubType == "image" || quesSubType == "imageandaudioicon") {
            imageArray.push(imagePath + "" + quesImg);
        }
        if (optionSubType == "image") {
            for (var p = 0; p < totalOptions; p++) {
                if (allOptionsArr[p] != "") {
                    imageArray.push(imagePath + "" + allOptionsArr[p]);
                } else {
                    alert("No image file is found in option " + p);
                }
            }
        }*/

       /* if (imageArray.length > 0) {
            Helpers.preloadImages(imageArray, this.preloadSounds);
        } else {
            this.preloadSounds();
        }*/
        
        if (optionSubType == "text") 
        {
            for (var p = 0; p < totalOptions; p++) {
                if (qType == "multiCorrect") {
                    var htm = '';
                    htm += '<div class="row qtypeTopBuffer">';
                        htm += '<div class="qtypeClLabelCheckBoxOpt  options col-md-2 col-lg-1" id="qtypeClLabelCheckBoxOptmcq1'+p+'">';
                            htm += '<div class="row">';
                                htm += '<div class="col-md-6">';
                                    var str = allOptionsArr[p].replace(/"/g, '&quot;');
                                    htm += '<input type="checkbox" name="opt_CB" id=cb_Id' + p + ' class="optCbox" value="' + str + '">';
                                    htm += '<label for="cb_Id' + p + '" class="check_option"><i for class="fa fa-square-o"></i></label>';
                                htm += '</div>';
                                htm += '<div class="col-md-6">';
                                    htm += '<div class="options" id=labId1' + p + '>' + optLabelArr[p];
                                    htm += '</div>';
                                htm += '</div>';
                            htm += '</div>';
                        htm += '</div>';
                        htm += '<div class="col-md-9 col-lg-10 qtypeClOptDiv qtypeNoLpadding qtypeClOptSndIcon options" id=optId' + p + '>' + allOptionsArr[p] + '</div>';
                    htm += '</div>';
                    $("#qtypeContainerOpt1", this.container).append(htm);
                } else {
                    var htm = '';
                    htm += '<div class="row qtypeTopBuffer">';
                        htm += '<div class="row qtypeTopBuffer">';
                            htm += '<div class="col-md-1 col-lg-1 col-sm-1">';
                                htm += '<div style="max-height:none;" class="qtypeClLabelOpt qtypeClickObjCl options" id=labId1' + p + '>' + optLabelArr[p];
                            htm += '</div>';
                        htm += '</div>';
                        htm += '<div class="col-md-10 col-lg-10 col-sm-10 qtypeClOptDiv qtypeNoLpadding qtypeClOptSndIcon options" id=optId' + p + '>' + allOptionsArr[p] + '</div>';
                    htm += '</div>';
                    
                    //$("#qtypeContainerOpt").append(htm);
                    $("#qtypeContainerOpt1").append(htm);
                    //$("#qtypeContainerOpt", this.container).append('<div class="row qtypeTopBuffer"><div class="col-md-12"><div class="qtypeClLabelOpt qtypeClickObjCl options" id=labId1' + p + '>' + optLabelArr[p] + '</div> <div class="qtypeClOptDiv qtypeNoLpadding qtypeClOptSndIcon options" id=optId' + p + '>' + refQuesViewObj.allOptionsArr[p] + '</div></div></div>');
                }
            }
        }
        else if (optionSubType == "image") 
        {
            var tmpColMiddle = "";
            var tmpRowS = '<div class="row qtypeTopBuffer">';
            var tmpRowE = '</div>';
            for (var p = 0; p < totalOptions; p++) 
            {
                if (qType == "multiCorrect") {
                    tmpColMiddle += '<div class="col-md-4 col-lg-4">';
                        tmpColMiddle += '<div class="row">';
                            // Create the option and the checkbox.
                            tmpColMiddle += '<div class="col-md-4">';
                                tmpColMiddle += '<div class="row qtypeClLabelCheckBoxOpt" id="qtypeClLabelCheckBoxOptmcq1'+p+'">';
                                    tmpColMiddle += '<div class="col-md-6">';
                                        tmpColMiddle += '<input type="checkbox" name="opt_CB" id=cb_Id' + p + ' class="optCbox" value="' + allOptionsArr[p] + '">';
                                        tmpColMiddle += '<label for="cb_Id' + p + '" class="check_option"><i for class="fa fa-square-o"></i></label>';
                                    tmpColMiddle += '</div>';
                                    tmpColMiddle += '<div class="col-md-6" id=labId1' + p + '>' + optLabelArr[p];
                                    tmpColMiddle += '</div>';
                                tmpColMiddle += '</div>';
                            tmpColMiddle += '</div>';

                            // Show the option content
                            tmpColMiddle += '<div class="col-md-8">';
                                tmpColMiddle += '<div class="qtypeClOptDiv qtypeClOptSndIcon" id=optId' + p + '>';
                                    tmpColMiddle += '<img class="img-responsive" src="' + imagePath + allOptionsArr[p] + '"/>';
                                tmpColMiddle += '</div>';
                            tmpColMiddle += '</div>';
                        tmpColMiddle +='</div>';
                    tmpColMiddle += '</div>';
                } else {
                    tmpColMiddle += '<div class="col-md-4 col-lg-4">';
                        tmpColMiddle += '<div class="row">';
                            // Create the option and the checkbox.
                            tmpColMiddle += '<div class="col-md-2">';
                                tmpColMiddle += '<div class="row">';
                                    tmpColMiddle += '<div style="max-height:none !important;" class="qtypeClLabelOpt qtypeClickObjCl" id=labId1' + p + '>' + optLabelArr[p];
                                    tmpColMiddle += '</div>';
                                tmpColMiddle += '</div>';
                            tmpColMiddle += '</div>';

                            // Show the option content
                            tmpColMiddle += '<div class="col-md-10">';
                                tmpColMiddle += '<div class="qtypeClOptDiv qtypeNoLpadding qtypeClOptSndIcon" id=optId' + p + '>';
                                    tmpColMiddle += '<img class="img-responsive" src="' + imagePath + allOptionsArr[p] + '"/>';
                                tmpColMiddle += '</div>';
                            tmpColMiddle += '</div>';
                        tmpColMiddle +='</div>';
                    tmpColMiddle += '</div>';
                }
                if (totalOptions == 2||totalOptions == 4) {                     
                    if (p == 1 || p == 3) {
                        $("#qtypeContainerOpt1", this.container).append(tmpRowS + tmpColMiddle + tmpRowE);
                        tmpColMiddle = "";
                    }
                }else if (totalOptions == 6){
                     if (p == 2 ||p == 5) 
                     {
                        $("#qtypeContainerOpt1", this.container).append(tmpRowS + tmpColMiddle + tmpRowE);
                        tmpColMiddle = "";
                     }
                } else {                        
                    if (p == 2 || p == 4) {
                        $("#qtypeContainerOpt1", this.container).append(tmpRowS + tmpColMiddle + tmpRowE);
                        tmpColMiddle = "";
                    }
                }
            }
        }
        else if (optionSubType == "audioicon") 
        {
            var tmpColMiddle = "";
            var tmpRowS = '<div class="row qtypeTopBuffer">';
            var tmpRowE = '</div>';
            var iconImage = assetsPath + 'sndIconOpt.png';
            for (var p = 0; p < totalOptions; p++) {
                if (qType == "multiCorrect") {
                    tmpColMiddle += '<div class="col-md-4 col-lg-4">';
                        tmpColMiddle += '<div class="row">';
                            // Create the option and the checkbox.
                            tmpColMiddle += '<div class="col-md-4">';
                                tmpColMiddle += '<div class="row qtypeClLabelCheckBoxOpt audio_option" id="qtypeClLabelCheckBoxOptmcq1'+p+'">';
                                    tmpColMiddle += '<div class="col-md-6">';
                                        tmpColMiddle += '<input type="checkbox" name="opt_CB" id=cb_Id' + p + ' class="optCbox" value="' + allOptionsArr[p] + '">';
                                        tmpColMiddle += '<label for="cb_Id' + p + '" class="check_option"><i for class="fa fa-square-o"></i></label>';
                                    tmpColMiddle += '</div>';
                                    tmpColMiddle += '<div class="col-md-6" id=labId1' + p + '>' + optLabelArr[p];
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
                            tmpColMiddle += '<div class="col-md-2">';
                                tmpColMiddle += '<div class="row audio_option">';
                                    tmpColMiddle += '<div style="max-height:none !important;" class="qtypeClLabelOpt qtypeClickObjCl" id=labId1' + p + '>' + optLabelArr[p];
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
                    //tmpColMiddle += '<div class="col-sm-1"><div class="qtypeClLabelOpt qtypeClickObjCl" id=labId1' + p + '>' + optLabelArr[p] + '</div></div><div class="qtypeClOptDiv qtypeNoLpadding col-sm-2 qtypeClOptSndIcon" id=optId' + p + '><img class="img-responsive" src="' + iconImage + '"/></div>');

                }
                if (totalOptions == 2||totalOptions == 4) {                     
                    if (p == 1 || p == 3) {
                        $("#qtypeContainerOpt1", this.container).append(tmpRowS + tmpColMiddle + tmpRowE);
                        tmpColMiddle = "";
                    }
                }else if (totalOptions == 6){
                     if (p == 2 ||p == 5) 
                     {
                        $("#qtypeContainerOpt1", this.container).append(tmpRowS + tmpColMiddle + tmpRowE);
                        tmpColMiddle = "";
                     }
                } else {                        
                    if (p == 2 || p == 4) {
                        $("#qtypeContainerOpt1", this.container).append(tmpRowS + tmpColMiddle + tmpRowE);
                        tmpColMiddle = "";
                    }
                }
            }
        } 

        for (var p = 0; p < totalOptions; p++) 
        {
            //user response
            if (userResponse != "") {
                
                if(qType == 'multiCorrect')
                {
                    //for ( j = 0; j < arrCorrectAnswer.length; j++) {
                    for ( j = 0; j < newArray.length; j++) {
                        if (optLabelForCorrectAns[p] == newArray[j] && jQuery.inArray(newArray[j], arrCorrectAnswer) !== -1) {
                                $(("#labId1" + p), this.container).append('<div class="signCls" id="signC1' + p + '"><img src="' + assetsPath + 'greenCmark.png"/></div>');
                                $(".signCls img").height("35px");
                                $(".signCls img").width("35px");
                                //$($("#qtypeClLabelCheckBoxOptmcq1")[p]).addClass("bgcolorGreen");
                                $("#qtypeClLabelCheckBoxOptmcq1"+[p]).addClass("bgcolorGreen");
                                $("#signC1" + p).css({
                                    'position' : 'absolute',
                                    'left' : '-10px',
                                    'top' : '0px'
                                });
                                // Change is done to count the number of correct option selected.
                        }
                        else
                        {
                            
                            if(optLabelForCorrectAns[p] == newArray[j])
                            {
                                $(("#labId1" + p), this.container).append('<div class="signCls" id="signC1' + p + '"><img src="' + assetsPath + 'wrongCmark.png"/></div>');
                                $(".signCls img").height("35px");
                                $(".signCls img").width("35px");
                                $("#qtypeClLabelCheckBoxOptmcq1"+[p]).addClass("bgcolorRed");
                                $("#signC1" + p).css({
                                    'position' : 'absolute',
                                    'left' : '-10px',
                                    'top' : '0px'
                                });
                                //$(".qtypeClLabelCheckBoxOpt").addClass("bgcolorRed");
                            }
                            else if(optLabelForCorrectAns[p] == arrCorrectAnswer[j]  && $.inArray(arrCorrectAnswer[j], newArray) == -1)
                            {
                                $(("#labId1" + p), this.container).append('<div class="signCls" id="signC1' + p + '"><img src="' + assetsPath + 'blueCmark.png"/></div>');
                                $(".signCls img").height("35px");
                                $(".signCls img").width("35px");
                                $("#qtypeClLabelCheckBoxOptmcq1"+[p]).addClass("bgcolorBlue");
                                $("#signC1" + p).css({
                                    'position' : 'absolute',
                                    'left' : '-10px',
                                    'top' : '0px'
                                });
                                //$(".qtypeClLabelCheckBoxOpt").addClass("bgcolorBlue");
                            }
                        }
                    }
                }
                else
                {
                    if (userResponse == optLabelForCorrectAns[p] || correctAnswer == optLabelForCorrectAns[p]) 
                    {
                        if (userResponse == correctAnswer) {
                            $(("#labId1" + p), this.container).append('<div class="signCls" id="signC12"><img src="' + assetsPath + 'greenCmark.png"/></div>');
                            $("#signC12 img").height("35px");
                            $("#signC12 img").width("35px");
                            $("#labId1" + p).addClass("bgcolorGreen");
                            $("#signC12").css({
                                'position' : 'absolute',
                                'left' : '33px',
                                'top' : '0px'
                            });
                        } 
                        else 
                        {
                            if(userResponse == optLabelForCorrectAns[p])
                            {
                                $(("#labId1" + p), this.container).append('<div class="signCls" id="signC23"><img src="' + assetsPath + 'wrongCmark.png"/></div>');

                                $("#signC23 img").height("35px");
                                $("#signC23 img").width("35px");
                                $("#labId1" + p).addClass("bgcolorRed");
                                $("#signC23").css({
                                    'position' : 'absolute',
                                    'left' : '33px',
                                    'top' : '0px'
                                });
                            }
                            
                            if (correctAnswer == optLabelForCorrectAns[p]) {
                                $(("#labId1" + p), this.container).append('<div class="signCls" id="signC34"><img src="' + assetsPath + 'blueCmark.png"/></div>');
                                $("#signC34 img").height("35px");
                                $("#signC34 img").width("35px");
                                $("#labId1" + p).addClass("bgcolorBlue");
                                $("#signC34").css({
                                    'position' : 'absolute',
                                    'left' : '33px',
                                    'top' : '0px'
                                });

                            }
                        }
                    }
                }
            } 
            else {
                if (correctAnswer == optLabelForCorrectAns[p]) {
                    $(("#labId1" + p), this.container).append('<div class="signCls" id="signC34"><img src="' + assetsPath + 'blueCmark.png"/></div>');
                    $("#signC34 img").height("35px");
                    $("#signC34 img").width("35px");
                    $("#labId1" + p).addClass("bgcolorBlue");
                    $("#signC34").css({
                        'position' : 'absolute',
                        'left' : '33px',
                        'top' : '0px'
                    });

                }
            }
        }
        this.quesDivBin = $("#quesDiv1", this.container);
        this.quesImgBin = $("#imgDiv1", this.container);
        this.quesDivBin = $("#quesDiv1", this.container);
        this.quesSndIconBin = $("#sndIcon", this.container);
        quesDivBin.html("");
        quesDivBin.html(quesText);

        
        if (quesSubType == "text" || quesSubType == "textAndaudioicon" || quesSubType == "audioicon") 
        {
            
            if (quesSubType != "audioicon") 
            {
                //refQuesViewObj.quesDivBin.html(refQuesViewObj.quesDataArr.quesText);
                quesDivBin.html("");
                quesDivBin.html(quesText);
            }

            if (quesSubType == "textAndaudioicon" || quesSubType == "audioicon") {

                quesSndIconBin.html('<img src="' + assetsPath + 'soundIconGrey.png"/>');
            }
        } 
        else if (quesSubType == "textAndimage") {
            
            quesDivBin.html("");
            quesDivBin.html(quesText);
            quesImgBin.css('display', '');
            quesImgBin.html('<img class="img-responsive" id=imgId src="' + imagePath + quesImgText + '"/>');

        } 
        else if (quesSubType == "image" || quesSubType == "imageAndaudioicon") 
        {
            quesImgBin.css('display', '');
            quesImgBin.html('<img class="img-responsive" id=imgId src="' + imagePath + quesImgText + '"/>');

            if (quesSubType == "imageAndaudioicon") {
                quesSndIconBin.html('<img src="' + assetsPath + 'soundIconGrey.png"/>');
            }
            quesImgBin.height("auto");
            quesImgBin.width("140px");


        }
        //html here

        $( "img" )
              .error(function() {
                console.log('m here');
                 imgNotLoading();   
            });
        
        if(explanation.trim() !== '')
        {
            this.quesExpBin = $("#explantionAreaMcq", this.container);
            quesExpBin.html("");
            var htmlExp = "<span class='explanationReport'> Explanation - </span>";
            htmlExp += "<span class='expText'>"+explanation+"</span>";
            quesExpBin.html(htmlExp);
        }

       
        /*$('#modalBlocker').show();
        $("#lookbackContentQues").show();*/
    }, 1000);
    
}


function showQuesContentBlank(qcode)
{

    setFlagBlankMcq = true;
    var qType              = blank[qcode].qType;
    var quesText           = blank[qcode].quesText;
    var quesAudioIconSound = blank[qcode].quesAudioIconSound;
    var userResponse       = blank[qcode].userResponse;
    var qcode              = blank[qcode].qcode;
    var explanation        = blank[qcode].explanation; 
    var correctResponse    = blank[qcode].correctResponse; 
    var correct            = blank[qcode].correct; 
    var queParams          = blank[qcode].queParams; //can be array
    var paramname          = blank[qcode].paramname; //can be array
    var paramTotal         = blank[qcode].paramTotal;
    var actualcorrAns      = blank[qcode].actualcorrAns
    var actualUserResponse  = blank[qcode].actualUserResponse;
    
    setTimeout(function(){

        var modal                       = document.getElementById('myModalSessionReport');
        modal.style.display             = "block";

        /*var lookbackContainerQues = document.createElement('div');
        lookbackContainerQues.className = 'lookbackContainerQues';
        var close_button = '<div class="prompt-heading"><button onclick="closeMcq()"  class="close-prompt toast-close" prompt-close=\'#lookbackContainerQues\'><i class="fa fa-close"></i></button> </div>';*/

        if(explanation != '')
        {
            /*$("#explantionAreaBlank").html("");
            var htmlExp = "<span class='explanationReport'> Explanation - </span>";
            htmlExp += "<span class='expText'>"+explanation+"</span>";
            $("#explantionAreaBlank").html(htmlExp);*/
            var explaHtml = '<div id="explantionAreaBlank"><span class="explanationReport"> Explanation - </span><span class="expText">'+explanation+'</span><div>';
        }
        else
            var explaHtml = '';

        

        //var elements = queParams.split(';');
        var elements = correctResponse.split('|');
       // console.log(elements.length);
        
        this.rightAnswer = elements[0];
        var rightAnswer = elements[0];

        var correctAnswer = elements;
        //var userResponse = userResponse.split('|');
        
        
        
        if(correct < 1 && qType != 'openEnded')
        {
            var showansHtml = '<div id="showAnsBlank" class="showAns"><img class="profile-icon-img" src="theme/img/Language/showans.png"></div>';
        }
        else
            var showansHtml = '';

        
        //lookbackContainerQues.innerHTML =  close_button;
        
        var text = quesText;
        
        var html2;
        var html3;
        var attempt_audio = Helpers.createElement('audio', {
                id : "attempt_audio",
                html : '<source src="" type="audio/mp3" /><source src="" type="audio/ogg"/>'
            });

        if (quesAudioIconSound != '') {
            html2 = Helpers.createElement('div', {
                className : 'blankAudioIcon',
                click : function() {
                    Helpers.sndPlayLoadErrEnd(Helpers.constants.LIVE_CONTENT_PATH + "templates_qtype/sounds/", "allAudio", quesAudioIconSound);
                }
            });
            html3 = Helpers.createElement('audio', {
                id : "allAudio",
                html : '<source src="" type="audio/mp3" /><source src="" type="audio/ogg"/>'
            });
        };

        //replace blank with inputs       
        //text = text.replace(/\[(Blank_\d+)\]/gi, '<input id="'+qcode+'" value="'+userResponse+'" disabled="true" type="text">');
        //replace dropDowns with select tags
        //text = text.replace(/\[(Dropdown_\d+)\]/gi, '<select id="'+qcode+'"></select>');
       

        //replace blank with inputs       
        text = text.replace(/\[(Blank_\d+)\]/gi, '<input id="$1_'+qcode+'" blank-type="blank"  type="text" disabled="true">');
        //replace dropDowns with select tags
        text = text.replace(/\[(Dropdown_\d+)\]/gi, '<select id="$1_'+qcode+'"  blank-type="dropdown"></select>');

        if(qType == 'openEnded')
        {
            //add textArea for openended questions
            text += '<div class="col-md-10 text-center"><textarea class="form-control blank-textarea" spellcheck="false" rows="3" cols="50" maxlength="1000" id="$1_'+qcode+'" blank-type="blank-text"  disabled="true">'+userResponse+'</textarea></div>';
            selectid = qcode;
        }
        if(qType == "spelling")
        {
            text = '<input id="Blank_1_'+qcode+'"  blank-type="blank" disabled="true" type="text">';
        }
        
        var blankContainer = Helpers.createElement('div', {
            className : 'blankContainer qtypeClQuesDiv',
            html : text,
        });
        
        $(blankContainer).prepend(attempt_audio);
        $(blankContainer).prepend(html3);
        $(blankContainer).prepend(html2);
        if (quesText != '') {
            // special handling for spelling qtype
            if(qType == "spelling") {
                var title = quesText.replace(/\[(.*?)\]/g,"");
                $(blankContainer).prepend('<div style="position: relative; height: auto; width: 100%">' + title + '</div>');
            }
        }
        if (qType == 'spelling') {
            $('input', blankContainer).addClass('specialInput');
        }
        // quesAudioIconSound = 'correct1.mp3'
        //var that = view;
        //selectid = 1799;
        selects = $('select', blankContainer);
        
        var rightAnswerDrop = '';
        var rightAnswerBlank = '';
        selects.each(function(element) {
            
            //elements = queParams.split(';');
            
            elements = paramTotal[this.id].split(';');
            rightAnswerDrop = elements[0];
            this.rightAnswer = elements[0];
            //Helpers.populateSelectElement(this, Helpers.shuffleArray(elements));
            Helpers.populateSelectElement(this, elements);
            
            selectid = this.id;

            $(this).attr('disabled','disabled');
            var element1 = $(this).find("option[value='"+ actualUserResponse[this.id] +"']");
            element1.attr('selected','selected');

            if (actualUserResponse[this.id] != rightAnswerDrop) {
                $(this).addClass('wrong');
            } 
            else 
            {
                $(this).addClass('right');
            }
            
        });
        
        /*if (Helpers.getSelectedOption(this).value != this.rightAnswer) {
            $(this).addClass('wrong');
        } 
        else 
        {
            $(this).addClass('right');
            // We do not evaluate these type of question.
            //score++;
        }*/

        inputs = $('input', blankContainer);
        inputs.each(function(element) {
            var tmpArr=[];
            
            selectid = this.id;
            //$.each(queParams.split(';'), function(){
                
            
            $.each(paramTotal[this.id].split(';'), function(){
                tmpArr.push($.trim(this));
            });            
            rightAnswerBlank = tmpArr.join("|");
            this.rightAnswer = tmpArr.join("|");
            $(this).attr('value',actualUserResponse[this.id]);
            
            //if(actualUserResponse[this.id] != rightAnswerBlank) 
            if(actualUserResponse[this.id].toLowerCase() != actualcorrAns[this.id].toLowerCase()) 
            {
                
                $(this).addClass('wrong');
            } 
            else 
            {
                $(this).addClass('right');
            }
             
           //this.rightAnswer = that.quesDataArr.queParams[this.id].replace(/;/g, '|');
           
        });
        //return;
        
        

        textarea = $('textarea', blankContainer);
        //$(view.container).html(blankContainer);
        $("#questionContainer").html(blankContainer);

        //for input
       
        /*for (var i = 0; i < userResponse.length; i++) 
        {
            var j= i + 1;
            var indexStr = "#Blank_"+j+"_"+qcode;
            $("#Blank_"+j+"_"+qcode).attr('value', actualUserResponse[indexStr].toLowerCase());
        }*/

        var quesContainer  = $("#questionContainer").html();
        $("#sessionReportQues").html(quesContainer+showansHtml+explaHtml);

        /*lookbackContainerQues.innerHTML =  close_button+quesContainer+showansHtml+explaHtml;
        $("#lookbackContentQues").html(lookbackContainerQues);*/

        
        //$('#modalBlocker').show();

        if(userResponse != '')
        {
            // console.log(queParams);
            // if(queParams != undefined && queParams != '' && queParams != 'null')
            // {
            //     var elements = queParams.split(';');
            //     this.rightAnswer = elements[0];
            // }

            /*for (var i = 0; i < userResponse.length; i++) 
            {
                var j = i + 1;
                $("#Dropdown_"+j+'_'+qcode+" > option").each(function() {
                    //$("#Dropdown_"+j+'_'+qcode).attr('disabled', 'disabled');

                    if (userResponse[i].toLowerCase() != rightAnswerDrop) {
                        $("#Dropdown_"+j+'_'+qcode).addClass('wrong');
                    } 
                    else 
                    {
                        $("#Dropdown_"+j+'_'+qcode).addClass('right');
                    }

                    var element = $("#Dropdown_"+j+'_'+qcode).find("option[value='"+ userResponse[i].toLowerCase() +"']");
                    element.attr('selected','selected');
                }); 
                
                if(userResponse[i].toLowerCase() != rightAnswerBlank) 
                {
                    
                    $("#Blank_"+j+"_"+qcode).addClass('wrong');
                } 
                else 
                {
                    $("#Blank_"+j+"_"+qcode).addClass('right');
                } 
            }*/
            

            if(qType == 'openEnded') 
            {
                //$('#'+selectid).removeClass('wrong');
                $("#Blank_1_"+qcode).removeClass('wrong');
                $("#explantionAreaBlank").addClass('open-margin');
            }
            else 
                $("#explantionAreaBlank").removeClass('open-margin');   

            /*var quesContainer  = $("#questionContainer").html();
            lookbackContainerQues.innerHTML =  close_button+quesContainer+showansHtml+explaHtml;
            $("#lookbackContentQues").html(''); 
            $("#lookbackContentQues").html(lookbackContainerQues);*/
        }
        //$("#lookbackContentQues").show();
        $('#showAnsBlank').on('click', function(e){

            /*$("#lookbackContentQues").hide();
            $("#lookbackContentQues").html('');*/

            $("#sessionReportQues").hide();

            $('.wrong').each(function() {
                var j = 0;
                var k = j + 1;
                var attrValue = $(this).attr('blank-type');
                
                if(attrValue == 'dropdown')
                {
                    var elementID = $(this).attr('id');

                    var elementUser = $(this).find("option[value='"+ actualUserResponse[elementID] +"']");
                    elementUser.removeAttr('selected','selected');

                    var element = $(this).find("option[value='" + actualcorrAns[elementID] +"']");
                    element.attr('selected','selected');
                }
                else if(attrValue == 'blank')
                {
                    var elementID = $(this).attr('id');
                    
                    $(this).attr('value', actualcorrAns[elementID]);
                }
                else if(attrValue == 'blank-text')
                {
                    var elementID = $(this).attr('id');
                    
                    //$(this).attr('value', actualcorrAns[elementID]);
                    $(this).text(actualcorrAns[elementID]);
                }
                $(this).addClass('corrected');
                j++;
            });

            /*var quesContainer  = $("#questionContainer").html();
            lookbackContainerQues.innerHTML =  close_button+quesContainer+showansHtml+explaHtml;

            $("#lookbackContentQues").html(lookbackContainerQues);
            $("#lookbackContentQues").show();*/
            $("#sessionReportQues").show();
        });
    }, 1000);
    
}



//function showQuesSeq1(totalOptions,quesSubType,optSubType,explanation,userResponse,option_a, option_b, option_c, option_d, option_e, option_f,correct,quesType,quesId,sound_a, sound_b, sound_c, sound_d, sound_e, sound_f)
function showQuesSeq1(qcode) //current
{

    sessionData.qID = qcode;
    setFlag = true;
    var totalOptions = sequence1[qcode].totalOptions;
    var quesSubType  = sequence1[qcode].quesSubType;
    var optSubType   = sequence1[qcode].optSubType;
    var explanation  = sequence1[qcode].explanation;
    var userResponse = sequence1[qcode].userResponse;
    /*var option_a     = sequence1[qcode].option_a.replace(/<br\s*[\/]?>/gi, "");
    var  option_b    = sequence1[qcode].option_b.replace(/<br\s*[\/]?>/gi, "");
    var  option_c    = sequence1[qcode].option_c.replace(/<br\s*[\/]?>/gi, "");
    var  option_d    = sequence1[qcode].option_d.replace(/<br\s*[\/]?>/gi, "");
    var  option_e    = sequence1[qcode].option_e.replace(/<br\s*[\/]?>/gi, "");
    var  option_f    = sequence1[qcode].option_f.replace(/<br\s*[\/]?>/gi, "");*/
    var option_a     = sequence1[qcode].option_a;
    var option_a1 = option_a.replace(/<br\s*\/?>/gi,'');

    var  option_b    = sequence1[qcode].option_b;
    var option_b1 = option_b.replace(/<br\s*\/?>/gi,'');

    var  option_c    = sequence1[qcode].option_c;
    var option_c1 = option_c.replace(/<br\s*\/?>/gi,'');

    var  option_d    = sequence1[qcode].option_d;
    var option_d1 = option_d.replace(/<br\s*\/?>/gi,'');

    var  option_e    = sequence1[qcode].option_e;
    var option_e1 = option_e.replace(/<br\s*\/?>/gi,'');

    var  option_f    = sequence1[qcode].option_f;
    var option_f1 = option_f.replace(/<br\s*\/?>/gi,'');

    var  correct     = sequence1[qcode].correct;
    var quesType     = sequence1[qcode].quesType;
    var quesId       = sequence1[qcode].quesId;
    var sound_a      = sequence1[qcode].sound_a;
    var  sound_b     = sequence1[qcode].sound_b;
    var  sound_c     = sequence1[qcode].sound_c;
    var  sound_d     = sequence1[qcode].sound_d;
    var  sound_e     = sequence1[qcode].sound_e;
    var  sound_f     = sequence1[qcode].sound_f;

    var assetsPath = Helpers.constants.THEME_PATH + "img/Language/templates/";
    var imagePath = Helpers.constants.LIVE_CONTENT_PATH + 'templates_qtype/images/';
    var allSndArr = [];
    

    var correctAnswer1 = [];
    var soundArr      = [];
    var userAns1       = [];
    var userlist      = [];
    var userlist2     = [];
    var correctlist   = [];
    var correctlist2  = [];

    correctAnswer1 = [option_a1, option_b1, option_c1, option_d1, option_e1, option_f1];
    var correctAnswer = correctAnswer1.filter(function(v){return v!==''});

    soundArr      = [sound_a, sound_b, sound_c, sound_d, sound_e, sound_f];

    var userResponseArr = userResponse.split('|');
    if(userResponseArr[0] != undefined)
        var userResponse1 = userResponseArr[0].replace(/<br\s*[\/]?>/gi, "");
    else
        var userResponse1 = '';

    if(userResponseArr[1] != undefined)
        var userResponse2 = userResponseArr[1].replace(/<br\s*[\/]?>/gi, "");
    else
        var userResponse2 = '';

    if(userResponseArr[2] != undefined)
        var userResponse3 = userResponseArr[2].replace(/<br\s*[\/]?>/gi, "");
    else
        var userResponse3 = '';

    if(userResponseArr[3] != undefined)
        var userResponse4 = userResponseArr[3].replace(/<br\s*[\/]?>/gi, "");
    else
        var userResponse4 = '';

    if(userResponseArr[4] != undefined)
        var userResponse5 = userResponseArr[4].replace(/<br\s*[\/]?>/gi, "");
    else
        var userResponse5 = '';

    if(userResponseArr[5] != undefined)
        var userResponse6 = userResponseArr[5].replace(/<br\s*[\/]?>/gi, "");
    else
        var userResponse6 = '';

    userAns1         = [userResponse1, userResponse2, userResponse3, userResponse4, userResponse5, userResponse6];
    var userAns = userAns1.filter(function(v){return v!==''});
    
    var optionsColorArr = ["hover1","hover2","hover3","hover4","hover5","hover6"];

    var maxHeight = 0;
    var leftSubType = optSubType.split("And")[0];
    if(leftSubType)
        leftSubType = leftSubType.toLowerCase();
    
    var rightSubType = optSubType.split("And")[1];
    if(rightSubType)
        rightSubType = rightSubType.toLowerCase();
    
    var quesLeftSubType = quesSubType.split("And")[0];
    if(quesLeftSubType)
        quesLeftSubType = quesLeftSubType.toLowerCase();
    
    var quesRightSubType = quesSubType.split("And")[1];
    if(quesRightSubType)
        quesRightSubType = quesRightSubType.toLowerCase();

    console.log(sequence1);
    var questions={qType: quesType, qID: quesId}; //change this to dynamic when submit
    setQuestion(questions);
    console.log(sequence1);
    
    setTimeout(function(){

        var maxHeight                   = 0;
        var modal                       = document.getElementById('myModalSessionReport');
        modal.style.display             = "block";

        //var close_button = '<div class="prompt-heading"><button class="close-prompt toast-close" prompt-close=\'#lookbackContainerQues\'><i class="fa fa-close"></i></button> </div>';
        //var close_button = '<input type="button" onclick="closeModalSession()" class="form-control btn btn-sm session-btn-cls" value="x">';

        var close_button = '';
        
        var quesHtml = sessionData.quesSeqHtml;
        var bodyHtml = '<div id="subContainerQuestion" class="container-fluid qtypeTopBuffer"><div id="sequenceContainer">'+quesHtml+'</div></div>';

        
        if(correct < 1)
        {
           
            var rightAns = '<div id="showAns" class="showAns"><img class="profile-icon-img" src="theme/img/Language/showans.png"></div>';    
        }
        else
        {
            var rightAns = '';
        }
        
        if(explanation != '')
        {
            var explaHtml = '<div id="explantionAreaSeq"><span class="explanationReport"> Explanation - </span><span class="expText">'+explanation+'</span><div>';
        }
        else
            var explaHtml = '';
        $("#sessionReportQues").html(close_button+bodyHtml+rightAns+explaHtml);
        
        for (var i = 0; i < totalOptions; i++) 
        {
            //making list with user answer
            userlist[i] ='<div class="ui-state-default questions numbers '+optionsColorArr[i]+'" id="quesId'+i+'" data_sound="'+soundArr[i]+'">'+userAns[i]+""+'</div><div class="hiddenQuestions" id="special'+i+'" ></div>';
           

            if(correct < 1)
            {
                //making list with correct answer
                correctlist[i] ='<div class="ui-state-default questions numbers '+optionsColorArr[i]+'" id="quesId'+i+'" data_sound="'+soundArr[i]+'">'+correctAnswer[i]+""+'</div><div class="hiddenQuestions" id="special'+i+'" ></div>';   
            }
        }
        
        $( "#sequenceContainer #sortable" ).html('');
        $( "#sequenceContainer #sortable" ).html(userlist);
        
        for(i=0; i<totalOptions; i++) {
            var optionText = $("#sequenceContainer #quesId"+i).html();
            
            if(leftSubType == "text" || rightSubType == "text") {    // Handling text in options
                if(maxHeight < $("#sequenceContainer #quesId"+i).outerHeight()) 
                    maxHeight = $("#sequenceContainer #quesId"+i).outerHeight()+10;
            }
            if(leftSubType == "image" || rightSubType == "image") {
                if(maxHeight < 120)
                    maxHeight = 120;

                if(leftSubType == "image") {
                    /*var imgName = optionText.split(":")[1];
                    var txtName = optionText.split(":")[0];*/
                    var imgName = optionText.split(":")[0];
                    var txtName = optionText.split(":")[1];
                }
                else {
                    /*var imgName = optionText.split(":")[0];
                    var txtName = optionText.split(":")[1];*/
                    var imgName = optionText.split(":")[0];
                    var txtName = optionText.split(":")[1];
                }

                if(leftSubType == "text" || rightSubType == "text") {
                    // mixed version
                    $("#sequenceContainer #quesId"+i).html("<img src="+imagePath+imgName+" /><text>"+txtName+"</text>");
                    $("#sequenceContainer #ansId"+i).html("<img src="+imagePath+imgName+" /><text>"+txtName+"</text>");
                }
                else {
                    // pure image
                    $("#sequenceContainer #quesId"+i).html("<img src="+imagePath+imgName+" style='margin-left: 40%'/>");
                    $("#sequenceContainer #ansId"+i).html("<img src="+imagePath+imgName+" style='margin-left: 40%'/>");
                }
                $("#sequenceContainer #quesId"+i).attr("data_image",imgName);
                $("#sequenceContainer #ansId"+i).attr("data_image",imgName);
            }
            if(leftSubType == "audioicon" || rightSubType == "audioicon") {

                
                if(maxHeight < 110)
                    maxHeight = 110;
                var audioName = $("#sequenceContainer #quesId"+i).attr("data_sound");
                allSndArr.push(audioName);
                var imgName = '<img src="' + assetsPath + 'soundIconGrey.png" class="audioOptions"/>';
                $("#sequenceContainer #quesId"+i).html(imgName);
                $("#sequenceContainer #ansId"+i).html(imgName);
            } //will come after html
        }
        
        for(var i=0; i<totalOptions; i++) {
            $("#sequenceContainer #ansId"+i).css({
                "top": $("#sequenceContainer #quesId"+i).position().top+"px",
                "display":"none"
            });
            $($(".answers")[i]).attr("data-pos",i);
            $($(".questions")[i]).attr("data-pos",i);
        }
        if(leftSubType == "audioicon" || rightSubType == "audioicon") {
            for (var i = 0; i < totalOptions; i++) {
                $($('.questions')[i]).append('<div class="labels rightLabels">'+String.fromCharCode(65+i)+'</div>');
                var id = $($('.questions')[i]).attr("id");

                $("#sequenceContainer #ansId"+id.substr(6,1)).append('<div class="labels rightLabels">'+String.fromCharCode(65+i)+'</div>');
            } //will come after html
        }

        setHeight(maxHeight,totalOptions); //will come after html
        //setBindingsMatch(); //will come after html
       
        for (var i=0; i<totalOptions; i++) {
            
            if(userAns[i].replace(/&nbsp;/g,"").trim() == correctAnswer[i].replace(/&nbsp;/g,"").trim()) 
            {
                
                $("#sequenceContainer #correctWrongSign").append('<div class="signCls" id="signC1' + i + '"><img src="'+ Helpers.constants.THEME_PATH +'img/Language/match/greenCmark.png"/></div>');
                signMarkDisplay($('[data-pos="' + i + '"]'), ("signC1" + i), 620, 18);
            }
            else {

                $("#sequenceContainer #correctWrongSign").append('<div class="signClsW" id="signC2' + i + '"><img src="'+ Helpers.constants.THEME_PATH +'img/Language/match/wrongCmark.png"/></div>');
                signMarkDisplay($('[data-pos="' + i + '"]'), ("signC2" + i), 620, 18);
            }
        }


        

        if(explanation != '')
        {
            var toTopExp                                                = $("#explantionAreaSeq").position().top;
            var topPostitionCountGet                                    = totalOptions - 1;
            var topPostition                                            = $("#quesId"+topPostitionCountGet).position().top;
            document.getElementById('explantionAreaSeq').style.top      = (toTopExp + topPostition) + "px";
            document.getElementById('explantionAreaSeq').style.position = "relative";
            document.getElementById('explantionAreaSeq').style.width = "80%";
        }

        $('#showAns').on('click', function(e){

            $("#sessionReportQues").hide();

            //var correctWrongSignHtml  = '<div id="correctWrongSign"> </div>';
            //var sortable = '<div id="sortable" class="ui-sortable" style="height: 558px;">'+correctlist+'</div>'
            
            //$( "#sequenceContainer" ).html(titleHtml+sortable+correctWrongSignHtml);

            $("#lookbackContentQues").html(''); 
            $( "#sequenceContainer #sortable" ).html('');
            $( "#sequenceContainer #sortable" ).html(correctlist);

            $( "#sequenceContainer #correctWrongSign" ).html('');


            for (var i=0; i<totalOptions; i++) 
            {
                $($(".questions")[i]).attr("data-pos",i);
                if(userAns[i] != correctAnswer[i])
                {
                    $("#signC2" + i).css("display", "none");
                    $("#correctWrongSign").append('<div class="signCls" id="signC3' + i + '"><img src="'+ Helpers.constants.THEME_PATH +'img/Language/match/blueCmark.png"/></div>');
                }
                else
                {
                    //$("#signC2" + i).css("display", "none");
                    $("#correctWrongSign").append('<div class="signCls" id="signC1' + i + '"><img src="'+ Helpers.constants.THEME_PATH +'img/Language/match/greenCmark.png"/></div>');
                }
            }

            $("#sessionReportQues").show();

            
            for (var j = 0; j < totalOptions; j++) {
                if(userAns[j] != correctAnswer[j])
                    signMarkDisplay($("#quesId"+j), ("signC3" + j), 620, 18);
                else  
                   signMarkDisplay($("#quesId"+j), ("signC1" + j), 620, 18);
            }

            for(var i=0; i<totalOptions; i++) {
                var optionText = $("#sequenceContainer #quesId"+i).html();
                if(leftSubType == "text" || rightSubType == "text") {    // Handling text in options
                    if(maxHeight < $("#sequenceContainer #quesId"+i).outerHeight()) 
                        maxHeight = $("#sequenceContainer #quesId"+i).outerHeight()+10;
                }
                if(leftSubType == "image" || rightSubType == "image") {
                    if(maxHeight < 120)
                        maxHeight = 120;

                    if(leftSubType == "image") {
                        var imgName = optionText.split("~")[0];
                        var txtName = optionText.split("~")[1];
                    }
                    else {
                        var imgName = optionText.split("~")[1];
                        var txtName = optionText.split("~")[0];
                    }

                    if(leftSubType == "text" || rightSubType == "text") {
                        // mixed version
                        $("#sequenceContainer #quesId"+i).html("<img src="+imagePath+imgName+" /><text>"+txtName+"</text>");
                        $("#sequenceContainer #ansId"+i).html("<img src="+imagePath+imgName+" /><text>"+txtName+"</text>");
                    }
                    else {
                        // pure image
                        $("#sequenceContainer #quesId"+i).html("<img src="+imagePath+imgName+" style='margin-left: 40%'/>");
                        $("#sequenceContainer #ansId"+i).html("<img src="+imagePath+imgName+" style='margin-left: 40%'/>");
                    }
                    $("#sequenceContainer #quesId"+i).attr("data_image",imgName);
                    $("#sequenceContainer #ansId"+i).attr("data_image",imgName);
                }
                if(leftSubType == "audioicon" || rightSubType == "audioicon") {

                    
                    if(maxHeight < 110)
                        maxHeight = 110;
                    var audioName = $("#sequenceContainer #quesId"+i).attr("data_sound");
                    allSndArr.push(audioName);
                    var imgName = '<img src="' + assetsPath + 'soundIconGrey.png" class="audioOptions"/>';
                    $("#sequenceContainer #quesId"+i).html(imgName);
                    $("#sequenceContainer #ansId"+i).html(imgName);
                } //will come after html
            }
            for (var j = 0; j < totalOptions; j++) {
                if(userAns[j] != correctAnswer[j])
                    signMarkDisplay($("#quesId"+j), ("signC3" + j), 620, 18); 
                else  
                   signMarkDisplay($("#quesId"+j), ("signC1" + j), 620, 18); 
            }

            
        });
       
        $(".questions").css({
            "cursor":"default"
        });

        $( "img" )
          .error(function() {
            imgNotLoading();
        });

        if(correct < 1)
        {
            var topPostitionCountGet                     = totalOptions - 1;
            var topPostition                             = $("#quesId"+topPostitionCountGet).position().top;
            
            //var toTopShwAns                              = $("#showUrAnsSeq").position().top;
            if(explanation != '')
            {
                var toTopExp      = $("#explantionAreaSeq").position().top;
                var topHe = toTopExp - 150;
                document.getElementById('showAns').style.top =  topHe + "px";
            }
            else
            {
                var topHe = 50;   
                document.getElementById('showAns').style.top = topHe + (topPostition) + "px";
            }
            

            //var toTopShwAns                              = $("#showUrAnsSeq").position().top;
            //document.getElementById('showUrAnsSeq').style.top = (topPostition) + "px";
        }
    }, 1000);
    
}

var signMarkDisplay = function(targObj, signObj, leftPos, topPos) {
    $("#sequenceContainer .signCls").show();
    var targetObj = targObj;
    var toLeft = $(targetObj).position().left;
    var toTop = $(targetObj).position().top;
    
    $("#sequenceContainer #" + signObj).css({
        'display' : ''
    });
    
    document.getElementById(signObj).style.visibility = "visible";
    document.getElementById(signObj).style.position   = "absolute";
    document.getElementById(signObj).style.left       = (toLeft + leftPos) + "px";
    document.getElementById(signObj).style.top        = (toTop + topPos) + "px";
};
function setHeight(maxHeight,totalOptions)
{
    $(".questions,.answers,.hiddenQuestions").css({
        "height":maxHeight+"px"
    });
    var offset = $( "#sequenceContainer #sortable" ).offset().top;
    $( "#sequenceContainer #sortable" ).height((maxHeight+15)*totalOptions);
    //$( "#sequenceContainer" ).height(((maxHeight+15)*totalOptions)+offset+maxHeight);
    $("#sessionReportQues").height(((maxHeight+15)*totalOptions)+offset+maxHeight);
    //$( "#showAns" ).height(((maxHeight+15)*totalOptions)+offset+maxHeight);
}

function showQuesMatch(qcode) //original
{   

    sessionData.qID = qcode;
    setFlag = true;
    var totalOptions  = match[qcode].totalOptions;
    var explanation   = match[qcode].explanation;
    var quesType      = match[qcode].quesType;
    var quesId        = match[qcode].quesId;
    var quesSubType   = match[qcode].quesSubType;
    var optSubType    = match[qcode].optSubType;
    var option_a      = match[qcode].option_a;
    var option_b      = match[qcode].option_b;
    var option_c      = match[qcode].option_c;
    var option_d      = match[qcode].option_d;
    var option_e      = match[qcode].option_e;
    var option_f      = match[qcode].option_f;
    var userResponsee = match[qcode].userResponsee;
    var correct       = match[qcode].correct;

    var assetsPath = Helpers.constants.THEME_PATH + "img/Language/templates/";
    var imagePath  = Helpers.constants.LIVE_CONTENT_PATH + 'templates_qtype/images/';

    var correctHtml = '';
    var userHtml    = '';
    var allSndArr= new Array();
    

    var optLabelForCorrectAns = [];
    //var optLabelForCorrectAns = [arrMatch[0], arrMatch[1], arrMatch[2], arrMatch[3], arrMatch[4], arrMatch[5]];
    var optLabelForCorrectAns = [option_a, option_b, option_c, option_d, option_e, option_f];
    //var userResponse          = arrUserResponceMatch[0].split('|');
    var userResponse          = userResponsee.split('|');

    var corrAns = [];
    var uabc = [];
    
    for ( i = 0; i < totalOptions; i++) 
    {
        var optionText  = optLabelForCorrectAns[i];
        var optionsArr  = optionText.split('~');
        
        var leftoption  = optionsArr[0].replace("/","").replace(/&nbsp;/g, ' ').replace(/'/g, "");
        
        var rightoption = optionsArr[1].replace("/","").replace(/&nbsp;/g, ' ').replace(/'/g, "");
        
        var corretAns = leftoption+rightoption;
        var correctAns1        = corretAns.toString();
        var correctAns2         = correctAns1.replace(/\s/g, '').replace(/<br\s*[\/]?>/gi, "").replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
        corrAns.push(correctAns2);
        
        
        var optionTextUser  = userResponse[i];
        var optionsArrUser  = optionTextUser.split(':');
        var leftoptionUser  = optionsArrUser[0].replace("/","");
        
        
        var rightoptionUser = optionsArrUser[1].replace("/","");
        
        

        //html of the correct answer
        if (i == 0)
        {
            //correctHtml += '<div style="position:relative; margin-top:-60px"><div style="height:65px" id="left' + i + '" data-pos=' + i + ' data-side="Left" class="columns left ui-draggable ui-droppable conn" data-val="'+handleQuotes(leftoption)+'"><span>' + leftoption + '</span><div class="labels leftLabels">'+parseInt(i+1)+'</div></div>';
            correctHtml += '<div style="position:relative; margin-top:-60px"><div id="left' + i + '" data-pos=' + i + ' data-side="Left" class="columns left ui-draggable ui-droppable conn" data-val="'+handleQuotesMatch(leftoption)+'"><span>' + leftoption + '</span><div class="labels leftLabels">'+parseInt(i+1)+'</div></div>';
        }
        else
        {
            correctHtml += '<div style="position:relative;"><div id="left' + i + '" data-pos=' + i + ' data-side="Left" class="columns left ui-draggable ui-droppable conn" data-val="'+handleQuotesMatch(leftoption)+'"><span>' + leftoption + '</span><div class="labels leftLabels">'+parseInt(i+1)+'</div></div>';
        }

        correctHtml += '<div id="right' + i + '" data-pos=' + i + ' data-side="Right"  class="columns right ui-draggable ui-droppable conn" data-val="'+handleQuotesMatch(rightoption)+'"><span>' + rightoption + '</span></div></div>';
        
        //html of the user answer
        if (i == 0)
        {
            //userHtml += '<div style="position:relative; margin-top:-60px"><div style="height:65px" id="left' + i + '" data-pos=' + i + ' data-side="Left" class="columns left ui-draggable ui-droppable conn" data-val="'+handleQuotesMatch(leftoptionUser)+'"><span>' + leftoptionUser + '</span><div class="labels leftLabels">'+parseInt(i+1)+'</div></div>';
            userHtml += '<div style="position:relative; margin-top:-60px"><div id="left' + i + '" data-pos=' + i + ' data-side="Left" class="columns left ui-draggable ui-droppable conn" data-val="'+handleQuotesMatch(leftoptionUser)+'"><span>' + leftoptionUser + '</span><div class="labels leftLabels">'+parseInt(i+1)+'</div></div>';
        }
        else
        {
            //userHtml += '<div style="position:relative;"><div style="height:65px" id="left' + i + '" data-pos=' + i + ' data-side="Left" class="columns left ui-draggable ui-droppable conn" data-val="'+handleQuotesMatch(leftoptionUser)+'"><span>' + leftoptionUser + '</span><div class="labels leftLabels">'+parseInt(i+1)+'</div></div>';

            userHtml += '<div style="position:relative;"><div id="left' + i + '" data-pos=' + i + ' data-side="Left" class="columns left ui-draggable ui-droppable conn" data-val="'+handleQuotesMatch(leftoptionUser)+'"><span>' + leftoptionUser + '</span><div class="labels leftLabels">'+parseInt(i+1)+'</div></div>';
        }

        userHtml += '<div id="right' + i + '" data-pos=' + i + ' data-side="Right"  class="columns right ui-draggable ui-droppable conn" data-val="'+handleQuotesMatch(rightoptionUser)+'"><span>' + rightoptionUser + '</span></div></div>';

    }

    

    /*var userResponseArr = userResponse.split('|');
    userAns         = [userResponseArr[0], userResponseArr[1], userResponseArr[2], userResponseArr[3], userResponseArr[4], userResponseArr[5]];*/

    //var optionsColorArr = ["hover1","hover2","hover3","hover4","hover5","hover6"];

    /*var maxHeight      = 0;*/
    var leftSubType      = optSubType.split("And")[0];
    var rightSubType     = optSubType.split("And")[1];
    var quesLeftSubType  = quesSubType.split("And")[0];
    var quesRightSubType = quesSubType.split("And")[1];
    

    var questions={qType: quesType, qID: quesId}; //change this to dynamic when submit
    setQuestion(questions);

    setTimeout(function(){

        /*var lookbackContainerQues = document.createElement('div');
        lookbackContainerQues.className = 'lookbackContainerQues';*/
        var maxHeight = 0;
        var modal = document.getElementById('myModalSessionReport');
        modal.style.display = "block";

        //var close_button = '<div class="prompt-heading"><button class="close-prompt toast-close" prompt-close=\'#lookbackContainerQues\'><i class="fa fa-close"></i></button> </div>';
        //var close_button = '<input type="button" onclick="closeModalSession()" class="form-control btn btn-sm session-btn-cls" value="x">';

        var close_button = '';
        
        var quesHtml = sessionData.quesMatchHtml;
        
        var bodyHtml = '<div id="subContainerQuestion" class="container-fluid qtypeTopBuffer"><div id="matchContainer">'+quesHtml+'</div></div>';

        if(correct < 1)
        {
            var rightAns = '<div id="showAns" class="showAns"><img class="profile-icon-img" src="theme/img/Language/showans.png"></div>';
        }
        else
            var rightAns = '';

        if(explanation != '')
        {
            var explaHtml = '<div id="explantionAreaMatch"><span class="explanationReport"> Explanation - </span><span class="expText">'+explanation+'</span><div>';
        }
        else
            var explaHtml = '';

        $("#sessionReportQues").html(close_button+bodyHtml+rightAns+explaHtml);
        //$('#modalBlocker').show();
       

        
        //$( "#matchContainer" ).height(500);
        

        var titleTxt = $( "#matchContainer #matchTitle" ).html();
        $( "#matchContainer" ).html('');

        var titleHtml = '<div id="matchTitle">'+titleTxt+'</div>';
        var correctWrongSignHtml  = '<div id="correctWrongSign"> </div>';

        //setBindings(); //for audio 
        
        $( "#matchContainer" ).html(titleHtml+userHtml+correctWrongSignHtml);
        

        for(i=0; i<totalOptions; i++) {
            var optionTextUser  = userResponse[i];
            var optionsArrUser  = optionTextUser.split(':');

            var leftoptionUser  = optionsArrUser[0].replace("/","");
            
            var rightoptionUser = optionsArrUser[1].replace("/","");
            
            var user     = leftoptionUser+rightoptionUser;
            var userR    = user.replace(/\s/g, '');
            var savedGC1 = userR.toString();
            var savedGC  = savedGC1.replace(/<br\s*[\/]?>/gi, "").replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
            
            
           
            var matchString    = corrAns.indexOf(savedGC);
            
            if(matchString !== -1)
            {  
                $("#correctWrongSign").append('<div class="signCls" id="signC1' + i + '"><img src="'+ Helpers.constants.THEME_PATH +'img/Language/match/greenCmark.png"/></div>');
                //signMarkDisplayMatch($("#right"+i), ("signC1" + i), 320, 18); 
            }
            else
            {
                $("#correctWrongSign").append('<div class="signClsW" id="signC2' + i + '"><img src="'+ Helpers.constants.THEME_PATH +'img/Language/match/wrongCmark.png"/></div>');
               // signMarkDisplayMatch($("#right"+i), ("signC2" + i), 320, 18);
            }


            if(leftSubType == "image" || rightSubType == "image") {  // Handling images in options
                maxHeight = 120;
                if(leftSubType == "image") {
                    var imgName = $("[data-pos='"+i+"'].left").attr("data-val");
                    var imgTag = "<img src="+imagePath+imgName+" />";
                    $("[data-pos='"+i+"'].left span").html(imgTag);
                }
                if(rightSubType == "image") {
                    var imgName = $("[data-pos='"+i+"'].right").attr("data-val");
                    var imgTag = "<img src="+imagePath+imgName+" />";
                    $("[data-pos='"+i+"'].right span").html(imgTag);
                }
            }
            if(leftSubType == "audio" || rightSubType == "audio") {  // Handling audio in options
                maxHeight = 120;
                if(leftSubType == "audio") {
                    var audioName = $("[data-pos='"+i+"'].left").attr("data-val");
                    allSndArr.push(audioName);
                    var imgTag = '<img src="' + assetsPath + 'soundIconGrey.png" class="audioOptions"/>';
                    $("[data-pos='"+i+"'].left span").html(imgTag);
                }
                if(rightSubType == "audio") {
                    var audioName = $("[data-pos='"+i+"'].right").attr("data-val");
                    allSndArr.push(audioName);
                    var imgTag = '<img src="' + assetsPath + 'soundIconGrey.png" class="audioOptions"/>';
                    $("[data-pos='"+i+"'].right span").html(imgTag);
                }
            }
            if(leftSubType == "text" && rightSubType == "text") {    // Handling text in options
                if(maxHeight < $("[data-pos='"+i+"'].left").outerHeight() || maxHeight < $("[data-pos='"+i+"'].right").outerHeight()) {
                    if($("[data-pos='"+i+"'].left").outerHeight() < $("[data-pos='"+i+"'].right").outerHeight())
                    {
                        maxHeight = $("[data-pos='"+i+"'].right").outerHeight()+10;
                    }
                    else
                    {
                        maxHeight = $("[data-pos='"+i+"'].left").outerHeight()+10;
                    }
                }
            }
        }
        $(".columns").css({
            "height":maxHeight+"px",
            "cursor":"default"
        });
        var initialHeight = 70;
        for(i=1;i<totalOptions;i++)
        {
            initialHeight = initialHeight + maxHeight+10;
            $("[data-pos="+i+"]").css({"top":initialHeight+"px"});
        }

        for (var k = 0; k < totalOptions; k++) 
        {
            var optionTextUser  = userResponse[k];
            var optionsArrUser  = optionTextUser.split(':');

            var leftoptionUser  = optionsArrUser[0].replace("/","");
            
            var rightoptionUser = optionsArrUser[1].replace("/","");
            
            var user     = leftoptionUser+rightoptionUser;
            var userR    = user.replace(/\s/g, '');
            var savedGC1 = userR.toString();
            var savedGC  = savedGC1.replace(/<br\s*[\/]?>/gi, "").replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
            
            
           
            var matchString    = corrAns.indexOf(savedGC);
            
            if(matchString !== -1)
            { 
                signMarkDisplayMatch($("#right"+k), ("signC1" + k), 320, 18); 
            }
            else
            {
                signMarkDisplayMatch($("#right"+k), ("signC2" + k), 320, 18);
            }
        }
        

        if(explanation != '')
        {
            var heightRight = totalOptions - 1;
            var toTopRight     = $("#right"+heightRight).position().top;
            var topFixedHeight = 96;

            var toTopExp                                                  = $("#explantionAreaMatch").position().top;
            document.getElementById('explantionAreaMatch').style.top      = toTopRight + toTopExp + topFixedHeight+"px";
            document.getElementById('explantionAreaMatch').style.position = "relative";
            document.getElementById('explantionAreaMatch').style.width = "80%";
        }

        if(correct == 0)
        {
            
            var heightRight = totalOptions - 1;

            var toTopRight     = $("#right"+heightRight).position().top;
            var toTopShwAns    = $("#showAns").position().top;
            

            if(explanation != '')
            {
                var toTopExp   = $("#explantionAreaMatch").position().top;
                var topFixedHeight =  toTopExp - 52;
                document.getElementById('showAns').style.top =   topFixedHeight+"px";
            }
            else
            {
                var topFixedHeight = 145;
                document.getElementById('showAns').style.top = toTopRight + toTopShwAns + topFixedHeight+"px";
            }
        }

        $('#showAns').on('click', function(e){

            $("#sessionReportQues").hide();
            var titleTxt = $( "#matchContainer #matchTitle" ).html();
            $( "#matchContainer" ).html('');

            var titleHtml = '<div id="matchTitle">'+titleTxt+'</div>';
            var correctWrongSignHtml  = '<div id="correctWrongSign"> </div>';

            
            $( "#matchContainer" ).html(titleHtml+correctHtml+correctWrongSignHtml);

           /* for (var i=0; i<totalOptions; i++) 
            {
                $("#signC2" + i).css("display", "none");
                $("#correctWrongSign").append('<div class="signCls" id="signC3' + i + '"><img src="'+ Helpers.constants.THEME_PATH +'img/Language/match/blueCmark.png"/></div>');
            }*/
            for (var i=0; i<totalOptions; i++) 
            {
                var optionTextUser  = userResponse[i];
                var optionsArrUser  = optionTextUser.split(':');

                var leftoptionUser  = optionsArrUser[0].replace("/","");
                
                var rightoptionUser = optionsArrUser[1].replace("/","");
                
                var user     = leftoptionUser+rightoptionUser;
                var userR    = user.replace(/\s/g, '');
                var savedGC1 = userR.toString();
                var savedGC  = savedGC1.replace(/<br\s*[\/]?>/gi, "").replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
                
                

                var matchString    = corrAns.indexOf(savedGC);
                
                if(matchString == -1)
                {  
                    $("#signC2" + i).css("display", "none");
                    $("#correctWrongSign").append('<div class="signCls" id="signC3' + i + '"><img src="'+ Helpers.constants.THEME_PATH +'img/Language/match/blueCmark.png"/></div>');
                }
                else
                {
                    $("#correctWrongSign").append('<div class="signCls" id="signC1' + i + '"><img src="'+ Helpers.constants.THEME_PATH +'img/Language/match/greenCmark.png"/></div>');
                }
            }
            for(i=0; i<totalOptions; i++) {
                if(leftSubType == "image" || rightSubType == "image") {  // Handling images in options
                    maxHeight = 120;
                    if(leftSubType == "image") {
                        var imgName = $("[data-pos='"+i+"'].left").attr("data-val");
                        var imgTag = "<img src="+imagePath+imgName+" />";
                        $("[data-pos='"+i+"'].left span").html(imgTag);
                    }
                    if(rightSubType == "image") {
                        var imgName = $("[data-pos='"+i+"'].right").attr("data-val");
                        var imgTag = "<img src="+imagePath+imgName+" />";
                        $("[data-pos='"+i+"'].right span").html(imgTag);
                    }
                }
                if(leftSubType == "audio" || rightSubType == "audio") {  // Handling audio in options
                    maxHeight = 120;
                    if(leftSubType == "audio") {
                        var audioName = $("[data-pos='"+i+"'].left").attr("data-val");
                        allSndArr.push(audioName);
                        var imgTag = '<img src="' + assetsPath + 'soundIconGrey.png" class="audioOptions"/>';
                        $("[data-pos='"+i+"'].left span").html(imgTag);
                    }
                    if(rightSubType == "audio") {
                        var audioName = $("[data-pos='"+i+"'].right").attr("data-val");
                        allSndArr.push(audioName);
                        var imgTag = '<img src="' + assetsPath + 'soundIconGrey.png" class="audioOptions"/>';
                        $("[data-pos='"+i+"'].right span").html(imgTag);
                    }
                }
                if(leftSubType == "text" && rightSubType == "text") {    // Handling text in options
                    if(maxHeight < $("[data-pos='"+i+"'].left").outerHeight() || maxHeight < $("[data-pos='"+i+"'].right").outerHeight()) {
                        if($("[data-pos='"+i+"'].left").outerHeight() < $("[data-pos='"+i+"'].right").outerHeight())
                        {
                            maxHeight = $("[data-pos='"+i+"'].right").outerHeight()+10;
                        }
                        else
                        {
                            maxHeight = $("[data-pos='"+i+"'].left").outerHeight()+10;
                        }
                    }
                }
                ;
                
            }
            $(".columns").css({
                "height":maxHeight+"px"
            });

            var initialHeight = 70;
            for(i=1;i<totalOptions;i++)
            {
                initialHeight = initialHeight + maxHeight+10;
                $("[data-pos="+i+"]").css({"top":initialHeight+"px"});
            }
            $("#sessionReportQues").show();

            for (var i = 0; i < totalOptions; i++) 
            {
                var optionTextUser  = userResponse[i];
                var optionsArrUser  = optionTextUser.split(':');

                var leftoptionUser  = optionsArrUser[0].replace("/","");
                
                var rightoptionUser = optionsArrUser[1].replace("/","");
                
                var user     = leftoptionUser+rightoptionUser;
                var userR    = user.replace(/\s/g, '');
                var savedGC1 = userR.toString();
                var savedGC  = savedGC1.replace(/<br\s*[\/]?>/gi, "").replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
                
                

                var matchString    = corrAns.indexOf(savedGC);
                if(matchString == -1)
                {
                    signMarkDisplayMatch($("#right"+i), ("signC3" + i), 320, 18);
                }
                else
                {
                    signMarkDisplayMatch($("#right"+i), ("signC1" + i), 320, 18); 
                }
            }
        });

        $( "img" )
          .error(function() {
            imgNotLoading();
        });
    }, 1000);
    
}


var signMarkDisplayMatch = function(targObj, signObj, leftPos, topPos) {
    $(".signCls").show();
    var targetObj = targObj;
    var toLeft = $(targetObj).position().left;
    var toTop = $(targetObj).position().top;
    $("#" + signObj).css({
        'display' : ''
    });
    //document.getElementById(signObj).style.visibility = "visible";
    document.getElementById(signObj).style.position     = "absolute";
    document.getElementById(signObj).style.left         = (toLeft + leftPos) + "px";
    document.getElementById(signObj).style.top          = (toTop + topPos) + "px";
};


// handling single and double quotes
/*var handleQuotesMatch = function(str) {
    var len = str.length;
    if(str.indexOf("\"")!=-1) {
        str = str.replace(/"/g,"");
    }
    if(str.indexOf("'")!=-1) {
        str = str.replace(/'/g,"");
    }
    return str;
};*/

var handleQuotesMatch = function(str) {

    
    if(str != undefined)
    {
        var len = str.length;
        if(str.indexOf("\"")!=-1) {
            str = str.replace(/"/g,'&quot;');
        }
        if(str.indexOf("'")!=-1) {
            str = str.replace(/'/g,"\\'");
        }
        if(str.indexOf(",")!=-1) {
            str = str.replace(/,/g,"\\,");
        }
        if(str.indexOf("&#10;")!=-1) {
           
            str = str.replace(/&#10;/g,"");
           
        }
        return str;
    }   
};

function makingwordQues(qcode)
{
    var quesType      = makingWord[qcode].quesType;
    var quesId        = makingWord[qcode].quesId;
    var explanation   = makingWord[qcode].explanation;
    var userResponsee = makingWord[qcode].userResponsee;

    var assetsPath        = Helpers.constants.THEME_PATH + "img/Language/templates/";
    var userResponseTotal = '';
    var userlist          = [];
    var correctlist       = [];
    var userResponse      = userResponsee;

    if(userResponse !='')
    {
        userResponseTotal =     userResponse.split('~');
        var newArray = userResponseTotal.filter(function(v){return v!==''});
    }

    var totalOptions = newArray.length;
    
    var questions={qType: quesType, qID: quesId}; //change this to dynamic when submit

    setQuestion(questions);

    setTimeout(function(){
        var modal                       = document.getElementById('myModalSessionReport');
        modal.style.display             = "block";

        //var close_button = '<div class="session-btn-cls"><input type="button" onclick="closeModalSession()" class="form-control btn btn-sm" value="x"></div>';
        var close_button = '';
        
        var quesHtml                = sessionData.quesMakingWordHtml;
        var quesMakingWordInputHtml = sessionData.quesMakingWordInputHtml;

        var bodyHtml = '<div id="subContainerQuestion" class="container-fluid qtypeTopBuffer"><div id="makingWordContainer">'+quesHtml+'</div><div id="qtypeContainerOptMaking">'+quesMakingWordInputHtml+'</div></div>';
        //var rightAns = '<div id="showAns"><img class="profile-icon-img" src="theme/img/Language/showans.png"></div>';
        var rightAns = '';

        if(explanation != '')
        {
            var explaHtml = '<div id="explantionAreaBlank"><span class="explanationReport"> Explanation - </span><span class="expText">'+explanation+'</span><div>';
        }
        else
            var explaHtml = '';

        $("#sessionReportQues").html(close_button+bodyHtml+rightAns+explaHtml);

        for (var i = 0; i < totalOptions; i++) 
        {
            var sr_no = i + 1;
            //making list with user answer
            userlist[i] ='<div id="contentContainer'+sr_no+'" class="col-md-5 col-lg-6 contentContainer"><div class="qtypeClLabelCheckBoxOpt options col-md-1" id="parentDivlabId'+i+'"><div class="row" id="divlabId'+i+'">'+sr_no+'</div></div><div id="optId'+i+'" class="col-md-8 col-lg-7 qtypeClOptDiv qtypeNoLpadding qtypeClOptSndIcon options"><input type="text" class="form-control get_response" disabled="true" id="cb_Id'+i+'" count="'+i+'" name="opt_CB" value="'+newArray[i]+'"><br></div></div>';
        }

        $( "#qtypeTopBufferDiv" ).html('');
        $( "#qtypeTopBufferDiv" ).html(userlist);

        for (var i = 0; i < totalOptions; i++) 
        {
            if(jQuery.inArray(newArray[i], sessionData.possibleOptions) == -1)
            {
                $(this).addClass('wrong');
                $("#divlabId"+i).append('<div class="signCls" id="signC'+i+'"><img src="' + assetsPath + 'wrongCmark.png"/></div>');
                $("#signC"+i+" img").height("35px");
                $("#signC"+i+" img").width("35px");
                $("#divlabId" + i).addClass("bgcolorRed");
                $("#parentDivlabId" + i).addClass("bgcolorRed");
                $("#signC"+i).css({
                    'position' : 'absolute',
                    'left' : '-23px',
                    'top' : '0px'
                });
            }
            else
            {
                $(this).addClass('right');
                $("#divlabId"+i).append('<div class="signCls" id="signC'+i+'"><img src="' + assetsPath + 'greenCmark.png"/></div>');
                $("#signC"+i+" img").height("35px");
                $("#signC"+i+" img").width("35px");
                $("#divlabId" + i).addClass("bgcolorGreen");
                $("#parentDivlabId" + i).addClass("bgcolorGreen");
                $("#signC"+i).css({
                    'position' : 'absolute',
                    'left' : '-23px',
                    'top' : '0px'
                });
            }
        }
    }, 1000);
}

function closeModalSession()
{
    //setFlag = false;
    var modal           = document.getElementById('myModalSessionReport');
    modal.style.display = "none";
    sessionData.quesComHtml = '';
}

function closeMcq(type)
{
    //setFlagBlankMcq = false;
    $('.hovers').hide();
    $('.prompts').hide();
    $('#modalBlocker').hide();

    if(type == 'conversation')
    {
        audioObject = new Audio($('#audioContainer')[0]);
        audioObject.stop();
    }
}

/*$(document).click(function(e) {
    console.log(e.target);
  if( e.target.id != 'info') {
    $("#info").hide();
  }
});*/

/*window.onclick = function(event) {
    var container = $("#sessionReportQues");
    if (event.target == modal) {
        var modal           = document.getElementById('myModalSessionReport');
        modal.style.display = "none";
    }
}*/

