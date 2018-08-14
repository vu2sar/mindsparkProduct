<?php
 	
error_reporting(0);
Class commentsystem_model extends MY_Model
{

	
	public function __construct() 
	{
		parent::__construct();			 
					 
		$this->dbEnglish = $this->load->database('mindspark_english',TRUE);
	}

	function getCategories()
	{
		
		/*$this->dbEnglish->Select('commentCategoryID, categoryName');
		$this->dbEnglish->from('commentCategories');
		$this->dbEnglish->order_by('commentCategoryID');
		
		$query = $this->dbEnglish->get();
		$getData = $query->result_array();
		return $getData;*/

		$categories = array();
		$subCat     = array();
		$finalArray = array();
		
		$this->dbEnglish->Select('cs.commentCategoryID, cs.categoryName, csc.commentSubCategoryID, csc.subCategoryName');
		$this->dbEnglish->from('commentCategories cs');
		$this->dbEnglish->join('commentSubCategories csc','csc.commentCategoryID=cs.commentCategoryID', 'LEFT');
		$query = $this->dbEnglish->get();
		$getData = $query->result_array();
		$i = 0;
		foreach ($getData as $key => $value) 
		{
			$categories[$i]['categoryName']      = $value['categoryName'];
			$categories[$i]['commentCategoryID'] = $value['commentCategoryID'];
			$i++;

			if($value['subCategoryName'] != '')
				$subCat[$value['categoryName']][] = $value;
		}
		$categoriesArr = $this->getUniqueArr($categories);

		$finalArray['categoriesArr'] = $categoriesArr;
		$finalArray['subCat']        = $subCat;

		return $finalArray;
	}

	function getUniqueArr($array)
	{
	  $result = array_map("unserialize", array_unique(array_map("serialize", $array)));

	  foreach ($result as $key => $value)
	  {
	    if ( is_array($value) )
	    {
	      $result[$key] = $this->getUniqueArr($value);
	    }
	  }

	  return $result;
	}

	function getSubCategories($commentCategoryID)
	{
		
		$this->dbEnglish->Select('*');
		$this->dbEnglish->from('commentSubCategories');
		$this->dbEnglish->where('commentCategoryID', $commentCategoryID);
		$this->dbEnglish->order_by('commentSubCategoryID');
		
		$query = $this->dbEnglish->get();
		$getData = $query->result_array();
		
		return $getData;
	}

	/**
	 * function role : Insert user comments 
	 * @return  none
	 * 
	 * */

	function insertUserComments($userID,$itemID,$page,$status,$sessionID,$commentCategoryID,$commentSubCategoryID,$comment)
	{
		//GET CURRENT DATETIME
		$this->dbEnglish->Select('NOW() as currentDate');
		$query   = $this->dbEnglish->get();
		$getDate = $query->row_array();

		//GET ASSIGNTO NAME HAVING PEER=1
		$this->dbEnglish->Select('srno, userName');
		$this->dbEnglish->from('commentReviewers');
		$this->dbEnglish->where('nextPeer', '1');
		$query = $this->dbEnglish->get();
		$currentAlloted = $query->row_array();

		$allotmentUserName = $currentAlloted['userName'];	
		
		$commentData = array(
				'userID'          => $userID,
				'itemID'          => $itemID,
				'page'            => $page,
				'status'          => $status,
				'commentReceived' => $getDate['currentDate'],
				//'assignTo'      => $assignTo['reviewerUserName'],
				'currentAlloted'  => $allotmentUserName,
		);

		$this->dbEnglish->insert('userComments', $commentData); 
		$insert_id = $this->dbEnglish->insert_id();
		$rowInserted = $this->dbEnglish->affected_rows();

		//CHANGE THE NEXT PEER

		//if($this->category == 'STUDENT')
		//{
			if($rowInserted == '1')
			{
				$this->dbEnglish->Select('srno, userName');
				$this->dbEnglish->from('commentReviewers');
				$this->dbEnglish->where('srno >', $currentAlloted['srno']);
				$this->dbEnglish->order_by('srno');	
				$query = $this->dbEnglish->get();
				$nextPeer = $query->result_array();
				
				if(count($nextPeer) > 0 )
					$nextPeer = $nextPeer[0]['srno'];
				else
				{
					$this->dbEnglish->Select('srno, userName');
					$this->dbEnglish->from('commentReviewers');
					$this->dbEnglish->where('nextPeer !=', '1');
					$this->dbEnglish->order_by('srno');	
					$this->dbEnglish->limit(1);	
					$query = $this->dbEnglish->get();
					$nextPeer = $query->result_array();
					$nextPeer = $nextPeer[0]['srno'];
				}


				$queryPeer = "UPDATE commentReviewers SET nextPeer = IF (srno = '".$nextPeer."', 1, 0)";
				$rowInserted1 = $this->dbEnglish->affected_rows();
				
				$this->dbEnglish->query($queryPeer);
			}
		//}

		//INSERT IN COMMENTDETAILS 
		//if($insert_id != '' && $insert_id != 0)
		if($insert_id != 0)
		{
			$comntDetailsData = array(
				'commentID'     => $insert_id,
				'userID'        => $userID,
				'sessionID'     => $sessionID,
				'comment'       => $comment,
				'commentCategoryID'    => $commentCategoryID,
				'commentSubCategoryID' => $commentSubCategoryID,
			);
			$this->dbEnglish->insert('userCommentDetails', $comntDetailsData);
			$commentDetailsinsert_id = $this->dbEnglish->insert_id();
		}

		//INSERT IN THE ALLOTMENT LOG
		/*$allotmentData = array(
				'commentDetailID' => $commentDetailsinsert_id,
				'assignBy'        => 'system',
				'currentAlloted'  => $allotmentUserName,
		);
		$this->dbEnglish->insert('commentAllotmentLog', $allotmentData);*/
	}

	/*IN VERSION 2 TIME SPECIFIC COMMENT SELETION WILL COME SO AS OF NOW WILL FETCH ALL THE COMMENTS HAVING STATUS CLOSED*/
	function getUserComments($userID)
	{
		$this->dbEnglish->Select('cd.commentDetailID, cd.commentID,cd.comment, max(DATE(cd.lastModified)) as lastModified, categoryName, page,userComments.viewed');
		$this->dbEnglish->from('userCommentDetails cd');
		$this->dbEnglish->join('userComments','userComments.commentID=cd.commentID');
		$this->dbEnglish->join('commentCategories','commentCategories.commentCategoryID=cd.commentCategoryID', 'LEFT');
		$this->dbEnglish->where('userComments.userID', $userID);
		$this->dbEnglish->where('status', 'closed');
		$this->dbEnglish->group_by('cd.commentID');	
		$this->dbEnglish->order_by('max(cd.lastModified)', 'desc');
		
		$query = $this->dbEnglish->get();
		$getData = $query->result_array();
		//echo "<pre>";print_r($this->dbEnglish->last_query());echo "</pre>";
		
		return $getData;
	}

	function fetchCommentDetails($userID, $commentID)
	{
		$finalData = array();
		$this->dbEnglish->Select('cd.userID, cd.sessionID, comment, DATE(cd.lastModified) as lastModified, categoryName, page, itemID, cd.commentCategoryID, cd.commentSubCategoryID, subCategoryName');
		$this->dbEnglish->from('userCommentDetails cd');
		$this->dbEnglish->join('userComments','userComments.commentID=cd.commentID');
		$this->dbEnglish->join('commentCategories','commentCategories.commentCategoryID=cd.commentCategoryID', 'LEFT');
		$this->dbEnglish->join('commentSubCategories','commentSubCategories.commentSubCategoryID=cd.commentSubCategoryID', 'LEFT');
		$this->dbEnglish->where('cd.commentID', $commentID);
		//$this->dbEnglish->order_by('DATE(cd.lastModified)', 'desc');
		
		$query = $this->dbEnglish->get();
		$getData = $query->result_array();
		$finalData['result'] = $getData;
		$count = 0;
		foreach ($finalData['result'] as $key => $value) 
		{
			//if($value['userID'] != '' && $value['userID'] != '0')
			if($value['userID'] != '0')
			{
				$count++;
				$finalData['result'][$key]['counter'] = $count;
			}
		}

		if($count >= 3)
			$showReply = FALSE;
		else
			$showReply = TRUE;

		$itemID = $finalData['result'][0]['itemID'];
		
		if($finalData['result'][0]['page'] == 'passage')
		{
			

			$this->dbEnglish->Select('passageContent, passageName, passageType');
			$this->dbEnglish->from('passageMaster');
			$this->dbEnglish->where('passageID', $itemID);
			$query = $this->dbEnglish->get();
			$passageContent = $query->row_array();

			$finalData['content']     = $passageContent['passageContent'];
			$finalData['contentName'] = $passageContent['passageName'];

			if($passageContent['passageType'] == 'Conversation')
				$finalData['passageType'] = 'Conversation';
			else
				$finalData['passageType'] = '';
		}
		if($finalData['result'][0]['page'] == 'question')
		{
			

			$this->dbEnglish->Select('quesText');
			$this->dbEnglish->from('questions');
			$this->dbEnglish->where('qcode', $itemID);
			$query = $this->dbEnglish->get();
			$quesContent = $query->row_array();
			$finalData['content']     = strip_tags($quesContent['quesText']);
			$finalData['contentName'] = '';
			$finalData['passageType'] = '';
		}
		if($finalData['result'][0]['page'] == 'essayWriter')
		{
			

			$this->dbEnglish->Select('essayTitle');
			$this->dbEnglish->from('essayMaster');
			$this->dbEnglish->where('essayID', $itemID);
			$query = $this->dbEnglish->get();
			$essayTitle = $query->row_array();
			$finalData['content']     = '';
			$finalData['passageType'] = '';
			if(count($essayTitle) > 0)
			{
				$finalData['contentName'] = $essayTitle['essayTitle'];
			}
			else
			{
				$finalData['contentName'] = '';
			}
		}
		if($finalData['result'][0]['page'] == 'grounds')
		{
			

			$this->dbEnglish->Select('igreDesc');
			$this->dbEnglish->from('IGREMaster');
			$this->dbEnglish->where('igreid', $itemID);
			$query = $this->dbEnglish->get();
			$igreTitle = $query->row_array();
			$finalData['content']     = '';
			$finalData['passageType'] = '';
			if(count($igreTitle) > 0)
			{	

				$gametitle = str_replace("_", " ", $igreTitle['igreDesc']);
				$finalData['contentName'] = $gametitle;
			}
			else
			{
				$finalData['contentName'] = '';
			}
		}

		$finalData['showReply']            = $showReply;
		$finalData['commentCategoryID']    = $finalData['result'][0]['commentCategoryID'];
		$finalData['commentSubCategoryID'] = $finalData['result'][0]['commentSubCategoryID'];
		$finalData['page']                 = $finalData['result'][0]['page'];
		$finalData['categoryName']         = $finalData['result'][0]['categoryName'];
		$finalData['itemID']               = $itemID;


		if($finalData['result'][0]['categoryName'] != 'Doubt')
			$finalData['subCategoryName']      = $finalData['result'][0]['subCategoryName'];
		else
			$finalData['subCategoryName'] = '';
		
		//UPDATE THE VIEWED IN USERCOMMENTS
		$this->dbEnglish->set('viewed', '1');
		$this->dbEnglish->where('commentID',$commentID);
		$this->dbEnglish->update('userComments');

		return $finalData;
	}

	function insertUserReplyComments($userID,$sessionID,$comment,$status, $commentID, $commentCatID, $commentSubCatID)
	{
		$comntDetailsData = array(
			'commentID'            => $commentID,
			'userID'               => $userID,
			'sessionID'            => $sessionID,
			'comment'              => $comment,
			'commentCategoryID'    => $commentCatID,
			'commentSubCategoryID' => $commentSubCatID,
		);
		$this->dbEnglish->insert('userCommentDetails', $comntDetailsData);
		$commentDetailsinsert_id = $this->dbEnglish->insert_id();

		//UPDATE THE STATUS IN USERCOMMENTS
		$this->dbEnglish->set('status', $status);
		$this->dbEnglish->where('commentID',$commentID);
		$this->dbEnglish->update('userComments');
	}

	function setCmntNotificationCount($userID)
	{
		$where = array(
				'userID' => $userID,
				'status' => 'closed',
				'viewed' => '0'
			);
		$this->dbEnglish->Select('*');
    	$this->dbEnglish->from('userComments');
    	$this->dbEnglish->where($where);
		
    	$query = $this->dbEnglish->get();
    	$totalCmntNotificationCount = $query->result_array();
		return $totalCmntNotificationCount;
	}
}

?>
