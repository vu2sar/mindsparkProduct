function initDashboardLoading() {
	$("#overallUsageChart").show();
	$("#overallUsageChart_loading").show();
	$("#overallUsageChart_highlights").hide();
	$("#usagePiChart").hide();
	$("#overallUsageChart_classAvg").text("");

	$("#overallAccuracyChart").show();
	$("#overallAccuracyChart_loading").show();
	$("#overallAccuracyChart_highlights").hide();
	$("#accuracyPiChart").hide();
	$("#overallAccuracyChart_classAvg").text("");

	$("#topicProgressChart").show();
	$("#topicProgressChart_loading").show();
	$("#topicProgressChart_list").hide();

	$("#areasHandledByMS").show();
	$("#areasHandledByMS_loading").show();
	$("#areasHandledByMS_list").hide();

	$("#buildingFluencyByDP").show();
	$("#buildingFluencyByDP_loading").show();
	$("#buildingFluencyByDP_list").hide();

	$("#dashboardBottomRow").show();
	$("#dashboardBottomRow_loading").show();
	$("#impactSummary_questions").hide();
	$("#impactSummary_misconceptions").hide();
	$("#impactSummary_higherLevel").hide();
	$("#impactSummary_activities").hide();
}

function getColorForLabel(label) {
	var color = "white";
	if(label == "zero")
		color = "black";
	else if (label == "notEnoughUsage")
		color = "#999";
	else if(label == "low")
		color = '#EF2F1D';
	else if(label == "average")
		color = '#FBD00F';
	else if(label == "good")
		color = '#4180FF';
	else if(label == "great")
		color = '#218F00';

	return color;
}

function drawUsageHorBarChart(chartDataObj)
{
   var chartData = new Array();
	var tickData = new Array();
	var tt=0;
	for (var key in chartDataObj) {
		var dataLabel = key;
		var dataValue = parseInt(chartDataObj[key]);	
		chartData.push([dataValue,tt]);	
		tickData.push([tt,dataLabel]);
		tt++;				
	}

 

	drawHorBarWithLabels("placeholder", chartData,tickData);
}


function drawHorBarWithLabels(divId, chartData,tickData)
{  
 

var tickDataLabels=new Array();
var yMaxtopicCount=0

$.each(tickData,function(index,value){
var t=value[1];
yMaxtopicCount++;
tickDataLabels.push(t);

if(t.length >20)
value[1]=t.substr(0,20)+"...";	 // truncating labels to n characters....
});

 $.plot("#placeholder", [ chartData ], {
			series: {
				bars: {
					show: true,
					barWidth: 0.2,
					horizontal:true,
				}
			},
			grid: {
				hoverable: true //IMPORTANT! this is needed for tooltip to work
			},
			tooltip: {
    			show: true,
    			content: "%x ",
    		},

    		xaxis:
    		{
    			min:0,
    			tickDecimals: 0,

    			axisLabel: "Number of Questions",
    			axisLabelUseCanvas: true,
			    axisLabelFontSizePixels: 11,
			    axisLabelFontFamily: 'Helvetica',
			    axisLabelPadding: 10
    		},
    		yaxis: {
    			min:0,
    			max:yMaxtopicCount,
    			tickSize:1,
				mode: "categories",
				ticks: tickData,
				axisLabel: "Topics",
				axisLabelUseCanvas: true,
			    axisLabelFontSizePixels: 11,
			    axisLabelFontFamily: 'Helvetica',
			    axisLabelPadding: 0
			}		
	  });	

// To display axis labels using javascript.

	setTimeout(function(){
		
	 $("#placeholder .yAxis  .tickLabel").each(function(index,item){
	 $(this).attr('title',tickDataLabels[index]);
	 	
	 });
	}, 1);


}

function drawUsageBarChart(chartDataObj,tag)
{    
	
	var chartData = new Array();
	var tickData = new Array();
	var axislabeltag=tag;
	var tt=0;
	var keyArr=Object.keys(chartDataObj);
	var labelstr = new Array();

	for (var key1 in keyArr) {
	var key=keyArr[key1];
	var dataLabel = key;
	var dataValue = chartDataObj[key];
	chartData.push([dataLabel,dataValue]);
	tickData.push([tt,dataLabel]);
	tt++;
	}

	
	drawBarWithLabels("placeholder", chartData,tickData,axislabeltag);
}

function drawBarWithLabels(divId, chartData,tickData,tag)
{
  // finding out the max val accordingly converting into sec, hours or min.
   var maxVal=0;
   var label='seconds';
   var timelabel = new Array();
   var finalChartData = new Array();
   var timeunit;
   var maxtickVal=0;

    $.each(chartData,function(index,value){	
	timelabel=value.toString().split(',');
	timeunit=parseFloat(timelabel[1]);
	if(timeunit>maxVal)maxVal=parseInt(timeunit);
	
	finalChartData.push([timelabel[0],timeunit]);	
  });

 /* if(maxVal>3600)
  {   
  	var finalChartData = new Array();
 	$.each(chartData,function(index,value){
 	timelabel=value.toString().split(',');
 	timeunit=parseFloat(timelabel[1]/3600);

 	finalChartData.push([timelabel[0],timeunit]);	
    });
    label='hours'; 
  }
  else */
  if (maxVal>60) 
  {
  	var finalChartData=new Array();
    $.each(chartData,function(index,value){
   	timelabel=value.toString().split(',');
 	var timechange=timelabel[1]/60;
 	finalChartData.push([timelabel[0],timechange]);	
     });
     label='minutes';
   }

 var YtickDatalabels="Time [in "+label+"]";
 var XtickDatalabels="Duration in "+tag;
 var flag=1;

   var tickDataLabels=new Array();
	$.each(tickData,function(index,value){
	var t=value[1];
	maxtickVal++;
	if(t.indexOf('Week')==0){ t='Week '+parseInt(index+1)+':'+t.substring(4);flag=1 }
	if(t.indexOf('Month')==0){ t='Month '+parseInt(index+1)+':|'+t.substring(5);flag=0}
    if(flag)
    {     if(t.length >6)
	      value[1]=t.substr(0,6);
	      tickDataLabels.push(t);
	}
	else
	{
		  if(t.length >7)
	      value[1]=t.substr(0,7);
	      tickDataLabels.push(t);
	}
	});
  //var finalChartData2={"Week| 2 Jun 2015-3 Jun 2015":1.40,"Week| 4 Jun 2015-10 Jun 2015":0.00,"Week| 11 Jun 2015-17 Jun 2015":0.00,"Week| 18 Jun 2015-24 Jun 2015":4.92,"Week| 25 Jun 2015-1 Jul 2015":1.47}
  //console.log("Array"+finalChartData2);

  console.log("Tick"+tickData);
	$.plot("#placeholder", [ finalChartData ], {
			series: {
				bars: {
					show: true,
					barWidth: 0.3,		
				}
			},
			grid: {
				hoverable: true //IMPORTANT! this is needed for tooltip to work
			},
			tooltip: {
    			show: true,
    			content: "%y",
    		},
    		
			xaxis: {
				min:0,
				max:maxtickVal,
				tickSize:1,
				mode: "categories",
				ticks: tickData,
				axisLabel: XtickDatalabels,
    			axisLabelUseCanvas: true,
			    axisLabelFontSizePixels: 11,
			    axisLabelFontFamily: 'Helvetica',
			    axisLabelPadding: 0,
				 tickFormatter: function(number) {
                            return parseFloat(number);
                  }		
			},
			yaxis:
			{
				min:0,
				axisLabel: YtickDatalabels,
    			axisLabelUseCanvas: true,
			    axisLabelFontSizePixels: 11,
			    axisLabelFontFamily: 'Helvetica',
			    axisLabelPadding: 0,
			}
			
	  });


	setTimeout(function(){	
	 $("#placeholder .xAxis  .tickLabel").each(function(index,item){
	 $(this).attr('title',tickDataLabels[index]); 	
	 });
	}, 1);


}


function drawUsagePieChart(chartDataObj,type)
{ 
	var chartData = new Array();
	for (var key in chartDataObj) {
	
		var labelString=key;
		if(key.length>20)
		var dataLabel= key.substring(0,20);
		else	
		var dataLabel = key;
		var dataValue = parseInt(chartDataObj[key]);
			chartData.push({
				label: labelString,
				data: dataValue
			});				
		
	}
	drawPieWithLabels("pie_placeholder", chartData,type);

}

function drawPieWithLabels(divId, chartData,type) {
//	type indicates whether to show in progress or number .If it is p then show progress in % otherwise show in numbers.
	/*var data2 = [
			{ label: "Series A",  data: 5},
			{ label: "Series B",  data: 10}
		];*/
	//console.log(data2);
	//	 [Object { label="School",  data="16"}, Object { label="Home",  data="312"}]
    if(type=='p')
    {
    	var contentpie ="%p.0% - %s";
    }
    else
    var contentpie ="%y.0 - %s";
	var kk=0;

		$.plot($("#"+divId),chartData,{
				series: {
					pie: { 
						innerRadius: 0,
						show: true
					}
					
				},
				grid: {
				hoverable: true //IMPORTANT! this is needed for tooltip to work
			},
			tooltip: {
    			show: true,
    			  content: contentpie,
    		},
    		legend: {
    		show:true,
    		labelFormatter: function(label, series){
    		kk=kk+1;
			if(label.length>20)
			{
				var labelshort=label.substr(0,20)+'...';
				var hovereffect=1;
			}
			else
			{ var 	labelshort=label;var hovereffect=0;}
		    if(hovereffect==1)
	    	return "<div id=\"label"+kk+"\" onmouseover='showtooltip(this.id)' onmouseout='hidetooltip(this.id)'>" +  labelshort  + "<div id=\"labeltooltop\" style=\"display:none\" class=\"labeltool\">"  +" "+label+" " +  "</div></div>";
	    	else
	    	return "<div id=\"label"+kk+"\">" + labelshort+"</div>";
	    	
    		  }
    		}


			});
}

function showtooltip(ele)
{
	//$("#labeltooltip").css("position","absolute");
	$("#"+ele+ " .labeltool").show();
}

function hidetooltip(ele)
{
		$("#"+ele+" .labeltool").hide();
}

function labelFormatter(label,series) {
	if(label.length>20)
		var labelshort=label.substring(0,20);
	else
	var 	labelshort=label;
    return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>"    + labelshort + "<br/>" + series.data[0][1] + "%</div>";
}

function drawUsageSummaryChart(chartDataObj) {
	var chartData = new Array();
	//console.log(chartDataObj);
	for (var key in chartDataObj) {
		var dataLabel = key;
		var dataValue = chartDataObj[key];
		var dataColour = getColorForLabel(key);
		if(key != "classAvg") {
			chartData.push({
				label: dataLabel,
				data: [[1, dataValue]],
				color: dataColour
			});				
		}
	}
	
	$("#usagePiChart").show();
	drawDonutWithLabels("usagePiChart", chartData.reverse());	
	$("#overallUsageChart_classAvg").css("color", getColorForLabel(chartDataObj.classAvg));
	$("#overallUsageChart_classAvg").text(firstToUpperCase(chartDataObj.classAvg));
}

function drawDonutWithLabels(divId, chartData) {	 
	$.plot($("#"+divId), chartData,
	    {
	        series: {
	            pie: {
	                show: true,
	                innerRadius: 0.25,
	                radius:0.60,
	            //     label: {
	            //         show: true,
	            //         formatter: function(label, series){
	            //             // console.log(series);
	            //             return '<div class="chart-label" style="width:40px">'+firstToUpperCase(label)+" ("+series.data[0][1]+')</div>';
	            //         },
	            //         radius: 0.8            
	            // }
	          }
	        },
	        legend: {
	            show: true,
             	labelFormatter: function(label, series){                       
                        return firstToUpperCase(label)+" ("+series.data[0][1]+')';
                    },
                position:"se"
	        }
	    });
}

function drawAccuracySummaryChart(chartDataObj) {
	var chartData = new Array();
	for (var key in chartDataObj) {
		var dataLabel = key;
		var dataValue = chartDataObj[key];
		var dataColour = getColorForLabel(key);
		if(key != "classAvg") {
			chartData.push({
				label: dataLabel,
				data: [[1, dataValue]],
				color: dataColour
			});				
		}
	}

	$("#accuracyPiChart").show();
	drawDonutWithLabels("accuracyPiChart", chartData.reverse());	
	$("#overallAccuracyChart_classAvg").css("color", getColorForLabel(chartDataObj.classAvg));
	var title = chartDataObj.classAvg;    
	if(chartDataObj.classAvg == "notEnoughUsage")
		title = "not enough usage";
	$("#overallAccuracyChart_classAvg").text(firstToUpperCase(title));
	$("#overallAccuracyChart").show();	
}

function getFirstThreeNames(nameArr, hasQuotes) {
	if(typeof(hasQuotes) == 'undefined') hasQuotes = false;
	var str = "";
	for(var i = 0; i < nameArr.length; i++) {
		if(hasQuotes) 
			str += "'" + nameArr[i] + "'";
		else
			str += nameArr[i];

		if(i==Math.min(2, nameArr.length-1))
			break;
		else if(i==1) {
			str += " and ";
		}
		else 
			str += ", ";
	}

	return str;
}

function getLastThreeNames(nameArr, hasQuotes) {
	if(typeof(hasQuotes) == 'undefined') hasQuotes = false;
	var str = "";
	for(var i = nameArr.length-1; i >= 0; i--) {
		if(hasQuotes) 
			str += "'" + nameArr[i] + "'";
		else
			str += nameArr[i];

		if(i==Math.max(nameArr.length-3, 0))
			break;
		else if(i==nameArr.length-2) {
			str += " and ";
		}
		else 
			str += ", ";
	}
	return str;
}

function drawUsageSummaryHighlights(result) {
		// console.log("inside drawUsageSummaryHighlights>>>>>>");
	if(isAnyUsageHighlights(result)) {
		$("#overallUsageChart_highlights").show();
		// console.log("low usage length = " + (result.lowUsageNames).length);
		if((result.lowUsageNames).length != 0) {			
			$("#overallUsageChart_highlightsLowUsage").show();
			$("#overallUsageChart_highlightsLowUsageNames").text(getFirstThreeNames(result.lowUsageNames));
		} else {
			$("#overallUsageChart_highlightsLowUsage").hide();
		}

		if((result.lowAccuracyNames).length != 0) {			
			$("#overallUsageChart_highlightsLowAccuracy").show();
			$("#overallUsageChart_highlightsLowAccuracyNames").text(getLastThreeNames(result.lowAccuracyNames));
		} else {
			$("#overallUsageChart_highlightsLowAccuracy").hide();
		}

		if((result.allTopicsCompletedNames).length != 0) {			

			$("#overallUsageChart_highlightsAllTopicsComplete").show();
			$("#overallUsageChart_highlightsAllTopicsCompleteNames").text(getFirstThreeNames(result.allTopicsCompletedNames));
		} else {
			$("#overallUsageChart_highlightsAllTopicsComplete").hide();
		}

		if((result.numerousAttemptsFailureNames).length != 0) {			
			$("#overallUsageChart_highlightsNumerousFailures").show();
			$("#overallUsageChart_highlightsNumerousFailuresNames").text(getFirstThreeNames(result.numerousAttemptsFailureNames));
		} else {
			$("#overallUsageChart_highlightsNumerousFailures").hide();
		}			
	}
}

function isAnyUsageHighlights(result) {
	var count = 0;
	count = (result.lowUsageNames).length + (result.lowAccuracyNames).length + (result.allTopicsCompletedNames).length +      	(result.numerousAttemptsFailureNames).length;
	// console.log("count =" + count);
	if (count > 0) {
		return true;			
	}
	return false;
}

function drawAccuracySummaryHighlights(result) {

	if(isAnyAccuracyHighlights(result)) {
		$("#overallAccuracyChart_highlights").show();
		if((result.greatAccuracyClusters).length != 0) {			
			$("#overallAccuracyChart_highlightsGreatAccuracy").show();
			$("#overallAccuracyChart_highlightsGreatAccuracyNames").text(getFirstThreeNames(result.greatAccuracyClusters, true));
		} else {
			$("#overallAccuracyChart_highlightsGreatAccuracy").hide();
		}

		if((result.lowAccuracyClusters).length != 0) {			
			$("#overallAccuracyChart_highlightsLowAccuracy").show();
			$("#overallAccuracyChart_highlightsLowAccuracyNames").text(getLastThreeNames(result.lowAccuracyClusters, true));
		} else {
			$("#overallAccuracyChart_highlightsLowAccuracy").hide();
		}

		if((result.misconceptionsIdentified).length != 0) {			

			$("#overallAccuracyChart_highlightsMisconceptionsIdentified").show();
			$("#overallAccuracyChart_highlightsMisconceptionsIdentifiedNames").text(getFirstThreeNames(result.misconceptionsIdentified, true));
		} else {
			$("#overallAccuracyChart_highlightsMisconceptionsIdentified").hide();
		}			
	} 
}

function isAnyAccuracyHighlights(result) {
	var count = 0;
	count = (result.greatAccuracyClusters).length + (result.lowAccuracyClusters).length + (result.misconceptionsIdentified).length;
	if (count > 0) {
		return true;			
	}
	return false;
}

function initDashboard() {
	$("#overallUsageChart").hide();
	$("#overallAccuracyChart").hide();
	$("#areasHandledByMS").hide();
	$("#topicProgressChart").hide();
	$("#dashboardBottomRow").hide();
	$("#dashboardReportTitle").hide();
}

function getUsageSummaryAjax(dataString) {
	var actionUrl = "functions/overallUsageAjax.php";
	// console.log(dataString);
	var cls = getValueFromSerializedArray("class", dataString);
	var sec = getValueFromSerializedArray("section", dataString);
	var startDate = (getValueFromSerializedArray("dateRange", dataString)).split("~")[0];
	var endDate = (getValueFromSerializedArray("dateRange", dataString)).split("~")[1];
	// console.log("myStudents.php?class="+cls+"&section="+sec+"&tillDate="+endDate+"&fromDate="+startDate);
	$("#overallUsageChart_studentWiseReportUrl").attr("href", "myStudents.php?class="+cls+"&section="+sec+"&tillDate="+endDate+"&fromDate="+startDate);
	$.ajax({
		type: "POST",
		url: actionUrl,
		data: dataString,
		success: function(data) {
			// console.debug("data = " + data);
			var result = jQuery.parseJSON(data);
			var chartDataObj = result.usageSummaryGraphDetails;
			$("#overallUsageChart_loading").hide();
			drawUsageSummaryChart(chartDataObj);
			drawUsageSummaryHighlights(result);
			drawZeroUsageTable(result.zeroUsageNames);
		}
	});
}

function drawZeroUsageTable(namesArr) {
	var trows = new Array();

	for(var i=0; i<namesArr.length; i++) {
		trows.push({"sno": i+1, "name": namesArr[i]});
	}

	var tableData = {
          "tcolumns": [{"cid" : "sno", "cname" : "S. No"},
          {"cid" : "name", "cname" : "Name"}
          ], 

					"trows": trows         
        };
  if(trows.length > 0) {
  	$("#zeroUsageTableDiv").show();
  	displayTable(tableData, "zeroUsageTable_thead", "zeroUsageTable_tbody");  	
  } else {
  	$("#zeroUsageTableDiv").hide();
  }
}

function drawHigherLevelStudentsTable(namesArr) {
	var trows = new Array();
	for(var i=0; i<namesArr.length; i++) {
		trows.push({"sno": i+1, "name": namesArr[i]});
	}

	var tableData = {
          "tcolumns": [{"cid" : "sno", "cname" : "S. No"},
          {"cid" : "name", "cname" : "Name"}
          ], 

					"trows": trows         
        };
  if(trows.length > 0) {
   	$("#higherLevelTableDiv").show();
  	displayTable(tableData, "higherLevelTable_thead", "higherLevelTable_tbody");
	} else {
   	$("#higherLevelTableDiv").hide();		
	}
}

function getAccuracySummaryAjax(dataString) {
	var actionUrl = "functions/overallAccuracyAjax.php";

	$.ajax({
		type: "POST",
		url: actionUrl,
		data: dataString,
		success: function(data) {
			// console.debug("data = " + data);
			var result = jQuery.parseJSON(data);
			var chartDataObj = result.accuracySummaryGraphDetails;

			$("#overallAccuracyChart_loading").hide();
			if(chartDataObj.hasOwnProperty("great") || chartDataObj.hasOwnProperty("good") || chartDataObj.hasOwnProperty("notEnoughUsage") || chartDataObj.hasOwnProperty("low")) {
				drawAccuracySummaryChart(chartDataObj);				
			} else {
				$("#overallAccuracyChart_classAvg").css("color", getColorForLabel("notEnoughUsage"));
				$("#overallAccuracyChart_classAvg").text("Not Enough Usage");
				$("#accuracyPiChart").html("<p class='pText'> No child has attempted a question in the selected date range.");
				$("#accuracyPiChart").show();
				$("#overallAccuracyChart").show();
			}
			drawAccuracySummaryHighlights(result);
			setTopicReportUrl(result.clusterList);
			}
		});
}

function setTopicReportUrl(clusterList) {
	var dataString = $("#frmTeacherReport :input").serializeArray();
	var cls = getValueFromSerializedArray("class", dataString);
	var sec = getValueFromSerializedArray("section", dataString);
	var startDate = (getValueFromSerializedArray("dateRange", dataString)).split("~")[0];
	var endDate = (getValueFromSerializedArray("dateRange", dataString)).split("~")[1];
	var schoolCode = getValueFromSerializedArray("schoolCode", dataString);
	var coteacherInterfaceFlag = getValueFromSerializedArray("coteacherInterfaceFlag", dataString);

	if(coteacherInterfaceFlag == 0)
	{		
		var tail = "?schoolCode="+schoolCode+"&cls="+cls+"&sec="+sec+"&topics="+clusterList+"&startDate="+startDate+"&endDate="+endDate+"&mode=1"; 
		$("#overallAccuracyChart_topicWiseReportUrl").html("<a href=\"topicReport.php"+ tail +"\" target=\"_blank\">Topic Accuracy Report</a>");
	}
	else
	{
		$("#overallAccuracyChart_topicWiseReportUrl").html("<a href=\"javascript:void(0)\" onclick=\"getAccuracyOfLearningUnits("+cls+",'"+sec+"','"+startDate+"','"+endDate+"','getAccuracyOfLearningUnits')\">Accuracy of Learning Units</a>");
	}
	
	$("#printableReports_accuracyReport").attr("clusters", clusterList);
	$("#printableReports_accuracyReport").attr("startDate", startDate);
	$("#printableReports_accuracyReport").attr("endDate", endDate);
}
function getAccuracyOfLearningUnits(cls,sec,startDate,endDate,mode)
{
	var dataString = {
		'cls' : cls,
		'section' : sec,
		'startDate' : startDate,
		'endDate' : endDate,
		'mode' : mode
	};	
	var html = '';
	$('.fade').show();
	html = '<div class="details-loader"></div>';
	$(".modal-body").html(html);	
	$.ajax({
		type: "GET",
		url: "topicReportController.php",
		data: dataString,
		dataType: "json",
		success: function(data) {
			showLearningUnit(data);
		}
	});
	var trackingString = {
		'pageUrl': 'classLevelReport.php/learningUnitSummary',
        'type' : 'teacherInterface',
        'userID' : $("#userID").val(),
        'sessionID' : $("#sessionID").val()  
	};	
	$.ajax({
		type: "POST",
		url: "ajaxRequest.php?mode=trackInterface",
		data: trackingString,
		success: function(data) {			
		}
	});
}
function closeLearningUnit(){
	$('.fade').hide();
}
function getAreasHandledByMSAjax(dataString, cls, sec) {
	var actionUrl = "functions/areasHandledByMSAjax.php";
	var cls = getValueFromSerializedArray("class", dataString);
	var sec = getValueFromSerializedArray("section", dataString);

	$.ajax({
		type: "POST",
		url: actionUrl,
		data: dataString,
		cls_value: cls,
		sec_value: sec,
		success: function(data) {	
			// console.log("areasHandledByMS=" + data);
			var result = jQuery.parseJSON(data);

			$("#areasHandledByMS_loading").hide();
			$("#areasHandledByMS_list").show();	

			if(result.mostClearedRemedial.remedialItemDesc != null) {
				$("#areasHandledByMS_remedialItemDesc").text("'" + result.mostClearedRemedial.remedialItemDesc + "'");
				$("#areasHandledByMS_topicName").text("'" + result.mostClearedRemedial.topicName + "'");
				$("#areasHandledByMS_remedialItemUrl").attr("href", "../userInterface/remedialItem.php?qcode="+result.mostClearedRemedial.remedialItemCode);					
				$("#areasHandledByMS_misconception").show();
			} else {
				$("#areasHandledByMS_misconception").hide();
			}

			if(result.mostAttemptedActivity.gameDesc != null) {
				$("#areasHandledByMS_mostAttemptedActivity").text("'" + result.mostAttemptedActivity.gameDesc + "'");
				$("#areasHandledByMS_mostAttemptedActivityUrl").attr("href", "../userInterface/enrichmentModule.php?gameID="+result.mostAttemptedActivity.gameID);
				$("#areasHandledByMS_classSection").text(this.cls_value + this.sec_value);
				$("#areasHandledByMS_activity").show();
			} else {
				$("#areasHandledByMS_activity").hide();
			}

			if(result.top3MostAttemptedPracticeModules.length != 0)
			{
				var dataString = $("#frmTeacherReport :input").serializeArray();
				var cls = getValueFromSerializedArray("class", dataString);
				var sec = getValueFromSerializedArray("section", dataString);
				var startDate = (getValueFromSerializedArray("dateRange", dataString)).split("~")[0];
				var endDate = (getValueFromSerializedArray("dateRange", dataString)).split("~")[1];
				var schoolCode = getValueFromSerializedArray("schoolCode", dataString);
				var html = "";
				var hoverText = "";
				for(i=0;i<result.top3MostAttemptedPracticeModules.length;i++)
				{
					var url = "topicReport.php?schoolCode="+schoolCode+"&cls="+cls+"&sec="+sec+"&topics="+result.top3MostAttemptedPracticeModules[i].topic+"&startDate="+startDate+"&endDate="+endDate+"&mode=0";
					hoverText = result.top3MostAttemptedPracticeModules[i].studentsAttempted+" out of "+result.top3MostAttemptedPracticeModules[i].totalStudents+" students attempted the module."
					html += "<li style='margin-bottom:3px;'><a href='"+url+"' title='"+hoverText+"' target='_blank'>"+result.top3MostAttemptedPracticeModules[i].practiceModuleName+"</a></li>";
				}
				$("#buildingFluencyByDP_list ul").html(html);
				$("#buildingFluencyDiv").show();
				$("#buildingFluencyByDP_loading").hide();
				$("#buildingFluencyByDP_list").show();
			}
			else
			{
				$("#buildingFluencyDiv").hide();
			}

		  $("#areasHandledByMS_commonAnswerReportUrl").attr("href", "cwa.php?cwaType=1&cls="+cls+"&childSection="+sec); 
			$("#areasHandledByMS").show();
		}
	});
}

function drawTopicProgressSummaryChart(result,cls,sec) {
	$("img.canvas-image").remove();
	var numOfTopics = result.ttProgress.length;
	if(numOfTopics == 0) {
		$("#topicProgressChart_noTopics").show();
		$("#topicProgressChart_scale").hide();
	} else {
		$("#topicProgressChart_noTopics").hide();
		$("#topicProgressChart_scale").show();
	}

	if(numOfTopics < 3) {
		for(var i = numOfTopics+1; i <= 3; i++) {
			$("#topicProgressChart_topicDesc" + i).hide();
			$("#topicProgressChart_canvas" + i).hide();
		}
	}

	for(var i = 1; i <=numOfTopics; i++) {
		var barWidth = 15;
		var progress_link = "topicProgress.php?ttCode="+result.ttProgress[i-1].ttCode+"&cls="+cls+"&section="+sec;
		var startPoint = result.ttProgress[i-1].startProgress;
		var endPoint = result.ttProgress[i-1].endProgress;
		var currentPoint = result.ttProgress[i-1].currentProgress;
		var topicDescription = result.ttProgress[i-1].ttDesc;
		$("#topicProgressChart_topicDesc" + i).text(result.ttProgress[i-1].ttDesc).show();
		$("#topicProgressChart_canvas" + i).show();
		if(i<=3)
		{
			$("#topicProgressChart_topicDesc" + i).wrap('<a title="Click to see the progress report of '+topicDescription+'." href="' + progress_link + '" />');
			$("#topicProgressChart_canvas" + i).wrap('<a title="Click to see the progress report of '+topicDescription+'." href="' + progress_link + '" />');
		}
		drawProgressBar("topicProgressChart_canvas" + i, startPoint, endPoint, currentPoint, barWidth);
		if(i == 3) {
			break;
		}
	}
	drawProgressBar("topicProgressScale",20,40,60,barWidth);

	$("#topicProgressChart").show(); 
}

function getTopicProgressSummaryAjax(dataString) {
	var actionUrl = "functions/topicProgressAjax.php";
	$.ajax({
		type: "POST",
		url: actionUrl,
		data: dataString,
		success: function(data) {
			// console.debug("data = " + data);
			var result = jQuery.parseJSON(data);
			$("#topicProgressChart_loading").hide();
			$("#topicProgressChart_list").show();
			var cls = getValueFromSerializedArray("class", dataString);
			var sec = getValueFromSerializedArray("section", dataString);

			drawTopicProgressSummaryChart(result,cls,sec);

			if(sec != "")
			{
				document.cookie = "section="+sec;
			}
			document.cookie = "dateRange="+getValueFromSerializedArray("dateRange", dataString);
			var schoolCode = getValueFromSerializedArray("schoolCode", dataString);
			var ttCode =  (result.ttProgress.length > 0)? result.ttProgress[0].ttCode: "";
			var topicName = (result.ttProgress.length > 0)? result.ttProgress[0].ttDesc: "";

			setTopicReportPrintParams(schoolCode, cls, sec, ttCode, topicName);
			//Setting total higher level reached
			$("#impactSummary_higherLevelTotal").html("Total &mdash; " + result.totalHigherLevelReached);
			drawHigherLevelStudentsTable(result.higherLevelStudents);
			}
		});
}

function setTopicReportPrintParams(schoolCode, cls, sec, ttCode, topicName) {
	$("#printableReports_topicReport").attr("schoolCode", schoolCode);
	$("#printableReports_topicReport").attr("cls", cls);
	$("#printableReports_topicReport").attr("sec", sec);
	$("#printableReports_topicReport").attr("ttCode", ttCode);	
	$("#printableReports_topicReport").attr("ttName", topicName);	
}

function drawRectangle(ctx, fillColor, x0, y0, x1, y1) {
	var lineWidth=0.1;
	ctx.fillStyle = fillColor; 
	ctx.shadowColor = '#AAA';
    ctx.fillRect(x0+lineWidth, y0+lineWidth, x1-lineWidth, y1-lineWidth);
    ctx.lineWidth=lineWidth;
    ctx.strokeRect(x0+lineWidth, y0+lineWidth, x1-lineWidth, y1-lineWidth);
  }

  function drawProgressBar(canvasId, startPoint, endPoint, currentPoint, barWidth) {
  	var c = $("#"+canvasId)[0];
  	var ctx = c.getContext('2d');
  	ctx.clearRect(-40, 0, c.width+40, c.height);
  	var sf = 3.4;
  	// console.log(sf);
  	if(startPoint < 0.1 && endPoint < 0.1) {
  		startPoint = 0.1;
  		endPoint = 0.2;
  	}	//To ensure left side border is not empty

  	drawRectangle(ctx, '#8CC63F', 25, 5, Math.round(startPoint*sf), barWidth); // earlier
  	drawRectangle(ctx, '#009245', Math.round(startPoint*sf)-2+25, 5, Math.round(endPoint*sf) - Math.round(startPoint*sf), barWidth);  // progress in range
  	drawRectangle(ctx, '#D1D1D1', Math.round(endPoint*sf)-2+25, 5, Math.round(100*sf) - Math.round(endPoint*sf), barWidth); //background bar
  	//console.log(endPoint);

  	
  	drawRectangle(ctx, '#8CC63F', Math.round(endPoint*sf)-2+25, 5, Math.round(currentPoint*sf) - Math.round(endPoint*sf), barWidth); /// current progress
  	if(canvasId !="topicProgressScale"){
  		drawProgressRangeTriangle(ctx, Math.round(endPoint*sf)+25, '#009245', barWidth, endPoint - startPoint,0);
  		drawCurrentPositionTriangle(ctx, Math.round(currentPoint*sf)+25, '#8CC63F', barWidth,currentPoint,0); 
  	}	 
  	else
  	{
  		drawProgressRangeTriangle(ctx, Math.round(endPoint*sf)+25, '#009245', barWidth, "Progress in range - ",1);
  		drawCurrentPositionTriangle(ctx, Math.round(currentPoint*sf)+25, '#8CC63F', barWidth,"Current Progress - ",1);
  	}
  }

  function drawProgressRangeTriangle(ctx, currentPoint, fillColor, barWidth, endProgress,flagForExplanation) {
  	ctx.beginPath();
  	ctx.moveTo(currentPoint-2, barWidth+5);
  	ctx.lineTo(currentPoint-2, barWidth+15);
  	ctx.lineTo(currentPoint-7, barWidth+15);
  	ctx.moveTo(currentPoint-2, barWidth+15);
  	ctx.lineTo(currentPoint-2, barWidth+30);
  	if(!flagForExplanation)
  	{
  		 	ctx.lineTo(currentPoint-27, barWidth+30);
  			ctx.lineTo(currentPoint-27, barWidth+15);
  	}
  	else
  	{
  			ctx.lineTo(currentPoint-127, barWidth+30);
  			ctx.lineTo(currentPoint-127, barWidth+15);
  	}
 
  	ctx.fillStyle = fillColor;
  	ctx.fill();
  	ctx.fillStyle = 'white';
  	if(!flagForExplanation)
  		ctx.fillText(Math.round(endProgress)+"%",currentPoint-25,barWidth+25);
  	else
  		ctx.fillText("% Progress in the range",currentPoint-120,barWidth+25);
  }

  function drawCurrentPositionTriangle(ctx, currentPoint, fillColor, barWidth, currentProgress, flagForExplanation) {
  	ctx.beginPath();
  	ctx.moveTo(currentPoint, barWidth+5);
  	ctx.lineTo(currentPoint, barWidth+15);
  	ctx.lineTo(currentPoint+5, barWidth+15);
  	ctx.moveTo(currentPoint, barWidth+15);
  	ctx.lineTo(currentPoint, barWidth+30);
  	if(!flagForExplanation)
  	{
  		currentPointx=currentPoint+25;
  		if (Math.round(currentProgress)>=100)
  			currentPointx=currentPoint+32;
  		ctx.lineTo(currentPointx, barWidth+30);
  		ctx.lineTo(currentPointx, barWidth+15);
  	}
  	else
  	{
  		ctx.lineTo(currentPoint+100, barWidth+30);
  		ctx.lineTo(currentPoint+100, barWidth+15);
  	}
  	
  	ctx.fillStyle = fillColor;
  	ctx.fill();
  	ctx.fillStyle = 'black';
  	
  	if(!flagForExplanation)
  		ctx.fillText(Math.round(currentProgress)+"%",currentPoint+3,barWidth+25);
  	else
  		ctx.fillText("% Current progress",currentPoint+5,barWidth+25);
  }

  function getImpactSummaryAjax(dataString) {
  	var actionUrl = "functions/impactSummaryAjax.php";
  	$.ajax({
  		type: "POST",
  		url: actionUrl,
  		data: dataString,
  		success: function(data) {
  			var result = jQuery.parseJSON(data);
  			$("#dashboardBottomRow_loading").hide();
  			$("#impactSummary_questions").show();
  			$("#impactSummary_misconceptions").show();
  			$("#impactSummary_higherLevel").show();
  			$("#impactSummary_activities").show();

  			var totalQuestions = result.questions.total;
  			if(!totalQuestions) {
  				totalQuestions = 0;
  			}

  			$("#impactSummary_questionsTotal").html("Total &mdash; " + totalQuestions + " <br /> " + "Average &mdash; " + result.questions.average);

  			$("#impactSummary_misconceptionsTotal").html("Total &mdash; " + result.remedials); 
  			if(result.remedials == 0) {
  				$("#impactSummary_misconceptions").hide();
  			}
  			$("#impactSummary_activitiesTotal").html("Total &mdash; " + result.activities); 	 
			}
		});
  }

  function generatePrintableReport() {

  	var dialog = $( "#printLoading" ).dialog({
  	  autoOpen: true,
  	  height: 300,
  	  width: 300,
  	  modal: true,
  	  dialogClass: "no-close"
  	});

		$("#classLevelReportPrintButton").hide();
		dialog.dialog( "open" );


// Added by Jayanth, Modified by Shashanka for Chrome43 issue: begin

// --------If this works, similar solution needs to be replicated for each canvas---
// Code to replace all required canvas images with img tags

		$("img.canvas-image").remove();

		$('canvas.base').each(function(){
			var q=$(this).get(0);
			var imgUrl=q.toDataURL();
			var w=$(this).attr('width'),h=$(this).attr('height');
			$(this).replaceWith('<img src="'+imgUrl+'" width="'+w+'" height="'+h+'"/>');
		});

		$('canvas.base2').each(function(){
			var q=$(this).get(0);
			var imgUrl=q.toDataURL();
			var w=$(this).attr('width'),h=$(this).attr('height');
			$('<img class="topic-progress-chart canvas-image" src="'+imgUrl+'" width="'+w+'" height="'+h+'"/>').insertAfter(this);
			// $(this).hide();
		});

// Added by Jayanth, Modified by Shashanka for Chrome43 issue: end  	
//Topic Report
		var schoolCode = $("#printableReports_topicReport").attr("schoolCode");
		var cls = $("#printableReports_topicReport").attr("cls");
		var sec = $("#printableReports_topicReport").attr("sec");
		var ttCode = $("#printableReports_topicReport").attr("ttCode");
		var topicName = $("#printableReports_topicReport").attr("ttName");
		// console.log(schoolCode + "::::::" + topicName);
		var docTitle ='ClassReport_'+cls; 
		docTitle += (sec != "")? '_'+sec:'';
		document.title = docTitle;

		$("#printableReports_topicName").text("Topic Report for Topic: " + topicName);

		var dataString = new Array();
		dataString.push({name: 'schoolCode', value: schoolCode});
		dataString.push({name: 'class', value: cls});
		dataString.push({name: 'section', value: sec});
		dataString.push({name: 'topic', value: ttCode});
		dataString.push({name: 'mode', value: 0});

		var actionUrl = "functions/topicReportAjax.php";
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

		    if(result.topicReport.length > 0) {
		    	$("#printableReports_topicReport").show();
		    displayTable(tableData, "topicReportTable_thead", "topicReportTable_tbody");
		    } else {
		    	$("#printableReports_topicReport").hide();
		    }

		    //Accuracy Report
		    var clusters = $("#printableReports_accuracyReport").attr("clusters");
		    var startDate = $("#printableReports_accuracyReport").attr("startDate");
		    var endDate = $("#printableReports_accuracyReport").attr("endDate");
		    var mode = 1;

		    $("#printableReports_accuracyReportHeading").text("Topic report for Class " + cls + sec + " for topics attempted between " + formatDate(startDate) + " and " + formatDate(endDate));

		    var dataString2 = new Array();
		    dataString2.push({name: 'schoolCode', value: schoolCode});
		    dataString2.push({name: 'class', value: cls});
		    dataString2.push({name: 'section', value: sec});
		    dataString2.push({name: 'topic', value: clusters});
		    dataString2.push({name: 'mode', value: mode});
		    dataString2.push({name: 'startDate', value: startDate});
		    dataString2.push({name: 'endDate', value: endDate});
		    $.ajax({
		      type: "POST",
		      url: actionUrl,
		      data: dataString2,
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
		        if(result.topicReport.length > 0) {
			        $("#printableReports_accuracyReport").show();
			      	displayTable(tableData, "accuracyReportTable_thead", "accuracyReportTable_tbody");
		        } else {
		        	$("#printableReports_accuracyReport").hide();
		        }
  				
  				$.post(
  					"myStudentAjax.php",
  					"class="+cls+"&section="+sec + "&fromDate="+startDate+"&tillDate="+endDate+"&schoolCode="+schoolCode,
  					function(data) { 
    					// $("#printableReports_studentUsageReport").text("Student-wise Usage Report"); 
    					$("#section3_reportContainer").html(data);
							dialog.dialog( "close" );
		      		launchPrint();
							$("#classLevelReportPrintButton").show();
  					}
  				);
		    }
		  });
			}
		});
  }

  function launchPrint() {
  	$("#printableReports").show();
  	$("#container").css("display","block");		// for firefox issue 10666
  	window.print();
  	$("#container").css("display","inline-block");		// for firefox issue 10666
  	$("#printableReports").hide();
  }

 function getformattedDate(dd1)
 {
	var p = dd1.split("-");
	var date = p[2]+"/"+p[1]+"/"+p[0];
	return date;
 }
 function getformattedDate2(dd)
 {
	var p = dd.split("-");
	var date = p[2]+"-"+p[1]+"-"+p[0];
	return date;

 }


  function generateClassLevelReportRedirect(fDate,tDate) {
  	var schoolCode = getFromRequest("schoolCode");
  	var cls = getFromRequest("cls");
  	var sec = getFromRequest("section");
  	var dateRange=fDate+"~"+tDate;

  

  	if(typeof(fDate)== "undefined" || fDate==="" || typeof(tDate)=="undefined"|| tDate==="") {
  		dateRange = getDate(-6) + "~" + getDate(0);
  		fDate=getDate(-6);
  		tDate=getDate(0);
  	}
   
  	var dataString = new Array();
  	dataString.push({name: 'class', value: cls});
  	dataString.push({name: 'section', value: sec});
  	dataString.push({name: 'dateRange', value: dateRange});
  	dataString.push({name: 'schoolCode', value: schoolCode});

  	/*$("#lstClass").val(cls);			// for the bug 12434
  	$("#lstSection").val(sec);*/
	var values=new Array(); // Array of storing options for dateRange
	var flag=0;
	 values = $("#dateRange>option").map(function() { 
	 	if($(this).val()==dateRange)
	 		flag=1;
	 	//alert($(this).val()); 
	 });
	
	if(flag)
  	$("#dateRange").val(dateRange);
  	else
  	{ 
  	var fromnewdate=getformattedDate(fDate);
  	var tillnewdate=getformattedDate(tDate);
  	var selnewdate=fromnewdate+" - "+tillnewdate +"(Change)";
  	$('#dateRange option:contains("Custom Range")').val(dateRange);
  	$('#dateRange option:contains("Custom Range")').attr("selected","selected");
  	$('#dateRange option:contains("Custom Range")').text(selnewdate);
  	var pickerfromnewdate=getformattedDate2(fDate);
  	var pickertonewdate=getformattedDate2(tDate);
  	$('#fromDate').val(pickerfromnewdate);
  	$('#toDate').val(pickertonewdate);
  	//$('#dateRange option:contains(selnewdate)')
     }
   
  	initDashboardLoading();

  	
if(typeof(fDate)=="undefined" || fDate===""|| typeof(tDate)=="undefined"|| tDate==="") {
  	  	$("#dashboardReportTitle_span").text("Class Report for class " + cls + sec + " from " + formatDate(getDate(-7)) + " to " + formatDate(getDate(0)));
 }
 else
 {
 	 $("#dashboardReportTitle_span").text("Class Report for class " + cls + sec + " from " + formatDate(fDate) + " to " + formatDate(tDate));
 }

  	$("#dashboardReportTitle").show();
  	getUsageSummaryAjax(dataString);
  	getAccuracySummaryAjax(dataString);
  	getAreasHandledByMSAjax(dataString, cls, sec);
  	getTopicProgressSummaryAjax(dataString);
  	getImpactSummaryAjax(dataString);
  }


function showLearningUnit(learning_data){
	$(".modal-title").html(learning_data.heading);
	var html = '';
	var chartArr = [];
	
	var index=0;
	if(!jQuery.isEmptyObject(learning_data.learningUnitDetails))
	{

		$.each(learning_data.learningUnitDetails, function(key, topic){
			html += '<div class="topic-header">';
				html += '<span class="topic-heading topic-name align-left"> Learning Units of '+ topic.name + '</span>';
				html += '<span class="align-right topic-heading">Overall Accuracy</span>'; 
			html += '</div>';
			html += '<div class="topic-details">';
				$.each(topic.clusterDetails, function(clusterKey, clusterObj){
					var rowHtml = createLearningUnitRow(key+'-'+clusterKey,clusterObj);
					html += rowHtml.replace('#id#', key+'-'+clusterKey);
					chartArr[index] = {
						canvas : key+'-'+clusterKey,
						data : clusterObj.accuracy,
						dash : clusterObj.dash
					}
					index++;
				});
			html += '</div>';
		});
	}
	else
	{
		html += '<div class="noLuData">No learning units attempted in the given date range.</div>';
	}
	$(".modal-body").html(html);	
	for(var i=0;i<chartArr.length;i++){
		createProgress($("#"+chartArr[i].canvas),chartArr[i].data,chartArr[i].dash);
	}
}

function createLearningUnitRow(key,clusterObj){
	var className = '';
	var canvasClassName = '';
	if(clusterObj.tick == 1){
		className = 'check-mark';
	}
	if(clusterObj.dash == 1){
		canvasClassName = 'dash-mark';
	}
	var html = '';
	html += '<div class="learning">';
		html += '<div class="checkbox middle width-5 text-right display-table inline">';
			html += '<div class="ghost">';
			html += '</div>';
			html += '<label class="middle">';
				html += '<span class="checkbox-material"><button class="check '+ className +'" data-id="tooltipTab-'+key+'" onclick="openTooltip(this,1)"></button>';
				html +=	'<div class="arrow-white-side tooltipTab-'+key+'" >';
					html += '<div id="activateTab">On successful completion of Learning Unit by atleast 75% of the class.</div>';								
					html += '</div> ';
				html +='</span>';
			html += '</label>';
				html += '<div class="learning-points"></div>';
		html += '</div>';
		html += '<div class="learning-list middle width-93 display-table inline">';
			html += '<div class="middle inline-table" style="width:90%">';
				html += '<span class="font-style">' + clusterObj.cluster + '</span>';
			html += '</div>';
			html += '<div class="middle inline-table" style="width:10%;position: relative;">';
				html += '<canvas id="#id#" width="50" height="50" class="checkCanvas '+canvasClassName+'" data-id="tooltipTab_'+key+'" onclick="openTooltip(this,'+clusterObj.dash+')"></canvas>';
				html +=	'<div class="arrow-right-side tooltipTab_'+key+'" >';
					html += '<div id="activateTab">Accuracy will appear when atleast 50% of the class has completed the learning unit.</div>';								
					html += '</div> ';
				html +='</span>';
			html += '</div>';
		html += '</div>';
	html += '</div>';

	return html;
}
function openTooltip(div,mark) {
	if(!mark)
		return;
	else
	{

		var currentID = div.dataset.id;
		$(".arrow-right-side,.arrow-white-side").css("display","none");
		$("."+currentID).css("display","block");
	}
}
function createProgress(canvas, data, dash){
    var fillStyle = "";
    if(dash == 1){
        fillStyle = '#E7E7E7';
        data = 0;
    }
    else if(data < 40){
        fillStyle = '#FC6D25';
    }
    else if(data < 80){
        fillStyle = '#F7D735';
    }
    else{
        fillStyle = '#5CD319';
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