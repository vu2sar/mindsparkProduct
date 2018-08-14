<?php
/*
 * loadSaveMode == 0 insert/update passage.
 * loadSaveMode == 1 scan for passages return if exists.
 * laodSaveMode == 2 scan for passages return if exists else create a brand new passage and return its id.
 */

error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING);
session_start();
include ("../../../../db_credentials.php");
include('../../ajax/spell_check.php');
require('../../classes/msLangclsQuestion.php');

$db=getDBConnection();
$objQuestion = new msLangQuestion($db);
$contentType = "passage";
$contentCode = $_REQUEST['passageID'];
$currentUser = $_SESSION['username'];

$link = mysql_connect(MASTER_HOST, MASTER_USER, MASTER_PWD);
if (!$link) {
	die('Could not connect: ' . mysql_error());
}

mysql_select_db("educatio_msenglish") or die("Could not select database" . mysql_error());

$response = '';

if ($_REQUEST['loadSaveMode'] == '0') {
	if (passageExists($_REQUEST['passageID'])) {
		updatePassageDetails();
	} else {
		insertPassageDetails();
	}
} else if ($_REQUEST['loadSaveMode'] == '2') {
	$result = getPassageDetails($_REQUEST['passageID']);
	if ($result == 0) {
		insertPassageDetails();
	} else
		echo json_encode($result);
} else if ($_REQUEST['loadSaveMode'] == '1') {
	
	if($_REQUEST['page']!='previewPassage')
	{
		$result = getPassageDetails($_REQUEST['passageID']);
		$result['currentUser'] = $currentUser;
		echo json_encode($result);
	}
	else
	{
		if($currentUser!="harsha.dediya" && $currentUser!="kushal.shah" && $currentUser!="sridhar" && $currentUser!="dev.dutta" && $currentUser!="aarushi.prabhakar")
		{
			$result = getPassageDetails($_REQUEST['passageID']);
			$result['currentUser'] = $currentUser;
			echo json_encode($result);
		}
		else
		{
		$result = getPassageDetails($_REQUEST['passageID']);
		$nonSpellCheckFields = array('passageID','passageName',	'passageImages',	'Author',	'Source',	'msLevel',	'passageType',	'Form',	'Genre',	'Style',	'isAudioUploaded',	'status',	'passageMaker',	'trail',	'createdOn',	'lastModified',	'first_alloted',	'second_alloted',	'currentAlloted',	'lastModifiedBy',	'titleImage',	'intro');

			$formattingErrorExists = 0;
			//$originalPassageContent = $result['passageContent'];
			//$result['passageContent'] =  strip_tags($result['passageContent']);
			$result = spell_check($contentType,$contentCode,$result,$nonSpellCheckFields);
			$resultData['passageData'] = $result[0];
			//$resultData['passageData']['passageContent'] = $originalPassageContent;
			$resultData['spellingErrors'] = $result[1];
			// $resultData['username'] = $_SESSION['username'];
			foreach ($resultData['passageData']  as $key => $value) {
				if (strpos($value,'for_err') !== false)  
				{
					$formattingErrorExists = 1;
					break;
				}
			}
			$resultData['formattingErrorExists'] = $formattingErrorExists;
			$resultData['currentUser']           = $currentUser;
			echo json_encode($resultData);
		}
	}
}

function passageExists($passageID) {
	$sql            = "Select * from passageMaster where passageID =" . $passageID;
	$result         = mysql_query($sql);
	$number_of_rows = mysql_num_rows($result);

	if ($number_of_rows > 0)
		return true;
	else
		return false;
}

function insertPassageDetails() {

	$passageName    = isset($_REQUEST['passageName']) ? $_REQUEST['passageName'] : "";
	$passageName    = addslashes($passageName);
	$passageContent = isset($_REQUEST['passageContent']) ? stripcslashes($_REQUEST['passageContent']) : "";
	$passageContent = addslashes($passageContent);
	$passageImages  = isset($_REQUEST['passageImages']) ? stripcslashes($_REQUEST['passageImages']) : "";
	$Author         = isset($_REQUEST['Author']) ? stripslashes($_REQUEST['Author']) : "";
	$Author         = addslashes($Author);
	$Source         = isset($_REQUEST['Source']) ? stripslashes($_REQUEST['Source']) : "";
	$Source         = addslashes($Source);
	$intro          = isset($_REQUEST['intro']) ? stripslashes($_REQUEST['intro']) : "";
	$intro          = addslashes($intro);
	$titleImage     = isset($_REQUEST['titleImage']) ? $_REQUEST['titleImage'] : "";
	$msLevel        = isset($_REQUEST['msLevel']) ? $_REQUEST['msLevel'] : "";
	$passageType    = isset($_REQUEST['passageType']) ? $_REQUEST['passageType'] : "";
	$Form           = isset($_REQUEST['Form']) ? $_REQUEST['Form'] : "";
	$Genre          = isset($_REQUEST['Genre']) ? $_REQUEST['Genre'] : "";
	$Style          = isset($_REQUEST['Style']) ? $_REQUEST['Style'] : "";
	$passageMaker   = isset($_REQUEST['username']) ? $_REQUEST['username'] : "";
	$first_alloted  = isset($_REQUEST['first_alloted']) ? $_REQUEST['first_alloted'] : "";
	$second_alloted = isset($_REQUEST['second_alloted']) ? $_REQUEST['second_alloted'] : "";

	
	//get comman data array for passing in the function for passageVersion
	$data = getDataArray($passageName,$passageContent,$passageImages,$Author,$Source,$intro,$titleImage,$msLevel,$passageType,$Form,$Genre,$Style,$passageMaker,$first_alloted,$second_alloted);
	
	//not getting throu comman data array as is different for insert and update so taken differently
	$data['passageMaker']   = $passageMaker;


	$sql = "Insert into passageMaster(passageName,passageContent,passageImages,Author,Source,msLevel,intro,titleImage,passageType,Form,Genre,Style,first_alloted,second_alloted,passageMaker,currentAlloted,trail,createdOn,status,isAudioUploaded) values
	                               ('$passageName','$passageContent','$passageImages','$Author','$Source','$msLevel','$intro','$titleImage','$passageType','$Form','$Genre','$Style','$first_alloted','$second_alloted','$passageMaker','$passageMaker','$passageMaker',now(),'0','0')";
	mysql_query($sql);
	$lastInsertID = mysql_insert_id();

	//for inserting/updating in passageVersion table
	if($lastInsertID != 0)
	{
		$passageID = $lastInsertID;

		$data['passageID'] = $passageID;

		//getting the version no
		$versionNo = getPassageVersion($passageID,0);
		
		//to insert into passageVersion table
		modifyPassageVersion($versionNo,false, $data);
	}
	echo $lastInsertID;
}

function updatePassageDetails() {

	$db=getDBConnection();		
	$objQuestion = new msLangQuestion($db);
	$objQuestion->setCommonPostParam();
	
	$passageName    = isset($_REQUEST['passageName']) ? $_REQUEST['passageName'] : "";
	$passageName    = addslashes($passageName);
	$passageContent = isset($_REQUEST['passageContent']) ? stripcslashes($_REQUEST['passageContent']) : "";
	
	$passageContent = addslashes($passageContent);
	
	$passageImages  = isset($_REQUEST['passageImages']) ? stripcslashes($_REQUEST['passageImages']) : "";
	$Author         = isset($_REQUEST['Author']) ? stripslashes($_REQUEST['Author']) : "";
	$Author         = addslashes($Author);
	$Source         = isset($_REQUEST['Source']) ? stripslashes($_REQUEST['Source']) : "";
	$Source         = addslashes($Source);
	$intro          = isset($_REQUEST['intro']) ? stripslashes($_REQUEST['intro']) : "";
	$intro          = addslashes($intro);
	$titleImage     = isset($_REQUEST['titleImage']) ? $_REQUEST['titleImage'] : "";
	$msLevel        = isset($_REQUEST['msLevel']) ? $_REQUEST['msLevel'] : "";
	$passageType    = isset($_REQUEST['passageType']) ? $_REQUEST['passageType'] : "";
	$Form           = isset($_REQUEST['Form']) ? $_REQUEST['Form'] : "";
	$Genre          = isset($_REQUEST['Genre']) ? $_REQUEST['Genre'] : "";
	$Style          = isset($_REQUEST['Style']) ? $_REQUEST['Style'] : "";
	$first_alloted  = isset($_REQUEST['first_alloted']) ? $_REQUEST['first_alloted'] : "";
	$second_alloted = isset($_REQUEST['second_alloted']) ? $_REQUEST['second_alloted'] : "";
	$currentAlloted = isset($_REQUEST['currentAlloted']) ? $_REQUEST['currentAlloted'] : "";
	$status         = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
	$passageID      = isset($_REQUEST['passageID']) ? $_REQUEST['passageID'] : "";
    $isAudioUploaded = isset($_REQUEST['isAudioUploaded'])? $_REQUEST['isAudioUploaded'] : 0;


    //get comman data array for passing in the function for passageVersion
	$data = getDataArray($passageName,$passageContent,$passageImages,$Author,$Source,$intro,$titleImage,$msLevel,$passageType,$Form,$Genre,$Style,$passageMaker,$first_alloted,$second_alloted);
	
	//not getting throu comman data array as is different for insert and update so taken differently
	$data['currentAlloted']  = $currentAlloted;
	$data['status']          = $status;
	$data['isAudioUploaded'] = $isAudioUploaded;
	$data['passageID']       = $passageID;
	
	//get passage details passing passageID
	$getPassageDetailsResult = getPassageDetails($passageID);
	//forming the string from the result for comparing the old string with the new string obtained for versioning purpose
	$old_string = trim($getPassageDetailsResult['passageName'].'-'.stripcslashes($getPassageDetailsResult['passageContent']).'-'.$getPassageDetailsResult['passageImages'].'-'.$getPassageDetailsResult['Author'].'-'.$getPassageDetailsResult['Source'].'-'.$getPassageDetailsResult['msLevel'].'-'.$getPassageDetailsResult['passageType'].'-'.$getPassageDetailsResult['Form'].'-'.$getPassageDetailsResult['Genre'].'-'.$getPassageDetailsResult['Style'].'-'.$getPassageDetailsResult['isAudioUploaded'].'-'.$getPassageDetailsResult['titleImage'].'-'.$getPassageDetailsResult['intro']);


	$sql = "Update passageMaster set passageName='$passageName',intro='$intro',titleImage='$titleImage',passageContent='$passageContent', passageImages = '$passageImages', Author='$Author', Source='$Source', msLevel='$msLevel', passageType='$passageType', Form='$Form', Genre='$Genre', Style='$Style', first_alloted='$first_alloted', second_alloted='$second_alloted', status = '$status', currentAlloted='$currentAlloted', isAudioUploaded='$isAudioUploaded' where passageID='$passageID'";
    //echo $sql;
	mysql_query($sql);
	
	if($msLevel!='')
	{
		$updatePsgQuestionssql = "Update questions set msLevel='$msLevel' where passageID='$passageID'";
		mysql_query($updatePsgQuestionssql);
		$getPsgQuestionsdataSql = "select * from questions where msLevel='$msLevel' and passageID='$passageID'";
		$psgQuesDataRes = mysql_query($getPsgQuestionsdataSql);
		while ($res = mysql_fetch_assoc($psgQuesDataRes)) {
			$versionNo=$objQuestion->getQuestionVersion($res['qcode'],0);
			$objQuestion->modifyQuestionVersion($versionNo,false);
		}
	}	

	//for inserting/updating in passageVersion table
	if($passageID  != 0)
	{
		//forming the string from the result for comparing the old string with the new string obtained for versioning purpose
		$new_string = trim(stripslashes($passageName).'-'.stripcslashes($passageContent).'-'.$passageImages.'-'.stripslashes($Author).'-'.stripslashes($Source).'-'.$msLevel.'-'.$passageType.'-'.$Form.'-'.$Genre.'-'.$Style.'-'.$isAudioUploaded.'-'.$titleImage.'-'.stripslashes($intro));
		
		$data['passageID'] = $passageID;
		
		//string comparision	
		$stringCompareResult = strcmp($old_string, $new_string);
		
		//if there is no difference in the string
		if($stringCompareResult == '0')
		{
			//get the latest version no
			$versionNo = getPassageVersion($passageID,0);
		}
		else
			$versionNo = getPassageVersion($passageID,1);	

		//insert/update in passageVersion table
		modifyPassageVersion($versionNo,false, $data);
	}
}

function getPassageDetails($passageID) {
	$query = "Select * from passageMaster where passageID=" . $passageID;
	$line = mysql_query($query);
	if ($result = mysql_fetch_assoc($line)) {
		foreach ($result as $key) {
			$result[key] = stripcslashes($result[key]);
		}
		return $result;
	} else
		return 0;
}

function getPassageVersion($passageID,$upgradeVersionFlag)
{
	
	/*if($this->ignoreWords!="")
	$this->addIgnoreWordsForQuestion($this->ignoreWords);*/

	if($passageID != '' && $passageID != 0)
	{
		//get the row for mentioned passageID.
		$getPassageVersionSql = "select a.*,b.*,a.status as passagestatus from passageMaster a, passageVersion b where a.passageID='$passageID' and a.passageID=b.passageID order by b.passageVersionNo DESC LIMIT 1";
		
		$passageVersionData = mysql_query($getPassageVersionSql);
		$row=mysql_fetch_assoc($passageVersionData);
		
		//return incremented version number if exist any otherwise return false
		if(count($row) != 0)
		{
			if($upgradeVersionFlag)
			{	
				$row['passageVersionNo']+=1;
			}
			return $row['passageVersionNo'];
		}
		else
		{
			return false;
		}
	}
	else
		return false;	
}

//for insert/update in passageVersion table
function modifyPassageVersion($versionNo,$newEntry,$data=array())
{
	$db=getDBConnection();		
	
	try
	{ 
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$db->beginTransaction();	
		$startDate = date("Y-m-d H:i:s");
		
		
		$passageID       = $data['passageID'];
		
		
		//get passage data passing the passageID
		$passageMasterData = getPassageDetails($data['passageID']);

		$lastModified    = $passageMasterData['lastModified'];
		$currentAlloted  = $passageMasterData['currentAlloted'];
		$lastModifiedBy  = $passageMasterData['lastModifiedBy'];
		$status          = $passageMasterData['status'];
		$trail           = $passageMasterData['trail'];
		$isAudioUploaded = isset($data['isAudioUploaded'])? $data['isAudioUploaded'] : 0;
		$passageContent  = stripslashes($data['passageContent']);
		$Author          = stripslashes($data['Author']);
		$Source          = stripslashes($data['Source']);    
		$intro           = stripslashes($data['intro']);
		$passageName     = stripslashes($data['passageName']);
		

		//if no version number or is a new entry then set version no to 1 and insert in passageVersion table
		if(!$versionNo||$newEntry)     
		// very first version entry sql for the passage
		{
			if(!$versionNo)
			{
				$versionNo=1;
			}				
			
			
			if(count($passageMasterData) > 0)
			{
				$fill_array = array($data['passageID'],$passageName,$passageContent,$data['passageImages'],$Author,$Source,$data['msLevel'],$data['passageType'],$data['Form'],$data['Genre'],$data['Style'],$isAudioUploaded,$data['passageMaker'],$data['first_alloted'],$data['second_alloted'],$data['titleImage'],$intro,$versionNo,$startDate,$currentAlloted,$lastModifiedBy,$status,$data['modifiedBy'],$data['passageMaker'],$startDate);
				$insertPassageVersion=$db->prepare("INSERT INTO passageVersion (passageID,passageName,passageContent,passageImages,Author,Source,msLevel,passageType,Form,Genre,Style,isAudioUploaded,passageMaker,first_alloted,second_alloted,titleImage,intro,passageVersionNo,startDate,currentAlloted,lastModifiedBy,status,modifiedBy,trail,createdOn)values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
				$insertPassageVersion->execute($fill_array);
			}
		}
		else
		// operations require after the very first entry of the passage. 
		{
			
			$getPassageVersionDataSql=$db->prepare("select * from passageVersion where passageID=".$passageID." and passageVersionNo=".$versionNo);
			$passageVersionData=$getPassageVersionDataSql->execute();

			if($getPassageVersionDataSql->rowCount()==1)  
			//if existing passageVersion entry need to be updated.(i.e= if passage status updates)				
			{	
				$updatePassageVersionStatusSql=$db->prepare("UPDATE passageVersion SET status=".$passageMasterData['status'].",currentAlloted='".$passageMasterData['currentAlloted']."',trail='".$passageMasterData['trail']."',first_alloted='".$passageMasterData['first_alloted']."',msLevel='".$passageMasterData['msLevel']."',second_alloted='".$passageMasterData['second_alloted']."' WHERE passageID=".$passageID ." and passageVersionNo=".$versionNo);
				
				$updatePassageVersionStatusSql->execute();
			}
			else     									
			// if new passageVersion entry need to entered.(i.e= if passagedata updates)
			{
				
				$first_alloted  = $passageMasterData['first_alloted'];
				$passageMaker   = $passageMasterData['passageMaker'];
				
				$updatePassagePreVerendDateSql = "UPDATE passageVersion SET endDate='".date('Y-m-d H:i:s')."' WHERE passageID=".$passageID ." ORDER BY passageVersionNo desc LIMIT 1";
				mysql_query($updatePassagePreVerendDateSql);


				$fill_array = array($data['passageID'],$data['passageName'],$passageContent,$data['passageImages'],$Author,$Source,$data['msLevel'],$data['passageType'],$data['Form'],$data['Genre'],$data['Style'],$data['isAudioUploaded'],$passageMaker,$first_alloted,$data['second_alloted'],$data['titleImage'],$intro,$versionNo,$startDate,$currentAlloted,$lastModifiedBy,$status,$data['modifiedBy'],$trail,$startDate);
				$insertPassageVersion=$db->prepare("INSERT INTO passageVersion (passageID,passageName,passageContent,passageImages,Author,Source,msLevel,passageType,Form,Genre,Style,isAudioUploaded,passageMaker,first_alloted,second_alloted,titleImage,intro,passageVersionNo,startDate,currentAlloted,lastModifiedBy,status,modifiedBy,trail,createdOn)values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
				$insertPassageVersion->execute($fill_array);
			}	
		}
		
		$db->commit();
	}
	catch(PDOException $pe)
	{
		$db->rollBack();
		$arr=array("success"=>'modifyPassageVersion Error',"exception"=>"exception ".$pe);
		echo json_encode($arr);
		exit;
	}
}

function getDataArray($passageName,$passageContent,$passageImages,$Author,$Source,$intro,$titleImage,$msLevel,$passageType,$Form,$Genre,$Style,$passageMaker,$first_alloted,$second_alloted)
{
	$data = array();

	$data['passageName']    = $passageName;
	$data['passageContent'] = $passageContent;
	$data['passageImages']  = $passageImages;
	$data['Author']         = $Author;
	$data['Source']         = $Source;
	$data['intro']          = $intro;
	$data['titleImage']     = $titleImage;
	$data['msLevel']        = $msLevel;
	$data['passageType']    = $passageType;
	$data['Form']           = $Form;
	$data['Genre']          = $Genre;
	$data['Style']          = $Style;
	$data['first_alloted']  = $first_alloted;
	$data['second_alloted'] = $second_alloted;
	$data['modifiedBy']     = $_SESSION['username'];

	return $data;
}

echo $response;
?>