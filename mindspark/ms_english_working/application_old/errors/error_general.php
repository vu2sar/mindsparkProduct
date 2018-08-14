<?php 

			
			$errordic=array('head' =>$heading,
							'details' => strip_tags($message) 
							);

			$error=array(	
							
							'eiCode' => '0',
							'eiMsg' => 'Server Error',
							'eiDebug'	=> $errordic
						);

			echo json_encode($error);	
			exit();
			
?>
