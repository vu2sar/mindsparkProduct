var pendingEssaysArr = [];
var reviewedEssaysArr = [];
var eMode;
var eScoreID;
var rubricVals = [-1, -1, -1, -1, -1, -1, -1];


function showTeacherDashboard() {
    hidePluginIfAny();
    if (sessionData.category == 'School Admin' && sessionData.subcategory == 'School') {
        getAlertForNotPrimaryTeacherAvailable();
    }
    $('#home_message').hide();
    if ($('#th_grade_select').val() == "") {
        $.ajax({
            type: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "teacherhome/getTeacherMappedClass",
            dataType: 'json',
        }).done(function (data) {
            Helpers.ajax_response(getTeacherDashboard, data, []);
            $('select#th_grade_select option:eq(1)').attr('selected', 'selected');
            getTeacherSection($('#th_grade_select').val(), 1);
        });
    } else {
        $('#th_go').click();
    }
}

/**
	 * function description : This function will get the class and section name which does not have primary teachers.
	 * param1   
	 * @return  redirect user to view teacher tab under my student page. 
	 * 
	 * */
function getAlertForNotPrimaryTeacherAvailable(){
    $.ajax({
        type: 'POST',
        url: Helpers.constants['CONTROLLER_PATH'] + "teacherhome/getAlertForNotPrimaryTeacherAvailable",
        dataType: 'json',
    }).done(function (data) {
        if(data.result_data!=='0'){
            var message = "Primary classes have not been set for some teachers.";
                Helpers.prompt({
                    text: message,
                    buttons: {
                        'Go to View teachers': function () {
                            $('#sbi_my_students').click();
                            setTimeout(function () {
                                viewTeachersMyStudentActivatePage();
                            }, 400);
                            
                        }
                    },
                    noClose: true,
                });
        }
    });
}


function getTeacherDashboard(data, extraParams)
{
    var html = "<option value=''>select</option>";
    $.each(data, function (key, value) {
        html += "<option value='" + value + "'>" + value + "</option>";
    });
    $('#th_grade_select').html(html);
    $('#teacherHomeReportStartDate').datepicker({dateFormat: 'dd/mm/yy', changeYear: true, yearRange: "-50:+50"});
    $('#teacherHomeReportEndDate').datepicker({dateFormat: 'dd/mm/yy', maxDate: 0, changeYear: true, yearRange: "-50:"});

    //  Select Default first option
    if ($('select[id=th_grade_select] option:eq(1)').length == 1 && $('select[id=th_grade_select] option').length == 2)
    {
        $('select[id=th_grade_select] option:eq(1)').attr('selected', 'selected');
        $("#teacherHomeReportEndDate").datepicker().datepicker("setDate", new Date());

        var prevMonth = "0" + ($('#teacherHomeReportEndDate').val().substring(3, 5) - 1);
        if (prevMonth == "00")
            prevMonth = 12;
        if ($('#teacherHomeReportEndDate').val().substring(3, 5) != '01')
        {
            var newDate = $('#teacherHomeReportEndDate').val().substring(0, 2) + "/" + prevMonth + "/" + $('#teacherHomeReportEndDate').val().substring(6);
        } else
        {
            var newDate = $('#teacherHomeReportEndDate').val().substring(0, 2) + "/" + prevMonth + "/" + ($('#teacherHomeReportEndDate').val().substring(6) - 1);
        }

        $("#teacherHomeReportStartDate").val(newDate);
        getTeacherSection($('#th_grade_select').val(), 1);
    }
}

function getTeacherSection(selectedClass, isSelectDefault) {
    $.ajax({
        type: 'POST',
        url: Helpers.constants['CONTROLLER_PATH'] + "teacherhome/getTeacherMappedSection",
        dataType: 'json',
        data: {'selectedClass': selectedClass},
    }).done(function (data) {

        Helpers.ajax_response(teacherSectionAjax, data, [isSelectDefault]);
    });
}

function teacherSectionAjax(data, extraParams)
{
    var isSelectDefault = extraParams[0];
    var html = "<option value=''>select</option>";
    $.each(data, function (key, value) {
        html += "<option value='" + value + "'>" + value + "</option>";
    });
    $('#th_section_select').html(html);
    var endDate = new Date();
    endDate.setDate(endDate.getDate() - 1);
    var endDate = $.datepicker.formatDate('dd/mm/yy', endDate);
    var startDate = new Date();
    startDate.setDate(startDate.getDate() - 7);
    var startDate = $.datepicker.formatDate('dd/mm/yy', startDate);
    $("#teacherHomeReportStartDate").val(startDate);
    $("#teacherHomeReportEndDate").val(endDate);
    if (isSelectDefault == 1) {
        if ($('select[id=th_section_select] option:eq(1)').length == 1)
        {
            $('select[id=th_section_select] option:eq(1)').attr('selected', 'selected');
            $('#th_go').click();
        } else {
            $('select[id=th_grade_select] option:eq(0)').attr('selected', 'selected');
            $('select[id=th_section_select] option:eq(0)').attr('selected', 'selected');
            $("#teacherHomeReportEndDate").datepicker().datepicker("setDate", '');
            $("#teacherHomeReportStartDate").datepicker().datepicker("setDate", '');
        }
    }
}

$('#th_grade_select').on("change", function () {
    if ($(this).val() == "") {
        $('#th_section_select').html("<option value=''>select</option>");
    } else {
        getTeacherSection($(this).val(), 0);
    }
});

$(document).delegate('#viewAllPendingEssays', 'click', function () {
    $(".list-group-item.essayPending.none").toggle()
});

$(document).delegate('#viewAllReviewedEssays', 'click', function () {
    $(".list-group-item.essayReviewed.none").toggle()
});

/*$(document).delegate('.evaluation-comment', 'mouseover', function() {
 //console.log($(this).attr('label'));
 $('.hiliteOn').removeClass('hiliteOn').addClass('commentHilight');
 var l = $(this).parent().parent().attr('label').split('~');
 var a = l[0],b = l[1];
 $('.commentHilight[name="' + a + '~' + b + '"] *').addClass('hiliteOn').removeClass('commentHilight');
 $('.commentHilight[name="' + a + '~' + b + '"]').addClass('hiliteOn').removeClass('commentHilight');
 //selStartOffset = a;
 //selEndOffset = b;
 //setCursor();
 
 //$('li[name=hilitecolor]').trigger('click');
 //$('.color[title=#ffff00]').trigger('click');
 });*/

$(document).delegate('.comment', 'mouseout', function () {
    $('.hiliteOn').removeClass('hiliteOn').addClass('commentHilight');
    //var rSel = rangy.getSelection();
    //rSel.removeAllRanges();
});

/*
 $('#addCommentBtn').fastClick(function() {
 angular.element(document.getElementById('essay_evaluation')).scope().getSelection();
 });*/



$(document).delegate('.comDbtn', 'click', function () {
    var l = $(this).parent().attr('label').split('~');
    console.log(l[0] + "***" + l[1]);
    $(this).parent().parent().remove();
    var a = l[0],
            b = l[1];
    $('.hilite[name="' + a + '~' + b + '"]').removeClass('hiliteOn');
    $('.hilite[name="' + a + '~' + b + '"]').removeClass('hilite');
});

$("#th_go").on("click", function () {
    $(".v2-usage-container").hide();
    var message = "";
    if ($('#th_grade_select').val() == "")
        message += "Please select a grade.<br>";
    if ($('#th_section_select').val() == "")
        message += "Please select a section.<br>";
    if ($('#teacherHomeReportStartDate').val() == "")
        //message+="Please select a Start Date.<br>";
        if ($('#teacherHomeReportEndDate').val() == "")
            //message+="Please select a End Date.<br>";

            //  check end date > start date
            var startDate = new Date($("#teacherHomeReportStartDate").datepicker('getDate'));
    var endDate = new Date($("#teacherHomeReportEndDate").datepicker('getDate'));
    if (endDate < startDate)
        message += "End Date must be greater than Start Date.";

    if (message == "") {
        //  get report data
        $.ajax({
            type: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "teacherhome/getTeacherDashBoardReport",
            dataType: 'json',
            data: {'class': $('#th_grade_select').val(), 'section': $('#th_section_select').val(), 'start_date': $('#teacherHomeReportStartDate').val(), 'end_date': $('#teacherHomeReportEndDate').val()},
        }).done(function (data) {

            Helpers.ajax_response(getReportDataAjax, data, []);
        });
    } else {
        Helpers.prompt(message);
    }
});
//new donut chart
function v2UpdateDonutChartSkill(el, percent, donut, text) {
    // round percent value
    percent = Math.round(percent);
    if (percent > 100) {
        percent = 100;
    } else if (percent < 0) {
        percent = 0;
    }
    //degree calculation  based on percent
    var deg = Math.round(360 * (percent / 100));
    if (percent > 50) {
        $(el + ' .pie').css('clip', 'rect(auto, auto, auto, auto)');
        $(el + ' .right-side').css('transform', 'rotate(180deg)');
    } else {
        $(el + ' .pie').css('clip', 'rect(0, 1em, 1em, 0.5em)');
        $(el + ' .right-side').css('transform', 'rotate(0deg)');
    }
    if (donut) {
        $(el + ' .right-side').css('border-width', '0.1em');
        $(el + ' .left-side').css('border-width', '10px');
    } else {
        $(el + ' .right-side').css('border-width', '0.5em');
        $(el + ' .left-side').css('border-width', '0.5em');
    }
    // rendering text/label
    $(el + ' .num').html(percent + "% <div>" + text + "</div>");
    $(el + ' .left-side').css('transform', 'rotate(' + deg + 'deg)');

    //reset color
    $(el).removeClass('v2-green');
    $(el).removeClass('v2-blue');
    $(el).removeClass('v2-orange');
    //end
    //add color class
    if (percent >= 70) {
        $(el).addClass('v2-green');
    }
    if (percent < 70 && percent > 35) {
        $(el).addClass('v2-blue');
    }
    if (percent <= 35) {
        $(el).addClass('v2-orange');
    }
    //end
}

equalheight = function (container) {

    var currentTallest = 0,
            currentRowStart = 0,
            rowDivs = new Array(),
            $el,
            topPosition = 0;
    $(container).each(function () {

        $el = $(this);
        $($el).height('auto')
        topPostion = $el.position().top;

        if (currentRowStart != topPostion) {
            for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
                rowDivs[currentDiv].height(currentTallest);
            }
            rowDivs.length = 0; // empty the array
            currentRowStart = topPostion;
            currentTallest = $el.height();
            rowDivs.push($el);
        } else {
            rowDivs.push($el);
            currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
        }
        for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
            rowDivs[currentDiv].height(currentTallest);
        }
    });
}

function getReportDataAjax(data, extraParams)
{
    $('#teacher_home div > .none').show();
    $('.teacher_accuracy').show();
    $('.teacher_skill').show();
    $('#home_message').show();
    $('#overall_accuracy_note').html('');
    // Click events for graph.
    /*$('#overall_accuracy').bind('jqplotDataClick',
     function (ev, seriesIndex, pointIndex, data) {                
     
     }
     );*/
    //  NO data found
    if (data.hasOwnProperty("noActiveUsersFound")) {
        $('#home_message').html("<br>Looks like there's been no activity!");
        $('#teacher_home div > .none').hide();
        $('.teacher_accuracy').hide();
        $('.teacher_skill').hide();
        $('#home_message').show();
    } else {
        if (!data.hasOwnProperty("usageSummaryGraphDetails")) {

            $('#home_message').html("<br>No Data Found.");
            $('#teacher_home div > .none').hide();
            $('.teacher_accuracy').hide();
            $('.teacher_skill').hide();
            $('#home_message').show();
        } else {
            $('#home_message').hide();
            if (!data.usageSummaryGraphDetails.hasOwnProperty('zero')) {
                data.usageSummaryGraphDetails.zero = 0;
            }
            //data from text
            $('span.v2-usage-overview').html($("#teacherHomeReportStartDate").val() + " to " + $("#teacherHomeReportEndDate").val());
            //end        

            //merging low and zero

            //data.lowUsageNamesAndTimespent = [data.lowUsageNamesAndTimespent, data.zeroUsageNamesAndTimespent];
            var lowUsageNamesAndTimespent = data.zeroUsageNamesAndTimespent.concat(data.lowUsageNamesAndTimespent);
            //end

            ///adding to local variable for ease in calucalation
            var low_data = data.usageSummaryGraphDetails.low + data.usageSummaryGraphDetails.zero;
            var average_data = data.usageSummaryGraphDetails.average;
            var high_data = data.usageSummaryGraphDetails.great;

            //percentage calculation
            var total_value = low_data + average_data + high_data;
            var high_per = high_data / total_value * 100;
            var average_per = average_data / total_value * 100;
            var low_per = low_data / total_value * 100;
            //end   
            //place low,average & high student name
            //Clearing div
            $('#v2-low-data-3').html('');
            $('#v2-low-data-n').html('');

            if (!lowUsageNamesAndTimespent.length) {
                $('#v2-low-data-3').append('<div class="v2-user-list"> No students found</div>');
                $('#v2-low-data-n').append('<div class="v2-user-list"> No students found</div>');
            } else {
                jQuery.each(lowUsageNamesAndTimespent, function (key, value) {
                    if (value.name == null) {
                        value.name = "null";
                    }
                    var firstName = value.name.split(' ').slice(0, -1).join(' '); //slicing first name
                    var lastName = value.name.split(' ').slice(-1).join(' '); //slicing last name
                    if (key < 3) {
                        // limit to 3 number only
                        $('#v2-low-data-3').append('<div class="v2-user-list"> <div class="v2-avtar">' + firstName.charAt(0) + lastName.charAt(0) + '</div><div class="v2-full-name">' + value.name + '</div></div>');
                    }
                    // data will show in modal box
                    $('#v2-low-data-n').append('<div class="v2-user-list"> <div class="v2-avtar">' + firstName.charAt(0) + lastName.charAt(0) + '</div><div class="v2-full-name">' + value.name + '<span class="v2-usage-time">' + Math.round(value.timeSpent / 60) + ' min.</span></div></div>');
                });
            }
            //clearing existing div
            $('#v2-average-data-3').html('');
            $('#v2-average-data-n').html('');
            if (!data.averageUsageNamesAndTimespent.length) {
                $('#v2-average-data-3').append('<div class="v2-user-list"> No students found</div>');
                $('#v2-average-data-n').append('<div class="v2-user-list"> No students found</div>');
            } else {
                jQuery.each(data.averageUsageNamesAndTimespent, function (key, value) {
                    if (value.name == null) {
                        value.name = "null";
                    }
                    var firstName = value.name.split(' ').slice(0, -1).join(' ');//slicing first name
                    var lastName = value.name.split(' ').slice(-1).join(' ');//slicing last name
                    if (key < 3) {
                        // limit to 3 number only
                        $('#v2-average-data-3').append('<div class="v2-user-list"> <div class="v2-avtar">' + firstName.charAt(0) + lastName.charAt(0) + '</div><div class="v2-full-name">' + value.name + '</div></div>');
                    }
                    // data will show in modal box
                    $('#v2-average-data-n').append('<div class="v2-user-list"> <div class="v2-avtar">' + firstName.charAt(0) + lastName.charAt(0) + '</div><div class="v2-full-name">' + value.name + '<span class="v2-usage-time">' + Math.round(value.timeSpent / 60) + ' min.</span></div></div>');
                });
            }

            $('#v2-high-data-3').html('');
            $('#v2-high-data-n').html('');
            if (!data.greatUsageNamesAndTimespent.length) {
                $('#v2-high-data-3').append('<div class="v2-user-list"> No students found</div>');
                $('#v2-high-data-n').append('<div class="v2-user-list"> No students found</div>');
            } else {
                jQuery.each(data.greatUsageNamesAndTimespent, function (key, value) {
                    if (value.name == null) {
                        value.name = "null";
                    }
                    var firstName = value.name.split(' ').slice(0, -1).join(' ');//slicing first name
                    var lastName = value.name.split(' ').slice(-1).join(' ');//slicing last name
                    if (key < 3) {
                        // limit to 3 number only
                        $('#v2-high-data-3').append('<div class="v2-user-list"> <div class="v2-avtar">' + firstName.charAt(0) + lastName.charAt(0) + '</div><div class="v2-full-name">' + value.name + '</div></div>');
                    }
                    // data will show in modal box
                    $('#v2-high-data-n').append('<div class="v2-user-list"> <div class="v2-avtar">' + firstName.charAt(0) + lastName.charAt(0) + '</div><div class="v2-full-name">' + value.name + '<span class="v2-usage-time">' + Math.round(value.timeSpent / 60) + ' min.</span></div></div>');
                });
            }
            //end
            //add more text
            $('#v2-low-data-more').html(''); //clearning div
            if (low_data - 3 > 0) {
                $('#v2-low-data-more').html("+" + (low_data - 3) + " more");
            }

            $('#v2-average-data-more').html(''); //clearnig div
            if (average_data - 3 > 0) {
                $('#v2-average-data-more').html("+" + (average_data - 3) + " more");
            }

            $('#v2-high-data-more').html(''); //clearing div
            if (high_data - 3 > 0) {
                $('#v2-high-data-more').html("+" + (high_data - 3) + " more");
            }
            //end

            //pie rendering for overview page
            // var usagePieData = [];
            var usagePieData = {
                "sortOrder": "value-desc",
                "content": []
            };
            if (high_per != 0) {
                high_per_newData = {
                    "label": "Great",
                    "value": high_data,
                    "color": "#C2F8C2",
                    "border": "#2ecc71",
                };
                usagePieData.content.push(high_per_newData);
            }
            if (average_per != 0) {
                average_per_newData = {
                    "label": "Average",
                    "value": average_data,
                    "color": "#FAECB7",
                    "border": "#f2c826"
                };
                usagePieData.content.push(average_per_newData);
            }
            if (low_per != 0) {
                low_per_newData = {
                    "label": "Low",
                    "value": low_data,
                    "color": "#FFD5C8",
                    "border": "#ff4651"
                };
                usagePieData.content.push(low_per_newData);
            }
            if ($("#v2-chart_ledgends").length != 0) {
                var endAng = 0;
                var legendId = 'v2-chart_ledgends';
                $("#" + legendId).html('');
                $("#" + legendId).append(
                        '<span style="color:#2cc55e">Great <span style="color:#000;">: > 45 mins</span></span>\n\
            <span style="color:#eebe1e">Avg <span style="color:#000;">: 15 - 45 min</span></span>\n\
            <span style="color:#fc2c40">Low <span style="color:#000;">: < 15 min</span></span>');
            }
            $("#v2_usage_report").html('');
            var pie = new d3pie("v2_usage_report", {
                "size": {
                    "canvasHeight": 250,
                    "canvasWidth": 270,
                    "pieInnerRadius": "20%",
                    "pieOuterRadius": "100%"
                },
                "data": usagePieData,
                "labels": {
                    "outer": {
                        "format": "label-value2",
                        "pieDistance": 15
                    },

                    "mainLabel": {
                        "fontSize": 12
                    },
                    "inner": {
                        "format": "none"
                    },
                    "percentage": {
                        "color": "#ffffff",
                        "decimalPlaces": 0
                    },
                    "value": {
                        "color": "#adadad",
                        "fontSize": 11
                    },
                    "lines": {
                        "enabled": true,
                        "style": "straight"
                    },
                    "truncation": {
                        "enabled": true
                    }
                },
                "effects": {
                    "load": {
                        "speed": 500
                    },
                    "pullOutSegmentOnClick": {
                        "effect": "none",
                        "speed": 1000,
                        "size": 8
                    },
                    "highlightSegmentOnMouseover": false,
                    "highlightLuminosity": 0.99
                },

            });
            //detail modal box
            $("#v2-usage-overview-detail").on('shown.bs.modal', function () {
                //report for low data
                var usageDetailPieData_1 = [];
                if (high_per != 0) {
                    high_per_newData = {
                        label: {
                            text: 'Good', // Text to be displayed in the lengend
                        },
                        value: high_per, // Value will be used to divide the charts (value in % to be given)
                        color: '#FFF', // Slice color used
                        borderColor: '#FFD5C8', // Border for the slice 
                        strokeColor: '#FFF',
                        textPrefix: '', // Prefix text to be shown with label
                        textPostfix: '%' // Postfix text to be shown with label
                    };
                    usageDetailPieData_1.push(high_per_newData);
                }
                if (average_per != 0) {
                    average_per_newData = {
                        label: {
                            text: 'Average', // Text to be displayed in the lengend
                        },
                        value: average_per, //value in %
                        color: '#FFF', // Slice color used
                        borderColor: '#FFD5C8', // Border for the slice 
                        textColor: '#FFF', // text color used
                        strokeColor: '#FFF', // stroke color used
                        textPrefix: '', // Prefix text to be shown with label
                        textPostfix: '%' // Postfix text to be shown with label
                    };
                    usageDetailPieData_1.push(average_per_newData);
                }
                if (low_per != 0) {
                    low_per_newData = {
                        label: {
                            text: 'Low', // Text to be displayed in the lengend
                            position: 'out', // Options in or out
                            pointer: 'none' // Options avaiable none, indicator  
                        },
                        value: low_per, //value in percentage
                        color: '#F9C0AB', // Slice color used
                        borderColor: '#ff4651', // Border for the slice 
                        textColor: '#ff4651', // text color used
                        strokeColor: '#ff4651', // stroke color used
                        textPrefix: '', // Prefix text to be shown with label
                        textPostfix: '%'// Postfix text to be shown with label
                    };
                    usageDetailPieData_1.push(low_per_newData);
                }

                $("#v2_usage_detail_report_low").eiPie({
                    appearance: 'donut_border',
                    background: "#FFF",
                    displaySet: 'all',
                    radius: 45,
                    label: {
                        text: low_data, //this data will show in center of canvas
                        position: 'in', // Options in or out
                        pointer: 'none', // Options avaiable none, indicator  
                        textColor: '#ff4651',
                        textPrefix: '', // Prefix text to be shown with label
                        textPostfix: '' // Postfix text to be shown with label
                    },
                    data: usageDetailPieData_1,
                    borderWidth: 10,
                    animate: true,
                    duration: 1000
                });
//report for average data
                var usageDetailPieData_2 = [];
                if (high_per != 0) {
                    high_per_newData = {
                        label: {
                            text: 'Good', // Text to be displayed in the legend
                        },
                        value: high_per, // Value will be used to divide the charts (value in % to be given)
                        color: '#FFF', // Slice color used
                        borderColor: '#FAECB7', // Border for the slice 
                        strokeColor: '#FFF',
                        textPrefix: '', // Prefix text to be shown with label
                        textPostfix: '%' // Postfix text to be shown with label
                    };
                    usageDetailPieData_2.push(high_per_newData);
                }
                if (average_per != 0) {
                    average_per_newData = {
                        label: {
                            text: 'Average', //text to be desplaed in the legend
                        },
                        value: average_per, //value will be used as %
                        color: '#FAECB7',
                        borderColor: '#f2c826',
                        textColor: '#f2c826',
                        strokeColor: '#f2c826',
                        textPrefix: '',
                        textPostfix: '%'// Postfix text to be shown with label
                    };
                    usageDetailPieData_2.push(average_per_newData);
                }
                if (low_per != 0) {
                    low_per_newData = {
                        label: {
                            text: 'Low', //text to be desplaed in the legend
                            position: 'out', // Options in or out
                            pointer: 'none' // Options avaiable none, indicator  
                        },
                        value: low_per, // Value will be used to divide the charts (value in % to be given)
                        color: '#FFF', // Slice color used
                        borderColor: '#FAECB7', // Border for the slice 
                        textColor: '#FFF',
                        strokeColor: '#FFF',
                        textPrefix: '', // Prefix text to be shown with label
                        textPostfix: '%'// Postfix text to be shown with label
                    };
                    usageDetailPieData_2.push(low_per_newData);
                }
                $("#v2_usage_detail_report_average").eiPie({
                    appearance: 'donut_border',
                    background: "#FFF",
                    displaySet: 'all',
                    radius: 45,
                    label: {
                        text: average_data, //this data will show in center of div
                        textColor: '#f2c826',
                        textPrefix: '', // Prefix text to be shown with label
                        textPostfix: '' // Postfix text to be shown with label
                    },
                    data: usageDetailPieData_2,
                    borderWidth: 10,
                    animate: true,
                    duration: 1000
                });
//report for good data
                var usageDetailPieData_3 = [];
                if (high_per != 0) {
                    high_per_newData = {
                        label: {
                            text: 'Good', // Text to be displayed in the label
                        },
                        value: high_per, // Value will be used to divide the charts (value in % to be given)
                        color: '#CDE8B3', // Slice color used
                        borderColor: '#2ecc71', // Border for the slice 
                        strokeColor: '#2ecc71',
                        textPrefix: '', // Prefix text to be shown with label
                        textPostfix: '%' // Postfix text to be shown with label
                    };
                    usageDetailPieData_3.push(high_per_newData);
                }
                if (average_per != 0) {
                    average_per_newData = {
                        label: {
                            text: 'Average',
                            position: 'out', // Options in or out
                            pointer: 'none' // Options avaiable none, indicator  
                        },
                        value: average_per, // Value will be used to divide the charts (value in % to be given)
                        color: '#FFF', // Slice color used
                        borderColor: '#C2F8C2', // Border for the slice 
                        textColor: '#FFF',
                        strokeColor: '#FFF',
                        textPrefix: '', // Prefix text to be shown with label
                        textPostfix: '%'// Postfix text to be shown with label
                    };
                    usageDetailPieData_3.push(average_per_newData);
                }
                if (low_per != 0) {
                    low_per_newData = {
                        label: {
                            text: 'Low',
                            position: 'out', // Options in or out
                            pointer: 'none' // Options avaiable none, indicator  
                        },
                        value: low_per, // Value will be used to divide the charts (value in % to be given)
                        color: '#FFF', // Slice color used
                        borderColor: '#C2F8C2', // Border for the slice 
                        textColor: '#FFF',
                        strokeColor: '#FFF',
                        textPrefix: '', // Prefix text to be shown with label
                        textPostfix: '%'// Postfix text to be shown with label
                    };
                    usageDetailPieData_3.push(low_per_newData);
                }
                $("#v2_usage_detail_report_good").eiPie({
                    appearance: 'donut_border',
                    background: "#FFF",
                    displaySet: 'all',
                    radius: 45,
                    label: {
                        text: high_data, // Text to be displayed in the center of canvas
                        textColor: '#2ecc71', // Text color for the label
                        textPrefix: '', // Prefix text to be shown with label
                        textPostfix: '' // Postfix text to be shown with label
                    },
                    data: usageDetailPieData_3,
                    borderWidth: 10,
                    animate: true,
                    duration: 1000
                });
            });

            //skill data

            $('#v2-rc-item').html('<li><span class="fa fa-book"></span> ' + data.readingPsgDetails.totalPassageRead + ' Passages Read</li><li><span class="fa fa-question-circle"></span> ' + data.readingPsgDetails.totalPassageQuesAttempted + ' Passage Questions Attempted</li>');
            $('#v2-lc-item').html('<li><span class="fa fa-headphones"></span> ' + data.listeningPsgDetails.totalPassageRead + ' Audio Clips Heard</li><li><span class="fa fa-question-circle"></span> ' + data.listeningPsgDetails.totalPassageQuesAttempted + ' Passage Questions Attempted</li>');
            $('#v2-gc-item').html('<li><span class="fa fa-question-circle"></span> ' + data.grammarQuesDetails.totalPassageQuesAttempted + ' Questions Attempted</li>');
            $('#v2-vc-item').html('<li><span class="fa fa-question-circle"></span> ' + data.VocabQuesDetails.totalPassageQuesAttempted + ' Questions Attempted</li>');
            //skill donut chart

            v2UpdateDonutChartSkill('#v2-rc-p1', data.readingPsgDetails.accuracy, true, 'Accuracy');
            v2UpdateDonutChartSkill('#v2-lc-p2', data.listeningPsgDetails.accuracy, true, 'Accuracy');
            v2UpdateDonutChartSkill('#v2-gc-p3', data.grammarQuesDetails.accuracy, true, 'Accuracy');
            v2UpdateDonutChartSkill('#v2-vc-p4', data.VocabQuesDetails.accuracy, true, 'Accuracy');
            //end

            //equalizing div height
            function equalHeightBlocks() {
                equalheight('.v2-skill-overview-blocks h2');
                equalheight('.v2-skill-overview-blocks');
                equalheight('.v2-skill-item');
                equalheight('.autoHeight');
            }
            setTimeout(function(){ equalHeightBlocks(); }, 100);
            equalHeightBlocks();
            $(window).resize(function () {
                equalHeightBlocks();
            });

            //end
        }
    }
}


$('#skill_report_toggle').on("change", function () {
    if ($('#skill_report_toggle').prop('checked')) {
        $('#skill_usage').hide();
        $('#skill_accuracy').show();
    } else {
        $('#skill_accuracy').hide();
        $('#skill_usage').show();
    }
});

$('#th_setting_grade_select').on("change", function () {
    $('#generalSetting').hide();
    if ($(this).val() == "") {
        $('#th_setting_section_select').html("<option value=''>select</option>");
    } else {
        getSetTeacherSection($(this).val(), 0);
    }
});

function showTeacherDefaultSttingControls()
{
    if ($('#th_setting_grade_select').val() == "") {
        $('#generalSetting').hide();

        $.ajax({
            type: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "teacherhome/getTeacherMappedClass",
            dataType: 'json',
        }).done(function (data) {

            Helpers.ajax_response(teacherDefaultSettingAjax, data, []);
        });
    } else {
        $('#th_setting_go').click();
    }
}

function teacherDefaultSettingAjax(data, extraParams)
{
    var html = "<option value=''>select</option>";
    $.each(data, function (key, value) {
        html += "<option value='" + value + "'>" + value + "</option>";
    });
    $('#th_setting_grade_select').html(html);

    //  Select Default first option
    if ($('select[id=th_setting_grade_select] option:eq(1)').length == 1)
    {
        $('select[id=th_setting_grade_select] option:eq(1)').attr('selected', 'selected');
        getSetTeacherSection($('#th_setting_grade_select').val(), 1);
    }
}

function getSetTeacherSection(selectedClass, isSelectDefault)
{

    $.ajax({
        type: 'POST',
        url: Helpers.constants['CONTROLLER_PATH'] + "teacherhome/getTeacherMappedSection",
        dataType: 'json',
        data: {'selectedClass': selectedClass},
    }).done(function (data) {
        Helpers.ajax_response(getSetTeacherSectionAjax, data, [isSelectDefault]);
    });
}


function getSetTeacherSectionAjax(data, extraParams)
{
    var isSelectDefault = extraParams[0];
    var html = "<option value=''>select</option>";
    $.each(data, function (key, value) {
        html += "<option value='" + value + "'>" + value + "</option>";
    });
    $('#th_setting_section_select').html(html);
    if (isSelectDefault == 1) {
        if ($('select[id=th_setting_section_select] option:eq(1)').length == 1)
        {
            $('#generalSetting').show();
            $('select[id=th_setting_section_select] option:eq(1)').attr('selected', 'selected');
            $('#th_setting_go').click();
        } else {
            $('#generalSetting').hide();
            $('select[id=th_grade_select] option:eq(0)').attr('selected', 'selected');
            $('select[id=th_setting_section_select] option:eq(0)').attr('selected', 'selected');
        }
    }
}


$('#ti_setting_save').on('click', function () {
    var message = "";
    if ($('#ti_setting_ground_enable_after').val() == "")
        message += "Please set ground minutes.<br>";

    if ($('#ti_setting_ground_enable_after').val() != '' && !($.isNumeric($('#ti_setting_ground_enable_after').val()))) {
        message += "Ground minutes must be numeric.<br>";
    } else {
        if ($('#ti_setting_ground_enable_after').val() > $('#ti_setting_session_length').val())
            message += "The length of the session must exceed the time after which the Grounds section will be activated.<br>";
    }
    if (message == "") {
        $.ajax({
            type: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "teacherhome/setTeacherGenSettings",
            dataType: 'json',
            data: {'class': $('#th_setting_grade_select').val(), 'section': $('#th_setting_section_select').val(), 'session_length': $('#ti_setting_session_length').val(), 'ground_enable_after': $('#ti_setting_ground_enable_after').val()},
        }).done(function (data) {

            Helpers.ajax_response('', data, []);
            //Helpers.prompt('Settings saved successfully.');
        });
    } else {
        Helpers.prompt(message);
    }

});
$("#th_setting_go").on("click", function () {
    var message = "";
    if ($('#th_setting_grade_select').val() == "")
        message += "Please select a grade.<br>";
    if ($('#th_setting_section_select').val() == "")
        message += "Please select a Section.<br>";


    if (message == "") {
        //  get report data
        $.ajax({
            type: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "teacherhome/getTeacherSettingPageData",
            dataType: 'json',
            data: {'class': $('#th_setting_grade_select').val(), 'section': $('#th_setting_section_select').val()},
        }).done(function (data) {
            Helpers.ajax_response(generalSettingAjax, data, []);
        });
    } else {
        $('#generalSetting').hide();
        Helpers.prompt(message);
    }
});

function generalSettingAjax(data, extraParams)
{
    $('#generalSetting').show();
    $('#ti_setting_session_length').val(data.session_length);
    $('#ti_setting_ground_enable_after').val(data.ground_enable_after);
}

/*************************DMS START*********************************/
function showGradesDMS()
{

    //hidePluginIfAny();
    $('#dms_grade_select').val("");


    if ($('#dms_grade_select').val() == "")
    {
        $.ajax({
            type: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "teacherdms/getTeacherMappedClassDMS",
            dataType: 'json',
        }).done(function (data) {
            Helpers.ajax_response(dmsClassesAjax, data, []);
        });
    }
}

function dmsClassesAjax(data, extraParams)
{
    var html = "<option value=''>select</option>";
    var childClass = sessionData.childClass;
    var dataLength = data;




    $.each(data, function (key, value) {
        if (value == childClass)
            var selected = 'selected';
        else
            var selected = '';
        html += "<option " + selected + " value='" + value + "'>" + value + "</option>";
    });
    $('#dms_grade_select').html(html);
    //startClassRoom();

    /* var selectedGrade = $("#dms_grade_select").val();
     if(selectedGrade == childClass)
     startClassRoom();*/

    if (Object.keys(dataLength).length == 1)
    {
        $("#dms_go").trigger("click");
        $("#dms_go").attr('disabled', 'disabled');
        $("#dms_grade_select").attr('disabled', 'disabled');
    } else
    {
        $("#dms_go").removeAttr('disabled', 'disabled');
        $("#dms_grade_select").removeAttr('disabled', 'disabled');
    }

    $("#dataLengthCount").val(Object.keys(dataLength).length);

}

$("#dms_go").on("click", function () {

    var message = "";
    if ($('#dms_grade_select').val() == "")
        message += "Please select a grade.<br>";

    if (message == "")
    {
        stopAndHideOtherActivities(true);
        $(".diaLog-explanation").hide();
        $("#prompt").hide();
        if ($('.rating-feedback').is(':visible'))
        {
            $('.rating-feedback').hide();
        }

        if ($('#questionSubmitButton').css('display') == 'block' || $('#questionSubmitButton').css('display') == 'inline-block')
        {
            $('#questionSubmitButton').hide();
        }

        var selectedGrade = $("#dms_grade_select").val();
        $.ajax({
            type: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "teacherdms/getCurrentStatus",
            dataType: 'json',
            data: {'class': selectedGrade},
        }).done(function (data) {
            $.ajax({
                //url : Helpers.constants['CONTROLLER_PATH'] + 'questionspage/getStudentPosition/' + sessionData.userID,
                url: Helpers.constants['CONTROLLER_PATH'] + 'questionspage/getStudentPosition',
                success: function (data) {
                    Helpers.ajax_response(afterGettingQuestion, data, []);
                },
                timeout: serverResponseTimeLimit,
                error: onAjaxError
            });
            //Helpers.ajax_response( dmsCurrectStatus, data, []);
        });
        //startClassRoom();
    } else
        Helpers.prompt(message);

});
/**************************DMS END********************************/

/********************************* My Student***********************************************/
function showTeacherMyStudentViewPage()
{

    $("#view_students").show();
    $("#activate_topic").hide();
    $("#student_table_activate").hide();
    $("#teacher_table_view").hide();
    $("#teacher_data_note").hide();
    $("#teacherBtns").hide();
    // Flick of active class issue on My Students page fixed.
    $(".active").removeClass('active');
    $("#view_students_li").attr('class', 'active');
    // ---- //
    $("#student_table_view").hide();
    $("#student_data_note").hide();
    $("#student_dataparentemail_note").hide();
    //hidePluginIfAny();
    $('#th_my_student_grade_select').val("");
    $('#th_my_student_section_select').val("");
    var html = "<option value=''>select</option>";
    $('#th_my_student_section_select').html(html);
    $("#ChildNameInput").val("");
    if ($('#th_my_student_grade_select').val() == "")
    {
        $.ajax({
            type: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "teachermystudents/getTeacherMappedClass",
            dataType: 'json',
        }).done(function (data) {
            Helpers.ajax_response(myStudentsMappedClassesAjax, data, []);
        });
    }
}

function myStudentsMappedClassesAjax(data, extraParams)
{
    var html = "<option value=''>select</option>";
    $.each(data, function (key, value) {
        html += "<option value='" + value + "'>" + value + "</option>";
    });
    $('#th_my_student_grade_select').html(html);
}

$('#th_my_student_grade_select').on("change", function () {
    if ($(this).val() == "") {
        $('#th_my_student_section_select').html("<option value=''>select</option>");
    } else {
        $.ajax({
            type: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "teachermystudents/getTeacherMappedSection",
            dataType: 'json',
            data: {'selectedClass': $(this).val()},
        }).done(function (data) {
            Helpers.ajax_response(getMyStudentsGradeAjax, data, []);
        });
    }
});

function getMyStudentsGradeAjax(data, extraParams)
{
    //var html="<option value=''>select</option>";
    var html = "";
    html += "<option value='all'>ALL</option>";
    $.each(data, function (key, value) {
        html += "<option value='" + value + "'>" + value + "</option>";
    });
    $('#th_my_student_section_select').html(html);
    $('select[id=th_my_student_section_select] option:eq(0)').attr('selected', 'selected');
}

$("#th_my_student_go").on("click", function () {

    $("#teacher_table_view").css('display', 'none');
    $("#teachers_view").css('display', 'none');
    $("#teacher_data_note").css('display', 'none');
    $("#teacherBtns").css('display', 'none');
    $("#my_student_view_tbody").empty();
    var message = "";
    /*if($('#th_my_student_grade_select').val()=="")
     message+="Please select a grade<br>";
     if($('#th_my_student_section_select').val()=="")
     message+="Please select a section";*/
    if (message == "") {
        var userIDArr = [];
        //  get report data
        $.ajax({
            type: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "teachermystudents/showEditStudentDetails",
            dataType: 'json',
            data: {'class': $('#th_my_student_grade_select').val(), 'section': $('#th_my_student_section_select').val(), 'childName': $("#ChildNameInput").val()},
        }).done(function (data) {

            Helpers.ajax_response(getMyStudentsDataAjax, data, [userIDArr]);
        });
    } else {
        Helpers.prompt(message);
    }
});


function getMyStudentsDataAjax(data, extraParams)
{


    var userIDArr = extraParams[0];
    $('#teacher_my_students div > .none').show();
    $('.students_table').show();

    if (data.studentData.length == 0)
    {
        $("#my_student_no_data").show();
        $("#my_students_view").hide();
        $("#student_data_note").hide();
        $("#student_dataparentemail_note").hide();
    } else
    {
        $("#my_student_no_data").hide();
        $("#student_data_note").show();
        $("#student_dataparentemail_note").show()
        for (var i = 0; i < data.studentData.length; i++) {
            userIDArr.push(data.studentData[i].userID);

            var sr_no = i + 1;
            var dateSplit2 = data.studentData[i].childDob.split("-");
            var formattedDob = dateSplit2.reverse().join('-');
            if (formattedDob == '00-00-0000')
            {
                formattedDob = '';
            }

            tr = $('<tr id="tr_' + data.studentData[i].userID + '"/>');
            tr.append("<td style='text-align:center'>" + sr_no + "</td>");
            tr.append("<td id='field" + data.studentData[i].userID + 1 + "'>" + data.studentData[i].userName + "</td>");
            tr.append("<td id='field" + data.studentData[i].userID + 2 + "'>" + data.studentData[i].childName + "</td>");
            //tr.append("<td id='field"+data.studentData[i].userID+3 +"'>" + data.studentData[i].childEmail + "</td>");
            tr.append("<td id='field" + data.studentData[i].userID + 4 + "'>" + formattedDob + "</td>");
            tr.append("<td id='field" + data.studentData[i].userID + 5 + "'>" + data.studentData[i].parentEmail.replace(/,/ig, '<br>') + "</td>");
            tr.append("<td id='field" + data.studentData[i].userID + 6 + "'>" + data.studentData[i].childClass + "<input type='hidden' id='childClass_" + data.studentData[i].userID + "' value='" + data.studentData[i].childClass + "'/></td>");
            tr.append("<td id='field" + data.studentData[i].userID + 7 + "'>" + data.studentData[i].childSection + "<input type='hidden' id='childSection_" + data.studentData[i].userID + "' value='" + data.studentData[i].childSection + "'/></td>");
            tr.append("<td id='field" + data.studentData[i].userID + 8 + "'>****</td>");
            tr.append("<td><input class='form-control' type='button'  id='edit_" + data.studentData[i].userID + "' onclick='EditTheFields(" + data.studentData[i].userID + ", " + sr_no + ")' id='' value='Edit'/><input class='form-control' type='button' style='display:none'  id='cancel" + data.studentData[i].userID + "' onclick='CancelTheFields(" + data.studentData[i].userID + "," + sr_no + ")' id='' value='Cancel'/><input type='button' class='form-control' style='display:none'  id='save_" + data.studentData[i].userID + "' onclick='SaveTheData(this.id, " + sr_no + ")' value='Save'/></td>");
            tr.append("<input type='hidden' id='flagCheck" + data.studentData[i].userID + "' name='flagCheck" + data.studentData[i].userID + "' value='0'>");
            $('#my_student_view_tbody').append(tr);
            $("#my_student_view_tbody").append('<tr style="display:none;" id="namechangereason' + data.studentData[i].userID + '"><td  colspan="9"><label>Reason for change in id/name</label>&nbsp;&nbsp;&nbsp;<input  class="form-control" type="text" name="changereason' + data.studentData[i].userID + '" id="changereason' + data.studentData[i].userID + '" OnKeyPress="checkenterkey(event)"/></td></tr>');
        }
    }
    var userIDString = userIDArr.join(',');
    $("#userIDString").val(userIDString);
}

function EditTheFields(userID, sr_no)
{
    /*var count = $('#my_student_view_tbody tr#tr_'+sr_no+' td').length;
     var count = count - 3;*/

    //get child section populated
    var selected_class = $("#th_my_student_grade_select").val();
    if (selected_class == '')
        var selected_class = $("#childClass_" + userID).val();
    var sectionArr = new Array();
    $.ajax({
        type: 'POST',
        url: Helpers.constants['CONTROLLER_PATH'] + "teachermystudents/getTeacherMappedSection",
        dataType: 'json',
        data: {'selectedClass': selected_class},
        async: false,
    }).done(function (data) {
        Helpers.ajax_response(sectionArrayAjax, data, [sectionArr]);
    });

    //userID      = $("#field"+userID+sr_no+count);
    userName = $("#field" + userID + 1).html();
    childName = $("#field" + userID + 2).html();
    //childEmail  = $("#field"+userID+3).html();
    DOB = $("#field" + userID + 4).html();
    parentEmailbr = $("#field" + userID + 5).html();
    parentEmail = parentEmailbr.replace(/<br>/ig, ',');
    childClass = $("#field" + userID + 6).html();
    childSection = $("#childSection_" + userID).val();


    var classIndex = -1;


    document.getElementById("edit_" + userID).style.display = "none";
    document.getElementById("cancel" + userID).style.display = "block";
    document.getElementById("save_" + userID).style.display = "block";


    tempHTML = "<input class='form-control' size='15' type='text' name='userNameTxt" + userID + "' id='userNameTxt" + userID + "'  OnKeyPress='checkenterkey(event)' onblur='checkChangeData(" + userID + ")' maxlength='20'/>";
    tempHTML = tempHTML + "<input size='15' type='hidden' name='userNameHdn" + userID + "' id='userNameHdn" + userID + "' value='" + userName + "' />";

    document.getElementById("field" + userID + "1").innerHTML = tempHTML;
    document.getElementById("userNameTxt" + userID).value = userName;

    tempHTML = "<input class='form-control' size='15' type='text' name='childNameTxt" + userID + "' onblur='checkChangeData(" + userID + ")' id='childNameTxt" + userID + "' OnKeyPress='checkenterkey(event)' maxlength='20'>";

    tempHTML = tempHTML + "<input class='form-control' size='15' type='hidden' name='childNameHdn" + userID + "' id='childNameHdn" + userID + "' value='" + childName + "'>";

    document.getElementById("field" + userID + "2").innerHTML = tempHTML;
    document.getElementById("childNameTxt" + userID).value = childName;

    /*        tempHTML = "<input class='form-control' size='20' type='text' name='childEmailTxt"+userID+"' id='childEmailTxt"+userID+"' maxlength='50' >";
     tempHTML = tempHTML + "<input class='form-control' size='20' type='hidden' name='childEmailHdn"+userID+"' id='childEmailHdn"+userID+"' maxlength='50' value='"+childEmail+"'>";
     document.getElementById("field"+userID+"3").innerHTML=tempHTML;
     document.getElementById("childEmailTxt"+userID).value=childEmail;
     */
    //DOB
    if (DOB != "N.A.")
    {
        tempHTML = "<input class='form-control datepicker' readonly='true' type='text' onkeydown='return DateFormat(this, event.keyCode)' maxlength='10'  name='DOBTxt" + userID + "' id='DOBTxt" + userID + "' size='8' value='" + DOB + "'>";

        tempHTML = tempHTML + "<input class='form-control datepicker' type='hidden'  maxlength='10'  name='DOBHdn" + userID + "' id='DOBHdn" + userID + "' size='8' value='" + DOB + "'>";
    } else
        tempHTML = "<input class='form-control  datepicker' readonly='true' type='text' onkeydown='return DateFormat(this, event.keyCode)' maxlength='10' name='DOBTxt" + userID + "' id='DOBTxt" + userID + "' size='8' value=''>";
    document.getElementById("field" + userID + "4").innerHTML = tempHTML;

    /*tempHTML = "<input class='form-control' size='27' type='text' name='parentEmailTxt"+userID+"' id='parentEmailTxt"+userID+"' >";*/
    tempHTML = "<textarea class='form-control' size='27' type='text' name='parentEmailTxt" + userID + "' id='parentEmailTxt" + userID + "' ></textarea>";
    tempHTML = tempHTML + "<input class='form-control' size='27' type='hidden' name='parentEmailHdn" + userID + "' id='parentEmailHdn" + userID + "' value='" + parentEmail + "' >";
    document.getElementById("field" + userID + "5").innerHTML = tempHTML;
    document.getElementById("parentEmailTxt" + userID).value = parentEmail;

    /* tempHTML = "<select name='childClassTxt"+userID+"' id='childClassTxt"+userID+"' disabled=true> <option value=''>All</option>";
     for(var k=0; k<gradeArray.length; k++)
     {
     tempHTML += "<option value='"+gradeArray[k]+"'";
     if(childClass==gradeArray[k])
     {
     tempHTML +=" selected ";
     classIndex = k;
     }
     tempHTML +=">"+gradeArray[k]+"</option>";
     }
     tempHTML += "</select>";
     document.getElementById("field"+userID+"6").innerHTML=tempHTML;
     
     
     if(classIndex != -1)
     var sectionStr = sectionArray[classIndex];*/

    if (sectionArr.length > 0) //if the class has sections, show the drop down
    {
        tempHTML = "<select class='form-control' name='childSectionTxt" + userID + "' id='childSectionTxt" + userID + "' >";
        for (k = 0; k < sectionArr.length; k++)
        {
            tempHTML += "<option value='" + sectionArr[k] + "'";
            if (childSection == sectionArr[k])
            {
                tempHTML += " selected ";
            }
            tempHTML += ">" + sectionArr[k] + "</option>";
        }
        tempHTML += "</select>";
        tempHTML += "<input type='hidden' id='childSection_" + userID + "' name='childSection_" + userID + "' value='" + childSection + "' />"
        document.getElementById("field" + userID + "7").innerHTML = tempHTML;
    }

    tempHTML = "<select class='form-control' name='password" + userID + "' id='lstPwd" + userID + "' >";
    tempHTML += "<option value='0'>No change</option>";
    tempHTML += "<option value='1'>Reset to username</option>";
    tempHTML += "<option value='2'>Remove password</option>";

    tempHTML += "</select>";
    document.getElementById("field" + userID + "8").innerHTML = tempHTML;

    $(".datepicker").datepicker({
        changeYear: true,
        dateFormat: 'dd-mm-yy'
    });
    document.getElementById("flagCheck" + userID).value = 1;
}

function sectionArrayAjax(data, extraParams)
{
    var sectionArr = extraParams[0];
    $.each(data, function (key, value) {
        sectionArr.push(value);
    });
}

function checkenterkey(event)
{
    if (event.keyCode == 13) {
        event.preventDefault();
        return false;
    }
}

//start from here

function SaveTheData(element_id, sr_no)
{
    var userStr = document.getElementById('userIDString').value;
    var userArr = userStr.split(",");
    var editedUserID = new Array();
    var repeatUserName = "";
    var changereasonUserName = "";
    var flagBlank = 0;
    var countUpdate = 0;
    var currentUpdateUserId = element_id;
    currentUpdateUserId = currentUpdateUserId.split('_');
    currentUpdateUserId = currentUpdateUserId[1];


    var childSection_selected = $("#childSectionTxt" + currentUpdateUserId).val();

    for (var t = 0; t < userArr.length; t++)
    {
        if (document.getElementById("flagCheck" + userArr[t]).value == 1)
        {
            if ($.trim($("#userNameTxt" + userArr[t]).val()) == "" || $.trim($("#childNameTxt" + userArr[t]).val()) == "") {
                var message = "Username and student name can not be empty.";
                Helpers.prompt(message);
                $("#childNameTxt" + userArr[t]).focus();
                flagBlank = 1;
                break;
            }

            /* var ck_username = /^[A-Za-z0-9_.]{3,20}$/; */
            /*if(!validateUserName($("#userNameTxt"+userArr[t]).val())){                      
             $("#userNameTxt"+userArr[t]).focus();            
             return false;         
             }*/
            if (!onlyAlpha($("#childNameTxt" + userArr[t]).val())) {
                $("#childNameTxt" + userArr[t]).focus();
                return false;
                ;
            }

            /* if($.trim($("#childEmailTxt"+userArr[t]).val()) != "") {
             if(!validateEmail($("#childEmailTxt"+userArr[t]).val())) {
             alert("Child's email address is invalid.");
             $("#childEmailTxt"+userArr[t]).focus();
             return false;
             }
             }*/
            /*if($.trim($("#parentEmailTxt"+userArr[t]).val()) != "") {
             if(!validateEmail($("#parentEmailTxt"+userArr[t]).val())) {
             alert("Parent email address is invalid.");
             $("#parentEmailTxt"+userArr[t]).focus();
             break;
             }
             }*/
            if ($("#parentEmailTxt" + userArr[t]).val() != "")     // For the mantis task 12360
            {
                var emailIdStr = $("#parentEmailTxt" + userArr[t]).val();
                var wrongEmailIdsCount = 0;
                var wrongEmailIdsArr = new Array();
                if (emailIdStr.indexOf(",") > 0)
                {
                    var emailIdArr = emailIdStr.split(",");
                    for (var i = 0; i < emailIdArr.length; i++)
                    {
                        var emailId = $.trim(emailIdArr[i]);
                        if (!validateEmail(emailId))
                        {
                            wrongEmailIdsArr.push(emailId);
                            wrongEmailIdsCount++;
                        }
                    }
                    if (wrongEmailIdsCount > 0)
                    {
                        var message = "One of the parent email addresses are invalid.";
                        Helpers.prompt(message);
                        $("#parentEmailTxt" + userArr[t]).focus();
                        break;
                    }
                } else
                {
                    var emailId = $.trim($("#parentEmailTxt" + userArr[t]).val());
                    if (!validateEmail(emailId))
                    {
                        var message = "Parent email address is invalid.";
                        Helpers.prompt(message);
                        $("#parentEmailTxt" + userArr[t]).focus();
                        break;
                    }
                }
            }

            countUpdate++;
        }
    }

    if (countUpdate > 0 && flagBlank == 0) {
        for (var t = 0; t < userArr.length; t++)
        {
            if (document.getElementById("flagCheck" + userArr[t]).value == 1)
            {
                editedUserID.push(userArr[t]);
            }
        }

        var count = 0;

        for (t = 0; t < editedUserID.length; t++)
        {
            count = 0;
            while (count < editedUserID.length)
            {
                if (document.getElementById("userNameTxt" + editedUserID[t]).value == document.getElementById("userNameTxt" + editedUserID[count]).value && t != count)
                {
                    repeatUserName += " " + document.getElementById("userNameTxt" + editedUserID[t]).value + " ,";
                    break;
                }
                if ($("#namechangereason" + editedUserID[t]).css('display') != 'none')
                {
                    if (document.getElementById("changereason" + editedUserID[t]).value == '')
                    {
                        changereasonUserName += " " + document.getElementById("userNameTxt" + editedUserID[t]).value + " ,";
                        break;
                    }
                }
                count++;
            }
        }

        if (changereasonUserName != "")
        {
            var message = "Reason for change can not be empty for" + changereasonUserName.replace(/,\s*$/, "");
            Helpers.prompt(message);
            $("#changereason" + currentUpdateUserId).focus();
            flagBlank = 1;
            //break;
        } else
        {
            if (repeatUserName == "")
            {
                /*document.getElementById("pageAction").value = "save";
                 setTryingToUnload();*/
                //document.frmTeacherReport.submit();
                //save data here
                $.ajax({
                    type: 'POST',
                    url: Helpers.constants['CONTROLLER_PATH'] + "teachermystudents/updateStudentDetails",
                    dataType: 'json',
                    //data : {'userID' : currentUpdateUserId, 'userNameTxt' : $("#userNameTxt"+currentUpdateUserId).val(), 'childNameTxt' : $("#childNameTxt"+currentUpdateUserId).val(), 'childEmailTxt' : $("#childEmailTxt"+currentUpdateUserId).val(), 'DOBTxt' : $("#DOBTxt"+currentUpdateUserId).val(), 'parentEmailTxt' : $("#parentEmailTxt"+currentUpdateUserId).val(), 'lstPwd' : $("#lstPwd"+currentUpdateUserId).val(), 'childClass' : $("#childClass_"+currentUpdateUserId).val(), 'childSection' : $("#childSection_"+currentUpdateUserId).val()},
                    data: {'userID': currentUpdateUserId, 'userNameTxt': $("#userNameTxt" + currentUpdateUserId).val(), 'childNameTxt': $("#childNameTxt" + currentUpdateUserId).val(), 'DOBTxt': $("#DOBTxt" + currentUpdateUserId).val(), 'parentEmailTxt': $("#parentEmailTxt" + currentUpdateUserId).val(), 'lstPwd': $("#lstPwd" + currentUpdateUserId).val(), 'childClass': $("#childClass_" + currentUpdateUserId).val(), 'childSection': childSection_selected, 'changereason': $("#changereason" + currentUpdateUserId).val()},
                }).done(function (data) {

                    Helpers.ajax_response(saveStudentDataAjax, data, [currentUpdateUserId, sr_no]);
                    /*var html="<option value=''>select</option>";
                     $.each(data,function(key,value){
                     html+="<option value='"+value+"'>"+value+"</option>";
                     });
                     $('#th_grade_select').html(html);*/
                });

            } else
            {
                repeatUserName = repeatUserName.substr(0, repeatUserName.length - 1);

                var msg = "Usernames can not be the same : " + repeatUserName;
                Helpers.prompt(msg);
            }
        }
    }
}

function saveStudentDataAjax(data, extraParams)
{
    var currentUpdateUserId = extraParams[0];
    var sr_no = extraParams[1];

    if (data != '')
    {
        if (data.msg == 'true')
        {
            var msg = "This username already exist.";
            Helpers.prompt(msg);
        } else
        {
            $("#tr_" + currentUpdateUserId).empty();
            $("#tr_" + currentUpdateUserId).append("<td style='text-align:center'>" + sr_no + "</td>");
            $("#tr_" + currentUpdateUserId).append("<td id='field" + currentUpdateUserId + 1 + "'>" + data.userName + "</td>");
            $("#tr_" + currentUpdateUserId).append("<td id='field" + currentUpdateUserId + 2 + "'>" + data.childName + "</td>");
            //$("#tr_"+currentUpdateUserId).append("<td id='field"+currentUpdateUserId+3 +"'>" + data.childEmail + "</td>");
            $("#tr_" + currentUpdateUserId).append("<td id='field" + currentUpdateUserId + 4 + "'>" + data.DOB + "</td>");
            $("#tr_" + currentUpdateUserId).append("<td id='field" + currentUpdateUserId + 5 + "'>" + data.parentEmail + "</td>");
            $("#tr_" + currentUpdateUserId).append("<td id='field" + currentUpdateUserId + 6 + "'>" + data.childClass + "<input type='hidden' id='childClass_" + currentUpdateUserId + "' value='" + data.childClass + "'/></td>");
            $("#tr_" + currentUpdateUserId).append("<td id='field" + currentUpdateUserId + 7 + "'>" + data.childSection + "<input type='hidden' id='childSection_" + currentUpdateUserId + "' value='" + data.childSection + "'/></td>");
            $("#tr_" + currentUpdateUserId).append("<td id='field" + currentUpdateUserId + 8 + "'>****</td>");

            $("#tr_" + currentUpdateUserId).append("<td><input class='form-control' type='button'  id='edit_" + currentUpdateUserId + "' onclick='EditTheFields(" + currentUpdateUserId + ", " + sr_no + ")' value='Edit'/><input class='form-control' type='button' style='display:none;'  id='cancel" + currentUpdateUserId + "' onclick='CancelTheFields(" + currentUpdateUserId + "," + sr_no + ")' value='Cancel'/><input class='form-control' type='button' style='display:none;'  id='save_" + currentUpdateUserId + "' onclick='SaveTheData(this.id, " + sr_no + ")' value='Save'/></td>");
            $("#tr_" + currentUpdateUserId).append("<input type='hidden' id='flagCheck" + currentUpdateUserId + "' name='flagCheck" + currentUpdateUserId + "' value='0'>");
            var msg = "Details updated successfully";
            document.getElementById("changereason" + currentUpdateUserId).value = '';
            document.getElementById("namechangereason" + currentUpdateUserId).style.display = "none";
            Helpers.prompt(msg);
        }
    }
}

function checkChangeData(userID)
{
    if (document.getElementById("userNameTxt" + userID).value != document.getElementById("userNameHdn" + userID).value || document.getElementById("childNameTxt" + userID).value != document.getElementById("childNameHdn" + userID).value)
    {
        document.getElementById("namechangereason" + userID).style.display = "table-row";
        /*alert('hello');
         $("#namechangereason").show();*/
    } else
    {
        document.getElementById("changereason" + userID).value = '';
        $("#namechangereason").hide();
        //document.getElementById("namechangereason"+userID).style.display = "none";
    }
}

function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function onlyAlpha(value) {
    var regex = /^[a-zA-Z ]*$/;
    if (regex.test(value)) {
        return true;
    } else {
        return false;
    }
}

function DateFormat(txt, keyCode)
{
    var isShift = false;

    if (keyCode == 16)
        isShift = true;
    //Validate that its Numeric
    if (((keyCode >= 48 && keyCode <= 57) || keyCode == 8 ||
            keyCode <= 37 || keyCode <= 39 ||
            (keyCode >= 96 && keyCode <= 105)) && isShift == false)
    {
        if ((txt.value.length == 2 || txt.value.length == 5) && keyCode != 8)
        {
            txt.value += seperator;
        }
        return true;
    } else
    {
        return false;
    }
}

var dtCh = "-";
var minYear = 1900;
var maxYear = 2100;

function isInteger(s) {
    var i;
    for (i = 0; i < s.length; i++) {
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9")))
            return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag) {
    var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++) {
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1)
            returnString += c;
    }
    return returnString;
}

function daysInFebruary(year) {
    // February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ((!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28);
}
function DaysArray(n) {
    for (var i = 1; i <= n; i++) {
        this[i] = 31
        if (i == 4 || i == 6 || i == 9 || i == 11) {
            this[i] = 30
        }
        if (i == 2) {
            this[i] = 29
        }
    }
    return this
}

function isDate(dtStr) {

    var daysInMonth = DaysArray(12)
    var pos1 = dtStr.indexOf(dtCh)
    var pos2 = dtStr.indexOf(dtCh, pos1 + 1)
    var strDay = dtStr.substring(0, pos1)
    var strMonth = dtStr.substring(pos1 + 1, pos2)
    var strYear = dtStr.substring(pos2 + 1)
    strYr = strYear
    if (strDay.charAt(0) == "0" && strDay.length > 1)
        strDay = strDay.substring(1)
    if (strMonth.charAt(0) == "0" && strMonth.length > 1)
        strMonth = strMonth.substring(1)
    for (var i = 1; i <= 3; i++) {
        if (strYr.charAt(0) == "0" && strYr.length > 1)
            strYr = strYr.substring(1)
    }
    month = parseInt(strMonth)
    day = parseInt(strDay)
    year = parseInt(strYr)
    if (pos1 == -1 || pos2 == -1) {
        alert("The date format should be : dd-mm-yyyy")
        return false
    }
    if (strMonth.length < 1 || month < 1 || month > 12) {
        alert("Please enter a valid month")
        return false
    }
    if (strDay.length < 1 || day < 1 || day > 31 || (month == 2 && day > daysInFebruary(year)) || day > daysInMonth[month]) {
        alert("Please enter a valid day")
        return false
    }
    if (strYear.length != 4 || year == 0 || year < minYear || year > maxYear) {
        alert("Please enter a valid 4 digit year between " + minYear + " and " + maxYear)
        return false
    }
    if (dtStr.indexOf(dtCh, pos2 + 1) != -1 || isInteger(stripCharsInBag(dtStr, dtCh)) == false) {
        alert("Please enter a valid date")
        return false
    }
    return true
}

function validateDate(dt) {

    if (dt.value != "" && isDate(dt.value) == false) {
        dt.focus()
        return false
    }

    return true
}

function CancelTheFields(userID, sr_no)
{

    userName = $("#userNameHdn" + userID).val();
    childName = $("#childNameHdn" + userID).val();
    //childEmail  = $("#childEmailHdn"+userID).val();
    DOB = $("#DOBHdn" + userID).val();
    parentEmailbr = $("#parentEmailHdn" + userID).val();
    parentEmail = parentEmailbr.replace(/,/ig, '<br>');
    childClass = $("#childClass_" + userID).val();
    childSection = $("#childSection_" + userID).val();

    document.getElementById("edit_" + userID).style.display = "";
    document.getElementById("cancel" + userID).style.display = "none";
    document.getElementById("save_" + userID).style.display = "none";

    $("#tr_" + userID).empty();
    $("#tr_" + userID).append("<td style='text-align:center'>" + sr_no + "</td>");
    $("#tr_" + userID).append("<td id='field" + userID + 1 + "'>" + userName + "</td>");
    $("#tr_" + userID).append("<td id='field" + userID + 2 + "'>" + childName + "</td>");
    //$("#tr_"+userID).append("<td id='field"+userID+3 +"'>" + childEmail + "</td>");
    $("#tr_" + userID).append("<td id='field" + userID + 4 + "'>" + DOB + "</td>");
    $("#tr_" + userID).append("<td id='field" + userID + 5 + "'>" + parentEmail + "</td>");
    $("#tr_" + userID).append("<td id='field" + userID + 6 + "'>" + childClass + "<input type='hidden' id='childClass_" + userID + "' value='" + childClass + "'/></td>");
    $("#tr_" + userID).append("<td id='field" + userID + 7 + "'>" + childSection + "<input type='hidden' id='childSection_" + userID + "' value='" + childSection + "'/></td>");
    $("#tr_" + userID).append("<td id='field" + userID + 8 + "'>****</td>");

    $("#tr_" + userID).append("<td><input class='form-control' type='button'  id='edit_" + userID + "' onclick='EditTheFields(" + userID + ", " + sr_no + ")' id='' value='Edit'/><input class='form-control' type='button' style='display:none'  id='cancel" + userID + "' onclick='CancelTheFields(" + userID + "," + sr_no + ")' id='' value='Cancel'/><input class='form-control' type='button' style='display:none'  id='save_" + userID + "' onclick='SaveTheData(this.id, " + sr_no + ")' value='Save'/></td>");
    $("#tr_" + userID).append("<input type='hidden' id='flagCheck" + userID + "' name='flagCheck" + userID + "' value='0'>");
    document.getElementById("changereason" + userID).value = '';
    document.getElementById("namechangereason" + userID).style.display = "none";
}

function showTeacherMyStudentActivatePage()
{
    //checking if user is super admin or not
    if (sessionData.category == 'School Admin' && sessionData.subcategory == 'All') {
        return false;
    }
    //end
    $("#teacher_table_view").hide();
    $("#teacher_data_note").css('display', 'none');
    $("#teacherBtns").css('display', 'none');
    $("#th_my_student_activate_grade_select").val("");
    $("#th_my_student_activate_section_select").val("");
    var html = "<option value=''>select</option>";
    $("#th_my_student_activate_section_select").html(html);
    $("#th_my_student_activate_groupskill_select").val("");
    $("#my_student_no_data").hide();
    $("#student_data_note").hide();
    $("#student_dataparentemail_note").hide();
    //$( ".datepicker" ).datepicker({ dateFormat: 'dd-mm-yy' });
    $('#studentActivateStartDate').datepicker({dateFormat: 'dd-mm-yy', minDate: 0, changeYear: true});
    $('#studentActivateEndDate').datepicker({dateFormat: 'dd-mm-yy', minDate: 0, changeYear: true});
    $("#studentActivateStartDate").datepicker().datepicker("setDate", new Date());
    $("#studentActivateEndDate").datepicker().datepicker("setDate", new Date());
    /*var prevMonth="0"+($('#studentActivateEndDate').val().substring(3, 5) - 1);
     if(prevMonth=="00")
     prevMonth=12;
     var newDate=$('#studentActivateEndDate').val().substring(0, 2)+"/"+prevMonth+"/"+$('#studentActivateEndDate').val().substring(6);
     $("#studentActivateStartDate").datepicker().datepicker("setDate", newDate);*/
    studentActivatePageStem();

    //hidePluginIfAny();
    $("#student_table_activate").show();
    if ($('#th_my_student_activate_grade_select').val() == "") {
        $.ajax({
            type: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "teachermystudents/getTeacherMappedClass",
            dataType: 'json',
        }).done(function (data) {
            Helpers.ajax_response(studentActiveGradeAjax, data, []);
        });
    }

    if ($('#th_my_student_activate_groupskill_select').val() == "") {
        $.ajax({
            type: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "teachermystudents/getGroupSkill",
            dataType: 'json',
        }).done(function (data) {

            Helpers.ajax_response(getGroupSkillsAjax, data, []);
        });
    }
}

function getGroupSkillsAjax(data, extraParams)
{
    var html = "<option value=''>select</option>";
    $.each(data, function (key, value) {
        html += "<option value='" + value.groupSkillID + "'>" + value.name + "</option>";
    });
    $('#th_my_student_activate_groupskill_select').html(html);
}

function studentActiveGradeAjax(data, extraParams)
{
    var html = "<option value=''>select</option>";
    $.each(data, function (key, value) {
        html += "<option value='" + value + "'>" + value + "</option>";
    });
    $('#th_my_student_activate_grade_select').html(html);
}

$('#th_my_student_activate_grade_select').on("change", function () {

    $('#studentActivateStartDate').datepicker({dateFormat: 'dd-mm-yy', minDate: 0, changeYear: true});
    $('#studentActivateEndDate').datepicker({dateFormat: 'dd-mm-yy', minDate: 0, changeYear: true});
    $("#studentActivateStartDate").datepicker().datepicker("setDate", new Date());
    $("#studentActivateEndDate").datepicker().datepicker("setDate", new Date());
    $("#th_my_student_activate_groupskill_select").val("");

    if ($(this).val() == "") {
        $('#th_my_student_activate_section_select').html("<option value=''>select</option>");
    } else {
        $.ajax({
            type: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "teachermystudents/getTeacherMappedSection",
            dataType: 'json',
            data: {'selectedClass': $(this).val()},
        }).done(function (data) {
            Helpers.ajax_response(getAcitvateStudentSectionAjax, data, []);
        });
    }
});

function getAcitvateStudentSectionAjax(data, extraParams)
{
    var html = "<option value=''>select</option>";
    $.each(data, function (key, value) {
        html += "<option value='" + value + "'>" + value + "</option>";
    });
    $('#th_my_student_activate_section_select').html(html);
}

$("#th_my_student_activate_go").on("click", function () {
    var message = "";
    if ($('#th_my_student_grade_select').val() == "")
        message += "Please select a grade<br>";
    if ($('#th_my_student_section_select').val() == "")
        message += "Please select a section";
    if (message == "") {
        var userIDArr = [];
        //  get report data
        $.ajax({
            type: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "teachermystudents/showEditStudentDetails",
            dataType: 'json',
            data: {'class': $('#th_my_student_grade_select').val(), 'section': $('#th_my_student_section_select').val(), 'childName': $("#ChildNameInput").val()},
        }).done(function (data) {

            Helpers.ajax_response(getEditStudentDetailsAjax, data, [userIDArr]);
        });
    } else {
        Helpers.prompt(message);
    }
});

function getEditStudentDetailsAjax(data, extraParams)
{
    var userIDArr = extraParams[0];
    $('#teacher_my_students div > .none').show();
    $('.students_table').show();

    for (var i = 0; i < data.studentData.length; i++) {

        userIDArr.push(data.studentData[i].userID);

        var sr_no = i + 1;

        tr = $('<tr id="tr_' + data.studentData[i].userID + '"/>');
        tr.append("<td style='text-align:center'>" + sr_no + "</td>");
        tr.append("<td id='field" + data.studentData[i].userID + 1 + "'>" + data.studentData[i].userName + "</td>");
        tr.append("<td id='field" + data.studentData[i].userID + 2 + "'>" + data.studentData[i].childName + "</td>");
        //tr.append("<td id='field"+data.studentData[i].userID+3 +"'>" + data.studentData[i].childEmail + "</td>");
        tr.append("<td id='field" + data.studentData[i].userID + 4 + "'>" + data.studentData[i].childDob + "</td>");
        tr.append("<td id='field" + data.studentData[i].userID + 5 + "'>" + data.studentData[i].parentEmail.replace(/,/ig, '<br>') + "</td>");
        tr.append("<td id='field" + data.studentData[i].userID + 6 + "'>" + data.studentData[i].childClass + "<input type='hidden' id='childClass_" + data.studentData[i].userID + "' value='" + data.studentData[i].childClass + "'/></td>");
        tr.append("<td id='field" + data.studentData[i].userID + 7 + "'>" + data.studentData[i].childSection + "<input type='hidden' id='childSection_" + data.studentData[i].userID + "' value='" + data.studentData[i].childSection + "'/></td>");
        tr.append("<td id='field" + data.studentData[i].userID + 8 + "'>****</td>");
        tr.append("<td><input class='form-control' type='button'  id='edit_" + data.studentData[i].userID + "' onclick='EditTheFields(" + data.studentData[i].userID + ", " + sr_no + ")' id='' value='Edit'/><input class='form-control' type='button' style='display:none'  id='cancel" + data.studentData[i].userID + "' onclick='CancelTheFields(" + data.studentData[i].userID + "," + sr_no + ")' id='' value='Cancel'/><input type='button' class='form-control' style='display:none'  id='save_" + data.studentData[i].userID + "' onclick='SaveTheData(this.id, " + sr_no + ")' value='Save'/></td>");
        tr.append("<input type='hidden' id='flagCheck" + data.studentData[i].userID + "' name='flagCheck" + data.studentData[i].userID + "' value='0'>");
        $('#my_student_view_tbody').append(tr);
    }
    /*td = $('<tr><td align="center" colspan="10"><input type="button" class="button" onclick="SaveTheData()" value="Save" id="save"></td></tr>');
     $('#my_student_view_tbody').append(td);*/
    var userIDString = userIDArr.join(',');
    $("#userIDString").val(userIDString);
}

$("#activate_students_li").on("click", function () {
    //checking if user is super admin or not
    if (sessionData.category == 'School Admin' && sessionData.subcategory == 'All') {
        $('#activate_students_li').addClass('opacity-0');
        var message = "You don't have necessary rights to perform this action.";
        Helpers.prompt(message);
        return false;
    }
    //end
    $(this).attr('class', 'active');
    $("#view_students_li").removeAttr('class');
    $("#view_teachers_li").removeAttr('class');
    $("#view_students").hide();
    $("#activate_topic").show();
    $("#student_table_view").hide();

});
$("#view_students_li").on("click", function () {
    $(this).attr('class', 'active');
    $("#activate_students_li").removeAttr('class');
    $("#view_teachers_li").removeAttr('class');
    $("#view_students").show();
    $("#activate_topic").hide();
    $("#student_table_activate").hide();
});

function studentActivatePageStem()
{
    var startDate = $("#studentActivateStartDate").val();

    var endDate = $("#studentActivateEndDate").val();

    var str = 'This selection is valid from ' + startDate + ' to ' + endDate + ' for all non-passage questions. Once the range of questions from the selected group skill have been exhausted OR the time has elapsed, you will be notified and the flow will revert to the default logic.';
    $("#activate_topic_txtarea").text(str);
}


$("#save_activate_changes").on("click", function () {
    var message = "";
    if ($('#th_my_student_activate_grade_select').val() == "")
        message += "Please select a grade. <br>";
    if ($('#th_my_student_activate_section_select').val() == "")
        message += "Please select a section. <br>";
    if ($('#studentActivateStartDate').val() == "")
        message += "Please select a Start Date. <br>";
    if ($('#studentActivateEndDate').val() == "")
        message += "Please select a End Date. <br>";
    if ($('#th_my_student_activate_groupskill_select').val() == "")
        message += "Please select a Topic.";

    //  check end date > start date
    var startDate = new Date($("#studentActivateStartDate").datepicker('getDate'));
    var endDate = new Date($("#studentActivateEndDate").datepicker('getDate'));
    if (endDate < startDate)
        message += "End Date must be greater than or equal to Start Date.";

    if (message == "")
    {
        var groupSkillID = $("#th_my_student_activate_groupskill_select").val();
        var startDate = $("#studentActivateStartDate").val();
        var endDate = $("#studentActivateEndDate").val();
        var grade = $("#th_my_student_activate_grade_select").val();
        var section = $("#th_my_student_activate_section_select").val();

        $.ajax({
            type: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "teachermystudents/saveCurrentStatusSkill",
            dataType: 'json',
            data: {'groupSkillID': groupSkillID, 'start_date': startDate, 'end_date': endDate, 'grade': grade, 'section': section},
        }).done(function (data) {

            Helpers.ajax_response('', data, []);
            //Helpers.prompt('Details updated/saved successfully.');       
        });
    } else
    {
        Helpers.prompt(message);
    }
});

$("#studentActivateStartDate").on("change", function () {
    studentActivatePageStem();
});
$("#studentActivateEndDate").on("change", function () {
    studentActivatePageStem();
});

/*********************************Reports*****************************************************/
function defaultReport(reportType) {
    if (typeof reportType === "undefined" || reportType === null) {
        reportType = "default";
    }
    if (reportType == "default") {
        //$('#teacherReportDatatable').hide();
        $('select#th_teacher_report_grade_select option:eq(1)').attr('selected', 'selected');
        var startDate = new Date();
        startDate.setDate(startDate.getDate() - 7);
        var startDate = $.datepicker.formatDate('dd-mm-yy', startDate);
        $("#teacherReportStartDate").val(startDate);
        var endDate = new Date();
        endDate.setDate(endDate.getDate() - 1);
        var endDate = $.datepicker.formatDate('dd-mm-yy', endDate);
        $("#teacherReportEndDate").val(endDate);
        getTeacherReportSection($('#th_teacher_report_grade_select').val(), 1);
    } else if (reportType == "custom") {
        $("select#th_teacher_report_grade_select").val($('#th_grade_select').val());
        var startDate = new Date();
        startDate.setDate(startDate.getDate() - 7);
        var startDate = $.datepicker.formatDate('dd-mm-yy', startDate);
        $("#teacherReportStartDate").val(startDate);
        var endDate = new Date();
        endDate.setDate(endDate.getDate() - 1);
        var endDate = $.datepicker.formatDate('dd-mm-yy', endDate);
        $("#teacherReportEndDate").val(endDate);
        getTeacherReportSection($('#th_teacher_report_grade_select').val(), $('#th_section_select').val());
    }

}
function getTeacherReportSection(selectedClass, isSelectDefault) {
    $.ajax({
        type: 'POST',
        url: Helpers.constants['CONTROLLER_PATH'] + "teacherhome/getTeacherMappedSection",
        dataType: 'json',
        data: {'selectedClass': selectedClass},
    }).done(function (data) {
        Helpers.ajax_response(getTeacherReportSectionAjax, data, [isSelectDefault]);
    });
}

function showTeacherReportsOverAllPage(reportType)
{
    hidePluginIfAny();
    if (typeof reportType === "undefined" || reportType === null) {
        reportType = "default";
    }
    $("#note_openended").hide();
    $('#teacherReportStartDate').datepicker({dateFormat: 'dd-mm-yy', changeYear: true, yearRange: "-50:+50"});
    $('#teacherReportEndDate').datepicker({dateFormat: 'dd-mm-yy', maxDate: 0, changeYear: true, yearRange: "-50:"});
    //$('#teacherReportEndDate').datepicker({dateFormat: 'dd-mm-yy'});
    $("#teacherReportStartDate").datepicker().datepicker("setDate", new Date());
    $("#teacherReportEndDate").datepicker().datepicker("setDate", new Date());
    $('#th_teacher_report_grade_select').val("");
    var html = "<option value=''>select</option>";
    $('#th_teacher_report_section_select').html(html);

    if ($('#th_teacher_report_grade_select').val() == "") {
        $.ajax({
            type: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "teacherreport/getTeacherMappedClass",
            dataType: 'json',
        }).done(function (data) {
            Helpers.ajax_response(getTeacherReportGradeAjax, data, [reportType]);

        });
    }
}
function getTeacherReportGradeAjax(data, extraParams)
{
    var reportType = extraParams[0];
    if (typeof reportType === "undefined" || reportType === null) {
        reportType = "default";
    }
    var html = "<option value=''>select</option>";
    $.each(data, function (key, value) {
        html += "<option value='" + value + "'>" + value + "</option>";
    });
    $('#th_teacher_report_grade_select').html(html);
    defaultReport(reportType);
}

$('#th_teacher_report_grade_select').on("change", function () {
    //showTeacherReportsOverAllPage();
    if ($(this).val() == "") {
        $('#th_teacher_report_section_select').html("<option value=''>select</option>");
    } else {
        $.ajax({
            type: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "teacherreport/getTeacherMappedSection",
            dataType: 'json',
            data: {'selectedClass': $(this).val()},
        }).done(function (data) {
            Helpers.ajax_response(getTeacherReportSectionAjax, data, []);
        });
    }
});

function getTeacherReportSectionAjax(data, extraParams)
{
    var isSelectDefault = extraParams[0];
    if (typeof isSelectDefault === "undefined" || isSelectDefault === null) {
        isSelectDefault = 0;
    }
    var html = "<option value=''>select</option>";
    $.each(data, function (key, value) {
        html += "<option value='" + value + "'>" + value + "</option>";
    });
    $('#th_teacher_report_section_select').html(html);

    if (isSelectDefault == 1) {
        if ($('select[id=th_teacher_report_section_select] option:eq(1)').length == 1)
        {
            $('select[id=th_teacher_report_section_select] option:eq(1)').attr('selected', 'selected');
            $("#th_report_go-1").click();
        }
    } else if (isSelectDefault != 0) {
        if ($('select[id=th_teacher_report_section_select] option:eq(1)').length == 1)
        {
            $("#th_teacher_report_section_select").val(isSelectDefault);
            $("#th_report_go-1").click();
        }
    }
}
//ng-detail report

// Defining angularjs application.
// Controller function and passing $http service and $scope var.


englishInterface.directive('input', function ($parse) {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function (scope, element, attrs) {
            if (attrs.ngModel && attrs.value) {
                $parse(attrs.ngModel).assign(scope, attrs.value);
            }
        }
    };
});

englishInterface.factory('Excel', function ($window) {
    var uri = 'data:application/vnd.ms-excel;base64,',
            template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
            base64 = function (s) {
                return $window.btoa(unescape(encodeURIComponent(s)));
            },
            format = function (s, c) {
                return s.replace(/{(\w+)}/g, function (m, p) {
                    return c[p];
                })
            };
    return {
        tableToExcel: function (tableId, worksheetName) {
            var table = $('#' + tableId),
                    ctx = {worksheet: worksheetName, table: table.html()}, href = uri + base64(format(template, ctx));
            var blob = new Blob([(format(template, ctx))], {
                type: uri
            });
            //  return href;
            saveAs(blob, worksheetName + ".xls");
            return;
        }
    };
});

englishInterface.controller('reportController', function ($scope, $http, $timeout, datatable, Excel) {
    //download excel
    $scope.exportToExcel = function (tableId) { // ex: '#my-table'
        var exportHref = Excel.tableToExcel(tableId, 'DetailedReport');
        //$timeout(function(){location.href=exportHref;},100); // trigger download
    }
    //end


    // create a blank object to handle form data.
    $scope.report = {};
    // calling our submit function.
    $scope.submitFormDetailReport = function () {
        //validation
        var message = "";
        if ($('#th_teacher_report_grade_select').val() == "")
            message += "Please select a grade.<br>";
        if ($('#th_teacher_report_section_select').val() == "")
            message += "Please select a section.";
        //  check end date > start date

        var startDate = new Date($("#teacherReportStartDate").datepicker('getDate'));
        var endDate = new Date($("#teacherReportEndDate").datepicker('getDate'));
        if (endDate < startDate)
            message += "End Date must be greater than Start Date.";

        if (message != "") {
            Helpers.prompt(message);
            return false;
        }

        //end
        // Posting data to php file
        $http({
            method: 'POST',
            //  url     : Helpers.constants['CONTROLLER_PATH'] + "teacherreport/getTeacherMappedSection",
            url: Helpers.constants['CONTROLLER_PATH'] + "teacherreport/generateTeacherReportForStudentInterface",
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function (obj) {
                var stringParam = '';
                stringParam = 'startDate=' + $("#teacherReportStartDate").val() + '&endDate=' + $("#teacherReportEndDate").val() + '&childclass=' + $("#th_teacher_report_grade_select option:selected").val() + '&childsection=' + $("#th_teacher_report_section_select option:selected").val();
                return stringParam;
            },
            data: $scope.report, //forms user object
            // headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
        })
                .success(function (data) {
                    var resultData = $.parseJSON(data.result_data);
                    if (resultData[0].noActiveUsersFound == 1) {
                        // Showing errors.
                        $('#teacherReportDatatable').hide();
                        $('#datatableNoData').html('No students found');
                        $('#datatableNoData').show();
                    } else {
                        var datatableConfig = {
                            "name": "simple_datatable",
                            "extraHeaders": {number: 1},
                            "columns": [
                                {
                                    "header": "Student Name",
                                    "property": "name",
                                    "order": true,
                                    "type": "text",
                                    "hide": false,
                                    "extraHeaders": {"0": ""}
                                },
                                {
                                    "header": "Total Usage (hh:mm)",
                                    "property": "timeSpent",
                                    "order": true,
                                    "type": "time",
                                    "hide": false,
                                    "extraHeaders": {"0": ""}
                                },
                                {
                                    "header": "No. of Passages",
                                    "property": "totalPassageAttempted",
                                    "order": true,
                                    "type": "number",
                                    "hide": false,
                                    "extraHeaders": {"0": ""}
                                },
                                {
                                    "header": "Total Questions",
                                    "property": "totalQuesAttempted",
                                    "order": true,
                                    "type": "number",
                                    "hide": false,
                                    "extraHeaders": {"0": ""}
                                },
                                {
                                    "header": "Accuracy",
                                    "property": "accuracy",
                                    "render": "{{cellValue}}%",
                                    "order": true,
                                    "type": "number",
                                    "hide": false,
                                    "extraHeaders": {"0": ""}
                                },
                                {
                                    "header": "Sparkies (Total)",
                                    "property": "sparkies",
                                    "order": true,
                                    "type": "number",
                                    "hide": false,
                                    "extraHeaders": {"0": ""}
                                },
                                {
                                    "header": "No. of days logged in",
                                    "extraHeader": "No. of days logged in",
                                    "property": "totalDaysSessions",
                                    "order": true,
                                    "type": "text",
                                    "hide": true,
                                    "extraHeaders": {"0": ""}
                                },
                                {
                                    "header": "Total Questions",
                                    "extraHeader": "Listening",
                                    "property": "listenTotalQues",
                                    "order": true,
                                    "type": "number",
                                    "hide": true,
                                    "extraHeaders": {"0": "Listening"}
                                },
                                {
                                    "header": "Accuracy",
                                    "extraHeader": "Listening",
                                    "render": "{{cellValue}}%",
                                    "property": "listenQuesAcc",
                                    "order": true,
                                    "type": "number",
                                    "hide": true,
                                    "extraHeaders": {"0": "Listening"}
                                },
                                {
                                    "header": "Total Questions",
                                    "extraHeader": "Reading",
                                    "property": "readTotalQues",
                                    "order": true,
                                    "type": "number",
                                    "hide": true,
                                    "extraHeaders": {"0": "Reading"}
                                },
                                {
                                    "header": "Accuracy",
                                    "extraHeader": "Reading",
                                    "render": "{{cellValue}}%",
                                    "property": "readQuesAcc",
                                    "order": true,
                                    "type": "number",
                                    "hide": true,
                                    "extraHeaders": {"0": "Reading"}
                                }, {
                                    "header": "Total Questions",
                                    "extraHeader": "Grammar",
                                    "property": "grammarTotalQues",
                                    "order": true,
                                    "type": "number",
                                    "hide": true,
                                    "extraHeaders": {"0": "Grammar"}
                                }, {
                                    "header": "Accuracy",
                                    "extraHeader": "Grammar",
                                    "render": "{{cellValue}}%",
                                    "property": "grammarQuesAcc",
                                    "order": true,
                                    "type": "number",
                                    "hide": true,
                                    "extraHeaders": {"0": "Grammar"}
                                }, {
                                    "header": "Total Questions",
                                    "property": "vocabTotalQues",
                                    "extraHeader": "Vocabulary",
                                    "order": true,
                                    "type": "number",
                                    "hide": true,
                                    "extraHeaders": {"0": "Vocabulary"}
                                }, {
                                    "header": "Accuracy",
                                    "extraHeader": "Vocabulary",
                                    "render": "{{cellValue}}%",
                                    "property": "vocabQuesAcc",
                                    "order": true,
                                    "type": "number",
                                    "hide": true,
                                    "extraHeaders": {"0": "Vocabulary"}
                                }, {
                                    "header": "No. of Essays Submitted",
                                    "extraHeader": "No. of Essays Submitted",
                                    "property": "totalEssayAttempt",
                                    "order": true,
                                    "type": "number",
                                    "hide": true,
                                    "extraHeaders": {"0": ""}
                                }
                            ],
                            "pagination": {
                                "active": false, //Active or not
                                "mode": 'local'
                            },
                            "order": {
                                "mode": 'local'
                            },
                            "showSequence": {
                                "active": true, //Active or not
                            },
                            "hide": {
                                "active": true, //Active or not
                                "byDefault": ['totalDaysSessions', 'listenTotalQues', 'readTotalQues', 'grammarTotalQues', 'vocabTotalQues', 'totalEssayAttempt'], //set default column in hide mode
                                "showButton": true//Show the hide button in the toolbar
                            },
                            "compact": true,
                            callbackEndDisplayResult: function () {
                                var isMobile = false; //initiate as false
// device detection
                                if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
                                        || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0, 4)))
                                    isMobile = true;
                                if (isMobile == true) {
                                    $("#v2-download-excel").hide();
                                    $("#teacher_reports_page").addClass('smoothScrollIpad');
                                }
                                $('#datatableNoData').hide();
                                $('#teacherReportDatatable').show();

                            },
                        };

                        //Simple exemple of data

                        //Init the datatable with his configuration
                        $scope.datatable = datatable(datatableConfig);
                        //Set the data to the datatable
                        $scope.datatable.setData(resultData);


                        // $scope.message = data.message;
                    }
                });
    };
});


/*********************************End Report*****************************************************/

$(document).delegate(".fa-square", "click", (function () {
    var cRB = $(".fa-check-square"),
            sc = 0,
            distSc = 0,
            dNum = 0;
    for (var i = 0; i < cRB.length; i++)
        rubricVals[getelemID(cRB.eq(i).attr("name"))] = cRB.eq(i).attr("value") * 1;
    for (var i = 0; i < 7; i++) {
        distSc += (rubricVals[i] == 0) ? $(".rubric-body tr").eq(i + 1).attr("min") * 1 + 8 : 0;
        dNum += (rubricVals[i] == 0) ? 1 : 0;
    }
    distSc = (dNum == 7) ? 0 : (distSc / (7 - dNum));
    for (var i = 0; i < 7; i++)
        sc += (rubricVals[i] <= 0) ? 0 : (rubricVals[i] - 1) * (2 + distSc / 4) + ($(".rubric-body tr").eq(i + 1).attr("min") * 1);
    sc = Math.round(sc);
    $("#sugScore").html(sc / 10);
    $("#essayScore").val(sc / 10);
}));



function getelemID(nm) {
    return (nm.charAt(1) * 1) - 1;
}

function showTeacherAllotmentViewPage() {
    if ($('#v2-showEssayData-grade-select').val() == "") {
        $.ajax({
            type: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "essayAllotment/getTeacherPrimaryMappedClass",
            dataType: 'json',
        }).done(function (data) {
            Helpers.ajax_response(getTeacherEssayAllotmentGrade, data, []);
            $('select#v2-showEssayData-grade-select option:eq(1)').attr('selected', 'selected');
            getTeacherEssayAllotmentSection($('#v2-showEssayData-grade-select').val(), 1);
        });
    } else {
        $('#v2-showEssayData').click();
    }
}
function getTeacherEssayAllotmentGrade(data, extraParams)
{
    var html = "<option value=''>select</option>";
    $.each(data, function (key, value) {
        html += "<option value='" + value + "'>" + value + "</option>";
    });
    $('#v2-showEssayData-grade-select').html(html);

    //  Select Default first option
    if ($('select[id=v2-showEssayData-grade-select] option:eq(1)').length == 1 && $('select[id=v2-showEssayData-grade-select] option').length == 2)
    {
        $('select[id=v2-showEssayData-grade-select] option:eq(1)').attr('selected', 'selected');

        getTeacherEssayAllotmentSection($('#v2-showEssayData-grade-select').val(), 1);
    }
}

function getTeacherEssayAllotmentSection(selectedClass, isSelectDefault) {
    $.ajax({
        type: 'POST',
        url: Helpers.constants['CONTROLLER_PATH'] + "essayAllotment/getTeacherPrimaryMappedSection",
        dataType: 'json',
        data: {'selectedClass': selectedClass},
    }).done(function (data) {
        Helpers.ajax_response(getTeacherEssaySectionAjax, data, [isSelectDefault]);
    });
}



function getTeacherEssaySectionAjax(data, extraParams)
{
    var isSelectDefault = extraParams[0];
    var html = "<option value=''>select</option>";
    $.each(data, function (key, value) {
        html += "<option value='" + value + "'>" + value + "</option>";
    });
    $('#v2-showEssayData-section-select').html(html);
    if (isSelectDefault == 1) {
        if ($('select[id=v2-showEssayData-section-select] option:eq(1)').length == 1)
        {
            $('select[id=v2-showEssayData-section-select] option:eq(1)').attr('selected', 'selected');
            $('#v2-showEssayData').click();
        } else {
            $('select[id=v2-showEssayData-grade-select] option:eq(0)').attr('selected', 'selected');
            $('select[id=v2-showEssayData-section-select] option:eq(0)').attr('selected', 'selected');
        }
    }
}


$('#v2-showEssayData-grade-select').on("change", function () {
    if ($(this).val() == "") {
        $('#v2-showEssayData-section-select').html("<option value=''>select</option>");
    } else {
        getTeacherEssayAllotmentSection($('#v2-showEssayData-grade-select').val(), 0);
    }
});
/**
	 Essay will start from here
	 * */
englishInterface.directive('modal', function () {
    return {
        template: '<div class="modal fade">' +
                '<div class="modal-dialog">' +
                '<div class="modal-content">' +
                '<div class="modal-header">' +
                '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' +
                '<h4 class="modal-title" visible="title" >{{ title }}</h4>' +
                '</div>' +
                '<div class="modal-body">' +
                '<div class="v2-modal-table-wrapper" >' +
                '<div  class="v2-no-essay-data-found" ng-show="noStudentData">No students found.</div>' +
                '<div  class="v2-no-essay-data-found" ng-show="noCurrentData">No submissions found.</div>' +
                '<div ng-show="showCurrentData">' +
                '<div ng-if="modalType==\'otherTopic\'">' +
                '<table class="table-responsive table-hover">' +
                '<thead><tr><th style="width: 50px;text-align: center;">S.No.</th><th>Essay Name</th><th>Submissions</th></tr></thead>' +
                '<tbody>' +
                '<tr ng-repeat="studentTopic in studentTopicsArray">' +
                '<td style="width: 50px;text-align: center;">{{ $index + 1 }}</td>' +
                '<td><a href="javascript:void(0);" ng-click="openModalEssay(\'currentTopic\',studentTopic.topicID,studentTopic.essayTitle)">{{ studentTopic.essayTitle }}</a></td>' +
                '<td>{{ studentTopic.submissions }}</td>' +
                '</tr>' +
                '</tbody>' +
                '</table>' +
                '</div>' +
                '<div ng-if="modalType == \'currentTopic\'">' +
                '<table class="table-responsive table-hover">' +
                '<thead><tr><th style="width: 50px;text-align: center;">S.No.</th><th>Student Name</th><th>Submitted On</th><th>Status</th></tr></thead>' +
                '<tbody>' +
                '<tr ng-repeat="currentTopic in currentTopicArray">' +
                '<td style="width: 50px;text-align: center;">{{ $index + 1 }}</td>' +
                '<td ng-if="currentTopic.status == \'Reviewed\'"><a href="javascript:void(0);" ng-click="getEssay(currentTopic.scoreID,this,1,currentTopic.topicID)" >{{ currentTopic.childName }}</a></td>' +
                '<td ng-if="currentTopic.status == \'Pending\'"><a href="javascript:void(0);" ng-click="getEssay(currentTopic.scoreID,this,0,currentTopic.topicID)">{{ currentTopic.childName }}</a></td>' +
                '<td>{{ currentTopic.submittedOn }}</td>' +
                '<td  ng-class="currentTopic.status==\'Pending\'? \'v2-essayPending\' :\'v2-essayReviewed\'" >{{ currentTopic.status }}</td>' +
                '</tr>' +
                '</tbody>' +
                '</table>' +
                '</div>' +
                '<div ng-if="modalType == \'submitted\'">' +
                '<table class="table-responsive table-hover">' +
                '<thead><tr><th style="width: 50px;text-align: center;">S.No.</th><th>Student Name</th><th>Status</th></tr></thead>' +
                '<tbody>' +
                '<tr ng-repeat="currentTopic in currentTopicArray">' +
                '<td style="width: 50px;text-align: center;">{{ $index + 1 }}</td>' +
                '<td ng-if="currentTopic.status == \'2\'"><a href="javascript:void(0);" ng-click="getEssay(currentTopic.scoreID,this,1,currentTopic.topicID)" data-title="Reviewed">{{ currentTopic.childName }}</a></td>' +
                '<td ng-if="currentTopic.status == \'1\'"><a href="javascript:void(0);" ng-click="getEssay(currentTopic.scoreID,this,0,currentTopic.topicID)" data-title="Pending">{{ currentTopic.childName }}</a></td>' +
                '<td ng-if="currentTopic.status == \'0\'">{{ currentTopic.childName }}</td>' +
                '<td ng-class="currentTopic.status==\'0\'? \'v2-essayPending\' :\'v2-essayReviewed\'">{{ currentTopic.submitted }}</td>' +
                '</tr>' +
                '</tbody>' +
                '</table>' +
                '</div>' +
                '<div ng-if="modalType == \'pending\'">' +
                '<table class="table-responsive table-hover">' +
                '<thead><tr><th style="width: 50px;text-align: center;">S.No.</th><th>Student Name</th><th>Submitted On</th><th>Status</th></tr></thead>' +
                '<tbody>' +
                '<tr ng-repeat="currentTopic in currentTopicArray">' +
                '<td style="width: 50px;text-align: center;">{{ $index + 1 }}</td>' +
                '<td><a href="javascript:void(0);" ng-click="getEssay(currentTopic.scoreID,this,0,currentTopic.topicID)">{{ currentTopic.childName }}</a></td>' +
                '<td>{{ currentTopic.submittedOn }}</td>' +
                '<td   ng-class="currentTopic.status==\'Pending\'? \'v2-essayPending\' :\'v2-essayReviewed\'"  >{{ currentTopic.status }}</td>' +
                '</tr>' +
                '</tbody>' +
                '</table>' +
                '</div>' +
                '</div>' +
                '</div></div>' +
                '</div>' +
                '</div>' +
                '</div>',
        restrict: 'E',
        transclude: true,
        replace: true,
        scope: true,
        link: function postLink(scope, element, attrs) {
            scope.$watch(attrs.visible, function (value) {
                if (value == true) {
                    scope.updateHeightModalBox();
                    $(window).resize(function () {
                        scope.updateHeightModalBox();
                    });
                    $(element).modal('show');
                } else {
                    $(element).modal('hide');
                }
            });

            $(element).on('shown.bs.modal', function () {
                scope.$apply(function () {
                    scope.$parent[attrs.visible] = true;
                });
            });

            $(element).on('hidden.bs.modal', function () {
                scope.$apply(function () {
                    scope.$parent[attrs.visible] = false;
                });
            });
        }
    };
});

/**
	 * Angular Controller for essay allotment
	 * */
englishInterface.controller('EssayAllotmentCtrl', function ($scope, filterFilter, $http) {
    $scope.showEssayAssignment = function () {
            $('#v2-topicStartDate').datepicker({dateFormat: 'dd-mm-yy', minDate: 0, maxDate:'+6M',changeYear: true, yearRange: ":+1"});
        $('#v2-topicEndDate').datepicker({dateFormat: 'dd-mm-yy', minDate: 0, maxDate:'+6M',changeYear: true, yearRange: ":+1"});
        $scope.getActiveTopicDetails();
        var grade = $('#v2-showEssayData-grade-select').val();
        var section = $('#v2-showEssayData-section-select').val();
        
        var message = "";
        if (grade == "")
            message += "Please select a grade.<br>";
        if (section == "")
            message += "Please select a section.<br>";

        if (message != "") {
            Helpers.prompt(message);
            return false;
        }
        //global
        $scope.pendingArray = [];
        $scope.currentlyArray = [];
        $scope.limitrecentlyPagignation = 5;
        $scope.limitpendingPagignation = 3;
        $scope.showCurrentTopicDataLoding=true;
        //end
        $http({
            method: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "essayAllotment/getTeacherEssayAllotment",
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function () {
                var stringParam = '';
                stringParam = 'childclass=' + grade + '&childsection=' + section;
                return stringParam;
            },
            data: $scope.response
        }).success(function (data) {
            $scope.pendingArray = data.result_data.pendingEssays;
            $scope.currentlyArray = data.result_data.topicsAssignByTeacher;
            $scope.totalPendingRecord = $scope.pendingArray.length;
            $scope.totalRecentlyRecord = $scope.currentlyArray.length;
            
            if ($scope.totalPendingRecord <= $scope.limitpendingPagignation) {
                $scope.limitpendingPagignation = $scope.totalPendingRecord;
            }
            if ($scope.totalRecentlyRecord <= $scope.limitrecentlyPagignation) {
                $scope.limitrecentlyPagignation = $scope.totalRecentlyRecord;
            }
            $scope.recentlyPagignation = 'Showing ' + $scope.limitrecentlyPagignation + ' of ' + $scope.totalRecentlyRecord;
            if ($scope.limitrecentlyPagignation === $scope.totalRecentlyRecord) {
                $scope.recentlyPagignation = false;
            }
            $scope.pendingPagignation = 'Showing ' + $scope.limitpendingPagignation + ' of ' + $scope.totalPendingRecord;
            if ($scope.limitpendingPagignation === $scope.totalPendingRecord) {
                $scope.pendingPagignation = false;
            }
            $scope.showCurrentTopicDataLoding=false;
        });
    };
/**
	 * function description : This function start the whole process when user come to essay review page..
	 * param1   class
         * param2   section
	 * @return  render all recently essay, pending essay and todays active topic.
	 * 
	 * */
    $scope.getActiveTopicDetails = function () {
        $scope.topicActive = 0;
        var grade = $('#v2-showEssayData-grade-select').val();
        var section = $('#v2-showEssayData-section-select').val();
        return $http({
            method: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "essayAllotment/getCurrentlyActiveTopic",
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function () {
                var stringParam = '';
                stringParam = 'childclass=' + grade + '&childsection=' + section;
                return stringParam;
            },
            data: $scope.response
        }).success(function (data) {
            var result_data = angular.fromJson(data.result_data);
            if (typeof result_data[0] != 'undefined') {
                $scope.topicActive = result_data[0].essayID;
                $scope.topicName = result_data[0].essayTitle;
                $scope.topicEndDate = result_data[0].deactivationDate;
            } else {
                $scope.topicActive = 0;
            }
        });
    };
/**
	 * function description : this function will get all the essay submission on requested topic..
	 * param1   essay ID
         * param2   grade
         * param3   section
	 * @return  return in json object student name, submission date and status of essay
	 * 
	 * */
    $scope.fetchSubmissionByTopic = function (essayId) {
        var grade = $('#v2-showEssayData-grade-select').val();
        var section = $('#v2-showEssayData-section-select').val();
        return $http({
            method: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "essayAllotment/getSubmissionByTopic",
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function () {
                var stringParam = '';
                stringParam = 'essayID=' + essayId + '&childclass=' + grade + '&childsection=' + section;
                return stringParam;
            },
            data: $scope.response
        }).success(function (data) {
            $scope.result_data = data.result_data;
        });
    };
    /**
	 * function description : this function will get all the essay submission on requested topic with all student name..
	 * param1   essay ID
         * param2   grade
         * param3   section
	 * @return  return in json object student name, submission date and status of essay
	 * 
	 * */
    $scope.fetchSubmissionByTopicStudent = function (essayId) {
        var grade = $('#v2-showEssayData-grade-select').val();
        var section = $('#v2-showEssayData-section-select').val();
        return $http({
            method: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "essayAllotment/getSubmissionByStudent",
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function () {
                var stringParam = '';
                stringParam = 'essayID=' + essayId + '&childclass=' + grade + '&childsection=' + section;
                return stringParam;
            },
            data: $scope.response
        }).success(function (data) {
                $scope.result_data = data.result_data;
        });
    };
/**
	 * function description : this function will get all the essay chosen by student.
	 * param1   grade ID
         * param2   section
	 * @return  return in json object essay id, essay name and total submission
	 * 
	 * */
    $scope.fetchEssayChosenByStudent = function () {
        var grade = $('#v2-showEssayData-grade-select').val();
        var section = $('#v2-showEssayData-section-select').val();
        return $http({
            method: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "essayAllotment/getEssayChosenByStudent",
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function () {
                var stringParam = '';
                stringParam = 'childclass=' + grade + '&childsection=' + section;
                return stringParam;
            },
            data: $scope.response
        }).success(function (data) {
            $scope.result_data = data.result_data;
        });
    };

    //default value
    $scope.showModal = false;
    $scope.modalType = "";
    $scope.topicActive = 0;
    
    /**
	 * function description : this function will open the modal box and adjust modal height based on screen height.
	 * param1   essay type
         * param2   essayID
         * param2   title of modal box header
	 * @return  return in json object student name, submission date and status of essay
	 * 
	 * */
    
    $scope.openModalEssay = function (typeOfEssay, essayId, title) {
        if (typeof (essayId) === 'undefined')
            essayId = '';
        if (typeof (title) === 'undefined')
            title = false;
        //end
        $scope.currentTopicArray = "";
        $scope.noCurrentData = 0;
        $scope.noStudentData=0;
        $scope.showCurrentData = 0;
        $scope.showCurrentTopicDataLoding = true;
        $scope.modalType = typeOfEssay;
        if (typeOfEssay === "otherTopic") {
            $scope.title = 'Topics chosen by students';
            $scope.fetchEssayChosenByStudent().then(function () {
                $scope.studentTopicsArray = angular.fromJson($scope.result_data);
                if ($scope.studentTopicsArray.length === 0) {
                    $scope.noCurrentData = true;
                    $scope.showCurrentTopicDataLoding = false;
                } else {
                    $scope.showCurrentTopicDataLoding = false;
                    $scope.showCurrentData = true;
                }
            });
        } else if (typeOfEssay === "currentTopic") {
            $scope.title = 'Topic: ' + title;
            $scope.fetchSubmissionByTopic(essayId).then(function () {
                $scope.currentTopicArray = angular.fromJson($scope.result_data);
                if ($scope.currentTopicArray.length === 0) {
                    $scope.noCurrentData = true;
                    $scope.showCurrentTopicDataLoding = false;
                } else {
                    $scope.showCurrentTopicDataLoding = false;
                    $scope.showCurrentData = true;
                }
            });
        } else if (typeOfEssay === "submitted") {
            $scope.title = 'Topic: ' + title;
            $scope.fetchSubmissionByTopicStudent(essayId).then(function () {
                $scope.currentTopicArray = angular.fromJson($scope.result_data);
                if ($scope.currentTopicArray.hasOwnProperty('noUserFound')) {
                    $scope.noStudentData = true;
                    $scope.showCurrentTopicDataLoding = false;
                } else {
                    $scope.showCurrentTopicDataLoding = false;
                    $scope.showCurrentData = true;
                }
            });

        } else if (typeOfEssay === "pending") {
            $scope.title = 'Topic: ' + title;
            $scope.fetchSubmissionByTopic(essayId).then(function () {
                $scope.currentTopicArray = angular.fromJson($scope.result_data);
                $scope.currentTopicArray = filterFilter($scope.currentTopicArray, {'status': 'Pending'});
                if ($scope.currentTopicArray.length === 0) {
                    $scope.noCurrentData = true;
                    $scope.showCurrentTopicDataLoding = false;
                } else {
                    $scope.showCurrentTopicDataLoding = false;
                    $scope.showCurrentData = true;
                }
            });
        } else {
            $scope.title = 'Topic: ' + title;
        }
        $scope.showModal = 1;
        $scope.essayId = essayId;
    };
    /**
	 * function description : this function will open the div form where the teacher can insert new essay topic.
	 * @return ;
	 * 
	 * */
    $scope.showHideActiveNew = function () {
        $('#v2-essayAssignment').slideDown();
        $scope.updateDateRange();
        $('#v2-active-new').css('visibility', 'hidden');
    };
    /**
	 * function description : this function will hide the div form where the teacher can insert new essay topic.
	 * @return ;
	 * 
	 * */
    $scope.hideHideActiveNew = function () {
        $('#v2-essayAssignment').slideUp();
        $scope.updateDateRange();
        $('#v2-active-new').css('visibility', 'visible');
    };
  /**
	 * function description : this function will insert new essay topic. and before that check whether any other essay is running between requested date or not. if not then simple insert. if running then prompt user with already exist essay.
	 * param1   topic name
         * param2   start date
         * param3   end date
	 * @return  return in json object. with activated or not.
	 * 
	 * */
    $scope.activateNewEssayTopic = function () {
        var topicName = $.trim($('#v2-topicName').val());
        var message = "";
        if ($('#v2-showEssayData-grade-select').val() == "")
            message += "Please select a grade.<br>";
        if ($('#v2-showEssayData-section-select').val() == "")
            message += "Please select a section.<br>";
        if ($('#v2-topicStartDate').val() == "")
            message+="Please select a Start Date.<br>";
        if ($('#v2-topicEndDate').val() == "")
            message+="Please select a End Date.<br>";
        if (topicName === "") {
           message+="Topic name is required.";
        }
        //  check end date > start date
        var startDate = new Date($("#v2-topicStartDate").datepicker('getDate'));
        var endDate = new Date($("#v2-topicEndDate").datepicker('getDate'));
        if (endDate < startDate)
            message += "End Date must be greater than Start Date.";

        if (message != "") {
           Helpers.prompt(message);
           return false;
        }
        $scope.showCurrentTopicDataLoding = true;
        
        $scope.activateNewEssayTopicAjax().then(function () {
            var activateNewEssayResponse = angular.fromJson($scope.activateNewEssayResponse);
            if (activateNewEssayResponse.hasOwnProperty('alreadyExists') === true) {
                var message = "A topic is already active within this date range. Would you like to deactivate it? Press 'Deactivate Existing' to activate a new topic, or press 'Cancel' to continue with the existing topic.";
                Helpers.prompt({
                    text: message,
                    buttons: {
                        'CANCEL': function () {},
                        'DEACTIVATE EXISTING': function () {
                            $scope.deactivateExistingEssayActiveNewEssay();
                        }
                    },
                    noClose: true,
                });
                $scope.showCurrentTopicDataLoding = false;
            }else{
                $scope.showCurrentTopicDataLoding = false;
            }
        });
    };
    /**
	 * function description : this function will convert field into query string for submission.
	 * param1   grade
         * param2   section
         * param3   topic name
         * param3   topic name
	 * @return  return in json object. with activated or not.
	 * 
	 * */
    $scope.getEssayStringParam=function(){
        var grade = $('#v2-showEssayData-grade-select').val();
        var section = $('#v2-showEssayData-section-select').val();
        var topicName = $('#v2-topicName').val();
        var topicStartDate = $('#v2-topicStartDate').val();
        var topicEndDate = $('#v2-topicEndDate').val();
        var stringParam = '';
        var stringParam = 'startDate=' + topicStartDate + '&endDate=' + topicEndDate + '&childclass=' + grade + '&childsection=' + section + '&essayName=' + topicName;
        return stringParam;
    };
    /**
	 * function description : This function will deactivate existing essay and simply insert new essay topics.
	 * param1   topic name
         * param2   topic end name
	 * @return  return in json object. with activated or not.
	 * 
	 * */
    $scope.deactivateExistingEssayActiveNewEssay = function () {
        $scope.showCurrentTopicDataLoding = true;
        var topicName = $('#v2-topicName').val();
        var topicEndDate = $('#v2-topicEndDate').val();
        //teacherEssayActivation
       return $http({
            method: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "essayAllotment/deactivateExistingEssayActiveNewEssay",
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function () {
               return $scope.getEssayStringParam();
            },
            data: $scope.response
        }).success(function (data) {
            $scope.activateNewEssayResponse = data.result_data;
            var responseFromServer = angular.fromJson(data.result_data);
            if (responseFromServer.hasOwnProperty('activated') === true) {
                setTimeout(function () {
                    var message = "<strong>Topic Activated!</strong><br/>'<em>" + topicName + "</em>' has been activated till " + topicEndDate + "!";
                    Helpers.prompt(message);
                    $('form[name="v2-activateNewEssayTopic"]')[0].reset();
                    $scope.updateDateRange();
                    $scope.showEssayAssignment();
                    $scope.hideHideActiveNew();
                }, 1000);
            }
            $scope.showCurrentTopicDataLoding = false;
        });
    };
    /**
	 * function description : this function will insert new essay topic.
	 * param1   topic name
         * param2   end date
	 * @return  return in json object. with activated or not.
	 * 
	 * */
    $scope.activateNewEssayTopicAjax = function () {
        $scope.showCurrentTopicDataLoding = true;
        var topicName = $('#v2-topicName').val();
        var topicEndDate = $('#v2-topicEndDate').val();
        //teacherEssayActivation
       return $http({
            method: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "essayAllotment/newEssayActivationByTeacher",
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function () {
                 return $scope.getEssayStringParam();
            },
            data: $scope.response
        }).success(function (data) {
            $scope.activateNewEssayResponse = data.result_data;
            var responseFromServer = angular.fromJson(data.result_data);
            if (responseFromServer.hasOwnProperty('activated') === true) {
                setTimeout(function () {
                    var message = "<strong>Topic Activated!</strong><br/>'<em>" + topicName + "</em>' has been activated till " + topicEndDate + "!";
                    Helpers.prompt(message);
                    $('form[name="v2-activateNewEssayTopic"]')[0].reset();
                    $scope.updateDateRange();
                    $scope.showEssayAssignment();
                    $scope.hideHideActiveNew();
                }, 1000);
            }
            $scope.showCurrentTopicDataLoding = false;
        });
    };
    /**
	 * function description : this function will deactivate currenlty active topic on click on deactive button.
	 * @return  return in json object. with deactivated or not.
	 * 
	 * */
    $scope.essayActiveTopicDeactivate = function () {
        $scope.showCurrentTopicDataLoding = true;
        deactiveCurrentTopic = $scope.topicActive;
        $http({
            method: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "essayAllotment/deactivateExistingEssay",
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            transformRequest: function () {
                var stringParam = '';
                stringParam = 'essayID=' + deactiveCurrentTopic;
                return stringParam;
            },
            data: $scope.response
        }).success(function (data) {
                Helpers.prompt("Topic '<em>" + $scope.topicName + "</em>' has been deactivated.");
                $scope.topicActive = 0;
                $scope.showEssayAssignment();
        });
        return true;
    };
    /**
	 * function description : this function will update end date on the basis of duration dropdown.
	 * param1   duration
         * param2   start date
         * param3   end Date
	 * @return  return;
	 * 
	 * */
    $scope.updateDateRange = function () {
        var duration = parseInt($('#v2-essayDuration option:selected').val(), 10);
        $scope.durationSelected = duration;
        var startDate = $("#v2-topicStartDate").val();
        var endDate = $("#v2-topicEndDate").val();
        //checking if enddate or start date is empty then we will use selected duration.
        if (startDate === "" || endDate === "") {
            duration = parseInt($('#v2-essayDuration option:selected').val(), 10);
        }
        //falback. if we didnt get value from dom then we will use defualt value.
        if (isNaN(duration)) {
            $scope.durationSelected = 15;
            duration = 15;
        }
        var endDate = new Date();
        endDate.setDate(endDate.getDate() + duration);
        var endDate = $.datepicker.formatDate('dd-mm-yy', endDate);
        var startDate = new Date();
        startDate.setDate(startDate.getDate());
        var startDate = $.datepicker.formatDate('dd-mm-yy', startDate);
        $("#v2-topicStartDate").val(startDate);
        $("#v2-topicEndDate").val(endDate);
    };
    /**
	 * function description : this function will show pagignation for pending.
	 * @return ;
	 * 
	 * */
    $scope.ShowAllPendingPagignation = function () {
        if ($scope.limitpendingPagignation === 3) {
            $scope.pendingPagignation = 'Showing ' + $scope.totalPendingRecord + ' of ' + $scope.totalPendingRecord;
            $scope.limitpendingPagignation = 1000;
        } else {
            $scope.limitpendingPagignation = 3;
            $scope.pendingPagignation = 'Showing ' + $scope.limitpendingPagignation + ' of ' + $scope.totalPendingRecord;
        }
    };
    /**
	 * function description : this function will show pagignation for recently created essay.
	 * @return ;
	 * 
	 * */
    $scope.ShowAllRecentlyPagignation = function () {
        if ($scope.limitrecentlyPagignation === 5) {
            $scope.recentlyPagignation = 'Showing ' + $scope.totalRecentlyRecord + ' of ' + $scope.totalRecentlyRecord;
            $scope.limitrecentlyPagignation = 1000;
        } else {
            $scope.limitrecentlyPagignation = 5;
            $scope.recentlyPagignation = 'Showing ' + $scope.limitrecentlyPagignation + ' of ' + $scope.totalRecentlyRecord;
        }
    };
    //end
    /**
	 * function description : this function is tunnel which transfer angular controller's function to jquery's function.
	 * @return ;
	 * 
	 * */
    $scope.getEssay = function (essayScoreID, CurrObj, mode, topicID) {
        $("#essayModalBox .close").click();
        getEssay(essayScoreID, CurrObj, mode, topicID);
    };
    //end
    /**
	 * function description : this function will update height of modal box to the maximum height -200 of window height.
	 * @return ;
	 * 
	 * */
    $scope.updateHeightModalBox = function () {
        $('.v2-modal-table-wrapper').css('max-height', $(window).height() - 200);
    };
    //end
});


function getEssay(essayScoreID, CurrObj, mode, topicID) {
    currentQuestion.qID = topicID;
    eScoreID = essayScoreID;
    if (mode)
        eMode = true;
    else
        eMode = false;

    angular.element(document.getElementById('essay_evaluation')).scope().hideRubric();
    angular.element(document.getElementById('essay_evaluation')).scope().getEssayDetails();
    showEssayEvaluation();
    $(this).siblings().removeClass('active');
    $(this).addClass('active');
    tmpCurDiv = $('#reviewedEssays .active');
    var curObj = $(CurrObj).closest('div').id;

    $(".rubric-heading-btn").css('left', '83%');
    $(".auto-comment-btn").css('left', '83%');
}



function getEssayCallBack(response, extraParams) {

    var essayScoreID = extraParams[0];
    var topicId = extraParams[1];
    var tmpCurDiv = extraParams[2];
    var curObj = extraParams[3];
    var mode = extraParams[4];

    var questionObject = {
        qID: essayScoreID,
        qType: 'essay',
        info: {
            userResponse: response["userResponse"],
            submitted: curObj == "completeEssays" ? true : false
        },
        curObj: curObj,
        mode: mode
    };
    $('.appContainers').hide();
    $('.the_classroom').show();
    setQuestion(questionObject, "report");
}

function showEvaluateEssayContainer()
{
    $(".moduleContainer").hide();
    $(".evaluateEssayContainer").show();
}

/*VIEW TEACHERS*/
var dataTeacher = '';
$("#cancel_viewteacher_changes").on('click', function () {
    viewTeachersMyStudentActivatePage();

    $(".isEditable").val(0);
    $("#teacherBtns").hide();
    //getTeachersDataAjax(dataTeacher)
    //if(dataTeacher)
});

function viewTeachersMyStudentActivatePage()
{
    //checking if user is super admin or not
    if (sessionData.category == 'School Admin' && sessionData.subcategory == 'All') {
        $('#view_teachers_li').addClass('opacity-0');
        var message = "You don't have necessary rights to perform this action.";
        Helpers.prompt(message);
        return false;
    }
    //end
    $("#view_students").hide();
    $("#activate_topic").hide();
    //$("#view_teachers").show();


    $("#student_table_activate").hide();
    // Flick of active class issue on My Students page fixed.
    $(".active").removeClass('active');
    $("#view_teachers_li").attr('class', 'active');
    // ---- //

    /*$("#student_table_view").hide();
     $("#student_data_note").hide();
     $("#student_dataparentemail_note").hide();*/

    //$("#my_teacher_view_tbody").empty();
    var message = "";

    if (message == "") {
        var userIDArr = [];
        //  get report data
        $.ajax({
            type: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "teachermystudents/showEditTeacherDetails",
            dataType: 'json',
            //data : {'class' : $('#th_my_student_grade_select').val(),'section' : $('#th_my_student_section_select').val(), 'childName': $("#ChildNameInput").val()},
        }).done(function (data) {
            dataTeacher = data;
            Helpers.ajax_response(getTeachersDataAjax, data, [userIDArr]);
        });
    } else {
        Helpers.prompt(message);
    }
}
function getTeachersDataAjax(data, extraParams)
{
    var userIDArr = extraParams[0];

    //$('#teacher_my_students div > .none').show();
    $("#my_teacher_view_tbody").html('');
    $('.teachers_table').show();
    $("#teacher_table_view").show();
    $("#teacher_data_note").show();
    //$("#teacherBtns").show();
    $("#student_table_view").hide();
    $("#student_data_note").hide();
    $("#student_dataparentemail_note").hide();

    if (data.teacherData.length == 0)
    {
        /*$("#my_student_no_data").show();
         $("#my_students_view").hide();
         $("#student_data_note").hide();
         $("#student_dataparentemail_note").hide();*/
    } else
    {
        /*$("#my_student_no_data").hide();
         $("#student_data_note").show();
         $("#student_dataparentemail_note").show()*/
        for (var i = 0; i < data.teacherData.length; i++) {
            userIDArr.push(data.teacherData[i].userID);

            var sr_no = i + 1;

            if (data.teacherData[i].combinedClass != '' && data.teacherData[i].combinedClass != null)
            {
                var primaryClass = data.teacherData[i].combinedClass.split(',');
                var combinedClass = data.teacherData[i].combinedClass;
            } else
            {
                var primaryClass = '';
                var combinedClass = '';
            }

            if (data.teacherData[i].actualPrimaryClass != '' && data.teacherData[i].actualPrimaryClass != null)
                var primaryClassActual = data.teacherData[i].actualPrimaryClass;
            else
            {
                //var primaryClassActual = primaryClass[0];
                var primaryClassActual = '';
            }

            tr = $('<tr id="tr_' + data.teacherData[i].userID + '"/>');
            tr.append("<td style='text-align:center'>" + sr_no + "</td><input size='15' type='hidden' name='isEditable" + data.teacherData[i].userID + "' user_id='" + data.teacherData[i].userID + "' id='isEditable_" + data.teacherData[i].userID + "' class='isEditable' value='0' />");
            tr.append("<td id='field" + data.teacherData[i].userID + 1 + "'>" + data.teacherData[i].userName + "</td><input size='15' type='hidden' name='userNameHdn" + data.teacherData[i].userID + "' id='userNameHdn" + data.teacherData[i].userID + "' value='" + data.teacherData[i].userName + "' />");
            tr.append("<td id='field" + data.teacherData[i].userID + 2 + "'>" + data.teacherData[i].childName + "</td><input class='form-control' size='15' type='hidden' name='childNameHdn" + data.teacherData[i].userID + "' id='childNameHdn" + data.teacherData[i].userID + "' value='" + data.teacherData[i].childName + "'>");
            tr.append("<td id='field" + data.teacherData[i].userID + 3 + "'>" + combinedClass + "</td><input type='hidden' id='allClasses' value='" + data.teacherData[i].allClasses + "'/><input type='hidden' id='classAssignedOld_" + data.teacherData[i].userID + "' value='" + combinedClass + "'/>");

            //tr.append("<td id='field"+data.teacherData[i].userID+4 +"'>" + data.teacherData[i].primaryClass + "<input type='hidden' id='primaryClass_"+data.teacherData[i].userID+"' value='"+data.teacherData[i].primaryClass+"'/></td>");

            var primaryClassArr = primaryClassActual.replace(/ /g, '').trim().split(',');

            var td = $("<td class='primaryClass' id='field" + data.teacherData[i].userID + 4 + "'><input type='hidden' id='primaryClass_" + data.teacherData[i].userID + "' value='" + primaryClassActual + "'/><input type='hidden' class='primaryClassChange' id='primaryClassChange_" + data.teacherData[i].userID + "' value='" + primaryClassActual + "'/><input type='hidden' id='primaryClassOld_" + data.teacherData[i].userID + "' value='" + primaryClassActual + "'/></td>");

            var html = [];
            for (var j = 0; j < primaryClassArr.length; j++)
            {
                html.push("<span class='token-label'>" + primaryClassArr[j] + "</span>");

            }
            td.append(html.join());
            tr.append(td);

            //tr.append("<td class='primaryClass' id='field"+data.teacherData[i].userID+4 +"'><span class='token-label'>" + primaryClassActual + "</span><input type='hidden' id='primaryClass_"+data.teacherData[i].userID+"' value='"+primaryClassActual+"'/><input type='hidden' class='primaryClassChange' id='primaryClassChange_"+data.teacherData[i].userID+"' value='"+primaryClassActual+"'/><input type='hidden' id='primaryClassOld_"+data.teacherData[i].userID+"' value='"+primaryClassActual+"'/></td>");

            if (data.teacherData[i].category == 'TEACHER')
            {
                tr.append("<td><input class='form-control' type='button'  id='edit_" + data.teacherData[i].userID + "' onclick='EditTheFieldsTeacher(" + data.teacherData[i].userID + ", " + sr_no + ")' id='' value='Edit'/><input class='form-control' type='button' style='display:none'  id='cancel" + data.teacherData[i].userID + "' onclick='CancelTheFieldsTeacher(" + data.teacherData[i].userID + "," + sr_no + ")' id='' value='Cancel'/><input type='button' class='form-control' style='display:none'  id='save_" + data.teacherData[i].userID + "' onclick='SaveTheDataTeacher(this.id, " + sr_no + "," + data.teacherData[i].userID + ")' value='Save'/></td>");
            } else if (data.teacherData[i].category == 'ADMIN' || data.teacherData[i].category == 'School Admin')
            {
                tr.append("<td><input class='form-control' type='button' disabled='disabled'  id='edit_" + data.teacherData[i].userID + "' onclick='EditTheFieldsTeacher(" + data.teacherData[i].userID + ", " + sr_no + ")' id='' value='Edit'/><input class='form-control' type='button' style='display:none'  id='cancel" + data.teacherData[i].userID + "' onclick='CancelTheFieldsTeacher(" + data.teacherData[i].userID + "," + sr_no + ")' id='' value='Cancel'/><input type='button' class='form-control' style='display:none'  id='save_" + data.teacherData[i].userID + "' onclick='SaveTheDataTeacher(this.id, " + sr_no + "," + data.teacherData[i].userID + ")' value='Save'/></td>");
            }
            tr.append("<input type='hidden' id='flagCheck" + data.teacherData[i].userID + "' name='flagCheck" + data.teacherData[i].userID + "' value='0'>");
            $('#my_teacher_view_tbody').append(tr);
            //$("#my_teacher_view_tbody").append('<tr style="display:none;" id="namechangereason'+data.teacherData[i].userID+'"><td  colspan="9"><label>Reason for change in id/name</label>&nbsp;&nbsp;&nbsp;<input  class="form-control" type="text" name="changereason'+data.teacherData[i].userID+'" id="changereason'+data.teacherData[i].userID+'" OnKeyPress="checkenterkey(event)"/></td></tr>');
        }
    }
    /*var userIDString = userIDArr.join(',');
     $("#userIDString").val(userIDString);*/
}
function EditTheFieldsTeacher(userID, sr_no)
{

    $("#teacherBtns").show();
    userName = $("#field" + userID + 1).html();
    childName = $("#field" + userID + 2).html();
    classAssigned = $("#field" + userID + 3).html();
    primaryClass = $("#field" + userID + 4).html();
    var primaryClassValue = $("#primaryClass_" + userID).val();
    var primaryClassValueOld = $("#primaryClassOld_" + userID).val();
    var classAssignedOld = $("#classAssignedOld_" + userID).val();

    var allClasses = $("#allClasses").val();

    var classAssigned = classAssigned.replace(/ /g, '').trim();

    /*document.getElementById("edit_"+userID).style.display = "none";
     document.getElementById("cancel"+userID).style.display = "block";
     document.getElementById("save_"+userID).style.display = "block";*/

    $("#edit_" + userID).attr('disabled', 'disabled');

    $("#isEditable_" + userID).val(1);


    tempHTML = "<input class='form-control applyTokenField' size='15' onblur='checkChangeDataTeacher(" + userID + ")' type='text' name='classAssigned" + userID + "'  id='classAssigned" + userID + "'  maxlength='20'>";

    tempHTML = tempHTML + "<input class='form-control' size='15' type='hidden' name='classAssignedHdn" + userID + "' id='classAssignedHdn" + userID + "' value='" + classAssigned + "'><input type='hidden' id='classAssignedOld_" + userID + "' name='classAssignedOld_" + userID + "' value='" + classAssignedOld + "' />";

    document.getElementById("field" + userID + "3").innerHTML = tempHTML;
    document.getElementById("classAssigned" + userID).value = classAssigned;

    var classAssignedDropDown = classAssigned.split(',');
    var allClassesarr = allClasses.split(',');

    if (classAssignedDropDown.length > 0)
    {



        tempHTML = "<input class='form-control' size='15' type='text' onblur='checkPrimaryClassWithinClassAssigned(" + userID + ")' name='primaryClassTxt" + userID + "'  id='primaryClassTxt" + userID + "'  maxlength='20'>";
        tempHTML += "<input type='hidden' id='primaryClass_" + userID + "' name='primaryClass_" + userID + "' value='" + primaryClassValue + "' /><input type='hidden' class='primaryClassChange' id='primaryClassChange_" + userID + "' name='primaryClassChange_" + userID + "' value='" + primaryClassValue + "' /><input type='hidden' id='primaryClassOld_" + userID + "' name='primaryClass_" + userID + "' value='" + primaryClassValueOld + "' />"
        document.getElementById("field" + userID + "4").innerHTML = tempHTML;
        document.getElementById("primaryClassTxt" + userID).value = primaryClassValue;
    }

    document.getElementById("flagCheck" + userID).value = 1;

    var allClassesValue = $("#allClasses").val().split(',');
    var classAssg = $('#classAssignedHdn' + userID).val().replace(/ /g, '').trim().split(',');

    for (var i = 0; i < classAssg.length; i++)
    {
        var removeItem = classAssg[i];
        allClassesValue = jQuery.grep(allClassesValue, function (value) {
            return value != removeItem;
        });
    }


    $('.applyTokenField').tokenfield({
        autocomplete: {
            source: allClassesValue,
            delay: 100
        },
        createTokensOnBlur: true,
        showAutocompleteOnFocus: true
    }).on('tokenfield:createdtoken', function (event) {

        var exists = true;
        var tokenClassValues = event.target.value + ',' + event.attrs.value;

        var array = allClassesValue;
        var index = array.indexOf(event.attrs.value);
        if (index > -1)
        {
            array.splice(index, 1);
        }

        $('#classAssigned' + userID).data('bs.tokenfield').$input.autocomplete({source: array});


        var classAssgForPrim = $('#primaryClassChange_' + userID).val().replace(/ /g, '').trim().split(',');
        var allClassesValuePri = tokenClassValues.split(',');

        for (var i = 0; i < classAssgForPrim.length; i++)
        {
            var removeItem = classAssgForPrim[i];
            allClassesValuePri = jQuery.grep(allClassesValuePri, function (value) {
                return value != removeItem;
            });
        }

        $('#primaryClassTxt' + userID).data('bs.tokenfield').$input.autocomplete({source: allClassesValuePri});


        $.each($("#allClasses").val().split(','), function (index, token) {

            if (token === event.attrs.value)
            {
                exists = false;

            }

        })
        //$('#classAssigned'+userID).tokenfield('destroy');
        if (exists === true)
            return false;
        //$(this).tokenfield('setTokens',event.target.value.split(','));
    }).on('tokenfield:removedtoken', function (event) {

        var array = allClassesValue;
        array.push(event.attrs.value);

        $('#classAssigned' + userID).data('bs.tokenfield').$input.autocomplete({source: array});

        var classAssigned = $('#classAssigned' + userID).tokenfield('getTokensList').replace(/ /g, '').trim();
        $('#primaryClassTxt' + userID).data('bs.tokenfield').$input.autocomplete({source: classAssigned.split(',')});

    });

    //$('#primaryClassTxt'+userID).tokenfield('destroy');
    var classAssgForPrim = $('#primaryClass_' + userID).val().replace(/ /g, '').trim().split(',');
    var allClassesValuePri = $("#classAssigned" + userID).val().split(',');

    for (var i = 0; i < classAssgForPrim.length; i++)
    {
        var removeItem = classAssgForPrim[i];
        allClassesValuePri = jQuery.grep(allClassesValuePri, function (value) {
            return value != removeItem;
        });
    }


    $('#primaryClassTxt' + userID).tokenfield({
        autocomplete: {
            source: allClassesValuePri,
            delay: 100
        },
        createTokensOnBlur: true,
        showAutocompleteOnFocus: true
    }).on('tokenfield:createdtoken', function (event) {

        var arrayPri = allClassesValuePri;
        var index = arrayPri.indexOf(event.attrs.value);
        if (index > -1)
        {
            arrayPri.splice(index, 1);
        }
        $('#primaryClassTxt' + userID).data('bs.tokenfield').$input.autocomplete({source: arrayPri});

        var primaryClassesAssigned = $('#primaryClassTxt' + userID).tokenfield('getTokensList');
        var primaryClassesAssignedTrim = primaryClassesAssigned.replace(/ /g, '').trim();

        $("#primaryClassChange_" + userID).val('');
        $("#primaryClassChange_" + userID).val(primaryClassesAssignedTrim);

    }).on('tokenfield:removedtoken', function (event) {

        var arrayPri = allClassesValuePri;
        arrayPri.push(event.attrs.value);

        $('#primaryClassTxt' + userID).data('bs.tokenfield').$input.autocomplete({source: arrayPri});

        var primaryClassesAssigned = $('#primaryClassTxt' + userID).tokenfield('getTokensList');
        var primaryClassesAssignedTrim = primaryClassesAssigned.replace(/ /g, '').trim();

        $("#primaryClassChange_" + userID).val('');
        $("#primaryClassChange_" + userID).val(primaryClassesAssignedTrim);

    });

    $("#teacher_my_students").scroll(function () {
        $("#classAssigned" + userID + "-tokenfield").blur();
        $("#primaryClassTxt" + userID + "-tokenfield").blur();
    });

}


$("#save_viewteacher_changes").on('click', function () {

    var editPriClasArr = [];
    var editClasArr = [];
    var difference = [];
    var userDataArray = [];
    var userDataArray = [];

    $(".isEditable").each(function () {

        var isEditableValue = $(this).val();

        if (isEditableValue == 1)
        {
            var userid = $(this).attr('user_id');
            var username = $("#userNameHdn" + userid).val();
            var childname = $("#childNameHdn" + userid).val();

            /*FOR PRIMARY CLASS*/
            var primaryClassesAssigned = $('#primaryClassTxt' + userid).tokenfield('getTokensList').replace(/ /g, '').trim().split(',').filter(function (v) {
                return v !== ''
            });

            var newPrmClsAsg = primaryClassesAssigned.filter(function (itm, i, primaryClassesAssigned) {
                return i == primaryClassesAssigned.indexOf(itm);
            });

            var lengtPrimaryClassesAssigned = newPrmClsAsg.length;
            for (var i = 0; i < newPrmClsAsg.length; i++)
            {
                editPriClasArr.push(newPrmClsAsg[i]);
            }


            /*PRIMARY CLASS OLD*/
            var primaryClassOld = $("#primaryClassOld_" + userid).val().replace(/ /g, '').trim().split(',').filter(function (v) {
                return v !== ''
            });
            var lengthPrimaryClassOld = primaryClassOld.length;

            /*IF REMOVED CLASS ASSIGNED TO ANYONE ELSE*/

            if (lengtPrimaryClassesAssigned <= lengthPrimaryClassOld)
            {
                $.grep(primaryClassOld, function (el) {
                    if ($.inArray(el, newPrmClsAsg) == -1)
                        difference.push(el.replace(/ /g, '').trim());
                });
            }


            /*END*/

            /*FOR CLASS ASSIGNED*/
            var classAssignedArr = [];
            var classAssigned = $('#classAssigned' + userid).tokenfield('getTokensList').replace(/ /g, '').trim().split(',').filter(function (v) {
                return v !== ''
            });
            for (var i = 0; i < classAssigned.length; i++)
            {
                editClasArr.push(classAssigned[i]);

                var classSection = classAssigned[i];
                var classSection2 = classAssigned[i].replace(/[a-zA-Z]/g, '');
                var classSection1 = classSection.replace(/[0-9]/g, '');
                var classSection3 = classSection2.trim() + '-' + classSection1.trim();
                var classSection4 = classSection3.replace('--', '-'); //this will remove extra hyphen from string.
                classAssignedArr.push(classSection4);
            }
            /*CLASS ASSIGNED OLD*/
            var classAssignedOld = $("#classAssignedOld_" + userid).val().replace(/ /g, '').trim();

            /*END*/

            //var primaryClassPost = newPrmClsAsg.join(",");

            var dataArr = {'userID': userid, 'classAssignedArr': classAssignedArr, 'classAssigned': classAssigned, 'primaryClass': newPrmClsAsg, 'oldPrimaryClass': primaryClassOld, 'userName': username, 'childname': childname, 'classAssignedOld': classAssignedOld}
            //userDataArray[userid] = {'userID' : userid,'classAssigned' : classAssigned, 'primaryClass' : primaryClassPost, 'oldPrimaryClass' : primaryClassOld,'userName':username,'childname':childname, 'classAssignedOld' : classAssignedOld}; 
            userDataArray.push(dataArr);
        }
    });

    /*FOR CHECKING ALL THE VALIDATIONS CLASS ASSIGNED*/
    var editClasArrUnique = unique(editClasArr);
    var getCheckValueClass = validateClassAssig(editClasArrUnique);

    var editPriClasArrUnique = unique(editPriClasArr);

    /*FOR CHECKING ALL THE VALIDATIONS*/
    if (!getCheckValueClass.notFromAsgnClasses)
    {
        var getCheckValuePrimary = validatePrimaryClass(editPriClasArrUnique, editClasArrUnique, difference);

        if (getCheckValuePrimary.isPrimaryWithinDropdown) //primary class not from the dropdown
            var msg = 'Primary class should be among the class(es) assigned!';
        else
        {
            if (getCheckValuePrimary.isPrimaryRepeatCheck && !getCheckValuePrimary.isPrimaryAssiToElse)
                var msg = 'This class has already been assigned to someone else as a primary class! Please edit your selection.';
            else if (!getCheckValuePrimary.isPrimaryRepeatCheck && getCheckValuePrimary.isPrimaryAssiToElse)
                var msg = 'Assign the removed primary class to someone else, otherwise you will not be able to save the changes!';
            else if (getCheckValuePrimary.isPrimaryRepeatCheck && getCheckValuePrimary.isPrimaryAssiToElse)
                var msg = 'This class has already been assigned to someone else as a primary class! Please edit your selection.<br>Assign the removed primary class to someone else, otherwise you will not be able to save the changes!';
            else
                var msg = '';
        }

    } else
    {
        var msg = 'Class assigned should be among the dropdown only!';
    }

    if (msg != '')
        Helpers.prompt(msg);
    else
    {
        saveData(userDataArray);
    }
});

function saveData(userData)
{
    $.ajax({
        type: 'POST',
        url: Helpers.constants['CONTROLLER_PATH'] + "teachermystudents/updateTeacherDetails",
        dataType: 'json',
        data: {'userData': userData},
    }).done(function (data) {
        Helpers.ajax_response(saveTeacherDataAjax, data, []);
    });
}

function saveTeacherDataAjax(data, extraParams)
{

    viewTeachersMyStudentActivatePage();
    //console.log('m here');
    var msg = "Details updated successfully";
    Helpers.prompt(msg);
    $("#teacherBtns").hide();
    $(".isEditable").val(0);
}

function validatePrimaryClass(editedClassArr, editClasAssignedArr, difference)
{
    var isPrimaryRepeatCheck = false;
    var isPrimaryWithinDropdown = false;
    var isPrimaryAssiToElse = false;

    $(".primaryClass span").css("background-color", "").css('color', '');


    //to check if primary class within the class assigned or not
    var check = checkPrimaryClass(editClasAssignedArr, editedClassArr);
    if (!check)
        isPrimaryWithinDropdown = true;

    //to check if primary class assigned to some other row or not
    for (var i = 0; i < editedClassArr.length; i++)
    {

        var lengthOfRepetdValue = $(".primaryClass span:contains('" + editedClassArr[i] + "')").length;

        if (lengthOfRepetdValue > 1)
        {
            isPrimaryRepeatCheck = true;
            var color = getRandomColor();
            $(".primaryClass span:contains('" + editedClassArr[i] + "')").css('background', color).css('color', 'white');
        }
    }

    //to check if removed primary class assigned to someone else or not
    var checkNotAssigned = checkPrimaryClass(editedClassArr, difference);

    if (!checkNotAssigned)
        isPrimaryAssiToElse = true;

    var checkPrimaryArray = {'isPrimaryRepeatCheck': isPrimaryRepeatCheck, 'isPrimaryWithinDropdown': isPrimaryWithinDropdown, 'isPrimaryAssiToElse': isPrimaryAssiToElse};

    return checkPrimaryArray;
}

function validateClassAssig(editClasAssignedArr)
{
  //  console.log(editClasAssignedArr);
    var notFromAsgnClasses = false;

    var allClassesCheck = $("#allClasses").val().split(',');
    var checkClass = checkPrimaryClass(allClassesCheck, editClasAssignedArr);

    if (!checkClass)
        notFromAsgnClasses = true;

   // console.log(notFromAsgnClasses)
    var checkClassAssiArray = {'notFromAsgnClasses': notFromAsgnClasses};

    return checkClassAssiArray;
}

function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

/*THIS FUNCTION IS TO CHECK SECOND ARRAY PRESENT IN FIRST OR NOT*/
function checkPrimaryClass(sup, sub) {
    sup.sort();
    sub.sort();
    var i, j;
    for (i = 0, j = 0; i < sup.length && j < sub.length; ) {
        if (sup[i] < sub[j]) {
            ++i;
        } else if (sup[i] == sub[j]) {
            ++i;
            ++j;
        } else {
            // sub[j] not in sup, so sub not subbag
            return false;
        }
    }
    // make sure there are no elements left in sub
    return j == sub.length;
}

function unique(list) {
    var result = [];
    $.each(list, function (i, e) {
        if ($.inArray(e, result) == -1)
            result.push(e);
    });
    return result;
}



/*VIEW TEACHERS END*/

/*SHOW SESSION REPORT OF STUDENTS TO TEACHER*/
function sessionReport(userid, childClass) {

    var userid = userid;
    var childclass = childClass;
    var childsection = $("#" + userid).attr('class');
    var startDate = $("#teacherReportStartDate").val();
    var endDate = $("#teacherReportEndDate").val();
    var childName = $("#" + userid).attr('child');
    $("#trchildName").val(childName);
    showSessionReport(userid, startDate, endDate, childclass);
}

function setValues()
{
    $("#treportGrade").val($("#th_teacher_report_grade_select").val());
    $("#treportSection").val($("#th_teacher_report_section_select").val());
    $("#treportStartDate").val($("#teacherReportStartDate").val());
    $("#treportEndDate").val($("#teacherReportEndDate").val());
    //$("#treportRadioBtnSeletion").val($("#report_mode").val());
}

  
$(document).on("click", "#backSessionReport", function ()
{
    sessionData.currentLocation.type = 'reports';
    showTeacherReportsPage();
    showTeacherReportsOverAllPage();

    var grade = $("#treportGrade").val();
    var section = $("#treportSection").val();
    var startDate = $("#treportStartDate").val();
    var endDate = $("#treportEndDate").val();
    var btnclicked = $("#treportBtnClicked").val();

    if (grade != '')
    {
        $('#th_teacher_report_grade_select').val("");
        var html = "<option value=''>select</option>";
        $('#th_teacher_report_section_select').html(html);
        $.ajax({
            type: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "teacherreport/getTeacherMappedClass",
            dataType: 'json',
        }).done(function (data) {

            Helpers.ajax_response(getTeacherReportGradeAjax, data, []);
            $("#th_teacher_report_grade_select").val(grade);
        });
    }

    if (section != '')
    {
        $.ajax({
            type: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "teacherreport/getTeacherMappedSection",
            dataType: 'json',
            data: {'selectedClass': grade},
        }).done(function (data) {
            Helpers.ajax_response(getTeacherReportSectionAjax, data, []);
            $("#th_teacher_report_section_select").val(section);

            setTimeout(function () {
                if (btnclicked == 'view')
                {
                    $("#th_report_view_all").trigger('click');
                } else if (btnclicked == 'go')
                {
                    $("#th_report_go").trigger('click');
                }
            }, 1000);

        });
    }
    if (startDate != '')
        $("#teacherReportStartDate").datepicker().datepicker("setDate", startDate);
    if (endDate != '')
        $("#teacherReportEndDate").datepicker().datepicker("setDate", endDate);

    if (grade == '' && section == '')
    {
        if (btnclicked == 'view')
        {
            $("#th_report_view_all").trigger('click');
        }
    }

    var openedTabArr = $("#treportTabClicked").val().split('|');
    var uniqueTabArr = jQuery.unique(openedTabArr.filter(function (v) {
        return v !== ''
    }));
    //console.log(uniqueTabArr.length);
    if (uniqueTabArr.length > 0)
    {
        for (var i = 0; i < uniqueTabArr.length; i++)
        {

            //console.log(uniqueTabArr[i]);
            $("#" + uniqueTabArr[i]).trigger('click');

        }
    }
});
/*END*/
$( "#v2-essayAssignment .fa.fa-info-circle" ).tooltip({
    tooltipClass: "essay-info-container",
    position: {
        my: 'left top-45', 
        at: 'right+15 top',
        collision: "flip"
    }
});
