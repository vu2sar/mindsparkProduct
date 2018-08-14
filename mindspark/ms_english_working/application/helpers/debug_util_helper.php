<?php
/**
    Debug Util File : This file is used to print the values of variables.
**/
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
function custom_format($n, $d = 0,$x=0,$y=0) {
// for making all decimal not to appear
	$d=0;
    $n = number_format($n, $d, '.', '');
    $n = strrev($n);

    if ($d) $d++;
    $d += 3;

    if (strlen($n) > $d)
        $n = substr($n, 0, $d) . ','
           . implode(',', str_split(substr($n, $d), 2));

    return strrev($n);
}
?>