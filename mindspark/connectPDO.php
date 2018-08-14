
<?php

  include_once 'db_credentials.php';
  function getDBConnection() {

  $hostname = MASTER_HOST;
  $username = MASTER_USER;
  $password = MASTER_PWD;
  $dbname = 'educatio_MSEnglish';

  try {
    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
  } catch(PDOException $e) {
    throw $e;
  }
  return $db;
  }

  function closeDBConnection($db) {
    $db = NULL;
  }    
?>

<!--
<?php


$user = 'root';
$password = '';
try {
$db = new PDO('mysql:host=192.168.0.7;dbname=educatio_MSEnglish', $user, $pass);


 } catch (PDOException $e) {
 
    echo 'Connection failed: ' . $e->getMessage();
}


?>-->