<?php
	/**
	* 
	* Connects to the slave database..
	* 
	*/
	if(!class_exists('db'))
		require(dirname(__FILE__). '/parentInterface/classes/class.db.php');
		
	$database = 'educatio_adepts';
	$db = null;
	
	$db = new db('MASTER',$database);
?>