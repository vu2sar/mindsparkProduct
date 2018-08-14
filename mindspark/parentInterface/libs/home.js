//var chart;
var googleChartLoaded = false;
// Load the Visualization API and the piechart package.
google.load('visualization', '1.0', {'packages': ['corechart']});
// Set a callback to run when the Google Visualization API is loaded.
google.setOnLoadCallback(chartOnLoad);

function chartOnLoad()
{
    googleChartLoaded = true;    
//    onChartLoad();
}
//onChartLoad();

//function onChartLoad()
//{
// fire off the request to usageReport.php
$.ajax({
    url: "/mindspark/parentInterface/usageReport.php",
    type: "get",   
    cache : false,
    success: function(response, textStatus, jqXHR) {
    if (textStatus === 'success')
    {
        try {
            console.log('called 4' + googleChartLoaded);
            var responseArray = $.parseJSON(response);
            drawUsageChart(responseArray);
        }
        catch (err)
        {
            console.log(err+'\n'+response);
        }
    }
},
error: function(xmlHttpRequest, textStatus, errorThrown){
    $( document ).ready(function() {
$('#chart_divUsage').html("<div style='padding-top: 35%;text-align:center;font-weight: normal;'>Error while fetching data for usage</div>");    
});
}
});
// fire off the request to topicProgressReport.php
$.ajax({
    url: "/mindspark/parentInterface/topicProgressReport.php",
    type: "get",
    cache : false,
    success: function(response, textStatus, jqXHR) {
    if (textStatus === 'success')
    {
        try {
            console.log('called 5');
            var responseArray = $.parseJSON(response);
            drawTopicProgressChart(responseArray);
        }
        catch (err)
        {
            $( document ).ready(function() {
            $('#chart_div').html($('#topicProgressMsgDiv').html());
            console.log(err);
            });
        }
    }
},
    error: function(xmlHttpRequest, textStatus, errorThrown){
$( document ).ready(function() {
$('#chart_div').html("<div style='padding-top: 24%;text-align:center;font-weight: normal;'>Error while fetching data for topic progress</div>");    
});
}
});
//}
function drawTopicProgressChart(responseArray) {
//    responseArray= [["Topic","Progress 15 days ago","Progress till date"],["Sets",2,11],["Decimals- Operations",0,4],["Decimals- Fundamentals",0,1],["Patterns, relations and functions",16,16]];
    // Create and populate the data table.
    if ( googleChartLoaded == true)
    {
    var data = google.visualization.arrayToDataTable(responseArray);
    
    // Set chart options
    var options = {'title': 'Topic progress',
//                    'width': 500,
        height: 400,
        fontSize: 10,
//        colors: ['red','blue'],
        titleTextStyle: {fontSize: 12},
        backgroundColor: {strokeWidth: 0.7},        
        hAxis: {title: "percentage", minValue: 0, maxValue: 100, ticks: [0, 20, 40, 60, 80, 100]}
    };
    var formatter = new google.visualization.NumberFormat({pattern: "#'%"});
    formatter.format(data, 2);
    formatter.format(data, 1);
    var view = new google.visualization.DataView(data);
      view.setColumns([0, 1,{ calc: "stringify",
                         sourceColumn: 1,
                         type: "string",
                         role: "annotation" },2,
                       { calc: "stringify",
                         sourceColumn: 2,
                         type: "string",
                         role: "annotation" }]);
    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.BarChart($('#chart_div').get(0));
    chart.draw(view, options);
    }
    else
    {
            window.setTimeout(function(){ drawTopicProgressChart(responseArray);},200);
    }
}
function drawUsageChart(responseArray) {
   
if ( googleChartLoaded == true)
    {
      var data = new google.visualization.DataTable();
  data.addColumn('string', 'User');
  data.addColumn('number', 'Time spent in minutes');
//  data.addColumn({ role: 'annotation'});
  
  
//  data.addRows([
//    [String(responseArray['studentName']), responseArray['timeSpentStudent'], String(responseArray['timeSpentStudent'])],
//    ['All students in class ' + responseArray['studentClass'], responseArray['timeSpentClass'], String(responseArray['timeSpentClass'])]    
//  ]);
data.addRows([
    [String(responseArray['studentName']), responseArray['timeSpentStudent']],
    ['All students in class ' + responseArray['studentClass'], responseArray['timeSpentClass']]    
  ]);
//  var formatter = new google.visualization.NumberFormat({pattern: "#' minutes"});    
//    formatter.format(data, 1);
  var view = new google.visualization.DataView(data);
      view.setColumns([0, 1,
                       { calc: "stringify",
                         sourceColumn: 1,
                         type: "string",
                         role: "annotation" }]);
    var options = {title: "Time spent",
        height: 400,
        legend: 'none',
        showRowNumber: true,
        backgroundColor: {strokeWidth: 0.7},
        vAxis: {title: "minutes", minValue: 0, viewWindow: {min: 0},titleTextStyle: {fontName:'Arial',italic:false}}
    };
    // Create and draw the visualization.
    var chart = new google.visualization.ColumnChart(document.getElementById('chart_divUsage'));
    chart.draw(view, options);
    }
    else
    {
        console.log('usage callback');
         window.setTimeout(function(){
             drawUsageChart(responseArray);
         },200);
    }
}