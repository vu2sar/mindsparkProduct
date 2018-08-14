<?php 
$term = $_REQUEST['term'];
//echo $_POST['category'];
//@include("../check.php");
include_once("../../check1.php");
//checkPermission("MNU");
//echo " CATEGORY-DROPDOWN -  ".$categoryDropdown;
$newInterface = false;
$array=array();
if( isset($_POST['categorySelect'])) 
{
    $_SESSION['categorySelect']=$_POST['categorySelect'];
}
if( isset($_POST['classSelect'])) 
{
    $_SESSION['classSelect']=$_POST['classSelect'];
}
if( isset($_POST['sectionSelect'])) 
{
    $_SESSION['sectionSelect']=$_POST['sectionSelect'];
}
if(isset($_REQUEST['newInterface']))
{
	if($_REQUEST['newInterface'])
		$newInterface = true;
}
$categoryDropdown = isset($_SESSION['categorySelect'])?$_SESSION['categorySelect']:"student";
$classDropdown = isset($_SESSION['classSelect'])?$_SESSION['classSelect']:"";
$sectionDropdown = isset($_SESSION['sectionSelect'])?$_SESSION['sectionSelect']:"";

if($newInterface)
{
	$query = "SELECT userID,childName, childClass, childSection, category, gender FROM adepts_userDetails WHERE childName LIKE '%$term%' 
			  AND username<>'".$_SESSION['username']."'  
			  AND schoolCode='".$_SESSION['schoolCode']."' ";

	if($_SESSION['category']=="STUDENT")
	{$query.= "AND category='TEACHER' AND enabled=1 AND endDate>=curdate() ";}

	if($_SESSION['category']=="TEACHER" || $_SESSION['category']=="School Admin")
	{	
		if($categoryDropdown=='student')
		{
		 	$query.= "AND (category='STUDENT')";
			if(!empty($classDropdown))
			{
				if(strpos($classDropdown, ",") > 0)
				{
					$classSecArr = explode(",", $classDropdown);
					$subQueryArr = array();
					for($i=0; $i<count($classSecArr); $i++)
					{
						if(strpos($classSecArr[$i], "-") > 0)
						{
							$tempArr = explode("-",$classSecArr[$i]);
							array_push($subQueryArr, "(childClass=".$tempArr[0]." AND childSection='".$tempArr[1]."') ");						
						}
						else
						{
							array_push($subQueryArr, "childClass=".$classSecArr[$i]);
						}
					}

					$subQuery = implode(" OR ", $subQueryArr);
					$query .= " AND (".$subQuery.")";
				}
				else
				{
					$subQueryArr = array();
					if(strpos($classDropdown, "-") > 0)
					{
						$sectionDropdownArr = explode("-", $classDropdown);
						$sectionDropdown = $sectionDropdownArr[1];
						if(!empty($sectionDropdown))
						{
							if(strpos($sectionDropdown, ",") > 0)
							{
								$tempArr = explode(",",$sectionDropdown);
								for($i=0; $i<count($tempArr); $i++)
								{
									array_push($subQueryArr ,"(childClass=".$sectionDropdownArr[0]." AND childSection='".$tempArr[$i]."')");						
								}
								$subQuery = implode(" OR ", $subQueryArr);
								$query .= " AND (".$subQuery.")";
							}
							else
							{						
								$query .= " AND childClass=".$sectionDropdownArr[0]." AND childSection='".$sectionDropdown."'";
							}
						}					
						else
							$query .= " AND childClass=".$classDropdown;
					}
					else
					{
						if(!empty($sectionDropdown))
						{
							if(strpos($sectionDropdown, ",") > 0)
							{
								$tempArr = explode(",",$sectionDropdown);
								for($i=0; $i<count($tempArr); $i++)
								{
									array_push($subQueryArr ,"(childClass=".$classDropdown." AND childSection='".$tempArr[$i]."')");						
								}
								$subQuery = implode(" OR ", $subQueryArr);
								$query .= " AND (".$subQuery.")";
							}
							else
							{						
								$query .= " AND childClass=".$classDropdown." AND childSection='".$sectionDropdown."'";
							}
						}					
						else
							$query .= " AND childClass=".$classDropdown;
					}
				}
			} 
			// $query .= " AND childClass IN ('".$classDropdown."')";
			// if(!empty($classDropdown) && !empty($sectionDropdown))
			// 	$query.=" AND childSection='".$sectionDropdown."'";
		$query .= " AND subcategory='School' AND enabled=1 AND endDate>=curdate()";}//IN(SELECT section FROM adepts_teacherClassMapping where userID='".$_SESSION['userID']."' )";
		}
		if($categoryDropdown=='teacher')
		 	$query.= " AND (category='TEACHER')  AND enabled=1 AND endDate>=curdate()";	
		$query.= " ORDER BY username";
		/*echo $query;
		exit;*/
		$i=0;
		$result = mysql_query($query) or die("Error in Query".mysql_error());
		while($row = mysql_fetch_assoc($result)){
			
		if($_SESSION['category']=="STUDENT" || ($_SESSION['category']=="TEACHER" && $categoryDropdown=='teacher') || $_SESSION['category']=="School Admin")
		{	
			if($row['gender']=='Girl')		
			{
				$valToSend = "Ms.".$row['childName']."<span data='".$row['userID']."'></span>";
			}
			else if($row['gender']=='Boy')		
			{
				$valToSend = "Mr.".$row['childName']."<span data='".$row['userID']."'></span>";
			}
			else 		
			{	
				$valToSend = $row['childName']."<span data='".$row['userID']."'></span>";
			}
	}

	if ( $_SESSION['category']=="TEACHER" && $categoryDropdown=='student' || $_SESSION['category']=="School Admin")
	{
		if(!empty($row['childClass']) && !empty($row['childSection']))
			$valToSend = $row['childName']."<span data='".$row['userID']."'> (".$row['childClass'].$row['childSection'].")</span>";
		else
			$valToSend = $row['childName']."<span data='".$row['userID']."'></span>";		
	}		
		array_push($array,$valToSend);
		//$array[$i] = array(childName=>$row['childName'],childClass=>$row['childClass'],childSection=>$row['childSection']);
	$i++;
	}	
}
else
{
	$query = "SELECT childName, childClass, childSection, category, gender FROM adepts_userDetails WHERE childName LIKE '%$term%' 
		  AND username<>'".$_SESSION['username']."'  
		  AND schoolCode='".$_SESSION['schoolCode']."' ";

	if($_SESSION['category']=="STUDENT")
	{$query.= "AND category='TEACHER' AND enabled=1 AND endDate>=curdate() ";}

	if($_SESSION['category']=="TEACHER" || $_SESSION['category']=="School Admin")
	{	if($categoryDropdown=='student')
			{
			 $query.= "AND (category='STUDENT') 
					   AND childClass=".$classDropdown." ";
					   if( isset($_SESSION['sectionSelect'])) {
				$query.="AND childSection='".$sectionDropdown."' AND subcategory='School' AND enabled=1 AND endDate>=curdate()";}//IN(SELECT section FROM adepts_teacherClassMapping where userID='".$_SESSION['userID']."' )";
			}
		if($categoryDropdown=='teacher')
			{
			 $query.= "AND (category='TEACHER')  AND enabled=1 AND endDate>=curdate()"; 
			}	
	}

	$query.= " ORDER BY username";

	$i=0;
	$result = mysql_query($query) or die("Error in Query".mysql_error());
	while($row = mysql_fetch_assoc($result)){
		
	if($_SESSION['category']=="STUDENT" || ($_SESSION['category']=="TEACHER" && $categoryDropdown=='teacher') || $_SESSION['category']=="School Admin")
	{	
		if($row['gender']=='Girl')		
		{
			$valToSend = "Ms.".$row['childName'];
		}
		else if($row['gender']=='Boy')		
		{
			$valToSend = "Mr.".$row['childName'];
		}
		else 		
		{	
			$valToSend = $row['childName'];
		}
	}

	if ( $_SESSION['category']=="TEACHER" && $categoryDropdown=='student' || $_SESSION['category']=="School Admin")
	{
		$valToSend = $row['childName'];//." - ".$row['childClass'].$row['childSection'];	
	}		
		array_push($array,$valToSend);
		//$array[$i] = array(childName=>$row['childName'],childClass=>$row['childClass'],childSection=>$row['childSection']);
	$i++;
	}
}

$response = json_encode($array);

echo $response;
//echo "</br>".$_SESSION['childClass']."   "  . $_SESSION['childSection'];
?>