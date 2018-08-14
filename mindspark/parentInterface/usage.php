<?php
include("header.php");
if (isset($_POST['generate'])) {
//    $fromDate = $_GET['fromDate'];
//    $tillDate = $_GET['tillDate'];
    $fromDate = $_POST['fromDate'];
    $tillDate = $_POST['tillDate'];
    $today = date('d-m-Y');
    $interval = date_create($fromDate)->diff(date_create($tillDate));    
//    if ($interval->days > 90) {
//        echo "<span style='color:red; font-weight:bold;margin-left: 33%;'>Date range can not be greater than 90 days.</span>";        
//    }
//    if ($fromDate > $tillDate) {
//        echo "<span style='color:red; font-weight:bold;margin-left: 33%;'>From date can not be greater than till date.</span>";
//    }
//    if ($fromDate > $today) {
//        echo "<span style='color:red; font-weight:bold;margin-left: 33%;'>From date can not be greater than today's date.</span>";
//    }
//    if ($tillDate > $today) {
//        echo "<span style='color:red; font-weight:bold;margin-left: 33%;'>Till date can not be greater than today's date.</span>";
//    }
    $startDate = date('Y-m-d', strtotime($fromDate));
    $endDate = date('Y-m-d', strtotime($tillDate));    
}
if ($startDate == '') {    
    $startDate = date('Y-m-d', strtotime("-15 days"));
    $fromDate = date('d-m-Y', strtotime("-15 days"));
}
if ($endDate == '') {
    $endDate = date('Y-m-d');
    $tillDate = date('d-m-Y');
}
?>

<title>Usage</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css?ver=10" rel="stylesheet" type="text/css">
<link href="css/usage.css?ver=2" rel="stylesheet" type="text/css">
<script src="libs/jquery-1.9.1.min.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css" />

<script src="libs/jquery-ui.min.js"></script>
<script>
    $(function() {
        $(".datepicker").datepicker({dateFormat: 'dd-mm-yy'});
    });
    function openCalender(id) {
        var id = id;
        if (id == "from") {
            $("#dateFrom").focus();
        }
        else {
            $("#dateTo").focus();
        }
    }
</script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="libs/dateValidator.js"></script>
<script>
    var langType = '<?= $language; ?>';
    function load() {
        var sideBarHeight = window.innerHeight - 95;
        var containerHeight = window.innerHeight - 115;
        $("#sideBar").css("height", sideBarHeight + "px");
        /*$("#container").css("height",containerHeight+"px");*/
    }
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
    var DateDiff = {
        inDays: function(d1, d2) {
            var t2 = d2.getTime();
            var t1 = d1.getTime();

            return parseInt((t2 - t1) / (24 * 3600 * 1000));
        },
        inWeeks: function(d1, d2) {
            var t2 = d2.getTime();
            var t1 = d1.getTime();

            return parseInt((t2 - t1) / (24 * 3600 * 1000 * 7));
        },
        inMonths: function(d1, d2) {
            var d1Y = d1.getFullYear();
            var d2Y = d2.getFullYear();
            var d1M = d1.getMonth();
            var d2M = d2.getMonth();

            return (d2M + 12 * d2Y) - (d1M + 12 * d1Y);
        },
        inYears: function(d1, d2) {
            return d2.getFullYear() - d1.getFullYear();
        }
    }
    function validate()
    {
        errorMsg = '';
        var fromDateValidation = isDate(document.getElementById('dateFrom').value);
        var isFromDateValid = (fromDateValidation==='true');
        if(!isFromDateValid)
            errorMsg += fromDateValidation+' for start date.\n';
        var tillDateValidation = isDate(document.getElementById('dateTo').value);
        var isTillDateValid = (tillDateValidation==='true');
        if(!isTillDateValid)
            errorMsg += tillDateValidation+' for end date.\n';
        if (document.getElementById('dateFrom').value == "")
        {
            errorMsg += 'Please enter the from date!\n';
        }
        if (document.getElementById('dateTo').value == "")
        {
            errorMsg += 'Please enter the till date!\n';
        }
        startDate = getDateObject(document.getElementById('dateFrom').value, "-");
        endDate = getDateObject(document.getElementById('dateTo').value, "-");
        var currentDate = new Date();
//        alert(DateDiff.inDays(startDate,endDate));
//        return false;
        dayDiff = DateDiff.inDays(startDate,endDate);
        if(dayDiff>90)
            errorMsg += 'Date range can not be more than 90 days.\n';
        if (startDate > endDate)
        {
            errorMsg += 'From Date cannot be greater than the Till Date.\n';
        }
        if (currentDate < endDate)
        {
            errorMsg += 'Till date can not be greater than current date.';
        }
        if (currentDate < startDate)
        {
            errorMsg += 'Start date can not be greater than current date.';
        }
        if (errorMsg != '')
        {
            alert(errorMsg);
            return false;
        }
		setTryingToUnload();
//        document.getElementById('frmDate').submit();
    }
    function getDateObject(dateString, dateSeperator)
    {
        //This function return a date object after accepting a date string and dateseparator as arguments
        var curValue = dateString;
        var sepChar = dateSeperator;
        var curPos = 0;
        var cDate, cMonth, cYear;
        //extract date portion
        curPos = dateString.indexOf(sepChar);
        cDate = parseInt(dateString.substring(0, curPos), 10);

        //extract month portion
        endPos = dateString.indexOf(sepChar, curPos + 1);
        cMonth = parseInt(dateString.substring(curPos + 1, endPos), 10);

        //extract date portion
        curPos = endPos;
        //endPos=curPos+5;
        cYear = parseInt(curValue.substring(curPos + 1), 10);

        //Create Date Object
        dtObject = new Date();
        dtObject.setFullYear(cYear, cMonth - 1, cDate);

        return dtObject;
    }
</script>
</head>
<body class="translation" onload="load()" onresize="load()">
    <?php include("eiColors.php") ?>
    <div id="fixedSideBar">
        <?php include("fixedSideBar.php") ?>
    </div>
    <div id="topBar">
        <?php include("topBar.php") ?>
    </div>
    <div id="sideBar">
        <?php include("sideBar.php") ?>
    </div>

    <div id="container">
        <?php include('referAFriendIcon.php') ?>
        <table id="childDetails">
            <td width="33%" id="sectionRemediation" class="pointer"><a href='topicUsage.php'><div class="smallCircle"></div></a><label class="pointer" value="secRemediation"><a href='topicUsage.php'>TOPIC PROGRESS</a></label></td></a>
            <td width="33%" id="sectionRemediation" class="pointer"><a href='usage.php'><div class="smallCircle red"></div></a><label class="textRed pointer" value="secRemediation"><a href='usage.php'>USAGE</a></label></td>
        </table>
        <form Method="POST" name="frmDate" id="frmDate">
            <table id="generateTable">
                <td width="16%">
                    <label for="fromDate">Date Range</label>
                </td>
				<td width="12%">
                    <label style="margin-left:20px;" for="tillDate">From</label>
                </td>
                <td width="32%"><input type="text" name="fromDate" class="datepicker floatLeft" id="dateFrom" value="<?= $fromDate ?>" autocomplete="off" onkeydown="return DateFormat(this, event.keyCode)" maxlength="10" size="15"/><div class="calenderImage linkPointer" id="from" onClick="openCalender(id)"></div></td>
                <td width="10%">
                    <label style="margin-left:20px;" for="tillDate">to</label>
                </td>
                <td width="34%"><input type="text" name="tillDate" class="datepicker floatLeft" id="dateTo" value="<?= $tillDate ?>" autocomplete="off" onkeydown="return DateFormat(this, event.keyCode)" maxlength="10" size="15"/><div class="calenderImage linkPointer" id="to" onClick="openCalender(id)"></div></td>
                <td width="8%"><input type="submit" class="button btnGo" name="generate" id="btnGo" value="Go" onClick="return validate();"></td>
            </table>
            <div id="divNote"><b>Note:</b> Date range must not be longer than 90 days.</div>
        </form>
        <div id="summaryNote">            
            <div id="timeSpentSummary">Total Time Spent :  minutes</div>            
<!--            <div id="timeSpentDisplay">Group by : </div>-->
        </div>
        <div>
            <?php include("usageReportDetail.php") ?>
        </div>
    </div>
    <?php include("footer.php") ?>