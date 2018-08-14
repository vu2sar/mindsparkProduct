var legendFlag = 1;
var scrollToDailyPractice = 1;
function loadTopicList(category, ttCode) {
  var dataArr = $("#frmTopicReport :input");

  var categoryNode = document.createElement('input'); // adding category to 
  categoryNode.setAttribute('type','hidden');
  categoryNode.setAttribute('name','category');
  categoryNode.setAttribute('id','category');
  categoryNode.setAttribute('value',category);
  dataArr.push(categoryNode);
  var selectObj = document.getElementById('lstTopic');
  ajaxRequestForActivatedTopicsForSelectedClassAndSection(dataArr, selectObj, ttCode);
}

function ajaxRequestForActivatedTopicsForSelectedClassAndSection(dataArr, selectObj, ttCode)
{
  var dataString = dataArr.serializeArray();
  var actionUrl = "functions/activatedAndDeactivatedTopicsAjax.php";
  selectObj.innerHTML = "";
  var optionsAsString = "<option value=''>Select</option>";
  $('[name=topic]').attr("disable", "");
  $.ajax({
    type: "POST",
    url: actionUrl,
    data: dataString,
    success: function(data) {
      var result = jQuery.parseJSON(data);
      if(result != "")
      {
        for(var i=0; i<Object.keys(result[Object.keys(result)]).length; i++) 
        {              
          optionsAsString += "<option value='" + Object.keys(result[Object.keys(result)])[i] + "'>" + result[Object.keys(result)][Object.keys(result[Object.keys(result)])[i]] + "</option>";
        }
      }
      $('#'+selectObj.id).append(optionsAsString);  
      $('#'+selectObj.id).removeAttr("disable"); 
      $("#"+selectObj.id).val(ttCode);       
    }
  });
}
function categorizeAccuracy(accuracy) {
  category = "";
  if(accuracy >= 0 && accuracy < 40) {
    category = 'low';
  } else if (accuracy>=40 && accuracy<80) {
    category = 'good';
  } else if(accuracy >= 80){
    category = 'great';
  } else {
    category = 'notEnoughUsage';
  } 
  return category;
}

function topicReportAjaxRedirect(coteacherInterfaceFlag) {          
  //topicReport.php?schoolCode=9582&cls=5&sec=A&topics=GEO002,DEC003&startDate=2015-03-22&endDate=2015-04-21&mode=1$topicName=Whatever%20it%20is

  var schoolCode = getFromRequest("schoolCode");
  var cls = getFromRequest("cls");
  var sec = getFromRequest("sec");
  var clusters = getFromRequest("topics");
  var startDate = getFromRequest("startDate");
  var endDate = getFromRequest("endDate");
  var mode = getFromRequest("mode");
  var topicName = getFromRequest("topicName");

  var dataString = new Array();
  dataString.push({name: 'class', value: cls});
  dataString.push({name: 'section', value: sec});
  dataString.push({name: 'topic', value: clusters});
  dataString.push({name: 'mode', value: mode});
  dataString.push({name: 'schoolCode', value: schoolCode});
  dataString.push({name: 'topicName', value: topicName});
  dataString.push({name: 'startDate', value: startDate});
  dataString.push({name: 'endDate', value: endDate});

  $("#lstClass").val(cls);
  $("#lstClass").trigger("change");
  $("#lstSection").val(sec);

  if(mode == 0) {
    setTimeout(function(){
      setTopicReportHeading(dataString);      
    },300);
    var intervalCode = setInterval(function () {
      if($("#lstTopic").children().length > 0) {
        $("#lstTopic").val(clusters);
        clearInterval(intervalCode);
      }
    }, 300);
    if(coteacherInterfaceFlag == 0)
    {

      // if for a single topic, show these reports as well
      if(parseInt(cls)>=4 && parseInt(cls)<=7)
      {
        $("#timedTestReport").hide();
        $("#dailyPracticeStarText").show();
        dailyPracticeReportAjax(dataString);
      }
      else
      {
        $("#dailyPracticeReport").hide();
        $("#dailyPracticeStarText").hide();
        timedTestReportAjax(dataString);
      }
      misconceptionsReportAjax(dataString);
      showMoreInformation("redirect");

      $("#misconceptionsReportHeading_span").text("Misconceptions in '" + topicName + "' for class " + cls + sec);
      $("#misconceptionsReportHeading").show();
    }
    
  } 
  else {
    if(coteacherInterfaceFlag == 0)
    {
      setTopicReportHeadingRedirect(cls, sec, startDate, endDate);
    }    
  }
  topicReportAjax(dataString,coteacherInterfaceFlag);
  if(legendFlag)
    $("#legendDiv").show();
  else
    $("#legendDiv").hide();
   $("#downloadAsExcelButton").removeAttr('disabled');
   $("#topicWiseProgressReportUrl").attr("href", "topicProgress.php?cls="+cls+"&section="+sec+"&ttCode="+clusters);
}

function setTopicReportHeadingRedirect(cls, sec, startDate, endDate) {
  $("#topicReportHeading").hide();

  $("#topicReportHeading_span").text("Topic report for Class " + cls + sec + " for topics attempted between " + formatDate(startDate) + " and " + formatDate(endDate));
  $("#topicReportHeading").show();
}

function topicReportAjax(dataString,coteacherInterfaceFlag) {
  $("#topicReport").show();
  $("#topicReportTable").hide();
  $("#topicReport_loading").show();
  
  if(dataString == '') {
    var dataString = $("#frmTopicReport :input").serializeArray();
    var topicName = $("[name=topic] :selected").html();  
    dataString.push({name: 'topicName', value: topicName});            
  }

  // console.log(dataString);
  var cls = getValueFromSerializedArray("class", dataString);
  var sec = getValueFromSerializedArray("section", dataString);
  var mode = getValueFromSerializedArray("mode", dataString);
  var topic = getValueFromSerializedArray("topic", dataString);
  if(coteacherInterfaceFlag == 0)
  {
     var actionUrl = "functions/topicReportAjax.php";
    setTimeout(function(){
      setTopicReportHeading(dataString);    
    },300);
    $.ajax({
      type: "POST",
      url: actionUrl,
      data: dataString,
      success: function(data) {
        // console.debug("data = " + data);
        var result = jQuery.parseJSON(data);              
        var tableData = {
          "tcolumns": [{"cid" : "flow", "cname" : "S. No"},
          {"cid" : "luName", "cname" : "Name of the Learning Unit"},
          {"cid" : "topic", "cname" : "Topic"},
          {"cid" : "numAttempted", "cname" : "Number of children that attempted it"},
          {"cid" : "percentAttempted", "cname" : "% of children that attempted it"},
          {"cid" : "accuracy", "cname" : "Accuracy of the Learning Unit"}
          ], 

          "trows": result.topicReport
        };

        if(result.topicReport.length == 0)  // For the task 11269
        {
          $(".intro-launch").hide();
          $("#topicReportavgAccuracy").hide();
          $("#topicReport_loading").hide();
          $("#topicReport_message").show();
          $("#averageAccuracySummary").hide();
          $("#topicReportTable").hide();
          legendFlag = 0;
          $("#myTopicsUrl").attr("href","#");
          $("#topicWiseProgressReportUrl").attr("href", "#");
        }
        else
        {
          var avgAccuracyText = "Average Accuracy of Learning Units: " + firstToUpperCase(result.avgAccuracyCategory);
          avgAccuracyText += (Math.round(result.avgAccuracy) > 0)? " (" + Math.round(result.avgAccuracy) + "%)" : "";
          legendFlag = 1;
          $("#topicReport_avgAccuracy").text(avgAccuracyText);
          displayTable(tableData, "topicReportTable_thead", "topicReportTable_tbody");
          makeTopicReportSortable();
          $("#topicReport_loading").hide();
          $("#topicReport_message").hide();
          $("#topicReportTable").show();
          if($("#dailyPracticeReport") && scrollToDailyPractice)
          {
            $("body").animate({
              scrollTop: $("#dailyPracticeReport").offset().top
            },500);    
            scrollToDailyPractice = 0;      
          }
        }
        if(mode == 0)
        {
            var flow = result.flow;
            $("#myTopicsUrl").unbind("click");
            $("#myTopicsUrl").attr("href","mytopics.php?ttCode="+ topic +"&cls="+cls+"&section="+sec+"&flow="+flow+"&interface=new&gradeRange=1-9");
        }
      }
    });
  }

        //To set topic page/research URL
        if(mode == 1) {
          $("#myTopicsUrl").attr("href","#");
          $("#topicWiseProgressReportUrl").attr("href", "#");

          $("#myTopicsUrl").click(function() {
  		      var alertMsg = "Topic Page/Research is available only for a single topic. Please select a topic and press Go.";	
            alert(alertMsg);
          });

          $("#topicWiseProgressReportUrl").click(function() {   // for the task 11269
            $("#topicWiseProgressReportUrl").unbind("click");
            if($("#lstTopic").val() == "")
              $("#topicWiseProgressReportUrl").attr("href", "topicProgress.php?ttCode=&cls="+cls+"&section="+sec+"&loadPageFlag=0");
            else  
            {
              cls = $("#lstClass").val();
              sec = $("#lstSection").val();
              topic = $("#lstTopic").val();
              $("#topicWiseProgressReportUrl").attr("href", "topicProgress.php?ttCode="+topic+"&cls="+cls+"&section="+sec+"&loadPageFlag=1");
            }
          });
        } else {         
          $("#topicWiseProgressReportUrl").click(function() {   // for the task 11269
            $("#topicWiseProgressReportUrl").unbind("click");
            if($("#lstTopic").val() == "")
              $("#topicWiseProgressReportUrl").attr("href", "topicProgress.php?ttCode=&cls="+cls+"&section="+sec+"&loadPageFlag=0");
            else  
            {
              cls = $("#lstClass").val();
              sec = $("#lstSection").val();
              topic = $("#lstTopic").val();
              $("#topicWiseProgressReportUrl").attr("href", "topicProgress.php?ttCode="+topic+"&cls="+cls+"&section="+sec+"&loadPageFlag=1");
            }
          });
          
        }
   
}

function setTopicReportHeading(dataString) {
  if(typeof dataString === "undefined") {
    var dataString = $("#frmTopicReport :input").serializeArray();            
  }
  $("#topicReportHeading").hide();
  var cls = getValueFromSerializedArray("class", dataString);
  var sec = getValueFromSerializedArray("section", dataString);
  var mode = getValueFromSerializedArray("mode", dataString);
  
  if(mode == 0){
    var topicName = getValueFromSerializedArray("topicName", dataString);
    if(topicName == "")     
      topicName = $("[name=topic] :selected").html();
    
    $("#topicReportHeading_span").text("Topic report for Class " + cls + sec + " for the topic: " + topicName);
  }
  $("#topicReportHeading").show();
}

function setMisconceptionsReportHeading() {
  $("#misconceptionsReportHeading").hide();
  var dataString = $("#frmTopicReport :input").serializeArray();
  var cls = getValueFromSerializedArray("class", dataString);
  var sec = getValueFromSerializedArray("section", dataString);
  var topicName = $("[name=topic] :selected").html();
  var ttCode = $("[name=topic] :selected").val();
  if(topicName == null || topicName == "" || typeof topicName === "undefined")
  {
    setTimeout(function(){
      topicName = $("[name=topic] :selected").html();
      $("#misconceptionsReportHeading_span").text("Misconceptions in '" + topicName + "' for class " + cls + sec);
    },300);
  }
  $("#misconceptionsReportHeading").show();
}

function setRedirectUrls(mode) {
  if(mode == "redirect") {
    var cls = getFromRequest("cls"); 
    var sec = getFromRequest("sec");
    var ttCode = getFromRequest("topics");
  } else {
    var dataString = $("#frmTopicReport :input").serializeArray();
    var cls = getValueFromSerializedArray("class", dataString);  
    var sec = getValueFromSerializedArray("section", dataString);
    var ttCode = getValueFromSerializedArray("topic", dataString);
  }

  // console.log("url = topicProgress.php?cls="+cls+"&section="+sec+"&ttCode="+ttCode);
  $("#topicWiseProgressReportUrl").attr("href", "topicProgress.php?cls="+cls+"&section="+sec+"&ttCode="+ttCode);
  $("#topicRemediationReportUrl").attr("href", "topicRemediationSection.php?cls="+cls+"&section="+sec+"&ttCode="+ttCode); 

  $("#cwaReportUrl").attr("href", "cwa.php?cwaType=1&cls="+cls+"&childSection="+sec+"&ttCode="+ttCode); 
}

function misconceptionsReportAjax(dataString) {
  setMisconceptionsReportHeading();
  if(typeof dataString === "undefined") {
    var dataString = $("#frmTopicReport :input").serializeArray();
    dataString.push({name: 'topicName', value: $("[name=topic] :selected").val()});
    dataString.push({name: 'topic', value: $("[name=topic] :selected").val()});
    dataString.push({name: 'topicDesc', value: $("[name=topic] :selected").html()});
  }
  var actionUrl = "functions/misconceptionsReportAjax.php";
  $.ajax({
    type: "POST",
    url: actionUrl,
    data: dataString,
    success: function(data) {
      // console.debug("data = " + data);
      $("#misconceptionsReportList").html(data);  
      $("#misconceptionsReport").show();
    }
  });        
}

function timedTestReportAjax(dataString) {
  $("#timedTestReport").show();
  $("#timedTestReportTable").hide();
  $("#timedTestReport_loading").show();
  $("#timeTestReport_message").hide();
  if(typeof dataString === "undefined") {
    var dataString = $("#frmTopicReport :input").serializeArray();
    dataString.push({name: 'topicName', value: $("[name=topic] :selected").val()});
    dataString.push({name: 'topic', value: $("[name=topic] :selected").val()});
    dataString.push({name: 'topicDesc', value: $("[name=topic] :selected").html()});
  }
  // console.log(dataString);
  var actionUrl = "functions/timedTestReportAjax.php";
  $.ajax({
    type: "POST",
    url: actionUrl,
    data: dataString,
    success: function(data) {
      // console.debug("data = " + data);
      var result = jQuery.parseJSON(data);       
      if(result.length != 0) {
        var tableData = {
          "tcolumns": [{"cid" : "sno", "cname" : "S. No"},
          {"cid" : "testName", "cname" : "Name of the Timed Test"},
          {"cid" : "topic", "cname" : "Topic"},
          {"cid" : "numAttempted", "cname" : "Number of children that attempted it"},
          {"cid" : "percentAttempted", "cname" : "% of children that attempted it"},
          {"cid" : "accuracy", "cname" : "Accuracy of the Learning Test"}
          ], 

          "trows": result
        };

        displayTable(tableData, "timedTestReportTable_thead", "timedTestReportTable_tbody");
        makeTimedTestReportSortable();
        $("#timedTestReportTable").show();

      } else {
        $("#timeTestReport_message").show();
        $("#timedTestReportTable").hide();
      }      

      $("#timedTestReport_loading").hide();

    }
  });          
}

function dailyPracticeReportAjax(dataString) {
  $("#dailyPracticeReport").show();
  $("#dailyPracticeReportTable").hide();
  $("#dailyPracticeReport_loading").show();
  $("#dailyPracticeReport_message").hide();
  if(typeof dataString === "undefined") {
    var dataString = $("#frmTopicReport :input").serializeArray();
    dataString.push({name: 'topicName', value: $("[name=topic] :selected").val()});
    dataString.push({name: 'topic', value: $("[name=topic] :selected").val()});
    dataString.push({name: 'topicDesc', value: $("[name=topic] :selected").html()});
  }
  // console.log(dataString);
  var cls = getValueFromSerializedArray("class", dataString);
  var sec = getValueFromSerializedArray("section", dataString);
  var topicName = getValueFromSerializedArray("topicDesc", dataString);
  setTimeout(function(){
    if(topicName == "" || topicName == null)     
        topicName = $("[name=topic] :selected").html();
    $("#dailyPracticeReportHeading").html("Daily Practice Report for Class "+cls+sec+" for the topic: "+topicName);
  },300);
  var actionUrl = "functions/dailyPracticeReportAjax.php";
  $.ajax({
    type: "POST",
    url: actionUrl,
    data: dataString,
    success: function(data) {
      // console.debug("data = " + data);
      var result = jQuery.parseJSON(data);       
      if(result.length != 0) {
        var tableData = {
          "tcolumns": [{"cid" : "srno", "cname" : "S. No"},
          {"cid" : "pmName", "cname" : "Daily Practice Description"},
          {"cid" : "luName", "cname" : "Learning Unit"},
          {"cid" : "studentsAttempted", "cname" : "Number of Students Attempted"},
          {"cid" : "studentsCompleted", "cname" : "Number of Students Completed"},
          {"cid" : "avgAccuracy", "cname" : "Average Accuracy"},
          {"cid" : "leastAccuracyStudents", "cname" : "<sup>*</sup> Students who need more practice"},
          ], 

          "trows": result
        };

        if(result.length == 0)
          legendFlag = 0;
        else
          legendFlag = 1;

        displayTable(tableData, "dailyPracticeReportTable_thead", "dailyPracticeReportTable_tbody");
        makeDailyPracticeReportSortable();
        $("#dailyPracticeReportTable").show();

      } else {
        $("#dailyPracticeReport_message").css({"display":"block","text-align":"center"});
        $("#dailyPracticeReportTable").hide();
      }      

      $("#dailyPracticeReport_loading").hide();
    }
  });          
}

function showMoreInformation(mode) {
  $("#moreInformation").show();
  setRedirectUrls(mode);
}

function makeTimedTestReportSortable() {
  $("#timedTestReportTable").tablesorter(); 
  $("#timedTestReportTable tr").each(function() {
      //TODO sortable icons CSS
      var accuracy = $(this).children()[5].innerHTML;
      // console.log("accuracy = " + $(this).children()[5].innerText);
      var category = categorizeAccuracy(accuracy.substring(0, accuracy.length -1));
      $(this).addClass(category);
    });
}

function makeDailyPracticeReportSortable() {
  $("#dailyPracticeReportTable").tablesorter(); 
  $("#dailyPracticeReportTable tr").each(function() {
      //TODO sortable icons CSS
      var accuracy = $(this).children()[5].innerHTML;
      // console.log("accuracy = " + $(this).children()[5].innerText);
      var category = categorizeAccuracy(accuracy.substring(0, accuracy.length -1));
      $(this).addClass(category);
    });
}

function makeTopicReportSortable() {
  $('#topicReportTable').tablesorter(); 
  $("#topicReportTable tr").each(function() {
      //TODO sortable icons CSS
      var accuracy = $(this).children()[5].innerHTML;
      var category = categorizeAccuracy(accuracy.substring(0, accuracy.length -1));
      $(this).addClass(category);
    });
}

function initIntroJs() {
  var introguide = introJs();
  var steps = [{
        element: '#topicDetails',
        intro: 'Choose Class, Section and Topic from this section. You can also download the generated report.'
      },
      {
        element: '#topicReportTable',
        intro: 'See how the different topics are doing at a class level. Pay attention to learning units with low accuracy, and address them.'
      },
      {
        element: '#timedTestReport',
        intro: 'Check the accuracy of Timed Tests in this topic and know how children are doing on some of the core concepts.'
      },      {
        element: '#misconceptionsReport',
        intro: 'View the top misconceptions that you should address immediately to improve learning levels.'
      },      {
        element: '#moreInformation',
        intro: 'Some more links to help you go into more details of this topic.'
      }
    ]; 

    var filteredSteps = new Array();

    for (var i=0; i<steps.length; i++) {
      if($(steps[i].element).is(':visible')) {
        filteredSteps.push(steps[i]);
      }
    }
    
    introguide.setOptions({
      steps: filteredSteps
    });
    introguide.start();
}