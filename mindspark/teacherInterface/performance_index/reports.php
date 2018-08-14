<?php
set_time_limit(0);
require_once('../../userInterface/check1.php');
if(!isset($_SESSION['userID'])) {
	exit('logout');
}

function pluralize($value, $suffix='s') {
	return ($value==1 ? '' : $suffix);
}
function group_into_weeks($from, $to) {
	while(!isset($end) || $end<$to) {
		$start = !isset($end) ? $from : date('Y-m-d H:i:s', strtotime('next Monday', strtotime($end)));
		$next_sunday = date('Y-m-d H:i:s', strtotime('next Sunday, 23:59:59', strtotime($start)));
		$end = strtotime($next_sunday)>strtotime($to) ? $to : $next_sunday;
		$weeks[] = array(
			'start' => $start,
			'end' => $end,
			'length' => round((strtotime($end)-strtotime($start))/(60*60*24))/7,
		);
	}
	return $weeks;
}
$get_student_profiles = function($schoolCode, $class, $section) use($schoolName) {
	$students = array();
	$result = mysql_query("
		select userID as user_id, childName as name, profilePicture as picture
		from adepts_userDetails
		where schoolCode=$schoolCode and childClass=$class and childSection='$section'
			and category='STUDENT' and subcategory='School' and endDate>=curdate() and enabled=1
		order by name
	") or die(mysql_error());
	if(mysql_num_rows($result)>0) {
		while($profile = mysql_fetch_assoc($result)) {
			$studentName = explode(' ', $profile['name']);
			$students[$profile['user_id']] = array(
				'userID' => $profile['user_id'],
				'last_name' => array_pop($studentName),
				'first_name' => implode(' ', $studentName),
				'picture' => $profile['picture'],
				'class' => $class,
				'section' => $section,
				'school_name' => $schoolName,
			);
		}
	}
	return $students;
};
function getValidTopics($from, $to, $schoolCode, $class, $section, $students) {
	// CSV is short for 'comma separated values'
	$student_ids['CSV'] = implode(',', array_keys($students));
	$activationPeriod = array(
		'from' => date('Y-m-d', strtotime($from)),
		'to' => date('Y-m-d', min(strtotime('-7 days', time()), strtotime($to))),
	);
	$result = mysql_query("
		select group_concat(distinct concat(\"'\", teacherTopicCode, \"'\")) as CSV
		from adepts_teacherTopicActivation
		where schoolCode=$schoolCode and class=$class and section='$section'
			and activationDate>='$activationPeriod[from]' and activationDate<='$activationPeriod[to]'
	") or die(mysql_error());
	$topics['activated'] = mysql_fetch_assoc($result);
	if(is_null($topics['activated']['CSV'])) {
		$topics['activated']['count'] = 0;
	} else {
		$topics['activated']['count'] = count(explode(',', $topics['activated']['CSV']));
		$result = mysql_query("
			select group_concat(distinct concat(\"'\", teacherTopicCode, \"'\")) as CSV
			from adepts_teacherTopicQuesAttempt_class$class
			where userID in ($student_ids[CSV]) and teacherTopicCode in ({$topics['activated']['CSV']})
				and lastModified>='$activationPeriod[from]' and lastModified<='$activationPeriod[to]'
			having count(teacherTopicCode)>0
		") or die(mysql_error());
		$topics['valid'] = mysql_fetch_assoc($result);
		if(is_null($topics['valid']['CSV'])) {
			$topics['valid']['count'] = 0;
		} else {
			$topics['valid']['count'] = count(explode(',', $topics['valid']['CSV']));
		}
	}
	return $topics;
}
$schoolCode = $_REQUEST['schoolCode'];
$schoolName = $_REQUEST['schoolName'];
$class = $_REQUEST['class'];
$section = $_REQUEST['section'];

$from = "$_REQUEST[fromDate] 00:00:00";
$to = "$_REQUEST[toDate] 23:59:59";
$fromMonth = date('M Y', strtotime($from));
$toMonth = date('M Y', strtotime($to));
$gracePeriod = $_REQUEST['gracePeriod'];
$scoreOutOf = $_REQUEST['scoreOutOf'];
$weeks = group_into_weeks($from, $to);
$numberOfWeeks = round(array_sum(array_map(function($week) {
	return $week['length'];
}, $weeks)), 2) - $gracePeriod;

$mpiSettings = $_REQUEST['mpiSettings'];
$maximumScore = number_format(round(array_sum($mpiSettings['weightages'])*$scoreOutOf/100, 1), 1);
$minimumWeeklyUsageTime = (integer) $mpiSettings['others']['Minimum weekly usage'];
$minimumWeeklyQuestions = (integer) $mpiSettings['others']['Minimum weekly question attempts'];
$students = $get_student_profiles($schoolCode, $class, $section);
if(count($students)==0)
	exit("There are no students in class $class$section.");
$topics = getValidTopics($from, $to, $schoolCode, $class, $section, $students);
$attemptTables = array(
	"adepts_teacherTopicQuesAttempt_class$class",
	"adepts_topicRevisionDetails",
	"adepts_revisionSessionDetails",
	"adepts_ttChallengeQuesAttempt",
	"practiseModulesQuestionAttemptDetails",
	"adepts_ncertQuesAttempt",
	"adepts_competitiveExamQuesAttempt",
	"adepts_diagnosticQuestionAttempt",
);
foreach($attemptTables as $table) {
	$attemptQueries[] = "
		select ifnull(sum(if(R=1, 1, 0)), 0) as correct, count(*) as total
		from $table
		where userID= and lastModified>='$from' and lastModified<='$to'
	";
}
$attemptQueries[] = "
	select ifnull(sum(quesCorrect), 0) as correct, ifnull(sum(noOfQuesAttempted), 0) as total
	from adepts_timedTestDetails
	where userID= and lastModified>='$from' and lastModified<='$to'
";
$combinedAttemptsQuery = implode(' union all ', $attemptQueries);

$calculateScores = array(
	'Accuracy' => function($user_id) use($combinedAttemptsQuery, $numberOfWeeks, $minimumWeeklyQuestions) {
		$studentAttemptsQuery = str_replace('userID= ', "userID=$user_id ", $combinedAttemptsQuery);
		$result = mysql_query("
			select sum(attempts.correct) as correct, sum(attempts.total) as total
			from ($studentAttemptsQuery) attempts
		") or die(mysql_error());
		$attempts = mysql_fetch_assoc($result);
		if($attempts['total']>$numberOfWeeks*$minimumWeeklyQuestions) {
			$fieldSummary['percentage'] = ($attempts['correct']/$attempts['total'])*100;
			$s = pluralize($attempts['total']);
			$fieldSummary['details'] = "$attempts[correct] correct out of $attempts[total] question attempt$s";
		} else {
			$fieldSummary['percentage'] = 0;
			$fieldSummary['details'] = "Not enough attempts to calculate accuracy";
		}
		return $fieldSummary;
	},
	'Badges' => function($user_id) use($from, $to) {
		$result = mysql_query("
			select count(*) as total
			from adepts_userBadges
			where userID=$user_id and lastModified>='$from' and lastModified<='$to'
				and batchType in ('bonusChamp', 'accuracyMonthly', 'consistentUsageMonthly', 'homeUsageChamp')
		") or die(mysql_error());
		$badges = mysql_fetch_assoc($result);
		$fieldSummary['percentage'] = min($badges['total']*10, 100);
		if($badges['total']>0) {
			$s = pluralize($badges['total']);
			$fieldSummary['details'] = "$badges[total] monthly badge$s earned on Mindspark";
		} else {
			$fieldSummary['details'] = "Did not earn a badge in report period";
		}
		return $fieldSummary;
	},
	'Weekly Usage Score' => function($user_id) use($weeks, $minimumWeeklyUsageTime, $numberOfWeeks) {
		$consistentWeeks = 0;
		foreach($weeks as $week) {
			$result['usage'] = mysql_query("
				select ifnull(sum(timestampdiff(second, startTime, endTime)), 0)/60 as minutes
				from adepts_sessionStatus
				where userID=$user_id and startTime>='$week[start]' and endTime<='$week[end]'
			") or die(mysql_error());
			$usage = mysql_fetch_assoc($result['usage']);
			if($usage['minutes']>$week['length']*$minimumWeeklyUsageTime)
				$consistentWeeks += $week['length'];
		}
		$consistentWeeks = min(round($consistentWeeks, 2), $numberOfWeeks);
		$fieldSummary['percentage'] = ($consistentWeeks/$numberOfWeeks)*100;
		$s = array(
			pluralize($minimumWeeklyUsageTime),
			pluralize($consistentWeeks),
		);
		$fieldSummary['details'] = "Weekly usage > $minimumWeeklyUsageTime min$s[0] in $consistentWeeks week$s[1] out of $numberOfWeeks";
		return $fieldSummary;
	},
	'Topic Completion' => function($user_id) use($topics, $class, $from, $to) {
		if($topics['activated']['count']==0) {
			$fieldSummary['percentage'] = 0;
			$fieldSummary['details'] = "No topic activated in current period";
		} else {
			if($topics['valid']['count']==0) {
				$topicAttempts['completed'] = 0;
				$fieldSummary['percentage'] = 0;
			} else {
				$result['attemptedTopics'] = mysql_query("
					select group_concat(distinct concat(\"'\", teacherTopicCode, \"'\")) as CSV
					from adepts_teacherTopicQuesAttempt_class$class
					where userID=$user_id and teacherTopicCode in ({$topics['valid']['CSV']})
						and lastModified>='$from' and lastModified<='$to'
					having count(teacherTopicCode)>0
				") or die(mysql_error());
				$attemptedTopics = mysql_fetch_assoc($result['attemptedTopics']);
				// $fieldSummary['attemptedTopics'] = $attemptedTopics;
				if(is_null($attemptedTopics['CSV'])) {
					$topicAttempts['completed'] = 0;
				} else {
					$result['topicAttempts'] = mysql_query("
						select ifnull(sum(if(progress>75, 1, 0)), 0) as completed
						from adepts_teacherTopicStatus
						where userID=$user_id and teacherTopicCode in ($attemptedTopics[CSV]) and ttAttemptNo=1
					") or die(mysql_error());
					$topicAttempts = mysql_fetch_assoc($result['topicAttempts']);
				}
				$fieldSummary['percentage'] = ($topicAttempts['completed']/$topics['valid']['count'])*100;
			}
			$s = pluralize($topicAttempts['completed']);
			$fieldSummary['details'] = "$topicAttempts[completed] topic$s completed out of {$topics['valid']['count']}";
		}
		return $fieldSummary;
	},
);

foreach($students as $user_id => $profile) {
	$certificate = array(
		'profile' => $profile,
		'report' => array(
			'overview' => array(
				'from_month' => $fromMonth,
				'to_month' => $toMonth,
				'total_score' => 0,
				'maximum_score' => $maximumScore,
			),
		),
	);
	foreach($mpiSettings['weightages'] as $field => $weightage) {
		$fieldSummary = $calculateScores[$field]($user_id);
		$fieldSummary['percentage'] = number_format(round($fieldSummary['percentage'], 1), 1);
		$fieldSummary['weightage'] = $weightage;
		$fieldSummary['out_of'] = number_format(round($weightage*$scoreOutOf/100, 1), 1);
		$certificate['report']['overview']['total_score'] += $fieldSummary['score'] = number_format(round(($fieldSummary['percentage']/100)*$fieldSummary['out_of'], 1), 1);
		$certificate['report']['performance'][$field] = $fieldSummary;
	}
	$certificate['report']['overview']['total_score'] = number_format($certificate['report']['overview']['total_score'], 1);
	$certificates[] = $certificate;
}
echo json_encode($certificates);
