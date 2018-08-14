<?php
$userID = $_SESSION ['userID'];
$schoolCode = isset ( $_SESSION ['schoolCode'] ) ? $_SESSION ['schoolCode'] : "";
$user = new User ( $userID );

if (strcasecmp ( $user->category, "School Admin" ) == 0) {
	$query = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
	FROM     adepts_userDetails
	WHERE    schoolCode='$schoolCode' AND category='STUDENT' AND subcategory='School' AND enabled=1 AND endDate>=curdate()
	AND subjects like '%" . SUBJECTNO . "%'
	GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
} elseif (strcasecmp ( $user->category, "Teacher" ) == 0) {
	$query = "SELECT   class, group_concat(distinct section ORDER BY section)
	FROM     adepts_teacherClassMapping
	WHERE    userID = $userID AND subjectno=" . SUBJECTNO . "
	GROUP BY class ORDER BY class, section";
} elseif (strcasecmp ( $user->category, "Home Center Admin" ) == 0) {
$query = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
FROM     adepts_userDetails
WHERE    category='STUDENT' AND subcategory='Home Center' AND schoolCode='$schoolCode' AND enabled=1
AND endDate>=curdate() AND subjects like '%" . SUBJECTNO . "%'
GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
} else {
	echo "You are not authorised to access this page!";
	exit ();
}

$classArray = $sectionArray = array ();
$hasSections = false;
$result = mysql_query ( $query ) or die ( mysql_error () );
while ( $line = mysql_fetch_array ( $result ) ) {
array_push ( $classArray, $line [0] );
if ($line [1] != '')
	$hasSections = true;
	$sections = explode ( ",", $line [1] );
	$sectionStr = "";
	for($i = 0; $i < count ( $sections ); $i ++) {
	$classSectionArr [] = $line [0] . $sections [$i];
	if ($sections [$i] != "")
		$sectionStr .= $sections [$i] . ",";
	}
	$sectionStr = substr ( $sectionStr, 0, - 1 );
	array_push ( $sectionArray, $sectionStr );
}
