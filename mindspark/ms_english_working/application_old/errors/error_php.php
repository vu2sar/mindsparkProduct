<?php
			$errordic=array('severity' =>$severity,
							'message' => strip_tags($message),
							'filepath' => $filepath,
							'linenumber' => $line

							);

			$error=array(	
							'eiCode' => '0',
							'eiMsg' => 'Developer Error',
							'eiDebug'	=> $errordic
						);

	echo json_encode($error);
	exit();

?>


