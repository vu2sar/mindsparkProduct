
<?php
		
		
  include_once 'db_credentials.php';
 
  function getDBConnection() {

  $hostname = MASTER_HOST;
  $username = MASTER_USER;
  $password = MASTER_PWD;
  $dbname = 'educatio_msenglish';

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




