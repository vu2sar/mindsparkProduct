<?php
	/**
	* 
	* Connects to the slave database..
	* 
	*/
	if(!class_exists('db'))
		require(dirname(__FILE__). '/class.db.php');
		
	$database = 'educatio_adepts';
	$sdb = null;
	
	$sdb = new db('SLAVE',$database);
//        $sdb->setErrorCallbackFunction('echo');
//        $sdb.setErrorCallbackFunction('echo');
?>