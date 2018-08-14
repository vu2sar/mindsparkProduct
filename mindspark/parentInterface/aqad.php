<?php
include("header.php");
if (isset($_GET['generate'])) {
    $fromDate = $_GET['fromDate'];
    $aqadClass = $_GET['classSelect'];
    $today = date('Y-m-d');
//    if($fromDate>$today)
//    {
//		echo "<span style='color:red; font-weight:bold;margin-left: 33%;'>From date can not be greater than today's date.</span>";        
//    }	
    $startDate = date('Y-m-d', strtotime($fromDate));
}
if ($startDate == '') {
    $fromDate = date('d-m-Y');
    $startDate = date('Y-m-d');
}
if ($aqadClass == '') {
    $aqadClass = $_SESSION['childClassUsed'];
}
?>
<title>Question A Day</title>
<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/aqad.css?ver=3" rel="stylesheet" type="text/css">
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
<script type="text/javascript" src="libs/closeDetection.js?interface=parent"></script>
<script type="text/javascript" src="libs/dateValidator.js"></script>
<script>
    var langType = '<?= $language; ?>';
    function load() {
        var sideBarHeight = window.innerHeight - 95;
        var containerHeight = window.innerHeight - 115;
        $("#sideBar").css("height", sideBarHeight + "px");
        /*$("#container").css("height",containerHeight+"px");*/
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
		setTryingToUnload();
        var DateValidation = isDate(document.getElementById('dateFrom').value);
        var isDateValid = (DateValidation==='true');
        if(!isDateValid)
            errorMsg += DateValidation+'\n';
        if (document.getElementById('dateFrom').value == "")
        {
            errorMsg += 'Please enter the from date!\n';
        }
        startDate = getDateObject(document.getElementById('dateFrom').value, "-");
        var currentDate = new Date();
        if (currentDate < startDate)
        {
            errorMsg += 'Selected date can not be greater than current date.';
        }
        var initialDate = new Date('June 25, 2013');
        if(startDate<initialDate)
            errorMsg += 'AQAD started from 25th June 2013. You can not select date earlier than that.';
        if (errorMsg != '')
        {
            alert(errorMsg);
            return false;
        }
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
	function changeQuestion(id){
		if(id==1){
			$("#aqad1").css("display","block");
			$("#aqad2").css("display","none");
			$("#aqad3").css("display","none");
			$(".questionPaging").removeClass("decorated");
			$("#1").addClass("decorated");
		}else if(id==2){
			$("#aqad2").css("display","block");
			$("#aqad1").css("display","none");
			$("#aqad3").css("display","none");
			$(".questionPaging").removeClass("decorated");
			$("#2").addClass("decorated");
		}else if(id==3){
			$("#aqad3").css("display","block");
			$("#aqad2").css("display","none");
			$("#aqad1").css("display","none");
			$(".questionPaging").removeClass("decorated");
			$("#3").addClass("decorated");
		}
	}
</script>
</head>
<body class="translation" onload="load()" onresize="load()">
<?php include("eiColors.php") ?>
    <div id="fixedSideBar">
    <?php include("fixedSideBar.php") ?>
    </div>
    <div id="topBar">
        <?php include("topBar.php");
		$childIDSel=$_SESSION['childID'];
		$child = new user($childIDSel);
		$startD = new DateTime($child->startDate);
		$aqadLiveDate = new DateTime('2014-08-30');
		if($aqadLiveDate>$startD){
			$startD = $aqadLiveDate;
		}
		$noOfQuestionsAttempted = 0;
		$datetime1 = new DateTime();
		$interval = $datetime1->diff($startD);
		$noOfQuestionsServed = $interval->format('%a') + 1;
		$noOfQuestionsCorrect = 0;
		$query = "select *,SUBSTR(entered_date,1,10) as endD from educatio_educat.aqad_responses where studentID=".$childIDSel." order by entered_date desc";
		$result = mysql_query($query);
		while($line = mysql_fetch_array($result)){
			$noOfQuestionsAttempted+=1;
			if($line['score']==1){
				$noOfQuestionsCorrect+=1;
			}
			$questionDate[4-$noOfQuestionsAttempted] = $line['endD'];
			$userAnswer[4-$noOfQuestionsAttempted] = $line['student_answer'];
		}
		if(isset($_SESSION['childClassUsed'])){
			$aqadClass = $_SESSION['childClassUsed'];
		}
		 ?>
    </div>
    <div id="sideBar">
        <?php include("sideBar.php") ?>
    </div>
    <div id="container">
       <?php include('referAFriendIcon.php') ?>
        <table id="childDetails">
            <td width="33%" id="sectionRemediation"><div class="smallCircle red"></div><label value="secRemediation" class="textRed pointer">Question A Day</label></td>
        </table>
		<?php if($_SESSION['childClassUsed']==1 || $_SESSION['childClassUsed']==2 || $_SESSION['childClassUsed']==10){ ?>
			<div style="font-size: 1.5em;font-weight: bold;text-align: center;margin-top: 50px;">Not available for class 1 and 2!</div>
		<?php }else{ ?>
        <div align='center' style='width: 100%'>
            <form Method="GET" name="frmDate">
                <a href='http://www.ei-india.com/asset-question-a-day-aqad/' style='float: left; padding-left: 5px;text-decoration:underline;font-size:1.3em;color:blue;' target='_blank'>What is AQAD?</a>
                <table cellpadding="3" id="generateTable" style="width: 40%;" >
                    <tr bgcolor="#e75903">
	                    <td>
	              			Questions asked
	                    </td>
	                    <td >
							Questions answered
						</td>
						<td>
							Questions answered correctly
						</td><!--
	                    <td>
	                       % Questions correct
	                    </td>-->
                    </tr>
					<tr bgcolor="#febd96">
	                    <td>
	              			<?=$noOfQuestionsServed?>
	                    </td>
	                    <td >
							<?=$noOfQuestionsAttempted?>
						</td>
						<td>
							<?=$noOfQuestionsCorrect?>
						</td>
	                    <!--<td>
	                      <?=round(($noOfQuestionsCorrect/$noOfQuestionsAttempted)*100,2)?>%
	                    </td>-->
                    </tr>
                    
                </table>
				<div style="clear:both"></div>
				<br/>
				<?php if($noOfQuestionsAttempted>0){ ?>
                <table align="left">
                    <tr>
                        <td colspan="5">
                        <div style="font-size:15px;margin-left: 160px;font-weight: bold;">Check last three questions attempted by <?=$_SESSION['childNameUsed']?></div>
                        </td>
                    </tr>
                </table>
				<div style="clear:both"></div>
				<br/>
				<?php } ?>
				<?php if($noOfQuestionsAttempted>=3){ ?>
                <div id="questionPaging">
					<div onclick="changeQuestion(1)" id="1" class="questionPaging">1</div>
					<div onclick="changeQuestion(2)" id="2" class="questionPaging">2</div>
					<div onclick="changeQuestion(3)" id="3" class="questionPaging decorated">3</div>
				</div>
				<?php } else if($noOfQuestionsAttempted==2){ ?>
					<div id="questionPaging">
						<div onclick="changeQuestion(2)" id="2" class="questionPaging">1</div>
						<div onclick="changeQuestion(3)" id="3" class="questionPaging decorated">2</div>
					</div>
				<?php }else if($noOfQuestionsAttempted==1){ ?>
					<div id="questionPaging">
						<div onclick="changeQuestion(3)" id="3" class="questionPaging decorated">1</div>
					</div>
				<?php }else if($noOfQuestionsAttempted==0){ ?>
					<div id="notAttemptedText">
						<?=$_SESSION['childNameUsed']?> has not attempted any questions till now.
					</div>
				<?php } ?>
                </form>        
            </div>
			<div style="clear:both"></div>
				<br/>
			<?php if($noOfQuestionsAttempted>0){ ?>
            <div id="aqad3">
    <?php
    include("eiaqad.cls.php");
    mysql_select_db("educatio_educat");
    echo generateAQADtemplate($questionDate[3], $aqadClass, $userAnswer[3],$_SESSION['childNameUsed']);
    ?>
            </div>
			<div id="aqad2">
    <?php
    echo generateAQADtemplate($questionDate[2], $aqadClass, $userAnswer[2],$_SESSION['childNameUsed']);
    ?>
            </div>
			<div id="aqad1">
    <?php
    echo generateAQADtemplate($questionDate[1], $aqadClass, $userAnswer[1],$_SESSION['childNameUsed']);
    mysql_select_db("educatio_adepts");
    ?>
            </div>
			<?php } ?>
			<?php } ?>
        </div>
    <?php include("footer.php") ?>