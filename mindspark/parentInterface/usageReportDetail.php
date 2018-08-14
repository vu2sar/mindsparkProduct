<?php
//$query = "SHOW VARIABLES where Variable_name='version'";
//$topic_result = mysql_query($query) or die(mysql_error());
//$result = mysql_fetch_array($topic_result);
//echo var_dump($result);
$userID = $_SESSION['childID'];
if ($userID == '')
    $userID = 113619;
$class = $_SESSION['childClassUsed'];
if ($class == '')
    $class = 5;
//$startDate = $_REQUEST['startDate']; 
//if($startDate=='')
//    $startDate = date('Y-m-d',strtotime("-15 days"));
//$endDate = $_REQUEST['endDate'];
$studentName = $_SESSION['childNameUsed'];
//if($endDate=='')
//    $endDate = date('Y-m-d');
$startDate_int = str_replace("-", "", $startDate);
$endDate_int = str_replace("-", "", $endDate);
$display = 'Daily';
$interval = date_create($startDate)->diff(date_create($endDate));
//echo $interval->days;
//This queries are to be converted to stored procedure
if ($interval->days > 60) {
    //Fortnight
    $display = 'Fortnight';
    $query = "select IFNULL(TimeSpanDate,convert(selected_date using utf8)),sum(IFNULL(TimeSpent,0)) TimeSpent,
concat((CASE WHEN ceil(week(selected_date)/2)<=ceil(week('$startDate')/2) THEN date_format('$startDate','%D %b') else date_format(date_add(selected_date, interval (-dayofweek(selected_date)+1) day) ,'%D %b') end),'-',
(CASE WHEN ceil(week(selected_date)/2)>=ceil(week('$endDate')/2) THEN date_format('$endDate','%D %b') else date_format(date_add(selected_date, interval (7 - dayofweek(selected_date)) + ((week(selected_date) % 2) * 7) day) ,'%D %b') end)) TimeSpan
 from 
(select adddate('2000-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v
left join (SELECT date_format(startTime,'%Y-%m-%d') as TimeSpanDate,sum(TIMESTAMPDIFF(SECOND, startTime,endTime))/60 as TimeSpent FROM  adepts_sessionStatus b
WHERE  b.userID=$userID AND startTime_int >= $startDate_int and startTime_int <=$endDate_int and endTime is not null GROUP BY date_format(startTime,'%Y-%m-%d')) tbl1 on TimeSpanDate=date_format(selected_date,'%Y-%m-%d')
where selected_date between '$startDate' and '$endDate' GROUP BY ceil(week(selected_date)/2)";
} else if ($interval->days > 14 && $interval->days < 60) {
    //Week
    $display = 'Week';
    $query = "select IFNULL(TimeSpanDate,convert(selected_date using utf8)),sum(IFNULL(TimeSpent,0)) TimeSpent,
concat((CASE WHEN selected_date<='$startDate' THEN date_format('$startDate','%D %b') else date_format(date_add(selected_date, interval (-dayofweek(selected_date)+1) day) ,'%D %b') end),'-',"
            . "(CASE WHEN WEEK(selected_date)>=WEEK('$endDate') THEN date_format('$endDate','%D %b') else date_format(date_add(selected_date, interval (7 - dayofweek(selected_date)) + ((week(selected_date) % 1) * 7) day) ,'%D %b') end)) TimeSpan
 from 
(select adddate('2000-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v
left join (SELECT date_format(startTime,'%Y-%m-%d') as TimeSpanDate,sum(TIMESTAMPDIFF(SECOND, startTime,endTime))/60 as TimeSpent FROM adepts_sessionStatus b
WHERE  b.userID=$userID AND startTime_int >= $startDate_int and startTime_int <=$endDate_int and endTime is not null GROUP BY date_format(startTime,'%Y-%m-%d')) tbl1 on TimeSpanDate=date_format(selected_date,'%Y-%m-%d')
where selected_date between '$startDate' and '$endDate' GROUP BY WEEK(selected_date)";
} else {
    //Day
    $display = 'Day';
    $query = "select convert(selected_date using utf8) as selected_date1,IFNULL(TimeSpanDate,convert(selected_date using utf8)),sum(IFNULL(TimeSpent,0)) TimeSpent,
date_format(selected_date,'%D %b') as TimeSpan
 from 
(select adddate('2000-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v
left join (SELECT date_format(startTime,'%Y-%m-%d') as TimeSpanDate,sum(TIMESTAMPDIFF(SECOND, startTime,endTime))/60 as TimeSpent FROM  adepts_sessionStatus b
WHERE  b.userID=$userID AND startTime_int >= $startDate_int and startTime_int <=$endDate_int and endTime is not null GROUP BY date_format(startTime,'%Y-%m-%d')) tbl1 on TimeSpanDate=date_format(selected_date,'%Y-%m-%d')
where selected_date between '$startDate' and '$endDate' GROUP BY selected_date";
}
//echo $query;
$result = mysql_query($query);
while ($data = mysql_fetch_assoc($result)) {
    $row[] = $data;
}

if (!function_exists('convertToTime')) {

    function convertToTime($date) {
        $hr = substr($date, 11, 2);
        $mm = substr($date, 14, 2);
        $ss = substr($date, 17, 2);
        $day = substr($date, 8, 2);
        $mnth = substr($date, 5, 2);
        $yr = substr($date, 0, 4);
        $time = mktime($hr, $mm, $ss, $mnth, $day, $yr);
        return $time;
    }

}
?>
<!--Div that will hold the pie chart-->
<div id="chart_divUsage" style="height: 400px; text-align: left; vertical-align: center;"></div>
<!--Load the AJAX API-->
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
<?php
$jsArray = array();
array_push($jsArray, array('Time span', 'Time spent in minutes'));
$count = 0;
$totalTimeSpent = 0;
foreach ($row as $data) {
    array_push($jsArray, array($data['TimeSpan'], round($data['TimeSpent'])));
    $totalTimeSpent += $data['TimeSpent'];
    $count++;
}
echo "var usageArray = " . json_encode($jsArray) . ";\n";
?>
    if (<?= $count ?> > 0)
    {
        // Load the Visualization API and the piechart package.
        google.load('visualization', '1.0', {'packages': ['corechart']});
        // Set a callback to run when the Google Visualization API is loaded.
        google.setOnLoadCallback(drawChart);
        // Callback that creates and populates a data table,
        // instantiates the pie chart, passes in the data and
        // draws it.
    }
    else
        document.getElementById("chart_divUsage").innerHTML = "No data found for usage in selected date range";
    function drawChart() {
        var options = {title: "Time spent",
            height: 400,
//                            legend: 'none',
            showRowNumber: true,
            animation: {
                duration: 2000,
                easing: 'linear',
            },
//            hAxis: {textStyle:{},slantedText:true},
            legend:'none',
            vAxis: {title: "minutes", minValue: 0, viewWindow: {min: 0},titleTextStyle: {fontName:'Arial',italic:false}}};
        var chart = new google.visualization.ColumnChart(document.getElementById('chart_divUsage'))
        // Create and populate the data table.
        var data = google.visualization.arrayToDataTable(usageArray);
//        var formatter = new google.visualization.NumberFormat({
//            fractionDigits: 0,
//            suffix: ' minutes'
//        });
//        formatter.format(data, 1); // Apply formatter to second column.
var view = new google.visualization.DataView(data);
      view.setColumns([0, 1,
                       { calc: "stringify",
                         sourceColumn: 1,
                         type: "string",
                         role: "annotation" }]);
        // Create and draw the visualization.
//        new google.visualization.ColumnChart(document.getElementById('chart_divUsage')).
        chart.draw(view, options);
    }
</script>

<?php
//echo $detailedReport;
//echo var_dump($topicAttemptedDetailArray);
?>
<script>
    if (document.getElementById('timeSpentSummary'))
        document.getElementById('timeSpentSummary').innerHTML = 'Total Time Spent : <?= round($totalTimeSpent); ?> minutes';
//    if (document.getElementById('timeSpentDisplay'))
//        document.getElementById('timeSpentDisplay').innerHTML = 'Group by : <?= $display; ?>';
</script>