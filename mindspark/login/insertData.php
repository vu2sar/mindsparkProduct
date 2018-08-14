<?php           
		$servername1			  		 = 	 "54.179.186.235";
		$username1 			   			 = 	 "techm2";
		$password1 			   			 = 	 "techmsGER5c4ZqjuSZ597";
		$dbname1 			   			 =	 "educatio_msenglish";
		//$conn1    						 =    mysqli_connect($servername1, $username1, $password1,$dbname1);
		
		$conn1							 =   new mysqli($servername1, $username1, $password1,$dbname1);
		$servername2			  		 = 	 "localhost";
		$username2 			   			 = 	 "root";
		$password2 			   			 = 	 "";
		$dbname2 			   			 =	 "educatio_msenglish";
		$conn2    						 =    mysqli_connect($servername2, $username2, $password2,$dbname2);
		if (!$conn1 || !$conn2) {
			die("Connection failed: " . mysqli_connect_error());
		}else{
		    $groupSkills	 	=     array(1,2,3,4,5,6,7,8,9);
			$querySelect        =     "select q.qcode from questions q,groupSkillIDMaster gs where q.skillID=gs.skilID and q.passageID=0 and q.msLevel=1"; 
			$returnResult  	 	=     mysqli_query($conn1, $querySelect);
			while( $row 		= 	  mysqli_fetch_array($returnResult)){
			    $qcodes			=	  $row['qcode'];
				echo $qcodes;
				/*for($count=1; $count<=max($groupSkills); $count++ ){
					echo 'count'.$count;
					
					continue;
				}*/
				//echo 'qcodes'.$qcodes;continue;
			}
		}					
		   
    		
    ?>