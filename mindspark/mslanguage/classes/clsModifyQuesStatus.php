<?php
require_once('msLangclsQuestion.php');

class modifyQuesStatus
{
	var $first_alloted;
	var $second_alloted;
	var $qcodeList;
	var $qcode;
	var $status;
	var $username;
	var $passageID;
	var $objQuestion;
	
	function modifyQuesStatus($db)
    {
		$this->qcode='';
		$this->username='';
		$this->passageID='';
		$this->first_alloted='';
		$this->second_alloted='';
		$this->qcodeList='';
		$this->status='';
		$this->dbConnection=$db;
		$this->objQuestion = new msLangQuestion($db);
		$this->objQuestion->setCommonPostParam();	
	}
	
	function setCommonPostParam(){
		if(isset($_POST['quesStatus'])) $this->status = $_POST['quesStatus'];
		if(isset($_POST['qcodeSend'])) $this->qcode = $_POST["qcodeSend"];
	}
	
	function setAssignReviewerParams()
	{
		if(isset($_POST['reviewerFirst'])) $this->first_alloted = $_POST['reviewerFirst'];
		if(isset($_POST["reviewerSecond"])) $this->second_alloted = $_POST["reviewerSecond"];
		if(isset($_POST['qcodeList'])) $this->qcodeList = $_POST['qcodeList'];
	}
	
	function setMakeFreeQuesLiveParams()
	{	
		$qcodeList=$_POST['qcodeList'];
		$this->qcodeList=explode(",",$qcodeList);
	}
	
	function setMakePsgQuesLiveParams()
	{	
		$this->username = isset($_POST['username'])?$_POST['username']:"";
		$this->passageID = isset($_POST['passageID'])?json_decode($_POST['passageID']):"" ;
	}
	
	function assignReviewerstoQuestion()
	{		
		try
		{ 
			$this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->dbConnection->beginTransaction();
			
			if($this->qcodeList=="")
			{				
				$stmt=$this->dbConnection->prepare("UPDATE questions SET currentAlloted='$this->first_alloted', first_alloted='$this->first_alloted', second_alloted='$this->second_alloted', status=1, trail=CONCAT(trail,'-','$this->first_alloted') WHERE qcode='$this->qcode'");
			
			}else{	
				// this scenario is not currently used anyWhere need to check if to be used for multiple qcode list has to be updated,need to work with loop as pdo not working for multiple update with single query
				$stmt=$this->dbConnection->prepare("UPDATE questions SET currentAlloted='$this->first_alloted', first_alloted='$this->first_alloted', second_alloted='$this->second_alloted, status=1, trail=CONCAT(trail,'-','$this->first_alloted') WHERE qcode IN ($this->qcodeList)");
			}
			$stmt->execute();
			
			if($stmt->rowCount()==1){
				$this->dbConnection->commit();
				$arr=array("success"=>1);
				//$stmt=$this->dbConnection->prepare("select * from questions WHERE qcode='$this->qcode'");
				//$stmt->execute();
				//$qcodeDataArr=$stmt->fetch();
				$this->objQuestion->assignQuesVerReviewers();
				$versionNo=$this->objQuestion->getQuestionVersion($this->qcode,0);
				$this->objQuestion->modifyQuestionVersion($versionNo,false);
				
				//echo $arr;
			}else{				
				$this->dbConnection->rollBack();
				$arr=array("success"=>0);							
				echo json_encode($arr);
			}
			
		}catch(PDOException $pe)
		{
			
			$this->dbConnection->rollBack();
			$arr=array("success"=>2,"exception"=>"exception ".$pe);
			echo json_encode($arr);
			exit;
		}
	}
	
	function sendBackToReviewer()
	{
		try
		{ 
			$this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->dbConnection->beginTransaction();		
			//UPDATE questions SET currentAlloted=second_alloted , status='2' WHERE qcode='168' 
			if($this->status==2){
				$stmt=$this->dbConnection->prepare("UPDATE questions SET currentAlloted=first_alloted ,trail=CONCAT(trail,'-',first_alloted),status=1 WHERE qcode='$this->qcode' ");
			}else{
				$stmt=$this->dbConnection->prepare("UPDATE questions SET currentAlloted=second_alloted ,trail=CONCAT(trail,'-',second_alloted),status=3 WHERE qcode='$this->qcode' ");
			}
			$stmt->execute();
			
			if($stmt->rowCount()==1){
				$this->dbConnection->commit();
				$arr=array("success"=>1);
				$versionNo=$this->objQuestion->getQuestionVersion($this->qcode,0);
				$this->objQuestion->modifyQuestionVersion($versionNo,false);
				//echo $arr;
			}else{
				
				$db->rollBack();
				$arr=array("success"=>0);				
				echo json_encode($arr);
			}
			
		}catch(PDOException $pe)
		{
			
			$this->dbConnection->rollBack();
			$arr=array("success"=>2,"exception"=>"exception ".$pe);
			echo json_encode($arr);
			//echo $("#showMsg").append('<br>'.json_encode($arr).'</br>');			
			exit;
		}
	}
	
	
	function changeQuestionStatus()
	{	
		try
		{ 
			$this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->dbConnection->beginTransaction();		
			
			if($this->status==1){
				$stmt=$this->dbConnection->prepare("UPDATE questions SET currentAlloted=second_alloted ,trail=CONCAT(trail,'-',second_alloted), status='3' WHERE qcode='$this->qcode'");
			}else{
				$stmt=$this->dbConnection->prepare("UPDATE questions SET currentAlloted=questionmaker , status='5' WHERE qcode='$this->qcode' ");
			}
			$stmt->execute();
			
			if($stmt->rowCount()==1){
				$this->dbConnection->commit();
				$arr=array("success"=>1);
				//$stmt=$db->prepare("select * from questions WHERE qcode='$qcode'");
				//$stmt->execute();
				//$qcodeDataArr=$stmt->fetch();
				
				$versionNo=$this->objQuestion->getQuestionVersion($this->qcode,0);
				$this->objQuestion->modifyQuestionVersion($versionNo,false);
				
				//echo $arr;
			}else{
				$this->dbConnection->rollBack();
				$arr=array("success"=>0);				
				echo json_encode($arr);
			}
			
		}catch(PDOException $pe)
		{
			$this->dbConnection->rollBack();
			$arr=array("success"=>2,"exception"=>"exception ".$pe);
			echo json_encode($arr);
			exit;
		}
	}
	
	function rejectQuestion()
	{	
		
		try
		{ 
			$this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->dbConnection->beginTransaction();			
			
			
			if($this->status == 1)
			{	
				$stmt=$this->dbConnection->prepare("UPDATE questions SET currentAlloted=questionmaker,trail=CONCAT(trail,'-',questionmaker),status='2' WHERE qcode='$this->qcode' ");
			}
			else
			{
				$stmt=$this->dbConnection->prepare("UPDATE questions SET currentAlloted=questionmaker,trail=CONCAT(trail,'-',questionmaker),status='4' WHERE qcode='$this->qcode' ");
			}		
			
			$stmt->execute();

			if($stmt->rowCount()==1){
				$this->dbConnection->commit();
				$arr=array("success"=>1);
				//$stmt=$db->prepare("select * from questions WHERE qcode='$qcode'");
				//$stmt->execute();
				//$qcodeDataArr=$stmt->fetch();
				$versionNo=$this->objQuestion->getQuestionVersion($this->qcode,0);
				$this->objQuestion->modifyQuestionVersion($versionNo,false);
				//$arr=array("success"=>1);
				//echo $arr;
			}else{
				$this->dbConnection->rollBack();
				$arr=array("success"=>0);				
				echo json_encode($arr);
			}
			
		}catch(PDOException $pe)
		{
			
			$this->dbConnection->rollBack();
			$arr=array("success"=>2,"exception"=>"exception ".$pe);
			echo json_encode($arr);
			exit;
		}
	}
	
	function makeFreeQuesLive()
	{
		try
		{ 
			$this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->dbConnection->beginTransaction();
			$liveDate = date("Y-m-d H:i:s");			
			
			foreach($this->qcodeList as $qcode)
			{
				$stmt=$this->dbConnection->prepare("UPDATE questions SET currentAlloted='',status=6,liveOn='".$liveDate."'  WHERE qcode=$qcode;");
				$stmt->execute();
			}
			
			if($stmt->rowCount()==1){
				$this->dbConnection->commit();
				foreach($this->qcodeList as $qcode)
				{
					//$stmt=$this->dbConnection->prepare("select * from questions WHERE qcode='$qcode'");
					//$stmt->execute();
					//$qcodeDataArr=$stmt->fetch(PDO::FETCH_ASSOC);
					$versionNo=$this->objQuestion->getQuestionVersion($qcode,0);
					$this->objQuestion->modifyQuestionVersion($versionNo,false);
				}
				$arr=array("success"=>1);
				//echo $arr;
			}else{
				
				$this->dbConnection->rollBack();
				$arr=array("success"=>0);				
				echo json_encode($arr);
			}
			
		}catch(PDOException $pe)
		{
			
			$this->dbConnection->rollBack();
			$arr=array("success"=>2,"exception"=>"exception ".$pe);
			echo json_encode($arr);
			exit;
		}
	}
	
	function makePsgQuesLive()
	{
		try
		{ 
			$this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->dbConnection->beginTransaction();	
			$liveDate = date("Y-m-d H:i:s");
			
			$updatereadytogolivestmt1=$this->dbConnection->prepare('UPDATE questions SET status=6,liveOn="'.$liveDate.'" where passageID = "'.$this->passageID .'" and status=5');	
			$updatereadytogolivestmt1->execute();
			
			$updatereadytogolivestmt2=$this->dbConnection->prepare('UPDATE passageMaster SET status=7 where passageID = "'.$this->passageID .'"');
			$updatereadytogolivestmt2->execute();

			//for updating status in passageversion table
			$updatereadytogolivestmt2=$this->dbConnection->prepare('UPDATE passageVersion SET status=7 where passageID = "'.$this->passageID .'" ORDER BY passageVersionNo desc LIMIT 1');
			$updatereadytogolivestmt2->execute();
			
			$getPassageQcodesSql=$this->dbConnection->prepare('select qcode as qcode from questions where passageID = "'.$this->passageID .'"');	
			$getPassageQcodesSql->execute();
		
			if($updatereadytogolivestmt1->rowCount()>=1){
				$this->dbConnection->commit();
				while($passageQcodeData=$getPassageQcodesSql->fetch(PDO::FETCH_ASSOC))
				{
					$varsionNo=$this->objQuestion->getQuestionVersion($passageQcodeData['qcode'],0);
					$this->objQuestion->modifyQuestionVersion($varsionNo,false);
				}
				$arr=array("success"=>1);
				echo json_encode($arr);
			}else{
				$this->dbConnection->rollBack();
				$arr=array("success"=>0);				
				echo json_encode($arr);
			}
			
		}catch(PDOException $pe)
		{
			$this->dbConnection->rollBack();
			$arr=array("success"=>2,"exception"=>"exception ".$pe);
			echo json_encode($arr);
			exit;
		}
	}
	
	function dropQuestion()
	{	
		
		$curUserName=$_SESSION['username'];
		try
		{ 
			$this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->dbConnection->beginTransaction();			
			$stmt=$this->dbConnection->prepare("UPDATE questions SET trail=CONCAT(trail,'-','$curUserName'),status='8',lastModifiedBy='$curUserName' WHERE qcode='$this->qcode' AND status!=6");			
			$stmt->execute();
			
			if($stmt->rowCount()==1){
				$this->dbConnection->commit();
				$arr=array("success"=>1);
				echo json_encode($arr);				
			}else{
				$this->dbConnection->rollBack();
				$arr=array("liveQuestion"=>1);	
				echo json_encode($arr);
			}
			
		}catch(PDOException $pe)
		{
			
			$this->dbConnection->rollBack();
			$arr=array("success"=>2,"exception"=>"exception ".$pe);
			echo json_encode($arr);
			exit;
		}
	}
	
	
}
?>
