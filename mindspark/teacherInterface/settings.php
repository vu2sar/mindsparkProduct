<?php
error_reporting(1);
header('X-UA-Compatible: IE=EmulateIE8');
include("header.php");
include("../userInterface/functions/functions.php");
require_once 'common-code.php';


    $userid =  $_SESSION['userID'];
    $schoolCode= $_SESSION["schoolCode"];
    $query  = "SELECT childName,schoolCode, category FROM adepts_userDetails WHERE userID='$userid'";
    $result = mysql_query($query) or die(mysql_error());
    $line   = mysql_fetch_array($result);
    $userName   = $line[0];
    $category   = $line[2];

    if(strcasecmp(trim($category),"School Admin")!=0)
    {
        echo "You are not authorised to access this page!";
        exit;
    }

$finalClassSectionArray = array();
$settingDefaults=array('deactivatedTopicsAtHome'=>'On','sessionDuration'=>'40','curriculum'=>'MS','mpi'=>json_encode(array(
  'weightages' => array(
    'Accuracy' => 40,
    'Badges' => 10,
    'Weekly Usage Score' => 40,
    'Topic Completion' => 10,
  ),
  'others' => array(
    'Minimum weekly usage' => 45,
    'Minimum weekly question attempts' => 25,
  ),
)));
$finalClassSectionArray = rearrange_final_class_section_array($classArray,$sectionArray);
is_school_class_section_available_in_userInterfaceSettings($schoolCode,$finalClassSectionArray,$userName);  
function amendMpiSettings($mpiDefaults, $schoolCode, $userName) {
  $mpiSettingsFormat = preg_replace('/[0-9]+/', '[0-9]+', $mpiDefaults);
  $result = mysql_query("
    select class, section, settingValue as mpiJson
    from userInterfaceSettings
    where schoolCode=$schoolCode and settingName='mpi' and settingValue not regexp '$mpiSettingsFormat'
  ") or die(mysql_error());
  if(mysql_num_rows($result)>0) {
    while($row = mysql_fetch_assoc($result)) {
      if(preg_match('/'.preg_replace('/[0-9]+/', '[0-9]+', $mpiDefaults).'/', $row['mpiJson']))
        continue;
      $defaultMpiSettings = json_decode($mpiDefaults, true);
      $existingMpiSettings = json_decode($row['mpiJson'], true);
      if(is_null($existingMpiSettings)) {
        $correctMpiSettings = $defaultMpiSettings;
      } else {
        if(isset($existingMpiSettings['others']['Recommended Weekly Usage'])) {
          $existingMpiSettings['others']['Minimum weekly usage'] = $existingMpiSettings['others']['Recommended Weekly Usage'];
          unset($existingMpiSettings['others']['Recommended Weekly Usage']);
        }
        $correctMpiSettings = array();
        foreach ($defaultMpiSettings as $category => $settings) {
          foreach ($settings as $name => $value) {
            if(isset($existingMpiSettings[$category][$name])) {
              if(gettype($existingMpiSettings[$category][$name])=='integer') {
                $correctMpiSettings[$category][$name] = $existingMpiSettings[$category][$name];
              } else {
                $correctMpiSettings[$category][$name] = (integer) floor($existingMpiSettings[$category][$name]);
              }
            } else {
              $correctMpiSettings[$category][$name] = $value;
            }
          }
        }
      }
      $newSettings = json_encode($correctMpiSettings);
      mysql_query("
        update userInterfaceSettings
        set settingValue='$newSettings', lastModifiedBy='$userName'
        where schoolCode='$schoolCode' and class=$row[class] and section='$row[section]'
          and settingName='mpi' and settingValue not regexp 'Minimum weekly usage'
      ") or die(mysql_error());
    }
  }
}
amendMpiSettings($settingDefaults['mpi'], $schoolCode, $userName);
// inserts into settings table if not available

//show_data_exit($finalClassSectionArray);
/*if(!$tsetting_school_code){
  foreach ($finalClassSectionArray as $class => $value) {
      $sections = array();
      $section = convert_string_to_array($value);
      foreach ($section as $division) {
          insertClassSectionSetting($schoolCode,$class,$division,$userName);
      }
  }
}*/
function insertClassSectionSetting($schoolCode,$class, $division, $userName, $settings){

  foreach ($settings as $settingName => $value) {
    $new_entry = "INSERT into userInterfaceSettings (schoolCode, class, section, settingName, settingValue, lastModifiedBy,settingType) 
                values ('$schoolCode','$class','$division','$settingName','$value','". $userName ."','School')";
    mysql_query($new_entry) or die(mysql_error());
  }
}

if($_POST["submitValue1"] == "putInSettings")
{
   execute_insert_and_update_on_userInterfaceSettings($finalClassSectionArray,$schoolCode,$userName);
  // update_data_in_related_tables($durationValue,$schoolCode);
} 


$settingDataArray = get_saved_data_of_setting_page($schoolCode);
//show_data($settingDataArray);

      foreach ($settingDataArray['data'] as $values) {
        
                  if($values['settingName'] == 'deactivatedTopicsAtHome'){

                          if($values['settingValue'] == "CustomOff" || $values['settingValue'] == "CustomOn"){
                                    $deactivatedValue = "Custom"; 
                          }else{
                                $deactivatedValue = $values['settingValue'];
                               
                          }
                  }

                  else if($values['settingName'] == 'curriculum'){

                    if($values['settingValue'] == 'MS')
                            $curriculumValue ="Mindspark";
                    else{

                            $curriculumValue =$values['settingValue'];
                    }
                  }
                  else if($values['settingName'] == 'sessionDuration')

                              $durationValue = $values['settingValue'];

                  else if($values['settingName'] == 'mpi'){

                     if($values['settingValue'] == "CustomOff" || $values['settingValue'] == "CustomOn"){
                                    $mpiValue = "Custom"; 
                          }else{
                                    $mpiValue = $values['settingValue'];

                          }


                  }

                             
        
      }


      $tabValues = get_form_display_values_from_userInterfaceSettings($settingDataArray);    // $tabValues is used inside the body tag for condition checking


?>


    
<script>

            var deactivatedValue="<?=$deactivatedValue?>";
            var durationValue="<?=$durationValue?>";
            var curriculumValue="<?=$curriculumValue?>";
            var mpiValue='<?=$mpiValue?>';
            function allowOnlyDigits(event) {
                return /Firefox/.test(window.navigator.userAgent) && event.keyCode!=0 || /^[0-9]$/.test(String.fromCharCode(event.which || event.keyCode));
            }


function saveDetails () {

          var height2;
          var mpiWeightages = [];
          $('#mpi #weightages .mpiSettingValue').each(function() {
            mpiWeightages.push(+this.value);
          });
          if(eval(mpiWeightages.join('+'))!=100) {
            alert('The weights you have selected do not add up to 100%');
            return;
          }
          var mpiOtherSettingsValidity = true;
          $('#mpi #others .mpiSettingValue').each(function() {
            if(this.name!='Minimum weekly usage')
              return;
            if(this.value=='') {
              mpiOtherSettingsValidity = false;
              alert('Please enter the '+this.name.toLowerCase().replace('minimum', 'recommended')+'.');
              this.focus();
              return false;
            }
          });
          if(!mpiOtherSettingsValidity)
            return;
          $('.loader').show();
          var mpiSettings = {
            weightages: {},
            others: {},
          };
          for(var category in mpiSettings) {
            if(!mpiSettings.hasOwnProperty(category))
              continue;
            $('#mpi #'+category+' .mpiSettingValue').each(function() {
              mpiSettings[category][this.name] = +this.value;
            });
          }
          mpiValue = JSON.stringify(mpiSettings);
        
                        // transmitting js value to php via form "settingValues"
                        document.getElementById("deactivatedValue").value = deactivatedValue;
                        document.getElementById("curriculumValue").value = curriculumValue;
                        document.getElementById("durationValue").value = durationValue;
                        document.getElementById("mpiValue").value = mpiValue;
                        document.getElementById("submitValue1").value = 'putInSettings';
                        document.getElementById('settingValues').submit();

 }


                var clicked =false;
                var changeDeactivate;
                var changempi;
                var anyTabOpen = false;
                var restoreDeactivate = false;
                var restoreDuration = false;
                var restorempi = false;
                var restoreCurriculum = false;

$(document).ready(function(){


                          $( "div[rel]").overlay();
                                var bodyHeight = jQuery( "body").height();
                                var contWidth = pageWidth();
                                if( contWidth > 1024) {
                                        jQuery( ".hlp_img").css( "width", "1024px");
                                        jQuery( ".hlp_img img").css( "width", "1024px");
                                        contWidth = (contWidth - 1024)/2;
                                }
                                else {
                                        someval = ( contWidth * 75)/100;
                                        jQuery( ".hlp_img").css( "width", ( someval + "px"));
                                        jQuery( ".hlp_img img").css( "width", ( someval + "px"));
                                        contWidth = 0;
                                }
                                someother = ( jQuery( "body").height() - ( ( 768*75)/100)) /2;
                                someother = ( someother > 0)? someother : 20;
                                jQuery( ".hlp_img").css( "margin-top", someother + "px");
                                $(document).keyup(function(event){
                                        if (event.keyCode == 27) {
                                                jQuery( ".help_image").css( "display", "none");
                                        }
                                });

                                jQuery( ".gray_outer").bind( "click", function(e) {
                                        jQuery( ".help_image").css( "display", "none");
                                });
    //jQuery for Save Settings Button
                        $( "#saveAll" ).click(function() {
                                 // $('#saveAll').hide();
                                   // $(".loader").show();
                        });

                                   $(window).load(function() {
                                         $(".loader").hide();
                                  });




    //jQuery for data Slider
                        $("[data-slider]")
                                        .each(function () {
                                          var input = $(this);
                                          $("#toChange")
                                            .addClass("output")
                                            .insertAfter($(this));
                                        })
                                        .bind("slider:ready slider:changed", function (event, data) {
                                          
                                          $(this)
                                            .nextAll(".output:first")
                                              .html(data.value.toFixed(0));
                                        });
                     

                                // jQuery for moving tabs onclick
                                  
                                    $(".tabs").live("click",function(){
                                      var currentTabId = $(this).attr("id");
                                      
                                      //alert($(this).attr("id"));
                                      $("."+$(this).attr("id")).slideDown(200);
                                      $('.tabs-inner').each(function(){

                                      if(!$(this).hasClass(currentTabId))
                                        $(this).slideUp(200);
                                      });
                                      
                                    });

        


         //jQuery for Deactivated main checkbox checked
       
        $("#deactivatedTopicCheck").change( function(){

                    var thisCheck = $(this);
                 //   alert(thisCheck);

          if(thisCheck.is (':checked')){  // checkbox on

                       jQuery("#deactivatedTopicsCheck").attr('checked',true);
                       jQuery("#deactivatedTopics input:radio").removeAttr('disabled');

                       deactivatedValue = "On";

                    
          }else{            // checkbox off
                
                jQuery("#deactivatedTopics input:radio").attr('disabled',true);
                $('#classDisplayDeactivate').hide();
                deactivatedValue="Off";        
                jQuery("#deactivatedTopicsCheck").removeAttr('checked');     
          }
        });


        $('input[type=radio][name=ClassSchoolDeactivate]').change(function() {
                              if (this.value == 'School') {
                                  $('#classDisplayDeactivate').hide();
                                  $("#deactivatedTopicCheck").removeAttr("disabled");
                                 
                                  deactivatedValue = "On";
                      
                                $("#sliderOnDeactivate").css("background","#067D00");
                               
                              }
                              else if (this.value == 'Class') {
                                 /* alert("Class");*/

                                  $('#classDisplayDeactivate').show();

                                  $('#deactivatedTopicCheck').attr("disabled", true);
                                  
                                  $("#sliderOnDeactivate").css("background","#676767");
                         
                                  deactivatedValue="Custom";
                             
                                  
                              }
                      });

    
      $("#mpiCheck").change( function(){


                    var thisCheck = $(this);
                 

          if(thisCheck.is (':checked')){  // checkbox on
                       jQuery("#mpiCheck").attr('checked',true);
                       jQuery("#mpi input:radio").removeAttr('disabled');
             jQuery("input[type=radio][name=ClassSchoolmpi][value=School]").prop('checked', true);
             mpiValue = "On";
             
            setTimeout(function(){
                  alert('As we are working on the reports, we recommend turning Mindspark Performance Report off till the new report is in place.');
            }, 200);
            
                    
          }else{            // checkbox off

                 
                jQuery("#mpi input:radio").attr('disabled',true);
                $('#classDisplaympi').hide();
            
                mpiValue="Off";
              

            
                jQuery("#mpiCheck").removeAttr('checked');

                    
          }


        });

          // jQuery for radio buttons of mpi

        $('input[type=radio][name=ClassSchoolmpi]').change(function() {
              if (this.value == 'School') {
                   $('#classDisplaympi').hide();
                   $("#mpiCheck").removeAttr("disabled");
                   $('#classDisplaympi').hide();
                   $("#sliderOnmpi").css("background","#067D00");
          
                  mpiValue = "On";
               
              }
              else if (this.value == 'Class') {
                 
                  $("#sliderOnmpi").css("background","#676767");

                  $('#classDisplaympi').show();
                  $('#mpiCheck').attr("disabled", true);                     // Change the color of a checkbox here bcos disabled
                  mpiValue="Custom";
             
                  
              }
      });


});

                  function saveCurriculum(id){
                       curriculumValue = id;
                    }


</script>



<title>Settings</title>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" >

<!-- <script src="libs/jquery.min.js"></script>  -->
<script src="libs/jquery.tools.min.js"></script>
 
<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/settings.css?datetime=2016.09.29.16.59.20" rel="stylesheet" type="text/css">
<link href="css/simple-slider.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/simple-slider.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>   
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>


<script>
        var langType = '<?= $language; ?>';
        function load(){
                var fixedSideBarHeight = window.innerHeight;
                var sideBarHeight = window.innerHeight-95;
                var containerHeight = window.innerHeight-115;
                $("#fixedSideBar").css("height",fixedSideBarHeight+"px");
                $("#sideBar").css("height",sideBarHeight+"px");
                $("#container").css("height",containerHeight+"px");
        }
        
    
</script>
                           
<script type="text/javascript" src="libs/getscreen.js"></script>
<script language="JavaScript" src="libs/gen_validatorv31.js" type="text/javascript"></script>
<script type="text/javascript">
//<![CDATA[
        var is_ie6 = ('ActiveXObject' in window && !('XMLHttpRequest' in window));
//]]>
</script>
                <script type="text/javascript">
                      

                        var spanxHeight;
                        var firstRun = 1;
                        function showHelp() {
                                jQuery( ".help_image").css( "display", "block");

                                if( firstRun == 1) {
                                        firstRun = 0;
                                        ratio = jQuery( "#help_img_home").width()/1024;
                                        jQuery.each( jQuery( "area"), function() {
                                                var coord = jQuery( this)[0].coords.split( ",");
                                                var a = coord[0] + "," +coord[1] + "," +coord[2] + "," +coord[3];
                                                for ( var i in coord) {
                                                        coord[ i ] = coord[ i ] * ratio;
                                                }
                                                jQuery( this)[0].coords = coord[0] + "," +coord[1] + "," +coord[2] + "," +coord[3];
                                        });

                                        jQuery.each( jQuery( "area"), function() {
                                                var offsetd = jQuery( this).context.coords.split(",")
                                                var offsets = jQuery( "#help_img_home").offset();
                                                var oTop = offsets.top + parseInt( offsetd[ 1 ]) - 130;
                                                var oLeft = offsets.left + parseInt( offsetd[ 0 ]);
                                                var mid = jQuery( this).context.id;
                                                if( oTop > 0) {
                                                        var customdiv = "<div class=\"tooltip_box_img\" id=\"tooltip_" + mid + "\" style=\"position: absolute; top: " + oTop + "px; left: " + oLeft + "px\">" + jQuery( this).context.alt +"</div>";
                                                }
                                                else {
                                                        oTop = offsets.top + parseInt( offsetd[ 3 ]);
                                                        var customdiv = "<div class=\"tooltip_box_img_invert\" id=\"tooltip_" + mid + "\" style=\"position: absolute; top: " + oTop + "px; left: " + oLeft + "px\">" + jQuery( this).context.alt +"</div>";
                                                }
                                                $( "body").append( customdiv);
                                        });

                                        jQuery( "area").bind( "mouseover", function( e) {
                                                jQuery( "#tooltip_" + jQuery( this).context.id).css( "display", "block");
                                        });

                                        jQuery( "area").bind( "mouseout", function( e) {
                                                jQuery( "#tooltip_" + jQuery( this).context.id).css( "display", "none");
                                        });
                                }
                        }

                        window.onload = function() {
                                var bodyHeight = pageHeight();
                                var contWidth = pageWidth();

                                if( contWidth > 1024) {
                                        contWidth = (contWidth - 1024)/2;
                                } else {
                                        contWidth = 0;
                                }

                                var contentHeight = ( pageHeight()>1024?1024:pageHeight()) - 55;
                                if( contentHeight < 560 ) {
                                        contentHeight = 560;
                                }

                                contentHeight -= 30;
                                spanxHeight = contentHeight;

                                var bHString = bodyHeight;
                                var chString = contentHeight;
                                var wString = contWidth;
                                var cWidth = pageWidth() - contWidth - contWidth -5;

                                $( "#score_page").css( {
                                        height: bHString
                                });

                                $( "#content").css( {
                                        height: ( spanxHeight)
                                });

                                if( is_ie6) {
                                        $( "#container").css( {width: cWidth});
                                }

                                if( wString > 0) {
                                        $( "#left_cover").css( {
                                                width: wString
                                        });

                                        $( "#right_cover").css( {
                                                width: wString
                                        });
                                }
                                else {
                                        $( "#left_cover").css( {
                                                "display": "none"
                                        });
                                        $( "#right_cover").css( {
                                                "display": "none"
                                        });
                                }

                                $( "#b_bar_left").css( {
                                        height: chString
                                });

                                $( "#b_bar_right").css( {
                                        height: chString
                                });

                                $( "#score_page").css( {
                                        display: "block"
                                });
                        }
                </script>

              
</head>
<body class="translation" onload="load()" onresize="load()">

        <?php
include("eiColors.php");
?>
        <div id="fixedSideBar">
                <?php
include("fixedSideBar.php");
?>
        </div>
        <div id="topBar">
                <?php
include("topBar.php");
?>
        </div>
        <div id="sideBar">
                        <?php
include("sideBar.php");
?>
        </div>

        <div id="container">
                <div id="innerContainer">
                      
                        <div id="containerHead">
                                <div id="triangle"> </div>
                                <span>My Settings</span>
                        </div>

                    <form id="settingValues" name="settingValues" action="<?= $_SERVER['PHP_SELF'] ?>" method="post"> 

                                <input type="hidden" name="deactivatedValue" id="deactivatedValue"/>
                                <input type="hidden" name="curriculumValue" id="curriculumValue"/>
                                <input type="hidden" name="durationValue" id="durationValue"/>
                                <input type="hidden" name="mpiValue" id="mpiValue"/>
                                <input type="hidden" name="submitValue1"  id="submitValue1" value='false'/>
                            <div id="containerBody">
                                <p class="tabs" id="deactivatedTopicsImage">&#10147; Allow deactivated Topics at Home <label style="float:right; font-size:0.8em"> <?=$deactivatedValue?></label></p>
                                <div id="deactivatedTopics" style=" display:none;"   class="tabs-inner deactivatedTopicsImage" >
                                <p>Allow your students to access deactivated topics at home.</p>
                                
                                <span style="white-space: nowrap; display: inline-block;">
                                <label>  
                                <input type="radio" class="radioClass" name="ClassSchoolDeactivate" value="School" 
                                      <?php 
                                              if($tabValues['deactivatedValue'] == "On")
                                                               echo 'checked="checked"'; 
                                              else if($tabValues['deactivatedValue'] == "Off")
                                                                echo 'checked="checked" disabled="true"'; 
                                      ?>
                                                            >Entire School
                                <br>  
                                </label>  
                                </span>

                                <span style="white-space: nowrap; display: inline-block; vertical-align: middle; position:relative;"> 
                                <label id="sliderLabel"> 
                                <input id="deactivatedTopicCheck" type="checkbox" 

                                 <?php  

                                    if($tabValues['deactivatedValue'] == "CustomOn"|| $tabValues['deactivatedValue'] == "CustomOff"){
                                                              echo 'checked="checked" disabled="true"';
                                    }
                                    else if($tabValues['deactivatedValue'] != "Off")
                                                     echo 'checked="checked"';
                                      ?>

                                />

                                <span id="slider">Off</span>                         
                                <span id="sliderOnDeactivate"
                                 <?php
                                     if($tabValues['deactivatedValue'] == "CustomOn"|| $tabValues['deactivatedValue'] == "CustomOff")
                                                               echo 'style="background-color: rgb(103, 103, 103)"';          
                                      
                                ?>
                                >On</span>                                           
                                </label>                         
                                </span>   <br>

                                <label>
                                <input type="radio" class="radioClass" name="ClassSchoolDeactivate" value="Class"

                                 <?php 
                                              if($tabValues['deactivatedValue'] == "CustomOn"|| $tabValues['deactivatedValue'] == "CustomOff")
                                                               echo 'checked="checked"'; 
                                              else if($tabValues['deactivatedValue'] == "Off")
                                                                echo 'disabled="true"'; 
                                      ?>



                                >Individual Classes 
                                </label>
                                <br>
                            <div class="classDisplay"> 
                                <div id="classDisplayDeactivate" 
                                 <?php 

                                  if($tabValues['deactivatedValue'] == "CustomOn" || $tabValues['deactivatedValue'] == "CustomOff")
                                    echo 'style="display:block;"'; 
                                  else 
                                    echo 'style="display:none;"'; 
                                ?> >
                                  <h3>Allow Deactivated Topics at Home</h3>
                                  <u>Click on the class divisions to deactivate them.</u><br><br>
                									<?php 
                                    foreach($finalClassSectionArray as $keyClass => $finalClassSection){
                                      $allSectiond = explode(",",$finalClassSection);
                                      $thisClassSettingData=array();
                                      foreach($settingDataArray['data'] as $classSection){
                                        if ($classSection['settingName']=='deactivatedTopicsAtHome' && $classSection['class']==$keyClass)
                                          $thisClassSettingData[$classSection['section']]=$classSection;
                                      }
                                      ?>
                                      <div style="font-weight:bold;">Class<?=$keyClass;?> </div>
                                      <?php
                                      foreach($allSectiond as $section){
                                        $thisClassSectionData=$thisClassSettingData[$section];
                                        if($thisClassSectionData['settingValue'] == "CustomOff"){ 
                                          $isChecked = 0;
                                        }
                                        else{
                                          $isChecked = 1;
                                        }
                                        ?>
                                        <label class="sliderLabelSmall">
                                          <input type="checkbox" <?= $isChecked == 1 ?" checked ":"";?>class="checkSelect" name="sectionValueDeactivate[<?=$keyClass.$section?>]" id="<?=$keyClass.$section?>">
                                          <span style="font-size: 10px" id="slider">
                                          <?php
                                          if($section == "")
                                            echo "Class-".$keyClass;
                                          else
                                            echo $section;
                                          ?>
                                          </span>
                                          <span style="font-size: 11px" id="sliderOn">
                                          <?php
                                          if($section == "")
                                            echo "Class-".$keyClass;
                                          else
                                            echo $section;
                                          ?></span>
                                        </label>
                                        <?php
                                      }
                                    }
                									?>
                									
                                </div>
                            </div>
                        </div>


              
              <p class="tabs" id="chooseCurriculumImage" style="position:relative">&#10147; Choose Curriculum  <label style="float:right; font-size:0.8em"><?=$curriculumValue?></label></p>
             <div id="chooseCurriculum" style="display:none; " class="tabs-inner chooseCurriculumImage" >
                 <p> The default curriculum you want students to use in Mindspark.</p>
                 <?php $tabValues['curriculumValue']; ?>
                 <input id="ms" type="radio" name="curriculum" value="MS" <?php if($tabValues['curriculumValue'] == "MS") echo 'checked="checked"'; ?> onclick="saveCurriculum('MS');">Mindspark
                <input id="cbse" type="radio" name="curriculum" value="CBSE" <?php if($tabValues['curriculumValue'] == "CBSE") echo 'checked="checked"'; ?> onclick="saveCurriculum('CBSE');">CBSE
                <input id="icse" type="radio" name="curriculum" value="ICSE" <?php if($tabValues['curriculumValue'] == "ICSE") echo 'checked="checked"'; ?> onclick="saveCurriculum('ICSE');">ICSE
                <input id="igcse" type="radio" name="curriculum" value="IGCSE" <?php if($tabValues['curriculumValue'] == "IGCSE") echo 'checked="checked"'; ?>  onclick="saveCurriculum('IGCSE');">IGCSE
                <br>
         
           
              </div>
                                    
                                 

        <p class="tabs" id="sessionDurationImage" style="position:relative">&#10147; Session Duration <label style="float:right; font-size:0.8em"><?=$durationValue?> minutes</label></p>
           <div id="sessionDuration" style=" display:none; "   class="tabs-inner sessionDurationImage">
              <p>The length of a Mindspark session in your school.</p>
              <input id="sliderValue" type="text" name="duration" data-slider="true" value="<?=$tabValues['durationValue']?>" data-slider-range="20,90" data-slider-step="1" data-slider-snap="true">
              
              <span id="toChange" class="output">
              <?=$tabValues['durationValue']?>
              </span><span> minutes</span> 
              <br>
              <p>We recommend 40 minutes.</p>
          </div>


        
              <p class="tabs" id="mpiImage" style=" position:relative">&#10147; Mindspark Performance Report <label style="float:right; font-size:0.8em">On</label></p>
              <div id="mpi" style=" display:none; "  class="tabs-inner mpiImage">
               <!-- <p> Mindspark Performance Report shows how well students use Mindspark.</p>  

              <span style="white-space: nowrap; display: inline-block;">  
              <label>  
              <input type="radio" class="radioClass" name="ClassSchoolmpi" value="School" checked="checked"

               <?php 
                                    if($tabValues['mpiValue'] == "On")
                                                     echo 'checked="checked"'; 
                                    else if($tabValues['mpiValue'] == "Off")
                                                      echo 'checked="checked" disabled="true"'; 
                            ?> 

              >Entire School
              <br>  
              </label>  
              </span>

              <span style="white-space: nowrap; display: inline-block; vertical-align: middle; position:relative;"> 
              <label id="sliderLabelmpi"> 
              <input id="mpiCheck" type="checkbox"

              <?php 
                                    if($tabValues['mpiValue'] == "CustomOn"|| $tabValues['mpiValue'] == "CustomOff"){
                                                              echo 'checked="checked" disabled="true"';
                                    }
                                    else if($tabValues['mpiValue'] != "Off")
                                                     echo 'checked="checked"';
                                    
                                                              
              ?>
              />
              <span id="slider">Off</span>                         
              <span id="sliderOnmpi"
              <?php
                   if($tabValues['mpiValue'] == "CustomOn"|| $tabValues['mpiValue'] == "CustomOff")
                                             echo 'style="background-color: rgb(103,103,103)"';          
                    
              ?>
              >On</span>                         
                                   
              </label>                         
              </span>   <br>
              <label>
              <input type="radio" class="radioClass" name="ClassSchoolmpi" value="Class"

              <?php 
                                    if($tabValues['mpiValue'] == "CustomOn"|| $tabValues['mpiValue'] == "CustomOff")
                                                     echo 'checked="checked"'; 
                                    else if($tabValues['mpiValue'] == "Off")
                                                      echo 'disabled="true"'; 
              ?>

              >Individual Classes
              </label>
              <br> -->
              <table id="weightages">
                <caption class="heading">Weightage for Parameters</caption>
                <tbody>
                  <?php foreach($tabValues['mpiValue']['weightages'] as $parameter => $weightage) { ?>
                  <tr class="parameter">
                    <td class="name"><?=$parameter?></td>
                    <td class="weightage">
                      <input type="text" class="mpiSettingValue" name="<?=$parameter?>" value="<?=$weightage?>" onkeypress="return allowOnlyDigits(event);" autocomplete="off">
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
              <table id="others">
                <tbody>
                  <?php
                    $units = array(
                      'Minimum weekly usage' => 'min',
                      'Minimum weekly question attempts' => 'Qs',
                    );
                    foreach($tabValues['mpiValue']['others'] as $setting => $value) {
                  ?>
                  <tr class="setting">
                    <td class="name"><?=$setting?></td>
                    <td class="value">
                      <input type="text" class="mpiSettingValue" name="<?=$setting?>" value="<?=$value?>" onkeypress="return allowOnlyDigits(event);" autocomplete="off">
                      <span class="unit"><?=$units[$setting]?></span>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
           <!-- <div class="classDisplay">   
              <div id="classDisplaympi" 

              <?php 
                    if($tabValues['mpiValue'] == "CustomOn"|| $tabValues['mpiValue'] == "CustomOff")
                                     echo 'style="display:block;"'; 
                    else 
                                      echo 'style="display:none;"'; 
              ?>

              >             
                                 
 
                                        
										
										<h3>Mindspark Performance Report</h3>
                    <u>Click on class divisions to deselect them.</u><br><br>

                    <?php 
                      foreach($finalClassSectionArray as $keyClass => $finalClassSection){
                        $allSectiond = explode(",",$finalClassSection);
                        $thisClassSettingData=array();
                        foreach($settingDataArray['data'] as $classSection){
                          if ($classSection['settingName']=='mpi' && $classSection['class']==$keyClass)
                            $thisClassSettingData[$classSection['section']]=$classSection;
                        }
                        ?>
                        <div style="font-weight:bold;">Class<?=$keyClass;?> </div>
                        <?php
                        foreach($allSectiond as $section){
                          $thisClassSectionData=$thisClassSettingData[$section];
                          if($thisClassSectionData['settingValue'] == "CustomOff"){ 
                            $isChecked = 0;
                          }
                          else{
                            $isChecked = 1;
                          }
                          ?>
                          <label class="sliderLabelSmall">
                            <input type="checkbox" <?= $isChecked == 1 ?" checked ":"";?>class="checkSelect" name="sectionValuempi[<?=$keyClass.$section?>]" id="<?=$keyClass.$section?>">
                            <span style="font-size: 10px" id="slider">
                            <?php
                            if($section == "")
                              echo "Class-".$keyClass;
                            else
                              echo $section;
                            ?>
                            </span>
                            <span style="font-size: 11px" id="sliderOn">
                            <?php
                            if($section == "")
                              echo "Class-".$keyClass;
                            else
                              echo $section;
                            ?></span>
                          </label>
                          <?php
                        }
                      }
                    ?>
							                    
                    </div>
            </div> -->
      
                            </div>
                    </form>
                </div>
             
        </div>
         <br>


        <div id="saveContainer" style="padding-left:1%; ">
             <input type="submit" class="btn" id="saveAll" name="submit" value="Save Settings" onclick="saveDetails();">
             <div class="loader"></div>
        </div>

<?php
include("footer.php");
?>


<?php

// Functions 

function show_data($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    
}
function show_data_exit($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    exit();
}

function rearrange_final_class_section_array($classArray, $sectionArray){

          $finalClassSectionArray = array();
          for ($i = 0; $i < count($classArray); $i++) {
           /* if(empty($sectionArray[$i]))
                $sectionArray[$i] = "Class-".$classArray[$i];*/
            $finalClassSectionArray[$classArray[$i]] = $sectionArray[$i];
          }

        return $finalClassSectionArray;
}

function get_saved_data_of_setting_page($schoolCode)
{
    
    $userInterfaceSettingsClasses = array();
    $school_divisions = "SELECT  section,class, schoolCode, settingName,settingValue from userInterfaceSettings where schoolCode = '$schoolCode' ORDER BY class, section";
    
    $data = mysql_query($school_divisions) or die(mysql_error());
    
    while ($row = mysql_fetch_assoc($data)) {
        
        $userInterfaceSettingsClasses['data'][] = array(
            "class" => $row['class'],
            "section" => $row['section'],
            "settingName" => $row['settingName'],
            "settingValue" => $row['settingValue'],
        );
        
    }
	

    return $userInterfaceSettingsClasses;
}

function is_school_class_section_available_in_userInterfaceSettings($schoolCode,$finalClassSectionArray,$userName)
{
  global $settingDefaults;
  foreach ($finalClassSectionArray as $class => $value) {
      $sections = array();
      $section = convert_string_to_array($value);
      foreach ($section as $division) {
        $settingQ='';
        foreach ($settingDefaults as $settingName => $value) {
          $settingQ.=", MAX(IF(settingName='$settingName',1,0)) $settingName ";
        }
        $query = "SELECT schoolCode $settingQ from userInterfaceSettings where schoolCode = '$schoolCode' AND class='$class' AND section='$division'";
        $data = mysql_query($query) or die(mysql_error());
        $row = mysql_fetch_assoc($data);

        $pushSettings=array();
        foreach ($settingDefaults as $settingName => $value) {
          if ($row[$settingName]==0) $pushSettings[$settingName]=$value;
        }
        if (count($pushSettings)>0) 
          insertClassSectionSetting($schoolCode,$class,$division,$userName,$pushSettings);
      }
  }
}

function convert_string_to_array($comma_separated_sections)
{
    $return_array = array();

        $return_array = explode(',', $comma_separated_sections);


    return $return_array;
}

/*function get_classes_and_section_array_from_school_code($schoolCode)
{
    $return_array = array();
    $query        = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection) as childSection
                                       FROM     adepts_userDetails
                                       WHERE    schoolCode=$schoolCode AND category='STUDENT' AND subcategory='School' AND enabled=1 AND endDate>=curdate()  AND subjects like '%" . SUBJECTNO . "%'
                                       GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
    
    $result = mysql_query($query) or die(mysql_error());
    
    while ($row = mysql_fetch_assoc($result)) {
        $childClass                       = $row['childClass'];
        $childSectionCommaSeparatedString = $row['childSection'];
        $return_array[$childClass]        = convert_string_to_array($childSectionCommaSeparatedString);
        
    }
    return $return_array;
}*/

function execute_insert_and_update_on_userInterfaceSettings($finalClassSectionArray,$schoolCode,$userName){

  $dectivatedValuePOST = $_POST["deactivatedValue"];
  $mpiValuePOST =$_POST["mpiValue"];

  $new_entry = "UPDATE userInterfaceSettings set settingValue='".$_POST['curriculum']."',lastModifiedBy='$userName'
                 where settingName='curriculum' and schoolCode='$schoolCode'";
  mysql_query($new_entry) or die(mysql_error());

  $new_entry = "UPDATE userInterfaceSettings set settingValue='" . $_POST['duration']."',lastModifiedBy='$userName'
                 where settingName='sessionDuration' and schoolCode='$schoolCode'";
  mysql_query($new_entry) or die(mysql_error());

  $new_entry = "UPDATE userInterfaceSettings set settingValue='" . $_POST['mpiValue']."',lastModifiedBy='$userName'
                 where settingName='mpi' and schoolCode='$schoolCode'";
  mysql_query($new_entry) or die(mysql_error());

  $updateDurationValue = "UPDATE adepts_userDetails set timeAllowedPerDay='".$_POST['duration']."' where schoolCode='$schoolCode' and category='Student' and subcategory='School'";
  mysql_query($updateDurationValue) or die(mysql_error()); 

  foreach ($finalClassSectionArray as $key => $value) {
  
    $sections = array();
    $section = convert_string_to_array($value);
     
    foreach ($section as $division) {
      $flagForPost = false;
      $flagMPI = false;

      if($_POST["deactivatedValue"] == "Custom"){
        foreach ($_POST['sectionValueDeactivate'] as $id => $value) {
          if($id == $key.$division){
            if ($value == "on"){ 
              $dectivatedValuePOST = "CustomOn";
              $flagForPost = true;
            }     
          } 
        }
      } 

      if($flagForPost == true){
           $flagForPost= false; 
      }else{
          $dectivatedValuePOST = "CustomOff";
      }

      $new_entry = "UPDATE userInterfaceSettings set settingValue='".$dectivatedValuePOST."',lastModifiedBy='$userName'
                          where settingName='deactivatedTopicsAtHome' and schoolCode='$schoolCode' and class='$key' and section='$division' ";
      mysql_query($new_entry) or die(mysql_error());//echo $new_entry;

      if($_POST['deactivatedValue'] != "Custom"){
        $new_entry = "UPDATE userInterfaceSettings set settingValue='".$_POST['deactivatedValue']."',lastModifiedBy='$userName'
                          where settingName='deactivatedTopicsAtHome' and schoolCode='$schoolCode' and class='$key' and section='$division' ";
        mysql_query($new_entry) or die(mysql_error());//echo $new_entry;
      }

      /*if($_POST["mpiValue"] == "Custom"){
          foreach ($_POST['sectionValuempi'] as $id => $value) {
            if($id == $key.$division){
                if ($value == "on"){ 
                  $mpiValuePOST = "CustomOn";
                  $flagMPI = true;
               }        
            }        
          }
      } 

      if($flagMPI == true){
        $new_entry = "UPDATE userInterfaceSettings set settingValue='" .$mpiValuePOST."',lastModifiedBy='$userName'
                                       where settingName='mpi' and schoolCode='$schoolCode' and class='$key' and section='$division' ";
        mysql_query($new_entry) or die(mysql_error());
        $flagMPI= false;
      }
      else{
        $mpiValuePOST = "CustomOff";
        $new_entry = "UPDATE userInterfaceSettings set settingValue='" .$mpiValuePOST."',lastModifiedBy='$userName'
                                     where settingName='mpi' and schoolCode='$schoolCode' and class='$key' and section='$division' ";
        mysql_query($new_entry) or die(mysql_error());
      }

      if($_POST['mpiValue'] != "Custom"){
        $new_entry = "UPDATE userInterfaceSettings set settingValue='".$_POST['mpiValue']."',lastModifiedBy='$userName'
                          where settingName='mpi' and schoolCode='$schoolCode' and class='$key' and section='$division' ";
        mysql_query($new_entry) or die(mysql_error());
      }      */
    }
  }
}




function get_form_display_values_from_userInterfaceSettings($settingDataArray){

      $onLoadTabValue = array();

      foreach ($settingDataArray['data'] as $key => $value) {

            if ($value['settingName'] == "deactivatedTopicsAtHome"){

                  $onLoadTabValue['deactivatedValue'] = $value['settingValue'];
    
    
                    
            }
            
            else if ($value['settingName'] == "sessionDuration"){

                  $onLoadTabValue['durationValue'] = $value['settingValue'];

            }
            else if ($value['settingName'] == "curriculum"){

                  $onLoadTabValue['curriculumValue'] = $value['settingValue'];

                  

            }else if ($value['settingName'] == "mpi"){

                  $onLoadTabValue['mpiValue'] = json_decode($value['settingValue'], true);

            }
      } 
      return $onLoadTabValue;

}


?> 