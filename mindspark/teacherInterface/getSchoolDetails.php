<?php 
include("header.php"); 
include("../../slave_connectivity.php");

         set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);

	if(!isset($_SESSION['userID']))
	{
		header("Location:logout.php");
		exit;
	}
                
	if(isset($_POST['submit']) && $_POST['schoolCode']!="")
	{
            if($_POST['subjectno']==1){                             
                    //get user mse id
                $mseQuery="SELECT * FROM educatio_educat.`common_user_details` uD WHERE uD.`MS_userID`='".$_SESSION['userID']."' AND uD.`category`='School Admin' AND uD.`subcategory`='All'";
                    $mseQuery = mysql_query($mseQuery);
                    $mseQueryRow = mysql_fetch_assoc($mseQuery);
                    $_SESSION['session_id'] =session_id();
                    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
                    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
                    $_SESSION['last_activity'] = time();
                    $_SESSION['mse_user_id']=$mseQueryRow['MSE_userID'];
                    $_SESSION['schoolCode']=$_POST['schoolCode'];  
                   
                    header('Location: ../../mindspark/ms_english/Language/login/index/'.$_SESSION['mse_user_id'].'/'.$_SESSION['osDetails'].'/'.$_SESSION['browserName'].'/'.$_SESSION['browserVersion'].'/'.$_SESSION['schoolCode']);                   
            }elseif($_POST['subjectno']==2){ 
                    $sq	= "SELECT COUNT(schoolCode) FROM adepts_offlineSchools WHERE schoolCode=".$_POST['schoolCode'];
                    $rs = mysql_query($sq);
                    $rw = mysql_fetch_array($rs);
                    if($rw[0]>0)
                    {
                            $_SESSION['isOffline'] = true;
                            $_SESSION['offlineStatus'] = 1;
                    }
                    $_SESSION['schoolCode'] = $_POST['schoolCode'];
                    $_SESSION['subjectno']  = $_POST['subjectno'];
                    $_SESSION["topicData"]	=	"";
                    $_SESSION["ttName"]	=	"";
                    $_SESSION["liveSession"]	=	"";
                    header("Location:home.php");
            }
	}

	$userID = $_SESSION['userID'];
	$user   = new User($userID);

	if(!($user->category=="School Admin" && $user->subcategory=="All"))
	{
		echo "You are not authorised to access this page!";
		exit;
	}

            $schoolCode_query = "SELECT schoolsite_da_ms_data.schoolCode, schoolsite_da_ms_data.product, schools.`schoolname`, schools.`city` FROM `educatio_educat`.`schools` INNER JOIN `educatio_educat`.`schoolsite_da_ms_data` ON (`schools`.`schoolno` = `schoolsite_da_ms_data`.`schoolCode`) WHERE schoolsite_da_ms_data.product IN ('mindspark','mindsparkEng') GROUP BY schoolsite_da_ms_data.schoolCode, schoolsite_da_ms_data.product ORDER BY schools.`schoolname` ";
            $result = mysql_query($schoolCode_query);
            $schoolCodeArray = array();
            while ($line = mysql_fetch_assoc($result)) {
                if (isset($schoolCodeArray[$line['schoolCode']])) {
                    $schoolCodeArray[$line['schoolCode']]['product'][count($schoolCodeArray[$line['schoolCode']['product']])+1]=$line['product'];
                     }else{
                $schoolCodeArray[$line['schoolCode']]=array('schoolname'=>$line['schoolname'].', '.$line['city'],'product'=>array($line['product']));
                
                }
            }
			
			$schoolCode_query = "SELECT B.schoolCode, 'mindspark', A.schoolname, A.city,order_type FROM educatio_educat.schools A, educatio_educat.ms_orderMaster B WHERE A.schoolno=B.schooLCode AND order_type='pilot' AND end_date>curdate()";
            $result = mysql_query($schoolCode_query);
            while ($line = mysql_fetch_array($result)) {
                if (isset($schoolCodeArray[$line['schoolCode']])) {
                    $schoolCodeArray[$line['schoolCode']]['product'][count($schoolCodeArray[$line['schoolCode']['product']])+1]=$line[1];
                     }else{
                $schoolCodeArray[$line['schoolCode']]=array('schoolname'=>$line['schoolname'].', '.$line['city'],'product'=>array($line[1]));
                
                }
            }
			
			$schoolCodeArray['2387554']=array('schoolname'=>'Demo School, Bangalore','product'=>array('mindsparkEng','mindspark'));
			$schoolCodeArray['3244973']=array('schoolname'=>'Demo School - Mindspark Offline, Ahmedabad','product'=>array('mindsparkEng'));
			$schoolCodeArray['3154686']=array('schoolname'=>'Demo School - Schoolsite, Ahmedabad','product'=>array('mindsparkEng'));
			$lastPage=$_SERVER['HTTP_REFERER'];
			$lastPage=explode('/',$lastPage);
			$lastPage=$lastPage[count($lastPage)-1];
			if(!isset($lastPage) && empty($lastPage)){
			  $lastPage="";
			}
			echo '<script>';
			echo 'var schoolList='.json_encode($schoolCodeArray).';';
			echo 'var lastPage="'.$lastPage.'";';// otherFeatures.php, session
			echo '</script>'; 
			
?>
<title>Mindspark</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/common.css" rel="stylesheet" type="text/css">
<style>
#sideBar {
	width:7% !important;
}
#container {
	padding:40px;
}
#schoolCode {
    display: none;
}
#subjectSelect {
    display: inline-block;
    min-width: 200px;
}
#schoolDetails{display:none;}
</style>

<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script>
	history.forward();
	var langType = '<?=$language;?>';
</script>
</head>

<body>
<?php include("eiColors.php") ?>
	<div id="fixedSideBar">

	</div>
	<div id="topBar">
	<?php
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	include("classes/testTeacherIDs.php");
	if(!isset($_SESSION['userID']))
	{
		header("Location:../logout.php");
		exit;
	}
	$userID     = $_SESSION['userID'];
	$schoolCode = isset($_SESSION['schoolCode'])?$_SESSION['schoolCode']:"";
	$user   = new User($userID);
	$todaysDate = date("d");
	

	if(strcasecmp($user->category,"Teacher")==0 || strcasecmp($user->category,"School Admin")==0)	{
		$query = "SELECT schoolname FROM educatio_educat.schools WHERE schoolno=".$schoolCode;
		$r = mysql_query($query);
		$l = mysql_fetch_array($r);
		$schoolName = $l[0];
	}

?>
	<div class="logo">
	</div>
	<div id="infoBarMiddle">
		<div id="interfaceDetails"><?php if (strcasecmp($user->category,"TEACHER")==0)	{	?>
                    <div align="center" class="label_title_top">Teacher interface
                    </div>
                <?php } elseif (strcasecmp($user->category,"School Admin")==0)	{	$userCategory='Admin';?>
                    <div align="center" class="label_title">&nbsp;&nbsp;Administator interface</div>
                <?php
                  }
                ?>
		</div>
		<div id="schoolDetails"><?php if (strcasecmp($user->category,"TEACHER")==0)	{	?>
                    <div align="center" class="label_title_top">
                    <?php if(!in_array(strtolower($user->username),$testIDArray)) { ?>
                    &nbsp;School: <?=$schoolName?>
                    <?php } ?>
                    </div>
                <?php } elseif (strcasecmp($user->category,"School Admin")==0)	{	$userCategory='Admin';?>
                    <div align="center" class="label_title">&nbsp;&nbsp;<?=$schoolName?></div>
                <?php
                  }
                ?>
		</div>
	</div>
	<div id="studentInfoLowerClass">
    	<div id="nameIcon"></div>
    	<div id="infoBarLeft">
        	<div id="nameDiv">
                <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><div id="nameC">Welcome <?=$user->childName?>&nbsp;&#9660;</div></a>
                                <ul>
                                    <li><a href='logout.php'><span>Logout</span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
            </div>
        </div>
    </div>
	</div>
	<div id="sideBar">

	</div>

	<div id="container">
            <?php if(isset($messageNotification) && !empty($messageNotification)){ echo '<div class="messageNotification" style="color:#F00;text-align:center;font-size:14px; padding-bottom:20px;">'.$messageNotification.'</div>'; } ?>
		<form name="frmSchoolSelection" id="frmSchoolSelection" method="POST" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="return validateSubmit();">
			<input type="hidden" name='mode' id="mode" value="nextAction">
                        <div class="subjectSelect" id="subjectSelect">
                        <label for="lstSubject">Subject:</label>
                        <select name="subjectno" id="lstSubject" onChange="updateList();">
                            <option value="0">--Select Subject--</option>
                            <option value="2">Maths</option>
                            <option value="1">English</option>
                        </select>
                        </div>
                        <div class="schoolCode" id="schoolCode" >
			<label for="lstSchool">Select a school:</label>
			<select name="schoolCode" id="lstSchool">
			<option value="0">--Select School--</option>
			</select>
                        </div>
			&nbsp;&nbsp;	
                        <input type="submit" id="btnSubmit" name="submit" value="Go" >
		</form>
	</div>
            <script>   
                function updateList(){
                    $("#schoolCode").css({'display':'none'});
                    $("select#lstSchool").html('<option value="0">--Select School--</option>');
                    var subjectNo=$('#lstSubject').val();
                        if(subjectNo!=0){
                            var options="";
                            if(subjectNo==2){
                                var filter="mindspark";
                            }else if(subjectNo==1){
                                var filter="mindsparkEng";
                            }
                            options +='<option value="0">--Select School--</option>';
                            $.each(schoolList, function (key, value) {
                            {
                                if($.inArray( filter, value.product )!= -1){
                                     options += '<option value="' + key + '">' + value.schoolname + '</option>';
                                }
                            }
                            });
                            $("#schoolCode").css({'display':'inline'});
                            $("select#lstSchool").html(options);
                            var options = $('select#lstSchool option');
                            var arr = options.map(function(_, o) { return { t: $(o).text(), v: o.value }; }).get();
                            arr.sort(function(o1, o2) { return o1.t > o2.t ? 1 : o1.t < o2.t ? -1 : 0; });
                            options.each(function(i, o) {
                              o.value = arr[i].v;
                              $(o).text(arr[i].t);
                            });
                        }                       
                }
                function validateSubmit(){
                    var subjectNo=$('#lstSubject').val();
                    var lstSchool=$('#lstSchool').val();
                    if(subjectNo==0){
                        alert('Please select subject.');
                        return false;
                    }
                    if(lstSchool==0){
                        alert('Please select school.');
                        return false;
                    }
                }
				$( document ).ready(function() {
					if(lastPage==="otherFeatures.php"){
						$("#lstSubject").val("2").change();
					}
					else if(lastPage==="session"){
						$("#lstSubject").val("1").change();
					}
				});
                </script>
</body>
</html>
