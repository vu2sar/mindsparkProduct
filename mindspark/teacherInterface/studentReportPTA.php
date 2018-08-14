<?php
    error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
    set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
    
    include("../userInterface/classes/clsTopicProgress.php");
    include("../userInterface/constants.php");
    
    include("header.php");
    include("../slave_connectivity.php");
    

    global $mpiFlag;
    
	
    $userid	=	$_SESSION['userID'];
    $schoolCode = $_SESSION['schoolCode'];
    $query  = "SELECT childName,schoolCode, category FROM adepts_userDetails WHERE userID=".$userid;
    
    $result = mysql_query($query) or die(mysql_error());
    $line   = mysql_fetch_array($result);
    $userName 	= $line[0];
    /*$schoolCode = $line[1];*/
    $category   = $line[2];
    $schoolName = getSchoolName($schoolCode);
    
    
    
    if(strcasecmp($category,"School Admin")!=0 && strcasecmp($category,"Teacher")!=0)
    {
        echo "You are not authorised to access this page!";
        exit;
    }
    if(strcasecmp($category,"School Admin")==0)
    {
        $query  = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
                   FROM     adepts_userDetails
                   WHERE    schoolCode=$schoolCode AND category='STUDENT' AND subcategory='School' AND endDate>=curdate() AND enabled=1
                   GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
    }
    elseif (strcasecmp($category,"Teacher")==0)
    {
        $query = "SELECT   class, group_concat(distinct section ORDER BY section)
                  FROM     adepts_teacherClassMapping
                  WHERE    userID = $userid
                  GROUP BY class ORDER BY class, section";
    }
    $classArray = $sectionArray = array();
    $hasSections = false;
    $result = mysql_query($query) or die(mysql_error());
    while($line=mysql_fetch_array($result))
    {
        array_push($classArray, $line[0]);
        if($line[1]!='')
            $hasSections = true;
        $sections = explode(",",$line[1]);
        $sectionStr = "";
        for($i=0; $i<count($sections); $i++)
        {
            if($sections[$i]!="")
                  $sectionStr .= "'".$sections[$i]."',";
        }
        $sectionStr = substr($sectionStr,0,-1);

        array_push($sectionArray, $sectionStr);
    }
    
    $class    = isset($_POST['class'])?$_POST['class']:"";
    $section  = isset($_POST['section'])?$_POST['section']:"";

    
    $date1 = $_POST['dateTo'];
    $tillDate = date('Y-m-d', strtotime(str_replace('/', '-', $date1)));
    
    $date2 = $_POST['dateFrom'];
    $fromDate = date('Y-m-d', strtotime(str_replace('/', '-', $date2)));
    
    
    $scoreOutOf = isset($_POST['scoreOutOf'])?$_POST['scoreOutOf']:100;
    $showMax  = isset($_POST['chkShowMax'])?1:0;
    $showAvg  = isset($_POST['chkShowAvg'])?1:0;
    $curDate = date("Y-m-d");
    /*echo '<script type="text/javascript">alert("' .$_POST['dateTo']. '"); </script>';
    echo '<script type="text/javascript">alert("' .$_POST['dateFrom']. '"); </script>';*/
    $scoreStr = "";
    
    if(!isint($scoreOutOf))
       $scoreOutOf = 10;
    $subscriptionPeriodResult = mysql_query("SELECT start_date, end_date from educatio_educat.ms_orderMaster where schoolCode=$schoolCode and start_date<=CURDATE() order by start_date desc limit 1");
    $subscriptionPeriod = mysql_fetch_assoc($subscriptionPeriodResult);
    
?>



<title>Mindspark Performance Report</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/studentReportPTA.css?datetime=2016.09.29.16.59.20" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="css/print1.css?ver=1" type="text/css" media="print" />
<link rel="stylesheet" type="text/css" href="css/colorbox.css" />
<link rel="stylesheet" href="performance_index/stylesheets/certificate.css?datetime=2016.09.29.16.59.20">
<style>
</style>
<!-- <script src="../userInterface/libs/jquery.js"></script> -->
<link rel="stylesheet" href="css/jquery-ui.css" />
<!--<script src="http://code.jquery.com/jquery-1.9.1.js"></script>-->
<!--<script src="libs/jquery-ui.js"></script>-->
<!--<script src="libs/jquery.colorbox-min.js" type="text/javascript"></script>-->
<!--<link rel="stylesheet" href="/resources/demos/style.css" />-->
<script>
    var availableDates = ["9-5-2011", "14-5-2011", "15-5-2011"];
    
    function available(date) {
        dmy = date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear();
        var day = date.getDate();
        var availableDates = ["9-5-2011", "14-5-2011", "15-5-2011"];
        if (day == 1 || day == 16) {
            return [true, "", "Available"];
        } else {
            return [false, "", "unAvailable"];
        }
    }
    function available2(date) {
        dmy = date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear();
        var day = date.getDate();
        var month = date.getMonth() + 1;
        if (month == 1 || month == 3 || month == 5 || month == 7 || month == 8 || month == 10 || month == 12) {
            var days = 31;
        }
        if (month == 2) {
            var year = date.getFullYear();
            if (year % 4 == 0)
                var days = 29;
            else
                var days = 28;
        }
        if (month == 4 || month == 6 || month == 9 || month == 11) {
            var days = 30;
        }
        if (day==15||day==days) {
            return [true, "", "Available"];
        } else {
            return [false, "", "unAvailable"];
        }
    }
    function checkNumeric(evt){
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && charCode != 46 && charCode != 47 && charCode != 45 && (charCode < 48 || charCode > 57)) {
           return false;
        }
        return true;
    }
    $(function () {
    
        var limit = {
            fromDate: function(value) {
                var dateComponents = value.split('/');
                var toDate = new Date(dateComponents[2], dateComponents[1]-1, dateComponents[0]);
                $('.datepicker1').datepicker('option', 'maxDate', new Date(dateComponents[2], toDate.getMonth()-1, dateComponents[0]));
            },
            toDate: function(value) {
                var dateComponents = value.split('/');
                var fromDate = new Date(dateComponents[2], dateComponents[1]-1, dateComponents[0]);
                $('.datepicker').datepicker('option', 'minDate', new Date(dateComponents[2], fromDate.getMonth()+1, dateComponents[0]));
            },
        };
        $(".datepicker").datepicker({
            dateFormat: 'dd/mm/yy',
            maxDate: '<?=$subscriptionPeriod['end_date']?>'.split('-').reverse().join('/'),
            // beforeShowDay: available2,
        });
        $(".datepicker1").datepicker({
            dateFormat: 'dd/mm/yy',
            minDate: '<?=$subscriptionPeriod['start_date']?>'.split('-').reverse().join('/'),
            onSelect: limit.toDate,
            // beforeShowDay: available,
        });
        /*if (document.getElementById('dateFrom').value == "") {
    
            $('.datepicker').datepicker('setDate', new Date());
    
            var str = $('.datepicker').datepicker('setDate', new Date()).val();
            var n = str.split("/");
    
            var dateString = "01/" + n[1] + "/" + n[2];
            var dateString2 = "15/" + n[1] + "/" + n[2];
    
            $(".datepicker1").datepicker("setDate", dateString);
            $(".datepicker").datepicker("setDate", dateString2);
        }*/
        $('.datepicker').datepicker('option', 'maxDate', '<?=date("d/m/Y");?>');
        limit.fromDate($('.datepicker').datepicker('option', 'maxDate'));
        limit.toDate($('.datepicker1').datepicker('option', 'minDate'));
        $('#gracePeriodTip, #scoreOutOfTip').on({
            click: function(event) {
                if($(this).find('div').is(':hidden')) {
                    $(this).find('div').show();
                    if(this.id=='gracePeriodTip')
                        $('#scoreOutOfTip div').hide();
                    else
                        $('#gracePeriodTip div').hide();
                    event.stopPropagation();
                }
            },
        }).find('div').hide();
        $('#gracePeriodTip div, #scoreOutOfTip div').on({
            click: function(event) {
                event.stopPropagation();
            },
        });
        $(document).on({
            click: function() {
                $('#gracePeriodTip div, #scoreOutOfTip div').hide();
            },
        });
    
    
    
    });
	function sendEmails()
	{
        var missedEmailStudents = [];
        var message = '';
		$(".mpi_certificate").each(function(index, element) {            
			$.ajax({
                type: 'post',
                url: 'ajaxRequest.php',
                async : false,
                data: {
                    mode: 'mpiStudentReportPTAMailer',
                    userID: $(this).find("input:hidden").val(),
                    mailBody: $(this).html()                    
                },                
            }).done(function(response) {
                if(response != 'success')
				    missedEmailStudents.push(response);
			});
		});
        if(missedEmailStudents.length>0)
        {
            message = 'Mindspark failed to email reports to the parents of the following students. This could be because no valid parent email address was found. Please ensure that your students have entered correct parent email addresses to receive this report. You can also click Download to download the reports for the entire class.\n';
            $(missedEmailStudents).each(function(index, element) {
               message += '('+(index+1)+') '+element+'\n';
            });
            alert(message);
            $("#sendEmail").css("visibility","hidden");
        }
        else
        {
            alert('Reports are mailed to the parents');
            $("#sendEmail").css("visibility","hidden");
        }
		//$(".mpi_certificate").on("each",function() {
			//alert("a");
			//$(this).find("input:hidden").val();
			/*$.ajax({
                type: 'post',
                url: 'ajaxRequest.php',
                data: {
                    mode: 'mpiLinkStudentReportPTA',
                    mailBody: $(this).find("input:hidden").val(),
                    userID: $(this).html()
                },
                dataType: 'json',
            }).done(function(response) {
				
			});*/
			//alert($(this).html());
		//});
	}
</script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script>
    var langType = '<?=$language;?>';
    function load(){
        var fixedSideBarHeight = window.innerHeight;
        var sideBarHeight = window.innerHeight-95;
        var containerHeight = window.innerHeight-115;
        $("#fixedSideBar").css("height",fixedSideBarHeight+"px");
        $("#sideBar").css("height",sideBarHeight+"px");
        /*$("#container").css("height",containerHeight+"px");*/
        $("#features").css("font-size","1.em");
        $("#features").css("margin-left","40px");
        $(".arrow-right-blue").css("margin-left","10px");
        $(".rectangle-right-blue").css("display","block");
        $(".arrow-right-blue").css("margin-top","3px");
        $(".rectangle-right-blue").css("margin-top","3px");
    }
    
    function openCalender(id){
        var id=id;
        if(id=="from"){
            $("#dateFrom").focus();
        }
        else{
            $("#dateTo").focus();
        }
    }
    
    function downloadExcel()
    {
    
        if($('.mpi_certificate').length==0)
        {
            alert("To download, first generate the report.");
        }
        else
        {
            $("#score_sheet").submit();
        }
    
    }
    
    
</script>


<script type="text/javascript" src="libs/calendarDateInput.js"></script>
<script type="text/javascript" src="libs/tablesort.js"></script>
<script type="text/javascript" src="libs/html2canvas/0.4.1/html2canvas.js"></script>
<script src="libs/idletimeout.js" type="text/javascript"></script>
<script>
    var gradeArray   = new Array();
    var sectionArray = new Array();
    <?php
        for($i=0; $i<count($classArray); $i++)
        {
            echo "gradeArray.push($classArray[$i]);\r\n";
            echo "sectionArray[$i] = new Array($sectionArray[$i]);\r\n";
        }
    ?>
    function setSection(sec)
    {
        var cls = document.getElementById('lstClass').value;
    
        if(document.getElementById('lstSection'))
        {
            var obj = document.getElementById('lstSection');
            removeAllOptions(obj);
            if(cls=="")
            {
    
                document.getElementById('lstSection').style.display = "inline";
                document.getElementById('lstSection').selectedIndex = 0;
            }
            else
            {
                for(var i=0; i<gradeArray.length && gradeArray[i]!=cls; i++);
                if(sectionArray[i].length>0)
                {
                    for (var j=0; j<sectionArray[i].length; j++)
                    {
                        OptNew = document.createElement('option');
                        OptNew.text = sectionArray[i][j];
                        OptNew.value = sectionArray[i][j];
                        if(sec==sectionArray[i][j])
                            OptNew.selected = true;
                        obj.options.add(OptNew);
                    }
    
                    document.getElementById('lstSection').style.display = "inline";
                    document.getElementById('lblSection').style.display = "inline";
                }
                else
                {
    
                    document.getElementById('lstSection').style.display = "none";
                    document.getElementById('lblSection').style.display = "none";
                }
            }
        }
    }
    
    function removeAllOptions(selectbox)
    {
        var i;
        for(i=selectbox.options.length-1;i>0;i--)
        {
            selectbox.remove(i);
        }
    }
    function allowOnlyNumbers(event) {
        return /Firefox/.test(window.navigator.userAgent) && event.keyCode!=0 || /^[0-9./]$/.test(String.fromCharCode(event.which || event.keyCode));
    }
    function drawDashedRing(ringRadius, numberOfDashes, fillMeasure) {
        var ringRadius = {
            outer: ringRadius,
            inner: 0.8*ringRadius,
        };
        var canvas = document.createElement('canvas');
        canvas.width = canvas.height = 2*ringRadius.outer;
        var drawing = canvas.getContext('2d');
        var dashMeasure = (2*Math.PI)/numberOfDashes;
        drawing.translate(ringRadius.outer, ringRadius.outer);
        drawing.rotate(-Math.PI/2);
        drawing.lineWidth = 1;
        var filledDashes = Math.round(fillMeasure/dashMeasure);
        var dashCount = 0;
        drawing.strokeStyle = '#111111';
        while(dashCount<filledDashes) {
            drawing.beginPath();
            drawing.moveTo(ringRadius.inner*Math.cos(dashMeasure*dashCount), ringRadius.inner*Math.sin(dashMeasure*dashCount));
            drawing.lineTo(ringRadius.outer*Math.cos(dashMeasure*dashCount), ringRadius.outer*Math.sin(dashMeasure*dashCount));
            drawing.stroke();
            dashCount++;
        }
        drawing.strokeStyle = '#bbbbbb';
        while(dashCount<numberOfDashes) {
            drawing.beginPath();
            drawing.moveTo(ringRadius.inner*Math.cos(dashMeasure*dashCount), ringRadius.inner*Math.sin(dashMeasure*dashCount));
            drawing.lineTo(ringRadius.outer*Math.cos(dashMeasure*dashCount), ringRadius.outer*Math.sin(dashMeasure*dashCount));
            drawing.stroke();
            dashCount++;
        }
        return canvas.toDataURL();
    }
    function compareObjects() {
        return 0;
    }
    function setMPISettings(newValues) {
        if(compareObjects(mpi.settings, newValues)!==0)
            return;
        mpi.settings = newValues;
        mpi.weightageRings = {};        
       for(var field in mpi.settings.weightages) {
            if(!mpi.settings.weightages.hasOwnProperty(field))
                continue;
            mpi.weightageRings[field] = drawDashedRing(35, 48, (mpi.settings.weightages[field]/100)*2*Math.PI);
        }        
    }
    var mpi = {
        templates: {
            score_sheet: {},
        },
    };
    setMPISettings({
        weightages: {
            'Accuracy': 40,
            'Badges': 10,
            'Weekly Usage Score': 40,
            'Topic Completion': 10,
        },
        others: {
            'Minimum weekly usage': 45,
            'Minimum weekly question attempts': 25,
        },
    });
    function generateReport()
    {
         var $messageDiv = $('#main'); 
        var tempClass=$("#lstClass").val(),tempSection=$("#lstSection").val();
		$('#MPISummary,#print,#printImage,.downloadImage,#downloads').css('display','none');
        var val=document.getElementById('dateFrom').value;
    
        var splits = val.split("/");
        var dt = new Date(splits[1] + "/" + splits[0] + "/" + splits[2]);
    
        //Validation for Dates
        if(dt.getDate()==splits[0] && dt.getMonth()+1==splits[1]
            && dt.getFullYear()==splits[2])
        {
    
        }
        else
        {
            alert("Please select a 'From' date.");
            return false;
        }
    
    
        var val=document.getElementById('dateTo').value;
    
        var splits = val.split("/");
        var dt = new Date(splits[1] + "/" + splits[0] + "/" + splits[2]);
    
        //Validation for Dates
        if(dt.getDate()==splits[0] && dt.getMonth()+1==splits[1]
            && dt.getFullYear()==splits[2])
        {
    
        }
        else
        {
            alert("Please select a 'To' date.");
            return false;
        }
    
    
        var a = document.getElementById('dateFrom').value;
        var arr = a.split('/');
        var mmd = new Date(parseInt(arr[2]),(parseInt(arr[1],10) - 1),parseInt(arr[0])) ;
//        alert(mmd);
    
        var b = document.getElementById('dateTo').value;
        var arr1 = b.split('/');
        var mmd1 = new Date(parseInt(arr1[2]),(parseInt(arr1[1],10) - 1),parseInt(arr1[0])) ;
//        alert(mmd1);
        //Get 1 day in milliseconds
          var one_day=1000*60*60*24;

          // Convert both dates to milliseconds
          var date1_ms = mmd1.getTime();
          var date2_ms = mmd.getTime();

          // Calculate the difference in milliseconds
          var difference_ms = date1_ms - date2_ms;
    
          // Convert back to days and return
          var noOfdays= Math.round(difference_ms/one_day); 
          var noOfWeeks= Math.round(noOfdays*100/7)/100; 
    
        if(mmd > mmd1)
        {
            alert("Your TO date should be greater than FROM date");
            return false;
        }
        
        var grace = document.getElementById('gracePeriod').value;
        if(!/^[0-9]+(\.[0-9]+)?(\/[0-9]+(\.[0-9]+)?)?$/.test(grace)) {
            alert('Grace period has to be a number.');
            return false;
        }
        var scoreOutOf = document.getElementById('scoreOutOf').value;
        if(!/^[0-9]+(\.[0-9]+)?(\/[0-9]+(\.[0-9]+)?)?$/.test(scoreOutOf)) {
            alert('Score has to be a number.');
            return false;
        }
        if(eval(grace)>noOfWeeks){
            alert("Grace period cannot be greater than period you've selected.");
            return false;
        }
        if(grace<0){
            alert("Grace period can't be less than zero");
            return false;
        }
    
        var cls = document.getElementById('lstClass').value;
        if(document.getElementById('lstSection'))
			var sec = document.getElementById('lstSection').value;
		else 
			var sec = '';
    
        if(cls =="")	{
            alert("Please select a class!");
            document.getElementById('lstClass').focus();
            return false;
        }
        else if(document.getElementById('lstSection') && $("#lstSection").is(":visible") && sec == "") // Temporarily section will not be asked.
		{
			alert("Please select a section!");
			document.getElementById('lstSection').focus();
			return false;
		}
        else {
            $("#main").attr('align', 'center').html('<img src="assets/loadingImg.gif" height="332" width="332" color:"red"><br><center>It will take 2 to 5 minutes.</center>').show();
            $.ajax({
                type: 'post',
                url: 'ajaxRequest.php',
                data: {
                    mode: 'mpiLinkStudentReportPTA',
                    schoolCode: <?=$schoolCode?>,
                    classValue: +$('#lstClass').val(),
                    sectionValue: $('#lstSection').val(),
                },
                dataType: 'json',
            }).done(function(response) {
                if(response.hasOwnProperty('weightages'))
                    setMPISettings(response);
                $.ajax({
                    type: 'post',
                    url: 'performance_index/reports.php',
                    data: {
                        schoolCode: <?=$schoolCode?>,
                        schoolName: '<?=$schoolName?>',
                        class: +$('#lstClass').val(),
                        section: $('#lstSection').val(),
                        fromDate: $('#dateFrom').val().split('/').reverse().join('-'),
                        toDate: $('#dateTo').val().split('/').reverse().join('-'),
                        gracePeriod: eval($('#gracePeriod').val()),
                        scoreOutOf: eval($('#scoreOutOf').val()),
                        mpiSettings: mpi.settings,
                    },
                    dataType: 'json',
                }).done(function(mpiReports) {

                    $('#main').append('<ul id="mpi_certificates"></ul>');
                    $('#score_sheet>table').remove();
                    $score_sheet = $(mpi.templates.score_sheet);
                    $headings = $score_sheet.children('thead');
                    $headings.html($headings.html().replace(/\{\{(.+?)\}\}/g, function(match, $1) {
                        return eval($1);
                    }));
                    $score_body = $score_sheet.children('tbody');
                    $score_row = $score_body.html();
                    $score_body.html('');
                    for(var index=0; index<mpiReports.length; index++) {
                        var $certificate = $(mpi.templates.certificate);
                        var $front = $certificate.children('.front'), certificate = mpiReports[index]; 
                        // var input = $certificate.children('.front').html();
                        // alert(input);                      
                        var performance = $front.find('.performance tbody');
                        var fieldTemplate = performance.find('.field').replaceWith('').wrap('<z>').parent().html();                                                            
                        for(var $field in certificate.report.performance) {
                            if(!certificate.report.performance.hasOwnProperty($field))
                                continue;
                            var summary = certificate.report.performance[$field];
                            $(fieldTemplate/*.replace(/\$field/g, $field)*/.replace(/\{\{(.+?)\}\}/g, function(match, $1) {
                                return eval($1);
                            })).appendTo(performance).find('.weightage').css('background-image', 'url("'+mpi.weightageRings[$field]+'")');
                        }
                        $front.html($front.html().replace(/\{\{(.+?)\}\}/g, function(match, $1) {
                            return eval($1);
                        }));
                        $score_body.append($score_row.replace(/\{\{(.+?)\}\}/g, function(match, $1) {
                            return eval($1);
                        }));
                        $certificate.appendTo('#mpi_certificates');
                    }
                    $score_sheet.appendTo('#score_sheet');
                    var certificates = {
                        elements: $('.mpi_certificate').remove(),
                        completed: [],
                    };
                    function imagifyCertificates() {
                        if(certificates.elements.length) {
                            var certificate = certificates.elements.eq(0).appendTo('#mpi_certificates');
                            html2canvas(certificate.children('.front')[0], {
                                onrendered: function(canvas) {
                                    $('<img src="'+canvas.toDataURL()+'" class="front">').insertAfter(certificate.children('.front'));
                                    html2canvas(certificate.children('.back')[0], {
                                        onrendered: function(canvas) {
                                            $('<img src="'+canvas.toDataURL()+'" class="back">').insertAfter(certificate.children('.back'));
                                            certificate.children('.front, .back').addClass('rendered');
                                            certificates.elements.splice(0, 1);
                                            certificates.completed.push($('.mpi_certificate').remove());
                                            imagifyCertificates();
                                        },
                                    });
                                },
                            });
                        } else {
                            for(var i=0; i<certificates.completed.length; i++)
                                certificates.completed[i].appendTo('#mpi_certificates');
                            $('#MPISummary,#print,#printImage,.downloadImage,#downloads').css('display','inline-block');
                            $('.flip_instruction').hide();
                            $('section.back').remove();
                            $('#main>:not("#mpi_certificates")').remove();
                            $('#main').removeAttr('align');
                        }
                    }
                    imagifyCertificates();
                    $('#score_sheet>#fileName')[0].value = mpiReports[0].profile.class+'-'+mpiReports[0].profile.section;
                    $('#score_sheet>#content')[0].value = $('#score_sheet>table')[0].outerHTML.replace(/\s\s+/g, '');
                }).fail(function(response) {
                    if(response.responseText.slice(-6)=='logout') {
                        window.location.href = '/mindspark/userInterface/error.php';
                    } else {
                        $('#main').html(response.responseText);
                    }
                });
            });
            // Added for mantis task ID-17783 
			// $("#sendEmail").css("visibility","visible");
        }
    }
    
    function printMe()
    {
        if($(".mpi_certificate").length == 0)
        {
            alert("To print, first generate the report.");
        }
        else
        {
			//$(".contentSection").css("min-height","700px");
            window.print();
			//$(".contentSection").removeAttr("style");
            return false;
        }
    }
    
</script>

 
<script>
    $(document).ready(function() {
        $(".selectAll,.uniqueCheck").change(function() {
            if($(this).hasClass("selectAll"))
            {
                if($('.selectAll').is(":checked"))
                {
                    $("#userString").val("");
                    $(".uniqueCheck").attr("checked",true);
                }
                else
                {
                    $(".uniqueCheck").attr("checked",false);
                }
            }
            else
            {
                if(!$(this).is(":checked"))
                {
                    $(".selectAll").attr("checked",false);
                }
                else
                {
                    var boolVar	=	true;
                    $('.uniqueCheck').each(function(){
                        if(!$(this).is(":checked"))
                            boolVar=false;
                    });
                    if(boolVar === false)
                        $(".selectAll").attr("checked",false);
                    else
                        $(".selectAll").attr("checked",true);
                }
            }
        });
        $("#btnDownloadPdf").click(function() {
            var link	=	$("#pdfLink").val();
            if($('.selectAll').is(":checked"))
            {
                $('#userString').val("");
                $("#frmDownloadExcel").attr("action", "generatePtaPdf.php");
                $("#frmDownloadExcel").submit();
            }
            else
            {
                var userids	=	'';
                $('.uniqueCheck').each(function(){
                    if($(this).is(":checked"))
                    {
                        var uid	=	$(this).attr("id");
                        var userid	=	uid.split("_");
                        userids	+=	userid[1]+",";
                    }
                });
                if(userids=='')
                    alert("Please select at least one user");
                else
                {
                    $('#userString').val(userids);
                    $("#frmDownloadExcel").attr("action", "generatePtaPdf.php");
                    $("#frmDownloadExcel").submit();
                }
            }
        });
        $('#print,printImage').css('visibility', 'hidden');
        var printEnabledBrowsers = {
            'Windows': [
                'Trident',
                'Firefox',
                'Chrome',
                'Edge',
            ],
            'Macintosh': [
                'Safari',
                'Chrome',
            ],
        };
        checkingPrint: for(var OS in printEnabledBrowsers) {
            if(!printEnabledBrowsers.hasOwnProperty(OS))
                continue;
            if(window.navigator.userAgent.indexOf(OS)>=0) {
                var i = printEnabledBrowsers[OS].length;
                while(i--) {
                    if(window.navigator.userAgent.indexOf(printEnabledBrowsers[OS][i])>=0) {
                        $('#print,printImage').css('visibility', 'visible');
                        break checkingPrint;
                    }
                }
            }
        }
        $.ajax({
            type: 'get',
            url: 'performance_index/templates/certificate.html?ver=1',
        }).done(function(certificate) {
            mpi.templates.certificate = certificate;
            $.ajax({
                type: 'get',
                url: 'performance_index/templates/score_sheet.html?ver=1',
            }).done(function(score_sheet) {
                mpi.templates.score_sheet = score_sheet;
                $('#btnGo').show();
            });
        });
    });
    
    
</script>
<script type="text/javascript">
    var isShift=false;
    var seperator = "/";
    function DateFormat(txt , keyCode)
    {
        if(keyCode==16)
            isShift = true;
        //Validate that its Numeric
        if(((keyCode >= 48 && keyCode <= 57) || keyCode == 8 ||
             keyCode <= 37 || keyCode <= 39 ||
             (keyCode >= 96 && keyCode <= 105)) && isShift == false)
        {
            if ((txt.value.length == 2 || txt.value.length==5) && keyCode != 8)
            {
                txt.value += seperator;
            }
            return true;
        }
        else
        {
            return false;
        }
    }	
    
function openMPITable()
	{  
		$('#MPITable').css('display','block');
		$.fn.colorbox({'href':'#MPITable','inline':true,'open':true,'escKey':true, 'height':400, 'width':700});
	}
</script>
</head>
<body class="translation" onResize="load()" onLoad="setSection('<?=$section?>');load();" onmousemove="reset_interval()" onclick="reset_interval()" onkeypress="reset_interval()" onscroll="reset_interval()" style="overflow: auto">
    <?php include("eiColors.php") ?>
    <div id="fixedSideBar">
        <?php include("fixedSideBar.php") ?>
    </div>
    <div id="topBar">
        <?php include("topBar.php"); include("../slave_connectivity.php"); ?>
    </div>
    <div id="sideBar">
        <?php include("sideBar.php") ?>
    </div>
    
    <div id="container">
        <!--pnlSelection-->
        <div id="trailContainer">
            <form id="frmTeacherReport" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
                <div id="headerBar">
                    <div id="pageName">
                        <div class="arrow-black"></div>
                        <div id="pageText">Mindspark Performance Report</div>
                    </div>
                    <div class="mpiHelpicon" onClick="window.open('performance_index/FAQs.pdf', '_blank');" style="position: absolute; right: 6px; top: 5px; width: 20px; height: 20px; border-radius: 50%; border: 1px solid black; display: inline-block; font-size: 18px; font-weight:bold; line-height: 20px; text-align: center; vertical-align: middle; cursor: pointer;">
                        ?
                        <span style="position: absolute; left: -4px; top: 100%; font-size: 16px; font-weight: normal;">Help</span>
                    </div>
                </div>

                <table id="topicDetails">
                    <td width="6%"><label>Class</label></td>
                    <td width="20%" style="border-right:1px solid #626161">
                            <select name="class" id="lstClass" style="width:95%;" onChange="setSection('')">
                            <option value="">Select</option>
                            <?php for($i=0; $i<count($classArray); $i++)	{ ?>
                            <option value="<?=$classArray[$i]?>" <?php if($class==$classArray[$i]) echo " selected";?>><?=$classArray[$i]?></option>
                            <?php	}	?>
                        </select>
                    </td>
                    <?php if($hasSections) { ?>
                    <td width="6%" class="noSection"><label id="lblSection" style="">Section</label></td>
                    <td width="10%" class="noSection" style="border-right:1px solid #626161">
                        <select name="section" id="lstSection">
                            <option value="">Select</option>
                        </select>
                    </td>
                    <?php } ?>
                    <!-- <td width="24%">Score out of<input type="text" name="scoreOutOf" onKeyPress="return checkNumeric(event)" id="txtScore" value="<?=$scoreOutOf?>"></td> -->
                    <td width="4%">
                        <label>From</label>
                    </td>
                    <td width="25%" style="border-right:1px solid #626161"><input type="text" name="dateFrom" class="datepicker1 floatLeft" readonly id="dateFrom" value="<?=$date2?>" autocomplete="off" size="20" onKeyDown="return DateFormat(this, event.keyCode)" maxlength="10" style="width: 80%;"/><div class="calenderImage linkPointer" id="from" onClick="openCalender(id)"></div></td>
                    <td width="4%">
                        <label style="">To</label>
                    </td>
                    <td width="25%" style=""><input type="text" name="dateTo" readonly class="datepicker floatLeft" id="dateTo" value="<?=$date1?>" autocomplete="off" size="20" onKeyDown="return DateFormat(this, event.keyCode)" maxlength="10" style="width: 80%;"/><div class="calenderImage linkPointer" id="to" onClick="openCalender(id)"></div></td>

                </table>

                <table id="generateTable">
                    <td width="1%"></td>
                    <td style=""><div style="text-align:right;">
                            <span style="vertical-align: middle;margin-left:64px;">Score out of</span><input type="text" name="scoreOutOf" id="scoreOutOf" onkeypress="return allowOnlyNumbers(event);" value="100" onKeyPress="return checkNumeric(event);" style="margin: 0 2px; width: 42px; vertical-align: middle;"><div id="scoreOutOfTip" style="position: relative; width: 20px; height: 20px; border-radius: 50%; border: 1px solid black; display: inline-block; font-size: 18px; font-weight:bold; line-height: 20px; text-align: center; vertical-align: middle; cursor: pointer;">?<div style="position: absolute; left: -70px; top: 112%; z-index: 10; width: 150px; box-shadow: 0 0 4px 3px gray; padding: 5px; border-radius: 3px; font-size: 14px; font-weight: normal; background-color: rgba(245, 245, 245, 1); cursor: default;">The maximum Mindspark performance score that a student can get</div></div>
                            <span style="vertical-align: middle;margin-left:4px;">Grace period</span><input type="text" name="gracePeriod" id="gracePeriod" onkeypress="return allowOnlyNumbers(event);" value="0" onKeyPress="return checkNumeric(event);" style="margin: 0 2px; width: 42px; vertical-align: middle;"><div id="gracePeriodTip" style="position: relative; width: 20px; height: 20px; border-radius: 50%; border: 1px solid black; display: inline-block; font-size: 18px; font-weight:bold; line-height: 20px; text-align: center; vertical-align: middle; cursor: pointer;">?<div style="position: absolute; left: -70px; top: 112%; z-index: 10; width: 150px; box-shadow: 0 0 4px 3px gray; padding: 5px; border-radius: 3px; font-size: 14px; font-weight: normal; background-color: rgba(245, 245, 245, 1); cursor: default;">No. of weeks where students are not required to do Mindspark</div></div>
                            <div style="float:none;vertical-align:middle;" class="printImage linkPointer" id="print" onClick="printMe()"></div><div id="print" style="margin-left:5px;color:#2f99cb;padding-top: 3px;cursor: pointer;vertical-align:middle;" onClick="printMe()">Print this page</div>
                            <div style="margin-left:22px;float:none;vertical-align:middle;" class="downloadImage linkPointer" id="from" onClick="downloadExcel()"></div><div id="downloads" style="color:#2f99cb;padding-top: 3px;cursor: pointer;margin-left:5px;vertical-align:middle;" onClick="downloadExcel()">Download</div><input type="button" class="button" name="generate" id="btnGo" value="Generate" onClick="return generateReport();" style="margin-left: 15px; vertical-align: middle; display: none;"><input type="button" class="button" name="sendEmail" id="sendEmail" value="Email to Parents" onClick="sendEmails();" style="margin-left: 15px; vertical-align: middle; visibility:hidden;">
                    </div></td>
                    <!-- <td width="26.2%" style="border-right:1px solid #626161"><input type="text" name="dateTo" readonly class="datepicker floatLeft" id="dateTo" value="<?=$date1?>" autocomplete="off" size="20" onKeyDown="return DateFormat(this, event.keyCode)" maxlength="10" /><div class="calenderImage linkPointer" id="to" onClick="openCalender(id)"></div></td> -->
                    <!-- <td width="24%">Show max. <input type="checkbox" name="chkShowMax" id="chkMax" <?php if($showMax==1) echo " checked"; ?>> Show Avg.<input type="checkbox" name="chkShowAvg" id="chkAvg" <?php if($showAvg==1) echo " checked"; ?>></td> -->
                </table>
                <!-- <div id="pagingTableSection" style="margin-bottom:8%;">
                    <table id="pagingTable">
                         <td width="22%">
                            <div style="font-size: 12px;font-weight: normal;color: red;text-align: center;">*grace is the number of days (apart from weekends) which were school holidays, like Diwali break</div>
                        </td>
                        <td width="3%" style=""><input type="button" class="button" name="generate" id="btnGo" value="Generate" onClick="return generateReport();"></td>
                       

                        <td width="8.5%" style=""><div class="printImage linkPointer" id="print" onClick="printMe()"></div><div id="print" style="margin-left:40px;color:#2f99cb;padding-top: 3px;cursor: pointer;" onClick="printMe()">Print this page</div></td>



                        <td width="1%" style=""><div class="downloadImage linkPointer" id="from" onClick="downloadExcel()"></div><div style="margin-left:40px;"><div id="downloads" style="color:#2f99cb;padding-top: 3px;cursor: pointer;" onClick="downloadExcel()">Download</div></div></td>
                       <td width="10%"> <div style="margin-left:40px;" id="MPISummary"><div style="color:#2f99cb;cursor: pointer;" onClick="openMPITable()">SUMMARY</div></div>

                        </td>
                    </table>

                </div> -->
                <input type="hidden" name="schoolCode" id="schoolCode" value="<?=$schoolCode?>">

            </form>
        </div>
        <!--pnlSelection-->

        <div align="center" id="main">
                   

        </div>
        <div id="loadingAnimation"></div>
        <form id="score_sheet" method="post" action="performance_index/score_sheet.php" style="display: none;">
            <input type="hidden" name="fileName" id="fileName" value="">
            <input type="hidden" name="content" id="content" value="">
        </form>
    </div>
	<div id='formPTA'>
    </div>

    <?php include("footer.php") ?>

    <?php

         getMySettingDataOfMPI($schoolCode);
    

        function getSchoolName($schoolCode)
        {
            $query  = "SELECT schoolname FROM educatio_educat.schools WHERE schoolno=$schoolCode";
            $result = mysql_query($query);
            $line   = mysql_fetch_array($result);
            return $line[0];
        }
        function isint( $mixed )
        {
            return ( preg_match( '/^\d*$/'  , $mixed) == 1 );
        }
    ?>
