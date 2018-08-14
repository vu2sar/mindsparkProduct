<?php
include("stemmerSearch.php");
include("../slave_connectivity.php");
 
 $term=trim($_REQUEST["q"]);
 $object = new Stemmer();
 $term_stem = $object->stem($term);
 $flow = strtolower($_REQUEST["flow"])."_level";
 $class=$_REQUEST["class"];

 $query_normal=mysql_query("SELECT a.* FROM adepts_clusterMaster a, adepts_subTopicMaster b, adepts_topicMaster c where a.subTopicCode=b.subTopicCode AND b.topicCode=c.topicCode and subjectno=2 and cluster like '%".$term."%' and FIND_IN_SET($class,$flow) and a.status='Live' order by noOfTimesSearched desc");
 
  $query_topic_normal=mysql_query("SELECT distinct(teacherTopicDesc) FROM adepts_teacherTopicMaster where teacherTopicDesc like '%".$term."%' and teacherTopicDesc not like '%- custom%' and subjectno=2 and live=1 AND customTopic=0");
  
 $query_wordnet = mysql_query("SELECT dw.lemma AS linkedlemma FROM sensesXsemlinksXsenses AS l LEFT JOIN words AS sw ON l.swordid = sw.wordid LEFT JOIN words AS dw ON l.dwordid = dw.wordid LEFT JOIN linktypes USING (linkid) WHERE sw.lemma = '$term' ORDER BY linkid,ssensenum");
 
 $query_stemNormal=mysql_query("SELECT a.* FROM adepts_clusterMaster a, adepts_subTopicMaster b, adepts_topicMaster c where a.subTopicCode=b.subTopicCode and b.topicCode=c.topicCode and subjectno=2 and cluster like '%".$term_stem."%' and FIND_IN_SET($class,$flow) and a.status='Live' order by noOfTimesSearched desc");
 
 $query_topic_stemNormal=mysql_query("SELECT distinct(teacherTopicDesc) FROM adepts_teacherTopicMaster where teacherTopicDesc like '%".$term_stem."%' and teacherTopicDesc not like '%- custom%' and subjectno=2 and live=1 AND customTopic=0");
 
  $query_stemWordnet = mysql_query("SELECT dw.lemma AS linkedlemma FROM sensesXsemlinksXsenses AS l LEFT JOIN words AS sw ON l.swordid = sw.wordid LEFT JOIN words AS dw ON l.dwordid = dw.wordid LEFT JOIN linktypes USING (linkid) WHERE sw.lemma = '$term_stem' ORDER BY linkid,ssensenum");
  
 
 	$json_normal=array();
    while($student=mysql_fetch_array($query_normal)){
         $json_normal[]=array(
                    'id'=> $student["cluster"],
                    'name'=>$student["cluster"]
                        );
    }
	
	
	$json_normal_otherClasses = addOtherClassClusters($term,$class,$flow);
	$json_normal = array_merge($json_normal,$json_normal_otherClasses);
	
	$json_topic_normal=array();
    while($student=mysql_fetch_array($query_topic_normal)){
         $json_topic_normal[]=array(
                    'id'=> $student["teacherTopicDesc"],
                    'name'=>$student["teacherTopicDesc"]
                        );
    }
	
	
	$json_stemNormal=array();
	if(strcmp($term,$term_stem)!=0) {
	while($student=mysql_fetch_array($query_stemNormal)){
         $json_stemNormal[]=array(
                    'id'=> $student["cluster"],
                    'name'=>$student["cluster"]
                        );
    }
	 }
	 $json_stemNormal_otherClasses = addOtherClassClusters($term_stem,$class,$flow);
	 $json_stemNormal = array_merge($json_stemNormal,$json_stemNormal_otherClasses);
	 
	$json_topic_stemNormal=array();
	if(strcmp($term,$term_stem)!=0) {
	while($student=mysql_fetch_array($query_topic_stemNormal)){
         $json_topic_stemNormal[]=array(
                    'id'=> $student["teacherTopicDesc"],
                    'name'=>$student["teacherTopicDesc"]
                        );
    }
	 }
	
	$json_wordnet=array();
	while($student1=mysql_fetch_array($query_wordnet)){
	$query_wordnet2 = "Select cluster from adepts_clusterMaster a, adepts_subTopicMaster b, adepts_topicMaster c where a.subTopicCode=b.subTopicCode AND b.topicCode=c.topicCode AND subjectno=2 AND cluster like '%$student1[0]%' and FIND_IN_SET($class,$flow) and a.status='Live' order by noOfTimesSearched desc";
	$student2 = mysql_query($query_wordnet2);
	while($topics=mysql_fetch_array($student2)){
         $json_wordnet[]=array(
                    'id'=> $topics[0],
                    'name'=>$topics[0]
                        );
		}
	$json_wordnet_otherClasses = addOtherClassClusters($student1[0],$class,$flow);
	$json_wordnet = array_merge($json_wordnet,$json_wordnet_otherClasses);
    }
	
	$json_topic_wordnet=array();
	while($student1=mysql_fetch_array($query_wordnet)){
	$query_wordnet3 = "Select distinct(teacherTopicDesc) from adepts_teacherTopicMaster where teacherTopicDesc like '%$student1[0]%' and teacherTopicDesc not like '%- custom%' and subjectno=2 and live=1";
	$student2 = mysql_query($query_wordnet3);
	while($topics=mysql_fetch_array($student2)){
         $json_topic_wordnet[]=array(
                    'id'=> $topics[0],
                    'name'=>$topics[0]
                        );
		}
    }
	
	
	$json_stemWordnet=array();
	if(strcmp($term,$term_stem)!=0) {
	while($student1=mysql_fetch_array($query_stemWordnet)){
	$query_wordnet2 = "Select cluster from adepts_clusterMaster a, adepts_subTopicMaster b, adepts_topicMaster c where a.subTopicCode=b.subTopicCode AND b.topicCode=c.topicCode AND subjectno=2 and cluster like '%$student1[0]%' and FIND_IN_SET($class,$flow) and a.status='Live' order by noOfTimesSearched desc";
	$student2 = mysql_query($query_wordnet2);
	while($topics=mysql_fetch_array($student2)){
         $json_stemWordnet[]=array(
                    'id'=> $topics[0],
                    'name'=>$topics[0]
                        );
	}
	$json_stemWordnet_otherClasses = addOtherClassClusters($student1[0],$class,$flow);
	$json_stemWordnet = array_merge($json_stemWordnet,$json_stemWordnet_otherClasses);
     }
	  }
	
	
	$json_topic_stemWordnet=array();
	if(strcmp($term,$term_stem)!=0) {
	while($student1=mysql_fetch_array($query_stemWordnet)){
	$query_wordnet3 = "Select distinct(teacherTopicDesc)from adepts_teacherTopicMaster where teacherTopicDesc like '%$student1[0]%' and teacherTopicDesc not like '%- custom%' and subjectno=2 and live=1 AND customTopic=0";
	$student2 = mysql_query($query_wordnet3);
	while($topics=mysql_fetch_array($student2)){
         $json_topic_stemWordnet[]=array(
                    'id'=> $topics[0],
                    'name'=>$topics[0]
                        );
	}
     }
	  }
	
  $result = array_merge($json_topic_normal,$json_topic_stemNormal,$json_topic_wordnet,$json_topic_stemWordnet,$json_normal,$json_stemNormal,$json_wordnet,$json_stemWordnet);
 echo json_encode($result);

 
?>

<?php

function addOtherClassClustersStructured($term,$class,$flow)
{
	$arr = array();
	$beforeClass= $afterClass = $class;
	for($i=0;$i<10;$i++)
	{
		$beforeClass = $beforeClass - 1;
		$afterClass = $afterClass + 1;
		
		if($beforeClass>=1)
		{
			 $query_normal=mysql_query("SELECT cluster FROM adepts_clusterMaster, adepts_subTopicMaster b, adepts_topicMaster c where a.subtopicCode=b.subTopicCode and b.topicCode=c.topicCode and subjectno=2 and cluster like '%".$term."%' and FIND_IN_SET($beforeClass,$flow) and not FIND_IN_SET($class,$flow) and a.status='Live' order by noOfTimesSearched desc");

		    while($student=mysql_fetch_array($query_normal)){
		         $arr[]=array(
				 			'id'=>$student["cluster"],
		                    'name'=>$student["cluster"]
		                        );
    		}
		}
		
		if($afterClass<=10)
		{
			 $query_normal=mysql_query("SELECT cluster FROM adepts_clusterMaster a, adepts_subTopicMaster b, adepts_topicMaster c  where a.subtopicCode=b.subTopicCode and b.topicCode=c.topicCode and subjectno=2 and cluster like '%".$term."%' and FIND_IN_SET($afterClass,$flow) and not FIND_IN_SET($class,$flow) and a.status='Live' order by noOfTimesSearched desc");

		    while($student=mysql_fetch_array($query_normal)){
		         $arr[]=array(
				 			'id'=>$student["cluster"],
		                    'name'=>$student["cluster"]
		                        );
    		}
		}
	}
	
	$arr = array_map("unserialize", array_unique(array_map("serialize", $arr)));
	
	return $arr;
}

function addOtherClassClusters($term,$class,$flow)
{
	$arr = array();
	
	$query_normal=mysql_query("SELECT cluster FROM adepts_clusterMaster a, adepts_subTopicMaster b, adepts_topicMaster c where a.subtopicCode=b.subTopicCode and b.topicCode=c.topicCode and subjectno=2 and cluster like '%".$term."%' and not FIND_IN_SET($class,$flow) and status='Live' order by noOfTimesSearched desc");

		    while($student=mysql_fetch_array($query_normal)){
		         $arr[]=array(
				 			'id'=>$student["cluster"],
		                    'name'=>$student["cluster"]
		                        );
    		}
			
	/*$arr = array_map("unserialize", array_unique(array_map("serialize", $arr)));*/
	return $arr;
}

?>


