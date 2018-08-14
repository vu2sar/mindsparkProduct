
<?php
Class simulationContentFlowlogic_model extends CI_Model{
      public function __construct(){
        parent::__construct();
        $this->dbEnglish = $this->load->database('mindspark_english',TRUE);
        $this->load->library('session');
        $this->class=0;     
      }

  public function contentFlowsimulation($userID){
    $result=$this->getChildClass($userID);
    if(!$result) :
      echo 'Invalid UserName'; exit;
    else :
      $this->class = $result;
      $this->questionAttemptClassTbl="questionAttempt_class".$this->class->childClass;
    endif;
    $userInformation = $this->user_model->getUserData($userID);
    $userCurrentStatus = $this->currentOngoingQtype($userID); 
    $contentStatus= $userCurrentStatus[0]['completed'];
    $contentType = $userCurrentStatus[0]['currentContentType'];
    $curContentType = $userCurrentStatus[0]['currentContentType'];
    $getCurrentPassagetype = $this->getCurrentPassagetype($userInformation['refID']);
    $valType= $this->contentType($getCurrentPassagetype ->passageType);    
    $getPsgIdForPsgQstns=$this->getPsgIdForPsgQstns($userInformation['currentContentType'],$userInformation['refID']);
    //--------------------------------User information ---------------------------------------//

    echo "<div style='margin: auto; width: 80%; border: solid;  padding: 20px;'>";
    echo "<h3 align=center>A User's Content Flow in Classroom</h3>";
    echo "<table align='center' width=70%  border=1 cellspacing=0 cellpadding=5 style=text-align:center>";
    echo "<tr><th colspan=8>Current Status Information of the User</th></tr>";
    echo " <tr>
        <th>User ID</th> <th>School Code</th> <th>Present Attempt Type</th> <th>* Present Attempt ID</th><th>* Present Attempt Status</th><th>Grade</th>
    </tr>";
    echo'<tr>'; 
          echo'<td>'. $userInformation['userID']."</td>";
          echo'<td>'. $userInformation['schoolCode'].'</td>';
          echo'<td>'. $userInformation['currentContentType'].'</td>';
          echo'<td>'. $getPsgIdForPsgQstns .'</td>';
          echo'<td>'. $contentStatus.'</td>';
          echo'<td>'. $userInformation['childClass'].'</td>';
          echo'</tr></table>';
          echo "<ul><li> <b>Present Attempt ID: </b>Will be PassageID if the user is currently reading Passage otherwise the value is qcode.</li>";
    echo "<li> <b>Present Attempt Status:</b> 0 if not completed otherwise value is 1</li></ul>";
      echo '<img width="50px" style="margin-left: 450px;" height="50px" src="http://pixsector.com/cache/0688783e/avbf566659ab2bdf82f87.png">';        
  
    //---------------------------------- Currnet Content Flow Master ------------------------------------// 
    $userLastAttemptArr  = array();
    $contentFlowOrder=$this->GetContentFlowOrder($userID,$currentContentType);
        ?>
         <html>
              <body>
                   <table align="center" border=1 cellspacing=0 cellpadding=5 width=70% style="text-align:center">
                    <tr>
                          <th colspan="3">Generic Content Flow </th>
                     </tr>
                     <tr>
                        <th>Content Type</th>
                        <th>Content Quantity</th>
                        <th>Content Order</th>
                      </tr>
                     <?php
                        foreach ($contentFlowOrder as $key => $value) {
                          $contentType=ucfirst($value->contentType);
                              $contentQuantity=$value->contentQuantity;
                          $contentOrder=$value->contentOrder;
                     ?>
                     <tr>
                        <td><?php echo $contentType ?></td>
                        <td><?php echo $contentQuantity ?></td>
                        <td><?php echo $contentOrder ?></td>
                      </tr>
                     <?php
                        }
                      ?>
                        </tr>
               </table>
              </body>
             </html>
             <?php            
              echo "<ul><li> reading is abbreviated as 'R'</li>";     
    echo "<li> freeques is abbreviated as 'F'</li>";    
    echo "<li>conversation is abbreviated as 'C'</li>";     
          echo '<img width="50px" style="margin-left: 410px;" height="50px" src="http://pixsector.com/cache/0688783e/avbf566659ab2bdf82f87.png">';
      if($userInformation['currentContentType'] == 'N/A' && $userInformation['refID']== 0):
        array_push($userLastAttemptArr, 'N/A');
        array_push($userLastAttemptArr, 'N/A');
      else:
           $passageTypeAttempt =$this->getUserLastAttmpt($userID);
           $userLastAttemptArr  = array();
           $curQuestionType = $userInformation['currentContentType'];
           if(count($passageTypeAttempt)==2){
                foreach ($passageTypeAttempt as $row){              
                     $valType= $this->contentType($row['passageType']);
                     array_push($userLastAttemptArr, $valType);
                }
                $userLastAttemptArr=array_reverse($userLastAttemptArr);
                $tmpLastToLastAttempt=$userLastAttemptArr[0];
                $tmpLastAttempt=$userLastAttemptArr[1];
                if($curContentType=='passage'){
                     $userLastAttemptArr[0]=$tmpLastAttempt;
                     $getCurrentPassagetype = $this->getCurrentPassagetype($userInformation['refID']);
                     $valType= $this->contentType($getCurrentPassagetype ->passageType);
                     $userLastAttemptArr[1]=$valType;
                }else if ($curContentType=='passage_ques') {       
                     $refCodeDetails =$this->getQcodePassageDetails($curRefID);
                     if($refCodeDetails['qcodePassageID']!=0){ 
                           $currentPassageID=$refCodeDetails['qcodePassageID'];
                           $checkPsgQuesAttempt=$this->userAttemptedPsgQues($currentPassageID,$userID);
                           if($checkPsgQuesAttempt){
                                $userLastAttemptArr[0]=$tmpLastToLastAttempt; 
                           }else{
                                $userLastAttemptArr[0]=$tmpLastAttempt;              
                           }
                           if($curQuestionType=='Conversation'){
                                $userLastAttemptArr[1]="C";
                           }else if($curQuestionType=='Reading'){
                                $userLastAttemptArr[1]="R";
                           }
                     }   
                }else if ($curContentType=='free_ques'){                                                
                     if($tmpLastAttempt=="F"){
                           $userLastAttemptArr[0]=$tmpLastToLastAttempt;        
                     }else{
                           $userLastAttemptArr[0]=$tmpLastAttempt;
                     }
                     $userLastAttemptArr[1]="F";                               
                }                                              
           }else if(count($passageTypeAttempt)==1){   
                $userLastAttemptArr  = array();
                array_push($userLastAttemptArr, 'NA');                            
                array_push($userLastAttemptArr,$this->contentType($passageTypeAttempt[0]['passageType']));
                $tmpLastAttempt=$userLastAttemptArr[1];
                if($curContentType=='passage'){
                     $checkPsgQuesAttempt=$this->userAttemptedPsgQues($curRefID,$userID);
                     if($checkPsgQuesAttempt){
                           $userLastAttemptArr[0]='NA';    
                           $userLastAttemptArr[1]=$tmpLastAttempt;                      
                     }else{
                          $userLastAttemptArr[0]=$tmpLastAttempt;
                           $userLastAttemptArr[1]=$valType;                                 
                      }                                                      
                }else if ($curContentType=='passage_ques'){        
                     $refCodeDetails =$this->getQcodePassageDetails($curRefID);
                     if($refCodeDetails['qcodePassageID']!=0){
                          $currentPassageID = $refCodeDetails['qcodePassageID'];
                          $checkPsgQuesAttempt=$this->userAttemptedPsgQues($currentPassageID,$userID);
                           if($checkPsgQuesAttempt){    
                                $userLastAttemptArr[0]='NA';    
                                $userLastAttemptArr[1]=$tmpLastAttempt;           
                           }else{
                                $userLastAttemptArr[0]=$tmpLastAttempt;
                                if($curQuestionType=='Conversation'){
                                     $userLastAttemptArr[1]="C";
                                }else if($curQuestionType=='Reading'){
                                     $userLastAttemptArr[1]="R";
                                };                                                                                             
                           }                                                                              
                      }                                                              
                }else if ($curContentType=='free_ques'){
                     $userLastAttemptArr[0]=$tmpLastAttempt;
                     $userLastAttemptArr[1]="F";                                                                      
                }
     }
                          else if(count($passageTypeAttempt)==0){                                                              
                                      if($curContentType=="N/A"){
                                                   array_push($userLastAttemptArr, 'N/A');
                                                   array_push($userLastAttemptArr, 'N/A');
                                      }
                                      else{

                                                   array_push($userLastAttemptArr, 'NA');
                                                   if($curContentType=='passage' || $curContentType=='passage_ques')
                                                   {
                                                                if($curQuestionType=='Conversation'){
                                                                       array_push($userLastAttemptArr, "C");
                                                                }
                                                                else if($curQuestionType=='Reading'){
                                                                  array_push($userLastAttemptArr, "R");
                                                                }

                                                                else
                                                                {
                                                                    $getCurrentPassagetype = $this->getCurrentPassagetype($userInformation['refID']);
                                                                    $valType= $this->contentType($getCurrentPassagetype ->passageType);
                                                                    array_push($userLastAttemptArr, $valType);
                                                                }



                                                   }
                                                   else if ($curContentType=='free_ques')
                                                   {       
                                                    
                                                                 array_push($userLastAttemptArr, "F");
                                                   }
                                      }
                         }
     endif;         
     echo "<table align='center' align='center' border=1 cellspacing=0 cellpadding=5 width=70% style=text-align:center>";
     echo "<tr><th colspan=2>User Attempt Data</th></tr>";
     echo "<tr>";
      echo "<th>Last Attempt</th>";
      echo "<th>Current Attempt</th>";    
      echo "</tr>";
      echo'<tr>'; 
      echo'<td>'. $userLastAttemptArr[0]."</td>";
      echo'<td>'. $userLastAttemptArr[1].'</td>';         
      echo'</tr></table>';
      $contentFlowArr =  $this->getMaxContentOrders();
      $counter = 0;
      $finalContentFlowArr=$this->nextContentOrderFlow($contentFlowArr,$userInformation,$userLastAttemptArr);
      $finalContentArr=  implode($finalContentFlowArr, " -> ");
      echo '<img width="50px" style="margin-left: 410px;" height="50px" src="http://pixsector.com/cache/0688783e/avbf566659ab2bdf82f87.png">';
      echo "<table border=1 align='center' cellspacing=0 cellpadding=5 width=60% style=text-align:center>";
      echo "<tr><th colspan=2>User Specific Next Content Flow</th></tr>";
      echo'<tr>'; 
      echo'<td><font size="5">'. $finalContentArr."</font></td>";               
      echo'</tr></table>';  

  
        //----------------------------------- Free Question Flow -----------------------------------//
      $schoolCode = $userInformation['schoolCode'];
      $childClass = $userInformation['childClass'];
      $groupSkillID = $userInformation['groupSkillID'];       
      $currentBunching=$this->nextSchoolBunchingOrder($schoolCode,$childClass,$groupSkillID); 
      if($currentBunching):
          $currentBunchingData = implode(',', $currentBunching); 
          echo '<img width="50px" style="margin-left: 410px;" height="50px" src="http://pixsector.com/cache/0688783e/avbf566659ab2bdf82f87.png">';    
          echo "<table border=1 align='center' cellspacing=0 cellpadding=5 width=50% style=text-align:center>";
          echo "<tr><th colspan=4>Bunch Order for Free Questions[Based On User's School and Class]</th></tr>";
          echo'<tr>';   
              echo'<td>'. $currentBunchingData.'</td>';         
          echo'</tr></table>';
            //-------------------------- New Bunching Flow for Free question -----------------//
           echo '<img width="50px" style="margin-left: 410px;" height="50px" src="http://pixsector.com/cache/0688783e/avbf566659ab2bdf82f87.png">';
      $bunchNextflow = $this->getCurrentBunchID($userID);
      $currentBunchingFlow = $bunchNextflow[0]->bunchID;
      if($currentBunchingFlow!=0){
        $arrLast = array_slice($currentBunching, array_search($currentBunchingFlow,$currentBunching)+1);
        $arrInit = array_slice($currentBunching, 0,array_search($currentBunchingFlow,$currentBunching)+1);
        $lastbunchFlowArr  = $bunchNextflow[0]->bunchID;
        $finalbunchFlowArr = implode(array_merge($arrLast,$arrInit),",");
        $getQcode=$this->getRecentAttmptdFreeQstns($userID);
        $questionsAttempted=$this->SplitArrayValue($getQcode);
        $currentBunchingID = $currentBunchingFlow;
        //$nextFreeQuestion=$this->nextFreeQuestion($this->questionAttemptClassTbl,$childClass,$userID,$currentBunchingFlow);
      }else{
        $lastbunchFlowArr  = $currentBunchingData[0];
        $arrLast = array_slice($currentBunching, array_search($currentBunchingFlow,$currentBunching)+1);
        $arrInit = array_slice($currentBunching, 0,array_search($currentBunchingFlow,$currentBunching)+1);
        $finalbunchFlowArr = implode(array_merge($arrLast,$arrInit),",");
        $currentBunchingID = $currentBunchingFlow;
      }
  //------------------------------------- Pending Qcodes Flow --------------------------------//

    $pending_qcode = "";    
    $nextQcodesToBeAttempted=$this->nextQcodesToBeAttempted($userID,$userInformation,$this->questionAttemptClassTbl,$newIndex,$userLastAttemptArr[0],$userLastAttemptArr[1],$currentBunchingID,$currentBunchingData,$userInformation['currentContentType'] );    
    if($nextQcodesToBeAttempted && $nextQcodesToBeAttempted!='N/A'):
      $freeQstnCount=count(explode(',', $nextQcodesToBeAttempted));
    else:
      $freeQstnCount=0;
    endif;
    $getRecentAttmptdFreeQstns = $this->getRecentAttmptdFreeQstns($userID); 
    $questionsAttempted = $this->SplitArrayValue($getRecentAttmptdFreeQstns);
    echo "<table align='center' border=1 cellspacing=0 cellpadding=5 width=50% style=text-align:center;word-wrap:break-word;>";
    echo "<tr>";
      echo "<th >Pending Questions in a Passage / Free Questions*</th>";
      echo "<th >No of Free Questions Pending</th>";
    echo "</tr>";
    echo "<tr>";    
      echo "<td><div style='width: 400px';>". $nextQcodesToBeAttempted."</div></td>";
      echo "<td>".$freeQstnCount ."</td>";    
    echo "</tr>";
    echo "</table>";

  // ------------------------------------Next Content Order Flow -------------------------------//
    echo "* Pending Free Questions are Fetched based on Bunches";
    echo "<br>";
    echo "<table align='center' border=1 cellspacing=0 cellpadding=5 width=80% style=text-align:center>";
    echo '<img width="50px" style="margin-left: 410px;" height="50px" src="http://pixsector.com/cache/0688783e/avbf566659ab2bdf82f87.png">'; 
    echo "<tr><th colspan=4>Bunch Attempt Details of the User </th></tr>";
    echo "<tr>";
    echo "<th>Current Bunch</th>";
    echo "<th>Next Bunch Order Flow</th>";
    echo "<th>Recently Attempted QCodes</th>";
      //Commented by Arun
      //echo "<th>Next Free Question To Be Attempted</th>";
    echo "</tr>";
    echo'<tr>'; 
    echo'<td>'. $lastbunchFlowArr."</td>";
    echo'<td> <h2>'.$finalbunchFlowArr.'</h2></td>';       
    echo'<td>'. $questionsAttempted."</td>";      
    echo'</tr></table>';
    echo '<img width="50px" style="margin-left: 410px;" height="50px" src="http://pixsector.com/cache/0688783e/avbf566659ab2bdf82f87.png">'; 
    echo "<table align='center' border=1 cellspacing=0 cellpadding=5 width=40% style=text-align:center>";
    echo "<tr>";
    echo "<th colspan='2'>Next Qcode and Passage ID to be given*</th>"; 
    echo "</tr>";
    echo "<tr>";
    echo "<th>Content Type</th>"; 
    echo "<th>Passage ID / Qcode</th>"; 
    echo "</tr>";
    foreach ($finalContentFlowArr as $key => $value) {
    ?>
     <tr>
         <td><?php echo $value ?></td>
         <td><div style="width: 300px;word-wrap: break-word"> 
         <?php
         if($value=='R'):
           $result= $this->getNextContentFlowForReading($userID);
           echo $result;
         elseif($value=='C'):
           $result=$this->getNextContentFlowForConvrstn($userID);
                echo $result;
         else:
           $lastAtmptIndex=$this->getLastAtmptIndex($contentFlowArr,$userLastAttemptArr);
           $currentAtmptIndex=$lastAtmptIndex+1;
           $nextQcodesToBeAttempted=$this->nextQcodesToBeAttempted($userID,$userInformation,$this->questionAttemptClassTbl,$currentAtmptIndex,$userLastAttemptArr[0],$userLastAttemptArr[1],$currentBunchingID,$currentBunchingData,'free_ques');
           echo $nextQcodesToBeAttempted;
         endif;
              ?>
              </div></td>
            </tr>
            <?php
    } 
    $getRecentAttmptdFreeQstns = $this->getRecentAttmptdFreeQstns($userID); 
    $questionsAttempted = $this->SplitArrayValue($getRecentAttmptdFreeQstns);
            echo'</table>';            
            echo "<span class='help-block'> <b>F</b> - Qcode, <b>R/C</b> - passageID </span>";  
  else:
    echo '<h3>School code not found in bunching Table</h3>';
  endif;
  echo "</div>";
}

      //function to get the content flow details
     public function GetContentFlowOrder($userID,$currentContentType){
		$selectContentFlow=$this->dbEnglish->query("SELECT contentType,contentQuantity,contentOrder FROM contentFlowMaster where contentStatus='Yes' order by contentOrder asc");
		$this->class = $selectContentFlow->result();
		return $this->class ;
	}

  /*function to get passage id for passage questions*/
  public function getPsgIdForPsgQstns($currentContentType,$refID){
    if($currentContentType=='passage_ques'):
      $getPsgIdFromRefID=$this->getPsgIdFromRefID($refID);
      return $refID.'(qcode)  /   '.$getPsgIdFromRefID[0] -> passageID.'(PassageID)';
    else:
      return $refID;
    endif;
  }

  /* function to get current passage type*/
      public function getCurrentPassagetype($refID) {
          $passageSql = "SELECT passageType from passageMaster where passageID=$refID";
          $result=$this->dbEnglish->query($passageSql);
          $value = $result->row();
          return $value;
      }

  /*function to fetch child class of the user*/
  function getChildClass($userID){
    $query=$this->dbEnglish->query("select childClass from userDetails where userId='".$userID."'");
    $result=$query->row();
    return $result;
  }

  //function to fetch user next bunch
  function getCurrentBunchID($userID){
    $bunchNextflowSQL = "SELECT bunchID,userID FROM ".$this->questionAttemptClassTbl." where userID='$userID' and questionType = 'freeQues' order by attemptedDate desc limit 2";             
    return  $this->dbEnglish->query($bunchNextflowSQL)->result();
  }
  
  //function to fetch user's index of last attempt
  function getLastAtmptIndex($contentFlowArr,$userLastAttemptArr){
            $value = array_keys($contentFlowArr,$userLastAttemptArr[1]);
            $new_value1 = $value[0]-1;
            if($new_value1<0):
              $new_value1=sizeof($contentFlowArr)+$new_value1;
            endif;
            $new_value2 = $value[1]-1;
            $contentFlow1=$contentFlowArr[$new_value1];
            $contentFlow2=$contentFlowArr[$new_value2];
            if($contentFlow1==$userLastAttemptArr[0]) :
              $newIndex= $new_value1;
            elseif($contentFlow2==$userLastAttemptArr[0]) :
              $newIndex= $new_value2;
            endif;
            return $newIndex;
  }

  //function to get User Specific Next Content Flow
  function nextContentOrderFlow($contentFlowArr,$userInformation,$userLastAttemptArr){
    if($userInformation['currentContentType'] !="N/A"){
      if(count($userLastAttemptArr)>1){
        $newIndex=$this->getLastAtmptIndex($contentFlowArr,$userLastAttemptArr);
                if($newIndex):
                     $finalContentFlowArr=array_slice($contentFlowArr,$newIndex+2);
          if(count($finalContentFlowArr)) :
            $arrInit = array_slice($contentFlowArr, 0,array_search($userLastAttemptArr[1],$contentFlowArr)+1);
            $finalContentFlowArr = array_merge($finalContentFlowArr,$arrInit);
          else :
            $finalContentFlowArr = $contentFlowArr;
          endif;        
                else:
                     $index=array_search($userLastAttemptArr[1],$contentFlowArr)+1;
                     $finalContentFlowArr=array_slice($contentFlowArr,$index);
                     $arrInit = array_slice($contentFlowArr, 0,array_search($userLastAttemptArr[1],$contentFlowArr)+1);
             $finalContentFlowArr = array_merge($finalContentFlowArr,$arrInit);
                endif;
      }else{
        $finalContentFlowArr=array_slice($contentFlowArr,array_search($userLastAttemptArr[1], $contentFlowArr)+1);
        $arrInit = array_slice($contentFlowArr, 0,array_search($userLastAttemptArr[1],$contentFlowArr)+1);
        $finalContentFlowArr = array_merge($finalContentFlowArr,$arrInit);
      }
    }else{

        $finalContentFlowArr = $contentFlowArr;
    }
    return $finalContentFlowArr;  

  }
  
  //function to check the type of user's recent 2 attempts
    public function getUserLastAttmpt($userID){
        $querys = "SELECT b.passageTypeName as passageType FROM ".$this->questionAttemptClassTbl." a,questions b where a.qcode=b.qcode and a.userID='$userID' group by  b.passageID,b.passageTypeName order by MAX(a.attemptedDate) desc limit 2";
		$getLastAttemptByUser = $this->dbEnglish->query($querys);
		return $getLastAttemptByUser->result_array();
	}
  //function to get Recent 10 Free Questions Attempted by the user
  public function getRecentAttmptdFreeQstns($userID){
    $selectAttemptedQcode="select qcode as questionsAttempted from $this->questionAttemptClassTbl qa where passageID=0 and userID=".$userID."  order by attemptedDate desc limit 10";
    return  $this->dbEnglish->query($selectAttemptedQcode)->result_array();
  }
  //function to fetch questions attempted by the user
  public function qnstnsAttmptdByUser($qstnAtmptClss,$userID){
    $selectAtmptdQstn ="select count(distinct qcode) as questionsAttempted
        from $qstnAtmptClss qa where passageID=0 and userID=".$userID."";
    return $this->dbEnglish->query($selectAtmptdQstn)->row();
  }
  //function to get content quantity
  public function getContentQuantity($newIndex){
    $newvalue = $newIndex + 1;
    $selectContentQty ="SELECT contentQuantity FROM contentFlowMaster where contentOrder=$newvalue";
    return $this->dbEnglish->query($selectContentQty)->row();
  }
  //function to get passageID from refID
  public function getPsgIdFromRefID($refID){
    $selectPassageID=$this->dbEnglish->query("SELECT passageID FROM questions where qcode=".$refID."");
    return   $selectPassageID->result();
  }

  //function to fetch next qcodes to be attmptd
  public function nextQcodesToBeAttempted($userID,$userInfo,$qstnAtmptClss,$newIndex,$lastAttmpt,$currentAttempt,$currentBunch,$finalbunchFlowArr,$currentContentType){   
    if($currentContentType=='passage_ques'):
      $passageId= $this->getPsgIdFromRefID($userInfo['refID']);
      $getPassageId=$passageId[0]->passageID;
    elseif($currentContentType=='passage'):
      $getPassageId=$userInfo['refID'];
    elseif($currentContentType=='free_ques'):
      $newIndexVal = ($newIndex) ? $newIndex : 1 ;
      $getContentQty=$this-> getContentQuantity($newIndexVal);
      $nextFreeQuestionsInBunch=$this->nextFreeQuestionsInBunch($currentBunch,$getContentQty,$userID,$userInfo,$qstnAtmptClss,$finalbunchFlowArr);
      return implode($nextFreeQuestionsInBunch, ",");
    endif;
    if($getPassageId):
      $getQcodes =$this->getRemaningPsgQstns($getPassageId,$userID);
                if($getQcodes):
                  $passageqstnsNotAtmptd= $this->SplitArrayValue($getQcodes);
                else:
                  $passageqstnsNotAtmptd="N/A";
                endif;
              else:
                $passageqstnsNotAtmptd="N/A";

              endif;
              if($passageqstnsNotAtmptd):
                return $passageqstnsNotAtmptd;
              endif;
  }
  // function to fetch remaining passage questions to be given to the user
  public function getRemaningPsgQstns($passageID,$userID){
    $selectQcodes=$this->dbEnglish->query("select q.qcode from questions q  left join $this->questionAttemptClassTbl qa on qa.qcode=q.qcode and qa.userID=".$userID." and qa.passageID=".$passageID." where qa.qcode is null and  q.passageID=".$passageID." order by q.qcode") ;
              return  $selectQcodes->result_array();
  }
  //function to get next bunching order
  public function nextBunchingOrder($currentBunch) {
    $arrLast = array_slice($currentBunching, array_search($currentBunchingFlow,$currentBunching)+1);
    $arrInit = array_slice($currentBunching, 0,array_search($currentBunchingFlow,$currentBunching)+1);
    $finalbunchFlowArr = implode(array_merge($arrLast,$arrInit),","); 

  }
  //function to get next free questions in bunch
  public function nextFreeQuestionsInBunch($currentBunch,$contentQty,$userID,$userInfo,$qstnAtmptClss,$currentBunchingData) {
    $childClass = $userInfo['childClass'];
    $attmptsQstn='';
             $getAttmptdQstnCrntBnch= $this->selectAttmptdQstnCurrentBnchQuery($currentBunch,$userID);
              $attmptsQstn=$getAttmptdQstnCrntBnch->questionsAttempted;
              $attemptedBunchingIDCount = count(explode(',',$getAttmptdQstnCrntBnch->questionsAttempted));               
    $currentBunchingCount = $this->bunchingCounResult($currentBunch,$childClass);
    $currentUnattempted = $this->bunchingCounResult($currentBunch,$childClass,$getAttmptdQstnCrntBnch->questionsAttempted);
    $totalAttemptedFreeQA = $this->totalAttemptedFreeQA($userInfo['userID'],$qstnAtmptClss);
    $modValue = $totalAttemptedFreeQA->questionsAttempted % $contentQty->contentQuantity;
    $remingQty = $contentQty->contentQuantity - $modValue;

     if(!$attemptedBunchingIDCount):
      $result = explode(",", $currentBunchingData);
      $newBunchingID=$result[0];
    elseif($attemptedBunchingIDCount ==$currentBunchingCount) :     
      $newBunchingID = $this->findNextBunchID($currentBunch,$userInfo);
    else:
      $newBunchingID = $currentBunch;
    endif;

    $allQueueCodes = array();
    $bunchingLimit = 0;
    $finalQcodes = $this->bunchingNewQcode($remingQty,$newBunchingID,$childClass,$allQueueCodes,$attmptsQstn,$bunchingLimit,$userInfo);
    if($finalQcodes) :
      $bunchingFlowQcode = $finalQcodes;
    else :
      $getCurrentBunching = $this->getCurrentBunching($userID);
      $currentAttempFlowCount = $getCurrentBunching->attemptCount;
      $finalQcodes = $this->getLowestAttmptdFreeQcode($remingQty,$userID,$userInfo,$newBunchingID,$currentAttempFlowCount,$allQueueCodes);  
      $bunchingFlowQcode = $finalQcodes;        
    endif;
    return $bunchingFlowQcode;
  
  }
  //function to fetch users current bunching
  public function getCurrentBunching($userID){
    $SQL = "SELECT bunchID,attemptCount FROM ".$this->questionAttemptClassTbl." where userID='$userID' and questionType = 'freeQues' order by attemptedDate desc limit 1";              
    return  $this->dbEnglish->query($SQL)->row();
  }

  //function to get Attempted Bunching Qcode
  public function getAttemptedBunchingQcode($userID,$newBunchingID,$currentAttempFlowCount) {
    $nextFlowCount = $currentAttempFlowCount + 1;
    $SQL = "select qcode from ".$this->questionAttemptClassTbl." where userID=$userID and passageID=0 and bunchID=$newBunchingID and attemptCount =$nextFlowCount ";
    return  $this->dbEnglish->query($SQL)->result();  
  }

  public function bunchingFreelowLevelQcode($remingQty,$userID,$newBunchingID,$currentAttempFlowCount) {
    $getAttemptedBunchingQcode = $this->getAttemptedBunchingQcode($userID,$newBunchingID,$currentAttempFlowCount);
    $SQL = "select count(*) Count,qcode from ".$this->questionAttemptClassTbl." where userID=$userID and passageID=0 and bunchID=$newBunchingID ";
    if($getAttemptedBunchingQcode) :
      $attemptedArray = $this->SplitArrayValue($getAttemptedBunchingQcode);
      $SQL.= " and qcode not IN($attemptedArray)";
    endif;
    $SQL.= " group by qcode order by Count,attemptedDate limit $remingQty";
    return  $this->dbEnglish->query($SQL)->result_array();
  }

  //function to fetch lowest attmptd free qcode
  public function   getLowestAttmptdFreeQcode($remingQty,$userID,$userInfo,$newBunchingID,$currentAttempFlowCount,$allQueueCodes) {
    $allQueueCodes = array();
    $totalvalue = $this->bunchingFreelowLevelQcode($remingQty,$userID,$newBunchingID,$currentAttempFlowCount);
    if(is_array($totalvalue)) :
            foreach ($totalvalue as $key => $value) :
              $allQueueCodes[] = $value['qcode'];
            endforeach;
          endif;
    if(count($allQueueCodes) <= $remingQty):
      return $allQueueCodes;
    else :
      $newBunchingID = $this->findNextBunchID($currentBunch,$userInfo);
      $remingQty - count($allQueueCodes);
      return $this->getLowestAttmptdFreeQcode($remingQty,$newBunchingID,$childClass,$allQueueCodes,$bunchingLimit);
    endif;
  }


  //function to fetch users next qcodes to be attmptd
  public function userNextQcodesToBeAttmptd($newBunchingID,$childClass,$attmptsQstn,$remingQty){
    $bunchingCounResult="select qcode as bunchMasterqcode from bunchMaster where bunchID=".$newBunchingID." and childClass=".$childClass."";
    if($attmptsQstn):
      $bunchingCounResult.=" and qcode not in ($attmptsQstn)";
    endif;
    $bunchingCounResult.=" limit ".$remingQty."";
    return $this->dbEnglish->query($bunchingCounResult)->result_array();
  }

  //function to fetch bunching new qcode
  public function bunchingNewQcode($remingQty,$newBunchingID,$childClass,$allQueueCodes,$attmptsQstn,$bunchingLimit,$userInfo) {
    if($bunchingLimit !=9) :
      $bunchingLimit = $bunchingLimit +1;
      $totalvalue = $this->userNextQcodesToBeAttmptd($newBunchingID,$childClass,$attmptsQstn,$remingQty);
      if(is_array($totalvalue)) :
              foreach ($totalvalue as $key => $value) :
                $allQueueCodes[] = $value['bunchMasterqcode'];
              endforeach;
            endif;
                    $countallQueuecode = count($allQueueCodes);
      if($countallQueuecode >= $remingQty):
        return $allQueueCodes;
      else :
        $newBunchingID = $this->findNextBunchID($newBunchingID,$userInfo);
        $remingQty - count($allQueueCodes);
                          
        return $this->bunchingNewQcode($remingQty,$newBunchingID,$childClass,$allQueueCodes,$attmptsQstn,$bunchingLimit,$userInfo);
      endif;
    else :
      $allQueueCodes = array();
      return $allQueueCodes;    
    endif;
  }

  //function to fetch total attmptd free question
  public function totalAttemptedFreeQA($userID,$qstnAtmptClss) {
    $selectAtmptdQstn ="select count(distinct qcode) as questionsAttempted
      from $qstnAtmptClss qa where passageID=0 and userID=".$userID."";
    return $this->dbEnglish->query($selectAtmptdQstn)->row();
  }

  //function to get total attempted free questions
  public function totalAttemptedFreeQAQcode($userID,$qstnAtmptClss) {
    $selectAtmptdQstn ="select distinct qcode as questionsAttempted
      from $qstnAtmptClss qa where passageID=0 and userID=".$userID."";
    return $this->dbEnglish->query($selectAtmptdQstn)->result_array();
  }


  //function to get next bunch Id
  public function findNextBunchID($currentBunch,$userInfo) {        
            $schoolBunchingFlow = $this->nextSchoolBunchingOrder($userInfo['schoolCode'],$userInfo['childClass'],$userInfo['groupSkillID']);
            if($currentBunch!=0) :
                $arrLast = array_slice($schoolBunchingFlow, array_search($currentBunch,$schoolBunchingFlow)+1);
                $arrInit = array_slice($schoolBunchingFlow, 0,array_search($currentBunch,$schoolBunchingFlow)+1);
                $finalbunchFlowArr = implode(array_merge($arrLast,$arrInit),",");
            else :
                $finalbunchFlowArr = $schoolBunchingFlow;
            endif;
            return $finalbunchFlowArr[0];
  }

  //function to fetch attmptd qstns in current bunch
  public function selectAttmptdQstnCurrentBnchQuery($currentBunch,$userID) {
    $selectAttmptdQstnCurrentBnch="select group_concat( distinct qcode) as questionsAttempted from $this->questionAttemptClassTbl qa where passageID=0 and userID=".$userID." and bunchID='".$currentBunch."'"; 
    return $this->dbEnglish->query($selectAttmptdQstnCurrentBnch)->row();
  }

//function to get count of qcodes  of current bunch
  public function bunchingCounResult($currentBunch,$childClass,$qcode="") {
    $bunchingCounResult="select count(qcode) as bunchMasterCount from bunchMaster where bunchID='".$currentBunch."' and childClass=".$childClass.""; 
    if($qcode) :
      $bunchingCounResult .=' and qcode not in('.$qcode.')';  
    endif;
    return $this->dbEnglish->query($bunchingCounResult)->row();
  }

  //function to get next school bunching order
  public function nextSchoolBunchingOrder($schoolCode,$childClass,$groupSkillID){
    $bunchsql = "select bunchOrder from schoolBunchingOrder where schoolCode = '$schoolCode'";
    $bunching_order = $this->dbEnglish->query($bunchsql)->row();
    if($bunching_order) : 
      $currentBunching = $this->bunchingorderFlow($bunching_order->bunchOrder,$childClass);
    endif;
    return $currentBunching;
  }

  //function to get content  quantity of free question
  public function getQuantityOfFreeQue($userID){
    $selectQuantity=$this->dbEnglish->query("SELECT contentQuantity FROM contentFlowMaster where contentType='freeques' limit 1");
    return $selectQuantity->row();
  }

  //function to fetch next free questions for new user
  public function nextFreeQsntnsForNewUsr($childClass,$nextSchoolOrder, $contentQuantity){
    $selectfreeQues="SELECT qcode FROM bunchMaster where childClass=".$childClass." and bunchID=".$nextSchoolOrder." order by rand() limit $contentQuantity";
    return $this->dbEnglish->query($selectfreeQues)->result();
  }

  // function to get Next Content Flow For Free Qstns
  public function getNextContentFlowForFreeQstns($userInformation,$lastbunchFlowArr,$getNextBunchId){
    if($userInformation['currentContentType']=='N/A' && $userInformation['refID'] ==0):  
      $nextSchoolBunchingOrder=$this->nextSchoolBunchingOrder($userInformation['schoolCode'],$userInformation['childClass'],$userInformation['groupSkillID']);
      if($nextSchoolBunchingOrder):
        $selectQuantity=$this->getQuantityOfFreeQue($userID);
        $getNextFreeQuestions=$this->nextFreeQsntnsForNewUsr($userInformation['childClass'],$nextSchoolBunchingOrder[0], $selectQuantity->contentQuantity);     
      endif;
      $result=$getNextFreeQuestions[0];
      return $result->qcode;
    else:
      $result=$this->getNxtFreeQstnForExstngUsr($userInformation['userID'],$lastbunchFlowArr,$userInformation['childClass'],$getNextBunchId);
      return $result;
    endif;
  }

  //fuction to get Count In Bunch
  public function getCountInBunch($bunchId,$childClass){
    $selectQcode=$this->dbEnglish->query("SELECT count(qcode) as count FROM bunchMaster where bunchID=".$bunchId." and childClass=".$childClass."");
    return $selectQcode->result();
  }

  //function to get qstns Atmptd Frm Currnt Bunch
  public function qstnsAtmptdFrmCurrntBunch($userID,$bunchId){
    $selectQcodeQstnAttmpt=$this->dbEnglish->query("SELECT distinct qcode FROM $this->questionAttemptClassTbl where userID=".$userID." and passageID=0 and bunchID=".$bunchId." order by attemptedDate desc");
    return $selectQcodeQstnAttmpt->result_array();
  }

  //function to get unAtmptd Qcodes From Current Bunch
  public function unAtmptdQcodesFromCurrentBunch($bunchId,$childClass,$qstnAtmptdQcode){    
    $selectUnatmptdQcode="SELECT qcode from bunchMaster where bunchID=".$bunchId." and childClass=".$childClass."";
    if($qstnAtmptdQcode):
      $selectUnatmptdQcode.= " and qcode not in ($qstnAtmptdQcode)";
    endif;
    return $this->dbEnglish->query($selectUnatmptdQcode)->result();

  }

  //function to get Qcodes From Bunch
  public function getQcodesFromBunch($nextBunchId,$childClass){
    $selectBunchMasterQcode=$this->dbEnglish->query("select qcode from bunchMaster where bunchID=".$nextBunchId." and childClass=".$childClass."");
    return  $selectBunchMasterQcode->result_array();
  }

  //function to get Nxt Free Qstn For Exstng Usr
  public function getNxtFreeQstnForExstngUsr($userID,$bunchId,$childClass,$nextBunchId){
    if($bunchId==0):
      $bunchId=$nextBunchId;
    endif;
    $getQcodeCount =$this->getCountInBunch($bunchId,$childClass);
    $result=$this->totalAttemptedFreeQAQcode($userID,$this->questionAttemptClassTbl);
    $getQcodeQstnAttmpt=$this->qstnsAtmptdFrmCurrntBunch($userID,$bunchId);
    $allqstnAtmptdQcode=$this->SplitArrayValue($result);
    if(count($getQcodeQstnAttmpt) < $getQcodeCount[0]->count):
      if($getQcodeQstnAttmpt) :
        $qstnAtmptdQcode=$this->SplitArrayValue($getQcodeQstnAttmpt);
      endif;
      $getUnatmptdQcode=$this->unAtmptdQcodesFromCurrentBunch($bunchId,$childClass,$allqstnAtmptdQcode);
      return $getUnatmptdQcode[0]->qcode;
    else:
      $getUnatmptdQcode=$this->unAtmptdQcodesFromCurrentBunch($bunchId,$childClass,$allqstnAtmptdQcode);
      return $getBunchMasterQcode[0]->qcode;
    endif;
  }

  // function to get passage type of user
  function getPassageType($passageID){
    $this->dbEnglish->Select('passageType');
    $this->dbEnglish->from('passageMaster');
    $this->dbEnglish->where('passageID',$passageID);
    $query = $this->dbEnglish->get();
    $passageTypeInfo = $query->result_array();
    return $passageTypeInfo[0]['passageType'];
  }

  //function to fetch Next Content Flow For Convrstn
  public function getNextContentFlowForConvrstn($userID){
    $attmptdConvrstnID=$this->getUserAttmptdConvrstn($userID);
    $currentContent=$this->currentOngoingQtype($userID);
    $contentType=$currentContent[0];
    if($contentType['currentContentType']=='passage') {
      $passageType=$this->getPassageType($contentType['refID']);
    }else if($contentType['currentContentType']=='passage_ques'){
      $arrDetailPsg=$this->getQcodePassageDetails($prevrefID);
      $passageType=$arrDetailPsg['passageType'];
    }
    $attemptedPassageIDArr = explode(',', $attmptdConvrstnID);
    $getUserLevel=$this->getUserCurrentLevel($userID);
    $getUserCurrentLevel=$getUserLevel[0];
    $passageLevel=$getUserCurrentLevel['passageLevel'];
    $conversationLevel=$getUserCurrentLevel['conversationLevel'];   
    $convesationMsLevel=$conversationLevel-gradeScallingConst;  
    $livePassageStatus=livePassageStatus;
    $listeningpsgsDataArr = $this->unatmptdConvrstnPsg($livePassageStatus,$attmptdConvrstnID,$convesationMsLevel);
    if($listeningpsgsDataArr):
      $nextRemediationPassageID = $this->getRemediationPsgId($listeningpsgsDataArr,$userID,$attmptdConvrstnID);
      return $nextRemediationPassageID;
    else:
      return $this->getLowestAttmptdPsg($userID);
    endif;


  }
  //function to fetch unatmptd Convrstn Psg
  public function unatmptdConvrstnPsg($livePassageStatus,$attmptdConvrstnID,$convesationMsLevel){
    $listeningpsgsSql="select passageID from passageMaster where msLevel=".$convesationMsLevel." and status=".$livePassageStatus."  and passageType='Conversation'";    
    if($attmptdConvrstnID):
      $listeningpsgsSql.= " and passageID not in ($attmptdConvrstnID)";
    endif;
    $listeningpsgsSql.=" order by rand()";
    $query = $this->dbEnglish->query($listeningpsgsSql);
    return  $query->result_array(); 
  }

  //function to get Next Content Flow For Reading
  public function getNextContentFlowForReading($userID){
    $attemptedPassageID=$this->getUserAttmptdReading($userID);
    $currentContent=$this->currentOngoingQtype($userID);
    $contentType=$currentContent[0];
    if($contentType['currentContentType']=='passage') {
      $passageType=$this->getPassageType($contentType['refID']);
    }else if($contentType['currentContentType']=='passage_ques'){
      $arrDetailPsg=$this->getQcodePassageDetails($prevrefID);
      $passageType=$arrDetailPsg['passageType'];
    }
    $attemptedPassageIDArr = explode(',', $attemptedPassageID);
    $getUserLevel=$this->getUserCurrentLevel($userID);
    $getUserCurrentLevel=$getUserLevel[0];
    $passageLevel=$getUserCurrentLevel['passageLevel'];
    $conversationLevel=$getUserCurrentLevel['conversationLevel'];
    $convesationMsLevel=$conversationLevel-gradeScallingConst;
    $gradeLowerLimit=number_format($passageLevel, 2);
    $gradeHigherLimit=$gradeLowerLimit+gradeHigherLimitIncreaseConst;
    $readingPsgCondArr = array('q.passageStatus' => livePassageStatus, 'q.diffRating >=' => $gradeLowerLimit, 'q.diffRating <=' => $gradeHigherLimit);
    $livePassageStatus=livePassageStatus;
    $readingPsgsDataArr=$this->unatmptdReadingQstns($attemptedPassageID,$gradeHigherLimit,$gradeLowerLimit,$livePassageStatus);
    if($readingPsgsDataArr):
      $nextRemediationPassageID =  $this->getRemediationPsgId($readingPsgsDataArr,$userID,$attemptedPassageID);
      return $nextRemediationPassageID;
    else:
      return $this->getLowestAttmptdPsg($userID);
    endif;
  }

  //function to get unatmptd Reading Qstns
  function unatmptdReadingQstns($attemptedPassageID,$gradeHigherLimit,$gradeLowerLimit,$livePassageStatus){
    $readingPsgsSql="Select p.passageID as passageID
          from passageMaster p inner join passageAdaptiveLogicParams q on p.passageID=q.passageID where diffRating between ".$gradeLowerLimit." and ".$gradeHigherLimit." and q.passageStatus =".$livePassageStatus." and p.passageType in ('Textual','Illustrated') ";
    if($attemptedPassageID):
      $readingPsgsSql.=" and p.passageID not in ($attemptedPassageID)";
    endif;
    $readingPsgsSql.=" order by rand()";
    return $this->dbEnglish->query($readingPsgsSql)->result_array();
  }

  //function to min Attempted Passage
  function minAttemptedPassage($maxAttmptPsgID,$userID){
    $minAtmptdPsgId="select distinct passageID from passageAttempt where passageID not in ($maxAttmptPsgID) and userID=".$userID." order by lastModified,attemptCount asc";
    $query = $this->dbEnglish->query($minAtmptdPsgId)->result_array();
    return $query;
  }

  //function to get Max Atmptd Psg
  function getMaxAtmptdPsg($userID){
    $maxCount="select passageID from passageAttempt where attemptCount = ( select max(attemptCount) from passageAttempt where userID=".$userID.") and userID=".$userID."";
    $result=$this->dbEnglish->query($maxCount);
              return   $result->result_array();
  }

  //function to conver array into string
  function SplitArrayValue($result){
    $values = array_map('array_pop', $result);
    return implode(',', $values);
  }

  //function to get Lowest Attmptd Psg
  function getLowestAttmptdPsg($userID) {
              $resultArr=  $this-> getMaxAtmptdPsg($userID);
              $maxAttmptPsgID=$this->SplitArrayValue($resultArr);
              $getminAtmptdPsgId=$this->minAttemptedPassage($maxAttmptPsgID,$userID);
              $psgWithMinAttmptCount = $this->getRemediationPsgId($getminAtmptdPsgId,$userID,$attmptdPsgID="");
              if($psgWithMinAttmptCount):
                $getPsgCount=$this->getPsgAtmptdCount($psgWithMinAttmptCount,$userID);
                return $psgWithMinAttmptCount; 
              endif;
  }

  //function to get Recent Attempt Count
  function getRecentAttemptCount($passageID,$userID,$completed) {
    $query = "select attemptCount from passageAttempt where passageID=".$passageID." and userID=".$userID." and completed=".$completed." order by lastModified desc limit 1";
    return $this->dbEnglish->query($query)->row();
  }

  //function to fetch Psg Atmptd Count
  function getPsgAtmptdCount($psgWithMinAttmptCount,$userID){
    $query="select max(attemptCount)  as maxAttemptCount from passageAttempt where passageID=".$psgWithMinAttmptCount." and userID=".$userID."";
    $result=$this->dbEnglish->query($query)->result_array();
  }

  //function to get next Remediation PsgId
  function getRemediationPsgId($unattemptedPassageID,$userID,$attmptdPsgID){
    $currentContent=$this->currentOngoingQtype($userID);
    if($attmptdPsgID == '' && $currentContent[0]['currentContentType']!='N/A'):
        $nextPassageId = $unattemptedPassageID[0]['passageID'];
    else:
        $nextPassageId = "";
        $LowLevelAccPsgID = array();
        $resultArr=explode(",",$attmptdPsgID);
        if(count($resultArr)% remediation == 0):
            $lastPassageArr=array_slice($resultArr,0,remediation);
            $lastAttmptString= implode(",",$lastPassageArr);
            $accuracy = $this->getLowestAccurcy($lastAttmptString,$userID);
            if($accuracy):
                foreach ($accuracy as $key => $accuracyval) :
                      if($accuracyval->acc < remediationAccuracy) :
                            $LowLevelAccPsgID[] =  $accuracyval->passageID;
                      endif;
                endforeach;
            endif;
            if(count($LowLevelAccPsgID) > 1) :
                $ffLowLevelAccurcy = implode(',',$LowLevelAccPsgID);
                $nextRemediationPassage=$this->getOldestAttmptdPsgId($ffLowLevelAccurcy,$userID);
                $nextPassageId=$nextRemediationPassage->passageID;
            else:
                $nextPassageId=$LowLevelAccPsgID[0];  
            endif;
        else:
              $nextPassageId = $unattemptedPassageID[0]['passageID'];
        endif;      
    endif;
    return  $nextPassageId;
  }


//function to get lowest accuracy passageId
  function getLowestAccurcy($lastPassageArr,$userID) {
    $getLowestAcc=$this->dbEnglish->query("select passageID,round(avg(correct)*100,2) as acc from $this->questionAttemptClassTbl where userID=".$userID." and passageID in ($lastPassageArr) group by passageID");
    return  $getLowestAcc->result();
  }


  //function to fetch oldest attempt passage id
  function getOldestAttmptdPsgId($ffLowLevelAccurcy,$userID){
    $query = "select passageID from $this->questionAttemptClassTbl where passageID in ($ffLowLevelAccurcy) and userID=".$userID." order by attemptedDate  limit 1";
    return $this->dbEnglish->query($query)->row();
  }

  //function to get passage id given qcode
  function getQcodePassageDetails($qcode) {
    $this->dbEnglish->Select('passageID as qcodePassageID');
    $this->dbEnglish->from('questions');
    $this->dbEnglish->where('qcode',$qcode);
    $query = $this->dbEnglish->get();
    $qcodePassageDetailArr = $query->row();
    if($qcodePassageDetailArr->qcodePassageID !=0){
      $this->dbEnglish->Select('passageType');
      $this->dbEnglish->from('passageMaster');
      $this->dbEnglish->where('passageID',$qcodePassageDetailArr->qcodePassageID);
      $query = $this->dbEnglish->get();
      $passageTypeArr = $query->row();
      $qcodePassageDetailArr->passageType=$passageTypeArr->passageType;
    }      
    return (array)$qcodePassageDetailArr;
  }

//function to fetch user attempted passageID
  function getUserAttmptdReading($userID){
    $querySelect=$this->dbEnglish->query("SELECT p.passageID FROM passageAttempt pa,passageMaster p where userID=".$userID." and completed=2 and pa.passageID=p.passageID and p.passageType IN ('Illustrated','Textual') order by pa.lastModified asc");
    return $this->SplitArrayValue($querySelect->result_array());
  }

	//function to fetch user attempted conversation questionsa
	  function getUserAttmptdConvrstn($userID){
		$querySelectConv=$this->dbEnglish->query("SELECT p.passageID FROM passageAttempt pa,passageMaster p where userID=".$userID."  and completed=2 and pa.passageID=p.passageID and p.passageType='Conversation' order by pa.lastModified asc");
		return $this->SplitArrayValue($querySelectConv->result_array());
	  }

	//function to fetch next free questions
	function nextFreeQuestion($questionAttmptTbl,$childClass,$userID,$currentBunchingFlow){
		$selectNextFreeQuestion="select qcode,min(cc) as leastAttempted from (select count(bm.qcode) as cc,qa.qcode from bunchMaster bm,$questionAttmptTbl qa where bm.bunchID=qa.bunchID and bm.qcode=qa.qcode and childClass=".$childClass." and userID=".$userID." and passageID=0 and  bm.bunchID=".$currentBunchingFlow." group by qa.qcode) as c1"; 
		$getNextFreeQuestion= $this->dbEnglish->query($selectNextFreeQuestion)->result();
		$leastAttemptedQcode=$getNextFreeQuestion[0];
		return $leastAttemptedQcode->qcode;
	  
	}

//function to get bunching order flow
  function bunchingorderFlow($bunchingjson,$childClass){
    $currentOrder = "";
    $json = json_decode($bunchingjson);
    $data = $json->bunchids->class;
    foreach ($data as $key => $value) {
      if($key==$childClass) :
        $currentOrder = $value->orders;
      endif;
    }
    return $currentOrder;
  }

//function to get maximun content orders
  function getMaxContentOrders(){
      $contentId=$this->dbEnglish->query("SELECT contentType,contentQuantity FROM contentFlowMaster where contentStatus = 'Yes' order by contentOrder ASC ");                
      $contentFlowArray = $contentId->result();
      $newdata = array();
      foreach ($contentFlowArray as $key => $contenFlow) :
         $content = $contenFlow->contentType[0];
      if($content!='f') :
        for($i=0; $i < $contenFlow->contentQuantity; $i++) :
                        $newdata[] = ucfirst($content); 
        endfor;
         else :
              $newdata[] = ucfirst($content); 
            endif;
     endforeach;
     return  $newdata;     
  }

  //function to get content type
  function contentType($type){
    if($type=='0'){
      return 'F';     
    }else if($type=='Textual'){
      return 'R';   
    }else if($type=='Illustrated'){
      return 'R';   
    }else if($type=='Conversation'){
      return 'C';   
    }
  }


  function generateArrayFlow($userLastAttemptArr,$contentFlowArr,$counter){   
      if(in_array($userLastAttemptArr[1],array_slice($contentFlowArr,array_search($userLastAttemptArr[0], $contentFlowArr)+1)))
            $val = array_search($userLastAttemptArr[1],array_slice($contentFlowArr,array_search($userLastAttemptArr[0], $contentFlowArr)+1));
      else
            $val = 1;
    if($counter==count($contentFlowArr))
      return 0; 
    if(in_array($userLastAttemptArr[0], $contentFlowArr) && !$val){
      $arrStartIndx =  array_search($userLastAttemptArr[0],$contentFlowArr)+2;
      for($i=0;$i<count($contentFlowArr);$i++)      {
        $outArr[$i] = $contentFlowArr[$arrStartIndx%count($contentFlowArr)];
        $arrStartIndx++; 
      }
      return $outArr;
    }else{
      $arrLast = array_slice($contentFlowArr, array_search($userLastAttemptArr[0],$contentFlowArr)+1);
      $arrInit = array_slice($contentFlowArr, 0,array_search($userLastAttemptArr[0],$contentFlowArr)+1);
      $contentFlowArr = array_merge($arrLast,$arrInit);
      $counter++;
      $this->generateArrayFlow($userLastAttemptArr,$contentFlowArr,$counter);
    }
  }

  function getUserCurrentLevel($userID){
    $this->dbEnglish->select('passageLevel,conversationLevel');
    $this->dbEnglish->from('userCurrentStatus');
    $this->dbEnglish->where('userID',$userID);
    $userLevelSql = $this->dbEnglish->get();
    $userLevelArr = $userLevelSql->result_array();
    return $userLevelArr;
  }
  
  function getQuesLevel($userID){
    $this->dbEnglish->Select('freeQuesLevel');
    $this->dbEnglish->from('userCurrentStatus');
    $this->dbEnglish->where('userID',$userID);
    $this->dbEnglish->limit(1);
    $currentFreeQuesLevel = $this->dbEnglish->get();
    if($currentFreeQuesLevel->num_rows() > 0){
      $currentFreeQuesLevelArr = $currentFreeQuesLevel->row();
      return $currentFreeQuesLevelArr->freeQuesLevel; 
    }else{
      return null;
    } 
  }

  //function to fetch current content type
  function currentOngoingQtype($userID){
    $this->dbEnglish->Select('currentContentType,refID,completed');
    $this->dbEnglish->from('userCurrentStatus');
    $this->dbEnglish->where('userID',$userID);
    $query = $this->dbEnglish->get();
    $userCurrentQtype = $query->result_array();
    return $userCurrentQtype;   
  }
}
?>
