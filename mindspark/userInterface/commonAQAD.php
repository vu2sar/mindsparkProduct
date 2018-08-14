<link rel="stylesheet" type="text/css" href="css/colorbox.css">
<link rel="stylesheet" href="/mindspark/userInterface/libs/css/jquery-ui.css" media="all" />
<script type="text/javascript">
function showAQAD(){
	$.fn.colorbox({'href':'#aqadContainer','inline':true,'open':true,'escKey':true, 'height':600, 'width':800});
}
function closePromptAQAD(){
	$(".aqadPrompt").css("display","none");
}
function goToByScroll(id){
    // Remove "link" from the ID
	 $("#11").css("display","none");
	  $("#22").css("display","none");
	   $("#33").css("display","none");
	   $("#dayDiv div").removeClass("active-link");
	   var m_names = new Array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
	   if(id == '11'){
		   $(".today-link").addClass("active-link");
	   }
	   if(id == '22'){
		   $(".yesterdat-link").addClass("active-link");
		   var d = new Date();
		   d.setDate(d.getDate() - 1);
		   var curr_date = d.getDate();
		   var curr_month = d.getMonth();
		   var curr_year = d.getFullYear();
		   $datepickerDate = curr_year+"-"+m_names[curr_month]+"-"+curr_date;
		   $(".datepicker-hidden-input").val($datepickerDate);
	   }
	   if(id == '33'){
		   $(".daybefore-link").addClass("active-link");
		   var d = new Date();
		   d.setDate(d.getDate() - 2);
		   var curr_date = d.getDate();
		   var curr_month = d.getMonth();
		   var curr_year = d.getFullYear();
		   $datepickerDate = curr_year+"-"+m_names[curr_month]+"-"+curr_date;
		   $(".datepicker-hidden-input").val($datepickerDate);
	   }
  $("#"+id).css("display","block");
}
function filterByDate(classTobeFilter,dateTobeFilter,classSectionArray,isTeacher,schoolCode,userResponse){
	if(classSectionArray != ""){
		var enjs = JSON.stringify(classSectionArray);
	}
	else{
		var enjs = "";
	}
	
	var data ="mode=filterAQADByDate&classTobeFilter="+classTobeFilter+"&dateTobeFilter="+dateTobeFilter+"&classSectionArray="+enjs+"&isTeacher="+isTeacher+"&schoolCode="+schoolCode+"&userResponse="+userResponse;
	$.ajax({
		  url: "../userInterface/commonAjax.php",
		  data : data ,
		  cache: false,
		  type: "POST",
		  success: function(html){
		    $("#common-aqad-div").html(html);
		    $( ".datepicker-hidden-input" ).datepicker(
		    		{ 
		    			dateFormat: 'yy-mm-dd',
		    			maxDate: '0',
		    			 beforeShowDay: function(date) {
		    			        var day = date.getDay();
		    			        return [(day != 0), ''];
		    			    },
		    				onSelect : function(selected){
		    					var allClasses = "<?php echo implode("-",$classArray); ?>";
		    					var schoolCode = "<?php echo $user->schoolCode; ?>";
		    					var currentClass =$(this).attr("id");
		    					var currentDate = selected;
		    					<?php 
		    					for($i = 0; $i < count($classArray); $i++){
		    						$classSectionArray[$classArray[$i]] = str_replace(",","$",$sectionArray[$i]);
		    					}
		    					?>
		    					var finalClassSection = <?php echo json_encode($classSectionArray); ?>;
		    					filterByDate(currentClass,currentDate,finalClassSection,1,schoolCode,"");
		    				}
		    		 });
		  }
		});
	return false;
}
$(function() {
	try
	{
	$( ".datepicker-hidden-input" ).datepicker(
	{ 
		dateFormat: 'yy-mm-dd',
		maxDate: '0',
		 beforeShowDay: function(date) {
		        var day = date.getDay();
		        return [(day != 0), ''];
		    },
		onSelect : function(selected){
			var allClasses = "<?php echo implode("-",$classArray); ?>";
			var schoolCode = "<?php echo $user->schoolCode; ?>";
			var currentClass =$(this).attr("id");
			var currentDate = selected;
			<?php 
			for($i = 0; $i < count($classArray); $i++){
				$classSectionArray[$classArray[$i]] = str_replace(",","$",$sectionArray[$i]);
			}
			?>
			var finalClassSection = <?php echo json_encode($classSectionArray); ?>;
			filterByDate(currentClass,currentDate,finalClassSection,1,schoolCode,"");
		}
	 });
	}catch(err) {}
});
</script>
<div style="display: none;">
	<div id="aqadContainer">
	<?php 
		if(!isset($_SESSION['userID'])){
			?>
				<a target="_blank" href="http://mindspark.in/registration.php?userID=<?=$userID?>"><div id="buyMindSparkLink">Buy mindspark</div></a>
			<?php 
		}
	?>
		<?php
	$startdateToday = date ( 'Y-m-d' );
	$query = "select student_answer from educatio_educat.aqad_responses where studentID=" . $userID . " and entered_date like '%" . $startdateToday . "%'";
	$result = mysql_query ( $query ) or die ( mysql_error () );
	if ($line = mysql_fetch_array ( $result )) {
		$userAnswer = $line ['student_answer'];
	} else {
		$userAnswer = "";
	}
	include ("eiaqad.cls.php");
	mysql_select_db ( "educatio_educat" );
	?>
	<div id="common-aqad-div" >
	<?php 
	if(strcasecmp ( $user->category, "Teacher" ) == 0 || strcasecmp ( $user->category, "School Admin" ) == 0){
		$currentClass = max($classArray) == "10"?"9": max($classArray);
		
		for($i = 0; $i < count($classArray); $i++){
			$classSectionArray[$classArray[$i]] = str_replace(",","$",$sectionArray[$i]); 
		}
		$classSectionArray = json_encode($classSectionArray);
		echo generateAQADtemplate ($startdateToday, $currentClass,'',0,1, $classSectionArray,$user->schoolCode);
	}else{
		echo generateAQADtemplate ( $startdateToday, $childClass, $userAnswer, $userID ,0);		
	}
	mysql_select_db ( "educatio_adepts" );
	?>
	</div>
		</div>

</div>
<?php  //} ?>
