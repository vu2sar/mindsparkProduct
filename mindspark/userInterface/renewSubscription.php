<?php
@include("check1.php");
include("classes/clsUser.php");
require_once 'constants.php';
if(!isset($_REQUEST['userID']) && $_REQUEST['action']!='thankyou')
{
    header("Location: index.php");
    exit;
}
//print_r($_REQUEST);
//error_reporting(E_ALL);
$feedbackset = 32;
if (!isset($_REQUEST['action'])) {
    $parentEmail = $_POST['parentEmail'];
    $feedbackType = "";
    $query = "SELECT qid FROM adepts_feedbackSet WHERE setno=$feedbackset";
    $result = mysql_query($query);
    $line = mysql_fetch_array($result);
    $qids = $line[0];
    $arrQues = array();
    $userID = $_POST['userID'];

    $query = "SELECT count(*) FROM renewalReminderLog WHERE type='N' AND userID='$userID'";
    $result = mysql_query($query);
    $line = mysql_fetch_array($result);

    $mailSent = 0;
    if ($line[0] > 0)
        $mailSent = 1;
}
elseif ($_REQUEST['action'] == 'saveData') {
    $userID = $_REQUEST['userID'];
    $feedbackset = $_REQUEST['feedbackset'];
    $feedbackType = $_REQUEST['feedbacktype'];

    $questionids = $_REQUEST['qid'];
    $maxques = $_POST['maxques'];
    $mailSent = $_POST['mailSent'];
    $qids = explode(",", $questionids);
    $query = "INSERT INTO adepts_feedbackresponse (userID, qid, response, feedbackset, type, feedbackdate) VALUES ";
    $isFeedbackPositive = true;
    $feedbackForParent = '';
    //$msg = "<table border='1'>";
    $feedbackgiven = false;
    for ($qno = 1; $qno <= $maxques; $qno++) {
        $qid = $qids[$qno - 1];
        $ans = $_POST['ans' . $qno];
        if (is_array($ans)) { //For Checkbox type questions...
            $ans = implode(",", $ans);
        }
        if (isset($_POST['ansComment' . $qno]) && $_POST['ansComment' . $qno] != "") {
            $ans = $_POST['ansComment' . $qno];
        }
        if ($ans != "") {
            $feedbackgiven = true;
            $ansVal = explode(' ', $ans);
            if ($ansVal < 3 && $qno < 3)
                $isFeedbackPositive = false;
            $query.= "(" . $userID . "," . $qid . ",'" . $ans . "'," . $feedbackset . ",'" . $feedbackType . "',now()),";
            if ($qno < 3)
                $feedbackForParent .= $arrQues[$qid][0] . " - $ans/5<br/>";
        }
    }
    $query = substr($query, 0, -1);

    if ($feedbackgiven) {
        mysql_query($query) or die(mysql_error());
    } else
        $isFeedbackPositive = false;
    $emailAddress = $_POST['parentEmail'];
//    $emailAddress = 'ruchit.rami@ei-india.com';
    $mode = ($mailSent == 0 ? 'notify' : 'remind');
    if ($emailAddress != '') {
        if ($isFeedbackPositive == true)
            sendSubscriptionMail($userID, $emailAddress, $mode);
        else
            sendSubscriptionMail($userID, $emailAddress, $mode);
    }
} elseif ($_REQUEST['action'] == 'sendEmail') {
    $userID = $_POST['userID'];

    $emailAddress = $_POST['parentEmail'];
//    $emailAddress = 'ruchit.rami@ei-india.com';
    $feedback = $_POST['feedback'];
    $positiveComments = $_POST['positiveFeedback'];
    $topics = $_POST['topics'];
    $mode = $_POST['mode'];
    if ($emailAddress != '')
        sendSubscriptionMail($userID, $emailAddress, $mode);
    exit;
}

function sendSubscriptionMail($userID, $emailAddress, $mode) {
    $user = new User($userID);

    if (isset($mode) && $mode == 'notify')
        sendSubscriptionRenewMail($userID, $user->childName, $emailAddress);
    elseif (isset($mode) && $mode == 'remind')
        sendSubscriptionRenewMailReminder($userID, $user->childName, $emailAddress);

    if (trim($user->parentEmail) == '') {
        $user->updateParentEmail($parentEmail);
        $user = new User($userID);
    }
}

$user1 = new User($userID);
$childClass = $user1->childClass;
?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
        <link rel="stylesheet" href="css/commonMidClass.css" />
        <link rel="stylesheet" href="css/feedbackForm/midClass.css" />
        <script type="text/javascript" src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
		<link rel="stylesheet" type="text/css" href="css/colorbox.css">
		<link href="css/home/prompt1.css?ver=4" rel="stylesheet" type="text/css">
		<script src="libs/jquery.colorbox-min.js" type="text/javascript"></script>
        <title>Renew subscription</title>
        <style>
            html,body{
                height: 100%;
            }
            #feedbackInfo{
                float: none;
            }
			#userResponse{
				display: none;
			}
			.submitButtonAQAD{
			    margin-left: inherit !important;
			}

.submitButtonAQAD {
	-moz-box-shadow:inset 0px 1px 0px 0px #c1ed9c;
	-webkit-box-shadow:inset 0px 1px 0px 0px #c1ed9c;
	box-shadow:inset 0px 1px 0px 0px #c1ed9c;
	margin-left: 40%;
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #9dce2c), color-stop(1, #8cb82b) );
	background:-moz-linear-gradient( center top, #9dce2c 5%, #8cb82b 100% );
	background-color:#9dce2c;
	-webkit-border-top-left-radius:20px;
	-moz-border-radius-topleft:20px;
	border-top-left-radius:20px;
	-webkit-border-top-right-radius:20px;
	-moz-border-radius-topright:20px;
	border-top-right-radius:20px;
	-webkit-border-bottom-right-radius:20px;
	-moz-border-radius-bottomright:20px;
	border-bottom-right-radius:20px;
	-webkit-border-bottom-left-radius:20px;
	-moz-border-radius-bottomleft:20px;
	border-bottom-left-radius:20px;
	text-indent:0;
	border:1px solid #83c41a;
	display:inline-block;
	color:#ffffff;
	font-family:Arial;
	font-size:15px;
	font-weight:bold;
	font-style:normal;
	height:40px;
	line-height:40px;
	width:100px;
	text-decoration:none;
	text-align:center;
	text-shadow:1px 1px 0px #689324;
	cursor:pointer;
}
.submitButtonAQAD:hover {
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #8cb82b), color-stop(1, #9dce2c) );
	background:-moz-linear-gradient( center top, #8cb82b 5%, #9dce2c 100% );
	background-color:#8cb82b;
}.submitButtonAQAD:active {
	position:relative;
	top:1px;
}
#buyMindSparkLink{
	float: right;
margin-right: 20px;
margin-top: 20px;
width: 115px;
height: 40px;
line-height: 40px;
/* border: 1px solid black; */
font-size: 1em;
text-decoration: none;
color: white;
font-weight: bold;
text-align: center;
font-family: Arial;
color: rgb(221, 86, 60);
margin-bottom: 10px;
background-color: #FFC867;
}
            #blue {
                background:#2f99cb;
                height:55%;
                /*float:left;*/
            }

            #green {
                background:#9ec956;
                height:15%;
                /*float:left;*/
            }

            #orange {
                background:#e75903;
                height:15%;
                /*float:left;*/
            }

            #yellow {
                background:#fbd212;
                height:15%;
                /*float:left;*/
            }
            .bars{
                width: 10px;
                border-top: 1px solid white;
            }
            #message{
                margin-left: 20px;
                width: 90%;
                text-align: center;
                font-size: 200%;

            }
			.day{
				float: left;
				margin-left: 10px;
				margin-right: 10px;
				text-decoration:underline;
			}
            #message,#feedbackFormMain{
                margin-left: 20px;
                width: 90%;
                text-align: center;
                font-size: 150%;

            }
            #parentEmail{

                width: 30%;
                border-radius: 10px;
                border: 1px solid #2f99cb;
                padding: 5px;
            }
            .button2 {
                display: inline-block;
                zoom: 1;
                vertical-align: baseline;
                margin: 0 2px;
                outline: none;
                cursor: pointer;
                text-align: center;
                text-decoration: none;
                font: 14px/100% Arial, Helvetica, sans-serif;
                padding: .5em 2em .55em;
                text-shadow: 0 1px 1px rgba(0,0,0,.3);
                -webkit-border-radius: .5em;
                -moz-border-radius: .5em;
                border-radius: .5em;
                -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.2);
                -moz-box-shadow: 0 1px 2px rgba(0,0,0,.2);
                box-shadow: 0 1px 2px rgba(0,0,0,.2);
            }
			#aqadButton{
				float: right;
				font-size:1.4em;
				padding: 12px;
				background-color: #e75903;
				color: white;
				cursor: pointer;
			}          
            .orange {
                color: #fef4e9;
                border: solid 1px #da7c0c;
                background: #f78d1d;
                background: -webkit-gradient(linear, left top, left bottom, from(#faa51a), to(#f47a20));
                background: -moz-linear-gradient(top, #faa51a, #f47a20);
                filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#faa51a', endColorstr='#f47a20');
            }
            .button2:hover {
                text-decoration: none;
            }
            .orange:hover {
                background: #f47c20;
                background: -webkit-gradient(linear, left top, left bottom, from(#f88e11), to(#f06015));
                background: -moz-linear-gradient(top, #f88e11, #f06015);
                filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#f88e11', endColorstr='#f06015');
            }

        </style>
        <script>
            //alert();
            function sendAQADResponse(qcode,correct){
	if (document.getElementById('option1').checked) {
	  var userAnswer = document.getElementById('option1').value;
	}else if (document.getElementById('option2').checked) {
	  var userAnswer = document.getElementById('option2').value;
	}else if (document.getElementById('option3').checked) {
	  var userAnswer = document.getElementById('option3').value;
	}else if (document.getElementById('option4').checked) {
	  var userAnswer = document.getElementById('option4').value;
	}else{
		alert("Please select an option!");
		return true;
	}
	$('.submitButtonAQAD').attr('onclick','');
	var paperCode = document.getElementById('papercode').innerHTML;
	if(correct==1){
		correct = 'A';
	}else if(correct==2){
		correct = 'B';
	}else if(correct==3){
		correct = 'C';
	}else if(correct==4){
		correct = 'D';
	}
	if(correct==userAnswer){
		var score=1;
	}else{
		var score=0;
	}
	var userID =<?php echo json_encode($userID); ?>;
	var explaination = document.getElementById('aqadExplaination').value;
	var aqadDate = $("#aqadDate").val();
	$.post("aqadResponse.php","studentID="+userID+"&paperCode="+paperCode+"&product='MS'&qcode="+qcode+"&student_answer="+userAnswer+"&score="+score+"&explaination="+encodeURIComponent(explaination)+"&class=<?php echo $childClass; ?>&aqadDate="+aqadDate,function(data){
		if(data)
		{
			$(".submitButtonAQAD").css("display","none");
			$("#divExplain").css("display","none");
                        if(explaination!='')
                        {
			$("#userResponse").html("<b>Your Answer: "+userAnswer+"</b><br/><br/><div><b>Your Explanation:</b>"+explaination+"<br/><br/>Come back tomorrow to see the correct answer</b></div>");
                    }
                    else
                    {
                        $("#userResponse").html("<b>Your Answer: "+userAnswer+"<br/><br/>Come back tomorrow to see the correct answer</b></div>");
                    }
//			$("#userResponse").css("display","block");
			$("#userResponse").show();
			$("#aqadIcon").removeClass('aqadBlink');
			$(".aqad").removeClass('aqadBlink');
			$(".redCircleAqad").html("0");
		}else{
			alert("Please answer again!");
		}
	});
	var datatoPass ="mode=ReloadAQADAfterUserAnswer&class=<?php echo $childClass; ?>&student_answer="+userAnswer+"&studentID="+userID;
	$.ajax({
		  url: "../userInterface/commonAjax.php",
		  data : datatoPass ,
		  cache: false,
		  type: "POST",
		  success: function(html){
		    $("#common-aqad-div").html(html);
		  }
		});
}
            function validate(maxques) {
                for (var i = 1; i < maxques + 1; i++)
                {
                    if (document.getElementsByName('ans' + i) && document.getElementsByName('ans' + i).length > 0)
                    {
                        var obj = eval("document.feedbackform.ans" + i);
                        var val = getCheckedValue(obj);
                        if (trim(val) == "")
                        {
                            /*if(i+1==maxques)
                             alert("Please enter some text.");
                             else*/
                            alert("Please select any one of the option for question-" + i);
                            return false;
                        }
                    }
                    document.getElementById('action').value = 'saveData';
                    document.getElementById('hdnParentEmail').value = document.getElementById('parentEmail').value;
                    /*else if($("input[type='checkbox'][name^='ans"+i+"']").length > 0) //Check box validation..
                     {
                     if($("input[type='checkbox'][name^='ans"+i+"']:checked").length == 0)
                     {
                     alert("Please select atleast one of the option for question-"+i);
                     return false;
                     }
                     }*/
                }
            }
            function getCheckedValue(radioObj) {
                var elemType = radioObj.type;
                if (elemType == "select-one" || elemType == "textarea" || elemType == "text")	//implies not a radio button, treat currently as text field
                    return radioObj.value;

                var radioLength = radioObj.length;
                for (var i = 0; i < radioLength; i++) {
                    if (radioObj[i].checked) {
                        return radioObj[i].value;
                    }
                }
                return "";
            }
            function trim(str) {
                // Strip leading and trailing white-space
                return str.replace(/^\s*|\s*$/g, "");
            }
            function sendEmail(mode) {
                //				alert(mode)
                //				imode=='notify' or 'remind'
                var parentEmail = document.getElementById('parentEmail').value;
                if (parentEmail == '' || echeck(parentEmail) == false)
                {
                    alert('Please provide valid parent email address.');
                    return false;
                }
                if ($('#sendEmail').text() == 'Remind them!')
                                        $('#sendEmail').text('Reminded!');
                                    if ($('#sendEmail').text() == 'Notify them!')
                                        $('#sendEmail').text('Notified!');
                                    $('#sendEmail').attr('disabled', 'disabled');
                                    $('#parentEmail').attr('disabled', 'disabled');
                                    $('#sendEmail').css({opacity: 0.5})
                var params = "action=sendEmail&mode=" + mode + "&userID=<?= $userID ?>&parentEmail="+parentEmail+"&positiveFeedback=<?= $positFeedback ?>";
                try {
                    var request = $.ajax('renewSubscription.php',
                            {
                                type: 'post',
                                data: params,
                                success: function(transport) {
                                    resp = transport;
//                                    alert(resp);        
                                    alert('Email has been sent!');
                                    window.location='renewSubscription.php?action=thankyou';                                   
                                }
                            }
                    );
                }
                catch (err) {
                    alert("sendMail " + err.description);
                }
            }
            function echeck(str) {
                var at = "@";
                var dot = ".";
                var lat = str.indexOf(at);
                var lstr = str.length;
                var ldot = str.indexOf(dot);
                if (str.indexOf(at) == -1) {
                    //alert("Invalid e-mail");
                    return false;
                }
                if (str.indexOf(at) == -1 || str.indexOf(at) == 0 || str.indexOf(at) == lstr) {
                    //alert("Invalid e-mail");
                    return false;
                }
                if (str.indexOf(dot) == -1 || str.indexOf(dot) == 0 || str.indexOf(dot) == lstr) {
                    //alert("Invalid e-mail");
                    return false;
                }
                if (str.indexOf(at, (lat + 1)) != -1) {
                    //alert("Invalid e-mail");
                    return false;
                }
                if (str.substring(lat - 1, lat) == dot || str.substring(lat + 1, lat + 2) == dot) {
                    //alert("Invalid e-mail");
                    return false;
                }
                if (str.indexOf(dot, (lat + 2)) == -1) {
                    //alert("Invalid e-mail");
                    return false;
                }
                if (str.indexOf(" ") != -1) {
                    //alert("Invalid e-mail");
                    return false;
                }
                return true;
            }
			function showAQAD(){
				$.fn.colorbox({'href':'#aqadContainer','inline':true,'open':true,'escKey':true, 'height':600, 'width':800});
			}
			function goToByScroll(id){
      // Remove "link" from the ID
	 $("#11").css("display","none");
	  $("#22").css("display","none");
	   $("#33").css("display","none");
    $("#"+id).css("display","block");
}
        </script>
    </head>
    <body>
        <img  src="assets/renewSubscription/activities.png" style="margin-left: 41%"/>
        <div id="header">
            <div id="eiColors" style="height: 100%;position: fixed;left: 0px;top:0px;">
                <div id="orange" class="bars"></div>
                <div id="yellow" class="bars"></div>
                <div id="green" class="bars"></div>
                <div id="blue" class="bars"></div>
            </div>
        </div>
		
        <?php
        if (!isset($_REQUEST['action']) && !($_REQUEST['action']=='thankyou')) {
            ?>
			<?php if($childClass!=1 && $childClass!=2 && $childClass!=10){ ?>
			<div id="aqadButton" onclick="showAQAD()">Continue to AQAD</div>
			<?php } ?>
            <div id="message">
                <p>Oh no! Your subscription period has ended! Team Mindspark really wants you back. </p>
				
                <?php
                if ($_POST['category'] == 'School') {
                    ?>
                    <p>Why don't you and your friends ask your teachers to renew your subscription for the year?</p>
                <?php } ?>
                <?php
                if ($_POST['category'] == 'Individual') {
                    ?>
                    <?php if ($mailSent) { ?>
                        <p>Your parents haven't renewed your subscription yet!  Would you like to remind them again?</p>
                    <?php } elseif ($mailSent == 0) {
                        ?>
                        <p>Why don't you ask your parents to renew your subscription? We'll send them a mail from your side!</p>
                    <?php } ?>
                    
                    <input type="email" id="parentEmail" value="<?= $parentEmail ?>" placeholder="<?php if($parentEmail=='') { echo 'abc@example.com';} ?>"/>
                    <?php if ($mailSent) { ?>
                        <button class="button2 orange" id="sendEmail" onclick="sendEmail('remind')">Remind them!</button>
                    <?php } elseif ($mailSent == 0) {
                        ?>
                        <button class="button2 orange" id="sendEmail" onclick="sendEmail('notify')">Notify them!</button>
                    <?php } ?>
                <?php } ?>
            </div>
            <br><br>
            <?php if (!isFeedBackGiven($userID, $feedbackset) && !($_REQUEST['action']=='thankyou')) { ?>
                <div id="feedbackFormMain">
                    <div id="feedbackInfo" >In the meantime,  why don't you tell us about your experience with Mindspark?</div>
                    <form name="feedbackform" id="frmFeedback" method="POST" >
                        <?php
                        $query = "SELECT * FROM adepts_feedbackquestions WHERE qid in ($qids)";
                        $result = mysql_query($query);
                        $qno = 1;

                        while ($line = mysql_fetch_array($result)) {
                            $arrQues[$line['qid']][0] = $line['question'];
                            $arrQues[$line['qid']][1] = $line['question_type'];
                            $arrQues[$line['qid']][2] = $line['optiona'];
                            $arrQues[$line['qid']][3] = $line['optionb'];
                            $arrQues[$line['qid']][4] = $line['optionc'];
                            $arrQues[$line['qid']][5] = $line['optiond'];
                            $arrQues[$line['qid']][6] = $line['optione'];
                            $arrQues[$line['qid']][7] = $line['optionf'];
                            $arrQues[$line['qid']][8] = $line['comments'];
                        }
                        $arrQuestionOrder = explode(",", $qids);
                        for ($i = 0; $i < count($arrQuestionOrder); $i++) {
                            ?><br>
                            <div id="pnlQues<?= $i + 1 ?>">
                                <div class="quesText2"><?= $arrQues[$arrQuestionOrder[$i]][0] ?></div>
                                <div class="clear"></div>
                                <?php if (strpos($arrQues[$arrQuestionOrder[$i]][1], "MCQ") !== false || strpos($arrQues[$arrQuestionOrder[$i]][1], "CHECK") !== false) { ?>
                                    <div class="quesOpt radioGroup<?= $i + 1 ?>" id="quesOpt<?= $i + 1 ?>">
                                        <?php
                                        if (strpos($arrQues[$arrQuestionOrder[$i]][1], "MCQ") !== false) {
                                            $type = "radio";
                                            $nameExt = "";
                                        } else if (strpos($arrQues[$arrQuestionOrder[$i]][1], "CHECK") !== false) {
                                            $type = "checkbox";
                                            $nameExt = "[]";
                                        }
                                        $totalOptions = explode("-", $arrQues[$arrQuestionOrder[$i]][1]);
                                        for ($o = 1; $o <= $totalOptions[1]; $o++) {
                                            ?>

                                            <label class="setOption radioGroup<?= $i + 1 ?>" id="setOption<?= ($i + 1) . $o ?>">
                                                <span class="ques2opt<?= $o ?>">
                                                    <input type="<?= $type ?>" name="ans<?= $qno . $nameExt ?>" id="ques1opt1<?= $o ?>" value="<?= $arrQues[$arrQuestionOrder[$i]][$o + 1] ?>">
                                                    <!--<div class="optTextQ2">--><?= $arrQues[$arrQuestionOrder[$i]][$o + 1] ?><!--</div>-->
                                                </span>
                                            </label>
                                        <?php } ?>
                                        <div class="clear"></div>
                                    </div>
                                    <?php
                                    $qno++;
                                } elseif ($arrQues[$arrQuestionOrder[$i]][1] == "blank") {
                                    ?>
                                    <div class="quesOpt">
                                        <textarea id="ques3Textarea" name="ansComment<?= $qno ?>" placeholder="Enter text here"></textarea>
                                    </div>
                                    <?php
                                    $qno++;
                                }
                                ?>
                            </div>
                        <?php } ?>
                        <div id="submitButtonDiv1" align="center">
                            <input type="submit" id="btnSubmit1" class='button1' name ="submit" value="Submit" onClick="return validate(<?= $qno ?>)">

                        </div>
                        <input type="hidden" id="action" name="action" value=""/>
                        <input type="hidden" name="maxques" value="<?= count($arrQuestionOrder) ?>">
                        <input type="hidden" name="feedbackset" value="<?= $feedbackset ?>">
                        <input type="hidden" name="feedbacktype" value="<?= $feedbackType ?>">
                        <input type="hidden" name="qid" value="<?= $qids ?>">            
                        <input type="hidden" name="userID" value="<?= $userID ?>">   
                        <input type="hidden" name="parentEmail" id="hdnParentEmail" value="">   
                        <input type="hidden" name="mailSent" id="mailSent" value="<?= $mailSent ?>">   
                        
                        </div>
                    <?php } ?>

                    <?php
                } elseif ($_REQUEST['action'] == 'saveData' || $_REQUEST['action']=='thankyou') {
                    ?>
                    <div id="message">
                        <p>See you soon!</p>


                    </div>
                    <script>
                        document.getElementById('sparkie').src = 'assets/renewSubscription/activities2.png';
                    </script>
                <?php } ?>
                <?php

                function isFeedBackGiven($userid, $feedbackset) {
                    $query = "SELECT count(qid) FROM adepts_feedbackresponse WHERE  userID=$userid AND type='' AND feedbackset=" . $feedbackset;
                    $result = mysql_query($query);
                    $line = mysql_fetch_array($result);
                    if ($line[0] > 0)
                        return true;
                    else
                        return false;
                }

                function formatName($name) {
                    $nameArray1 = explode(' ', $name);
                    $nameArray = array_map('ucfirst', $nameArray1);
                    $name = implode(' ', $nameArray);
                    return $name;
                }

                function sendSubscriptionRenewMail($userID, $childName, $emailAddress) {
                    $positiveComments = getPositiveComments($userID);
                    $feedback = getFeedbackResponse($userID);
                    $subject = "Mindspark Subscription Reminder!";
                    $headers = "From:<mindspark@ei-india.com>\r\n";
                    $headers .= "Bcc:mindspark@ei-india.com\r\n";
                    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
                    $body = "Dear Parent,<br/><br/>";
                    $body .= formatName($childName) . " has requested you to renew his/her Mindspark subscription. <br/><a href='https://www.mindspark.in/registration.php?userID=$userID' target='_blank'>Renew</a> the subscription today!<br/><br/>";
                    if ($feedback != "") {
                        $body .= "<b>Here's what your child had to say about his/her Mindspark experience:</b><br/>";
                        $body .= $feedback;
                        $body .="<br/><br/>";
                    }
                    if ($positiveComments != "") {
                        $body .= "<b>Here are some of the comments your child sent us over the last few months:</b><br/>";
                        $body .= "Looks like " . formatName($childName) . " really enjoyed doing Mindspark!<br/>";
                        $body .= $positiveComments;
                        $body .="<br/><br/>";
                    }
                    $body .= "Write to us at mindspark@ei-india.com if you have any queries.<br/>";
                    $body .= "Regards,<br/>";
                    $body .= "The Mindspark Team<br/>";
                    $body = wordwrap($body, 70);
//                    echo $body;
                    if ($emailAddress == "") {
                        // do not send mail
                    } else {
                        $success = mail($emailAddress, $subject, $body, $headers);
                    }
                    if ($success) {
                        insert(12, $emailAddress, "", "mindspark@ei-india.com", "mindspark@ei-india.com", "", 1);
                        addReminderMailLog($userID, $emailAddress, 'N', 'Active');
                    } else {
                        insert(12, $emailAddress, "", "mindspark@ei-india.com", "mindspark@ei-india.com", "", 0);
                        addReminderMailLog($userID, $emailAddress, 'N', 'Active');
                    }
                }

                function sendSubscriptionRenewMailReminder($userID, $childName, $emailAddress) {
                    $topics = getTopicsForMail($userID);
                    $feedback = getFeedbackResponse($userID);
                    $subject = "Mindspark Subscription Reminder!";
                    $headers = "From:<mindspark@ei-india.com>\r\n";
                    $headers .= "Bcc:mindspark@ei-india.com\r\n";
                    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
                    $body = "Dear Parent,<br/><br/>";
                    $body .= formatName($childName) . " has sent you a reminder to renew his/her Mindspark subscription. <br/><a href='https://www.mindspark.in/registration.php?userID=$userID' target='_blank'>Renew</a> the subscription today!<br/><br/>";
                    if ($feedback != "") {
                        $body .= "<b>Here's what your child had to say about his/her Mindspark experience:</b><br/>";
                        $body .= $feedback;
                        $body .="<br/><br/>";
                    }
                    if ($topics != "") {
                        $body .= "<b>Here are some Topics your child enjoyed doing in Mindspark:</b><br/>";
                        $body .= $topics;
                        $body .="<br/><br/>";
                    }
                    $body .= "Write to us at mindspark@ei-india.com if you have any queries.<br/>";
                    $body .= "Regards,<br/>";
                    $body .= "The Mindspark Team<br/>";
                    $body = wordwrap($body, 70);
//                    echo $body;
                    if ($emailAddress == "") {
                        // do not send mail
                    } else {
                        $success = mail($emailAddress, $subject, $body, $headers);
                    }

                    if ($success) {
                        insert(13, $emailAddress, "", "mindspark@ei-india.com", "mindspark@ei-india.com", "", 1);
                        addReminderMailLog($userID, $emailAddress, 'R', 'Active');
                    } else {
                        insert(13, $emailAddress, "", "mindspark@ei-india.com", "mindspark@ei-india.com", "", 0);
                        addReminderMailLog($userID, $emailAddress, 'R', 'Active');
                    }
                }

                function addReminderMailLog($userID, $parentEmail, $type, $status) {
                    $time_stamp = time();
                    $query = " insert into renewalReminderLog set userID = $userID, parentEmail='$parentEmail', type='$type', dateReminderSent=now(), status='$status' ";

                    mysql_query($query) or die(mysql_error());
                }

                function getTopicsForMail($userID) {
                    $topics = '';
                    $query = "SELECT teacherTopicDesc FROM adepts_teacherTopicStatus s,adepts_teacherTopicMaster m WHERE s.teacherTopicCode=m.teacherTopicCode AND userID=$userID AND progress=100.00 order by rand() limit 3;";
                    $result = mysql_query($query);
                    if (mysql_num_rows($result) > 3) {
                        while ($line = mysql_fetch_array($result))
                            $topics.=$line[0] . "<BR>";
                    }
                    return $topics;
                }

                function getPositiveComments($userID) {
                    $positFeedback = '';
                    $positFeedbackQuery = "select comment from adepts_userComments where category='Positive Feedback' and comment is not null and userID=$userID limit 3";
//                    echo $positFeedbackQuery;
                    $result = mysql_query($positFeedbackQuery);
                    while ($line = mysql_fetch_array($result))
                        $positFeedback.='"' . $line[0] . '"' . "<BR>";
                    if ($positFeedback == '')
                        $positFeedback = 0;
                    return $positFeedback;
                }

                function getFeedbackResponse($userID) {
                    $query = "SELECT qid FROM adepts_feedbackSet WHERE setno=32";
                    $result = mysql_query($query);
                    $line = mysql_fetch_array($result);
                    $qids = $line[0];
                    $positFeedback = '';
                    $positFeedbackQuery = "SELECT question,response FROM adepts_feedbackresponse r,adepts_feedbackquestions q WHERE r.qid=q.qid AND userID=$userID AND r.qid in ($qids)";
                    $result = mysql_query($positFeedbackQuery);
                    $qno = 0;
                    $isFeedbackPositive = true;
                    $feedbackForParent = '';
                    $feedbackgiven = false;
                    while ($line = mysql_fetch_array($result)) {
                        $qno +=1;
                        $ans = $line[1];
                        if ($ans != "") {
                            $feedbackgiven = true;
                            $ansVal = explode(' ', $ans);
                            if ($ansVal[0] < 3 && $qno < 3)
                                $isFeedbackPositive = false;
                            if ($qno < 3)
                                $feedbackForParent .= $line[0] . " - " . $ansVal[0] . "/5<br/>";
                        }
                    }
                    if ($isFeedbackPositive == true)
                        return $feedbackForParent;
                    else
                        return '';
                }

                function insert($type, $to, $cc, $bcc, $from, $reply_to, $success) {

                    $time_stamp = time();
                    $query = " insert into adepts_mail set ";
                    $query.= " type = '" . $type . "', ";
                    $query.= " sent_to = '" . $to . "', ";
                    $query.= " sent_cc = '" . $cc . "', ";
                    $query.= " sent_bcc = '" . $bcc . "', ";
                    $query.= " sender = '" . $from . "', ";
                    $query.= " reply_to = '" . $reply_to . "', ";
                    $query.= " success = '" . $success . "' ";

                    //echo $query;
                    @mysql_query($query) or die(mysql_error());
                }
                ?>
                </body>

                </html>
<?php include("commonAQAD.php"); ?>
