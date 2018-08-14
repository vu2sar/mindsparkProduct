<?php 

			$errordic=array('head' =>$heading,
							'details' => strip_tags($message) 
							);

			$error=array(	
							
							'eicode' => '0',
							'eiMsg' => 'Invalid Request',
							'eiDebug'	=> $errordic
						);

			echo json_encode($error);
			exit();		


?>
