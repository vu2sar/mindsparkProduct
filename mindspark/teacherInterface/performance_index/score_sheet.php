<?php
$fileName = str_replace(' ', '', $_REQUEST['fileName']).'.xls';
$content = stripslashes($_REQUEST['content']);
$content = strip_tags($content, '<table><tr><td>');
header('Expires: 0');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: application/vnd.ms-excel');
header('Content-Length: '.strlen($content));
header('Content-Disposition: attachment; filename='.$fileName);
exit($content);
