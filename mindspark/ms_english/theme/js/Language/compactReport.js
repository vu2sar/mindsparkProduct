/**
 * @author kalpesh Awathare
 */

var maxBarWidth = 500;
window.onload = initialize;

var remedialText = [
    "Practice activites for Grammar",
    "You seem to have scored relatively lower on Vocabulary.This implies that you need to increase your understanding of words and their various interpretations. One of the best ways to increase your vocabulary is through contextual reading and listening; Read texts that are somewhat above your comfort level in reading, and highlight words/phrases which seem difficult or leave you in doubt. Note down what you THINK they mean, and cross-reference against a dictionary to see whether they match. This will make it interesting and help you remember contextual meanings better. <br>However, skills are always interdependent, and therefore the best way to acquire a language is through continued exposure to listening, speaking, reading and writing.",
    " You seem to have scored relatively lower on Reading.  This implies that you need to work towards improving reading-oriented skills, such as recall, inference and extrapolation. In order to do this, you should primarily develop a habit of reading regularly and for an extended period of time. Whether it be fiction or non-fiction, try and read somewhat longer texts which challenge your attention span and ability to recollect characters and events (so pick up something that interests you!). If you find it difficult to keep track, make notes and summaries while reading, to jog your memory when you pick it up again. Also try and analyze the text while you are reading it, for example by thinking of alternate/future events which might take place, and listing down some key plot and character traits. Look up discussions on the web as well (Goodreads etc.) - it will help you get different interpretations of the same text. <br>However, skills are always interdependent, and therefore the best way to acquire a language is through continued exposure to listening, speaking, reading and writing.",
    " You seem to have scored relatively lower on Listening.  This implies that you need to focus on increasing listening-oriented skills. In order to do this, you need to primarily converse in English with your peers. Watch English movies, news channels and documentaries (with subtitles initially, then without) and see how much you are able to comprehend and clearly remember. Join a film discussion group, for example. Another great way of increasing your listening ability is to listen to audiobooks, which are informative and require a certain level of attention in order to closely follow. <br>However, skills are always interdependent, and therefore the best way to acquire a language is through continued exposure to listening, speaking, reading and writing."
];

var remedialTitle = ['Grammar' , 'Vocabulary', 'Comprehension', 'Listening'];

var toDisplay = {
    'Grammar': 'Grammar',
    'Vocabulary': 'Vocabulary',
    'Comprehension': 'Reading',
    'Listening': 'Listening'
};

var remedialToDisplay = {
    'Grammar': 'Grammar',
    'Vocabulary': 'Vocabulary',
    'Comprehension': 'Reading Comprehension',
    'Listening': 'Listening Comprehension'
    };

function initialize(){
    alert("Check the code here for diagnosticTest. Check the ajax call in the initialize function of compactReport.js ");
    var id = document.URL.split('?')[1];
    if(id == undefined)
        id = '';
    $.ajax({
        url: Helpers.constants['CONTROLLER_PATH'] + 'diagnosticTest/getDiagnosticTestReportData/' + id,
    }).done(function(data){
        Helpers.ajax_response( getDiagnosticTestReportData , data , [] );
    });
};

function getDiagnosticTestReportData(data, extraParams)
{
    reportJSON = data;
    populateResultsTable();
    graphifyBars();
}

function populateResultsTable(){
    for(var i = 0 ; i < 1; i ++){
        var json = reportJSON[i];
        var resultJson = {};
        var currentTable;
        for(var key in json){
            if(json.hasOwnProperty(key)){
                resultJson = json[key];
               // $($('.dearUserName')[i]).html('Dear ' + key + ',');
                $('#reports').append('<table><tbody></tbody></table>');
                currentTable = $('tbody')[i];
                $(currentTable).append('<tr><td>Skill Tested</td><td><div class="your legend"> </div> Average accuracy <div class="avg legend"> </div> Highest accuracy </td></tr>');
                break;
            }
        }
        for(var key in resultJson){
            if(resultJson.hasOwnProperty(key)){
                var topic = key;
                $(currentTable).append('<tr><td>' + toDisplay[key] + '</td><td><div class="bars your" value="' + resultJson[key]['avg'] + '"></div> <div class="bars avg" value="' + resultJson[key]['highest'] + '"></div> </td></tr>');
            }
        }
        
        if(!(resultJson.Grammar == undefined)){
            $(currentTable).append('<tr><td></td><td><div class="scale"></div><div class="clear">Percentage Accuracy</div></td></tr>');
            $('.reportContainer').append('<hr>');
        }
    }
}

function graphifyBars(){
    bars = $('.bars');
    
    for(var i = 0 ; i < bars.length; i++){
        var value = parseFloat($(bars[i]).attr('value')) * 100;
        $(bars[i]).css('width', value + '%');
        if($(bars[i]).hasClass('your') || $(bars[i]).hasClass('avg')){
            var className = 'in';
            if(value < 10){
                className = 'out';
            }
            $(bars[i]).append('<div class="value ' + className + '">' + Math.round(value) + '%</div>');
        }
    }
    
    var scales = $('.scale');
    for( var i = 0 ; i < scales.length ; i++){
        for( var j = 0 ; j < 6; j++){
            $(scales[i]).append('<div class="tick"><div class="tickValue">'  +(20*j) + '</div></div>');
        }
    }
}

function showRemedialText(){
    var results = $('tbody');
    for(var i = 0 ; i < results.length; i++){
        var least = 100;
        var bars = $('.avg',results[i]);
        var skillProblemIn = 3;
        for(var j = 1; j < bars.length; j++){
            value = parseFloat($(bars[j]).attr('value'));
            if(value < least){
                least = value;
                skillProblemIn = j - 1;
            }
        }
        $('<div class="remedialactionheader">Skill In need of Relative Improvement - <strong>' + remedialToDisplay[remedialTitle[skillProblemIn]] + '.</strong></div><div class="remedialaction">' + remedialText[skillProblemIn] + '</div><br>').insertAfter(results[i]);
    }
}

