<?php
    error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
    set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
    include("header.php");
	include("../slave_connectivity.php");
    include("../userInterface/functions/orig2htm.php");
    include("../userInterface/classes/clsQuestion.php");
	include("../userInterface/functions/functionsForDynamicQues.php");
    include("../userInterface/constants.php");
    if(!isset($_SESSION['userID']))
    {
        echo "<script>window.location='logout.php'</script>";
        exit();
    }
    $userid   = $_SESSION['userID'];
    $category = $_SESSION['admin'];
    if(strcasecmp($category,"School Admin")!=0 && strcasecmp($category,"Teacher")!=0 && strcasecmp($category,"Home Center Admin")!=0)
    {
    	echo "You are not authorised to access this page!";
    	exit;
    }
    $sessionStr  = isset($_REQUEST['sessionstr'])?$_REQUEST['sessionstr']:"";
    $qno         = isset($_REQUEST['qno'])?$_REQUEST['qno']:"";



?>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/searchQuestions.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/load.js"></script>
<style type="text/css">
.column1
{
	width:100% !important;
}
.column2
{
	width:45% !important;
}
.column3
{
	width:30% !important;
}
.column4
{
	width:22% !important;
}
.singleQuestion
{
	margin-bottom:50px;
	margin-right:20px;
	float:left;
}
.groupQues
{
	margin-left:100px;
}
</style>
<script src="libs/idletimeout.js" type="text/javascript"></script>
<link rel="stylesheet" href="libs/css/jquery-ui.css" />
  <script src="libs/jquery-ui.js"></script>
  <link rel="stylesheet" href="/resources/demos/style.css" />
  <script>
  $(function() {
    $( ".datepicker" ).datepicker({ dateFormat: 'dd-mm-yy' });
  });
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
		
		document.cookie = 'SHTS=;';
		document.cookie = 'SHTSP=;';
		document.cookie = 'SHTParams=;';
	}
	
</script>
<script>
    function validate()
    {

        var sessionID = trim(document.getElementById('txtSessionID').value);
        var qno = trim(document.getElementById('txtQuesNo').value);
        if(sessionID =="")    {
            alert("Please specify a session id!");
            document.getElementById('txtSessionID').focus();
            return false;
        }else if(isNaN(sessionID)){
			 alert("Please enter Numeric value for session id!");
            document.getElementById('txtSessionID').focus();
            return false;
		}

        if(qno=="")    {
            alert("Please specify a question no.!");
            document.getElementById('txtQuesNo').focus();
            return false;
        }else if(isNaN(qno)){
			 alert("Please enter Numeric value for Question Number!");
            document.getElementById('txtQuesNo').focus();
            return false;
		}
        else{
			setTryingToUnload();
            return true;
		}

    }
    function trim(str) {
        // Strip leading and trailing white-space
        return str.replace(/^\s*|\s*$/g, "");
    }

</script>
</head>
<body class="translation" onLoad="load()" onResize="load()"  onmousemove="reset_interval()" onclick="reset_interval()" onkeypress="reset_interval()" onscroll="reset_interval()">
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
		<div id="innerContainer">
			
			<div id="containerHead">
				<div id="triangle"> </div>
				<span>Search Question</span>
			</div>
			<div id="containerBody">
			<form id="frmSearchQuestion" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
			<table id="content">
			<tr>
				<td>Session</td>
				<td>Question Number</td>
			</tr>
			<tr>
				<td>
					<input type="text" name="sessionstr" id="txtSessionID" value="<?=$sessionStr?>">
				</td>
				<td>
					<input type="text" name="qno" id="txtQuesNo" value="<?=$qno?>" size="10">
				</td>
			</tr>
			<tr>
				<td style="padding-top: 20px;padding-bottom: 30px;">
					<input type="submit" class="buttons" name="search" id="btnSearch" value="Search" onClick="return validate();">
				</td>
			</tr>
			</table>
			<hr>
			</form>
			
			<div align="center">
<?php
    if(isset($_REQUEST['search']))
    {
        /*$temp = explode("-",$sessionStr);
        $studentID = $temp[0];
        $sessionID = $temp[1];*/
        $sessionID = $sessionStr;
        if($sessionID=="")
            echo "<div align='center' style='color:#FF0000; font-weight:bold'>Invalid session ID !</div>";
        else
        {
        	$class = getSudentClass($sessionID);
            $query = "SELECT qcode, c.srno
                      FROM   ".TBL_SESSION_STATUS." b, ".TBL_QUES_ATTEMPT."_class$class c
                      WHERE  b.userID = c.userID AND b.sessionID = $sessionID AND
                             b.sessionID = c.sessionID AND c.questionNo = $qno";
			//echo $query;

            $result= mysql_query($query);
            $cnt = mysql_num_rows($result);
            if($cnt==0)
            {
            	$query = "SELECT qcode, c.srno
            		FROM ".TBL_SESSION_STATUS." b LEFT JOIN  adepts_userComments c
            		ON b.userID = c.userID AND  b.sessionID = c.sessionID AND c.questionNo = $qno
            		WHERE b.sessionID = $sessionID AND !isnull(qcode)";
            	//echo $query;
            	$result= mysql_query($query);
            	$cnt = mysql_num_rows($result);

            }
            if($cnt==0)
            {
                	echo "<div align='center' style='color:#FF0000; font-weight:bold'>No data found!</div>";
            }
            else
            {
                $line=mysql_fetch_array($result);
                $qcode = $line['qcode'];
                $quesAttempt_srno = $line['srno'];
                //showQuestion($question,$optiona, $optionb, $optionc,$optiond, $correct_answer, $question_type);
				$sql = "SELECT clusterType, groupID FROM adepts_clusterMaster a, adepts_questions b WHERE a.clusterCode=b.clusterCode AND qcode='$qcode'";
				$result = mysql_query($sql);
				$row = mysql_fetch_assoc($result);
				if($row["clusterType"] == "practice")
				{
					$groupID = $row["groupID"];
					$sql = "SELECT groupText, groupColumn FROM adepts_groupInstruction WHERE groupID='$groupID'";
					$result = mysql_query($sql);
					$row = mysql_fetch_assoc($result);
					$groupText = $row['groupText'];
					$groupColumn = $row['groupColumn'];

					echo '<div class="groupQues">';
					?>
                    <table width="100%" border="0" cellspacing="2" cellpadding="3" align="center">
                         <tr>
                          <td width="5%" align="center" valign="top" >&nbsp;</td>
                          <td valign="top" align="left"><p><span class="quesDetails"><?php echo $groupText;?></span><br></p></td>
                        </tr>
                    </table>
                    <?php
					$sql = "SELECT qcode FROM adepts_questions WHERE groupID='$groupID'";
					$result = mysql_query($sql);
					$i = 0;
					while($row = mysql_fetch_assoc($result))
					{
						echo '<div class="singleQuestion column'.$groupColumn.'">';
						$qcode = $row['qcode'];
						showQuestion($qcode, $quesAttempt_srno, $class);
						$quesAttempt_srno++;
						echo '</div>';
						$i++;
						if($i % $groupColumn == 0)
							echo '<div style="clear:both;"></div>';
					}
					echo '</div>';
				}
				else
                	showQuestion($qcode, $quesAttempt_srno, $class);

            }
        }

    }
?>
</div>
			
			</div>
			
		
		</div>
		
		
	</div>

<?php include("footer.php") ?>

<?php
function showQuestion($qcode, $srno, $class)
{
	$question = new Question($qcode);
	if($question->isDynamic())
	{
		$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE mode='normal' AND class=$class AND quesAttempt_srno= $srno";
		$result = mysql_query($query);
		$line   = mysql_fetch_array($result);
		$question->generateQuestion("answer",$line[0]);
	}
	$correct_answer = $question->getCorrectAnswerForDisplay();
?>
    <div align="center" style="width:80%">
    <!--<fieldset style="border-color: #FFFFFF;">
        <legend>Question</legend>-->
		
    <table width="100%" border="0" cellspacing="2" cellpadding="3" align="center">
         <tr>
          <td width="5%" align="center" valign="top" >&nbsp;</td>
          <td valign="top" align="left"><p><span class="quesDetails"><?php echo $question->getQuestion();?></span><br></p></td>
        </tr>
    </table>
    <br>
<?php
         if($question->quesType=='MCQ-4' || $question->quesType=='MCQ-3' || $question->quesType=='MCQ-2')
         {

?>

      <table width="100%" border="0" cellspacing="2" cellpadding="3" style="padding-bottom: 30px;">
          <?php    if($question->quesType=='MCQ-4' || $question->quesType=='MCQ-2')    {    ?>
        <tr valign="top">
          <td width="5%" align="center">&nbsp;</td>
          <td width="5%" ><b>A</b><input type="radio" name="ansRadio_<?=$srno?>" value="A" <?php if($correct_answer=='A') echo "checked"; ?> onClick="return false;"></td>
          <td width="43%" align="left"><span class="quesDetails"><?php echo $question->getOptionA();?></span></td>
          <td width="5%" ><b>B</b><input type="radio" name="ansRadio_<?=$srno?>" value="B" <?php if($correct_answer=='B') echo "checked"; ?> onClick="return false;"></td>
          <td width="42%" align="left"><span class="quesDetails"><?php echo $question->getOptionB();?></span></td>
        </tr>
        <?php    }    ?>
        <?php    if($question->quesType=='MCQ-4')    {    ?>
        <tr valign="top">
          <td width="5%" align="center">&nbsp;</td>
          <td width="5%" ><b>C</b><input type="radio" name="ansRadio_<?=$srno?>" value="C" <?php if($correct_answer=='C') echo "checked"; ?> onClick="return false;"></td>
          <td width="43%"  align="left"><span class="quesDetails"><?php echo $question->getOptionC();?></span></td>
          <td width="5%" ><b>D</b><input type="radio" name="ansRadio_<?=$srno?>" value="D" <?php if($correct_answer=='D') echo "checked"; ?> onClick="return false;"></td>
          <td width="42%" align="left"><span class="quesDetails"><?php echo $question->getOptionD();?></span></td>
        </tr>
        <?php    }    ?>
        <?php    if($question->quesType=='MCQ-3')    {    ?>
        <tr valign="top">
          <td width="5%" align="center">&nbsp;</td>
          <td width="5%" nowrap><b>A</b><input type="radio" name="ansRadio_<?=$srno?>" value="A"  <?php if($correct_answer=='A') echo "checked"; ?> onClick="return false;"></td>
          <td width="28%" align="left"><span class="quesDetails"><?php echo $question->getOptionA();?></span></td>
          <td width="3%" nowrap><b>B</b><input type="radio" name="ansRadio_<?=$srno?>" value="B" <?php if($correct_answer=='B') echo "checked"; ?> onClick="return false;"></td>
          <td width="28%" align="left"><span class="quesDetails"><?php echo $question->getOptionB();?></span></td>
          <td width="3%" nowrap><b>C</b><input type="radio" name="ansRadio_<?=$srno?>" value="C" <?php if($correct_answer=='C') echo "checked"; ?> onClick="return false;"></td>
          <td width="28%" align="left"><span class="quesDetails"><?php echo $question->getOptionC();?></span></td>
        </tr>
        <?php    }    ?>
      </table>
     <?php
        }
        elseif($correct_answer!="")
        	echo "<span class='quesDetails'><br>Correct answer : ".$correct_answer."</span>";
?>
    <!--</fieldset>-->
    </div>
<?php
}

function getSudentClass($sessionID)
{
	$query  = "SELECT childClass FROM ".TBL_SESSION_STATUS." a, adepts_userDetails b WHERE a.userID=b.userID AND sessionID=$sessionID";
	$result = mysql_query($query) or die("Error in fetching student class!");
	$line   = mysql_fetch_array($result);
	return $line[0];
}

?>

