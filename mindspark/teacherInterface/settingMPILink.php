<?php 
  include("../slave_connectivity.php");

                
                $query = "SELECT class,section,settingValue from userInterfaceSettings where schoolCode='".$_REQUEST['schoolCode']."' and settingName='mpi'";

                            $statusMPI =  mysql_query($query) or die(mysql_error());

                                        
                            while ($row = mysql_fetch_assoc($statusMPI)) {

                               /*  echo $row["class"]."--".$_REQUEST['classValue'];
                                   

                                   echo $row["section"]."--".$_REQUEST['section'];*/

                               if($row["settingValue"] == "CustomOff" && $row["class"]== $_REQUEST['classValue'] && $row["section"]== $_REQUEST['sectionValue']){

                                   
        
                                    /*echo "This Class does not have access to Mindspark Progress Report";
                                    */
                                    echo "Off";
                                    exit();
                                   
                                   
                                }else if($row["class"]== $_REQUEST['classValue'] && $row["section"]== $_REQUEST['sectionValue']){

 
                                    echo "On";
                                    exit();
                               
                                }

                              
                            }

                         
                           
                     

        

?>