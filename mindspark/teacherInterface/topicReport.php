<?php
  error_reporting(E_ALL & ~E_DEPRECATED);
  set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
  include("../userInterface/constants.php");
  include("header.php");
  include("../slave_connectivity.php");
  mysql_select_db("educatio_adepts") or die (mysql_errno());
  
  $userID     = $_SESSION['userID'];
  $schoolCode = $_SESSION['schoolCode'];
  $schoolCodeArray = array();
  $coteacherInterfaceFlag = 0;
  $query  = "SELECT childName, startDate, endDate, category, subcategory FROM adepts_userDetails WHERE userID=".$userID;
  $result = mysql_query($query) or die(mysql_error());
  $line   = mysql_fetch_array($result);
  $userName   = $line[0];
  $startDate  = $line[1];
  $endDate    = $line[2];
  $category   = $line[3];
  $subcategory = $line[4];

  if(strcasecmp($category,"School Admin")!=0 && strcasecmp($category,"Teacher")!=0 && strcasecmp($category,"Home Center Admin")!=0)
  {
    echo "You are not authorised to access this page!";
    exit;
  }

  if(strcasecmp($category,"School Admin")==0)
  {
    $query  = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
               FROM     adepts_userDetails
               WHERE    schoolCode=$schoolCode AND category='STUDENT' AND subcategory='School' AND enabled=1 AND endDate>=curdate() AND subjects like '%".SUBJECTNO."%'
               GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
  }
  elseif (strcasecmp($category,"Teacher")==0)
  {
    $query = "SELECT   class, group_concat(distinct section ORDER BY section)
          FROM     adepts_teacherClassMapping
          WHERE    userID = $userID AND subjectno=".SUBJECTNO."
          GROUP BY class ORDER BY class, section";
  }
  elseif (strcasecmp($category,"Home Center Admin")==0)
  {
    $query  = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
               FROM     adepts_userDetails
               WHERE    category='STUDENT' AND subcategory='Home Center' AND schoolCode=$schoolCode AND endDate>=curdate() AND enabled=1 AND  subjects like '%".SUBJECTNO."%'
               GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
  }
  $classArray = $sectionArray = array();
  $hasSections = true;  // for the task 11269
  $result = mysql_query($query) or die(mysql_error());
  
  while($line=mysql_fetch_array($result))
  {
    array_push($classArray, $line[0]);
    // if($line[1]!='')             // for the task 11269
    //   $hasSections = true;
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


  $oneDay = 24*60*60;
  $lastWeek = date("d-m-Y",strtotime("-1 day"));
  
  $class    = isset($_REQUEST['class'])?$_REQUEST['class']:"";
  $section  = isset($_REQUEST['section'])?$_REQUEST['section']:"";
  $tillDate = isset($_REQUEST['tillDate'])?$_REQUEST['tillDate']:date("d-m-Y",strtotime("-1 day"));
  
  $fromDate = isset($_REQUEST['fromDate'])?$_REQUEST['fromDate']:$lastWeek;
  $chkTopicsAttempted = isset($_POST['chkTopicsAttempted'])?1:0;
 
  $query  = "SELECT schoolCode from adepts_rewardSystemPilot where flag=2";
  $result = mysql_query($query) or die(mysql_error());
  while($line   = mysql_fetch_array($result))
  {
      $schoolCodeArray[] =$line[0];
  }
  if(in_array($schoolCode,  $schoolCodeArray) || empty($schoolCodeArray))
  {          
    $coteacherInterfaceFlag = 1;
  } 

?>

<title>Topic Report</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css?ver=1" rel="stylesheet" type="text/css">
<link href="css/myStudents.css?ver=7" rel="stylesheet" type="text/css">
<link href="css/topicReport.css?ver=2" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="css/myClasses.css" />
<link rel="stylesheet" href="css/dashboard.css" />

<!-- <script src="libs/jquery-1.9.1.js"></script> -->
<!-- <link rel="stylesheet" href="css/jquery-ui.css" /> -->
<link rel="stylesheet" href="css/introjs.css" />

<!-- <script src="libs/jquery-ui.js"></script> -->
<!-- <script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script> -->
<script type="text/javascript" src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo HTML5_COMMON_LIB; ?>/jquery-ui-1.10.1.custom.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script src="libs/idletimeout.js" type="text/javascript"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script type="text/javascript" src="libs/intro.js"></script>

<script>
  var langType = '<?=$language;?>';
  function load(){
    var fixedSideBarHeight = window.innerHeight;
    var sideBarHeight = window.innerHeight-95;
    var containerHeight = window.innerHeight-115;
    $("#fixedSideBar").css("height",fixedSideBarHeight+"px");
    $("#sideBar").css("height",sideBarHeight+"px");
    $("#classes").css("font-size","1.4em");
    $("#classes").css("margin-left","40px");
    $(".arrow-right").css("margin-left","10px");
    $(".rectangle-right").css("display","block");
    $(".arrow-right").css("margin-top","3px");
    $(".rectangle-right").css("margin-top","3px");
  }
</script>
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
              /*$(".noSection").show();*/
              $(".noSection").css("visibility","visible");
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
          /*$(".noSection").hide();*/
          $(".noSection").css("visibility","hidden");          
        }
        }
    }
  }

  function removeAllOptions(selectbox)
  {
      var i;
      for(i=selectbox.options.length-1;i>=0;i--)
      {
          selectbox.remove(i);
      }
  }
  
  function showHideTopics(userid) {
    var obj = document.getElementById('pnlAttemptedTopics'+userid);
    var img = document.getElementById('img'+userid);
    if (obj.style.display=="none") {
      obj.style.display="block";
      img.src="assets/collapse.gif";
      img.title='';
      document.getElementById('pnlDefaultTopic'+userid).style.display="none";
    }
    else {
      obj.style.display="none";
      img.src="assets/expand.gif";
      img.title='Click to see all topics done';
      document.getElementById('pnlDefaultTopic'+userid).style.display="inline";
    }
  }

</script>
</head>
<body class="translation" onLoad="load();" onResize="load()" onmousemove="reset_interval()" onclick="reset_interval()" onkeypress="reset_interval()" onscroll="reset_interval()">
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
    <div id="trailContainer">
    <table id="childDetails">
          <tbody>
              <tr>
                  <td width="33%" class="activatedTopic" ><a id="myTopicsUrl" href="mytopics.php" style="text-decoration: none;"><div style="cursor:pointer;" class="smallCircle" ></div><label class="pointer" style="cursor:pointer;">Topic Page / Research </label></a></td>
                  <td width="33%" class="activateTopicAll" ><a href="" style="text-decoration: none;"><div  style="cursor:pointer;" class="smallCircle red" ></div><label class="pointer textRed2" style="cursor:pointer;">Topic Report</label></a></td>  <!-- removed link for the task 11583  -->
                  <td width="33%" class="activateTopics" ><a id="topicWiseProgressReportUrl" href="topicProgress.php" style="text-decoration: none;"><div  style="cursor:pointer;" class="smallCircle" ></div> <label class="pointer" style="cursor:pointer;">Topic Progress Report</label></a></td>
              </tr> 
          </tbody>
      </table>

      <div id="frmTopicReportDiv" >
        <form id="frmTopicReport" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
        
        <table id="topicDetails" >
          <td width="5%"><label for="lstClass">Class</label></td>
              <td width="15%" style="border-right:1px solid #626161">
                  <select name="class" id="lstClass"  onchange="" style="width:65%;">
            <option value="">Select</option>
            <?php for($i=0; $i<count($classArray); $i++)  { ?>
              <option value="<?=$classArray[$i]?>" <?php if($class==$classArray[$i]) echo " selected";?>><?=$classArray[$i]?></option>
            <?php } ?>
            </select>
              </td>
          <?php if($hasSections) { ?>
          <td width="6%" class="noSection"><label  id="lblSection" for="lstSection" style="margin-left:20px;">Section</label></td>
              <td class="noSection" width="15%" style="border-right:1px solid #626161">
                  <select name="section" id="lstSection" style="width:65%;">           
          </select>
              </td>
          <?php } ?>
          <td colspan="8" width="8%"> <label id="lblTopic" for="lstTopic" style="margin-left:20px;">Topic</label></td>
          <td width="26%" style="border-right:1px solid #626161">
            <select name="topic" id="lstTopic" style="width:100%;">
            </select>
        </td>
        <td><input type="button" value="Go" id="generateReportButton" class="button" data-flag="<?php echo $coteacherInterfaceFlag?>">
        <?php if(!$coteacherInterfaceFlag){ ?>
        <input type="button" value="Download" id="downloadAsExcelButton" class="button" style="margin-left:20px; clear: both">
        <?php } ?>
        </td>
        </table>
        <input type="hidden" name="schoolCode" id="schoolCode" value="<?=$schoolCode?>">
        <input type="hidden" name="mode" id="mode" value="0">

        </form>
      </div>
      <script type="text/javascript" src="libs/jquery.tablesorter.min.js"></script>
      <script type="text/javascript" src="libs/dashboardCommon.js"></script>      
      <script type="text/javascript" src="libs/topicReport.js?ver=10"></script>
      <script type="text/javascript" src="libs/table2CSV.js"></script>
      <script type="text/javascript">
        var loadPage = 1; 
        $(window).load(function() {
          $("#topicReport").hide();
          $("#topicReportHeading").hide();
          $("#timedTestReport").hide();
          $("#dailyPracticeReport").hide();
          $("#misconceptionsReport").hide();
          $("#moreInformation").hide();
          $("#downloadAsExcelButton").attr('disabled','disabled');
          $("#lstClass").on("change" ,function (event) {  // for the task 11269
            if(loadPage)
            {
              loadPage = 0;
              <?php 
                if(strpos(",", $_GET['sec']) > 0) 
                { 
                  echo "setSection('');";
                }
                else
                { 
                  echo "setSection('".$_GET['sec']."');";
                  if(strpos(",", $_GET['topics']) < 0)
                  {
                    echo "loadTopicList('".$category."','".$_GET['topics']."')";
                  }
                }
                echo '$("#lstSection").trigger("change");';                  
              ?>              
            }
            else
            {
              setSection('');
              $("#lstSection").trigger("change");
            }
          });

          $("#lstSection").on("change", function (event) {
            loadTopicList('<?=$category?>',"<?=$lstTopic?>");
          });

          $("#generateReportButton").click(function() {
            if($("[name=topic]").val() == "") {
              alert("Please select a topic from the menu.");
              return false;
            }   
            var coteacherflag = $(this).attr('data-flag');
            if(coteacherflag == 1)                 
              angular.element(document.getElementById('topicReport')).scope().go();
            else
            {
              var cls = $("#lstClass").val();
              var sec = $("#lstSection").val();
              var topicName = $("[name=topic] :selected").html();
              topicReportAjax('',coteacherflag);
              if(parseInt(cls)>=4 && parseInt(cls)<=7)
              {
                $("#timedTestReport").hide();
                $("#dailyPracticeStarText").show();
                dailyPracticeReportAjax();
              }
              else
              {
                $("#dailyPracticeReport").hide();
                $("#dailyPracticeStarText").hide();
                timedTestReportAjax();
              }
              misconceptionsReportAjax();
              $("#misconceptionsReportHeading_span").text("Misconceptions in '" + topicName + "' for class " + cls + sec);
              $("#misconceptionsReportHeading").show();
              showMoreInformation("submit");
              $("#downloadAsExcelButton").removeAttr('disabled');
            }
          });   

           $("#downloadAsExcelButton").click(function() {
            $("#topicReportTable").table2CSV();
          });   
          if(document.URL.split('?').length > 1) {
            topicReportAjaxRedirect(<?= $coteacherInterfaceFlag ?>);
          }
        });
      </script>
      <br>
<?php
  if($coteacherInterfaceFlag)
    include('progress-report.php');
  else
  {
    ?>
      <div id="topicReport">
        <div id="topicReportHeading" class="dashboard-report-title">
            <span id="topicReportHeading_span">Topic Report for 3A for Topic: </span>
            <a class="intro-launch" onClick='initIntroJs();' title="Help" style="cursor: help;"></a>
        </div>
        <div id="topicReportavgAccuracy">
          <span id="topicReport_avgAccuracy" class="dashboard-report-title" style="text-align: left; font-size: 15px; font-weight: normal"></span>
        </div>
        <div id="topicReport_loading" class="dashboard-loading">
          <p>This may take a few minutes</p> 
        </div>
        <div id="topicReport_message" style="text-align:center">No student has attempted a question in the selected date range.</div>
        <div id="averageAccuracySummary"></div>
        <table id="topicReportTable" >
          <thead id="topicReportTable_thead"></thead>
          <tbody id="topicReportTable_tbody"></tbody>
        </table>
      </div> 

      <div id="timedTestReport" >
        <div id="timedTestReportHeading" class="dashboard-report-title">Timed Tests</div>
        <div id="timedTestReport_loading" class="dashboard-loading">
          <p>This may take a few minutes</p> 
        </div>
        <div id="timeTestReport_message">There are no timed tests for this topic attempted by students.</div>
        <table id="timedTestReportTable">
          <thead id="timedTestReportTable_thead"></thead>
          <tbody id="timedTestReportTable_tbody"></tbody>
        </table>        
      </div>

      <div id="dailyPracticeReport" >
        <div id="dailyPracticeReportHeading" class="dashboard-report-title">Daily Practice Report for Class 4 Robins for the topic: </div>
        <div id="dailyPracticeReport_loading" class="dashboard-loading">
          <p>This may take a few minutes</p> 
        </div>
        <div id="dailyPracticeReport_message">There is no daily practice for this topic attempted by students.</div>
        <table id="dailyPracticeReportTable">
          <thead id="dailyPracticeReportTable_thead"></thead>
          <tbody id="dailyPracticeReportTable_tbody"></tbody>
        </table>        
      </div>

      <div id='legendDiv'>
        <p id='legendTxt'><b>Legend:</b></p> 
        <table id="topicReportLegend">
          <tr>
            <td class="great">Great Accuracy (&gt;80%)</td>
            <td class="good">Good Accuracy (40%-80%)</td>
            <td class="low">Low Accuracy (&lt;40%)</td>
            <td class="notEnoughUsage">Not Enough Usage</td>
          </tr>
        </table>
        <div id='dailyPracticeStarText'><b><sup>*</sup> Top 5 students with least accuracy and/or fluency in the module.<b></div>
      </div>

      <div id="misconceptionsReport">
      <div id="misconceptionsReportHeading" class="dashboard-report-title">
        <span id="misconceptionsReportHeading_span"></span></div>
        <p id="misconceptionsReportList"></p>
      </div>


      <div id="moreInformation" >
      <div id="moreInformationHeading" class="dashboard-report-title">Click on the following for more information about this topic</div>
        <p id="moreInformationLinks">
          <ul>
            <li><a id="topicRemediationReportUrl" href="topicRemediationSection.php" target="_blank">Topic Remediation</a></li>
            <li><a id="cwaReportUrl" href="cwa.php" target="_blank">Common Wrong Answer Report</a></li>
          </ul>
        </p>
      </div>
       <?php }
      ?>
<?php include("footer.php") ?>

